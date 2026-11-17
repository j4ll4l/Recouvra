<?php

namespace App\Factory;

use App\Entity\ReminderStep;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ReminderStep>
 */
final class ReminderStepFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return ReminderStep::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'scenario' => ReminderScenarioFactory::new(),
            'position' => 1,
            'delayDays' => 3,
            'tone' => 'courteous',
            'subject' => 'Rappel facture {numero}',
            'body' => 'Bonjour {client}, sauf erreur de notre part, la facture {numero} de {montant} € est échue depuis le {echeance}. Merci de procéder au règlement.',
        ];
    }

    public function courteous(): static
    {
        return $this->with([
            'position' => 1,
            'delayDays' => 3,
            'tone' => 'courteous',
            'subject' => 'Rappel facture {numero}',
            'body' => 'Bonjour {client}, sauf erreur de notre part, la facture {numero} de {montant} € est échue depuis le {echeance}. Merci de procéder au règlement.',
        ]);
    }

    public function firm(): static
    {
        return $this->with([
            'position' => 2,
            'delayDays' => 15,
            'tone' => 'firm',
            'subject' => 'Facture {numero} impayée',
            'body' => 'Bonjour {client}, malgré notre précédent rappel, la facture {numero} de {montant} € reste impayée depuis {jours_retard} jours. Merci de régulariser sous 8 jours.',
        ]);
    }

    public function formalNotice(): static
    {
        return $this->with([
            'position' => 3,
            'delayDays' => 30,
            'tone' => 'formal_notice',
            'subject' => 'Mise en demeure – facture {numero}',
            'body' => 'Madame, Monsieur, nous vous mettons en demeure de régler la somme de {montant} € au titre de la facture {numero} sous 8 jours, faute de quoi nous engagerons une procédure de recouvrement.',
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ReminderStep $reminderStep): void {})
        ;
    }
}
