<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\fpCreditCard\Gateway\Http\Client\ClientMock;
use Magento\Framework\View\Asset\Source;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'fpCreditCard';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     * @param CcConfig $ccConfig
     * @param Source $assetSource
     * @var string[]
     */
    
    protected $_methodCode = 'Magentopayment';
   
    public function __construct(
        \Magento\Payment\Model\CcConfig $ccConfig,
        Source $assetSource
    ) {
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ]
                ],
                'fpCreditCard' => [
                    'availableTypes' => [$this->_methodCode => $this->ccConfig->getCcAvailableTypes()],
                    'months' => [$this->_methodCode => $this->ccConfig->getCcMonths()],
                    'years' => [$this->_methodCode => $this->ccConfig->getCcYears()],
                    'hasVerification' => $this->ccConfig->hasVerification(),
                ]
            ]
        ];
    }
}
