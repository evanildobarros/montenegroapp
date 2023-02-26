<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use App\Middleware\DispositivosMiddleware;
use App\Policy\RequestPolicy;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Middleware\RequestAuthorizationMiddleware;
use Authorization\Policy\MapResolver;
use Authorization\Policy\OrmResolver;
use Authorization\Policy\ResolverCollection;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\Exception\MissingPluginException;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use PagSeguro\Library as PagSeguroLibrary;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements
    AuthenticationServiceProviderInterface,
    AuthorizationServiceProviderInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        } else {
            FactoryLocator::add(
                'Table',
                (new TableLocator())->allowFallbackClass(false)
            );
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addPlugin('DebugKit', [
                'bootstrap' => true,
                'routes' => true,
                'middleware' => true,
            ]);
        }

        // Load more plugins here
        $this->addPlugin('AuditLog');
        $this->addPlugin('Cors');
        $this->addPlugin('AssetMix');
        $this->addPlugin('Search');
        $this->addPlugin('Winsite', ['bootstrap' => true]);
        $this->addPlugin('Crud');
        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');
        $this->addPlugin('Correios');
        $this->addPlugin('Josegonzalez/Upload');
        $this->addPlugin('Queue', ['routes' => true]);

        PagSeguroLibrary::initialize();
        \PagSeguro\Configuration\Configure::setCharset('UTF-8');
    }

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        $params = $request->getAttribute('params');
        if (isset($params['prefix']) && $params['prefix'] == 'Api') {
            if (!file_exists(CONFIG . 'jwt.pem')) {
                throw new InternalErrorException('Chave pública do JWT não encontrada!');
            }

            $fields = [
                IdentifierInterface::CREDENTIAL_USERNAME => 'email',
                IdentifierInterface::CREDENTIAL_PASSWORD => 'senha',
            ];

            $service->loadAuthenticator('Authentication.Jwt', [
                'secretKey' => file_get_contents(CONFIG . 'jwt.pem'),
                'algorithms' => ['RS512'],
                'returnPayload' => false,
            ]);
            $service->loadAuthenticator('Authentication.Form', [
                'fields' => $fields,
                'loginUrl' => Router::url([
                    'controller' => 'Pessoas',
                    'action' => 'login',
                    'plugin' => null,
                    'prefix' => 'Api',
                ]),
            ]);

            $service->loadIdentifier('Authentication.Password', [
                'fields' => $fields,
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'userModel' => 'Pessoas',
                    'finder' => 'ativo',
                ],
            ]);
            $service->loadIdentifier('Authentication.JwtSubject', [
                'fields' => $fields,
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'userModel' => 'Pessoas',
                ],
            ]);

            return $service;
        }

        $service->setConfig([
            'unauthenticatedRedirect' => Router::url([
                'controller' => 'Users',
                'action' => 'login',
                'plugin' => null,
            ]),
            'queryParam' => 'redirect',
        ]);

        $service->loadAuthenticator('Authentication.Session');
        $service->loadAuthenticator('Authentication.Form', [
            'loginUrl' => Router::url([
                'controller' => 'Users',
                'action' => 'login',
                'plugin' => null,
            ]),
        ]);

        $service->loadIdentifier('Authentication.Password');

        return $service;
    }

    /**
     * Returns authorization service instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authorization\AuthorizationServiceInterface AuthorizationServiceInterface
     */
    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $appNamespace = 'App';

        $overrides = [
            'Pedido',
            'PedidosTable',
            'Pessoa',
            'RotasTable',
            'RotaPedido',
        ];
        $ormResolver = new OrmResolver($appNamespace, $overrides);

        $mapResolver = new MapResolver([
            ServerRequest::class => RequestPolicy::class,
        ]);

        $resolver = new ResolverCollection([$mapResolver, $ormResolver]);

        return new AuthorizationService($resolver);
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())

            // Cross Site Request Forgery (CSRF) Protection Middleware
            // https://book.cakephp.org/4/en/controllers/middleware.html#cross-site-request-forgery-csrf-middleware
//            ->add(new CsrfProtectionMiddleware([
//                'httponly' => true,
//            ]))
            ->add(new AuthenticationMiddleware($this))

            // Add authorization (after authentication if you are using that plugin too).
            ->add(new AuthorizationMiddleware($this, [
//                'requireAuthorizationCheck' => false,
                'identityDecorator' => function ($authorizationService, $user) {
                    return $user->setAuthorization($authorizationService);
                },
            ]))
            ->add(new RequestAuthorizationMiddleware())
            ->add(new DispositivosMiddleware());

        return $middlewareQueue;
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/4/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
    }

    /**
     * Bootstrapping for CLI application.
     *
     * That is when running commands.
     *
     * @return void
     */
    protected function bootstrapCli(): void
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');

        // Load more plugins here
        if (Configure::read('debug')) {
            $this->addPlugin('IdeHelper');
        }
    }
}
