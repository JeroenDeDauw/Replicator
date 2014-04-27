<?php

namespace Queryr\Replicator\Commands\Installer;

use Queryr\Replicator\Commands\ProgressTrait;
use Queryr\Replicator\ConfigFile;
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
		try {
			return ConfigFile::newInstance()->read();
		}
		catch ( \RuntimeException $ex ) {
			throw new InstallationException( $ex->getMessage() );
		}
	}

	private function dropDatabase() {
		$config = $this->readConfigFromFile();

		$dbName = $config['database'];
		$user = $config['user'];

		$this->sqlExecutor->exec(
			"DROP DATABASE $dbName;",
			'Dropping database "' . $dbName . '"'
		);

		$this->sqlExecutor->exec(
			"DROP USER '$user'@'localhost';",
			'Dropping user "' . $user . '"'
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
