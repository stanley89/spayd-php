<?php
/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

namespace Spayd\Model\Error;

/**
 *
 * @author petrdvorak
 */
class SpaydValidationError {

    private $errorCode;
    private $errorDescription;

    const ERROR_INVALID_CHARSET              = "ERROR_INVALID_CHARSET";
    const ERROR_NOT_SPAYD                    = "ERROR_NOT_SPAYD";
    const ERROR_INVALID_STRUCTURE            = "ERROR_INVALID_STRUCTURE";
    const ERROR_INVALID_AMOUNT               = "ERROR_INVALID_AMOUNT";
    const ERROR_INVALID_CURRENCY             = "ERROR_INVALID_CURRENCY";
    const ERROR_INVALID_SENDERS_REFERENCE    = "ERROR_INVALID_SENDERS_REFERENCE";
    const ERROR_INVALID_RECIPIENT_NAME       = "ERROR_INVALID_RECIPIENT_NAME";
    const ERROR_INVALID_DUE_DATE             = "ERROR_INVALID_DUE_DATE";
    const ERROR_INVALID_PAYMENT_TYPE         = "ERROR_INVALID_PAYMENT_TYPE";
    const ERROR_INVALID_MESSAGE              = "ERROR_INVALID_MESSAGE";
    const ERROR_INVALID_KEY_FOUND            = "ERROR_INVALID_KEY_FOUND";
    const ERROR_INVALID_IBAN                 = "ERROR_INVALID_IBAN";
    const ERROR_INVALID_ALTERNATE_IBAN       = "ERROR_INVALID_ALTERNATE_IBAN";
    const ERROR_IBAN_NOT_FOUND               = "ERROR_IBAN_NOT_FOUND";
    const ERROR_INVALID_NOTIFICATION_TYPE    = "ERROR_INVALID_NOTIFICATION_TYPE";
    const ERROR_REQUEST_GENERIC              = "ERROR_REQUEST_GENERIC";

    public function getErrorCode() {
        return $this->errorCode;
    }

    public function setErrorCode(String $errorCode) {
        $this->errorCode = $errorCode;
    }

    public function getErrorDescription() {
        return $this->errorDescription;
    }

    public function setErrorDescription(String $errorDescription) {
        $this->errorDescription = $errorDescription;
    }

}
