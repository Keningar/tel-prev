<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;

class InfoPlanCabRepository extends EntityRepository
{

    /**
     * Obtiene el perfil del plan, solicitado en caso de que se solicite
     * la ip como producto o este contenido en un plan.
     * @param type $esPlan
     * @param type $id_plan
     * @param type $id_punto
     * @param type $retornaIdPlan
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-11-2015 Se agrega parametros para reutilizacion de funcion en cambio de planes
     * @return type
     */
    public function getPerfilByPlanIdAndPuntoId($esPlan, $id_plan, $id_punto, $retornaIdPlan = ''){
        $em = $this->_em;
        //si es un producto
        if(strtolower($esPlan) == 'no')
        {
            $id_plan = $em->getRepository('schemaBundle:InfoPlanCab')->getPlanPorPunto($id_punto);
            
            if (strpos($id_plan, 'Error') !== false)
            {
                return $id_plan;
            }
        }
        
        $perfil = $em->getRepository('schemaBundle:InfoPlanCab')->getPerfilByPlanId($id_plan);
        if ($retornaIdPlan == "SI")
        {
            $arrayRespuesta              = array();
            $arrayRespuesta['strPerfil']  = $perfil;
            $arrayRespuesta['strIdPlan'] = $id_plan;
            return $arrayRespuesta;
        }
        else
        {
            return $perfil;
        }
    }
    
    public function getPlanPorPunto($id_punto) {
        
        $em = $this->_em;
        
        $planCabEnt = $em->createQuery("select IDENTITY(s.planId) from schemaBundle:InfoServicio s  
                , schemaBundle:InfoPlanCab pc
                , schemaBundle:InfoPlanDet pd  
                , schemaBundle:AdmiProducto p  
                where 
                pc.id = s.planId
                and pd.planId = pc.id
                and p.id = pd.productoId
                and s.estado = 'Activo' 
                and p.nombreTecnico = 'INTERNET' 
                and s.puntoId = $id_punto");
        
        try {
            $planId = $planCabEnt->getSingleScalarResult();
            if(!$planId)
				$planId = 'Error 1: No existe un Servicio con Plan Activo para el Punto cliente. Imposible dar Ip Fija.';
        } catch (\Exception $e) {
            $planId = 'Error 2: No existe un Servicio con Plan Activo para el Punto cliente. Imposible dar Ip Fija.';
        }
        return $planId;
    }
    
    /**
     * Obtiene el perfil de un plan dado.
     * @param type $id_plan
     * @return string
     */
    public function getPerfilByPlanId($id_plan) {
        $em = $this->_em;
        
        $perfilEnt = $em->createQuery("select pdc.valor from schemaBundle:InfoPlanCab pc, schemaBundle:InfoPlanDet pd, schemaBundle:AdmiProducto p, schemaBundle:AdmiProductoCaracteristica pca, schemaBundle:AdmiCaracteristica ac, schemaBundle:InfoPlanProductoCaract pdc
            where 
            pd.planId = pc.id 
            and p.id = pd.productoId 
            and pca.productoId = p.id
            and ac.id = pca.caracteristicaId
            and pdc.productoCaracterisiticaId = pca.id
            and pc.empresaCod = 18 and pc.id = $id_plan 
            and pd.id = pdc.planDetId
            and p.codigoProducto = 'INTD'
            and ac.descripcionCaracteristica = 'PERFIL'");
        
        try {
            $perfil = $perfilEnt->getSingleScalarResult();
            if(!$perfil){
				$plan = $em->getRepository('schemaBundle:InfoPlanCab')->find($id_plan);
				$perfil = 'Error: No existe perfil para el plan <b>'.$plan->getNombrePlan()."</b>. Favor Notificar a Sistemas.";
			}	
        } catch (\Doctrine\ORM\NoResultException $e) {
			$plan = $em->getRepository('schemaBundle:InfoPlanCab')->find($id_plan);
            $perfil = 'Error: No existe perfil para el plan <b>'.$plan->getNombrePlan()."</b>. Favor Notificar a Sistemas.";
        }
        return $perfil;
    }
    
    /**
     * find30PlanesPorEmpresaPorEstado
     * 
     * Funcion que sirve para obtener listado de planes por empresa, estado, limitado entre rangos y 
     * por su nombre.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 17-01-2017
     *
     * @param Array $arrayParametros['strEstado']        String  Estado del plan.
     *              $arrayParametros['intIdEmpresa']    int     Id de la empresa.
     *              $arrayParametros['intLimit']        int     Valor maximo para obtener listado de planes
     *              $arrayParametros['intStart']        int     Valor inicial para obtener listado de planes
     *              $arrayParametros['strNombrePlan']   String  Nombre del plan a buscar
     * 
     * @return Array Listado de Planes
     * 
     */
	public function find30PlanesPorEmpresaPorEstado( $arrayParametros ){	
        
        $strQuery = "SELECT ipc
                        FROM 
                                schemaBundle:InfoPlanCab ipc
                        WHERE 
                                ipc.estado = '"   . $arrayParametros['strEstado']    . "' AND
                                ipc.empresaCod='" . $arrayParametros['intIdEmpresa'] . "' ";

        if ($arrayParametros['strNombrePlan'] != NULL)
        {
            $strQuery.=  " AND UPPER(ipc.nombrePlan) like '%".strtoupper(trim($arrayParametros['strNombrePlan']))."%' ";
        }
        
        $strQuery.= " order by ipc.id desc ";
            
        $query  =   $this->_em->createQuery($strQuery);
                
		$total  =   count($query->getResult());
        $datos  =   $query->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
		$resultado['registros'] =$datos;
		$resultado['total']     =$total;
		return $resultado;
		//->setMaxResults(1000)
	}
	
	public function findPlanesPorCriterios($estado,$idEmpresa,$fechaDesde,$fechaHasta,$limit,$page,$start){	
                $query = $this->_em->createQuery("SELECT ipc
		FROM 
                schemaBundle:InfoPlanCab ipc
		WHERE 
                ipc.estado = '".$estado."' AND
                ipc.empresaCod='".$idEmpresa."' AND
                ipc.feCreacion >= '".$fechaDesde."' AND 
                ipc.feCreacion <= '".$fechaHasta."' order by ipc.id desc");
		$total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		return $resultado;
	}
        
        //public function findListarPlanesPorEmpresaYEstado($estado,$idEmpresa){	
       /* public function findListarPlanesPorEmpresaYEstado($estado,$nombre_tipo_negocio){	
                
		$query = $this->_em->createQuery("SELECT ipc
		FROM 
                schemaBundle:InfoPlanCab ipc
		WHERE 
                ipc.estado = '$estado' and 
                UPPER(ipc.nombrePlan) like '".strtoupper(trim($nombre_tipo_negocio))."%'");
		$datos = $query->getResult();
                //echo $query->getSQL();
		return $datos;
	}*/

       public function findListarPlanesPorEmpresaYEstado($estado,$nombre_tipo_negocio){	
        //       $query = $this->_em->createQuery("SELECT ipc
		//FROM 
        //        schemaBundle:InfoPlanCab ipc
		//WHERE 
         //       ipc.estado = '".$estado."' AND
         //       ipc.empresaCod='".$idEmpresa."'");
                
		$query = $this->_em->createQuery("SELECT ipc
		FROM 
                schemaBundle:InfoPlanCab ipc
		WHERE 
                ipc.estado = '".$estado."'");
		$datos = $query->getResult();
                //echo $query->getSQL();
		return $datos;
	}	
    
    
    /**
     * Función que obtiene los planes permitidos para la venta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-01-2018 Se agrega restricción para planes no permitidos para la venta, que sólo están permitidos para la migración
     * 
     * @param type $codEmpresa
     * @param type $estado
     * @param type $idTipoNegocio
     * @param type $idFormaPago
     * @param type $idTipoCuenta
     * @param type $idBancoTipoCuenta
     * @param type $nombre
     * @return type
     */
	public function findByCondiciones($codEmpresa, $estado, $idTipoNegocio, $idFormaPago, $idTipoCuenta, $idBancoTipoCuenta, $nombre)
	{
	    $query = $this->_em->createQuery('SELECT ipc.id AS idPlan, ipc.nombrePlan
	            FROM schemaBundle:InfoPlanCab ipc
	            WHERE ipc.empresaCod = :codEmpresa
	            AND ipc.estado = :estado'
	            . ($nombre ? ' AND UPPER(ipc.nombrePlan) like :nombre ' : '') .
	            ' AND (NOT EXISTS (
                        SELECT 1
                        FROM schemaBundle:InfoPlanCondicion ippno
                        WHERE ippno.empresaCod = :codEmpresa
                        AND ippno.planId = ipc.id)'
                . (($idTipoNegocio || $idFormaPago || $idTipoCuenta || $idBancoTipoCuenta) ? (' OR EXISTS (
                        SELECT 1
                        FROM schemaBundle:InfoPlanCondicion ipp
                        WHERE ipp.empresaCod = :codEmpresa
                        AND ipp.planId = ipc.id 
                        AND ipp.estado = \'Activo\' '
                        . ($idTipoNegocio ? ' AND (ipp.tipoNegocioId = :idTipoNegocio OR ipp.tipoNegocioId IS NULL) ' : '')
                        . ($idFormaPago ? ' AND (ipp.formaPagoId = :idFormaPago OR ipp.formaPagoId IS NULL) ' : '')
                        . ($idTipoCuenta ? ' AND (ipp.tipoCuentaId = :idTipoCuenta OR ipp.tipoCuentaId IS NULL) ' : '')
                        . ($idBancoTipoCuenta ? ' AND (ipp.bancoTipoCuentaId = :idBancoTipoCuenta OR ipp.bancoTipoCuentaId IS NULL) ' : '')
                        . ')') : '') .
	            ')
                AND (NOT EXISTS (
                        SELECT 1
                        FROM schemaBundle:InfoPlanCaracteristica planCaract,
                        schemaBundle:AdmiCaracteristica caract
                        WHERE planCaract.planId = ipc.id
                        AND caract.id = planCaract.caracteristicaId
                        AND caract.descripcionCaracteristica = :descripcionSoloCambioPlan
                        AND planCaract.estado = :estado ))
	            ORDER BY ipc.nombrePlan');
        $query->setParameter('descripcionSoloCambioPlan', "PERMITIDO_SOLO_MIGRACION");
        $query->setParameter('codEmpresa', $codEmpresa);
	    $query->setParameter('estado', $estado);
	    if ($nombre)
	    {
	        $query->setParameter('nombre', '%'.strtoupper($nombre).'%');
	    }
	    if ($idTipoNegocio)
	    {
	        $query->setParameter('idTipoNegocio', $idTipoNegocio);
	    }
	    if ($idFormaPago)
	    {
	        $query->setParameter('idFormaPago', $idFormaPago);
	    }
	    if ($idTipoCuenta)
	    {
	        $query->setParameter('idTipoCuenta', $idTipoCuenta);
	    }
	    if ($idBancoTipoCuenta)
	    {
	        $query->setParameter('idBancoTipoCuenta', $idBancoTipoCuenta);
        }
	    $datos = $query->getResult();
	    return $datos;
	}
        
        /**
        * findByCondicionesPlanes
        * Obtiene el listado de planes considerando el tipo de negocio, forma de pago y valor del plan.
        * 
        * @author Alex Arreaga <atarreaga@telconet.ec>
        * @version 1.0 11-06-2019
        * 
        * Costo 49
        * 
        * El campo de forma de pago no es obligatorio,, limite de precio se incluye el precio indicado
        *
        * @author Jose Bedon Sanchez <jobedon@telconet.ec>
        * @version 1.1 06-02-2020
        * 
        * Costo 31
        *
        * @param Array $arrayParametros['tipoNegocio']   String Tipo de negocio.
        *              $arrayParametros['formaPago']     String Forma de pago.
        *              $arrayParametros['valorPlan']     float  Valor del plan.
        * 
        * @return Array Listado de Planes Posibles.
        * 
        */
        public function findByCondicionesPlanes($arrayParametros)
	{           
            $objResultSet = new ResultSetMappingBuilder($this->_em);
           
            $objResultSet->addScalarResult('ID_PLAN',     'id_plan',     'string');
            $objResultSet->addScalarResult('CODIGO_PLAN', 'cod_plan',    'string');
            $objResultSet->addScalarResult('NOMBRE_PLAN', 'nombre_plan', 'string');
            $objResultSet->addScalarResult('PRECIO',      'precio',      'string');
            
            try
            {           
                if(is_numeric($arrayParametros['valorPlan']))
                {
                    $objQuery = $this->_em->createNativeQuery(null, $objResultSet);    
                    $strSql = "SELECT IPC.ID_PLAN,
                                    IPC.CODIGO_PLAN,
                                    IPC.NOMBRE_PLAN,
                                    DB_COMERCIAL.COMEK_CONSULTAS.F_GET_VALOR_PLAN(IPC.ID_PLAN) PRECIO
                                FROM DB_COMERCIAL.INFO_PLAN_CAB IPC
                                WHERE IPC.ESTADO                                                = 'Activo'
                                AND IPC.EMPRESA_COD                                             = 18
                                AND IPC.TIPO                                                    = :tipoNegocio
                                AND DB_COMERCIAL.COMEK_CONSULTAS.F_GET_VALOR_PLAN(IPC.ID_PLAN) >= :valorPlan
                                AND EXISTS
                                    (SELECT 1
                                    FROM DB_COMERCIAL.INFO_PLAN_DET IPD,
                                    DB_COMERCIAL.ADMI_PRODUCTO AP
                                    WHERE AP.ID_PRODUCTO  = IPD.PRODUCTO_ID
                                    AND AP.NOMBRE_TECNICO = 'INTERNET'
                                    AND IPD.PLAN_ID       = IPC.ID_PLAN AND AP.ESTADO = 'Activo' AND IPD.ESTADO = 'Activo' AND AP.EMPRESA_COD = 18)
                                ";

                    $objQuery->setParameter('tipoNegocio', $arrayParametros['tipoNegocio']);
                    $objQuery->setParameter('valorPlan',   $arrayParametros['valorPlan']);

                    $arrayResultado = $objQuery->setSQL($strSql)->getArrayResult();
                }
                else
                {
                   $arrayResultado['strMensaje'] = 'Error en los parámetros';
                }
            }

            catch (Exception $ex)
            {
                $arrayResultado['strMensaje'] = 'Error: ' . $ex->getMessage();
            }
                return $arrayResultado; 
            }
            
            
     /*
     * findByCondicionesProducto
     * Método encargado de devolver los productos de un de un 
     * plan.
     * 
     * @author Macjhony Vargas <mmvargas@telconet.ec>
     * @version 1.0
     * @since 14-10-2019
     * 
     * Costo 6
     * 
     */

        public function findByCondicionesProducto($arrayParametros)
	{                
            $objResultSet = new ResultSetMappingBuilder($this->_em);
            
            $objResultSet->addScalarResult('ID_PRODUCTO',     'id_producto',     'integer');
            $objResultSet->addScalarResult('CODIGO_PRODUCTO', 'codigo_producto', 'string');
            
            try
            {           
                if($arrayParametros)
                {
                    $objQuery = $this->_em->createNativeQuery(null, $objResultSet);    
                    $strSql = "SELECT PROD.ID_PRODUCTO, PROD.CODIGO_PRODUCTO
                                 FROM DB_COMERCIAL.INFO_PLAN_CAB CAB,
                                      DB_COMERCIAL.INFO_PLAN_DET DET,
                                      DB_COMERCIAL.ADMI_PRODUCTO PROD
                                WHERE CAB.ID_PLAN      = DET.PLAN_ID 
                                  AND DET.PRODUCTO_ID  = PROD.ID_PRODUCTO 
                                  AND PROD.ESTADO  = 'Activo'
                                  AND CAB.ESTADO   = 'Activo'
                                  AND CAB.ID_PLAN = :id_plan";

                    
                    $objQuery->setParameter('id_plan',   $arrayParametros['id_plan']);
                    
                    $arrayResultado = $objQuery->setSQL($strSql)->getArrayResult();
                }
                else
                {
                   $arrayResultado['strMensaje'] = 'Error en los parámetros';
                }
            }

            catch (Exception $ex)
            {
                error_log($ex->getMessage());
            }
                return $arrayResultado; 
        }
            
    /*
     * findByCondicionesCaracteristicas
     * Método encargado de devolver los caracteristicas de un producto 
     * por plan. 
     * 
     * 
     * @author Macjhony Vargas <mmvargas@telconet.ec>
     * @version 1.0
     * @since 14-10-2019
     * 
     * Costo 9
     * 
     */
            
        public function findByCondicionesCaracteristicas($arrayParametros)
        {   
            $objResultSet = new ResultSetMappingBuilder($this->_em);
                    
            $objResultSet->addScalarResult('DESCRIPCION_CARACTERISTICA', 'nombre', 'string');    
            $objResultSet->addScalarResult('VALOR',     'valor',     'string');
            try
            {           
                if($arrayParametros)
                {
                    $objQuery = $this->_em->createNativeQuery(null, $objResultSet);    
                    $strSql = "SELECT  CARACT.DESCRIPCION_CARACTERISTICA,
                                       REPLACE(PDC.VALOR,chr(34),'') AS VALOR
                                 FROM  DB_COMERCIAL.INFO_PLAN_CAB CAB,
                                       DB_COMERCIAL.INFO_PLAN_DET DET,
                                       DB_COMERCIAL.ADMI_PRODUCTO PROD,
                                       DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PRODC,
                                       DB_COMERCIAL.ADMI_CARACTERISTICA CARACT,
                                       DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT PDC
                                WHERE  CAB.ID_PLAN      = DET.PLAN_ID 
                                  AND  DET.PRODUCTO_ID  = PROD.ID_PRODUCTO 
                                  AND  PROD.ID_PRODUCTO = PRODC.PRODUCTO_ID 
                                  AND  PRODC.CARACTERISTICA_ID = CARACT.ID_CARACTERISTICA 
                                  AND  PRODC.VISIBLE_COMERCIAL = 'SI' 
                                  AND  PDC.valor IS NOT NULL 
                                  AND  PRODC.estado = 'Activo' 
                                  AND  DET.ID_ITEM  = PDC.PLAN_DET_ID 
                                  AND  PRODC.ID_PRODUCTO_CARACTERISITICA = PDC.PRODUCTO_CARACTERISITICA_ID 
                                  AND  PDC.ESTADO       = 'Activo'
                                  AND  PROD.ESTADO      = 'Activo'
                                  AND  CAB.ESTADO       = 'Activo'
                                  AND  CAB.ID_PLAN      = :id_plan  
                                  AND  PROD.ID_PRODUCTO = :id_producto";

                    $objQuery->setParameter('id_plan',       $arrayParametros['id_plan']);
                    $objQuery->setParameter('id_producto',   $arrayParametros['id_producto']);
                    $arrayResultado = $objQuery->setSQL($strSql)->getArrayResult();
                }
                else
                {
                   $arrayResultado['strMensaje'] = 'Error en los parámetros';
                }
            }
            catch (Exception $ex)
            {
                error_log($ex->getMessage());
            }
                return $arrayResultado; 
        }
            

	public function findListarPlanesPorNombreNegocio($estado,$nombre_tipo_negocio,$nombre_tipo_negocio_no,$empresa){	
            $datos_isp=array();
            $datos_ig=$this->getPlanesIguales($nombre_tipo_negocio_no,$empresa);
            if(strtoupper($nombre_tipo_negocio)=='ISP'){
                $datos_isp=$this->getPlanesIguales($nombre_tipo_negocio,$empresa);
            }
            $datos_dif=$this->getPlanNoInternet($empresa);

            $arr_encontrados = array();
            foreach($datos_ig as $dat)
            {
                    $arr_encontrados[]=array('idPlan' =>$dat->getId(),'nombrePlan' =>trim($dat->getNombrePlan()));
            }

            foreach($datos_isp as $dat)
            {
                    $arr_encontrados[]=array('idPlan' =>$dat->getId(),'nombrePlan' =>trim($dat->getNombrePlan()));
            }
            foreach($datos_dif as $dat)
            {
                    $arr_encontrados[]=array('idPlan' =>$dat->getId(),'nombrePlan' =>trim($dat->getNombrePlan()));
            }                

            return $arr_encontrados;
	}
    
    public function getPlanesIguales($nombre_tipo_negocio_no, $codEmpresa)
    {
        $query = $this->_em->createQuery("select ipc
            from 
                schemaBundle:InfoPlanCab ipc, schemaBundle:InfoPlanDet ipd, schemaBundle:AdmiProducto ap
            where ap.id = ipd.productoId 
                and ipd.planId = ipc.id 
                and ap.descripcionProducto like 'INTERNET DEDICADO%' 
                and ipc.estado = 'Activo' 
                and UPPER(ipc.nombrePlan) like :nombre_tipo_negocio_no 
                and ipc.empresaCod = :empresa");
        $query->setParameter('nombre_tipo_negocio_no', '%'.strtoupper($nombre_tipo_negocio_no).'%');
        $query->setParameter('empresa', $codEmpresa);
        $datos = $query->getResult();
        return $datos;
    }
    
    public function getPlanNoInternet($codEmpresa)
    {
        $query = $this->_em->createQuery("select ipc
            from
                schemaBundle:InfoPlanCab ipc, schemaBundle:InfoPlanDet ipd, schemaBundle:AdmiProducto ap
            where ap.id = ipd.productoId
                and ipd.planId = ipc.id
                and ap.descripcionProducto not like 'INTERNET DEDICADO%'
                and ipc.estado = 'Activo' 
                and UPPER(ipc.nombrePlan) not like '%HOME%'
                and UPPER(ipc.nombrePlan) not like '%PYME%'
                and ipc.empresaCod = :empresa");
        $query->setParameter('empresa', $codEmpresa);
        $datos = $query->getResult();
        return $datos;
    }
    
    public function generarJsonPlanesPorEmpresa($planNombre, $idEmpresa,$estado,$start,$limit, $em , $tipo=''){
        $arr_encontrados = array();
        
        $entidadesTotal = $this->getPlanesPorEmpresa($planNombre,$idEmpresa,$estado,'','',$tipo);
        
        $entidades = $this->getPlanesPorEmpresa($planNombre,$idEmpresa,$estado,$start,$limit,$tipo);
        
//        $num = count($entidadesTotal);
//        print($num);
//        die();
        
//        error_log('entra');
        if ($entidades) {
            
            $num = count($entidadesTotal);
            
            $caracteristica1 = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" =>"CAPACIDAD1"));
            $caracteristica2 = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" =>"CAPACIDAD2"));

            $caracteristica3 = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" =>"CAPACIDAD-INT1"));
            $caracteristica4 = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" =>"CAPACIDAD-INT2"));

            $caracteristica5 = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" =>"CAPACIDAD-PROM1"));
            $caracteristica6 = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" =>"CAPACIDAD-PROM2"));

            $producto = $em->getRepository('schemaBundle:AdmiProducto')->findOneBy(array( "descripcionProducto" =>"INTERNET DEDICADO", "empresaCod"=>$idEmpresa, "estado"=>"Activo"));

            $productoCaracteristica1 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" =>$producto->getId(), "caracteristicaId"=>$caracteristica1->getId()));
            $productoCaracteristica2 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" =>$producto->getId(), "caracteristicaId"=>$caracteristica2->getId()));

            $productoCaracteristica3 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" =>$producto->getId(), "caracteristicaId"=>$caracteristica3->getId()));
            $productoCaracteristica4 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" =>$producto->getId(), "caracteristicaId"=>$caracteristica4->getId()));

            $productoCaracteristica5 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" =>$producto->getId(), "caracteristicaId"=>$caracteristica5->getId()));
            $productoCaracteristica6 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" =>$producto->getId(), "caracteristicaId"=>$caracteristica6->getId()));
            
            foreach ($entidades as $entidad)
            {
                
                
                //se agrega filtrado de estado para obtener detalles de planes
                $planDet1 = $em->getRepository('schemaBundle:InfoPlanDet')->findBy(array( "planId" => $entidad->getId(),
                                                                                          "estado" => $entidad->getEstado() ));
                $total=0;
                for($i=0;$i<count($planDet1);$i++){
                    $total = $total + ($planDet1[$i]->getPrecioItem() * $planDet1[$i]->getCantidadDetalle());
                }
                
                $planDet = $em->getRepository('schemaBundle:InfoPlanDet')->findOneBy(array( "planId" =>$entidad->getId(), "productoId"=>$producto->getId()));
                
                if($planDet!=null){
                    $valorObj1 = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array( "planDetId" =>$planDet->getId(), "productoCaracterisiticaId"=>$productoCaracteristica1));
                    $valorObj2 = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array( "planDetId" =>$planDet->getId(), "productoCaracterisiticaId"=>$productoCaracteristica2));
                    
                    $valorObj3 = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array( "planDetId" =>$planDet->getId(), "productoCaracterisiticaId"=>$productoCaracteristica3));
                    $valorObj4 = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array( "planDetId" =>$planDet->getId(), "productoCaracterisiticaId"=>$productoCaracteristica4));
                    
                    $valorObj5 = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array( "planDetId" =>$planDet->getId(), "productoCaracterisiticaId"=>$productoCaracteristica5));
                    $valorObj6 = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findOneBy(array( "planDetId" =>$planDet->getId(), "productoCaracterisiticaId"=>$productoCaracteristica6));
                    
                    if($valorObj1!=null && $valorObj2!=null){
                        $valor1 = $valorObj1->getValor();
                        $valor2 = $valorObj2->getValor();
                    }
                    else{
                        $valor1="NA";
                        $valor2="NA";
                    }
                    
                    
                    
                    if(($valorObj3!=null || $valorObj3!="" || !empty($valorObj3)) && ($valorObj4!=null || $valorObj4!="" || !empty($valorObj4))){
                        $valor3 = $valorObj3->getValor();
                        $valor4 = $valorObj4->getValor();
                    }
                    else if(($valorObj5!=null || $valorObj5!="" || !empty($valorObj5)) && ($valorObj6!=null || $valorObj6!="" || !empty($valorObj6))){
                        $valor3 = $valorObj5->getValor();
                        $valor4 = $valorObj6->getValor();
                    }
                    else{
                        $valor3 = "NA";
                        $valor4 = "NA";
                    }
                }
                else{
                    $valor1="0";
                    $valor2="0";
                    $valor3="0";
                    $valor4="0";
                }
                    
                
                $arr_encontrados[]=array('idPlan' =>$entidad->getId(),
                                         'nombrePlan' =>trim($entidad->getNombrePlan()),
                                         'valorCapacidad1' => trim($valor1),
                                         'valorCapacidad2' => trim($valor2),
                                         'valorCapacidad3' => trim($valor3),
                                         'valorCapacidad4' => trim($valor4),
                                         'estado'           => trim($entidad->getEstado()),
                                         'total'            => $total
                                        );
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
   
    public function getPlanesPorEmpresa($planNombre,$idEmpresa,$estado,$start,$limit,$tipo=''){
        
//        $qb = $this->_em->createQueryBuilder();
//            $qb->select('e')
//               ->from('schemaBundle:InfoPlanCab','e');
//        
//        if($planNombre!=""){
//            $qb ->where( 'e.nombrePlan like ?1');
//            $qb->setParameter(1, "'%".$planNombre."%'");
//        }
//        if($idEmpresa!=""){
//            $qb ->andWhere( 'e.empresaCod = ?2');
//            $qb->setParameter(2, "'".$idEmpresa."'");
//        }
//        if($estado!="Todos"){
//            $qb ->andWhere('e.estado = ?3');
//            $qb->setParameter(3, "'".$estado."'");
//        }
//        if($start!='')
//            $qb->setFirstResult($start);   
//        if($limit!='')
//            $qb->setMaxResults($limit);
//        
//        $query = $qb->getQuery();
//        
//        print_r(count($query->getResult()));
//        die();
        if($tipo!=""){
            $sql = "SELECT ipc 
		FROM 
                schemaBundle:InfoPlanCab ipc 
		WHERE 
                ipc.estado = '".$estado."' and 
                UPPER(ipc.tipo) like '%".strtoupper(trim($tipo))."%' and
                UPPER(ipc.nombrePlan) like '%".strtoupper(trim($planNombre))."%' 
                and ipc.empresaCod = '".$idEmpresa."'";
        }
        if($planNombre!=""){
            $sql = "SELECT ipc 
		FROM 
                schemaBundle:InfoPlanCab ipc 
		WHERE 
                ipc.estado = '".$estado."' and 
                UPPER(ipc.tipo) like '%".strtoupper(trim($tipo))."%' and
                UPPER(ipc.nombrePlan) like '%".strtoupper(trim($planNombre))."%' 
                and ipc.empresaCod = '".$idEmpresa."'";
        }
        else{
            $sql = "SELECT ipc 
		FROM 
                schemaBundle:InfoPlanCab ipc 
		WHERE 
                ipc.estado = '".$estado."'  and
                UPPER(ipc.tipo) like '%".strtoupper(trim($tipo))."%' 
                and ipc.empresaCod = '".$idEmpresa."'";
        }
        
        if($estado=="Todos" && $planNombre!=""){
            $sql = "SELECT ipc 
		FROM 
                schemaBundle:InfoPlanCab ipc 
		WHERE 
                UPPER(ipc.nombrePlan) like '%".strtoupper(trim($planNombre))."%' 
                and ipc.empresaCod = '".$idEmpresa."'";
        }
        
        $query = $this->_em->createQuery($sql);
        
        if($start!="" && $limit!=""){
            $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        }
        else{
            $datos = $query->getResult();

        }                

        return $datos;
    }


    public function getPlanRequiereInternet($idplan){
	    $sql = "SELECT prod.id 
		FROM 
		schemaBundle:InfoPlanCab ipc, 
		schemaBundle:InfoPlanDet ipd,
		schemaBundle:AdmiProducto prod 
		WHERE 
		ipc.id = $idplan AND
		ipc.id=ipd.planId AND
		ipd.productoId = prod.id AND prod.codigoProducto in ('INTD')";
		
		$query = $this->_em->createQuery($sql);
		$datos = $query->setFirstResult(0)->setMaxResults(1)->getResult();
		//echo $query->getSQL();
		return $datos;
    }	  
    
   /**
    * Funcion que verifica si el codigo del plan a clonar no se encuentran ya ingresados en un Plan en estado Activo, en
    * ese caso no permitira el clonado del plan hasta que ingresen codigo Unico
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 20-07-2014
    * @param string $strCodigoPlan     
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return $intCantidadplanId
    */
    //$intIdPlan,$strCodigoPlan,$intPlanId
    public function validaCodigoPlan($arrayParametros)
    {   
        $em = $this->_em; 
        $sql = $em->createQuery("SELECT count(p.id) 
              from                                                           
              schemaBundle:InfoPlanCab p         
              where                
              p.id !=:intIdPlan                              
              and p.id !=:intPlanId                                                        
              and p.codigoPlan =:strCodigoPlan
              and p.empresaCod = :strCodigoEmpresa              
              and p.estado=:srtEstado");                
            $sql->setParameter( 'intPlanId' , $arrayParametros['intPlanId']);                             
             $sql->setParameter( 'intIdPlan' , $arrayParametros['intIdPlan']);                      
             $sql->setParameter( 'strCodigoPlan' , $arrayParametros['strCodigoPlan']);
             $sql->setParameter( 'strCodigoEmpresa' , $arrayParametros['strCodigoEmpresa']);                 
             $sql->setParameter( 'srtEstado' , 'Activo');                 
             $intCantidadplanId = $sql->getSingleScalarResult();
        if(!$intCantidadplanId)
        {
            $intCantidadplanId = 0;
        }  
        return $intCantidadplanId;
    }  	    
    
   /**
    * Funcion que verifica si el nombre del plan a clonar no se encuentran ya ingresados en un Plan en estado Activo, en
    * ese caso no permitira el clonado del plan hasta que ingresen nombre Unico
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 20-07-2014     
    * @param string $strNombrePlan     
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return $intCantidadplanId
    */
    public function validaNombrePlan($arrayParametros)
    {   
        $em = $this->_em; 
        $sql = $em->createQuery("SELECT count(p.id) 
              from                                                           
              schemaBundle:InfoPlanCab p         
              where                
              p.id !=:intIdPlan                              
              and p.id !=:intPlanId                              
              and  p.nombrePlan =:strNombrePlan
              and p.empresaCod = :strCodigoEmpresa              
              and p.estado=:srtEstado");                
             $sql->setParameter( 'intPlanId' , $arrayParametros['intPlanId']);                             
             $sql->setParameter( 'intIdPlan' , $arrayParametros['intIdPlan']);                      
             $sql->setParameter( 'strNombrePlan' , $arrayParametros['strNombrePlan']);
             $sql->setParameter( 'strCodigoEmpresa' , $arrayParametros['strCodigoEmpresa']);                 
             $sql->setParameter( 'srtEstado' , 'Activo');                 
             $intCantidadplanId = $sql->getSingleScalarResult();
        if(!$intCantidadplanId)
        {
            $intCantidadplanId = 0;
        }  
        return $intCantidadplanId;
    }

    
    /**
     * getValorInstalacion
     *
     * Metodo encargado de obtener el valor pagado por la instalación del cliente.
     *
     * @param string $strPuntoId Id del punto del cliente
     *          
     * @return array $arrayResultados
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>    
     * @version 1.2 16-09-2015 - Se modifica para que retorne todos los documentos de tipo Factura 
     *                           que son de nombreTipoDocumento 'Factura' y 'Factura proporcional'.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 10-09-2015 - Se modifica para que retorne todas las instalaciones realizadas al
     *                           punto de un cliente.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 19-08-2015
     */  
    public function getValorInstalacion( $strPuntoId )
    {
        $arrayResultados = array();
        
        $query = $this->_em->createQuery();
        
        $strSelect  = "SELECT ipc.id, ipc.nombrePlan, idfd.precioVentaFacproDetalle,
                              idfi.valorImpuesto, idfd.descuentoFacproDetalle ";
        
        $strFrom    = "FROM schemaBundle:InfoDocumentoFinancieroCab idfc,
                            schemaBundle:InfoDocumentoFinancieroDet idfd,
                            schemaBundle:InfoPlanCab ipc,
                            schemaBundle:InfoDocumentoFinancieroImp idfi ";
        
        $strWhere   = "WHERE idfc.id = idfd.documentoId 
                         AND ipc.id = idfd.planId
                         AND idfi.detalleDocId = idfd.id
                         AND ipc.codigoPlan LIKE :instalacion
                         AND idfc.puntoId = :punto
                         AND idfc.tipoDocumentoId IN (
                                                        SELECT atdf.id
                                                        FROM schemaBundle:AdmiTipoDocumentoFinanciero atdf
                                                        WHERE atdf.nombreTipoDocumento = :factura
                                                           OR atdf.nombreTipoDocumento = :facturaProporcional
                                                     ) ";
        
        $query->setParameter("instalacion"        , "%INS%");
        $query->setParameter("punto"              , $strPuntoId);
        $query->setParameter("factura"            , 'Factura proporcional');
        $query->setParameter("facturaProporcional", 'Factura');

        $strDql = $strSelect.$strFrom.$strWhere;
        
        $query->setDQL($strDql);
        
        $arrayResultados = $query->getResult();

        return $arrayResultados;
    }

    /**
     * getTipoServicioByPlan, Obtiene el tipo de servicio enviando el plan ID.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-11-2015
     * 
     * @param array $arrayRequest[
     *                          intIdPlan               => Recibe el ID del plan.
     *                          strPrimerExists         => Recibe la clausala del primer exists del query
     *                          strSegundoExists        => Recibe la clausala del segundo exists del query
     *                          strNombreTecnicoPExists => Recibe el nombre tecnico a buscar en la AdmiProducto del primer exists
     *                          strNombreTecnicoSExists => Recibe el nombre tecnico a buscar en la AdmiProducto del segundo exists
     *                          ]
     * 
     * @return array $arrayResponse[
     *                              strStatus  => Codigo que define que sucedio en el proceso
     *                                            [000 => No realizo alguna accion, 
     *                                             001 => Error, 
     *                                             100 => Proceso realizado con exito].
     *                              strMensaje => Mensaje que describe que sucedio en el proceso.
     *                              arrayData  => Retorna la entidad InfoPlanCab
     *                             ]
     */
    public function getTipoServicioByPlan($arrayRequest)
    {
        $arrayResponse               = array();
        $arrayResponse['strStatus']  = '000';
        $arrayResponse['strMensaje'] = 'No se realizo la consulta';
        $arrayResponse['arrayData']  = '';
        try
        {
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT IPC.id,
                                IPC.codigoPlan ,
                                IPC.nombrePlan ,
                                IPC.descripcionPlan ,
                                IPC.empresaCod ,
                                IPC.descuentoPlan,
                                IPC.estado,
                                IPC.ipCreacion,
                                IPC.feCreacion ,
                                IPC.usrCreacion ,
                                IPC.iva,
                                IPC.idSit,
                                IPC.tipo,
                                IPC.planId ,
                                IPC.codigoInterno ,
                                IPC.feUltMod,
                                IPC.usrUltMod 
                           FROM schemaBundle:InfoPlanDet IPD,
                                schemaBundle:InfoPlanCab IPC
                            WHERE IPC.id = IPD.planId" ;

            //Si $arrayRequest['strPrimerExists'] no es vacia crea el primer EXISTS
            if(!empty($arrayRequest['strPrimerExists']))
            {
                //Concatena con la clausula EXIST
                if("EXISTS" === $arrayRequest['strPrimerExists'])
                {
                    $strQuery .= " AND EXISTS";
                } //Concatena con la clausula NOT EXIST
                else if("NOT EXISTS" === $arrayRequest['strPrimerExists'])
                {
                    $strQuery .= " AND NOT EXISTS";
                }
                $strQuery .= " (SELECT IPCP
                                FROM schemaBundle:InfoPlanCab IPCP,
                                  schemaBundle:InfoPlanDet IPDP ,
                                  schemaBundle:AdmiProducto APP
                                WHERE IPDP.planId   = IPCP.id
                                AND IPDP.productoId = APP.id
                                AND IPCP.id         = IPD.planId
                                AND APP.nombreTecnico = :strNombreTecnicoPExists
                                )";
                $objQuery->setParameter('strNombreTecnicoPExists', $arrayRequest['strNombreTecnicoPExists']);
            }

            //Si $arrayRequest['strSegundoExists'] no es vacia crea el segundo EXISTS
            if(!empty($arrayRequest['strSegundoExists']))
            {
                //Concatena con la clausula EXIST
                if("EXISTS" === $arrayRequest['strSegundoExists'])
                {
                    $strQuery .= " AND EXISTS";
                } //Concatena con la clausula NOT EXIST
                else if("NOT EXISTS" === $arrayRequest['strSegundoExists'])
                {
                    $strQuery .= " AND NOT EXISTS";
                }
                $strQuery .= " (SELECT IPCS
                                FROM schemaBundle:InfoPlanCab IPCS,
                                  schemaBundle:InfoPlanDet IPDS ,
                                  schemaBundle:AdmiProducto APS
                                WHERE IPDS.planId   = IPCS.id
                                AND IPDS.productoId = APS.id
                                AND IPCS.id         = IPD.planId
                                AND APS.nombreTecnico = :strNombreTecnicoSExists
                                )";
                $objQuery->setParameter('strNombreTecnicoSExists', $arrayRequest['strNombreTecnicoSExists']);
            }

            //Crea el filtro por $arrayRequest['intIdPlan']
            if(!empty($arrayRequest['intIdPlan']))
            {
                $strQuery .= " AND IPD.planId = :intIdPlan";
                $objQuery->setParameter('intIdPlan', $arrayRequest['intIdPlan']);
            }

            $strQuery .= " GROUP BY IPC.id,
                                IPC.codigoPlan ,
                                IPC.nombrePlan ,
                                IPC.descripcionPlan ,
                                IPC.empresaCod ,
                                IPC.descuentoPlan,
                                IPC.estado,
                                IPC.ipCreacion,
                                IPC.feCreacion ,
                                IPC.usrCreacion ,
                                IPC.iva,
                                IPC.idSit,
                                IPC.tipo,
                                IPC.planId ,
                                IPC.codigoInterno ,
                                IPC.feUltMod,
                                IPC.usrUltMod";
            $objQuery->setDQL($strQuery);
            $arrayResponse['arrayData']  = $objQuery->getResult();
            $arrayResponse['strMensaje'] = 'Consulta realizada con éxito';
            $arrayResponse['strStatus']  = '100';
        } 
        catch (Exception $ex) 
        {
            $arrayResponse['strMensaje'] = 'Error: ' . $ex->getMessage();
            $arrayResponse['strStatus']  = '001';
        }
        return $arrayResponse;
    } //getTipoServicioByPlan
    
    /**
     * 
     * Metodo que obtiene el json de los clientes dado un id plan enviado como parametro
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-01-2016
     * 
     * @param integer $intIdPlan     
     * @return $jsonData
     */
    public function getJsonClientesPorPlanId($intIdPlan)
    {
        $arrayResultado = $this->getResultadoClientesPorPlanId($intIdPlan);        
        
        $total     = $arrayResultado['total'];                        
        $resultado = $arrayResultado['resultado'];
        
        if($resultado)
        {
            foreach($resultado as $data)
            {
                $arrayEncontrados[]=array('id_parte_afectada'    =>$data['idPunto'],
                                         'nombre_parte_afectada' =>$data['login'],
                                         'id_descripcion_1'      =>$data['idPersona'],
                                         'nombre_descripcion_1'  =>$data['nombre'],
                                         'id_descripcion_2'      => '',
                                         'nombre_descripcion_2'  =>'');
            }

            $arrayRespuesta = array('total'=> $total , 'encontrados' => $arrayEncontrados);                                            
        }
        else
        {
            $arrayRespuesta = array('total'=> 0 , 'encontrados' => '[]');                                            
        }
        
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    /**
     * 
     * Metodo que obtiene el resultado del query de los clientes dado un id plan enviado como parametro
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 10-01-2016
     * 
     * @param $intIdPlan     
     * @return $arrayResultado [ total , resultado ]
     */
    public function getResultadoClientesPorPlanId($intIdPlan)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $query = $this->_em->createNativeQuery(null, $rsm);      
            
            $strSelectCont = " SELECT COUNT(*) CONT ";

            $strSelectSql   = "SELECT PUNTO.ID_PUNTO,
                            PUNTO.LOGIN,
                            PERSONA.ID_PERSONA,
                            NVL(PERSONA.RAZON_SOCIAL, PERSONA.NOMBRES
                            ||' '
                            ||PERSONA.APELLIDOS) NOMBRE";
            
             $strSql .= " FROM INFO_PLAN_CAB CAB,
                            INFO_SERVICIO SERVICIO,
                            INFO_PUNTO PUNTO,
                            INFO_PERSONA_EMPRESA_ROL PERSONA_ROL,
                            INFO_PERSONA PERSONA
                          WHERE CAB.ID_PLAN              = SERVICIO.PLAN_ID
                          AND SERVICIO.PUNTO_ID          = PUNTO.ID_PUNTO
                          AND PERSONA_ROL.ID_PERSONA_ROL = PUNTO.PERSONA_EMPRESA_ROL_ID
                          AND PERSONA_ROL.PERSONA_ID     = PERSONA.ID_PERSONA
                          AND CAB.ID_PLAN                = :plan";                        

            $rsm->addScalarResult('ID_PUNTO', 'idPunto', 'integer');
            $rsm->addScalarResult('LOGIN', 'login', 'string');
            $rsm->addScalarResult('ID_PERSONA', 'idPersona', 'integer');
            $rsm->addScalarResult('NOMBRE', 'nombre', 'string');

            $query->setParameter('plan',$intIdPlan);              

            $query->setSQL($strSelectCont.$strSql);

            $arrayResultado['total'] = $query->getSingleScalarResult();

            $query->setSQL($strSelectSql.$strSql);

            $arrayResultado['resultado'] = $query->getArrayResult();
        
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;
    }
    
    
    /**
     * getPlanesByEmpresa, obtiene los planes para la empresa enviada como parámetro.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 14-12-2016
     * 
     * @param array $arrayParametros[]
     *              'strPrefijoEmpresa'   => Recibe el prefijo de la empresa en sesión.
     *              'strEstadoEmpresa'    => Recibe el estado de la empresa 
     *              'strEstadoPlan'       => Recible el estado del plan
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con el resultado de la consulta
     */
    public function getPlanesByEmpresa($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT COUNT(ipc) ";
            $objQuery      = $this->_em->createQuery();
            
            $strQuery = "  SELECT ipc.id            intIdObj,    "
                       ."         ipc.nombrePlan    strDescripcionObj ";
            $strFromQuery = "  FROM "
                               . "schemaBundle:InfoPlanCab ipc, "
                               . "schemaBundle:InfoEmpresaGrupo ieg "
                               . "WHERE "
                               . "ipc.empresaCod   = ieg.id "
                               . "AND ieg.estado   = :strEstadoEmpresa "
                               . "AND ipc.estado   = :strEstadoPlan "
                               . "AND ieg.prefijo  = :strPrefijoEmpresa "; 
                    
            $objQuery->setParameter('strEstadoEmpresa' , $arrayParametros['strEstadoEmpresa']);
            $objQuery->setParameter('strEstadoPlan'    , $arrayParametros['strEstadoPlan']);
            $objQuery->setParameter('strPrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
            $objQuery->setDQL($strQuery . $strFromQuery);
            $objReturnResponse->setRegistros($objQuery->getResult());
            $objReturnResponse->setTotal(0);
            
            if($objReturnResponse->getRegistros())
            { 
                $strQueryCount = $strQueryCount . $strFromQuery;
                $objQueryCount->setDQL($strQueryCount);
                $objReturnResponse->setTotal($objQueryCount->getSingleScalarResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existio un error en getPlanesByEmpresa - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } 
    
    /*
      * Recibe un conjunto de parametros para obtener si un plan pertenece a grupo de internet
      * @author jose vinueza<jdvinueza@telconet.ec>
      * @version 1.0 18-08-2017
      * 
     */
    public function isPlanesByGrupo($arrayParametros)
    {
       try
        {
           $objReturnResponse = new ReturnResponse();
           $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
           $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        
            $boolRetorno = false;
            $objQuery      = $this->_em->createQuery();
            
            $strQuery = "  select count(det.planId) as valor "
                               ."  FROM "
                               . "schemaBundle:InfoPlanDet det, "
                               . "schemaBundle:AdmiProducto pr "
                               . "WHERE det.productoId = pr.id "
                               . "and pr.empresaCod = :strCodEmpresa "
                               . "and pr.grupo = :strGrupo "
                               . "and det.planId = :idPlan "; 
                    
            $objQuery->setParameter('strCodEmpresa' , $arrayParametros['strCodEmpresa']);
            $objQuery->setParameter('strGrupo'    , $arrayParametros['strGrupo']);
            $objQuery->setParameter('idPlan', $arrayParametros['idPlan']);
            $objQuery->setDQL($strQuery);
            $objReturnResponse->setTotal(0);
            
            $objReturnResponse->setTotal($objQuery->getSingleScalarResult());
            if ($objReturnResponse->getTotal() == 1 )
            {
                $boolRetorno = true;
                
            }           
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existio un error en isPlanesByGrupo - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        
        
        return $boolRetorno;
    
    }
    
    
        
}
