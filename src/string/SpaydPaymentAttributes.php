<?php
/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

namespace Spayd\String;
use Spayd\Model\Account\BankAccount;

/**
 *
 * @author petrdvorak
 */
class SpaydPaymentAttributes {

    private $bankAccount;
    private $alternateAccounts;
    private $amount;
    private $currency;
    private $sendersReference;
    private $recipientName;
    private $date;
    private $paymentType;
    private $message;
    private $notificationType;
    private $notificationValue;
    private $crc32;

    const PAYMENT_NOTIFICATION_TYPE_EMAIL = "PAYMENT_NOTIFICATION_TYPE_EMAIL";
    const PAYMENT_NOTIFICATION_TYPE_PHONE = "PAYMENT_NOTIFICATION_TYPE_PHONE";

    public function getAlternateAccounts() {
        return $this->alternateAccounts;
    }

    public function setAlternateAccounts(array $alternateAccounts) {
        $this->alternateAccounts = $alternateAccounts;
    }

    public function getBankAccount() {
        return $this->bankAccount;
    }

    public function setBankAccount(BankAccount $bankAccount) {
        $this->bankAccount = $bankAccount;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setCurrency(String $currency) {
        $this->currency = $currency;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage(String $message) {
        $this->message = $message;
    }

    public function getRecipientName() {
        return $this->recipientName;
    }

    public function setRecipientName(String $recipientName) {
        $this->recipientName = $recipientName;
    }

    public function getSendersReference() {
        return $this->sendersReference;
    }

    public function setSendersReference(String $sendersReference) {
        $this->sendersReference = $sendersReference;
    }

    public function getPaymentType() {
        return $this->paymentType;
    }

    public function setPaymentType(String $paymentType) {
        $this->paymentType = $paymentType;
    }

    public function getNotificationType() {
        return $this->notificationType;
    }

    public function setNotificationType($notificationType) {
        $this->notificationType = $notificationType;
    }

    public function getNotificationValue() {
        return $this->notificationValue;
    }

    public function setNotificationValue(String $notificationValue) {
        $this->notificationValue = $notificationValue;
    }

    public function getCrc32() {
        return $this->crc32;
    }

    public function setCrc32(String $crc32) {
        $this->crc32 = $crc32;
    }

}
