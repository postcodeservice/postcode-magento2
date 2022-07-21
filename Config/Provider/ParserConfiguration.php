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
namespace TIG\Postcode\Config\Provider;

class ParserConfiguration extends AbstractConfigProvider
{
    public const XPATH_STREETMERGING      = 'tig_postcode/configuration/fieldparsing_street';
    public const XPATH_HOUSENUMBERMERGING = 'tig_postcode/configuration/fieldparsing_housenumber';
    public const XPATH_ADDITIONMERGING    = 'tig_postcode/configuration/fieldparsing_addition';

    public const DEFAULT_PARSELINE = 1;

    public const PARSE_TYPE_ONE   = 1;
    public const PARSE_TYPE_TWO   = 2;
    public const PARSE_TYPE_THREE = 3;
    public const PARSE_TYPE_FOUR  = 4;

    /**
     * @var int[]
     */
    private $parslines = [
        '111' => 1, // All on one line.
        '122' => 2, // Number and addition on line two.
        '123' => 3, // Number on line two and addition on three.
        '133' => 4, // Number and addition on line three.
    ];

    /**
     * Get merge type via store ID
     *
     * @param string|int|null $store
     *
     * @return int|mixed
     */
    public function getMergeType($store = null)
    {
        $parseLine = $this->getLineMergingFormat($store);
        if (!key_exists($parseLine, $this->parslines)) {
            return static::DEFAULT_PARSELINE;
        }

        return $this->parslines[$parseLine];
    }

    /**
     * Get street merging configuration via store ID
     *
     * @param string|int|null $store
     *
     * @return mixed
     */
    public function getStreetMerging($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_STREETMERGING, $store);
    }

    /**
     * Get house number merging configuration via store ID
     *
     * @param string|int|null $store
     *
     * @return mixed
     */
    public function getHousenumberMerging($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_HOUSENUMBERMERGING, $store);
    }

    /**
     * Get house number addition merging configuration via store ID
     *
     * @param string|int|null $store
     *
     * @return mixed
     */
    public function getAdditionMerging($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_ADDITIONMERGING, $store);
    }

    /**
     * Get line merging format via store ID
     *
     * @param string|int|null $store
     *
     * @return string
     */
    private function getLineMergingFormat($store = null)
    {
        $parsing = [
            $this->getStreetMerging($store),
            $this->getHousenumberMerging($store),
            $this->getAdditionMerging($store),
        ];

        return implode('', $parsing);
    }
}
