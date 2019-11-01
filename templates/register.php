<div style="float:right; text-align:center; background-color:#eee; width:350px; padding:5px;">
    <h4>Alternate Representations</h4>
    <p>You can view this register's content in different formats and according to different profiles (data specifications).</p>
    <p>See the specification of <a href="https://w3c.github.io/dxwg/conneg-by-ap/">Content Negotiation by Profile</a> for how to do this in general.</p>
    <p>See this register's <a href="http://linked.data.gov.au/org/connegp.php">Conneg P Description page</a> for specifics.</p>
</div>

<div>
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
