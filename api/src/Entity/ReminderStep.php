<?php

namespace App\Entity;

use App\Repository\ReminderStepRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReminderStepRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_step_scenario_position', columns: ['scenario_id', 'position'])]
class ReminderStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReminderScenario $scenario = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?int $delayDays = null;

    #[ORM\Column(length: 20)]
    private ?string $tone = null;

    #[ORM\Column(length: 200)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    /**
     * @var Collection<int, Reminder>
     */
    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'step')]
    private Collection $reminders;

    public function __construct()
    {
        $this->reminders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScenario(): ?ReminderScenario
    {
        return $this->scenario;
    }

    public function setScenario(?ReminderScenario $scenario): static
    {
        $this->scenario = $scenario;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getDelayDays(): ?int
    {
        return $this->delayDays;
    }

    public function setDelayDays(int $delayDays): static
    {
        $this->delayDays = $delayDays;

        return $this;
    }

    public function getTone(): ?string
    {
        return $this->tone;
    }

    public function setTone(string $tone): static
    {
        $this->tone = $tone;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

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
            $reminder->setStep($this);
        }

        return $this;
    }

    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminders->removeElement($reminder)) {
            // set the owning side to null (unless already changed)
            if ($reminder->getStep() === $this) {
                $reminder->setStep(null);
            }
        }

        return $this;
    }
}
