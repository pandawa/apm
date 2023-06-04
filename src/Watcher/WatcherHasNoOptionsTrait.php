<?php

declare(strict_types=1);

namespace Pandawa\Apm\Watcher;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait WatcherHasNoOptionsTrait
{
    public function setOptions(array $options = []): void
    {
    }
}
