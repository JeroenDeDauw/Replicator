<?php

namespace Wikibase\Dump\Store\SQLite;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Dump\Page;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreWriter {

	private $queryInterface;

	public function __construct( QueryInterface $queryInterface ) {
		$this->queryInterface = $queryInterface;
	}

	/**
	 * @see DumpStore::storePage
	 *
	 * @param Page $page
	 */
	public function storePage( Page $page ) {
		$revision = $page->getRevision();

		$this->queryInterface->insert(
			'entities',
			array(
				'page_id' => $page->getId(),
				'page_title' => $page->getTitle(),
				'page_namespace' => $page->getNamespace(),

				'revision_id' => $revision->getId(),
				'revision_model' => $revision->getModel(),
				'revision_format' => $revision->getFormat(),
				'revision_time' => $revision->getTimeStamp(),

				'entity' => $revision->getText(),
			)
		);
	}

}