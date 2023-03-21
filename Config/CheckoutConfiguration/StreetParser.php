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
namespace TIG\Postcode\Config\CheckoutConfiguration;

use TIG\Postcode\Config\Provider\ModuleConfiguration;
use TIG\Postcode\Config\Provider\ParserConfiguration;

class StreetParser implements CheckoutConfigurationInterface
{
    /**
     * @var ModuleConfiguration
     */
    private $moduleConfiguration;

    /**
     * @var ParserConfiguration
     */
    private $parserConfiguration;

    /**
     * IsPostcodeCheckActive constructor.
     *
     * @param ParserConfiguration $parserConfiguration
     */
    public function __construct(
        ParserConfiguration $parserConfiguration
    ) {
        $this->parserConfiguration = $parserConfiguration;
    }

    /**
     * Get value and return the housenumber information
     *
     * @return mixed
     */
    public function getValue()
    {
        return [
            'housenumberParsing'         => $this->parserConfiguration->getHousenumberMerging(),
            'housenumberAdditionParsing' => $this->parserConfiguration->getAdditionMerging()
        ];
    }
}
