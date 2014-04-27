<?php

namespace Tests\Queryr\Replicator\Importer;

use Exception;
use Queryr\Replicator\Importer\ImportStats;

/**
 * @covers Queryr\Replicator\Importer\ImportStats
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportStatsTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ImportStats
	 */
	private $stats;

	public function setUp() {
		$this->stats = new ImportStats();
	}

	public function testWhenNothingIsRecorded_getCountReturnsZero() {
		$this->assertEquals( 0, $this->stats->getEntityCount() );
	}

	public function testBothSuccessAndFailureAddsToCount() {
		$this->stats->recordSuccess();
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordSuccess();

		$this->assertEquals( 3, $this->stats->getEntityCount() );
	}

	public function testOnlyFailureAddsToFailureCount() {
		$this->stats->recordSuccess();
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordSuccess();

		$this->assertEquals( 1, $this->stats->getErrorCount() );
	}

	public function testGetErrorMessagesReturnsEmptyArrayWhenThereAreNoErrors() {
		$this->stats->recordSuccess();

		$this->assertEquals( array(), $this->stats->getErrorMessages() );
	}

	public function testGetErrorMessagesReturnsArrayWithMessagesAsKeysAndCountsAsValues() {
		$this->stats->recordError( new Exception( '~[,,_,,]:3' ) );
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordError( new Exception( 'to much foobar' ) );
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordError( new Exception( 'to much foobar' ) );

		$this->assertEquals(
			[
				'not enough kittens' => 3,
				'to much foobar' => 2,
				'~[,,_,,]:3' => 1,
			],
			$this->stats->getErrorMessages()
		);

		$this->assertEquals( [3, 2, 1], array_values( $this->stats->getErrorMessages() ) );
	}

	public function testDuplicateEntriesAreOneElementInArray() {
		$this->stats->recordError( new Exception( "Duplicate entry 'jo2010555010-P691-Q15830226' for key 'value_property'" ) );
		$this->stats->recordError( new Exception( "Duplicate entry 'jk01041856-P691-Q15830225' for key 'value_property'" ) );
		$this->stats->recordError( new Exception( "Duplicate entry 'Q5-P31-Q15830216' for key 'value_property'" ) );

		$this->assertEquals(
			[
				'Duplicate entry' => 3
			],
			$this->stats->getErrorMessages()
		);
	}

	public function testOnlySuccessAddsToSuccessCount() {
		$this->stats->recordSuccess();
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordSuccess();

		$this->assertEquals( 2, $this->stats->getSuccessCount() );
	}

	public function testWhenThereAreNoErrors_errorRatioIsZero() {
		$this->stats->recordSuccess();
		$this->stats->recordSuccess();

		$this->assertEquals( 0, $this->stats->getErrorRatio() );
	}

	public function testWhenThereAreNoEntities_errorRatioIsZero() {
		$this->assertEquals( 0, $this->stats->getErrorRatio() );
	}

	public function testWhenThereAreOnlyErrors_errorRatioIsOneHundred() {
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordError( new Exception( 'not enough kittens' ) );

		$this->assertEquals( 100, $this->stats->getErrorRatio() );
	}

	public function testErrorRatio() {
		$this->stats->recordSuccess();
		$this->stats->recordError( new Exception( 'not enough kittens' ) );
		$this->stats->recordError( new Exception( 'not enough kittens' ) );

		$this->assertEquals( 2 / 3 * 100, $this->stats->getErrorRatio() );
	}

}
