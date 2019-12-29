<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Rest;

use Closure;

use function Shlinkio\Shlink\Common\loadConfigFromGlob;
use function sprintf;

class ConfigProvider
{
    private const ROUTES_PREFIX = '/rest/v{version:1|2}';

    private Closure $loadConfig;

    public function __construct(?callable $loadConfig = null)
    {
        $this->loadConfig = Closure::fromCallable($loadConfig ?? fn (string $glob) => loadConfigFromGlob($glob));
    }

    public function __invoke()
    {
        $config = ($this->loadConfig)(__DIR__ . '/../config/{,*.}config.php');
        return $this->applyRoutesPrefix($config);
    }

    private function applyRoutesPrefix(array $config): array
    {
        $routes =& $config['routes'] ?? [];

        // Prepend the routes prefix to every path
        foreach ($routes as $key => $route) {
            ['path' => $path] = $route;
            $routes[$key]['path'] = sprintf('%s%s', self::ROUTES_PREFIX, $path);
        }

        return $config;
    }
}
