<?php

namespace Tests\Queryr\Replicator\Integration;

use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\ServiceFactory;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TestEnvironment {

	public static function newInstance() {
		return new self();
	}

	/**
	 * @var ServiceFactory
	 */
	private $factory;

	private function __construct() {
		$connection = DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );

		$this->factory = ServiceFactory::newFromConnection( $connection );

		$this->factory->newEntityStoreInstaller()->install();

		if ( defined( 'WIKIBASE_QUERYENGINE_VERSION' ) ) {
			$this->factory->newQueryEngineInstaller()->install();
		}

		$this->factory->newTermStoreInstaller()->install();
	}

	/**
	 * @return ServiceFactory
	 */
	public function getFactory(): ServiceFactory {
		return $this->factory;
	}

}
