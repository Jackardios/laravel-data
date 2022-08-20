<?php

namespace Spatie\LaravelData\Support\Wrapping;

use Spatie\Enum\Enum;

/**
 * @method static self Disabled()
 * @method static self Enabled()
 * @method static self TemporarilyDisabled()
 */
class WrapExecutionType extends Enum
{
    public function shouldExecute(): bool
    {
        return $this->equals(self::Enabled());
    }
}
