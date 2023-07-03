<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPersonaContactoRepository extends EntityRepository
{
	public function findPorPersona($idEmpresa,$idPersona){	
		$query = $this->_em->createQuery("SELECT b
		FROM 
                schemaBundle:InfoPersona a,schemaBundle:InfoPersonaContacto b, 
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d
		WHERE 
                b.contactoId=$idPersona AND
                c.id=b.personaEmpresaRolId AND
                a.id=c.personaId AND
                c.empresaRolId=d.id AND
                d.empresaCod='$idEmpresa' AND
                b.estado='Activo'");
                //echo $query->getSQL(); die;
		$datos = $query->getResult();
             
		return $datos;
	}
        
        public function findPorCliente($idEmpresa,$idPersona,$limit,$page,$start){            
		$query = $this->_em->createQuery("SELECT persCont
		FROM 
                schemaBundle:InfoPersona contacto, schemaBundle:InfoPersona cliente,
                schemaBundle:InfoPersonaContacto persCont, schemaBundle:InfoPersonaEmpresaRol perEmpRol , 
                schemaBundle:InfoEmpresaRol empRol
		WHERE 
                cliente.id=$idPersona AND
                cliente.id=perEmpRol.personaId AND
                perEmpRol.id=persCont.personaEmpresaRolId AND
                perEmpRol.empresaRolId=empRol.id AND
                empRol.empresaCod='$idEmpresa' AND                
                contacto.id=persCont.contactoId AND
                cliente.id=perEmpRol.personaId AND
                contacto.estado='Activo' 
                order by contacto.feCreacion DESC");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
                //print_r($resultado['registros']);die;
		return $resultado;
	}

        public function findTotalContactosPorCliente($idEmpresa,$idPersona){            
		$query = $this->_em->createQuery("SELECT count(a) as total
		FROM 
                schemaBundle:InfoPersona a,schemaBundle:InfoPersonaContacto b, 
                schemaBundle:InfoPersonaEmpresaRol c, schemaBundle:InfoEmpresaRol d
		WHERE 
                c.personaId=$idPersona AND
                b.personaEmpresaRolId=c.id AND    
                a.id=c.personaId AND
                c.empresaRolId=d.id AND
                d.empresaCod='$idEmpresa' AND
                b.estado='Activo'");
		$datos = $query->getResult();
		return $datos;
	}
	public function find30PorEmpresaPorCliente($idEmpresa,$limit,$page,$start,$idCliente){
                $criterioCliente='';
                if($idCliente){
                    $criterioCliente=" cliente.id=$idCliente AND ";
                }
				
		$query = $this->_em->createQuery("SELECT contacto
		FROM 
                schemaBundle:InfoPersona contacto, schemaBundle:InfoPersona cliente,
                schemaBundle:InfoPersonaContacto persCont, schemaBundle:InfoPersonaEmpresaRol perEmpRol , 
                schemaBundle:InfoEmpresaRol empRol
		WHERE 
                $criterioCliente
                cliente.id=perEmpRol.personaId AND
                perEmpRol.id=persCont.personaEmpresaRolId AND
                perEmpRol.empresaRolId=empRol.id AND
                empRol.empresaCod='$idEmpresa' AND                
                contacto.id=persCont.contactoId AND
                
                persCont.estado='Activo' 
                order by contacto.feCreacion DESC"
                )->setMaxResults(30);
		$datos = $query->getResult();
                //echo $query->getSQL();
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}         
        
	public function findPersonasPorCriterios($estado,$idEmpresa,$fechaDesde,$fechaHasta,$nombre,$limit,$page,$start,$idCliente){
                $criterio_fecha_desde='';
                $criterio_fecha_hasta='';
                $nombre=  strtoupper($nombre);  
                $criterioCliente='';
                if($idCliente){
                    $criterioCliente=" cliente.id=$idCliente AND ";
                }                
                if ($fechaDesde){
                    $fechaD = date("Y/m/d", strtotime($fechaDesde));			
                    $fechaDesde = $fechaD ;
                    $criterio_fecha_desde=" contacto.feCreacion >= '$fechaDesde' AND ";
                }
                if ($fechaHasta){
                    $fechaH = date("Y/m/d", strtotime($fechaHasta));			
                    $fechaHasta = $fechaH;
                    $criterio_fecha_hasta=" contacto.feCreacion <= '$fechaHasta' AND ";
                }
                $criterio_estado='';
                $criterio_nombre='';             
                if ($estado){       
                    $criterio_estado="contacto.estado = '$estado' AND ";
                }    
                if ($nombre){       
                    $criterio_nombre=" (CONCAT(UPPER(contacto.nombres),CONCAT(' ',UPPER(contacto.apellidos))) like '%$nombre%' OR UPPER(contacto.razonSocial) like '%$nombre%') AND ";
                }  
		$query = $this->_em->createQuery("SELECT contacto
		FROM 
                schemaBundle:InfoPersona contacto, schemaBundle:InfoPersona cliente,
                schemaBundle:InfoPersonaContacto persCont, schemaBundle:InfoPersonaEmpresaRol perEmpRol , 
                schemaBundle:InfoEmpresaRol empRol, schemaBundle:AdmiRol rol, schemaBundle:AdmiTipoRol tipoRol
		WHERE 
                contacto.id=persCont.contactoId AND
                cliente.id=perEmpRol.personaId AND
                perEmpRol.empresaRolId=empRol.id AND
                empRol.rolId=rol.id AND
                rol.tipoRolId=tipoRol.id AND
                $criterioCliente  
                empRol.empresaCod='$idEmpresa' AND
                $criterio_estado
                $criterio_nombre   
                $criterio_fecha_desde
                $criterio_fecha_hasta                    
                contacto.estado='Activo' 
                order by a.feCreacion DESC");
                //echo $query->getSQL();die;
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}
	
	
     /**
     * getResultadoContactosPorCliente
     * 
     * Esta funcion ejecuta el Query que retorna los contactos de un cliente
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 28-12-2015 
     * 
     * @param integer  $intIdEmpresa
     * @param integer  $intIdCliente
     * 
     * @return array $arrayContactos        Consulta de la BD
     * 
     */
    public function getResultadoContactosPorCliente($intIdEmpresa,$intIdCliente)
    {    
        $arrayContactos = array();        
        $objQuery       = $this->_em->createQuery();         
        $strCampos      = " SELECT 
                               contacto";
                          
        $strFrom        = " FROM 
                                schemaBundle:InfoPersona contacto,
                                schemaBundle:InfoPersona cliente,
                                schemaBundle:InfoPersonaContacto persCont,
                                schemaBundle:InfoPersonaEmpresaRol perEmpRol,
                                schemaBundle:InfoEmpresaRol empRol
                            
                            WHERE 
                                cliente.id             = :varClienteId 
                            AND
                                cliente.id             = perEmpRol.personaId
                            AND
                                perEmpRol.id           = persCont.personaEmpresaRolId
                            AND
                                perEmpRol.empresaRolId = empRol.id
                            AND
                                empRol.empresaCod      = :varEmpresaCod
                            AND
                                contacto.id            = persCont.contactoId
                            AND
                                persCont.estado        IN (:varEstado)
                            ORDER BY 
                                contacto.feCreacion DESC ";

        $strSelect     = $strCampos . $strFrom;
        $objQuery->setParameter("varEstado", array('Activo','Inactivo'));
        $objQuery->setParameter("varClienteId", $intIdCliente);
        $objQuery->setParameter("varEmpresaCod", $intIdEmpresa);
        $objQuery->setDQL($strSelect); 
        $arrayContactos = $objQuery->getResult();

        return $arrayContactos;
    } 
    
     /**
     * getResultadoTipoContactosPorCliente
     * 
     * Funcion que retorna un tipo de contacto especificado por el parametro $strTipoContacto
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 14-03-2016 
     * 
     * @param integer  $intIdEmpresa
     * @param integer  $intIdCliente
     * @param string   $strTipoContacto 
     * 
     * @return objContactoTecnico
     * 
     */
    public function getResultadoTipoContactosPorCliente($intIdEmpresa,$intIdCliente,$strTipoContacto)
    {    
        $arrayContactos = array();        
        $objQuery       = $this->_em->createQuery();         
        $strCampos      = " SELECT 
                               contacto";
                          
        $strFrom        = " FROM 
                                schemaBundle:InfoPersona contacto,
                                schemaBundle:InfoPersona cliente,
                                schemaBundle:InfoPersonaContacto persCont,
                                schemaBundle:InfoPersonaEmpresaRol perEmpRol,
                                schemaBundle:InfoEmpresaRol empRol
                            
                            WHERE 
                                cliente.id             = :varClienteId 
                            AND
                                cliente.id             = perEmpRol.personaId
                            AND
                                perEmpRol.id           = persCont.personaEmpresaRolId
                            AND
                                perEmpRol.empresaRolId = empRol.id
                            AND
                                empRol.empresaCod      = :varEmpresaCod
                            AND
                                contacto.id            = persCont.contactoId
                            AND
                                persCont.estado        IN (:varEstado)
                            ORDER BY 
                                contacto.feCreacion DESC ";

        $strSelect     = $strCampos . $strFrom;
        $objQuery->setParameter("varEstado", array('Activo','Inactivo'));
        $objQuery->setParameter("varClienteId", $intIdCliente);
        $objQuery->setParameter("varEmpresaCod", $intIdEmpresa);
        $objQuery->setDQL($strSelect); 
        $arrayContactos = $objQuery->getResult();
        
        $objContactoTecnico = null;
        
        if(count($arrayContactos)>0)
        {
            foreach($arrayContactos as $contacto):
                $objQuery2       = $this->_em->createQuery(); 
                $strConsulta = "   select rol
                                   from   schemaBundle:InfoPersona ip, 
                                          schemaBundle:InfoPersonaEmpresaRol iper,
                                          schemaBundle:InfoEmpresaRol ier,
                                          schemaBundle:AdmiRol rol
                                   where  iper.personaId    = ip.id
                                   and    ier.id            = iper.empresaRolId
                                   and    rol.id            = ier.rolId
                                   and    ip.id             = :varContactoId
                                   and    ier.empresaCod    = :varEmpresaCod
                                   
                                   and    ip.estado         = :varEstadoContacto"; 

               
               $objQuery2->setParameter("varEstadoContacto", 'Activo');
               $objQuery2->setParameter("varContactoId", $contacto->getId());
               $objQuery2->setParameter("varEmpresaCod", $intIdEmpresa);
               $objQuery2->setDQL($strConsulta);               
               $objResultado = $objQuery2->getResult();
               $objRol= $objResultado[0];
               if($objResultado)                  
               {
                    $objRol= $objResultado[0];
                    if($objRol->getDescripcionRol()==trim($strTipoContacto))
                    {
                        $objContactoTecnico = $contacto; 
                        return $objContactoTecnico;
                    } 
                    
               }
            endforeach;
        }
        return $objContactoTecnico;  
    }  	    
        


    /**
     * getContactosDeClientePunto, obtiene la informacion del contacto de un cliente o punto segun un rol contacto
     * El count realiza el conteo no agrupado
     * 
     * @param array $arrayParametros[
     *                              strDescripcionTipoRol   =>  Recibe la descripcion del tipo de rol
     *                              strEstadoTipoRol        =>  Recibe el estado de la descripcion del tipo de rol
     *                              strDescripcionRol       =>  Recibe la descripcion del rol
     *                              strEstadoRol            =>  Recibe el estado del rol
     *                              strEmpresaCod           =>  Recibe el codigo de la empresa
     *                              strEstadoIER            =>  Recibe el estado de empresa rol
     *                              strEstadoIPER           =>  Recibe el estado de la info persona empresa rol
     *                              strEstadoIPC            =>  Recibe el estado de la info persona contacto
     *                              intIdPersonaEmpresaRol  =>  Recibe el id de la persona empresa rol
     *                              strNombrePersona        =>  Recibe el nombre de la persona
     *                              strFechaIncio           =>  Recibe la fecha inicio de creacion
     *                              strFechaFin             =>  Recibe la fecha fin de creacion
     *                              intStart                =>  Recibe el inicio para el resultado de la busqueda.
     *                              intLimit                =>  Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * 
     * @return Object ReturnResponse Retorna un mensaje y estado de la consulta con sus datos y el numero de registros.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 15-06-2018 - Se añade filtro por Clientes y Puntos Activos a la consulta de Contactos. 
     * 
     */
    function getContactosDeClientePunto($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount  = $this->_em->createQuery();
            $strQueryCount  = "SELECT count(ipr.id) ";
            $objQuery       = $this->_em->createQuery();
            $strQuery       = "SELECT ipr.id intIdPersona, "
                            . " COALESCE(ipr.razonSocial, ipr.nombres, ipr.representanteLegal) strNombres, "
                            . " ipr.razonSocial, "
                            . " ipr.nombres, "
                            . " ipr.representanteLegal, "
                            . " ipr.apellidos strApellidos, "
                            . " ipr.identificacionCliente strIdentificacionCliente, "
                            . " ipr.tipoIdentificacion strTipoIdentificacion, "
                            . " ipr.feCreacion dateFeCreacion, "
                            . " ipr.usrCreacion strUsrCreacion, "
                            . " ipr.estado strEstado, ";

            //Si $arrayParametros['strGroupBy'] no agrupara el resultado
            if(empty($arrayParametros['strGroupBy']))
            {
                $strQuery  .= " ar.descripcionRol strDescripcionRol, "
                            . " iper.id intIdPersonaEmpresaRol, ";
            }
            $strQuery      .= " at.descripcionTitulo strDescripcionTitulo, "
                            . " at.id intIdTitulo ";

            $strFromQuery = "FROM schemaBundle:AdmiTipoRol atr, "
                            . " schemaBundle:AdmiRol ar, "
                            . " schemaBundle:InfoEmpresaRol ier, ";

            //Setea la tabla InfoPersonaContacto por default en la variable $strJoinPuntoPersona
            $strJoinPuntoPersona = " schemaBundle:InfoPersonaContacto ipc, ";

            //Si $arrayParametros['strJoinPunto'] no esta vacia setea la tabla InfoPuntoContacto en la variable $strJoinPuntoPersona
            if(!empty($arrayParametros['strJoinPunto']))
            {
                $strJoinPuntoPersona = " schemaBundle:InfoPuntoContacto ipc, ";
            }

            $strFromQuery .= $strJoinPuntoPersona;

            $strFromQuery .=  " schemaBundle:InfoPersonaEmpresaRol iper, "
                            . " schemaBundle:InfoPersona ipr"
                            . " LEFT JOIN schemaBundle:AdmiTitulo at WITH at.id = ipr.tituloId "
                            . " WHERE ar.tipoRolId              =   atr.id "
                            . " AND   ier.rolId                 =   ar.id "
                            . " AND   iper.empresaRolId         =   ier.id "
                            . " AND   ipc.contactoId            =   iper.personaId "
                            . " AND   ipc.contactoId            =   ipr.id ";

            //Pregunta si $arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.descripcionTipoRol ';
                $arrayParams['strBindParam']    = ':arrayDescripcionTipoRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayTipoRol']['strComparadorDescTR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['arrayDescripcionTipoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayTipoRol']['strEstadoTipoRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayTipoRol']['strEstadoTipoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.estado ';
                $arrayParams['strBindParam']    = ':strEstadoTipoRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayTipoRol']['strComparadorEstadoTR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['strEstadoTipoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('strEstadoTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('strEstadoTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
 
            }
            
            //Pregunta si $arrayParametros['arrayTipoRol']['arrayTipoRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayTipoRol']['arrayTipoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.id ';
                $arrayParams['strBindParam']    = ':arrayTipoRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayTipoRol']['strComparadorTR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayTipoRol']['arrayTipoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
 
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayDescripcionRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayRol']['arrayDescripcionRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.descripcionRol ';
                $arrayParams['strBindParam']    = ':arrayDescripcionRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayRol']['strComparadorDescR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayRol']['arrayDescripcionRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayDescripcionRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayDescripcionRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayEstadoRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayRol']['arrayEstadoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayRol']['strComparadorEstadoR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayRol']['arrayEstadoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayRol']['arrayRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.id ';
                $arrayParams['strBindParam']    = ':arrayRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayRol']['strComparadorRol'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayRol']['arrayRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayTitulo']['arrayTitulo'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayTitulo']['arrayTitulo']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' at.id ';
                $arrayParams['strBindParam']    = ':arrayTitulo';
                $arrayParams['strComparador']   = $arrayParametros['arrayTitulo']['strComparadorTitulo'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayTitulo']['arrayTitulo'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayTitulo', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayTitulo', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEmpresaCod'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaRol']['arrayEmpresaCod']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.empresaCod ';
                $arrayParams['strBindParam']    = ':arrayEmpresaCod';
                $arrayParams['strComparador']   = $arrayParametros['arrayEmpresaRol']['strComparadorER'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaRol']['arrayEmpresaCod'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEmpresaCod', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEmpresaCod', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresaRol']['arrayEstadoER'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaRol']['arrayEstadoER']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoER';
                $arrayParams['strComparador']   = $arrayParametros['arrayEmpresaRol']['strComparadorEstR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaRol']['arrayEstadoER'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoER', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPerEmpRol']['arrayEstadoPerEmpRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPerEmpRol']['arrayEstadoPerEmpRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iper.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoPerEmpRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayPerEmpRol']['strComparadorEstPER'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPerEmpRol']['arrayEstadoPerEmpRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoPerEmpRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoPerEmpRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaPuntoContacto']['arrayEstadoPerPuntoContacto'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoContacto']['arrayEstPerPuntoContacto']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoPerContacto';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersonaPuntoContacto']['strComparadorEstPPC'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaPuntoContacto']['arrayEstPerPuntoContacto'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoPerContacto', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoPerContacto', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaPuntoContacto']['arrayPersonaEmpresaRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoContacto']['arrayPersonaEmpresaRol']) 
                && empty($arrayParametros['strJoinPunto']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.personaEmpresaRolId ';
                $arrayParams['strBindParam']    = ':arrayPersonaEmpresaRol';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersonaPuntoContacto']['strComparadorPER'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaPuntoContacto']['arrayPersonaEmpresaRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersonaEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersonaEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayPersonaPuntoContacto']['arrayPunto'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoContacto']['arrayPunto']) 
                && !empty($arrayParametros['strJoinPunto']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.puntoId ';
                $arrayParams['strBindParam']    = ':arrayPunto';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersonaPuntoContacto']['strComparadorPunto'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaPuntoContacto']['arrayPunto'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPunto', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPunto', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersona']['strNombrePersona'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['strNombrePersona']))
            {
                $strFromQuery .= " AND (UPPER(ipr.nombres) LIKE :strNombrePersona"
                               . " OR UPPER(ipr.apellidos) LIKE :strNombrePersona)";
                $objQuery->setParameter('strNombrePersona', '%' . $arrayParametros['arrayPersona']['strNombrePersona'] . '%');
                $objQueryCount->setParameter('strNombrePersona', '%' . $arrayParametros['arrayPersona']['strNombrePersona'] . '%');
            }
            
            //Pregunta si $arrayParametros['arrayPersona']['arrayNombres'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['arrayNombres']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' UPPER(ipr.nombres) ';
                $arrayParams['strBindParam']    = ':arrayNombres';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersona']['strComparadorNmbP'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayNombres'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayNombres', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayNombres', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayPersona']['arrayApellidos'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['arrayApellidos']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' UPPER(ipr.apellidos) ';
                $arrayParams['strBindParam']    = ':arrayApellidos';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersona']['strComparadorAplP'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayApellidos'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayApellidos', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayApellidos', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['strEsatdoIPR'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['arrayEstadoIPR']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipr.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPR';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersona']['strComparadorEstIPR'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayEstadoIPR'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPR', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPR', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayPersonaPuntoContacto']['strUsrCreacion'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoContacto']['arrayUsrCreacion']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.usrCreacion ';
                $arrayParams['strBindParam']    = ':arrayUsrCreacion';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersonaPuntoContacto']['strComparadorUsrCreaIPPC'];
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaPuntoContacto']['arrayUsrCreacion'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayUsrCreacion', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayUsrCreacion', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaPuntoContacto']['strFechaIncio'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoContacto']['strFechaIncio']))
            {
                $strFromQuery .= " AND ipc.feCreacion >= :strFechaIncio";
                $objQuery->setParameter('strFechaIncio', $arrayParametros['arrayPersonaPuntoContacto']['strFechaIncio']);
                $objQueryCount->setParameter('strFechaIncio', $arrayParametros['arrayPersonaPuntoContacto']['strFechaIncio']);
            }
            
            //Pregunta si $arrayParametros['arrayPersonaPuntoContacto']['strFechaFin'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoContacto']['strFechaFin']))
            {
                $strFromQuery .= " AND ipc.feCreacion <= :strFechaFin";
                $objQuery->setParameter('strFechaFin', $arrayParametros['arrayPersonaPuntoContacto']['strFechaFin']);
                $objQueryCount->setParameter('strFechaFin', $arrayParametros['arrayPersonaPuntoContacto']['strFechaFin']);
            }
            
            //Filtra por los Clientes y Puntos Activos
            $strFromQuery .= " AND ipr.estado = :strEstado";
            $objQuery->setParameter('strEstado', "Activo");
            $objQueryCount->setParameter('strEstado', "Activo");
            
            if(!empty($arrayParametros['strGroupBy']))
            {
                $strFromQuery  .= " GROUP BY ipr.id, "
                                . " ipr.razonSocial, "
                                . " ipr.nombres, "
                                . " ipr.representanteLegal, "
                                . " ipr.apellidos, "
                                . " ipr.identificacionCliente, "
                                . " ipr.tipoIdentificacion, "
                                . " ipr.feCreacion, "
                                . " ipr.usrCreacion, "
                                . " ipr.estado, "
                                . " at.descripcionTitulo, "
                                . " at.id ";
            }
            
            if(!empty($arrayParametros['strGroupBy']))
            {
                $strFromQuery  .= " ORDER BY ipr.id, "
                                . " ipr.razonSocial, "
                                . " ipr.nombres, "
                                . " ipr.representanteLegal, "
                                . " ipr.apellidos, "
                                . " ipr.identificacionCliente, "
                                . " ipr.tipoIdentificacion, "
                                . " ipr.feCreacion, "
                                . " ipr.usrCreacion, "
                                . " ipr.estado, "
                                . " at.descripcionTitulo, "
                                . " at.id ";
            }
            else
            {
                $strFromQuery .= " ORDER BY ipr.razonSocial, ipr.nombres, ipr.representanteLegal, ipr.feCreacion ASC ";
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
                $objReturnResponse->setTotal($objQueryCount->getResult());
            }
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrMessageStatus('Existion un error en getContactosDeClientePunto - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getContactosDeClientePunto

    /**
     * getRolesPersonaPunto, obtiene los roles de una persona o del punto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              'arrayEmpresaRol'           => ['arrayEstado'] Recibe el estado de la empresa rol
     *                              'arrayRol'                  => ['arrayEstado'] Recible el estado del rol
     *                              'arrayPersonaEmpresaRol'    => ['arrayEstado', 'arrayPersona'] Recibe el id de la persona 
     *                                                              y el estado de la persona empresa rol
     *                              'intStart'                  => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                  => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getRolesPersonaPunto($arrayParametros)
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
                             . "ar.usrCreacion strUsrCreacion, "
                             . "ar.usrUltMod strUsrUltMod, "
                             . "ar.feCreacion dateFeCreacion, "
                             . "ar.feUltMod dateFeUtlMod, "
                             . "iper.id intIdPersonaEmpresaRol, "
                             . "iper.usrCreacion strUsrCreacionIPER, "
                             . "iper.feCreacion dateFeCreacionIPER, "
                             . "iper.estado strEstadoIPER, "
                             . "ieg.id intIdEmpresa, "
                             . "ieg.prefijo strPrefijo ";

            $strFromQuery = "FROM schemaBundle:InfoPersonaEmpresaRol iper, ";
            $strInfoContacto = ' schemaBundle:InfoPersonaContacto ipc, ';
            if('PUNTO' === $arrayParametros['strTipoConsulta'])
            {
                $strInfoContacto = ' schemaBundle:InfoPuntoContacto ipc, ';
            }
            $strFromQuery       .= $strInfoContacto 
                                ." schemaBundle:InfoEmpresaRol ier, "
                                . " schemaBundle:InfoEmpresaGrupo ieg, "
                                . " schemaBundle:AdmiRol ar "
                                . " WHERE ier.id       = iper.empresaRolId "
                                . " AND ier.empresaCod = ieg.id "
                                . " AND ar.id          = ier.rolId ";
            $strInfoContactoWhere = ' AND ipc.personaRolId = iper.id ';
            if('PUNTO' === $arrayParametros['strTipoConsulta'])
            {
                $strInfoContactoWhere = ' AND ipc.personaEmpresaRolId = iper.id ';
            }
            
            $strFromQuery       .= $strInfoContactoWhere; 

            //Pregunta si $arrayParametros['arrayEmpresaGrupo']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayEmpresaGrupo']['arrayEmpresa']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ieg.prefijo ';
                $arrayParams['strBindParam']    = ':arrayEmpresaGrupo';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaGrupo']['arrayEmpresa'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEmpresaGrupo', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEmpresaGrupo', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaContacto']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPC';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaContacto']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPC', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPC', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaContacto']['arrayPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.contactoId ';
                $arrayParams['strBindParam']    = ':arrayPersona';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaContacto']['arrayPersona'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaContacto']['arrayPersonaEmpresaRol']) &&
               'PUNTO' !== $arrayParametros['strTipoConsulta'])
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.personaEmpresaRolId ';
                $arrayParams['strBindParam']    = ':arrayPersonaEmpresaRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaContacto']['arrayPersonaEmpresaRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersonaEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersonaEmpresaRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }
            
            //Pregunta si $arrayParametros['arrayRol']['arrayRol'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaContacto']['arrayPunto']) &&
               'PUNTO' === $arrayParametros['strTipoConsulta'])
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.puntoId ';
                $arrayParams['strBindParam']    = ':arrayPunto';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaContacto']['arrayPunto'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPunto', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPunto', $objReturnResponse->putTypeParamBind($arrayParams));
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
            $objReturnResponse->setStrMessageStatus('Existion un error en getResultadoRolesPersona - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getRolesPersonaPunto

    /**
     * getJSONContactoClienteByTipoRol, retorna la informacion en formato json obtenida en el metodo getContactosDeClientePunto
     * 
     * @param array $arrayParametros[
     *                              strDescripcionTipoRol   =>  Recibe la descripcion del tipo de rol
     *                              strEstadoTipoRol        =>  Recibe el estado de la descripcion del tipo de rol
     *                              strDescripcionRol       =>  Recibe la descripcion del rol
     *                              strEstadoRol            =>  Recibe el estado del rol
     *                              strEmpresaCod           =>  Recibe el codigo de la empresa
     *                              strEstadoIER            =>  Recibe el estado de empresa rol
     *                              strEstadoIPER           =>  Recibe el estado de la info persona empresa rol
     *                              strEstadoIPC            =>  Recibe el estado de la info persona contacto
     *                              intIdPersonaEmpresaRol  =>  Recibe el id de la persona empresa rol
     *                              strNombrePersona        =>  Recibe el nombre de la persona
     *                              strFechaIncio           =>  Recibe la fecha inicio de creacion
     *                              strFechaFin             =>  Recibe la fecha fin de creacion
     *                              intStart                =>  Recibe el inicio para el resultado de la busqueda.
     *                              intLimit                =>  Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * 
     * @return json $jsonData Retorna la informacion en formato json.   
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     */
    function getJSONContactoClienteByTipoRol($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        try
        {
            $objGetResult   = $this->getContactosDeClientePunto($arrayParametros);
            $jsonData       = json_encode(array('strStatus'         => $objReturnResponse::PROCESS_SUCCESS,
                                                'strMessageStatus'  => $objReturnResponse::MSN_PROCESS_SUCCESS,
                                                'total'             => $objGetResult->getTotal(),
                                                'encontrados'       => $objGetResult->getRegistros()));
        }
        catch(\Exception $ex)
        {
            $jsonData = json_encode(array('strStatus' => $objReturnResponse::ERROR, 'strMessageStatus' => $objReturnResponse::MSN_ERROR .
                                          ' ' . $ex->getMessage()));
        }
        return $jsonData;
    } //getJSONContactoClienteByTipoRol

    /**
     * getResultadoInfoPersonaRelacionadasAlContacto, obtiene las persona relacionadas al contacto.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              'arrayPersonaContacto'   => ['arrayEstado', 'arrayPersonaContactoId'] Recibe el estado y el id del contacto.
     *                              'arrayPersonaEmpresaRol' => ['arrayEstado'] Recible el estado de la persona empresa rol.
     *                              'arrayPersona'           => ['arrayEstado'] Recibe el estado de la persona.
     *                              'intStart'               => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'               => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getResultadoInfoPersonaRelacionadasAlContacto($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT COUNT(ipr.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT ipr.id intIdPersona, "
                      . "COALESCE(ipr.razonSocial, ipr.nombres, ipr.representanteLegal) strNombres, "
                      . "ipr.apellidos strApellidos, "
                      . "ipr.identificacionCliente strIdentificacionCliente, "
                      . "ipr.usrCreacion strUsrCreacion, "
                      . "ipr.feCreacion strFeCreacion, "
                      . "iper.estado strEstado, "
                      . "at.id intIdTitulo, "
                      . "at.descripcionTitulo strDescripcionTitulo ";

            $strFromQuery = "FROM schemaBundle:InfoPersonaContacto ipc, "
                                . " schemaBundle:InfoPersonaEmpresaRol iper, "
                                . " schemaBundle:InfoPersona ipr "
                                . " LEFT JOIN schemaBundle:AdmiTitulo at WITH ipr.tituloId = at.id "
                                . " WHERE ipr.id  = iper.personaId "
                                . " AND iper.id   = ipc.personaEmpresaRolId ";

            //Pregunta si $arrayParametros['arrayPersonaContacto']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaContacto']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPC';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaContacto']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPC', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPC', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaEmpresaRol']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaEmpresaRol']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iper.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPER';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaEmpresaRol']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPER', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersona']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersona']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipr.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPR';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPR', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPR', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaContacto']['arrayPersonaContactoId'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaContacto']['arrayPersonaContactoId']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipc.contactoId ';
                $arrayParams['strBindParam']    = ':arrayPersonaContactoId';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaContacto']['arrayPersonaContactoId'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersonaContactoId', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersonaContactoId', $objReturnResponse->putTypeParamBind($arrayParams));
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
            $objReturnResponse->setStrMessageStatus('Existion un error en getResultadoInfoPersonaRelacionadasAlContacto - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getResultadoInfoPersonaRelacionadasAlContacto
    
    /**
     * Funcion que sirve para obtener el mail de un contacto tecnico del cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 14-06-2016
     * @param $arrayParametros [idPersonaEmpresaRol, estado, strDescFormaContacto, strContactoTecnico, idEmpresa]
     */
    public function getValorFormaContactoTecnico($arrayParametros)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql = "SELECT IPFC.VALOR as FORMA_CONTACTO
                FROM INFO_PERSONA_EMPRESA_ROL IPER,
                  INFO_EMPRESA_ROL IER,
                  ADMI_ROL AR,
                  INFO_PERSONA_FORMA_CONTACTO IPFC,
                  ADMI_FORMA_CONTACTO AFC,
                  INFO_PERSONA_CONTACTO IPC
                WHERE IPC.PERSONA_EMPRESA_ROL_ID   = :idPersonaEmpresaRol
                AND IPC.ESTADO                     = :estado
                AND IPC.CONTACTO_ID                = IPFC.PERSONA_ID
                AND IPFC.ESTADO                    = :estado
                AND IPFC.FORMA_CONTACTO_ID         = AFC.ID_FORMA_CONTACTO
                AND AFC.DESCRIPCION_FORMA_CONTACTO = :strDescFormaContacto
                AND IPFC.PERSONA_ID                = IPER.PERSONA_ID
                AND IPER.ESTADO                    = :estado
                AND IPER.EMPRESA_ROL_ID            = IER.ID_EMPRESA_ROL
                AND IER.EMPRESA_COD                = :idEmpresa
                AND IER.ESTADO                     = :estado
                AND IER.ROL_ID                     = AR.ID_ROL
                AND AR.DESCRIPCION_ROL             = :strContactoTecnico
                AND AR.ESTADO                      = :estado
                AND ROWNUM < 2";

        $query->setParameter("estado",               $arrayParametros['estado']);
        $query->setParameter("idEmpresa",            $arrayParametros['idEmpresa']);
        $query->setParameter("idPersonaEmpresaRol",  $arrayParametros['idPersonaEmpresaRol']);
        $query->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']);
        $query->setParameter("strContactoTecnico",   $arrayParametros['strContactoTecnico']);

        $rsm->addScalarResult('FORMA_CONTACTO', 'formaContacto', 'string');
        
        $query->setSQL($sql);
        $datos = $query->getOneOrNullResult();

        return $datos;
    }
    
 /**
     * getCantidadPorTipoContactoPorCliente
     * 
     * Funcion que retorna cantidad de Contactos por Cliente y por Tipo de Contacto
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 23-06-2016     
     * @param integer $intIdPerRol      
     * @param string $strNombreRolContacto  
     * @return integer
     * 
     */
    public function getCantidadPorTipoContactoPorCliente($intIdPerRol,$strNombreRolContacto)
    {            
        $query = $this->_em->createQuery("SELECT  count(perContacto) from                                                                
                               
                                schemaBundle:InfoPersonaContacto perContacto,
                                schemaBundle:InfoPersona contacto,                               
                                schemaBundle:InfoPersonaEmpresaRol prolContacto,
                                schemaBundle:InfoEmpresaRol emprolContacto,
                                schemaBundle:AdmiRol rolContacto,
                                schemaBundle:AdmiTipoRol trolContacto

                                where perContacto.contactoId=contacto.id
                                and perContacto.personaRolId=prolContacto.id
                                and prolContacto.empresaRolId=emprolContacto.id
                                and emprolContacto.rolId=rolContacto.id
                                and rolContacto.tipoRolId=trolContacto.id
                                and trolContacto.descripcionTipoRol =:strContacto                               
                                and rolContacto.descripcionRol =:strNombreRolContacto
                                and perContacto.personaEmpresaRolId=:intIdPerRol
                                and prolContacto.estado IN (:arrayEstado)
                                and perContacto.estado =:strEstado
        ");
               
        $query->setParameters(array('arrayEstado'          => array('Activo','Inactivo'),
                                    'strEstado'            => 'Activo',
                                    'strContacto'          => 'Contacto',
                                    'strNombreRolContacto' => $strNombreRolContacto,
                                    'intIdPerRol'          => $intIdPerRol
                                               
                                              ));                
        $intCantidadContactos = $query->getSingleScalarResult();
        if(!$intCantidadContactos)
        {
            $intCantidadContactos = 0;
        }
        return $intCantidadContactos;   
    }   

     /**
     * findByPersonaEmpresaRolIdYEstado
     * 
     * Funcion retorna Contactos por Cliente y por Estado
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 28-06-2016     
     * @param integer $intIdPerRol      
     * @param string  $strEstado  
     * @return datos     
     * 
     */
    public function findByPersonaEmpresaRolIdYEstado($intIdPerRol,$strEstado)
    {            
        $query = $this->_em->createQuery("SELECT perContacto from                                                                
                               
                                schemaBundle:InfoPersonaContacto perContacto,
                                schemaBundle:InfoPersona contacto
                                
                                where perContacto.contactoId=contacto.id                                
                                and perContacto.personaEmpresaRolId=:intIdPerRol                                
                                and perContacto.estado =:strEstado");
               
        $query->setParameters(array('strEstado'            => $strEstado,
                                    'intIdPerRol'          => $intIdPerRol
                                               
                                              ));                
        $datos = $query->getResult();       
        return $datos;   
    } 
    
    /**
     * Funcion que sirve para obtener los contactos a nivel de punto.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 09-05-2018
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 18-10-2018 Se agrega filtro por estado del contacto Activo.
     * @param $arrayParametros [strPrefijoEmpresa, strEstado, intIdPunto]
     */
    public function getContactosPuntoRol($arrayParametros)
    {       
        $arrayRespuesta  = array();
     
        try
        {
            $objRsm          = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSqlContactos = " SELECT PERS.ID_PERSONA,
                                        CONCAT(PERS.NOMBRES,CONCAT(' ',PERS.APELLIDOS)) AS NOMBRES,
                                        ROL.DESCRIPCION_ROL
                                FROM DB_COMERCIAL.INFO_PERSONA PERS,
                                     DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PEMPROL,
                                     DB_COMERCIAL.INFO_EMPRESA_ROL EMPROL,
                                     DB_COMERCIAL.INFO_EMPRESA_GRUPO EMPGR,
                                     DB_GENERAL.ADMI_ROL ROL,
                                     DB_GENERAL.ADMI_TIPO_ROL TROL,
                                     DB_COMERCIAL.INFO_PUNTO_CONTACTO PTOC,
                                     DB_COMERCIAL.INFO_PUNTO PTO
                                WHERE 
                                     PERS.ID_PERSONA        = PEMPROL.PERSONA_ID
                                AND  PEMPROL.EMPRESA_ROL_ID = EMPROL.ID_EMPRESA_ROL
                                AND  EMPROL.EMPRESA_COD     = EMPGR.COD_EMPRESA
                                AND  EMPROL.ROL_ID          = ROL.ID_ROL
                                AND  ROL.TIPO_ROL_ID        = TROL.ID_TIPO_ROL
                                AND  PTOC.CONTACTO_ID       = PERS.ID_PERSONA
                                AND  PEMPROL.ID_PERSONA_ROL = PTOC.PERSONA_EMPRESA_ROL_ID
                                AND  PTOC.PUNTO_ID          = PTO.ID_PUNTO
                                AND  EMPGR.PREFIJO          = :strPrefijoEmpresa                          
                                AND  PTOC.ESTADO            = :strEstadoPunto
                                AND  PTO.ID_PUNTO           = :intIdPunto
                                AND  PERS.estado            = :strEstadoContacto
                                AND  ROWNUM                <= :intNumContactos
                                ORDER BY ROL.DESCRIPCION_ROL
                                " ;    
            
            
            $objRsm->addScalarResult('ID_PERSONA', 'idPersona','integer');
            $objRsm->addScalarResult('NOMBRES', 'nombres','string');           
            $objRsm->addScalarResult('DESCRIPCION_ROL', 'descripcionRol','string');           
            

            $objNtvQuery->setParameter("strPrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
            $objNtvQuery->setParameter("strEstadoPunto", $arrayParametros['strEstado']);
            $objNtvQuery->setParameter("intIdPunto",  $arrayParametros['intIdPunto']);
            $objNtvQuery->setParameter("strEstadoContacto",  $arrayParametros['strEstado']);
            $objNtvQuery->setParameter("intNumContactos",  $arrayParametros['intMaxContactos']);

            $objNtvQuery->setSQL($strSqlContactos);
           
            $arrayRespuesta = $objNtvQuery->getResult();  
            
        } catch (Exception $ex) 
        {
            error_log($ex->getMessage());
        }

        return $arrayRespuesta;
    }
    
    
    
    /**
     * Funcion que sirve para obtener los contactos a nivel de punto.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 09-05-2018
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 18-10-2018 Se agrega filtro por estado del contacto Activo.
     * @param $arrayParametros [strPrefijoEmpresa, strEstado, intIdPunto]
     */
    public function getContactosPersonaRol($arrayParametros)
    {       
        $arrayRespuesta  = array(); 
        
        try
        {
            $objRsm          = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);            
            
            $strSqlContactos=" SELECT
                                 contacto.ID_PERSONA,
                                 CONCAT(contacto.NOMBRES,CONCAT(' ',contacto.APELLIDOS)) AS NOMBRES,
                                 rolContacto.DESCRIPCION_ROL
                              FROM        
                                DB_COMERCIAL.INFO_PERSONA_CONTACTO    PerContacto,                               
                                DB_COMERCIAL.INFO_PERSONA             contacto,                                
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL prolContacto,
                                DB_COMERCIAL.INFO_EMPRESA_ROL         emprolContacto,
                                DB_GENERAL.ADMI_ROL                   rolContacto,
                                DB_GENERAL.ADMI_TIPO_ROL              trolContacto,
                                DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL cliente,
                                DB_COMERCIAL.INFO_PERSONA             persona                                
                             WHERE 
                                 PerContacto.CONTACTO_ID            = contacto.ID_PERSONA
                             AND PerContacto.PERSONA_ROL_ID         = prolContacto.ID_PERSONA_ROL
                             AND prolContacto.EMPRESA_ROL_ID        = emprolContacto.ID_EMPRESA_ROL
                             AND emprolContacto.ROL_ID              = rolContacto.ID_ROL
                             AND rolContacto.TIPO_ROL_ID            = trolContacto.ID_TIPO_ROL                                                                                        
                             AND PerContacto.PERSONA_EMPRESA_ROL_ID = cliente.ID_PERSONA_ROL
                             AND cliente.PERSONA_ID                 = persona.ID_PERSONA
                             AND cliente.ID_PERSONA_ROL             = :intIdPersonaRol
                             AND contacto.ESTADO                    = :strEstadoContacto
                             AND PerContacto.estado                 = :strEstadoPersonaContacto
                             AND  ROWNUM                           <= :intNumContactos
                             ORDER BY rolContacto.DESCRIPCION_ROL ";  
            
            $objRsm->addScalarResult('ID_PERSONA', 'idPersona','integer');
            $objRsm->addScalarResult('NOMBRES', 'nombres','string');           
            $objRsm->addScalarResult('DESCRIPCION_ROL', 'descripcionRol','string');                

            $objNtvQuery->setParameter("strPrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
            $objNtvQuery->setParameter("strEstadoContacto", $arrayParametros['strEstado']);
            $objNtvQuery->setParameter("strEstadoPersonaContacto", $arrayParametros['strEstado']);
            $objNtvQuery->setParameter("intIdPersonaRol",  $arrayParametros['intIdPersonaRol']);
            $objNtvQuery->setParameter("intNumContactos",  $arrayParametros['intMaxContactos']);

            $objNtvQuery->setSQL($strSqlContactos);
            
            $arrayRespuesta = $objNtvQuery->getResult();
            
        } catch (Exception $ex) 
        {
            error_log($ex->getMessage());
        }

        return $arrayRespuesta;
    }
    
    /**
     * getEmailContactoCliente
     * Función que sirve para obtener el mail de los contactos técnicos del cliente
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 15-03-2019
     * 
     * @param array $arrayParametros [
     *           intIdPersonaEmpresaRol  - Id Persona empresa rol del cliente a consultar
     *           strEstado               - Estado del cliente en la info persona
     *           strDescFormaContacto    - Descripción para la forma de contacto    
     *           strContactoTecnico      - Descripcion para el contacto tecnico
     *           intIdEmpresa            - Id empresa donde esta el cliente ]
     * 
     * @return array $arrayResultado[
     *   strFormaContacto - Estado de la Incidencia
     *   strDescripcion   - Descripción de la forma de contacto de la persona
     * ]
     * 
     * Costo Query: 15
     */
    public function getEmailContactoCliente($arrayParametros)
    {       
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = " SELECT MAX(IPFC.VALOR) as FORMA_CONTACTO, IPER2.PERSONA_ID,AFC.DESCRIPCION_FORMA_CONTACTO
                FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                INNER JOIN DB_COMERCIAL.INFO_PERSONA_CONTACTO IPC ON IPC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL 
                INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.PERSONA_ID = IPC.CONTACTO_ID
                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPER2.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL 
                INNER JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL
                INNER JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC ON IPFC.PERSONA_ID = IPER2.PERSONA_ID
                INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO            
                WHERE 
                IPER.ID_PERSONA_ROL        = :idPersonaEmpresaRol
                AND AFC.DESCRIPCION_FORMA_CONTACTO = :strDescFormaContacto
                AND IER.EMPRESA_COD                = :idEmpresa
                AND IER.ESTADO                     = :estado
                AND AR.DESCRIPCION_ROL             = :strContactoTecnico
                AND IPC.ESTADO                     = :estado
                AND AR.ESTADO                      = :estado
                AND IPER.ESTADO                    = :estado
                AND IPER2.ESTADO                   = :estado
                AND IPFC.ESTADO                    = :estado
                GROUP BY IPER2.PERSONA_ID,AFC.DESCRIPCION_FORMA_CONTACTO ";

        $objQuery->setParameter("estado",               $arrayParametros['strEstado']);
        $objQuery->setParameter("idEmpresa",            $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter("idPersonaEmpresaRol",  $arrayParametros['intIdPersonaEmpresaRol']);
        $objQuery->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']);
        $objQuery->setParameter("strContactoTecnico",   $arrayParametros['strContactoTecnico']);

        $objRsm->addScalarResult('FORMA_CONTACTO',             'strFormaContacto', 'string');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'strDescripcion','string');
        
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getResult();
        return $arrayDatos;
    }
    
    
    /**
     * getEmailClientePorPunto
     * Función que sirve para obtener el mail del punto
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 18-03-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 16-08-2019 - Se agrega los contacos técnicos del punto
     * @since 1.0
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.2 22-07-2020 - Se agrega un filtro para poder mostrar todos los contactos 
     *                           del punto y no solo los tecnicos
     * @since 1.1
     * 
     * @param array $arrayParametros [
     *          intIdPunto           - Id del punto
     *          strEstado            - Estado del cliente en la info persona
     *          strDescFormaContacto - Descripción para la forma de contacto  ]
     * 
     * @return array $arrayResultado[
     *   strFormaContacto - Estado de la Incidencia
     *   strDescripcion   - Descripción de la forma de contacto de la persona
     * ]  
     * 
     * Costo Query: 5
     * 
     */
    public function getEmailClientePorPunto($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strWhere = "";

        $strSql = " SELECT IPFC.VALOR as FORMA_CONTACTO,'Punto' AS DESCRIPCION_FORMA_CONTACTO
                    FROM DB_COMERCIAL.info_punto_contacto IPC
                    INNER JOIN DB_COMERCIAL.info_persona_forma_contacto IPFC on IPFC.PERSONA_ID = IPC.CONTACTO_ID
                    INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.PERSONA_ID = IPC.CONTACTO_ID
                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPER2.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                    INNER JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL
                    WHERE IPC.punto_id = :idPunto 
                    AND AFC.DESCRIPCION_FORMA_CONTACTO IN (:strDescFormaContacto)
                    AND IPC.estado=:estado ";
               
        $objQuery->setParameter("estado",               $arrayParametros['strEstado']);
        $objQuery->setParameter("idPunto",              $arrayParametros['intIdPunto']);
        $objQuery->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']);

        if(isset($arrayParametros['strContactoTecnico']) && !empty($arrayParametros['strContactoTecnico']))
        {
            $strWhere = " AND AR.DESCRIPCION_ROL = :strContactoTecnico ";
            $objQuery->setParameter("strContactoTecnico",   $arrayParametros['strContactoTecnico']);
        }

        if(isset($arrayParametros['strContactoTecnico']) && !empty($arrayParametros['strContactoTecnico']))
        {
            $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'strDescripcion',   'string');
        }
        $objRsm->addScalarResult('FORMA_CONTACTO',             'strFormaContacto', 'string');
        
        $strSql     = $strSql.$strWhere;
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getResult();

        if(empty($arrayDatos))
        {
            $strSql    = "  SELECT IPFC.VALOR AS FORMA_CONTACTO,'Punto' AS DESCRIPCION_FORMA_CONTACTO
                            FROM DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO  IPFC 
                            INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO 
                            INNER JOIN DB_COMERCIAL.INFO_PUNTO IPO ON IPO.ID_PUNTO = IPFC.PUNTO_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.ID_PERSONA_ROL = IPO.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPER2.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                            INNER JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL 
                            WHERE IPFC.PUNTO_ID = :idPunto AND IPFC.ESTADO = :estado
                            AND AFC.DESCRIPCION_FORMA_CONTACTO IN (:strDescFormaContacto) ";

            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();
    
        }

        return $arrayDatos;
    }
        
    /**
     * getEmailCliente
     * Función que sirve para obtener el mail del punto
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 18-03-2019
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 22-10-2020 - Se cambia a un arreglo la forma de contacto
     * @since 1.0
     * 
     * @param array $arrayParametros [
     *          intIdPersonaEmpresaRol  - Id de la persona empresa rol
     *          strEstado               - Estado del cliente en la info persona
     *          strDescFormaContacto    - Descripción para la forma de contacto  ]  
     *
     * @return array $arrayResultado[
     *   strFormaContacto - Estado de la Incidencia
     *   strDescripcion   - Descripción de la forma de contacto de la persona
     * ]   
     * 
     * Costo Query: 6
     */
    public function getEmailCliente($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql = " SELECT IPFC.VALOR as FORMA_CONTACTO,'Personal' AS DESCRIPCION_FORMA_CONTACTO
                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER 
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC ON IPFC.PERSONA_ID = IPER.PERSONA_ID
                    INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO
                    WHERE 
                    IPER.ID_PERSONA_ROL                 = :idPersonaEmpresaRol
                    AND AFC.DESCRIPCION_FORMA_CONTACTO  IN (:strDescFormaContacto)
                    AND IPER.ESTADO                     = :estado
                    AND IPFC.ESTADO                     = :estado ";

        $objQuery->setParameter("estado",               $arrayParametros['strEstado']);
        $objQuery->setParameter("idPersonaEmpresaRol",  $arrayParametros['intIdPersonaEmpresaRol']);
        $objQuery->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']);

        $objRsm->addScalarResult('FORMA_CONTACTO',             'strFormaContacto', 'string');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'strDescripcion','string');
        
        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getResult();

        return $arrayDatos;
    }
    
     /**
     * getContactosClientePorTipoRol
     * Función que sirve para obtener los contactos del cliente según los valores enviados como parámetros.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-06-2022
     * 
     * @author Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.1 09-11-2022  Se agrega sentencia distinc para evitar duplicidad de registros. 
     * 
     * 
     * @param array $arrayParametros [
     *           intIdPersonaEmpresaRol  - Id Persona empresa rol del cliente a consultar
     *           strEstado               - Estado del cliente en la info persona
     *           strDescFormaContacto    - Descripción para la forma de contacto    
     *           strTipoContacto         - Descripcion para el tipo de contacto (Comercial, Facturación, Cobranzas, etc)
     *           strEmpresaCod           - Id empresa donde esta el cliente ]
     * 
     * @return array $arrayResultado
     * 
     * Costo Query: 15
     */
    public function getContactosClientePorTipoRol($arrayParametros)
    {
        $arrayResultado = array();
        
        try
        {        
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = " SELECT DISTINCT(regexp_substr(IPFC.VALOR,'[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}')) VALOR 
                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_CONTACTO IPC ON IPC.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL 
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.PERSONA_ID = IPC.CONTACTO_ID
                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPER2.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL 
                    INNER JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC ON IPFC.PERSONA_ID = IPER2.PERSONA_ID
                    INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO            
                    WHERE 
                    IPER.ID_PERSONA_ROL                = :idPersonaEmpresaRol
                    AND AFC.DESCRIPCION_FORMA_CONTACTO = :strDescFormaContacto
                    AND IER.EMPRESA_COD                = :strEmpresaCod
                    AND IER.ESTADO                     = :estado
                    AND AR.DESCRIPCION_ROL             = :strTipoContacto
                    AND IPC.ESTADO                     = :estado
                    AND AR.ESTADO                      = :estado
                    AND IPER.ESTADO                    = :estado
                    AND IPER2.ESTADO                   = :estado
                    AND IPFC.ESTADO                    = :estado  ";

            $objRsm->addScalarResult('VALOR','valor','string');
            $objQuery->setParameter("estado",               $arrayParametros['strEstado']);
            $objQuery->setParameter("strEmpresaCod",        $arrayParametros['strEmpresaCod']);
            $objQuery->setParameter("idPersonaEmpresaRol",  $arrayParametros['intIdPersonaEmpresaRol']);
            $objQuery->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']);
            $objQuery->setParameter("strTipoContacto",   $arrayParametros['strTipoContacto']);

            $objQuery->setSQL($strSql);
            $arrayResultado = $objQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }        
        return $arrayResultado;
    }    
    

     
    /**
     * getEmailComercialCD
     * Función que sirve para obtener el mail del punto
     * si es juridico entrega mail de representante natural
     * 
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 23-09-2022 
     * @param array $arrayParametros [
     *          intIdPunto           - Id del punto
     *          strEstado            - Estado del cliente en la info persona
     *          strDescFormaContacto - Descripción para la forma de contacto  ]
     * 
     * @return array $arrayResultado[
     * 
     *   strFormaContacto - Estado de la Incidencia
     *   strDescripcion   - Descripción de la forma de contacto de la persona
     * ]   
     */
    public function getEmailComercialCD($arrayParametros)
    {
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm); 

        $strSql =  " SELECT  
        per.ID_PERSONA
        FROM DB_COMERCIAL.INFO_PUNTO clip
        INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper ON iper.ID_PERSONA_ROL = clip.PERSONA_EMPRESA_ROL_ID 
        INNER JOIN DB_COMERCIAL.INFO_PERSONA per ON  per .ID_PERSONA = iper.PERSONA_ID
        WHERE clip.ID_PUNTO =   :idPunto 
        AND   per.TIPO_TRIBUTARIO = 'JUR'
        AND   per.TIPO_IDENTIFICACION  = 'RUC' ";  

        $objQuery->setParameter("idPunto",  $arrayParametros['intIdPunto']); 
        $objRsm->addScalarResult('ID_PERSONA', 'intIdPersona', 'string');

        $objQuery->setSQL($strSql);
        $arrayDatos = $objQuery->getResult();

        if(!empty($arrayDatos))//si es persona juridica.
        {

            $intIdPersona =$arrayDatos[0]['intIdPersona']; 

            $strSql =  "    SELECT         
            REP_IP.ID_PERSONA
            FROM DB_COMERCIAL.INFO_PERSONA CLI_IP 
            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL CLI_IPER ON  CLI_IPER.PERSONA_ID  = CLI_IP.ID_PERSONA
            INNER JOIN DB_COMERCIAL.INFO_PERSONA_REPRESENTANTE CLI_IPR ON  CLI_IPR.PERSONA_EMPRESA_ROL_ID  =  CLI_IPER.ID_PERSONA_ROL
            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL REP_IPER ON  REP_IPER.ID_PERSONA_ROL =  CLI_IPR.REPRESENTANTE_EMPRESA_ROL_ID
            INNER JOIN DB_COMERCIAL.INFO_PERSONA   REP_IP ON REP_IP.ID_PERSONA = REP_IPER.PERSONA_ID 
            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL CLI_IER ON  CLI_IER.ID_EMPRESA_ROL  = CLI_IPER.EMPRESA_ROL_ID 
            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL REP_IER ON  REP_IER.ID_EMPRESA_ROL  = REP_IPER.EMPRESA_ROL_ID 
            INNER JOIN DB_COMERCIAL.ADMI_ROL  CLI_AROL ON  CLI_AROL.ID_ROL = CLI_IER.ROL_ID
            INNER JOIN DB_COMERCIAL.ADMI_ROL  REP_AROL ON  REP_AROL .ID_ROL = REP_IER.ROL_ID
            WHERE CLI_IPR.ESTADO  IN  ('Pendiente','Cancelado', 'Activo')            
            AND   CLI_AROL.DESCRIPCION_ROL IN ('Cliente', 'Pre-cliente')
            AND   REP_AROL.DESCRIPCION_ROL IN ('Representante Legal Juridico')
            AND   REP_IP.TIPO_TRIBUTARIO   = 'NAT'
            AND   CLI_IP.ID_PERSONA        = :idPersona
            "; 
            
            $objQuery->setParameter("idPersona",  $intIdPersona); 
            $objRsm->addScalarResult('ID_PERSONA', 'intIdPersona', 'string');
            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();

            if(!empty($arrayDatos))//si tiene representante legal natural
            {

                $intIdPersonaRepresent = $arrayDatos[0]['intIdPersona']; 

                $strSql =  "  SELECT 
                IPFC.VALOR  AS FORMA_CONTACTO,
                'representante' AS DESCRIPCION_FORMA_CONTACTO  
                FROM DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC 
                INNER JOIN  DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON  AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID
                WHERE IPFC.PERSONA_ID = :idPersona
                AND AFC.DESCRIPCION_FORMA_CONTACTO IN (:strDescFormaContacto)
                AND IPFC.estado= 'Activo'
                AND AFC.estado= 'Activo'";
                
                $objQuery->setParameter("idPersona",    $intIdPersonaRepresent);
                $objQuery->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']); 

                $objRsm->addScalarResult('FORMA_CONTACTO',             'strFormaContacto', 'string');
                $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'strDescripcion','string');

                $objQuery->setSQL($strSql);
                $arrayDatos = $objQuery->getResult();
         
            } 

        
        }
        else
        {

            $strSql = " SELECT IPFC.VALOR as FORMA_CONTACTO,'Punto' AS DESCRIPCION_FORMA_CONTACTO
            FROM DB_COMERCIAL.info_punto_contacto IPC
            INNER JOIN DB_COMERCIAL.info_persona_forma_contacto IPFC on IPFC.PERSONA_ID = IPC.CONTACTO_ID
            INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO
            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.PERSONA_ID = IPC.CONTACTO_ID
            INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPER2.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
            INNER JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL
            WHERE IPC.punto_id = :idPunto 
            AND AFC.DESCRIPCION_FORMA_CONTACTO IN (:strDescFormaContacto)
            AND IPC.estado=:estado ";

            $objQuery->setParameter("estado",               $arrayParametros['strEstado']);
            $objQuery->setParameter("idPunto",              $arrayParametros['intIdPunto']);
            $objQuery->setParameter("strDescFormaContacto", $arrayParametros['strDescFormaContacto']);

            if(isset($arrayParametros['strContactoTecnico']) && !empty($arrayParametros['strContactoTecnico']))
            {
            $strWhere = " AND AR.DESCRIPCION_ROL = :strContactoTecnico ";
            $objQuery->setParameter("strContactoTecnico",   $arrayParametros['strContactoTecnico']);
            }

            if(isset($arrayParametros['strContactoTecnico']) && !empty($arrayParametros['strContactoTecnico']))
            {
            $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'strDescripcion',   'string');
            }
            $objRsm->addScalarResult('FORMA_CONTACTO',             'strFormaContacto', 'string');

            $strSql     = $strSql.$strWhere;
            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();

            if(empty($arrayDatos))
            {
            $strSql    = "  SELECT IPFC.VALOR AS FORMA_CONTACTO,'Punto' AS DESCRIPCION_FORMA_CONTACTO
                    FROM DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO  IPFC 
                    INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON IPFC.FORMA_CONTACTO_ID = AFC.ID_FORMA_CONTACTO 
                    INNER JOIN DB_COMERCIAL.INFO_PUNTO IPO ON IPO.ID_PUNTO = IPFC.PUNTO_ID
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.ID_PERSONA_ROL = IPO.PERSONA_EMPRESA_ROL_ID
                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IPER2.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                    INNER JOIN DB_GENERAL.ADMI_ROL AR ON IER.ROL_ID = AR.ID_ROL 
                    WHERE IPFC.PUNTO_ID = :idPunto AND IPFC.ESTADO = :estado
                    AND AFC.DESCRIPCION_FORMA_CONTACTO IN (:strDescFormaContacto) ";

            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();

            } 

        }

        return $arrayDatos; 
    }

}
