# Postcode Service 1.7.0 Release

## New features

- Added French language support to the extension. French language support can be enabled in the
  backend admin panel. To upgrade your subscription to enable French language validation, see the
  pricing
  information at https://postcodeservice.com/#compare-packages.
- Moved Netherlands, Belgium, and Germany API calls to the latest uniform URL pattern for uniform
  error handling and debugging support.

For example, for the Netherlands, moved from API V5:

```text
https://api.postcodeservice.com/nl/v5/find?postcode=4201KB&huisnummer=63
```

to V6 (uniform URL call):

```text
https://api.postcodeservice.com/nl/v6/address-validation?zipcode=4201KB&house_number=63
```

See https://developers.postcodeservice.com/#netherlands-api for more details.

- Changed the internal APIs to the latest versions, resulting in code cleanup in legacy API
  validation functions.
- Restructured postcode-nl.js, postcode-be.js, postcode-de.js so that it is easier to change the
  dynamic values in the JavaScript files by using constants, which can be found in the head of the
  files.
- Updated postcode_nl.css, postcode_be.css, postcode_de.css so that the margins are better aligned
  with the default Lumen theme.
- Updated the Plugin/Model/ResourceModel/Country/CollectionPlugin.php file with the most optimal
  input fields order for Belgium, Germany, and France.
- Tested with Magento 2.4.7-beta2 and 2.4.7-beta3.

## UX/UC improvements

- Changed the field order for Belgium and Germany to match the default country form-filling
  standard. For example, for Belgium and Germany, the default form input fields are zipcode ->
  city -> street -> house number. For the Netherlands, this is still zipcode -> house number.
- Changed the load delay for The Netherlands from 30 ms to instant loading.

## Fixed bugs

- Fixed CSS styling bug where the dropdown list goes under input fields when there are more than the
  default 7 maximum results.
- Fixed bug where a PHP Fatal error could occur if the field order is overwritten by other
  extensions or custom code.

## Release credits

- Thanks go out to the following people for contributing to this release: Tim S., Viktoria, Peter
  S., Robert