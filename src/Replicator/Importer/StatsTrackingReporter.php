<?php

namespace Queryr\Replicator\Importer;

use Exception;
use Queryr\DumpReader\Page;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StatsTrackingReporter implements PageImportReporter {

	private $reporter;

	/**
	 * @var ImportStats
	 */
	private $stats;

	public function __construct( PageImportReporter $reporter ) {
		$this->reporter = $reporter;
		$this->stats = new ImportStats();
	}

	public function started( Page $entityPage ) {
		$this->reporter->started( $entityPage );
	}

	public function endedSuccessfully() {
		$this->reporter->endedSuccessfully();
		$this->stats->recordSuccess();
	}

	public function endedWithError( Exception $ex ) {
		$this->reporter->endedWithError( $ex );
		$this->stats->recordError( $ex );
	}

	public function stepStarted( $message ) {
		$this->reporter->stepStarted( $message );
	}

	public function stepCompleted() {
		$this->reporter->stepCompleted();
	}

	public function getStats() {
		return $this->stats;
	}

}