<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Response; 

class InfoPagoAutomaticoDetRepository extends EntityRepository
{
    
    /**
     * Función que sirve para setear un nuevo estado a los detalles de estado de cuenta con id y estado enviado como parámetro.
     * @author Edgar Holguin <fadum@netlife.net.ec>
     * @version 1.0 19-10-2020
     * @version 1.1 01-12-2020 Corrección en sentencia sql para cambio de estado.
     * @param int $intIdDetPagoAutomatico 
     * @param int $strEstado
     */
    public function setEstadoPagAutDetByPagAutId($intIdDetPagoAutomatico, $strEstado)
    {
        try
        {
            $strSql    = "UPDATE INFO_PAGO_AUTOMATICO_DET SET ESTADO=:estadoDet WHERE PAGO_AUTOMATICO_ID = :idDetPagoAutomatico";
            
            $objStmt   = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('idDetPagoAutomatico',  $intIdDetPagoAutomatico);
            $objStmt->bindValue('estadoDet',         $strEstado);
            $objStmt->execute();
            
            $strStatus = "Ok";
        }
        catch(\Exception $e)
        {
            $strStatus = "Error";
        }
        
        return $strStatus;
    }    

}
