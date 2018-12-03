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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Services\Converter;

use TIG\Postcode\Exception as PostcodeException;

class Factory
{
    /**
     * @var array|ConverterInterface[]
     */
    private $converters;

    /**
     * Factory constructor.
     *
     * @param ConverterInterface[] $converters
     */
    public function __construct(
        $converters = []
    ) {
        $this->converters = $converters;
    }

    /**
     * @param       $type
     * @param       $data
     * @param array $keys
     *
     * @return mixed
     */
    public function convert($type, $data, $keys = null)
    {
        foreach ($this->converters as $converter) {
            $this->checkImplementation($converter);
        }

        return $this->converter($type, $data, $keys);
    }

    /**
     * @param       $type
     * @param       $data
     * @param array $keys
     *
     * @return mixed
     * @throws PostcodeException
     */
    private function converter($type, $data, $keys)
    {
        if (!isset($this->converters[$type])) {
            // @codingStandardsIgnoreLine
            throw new PostcodeException(__('Could not find type %1 as converter', $type));
        }

        if ($keys) {
            $this->converters[$type]->setValidationKeys($keys);
        }

        return $this->converters[$type]->convert($data);
    }

    /**
     * @param $converter
     *
     * @throws PostcodeException
     */
    private function checkImplementation($converter)
    {
        if (!array_key_exists(ConverterInterface::class, class_implements($converter))) {
            // @codingStandardsIgnoreLine
            throw new PostcodeException(__('Class is not an implementation of %1', ConverterInterface::class));
        }
    }
}
