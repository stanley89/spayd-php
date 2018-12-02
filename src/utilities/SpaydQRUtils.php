<?php

namespace Spayd\Utilities;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\LabelAlignment;

/**
 *
 * @author petrdvorak
 */
class SpaydQRUtils {

    const MIN_QR_SIZE = 200;
    const DEFAULT_QR_SIZE = 250;
    const MAX_QR_SIZE = 800;

    /**
     * Generate a QR code with the payment information of a given size, optionally, with branding
     * @param size A size of the QR code (without the branding) in pixels
     * @param paymentString A SPAYD string with payment information
     * @return An image with the payment QR code
     */
    public static function getQRCode($size, $paymentString, $hasBranding = true) {
        if ($size == null) {
            $size = self::DEFAULT_QR_SIZE;
        } else if ($size < self::MIN_QR_SIZE) {
            $size = self::MIN_QR_SIZE;
        } else if ($size > self::MAX_QR_SIZE) {
            $size = self::MAX_QR_SIZE;
        }

        $qrCode = new QrCode($paymentString);
        $qrCode->setSize($size);

        if ($hasBranding) {
            $qrCode->setLabelMargin([0,0,0,0]);
            $qrCode->setLabel("QR platba");
            $qrCode->setLabelAlignment(LabelAlignment::LEFT);
        }

        return $qrCode;
    }
}
