<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\Dump\Reader\DumpReader;
use Queryr\Dump\Reader\ReaderFactory;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

		$this->addOption(
			'continue',
			'c',
			InputOption::VALUE_OPTIONAL,
			'The title to resume from (title not included)'
		);
	}

	/**
	 * @var ServiceFactory|null
	 */
	private $factory = null;

	public function setServiceFactory( ServiceFactory $factory ) {
		$this->factory = $factory;
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->initServiceFactory( $output );

		$pagesImporter = new PagesImporter(
			$this->newImporter( $this->newReporter( $output ) ),
			new ConsoleStatsReporter( $output )
		);

		$iterator = $this->getDumpIterator( $input, $output );

		pcntl_signal( SIGINT, [ $pagesImporter, 'stop' ] );
		pcntl_signal( SIGTERM, [ $pagesImporter, 'stop' ] );

		$pagesImporter->importPages( $iterator );
	}

	private function initServiceFactory( OutputInterface $output ) {
		if ( $this->factory === null ) {
			try {
				$this->factory = ServiceFactory::newFromConfig();
			}
			catch ( RuntimeException $ex ) {
				$output->writeln( '<error>Could not instantiate the Replicator app</error>' );
				$output->writeln( '<error>' . $ex->getMessage() . '</error>' );
				return;
			}
		}
	}

	private function getDumpIterator( InputInterface $input, OutputInterface $output ) {
		$reader = $this->newDumpReader( $input->getArgument( 'file' ) );

		$this->handleContinueArgument( $input, $output, $reader );

		return $reader->getIterator();
	}

	private function handleContinueArgument( InputInterface $input, OutputInterface $output, DumpReader $reader ) {
		$continueTitle = $input->getOption( 'continue' );

		if ( $continueTitle !== null ) {
			$output->write( "<info>Seeking to title </info><comment>$continueTitle</comment><info>... </info>" );
			$reader->seekToTitle( $continueTitle );
			$output->writeln( "<info>done</info>" );
		}
	}

	private function newDumpReader( $file ) {
		$dumpReaderFactory = new ReaderFactory();
		return $dumpReaderFactory->newDumpReaderForFile( $file );
	}

	private function newImporter( PageImportReporter $reporter ) {
		return new PageImporter(
			$this->factory->newDumpStore(),
			$this->factory->newEntityDeserializer(),
			$this->factory->newQueryStoreWriter(),
			$reporter,
			$this->factory->newTermStore()
		);
	}

	private function newReporter( OutputInterface $output ) {
		return $output->isVerbose() ? new VerboseReporter( $output ) : new SimpleReporter( $output );
	}


}
