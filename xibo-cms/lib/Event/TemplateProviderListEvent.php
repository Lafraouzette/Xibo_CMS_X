<?php


namespace Xibo\Event;

use Xibo\Connector\ProviderDetails;

/**
 * Get a list of template providers
 */
class TemplateProviderListEvent extends Event
{
    protected static $NAME = 'connector.provider.template.list';
    /**
     * @var array
     */
    private mixed $providers;

    public function __construct($providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * @param ProviderDetails $provider
     * @return TemplateProviderListEvent
     */
    public function addProvider(ProviderDetails $provider): TemplateProviderListEvent
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * @return ProviderDetails[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }
}
