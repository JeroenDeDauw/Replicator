<?php

namespace Queryr\Replicator\Commands\Importer;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\Dump\Reader\Page;
use Queryr\Dump\Reader\ReaderFactory;
use Queryr\Dump\Store\ItemRow;
use Queryr\Dump\Store\Store;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\Database\QueryInterface\InsertFailedException;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\QueryEngine\QueryStoreWriter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportCommandExecutor {

	private $input;
	private $output;
	private $dumpStore;
	private $entityDeserializer;
	private $queryStoreWriter;

	public function __construct( InputInterface $input, OutputInterface $output,
		Store $dumpStore, Deserializer $entityDeserializer, QueryStoreWriter $queryStoreWriter ) {

		$this->input = $input;
		$this->output = $output;
		$this->dumpStore = $dumpStore;
		$this->entityDeserializer = $entityDeserializer;
		$this->queryStoreWriter = $queryStoreWriter;
	}

	public function run() {
		$dumpReader = $this->newDumpReader();

		/**
		 * @var Page $entityPage
		 */
		foreach ( $dumpReader->getIterator() as $key => $entityPage ) {
			$this->importEntityPage( $key, $entityPage );
		}
	}

	private function newDumpReader() {
		$dumpReaderFactory = new ReaderFactory();

		return $dumpReaderFactory->newDumpReaderForFile( $this->input->getArgument( 'file' ) );
	}

	private function importEntityPage( $importNumber, Page $entityPage ) {
		$this->output->write( 'Importing entity ' . $importNumber . ': ' . $entityPage->getTitle() . '... ' );

		try {
			$entity = $this->entityFromEntityPage( $entityPage );
			$this->output->write( 'deserialized... ' );

			if ( $entity->getType() !== 'item' ) {
				$this->output->writeln( 'not an item - skipping.' );
				return;
			}

			$this->insertIntoQueryStore( $entity );

			$this->insertIntoDumpStore( $entityPage, $entity->getId() );
		}
		catch ( DeserializationException $ex ) {
			$this->output->writeln( 'deserialization failed!' );
			$this->output->writeln( $ex->getMessage() );
		}
		catch ( InsertFailedException $ex ) {
			$this->output->writeln( 'insert failed!' );
			$this->output->writeln( $ex->getMessage() );
		}
		catch ( \OutOfBoundsException $ex ) {
			$this->output->writeln( 'insert failed!' );
			$this->output->writeln( $ex->getMessage() );
		}
	}

	private function insertIntoQueryStore( Entity $entity ) {
		$this->queryStoreWriter->insertEntity( $entity );
		$this->output->write( 'in query store...' );
	}

	private function insertIntoDumpStore( Page $entityPage, ItemId $id ) {
		$itemRow = $this->itemRowFromEntityPage( $entityPage, $id );

		$this->dumpStore->storeItemRow( $itemRow );
		$this->output->writeln( 'in dump store.' );
	}

	private function itemRowFromEntityPage( Page $entityPage, ItemId $id ) {
		$revision = $entityPage->getRevision();

		return new ItemRow(
			$id->getNumericId(),
			$revision->getText(),
			$entityPage->getTitle(),
			$revision->getId(),
			$revision->getTimeStamp()
		);
	}

	/**
	 * @param Page $entityPage
	 * @return Item
	 * @throws DeserializationException
	 */
	private function entityFromEntityPage( Page $entityPage ) {
		return $this->entityDeserializer->deserialize(
			json_decode( $entityPage->getRevision()->getText(), true )
		);
	}

}