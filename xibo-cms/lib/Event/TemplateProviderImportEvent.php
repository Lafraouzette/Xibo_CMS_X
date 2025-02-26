<?php


namespace Xibo\Event;

/**
 * Event raised when one or more provider search results have been chosen for importing on a layout
 */
class TemplateProviderImportEvent extends Event
{
    protected static $NAME = 'connector.provider.template.import';
    /**
     * @var string
     */
    private $downloadUrl;
    /** @var string */
    private $libraryLocation;
    /**
     * @var string
     */
    private $fileName;
    /** @var string */
    private $tempFile;

    public function __construct(
        string $uri,
        string $fileName,
        string $libraryLocation
    ) {
        $this->downloadUrl = $uri;
        $this->fileName = $fileName;
        $this->libraryLocation = $libraryLocation;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getLibraryLocation(): string
    {
        return $this->libraryLocation;
    }

    public function setFilePath($tempFile)
    {
        $this->tempFile = $tempFile;
    }

    public function getFilePath()
    {
        return $this->tempFile;
    }
}
