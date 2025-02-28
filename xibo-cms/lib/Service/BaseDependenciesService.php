<?php


namespace Xibo\Service;

use Psr\Log\NullLogger;
use Slim\Views\Twig;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Xibo\Entity\User;
use Xibo\Helper\ApplicationState;
use Xibo\Helper\NullSanitizer;
use Xibo\Helper\NullView;
use Xibo\Helper\SanitizerService;
use Xibo\Storage\PdoStorageService;

class BaseDependenciesService
{
    /**
     * @var LogServiceInterface
     */
    private $log;

    /**
     * @var  SanitizerService
     */
    private $sanitizerService;

    /**
     * @var ApplicationState
     */
    private $state;

    /**
     * @var ConfigServiceInterface
     */
    private $configService;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Twig
     */
    private $view;

    /**
     * @var PdoStorageService
     */
    private $storageService;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $dispatcher;

    public function setLogger(LogServiceInterface $logService)
    {
        $this->log = $logService;
    }

    /**
     * @return LogServiceInterface
     */
    public function getLogger()
    {
        if ($this->log === null) {
            $this->log = new NullLogService(new NullLogger());
        }

        return $this->log;
    }

    public function setSanitizer(SanitizerService $sanitizerService)
    {
        $this->sanitizerService = $sanitizerService;
    }

    public function getSanitizer(): SanitizerService
    {
        if ($this->sanitizerService === null) {
            $this->sanitizerService = new NullSanitizer();
        }

        return $this->sanitizerService;
    }

    public function setState(ApplicationState $applicationState)
    {
        $this->state = $applicationState;
    }

    public function getState(): ApplicationState
    {
        return $this->state;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setConfig(ConfigServiceInterface $configService)
    {
        $this->configService = $configService;
    }

    public function getConfig() : ConfigServiceInterface
    {
        return $this->configService;
    }

    public function setView(Twig $view)
    {
        $this->view = $view;
    }

    public function getView() : Twig
    {
        if ($this->view === null) {
            $this->view = new NullView();
        }
        return $this->view;
    }

    public function setStore(PdoStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function getStore()
    {
        return $this->storageService;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher): BaseDependenciesService
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        if ($this->dispatcher === null) {
            $this->getLogger()->error('getDispatcher: [base] No dispatcher found, returning an empty one');
            $this->dispatcher = new EventDispatcher();
        }
        return $this->dispatcher;
    }
}
