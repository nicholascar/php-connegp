<?php
// set error visibility
// include used resources

// declare available Profiles and default
// get returning representation's Profile
// get returning representation's Media Type
// render result based on returning Profile & Media Type

// get the org/ register data from the AGLDWG catalogue
// declare a sort by title function
// declare available Profiles and default
// sort data by title
// create register items objects array

/*
// set error visibility
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
// include used resources
require '../vendor/autoload.php';
require 'functions.php';

// declare available Profiles and default
$profiles_supported = array(
    'http://purl.org/linked-data/registry' => array(
        'title' => 'Registry Ontology',
        'description' => 'A list with minimal metadata formulated according to the Registry Ontology',
        'mediatypes' => array(
            'text/html',
            'text/turtle'
        ),
        'mediatype_default' => 'text/html',
        'default' => true
    ),
    'https://w3id.org/profile/uri-list' => array(
        'title' => 'URI List',
        'description' => 'A list of just registered item URIs, one per line',
        'mediatypes' => array(
            'text/uri-list'
        ),
        'mediatype_default' => 'text/uri-list'
    ),
    'http://www.w3.org/ns/dx/conneg/altr' => array(
        'title' => 'Alternate Representations Data Model',
        'description' => 'The representation of the resource that lists all other representations (profiles and Media Types)',
        'mediatypes' => array(
            'text/html',
            'text/turtle'
        ),
        'mediatype_default' => 'text/html'
    )
);
$profiles_default = 'http://purl.org/linked-data/registry';

// get returning representation's Profile
$profiles_requested = get_profiles_requested($_SERVER['HTTP_ACCEPT_PROFILE']);
$profile_returning = get_profile_to_return($profiles_supported, $profiles_requested, $profiles_default);
print('$profile_returning: ' . $profile_returning . "\n");

// get returning representation's Media Type
$mediatypes_supported = $profiles_supported[$profile_returning]['mediatypes'];
$mediatype_default = $profiles_supported[$profile_returning]['mediatype_default'];
$mediatypes_requested = get_mediatypes_requested($_SERVER['HTTP_ACCEPT']);
$mediatype_returning = get_mediatype_to_return($mediatypes_supported, $mediatypes_requested, $mediatype_default);
print('$mediatype_returning: ' . $mediatype_returning . "\n");


/*
$templates = new League\Plates\Engine('../php/templates');

// get the org/ register data from the AGLDWG catalogue
$headers = array('Accept' => 'application/json');
$request = Requests::get('http://catalogue.linked.data.gov.au/org', $headers);

// declare a sort by title function
function cmp($a, $b) {
    return strcmp($a->title, $b->title);
}

// sort data by title
usort($data, "cmp");

// create register items objects array
$register_items = array();
foreach ($data as $record) {
    $register_items[] = array(
        'uri' => 'http://linked.data.gov.au' . $record->field_agldwg_identifier,
        'title' => $record->title,
        'desc' => $record->description,
        'homepage' => $record->field_homepage,
        'agor_id' => $record->field_agor_identifier,
        'crs_id' => $record->field_crs_identifier
    );
}
*/


/*
if ($_GET['_mediatype'] == 'text/turtle' || $_SERVER['Accept'] == 'text/turtle') {
    header('Content-Type: text/turtle');
    echo $templates->render(
        'register-ttl', [
            'register_title' => 'Organisation Register',
            'register_uri'=> 'http://linked.data.gov.au/org/',
            'register_items' => $register_items
        ]);
} else {
    echo $templates->render(
        'page', [
            'page_title' => 'Organisations',
            'register_title' => 'Organisation Register',
            'register_items' => $register_items
        ]);
}
*/
