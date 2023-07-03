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
class LZString
{
    /**
     * Compress into a string that is already URI encoded
     *
     * @param string $strInput
     *
     * @return string
     */
    public static function compressToEncodedURIComponent($strInput)
    {
        if ($strInput === null) 
        {
            return "";
        }

        $arrayParameter =array("strCompressed" => $strInput,
                                "intResetValue" => 6,
                                "objGetNextValue" => function($strA) 
                                {
                                    return LZUtil::$strKeyStrUriSafe[$strA];
                                });

        return static::compressData($arrayParameter);
    }

    /**
     * Decompress from an output of compressToEncodedURIComponent
     *
     * @param string $strInput
     *
     * @return null|string
     */
    public static function decompressFromEncodedURIComponent($strInput)
    {
        if ($strInput === null) 
        {
            return "";
        }
        if ($strInput === "") 
        {
            return null;
        }

        $strInput = str_replace(' ', "+", $strInput);

        $arrayParameter =array("strCompressed" => $strInput,
                                "intResetValue" => 32,
                                "objGetNextValue" => function($objData) 
                                {
                                    $strSub = substr($objData->strStr, $objData->intIndex, 6);
                                    $strSub = LZUtil::utf8_charAt($strSub, 0);
                                    $objData->intIndex += strlen($strSub);
                                    $objData->boolEnd = strlen($strSub) <= 0;
                                    return LZUtil::getBaseValue( LZUtil::$strKeyStrUriSafe, $strSub );
                                });
        return static::decompressData($arrayParameter);
    }

    public static function compressToBase64($strInput)
    {
        $arrayParameter =array("strCompressed" => $strInput,
                                "intResetValue" => 6,
                                "objGetNextValue" => function($strA) 
                                {
                                    return LZUtil::$strKeyStrBase64[$strA];
                                });
        $strRes = static::compressData($arrayParameter);

        switch (strlen($strRes) % 4) 
        { // To produce valid Base64
            default: // When could this happen ?
            case 0 : 
                return $strRes;
                break;
            case 1 : 
                $strRes = $strRes ."===";
                break;
            case 2 : 
                $strRes = $strRes ."==";
                break;
            case 3 : 
                $strRes = $strRes ."=";
                break;
        }
        return $strRes;
    }

    public static function decompressFromBase64($strInput)
    {
        $arrayParameter =array("strCompressed" => $strInput,
                                "intResetValue" => 32,
                                "objGetNextValue" => function($objData) 
                                {
                                    $strSub = substr($objData->strStr, $objData->intIndex, 6);
                                    $strSub = LZUtil::utf8_charAt($strSub, 0);
                                    $objData->intIndex += strlen($strSub);
                                    $objData->boolEnd = strlen($strSub) <= 0;
                                    return LZUtil::getBaseValue(LZUtil::$strKeyStrBase64, $strSub);
                                });
        return static::decompressData($arrayParameter);
    }

    public static function compressToUTF16($strInput) 
    {
        $arrayParameter =array("strCompressed" => $strInput,
                                "intResetValue" => 15,
                                "objGetNextValue" => function($strA) 
                                {
                                    return LZUtil16::fromCharCode($strA+32);
                                });

        return static::compressData($arrayParameter). LZUtil16::utf16_chr(32);
    }

    public static function decompressFromUTF16($strInput) 
    {
        $arrayParameter =array("strCompressed" => $strInput,
                                "intResetValue" => 16384,
                                "objGetNextValue" => function($objData) 
                                {
                                    return LZUtil16::charCodeAt($objData)-32;
                                });
        return static::decompressData($arrayParameter);
    }

    /**
     * @param string $strUncompressed
     * @return string
     */
    public static function compress($strUncompressed)
    {
        $arrayParameter =array("strCompressed" => $strUncompressed,
                                "intResetValue" => 16,
                                "objGetNextValue" => function($strA) 
                                {
                                    return LZUtil::fromCharCode($strA);
                                });

        return static::compressData($arrayParameter);
    }

    /**
     * @param string $strCompressed
     * @return string
     */
    public static function decompress($strCompressed)
    {
        $arrayParameter =array("strCompressed" => $strCompressed,
                                "intResetValue" => 32768,
                                "objGetNextValue" => function($objData) 
                                {
                                    $strSub = substr($objData->strStr, $objData->intIndex, 16);
                                    $strSub = LZUtil::utf8_charAt($strSub, 0);
                                    $objData->intIndex += strlen($strSub);
                                    $objData->boolEnd = strlen($strSub) <= 0;
                                    return LZUtil::charCodeAt($strSub, 0);
                                });
        return static::decompressData($arrayParameter);
    }

    /**
     * @param string $strUncompressed
     * @param integer $intBitsPerChar
     * @param callable $objGetCharFromInt
     * @return string
     * {@inheritdoc}
     */
    private static function compressData($arrayParameter) 
    {
        $strUncompressed = $arrayParameter['strUncompressed'];
        $intBitsPerChar = $arrayParameter['intBitsPerChar'];
        $objGetCharFromInt = $arrayParameter['objGetCharFromInt'];

        if(!is_string($strUncompressed) || strlen($strUncompressed) === 0) 
        {
            return '';
        }
        $objLzData = new LZData();
        $objContext = new LZContext($objLzData);
        $intLength = 0;
        $intIi = 0;
        do 
        {
            // take the context symbol in UTF-8
            $strSub = substr( $strUncompressed, $intIi, 6); // cover the full utf-8 character space
            $objContext->strC = mb_substr( $strSub, 0, 1, 'UTF-8'); // fast take the character
            $intLength = strlen( $objContext->strC ); // get amount of bytes taken
            $intIi += $intLength; // advance the index
            // handle the compression
            if(!$objContext->isDictionaryContains($objContext->strC)) 
            {
                $objContext->addToDictionary($objContext->strC);
                $objContext->arrayDictionaryToCreate[$objContext->strC] = true;
            }
            $objContext->strWc = $objContext->strW . $objContext->strC;
            if($objContext->isDictionaryContains($objContext->strWc)) 
            {
                $objContext->strW = $objContext->strWc;
            } 
            else 
            {
                $arrayParameter =array("objContext" => $objContext,
                                        "intBitsPerChar" => $intBitsPerChar,
                                        "objGetCharFromInt" => $objGetCharFromInt);
                static::produceW($arrayParameter);
            }
        } while( $intLength > 0 );

        if($objContext->strW !== '') 
        {
            $arrayParameter =array("objContext" => $objContext,
                                    "intBitsPerChar" => $intBitsPerChar,
                                    "objGetCharFromInt" => $objGetCharFromInt);
            static::produceW($arrayParameter);
        }

        $intValue = 2;
        for($intI=0; $intI<$objContext->intNumBits; $intI++) 
        {
            $arrayParameter =array("strValue" => $intValue&1,
                                    "objData" => $objContext->objData,
                                    "intBitsPerChar" => $intBitsPerChar,
                                    "objGetCharFromInt" => $objGetCharFromInt);

            static::writeBit($arrayParameter);
            $intValue = $intValue >> 1;
        }

        while (true) 
        {
            $objContext->objData->objVal = $objContext->objData->objVal << 1;
            if ($objContext->objData->intPosition == ($intBitsPerChar-1)) 
            {
                $objContext->objData->append($objGetCharFromInt($objContext->objData->objVal));
                break;
            }
            $objContext->objData->intPosition++;
        }

        return $objContext->objData->strStr;
    }

    /**
     * @param LZContext $objContext
     * @param integer $intBitsPerChar
     * @param callable $objGetCharFromInt
     *
     * @return LZContext
     */
    private static function produceW($arrayParameter)
    {
        $objContext = $arrayParameter['objContext'];
        $intBitsPerChar = $arrayParameter['intBitsPerChar'];
        $objGetCharFromInt = $arrayParameter['objGetCharFromInt'];

        if($objContext->isDictionaryToCreateContains($objContext->strW)) 
        {
            if(LZUtil::charCodeAt($objContext->strW)<256) 
            {
                for ($intI=0; $intI<$objContext->intNumBits; $intI++) 
                {
                    $arrayParameter =array("strValue" => null,
                                            "objData" => $objContext->objData,
                                            "intBitsPerChar" => $intBitsPerChar,
                                            "objGetCharFromInt" => $objGetCharFromInt);

                    static::writeBit($arrayParameter);
                }
                $objValue = LZUtil::charCodeAt($objContext->strW);
                for ($intI=0; $intI<8; $intI++) 
                {
                    $arrayParameter =array("strValue" => $objValue&1,
                                            "objData" => $objContext->objData,
                                            "intBitsPerChar" => $intBitsPerChar,
                                            "objGetCharFromInt" => $objGetCharFromInt);

                    static::writeBit($arrayParameter);
                    $objValue = $objValue >> 1;
                }
            } 
            else 
            {
                $objValue = 1;
                for ($intI=0; $intI<$objContext->intNumBits; $intI++) 
                {
                    $arrayParameter =array("strValue" => $objValue,
                                            "objData" => $objContext->objData,
                                            "intBitsPerChar" => $intBitsPerChar,
                                            "objGetCharFromInt" => $objGetCharFromInt);

                    static::writeBit($arrayParameter);
                    $objValue = 0;
                }
                $objValue = LZUtil::charCodeAt($objContext->strW);
                for ($intI=0; $intI<16; $intI++) 
                {
                    $arrayParameter =array("strValue" => $objValue&1,
                                            "objData" => $objContext->objData,
                                            "intBitsPerChar" => $intBitsPerChar,
                                            "objGetCharFromInt" => $objGetCharFromInt);

                    static::writeBit($arrayParameter);
                    $objValue = $objValue >> 1;
                }
            }
            $objContext->enlargeIn();
            unset($objContext->arrayDictionaryToCreate[$objContext->strW]);
        } 
        else
        {
            $objValue = $objContext->arrayDictionary[$objContext->strW];
            for ($intI=0; $intI<$objContext->intNumBits; $intI++) 
            {
                $arrayParameter =array("strValue" => $objValue&1,
                                        "objData" => $objContext->objData,
                                        "intBitsPerChar" => $intBitsPerChar,
                                        "objGetCharFromInt" => $objGetCharFromInt);

                static::writeBit($arrayParameter);
                $objValue = $objValue >> 1;
            }
        }
        $objContext->enlargeIn();
        $objContext->addToDictionary($objContext->strWc);
        $objContext->strW = $objContext->strC.'';
    }

    /**
     * @param string $strValue
     * @param LZData $objData
     * @param integer $intBitsPerChar
     * @param callable $objGetCharFromInt
     */
    private static function writeBit($arrayParameter)
    {
        $strValue = $arrayParameter['strValue'];
        $objData = $arrayParameter['objData'];
        $intBitsPerChar = $arrayParameter['intBitsPerChar'];
        $objGetCharFromInt = $arrayParameter['objGetCharFromInt'];

        if(null !== $strValue) 
        {
            $objData->objVal = ($objData->objVal << 1) | $strValue;
        } 
        else 
        {
            $objData->objVal = ($objData->objVal << 1);
        }
        if ($objData->intPosition == ($intBitsPerChar-1)) 
        {
            $objData->intPosition = 0;
            $objData->append($objGetCharFromInt($objData->objVal));
            $objData->objVal = 0;
        } 
        else 
        {
            $objData->intPosition++;
        }
    }

    /**
     * @param LZData $objData
     * @param integer $intResetValue
     * @param callable $objGetNextValue
     * @param integer $intExponent
     * @param string $strFeed
     * @return integer
     */
    private static function readBits($arrayParameter)
    {
        $objData = $arrayParameter['objData'];
        $intResetValue = $arrayParameter['intResetValue'];
        $objGetNextValue = $arrayParameter['objGetNextValue'];
        $intExponent = $arrayParameter['intExponent'];

        $intBits = 0;
        $objMaxPower = pow(2, $intExponent);
        $intPower=1;
        while($intPower != $objMaxPower) 
        {
            $objResb = $objData->objVal & $objData->intPosition;
            $objData->intPosition >>= 1;
            if ($objData->intPosition == 0) 
            {
                $objData->intPosition = $intResetValue;
                $objData->objVal = $objGetNextValue($objData);
            }
            $intBits |= (($objResb>0 ? 1 : 0) * $intPower);
            $intPower <<= 1;
        }
        return $intBits;
    }

    /**
     * @param string $strCompressed
     * @param integer $intResetValue
     * @param callable $objGetNextValue
     * @return string
     * {@inheritdoc}
     */
    private static function decompressData($arrayParameter)
    {
        $strCompressed = $arrayParameter['strCompressed'];
        $intResetValue = $arrayParameter['intResetValue'];
        $objGetNextValue = $arrayParameter['objGetNextValue'];

        if(!is_string($strCompressed) || strlen($strCompressed) === 0) 
        {
            return '';
        }

        $objEntry = null;
        $intEnlargeIn = 4;
        $intNumBits = 3;
        $strResult = '';

        $objDictionary = new LZReverseDictionary();

        $objData = new LZData();
        $objData->strStr = $strCompressed;
        $objData->intIndex = 0;
        $objData->boolEnd = false;
        $objData->objVal = $objGetNextValue($objData);
        $objData->intPosition = $intResetValue;

        $arrayParameter =array("objData" => $objData,
                                "intResetValue" => $intResetValue,
                                "objGetNextValue" => $objGetNextValue,
                                "intExponent" => 2);

        $intNext = static::readBits($arrayParameter);

        if($intNext < 0 || $intNext > 1) 
        {
            return '';
        }

        $intExponent = ($intNext == 0) ? 8 : 16;

        $arrayParameter =array("objData" => $objData,
                                "intResetValue" => $intResetValue,
                                "objGetNextValue" => $objGetNextValue,
                                "intExponent" => $intExponent);
        $intBits = static::readBits($arrayParameter);

        $strC = LZUtil::fromCharCode($intBits);
        $objDictionary->addEntry($strC);
        $strW = $strC;

        $strResult .= $strC;

        while(true) 
        {
            if($objData->boolEnd) 
            {
                return '';
            }
            $arrayParameter =array("objData" => $objData,
                                    "intResetValue" => $intResetValue,
                                    "objGetNextValue" => $objGetNextValue,
                                    "intExponent" => $intNumBits);
            $strBits = static::readBits($arrayParameter);

            $strC = $strBits;

            switch($strC) 
            {
                case 0:
                    $arrayParameter =array("objData" => $objData,
                                            "intResetValue" => $intResetValue,
                                            "objGetNextValue" => $objGetNextValue,
                                            "intExponent" => 8);
                    $strBits = static::readBits($arrayParameter);
                    $strC = $objDictionary->size();
                    $objDictionary->addEntry(LZUtil::fromCharCode($strBits));
                    $intEnlargeIn--;
                    break;
                case 1:
                    $arrayParameter =array("objData" => $objData,
                                            "intResetValue" => $intResetValue,
                                            "objGetNextValue" => $objGetNextValue,
                                            "intExponent" => 16);
                    $strBits = static::readBits($arrayParameter);
                    $strC = $objDictionary->size();
                    $objDictionary->addEntry(LZUtil::fromCharCode($strBits));
                    $intEnlargeIn--;
                    break;
                case 2:
                    return $strResult;
                    break;
                default:
            }

            if($intEnlargeIn == 0) 
            {
                $intEnlargeIn = pow(2, $intNumBits);
                $intNumBits++;
            }

            if($objDictionary->hasEntry($strC)) 
            {
                $objEntry = $objDictionary->getEntry($strC);
            }
            else 
            {
                if ($strC == $objDictionary->size()) 
                {
                    $objEntry = $strW . $strW[0];
                } 
                else 
                {
                    return null;
                }
            }

            $strResult .= $objEntry;
            $objDictionary->addEntry($strW . LZUtil::utf8_charAt($objEntry, 0));
            $strW = $objEntry;

            $intEnlargeIn--;
            if($intEnlargeIn == 0) 
            {
                $intEnlargeIn = pow(2, $intNumBits);
                $intNumBits++;
            }
        }
    }
}
