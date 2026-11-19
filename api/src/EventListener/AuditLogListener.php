<?php

namespace App\EventListener;

use App\Entity\AuditLog;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
final class AuditLogListener
{
    private const IGNORED_FIELDS = ['createdAt', 'updatedAt'];

    /** @var list<AuditLog> */
    private array $pending = [];

    public function __construct(private readonly Security $security)
    {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->record($args->getObject(), 'created');
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $changes = [];
        foreach ($args->getEntityChangeSet() as $field => $change) {
            // On ignore les dates techniques et les changements de collections
            if (in_array($field, self::IGNORED_FIELDS, true) || !is_array($change)) {
                continue;
            }
            $changes[$field] = [
                'old' => $this->normalize($change[0] ?? null),
                'new' => $this->normalize($change[1] ?? null),
            ];
        }

        if ([] !== $changes) {
            $this->record($args->getObject(), 'updated', $changes);
        }
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $this->record($args->getObject(), 'deleted');
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ([] === $this->pending) {
            return;
        }

        $logs = $this->pending;
        $this->pending = []; // vidé AVANT le flush, sinon boucle infinie

        $em = $args->getObjectManager();
        foreach ($logs as $log) {
            $em->persist($log);
        }
        $em->flush();
    }

    /**
     * @param array<string, mixed>|null $data
     */
    private function record(object $entity, string $action, ?array $data = null): void
    {
        [$type, $id, $company] = match (true) {
            $entity instanceof Client => ['Client', $entity->getId(), $entity->getCompany()],
            $entity instanceof Invoice => ['Invoice', $entity->getId(), $entity->getCompany()],
            $entity instanceof Payment => ['Payment', $entity->getId(), $entity->getInvoice()?->getCompany()],
            default => [null, null, null],
        };

        if (null === $type || null === $id || !$company instanceof Company) {
            return;
        }

        $actor = $this->security->getUser();

        $this->pending[] = (new AuditLog())
            ->setCompany($company)
            ->setActor($actor instanceof User ? $actor : null)
            ->setAction(strtolower($type).'.'.$action) // ex. "invoice.created"
            ->setEntityType($type)
            ->setEntityId($id)
            ->setData($data);
    }

    private function normalize(mixed $value): mixed
    {
        return match (true) {
            $value instanceof \DateTimeInterface => $value->format('Y-m-d'),
            is_object($value) && method_exists($value, 'getId') => $value->getId(),
            default => $value,
        };
    }
}
