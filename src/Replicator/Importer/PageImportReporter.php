<?php

namespace Queryr\Replicator\Importer;

use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface PageImportReporter {

	public function started( EntityPage $entityPage );

	public function endedSuccessfully();

	public function endedWithError( \Exception $ex );

	public function stepStarted( $message );

	public function stepCompleted();

}