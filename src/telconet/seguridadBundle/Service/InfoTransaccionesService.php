<?php

namespace telconet\seguridadBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use telconet\schemaBundle\Entity\InfoTransacciones;

/**
 * Clase InfoTransaccionesService
 *
 * @since 18-03-2016
 * @version 1.0 
 * @author Edson Franco <efranco@telconet.ec>
 */
class InfoTransaccionesService
{       
    private $emSeguridad;    
    
    function setDependencies(ContainerInterface $container)
    {        
        $this->emSeguridad = $container->get('doctrine.orm.telconet_seguridad_entity_manager');           
    }

    /**
     * Documentación para el método 'guardarTransaccion'
     * 
     * Funcion que se de insertar un registro en la tabla 'INFO_TRANSACCIONES'
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0
     * @since 18-03-2016     
     * @param array $arrayParametros  ['intStart', 'intLimit', 'empresaSession', 'estadoTransaccion', 'usuarioSession', 'ipSession', 
     *                                 'nombreTransaccion', 'tipoTransaccion', 'criterios' => ( 'nombreModulo', 'nombreAccion', 'estadosModulo', 
     *                                                                                          'estadosAcciones' ) ] 
     * @throws Exception 
     * @return boolean $boolGuardado
     */
    public function guardarTransaccion($arrayParametros)
    {
        $boolGuardado = false;
        
        $this->emSeguridad->getConnection()->beginTransaction();	

        try
        {
            $arrayRelacionesSistema = $this->emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                        ->getRelacionSistemaByCriterios($arrayParametros);
            $arrayRegistros         = $arrayRelacionesSistema['registros'];
            $objRelacionSistema     = $arrayRegistros ? $arrayRegistros[0] : null;
                    
            $objInfoTransacciones = new InfoTransacciones();
            $objInfoTransacciones->setEmpresaId($arrayParametros['empresaSession']);
            $objInfoTransacciones->setEstado($arrayParametros['estadoTransaccion']);
            $objInfoTransacciones->setFeCreacion(new \DateTime());
            $objInfoTransacciones->setUsrCreacion($arrayParametros['usuarioSession']);
            $objInfoTransacciones->setIpCreacion($arrayParametros['ipSession']);
            $objInfoTransacciones->setNombreTransaccion($arrayParametros['nombreTransaccion']);
            $objInfoTransacciones->setTipoTransaccion($arrayParametros['tipoTransaccion']);
            $objInfoTransacciones->setRelacionSistemaId($objRelacionSistema);
            
            $this->emSeguridad->persist($objInfoTransacciones);
            $this->emSeguridad->flush();	

            $this->emSeguridad->getConnection()->commit();
            
            $boolGuardado = true;
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $this->emSeguridad->getConnection()->rollback();
        }//try
        
        $this->emSeguridad->getConnection()->close();
        
        return $boolGuardado;
    }
}
