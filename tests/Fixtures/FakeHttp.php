<?php

namespace Tests\Queryr\Replicator\Fixtures;

use Queryr\Replicator\EntitySource\Api\Http;

class FakeHttp extends Http {

	public function get( $url ) {
		if ( $url === 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q1&format=json' ) {
			return file_get_contents( __DIR__ . '/../data/api/Q1.json' );
		}
		else {
			throw new \Exception();
		}
	}

}