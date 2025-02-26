<?php



namespace Xibo\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Xibo\Helper\ApplicationState;
use Xibo\Helper\LogoutTrait;
use Xibo\Support\Exception\AccessDeniedException;
use Xibo\Support\Exception\NotFoundException;

/**
 * Class CASAuthentication
 * @package Xibo\Middleware
 *
 * Provide CAS authentication to Xibo configured via settings.php.
 *
 * This class was originally contributed by Emmanuel Blindauer
 */
class CASAuthentication extends AuthenticationBase
{
    use LogoutTrait;

    /**
     * @return $this
     */
    public function addRoutes()
    {
        $app = $this->app;
        $app->getContainer()->set('logoutRoute', 'cas.logout');

        $app->map(['GET', 'POST'], '/cas/login', function (ServerRequest $request, Response $response) {
            // Initiate CAS SSO
            $this->initCasClient();
            \phpCAS::setNoCasServerValidation();

            // Login happens here
            \phpCAS::forceAuthentication();

            $username = \phpCAS::getUser();

            try {
                $user = $this->getUserFactory()->getByName($username);
            } catch (NotFoundException $e) {
                throw new AccessDeniedException('Unable to authenticate');
            }

            if ($user->retired === 1) {
                throw new AccessDeniedException('Sorry this account does not exist or cannot be authenticated.');
            }

            if (isset($user) && $user->userId > 0) {
                // Load User
                $this->getUser(
                    $user->userId,
                    $request->getAttribute('ip_address'),
                    $this->getSession()->get('sessionHistoryId')
                );

                // Overwrite our stored user with this new object.
                $this->setUserForRequest($user);

                // Switch Session ID's
                $this->getSession()->setIsExpired(0);
                $this->getSession()->regenerateSessionId();
                $this->getSession()->setUser($user->userId);

                $user->touch();

                // Audit Log
                // Set the userId on the log object
                $this->getLog()->audit('User', $user->userId, 'Login Granted via CAS', [
                    'UserAgent' => $request->getHeader('User-Agent')
                ]);
            }

            return $response->withRedirect($this->getRouteParser()->urlFor('home'));
        })->setName('cas.login');

        // Service for the logout of the user.
        // End the CAS session and the application session
        $app->get('/cas/logout', function (ServerRequest $request, Response $response) {
            // The order is first: local session to destroy, second the cas session
            // because phpCAS::logout() redirects to CAS server
            $this->completeLogoutFlow(
                $this->getUser(
                    $_SESSION['userid'],
                    $request->getAttribute('ip_address'),
                    $_SESSION['sessionHistoryId']
                ),
                $this->getSession(),
                $this->getLog(),
                $request
            );

            $this->initCasClient();
            \phpCAS::logout();
        })->setName('cas.logout');

        return $this;
    }

    /**
     * Initialise the CAS client
     */
    private function initCasClient()
    {
        $settings = $this->getConfig()->casSettings['config'];
        \phpCAS::client(
            CAS_VERSION_2_0,
            $settings['server'],
            intval($settings['port']),
            $settings['uri'],
            $settings['service_base_url']
        );
    }

    /** @inheritDoc */
    public function redirectToLogin(Request $request)
    {
        if ($this->isAjax($request)) {
            return $this->createResponse($request)
                ->withJson(ApplicationState::asRequiresLogin());
        } else {
            return $this->createResponse($request)
                ->withRedirect($this->getRouteParser()->urlFor('login'));
        }
    }

    /** @inheritDoc */
    public function getPublicRoutes(Request $request)
    {
        return array_merge($request->getAttribute('publicRoutes', []), [
            '/cas/login',
            '/cas/logout',
        ]);
    }

    /** @inheritDoc */
    public function shouldRedirectPublicRoute($route)
    {
        return $this->getSession()->isExpired() && ($route == '/login/ping' || $route == 'clock');
    }

    /** @inheritDoc */
    public function addToRequest(Request $request)
    {
        return $request->withAttribute(
            'excludedCsrfRoutes',
            array_merge($request->getAttribute('excludedCsrfRoutes', []), ['/cas/login', '/cas/logout'])
        );
    }
}
