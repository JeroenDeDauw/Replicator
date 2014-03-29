<?php

namespace QueryR\Replicator;

use QueryR\Replicator\Commands\ImportCommand;
use QueryR\Replicator\Commands\Installer\InstallCommand;
use QueryR\Replicator\Commands\RunTestsCommand;
use QueryR\Replicator\Commands\Installer\UninstallCommand;
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
		$app->setVersion( '0.1 alpha' );

		$this->registerCommands( $app );

		return $app;
	}

	private function registerCommands( Application $app ) {
		$app->add( new RunTestsCommand() );
		$app->add( new ImportCommand() );
		$app->add( new InstallCommand() );
		$app->add( new UninstallCommand() );
	}

}