<?php


namespace Xibo\Widget\Definition;

/**
 * Option: typically used when paired with a dropdown
 * @SWG\Definition()
 */
class Option implements \JsonSerializable
{
    /**
     * @SWG\Property(description="Name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Image: optional image asset")
     * @var string
     */
    public $image;

    /**
     * @SWG\Property(description="Set")
     * @var string[]
     */
    public $set = [];

    /**
     * * @SWG\Property(description="Title: shown in the dropdown/select")
     * @var string
     */
    public $title;

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'image' => $this->image,
            'set' => $this->set,
            'title' => $this->title
        ];
    }
}
