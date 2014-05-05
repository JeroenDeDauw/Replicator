<?php

namespace Queryr\Replicator\Importer;

use Iterator;

class PagesImporter {

	private $importer;
	private $statsReporter;

	public function __construct( PageImporter $importer, StatsReporter $statsReporter ) {
		$this->importer = $importer;
		$this->statsReporter = $statsReporter;
	}

	public function importPages( Iterator $entityPageIterator ) {
		$reporter = new StatsTrackingReporter( $this->importer->getReporter() );

		$this->importer->setReporter( $reporter );

		foreach ( $entityPageIterator as $entityPage ) {
			$this->importer->import( $entityPage );
		}

		$this->statsReporter->reportStats( $reporter->getStats() );
	}



}

