<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;

class AdmiTipoRolRepository extends EntityRepository
{

    /**
     * getRolByTipoRol, obtiene los roles segun los tipo de rol y empresa.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 25-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              'arrayEmpresaRol'           => ['arrayEstado'        => Recibe el estado de la empresa rol]
     *                              'arrayTipoRol'              => ['arrayEstadoTipoRol' => Recible el estado del rol
     *                                                              'arrayTipoRol'       => Recibe la descripcion tipo rol a buscar]
     *                              'intStart'                  => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                  => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getRolByTipoRol($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(ar.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT ar.id intIdRol, "
                             . "ar.descripcionRol strDescripcionRol, "
                             . "ar.estado strEstadoRol, "
                             . "ar.usrCreacion strUsrCreacionRol, "
                             . "ar.feCreacion dateFeCreacionRol, "
                             . "ar.usrUltMod strUsrUltModRol, "
                             . "ar.feUltMod dateFeUltModRol, "
                             . "ar.esJefe strEsJefeRol, "
                             . "ar.permiteAsignacion strPermiteAsigancionRol, "
                             . "atr.id intIdTipoRol, "
                             . "atr.descripcionTipoRol strDescripcionTipoRol, "
                             . "atr.estado strEsatdoTipoRol, "
                             . "ier.id intIdEmpresaRol, "
                             . "ier.estado strEstadoEmpresaRol, "
                             . "ieg.id intIdEmpresa, "
                             . "ieg.prefijo strPrefijo, "
                             . "ieg.estado strEstadoEmpresa ";

            $strFromQuery = "FROM schemaBundle:AdmiRol ar, "
                              . " schemaBundle:AdmiTipoRol atr, "
                              . " schemaBundle:InfoEmpresaRol ier, "
                              . " schemaBundle:InfoEmpresaGrupo ieg "
                              . " WHERE ar.id = ier.rolId "
                              . " AND ar.tipoRolId = atr.id "
                              . " AND ier.empresaCod = ieg.id ";
            if(!empty($arrayParametros['strDisponiblesPersona']))
            {
                $strFromQuery .= " AND NOT EXISTS ( SELECT arne "
                               . "                  FROM schemaBundle:InfoPersonaEmpresaRol iperne, "
                               . "                       schemaBundle:InfoEmpresaRol ierne, "
                               . "                       schemaBundle:AdmiRol arne "
                               . "                  WHERE iperne.empresaRolId = ierne.id "
                               . "                      AND ierne.rolId = arne.id "
                               . "                      AND ierne.id = ier.id ";
            }
            
            //Pregunta si $arrayParametros['arrayEmpRolDis']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpRolDis']['arrayEstado'])
                && !empty($arrayParametros['strDisponiblesPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ierne.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoEmpRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpRolDis']['arrayEstado'];
                $arrayParams['strComparador']   = $arrayParametros['arrayEmpRolDis']['strComparadorEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEmpRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEmpRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayPersona']['arrayPersona'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['arrayEstado'])
                && !empty($arrayParametros['strDisponiblesPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iperne.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoPerEmpRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayEstado'];
                $arrayParams['strComparador']   = $arrayParametros['arrayPersona']['strComparadorEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoPerEmpRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoPerEmpRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersona']['arrayPersona'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['arrayPersona'])
                && !empty($arrayParametros['strDisponiblesPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iperne.personaId ';
                $arrayParams['strBindParam']    = ':arrayPersona';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayPersona'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams) . ' ) ';
                $objQuery->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.descripcionTipoRol ';
                $arrayParams['strBindParam']    = ':arrayDescripcionTipoRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayTipoRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayTipoRol']['strComparadorEstadoATR'];
                $arrayParams['strBindParam']    = ':arrayEstadoTipoRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayRol']['strComparadorEstadoAR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoEmpresaRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayEmpresaRol']['strComparadorEstadoIER'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEmpresa'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaRol']['arrayEmpresa']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.empresaCod ';
                $arrayParams['strBindParam']    = ':arrayEstadoEmpresaCod';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaRol']['arrayEmpresa'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEmpresaCod', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEmpresaCod', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresa']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresa']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieg.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoEmpresa';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresa']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEmpresa', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEmpresa', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresa']['arrayPrefijo'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresa']['arrayPrefijo']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieg.prefijo ';
                $arrayParams['strBindParam']    = ':arrayPrefijo';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresa']['arrayPrefijo'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPrefijo', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPrefijo', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['strOrderBy'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strOrderBy']))
            {
                $strFromQuery .= " ORDER BY ar.descripcionRol " . $arrayParametros['strOrderBy'];
            }

            $objQuery->setDQL($strQuery . $strFromQuery);
            //Pregunta si $arrayParametros['intStart'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }
            //Pregunta si $arrayParametros['intLimit'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }
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
            $objReturnResponse->setStrMessageStatus('Existion un error en getRolByTipoRol - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getRolByTipoRol

    /**
     * getRolDisponiblesPersona, obtiene los roles disponibles para una persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 25-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              'arrayEmpresaRol'           => ['arrayEstado'        => Recibe el estado de la empresa rol]
     *                              'arrayTipoRol'              => ['arrayEstadoTipoRol' => Recible el estado del rol
     *                                                              'arrayTipoRol'       => Recibe la descripcion tipo rol a buscar]
     *                              'intStart'                  => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                  => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getRolDisponiblesPersona($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(ar.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT ar.id intIdRol, "
                             . "ar.descripcionRol strDescripcionRol, "
                             . "ar.estado strEstadoRol, "
                             . "ar.usrCreacion strUsrCreacionRol, "
                             . "ar.feCreacion dateFeCreacionRol, "
                             . "ar.usrUltMod strUsrUltModRol, "
                             . "ar.feUltMod dateFeUltModRol, "
                             . "ar.esJefe strEsJefeRol, "
                             . "ar.permiteAsignacion strPermiteAsigancionRol, "
                             . "atr.id intIdTipoRol, "
                             . "atr.descripcionTipoRol strDescripcionTipoRol, "
                             . "atr.estado strEsatdoTipoRol, "
                             . "ier.id intIdEmpresaRol, "
                             . "ier.estado strEstadoEmpresaRol, "
                             . "ieg.id intIdEmpresa, "
                             . "ieg.prefijo strPrefijo, "
                             . "ieg.estado strEstadoEmpresa ";

            $strFromQuery = "FROM schemaBundle:AdmiRol ar, "
                              . " schemaBundle:AdmiTipoRol atr, "
                              . " schemaBundle:InfoEmpresaRol ier, "
                              . " schemaBundle:InfoEmpresaGrupo ieg "
                              . " WHERE ar.id = ier.rolId "
                              . " AND ar.tipoRolId = atr.id "
                              . " AND ier.empresaCod = ieg.id ";

            //Pregunta si $arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.descripcionTipoRol ';
                $arrayParams['strBindParam']    = ':arrayDescripcionTipoRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayTipoRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayTipoRol']['strComparadorEstadoATR'];
                $arrayParams['strBindParam']    = ':arrayEstadoTipoRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayRol']['strComparadorEstadoAR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEmpresaRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoEmpresaRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayEmpresaRol']['strComparadorEstadoIER'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresa']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEmpresa']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieg.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoEmpresa';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresa']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoEmpresa', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoEmpresa', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresa']['arrayPrefijo'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEmpresa']['arrayPrefijo']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieg.prefijo ';
                $arrayParams['strBindParam']    = ':arrayPrefijo';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresa']['arrayPrefijo'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPrefijo', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPrefijo', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['strOrderBy'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strOrderBy']))
            {
                $strFromQuery .= " ORDER BY ar.descripcionRol " . $arrayParametros['strOrderBy'];
            }

            $objQuery->setDQL($strQuery . $strFromQuery);
            //Pregunta si $arrayParametros['intStart'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intStart']))
            {
                $objQuery->setFirstResult($arrayParametros['intStart']);
            }
            //Pregunta si $arrayParametros['intLimit'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intLimit']))
            {
                $objQuery->setMaxResults($arrayParametros['intLimit']);
            }
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
            $objReturnResponse->setStrMessageStatus('Existion un error en getRolByTipoRol - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getRolByTipoRol

    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_tipo_rol' =>$data->getId(),
                                         'descripcion_tipo_rol' =>trim($data->getDescripcionTipoRol()),
                                         'estado' =>(trim($data->getEstado())=='ELIMINADO' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($data->getEstado())=='ELIMINADO' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($data->getEstado())=='ELIMINADO' ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_tipo_rol' => 0 , 'descripcion_tipo_rol' => 'Ninguno', 'tipo_rol_id' => 0 , 'tipo_rol_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiTipoRol','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.descripcionTipoRol) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
}
