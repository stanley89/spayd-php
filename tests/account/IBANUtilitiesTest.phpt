<?php

require __DIR__ . '/../../vendor/autoload.php';       # installation by Composer
require __DIR__ . '/../../src/utilities/IBANUtilities.php';
require __DIR__ . '/../../src/model/account/BankAccount.php';
require __DIR__ . '/../../src/model/account/CzechBankAccount.php';

use Tester\Assert;
use Spayd\Model\Account\CzechBankAccount;
use Spayd\Utilities\IBANUtilities;

Tester\Environment::setup();

/**
 *
 * @author petrdvorak
 */
class IBANUtilitiesTest extends Tester\TestCase {

    /**
     * Test of computeIBANFromBankAccount method, of class IBANUtilities.
     */
    public function testComputeIBANFromBankAccount() {
        $prefix = "19";
        $number = "2000145399";
        $bank = "0800";
        $account = new CzechBankAccount($prefix, $number, $bank);
        $expResult = "CZ6508000000192000145399";
        $result = IBANUtilities::computeIBANFromCzechBankAccount($account);
        Assert::equal($expResult, $result);
    }

    public function testComputeIBANFromBankAccount2() {
        $prefix = "178124";
        $number = "4159";
        $bank = "0710";
        $account = new CzechBankAccount($prefix, $number, $bank);
        $expResult = "CZ6907101781240000004159";
        $result = IBANUtilities::computeIBANFromCzechBankAccount($account);
        Assert::equal($expResult, $result);
    }
}

(new IBANUtilitiesTest)->run();
