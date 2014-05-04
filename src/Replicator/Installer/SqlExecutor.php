<?php

namespace Queryr\Replicator\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SqlExecutor {
	use ProgressTrait;

	private $input;
	private $output;

	/**
	 * @var Connection|null
	 */
	private $connection = null;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;
	}

	public function exec( $sql, $message ) {
		$this->establishConnectionIfNeeded();

		$this->writeProgress( $message );

		$execResult = $this->connection->exec( $sql );

		if ( $execResult === false ) {
			throw new InstallationException(
				'Error during operation "' . $message . '": ' . print_r( $this->connection->errorInfo(), true )
			);
		}

		$this->writeProgressEnd();
	}

	private function establishConnectionIfNeeded() {
		if ( $this->connection === null ) {
			$this->establishConnection();
		}
	}

	private function establishConnection() {
		$this->writeProgress( 'Establishing MySQL connection' );

		try {
			$this->connection = DriverManager::getConnection( array(
				'driver' => 'pdo_mysql',
				'host' => 'localhost',
				'user' => $this->input->getArgument( 'install-user' ),
				'password' => $this->input->getArgument( 'install-password' ),
				'dbname' => $this->input->getArgument( 'database' ),
			) );
		}
		catch ( DBALException $ex ) {
			throw new InstallationException( 'Could not establish a MySQL connection', 0, $ex );
		}

		$this->writeProgressEnd();
	}

	public function getConnection() {
		$this->establishConnectionIfNeeded();
		return $this->connection;
	}

}
