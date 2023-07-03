<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPuntoFormaContactoRepository extends EntityRepository
{
     /**
     * obtenerFormasContactoPorTipo
     *     
     * Función para obtener las formas de contactos del punto cliente en base a los parametros recibidos y a los codigos de las formas de contacto
     * parametrizados .
     * 
     * @param $arrayParametros [
     *                              intPuntoId             : Id del Punto.
     *                              strEstado              : Estado de la forma de la Contacto.     
     *                              arrayCodFormasContacto : Array de los códigos de las formas de Contacto parametrizados  
     *                              intLimit               : Limite para la consulta
     *                              intStart               : Inicio del registro.    
     *                          ]
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 18-03-2020
     * @return $arrayResultadoPuntos
     */
     public function obtenerFormasContactoPorTipo($arrayParametros)
    {
        $strSqlDatos      = 'SELECT pfc ';
        $strSqlCantidad   = 'SELECT count(pfc) ';
        $strSqlFrom       = 'FROM schemaBundle:InfoPuntoFormaContacto pfc
                             INNER JOIN schemaBundle:AdmiFormaContacto fc WITH fc.id = pfc.formaContactoId
                             WHERE pfc.puntoId = (?1) 
                             AND pfc.estado   = (?2)   
                             AND pfc.valor    IS NOT NULL '.                             
                             ($arrayParametros['arrayCodFormasContacto'] ? ' AND fc.codigo in (?3) ' : '') ;    

        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        $strQueryDatos->setParameter(1, $arrayParametros['intPuntoId']);
        $strQueryDatos->setParameter(2, $arrayParametros['strEstado']);

        if($arrayParametros['arrayCodFormasContacto'] != "")
        { 
            $strQueryDatos->setParameter(3, $arrayParametros['arrayCodFormasContacto']);
        }
       
        $strSqlDatos    .= $strSqlFrom;
        $strQueryDatos->setDQL($strSqlDatos);                
        $objDatos        = $strQueryDatos->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        
        $strQueryCantidad   = '';
        $strQueryCantidad   = $this->_em->createQuery();
        $strQueryCantidad->setParameter(1, $arrayParametros['intPuntoId']);
        $strQueryCantidad->setParameter(2, $arrayParametros['strEstado']);

        if($arrayParametros['arrayCodFormasContacto'] != "")
        { 
            $strQueryCantidad->setParameter(3, $arrayParametros['arrayCodFormasContacto']);
        }
       
        $strSqlCantidad .= $strSqlFrom;   
        $strQueryCantidad->setDQL($strSqlCantidad);        
        $intTotal        = $strQueryCantidad->getSingleScalarResult();
        
        $arrayResultadoPuntos['registros'] = $objDatos;
        $arrayResultadoPuntos['total']     = $intTotal;
        
        return $arrayResultadoPuntos;
    }

     /**
     * getArrayFormasContactoPorTipo
     *     
     * Función que obtiene un array con las formas de contactos por Punto en base a los parametros enviados.
     * 
     * @param $arrayParametros [
     *                              intPuntoId             : Id del Punto.
     *                              strEstado              : Estado de la forma de la Contacto.     
     *                              arrayCodFormasContacto : Array de los códigos de las formas de Contacto parametrizados 
     *                              intLimit               : Limite para la consulta
     *                              intStart               : Inicio del registro.    
     *                          ]
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 18-03-2020
      * 
     * @return array
     */
    public function getArrayFormasContactoPorTipo($arrayParametros)
    {
        $arrayFormasContacto       = array();        
        $arrayResultado            = $this->obtenerFormasContactoPorTipo($arrayParametros);
        $objFormasContactoPorTipo  = $arrayResultado['registros'];
        $intTotal                  = $arrayResultado['total'];
        if($objFormasContactoPorTipo)
        {            
            foreach($objFormasContactoPorTipo as $objFormasContactoPorTipo)
            {                             
                $arrayFormasContacto[] = array('idPersonaFormaContacto' => $objFormasContactoPorTipo->getId(),
                                               'idPersona'              => $objFormasContactoPorTipo->getPuntoId()->getId(),
                                               'formaContacto'          => $objFormasContactoPorTipo->getFormaContactoId()
                                                                                                    ->getDescripcionFormaContacto(),
                                               'valor'                  => $objFormasContactoPorTipo->getValor(),
                                               'idFormaContacto'        => $objFormasContactoPorTipo->getFormaContactoId()->getId());  
            }
        }
       
         return array('total' => $intTotal, 'registros' => $arrayFormasContacto);      
    }
    
    public function findPorEstadoPorPunto($idPunto, $estado, $limit, $start)
    {
        $query = $this->_em->createQuery("SELECT a
                FROM 
                        schemaBundle:InfoPuntoFormaContacto a
                WHERE    
                        a.puntoId = :idPunto AND
                        a.valor is not null
                        AND a.estado = :estado");
        $query->setParameter('idPunto', $idPunto);
        $query->setParameter('estado', $estado);
        // $datos = $query->getResult(); // era redundante e innecesario por el count de mas abajo
        $total = count($query->getResult()); // TODO: usar count SQL
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    }
        
        
        
        public function findContactosByPunto($login,$formaContacto){
            
            if($formaContacto)
			$whereFormaContacto = " AND	lower(afc.descripcionFormaContacto) = lower('$formaContacto')";
		else
			$whereFormaContacto = "";
			
		$query = $this->_em->createQuery("SELECT afc.descripcionFormaContacto,
					   ptofc.valor
		FROM 
				schemaBundle:InfoPunto pto,
				
				schemaBundle:InfoPuntoFormaContacto ptofc,
				schemaBundle:AdmiFormaContacto afc   				
		WHERE    
				ptofc.puntoId = pto.id AND
				afc.id=ptofc.formaContactoId 
				AND  lower(pto.login) = lower('$login')
				AND lower(ptofc.estado) = lower('Activo')
				AND ptofc.valor is not null
				$whereFormaContacto ");
                $total=count($query->getResult());
		$datos = $query->getResult();
               // echo($query->getSQL()); die();
		return $datos;
            
        }
        
           
    /**
     * getDatosPunto
     *
     * Metodo encargado obtener la informacion basica por Punto (Nombre Cliente y login)      
     *
     * @return array con informacion a mostrar
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 04-09-2014
     */           
    public function getDatosPunto($intPunto)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);

        $sql ="select 
                DISTINCT
                NVL(c.RAZON_SOCIAL, c.NOMBRES  || '' || c.APELLIDOS) as NOMBRE,
                a.LOGIN,
                a.ID_PUNTO
                from
                info_punto a,
                info_persona_empresa_rol b,
                info_persona c
                where
                a.PERSONA_EMPRESA_ROL_ID = b.ID_PERSONA_ROL and
                b.PERSONA_ID             = c.ID_PERSONA    
                AND a.ID_PUNTO in (:punto)
                ";
        
        $query->setParameter('punto', $intPunto);

        $rsm->addScalarResult('NOMBRE', 'nombre', 'string');
        $rsm->addScalarResult('LOGIN', 'login', 'string');
        $rsm->addScalarResult('ID_PUNTO', 'idPunto', 'integer');                        
        
        $query->setSQL($sql);
        
        $arrayResultado = $query->getArrayResult();
        
        $array = array();
        
        if($arrayResultado)
        {
            foreach($arrayResultado as $data)
            {
                $array[] = array(
                    "id"          => $data['idPunto'],
                    "nombre"      => $data['nombre'],
                    "login"       => $data['login']                  
                );
            }         
        }

        return $array;
    }
           
     /**
     * getContactosPorFormasContactoId
     *
     * Metodo encargado obtener informacion de formas de contactos que no fueron usados par despacho de notificaciones     
     *
     * @return string con formas de contacto 
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 04-09-2014
     */             
    public function getContactosPorFormasContactoId($intPunto ,$strTipo)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $query = $this->_em->createNativeQuery(null, $rsm);
        
        //Hace una busqueda en las formas de contacto que sean MOVILES (CLARO, MOVISTAR, ALEGRO) 
        //que se encuentren Inactivos por lo que no fueron enviados
        if(strcmp($strTipo, "MOVIL") !==0 )
        {
            $strWhere = " d.FORMA_CONTACTO_ID in (1,25,26,27) and d.estado = :estado and ";             
            $query->setParameter('estado', 'Inactivo');                        
        }
        //Hace una busqueda de numeros celulares guardados como telefonos FIJOS para verificacion
        // de que tengan formatos correctos
        else
        {
            $strWhere = " d.FORMA_CONTACTO_ID in (4) and ";                        
        }

        $sql ="select distinct
                d.valor                
                FROM
                info_punto a,
                info_persona_empresa_rol b,
                info_persona c,
                INFO_PERSONA_FORMA_CONTACTO d
                where
                a.PERSONA_EMPRESA_ROL_ID = b.ID_PERSONA_ROL and
                b.PERSONA_ID             = c.ID_PERSONA and
                d.PERSONA_ID             = c.ID_PERSONA and
                $strWhere
                a.ID_PUNTO = :punto
                
                UNION
                
                select distinct
                d.valor                
                from
                info_punto a,
                INFO_PUNTO_FORMA_CONTACTO d
                where
                d.PUNTO_ID = a.ID_PUNTO and
                $strWhere
                a.ID_PUNTO = :punto";
        
        $query->setParameter('punto', $intPunto);                      
        
        $rsm->addScalarResult('VALOR', 'valor', 'string');                                     
        
        $query->setSQL($sql);
        
        $arrayResultado = $query->getArrayResult();                
        
        $strContactos = "";
        
        if($arrayResultado)
        {
            foreach($arrayResultado as $data)
            {
                $strContactos .= $data['valor']." ";
            }         
        }

        return $strContactos;
    }

    /**
     * Documentación para el método 'getFormasContactoParaSession'.
     * 
     * Método que obtiene las formas de contacto del punto para mostrar en la sesión 
     * 
     * @param Array $arrayParametros Parámetros para la consulta:
     *                               $arrayParametros['PUNTOID'] Id del Punto.
     *                               $arrayParametros['FORMA1']  Descripción de la primera forma de contacto.
     *                               $arrayParametros['FORMA2']  Descripción de la segunda forma de contacto.
     *                               $arrayParametros['ESTADO']  Estado por el que se consulta la forma de contacto.
     *                               
     * @return Array Formas de contacto del punto.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 27-11-2015
     */
    public function getFormasContactoParaSession($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $dqlQuery = "SELECT fc.id as idFormaContacto, fc.descripcionFormaContacto, p.id, p.valor, p.feCreacion, p.usrCreacion
                     FROM schemaBundle:InfoPuntoFormaContacto p, schemaBundle:AdmiFormaContacto fc 
                     WHERE p.formaContactoId = fc.id
                     AND p.puntoId = :PUNTOID 
                     AND (LOWER(fc.descripcionFormaContacto) like LOWER(:FORMA1) or LOWER(fc.descripcionFormaContacto) like LOWER(:FORMA2))
                     AND LOWER(fc.estado) = LOWER(:ESTADO)
                     AND LOWER(p.estado) = LOWER(:ESTADO)
                     ORDER BY fc.descripcionFormaContacto";
        $objQuery->setParameter('PUNTOID', $arrayParametros['PUNTOID']);
        $objQuery->setParameter('FORMA1',  $arrayParametros['FORMA1']);
        $objQuery->setParameter('FORMA2',  $arrayParametros['FORMA2']);
        $objQuery->setParameter('ESTADO',  $arrayParametros['ESTADO']);
        $objQuery->setDQL($dqlQuery);

        $listaFormasContactoPunto  = $objQuery->getResult();
        $arrayPuntoFormasContactos = array();
        if($listaFormasContactoPunto && count($listaFormasContactoPunto) > 0)
        {
            foreach($listaFormasContactoPunto as $entity)
            {
                if($entity["valor"] != "" && $entity["valor"])
                {
                    $puntoFormaContacto['idFormaContacto']        = $entity["idFormaContacto"];
                    $puntoFormaContacto['idPersonaFormaContacto'] = $entity["id"];
                    $puntoFormaContacto['formaContacto']          = $entity["descripcionFormaContacto"];
                    $puntoFormaContacto['valor']                  = $entity["valor"];
                $puntoFormaContacto['usrCreacion']                = $entity["usrCreacion"];
                $puntoFormaContacto['feCreacion']                 = $entity["feCreacion"];
                    $arrayPuntoFormasContactos[]                  = $puntoFormaContacto;
                }
            }
        }
        return $arrayPuntoFormasContactos;
    }
    
    public function getArrayFormaContactosPorPunto($intPunto)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $objQuery2 = $this->_em->createNativeQuery(null, $objRsm);
            $strSql = "select regexp_substr(VALOR,'[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}') VALOR
                    from INFO_PUNTO_FORMA_CONTACTO
                    where punto_id = :punto and FORMA_CONTACTO_ID = :formacontacto";
            $objRsm->addScalarResult('VALOR', 'valor', 'string');
            $objQuery->setParameter("punto", $intPunto);
            $objQuery->setParameter("formacontacto", 5);
            $objQuery->setSQL($strSql);
            $arrayPuntoFormaContacto = $objQuery->getResult();
            
            $strSql1 = "select regexp_substr(FC.VALOR,'[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}') VALOR
                    FROM DB_COMERCIAL.info_punto PC,
                         DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO FC,
                         DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER,
                         DB_COMERCIAL.INFO_EMPRESA_ROL ER--,
                         WHERE pc.id_punto= :punto
                         and PC.PERSONA_EMPRESA_ROL_ID        =  PER.ID_PERSONA_ROL
                         AND PER.ID_PERSONA_ROL   = PC.PERSONA_EMPRESA_ROL_ID
                         AND PER.EMPRESA_ROL_ID   = ER.ID_EMPRESA_ROL
                         and PER.PERSONA_ID=FC.PERSONA_ID
                         AND FC.FORMA_CONTACTO_ID = :formacontacto
                         AND FC.ESTADO            = :activo
                         AND PC.ESTADO            = :activo     ";
            $objRsm->addScalarResult('VALOR', 'valor', 'string');
            $objQuery2->setParameter("punto", $intPunto);
            $objQuery2->setParameter("formacontacto", 5);
            $objQuery2->setParameter("activo", 'Activo');
            $objQuery2->setSQL($strSql1);
            $arrayPersonaContacto = $objQuery2->getArrayResult();
            $arrayResultado = array_merge($arrayPuntoFormaContacto, $arrayPersonaContacto);
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }
    
    
     /**
     /**
     * método que retorna las formas de contacto de un punto
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0
     * @since 14-04-2021
     */
    public function getFormasContactoPunto($intIdPunto)
    {
      
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql         = " SELECT IPFC.ID_PUNTO_FORMA_CONTACTO,IPFC.PUNTO_ID,IPFC.VALOR,
                               IPFC.FORMA_CONTACTO_ID,AFC.DESCRIPCION_FORMA_CONTACTO,
                               IPFC.FE_CREACION,IPFC.USR_CREACION
                              FROM DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPFC
                             JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC ON AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID
                              WHERE IPFC.ESTADO= :estado AND IPFC.PUNTO_ID= :idPunto ";
        
        $objQuery->setParameter("idPunto",  $intIdPunto);
        $objQuery->setParameter("estado",      'Activo');
        
        $objRsm->addScalarResult('ID_PUNTO_FORMA_CONTACTO', 'idPuntoFormaContacto', 'string');
        $objRsm->addScalarResult('PUNTO_ID', 'idPunto', 'string');
        $objRsm->addScalarResult('VALOR', 'valor', 'string');
        $objRsm->addScalarResult('FORMA_CONTACTO_ID', 'formaContactoId', 'string');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'descripcionFormaContacto', 'string');
        $objRsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();
        
		
	$arrayPuntoFormaContacto = array();
	if($arrayResultado && count($arrayResultado)>0)
	{
		foreach($arrayResultado as $objEntity)
		{
			if($objEntity["valor"] != "" && $objEntity["valor"])
			{
				$arrayPuntoFormaContactoOne['idPuntoFormaContacto'] = $objEntity["idPuntoFormaContacto"];
				$arrayPuntoFormaContactoOne['idPunto'] = $objEntity["idPunto"];
				$arrayPuntoFormaContactoOne['formaContactoId'] = $objEntity["formaContactoId"];
				$arrayPuntoFormaContactoOne['formaContacto'] = $objEntity["descripcionFormaContacto"];
				$arrayPuntoFormaContactoOne['valor'] = $objEntity["valor"];
				$arrayPuntoFormaContactoOne['usrCreacion'] = $objEntity["usrCreacion"];
				$arrayPuntoFormaContactoOne['feCreacion'] = $objEntity['feCreacion'];
				$arrayPuntoFormaContacto[] = $arrayPuntoFormaContactoOne;
			}
		}
	}
	
        return $arrayPuntoFormaContacto;

               
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
        
        $strSql         = " SELECT IPER.PERSONA_ID 
                               FROM INFO_PERSONA_EMPRESA_ROL IPER
                              JOIN INFO_EMPRESA_ROL IER ON IER.ID_EMPRESA_ROL = IPER.EMPRESA_ROL_ID
                               JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL = IER.ROL_ID
                              WHERE IPER.ID_PERSONA_ROL = :idPersonaRol
                               AND AR.DESCRIPCION_ROL='Cliente' AND IER.ESTADO=:estado
                               AND IER.EMPRESA_COD=:empresaCod
                               AND ROWNUM =1 ";
        
        $objQuery->setParameter("idPersonaRol",  $arrayParametro['intIdPersonaRol']);
        $objQuery->setParameter("empresaCod", $arrayParametro['strEmpresaCod']);
        $objQuery->setParameter("estado",      'Activo');
        
        $objRsm->addScalarResult('PERSONA_ID', 'idPersona', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getArrayResult();

        return $arrayResultado;
        
        
    }
    
    
}
