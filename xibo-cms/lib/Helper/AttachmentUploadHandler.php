<?php


namespace Xibo\Helper;

/**
 * Class AttachmentUploadHandler
 * @package Xibo\Helper
 */
class AttachmentUploadHandler extends BlueImpUploadHandler
{
    /**
     * @param $file
     * @param $index
     */
    protected function handleFormData($file, $index)
    {
        $controller = $this->options['controller'];
        /* @var \Xibo\Controller\Notification $controller */

        $controller->getLog()->debug('Upload complete for name: ' . $file->name);
    }
}