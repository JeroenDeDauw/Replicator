<?php

namespace Queryr\Replicator\EntitySource\Api;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Http {

	/**
	 * @param string $url
	 * @return string|false
	 */
	public function get( string $url ) {
		return file_get_contents( $url );
	}

}
