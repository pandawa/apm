<?php

declare(strict_types=1);

namespace Pandawa\Apm;

use Illuminate\Foundation\Application;
use Pandawa\Apm\Apm\Elastic\ElasticApmAdapter;
use Pandawa\Apm\Apm\Log\LogApmAdapter;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Component\Module\AbstractModule;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ApmModule extends AbstractModule
{
    public function build(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/apm.php' => config_path('apm.php'),
            ], 'apm');
        }

        $this->app->booted(function ($app) {
            $app[AgentInterface::class]->start($app);
        });
    }

    public function init(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/apm.php', 'apm');

        $this->registerApms();

        $this->app->singleton(AgentInterface::class, function (Application $app) {
            $config = $app['config']['apm'];

            return new Agent(
                $config['enabled'],
                $config['default'],
                $config['watchers'],
                $app->tagged('apms'),
            );
        });
    }

    private function registerApms(): void
    {
        $this->app->bind(ElasticApmAdapter::class);
        $this->app->tag(ElasticApmAdapter::class, ['apms']);

        $this->app->bind(LogApmAdapter::class);
        $this->app->tag(LogApmAdapter::class, ['apms']);
    }
}
