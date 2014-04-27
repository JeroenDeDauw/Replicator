<?php

namespace Queryr\Replicator\Commands\Installer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UninstallCommand extends Command {

	protected function configure() {
		$this->setName( 'uninstall' );
		$this->setDescription( 'Uninstalls QueryR Replicator. NOTE: arguments are not escaped' );

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
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$executor = new UninstallCommandExecutor( $input, $output );
		$executor->run();
	}

}
