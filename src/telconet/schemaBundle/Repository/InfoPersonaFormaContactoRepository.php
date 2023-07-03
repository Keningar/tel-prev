<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;

class InfoPersonaFormaContactoRepository extends EntityRepository
{
    public function findPorEstadoPorPersona($idPersona, $estado, $limit, $page, $start)
    {
        $query = $this->_em->createQuery("SELECT a
                FROM
                        schemaBundle:InfoPersonaFormaContacto a
                WHERE
                        a.personaId = :idPersona AND
                        a.valor is not null
                        AND a.estado = :estado");
        $query->setParameter('idPersona', $idPersona);
        $query->setParameter('estado', $estado);
        //$datos = $query->getResult(); // era redundante e innecesario por el count de mas abajo
        $total = count($query->getResult()); // TODO: usar count SQL
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    }  
        
	public function findContactosServicioPorEstadoPorPunto($idPunto,$estado,$limit,$page,$start){
		$query = $this->_em->createQuery("SELECT fc
		FROM 
				schemaBundle:AdmiFormaContacto fc,
				schemaBundle:InfoPersonaFormaContacto pfc,
				schemaBundle:InfoPersona p,
				schemaBundle:InfoPersonaContacto pc,
                schemaBundle:InfoContactoServicio cserv,
				schemaBundle:InfoServicio serv,
				schemaBundle:InfoPunto pto
		WHERE    
				fc.id=pfc.formaContactoId AND
				pfc.personaId=p.id AND
				p.id=pc.contactoId AND
				pc.id=cserv.personaContactoId AND
				cserv.servicioId=serv.id AND
				serv.puntoId=pto.id AND
                pto.id=$idPunto
                AND cserv.estado='$estado'");
                $total=count($query->getResult());
		$datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
                //echo $query->getSQL();die;
                $resultado['registros']=$datos;
                $resultado['total']=$total;
		return $resultado;
	}         
               
	public function getPersonaFormaContactoParaSession($idPersona, $FormaContactoId)
	{
            $query = $this->_em->createQuery(
                                'SELECT p
                                    FROM schemaBundle:InfoPersonaFormaContacto p
                                    WHERE p.personaId = :PersonaId 
                                    AND p.formaContactoId = :FormaContacto
                                    AND p.estado = :estado')
                                    ->setMaxResults(1)
                            ->setParameter('PersonaId',$idPersona)
                            ->setParameter('FormaContacto',$FormaContactoId)
                            ->setParameter('estado',"Activo");			
            $entity =  $query->getResult();
			
            $personaFormaContacto = array();
            if($entity && count($entity)>0)
			{
				$entity = $entity[0];
	            $personaFormaContacto['id'] = $entity->getId();
	            $personaFormaContacto['valor'] = $entity->getValor();
	            $personaFormaContacto['estado'] = $entity->getEstado();
			}
            return $personaFormaContacto;
	}
	
	       
	public function getFormasContactoParaSession($idPersona)
	{
		$query = $this->_em->createQuery(
                "	SELECT fc.id as idFormaContacto, fc.descripcionFormaContacto, p.id, p.valor, 
                        p.feCreacion, p.usrCreacion, p.usrUltMod, p.feUltMod
                        FROM schemaBundle:InfoPersonaFormaContacto p, schemaBundle:AdmiFormaContacto fc 
                        WHERE p.formaContactoId = fc.id
                        AND p.personaId = :PersonaId 
                        AND (lower(fc.descripcionFormaContacto) like lower('%telefono%') 
                        or lower(fc.descripcionFormaContacto) like lower('%correo%') )
                        AND lower(fc.estado) = lower('Activo')  
                        AND lower(p.estado) = lower('Activo')   
                ")
        ->setParameter('PersonaId',$idPersona);			
		$entities =  $query->getResult();
		
		$personaFormaContacto = array();
		if($entities && count($entities)>0)
		{
			foreach($entities as $entity)
			{
				if($entity["valor"] != "" && $entity["valor"])
				{
					$personaFormaContactoOne['idFormaContacto'] = $entity["idFormaContacto"];
					$personaFormaContactoOne['idPersonaFormaContacto'] = $entity["id"];
					$personaFormaContactoOne['formaContacto'] = $entity["descripcionFormaContacto"];
					$personaFormaContactoOne['valor'] = $entity["valor"];
					$personaFormaContactoOne['usrCreacion'] = $entity["usrCreacion"];
					$personaFormaContactoOne['feCreacion'] = date_format($entity['feCreacion'],"Y/m/d H:i:s");
                                        $personaFormaContactoOne['usrUltMod'] = $entity["usrUltMod"];
					$personaFormaContactoOne['feUltMod'] = !empty($entity['feUltMod'])? date_format($entity['feUltMod'],"Y/m/d H:i:s"):'';
					$personaFormaContacto[] = $personaFormaContactoOne;
				}
			}
		}
		return $personaFormaContacto;
	}
	
    /**
     * getStringFormasContactoParaSession
     *
     * Esta funcion obtiene las formas de contacto de acuerdo al id de la persona y a la descripción de la forma de contacto
     * @version 1.0 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 11-11-2016 Se modifica la función para que únicamente tome en cuenta el estado de la relación entre la persona y sus formas
     *                         de contacto y no se considere el estado de la forma de contacto.
     * 
     * 
     * @return string $strValoresFormasContacto
     *
     */
	public function getStringFormasContactoParaSession($intIdPersona, $strDescripcionFormaContacto)
	{
        $strValoresFormasContacto = "";
        try
        {
            $objQuery   = $this->_em->createQuery();
            $strQuery   = " SELECT pfc.valor 
                            FROM schemaBundle:InfoPersonaFormaContacto pfc,
                                 schemaBundle:AdmiFormaContacto fc 
                            WHERE pfc.formaContactoId = fc.id
                            AND pfc.personaId = :intIdPersona 
                            AND lower(fc.descripcionFormaContacto) like lower(:strDescripcionFormaContacto)  
                            AND lower(pfc.estado) = lower('Activo') ";
            $objQuery->setParameter('intIdPersona',$intIdPersona);
            $objQuery->setParameter('strDescripcionFormaContacto','%'.$strDescripcionFormaContacto.'%');
            $objQuery->setDQL($strQuery);
            
            $arrayPersonaFormasContacto             = array();
            $arrayResultadoPersonaFormasContacto    =  $objQuery->getResult();
            if($arrayResultadoPersonaFormasContacto && count($arrayResultadoPersonaFormasContacto)>0)
            {
                foreach($arrayResultadoPersonaFormasContacto as $arrayFormaContacto)
                {
                    if($arrayFormaContacto["valor"] != "" && $arrayFormaContacto["valor"])
                    {
                        $arrayPersonaFormasContacto[] = $arrayFormaContacto["valor"];
                    }
                }
                
                if($arrayPersonaFormasContacto && count($arrayPersonaFormasContacto)>0)
                {
                    $strImplodeValoresFormasContacto    = implode(", ", $arrayPersonaFormasContacto);
                    $strValoresFormasContacto           = "".$strImplodeValoresFormasContacto."";
                }
            }
        } 
        catch (\Exception $e) 
        {
            error_log($e->getMessage());
        }
        
        return $strValoresFormasContacto;
	}


     /**
     * Costo: 6
     * getNumerosAsignados
     *
     * Función que retorna los numeros asignados y que pasan por el consumo celular
     *
     * @param array arrayParametros [ intIdPersonaEmpresaRol  => id_persona_empresa_rol ]
     *
     * @return string strNumerosAsignados
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 12-11-2018
     */
    public function getNumerosAsignados($arrayParametros)
    {
        $strDetalleNombre = 'COLABORADOR';
        $strEstado        = 'Activo';

        $strSql = " SELECT LISTAGG(IE.NOMBRE_ELEMENTO,',') WITHIN GROUP (ORDER BY IE.ID_ELEMENTO) NUMEROS_ASIGNADOS
                                        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE,DB_INFRAESTRUCTURA.INFO_ELEMENTO IE
                                        WHERE IDE.ELEMENTO_ID = IE.ID_ELEMENTO
                                        AND IDE.DETALLE_VALOR = :paramDetalleValor
                                        AND IDE.DETALLE_NOMBRE = :paramDetalleNombre
                                        AND IDE.ESTADO = :paramEstado ";

        $objStmt = $this->_em->getConnection()->prepare($strSql);
        $objStmt->bindValue('paramDetalleValor',$arrayParametros["intIdPersonaEmpresaRol"]);
        $objStmt->bindValue('paramDetalleNombre',$strDetalleNombre);
        $objStmt->bindValue('paramEstado',$strEstado);
        $objStmt->execute();

        $intSolicitudId = $objStmt->fetchColumn();

        return $intSolicitudId;
    }

	public function findContactosByLoginAndFormaContacto($login,$formaContacto){
		if($formaContacto)
			$whereFormaContacto = " AND	lower(afc.descripcionFormaContacto) = lower('$formaContacto')";
		else
			$whereFormaContacto = "";
			
		$query = $this->_em->createQuery("SELECT afc.descripcionFormaContacto,
					   pfc.valor
		FROM 
				schemaBundle:InfoPunto pto,
				schemaBundle:InfoPersonaEmpresaRol iper,
				schemaBundle:InfoPersona p, 
				schemaBundle:InfoPersonaFormaContacto pfc,
				schemaBundle:AdmiFormaContacto afc   				
		WHERE    
				pto.personaEmpresaRolId = iper.id AND
				iper.personaId=p.id AND
				p.id = pfc.personaId
				AND afc.id = pfc.formaContactoId
				AND	lower(pto.login) = lower('$login')
				AND lower(pfc.estado) = lower('Activo')
				AND pfc.valor is not null
				$whereFormaContacto ");
                $total=count($query->getResult());
		$datos = $query->getResult();
		return $datos;
	}  
	
      /**
     * El metodo findContactosTelefonicosPorPunto
     * Encuentra y concatena con una coma los telefonos por punto y persona
     * @param   type $puntoId Recibe el id del punto
     * @return  string Retorna los numeros concatenados por una
     * 
     * @author  Allan Suarez  <arsuarez@telconet.ec>     
     * @version 1.0 05-05-2015 Version Inicial     
     * 
     * @author  Allan Suarez  <arsuarez@telconet.ec>     
     * @version 1.5 07-05-2015 (Se ajusta a native query que acepte el union que trae las formas de contacto sin que se repitan) 
     */
	public function findContactosTelefonicosPorPunto($puntoId)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        $sql = "
            (SELECT 
                d.VALOR
             FROM info_persona a,
                  INFO_PERSONA_EMPRESA_ROL b,
                  info_punto c,
                  info_persona_forma_contacto d
             WHERE 
                c.PERSONA_EMPRESA_ROL_ID       = b.ID_PERSONA_ROL
                AND b.PERSONA_ID               = a.ID_PERSONA
                AND d.PERSONA_ID               = a.ID_PERSONA
                AND d.FORMA_CONTACTO_ID NOT   IN (:formaContacto)
                AND c.ID_PUNTO                 = :punto
                AND d.ESTADO                   = :estado
             )
             UNION
            (SELECT 
                b.VALOR
            FROM info_punto c,
                INFO_PUNTO_FORMA_CONTACTO b
            WHERE 
                b.PUNTO_ID                   = c.ID_PUNTO
                AND b.FORMA_CONTACTO_ID NOT IN (:formaContacto)
                AND c.ID_PUNTO               = :punto
                AND b.estado                 = :estado
            )
            ";
        
        $rsm->addScalarResult('VALOR', 'valor', 'string');
        
        $query->setParameter('formaContacto', 5);
        $query->setParameter('punto', $puntoId);
        $query->setParameter('estado', 'Activo');     
        
        $query->setSQL($sql);
        
        $resultado = $query->getArrayResult();
        
        $contactos = '';
        
        foreach($resultado as $dato)
        {
            $contactos .= $dato['valor'] . ' , ';
        }
        
        if($contactos==!'')
        {
            $contactos = trim($contactos, ', ');//Elimino la ultima coma que queda suelta    
        }

        return $contactos;         
    }

    /**
     * El metodo findCorreosPorPunto
     * Encuentra y concatena con una coma los correos por punto
     * @param   type $puntoId Recibe el id del punto
     * @return  string Retorna los correso concatenados por una
     * @author  Allan Suarez  <arsuarez@telconet.ec>
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1
     * @since   1.0
     */
    public function findCorreosPorPunto($puntoId, $estado = 'Activo')
    {

        $query = $this->_em->createQuery(" 
                SELECT d.valor					 
                FROM 
                        schemaBundle:InfoPunto c,
                        schemaBundle:InfoPuntoFormaContacto d						
                WHERE    
                        d.puntoId = c.id
                        and d.estado = :estado
                        and d.formaContactoId = 5 and c.id = :puntoId 
				");
        $query->setParameter('puntoId', $puntoId);
        $query->setParameter('estado', $estado);

        $arrayInfoPuntoFrmCnt = $query->getResult();

        $query = $this->_em->createQuery("select d.valor 
                        FROM
                                schemaBundle:InfoPersona a,
                                schemaBundle:InfoPersonaEmpresaRol b,
                                schemaBundle:InfoPunto c,
                                schemaBundle:InfoPersonaFormaContacto d 
                        WHERE
                                c.personaEmpresaRolId = b.id and
                                b.personaId = a.id and
                                d.personaId = a.id
                                and d.estado = :estado
                                and d.formaContactoId = 5 and c.id = :puntoId ");
        $query->setParameter('puntoId', $puntoId);
        $query->setParameter('estado', $estado);
        $arrayInfoPersFrmCnt = $query->getResult();

        $datos = array_merge($arrayInfoPuntoFrmCnt, $arrayInfoPersFrmCnt);

        $contactos = '';

        foreach($datos as $dato)
        {

            $contactos .= $dato['valor'] . ' , ';
        }

        return $contactos;
    }
    
    /**
      * getFormasContactoPorPersona
      *
      * Método que devuelve todas las formas de contacto por personaId enviado como parametro
      * 
      * @param $idPersona            
      *                                                                             
      * @return json con resultado
      * 
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.1 20-07-2016 - Se añade filtro de estado a la consulta de contactos
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 25-02-2015
      */       
    public function getFormasContactoPorPersona($idPersona)
    {
        $arr_encontrados = array();
        
        $query = $this->_em->createQuery();
        $dql ="
                SELECT 
                forma.descripcionFormaContacto,
                contacto.valor,
                contacto.estado                       
                FROM                         
                schemaBundle:InfoPersonaFormaContacto contacto,
                schemaBundle:AdmiFormaContacto forma                        
                WHERE    
                contacto.formaContactoId  =  forma.id and                                              
                contacto.personaId        =  :persona and
                contacto.estado           =  :estado
                ";
        
        $query->setParameter('persona', $idPersona); 
        $query->setParameter('estado', 'Activo');
        
        $query->setDQL($dql);            
              
        $datos = $query->getResult();                             
                
        if ($datos) 
        {                                       
            $total = count($datos);
            
            foreach ($datos as $data)
            {                                                               
                $arr_encontrados[]=array('descripcionFormaContacto'  => $data['descripcionFormaContacto'],
                                         'valor'                     => $data['valor'],
                                         'estado'                    => $data['estado']                                                              
                                        );            
            }

            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$total.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    /**
     * getResultadoPuntoPersonaFormaContacto, obtiene las formas contacto de las personas
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              'arrayEmpresaRol'           => ['arrayEstado', 'arrayEmpresa'] Recibe el estado y el id de la empresa rol
     *                              'arrayFormaContactoEstado'  => ['arrayEstado'] Recible el estado de la forma contacto
     *                              'arrayPersonaEmpresaRol'    => ['arrayEstado'] Recibe el estado de la persona empresa rol
     *                              'arrayPersonaPuntoFormCont' => ['arrayEstado', 'arrayPersona'] Recibe el estado y el id de la persona 
     *                                                             forma contacto
     *                              'intStart'                  => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                  => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getResultadoPuntoPersonaFormaContacto($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT DISTINCT count(afc.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT afc.id intIdFormaContacto, "
                      . " afc.descripcionFormaContacto strDescripcionFormaContacto, "
                      . " afc.estado strEstadoFormaContacto, "
                      . " ipfc.id intIdPersonaFormaContacto, "
                      . " ipfc.valor strValor, "
                      . " ipfc.estado strEstadoPersonaFormaContacto, "
                      . " ipfc.usrCreacion strUsrCreacion, "
                      . " ipfc.feCreacion strFeCreacion "
                      . "FROM schemaBundle:InfoPersonaFormaContacto ipfc, "
                      . " schemaBundle:AdmiFormaContacto afc "
                      . " WHERE afc.id        = ipfc.formaContactoId ";
            
            //Pregunta si $arrayParametros['arrayFormaContactoEstado']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayFormaContactoEstado']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' afc.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoAFC';
                $arrayParams['arrayValue']      = $arrayParametros['arrayFormaContactoEstado']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoAFC', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoAFC', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaPuntoFormCont']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoFormCont']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipfc.estado ';
                $arrayParams['strBindParam']    = ':arrayEstadoIPPFCE';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaPuntoFormCont']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPPFCE', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPPFCE', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersonaPuntoFormCont']['arrayPersona'] es diferente de vacío para agregar la condición.
            if(!$objReturnResponse->emptyArray($arrayParametros['arrayPersonaPuntoFormCont']['arrayPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipfc.personaId ';
                $arrayParams['strBindParam']    = ':arrayPersona';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersonaPuntoFormCont']['arrayPersona'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            
            $strFromQuery .= " GROUP BY afc.id, "
                          . " afc.descripcionFormaContacto, "
                          . " afc.estado, "
                          . " ipfc.id, "
                          . " ipfc.valor, "
                          . " ipfc.estado, "
                          . " ipfc.usrCreacion, "
                          . " ipfc.feCreacion "
                          . " ORDER BY afc.descripcionFormaContacto ASC,"
                          . " ipfc.valor ASC ";
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
            $objReturnResponse->setStrMessageStatus('Existion un error en getResultadoPuntoPersonaFormaContacto - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getResultadoPuntoPersonaFormaContacto
    
     /**
     * getResultadoFormasContactosPorPersona
     * 
     * Esta funcion ejecuta el Query que retorna los contactos de un cliente
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 29-12-2015 
     * 
     * @param integer  $intIdPersona
     * @param integer  $intIdEmpresa
     * 
     * @return array $arrayFormasContacto        Consulta de la BD
     * 
     */
     
    public function getResultadoFormasContactosPorPersona($intIdEmpresa,$intIdPersona)
    {
        $arrayFormasContacto = array();
     
        $objQuery       = $this->_em->createQuery();         
        $strCampos      = " SELECT contacto"; 
        
        $strFrom        = " FROM 
                                schemaBundle:InfoPersonaFormaContacto contacto,
                                schemaBundle:AdmiFormaContacto forma,
                                schemaBundle:InfoPersonaEmpresaRol perEmpRol,
                                schemaBundle:InfoEmpresaRol empRol
                            
                            WHERE 
                                contacto.formaContactoId  =  forma.id
                            AND
                                contacto.personaId        =  :varPersona
                            AND
                                contacto.estado           =  :varEstado                                
                            AND
                                contacto.personaId        =  perEmpRol.personaId
                            AND
                                perEmpRol.empresaRolId    =  empRol.id
                            AND
                                empRol.empresaCod         = :varEmpresaCod                                
                           ";

        $strSelect     = $strCampos . $strFrom;        
        
        $objQuery->setParameter("varPersona", $intIdPersona);
        $objQuery->setParameter("varEmpresaCod", $intIdEmpresa);
        $objQuery->setParameter("varEstado", 'Activo');
        $objQuery->setDQL($strSelect); 
        $arrayFormasContacto = $objQuery->getResult();
        
        return $arrayFormasContacto;        
    }    
    
    
   /**
    * getValorFormaContactoPorCodigo
    * 
    * Función que retorna el valor de la forma de contacto según el código
    * 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 22-09-2015 
    * 
    * @param integer  $intPersonaId Id Persona
    * @param integer  $strCodFormaContacto Código forma de contacto
    * 
    * @return string $strValorFormaContacto
    * 
    */
    public function getValorFormaContactoPorCodigo($intPersonaId, $strCodFormaContacto)
    { 
        $strValorFormaContacto = "";
        $objQuery = $this->_em->createQuery("SELECT ipfc
                                             FROM   schemaBundle:InfoPersonaFormaContacto ipfc
                                             JOIN   schemaBundle:AdmiFormaContacto afc with ipfc.formaContactoId = afc.id
                                             WHERE  ipfc.personaId = :personaId 
                                             AND    afc.codigo     = :codFormaContacto
                                             AND    ipfc.estado    = :estado"
                                           );

        $objQuery->setParameter('personaId',$intPersonaId);
        $objQuery->setParameter('codFormaContacto',$strCodFormaContacto);
        $objQuery->setParameter('estado','Activo');
        $objQuery->setMaxResults(1);
        $arrayDatos = $objQuery->getResult();
        
        if($arrayDatos && count($arrayDatos)>0) 
        {
            if(is_object($arrayDatos[0]))
            {
                $strValorFormaContacto = $arrayDatos[0]->getValor(); 
            }
        }
            
        return $strValorFormaContacto;
    }
    
    /**
     * Documentación para el método 'getResultadoFormasContactoClienteByCriterios'.
     *
     * Método utilizado para obtener los contactos de un cliente de acuerdo a los parámetros enviados
     * costoQuery: 14
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-03-2017
     *
     * @param array $arrayParametros [  'strDescripcionFormaContacto'       => descripcion de forma de contacto
     *                                  'intIdPersonaEmpresaRol'            => id de la persona empresa rol,
     *                                  'strLeftJoinFormasContactoCliente'  => 'SI' cuando se quiere relacionar con las formas de contacto del
     *                                                                         cliente ya seleccionadas
     *                                  'strInnerJoinFormasContactoCliente' => 'SI' cuando se quiere relacionar sólo con las formas de contacto del
     *                                                                         cliente ya seleccionadas
     *                               ]
     * @return array $arrayRespuesta
     */
    public function getResultadoFormasContactoClienteByCriterios($arrayParametros)
    {
        $arrayRespuesta                     = array();
        $arrayRespuesta['intTotal']         = 0;
        $arrayRespuesta['arrayResultado']   = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);
            
            $strSelect          = " SELECT IPFC.ID_PERSONA_FORMA_CONTACTO, IPERSONA.ID_PERSONA,
                                    COALESCE(IPERSONA.RAZON_SOCIAL, IPERSONA.NOMBRES, IPERSONA.REPRESENTANTE_LEGAL) AS NOMBRE_PERSONA,
                                    IPERSONA.IDENTIFICACION_CLIENTE,
                                    IPER.ID_PERSONA_ROL,
                                    IPFC.VALOR ";

            $strSelectCount     = " SELECT COUNT(IPER.ID_PERSONA_ROL) AS TOTAL ";
            $strFrom            = " FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER 
                                      INNER JOIN DB_COMERCIAL.INFO_PERSONA IPERSONA ON IPER.PERSONA_ID = IPERSONA.ID_PERSONA
                                      INNER JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC ON IPERSONA.ID_PERSONA = IPFC.PERSONA_ID 
                                      INNER JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC 
                                        ON AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID ";
            
            $strOrderBy         = " ORDER BY IPFC.VALOR ASC ";

            $objRsm->addScalarResult('ID_PERSONA', 'intIdPersona', 'integer');
            $objRsm->addScalarResult('NOMBRE_PERSONA', 'strNombrePersona', 'string');
            $objRsm->addScalarResult('IDENTIFICACION_CLIENTE', 'strIdentificacionCliente', 'string');
            $objRsm->addScalarResult('ID_PERSONA_ROL', 'intIdPersonaEmpresaRol', 'integer');
            $objRsm->addScalarResult('ID_PERSONA_FORMA_CONTACTO', 'intIdPersonaFormaContacto', 'integer');
            $objRsm->addScalarResult('VALOR', 'strValorFormaContacto', 'string');
            
            $objRsm->addScalarResult('TOTAL', 'intTotal', 'integer');
            
            $strWhere           = " WHERE IPER.ESTADO = :strEstadoActivo ";
            $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
            
            if( isset($arrayParametros['strLeftJoinFormasContactoCliente']) && !empty($arrayParametros['strLeftJoinFormasContactoCliente'])
                    && ($arrayParametros['strLeftJoinFormasContactoCliente']=="SI"))
            {
                $strSelect  .= ",IPERC.ID_PERSONA_EMPRESA_ROL_CARACT,
                                           CASE WHEN IPERC.ID_PERSONA_EMPRESA_ROL_CARACT IS NOT NULL THEN 1 ELSE 0 END AS FORMA_CONTACTO_SELECC ";

                $strFrom    .= " LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC 
                                   ON ( COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = IPFC.ID_PERSONA_FORMA_CONTACTO
                                        AND IPERC.ESTADO = :strEstadoActivo) ";
                $objRsm->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT', 'intIdPerCaractCorreo', 'integer');
                $objRsm->addScalarResult('FORMA_CONTACTO_SELECC', 'boolFormaContactoSeleccionado', 'boolean');
            }
            
            if( isset($arrayParametros['strInnerJoinFormasContactoCliente']) && !empty($arrayParametros['strInnerJoinFormasContactoCliente'])
                    && ($arrayParametros['strInnerJoinFormasContactoCliente']=="SI"))
            {
                $strSelect  .= ",IPERC.ID_PERSONA_EMPRESA_ROL_CARACT ";

                $strFrom    .= " INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC 
                                   ON COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = IPFC.ID_PERSONA_FORMA_CONTACTO ";
                $strWhere  .= " AND IPERC.ESTADO = :strEstadoActivo ";
                
                $objRsm->addScalarResult('ID_PERSONA_EMPRESA_ROL_CARACT', 'intIdPerCaractCorreo', 'integer');
            }
            
            
            if( isset($arrayParametros['strDescripcionFormaContacto']) && !empty($arrayParametros['strDescripcionFormaContacto']))
            {
                $strWhere  .= " AND IPFC.ESTADO = :strEstadoActivo ";
                $strWhere  .= " AND AFC.DESCRIPCION_FORMA_CONTACTO = :strDescripcionFormaContacto ";
                $objNtvQuery->setParameter('strDescripcionFormaContacto', $arrayParametros['strDescripcionFormaContacto']);
                
            }
            
            if( isset($arrayParametros['intIdPersonaEmpresaRol']) && !empty($arrayParametros['intIdPersonaEmpresaRol']) )
            {
                $strWhere .= " AND IPER.ID_PERSONA_ROL = :intIdPersonaEmpresaRol";
                $objNtvQuery->setParameter('intIdPersonaEmpresaRol', $arrayParametros['intIdPersonaEmpresaRol']);
            }

            $strQuery       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objNtvQuery->setSQL($strQuery);
            $arrayResultado = $objNtvQuery->getResult();
            
            $strQueryCount  = $strSelectCount.$strFrom.$strWhere;
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
     * getJSONFormasContactoClienteByCriterios
     * 
     * Método utilizado para obtener el json con los contactos de un cliente de acuerdo a los parámetros enviados
     *  
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-01-2017
     * 
     * @param array $arrayParametros [  'strDescripcionFormaContacto'       => descripcion de forma de contacto
     *                                  'intIdPersonaEmpresaRol'            => id de la persona empresa rol,
     *                                  'strLeftJoinFormasContactoCliente'  => 'SI' cuando se quiere relacionar con las formas de contacto del
     *                                                                         cliente ya seleccionadas
     *                                  'strInnerJoinFormasContactoCliente' => 'SI' cuando se quiere relacionar sólo con las formas de contacto del
     *                                                                         cliente ya seleccionadas 
     *                                  'boolTienePortalActivo'             => bool para validar si un cliente ya posee acceso al portal
     *                                  'strCorreoUsuario'                  => correo que sirve como usuario en el portal
     *                               ]
     * @return string $strJsonData
     */
    public function getJSONFormasContactoClienteByCriterios($arrayParametros)
    {  
        $arrayRespuestaFormasContacto   = $this->getResultadoFormasContactoClienteByCriterios($arrayParametros);
        $arrayResultadoFormasContacto   = $arrayRespuestaFormasContacto['arrayResultado'];
        $intTotalFormasContactos        = $arrayRespuestaFormasContacto['intTotal'];
        $arrayFormasContactoEncontradas = array();
        if($intTotalFormasContactos>0)
        {
            foreach($arrayResultadoFormasContacto as $arrayDataFc)
            {
                $boolEsCorreoUsuarioSeleccionado = false;
                if(isset($arrayParametros["boolTienePortalActivo"]) && !empty($arrayParametros["boolTienePortalActivo"])
                    && isset($arrayParametros["strCorreoUsuario"]) && !empty($arrayParametros["strCorreoUsuario"]) 
                        && $arrayParametros["boolTienePortalActivo"] && $arrayParametros["strCorreoUsuario"]==$arrayDataFc["strValorFormaContacto"])
                {
                    $boolEsCorreoUsuarioSeleccionado = true;
                }
                $arrayFormasContactoEncontradas[]   = array('intIdPersonaFormaContacto'         => $arrayDataFc["intIdPersonaFormaContacto"],
                                                            'intIdPersona'                      => $arrayDataFc["intIdPersona"],
                                                            'strNombrePersona'                  => $arrayDataFc["strNombrePersona"],
                                                            'strIdentificacionCliente'          => $arrayDataFc["strIdentificacionCliente"],
                                                            'intIdPersonaEmpresaRol'            => $arrayDataFc["intIdPersonaEmpresaRol"],
                                                            'strValorFormaContacto'             => $arrayDataFc["strValorFormaContacto"],
                                                            'intIdPerCaractCorreo'              => $arrayDataFc["intIdPerCaractCorreo"],
                                                            'boolFormaContactoSeleccionado'     => $arrayDataFc["boolFormaContactoSeleccionado"],
                                                            'boolEsCorreoUsuarioSeleccionado'   => $boolEsCorreoUsuarioSeleccionado
                                                      );    
            }
        }
        

        $strJsonData = json_encode(array('intTotal' => $intTotalFormasContactos, 'arrayResultado' => $arrayFormasContactoEncontradas));
        return $strJsonData;
    }
	
	/**
	 * getJSONContactosClienteyPuntoPorCodigoContacto
	 *
	 * Método utilizado para obtener el json con los contactos de un cliente y su punto de acuerdo a los parámetros enviados.
	 *
	 * @author  Marlon Plúas <mpluas@telconet.ec>
	 * @version 1.0 11-11-2019
	 *
	 * @param array $arrayParametros    [   'intIdPersona'       => id de la persona
	 *                                      'intIdPunto'         => id del punto de la persona,
	 *                                      'strCodigoContacto'  => código forma de contacto
	 *                                  ]
	 *
	 * @return string $strJsonData
	 */
	public function getJSONContactosClienteyPuntoPorCodigoContacto($arrayParametros)
	{
		$arrayRespuesta                   = array();
		$arrayRespuesta['intTotal']       = 0;
		$arrayRespuesta['arrayResultado'] = array();
		$arrayMergeContac                 = array();
		$arrayFormasContactoEncontradas   = array();
		
		$objQueryContacPerson = $this->_em->createQuery("    SELECT   pfc.valor
                      FROM    schemaBundle:InfoPersonaFormaContacto pfc, 
                              schemaBundle:AdmiFormaContacto fc
                      WHERE   pfc.formaContactoId = fc.id
                      AND     pfc.personaId       = :PersonaId
                      AND     fc.codigo           = :CodigoContacto
                      AND     fc.estado           = :Estado
                      AND     pfc.estado          = :Estado");
		
		$objQueryContacPerson->setParameters(array('PersonaId'      => $arrayParametros["intIdPersona"],
		                                           'CodigoContacto' => $arrayParametros["strCodigoContacto"],
		                                           'Estado'         => "Activo"));
		
		$arrayContacPerson = $objQueryContacPerson->getResult();
		foreach($arrayContacPerson as $dato)
		{
			array_push($arrayMergeContac, $dato["valor"]);
		}
		
		$objQueryContacPunto = $this->_em->createQuery("    SELECT   pfc.valor
                      FROM    schemaBundle:InfoPuntoFormaContacto pfc, 
                              schemaBundle:AdmiFormaContacto fc
                      WHERE   pfc.formaContactoId   = fc.id
                      AND     pfc.puntoId           = :PuntoId
                      AND     fc.codigo             = :CodigoContacto
                      AND     fc.estado             = :Estado
                      AND     pfc.estado            = :Estado");
		
		$objQueryContacPunto->setParameters(array('CodigoContacto' => $arrayParametros["strCodigoContacto"],
		                                          'PuntoId'        => $arrayParametros["intIdPunto"],
		                                          'Estado'         => "Activo"));
		
		$arrayContacPunto = $objQueryContacPunto->getResult();
		foreach($arrayContacPunto as $dato)
		{
			array_push($arrayMergeContac, $dato["valor"]);
		}
		
		$arrayContactos = array_values(array_unique($arrayMergeContac));
		
		foreach($arrayContactos as $valor)
		{
			$arrayFormasContactoEncontradas[] = array("strValorFormaContacto" => $valor);
		}
		
		$arrayRespuesta['arrayResultado'] = $arrayFormasContactoEncontradas;
		$arrayRespuesta['intTotal']       = count($arrayRespuesta['arrayResultado']);
		$strJsonData                      = json_encode($arrayRespuesta, true);
		
		return $strJsonData;
	}

     /**
     * Documentación para el método 'getFormasContactoPersona'.
     *
     * Método que devuelve las formas de contacto del cliente con id enviado como parámetro.
     *
     * @return $arrayPersonaFormaContacto Array resultante.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 11-05-2018 S
     */   
    public function getFormasContactoPersona($intIdPersona)
    {           
        $objQuery = $this->_em->createQuery(
                                            "   SELECT fc.id as idFormaContacto, 
                                                       fc.descripcionFormaContacto, 
                                                       p.id, 
                                                       p.valor 
                                                FROM   schemaBundle:InfoPersonaFormaContacto p, 
                                                       schemaBundle:AdmiFormaContacto fc 
                                                WHERE  p.formaContactoId = fc.id
                                                AND    p.personaId       = :PersonaId
                                                AND    fc.estado         = :EstadoAdmiFormaContacto  
                                                AND    p.estado          = :EstadoInfoPersonaFormaContacto                                                      
                                                AND    (lower(fc.descripcionFormaContacto) like lower('%telefono%') 
                                                OR     lower(fc.descripcionFormaContacto) like lower('%correo%') )
                                            "
                                        );

        $objQuery->setParameters(array('PersonaId'                      => $intIdPersona,
                                       'EstadoAdmiFormaContacto'        => 'Activo',
                                       'EstadoInfoPersonaFormaContacto' => 'Activo'));

        $arrayFormasContacto =  $objQuery->getResult();

        $arrayPersonaFormaContacto = array();

        if($arrayFormasContacto && count($arrayFormasContacto)>0)
        {
            $strValorInicial = "";
            
            foreach($arrayFormasContacto as $objFormaContacto)
            {                
                if($objFormaContacto["valor"] != "" && $objFormaContacto["valor"])
                {
                    if($strValorInicial != $objFormaContacto["valor"])
                    {
                        $arrayFormaContacto['idPersona']              = $intIdPersona;
                        $arrayFormaContacto['idFormaContacto']        = $objFormaContacto["idFormaContacto"];
                        $arrayFormaContacto['idPersonaFormaContacto'] = $objFormaContacto["id"];
                        $arrayFormaContacto['formaContacto']          = $objFormaContacto["descripcionFormaContacto"];
                        $arrayFormaContacto['valor']                  = $objFormaContacto["valor"];
                        $arrayPersonaFormaContacto[]                  = $arrayFormaContacto;
                    }
                    $strValorInicial = $objFormaContacto["valor"];
                }
            }
        }
        
        return $arrayPersonaFormaContacto;
    }


    /**
     * Metodo que retorna todas las formas de contacto de un cliente
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0
     * @since 14-04-2021
     */
    public function getFormasContacto($intIdPersona)    
    {
        
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql         = " SELECT IPFC.ID_PERSONA_FORMA_CONTACTO,IPFC.PERSONA_ID,IPFC.FORMA_CONTACTO_ID, IPFC.VALOR,IPFC.USR_CREACION,
                                IPFC.FE_CREACION,AFC.DESCRIPCION_FORMA_CONTACTO 
                              FROM DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC 
                             JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID
                              WHERE IPFC.ESTADO=:estado AND IPFC.PERSONA_ID= :idPersona ";
        
        $objQuery->setParameter("idPersona",  $intIdPersona);
        $objQuery->setParameter("estado",      'Activo');
        
        $objRsm->addScalarResult('ID_PERSONA_FORMA_CONTACTO', 'idPersonaFormaContacto', 'string');
        $objRsm->addScalarResult('PERSONA_ID', 'idPersona', 'string');
        $objRsm->addScalarResult('VALOR', 'valor', 'string');
        $objRsm->addScalarResult('FORMA_CONTACTO_ID', 'formaContactoId', 'string');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'descripcionFormaContacto', 'string');
        $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();
        
		
	$arrayPersonaFormaContacto = array();
	if($arrayResultado && count($arrayResultado)>0)
	{
		foreach($arrayResultado as $objEntity)
		{
			if($objEntity["valor"] != "" && $objEntity["valor"])
			{
				$arrayPersonaFormaContactoOne['idPersonaFormaContacto'] = $objEntity["idPersonaFormaContacto"];
				$arrayPersonaFormaContactoOne['idPersona'] = $objEntity["idPersona"];
				$arrayPersonaFormaContactoOne['formaContactoId'] = $objEntity["formaContactoId"];
				$arrayPersonaFormaContactoOne['formaContacto'] = $objEntity["descripcionFormaContacto"];
				$arrayPersonaFormaContactoOne['valor'] = $objEntity["valor"];
				$arrayPersonaFormaContactoOne['usrCreacion'] = $objEntity["usrCreacion"];
				$arrayPersonaFormaContactoOne['feCreacion'] = $objEntity['feCreacion'];
				$arrayPersonaFormaContacto[] = $arrayPersonaFormaContactoOne;
			}
		}
	}
	
        return $arrayPersonaFormaContacto;
        
    }
    
    /**
     * Metodo que valida que el id_persona_rol, tenga un rol de tipo cliente
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0
     * @since 14-04-2021
     */
    public function getPersonaRol($arrayParametro)
    {
        
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql         = " SELECT IPER.ID_PERSONA_ROL 
                               FROM INFO_PERSONA_EMPRESA_ROL IPER
                              JOIN INFO_EMPRESA_ROL IER ON IER.ID_EMPRESA_ROL = IPER.EMPRESA_ROL_ID
                               JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL = IER.ROL_ID
                              WHERE IPER.PERSONA_ID=:idPersona
                               AND AR.DESCRIPCION_ROL='Cliente' AND IER.ESTADO=:estado
                               AND IER.EMPRESA_COD=:empresaCod
                               AND ROWNUM =1 ";
        
        $objQuery->setParameter("idPersona",  $arrayParametro['intIdPersona']);
        $objQuery->setParameter("empresaCod", $arrayParametro['strEmpresaCod']);
        $objQuery->setParameter("estado",      'Activo');
        
        $objRsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaRol', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();

        return $arrayResultado;
        
        
    }
    
     /**
     * Documentación para el método 'getFormasContactoPersonaPunto'.
     *
     * Método que devuelve las formas de contacto del cliente con id enviado como parámetro para el punto.
     *
     * @return $arrayPersonaFormaContacto Array resultante.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 15-09-2022 
     */   
    public function getFormasContactoPersonaPunto($intIdPersona)
    {           
        $objQuery = $this->_em->createQuery(
                                            "   SELECT fc.id as idFormaContacto, 
                                                       fc.descripcionFormaContacto, 
                                                       p.id, 
                                                       p.valor 
                                                FROM   schemaBundle:InfoPersonaFormaContacto p, 
                                                       schemaBundle:AdmiFormaContacto fc 
                                                WHERE  p.formaContactoId = fc.id
                                                AND    p.personaId       = :PersonaId
                                                AND    fc.estado         = :EstadoAdmiFormaContacto  
                                                AND    p.estado          = :EstadoInfoPersonaFormaContacto 
                                            "
                                        );

        $objQuery->setParameters(array('PersonaId'                      => $intIdPersona,
                                       'EstadoAdmiFormaContacto'        => 'Activo',
                                       'EstadoInfoPersonaFormaContacto' => 'Activo'));

        $arrayFormasContacto =  $objQuery->getResult();

        $arrayPersonaFormaContacto = array();

        if($arrayFormasContacto && count($arrayFormasContacto)>0)
        {
            $strValorInicial = "";
            
            foreach($arrayFormasContacto as $objFormaContacto)
            {                
                if($objFormaContacto["valor"] != "" && $objFormaContacto["valor"])
                {
                    if($strValorInicial != $objFormaContacto["valor"])
                    {
                        $arrayFormaContacto['idPersona']              = $intIdPersona;
                        $arrayFormaContacto['idFormaContacto']        = $objFormaContacto["idFormaContacto"];
                        $arrayFormaContacto['idPersonaFormaContacto'] = $objFormaContacto["id"];
                        $arrayFormaContacto['formaContacto']          = $objFormaContacto["descripcionFormaContacto"];
                        $arrayFormaContacto['valor']                  = $objFormaContacto["valor"];
                        $arrayPersonaFormaContacto[]                  = $arrayFormaContacto;
                    }
                    $strValorInicial = $objFormaContacto["valor"];
                }
            }
        }
        
        return $arrayPersonaFormaContacto;
    }


        /**
     * Metodo retorna numeros de contacto y descripcion de contacto por servicio
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.1 20-03-2023 - Se modifica el query para obtener los numeros de contacto,
     *                           independiente a la empresa y se agrega la descripcion de la misma. 
     * 
     * @since 02-11-2022
     */
    public function getNumeroContactoPorServicio($arrayParametro)
    {
        try
        {
            $arrayRespuesta = array();
            $arrayData = array();
            $objRsm         = new ResultSetMappingBuilder($this->_em);
            $objQuery       = $this->_em->createNativeQuery(null, $objRsm);

            $strSql = "select FC.VALOR, AFC.DESCRIPCION_FORMA_CONTACTO
                        from DB_COMERCIAL.INFO_SERVICIO s, 
                        DB_COMERCIAL.INFO_PUNTO p,
                        DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO fc, 
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper,
                        DB_COMERCIAL.INFO_EMPRESA_ROL ier,
                        DB_GENERAL.ADMI_ROL ar,
                        DB_COMERCIAL.ADMI_FORMA_CONTACTO afc
                        where S.PUNTO_ID = P.ID_PUNTO
                        and P.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                        and IPER.PERSONA_ID = FC.PERSONA_ID
                        and IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                        and IER.ROL_ID = AR.ID_ROL
                        and AR.DESCRIPCION_ROL = 'Cliente'
                        and fc.FORMA_CONTACTO_ID in (25,26,27)
                        and AFC.ID_FORMA_CONTACTO = fc.FORMA_CONTACTO_ID
                        and S.ID_SERVICIO = :idServicio
                        and fc.ESTADO = 'Activo'
                        and IPER.ESTADO='Activo'";
            
            $objQuery->setParameter('idServicio',$arrayParametro['idServicio']);
            $objRsm->addScalarResult('VALOR','numeroContacto','string');
            $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO','formaContacto','string');
            $objQuery->setSQL($strSql);
            
            $arrayData = $objQuery->getArrayResult();

            $arrayRespuesta['arrayNumerosContactos'] = $arrayData;
            $arrayRespuesta['status'] = 'Ok';
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status'] = 'Error';
            $arrayRespuesta['mensaje'] = $ex->getMessage();
        }
        return $arrayRespuesta;
    }    
    
}
