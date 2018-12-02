<?php

require __DIR__ . '/../../vendor/autoload.php';       # installation by Composer
require __DIR__ . '/../../src/string/SpaydPayment.php';
require __DIR__ . '/../../src/string/SpaydPaymentAttributes.php';
require __DIR__ . '/../../src/model/account/BankAccount.php';
require __DIR__ . '/../../src/model/account/CzechBankAccount.php';
require __DIR__ . '/../../src/utilities/IBANUtilities.php';

use Tester\Assert;
use Spayd\String\SpaydPayment;
use Spayd\String\SpaydPaymentAttributes;
use Spayd\Model\Account\CzechBankAccount;

Tester\Environment::setup();

/**
 *
 * @author petrdvorak
 */
class SpaydTest extends Tester\TestCase {

    /**
     * Test of paymentStringFromAccount method, of class SmartPayment.
     */
    public function testPaymentStringFromAccount() {
        $parameters = new SpaydPaymentAttributes();
        $parameters->setBankAccount(new CzechBankAccount("19", "123", "0800"));
        $extendedParameters = null;
        $transliterateParams = false;
        $expResult = "SPD*1.0*ACC:CZ2408000000190000000123";
        $result = SpaydPayment::paymentStringFromAccount($parameters, $extendedParameters, $transliterateParams);
        Assert::equal($expResult, $result);
    }

    public function testSpecialCharacterEscaping() {
        $original = "abc  123\u{2665}\u{2620}**123  abc-+ěščřžýáíé---%20";
        $expected = "abc  123%E2%99%A5%E2%98%A0%2A%2A123  abc-%2B%C4%9B%C5%A1%C4%8D%C5%99%C5%BE%C3%BD%C3%A1%C3%AD%C3%A9---%2520";
        Assert::equal($expected, SpaydPayment::escapeDisallowedCharacters($original));
    }

    /**
     * Test of paymentStringFromAccount method, of class SmartPayment.
     */
    public function testPaymentStringFromAccountAmountAndAlternateAccounts() {
        $parameters = new SpaydPaymentAttributes();
        $parameters->setBankAccount(new CzechBankAccount("19", "123", "0800"));
        $alternateAccounts = [];
        $alternateAccounts[] = new CzechBankAccount(null, "19", "5500");
        $alternateAccounts[] = new CzechBankAccount(null, "19", "0100");
        $parameters->setAlternateAccounts($alternateAccounts);
        $parameters->setAmount(100.5);
        $extendedParameters = null;
        $transliterateParams = false;
        $expResult = "SPD*1.0*ACC:CZ2408000000190000000123*ALT-ACC:CZ9755000000000000000019,CZ7301000000000000000019*AM:100.5";
        $result = SpaydPayment::paymentStringFromAccount($parameters, $extendedParameters, $transliterateParams);
        Assert::equal($expResult, $result);
    }
}

(new SpaydTest)->run();
