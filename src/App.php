<?php

namespace Baubyte;

use Baubyte\Config\Config;
use Baubyte\Database\Drivers\DatabaseDriver;
use Baubyte\Database\Model;
use Baubyte\Http\HttpMethod;
use Baubyte\Http\HttpNotFoundException;
use Baubyte\Http\Request;
use Baubyte\Http\Response;
use Baubyte\Routing\Router;
use Baubyte\Server\Server;
use Baubyte\Session\Session;
use Baubyte\Session\SessionStorage;
use Baubyte\Validation\Exceptions\ValidationException;
use Baubyte\View\View;
use Dotenv\Dotenv;
use Throwable;

/**
 * App runtime.
 */
class App {
    /**
     * Root Directory
     *
     * @var string
     */
    public static string $root;

    /**
     * Router instance.
     *
     * @var \Baubyte\Routing\Router
     */
    public Router $router;
    /**
     * Current HTTP request.
     *
     * @var \Baubyte\Http\Request
     */
    public Request $request;
    /**
     * Server Instance
     *
     * @var \Baubyte\Server\Server
     */
    public Server $server;

    /**
     * View Instance
     *
     * @var \Baubyte\View\View
     */
    public View $view;
    /**
     * Session Instance
     *
     * @var \Baubyte\Session\Session
     */
    public Session $session;

    /**
     * Database Drivers
     *
     * @var \Baubyte\Database\Drivers\DatabaseDriver
     */
    public DatabaseDriver $database;
    /**
     * Create a new app instance.
     *
     * @return self
     */
    public static function bootstrap(string $root): self {
        self::$root = $root;
        $app = singleton(self::class);

        return $app
                ->loadConfig()
                ->runServiceProviders('boot')
                ->setHttpHandlers()
                ->setUpDatabaseConnections()
                ->runServiceProviders('runtime');
    }
    /**
     * Handle request and send response.
     *
     */
    public function run() {
        try {
            $response = $this->router->resolve($this->request);
            $this->terminate($response);
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * Respond with error.
     *
     * @param Throwable $e
     */
    private function handleError(Throwable $e) {
        match (get_class($e)) {
            ValidationException::class => $this->abort(back()->withErrors($e->errors())),
            HttpNotFoundException::class => $this->abort(view("errors/404")->setStatus(404)),
            default => $this->abort(view("errors/500", compact('e'), "error")->setStatus(500)),
        };
    }

    /**
     * Load Baubyte configuration.
     *
     * @return self
     */
    protected function loadConfig(): self {
        Dotenv::createImmutable(self::$root)->load();
        Config::load(self::$root .DIRECTORY_SEPARATOR."config");
        return $this;
    }

    /**
     * Register container instances.
     *
     * @param string $type
     * @return self
     */
    protected function runServiceProviders(string $type): self {
        foreach (config("providers.{$type}", []) as $provider) {
            $provider = new $provider();
            $provider->registerServices();
        }
        return $this;
    }

    protected function setHttpHandlers(): self {
        $this->router = singleton(Router::class);
        $this->server = app(Server::class);
        $this->request = singleton(Request::class, fn () => $this->server->getRequest());
        $this->session = singleton(Session::class, fn () => new Session(app(SessionStorage::class)));
        return $this;
    }

    /**
     * Open database connections or other connections.
     *
     * @return self
     */
    protected function setUpDatabaseConnections(): self {
        $this->database = app(DatabaseDriver::class);
        $this->database->connect(
            config("database.connection"),
            config("database.host"),
            config("database.port"),
            config("database.database"),
            config("database.username"),
            config("database.password")
        );
        Model::setDatabaseDriver($this->database);

        return $this;
    }

    /**
     * Set session variables or other parameters for the next request.
     */
    protected function prepareNextRequest() {
        if ($this->request->method() == HttpMethod::GET()) {
            $this->session->set('_previous', $this->request->uri());
        }
    }

    /**
     * Kill the current process. If necessary, release resources here.
     *
     * @param \Baubyte\Http\Response $response
     */
    protected function terminate(Response $response) {
        $this->prepareNextRequest();
        $this->server->sendResponse($response);
        $this->database->close();
        exit();
    }
    /**
     * Stop execution from any point.
     *
     * @param \Baubyte\Http\Response $response
     * @return \Baubyte\Http\Response $response
     */
    public function abort(Response $response) {
        $this->terminate($response);
    }
}
