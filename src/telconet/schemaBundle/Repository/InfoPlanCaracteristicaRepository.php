<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPlanCaracteristicaRepository extends EntityRepository
{
    /**
     * Documentación para el método 'getCaracteristicasPlanByCriterios'.
     *
     * Método utilizado para obtener las características asociadas a un plan de acuerdo a los parámetros enviados
     * costoQuery: 5
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-04-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-03-2019 Se modifica la variable comparada con el estado enviado como parámetro
     *
     * @param array $arrayParametros [  'intIdPlan'                         => id del plan 
     *                                  'strDescripcionCaracteristicaPlan'  => descripción de la Caracteristica
     *                                  'strEstado'                         => estado de la asociación entre el plan y la característica
     *                               ]
     * @return array $arrayRespuesta
     */
    public function getCaracteristicasPlanByCriterios($arrayParametros)
    {
        $arrayRespuesta['intTotal']         = 0;
        $arrayRespuesta['arrayResultado']   = array();
        try
        {
            $rsm                = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $rsm);
            
            $strSelect          = " SELECT IPCARACT.ID_PLAN_CARACTERISITCA, IPCARACT.VALOR, AC.DESCRIPCION_CARACTERISTICA ";
            $strSelectCount     = " SELECT COUNT(IPCARACT.ID_PLAN_CARACTERISITCA) AS TOTAL ";
            $strFromWhere       = " FROM DB_COMERCIAL.INFO_PLAN_CARACTERISTICA IPCARACT,
                                      DB_COMERCIAL.INFO_PLAN_CAB IPC,
                                      DB_COMERCIAL.ADMI_CARACTERISTICA AC
                                    WHERE IPC.ID_PLAN = IPCARACT.PLAN_ID
                                    AND AC.ID_CARACTERISTICA = IPCARACT.CARACTERISTICA_ID ";
            $strWhereAdicional  = "";

            $rsm->addScalarResult('ID_PLAN_CARACTERISITCA', 'intIdPlanCaracteristica', 'integer');
            $rsm->addScalarResult('DESCRIPCION_CARACTERISTICA', 'strDescripcionCaracteristica', 'string');
            $rsm->addScalarResult('VALOR', 'strValor', 'string');
            $rsm->addScalarResult('TOTAL', 'intTotal', 'integer');
            
            if( isset($arrayParametros['intIdPlan']) && !empty($arrayParametros['intIdPlan']) )
            {
                $strWhereAdicional .= " AND IPC.ID_PLAN = :intIdPlan";
                $objNtvQuery->setParameter('intIdPlan', $arrayParametros['intIdPlan']);
            }
            
            if( isset($arrayParametros['strDescripcionCaracteristicaPlan']) && !empty($arrayParametros['strDescripcionCaracteristicaPlan']) )
            {
                $strWhereAdicional .= " AND AC.DESCRIPCION_CARACTERISTICA = :strDescripcionCaracteristicaPlan";
                $objNtvQuery->setParameter('strDescripcionCaracteristicaPlan', $arrayParametros['strDescripcionCaracteristicaPlan']);
            }
            
            if( isset($arrayParametros['strValorCaracteristicaPlan']) && !empty($arrayParametros['strValorCaracteristicaPlan']) )
            {
                $strWhereAdicional .= " AND IPCARACT.VALOR = :strValorCaracteristicaPlan";
                $objNtvQuery->setParameter('strValorCaracteristicaPlan', $arrayParametros['strValorCaracteristicaPlan']);
            }
            
            if( isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']) )
            {
                $strWhereAdicional .= " AND IPCARACT.ESTADO = :strEstado";
                $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);
            }
            
            $strQuery       = $strSelect.$strFromWhere.$strWhereAdicional;
            $objNtvQuery->setSQL($strQuery);
            $arrayResultado = $objNtvQuery->getResult();
            
            
            $strQueryCount  = $strSelectCount.$strFromWhere.$strWhereAdicional;
            $objNtvQuery->setSQL($strQueryCount);
            $intTotal       = $objNtvQuery->getSingleScalarResult();

            $arrayRespuesta['arrayResultado']   = $arrayResultado;
            $arrayRespuesta['intTotal']         = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
    * Funcion que devuelve registro de caracteristica del plan por estados
    *
    * @param array [ -intStart => valor minimo
    *                -intLimit => valor maximo
    *                -strEstado => Valor del estado a buscar
    *                -intIdEmpresa => Id de la empresa a buscar ]
    * @return $resultado [arreglo con el detalle de los planes]
    *
    * @author Desconocido
    * @version 1.0 - Version inicial ya existia, pero no funcionaba
    *
    * @author Daniel Reyes <djreyes@telconet.ec>
    * @version 1.1 - Se corrige sistema de parametros que no estaban bien mapeados y no traia resultados 
    * 
    */
    public function find30PlanesPorEmpresaPorEstado($arrayParametros)
    {	
        $intStart = $arrayParametros['intStart'];
        $intLimit = $arrayParametros['intLimit'];
        $strQuery = $this->_em->createQuery(" SELECT ipc
		        FROM schemaBundle:InfoPlanCab ipc
		        WHERE ipc.estado = :estado AND
                      ipc.empresaCod = :empresa order by ipc.id desc");
        $strQuery->setParameter('estado', $arrayParametros['strEstado']);
        $strQuery->setParameter('empresa', $arrayParametros['intIdEmpresa']);
		$intTotal = count($strQuery->getResult());
		$arrayDatos = $strQuery->setFirstResult($intStart)->setMaxResults($intLimit)->getResult();
		$arrayResultado['registros']=$arrayDatos;
		$arrayResultado['total']=$intTotal;
		return $arrayResultado;
    }

    public function findPlanesPorCriterios($estado,$idEmpresa,$fechaDesde,$fechaHasta,$nombre,$limit,$page,$start){
                $nombre=trim($nombre);
                $query = $this->_em->createQuery("SELECT ipc
		FROM 
                schemaBundle:InfoPlanCab ipc
		WHERE  ". ($fechaDesde ? " ipc.feCreacion>=:fechaDesde AND " : "") .                   
		" ". ($fechaHasta ? " ipc.feCreacion<=:fechaHasta  and " : "") .  
                " ". ($nombre ? "  ipc.nombrePlan like '%$nombre%' AND " : "") .
                " ipc.estado=:estado  AND
                ipc.empresaCod=:idEmpresa  
                 order by ipc.id desc");
                
                 $query->setParameter('estado', $estado);
                 $query->setParameter('idEmpresa', $idEmpresa);
                 
                 if ($fechaDesde && $fechaHasta)
	         {
                     $query->setParameter('fechaDesde', $fechaDesde);
                     $query->setParameter('fechaHasta', $fechaHasta);
                 }
                
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
    }    
    
    public function getCaracteristicasPlan($idPlan)
    {
       $query = $this->_em->createQuery("		
           select ipc.id as idPlanCaract,ca.id as idCaract,ca.tipo as tipo ,ca.descripcionCaracteristica as nombre,
           ipc.valor as valor,
           ipc.estado as estado
           from schemaBundle:AdmiCaracteristica ca,     
           schemaBundle:InfoPlanCaracteristica ipc
           where ca.id = ipc.caracteristicaId       
           and   ipc.planId =:idPlan and ipc.estado in (:estados)
           ");
       $query->setParameter('idPlan', $idPlan); 
       $query->setParameter('estados',array('Activo','Pendiente','Clonado'));
       $datos = $query->getResult();
       return $datos;
   }
   
   public function getPlanCaract($idPlan){
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:InfoPlanCaracteristica ipc
       where ipc.estado='Activo' and ipc.planId = $idPlan 
       ");
                $datos = $query->getResult();
		//$datos = $query->getDQL();
                //print_r($datos);
                //die();
		return $datos;
   }
    public function findOneByIdPlanCaracteristica($idPlan,$idcaracteristica,$estado)
    {
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:AdmiCaracteristica ca,     
       schemaBundle:InfoPlanCaracteristica ipc
       where ca.id = ipc.caracteristicaId       
       and   ipc.planId =:idPlan and ipc.estado =:estado and ca.id=:idcaracteristica ");
        
        $query->setParameter('idPlan', $idPlan);
        $query->setParameter('idcaracteristica', $idcaracteristica);        
        $query->setParameter('estado', $estado);
        $datos = $query->getOneOrNullResult();
        
        return $datos;        
   }
   /**
    * Funcion que devuelve las caracteristicas de los planes en estado Activo sin considerar Frecuencia y Numero de Ips Max
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-07-2014            
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 04-08-2021 - Se agrega característica TIPO_CATEGORIA_PLAN_ADULTO_MAYOR que no se considere en el query.  
    *
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanCaracteristica
    * @return $datos
    */
    public function getPlanCaractProviene($intPlanId){
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:InfoPlanCaracteristica ipc,
       schemaBundle:AdmiCaracteristica ca
       where ipc.caracteristicaId=ca.id and
       ca.descripcionCaracteristica not in (:strDescripcion) and
       ipc.estado=:strEstado and ipc.planId =:intPlanId 
       ");
       
       $query->setParameter( 'intPlanId', $intPlanId );
       $query->setParameter( 'strDescripcion',array('FRECUENCIA','IP_MAX_PERMITIDAS','TIPO_CATEGORIA_PLAN_ADULTO_MAYOR') );
       $query->setParameter( 'strEstado', "Activo" );
       $datos = $query->getResult();		
       return $datos;
   }
    /**
    * Funcion que devuelve registro de caracteristica del plan por estados
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-07-2014            
    * @param integer $intPlanId
    * @param integer $intIdCarac
    * @see \telconet\schemaBundle\Entity\InfoPlanCaracteristica
    * @return $datos
    */
    public function findOneByIdPlanCaracteristicaEstados($intPlanId,$intIdCarac)
    {
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:AdmiCaracteristica ca,     
       schemaBundle:InfoPlanCaracteristica ipc
       where ca.id = ipc.caracteristicaId       
       and   ipc.planId =:intPlanId and ipc.estado in (:strEstados) and ca.id=:intIdCarac ");
        
        $query->setParameter( 'intPlanId', $intPlanId);
        $query->setParameter( 'intIdCarac', $intIdCarac);   
        $query->setParameter( 'strEstados',array('Activo','Pendiente') );        
        $datos = $query->getOneOrNullResult();
        
        return $datos;        
   }
    /**
    * Funcion que devuelve las caracteristicas de los planes
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-07-2014            
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanCaracteristica
    * @return $datos
    */
   public function getPlanCaractEstados($intIdPlan)
   {
       $query = $this->_em->createQuery("		
       select ipc
       from schemaBundle:InfoPlanCaracteristica ipc
       where ipc.estado in (:strEstados) and ipc.planId =:intIdPlan 
       ");
       $query->setParameter( 'intIdPlan' , $intIdPlan ); 
       $query->setParameter( 'strEstados',array('Activo','Pendiente','Clonado') );
       
       $datos = $query->getResult();		
       return $datos;
   }
    /**
    * Funcion que devuelve las caracteristicas de los planes por estado
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-07-2014            
    * @param integer $intPlanId
    * @param string  $strEstado
    * @see \telconet\schemaBundle\Entity\InfoPlanCaracteristica
    * @return $datos
    */
    public function findPlanIdYEstado($intPlanId,$strEstado)
    {
        $query = $this->_em->createQuery("		
        select ipc
        from schemaBundle:InfoPlanCaracteristica ipc
        where ipc.estado=:strEstado and ipc.planId =:intPlanId 
        ");
        $query->setParameter( 'intPlanId' , $intPlanId ); 
        $query->setParameter( 'strEstado' ,$strEstado);
       
        $datos = $query->getResult();		
        return $datos;
   }
}
