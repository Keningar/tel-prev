<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;

class AdmiParametroDetRepository extends EntityRepository
{
    /**
     * getEstadosServCambioCiclo, 
     * Método encargado de obtener los estados de los servicios definidos para filtro.
     * registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'ESTADOS_SERVICIOS_CAMBIO_CICLO'
     *  
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20-09-2017
     * Costo: 2
     * @param array arrayParametros [
     *                               'strParamPadre'   => Recibe el nombre del Parametro Padre
     *                               'strModulo'       => Recibe el nombre del modulo
     *                               'strIdEmpresa'    => Recibe el Id de la empresa en sesion
     *                              ]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna un objeto con el resultado de la consulta
     */
    public function getEstadosServCambioCiclo($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        try
        {
            $objQueryCount = $this->_em->createQuery();
            
            $strQueryCount = "SELECT COUNT(iju) ";
            
            $objQuery = $this->_em->createQuery();
            
            $strQuery = "  SELECT PD.id AS intIdObj,    "
                       ."  PD.valor1    AS strDescripcionObj ";
            $strFromQuery = "  FROM "
                               . "schemaBundle:AdmiParametroDet PD, "
                               . "schemaBundle:AdmiParametroCab PC "                               
                               . "WHERE "
                               . "PC.id = PD.parametroId "
                               . "AND PC.nombreParametro = :strParamPadre "
                               . "AND PC.modulo =:strModulo "
                               . "AND PC.estado = :strEstado "                               
                               . "AND PD.estado = :strEstado "
                               . "AND PD.empresaCod =:strIdEmpresa ";                               
                                
            $objQuery->setParameter('strParamPadre' , $arrayParametros['strParamPadre']);
            $objQuery->setParameter('strModulo' , $arrayParametros['strModulo']);
            $objQuery->setParameter('strEstado', 'Activo');
            $objQuery->setParameter('strIdEmpresa', $arrayParametros['strIdEmpresa']);
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
            $objReturnResponse->setStrMessageStatus('Existio un error en getEstadosServCambioCiclo - ' . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
        }
        return $objReturnResponse;
    }     

    /**
     * getEmpresaEquivalente
     *
     * Metodo creado para  obtener el prefijo de la empresa según la empresa
     * y el tipo de medio.
     *
     * @param integer $idServicio
     * @param integer $prefijoEmpresa
     *
     * @return array  $result (prefijo y el id empresa)
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-10-2014
     */
    public function getEmpresaEquivalente($idServicio, $prefijoEmpresa)
    {
        $result = '';
        $tipoMedio = '';
        $parametros='';

        if($idServicio)
        {
            $dql = "SELECT TM.codigoTipoMedio
                    FROM schemaBundle:InfoServicio SE,
                    schemaBundle:InfoServicioTecnico ST,
                    schemaBundle:AdmiTipoMedio TM
                    WHERE SE.id = ST.servicioId
                    AND TM.id = ST.ultimaMillaId
                    AND SE.id = :idServicio";
            $query = $this->_em->createQuery($dql);
            $query->setParameter("idServicio", $idServicio);
            $arrTipoMedio = $query->getOneOrNullResult();
            $tipoMedio = $arrTipoMedio['codigoTipoMedio'];
        }

        if(!empty($tipoMedio) && trim($prefijoEmpresa) != '')
        {
            $parametros = $this->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresa, $tipoMedio, "", "", "", null);
            if(!empty($parametros))
            {
                if($parametros['valor3'] != '')
                {
                    $dqlPrefijo = "SELECT EG.prefijo, EG.id 
                                   FROM schemaBundle:InfoEmpresaGrupo EG
                                   WHERE EG.prefijo = :prefijo
                                   AND EG.estado = :estado";

                    $query = $this->_em->createQuery($dqlPrefijo);
                    $query->setParameter("prefijo", $parametros['valor3']);
                    $query->setParameter("estado", "Activo");
                    $result = $query->getOneOrNullResult();
                }
            }
        }
        
        return $result;
    }
     
    /**
     * get
     *
     * Metodo creado para obtener los parametros segun los filtros enviados.
     *
     * @param string $nombreParametro
     * @param string $modulo
     * @param string $proceso
     * @param string $descripcion
     * @param string $valor1
     * @param string $valor2
     * @param string $valor3
     * @param string $valor4
     * @param string $valor5
     * @param string $valor6
     * @param string $valor7
     * @param string $empresaCod
     *
     * @return array  $datos 
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-10-2014
     * 
     * @author Alejandro Dominguez Vargas<adominguez@telconet.ec>
     * @version 1.1 21-12-2015
     * @since 1.0
     * Se agregan los filtros por $valor5 y $empresaCod
     * 
     * @author Alejandro Dominguez Vargas<adominguez@telconet.ec>
     * @version 1.2 08-03-2016
     * @since 1.1 Se agregan los perfiles
     *
     * @author Richard Cabrera<rcabrera@telconet.ec>
     * @version 1.3 01-04-2021 - Se agregan los parametros valor6 y valor7
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 1.4 07-06-2023 - Se agregan los parametros valor8 y valor9
     *
     * Se agrega el campo "$strOrderBy = null" para el ordenamiento del listado de ser requerido.
     */
    public function get($nombreParametro, $modulo, $proceso, $descripcion, $valor1, $valor2, $valor3, $valor4, $valor5="", $empresaCod="", 
                        $strOrderBy = null,$strValor6 = null, $strValor7 = null, $strValor8 = null, $strValor9 = null)
    {
        $query = $this->getDql($nombreParametro, $modulo, $proceso, $descripcion, $valor1, $valor2, $valor3, $valor4, $valor5, $empresaCod, 
                               $strOrderBy,$strValor6, $strValor7, $strValor8, $strValor9);
        return $query->getResult();
    }

    public function getTodasLasEmpresas($strNombreParametro, $strModulo, $strProceso, 
                $strDescripcion, $strValor1, $strValor2, $strValor3, $strValor4, $strValor5="", 
                        $strOrderBy = null,$strValor6 = null, $strValor7 = null)
    {
        $objQuery = $this->getDqlTodasLasEmpresas($strNombreParametro, $strModulo, $strProceso, 
        $strDescripcion, $strValor1, $strValor2, $strValor3, $strValor4, $strValor5, 
        $empresaCod, $strOrderBy, $strValor6, $strValor7);
        return $objQuery->getResult();
    }

    

    
    /**
     * getOne
     *
     * Metodo creado para obtener los parametros segun los filtros enviados, solo retorna un registro
     *
     * @param string $nombreParametro
     * @param string $modulo
     * @param string $proceso
     * @param string $descripcion
     * @param string $valor1
     * @param string $valor2
     * @param string $valor3
     * @param string $valor4
     * @param string $valor5
     * @param string $valor6
     * @param string $valor7
     * @param string $empresaCod
     *
     * @return array con $datos 
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-10-2014
     * 
     * @author Alejandro Dominguez Vargas<adominguez@telconet.ec>
     * @version 1.1 21-12-2015
     * @since 1.0
     * Se agrega la opción de filtrado por empresa
     * 
     * @author Alejandro Dominguez Vargas<adominguez@telconet.ec>
     * @version 1.2 08-03-2016
     * @since 1.1
     *
     * @author Richard Cabrera<rcabrera@telconet.ec>
     * @version 1.3 01-04-2021 - Se agregan los parametros valor6 y valor7
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 1.4 07-06-2023 - Se agregan los parametros valor8 y valor9
     *
     * Se agrega el campo "$strOrderBy = null" para el ordenamiento del listado de ser requerido.
     */
    public function getOne($nombreParametro, $modulo, $proceso, $descripcion, $valor1, $valor2, $valor3, $valor4, $valor5="", $empresaCod="", 
                           $strOrderBy = null,$strValor6 = null, $strValor7 = null, $strValor8 = null, $strValor9 = null)
    {
        $query = $this->getDql($nombreParametro, $modulo, $proceso, $descripcion, $valor1, $valor2, $valor3, $valor4, $valor5, $empresaCod, 
                               $strOrderBy,$strValor6, $strValor7, $strValor8, $strValor9);
        return $query->getOneOrNullResult();
    }
     
    /**
     * getDql
     *
     * Metodo creado para obtener los parametros segun los filtros enviados, retorna uno o varios registro dependiendo
     * de la variable unico registro. 
     *
     * @param integer $nombreParametro
     * @param integer $modulo
     * @param integer $proceso
     * @param integer $descripcion
     * @param integer $valor1
     * @param integer $valor2
     * @param integer $valor3
     * @param integer $valor4
     * @param integer $valor5
     * @param integer $empresaCod
     *
     * @return array con $datos 
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-10-2014
      * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-02.2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 14-09-2015 - Se modifica para que el Valor2 de la tabla permita realizar una consulta por
     *                           'menor a', para que se puedan mostrar los cargos superiores al cargo actual
     *
     * @author Alejandro Dominguez Vargas<adominguez@telconet.ec>
     * @version 1.3 21-12-2015
     * Se agregan los filtros por $valor5 y $empresaCod
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.4 2016-05-28 Se incluye el ORDER BY PD.descripcion
     * 
     * @author Alejandro Dominguez Vargas<adominguez@telconet.ec>
     * @version 1.5 10-06-2016
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.6
     * @since 21-03-2019
     * Se agrega dentro del select devolver un valor adicional ( valor6 y valor7 )
     *
     * @author Richard Cabrera<rcabrera@telconet.ec>
     * @version 1.3 01-04-2021 - Se agregan los parametros valor6 y valor7
     *
     * Se agrega el campo "$strOrderBy = null" para el ordenamiento del listado de ser requerido, por default ordena por descripción
     * Se agrega PD.valor5 a los campos de resultado de la respuesta.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.7 16-06-2021
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 1.8 07-06-2023 - Se agregan los filtros para los parametros valor8 y valor9
     * 
     * Se agrega el campo observacion el cual nos permitirá obtener el valor de la observación de la tabla ADMI_PARAMETRO_DET.
     * 
     */
    public function getDql($nombreParametro, $modulo, $proceso, $descripcion, $valor1, $valor2, $valor3, $valor4, $valor5, $empresaCod, 
                           $strOrderBy = null,$strValor6 = null, $strValor7 = null, $strValor8 = null, $strValor9 = null)
    {
        if($nombreParametro == "")
        {
            return FALSE;
        }
        
        $query = $this->_em->createQuery();

        $strDql = "SELECT PD.id, PD.descripcion, PD.valor1, PD.valor2, PD.valor3, PD.valor4, PD.valor5, PD.estado, PD.valor6, PD.valor7
                   , PD.observacion 
                  FROM schemaBundle:AdmiParametroDet PD,
                       schemaBundle:AdmiParametroCab PC
                  WHERE PC.id = PD.parametroId
                  AND PC.nombreParametro = :nombreParametro
                  AND PC.estado = :estado
                  AND PD.estado = :estado ";

        $query->setParameter("nombreParametro", $nombreParametro);
        $query->setParameter("estado", "Activo");

        if($modulo != "")
        {
            $strDql.= " AND PC.modulo = :modulo";
            $query->setParameter("modulo", $modulo);
        }
        if($proceso != "")
        {
            $strDql.= " AND PC.proceso = :proceso";
            $query->setParameter("proceso", $proceso);
        }
        if($descripcion != "")
        {
            $strDql.= " AND PD.descripcion = :descripcion";
            $query->setParameter("descripcion", $descripcion);
        }
        if($valor1 != "")
        {
            $strDql.= " AND PD.valor1 = :valor1";
            $query->setParameter("valor1", $valor1);
        }
        if($valor2 != "")
        {
            $arrayValor2 = explode(':', $valor2);
            
            if( $arrayValor2[0] == 'CargosMayores' )
            {
                $strDql.= " AND PD.valor2 < :valor2";
                $query->setParameter("valor2", $arrayValor2[1]);
            }
            else
            {
                $strDql.= " AND PD.valor2 = :valor2";
                $query->setParameter("valor2", $valor2);
            }
        }
        if($valor3 != "")
        {
            $strDql.= " AND PD.valor3 = :valor3";
            $query->setParameter("valor3", $valor3);
        }
        if($valor4 != "")
        {
            $strDql.= " AND PD.valor4 = :valor4";
            $query->setParameter("valor4", $valor4);
        }
        if($valor5 != "")
        {
            $strDql.= " AND PD.valor5 = :valor5";
            $query->setParameter("valor5", $valor5);
        }
        if($strValor6 != "")
        {
            $strDql.= " AND PD.valor6 = :valor6";
            $query->setParameter("valor6", $strValor6);
        }
        if($strValor7 != "")
        {
            $strDql.= " AND PD.valor7 = :valor7";
            $query->setParameter("valor7", $strValor7);
        }
        if($strValor8 != "")
        {
            $strDql.= " AND PD.valor8 = :valor8";
            $query->setParameter("valor8", $strValor8);
        }
        if($strValor9 != "")
        {
            $strDql.= " AND PD.valor9 = :valor9";
            $query->setParameter("valor9", $strValor9);
        }
        if($empresaCod)
        {
            $strDql .= " AND PD.empresaCod = :empresaCod";
            $query->setParameter("empresaCod", $empresaCod);
        }
        else
        {
            // Si no se recibe el parámetro empresaCod, igualar a NULL este filtro retornará los registros antigüos que no disponen de la empresa.
            $strDql .= " AND PD.empresaCod IS NULL";
        }
        
        if( $strOrderBy == 'valor1' || $strOrderBy == 'valor2' || $strOrderBy == 'valor3' || $strOrderBy == 'valor4' || $strOrderBy == 'valor5' ||
            $strOrderBy == 'valor6' || $strOrderBy == 'valor7')
        {
            $strDql .= " ORDER BY PD.$strOrderBy";
        }
        else
        {
            $strDql .= " ORDER BY PD.descripcion";
        }
        
        $query->setDQL($strDql);
        
        return $query;
    }

    public function getDqlTodasLasEmpresas($strNombreParametro, $strModulo, $strProceso, 
                $strDescripcion, $strValor1, $strValor2, $strValor3, $strValor4, $strValor5, 
                           $strOrderBy = null,$strValor6 = null, $strValor7 = null)
    {
        if($strNombreParametro == "")
        {
            return false;
        }
        
        $objQuery = $this->_em->createQuery();

        $strDql = "SELECT PD.id, PD.descripcion, PD.valor1, PD.valor2, PD.valor3, PD.valor4, PD.valor5, PD.estado, PD.valor6, PD.valor7
                   , PD.observacion 
                  FROM schemaBundle:AdmiParametroDet PD,
                       schemaBundle:AdmiParametroCab PC
                  WHERE PC.id = PD.parametroId
                  AND PC.nombreParametro = :nombreParametro
                  AND PC.estado = :estado
                  AND PD.estado = :estado ";

        $objQuery->setParameter("nombreParametro", $strNombreParametro);
        $objQuery->setParameter("estado", "Activo");

        if($strModulo != "")
        {
            $strDql.= " AND PC.modulo = :modulo";
            $objQuery->setParameter("modulo", $strModulo);
        }
        if($strProceso != "")
        {
            $strDql.= " AND PC.proceso = :proceso";
            $objQuery->setParameter("proceso", $strProceso);
        }
        if($strDescripcion != "")
        {
            $strDql.= " AND UPPER(PD.descripcion) LIKE :descripcion";
            $objQuery->setParameter("descripcion", "%".mb_strtoupper($strDescripcion, 'utf-8')."%");
        }
        if($strValor1 != "")
        {
            $strDql.= " AND PD.valor1 = :valor1";
            $objQuery->setParameter("valor1", $strValor1);
        }
        if($strValor2 != "")
        {
            $arrayValor2 = explode(':', $strValor2);
            
            if( $arrayValor2[0] == 'CargosMayores' )
            {
                $strDql.= " AND PD.valor2 < :valor2";
                $objQuery->setParameter("valor2", $arrayValor2[1]);
            }
            else
            {
                $strDql.= " AND PD.valor2 = :valor2";
                $objQuery->setParameter("valor2", $strValor2);
            }
        }
        if($strValor3 != "")
        {
            $strDql.= " AND PD.valor3 = :valor3";
            $objQuery->setParameter("valor3", $strValor3);
        }
        if($strValor4 != "")
        {
            $strDql.= " AND PD.valor4 = :valor4";
            $objQuery->setParameter("valor4", $strValor4);
        }
        if($strValor5 != "")
        {
            $strDql.= " AND PD.valor5 = :valor5";
            $objQuery->setParameter("valor5", $strValor5);
        }
        if($strValor6 != "")
        {
            $strDql.= " AND PD.valor6 = :valor6";
            $objQuery->setParameter("valor6", $strValor6);
        }
        if($strValor7 != "")
        {
            $strDql.= " AND PD.valor7 = :valor7";
            $objQuery->setParameter("valor7", $strValor7);
        }
        
        if( $strOrderBy == 'valor1' || $strOrderBy == 'valor2' || $strOrderBy == 'valor3' || $strOrderBy == 'valor4' || $strOrderBy == 'valor5' ||
            $strOrderBy == 'valor6' || $strOrderBy == 'valor7')
        {
            $strDql .= " ORDER BY PD.$strOrderBy";
        }
        else
        {
            $strDql .= " ORDER BY PD.descripcion";
        }
        
        $objQuery->setDQL($strDql);
        
        return $objQuery;
    }
    
    /**
     * findParametrosDet, Crea la sentencia DQL según los parámetros enviados de la entidad AdmiParametroDet
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 09-09-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 30-03-2016
     * Se agrega a los filtros de búsqueda el Código de la Empresa
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 28-11-2016 - Se agrega a los filtros de búsqueda por el nombre del parámetro de la cabecera. Adicional que el método retorne los
     *                           valores 'strValor5' y 'strEmpresaCod'
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 29-10-2018  Se agrega consulta de campo valor6
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 14-05-2020  Se agrega consulta de campo valor7
     *  
     * @param   array $arrayParametros['strEstado'      => Estado del parámetro
     *                                 'strValor1'      => valor1 de la entidad AdmiParametroDet,
     *                                 'strValor2'      => valor2 de la entidad AdmiParametroDet,
     *                                 'strValor3'      => valor3 de la entidad AdmiParametroDet,
     *                                 'strValor4'      => valor4 de la entidad AdmiParametroDet,
     *                                 'strUsrCreacion' => Usuario que creó el parámetro,
     *                                 'strEmpresaCod'  => Código de la Empresa]
     * 
     * @return array $arrayResult['strMensajeError' => 'Mensaje de error en caso de existir',
     *                            'arrayResultado'  => 'Array con la información devuelta por la entidad AdmiParametroDet según los filtros
     *                                                  enviados'
     *                                                  array[  apd.id          => intIdParametroDet, 
     *                                                          apd.descripcion => strDescripcionDet,
     *                                                          apd.valor1      => strValor1,
     *                                                          apd.valor2      => strValor2,
     *                                                          apd.valor3      => strValor3,
     *                                                          apd.valor4      => strValor4,
     *                                                          apd.valor5      => strValor5,
     *                                                          apd.valor6      => strValor6,
     *                                                          apd.valor7      => strValor7,
     *                                                          apd.empresaCod  => strEmpresaCod,
     *                                                          apd.estado      => strEstado,
     *                                                          apd.usrCreacion => strUsrCreacion,
     *                                                          apd.feCreacion  => strFeCreacion,
     *                                                          apd.ipCreacion  => stripCreacion,
     *                                                          apd.usrUltMod   => strUsrUltMod,
     *                                                          apd.feUltMod    => strFeUltMod,
     *                                                          apd.ipUltMod    => strIpUltMod  ],
     *                            'intTotal'        => 'Cantidad de registros retornados por el query']
     */
    public function findParametrosDet($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(apd.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT apd.id intIdParametroDet, "
                                . "apd.descripcion strDescripcionDet, "
                                . "apd.valor1 strValor1, "
                                . "apd.valor2 strValor2, "
                                . "apd.valor3 strValor3, "
                                . "apd.valor4 strValor4, "
                                . "apd.valor5 strValor5, "
                                . "apd.valor6 strValor6, "
                                . "apd.valor7 strValor7, "
                                . "apd.empresaCod strEmpresaCod, "
                                . "apd.estado strEstado, "
                                . "apd.usrCreacion strUsrCreacion, "
                                . "apd.feCreacion strFeCreacion, "
                                . "apd.ipCreacion stripCreacion, "
                                . "apd.usrUltMod strUsrUltMod, "
                                . "apd.feUltMod strFeUltMod, "
                                . "apd.ipUltMod strIpUltMod ";
                                
            $strFromQuery = "FROM schemaBundle:AdmiParametroDet apd ";
            $strWhere     = "WHERE 1 = 1 ";
            $strOrderBy   = " ORDER BY apd.id desc ";
            
            
            //Pregunta si $arrayParametros['strNombreParametroCab'] existe y si es diferente de vacío para agregar la condición.
            if( isset($arrayParametros['strNombreParametroCab']) && !empty($arrayParametros['strNombreParametroCab']) )
            {
                $strFromQuery .= ", schemaBundle:AdmiParametroCab apc ";
                $strWhere     .= "AND apd.parametroId = apc.id ".
                                 "AND apc.nombreParametro = :strNombreParametroCab ";
                
                $objQuery->setParameter('strNombreParametroCab',      $arrayParametros['strNombreParametroCab']);
                $objQueryCount->setParameter('strNombreParametroCab', $arrayParametros['strNombreParametroCab']);
            }
            
            //Pregunta si $arrayParametros['strEstado'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strEstado']))
            {
                $strWhere .= " AND apd.estado IN (:strEstado)";
                $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
                $objQueryCount->setParameter('strEstado', $arrayParametros['strEstado']);
            }

            //Pregunta si $arrayParametros['intIdParametroCab'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['intIdParametroCab']))
            {
                $strWhere .= " AND apd.parametroId = :intIdParametroCab";
                $objQuery->setParameter('intIdParametroCab', $arrayParametros['intIdParametroCab']);
                $objQueryCount->setParameter('intIdParametroCab', $arrayParametros['intIdParametroCab']);
            }
            //Pregunta si $arrayParametros['strDescripcionDet'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strDescripcionDet']))
            {
                $strWhere .= " AND apd.descripcion LIKE :strDescripcionDet";
                $objQuery->setParameter('strDescripcionDet', '%'.$arrayParametros['strDescripcionDet'].'%');
                $objQueryCount->setParameter('strDescripcionDet', '%'.$arrayParametros['strDescripcionDet'].'%');
            }
            //Pregunta si $arrayParametros['strValor1'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strValor1']))
            {
                $strWhere .= " AND apd.valor1 LIKE :strValor1";
                $objQuery->setParameter('strValor1', '%'.$arrayParametros['strValor1'].'%');
                $objQueryCount->setParameter('strValor1', '%'.$arrayParametros['strValor1'].'%');
            }
            //Pregunta si $arrayParametros['strValor2'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strValor2']))
            {
                $strWhere .= " AND apd.valor2 LIKE :strValor2";
                $objQuery->setParameter('strValor2', '%'.$arrayParametros['strValor2'].'%');
                $objQueryCount->setParameter('strValor2', '%'.$arrayParametros['strValor2'].'%');
            }
            //Pregunta si $arrayParametros['strValor3'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strValor3']))
            {
                $strWhere .= " AND apd.valor3 LIKE :strValor3";
                $objQuery->setParameter('strValor3', '%'.$arrayParametros['strValor3'].'%');
                $objQueryCount->setParameter('strValor3', '%'.$arrayParametros['strValor3'].'%');
            }
            //Pregunta si $arrayParametros['strValor4'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strValor4']))
            {
                $strWhere .= " AND apd.valor4 LIKE :strValor4";
                $objQuery->setParameter('strValor4', '%'.$arrayParametros['strValor4'].'%');
                $objQueryCount->setParameter('strValor4', '%'.$arrayParametros['strValor4'].'%');
            }
            //Pregunta si $arrayParametros['strValor4'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strUsrCreacion']))
            {
                $strWhere .= " AND apd.usrCreacion LIKE :strUsrCreacion";
                $objQuery->setParameter('strUsrCreacion', '%'.$arrayParametros['strUsrCreacion'].'%');
                $objQueryCount->setParameter('strUsrCreacion', '%'.$arrayParametros['strUsrCreacion'].'%');
            }
            
            //Pregunta si $arrayParametros['strEmpresaCod'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strEmpresaCod']))
            {
                // Una vez que todos los parámetros Detalle dispongan del código de empresa se deberá remover la cláusula "apd.empresaCod is null OR"
                // que devuelve todos los registros en caso de que los detalles del parámetro no dispongan del código de la empresa
                $strWhere .= " AND (apd.empresaCod is null OR apd.empresaCod = :strEmpresaCod)";
                $objQuery->setParameter('strEmpresaCod',      $arrayParametros['strEmpresaCod']);
                $objQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
            }

            $strSql = $strQuery . $strFromQuery . $strWhere . $strOrderBy;
            $objQuery->setDQL($strSql);
            
            $arrayResult['arrayResultado'] = $objQuery->setFirstResult($arrayParametros['intStart'])
                                                      ->setMaxResults($arrayParametros['intLimit'])->getResult();
            //Pregunta si $arrayResult['arrayResultado'] es diferente de vacio para realizar el count
            if(!empty($arrayResult['arrayResultado']))
            {
                $strQueryCount = $strQueryCount . $strFromQuery . $strWhere . $strOrderBy;
                $objQueryCount->setDQL($strQueryCount);
                $arrayResult['intTotal'] = $objQueryCount->getSingleScalarResult();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en findParametrosCab - ' . $ex->getMessage();
        }
        return $arrayResult;
    }//findParametrosDet
    
    
    /**
     * getParametrosByCriterios
     *
     * Método que retornará los parámetros dependiendo de los criterios ingresados por el usuario                                
     *
     * @param array $arrayParametros [ 'estado'                       => 'Estado del detalle del parámetro', 
     *                                 'noCargosAsignadosACuadrilla'  => 'Array con los cargos no asignados a la cuadrilla', 
     *                                 'valor3'                       => 'Valor3 del detalle del parámetro', 
     *                                 'valor4'                       => 'Valor4 del detalle del parámetro', 
     *                                 'parametroId'                  => 'Id del parámetro cabecera', 
     *                                 'cuadrilla'                    => 'Id de la cuadrilla a buscar', 
     *                                 'cargosCuadrilla'              => 'Cargos de la cuadrilla a buscar',
     *                                 'descripcion'                  => 'Descripción del detalle del parámetro', 
     *                                 'valor1'                       => 'Valor1 del detalle del parámetro', 
     *                                 'strNombreParametroCab'        => 'Nombre de la cabecera del parámetro', 
     *                                 'strEmpresaCod'                => 'Código de la empresa que va a consultar los parámetros' ]
     * 
     * @return array $arrayResultados ['registros', 'total']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-11-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-01-2016 - Se agrega que se pueda buscar por descripcion en los parámetros
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 13-04-2016 - Se agrega que se pueda buscar por valor1 en los parámetros
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 24-08-2016 - Se agrega que se pueda buscar por valor2 en los parámetros
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 18-01-2017 - Se modifica la función para verificar si el contenido del parámetro $arrayParametros['valor1'] es un String o un
     *                           array, para obtener la información correspondiente dependiendo del caso. Adicional se añade validación por nombre
     *                           de la cabecera con el parámetro $arrayParametros['strNombreParametroCab'].
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 22-02-2017 - Se modifica la función para consultar los detalles de los parámetros por el campo 'EMPRESA_COD'. Para ello se usa
     *                           el parámetro 'strEmpresaCod'. Adicional en el SELECT se añade que retorne los campos 'apd.valor3', 'apd.valor4', 
     *                           'apd.valor5' y 'apd.empresaCod'
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.6 13-02-2023 - Se agregan campos valor6 y valor7 a select
     * 
     * Costo del query['valor1', 'strEmpresaCod', 'strNombreParametroCab', 'estado']: 4
     */
    public function getParametrosByCriterios( $arrayParametros )
    {
        $arrayResultados = array();
        
        $query      = $this->_em->createQuery();	
        $queryCount = $this->_em->createQuery();

        $strSelect= "SELECT apd.id, apd.descripcion, apd.valor2, apd.valor1, apd.valor3, apd.valor4,apd.valor5,apd.valor6,apd.valor7,apd.empresaCod ";
        $strSelectCount = "SELECT COUNT(apd.id) ";
        $strFrom        = "FROM schemaBundle:AdmiParametroDet apd ";
        $strWhere       = "WHERE apd.estado = :estadoActivo ";
        $strOrderBy     = "ORDER BY apd.valor1 ";
        
        
        if( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) )
        {
            $strWhere .= 'AND apd.empresaCod = :strEmpresaCod ';

            $query->setParameter("strEmpresaCod",      $arrayParametros['strEmpresaCod'] );
            $queryCount->setParameter("strEmpresaCod", $arrayParametros['strEmpresaCod'] );
        }
        
        
        if( isset($arrayParametros['descripcion']) )
        {
            if($arrayParametros['descripcion'])
            {
                $strWhere .= 'AND apd.descripcion = :descripcion ';
                
                $query->setParameter("descripcion",      $arrayParametros['descripcion'] );
                $queryCount->setParameter("descripcion", $arrayParametros['descripcion'] );
            }
        }
        
        
        if( !empty($arrayParametros['valor2']) )
        {
            $strWhere .= 'AND apd.valor2 = :valor2 ';

            $query->setParameter("valor2",      $arrayParametros['valor2'] );
            $queryCount->setParameter("valor2", $arrayParametros['valor2'] );
        }
        
        
        //Pregunta si $arrayParametros['strNombreParametroCab'] existe y si es diferente de vacío para agregar la condición.
        if( isset($arrayParametros['strNombreParametroCab']) && !empty($arrayParametros['strNombreParametroCab']) )
        {
            $strFrom  .= ", schemaBundle:AdmiParametroCab apc ";
            $strWhere .= "AND apd.parametroId = apc.id ".
                         "AND apc.nombreParametro = :strNombreParametroCab ";

            $query->setParameter('strNombreParametroCab',      $arrayParametros['strNombreParametroCab']);
            $queryCount->setParameter('strNombreParametroCab', $arrayParametros['strNombreParametroCab']);
        }
        
        
        if( isset($arrayParametros['valor1']) && !empty($arrayParametros['valor1']) )
        {
            if( is_array($arrayParametros['valor1']) )
            {
                $strWhere .= 'AND apd.valor1 IN (:valor1) ';

                $query->setParameter("valor1",      array_values($arrayParametros['valor1']) );
                $queryCount->setParameter("valor1", array_values($arrayParametros['valor1']) );
            }
            else
            {
                $strWhere .= 'AND apd.valor1 = :valor1 ';

                $query->setParameter("valor1",      $arrayParametros['valor1'] );
                $queryCount->setParameter("valor1", $arrayParametros['valor1'] );
            }
        }
        
        
        if( isset($arrayParametros['valor3']) )
        {
            if($arrayParametros['valor3'])
            {
                $strWhere .= 'AND apd.valor3 = :valor3 ';
                
                $query->setParameter("valor3", $arrayParametros['valor3'] );
                $queryCount->setParameter("valor3", $arrayParametros['valor3'] );
            }
        }
        
        
        if( isset($arrayParametros['valor4']) )
        {
            if($arrayParametros['valor4'])
            {
                $strWhere .= 'AND apd.valor4 = :valor4 ';
                
                $query->setParameter("valor4", $arrayParametros['valor4'] );
                $queryCount->setParameter("valor4", $arrayParametros['valor4'] );
            }
        }
        
        
        if( isset($arrayParametros['parametroId']) )
        {
            if($arrayParametros['parametroId'])
            {
                $strWhere .= 'AND apd.parametroId = :parametroId ';
                
                $query->setParameter("parametroId", $arrayParametros['parametroId'] );
                $queryCount->setParameter("parametroId", $arrayParametros['parametroId'] );
            }
        }
        

        if( isset($arrayParametros['noCargosAsignadosACuadrilla']) )
        {
            if($arrayParametros['noCargosAsignadosACuadrilla'] == 'S')
            {
                $strWhere .= 'AND apd.descripcion NOT IN (
                                                            SELECT UPPER(iperc.valor)
                                                            FROM schemaBundle:InfoPersonaEmpresaRolCarac iperc
                                                            WHERE iperc.personaEmpresaRolId IN (
                                                                                                    SELECT iper.id
                                                                                                    FROM schemaBundle:InfoPersonaEmpresaRol iper
                                                                                                    WHERE iper.cuadrillaId = :cuadrilla
                                                                                                )
                                                            AND iperc.estado = :estadoActivo
                                                            AND iperc.valor IN (:cargosCuadrilla)
                                                          ) ';
                
                $query->setParameter("cuadrilla",       $arrayParametros['cuadrilla'] );
                $query->setParameter("cargosCuadrilla", $arrayParametros['cargosCuadrilla'] );
                
                $queryCount->setParameter("cuadrilla",       $arrayParametros['cuadrilla'] );
                $queryCount->setParameter("cargosCuadrilla", $arrayParametros['cargosCuadrilla'] );
            }
        }
        
        if( isset($arrayParametros['cargosNoVisibles']) )
        {
            
            if($arrayParametros['cargosNoVisibles'])
            {
                $strWhere .= 'AND apd.descripcion NOT IN ( :cargosNoVisibles ) ';
                
                $query->setParameter("cargosNoVisibles", array_values($arrayParametros['cargosNoVisibles']) );
                $queryCount->setParameter("cargosNoVisibles", array_values($arrayParametros['cargosNoVisibles']) );
            }
        }

        $strSql      = $strSelect.$strFrom.$strWhere.$strOrderBy;
        $strSqlCount = $strSelectCount.$strFrom.$strWhere;

        $query->setDQL($strSql);
        $queryCount->setDQL($strSqlCount);

        $query->setParameter("estadoActivo", $arrayParametros['estado'] );
        $queryCount->setParameter("estadoActivo", $arrayParametros['estado'] );

        $arrayResultados['registros'] = $query->getResult();
        $arrayResultados['total']     = $queryCount->getSingleScalarResult();
        
        return $arrayResultados;
    }
    
    
    /**
     * getJSONParametrosByCriterios
     *
     * Método que retornará los parámetros dependiendo de los criterios ingresados por el usuario en formato JSON                               
     *
     * @param array $arrayParametros [ 'estado', 'noCargosAsignadosACuadrilla', 'valor3', 'valor4', 'parametroId', 'cuadrilla', 'cargosCuadrilla',
     *                                 'descripcion' ]
     * 
     * @return json $jsonData ['total', 'encontrados']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-01-2016
     */
    public function getJSONParametrosByCriterios( $arrayParametros )
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getParametrosByCriterios($arrayParametros);
        $arrayAdmiParametrosDet = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayAdmiParametrosDet )
        {
            foreach( $arrayAdmiParametrosDet as $arrayParametroDet )
            {
                $arrayEncontrados[] = $arrayParametroDet;
            }//foreach( $arrayAdmiParametrosDet as $objParametroDet )
        }//( $arrayAdmiParametrosDet )

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
    
    
    /**
     * getArrayDetalleParametros
     *
     * Método que retornará los detalles de los parámetros dependiendo de los criterios ingresados por el usuario.                              
     *
     * @param array $arrayParametros [ 'estado'                       => 'Estado del detalle del parámetro', 
     *                                 'noCargosAsignadosACuadrilla'  => 'Array con los cargos no asignados a la cuadrilla', 
     *                                 'valor3'                       => 'Valor3 del detalle del parámetro', 
     *                                 'valor4'                       => 'Valor4 del detalle del parámetro', 
     *                                 'parametroId'                  => 'Id del parámetro cabecera', 
     *                                 'cuadrilla'                    => 'Id de la cuadrilla a buscar', 
     *                                 'cargosCuadrilla'              => 'Cargos de la cuadrilla a buscar',
     *                                 'descripcion'                  => 'Descripción del detalle del parámetro', 
     *                                 'valor1'                       => 'Valor1 del detalle del parámetro', 
     *                                 'strNombreParametroCab'        => 'Nombre de la cabecera del parámetro' ]
     * 
     * @return array $arrayRespuesta ['encontrados', 'total']
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 18-01-2017
     */
    public function getArrayDetalleParametros( $arrayParametros )
    {
        $arrayRespuesta         = array();
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getParametrosByCriterios($arrayParametros);
        $arrayAdmiParametrosDet = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayAdmiParametrosDet )
        {
            foreach( $arrayAdmiParametrosDet as $arrayParametroDet )
            {
                $arrayEncontrados[] = $arrayParametroDet;
            }//foreach( $arrayAdmiParametrosDet as $objParametroDet )
        }//( $arrayAdmiParametrosDet )

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);

        return $arrayRespuesta;
    }
    
    
    /**
     * getResultadoDetallesParametro
     *
     * Método que consulta los valores de los detalles de un determinado parámetro                              
     *
     * @param string $nombreParametro
     * @param string $valor1
     * @param string $valor2
     * 
     * @return array $datos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se modifica la función para que también se obtenga la columna valor1
     * 
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 Se modifica la función para que también se obtenga la columna valor3
     * 
     * 
     */
    public function getResultadoDetallesParametro($nombreParametro, $valor1,$valor2)
    {
        $arrayResultado['registros'] = '';
        $arrayResultado['total']     = 0;
        try
        {
            $query              = $this->_em->createQuery();
            $queryCount         = $this->_em->createQuery();
            $strSelectCount     = "SELECT COUNT(PD.id) ";
            $strSelect          = "SELECT PD.id, PD.valor1, PD.valor2, PD.valor3";
            $strFromAndWhere    = "
                                    FROM schemaBundle:AdmiParametroDet PD,
                                         schemaBundle:AdmiParametroCab PC
                                    WHERE PC.id = PD.parametroId
                                    AND PC.nombreParametro = :nombreParametro
                                    AND PC.estado = :estado
                                    AND PD.estado = :estado 
                                    ";

            if($valor1 != "")
            {
                $strFromAndWhere.= " AND PD.valor1 = :valor1";
                $query->setParameter("valor1", $valor1);
                $queryCount->setParameter("valor1", $valor1);

            }
            
            if($valor2 != "")
            {
                $strFromAndWhere.= " AND PD.valor2 = :valor2";
                $query->setParameter("valor2", $valor2);
                $queryCount->setParameter("valor2", $valor2);

            }
            $strOrderBy= " ORDER BY PD.id ASC ";
            $query->setParameter("nombreParametro", $nombreParametro);
            $query->setParameter("estado", "Activo");

            $queryCount->setParameter("nombreParametro", $nombreParametro);
            $queryCount->setParameter("estado", "Activo");


            $strSql      = $strSelect.$strFromAndWhere.$strOrderBy;
            $strSqlCount = $strSelectCount.$strFromAndWhere;

            $query->setDQL($strSql);
            $queryCount->setDQL($strSqlCount);

            $arrayResultado['registros'] = $query->getResult();
            $arrayResultado['total']     = $queryCount->getSingleScalarResult();
            
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        
        return $arrayResultado;
    }
    /**
     * getJSONDetallesParametro
     *
     * Método que retorna el JSON con los valores de los detalles de un determinado parámetro                              
     *
     * @param string $nombreParametro
     * @param string $valor1
     * @param string $valor2
     * 
     * @return array $datos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     */
    public function getJSONDetallesParametro($nombreParametro, $valor1,$valor2)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getResultadoDetallesParametro($nombreParametro, $valor1,$valor2);
        $arrayAdmiParametrosDet = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayAdmiParametrosDet )
        {
            foreach( $arrayAdmiParametrosDet as $arrayParametroDet )
            {
                $arrayItem  = array();
                $arrayItem["nombre_region"] = $arrayParametroDet["valor2"];
                $arrayItem["value_region"]  = $arrayParametroDet["valor2"];
                $arrayEncontrados[] = $arrayItem;
            }//foreach( $arrayAdmiParametrosDet as $objParametroDet )
        }//( $arrayAdmiParametrosDet )

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);

        return $jsonData;
    }
    
    /**
     * getJSONDetallesParametroGeneral
     *
     * Método que consulta los valores de los detalles con su respectivo id de un determinado parámetro                              
     *
     * @param string $nombreParametro
     * @param string $valor1
     * 
     * @return array $datos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 08-12-2016 Se agrega el VALOR2 al arreglo obtenido
     * 
     */
    public function getJSONDetallesParametroGeneral($nombreParametro, $valor1,$valor2)
    {
        $arrayEncontrados       = array();
        $arrayResultado         = $this->getResultadoDetallesParametro($nombreParametro, $valor1,$valor2);
        $arrayAdmiParametrosDet = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];

        if( $arrayAdmiParametrosDet )
        {
            foreach( $arrayAdmiParametrosDet as $arrayParametroDet )
            {
                $arrayItem  = array();
                $arrayItem["idParametroDet"]    = $arrayParametroDet["id"];
                $arrayItem["valor1"]            = $arrayParametroDet["valor1"];
                $arrayItem["valor2"]            = $arrayParametroDet["valor2"];
                $arrayEncontrados[] = $arrayItem;
            }//foreach( $arrayAdmiParametrosDet as $objParametroDet )
        }//( $arrayAdmiParametrosDet )

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        $jsonData       = json_encode($arrayRespuesta);
        return $jsonData;
    }
    
    /**
     * 
     * Metodo encargado de retornar los vendedores
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.1 13-08-2019 Se corrige el número de mes para retornar el listado de vendedores al momento de crear las metas.
     *
     * Costo 31
     * 
     * @param  string $strTipo
     * @param  string $intIdEmpresa     
     * @return array $arrayVendedor
     */    
    public function getVendedores($strTipo,$intIdEmpresa)
    {
        $arrayVendedor=array();    
        try
        {
            $strSelect = "SELECT    ip.login,
                                    concat(concat(UPPER(ip.apellidos),' '),
                                    UPPER(ip.nombres)) AS vendedor  ";
            $strFrom   = " FROM db_comercial.info_persona_empresa_rol  iper 
                            JOIN db_comercial.info_persona             ip ON ip.id_persona=iper.persona_id   ";
            $strWhere  = " WHERE iper.estado='Activo'
                            AND ip.estado='Activo'
                            AND iper.reporta_persona_empresa_rol_id IN (SELECT iper.id_persona_rol 
                                                                                FROM db_comercial.info_persona_empresa_rol iper 
                                                                                JOIN db_comercial.info_persona             ip ON ip.id_persona=iper.persona_id
                                                                                WHERE iper.estado='Activo'
                                                                                AND ip.estado='Activo'
                                                                                AND ip.login IN (select valor1 
                                                                                                    from DB_GENERAL.ADMI_PARAMETRO_DET apd 
                                                                                                        where apd.DESCRIPCION = 'LISTADO DE GERENTES Y SUBGERENTES' 
                                                                                                        AND apd.estado='Activo' )
                                                                        )
                              ";

            if( $strTipo == 'crear' )
            {
                $arrayMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $strMes= (string)$arrayMeses[date('n')-1];
                $strAndWhere = "AND ip.id_persona NOT IN (SELECT apd.valor4
                                                        FROM db_general.admi_parametro_det apd 
                                                            WHERE lower(apd.valor1) = lower(:strMes)
                                                            AND apd.estado='Activo'
                                                            AND apd.descripcion = 'BASES POR VENDEDOR'
                                                            AND apd.EMPRESA_COD=:intIdEmpresa
                                                            AND apd.valor2 = to_char(SYSDATE,'YYYY')
                                                    )";
                $strWhere=$strWhere.$strAndWhere;
            }
            else if( $strTipo =='editar' )
            {
                $strSelectAux = ", apd.VALOR1 ";
                $strJoin = " JOIN DB_GENERAL.ADMI_PARAMETRO_DET apd on apd.valor4=ip.ID_PERSONA ";                
                $strAndWhere =" AND apd.estado='Activo'
                                AND apd.descripcion = 'BASES POR VENDEDOR'
                                AND apd.EMPRESA_COD=:intIdEmpresa
                                AND apd.valor2 = to_char(SYSDATE,'YYYY') 
                                ";
                $strSelect = $strSelect.$strSelectAux;
                $strFrom = $strFrom.$strJoin;
                $strWhere=$strWhere.$strAndWhere;
            }
            else if( $strTipo == 'crearMeta' )
            {
                $arrayMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $strMes= (string)$arrayMeses[date('n')-1];

                $strAndWhere = " AND lower(ip.login) NOT IN ( SELECT lower(apd.valor5)
                                                                FROM db_general.admi_parametro_det apd 
                                                                    WHERE apd.estado='Activo'
                                                                    AND apd.descripcion = 'METAS POR VENDEDOR'
                                                                    AND apd.EMPRESA_COD=:intIdEmpresa 
                                                                    AND lower(apd.valor6) = lower(:strMes)
                                                                    AND apd.valor7 = to_char(SYSDATE,'YYYY')) ";
                $strWhere    = $strWhere.$strAndWhere;
            }
            else if( $strTipo =='editarMeta' )
            {
                $strSelectAux = ", apd.VALOR6 ";
                $strJoin = " JOIN DB_GENERAL.ADMI_PARAMETRO_DET apd on apd.valor5=ip.login ";                
                $strAndWhere =" AND apd.estado='Activo'
                                AND apd.descripcion = 'METAS POR VENDEDOR'
                                AND apd.EMPRESA_COD=:intIdEmpresa
                                AND apd.valor7 = to_char(SYSDATE,'YYYY') 
                                ";
                $strSelect = $strSelect.$strSelectAux;
                $strFrom = $strFrom.$strJoin;
                $strWhere=$strWhere.$strAndWhere;
            }                        
            $strOrder="ORDER BY ip.apellidos ";
            
            $strSql  = $strSelect.$strFrom.$strWhere.$strOrder;
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('strMes',$strMes);
            $objStmt->bindValue('intIdEmpresa',$intIdEmpresa);
            $objStmt->execute();

            $arrayVendedor = $objStmt->fetchAll();
        }
        catch(\Exception $e)
        {
            error_log('getVendedoresBases -> '.$e->getMessage());
            throw($e);
        } 
        return $arrayVendedor;        
    }

    /**
     * 
     * Metodo encargado de retornar los datos necesarios 
     * para llenar el grid en la administracion de la base
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-09-2018
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 12-12-2021 Se retornar los valores de Internet/Datos y Business Solutions.
    * @param array $arrayParametros [ 'id_parametro_det' => 'id', 
    *                                 'descripcion'      => 'descripcion del parametro', 
    *                                 'nombres'          => 'nombre del vendedor', 
    *                                 'apellidos'        => 'apellido del vendedor', 
    *                                 'valor1'           => 'Valor3 del detalle del parámetro', 
    *                                 'valor2'           => 'Valor2 del detalle del parámetro', 
    *                                 'valor3'           => 'Valor3 del detalle del parámetro',
    *                                 'estado'           => 'estado',  ]

     * @return array $objResultado ['encontrados', 'total']
     */    
    public function generarJson($arrayParametro)
    {
        $arrayEncontrados    = array();
        $arrayRegistros      = $this->getRegistros($arrayParametro);
        $arrayParametro['intStart']='';
        $arrayParametro['intLimit']='';
        $arrayRegistrosTotal = $this->getRegistros($arrayParametro);
        
        if ( $arrayRegistros ) 
        {
            $intTotal = count($arrayRegistrosTotal);
            foreach ($arrayRegistros as $data)
            {                        
                $arrayEncontrados[]=array('id_parametro_det' => $data['id'],
                                         'descripcion'      => ucwords(strtolower(trim($data['descripcion']))),
                                         'VENDEDOR'         => ucwords(strtolower(trim($data['apellidos'] .' '. $data['nombres']))),
                                         'BASEID'           => trim('$ '.$data['valor6']),
                                         'BASEBS'           => trim('$ '.$data['valor7']),
                                         'VALOR'            => trim('$ '.$data['valor3']),
                                         'VIGENCIA'         => trim($data['valor1'] .' - '. $data['valor2']),
                                         'estado'           => ucwords(strtolower (trim($data['estado']))),
                                         'action1'          => 'button-grid-show',
                                         'action2'          => (strtolower(trim($data['ESTADO']))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3'          => (strtolower(trim($data['ESTADO']))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if( $intTotal == 0 )
            {
                $objResultado= array('total' => 1 ,
                                 'encontrados' => array('id_caracteristica' => 0 , 'tipo' => 'Ninguno', 'descripcion_caracteristica' => 'Ninguno', 'caracteristica_id' => 0 , 'caracteristica_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objResultado = json_encode( $objResultado);
                return $objResultado;
            }
            else
            {
                $objData =json_encode($arrayEncontrados);
                $objResultado= '{"total":"'.$intTotal.'","encontrados":'.$objData.'}';
                return $objResultado;
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
            return $objResultado;
        }
        
    }
    /**
     * 
     * Metodo encargado de retornar los datos necesarios 
     * para generar el json en la administracion de la base
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-09-2018
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 12-12-2021 Se retornan los valores de Internet/Datos y Business Solutions.
     * Costo 5
     * 
     * @param array $arrayParametros [ 'strNombre'     => 'nombre a buscar', 
     *                                 'strApellido'   => 'apellido a buscar', 
     *                                 'strLogin'      => 'login a buscar',         
     *                                 'strMes'        => 'mes a buscar', 
     *                                 'intStart'      => 'valor de inicio', 
     *                                 'intLimit'      => 'valor limite',  ]
     * @return array $objResultado ['encontrados', 'total']
     */        
    public function getRegistros($arrayParametro)
    {
        $strAnio = date("Y");
        $objQueryBuilder = $this->_em->createQueryBuilder();
            $objQueryBuilder->select('APD.id,'
                        . 'APD.descripcion,'
                        . 'IP.nombres,'
                        . 'IP.apellidos,'
                        . 'APD.valor1,'
                        . 'APD.valor2,'
                        . 'APD.valor3,'
                        . 'APD.valor6,'
                        . 'APD.valor7,'
                        . 'APD.estado ')
               ->from('schemaBundle:AdmiParametroDet','APD')
               ->from('schemaBundle:InfoPersona','IP')
               ->where("IP.login=APD.valor5 ")
               ->andWhere("APD.descripcion = 'BASES POR VENDEDOR'")
               ->andWhere("IP.estado = 'Activo'")
               ->andWhere("APD.estado = 'Activo'")
               ->andWhere('APD.valor2 = ?5')
               ->orderBy('IP.apellidos', 'ASC');
               $objQueryBuilder->setParameter(5,(string)$strAnio);
                    
        if( $arrayParametro['strNombre']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(IP.nombres) like LOWER(?1)');
            $objQueryBuilder->setParameter(1, '%'.$arrayParametro['strNombre'].'%');
        }

        if( $arrayParametro['strApellido']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(IP.apellidos) like LOWER(?2)');
            $objQueryBuilder->setParameter(2, '%'.$arrayParametro['strApellido'].'%');
        }
        
        if( $arrayParametro['strMes']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(APD.valor1) like LOWER(?3)');
            $objQueryBuilder->setParameter(3, '%'.$arrayParametro['strMes'].'%');
        }        

        if( $arrayParametro['strLogin']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(APD.valor5) like LOWER(?4)');
            $objQueryBuilder->setParameter(4, '%'.$arrayParametro['strLogin'].'%');
        }

        if( $arrayParametro['intStart']!='' )
        {
            $objQueryBuilder->setFirstResult($arrayParametro['intStart']);
        }
            
        if( $arrayParametro['intLimit']!='' )
        {
            $objQueryBuilder->setMaxResults($arrayParametro['intLimit']);
        }            
                
        $objQuery = $objQueryBuilder->getQuery();
        
        return $objQuery->getResult();
    }
    /**
     * 
     * Metodo encargado de retornar los datos necesarios 
     * para llenar el grid en la administracion de la meta
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-09-2018
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 12-12-2021 Se retornan los valores de Internet/Datos y Business Solutions.
    * @param array $arrayParametros [ 'id_parametro_det' => 'id', 
    *                                 'descripcion'      => 'descripcion del parametro', 
    *                                 'nombres'          => 'nombre del vendedor', 
    *                                 'apellidos'        => 'apellido del vendedor', 
    *                                 'valor1'           => 'Valor3 del detalle del parámetro', 
    *                                 'valor2'           => 'Valor2 del detalle del parámetro', 
    *                                 'valor3'           => 'Valor3 del detalle del parámetro',
    *                                 'estado'           => 'estado',  ]

     * @return array $objResultado ['encontrados', 'total']
     */    
    public function generarJsonMeta($arrayParametro)
    {        
        $arrayEncontrados = array();
        $arrayRegistros   = $this->getRegistrosMetas($arrayParametro);
        $arrayParametro['intStart']='';
        $arrayParametro['intLimit']='';
        $arrayRegistrosTotal = $this->getRegistrosMetas($arrayParametro);
        
        if ( $arrayRegistros ) 
        {
            $intTotal = count($arrayRegistrosTotal);
            foreach ($arrayRegistros as $data)
            {                        
                $arrayEncontrados[]=array('id_parametro_det' => $data['id'],
                                         'descripcion'      => ucwords(strtolower(trim($data['descripcion']))),                                         
                                         'VENDEDOR'         => ucwords(strtolower(trim($data['apellidos'] .' '. $data['nombres']))),
                                         'METAID'           => trim('$ '.$data['valor1']),
                                         'METABS'           => trim('$ '.$data['valor2']),
                                         'VALOR_MRC'        => trim('$ '.$data['valor3']),
                                         'VALOR_NRC'        => trim('$ '.$data['valor4']),
                                         'VIGENCIA'         => trim($data['valor6'] .' - '. $data['valor7']),
                                         'estado'           => ucwords(strtolower (trim($data['estado']))),
                                         'action1'          => 'button-grid-show',
                                         'action2'          => (strtolower(trim($data['ESTADO']))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3'          => (strtolower(trim($data['ESTADO']))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if( $intTotal == 0 )
            {
                $objResultado= array('total' => 1 ,
                                 'encontrados' => array('id_caracteristica' => 0 , 'tipo' => 'Ninguno', 'descripcion_caracteristica' => 'Ninguno', 'caracteristica_id' => 0 , 'caracteristica_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objResultado = json_encode( $objResultado);
                return $objResultado;
            }
            else
            {
                $objData =json_encode($arrayEncontrados);
                $objResultado= '{"total":"'.$intTotal.'","encontrados":'.$objData.'}';
                return $objResultado;
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
            return $objResultado;
        }
        
    }
    /**
     * 
     * Metodo encargado de retornar los datos necesarios 
     * para generar el json en la administracion de la meta
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-09-2018
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 12-12-2021 Se retornan valores de Internet/Datos y Business Solutions.
     * 
     * Costo 5
     * 
     * @param array $arrayParametros [ 'strNombre'     => 'nombre a buscar', 
     *                                 'strApellido'   => 'apellido a buscar', 
     *                                 'strLogin'      => 'login a buscar',         
     *                                 'intStart'      => 'valor de inicio', 
     *                                 'intLimit'      => 'valor limite',  ]
     * @return array $objResultado ['encontrados', 'total']
     */            
    public function getRegistrosMetas($arrayParametro)
    {
        $strAnio = date("Y");
        $objQueryBuilder = $this->_em->createQueryBuilder();
            $objQueryBuilder->select('APD.id,'
                        . 'APD.descripcion,'
                        . 'IP.nombres,'
                        . 'IP.apellidos,'
                        . 'APD.valor1,'
                        . 'APD.valor2,'
                        . 'APD.valor3,'
                        . 'APD.valor4,'
                        . 'APD.valor6,'
                        . 'APD.valor7,'                        
                        . 'APD.estado ')
               ->from('schemaBundle:AdmiParametroDet','APD')
               ->from('schemaBundle:InfoPersona','IP')
               ->where("IP.login=APD.valor5 ")
               ->andWhere("APD.descripcion = 'METAS POR VENDEDOR'")
               ->andWhere("IP.estado = 'Activo'")
               ->andWhere("APD.estado = 'Activo'")
               ->andWhere('APD.valor7 = ?4')
               ->orderBy('IP.apellidos', 'ASC');               
               $objQueryBuilder->setParameter(4,(string)$strAnio);
            
        if( $arrayParametro['strNombre']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(IP.nombres) like LOWER(?1)');
            $objQueryBuilder->setParameter(1, '%'.$arrayParametro['strNombre'].'%');
        }

        if( $arrayParametro['strApellido']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(IP.apellidos) like LOWER(?2)');
            $objQueryBuilder->setParameter(2, '%'.$arrayParametro['strApellido'].'%');
        }        

        if( $arrayParametro['strLogin']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(APD.valor5) like LOWER(?3)');
            $objQueryBuilder->setParameter(3, '%'.$arrayParametro['strLogin'].'%');
        }

        if( $arrayParametro['strMes']!="" )
        {
            $objQueryBuilder ->andWhere( 'LOWER(APD.valor6) like LOWER(?5)');
            $objQueryBuilder->setParameter(5, '%'.$arrayParametro['strMes'].'%');
        }

        if( $arrayParametro['intStart']!='' )
        {
            $objQueryBuilder->setFirstResult($arrayParametro['intStart']);
        }
            
        if( $arrayParametro['intLimit']!='' )
        {
            $objQueryBuilder->setMaxResults($arrayParametro['intLimit']);
        }            
                
        $objQuery = $objQueryBuilder->getQuery();
        
        return $objQuery->getResult();
    } 
    /**
     * 
     * Metodo que verifica atraves del id_producto, si dicho producto necesita escalabilidad
     * 
     * Costo 6
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 08-04-2019
     * 
     * @param string $strIdProducto
     * @return $boolDatos
     */
    public function findProductosEscalabilidad($intIdProducto)
    {
            $objQuery = $this->_em->createQuery("SELECT apd "
                . " FROM schemaBundle:AdmiParametroCab apc, "
                . "      schemaBundle:AdmiParametroDet apd, "
                . "      schemaBundle:AdmiProducto ap "
                . " WHERE apc.nombreParametro ='PRODUCTOS CON FILTRO ESCALABLES' "
                . " and apc.id=apd.parametroId and apc.estado=:Estado"
                . " and ap.descripcionProducto = apd.valor1 "
                . " and ap.id=:Producto");
            $objQuery->setParameter("Producto", $intIdProducto);
            $objQuery->setParameter("Estado", 'Activo');
            $intTotal = count($objQuery->getResult());

            if($intTotal == 0)
            {
                $boolDatos = false;
            }
            else
            {
                $boolDatos = true;
            }
        return $boolDatos;
    }

    /**
     * 
     * Metodo que obtiene arbol de categorias con sus respectivas tareas para Movil
     * 
     * Costo 219
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.0 11-06-2019
     * 
     * @param string $arrayParametro
     * @return $arrayFinish
     * 
     * Se cambia nombre de variable para mayor lectura de código
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.1 11-06-2019
     * 
     * Se cambia nombre de variable para mayor lectura de código
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.2 09-11-2019
     *
     * 
     * Se valida la respuesta según el origen de la petición si en desde el móvil o desde Web.
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.3 31-03-2020 
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @author Ronny Moran <rmoranc@telconet.ec>
     * @version 1.4 - 28-07-2020 
     * se agrega filtro por estado
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.5 - 05-11-2020 
     * se modifica filtro DPT_SIN_FILTRO para poder buscar árbol de categorias de varios departamentos especificos
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.6 - 10-05-2021 
     * se agrega variable requiereEquipo según el fin de tarea.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.7 - 30-06-2021 
     * se agrega variable requiereEquiposNodo según el fin de tarea.
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.8 - 13-06-2022 
     * se agrega validacion para departamento de fibra.
     * 
     * @param string $arrayParametro
     * @return $arrayReturn array respuesta.
     * 
     */
    public function getCategoriasTareas($arrayParametro)
    {
        try
        {
            $objRsm                     = new ResultSetMappingBuilder($this->_em);
            $objQuery                   = $this->_em->createNativeQuery(null, $objRsm);
            $strTipoOrigen              = $arrayParametro['tipoOrigen'];
            $strParametroOrigenWeb      = $arrayParametro['strParametroOrigenWeb'];
            $strParametroOrigenMovil    = $arrayParametro['strParametroOrigenMovil'];
            $strParametroAdministrativo = $arrayParametro['strParametroAdministrativo'];
            $arrayParametroAdministrativo = explode(",", $strParametroAdministrativo);
            
            $strSelect = "SELECT DISTINCT APD.VALOR3, AT2.NOMBRE_TAREA, AT2.REQUIERE_FIBRA , AT2.REQUIERE_MATERIAL, AT2.REQUIERE_EQUIPO, 
                            AT2.REQUIERE_RUTA_FIBRA, AT2.REQUIERE_EQUIPOS_NODO, AT2.REQUIERE_ACCESO_NODO, APD.VALOR1, APD.VALOR2, APD.VALOR4 
            FROM
            (
            SELECT AT.ID_TAREA, AT.NOMBRE_TAREA, AT.REQUIERE_FIBRA , RM.REQUIERE_MATERIAL, RRF.REQUIERE_RUTA_FIBRA, REQ.REQUIERE_EQUIPO,
                   REN.REQUIERE_EQUIPOS_NODO, RAN.REQUIERE_ACCESO_NODO
                        FROM
                        (
                        SELECT AT1.* 
                        FROM DB_SOPORTE.ADMI_TAREA AT1
                            WHERE AT1.ESTADO = 'Activo'
                        ) AT
                        LEFT JOIN 
                        (
                        SELECT ITC1.TAREA_ID, ITC1.VALOR AS REQUIERE_MATERIAL 
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC1 
                        WHERE ITC1.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
                        AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_MATERIAL'
                        ) RM ON AT.ID_TAREA                 = RM.TAREA_ID
                        LEFT JOIN 
                        (
                        SELECT ITC2.TAREA_ID, ITC2.VALOR AS REQUIERE_RUTA_FIBRA 
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC2 
                        WHERE ITC2.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
                        AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_RUTA_FIBRA'
                        ) RRF ON AT.ID_TAREA                = RRF.TAREA_ID
                        LEFT JOIN 
                        (
                        SELECT ITC3.TAREA_ID, ITC3.VALOR AS REQUIERE_EQUIPO 
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC3 
                        WHERE ITC3.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
                        AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_EQUIPO'
                        ) REQ ON AT.ID_TAREA                = REQ.TAREA_ID
                        LEFT JOIN 
                        (
                        SELECT ITC4.TAREA_ID, ITC4.VALOR AS REQUIERE_EQUIPOS_NODO 
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC4 
                        WHERE ITC4.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
                        AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_EQUIPOS_NODO'
                        ) REN ON AT.ID_TAREA                = REN.TAREA_ID
                        LEFT JOIN 
                        (
                        SELECT ITC3.TAREA_ID, ITC3.VALOR AS REQUIERE_ACCESO_NODO 
                        FROM DB_COMERCIAL.ADMI_CARACTERISTICA AC, DB_SOPORTE.INFO_TAREA_CARACTERISTICA ITC3 
                        WHERE ITC3.CARACTERISTICA_ID      = AC.ID_CARACTERISTICA
                        AND AC.DESCRIPCION_CARACTERISTICA = 'REQUIERE_ACCESO_NODO'
                        ) RAN ON AT.ID_TAREA                = RAN.TAREA_ID
            )AT2, DB_GENERAL.ADMI_PARAMETRO_DET APD ";

            $strWhere = " WHERE APD.PARAMETRO_ID = :parametroId
            AND APD.VALOR3 = AT2.ID_TAREA 
            AND APD.ESTADO = :estadoParametroCat";

            $strOrderBy = " ORDER BY APD.VALOR1, APD.VALOR2 ";

            $boolFiltrarDep = true;

            for ($intPosicion = 0; $intPosicion < count($arrayParametro['arrayDeptSinFiltro']); $intPosicion++)
            {
                if($arrayParametro['idDepartamento'] == $arrayParametro['arrayDeptSinFiltro'][$intPosicion]->getValor1())
                {
                    $boolFiltrarDep = false;
                    $arrayDepartamentosFiltro = explode(",",$arrayParametro['arrayDeptSinFiltro'][$intPosicion]->getValor2());
                }
            }

            if($boolFiltrarDep === true)
            {
                $strWhere .= " AND APD.VALOR5 =:idDepartamento ";
            }
            else
            {
                $strWhere .= " AND APD.VALOR5 in (:idDepartamento) ";
                $arrayParametro['idDepartamento'] = $arrayDepartamentosFiltro;
            }

            $objQuery->setParameter("parametroId",      $arrayParametro['idParametroCab']);
            $objQuery->setParameter("idDepartamento",   $arrayParametro['idDepartamento']);
            $objQuery->setParameter("estadoParametroCat",   "Activo");

            $objRsm->addScalarResult('NOMBRE_TAREA',        'nombreTarea',      'string');
            $objRsm->addScalarResult('REQUIERE_FIBRA',      'requiereFibra',    'string');
            $objRsm->addScalarResult('REQUIERE_MATERIAL',   'requiereMaterial', 'string');
            $objRsm->addScalarResult('REQUIERE_RUTA_FIBRA', 'requiereRutaFibra','string');
            $objRsm->addScalarResult('REQUIERE_EQUIPO',     'requiereEquipo',   'string');
            $objRsm->addScalarResult('REQUIERE_EQUIPOS_NODO', 'requiereEquiposNodo',   'string');
            $objRsm->addScalarResult('REQUIERE_ACCESO_NODO', 'requiereAccesoNodo','string');
            $objRsm->addScalarResult('VALOR1',              'valor1',           'string');
            $objRsm->addScalarResult('VALOR2',              'valor2',           'string');
            $objRsm->addScalarResult('VALOR3',              'valor3',           'string');
            $objRsm->addScalarResult('VALOR4',              'valor4',           'string');

            $objQuery->setSQL($strSelect.$strWhere.$strOrderBy);    
            $arrayResultado = $objQuery->getArrayResult();

            $arrayData=[];
            $arrayFinish=[];
            if(!empty($arrayResultado))
            {
                for ($intPosicion = 0; $intPosicion < count($arrayResultado); $intPosicion++)
                {
                $arrayData[$arrayResultado[$intPosicion]['valor1']][$arrayResultado[$intPosicion]['valor2']][]=
                array(
                    'numeroTarea'         => $arrayResultado[$intPosicion]['valor3'],
                    'nombreTarea'         => $arrayResultado[$intPosicion]['nombreTarea'],
                    'requiereMaterial'    => ($arrayResultado[$intPosicion]['requiereMaterial']  
                                                ? $arrayResultado[$intPosicion]['requiereMaterial'] : "N"),
                    'requiereFibra'       => ($arrayResultado[$intPosicion]['requiereFibra']  
                                                ? $arrayResultado[$intPosicion]['requiereFibra']  : "N"),
                    'requiereRutaFibra'   => ($arrayResultado[$intPosicion]['requiereRutaFibra'] 
                                                ? $arrayResultado[$intPosicion]['requiereRutaFibra']  : "N"),
                    'requiereEquipo'      => ($arrayResultado[$intPosicion]['requiereEquipo']  
                                                ? $arrayResultado[$intPosicion]['requiereEquipo'] : "N"),
                    'requiereEquiposNodo'      => ($arrayResultado[$intPosicion]['requiereEquiposNodo']  
                                                ? $arrayResultado[$intPosicion]['requiereEquiposNodo'] : "N"),
                    'requiereAccesoNodo'  => ($arrayResultado[$intPosicion]['requiereAccesoNodo'] 
                                                ? $arrayResultado[$intPosicion]['requiereAccesoNodo']  : "N"),
                    'nombreImagen'        => $arrayResultado[$intPosicion]['valor4']
                );
                }
                foreach($arrayData as $strEtiqueta=>$arrayItem)
                {
                    $arrayChildrens=[];
                    foreach($arrayItem as $strEtiquetaHijo=>$arrayChild)
                    {
                        $arrayTareas=[];
                        foreach($arrayChild as $arrayValue)
                        {
                            $arrayTareas[]=[
                                'numeroTarea'       =>$arrayValue['numeroTarea'],
                                'nombreTarea'       =>$arrayValue['nombreTarea'],
                                'requiereMaterial'  =>$arrayValue['requiereMaterial'],
                                'requiereFibra'     =>$arrayValue['requiereFibra'],
                                'requiereRutaFibra' =>$arrayValue['requiereRutaFibra'],
                                'requiereEquipo'    =>$arrayValue['requiereEquipo'],
                                'requiereEquiposNodo' =>$arrayValue['requiereEquiposNodo'],
                                'requiereAccesoNodo' =>$arrayValue['requiereAccesoNodo'],
                                'nombreTarea'       =>$arrayValue['nombreTarea']
                            ];
                            $strImagen = $arrayValue['nombreImagen'];
                        }
                        $arrayChildrens[]=['nombreHijo'=>$strEtiquetaHijo,'listaTareas'=>$arrayTareas];
                    }
                    
                    if($strTipoOrigen == $strParametroOrigenMovil && !in_array($strEtiqueta, $arrayParametroAdministrativo))
                    {
                        $arrayFinish[]=
                        [
                            'nombreCategoria'=>$strEtiqueta, 
                            'nombreImagen'=>$arrayParametro['urlImage'].$strImagen ,
                            'hijosCategoria'=> $arrayChildrens
                        ];    
                    }
                    
                    if($strTipoOrigen == $strParametroOrigenWeb)
                    {
                        $arrayFinish[]=
                        [
                            'nombreCategoria'=>$strEtiqueta, 
                            'nombreImagen'=>$arrayParametro['urlImage'].$strImagen ,
                            'hijosCategoria'=> $arrayChildrens
                        ];
                    }
                    
                }

            }
        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta.';
            return $arrayReturn;
        } 
        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayFinish;
        return $arrayReturn;
    }

    /**
     * 
     * Metodo que obtiene etiquetas de fotos por departamento
     * 
     * Costo (2)
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.0 01-07-2019
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 26-09-2022 - Se agrega la validación para permitir etiquetas personalizadas
     *                           por nombre técnico del producto de un servicio.
     * 
     * @param string $arrayParametro
     * @return $arrayReturn
     */
    public function getEtiquetasFotos($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT DISTINCT APD.VALOR1, APD.VALOR2, APD.VALOR4, APD.VALOR5
            FROM DB_GENERAL.ADMI_PARAMETRO_DET APD ";

            $strWhere = " WHERE APD.PARAMETRO_ID = :parametroId ";

            $boolFiltrarDep = true;

            for ( $intIterador = 0; $intIterador < count($arrayParametro['arrayDeptSinFiltro']); $intIterador++ )
            {
                if( $arrayParametro['idDepartamento'] == $arrayParametro['arrayDeptSinFiltro'][$intIterador]->getValor1() )
                {
                    $boolFiltrarDep = false;
                }
            }

            if($boolFiltrarDep === true)
            {
                $strWhere .= " AND APD.VALOR3 =:idDepartamento ";
            }

            $objQuery->setParameter("parametroId",      $arrayParametro['idParametroCab']);
            $objQuery->setParameter("idDepartamento",   $arrayParametro['idDepartamento']);

            if(isset($arrayParametro['strNombreTecnico']) && !empty($arrayParametro['strNombreTecnico']))
            {
                $arrayParVerNombreTenico = $this->_em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('ETIQUETA_FOTO_NOMBRE_TECNICO',
                                                         'MOVIL',
                                                         '',
                                                         '',
                                                         $arrayParametro['strNombreTecnico'],
                                                         '',
                                                         '',
                                                         '',
                                                         '');
                if(isset($arrayParVerNombreTenico) && !empty($arrayParVerNombreTenico))
                {
                    $strWhere .= " AND ( APD.VALOR6 = :strEstadoServicio OR APD.VALOR6 IS NULL )
                                   AND APD.VALOR7 = :strNombreTecnico ";
                    $objQuery->setParameter("strEstadoServicio", $arrayParametro['strEstadoServicio']);
                    $objQuery->setParameter("strNombreTecnico", $arrayParametro['strNombreTecnico']);
                }
                else
                {
                    $strWhere .= " AND APD.VALOR7 IS NULL ";
                }
            }
            else
            {
                $strWhere .= " AND APD.VALOR7 IS NULL ";
            }

            $objRsm->addScalarResult('VALOR1',  'valor1',   'string');
            $objRsm->addScalarResult('VALOR2',  'valor2',   'string');
            $objRsm->addScalarResult('VALOR4',  'valor4',   'string');
            $objRsm->addScalarResult('VALOR5',  'valor5',   'string');

            $objQuery->setSQL($strSelect.$strWhere.$strOrderBy);    
            $arrayResultado = $objQuery->getArrayResult();
            
            $arrayFinish=[];

            if(!empty($arrayResultado))
            {
                foreach($arrayResultado as $arrayValue)
                {
                    $arrayFinish[] = ['nombreEtiqueta'=>$arrayValue['valor1'], 
                                      'nombreImagen'=>$arrayParametro['urlImage'].$arrayValue['valor2'],
                                      'obligatorio'=> $arrayValue['valor4'],
                                      'empresa'=> ($arrayValue['valor5'] ? $arrayValue['valor5']  : "0")];
                                      
                }
            }

        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta.';
            return $arrayReturn;
        } 

        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayFinish;
        return $arrayReturn;
    }

    /**
     * 
     * Metodo que obtiene todos los parametros generales del Móvil
     * 
     * Costo (2)
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.0 11-07-2019
     * 
     * Se agrega orderBy tipo ASC para mejor lectura de los parametros.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.1 30-10-2020
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.1 15-10-2020 Se modifíca lógica para mejorar la obtención de parametros 
     * 
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.2 06-01-2021
     * 
     * se agrega lógica para agregar nuevo parametro en json TIEMPO_MAXIMO_TOKEN_SEGURIDAD
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.3 26-11-2021
     * 
     * se agrega lógica para agregar nuevos parametros en 
     * json ('COMBO_RESOLUCION_CAMARA', 'COMBO_CODEC_CAMARA', 'COMBO_FPS_CAMARA')       = $strFpsCamara;
     * 
     * @param string $arrayParametro
     * @return $arrayReturn
     */
    public function getParametrosGeneralesMovil($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $strEmpresaIdTN = '10';

            $strSelect = "SELECT DISTINCT APD.ID_PARAMETRO_DET, APD.VALOR1, APD.VALOR2
            FROM DB_GENERAL.ADMI_PARAMETRO_DET APD ";

            $strWhere = " WHERE APD.PARAMETRO_ID = :parametroId 
            AND APD.ESTADO = :estado 
            AND APD.VALOR3 IS NULL 
            ORDER BY APD.ID_PARAMETRO_DET ASC ";

            $objQuery->setParameter("parametroId",  $arrayParametro['idParametroCab']);
            $objQuery->setParameter("estado",       'Activo');

            $objRsm->addScalarResult('VALOR1',  'valor1',   'string');
            $objRsm->addScalarResult('VALOR2',  'valor2',   'string');

            $objQuery->setSQL($strSelect.$strWhere.$strOrderBy);    
            $arrayResultado = $objQuery->getArrayResult();
            
            $arrayFinish=[];

            if(!empty($arrayResultado))
            {
                foreach($arrayResultado as $arrayValue)
                {
                    if(!is_null($arrayValue['valor1']) && !is_null($arrayValue['valor2']))
                    {
                        $arrayFinish[$arrayValue['valor1']] = $arrayValue['valor2'];
                    }                                      
                }
            }

            //Inicio agregar un nuevo key para guardar el valor de tiempo de token
            $strTiempoMaximoTokenSeguridad = "60";
            $strNameSourceTMO      = "";
            $arrayNameSourceTMO    = $this->getOne('PARAMETROS_GENERALES_MOVIL', 
                    '', 
                    '', 
                    '', 
                    'NOMBRE_SOURCE_MOVIL', 
                    '', 
                    '', 
                    ''
                    );

            if(is_array($arrayNameSourceTMO))
            {
                $strNameSourceTMO = !empty($arrayNameSourceTMO['valor2']) ? $arrayNameSourceTMO['valor2'] : "ec.telconet.mobile.telcos.operaciones";
            }
            $arrayRequestDataToken = array('nameApplication' => $strNameSourceTMO);
            $arrayResultadoDataToken = $this->getDataTokenApplication($arrayRequestDataToken);
            if($arrayResultadoDataToken['status'] == 'OK' 
                && count($arrayResultadoDataToken['respuesta']) > 0)
                {
                    $strTiempoMaximoTokenSeguridad = $arrayResultadoDataToken['respuesta'][0]['expiredTime'];
                }

            $arrayFinish['TIEMPO_MAXIMO_TOKEN_SEGURIDAD'] = $strTiempoMaximoTokenSeguridad;
            //Fin agregar un nuevo key para guardar el valor de tiempo de token

            //Inicio agregar un nuevos key para guardar los valores de caracteristicas de camaras para activacion safeCity
            $arrayResolucionCamara  = [];
            $arrayCodecCamara       = [];
            $arrayFpsCamara         = [];

            $arrayParametrosResolucionCamara = $this->get('PARAMETROS PROYECTO GPON SAFECITY',
            'INFRAESTRUCTURA',
            'PARAMETROS',
            'RESOLUCION_CAMARA',
            '',
            '',
            '',
            '',
            '',
            $strEmpresaIdTN);

            $arrayParametrosCodecCamara = $this->get('PARAMETROS PROYECTO GPON SAFECITY',
            'INFRAESTRUCTURA',
            'PARAMETROS',
            'CODEC',
            '',
            '',
            '',
            '',
            '',
            $strEmpresaIdTN);

            $arrayParametrosFpsCamara = $this->get('PARAMETROS PROYECTO GPON SAFECITY',
            'INFRAESTRUCTURA',
            'PARAMETROS',
            'FPS',
            '',
            '',
            '',
            '',
            '',
            $strEmpresaIdTN);

            foreach($arrayParametrosResolucionCamara as $arrayParametroResolucionCamara)
            {
                $arrayResolucionCamara[] = $arrayParametroResolucionCamara['valor1'];
            }

            foreach($arrayParametrosCodecCamara as $arrayParametroCodecCamara)
            {
                $arrayCodecCamara[] = $arrayParametroCodecCamara['valor1'];
            }

            foreach($arrayParametrosFpsCamara as $arrayParametroFpsCamara)
            {
                $arrayFpsCamara[] = $arrayParametroFpsCamara['valor1'];
            }

            $strResolucionCamara    = implode(",", $arrayResolucionCamara);
            $strCodecCamara         = implode(",", $arrayCodecCamara);
            $strFpsCamara           = implode(",", $arrayFpsCamara);

            $arrayFinish['COMBO_RESOLUCION_CAMARA'] = $strResolucionCamara;
            $arrayFinish['COMBO_CODEC_CAMARA']      = $strCodecCamara;
            $arrayFinish['COMBO_FPS_CAMARA']        = $strFpsCamara;
            //Fin agregar un nuevos key para guardar los valores de caracteristicas de camaras para activacion safeCity

        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta.';
            return $arrayReturn;
        } 

        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayFinish;
        return $arrayReturn;
    }

    /**
     * Documentación para el método 'getMetasVendedor'.
     *
     * Método encargado de retornar metas de los vendedores según los parámetros.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 25-07-2019
     *
     * Costo 50
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strDescripcion"        => Descripción que identifica la meta de un vendedor.
     *                                  "strMes"                => Mes valido para la meta del vendedor.
     *                                  "strAnio"               => Año valido para la meta del vendedor.
     *                                  "strEstado"             => Estado de la meta del vendedor.
     *                                  "strGrupoSubgerente"    => Usuario del Subgerente que se desea retornar la meta por los 
     *                                                             vendedores que reportan al mismo.
     *                                  "strVendedor"           => Usuario del vendeddor que se desea retornar la meta.
     *                                  "strAplication"         => Nombre de la aplicación.
     *                               ]
     *
     * @return array $arrayMetasVendedor arreglo de las metas del o los vendedores.
     */
    public function getMetasVendedor($arrayParametros)
    {
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $arrayDescripcion      = array();
        $arrayDescripcion      = $arrayParametros['arrayDescripcion'] ? $arrayParametros['arrayDescripcion']:"";
        $strMes                = $arrayParametros['strMes'] ? $arrayParametros['strMes']:"";
        $strAnio               = $arrayParametros['strAnio'] ? $arrayParametros['strAnio']:"";
        $strEstado             = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:"Activo";
        $strGrupoSubgerente    = $arrayParametros['strGrupoSubgerente'] ? $arrayParametros['strGrupoSubgerente']:"";
        $strVendedor           = $arrayParametros['strVendedor'] ? $arrayParametros['strVendedor']:"";
        $arrayMetasVendedor    = array();
        $strMensajeError       = '';
        $objQuery              = $this->_em->createQuery();
        try
        {
            if( (!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN') && (!empty($arrayDescripcion) && !empty($strCodEmpresa)) )
            {
                $strSelect = "SELECT apd.valor5 vendedor, "
                                    . "apd.valor3 metaMrc, "
                                    . "apd.valor4 metaNrc, "
                                    . "apd.valor6 mesVigente, "
                                    . "apd.valor7 anioVigente ";
                $strFrom   = "FROM schemaBundle:AdmiParametroDet apd ";
                $strWhere  = "WHERE apd.descripcion     = :strDescripcion
                                    AND apd.estado      = :strEstado
                                    AND apd.empresaCod  = :strCodEmpresa ";
                if( !empty($strMes) )
                {
                    $strWhere.="AND lower(apd.valor6) = lower(:strMes) ";
                    $objQuery->setParameter("strMes", $strMes);
                }
                if( !empty($strAnio) )
                {
                    $strWhere.="AND lower(apd.valor7) = lower(:strAnio) ";
                    $objQuery->setParameter("strAnio", $strAnio);
                }
                $objQuery->setParameter("strDescripcion", $arrayDescripcion['METAS']);
                $objQuery->setParameter("strEstado", $strEstado);
                $objQuery->setParameter("strCodEmpresa", $strCodEmpresa);
                if( !empty($strGrupoSubgerente) || !empty($strVendedor) )
                {
                    $strInSelect = "SELECT ipvendedor.login ";
                    $strInFrom   = "FROM schemaBundle:InfoPersona ipvendedor ";
                    if( !empty($strGrupoSubgerente) )
                    {
                        $strInFrom  .= " ,schemaBundle:InfoPersonaEmpresaRol  ipervendedor, "
                                        . "schemaBundle:InfoPersona           ip, "
                                        . "schemaBundle:InfoPersonaEmpresaRol iper ";
                        $strInWhere .= "WHERE ip.id                                     = iper.personaId
                                            AND ip.login                                = :strUsuarioSubgerente
                                            AND ipvendedor.id                           = ipervendedor.personaId
                                            AND ipervendedor.reportaPersonaEmpresaRolId = iper.id";
                        $objQuery->setParameter("strUsuarioSubgerente", $strGrupoSubgerente);
                    }
                    if( !empty($strVendedor) )
                    {
                        $strInWhere = "WHERE ipvendedor.login = :strUsuarioVendedor";
                        $objQuery->setParameter("strUsuarioVendedor", $strVendedor);
                    }
                    $strWhere .= "AND apd.valor5 in( ".$strInSelect.$strInFrom.$strInWhere.")";
                }
                $strOrderBy = 'order by apd.feCreacion asc';
                $strSql     = $strSelect.$strFrom.$strWhere.$strOrderBy;
                $objQuery->setDQL($strSql);
                $arrayMetasVendedor['MetasVendedor'] = $objQuery->getResult();
            }
            else
            {
                throw new \Exception('Las metas del o los vendedores solo aplican para la empresa Telconet');
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayMetasVendedor['error'] = $strMensajeError;
        return $arrayMetasVendedor;
    }
    
    /**
     * Documentación para el método 'getBasesVendedor'.
     *
     * Método encargado de retornar bases de los vendedores según los parámetros.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 25-07-2019
     *
     * Costo 14
     *
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"     => Prefijo de la empresa.
     *                                  "strCodEmpresa"         => Código de la empresa.
     *                                  "strDescripcion"        => Descripcion que identifica la meta de un vendedor.
     *                                  "strMes"                => Mes válido para la meta del vendedor.
     *                                  "strAnio"               => Año válido para la meta del vendedor.
     *                                  "strEstado"             => Estado de la meta del vendedor.
     *                                  "strGrupoSubgerente"    => Usuario del Subgerente que se desea retornar la meta por los
     *                                                             vendedores que reportan al mismo.
     *                                  "strVendedor"           => Usuario del vendeddor que se desea retornar la meta.
     *                                  "strAplication"         => Nombre de la aplicación.
     *                               ]
     *
     * @return array $arrayBasesVendedor arreglo de las bases del o los vendedores.
     */
    public function getBasesVendedor($arrayParametros)
    {
        $strPrefijoEmpresa     = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa']:"";
        $strCodEmpresa         = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa']:"";
        $arrayDescripcion      = array();
        $arrayDescripcion      = $arrayParametros['arrayDescripcion'] ? $arrayParametros['arrayDescripcion']:"";
        $strMes                = $arrayParametros['strMes'] ? $arrayParametros['strMes']:"";
        $strAnio               = $arrayParametros['strAnio'] ? $arrayParametros['strAnio']:"";
        $strEstado             = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:"Activo";
        $strGrupoSubgerente    = $arrayParametros['strGrupoSubgerente'] ? $arrayParametros['strGrupoSubgerente']:"";
        $strVendedor           = $arrayParametros['strVendedor'] ? $arrayParametros['strVendedor']:"";
        $arrayBasesVendedor    = array();
        $strMensajeError       = '';
        $objQuery              = $this->_em->createQuery();
        try
        {
            if( (!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN') && (!empty($arrayDescripcion) && !empty($strCodEmpresa)) )
            {
                $strSelect = "SELECT apd.valor5 vendedor, "
                                    . "apd.valor3 baseMrc, "
                                    . "apd.valor1 mesVigente, "
                                    . "apd.valor2 anioVigente ";
                $strFrom   = "FROM schemaBundle:AdmiParametroDet apd ";
                $strWhere  = "WHERE apd.descripcion     = :strDescripcion
                                    AND apd.estado      = :strEstado
                                    AND apd.empresaCod  = :strCodEmpresa ";
                if( !empty($strMes) )
                {
                    $strWhere.="AND lower(apd.valor1) = lower(:strMes) ";
                    $objQuery->setParameter("strMes", $strMes);
                }
                if( !empty($strAnio) )
                {
                    $strWhere.="AND apd.valor2 = :strAnio ";
                    $objQuery->setParameter("strAnio", $strAnio);
                }
                $objQuery->setParameter("strDescripcion", $arrayDescripcion['BASES']);
                $objQuery->setParameter("strEstado", $strEstado);
                $objQuery->setParameter("strCodEmpresa", $strCodEmpresa);
                if( !empty($strGrupoSubgerente) || !empty($strVendedor) )
                {
                    $strInSelect = "SELECT ipvendedor.login ";
                    $strInFrom   = "FROM schemaBundle:InfoPersona ipvendedor ";
                    if( !empty($strGrupoSubgerente) )
                    {
                        $strInFrom  .= " ,schemaBundle:InfoPersonaEmpresaRol  ipervendedor, "
                                        . "schemaBundle:InfoPersona           ip, "
                                        . "schemaBundle:InfoPersonaEmpresaRol iper ";
                        $strInWhere .= "WHERE ip.id                                     = iper.personaId
                                            AND ip.login                                = :strUsuarioSubgerente
                                            AND ipvendedor.id                           = ipervendedor.personaId
                                            AND ipervendedor.reportaPersonaEmpresaRolId = iper.id";
                        $objQuery->setParameter("strUsuarioSubgerente", $strGrupoSubgerente);
                    }
                    if( !empty($strVendedor) )
                    {
                        $strInWhere = "WHERE ipvendedor.login = :strUsuarioVendedor";
                        $objQuery->setParameter("strUsuarioVendedor", $strVendedor);
                    }
                    $strWhere .= "AND apd.valor5 in( ".$strInSelect.$strInFrom.$strInWhere.")";
                }
                $strOrderBy = 'order by apd.feCreacion asc';
                $strSql     = $strSelect.$strFrom.$strWhere.$strOrderBy;
                $objQuery->setDQL($strSql);
                $arrayBasesVendedor['BaseVendedor'] = $objQuery->getResult();
            }
            else
            {
                throw new \Exception('Las bases del o los vendedores solo aplican para la empresa Telconet');
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayBasesVendedor['error'] = $strMensajeError;
        return $arrayBasesVendedor;
    }

    /**
     * 
     * Metodo que obtiene arbol de categorias de fotos para Movil
     * 
     * Costo 219
     * 
     * Se cambia nombre de variable para mayor lectura de código
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.0 29-10-2019
     * 
     * @param string $arrayParametro
     * @return $arrayReturn array respuesta.
     * 
     */
    public function getCategoriasFotos($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT DISTINCT APD.VALOR1, APD.VALOR2
            FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
            WHERE APD.PARAMETRO_ID  = :parametroId
            AND APD.VALOR4          = :idTarea
            AND APD.VALOR5          = :idDepartamento
            AND APD.EMPRESA_COD     = :idEmpresa
            AND APD.ESTADO          = :estado ";

            $strOrderBy = " ORDER BY APD.VALOR1, APD.VALOR2 ";

            $objQuery->setParameter("parametroId",      $arrayParametro['idParametroCab']);
            $objQuery->setParameter("idTarea",          $arrayParametro['idTarea']);
            $objQuery->setParameter("idDepartamento",   $arrayParametro['idDepartamento']);
            $objQuery->setParameter("idEmpresa",        $arrayParametro['idEmpresa']);
            $objQuery->setParameter("estado",           'Activo');

            $objRsm->addScalarResult('VALOR1',  'valor1',   'string');
            $objRsm->addScalarResult('VALOR2',  'valor2',   'string');

            $objQuery->setSQL($strSelect.$strOrderBy);    
            $arrayResultado = $objQuery->getArrayResult();

            $arrayFinish = [];

            if(!empty($arrayResultado))
            {
                for ($intPosicion = 0; $intPosicion < count($arrayResultado); $intPosicion++)
                {
                $arrayData[$arrayResultado[$intPosicion]['valor1']][]=
                array('nombreHijo' => $arrayResultado[$intPosicion]['valor2']);
                }

                foreach($arrayData as $strEtiqueta=>$arrayItem)
                {
                    $arrayChildrens=[];
                    foreach($arrayItem as $arrayValue)
                    {
                        $arrayChildrens[] = ['nombreHijo' => $arrayValue['nombreHijo']];
                    }
                    
                    $arrayFinish[]=
                    [
                        'nombreCategoria'=>$strEtiqueta, 
                        'hijosCategoria'=> $arrayChildrens
                    ];
                }
            }
        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta: ' . $e->getMessage();
            return $arrayReturn;
        } 
        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayFinish;
        return $arrayReturn;
    }
    
    
    
    /**
     * 
     * Método que obtiene motivos de fin de tarea.
     * 
     * Costo 7
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 4-09-2020
     * 
     * @param string $arrayParametro
     * @return $arrayReturn array respuesta.
     * 
     *  
     * @param string $arrayParametro
     * @return $arrayReturn array respuesta.
     * 
     */
    public function getMotivosFinTarea($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT 
                          ID_MOTIVO, NOMBRE_MOTIVO  
                          FROM 
                          DB_GENERAL.ADMI_MOTIVO 
                          WHERE ID_MOTIVO IN (
                          SELECT VALOR4 FROM ADMI_PARAMETRO_DET WHERE
                          PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                          WHERE NOMBRE_PARAMETRO = 'MOTIVOS_CATEGORIA_DE_TAREA')
                          AND VALOR1 = :strNombreCategoria 
                          AND VALOR2 = :strNombreHijo AND VALOR3 = :strNumeroTarea) ";

            $objQuery->setParameter("strMotivoCategoriaTarea",  $arrayParametro['strMotivoCategoriaTarea']);    
                                                
            $objQuery->setParameter("strNombreCategoria",       $arrayParametro['strNombreCategoria']);
            $objQuery->setParameter("strNombreHijo",            $arrayParametro['strNombreHijo']);
            $objQuery->setParameter("strNumeroTarea",           $arrayParametro['strNumeroTarea']);

            $objRsm->addScalarResult('ID_MOTIVO',      'id_motivo',       'integer');
            $objRsm->addScalarResult('NOMBRE_MOTIVO',  'nombre_motivo',   'string');

            $objQuery->setSQL($strSelect);    
            $arrayResultado = $objQuery->getArrayResult();
           
        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta: ' . $e->getMessage();
            return $arrayReturn;
        } 
        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayResultado;
        return $arrayReturn;
    }

    /**
     * 
     * Metodo encargado de retornar los datos necesarios 
     * para llenar el grid en la administracion de Holding
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 12-10-2020
     * 
     * 
    * @param array $arrayParametros [ 'id_parametro_det' => 'id', 
    *                                 'descripcion'      => 'descripcion del parametro', 
    *                                 'nombres'          => 'nombre del vendedor', 
    *                                 'apellidos'        => 'apellido del vendedor', 
    *                                 'valor1'           => 'Valor3 del detalle del parámetro', 
    *                                 'valor2'           => 'Valor2 del detalle del parámetro', 
    *                                 'valor3'           => 'Valor3 del detalle del parámetro',
    *                                 'estado'           => 'estado',  ]

     * @return array $objResultado ['encontrados', 'total']
     */    
    public function generarJsonHolding($arrayParametro)
    {        
        $arrayEncontrados = array();
        $arrayRegistros   = $this->getRegistrosHolding($arrayParametro);
    
        
        if ( $arrayRegistros ) 
        {
            $intTotal = count($arrayRegistros);
            foreach ($arrayRegistros as $data)
            {                        
                $arrayEncontrados[]=array('id_parametro_det' => $data['id'],
                                         'Nombre'            => ucwords(strtolower(trim($data['Nombre']))),                                         
                                         'Identificacion'    => ucwords(strtolower(trim($data['Identificacion']))),
                                         'Estado'            => ucwords(strtolower(trim($data['Estado']))),  
                                         'Vendedor'          => ucwords(strtolower(trim($data['Nombres'].' '.$data['Apellidos']))),
                                         'action1'           => 'button-grid-show',
                                         'action2'           => 
                                             (strtolower(trim($data['ESTADO']))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3'           => 
                                             (strtolower(trim($data['ESTADO']))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if( $intTotal == 0 )
            {
                $objResultado= array('total' => 1 ,
                                 'encontrados' => array('id_caracteristica' => 0 , 'tipo' => 'Ninguno', 
                                  'descripcion_caracteristica' => 'Ninguno', 'caracteristica_id' => 0 , 
                                     'caracteristica_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objResultado = json_encode( $objResultado);
                return $objResultado;
            }
            else
            {
                $objData =json_encode($arrayEncontrados);
                $objResultado= '{"total":"'.$intTotal.'","encontrados":'.$objData.'}';
                return $objResultado;
            }
        }
        else
        {
            $objResultado= '{"total":"0","encontrados":[]}';
            return $objResultado;
        }
        
    }
    /**
     * 
     * Metodo encargado de retornar los datos necesarios 
     * para generar el json en la administracion de Holding
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 12-10-2020
     * 
     * Costo 5
     * 
     * @param array $arrayParametros [ 'NombreRazon'     => 'nombre a buscar', 
     *                                 'Identificacion'   => 'identificación', 
     *                                 'Estado'      => 'estado'       
     *                                ]
     * @return array $objResultado ['encontrados', 'total']
     */            
    public function getRegistrosHolding($arrayParametro)
    {
        $arrayResultado = array();
        $objQueryData   = $this->_em->createQuery();
        $strSelectC     = "SELECT  apd.id, apd.valor1 Nombre, apd.valor2 Identificacion, apd.estado Estado,"
                                            . " iper.nombres AS Nombres , iper.apellidos As Apellidos ";        
        try
        {

            $strFrom = "From schemaBundle:AdmiParametroCab apc, "
                         . "schemaBundle:AdmiParametroDet apd, "
                         . "schemaBundle:InfoPersona iper ";

            $strWhere = " where 
                         apc.nombreParametro = :Parametro and 
                         apc.id = apd.parametroId and 
                         apd.valor3 = iper.login
                         ";
            $objQueryData->setParameter("Parametro", $arrayParametro['NombrePara']);
            
            if($arrayParametro['Estado'] != "")
            {
                $strWhere .= " AND apd.estado = :Estado";
                $objQueryData->setParameter("Estado", $arrayParametro['Estado']);
            }
            if($arrayParametro['NombreRazon'] != "")
            {
               $strWhere .= " AND apd.valor1 LIKE :Nombre";
               $objQueryData->setParameter('Nombre', '%'.$arrayParametro['NombreRazon'].'%');
            }
            
            if($arrayParametro['Identificacion'] != "")
            {
               $strWhere .= " AND apd.valor2 = :Indentificacion" ;
               $objQueryData->setParameter("Indentificacion", $arrayParametro['Identificacion']);
            }
            

            $objQueryData->setDQL($strSelectC . $strFrom .$strWhere);
            $arrayResultado = $objQueryData->getArrayResult();

        }
        catch(\Exception $ex)
        {
            $arrayResultado['error'] = $ex->getMessage();
        }
        
        return $arrayResultado;

    } 

    /**
     * 
     * Metodo que obtiene los campos de la tabla DB_TOKENSECURITY.APPLICATION por nombre
     * 
     * Costo (4)
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.0 06-01-2021
     * 
     * @param string $arrayParametro
     * @return $arrayReturn
     */
    public function getDataTokenApplication($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT TSA.ID_APPLICATION, TSA.NAME, TSA.STATUS, TSA.EXPIRED_TIME FROM DB_TOKENSECURITY.APPLICATION TSA ";

            $strWhere = " WHERE TSA.NAME = :nameApplication ";

            $objQuery->setParameter("nameApplication",  $arrayParametro['nameApplication']);

            $objRsm->addScalarResult('ID_APPLICATION',  'idApplication',    'string');
            $objRsm->addScalarResult('NAME',            'name',             'string');
            $objRsm->addScalarResult('STATUS',          'status',           'string');
            $objRsm->addScalarResult('EXPIRED_TIME',    'expiredTime',      'string');


            $objQuery->setSQL($strSelect.$strWhere);    
            $arrayResultado = $objQuery->getArrayResult();
        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta.';
            return $arrayReturn;
        } 

        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayResultado;
        return $arrayReturn;
    }

    /**
     * 
     * Método que obtiene parametros para el reporte de Cartera
     * 
     * Costo 7
     * 
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 1.0 22-03-2021
     * 
     * @param string $arrayParametro
     * @return $arrayReturn array respuesta.
     * 
     */
    public function gePathReporteCartera($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT 
                          VALOR1
                          FROM 
                          db_general.admi_parametro_DET 
                          WHERE
                          PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                          WHERE NOMBRE_PARAMETRO = 'REPORTE_CARTERA')
                          AND DESCRIPCION = 'REPORTES_DISPONIBLES'
                          AND EMPRESA_COD = :strEmpresaCod";

                                                
            $objQuery->setParameter("strEmpresaCod",$arrayParametro['strCodEmpresa']);
            $objRsm->addScalarResult('VALOR1',  'path',   'string');

            $objQuery->setSQL($strSelect);    
            $arrayResultado = $objQuery->getArrayResult();
           
        }
        catch(\Exception $e)
        {
            $arrayResultado="";
        } 
        return $arrayResultado;
    }

    /**
     * 
     * Documentación para el método 'getArrayParametrosDetalle'.
     * 
     * Método que general que retorna los parametros según el nombre y el modulo.
     * Registrados en la tabla ADMI_PARAMETRO_DET
     * 
     * @param array $arrayParametros ['nombreParametro'     => 'nombre a buscar',
     *                                'modulo'              => 'Modulo al que pertenece',
     *                                'proceso'
     *                                'descripcion',
     *                                'valor1',
     *                                'valor2',
     *                                'valor3',
     *                                'valor4',
     *                                'valor5',
     *                                'empresaCod',
     *                                'strLlave'            => 'nombre de la columna que va a retornar el array',
     *                                'strOrderBy',
     *                                'strValor6',
     *                                'strValor7'
     *                                ]
     *
     * @return array.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 07-10-2021
     * 
     */
    public function getArrayParametrosDetalle($arrayParametros)
    {
      $strLlave = $arrayParametros['strLlave'];
      if(!isset($arrayParametros['strLlave']) || empty($arrayParametros['strLlave']))
      {
        return [];
      }
        $objEstados     = $this->get(
          $arrayParametros['nombreParametro'], 
          $arrayParametros['modulo'], 
          $arrayParametros['proceso'], 
          $arrayParametros['descripcion'], 
          $arrayParametros['valor1'], 
          $arrayParametros['valor2'], 
          $arrayParametros['valor3'], 
          $arrayParametros['valor4'], 
          $arrayParametros['valor5'], 
          $arrayParametros['empresaCod'],
          $arrayParametros['strOrderBy'],
          $arrayParametros['strValor6'],
          $arrayParametros['strValor7']);
        $arrayEstados = array();

        foreach($objEstados as $entityEstado)
        {
            $arrayEstados[] = $entityEstado[$strLlave];
        }
        return $arrayEstados;
    }
     
    /* getUsoSubred
     *
     * Método que retornará los usos de la Subred por Pe para la función getJsonUsoSubred  
     * 
     * @return array $arrayResultados ['registros', 'total']
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */
    public function getUsoSubred( $arrayParametros )
    {
        $objRsm            = new ResultSetMappingBuilder($this->_em);
        $strQuery          = $this->_em->createNativeQuery(null, $objRsm);
        $strStart          = $arrayParametros['start'];
        $strLlimit          = $arrayParametros['limit'];
             
        $strSql = "SELECT 
                        VALOR1
                        FROM 
                        db_general.admi_parametro_DET 
                        WHERE
                        PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                        WHERE NOMBRE_PARAMETRO = 'TIPO_USO')
                        AND DESCRIPCION = 'TIPOS DE USO EN SUBREDES POR PE' 
                        AND ESTADO = :estadoElemento ";
                
        $strQuery->setParameter("estadoElemento", 'Activo');
        $strSql.= " ORDER BY FE_CREACION DESC ";
         
        
        $objRsm->addScalarResult(strtoupper('VALOR1'), 'descripcion', 'string');
       

        $strQuery->setSQL($strSql);

        $entitySolicitudes = $strQuery->getResult();
        $arrayTotalSol = count($entitySolicitudes);

        $objEncontrados = array_slice($entitySolicitudes, $strStart, $strLlimit);

        $arraySolicitudes = array();
        if($objEncontrados)
        {
            foreach($objEncontrados as $objRegistro)
            {
                $arraySolicitudes[] = array(
                                      
                    'descripcion'          => $objRegistro['descripcion']);
            }
        }

        $arrayResultado['registros'] = $arraySolicitudes;
        $arrayResultado['total'] = $arrayTotalSol;
        return $arrayResultado;
    }
    
    /**
     * getJsonUsoSubred
     *
     * Método que retornará los usos de la Subred por Pe   
     * 
     * @return array $data Json
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */
    public function getJsonUsoSubred( $arrayParametros )
    {
        
        $arrayRespSol = $this->getUsoSubred($arrayParametros);

        if($arrayRespSol)
        {
            $arrayData = '{"total":"' . $arrayRespSol['total'] . '","encontrados":' . json_encode($arrayRespSol['registros']) . '}';
        }
        else
        {
            $arrayData = '{"total":"0","encontrados":[]}';
        }
        return $arrayData;
    }
   

    /**
     * getTipoSubred
     *
     * Método que retornará los tipos de la Subred por Pe para la función getJsonTipoSubred  
     * 
     * @return array $arrayResultados ['registros', 'total']
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */
    public function getTipoSubred( $arrayParametros )
    {
        $objRsm            = new ResultSetMappingBuilder($this->_em);
        $strQuery          = $this->_em->createNativeQuery(null, $objRsm);
        $strStart          = $arrayParametros['start'];
        $strLlimit          = $arrayParametros['limit'];
             
        $strSql = "SELECT 
                        VALOR1
                        FROM 
                        db_general.admi_parametro_DET 
                        WHERE
                        PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                        WHERE NOMBRE_PARAMETRO = 'TIPO_RED')
                        AND DESCRIPCION = 'TIPOS DE RED SUBREDES POR PE' 
                        AND ESTADO = :estadoElemento ";
                
        $strQuery->setParameter("estadoElemento", 'Activo');
        $strSql.= " ORDER BY FE_CREACION DESC ";
         
        
        $objRsm->addScalarResult(strtoupper('VALOR1'), 'descripcion', 'string');
       

        $strQuery->setSQL($strSql);

        $entitySolicitudes = $strQuery->getResult();
        $arrayTotalSol = count($entitySolicitudes);

        $objEncontrados = array_slice($entitySolicitudes, $strStart, $strLlimit);

        $arraySolicitudes = array();
        if($objEncontrados)
        {
            foreach($objEncontrados as $objRegistro)
            {
                $arraySolicitudes[] = array(
                                      
                    'descripcion_tipo'          => $objRegistro['descripcion']);
            }
        }

        $arrayResultado['registros'] = $arraySolicitudes;
        $arrayResultado['total'] = $arrayTotalSol;
        return $arrayResultado;
    }
    
    /**
     * getJsonTipoSubred
     *
     * Método que retornará los tipos de la Subred por Pe   
     * 
     * @return array $data Json
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */

    
    public function getJsonTipoSubred( $arrayParametros )
    {
        
        $arrayRespSol = $this->getTipoSubred($arrayParametros);

        if($arrayRespSol)
        {
            $arrayData = '{"total":"' . $arrayRespSol['total'] . '","encontrados":' . json_encode($arrayRespSol['registros']) . '}';
        }
        else
        {
            $arrayData = '{"total":"0","encontrados":[]}';
        }
        return $arrayData;
    }

    /**
     * getEstadoSubred
     *
     * Método que retornará la data para la función getJsonEstadoSubred  
     * 
     * @return array $arrayResultados ['registros', 'total']
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 22-09-2021
     */
    public function getEstadoSubred( $arrayParametros )
    {
        $objRsm            = new ResultSetMappingBuilder($this->_em);
        $strQuery          = $this->_em->createNativeQuery(null, $objRsm);
        $strStart          = $arrayParametros['start'];
        $strLlimit          = $arrayParametros['limit'];
             
        $strSql = "SELECT 
                        VALOR1
                        FROM 
                        db_general.admi_parametro_DET 
                        WHERE
                        PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                        WHERE NOMBRE_PARAMETRO = 'ESTADOS_SUBRED_PE')
                        AND DESCRIPCION = 'ESTADOS SUBREDES POR PE' 
                        AND ESTADO = :estadoElemento ";
                
        $strQuery->setParameter("estadoElemento", 'Activo');
        $strSql.= " ORDER BY FE_CREACION DESC ";
         
        
        $objRsm->addScalarResult(strtoupper('VALOR1'), 'descripcion', 'string');
       

        $strQuery->setSQL($strSql);

        $entitySolicitudes = $strQuery->getResult();
        $arrayTotalSol = count($entitySolicitudes);

        $objEncontrados = array_slice($entitySolicitudes, $strStart, $strLlimit);

        $arraySolicitudes = array();
        if($objEncontrados)
        {
            foreach($objEncontrados as $objRegistro)
            {
                $arraySolicitudes[] = array(
                                      
                    'descripcion_estado'          => $objRegistro['descripcion']);
            }
        }

        $arrayResultado['registros'] = $arraySolicitudes;
        $arrayResultado['total'] = $arrayTotalSol;
        return $arrayResultado;
    }
    
    /**
     * getJsonEstadoSubred
     *
     * Método que retornará los estados de la Subred   
     * 
     * @return array $data Json
     *
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 22-09-2021
     */

  
    public function getJsonEstadoSubred( $arrayParametros )
    {
        
        $arrayRespSol = $this->getEstadoSubred($arrayParametros);

        if($arrayRespSol)
        {
            $arrayData = '{"total":"' . $arrayRespSol['total'] . '","encontrados":' . json_encode($arrayRespSol['registros']) . '}';
        }
        else
        {
            $arrayData = '{"total":"0","encontrados":[]}';
        }
        return $arrayData;
    }
    

    /*
    * @author Pedro Velez <psvelez@telconet.ec>
    * @version 1.0 27-08-2021
    * Se crea funcion para obtener la hipotesis final del 
    * cierre de caso automatico Hal
    * 
    * costo 5
    * 
    * @param string $arrayParametro
    * @return $arrayReturn array respuesta.
    * 
    */
    public function getHipotesisFinCaso($arrayParametro)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $strSelect = "SELECT S.ID_HIPOTESIS, S.NOMBRE_HIPOTESIS  
                           FROM DB_SOPORTE.ADMI_HIPOTESIS S 
                          WHERE S.ID_HIPOTESIS IN (
                                         SELECT VALOR4 
                                           FROM ADMI_PARAMETRO_DET 
                                          WHERE PARAMETRO_ID = (
                                                      SELECT ID_PARAMETRO 
                                                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                      WHERE NOMBRE_PARAMETRO = 'MOTIVOS_CATEGORIA_FIN_CASO' 
                                                               )
                                            AND VALOR1 = :strNombreCategoria 
                                            AND VALOR2 = :strNombreHijo 
                                            AND VALOR3 = :strNumeroTarea
                                                  )";    
                                                
            $objQuery->setParameter("strNombreCategoria",       $arrayParametro['strNombreCategoria']);
            $objQuery->setParameter("strNombreHijo",            $arrayParametro['strNombreHijo']);
            $objQuery->setParameter("strNumeroTarea",           $arrayParametro['strNumeroTarea']);

            $objRsm->addScalarResult('ID_HIPOTESIS',      'id_motivo',       'integer');
            $objRsm->addScalarResult('NOMBRE_HIPOTESIS',  'nombre_motivo',   'string');

            $objQuery->setSQL($strSelect);    
            $arrayResultado = $objQuery->getArrayResult();
           
        }
        catch(\Exception $e)
        {
            $arrayReturn['status']       = 'ERROR';
            $arrayReturn['respuesta']    = 'Error al realizar consulta: ' . $e->getMessage();
            return $arrayReturn;
        } 
        $arrayReturn['status']       = 'OK';
        $arrayReturn['respuesta']    = $arrayResultado;
        return $arrayReturn;
    }

/*
* Método extraído de librería oficial de PHP versión  >= 5.5.0
* Devuelve los valores de una sola columna del array de entrada
*
* @author Joel Muñoz Moran <jrmunoz@telconet.ec>
* @version 1.0 - 28-07-2022
*
* @param  Array $input
* @param  String $columnKey
* @param  String $indexKey
* @return Array $array
*/
    public function array_column(array $arrayInput, $strColumnKey, $intIndexKey = null)
    {
        $arrayResponse = array();
        foreach ($arrayInput as $arrayValue)
        {
            if (!isset($arrayValue[$strColumnKey]))
            {
                trigger_error("Key \"$strColumnKey\" does not exist in array");
                return false;
            }

            if (is_null($intIndexKey))
            {
                $arrayResponse[] = $arrayValue[$strColumnKey];
            }
            else
            {
                if (!isset($arrayValue[$intIndexKey]))
                {
                    trigger_error("Key \"$intIndexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($arrayValue[$intIndexKey]))
                {
                    trigger_error("Key \"$intIndexKey\" does not contain scalar value");
                    return false;
                }
                $arrayResponse[$arrayValue[$intIndexKey]] = $arrayValue[$strColumnKey];
            }
        }

        return $arrayResponse;
    }

    /*
     * findAccesoNodoByParam
     *
     * Método encargado de obtener el parametro detalle de 'PARAMETROS_ACCESO_NODOS'.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.0 01-04-2021
     * 
     */
    public function findAccesoNodoByParam($strValue)
    {
        $objQB = $this->createQueryBuilder('apd');
        $objQB
            ->innerJoin('apd.parametroId', 'apc')
            ->andWhere('apc.nombreParametro = :nombreParametro')
            ->setParameter('nombreParametro', 'PARAMETROS_ACCESO_NODOS');

        $objQB
            ->andWhere('apd.valor1 = :valor1')
            ->setParameter('valor1', $strValue);

        return $objQB
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleResult();
    }
}
