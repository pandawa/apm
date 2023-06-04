<?php

declare(strict_types=1);

namespace Pandawa\Apm;

use Illuminate\Foundation\Application;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Contract\ApmInterface;
use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\WatcherInterface;
use Pandawa\Apm\Exception\MissingTransactionException;
use Illuminate\Contracts\Http\Kernel;
use Pandawa\Apm\Middleware\StartRequestTransactionMiddleware;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Agent implements AgentInterface
{
    private ?Transaction $transaction = null;

    /**
     * @var ApmInterface[]
     */
    private array $apms = [];

    public function __construct(
        private bool $enabled,
        private string $defaultApm,
        private array $watchers,
        iterable $apms,
    ) {
        foreach ($apms as $apm) {
            $this->addApm($apm);
        }
    }

    public function addApm(ApmInterface $apm): void
    {
        $this->apms[$apm->name()] = $apm;
    }

    public function hasApm(string $name): bool
    {
        return array_key_exists($name, $this->apms);
    }

    public function hasTransaction(): bool
    {
        return null !== $this->transaction;
    }

    public function setCurrentTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }

    public function getCurrentTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function addSpan(SpanInterface $span): void
    {
        $this->assertHasTransaction();

        $span->setTransaction($this->transaction);

        $this->transaction->addSpan($span);
    }

    public function start(Application $app): bool
    {
        if (true !== $this->enabled) {
            return false;
        }

        $this->startHttpTransaction($app);
        $this->registerWatchers($app);

        return true;
    }

    public function capture(?string $apm = null): void
    {
        $this->assertHasTransaction();

        $this->apms[$apm ?? $this->defaultApm]->capture($this->transaction);
    }

    private function startHttpTransaction(Application $app): void
    {
        if ($app->bound(Kernel::class)) {
            $kernel = $app->make(Kernel::class);
            $kernel->prependMiddleware(StartRequestTransactionMiddleware::class);
        }
    }

    private function registerWatchers(Application $app): void
    {
        foreach ($this->watchers as $key => $watcher) {
            if (is_string($key) && false === $watcher) {
                continue;
            }

            if (is_array($watcher) && true !== ($watcher['enabled'] ?? false)) {
                continue;
            }

            /** @var WatcherInterface $watcher */
            $watcher = $app->make(is_string($key) ? $key : $watcher);
            $watcher->setOptions(is_array($watcher) ? $watcher : []);
            $watcher->register($app);
        }
    }

    private function assertHasTransaction(): void
    {
        if (null === $this->transaction) {
            throw new MissingTransactionException();
        }
    }
}
