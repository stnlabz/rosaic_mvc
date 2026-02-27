<?php
declare(strict_types=1);

/**
 * Router
 *
 * Routes requests to controllers/methods in a strict and secure way.
 * - No silent fallback to index()
 * - Strict controller/method name validation
 * - Blocks magic/underscore methods
 * - Throws internally, responds with controlled 404 externally (Strategy D)
 *
 * @package STN-Labz
 */

final class router
{
    /**
     * Controller file directory (relative to APPROOT).
     *
     * @var string
     */
    private string $controllersDir = '/controllers';

    /**
     * Default controller name.
     *
     * @var string
     */
    private string $defaultController = 'home';

    /**
     * Default method name.
     *
     * @var string
     */
    private string $defaultMethod = 'index';

    /**
     * Run router dispatch with controlled failure handling.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $this->dispatch();
        } catch (Throwable $e) {
            $this->logException($e);
            $this->respond404();
        }
    }

    /**
     * Dispatch request to controller and method.
     *
     * @return void
     * @throws RuntimeException When routing cannot proceed safely.
     */
    private function dispatch(): void
    {
        $url = $this->get_url();

        // Shortcuts (kept, but made deterministic)
        if (($url[0] ?? '') === 'login') {
            $url = ['auth', 'login'];
        } elseif (($url[0] ?? '') === 'logout') {
            $url = ['auth', 'logout'];
        }

        $controllerName = $this->normalizeControllerName($url[0] ?? $this->defaultController);
        $methodName     = $this->normalizeMethodName($url[1] ?? $this->defaultMethod);

        // Remaining segments passed as params array (MVC-style)
        $params = array_slice($url, 2);

        // If controller exists, dispatch to it; otherwise fall back to "page" controller for slugs.
        if ($this->controllerFileExists($controllerName)) {
            $this->dispatchController($controllerName, $methodName, $params);
            return;
        }

        $this->dispatchPageFallback($controllerName);
    }

    /**
     * Dispatch to a concrete controller + method.
     *
     * @param string $controllerName Controller class/file name (already normalized).
     * @param string $methodName     Method name (already normalized).
     * @param array<int,string> $params URL parameters.
     * @return void
     */
    private function dispatchController(string $controllerName, string $methodName, array $params): void
    {
        $controllerFile = $this->controllerFilePath($controllerName);

        require_once $controllerFile;

        if (!class_exists($controllerName, false)) {
            throw new RuntimeException('Controller class not found after require: ' . $controllerName);
        }

        // Optional contract enforcement if base controller exists in your stack
        if (class_exists('controller', false) && !is_subclass_of($controllerName, 'controller')) {
            throw new RuntimeException('Controller does not extend base controller: ' . $controllerName);
        }

        $controller = new $controllerName();

        // Hard deny magic/hidden methods.
        if ($this->isDeniedMethod($methodName)) {
            throw new RuntimeException('Denied method requested: ' . $controllerName . '::' . $methodName);
        }

        if (!method_exists($controller, $methodName)) {
            throw new RuntimeException('Method not found: ' . $controllerName . '::' . $methodName);
        }

        // Enforce public methods only + parameter sanity.
        $ref = new ReflectionMethod($controller, $methodName);
        if (!$ref->isPublic()) {
            throw new RuntimeException('Non-public method requested: ' . $controllerName . '::' . $methodName);
        }

        $required = $ref->getNumberOfRequiredParameters();
        $total    = $ref->getNumberOfParameters();

        // Your MVC passes params as an array (common pattern). Support both:
        // A) method(array $params)
        // B) method($p1, $p2, ...)
        if ($total === 1) {
            $controller->{$methodName}($params);
            return;
        }

        // If the method expects discrete args, enforce parameter counts.
        $argc = count($params);
        if ($argc < $required || $argc > $total) {
            throw new RuntimeException(
                'Parameter mismatch for ' . $controllerName . '::' . $methodName .
                ' (given ' . $argc . ', required ' . $required . ', total ' . $total . ')'
            );
        }

        $controller->{$methodName}(...$params);
    }

    /**
     * Dispatch to page fallback controller using the first segment as a slug.
     *
     * @param string $slug Normalized controllerName used as slug.
     * @return void
     */
    private function dispatchPageFallback(string $slug): void
    {
        // Slug must still be safe.
        if (!$this->isValidName($slug)) {
            throw new RuntimeException('Invalid slug for page fallback.');
        }

        $pageController = 'page';
        if (!$this->controllerFileExists($pageController)) {
            throw new RuntimeException('Page controller not available for fallback routing.');
        }

        require_once $this->controllerFilePath($pageController);

        if (!class_exists($pageController, false)) {
            throw new RuntimeException('Page controller class not found after require.');
        }

        $page = new page();

        // Expecting: page::index($slug) OR page::index(array $params)
        if (!method_exists($page, 'index')) {
            throw new RuntimeException('Page controller missing index().');
        }

        $ref = new ReflectionMethod($page, 'index');
        if (!$ref->isPublic()) {
            throw new RuntimeException('Page index() not public.');
        }

        $total = $ref->getNumberOfParameters();
        if ($total === 1) {
            $page->index($slug);
            return;
        }

        // If your page controller expects array params, still support it.
        $page->index([$slug]);
    }

    /**
     * Build controller file path.
     *
     * @param string $controllerName
     * @return string
     */
    private function controllerFilePath(string $controllerName): string
    {
        return APPROOT . $this->controllersDir . '/' . $controllerName . '.php';
    }

    /**
     * Check if controller file exists.
     *
     * @param string $controllerName
     * @return bool
     */
    private function controllerFileExists(string $controllerName): bool
    {
        return is_file($this->controllerFilePath($controllerName));
    }

    /**
     * Normalize and validate controller name.
     *
     * Supports "-" by converting to "_" (historic support).
     *
     * @param string $name
     * @return string
     */
    private function normalizeControllerName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace('-', '_', $name);

        if (!$this->isValidName($name)) {
            throw new RuntimeException('Invalid controller name.');
        }

        return $name;
    }

    /**
     * Normalize and validate method name.
     *
     * @param string $name
     * @return string
     */
    private function normalizeMethodName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace('-', '_', $name);

        if (!$this->isValidName($name)) {
            throw new RuntimeException('Invalid method name.');
        }

        return $name;
    }

    /**
     * Validate controller/method names: only [a-z0-9_]
     *
     * @param string $name
     * @return bool
     */
    private function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9_]+$/', $name);
    }

    /**
     * Deny magic methods and underscore-prefixed methods.
     *
     * @param string $methodName
     * @return bool
     */
    private function isDeniedMethod(string $methodName): bool
    {
        if ($methodName === '') {
            return true;
        }

        if (str_starts_with($methodName, '__')) {
            return true;
        }

        if (str_starts_with($methodName, '_')) {
            return true;
        }

        return false;
    }

    /**
     * Parse URL segments from GET param.
     *
     * @return array<int,string>
     */
    private function get_url(): array
    {
        if (!isset($_GET['url'])) {
            return [];
        }

        $raw = (string) $_GET['url'];
        $raw = trim($raw);
        $raw = rtrim($raw, '/');

        if ($raw === '') {
            return [];
        }

        // Split and normalize each segment.
        $parts = explode('/', $raw);
        $out = [];

        foreach ($parts as $p) {
            $p = rawurldecode($p);
            $p = strtolower(trim($p));

            // Convert hyphens to underscores per historic behavior.
            $p = str_replace('-', '_', $p);

            // Keep only allowed characters; anything else becomes invalid later.
            $out[] = $p;
        }

        return $out;
    }

    /**
     * Emit a controlled 404 response.
     *
     * @return void
     */
    private function respond404(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        // If you have an error controller, use it; otherwise provide a minimal output.
        $errController = 'error_handler';
        if ($this->controllerFileExists($errController)) {
            require_once $this->controllerFilePath($errController);

            if (class_exists($errController, false)) {
                $c = new error_handler();

                if (method_exists($c, 'index')) {
                    try {
                        $ref = new ReflectionMethod($c, 'index');
                        if ($ref->isPublic()) {
                            $c->index(['404']);
                            exit;
                        }
                    } catch (Throwable) {
                        // Fall through to minimal output.
                    }
                }
            }
        }

        echo '404 Not Found';
        exit;
    }

    /**
     * Log exception detail to PHP error log (no extra files, no DB).
     *
     * @param Throwable $e
     * @return void
     */
    private function logException(Throwable $e): void
    {
        error_log('[router] ' . $e->getMessage());
    }
}
