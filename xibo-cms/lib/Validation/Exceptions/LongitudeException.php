<?php


namespace Xibo\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

/**
 * Latitude Exception
 */
class LongitudeException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Longitude is not valid',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Negative of Longitude is not valid.',
        ],
    ];
}
