# QueryR Replicator

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/Replicator.png?branch=master)](http://travis-ci.org/JeroenDeDauw/Replicator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/?branch=master)

QueryR Replicator is an application for replicating a [Wikibase](http://wikiba.se/) entity base.

## System dependencies

* PHP 5.5 or later (PHP 7 and HHVM are supported)
* php5-mysql
* php5-sqlite (only needed for running the tests)

## Application

A CLI application using the [Symfony Console component]
(http://symfony.com/doc/current/components/console/introduction.html).

### Installation

Clone the git repository and move into its directory.

Enter the details of your database in `config/db.json`. An example of how this is done
can be found in `config/db-example.json`. The parameters are fed directly to Doctrine
DBAL. A list of available parameters can be found [in the DBAL docs]
(http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html).

[Get Composer](https://getcomposer.org/download/) and execute:

    composer install
    php replicator install

If you just downloaded the composer.phar executable, the install command works as follows:

    php composer.phar install

Certain functions from the PHP Process Control library (PCNTL) are used. They are disabled
by default on some linux distributions. You might need to remove some functions from the
`disable_functions` section in your `php.ini` file. In particular the `pcntl_signal_dispatch`
function.

### Updating

    git pull
    composer update

### Removal

This will remove Replicator from the system, without deleting the application files themselves.

    php replicator uninstall root-db-user root-db-pwd

### Usage

List of commands:

    php replicator

#### Importing JSON dumps

Importing a JSON dump:

    php replicator import:json tests/data/big/1000-entities.json -v

Import command help:

    php replicator help import:json

The command can be aborted with `ctrl+c`. It will exit gracefully and provide you
with the page position marker needed to resume the import.

    php replicator import:json tests/data/big/1000-entities.json -v --continue 2557421

#### Importing from the Wikidata.org API

Importing entities via the web API:

    php replicator import:api Q1 Q2 Q1337 -v
    
Including referenced entities:

    php replicator import:api Q1 Q2 Q1337 -v -r

Import command help:

    php replicator help import:api

It is possible to specify ID ranges:

    php replicator import:api Q1-Q1000

Multiple ranges and single IDs can be specified:

    php replicator import:api Q1 Q100-Q102 P43-P45 Q64

#### Importing XML dumps

Importing an XML dump:

    php replicator import:xml tests/data/big/5341-revs-3-props.xml -v

Import command help:

    php replicator help import:xml

The command can be aborted with `ctrl+c`. It will exit gracefully and provide you
with the page title needed to resume the import.

    php replicator import:xml tests/data/big/5341-revs-3-props.xml --continue Q15826105 -v

### Logging

All logs are written into `var/log`. Each import run writes a detailed log to a dedicated file,
which gets named based on the time the import started. Error events get written to errors.log,
which is a general error file, appended to by each import run.


## Running the tests

For tests only

    composer test

For style checks only

	composer cs

For a full CI run

	composer ci
