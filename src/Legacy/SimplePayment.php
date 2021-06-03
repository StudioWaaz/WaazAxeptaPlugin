<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin\Legacy;

use Payum\Core\Reply\HttpResponse;

/**
 * @author Andde Zudaire <dev2@studiowaaz.com>
 */
final class SimplePayment
{
    /**
     * @var Axepta|object
     */
    private $axepta;

    /**
     * @var string
     */
    private $blowfishKey;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $hmacKey;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $transactionReference;

    /**
     * @var string
     */
    private $automaticResponseUrl;

    /**
     * @var string
     */
    private $targetUrl;

    /**
     * @var string
     */
    private $cancelUrl;

    /**
     * @param Axepta $axepta
     * @param $merchantId
     * @param $amount
     * @param $targetUrl
     * @param $currency
     * @param $transactionReference
     * @param $automaticResponseUrl
     */
    public function __construct(
        Axepta $axepta,
        $merchantId,
        $hmacKey,
        $blowfishKey,
        $amount,
        $targetUrl,
        $currency,
        $transactionReference,
        $automaticResponseUrl,
        $cancelUrl,
        $failureUrl
    )
    {
        $this->automaticResponseUrl = $automaticResponseUrl;
        $this->transactionReference = $transactionReference;
        $this->axepta = $axepta;
        $this->blowfishKey = $blowfishKey;
        $this->merchantId = $merchantId;
        $this->hmacKey = $hmacKey;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->targetUrl = $targetUrl;
        $this->cancelUrl = $cancelUrl;
        $this->failureUrl = $failureUrl;
    }

    public function execute()
    {
        $this->axepta->setFields([
          'merchant_id' => $this->merchantId,
          'trans_id' => $this->generateUniqueTransId(),
          'hmac_key' => $this->hmacKey,
          'blowfish_key' => $this->blowfishKey,
          'payment' => [
              'amount' => $this->amount,
              'currency' => CurrencyNumber::getByCode($this->currency),
          ],
          'order' => [
              'ref' => $this->transactionReference,
              'amount' => $this->amount,
              'currency' => CurrencyNumber::getByCode($this->currency),
          ],
          'url_failure' => $this->failureUrl,
          'url_success' => $this->targetUrl,
          'url_notify' => $this->automaticResponseUrl,
          'url_back' => $this->cancelUrl
        ]);

        // doit générer du html qui redirige vers la banque
        $response = $this->axepta->executeRequest();

        throw new HttpResponse($response);
    }

    private function generateUniqueTransId() {
      $range = range(0, 899999);
      shuffle($range);
      return sprintf('%06d', $range[0]);
    }
}
