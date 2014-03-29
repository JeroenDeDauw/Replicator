<?php

namespace QueryR\Replicator\Commands\Installer;

use QueryR\Replicator\ServiceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InstallCommand extends Command {

	protected function configure() {
		$this->setName( 'install' );
		$this->setDescription( 'Installs QueryR Replicator. NOTE: arguments are not escaped' );

		$this->addArgument(
			'install-user',
			InputArgument::REQUIRED,
			'Name of a MySQL database user with database and user creation rights'
		);

		$this->addArgument(
			'install-password',
			InputArgument::REQUIRED,
			'Password of the specified MySQL database user'
		);

		$this->addArgument(
			'database',
			InputArgument::REQUIRED,
			'Name of the database to create for the application'
		);

		$this->addArgument(
			'user',
			InputArgument::REQUIRED,
			'MySQL user to create for the application'
		);

		$this->addArgument(
			'password',
			InputArgument::REQUIRED,
			'Password for the new MySQL user'
		);
	}

	private $serviceFactory;

	public function setDependencies( ServiceFactory $serviceFactory ) {
		$this->serviceFactory = $serviceFactory;
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$executor = new InstallCommandExecutor( $input, $output, $this->serviceFactory );
		$executor->run();
	}

}