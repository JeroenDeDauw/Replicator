<?php

namespace Tests\Queryr\Replicator\EntitySource\Api;

use Queryr\Replicator\EntitySource\Api\EntityPagesFetcher;

/**
 * @covers Queryr\Replicator\EntitySource\Api\EntityPagesFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityPagesFetcherTest extends \PHPUnit_Framework_TestCase {

	public function testGivenNoIds_emptyArrayIsReturned() {
		$fetcher = new EntityPagesFetcher( $this->newHttpMockThatShouldNotBeCalled() );

		$pages = $fetcher->fetchEntityPages( [] );

		$this->assertSame( [], $pages );
	}

	private function newHttpMockThatShouldNotBeCalled() {
		$http = $this->getMock( 'Queryr\Replicator\EntitySource\Api\Http' );

		$http->expects( $this->never() )->method( 'get' );

		return $http;
	}

	/**
	 * @dataProvider idAndUrlProvider
	 */
	public function testGivenIds_urlHasIds( array $ids, $expectedUrl ) {
		$http = $this->getMock( 'Queryr\Replicator\EntitySource\Api\Http' );

		$http->expects( $this->once() )
			->method( 'get' )
			->with( $this->equalTo( $expectedUrl ) );

		$fetcher = new EntityPagesFetcher( $http );
		$fetcher->fetchEntityPages( $ids );
	}

	public function idAndUrlProvider() {
		return [
			[
				[ 'Q1' ],
				'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q1&format=json'
			],
			[
				[ 'Q1', 'Q2', 'Q3' ],
				'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q1|Q2|Q3&format=json'
			],
		];
	}

}