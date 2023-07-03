<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class InfoPersonaEmpresaRolRepository extends BaseRepository
{
    /**
     * getPersonasProveedorVentaExterna
     * 
     * Obtiene Proveedores Externos identificados por el ROL -> Venta Externa, por Empresa y por Estado
     * 
     * costoQuery: 23
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 17-02-2017
     * 
     * @param  array $arrayParametros [
     *                                  "strPrefijoEmpresa" : Prefijo de la empresa
     *                                  "strDescripRol"     : Descripcion del Rol,
     *                                  "strEstado"         : Estado de la persona empresa rol
     *                                ]     
     * 
     * @return $arrayResultado 
     */
    public function getPersonasProveedorVentaExterna($arrayParametros)
    {
        $strSqlDatos = "SELECT per
                        FROM 
                        schemaBundle:InfoPersona p,
                        schemaBundle:InfoPersonaEmpresaRol per,
                        schemaBundle:InfoEmpresaRol er,
                        schemaBundle:InfoEmpresaGrupo eg,
                        schemaBundle:AdmiRol rol,
                        schemaBundle:AdmiTipoRol trol
                        WHERE 
                        per.personaId               = p.id 
                        AND per.empresaRolId        = er.id 
                        AND er.empresaCod           = eg.id 
                        AND er.rolId                = rol.id 
                        AND rol.tipoRolId           = trol.id    
                        AND eg.prefijo              =:strPrefijoEmpresa
                        AND trol.descripcionTipoRol =:strTipoRol 
                        AND rol.descripcionRol      =:strDescripRol                         
                        AND per.estado              =:strEstado
                        ORDER BY per.id ASC";
        
        $strQueryDatos = $this->_em->createQuery($strSqlDatos);        
        $strQueryDatos->setParameter('strPrefijoEmpresa', $arrayParametros['strPrefijoEmpresa']);
        $strQueryDatos->setParameter('strDescripRol', $arrayParametros['strDescripRol']);
        $strQueryDatos->setParameter('strTipoRol', 'Proveedor');
        $strQueryDatos->setParameter('strEstado', $arrayParametros['strEstado']);
        
        $arrayResultado = $strQueryDatos->getResult();
        
        return $arrayResultado;
    }

    /**
     * Documentación para el método 'findByIdentificacionTipoRolEmpresa'.
     *
     * Busca un empresa rol id por identificacion descripcionTipoRol y codEmpresa 
     *
     * @param mixed $identificacion
     * @param mixed $descRol 
     * @param mixed $codEmpresa
     *
     * @return InfoPersonaEmpresaRol $InfoPersonaEmpresaRol
     *
     * @author Kenneth Jimenez <kjimenez.ec>
     * @version 1.0 03-10-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 09-04-2015
     * @since 1.0
     */
    public function findByIdentificacionTipoRolEmpresa($identificacion, $descRol, $codEmpresa)
    {
        $query_string = "SELECT per
                        FROM 
                            schemaBundle:InfoPersona ip,
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol
                        WHERE 
                            per.empresaRolId= er.id AND
                            er.rolId = rol.id AND
                            rol.tipoRolId= trol.id AND
                            per.personaId = ip.id AND
                            ip.identificacionCliente = :identificacion AND
                            trol.descripcionTipoRol IN (:descRol) AND
                            er.empresaCod = :codEmpresa 
                            AND per.estado = :estado"
        ;
        $query = $this->_em->createQuery($query_string);
        $query->setParameter('identificacion', $identificacion);
        $query->setParameter('descRol', $descRol);
        $query->setParameter('codEmpresa', $codEmpresa);
        $query->setParameter('estado', "Activo");
        $InfoPersonaEmpresaRol = $query->getOneOrNullResult();

        return $InfoPersonaEmpresaRol;
    }


    /**
     * Costo: 10
     *
     * getEmpleadosWebService
     *
     * Esta función se encarga de retornar información de los empleados la cual es llamada mediante un método Web Service
     *
     * @param array $arrayParametros[ 'strTipoRol' => tipo de rol: Empleado,Cliente,etc.
     *                                'strEstado'  => Activo
     *                                'strLogin'   => login del empleado ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 23-10-2018
     *
     * @return array $arrayPuntos
     */
    public function getEmpleadosWebService($arrayParametros)
    {
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);

        try
        {
            $strSql = " SELECT
                            IP.NOMBRES,
                            IP.APELLIDOS,
                            (SELECT AD.NOMBRE_DEPARTAMENTO FROM ADMI_DEPARTAMENTO AD WHERE AD.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID ) DEPARTAMENTO,
                            AR.DESCRIPCION_ROL PERFIL,
                            (SELECT IEG.NOMBRE_EMPRESA FROM INFO_EMPRESA_GRUPO IEG WHERE IEG.COD_EMPRESA = IOG.EMPRESA_ID) EMPRESA,
                            (SELECT AC.NOMBRE_CANTON FROM ADMI_CANTON AC WHERE AC.ID_CANTON = IOG.CANTON_ID) CIUDAD,

                            DECODE(
                            (SELECT NOMBRE_AREA FROM ADMI_AREA WHERE ID_AREA =
                            (SELECT AD.AREA_ID FROM ADMI_DEPARTAMENTO AD WHERE AD.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID)),
                            'Tecnica.','TECNICA',
                              (SELECT NOMBRE_AREA FROM ADMI_AREA WHERE ID_AREA =
                              (SELECT AD.AREA_ID FROM ADMI_DEPARTAMENTO AD WHERE AD.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID))) AREA

                            FROM INFO_PERSONA IP,INFO_PERSONA_EMPRESA_ROL IPER,INFO_EMPRESA_ROL IER,ADMI_ROL AR,ADMI_TIPO_ROL ATR,
                            INFO_OFICINA_GRUPO IOG
                            WHERE IP.ID_PERSONA = IPER.PERSONA_ID
                            AND IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                            AND IER.ROL_ID = AR.ID_ROL
                            AND AR.TIPO_ROL_ID = ATR.ID_TIPO_ROL
                            AND IPER.OFICINA_ID = IOG.ID_OFICINA
                            AND IP.login = :paramLogin
                            AND ATR.DESCRIPCION_TIPO_ROL = :paramTipoRol
                            AND IPER.ESTADO = :paramEstado ";

            $objRsmb->addScalarResult('NOMBRES', 'nombres', 'string');
            $objRsmb->addScalarResult('APELLIDOS', 'apellidos', 'string');
            $objRsmb->addScalarResult('DEPARTAMENTO', 'departamento', 'string');
            $objRsmb->addScalarResult('PERFIL', 'perfil', 'string');
            $objRsmb->addScalarResult('EMPRESA', 'empresa', 'string');
            $objRsmb->addScalarResult('CIUDAD', 'ciudad', 'string');
            $objRsmb->addScalarResult('AREA', 'area', 'string');

            $objQuery->setParameter("paramTipoRol", $arrayParametros["strTipoRol"]);
            $objQuery->setParameter("paramEstado", $arrayParametros["strEstado"]);
            $objQuery->setParameter("paramLogin", $arrayParametros["strLogin"]);

            $objQuery->setSQL($strSql);

            $arrayClientes = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }

        return $arrayClientes;
    }


    /**
     * Costo: 720
     *
     * getEmpleadosPorEmpresa
     *
     * Esta función retorna los empleados por empresa
     *
     * @param array $arrayParametros[ 'strEstado'     => estado de la persona empresa rol
     *                                'strTipoRol'    => tipo rol
     *                                'strCodEmpresa' => codigo de la empresa
     *                                'strNombres'    => nombres del empleado ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 21-05-2019
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.1 09-12-2022  Se ordenan los resultados en relación ascendente por nombres
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 1.2 01-06-2023  Se ordenan los resultados en relación ascendente por nombres al momento de
     *                          filtrar por nombres o apellidos del empleado.
     *
     * @return array $arrayEmpleados [ 'idPersonaEmpresaRol' => id persona empresa rol
     *                                 'nombresEmpleado'     => nombres del empleado ]
     */
    public function getEmpleadosPorEmpresa($arrayParametros)
    {
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
        $strWhere = "";
        $strOrderBy =" ORDER BY INFOPERSONA.NOMBRES ASC";

        $strSql = " SELECT

                        INFOPERSONAEMPRESAROL.ID_PERSONA_ROL,
                        (SELECT NOMBRES ||' '||APELLIDOS FROM INFO_PERSONA WHERE ID_PERSONA = INFOPERSONAEMPRESAROL.PERSONA_ID) NOMBRES

                        FROM
                        INFO_PERSONA INFOPERSONA,
                        INFO_PERSONA_EMPRESA_ROL INFOPERSONAEMPRESAROL,
                        INFO_EMPRESA_ROL INFOEMPRESAROL,
                        INFO_EMPRESA_GRUPO INFOEMPRESAGRUPO,
                        ADMI_ROL ADMIROL,
                        ADMI_TIPO_ROL ADMITIPOROL

                        WHERE INFOPERSONA.ID_PERSONA = INFOPERSONAEMPRESAROL.PERSONA_ID
                        AND INFOPERSONAEMPRESAROL.EMPRESA_ROL_ID = INFOEMPRESAROL.ID_EMPRESA_ROL
                        AND INFOEMPRESAGRUPO.COD_EMPRESA = INFOEMPRESAROL.EMPRESA_COD
                        AND ADMIROL.ID_ROL = INFOEMPRESAROL.ROL_ID
                        AND ADMITIPOROL.ID_TIPO_ROL = ADMIROL.TIPO_ROL_ID
                        AND INFOPERSONAEMPRESAROL.ESTADO = :estadoEmpresaRol
                        AND ADMITIPOROL.DESCRIPCION_TIPO_ROL = :tipoRol
                        AND INFOEMPRESAGRUPO.COD_EMPRESA = :codEmpresa ";                        

        if(!empty($arrayParametros["strNombres"]))
        {
            $strWhere = "AND (LOWER(CONCAT(INFOPERSONA.NOMBRES, CONCAT(' ', INFOPERSONA.APELLIDOS))) like LOWER(:varNombre))";
            $objQuery->setParameter("varNombre", '%' . $arrayParametros["strNombres"] . '%');
        }

        $objRsmb->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRol', 'string');
        $objRsmb->addScalarResult('NOMBRES', 'nombresEmpleado', 'string');

        $objQuery->setParameter("estadoEmpresaRol", $arrayParametros["strEstado"]);
        $objQuery->setParameter("tipoRol", $arrayParametros["strTipoRol"]);
        $objQuery->setParameter("codEmpresa", $arrayParametros["strCodEmpresa"]);

        $strSql = $strSql . $strWhere . $strOrderBy;

        $objQuery->setSQL($strSql);

        $arrayEmpleados = $objQuery->getResult();

        return $arrayEmpleados;
    }


    public function findClientesXEmpresaEstado()
    {
        return $qb=$this->createQueryBuilder('iper','ip')
            ->select('ip')
            ->innerJoin('iper.personaId', 'ip')
            ->innerJoin('iper.empresaRolId', 'ier')
            ->innerJoin('ier.rolId', 'ar')
            ->innerJoin('ar.tipoRolId', 'atr')
            ->where("atr.descripcionTipoRol ='Cliente'");

        //->getDQL()
        //print_r($qb);
    }

     /**
     * getResultadoJefeDepartamentoEmpresa
     *
     * Esta funcion ejecuta el Query que retorna los jefes de un departamento por empresa
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-02-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-06-2018 - Se agrega validacion para obtener el jefe por region
     *
     * @param int    $intIdDepartamento
     * @param string $strCodEmpresa
     * @param string $strPorRegion
     *
     * @return array $strDatos
     */
    public function getResultadoJefeDepartamentoEmpresa($intIdDepartamento, $strCodEmpresa, $strPorRegion)
    {
        $objRsmb            = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null,$objRsmb);
        $strNombreParametro = "WEB SERVICE TAREAS";
        $strDescripcion     = "ROLES_CONSIDERAR";
        $strDatos           = array();
        $strSql = "SELECT infoPersona.ID_PERSONA ID_PERSONA, infoPersona.NOMBRES||' '||infoPersona.APELLIDOS NOMBRE_COMPLETO,
                infoPersonaEmpresaRol.ID_PERSONA_ROL ID_PERSONA_ROL
                FROM Admi_Departamento admiDepartamento,Info_Persona_Empresa_Rol infoPersonaEmpresaRol,
                Info_Empresa_Rol infoEmpresaRol,Admi_Rol admiRol,Info_Persona infoPersona
                WHERE admiDepartamento.ID_DEPARTAMENTO = infoPersonaEmpresaRol.DEPARTAMENTO_ID
                AND infoPersonaEmpresaRol.EMPRESA_ROL_ID = infoEmpresaRol.ID_EMPRESA_ROL
                AND infoEmpresaRol.ROL_ID = admiRol.ID_ROL
                AND infoPersona.ID_PERSONA = infoPersonaEmpresaRol.PERSONA_ID
                AND admiDepartamento.ID_DEPARTAMENTO = :varDepartamento
                AND infoPersonaEmpresaRol.ESTADO = :varEstado
                AND admiDepartamento.EMPRESA_COD = :varEmpresaCod
                AND admiRol.ES_JEFE = :varEsJefe
                AND infoPersonaEmpresaRol.EMPRESA_ROL_ID IN ( SELECT infoEmpresaRo.ID_EMPRESA_ROL FROM Info_Empresa_Rol infoEmpresaRo
                WHERE infoEmpresaRo.ID_EMPRESA_ROL IN (
                        SELECT COALESCE(TO_NUMBER(REGEXP_SUBSTR(admiParametroDet.VALOR1,'^\d+')),0) FROM Admi_Parametro_Det admiParametroDet
                            WHERE admiParametroDet.PARAMETRO_ID = ( SELECT admiParametroCab.ID_PARAMETRO FROM Admi_Parametro_Cab admiParametroCab
                                                                        WHERE admiParametroCab.NOMBRE_PARAMETRO = :varNombreParametro
                                                                            AND admiParametroDet.DESCRIPCION = :varDescripcion ))) ";

        if($strPorRegion == "R1" || $strPorRegion == "R2")
        {
            $strSql = $strSql . ' AND infoPersonaEmpresaRol.OFICINA_ID IN (SELECT infoOficinaGrupo.ID_OFICINA
                                    FROM Info_Oficina_Grupo infoOficinaGrupo
                                WHERE infoOficinaGrupo.CANTON_ID IN (SELECT admiCanton.ID_CANTON
                                  FROM Admi_Canton admiCanton WHERE admiCanton.REGION = :varRegion )) ';

            $objQuery->setParameter("varRegion", $strPorRegion);
        }

        $objQuery->setParameter("varNombreParametro", $strNombreParametro);
        $objQuery->setParameter("varDescripcion", $strDescripcion);
        $objQuery->setParameter("varDepartamento", $intIdDepartamento);
        $objQuery->setParameter("varEstado", "Activo");
        $objQuery->setParameter("varEmpresaCod", $strCodEmpresa);
        $objQuery->setParameter("varEsJefe", "S");

        $objRsmb->addScalarResult('ID_PERSONA', 'idPersona', 'integer');
        $objRsmb->addScalarResult('NOMBRE_COMPLETO', 'nombreCompleto', 'string');
        $objRsmb->addScalarResult('ID_PERSONA_ROL', 'personaEmpresaRolId', 'integer');

        $objQuery->setSQL($strSql);
        $strDatos = $objQuery->getResult();

        return $strDatos[0];
    }

    public function getPersonaByRoles($idsRoles = null)
    {
        if($idsRoles && count($idsRoles) > 0)
        {
            $string_roles_implode = implode("', '", $idsRoles);
            $string_roles = "'" . $string_roles_implode . "'";

            $query_string = "SELECT per 
                            FROM schemaBundle:InfoPersonaEmpresaRol per 
                            JOIN per.empresaRolId er 
                            WHERE er.rolId IN ($string_roles) 
                            ";
            return $this->_em->createQuery($query_string)->getResult();
        }
        else
            return false;
    }

    public function getPersonaByRoles2($idsRoles = null)
    {
        if($idsRoles && count($idsRoles) > 0)
        {
            $string_roles_implode = implode("', '", $idsRoles);
            $string_roles = "'" . $string_roles_implode . "'";

            $query_string = "SELECT p  
                            FROM schemaBundle:InfoPersonaEmpresaRol per,  
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:InfoPersona p 
                            WHERE per.empresaRolId = er.id 
                            AND per.personaId = p.id
                            AND er.rolId IN ($string_roles) 
                            ORDER BY p.nombres, p.apellidos ASC 
                            ";
            return $this->_em->createQuery($query_string)->getResult();
        }
        else
            return false;
    }

    /**
     * getResultadoPersonaEmpresaRol, obtiene informacion de la persona por rol, tipo rol, empresa
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              '(array)'                    => (array) Para todos los array's que contengan los elementos.
     *                                                              [
     *                                                              'strComparador' => Simbolo con el que se quiere realizar la busqueda (IN, =, LIKE)
     *                                                              'arrayEstado'   => Recibe el estado.
     *                                                              ]

     *                              'intStart'                   => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                   => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getResultadoPersonaEmpresaRol($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(iper.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT iper.id intIdPersonaEmpresaRol, "
                            . "iper.estado strEstadoIPER, "
                            . "iper.usrCreacion strUsrCreacionIPER, "
                            . "iper.feCreacion dateFeCreacionIPER, "
                            . "ipr.id intIdPersona, "
                            . "ipr.nombres strNombres, "
                            . "ipr.apellidos strApellidos, "
                            . "ipr.razonSocial strRazonSocial, "
                            . "ipr.representanteLegal strRepresentanteLegal, "
                            . "ipr.identificacionCliente strIdentificacionCliente, "
                            . "ipr.estado strEstadoPersona, "
                            . "ipr.usrCreacion strUsrCreacionIPR, "
                            . "ipr.feCreacion dateFeCreacionIPR, "
                            . "atr.id intIdTipoRol, "
                            . "atr.descripcionTipoRol strDescripcionTipoRol, "
                            . "atr.estado strEstadoTipoRol, "
                            . "atr.usrCreacion strUsrCreacionATR, "
                            . "atr.feCreacion dateFeCreacionATR, "
                            . "ar.id intIdRol, "
                            . "ar.descripcionRol strDescripcionRol, "
                            . "ar.estado strEstadoRol,"
                            . "ar.usrCreacion strUsrCreacionAR, "
                            . "ar.feCreacion dateFeCreacionAR, "
                            . "ier.id intIdEmpresaRol,"
                            . "ier.estado strEstadoEmpresaRol,"
                            . "ier.usrCreacion strUsrCreacionIER, "
                            . "ier.feCreacion dateFeCreacionIER ";

            $strFromQuery = "FROM schemaBundle:InfoPersona ipr, "
                                . " schemaBundle:InfoPersonaEmpresaRol iper, "
                                . " schemaBundle:InfoEmpresaRol ier, "
                                . " schemaBundle:AdmiRol ar, "
                                . " schemaBundle:AdmiTipoRol atr "
                                . " WHERE atr.id        = ar.tipoRolId "
                                . " AND ar.id           = ier.rolId "
                                . " AND ier.id          = iper.empresaRolId "
                                . " AND iper.personaId  = ipr.id ";

            //Pregunta si $arrayParametros['arrayEstadoATR']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEstadoATR']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEstadoATR']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoATR';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEstadoATR']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoATR', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoATR', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEstadoAR']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEstadoAR']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ar.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEstadoAR']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoAR';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEstadoAR']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoAR', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoAR', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEstadoIER']['strComparador'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEstadoIER']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEstadoIER']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoIER';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEstadoIER']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIER', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEstadoIPER']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEstadoIPER']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' iper.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEstadoIPER']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoIPER';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEstadoIPER']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPER', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPER', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEstadoIPR']['arrayEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEstadoIPR']['arrayEstado']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipr.estado ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEstadoIPR']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEstadoIPR';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEstadoIPR']['arrayEstado'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEstadoIPR', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEstadoIPR', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayDescripcionTipoRol']['arrayDescripcionTipoRol'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayDescripcionTipoRol']['arrayDescripcionTipoRol']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' atr.descripcionTipoRol ';
                $arrayParams['strComparador']   = $arrayParametros['arrayDescripcionTipoRol']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayDescripcionTipoRol';
                $arrayParams['arrayValue']      = $arrayParametros['arrayDescripcionTipoRol']['arrayDescripcionTipoRol'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayDescripcionTipoRol', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayEmpresaCod']['arrayEmpresaCod'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayEmpresaCod']['arrayEmpresaCod']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ier.empresaCod ';
                $arrayParams['strComparador']   = $arrayParametros['arrayEmpresaCod']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayEmpresaCod';
                $arrayParams['arrayValue']      = $arrayParametros['arrayEmpresaCod']['arrayEmpresaCod'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayEmpresaCod', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayEmpresaCod', $objReturnResponse->putTypeParamBind($arrayParams));
            }

            //Pregunta si $arrayParametros['arrayPersona']['arrayPersona'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['arrayPersona']['arrayPersona']))
            {
                $arrayParams                    = array();
                $arrayParams['strField']        = ' ipr.id ';
                $arrayParams['strComparador']   = $arrayParametros['arrayPersona']['strComparador'];
                $arrayParams['strBindParam']    = ':arrayPersona';
                $arrayParams['arrayValue']      = $arrayParametros['arrayPersona']['arrayPersona'];
                $strFromQuery                   .= $objReturnResponse->putWhereClause($arrayParams);
                $objQuery->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
                $objQueryCount->setParameter('arrayPersona', $objReturnResponse->putTypeParamBind($arrayParams));
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
            $objReturnResponse->setStrMessageStatus('Existion un error en getResultadoPersonaEmpresaRol - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    } //getResultadoPersonaEmpresaRol

    /**
     * getJSONPersonaEmpresaRol, obtiene informacion de la persona por rol, tipo rol, empresa retornando un json
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-05-2016
     * @since 1.0
     * 
     * @param array $arrayParametros[
     *                              '(array)'                    => (array) Para todos los array's que contengan los elementos.
     *                                                              [
     *                                                              'strComparador' => Simbolo con el que se quiere realizar la busqueda (IN, =, LIKE)
     *                                                              'arrayEstado'   => Recibe el estado.
     *                                                              ]
     *                              'intStart'                   => Recibe el inicio para el resultado de la busqueda.
     *                              'intLimit'                   => Recibe el fin para el resultado de la busqueda del query.
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con los registros
     */
    public function getJSONPersonaEmpresaRol($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        try
        {
            $objGetResult = $this->getResultadoPersonaEmpresaRol($arrayParametros);
            $jsonData     = json_encode(array('strStatus'           => $objReturnResponse::PROCESS_SUCCESS,
                                              'strMessageStatus'    => $objReturnResponse::MSN_PROCESS_SUCCESS,
                                              'total'               => $objGetResult->getTotal(),
                                              'encontrados'         => $objGetResult->getRegistros()));
        }
        catch(\Exception $ex)
        {
            $jsonData = json_encode(array('strStatus'           => $objReturnResponse::ERROR,
                                          'strMessageStatus'    => $objReturnResponse::MSN_ERROR . ' getJSONPersonaEmpresaRol '
                                          . $ex->getMessage()));
        }
        return $jsonData;
    } //getJSONPersonaEmpresaRol

    public function getPersonaEmpresaRolPorPersonaPorTipoRol($idPersona, $descRol, $codEmpresa)
    {
        $query_string = "SELECT per 
                FROM 
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoEmpresaRol er,
                schemaBundle:AdmiRol rol,
                schemaBundle:AdmiTipoRol trol
                WHERE 
                per.empresaRolId= er.id AND
                er.rolId = rol.id AND
                rol.tipoRolId= trol.id AND
                per.personaId = :idPersona AND
                trol.descripcionTipoRol = :descRol AND
                er.empresaCod = :codEmpresa"
        //." AND per.estado not in('Cancel','Cancelado','Anulado')"
        ;
        if(!empty($descRol) && $descRol == "Personal Externo")
        {
            $query_string .= " AND per.estado not in('Cancel','Cancelado','Anulado','Eliminado') ";
        }
        $query = $this->_em->createQuery($query_string);
        $query->setParameter('idPersona', $idPersona);
        $query->setParameter('descRol', $descRol);
        $query->setParameter('codEmpresa', $codEmpresa);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }

    /**
     * Método para obtener la persona empresa rol en estado Activo
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 15-07-2020
     * 
     */
    public function getPersonaEmpresaRolPorPersonaPorTipoRolNew($arrayParametrosPersonaEmpresaRol)
    {
        $strQuery = "SELECT per 
                FROM 
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoEmpresaRol er,
                schemaBundle:AdmiRol rol,
                schemaBundle:AdmiTipoRol trol
                WHERE 
                per.empresaRolId= er.id AND
                er.rolId = rol.id AND
                rol.tipoRolId= trol.id AND
                per.personaId = :idPersona AND
                trol.descripcionTipoRol = :descRol AND
                er.empresaCod = :codEmpresa"
        ." AND per.estado = :estado"
        ;
        $objQuery = $this->_em->createQuery($strQuery);
        $objQuery->setParameter('idPersona', $arrayParametrosPersonaEmpresaRol['intIdPersona']);
        $objQuery->setParameter('descRol', $arrayParametrosPersonaEmpresaRol['strDescRol']);
        $objQuery->setParameter('codEmpresa', $arrayParametrosPersonaEmpresaRol['intCodEmpresa']);
        $objQuery->setParameter('estado', "Activo");
        $arrayDatos = $objQuery->getOneOrNullResult();
        return $arrayDatos;
    }

     /**
     * Obtener el empresa rol id de la persona por tipo 
     * 
     * @author: Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 13-08-2018  
     * 
     * @author: Wilmer Vera <wvera@telconet.ec>
     * @version 1.2 Se agrega el filtro por estado activo y cancelado del cliente para el control de fibra
     * @since 19-12-2018 
     *  
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.3 Se quita el filtro por estado activo y cancelado del cliente para el control de fibra
     * @since 31-07-2019 
     * 
     * @author: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.4 Se reversa la versión 1.3
     * @since 01-08-2019 
     * 
     * @author: Wilmer Vera <wvera@telconet.ec>
     * @version 1.5 se elimina filtro por estado. 
     * @since  23-02-2021 
     * 
     * Costo: 7
     *  
     * @param array $arrayParametros
     * @return string $objPersonaEmpresaRol
     */ 
    
    public function getPersonaEmpresaRolPorPersonaPorRol($arrayParametros)
    {
            $objRsm              = new ResultSetMappingBuilder($this->_em);
            $objQuery            = $this->_em->createNativeQuery(null, $objRsm);
            $objPersonaEmpresaRol= "";      
            $strWhere = "";
            
            if($arrayParametros['tipoActividad'] != null && isset($arrayParametros['tipoActividad']) && !empty($arrayParametros['tipoActividad']) &&
                $arrayParametros['tipoActividad'] == "Retiro")
            {
                $strWhere =  " AND per.FE_CREACION = (
                                    SELECT max(per2.fe_creacion)
                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per2
                                    WHERE per2.PERSONA_ID = per.PERSONA_ID
                                    and per2.EMPRESA_ROL_ID = per.EMPRESA_ROL_ID
                            ) ";            
            } 
            else 
            {
                
                $strWhere =  " AND per.ESTADO = :strEstado "; 
                $objQuery->setParameter('strEstado', 'Activo');
                
            }

            $strSql = " SELECT per.ID_PERSONA_ROL
                        FROM 
                            DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per
                        INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL er ON per.EMPRESA_ROL_ID=er.ID_EMPRESA_ROL
                        INNER JOIN DB_GENERAL.ADMI_ROL rol ON rol.ID_ROL=er.ROL_ID
                        INNER JOIN DB_GENERAL.ADMI_TIPO_ROL trol ON trol.ID_TIPO_ROL=rol.TIPO_ROL_ID
                        WHERE
                            per.persona_Id = :idPersona AND
                            trol.descripcion_Tipo_Rol = :descRol AND
                            er.empresa_Cod = :codEmpresa ".$strWhere. " AND ROWNUM <= 1 ";

            $objQuery->setParameter('idPersona',  $arrayParametros['idPersona']);
            $objQuery->setParameter('descRol', $arrayParametros['descripcion']);
            $objQuery->setParameter('codEmpresa', $arrayParametros['idEmpresa']);
           

            $objRsm->addScalarResult('ID_PERSONA_ROL',          'idPersonaRol',            'integer');
            $objQuery->setSQL($strSql);
            
            $objPersonaEmpresaRol = $objQuery->getOneOrNullResult(); 
       
        return $objPersonaEmpresaRol;
    }
    
    public function getPersonaEmpresaRolPorPersonaPorTipoRolParaConvertir($idPersona, $descRol, $codEmpresa)
    {
        $query_string = "SELECT per
                FROM 
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoEmpresaRol er,
                schemaBundle:AdmiRol rol,
                schemaBundle:AdmiTipoRol trol
                WHERE 
                per.empresaRolId= er.id AND
                er.rolId = rol.id AND
                rol.tipoRolId= trol.id AND
                per.personaId = :idPersona AND
                trol.descripcionTipoRol = :descRol AND
                er.empresaCod = :codEmpresa 
                AND per.estado in('Activo','Pend-convertir')"
        ;
        $query = $this->_em->createQuery($query_string);
        $query->setParameter('idPersona', $idPersona);
        $query->setParameter('descRol', $descRol);
        $query->setParameter('codEmpresa', $codEmpresa);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }

    public function getPersonaEmpresaRolPorPersonaPorTipoRolParaNew($personaId, $descRol, $codEmpresa)
    {
        $query = $this->_em->createQuery("SELECT per 
                FROM 
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoEmpresaRol er,
                schemaBundle:AdmiRol rol,
                schemaBundle:AdmiTipoRol trol
                WHERE 
                per.empresaRolId= er.id AND
                er.rolId = rol.id AND
                rol.tipoRolId= trol.id AND
                per.personaId = :personaId AND
                trol.descripcionTipoRol = :descRol AND
                er.empresaCod = :codEmpresa AND
                per.estado in('Activo','Pendiente')");
        $query->setParameter('personaId', $personaId);
        $query->setParameter('descRol', $descRol);
        $query->setParameter('codEmpresa', $codEmpresa);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }

    public function getPersonaEmpresaRolPorPersonaPorTipoRolTodos($personaId, $descRol, $empresa)
    {

        $query_string = "SELECT per 
                            FROM 
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol
                            WHERE 
                            per.empresaRolId= er.id AND
                            er.rolId = rol.id AND
                            rol.tipoRolId= trol.id AND
                            per.personaId=$personaId AND
                            trol.descripcionTipoRol = '$descRol' AND er.empresaCod='$empresa'"
        //." AND per.estado not in('Cancel','Cancelado','Anulado')"
        ;
        $query = $this->_em->createQuery($query_string);
        $datos = $query->getResult();
        //echo $query->getSQL();die;
        return $datos;
    }

    public function getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($personaId, $descRol, $empresa)
    {

        $query_string1 = "SELECT per 
                            FROM 
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol
                            WHERE 
                            per.empresaRolId= er.id AND
                            er.rolId = rol.id AND
                            rol.tipoRolId= trol.id AND
                            per.personaId=$personaId AND
                            trol.descripcionTipoRol = '$descRol' AND er.empresaCod='$empresa' AND 
							per.estado IN ('Activo','Pendiente','Pend-convertir') ";
        $query1 = $this->_em->createQuery($query_string1);
        //echo $query1->getSQL();die;
        $datos = $query1->getOneOrNullResult();
        //echo $query->getSQL();die;
        //print_r($datos);die;
        return $datos;
    }

    public function getPersonaEmpresaRolNoCancelPorPersonaPorTipoRol($personaId, $descRol, $empresa)
    {

        $query_string = "SELECT per 
                            FROM 
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol
                            WHERE 
                            per.empresaRolId= er.id AND
                            er.rolId = rol.id AND
                            rol.tipoRolId= trol.id AND
                            per.personaId=$personaId AND
                            trol.descripcionTipoRol = '$descRol' AND er.empresaCod='$empresa'"
            . " AND per.estado not in('Cancel','Cancelado','Anulado')"
        ;
        $query = $this->_em->createQuery($query_string);
        $datos = $query->getOneOrNullResult();
        //echo $query->getSQL();die;
        return $datos;
    }

    /**
     * Documentación para el método 'getEmpresasByPersona'.
     *
     * Muestra la información de las empresas dependiendo del usuario en sessión
     * 
     * @return array $datos 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-06-2017 - Se modifica para que retorne la información geográfica correspondiente al país, región, provincia y ciudad a la que
     *                           pertenece la persona en sessión
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 07-01-2021 - Se agrega el order by por el id persona empresa rol.
     */
    public function getEmpresasByPersona($userPersona, $descRol)
    {
        $query_string = "SELECT eg.id as CodEmpresa,
                            eg.razonSocial,
                            og.id as IdOficina,
                            og.nombreOficina,
                            d.id as IdDepartamento,
                            d.nombreDepartamento,
                            per.id as IdPersonaEmpresaRol,
                            eg.prefijo,
                            aps.id as idPais,
                            aps.nombrePais,
                            ar.id as idRegion,
                            ar.nombreRegion,
                            ac.id as idCanton,
                            ac.nombreCanton,
                            ap.id as idProvincia,
                            ap.nombreProvincia,
                            eg.facturaElectronico,
                            eg.nombreEmpresa
                         FROM 
                            schemaBundle:InfoPersona p,
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:InfoOficinaGrupo og,
                            schemaBundle:InfoEmpresaGrupo eg,
                            schemaBundle:AdmiRol r,
                            schemaBundle:AdmiTipoRol tr,
                            schemaBundle:AdmiDepartamento d,
                            schemaBundle:AdmiCanton ac,
                            schemaBundle:AdmiProvincia ap,
                            schemaBundle:AdmiRegion ar,
                            schemaBundle:AdmiPais aps
                         WHERE
                            p.id = per.personaId AND
                            per.empresaRolId = er.id AND
                            per.oficinaId = og.id AND
                            er.empresaCod = eg.id AND
                            og.empresaId = eg.id AND
                            er.rolId = r.id AND
                            r.tipoRolId= tr.id AND
                            per.departamentoId= d.id AND
                            ac.id = og.cantonId AND
                            ac.provinciaId = ap.id AND
                            ap.regionId = ar.id AND
                            ar.paisId = aps.id AND
                            p.login = :userPersona AND
                            tr.descripcionTipoRol = :descRol AND
        
                            per.estado = 'Activo'
                         ORDER BY per.id ASC
                        ";

        $query = $this->_em->createQuery($query_string);
        $query->setParameter('userPersona', $userPersona);
        $query->setParameter('descRol', $descRol);
        $datos = $query->getResult();
        //echo $query->getSQL();
        //die();
        return $datos;
    }

    public function getPersonaEmpresaRolPorPersonaPorEmpresa($personaId, $empresa)
    {

        $query_string = "SELECT per 
                            FROM 
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er
                            WHERE 
                            per.empresaRolId= er.id AND
                            per.personaId=$personaId AND
                            er.empresaCod='$empresa'";
        $query = $this->_em->createQuery($query_string);
        $datos = $query->getResult();
        //echo $query->getSQL();
        return $datos;
    }

    public function getPersonaEmpresaRolPorPersonaPorEmpresaActivos($personaId, $empresa)
    {
        $query_string = "SELECT per 
                        FROM 
                        schemaBundle:InfoPersonaEmpresaRol per,
                        schemaBundle:InfoEmpresaRol er
                        WHERE 
                        per.empresaRolId= er.id AND
                        per.personaId = :personaId AND
                        per.estado in ('Activo','Pendiente','Pend-convertir') AND
                        er.empresaCod = :empresa";
        $query = $this->_em->createQuery($query_string);
        $query->setParameter('personaId', $personaId);
        $query->setParameter('empresa', $empresa);
        $datos = $query->getResult();
        //echo $query->getSQL();
        return $datos;
    }

    /**
     * getPersonaEmpresaRolPorIdPersonaYEmpresa - Funcion que obtiene el personaEmpresaRol segun el ID_PERSONA y
     *                                            COD_EMPRESA
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 17-10-2016
     *
     * @param array $arrayParametros[ string  strCodigoEmpresa,
     *                                integer intPersonaId
     *                              ]
     *
     * @return int $intPersonaEmpresaRolId
     */
    public function getPersonaEmpresaRolPorIdPersonaYEmpresa($arrayParametros)
    {
        $objQuery   = $this->_em->createQuery();
        $strEstado  = "Activo";
        $strTipoRol = "Empleado";
        $strSQL     = " SELECT iper.id FROM schemaBundle:InfoPersonaEmpresaRol iper
                            WHERE iper.oficinaId in (SELECT iog.id FROM schemaBundle:InfoOficinaGrupo iog
                                                        WHERE iog.empresaId = :paramCodEmpresa)
                            AND iper.estado = :paramEstado
                            AND iper.personaId = :paramPersona
                            AND iper.empresaRolId IN (SELECT ier.id FROM schemaBundle:InfoEmpresaRol ier WHERE ier.rolId IN
                                (SELECT ar.id FROM schemaBundle:AdmiRol ar WHERE ar.tipoRolId IN
                                (SELECT atr.id FROM schemaBundle:AdmiTipoRol atr WHERE atr.descripcionTipoRol = :paramTipoRol))
                                AND ier.empresaCod = :paramCodEmpresa) ";

        $objQuery->setParameter('paramCodEmpresa', $arrayParametros["strCodigoEmpresa"]);
        $objQuery->setParameter('paramEstado', $strEstado);
        $objQuery->setParameter('paramTipoRol', $strTipoRol);
        $objQuery->setParameter('paramPersona', $arrayParametros["intPersonaId"]);

        $objQuery->setDQL($strSQL);

        $intPersonaEmpresaRolId = $objQuery->getSingleScalarResult();

        return $intPersonaEmpresaRolId;
    }

    public function generarArrayDepartamentosXOficina($id_oficina)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('entidad')
            ->from('schemaBundle:InfoPersonaEmpresaRol', 'entidad')
            ->where('entidad.oficinaId = ?1')
            ->setParameter(1, $id_oficina)
            ->andWhere("entidad.estado not like 'Eliminado'");

        $query = $qb->getQuery();

        $results = $query->getResult();

        $array = array();
        foreach($results as $data)
        {
            if($this->verificarArray($array, $data->getDepartamentoId()))
                $array[] = $data->getDepartamentoId();
        }
        return $array;
    }

    public function verificarArray($array, $id)
    {

        foreach($array as $data)
        {
            if($data == $id)
                return false;
        }
        return true;
    }

    /**
     * generarJsonEmpleadosXDepartamento
     *
     * Funcion que retorna los empleados asociados a un departamento, en la asignacion de una tarea
     *
     * @param number  $id_departamento
     * @param number  $id_oficina
     * @param string  $nombre
     * @param boolean $soloJefes
     * @param boolean $retornaIdPersonaEmpresaRol
     * @param string  $codEmpresa
     * @param array   $cantonesArray
     * @param string  $esMD
     * @param number  $id_canton
     *
     * @version 1.0 Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 24-03-2016 Se realizan ajustes de parametrizar query (parametros que llegan al metodo y los predefinidos internamente)
     *
     * @return $resultado
     */
    public function generarJsonEmpleadosXDepartamento($intIdDepartamento = "", 
                                                        $intIdOficina = "", $strNombre = "", $boolSoloJefes = false, 
                                                        $boolRetornaIdPersonaEmpresaRol = false, $strCodEmpresa = "", 
                                                        $arrayCantones = array(), $strEsMD = 'no', $intIdCanton = "")
    {
        $arr_encontrados = array();
        $query = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();

        $where = "";
        $from = "";
        if($strNombre && $strNombre != "")
        {
            $where .= "AND (LOWER(p.razonSocial) like LOWER(:varNombre) OR
                        LOWER(CONCAT(p.nombres, CONCAT(' ', p.apellidos))) like LOWER(:varNombre))";

            $query->setParameter('varNombre', '%' . $strNombre . '%');
            $queryCount->setParameter('varNombre', '%' . $strNombre . '%');
        }
        if($strEsMD == 'no')
        {
            if($intIdDepartamento && $intIdDepartamento != "" && is_numeric($intIdDepartamento))
            {
                $where .= "AND d.id = :departamento ";
                $query->setParameter('departamento', $intIdDepartamento);
                $queryCount->setParameter('departamento', $intIdDepartamento);
            }
            if(isset($strCodEmpresa) && $strCodEmpresa && $strCodEmpresa != "")
            {
                $where .= "AND eg.id = :codEmpresa ";
                $query->setParameter('codEmpresa', $strCodEmpresa);
                $queryCount->setParameter('codEmpresa', $strCodEmpresa);
            }
        }
        else
        {
            if($intIdDepartamento && $intIdDepartamento != "" && is_numeric($intIdDepartamento))
            {
                $where .= "AND d.id in ( :departamento )  ";
                $query->setParameter('departamento', $intIdDepartamento);
                $queryCount->setParameter('departamento', $intIdDepartamento);
            }
            if(isset($strCodEmpresa) && $strCodEmpresa && $strCodEmpresa != "")
            {            
                $where .= "AND eg.id in ( :codEmpresa )  ";
                $query->setParameter('codEmpresa', $strCodEmpresa);
                $queryCount->setParameter('codEmpresa', $strCodEmpresa);
                
            }
        }
        if($boolSoloJefes)
        {
            $where .= "AND lower(r.esJefe) = lower( :esJefe ) ";
            $query->setParameter('esJefe', 'S');
            $queryCount->setParameter('esJefe', 'S');
        }

        if($intIdOficina && $intIdOficina != "" && is_numeric($intIdOficina))
        {
            $where .= "AND og.id = :idOficina ";
            $query->setParameter('idOficina', $intIdOficina);
            $queryCount->setParameter('idOficina', $intIdOficina);
        }

        if($intIdCanton && $intIdCanton != "" && is_numeric($intIdCanton))
        {
            $where .= "AND ac.id = :idCanton and ac.id = og.cantonId  ";
            $from .= " , schemaBundle:AdmiCanton ac ";
            $query->setParameter('idCanton', $intIdCanton);
            $queryCount->setParameter('idCanton', $intIdCanton);
        }


        $boolCantones = false;
        if(isset($arrayCantones) && $arrayCantones && count($arrayCantones) > 0)
        {
            $boolTieneCantonesValidos = true;
            foreach($arrayCantones as $intElementoIdCanton)
            {
                if (!is_numeric($intElementoIdCanton))
                {
                    $boolTieneCantonesValidos = false;
                    break;
                }
            }
            
            if ($boolTieneCantonesValidos)
            {
                $boolCantones = true;
                $where .= "AND og.cantonId IN ( :cantones ) ";
                $query->setParameter('cantones', $arrayCantones);
                $queryCount->setParameter('cantones', $arrayCantones);
            }
        }

        $sqlCount = "SELECT COUNT( p.id ) as totalEmpleados ";

        $sqlReg = "SELECT p.id as idPersona, p.nombres, p.apellidos, per.id as idPersonaEmpresaRol ";

        $sqlTotal = " FROM
                      schemaBundle:InfoPersona p,
                      schemaBundle:InfoEmpresaGrupo eg,
                      schemaBundle:InfoOficinaGrupo og,
                      schemaBundle:InfoPersonaEmpresaRol per,
                      schemaBundle:InfoEmpresaRol er,
                      schemaBundle:AdmiDepartamento d,
                      schemaBundle:AdmiRol r,
                      schemaBundle:AdmiTipoRol tr
		$from
                WHERE per.personaId = p.id 
                AND per.empresaRolId = er.id 
                AND per.oficinaId = og.id 
                AND per.departamentoId = d.id 
                AND er.empresaCod = og.empresaId  
                AND er.empresaCod = eg.id 
                AND er.rolId = r.id 
                AND r.tipoRolId = tr.id 
                AND r.tipoRolId = tr.id 
                AND tr.descripcionTipoRol = 'Empleado'
                AND LOWER(eg.estado) not like LOWER('Eliminado') 
                AND LOWER(og.estado) not like LOWER('Eliminado') 
                AND LOWER(d.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Inactivo')
                AND LOWER(per.estado) not like LOWER('Cancelado')
                AND LOWER(er.estado) not like LOWER('Eliminado') 
                AND LOWER(r.estado) not like LOWER('Eliminado') 
                AND LOWER(tr.estado) not like LOWER('Eliminado') 
                $where 
				
				ORDER BY p.nombres, p.apellidos 
               ";

        $strQuery = $sqlReg . $sqlTotal;

        $query->setDQL($strQuery);
        $registros = $query->getResult();

        if($registros)
        {
            $strQueryCount = $sqlCount . $sqlTotal;

            $queryCount->setDQL($strQueryCount);

            $num = $queryCount->getSingleScalarResult();

            foreach($registros as $persona)
            {
                $intId = ($boolRetornaIdPersonaEmpresaRol) ? ($persona["idPersona"] . "@@" . $persona["idPersonaEmpresaRol"]) : $persona["idPersona"];

                $arr_encontrados[] = array('id_empleado' => $intId,
                    'nombre_empleado' => ucwords(strtolower(trim($persona["nombres"] . ' ' . $persona["apellidos"])))
                );
            }

            $dataF = json_encode($arr_encontrados);

            if($num <= 0)
            {
                if($boolCantones)
                {
                    $messageError = "No existen empleados asignados de este departamento en las ciudades correspondientes a los afectados.";
                }
                else
                {
                    if($boolSoloJefes)
                    {
                        $messageError = "No existen jefes asignados para este departamento.";
                    }
                    else
                    {
                        $messageError = "No existen empleados asignados para este departamento.";
                    }
                }

                $resultado = '{"result": {"total":"1", "encontrados":' . $dataF . '}, "myMetaData": {"boolSuccess": "0", "message":"' . $messageError . '"} }';
            }
            else
            {
                $resultado = '{"result": {"total":"' . $num . '","encontrados":' . $dataF . '}, "myMetaData": {"boolSuccess": "1", "message":""} }';
            }

            return $resultado;
        }
        else
        {
            $arr_encontrados[] = array('id_empleado' => '', 'nombre_empleado' => '');
            $dataF = json_encode($arr_encontrados);

            if($boolCantones)
            {
                $messageError = "No existen empleados asignados de este departamento en las ciudades correspondientes a los afectados.";
            }
            else
            {
                if($boolSoloJefes)
                {
                    $messageError = "No existen jefes asignados para este departamento.";
                }
                else
                {
                    $messageError = "No existen empleados asignados para este departamento.";
                }
            }

            $resultado = '{"result": {"total":"1", "encontrados":' . $dataF . '}, "myMetaData": {"boolSuccess": "0", "message":"' . $messageError . '"} }';
            return $resultado;
        }
    }

    /**
     * getJsonPersonaAfectada
     *
     * Esta funcion retorna la informacion del afectado que se selecciona al momento de crear el caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-01-2016
     *
     * @param integer  $idAfectado
     * @param String   $nombreAfectado
     * @param String   $codEmpresa
     *
     * @return JSON $resultado
     *
     */
    public function getJsonPersonaAfectada($idAfectado, $nombreAfectado, $codEmpresa)
    {
        $arr_encontrados = array();

        if($idAfectado)
        {

            $datosAfectado = $this->getRegistrosPersonaAfectada($idAfectado, $codEmpresa);

            if($datosAfectado)
            {
                $arr_encontrados[] = array('id_parte_afectada' => $idAfectado,
                    'nombre_parte_afectada' => $nombreAfectado,
                    'id_descripcion_1' => '',
                    'nombre_descripcion_1' => $datosAfectado['nombreDepartamento'],
                    'id_descripcion_2' => '',
                    'nombre_descripcion_2' => $datosAfectado['nombreCanton']);

                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"1","encontrados":' . $data . '}';
            }
            else
            {
                $resultado = '{"total":"0","encontrados":[]}';
            }
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    /**
     * getRegistrosPersonaAfectada
     *
     * Esta funcion retorna la informacion del afectado que se selecciona al momento de crear el caso
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-01-2016
     *
     * @param integer  $idAfectado
     * @param integer  $codEmpresa
     *
     * @return array $strDatos
     *
     */
    public function getRegistrosPersonaAfectada($idAfectado, $codEmpresa)
    {
        $strQuery = $this->_em->createQuery();
        $strSelect = " SELECT
                            infoPersona.id as id,admiDepartamento.nombreDepartamento as nombreDepartamento,admiCanton.nombreCanton as nombreCanton
                        FROM schemaBundle:InfoPersona infoPersona,schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol,
                            schemaBundle:InfoOficinaGrupo infoOficinaGrupo,schemaBundle:AdmiDepartamento admiDepartamento,
                            schemaBundle:AdmiCanton admiCanton
                       WHERE infoPersona.id = infoPersonaEmpresaRol.personaId
                       AND infoPersonaEmpresaRol.oficinaId = infoOficinaGrupo.id
                       AND infoPersonaEmpresaRol.departamentoId = admiDepartamento.id
                       AND infoOficinaGrupo.cantonId = admiCanton.id
                       AND infoOficinaGrupo.empresaId = :varCodEmpresa
                       AND infoPersona.id = :varAfectado
                       AND ( infoPersonaEmpresaRol.estado = :varEstado OR infoPersonaEmpresaRol.estado = :varEstado1) ";

        $strQuery->setParameter("varCodEmpresa", $codEmpresa);
        $strQuery->setParameter("varAfectado", $idAfectado);
        $strQuery->setParameter("varEstado", "Activo");
        $strQuery->setParameter("varEstado1", "Modificado");
        $strQuery->setDQL($strSelect);

        $arrayDatos = $strQuery->getOneOrNullResult();

        return $arrayDatos;
    }

    /**
     * getJsonPersonasPorDepartamentoAfectado
     *
     * Esta funcion retorna la informacion de los afectados de un departamento seleccionado
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-01-2016
     *
     * @param integer  $departamento
     * @param integer  $ciudad
     * @param String   $codEmpresa
     *
     * @return JSON $resultado
     *
     */
    public function getJsonPersonasPorDepartamentoAfectado($departamento, $ciudad, $codEmpresa)
    {
        $arr_encontrados = array();
        $total = "";
        $datos = array();
        if($departamento)
        {

            $datos = $this->getRegistrosPersonasPorDepartamentoAfectado($departamento, $ciudad, $codEmpresa);
            $datosAfectados = $datos["registros"];
            $total = $datos["total"];
            if($datosAfectados)
            {
                foreach($datosAfectados as $afectado)
                {
                    $arr_encontrados[] = array('id_parte_afectada' => $afectado['id'],
                        'nombre_parte_afectada' => $afectado['nombreEmpleado'],
                        'id_descripcion_1' => '',
                        'nombre_descripcion_1' => $afectado['nombreDepartamento'],
                        'id_descripcion_2' => '',
                        'nombre_descripcion_2' => $afectado['nombreCanton']);

                    $data = json_encode($arr_encontrados);
                    $resultado = '{"total":' . $total . ',"encontrados":' . $data . '}';
                }
            }
            else
            {
                $resultado = '{"total":"0","encontrados":[]}';
            }
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    /**
     * getRegistrosPersonasPorDepartamentoAfectado
     *
     * Esta funcion retorna la informacion de los afectados de un departamento seleccionado
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 05-01-2016
     *
     * @param integer $departamento
     * @param integer $ciudad
     * @param String  $codEmpresa
     *
     * @return array $strDatos
     *
     */
    public function getRegistrosPersonasPorDepartamentoAfectado($departamento, $ciudad, $codEmpresa)
    {
        $strQuery = $this->_em->createQuery();
        $strQueryCount = $this->_em->createQuery();
        $strSelect = " SELECT
                            infoPersona.id as id,CONCAT(infoPersona.nombres,CONCAT(' ',infoPersona.apellidos)) as nombreEmpleado,
                            admiDepartamento.nombreDepartamento as nombreDepartamento,admiCanton.nombreCanton as nombreCanton ";
        $strFrom = " FROM schemaBundle:InfoPersona infoPersona,schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol,
                            schemaBundle:InfoOficinaGrupo infoOficinaGrupo,schemaBundle:AdmiDepartamento admiDepartamento,
                            schemaBundle:AdmiCanton admiCanton
                       WHERE infoPersona.id = infoPersonaEmpresaRol.personaId
                       AND infoPersonaEmpresaRol.oficinaId = infoOficinaGrupo.id
                       AND infoPersonaEmpresaRol.departamentoId = admiDepartamento.id
                       AND infoOficinaGrupo.cantonId = admiCanton.id
                       AND infoOficinaGrupo.empresaId = :varCodEmpresa
                       AND infoPersonaEmpresaRol.departamentoId = :varDepartamento
                       AND admiCanton.id = :varCiudad
                       AND ( infoPersonaEmpresaRol.estado = :varEstado OR infoPersonaEmpresaRol.estado = :varEstado1) ";

        $strQuery->setParameter("varCodEmpresa", $codEmpresa);
        $strQuery->setParameter("varDepartamento", $departamento);
        $strQuery->setParameter("varCiudad", $ciudad);
        $strQuery->setParameter("varEstado", "Activo");
        $strQuery->setParameter("varEstado1", "Modificado");
        $strSelect = $strSelect . $strFrom;
        $strQuery->setDQL($strSelect);

        $arrayDatos["registros"] = $strQuery->getResult();

        $strSelectCount = "SELECT COUNT(infoPersona.id) ";

        $strQueryCount->setParameter("varCodEmpresa", $codEmpresa);
        $strQueryCount->setParameter("varDepartamento", $departamento);
        $strQueryCount->setParameter("varCiudad", $ciudad);
        $strQueryCount->setParameter("varEstado", "Activo");
        $strQueryCount->setParameter("varEstado1", "Modificado");
        $strQueryTotal = $strSelectCount . $strFrom;
        $strQueryCount->setDQL($strQueryTotal);

        $arrayDatos["total"] = $strQueryCount->getSingleScalarResult();


        return $arrayDatos;
    }

    public function findClientesXEmpresa($nombre = '', $codEmpresa = '', $start = '', $limit = '')
    {
        $arr_encontrados = array();

        $where = "";
        if($nombre && $nombre != "")
        {
            $where .= "AND (
                            LOWER(p.razonSocial) like LOWER('%$nombre%') OR
                            LOWER(CONCAT(p.nombres, CONCAT(' ', p.apellidos))) like LOWER('%$nombre%') 
                          )
                    ";
        }
        if($codEmpresa && $codEmpresa != "")
        {
            $where .= "AND eg.id = '$codEmpresa' ";
        }

        $sql = "SELECT p  
        
                FROM 
                schemaBundle:InfoPersona p, 
                schemaBundle:InfoEmpresaGrupo eg,
                schemaBundle:InfoPersonaEmpresaRol per, 
                schemaBundle:InfoEmpresaRol er,
                schemaBundle:AdmiRol r, 
                schemaBundle:AdmiTipoRol tr 
        
                WHERE per.personaId = p.id
                AND per.empresaRolId = er.id
                AND er.empresaCod = eg.id
                AND er.rolId = r.id
                AND r.tipoRolId = tr.id
                AND r.tipoRolId = tr.id
                AND tr.descripcionTipoRol = 'Cliente'  
                AND LOWER(p.estado) not like LOWER('Eliminado') 
                AND LOWER(eg.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(er.estado) not like LOWER('Eliminado') 
                AND LOWER(r.estado) not like LOWER('Eliminado') 
                AND LOWER(tr.estado) not like LOWER('Eliminado') 
                $where 
               ";

        $query = $this->_em->createQuery($sql);
        // $registros = $query->getResult();

        if($start != '' && $limit != '')
            $registros = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else if($start != '' && $limit == '')
            $registros = $query->setFirstResult($start)->getResult();
        else if($start == '' && $limit != '')
            $registros = $query->setMaxResults($limit)->getResult();
        else
            $registros = $query->getResult();

        if($registros)
        {
            $num = count($registros);
            foreach($registros as $entity)
            {

                $cliente = $entity->getRazonSocial() ? $entity->getRazonSocial() : $entity->getNombres() . ' ' . $entity->getApellidos();

                $arr_encontrados[] = array('id_cliente' => $entity->getId(),
                    'cliente' => ucwords(strtolower(trim($cliente)))
                );
            }
            $dataF = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    public function generarJsonLoginesXCliente($id_param, $estado, $start, $limit)
    {
        $arr_encontrados = array();


        $qb = $this->_em->createQueryBuilder();
        $qb->select('entidad')
            ->from('schemaBundle:InfoPersonaEmpresaRol', 'iper')
            ->from('schemaBundle:InfoPunto', 'entidad')
            ->where('iper.personaId = ?1')
            ->setParameter(1, $id_param)
            ->andWhere('entidad.personaEmpresaRolId = iper.id')
            ->andWhere("entidad.estado not like 'Eliminado'");
        $query = $qb->getQuery();

        $registros = $query->getResult();
        if($registros)
        {
            $num = count($registros);
            foreach($registros as $data)
            {
                $idCliente = ($data->getPersonaEmpresaRolId() ? ($data->getPersonaEmpresaRolId()->getPersonaId() ?
                            ($data->getPersonaEmpresaRolId()->getPersonaId()->getId() ? $data->getPersonaEmpresaRolId()->getPersonaId()->getId() : "" ) : "") : "");
                $nombreCliente = ($data->getPersonaEmpresaRolId() ? ($data->getPersonaEmpresaRolId()->getPersonaId() ?
                            ($data->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial() ? $data->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial() : $data->getPersonaEmpresaRolId()->getPersonaId()->getNombres() . " " . $data->getPersonaEmpresaRolId()->getPersonaId()->getApellidos() ) : "") : "");

                $arr_encontrados[] = array('id_parte_afectada' => $data->getId(),
                    'nombre_parte_afectada' => $data->getLogin(),
                    'id_descripcion_1' => $idCliente,
                    'nombre_descripcion_1' => ucwords(strtolower(trim($nombreCliente))),
                    'id_descripcion_2' => '',
                    'nombre_descripcion_2' => '');
            }
            $dataF = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    public function generarJsonElementosXCliente($id_param, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $sql = "SELECT ie  
                FROM 
                schemaBundle:InfoPersona pe,
                schemaBundle:InfoPersonaEmpresaRol per,
                schemaBundle:InfoPunto pu,
                schemaBundle:InfoServicio s,
                schemaBundle:InfoInterfaceElemento ie,
                schemaBundle:InfoElemento e 
                
                WHERE 
                per.personaId = pe.id 
                AND pu.personaEmpresaRolId = per.id 
                AND s.puntoId = pu.id 
                AND s.interfaceElementoId = ie.id    
                AND ie.elementoId = e.id  
                AND pe.id = '$id_param' 
        
                AND LOWER(s.estado) not like LOWER('Eliminado') 
                AND LOWER(pu.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(pe.estado) not like LOWER('Eliminado') 
               ";

        $query = $this->_em->createQuery($sql);
        $registros = $query->getResult();

        if($registros)
        {
            $num = count($registros);
            foreach($registros as $entity)
            {
                $arr_encontrados[] = array('id_parte_afectada' => $entity->getElementoId()->getId(),
                    'nombre_parte_afectada' => $entity->getElementoId()->getNombreElemento(),
                    'id_descripcion_1' => $entity->getId(),
                    'nombre_descripcion_1' => $entity->getNombreInterfaceElemento(),
                    'id_descripcion_2' => '',
                    'nombre_descripcion_2' => '');
            }
            $dataF = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    public function find30PorEmpresaPorEstadoPorUsuario($idEmpresa, $usuario, $tipoPersona, $limit, $page, $start, $idCliente)
    {
        $criterio_usuario = '';
        $criterio_cliente = '';
        $from_contacto_cli = '';
        $objeto_retorna = 'b';
        if($usuario != '')
        {
            $criterio_usuario = " a.usrCreacion='$usuario' AND ";
        }

        if($idCliente)
        {
            $tipoPersona = 'Cliente';
            $objeto_retorna = 'g';
            $from_contacto_cli = " ,schemaBundle:InfoPersonaContacto f, schemaBundle:InfoPersona g ";
            $criterio_cliente = " b.id=f.personaEmpresaRolId AND f.contactoId=g.id AND b.personaId=$idCliente AND";
        }
        $query = $this->_em->createQuery("SELECT $objeto_retorna
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e $from_contacto_cli
		WHERE 
                a.id=b.personaId AND b.empresaRolId=c.id AND c.rolId=d.id AND d.tipoRolId=e.id "
                . "AND
                $criterio_cliente
                $criterio_usuario    
                c.empresaCod='$idEmpresa' 
                AND LOWER(e.descripcionTipoRol)=LOWER('$tipoPersona') order by a.feCreacion DESC"
            )->setMaxResults(30);
        $datos = $query->getResult();
//echo $query->getSQL();die;
        $total = count($query->getResult());
        $datos = $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;
        return $resultado;
    }

    /**
     * Documentación para el método 'findPorEmpresaPorRazonSocial'.
     * Obtiene informacion de los Roles de la persosona
     * @param integer $idEmpresa 
     * @param string  $razonSocial
     * 
     * @return array $arrayDatos      
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-05-15
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-23 Obtener unicamente el id de Empresa Rol y la Razon Social del Cliente
     */    
	public function findPorEmpresaPorRazonSocial($idEmpresa, $razonSocial)
    {
        $sql = "SELECT IPER.id,
                       IP.razonSocial,
                       IP.nombres,
                       IP.apellidos
                FROM schemaBundle:InfoPersona IP,
                     schemaBundle:InfoPersonaEmpresaRol IPER, 
                     schemaBundle:InfoEmpresaRol IER
                WHERE     IP.id             = IPER.personaId 
                      AND IPER.empresaRolId = IER.id 
                      AND IPER.estado       = :estado 
                      AND IER.empresaCod    = :empresaCod 
                      AND ( (IP.tipoTributario = :tipoTributario 
                                AND LOWER(IP.razonSocial) like :razonSocial)
                         OR (IP.tipoTributario != :tipoTributario 
                                AND (LOWER(IP.nombres)     like :razonSocial
                                  OR LOWER(IP.apellidos)   like :razonSocial))
                            )";
		$query = $this->_em->createQuery($sql);
        
        $query->setParameter("empresaCod", $idEmpresa);
        $query->setParameter("razonSocial", '%'.strtolower($razonSocial).'%');
        $query->setParameter("tipoTributario", 'JUR');
        $query->setParameter("estado", 'Activo');
        $query->setMaxResults(1);

        $datos = $query->getResult();
        $resultado['registros'] = $datos;
        return $resultado;
    }

    /**
     * Documentación para el método 'findPersonasPorCriterios'.
     * Obtiene informacion de los prospectos  segun criterios de busqueda 
     * @param array     $arrayParametros    
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 04-05-2016  
     * @version 1.0 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 16-05-2016 - Se corrige la paginacion
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.3 2016-05-28 - Mejorar el tiempo de consulta de la consulta de 17 a 4 segundos
     *                           Nombre, apellido y RazonSocial ya los convertía en UPPER
     *                           Parametrizar las variables e incluir verificación en blanco ''
     *                           $criterio_identificacion no estaba definido, y se presentaba error sin consecuencia
     *                           Se elimina parametro $intIdCliente ya que no es utilizado, y
     *                           Se convierte en Native Query para no contar todo el ResultSet
     *                           Se obtuvo el intval del limit y star para enviarlos a setQueryLimit
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.4 15-07-2016 Se agrega campos TIPO_TRIBUTARIO,REPRESENTANTE_LEGAL en la consulta.
     * 
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.5 08-08-2016 Se agrega campo OFICINA_ID en la consulta.
     * 
     * @author : Edgar Holguin <apenaherrera@telconet.ec>
     * @version 1.6 24-10-2016 Se corrige para que filtro consulte por fecha de creacion del rol.
     * 
     * @author : Andrés Montero <amontero@telconet.ec>
     * @version 1.7 10-11-2016 Se agrega campo LOGIN de la persona en la consulta.
     * 
     * @author : Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 20-11-2016 Se agrega en la consulta el id de la cuadrilla
     * 
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.9 19-11-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar los clientes de acuerdo
     *                         a la caracteristica de la persona en sesion por medio de las siguiente 
     *                         descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     *                         Estos cambios solo aplican para Telconet
     *
     * @author : Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.10 07-05-2020 Se agrega en la consulta el filtro por nombres completo y ruc del cliente/persona
     *
     * @author : Néstor Naula<nnaulal@telconet.ec>
     * @version 2.0 03-09-2020 - El tipo de persona se cambia strin a un array de string
     * @since 1.10
     * 
     * @author : Emilio Flores <eaflores@telconet.ec>
     * @version 2.1 19-04-2023 - Se parametriza el valor por defecto de fechaDesde.
     *
     * @return array $arrayDatos      
     */
    public function findPersonasPorCriterios($arrayParametros)
    {
        $fechaDesde        = $arrayParametros['fechaDesde'];
        $fechaHasta        = $arrayParametros['fechaHasta'];
        $strUsuario        = $arrayParametros['usuario'] ? $arrayParametros['usuario'] : "";
        $strLogin          = $arrayParametros['login'] ? $arrayParametros['login'] : "";
        $strEstado         = $arrayParametros['estado'] ? $arrayParametros['estado'] : '';
        $strNombre         = $arrayParametros['nombre'] ? strtoupper($arrayParametros['nombre']) : "";
        $strApellido       = $arrayParametros['apellido'] ? strtoupper($arrayParametros['apellido']) : "";
        $strRazonSocial    = $arrayParametros['razon_social'] ? strtoupper($arrayParametros['razon_social']) : "";
        $intIdEmpresa      = $arrayParametros['idEmpresa'] ? $arrayParametros['idEmpresa'] : "";
        $strTipoPersona    = $arrayParametros['tipo_persona'];
        $strIdentificacion = $arrayParametros['identificacion'] ? $arrayParametros['identificacion'] : "";       
        $limit             = $arrayParametros['limit'] ? intval($arrayParametros['limit']) : 0;
        $start             = $arrayParametros['start'] ? intval($arrayParametros['start']) : 0; 
        $strTipo           = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                ? $arrayParametros['strPrefijoEmpresa'] : '';
        $strModulo         = ( isset($arrayParametros['strModulo']) && !empty($arrayParametros['strModulo']) )
                                ? $arrayParametros['strModulo'] : '';
        $strCliente        = $arrayParametros['strCliente'] ? $arrayParametros['strCliente'] : "";
        $strEstadoActivo   = 'Activo';
        $strDescripcion    = 'ASISTENTE_POR_CARGO';
        $strQueryIn        = " ";
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strQueryVendAsignados = " ";

        try
        {
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $queryCount = $this->_em->createNativeQuery(null, $rsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');
            
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addScalarResult('PERSONA_ID','persona_id','integer');
            $rsm->addScalarResult('ID_PERSONA_ROL','id','integer');
            $rsm->addScalarResult('CUADRILLA_ID','cuadrilla_id','integer');
            $rsm->addScalarResult('VLAN','vlan','integer');
            $rsm->addScalarResult('ID_ELEMENTO','id_elemento','integer');
            $rsm->addScalarResult('ESTADO', 'estado','string');
            $rsm->addScalarResult('RAZON_SOCIAL', 'razon_social','string');
            $rsm->addScalarResult('NOMBRES', 'nombres','string');
            $rsm->addScalarResult('APELLIDOS', 'apellidos','string');
            $rsm->addScalarResult('TIPO_IDENTIFICACION','tipo_identificacion','string');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE','identificacion','string');
            $rsm->addScalarResult('DIRECCION_TRIBUTARIA', 'direccion_tributaria','string');
            $rsm->addScalarResult('FE_CREACION','fe_creacion','datetime');
            $rsm->addScalarResult('USR_CREACION', 'usr_creacion','string');
            $rsm->addScalarResult('LOGIN', 'login','string');
            $rsm->addScalarResult('TIPO_EMPRESA', 'tipo_empresa','string');
            $rsm->addScalarResult('TIPO_TRIBUTARIO', 'tipo_tributario','string');
            $rsm->addScalarResult('REPRESENTANTE_LEGAL', 'representante_legal','string');
            $rsm->addScalarResult('OFICINA_ID', 'oficina_id','integer'); 

            $query = $this->_em->createNativeQuery(null, $rsm);

            $strSelect = "SELECT DISTINCT IPER.PERSONA_ID,
                        IPER.ID_PERSONA_ROL,
                        IPER.ESTADO,
                        IPER.CUADRILLA_ID,
                        IP.RAZON_SOCIAL,
                        IP.NOMBRES,
                        IP.APELLIDOS,
                        IP.TIPO_IDENTIFICACION,
                        IP.IDENTIFICACION_CLIENTE,
                        IP.DIRECCION_TRIBUTARIA,
                        IPER.FE_CREACION,
                        IPER.USR_CREACION,
                        IP.LOGIN,                        
                        IP.TIPO_EMPRESA,
                        IP.TIPO_TRIBUTARIO,
                        IP.REPRESENTANTE_LEGAL,
                        IPER.OFICINA_ID";
            $strFrom = " FROM
                        INFO_PERSONA IP, 
                        INFO_PERSONA_EMPRESA_ROL IPER, 
                        INFO_EMPRESA_ROL IER, 
                        ADMI_ROL AR, 
                        ADMI_TIPO_ROL ATR";
            $strWhere = " WHERE 
                                IP.ID_PERSONA           = IPER.PERSONA_ID
                            AND IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                            AND IER.ROL_ID        = AR.ID_ROL
                            AND AR.TIPO_ROL_ID    = ATR.ID_TIPO_ROL
                            AND IER.EMPRESA_COD   = :intIdEmpresa
                            AND LOWER(ATR.DESCRIPCION_TIPO_ROL) IN (:strTipoPersona)";

            if(!is_array($strTipoPersona))
            {
                $strTipoPersona = strtolower($strTipoPersona);
            }

            $query->setParameter('intIdEmpresa', $intIdEmpresa);
            $query->setParameter('strTipoPersona', $strTipoPersona);
            $queryCount->setParameter('intIdEmpresa', $intIdEmpresa);
            $queryCount->setParameter('strTipoPersona', $strTipoPersona);
        
            if ($strEstado!="")
            {       
                $strWhere .=" AND UPPER(IPER.ESTADO) = :strEstado";
                $query->setParameter('strEstado', strtoupper($strEstado));
                $queryCount->setParameter('strEstado', strtoupper($strEstado));
            }    
            if ($strNombre!="")
            {       
                $strWhere .=" AND UPPER(IP.NOMBRES) like :strNombre";
                $query->setParameter('strNombre', "%".$strNombre."%");
                $queryCount->setParameter('strNombre', "%".$strNombre."%");
            }  
            if ($strApellido!="")
            {       
                $strWhere .=" AND UPPER(IP.APELLIDOS) like :strApellido";
                $query->setParameter('strApellido', "%".$strApellido."%");
                $queryCount->setParameter('strApellido', "%".$strApellido."%");
            } 
            if ($strRazonSocial!="")
            {       
                if(!empty($strCliente))
                {
                    $strWhere .= " AND ( UPPER(IP.RAZON_SOCIAL) like :strRazonSocial OR ";
                    $strWhere .= " UPPER(CONCAT(IP.APELLIDOS,' ' || IP.NOMBRES)) LIKE :strCliente OR ";
                    $strWhere .= " UPPER(CONCAT(IP.NOMBRES,' ' || IP.APELLIDOS)) LIKE :strCliente OR ";
                    $strWhere .= " IP.IDENTIFICACION_CLIENTE LIKE :strCliente )";
                    $query->setParameter('strCliente', "%".strtoupper($strCliente)."%");
                    $queryCount->setParameter('strCliente', "%".strtoupper($strCliente)."%");
                }
                else
                {
                    $strWhere .=" AND UPPER(IP.RAZON_SOCIAL) like :strRazonSocial";
                }
                $query->setParameter('strRazonSocial', "%".$strRazonSocial."%");
                $queryCount->setParameter('strRazonSocial', "%".$strRazonSocial."%");
            }         
            if ($strIdentificacion)
            {       
                $strWhere .=" AND IP.IDENTIFICACION_CLIENTE = :strIdentificacion";
                $query->setParameter('strIdentificacion', $strIdentificacion);
                $queryCount->setParameter('strIdentificacion', $strIdentificacion);
            }
            if ($strUsuario!="")
            {       
                $strWhere .=" AND UPPER(IP.USR_CREACION) = UPPER(:strUsuario)";
                $query->setParameter('strUsuario', $strUsuario);
                $queryCount->setParameter('strUsuario', $strUsuario);
            }
            if ($strLogin!="")
            {       
                $strWhere .=" AND UPPER(IP.LOGIN) = UPPER(:strLogin)";
                $query->setParameter('strLogin', $strLogin);
                $queryCount->setParameter('strLogin', $strLogin);
            }             

            if (!$fechaDesde)
            {
                $strDias = $arrayParametros['strLimiteDias'];
                $objHoy = date('Y/m/d');
                $fechaDesde = date('Y/m/d', strtotime($objHoy. ' - ' . $strDias . ' days'));
            }
            $objFechaD = strtotime($fechaDesde);
            if($objFechaD)
            {
                $strWhere .=" AND IPER.FE_CREACION >= :fechaDesde";
                $query->setParameter('fechaDesde', date("Y/m/d", $objFechaD));
                $queryCount->setParameter('fechaDesde', date("Y/m/d", $objFechaD));
            }

            if ($fechaHasta)
            {
                $fechaH = strtotime($fechaHasta);
                if($fechaH)//Si devuelve una marca de tiempo
                {
                    $strWhere .=" AND IPER.FE_CREACION <= :fechaHasta";
                    $query->setParameter('fechaHasta', date("Y/m/d", $fechaH));
                    $queryCount->setParameter('fechaHasta', date("Y/m/d", $fechaH));
                }
            }
            if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
            {
                if( $strTipo == 'SUBGERENTE' )
                {
                    $strQueryIn = " AND ipuVendAsignado.USR_VENDEDOR IN
                                    (SELECT IPVENDEDOR.LOGIN
                                        FROM INFO_PERSONA IPVENDEDOR
                                        JOIN INFO_PERSONA_EMPRESA_ROL IPERVENDEDOR
                                        ON IPERVENDEDOR.PERSONA_ID = IPVENDEDOR.ID_PERSONA
                                    WHERE IPERVENDEDOR.ESTADO                            = :strEstadoActivo
                                        AND IPVENDEDOR.ESTADO                            = :strEstadoActivo
                                        AND (IPERVENDEDOR.REPORTA_PERSONA_EMPRESA_ROL_ID = :intIdPersonEmpresaRol
                                        OR IPERVENDEDOR.ID_PERSONA_ROL                   = :intIdPersonEmpresaRol))
                                  ";
                }
                elseif( $strTipo == 'ASISTENTE' )
                {
                    $strQueryAsis = "";
                    $strIniParen  = "";
                    $strFinParen  = "";
                    if( $strModulo == 'Pre-Cliente' )
                    {
                        $strIniParen  = "(";
                        $strFinParen  = ")";
                        $strQueryAsis =" OR ipuVendAsignado.usr_creacion IN
                                            (SELECT ipAsis.login
                                                FROM db_comercial.info_persona_empresa_rol iperAsis
                                                    JOIN db_comercial.info_persona ipAsis
                                                    ON iperAsis.persona_id    = ipAsis.id_persona
                                                WHERE iperAsis.id_persona_rol = :intIdPersonEmpresaRol
                                                    AND iperAsis.estado       = :strEstadoActivo
                                                ) ";
                    }
                    $strQueryIn = " AND ".$strIniParen." ipuVendAsignado.USR_VENDEDOR IN
                                        (SELECT IP.LOGIN
                                            FROM INFO_PERSONA_EMPRESA_ROL_CARAC IPERC
                                                JOIN DB_COMERCIAL.ADMI_CARACTERISTICA AC
                                                ON AC.ID_CARACTERISTICA =IPERC.CARACTERISTICA_ID
                                                JOIN INFO_PERSONA IP
                                                ON IP.ID_PERSONA = TO_NUMBER(IPERC.VALOR)
                                        WHERE IPERC.PERSONA_EMPRESA_ROL_ID        = :intIdPersonEmpresaRol
                                                AND AC.DESCRIPCION_CARACTERISTICA = :strDescripcion
                                                AND AC.ESTADO                     = :strEstadoActivo
                                                AND IPERC.ESTADO                  = :strEstadoActivo
                                                AND IP.ESTADO                     = :strEstadoActivo )
                                        ".$strQueryAsis."
                                        ".$strFinParen."
                                  ";
                    $query->setParameter('strDescripcion', $strDescripcion);
                    $queryCount->setParameter('strDescripcion', $strDescripcion);
                }
                elseif( $strTipo == 'VENDEDOR' )
                {
                    $strQueryIn = " AND ipuVendAsignado.USR_VENDEDOR IN
                                    (SELECT IP.LOGIN
                                        FROM INFO_PERSONA IP
                                        JOIN INFO_PERSONA_EMPRESA_ROL IPER
                                        ON IPER.PERSONA_ID = IP.ID_PERSONA
                                    WHERE IPER.ID_PERSONA_ROL    = :intIdPersonEmpresaRol
                                        AND IPER.ESTADO          = :strEstadoActivo
                                        AND IP.ESTADO            = :strEstadoActivo)
                                  ";
                }
                $strQueryVendAsignados = " ,(SELECT concat(ipVendAsignado.APELLIDOS,' ' ||ipVendAsignado.NOMBRES)
                                                FROM DB_comercial.Info_punto ipuVendAsignado ,
                                                    DB_comercial.info_persona ipVendAsignado
                                                WHERE ipuVendAsignado.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                                                    AND ipVendAsignado.LOGIN                 = ipuVendAsignado.USR_VENDEDOR
                                                    ".$strQueryIn."
                                                    AND rownum                           = 1
                                                ) AS vendedorasignado";
                $rsm->addScalarResult('VENDEDORASIGNADO', 'vendedorasignado','string');
                $query->setParameter('strEstadoActivo', $strEstadoActivo);
                $query->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);

                $queryCount->setParameter('strEstadoActivo', $strEstadoActivo);
                $queryCount->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
                $strSelect .= $strQueryVendAsignados;
            }
            if( ($strModulo == 'Cliente' || $strModulo == 'Pre-Cliente') && $strPrefijoEmpresa == 'TN' )
            {
                $strSelect .=" ,(SELECT concat(ipVend.APELLIDOS,' '
                                    ||ipVend.NOMBRES)
                                  FROM DB_comercial.Info_punto ipuVend ,
                                    DB_comercial.info_persona ipVend
                                  WHERE ipuVend.PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                                  AND ipVend.LOGIN                     = ipuVend.USR_VENDEDOR
                                  and ipuVend.ESTADO                   = 'Activo'
                                  AND rownum                           = 1
                                ) AS vendedor ";
                $rsm->addScalarResult('VENDEDOR', 'vendedor','string');
            }
            $sql = $strSelect . $strFrom . $strWhere;

            $strSqlCount = $strSelectCount ." FROM (".$sql.")";
            $queryCount->setSQL($strSqlCount);      
            $totalPersonas = $queryCount->getSingleScalarResult();
            
            $sql .= " ORDER BY IPER.FE_CREACION DESC";
            $query->setSQL($sql);
            $arrayPersonas = $this->setQueryLimit($query,$limit,$start)->getArrayResult();
            
            $objResultado['total'] = $totalPersonas;
            $objResultado['registros'] = $arrayPersonas;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $objResultado = array(
                            'total'     => 0 ,
                            'registros' => array()
                            );
            
        }
        
        return $objResultado;
        
    }
        
    public function findVendedoresByEmpresa($arrayParametros)
    {
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                    ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                    ? $arrayParametros['strPrefijoEmpresa'] : '';
        $strCodEmpresa         = ( isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']) )
                                    ? $arrayParametros['strCodEmpresa'] : '';
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';
        $objQuery                 = $this->_em->createQuery();
        $strSelect = "SELECT pe ";
        $strFrom   = "FROM schemaBundle:InfoPunto p,
                           schemaBundle:InfoPersonaEmpresaRol per,
                           schemaBundle:InfoEmpresaRol er,
                           schemaBundle:InfoPersona pe ";
        $strWhere  = "WHERE lower(p.usrVendedor) = lower(pe.login)
                        AND per.personaId = pe.id
                        AND per.empresaRolId = er.id
                        AND er.empresaCod = $strCodEmpresa
                        AND lower(p.estado) not in ('eliminado','cancelado','cancel') ";
        $strOrder   = "ORDER BY pe.nombres, pe.razonSocial ";

        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !empty($intIdPersonEmpresaRol)) )
        {
            if( $strTipo == 'SUBGERENTE' )
            {
                $strWhere .= " AND pe.login IN
                                (SELECT ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.estado                        = :strEstadoActivo
                                    AND ipervend.personaId                   = ipvend.id
                                    AND ipvend.estado                        = :strEstadoActivo
                                    AND (ipervend.reportaPersonaEmpresaRolId = :intIdPersonEmpresaRol
                                    OR ipervend.id                           = :intIdPersonEmpresaRol))
                              ";
            }
            elseif( $strTipo == 'ASISTENTE' )
            {
                $strWhere .= " AND pe.login IN
                                (select ipvend.login
                                    from schemaBundle:InfoPersonaEmpresaRolCarac ipercvend ,
                                      schemaBundle:AdmiCaracteristica acvend ,
                                      schemaBundle:InfoPersona ipvend
                                WHERE ipercvend.personaEmpresaRolId          = :intIdPersonEmpresaRol
                                        and acvend.id                        = ipercvend.caracteristicaId
                                        and ipvend.id                        = ipercvend.valor
                                        AND acvend.descripcionCaracteristica = :strDescripcion
                                        AND acvend.estado                    = :strEstadoActivo
                                        AND ipercvend.estado                 = :strEstadoActivo
                                        AND ipvend.estado                    = :strEstadoActivo )
                              ";
                $objQuery->setParameter('strDescripcion', $strDescripcion);
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strWhere .= " AND pe.login IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = :intIdPersonEmpresaRol
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = :strEstadoActivo
                                    AND ipvend.estado      = :strEstadoActivo)
                              ";
            }
            $objQuery->setParameter('strEstadoActivo', $strEstadoActivo);
            $objQuery->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
        }
        $strSql     = $strSelect.$strFrom.$strWhere.$strOrder;
        $objQuery->setDQL($strSql);
        $arrayVendedores = $objQuery->getResult();
        return $arrayVendedores;
    }

    /**
     * Documentacion para la funcion esRecontratacion
     *
     * Funcion que retorna si la persona es Recontratacion Si o No
     *
     * @param integer $idPersona
     * @param string  $codEmpresa
     *
     * @return string $esRecontratacion
     *
     * @version 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 03-08-2016 Se realizan ajustes a la funcion para mejorar su rendimiento
     *
     */
    public function esRecontratacion($idPersona, $codEmpresa)
    {
        $esRecontratacion       = "No";
        $rolesPreClienteActivos = 0;
        $rolesClienteActivos    = 0;
        $rolesClienteCancelados = 0;
        try
        {
            $rsm      = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);

            $strSql = " SELECT
                            per.ID_PERSONA_ROL,
                            atr.DESCRIPCION_TIPO_ROL,
                            per.ESTADO

                        FROM INFO_PERSONA pe,
                            INFO_PERSONA_EMPRESA_ROL per,
                            INFO_EMPRESA_ROL er,
                            ADMI_ROL ar,
                            ADMI_TIPO_ROL atr

                        WHERE per.persona_Id            = pe.id_persona
                            AND per.empresa_Rol_Id          = er.id_empresa_rol
                            AND er.empresa_Cod              = :empresaCod
                            AND ar.id_rol                   = er.rol_Id
                            AND atr.id_tipo_rol             = ar.tipo_Rol_Id
                            AND per.persona_Empresa_Rol_Id IS NULL
                            AND per.persona_Id              =  :personaId ";

            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRol', 'integer');
            $rsm->addScalarResult('DESCRIPCION_TIPO_ROL', 'DescripcionTIpoRol', 'string');
            $rsm->addScalarResult('ESTADO', 'estado', 'string');

            $ntvQuery->setParameter('empresaCod', $codEmpresa);
            $ntvQuery->setParameter('personaId', $idPersona);

            $ntvQuery->setSQL($strSql);
            $arrayResultado = $ntvQuery->getResult();

            foreach($arrayResultado as $rol)
            {
                if($rol['DescripcionTIpoRol'] == "Cliente" && $rol['estado'] == "Cancelado")
                {
                    $rolesClienteCancelados++;
                }
                if($rol['DescripcionTIpoRol'] == "Cliente" && $rol['estado'] == "Activo")
                {
                    $rolesClienteActivos++;
                }
                if($rol['DescripcionTIpoRol'] == "Pre-cliente" && $rol['estado'] == "Activo")
                {
                    $rolesPreClienteActivos++;
                }
            }

            if($rolesClienteCancelados >= 1 && ($rolesPreClienteActivos >= 1 or $rolesClienteActivos >= 1))
            {
                $esRecontratacion = "Si";
            }
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $esRecontratacion;
    }

    ////generar todos los empleados por repository


    public function generarTodosEmpleadosXDepartamento($id_departamento = "", $id_oficina = "", $nombre = "", $soloJefes = false, $retornaIdPersonaEmpresaRol = false, $codEmpresa = "", $cantonesArray = array())
    {

        $arr_encontrados = array();

        $where = "";
        if($nombre && $nombre != "")
        {
            $where .= "AND (
                            LOWER(p.razonSocial) like LOWER('%$nombre%') OR
                            LOWER(CONCAT(p.nombres, CONCAT(' ', p.apellidos))) like LOWER('%$nombre%') 
                          ) 
                    ";
        }
        if($id_departamento && $id_departamento != "")
        {
            $where .= "AND d.id = '$id_departamento' ";
        }
        if($soloJefes)
        {
            $where .= "AND lower(r.esJefe) = lower('S') ";
        }
//         if($id_oficina && $id_oficina!="")
//         {
//             $where .= "AND og.id = '$id_oficina' ";
//         }

        if(isset($codEmpresa))
        {
            if($codEmpresa && $codEmpresa != "")
            {
                $where .= "AND eg.id = '$codEmpresa' ";
            }
        }

        $boolCantones = false;
        if(isset($cantonesArray))
        {
            if($cantonesArray && count($cantonesArray) > 0)
            {
                $boolCantones = true;
                $cantones_separado_por_comas = implode(",", $cantonesArray);
                $where .= "AND og.cantonId IN ($cantones_separado_por_comas) ";
            }
        }

        $sql = "SELECT p.id as idPersona, p.nombres, p.apellidos, per.id as idPersonaEmpresaRol  
        
                FROM 
                schemaBundle:InfoPersona p, 
                schemaBundle:InfoEmpresaGrupo eg,
                schemaBundle:InfoOficinaGrupo og,
                schemaBundle:InfoPersonaEmpresaRol per, 
                schemaBundle:InfoEmpresaRol er,
                schemaBundle:AdmiDepartamento d, 
                schemaBundle:AdmiRol r, 
                schemaBundle:AdmiTipoRol tr 
        
                WHERE per.personaId = p.id 
                AND per.empresaRolId = er.id 
                AND per.oficinaId = og.id 
                AND per.departamentoId = d.id 
                AND er.empresaCod = og.empresaId  
                AND er.empresaCod = eg.id 
                AND er.rolId = r.id 
                AND r.tipoRolId = tr.id 
                AND r.tipoRolId = tr.id 
                AND tr.descripcionTipoRol = 'Empleado'  
                AND LOWER(p.estado) not like LOWER('Eliminado') 
                AND LOWER(eg.estado) not like LOWER('Eliminado') 
                AND LOWER(og.estado) not like LOWER('Eliminado') 
                AND LOWER(d.estado) not like LOWER('Eliminado') 
                AND LOWER(per.estado) not like LOWER('Eliminado') 
                AND LOWER(er.estado) not like LOWER('Eliminado') 
                AND LOWER(r.estado) not like LOWER('Eliminado') 
                AND LOWER(tr.estado) not like LOWER('Eliminado') 
                $where 
				
				ORDER BY p.nombres, p.apellidos 
               ";

        $query = $this->_em->createQuery($sql);

        $datos = $query->getResult();
        $total = count($datos);

        if($total > 0)
        {
            $resultado['registros'] = $datos;
            $resultado['total'] = $total;
        }
        else
        {
            $resultado['registros'] = '[]';
            $resultado['total'] = 0;
        }

        return $resultado;
    }

    /**
     * Consulta los roles por id persona, por tipo de rol y por estados. 
     * Retorna id personaEmpresaRol con estado y empresaRolId.
     * @param integer $personaId
     * @param array $descRol
     * @param string $empresa
     * @param array $estados
     * @return array
     */
    public function getPersonaEmpresaRolPorPersonaPorTipoRolEstados($personaId, $arrayDescRol, $empresa, $arrayEstados)
    {
        $query = "SELECT per.id,per.estado,er.id as empresaRolId
        FROM 
        schemaBundle:InfoPersonaEmpresaRol per,
        schemaBundle:InfoEmpresaRol er,
        schemaBundle:AdmiRol rol,
        schemaBundle:AdmiTipoRol trol
        WHERE 
        per.empresaRolId= er.id AND
        er.rolId = rol.id AND
        rol.tipoRolId= trol.id AND
        per.personaId=:personaId AND
        trol.descripcionTipoRol IN (:descRol) AND 
        er.empresaCod=:empresaCod AND 
        per.estado IN (:estados) ORDER BY per.estado ASC";
        $query1 = $this->_em->createQuery($query);
        $query1->setParameter('personaId', $personaId);
        $query1->setParameter('descRol', $arrayDescRol);
        $query1->setParameter('empresaCod', $empresa);
        $query1->setParameter('estados', $arrayEstados);
        $datos = $query1->getResult();
        return $datos;
    }

    /**
     * getDepartamentoPersonaLogueada
     *
     * Esta funcion retorna el departamento que se muestra en la opcion de ver seguimientos de tareas.
     *
     * @version Inicial 1.0
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 17-03-2016 Se realizan ajustes para presentar correctamente el departamento ya que no esta validando el estado
     *
     * @param integer  $idpersona
     * @param String   $codEmpresa
     *
     * @return array $datos
     *
     */
    public function getDepartamentoPersonaLogueada($idpersona, $codEmpresa)
    {
        $query = "SELECT per.departamentoId as departamento
                        FROM
                        schemaBundle:InfoPersonaEmpresaRol per,
                        schemaBundle:InfoOficinaGrupo og
                        WHERE
                        per.personaId = :idPersona AND
                        per.oficinaId = og.id AND
                        og.empresaId  = :codEmpresa AND
                        per.departamentoId is not null and per.departamentoId <> 0 AND
                        per.estado NOT IN (:estados) ";

        $query1 = $this->_em->createQuery($query);
        $query1->setParameter('idPersona', $idpersona);
        $query1->setParameter('codEmpresa', $codEmpresa);
        $query1->setParameter('estados', array('Inactivo', 'Cancelado', 'Anulado', 'Eliminado'));

        $datos = $query1->getResult();

        return $datos;
    }
    /**
     * getNombreDepartamentoPersonaLogueada
     *
     * Esta funcion retorna el nombre del departamento que se muestra en la opcion de ver seguimientos de tareas.
     *
     * @version Inicial 1.0
     *
     * @author modificado Jose Guaman <jaguamanp@telconet.ec>
     * @version 1.1 20-07-2022 Se crea el metodo para buscar el nombre del departamento con el login del usuario
     *
     * @param integer  $idpersona
     *
     * @return array $datos
     *
     */
    public function getNombreDepartamentoPersonaLogueada($intIdpersona)
    {
        $strQuery = "SELECT a.nombreDepartamento AS departamento 
            FROM schemaBundle:AdmiDepartamento a 
            where a.id in (
                SELECT s.departamentoId
                        FROM schemaBundle:InfoPersonaEmpresaRol s 
                        WHERE s.personaId= :idPersona
                        and s.estado= 'Activo'
                        and s.departamentoId is not null and s.departamentoId <> 0
            )";

        $objQuery1 = $this->_em->createQuery($strQuery);
        $objQuery1->setParameter('idPersona', $intIdpersona);
        $arrayDatos = $objQuery1->getResult();
        return $arrayDatos;
    }

    public function getOficinaXEmpresaYUser($codEmpresa, $idPersona)
    {


        $query = "SELECT per.id
                            FROM 
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoOficinaGrupo og                           
                            WHERE 
                            per.personaId = $idPersona AND
                            per.oficinaId = og.id AND
                            og.empresaId  = $codEmpresa                          
                            ";
        $query1 = $this->_em->createQuery($query);
        //  echo $query1->getSQL(); die();            
        $datos = $query1->getResult();
        //print_r($datos);die;
        return $datos;
    }

    public function getDepartamentosRolXLoginEmpleado($login)
    {

        $query = "SELECT a.departamentoId
                            FROM 
                            schemaBundle:InfoPersonaEmpresaRol a,
                            schemaBundle:InfoPersona b                           
                            WHERE 
                            a.personaId = b.id and
                            b.login = '$login'                            
                            ";
        $query1 = $this->_em->createQuery($query);
        //  echo $query1->getSQL(); die();            
        $datos = $query1->getResult();
        //print_r($datos);die;
        return $datos;
    }

    /**
     * Funcion que consulta las ciudades por empresa
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-06-2016 Se habilita la busqueda del combo ciudad y se organiza el envio de parametros al query
     *
     * @version 1.0
     *
     * @param $codEmpresa
     * @param $ciudad
     * @param $origen
     *
     * @return array $respuesta
     */
    public function generarJsonCiudadesPorEmpresa($codEmpresa,$ciudad,$origen)
    {
        $query = $this->_em->createQuery();

        $from = "SELECT b.id , b.nombreCanton
                            FROM
                            schemaBundle:InfoOficinaGrupo a,
                            schemaBundle:AdmiCanton b ";
        $where = " WHERE a.cantonId = b.id ";
        $groupBy = " GROUP BY b.id , b.nombreCanton order by b.nombreCanton ASC ";

        if(isset($codEmpresa))
        {
            if($codEmpresa && $codEmpresa != "")
            {
                $where .= " AND a.empresaId = :codEmpresa ";
                $query->setParameter('codEmpresa', $codEmpresa);
            }
        }

        if(isset($ciudad))
        {
            if($ciudad && $ciudad != "")
            {
                $where .= " AND upper(b.nombreCanton) like upper(:nombreCiudad) ";
                $query->setParameter('nombreCiudad',"%".$ciudad."%");
            }
        }

        $sql = $from . $where . $groupBy;

        $query->setDQL($sql);

        $datos = $query->getResult();

        if($datos)
        {
            $num = count($datos);
            foreach($datos as $data)
            {
                if($origen == "")
                {
                    $arr_encontrados[] = array('id_canton' => $data['id'],
                        'nombre_canton' => ucwords(strtolower(trim($data['nombreCanton']))));
                }
                elseif($origen == "O")
                {
                    $arr_encontrados[] = array('id_cantonO' => $data['id'],
                        'nombre_cantonO' => ucwords(strtolower(trim($data['nombreCanton']))));
                }
                elseif($origen == "D")
                {
                    $arr_encontrados[] = array('id_cantonD' => $data['id'],
                        'nombre_cantonD' => ucwords(strtolower(trim($data['nombreCanton']))));
                }
            }
            $dataF = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    /**
     * generarJsonDepartamentosPorCiudadYEmpresa
     * 
     * Esta funcion ejecuta el Query que retorna todos los departamentos asociados a una empresa y ciudad
     * 
     * @version Inicial 1.0
     * 
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-11-2015 Se realizan ajustes para obtener todos los departamentos
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-01-2018 Se agrega validacion de Estado
     *
     * @param integer  $codEmpresa
     * @param integer  $idCanton
     * @param string   $nombreDep
     * @param string   $origen
     *
     * @return array $resultado
     * 
     */
    public function generarJsonDepartamentosPorCiudadYEmpresa($codEmpresa, $idCanton, $nombreDep, $origen)
    {

        $where = '';
        $select = '';

        if($idCanton && $idCanton != '')
        {

            $select .= '  ,schemaBundle:AdmiCanton b,
                            schemaBundle:InfoOficinaGrupo d ';

            $where .= "    and b.id = $idCanton and
                            b.id = d.cantonId ";
        }

        if($nombreDep && $nombreDep != '')
        {

            $where .= "    and lower(a.nombreDepartamento) like lower('%$nombreDep%') 
                            ";
        }

        $where .= " and a.estado <> 'Eliminado' ";

        $query = "SELECT a.id , a.nombreDepartamento
                            FROM                             
                            schemaBundle:AdmiDepartamento a
                            $select
                            WHERE 			    
                            a.empresaCod = $codEmpresa  
                            $where
                            group by a.id,a.nombreDepartamento order by a.nombreDepartamento ASC
                            ";
        $query1 = $this->_em->createQuery($query);

        $datos = $query1->getResult();

        if($datos)
        {
            $num = count($datos);
            foreach($datos as $data)
            {
                if($origen == "O")
                {
                    $arr_encontrados[] = array('id_departamentoO' => $data['id'],
                        'nombre_departamentoO' => ucwords(trim($data['nombreDepartamento'])));
                }
                elseif($origen == "")
                {
                    $arr_encontrados[] = array('id_departamento' => $data['id'],
                        'nombre_departamento' => ucwords(trim($data['nombreDepartamento'])));
                }
            }
            $dataF = json_encode($arr_encontrados);
            $resultado = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
            return $resultado;
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    /**
     * getRegistrosPersonaRol
     *
     * Método que devuelve los registros de persona rola del empleado enviado por referencia y la empresa
     *
     * @param integer $idPersona         
     * @param string $codEmpresa        
     *
     * @return registros de salida del query
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function getRegistrosPersonaRol($idPersona, $codEmpresa)
    {

        $sql = "SELECT
	          a.id,
		  b.nombreOficina ,
		  e.nombreDepartamento,
		  d.descripcionRol,
		  d.esJefe
		  FROM
		  schemaBundle:InfoPersonaEmpresaRol a,
		  schemaBundle:InfoOficinaGrupo b,
		  schemaBundle:InfoEmpresaRol c,
		  schemaBundle:AdmiRol d,		  
		  schemaBundle:AdmiDepartamento e		  
		  WHERE
		  a.oficinaId = b.id and
		  a.empresaRolId = c.id and
		  a.departamentoId = e.id and		  
		  c.rolId = d.id and
		  a.estado    = :estado and
		  b.empresaId = :codEmpresa and
		  a.personaId = :personaId";

        $query = $this->_em->createQuery($sql);

        $query->setParameter('estado', 'Activo');
        $query->setParameter('codEmpresa', $codEmpresa);
        $query->setParameter('personaId', $idPersona);

        return $query->getResult();
    }

    /**
     * generarJsonPersonaRol
     *
     * Método que devuelve el json con el perfil obtenido de la persona enviada como referencia y la empresa
     *
     * @param integer $idPersona         
     * @param string $codEmpresa         
     *
     * @return JSON con valores a mostrar 
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function generarJsonPersonaRol($idPersona, $codEmpresa)
    {

        $registros = $this->getRegistrosPersonaRol($idPersona, $codEmpresa);

        if($registros)
        {

            foreach($registros as $data)
            {
                $arr_encontrados[] = array(
                    'nombreOficina' => $data['nombreOficina'],
                    'nombreDepartamento' => $data['nombreDepartamento'],
                    'descripcionRol' => $data['descripcionRol'],
                    'esJefe' => $data['esJefe'],
                    'id' => $data['id']
                );
            }

            $data = json_encode($arr_encontrados);
            $resultado = '{"encontrados":' . $data . ',"success":"true"}';

            return $resultado;
        }
        else
        {
            $resultado = '{"encontrados":[],"success":"false"}';

            return $resultado;
        }
    }

    /**
     * Consulta los roles por id persona, por tipo de rol y por estados. 
     * Retorna array de objetos de personaEmpresaRol
     * @param  integer $personaId
     * @param  integer $codEmpresa
     * @param  array   $arrayEstado
     * @param  array   $descripcionTipoRol
     * @return array
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-10-2014
     * @since 1.0
     *
     * @author Modificado: Sofia Fernandez <sfernadnez@telconet.ec>
     * @version 1.2 06-03-2018 - Se agrega tipo de rol para personal externo
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 23-04-2020 - Se modifica para que el tipo de rol externo sea enviado como parámetro
     */
    public function findPersonaEmpresaRolByParams($personaId, $codEmpresa, $arrayEstado, $descripcionTipoRol)
    {
        $query = "SELECT per 
                        FROM 
                        schemaBundle:InfoPersonaEmpresaRol per,
                        schemaBundle:InfoEmpresaRol er,
                	schemaBundle:AdmiRol r, 
                	schemaBundle:AdmiTipoRol tr 
                        WHERE 
                        per.empresaRolId= er.id AND
                        per.personaId = :personaId AND
			er.rolId = r.id AND
                	r.tipoRolId = tr.id AND
                        per.estado in (:arrayEstadoRol) AND
                        er.empresaCod = :empresa AND
                        tr.descripcionTipoRol = :descripcionTipoRol";

        $query1 = $this->_em->createQuery($query);
        $query1->setParameter('personaId', $personaId);
        $query1->setParameter('empresa', $codEmpresa);
        $query1->setParameter('arrayEstadoRol', $arrayEstado);
        $query1->setParameter('descripcionTipoRol', $descripcionTipoRol);        
        $datos = $query1->getOneOrNullResult();
        return $datos;
    }
    /**
     * generarJsonPerfilesPersona
     *
     * Método que devuelve json con los registros consultados
     *
     * @param integer $idPersona                     
     *
     * @return JSON con valores a mostrar 
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function generarJsonPerfilesPersona($idPersona)
    {

        $sql = "SELECT
	          a.id,
		  e.descripcionRol ,
		  c.nombreOficina,
		  f.nombreDepartamento,
		  d.nombreEmpresa,
		  a.estado,
		  a.usrCreacion		  
		  FROM
		  schemaBundle:InfoPersonaEmpresaRol a,
		  schemaBundle:InfoEmpresaRol b,
		  schemaBundle:InfoOficinaGrupo c,
		  schemaBundle:InfoEmpresaGrupo d,
		  schemaBundle:AdmiRol e,		  
		  schemaBundle:AdmiDepartamento f		  
		  WHERE
		  a.empresaRolId = b.id and
		  a.oficinaId = c.id and
		  a.departamentoId = f.id and		  
		  c.empresaId = d.id and
		  b.rolId = e.id and		  
		  a.personaId = :personaId";

        $query = $this->_em->createQuery($sql);

        $query->setParameter('personaId', $idPersona);

        $registros = $query->getResult();

        if($registros)
        {

            foreach($registros as $data)
            {
                $arr_encontrados[] = array(
                    'id_persona_rol' => $data['id'],
                    'rol' => $data['descripcionRol'],
                    'oficina' => $data['nombreOficina'],
                    'departamento' => $data['nombreDepartamento'],
                    'empresa' => $data['nombreEmpresa'],
                    'estado' => $data['estado'],
                    'usuario_creacion' => $data['usrCreacion']
                );
            }

            $data = json_encode($arr_encontrados);
            $resultado = '{"encontrados":' . $data . ',"success":"true"}';

            return $resultado;
        }
        else
        {
            $resultado = '{"encontrados":[],"success":"false"}';

            return $resultado;
        }
    }

    /**
     * Documentación para el método: 'buscaClientesPorIdentificacionTipoRolEmpresaEstados'.
     * Busca un persona empresa rol por identificacion, descripcionTipoRol, codEmpresa y estados de persona empresa rol 
     * @param string $identificacion
     * @param array $descRol 
     * @param mixed $codEmpresa
     * @param array $estados
     * @return InfoPersonaEmpresaRol $InfoPersonaEmpresaRol
     *
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 23-12-2014
     */
    public function buscaClientesPorIdentificacionTipoRolEmpresaEstados($identificacion, $arrayDescRoles, $codEmpresa, $arrayEstados)
    {
        $query_string = "SELECT per
                        FROM 
                            schemaBundle:InfoPersona ip,
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol
                        WHERE 
                            per.empresaRolId= er.id AND
                            er.rolId = rol.id AND
                            rol.tipoRolId= trol.id AND
                            per.personaId = ip.id AND
                            ip.identificacionCliente = :identificacion AND
                            trol.descripcionTipoRol in (:descRol) AND
                            er.empresaCod = :codEmpresa 
                            AND per.estado in (:estado) ORDER BY per.estado DESC"
        ;
        $query = $this->_em->createQuery($query_string);
        $query->setParameter('identificacion', $identificacion);
        $query->setParameter('descRol', $arrayDescRoles);
        $query->setParameter('codEmpresa', $codEmpresa);
        $query->setParameter('estado', $arrayEstados);
        $InfoPersonaEmpresaRol = $query->getResult();

        return $InfoPersonaEmpresaRol;
    }

    /**
     * findPersonalByCriterios
     *
     * Método que retorna los jefes y empleados de cada departamento dependiendo del usuario logueado                                    
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayResultados
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-08-2015
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-10-2015 - Se cambia el nombre del método de 'findPersonal' a 'findPersonalByCriterios'
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 13-10-2015 - Se realiza una validación para descartar roles cuando se consulta por el 'nombreArea' 
     *                           igual a 'Tecnico', y se valida si los empleados están o no asignados a cuadrillas.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 26-10-2015 - Se modifica para que retorne el personal prestado a un coordinador específico.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 10-11-2015 - Se quita de la función la validación del campo 'ESTADO' de la tabla 'INFO_PERSONA'.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.5 16-11-2015 - Se modifica para agregar un filtro adicional para que retorne el personal de acuerdo a la especificación
     *                           del inicio de un cargo.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.6 24-11-2015 - Se modifica para que retorne el id persona empresa rol de la tabla 'InfoPersonaEmpresaRol' cuando se busca por 
     *                           cargo.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.7 14-12-2015 - Se modifica para que retorne el personal de acuerdo a la ciudad del usuario en sessión y los cargos del personal del
     *                           área técnica y para ello se separa la validación por departamento para que no sea un campo obliogatorio.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.8 02-02-2016 - Se modifica para que filtre la consulta por login del empleado
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.9 09-05-2016 - Se modifica para que cuando el nombreArea sea 'Tecnico' retorne a los empleados correspondientes a la misma ciudad
     *                           del usuario en sessión
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 2.0 28-06-2016 - Se agrega validación por tipo de rol en la consulta con la variable 'strTipoRol'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.0 30-06-2016 - Se corrige que cuando no se envía la variable 'strTipoRol' tome el arreglo con la descripción de 'Empleado'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 29-11-2016 - Se agrega validación para los roles que trabajarán como si tuvieran rol de jefes, que aparecerán tanto en el listado
     *                           de jefes y préstamo de cuadrillas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 12-01-2017 - Se modifica la validación por cantón cuando la petición sea enviada desde el listado de empleados en la opción 
     *                           Asignar Jefes Técnicos, filtrando por la región del usuario en sesión en lugar del cantón del usuario en sesión.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.3 14-03-2017 - Se verifica si el contenido del parámetro enviado "$arrayParametros['departamento']" es un array, caso contrario se
     *                           parsea el valor enviado a un array para realizar la consulta del personal que se encuentran dentro de esos 
     *                           departamentos enviados, es decir se cambia a que el query principal realice la consulta mediante un 'IN' para 
     *                           incluir al grupo de departamentos enviados como un arreglo.
     *                           Se agrega el parámetro "$arrayParametros['strNoAsignadosProducto']" para obtener al personal que no ha sido
     *                           marcado como GERENTE DE PRODUCTO de un producto específico.
     *                           Se agrega el parámetro "$arrayParametros['strAsignadosProducto']" para obtener al personal que ha sido marcado como
     *                           GERENTE DE PRODUCTO de un producto específico.
     *                           Se agrega el parámetro "$arrayParametros['strExceptoFreelanceComisionista']" para obtener al personal que no ha sido
     *                           marcado como freelance o comisionista.
     *                           Se agrega el parámetro "$arrayParametros['strSoloFreelanceComisionista']" para obtener al personal que ha sido
     *                           marcado como freelance o comisionista.
     *                           Se agrega la variable 'strSoloJefesTelcos' para retornar solo los jefes marcados como Jefes en telcos.
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.4 14-12-2018 - Se agrega validacion en caso de que sea asistente retorna los vendedores que esten o no asignados al asistente
     * 
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 2.5 30-12-2018 Se agrega en la consulta el usuario en sesion, IdPersonEmpresaRol
     *                         Adicional se agrega logica para retornar la info. de acuerdo
     *                         a la caracteristica de la persona en sesion por medio de las siguiente 
     *                         descripciones de caracteristica:
     *                         'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO'
     *                         Estos cambios solo aplican para Telconet
     *
     * @author : Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 2.6 30-11-2019 Implementación para obtener personas independiente del estado
     *                         en la tabla InfoPersonaEmpresaRol
     * 
     * @author : José Castillo <jmcastillo@telconet.ec>
     * @version 2.7 06-06-2023 Se omite la restricción de area para el listado de personal en jefes tecnicos
     *
     * Costo del Query: 96
     */
    public function findPersonalByCriterios($arrayParametros)
    {
        $arrayResultados            = array();

        $query = $this->_em->createQuery();
        $queryCount = $this->_em->createQuery();
        $strTipo               = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                   ? $arrayParametros['strTipoPersonal'] : 'Otros';
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                   ? $arrayParametros['strPrefijoEmpresa'] : '';
        $intIdPersonEmpresaRol = $arrayParametros['intIdPersonEmpresaRol'] ? intval($arrayParametros['intIdPersonEmpresaRol']) : 0;
        $strEstadoActivo       = 'Activo';
        $strDescripcion        = 'ASISTENTE_POR_CARGO';
        $boolGerenteProducto   = false;
        $strQueryNotIn =' NOT IN ' ;

        $strSelect = 'SELECT DISTINCT ip.nombres as nombres, ip.apellidos as apellidos, ip.id as id, iper.reportaPersonaEmpresaRolId,
                                      iper.id as idPersonaEmpresaRol, ar.esJefe, ip.login, ip.feCreacion ';
        $strFrom = 'FROM schemaBundle:InfoPersonaEmpresaRol iper,
                           schemaBundle:InfoPersona ip,
                           schemaBundle:AdmiRol ar,
                           schemaBundle:InfoEmpresaRol ier,
                           schemaBundle:InfoEmpresaGrupo ioegEmp, 
                           schemaBundle:AdmiTipoRol atr ';
        $strWhere = 'WHERE iper.personaId = ip.id
                        AND iper.empresaRolId = ier.id 
                        AND ar.id = ier.rolId
                        AND ar.tipoRolId = atr.id
                        AND ier.empresaCod = ioegEmp.id 
                        AND ioegEmp.id = :empresa
                        AND atr.descripcionTipoRol IN (:descripcionRol)
                        AND ioegEmp.estado not like :estadoEliminado 
                        AND ier.estado not like :estadoEliminado 
                        AND ar.estado not like :estadoEliminado 
                        AND atr.estado not like :estadoEliminado ';

        /**
         * Bloque que indica si se debe incluir todos los estados de la info persona empresa rol, con el fin de
         * encontrar a aquellos personas en estado diferente a 'Activo', ya sea, por salida de la empresa o cambio de cargo.
         */
        if(!isset($arrayParametros['strFiltrarTodosEstados']) || $arrayParametros['strFiltrarTodosEstados'] != 'S')
        {

            $strWhere .= ' AND iper.estado not like :estadoEliminado 
                           AND iper.estado not like :estadoInactivo 
                           AND iper.estado not like :estadoCancelado ';

            $query->setParameter('estadoEliminado', '%Eliminado%');
            $query->setParameter('estadoInactivo', '%Inactivo%');
            $query->setParameter('estadoCancelado', '%Cancelado%');

            $queryCount->setParameter('estadoEliminado', '%Eliminado%');
            $queryCount->setParameter('estadoInactivo', '%Inactivo%');
            $queryCount->setParameter('estadoCancelado', '%Cancelado%');
        }
        else
        {
            $strWhere .= ' AND iper.id = 
                            (select MAX(ipersq.id)
                                from schemaBundle:InfoPersonaEmpresaRol ipersq, 
                                     schemaBundle:InfoPersonaEmpresaRolCarac ipercsq 
                                where ipersq.personaId = iper.personaId AND 
                                      iper.departamentoId = ipersq.departamentoId AND 
                                      ipercsq.estado = iperc.estado AND 
                                      ipercsq.valor = iperc.valor ) ';
        }

        $strWhereCaracteristicaCargo = 'SELECT ac2.id
                                        FROM schemaBundle:AdmiCaracteristica ac2
                                        WHERE ac2.descripcionCaracteristica = :descripcionCaracteristica ';

        $strWhereCaracteristicaPrestamoEmpleado = 'SELECT ac3.id
                                                   FROM schemaBundle:AdmiCaracteristica ac3
                                                   WHERE ac3.descripcionCaracteristica = :descripcionPrestamoEmpleado ';
        
        /**
         * Bloque que indica el nombre de la característica de los cargos con la cual se debe realizar la búsqueda de los empleados.
         */
        $strCaracteristicaCargo = 'CARGO';
        if( isset($arrayParametros['caracteristicaCargo']) && !empty($arrayParametros['caracteristicaCargo']) )
        {
            $strCaracteristicaCargo = $arrayParametros['caracteristicaCargo'];
        }//( isset($arrayParametros['caracteristicaCargo']) && !empty($arrayParametros['caracteristicaCargo']) )
        
        
        /**
         * Bloque que retorna los empleados que han sido asignados o no con cargo 'Freelance' o 'Comisionistas'
         */
        if( ( isset($arrayParametros['strSoloFreelanceComisionista']) && $arrayParametros['strSoloFreelanceComisionista'] == "S" ) 
            || ( isset($arrayParametros['strExceptoFreelanceComisionista']) && $arrayParametros['strExceptoFreelanceComisionista'] == "S" ) )
        {
            $strWherePersonalExterno = " NOT IN ";
            
            if( isset($arrayParametros['strSoloFreelanceComisionista']) && $arrayParametros['strSoloFreelanceComisionista'] == "S" )
            {
                $strWherePersonalExterno = " IN ";
            }
            
            $strWhere .= 'AND iper.id '.$strWherePersonalExterno.' 
                          (
                                SELECT iperPersonalExterno.id
                                FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercPersonalExterno
                                JOIN ipercPersonalExterno.personaEmpresaRolId iperPersonalExterno
                                WHERE ipercPersonalExterno.estado = :strEstadoActivoPersonalExterno
                                AND ipercPersonalExterno.caracteristicaId = 
                                ( 
                                    SELECT acPersonalExterno.id
                                    FROM schemaBundle:AdmiCaracteristica acPersonalExterno
                                    WHERE acPersonalExterno.descripcionCaracteristica = :strCargoPersonalExterno
                                    AND acPersonalExterno.estado = :strEstadoActivoPersonalExterno
                                )
                                AND ipercPersonalExterno.valor IN
                                (
                                    SELECT apdPersonalExterno.id
                                    FROM schemaBundle:AdmiParametroDet apdPersonalExterno
                                    JOIN apdPersonalExterno.parametroId apcPersonalExterno
                                    WHERE apcPersonalExterno.estado = :strEstadoActivoPersonalExterno
                                    AND apdPersonalExterno.estado = :strEstadoActivoPersonalExterno
                                    AND apcPersonalExterno.nombreParametro = :strNombreParametroPersonalExterno
                                    AND apdPersonalExterno.valor4 = :strValorPersonalExterno
                                )
                           ) ';

            $query->setParameter('strEstadoActivoPersonalExterno',    'Activo');
            $query->setParameter('strNombreParametroPersonalExterno', 'GRUPO_ROLES_PERSONAL');
            $query->setParameter('strValorPersonalExterno',           'PERSONAL_EXTERNO');
            $query->setParameter('strCargoPersonalExterno',           $strCaracteristicaCargo);

            $queryCount->setParameter('strEstadoActivoPersonalExterno',    'Activo');
            $queryCount->setParameter('strNombreParametroPersonalExterno', 'GRUPO_ROLES_PERSONAL');
            $queryCount->setParameter('strValorPersonalExterno',           'PERSONAL_EXTERNO');
            $queryCount->setParameter('strCargoPersonalExterno',           $strCaracteristicaCargo);
        }//( isset($arrayParametros['strSoloFreelanceComisionista']) && $arrayParametros['strSoloFreelanceComisionista'] == "S" )...
        
        
        /**
         * Bloque que retorna los empleados que han sido asignados o no con un cargo en TELCOS asociados a un producto
         */
        if( ( isset($arrayParametros['strNoAsignadosProducto']) && !empty($arrayParametros['strNoAsignadosProducto']) ) 
            || ( isset($arrayParametros['strAsignadosProducto']) && !empty($arrayParametros['strAsignadosProducto']) ) )
        {
            $strProducto               = "";
            $strWhereAsignadosProducto = " NOT IN ";
            
            if( isset($arrayParametros['strAsignadosProducto']) && !empty($arrayParametros['strAsignadosProducto']))
            {
                $boolGerenteProducto = true;
                $strProducto               = $arrayParametros['strAsignadosProducto'];
                $strWhereAsignadosProducto = " IN ";
            }
            else
            {
                $strProducto = $arrayParametros['strNoAsignadosProducto'];
            }
            
            $strWhere .= 'AND iper.id '.$strWhereAsignadosProducto.' 
                          (
                                SELECT iperProducto.id
                                FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercProducto
                                JOIN ipercProducto.personaEmpresaRolId iperProducto
                                WHERE ipercProducto.estado = :strEstadoActivoCaracteristicaProducto
                                AND ipercProducto.caracteristicaId = 
                                ( 
                                    SELECT acCargoProducto.id
                                    FROM schemaBundle:AdmiCaracteristica acCargoProducto
                                    WHERE acCargoProducto.descripcionCaracteristica = :strCaracteristicaCargoProducto
                                    AND acCargoProducto.estado = :strEstadoActivoCaracteristicaProducto
                                )
                                AND ipercProducto.valor = :strProducto
                           ) ';

            $query->setParameter('strProducto',                           $strProducto);
            $query->setParameter('strEstadoActivoCaracteristicaProducto', 'Activo');
            $query->setParameter('strCaracteristicaCargoProducto',        'CARGO_GERENTE_PRODUCTO');

            $queryCount->setParameter('strProducto',                           $strProducto);
            $queryCount->setParameter('strEstadoActivoCaracteristicaProducto', 'Activo');
            $queryCount->setParameter('strCaracteristicaCargoProducto',        'CARGO_GERENTE_PRODUCTO');
        }//( isset($arrayParametros['strNoAsignadosProducto']) && !empty($arrayParametros['strNoAsignadosProducto']) )...

        if( isset($arrayParametros['departamento']) && !empty($arrayParametros['departamento']) )
        {
            if( !is_array($arrayParametros['departamento']) )
            {
                $arrayParametros['departamento'] = array($arrayParametros['departamento']);
            }
            
            $strFrom .= ', schemaBundle:AdmiDepartamento ad ';
            $strWhere .= 'AND iper.departamentoId = ad.id 
                           AND iper.departamentoId IN (:departamento) 
                           AND ad.estado not like :estadoEliminado ';

            $query->setParameter('departamento',      array_values($arrayParametros['departamento']));
            $queryCount->setParameter('departamento', array_values($arrayParametros['departamento']));
        }

        /*
         * Esta bloque retorna la información correspondientes a los jefes, en el cual se tienen las siguientes validaciones:
         *   - Si se requieren los jefes que esten asignados desde el NAF y el personal que haya sido asignado a un cargo de 
         *     'Supervisor', 'Lider' o 'Coordinador' en la tabla 'InfoPersonaEmpresaRolCarac'. Para ello se usará la variable
         *     $arrayParametros['jefeConCargo'] la cual tendrá los cargos de los jefes que se requieren buscar
         *   ó
         *   - Si sólo se requieren los jefes que esten asignados desde el NAF. Para ello se usará la variable 
         *     $arrayParametros['soloJefesNaf']
         */
        if(isset($arrayParametros['esJefe']))
        {
            if($arrayParametros['esJefe'] == 'S')
            {
                $strTmpWhere = '';

                if(isset($arrayParametros['jefeConCargo']))
                {
                    if($arrayParametros['jefeConCargo'])
                    {
                        $strTmpWhere .= 'AND ipercCargo.valor IN (:jefeConCargo) ';

                        $query->setParameter('jefeConCargo', array_values($arrayParametros['jefeConCargo']));

                        $queryCount->setParameter('jefeConCargo', array_values($arrayParametros['jefeConCargo']));
                    }
                }

                if(isset($arrayParametros['soloJefesNaf']))
                {
                    if($arrayParametros['soloJefesNaf'])
                    {
                        $strWhere .= 'AND ( ar.esJefe = :esJefe ';
                        
                        if(isset($arrayParametros['cargosFuncionanComoJefe']) && !empty($arrayParametros['cargosFuncionanComoJefe']))
                        {
                            $strWhere .= ' OR ar.descripcionRol IN ( :cargosFuncionanComoJefe ) ) ';
                            $query->setParameter("cargosFuncionanComoJefe", array_values($arrayParametros['cargosFuncionanComoJefe']) );
                            $queryCount->setParameter("cargosFuncionanComoJefe", array_values($arrayParametros['cargosFuncionanComoJefe']) );
                        }
                        else
                        {
                            $strWhere .= ") ";
                        }
                        
                        $query->setParameter('esJefe',      'S');
                        $queryCount->setParameter('esJefe', 'S');
                    }
                }
                elseif( isset($arrayParametros['strSoloJefesTelcos']) && $arrayParametros['strSoloJefesTelcos'] == "S" )
                {
                    $strFrom .= ", schemaBundle:InfoPersonaEmpresaRolCarac iperc ";
                    $strWhere .= 'AND iper.id IN (
                                                    SELECT(
                                                                 SELECT infoPersonaEmpresaRol.id
                                                                 FROM schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                                                                 WHERE infoPersonaEmpresaRol.id = ipercCargo.personaEmpresaRolId 
                                                           )
                                                    FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercCargo 
                                                    WHERE ipercCargo.estado = :estadoActivo
                                                      AND ipercCargo.caracteristicaId IN (' . $strWhereCaracteristicaCargo . ')
                                                    ' . $strTmpWhere . '
                                                 ) ';
                    
                    $query->setParameter('estadoActivo',              'Activo');
                    $query->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);

                    $queryCount->setParameter('estadoActivo',              'Activo');
                    $queryCount->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                }//( isset($arrayParametros['soloJefesNaf']) )
                else
                {
                    $strFrom .= ", schemaBundle:InfoPersonaEmpresaRolCarac iperc ";
                    $strWhere .= 'AND (ar.esJefe = :esJefe OR iper.id IN ( 
                                                                           SELECT(
                                                                                        SELECT infoPersonaEmpresaRol.id
                                                                                        FROM schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                                                                                        WHERE infoPersonaEmpresaRol.id = ipercCargo.personaEmpresaRolId 
                                                                                  )
                                                                           FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercCargo 
                                                                           WHERE ipercCargo.estado = :estadoActivo
                                                                             AND ipercCargo.caracteristicaId IN (' . $strWhereCaracteristicaCargo . ')
                                                                           ' . $strTmpWhere . '
                                                                         ) 
                                      ) ';
                    $query->setParameter('estadoActivo',              'Activo');
                    $query->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);

                    $queryCount->setParameter('estadoActivo',              'Activo');
                    $queryCount->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                    
                    $query->setParameter('esJefe',      'S');
                    $queryCount->setParameter('esJefe', 'S');
                }//( isset($arrayParametros['soloJefesNaf']) )
            }//( $arrayParametros['esJefe'] == 'S' )
        }//( isset($arrayParametros['esJefe']) )

        if( isset($arrayParametros['strEsAsistente']) && $arrayParametros['strEsAsistente']=='S' )
        {
            if(isset($arrayParametros['CargoVendedor']) && !empty($arrayParametros['CargoVendedor']))
            {
                $strWhere .= '  AND iper.reportaPersonaEmpresaRolId IS NOT NULL
                                AND iper.id IN ( 
                                                    SELECT(
                                                                SELECT infoPersonaEmpresaRol.id
                                                                FROM schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                                                                WHERE infoPersonaEmpresaRol.id = ipercCargo.personaEmpresaRolId 
                                                           )
                                                    FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercCargo 
                                                    WHERE ipercCargo.estado = :estadoActivo
                                                      AND ipercCargo.caracteristicaId = (' . $strWhereCaracteristicaCargo . ')
                                                      AND ipercCargo.valor = :CargoVendedor
                                                 ) ';
                $query->setParameter('estadoActivo',              'Activo');
                $query->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                $query->setParameter('CargoVendedor',              $arrayParametros['CargoVendedor']);
                $query->setParameter('descripcionCargoAsistente', 'ASISTENTE_POR_CARGO');

                $queryCount->setParameter('estadoActivo',              'Activo');
                $queryCount->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                $queryCount->setParameter('CargoVendedor',              $arrayParametros['CargoVendedor']);
                $queryCount->setParameter('descripcionCargoAsistente', 'ASISTENTE_POR_CARGO');
            }
            if (isset($arrayParametros['asistentesDe']) && !empty($arrayParametros['asistentesDe']))
            {
                $strQueryNotIn =' IN ' ;
                $strFrom      .= " , schemaBundle:InfoPersonaEmpresaRolCarac IPERC 
                                   , schemaBundle:AdmiCaracteristica ac ";
                $strWhere     .= " AND IPERC.valor         = ip.id
                                   AND IPERC.caracteristicaId=ac.id 
                                   AND IPERC.estado = :estadoActivo
                                   AND ac.descripcionCaracteristica = :descripcionCargoAsistente 
                                   AND ac.estado = :estadoActivo ";

                $query->setParameter('estadoActivo',              'Activo');
                $query->setParameter('descripcionCargoAsistente', 'ASISTENTE_POR_CARGO');

                $queryCount->setParameter('estadoActivo',              'Activo');
                $queryCount->setParameter('descripcionCargoAsistente', 'ASISTENTE_POR_CARGO');
            }            
        }
        if(isset($arrayParametros['exceptoUsr']))
        {
            if($arrayParametros['exceptoUsr'])
            {
                foreach($arrayParametros['exceptoUsr'] as $strExceptoUsr)
                {
                    if($strExceptoUsr)
                    {
                        if( isset($arrayParametros['strEsAsistente']) && $arrayParametros['strEsAsistente']=='S') 
                        {
                            $strSelect    .=' ,( select ias.tiempoDias
                                                from schemaBundle:InfoAsignacion ias
                                                where ias.personaEmpresaRolIdVend=iper.id
                                                and ias.personaEmpresaRolIdAsist = :exceptoUsr_' . $strExceptoUsr . '
                                                and ias.estado = :estadoActivo
                                                )as strTiempoLimite ';
                            $strWhere.=' AND ip.id '.$strQueryNotIn.'
                                                        (SELECT ipvend.id
                                                        FROM schemaBundle:InfoPersona ipvend,
                                                          schemaBundle:InfoPersonaEmpresaRolCarac ipercvend,
                                                          schemaBundle:AdmiCaracteristica acvend
                                                        WHERE ipvend.id                            = ipercvend.valor
                                                        AND   acvend.id                            = ipercvend.caracteristicaId
                                                        AND   ipercvend.personaEmpresaRolId        = :exceptoUsr_' . $strExceptoUsr . '
                                                        AND   acvend.descripcionCaracteristica     = :descripcionCargoAsistente 
                                                        AND   acvend.estado                        = :estadoActivo
                                                        AND   ipercvend.estado                     = :estadoActivo
                                                        ) ';
                            $query->setParameter('descripcionCargoAsistente', 'ASISTENTE_POR_CARGO');
                            $query->setParameter('estadoActivo',              'Activo');

                            $queryCount->setParameter('descripcionCargoAsistente', 'ASISTENTE_POR_CARGO');
                            $queryCount->setParameter('estadoActivo',              'Activo');
                        }
                        $strWhere .= 'AND iper.id <> :exceptoUsr_' . $strExceptoUsr . ' ';

                        $query->setParameter('exceptoUsr_' . $strExceptoUsr, $strExceptoUsr);

                        $queryCount->setParameter('exceptoUsr_' . $strExceptoUsr, $strExceptoUsr);
                    }
                }
            }
        }

        /*
         * Esta bloque retorna la información correspondientes al personal que no esté asignado a ningún tipo de Jefe,
         * en el cual se tienen las siguientes validaciones:
         *   - Además de retornar el personal que no tenga asignación a ningún tipo de Jefe, también se valida que 
         *     no retorne el personal de rango superior al cargo del Jefe al cual se le van asignar los empleados.
         */
        if(isset($arrayParametros['noAsignados']))
        {
            if($arrayParametros['noAsignados'])
            {
                $strWhere .= 'AND (iper.reportaPersonaEmpresaRolId IS NULL OR iper.reportaPersonaEmpresaRolId = :noAsignado) ';

                $query->setParameter('noAsignado', '');

                $queryCount->setParameter('noAsignado', '');

                if(isset($arrayParametros['jefeConCargo']))
                {
                    if($arrayParametros['jefeConCargo'])
                    {
                        if(in_array('Jefe', $arrayParametros['jefeConCargo']))
                        {
                            $strWhere .= 'AND ( ar.esJefe <> :esJefe OR ar.esJefe IS NULL ) ';

                            $query->setParameter('esJefe', 'S');

                            $queryCount->setParameter('esJefe', 'S');
                        }

                        $strWhere .= 'AND iper.id NOT IN ( 
                                                            SELECT(
                                                                        SELECT infoPersonaEmpresaRol.id
                                                                        FROM schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                                                                        WHERE infoPersonaEmpresaRol.id = ipercCargo.personaEmpresaRolId 
                                                                   )
                                                            FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercCargo 
                                                            WHERE ipercCargo.estado = :estadoActivo
                                                              AND ipercCargo.caracteristicaId = (' . $strWhereCaracteristicaCargo . ')
                                                              AND ipercCargo.valor IN (:jefeConCargo)
                                                         ) ';
                        
                        $query->setParameter('estadoActivo',              'Activo');
                        $query->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                        $query->setParameter('jefeConCargo',              array_values($arrayParametros['jefeConCargo']));

                        $queryCount->setParameter('estadoActivo',              'Activo');
                        $queryCount->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                        $queryCount->setParameter('jefeConCargo',              array_values($arrayParametros['jefeConCargo']));
                    }//( $arrayParametros['jefeConCargo'] )
                }//( isset($arrayParametros['jefeConCargo']) )
            }//( $arrayParametros['noAsignados'] )
        }//( isset($arrayParametros['noAsignados']) )

        if(isset($arrayParametros['asignadosA']))
        {
            if($arrayParametros['asignadosA'])
            {
                $strWhere .= 'AND ( iper.reportaPersonaEmpresaRolId = :asignadosA
                                     OR iper.id IN (
                                                        SELECT (
                                                                    SELECT infoPersonaEmpresaRol.id
                                                                    FROM schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                                                                    WHERE infoPersonaEmpresaRol.id = ipercPrestado.personaEmpresaRolId 
                                                                )
                                                        FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercPrestado 
                                                        WHERE ipercPrestado.estado = :estadoActivoCaracteristica
                                                          AND ipercPrestado.caracteristicaId = (' . $strWhereCaracteristicaPrestamoEmpleado . ')
                                                          AND ipercPrestado.valor = :asignadosA
                                                   ) 
                                    ) ';

                $query->setParameter('estadoActivoCaracteristica', 'Activo');
                $query->setParameter('asignadosA', $arrayParametros['asignadosA']);
                $query->setParameter('descripcionPrestamoEmpleado', 'PRESTAMO EMPLEADO');

                $queryCount->setParameter('estadoActivoCaracteristica', 'Activo');
                $queryCount->setParameter('asignadosA', $arrayParametros['asignadosA']);
                $queryCount->setParameter('descripcionPrestamoEmpleado', 'PRESTAMO EMPLEADO');
            }
        }

        if(isset($arrayParametros['criterios']))
        {
            if(isset($arrayParametros['criterios']['login']))
            {
                if($arrayParametros['criterios']['login'])
                {
                    $strWhere .= "AND ip.login = :login ";

                    $query->setParameter('login', trim($arrayParametros['criterios']['login']));

                    $queryCount->setParameter('login', trim($arrayParametros['criterios']['login']));
                }
            }

            if(isset($arrayParametros['criterios']['cargoSimilar']))
            {
                if($arrayParametros['criterios']['cargoSimilar'])
                {
                    if(is_array($arrayParametros['criterios']['cargoSimilar']))
                    {
                        $strWhere .= "AND ar.descripcionRol IN (:cargoSimilar) ";

                        $query->setParameter('cargoSimilar', array_values($arrayParametros['criterios']['cargoSimilar']));

                        $queryCount->setParameter('cargoSimilar', array_values($arrayParametros['criterios']['cargoSimilar']));
                    }
                    else
                    {
                        $strWhere .= "AND (ar.descripcionRol LIKE :cargoSimilar ";
                        $query->setParameter('cargoSimilar', trim($arrayParametros['criterios']['cargoSimilar']) . '%');
                        $queryCount->setParameter('cargoSimilar', trim($arrayParametros['criterios']['cargoSimilar']) . '%');
                        
                        if(isset($arrayParametros['cargosFuncionanComoJefe']) && !empty($arrayParametros['cargosFuncionanComoJefe']))
                        {
                            $strWhere .= ' OR ar.descripcionRol IN ( :cargosFuncionanComoJefe ) ) ';
                            $query->setParameter("cargosFuncionanComoJefe", array_values($arrayParametros['cargosFuncionanComoJefe']) );
                            $queryCount->setParameter("cargosFuncionanComoJefe", array_values($arrayParametros['cargosFuncionanComoJefe']) );
                        }
                        else
                        {
                            $strWhere .= ") ";
                        }
                    }
                }
            }

            if(isset($arrayParametros['criterios']['nombres']))
            {
                if($arrayParametros['criterios']['nombres'])
                {
                    $strWhere .= "AND ip.nombres like :nombres ";

                    $query->setParameter('nombres', '%' . strtoupper(trim($arrayParametros['criterios']['nombres']) . '%'));

                    $queryCount->setParameter('nombres', '%' . strtoupper(trim($arrayParametros['criterios']['nombres']) . '%'));
                }
            }

            if(isset($arrayParametros['criterios']['apellidos']))
            {
                if($arrayParametros['criterios']['apellidos'])
                {
                    $strWhere .= "AND ip.apellidos like :apellidos ";

                    $query->setParameter('apellidos', '%' . strtoupper(trim($arrayParametros['criterios']['apellidos']) . '%'));

                    $queryCount->setParameter('apellidos', '%' . strtoupper(trim($arrayParametros['criterios']['apellidos']) . '%'));
                }
            }

            if(isset($arrayParametros['criterios']['nombreEmpleado']))
            {
                if($arrayParametros['criterios']['nombreEmpleado'])
                {
                    $strWhere .= "AND CONCAT(ip.nombres, CONCAT(' ', ip.apellidos)) LIKE :nombreEmpleado ";

                    $query->setParameter('nombreEmpleado', '%' . strtoupper(trim($arrayParametros['criterios']['nombreEmpleado']) . '%'));

                    $queryCount->setParameter('nombreEmpleado', '%' . strtoupper(trim($arrayParametros['criterios']['nombreEmpleado']) . '%'));
                }
            }

            if(isset($arrayParametros['criterios']['cargo']))
            {
                $strCargo = $arrayParametros['criterios']['cargo'];

                if($strCargo)
                {
                    if($strCargo == 'Empleado' || $strCargo == 'Jefe' || $strCargo == 'Vendedor')
                    {
                        if($strCargo == 'Empleado' || $strCargo == 'Vendedor')
                        {
                            $strWhere .= 'AND ( ar.esJefe <> :esJefe OR ar.esJefe IS NULL ) ';
                        }
                        else
                        {
                            $strWhere .= 'AND ar.esJefe = :esJefe ';
                        }

                        $strWhere .= 'AND iper.id NOT IN ( 
                                                            SELECT(
                                                                        SELECT infoPersonaEmpresaRol.id
                                                                        FROM schemaBundle:InfoPersonaEmpresaRol infoPersonaEmpresaRol
                                                                        WHERE infoPersonaEmpresaRol.id = ipercCargo.personaEmpresaRolId 
                                                                   )
                                                            FROM schemaBundle:InfoPersonaEmpresaRolCarac ipercCargo 
                                                            WHERE ipercCargo.estado = :estadoActivo
                                                              AND ipercCargo.caracteristicaId = (' . $strWhereCaracteristicaCargo . ')
                                                          ) ';

                        $query->setParameter('esJefe',                    'S');
                        $query->setParameter('estadoActivo',              'Activo');
                        $query->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);

                        $queryCount->setParameter('esJefe',                    'S');
                        $queryCount->setParameter('estadoActivo',              'Activo');
                        $queryCount->setParameter('descripcionCaracteristica', $strCaracteristicaCargo);
                    }
                    else
                    {
                        if($arrayParametros['nombreArea'] == 'Comercial')
                        {
                            $strFrom .= ", schemaBundle:InfoPersonaEmpresaRolCarac iperc ";

                            $strWhere .= 'AND iperc.personaEmpresaRolId = iper.id 
                                          AND iperc.estado = :estadoActivo
                                          AND iperc.valor = :cargo ';

                            $query->setParameter('cargo', $strCargo);
                            $query->setParameter('estadoActivo', 'Activo');

                            $queryCount->setParameter('cargo', $strCargo);
                            $queryCount->setParameter('estadoActivo', 'Activo');
                        }
                        elseif($arrayParametros['nombreArea'] == 'Tecnico')
                        {
                            $strWhere .= 'AND ar.descripcionRol = :cargo ';

                            $query->setParameter('cargo', $strCargo);

                            $queryCount->setParameter('cargo', $strCargo);
                        }
                    }//( $strCargo == 'Empleado' || $strCargo == 'Jefe' || $strCargo == 'Vendedor' )
                }//( $strCargo )
            }//( isset($arrayParametros['criterios']['cargo']) )
        }//( isset($arrayParametros['criterios']) )


        if(isset($arrayParametros['nombreArea']))
        {
            if($arrayParametros['nombreArea'] == 'Tecnico')
            {
                $strWhere .= 'AND ar.descripcionRol NOT IN (:rolesNoIncluidos) ';

                $query->setParameter('rolesNoIncluidos', array_values($arrayParametros['rolesNoIncluidos']));

                $queryCount->setParameter('rolesNoIncluidos', array_values($arrayParametros['rolesNoIncluidos']));
            }
        }


        if(isset($arrayParametros['sinCuadrilla']))
        {
            if($arrayParametros['sinCuadrilla'] == 'S')
            {
                $strWhere .= 'AND iper.cuadrillaId IS NULL ';
            }
        }


        if(isset($arrayParametros['intIdCuadrilla']))
        {
            if($arrayParametros['intIdCuadrilla'])
            {
                $strWhere .= 'AND iper.cuadrillaId = :intIdCuadrilla ';

                $query->setParameter('intIdCuadrilla', $arrayParametros['intIdCuadrilla']);

                $queryCount->setParameter('intIdCuadrilla', $arrayParametros['intIdCuadrilla']);
            }
        }
        
        
        if(!empty($arrayParametros['strTipoRol']))
        {
            $query->setParameter('descripcionRol',      array_values($arrayParametros['strTipoRol']));
            $queryCount->setParameter('descripcionRol', array_values($arrayParametros['strTipoRol']));
        }
        else
        {
            $query->setParameter('descripcionRol',      array_values(array('Empleado')));
            $queryCount->setParameter('descripcionRol', array_values(array('Empleado')));
        }

        if( ($strPrefijoEmpresa == 'TN' && $strTipo !== 'Otros' ) && ( $strTipo !=='GERENTE_VENTAS' && !$boolGerenteProducto ) )
        {
            if( $strTipo == 'SUBGERENTE' )
            {
                $strWhere .= " AND ip.login IN
                                (SELECT ipvendSub.login
                                    FROM schemaBundle:InfoPersona ipvendSub ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.estado                        = :strEstadoActivo
                                    AND ipervend.personaId                   = ipvendSub.id
                                    AND ipvendSub.estado                        = :strEstadoActivo
                                    AND (ipervend.reportaPersonaEmpresaRolId = :intIdPersonEmpresaRol
                                    OR ipervend.id                           = :intIdPersonEmpresaRol))
                              ";
            }
            elseif( $strTipo == 'ASISTENTE' )
            {
                $strWhere .= " AND ip.login IN
                                (select ipvend.login
                                    from schemaBundle:InfoPersonaEmpresaRolCarac ipercvend ,
                                      schemaBundle:AdmiCaracteristica acvend ,
                                      schemaBundle:InfoPersona ipvend
                                WHERE ipercvend.personaEmpresaRolId          = :intIdPersonEmpresaRol
                                        and acvend.id                        = ipercvend.caracteristicaId
                                        and ipvend.id                        = ipercvend.valor
                                        AND acvend.descripcionCaracteristica = :strDescripcion
                                        AND acvend.estado                    = :strEstadoActivo
                                        AND ipercvend.estado                 = :strEstadoActivo
                                        AND ipvend.estado                    = :strEstadoActivo )
                              ";
                $query->setParameter('strDescripcion', $strDescripcion);
                $queryCount->setParameter('strDescripcion', $strDescripcion);
            }
            elseif( $strTipo == 'VENDEDOR' )
            {
                $strWhere .= " AND ip.login IN
                                (select ipvend.login
                                    FROM schemaBundle:InfoPersona ipvend ,
                                    schemaBundle:InfoPersonaEmpresaRol ipervend
                                WHERE ipervend.id          = :intIdPersonEmpresaRol
                                    AND ipervend.personaId = ipvend.id
                                    AND ipervend.estado    = :strEstadoActivo
                                    AND ipvend.estado      = :strEstadoActivo)
                              ";
            }
            $query->setParameter('strEstadoActivo', $strEstadoActivo);
            $query->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);

            $queryCount->setParameter('strEstadoActivo', $strEstadoActivo);
            $queryCount->setParameter('intIdPersonEmpresaRol', $intIdPersonEmpresaRol);
        }

        $strOrderBy = 'ORDER BY ip.nombres, ip.apellidos';

        $strSql = $strSelect . $strFrom . $strWhere . $strOrderBy;

        $query->setParameter('estadoEliminado', '%Eliminado%');
        $query->setParameter('empresa', $arrayParametros['empresa']);

        $queryCount->setParameter('estadoEliminado', '%Eliminado%');
        $queryCount->setParameter('empresa', $arrayParametros['empresa']);

        $query->setDQL($strSql);

        if(isset($arrayParametros['inicio']))
        {
            if($arrayParametros['inicio'])
            {
                $query->setFirstResult($arrayParametros['inicio']);
            }
        }

        if(isset($arrayParametros['limite']))
        {
            if($arrayParametros['limite'])
            {
                $query->setMaxResults($arrayParametros['limite']);
            }
        }

        $arrayTmpDatos = $query->getResult();

        if(!isset($arrayParametros['strFiltrarTodosEstados']) || $arrayParametros['strFiltrarTodosEstados'] != 'S')
        {
            $strSelectCount = 'SELECT COUNT( DISTINCT ip.id ) ';
        }
        else
        {
            $strSelectCount = 'SELECT COUNT( DISTINCT iper.id ) ';
        }

        $strSqlCount = $strSelectCount . $strFrom . $strWhere;

        $queryCount->setDQL($strSqlCount);

        $intTotal = $queryCount->getSingleScalarResult();

        $arrayResultados['registros'] = $arrayTmpDatos;
        $arrayResultados['total'] = $intTotal;

        return $arrayResultados;
    }

    /**
     * findHistorialObservacionByCriterios
     *
     * Método que retorna el último historial de una persona de acuerdo a los parametros ingresados por el usuario                                    
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayResultado
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-10-2015
     */
    public function findHistorialObservacionByCriterios($arrayParametros)
    {
        $query = $this->_em->createQuery();

        $strSelect = 'SELECT iperh.observacion ';
        $strFrom = 'FROM schemaBundle:InfoPersonaEmpresaRol iper,
                           schemaBundle:InfoPersonaEmpresaRolHisto iperh ';
        $strWhere = 'WHERE iperh.personaEmpresaRolId = iper.id
                        AND iperh.id = (
                                            SELECT MAX ( iperh2.id )
                                            FROM schemaBundle:InfoPersonaEmpresaRolHisto iperh2
                                            WHERE iperh2.personaEmpresaRolId = :usuarioRolId
                                              AND iperh2.motivoId = :motivoId
                                        ) ';

        $query->setParameter('usuarioRolId', $arrayParametros['usuarioRolId']);
        $query->setParameter('motivoId', $arrayParametros['motivoId']);

        $strSql = $strSelect . $strFrom . $strWhere;

        $query->setDQL($strSql);

        $arrayResultado = $query->getSingleResult();

        return $arrayResultado;
    }

    /**
     * getChoferAsignadoACuadrilla
     *
     * Obtiene el chofer asignado a una determinada cuadrilla.                                    
     *      
     * @param integer $idCuadrilla
     * 
     * @return array $response
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-02-2016
     * 
     */
    public function getChoferAsignadoACuadrilla($idCuadrilla)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);

            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);

            $strChofer = 'Chofer';
            $strDescripcionCaracteristica = 'CARGO';
            $strEstadoActivo = 'Activo';
            $strSelectCount = "SELECT COUNT (*) AS TOTAL ";
            $strSelect = "   SELECT DISTINCT per.ID_PERSONA_ROL,p.ID_PERSONA, p.IDENTIFICACION_CLIENTE,p.NOMBRES,p.APELLIDOS ";
            $strFromAndWhere = " 
                            FROM DB_COMERCIAL.ADMI_CUADRILLA ac 
                            LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per ON per.CUADRILLA_ID=ac.ID_CUADRILLA
                            LEFT JOIN DB_COMERCIAL.INFO_PERSONA p ON per.PERSONA_ID = p.ID_PERSONA
                            LEFT JOIN DB_COMERCIAL.INFO_EMPRESA_ROL er ON per.EMPRESA_ROL_ID = er.ID_EMPRESA_ROL
                            LEFT JOIN DB_GENERAL.ADMI_ROL r ON er.ROL_ID =r.ID_ROL
                            LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC perc ON per.ID_PERSONA_ROL = perc.PERSONA_EMPRESA_ROL_ID
                            LEFT JOIN DB_COMERCIAL.ADMI_CARACTERISTICA c ON c.ID_CARACTERISTICA = perc.CARACTERISTICA_ID
                            WHERE 
                            (
                                (
                                  (er.ROL_ID is not null and r.DESCRIPCION_ROL= :strChofer) 
                                  OR (  per.ID_PERSONA_ROL is not null and perc.VALOR= :strChofer 
                                        AND c.DESCRIPCION_CARACTERISTICA = :descripcionCaracteristica 
                                        AND c.ESTADO = :estadoActivo  AND perc.ESTADO = :estadoActivo 
                                     )
                                )
                            ) AND ac.ID_CUADRILLA=:idCuadrilla ";



            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRolChofer', 'integer');
            $rsm->addScalarResult('ID_PERSONA', 'idPersonaChofer', 'integer');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionChofer', 'string');
            $rsm->addScalarResult('NOMBRES', 'nombresChofer', 'string');
            $rsm->addScalarResult('APELLIDOS', 'apellidosChofer', 'string');

            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $ntvQuery->setParameter('idCuadrilla', $idCuadrilla);
            $ntvQuery->setParameter('descripcionCaracteristica', $strDescripcionCaracteristica);
            $ntvQuery->setParameter('estadoActivo', $strEstadoActivo);
            $ntvQuery->setParameter('strChofer', $strChofer);

            $ntvQueryCount->setParameter('idCuadrilla', $idCuadrilla);
            $ntvQueryCount->setParameter('descripcionCaracteristica', $strDescripcionCaracteristica);
            $ntvQueryCount->setParameter('estadoActivo', $strEstadoActivo);
            $ntvQueryCount->setParameter('strChofer', $strChofer);

            $strQuery = $strSelect . $strFromAndWhere;
            $ntvQuery->setSQL($strQuery);
            $arrayResultado = $ntvQuery->getResult();

            $strQueryCount = $strSelectCount . $strFromAndWhere;
            $ntvQueryCount->setSQL($strQueryCount);
            $intTotal = $ntvQueryCount->getSingleScalarResult();

            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * getResultadoChoferAsignacionVehicularPredefinida
     *
     * Consulta el chofer de una asignación predefinida                               
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayRespuesta['total','resultado']
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-08-2016
     * 
     */
    public function getResultadoChoferAsignacionVehicularPredefinida($arrayParametros)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsmCount = new ResultSetMappingBuilder($this->_em);


            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $strSelect = " SELECT detalleAsignacion.PERSONA_EMPRESA_ROL_ID, detalleAsignacion.REF_ASIGNADO_NOMBRE ";

            $strFrom = " FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD detalleSolicitud
                                    INNER JOIN DB_SOPORTE.INFO_DETALLE detalle 
                                        ON detalle.DETALLE_SOLICITUD_ID=detalleSolicitud.ID_DETALLE_SOLICITUD
                                    INNER JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION detalleAsignacion
                                        ON detalleAsignacion.DETALLE_ID=detalle.ID_DETALLE
                                    INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOL_CARACT detalleSolCaractDepartamento
                                        ON detalleSolCaractDepartamento.DETALLE_SOLICITUD_ID=detalleSolicitud.ID_DETALLE_SOLICITUD";


            if(isset($arrayParametros['intIdZonaCuadrilla']))
            {
                if($arrayParametros['intIdZonaCuadrilla'])
                {
                    $strFrom .= "   INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOL_CARACT detalleSolCaractZona
                                    ON detalleSolCaractZona.DETALLE_SOLICITUD_ID = detalleSolicitud.ID_DETALLE_SOLICITUD
                                        AND detalleSolCaractZona.CARACTERISTICA_ID=:idCaracteristicaZonaPredefinida
                                        AND detalleSolCaractZona.VALOR = :idZonaCuadrilla ";

                    $ntvQuery->setParameter('idCaracteristicaZonaPredefinida', $arrayParametros['intIdCaracteristicaZonaPredefinida']);
                    $ntvQueryCount->setParameter('idCaracteristicaZonaPredefinida', $arrayParametros['intIdCaracteristicaZonaPredefinida']);

                    $ntvQuery->setParameter('idZonaCuadrilla', $arrayParametros['intIdZonaCuadrilla']);
                    $ntvQueryCount->setParameter('idZonaCuadrilla', $arrayParametros['intIdZonaCuadrilla']);
                }
            }

            if(isset($arrayParametros['intIdTareaCuadrilla']))
            {
                if($arrayParametros['intIdTareaCuadrilla'])
                {
                    $strFrom .= "   INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOL_CARACT detalleSolCaractTarea
                                        ON detalleSolCaractTarea.DETALLE_SOLICITUD_ID = detalleSolicitud.ID_DETALLE_SOLICITUD
                                            AND detalleSolCaractTarea.CARACTERISTICA_ID=:idCaracteristicaTareaPredefinida
                                            AND detalleSolCaractTarea.VALOR = :idTareaCuadrilla ";


                    $ntvQuery->setParameter('idCaracteristicaTareaPredefinida', $arrayParametros['intIdCaracteristicaTareaPredefinida']);
                    $ntvQueryCount->setParameter('idCaracteristicaTareaPredefinida', $arrayParametros['intIdCaracteristicaTareaPredefinida']);

                    $ntvQuery->setParameter('idTareaCuadrilla', $arrayParametros['intIdTareaCuadrilla']);
                    $ntvQueryCount->setParameter('idTareaCuadrilla', $arrayParametros['intIdTareaCuadrilla']);
                }
            }

            $strWhere = " WHERE detalleSolicitud.ID_DETALLE_SOLICITUD = :intIdDetalleSolicitud
                                AND detalleSolicitud.ESTADO = :strEstadoActivo 
                                AND detalleSolCaractDepartamento.CARACTERISTICA_ID = :intIdCaractDepartamentoPredefinido
                                AND detalleSolCaractDepartamento.VALOR = :intIdDepartamentoCuadrilla
                                AND detalleSolCaractDepartamento.ESTADO = :strEstadoActivo ";

            $rsm->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'idPersonaEmpresaRolChofer', 'integer');
            $rsm->addScalarResult('REF_ASIGNADO_NOMBRE', 'chofer', 'string');

            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $ntvQuery->setParameter('intIdDetalleSolicitud', $arrayParametros['intIdDetalleSolicitud']);
            $ntvQueryCount->setParameter('intIdDetalleSolicitud', $arrayParametros['intIdDetalleSolicitud']);
            
            $ntvQuery->setParameter('intIdCaractDepartamentoPredefinido', $arrayParametros['intIdCaracteristicaDepartamentoPredefinido']);
            $ntvQueryCount->setParameter('intIdCaractDepartamentoPredefinido', $arrayParametros['intIdCaracteristicaDepartamentoPredefinido']);

            $ntvQuery->setParameter('intIdDepartamentoCuadrilla', $arrayParametros['intIdDepartamentoCuadrilla']);
            $ntvQueryCount->setParameter('intIdDepartamentoCuadrilla', $arrayParametros['intIdDepartamentoCuadrilla']);

            $ntvQuery->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);
            $ntvQueryCount->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);


            $strSqlPrincipal = $strSelect . $strFrom . $strWhere;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
        }
        catch(\Exception $e)
        {

            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }
    
    /**
     * Documentación para el método 'getResultadoSupervisoresACargo'
     *
     * Método que obtiene la lista de supervisores por jefe
     *      
     * @param Integer $intIdJefe IdPersonaEmpresaRol del Jefe.
     * 
     * @return Array $arrayResult Listado de supervisores.
     * 
     * costoQuery: 11
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 04-11-2015
     */
    public function getResultadoSupervisoresACargo($intIdJefe)
    {
        $query     = $this->_em->createQuery();
        $strQuery = " SELECT per.id, pe.apellidos, pe.nombres 
                      FROM schemaBundle:InfoPersonaEmpresaRol      per,
                           schemaBundle:InfoPersona                pe, 
                           schemaBundle:InfoPersonaEmpresaRolCarac perc,
                           schemaBundle:AdmiCaracteristica         ac
                      WHERE pe.id                          = per.personaId
                        AND perc.personaEmpresaRolId       = per.id
                        AND ac.id                          = perc.caracteristicaId
                        AND perc.valor                     = :VALOR
                        AND perc.estado                  in (:ESTADOS)
                        AND ac.descripcionCaracteristica   = :CARACTERISTICA
                        AND per.reportaPersonaEmpresaRolId = :IDPERSONAEMPRESAROL
                      ORDER BY pe.apellidos, pe.nombres ";
        $query->setParameter('VALOR',               'Supervisor');
        $query->setParameter('ESTADOS',             array('Activo', 'Modificado'));
        $query->setParameter('CARACTERISTICA',      'CARGO');
        $query->setParameter('IDPERSONAEMPRESAROL', $intIdJefe);
        $query->setDQL($strQuery);
        return $query->getResult();
    }
    
    /**
     * getResultadoChoferAsignacionVehicularPredefinida
     *
     * Consulta el chofer de una asignación predefinida                               
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayRespuesta['total','resultado']
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     */
    public function getChoferAsignacionPredefinida($arrayParametros)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);

            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);


            $strEstadoActivo = 'Activo';
            $strSelectCount = "SELECT COUNT (*) AS TOTAL ";
            $strSelect = "   SELECT DISTINCT per.ID_PERSONA_ROL,p.ID_PERSONA, p.IDENTIFICACION_CLIENTE,p.NOMBRES,p.APELLIDOS ";
            $strFromAndWhere = " 
                            FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD detalleSolicitud 
                            
                            INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOL_HIST solHist
                                ON detalleSolicitud.ID_DETALLE_SOLICITUD=solHist.DETALLE_SOLICITUD_ID  
                                
                            INNER JOIN DB_SOPORTE.INFO_DETALLE detalle 
                                ON detalle.DETALLE_SOLICITUD_ID=detalleSolicitud.ID_DETALLE_SOLICITUD 
                                
                            INNER JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION detalleAsignacion
                                ON detalleAsignacion.DETALLE_ID=detalle.ID_DETALLE

                            INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per ON per.ID_PERSONA_ROL=detalleAsignacion.PERSONA_EMPRESA_ROL_ID
                            INNER JOIN DB_COMERCIAL.INFO_PERSONA p ON per.PERSONA_ID = p.ID_PERSONA
                                AND detalleSolicitud.ELEMENTO_ID =:idElemento 
                                WHERE detalleSolicitud.TIPO_SOLICITUD_ID = :idTipoSolicitudPredefinida
                                    AND detalleSolicitud.ESTADO = :estadoActivo 
                                    AND solHist.ID_SOLICITUD_HISTORIAL=
                                        (
                                            SELECT MAX(solHistorialMax.ID_SOLICITUD_HISTORIAL) 
                                            FROM DB_COMERCIAL.INFO_DETALLE_SOL_HIST solHistorialMax
                                            WHERE solHistorialMax.DETALLE_SOLICITUD_ID=solHist.DETALLE_SOLICITUD_ID
                                        ) 
                                     ";

            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRolChofer', 'integer');
            $rsm->addScalarResult('ID_PERSONA', 'idPersonaChofer', 'integer');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionChofer', 'string');
            $rsm->addScalarResult('NOMBRES', 'nombresChofer', 'string');
            $rsm->addScalarResult('APELLIDOS', 'apellidosChofer', 'string');


            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $ntvQuery->setParameter('idElemento', $arrayParametros["idElemento"]);
            $ntvQuery->setParameter('estadoActivo', $strEstadoActivo);
            $ntvQuery->setParameter('idTipoSolicitudPredefinida', $arrayParametros["idTipoSolicitudPredefinida"]);


            $ntvQueryCount->setParameter('idElemento', $arrayParametros["idElemento"]);
            $ntvQueryCount->setParameter('estadoActivo', $strEstadoActivo);
            $ntvQueryCount->setParameter('idTipoSolicitudPredefinida', $arrayParametros["idTipoSolicitudPredefinida"]);

            $strQuery = $strSelect . $strFromAndWhere;


            $ntvQuery->setSQL($strQuery);
            $arrayResultado = $ntvQuery->getResult();

            $strQueryCount = $strSelectCount . $strFromAndWhere;
            $ntvQueryCount->setSQL($strQueryCount);
            $intTotal = $ntvQueryCount->getSingleScalarResult();

            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

    /*     * **********************************Asignación Vehicular Predefinida*********************** */

    /**
     * getJSONChoferesPredefinidosDisponibles
     *
     * Obtiene el json de los choferes predefinidos que se encuentran disponibles                                  
     *      
     * @param array $arrayParametros
     * 
     * @return $jsonData
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2016 Se realiza validación para obtener los choferes de acuerdo al horario seleccionado
     * 
     */
    public function getJSONChoferesPredefinidosDisponibles($arrayParametros)
    {
        $arrayEncontrados = array();
        
        if($arrayParametros['boolErrorHoras'])
        {
            $total = 0;
        }
        else
        {

            $arrayResultado = $this->getResultadoChoferesPredefinidosDisponibles($arrayParametros);
            $resultado = $arrayResultado['resultado'];
            $intTotal = $arrayResultado['total'];
            $total = 0;

            if($resultado)
            {
                $total = $intTotal;
                foreach($resultado as $data)
                {
                    $arrayEncontrados[] = array(
                        "idPersonaEmpresaRolChofer" => $data['idPersonaEmpresaRolChofer'],
                        "idPersonaChofer"           => $data['idPersonaChofer'],
                        "identificacionChofer"      => $data['identificacionChofer'],
                        "nombresChofer"             => $data['nombresChofer'],
                        "apellidosChofer"           => $data['apellidosChofer']
                    );
                }
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }

    /**
     * getResultadoChoferesPredefinidosDisponibles
     *
     * Consulta los choferes predefinidos que se encuentran disponibles                                 
     *      
     * @param array $arrayParametros
     * 
     * @return array $arrayRespuesta['total','resultado']
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     */
    public function getResultadoChoferesPredefinidosDisponibles($arrayParametros)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsmCount = new ResultSetMappingBuilder($this->_em);


            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $strSelect = " SELECT DISTINCT (per.ID_PERSONA_ROL), p.ID_PERSONA, p.NOMBRES, p.APELLIDOS, p.IDENTIFICACION_CLIENTE ";

            $strFromAndWhere = "
                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per
                                    INNER JOIN DB_COMERCIAL.INFO_PERSONA p ON per.PERSONA_ID = p.ID_PERSONA 
                                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL er ON per.EMPRESA_ROL_ID = er.ID_EMPRESA_ROL 
                                    INNER JOIN DB_GENERAL.ADMI_ROL r ON er.ROL_ID =r.ID_ROL 
                                    INNER JOIN DB_GENERAL.ADMI_TIPO_ROL tr ON r.TIPO_ROL_ID = tr.ID_TIPO_ROL 
                                    WHERE 
                                        per.ESTADO = :strEstadoActivo AND
                                        r.DESCRIPCION_ROL= :strDescripcionRol AND r.ESTADO NOT LIKE :strEstadoEliminado 
                                        AND tr.ESTADO NOT LIKE :strEstadoEliminado 
                                        AND er.EMPRESA_COD=:intEmpresa
                                        AND per.ID_PERSONA_ROL NOT IN 

                                        (
                                            SELECT DISTINCT detalleAsignacion.PERSONA_EMPRESA_ROL_ID 
                                            FROM DB_COMERCIAL.INFO_DETALLE_SOLICITUD detalleSolicitud
                                            INNER JOIN DB_SOPORTE.INFO_DETALLE detalle 
                                                ON detalle.DETALLE_SOLICITUD_ID=detalleSolicitud.ID_DETALLE_SOLICITUD
                                            INNER JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION detalleAsignacion
                                                ON detalleAsignacion.DETALLE_ID=detalle.ID_DETALLE 
                                            WHERE detalleSolicitud.TIPO_SOLICITUD_ID = :intTipoSolicitud 
                                                AND detalleSolicitud.ESTADO = :strEstadoActivo 
                                        )
                                         ";

            $strOrderBy = "ORDER BY p.APELLIDOS,p.NOMBRES ";

            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRolChofer', 'integer');
            $rsm->addScalarResult('ID_PERSONA', 'idPersonaChofer', 'integer');
            $rsm->addScalarResult('NOMBRES', 'nombresChofer', 'string');
            $rsm->addScalarResult('APELLIDOS', 'apellidosChofer', 'string');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionChofer', 'string');

            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $ntvQuery->setParameter('intTipoSolicitud', $arrayParametros['intTipoSolicitud']);
            $ntvQueryCount->setParameter('intTipoSolicitud', $arrayParametros['intTipoSolicitud']);

            $ntvQuery->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);
            $ntvQueryCount->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);

            $ntvQuery->setParameter('strDescripcionRol', $arrayParametros['strDescripcionRol']);
            $ntvQueryCount->setParameter('strDescripcionRol', $arrayParametros['strDescripcionRol']);


            $ntvQuery->setParameter('intEmpresa', $arrayParametros['intEmpresa']);
            $ntvQueryCount->setParameter('intEmpresa', $arrayParametros['intEmpresa']);

            $ntvQuery->setParameter('strEstadoEliminado', $arrayParametros['strEstadoEliminado']);
            $ntvQueryCount->setParameter('strEstadoEliminado', $arrayParametros['strEstadoEliminado']);

            $strWhereBusqueda = '';


            if(isset($arrayParametros['criterios_chofer']))
            {
                if(isset($arrayParametros['criterios_chofer']['identificacionChoferDisponible']))
                {
                    if($arrayParametros['criterios_chofer']['identificacionChoferDisponible'])
                    {
                        $strWhereBusqueda .= ' AND p.IDENTIFICACION_CLIENTE = :identificacionChoferDisponible ';

                        $ntvQuery->setParameter('identificacionChoferDisponible', $arrayParametros['criterios_chofer']['identificacionChoferDisponible']);

                        $ntvQueryCount->setParameter('identificacionChoferDisponible', $arrayParametros['criterios_chofer']['identificacionChoferDisponible']);
                    }
                }

                if(isset($arrayParametros['criterios_chofer']['nombresChoferDisponible']))
                {
                    if($arrayParametros['criterios_chofer']['nombresChoferDisponible'])
                    {
                        $strWhereBusqueda .= ' AND p.NOMBRES LIKE :nombresChoferDisponible ';

                        $ntvQuery->setParameter('nombresChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['nombresChoferDisponible'])) . '%');

                        $ntvQueryCount->setParameter('nombresChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['nombresChoferDisponible'])) . '%');
                    }
                }
                if(isset($arrayParametros['criterios_chofer']['apellidosChoferDisponible']))
                {
                    if($arrayParametros['criterios_chofer']['apellidosChoferDisponible'])
                    {
                        $strWhereBusqueda .= ' AND p.APELLIDOS LIKE :apellidosChoferDisponible ';

                        $ntvQuery->setParameter('apellidosChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['apellidosChoferDisponible'])) . '%');

                        $ntvQueryCount->setParameter('apellidosChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['apellidosChoferDisponible'])) . '%');
                    }
                }
            }


            $strSqlPrincipal = $strSelect . $strFromAndWhere . $strWhereBusqueda . $strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();

            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();

 
            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
        }
        catch(\Exception $e)
        {

            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }

    /*     * *******************************Fin Asignación Vehicular Predefinida*********************** */






    /*     * *************************************Asignación Operativa********************************* */

    /**
     * getJSONChoferesDisponibles
     *
     * Obtiene el json de los choferes que se encuentran disponibles para determinado horario                                 
     *      
     * @param array $arrayParametros
     * 
     * @return $jsonData
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     */
    public function getJSONChoferesDisponibles($arrayParametros)
    {
        $arrayEncontrados = array();
        if($arrayParametros['errorFechasHoras'])
        {
            $total = 0;
        }
        else
        {
            $arrayResultado = $this->getResultadoChoferesDisponibles($arrayParametros);
            $resultado = $arrayResultado['resultado'];
            $intTotal = $arrayResultado['total'];
            $total = 0;

            if($resultado)
            {
                $total = $intTotal;
                foreach($resultado as $data)
                {
                    $arrayEncontrados[] = array(
                        "idPersonaEmpresaRolChofer" => $data['idPersonaEmpresaRolChofer'],
                        "idPersonaChofer" => $data['idPersonaChofer'],
                        "identificacionChofer" => $data['identificacionChofer'],
                        "nombresChofer" => $data['nombresChofer'],
                        "apellidosChofer" => $data['apellidosChofer']
                    );
                }
            }
        }
        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }

    /**
     * getResultadoChoferesDisponibles
     *
     * Consulta los choferes que se encuentran disponibles para determinado horario                                 
     *      
     * @param array $arrayParametros
     * 
     * @return $jsonData
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     */
    public function getResultadoChoferesDisponibles($arrayParametros)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";

        try
        {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsmCount = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsm);
            $ntvQueryCount = $this->_em->createNativeQuery(null, $rsmCount);

            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $strSelect = " SELECT DISTINCT (per.ID_PERSONA_ROL), p.ID_PERSONA, p.NOMBRES, p.APELLIDOS, p.IDENTIFICACION_CLIENTE ";
            $strFromAndWhere = "
                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL per
                                    INNER JOIN DB_COMERCIAL.INFO_PERSONA p ON per.PERSONA_ID = p.ID_PERSONA 
                                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL er ON per.EMPRESA_ROL_ID = er.ID_EMPRESA_ROL 
                                    INNER JOIN DB_GENERAL.ADMI_ROL r ON er.ROL_ID =r.ID_ROL 
                                    INNER JOIN DB_GENERAL.ADMI_TIPO_ROL tr ON r.TIPO_ROL_ID = tr.ID_TIPO_ROL 
                                    WHERE 
                                        r.DESCRIPCION_ROL= :descripcionRol AND r.ESTADO NOT LIKE :strEstadoEliminado 
                                        AND tr.ESTADO NOT LIKE :strEstadoEliminado 
                                        AND er.EMPRESA_COD=:intEmpresa
                                        AND per.ID_PERSONA_ROL NOT IN 
                                        (
                                            SELECT DISTINCT(idChoferProvisional)
                                            FROM  
                                            (
                                                SELECT
                                                idChoferProvisional,refDetalle,
                                                MAX(CASE WHEN nombreDetalle=:strDetalleFechaInicioProvisional THEN valorDetalle ELSE NULL END) 
                                                AS fechaInicio,
                                                MAX(CASE WHEN nombreDetalle=:strDetalleFechaFinProvisional THEN valorDetalle ELSE NULL END) 
                                                AS fechaFin,
                                                MAX(CASE WHEN nombreDetalle=:strDetalleHoraInicioProvisional THEN valorDetalle ELSE NULL END) 
                                                AS horaInicio,
                                                MAX(CASE WHEN nombreDetalle=:strDetalleHoraFinProvisional THEN valorDetalle ELSE NULL END) 
                                                AS horaFin
                                                
                                                FROM
                                                (
                                                    SELECT 
                                                    CONNECT_BY_ROOT asignacion_chofer_provisional.DETALLE_VALOR as idChoferProvisional,
                                                    asignacion_chofer_provisional.DETALLE_NOMBRE as nombreDetalle, 
                                                    asignacion_chofer_provisional.DETALLE_VALOR as valorDetalle,
                                                    asignacion_chofer_provisional.REF_DETALLE_ELEMENTO_ID as refDetalle
                                                    FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO asignacion_chofer_provisional
                                                    WHERE asignacion_chofer_provisional.ESTADO = :strEstadoActivo 
                                                    AND CONNECT_BY_ROOT 
                                                    asignacion_chofer_provisional.ID_DETALLE_ELEMENTO =
                                                    asignacion_chofer_provisional.REF_DETALLE_ELEMENTO_ID 
                                                    START WITH asignacion_chofer_provisional.DETALLE_NOMBRE = :strDetalleChoferAsignacionProvisional
                                                    CONNECT BY 
                                                    PRIOR 
                                                    asignacion_chofer_provisional.ID_DETALLE_ELEMENTO = 
                                                    asignacion_chofer_provisional.REF_DETALLE_ELEMENTO_ID
                                                )
                                                GROUP BY
                                                idChoferProvisional,refDetalle
                                            )
                                            WHERE 
                                            (
                                                (

                                                    TO_TIMESTAMP( fechaFin ,'DD/MM/YYYY' ) >= 
                                                    TO_TIMESTAMP(:strFechaDesdeAsignacion,'DD/MM/YYYY' )
                                                    AND 
                                                    TO_TIMESTAMP( fechaFin ,'DD/MM/YYYY' )<= 
                                                    TO_TIMESTAMP(:strFechaHastaAsignacion,'DD/MM/YYYY' )
                                                )

                                                OR

                                                (
                                                    TO_TIMESTAMP( fechaInicio ,'DD/MM/YYYY' ) <= 
                                                    TO_TIMESTAMP(:strFechaHastaAsignacion,'DD/MM/YYYY' )
                                                    AND 
                                                    TO_TIMESTAMP( fechaInicio ,'DD/MM/YYYY' ) >= 
                                                    TO_TIMESTAMP(:strFechaDesdeAsignacion,'DD/MM/YYYY' )
                                                )

                                                OR

                                                (     
                                                    TO_TIMESTAMP( fechaInicio ,'DD/MM/YYYY' ) <= 
                                                    TO_TIMESTAMP(:strFechaDesdeAsignacion,'DD/MM/YYYY' )
                                                    AND
                                                    TO_TIMESTAMP( fechaFin,'DD/MM/YYYY' ) >= 
                                                    TO_TIMESTAMP(:strFechaHastaAsignacion,'DD/MM/YYYY' )
                                                )
                                            )
                                            AND 
                                            (
                                                (

                                                    TO_TIMESTAMP( horaFin ,'HH24:MI' ) > 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( horaFin ,'HH24:MI' ) < 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                )

                                                OR

                                                (

                                                    TO_TIMESTAMP( horaInicio ,'HH24:MI' ) < 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( horaInicio ,'HH24:MI' ) > 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                )

                                                OR
                                                (
                                                    TO_TIMESTAMP( horaInicio ,'HH24:MI' ) <= 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( horaFin ,'HH24:MI' ) >= 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                )

                                                OR

                                                (
                                                    TO_TIMESTAMP( horaInicio ,'HH24:MI' ) >= 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( horaFin ,'HH24:MI' ) <= 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                )
                                            )         
                                        ) 

                                        AND per.ID_PERSONA_ROL NOT IN 
                                        (
                                            SELECT DISTINCT detalleAsignacion.PERSONA_EMPRESA_ROL_ID
                                            FROM DB_COMERCIAL.ADMI_CUADRILLA ac 

                                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla 
                                              ON ide_cuadrilla.DETALLE_VALOR= ac.ID_CUADRILLA
                                              
                                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_detalle_solicitud 
                                              ON ide_detalle_solicitud.REF_DETALLE_ELEMENTO_ID=ide_cuadrilla.ID_DETALLE_ELEMENTO

                                            INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD detalleSolicitud 
                                            ON detalleSolicitud.ID_DETALLE_SOLICITUD = ide_detalle_solicitud.DETALLE_VALOR 

                                            INNER JOIN DB_SOPORTE.INFO_DETALLE detalle
                                              ON detalle.DETALLE_SOLICITUD_ID = detalleSolicitud.ID_DETALLE_SOLICITUD

                                            INNER JOIN DB_SOPORTE.INFO_DETALLE_ASIGNACION detalleAsignacion 
                                              ON detalleAsignacion.DETALLE_ID=detalle.ID_DETALLE 

                                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla_fecha_inicio 
                                                ON ide_cuadrilla_fecha_inicio.REF_DETALLE_ELEMENTO_ID=ide_cuadrilla.ID_DETALLE_ELEMENTO 

                                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla_hora_inicio 
                                                ON ide_cuadrilla_hora_inicio.REF_DETALLE_ELEMENTO_ID=ide_cuadrilla.ID_DETALLE_ELEMENTO 

                                            INNER JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide_cuadrilla_hora_fin   
                                                ON ide_cuadrilla_hora_fin.REF_DETALLE_ELEMENTO_ID=ide_cuadrilla.ID_DETALLE_ELEMENTO 

                                            WHERE ide_cuadrilla_fecha_inicio.ESTADO = :strEstadoActivo
                                            AND ide_cuadrilla.DETALLE_NOMBRE = :strDetalleCuadrillaAsignacionVehicular
                                            AND ide_cuadrilla.ESTADO = :strEstadoActivo

                                            AND detalleSolicitud.TIPO_SOLICITUD_ID = :idTipoSolicitud
                                            AND detalleSolicitud.ESTADO = :strEstadoActivo

                                            AND ide_cuadrilla_fecha_inicio.DETALLE_NOMBRE =:strDetalleFechaIniAsignacionVehicular
                                            AND ide_cuadrilla_hora_inicio.DETALLE_NOMBRE =:strDetalleHoraIniAsignacionVehicular 
                                            AND ide_cuadrilla_hora_fin.DETALLE_NOMBRE =:strDetalleHoraFinAsignacionVehicular
                                            AND ide_detalle_solicitud.DETALLE_NOMBRE =:strDetalleSolicitudAsignacionVehicular
                                            AND
                                            (
                                                (
                                                TO_TIMESTAMP( ide_cuadrilla_hora_fin.DETALLE_VALOR,'HH24:MI' ) > 
                                                TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                AND
                                                TO_TIMESTAMP( ide_cuadrilla_hora_fin.DETALLE_VALOR,'HH24:MI' ) < 
                                                TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                )

                                                OR

                                                (
                                                    TO_TIMESTAMP( ide_cuadrilla_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) < 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( ide_cuadrilla_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) > 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                )

                                                OR
                                                (
                                                    TO_TIMESTAMP( ide_cuadrilla_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) <= 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( ide_cuadrilla_hora_fin.DETALLE_VALOR ,'HH24:MI' ) >= 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                )

                                                OR

                                                (
                                                    TO_TIMESTAMP( ide_cuadrilla_hora_inicio.DETALLE_VALOR ,'HH24:MI' ) >= 
                                                    TO_TIMESTAMP(:strHoraDesdeAsignacion,'HH24:MI')
                                                    AND
                                                    TO_TIMESTAMP( ide_cuadrilla_hora_fin.DETALLE_VALOR ,'HH24:MI' ) <= 
                                                    TO_TIMESTAMP(:strHoraHastaAsignacion,'HH24:MI')
                                                )
                                            )
                                            AND ide_cuadrilla.ESTADO = :strEstadoActivo 
                                            AND ide_cuadrilla_fecha_inicio.ESTADO = :strEstadoActivo 
                                            AND ide_cuadrilla_hora_inicio.ESTADO = :strEstadoActivo
                                            AND ide_cuadrilla_hora_fin.ESTADO = :strEstadoActivo
                                            AND ide_detalle_solicitud.ESTADO = :strEstadoActivo
                                        )
                        ";

            $strOrderBy = "ORDER BY p.APELLIDOS,p.NOMBRES ";

            $rsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRolChofer', 'integer');
            $rsm->addScalarResult('ID_PERSONA', 'idPersonaChofer', 'integer');
            $rsm->addScalarResult('NOMBRES', 'nombresChofer', 'string');
            $rsm->addScalarResult('APELLIDOS', 'apellidosChofer', 'string');
            $rsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacionChofer', 'string');

            $rsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $ntvQuery->setParameter('detalles', array_values($arrayParametros["arrayDetallesProvisional"]));
            $ntvQueryCount->setParameter('detalles', array_values($arrayParametros["arrayDetallesProvisional"]));

            $ntvQuery->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);
            $ntvQueryCount->setParameter('strEstadoActivo', $arrayParametros['strEstadoActivo']);


            $arrayDetallesProvisional = $arrayParametros["arrayDetallesProvisional"];

            $ntvQuery->setParameter('strDetalleChoferAsignacionProvisional',$arrayDetallesProvisional['strDetalleChoferAsignacionProvisional']);
            $ntvQueryCount->setParameter('strDetalleChoferAsignacionProvisional',$arrayDetallesProvisional['strDetalleChoferAsignacionProvisional']);

            $ntvQuery->setParameter('strDetalleFechaInicioProvisional',$arrayDetallesProvisional['strDetalleFechaInicioAsignacionProvisional']);
            $ntvQueryCount->setParameter('strDetalleFechaInicioProvisional',$arrayDetallesProvisional['strDetalleFechaInicioAsignacionProvisional']);

            $ntvQuery->setParameter('strDetalleFechaFinProvisional',$arrayDetallesProvisional['strDetalleFechaFinAsignacionProvisional']);
            $ntvQueryCount->setParameter('strDetalleFechaFinProvisional',$arrayDetallesProvisional['strDetalleFechaFinAsignacionProvisional']);

            $ntvQuery->setParameter('strDetalleHoraInicioProvisional',$arrayDetallesProvisional['strDetalleHoraInicioAsignacionProvisional']);
            $ntvQueryCount->setParameter('strDetalleHoraInicioProvisional',$arrayDetallesProvisional['strDetalleHoraInicioAsignacionProvisional']);

            $ntvQuery->setParameter('strDetalleHoraFinProvisional',$arrayDetallesProvisional['strDetalleHoraFinAsignacionProvisional']);
            $ntvQueryCount->setParameter('strDetalleHoraFinProvisional',$arrayDetallesProvisional['strDetalleHoraFinAsignacionProvisional']);


            $arrayDetallesVehicular = $arrayParametros["arrayDetallesVehicular"];

            $ntvQuery->setParameter('strDetalleCuadrillaAsignacionVehicular',$arrayDetallesVehicular['strDetalleCuadrillaAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleCuadrillaAsignacionVehicular',$arrayDetallesVehicular['strDetalleCuadrillaAsignacionVehicular']);

            $ntvQuery->setParameter('strDetalleFechaIniAsignacionVehicular',$arrayDetallesVehicular['strDetalleFechaInicioAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleFechaIniAsignacionVehicular',$arrayDetallesVehicular['strDetalleFechaInicioAsignacionVehicular']);

            $ntvQuery->setParameter('strDetalleHoraIniAsignacionVehicular',$arrayDetallesVehicular['strDetalleHoraInicioAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleHoraIniAsignacionVehicular',$arrayDetallesVehicular['strDetalleHoraInicioAsignacionVehicular']);

            $ntvQuery->setParameter('strDetalleHoraFinAsignacionVehicular',$arrayDetallesVehicular['strDetalleHoraFinAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleHoraFinAsignacionVehicular',$arrayDetallesVehicular['strDetalleHoraFinAsignacionVehicular']);
            
            $ntvQuery->setParameter('strDetalleSolicitudAsignacionVehicular',$arrayDetallesVehicular['strDetalleSolicitudAsignacionVehicular']);
            $ntvQueryCount->setParameter('strDetalleSolicitudAsignacionVehicular',$arrayDetallesVehicular['strDetalleSolicitudAsignacionVehicular']);

            $ntvQuery->setParameter('intEmpresa', $arrayParametros['intEmpresa']);
            $ntvQueryCount->setParameter('intEmpresa', $arrayParametros['intEmpresa']);

            $ntvQuery->setParameter('descripcionRol', $arrayParametros['strDescripcionRol']);
            $ntvQueryCount->setParameter('descripcionRol', $arrayParametros['strDescripcionRol']);

            $ntvQuery->setParameter('strEstadoEliminado', $arrayParametros['strEstadoEliminado']);
            $ntvQueryCount->setParameter('strEstadoEliminado', $arrayParametros['strEstadoEliminado']);

            $ntvQuery->setParameter('descripcionCaracteristica', $arrayParametros['strDescripcionCaracteristica']);
            $ntvQueryCount->setParameter('descripcionCaracteristica', $arrayParametros['strDescripcionCaracteristica']);

            $ntvQuery->setParameter('strFechaDesdeAsignacion', $arrayParametros['strFechaDesdeAsignacion']);
            $ntvQueryCount->setParameter('strFechaDesdeAsignacion', $arrayParametros['strFechaDesdeAsignacion']);

            $ntvQuery->setParameter('strFechaHastaAsignacion', $arrayParametros['strFechaHastaAsignacion']);
            $ntvQueryCount->setParameter('strFechaHastaAsignacion', $arrayParametros['strFechaHastaAsignacion']);

            $ntvQuery->setParameter('strHoraDesdeAsignacion', $arrayParametros['strHoraDesdeAsignacion']);
            $ntvQueryCount->setParameter('strHoraDesdeAsignacion', $arrayParametros['strHoraDesdeAsignacion']);

            $ntvQuery->setParameter('strHoraHastaAsignacion', $arrayParametros['strHoraHastaAsignacion']);
            $ntvQueryCount->setParameter('strHoraHastaAsignacion', $arrayParametros['strHoraHastaAsignacion']);

            $ntvQuery->setParameter('idTipoSolicitud', $arrayParametros['intIdTipoSolicitudAsignacionPredefinida']);
            $ntvQueryCount->setParameter('idTipoSolicitud', $arrayParametros['intIdTipoSolicitudAsignacionPredefinida']);

            $ntvQuery->setParameter('idCaractDepartamentoPredefinido', $arrayParametros['intIdCaractDepartamentoPredefinido']);
            $ntvQueryCount->setParameter('idCaractDepartamentoPredefinido', $arrayParametros['intIdCaractDepartamentoPredefinido']);



            $strWhereBusqueda = '';
            if(isset($arrayParametros['idPerChoferAsignadoXVehiculo']))
            {
                if($arrayParametros['idPerChoferAsignadoXVehiculo'])
                {
                    $strWhereBusqueda .= 'AND per.ID_PERSONA_ROL <> :idPerChoferAsignadoXVehiculo ';

                    $ntvQuery->setParameter('idPerChoferAsignadoXVehiculo', $arrayParametros['idPerChoferAsignadoXVehiculo']);

                    $ntvQueryCount->setParameter('idPerChoferAsignadoXVehiculo', $arrayParametros['idPerChoferAsignadoXVehiculo']);
                }
            }

            if(isset($arrayParametros['criterios_chofer']))
            {
                if(isset($arrayParametros['criterios_chofer']['identificacionChoferDisponible']))
                {
                    if($arrayParametros['criterios_chofer']['identificacionChoferDisponible'])
                    {
                        $strWhereBusqueda .= ' AND p.IDENTIFICACION_CLIENTE = :identificacionChoferDisponible ';

                        $ntvQuery->setParameter('identificacionChoferDisponible', $arrayParametros['criterios_chofer']['identificacionChoferDisponible']);

                        $ntvQueryCount->setParameter('identificacionChoferDisponible', $arrayParametros['criterios_chofer']['identificacionChoferDisponible']);
                    }
                    if($arrayParametros['criterios_chofer']['nombresChoferDisponible'])
                    {
                        $strWhereBusqueda .= ' AND p.NOMBRES LIKE :nombresChoferDisponible ';

                        $ntvQuery->setParameter('nombresChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['nombresChoferDisponible'])) . '%');

                        $ntvQueryCount->setParameter('nombresChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['nombresChoferDisponible'])) . '%');
                    }
                    if($arrayParametros['criterios_chofer']['apellidosChoferDisponible'])
                    {
                        $strWhereBusqueda .= ' AND p.APELLIDOS LIKE :apellidosChoferDisponible ';

                        $ntvQuery->setParameter('apellidosChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['apellidosChoferDisponible'])) . '%');

                        $ntvQueryCount->setParameter('apellidosChoferDisponible', '%' . strtoupper(trim($arrayParametros['criterios_chofer']['apellidosChoferDisponible'])) . '%');
                    }
                }
            }


            $strSqlPrincipal = $strSelect . $strFromAndWhere . $strWhereBusqueda . $strOrderBy;

            $strSqlFinal = '';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio = $arrayParametros['intStart'];
                    $intFin = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strSqlPrincipal . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSqlPrincipal;
            }

            $ntvQuery->setSQL($strSqlFinal);
            $arrayResultado = $ntvQuery->getResult();


            $strSqlCount = $strSelectCount . " FROM (" . $strSqlPrincipal . ")";
            $ntvQueryCount->setSQL($strSqlCount);

            $intTotal = $ntvQueryCount->getSingleScalarResult();


            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $intTotal;
        }
        catch(\Exception $e)
        {

            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }

    /*     * *************************************Fin Asignación Operativa********************************* */

    /**
     * Documentación para el método: 'getResultadoContratistasoProveedoresVehiculoPorCriterios'.
     * Obtiene todos los contratistas de acuerdo a los parámetros enviados
     * 
     * @param array $arrayParametros ['idEmpresa','identificacion','nombre','tipoPersona','limit','page','start','estado']
     * @return array $arrayResultados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-01-2016
     */
    public function getResultadoContratistasoProveedoresVehiculoPorCriterios($arrayParametros)
    {


        $arrayRespuesta['resultado'] = "";
        $arrayRespuesta['total'] = 0;
        try
        {
            $objeto_retorna = 'b';
            $objeto_criterio = 'a';

            $criterio_nombre = '';
            $criterio_tipos_roles = '';
            $criterio_identificacion = '';
            $criterio_estado = '';
            $query = $this->_em->createQuery();
            if($arrayParametros['nombre'])
            {
                $nombre = strtoupper($arrayParametros['nombre']);
                $criterio_nombre = " ($objeto_criterio.nombres like :nombres "
                    . "OR $objeto_criterio.apellidos like :apellidos "
                    . "OR $objeto_criterio.razonSocial like :razonSocial) AND ";

                $query->setParameter('nombres', '%' . $nombre . '%');
                $query->setParameter('apellidos', '%' . $nombre . '%');
                $query->setParameter('razonSocial', '%' . $nombre . '%');
            }
            if($arrayParametros['identificacion'])
            {
                $identificacion = $arrayParametros['identificacion'];
                $criterio_identificacion = " $objeto_criterio.identificacionCliente = :identificacion AND ";
                $query->setParameter('identificacion', $identificacion);
            }
            if($arrayParametros['estado'])
            {
                $estado = $arrayParametros['estado'];
                $criterio_estado = " $objeto_criterio.estado = :estado AND ";
                $query->setParameter('estado', $estado);
            }
            if($arrayParametros['tiposRoles'])
            {
                $criterio_tipos_roles = 'AND e.descripcionTipoRol IN (:tiposRoles) ';
                $query->setParameter('tiposRoles', array_values($arrayParametros['tiposRoles']));
            }

            $idEmpresa = $arrayParametros['idEmpresa'];

            $query->setParameter('empresaCod', $idEmpresa);

            $strSql = "SELECT $objeto_retorna
            FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b, 
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:AdmiTipoRol e 
            WHERE 
                a.id=b.personaId AND
                b.empresaRolId=c.id AND
                c.rolId=d.id AND
                d.tipoRolId=e.id AND
                $criterio_identificacion
                $criterio_nombre
                $criterio_estado
                c.empresaCod= :empresaCod 
                $criterio_tipos_roles 
            ORDER BY a.feCreacion DESC";

            $query->setDQL($strSql);
            $total = count($query->getResult());

            $arrayResultado = $query->setFirstResult($arrayParametros['start'])->setMaxResults($arrayParametros['limit'])->getResult();
            $arrayRespuesta['resultado'] = $arrayResultado;
            $arrayRespuesta['total'] = $total;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para el método: 'getContratistasoProveedoresVehiculoPorCriterios'.
     * Obtiene todos los contratistas de acuerdo a los parámetros enviados
     * 
     * @param array $arrayCriterios ['idEmpresa','identificacion','nombre','tipoPersona','limit','page','start','estado']
     * @return array $arrayResultados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-01-2016
     */
    public function getJSONContratistasoProveedoresVehiculoPorCriterios($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoContratistasoProveedoresVehiculoPorCriterios($arrayParametros);
        $registros = $arrayResultado['resultado'];
        $intTotal = $arrayResultado['total'];
        $total = 0;

        $i = 1;
        //codigo y se llena variable $arrayEncontrados
        if($registros)
        {
            $total = $intTotal;
            foreach($registros as $contratista)
            {
                if($i % 2 == 0)
                {
                    $clase = 'k-alt';
                }
                else
                {
                    $clase = '';
                }

                $estado = '';
                //Obtiene el ultimo estado de la persona
                $ultimoEstado = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')
                    ->findUltimoEstadoPorPersonaEmpresaRol($contratista->getId());
                $idUltimoEstado = $ultimoEstado[0]['ultimo'];
                if($idUltimoEstado)
                {
                    $entityUltimoEstado = $this->_em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
                    $estado = $entityUltimoEstado->getEstado();
                }
                else
                {
                    $estado = $contratista->getEstado();
                }

                $objPersonaContratista = $contratista->getPersonaId();

                $nombreContratista = sprintf('%s', $objPersonaContratista);

                $arrayItem = array(
                    'idPersonaEmpresaRol' => $contratista->getId(),
                    'idPersona' => $objPersonaContratista->getId(),
                    'Nombre' => $nombreContratista,
                    'Direccion' => $objPersonaContratista->getDireccionTributaria(),
                    'fechaCreacion' => strval(date_format($contratista->getFeCreacion(), "d/m/Y G:i")),
                    'usuarioCreacion' => $contratista->getUsrCreacion(),
                    'estado' => $estado,
                    'tipoEmpresa' => $objPersonaContratista->getTipoEmpresa(),
                    'clase' => $clase,
                    'boton' => ""
                );

                $arrayEncontrados[] = $arrayItem;

                $i++;
            }
        }


        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);

        return $jsonData;
    }

    /**
     * getResultadoPersonaEmpresaRolPorCriterios
     *
     * Esta funcion ejecuta el Query que retorna los jefes por criterios
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-08-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 30-08-2016 Se cambia el orden de la consulta
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 10-11-2016 Se modifica la consulta para obtener los números telefónicos de los empleados.
     *                         Nuevo costo de la consulta = 473 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 18-11-2016 Se agrega la búsqueda de los empleados por login. 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 19-11-2016 Se agrega la búsqueda por id de la persona y además se agrega la obtención del id y nombre del departamento,
     *                         así como también se agrega en el respectivo group by los nuevos campos incluidos en el select
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 14-12-2016 Se agrega la búsqueda por id de la persona empresa rol
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 03-05-2018 Se agrega la búsqueda por región y departamento del empleado
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 08-11-2018 Se realizan ajustes en el comboBox que muestra los ingenieros de IPCCL2 en la pantalla de asignar responsable,
     *                         el cambio consiste en mostrar solo el personal del departamento de IPCCL2 y el número telefónico asignado
     *                         por la empresa
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 23-11-2018 Se realizan ajustes en la programación para considerar los departamentos por codigo de empresa
     *
     * @param array $arrayParametros [
     *                                  "esJefe"                : determina si la persona es o no jefe
     *                                  "strDescripcionRol"     : descripcion del rol de la persona
     *                                  "strDescripcionTipoRol" : descripcion del tipo de rol de la persona
     *                                  "idDepartamento"        : id del departamento
     *                                  "nombreApellidoPer"     : nombres o apellidos de la persona
     *                                  "nombresPersona"        : nombres de la persona
     *                                  "apellidosPersona"      : apellidos de la persona
     *                                  "identificacionPersona" : identificacion de la persona
     *                                  "estado"                : estado de la persona
     *                                  "start"                 : inicio del rownum
     *                                  "limit"                 : fin del rownum,
     *                                  "intIdPerEmpresaRol"    : id persona empresa rol,
     *                                  "strNombreDepartamento" : nombre del departamento
     *                                  "strRegionEmpleado"     : región R1 o R2 del empleado
     *                                  "origen'                : opción para identificar de donde es llamada la función
     *                                  "departamento"          : nombre del departamento
     *                              ]
     *
     * @return array $strDatos
     *
     */
    public function getResultadoPersonaEmpresaRolPorCriterios($arrayParametros)
    {
        $arrayRespuesta['total']     = 0;
        $arrayRespuesta['resultado'] = "";
        $strEstado                   = "Activo";
        $strDetalleNombre            = 'COLABORADOR';
        $strWhereAdicional           = "";

        try
        {
            
            
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery       = $this->_em->createNativeQuery(null, $objRsm);
            $objRsmCount       = new ResultSetMappingBuilder($this->_em);
            $objNtvQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);

            if($arrayParametros["origen"] == "IngenieroL2")
            {
                $strSelectNumero = " , ( SELECT LISTAGG(ie.NOMBRE_ELEMENTO,',') WITHIN GROUP (ORDER BY ie.ID_ELEMENTO)
                                        FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO ide,DB_INFRAESTRUCTURA.INFO_ELEMENTO ie
                                        WHERE ide.elemento_id = ie.id_elemento
                                        AND ide.DETALLE_VALOR = infoPersonaEmpresaRol.ID_PERSONA_ROL
                                        AND ide.detalle_nombre = :detalleNombre
                                        and ide.estado = :estado ) CONSUMO_CELULAR ";
            }

            $strSelect      = " SELECT infoPersonaEmpresaRol.ID_PERSONA_ROL,infoPersona.ID_PERSONA,
                                infoPersona.NOMBRES,infoPersona.APELLIDOS,infoPersona.IDENTIFICACION_CLIENTE,
                                CONCAT(infoPersona.NOMBRES,CONCAT(' ',infoPersona.APELLIDOS)) as NOMBRE_COMPLETO,
                                admiDepartamento.ID_DEPARTAMENTO, admiDepartamento.NOMBRE_DEPARTAMENTO, infoPersonaEmpresaRol.OFICINA_ID ";
            $strSelectCount = " SELECT COUNT(infoPersonaEmpresaRol.ID_PERSONA_ROL) AS TOTAL ";
            
            $strFrom        = " FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL infoPersonaEmpresaRol,
                                DB_COMERCIAL.INFO_PERSONA infoPersona,
                                DB_COMERCIAL.INFO_EMPRESA_ROL infoEmpresaRol,
                                DB_COMERCIAL.ADMI_DEPARTAMENTO admiDepartamento,
                                DB_COMERCIAL.ADMI_ROL admiRol,
                                DB_COMERCIAL.ADMI_TIPO_ROL admiTipoRol ";
            
            $strWhere       = " WHERE infoPersona.ID_PERSONA = infoPersonaEmpresaRol.PERSONA_ID 
                                AND infoPersonaEmpresaRol.EMPRESA_ROL_ID = infoEmpresaRol.ID_EMPRESA_ROL
                                AND admiDepartamento.ID_DEPARTAMENTO = infoPersonaEmpresaRol.DEPARTAMENTO_ID
                                AND infoEmpresaRol.ROL_ID = admiRol.ID_ROL 
                                AND admiRol.TIPO_ROL_ID=admiTipoRol.ID_TIPO_ROL 
                                AND infoEmpresaRol.EMPRESA_COD= :idEmpresa 
                                AND admiDepartamento.EMPRESA_COD = :idEmpresa ";

            if($arrayParametros["origen"] == "IngenieroL2")
            {
                $strWhereAdicional = " AND admiDepartamento.ID_DEPARTAMENTO =
                                        ( SELECT admiDepartamento.ID_DEPARTAMENTO FROM  DB_GENERAL.ADMI_DEPARTAMENTO admiDepartamento
                                        WHERE admiDepartamento.NOMBRE_DEPARTAMENTO = :nombreDepartamento AND admiDepartamento.ESTADO = :estado AND
                                        admiDepartamento.EMPRESA_COD = :codEmpresa ) ";
            }

            $strGroupBy     = "";
            
            $strOrderBy     = " ORDER BY infoPersona.APELLIDOS,infoPersona.NOMBRES ASC ";

            $objRsm->addScalarResult('ID_PERSONA_ROL', 'idPersonaEmpresaRol', 'integer');
            $objRsm->addScalarResult('ID_PERSONA', 'idPersona', 'integer');
            $objRsm->addScalarResult('NOMBRES', 'nombres', 'string');
            $objRsm->addScalarResult('APELLIDOS', 'apellidos', 'string');
            $objRsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacion', 'string');
            $objRsm->addScalarResult('NOMBRE_COMPLETO', 'nombreCompleto', 'string');
            $objRsm->addScalarResult('ID_DEPARTAMENTO', 'idDepartamento', 'integer');
            $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO', 'nombreDepartamento', 'string');
            $objRsm->addScalarResult('OFICINA_ID', 'idOficina', 'integer');
            
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');

            $objNtvQuery->setParameter('idEmpresa', $arrayParametros['idEmpresa']);
            $objNtvQueryCount->setParameter('idEmpresa', $arrayParametros['idEmpresa']);

            if($arrayParametros["origen"] == "IngenieroL2")
            {
                $objRsm->addScalarResult('CONSUMO_CELULAR', 'consumoCelular', 'string');
                $objNtvQuery->setParameter('estado', $strEstado);
                $objNtvQuery->setParameter('nombreDepartamento', $arrayParametros["departamento"]);
                $objNtvQuery->setParameter('detalleNombre', $strDetalleNombre);
                $objNtvQuery->setParameter('codEmpresa', $arrayParametros["idEmpresa"]);
                $objNtvQueryCount->setParameter('estado', $strEstado);
                $objNtvQueryCount->setParameter('nombreDepartamento', $arrayParametros["departamento"]);
                $objNtvQueryCount->setParameter('detalleNombre', $strDetalleNombre);
                $objNtvQueryCount->setParameter('codEmpresa', $arrayParametros["idEmpresa"]);
            }

            if(isset($arrayParametros['intIdPerEmpresaRol']) && !empty($arrayParametros['intIdPerEmpresaRol']))
            {
                $strWhere .= 'AND infoPersonaEmpresaRol.ID_PERSONA_ROL = :intIdPerEmpresaRol ';

                $objNtvQuery->setParameter('intIdPerEmpresaRol', $arrayParametros['intIdPerEmpresaRol']);
                $objNtvQueryCount->setParameter('intIdPerEmpresaRol', $arrayParametros['intIdPerEmpresaRol']);
            }
            
            if(isset($arrayParametros['intIdPersona']) && !empty($arrayParametros['intIdPersona']))
            {
                $strWhere .= 'AND infoPersona.ID_PERSONA = :intIdPersona ';

                $objNtvQuery->setParameter('intIdPersona', $arrayParametros['intIdPersona']);
                $objNtvQueryCount->setParameter('intIdPersona', $arrayParametros['intIdPersona']);
            }

            if(isset($arrayParametros['esJefe']))
            {
                if($arrayParametros['esJefe'])
                {
                    $strWhere .= 'AND admiRol.ES_JEFE = :esJefe ';
                    $objNtvQuery->setParameter('esJefe', $arrayParametros['esJefe']);
                    $objNtvQueryCount->setParameter('esJefe', $arrayParametros['esJefe']);
                }
            }
            
            if(isset($arrayParametros['strDescripcionRol']))
            {
                if($arrayParametros['strDescripcionRol'])
                {
                    $strWhere .= 'AND admiRol.DESCRIPCION_ROL = :strDescripcionRol ';
                    $objNtvQuery->setParameter('strDescripcionRol', $arrayParametros['strDescripcionRol']);
                    $objNtvQueryCount->setParameter('strDescripcionRol', $arrayParametros['strDescripcionRol']);
                }
            }
            
            if(isset($arrayParametros['strDescripcionTipoRol']))
            {
                if($arrayParametros['strDescripcionTipoRol'])
                {
                    $strWhere .= 'AND admiTipoRol.DESCRIPCION_TIPO_ROL = :strDescripcionTipoRol ';
                    $objNtvQuery->setParameter('strDescripcionTipoRol', $arrayParametros['strDescripcionTipoRol']);
                    $objNtvQueryCount->setParameter('strDescripcionTipoRol', $arrayParametros['strDescripcionTipoRol']);
                }
            }
            
            
            if(isset($arrayParametros['idDepartamento']))
            {
                if($arrayParametros['idDepartamento'])
                {
                    $strWhere .= 'AND admiDepartamento.ID_DEPARTAMENTO = :idDepartamento ';

                    $objNtvQuery->setParameter('idDepartamento', $arrayParametros['idDepartamento']);
                    $objNtvQueryCount->setParameter('idDepartamento', $arrayParametros['idDepartamento']);
                }
            }
            
            if(isset($arrayParametros['strNombreDepartamento']) && !empty($arrayParametros['strNombreDepartamento']))
            {
                $strWhere .= 'AND admiDepartamento.NOMBRE_DEPARTAMENTO = :strNombreDepartamento ';

                $objNtvQuery->setParameter('strNombreDepartamento', $arrayParametros['strNombreDepartamento']);
                $objNtvQueryCount->setParameter('strNombreDepartamento', $arrayParametros['strNombreDepartamento']);

            }
            
            if(isset($arrayParametros['nombreApellidoPer']))
            {
                if($arrayParametros['nombreApellidoPer'])
                {
                    $strWhere .= 'AND (infoPersona.NOMBRES LIKE :nombreApellidoPer OR infoPersona.APELLIDOS LIKE :nombreApellidoPer) ';

                    $objNtvQuery->setParameter('nombreApellidoPer', '%'.strtoupper($arrayParametros['nombreApellidoPer']).'%');
                    $objNtvQueryCount->setParameter('nombreApellidoPer', '%'.strtoupper($arrayParametros['nombreApellidoPer']).'%');
                }
            }
            
            if(isset($arrayParametros['nombresPersona']))
            {
                if($arrayParametros['nombresPersona'])
                {
                    $strWhere .= 'AND infoPersona.NOMBRES LIKE :nombresPersona ';

                    $objNtvQuery->setParameter('nombresPersona', '%'.strtoupper($arrayParametros['nombresPersona']).'%');
                    $objNtvQueryCount->setParameter('nombresPersona', '%'.strtoupper($arrayParametros['nombresPersona']).'%');
                }
            }
            
            if(isset($arrayParametros['apellidosPersona']))
            {
                if($arrayParametros['apellidosPersona'])
                {
                    $strWhere .= 'AND infoPersona.APELLIDOS LIKE :apellidosPersona ';

                    $objNtvQuery->setParameter('apellidosPersona', '%'.strtoupper($arrayParametros['apellidosPersona']).'%');
                    $objNtvQueryCount->setParameter('apellidosPersona', '%'.strtoupper($arrayParametros['apellidosPersona']).'%');
                }
            }
            
            if(isset($arrayParametros['identificacionPersona']))
            {
                if($arrayParametros['identificacionPersona'])
                {
                    $strWhere .= 'AND infoPersona.IDENTIFICACION_CLIENTE = :identificacionPersona ';

                    $objNtvQuery->setParameter('identificacionPersona', $arrayParametros['identificacionPersona']);
                    $objNtvQueryCount->setParameter('identificacionPersona', $arrayParametros['identificacionPersona']);
                }
            }
            
            if(isset($arrayParametros['strLoginPersona']) && !empty($arrayParametros['strLoginPersona']))
            {
                $strWhere .= 'AND infoPersona.LOGIN = :strLoginPersona ';

                $objNtvQuery->setParameter('strLoginPersona', $arrayParametros['strLoginPersona']);
                $objNtvQueryCount->setParameter('strLoginPersona', $arrayParametros['strLoginPersona']);
            }
            
            if(isset($arrayParametros['estado']))
            {
                if($arrayParametros['estado'])
                {
                    $strWhere .= 'AND infoPersonaEmpresaRol.ESTADO = :estado ';

                    $objNtvQuery->setParameter('estado', $arrayParametros['estado']);
                    $objNtvQueryCount->setParameter('estado', $arrayParametros['estado']);
                }
            }

            $strQuerySqlCount  = $strSelectCount . $strFrom .$strWhere . $strWhereAdicional . $strOrderBy;
            $objNtvQueryCount->setSQL($strQuerySqlCount);
            $intTotal       = $objNtvQueryCount->getSingleScalarResult();

            if(isset($arrayParametros['strDescripcionFormaContacto']))
            {
                if($arrayParametros['strDescripcionFormaContacto'])
                {
                    $strSelect.=", LISTAGG( infoPersonaFormaContacto.VALOR, ', ') WITHIN GROUP (ORDER BY infoPersonaFormaContacto.VALOR) CONTACTO "; 
                    $strFrom  .=", DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO infoPersonaFormaContacto "
                              . ", DB_COMERCIAL.ADMI_FORMA_CONTACTO admiFormaContacto ";
                    $strWhere .= ' AND infoPersonaFormaContacto.PERSONA_ID = infoPersona.ID_PERSONA'
                              .  ' AND admiFormaContacto.ID_FORMA_CONTACTO = infoPersonaFormaContacto.FORMA_CONTACTO_ID '
                              .  ' AND admiFormaContacto.DESCRIPCION_FORMA_CONTACTO = :strDescripcionFormaContacto '
                              .  ' AND infoPersonaFormaContacto.ESTADO = :estadoFormaContacto ';
                    $strGroupBy .= " GROUP BY infoPersonaEmpresaRol.ID_PERSONA_ROL,infoPersona.ID_PERSONA, "
                                .  "infoPersona.NOMBRES, infoPersona.APELLIDOS, "
                                .  "infoPersona.IDENTIFICACION_CLIENTE, "
                                .  "admiDepartamento.ID_DEPARTAMENTO, "
                                .  "admiDepartamento.NOMBRE_DEPARTAMENTO, "
                                .  "infoPersonaEmpresaRol.OFICINA_ID ";
                    
                    $objRsm->addScalarResult('CONTACTO', 'contactos', 'string');
                    $objNtvQuery->setParameter('strDescripcionFormaContacto', $arrayParametros['strDescripcionFormaContacto']);
                    $objNtvQuery->setParameter('estadoFormaContacto', 'Activo');
                }
            }
            
            if(isset($arrayParametros['strRegionEmpleado']) && !empty($arrayParametros['strRegionEmpleado']))
            {
                $strSelect  .= ",   admiCanton.ID_CANTON ";
                $strFrom    .= ",   DB_COMERCIAL.INFO_OFICINA_GRUPO infoOficina,
                                    DB_GENERAL.ADMI_CANTON admiCanton ";
                $strWhere   .= "    AND infoPersonaEmpresaRol.OFICINA_ID = infoOficina.ID_OFICINA 
                                    AND infoOficina.CANTON_ID = admiCanton.ID_CANTON 
                                    AND admiCanton.REGION = :strRegionEmpleado ";
                if(!empty($strGroupBy))
                {
                    $strGroupBy .= ", admiCanton.ID_CANTON ";
                }
                $objRsm->addScalarResult('ID_CANTON', 'idCanton', 'integer');
                $objNtvQuery->setParameter('strRegionEmpleado', $arrayParametros['strRegionEmpleado']);
                $objNtvQueryCount->setParameter('strRegionEmpleado', $arrayParametros['strRegionEmpleado']);
            }
            

            $strQuerySql    = $strSelect . $strSelectNumero . $strFrom. $strWhere. $strWhereAdicional . $strGroupBy.$strOrderBy;
            $strSqlFinal    = '';

            if(isset($arrayParametros['start']) && isset($arrayParametros['limit']))
            {
                if($arrayParametros['start'] && $arrayParametros['limit'])
                {
                    $intInicio = $arrayParametros['start'];
                    $intFin = $arrayParametros['start'] + $arrayParametros['limit'];
                    $strSqlFinal = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM (' . $strQuerySql . ') consultaPrincipal 
                                            WHERE rownum<=' . $intFin . '
                                        ) WHERE consultaPrincipal_rownum >' . $intInicio;
                }
                else
                {
                    if($arrayParametros['limit'])
                    {
                        $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strQuerySql . ') consultaPrincipal 
                                        WHERE rownum<=' . $arrayParametros['limit'];
                    }
                    else
                    {
                        $intLimit    = 10;
                        $strSqlFinal = '  SELECT consultaPrincipal.* 
                                        FROM (' . $strQuerySql . ') consultaPrincipal 
                                        WHERE rownum<=' . $intLimit;
                    }
                    
                }
            }
            else
            {
                $strSqlFinal = $strQuerySql;
            }
            
            $objNtvQuery->setSQL($strSqlFinal);
            $arrayResultado = $objNtvQuery->getResult();

            $arrayRespuesta["total"]        = $intTotal;
            $arrayRespuesta['resultado']    = $arrayResultado;
            
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $arrayRespuesta;
    }
    
    
    /**
     * getJSONPersonaEmpresaRolPorCriterios
     *
     * Esta funcion retorna el json de los jefes de un departamento por empresa
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-08-2016
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 10-11-2016 Se aumenta el campo con la información de los nombres y sus teléfonos móviles. Si no existiera la información del 
     *                         nombre completo y de sus contactos, el campo info_adicional sería vacío
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 08-11-2018 Se realizan ajustes en el comboBox que muestra los ingenieros de IPCCL2 en la pantalla de asignar responsable,
     *                         el cambio consiste en mostrar solo el personal del departamento de IPCCL2 y el número telefónico asignado
     *                         por la empresa
     *
     * @param array $arrayParametros[
     *                                  "esJefe"                : determina si la persona es o no jefe
     *                                  "strDescripcionRol"     : descripcion del rol de la persona
     *                                  "strDescripcionTipoRol" : descripcion del tipo de rol de la persona
     *                                  "idDepartamento"        : id del departamento
     *                                  "nombreApellidoPer"     : nombres o apellidos de la persona
     *                                  "nombresPersona"        : nombres de la persona
     *                                  "apellidosPersona"      : apellidos de la persona
     *                                  "identificacionPersona" : identificacion de la persona
     *                                  "estado"                : estado de la persona
     *                                  "start"                 : inicio del rownum
     *                                  "limit"                 : fin del rownum
     *                              ]
     *
     * @return json $jsonData
     *
     */
    public function getJSONPersonaEmpresaRolPorCriterios($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoPersonaEmpresaRolPorCriterios($arrayParametros);
        $registros        = $arrayResultado['resultado'];
        $intTotal         = $arrayResultado['total'];
        
        if ($registros) 
        {
            $total=$intTotal;
            foreach ($registros as $data)
            {                   
                $arrayEncontrados[]=array(
                                            'idPersonaEmpresaRol'   => $data["idPersonaEmpresaRol"],
                                            'idPersona'             => $data["idPersona"],
                                            'nombreCompleto'        => trim($data["nombreCompleto"]),
                                            'nombres'               => trim($data["nombres"]),
                                            'apellidos'             => trim($data["apellidos"]),
                                            'identificacion'        => $data["identificacion"],
                                            'info_adicional'        => $data["nombreCompleto"] ." " .$data["consumoCelular"]
                                    );
            }
        }
        else
        {
            $total=0;
        }
        
        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
    
    
    /**
     * getSaldoCliente
     * 
     * Obtiene la información de las cámaras de los servicios de los clientes
     * Costo = 173
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-07-2017
     * 
     * @param  array $arrayParametros[  
     *                                  'intIdPer'    => id persona empresa rol
     *                               ]
     * 
     * @return array $arrayResultado
     */
    public function getSaldoCliente($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);
            
            $strQuery           = " SELECT SUM(ecr.saldo) AS SALDO_PENDIENTE 
                                    FROM
                                        (SELECT DISTINCT ipunto.ID_PUNTO
                                        FROM DB_COMERCIAL.info_punto ipunto
                                        WHERE ipunto.PERSONA_EMPRESA_ROL_ID = :intIdPer
                                        AND ipunto.estado = :strEstadoActivo
                                        ) iPuntosServ
                                    INNER JOIN DB_FINANCIERO.VISTA_ESTADO_CUENTA_RESUMIDO ecr
                                    ON ecr.PUNTO_ID = iPuntosServ.ID_PUNTO ";
            
            $objNtvQuery->setParameter('intIdPer', $arrayParametros['intIdPer']);
            $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
            $objRsm->addScalarResult('SALDO_PENDIENTE', 'saldoPendiente', 'float');
            $objNtvQuery->setSQL($strQuery);            
            $arrayResultado = $objNtvQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * getCoordinadorDeCuadrilla
     *
     * Obtiene el coordinador de una cuadrilla
     *
     * costoQuery: 3
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 24-08-2017
     *
     * @param  array $arrayParametros [
     *                                  "intIdPersonaRol"   : idPersonaEmpresaRol de la persona de cuadrilla.
     *                                ]
     *
     * @return $arrayResultado
     */
    public function getCoordinadorDeCuadrilla($arrayParametros)
    {
        $objResultado = null;
        try
        {
            $strSqlDatos = "SELECT coordinador
                            FROM
                            schemaBundle:InfoPersonaEmpresaRol coordinador
                            WHERE
                            coordinador.id = (SELECT cuadrilla.coordinadorPrincipalId
                                              FROM schemaBundle:AdmiCuadrilla cuadrilla
                                              WHERE cuadrilla.id = :idCuadrilla
                                             )
                           ";

            $strQueryDatos = $this->_em->createQuery($strSqlDatos);
            $strQueryDatos->setParameter('idCuadrilla', $arrayParametros['idCuadrilla']);

            $objResultado = $strQueryDatos->getOneOrNullResult();
        }
        catch(\Exception $e)
        {
            error_log('InfoPersonaEmpresaRolRepository->getCoordinadorDeCuadrilla() '.$e->getMessage());
        }
        return $objResultado;
    }
    
    /**
     * getArrayCorreoVipPorPersonaRol
     * 
     * Método que obtiene correo del Ingeniero VIP de un cliente
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0
     * @since 16-03-2018
     * 
     * Costo : 11
     * 
     * @param  Array $arrayParametros
     *                               [
     *                                   intIdPersonaRol    Identificador del rol de cliente
     *                               ]
     * @return Array $arrayResultado  correo Vip de cliente
     */
    public function getArrayCorreoVipPorPersonaRol($arrayParametros)
    {        
        $objRsm   = new ResultSetMappingBuilder($this->_em);	      
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);	                

        $strSql = "  SELECT
                    upper(perfc.valor) AS CORREO
                FROM
                    db_comercial.info_persona_empresa_rol_carac pemprolc
                    JOIN db_comercial.admi_caracteristica carac 
                         ON pemprolc.caracteristica_id = carac.id_caracteristica
                    JOIN db_comercial.info_persona_empresa_rol peremprolvip 
                         ON coalesce(to_number(regexp_substr(pemprolc.valor,'^\d+') ),0) = peremprolvip.id_persona_rol
                    JOIN db_comercial.info_persona pervip 
                         ON ( peremprolvip.persona_id ) = ( pervip.id_persona )
                    JOIN db_comercial.info_persona_forma_contacto perfc 
                         ON pervip.id_persona = perfc.persona_id
                    JOIN db_comercial.admi_forma_contacto fc 
                         ON perfc.forma_contacto_id = fc.id_forma_contacto
                WHERE
                    carac.descripcion_caracteristica = :caractVipParam
                    AND   fc.descripcion_forma_contacto = :caractCorreoParam
                    AND   pemprolc.persona_empresa_rol_id =:idPersonalRolParam
                    AND   ROWNUM <= :rowNumParam
                ";

        $objRsm->addScalarResult('CORREO','strCorreo','string');                                   		                        

        $objQuery->setParameter('idPersonalRolParam', $arrayParametros['intIdPersonaRol']);  
        $objQuery->setParameter('caractVipParam', 'ID_VIP');  
        $objQuery->setParameter('caractCorreoParam', 'Correo Electronico');  
        $objQuery->setParameter('rowNumParam', 1);  

        $objQuery->setSQL($strSql);                   

        $arrayResultado = $objQuery->getOneOrNullResult();     
              
        return $arrayResultado;      
    }
    
    /**
     * getArrayEsVipPorPersonaRol
     * 
     * Método que obtiene caracteristica de un cliente para identificar si es VIP
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0
     * @since 16-03-2018
     * 
     * Costo : 11
     * 
     * @param  Array $arrayParametros
     *                               [
     *                                   intIdPersonaRol    Identificador del rol de cliente
     *                               ]
     * @return Array $arrayResultado  Caracteristica ESVIP de cliente
     */
    public function getArrayEsVipPorPersonaRol($arrayParametros)
    {        
        $objRsm   = new ResultSetMappingBuilder($this->_em);	      
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);	                

        $strSql = "  SELECT
                    icd.es_vip AS ESVIP
                FROM
                    db_comercial.info_persona_empresa_rol iper,
                    db_comercial.info_empresa_rol ier,
                    db_comercial.info_contrato ic,
                    db_comercial.info_contrato_dato_adicional icd
                WHERE
                    ic.persona_empresa_rol_id = iper.id_persona_rol
                    AND   iper.empresa_rol_id = ier.id_empresa_rol
                    AND   icd.contrato_id = ic.id_contrato
                    AND   iper.id_persona_rol = :idPersonalRolParam
                    AND   ic.estado = :estadoContradoParam
                    AND   ier.empresa_cod = :empresaCodParam
                    AND   ROWNUM <= :rowNumParam
                ";

        $objRsm->addScalarResult('ESVIP','strEsVip','string');                                   		                        

        $objQuery->setParameter('idPersonalRolParam', $arrayParametros['intIdPersonaRol']);
        $objQuery->setParameter('estadoContradoParam', 'Activo');
        $objQuery->setParameter('empresaCodParam', '10');
        $objQuery->setParameter('rowNumParam', 1);

        $objQuery->setSQL($strSql);                   

        $arrayResultado = $objQuery->getOneOrNullResult();     
              
        return $arrayResultado;      
    }

    /**
     * getCoordinadorXDepartamento
     *
     * Obtiene los coordinadores por departamento.
     *
     * costoQuery: 79
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 24-08-2017
     *
     * @param  array $arrayParametros [
     *                                  "intIdPersonaRol"   : idPersonaEmpresaRol de la persona de cuadrilla.
     *                                ]
     *
     * @return $arrayResultado
     */
    public function getCoordinadorXDepartamento($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

            $strQuery           = " SELECT ipersona.NOMBRES||' '||ipersona.APELLIDOS AS NOMBRES,
                                           ar.DESCRIPCION_ROL AS DESCRIPCION_ROL,
                                           ipersona.ID_PERSONA AS ID_PERSONA,
                                           iper.ID_PERSONA_ROL AS ID_PERSONA_ROL
                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper
                                    INNER JOIN DB_COMERCIAL.INFO_persona ipersona
                                    ON ipersona.ID_PERSONA = iper.PERSONA_ID
                                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ier
                                    ON ier.ID_EMPRESA_ROL = iper.EMPRESA_ROL_ID
                                    INNER JOIN DB_GENERAL.ADMI_ROL ar
                                    ON ar.ID_ROL = ier.ROL_ID
                                    WHERE  ar.ESTADO in (:strEstado)
                                    AND ar.DESCRIPCION_ROL IN
                                      (SELECT APD.DESCRIPCION
                                      FROM ADMI_PARAMETRO_CAB AP
                                      INNER JOIN ADMI_PARAMETRO_DET APD
                                      ON APD.PARAMETRO_ID      = AP.ID_PARAMETRO
                                      WHERE AP.NOMBRE_PARAMETRO= :strCargoArea
                                      AND APD.VALOR1           = :strValor
                                      )
                                    AND iper.DEPARTAMENTO_ID = :intIdDepartamento
                                    AND iper.ESTADO in (:strEstado)
                                    order by ipersona.NOMBRES, ipersona.APELLIDOS";

            $objNtvQuery->setParameter('intIdDepartamento', $arrayParametros['idDepartamentoInicial']);
            $objNtvQuery->setParameter('strEstado',         array('Activo', 'Modificado'));
            $objNtvQuery->setParameter('strValor',          'Jefes');
            $objNtvQuery->setParameter('strCargoArea',      'CARGOS AREA TECNICA');
            $objRsm->addScalarResult('NOMBRES',         'nombres',        'string');
            $objRsm->addScalarResult('DESCRIPCION_ROL', 'descripcionRol', 'string');
            $objRsm->addScalarResult('ID_PERSONA',      'idPersona',      'integer');
            $objRsm->addScalarResult('ID_PERSONA_ROL',  'idPersonaRol',   'integer');
            $objNtvQuery->setSQL($strQuery);
            $arrayResultado = $objNtvQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log('InfoPersonaEmpresaRolRepository->getCoordinadorXDepartamento() '.$e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Método encargado de obtener los datos de una persona de acuerdo a su rol y empresa
     *
     * Costo 20
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 29-11-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 12-05-2021 - Se agrega el filtro por el id de persona e identificación cliente.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 17-08-2021 - Se agrega nuevo filtro por identificación, adicional se retorna la región.
     *
     * @param Array $arrayParametros [
     *                                  intIdPersona               : Id de la persona.
     *                                  strIdentificacionCliente   : Identificación de la persona.
     *                                  strRol                     : Descripción del rol,
     *                                  strPrefijo                 : Prefijo de la empresa,
     *                                  intIdentificacion          : Identificación del empleado,
     *                                  strLogin                   : Login del empleado,
     *                                  strNombres                 : Nombres del empleado,
     *                                  strApellidos               : Apellidos del empleado,
     *                                  strDepartamento            : Nombre del departamento,
     *                                  strEstadoPersona           : Estado de la persona,
     *                                  strEstadoPersonaEmpresaRol : Estado de la personaEmpresaRol,
     *                                  strNombreCanton            : Cantón,
     *                                  strCodEmpresa              : Código de empresa
     *                               ]
     * @return Array
     */
    public function getInfoDatosPersona($arrayParametros)
    {
        try
        {
            $objResultSetMap =  new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  =  $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere        = "AND IPER.DEPARTAMENTO_ID = ADEP.ID_DEPARTAMENTO ".
                               "AND ADEP.EMPRESA_COD     = IEGR.COD_EMPRESA     ";

            if (isset($arrayParametros['intIdPersona']) && !empty($arrayParametros['intIdPersona']))
            {
                $strWhere .= "AND IPERSONA.ID_PERSONA = :intIdPersona ";
                $objNativeQuery->setParameter("intIdPersona" , $arrayParametros['intIdPersona']);
            }

            if (isset($arrayParametros['strRol']) && !empty($arrayParametros['strRol']))
            {
                if ($arrayParametros['strRol'] === 'Cliente')
                {
                    $strWhere = "AND IPER.DEPARTAMENTO_ID = ADEP.ID_DEPARTAMENTO(+) ";
                }

                $strWhere .= "AND ATROL.DESCRIPCION_TIPO_ROL IN (:strRol) ";
                $objNativeQuery->setParameter("strRol" , $arrayParametros['strRol']);
            }

            if (isset($arrayParametros['intIdPersona']) && !empty($arrayParametros['intIdPersona']))
            {
                $strWhere .= "AND IPERSONA.ID_PERSONA = :intIdPersona ";
                $objNativeQuery->setParameter("intIdPersona" , $arrayParametros['intIdPersona']);
            }

            if (isset($arrayParametros['strIdentificacionCliente']) && !empty($arrayParametros['strIdentificacionCliente']))
            {
                $strWhere .= "AND IPERSONA.IDENTIFICACION_CLIENTE = :strIdentificacionCliente ";
                $objNativeQuery->setParameter("strIdentificacionCliente" , $arrayParametros['strIdentificacionCliente']);
            }

            if (isset($arrayParametros['strPrefijo']) && !empty($arrayParametros['strPrefijo']))
            {
                $strWhere .= "AND IEGR.PREFIJO = :strPrefijo ";
                $objNativeQuery->setParameter("strPrefijo" , $arrayParametros['strPrefijo']);
            }

            if (isset($arrayParametros['strCodEmpresa']) && !empty($arrayParametros['strCodEmpresa']))
            {
                $strWhere .= "AND IEGR.COD_EMPRESA = :strCodEmpresa ";
                $objNativeQuery->setParameter("strCodEmpresa" , $arrayParametros['strCodEmpresa']);
            }

            if (isset($arrayParametros['strDepartamento']) && !empty($arrayParametros['strDepartamento']))
            {
                $strWhere .= "AND UPPER(ADEP.NOMBRE_DEPARTAMENTO) LIKE (UPPER(:strDepartamento)) ";
                $objNativeQuery->setParameter("strDepartamento" , '%'.$arrayParametros['strDepartamento'].'%');
            }

            if (isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= "AND UPPER(IPERSONA.LOGIN) = UPPER(:strLogin) ";
                $objNativeQuery->setParameter("strLogin" , $arrayParametros['strLogin']);
            }

            if (isset($arrayParametros['strNombres']) && !empty($arrayParametros['strNombres']))
            {
                $strWhere .= "AND UPPER(IPERSONA.NOMBRES) LIKE (UPPER(:strNombres)) ";
                $objNativeQuery->setParameter("strNombres" , '%'.$arrayParametros['strNombres'].'%');
            }

            if (isset($arrayParametros['strApellidos']) && !empty($arrayParametros['strApellidos']))
            {
                $strWhere .= "AND UPPER(IPERSONA.APELLIDOS) LIKE (UPPER(:strApellidos)) ";
                $objNativeQuery->setParameter("strApellidos" , '%'.$arrayParametros['strApellidos'].'%');
            }

            if (isset($arrayParametros['strEstadoPersona']) && !empty($arrayParametros['strEstadoPersona']))
            {
                $strWhere .= "AND IPERSONA.ESTADO IN (:strEstadoPersona) ";
                $objNativeQuery->setParameter("strEstadoPersona" , $arrayParametros['strEstadoPersona']);
            }

            if (isset($arrayParametros['strEstadoPersonaEmpresaRol']) && !empty($arrayParametros['strEstadoPersonaEmpresaRol']))
            {
                $strWhere .= "AND IPER.ESTADO = :strEstadoPersonaEmpresaRol ";
                $objNativeQuery->setParameter("strEstadoPersonaEmpresaRol" , $arrayParametros['strEstadoPersonaEmpresaRol']);
            }

            if (isset($arrayParametros['strNombreCanton']) && !empty($arrayParametros['strNombreCanton']))
            {
                $strWhere .= "AND UPPER(ADCA.NOMBRE_CANTON) LIKE (UPPER(:strNombreCanton)) ";
                $objNativeQuery->setParameter("strNombreCanton" , '%'.$arrayParametros['strNombreCanton'].'%');
            }

            if (isset($arrayParametros['intIdPersonaEmpresaRol']) && !empty($arrayParametros['intIdPersonaEmpresaRol']))
            {
                $strWhere .= "AND IPER.ID_PERSONA_ROL = :intIdPersonaEmpresaRol ";
                $objNativeQuery->setParameter("intIdPersonaEmpresaRol" , $arrayParametros['intIdPersonaEmpresaRol']);
            }

            $strSql = "SELECT IPER.ID_PERSONA_ROL      AS ID_PERSONA_ROL, ".
                             "IEGR.COD_EMPRESA         AS COD_EMPRESA, ".
                             "IEGR.PREFIJO             AS PREFIJO, ".
                             "IPERSONA.ID_PERSONA      AS ID_PERSONA, ".
                             "IPERSONA.IDENTIFICACION_CLIENTE, ".
                             "IPERSONA.LOGIN           AS LOGIN, ".
                             "IPERSONA.NOMBRES         AS NOMBRES, ".
                             "IPERSONA.APELLIDOS       AS APELLIDOS, ".
                             "IPERSONA.RAZON_SOCIAL    AS RAZON_SOCIAL, ".
                             "ADEP.ID_DEPARTAMENTO     AS ID_DEPARTAMENTO, ".
                             "ADEP.NOMBRE_DEPARTAMENTO AS NOMBRE_DEPARTAMENTO, ".
                             "ADCA.NOMBRE_CANTON       AS NOMBRE_CANTON, ".
                             "IOG.NOMBRE_OFICINA       AS NOMBRE_OFICINA, ".
                             "UPPER(ADCA.REGION)       AS REGION ".
                        "FROM DB_COMERCIAL.INFO_PERSONA             IPERSONA, ".
                             "DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER, ".
                             "DB_GENERAL.ADMI_DEPARTAMENTO          ADEP, ".
                             "DB_COMERCIAL.INFO_EMPRESA_GRUPO       IEGR, ".
                             "DB_COMERCIAL.INFO_EMPRESA_ROL         IERO, ".
                             "DB_GENERAL.ADMI_ROL                   AROL, ".
                             "DB_GENERAL.ADMI_TIPO_ROL              ATROL, ".
                             "DB_COMERCIAL.INFO_OFICINA_GRUPO       IOG, ".
                             "DB_GENERAL.ADMI_CANTON                ADCA ".
                      "WHERE IPERSONA.ID_PERSONA  = IPER.PERSONA_ID ".
                        "AND IPER.EMPRESA_ROL_ID  = IERO.ID_EMPRESA_ROL ".
                        "AND IERO.ROL_ID          = AROL.ID_ROL ".
                        "AND AROL.TIPO_ROL_ID     = ATROL.ID_TIPO_ROL ".
                        "AND IPER.OFICINA_ID      = IOG.ID_OFICINA ".
                        "AND IOG.CANTON_ID        = ADCA.ID_CANTON ".
                        "AND IOG.EMPRESA_ID       = IEGR.COD_EMPRESA ".
                        "$strWhere ";

            $objResultSetMap->addScalarResult('COD_EMPRESA'            , 'idEmpresa'             , 'string');
            $objResultSetMap->addScalarResult('PREFIJO'                , 'prefijoEmpresa'        , 'string');
            $objResultSetMap->addScalarResult('ID_PERSONA_ROL'         , 'idPersonaEmpresaRol'   , 'integer');
            $objResultSetMap->addScalarResult('ID_PERSONA'             , 'idPersona'             , 'integer');
            $objResultSetMap->addScalarResult('IDENTIFICACION_CLIENTE' , 'identificacionCliente' , 'string');
            $objResultSetMap->addScalarResult('LOGIN'                  , 'loginEmpleado'         , 'string');
            $objResultSetMap->addScalarResult('NOMBRES'                , 'nombres'               , 'string');
            $objResultSetMap->addScalarResult('APELLIDOS'              , 'apellidos'             , 'string');
            $objResultSetMap->addScalarResult('RAZON_SOCIAL'           , 'razonSocial'           , 'string');
            $objResultSetMap->addScalarResult('ID_DEPARTAMENTO'        , 'idDepartamento'        , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_DEPARTAMENTO'    , 'nombreDepartamento'    , 'string');
            $objResultSetMap->addScalarResult('NOMBRE_OFICINA'         , 'nombreOficina'         , 'string');
            $objResultSetMap->addScalarResult('NOMBRE_CANTON'          , 'nombreCanton'          , 'string');
            $objResultSetMap->addScalarResult('REGION'                 , 'region'                , 'string');

            $objNativeQuery->setSQL($strSql);

            $arrayDatos = $objNativeQuery->getResult();

            if (empty($arrayDatos) || count($arrayDatos) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos');
            }

            $arrayResultado = array ('status' => 'ok', 'result' => $arrayDatos);
        }
        catch (\Exception $objException)
        {
            $arrayResultado = array ('status' => 'fail', 'message' => $objException->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Función que obtiene el coordinador de un usuario.
     *
     * costoQuery: 14
     * @author Néstor Naula López <nnaulal@telconet.ec>
     * @version 1.0 31-08-2018
     *
     * @param  array $arrayParametros [
     *                                  "user"   : usuario del lider de cuadrilla.
     *                                ]
     *
     * @return $arrayResultado
     */
    public function getCoordinadorPorUsuario($arrayParametros)
    {
        $arrayResultado     = array();
        $strUser            = $arrayParametros['usrCreacion'];
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

        $strQuery           = " SELECT NOMBRE_DEPARTAMENTO,DEPARTAMENTO_ID,JEFE_PERSONA_EMPRESA_ROL_ID,
                                ID_PERSONA_JEFE,NOMBRES,APELLIDOS,REGION REGION,ID_CANTON,CORREO
                                FROM (SELECT ADEP.NOMBRE_DEPARTAMENTO,IPER.DEPARTAMENTO_ID,IPER.REPORTA_PERSONA_EMPRESA_ROL_ID JEFE_PERSONA_EMPRESA_ROL_ID,
                                IPE2.ID_PERSONA ID_PERSONA_JEFE,IPE2.NOMBRES,IPE2.APELLIDOS,ACAN.REGION REGION,ACAN.ID_CANTON,IPF.VALOR CORREO
                                FROM INFO_PERSONA IPE
                                INNER JOIN INFO_PERSONA_EMPRESA_ROL IPER ON IPE.ID_PERSONA = IPER.PERSONA_ID
                                INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IFO ON IFO.ID_OFICINA = IPER.OFICINA_ID
                                INNER JOIN DB_GENERAL.ADMI_CANTON ACAN ON ACAN.ID_CANTON = IFO.CANTON_ID
                                INNER JOIN ADMI_DEPARTAMENTO ADEP ON ADEP.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID
                                LEFT JOIN INFO_PERSONA_EMPRESA_ROL IPER2 ON IPER2.ID_PERSONA_ROL = IPER.REPORTA_PERSONA_EMPRESA_ROL_ID
                                LEFT JOIN INFO_PERSONA IPE2 ON IPE2.ID_PERSONA = IPER2.PERSONA_ID
                                LEFT JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPF ON IPF.PERSONA_ID=IPE2.ID_PERSONA
                                WHERE IPE.LOGIN=:strUser AND IPER.ESTADO=:strEstado AND IPER.DEPARTAMENTO_ID IS NOT NULL 
                                AND ADEP.EMPRESA_COD = :strCodigoEmpresa AND IPF.FORMA_CONTACTO_ID=:intFormaContacto
                                order by IPER.FE_CREACION DESC) T1
                                WHERE ROWNUM = :intNumeroFilas
                            ";

        $objNtvQuery->setParameter('strUser',           $strUser);
        $objNtvQuery->setParameter('strEstado',         'Activo');
        $objNtvQuery->setParameter('strCodigoEmpresa',  '10');
        $objNtvQuery->setParameter('intNumeroFilas',    '1');
        $objNtvQuery->setParameter('intFormaContacto',  '5');
        
        $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO',         'nombreDepartamento',       'string');
        $objRsm->addScalarResult('DEPARTAMENTO_ID',             'departamentoId',           'string');
        $objRsm->addScalarResult('JEFE_PERSONA_EMPRESA_ROL_ID', 'jefePersonaEmpresaRolId',  'string');
        $objRsm->addScalarResult('ID_PERSONA_JEFE',             'personaIdJefe',            'string');
        $objRsm->addScalarResult('NOMBRES',                     'nombresJefe',              'string');
        $objRsm->addScalarResult('APELLIDOS',                   'apellidosJefe',            'string');
        $objRsm->addScalarResult('REGION',                      'region',                   'string');
        $objRsm->addScalarResult('ID_CANTON',                   'cantonId',                 'string');
        $objRsm->addScalarResult('CORREO',                      'correo',                   'string');

        $objNtvQuery->setSQL($strQuery);
        $arrayResultado = $objNtvQuery->getOneOrNullResult();

        return $arrayResultado;
    }
 /**
     * Función que obtiene informacion de una persona por Cargo y por 
     * Departamento.
     *
     * costoQuery: 87
     * @param  array $arrayParametros [
     *                                  "intDepartamentoId"   : id departamento.
     *                                  "strDescripcionRol"   : por ejemplo 'jefe departamental'.
     *                                  "intCodEmpresa"       : codigo de la empresa.
     *                                  "strRegion"           : region.
     *                                  "intFormaContacto"    : forma de contacto correo.
     *  
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.0 11-03-2019
     *
     * @return $arrayResultado
     */
    public function getResponsablePorCargo($arrayParametros)
    {
        try
        {
            $arrayResultado     = array();
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

            $strQuery           = "SELECT i0_.ID_PERSONA AS ID_PERSONA,
                                        i0_.NOMBRES
                                        || ' '
                                        || i0_.APELLIDOS   AS NOMBRE_COMPLETO,
                                        i1_.ID_PERSONA_ROL AS ID_PERSONA_ROL,
                                        a2_.nombre_departamento AS NOMBRE_DEPARTAMENTO,
                                        a2_.id_departamento AS DEPARTAMENTO_ID,
                                        i0_.LOGIN||'@telconet.ec' AS CORREO
                                    FROM DB_GENERAL.admi_departamento a2_,
                                        DB_COMERCIAL.info_persona_empresa_rol i1_,
                                        DB_COMERCIAL.info_empresa_rol i3_,
                                        DB_GENERAL.admi_rol a4_,
                                        DB_COMERCIAL.info_persona i0_
                                    WHERE a2_.ID_DEPARTAMENTO = i1_.DEPARTAMENTO_ID
                                    AND i1_.EMPRESA_ROL_ID    = i3_.ID_EMPRESA_ROL
                                    AND i3_.ROL_ID            = a4_.ID_ROL
                                    AND i0_.ID_PERSONA        = i1_.PERSONA_ID
                                    AND a2_.ID_DEPARTAMENTO   = :intDepartamentoId
                                    AND i1_.ESTADO            = 'Activo'
                                    AND a2_.EMPRESA_COD       = :intCodEmpresa
                                    AND i3_.EMPRESA_COD       = :intCodEmpresa
                                    AND a4_.ES_JEFE           = 'S'
                                    AND a4_.DESCRIPCION_ROL   = :strDescripcionRol
                                    AND i1_.oficina_Id IN 
                                        (
                                            SELECT infoOficinaGrupo.id_oficina
                                            FROM DB_COMERCIAL.info_oficina_grupo infoOficinaGrupo
                                            WHERE infoOficinaGrupo.canton_Id IN (
                                                                                    SELECT admiCanton.id_canton
                                                                                    FROM DB_GENERAL.Admi_Canton admiCanton 
                                                                                    WHERE admiCanton.JURISDICCION = :strJurisdiccion 
                                                                                )
                                        )";
    
            $objNtvQuery->setParameter('intDepartamentoId', $arrayParametros['intDepartamentoId']);
            $objNtvQuery->setParameter('strDescripcionRol', $arrayParametros['strDescripcionRol']);
            $objNtvQuery->setParameter('intCodEmpresa',     $arrayParametros['intCodEmpresa']);
            $objNtvQuery->setParameter('strJurisdiccion',   $arrayParametros['strJurisdiccion']);
            
            $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO',         'nombreDepartamento',       'string');
            $objRsm->addScalarResult('DEPARTAMENTO_ID',             'departamentoId',           'string');
            $objRsm->addScalarResult('ID_PERSONA_ROL',              'personaEmpresaRolId',      'string');
            $objRsm->addScalarResult('ID_PERSONA',                  'personaId',                'string');
            $objRsm->addScalarResult('NOMBRE_COMPLETO',             'nombreCompleto',           'string');
            $objRsm->addScalarResult('CORREO',                      'correo',                   'string');

            $objNtvQuery->setSQL($strQuery);
            $arrayResultado = $objNtvQuery->getOneOrNullResult();
    }
    catch(\Exception $e)
    {
        $serviceUtil = $this->get('schema.Util');

        $serviceUtil->insertError( 'Telcos+', 
        'InfoPersonaEmpresaRolRepository', 
        $ex->getMessage(), 
        'telcos', 
        '127.0.0.1' );

        $arrayResultado['salida']  = 500;
        $arrayResultado['mensaje'] = $ex->getMessage();
        
    }
        return $arrayResultado;
    }

    
    /**
     * Función que obtiene los jefes de Sucursales por Región.
     *
     * costoQuery: 107
     * @author Néstor Naula López <nnaulal@telconet.ec>
     * @version 1.0 31-08-2018
     *
     * @param  array $arrayParametros [
     *                                  "user"   : usuario del lider de cuadrilla.
     *                                ]
     *
     * @return $arrayResultado
     */
    public function getJefesSucursalPorRegion($arrayParametros)
    {
        $arrayResultado     = array();
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

        $strQuery           = " SELECT ADEP.NOMBRE_DEPARTAMENTO,IPER.DEPARTAMENTO_ID,IPER.REPORTA_PERSONA_EMPRESA_ROL_ID JEFE_PERSONA_EMPRESA_ROL_ID,
                                IPE.ID_PERSONA ID_PERSONA_JEFE,IPE.NOMBRES,IPE.APELLIDOS,ACAN.REGION
                                from INFO_PERSONA IPE
                                INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPE.ID_PERSONA = IPER.PERSONA_ID
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IER.ID_EMPRESA_ROL=IPER.EMPRESA_ROL_ID
                                INNER JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL=IER.ROL_ID
                                INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IFO ON IFO.ID_OFICINA = IPER.OFICINA_ID
                                INNER JOIN DB_GENERAL.ADMI_CANTON ACAN ON ACAN.ID_CANTON = IFO.CANTON_ID
                                INNER JOIN ADMI_DEPARTAMENTO ADEP ON ADEP.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID
                                INNER JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO IPF ON IPF.PERSONA_ID=IPE.ID_PERSONA
                                WHERE IPER.ESTADO=:strEstado AND IPE.CARGO = :strCargo 
                                AND AR.DESCRIPCION_ROL = :strCargo 
                                AND ADEP.NOMBRE_DEPARTAMENTO = :strDepartamento
                                AND IPF.FORMA_CONTACTO_ID=:intFormaContacto
                                AND ROWNUM = :intNumeroFilas 
                                 ";

        $strWhere="";
        
        $objNtvQuery->setParameter('strCargo',          $arrayParametros['strCargo']);
        $objNtvQuery->setParameter('strDepartamento',   $arrayParametros['strDepartamento']);
        $objNtvQuery->setParameter('strEstado',         'Activo');
        $objNtvQuery->setParameter('intNumeroFilas',    '1');
        $objNtvQuery->setParameter('intFormaContacto',  '5');
        
        if(isset($arrayParametros['strRegion']) && !empty($arrayParametros['strRegion'])){
            $strWhere .= " AND ACAN.REGION = :strRegion ";
            $objNtvQuery->setParameter('strRegion',$arrayParametros['strRegion']);
        }
        
        $strQuery .= $strWhere;
        
        $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO',         'nombreDepartamento',       'string');
        $objRsm->addScalarResult('DEPARTAMENTO_ID',             'departamentoId',           'string');
        $objRsm->addScalarResult('JEFE_PERSONA_EMPRESA_ROL_ID', 'jefePersonaEmpresaRolId',  'string');
        $objRsm->addScalarResult('ID_PERSONA_JEFE',             'personaIdJefe',            'string');
        $objRsm->addScalarResult('NOMBRES',                     'nombresJefe',              'string');
        $objRsm->addScalarResult('APELLIDOS',                   'apellidosJefe',            'string');
        $objRsm->addScalarResult('REGION',                      'region',                   'string');
        $objRsm->addScalarResult('CORREO',                      'correo',                   'string');

        $objNtvQuery->setSQL($strQuery);
        $arrayResultado = $objNtvQuery->getOneOrNullResult();

        return $arrayResultado;
    }

    /**
     * getPersonaOficina
     * Costo: 5
     * Esta función retorna un objeto personaEmpresaRol
     *
     * @author Walther Joao Gaibor<wgaibor@telconet.ec>
     * @version 1.0 - 21/12/2018
     *
     * @param array $arrayParametro
     *
     * @return array $datos
     *
     */
    public function getPersonaOficina($arrayParametro)
    {
        $strQuery = "SELECT per
                        FROM
                        schemaBundle:InfoPersonaEmpresaRol per,
                        schemaBundle:InfoOficinaGrupo og
                        WHERE
                        per.personaId = :idPersona AND
                        per.oficinaId = og.id AND
                        og.empresaId  = :codEmpresa AND
                        per.departamentoId is not null and per.departamentoId <> 0 AND
                        per.estado    = :estados ";

        $objQuery = $this->_em->createQuery($strQuery);
        $objQuery->setParameter('idPersona',  $arrayParametro['intIdPersona']);
        $objQuery->setParameter('codEmpresa', $arrayParametro['intCodEmpresa']);
        $objQuery->setParameter('estados',    $arrayParametro['strEstado']);

        $objDatos = $objQuery->getOneOrNullResult();

        return $objDatos;
    }
    
    
    /**
     * getJefeSucursalPorRegionYCargo
     * Función que obtiene los jefes de Sucursales por Región.
     *
     * @author Néstor Naula López <nnaulal@telconet.ec>
     * @version 1.0 21-02-2019
     * 
     * @param array $arrayParametros [
     *              strIdEmpresa        - Id empresa al que pertenece la persona
     *              strRegion           - Región a la que pertenece la persona
     *              strDepartamento     - Departamento al que pertenece la persona
     *              strDescripcionRol   - Descripción del Rol de la persona
     *              strEstado           - Estado de la persona]
     *
     * @return array $arrayResultado[
     *              idPersonaRol - Retorna el Id empresa rol del ejefe de sucursal]
     * 
     * costoQuery: 44
     * 
     */
    public function getJefeSucursalPorRegionYCargo($arrayParametros)
    {
        $arrayResultado     = array();
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

        $strQuery           = " SELECT IPER.ID_PERSONA_ROL
                                FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER 
                                INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL IER ON IER.ID_EMPRESA_ROL=IPER.EMPRESA_ROL_ID
                                INNER JOIN DB_GENERAL.ADMI_DEPARTAMENTO ADE ON ade.id_departamento=IPER.DEPARTAMENTO_ID
                                INNER JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO IOG ON IOG.ID_OFICINA=IPER.OFICINA_ID
                                INNER JOIN DB_GENERAL.ADMI_CANTON ACO ON ACO.ID_CANTON=IOG.CANTON_ID
                                INNER JOIN DB_GENERAL.ADMI_ROL AR ON AR.ID_ROL=IER.ROL_ID
                                WHERE ADE.EMPRESA_COD=:codEmpresa AND ACO.REGION=:region AND UPPER(ADE.NOMBRE_DEPARTAMENTO) = :nombreDepartamento
                                AND AR.DESCRIPCION_ROL =:descripcionRol AND IPER.ESTADO=:estado AND ROWNUM=1
                                 ";

        $strWhere="";
        
        $objNtvQuery->setParameter('codEmpresa',        $arrayParametros['strIdEmpresa']);
        $objNtvQuery->setParameter('region',            $arrayParametros['strRegion']);
        $objNtvQuery->setParameter('nombreDepartamento',$arrayParametros['strDepartamento']);
        $objNtvQuery->setParameter('descripcionRol',    $arrayParametros['strDescripcionRol']);
        $objNtvQuery->setParameter('estado',            $arrayParametros['strEstado']);
       
        $strQuery .= $strWhere;
        
        $objRsm->addScalarResult('ID_PERSONA_ROL',  'idPersonaRol',  'string');

        $objNtvQuery->setSQL($strQuery);
        $arrayResultado = $objNtvQuery->getOneOrNullResult();

        return $arrayResultado;
    }
    
    /**
     * getInfoPersonaRolPorLoginTecnico
     *
     * Costo: 15
     * 
     * Función que retorna ID_PERSONA_ROL, OFICINA_ID, DEPARTAMENTO_ID según EL Login del técnico ingresado.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 17-09-2019
     *
     * 
     * Costo: 7
     * 
     * Se obtiene el nombre y apellido del login técnico ingresado. 
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.1 21-10-2019
     * 
     * 
     * @param array  $arrayParametros
     *
     * @return array $objPersonaEmpresaRol
     *
     */
    public function getInfoPersonaRolPorLoginTecnico($arrayParametros)
    {
        $objRsm              = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = " SELECT 
                        IPER.ID_PERSONA_ROL, IPER.OFICINA_ID, IPER.DEPARTAMENTO_ID,
                        IFPR.NOMBRES, IFPR.APELLIDOS
                    FROM 
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER, DB_COMERCIAL.INFO_PERSONA IFPR 
                    WHERE 
                        IFPR.LOGIN = :login
                        AND IPER.PERSONA_ID = IFPR.ID_PERSONA
                        AND IPER.ESTADO = :estado
                        AND IPER.REPORTA_PERSONA_EMPRESA_ROL_ID IS NOT null";

        $objQuery->setParameter('login',  $arrayParametros['login']);
        $objQuery->setParameter('estado', 'Activo');
        
        $objRsm->addScalarResult('ID_PERSONA_ROL',      'idPersonaRol',     'string');
        $objRsm->addScalarResult('OFICINA_ID',          'idOficina',        'integer');
        $objRsm->addScalarResult('DEPARTAMENTO_ID',     'idDepartamento',   'integer');
        $objRsm->addScalarResult('NOMBRES',             'nombres',          'string');
        $objRsm->addScalarResult('APELLIDOS',           'apellidos',        'string');

        $objQuery->setSQL($strSql);
        
        $objPersonaEmpresaRol = $objQuery->getOneOrNullResult(); 
        return $objPersonaEmpresaRol;
    }
    
    
    
    /**
     * getIdDepartCoordinador
     *
     * Costo: 18
     * 
     * Función que retorna el Id del departamento de un coordinador.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 17-09-2019
     *
     * @param integer  $intIdPersona
     *
     * @return array $objPersonaEmpresaRol
     *
     */
    public function getIdDepartCoordinador($intIdPersona)
    {
        
        $objRsm              = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql = "SELECT 
                        IPER.DEPARTAMENTO_ID
                   FROM 
                        DB_SOPORTE.INFO_PERSONA  INFPER, 
                        DB_SOPORTE.INFO_PERSONA_EMPRESA_ROL IPER, 
                        DB_SOPORTE.ADMI_DEPARTAMENTO ADMDP,
                        DB_SOPORTE.INFO_OFICINA_GRUPO IPG
                   WHERE 
                        INFPER.ID_PERSONA = :idPersona
                        AND IPER.PERSONA_ID = INFPER.ID_PERSONA 
                        AND IPER.ESTADO = :estado 
                        AND IPER.DEPARTAMENTO_ID IS NOT NULL 
                        AND IPER.DEPARTAMENTO_ID = ADMDP.ID_DEPARTAMENTO 
                        AND IPER.OFICINA_ID  = IPG.ID_OFICINA
                        AND IPG.EMPRESA_ID = :empresaId
                        AND ADMDP.EMPRESA_COD = :empresaId";

        $objQuery->setParameter('idPersona',  $intIdPersona);
        $objQuery->setParameter('estado', 'Activo');
        $objQuery->setParameter('empresaId', '10');
        
        $objRsm->addScalarResult('DEPARTAMENTO_ID',          'idDepartamento',        'integer');
        $objQuery->setSQL($strSql);
        
        $objPersonaEmpresaRol = $objQuery->getOneOrNullResult(); 
        
        return $objPersonaEmpresaRol;
    }

    /**
     * Documentación para el método 'getArrayIngenierosVIPCliente'.
     *
     * Retorna un arreglo con los datos de los Ingenieros VIP relacionados al cliente VIP
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 27-01-2020
     *
     * @param Array $arrayParametros['strEmpresa']         String: Código de la empresa
     *                              ['strCaracteristica']  String: Descripción de la Característica
     *                              ['strEstado']          String: Estado de la característica
     *                              ['intIdPerEmp']        Int   : Id del Cliente
     *                              ['strCaractCiudad']    String: Id de la Característica de la Ciudad, puede ser null
     *                              ['strCaractExt']       String: Id de la Característica de la Extensión, puede ser null
     *
     * @return Array $arrayResultado[
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'result'    => arreglo con la información de los Ingenieros VIP o mensaje de error
     *                              ]
     *
     * costoQuery: 31
     */
    public function getIngenierosVipTecnicoCliente($arrayParametros)
    {
        try
        {
            $booleanCaractCiudad        = false;
            $booleanCaractExtension     = false;
            $objMappingBuilder          = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery             = $this->_em->createNativeQuery(null, $objMappingBuilder);

            if( isset($arrayParametros['strCaractCiudad']) && !empty($arrayParametros['strCaractCiudad']) )
            {
                $booleanCaractCiudad    = true;
                $objMappingBuilder->addScalarResult('CIUDAD',   'ciudad',   'string');
                $objNativeQuery->setParameter('ID_CARACT_CIUDAD',$arrayParametros['strCaractCiudad']);
            }
            if( isset($arrayParametros['strCaractExt']) && !empty($arrayParametros['strCaractExt']) )
            {
                $booleanCaractExtension = true;
                $objMappingBuilder->addScalarResult('EXTENSION','extension','string');
                $objNativeQuery->setParameter('ID_CARACT_EXT', $arrayParametros['strCaractExt']);
            }

            $strSQL     = "SELECT PERC.VALOR ID_PER, 
                                  UPPER(P.NOMBRES || ' ' || P.APELLIDOS) INGENIERO, 
                                  EL.NOMBRE_ELEMENTO CELULAR_EMP,
                                  EMP.MAIL_CIA CORREO";

            if($booleanCaractCiudad)
            {
                $strSQL = $strSQL.", PER_CIU.CIUDAD";
            }
            if($booleanCaractExtension)
            {
                $strSQL = $strSQL.", PER_EXT.VALOR EXTENSION";
            }

            $strSQL     = $strSQL." FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PERC
                       INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  PER
                                    ON PER.ID_PERSONA_ROL  = COALESCE(TO_NUMBER(REGEXP_SUBSTR(PERC.VALOR,'^\d+')),0)
                       INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL          ER   ON ER.ID_EMPRESA_ROL   = PER.EMPRESA_ROL_ID
                       INNER JOIN DB_COMERCIAL.INFO_PERSONA              P    ON P.ID_PERSONA        = PER.PERSONA_ID
                       INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA       C    ON C.ID_CARACTERISTICA = PERC.CARACTERISTICA_ID
                       LEFT JOIN NAF47_TNET.V_Empleados_Empresas         EMP  ON EMP.LOGIN_EMPLE     = P.LOGIN
                                                                              AND EMP.ESTADO='A'
                       LEFT JOIN DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO DE  ON  DE.DETALLE_VALOR   = PER.ID_PERSONA_ROL
                                                                              AND DE.ESTADO          = :ESTADO
                                                                              AND DE.DETALLE_NOMBRE  = :COLABORADOR
                       LEFT JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO        EL   ON  EL.ID_ELEMENTO      = DE.ELEMENTO_ID
                                                                              AND EL.DESCRIPCION_ELEMENTO = :NUMERO_CELULAR";

            if($booleanCaractCiudad)
            {
                $strSQL = $strSQL." LEFT JOIN ( SELECT PER_CIUDAD.PERSONA_EMPRESA_ROL_CARAC_ID, CANTON.NOMBRE_CANTON CIUDAD
                                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PER_CIUDAD
                                    INNER JOIN DB_GENERAL.ADMI_CANTON CANTON ON CANTON.ID_CANTON = PER_CIUDAD.VALOR
                                    WHERE PER_CIUDAD.CARACTERISTICA_ID = :ID_CARACT_CIUDAD AND PER_CIUDAD.ESTADO = :ESTADO ) PER_CIU
                                    ON PER_CIU.PERSONA_EMPRESA_ROL_CARAC_ID = PERC.ID_PERSONA_EMPRESA_ROL_CARACT";
            }
            if($booleanCaractExtension)
            {
                $strSQL = $strSQL." LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC PER_EXT
                                    ON PER_EXT.PERSONA_EMPRESA_ROL_CARAC_ID = PERC.ID_PERSONA_EMPRESA_ROL_CARACT
                                    AND PER_EXT.CARACTERISTICA_ID = :ID_CARACT_EXT
                                    AND PER_EXT.ESTADO = :ESTADO";
            }

            $strSQL     = $strSQL." WHERE PERC.PERSONA_EMPRESA_ROL_ID  = :ID_PER
                                    AND   C.DESCRIPCION_CARACTERISTICA = :CARACTERISTICA
                                    AND   PERC.ESTADO                  = :ESTADO
                                    AND   ER.EMPRESA_COD               = :EMPRESA
                                    ORDER BY P.NOMBRES,  P.APELLIDOS";

            $objNativeQuery->setParameter('ID_PER',         $arrayParametros['intIdPerEmp']);
            $objNativeQuery->setParameter('CARACTERISTICA', $arrayParametros['strCaracteristica']);
            $objNativeQuery->setParameter('ESTADO',         $arrayParametros['strEstado']);
            $objNativeQuery->setParameter('EMPRESA',        $arrayParametros['strEmpresa']);
            $objNativeQuery->setParameter('COLABORADOR',    'COLABORADOR');
            $objNativeQuery->setParameter('NUMERO_CELULAR', 'NUMERO CELULAR');

            $objMappingBuilder->addScalarResult('ID_PER',       'id_per',       'string');
            $objMappingBuilder->addScalarResult('INGENIERO',    'ingeniero',    'string');
            $objMappingBuilder->addScalarResult('CELULAR_EMP',  'celular',      'string');
            $objMappingBuilder->addScalarResult('CORREO',       'correo',       'string');
            $objMappingBuilder->addScalarResult('TOTAL',        'total',        'integer');

            $objNativeQuery->setSQL($strSQL);
            $arrayResultado = $objNativeQuery->getArrayResult();

            $arrayResult = array(
                'status' => 'OK',
                'result' => $arrayResultado
            );
        }
        catch(\Exception $ex)
        {
            $arrayResult = array(
                'status' => 'ERROR',
                'result' => $ex->getMessage()
            );
        }
        return $arrayResult;
    }

    /**
     * Documentación para el método 'getSubgerentePorLoginVendedor'.
     *
     * Método encargado de retornar el subgerente por el login del vendedor.
     *
     * Costo 11
     *
     * @param array $arrayParametros [
     *                                  "strLogin" => login del vendedor.
     *                               ]
     *
     * @return array $arrayResultado arreglo del subgerente.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function getSubgerentePorLoginVendedor($arrayParametros)
    {
        try
        {
            $strLogin          = $arrayParametros['strLogin'] ? $arrayParametros['strLogin']:"";
            $arrayDatos        = array();
            $strMensajeError   = "";
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objQuery          = $this->_em->createNativeQuery(null,$objRsm);

            if( empty($strLogin) )
            {
                throw new \Exception('El campo login vendedor es obligatorio.');
            }

            $strSelect = " SELECT IPE_SUB.LOGIN AS  LOGIN_SUBGERENTE,
                                  IPE_VEND.LOGIN AS LOGIN_VENDEDOR";

            $strFrom   = " FROM
                                INFO_PERSONA                  IPE_VEND
                                JOIN INFO_PERSONA_EMPRESA_ROL IPER_VEND ON IPE_VEND.ID_PERSONA     = IPER_VEND.PERSONA_ID
                                                                        AND IPER_VEND.REPORTA_PERSONA_EMPRESA_ROL_ID IS NOT NULL
                                                                        AND IPER_VEND.ESTADO IN (:estado)
                                JOIN INFO_PERSONA_EMPRESA_ROL IPER_SUB  ON IPER_SUB.ID_PERSONA_ROL = IPER_VEND.REPORTA_PERSONA_EMPRESA_ROL_ID
                                JOIN INFO_PERSONA             IPE_SUB   ON IPE_SUB.ID_PERSONA      = IPER_SUB.PERSONA_ID ";

            $strWhere  = " WHERE IPE_VEND.LOGIN = :strLogin " ;
            $objQuery->setParameter("strLogin", $strLogin);
            $objQuery->setParameter("estado", array('Activo','Modificado','Pendiente'));
            $objRsm->addScalarResult('LOGIN_SUBGERENTE', 'LOGIN_SUBGERENTE', 'string');
            $objRsm->addScalarResult('LOGIN_VENDEDOR',   'LOGIN_VENDEDOR', 'string');

            $strSql= $strSelect.$strFrom.$strWhere;

            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado;
    }
    
   /**
    * getClienteParametros, función que consulta los clientes según valores enviados como parámetro.
    * Costo:1180
    * @author Adrián Limones <alimones@telconet.ec>
    * @version 1.0 08-09-2020          
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.1 03-12-2020 Se agrega filtro a query.
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.2 02-12-2020 Se agrega filtro a query por identificacion, nombre completo.
    *
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.3 03-05-2022 Se mejora consulta para el caso de ingreso de letras minúsculas convierta búsqueda a mayúsculas.
    *                    
    * @return array de clientes.
    */  
   public function getClienteParametros($arrayParametros)
   {
       $objRsm      = new ResultSetMappingBuilder($this->_em);
       $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
       $strFiltro   = $arrayParametros['strSearch'];     

       $strQuery      = "SELECT
                        IPER.ID_PERSONA_ROL,
                        IPER.PERSONA_ID,
                        (SELECT 
                        CASE WHEN RAZON_SOCIAL IS NULL THEN 
                                   NOMBRES ||' '||APELLIDOS 
                               ELSE
                                  RAZON_SOCIAL   
                               END AS NOMBRES
                         FROM INFO_PERSONA WHERE ID_PERSONA = IPER.PERSONA_ID) NOMBRES
                       FROM
                        DB_COMERCIAL.INFO_PERSONA INFOPERSONA,
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                        DB_COMERCIAL.INFO_EMPRESA_ROL IER,
                        DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG,
                        DB_COMERCIAL.ADMI_ROL ADMIROL,
                        DB_COMERCIAL.ADMI_TIPO_ROL ATR
                       WHERE INFOPERSONA.ID_PERSONA = IPER.PERSONA_ID
                       AND IPER.EMPRESA_ROL_ID = IER.ID_EMPRESA_ROL
                       AND IEG.COD_EMPRESA = IER.EMPRESA_COD
                       AND ADMIROL.ID_ROL = IER.ROL_ID
                       AND ATR.ID_TIPO_ROL = ADMIROL.TIPO_ROL_ID
                       AND ATR.DESCRIPCION_TIPO_ROL = :strRol
                       AND IEG.COD_EMPRESA = :strEmpresaCod ";

       if(isset($strFiltro))
       {
           $strQuery .= " AND (UPPER(INFOPERSONA.RAZON_SOCIAL) LIKE UPPER('%".$strFiltro."%') OR ";
           $strQuery .= " UPPER(CONCAT(INFOPERSONA.APELLIDOS,' ' || INFOPERSONA.NOMBRES)) LIKE '%".$strFiltro."%' OR ";
           $strQuery .= " UPPER(CONCAT(INFOPERSONA.NOMBRES,' ' || INFOPERSONA.APELLIDOS)) LIKE '%".$strFiltro."%' OR ";
           $strQuery .= " INFOPERSONA.IDENTIFICACION_CLIENTE LIKE '%".$strFiltro."%' )";  
       }
       
       
       $strQuery .= " AND ROWNUM <= 10";
       
       $objRsm->addScalarResult('ID_PERSONA_ROL', 'intIdPersonaRol', 'integer');
       $objRsm->addScalarResult('PERSONA_ID', 'intIdPersona', 'integer');
       $objRsm->addScalarResult('NOMBRES', 'strNombres', 'string');
       
       $objNtvQuery->setParameter('strRol', $arrayParametros['strRol']);
       $objNtvQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
               
       $objNtvQuery->setSQL($strQuery);
       $objDatos = $objNtvQuery->getResult();

       $arrayResultado['objRegistros'] = $objDatos;

       return $arrayResultado;
   }    

    /**
     * Documentación para la función 'getSaldoPorCliente'.
     *
     * Función encargado de retornar el saldo pendiente por cliente.
     *
     * Costo 223
     *
     * @param array $arrayParametros [
     *                                  "intIdPersonEmpresaRol" => Id Persona Rol del cliente.
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                  "floatSaldoPendiente" => Saldo pendiente del cliente.
     *                                  "error"               => Mensaje de error en caso de existir.
     *                               ]
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
    public function getSaldoPorCliente($arrayParametros)
    {
        $intIdPersonEmpresaRol = ( isset($arrayParametros['intIdPersonEmpresaRol']) && !empty($arrayParametros['intIdPersonEmpresaRol']) )
                                    ? $arrayParametros['intIdPersonEmpresaRol'] : "";
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                    ? $arrayParametros['strPrefijoEmpresa'] : "";
        $arrayResultado        = array();
        $strMensajeError       = "";
        $floatSaldoPendiente   = 0;

        try
        {
            if(empty($strPrefijoEmpresa) || $strPrefijoEmpresa != "TN")
            {
                throw new \Exception("La consulta solo aplica para Telconet.");
            }
            if(empty($intIdPersonEmpresaRol))
            {
                throw new \Exception('Parámetros incompletos.');
            }
            $objRsm      = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strQuery    = " SELECT SUM(ECR.SALDO) AS SALDO_PENDIENTE
                                FROM
                                    (
                                        SELECT DISTINCT
                                            IPUNTO.ID_PUNTO
                                        FROM
                                            DB_COMERCIAL.INFO_PUNTO IPUNTO
                                        WHERE
                                            IPUNTO.PERSONA_EMPRESA_ROL_ID = :intIdPer
                                            ) IPUNTOSSERV
                                INNER JOIN DB_FINANCIERO.VISTA_ESTADO_CUENTA_RESUMIDO ECR ON ECR.PUNTO_ID = IPUNTOSSERV.ID_PUNTO ";
            $objNtvQuery->setParameter('intIdPer', $intIdPersonEmpresaRol);
            $objRsm->addScalarResult('SALDO_PENDIENTE', 'floatSaldoPendiente', 'float');
            $objNtvQuery->setSQL($strQuery);
            $arrayData = $objNtvQuery->getOneOrNullResult();
        }
        catch(\Exception $e)
        {
            $strMensajeError = $e->getMessage();
        }
        $arrayResultado['floatSaldoPendiente'] = $arrayData["floatSaldoPendiente"];
        $arrayResultado['error']               = $strMensajeError;
        return $arrayResultado;
    }
    /**
     * Documentación para la función 'getDistribuidor'.
     *
     * Función encargado de retornar si la identificación a ingresar pertenece a un Distribuidor.
     *
     * Costo 100
     *
     * @param array $arrayParametros [
     *                                  "strIdentificacion"     => Identificación del cliente.
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                  "resultado" => Datos del distribuidor.
     *                                  "error"     => Mensaje de error en caso de existir.
     *                               ]
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function getDistribuidor($arrayParametros)
    {
        $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                    ? $arrayParametros['strPrefijoEmpresa'] : "";
        $strIdentificacion     = ( isset($arrayParametros['strIdentificacion']) && !empty($arrayParametros['strIdentificacion']) )
                                    ? $arrayParametros['strIdentificacion'] : "";
        $arrayResultado        = array();
        $strMensajeError       = "";
        $strDescripcionCaract  = "IDENTIFICACION_CLT_DISTRIBUIDOR";
        $arrayEstadoServicio   = array('Anulado','Cancelado','Inactivo','Eliminado','Cancel','Rechazada');
        $strEstadoActivo       = "Activo";
        $strEstadoAprobada     = "Aprobada";
        $strDescripcionTipoSol = "SOLICITUD DE DISTRIBUIDOR";
        try
        {
            if(empty($strPrefijoEmpresa) || $strPrefijoEmpresa != "TN")
            {
                throw new \Exception("La consulta solo aplica para Telconet.");
            }
            if(empty($strIdentificacion))
            {
                throw new \Exception('Parámetros incompletos.');
            }
            $objRsm      = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect   = " SELECT
                                (
                                    SELECT
                                        IPE.RAZON_SOCIAL
                                    FROM
                                        DB_COMERCIAL.INFO_PERSONA               IPE
                                        JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL   IPER ON IPER.PERSONA_ID = IPE.ID_PERSONA
                                    WHERE
                                        IPER.ID_PERSONA_ROL = IPU.PERSONA_EMPRESA_ROL_ID
                                ) AS DISTRIBUIDOR,
                                (
                                    SELECT
                                        COUNT(ID_SERVICIO)
                                    FROM
                                        DB_COMERCIAL.INFO_SERVICIO ISER
                                    WHERE
                                        ISER.PUNTO_ID = IPU.ID_PUNTO
                                        AND ISER.ESTADO NOT IN (:arrayEstadoServicio)
                                ) AS CANTIDAD_SERVICIO,
                                IPU.ID_PUNTO,
                                IPU.PERSONA_EMPRESA_ROL_ID,
                                IPU.LOGIN,
                                IPU.USR_VENDEDOR,
                                IPU.ESTADO,
                                IPUC.VALOR,
                                (
                                    SELECT
                                        COUNT(*)
                                    FROM
                                        ADMI_TIPO_SOLICITUD       ATS
                                        JOIN INFO_DETALLE_SOLICITUD    IDS ON IDS.TIPO_SOLICITUD_ID = ATS.ID_TIPO_SOLICITUD
                                        JOIN INFO_DETALLE_SOL_CARACT   IDSC ON IDSC.DETALLE_SOLICITUD_ID = IDS.ID_DETALLE_SOLICITUD
                                        JOIN ADMI_CARACTERISTICA       AC ON AC.ID_CARACTERISTICA = IDSC.CARACTERISTICA_ID
                                                                    AND AC.DESCRIPCION_CARACTERISTICA = 'IDENTIFICACION_CLT_DISTRIBUIDOR'
                                    WHERE
                                        UPPER(ATS.DESCRIPCION_SOLICITUD) = :strDescripcionTipoSol
                                        AND ATS.ESTADO  = :strEstadoActivo
                                        AND IDS.ESTADO  = :strEstadoAprobada
                                        AND IDSC.ESTADO = :strEstadoAprobada
                                        AND IDSC.VALOR  = :strIdentificacion
                                ) AS CANT_SOL_APROBADA
                                 ";
            $strFrom     = " FROM
                                db_comercial.info_punto                  ipu
                                JOIN db_comercial.info_punto_caracteristica   ipuc ON ipu.id_punto = ipuc.punto_id
                                                                                    AND ipuc.estado = 'Activo'
                                JOIN db_comercial.admi_caracteristica         ac ON ipuc.caracteristica_id = ac.id_caracteristica
                                                                            AND ac.descripcion_caracteristica = :strDescripcionCaract ";
            $strWhere    = " WHERE DBMS_LOB.SUBSTR(ipuc.valor, 4000,1) = :strIdentificacion ";
            $strQuery    = $strSelect.$strFrom.$strWhere;

            $objRsm->addScalarResult('DISTRIBUIDOR'          , 'strDistribuidor'         , 'string');
            $objRsm->addScalarResult('CANTIDAD_SERVICIO'     , 'intCantidadServ'         , 'integer');
            $objRsm->addScalarResult('ID_PUNTO'              , 'intIdPunto'              , 'integer');
            $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'intIdPersonaEmpresaRol'  , 'integer');
            $objRsm->addScalarResult('LOGIN'                 , 'strLogin'                , 'string');
            $objRsm->addScalarResult('USR_VENDEDOR'          , 'strVendedor'             , 'string');
            $objRsm->addScalarResult('ESTADO'                , 'strEstado'               , 'string');
            $objRsm->addScalarResult('VALOR'                 , 'intCantidadServstrValor' , 'string');
            $objRsm->addScalarResult('CANT_SOL_APROBADA'     , 'intCantidadSolAprobada'  , 'integer');

            $objNtvQuery->setParameter('strIdentificacion'     , $strIdentificacion);
            $objNtvQuery->setParameter('strDescripcionCaract'  , $strDescripcionCaract);
            $objNtvQuery->setParameter('arrayEstadoServicio'   , $arrayEstadoServicio);
            $objNtvQuery->setParameter('strEstadoAprobada'     , $strEstadoAprobada);
            $objNtvQuery->setParameter('strEstadoActivo'       , $strEstadoActivo);
            $objNtvQuery->setParameter('strDescripcionTipoSol' , $strDescripcionTipoSol);

            $objNtvQuery->setSQL($strQuery);
            $arrayData = $objNtvQuery->getResult();
        }
        catch(\Exception $e)
        {
            $strMensajeError = $e->getMessage();
        }
        $arrayResultado['resultado'] = $arrayData;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado;
    }

    /**
     * funcion para saber mediante el punto si es cliente nuevo
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 16-06-2021
     * 
     * @param array $arrayParametros 
     * @return array  boolean
     * 
     * costoQuery: 10
     **/
    public function getEsCliente($arrayParametros)
    {
        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery = " select count(*) NUM_CONTRATO
                   from DB_COMERCIAL.INFO_PUNTO ip, 
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper, 
                        DB_COMERCIAL.INFO_CONTRATO ic, 
                        DB_COMERCIAL.INFO_EMPRESA_ROL ier,
                        DB_GENERAL.ADMI_ROL ar,
                        DB_GENERAL.ADMI_TIPO_ROL atr
                    where ip.PERSONA_EMPRESA_ROL_ID = iper.ID_PERSONA_ROL
                    and iper.ID_PERSONA_ROL = ic.PERSONA_EMPRESA_ROL_ID
                    and iper.EMPRESA_ROL_ID = ier.ID_EMPRESA_ROL
                    and ier.ROL_ID = ar.ID_ROL
                    and ar.TIPO_ROL_ID = atr.ID_TIPO_ROL
                    and ic.ESTADO = 'Activo'
                    and atr.DESCRIPCION_TIPO_ROL = 'Cliente'
                    and ip.ID_PUNTO = :punto
                    and ier.EMPRESA_COD = :empresa";

        $objRsm->addScalarResult(strtoupper('NUM_CONTRATO'), 'NUM_CONTRATO', 'integer');
        $objQuery->setParameter("punto", $arrayParametros['puntoId']);
        $objQuery->setParameter("empresa", $arrayParametros['empresaCod']);
        $objQuery->setSQL($strQuery); 

        $intCantContarto = $objQuery->getSingleScalarResult();
 
        if ($intCantContarto > 0)
        {
            return true;
        }
        
        return false;
        
    }
    /**
     * Documentación para el método 'getClienteByParametros'.
     *
     * Realiza consulta de cliente según los valores enviados como parámetros
     * Costo: 12
     * @param array $arrayParametros [
     *                                  "strIdentificacion"  => Identificación del cliente.
     *                                  "strEmpresaCod"      => Código de la empresa.
     *                                  "strDescRol"         => Descripción de rol.
     *                                  "arrayEstados"       => Array de estados.
     *                               ]
     *
     * @return InfoPersonaEmpresaRol $InfoPersonaEmpresaRol
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-07-2021
     */
    public function getClienteByParametros($arrayParametros)
    {
        $strQuery = "SELECT per
                        FROM 
                            schemaBundle:InfoPersona ip,
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol
                        WHERE 
                            per.empresaRolId = er.id   AND
                            er.rolId         = rol.id  AND
                            rol.tipoRolId    = trol.id AND
                            per.personaId    = ip.id   AND
                            ip.identificacionCliente = :identificacion AND
                            er.empresaCod            = :codEmpresa AND
                            trol.descripcionTipoRol IN (:descRol)                             
                            AND per.estado          IN (:estado) ";
        $objQuery = $this->_em->createQuery($strQuery);
        $objQuery->setParameter('identificacion', $arrayParametros['strIdentificacion']);
        $objQuery->setParameter('descRol', $arrayParametros['strDescRol']);
        $objQuery->setParameter('codEmpresa', $arrayParametros['strEmpresaCod']);
        $objQuery->setParameter('estado', $arrayParametros['arrayEstados']);
        $objInfoPersonaEmpresaRol = $objQuery->getResult();

        return $objInfoPersonaEmpresaRol;
    }
    
    /**
     * getArrayNombreVipPorPersonaRol
     * 
     * Método que obtiene correo del Ingeniero VIP de un cliente
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 27-08-2021
     * 
     * Costo : 11
     * 
     * @param  Array $arrayParametros
     *                               [
     *                                   intIdPersonaRol    Identificador del rol de cliente
     *                               ]
     * @return Array $arrayResultado  nombre Vip de cliente
     */
    public function getArrayNombreVipPorPersonaRol($arrayParametros)
    {        
        $objRsm   = new ResultSetMappingBuilder($this->_em);	      
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);	                

        $strSql = "  SELECT
                    upper(perfc.persona_id) AS PERSONA_ID
                FROM
                    db_comercial.info_persona_empresa_rol_carac pemprolc
                    JOIN db_comercial.admi_caracteristica carac 
                         ON pemprolc.caracteristica_id = carac.id_caracteristica
                    JOIN db_comercial.info_persona_empresa_rol peremprolvip 
                         ON coalesce(to_number(regexp_substr(pemprolc.valor,'^\d+') ),0) = peremprolvip.id_persona_rol
                    JOIN db_comercial.info_persona pervip 
                         ON ( peremprolvip.persona_id ) = ( pervip.id_persona )
                    JOIN db_comercial.info_persona_forma_contacto perfc 
                         ON pervip.id_persona = perfc.persona_id
                    JOIN db_comercial.admi_forma_contacto fc 
                         ON perfc.forma_contacto_id = fc.id_forma_contacto
                WHERE
                    carac.descripcion_caracteristica = :caractVipParam
                    AND   fc.descripcion_forma_contacto = :caractCorreoParam
                    AND   pemprolc.persona_empresa_rol_id =:idPersonalRolParam
                    AND   ROWNUM <= :rowNumParam
                ";

        $objRsm->addScalarResult('PERSONA_ID','idPersona','integer');                                   		                        

        $objQuery->setParameter('idPersonalRolParam', $arrayParametros['intIdPersonaRol']);  
        $objQuery->setParameter('caractVipParam', 'ID_VIP');  
        $objQuery->setParameter('caractCorreoParam', 'Correo Electronico');  
        $objQuery->setParameter('rowNumParam', 1);  

        $objQuery->setSQL($strSql);                   

        $arrayResultado = $objQuery->getOneOrNullResult();     
              
        return $arrayResultado;      
    }
    
    /**
     * Función que extrae el identificador de la cuadrilla por medio de su usuario Login
     * @author Wilmer Vera G.<wvera@telconet.ec>
     * @version 1.0,  12-03-2022
     *
     */
    public function getCuadrillaPorLoginUsuario($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strSql = " SELECT CUADRILLA_ID
                        FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL 
                        WHERE persona_id = :intPersonaId 
                        AND ESTADO = :strEstado
                        AND CUADRILLA_ID IS NOT NULL ";

            $objNativeQuery->setParameter("intPersonaId" , $arrayParametros['personaId'] );
            $objNativeQuery->setParameter("strEstado" , $arrayParametros['estado'] );

            $objResultSetMap->addScalarResult('CUADRILLA_ID'         , 'cuadrillaId'           , 'integer');

            $objNativeQuery->setSQL($strSql);

            $strResult = $objNativeQuery->getOneOrNullResult();

        }
        catch (\Exception $objException)
        {
            $strResult = null ;
        }
        return $strResult;
    }

    /**
     * getCrsPorLoginValidateServices
     * 
     * Valida que no existan puntos activos en cliente origen al hacer crs 
     * 
     * costoQuery: 19
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 24-01-2023
     * 
     * @param  array $arrayParametros [
     *   intIdPersonaRol    Identificador del rol de cliente destino
     *   intIdPunto         Identificador del punto
     *                                ]     
     * 
     * @return $arrayResultado 
     */
    public function getCrsPorLoginValidateServices($arrayParametros)
    { 
        
        $objResultSetMap = new ResultSetMappingBuilder($this->_em);
        $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
        $strSql = " SELECT 
                    des_ic.ID_CONTRATO,
                    des_ic.ESTADO ESTADO_CONTRATO ,
                    des_ia.ID_ADENDUM , 
                    des_ia.ESTADO  ESTADO_ADENDUM ,
                    des_ip2.ID_PUNTO ,
                    des_ip2.ESTADO  ESTADO_PUNTO ,
                    des_is2.ID_SERVICIO ,
                    des_is2.ESTADO  ESTADO_SERVICIO,
                    des_is2.OBSERVACION ID_SERVICIO_ORIGEN,
                    ORI_is2.ESTADO  ESTADO_SERVICIO_ORIGEN
                    FROM db_comercial.INFO_CONTRATO des_ic 
                    INNER JOIN db_comercial.INFO_PERSONA_EMPRESA_ROL des_iper ON des_IPER.ID_PERSONA_ROL  =  des_ic .PERSONA_EMPRESA_ROL_ID 
                    INNER JOIN db_comercial.INFO_PERSONA des_ip ON des_ip.ID_PERSONA = des_iper.PERSONA_ID 
                    INNER JOIN db_comercial.INFO_PUNTO des_ip2 ON des_IP2.PERSONA_EMPRESA_ROL_ID = des_ic.PERSONA_EMPRESA_ROL_ID 
                    INNER JOIN db_comercial.INFO_SERVICIO des_is2 ON des_is2.PUNTO_ID  = des_ip2.ID_PUNTO 
                    INNER JOIN db_comercial.INFO_ADENDUM des_ia ON des_ia.CONTRATO_ID  = des_ic.ID_CONTRATO  
                    INNER JOIN db_comercial.INFO_SERVICIO ori_is2 ON ori_is2.ID_SERVICIO =  CAST(des_is2.OBSERVACION   AS VARCHAR2(200)) 
                    WHERE des_ip2.PERSONA_EMPRESA_ROL_ID = :intIdPersonaRol 
                    AND des_ip2.ID_PUNTO = :intIdPunto 
                    AND ori_is2.ESTADO = :strEstadoServicio 
                     ";
          
        $objNativeQuery->setParameter('intIdPersonaRol', $arrayParametros['intIdPersonaRol']);  
        $objNativeQuery->setParameter('intIdPunto',      $arrayParametros['intIdPunto']);   
        $objNativeQuery->setParameter('strEstadoServicio',  'Activo'); 

        $objResultSetMap->addScalarResult('ID_CONTRATO',     'ID_CONTRATO',     'integer');
        $objResultSetMap->addScalarResult('ESTADO_CONTRATO', 'ESTADO_CONTRATO', 'string');
        $objResultSetMap->addScalarResult('ID_ADENDUM',      'ID_ADENDUM' ,     'integer');
        $objResultSetMap->addScalarResult('ESTADO_ADENDUM',  'ESTADO_ADENDUM',  'string');
        $objResultSetMap->addScalarResult('ID_PUNTO',        'ID_PUNTO' ,       'integer');
        $objResultSetMap->addScalarResult('ESTADO_PUNTO',    'ESTADO_PUNTO',    'string');
        $objResultSetMap->addScalarResult('ID_SERVICIO',     'ID_SERVICIO' ,    'integer');
        $objResultSetMap->addScalarResult('ESTADO_SERVICIO',        'ESTADO_SERVICIO',         'string');
        $objResultSetMap->addScalarResult('ID_SERVICIO_ORIGEN',     'ID_SERVICIO_ORIGEN',      'integer');
        $objResultSetMap->addScalarResult('ESTADO_SERVICIO_ORIGEN', 'ESTADO_SERVICIO_ORIGEN' , 'string');
         

        $objNativeQuery->setSQL($strSql); 

         
        $arrayResult = $objNativeQuery->getArrayResult();

        return $arrayResult;
     
}



}



