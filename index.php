<?php
// include used resources
// declare available Profiles and default
// get returning representation's Profile
// declare available Media Types for requested Profile and default
// get returning representation's Media Type
// get the org/ register data from the AGLDWG catalogue
// make required ConnegP headers
// render result based on returning Profile & Media Type

// functions
// get data
// render functions
// general render function


// include used resources
require 'vendor/autoload.php';
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

// declare available Media Types for requested Profile and default
$mediatypes_supported = $profiles_supported[$profile_returning]['mediatypes'];
$mediatype_default = $profiles_supported[$profile_returning]['mediatype_default'];

// get returning representation's Media Type
$mediatypes_requested = get_mediatypes_requested($_SERVER['HTTP_ACCEPT']);
$mediatype_returning = get_mediatype_to_return($mediatypes_supported, $mediatypes_requested, $mediatype_default);

// get the org/ register data from the AGLDWG catalogue
$register_items = get_register_contents();

// make required ConnegP headers
header(make_header_list_profiles($_SERVER['REQUEST_URI'], $profiles_supported));
header(make_header_content_profile($profile_returning));

// render result based on returning Profile & Media Type
render($register_items, $profile_returning, $mediatype_returning, $profiles_supported);


// functions
// get data
function get_register_contents() {
    // get the org/ register data from the AGLDWG catalogue
    $headers = array('Accept' => 'application/json');
    $request = Requests::get('http://catalogue.linked.data.gov.au/org', $headers);
    $data = json_decode($request->body);

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

    return $register_items;
}

// render functions
function render_as_reg_html($templates, $register_items) {
    echo $templates->render(
        'page', [
        'page_title' => 'Organisations',
        'register_title' => 'Organisation Register',
        'register_items' => $register_items
    ]);
}

function render_as_reg_turtle($templates, $register_items) {
    header('Content-Type: text/turtle');
    echo $templates->render(
        'register-ttl', [
        'register_title' => 'Organisation Register',
        'register_uri'=> 'http://linked.data.gov.au/org/',
        'register_items' => $register_items
    ]);
}

function render_as_uri_list($register_items) {
    $content = '';
    foreach ($register_items as $register_item) {
        $content .= $register_item['uri'] . "\n";
    }
    header('Content-Type: text/uri-list');
    echo $content;
}

function render_altp_as_html($templates, $profiles) {
    $resource_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    header('Content-Type: text/turtle');
    echo $templates->render(
        'altp', [
        'page_title' => 'Organisation Register',
        'resource_uri' => $resource_uri,
        'profiles' => $profiles
    ]);
}

function render_altp_as_turtle($templates, $profiles) {
    $resource_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    header('Content-Type: text/turtle');
    echo $templates->render(
        'altp-ttl', [
        'page_title' => 'Organisation Register',
        'resource_uri' => $resource_uri,
        'profiles' => $profiles
    ]);
}

// general render function
function render($register_items, $profile_returning, $mediatype_returning, $profiles_supported) {
    switch ($profile_returning) {
        case 'http://www.w3.org/ns/dx/conneg/altr':
            // all Media Types for this profile use templates
            $templates = new League\Plates\Engine('templates');

            if ($mediatype_returning == 'text/turtle') {
                render_altp_as_turtle($templates, $profiles_supported);
            } else {
                render_altp_as_html($templates, $profiles_supported);
            }


            break;
        case 'https://w3id.org/profile/uri-list':
            // only one Media Type supported for this Profile so no if statement based on Media Type
            render_as_uri_list($register_items);
            break;
        default: // 'http://purl.org/linked-data/registry'
            // all Media Types for this profile use templates
            $templates = new League\Plates\Engine('templates');

            if ($mediatype_returning == 'text/turtle') {
                render_as_reg_turtle($templates, $register_items);
            } else {
                render_as_reg_html($templates, $register_items);
            }
    }
}