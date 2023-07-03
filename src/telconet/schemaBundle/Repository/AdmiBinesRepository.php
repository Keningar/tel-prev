<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiBinesRepository extends EntityRepository
{
    /**
     * Documentación para el método 'findBinValido'.
     *
     * Retorna si el bin ingresado de la tarjeta de credito es valido
     *
     * @param mixed $bancoTipoCuentaId Banco tipo cuenta para la validacion
     * @param mixed $binTarjeta        Digitos de la tarjeta a verificar 
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 14-04-2015
     */
    public function findBinValido($bancoTipoCuentaId,$binTarjeta)
    {
        $query = $this->_em->createQuery();
        
        $dqlSelectCount="SELECT 
                            count(ab.id) 
                        FROM 
                            schemaBundle:AdmiBines ab
                        WHERE 
                            ab.bancoTipoCuentaId= :bancoTipoCuentaId
                            AND ab.estado= :estado
                            AND (ab.binAntiguo= :binTarjeta OR ab.binNuevo= :binTarjeta)";
        
        $query->setParameter('bancoTipoCuentaId', $bancoTipoCuentaId);
        $query->setParameter('binTarjeta', $binTarjeta);
        $query->setParameter('estado', 'Activo');
        $query->setDQL($dqlSelectCount);
        $intTotal= $query->getSingleScalarResult();
        
        return $intTotal;
    }

     /**
     * Documentación para el método 'getListaBines'.
     *
     * Retorna si una lista de bines por los filtros de estado o código de bin.
     *
     * @param mixed $estado Banco tipo cuenta para la validacion
     * @param mixed $bin       código de BIN.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-08-2015, 1.1 27-08-2015
     */
    public function getListaBines($arrayParametros)
    {
        try
        {
            $arrayParametros['strTipoQuery']  = 'count';
            $objBinesCountNQuery = $this->getListaBinesInterno($arrayParametros);

            if(empty($objBinesCountNQuery['strMensajeError']))
            {
                $intTotalRegistros                  = $objBinesCountNQuery['objQuery']->getSingleScalarResult();
                $arrayParametros['strTipoQuery']  = 'query';
                //Obtiene la data del query formado para obtener los bines
                $objBinNQuery            = $this->getListaBinesInterno($arrayParametros);
                if(empty($objBinNQuery['strMensajeError']))
                {
                    //Pregunta si el limite es > 0
                    if($arrayParametros['intLimit'] > 0)
                    {
                        $objBinNQuery['objQuery']->setSQL('SELECT a.*, rownum AS intDocRowNum FROM (' 
                                                                    . $objBinNQuery['objQuery']->getSQL() 
                                                                    . ') a WHERE rownum <= :intDocLimit');
                        $objBinNQuery['objQuery']->setParameter('intDocLimit', $arrayParametros['intLimit'] + $arrayParametros['intStart']);

                        if($arrayParametros['intStart'] > 0)
                        {
                            
                            $objBinNQuery['objQuery']->setSQL('SELECT * FROM (' 
                                                                        . $objBinNQuery['objQuery']->getSQL() 
                                                                        . ') WHERE intDocRowNum >= :intDocStart');
                            $objBinNQuery['objQuery']->setParameter('intDocStart', $arrayParametros['intStart'] + 1);
                        }
                    }
                    $arrayResult['arrayRegistros'] = $objBinNQuery['objQuery']->getResult();

                    foreach($arrayResult['arrayRegistros'] as $arrayAdmiBines):
                            $arrayStoreBines[] = array('asociados_dm'   => $arrayAdmiBines['asociados'],
                                                       'id_bin_dm'      => $arrayAdmiBines['id'],
                                                       'bin_dm'         => $arrayAdmiBines['binNuevo'],
                                                       'descripcion_dm' => $arrayAdmiBines['descripcion'],
                                                       'tarjeta_dm'     => $arrayAdmiBines['tarjeta'],
                                                       'banco_dm'       => $arrayAdmiBines['banco'],
                                                       'estado_dm'      => $arrayAdmiBines['estado']);
                    endforeach;
                    $arrayResult['arrayStoreBines']    = $arrayStoreBines;
                    $arrayResult['intTotalRegistros']  = $intTotalRegistros;
                }
                else //Setea el error en una variable
                {
                    $arrayResult['strMensajeError'] = $objBinesCountNQuery['strMensajeError'];
                }
            }
            else //Setea el error en una variable
            {
                $arrayResult['strMensajeError'] = $objBinesCountNQuery['strMensajeError'];
            }//if(empty($objDocumentoCountFinanNQuery['strMensajeError']))
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existió un error en getListaBines ' . $ex->getMessage();
        }
        return $arrayResult;
    }
    
     /**
     * Documentación para el método 'getListaBines'.
     *
     * Retorna si una lista de bines por los filtros de estado o código de bin.
     *
     * @param mixed $estado Banco tipo cuenta para la validacion
     * @param mixed $bin       código de BIN.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-08-2015, 
     *          1.1 27-08-2015
     */
    public function getListaBinesInterno($arrayParametrosIn)
    {
        try
        {
            $strWhere = '';
            $rsmBuilder = new ResultSetMappingBuilder($this->_em);
            $ntvQuery = $this->_em->createNativeQuery(null, $rsmBuilder);
            
            $strBody = ' FROM  DB_GENERAL.ADMI_BINES ABI ';
            switch($arrayParametrosIn['strTipoQuery'])
            {
                case 'count' : $strSelect = 'SELECT COUNT(*) AS TOTAL';
                    $sqlCompleto = $strSelect . $strBody;
                    $rsmBuilder->addScalarResult('TOTAL', 'total', 'integer');
                    break;
                case 'query' : 
                    $strSelect = "SELECT (SELECT COUNT(ICFP.CONTRATO_ID)
                                          FROM DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO ICFP
                                          WHERE ICFP.BIN_VIRTUAL = ABI.BIN_NUEVO AND ICFP.ESTADO = :ICFPestado 
                                          ) AS ASOCIADOS,
                                          ABI.ID_BIN, ABI.BIN_NUEVO, ABI.DESCRIPCION, ABI.TARJETA, ABI.BANCO, ABI.ESTADO";
                    $ntvQuery->setParameter('ICFPestado', $arrayParametrosIn['strEstCFP']);
                    if(isset($arrayParametrosIn['intBin']) && $arrayParametrosIn['intBin'] != "")
                    {
                        $strWhere .= " where ABI.BIN_NUEVO =  :bin ";
                        $ntvQuery->setParameter('bin', $arrayParametrosIn['intBin']);
                    }
                    if(isset($arrayParametrosIn['strEstado']) && $arrayParametrosIn['strEstado'] != "" && $arrayParametrosIn['strEstado'] != 'Todos')
                    {
                        $strWhere .= $strWhere == "" ? " where ABI.ESTADO = :estado " : " and ABI.ESTADO = :estado ";
                        $ntvQuery->setParameter('estado', $arrayParametrosIn['strEstado']);
                    }
            
                    $strOrBy = ' ORDER BY ABI.BIN_NUEVO DESC ';
                               $rsmBuilder->addScalarResult('ASOCIADOS', 'asociados', 'string');
                               $rsmBuilder->addScalarResult('ID_BIN', 'id', 'string');
                               $rsmBuilder->addScalarResult('BIN_NUEVO', 'binNuevo', 'string');
                               $rsmBuilder->addScalarResult('DESCRIPCION', 'descripcion', 'string');
                               $rsmBuilder->addScalarResult('TARJETA', 'tarjeta', 'string');
                               $rsmBuilder->addScalarResult('BANCO', 'banco', 'string');
                               $rsmBuilder->addScalarResult('ESTADO', 'estado', 'string');
                    $sqlCompleto = $strSelect . $strBody . $strWhere . $strOrBy;
                    break;
            }
            $arrayResult['objQuery'] = $ntvQuery->setSQL($sqlCompleto);
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existion un error en getListaBinesInterno ' . $ex->getMessage();
        }
        return $arrayResult;
    }

     /**
     * Documentación para el método 'getBinByCodigo'.
     *
     * Retorna si un objeto tipo AdmiBin por el filtro código de bin.
     *
     * @param mixed $bin       código de BIN.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-08-2015
     */
    public function getBinByCodigo($binNuevo, $arrayEstados)
    {
        $query = $this->_em->createQuery();
        $dqlSelect = " SELECT ab FROM schemaBundle:AdmiBines ab
                                 WHERE ab.binNuevo= :binNuevo 
                                 and ab.estado in (:estados) ";
        $query->setParameter('binNuevo', $binNuevo);
        $query->setParameter('estados', $arrayEstados);
        $query->setDQL($dqlSelect);
        return $query->getOneOrNullResult();
    }
    
     /**
     * Documentación para el método 'getClientesAsociados'.
     *
     * Retorna la lista de Clientes que tiene contratos asociados al BIN
     *
     * @param mixed $arrayParametros código de BIN, Estado de la forma contacto, Id's formas contacto telefónicas.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 24-08-2015
     */
    public function getClientesAsociados($arrayParametros)
    {
        $rsmBuilder = new ResultSetMappingBuilder($this->_em);
        $ntvQuery = $this->_em->createNativeQuery(null, $rsmBuilder);
        $sqlQuery = "SELECT IPT.LOGIN, IP.IDENTIFICACION_CLIENTE,  IP.APELLIDOS,  IP.NOMBRES, 
                   REPLACE( REGEXP_REPLACE( REGEXP_REPLACE(LISTAGG (TRIM(ISV.ESTADO), ', ') 
                   WITHIN GROUP (ORDER BY ISV.ESTADO),',\s*',',') , '([^,]+)(,\\1)+', '\\1') ,',',', ') SERVICIO,
                   IOG.NOMBRE_OFICINA, 
                   CASE INSTR(REGEXP_REPLACE( LISTAGG (TRIM(IPF.VALOR), ', ') WITHIN GROUP (ORDER BY IPF.VALOR), '([^,]+)(,\\1)+', '\\1'),',', 1, 4)
                   WHEN 0 THEN REGEXP_REPLACE( LISTAGG (TRIM(IPF.VALOR), ', ') WITHIN GROUP (ORDER BY IPF.VALOR), '([^,]+)(,\\1)+', '\\1') 
                   ELSE SUBSTR( REGEXP_REPLACE( LISTAGG (TRIM(IPF.VALOR), ', ') WITHIN GROUP (ORDER BY IPF.VALOR), '([^,]+)(,\\1)+', '\\1'), 0,
                                INSTR(REGEXP_REPLACE( LISTAGG (TRIM(IPF.VALOR), ', ') WITHIN GROUP (ORDER BY IPF.VALOR), 
                                                      '([^,]+)(,\\1)+', '\\1'),',', 1, 4)-1) END TELEFONOS,
                   COALESCE(IP.DIRECCION, IP.DIRECCION_TRIBUTARIA) DIRECCION
                   FROM DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO ICFP
                   LEFT JOIN DB_COMERCIAL.INFO_CONTRATO             IC   ON IC  .ID_CONTRATO            = ICFP.CONTRATO_ID
                   LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  IPER ON IPER.ID_PERSONA_ROL         = IC  .PERSONA_EMPRESA_ROL_ID
                   LEFT JOIN DB_COMERCIAL.INFO_PUNTO                IPT  ON IPT .PERSONA_EMPRESA_ROL_ID = IPER.ID_PERSONA_ROL
                   LEFT JOIN DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO IPF  ON IPF .PUNTO_ID               = IPT .ID_PUNTO
                   LEFT JOIN DB_COMERCIAL.INFO_SERVICIO             ISV  ON ISV .PUNTO_ID               = IPT .ID_PUNTO
                   LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO        IOG  ON IOG .ID_OFICINA             = IPER.OFICINA_ID
                   LEFT JOIN DB_COMERCIAL.INFO_PERSONA              IP   ON IP  .ID_PERSONA             = IPER.PERSONA_ID
                   WHERE ICFP.BIN_VIRTUAL =:bin
                        AND IPF.ESTADO    =:estado
                   GROUP BY IPT.LOGIN, IP.IDENTIFICACION_CLIENTE, IP.NOMBRES, IP.APELLIDOS, IOG.NOMBRE_OFICINA, IP.DIRECCION, IP.DIRECCION_TRIBUTARIA
                   ORDER BY 3, 1";

        $ntvQuery->setParameter('bin', $arrayParametros["strBinNuevo"]);
        $ntvQuery->setParameter('estado', $arrayParametros["strServEstado"]);

        $rsmBuilder->addScalarResult('LOGIN',                  'login',         'string');
        $rsmBuilder->addScalarResult('IDENTIFICACION_CLIENTE', 'idCliente',     'string');
        $rsmBuilder->addScalarResult('APELLIDOS',              'apellidos',     'string');
        $rsmBuilder->addScalarResult('NOMBRES',                'nombres',       'string');
        $rsmBuilder->addScalarResult('SERVICIO',               'servicio',      'string');
        $rsmBuilder->addScalarResult('NOMBRE_OFICINA',         'nombreOficina', 'string');
        $rsmBuilder->addScalarResult('TELEFONOS',              'telefonos',     'string');
        $rsmBuilder->addScalarResult('DIRECCION',              'direccion',     'string');
        return $ntvQuery->setSQL($sqlQuery)->getResult();
    }
}
