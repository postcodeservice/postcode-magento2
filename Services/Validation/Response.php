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
    private $keysToContain = ['success', 'straatnaam', 'woonplaats'];

    /**
     * @inheritDoc
     */
    public function validate($data)
    {
        if (!is_array($data)) {
            return false;
        }

        if ($this->checkIfRecursive($data)) {
            return $this->validateElements($data);
        }

        if (!$this->validateResult($data)) {
            return false;
        }

        if (!$this->checkStreetNameValue($data)) {
            return false;
        }

        return true;
    }

    /**
     * Get the Keys
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->keysToContain;
    }

    /**
     * Set the keys
     *
     * @param mixed $keys
     */
    public function setKeys($keys)
    {
        $this->keysToContain = $keys;
    }

    /**
     * Check the keys
     *
     * @param mixed $data
     *
     * @return bool
     */
    private function checkKeys($data)
    {
        $check = 0;
        foreach ($this->keysToContain as $key) {
            array_key_exists($key, $data)?: $check++;
        }

        return $check == 0;
    }

    /**
     * Important note : Before using this method, first trigger the checkKeys method.
     *
     * @param mixed $data
     *
     * @return bool
     */
    private function checkStreetNameValue($data)
    {
        if (strpos($data['straatnaam'], 'limiet bereikt') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Validate Result
     *
     * @param array $result
     *
     * @return bool
     */
    private function validateResult($result)
    {
        if (!$this->checkKeys($result)) {
            return false;
        }

        return true;
    }

    /**
     * Check if multiple results are returned
     *
     * BE returns multiple results whereas NL always returns one result.
     * This method is to determine if multiple results were returned.
     *
     * @param array $data
     *
     * @return bool
     */
    private function checkIfRecursive($data)
    {
        return count($data) != count($data, COUNT_RECURSIVE);
    }

    /**
     * Validate elements
     *
     * @param mixed $data
     *
     * @return bool
     */
    private function validateElements($data)
    {
        $success = false;
        array_walk(
            $data,
            function ($result) use (&$success) {
                if (!$this->checkKeys($result)) {
                    $success = false;
                    return;
                }
                $success = true;
            }
        );

        return $success;
    }
}
