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
    /**
     * @var ValidationRequest
     */
    private ValidationRequest $validation;

    /**
     * Request constructor.
     *
     * The IgnoreLine is used because the Magento phpcs checks only on 'Request' part of the class and Request is
     * in as a default Magento Class like the session which can only called as a method argument.
     * But that check is invalid for our validation class.
     *
     * @param ValidationRequest $validation
     */
    public function __construct(ValidationRequest $validation) {
        $this->validation = $validation;
    }

    /**
     * @inheritdoc
     */
    public function setValidationKeys($keys): void
    {
        $this->validation->setKeyFields($keys);
    }

    /**
     * Converts the provided data into a new format.
     *
     * This function first validates the input data. If the data is invalid,
     * it returns false. If the data is valid, it constructs a new array
     * containing only the keys specified by the getRequestFields method of
     * the validation object, with corresponding values from the input data.
     *
     * @param mixed $data The data to convert.
     * @return bool|array Returns the converted data if the input was valid, false otherwise.
     * @inheritDoc
     */
    public function convert($data): bool|array
    {
        // Validate the input data
        if (!$this->validation->validateResponseData($data)) {
            return false;
        }

        // Iterate over each key specified by the getRequestFields method
        $converted = [];
        foreach ($this->validation->getRequestFields() as $key) {
            $converted[$key] = $data[$key];
        }

        // Return the converted data
        return $converted;
    }
}
