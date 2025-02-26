<?php


namespace Xibo\Service;

use Xibo\Service\ImageProcessingServiceInterface;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManagerStatic as Img;

/**
 * Class ImageProcessingService
 * @package Xibo\Service
 */
class ImageProcessingService implements ImageProcessingServiceInterface
{

    /** @var LogServiceInterface */
    private $log;

    /**
     * @inheritdoc
     */
    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public function setDependencies($log)
    {
        $this->log = $log;
        return $this;
    }

    /** @inheritdoc */
    public function resizeImage($filePath, $width, $height)
    {
        try {
            Img::configure(array('driver' => 'gd'));
            $img = Img::make($filePath);
            $img->resize($width, $height, function ($constraint)  {
                $constraint->aspectRatio();
            });

            // Get the updated height and width
            $updatedHeight = $img->height();
            $updatedWidth = $img->width();

            $img->save($filePath);
            $img->destroy();
        } catch (NotReadableException $notReadableException) {
            $this->log->error('Image not readable: ' . $notReadableException->getMessage());
        }

        return [
            'filePath' => $filePath,
            'height' => $updatedHeight ?? $height,
            'width' => $updatedWidth ?? $width
        ];
    }
}