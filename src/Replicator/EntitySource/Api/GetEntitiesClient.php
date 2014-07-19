<?php

namespace Queryr\Replicator\EntitySource\Api;

use Queryr\Replicator\EntitySource\EntityPageBatchFetcher;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GetEntitiesClient implements EntityPageBatchFetcher {

	private $http;

	/**
	 * @var GetEntitiesInterpreter
	 */
	private $responseInterpreter;

	public function __construct( Http $http ) {
		$this->http = $http;
		$this->responseInterpreter = new GetEntitiesInterpreter();
	}

	/**
	 * @param string[] $entityIds
	 * @return EntityPage[]
	 */
	public function fetchEntityPages( array $entityIds ) {
		if ( empty( $entityIds ) ) {
			return [];
		}

		$response = $this->makeRequest( $this->constructRequestUrl( $entityIds ) );
		return $this->getEntityPagesFromResponse( $response );
	}

	/**
	 * @param string[] $entityIds
	 * @return string
	 */
	private function constructRequestUrl( array $entityIds ) {
		$ids = implode( '|', array_map( 'urlencode', $entityIds ) );

		return 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=' . $ids . '&format=json';
	}

	private function makeRequest( $url ) {
		return $this->http->get( $url );
	}

	/**
	 * @param string $response
	 * @return EntityPage[]
	 */
	private function getEntityPagesFromResponse( $response ) {
		return $this->responseInterpreter->getEntityPagesFromResult( $response );
	}

}