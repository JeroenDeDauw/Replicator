<?php

namespace Tests\Queryr\Replicator\EntitySource\Api;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\EntitySource\Api\GetEntitiesInterpreter;
use Queryr\Replicator\Model\EntityPage;

/**
 * @covers \Queryr\Replicator\EntitySource\Api\GetEntitiesInterpreter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GetEntitiesInterpreterTest extends TestCase {

	public function testGivenInvalidJson_emptyArrayIsReturned() {
		$interpreter = new GetEntitiesInterpreter();

		$entityPages = $interpreter->getEntityPagesFromResult( '~=[,,_,,]:3' );

		$this->assertSame( [], $entityPages );
	}

	public function testGivenMissingIdResponse_emptyArrayIsReturned() {
		$response = <<<EOT
{
    "entities": {
        "-1": {
            "id": "Q99999999",
            "missing": ""
        },
        "-2": {
            "id": "Q999999991",
            "missing": ""
        }
    },
    "success": 1
}
EOT;

		$interpreter = new GetEntitiesInterpreter();

		$entityPages = $interpreter->getEntityPagesFromResult( $response );

		$this->assertSame( [], $entityPages );
	}

	public function testGivenErrorResponse_emptyArrayIsReturned() {
		$response = <<<EOT
{
    "servedby": "mw1130",
    "error": {
        "code": "no-such-entity",
        "info": "Invalid id: Q0"
    }
}
EOT;

		$interpreter = new GetEntitiesInterpreter();

		$entityPages = $interpreter->getEntityPagesFromResult( $response );

		$this->assertSame( [], $entityPages );
	}

	public function testGivenQ1Response_arrayWithQ1IsReturned() {
		$response = file_get_contents( __DIR__ . '/wbgetentities-Q1.json' );

		$interpreter = new GetEntitiesInterpreter();

		$entityPages = $interpreter->getEntityPagesFromResult( $response );

		$this->assertCount( 1, $entityPages );
		$this->assertIsQ1Page( $entityPages[0] );
	}

	private function assertIsQ1Page( EntityPage $entityPage ) {
		$this->assertSame( 0, $entityPage->getNamespaceId() );
		$this->assertSame( '2014-06-16T06:28:09Z', $entityPage->getRevisionTime() );
		$this->assertSame( 138106750, $entityPage->getRevisionId() );
		$this->assertSame( 'Q1', $entityPage->getTitle() );
		$this->assertContains( 'universe', $entityPage->getEntityJson() );
	}

	public function testGivenMultipleEntityResponse_arrayWithAllEntitiesIsReturned() {
		$response = file_get_contents( __DIR__ . '/wbgetentities-Q1Q2Q3.json' );

		$interpreter = new GetEntitiesInterpreter();

		$entityPages = $interpreter->getEntityPagesFromResult( $response );

		$this->assertCount( 3, $entityPages );
		$this->assertIsQ1Page( $entityPages[0] );
	}

}