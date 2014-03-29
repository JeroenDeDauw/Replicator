<?php

namespace QueryR\Replicator\Commands\Installer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UninstallCommandExecutor {
	use ProgressTrait;

	private $input;
	private $output;

	/**
	 * @var SqlExecutor
	 */
	private $sqlExecutor;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;
	}

	public function run() {
		$this->startRemoval();

		$this->sqlExecutor = new SqlExecutor( $this->input, $this->output );

		try {
			$this->dropDatabase();
			$this->reportRemovalSuccess();
		}
		catch ( InstallationException $ex ) {
			$this->reportInstallationFailure( $ex->getMessage() );
		}
	}

	private function readConfigFromFile() {
		$configJson = @file_get_contents( __DIR__ . '/../../replicator.json' );

		if ( $configJson === false ) {
			throw new InstallationException( 'Could not read the config file' );
		}

		return json_decode( $configJson );
	}

	private function dropDatabase() {
		$config = $this->readConfigFromFile();

		$this->sqlExecutor->exec(
			"DROP DATABASE $config->database;",
			'Dropping database "' . $config->database . '"'
		);

		$this->sqlExecutor->exec(
			"DROP USER '$config->user'@'localhost';",
			'Dropping user "' . $config->user . '"'
		);
	}

	private function startRemoval() {
		$this->output->writeln( '<info>Uninstalling QueryR Replicator.</info>' );
	}

	private function reportInstallationFailure( $failureMessage ) {
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
