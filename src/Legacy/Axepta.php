<?php

namespace Waaz\AxeptaPlugin\Legacy;

/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
class Axepta
{
    private $projectDir;

    /**
     * @var array
     */
    private $mandatoryFields = array(
        'merchant_id' => '',
        'trans_id' => '',
        'blowfish_key' => '',
        'payment' => [
            'amount' => '',
            'currency' => '',
        ],
        'url_success' => '',
        'url_failure' => '',
        'url_notify' => '',
        'url_back' => ''
    );

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $token;

    public function __construct($key)
    {
        $this->key = $key;

    }

    /**
     * @param $fields
     * @return $this
     */
    public function setFields($fields)
    {

        foreach ($fields as $field => $value){
            if (is_array($value)){
                foreach ($value as $field2 => $value2){
                    if (empty($this->mandatoryFields[$field2]))
                        $this->mandatoryFields[$field][$field2] = $value2;
                }
            }else{
                if (empty($this->mandatoryFields[$field]))
                    $this->mandatoryFields[$field] = $value;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->mandatoryFields;
    }

    public function executeRequest()
    {

        $request = $this->getRequest();
        // create an instance

        $paymentRequest = new AxeptaSDK($request['hmac_key']);
		    $paymentRequest->setCryptKey($request['blowfish_key']);

        $paymentRequest->setUrl(AxeptaSDK::PAYSSL);
    		$paymentRequest->setMerchantID($request['merchant_id']);
    		$paymentRequest->setTransID($request['trans_id']);
    		$paymentRequest->setAmount($request['payment']['amount']);
    		$paymentRequest->setCurrency($request['order']['currency']);
    		$paymentRequest->setRefNr($request['order']['ref']);

    		$paymentRequest->setURLSuccess($request['url_success']);
    		$paymentRequest->setURLFailure($request['url_failure']);
    		$paymentRequest->setURLNotify($request['url_notify']);

    		$paymentRequest->setURLBack($request['url_back']);
    		$paymentRequest->setReponse('encrypt');
    		$paymentRequest->setLanguage('fr_FR');

        $paymentRequest->setDebug(true);

    		// check your data
    		$paymentRequest->validate();

    		// compute
    		$mac = $paymentRequest->getShaSign() ; 		// run HMAC hash
    		$data = $paymentRequest->getBfishCrypt();	// run Crypt & retrieve Data
    		$len = $paymentRequest->getLen();		// retrieve Crypt length

        $return =    "<html><body><form name=\"redirectForm\" method=\"GET\" action=\"" . $paymentRequest->getUrl() . "\">" .
  			 "<input type=\"hidden\" name=\"MerchantID\" value=\"". $paymentRequest->getMerchantID() . "\">" .
  			 "<input type=\"hidden\" name=\"Len\" value=\"". $paymentRequest->getLen() . "\">" .
  			 "<input type=\"hidden\" name=\"Data\" value=\"". $paymentRequest->getBfishCrypt() . "\">" .
  			 "<input type=\"hidden\" name=\"URLNotify\" value=\"". $paymentRequest->getURLNotify() . "\">" .
  			 "<input type=\"hidden\" name=\"URLBack\" value=\"". $paymentRequest->getURLBack() . "\">" .
  			 "<input type=\"hidden\" name=\"Amount\" value=\"". $paymentRequest->getAmount()/100 . "\">" .
  			 "<input type=\"hidden\" name=\"TransID\" value=\"". $paymentRequest->getTransID() . "\">" .
  			 "<noscript><input type=\"submit\" name=\"Go\" value=\"Click to continue\"/></noscript> </form>" .
  			 "<script type=\"text/javascript\">document.redirectForm.submit(); </script>" .
  			 "</body></html>";

        return $return;


    }

    public function getPaymentDetails()
    {

      $request = $this->getRequest();
      // create an instance
      $axeptaSDK = new AxeptaSDK($this->key);
      $paymentResponse = new Axepta($Your_HMAC);

    	$axeptaSDK->setCryptKey($Your_CRYPTKEY);
    	$axeptaSDK->setResponse($_GET);

    	if($axeptaSDK->isValid() && $axeptaSDK->isSuccessful()) {
        $response = $axeptaSDK->getStatus();
    	} else {
        $response = $axeptaSDK->getStatus();
    	}

      return $response;
    }
}
