# PHP implementation of SmartPaymentDescriptor Generator

SmartPaymentDescriptor is a lightweight format for exchanging a payment
information. A vCard for payment instead of contacts, if you will..

This PHP library simplifies adding the support for generating/validating the SmartPaymentDescriptor
(both file and QR code generation) to any PHP application.

Original Java implementation: https://github.com/spayd/spayd-java

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require stanley89/spayd-php
```

## Basic usage

```php
use Spayd\String\SpaydPaymentAttributes;
use Spayd\String\SpaydExtendedPaymentAttributeMap;
use Spayd\String\SpaydPayment;
use Spayd\Model\Account\CzechBankAccount;
use Spayd\Utilities\SpaydQRUtils;

$parameters = new SpaydPaymentAttributes();
$parameters->setBankAccount(new CzechBankAccount("19", "123", "0800"));

$parameters->setAmount("1");
$parameters->setCurrency("CZK");
$parameters->setDate(new \DateTime("2018-12-06"));
$parameters->setRecipientName("Jan Novák");
$parameters->setMessage("Příliš žluťoučký kůň úpěl ďábelské ódy.");
$extendedParameters = new SpaydExtendedPaymentAttributeMap(["VS" => 123, "SS" => 456, "KS" => 558]);
$transliterateParams = true;
$result = SpaydPayment::paymentStringFromAccount($parameters, $extendedParameters, $transliterateParams);

$qrCode = SpaydQRUtils::getQRCode(null,  $result, true);
$qrCode->writeFile(__DIR__.'/qrcode.png');
```

## License

The sources are available under [Apache 2.0 License](http://www.apache.org/licenses/LICENSE-2.0.html)
