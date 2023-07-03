<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoContratoFormaPagoRepository extends EntityRepository
{

    public function findPorContratoIdYEstado($contratoid, $estado)
    {
        $query = $this->_em->createQuery('SELECT icfp
            FROM 
                schemaBundle:InfoContratoFormaPago icfp
            WHERE 
                icfp.contratoId = :contratoid AND icfp.estado = :estado');
        $query->setParameters(array('contratoid' => $contratoid, 'estado' => $estado));
        $datos = $query->getOneOrNullResult();
        return $datos;
    }

    public function findPorContratoId($contratoid)
    {
        $query = $this->_em->createQuery("SELECT icfp
            FROM 
                schemaBundle:InfoContratoFormaPago icfp
            WHERE 
                icfp.contratoId = :contratoid AND icfp.estado in ('Activo','Pendiente')");
        $query->setParameter('contratoid', $contratoid);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }
    
     /**
     * getUltimoHistorialFormaPago
     * 
     * Función retorna el último historial de forma de pago
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 25-03-2020 
     * 
     * @param integer  $intContratoId
     * 
     * @return array $strDatos
     * 
     */
    public function getUltimoHistorialFormaPago($intContratoId) 
    {
        $strQuery   = $this->_em->createQuery();        
        $strSelect  = " SELECT                     
                            icfph
                       FROM 
                            schemaBundle:InfoContratoFormaPagoHist icfph
                       WHERE 
                            icfph.id = ( SELECT MAX(icfph1.id)
                                         FROM   schemaBundle:InfoContratoFormaPagoHist icfph1
                                         WHERE icfph1.contratoId = :intContratoId) ";
        $strQuery->setParameter("intContratoId", $intContratoId);      
        $strQuery->setDQL($strSelect);
        $objDatos = $strQuery->getOneOrNullResult();
        return $objDatos;
    }    

    /**
     * getHistorialFormaPagoPorFecha
     * 
     * Función que retorna las formas de pago mayor a una fecha Inicial de busqueda
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 03-11-2020 
     * 
     * @param array $arrayParams[
     *                                  $intContratoId          : Id Contrato
     *                                  $strFechaInicial        : Fecha inicial de busqueda
     *                                  $intBancoTipoCuentaId   : Id de Banco
     *                                  $intNumeroCtaTarjeta    : Numero de Tarjeta
     *                                  $intTipoCuentaId        : Id Tipo de cuenta
     *                                  $intFormaPagoId         : Id Forma de pago
     *                          ]
     * 
     * @return array $objDatos El historial de forma de pago 
     * 
     */
    public function getHistorialFormaPagoPorFecha($arrayParams) 
    {
        $intContratoId          = $arrayParams["intContratoId"];
        $strFechaInicial        = $arrayParams["strFechaInicial"];
        $intBancoTipoCuentaId   = $arrayParams["intBancoTipoCuentaId"];
        $intNumeroCtaTarjeta    = $arrayParams["intNumeroCtaTarjeta"];
        $intTipoCuentaId        = $arrayParams["intTipoCuentaId"];
        $intFormaPagoId         = $arrayParams["intFormaPagoId"];

        $strQuery   = $this->_em->createQuery();        
        $strSelect  = "SELECT                     
                            icfph
                       FROM 
                            schemaBundle:InfoContratoFormaPagoHist icfph
                       WHERE icfph.contratoId = :intContratoId 
                        AND  icfph.id = ( SELECT MAX(icfph1.id)
                                         FROM   schemaBundle:InfoContratoFormaPagoHist icfph1
                                         WHERE icfph1.contratoId = :intContratoId)";
        $strQuery->setParameter("intContratoId", $intContratoId); 
        if(isset($intBancoTipoCuentaId))
        {
            $strSelect .= " AND icfph.bancoTipoCuentaId=:intBancoTipoCuentaId";
            $strQuery->setParameter("intBancoTipoCuentaId", $intBancoTipoCuentaId);
        }
        if(isset($intNumeroCtaTarjeta))
        {
            $strSelect .= " AND icfph.numeroCtaTarjeta=:intNumeroCtaTarjeta";
            $strQuery->setParameter("intNumeroCtaTarjeta", $intNumeroCtaTarjeta);
        }
        if(isset($intTipoCuentaId))
        {
            $strSelect .= " AND icfph.tipoCuentaId=:intTipoCuentaId";
            $strQuery->setParameter("intTipoCuentaId", $intTipoCuentaId);
        }
        if(isset($intFormaPagoId))
        {
            $strSelect .= " AND icfph.formaPago=:intFormaPagoId";
            $strQuery->setParameter("intFormaPagoId", $intFormaPagoId);
        }
        if(isset($strFechaInicial))
        {
            $strSelect .= " AND icfph.feCreacion > :dateInicial";            
            $strQuery->setParameter("dateInicial", $strFechaInicial);
        }
        
        $strQuery->setDQL($strSelect);
        $objDatos = $strQuery->getResult();
        return $objDatos;
    }    

    /** 
     * getContratoConCambios
     * Función que retorna la forma de pago que no ha sido cambiada
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 03-11-2020 
     * 
     * @param int $intContratoId Id de contrato
     * 
     * @return array Contrato que haya sido modificado
     * 
    */
    public function getContratoConCambios($intContratoId)
    {
        
        $strQuery   = $this->_em->createQuery();        
        $strSelect  = "SELECT                     
                            icfp
                       FROM 
                            schemaBundle:InfoContratoFormaPago icfp
                       WHERE icfp.contratoId = :intContratoId 
                        AND  icfp.feUltMod IS NOT NULL";
                        
        $strQuery->setParameter("intContratoId", $intContratoId); 
        $strQuery->setDQL($strSelect);
        
        $arrayDatos = $strQuery->getResult();
        return $arrayDatos;
    }
}
