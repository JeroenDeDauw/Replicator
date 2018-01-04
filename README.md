# Replicator

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/Replicator.png?branch=master)](http://travis-ci.org/JeroenDeDauw/Replicator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/Replicator/?branch=master)

Replicator is a CLI application for replicating a [Wikibase](http://wikiba.se/) entity base
such as [Wikidata](https://www.wikidata.org).

Replicator can import entities from the Wikidata API and from Wikibase dumps in various formats.
It features abort/resume, graceful error handling, progress reporting, dynamic fetching of
dependencies, API batching and standalone installation (no own MediaWiki or Wikibase required).
Furthermore it uses the same deserialization code as Wikibase itself, so is always 100% compatible.

Information is by default written to the
[QueryR EntityStore](https://www.entropywins.wtf/blog/2015/11/14/entitystore-and-termstore-for-wikibasewikidata/)
and [Queryr TermStore](https://www.entropywins.wtf/blog/2015/11/14/entitystore-and-termstore-for-wikibasewikidata/),
as Replicator was created to populate the [QueryR REST API](http://queryr.wmflabs.org/about/).
With some simple PHP additions you can write to the sources of your choosing.

## Installation

### Installation with Vagrant (inside a virtual machine)

Get a copy of the code and make sure you have [Vagrant](https://www.vagrantup.com/) installed.

Copy `config/db-sqlite-example.json` to `config/db.json`.

    cp config/db-sqlite-example.json config/db.json

Then, inside the root directory of the project, execute

    vagrant up
    vagrant ssh
    
Once you're ssh'd into the VM, you can find Replicator fully installed in `/vagrant`.

    cd /vagrant
    ./replicator

### Local installation

Make sure you have all system dependencies:

* PHP 7
* php7.0-mysql
* php7.0-sqlite (only needed for running the tests)

For an always fully up to date list, see `build/vagrant/install_packages.sh`.

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

## Updating

    git pull
    composer update

## Removal

This will remove Replicator from the system, without deleting the application files themselves.

    php replicator uninstall

## Usage

List of commands:

    php replicator

### Importing extracted JSON dumps

Importing a JSON dump:

    php replicator import:json tests/data/simple/five-entities.json -v

Import command help:

    php replicator help import:json

The command can be aborted with `ctrl+c`. It will exit gracefully and provide you
with the page position marker needed to resume the import.

    php replicator import:json tests/data/simple/five-entities.json -v --continue 66943

### Importing compressed JSON dumps

Importing a gzipped JSON dump:

    php replicator import:gz tests/data/simple/five-entities.json.gz -v

Import command help:

    php replicator help import:gz

The command can be aborted with `ctrl+c`. It will exit gracefully and provide you
with the page position marker needed to resume the import.

    php replicator import:gz tests/data/simple/five-entities.json.gz -v --continue=76071

Bzip2 support is also available via the `import:bz2` command. However beware that at the time
of writing this documentation (November 2015), the Wikidata bz2 dumps have an issue that
prevents PHP (and thus this library) from reading them entirely.

### Importing from the Wikidata.org API

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

### Importing XML dumps

Importing an XML dump:

    php replicator import:xml tests/data/big/5341-revs-3-props.xml -v

Import command help:

    php replicator help import:xml

The command can be aborted with `ctrl+c`. It will exit gracefully and provide you
with the page title needed to resume the import.

    php replicator import:xml tests/data/big/5341-revs-3-props.xml --continue Q15826105 -v

## Logging

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


## Release notes

### Version 0.2 (2017-03-06)

* Upgraded Wikibase DataModel from 4.x to 6.x (needed to work with recent data from Wikidata)
* Added Vagrant support
* The query store is no longer installed by default (install with `composer require jeroen/query-engine`)
* PHP 7.0 or later is now required (for local installation)

### Version 0.1 (2016-01-25)

* Initial release: see [blog post](https://www.entropywins.wtf/blog/2016/01/25/replicator-a-cli-tool-for-wikidata/)