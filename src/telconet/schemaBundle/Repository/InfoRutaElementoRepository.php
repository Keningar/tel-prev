<?php

namespace telconet\schemaBundle\Repository;

use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Documentación para la clase 'InfoRutaElementoRepository'.
 *
 * Clase que contiene todas las consultas a la Entidad InfoRutaEstatica
 *
 * @author Kenneth Jimenez <kjimenez@telconet.ec>
 * @version 1.0 26-12-2015
*/
class InfoRutaElementoRepository extends BaseRepository
{
    /**
     * Documentación para el método 'getRutasEstaticas'.
     *
     * Método utilizado para obtener las rutas estaticas de un servicio
     *
     * @param int idServicio id del servicio a buscar las rutas estaticas
     * @param int start min de registros de rutas estaticas a buscar.
     * @param int limit max de registros de rutas estaticas a buscar.
     *
     * @return array arrayResultado
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 26-12-2015
    */
    public function getRutasEstaticas($idServicio,$start,$limit)
    {
        $arrayResultado = array(
                                 'total' => 0,
                                 'data'  => array()
                               );
        
        
        if($idServicio > 0)
        {
        
            $query = $this->_em->createQuery(null);
            
            $select = "SELECT 
                        ire.id,
                        ire.nombre,
                        ire.redLan,
                        ire.mascaraRedLan,
                        ire.distanciaAdmin,
                        ire.usrCreacion,
                        to_char(ire.feCreacion) as feCreacion,
                        ip.ip ipDestino ";
            $dql =  "FROM
                        schemaBundle:InfoRutaElemento ire,
                        schemaBundle:InfoIp ip
                    WHERE 
                        ire.estado     = :estado
                    AND ip.estado      = :estado    
                    AND ire.servicioId = ip.servicioId
                    AND ire.servicioId = :servicioId";

            $query->setParameter('servicioId', $idServicio);
            $query->setParameter('estado', "Activo");
            
            //registros
            $query->setDQL($select.$dql);   
            if($start!='' && $limit!='') 
            {    
                $query->setFirstResult($start)->setMaxResults($limit);        
            }
            $arrayRutasEstaticas = $query->getArrayResult();
            
            
            $arrayResultado = array(
                                    'total' => count($arrayRutasEstaticas) ,
                                    'data'  => $arrayRutasEstaticas
                                );

        }
        
        return $arrayResultado;
    }
    
    /**
     * Obtiene todas las rutas del servicio por estados
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 06-04-2016
     * 
     * @param array   $arrayParametros
     * 
     * @return result
     **/
    public function getRutasPorServicioPorEstados( $arrayParametros ) {
        
        $em  = $this->_em;
        $sql = "SELECT
                    ire
                FROM
                    schemaBundle:InfoRutaElemento ire
                WHERE 
                ire.servicioId = :servicioIdParam
                AND ire.estado in (:estadosParam)";

        $query = $em->createQuery($sql);
        $query->setParameter('servicioIdParam', $arrayParametros['idServicio']);
        $query->setParameter('estadosParam',    $arrayParametros['arrayEstados']);

        return $query->getResult();
    }
    
    /**
     * Obtiene todas las rutas del servicio por estados en un ajax
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 06-04-2016
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2018     Se agrega parametro arrayTipos para filtrar información según la necesidad
     * @since 1.0
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 04-07-2018     Se devuelve la subred para rutas estaticas con su respectiva mascara de subred concatenada
     * @since 1.1
     * 
     * @param array   $arrayParametros
     * 
     * @return result
     **/
    public function getAjaxRutasPorServicioPorEstados( $arrayParametros ) {
        
        if($arrayParametros)
        {
            $rsm   = new ResultSetMappingBuilder($this->_em);         
            $query = $this->_em->createNativeQuery(null, $rsm);                              

            $strSelectCont = "SELECT COUNT(*) AS CONT  ";

            $strSql = "SELECT  IRE.ID_RUTA_ELEMENTO                 AS ID_RUTA_ELEMENTO,
                               IRE.NOMBRE                           AS NOMBRE_RUTA_ELEMENTO,
                               IIP.IP                               AS IP,
                               (CASE 
                                  WHEN IRE.RED_LAN IS NULL THEN ISU.SUBRED
                                  ELSE
                                    CASE IRE.MASCARA_RED_LAN
                                      WHEN '255.255.255.255' THEN IRE.RED_LAN||'/32'
                                      WHEN '255.255.255.254' THEN IRE.RED_LAN||'/31'
                                      WHEN '255.255.255.252' THEN IRE.RED_LAN||'/30'
                                      WHEN '255.255.255.248' THEN IRE.RED_LAN||'/29'
                                      WHEN '255.255.255.240' THEN IRE.RED_LAN||'/28'
                                      WHEN '255.255.255.224' THEN IRE.RED_LAN||'/27'
                                      WHEN '255.255.255.192' THEN IRE.RED_LAN||'/26'
                                      WHEN '255.255.255.128' THEN IRE.RED_LAN||'/25'
                                      WHEN '255.255.255.0' THEN IRE.RED_LAN||'/24'
                                      ELSE IRE.RED_LAN
                                    END                         
                               END) AS SUBRED,
                               NVL(IRE.MASCARA_RED_LAN,ISU.MASCARA) AS MASCARA,
                               IRE.TIPO                             AS TIPO,
                               IRE.FE_CREACION                      AS FE_CREACION";
            
            $from = " FROM INFO_RUTA_ELEMENTO IRE 
                         JOIN INFO_IP            IIP     ON      IIP.ID_IP        = IRE.IP_ID
                         LEFT JOIN INFO_SUBRED   ISU     ON      ISU.ID_SUBRED    = IRE.SUBRED_ID
                      WHERE  IRE.SERVICIO_ID  = :servicioIdParam
                         AND IRE.ESTADO       in (:estadosParam) 
                         AND IRE.TIPO         in (:tiposParam) 
                      ORDER BY IRE.FE_CREACION DESC";

            $rsm->addScalarResult('ID_RUTA_ELEMENTO'        ,'idRutaElemento'       ,'string');
            $rsm->addScalarResult('NOMBRE_RUTA_ELEMENTO'    ,'nombreRutaElemento'   ,'string');
            $rsm->addScalarResult('IP'                      ,'ip'                   ,'string');
            $rsm->addScalarResult('SUBRED'                  ,'subred'               ,'string');
            $rsm->addScalarResult('MASCARA'                 ,'mascara'              ,'string');
            $rsm->addScalarResult('TIPO'                    ,'tipo'                 ,'string');
            $rsm->addScalarResult('FE_CREACION'             ,'feCreacion'           ,'string');
            //...
            $rsm->addScalarResult('CONT'                    ,'total'                ,'integer');
            
            $query->setParameter('servicioIdParam', $arrayParametros['idServicio']);
            $query->setParameter('estadosParam',    $arrayParametros['arrayEstados']);
            $query->setParameter('tiposParam',      $arrayParametros['arrayTipos']);
            
            // Se obtiene el total de los datos 
            $query->setSQL($strSelectCont.$from);      
            $arrayResultado['total'] = $query->getSingleScalarResult(); 
            //registros
            $query->setSQL($strSql.$from);   
            if($arrayParametros['start']!='' && $arrayParametros['limit']!='') 
            {
                $objQuery = $this->setQueryLimit($query,$arrayParametros['limit'],$arrayParametros['start']);   
            }
            $arrayResultado['encontrados'] = $objQuery->getArrayResult();
            
        }
        return json_encode( $arrayResultado );
    }
    
    
    public function getAjaxRutasPorServicioPorEstadosDQL( $arrayParametros ) {
        
        $em  = $this->_em;
        $sql = "SELECT
                    ire.id                                      AS id,
                    iip.ip                                      AS ip,
                    COALESCE(ire.redLan, isu.subred)            AS subred,
                    COALESCE(ire.mascaraRedLan, isu.mascara)    AS mascara,
                    CASE
                        WHEN ire.redLan is null 
                          THEN 'Automatico' 
                          ELSE 'Estatico' 
                    END                                         AS tipo,
                    ire.feCreacion                              AS feCreacion,
                FROM
                    schemaBundle:InfoRutaElemento ire
                    JOIN schemaBundle:InfoIp iip WITH iip.id = ire.ipId
                    LEFT JOIN schemaBundle:InfoSubred isu WITH isu.id  = ire.subredId 
                WHERE 
                ire.servicioId = :servicioIdParam
                AND ire.estado in (:estadosParam)";

        $query = $em->createQuery($sql);
        $query->setParameter('servicioIdParam', $arrayParametros['idServicio']);
        $query->setParameter('estadosParam',    $arrayParametros['arrayEstados']);
        
        $arrayQuery = $query->getResult();
        $count      = count($arrayQuery);
        
        if($arrayParametros['start']!='' && $arrayParametros['limit']!='') 
        {    
            $query->setFirstResult($arrayParametros['start'])->setMaxResults($arrayParametros['limit']);        
        }
                
        $resultado= array('total'        => $count ,
                          'encontrados'  => $query->getArrayResult());
        
        return json_encode( $resultado);
    }

    

    /**
     * Obtiene las Ruta por servicio y trae la subred
     * 
     * @author Brenyx Giraldo <agiraldo@telconet.ec>
     * @version 1.0 14-06-2022 
     * 
     * @param array   $arrayParametros
     * 
     * @return result
     **/
    public function getSubredRutasPorServicio( $arrayParametros ) 
    {
        
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsm);

        $intIdServicio      = (int)$arrayParametros['idServicio'];
        $strEstadoIp        = $arrayParametros['strEstadoIp'];
        
        try
        {
            $strSql = "  SELECT INFP.IP,IRE.FE_CREACION,INFR.SUBRED,INFR.MASCARA,INFR.IP_INICIAL,
            INFR.IP_FINAL,INFR.TIPO,INFR.GATEWAY
            FROM DB_INFRAESTRUCTURA.INFO_RUTA_ELEMENTO IRE
            INNER JOIN DB_INFRAESTRUCTURA.INFO_SUBRED INFR ON INFR.ID_SUBRED = IRE.SUBRED_ID
            INNER JOIN DB_INFRAESTRUCTURA.INFO_IP INFP ON IRE.IP_ID = INFP.ID_IP
            WHERE IRE.ESTADO = :Lv_Estado
            AND INFP.ESTADO = :Lv_Estado
            AND IRE.SERVICIO_ID = :Ln_IdServicio ";
            
            $objQuery->setSQL($strSql);
            $objQuery->setParameter('Ln_IdServicio',$intIdServicio);
            $objQuery->setParameter('Lv_Estado',$strEstadoIp);
            
            $objRsm->addScalarResult('IP'                          ,'strIp'               ,'string');
            $objRsm->addScalarResult('FE_CREACION'                 ,'srtFechaCreacion'    ,'string');
            $objRsm->addScalarResult('SUBRED'                      ,'strSubred'           ,'string');
            $objRsm->addScalarResult('MASCARA'                     ,'strMascara'          ,'string');
            $objRsm->addScalarResult('IP_INICIAL'                  ,'strIpInicial'        ,'string');
            $objRsm->addScalarResult('IP_FINAL'                    ,'strIpFinal'          ,'string');
            $objRsm->addScalarResult('TIPO'                    ,'strTipoIp'          ,'string');
            $objRsm->addScalarResult('GATEWAY'                     ,'strGateway'          ,'string');
            
            $arrayResultado = $objQuery->getArrayResult();
                
        }
        catch(\Exception $e)
        {
            $strRespuesta   = " Error al obtener el cliente por su login. Favor Notificar a Sistemas";
            $arrayResultado = array ('strMensaje'           =>$strRespuesta);
            
            return $arrayResultado;
        }

        return $arrayResultado;
    }

}

