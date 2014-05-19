<?php

namespace Queryr\Replicator\Installer;

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
		$this->setDescription( 'Uninstalls QueryR Replicator.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$executor = new UninstallCommandExecutor( $input, $output );
		$executor->run();
	}

}
