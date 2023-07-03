<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiFormaContactoRepository extends EntityRepository
{

    public function findFormasContactoPorEstado($estado){	
        $query = $this->_em->createQuery("SELECT p
        FROM schemaBundle:AdmiFormaContacto p
                WHERE p.estado = :estado 
                ORDER BY p.id");
        $query->setParameter('estado', $estado);
        $datos = $query->getResult();
        return $datos;
    }
      

    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_forma_contacto' =>$data->getId(),
                                         'descripcion_forma_contacto' =>trim($data->getDescripcionFormaContacto()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_forma_contacto' => 0 , 'descripcion_forma_contacto' => 'Ninguno', 'forma_contacto_id' => 0 , 'forma_contacto_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:AdmiFormaContacto','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.descripcionFormaContacto) like LOWER(?1)');
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

    /**
     * Función que obtiene los contactos dependiendo del tipo que se le proporcione.
     * Puede obtener los contactos del punto y de la persona según sea proporcionado.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 19-06-2018
     */
    public function obtieneFormaContactoxParametros($arrayParametros)
    {
        $arrayRespuesta = array();
        $strSql = "SELECT
                        DISTINCT IPFC.VALOR
                    FROM
                        DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC,
                        DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPFC
                    WHERE
                        AFC.DESCRIPCION_FORMA_CONTACTO LIKE :strDescripcion
                        AND AFC.ESTADO = :strEstado
                        AND IPFC.PUNTO_ID = :intPuntoId
                        AND IPFC.ESTADO = :strEstado
                        AND AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID
                    UNION
                    SELECT
                        DISTINCT IPFC.VALOR
                    FROM
                        DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC,
                        DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPFC
                    WHERE
                        AFC.DESCRIPCION_FORMA_CONTACTO LIKE :strDescripcion
                        AND AFC.ESTADO = :strEstado
                        AND IPFC.PERSONA_ID = :intPersonaId
                        AND IPFC.ESTADO = :strEstado
                        AND AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID";
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("strDescripcion", $arrayParametros["strDescripcionFormaContacto"]);
        $objQuery->setParameter("intPuntoId", $arrayParametros["intPuntoId"]);
        $objQuery->setParameter("intPersonaId", $arrayParametros["intPersonaId"]);
        $objQuery->setParameter("strEstado", "Activo");

        $objRsm->addScalarResult('VALOR', 'valor', 'string');
        $objQuery->setSQL($strSql);
        $arrayContactos = $objQuery->getScalarResult();
        foreach($arrayContactos as $arrayDestinatarioHijo)
        {
            $arrayRespuesta[] = $arrayDestinatarioHijo["valor"];
        }
        return $arrayRespuesta;
    }

    /**
     * Función que obtiene los contactos del punto dependiendo del tipo que se le proporcione.
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0
     * @since 07-12-2020
     */
    public function obtieneFormaContactoxPunto($arrayParametros)
    {
        $strSql = "SELECT
                        IPFC.ID_PUNTO_FORMA_CONTACTO, AFC.DESCRIPCION_FORMA_CONTACTO, IPFC.VALOR, AFC.ID_FORMA_CONTACTO, AFC.ESTADO
                    FROM
                        DB_COMERCIAL.ADMI_FORMA_CONTACTO AFC,
                        DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPFC
                    WHERE
                            AFC.ESTADO = :strEstado
                        AND IPFC.PUNTO_ID = :intPuntoId
                        AND IPFC.ESTADO = :strEstado
                        AND AFC.ID_FORMA_CONTACTO = IPFC.FORMA_CONTACTO_ID";
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $objQuery->setParameter("intPuntoId", $arrayParametros["intPuntoId"]);
        $objQuery->setParameter("strEstado", "Activo");

        $objRsm->addScalarResult('VALOR', 'valor', 'string');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO', 'formaContacto', 'string');
        $objRsm->addScalarResult('ID_PUNTO_FORMA_CONTACTO', 'idPersonaFormaContacto','integer');
        $objRsm->addScalarResult('ID_FORMA_CONTACTO', 'idFormaContacto','integer');
        $objRsm->addScalarResult('ESTADO', 'estado','string');
        $objQuery->setSQL($strSql);
        $arrayContactos = $objQuery->getScalarResult();
        
        return $arrayContactos;
    }
        
	public function findPorDescripcionFormaContacto($descripcion){	
		$query = $this->_em->createQuery("SELECT p
		FROM schemaBundle:AdmiFormaContacto p
			WHERE p.descripcionFormaContacto = :descripcion");
		$query->setParameter('descripcion', $descripcion);
		$datos = $query->getOneOrNullResult();
		return $datos;
	}        

    /**
     * Función que obtiene los contactos dependiendo del codigo que se le proporcione.
     * @author: Eduardo Vargas Perero <eevargas@telconet.ec>
     * @version 1.0
     * 
     * @since 15-03-2023
     */ 
    public function findPorCodigoFormaContacto($arrayCodigo)
    {	
		$objQuery = $this->_em->createQuery("SELECT p
		FROM schemaBundle:AdmiFormaContacto p
			WHERE p.codigo = :codigo");
		$objQuery->setParameter('codigo', $arrayCodigo);
		$arrayDatos = $objQuery->getOneOrNullResult();
		return $arrayDatos;  
	}

    /**
     * Función que obtiene los contactos dependiendo de los parámetros recibidos.
     * @author: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0
     * costoQuery: 3
     * @since 10-04-2019
     */        
    public function getFormasContactoParametros($arrayParametros)
    {	
        $objQuery = $this->_em->createQuery();
        $strWhere = "";
        $strSql = "SELECT p 
        FROM schemaBundle:AdmiFormaContacto p
                WHERE 1 = 1 ";
        $strOrder = "ORDER BY p.id";
        if (isset($arrayParametros['strMostrarApp']))
        {
            $strWhere .= " and p.mostrarApp = :mostrarApp ";
            $objQuery->setParameter('mostrarApp', $arrayParametros['strMostrarApp']);        
        }
        
        if (isset($arrayParametros['strEstado']))
        {
            $strWhere .= " and p.estado = :estado ";
            $objQuery->setParameter('estado', $arrayParametros['strEstado']);        
        }

        $objQuery->setDQL($strSql . $strWhere . $strOrder);
        $arrayRespuesta = $objQuery->getResult();
        return $arrayRespuesta;
    }

    /**
    * Función que obtiene las formas de contacto según parametros:
    * arrayParametros[
    *     intIdPunto           => Id del punto
    *     strTipoFormaContacto => Tipo de Forma de contacto MAIL o FONO
    * ]
    * @param  type array $arrayParametros
    * @return type array $arrayAreas
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 25-10-2019
    */
    public function getFormasContactoByPunto($arrayParametros)
    {
        $intIdPunto           = $arrayParametros['intIdPunto'];
        $strTipoFormaContacto = $arrayParametros['strTipoFormaContacto'];
        $arrayDatos           = array();
        $objRsm               = new ResultSetMappingBuilder($this->_em);
        $objQuery             = $this->_em->createNativeQuery(null,$objRsm);
        $strQuery             = "SELECT COMEK_CONSULTAS.F_GET_FORMAS_CONTACTO_BY_PUNTO(:idPunto, :tipoFormaContacto) AS DATOS FROM DUAL";

        $objRsm->addScalarResult('DATOS', 'datos', 'string');

        if( ( isset($intIdPunto) && !empty($intIdPunto) ) && ( isset($strTipoFormaContacto) && !empty($strTipoFormaContacto) ) )
        {
            $objQuery->setParameter('idPunto',  $intIdPunto);
            $objQuery->setParameter('tipoFormaContacto',  $strTipoFormaContacto);
            $objQuery->setSQL($strQuery);
            $arrayDatos = $objQuery->getArrayResult();
        }
        return $arrayDatos;
    }

    /**
     * Realiza la consulta de tareas pendientes por departamento
     * @param $arrayParametros
     * [
     *     strTipoFormaContacto    => Tipo de forma de contacto puede ser "FONO", "MOVIL" o "MAIL"
     *     strCodEmpresa           => id de la empresa
     *     strDatabaseDsn          => dsn para conexión a base de datos
     *     strUserDbComercial      => usuario de esquema DbComercial
     *     strPasswordDbComercial  => password de esquema DbComercial
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-10-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getPuntoByFormasContacto($arrayParametros)
    {
        $objCursorPuntos = null;
        try
        {
            $strCodEmpresa          = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                       ? $arrayParametros['strCodEmpresa'] : '';
            $strTipoFormaContacto   = ( isset($arrayParametros['strTipoFormaContacto']) && !empty($arrayParametros['strTipoFormaContacto']) )
                                       ? $arrayParametros['strTipoFormaContacto'] : '';
            $strValorFormaContacto  = ( isset($arrayParametros['strValorFormaContacto']) && !empty($arrayParametros['strValorFormaContacto']) )
                                       ? $arrayParametros['strValorFormaContacto'] : '';
            $strDatabaseDsn         = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                       ? $arrayParametros['strDatabaseDsn'] : null;
            $strUserDbComercial     = ( isset($arrayParametros['strUserDbComercial']) && !empty($arrayParametros['strUserDbComercial']) )
                                       ? $arrayParametros['strUserDbComercial'] : null;
            $strPasswordDbComercial = ( isset($arrayParametros['strPasswordDbComercial']) && !empty($arrayParametros['strPasswordDbComercial']) )
                                       ? $arrayParametros['strPasswordDbComercial'] : null;
            if( !empty($strCodEmpresa)     && !empty($strTipoFormaContacto) && !empty($strValorFormaContacto) &&
                !empty($strUserDbComercial)  && !empty($strPasswordDbComercial) )
            {
                $objOciConexion  = oci_connect($strUserDbComercial, $strPasswordDbComercial, $strDatabaseDsn);
                $objCursorPuntos = oci_new_cursor($objOciConexion);
                $strSQL          = "BEGIN DB_COMERCIAL.COMEK_CONSULTAS.P_GET_PUNTOS_BY_FORMA_CONTAC( ".
                                                                                                     ":strCodEmpresa, ".
                                                                                                     ":strValorFormaContacto, ".
                                                                                                     ":strTipoFormaContacto, ".
                                                                                                     ":strMensajeRespuesta, ".
                                                                                                     ":cursorPuntos  ); END;";
                $objStmt          = oci_parse($objOciConexion, $strSQL);
                oci_bind_by_name($objStmt, ":strCodEmpresa"        ,  $strCodEmpresa, 2000);
                oci_bind_by_name($objStmt, ":strValorFormaContacto",  $strValorFormaContacto ,2000);
                oci_bind_by_name($objStmt, ":strTipoFormaContacto" ,  $strTipoFormaContacto, 2000);
                oci_bind_by_name($objStmt, ":strMensajeRespuesta"  ,  $strMensajeRespuesta,2000);
                oci_bind_by_name($objStmt, ":cursorPuntos"         ,  $objCursorPuntos, -1, OCI_B_CURSOR);
                oci_execute($objStmt);
                oci_execute($objCursorPuntos);
                oci_commit($objOciConexion);
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información. - strCodEmpresa('.
                                     $strCodEmpresa.'), strValorFormaContacto('.
                                     $strValorFormaContacto.'), '.', strTipoFormaContacto('.$strTipoFormaContacto.'), '.
                                     ' Database('.$strDatabaseDsn.'), UsrSoporte('.$strUserDbComercial.'), PassSoporte('.$strPasswordDbComercial.').'
                                    );
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $objCursorPuntos;
    }

    /**
     * Método encargado de obtener los contactos de un cliente.
     *
     * Costo 20
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 06-02-2020
     *
     * @param Array $arrayParametros [
     *                                 serviceUtil       : Objeto de la clase UtilService.
     *                                 strUser           : Usuario quien realiza la petición.
     *                                 strIp             : Ip del usuario quien realiza la petición.
     *                                 intIdPunto        : Id del punto cliente (Info_Punto).
     *                                 strLogin          : Login del punto cliente (Info_Punto).
     *                                 strEstadoContacto : Estado del contacto del cliente y punto
     *                                                       (Info_Persona_Forma_Contacto y Info_Punto_Forma_Contacto).
     *                                 strEstadoWs       : Estado del contacto en uso del cliente y punto
     *                                                       (Info_Persona_Forma_Contacto y Info_Punto_Forma_Contacto).
     *                                 strValor          : Valor de la forma de contacto del cliente y punto.
     *                               ]
     *
     * @return $arrayResultado
     */
    public function getNumeroMovilPorPunto($arrayParametros)
    {
        $serviceUtil   = $arrayParametros['serviceUtil'];
        $strUser       = $arrayParametros["strUser"] ? $arrayParametros["strUser"] : "Telcos+";
        $strIp         = $arrayParametros["strIp"]   ? $arrayParametros["strIp"]   : "127.0.0.1";
        $strWhereIpufc = '';
        $strWhereIpefc = '';

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            if(isset($arrayParametros['intIdPunto']) && !empty($arrayParametros['intIdPunto']))
            {
                $strWhereIpefc .= 'AND IP.ID_PUNTO = :intIdPunto ';
                $strWhereIpufc .= $strWhereIpefc;
                $objNativeQuery->setParameter('intIdPunto', $arrayParametros['intIdPunto']);
            }

            if(isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhereIpefc .= 'AND UPPER(IP.LOGIN) = UPPER(:strLogin) ';
                $strWhereIpufc .= $strWhereIpefc;
                $objNativeQuery->setParameter('strLogin', $arrayParametros['strLogin']);
            }

            if(isset($arrayParametros['strEstadoContacto']) && !empty($arrayParametros['strEstadoContacto']))
            {
                $strWhereIpefc .= 'AND UPPER(IPEFC.ESTADO) = UPPER(:strEstadoContacto) ';
                $strWhereIpufc .= 'AND UPPER(IPUFC.ESTADO) = UPPER(:strEstadoContacto) ';
                $objNativeQuery->setParameter('strEstadoContacto', $arrayParametros['strEstadoContacto']);
            }

            if(isset($arrayParametros['strEstadoWs']) && !empty($arrayParametros['strEstadoWs']))
            {
                $strWhereIpefc .= 'AND UPPER(IPEFC.ESTADO_WS) = UPPER(:strEstadoWs) ';
                $strWhereIpufc .= 'AND UPPER(IPUFC.ESTADO_WS) = UPPER(:strEstadoWs) ';
                $objNativeQuery->setParameter('strEstadoWs', $arrayParametros['strEstadoWs']);
            }

            if(isset($arrayParametros['strValor']) && !empty($arrayParametros['strValor']))
            {
                $strWhereIpefc .= 'AND IPEFC.VALOR = :strValor ';
                $strWhereIpufc .= 'AND IPUFC.VALOR = :strValor ';
                $objNativeQuery->setParameter('strValor', $arrayParametros['strValor']);
            }

            $strSql = " SELECT IP.ID_PUNTO                     AS ID_PUNTO, ".
                              "IP.LOGIN                        AS LOGIN_PUNTO, ".
                              "IPEFC.ID_PERSONA_FORMA_CONTACTO AS ID_CONTACTO, ".
                              "'personaFormaContacto'          AS TIPO_CONTACTO, ".
                              "IPEFC.ESTADO                    AS ESTADO_CONTACTO, ".
                              "AFC.DESCRIPCION_FORMA_CONTACTO  AS FORMA_CONTACTO, ".
                              "REPLACE(REPLACE(REGEXP_REPLACE( ".
                                   "NVL2(IPEFC.VALOR,IPEFC.VALOR, NULL),'[^[:digit:]|;]',''),'  ',''),' ','') AS VALOR, ".
                              "TO_CHAR(IPEFC.FE_CREACION,'RRRR-MM-DD HH24:MI:SS')    AS FECHA_CREACION_CONTACTO, ".
                              "IPEFC.ESTADO_WS                                       AS ESTADO_WS, ".
                              "TO_CHAR(IPEFC.FE_CREACION_WS,'RRRR-MM-DD HH24:MI:SS') AS FECHA_CREACION_WS ".
                       "FROM DB_COMERCIAL.INFO_PUNTO                  IP, ".
                            "DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL    IPER, ".
                            "DB_COMERCIAL.INFO_PERSONA                IPR, ".
                            "DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPEFC, ".
                            "DB_COMERCIAL.ADMI_FORMA_CONTACTO         AFC ".
                       "WHERE IP.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL ".
                         "AND IPER.PERSONA_ID           = IPR.ID_PERSONA ".
                         "AND IPR.ID_PERSONA            = IPEFC.PERSONA_ID ".
                         "AND IPEFC.FORMA_CONTACTO_ID   = AFC.ID_FORMA_CONTACTO ".
                         "AND IP.ESTADO                IN (:arrayEstadosPunto) ".
                         "AND AFC.ESTADO                = :strEstadoFormaContacto ".
                         "AND AFC.CODIGO               IN ( ".
                              "SELECT CODIGO ".
                                "FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO ".
                              "WHERE DESCRIPCION_FORMA_CONTACTO LIKE ".
                                "CASE 'MOVIL' ".
                                  "WHEN 'MAIL' ".
                                    "THEN 'Correo%' ".
                                  "WHEN 'FONO' ".
                                    "THEN 'Telefono%' ".
                                  "WHEN 'MOVIL' ".
                                    "THEN '%Movil%' ".
                                  "ELSE 'Telefono%' ".
                                "END ".
                         ") $strWhereIpefc".
                       "UNION ".
                       "SELECT IP.ID_PUNTO                    AS ID_PUNTO, ".
                              "IP.LOGIN                       AS LOGIN_PUNTO, ".
                              "IPUFC.ID_PUNTO_FORMA_CONTACTO  AS ID_CONTACTO, ".
                              "'puntoFormaContacto'           AS TIPO_CONTACTO, ".
                              "IPUFC.ESTADO                   AS ESTADO_CONTACTO, ".
                              "AFC.DESCRIPCION_FORMA_CONTACTO AS FORMA_CONTACTO, ".
                              "REPLACE(REPLACE(REGEXP_REPLACE( ".
                               "NVL2(IPUFC.VALOR,IPUFC.VALOR, NULL),'[^[:digit:]|;]',''),'  ',''),' ','') AS VALOR, ".
                              "TO_CHAR(IPUFC.FE_CREACION,'RRRR-MM-DD HH24:MI:SS')    AS FECHA_CREACION_CONTACTO, ".
                              "IPUFC.ESTADO_WS                                       AS ESTADO_WS, ".
                              "TO_CHAR(IPUFC.FE_CREACION_WS,'RRRR-MM-DD HH24:MI:SS') AS FECHA_CREACION_WS ".
                       "FROM DB_COMERCIAL.INFO_PUNTO                IP, ".
                            "DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPUFC, ".
                            "DB_COMERCIAL.ADMI_FORMA_CONTACTO       AFC ".
                       "WHERE IP.ID_PUNTO              = IPUFC.PUNTO_ID ".
                         "AND IPUFC.FORMA_CONTACTO_ID  = AFC.ID_FORMA_CONTACTO ".
                         "AND IP.ESTADO               IN (:arrayEstadosPunto) ".
                         "AND AFC.ESTADO               = :strEstadoFormaContacto ".
                         "AND AFC.CODIGO              IN ( ".
                              "SELECT CODIGO ".
                                "FROM DB_COMERCIAL.ADMI_FORMA_CONTACTO ".
                              "WHERE DESCRIPCION_FORMA_CONTACTO LIKE ".
                                "CASE 'MOVIL' ".
                                  "WHEN 'MAIL' ".
                                    "THEN 'Correo%' ".
                                  "WHEN 'FONO' ".
                                    "THEN 'Telefono%' ".
                                  "WHEN 'MOVIL' ".
                                    "THEN '%Movil%' ".
                                  "ELSE 'Telefono%' ".
                                "END ".
                         ") $strWhereIpufc";

            $objNativeQuery->setParameter('arrayEstadosPunto'      , array('Activo','In-Corte','Pendiente'));
            $objNativeQuery->setParameter('strEstadoFormaContacto' , 'Activo');

            $objResultSetMap->addScalarResult('ID_PUNTO'                , 'idPunto'               , 'integer');
            $objResultSetMap->addScalarResult('LOGIN_PUNTO'             , 'loginPunto'            , 'string');
            $objResultSetMap->addScalarResult('ID_CONTACTO'             , 'idContacto'            , 'integer');
            $objResultSetMap->addScalarResult('TIPO_CONTACTO'           , 'tipoContacto'          , 'string');
            $objResultSetMap->addScalarResult('ESTADO_CONTACTO'         , 'estadoContacto'        , 'string');
            $objResultSetMap->addScalarResult('FORMA_CONTACTO'          , 'formaContacto'         , 'string');
            $objResultSetMap->addScalarResult('VALOR'                   , 'numeroTelefono'        , 'string');
            $objResultSetMap->addScalarResult('FECHA_CREACION_CONTACTO' , 'fechaCreacionContacto' , 'string');
            $objResultSetMap->addScalarResult('ESTADO_WS'               , 'estadoWs'              , 'string');
            $objResultSetMap->addScalarResult('FECHA_CREACION_WS'       , 'fechaEstadoWs'         , 'string');

            $objNativeQuery->setSQL($strSql);

            $arrayResult    = $objNativeQuery->getResult();
            $arrayRespuesta = array ('status' => 'ok','result' => $arrayResult);

            if (count($arrayResult) < 1 || empty($arrayResult))
            {
                $arrayRespuesta = array ('status'  => 'fail',
                                         'message' => 'La consulta no retornó información con los valores ingresados.');
            }
        }
        catch (\Exception $objException)
        {
            if (is_object($serviceUtil))
            {
                $serviceUtil->insertError('Telcos+',
                                          'AdmiFormaContactoRepository->getNumeroMovilPorPunto',
                                           $objException->getMessage(),
                                           $strUser,
                                           $strIp);
            }

            $arrayRespuesta = array ('status' => 'fail', 'message' => 'Error al obtener los datos');
        }

        return $arrayRespuesta;
    }


 /**
    * Método que sirve para extraer los contactos telefonicos 
    * de un punto TN por medio de su login. 
    * arrayParametros[
    *     strLogin          => Login
    *     strTipoRol        => Contacto
    * ]
    *
    * @param  type array $arrayParametros
    * @return type array $arrayDatos
    * @author Wilmer Vera <wvera@telconet.ec>
    * @version 1.0 31-03-2020
    */
    public function getContactosTNPorLogin($arrayParametros)
    {
        $strLogin             = $arrayParametros['strLogin'];
        $strTipoRol           = $arrayParametros['strTipoRol'];
        
        $arrayDatos           = array();
        $objRsm               = new ResultSetMappingBuilder($this->_em);
        $objQuery             = $this->_em->createNativeQuery(null,$objRsm);

        $strQuery             = "SELECT DISTINCT * FROM (
            SELECT AR2.DESCRIPCION_ROL, (IP2.NOMBRES||' '||IP2.APELLIDOS) NOMBRES ,AFC.DESCRIPCION_FORMA_CONTACTO,IPFC.VALOR FROM (
            SELECT DISTINCT IPER.* FROM DB_GENERAL.ADMI_ROL AR,ADMI_TIPO_ROL ATR,DB_GENERAL.INFO_EMPRESA_ROL IER ,
            INFO_PERSONA_EMPRESA_ROL IPER,INFO_PERSONA_CONTACTO IPC,INFO_PUNTO IP
            WHERE AR.DESCRIPCION_ROL like '%Contacto%'
            AND AR.TIPO_ROL_ID=ATR.ID_TIPO_ROL AND ATR.DESCRIPCION_TIPO_ROL=:strTipoRol  
            AND IER.ROL_ID IN AR.ID_ROL AND IER.EMPRESA_COD=10 AND
            IER.EMPRESA_COD=10 AND
            IP.LOGIN=:strLogin  AND 
            IP.PERSONA_EMPRESA_ROL_ID=IPC.PERSONA_EMPRESA_ROL_ID AND 
            IPC.ESTADO!='Eliminado' AND
            IPC.PERSONA_ROL_ID=IPER.ID_PERSONA_ROL) DATOS,DB_GENERAL.ADMI_ROL AR2, INFO_PERSONA IP2,DB_GENERAL.INFO_EMPRESA_ROL IER2,
            INFO_PERSONA_FORMA_CONTACTO IPFC, ADMI_FORMA_CONTACTO AFC
            WHERE IP2.ID_PERSONA=DATOS.PERSONA_ID AND
            IER2.ID_EMPRESA_ROL=DATOS.EMPRESA_ROL_ID AND
            IER2.ROL_ID=AR2.ID_ROL AND 
            IP2.ID_PERSONA=IPFC.PERSONA_ID AND
            DATOS.PERSONA_ID=IPFC.PERSONA_ID AND
            AFC.ID_FORMA_CONTACTO=IPFC.FORMA_CONTACTO_ID AND
            AFC.ESTADO='Activo' AND
            IPFC.FORMA_CONTACTO_ID IN 
            (SELECT AFC2.ID_FORMA_CONTACTO FROM ADMI_FORMA_CONTACTO AFC2 WHERE AFC2.DESCRIPCION_FORMA_CONTACTO LIKE '%Telefono%')
            UNION
            
            SELECT AR2.DESCRIPCION_ROL,(IP2.NOMBRES||' '||IP2.APELLIDOS) NOMBRES,AFC.DESCRIPCION_FORMA_CONTACTO,IPFC.VALOR FROM (
            SELECT DISTINCT IPER.*,IP.ID_PUNTO, IPC.CONTACTO_ID FROM DB_GENERAL.ADMI_ROL AR,ADMI_TIPO_ROL ATR,DB_GENERAL.INFO_EMPRESA_ROL IER ,
            INFO_PERSONA_EMPRESA_ROL IPER,INFO_PUNTO_CONTACTO IPC,INFO_PUNTO IP
            WHERE AR.DESCRIPCION_ROL like '%Contacto%'
            AND AR.TIPO_ROL_ID=ATR.ID_TIPO_ROL AND ATR.DESCRIPCION_TIPO_ROL=:strTipoRol  
            AND IER.ROL_ID IN AR.ID_ROL AND IER.EMPRESA_COD=10 AND
            IER.EMPRESA_COD=10 AND
            IP.LOGIN=:strLogin  AND 
            IPER.ID_PERSONA_ROL=IPC.PERSONA_EMPRESA_ROL_ID AND 
            IPC.ESTADO!='Eliminado' AND
            IPC.PUNTO_ID=IP.ID_PUNTO) DATOS,DB_GENERAL.ADMI_ROL AR2, INFO_PERSONA IP2,DB_GENERAL.INFO_EMPRESA_ROL IER2,
            INFO_PERSONA_FORMA_CONTACTO IPFC, ADMI_FORMA_CONTACTO AFC
            WHERE IP2.ID_PERSONA=DATOS.PERSONA_ID AND
            IER2.ID_EMPRESA_ROL=DATOS.EMPRESA_ROL_ID AND
            IER2.ROL_ID=AR2.ID_ROL AND 
            IP2.ID_PERSONA=DATOS.PERSONA_ID AND
            IPFC.PERSONA_ID=DATOS.CONTACTO_ID AND
            AFC.ID_FORMA_CONTACTO=IPFC.FORMA_CONTACTO_ID AND
            AFC.ESTADO='Activo' AND
            IPFC.FORMA_CONTACTO_ID IN (SELECT AFC2.ID_FORMA_CONTACTO FROM ADMI_FORMA_CONTACTO AFC2 
            WHERE AFC2.DESCRIPCION_FORMA_CONTACTO LIKE '%Telefono%')) ";


        $objRsm->addScalarResult('DESCRIPCION_ROL', 'formaContacto', 'string');
        $objRsm->addScalarResult('NOMBRES', 'nombre', 'string');
        $objRsm->addScalarResult('VALOR', 'valor', 'string');

        if( ( isset($strLogin) && !empty($strLogin) ) && ( isset($strTipoRol) && !empty($strTipoRol) ) )
        {
            $objQuery->setParameter('strLogin',  $strLogin);
            $objQuery->setParameter('strTipoRol',  $strTipoRol);
            $objQuery->setSQL($strQuery);
            $arrayDatos = $objQuery->getArrayResult();
        }
        return $arrayDatos;
    }

}
