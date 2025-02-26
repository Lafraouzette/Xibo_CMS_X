<?php


namespace Xibo\Widget\DataType;

use Xibo\Widget\Definition\DataType;

/**
 * Product Category (primarily used for the Menu Board component)
 */
class ProductCategory implements \JsonSerializable, DataTypeInterface
{
    public $name;
    public $description;
    public $image;

    public function getDefinition(): DataType
    {
        $dataType = new DataType();
        $dataType->id = 'product-category';
        $dataType->name = __('Product Category');
        $dataType->addField('name', __('Name'), 'string');
        $dataType->addField('description', __('Description'), 'string');
        $dataType->addField('image', __('Image'), 'int');
        return $dataType;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }
}
