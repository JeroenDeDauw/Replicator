<?php

namespace QueryR\Replicator;

use QueryR\Replicator\Commands\ImportCommand;
use QueryR\Replicator\Commands\RunTestsCommand;
use Symfony\Component\Console\Application;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Replicator {

	/**
	 * @return Application
	 */
	public function newApplication() {
		$app = new Application();

		$app->setName( 'QueryR Replicator' );
		$app->setVersion( '1.0.0 alpha' );

		$this->registerCommands( $app );

		return $app;
	}

	private function registerCommands( Application $app ) {
		$app->add( new RunTestsCommand() );
		$app->add( new ImportCommand() );
		//$app->add( new ReplicateCommand() );
	}

}