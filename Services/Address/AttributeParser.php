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
namespace TIG\Postcode\Services\Address;

class AttributeParser
{
    /** @var null|string  */
    private $tigHousenumber = null;

    /** @var null|string  */
    private $tigHousenumberAddition = null;

    /**
     * Set attribute
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function set(array $attributes)
    {
        if (isset($attributes['tig_housenumber'])) {
            $this->tigHousenumber = $attributes['tig_housenumber'];
        }

        if (isset($attributes['tig_housenumber_addition'])) {
            $this->tigHousenumberAddition = $attributes['tig_housenumber_addition'];
        }

        return $this;
    }

    /**
     * Get House number
     *
     * @return null|string
     */
    public function getTigHousenumber()
    {
        return $this->tigHousenumber;
    }

    /**
     * Get House number Addition
     *
     * @return null|string
     */
    public function getTigHousenumberAddition()
    {
        return $this->tigHousenumberAddition;
    }
}
