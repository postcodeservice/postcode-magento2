<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\Postcode\Services\Validation;

class Response implements ValidationInterface
{
    /**
     * @var string[]
     */
    private array $responseKeys = []; // set in NLAddressValidation, GetBEStreetFind, GetBEZipcodeFind, etc.

    /**
     * Set the keys
     *
     * @param mixed $keys
     */
    public function setKeyFields(mixed $keys): void
    {
        $this->responseKeys = $keys;
    }

    /**
     * @inheritDoc
     */
    public function validateResponseData(mixed $data): bool
    {
        /* $data should be something like:
        {
            "results": [
                {
                    "street": "Kabelweg",
                    "street_language": "nl",
                    "street_id": "st_4876428982452",
                    "house_number": 21,
                    "house_letter": "L",
                    "house_letter_validated": false,
                    "zipcode": "1014BA",
                    "city": "Amsterdam",
                    "municipality": "Amsterdam",
                    "province": "Noord-Holland",
                    "is_po_box": false,
                    "is_on_wadden_islands": false,
                    "address_function": [
                        "accommodation",
                        "multipurpose",
                        "office",
                        "storage"
                    ],
                    "geo_precision": "rooftop",
                    "latitude": 52.390515085771,
                    "longitude": 4.8463724025161,
                    "postal_address": {
                        "line_1": "Kabelweg 21L",
                        "line_2": "1014BA AMSTERDAM",
                        "line_3": "Netherlands"
                    }
                }
            ],
            "error_code": null,
            "errors": [],
            "pagination": {
                "current_page": 1,
                "per_page": 10,
                "is_last_page": true
            }
        }
        */

        // If the response is not an array or the results array does not exist, it's invalid
        if (!is_array($data)) {
            return false;
        }

        // Use array_reduce to check if all keys in $this->responseKeys are present in each item
        // If any keys are missing, return false
        return array_reduce($this->responseKeys, function ($carry, $key) use ($data) {
            return $carry && array_key_exists($key, $data);
        }, true);
    }
}
