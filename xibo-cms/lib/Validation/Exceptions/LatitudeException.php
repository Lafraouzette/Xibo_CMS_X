<?php


namespace Xibo\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

/**
 * Latitude Exception
 */
class LatitudeException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Latitude is not valid',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Negative of Latitude is not valid.',
        ],
    ];
}
