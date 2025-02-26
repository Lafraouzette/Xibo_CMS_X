<?php


namespace Xibo\Helper;

use Xibo\Support\Exception\LibraryFullException;

/**
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName
 */
class UploadHandler extends BlueImpUploadHandler
{
    /**
     * @var callable
     */
    private $postProcess;

    /** @var ApplicationState */
    private $state;

    /**
     * Set post processor
     * @param callable $function
     */
    public function setPostProcessor(callable $function)
    {
        $this->postProcess = $function;
    }

    /**
     * @param ApplicationState $state
     * @return $this
     */
    public function setState(ApplicationState $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Handle form data from BlueImp
     * @param $file
     * @param $index
     */
    protected function handleFormData($file, $index)
    {
        try {
            $filePath = $this->getUploadDir() . $file->name;
            $file->fileName = $file->name;

            $name = htmlspecialchars($this->getParam($index, 'name', $file->name));
            $file->name = $name;

            // Check Library
            if ($this->options['libraryQuotaFull']) {
                throw new LibraryFullException(
                    sprintf(
                        __('Your library is full. Library Limit: %s K'),
                        $this->options['libraryLimit']
                    )
                );
            }

            $this->getLogger()->debug('Upload complete for name: ' . $name . '. Index is ' . $index);

            if ($this->postProcess !== null) {
                $file = call_user_func($this->postProcess, $file, $this);
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error('Error uploading file : ' . $exception->getMessage());
            $this->getLogger()->debug($exception->getTraceAsString());

            // Unlink the temporary file
            @unlink($filePath);
            $this->state->setCommitState(false);
            $file->error = $exception->getMessage();
        }

        return $file;
    }

    /**
     * Get Param from File Input, taking into account multi-upload index if applicable
     * @param int $index
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    private function getParam($index, $param, $default)
    {
        if ($index === null) {
            if (isset($_REQUEST[$param])) {
                return $_REQUEST[$param];
            } else {
                return $default;
            }
        } else {
            if (isset($_REQUEST[$param][$index])) {
                return $_REQUEST[$param][$index];
            } else {
                return $default;
            }
        }
    }
}
