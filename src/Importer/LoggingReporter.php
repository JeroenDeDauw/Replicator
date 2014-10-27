<?php

namespace Queryr\Replicator\Importer;

use Exception;
use Psr\Log\LoggerInterface;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LoggingReporter implements PageImportReporter {

	private $logger;

	private $number = 0;

	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	public function started( EntityPage $entityPage ) {
		$this->logger->info( "Importing entity " . ++$this->number . ': ' . $entityPage->getTitle() . '...' );
	}

	public function endedSuccessfully() {
		$this->logger->info( 'Entity imported' );
	}

	public function endedWithError( Exception $ex ) {
		$this->logger->error( $ex->getMessage() );
	}

	public function stepStarted( $message ) {
		$this->logger->debug( 'Started step: ' . $message );
	}

	public function stepCompleted() {
		$this->logger->debug( 'Step completed' );
	}

}
