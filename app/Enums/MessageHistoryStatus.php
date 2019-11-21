<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class MessageHistoryStatus extends Enum
{
    const SUCCESS = 0;
    const FAILED = 1;
}
