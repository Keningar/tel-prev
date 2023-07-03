<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class VistaEstadoCuentaResumidoRepository extends EntityRepository
{
    
	/**
     * Devuelve el saldo de un cliente correspondiente al empresaCod e identificacionCliente dados.
     * No importa si el cliente no tiene roles activos.
     * Devuelve una estructura con los siguientes campos:
     * id, identificacionCliente, razonSocial, nombres, apellidos,
     * saldo (suma de saldos de todos los puntos del cliente, tomado de VistaEstadoCuentaResumido) 
     * @author ltama
     */
    public function findSaldoPorEmpresaPorIdentificacion($empresaCod, $identificacionCliente)
    {
        $query = $this->_em->createQuery("
            SELECT
                per.id,per.identificacionCliente,per.razonSocial,per.nombres,per.apellidos,
                SUM(est.saldo) AS saldo
            FROM schemaBundle:VistaEstadoCuentaResumido est
                JOIN schemaBundle:InfoPunto pun WITH pun.id=est.id
                JOIN schemaBundle:InfoPersonaEmpresaRol rol WITH rol.id=pun.personaEmpresaRolId
                JOIN schemaBundle:InfoEmpresaRol emp WITH emp.id=rol.empresaRolId
                JOIN schemaBundle:InfoPersona per WITH per.id=rol.personaId
            WHERE emp.empresaCod=:empresaCod
                AND per.identificacionCliente=:identificacionCliente
            GROUP BY
                per.id,per.identificacionCliente,per.razonSocial,per.nombres,per.apellidos
        ");
        $query->setParameters(array(
            'empresaCod' => $empresaCod,
            'identificacionCliente' => $identificacionCliente,
        ));
        return $query->getOneOrNullResult ();
    }
	
    /**
     * Documentación para getSaldoPorEmpresaPorIdentificacion
     * 
     * Función que consulta saldo de Cliente por empresa e identificacion
     * 
     * @param $empresaCod, $identificacionCliente  
     * @return Retorna parametros devueltos por Procedure
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.0 29-05-2022
     * 
     * @author Erick Melgar <emelgar@telconet.ec>
     * @version 1.1 04/07/2022
     * Se incrementa las dimesiones de las variables strNombreCliente
     
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.2 06-07-2022 - Cambio de parametros de entrada y salida en llamado de SP.
     */
    public function getSaldoPorEmpresaPorIdentificacion($arrayRequest)
    {
            
        try
        {
            if( !is_null($arrayRequest['codigoExternoEmpresa']) && !is_null($arrayRequest['identificacionCliente']) ) 
            {
                $strMensaje  = "";
                $strMensaje  = str_pad($strMensaje, 100, " "); 
                $strResponse  = "";
                $strResponse  = str_pad($strResponse, 900, " ");
                $strEstado  = "";
                $strEstado  = str_pad($strEstado, 50, " "); 

                $strRequest = json_encode((array) $arrayRequest);
                $strSql = "BEGIN DB_FINANCIERO.FNCK_PAGOS_LINEA.P_CONSULTAR_SALDO_POR_IDENTIF(:Pcl_Request,".
                ":Pv_Status,:Pv_Mensaje, :Pcl_Response); END;";
                                                               
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pcl_Request', $strRequest);
                $objStmt->bindParam('Pv_Status', $strEstado);
                $objStmt->bindParam('Pv_Mensaje', $strMensaje);
                $objStmt->bindParam('Pcl_Response', $strResponse);
                $objStmt->execute();

                $objJson = json_decode($strResponse);
            }
        }
        catch(\Exception $ex)
        {
           throw($ex);
           
        }

        return array('nombreCliente'     => $objJson->nombreCliente,
                     'saldo'             => $objJson->saldoAdeudado,
                     'numeroContrato'    => $objJson->numeroContrato,
                     'identificacionCliente' => $arrayRequest['identificacionCliente']
                    );
    }
}