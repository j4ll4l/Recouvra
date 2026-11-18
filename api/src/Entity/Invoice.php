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
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\InvoiceRepository;
use App\State\CompanyProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'uniq_invoice_company_number', columns: ['company_id', 'number'])]
#[UniqueEntity(fields: ['company', 'number'], message: 'Ce numéro de facture existe déjà.', errorPath: 'number')]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(processor: CompanyProcessor::class),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['invoice:read']],
    denormalizationContext: ['groups' => ['invoice:write']],
    order: ['dueAt' => 'ASC'],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'number' => 'partial',
    'status' => 'exact',
    'client' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['dueAt', 'issuedAt', 'amountTtcCents'])]
#[ApiFilter(DateFilter::class, properties: ['dueAt'])]
class Invoice
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?int $id = null;

    // Pas de groupe : remplie depuis l'utilisateur connecté (après le JWT)
    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invoice:read', 'invoice:write'])]
    #[Assert\NotNull]
    private ?Client $client = null;

    #[ORM\Column(length: 50)]
    #[Groups(['invoice:read', 'invoice:write'])]
    #[Assert\NotBlank, Assert\Length(max: 50)]
    private ?string $number = null;

    #[ORM\Column]
    #[Groups(['invoice:read', 'invoice:write'])]
    #[Assert\NotNull, Assert\PositiveOrZero]
    private ?int $amountHtCents = null;

    #[ORM\Column]
    #[Groups(['invoice:read', 'invoice:write'])]
    #[Assert\NotNull, Assert\GreaterThanOrEqual(propertyPath: 'amountHtCents')]
    private ?int $amountTtcCents = null;

    // Lecture seule : c'est le Workflow qui le change
    #[ORM\Column(length: 20)]
    #[Groups(['invoice:read'])]
    private string $status = 'draft';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['invoice:read', 'invoice:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $issuedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['invoice:read', 'invoice:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull, Assert\GreaterThanOrEqual(propertyPath: 'issuedAt')]
    private ?\DateTimeImmutable $dueAt = null;

    // Pas de groupe : on n'expose jamais le chemin du fichier
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfPath = null;

    /** @var Collection<int, Payment> */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'invoice')]
    #[Groups(['invoice:read'])]
    private Collection $payments;

    // Pas de groupe : les relances auront leurs propres endpoints
    /** @var Collection<int, Reminder> */
    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'invoice')]
    private Collection $reminders;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->reminders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getAmountHtCents(): ?int
    {
        return $this->amountHtCents;
    }

    public function setAmountHtCents(int $amountHtCents): static
    {
        $this->amountHtCents = $amountHtCents;

        return $this;
    }

    public function getAmountTtcCents(): ?int
    {
        return $this->amountTtcCents;
    }

    public function setAmountTtcCents(int $amountTtcCents): static
    {
        $this->amountTtcCents = $amountTtcCents;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIssuedAt(): ?\DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeImmutable $issuedAt): static
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    public function getDueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }

    public function setDueAt(\DateTimeImmutable $dueAt): static
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): static
    {
        $this->pdfPath = $pdfPath;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setInvoice($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getInvoice() === $this) {
                $payment->setInvoice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setInvoice($this);
        }

        return $this;
    }

    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminders->removeElement($reminder)) {
            // set the owning side to null (unless already changed)
            if ($reminder->getInvoice() === $this) {
                $reminder->setInvoice(null);
            }
        }

        return $this;
    }
}
