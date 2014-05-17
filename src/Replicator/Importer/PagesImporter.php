<?php

namespace Queryr\Replicator\Importer;

use Iterator;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PagesImporter {

	private $importer;
	private $statsReporter;

	private $shouldStop = false;

	public function __construct( PageImporter $importer, StatsReporter $statsReporter ) {
		$this->importer = $importer;
		$this->statsReporter = $statsReporter;
	}

	public function importPages( Iterator $entityPageIterator ) {
		$reporter = new StatsTrackingReporter( $this->importer->getReporter() );

		$this->importer->setReporter( $reporter );

		$this->runImportLoop( $entityPageIterator );

		$this->statsReporter->reportStats( $reporter->getStats() );
	}

	private function runImportLoop( Iterator $entityPageIterator ) {
		$this->shouldStop = false;

		foreach ( $entityPageIterator as $entityPage ) {
			$this->importer->import( $entityPage );

			pcntl_signal_dispatch();
			if ( $this->shouldStop ) {
				$this->statsReporter->reportAbortion();
				return;
			}
		}
	}

	public function stop() {
		$this->shouldStop = true;
	}

}

