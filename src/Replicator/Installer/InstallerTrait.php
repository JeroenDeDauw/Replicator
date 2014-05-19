<?php

namespace Queryr\Replicator\Installer;

use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\ConfigFile;
use Queryr\Replicator\ServiceFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
trait InstallerTrait {

	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @var ServiceFactory
	 */
	private $factory;

	private function writeProgress( $message ) {
		$this->output->write( "<comment>$message... </comment>" );
	}

	private function writeProgressEnd( $message = 'done.' ) {
		$this->output->writeln( "<comment>$message</comment>" );
	}

	private function writeError( $message ) {
		$this->output->writeln( "<error>$message</error>" );
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

}