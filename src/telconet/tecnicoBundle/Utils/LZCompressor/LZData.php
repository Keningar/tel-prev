<?php
namespace telconet\tecnicoBundle\Utils\LZCompressor;

/**
 * Clase que permite comprimir/descomprimir cadenas codificadas en 
 * Base64 - URI - UTF16
 * 
 * @author Javier Hidalgo <jihidalgo@telconet.ec>
 * @version 1.0
 * @since 29/09/2022
 */
class LZData
{
    /**
     * @var
     */
    public $strStr = '';

    /**
     * @var
     */
    public $objVal;

    /**
     * @var int
     */
    public $intPosition = 0;

    /**
     * @var int - index of letters (may be multiple of characters)
     */
    public $intIndex = 1;
    
    /*
     * @var bool - set to true if theindex is out of str range
     */
    public $boolEnd = true;
    
    /**
     * @param unknown $str
     */
    public function append($strStr) 
    {
        $this->strStr .= $strStr;
    }
}
