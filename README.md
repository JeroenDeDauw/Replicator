# QueryR Replicator

QueryR Replicator is an application for replicating a [Wikibase](http://wikiba.se/) entity base.

## System dependencies

* PHP 5.5 or later
* php5-mysql
* php5-sqlite (only needed for running the tests)

## Application

A CLI application using the [Symfony Console component]
(http://symfony.com/doc/current/components/console/introduction.html).

##### Installation

[Get Composer](https://getcomposer.org/download/) and execute:

    composer install

##### Usage

List of commands:

    php app/replicator.php

Importing an XML dump:

    php app/replicator.php import tests/data/big/5341-revs-3-props.xml

Import command help:

    php app/replicator.php --help import

## Running the tests

Setup test database

    mysql --user root -p < tests/createTestDB.sql

Drop test database

    mysql --user root -p < tests/dropTestDB.sql

Running the tests

    phpunit

## DumpReader

This repo also contains the DumpReader library, which is needed by the
QueryR Replicator application.

Services from this library should be constructed via the Factory in its
root. Classes that are not in the library root are package private.

## DumpStore

This repo also contains the DumpStore library, which is needed by the
QueryR Replicator application.

Services from this library should be constructed via the Factory in its
root. Classes that are not in the library root are package private.