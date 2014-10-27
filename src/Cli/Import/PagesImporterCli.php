<?php

namespace Queryr\Replicator\Cli\Import;

use Psr\Log\NullLogger;
use Queryr\Replicator\Importer\CompositeReporter;
use Queryr\Replicator\Importer\LoggingReporter;
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

	/**
	 * @var callable|null
	 */
	private $onAborted;

	public function __construct( InputInterface $input, OutputInterface $output, ServiceFactory $factory, callable $onAborted = null ) {
		$this->input = $input;
		$this->output = $output;
		$this->factory = $factory;
		$this->onAborted = $onAborted;
	}

	public function runImport( \Iterator $entityPageIterator ) {
		$pagesImporter = new PagesImporter(
			$this->newImporter( $this->newReporter() ),
			new ConsoleStatsReporter( $this->output ),
			$this->onAborted
		);

		pcntl_signal( SIGINT, [ $pagesImporter, 'stop' ] );
		pcntl_signal( SIGTERM, [ $pagesImporter, 'stop' ] );

		$pagesImporter->importPages( $entityPageIterator );
	}

	private function newImporter( PageImportReporter $reporter ) {
		// TODO: move into factory
		return new PageImporter(
			$this->factory->newEntityStore(),
			$this->factory->newLegacyEntityDeserializer(),
			$this->factory->newQueryStoreWriter(),
			$reporter,
			$this->factory->newTermStore()
		);
	}

	private function newReporter() {
		$cliReporter = $this->output->isVerbose() ?
			new VerboseReporter( $this->output, $this->output->isVeryVerbose() )
			: new SimpleReporter( $this->output );

		$loggingReporter = new LoggingReporter( new NullLogger() );

		return new CompositeReporter( $loggingReporter, $cliReporter );
	}


}
