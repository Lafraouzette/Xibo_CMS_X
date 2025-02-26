<?php


namespace Xibo\Widget\DataType;

use Xibo\Widget\Definition\DataType;

/**
 * Product DataType (primarily used for the Menu Board component)
 */
class Product implements \JsonSerializable, DataTypeInterface
{
    public $name;
    public $price;
    public $description;
    public $availability;
    public $allergyInfo;
    public $calories;
    public $image;
    public $productOptions;

    public function getDefinition(): DataType
    {
        $dataType = new DataType();
        $dataType->id = 'product';
        $dataType->name = __('Product');
        $dataType->addField('name', __('Name'), 'string');
        $dataType->addField('price', __('Price'), 'decimal');
        $dataType->addField('description', __('Description'), 'string');
        $dataType->addField('availability', __('Availability'), 'int');
        $dataType->addField('allergyInfo', __('Allergy Information'), 'string');
        $dataType->addField('calories', __('Calories'), 'string');
        $dataType->addField('image', __('Image'), 'int');
        $dataType->addField('productOptions', __('Product Options'), 'array');
        return $dataType;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'availability' => $this->availability,
            'calories' => $this->calories,
            'allergyInfo' => $this->allergyInfo,
            'image' => $this->image,
            'productOptions' => $this->productOptions,
        ];
    }
}
