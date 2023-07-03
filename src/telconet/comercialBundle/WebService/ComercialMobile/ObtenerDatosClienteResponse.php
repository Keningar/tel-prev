<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Clase que contiene el objeto Response que estara conformado por un objeto persona y un arreglo de puntos 
 * 
 * @author Jose Vinueza <jdvinueza@telconet.ec>
 * @version 1.0 22-08-2017
 */
class ObtenerDatosClienteResponse 
{
    public $objPersona;
    public $arrayPuntos;
    
    
    /**
     * Constructor que recibe los siguientes parametros
     * @param PersonaComplexType $objPersona
     * @param array $arrayPuntos        
     */
    public function __construct($arrayPuntospm, PersonaComplexType $objPersona = null ) 
    {
        unset($objPersona->id);
        unset($objPersona->formaPagoId);
        unset($objPersona->tipoCuentaId);
        unset($objPersona->bancoTipoCuentaId);
        unset($objPersona->roles);
        unset($objPersona->datosPersonaEmpresaRol);
        unset($objPersona->origenIngresos);
        unset($objPersona->referidoId);
        unset($objPersona->referidoNombre);
        
        $arrayPuntos = array();
        foreach ($arrayPuntospm as $objPunto)
        {
            unset($objPunto['idPto']);
            unset($objPunto['rol']);
            unset($objPunto['ptoCoberturaId']);
            unset($objPunto['cantonId']);
            unset($objPunto['parroquiaId']);
            unset($objPunto['sectorId']);
            unset($objPunto['esedificio']);
            unset($objPunto['nombreEdificio']);
            unset($objPunto['dependedeedificio']);
            unset($objPunto['puntoedificioid']);
            unset($objPunto['tipoNegocioId']);
            unset($objPunto['tipoUbicacionId']);            
            unset($objPunto['personaId']);
            unset($objPunto['file']);
            unset($objPunto['fileDigital']);
            unset($objPunto['loginVendedor']);
            $arrayFormasContacto = array();
            $arrayServicios = array();
            foreach($objPunto['formasContacto'] as $objContacto)
            {
                unset($objContacto['idPersonaFormaContacto']);
                unset($objContacto['idPersona']);
                unset($objContacto['idFormaContacto']);
                $arrayFormasContacto[] = $objContacto;
            }
            foreach($objPunto['servicios'] as $objServicio)
            {
                unset($objServicio['id']);
                unset($objServicio['puntoId']);
                unset($objServicio['productoId']);
                unset($objServicio['planId']);
                unset($objServicio['ultimaMillaId']);
                unset($objServicio['nombreUltimaMilla']);
                $arrayServicios[] = $objServicio;
            }
            $objPunto['servicios'] = $arrayServicios;
            $objPunto['formasContacto'] = $arrayFormasContacto;
            $arrayPuntos[] = $objPunto;
        }
        
        $this->objPersona = $objPersona;
        $this->arrayPuntos = $arrayPuntos;       
    }
    
}
