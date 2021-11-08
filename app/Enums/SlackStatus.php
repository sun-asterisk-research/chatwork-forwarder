<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SlackStatus extends Enum
{
    const NO_PERMISSION = 'no_permission';
    const RATE_LIMITED =   'ratelimited';
    const INVALID_AUTH = 'invalid_auth';
    const INVALID_BLOCKS = 'invalid_blocks_format';
}
