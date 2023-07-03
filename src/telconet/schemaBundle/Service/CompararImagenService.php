<?php

namespace telconet\schemaBundle\Service;

/**
 * Clase para comparar dos imagenes.
 * @author jnazareno 23/10/2019
 */
class CompararImagenService
{	
	/**
     * Función que reduce la imagen para su posterior comparación
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $arrayParametros
     * @return $strImagenTemporal
     */
    private function reduceImagen($arrayParametros)								
	{
		$strTipoImagen	= $arrayParametros['strTipoImagen'];
		$strArchivo 	= $arrayParametros['strArchivo'];
		$intAncho 		= $arrayParametros['intSize'];
		$intAlto 		= $arrayParametros['intSize'];

		$strImagenOriginal  = null;
		
        if ($strTipoImagen == 'png')
        {		
			$strImagenOriginal = imagecreatefrompng($strArchivo);
        }

        if ($strTipoImagen == 'jpg')
        {
			$strImagenOriginal = imagecreatefromjpeg($strArchivo);			
        }

		$intAnchoMaximo     = $intAncho;
		$intAltoMaximo      = $intAlto;
		list($intAnchoImagen, $intAltoImagen) = getimagesize($strArchivo);
		$intProporcionX     = $intAnchoMaximo/$intAnchoImagen;
		$intProporcionY     = $intAltoMaximo/$intAltoImagen;
		
		if(($intAnchoImagen <= $intAnchoMaximo) && ($intAltoImagen <= $intAltoMaximo))
		{
			$intAnchoFinal = $intAnchoImagen;
			$intAltoFinal  = $intAltoImagen;
		}
		elseif (($intProporcionX * $intAltoImagen) < $intAltoMaximo)
		{
			$intAltoFinal  = ceil($intProporcionX * $intAltoImagen);
			$intAnchoFinal = $intAnchoMaximo;
		}
		else
		{
			$intAnchoFinal = ceil($intProporcionY * $intAnchoImagen);
			$intAltoFinal  = $intAltoMaximo;
		}
		
        $strImagenTemporal  =	imagecreatetruecolor($intAnchoFinal, 
                                                        $intAltoFinal);	
        
        imagecopyresampled($strImagenTemporal, 
                            $strImagenOriginal, 
                            0, 
                            0, 
                            0, 
                            0, 
                            $intAnchoFinal, 
                            $intAltoFinal, 
                            $intAnchoImagen, 
                            $intAltoImagen);

		imagedestroy($strImagenOriginal);
		
		return $strImagenTemporal;
	}
 
    /**
     * Función que convierte imagen a escala de grises.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $strImagen
     */
	private function convertirImagenBW($strImagen)
	{
		imagefilter($strImagen, IMG_FILTER_GRAYSCALE);
	}

    /**
     * Función que calcula cual es el color promedio más usado.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $strImagen
     * @return $intColorR
     */
	private function calcularColorPromedio($strImagen)
	{
		$intAncho   = imagesx($strImagen);
		$intAlto    = imagesy($strImagen);
        $intColorR  = 0;
		
		for($intY = 0; $intY < $intAlto; $intY++)
		{
			for($intX = 0; $intX < $intAncho; $intX++)
			{
				$intRGB     = imagecolorat($strImagen, $intX, $intY);
				$intColorR  += $intRGB >> 16;
			}
		}
		
		$intPixeles = $intAncho * $intAlto;
		$intColorR = (round($intColorR / $intPixeles));

        if($intColorR < 10)
        {
            $intColorR = 0;
        }
			
        if($intColorR > 245)
        {
            $intColorR = 255;
        }
			
		return $intColorR; 
	} 
	
    /**
     * Función que obtiene un array con el arrayMatriz.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $strImagen, $intAvg
     * @return $arrayMatriz
     */
	private function calcularHash($arrayParametros)
	{
		$strImagen 	= 	$arrayParametros['strImagen'];
		$intAvg 	=	$arrayParametros['intAvg'];

		$intAncho   = imagesx($strImagen);
		$intAlto    = imagesy($strImagen);
		
		$arrayMatriz = array();		
		
        $intColorR = 0;
		
		for($intY = 0; $intY < $intAlto; $intY++)
		{
			$strFila = "";
			
			for($intX = 0; $intX < $intAncho; $intX++)
			{
				$intRGB = imagecolorat($strImagen, $intX, $intY);
				$intColorR   = $intRGB >> 16;
				
				if($intColorR <= $intAvg)
				{
					$strFila = $strFila."0";
				}
				else
				{
					$strFila = $strFila."1";
				}
			}
			$arrayMatriz[$intY]=$strFila;
		}
		
		return $arrayMatriz;
	}
	
    /**
     * Función que vuelca el array a un string.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $arrayValor
     * @return $strHashConcatenado
     */
	private function concatenarArray($arrayValor)
	{
		$strHashConcatenado = "";
		
		for ($intPosicion = 0; $intPosicion <= count($arrayValor)-1; $intPosicion++)
		{
			$strImagenTemporal  = $arrayValor[$intPosicion];
			$strHashConcatenado = $strHashConcatenado.$strImagenTemporal;
        }
        
		return $strHashConcatenado;
	}
	
    /**
     * Función que genera todo el proceso de hasheo de una imagen.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $arrayParametros
     * @return $arrayMatriz
     */
	private function hashImagen($arrayParametros)
	{
		$strArchivo 	= $arrayParametros['strImagen'];
		$strTipoImagen 	= $arrayParametros['strExtencion'];
		$intSize 		= 8;

		$arrayData  = array(
			'strTipoImagen'	=> $strTipoImagen, 
			'strArchivo'   	=> $strArchivo,
			'intSize'		=> $intSize,
		);

		$strImagen  = $this->reduceImagen($arrayData);
		$this->convertirImagenBW($strImagen);
		$intAvg  	= $this->calcularColorPromedio($strImagen);

		$arrayData2  = array(
			'strImagen'	=> $strImagen, 
			'intAvg'   	=> $intAvg
		);

		$arrayMatriz = $this->calcularHash($arrayData2);
		
		return $arrayMatriz;
	}
	
    /**
     * Función que calcula el porcentaje de diferencias
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $arrayHash1, $arrayHash2
     * @return $intDiferencia
     */
	private function calcularPorcentajeDiferencia($arrayParametros)
	{
		$arrayHash1 = $arrayParametros['arrayHash1'];
		$arrayHash2 = $arrayParametros['arrayHash2'];
		$intFallo = 0;	
		
		for($intX = 0; $intX <= strlen($arrayHash2)-1; $intX++)
		{
			if($arrayHash1[$intX] != $arrayHash2[$intX])
			{
				$intFallo++;
			}
		}
		
		$intDiferencia = ($intFallo*100)/strlen($arrayHash2);
		
		return $intDiferencia;
	}
	
    /**
     * Función que obtiene la extension del archivo.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $strArchivo
     * @return string
     */
	private function obtenerExtencionArchivo($strArchivo)
	{
		return pathinfo($strArchivo, PATHINFO_EXTENSION);
	}
	
    /**
     * Función que compara dos imagenes.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 23-10-2019
	 *
     * @param $strImagen1, strImagen2
     * @return $intDiferencia
     */
	public function compararImagenes($arrayParametros)
	{
		$strImagen1 = $arrayParametros['strImagen1'];
		$strImagen2	= $arrayParametros['strImagen2'];

		$strExtencion1  = $this->obtenerExtencionArchivo($strImagen1);

		$arrayData  = array(
			'strImagen'			=> $strImagen1, 
			'strExtencion'   	=> $strExtencion1
		);

		$arrayHash1     = $this->hashImagen($arrayData);
		$arrayStrHash1  = $this->concatenarArray($arrayHash1);
		
		$strExtencion2  = $this->obtenerExtencionArchivo($strImagen2);

		$arrayData  = array(
			'strImagen'			=> $strImagen2, 
			'strExtencion'   	=> $strExtencion2
		);

		$arrayHash2     = $this->hashImagen($arrayData);
		$arrayStrHash2  = $this->concatenarArray($arrayHash2);

		$arrayData2  = array(
			'arrayHash1'	=> $arrayStrHash1, 
			'arrayHash2'   	=> $arrayStrHash2
		);
		
		$intDiferencia  = $this->calcularPorcentajeDiferencia($arrayData2);
		return (100 - round($intDiferencia, 2));
	}
}
