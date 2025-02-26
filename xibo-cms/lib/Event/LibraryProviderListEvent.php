<?php


namespace Xibo\Event;

use Xibo\Connector\ProviderDetails;

class LibraryProviderListEvent extends Event
{
    protected static $NAME = 'connector.provider.library.list';
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
     * @return LibraryProviderListEvent
     */
    public function addProvider(ProviderDetails $provider): LibraryProviderListEvent
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
