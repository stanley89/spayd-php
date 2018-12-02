<?php
/**
 *  Copyright (c) 2012, SPAYD (www.spayd.org).
 */

 namespace Spayd\String;

/**
 *
 * @author petrdvorak
 */
class SpaydExtendedPaymentAttributeMap {

    private $data = [];

    /**
     * Creates a new map for the SPAYD extended attributes
     * @param map A map for extended parameters to be used, with key and X- values
     */
    public function __construct(array $map = []) {
        foreach ($map as $key => $value) {
            $key = strtoupper($key);
            $value = strtoupper($value);
            if (!preg_match("/^X-/", $key)) {
                $key = "X-" . $key;
            }
            $this->data[$key] = $value;
        }
    }

    /**
     * Builds a string with extended parameter definitions
     * @return A part of the SPAYD string that contains all X- attributes
     */
    public function toExtendedParams() {
        $returnValue = "";
        foreach ($this->data as $key => $value) {
            $returnValue .= $key . ":" . SpaydPayment::escapeDisallowedCharacters($value) . "*";
        }
        return $returnValue;
    }

    public function isEmpty() {
        return empty($this->data);
    }

}
