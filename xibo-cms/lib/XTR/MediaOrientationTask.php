<?php


namespace Xibo\XTR;

use Xibo\Factory\MediaFactory;

class MediaOrientationTask implements TaskInterface
{
    use TaskTrait;

    /**
     * @var MediaFactory
     */
    private $mediaFactory;

    /** @inheritdoc */
    public function setFactories($container)
    {
        $this->mediaFactory = $container->get('mediaFactory');
        return $this;
    }

    /** @inheritdoc */
    public function run()
    {
        $this->runMessage = '# ' . __('Media Orientation') . PHP_EOL . PHP_EOL;

        // Long running task
        set_time_limit(0);

        $this->setMediaOrientation();
    }

    private function setMediaOrientation()
    {
        $this->appendRunMessage('# Setting Media Orientation on Library Media files.');

        // onlyMenuBoardAllowed filter means images and videos
        $filesToCheck = $this->mediaFactory->query(null, ['requiresMetaUpdate' => 1, 'onlyMenuBoardAllowed' => 1]);
        $count = 0;

        foreach ($filesToCheck as $media) {
            $count++;
            $filePath = '';
            $libraryFolder = $this->config->getSetting('LIBRARY_LOCATION');

            if ($media->mediaType === 'image') {
                $filePath = $libraryFolder . $media->storedAs;
            } elseif ($media->mediaType === 'video' && file_exists($libraryFolder . $media->mediaId . '_videocover.png')) {
                $filePath = $libraryFolder . $media->mediaId . '_videocover.png';
            }

            if (!empty($filePath)) {
                list($imgWidth, $imgHeight) = @getimagesize($filePath);
                $media->width = $imgWidth;
                $media->height = $imgHeight;
                $media->orientation = ($imgWidth >= $imgHeight) ? 'landscape' : 'portrait';
                $media->save(['saveTags' => false, 'validate' => false]);
            }
        }
        $this->appendRunMessage('Updated ' . $count . ' items');
        $this->disableTask();
    }

    private function disableTask()
    {
        $this->appendRunMessage('# Disabling task.');
        $this->log->debug('Disabling task.');

        $this->getTask()->isActive = 0;
        $this->getTask()->save();

        $this->appendRunMessage(__('Done.'. PHP_EOL));
    }
}
