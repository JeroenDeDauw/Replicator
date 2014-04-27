<?php

namespace Queryr\Replicator\Importer;

use Queryr\Dump\Reader\Page;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface PageImportReporter {

	public function started( Page $entityPage );

	public function endedSuccessfully();

	public function endedWithError( \Exception $ex );

	public function stepStarted( $message );

	public function stepCompleted();

}