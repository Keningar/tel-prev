<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoMantenimientoTareaRepository extends EntityRepository
{

    public function getResultadoTareasPorMantenimiento($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $query        = $this->_em->createQuery();
            $queryCount   = $this->_em->createQuery();
            
            $strSelect      = "SELECT t.id,t.nombreTarea,mt.frecuencia,mt.tipoFrecuencia,mt.estado ";
            $strSelectCount = "SELECT COUNT(t) ";

            $strFrom        = " FROM
                                schemaBundle:AdmiTarea t
                                INNER JOIN schemaBundle:InfoMantenimientoTarea mt WITH mt.tareaId=t.id
                                INNER JOIN schemaBundle:AdmiProceso m WITH m.id=mt.mantenimientoId
                                INNER JOIN schemaBundle:AdmiProcesoEmpresa mte WITH mte.procesoId=m.id 
                                WHERE mt.estado = :estadoActivo ";
            $strWhere       = '';
            $strOrderBy     = " ORDER BY t.nombreTarea ";
            
            $query->setParameter("estadoActivo", 'Activo');
            $queryCount->setParameter("estadoActivo", 'Activo');
            
            if(isset($arrayParametros["idMantenimiento"]))
            {
                if($arrayParametros["idMantenimiento"] && $arrayParametros["idMantenimiento"]!="")
                {
                    $strWhere .= "AND m.id = :idMantenimiento ";
                    $query->setParameter("idMantenimiento", $arrayParametros["idMantenimiento"]);
                    $queryCount->setParameter("idMantenimiento", $arrayParametros["idMantenimiento"]);
                }
            }
            
            
            $querySqlCount = $strSelectCount . $strFrom .$strWhere .$strOrderBy;
            $queryCount->setDQL($querySqlCount);
            $intTotal       = $queryCount->getSingleScalarResult();
            
            $querySql = $strSelect. $strFrom .$strWhere .$strOrderBy;
            $query->setDQL($querySql);
            $arrayResultado = $query->getResult();
            
            $arrayRespuesta["total"]        = $intTotal;
            $arrayRespuesta['resultado']    = $arrayResultado;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }

    public function getJSONTareasPorMantenimiento($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoTareasPorMantenimiento($arrayParametros);
        $registros        = $arrayResultado['resultado'];
        $intTotal         = $arrayResultado['total'];
        
        if ($registros) 
        {
            $total=$intTotal;
            foreach ($registros as $data)
            {   
                $arrayEncontrados[]=array(
                    'idTarea'               => $data["id"],
                    'nombreTarea'           => trim($data["nombreTarea"]),
                    'estado'                => (strtolower(trim($data["estado"]))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                    'frecuenciaTarea'       => $data["frecuencia"],
                    'tipoFrecuenciaTarea'   => $data["tipoFrecuencia"],
                );
            }
        }
        else
        {
            $total=0;
        }
        
        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
        
    }
    
    /**
      * getResultadoTareasyCategoriasByCriterios
      *
      * Método que retornará las tareas para cerrar un caso TN de acuerdo a los parámetros enviados                                   
      *
      * @param array $parametros["idMantenimiento"=> id de la orden de Mantenimiento]
      * @return json $resultado
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 01-06-2016
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.1 22-06-2016 Se realizan cambios de acuerdo a los formatos de calidad establecidos
      * 
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.2 05-08-2016 Se parametriza el estado de la tarea
      */
    public function getResultadoTareasyCategoriasByCriterios($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $query        = $this->_em->createQuery();
            $queryCount   = $this->_em->createQuery();
            
            $strSelect      = "SELECT t.id,t.nombreTarea,mt.estado, cat.id as idCategoria, cat.valor1 as nombreCategoria ";
            $strSelectCount = "SELECT COUNT(t) ";

            $strFrom        = " FROM
                                schemaBundle:AdmiTarea t
                                INNER JOIN schemaBundle:InfoMantenimientoTarea mt WITH mt.tareaId=t.id
                                INNER JOIN schemaBundle:AdmiProceso m WITH m.id=mt.mantenimientoId
                                INNER JOIN schemaBundle:AdmiProcesoEmpresa mte WITH mte.procesoId=m.id 
                                LEFT JOIN schemaBundle:AdmiParametroDet cat WITH cat.id = t.categoriaTareaId
                                WHERE mt.estado = :estadoActivo ";
            $strWhere       = '';
            $strOrderBy     = " ORDER BY t.nombreTarea ";
            
            $query->setParameter("estadoActivo", 'Activo');
            $queryCount->setParameter("estadoActivo", 'Activo');
            
            if(isset($arrayParametros["idMantenimiento"]))
            {
                if($arrayParametros["idMantenimiento"] && $arrayParametros["idMantenimiento"]!="")
                {
                    $strWhere .= "AND m.id = :idMantenimiento ";
                    $query->setParameter("idMantenimiento", $arrayParametros["idMantenimiento"]);
                    $queryCount->setParameter("idMantenimiento", $arrayParametros["idMantenimiento"]);
                }
            }
            
            
            $querySqlCount = $strSelectCount . $strFrom .$strWhere .$strOrderBy;
            $queryCount->setDQL($querySqlCount);
            $intTotal       = $queryCount->getSingleScalarResult();
            
            $querySql = $strSelect. $strFrom .$strWhere .$strOrderBy;
            $query->setDQL($querySql);
            $arrayResultado = $query->getResult();
            
            $arrayRespuesta["total"]        = $intTotal;
            $arrayRespuesta['resultado']    = $arrayResultado;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }
    
    /**
      * getJSONTareasyCategoriasByCriterios
      *
      * Método que retornará el json de las tareas y su respectiva categoría                                  
      *
      * @param array $arrayParametros["idMantenimiento"=> id de la orden de Mantenimiento]
      * @return json $jsonData
      *
      * @author Lizbeth Cruz <mlcruz@telconet.ec>
      * @version 1.0 30-08-2016
      * 
      */
    public function getJSONTareasyCategoriasByCriterios($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoTareasyCategoriasByCriterios($arrayParametros);
        $registros        = $arrayResultado['resultado'];
        $intTotal         = $arrayResultado['total'];
        
        if ($registros) 
        {
            $total=$intTotal;
            foreach ($registros as $data)
            {   
                $arrayEncontrados[]=array(
                                            'id_tarea'              => $data["id"],
                                            'id_categoria'          => $data["idCategoria"],
                                            'nombre_tarea'          => $data["nombreTarea"],
                                            'nombre_categoria'      => $data["nombreCategoria"],
                                            'estado'                => $data["estado"]
                                    );
            }
        }
        else
        {
            $total=0;
        }
        
        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
        
    }
    
    
}