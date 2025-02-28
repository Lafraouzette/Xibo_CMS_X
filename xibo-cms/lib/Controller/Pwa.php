<?php


namespace Xibo\Controller;

use Psr\Container\ContainerInterface;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;
use Xibo\Factory\DisplayFactory;
use Xibo\Support\Exception\AccessDeniedException;
use Xibo\Support\Exception\GeneralException;
use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Xmds\Soap7;

/**
 * PWA
 *  routes for a PWA to download resources which live in an iframe
 */
class Pwa extends Base
{
    public function __construct(
        private readonly DisplayFactory $displayFactory,
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Xibo\Support\Exception\NotFoundException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Xibo\Support\Exception\AccessDeniedException
     * @throws \Xibo\Support\Exception\GeneralException
     */
    public function getResource(Request $request, Response $response): Response
    {
        // Create a Soap client and call it.
        $params = $this->getSanitizer($request->getParams());

        try {
            // Which version are we?
            $version = $params->getInt('v', [
                'default' => 7,
                'throw' => function () {
                    throw new InvalidArgumentException(__('Missing Version'), 'v');
                }
            ]);

            if ($version < 7) {
                throw new InvalidArgumentException(__('PWA supported from XMDS schema 7 onward.'), 'v');
            }

            // Validate that this display should call this service.
            $hardwareKey = $params->getString('hardwareKey');
            $display = $this->displayFactory->getByLicence($hardwareKey);
            if (!$display->isPwa()) {
                throw new AccessDeniedException(__('Please use XMDS API'), 'hardwareKey');
            }

            // Check it is still authorised.
            if ($display->licensed == 0) {
                throw new AccessDeniedException(__('Display unauthorised'));
            }

            /** @var Soap7 $soap */
            $soap = $this->getSoap($version);

            $this->getLog()->debug('getResource: passing to Soap class');

            $body = $soap->GetResource(
                $params->getString('serverKey'),
                $params->getString('hardwareKey'),
                $params->getInt('layoutId'),
                $params->getInt('regionId') . '',
                $params->getInt('mediaId') . '',
            );

            $response->getBody()->write($body);

            return $response
                ->withoutHeader('Content-Security-Policy');
        } catch (\SoapFault $e) {
            throw new GeneralException($e->getMessage());
        }
    }

    /**
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Xibo\Support\Exception\NotFoundException
     * @throws \Xibo\Support\Exception\GeneralException
     */
    public function getData(Request $request, Response $response): Response
    {
        $params = $this->getSanitizer($request->getParams());

        try {
            $version = $params->getInt('v', [
                'default' => 7,
                'throw' => function () {
                    throw new InvalidArgumentException(__('Missing Version'), 'v');
                }
            ]);

            if ($version < 7) {
                throw new InvalidArgumentException(__('PWA supported from XMDS schema 7 onward.'), 'v');
            }

            // Validate that this display should call this service.
            $hardwareKey = $params->getString('hardwareKey');
            $display = $this->displayFactory->getByLicence($hardwareKey);
            if (!$display->isPwa()) {
                throw new AccessDeniedException(__('Please use XMDS API'), 'hardwareKey');
            }

            // Check it is still authorised.
            if ($display->licensed == 0) {
                throw new AccessDeniedException(__('Display unauthorised'));
            }

            /** @var Soap7 $soap */
            $soap = $this->getSoap($version);
            $body = $soap->GetData(
                $params->getString('serverKey'),
                $params->getString('hardwareKey'),
                $params->getInt('widgetId'),
            );

            $response->getBody()->write($body);

            return $response
                ->withoutHeader('Content-Security-Policy');
        } catch (\SoapFault $e) {
            throw new GeneralException($e->getMessage());
        }
    }

    /**
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getSoap(int $version): mixed
    {
        $class = '\Xibo\Xmds\Soap' . $version;
        if (!class_exists($class)) {
            throw new InvalidArgumentException(__('Unknown version'), 'version');
        }

        // Overwrite the logger
        $uidProcessor = new \Monolog\Processor\UidProcessor(7);
        $logProcessor = new \Xibo\Xmds\LogProcessor(
            $this->container->get('logger'),
            $uidProcessor->getUid()
        );
        $this->container->get('logger')->pushProcessor($logProcessor);

        return new $class(
            $logProcessor,
            $this->container->get('pool'),
            $this->container->get('store'),
            $this->container->get('timeSeriesStore'),
            $this->container->get('logService'),
            $this->container->get('sanitizerService'),
            $this->container->get('configService'),
            $this->container->get('requiredFileFactory'),
            $this->container->get('moduleFactory'),
            $this->container->get('layoutFactory'),
            $this->container->get('dataSetFactory'),
            $this->displayFactory,
            $this->container->get('userGroupFactory'),
            $this->container->get('bandwidthFactory'),
            $this->container->get('mediaFactory'),
            $this->container->get('widgetFactory'),
            $this->container->get('regionFactory'),
            $this->container->get('notificationFactory'),
            $this->container->get('displayEventFactory'),
            $this->container->get('scheduleFactory'),
            $this->container->get('dayPartFactory'),
            $this->container->get('playerVersionFactory'),
            $this->container->get('dispatcher'),
            $this->container->get('campaignFactory'),
            $this->container->get('syncGroupFactory'),
            $this->container->get('playerFaultFactory')
        );
    }
}
