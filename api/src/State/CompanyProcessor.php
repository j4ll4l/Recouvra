<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @implements ProcessorInterface<Client|Invoice, Client|Invoice>
 */
final class CompanyProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<Client|Invoice, Client|Invoice> $persistProcessor
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private readonly Security $security,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        // 1. L'entreprise vient toujours de l'utilisateur connecté, jamais du JSON
        $data->setCompany($user->getCompany());

        // 2. On revalide maintenant que company est remplie (pour UniqueEntity)
        $this->validator->validate($data, ['groups' => $operation->getValidationContext()['groups'] ?? null]);

        // 3. On laisse Doctrine enregistrer
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
