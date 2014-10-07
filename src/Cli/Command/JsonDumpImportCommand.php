<?php

namespace Queryr\Replicator\Cli\Command;

use Queryr\Replicator\Cli\Import\PagesImporterCli;
use Queryr\Replicator\EntitySource\JsonDump\JsonDumpIterator;
use Queryr\Replicator\EntitySource\JsonDump\JsonDumpReader;
use Queryr\Replicator\Model\EntityPage;
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
class JsonDumpImportCommand extends Command {

	protected function configure() {
		$this->setName( 'import:dump' );
		$this->setDescription( 'Imports entities from a JSON dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the JSON dump file'
		);

		$this->addOption(
			'continue',
			'c',
			InputOption::VALUE_OPTIONAL,
			'The position to resume import from'
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

		$reader = new JsonDumpReader(
			$input->getArgument( 'file' ),
			$this->factory->newCurrentEntityDeserializer()
		);

		$continuePosition = $input->getOption( 'continue' );

		if ( $continuePosition !== null ) {
			$reader->seekToPosition( (int)$continuePosition );
			$reader->nextJsonLine();
		}

		$iterator = new JsonDumpIterator(
			$reader,
			$this->factory->newCurrentEntityDeserializer()
		);

		$onAborted = function() use ( $output, $reader ) {
			$output->writeln( "\n" );
			$output->writeln( "<info>Import process aborted</info>" );
			$output->writeln( "<comment>To resume, run with</comment> --continue=" . $reader->getPosition() );
		};

		$importer = new PagesImporterCli( $input, $output, $this->factory, $onAborted );

		$importer->runImport( $this->newEntityPageIterator( $iterator ) );
	}

	private function newEntityPageIterator( JsonDumpIterator $dumpIterator ) {
		foreach ( $dumpIterator as $entity ) {
			yield new EntityPage(
				$dumpIterator->getCurrentJson(),
				// TODO
				$entity->getType() === 'property' ? 'Property:' . $entity->getId() : $entity->getId(),
				0,
				0,
				( new \DateTime() )->format( 'Y-m-d\TH:i:s\Z' )
			);
		}
	}

}
