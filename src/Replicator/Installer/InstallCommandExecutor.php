<?php

namespace Queryr\Replicator\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\ConfigFile;
use Queryr\Replicator\ServiceFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InstallCommandExecutor {
	use ProgressTrait;

	private $output;

	/**
	 * @var ServiceFactory
	 */
	private $factory;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->output = $output;
	}

	public function run() {
		$this->startInstallation();

		try {
			$this->establishDatabaseConnection();
			$this->createDumpStore();
			$this->createQueryEngine();

			$this->reportInstallationSuccess();
		}
		catch ( InstallationException $ex ) {
			$this->reportInstallationFailure( $ex->getMessage() );
		}
	}

	private function startInstallation() {
		$this->output->writeln( '<info>Installing QueryR Replicator.</info>' );
	}

	private function establishDatabaseConnection() {
		$config = $this->tryTask(
			'Reading database configuration file (config/db.json)',
			function() {
				return ConfigFile::newInstance()->read();
			}
		);

		$connection = $this->tryTask(
			'Establishing database connection',
			function() use ( $config ) {
				return DriverManager::getConnection( $config );
			}
		);

		$this->factory = ServiceFactory::newFromConnection( $connection );
	}

	private function createDumpStore() {
		$this->tryTask(
			'Creating dump store',
			function() {
				$this->factory->newDumpStoreInstaller()->install();
			}
		);
	}

	private function createQueryEngine() {
		$this->tryTask(
			'Creating query engine',
			function() {
				$this->factory->newQueryEngineInstaller()->install();
			}
		);
	}

	private function tryTask( $message, $task ) {
		$this->writeProgress( $message );

		try {
			$returnValue = call_user_func( $task );
		}
		catch ( \Exception $ex ) {
			throw new InstallationException( $ex->getMessage(), 0, $ex );
		}

		$this->writeProgressEnd();
		return $returnValue;
	}

	private function reportInstallationFailure( $failureMessage ) {
		$this->writeProgressEnd( 'failed!' );
		$this->writeError( 'Error! Installation of QueryR Replicator failed.' );
		$this->writeError( 'Reason: ' . $failureMessage );
	}

	private function reportInstallationSuccess() {
		$tag = 'bg=green;fg=black';

		$this->output->writeln(
			"<$tag>Installation of QueryR Replicator has completed successfully.</$tag>"
		);
	}

}
