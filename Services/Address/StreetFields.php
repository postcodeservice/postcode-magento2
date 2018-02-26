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
use TIG\Postcode\Config\Source\Parser;
use Magento\Quote\Api\Data\AddressExtensionInterface;

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
     * @param                           $street
     * @param AddressExtensionInterface $attributes
     *
     * @return mixed|string
     */
    public function parse($street, AddressExtensionInterface $attributes)
    {
        $merger = $this->parseConfiguration->getStreetMerging();
        if ($merger == Parser::ONE_STREETFIELD) {
            $street = $this->mergeToOneField($street, $attributes);
        }

        if ($merger == Parser::TWO_STREETFIELDS) {
            $street = $this->mergeToTowFields($street, $attributes);
        }

        if ($merger == Parser::THREE_STREETFIELDS) {
            $street = $this->mergeToThreeFields($street, $attributes);
        }

        return $street;
    }

    /**
     * @param                           $street
     * @param AddressExtensionInterface $attributes
     *
     * @return string
     */
    private function mergeToOneField($street, AddressExtensionInterface $attributes)
    {
        return implode(' ', [
            $street[0],
            $attributes->getTigHousenumber(),
            $attributes->getTigHousenumberAddition()
        ]);
    }

    /**
     * @param                           $street
     * @param AddressExtensionInterface $attributes
     *
     * @return mixed
     */
    private function mergeToTowFields($street, AddressExtensionInterface $attributes)
    {
        $street[1] = implode(' ', [
            $attributes->getTigHousenumber(),
            $attributes->getTigHousenumberAddition()
        ]);

        return $street;
    }

    /**
     * @param                           $street
     * @param AddressExtensionInterface $attributes
     *
     * @return mixed
     */
    private function mergeToThreeFields($street, AddressExtensionInterface $attributes)
    {
        $street[1] = $attributes->getTigHousenumber();
        $street[2] = $attributes->getTigHousenumberAddition();

        return $street;
    }
}
