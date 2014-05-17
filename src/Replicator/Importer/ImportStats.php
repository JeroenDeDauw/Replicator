<?php

namespace Queryr\Replicator\Importer;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportStats {

	private $count = 0;
	private $errorCount = 0;
	private $errorMessages = [];
	private $durationInMs;

	public function recordSuccess() {
		$this->count++;
	}

	public function recordError( \Exception $ex ) {
		$this->count++;
		$this->errorCount++;
		$this->recordErrorMessage( $ex->getMessage() );
	}

	public function setDuration( $durationInMs ) {
		$this->durationInMs = $durationInMs;
	}

	private function recordErrorMessage( $message ) {
		$message = $this->getNiceMessage( $message );

		if ( array_key_exists( $message, $this->errorMessages ) ) {
			$this->errorMessages[$message]++;
		}
		else {
			$this->errorMessages[$message] = 1;
		}
	}

	private function getNiceMessage( $message ) {
		if ( strpos( $message, 'Duplicate entry' ) === 0 ) {
			return 'Duplicate entry';
		}

		return $message;
	}

	public function getEntityCount() {
		return $this->count;
	}

	public function getErrorCount() {
		return $this->errorCount;
	}

	public function getErrorMessages() {
		arsort( $this->errorMessages, SORT_NUMERIC );
		return $this->errorMessages;
	}

	public function getSuccessCount() {
		return $this->count - $this->errorCount;
	}

	public function getErrorRatio() {
		if ( $this->count === 0 ) {
			return 0;
		}

		return $this->errorCount / $this->count * 100;
	}

	public function getDurationInMs() {
		return $this->durationInMs;
	}

}