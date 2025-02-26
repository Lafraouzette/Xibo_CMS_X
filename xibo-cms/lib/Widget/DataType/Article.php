<?php


namespace Xibo\Widget\DataType;

use Xibo\Widget\Definition\DataType;

/**
 * An article, usually from a blog or news feed.
 */
class Article implements \JsonSerializable, DataTypeInterface
{
    public static $NAME = 'article';
    public $title;
    public $summary;
    public $content;
    public $author;
    public $permalink;
    public $link;
    public $image;

    /** @var \Carbon\Carbon */
    public $date;

    /** @var \Carbon\Carbon */
    public $publishedDate;

    /** @inheritDoc */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'author' => $this->author,
            'permalink' => $this->permalink,
            'link' => $this->link,
            'date' => $this->date->format('c'),
            'publishedDate' => $this->publishedDate->format('c'),
            'image' => $this->image,
        ];
    }

    public function getDefinition(): DataType
    {
        $dataType = new DataType();
        $dataType->id = self::$NAME;
        $dataType->name = __('Article');
        $dataType
            ->addField('title', __('Title'), 'text')
            ->addField('summary', __('Summary'), 'text')
            ->addField('content', __('Content'), 'text')
            ->addField('author', __('Author'), 'text')
            ->addField('permalink', __('Permalink'), 'text')
            ->addField('link', __('Link'), 'text')
            ->addField('date', __('Created Date'), 'datetime')
            ->addField('publishedDate', __('Published Date'), 'datetime')
            ->addField('image', __('Image'), 'image');
        return $dataType;
    }
}
