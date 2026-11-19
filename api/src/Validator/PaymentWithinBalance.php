<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class PaymentWithinBalance extends Constraint
{
    public string $message = 'Le paiement ({{ amount }} €) dépasse le reste dû ({{ remaining }} €).';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
