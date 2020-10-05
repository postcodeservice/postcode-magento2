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

use TIG\Postcode\Exception as PostcodeException;

class Factory
{
    /**
     * @var array|ValidationInterface[]
     */
    private $validators;

    /**
     * Factory constructor.
     *
     * @param ValidationInterface[] $validators
     */
    public function __construct(
        $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @param $type
     * @param $data
     *
     * @return bool|mixed
     */
    public function validate($type, $data)
    {
        foreach ($this->validators as $validator) {
            $this->checkImplementation($validator);
        }

        return $this->validator($type, $data);
    }

    /**
     * @param $type
     * @param $data
     *
     * @return bool|mixed
     * @throws PostcodeException
     */
    private function validator($type, $data)
    {
        if (!isset($this->validators[$type])) {
            // @codingStandardsIgnoreLine
            throw new PostcodeException(__('Could not find type %1 as validator', $type));
        }

        return $this->validators[$type]->validate($data);
    }

    /**
     * @param $validator
     *
     * @throws PostcodeException
     */
    private function checkImplementation($validator)
    {
        if (!array_key_exists(ValidationInterface::class, class_implements($validator))) {
            // @codingStandardsIgnoreLine
            throw new PostcodeException(__('Class is not an implementation of %1', ValidationInterface::class));
        }
    }
}
