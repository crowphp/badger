<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Badger\Commands\CoverageBadge;

$application = new Application();

// ... register commands
$application->add(new CoverageBadge());

$application->run();