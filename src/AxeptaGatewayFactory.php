<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin;

use Waaz\AxeptaPlugin\Action\ConvertPaymentAction;
use Waaz\AxeptaPlugin\Bridge\AxeptaBridgeInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
final class AxeptaGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'axepta',
            'payum.factory_title' => 'Axepta - BNP Paribas',

            'payum.action.convert' => new ConvertPaymentAction(),

            'payum.http_client' => '@waaz.axepta.bridge.axepta_bridge',
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'merchant_id' => '',
                'hmac_key' => '',
                'blowfish_key' => '',
            ];

            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['merchant_id', 'hmac_key', 'blowfish_key'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                /** @var AxeptaBridgeInterface $axepta */
                $axepta = $config['payum.http_client'];

                $axepta->setMerchantId($config['merchant_id']);
                $axepta->setHmacKey($config['hmac_key']);
                $axepta->setBlowfishKey($config['blowfish_key']);

                return $axepta;
            };
        }
    }
}
