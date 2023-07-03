<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\ReturnResponse;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPuntoContactoRepository extends EntityRepository
{
/**
     * getResultadoTipoContactosPorPunto
     * 
     * Funcion que retorna cantidad de Contactos por Punto por y por Tipo de Contacto
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20-06-2016     
     * @param integer $intIdPunto      
     * @param string $strNombreRolContacto  
     * @return integer
     * 
     */
    public function getCantidadPorTipoContactoPorPunto($intIdPunto,$strNombreRolContacto)
    {            
        $query = $this->_em->createQuery("SELECT count(ptoContacto) from        
                                schemaBundle:InfoPuntoContacto ptoContacto,
                                schemaBundle:InfoPunto punto,
                                schemaBundle:InfoPersona contacto,                                
                                schemaBundle:InfoPersonaEmpresaRol prolContacto,
                                schemaBundle:InfoEmpresaRol emprolContacto,
                                schemaBundle:AdmiRol rolContacto,
                                schemaBundle:AdmiTipoRol trolContacto
                                
                                where ptoContacto.puntoId=punto.id
                                and ptoContacto.contactoId=contacto.id
                                and ptoContacto.personaEmpresaRolId=prolContacto.id
                                and prolContacto.empresaRolId=emprolContacto.id
                                and emprolContacto.rolId=rolContacto.id
                                and rolContacto.tipoRolId=trolContacto.id
                                and trolContacto.descripcionTipoRol=:strContacto
                                and rolContacto.descripcionRol=:strNombreRolContacto
                                and punto.id=:intIdPunto
                                and prolContacto.estado IN (:arrayEstado)
                                and ptoContacto.estado =:strEstado
        ");
               
        $query->setParameters(array('arrayEstado'          => array('Activo','Inactivo'),
                                    'strEstado'            => 'Activo',
                                    'strContacto'          => 'Contacto',
                                    'strNombreRolContacto' => $strNombreRolContacto,
                                    'intIdPunto'           => $intIdPunto
                                               
                                              ));                
        $intCantidadContactos = $query->getSingleScalarResult();
        if(!$intCantidadContactos)
        {
            $intCantidadContactos = 0;
        }
        return $intCantidadContactos;   
    }
    
    /**
     * findByPuntoIdYEstado
     * 
     * Funcion que retorna Contactos por Punto por y por Estado
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 28-06-2016     
     * @param integer $intIdPunto      
     * @param string  $strEstado  
     * @return datos
     * 
     */
    public function findByPuntoIdYEstado($intIdPunto,$strEstado)
    {            
        $query = $this->_em->createQuery("SELECT ptoContacto from        
                                schemaBundle:InfoPuntoContacto ptoContacto,
                                schemaBundle:InfoPunto punto
                                
                                where ptoContacto.puntoId=punto.id
                                and punto.id=:intIdPunto                                
                                and ptoContacto.estado =:strEstado");
               
        $query->setParameters(array('intIdPunto'           => $intIdPunto,
                                    'strEstado'            => $strEstado
                                               
                                   ));                
        $datos = $query->getResult();       
        return $datos;   
    }

     /**
     * 
     * Metodo que devuelve los correos dado un punto y un tipo de contacto ( rol ) enviado por parametro
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 23-06-2016
     *
     * @param type $intPunto
     * @param type $strRolContacto
     * @return type
     */
    public function getArrayContactosPorPuntoYTipo($intPunto , $strRolContacto)
    {
        $arrayResultado = array();
        
        try
        {
            $rsm   = new ResultSetMappingBuilder($this->_em);	      
            $query = $this->_em->createNativeQuery(null, $rsm);
            error_log('***getArrayContactosPorPuntoYTipo**'.$intPunto.'**'. $strRolContacto);
            $sql = "SELECT 
                        regexp_substr(FC.VALOR,'[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}') VALOR
                      FROM 
                        DB_COMERCIAL.INFO_PUNTO_CONTACTO PC,
                        DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO FC,
                        DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER,
                        DB_COMERCIAL.INFO_EMPRESA_ROL ER,
                        DB_COMERCIAL.ADMI_ROL R  
                      WHERE PC.punto_id        = :punto
                      AND PER.ID_PERSONA_ROL   = PC.PERSONA_EMPRESA_ROL_ID
                      AND PER.EMPRESA_ROL_ID   = ER.ID_EMPRESA_ROL
                      AND ER.ROL_ID            = R.ID_ROL
                      AND PC.CONTACTO_ID       = FC.PERSONA_ID
                      AND FC.FORMA_CONTACTO_ID = :formaContacto
                      AND FC.ESTADO            = :estado
                      AND PC.ESTADO            = :estado
                      AND R.DESCRIPCION_ROL    = :rolContacto ";

            $rsm->addScalarResult('VALOR','valor','string');

            $query->setParameter('estado','Activo'); 
            $query->setParameter('formaContacto',5); 
            $query->setParameter('rolContacto',$strRolContacto); 
            $query->setParameter('punto',$intPunto); 
            $query->setSQL($sql);	

            $arrayResultado = $query->getResult();                                                                     
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        
        return $arrayResultado;  
    }

    /**
     * crearContactoMasivo
     * Método que genera la creación masiva de contactos
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 17-10-2019
     *
     * @param array $arrayParametros
     * [
     *     strEmpresaRoles      => Id de roles del contacto
     *     strIdPuntos          => Id de puntos
     *     intIdCliente         => Id del cliente
     *     intIdOficina         => Id de oficina
     *     intIdPersona         => Id del contacto
     *     strCodEmpresa        => Id de empresa
     *     intCrearNivelCliente => Flag que indica si se debe crear a nivel cliente
     *     strUsuario           => Usuario creador
     *     strIp                => Ip creador
     *     arrayExtraParams     => Parámetros adicionales
     * ]
     *
     * @return array $arrayRespuesta
     * [
     *     strMensaje => Mensaje de error si existiese
     * ]
     */
    public function crearContactoMasivo($arrayParametros)
    {
        $strIdRoles           = isset($arrayParametros['strEmpresaRoles']) ? $arrayParametros['strEmpresaRoles'] : '';
        $strIdPuntos          = isset($arrayParametros['strIdPuntos']) ? $arrayParametros['strIdPuntos'] : '';
        $intIdCliente         = isset($arrayParametros['intIdCliente']) ? $arrayParametros['intIdCliente'] : 0;
        $intIdOficina         = isset($arrayParametros['intIdOficina']) ? $arrayParametros['intIdOficina'] : 0;
        $intIdPersona         = isset($arrayParametros['intIdPersona']) ? $arrayParametros['intIdPersona'] : 0;
        $strCodEmpresa        = isset($arrayParametros['strCodEmpresa']) ? $arrayParametros['strCodEmpresa'] : '10';
        $intCrearNivelCliente = isset($arrayParametros['intCrearNivelCliente']) ? $arrayParametros['intCrearNivelCliente'] : 0;
        $strUsuario           = isset($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : '';
        $strIp                = isset($arrayParametros['strIp']) ? $arrayParametros['strIp'] : '';
        $strDescripcionRol1   = isset($arrayParametros['arrayExtraParams']['strDescripcionRol1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionRol1'] :
            '';
        $strDescripcionCarac1 = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac1']
            : '';
        $strDescripcionCarac2 = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac2'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac2']
            : '';
        $strEscalabilidad     = isset($arrayParametros['arrayExtraParams']['strEscalabilidad'])
            ? $arrayParametros['arrayExtraParams']['strEscalabilidad']
            : '';
        $strHorario           = isset($arrayParametros['arrayExtraParams']['strHorario'])
            ? $arrayParametros['arrayExtraParams']['strHorario']
            : '';
        $arrayExtraParams     = array('intIdOficina'         => $intIdOficina,
                                      'strCodEmpresa'        => $strCodEmpresa,
                                      'strUsuario'           => $strUsuario,
                                      'strIp'                => $strIp,
                                      'strDescripcionRol1'   => $strDescripcionRol1,
                                      'strDescripcionCarac1' => $strDescripcionCarac1,
                                      'strDescripcionCarac2' => $strDescripcionCarac2,
                                      'strEscalabilidad'     => $strEscalabilidad,
                                      'strHorario'           => $strHorario);

        $strMsjError          = '';

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_CONTACTO_MASIVO.P_CREAR_MASIVO(:PCL_IDROLES,
                                                                              :PCL_IDPUNTOS,
                                                                              :PCL_EXTRAPARAMS,
                                                                              :PN_IDCLIENTE,
                                                                              :PN_IDPERSONA,
                                                                              :PN_NIVEL_CLIENTE,
                                                                              :PV_MSGERROR);
                                                                               END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                                   $arrayConnParams['password'],
                                   $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objRolesClob = oci_new_descriptor($objConn);
            $objPuntosClob = oci_new_descriptor($objConn);
            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objRolesClob->writetemporary($strIdRoles);
            $objPuntosClob->writetemporary($strIdPuntos);
            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt, ':PCL_IDROLES', $objRolesClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PCL_IDPUNTOS', $objPuntosClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PCL_EXTRAPARAMS', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt, ':PN_IDCLIENTE', $intIdCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt, ':PN_IDPERSONA', $intIdPersona, 32, SQLT_INT);
            oci_bind_by_name($objStmt, ':PN_NIVEL_CLIENTE', $intCrearNivelCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt, ':PV_MSGERROR', $strMsjError, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMsjError);

                if(empty($strMsjError))
                {
                    $strMsjError = $strOCIError['message'];
                }
            }
            else
            {
                $strMsjError = trim($strMsjError);
            }

            if (empty($strMsjError))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMsjError);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * eliminarContactoMasivo
     * Método que genera la eliminación masiva de contactos
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 31-10-2019
     *
     * @param array $arrayParametros
     * [
     *     intIdCliente           => Id del cliente
     *     intIdPersona           => Id del contacto
     *     intIdOficina           => Id de oficina
     *     strCodEmpresa          => Id de empresa
     *     strUsuario             => Usuario creador
     *     strIp                  => Ip creador
     * ]
     *
     * @return array $arrayRespuesta
     * [
     *     strMensaje => Mensaje de error si existiese
     * ]
     */
    public function eliminarContactoMasivo($arrayParametros)
    {
        $intIdCliente           = isset($arrayParametros['intIdCliente']) ? $arrayParametros['intIdCliente'] : 0;
        $intIdPersona           = isset($arrayParametros['intIdPersona']) ? $arrayParametros['intIdPersona'] : 0;
        $intIdOficina           = isset($arrayParametros['intIdOficina']) ? $arrayParametros['intIdOficina'] : 0;
        $strCodEmpresa          = isset($arrayParametros['strCodEmpresa']) ? $arrayParametros['strCodEmpresa'] : '10';
        $strUsuario             = isset($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : '';
        $strIp                  = isset($arrayParametros['strIp']) ? $arrayParametros['strIp'] : '';
        $arrayExtraParams       = array('intIdOficina'         => $intIdOficina,
                                        'strCodEmpresa'        => $strCodEmpresa,
                                        'strUsuario'           => $strUsuario,
                                        'strIp'                => $strIp);
        $strMsjError           = '';

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_CONTACTO_MASIVO.P_ELIMINAR_MASIVO(:PN_IDCLIENTE,
                                                                                 :PN_IDPERSONA,
                                                                                 :PCL_EXTRAPARAMS,
                                                                                 :PV_MSGERROR);
                                                                                  END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                $arrayConnParams['password'],
                $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt,':PN_IDCLIENTE', $intIdCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_IDPERSONA', $intIdPersona, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PCL_EXTRAPARAMS', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PV_MSGERROR', $strMsjError, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMsjError);

                if(empty($strMsjError))
                {
                    $strMsjError = $strOCIError['message'];
                }
            }
            else
            {
                $strMsjError = trim($strMsjError);
            }

            if (empty($strMsjError))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMsjError);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * asignarTipoContactoMasivo
     * Método que genera la asignación masiva de roles a contactos
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 17-10-2019
     *
     * @param array $arrayParametros
     * [
     *     strEmpresaRoles       => Id de roles del contacto
     *     intIdCliente          => Id del cliente
     *     intAsignaNivelCliente => Flag que indica si se debe asginar rol a nivel cliente
     *     intIdOficina          => Id de oficina
     *     intIdPersona          => Id del contacto
     *     strCodEmpresa         => Id de empresa
     *     strUsuario            => Usuario creador
     *     strIp                 => Ip creador
     *     arrayExtraParams      => Parámetros adicionales
     * ]
     *
     * @return array $arrayRespuesta
     * [
     *     strMensaje => Mensaje de error si existiese
     * ]
     */
    public function asignarTipoContactoMasivo($arrayParametros)
    {
        $strIdRoles            = isset($arrayParametros['strEmpresaRoles']) ? $arrayParametros['strEmpresaRoles'] : '';
        $intIdCliente          = isset($arrayParametros['intIdCliente']) ? $arrayParametros['intIdCliente'] : 0;
        $intAsignaNivelCliente = isset($arrayParametros['intAsignaNivelCliente']) ? $arrayParametros['intAsignaNivelCliente'] : 1;
        $intIdOficina          = isset($arrayParametros['intIdOficina']) ? $arrayParametros['intIdOficina'] : 0;
        $intIdPersona          = isset($arrayParametros['intIdPersona']) ? $arrayParametros['intIdPersona'] : 0;
        $strCodEmpresa         = isset($arrayParametros['strCodEmpresa']) ? $arrayParametros['strCodEmpresa'] : '10';
        $strUsuario            = isset($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : '';
        $strIp                 = isset($arrayParametros['strIp']) ? $arrayParametros['strIp'] : '';
        $strDescripcionRol1    = isset($arrayParametros['arrayExtraParams']['strDescripcionRol1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionRol1']
            : '';
        $strDescripcionCarac1  = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac1']
            : '';
        $strDescripcionCarac2  = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac2'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac2']
            : '';
        $strEscalabilidad      = isset($arrayParametros['arrayExtraParams']['strEscalabilidad'])
            ? $arrayParametros['arrayExtraParams']['strEscalabilidad']
            : '';
        $strHorario            = isset($arrayParametros['arrayExtraParams']['strHorario'])
            ? $arrayParametros['arrayExtraParams']['strHorario']
            : '';
        $arrayExtraParams      = array('intIdOficina'         => $intIdOficina,
                                       'strCodEmpresa'        => $strCodEmpresa,
                                       'strUsuario'           => $strUsuario,
                                       'strIp'                => $strIp,
                                       'strDescripcionRol1'   => $strDescripcionRol1,
                                       'strDescripcionCarac1' => $strDescripcionCarac1,
                                       'strDescripcionCarac2' => $strDescripcionCarac2,
                                       'strEscalabilidad'     => $strEscalabilidad,
                                       'strHorario'           => $strHorario);
        $strMsjError           = '';

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_CONTACTO_MASIVO.P_ASIGNAR_TIPO_MASIVO(:PCL_IDROLES,
                                                                                     :PCL_EXTRAPARAMS,
                                                                                     :PN_IDCLIENTE,
                                                                                     :PN_IDPERSONA,
                                                                                     :PN_ASIGNA_CLIENTE,
                                                                                     :PV_MSGERROR);
                                                                                      END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                $arrayConnParams['password'],
                $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objRolesClob = oci_new_descriptor($objConn);
            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objRolesClob->writetemporary($strIdRoles);
            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt,':PCL_IDROLES', $objRolesClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PCL_EXTRAPARAMS', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PN_IDCLIENTE', $intIdCliente,32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_IDPERSONA', $intIdPersona,32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_ASIGNA_CLIENTE', $intAsignaNivelCliente,32, SQLT_INT);
            oci_bind_by_name($objStmt,':PV_MSGERROR', $strMsjError, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMsjError);

                if(empty($strMsjError))
                {
                    $strMsjError = $strOCIError['message'];
                }
            }
            else
            {
                $strMsjError = trim($strMsjError);
            }

            if (empty($strMsjError))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMsjError);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * eliminarTipoContactoMasivo
     * Método que genera la eliminación masiva de roles a contactos
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 18-10-2019
     *
     * @param array $arrayParametros
     * [
     *     strIdRoles             => Ids de INFO_PERSONA_EMPRESA_ROL a eliminar
     *     intIdCliente           => Id del cliente
     *     intEliminaNivelCliente => Flag que indica si se debe eliminar rol a nivel cliente
     *     intIdOficina           => Id de oficina
     *     intIdPersona           => Id del contacto
     *     strCodEmpresa          => Id de empresa
     *     strUsuario             => Usuario creador
     *     strIp                  => Ip creador
     *     arrayExtraParams       => Parámetros adicionales
     * ]
     *
     * @return array $arrayRespuesta
     * [
     *     strMensaje => Mensaje de error si existiese
     * ]
     */
    public function eliminarTipoContactoMasivo($arrayParametros)
    {
        $strIdRoles             = isset($arrayParametros['strIdRoles']) ? $arrayParametros['strIdRoles'] : '';
        $intIdCliente           = isset($arrayParametros['intIdCliente']) ? $arrayParametros['intIdCliente'] : 0;
        $intEliminaNivelCliente = isset($arrayParametros['intEliminaNivelCliente']) ? $arrayParametros['intEliminaNivelCliente'] : 1;
        $intIdOficina           = isset($arrayParametros['intIdOficina']) ? $arrayParametros['intIdOficina'] : 0;
        $intIdPersona           = isset($arrayParametros['intIdPersona']) ? $arrayParametros['intIdPersona'] : 0;
        $strCodEmpresa          = isset($arrayParametros['strCodEmpresa']) ? $arrayParametros['strCodEmpresa'] : '10';
        $strUsuario             = isset($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : '';
        $strIp                  = isset($arrayParametros['strIp']) ? $arrayParametros['strIp'] : '';
        $strDescripcionRol1     = isset($arrayParametros['arrayExtraParams']['strDescripcionRol1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionRol1']
            : '';
        $strDescripcionCarac1   = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac1']
            : '';
        $strDescripcionCarac2   = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac2'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac2']
            : '';
        $strEscalabilidad       = isset($arrayParametros['arrayExtraParams']['strEscalabilidad'])
            ? $arrayParametros['arrayExtraParams']['strEscalabilidad']
            : '';
        $strHorario             = isset($arrayParametros['arrayExtraParams']['strHorario'])
            ? $arrayParametros['arrayExtraParams']['strHorario']
            : '';
        $arrayExtraParams       = array('intIdOficina'         => $intIdOficina,
                                        'strCodEmpresa'        => $strCodEmpresa,
                                        'strUsuario'           => $strUsuario,
                                        'strIp'                => $strIp,
                                        'strDescripcionRol1'   => $strDescripcionRol1,
                                        'strDescripcionCarac1' => $strDescripcionCarac1,
                                        'strDescripcionCarac2' => $strDescripcionCarac2,
                                        'strEscalabilidad'     => $strEscalabilidad,
                                        'strHorario'           => $strHorario);
        $strMsjError           = '';

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_CONTACTO_MASIVO.P_ELIMINAR_TIPO_MASIVO(:PCL_IDROLES,
                                                                                      :PCL_EXTRAPARAMS,
                                                                                      :PN_IDCLIENTE,
                                                                                      :PN_IDPERSONA,
                                                                                      :PN_ELIMINA_CLIENTE,
                                                                                      :PV_MSGERROR);
                                                                                       END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                $arrayConnParams['password'],
                $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objRolesClob = oci_new_descriptor($objConn);
            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objRolesClob->writetemporary($strIdRoles);
            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt,':PCL_IDROLES', $objRolesClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PCL_EXTRAPARAMS', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PN_IDCLIENTE', $intIdCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_IDPERSONA', $intIdPersona, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_ELIMINA_CLIENTE', $intEliminaNivelCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PV_MSGERROR', $strMsjError, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMsjError);

                if(empty($strMsjError))
                {
                    $strMsjError = $strOCIError['message'];
                }
            }
            else
            {
                $strMsjError = trim($strMsjError);
            }

            if (empty($strMsjError))
            {
                $arrayRespuesta = array ('strMensaje' => 'OK');
            }
            else
            {
                $arrayRespuesta = array ('strMensaje' => $strMsjError);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * duplicarContactoMasivo
     * Método que genera la duplicación masiva de contactos
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 19-10-2019
     *
     * @param array $arrayParametros
     * [
     *     strIdRoles             => Id de roles del contacto
     *     strIdPuntos            => Id de puntos en donde se duplicará contacto
     *     intIdCliente           => Id del cliente
     *     intDuplicaNivelCliente => Flag que indica si se debe duplicar contacto a nivel cliente
     *     intIdOficina           => Id de oficina
     *     intIdPersona           => Id del contacto
     *     strCodEmpresa          => Id de empresa
     *     strUsuario             => Usuario creador
     *     strIp                  => Ip creador
     *     arrayExtraParams       => Parámetros adicionales
     * ]
     *
     * @return array $arrayRespuesta
     * [
     *     strMensaje         => Mensaje general de error en la duplicación del contacto si existiese
     *     strMsjNivelCliente => Mensaje de error en la duplicación del contacto a nivel cliente si existiese
     *     strLoginRepetidos  => Nombres de los login en donde ya existe el contacto
     * ]
     */
    public function duplicarContactoMasivo($arrayParametros)
    {
        $strIdRoles             = isset($arrayParametros['strIdRoles']) ? $arrayParametros['strIdRoles'] : '';
        $strIdPuntos            = isset($arrayParametros['strIdPuntos']) ? $arrayParametros['strIdPuntos'] : '';
        $intIdCliente           = isset($arrayParametros['intIdCliente']) ? $arrayParametros['intIdCliente'] : 0;
        $intDuplicaNivelCliente = isset($arrayParametros['intDuplicaNivelCliente']) ? $arrayParametros['intDuplicaNivelCliente'] : 0;
        $intIdOficina           = isset($arrayParametros['intIdOficina']) ? $arrayParametros['intIdOficina'] : 0;
        $intIdPersona           = isset($arrayParametros['intIdPersona']) ? $arrayParametros['intIdPersona'] : 0;
        $strCodEmpresa          = isset($arrayParametros['strCodEmpresa']) ? $arrayParametros['strCodEmpresa'] : '10';
        $strUsuario             = isset($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : '';
        $strIp                  = isset($arrayParametros['strIp']) ? $arrayParametros['strIp'] : '';
        $strDescripcionRol1     = isset($arrayParametros['arrayExtraParams']['strDescripcionRol1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionRol1'] :
            '';
        $strDescripcionCarac1   = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac1'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac1']
            : '';
        $strDescripcionCarac2   = isset($arrayParametros['arrayExtraParams']['strDescripcionCarac2'])
            ? $arrayParametros['arrayExtraParams']['strDescripcionCarac2']
            : '';
        $intCantidadLoginLimite = isset($arrayParametros['arrayExtraParams']['intCantidadLoginLimite'])
            ? $arrayParametros['arrayExtraParams']['intCantidadLoginLimite']
            : 5;
        $strEscalabilidad       = isset($arrayParametros['arrayExtraParams']['strEscalabilidad'])
            ? $arrayParametros['arrayExtraParams']['strEscalabilidad']
            : '';
        $strHorario             = isset($arrayParametros['arrayExtraParams']['strHorario'])
            ? $arrayParametros['arrayExtraParams']['strHorario']
            : '';
        $arrayExtraParams       = array('intIdOficina'         => $intIdOficina,
                                      'strCodEmpresa'          => $strCodEmpresa,
                                      'strUsuario'             => $strUsuario,
                                      'strIp'                  => $strIp,
                                      'intLoginLimite'         => $intCantidadLoginLimite,
                                      'strDescripcionRol1'     => $strDescripcionRol1,
                                      'strDescripcionCarac1'   => $strDescripcionCarac1,
                                      'strDescripcionCarac2'   => $strDescripcionCarac2,
                                      'strEscalabilidad'       => $strEscalabilidad,
                                      'strHorario'             => $strHorario);
        $intLoginRepetidos      = 0;
        $strLoginRepetidos      = '';
        $strMsjNivelCliente     = '';
        $strMsjError            = '';

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_CONTACTO_MASIVO.P_DUPLICAR_MASIVO(:PCL_IDROLES,
                                                                                 :PCL_IDPUNTOS,
                                                                                 :PCL_EXTRAPARAMS,
                                                                                 :PN_IDCLIENTE,
                                                                                 :PN_IDPERSONA,
                                                                                 :PN_DUPLICA_CLIENTE,
                                                                                 :PN_LOGIN_REPETIDOS,
                                                                                 :PV_LOGIN_REPETIDOS,
                                                                                 :PV_MSG_NIVEL_CLIENTE,
                                                                                 :PV_MSGERROR);
                                                                                  END;";

            $arrayConnParams = $this->getEntityManager()->getConnection()->getParams();

            $objConn = oci_connect($arrayConnParams['user'],
                $arrayConnParams['password'],
                $arrayConnParams['dbname']);

            $objStmt = oci_parse($objConn, $strSql);

            $objRolesClob = oci_new_descriptor($objConn);
            $objPuntosClob = oci_new_descriptor($objConn);
            $objExtraParamsClob = oci_new_descriptor($objConn);

            $objRolesClob->writetemporary($strIdRoles);
            $objPuntosClob->writetemporary($strIdPuntos);
            $objExtraParamsClob->writetemporary(json_encode($arrayExtraParams));

            oci_bind_by_name($objStmt,':PCL_IDROLES', $objRolesClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PCL_IDPUNTOS', $objPuntosClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PCL_EXTRAPARAMS', $objExtraParamsClob, -1, SQLT_CLOB);
            oci_bind_by_name($objStmt,':PN_IDCLIENTE', $intIdCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_IDPERSONA', $intIdPersona, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_DUPLICA_CLIENTE', $intDuplicaNivelCliente, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PN_LOGIN_REPETIDOS', $intLoginRepetidos, 32, SQLT_INT);
            oci_bind_by_name($objStmt,':PV_LOGIN_REPETIDOS', $strLoginRepetidos, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt,':PV_MSG_NIVEL_CLIENTE', $strMsjNivelCliente, 32*1024, SQLT_CHR);
            oci_bind_by_name($objStmt,':PV_MSGERROR', $strMsjError, 32*1024, SQLT_CHR);

            if(oci_execute($objStmt) === false)
            {
                $strOCIError = oci_error($objStmt);
                $strMsjError = trim($strMsjError);

                if(empty($strMsjError))
                {
                    $strMsjError = $strOCIError['message'];
                }
            }
            else
            {
                $strMsjError = trim($strMsjError);
            }

            if (empty($strMsjError))
            {
                $arrayRespuesta = array ('strMensaje'         => 'OK',
                                         'strMsjNivelCliente' => $strMsjNivelCliente,
                                         'strLoginRepetidos'  => $strLoginRepetidos,
                                         'intLoginRepetidos'  => $intLoginRepetidos);
            }
            else
            {
                $arrayRespuesta = array ('strMensaje'         => $strMsjError,
                                         'strMsjNivelCliente' => $strMsjNivelCliente,
                                         'strLoginRepetidos'  => $strLoginRepetidos,
                                         'intLoginRepetidos'  => $intLoginRepetidos);
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array ('strMensaje' => $e->getMessage());
        }

        return $arrayRespuesta;
    }

    /**
     * Función encarga de retornar los contactos de un punto
     *
     * Costo 20
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 12-12-2019
     *
     * @param $arrayParametros [
     *                           intIdPersonaEmpresaRol : Id persona empresa rol del cliente.
     *                           arrayStrEstadoPunto    : Lista de los estados del punto.
     *                           strEstadoFormaContacto : Estado de la INFO_PERSONA_FORMA_CONTACTO
     *                           strEstadoPuntoContacto : Estado de la INFO_PUNTO_CONTACTO.
     *                           arrayStrRolContacto    : Lista de los roles de contacto.
     *                           arrayStrFormaContacto  : Lista de las formas de contacto.
     *                           strUsuario             : Usuario quien realiza la petición.
     *                           strIp                  : Ip del usuario quien realiza la petición.
     *                           objUtilService         : Objeto del service Util.
     *                         ]
     * @return $arrayResultado
     */
    public function getContactosPunto($arrayParametros)
    {
        $objUtilService = $arrayParametros['objUtilService'];
        $strUsuario     = $arrayParametros['strUsuario'] ? $arrayParametros['strUsuario'] : 'Telcos+';
        $strIp          = $arrayParametros['strIp']      ? $arrayParametros['strIp']      : '127.0.0.1';
        $strFrom        = '';
        $strWhere       = '';

        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            if (isset($arrayParametros['arrayStrRolContacto']) && !empty($arrayParametros['arrayStrRolContacto']))
            {
                $strWhere .= "AND ADRO.DESCRIPCION_ROL IN (:arrayStrRolContacto) ";
                $objNativeQuery->setParameter('arrayStrRolContacto' , array_values($arrayParametros['arrayStrRolContacto']));
            }
            else
            {
                $strFrom.= ",DB_GENERAL.ADMI_PARAMETRO_CAB ADPACAB ".
                           ",DB_GENERAL.ADMI_PARAMETRO_DET ADPADET ";

                $strWhere.= "AND ADPACAB.ID_PARAMETRO        = ADPADET.PARAMETRO_ID ".
                            "AND ADPACAB.NOMBRE_PARAMETRO    = :strTipoContacto ".
                            "AND ADPADET.VALOR1              = :strValor1 ".
                            "AND ADPACAB.ESTADO              = :strEstadoParametro ".
                            "AND ADPADET.ESTADO              = :strEstadoParametro ".
                            "AND UPPER(ADRO.DESCRIPCION_ROL) = UPPER(ADPADET.VALOR2)";

                $objNativeQuery->setParameter('strTipoContacto'    , 'PARAMETROS_TELCOGRAF');
                $objNativeQuery->setParameter('strValor1'          , 'CONTACTOS');
                $objNativeQuery->setParameter('strEstadoParametro' , 'Activo');
            }

            $strSql = "SELECT ".
                        "REGEXP_SUBSTR(INPEFOCO.VALOR, ".
                          "'[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}') AS CONTACTO, ".
                        "ADRO.DESCRIPCION_ROL AS TIPO_CONTACTO ".
                      "FROM ".
                         "DB_COMERCIAL.INFO_PUNTO                  INPU ".
                        ",DB_COMERCIAL.INFO_PUNTO_CONTACTO         INPUCO ".
                        ",DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL    INPEEMRO ".
                        ",DB_COMERCIAL.INFO_EMPRESA_ROL            INEMRO ".
                        ",DB_COMERCIAL.ADMI_ROL                    ADRO ".
                        ",DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO INPEFOCO ".
                        ",DB_COMERCIAL.ADMI_FORMA_CONTACTO         ADFOCO ".
                        "$strFrom".
                      "WHERE ".
                        "INPU.PERSONA_EMPRESA_ROL_ID       = :intIdPersonaEmpresaRol ".
                        "AND INPU.ID_PUNTO                 = INPUCO.PUNTO_ID ".
                        "AND INPUCO.PERSONA_EMPRESA_ROL_ID = INPEEMRO.ID_PERSONA_ROL ".
                        "AND INPEEMRO.EMPRESA_ROL_ID       = INEMRO.ID_EMPRESA_ROL ".
                        "AND INEMRO.ROL_ID                 = ADRO.ID_ROL ".
                        "AND INPUCO.CONTACTO_ID            = INPEFOCO.PERSONA_ID ".
                        "AND INPEFOCO.FORMA_CONTACTO_ID    = ADFOCO.ID_FORMA_CONTACTO ".
                        "AND INPU.ESTADO                  IN (:arrayStrEstadoPunto) ".
                        "AND INPEFOCO.ESTADO               = :strEstadoFormaContacto ".
                        "AND INPUCO.ESTADO                 = :strEstadoPuntoContacto ".
                        "AND UPPER(ADFOCO.DESCRIPCION_FORMA_CONTACTO) IN (:arrayStrFormaContacto) ".
                        "$strWhere".
                      "GROUP BY INPEFOCO.VALOR,ADRO.DESCRIPCION_ROL";

            $objNativeQuery->setParameter('intIdPersonaEmpresaRol' , $arrayParametros['intIdPersonaEmpresaRol']);
            $objNativeQuery->setParameter('arrayStrEstadoPunto'    , array_values($arrayParametros['arrayStrEstadoPunto']));
            $objNativeQuery->setParameter('strEstadoFormaContacto' , $arrayParametros['strEstadoFormaContacto']);
            $objNativeQuery->setParameter('strEstadoPuntoContacto' , $arrayParametros['strEstadoPuntoContacto']);
            $objNativeQuery->setParameter('arrayStrFormaContacto'  , array_map('strtoupper',
                                                                        array_values($arrayParametros['arrayStrFormaContacto'])) );

            $objResultSetMap->addScalarResult('CONTACTO'      , 'contacto'     , 'string');
            $objResultSetMap->addScalarResult('TIPO_CONTACTO' , 'tipoContacto' , 'string');

            $objNativeQuery->setSQL("SELECT A.* FROM ($strSql) A ORDER BY A.TIPO_CONTACTO ASC");

            $arrayResult    = $objNativeQuery->getResult();
            $arrayRespuesta = array("status" => true,
                                    "total"  => count($arrayResult),
                                    "result" => $arrayResult);
        }
        catch(\Exception $objException)
        {
            $arrayRespuesta = array ("status" => false,
                                     "result" => null);

            if (is_object($objUtilService))
            {
                $objUtilService->insertError('Telcos+',
                                             'InfoPuntoContactoRepository->getContactosPunto',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);
            }
        }
        return $arrayRespuesta;
    }


    /**
     * Función encarga de retornar las formas de contacto de un punto
     *
     * Costo 15
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 24-11-2020
     *
     * @param $arrayParametros [
     *                           intIdPunto          : Id del punto.
     *                           strRolContacto      : rol del contacto.
     *                           arrayFormasContacto : Lista de las formas de contacto.
     *                         ]
     * @return $arrayResultado
     */
    public function getFormasContactoPorPunto($arrayParametros)
    {
        $intPunto            = $arrayParametros['intIdPunto'];
        $strRolContacto      = $arrayParametros['strRolContacto'];
        $arrayFormasContacto = $arrayParametros['arrayFormasContacto'];
        $strWhere            = "";
        $strWhereTelefonos   = "";
        $arrayResultado      = array();

        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSql   = "SELECT FCON.DESCRIPCION_FORMA_CONTACTO,
                             CASE WHEN FCON.DESCRIPCION_FORMA_CONTACTO = 'Correo Electronico' THEN
                                       regexp_substr(FC.VALOR,'[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}')
                             ELSE FC.VALOR END VALOR
                         FROM
                             DB_COMERCIAL.INFO_PUNTO_CONTACTO PC
                             JOIN DB_COMERCIAL.INFO_PERSONA_FORMA_CONTACTO FC ON PC.CONTACTO_ID = FC.PERSONA_ID
                             JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PER   ON PER.ID_PERSONA_ROL = PC.PERSONA_EMPRESA_ROL_ID
                             JOIN DB_COMERCIAL.INFO_EMPRESA_ROL ER            ON PER.EMPRESA_ROL_ID   = ER.ID_EMPRESA_ROL
                             JOIN DB_COMERCIAL.ADMI_ROL R                     ON ER.ROL_ID = R.ID_ROL
                             JOIN DB_COMERCIAL.ADMI_FORMA_CONTACTO FCON       ON FC.FORMA_CONTACTO_ID = FCON.ID_FORMA_CONTACTO
                         WHERE
                             FC.ESTADO     = :estado
                             AND PC.ESTADO = :estado ";

            if (isset($intPunto) && !empty($intPunto))
            {
                $strWhere .= ' AND PC.punto_id = :punto';
                $objQuery->setParameter('punto',$intPunto);
            }

            if ( isset($arrayFormasContacto) && !empty($arrayFormasContacto) )
            {
                $strWhere .= ' AND UPPER(FCON.DESCRIPCION_FORMA_CONTACTO) IN (:formaContactoIn)';
                $objQuery->setParameter('formaContactoIn',$arrayFormasContacto);
            }

            if ( isset($strRolContacto) && !empty($strRolContacto ) )
            {
                $strWhere .= ' AND UPPER(R.DESCRIPCION_ROL) = UPPER(:rolContacto)';
                $objQuery->setParameter('rolContacto',$strRolContacto );
            }

            $strSql = $strSql . $strWhere . $strWhereTelefonos." ORDER BY PC.ID_PUNTO_CONTACTO DESC";
            $objQuery->setParameter('estado','Activo');

            $objRsm->addScalarResult('DESCRIPCION_FORMA_CONTACTO','descripcion','string');
            $objRsm->addScalarResult('VALOR','valor','string');

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
     * Funcion que permite obtener los contactos registrados en el punto
     * 
     * @param array $arrayParametros ('intIdPunto' => int)
     * @return array 
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 - Version inicial
     */
    public function getPersonaContactosPorPunto($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);        
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strSql  = "SELECT EMP.ID_PERSONA_ROL, PER.ID_PERSONA, PER.NOMBRES, PER.APELLIDOS, PER.IDENTIFICACION_CLIENTE, ROL.DESCRIPCION_ROL
                    FROM DB_COMERCIAL.INFO_PUNTO PUN
                    INNER JOIN DB_COMERCIAL.INFO_PUNTO_CONTACTO CONT ON CONT.PUNTO_ID = PUN.ID_PUNTO
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA PER ON PER.ID_PERSONA = CONT.CONTACTO_ID
                    INNER JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL EMP ON EMP.PERSONA_ID = CONT.CONTACTO_ID
                    INNER JOIN DB_COMERCIAL.INFO_EMPRESA_ROL EROL ON EROL.ID_EMPRESA_ROL = EMP.EMPRESA_ROL_ID
                    INNER JOIN DB_COMERCIAL.ADMI_ROL ROL ON ROL.ID_ROL = EROL.ROL_ID
                    WHERE CONT.ESTADO = :estado
                    AND PUN.ID_PUNTO = :idPunto";

            $objRsm->addScalarResult('ID_PERSONA_ROL','idPersonaRol','integer');
            $objRsm->addScalarResult('ID_PERSONA','idPersona','integer');
            $objRsm->addScalarResult('NOMBRES','nombres','string');
            $objRsm->addScalarResult('APELLIDOS','apellidos','string');
            $objRsm->addScalarResult('IDENTIFICACION_CLIENTE','identificacion','string');
            $objRsm->addScalarResult('DESCRIPCION_ROL','rol','string');

            $objQuery->setParameter('estado','Activo'); 
            $objQuery->setParameter('idPunto',$arrayParametros['intIdPunto']); 
            $objQuery->setSQL($strSql);  

            $arrayResultado = $objQuery->getResult();                                                                     
        }
        catch(\Exception $e)
        {
           error_log($e->getMessage());
        }
        return array_unique($arrayResultado, SORT_REGULAR);  
    }
}