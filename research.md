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

## Full import

### Internal dumps

We can use the current dumps, though there is no reusable deserialization component for this yet.

### JSON dumps

We can also wait for JSON dumps for which there is a deserialization component, though it is not
clear when WMDE and WMF will manage to finally get this functionality working and deployed.

## Change replication

### PubSubHubbub

http://en.wikipedia.org/wiki/PubSubHubbub

https://www.mediawiki.org/wiki/Extension:PubSubHubbub

### Partial dumps
