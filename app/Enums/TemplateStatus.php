<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TemplateStatus extends Enum
{
    const STATUS_PRIVATE = 0;
    const STATUS_REVIEWING = 1;
    const STATUS_PUBLIC = 2;
    const STATUS_UNPUBLIC = 3;
}
