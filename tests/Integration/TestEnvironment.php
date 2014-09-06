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
		$instance = new self();
		$instance->initialize();
		return $instance;
	}

	/**
	 * @var ServiceFactory
	 */
	private $factory;

	private function __construct() {}

	private function initialize() {
		$connection = DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );

		$this->factory = ServiceFactory::newFromConnection( $connection );

		$this->factory->newEntityStoreInstaller()->install();
		$this->factory->newQueryEngineInstaller()->install();
		$this->factory->newTermStoreInstaller()->install();
	}

	/**
	 * @return ServiceFactory
	 */
	public function getFactory() {
		return $this->factory;
	}

}
