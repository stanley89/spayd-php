<?php
/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

 namespace Spayd\String;

/**
 *
 * @author petrdvorak
 */
class SpaydPayment {

    private static $protocolVersion = "1.0";

    /**
     * Escapes the payment string value in a correct way
     * @param originalString The original non-escaped value
     * @return A string with percent encoded characters
     */
    public static function escapeDisallowedCharacters(String $originalString) {
        $working = "";
        for ($i = 0; $i < strlen($originalString); $i++) {
            if (ord($originalString[$i]) > 127) { // escape non-ascii characters
                $working .= urlencode($originalString[$i]);
            } else {
                if ($originalString[$i] == '*') { // star is a special character for the SPAYD format
                    $working .= "%2A";
                } else if ($originalString[$i] == '+') { // plus is a special character for URL encode
                    $working .= "%2B";
                } else if ($originalString[$i] == '%') { // percent is an escape character
                    $working .= "%25";
                } else {
                    $working .= $originalString[$i]; // ascii characters may be used as expected
                }
            }
        }
        return $working;
    }

    /**
     * Uses parameters and extended parameters to build a SPAYD string.
     * @param parameters The basic parameters, such as account number, amount, currency, ...
     * @param extendedParameters Extended (proprietary) attributes, prefixed with X-
     * @param transliterateParams Flag indicating if the characters should be transliterated to ASCII chars only
     * @return A SPAYD String
     */
    public static function paymentStringFromAccount(SpaydPaymentAttributes $parameters, SpaydExtendedPaymentAttributeMap $extendedParameters = null, $transliterateParams) {
        $paymentString = "SPD*" . self::$protocolVersion . "*";
        if ($parameters->getBankAccount()->getIBAN() != null) {
            $paymentString .= "ACC:" . $parameters->getBankAccount()->getIBAN();
            if ($parameters->getBankAccount()->getBIC() != null) {
                $paymentString .= "+" . $parameters->getBankAccount()->getBIC();
            }
            $paymentString .= "*";
        }
        if ($parameters->getAlternateAccounts() != null && !empty($parameters->getAlternateAccounts())) {
            $paymentString .= "ALT-ACC:";
            $firstItem = true;
            foreach ($parameters->getAlternateAccounts() as $bankAccount) {
                if (!$firstItem) {
                    $paymentString .= ",";
                } else {
                    $firstItem = false;
                }
                $paymentString .= $bankAccount->getIBAN();
                if ($bankAccount->getBIC() != null) {
                    $paymentString .= "+" . $bankAccount->getBIC();
                }
            }
            $paymentString .= "*";
        }
        if ($parameters->getAmount() != null) {
            $paymentString .= "AM:" . $parameters->getAmount() . "*";
        }
        if ($parameters->getCurrency() != null) {
            $paymentString .= "CC:" . $parameters->getCurrency() . "*";
        }
        if ($parameters->getSendersReference() != null) {
            $paymentString .= "RF:" . $parameters->getSendersReference() . "*";
        }
        if ($parameters->getRecipientName() != null) {
            if ($transliterateParams) {
                $paymentString .= "RN:"
                        . SpaydPayment::escapeDisallowedCharacters(strtoupper(iconv("UTF-8", "ASCII//TRANSLIT", $parameters->getRecipientName())))
                        . "*";
            } else {
                $paymentString .= "RN:"
                        . SpaydPayment::escapeDisallowedCharacters($parameters->getRecipientName())
                        . "*";
            }
        }
        if ($parameters->getDate() != null) {
            $paymentString .= "DT:" . $parameters->getDate()->format("Ymd") . "*";
        }
        if ($parameters->getMessage() != null) {
            if ($transliterateParams) {
                $paymentString .= "MSG:"
                        . SpaydPayment::escapeDisallowedCharacters(strtoupper(iconv("UTF-8", "ASCII//TRANSLIT", $parameters->getMessage())))
                        . "*";
            } else {
                $paymentString .= "MSG:"
                        . SpaydPayment::escapeDisallowedCharacters($parameters->getMessage())
                        . "*";
            }
        }
        if ($parameters->getNotificationType() != null) {
            if ($parameters->getNotificationType() == SpaydPaymentAttributes::PAYMENT_NOTIFICATION_TYPE_EMAIL) {
                $paymentString .= "NT:E*";
            } else if ($parameters->getNotificationType() == SpaydPaymentAttributes::PAYMENT_NOTIFICATION_TYPE_PHONE) {
                $paymentString .= "NT:P*";
            }
        }
        if ($parameters->getNotificationValue() != null) {
            $paymentString .= "NTA:" + SpaydPayment::escapeDisallowedCharacters($parameters->getNotificationValue()) + "*";
        }
        if ($extendedParameters != null && !$extendedParameters->isEmpty()) {
            $paymentString .= $extendedParameters->toExtendedParams();
        }
        return substr($paymentString, 0, strlen($paymentString) - 1);
    }
}
