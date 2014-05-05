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
	use ProgressTrait;

	private $input;
	private $output;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;
	}

	public function run() {
		$this->startRemoval();

		try {
			$config = $this->readConfigFromFile();
			$connection = $this->createConnection();

			$this->dropDatabase( $config, $connection );
			$this->dropDatabaseUser( $config, $connection );

			$this->reportRemovalSuccess();
		}
		catch ( InstallationException $ex ) {
			$this->reportInstallationFailure( $ex->getMessage() );
		}
	}

	private function startRemoval() {
		$this->output->writeln( '<info>Uninstalling QueryR Replicator.</info>' );
	}

	private function readConfigFromFile() {
		$this->writeProgress( 'Reading config file' );

		try {
			$config = ConfigFile::newInstance()->read();
			$this->writeProgressEnd();
			return $config;
		}
		catch ( \RuntimeException $ex ) {
			throw new InstallationException( $ex->getMessage() );
		}
	}

	private function createConnection() {
		$this->writeProgress( 'Establishing MySQL connection' );

		try {
			$connection = DriverManager::getConnection( array(
				'driver' => 'pdo_mysql',
				'host' => 'localhost',
				'user' => $this->input->getArgument( 'install-user' ),
				'password' => $this->input->getArgument( 'install-password' ),
			) );

			$this->writeProgressEnd();

			return $connection;
		}
		catch ( DBALException $ex ) {
			throw new InstallationException( 'Could not establish a MySQL connection', 0, $ex );
		}
	}

	private function dropDatabase( array $config, Connection $connection ) {
		$this->writeProgress( 'Dropping database "' . $config['database'] . '"' );
		$connection->getSchemaManager()->dropDatabase( $config['database'] );
		$this->writeProgressEnd();
	}

	private function dropDatabaseUser( array $config, Connection $connection ) {
		$this->writeProgress( 'Dropping user "' . $config['user'] . '"' );
		$connection->exec( "DROP USER '" . $config['user'] . "'@'localhost';" );
		$this->writeProgressEnd();
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
