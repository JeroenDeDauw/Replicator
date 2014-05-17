<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\Replicator\Importer\ImportStats;
use Queryr\Replicator\Importer\StatsReporter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ConsoleStatsReporter implements StatsReporter {

	private $output;

	public function __construct( OutputInterface $output ) {
		$this->output = $output;
	}

	public function reportStats( ImportStats $stats ) {
		$this->output->writeln( "\n" );

		$this->output->writeln( '<info>Import stats:</info>' );
		$this->output->writeln(
			sprintf(
				'<comment>%d entities, %d errors, %d successful, %g%% error ratio</comment>',
				$stats->getEntityCount(),
				$stats->getErrorCount(),
				$stats->getSuccessCount(),
				$stats->getErrorRatio()
			)
		);

		$errors = $stats->getErrorMessages();

		if ( !empty( $errors ) ) {
			$this->reportErrors( $errors );
		}
	}

	private function reportErrors( array $errors ) {
		$this->output->writeln( "\nErrors:" );

		foreach ( $errors as $errorMessage => $errorCount ) {
			$this->output->writeln( sprintf(
				"\t* %d times: %s",
				$errorCount,
				$errorMessage
			) );
		}
	}

	public function reportAbortion( $pageTitle ) {
		$this->output->writeln( "\n" );
		$this->output->writeln( "<info>Import process aborted</info>" );
		$this->output->writeln( "<comment>To resume, run with</comment> --continue=$pageTitle" );
	}

}
