<?php

namespace Queryr\Replicator\Importer;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface StatsReporter {

	public function reportStats( ImportStats $stats );

}