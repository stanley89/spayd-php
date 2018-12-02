<?php

/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

namespace Spayd\Model\Account;

/**
 *
 * @author petrdvorak
 */
abstract class BankAccount {

    protected $iban;
    protected $bic;

    public function __construct(String $iban = null, String $bic = null) {
        if ($iban) {
            $this->iban = $iban;
        }
        if ($bic) {
            $this->bic = $bic;
        }
    }

    public abstract function getIBAN();
    public abstract function setIBAN(String $iban);
    public abstract function getBIC();
    public abstract function setBIC(String $bic);

}
