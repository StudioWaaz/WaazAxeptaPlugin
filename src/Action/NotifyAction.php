<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin\Action;

use Waaz\AxeptaPlugin\Bridge\AxeptaBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\Notify;
use Sylius\Component\Payment\PaymentTransitions;
use Webmozart\Assert\Assert;
use SM\Factory\FactoryInterface;

/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var AxeptaBridgeInterface
     */
    private $axeptaBridge;

    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);

        if ($this->axeptaBridge->paymentVerification()) {

          $accessKey = $this->axeptaBridge->getAccessKey();

          $axepta = $this->axeptaBridge->createAxepta($accessKey);

          $axepta->setFields([
            'merchant_id' => $this->axeptaBridge->getMerchantId(),
            'hmac_key' => $this->axeptaBridge->getHmacKey()
          ]);
          $params['token'] = $this->axeptaBridge->paymentVerification();

          $responsePayment = $axepta->getPaymentDetails();

          if($responsePayment === 'OK'){

              /** @var PaymentInterface $payment */
              $payment = $request->getFirstModel();

              Assert::isInstanceOf($payment, PaymentInterface::class);

              $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->apply(PaymentTransitions::TRANSITION_COMPLETE);

          }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setApi($axeptaBridge)
    {
        if (!$axeptaBridge instanceof AxeptaBridgeInterface) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->axeptaBridge = $axeptaBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject
        ;
    }
}
