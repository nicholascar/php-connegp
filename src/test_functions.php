<?php

class CustomError extends AssertionError {}

require 'ConnegP.php';
require 'Profile.php';

function arrays_equal(array $a, array $b) {
    // check size of both arrays
    if (count($a) !== count($b)) {
        return false;
    }

    foreach ($b as $key => $bValue) {
        // check that expected value exists in the array
        if (!in_array($bValue, $a, true)) {
            return false;
        }

        // check that expected value occurs the same amount of times in both arrays
        if (count(array_keys($a, $bValue, true)) !== count(array_keys($b, $bValue, true))) {
            return false;
        }
    }

    // check array sequencing
    for ($i = 0; $i < count($a); $i++) {
        if ($a[$i] != $b[$i]) {
            return false;
        }
    }

    return true;
}

function test_get_requested_profiles($cp) {
    // text q weighting
    $header_accept = '<http://example.org/profile/c>;q=0.8, <http://example.org/profile/a>, <http://example.org/profile/b>;q=0.9, <http://example.org/profile/d>;q=0.5';
    $results_expected = array(
        'http://example.org/profile/a',
        'http://example.org/profile/b',
        'http://example.org/profile/c',
        'http://example.org/profile/d',
    );
    $results_actual = $cp->get_profiles_requested($header_accept);

    assert(
        arrays_equal($results_expected, $results_actual),
        new CustomError('test_get_requested_profiles() Test 1 arrays not equal')
    );

    // test duplicate handing
    $header_accept = '<http://example.org/profile/c>;q=0.8, <http://example.org/profile/a>, <http://example.org/profile/b>;q=0.9, <http://example.org/profile/d>;q=0.5, <http://example.org/profile/c>';
    $results_expected = array(
        'http://example.org/profile/a',
        'http://example.org/profile/c',
        'http://example.org/profile/b',
        'http://example.org/profile/d',
    );
    $results_actual  = $cp->get_profiles_requested($header_accept);

    assert(
        arrays_equal($results_expected, $results_actual),
        new CustomError('test_get_requested_profiles() Test 2 arrays not equal')
    );

    // test malformed URI - missing starting & ending  < & >
    $header_accept = '<http://example.org/profile/c>;q=0.8, http://example.org/profile/a, <http://example.org/profile/b>;q=0.9, <http://example.org/profile/d>;q=0.5';
    $results_expected = 'Malformed Accept-Profile header. All profiles must be identified with http, https or urn URIs enclosed in <>. All q values must be numeric.';
    $results_actual = $cp->get_profiles_requested($header_accept);

    assert(
        $results_expected ==  $results_actual,
        new CustomError('test_get_requested_profiles() Test 3 error not reported correctly')
    );

//    // test malformed URI - malformed uri, not http, ttp
//    $header_accept = '<http://example.org/profile/c>;q=0.8, <http://example.org/profile/a>, ttp://example.org/profile/b>;q=0.9, <http://example.org/profile/d>;q=0.5';
//    $results_expected = 'Malformed Accept-Profile header. All profiles must be identified with http, https or urn URIs enclosed in <>.';
//    $results_actual = get_requested_profiles($header_accept);
//
//    assert(
//        $results_expected ==  $results_actual,
//        new CustomError('test_get_requested_profiles() Test 4 error not reported correctly')
//    );

    // test malformed URI - q=x
    $header_accept = '<http://example.org/profile/c>;q=x, <http://example.org/profile/a>, <http://example.org/profile/b>;q=0.9, <http://example.org/profile/d>;q=0.5';
    $results_expected = 'Malformed Accept-Profile header. All profiles must be identified with http, https or urn URIs enclosed in <>. All q values must be numeric.';
    $results_actual = $cp->get_profiles_requested($header_accept);

    assert(
        $results_expected ==  $results_actual,
        new CustomError('test_get_requested_profiles() Test 5 error not reported correctly')
    );

    // test malformed URN
    $header_accept = '<http://example.org/profile/c>;q=0.2, <urn:one:two:three>, <http://example.org/profile/b>;q=0.9, <http://example.org/profile/d>;q=0.5';
    $results_expected = array(
        'urn:one:two:three',
        'http://example.org/profile/b',
        'http://example.org/profile/d',
        'http://example.org/profile/c',
    );
    $results_actual = $cp->get_profiles_requested($header_accept);

    assert(
        arrays_equal($results_expected, $results_actual),
        new CustomError('test_get_requested_profiles() Test 6 error not reported correctly')
    );
}

function test_get_profile_to_return($cp) {
    $profiles_supported = array(
        'http://purl.org/linked-data/registry' => new ConnegP\Profile(
            'Registry Ontology',
            'A list with minimal metadata formulated according to the Registry Ontology',
            array(
                'text/html',
                'text/turtle'
            ),
            'text/html'
        ),
        'https://w3id.org/profile/uri-list' => new ConnegP\Profile(
            'URI List',
            'A list of just registered item URIs, one per line',
            array(
                'text/uri-list'
            ),
            'text/uri-list'
        ),
        'http://www.w3.org/ns/dx/conneg/altr' => new ConnegP\Profile(
            'Alternate Representations Data Model',
            'The representation of the resource that lists all other representations (profiles and Media Types)',
            array(
                'text/html',
                'text/turtle'
            ),
            'text/html'
        )
    );

    // request a supported profile
    $profiles_requested = [
        'http://purl.org/linked-data/registry',
        'https://w3id.org/profile/uri-list'
    ];

    $profile_default = 'http://purl.org/linked-data/registry';

    $profile_returned = $cp->get_profile_to_return($profiles_supported, $profiles_requested, $profile_default);
    assert(
        $profile_returned == 'http://purl.org/linked-data/registry',
        new CustomError('test_get_profile_to_return() Test 1 profiles not equal')
    );

    // request an no supported profiles, gets default
    $profiles_requested = [
        'http://purl.org/linked-data/registryx',
        'https://w3id.org/profile/uri-listx'
    ];

    $profile_returned = $cp->get_profile_to_return($profiles_supported, $profiles_requested, $profile_default);
    assert(
        $profile_returned == $profile_default,
        new CustomError('test_get_profile_to_return() Test 2 profiles not equal. Expected ' . $profile_default . ' got ' . $profile_returned)
    );

    // request no profile, get default
    $profiles_requested = [];

    $profile_returned = $cp->get_profile_to_return($profiles_supported, $profiles_requested, $profile_default);
    assert(
        $profile_returned == $profile_default,
        new CustomError('test_get_profile_to_return() Test 3 profiles not equal')
    );

    // request null, get default
    $profiles_requested = null;

    $profile_returned = $cp->get_profile_to_return($profiles_supported, $profiles_requested, $profile_default);
    assert(
        $profile_returned == $profile_default,
        new CustomError('test_get_profile_to_return() Test 4 profiles not equal')
    );

    // request second profile, get default
    $profiles_requested = [
        'http://purl.org/linked-data/registryx',
        'http://www.w3.org/ns/dx/conneg/altr'
    ];

    $profile_returned = $cp->get_profile_to_return($profiles_supported, $profiles_requested, $profile_default);
    assert(
        $profile_returned == $profile_default,
        new CustomError('test_get_profile_to_return() Test 5 profiles not equal')
    );
}

function test_get_requested_mediatypes($cp) {
    // q weighting
    $header_accept = 'text/html;q=0.8, text/turtle, application/pdf;q=0.9, text/n3;q=0.5';
    $results_expected = array(
        'text/turtle',
        'application/pdf',
        'text/html',
        'text/n3',
    );
    $results_actual = $cp->get_mediatypes_requested($header_accept);

    assert(
        arrays_equal($results_expected, $results_actual),
        new CustomError('test_get_requested_mediatypes() Test 1 arrays not equal')
    );

    // multi 1.0
    $header_accept = 'text/html;q=0.8, text/turtle, application/pdf;q=0.9, text/n3';
    $results_expected = array(
        'text/turtle',
        'text/n3',
        'application/pdf',
        'text/html',
    );
    $results_actual = $cp->get_mediatypes_requested($header_accept);

    assert(
        arrays_equal($results_expected, $results_actual),
        new CustomError('test_get_requested_mediatypes() Test 2 arrays not equal')
    );

    // broken header
    $header_accept = 'text/html;q=0.8, text/,turtle, application/pdf;q=0.9, text/n3';
    $results_expected = 'Malformed Accept header. All Media Types must be IANA Media Types of the form xxxx/yyyy.';
    $results_actual = $cp->get_mediatypes_requested($header_accept);

    assert(
        $results_expected == $results_actual,
        new CustomError('test_get_requested_mediatypes() Test 3 error string not returned')
    );
}

function test_get_mediatype_to_return($cp) {
    // simple request
    $mediatypes_supported = [
        'text/turtle',
        'text/n3',
        'h/text/n-triples',
    ];

    // request a supported profile
    $mediatypes_requested = [
        'text/n3'
    ];

    $mediatypes_returned = $cp->get_mediatype_to_return($mediatypes_supported, $mediatypes_requested, 'text/html');
    assert(
        $mediatypes_returned == 'text/n3',
        new CustomError('test_get_mediatype_to_return() Test 1 Media Types not equal')
    );

    // returning default
    $mediatypes_requested = [
        'text/xxx'
    ];

    $mediatypes_returned = $cp->get_mediatype_to_return($mediatypes_supported, $mediatypes_requested, 'text/html');
    assert(
        $mediatypes_returned == 'text/html',
        new CustomError('test_get_mediatype_to_return() Test 2 Media Types not equal')
    );
}

function test_make_header_list_profiles($cp) {
    $resource_uri = 'http://example.org/resource/a';
    $profiles = array(
        'http://purl.org/linked-data/registry' => array(
            'title' => 'Registry Ontology',
            'description' => 'A list with minimal metadata formulated according to the Registry Ontology',
            'mediatypes' => array(
                'text/html',
                'text/turtle'
            ),
            'mediatype_default' => 'text/html',
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

    $profile_default = 'http://purl.org/linked-data/registry';

    $link_header_expected = 'Link: <http://example.org/resource/a> rel="default"; type="text/html"; profile="http://purl.org/linked-data/registry", <http://example.org/resource/a> rel="alternate"; type="text/turtle"; profile="http://purl.org/linked-data/registry", <http://example.org/resource/a> rel="alternate"; type="text/uri-list"; profile="https://w3id.org/profile/uri-list", <http://example.org/resource/a> rel="alternate"; type="text/html"; profile="http://www.w3.org/ns/dx/conneg/altr", <http://example.org/resource/a> rel="alternate"; type="text/turtle"; profile="http://www.w3.org/ns/dx/conneg/altr"';
    $link_header_actual = $cp->make_header_list_profiles($resource_uri, $profiles, $profile_default);

    assert(
        trim($link_header_actual) == trim($link_header_expected),
        new CustomError('test_make_header_list_profiles() Test 1 Link header not as expected')
    );
}

$cp = new ConnegP\ConnegP();
test_get_requested_profiles($cp);
test_get_profile_to_return($cp);
test_get_requested_mediatypes($cp);
test_get_mediatype_to_return($cp);
test_make_header_list_profiles($cp);

print('tests completed' . "\n\n");