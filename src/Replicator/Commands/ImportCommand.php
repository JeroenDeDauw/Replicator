<?php

namespace QueryR\Replicator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DumpReader\Factory;
use Wikibase\DumpReader\Page;

class ImportCommand extends Command {

	protected function configure() {
		$this->setName( 'import' );
		$this->setDescription( 'Imports entities from an XML dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the XML dump'
		);
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$dumpReaderFactory = new Factory();

		$dumpReader = $dumpReaderFactory->newDumpReaderForFile( $input->getArgument( 'file' ) );

		/**
		 * @var Page $entityPage
		 */
		foreach ( $dumpReader->getIterator() as $key => $entityPage ) {
			// TODO
			$output->writeln( 'Importing entity ' . $key . ': ' . $entityPage->getTitle() );
		}
	}

}