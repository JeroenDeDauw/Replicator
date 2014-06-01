<?php

namespace Queryr\Replicator\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\ConfigFile;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UninstallCommandExecutor {
	use InstallerTrait;

	private $output;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->output = $output;
	}

	public function run() {
		$this->startRemoval();

		try {
			$this->establishDatabaseConnection();

			$this->removeDumpStore();
			$this->removeTermStore();
			$this->removeQueryEngine();

			$this->reportRemovalSuccess();
		}
		catch ( InstallationException $ex ) {
			$this->reportUninstallationFailure( $ex->getMessage() );
		}
	}

	private function removeDumpStore() {
		$this->tryTask(
			'Removing dump store',
			function() {
				$this->factory->newDumpStoreInstaller()->uninstall();
			}
		);
	}

	private function removeTermStore() {
		$this->tryTask(
			'Removing term store',
			function() {
				$this->factory->newTermStoreInstaller()->uninstall();
			}
		);
	}

	private function removeQueryEngine() {
		$this->tryTask(
			'Removing query engine',
			function() {
				$this->factory->newQueryEngineUninstaller()->uninstall();
			}
		);
	}

	private function startRemoval() {
		$this->output->writeln( '<info>Uninstalling QueryR Replicator.</info>' );
	}

	private function reportUninstallationFailure( $failureMessage ) {
		$this->writeProgressEnd( 'failed!' );
		$this->writeError( 'Error! Removal of QueryR Replicator failed.' );
		$this->writeError( 'Reason: ' . $failureMessage );
	}

	private function reportRemovalSuccess() {
		$tag = 'bg=green;fg=black';

		$this->output->writeln(
			"<$tag>Removal of QueryR Replicator has completed successfully.</$tag>"
		);
	}

}
