<?php

namespace Queryr\Replicator\EntitySource\Api;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Http {

	public function get( $url ) {
		return file_get_contents( $url );
	}

}
