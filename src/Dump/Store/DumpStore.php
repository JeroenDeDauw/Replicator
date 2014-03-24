<?php

namespace Wikibase\Dump\Store;

use Wikibase\Dump\Page;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface DumpStore {

	public function storePage( Page $page );

}