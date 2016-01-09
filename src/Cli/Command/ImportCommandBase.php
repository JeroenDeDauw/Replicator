<?php

namespace Queryr\Replicator\Cli\Command;

use BatchingIterator\BatchingIterator;
use Queryr\Replicator\Cli\Import\PagesImporterCli;
use Queryr\Replicator\EntityIdListNormalizer;
use Queryr\Replicator\EntitySource\Api\GetEntitiesClient;
use Queryr\Replicator\EntitySource\Api\Http;
use Queryr\Replicator\EntitySource\BatchingEntityPageFetcher;
use Queryr\Replicator\EntitySource\ReferencedEntityPageIterator;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DataModel\Entity\BasicEntityIdParser;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class ImportCommandBase extends Command {

	/**
	 * @var ServiceFactory|null
	 */
	protected $factory = null;

	public function setServiceFactory( ServiceFactory $factory ) {
		$this->factory = $factory;
	}

	final protected function execute( InputInterface $input, OutputInterface $output ) {
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

		$this->executeCommand( $input, $output );
	}

	protected abstract function executeCommand( InputInterface $input, OutputInterface $output );


}
