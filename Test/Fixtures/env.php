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
return  [
    'backend' =>
         [
            'frontName' => 'admin',
        ],
    'install' =>
         [
            'date' => 'Fri, 15 Dec 2017 12:35:34 +0000',
        ],
    'crypt' =>
         [
            'key' => 'a23dea1d65689d5d22571c3e906ca302',
        ],
    'session' =>
         [
            'save' => 'db',
        ],
    'db' =>
         [
            'table_prefix' => '',
            'connection' =>
                 [
                    'default' =>
                         [
                            'host' => 'MAGENTO_DB_HOST',
                            'dbname' => 'MAGENTO_DB_NAME',
                            'username' => 'MAGENTO_DB_USER',
                            'password' => 'MAGENTO_DB_PASS',
                            'active' => '1',
                        ],
                ],
        ],
    'resource' =>
         [
            'default_setup' =>
                 [
                    'connection' => 'default',
                ],
        ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'default',
];
