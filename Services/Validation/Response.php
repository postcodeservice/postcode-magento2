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

    private array $responseKeys = []; // set in GetAddress, GetBeStreet, GetBePostcode, etc.

    /**
     * Set the keys
     *
     * @param mixed $keys
     */
    public function setRequestFields($keys): void
    {
        $this->responseKeys = $keys;
    }

    /**
     * @inheritDoc
     */
    public function validate($data): bool
    {
        // if the response is not an array, it's invalid
        if (!is_array($data)) {
            return false;
        }

        // allow succes === false request to be processed at front-end side
        if (array_key_exists("success", $data) && $data["success"] === false) {
            return true;
        }

        return $this->validateResponseFields($data);
    }

    /**
     * Check if the required response fields are present
     *
     * @param mixed $data
     *
     * @return bool
     */
    private function validateResponseFields(array $data): bool
    {
        // If $data is a single-level array (NL) instead of multi-level array (BE),
        // wrap it in another array for compatibility with the code in this method
        if (!is_array(reset($data))) {
            $data = [$data];
        }

        // Iterate over each element (which is now guaranteed to be an array) in the data array
        foreach ($data as $item) {
            // Check if all keys in $this->responseKeys are present in the item
            $missingKeys = array_diff($this->responseKeys, array_keys($item));

            // If any keys are missing, return false
            if (!empty($missingKeys)) {
                return false;
            }
        }

        // If all items passed the check, return true
        return true;
    }
}
