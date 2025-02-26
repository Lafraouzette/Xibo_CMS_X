<?php


namespace Xibo\Widget\Definition;

/**
 * @SWG\Definition()
 * A class representing one template extending another
 */
class Extend implements \JsonSerializable
{
    public $template;
    public $override;
    public $with;
    public $escapeHtml;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'template' => $this->template,
            'override' => $this->override,
            'with' => $this->with,
            'escapeHtml' => $this->escapeHtml,
        ];
    }
}
