<?php

namespace Queryr\Replicator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RunTestsCommand extends Command {

	protected function configure() {
		$this->setName( 'test' );
		$this->setDescription( 'Run the PHPUnit tests' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$pwd = getcwd();
		chdir( __DIR__ . '/../../..' );
		passthru( 'phpunit' );
		chdir( $pwd );
	}

}