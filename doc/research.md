This document contains notes from our initial research into how to replicate the WD entity base.

## Overall strategy

We will maintain a copy of the latest entities on our system.

We will have an entity table with a row for each entity. Each row will have a blob with the
serialized entity and some fields for indexing. We can use various persistence technologies
for this, the exact choice of which has no further relevance on this document. Presumably we
initially pick a tool we will already be using for other purposes.

From this entity storage, we can than create derivatives. The primary one we will have at
first will be the QueryEngine.

We need a way to do a full import of the data to get our system up and running, experiment
with additional copies and recover from bad failures. We also need a way to keep our copy
of the WD entity base up to date, by periodically integrating changes made on WD.

* https://www.wikidata.org/wiki/Wikidata:Database_download
* https://www.wikidata.org/wiki/Wikidata:Data_access

## Full import

### Internal dumps

http://dumps.wikimedia.org/wikidatawiki/

We can use the current dumps, though there is no reusable deserialization component for this yet.

Such a component can however be created by us. Doing the same is high on the prio list of the WD
team (though for different reasons), so we presumably do not need to do all work. [A git repo]
(https://github.com/wmde/WikibaseInternalSerialization) exists already.

### JSON dumps

We can also wait for JSON dumps for which there is a [deserialization component]
(https://github.com/wmde/WikibaseDataModelSerialization), though it is not clear when
WMDE and WMF will manage to finally get this functionality working and deployed.

## Change replication

### PubSubHubbub

http://en.wikipedia.org/wiki/PubSubHubbub

https://www.mediawiki.org/wiki/Extension:PubSubHubbub

Extension developed mainly by a team of students that has some contact with the WD dev team.

* Not clear when the code will be production ready
* Not clear when this will be deployed (if at all) - can we do this ourselves?

PubSubHubbub potentially allows us to react to each entity change in near real-time.

### Incremental dumps

http://dumps.wikimedia.org/other/incr/wikidatawiki/

Used by Magnus his [Wikidata query](https://bitbucket.org/magnusmanske/wikidataquery)
[here](https://bitbucket.org/magnusmanske/wikidataquery/src/846c96135e52228b701c9fc5ab37d13719b668d6/download_and_process_incremental_dumps.php?at=master).

We also have the serialization format dilemma here.

Dumps are created for 1 day intervals.

The last 12 days are currently empty directories. Either the dumps are not created each day,
or the process is not very reliable.
