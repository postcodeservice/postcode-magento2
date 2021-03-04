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

class CustomerForm
{
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

        $housenumber = $request->getPostValue('tig-housenumber');
        $housenrAddition = $request->getPostValue('tig-housenumber-addition');

        $street = $result['street'];

        if (is_array($street) && array_key_exists(0, $street)) {
            $street = $street[0];
        }

        $result['street'] = [
            $street,
            $housenumber,
            $housenrAddition
        ];

        return $result;
    }
}
