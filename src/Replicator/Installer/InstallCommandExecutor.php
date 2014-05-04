<?php

namespace Queryr\Replicator\Installer;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\ConfigFile;
use Queryr\Replicator\ServiceFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\QueryEngine\QueryEngineException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InstallCommandExecutor {
	use ProgressTrait;

	private $input;
	private $output;

	/**
	 * @var ServiceFactory
	 */
	private $factory;

	/**
	 * @var SqlExecutor
	 */
	private $sqlExecutor;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;
	}

	public function run() {
		$this->startInstallation();

		$this->sqlExecutor = new SqlExecutor( $this->input, $this->output );

		try {
			$this->factory = ServiceFactory::newForInstaller(
				$this->sqlExecutor->getConnection(),
				$this->input->getArgument( 'database' )
			);

			$this->createConfigFile();
			$this->createDatabase();
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

	private function createConfigFile() {
		$this->writeProgress( 'Creating config file' );

		try {
			ConfigFile::newInstance()->write( $this->createConfigData() );
		}
		catch ( \RuntimeException $ex ) {
			throw new InstallationException( $ex->getMessage() );
		}

		$this->writeProgressEnd();
	}

	private function createConfigData() {
		return array(
			'user' => $this->input->getArgument( 'user' ),
			'password' => $this->input->getArgument( 'password' ),
			'database' => $this->input->getArgument( 'database' ),
		);
	}

	private function createDatabase() {
		$database = $this->input->getArgument( 'database' );
		$user = $this->input->getArgument( 'user' );
		$password = $this->input->getArgument( 'password' );

		$connection = DriverManager::getConnection( array(
			'driver' => 'pdo_mysql',
			'host' => 'localhost',
			'user' => $this->input->getArgument( 'install-user' ),
			'password' => $this->input->getArgument( 'install-password' ),
		) );

		$this->writeProgress( 'Creating database' );
		$connection->getSchemaManager()->createDatabase( $database );
		$connection->close();
		$this->writeProgressEnd();

		$this->sqlExecutor->exec(
			"CREATE USER '$user'@'localhost' IDENTIFIED BY '$password';",
			'Creating database user'
		);

		$this->sqlExecutor->exec(
			"GRANT ALL PRIVILEGES ON $database.* TO '$user'@'localhost';",
			'Assigning rights to database user'
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

	private function createDumpStore() {
		$this->writeProgress( 'Creating dump store' );

		$installer = $this->factory->newDumpStoreInstaller(
			$this->sqlExecutor->getConnection(),
			$this->input->getArgument( 'database' )
		);

		try {
			$installer->install();
		}
		// FIXME
		catch ( DBALException $ex ) {
			throw new InstallationException( $ex->getMessage(), 0, $ex );
		}

		$this->writeProgressEnd();
	}

	private function createQueryEngine() {
		$this->writeProgress( 'Creating query engine' );

		$installer = $this->factory->newQueryEngineInstaller(
			$this->sqlExecutor->getConnection(),
			$this->input->getArgument( 'database' )
		);

		// TODO: report once QE supports detailed reporting
		try {
			$installer->install();
		}
		catch ( QueryEngineException $ex ) {
			throw new InstallationException( $ex->getMessage(), 0, $ex );
		}

		$this->writeProgressEnd();
	}

}
