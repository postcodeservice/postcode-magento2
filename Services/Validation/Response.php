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
 * to support@postcodeservice.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Services\Validation;

class Response implements ValidationInterface
{
    private $keysToContain = ['success', 'straatnaam', 'woonplaats'];

    /**
     * {@inheritDoc}
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
     * @return array
     */
    public function getKeys()
    {
        return $this->keysToContain;
    }

    /**
     * @param $keys
     */
    public function setKeys($keys)
    {
        $this->keysToContain = $keys;
    }

    /**
     * @param $data
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
     * Importand note : Before using this method, first trigger the checkKeys method.
     *
     * @param $data
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
     * BE returns multiple results whereas NL always returns one result. This method is to determine
     * if multiple results were returned.
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
     * @param $data
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
