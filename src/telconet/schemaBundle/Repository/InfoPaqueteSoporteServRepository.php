<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPaqueteSoporteServRepository extends EntityRepository
{
    //Configuración de logines y servicios
    public function putServiciosPaqueteSoporte($arrayParametros)
    {
        $strUuidPaquete = isset($arrayParametros['uuidPaquete']) ? $arrayParametros['uuidPaquete'] : '';
        $strUsuario = isset($arrayParametros['usuario']) ? $arrayParametros['usuario'] : '';
        $arrayServicios = isset($arrayParametros['servicios']) ? $arrayParametros['servicios'] : array();

        $arrayExtraParams = array('uuid_paquete' => $strUuidPaquete,
                                  'usuario' => $strUsuario,
                                  'servicios' => $arrayServicios);

        $strMensaje          = '';
        $strStatus           = '';

        try 
        {
            $strSql = "BEGIN DB_SOPORTE.SPKG_GESTION_PAQUETE_SOPORTE.P_REGISTRA_SERVICIOS_ASOCIADOS                                   (:PCL_REQUEST,
                                                                              :PV_STATUS,
                                                                              :PV_MENSAJE);
                                                                               END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect(
                $arrayConnParams['user'],
                $arrayConnParams['password'],
                $arrayConnParams['dbname']
            );

            $objStmt = oci_parse($objConn, $strSql);

            $objExtraParamsClob = oci_new_descriptor($objConn);


            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt, ':PCL_REQUEST', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PV_STATUS', $strStatus, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt, ':PV_MENSAJE', $strMensaje, 32*1024, SQLT_CHR);

            if (oci_execute($objStmt) === false) 
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMensaje);

                if (empty($strMensaje)) 
                {
                    $strMensaje = $strOCIError['message'];
                    $strMensaje = trim($strMensaje);
                }

                if (empty($strMensaje)) 
                {
                    $arrayRespuesta = array('strMensaje' => 'OK');
                } else 
                {
                    $arrayRespuesta = array('strMensaje' => $strMensaje);
                }
            }
        } catch(\Exception $e) 
        {
            $arrayRespuesta = array('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

     /*
     * Función que retorna el listado de procesos de getUuIdPaqueteSoporteServ.
     *
     * @version Initial - 1.0
     *
     * @param type $arrayParametros que contiene el servicio_id
     * @return type
     */
    public function ajaxGetServiciosSoporte($arrayParametros)
    {
        $intPersonaEmpresaRolId = $arrayParametros['persona_empresa_rol_id'];
        $strUuIdPaquete         = $arrayParametros['uuid_paquete'];
        $intIdPunto             = $arrayParametros['id_punto'];

        try 
        {
            $strQuery   = $this->_em->createQuery();
            $strSelect  = "SELECT a.loginAux, c.descripcionProducto
                FROM schemaBundle:InfoServicio a,
                     schemaBundle:InfoPunto    b,
                     schemaBundle:AdmiProducto c,
                     schemaBundle:InfoPaqueteSoporteServ d,
                     schemaBundle:InfoPaqueteSoporteCab e
                     WHERE a.puntoId             = b.id
                AND a.productoId            = c.id
                AND d.paqueteSoporteCabId   = e.id
                AND a.loginAux              = d.loginServicioSoporte
                AND e.uuidPaqueteSoporteCab = :strUuIdPaquete
                AND b.personaEmpresaRolId   = :intPersonaEmpresaRolId
                AND a.estado              IN ('Pendiente','Activo')
                AND c.empresaCod            = '10' 
                AND c.descripcionProducto   <> 'PAQUETE HORAS SOPORTE' 
                AND b.id                    = :intIdPunto 
            ";

            $strQuery->setParameter("intPersonaEmpresaRolId", $intPersonaEmpresaRolId);
            $strQuery->setParameter("strUuIdPaquete",         $strUuIdPaquete);
            $strQuery->setParameter("intIdPunto",             $intIdPunto);
            $strQuery->setDQL($strSelect);

            $arrayDatos     = $strQuery->getResult();
            $arrayTotalServ = count($arrayDatos);

            if ($arrayDatos) 
            {
                foreach ($arrayDatos as $objRegistro) 
                {
                    $arrayServicios[] = array(
                        'descripcion_producto' => $objRegistro['descripcionProducto'].' - '.$objRegistro['loginAux'],
                        'id_producto'          => $objRegistro['loginAux'],);
                }
            } else 
            {
                throw new \Exception('No hay datos que mostrar'.', notificar a Sistemas');
            }
            $arrayResultado['registros'] = $arrayServicios;
            $arrayResultado['total']     = $arrayTotalServ;
        } catch (\Exception $e) 
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
        }
        return $arrayServicios;
    }


    /*
     * Función que retorna el listado  InfoPaqueteSoporteServ por estado
     *
     * @version Initial - 1.0
     *
     * @param type $strEstado
     * @return type
    */


  public function findTodasPorEstado($strEstado)
  {
      $objQuery = $this->_em->createQuery("SELECT app 
                FROM 
            schemaBundle:InfoPaqueteSoporteServ app
        WHERE 
                        app.estado='".$strEstado."'");
      $objDatos=$objQuery->getResult();
      return $objDatos;
  }


    /*
     * Función que retorna la informaciòn de la tabla InfoPaqueteSoporteServ filtrado por el login
     *
     * @version Initial - 1.0
     *
     * @param type $strEstado
     * @return type
     * 
    */


    public function soporteServPorLogin($arrayParametros)
    {
        $strLoginPuntoSoporte = $arrayParametros['loginPuntoSoporte'];
        try 
        {
            $objQuery   = $this->_em->createQuery();
            $strSelect  = "SELECT app.paqueteSoporteCabId, app.loginPuntoSoporte 
                    FROM schemaBundle:InfoPaqueteSoporteServ app
                    WHERE app.loginPuntoSoporte = :strLoginPuntoSoporte
                    ";
            $objQuery->setParameter("strLoginPuntoSoporte", $strLoginPuntoSoporte);
            $objQuery->setDQL($strSelect);

            $arrayResultado = $objQuery->getArrayResult(); 
        }
        catch (\Exception $e) 
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
        }
        return $arrayResultado;
    }
}
