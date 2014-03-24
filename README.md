# QueryR Replicator

QueryR Replicator is an application for replicating a Wikibase entity base.

## Application

A CLI application using the Symfony Console component.

##### Installation

[Get Composer](https://getcomposer.org/download/)

    composer install

##### To show a list of commands:

    php app/replicator.php

##### Importing an XML dump:

    php app/replicator.php import tests/data/big/5341-revs-3-props.xml

##### Import command help

    php app/replicator.php --help import

## DumpReader

This repo also contains the DumpReader library, which is needed by the
QueryR Replicator application.

Services from this library should be constructed via the Factory in its
root. Classes that are not in the library root are package private.
