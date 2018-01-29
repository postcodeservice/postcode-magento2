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
namespace TIG\Postcode\Config\Provider;

use Magento\Checkout\Model\ConfigProviderInterface;
use TIG\Postcode\Config\CheckoutConfiguration\CheckoutConfigurationInterface;
use TIG\Postcode\Exception;

class CheckoutConfiguration implements ConfigProviderInterface
{
    /**
     * @var array
     */
    private $postcodeConfiguration;

    /**
     * CheckoutConfiguration constructor.
     *
     * @param array $postcodeConfiguration
     */
    public function __construct(
        $postcodeConfiguration = []
    ) {
        $this->postcodeConfiguration = $postcodeConfiguration;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->postcodeConfiguration as $key => $configuration) {
            $this->checkImplementation($configuration, $key);
            $config[$key] = $configuration->getValue();
        }

        return [
            'postcode' => $config
        ];
    }

    /**
     * @param $configuration
     * @param $key
     *
     * @throws Exception
     */
    private function checkImplementation($configuration, $key)
    {
        if (!($configuration instanceof CheckoutConfigurationInterface)) {
            throw new Exception(__('%1 is not an implementaiton of %2', $key, CheckoutConfigurationInterface::class));
        }
    }
}
