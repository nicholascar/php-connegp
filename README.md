# PHP ConnegP
A library of functions for HTTP Content Negotiation by Profile (https://www.w3.org/TR/dx-prof-conneg/).

This code is online at <https://github.com/nicholascar/php-connegp>.

All functions are in [src/functions.php](src/functions.php) while [src/test_functions.php](src/test_functions.php) contains unit tests for most of them. The functions are documented in place but basically serve the following 3 purposes:

1. supports the *list profiles* function
    * by creating a `Link` header with Alternate Representation information, as per the [HTTP *list profile*](https://w3c.github.io/dxwg/conneg-by-ap/#http-listprofiles) part of the ConnegP specification
    * see `make_header_list_profiles()`
2. supports returning a *Content-Profile* header
    * see `make_header_content_profile()`
    * requires that the URI of the profile that the returned representation conforms to has been calculated
3. supports *get resource by profile*
    * by assisting with parsing `Accept-Profile` HTTP request header: `get_profiles_requested()`
    * by checking if the requested profile matches a supported profile: `get_profile_to_return()`
    * same for Media Types: `get_mediatypes_requested()` & `get_mediatype_to_return()`

## License & Rights
This code was developed by Nicholas Car to assist with the Content Negotiation by Profile W3C Recommendation.

### Rights
&copy; Nicholas J. Car, 2019

### License
GNU GPL 3 (see [LICENSE](LICENSE) for the deed).

## Contact
Author:  
**Nicholas Car**  
*Data System Architect*  
[SURROUND Australia Pty Ltd](https://surroundaustralia.com)  
[nicholas.car@surround.com](mailto:nicholas.car@surround.com)
