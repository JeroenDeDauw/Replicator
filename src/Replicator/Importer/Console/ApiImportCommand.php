<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\Dump\Reader\DumpReader;
use Queryr\Dump\Reader\ReaderFactory;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
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

		$importer->runImport( new \ArrayIterator() );
	}

}
