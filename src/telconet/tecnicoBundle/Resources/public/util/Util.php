<?php

namespace Telconet\tecnicoBundle\Resources\util;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Border;

class Util
{
    /**
     * Devuelve el slug de la cadena de texto que se le pasa
     * Código copiado del método urlize() de Doctrine 1
     * 
     * @param string $cadena Cadena de texto original
     * @return string Slug calculado para la cadena original
     */
    public static function slugify($cadena)
    {
        // Remove all non url friendly characters with the unaccent function
        $valor = self::sinAcentos($cadena);

        if (function_exists('mb_strtolower')) {
            $valor = mb_strtolower($valor);
        } else {
            $valor = strtolower($valor);
        }

        // Remove all none word characters
        $valor = preg_replace('/\W/', ' ', $valor);

        // More stripping. Replace spaces with dashes
        $valor = strtolower(preg_replace('/[^A-Z^a-z^0-9^\/]+/', '-',
                           preg_replace('/([a-z\d])([A-Z])/', '\1_\2',
                           preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2',
                           preg_replace('/::/', '/', $valor)))));

        return trim($valor, '-');
    }
    
    public static function esEmailValido($email)
    {
        // Primero, checamos que solo haya un simbolo @, y que los largos sean correctos
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email))
        {
            // correo invalido por numero incorrecto de caracteres en una parte, o numero incorrecto de simbolos @
            return false;
        }
        // se divide en partes para hacerlo mas sencillo
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++)
        {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i]))
            {
                return false;
            }
        }
        // se revisa si el dominio es una IP. Si no, debe ser un nombre de dominio valido
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1]))
        {
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2)
            {
                return false; // No son suficientes partes o secciones para se un dominio
            }
            for ($i = 0; $i < sizeof($domain_array); $i++)
            {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i]))
                {
                    return false;
                }
            }
        }
        return true;
    }
    
    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif (!empty($_SERVER['REMOTE_ADDR']))
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ip=self::obtenerIpInterfaz('eth0');
        }
        return $ip;
    }
    
    private static function obtenerIpInterfaz($interfaz)
    {
        if( !preg_match('/^[a-zA-Z0-9]+$/', $interfaz) )
        { 
            throw new Exception("Caracter ilegal encontrado en la cadena: ". $interfaz);
        }
        $interfaz = escapeshellarg($interfaz);
        $comando = ( "LANG=EN /sbin/ifconfig ".$interfaz." | grep 'inet addr:'| cut -d: -f2 | awk '{ print $1}'" );
        //Deshabilitar output buffering para evitar que el comando system haga un flush del buffer de salida
        //e imprima caractere, daniando asi el xml del servicio
        ob_start();
        $respuesta = system($comando, $r);
        ob_end_clean();
        
        if(!$respuesta)
        {
            $mensajeError = "No se pudo recuperar la ip del servidor. ";
            error_log("[Custom Error]: ". $mensajeError . $comando);
            throw new Exception($mensajeError);
        }
        return $respuesta;
    }
    
    public static function sinAcentos($string)
    {
        if ( ! preg_match('/[\x80-\xff]/', $string) ) {
          return $string;
        }

        if (self::seemsUtf8($string)) {
          $chars = array(
          // Decompositions for Latin-1 Supplement
          chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
          chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
          chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
          chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
          chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
          chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
          chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
          chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
          chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
          chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
          chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
          chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
          chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
          chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
          chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
          chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
          chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
          chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
          chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
          chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
          chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
          chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
          chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
          chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
          chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
          chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
          chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
          chr(195).chr(191) => 'y',
          // Decompositions for Latin Extended-A
          chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
          chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
          chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
          chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
          chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
          chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
          chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
          chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
          chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
          chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
          chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
          chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
          chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
          chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
          chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
          chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
          chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
          chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
          chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
          chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
          chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
          chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
          chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
          chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
          chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
          chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
          chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
          chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
          chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
          chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
          chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
          chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
          chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
          chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
          chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
          chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
          chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
          chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
          chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
          chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
          chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
          chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
          chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
          chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
          chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
          chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
          chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
          chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
          chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
          chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
          chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
          chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
          chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
          chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
          chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
          chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
          chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
          chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
          chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
          chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
          chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
          chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
          chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
          chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
          // Euro Sign
          chr(226).chr(130).chr(172) => 'E',
          // GBP (Pound) Sign
          chr(194).chr(163) => '',
          'Ä' => 'Ae', 'ä' => 'ae', 'Ü' => 'Ue', 'ü' => 'ue',
          'Ö' => 'Oe', 'ö' => 'oe', 'ß' => 'ss',
          // Norwegian characters
          'Å'=>'Aa','Æ'=>'Ae','Ø'=>'O','æ'=>'a','ø'=>'o','å','aa'
          );

          $string = strtr($string, $chars);
        } else {
          // Assume ISO-8859-1 if not UTF-8
          $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
            .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
            .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
            .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
            .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
            .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
            .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
            .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
            .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
            .chr(252).chr(253).chr(255);

          $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

          $string = strtr($string, $chars['in'], $chars['out']);
          $doubleChars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
          $doubleChars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
          $string = str_replace($doubleChars['in'], $doubleChars['out'], $string);
        }

        return $string;
    }
    
    private static function seemsUtf8($string)
    {
      for ($i = 0; $i < strlen($string); $i++) {
        if (ord($string[$i]) < 0x80) continue; # 0bbbbbbb
        elseif ((ord($string[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
        elseif ((ord($string[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
        elseif ((ord($string[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
        elseif ((ord($string[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
        elseif ((ord($string[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
        else return false; # Does not match any model
        for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
          if ((++$i == strlen($string)) || ((ord($string[$i]) & 0xC0) != 0x80))
          return false;
        }
      }
      return true;
    }
    
    public static function DMStoDEC($deg,$min,$sec)
    {
        // Converts DMS ( Degrees / minutes / seconds ) 
        // to decimal format longitude / latitude
        return $deg+((($min*60)+($sec))/3600);
    }

    public static function DECtoDMS($dec)
    {
        // Converts decimal longitude / latitude to DMS
        // ( Degrees / minutes / seconds ) 

        // This is the piece of code which may appear to 
        // be inefficient, but to avoid issues with floating
        // point math we extract the integer part and the float
        // part by using a string function.
        if($dec==0)
            return array("deg"=>0,"min"=>0,"sec"=>0);
        $vars = explode(".",$dec);
        $deg = $vars[0];
        $tempma = "0.".$vars[1];

        $tempma = $tempma * 3600;
        $min = floor($tempma / 60);
        $sec = $tempma - ($min*60);

        return array("deg"=>$deg,"min"=>$min,"sec"=>$sec);
    }
    
    public static function getFormatEstado($estado)
    {
        switch ($estado)
        {
            case 'ACTIVE':
                return 'Activo';
            case 'INACTIVE':
                return 'Inactivo';
            case 'RESERVED':
                return 'Reservado';
            case 'PORTS':
                return 'Puertos';
            case 'SETUP':
                return 'Configurado';
            case 'EDITED':
                return 'Modificado';
            case 'DELETED':
                return 'Eliminado';
            default:
                return $estado;
        }
    }
    
    
    
    public static function addFillColor($objPHPExcel,$celda,$color){
        $objPHPExcel->getActiveSheet()->getStyle($celda)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($celda)->getFill()->getStartColor()->setARGB($color);
    }
    public static function addValue($objPHPExcel,$celda,$valor){
        $objPHPExcel->getActiveSheet()->getStyle($celda)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($celda,$valor);
        $objPHPExcel->getActiveSheet()->getStyle($celda)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle($celda)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
    }
    public static function addBorderThinAll($objPHPExcel,$celdas){
        $objPHPExcel->getActiveSheet()->getStyle($celdas)->applyFromArray(
                array(
                                'borders' => array(
                                        'top'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        ),
                                        'bottom'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        ),
                                    'left'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        ),
                                        'right'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        )
                                )
                        )
            );

    }
    public static function addBorderThinL($objPHPExcel,$celdas){
        $objPHPExcel->getActiveSheet()->getStyle($celdas)->applyFromArray(
                array(
                                'borders' => array(
                                        
                                    'left'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        )
                                )
                        )
            );

    }
    public static function addBorderThinR($objPHPExcel,$celdas){
        $objPHPExcel->getActiveSheet()->getStyle($celdas)->applyFromArray(
                array(
                                'borders' => array(

                                    'right'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        )
                                )
                        )
            );

    }
    public static function addBorderThinB($objPHPExcel,$celdas){
        $objPHPExcel->getActiveSheet()->getStyle($celdas)->applyFromArray(
                array(
                                'borders' => array(

                                    'bottom'     => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        )
                                )
                        )
            );

    }
}