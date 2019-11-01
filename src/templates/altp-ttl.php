@prefix altr: <http://www.w3.org/ns/dx/conneg/altr#> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix prof: <http://www.w3.org/ns/prof/> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix xml: <http://www.w3.org/XML/1998/namespace> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .

<<?=$resource_uri?>>
    a rdfs:Resource ;
<?php foreach($profiles as $uri => $profile):?>
    <?php if ($profile['default'] == true): ?>
    altr:hasDefaultRepresentation
    <?php else: ?>
    altr:hasRepresentation
    <?php endif ?>
    [
            a altr:Representation ;
            rdfs:label "<?=$profile['title']?>" ;
            dct:conformsTo <<?=$uri?>> ;
            dct:format <http://w3id.org/mediatype/<?=implode('>, <http://w3id.org/mediatype/', $profile['mediatypes'])?>> ;
            rdfs:comment """<?=$profile['description']?>""" ;
        ] ;
<?php endforeach ?>
.