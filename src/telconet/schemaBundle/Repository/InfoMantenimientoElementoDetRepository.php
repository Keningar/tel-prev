<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoMantenimientoElementoDetRepository extends EntityRepository
{
    
    /**
      * getJSONDetallesMantenimientosXParametros
      *
      * Método que retornará los detalles de los mantenimientos ingresados al transporte                        
      *
      * @param array $parametros["idMantenimientoTransporte": id de la orden de mantenimiento del vehículo]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 07-07-2016
      */
    public function getJSONDetallesMantenimientosXParametros($parametros)
    {
        $arrayEncontrados   = array();
        $arrayResultado     = $this->getResultadoDetallesMantenimientosXParametros($parametros);
        $resultado          = $arrayResultado['resultado'];
        $intTotal           = $arrayResultado['total'];
        $total              = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                $arrayEncontrados[]=array(
                                            'idDetMantenimientoElemento'=> $data["id"],
                                            'nombreCategoria'           => $data["valor1"],
                                            'valorTotalCategoria'       => $data["valorTotal"],
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
      * getResultadoDetallesMantenimientosXParametros
      *
      * Método que obtendrá los mantenimientos de los transportes                                
      *
      * @param array $parametros["idMantenimientoTransporte": id de la orden de mantenimiento del vehículo]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 01-06-2016
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.1 22-06-2016 Se realizan cambios de acuerdo a los formatos de calidad establecidos
      */
    public function getResultadoDetallesMantenimientosXParametros($parametros)
    {
        $arrayRespuesta['resultado']   = "";
        $arrayRespuesta['total']       = 0;
        try
        {
            $query          = $this->_em->createQuery();
            $queryCount     = $this->_em->createQuery();
            $strWhere       = "";
            $sqlSelect      = " SELECT det.id, parDet.valor1,det.valorTotal,det.usrCreacion,det.feCreacion ";
            $sqlSelectCount = "SELECT COUNT(det.id) ";

            $sqlFrom = "FROM 
                        schemaBundle:InfoMantenimientoElemento me, 
                        schemaBundle:InfoMantenimientoElementoDet det, 
                        schemaBundle:AdmiParametroDet parDet 
                        WHERE det.categoriaId=parDet.id 
                        AND me.id=det.mantenimientoElementoId 
                        AND me.id= :idMantenimientoTransporte ";
            
            $query->setParameter("idMantenimientoTransporte", $parametros["idMantenimientoTransporte"] );
            $queryCount->setParameter("idMantenimientoTransporte", $parametros["idMantenimientoTransporte"] );
            
            $sqlOrderBy=" ORDER BY det.feCreacion ASC";

            $sql        = $sqlSelect.$sqlFrom.$strWhere.$sqlOrderBy;       
            $sqlCount   = $sqlSelectCount.$sqlFrom.$strWhere;

            $query->setDQL($sql);

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
