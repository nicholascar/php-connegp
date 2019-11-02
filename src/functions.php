<?php

namespace NicholasCar\ConnegP;

// gets the complete request of a URI from PHPs environment variables
function get_fully_qualified_resource_uri() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// gets a list of profile URIs in most weighted order, from Accept-Profile header
function get_profiles_requested($header_accept_profile=null) {
    if (is_null($header_accept_profile)) {
        $header_accept_profile = $_SERVER['HTTP_ACCEPT_PROFILE'];
    }

    if (!empty($header_accept_profile)) {
        $profiles = array();
        foreach (preg_split("/,\s*</", $header_accept_profile) as $pair) {
            // check all entries start correctly, either <, h, u
            if (substr($pair, 0, 3) != 'htt' and substr($pair, 0, 3) != 'urn' and $pair[0] != '<') { //for http, https or urn
                return 'Malformed Accept-Profile header. All profiles must be identified with http, https or urn URIs enclosed in <>.';
            }
            // check all entries end correctly, either < or a number
            if (substr($pair, -1) != '>' and !is_numeric(substr($pair, -1))) { //for http, https or urn
                return 'Malformed Accept-Profile header. All profiles must be identified with http, https or urn URIs enclosed in <>. All q values must be numeric.';
            }
            $profile_parts = preg_split('/;q=/', $pair);

            $profiles[] = array(
                'profile' => trim($profile_parts[0], '<>'),
                'q' => (!empty($profile_parts[1]) ? $profile_parts[1] : 1.0) // set profiles with no q value to 1.0
            );
        }
        // sort the array by q values, largest first
        usort($profiles, function ($a, $b) {
            return $b['q'] <=> $a['q'];
        });

        $p = [];
        foreach ($profiles as $profile) {
            $p[] = $profile['profile'];
        }
        return array_values(array_unique($p));
    } else {
        return [];
    }
}

// returns either the URI of first profile requested that is supported, or the default profile URI
function get_profile_to_return($profiles_supported, $profiles_requested, $profile_default) {
    // get keys from $profiles_supported
    $ps = array_keys($profiles_supported);

    if (!is_null($profiles_requested)) {
        foreach ($profiles_requested as $profile_requested) {
            foreach ($ps as $profile_supported) {
                if ($profile_requested == $profile_supported) {
                    return $profile_requested;
                }
            }
        }
    }

    return $profile_default;
}

// gets a list of profile URIs in most weighted order, from Accept-Profile header
// TODO: determine if need to cater for all Accept header parts other than q, e.g. char encoding directives, if relevant
function get_mediatypes_requested($header_accept=null) {
    if (is_null($header_accept)) {
        $header_accept = $_SERVER['HTTP_ACCEPT'];
    }

    if (!empty($header_accept)) {
        // tidy the header
        $header_accept = strtolower(str_replace(' ', '', $header_accept));
        $mediatypes = array();
        foreach (explode(',', $header_accept) as $pair) {
            $profile_parts = preg_split('/;q=/', $pair);

            // the Media Type must contain a /
            if (strpos($profile_parts[0], '/') === false){
                return 'Malformed Accept header. All Media Types must be IANA Media Types of the form xxxx/yyyy.';
            }

            $mediatypes[] = array(
                'mediatype' => trim($profile_parts[0], '<>'),
                'q' => (!empty($profile_parts[1]) ? $profile_parts[1] : 1.0) // set profiles with no q value to 1.0
            );
        }
        // sort the array by q values, largest first
        usort($mediatypes, function($a, $b) {
            return $b['q'] <=> $a['q'];
        });

        $p = [];
        foreach ($mediatypes as $profile) {
            $p[] = $profile['mediatype'];
        }
        return array_values(array_unique($p));
    } else {
        return 'No Media Types found.';
    }
}

// returns either the token of first Media Type requested that is supported, or the profile's default Media Type
function get_mediatype_to_return($mediatypes_supported, $mediatypes_requested, $mediatype_default) {
    if (!is_null($mediatypes_requested)) {
        foreach ($mediatypes_requested as $mediatype_requested) {
            foreach ($mediatypes_supported as $mediatype_supported) {
                if ($mediatype_requested == $mediatype_supported) {
                    return $mediatype_requested;
                }
            }
        }
    }

    return $mediatype_default;
}

// makes the Link header required for the *list profiles* function.
//
// Requires as inputs the resource URI and an array of profiles. See test_functions.php for examples of how to format
// arrays of profiles.
function make_header_list_profiles($resource_uri, $profiles, $profile_default) {
    $links = array();
    foreach ($profiles as $profile_uri => $profile) {
        foreach ($profile['mediatypes'] as $mediatype) {
            if ($profile_uri == $profile_default and  $mediatype == $profile['mediatype_default']) {
                $rel = 'default';
            } else {
                $rel = 'alternate';
            }
            $links[] = strtr(
                '<$resource_uri> rel="$rel"; type="$mediatype"; profile="$profile_uri"',
                array(
                    '$resource_uri' => $resource_uri,
                    '$rel' => $rel,
                    '$mediatype' => $mediatype,
                    '$profile_uri' => $profile_uri
                )
            );
        }
    }

    return 'Link: ' . implode($links,', ') . "\n";
}

function make_header_content_profile($returned_profile_uri) {
    return 'Content-Profile: ' . $returned_profile_uri . "\n";
}
