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
class LZUtil
{
    /**
     * @var string
     */
    public static $strKeyStrBase64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    public static $strKeyStrUriSafe = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+-$";
    private static $arrayBaseReverseDic = array();

    /**
     * @param string $strAlphabet
     * @param integer $intCharacter
     * @return string
     */
    public static function getBaseValue($strAlphabet, $intCharacter)
    {
        if(!array_key_exists($strAlphabet, static::$arrayBaseReverseDic)) 
        {
            static::$arrayBaseReverseDic[$strAlphabet] = array();
            for($intI=0; $intI<strlen($strAlphabet); $intI++) 
            {
                static::$arrayBaseReverseDic[$strAlphabet][$strAlphabet[$intI]] = $intI;
            }
        }
        return static::$arrayBaseReverseDic[$strAlphabet][$intCharacter];
    }

    /**
     * @return string
     */
    public static function fromCharCode()
    {
        return array_reduce(func_get_args(), function ($strA, $strB) 
        {
            $strA .= static::utf8_chr($strB);
            return $strA;
        });
    }

    /**
     * Phps chr() equivalent for UTF-8 encoding
     *
     * @param int|string $objU
     * @return string
     */
    public static function utf8_chr($objU)
    {
        return mb_convert_encoding('&#' . intval($objU) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     * @param string $strStr
     * @param int $intNum
     *
     * @return bool|integer
     */
    public static function charCodeAt($strStr, $intNum=0)
    {
        return static::utf8_ord(static::utf8_charAt($strStr, $intNum));
    }

    /**
     * @param string $strCh
     *
     * @return bool|integer
     */
    public static function utf8_ord($strCh)
    {
        // must remain php's strlen
        $intLen = strlen($strCh);
        if ($intLen <= 0) 
        {
            return -1;
        }

        $objResult = -2;

        $intH = ord($strCh[0]);
        if ($intH <= 0x7F)
        {
            $objResult = $intH;
        } 
        elseif ($intH < 0xC2)
        {
            $objResult = -3;
        } 
        elseif ($intH <= 0xDF && $intLen > 1)
        {
            $objResult = ($intH & 0x1F) << 6 | (ord($strCh[1]) & 0x3F);
        } 
        elseif ($intH <= 0xEF && $intLen > 2)
        {
            $objResult = ($intH & 0x0F) << 12 | (ord($strCh[1]) & 0x3F) << 6 | (ord($strCh[2]) & 0x3F);
        } 
        elseif ($intH <= 0xF4 && $intLen > 3)
        {
            $objResult = ($intH & 0x0F) << 18 | (ord($strCh[1]) & 0x3F) << 12 | (ord($strCh[2]) & 0x3F) << 6 | (ord($strCh[3]) & 0x3F);
        }
        return $objResult;
    }

    /**
     * @param string $strStr
     * @param integer $intNum
     *
     * @return string
     */
    public static function utf8_charAt($strStr, $intNum)
    {
        return mb_substr($strStr, $intNum, 1, 'UTF-8');
    }

    /**
     * @param string $strStr
     * @return integer
     */
    public static function utf8_strlen($strStr) 
    {
        return mb_strlen($strStr, 'UTF-8');
    }
}