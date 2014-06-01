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
	use InstallerTrait;

	private $output;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->output = $output;
	}

	public function run() {
		$this->startInstallation();

		try {
			$this->establishDatabaseConnection();
			$this->createDumpStore();
			$this->createTermStore();
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

	private function createDumpStore() {
		$this->tryTask(
			'Creating dump store',
			function() {
				$this->factory->newDumpStoreInstaller()->install();
			}
		);
	}

	private function createTermStore() {
		$this->tryTask(
			'Creating term store',
			function() {
				$this->factory->newTermStoreInstaller()->install();
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
