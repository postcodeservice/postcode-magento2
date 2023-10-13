# Postcode Service 1.6.0 Release

## New features

- Switched from Netherlands V3 API to Netherlands V5 API for quicker responses:

from:

```text
V3: https://api.postcodeservice.com/nl/v3/getAddress?postcode=4201KB&huisnummer=63
```

to:

```text
V5: https://api.postcodeservice.com/nl/v5/find?zipcode=4201KB&houseno=63
```

- Added support for Belgium bilingual results in bilingual municipalities,
  see https://developers.postcodeservice.com/#belgium-api-GETbe-v3-zipcode-find
- Switched from Belgium V2 API to Belgium V3 API, also enabling bilingual results for zipcodes, for
  example:

```text
V3: https://api.postcodeservice.com/be/v3/zipcode-find?zipcodezone=1050&multiresults=1
```

```json
[
  {
    "zipcode": 1050,
    "city": "Brussel",
    "latitude": 50.8222854,
    "longitude": 4.3815707
  },
  {
    "zipcode": 1050,
    "city": "Bruxelles",
    "latitude": 50.8222854,
    "longitude": 4.3815707
  }
  ...
]
```

- Added Germany as a configuration option in the Magento Admin panel, expanding geographical
  support.
- Added Germany V1 API support in the Magento extension, for example this returns all zipcodes
  starting with 4072XX when the end-user starts typing `4072..` in the zipcode field:

```text
https://api.postcodeservice.com/de/v1/zipcode-find?zipcodezone=4072
```

```json
[
  {
    "zipcode": 40721,
    "city": "Hilden"
  },
  {
    "zipcode": 40723,
    "city": "Hilden"
  },
  {
    "zipcode": 40724,
    "city": "Hilden"
  }
]
```

- Added console logging and info message display when the Postcode Service API is called and an
  issue occurs, for example when the authorization is failed, improving overal debuggability. The
  code can be found in the folder `view/base/web/js/postcode-handler` in files `postcode-nl.js`
  and `postcode-be.js`:

```js
if (data.error_code) {
    // show error in console
    console.error('Postcodeservice.com extension: ' + JSON.stringify(data));
    if (data.error_code > 400) {
        errorMessage = 'Could not perform address validation.';
    }
    if (data.error_code === 429) {
        errorMessage = 'Address validation temporarily unavailable.';
    }
}
 ```

- Added handling of rate limiting errors from api.postcodeservice.com in JSON format instead of
  plain text.
- Revised and updated the Magento User manual to ensure it matches the latest changes.
- Updated `SECURITY.md` with GitHub’s private reporting option, providing a secure channel for issue
  reporting.
- Successfully tested the extension with the new Magento 2.4.7-beta1, Magento 2.4.7-beta2, confirming compatibility and
  performance.

## UX/UC improvements

- Substantially decreased the loading speed of the address results for the Netherlands, reducing the
  wait time from 500 milliseconds to a mere 30 milliseconds by default. This can be changed
  in `postcode-nl.js`,
  see comment `// The last parameter is the delay in millisecond` in the file.
- Similarly, decreased the loading speed of the address results for Belgium from 500 milliseconds to
  50 milliseconds for
  streets and 30 milliseconds for zipcodes by default. This can be changed in `postcode-be.js`, see
  twice
  the comment `// Parameter for the results delay in milliseconds`.
- Eliminated the redundant loading screen for Nederlands in Magento Checkout, enhancing the overall
  user experience in the vanilla Magento Luma checkout theme. You can still turn this on in the
  file `postcode-api.js` in the method `getPostCodeNL` by setting the variable `showLoader: false` to `true`.
- Deactivated advanced settings that were not being utilized, thereby simplifying the user
  interface.
- Added new documentation links from within the extension to the Magento User manual and underlying
  API.
- Integrated direct support form links to the Postcode Service support team in the Magento Admin
  panel, ensuring more efficient assistance when required.
- Transitioned inline SVG images to external links, resulting in a cleaner codebase and smaller svg
  files.
- Modified the external image link to align with the Postcode Service color scheme.
- Introduced explanatory text for test credentials in the Magento Admin panel to prevent confusion,
  improving user comprehension.
- Revised info messages such as `Loading streets ...`, `Cannot find street, is it correct?` to be
  more clear and consistent, enhancing
  end-user communication clarity.
- Renamed menu item in `Stores -> Configuration -> Sales` name from `Postcode Service International`
  to `Postcode Service` to make it more intuitive.

## Fixed bugs

- Rectified a typo error in the sign-up button.
- Undertook code refactoring in `tab.phtml`, `postcode-nl.js`, `postcode-be.js`,
  and `postcode-handler.js` to improve readability and maintainability.
- Resolved a z-index/overlap issue on Belgium data fields that occurred when more than 7 results
  were returned, ensuring proper display of the result set in the checkout.
- Addressed a bug where info messages such as `Loading zipcodes ...` could inadvertently be copied
  into
  the postcode, street, or city field.

## Language changes

- Updated the language files `nl_NL.csv`, `be_BE.csv`, and `fr_FR.csv` by adding new
  JavaScript and PHP output strings for improved localization support.

## Release credits

- Thanks go out to the following people for contributing to this release: Robert, Tim S., Vincent, Erik de
  Groot, Viktoriia and Peter S.
