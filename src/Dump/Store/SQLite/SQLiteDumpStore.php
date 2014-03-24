<?php

namespace Wikibase\Dump\Store\SQLite;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Dump\Page;
use Wikibase\Dump\Store\DumpStore;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SQLiteDumpStore implements DumpStore {

	private $tableBuilder;

	public function __construct( TableBuilder $tableBuilder ) {
		$this->tableBuilder = $tableBuilder;
	}

	/**
	 * @see DumpStore::install
	 */
	public function install() {
		$this->tableBuilder->createTable( new TableDefinition(
			'entities',
			array(
				new FieldDefinition(
					'page_id',
					TypeDefinition::TYPE_BIGINT
				),
				new FieldDefinition(
					'page_title',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						255
					)
				),
				new FieldDefinition(
					'page_namespace',
					TypeDefinition::TYPE_INTEGER
				),
				new FieldDefinition(
					'revision_id',
					TypeDefinition::TYPE_BIGINT
				),
				new FieldDefinition(
					'revision_model',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						32
					)
				),
				new FieldDefinition(
					'revision_format',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						64
					)
				),
				new FieldDefinition(
					'revision_time',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						25
					)
				),
				new FieldDefinition(
					'entity',
					TypeDefinition::TYPE_BLOB
				),
			)
		) );
	}

	/**
	 * @see DumpStore::storePage
	 *
	 * @param Page $page
	 */
	public function storePage( Page $page ) {
		// TODO
	}

}