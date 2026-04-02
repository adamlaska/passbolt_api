#!/usr/bin/php -q
<?php
// =================================================================
// ⚠ WARNING
// This file is patched while building official packages.
// If you do any changes make sure to notify the SRE/packaging team.
// =================================================================
// Check platform requirements
require dirname(__DIR__) . '/config/requirements.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use Cake\Console\CommandRunner;

// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
exit($runner->run($argv));
