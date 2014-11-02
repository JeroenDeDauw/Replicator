<?php

namespace Queryr\Replicator\EntitySource\Api;

use Queryr\Replicator\Model\EntityPage;

/**
 * Interpreter for output created by the Wikibase wbgetentities web API module.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GetEntitiesInterpreter {

	/**
	 * @param string $resultJson
	 *
	 * @return EntityPage[]
	 */
	public function getEntityPagesFromResult( $resultJson ) {
		$resultData = json_decode( $resultJson, true );

		if ( !is_array( $resultData ) ) {
			return [];
		}

		if ( array_key_exists( 'entities', $resultData ) ) {
			return $this->constructEntityPages( $resultData['entities'] );
		}

		return [];
	}

	private function constructEntityPages( array $entityPagesData ) {
		$entityPages = [];

		foreach ( $entityPagesData as $key => $entityPageData ) {
			$isActualEntity = array_key_exists( 'id', $entityPageData )
				&& $entityPageData['id'] === $key
				&& !array_key_exists( 'missing', $entityPageData );

			if ( $isActualEntity ) {
				$entityPages[] = $this->constructEntityPage( $entityPageData );
			}
		}

		return $entityPages;
	}

	private function constructEntityPage( array $entityPageData ) {
		return new EntityPage(
			json_encode( $entityPageData ),
			$entityPageData['title'],
			$entityPageData['ns'],
			$entityPageData['lastrevid'],
			$entityPageData['modified']
		);
	}

}
