<?php

namespace Wikibase\Dump\Store\SQLite;

use Wikibase\Dump\Page;
use Wikibase\Dump\Store\DumpStore;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SQLiteDumpStore implements DumpStore {

	private $storeInstaller;
	private $storeWriter;

	public function __construct( StoreInstaller $storeInstaller, StoreWriter $storeUpdater ) {
		$this->storeInstaller = $storeInstaller;
		$this->storeWriter = $storeUpdater;
	}

	/**
	 * @see DumpStore::install
	 */
	public function install() {
		$this->storeInstaller->install();
	}

	/**
	 * @see DumpStore::uninstall
	 */
	public function uninstall() {
		$this->storeInstaller->uninstall();
	}

	/**
	 * @see DumpStore::storePage
	 *
	 * @param Page $page
	 */
	public function storePage( Page $page ) {
		$this->storeWriter->storePage( $page );
	}

}