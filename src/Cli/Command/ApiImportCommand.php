<?php

namespace Queryr\Replicator\Cli\Command;

use BatchingIterator\BatchingIterator;
use Queryr\Replicator\Cli\Import\PagesImporterCli;
use Queryr\Replicator\EntityIdListNormalizer;
use Queryr\Replicator\EntitySource\Api\GetEntitiesClient;
use Queryr\Replicator\EntitySource\Api\Http;
use Queryr\Replicator\EntitySource\BatchingEntityPageFetcher;
use Queryr\Replicator\EntitySource\ReferencedEntityPageIterator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DataModel\Entity\BasicEntityIdParser;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiImportCommand extends ImportCommandBase {

	protected function configure() {
		$this->setName( 'import:api' );
		$this->setDescription( 'Imports entities via a Wikibase Repo web API' );

		$this->addArgument(
			'entities',
			InputArgument::IS_ARRAY,
			'The IDs of the entities to import, separated by spaces. ID ranges can be specified: Q1-Q100'
		);

//		$this->addOption(
//			'url',
//			null,
//			InputOption::VALUE_OPTIONAL,
//			'The full url of the Wikibase Repo web API to use',
//			'https://www.wikidata.org/w/api.php'
//		);

		$this->addOption(
			'batchsize',
			'b',
			InputOption::VALUE_OPTIONAL,
			'The number of API requests to bundle together',
			10
		);

		$this->addOption(
			'include-references',
			'r',
			InputOption::VALUE_NONE,
			'If referenced entities should also be imported'
		);
	}

	/**
	 * @var Http|null
	 */
	private $http;

	public function setHttp( Http $http ) {
		$this->http = $http;
	}

	protected function executeCommand( InputInterface $input, OutputInterface $output ) {
		$importer = new PagesImporterCli( $input, $output, $this->factory );

		$importer->runImport( $this->getEntityPageIterator( $input ) );
	}

	private function getEntityPageIterator( InputInterface $input ): \Iterator {
		$http = $this->http === null ? new Http() : $this->http;

		$idListNormalizer = new EntityIdListNormalizer( new BasicEntityIdParser() );
		$ids = [];

		foreach ( $idListNormalizer->getNormalized( $input->getArgument( 'entities' ) ) as $id ) {
			  $ids[] = $id->getSerialization();
		}

		$batchingFetcher = new BatchingEntityPageFetcher(
			new GetEntitiesClient( $http ),
			$ids
		);

		$iterator = new BatchingIterator( $batchingFetcher );
		$iterator->setMaxBatchSize( (int)$input->getOption( 'batchsize' ) );

		if ( $input->getOption( 'include-references' ) ) {
			return new ReferencedEntityPageIterator(
				$iterator,
				$batchingFetcher,
				$this->factory->newLegacyEntityDeserializer()
			);
		}

		return $iterator;
	}

}
