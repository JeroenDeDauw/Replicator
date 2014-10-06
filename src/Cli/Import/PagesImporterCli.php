<?php

namespace Queryr\Replicator\Cli\Import;

use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\ServiceFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PagesImporterCli {

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
	private $factory = null;

	public function __construct( InputInterface $input, OutputInterface $output, ServiceFactory $factory ) {
		$this->input = $input;
		$this->output = $output;
		$this->factory = $factory;
	}

	public function runImport( \Iterator $entityPageIterator ) {
		$pagesImporter = new PagesImporter(
			$this->newImporter( $this->newReporter() ),
			new ConsoleStatsReporter( $this->output )
		);

		pcntl_signal( SIGINT, [ $pagesImporter, 'stop' ] );
		pcntl_signal( SIGTERM, [ $pagesImporter, 'stop' ] );

		$pagesImporter->importPages( $entityPageIterator );
	}

	private function newImporter( PageImportReporter $reporter ) {
		return new PageImporter(
			$this->factory->newEntityStore(),
			$this->factory->newLegacyEntityDeserializer(),
			$this->factory->newQueryStoreWriter(),
			$reporter,
			$this->factory->newTermStore()
		);
	}

	private function newReporter() {
		return $this->output->isVerbose() ? new VerboseReporter( $this->output ) : new SimpleReporter( $this->output );
	}


}
