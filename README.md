# QueryR Replicator

QueryR Replicator is an application for replicating a [Wikibase](http://wikiba.se/) entity base.

## System dependencies

* PHP 5.5 or later
* php5-mysql
* php5-sqlite (only needed for running the tests)

## Application

A CLI application using the [Symfony Console component]
(http://symfony.com/doc/current/components/console/introduction.html).

#### Installation

Clone the git repository and move into its directory.

[Get Composer](https://getcomposer.org/download/) and execute:

    composer install
    php app/replicator.php install root-db-user root-db-pwd new-db-name new-db-user new-user-pwd

If you just downloaded the composer.phar executable, the install command works as follows:

    php composer.phar install

Certain functions from the PHP Process Control library (PCNTL) are used. They are disabled
by default on some linux distributions. You might need to remove some functions from the
`disable_functions` section in your `php.ini` file. In particular the `pcntl_signal_dispatch`
function.

### Updating

    git pull
    composer update

#### Removal

This will remove Replicator from the system, without deleting the application files themselves.

    php app/replicator uninstall root-db-user root-db-pwd

### Usage

List of commands:

    php app/replicator

#### Import command

Importing an XML dump:

    php app/replicator import tests/data/big/5341-revs-3-props.xml -v

Import command help:

    php app/replicator --help import

The command can be aborted with `ctrl+c`. It will exit gracefully and provide you
with the page title needed to resume the import.

    php app/replicator import tests/data/big/5341-revs-3-props.xml --continue Q15826105 -v

## Running the tests

Running the tests

    phpunit

Setup test database (optional, only needed for some integration tests)

    mysql --user root -p < tests/createTestDB.sql

Drop test database

    mysql --user root -p < tests/dropTestDB.sql

## DumpReader

This repo also contains the DumpReader library, which is needed by the
QueryR Replicator application.

Services from this library should be constructed via the Factory in its
root. Classes that are not in the library root are package private.
