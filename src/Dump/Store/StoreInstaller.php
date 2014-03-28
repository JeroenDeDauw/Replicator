<?php

namespace Wikibase\Dump\Store;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\Database\Schema\TableBuilder;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreInstaller {

	private $tableBuilder;

	public function __construct( TableBuilder $tableBuilder ) {
		$this->tableBuilder = $tableBuilder;
	}

	public function install() {
		$this->tableBuilder->createTable( new TableDefinition(
			Store::ITEMS_TABLE_NAME,
			array(
				new FieldDefinition(
					'item_id',
					TypeDefinition::TYPE_BIGINT
				),
				new FieldDefinition(
					'item_json',
					TypeDefinition::TYPE_BLOB
				),
				new FieldDefinition(
					'page_title',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						255
					)
				),
				new FieldDefinition(
					'revision_id',
					TypeDefinition::TYPE_BIGINT
				),
				new FieldDefinition(
					'revision_time',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						25
					)
				),
			)
		) );
	}

	public function uninstall() {
		$this->tableBuilder->dropTable( Store::ITEMS_TABLE_NAME );
	}

}