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
namespace TIG\Postcode\Services\Converter;

use TIG\Postcode\Services\Validation\Request as ValidationRequest;

class Request implements ConverterInterface
{
    private $validation;

    /**
     * Request constructor.
     *
     * The IgnoreLine is used because the Magento phpcs checks only on 'Request' part of the class and Request is
     * in as an default Magento Class like the session which can only called as a method argument.
     * But that check is invalid for our validation class.
     *
     * @param ValidationRequest $validation
     */
    public function __construct(
        // @codingStandardsIgnoreLine
        ValidationRequest $validation
    ) {
        $this->validation = $validation;
    }

    /**
     * @param $keys
     */
    public function setValidationKeys($keys)
    {
        $this->validation->setKeys($keys);
    }

    /**
     * {@inheritDoc}
     */
    public function convert($data)
    {
        if (!$this->validation->validate($data)) {
            return false;
        }

        $converted = [];
        foreach ($this->validation->getKeys() as $key) {
            $converted[$key] = $data[$key];
        }

        return $converted;
    }
}
