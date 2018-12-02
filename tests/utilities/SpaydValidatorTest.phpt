<?php

require __DIR__ . '/../../vendor/autoload.php';       # installation by Composer
require __DIR__ . '/../../src/utilities/SpaydValidator.php';
require __DIR__ . '/../../src/model/error/SpaydValidationError.php';

use Tester\Assert;
use Spayd\Utilities\SpaydValidator;
use Spayd\Model\Account\CzechBankAccount;

Tester\Environment::setup();

/**
 *
 * @author petrdvorak
 */
class SpaydValidatorTest extends Tester\TestCase {

    /**
     * Test of validatePaymentString method, of class SmartPaymentValidator.
     */
    public function testValidatePaymentStringBasic() {
        $paymentString = "SPD*1.0*232131";
        $result = SpaydValidator::validatePaymentString($paymentString);
        // 1 error is expected
        Assert::equal(count($result), 1);
    }

    /**
     * Test of validatePaymentString method, of class SmartPaymentValidator.
     */
    public function testValidatePaymentStringSimpleCorrect() {
        $paymentString = "SPD*1.0*ACC:CZ3155000000001043006511";
        $result = SpaydValidator::validatePaymentString($paymentString);
        // 0 error is expected
        Assert::null($result);
    }

    /**
     * Test of generating SPAYD string with alternate account
     */
    public function testValidatePaymentStringAlternateAccounts() {
        $paymentString = "SPD*1.0*ACC:CZ3155000000001043006511+RBCZ66*ALT-ACC:CZ3155000000001043006511+RBCZ66,CZ3155000000001043006511+RBCZ66,CZ3155000000001043006511+RBCZ66";
        $result = SpaydValidator::validatePaymentString($paymentString);
        // 0 error is expected
        Assert::null($result);
    }

    /**
     * Test of the situation with "**" in the string.
     */
    public function testDoubleStarInString() {
        $paymentString = "SPD*1.0*AM:100**ACC:CZ05678876589087329";
        $result = SpaydValidator::validatePaymentString($paymentString);
        // 1 error is expected
        Assert::equal(count($result), 1);
    }

}

(new SpaydValidatorTest)->run();
