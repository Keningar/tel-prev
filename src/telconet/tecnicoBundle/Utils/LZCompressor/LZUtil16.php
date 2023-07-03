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
class LZUtil16
{

    /**
     * @return string
     */
    public static function fromCharCode()
    {
        return array_reduce(func_get_args(), function ($objA, $objB) 
        {
            $objA .= static::utf16_chr($objB);
            return $objA;
        });
    }

    /**
     * Phps chr() equivalent for UTF-16 encoding
     *
     * @param int|string $u
     * @return string
     */
    public static function utf16_chr($objU)
    {
        return mb_convert_encoding('&#' . intval($objU) . ';', 'UTF-16', 'HTML-ENTITIES');
    }

    /**
     * @param string $strStr
     * @param int $num
     *
     * @return bool|integer
     */
    public static function charCodeAt($objData)
    {
        $strSub = substr($objData->strStr, $objData->intIndex, 2);
        $strSub = static::utf16_charAt($strSub, 0);
        $objData->intIndex += strlen($strSub);
        $objData->boolEnd = strlen($strSub) <= 0;
        return static::utf16_ord($strSub);
    }

    /**
     * @source http://blog.sarabande.jp/post/35970262740
     * @param string $strCh
     * @return bool|integer
     */
    public static function utf16_ord($strCh) 
    {
        $intLength = strlen($strCh);
        if (2 === $intLength) 
        {
            return hexdec(bin2hex($strCh));
        } 
        else if (4 === $intLength) 
        {
            $strW1 = $strCh[0].$strCh[1];
            $strW2 = $strCh[2].$strCh[3];
            if ($strW1 < "\xD8\x00" || "\xDF\xFF" < $strW1 || $strW2 < "\xDC\x00" || "\xDF\xFF" < $strW2) 
            {
                return false;
            }
            $strW1 = (hexdec(bin2hex($strW1)) & 0x3ff) << 10;
            $strW2 =  hexdec(bin2hex($strW2)) & 0x3ff;
            return $strW1 + $strW2 + 0x10000;
        }
        return false;
    }

    /**
     * @param string $strStr
     * @param integer $intNum
     *
     * @return string
     */
    public static function utf16_charAt($strStr, $intNum)
    {
        return mb_substr($strStr, $intNum, 1, 'UTF-16');
    }

    /**
     * @param string $strStr
     * @return integer
     */
    public static function utf16_strlen($strStr)
    {
        return mb_strlen($strStr, 'UTF-16');
    }

}