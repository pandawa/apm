<?php

declare(strict_types=1);

namespace Pandawa\Apm\Contract;

use Illuminate\Foundation\Application;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface WatcherInterface
{
    public function setOptions(array $options = []): void;

    public function register(Application $app): void;
}
