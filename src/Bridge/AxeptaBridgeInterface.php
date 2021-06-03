<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin\Bridge;

use Waaz\AxeptaPlugin\Legacy\SimplePay;

/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
interface AxeptaBridgeInterface
{
    /**
     * @param string $hmacKey
     *
     * @return SimplePay
     */
    public function createAxepta($hmacKey);

    /**
     * @return bool
     */
    public function paymentVerification();

    /**
     * @return bool
     */
    public function isGetMethod();

    /**
     * @return string
     */
    public function getHmacKey();

    /**
     * @param string $accessKey
     */
    public function setHmacKey($hmacKey);

    /**
     * @return string
     */
    public function getMerchantId();

    /**
     * @param string $merchantId
     */
    public function setMerchantId($merchantId);

    /**
     * @return string
     */
    public function getBlowfishKey();

    /**
     * @param string $blowfishKey
     */
    public function setBlowfishKey($blowfishKey);
}
