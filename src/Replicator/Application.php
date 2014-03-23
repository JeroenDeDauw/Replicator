<?php

namespace Queryr\Replicator;

use Queryr\Replicator\Commands\RunTestsCommand;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Application extends \Symfony\Component\Console\Application {

	protected function getDefaultCommands() {
		$commands = parent::getDefaultCommands();

		$commands[] = new RunTestsCommand();

		return $commands;
	}

}