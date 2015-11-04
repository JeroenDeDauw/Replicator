<?php

namespace Queryr\Replicator\Cli;

use Queryr\Replicator\Cli\Command\ApiImportCommand;
use Queryr\Replicator\Cli\Command\InstallCommand;
use Queryr\Replicator\Cli\Command\JsonDumpImportCommand;
use Queryr\Replicator\Cli\Command\RunTestsCommand;
use Queryr\Replicator\Cli\Command\UninstallCommand;
use Queryr\Replicator\Cli\Command\XmlDumpImportCommand;
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

		$this->app->add( new XmlDumpImportCommand() );
		$this->app->add( new ApiImportCommand() );
		$this->app->add( new JsonDumpImportCommand() );
	}

	public function run() {
		$this->newApplication()->run();
	}

}
