<?php

namespace QueryR\Replicator;

use QueryR\Replicator\Commands\ImportCommand;
use QueryR\Replicator\Commands\Installer\InstallCommand;
use QueryR\Replicator\Commands\Installer\UninstallCommand;
use QueryR\Replicator\Commands\RunTestsCommand;
use Symfony\Component\Console\Application;

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
	 * @var ServiceFactory
	 */
	private $factory;

	/**
	 * @return Application
	 */
	public function newApplication() {
		$this->app = new Application();
		$this->factory = new ServiceFactory();

		$this->setApplicationInfo();
		$this->registerCommands();

		return $this->app;
	}

	private function setApplicationInfo() {
		$this->app->setName( 'QueryR Replicator' );
		$this->app->setVersion( '0.1 alpha' );
	}

	private function registerCommands() {
		$this->app->add( new RunTestsCommand() );
		$this->app->add( new ImportCommand() );
		$this->app->add( $this->newInstallCommand() );
		$this->app->add( new UninstallCommand() );
	}

	private function newInstallCommand() {
		$install = new InstallCommand();

		$install->setDependencies( $this->factory );

		return $install;
	}



}

