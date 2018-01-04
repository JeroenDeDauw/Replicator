<?php

namespace Tests\Queryr\Replicator\Importer;

use Exception;
use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Importer\ImportStats;

/**
 * @covers \Queryr\Replicator\Importer\ImportStats
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportStatsTest extends TestCase {

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
