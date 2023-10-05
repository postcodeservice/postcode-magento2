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

class Request implements ValidationInterface
{
    /** @var string[] */
    private array $requestKeys = []; // set in GetAddress, GetBeStreet, GetNlStreet, etc.

    /**
     * @inheritdoc
     *
     * @param string[] $keys
     */
    public function setRequestFields($keys): void
    {
        $this->requestKeys = $keys;
    }

    /**
     * Get keys
     *
     * @return string[]
     */
    public function getRequestFields(): array
    {
        return $this->requestKeys;
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

        return $this->validateRequestFields($data);

    }

    /**
     * Validates that all request keys are present in the provided data.
     *
     * This function iterates over each key in $this->requestKeys and checks if it exists in the provided data array.
     * If any key is missing, the function immediately returns false. If all keys are present, it returns true.
     *
     * @param array $data The data to validate.
     *
     * @return bool Returns true if all request keys are present in the data, false otherwise.
     */
    public function validateRequestFields(array $data): bool
    {
        // Check if all keys are present in the request keys
        foreach ($this->requestKeys as $key) {
            // If a key is missing, return false immediately
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }

        // If all keys were present, return true
        return true;
    }

}
