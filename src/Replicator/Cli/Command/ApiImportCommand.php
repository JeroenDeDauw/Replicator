<?php

namespace Queryr\Replicator\Cli\Command;

use Queryr\Replicator\Cli\Import\PagesImporterCli;
use Queryr\Replicator\EntitySource\Api\ApiEntityPageIterator;
use Queryr\Replicator\EntitySource\Api\EntityPagesFetcher;
use Queryr\Replicator\EntitySource\Api\Http;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiImportCommand extends Command {

	protected function configure() {
		$this->setName( 'import:api' );
		$this->setDescription( 'Imports entities via a Wikibase Repo web API' );

		$this->addArgument(
			'entities',
			InputArgument::IS_ARRAY,
			'The ids of the entities to import'
		);

		$this->addOption(
			'url',
			null,
			InputOption::VALUE_OPTIONAL,
			'The full url of the Wikibase Repo web API to use',
			'https://www.wikidata.org/w/api.php'
		);
	}

	/**
	 * @var ServiceFactory|null
	 */
	private $factory = null;

	public function setServiceFactory( ServiceFactory $factory ) {
		$this->factory = $factory;
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		if ( $this->factory === null ) {
			try {
				$this->factory = ServiceFactory::newFromConfig();
			}
			catch ( RuntimeException $ex ) {
				$output->writeln( '<error>Could not instantiate the Replicator app</error>' );
				$output->writeln( '<error>' . $ex->getMessage() . '</error>' );
				return;
			}
		}

		$importer = new PagesImporterCli( $input, $output, $this->factory );

		$importer->runImport( new ApiEntityPageIterator(
			new EntityPagesFetcher( new Http() ),
			[ 'Q3' ]
		) );
	}

}
