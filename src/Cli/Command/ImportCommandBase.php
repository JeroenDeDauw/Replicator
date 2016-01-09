<?php

namespace Queryr\Replicator\Cli\Command;

use Queryr\Replicator\Importer\EntityHandler;
use Queryr\Replicator\Plugin\EntityHandlerPlugin;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DataModel\Entity\EntityDocument;

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
		try {
			if ( $this->factory === null ) {
				$this->factory = ServiceFactory::newFromConfig();
			}

			$this->factory->setEntityHandlers( $this->getPluginEntityHandlers() );
		}
		catch ( RuntimeException $ex ) {
			$output->writeln( '<error>Could not instantiate the Replicator app</error>' );
			$output->writeln( '<error>' . $ex->getMessage() . '</error>' );
			return;
		}

		$this->executeCommand( $input, $output );
	}

	private function getPluginEntityHandlers() {
		global $replicatorEntityHandlers;
		return ( new EntityHandlerPluginReader( $replicatorEntityHandlers ) )->getEntityHandlersPlugins();
	}

	abstract protected function executeCommand( InputInterface $input, OutputInterface $output );

}

class EntityHandlerPluginReader {

	private $entityHandlersPlugins;

	public function __construct( array $entityHandlers = null ) {
		if ( $entityHandlers === null ) {
			$entityHandlers = [];
		}

		$this->entityHandlersPlugins = $entityHandlers;
	}

	/**
	 * @return mixed
	 * @throws RuntimeException
	 */
	public function getEntityHandlersPlugins(): array {
		$entityHandlers = [];

		foreach ( $this->entityHandlersPlugins as $plugin ) {
			$handlerInfo = $this->getPluginInfo( $plugin );

			if ( $this->pluginIsEnabled( $handlerInfo ) ) {
				$entityHandlers[] = $this->getEntityHandler( $handlerInfo );
			}
		}

		return $entityHandlers;
	}

	private function getPluginInfo( $plugin ): array {
		$handlerInfo = is_callable( $plugin ) ? call_user_func( $plugin ) : $plugin;

		$this->validateHandlerInfo( $handlerInfo );

		return $handlerInfo;
	}

	private function validateHandlerInfo( array $handlerInfo ) {
		if ( !is_array( $handlerInfo ) ) {
			throw new RuntimeException( 'Invalid plugin registered! Needs to be an array' );
		}

		if ( array_key_exists( 'input-options', $handlerInfo ) ) {
			if ( !is_array( $handlerInfo['input-options'] ) ) {
				throw new RuntimeException( 'Entity handler plugins need to have an array for input-options' );
			}
		}

		if ( !is_callable( $handlerInfo['is-enabled-function'] ) ) {
			throw new RuntimeException( 'Entity handler plugins need to have a callable for is-enabled-function' );
		}

		if ( !is_callable( $handlerInfo['handler-builder-function'] ) ) {
			throw new RuntimeException( 'Entity handler plugins need to have a callable for handler-builder-function' );
		}
	}

	private function pluginIsEnabled( array $handlerInfo ) {
		return call_user_func( $handlerInfo['is-enabled-function'] );
	}

	private function getEntityHandler( array $handlerInfo ): EntityHandler {
		$entityHandler = call_user_func( $handlerInfo['handler-builder-function'] );

		if ( !( $entityHandler instanceof EntityHandlerPlugin ) ) {
			throw new RuntimeException( 'Plugin error: handler-builder-function needs to return a EntityHandlerPlugin' );
		}

		return new PluginAdaptedEntityHandler( $entityHandler );
	}

}

class PluginAdaptedEntityHandler implements EntityHandler {

	private $plugin;

	public function __construct( EntityHandlerPlugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function handleEntity( EntityDocument $entity ) {
		$this->plugin->handleEntity( $entity );
	}

	public function getHandlingMessage( EntityDocument $entity ): string {
		return $this->plugin->getHandlingMessage( $entity );
	}

}