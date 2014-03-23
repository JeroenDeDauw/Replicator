<?php

namespace Queryr\Replicator;

use Queryr\Replicator\Commands\RunTestsCommand;
use Symfony\Component\Console\Application;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Replicator {

	public function newApplication() {
		$app = new Application();

		$app->add( new RunTestsCommand() );

		return $app;
	}

}