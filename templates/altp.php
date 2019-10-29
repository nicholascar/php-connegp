<div >
    Linked data Registers: <a href="http://linked.data.gov.au/def/">Definitional Items</a> | <a href="http://linked.data.gov.au/dataset/">Datasets</a> | <a href="http://linked.data.gov.au/org/">Organisations</a>
</div>

<h1>Alternate Representations of the <?=$page_title?></h1>
<h4><a href="<?=$resource_uri?>"><?=$resource_uri?></a></h4>

<p><sup>*</sup> default</p>
<table>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Profile URI</th>
        <th>Media Types</th>
        <th>Default</th>
    </tr>

<?php foreach($profiles as $uri => $profile):?>
    <tr>
        <td><?=$profile['title']?></td>
        <td><?=$profile['description']?></td>
        <td><a href="<?=$uri?>"><?=$uri?></a></td>
        <td>
<?php foreach($profile['mediatypes'] as $mediatype):?>
<?php if ($mediatype == $profile['mediatype_default']): ?>
            <a href="http://w3id.org/mediatype/<?=$mediatype?>"><?=$mediatype?></a> <strong><sup>*</sup></strong><br />
<?php else: ?>
            <a href="http://w3id.org/mediatype/<?=$mediatype?>"><?=$mediatype?></a><br />
<?php endif ?>
<?php endforeach ?>
        </td>
<?php if ($profile['default'] == true): ?>
        <td><strong><sup>*</sup></strong></td>
<?php else: ?>
        <td></td>
<?php endif ?>
    </tr>
<?php endforeach ?>
</table>
