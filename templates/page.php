<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$this->e($page_title)?></title>
<meta charset="utf-8" />
<link rel="schema.dcterms" href="http://purl.org/dc/terms/" />
<meta name="dcterms.format" content="text/html" />
<meta name="dcterms.language" content="en" />
<meta name="dcterms.type" content="Text" />
<meta name="dcterms.publisher" content="Australian Government Linked Data Working Group" />
<meta name="dcterms.creator" content="Nicholas Car" />
<meta name="dcterms.title" content="Australian Government Linked Data Working Group home page" />
<meta name="dcterms.date" content="2017-02-18T20:30+10:00" />
<meta name="dcterms.modified" content="2018-07-25T14:30+10:00" />
<meta name="description" content="Home page of the Australian Government Linked Data Working Group" />
<meta name="generator" content="Lovingly coded by hand" />

<link type="text/css" rel="stylesheet" href="http://www.linked.data.gov.au/style/css/ochre.css" media="all" />
</head>
<body>
<header id="header" style="text-align:right; padding-top:10px;">
    <div style="width:150px; height:150px; float:left;">
        <img src="http://www.linked.data.gov.au/style/img/agldwg-logo-ochre-150.png" alt="AGLDWG logo" />
    </div>
    <div id="nav">
        <a href="/">Home</a> |
        <a href="http://www.linked.data.gov.au/governance">Governance</a> |
        <a href="http://www.linked.data.gov.au/assistance">Assistance</a> |
        <a href="http://www.linked.data.gov.au/showcase">Showcase</a> |
        <a href="http://www.linked.data.gov.au/events">Events</a> |
        <a href="http://www.linked.data.gov.au/groups">Groups</a> |
        <a href="http://www.linked.data.gov.au/howto">How To</a> |
        <a href="http://www.linked.data.gov.au/contact">Contact</a> |
        <a href="http://www.linked.data.gov.au/join">Join</a>
    </div>
</header>

<div id="main" style="clear:both;">

    <?php $this->insert('register', ['page_title' => $page_title, 'register_title' => $register_title, 'register_items' => $register_items]) ?>

</div> <!-- #main -->

<div id="footer">
    <div id="footer_content">
        <div id="license">
            <img src="http://www.linked.data.gov.au/style/img/cc-by.png" alt="CC BY 4.0" width="88" height="31">
            This web page content is licensed under a <a href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>
        </div>
        <div style="float:right; width:49%; text-align:right;">

        </div>
        <div style="clear:both;">&nbsp;</div>
    </div>
</div><!-- footer -->
</body>
</html>