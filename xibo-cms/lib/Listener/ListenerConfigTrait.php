<?php


namespace Xibo\Listener;

use Xibo\Service\ConfigServiceInterface;

trait ListenerConfigTrait
{
    /** @var ConfigServiceInterface */
    private $config;

    /**
     * @param ConfigServiceInterface $config
     * @return $this
     */
    public function useConfig(ConfigServiceInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return ConfigServiceInterface
     */
    protected function getConfig()
    {
        return $this->config;
    }
}
