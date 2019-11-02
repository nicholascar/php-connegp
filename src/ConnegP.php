<?php

namespace NicholasCar\ConnegP;

class ConnegP
{
    /**
     * Gets the complete request URI from PHPs environment variables.
     *
     * @return string
     */
    public function get_fully_qualified_resource_uri()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * Gets a list of profile URIs in most weighted order, from Accept-Profile header.
     *
     * @param $header_accept_profile. String/null. Pass string to override $_SERVER['HTTP_ACCEPT_PROFILE']
     * @return array. Of URIs of profiles
     */
    public function get_profiles_requested($header_accept_profile = null)
    {
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

    /**
     * Gets either the URI of first profile requested that is supported, or the default profile URI.
     *
     * @param $profiles_supported_uris. Array of supported profiles in Profiles Array format
     * @param $profiles_requested_uris. Array of URIs of requested profiles in preference order
     * @param $profile_default. String of URI of the default profile
     * @return string. URI of a profile
     */
    public function get_profile_to_return($profiles_supported_uris, $profiles_requested_uris, $profile_default)
    {
        if (!is_null($profiles_requested_uris)) {
            foreach ($profiles_requested_uris as $profile_requested) {
                foreach ($profiles_supported_uris as $profile_supported) {
                    if ($profile_requested == $profile_supported) {
                        return $profile_requested;
                    }
                }
            }
        }

        return $profile_default;
    }

    // TODO: determine if need to cater for all Accept header parts other than q, e.g. char encoding directives, if relevant
    /**
     * Gets a list of profile URIs in most weighted order, from Accept-Profile header
     *
     * @param $header_accept_profile. String/null. Pass string to override $_SERVER['HTTP_ACCEPT']
     * @return array. Of strings of Media Types (e.g. text/html)
     */
    public function get_mediatypes_requested($header_accept = null)
    {
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
                if (strpos($profile_parts[0], '/') === false) {
                    return 'Malformed Accept header. All Media Types must be IANA Media Types of the form xxxx/yyyy.';
                }

                $mediatypes[] = array(
                    'mediatype' => trim($profile_parts[0], '<>'),
                    'q' => (!empty($profile_parts[1]) ? $profile_parts[1] : 1.0) // set profiles with no q value to 1.0
                );
            }
            // sort the array by q values, largest first
            usort($mediatypes, function ($a, $b) {
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

    /**
     * Gets either the token of first Media Type requested that is supported by the returning profile,
     * or the profile's default Media Type
     *
     * @param $mediatypes_supported. Array of strings of supported Media Types for the profile requested
     * @param $mediatypes_requested. Array of strings of requested Media Types in preference order
     * @param $mediatype_default. String of the default Media Type for the profile requested
     * @return string. Of Media Type (e.g. text/turtle)
     */
    public function get_mediatype_to_return($mediatypes_supported, $mediatypes_requested, $mediatype_default)
    {
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

    /**
     * Makes the Link header required for the *list profiles* function.
     *
     * @param $resource_uri. String, the URI requested.
     * @param $profiles_supported. Array of Profiles
     * @param $profile_default. String, URI of default profile
     * @return string. Link header
     */
    public function make_header_list_profiles($resource_uri, $profiles_supported, $profile_default)
    {
        $links = array();
        foreach ($profiles_supported as $profile_uri => $profile) {
            foreach ($profile->mediatypes as $mediatype) {
                if ($profile_uri == $profile_default and $mediatype == $profile->mediatype_default) {
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

        return 'Link: ' . implode($links, ', ') . "\n";
    }

    /**
     * Makes the Content-Profile header required for all ConnegP responses.
     *
     * @param $returned_profile_uri. String of URI of profile returned
     * @return string. Content-Profile header
     */
    public function make_header_content_profile($returned_profile_uri)
    {
        return 'Content-Profile: ' . $returned_profile_uri . "\n";
    }
}