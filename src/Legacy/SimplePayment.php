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
        $failureUrl,
        $ref
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
        $this->ref = $ref;
    }

    public function execute()
    {
        $this->axepta->setCryptKey($this->blowfishKey);
        $this->axepta->setUrl(Axepta::PAYSSL);
        $this->axepta->setMerchantID($this->merchantId);
		$this->axepta->setTransID($this->generateUniqueTransId());
		$this->axepta->setAmount($this->amount);
		$this->axepta->setCurrency($this->currency);
		$this->axepta->setRefNr($this->transactionReference);
		
		$this->axepta->setURLSuccess($this->targetUrl);    
		$this->axepta->setURLFailure($this->failureUrl);    
		$this->axepta->setURLNotify($this->automaticResponseUrl); 
		
		$this->axepta->setURLBack($this->cancelUrl);    
		$this->axepta->setReponse('encrypt');    
		$this->axepta->setLanguage('fr');
        $this->axepta->setOrderDesc('Commande '.$this->ref);
		
		// check your data
		$this->axepta->validate();
	
		// compute
		$mac = $this->axepta->getShaSign() ; 		// run HMAC hash
		$data = $this->axepta->getBfishCrypt();	// run Crypt & retrieve Data
		$len = $this->axepta->getLen();		// retrieve Crypt length
        
        $response = "<html><body><form name=\"redirectForm\" method=\"GET\" action=\"" . $this->axepta->getUrl() . "\">" .
  			 "<input type=\"hidden\" name=\"MerchantID\" value=\"". $this->axepta->getMerchantID() . "\">" .
  			 "<input type=\"hidden\" name=\"Len\" value=\"". $this->axepta->getLen() . "\">" .
  			 "<input type=\"hidden\" name=\"Data\" value=\"". $this->axepta->getBfishCrypt() . "\">" .
  			 "<input type=\"hidden\" name=\"URLNotify\" value=\"". $this->axepta->getURLNotify() . "\">" .
  			 "<input type=\"hidden\" name=\"URLBack\" value=\"". $this->axepta->getURLBack() . "\">" .
  			 "<input type=\"hidden\" name=\"Amount\" value=\"". $this->axepta->getAmount()/100 . "\">" .
  			 "<input type=\"hidden\" name=\"TransID\" value=\"". $this->axepta->getTransID() . "\">" .
  			 "<noscript><input type=\"submit\" name=\"Go\" value=\"Click to continue\"/></noscript> </form>" .
  			 "<script type=\"text/javascript\">document.redirectForm.submit(); </script>" .
  			 "</body></html>";
        
        throw new HttpResponse($response);
        
    }

    private function generateUniqueTransId() {
      $range = range(0, 899999);
      shuffle($range);
      return sprintf('%06d', $range[0]);
    }
}
