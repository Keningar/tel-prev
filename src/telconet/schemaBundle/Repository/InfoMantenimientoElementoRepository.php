<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoMantenimientoElementoRepository extends EntityRepository
{
    
    /**
      * getJsonMantenimientosXParametros
      *
      * Método que retornará los mantenimientos ingresados a un determinado transporte                                  
      *
      * @param array $parametros[
      *                             "idElemento"        : id del vehículo
      *                             "idOrdenTrabajo"    : id de la orden de trabajo
      *                             "estado"            : estado de la orden mantenimiento
      *                             "intStart"          : inicio del rownum
      *                             "intLimit"          : fin del rownum
      *                         ]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 02-08-2016
      */
    public function getJSONMantenimientosXParametros($parametros)
    {
        $arrayEncontrados   = array();
        $arrayResultado     = $this->getResultadoMantenimientosXParametros($parametros);
        $resultado          = $arrayResultado['resultado'];
        $intTotal           = $arrayResultado['total'];
        $total              = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayEncontrados[]=array(
                                            'idMantenimientoElemento'   => $data["id"],
                                            'numeroOrdenTrabajo'        => ($data["verNumeracion"]=="SI") ? $data["numeroOrdenTrabajo"] : "",
                                            'tipoMantenimiento'         => $data["tipoMantenimiento"],
                                            'kmActual'                  => $data["kmActual"],
                                            'valorTotal'                => $data["valorTotal"],
                                            'usrCreacion'               => $data["usrCreacion"],
                                            'estado'                    => $data["estado"],
                                            'fechaInicio'               => date_format($data["feInicio"], "Y-m-d"),
                                            'fechaFin'                  => date_format($data["feFin"], "Y-m-d"),
                                            'usrCreacion'               => $data["usrCreacion"],
                                            'fechaCreacion'             => date_format($data["feCreacion"], "Y-m-d G:i")
                                    );
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    
    /**
      * getResultadoMantenimientosXParametros
      *
      * Método que obtendrá los mantenimientos de los transportes                                
      *
      * @param array $parametros[
      *                             "idElemento"        : id del vehículo
      *                             "idOrdenTrabajo"    : id de la orden de trabajo
      *                             "estado"            : estado de la orden mantenimiento
      *                             "intStart"          : inicio del rownum
      *                             "intLimit"          : fin del rownum
      *                         ]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 02-08-2016
     * 
      */
    public function getResultadoMantenimientosXParametros($parametros)
    {
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        try
        {
            $query          = $this->_em->createQuery();
            $queryCount     = $this->_em->createQuery();
            $strWhere       = "";
            $sqlSelect      = " SELECT me.id,ot.numeroOrdenTrabajo,
                                otCaractTipoMant.valor as tipoMantenimiento,otCaractKm.valor as kmActual,otCaractNumeracion.valor as verNumeracion,
                                me.feInicio,me.feFin,me.usrCreacion,me.feCreacion,
                                me.valorTotal, me.usrCreacion, 
                                me.estado ";
            $sqlSelectCount = "SELECT COUNT(me.id) ";

            $sqlFrom = "FROM 
                        schemaBundle:InfoMantenimientoElemento me,
                        schemaBundle:InfoOrdenTrabajo ot,
                        schemaBundle:InfoOrdenTrabajoCaract otCaractKm, 
                        schemaBundle:InfoOrdenTrabajoCaract otCaractTipoMant, 
                        schemaBundle:InfoOrdenTrabajoCaract otCaractNumeracion
                        WHERE me.ordenTrabajoId= ot.id
                        and otCaractKm.ordenTrabajo=ot.id 
                        and otCaractTipoMant.ordenTrabajo=ot.id 
                        and otCaractNumeracion.ordenTrabajo=ot.id 
                        and otCaractKm.caracteristica=:idCaracteristicaKm
                        and otCaractTipoMant.caracteristica=:idCaracteristicaTipoMantenimiento
                        and otCaractNumeracion.caracteristica=:idCaracteristicaNumeracion ";
            
            
            $query->setParameter("idCaracteristicaKm", $parametros["idCaracteristicaKm"] );
            $queryCount->setParameter("idCaracteristicaKm", $parametros["idCaracteristicaKm"] );
            $query->setParameter("idCaracteristicaTipoMantenimiento", $parametros["idCaracteristicaTipoMantenimiento"] );
            $queryCount->setParameter("idCaracteristicaTipoMantenimiento", $parametros["idCaracteristicaTipoMantenimiento"] );
            $query->setParameter("idCaracteristicaNumeracion", $parametros["idCaracteristicaNumeracion"] );
            $queryCount->setParameter("idCaracteristicaNumeracion", $parametros["idCaracteristicaNumeracion"] );
            
            if($parametros["idElemento"])
            {
                $strWhere.=" AND me.elementoId = :idElemento ";
                $query->setParameter("idElemento", $parametros["idElemento"] );
                $queryCount->setParameter("idElemento", $parametros["idElemento"] );
            }
            
            if($parametros["idOrdenTrabajo"])
            {
                $strWhere.=" AND ot.id = :idOrdenTrabajo ";
                $query->setParameter("idOrdenTrabajo", $parametros["idOrdenTrabajo"] );
                $queryCount->setParameter("idOrdenTrabajo", $parametros["idOrdenTrabajo"] );
            }
            
            if($parametros["estado"])
            {
                $strWhere.=" AND me.estado = :estado";
                $query->setParameter("estado", $parametros["estado"] );
                $queryCount->setParameter("estado", $parametros["estado"] );
            }
            
            $sqlOrderBy=" ORDER BY me.feCreacion ASC";

            $sql        = $sqlSelect.$sqlFrom.$strWhere.$sqlOrderBy;       
            $sqlCount   = $sqlSelectCount.$sqlFrom.$strWhere;

            $query->setDQL($sql);
            
            if( isset($parametros['intStart']) )
            {
                if($parametros['intStart'])
                {
                    $query->setFirstResult($parametros['intStart']);
                }
            }
            
            if( isset($parametros['intLimit']) )
            {
                if($parametros['intLimit'])
                {
                    $query->setMaxResults($parametros['intLimit']);
                }
            }

            $arrayRespuesta['resultado'] = $query->getResult();

            $queryCount->setDQL($sqlCount);
            $arrayRespuesta['total'] = $queryCount->getSingleScalarResult();
        } 
        catch (Exception $e) 
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

}
