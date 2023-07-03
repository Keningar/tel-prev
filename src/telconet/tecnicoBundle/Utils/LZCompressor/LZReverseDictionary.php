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
class LZReverseDictionary
{

    public $arrayEntries = array(0, 1 ,2);

    public function size() 
    {
        return count($this->arrayEntries);
    }

    public function hasEntry($intIndex) 
    {
        return array_key_exists($intIndex, $this->arrayEntries);
    }

    public function getEntry($intIndex) 
    {
        return $this->arrayEntries[$intIndex];
    }

    public function addEntry($objChar) 
    {
        $this->arrayEntries[] = $objChar;
    }

}