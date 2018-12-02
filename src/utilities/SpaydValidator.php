<?php
/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

namespace Spayd\Utilities;
use Spayd\Model\Error\SpaydValidationError;

/**
 *
 * @author petrdvorak
 */
class SpaydValidator {

    private static $allowedKeys = [
        "ACC",
        "ALT-ACC",
        "AM",
        "CC",
        "RF",
        "RN",
        "DT",
        "PT",
        "MSG",
        "NT",
        "NTA",
        "CRC32"];

    public static function validatePaymentString(String $paymentString) {
        $errors = [];

        if (!self::checkISO88591($paymentString)) { // check encoding
            $error = new SpaydValidationError();
            $error->setErrorCode(SpaydValidationError::ERROR_INVALID_CHARSET);
            $error->setErrorDescription("Invalid charset - only ISO-8859-1 characters must be used");
            $errors[] = $error;
            return $errors;
        }

        if (!preg_match("/^SPD\\*[0-9]+\\.[0-9]+\\*.*/", $paymentString)) {
            $error = new SpaydValidationError();
            $error->setErrorCode(SpaydValidationError::ERROR_NOT_SPAYD);
            $error->setErrorDescription('Invalid data prefix - SPD*{$VERSION}* expected.');
            $errors[] = $error;
            return $errors;
        }

        if (!preg_match('/^SPD\\*[0-9]+\\.[0-9]+(\\*[0-9A-Z $%*+-.]+:[^\\*]+)+\\*?$/', $paymentString)) {
            $error = new SpaydValidationError();
            $error->setErrorCode(SpaydValidationError::ERROR_INVALID_STRUCTURE);
            $error->setErrorDescription("Payment String code didn't pass the basic regexp validation.");
            $errors[] = $error;
            return $errors;
        }

        $components = explode("*", $paymentString);

        $ibanFound = false;

        // skip the header and version => start with 2
        for ($i = 2; $i < count($components); $i++) {
            $index = strpos($components[$i], ":");
            if ($index === false) { // missing pair between two stars ("**")
                $error = new SpaydValidationError();
                $error->setErrorCode(SpaydValidationError::ERROR_INVALID_STRUCTURE);
                $error->setErrorDescription("Payment String code didn't pass the basic regexp validation.");
                $errors[] = $error;
                continue;
            }
            $key = substr($components[$i], 0, $index);

            $value = urldecode(str_replace("+", "%2B", substr($components[$i], $index+1)));

            if (!in_array($key, self::$allowedKeys) && substr($key,0,2) == "X-") {
                $error = new SpaydValidationError();
                $error->setErrorCode(SpaydValidationError::ERROR_INVALID_KEY_FOUND);
                $error->setErrorDescription("Unknown key detected. Use 'X-' prefix to create your own key.");
                $errors[] = $error;
                continue;
            }

            if ($key == "ACC") {
                $ibanFound = true;
                if (!preg_match("/^([A-Z]{2,2}[0-9]{2,2}[A-Z0-9]+)(\\+([A-Z0-9]+))?$/", $value)) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_IBAN);
                    $error->setErrorDescription("IBAN+BIC pair was not in the correct format.".$value);
                    $errors[] = $error;
                }
            } else if ($key == "ALT-ACC") {
                $ibanFound = true;
                if (!preg_match("/^([A-Z]{2,2}[0-9]{2,2}[A-Z0-9]+)(\\+([A-Z0-9]+))?(,([A-Z]{2,2}[0-9]+)(\\+([A-Z0-9]+))?)*$/", $value)) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_ALTERNATE_IBAN);
                    $error->setErrorDescription("Alternate accounts are not properly formatted - should be IBAN+BIC list with items separated by ',' character.".$value);
                    $errors[] = $error;
                }
            } else if ($key == "AM") {
                if (!preg_match("/^[0-9]{0,10}(\\.[0-9]{0,2})?$/",$value) || $value == ".") {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_AMOUNT);
                    $error->setErrorDescription("Amount must be a number with at most 2 decimal digits.");
                    $errors[] = $error;
                }
            } else if ($key == "CC") {
                $currencies = \Money\Currencies\ISOCurrencies();
                if (!$currencies->contains(new \Money\Currency($value))) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_CURRENCY);
                    $error->setErrorDescription("Currency must be a valid currency from ISO 4271.");
                    $errors[] = $error;
                }
            } else if ($key == "RF") {
                if (strlen($value) > 16 || strlen($value) < 1 || !preg_match("/^[0-9]+$/",$value)) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_SENDERS_REFERENCE);
                    $error->setErrorDescription("Senders reference must be a decimal string with length between 1 and 16 characters.");
                    $errors[] = $error;
                }
            } else if ($key == "RN") {
                if (strlen($value) > 40 || strlen($value) < 1) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_RECIPIENT_NAME);
                    $error->setErrorDescription("Recipient name must be a string with length between 1 and 40 characters.");
                    $errors[] = $error;
                }
            } else if ($key == "NT") {
                if ($value != "E" && ! $value !="P") {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_NOTIFICATION_TYPE);
                    $error->setErrorDescription("Notification type must be 'E' (e-mail) or 'P' (phone).");
                    $errors[] = $error;
                }
            } else if ($key == "DT") {
                if (!preg_match("/^[0-9]{8,8}$/", $value)) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_DUE_DATE);
                    $error->setErrorDescription("Due date must be represented as a decimal string in YYYYmmdd format.");
                    $errors[] = $error;
                } else {
                    $month = substr($value, 4, 2);
                    $day = substr($value, 6, 2);
                    $year = substr($value, 0, 4);
                    if (!checkdate($month, $day, $year)) {
                        $error = new SpaydValidationError();
                        $error->setErrorCode(SpaydValidationError::ERROR_INVALID_DUE_DATE);
                        $error->setErrorDescription("Due date must be represented as a decimal string in YYYYmmdd format.");
                        $errors[] = $error;
                    }
                }
            } else if ($key == "PT") {
                if (strlen($value) > 3 || strlen($value) < 1) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_PAYMENT_TYPE);
                    $error->setErrorDescription("Payment type must be at represented as a string with length between 1 and 3 characters.");
                    $errors[] = $error;
                }
            } else if ($key == "MSG") {
                if (strlen($value) > 60 || strlen($value) < 1) {
                    $error = new SpaydValidationError();
                    $error->setErrorCode(SpaydValidationError::ERROR_INVALID_MESSAGE);
                    $error->setErrorDescription("Message must be at represented as a string with length between 1 and 60 characters.");
                    $errors[] = $error;
                }
            }
        }
        if (!$ibanFound) {
            $error = new SpaydValidationError();
            $error->setErrorCode(SpaydValidationError::ERROR_IBAN_NOT_FOUND);
            $error->setErrorDescription("You must specify an account number.");
            $errors[] = $error;
        }
        return empty($errors) ? null : $errors;
    }

    private static function checkISO88591($str) {
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c>=128 && $c<=159) {
                return false;
            }
        }
        return true;
    }
}
