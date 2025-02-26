<?php


namespace Xibo\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Xibo\Support\Exception\InvalidArgumentException;

trait CustomDisplayProfileMiddlewareTrait
{
    /**
     * @return string
     */
    public static function getClass():string
    {
        return self::class;
    }

    public static function getEditTemplateFunctionName():string
    {
        return 'getCustomEditTemplate';
    }

    public static function getDefaultConfigFunctionName():string
    {
        return 'getDefaultConfig';
    }

    public static function getEditCustomFieldsFunctionName():string
    {
        return 'editCustomConfigFields';
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws InvalidArgumentException
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->getFromContainer('logService')->debug('Loading additional Middleware for Custom Display Profile type:' . self::getType());

        $store = $this->getFromContainer('store');
        $results = $store->select('SELECT displayProfileId FROM displayprofile WHERE type = :type', ['type' => self::getType()]);

        if (count($results) <= 0) {
            $profile = $this->getFromContainer('displayProfileFactory')->createCustomProfile([
                'name' => self::getName(),
                'type' => self::getType(),
                'isDefault' => 1,
                'userId' => $this->getFromContainer('userFactory')->getSuperAdmins()[0]->userId
            ]);
            $profile->save();
        }

        $this->getFromContainer('displayProfileFactory')->registerCustomDisplayProfile(
            self::getType(),
            self::getClass(),
            self::getEditTemplateFunctionName(),
            self::getDefaultConfigFunctionName(),
            self::getEditCustomFieldsFunctionName()
        );
        // Next middleware
        return $handler->handle($request);
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getCustomEditTemplate() : string
    {
        return 'displayprofile-form-edit-'.self::getType().'.twig';
    }

    /** @var \Slim\App */
    private $app;

    /**
     * @param \Slim\App $app
     * @return $this
     */
    public function setApp(App $app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @return \Slim\App
     */
    protected function getApp()
    {
        return $this->app;
    }

    /**
     * @return \Psr\Container\ContainerInterface|null
     */
    protected function getContainer()
    {
        return $this->app->getContainer();
    }

    /***
     * @param $key
     * @return mixed
     */
    protected function getFromContainer($key)
    {
        return $this->getContainer()->get($key);
    }

    private static function handleChangedSettings($setting, $oldValue, $newValue, &$changedSettings)
    {
        if ($oldValue != $newValue) {
            $changedSettings[$setting] = $oldValue . ' > ' . $newValue;
        }
    }
}
