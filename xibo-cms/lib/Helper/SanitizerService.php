<?php


namespace Xibo\Helper;


use Xibo\Support\Sanitizer\RespectSanitizer;
use Xibo\Support\Sanitizer\SanitizerInterface;
use Xibo\Support\Validator\RespectValidator;
use Xibo\Support\Validator\ValidatorInterface;

class SanitizerService
{
    /**
     * @param $array
     * @return SanitizerInterface
     */
    public function getSanitizer($array)
    {
        return (new RespectSanitizer())
            ->setCollection($array)
            ->setDefaultOptions([
                'checkboxReturnInteger' => true
            ]);
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return new RespectValidator();
    }
}