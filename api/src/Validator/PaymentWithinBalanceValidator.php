<?php

namespace App\Validator;

use App\Entity\Payment;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class PaymentWithinBalanceValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PaymentWithinBalance) {
            throw new UnexpectedTypeException($constraint, PaymentWithinBalance::class);
        }

        if (!$value instanceof Payment || null === $value->getInvoice() || null === $value->getAmountCents()) {
            return; // NotNull s'occupe déjà de ces cas
        }

        $remaining = $value->getInvoice()->getRemainingCents();

        if ($value->getAmountCents() > $remaining) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ amount }}', number_format($value->getAmountCents() / 100, 2, ',', ' '))
                ->setParameter('{{ remaining }}', number_format($remaining / 100, 2, ',', ' '))
                ->atPath('amountCents')
                ->addViolation();
        }
    }
}
