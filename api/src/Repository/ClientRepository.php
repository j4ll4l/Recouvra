<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Returns the clients with the highest overdue amount still due.
     *
     * @return list<array{id: int, name: string, due_cents: int}>
     */
    public function findTopDebtors(Company $company, int $limit = 5): array
    {
        $sql = <<<'SQL'
            SELECT c.id, c.name, SUM(i.amount_ttc_cents - COALESCE(p.paid_cents, 0)) AS due_cents
            FROM client c
            JOIN invoice i ON i.client_id = c.id
            LEFT JOIN (
                SELECT invoice_id, SUM(amount_cents) AS paid_cents
                FROM payment
                GROUP BY invoice_id
            ) p ON p.invoice_id = i.id
            WHERE c.company_id = :company
            AND i.due_at < CURRENT_DATE
            AND i.status IN ('issued', 'overdue', 'reminded')
            GROUP BY c.id, c.name
            HAVING SUM(i.amount_ttc_cents - COALESCE(p.paid_cents, 0)) > 0
            ORDER BY due_cents DESC
            LIMIT :limit
            SQL;

        $rows = $this->getEntityManager()->getConnection()
            ->executeQuery(
                $sql,
                ['company' => $company->getId(), 'limit' => $limit],
                ['company' => ParameterType::INTEGER, 'limit' => ParameterType::INTEGER],
            )
            ->fetchAllAssociative();

        return array_map(static fn (array $row): array => [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'due_cents' => (int) $row['due_cents'],
        ], $rows);
    }
}
