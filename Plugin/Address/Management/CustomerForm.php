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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Postcode\Plugin\Address\Management;

use Magento\Customer\Model\Metadata\Form;
use Magento\Framework\App\RequestInterface;
use TIG\Postcode\Services\Address\AttributeParser;
use TIG\Postcode\Services\Address\StreetFields;

class CustomerForm
{
    /**
     * @var AttributeParser
     */
    private $attributeParser;

    /**
     * @var StreetFields
     */
    private $streetFields;

    public function __construct(
        AttributeParser $attributeParser,
        StreetFields $streetFields
    ) {
        $this->attributeParser = $attributeParser;
        $this->streetFields = $streetFields;
    }

    /**
     * @param Form             $subject
     * @param                  $result
     * @param RequestInterface $request
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function afterExtractData(Form $subject, $result, RequestInterface $request)
    {
        $country = $request->getPostValue('country_id');

        if ($country !== 'NL' && $country !== 'BE') {
            return $result;
        }

        $attributes = [
            'tig_housenumber' => $request->getPostValue('tig-housenumber'),
            'tig_housenumber_addition', $request->getPostValue('tig-housenumber-addition')
        ];

        $this->attributeParser->set($attributes);

        $result['street'] = $this->streetFields->parse($result['street'], $this->attributeParser);

        return $result;
    }
}
