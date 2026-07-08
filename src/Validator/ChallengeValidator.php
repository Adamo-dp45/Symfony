<?php

namespace App\Validator;

use App\Domain\Antispam\ChallengeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ChallengeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ChallengeInterface $challenge
    )
    {
    }

    /**
     * Summary of validate
     * @param array{challenge: string, answer: string} $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var Challenge $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if(!$this->challenge->verify($value['challenge'], $value['answer'] ?? '')) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
