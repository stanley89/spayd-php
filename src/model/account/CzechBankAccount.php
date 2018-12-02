<?php

/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

namespace Spayd\Model\Account;
use Spayd\Utilities\IBANUtilities;

/**
 *
 * @author petrdvorak
 */
class CzechBankAccount extends BankAccount {

    private $prefix;
    private $number;
    private $bankCode;

    private function validateMod11(String $number) {
        $weight = 1;
        $sum = 0;
        for ($k = strlen($number) - 1; $k >= 0; $k--) {
            $sum += $number[$k] * $weight;
            $weight *= 2;
        }

        if (($sum % 11) != 0) {
            return false;
        }

        return true;
    }

    public function __construct(String $prefix = null, String $number = null, String $bankCode = null) {
        $this->validateAccountParameters($prefix, $number, $bankCode);
        $this->prefix = $prefix;
        $this->number = $number;
        $this->bankCode = $bankCode;
    }

    public function getBankCode() {
        return $this->bankCode;
    }

    public function setBankCode(String $bankCode) {
        $this->validateAccountParameters(null, null, bankCode);
        $this->bankCode = $bankCode;
    }

    public function getNumber() {
        return $this->number;
    }

    public function setNumber(String $number) {
        $this->validateAccountParameters(null, $number, null);
        $this->number = $number;
    }

    public function getPrefix() {
        if ($this->prefix == null) {
            return "000000";
        }
        return $this->prefix;
    }

    public function setPrefix(String $prefix) {
        $this->validateAccountParameters($prefix, null, null);
        $this->prefix = $prefix;
    }

    public function setIBAN(String $iban) {
        $this->iban = $iban;
        $this->bankCode = substr($iban, 4, 4);
        $this->number = substr($iban, 14, 10);
        $this->prefix = substr($iban, 8, 6);
    }

    public function getIBAN() {
        return IBANUtilities::computeIBANFromCzechBankAccount($this);
    }

    public function getBIC() {
        return $this->bic;
    }

    public function setBIC(String $bic) {
        $this->bic = $bic;
    }

    private function validateAccountParameters(String $prefix = null, String $number = null, String $bankCode = null) {
        if ($prefix != null) {
            for ($i = 0; $i < strlen($prefix); $i++) {
                if ($prefix[$i] < 0 && $prefix[$i] > 9) {
                    throw new InvalidArgumentException("Czech account number (prefix) must be numeric.");
                }
            }
            if (!$this->validateMod11($prefix)) {
                throw new InvalidArgumentException("Czech account number (prefix) must pass bank mod 11 test.");
            }
        }
        if ($number != null) {
            for ($i = 0; $i < strlen($number); $i++) {
                if ($number[$i] < 0 && $number[$i] > 9) {
                    throw new InvalidArgumentException("Czech account number (basic part) must be numeric.");
                }
            }
            if (!$this->validateMod11($number)) {
                throw new IllegalArgumentException("Czech account number (basic part) must pass bank mod 11 test.");
            }
        }
        if ($bankCode != null) {
            for ($i = 0; $i < strlen($bankCode); $i++) {
                if ($bankCode[$i] < 0 && $bankCode[$i] > 9) {
                    throw new InvalidArgumentException("Czech account number (bank code) must be numeric.");
                }
            }
        }
    }
}
