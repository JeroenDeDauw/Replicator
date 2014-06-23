<?php

namespace Queryr\Replicator;
use Queryr\Replicator\Importer\Console\ApiImportCommand;
use Queryr\Replicator\Importer\Console\DumpImportCommand;
use Queryr\Replicator\Installer\InstallCommand;
use Queryr\Replicator\Installer\UninstallCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Shell;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Replicator {

	/**
	 * @var Application
	 */
	private $app;

	/**
	 * @return Application
	 */
	public function newApplication() {
		$this->app = new Application();

		$this->setApplicationInfo();
		$this->registerCommands();

		return $this->app;
	}

	private function setApplicationInfo() {
		$this->app->setName( 'QueryR Replicator' );
		$this->app->setVersion( '0.1 alpha' );
	}

	private function registerCommands() {
		$this->app->add( new InstallCommand() );
		$this->app->add( new UninstallCommand() );

		$this->app->add( new RunTestsCommand() );

		$this->app->add( new DumpImportCommand() );
		$this->app->add( new ApiImportCommand() );
	}

	public function run() {
		$this->newApplication()->run();
	}

}
