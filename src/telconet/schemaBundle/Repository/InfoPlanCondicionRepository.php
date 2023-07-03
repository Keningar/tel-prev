<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPlanCondicionRepository extends EntityRepository
{     
   /**
    * Funcion que devuelve las condiciones ingresadas para un plan especifico
    * Consideraciones: Se toma solo las condiciones en estado Activo
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param integer $idPlan     
    * @version 1.0 23-05-2014
    * @return object
    */
    public function getCondicionesPlanXPlan($idPlan)
    {	         
        $sql="select plancon.id id_plan_condicion, plancon.empresaCod empresa_cod,planc.nombrePlan nombre_plan,
                plancon.tipoNegocioId tipo_negocio_id,tn.nombreTipoNegocio nombre_tipo_negocio,
                plancon.formaPagoId forma_pago_id,fp.descripcionFormaPago descripcion_forma_pago,
                plancon.tipoCuentaId tipo_cuenta_id,tc.descripcionCuenta descripcion_cuenta,
                plancon.bancoTipoCuentaId banco_tipo_cuenta_id,bc.descripcionBanco descripcion_banco
                from schemaBundle:InfoPlanCondicion plancon
                join schemaBundle:InfoPlanCab planc WITH planc.id=plancon.planId
                left join schemaBundle:AdmiFormaPago fp WITH fp.id=plancon.formaPagoId
                left join schemaBundle:AdmiTipoNegocio tn WITH tn.id=plancon.tipoNegocioId
                left join schemaBundle:AdmiTipoCuenta tc WITH tc.id=plancon.tipoCuentaId
                left join schemaBundle:AdmiBancoTipoCuenta btc WITH btc.id=plancon.bancoTipoCuentaId
                left join schemaBundle:AdmiBanco bc WITH bc.id=btc.bancoId                
                where  plancon.planId=:idPlan and plancon.estado in (:estados)  ";        
        
        $qb = $this->_em->createQuery($sql);
        $qb->setParameter('idPlan', $idPlan); 
        $qb->setParameter('estados',array('Activo','Pendiente','Clonado'));
        $query = $qb->getResult();
        
        return $query;
    }
    
    /**
    * Funcion que devuelvela condicion de un plan especifico
    * Consideraciones: Se toma solo las condiciones en estado Activo
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param integer $idPlan     
    * @version 1.0 23-05-2014
    * @return object
    */
    public function getPlanCondicion($idPlan){
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:InfoPlanCondicion ipc
       where ipc.estado in (:estados)
       and ipc.planId=:idPlan
       ");
       $query->setParameter('idPlan', $idPlan); 
       $query->setParameter('estados',array("Activo","Pendiente"));
       $datos = $query->getResult();	
       return $datos;
   }
   /**
    * Funcion que devuelvela condicion de un plan especifico en estado Pendiente
    * Consideraciones: Se toma solo las condiciones en estado Pendiente
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param integer $intIdPlan     
    * @param string  $strEstado 
    * @version 1.0 23-05-2014
    * @return object
    */
    public function findPlanIdYEstado($intPlanId,$strEstado){
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:InfoPlanCondicion ipc
       where ipc.estado=:strEstado
       and ipc.planId=:intPlanId
       ");
       $query->setParameter( 'intPlanId' , $intPlanId ); 
       $query->setParameter( 'strEstado' ,$strEstado);
       $datos = $query->getResult();	
       return $datos;
   }
}
