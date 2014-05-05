<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\Dump\Reader\ReaderFactory;
use Queryr\Replicator\Importer\ImportStats;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\Importer\StatsTrackingReporter;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
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
		try {
			$serviceFactory = ServiceFactory::newFromConfig();
		}
		catch ( RuntimeException $ex ) {
			$output->writeln( '<error>Could not instantiate the Replicator app</error>' );
			$output->writeln( '<error>' . $ex->getMessage() . '</error>' );
			return;
		}

		$pagesImporter = new PagesImporter(
			$this->newImporter( $serviceFactory, $this->newReporter( $output ) ),
			new ConsoleStatsReporter( $output )
		);

		$pagesImporter->importPages( $this->getDumpIterator( $input ) );
	}

	private function getDumpIterator( InputInterface $input ) {
		return $this->newDumpReader( $input->getArgument( 'file' ) )->getIterator();
	}

	private function newDumpReader( $file ) {
		$dumpReaderFactory = new ReaderFactory();
		return $dumpReaderFactory->newDumpReaderForFile( $file );
	}

	private function newImporter( ServiceFactory $serviceFactory, PageImportReporter $reporter ) {
		return new PageImporter(
			$serviceFactory->newDumpStore(),
			$serviceFactory->newEntityDeserializer(),
			$serviceFactory->newQueryStoreWriter(),
			$reporter
		);
	}

	private function newReporter( OutputInterface $output ) {
		return $output->isVerbose() ? new VerboseReporter( $output ) : new SimpleReporter( $output );
	}


}
