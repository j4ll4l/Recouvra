<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class CompanyExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->restrictToCompany($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->restrictToCompany($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function restrictToCompany(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        if (!in_array($resourceClass, [Client::class, Invoice::class, Payment::class], true)) {
            return;
        }

        $user = $this->security->getUser();

        // Si l'utilisateur n’est pas identifié on ne renvoie rien
        if (!$user instanceof User) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $param = $queryNameGenerator->generateParameterName('company');

        if (Payment::class === $resourceClass) {
            //Pour Payment on passe par sa facture
            $invoiceAlias = $queryNameGenerator->generateJoinAlias('invoice');
            $queryBuilder->join(sprintf('%s.invoice', $rootAlias), $invoiceAlias)
                ->andWhere(sprintf('%s.company = :%s', $invoiceAlias, $param));
        } else {
            $queryBuilder->andWhere(sprintf('%s.company = :%s', $rootAlias, $param));
        }

        $queryBuilder->setParameter($param, $user->getCompany());
    }
}