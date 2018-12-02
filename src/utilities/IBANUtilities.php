<?php
/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

 namespace Spayd\Utilities;
 use Spayd\Model\Account\CzechBankAccount;


/**
 *
 * @author petrdvorak
 */
class IBANUtilities {

    /**
     * Computes the IBAN number from a given Czech account information.
     * @param account A Czech account model class.
     * @return An IBAN number.
     */
    public static function computeIBANFromCzechBankAccount(CzechBankAccount $account) {
        // preprocess the numbers
        $prefix = sprintf('%06d', $account->getPrefix());
        $number = sprintf('%010d', $account->getNumber());
        $bank = sprintf('%04d', $account->getBankCode());

        // calculate the check sum
        $buf = $bank . $prefix . $number . "123500";
        $index = 0;
        $pz = -1;
        while ($index <= strlen($buf)) {
            if ($pz < 0) {
                $dividend = substr($buf, $index, 9);
                $index += 9;
            } else if ($pz >= 0 && $pz <= 9) {
                $dividend = $pz . substr($buf, $index, 8);
                $index += 8;
            } else {
                $dividend = $pz . substr($buf, $index, 7);
                $index += 7;
            }
            $pz = $dividend % 97;
        }
        $pz = 98 - $pz;

        // assign the checksum
        $checksum = sprintf("%02d", $pz);

        // build the IBAN number
        return "CZ" . $checksum . $bank . $prefix . $number;
    }
}
