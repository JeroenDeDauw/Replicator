<?php

namespace Tests\Queryr\Replicator\EntitySource\Api;

use Queryr\Replicator\EntitySource\Api\GetEntitiesClient;

/**
 * @covers Queryr\Replicator\EntitySource\Api\GetEntitiesClient
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GetEntitiesClientTest extends \PHPUnit_Framework_TestCase {

	public function testGivenNoIds_emptyArrayIsReturned() {
		$fetcher = new GetEntitiesClient( $this->newHttpMockThatShouldNotBeCalled() );

		$this->assertSame(
			[],
			$fetcher->fetchEntityPages( [] )
		);
	}

	private function newHttpMockThatShouldNotBeCalled() {
		$http = $this->getMock( 'Queryr\Replicator\EntitySource\Api\Http' );

		$http->expects( $this->never() )->method( 'get' );

		return $http;
	}

	public function testWhenHttpReturnsFalse_emptyArrayIsReturned() {
		$http = $this->getMock( 'Queryr\Replicator\EntitySource\Api\Http' );

		$http->expects( $this->any() )
				->method( 'get' )
				->willReturn( false );

		$fetcher = new GetEntitiesClient( $http );

		$this->assertSame(
			[],
			$fetcher->fetchEntityPages( [] )
		);
	}

	/**
	 * @dataProvider idAndUrlProvider
	 */
	public function testGivenIds_urlHasIds( array $ids, $expectedUrl ) {
		$http = $this->getMock( 'Queryr\Replicator\EntitySource\Api\Http' );

		$http->expects( $this->once() )
			->method( 'get' )
			->with( $this->equalTo( $expectedUrl ) );

		$fetcher = new GetEntitiesClient( $http );
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