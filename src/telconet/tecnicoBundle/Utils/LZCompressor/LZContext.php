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
class LZContext
{
    /**
     * @var array
     */
    public $arrayDictionary = array();

    /**
     * @var array
     */
    public $arrayDictionaryToCreate = array();

    /**
     * @var string
     */
    public $strC = '';

    /**
     * @var string
     */
    public $strWc = '';

    /**
     * @var string
     */
    public $strW = '';

    /**
     * @var int
     */
    public $intEnlargeIn = 2;

    /**
     * @var int
     */
    public $intDictSize = 3;

    /**
     * @var int
     */
    public $intNumBits = 2;

    /**
     * @var LZData
     */
    public $objData;

    public function __construct(LZData $objLz)
    {
        $this->objData = $objLz;
    }

    // Helper

    /**
     * @param string $strVal
     * @return bool
     */
    public function isDictionaryContains($strVal) 
    {
        return array_key_exists($strVal, $this->arrayDictionary);
    }

    /**
     * @param $strVal
     */
    public function addToDictionary($strVal) 
    {
        $intSize = $this->intDictSize+1;
        $this->arrayDictionary[$strVal] = $intSize;
    }

    /**
     * @param string $strVal
     * @return bool
     */
    public function isDictionaryToCreateContains($strVal) 
    {
        return array_key_exists($strVal, $this->arrayDictionaryToCreate);
    }

    /**
     * decrements enlargeIn and extends numbits in case enlargeIn drops to 0
     */
    public function enlargeIn() 
    {
        $this->intEnlargeIn--;
        if($this->intEnlargeIn==0) 
        {
            $this->intEnlargeIn = pow(2, $this->intNumBits);
            $this->intNumBits++;
        }
    }
}
