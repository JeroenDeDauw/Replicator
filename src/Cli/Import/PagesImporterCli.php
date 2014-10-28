<?php

namespace Queryr\Replicator\Cli\Import;

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
		$pagesImporter = $this->factory->newPagesImporter(
			$this->newReporter(),
			new ConsoleStatsReporter( $this->output ),
			$this->onAborted
		);

		pcntl_signal( SIGINT, [ $pagesImporter, 'stop' ] );
		pcntl_signal( SIGTERM, [ $pagesImporter, 'stop' ] );

		$pagesImporter->importPages( $entityPageIterator );
	}

	private function newReporter() {
		return $this->output->isVerbose() ?
			new VerboseReporter( $this->output, $this->output->isVeryVerbose() )
			: new SimpleReporter( $this->output );
	}


}
