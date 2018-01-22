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
namespace TIG\Postcode\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use TIG\Postcode\Webservices\Endpoints\GetAddress;
use TIG\Postcode\Services\Converter\Factory;

class Service extends Action
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var Factory
     */
    private $converter;

    /**
     * @var GetAddress
     */
    private $getAddress;

    /**
     * Service constructor.
     *
     * @param Context     $context
     * @param JsonFactory $jsonFactory
     * @param Factory     $converterFactory
     * @param GetAddress  $getAddress
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Factory $converterFactory,
        GetAddress $getAddress
    ) {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory;
        $this->converter   = $converterFactory;
        $this->getAddress  = $getAddress;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $params = $this->converter->convert('request', $params);
        if (!$params) {
            return $this->returnJson([
                'success' => false,
                'error'   => __('Request validation failed')
            ]);
        }

        $this->getAddress->setRequestData($params);
        $result = $this->getAddress->call();
        if (!$result) {
            return $this->returnJson([
                'success' => false,
                'error'   => __('Response validation failed')
            ]);
        }

        return $this->returnJson($result);
    }

    /**
     * @param $data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function returnJson($data)
    {
        $response = $this->jsonFactory->create();
        return $response->setData($data);
    }
}
