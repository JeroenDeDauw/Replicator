<?php

namespace QueryR\Replicator\Commands\Installer;

use PDO;
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
	 * @var PDO
	 */
	private $pdo = null;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;
	}

	public function exec( $sql, $message ) {
		$this->establishConnectionIfNeeded();

		$this->writeProgress( $message );

		$execResult = $this->pdo->exec( $sql );

		if ( $execResult === false ) {
			throw new InstallationException(
				'Error during operation "' . $message . '": ' . print_r( $this->pdo->errorInfo(), true )
			);
		}

		$this->writeProgressEnd();
	}

	private function establishConnectionIfNeeded() {
		if ( $this->pdo === null ) {
			$this->establishConnection();
		}
	}

	private function establishConnection() {
		$this->writeProgress( 'Establishing MySQL connection' );

		try {
			$this->pdo = new PDO(
				'mysql:host=localhost',
				$this->input->getArgument( 'install-user' ),
				$this->input->getArgument( 'install-password' )
			);
		}
		catch ( \PDOException $ex ) {
			throw new InstallationException( 'Could not establish a MySQL connection' );
		}

		$this->writeProgressEnd();
	}

	public function getPDO() {
		$this->establishConnectionIfNeeded();
		return $this->pdo;
	}

}
