<?php

namespace Queryr\Replicator\Installer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InstallCommand extends Command {

	protected function configure() {
		$this->setName( 'install' );
		$this->setDescription( 'Installs QueryR Replicator using the database details listed in config/db.json' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$executor = new InstallCommandExecutor( $input, $output );
		$executor->run();
	}

}