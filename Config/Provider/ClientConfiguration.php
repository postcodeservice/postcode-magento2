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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\Manager;

class ClientConfiguration extends AbstractConfigProvider
{
    const XPATH_CONFIGURATION_CLIENT_ID = 'tig_postcode/configuration/client_id';
    const XPATH_CONFIGURATION_API_KEY   = 'tig_postcode/configuration/api_key';

    /**
     * @var ModuleConfiguration
     */
    private $moduleConfiguration;

    /**
     * ClientConfiguration constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager              $moduleManager
     * @param Encryptor            $crypt
     * @param ModuleConfiguration  $moduleConfiguration
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Encryptor $crypt,
        ModuleConfiguration $moduleConfiguration
    ) {
        parent::__construct($scopeConfig, $moduleManager, $crypt);
        $this->moduleConfiguration = $moduleConfiguration;
    }

    /**
     * Returns the client id bases on the modus of the extension.
     *
     * @param null $store
     *
     * @return mixed
     */
    public function getClientId($store = null)
    {
        $modusXpath = $this->getModusXpath(static::XPATH_CONFIGURATION_CLIENT_ID, $store);
        return $this->getConfigFromXpath($modusXpath, $store);
    }

    /**
     * Returns the decrypted API key.
     *
     * @param null $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        $modusXpath = $this->getModusXpath(static::XPATH_CONFIGURATION_API_KEY, $store);
        $key = $this->getConfigFromXpath($modusXpath, $store);

        return $this->crypt->decrypt($key);
    }

    /**
     * Gets the xpath bases on de module modus.
     *
     * @param      $xpath
     * @param null $store
     *
     * @return string
     */
    public function getModusXpath($xpath, $store = null)
    {
        if ($this->moduleConfiguration->isModusTest($store)) {
            $xpath .= '_test';
        }

        return $xpath;
    }
}
