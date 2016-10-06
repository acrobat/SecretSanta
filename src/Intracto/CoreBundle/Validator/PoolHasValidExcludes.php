<?php

namespace Intracto\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PoolHasValidExcludes extends Constraint
{
    public $message = 'pool.non_unique';

    public function validatedBy()
    {
        return 'intracto_core.validator.pool_has_valid_excludes';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
