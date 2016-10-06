<?php

namespace Intracto\SecretSantaBundle\Validator;

use Intracto\Domain\Entry\Service\Shuffler;
use Intracto\Domain\Pool\Model\Pool;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PoolHasValidExcludesValidator extends ConstraintValidator
{
    private $entryShuffler;

    // Todo: find a way to activate this validator only if EntryHasValidExcludes passes validation.
    public function __construct(Shuffler $entryShuffler)
    {
        $this->entryShuffler = $entryShuffler;
    }

    public function validate($pool, Constraint $constraint)
    {
        /*
         * @var Pool
         */
        if (!$this->entryShuffler->shuffleEntries($pool)) {
            $this->context->addViolationAt(
                'entries',
                $constraint->message
            );
        }
    }
}
