<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\PaymentRepository;
use App\Validator\PaymentWithinBalance;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[PaymentWithinBalance]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['payment:read']],
    denormalizationContext: ['groups' => ['payment:write']],
    order: ['paidAt' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['invoice' => 'exact', 'method' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['paidAt', 'amountCents'])]
#[ApiFilter(DateFilter::class, properties: ['paidAt'])]
class Payment
{
    use TimestampableTrait;

    public const METHODS = ['transfer', 'check', 'card', 'cash'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['payment:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['payment:read', 'payment:write'])]
    #[Assert\NotNull]
    private ?Invoice $invoice = null;

    #[ORM\Column]
    #[Groups(['payment:read', 'payment:write'])]
    #[Assert\NotNull, Assert\Positive]
    private ?int $amountCents = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['payment:read', 'payment:write'])]
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!Y-m-d'],
    )]
    #[Assert\NotNull, Assert\LessThanOrEqual('today', message: 'Un paiement ne peut pas être dans le futur.')]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(length: 20)]
    #[Groups(['payment:read', 'payment:write'])]
    #[Assert\NotBlank, Assert\Choice(choices: self::METHODS)]
    private ?string $method = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getAmountCents(): ?int
    {
        return $this->amountCents;
    }

    public function setAmountCents(int $amountCents): static
    {
        $this->amountCents = $amountCents;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): static
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }
}
