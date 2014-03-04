## Platform

Will use PHP, can go with 5.4 or 5.5.

## Infrastructure

WikibaseDataModel https://github.com/wmde/WikibaseDataModel

Will have CLI interface, so probably want to use
http://symfony.com/doc/current/components/console/introduction.html

## Structure

Blob storage of entities + meta data, indexed somehow. This is what we keep in sync.
Other data is derived from this synced entity base.

## Workflow

* Change comes into the system
* Entity is constructed from change
* Entity is serialized and stuffed into our persistence
* Event is fired that allows other parts of the system to respond
* QueryEngine plugin handles event and handles entity to the QueryEngine instance

## UI

* CLI script for full import
* CLI script for change replication

The two operations need to be distinguished as well in the system, as much code does not need
to run on initial import.