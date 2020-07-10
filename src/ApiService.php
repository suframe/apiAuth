<?php


namespace suframe\apiAuth;


use DirectoryIterator;
use think\facade\Config;
use think\Route;
use think\Service;

class ApiService extends Service
{

    protected $enable = false;

    public function register()
    {
        $config = include(__DIR__ . '/config/apiAuth.php');
        $config = config('apiAuth') + $config;
        Config::set($config, 'apiAuth');
        $this->enable = config('apiAuth.enable', false);
        if (!$this->enable) {
            return false;
        }
        $this->initApiAuth();
        $this->createMigrations();
    }

    /**
     * @param Route $route
     * @return bool|void
     */
    public function boot(Route $route)
    {
        if (!$this->enable) {
            return false;
        }
        $this->setRouter($route);
    }

    /**
     * @param Route $route
     */
    protected function setRouter($route)
    {
        return $route->group('apiAuth', function () use ($route) {
            $controllers = config(
                'thinkAdmin.controllers',
                ['login', 'phoneCode']
            );
            foreach ($controllers as $controller) {
                $controllerUc = ucfirst($controller);
                $route->any("{$controller}/:action", "\\suframe\\apiAuth\\controller\\{$controllerUc}@:action");
            }
        });
    }

    protected function initApiAuth()
    {
        if ($this->app->runningInConsole()) {
            return false;
        }
        $this->app->bind('apiAuth', Auth::class);
    }

    /**
     * 数据库迁移
     * @return bool
     */
    protected function createMigrations()
    {
        if (!$this->app->runningInConsole()) {
            return false;
        }
        $dataPath = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        if (!is_dir($dataPath)) {
            mkdir($dataPath, 0755, true);
        }
        $sqlDir = __DIR__ . '/database/migrations';
        foreach (new DirectoryIterator($sqlDir) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $target = $dataPath . $fileInfo->getFilename();
            if (!file_exists($target)) {
                copy($fileInfo->getRealPath(), $target);
            }
        }
    }

}