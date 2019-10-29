@prefix dct: <http://purl.org/dc/terms/> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix reg: <http://purl.org/linked-data/registry#> .
@prefix sdo: <https://schema.org/> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .

<<?=$register_uri?>> a reg:Register ;
    dct:title "Organisations Register" ;
    reg:containedItemClass sdo:Organization ;
    dct:description "Register of all Organisations listed by the Austrlaian Government Linked Data Working Group characterised as schema.org Organization class instances." ;
    dct:publisher <http://linked.data.gov.au/org/agldwg> ;
.

<?php foreach($register_items as $item):?>
<<?=$item['uri']?>> a reg:RegisteredItem ;
    rdfs:label "<?=$item['title']?>" ;
    reg:register <<?=$register_uri?>> .

<?php endforeach ?>
