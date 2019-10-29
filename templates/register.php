<div >
    Linked data Registers: <a href="http://linked.data.gov.au/def/">Definitional Items</a> | <a href="http://linked.data.gov.au/dataset/">Datasets</a> | <a href="http://linked.data.gov.au/org/">Organisations</a>
</div>

<h1><?=$register_title?></h1>

<ul>
    <?php foreach($register_items as $item):?>
        <li>
            <a href="<?=$item['uri']?>"><?=$item['title']?></a>
        </li>
    <?php endforeach ?>
</ul>
