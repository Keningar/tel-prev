<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoOrdenTrabajoDetRepository extends EntityRepository
{
    /**
    * getRegistros
    *
    * Esta funcion retorna la orden de trabajo con su respectivos detalles asociados a las tareas
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.0 14-07-2016 
    *
    *
    * @param array $arrayParametros ['idOrdenTrabajoCab': id de la orden de trabajo]
    *
    * @return array $arrayRespuesta
    *
    */
    public function getRegistros($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $query        = $this->_em->createQuery();
            $queryCount   = $this->_em->createQuery();

            $where      = "";
            $strCount   = " SELECT COUNT(DISTINCT od.id)  ";
            $strSelect  = " SELECT o.id as idOrdenTrabajoCab, od.id as idOrdenTrabajoDet,
                            t.id as idTarea,t.nombreTarea as nombreTarea,pd.id as idCategoriaTarea,pd.valor1 as nombreCategoria ";
            $strFrom    ="  FROM schemaBundle:InfoOrdenTrabajo o
                            INNER JOIN schemaBundle:InfoOrdenTrabajoDet od WITH o.id=od.ordenTrabajoId
                            INNER JOIN schemaBundle:AdmiParametroDet pd WITH pd.id=od.categoriaTareaId
                            INNER JOIN schemaBundle:AdmiTarea t WITH t.id=od.tareaId ";
            $strOrderBy = " ORDER BY idCategoriaTarea,idTarea ";
            
            if($arrayParametros["idOrdenTrabajoCab"])
            {
                $where .= "WHERE o.id = :idOrdenTrabajoCab ";
                $query->setParameter("idOrdenTrabajoCab", $arrayParametros["idOrdenTrabajoCab"]);
                $queryCount->setParameter("idOrdenTrabajoCab", $arrayParametros["idOrdenTrabajoCab"]);
            }

            $querySql = $strSelect . $strFrom . $where. $strOrderBy;
            $query->setDQL($querySql);
            $arrayResultado = $query->getResult();
            
            $querySqlCount = $strCount . $strFrom . $where;
            $queryCount->setDQL($querySqlCount);
            $intTotal = $queryCount->getSingleScalarResult();
            
            $arrayRespuesta['total']     = $intTotal;
            $arrayRespuesta['resultado'] = $arrayResultado;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayRespuesta;
    }
    
}
