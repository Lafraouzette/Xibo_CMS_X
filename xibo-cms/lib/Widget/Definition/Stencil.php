<?php


namespace Xibo\Widget\Definition;

/**
 * @SWG\Definition()
 * A Stencil is a template which is rendered in the server and/or client
 * it can optionally have properties and/or elements
 */
class Stencil implements \JsonSerializable
{
    /** @var \Xibo\Widget\Definition\Element[] */
    public $elements = [];

    /** @var string|null */
    public $twig;

    /** @var string|null */
    public $hbs;

    /** @var string|null */
    public $head;

    /** @var string|null */
    public $style;

    /** @var string|null */
    public $hbsId;

    /** @var double Optional positional information if contained as part of an element group */
    public $width;

    /** @var double Optional positional information if contained as part of an element group */
    public $height;

    /** @var double Optional positional information if contained as part of an element group */
    public $gapBetweenHbs;

    /**
     * @SWG\Property(description="An array of element groups")
     * @var \Xibo\Widget\Definition\ElementGroup[]
     */
    public $elementGroups = [];

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'hbsId' => $this->hbsId,
            'hbs' => $this->hbs,
            'head' => $this->head,
            'style' => $this->style,
            'width' => $this->width,
            'height' => $this->height,
            'gapBetweenHbs' => $this->gapBetweenHbs,
            'elements' => $this->elements,
            'elementGroups' => $this->elementGroups
        ];
    }
}
