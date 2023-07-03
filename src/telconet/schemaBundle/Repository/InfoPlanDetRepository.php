<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPlanDetRepository extends EntityRepository
{
    public function findByPlanIdYEstado($idPlan, $estado)
    {
        $query = $this->_em->createQuery("SELECT ipd
            FROM 
                schemaBundle:InfoPlanDet ipd
            WHERE 
                ipd.estado = :estado AND
                ipd.planId = :idPlan");
        $query->setParameter('estado', $estado);
        $query->setParameter('idPlan', $idPlan);
        $datos = $query->getResult();
        return $datos;
    }

    /**
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 01-01-2019 Se agrega la obtención del id producto característica en la consulta
     * @since 1.0
     * 
     * @param integer $idPlanDet
     * @return array 
     */
    public function getCaracteristicas($idPlanDet)
    {
        $query = $this->_em->createQuery("
            select apc.id as idProductoCaracteristica,
                   ca.descripcionCaracteristica as nombre,
                   ippc.valor as valor,
                   ippc.estado as estado
            from schemaBundle:AdmiCaracteristica ca,
                 schemaBundle:AdmiProductoCaracteristica apc,
                 schemaBundle:InfoPlanProductoCaract ippc
            where ca.id = apc.caracteristicaId
            and apc.id = ippc.productoCaracterisiticaId
            and ippc.planDetId = :idPlanDet 
            ");
        $query->setParameter('idPlanDet', $idPlanDet);
        $datos = $query->getResult();
        return $datos;
    }
    /**
    * Funcion que devuelve los detalles de los productos que forman un plan   
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-07-2014            
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanDet
    * @return $datos
    */
    public function getPlanIdYEstados($intPlanId)
    {   
        $em = $this->_em; 
        $query = $em->createQuery("SELECT p 
              from                                                           
              schemaBundle:InfoPlanDet p         
              where                
              p.planId =:intPlanId                                           
              and p.estado in (:strEstado) ");                
       $query->setParameter( 'intPlanId' , $intPlanId);                             
       $query->setParameter( 'strEstado' ,array('Activo','Pendiente','Clonado'));                        
       $datos = $query->getResult();        
       return $datos;
    }  	 
    /**
    * Funcion que devuelve los detalles de los productos que forman un plan en estado Activos o Pendientes
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 08-08-2014            
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanDet
    * @return $datos
    */
    public function getDetallePlanIdXEstados($intPlanId,$arrayEstados)
    {   
        $em = $this->_em; 
        $query = $em->createQuery("SELECT p 
              from                                                           
              schemaBundle:InfoPlanDet p         
              where                
              p.planId =:intPlanId                                           
              and p.estado in (:arrayEstados) ");                
       $query->setParameter( 'intPlanId' , $intPlanId);                             
       $query->setParameter( 'arrayEstados' ,$arrayEstados);                        
       $datos = $query->getResult();        
       return $datos;
    }

    /**
     * getProductosPlan
     * 
     * Obtiene los productos asociados al plan de acuerdo a los parámetros
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-07-2017
     * 
     * @param  array $arrayParametros[  
     *                                  'intIdPlan'         => id del plan
     *                                  'strNombreTecnico'  => nombre técnico del producto
     *                               ]
     * 
     * @return array $arrayResultado
     */
    public function getProductosPlan($arrayParametros)
    {   
        $arrayResultado = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);
            
            $strQuery           = "SELECT ap.NOMBRE_TECNICO 
                                   FROM DB_COMERCIAL.info_plan_cab ipc 
                                   INNER JOIN DB_COMERCIAL.info_plan_det ipd on ipd.plan_id = ipc.id_plan 
                                   INNER JOIN DB_COMERCIAL.admi_producto ap on ap.id_producto = ipd.producto_id 
                                   WHERE ap.estado = :strEstadoActivo ";
            
            $strWhere = "";
            
            $objNtvQuery->setParameter('strEstadoActivo', "Activo");
            
            if(isset($arrayParametros['intIdPlan']) && !empty($arrayParametros['intIdPlan']))
            {
                $strWhere .= "AND ipc.id_plan = :intIdPlan ";
                $objNtvQuery->setParameter('intIdPlan', $arrayParametros['intIdPlan']);
            }
            
            if(isset($arrayParametros['strNombreTecnico']) && !empty($arrayParametros['strNombreTecnico']))
            {
                $strWhere .= "AND ap.nombre_tecnico = :strNombreTecnico ";
                $objNtvQuery->setParameter('strNombreTecnico', $arrayParametros['strNombreTecnico']);
            }
            $objRsm->addScalarResult('NOMBRE_TECNICO', 'nombre_tecnico', 'string');
            $strQueryFinal = $strQuery.$strWhere;
            $objNtvQuery->setSQL($strQueryFinal);
            $arrayResultado = $objNtvQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }
}
