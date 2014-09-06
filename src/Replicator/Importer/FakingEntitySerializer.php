<?php

namespace Queryr\Replicator\Importer;

use Serializers\Serializer;

class FakingEntitySerializer implements Serializer {

	private $serialization;

	public function __construct( $serialization ) {
		$this->serialization = $serialization;
	}

	public function serialize( $object ) {
		return $this->serialization;
	}

}