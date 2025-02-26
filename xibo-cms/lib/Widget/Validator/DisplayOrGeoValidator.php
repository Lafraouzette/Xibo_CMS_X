<?php


namespace Xibo\Widget\Validator;

use Respect\Validation\Validator as v;
use Xibo\Entity\Module;
use Xibo\Entity\Widget;
use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Widget\Provider\WidgetValidatorInterface;
use Xibo\Widget\Provider\WidgetValidatorTrait;

/**
 * Validate that we either use display location or a lat/lng have been set
 */
class DisplayOrGeoValidator implements WidgetValidatorInterface
{
    use WidgetValidatorTrait;

    /** @inheritDoc */
    public function validate(Module $module, Widget $widget, string $stage): void
    {
        $useDisplayLocation = $widget->getOptionValue('useDisplayLocation', null);
        if ($useDisplayLocation === null) {
            foreach ($module->properties as $property) {
                if ($property->id === 'useDisplayLocation') {
                    $useDisplayLocation = $property->default;
                }
            }
        }
        if ($useDisplayLocation === 0) {
            // Validate lat/long
            // only if they have been provided (our default is the CMS lat/long).
            $lat = $widget->getOptionValue('latitude', null);
            if (!empty($lat) && !v::latitude()->validate($lat)) {
                throw new InvalidArgumentException(__('The latitude entered is not valid.'), 'latitude');
            }

            $lng = $widget->getOptionValue('longitude', null);
            if (!empty($lng) && !v::longitude()->validate($lng)) {
                throw new InvalidArgumentException(__('The longitude entered is not valid.'), 'longitude');
            }
        }
    }
}
