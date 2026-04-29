<?php

namespace Marvel\Enums;

use BenSampo\Enum\Enum;

final class PromotionType extends Enum
{
    public const PERCENTAGE = 'percentage';
    public const FIXED = 'fixed_rate';
    public const AMOUNT = 'amount';
}
