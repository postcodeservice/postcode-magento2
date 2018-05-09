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
namespace TIG\Postcode\Services\Address;

use TIG\Postcode\Config\Provider\ParserConfiguration;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use TIG\Postcode\Services\Address\AttributeParser;

class StreetFields
{
    /**
     * @var ParserConfiguration
     */
    private $parseConfiguration;

    /**
     * StreetFields constructor.
     *
     * @param ParserConfiguration $parserConfiguration
     */
    public function __construct(
        ParserConfiguration $parserConfiguration
    ) {
        $this->parseConfiguration = $parserConfiguration;
    }

    /**
     * @param                                           $street
     * @param AddressExtensionInterface|AttributeParser $attributes
     *
     * @return mixed|string
     */
    public function parse($street, $attributes)
    {
        $merger = $this->parseConfiguration->getMergeType();
        if ($merger == ParserConfiguration::PARSE_TYPE_ONE) {
            $street = $this->mergeTypeOne($street, $attributes);
        }

        if ($merger == ParserConfiguration::PARSE_TYPE_TWO) {
            $street = $this->mergeTypeTwo($street, $attributes);
        }

        if ($merger == ParserConfiguration::PARSE_TYPE_THREE) {
            $street = $this->mergeTypeThree($street, $attributes);
        }

        if ($merger == ParserConfiguration::PARSE_TYPE_FOUR) {
            $street = $this->mergeTypeFour($street, $attributes);
        }

        return $street;
    }

    /**
     * @param                                           $street
     * @param AddressExtensionInterface|AttributeParser $attributes
     *
     * @return string
     */
    private function mergeTypeOne($street, $attributes)
    {
        $street[0] = implode(' ', [
            $street[0],
            $attributes->getTigHousenumber(),
            $attributes->getTigHousenumberAddition()
        ]);

        return $street;
    }

    /**
     * @param                                           $street
     * @param AddressExtensionInterface|AttributeParser $attributes
     *
     * @return mixed
     */
    private function mergeTypeTwo($street, $attributes)
    {
        $street[1] = implode(' ', [
            $attributes->getTigHousenumber(),
            $attributes->getTigHousenumberAddition()
        ]);

        return $street;
    }

    /**
     * @param                                           $street
     * @param AddressExtensionInterface|AttributeParser $attributes
     *
     * @return mixed
     */
    private function mergeTypeThree($street, $attributes)
    {
        $street[1] = $attributes->getTigHousenumber();
        $street[2] = $attributes->getTigHousenumberAddition();

        return $street;
    }

    /**
     * @param                                           $street
     * @param AddressExtensionInterface|AttributeParser $attributes
     *
     * @return mixed
     */
    private function mergeTypeFour($street, $attributes)
    {
        $street[1] = '';
        $street[2] = implode(' ', [
            $attributes->getTigHousenumber(),
            $attributes->getTigHousenumberAddition()
        ]);

        return $street;
    }
}
