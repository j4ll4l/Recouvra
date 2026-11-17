<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\Company;
use App\Entity\Payment;
use Doctrine\DBAL\Types\Types;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }


    /**
     * @return Invoice[] Returns an array of Invoices that are overdue and unpaid
     */
    public function findOverdueUnpaid(Company $company, \DateTimeImmutable $today = new \DateTimeImmutable('today')): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.company = :company')
            ->andWhere('i.dueAt < :today')
            ->andWhere('i.status IN (:statuses)')
            ->setParameter('company', $company)
            ->setParameter('today', $today, Types::DATE_IMMUTABLE)
            ->setParameter('statuses', ['issued', 'overdue', 'reminded'])
            ->orderBy('i.dueAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns the remaining amount due on an invoice, in cents
     * (TTC amount minus the sum of its payments). 
     */
    public function getRemainingDueCents(Invoice $invoice): int
    {
        $paidCents = (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COALESCE(SUM(p.amountCents), 0)')
            ->from(Payment::class, 'p')
            ->andWhere('p.invoice = :invoice')
            ->setParameter('invoice', $invoice)
            ->getQuery()
            ->getSingleScalarResult();

        return $invoice->getAmountTtcCents() - $paidCents;
    }
}
