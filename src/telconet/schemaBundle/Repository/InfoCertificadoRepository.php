<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use telconet\schemaBundle\DependencyInjection\BaseRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoCertificadoRepository extends BaseRepository
{
    /**
     * Documentación para el método 'findCertificado'.
     * 
     * Método que obtiene certificado acorde a los filtros especifícados
     * 
     * @param Array $arrayParametros [strCodEmpresa, strNumCedula, strEstado]
     * @return $arrayResultado['total']     Integer Cantitad de registros obtenidos.
    *                         ['registros'] Array   Listado de Contratos.
    *                         ['error']     String  Mensaje de error.
     * 
     * CostoQuery: 3
     * 
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 15-03-2019
     */
    
    public function findCertificado($arrayParametros)
    {
        try
        {
            $objRsm           = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
            $strSqlWhere      = "";
            if (isset($arrayParametros["strCodEmpresa"])  && !is_null($arrayParametros["strCodEmpresa"]))
            {
                $strSqlWhere .= " AND EMP.REFERENCIA_EMPRESA = :strCodEmpresa";
                $objNtvQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
            }
            if (isset($arrayParametros["strNumCedula"]) && !is_null($arrayParametros["strNumCedula"]))
            {
                $strSqlWhere .= " AND CER.NUM_CEDULA = :strNumCedula";
                $objNtvQuery->setParameter('strNumCedula', $arrayParametros['strNumCedula']);
            }
            if (isset($arrayParametros["strEstado"]) && !is_null($arrayParametros["strEstado"]))
            {
                $strSqlWhere .= " AND CER.ESTADO = :strEstado";
                $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);
            }
 
            $strSqlDatos      = " SELECT CER.ID_CERTIFICADO, CER.EMPRESA_ID, CER.SERIAL_NUMBER, CER.EMAIL, CER.NUM_CEDULA, CER.NOMBRES, 
                                         CER.PRIMER_APELLIDO, CER.SEGUNDO_APELLIDO, CER.DIRECCION, CER.TELEFONO, CER.CIUDAD, CER.PAIS, 
                                         CER.PROVINCIA, CER.NUM_FACTURA, CER.NUM_SERIE_TOKEN, CER.PASSWORD, CER.ENTERPRISE, CER.PERSONA_NATURAL, 
                                         CER.NUM_DIAS_VIGENCIA, CER.GRUPOS_PERTENENCIA, CER.RESPUESTA, CER.ESTADO, CER.USR_CREACION, 
                                         CER.FE_CREACION, CER.RECUPERADO, CER.DOCUMENTADO, CER.RUBRICA, CER.FECHA_CREACION, 
                                         CER.USUARIO_CREACION, EMP.NOMBRE, EMP.RAZON_SOCIAL, EMP.RUC, EMP.REFERENCIA_EMPRESA, 
                                         TO_CHAR(CER.FE_CREACION, 'DDMMYYYY') FE_RUTA";

            $strSqlFrom       = ' FROM DB_FIRMAELECT.INFO_CERTIFICADO CER
                                       LEFT JOIN DB_FIRMAELECT.ADM_EMPRESA EMP
                                       ON CER.EMPRESA_ID = EMP.ID_EMPRESA
                                  WHERE 1 = 1 ';

            $objRsm->addScalarResult('ID_CERTIFICADO'       , 'id'                , 'integer');
            $objRsm->addScalarResult('EMPRESA_ID'           , 'empresaId'         , 'string');
            $objRsm->addScalarResult('SERIAL_NUMBER'        , 'serialNumber'      , 'string');
            $objRsm->addScalarResult('EMAIL'                , 'email'             , 'string');
            $objRsm->addScalarResult('NUM_CEDULA'           , 'numCedula'         , 'string');
            $objRsm->addScalarResult('NOMBRES'              , 'nombres'           , 'string');
            $objRsm->addScalarResult('PRIMER_APELLIDO'      , 'primerApellido'    , 'string');
            $objRsm->addScalarResult('SEGUNDO_APELLIDO'     , 'segundoApellido'   , 'string');
            $objRsm->addScalarResult('DIRECCION'            , 'direccion'         , 'string');
            $objRsm->addScalarResult('TELEFONO'             , 'telefono'          , 'string');
            $objRsm->addScalarResult('CIUDAD'               , 'ciudad'            , 'string');
            $objRsm->addScalarResult('PAIS'                 , 'pais'              , 'string');
            $objRsm->addScalarResult('PROVINCIA'            , 'provincia'         , 'string');
            $objRsm->addScalarResult('NUM_FACTURA'          , 'numFactura'        , 'string');
            $objRsm->addScalarResult('NUM_SERIE_TOKEN'      , 'numSerieToken'     , 'string');
            $objRsm->addScalarResult('PASSWORD'             , 'password'          , 'string');
            $objRsm->addScalarResult('ENTERPRISE'           , 'enterprise'        , 'string');
            $objRsm->addScalarResult('PERSONA_NATURAL'      , 'personaNatural'    , 'string');
            $objRsm->addScalarResult('NUM_DIAS_VIGENCIA'    , 'numDiasVigencia'   , 'string');
            $objRsm->addScalarResult('GRUPOS_PERTENENCIA'   , 'gruposPertenencia' , 'string');
            $objRsm->addScalarResult('RESPUESTA'            , 'respuesta'         , 'string');
            $objRsm->addScalarResult('ESTADO'               , 'estado'            , 'string');
            $objRsm->addScalarResult('USR_CREACION'         , 'usrCreacion'       , 'string');
            $objRsm->addScalarResult('FE_CREACION'          , 'feCreacion'        , 'string');
            $objRsm->addScalarResult('RECUPERADO'           , 'recuperado'        , 'string');
            $objRsm->addScalarResult('DOCUMENTADO'          , 'documentado'       , 'string');
            $objRsm->addScalarResult('RUBRICA'              , 'rubrica'           , 'string');
            $objRsm->addScalarResult('FECHA_CREACION'       , 'fechaCreacion'     , 'string');
            $objRsm->addScalarResult('USUARIO_CREACION'     , 'usuarioCreacion'   , 'string');
            $objRsm->addScalarResult('NOMBRE'               , 'nombre'            , 'string');
            $objRsm->addScalarResult('RAZON_SOCIAL'         , 'razonSocial'       , 'string');
            $objRsm->addScalarResult('RUC'                  , 'ruc'               , 'string');
            $objRsm->addScalarResult('REFERENCIA_EMPERSA'   , 'referenciaEmpresa' , 'string');
            $objRsm->addScalarResult('FE_RUTA'              , 'feRuta'            , 'string');

            $strSqlDatos    .= $strSqlFrom;
            $strSqlDatos    .= $strSqlWhere;

            $objNtvQuery->setSQL($strSqlDatos);
            $arrayDatos = $objNtvQuery->getResult();

        } 
        catch (Exception $ex)
        {
            $arrayDatos = null;
        }
        return $arrayDatos;

    }

    /**
     *
     * Método para obtener el máximo contrato que tiene una persona sin 
     * importar el estado metodo temporal para la regularizacion de contratos
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0
     * @since 11-07-2020
     *
     * @param array $arrayParams[strIdentificacion]
     * @return array $arrayContrato
     */
    public function findMaxContratoPorPersonaSinEstado($arrayParams)
    {        
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsm);

        $strSelect = "SELECT MAX(IC.ID_CONTRATO) AS ID_CONTRATO";
        $strFrom   = "  FROM DB_GENERAL.INFO_PERSONA IP, DB_GENERAL.INFO_PERSONA_EMPRESA_ROL IPER, DB_COMERCIAL.INFO_CONTRATO IC  ";        
        $strWhere  = " WHERE IP.IDENTIFICACION_CLIENTE = :strIdentificacion
                        AND IP.ID_PERSONA = IPER.PERSONA_ID 
                        AND IPER.ID_PERSONA_ROL = IC.PERSONA_EMPRESA_ROL_ID ";
        
        $objRsm->addScalarResult('ID_CONTRATO'  , 'intIdContrato'   , 'integer');
        
        $objQuery->setParameter('strIdentificacion',    $arrayParams['strIdentificacion']);

        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery->setSQL($strSql);
        $arrayContrato = $objQuery->getArrayResult();

        return $arrayContrato;
    } 

}
