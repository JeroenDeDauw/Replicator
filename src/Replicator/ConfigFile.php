<?php

namespace Queryr\Replicator;

use RuntimeException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ConfigFile {

	private $configDir;
	private $configPath;

	public static function newInstance() {
		return new self();
	}

	private function __construct() {
		$this->configDir = __DIR__ . '/../../config/';
		$this->configPath = $this->configDir . 'db.json';
	}

	public function write( array $config ) {
		$this->createDirIfNeeded();

		$writeResult = @file_put_contents(
			$this->configPath,
			json_encode( $config, JSON_PRETTY_PRINT )
		);

		if ( $writeResult === false ) {
			throw new RuntimeException( 'Could not write the config file' );
		}
	}

	private function createDirIfNeeded() {
		if ( !is_dir( $this->configDir ) ) {
			$success = @mkdir( $this->configDir );

			if ( !$success ) {
				throw new RuntimeException( 'Could create the config directory' );
			}
		}
	}

	/**
	 * @return array
	 * @throws RuntimeException
	 */
	public function read() {
		$configJson = @file_get_contents( $this->configPath );

		if ( $configJson === false ) {
			throw new RuntimeException( 'Could not read the config file' );
		}

		return json_decode( $configJson, true );
	}

}

