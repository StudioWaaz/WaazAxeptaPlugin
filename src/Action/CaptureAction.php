<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin\Action;

use Waaz\AxeptaPlugin\Legacy\SimplePayment;
use Waaz\AxeptaPlugin\Bridge\AxeptaBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;
use Payum\Core\Payum;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    private Payum $payum;

    private AxeptaBridgeInterface $axeptaBridge;

    private UrlGeneratorInterface $router;

    /**
     * @param Payum $payum
     */
    public function __construct(Payum $payum, AxeptaBridgeInterface $axeptaBridge, UrlGeneratorInterface $router)
    {
        $this->payum = $payum;
        $this->axeptaBridge = $axeptaBridge;
        $this->router = $router;
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
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $transactionReference = isset($model['transactionReference']) ? $model['transactionReference'] : null;

        if ($transactionReference !== null && $model['status'] === PaymentInterface::STATE_COMPLETED) {
            return;
        }

        $notifyToken = $this->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $hmacKey = $this->axeptaBridge->getHmacKey();

        $axepta = $this->axeptaBridge->createAxepta($hmacKey);

        $merchantId = $this->axeptaBridge->getMerchantId();
        $blowfishKey = $this->axeptaBridge->getBlowfishKey();

        $automaticResponseUrl = $notifyToken->getTargetUrl();

        $currencyCode = $payment->getCurrencyCode();

        $targetUrl = $this->router->generate('sylius_shop_order_thank_you', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->router->generate('sylius_shop_order_show', ['tokenValue' => $payment->getOrder()->getTokenValue()], UrlGeneratorInterface::ABSOLUTE_URL);
        $failureUrl = $this->router->generate('sylius_shop_order_show', ['tokenValue' => $payment->getOrder()->getTokenValue()], UrlGeneratorInterface::ABSOLUTE_URL);

        //$targetUrl = $request->getToken()->getTargetUrl();
        $amount = $payment->getAmount();

        $transactionReference = $payment->getOrder()->getId();

        $ref = $payment->getOrder()->getNumber();

        $model['transactionReference'] = $transactionReference;

        $simplePayment = new SimplePayment(
            $axepta,
            $merchantId,
            $hmacKey,
            $blowfishKey,
            $amount,
            $targetUrl,
            $currencyCode,
            $transactionReference,
            $automaticResponseUrl,
            $cancelUrl,
            $failureUrl,
            $ref
        );

        $request->setModel($model);

        $simplePayment->execute();
    }

    /**
     * @param string $gatewayName
     * @param object $model
     *
     * @return TokenInterface
     */
    private function createNotifyToken($gatewayName, $model)
    {
        return $this->payum->getTokenFactory()->createNotifyToken(
            $gatewayName,
            $model
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
