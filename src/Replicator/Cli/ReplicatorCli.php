<?php

namespace Queryr\Replicator\Cli;

use Queryr\Replicator\Cli\Command\ApiImportCommand;
use Queryr\Replicator\Cli\Command\DumpImportCommand;
use Queryr\Replicator\Cli\Command\InstallCommand;
use Queryr\Replicator\Cli\Command\RunTestsCommand;
use Queryr\Replicator\Cli\Command\UninstallCommand;
use Symfony\Component\Console\Application;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReplicatorCli {

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
