<?php


namespace Xibo\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

/**
 * Class Latitude
 * @package Xibo\Validation\Rules
 */
class Latitude extends AbstractRule
{
    /** @inheritdoc */
    public function validate($input): bool
    {
        if (!is_numeric($input)) {
            return false;
        }

        $latitude = doubleval($input);

        return ($latitude >= -90 && $latitude <= 90);
    }
}
