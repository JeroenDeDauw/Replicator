<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\DumpReader\DumpReader;
use Queryr\DumpReader\ReaderFactory;
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
class DumpImportCommand extends Command {

	protected function configure() {
		$this->setName( 'import:dump' );
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

		$importer = new PagesImporterCli( $input, $output, $this->factory );

		$importer->runImport( $this->getDumpIterator( $input, $output ) );
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

}
