<?php

namespace Queryr\Replicator\Importer;

use Exception;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CompositeReporter implements PageImportReporter {

	private $firstReporter;
	private $secondReporter;

	public function __construct( PageImportReporter $firstReporter, PageImportReporter $secondReporter ) {
		$this->firstReporter = $firstReporter;
		$this->secondReporter = $secondReporter;
	}

	public function started( EntityPage $entityPage ) {
		$this->firstReporter->started( $entityPage );
		$this->secondReporter->started( $entityPage );
	}

	public function endedSuccessfully() {
		$this->firstReporter->endedSuccessfully();
		$this->secondReporter->endedSuccessfully();
	}

	public function endedWithError( Exception $ex ) {
		$this->firstReporter->endedWithError( $ex );
		$this->secondReporter->endedWithError( $ex );
	}

	public function stepStarted( $message ) {
		$this->firstReporter->stepStarted( $message );
		$this->secondReporter->stepStarted( $message );
	}

	public function stepCompleted() {
		$this->firstReporter->stepCompleted();
		$this->secondReporter->stepCompleted();
	}

}
