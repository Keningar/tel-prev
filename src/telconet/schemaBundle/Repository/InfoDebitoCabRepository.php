<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDebitoCabRepository extends EntityRepository
{

    /**
    * Busca las cabeceras de los debitos segun criterios: estados y debitoGeneralId
    * @param integer $id (Id del debito general)
    * @param array $estados (Estados de las cabeceras de debitos que retorne)
    * @return object (Retorna objeto con los registros de las cabeceras de los debitos)
    */
    public function findDebitosPorDebitoGeneralIdPorEstado($id,$estados)
    {    
        $query = $this->_em->createQuery("SELECT a
        FROM
        schemaBundle:InfoDebitoCab a
        WHERE
        a.debitoGeneralId= :debitoGeneralId AND
        a.estado in(:estados) order by a.feCreacion");
        $query->setParameter('estados',$estados);
        $query->setParameter('debitoGeneralId',$id);
        $datos = $query->getResult();
        return $datos;
    }

	public function findDebitosPorDebitoGeneralIdNoInactivos($id)
    {
        $query = $this->_em->createQuery("SELECT a
        FROM 
        schemaBundle:InfoDebitoCab a
        WHERE 
        a.debitoGeneralId=$id AND a.estado in ('Activo','Pendiente','Procesado') order by a.feCreacion"); 
        $datos = $query->getResult();
        return $datos;
	}        
        
    /**
    * Busca cantidad y suma de debitos por: estados y debitoGeneralId
    * @param integer $id (Id del debito general)
    * @param string $estados (Estado de la cabecera de debitos que retorne)
    * @return array (Retorna arreglo con el valor total, valor debitado y los registros encontrados)
    */
    public function findCountDebitosPendientesPorDebitoGeneralIdPorEstado($id,$estado)
    {	
        $query = $this->_em->createQuery(
            "SELECT count(det) as total, 
                sum(det.valorTotal) as recaudado, 
                sum(det.valorDebitado) as debitado
            FROM 
            schemaBundle:InfoDebitoDet det,
            schemaBundle:InfoDebitoCab cab
            WHERE
            det.debitoCabId=cab.id AND
            cab.debitoGeneralId=$id AND
            cab.estado not in ('Inactivo') AND    
            det.estado = '$estado'"); 
        $datos = $query->getResult();
        return $datos;
	}
        

    /**
    * Metodo que permite obtener el valor de la caracteristica de la generacion automatica de debitos
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.0 04-09-2017
    * @param  integer $intEmpresaCod (Codigo de empresa)
    * @param  string  $strNombreGrupo (Nombre del grupo)
    * @return Object  $objDatos (Retorna los datos de la tabla ADMI_FORMATO_DEBITO_CARACT)
    */
    public function findDebitosAutomaticos($arrayDebitosAuto)
    {
        $objQuery = $this->_em->createQuery();
        $objDatos = null;
        $strDql =
        "SELECT a
            FROM
                schemaBundle:AdmiFormatoDebitoCaract   a,
                schemaBundle:AdmiGrupoArchivoDebitoCab b,
                schemaBundle:AdmiCaracteristica        c
            WHERE a.bancoTipoCuentaId   = b.bancoTipoCuentaId
                AND a.caracteristicaId  = c.id
                AND a.empresaCod        = :empresaCod
                AND b.empresaCod        = :empresaCod
                AND b.nombreGrupo       = :nombreGrupo
                AND a.estado            = :estado
                and b.estado            = :estado
                and c.estado            = :estado
                AND c.descripcionCaracteristica = :caracteristica";

        $objQuery->setParameter('empresaCod',     $arrayDebitosAuto["intIdEmpresa"]);
        $objQuery->setParameter('nombreGrupo',    $arrayDebitosAuto["strNombreBanco"]);
        $objQuery->setParameter('estado',         $arrayDebitosAuto["strEstado"]);
        $objQuery->setParameter('caracteristica', $arrayDebitosAuto["strCaracteristica"]);

        try
        {
            $objQuery->setDQL($strDql);
            $objDatos = $objQuery->getOneOrNullResult();
        }
        catch (\Exception $ex)
        {
            error_log("InfoDebitoCabRepository->findDebitosAutomaticos ".$ex.getMessage());
        }

        return $objDatos;
    }


    /**
     * Documentación para el método 'ejecutarDebitosPendientes'.
     *
     * Ejecuta los debitos pendientes.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-09-2017
     *
     */
    public function ejecutarDebitosPendientes()
    {
        try
        {
            $strSql = "DECLARE
                         Ln_job_exists NUMBER;
                       BEGIN

                            SELECT COUNT(*) INTO Ln_job_exists
                              FROM user_scheduler_jobs
                             WHERE job_name = 'JOB_DEBITOS_PENDIENTES';

                            IF Ln_job_exists = 1 THEN
                                DBMS_SCHEDULER.DROP_JOB(job_name => '\"DB_FINANCIERO\".\"JOB_DEBITOS_PENDIENTES\"',
                                                        defer    => false,
                                                        force    => false);
                            END IF;

                            DBMS_SCHEDULER.CREATE_JOB (job_name   => '\"DB_FINANCIERO\".\"JOB_DEBITOS_PENDIENTES\"',
                                                       job_type   => 'PLSQL_BLOCK',
                                                       job_action => 'DECLARE
                                                        BEGIN
                                                            DB_FINANCIERO.FNKG_PROCESO_MASIVO_DEB.P_PROCESAR_DEBITOS_P;
                                                        END;',
                                                       number_of_arguments => 0,
                                                       start_date          => NULL,
                                                       repeat_interval     => NULL,
                                                       end_date            => NULL,
                                                       enabled             => FALSE,
                                                       auto_drop           => TRUE,
                                                       comments => 'Proceso para ejecutar los debitos pendientes.');

                            DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '\"DB_FINANCIERO\".\"JOB_DEBITOS_PENDIENTES\"',
                                                         attribute => 'logging_level',
                                                         value     => DBMS_SCHEDULER.LOGGING_OFF);

                            DBMS_SCHEDULER.enable(name => '\"DB_FINANCIERO\".\"JOB_DEBITOS_PENDIENTES\"');

                        END;";

            $objStmt = $this->_em->getConnection()->prepare($strSql);

            $objStmt->execute();

        }
        catch (\Exception $ex)
        {
            error_log("InfoDebitoCabRepository->ejecutarDebitosPendientes ".$ex.getMessage());
            throw($ex);
        }
    }

    public function findPagosPorDebitoGeneral($idDebitoGen)
    {
        $query = $this->_em->createQuery(
            "SELECT 
            tdoc.codigoTipoDocumento, pcab.puntoId, 
            pcab.numeroPago, pcab.valorTotal, 
            bco.descripcionBanco, atc.descripcionCuenta,
            pdet.numeroCuentaBanco,pdet.referenciaId
            FROM 
            schemaBundle:InfoPagoCab pcab, schemaBundle:InfoDebitoDet ddet,
            schemaBundle:InfoDebitoCab dcab, schemaBundle:InfoPagoDet pdet, 
            schemaBundle:AdmiBancoTipoCuenta btc, 
            schemaBundle:AdmiBanco bco, schemaBundle:AdmiTipoCuenta atc,
            schemaBundle:AdmiTipoDocumentoFinanciero tdoc
            WHERE
            dcab.debitoGeneralId=$idDebitoGen 
            AND pcab.tipoDocumentoId=tdoc.id 
            AND pcab.id=pdet.pagoId 
            AND pcab.debitoDetId=ddet.id 
            AND ddet.debitoCabId=dcab.id 
            AND dcab.bancoTipoCuentaId=btc.id 
            AND btc.bancoId=bco.id 
            AND btc.tipoCuentaId = atc.id 
            AND pcab.estadoPago not in ('Anulado')"); 
        $datos = $query->getResult();
        return $datos;
    }

    /**
    * Busca las cabeceras de los debitos segun criterios: estados y debitoGeneralId
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.1 29-08-2017 Se modifica el query para obtener valores debitados sólo para los procesados.
    * @since 1.0
    * @param integer $intId (Id de la cabecera del debito)
    * @param string $strEstado (Estado de la cabecera de debitos que retorne)
    * @return array (Retorna arreglo con el valor total y los registros encontrados)
    */
    public function findCountDebitosPorDebitoCabIdPorEstado($intId, $strEstado)
    {
        $strCampo = "valorTotal";
        if ($strEstado == 'Procesado')
        {
            $strCampo = "valorDebitado";
        }
        $query = $this->_em->createQuery(
        "SELECT count(det) as total, sum(det.". $strCampo .") as recaudado
        FROM
        schemaBundle:InfoDebitoDet det,
        schemaBundle:InfoDebitoCab cab
        WHERE
        det.debitoCabId=cab.id AND
        cab.id=:debitoCabId AND
        cab.estado not in (:estadosCabecera) AND
        det.estado = :estadoDetalle");
        $estadosCabecera=array('Inactivo');
        $query->setParameter('estadosCabecera',$estadosCabecera);
        $query->setParameter('debitoCabId',$intId);
        $query->setParameter('estadoDetalle',$strEstado);
        $datos = $query->getResult();
        return $datos;
    }        
    
    /**
     * Documentación para getSumaValorTotalPagosGenerados.
     * 
     * Obtiene la suma total de los pagos generados de las tablas de pago.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 05-04-2019
     * Costo query : 12
     * @param array $arrayParametros[]                  
     *              'debitoCabId'  => Id de la cabecera del débito
     *              'strEstado'    => estado de débito
     * @return Response suma valores de cabecera de pago InfoPagoCab
     */
    public function getSumaValorTotalPagosGenerados($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $strDql   = "";
        try 
        {  
            $strDql = " SELECT "
                    . " sum(ipc.valorTotal) as suma "
                    . " FROM "
                    . " schemaBundle:InfoPagoCab   ipc, "
                    . " schemaBundle:InfoPagoDet   ipd, "
                    . " schemaBundle:InfoDebitoCab idc, "
                    . " schemaBundle:InfoDebitoDet idd, "
                    . " schemaBundle:AdmiTipoDocumentoFinanciero atdf "
                    . " WHERE "
                    . " ipd.pagoId                =  ipc.id                             AND "
                    . " ipc.debitoDetId           =  idd.id                             AND "
                    . " idd.debitoCabId           =  idc.id                             AND " 
                    . " idc.id                    =  :debitoCabId                       AND "
                    . " ipc.tipoDocumentoId       =  atdf.id                            AND "
                    . " ipd.estado                in  ('Cerrado','Pendiente')           AND "
                    . " atdf.codigoTipoDocumento  in ('PAG','ANT','ANTC','ANTS','PAGC') AND "
                    . " ipc.estadoPago            in ('Cerrado','Pendiente')            AND "
                    . " idd.estado = :strEstado ";
            
            $objQuery->setParameter('debitoCabId' ,$arrayParametros['debitoCabId']);
            $objQuery->setParameter('strEstado'   ,$arrayParametros['strEstado']);
            
            $objQuery->setDql($strDql);
            $arrayResult = $objQuery->getResult();
           
            
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
        return $arrayResult;
    }
    
    
     /**
     * Documentación para función 'getDiferenciasDebitosClientes'.
     * 
     * Obtiene listado de clientes con valores generados, debitados y las diferencias 
     * entre estos dos valores.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 10-04-2019
     * Costo query : 1021
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 12-08-2019 - Se elimina de la consulta la tabla INFO_PUNTO porque generaba duplicidad en los valores.
     * Costo query : 13 
     * 
     * @param array $arrayParametros[]                  
     *              'debitoCabId'  => Id de la cabecera del débito
     * @return Response lista de clientes con valores generados y debitados.
     */
    public function getDiferenciaDebitosClientes($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strSql      = "SELECT SUM(ipd.valor_pago) total,
                          idd.valor_total,
                          idd.valor_debitado,
                          ips.nombres ,
                          ips.apellidos ,
                          ips.razon_social ,
                          ips.identificacion_cliente,
                          iper.id_persona_rol,
                          (SELECT LISTAGG(i.login,',') WITHIN GROUP (ORDER BY i.id_punto) 
                          FROM DB_COMERCIAL.info_punto i 
                          WHERE i.persona_empresa_rol_id = iper.id_persona_rol 
                          AND i.estado in ('In-Corte','Activo','Trasladado','Cancelado')) login,
                            ips.id_persona,
                            idd.id_debito_det,
                            idd.observacion_rechazo,
                            idd.fe_creacion
                        FROM DB_FINANCIERO.INFO_DEBITO_DET idd,
                          DB_FINANCIERO.INFO_PAGO_CAB ipc,
                          DB_FINANCIERO.INFO_PAGO_DET ipd,        
                          DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL iper,
                          DB_COMERCIAL.INFO_PERSONA ips   
                        WHERE ips.id_persona          = iper.persona_id          
                        AND iper.id_persona_rol       = idd.persona_empresa_rol_id     
                        AND ipd.pago_id               = ipc.id_pago                
                        AND ipc.debito_det_id         = idd.id_debito_det          
                        AND idd.debito_cab_id         = :debitoCabId 
                        AND ipd.estado                in ('Cerrado','Pendiente')   
                        AND ipc.estado_pago           in ('Cerrado','Pendiente')   
                        AND idd.estado                = 'Procesado' 
                        GROUP BY idd.fe_creacion,
                          idd.id_debito_det, 
                          ips.nombres,
                          ips.apellidos, 
                          ips.identificacion_cliente, 
                          ips.razon_social, 
                          idd.valor_total, 
                          idd.valor_debitado, 
                          idd.observacion_rechazo, 
                          iper.id_persona_rol, 
                          ips.id_persona ";

               $objNtvQuery->setParameter('debitoCabId',$arrayParametros['debitoCabId']);

               $objNtvQuery->setSQL($strSql);
               $objRsm->addScalarResult('TOTAL', 'total', 'string'); 
               $objRsm->addScalarResult('LOGIN', 'login', 'string');    
               $objRsm->addScalarResult('NOMBRES', 'nombres', 'string'); 
               $objRsm->addScalarResult('APELLIDOS', 'apellidos', 'string');
               $objRsm->addScalarResult('ID_PERSONA', 'id_persona', 'string'); 
               $objRsm->addScalarResult('FE_CREACION', 'fe_creacion', 'string'); 
               $objRsm->addScalarResult('VALOR_TOTAL', 'valor_total', 'string');
               $objRsm->addScalarResult('RAZON_SOCIAL', 'razon_social', 'string');
               $objRsm->addScalarResult('ID_DEBITO_DET', 'id_debito_det', 'string'); 
               $objRsm->addScalarResult('ID_PERSONA_ROL', 'id_persona_rol', 'string'); 
               $objRsm->addScalarResult('VALOR_DEBITADO', 'valor_debitado', 'string');    
               $objRsm->addScalarResult('OBSERVACION_RECHAZO', 'observacion_rechazo', 'string'); 
               $objRsm->addScalarResult('IDENTIFICACION_CLIENTE', 'identificacion_cliente', 'string'); 

               $arrayResult = $objNtvQuery->getResult();

               return $arrayResult;
    }
    
    /**
     * Documentación para getSumaValorTotalProcesados.
     * 
     * Obtiene suma de valores procesados de los débitos.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 05-03-2019
     * Costo query : 24
     * @param array $arrayParametros[]                  
     *              'debitoCabId'  => Id de la cabecera del débito
     *              'strEstado'    => estado del débito
     * @return array suma de valores debitados de la tabla InfoDebitoDet
    */
    public function  getSumaValorTotalProcesados($arrayParametros)
    {      
        $objQuery = $this->_em->createQuery();
        $strDql   = "";
        try 
        {
            $strDql = " SELECT "
                     ." sum(idt.valorDebitado) as suma "
                     ." FROM "
                     ." schemaBundle:InfoDebitoCab idc, "
                     ." schemaBundle:InfoDebitoDet idt "
                     ." WHERE "
                     ." idc.id          = idt.debitoCabId AND "
                     ." idt.debitoCabId = :debitoCabId    AND "
                     ." idt.estado      = :strEstado      AND "
                     ." idc.estado      = 'Procesado' ";

            $objQuery->setParameter('debitoCabId', $arrayParametros["debitoCabId"]);
            $objQuery->setParameter('strEstado'  , $arrayParametros["strEstado"]);

            $objQuery->setDql($strDql);
            
            $arrayResult = $objQuery->getResult();
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
        return $arrayResult;
    }

    /**
     * Documentación para cuentaCabecerasPorParametros
     *
     * Obtiene la cantidad de débitos pendientes.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 27-11-2017 - Versión inicial.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.1 15-03-2019 - Se agrega nuevas condiciones en el where,
     *                           Se pregunta si tiene la caraterística 'DEBITOS PARCIALES DIARIOS'
     *                           y que no exista un Cierre Final Manual en la INFO_DEBITO_GENERAL_HISTORIAL.
     * Costo query : 149
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.2 06-05-2020 - Se agrega al query la condición de estado 'Activo' de la tabla 
     *                           'DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT'.
     * Costo query : 153
     *
     */
    public function cuentaCabecerasPorParametros($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            $strSql  = " SELECT COUNT (*) TOTAL "
                     . " FROM  "
                     . " DB_FINANCIERO.INFO_DEBITO_CAB IDC,"
                     . " DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT AFDC, "
                     . " DB_COMERCIAL.ADMI_CARACTERISTICA AC "
                     . " WHERE "
                     . " IDC.DEBITO_GENERAL_ID         = :intDebitoGeneralId          AND "
                     . " NOT EXISTS(SELECT 'X' "
                     . " FROM "
                     . " DB_FINANCIERO.INFO_DEBITO_GENERAL_HISTORIAL IDGH "
                     . " WHERE "
                     . " IDGH.DEBITO_GENERAL_ID        = IDC.DEBITO_GENERAL_ID       AND "
                     . " (IDGH.OBSERVACION             = 'Cierre final manual'       OR  "
                     . " IDGH.OBSERVACION              = 'Cierre manual por debitos parciales')) AND "
                     . " IDC.VALOR_TOTAL > 0 AND " 
                     . " IDC.BANCO_TIPO_CUENTA_ID      = AFDC.BANCO_TIPO_CUENTA_ID   AND "
                     . " AFDC.CARACTERISTICA_ID        = AC.ID_CARACTERISTICA        AND "
                     . " AFDC.ESTADO                   = 'Activo'                    AND "
                     . " AC.DESCRIPCION_CARACTERISTICA = 'DEBITOS PARCIALES DIARIOS' ";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);

           $objQuery->setParameter('intDebitoGeneralId', $arrayParametros['intDebitoGeneralId']);
           $objRsm->addScalarResult('TOTAL', 'total', 'integer');     
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
    
    
     /**
     * Documentación para getObtieneCaracteristica.
     *
     * Obtiene la característica asignada a un banco mediante el id débito general
     * enviado como parámetro.
     * 
     * @param  array $arrayParametros (Código del  débito general,Característica asociada al banco)
     * @return array (Retorna arreglo con el valor total de los registros encontrados)
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 21-03-2019 - Versión inicial.
     * Costo query : 8
     */
    public function getObtieneCaracteristica($arrayParametros)
    {
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            $strSql  = " SELECT COUNT (*) TOTAL "
                     . " FROM  "
                     . " DB_FINANCIERO.INFO_DEBITO_CAB IDC,"
                     . " DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT AFDC, "
                     . " DB_COMERCIAL.ADMI_CARACTERISTICA AC "
                     . " WHERE "
                     . " IDC.DEBITO_GENERAL_ID         = :intDebitoGeneralId       AND "
                     . " IDC.BANCO_TIPO_CUENTA_ID      = AFDC.BANCO_TIPO_CUENTA_ID AND "
                     . " AFDC.CARACTERISTICA_ID        = AC.ID_CARACTERISTICA      AND "
                     . " AC.DESCRIPCION_CARACTERISTICA = :strCaracteristica ";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);

           $objQuery->setParameter('intDebitoGeneralId', $arrayParametros['idDebGen']);
           $objQuery->setParameter('strCaracteristica',  $arrayParametros['strCaracteristica']);
           $objRsm->addScalarResult('TOTAL', 'total', 'integer');     
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();
           
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }

    /**
     * Metodo que permite obtener si se ha generado un débito en un rango de tiempo de 30 días
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-08-2016
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 05-11-2017 - Se agrega el filtro para el CICLO_ID.
     *                         - Se modifican los parámetros de la función.
     *
     * @param string $strEstado
     * @param int    $intGrupoDebitoCab
     * @param int    $intIdEmpresa
     * @param array  $intIdGrupoDebitoDet
     * @return array $arrayResultado [ resultado ]
     */
    public function validadorDebitoCabExistente($arrayParametros)
    {
        $arrayResultado       = array('resultado' => null);
        $intCicloId           = $arrayParametros['intCicloId'];
        $strEstado            = $arrayParametros['strEstado'];
        $intGrupoDebitoCab    = $arrayParametros['intGrupoDebitoCab'];
        $intIdEmpresa         = $arrayParametros['intIdEmpresa'];
        $intIdGrupoDebitoDet  = $arrayParametros['intIdGrupoDebitoDet'];
        $strWhereCicloId      = isset($intCicloId) ? ' AND IDG.CICLO_ID        = :intCicloId ' : '';
        $boolByGrupoDebitoDet = ( !empty($intIdGrupoDebitoDet) ) ? true : false;
            
        try
        {
            if( !empty($strEstado) && !empty($intGrupoDebitoCab) && !empty($intIdEmpresa) )
            {
                $rsm   = new ResultSetMappingBuilder($this->_em);	      
                $query = $this->_em->createNativeQuery(null, $rsm);

                $strSelect = "SELECT AGADC2.NOMBRE_GRUPO AS DEBITO, IDC2.FE_CREACION ";
                $strFrom   = "FROM DB_FINANCIERO.INFO_DEBITO_GENERAL IDG2 ";
                $strJoin   = "";
                $strWhere  = "WHERE IDG2.ID_DEBITO_GENERAL IS NOT NULL ";
                
                if( $boolByGrupoDebitoDet )
                {
                    $strSelect  = "SELECT CONCAT(AB2.DESCRIPCION_BANCO, CONCAT(' ', ATC2.DESCRIPCION_CUENTA)) AS DEBITO, IDC2.FE_CREACION ";
                    $strJoin   .= "JOIN DB_FINANCIERO.INFO_DEBITO_CAB IDC2
                                   ON IDG2.ID_DEBITO_GENERAL = IDC2.DEBITO_GENERAL_ID
                                   JOIN ADMI_GRUPO_ARCHIVO_DEBITO_DET AGADD2
                                   ON AGADD2.BANCO_TIPO_CUENTA_ID = IDC2.BANCO_TIPO_CUENTA_ID
                                   JOIN DB_GENERAL.ADMI_BANCO_TIPO_CUENTA ABTC2
                                   ON ABTC2.ID_BANCO_TIPO_CUENTA = IDC2.BANCO_TIPO_CUENTA_ID
                                   JOIN DB_GENERAL.ADMI_BANCO AB2
                                   ON AB2.ID_BANCO = ABTC2.BANCO_ID
                                   JOIN DB_GENERAL.ADMI_TIPO_CUENTA ATC2
                                   ON ATC2.ID_TIPO_CUENTA = ABTC2.TIPO_CUENTA_ID ";
                    $strWhere  .= "AND AGADD2.ID_GRUPO_DEBITO_DET = :intIdGrupoDebitoDet 
                                   AND IDG2.ID_DEBITO_GENERAL = (
                                                                    SELECT MAX(IDG.ID_DEBITO_GENERAL)
                                                                    FROM DB_FINANCIERO.INFO_DEBITO_GENERAL IDG
                                                                    JOIN DB_FINANCIERO.INFO_DEBITO_CAB IDC
                                                                    ON IDG.ID_DEBITO_GENERAL = IDC.DEBITO_GENERAL_ID
                                                                    JOIN ADMI_GRUPO_ARCHIVO_DEBITO_DET AGADD
                                                                    ON AGADD.BANCO_TIPO_CUENTA_ID = IDC.BANCO_TIPO_CUENTA_ID
                                                                    WHERE AGADD.ID_GRUPO_DEBITO_DET = :intIdGrupoDebitoDet "; 
                    
                    $query->setParameter('intIdGrupoDebitoDet', $intIdGrupoDebitoDet); 
                }
                else
                {
                    $strJoin  .= "JOIN DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB AGADC2
                                  ON IDG2.GRUPO_DEBITO_ID = AGADC2.ID_GRUPO_DEBITO
                                  JOIN DB_FINANCIERO.INFO_DEBITO_CAB IDC2
                                  ON IDC2.DEBITO_GENERAL_ID = IDG2.ID_DEBITO_GENERAL ";
                    $strWhere .= "AND ROWNUM = 1
                                  AND IDG2.ID_DEBITO_GENERAL = (
                                                                   SELECT MAX(IDG.ID_DEBITO_GENERAL)
                                                                   FROM DB_FINANCIERO.INFO_DEBITO_GENERAL IDG
                                                                   JOIN DB_FINANCIERO.INFO_DEBITO_CAB IDC
                                                                   ON IDG.ID_DEBITO_GENERAL = IDC.DEBITO_GENERAL_ID
                                                                   WHERE IDG.ID_DEBITO_GENERAL IS NOT NULL ";
                }
                
                $strWhere .= "AND IDC.ESTADO          = :strEstado
                              AND IDC.FE_CREACION     >= ADD_MONTHS(SYSDATE, -1)
                              AND IDC.FE_CREACION     < (SYSDATE             +1)
                              AND IDG.GRUPO_DEBITO_ID = :intGrupoDebitoCab " . $strWhereCicloId .
                            " AND IDC.EMPRESA_ID      = :intIdEmpresa ) ";
                

                $rsm->addScalarResult('DEBITO',      'debito',     'string');
                $rsm->addScalarResult('FE_CREACION', 'feCreacion', 'string');

                $query->setParameter('strEstado',         $strEstado); 
                $query->setParameter('intGrupoDebitoCab', $intGrupoDebitoCab); 
                $query->setParameter('intIdEmpresa',      $intIdEmpresa);   
                if( isset($intCicloId))
                {
                    $query->setParameter('intCicloId',    $intCicloId);
                }

                $strSql = $strSelect.$strFrom.$strJoin.$strWhere;
                    
                $query->setSQL($strSql);	  

                $arrayResultado['resultado'] = $query->getResult();
            }//( !empty($strEstado) && !empty($intBancoTipoCuentaId) && !empty($intIdEmpresa) )
            else
            {
                throw new \Exception("No se enviaron los parámetros adecuados para realizar la consulta de validar debito existente");
            }
        }
        catch(\Exception $e)
        {
           throw($e);
        }
        
        return $arrayResultado;
    }
     
    /**
     * Documentación para getCuentaDebitoHistorialPorParametros
     *
     * Obtiene el total de registros de la tabla INFO_DEBITO_GENERAL_HISTORIAL
     * con la Observación Cierre Final Manual.
     * 
     * @param  array $arrayParametros (Código del  débito general)
     * @return array (Retorna arreglo con el valor total y los registros encontrados)
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 21-03-2019 - Versión inicial.
     * Costo query : 142
     */
    public function getCuentaDebitoHistorialPorParametros($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $strDql   = "";
         
        try
        {
            $strDql = " SELECT COUNT(idgh) as total "
                      . " FROM "
                      . " schemaBundle:InfoDebitoGeneralHistorial idgh "
                      . " WHERE "
                      . " idgh.debitoGeneralId = :intIdDebitoGeneral   AND "
                      . " (idgh.observacion    = 'Cierre final manual' OR "
                      . " idgh.observacion     = 'Cierre manual por debitos parciales') AND "
                      . " idgh.estado          = 'Activo'";

            $objQuery->setParameter("intIdDebitoGeneral", $arrayParametros['idDebGen']);
            $objQuery->setDql($strDql);
           
            $arrayResult =  $objQuery->getResult();           
        } 
        catch (\Exception $ex) 
        {
            throw($ex);        
        }
        
        return $arrayResult;
    }
    
    
    /**
     * Documentación para getTipoCuentaTarjeta.
     *
     * Obtiene el total de registros que tienen asociado el tipo de cuenta 'TARJETA'.
     *
     * @author Ricardo Robles <lcabrera@telconet.ec>
     * @version 1.0 17-07-2019 - Versión inicial.
     * 
     * Costo query : 8
     * 
     */
    public function getTipoCuentaTarjeta($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            $strSql  = " SELECT COUNT (*) TOTAL "
                     . " FROM  "
                     . " DB_FINANCIERO.info_debito_cab idc,"
                     . " DB_FINANCIERO.admi_banco_tipo_cuenta btc, "
                     . " DB_FINANCIERO.admi_tipo_cuenta atc "
                     . " WHERE idc.debito_general_id  = :intDebitoGeneralId "
                     . " AND idc.banco_tipo_cuenta_id = btc.id_banco_tipo_cuenta "
                     . " AND btc.tipo_cuenta_id       = atc.id_tipo_cuenta "
                     . " AND atc.estado               = 'Activo' "
                     . " AND btc.estado not in ('Inactivo') "
                     . " AND atc.descripcion_cuenta like '%TARJETA%'";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);

           $objQuery->setParameter('intDebitoGeneralId', $arrayParametros['intDebitoGeneralId']);
           $objRsm  ->addScalarResult('TOTAL', 'total', 'integer');     
           $objQuery->setSQL($strSql);
           
           $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
      
    /**
     * Documentación para cuentaCabecerasParametroTarjeta.
     *
     * Obtiene la cantidad de débitos pendientes y que tengan estado pendiente.
     *
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 27-11-2017 - Versión inicial.
     * 
     * Costo query : 2
     * 
     */
    public function cuentaCabecerasParametroTarjeta($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);          
            $strSql  = " SELECT COUNT (*) TOTAL "
                     . " FROM DB_FINANCIERO.INFO_DEBITO_CAB CAB "
                     . " WHERE CAB.DEBITO_GENERAL_ID = :intDebitoGeneralId "
                     . " AND CAB.VALOR_TOTAL > 0 "
                     . " AND CAB.ESTADO = :strEstado";
            
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter('intDebitoGeneralId', $arrayParametros['intDebitoGeneralId']);
            $objQuery->setParameter('strEstado',          $arrayParametros['strEstado']);
            
            $objRsm  ->addScalarResult('TOTAL', 'total', 'integer');     
            $objQuery->setSQL($strSql);
            
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
    
    /**
     * Documentación para getValidaEstadoCierre.
     *
     * Obtiene el estado del proceso de cierre de la tabla INFO_DEBITO_RESPUESTA 'S' si esta ejecutando
     * 'F' si el proceso  ha finalizado.
     * 
     * @param  array $arrayParametros (Código del  débito general)
     * @return array (Retorna el total de los registros encontrados)
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 21-03-2019 - Versión inicial.
     * Costo query : 2
     */
    public function getValidaEstadoCierre($arrayParametros)
    {
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            $strSql  = " SELECT IDR.ESTADO_CIERRE ESTADO,IDR.ID_RESPUESTA_DEBITO DEBITO "
                     . " FROM  "
                     . " DB_FINANCIERO.INFO_DEBITO_RESPUESTA IDR "
                     . " WHERE IDR.ID_RESPUESTA_DEBITO = (SELECT MAX(ID_RESPUESTA_DEBITO)
                                                          FROM  
                                                          DB_FINANCIERO.INFO_DEBITO_RESPUESTA 
                                                          WHERE DEBITO_GENERAL_ID =:intDebitoGeneralId)";
                 

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);

           $objQuery->setParameter('intDebitoGeneralId', $arrayParametros['idDebGen']);
           $objRsm->addScalarResult('ESTADO', 'estado', 'string');
           $objRsm->addScalarResult('DEBITO', 'debito', 'integer');
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();
           
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
    
    
    /**
     * Documentación para reversoProcesoDiferenciasDebitos.
     *
     * Cambia el estado a null del  debito generado en la tabla INFO_DEBITO_RESPUESTA .
     *
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 27-11-2017 - Versión inicial.
     * 
     * 
     */
    public function reversoProcesoDiferenciasDebitos($arrayParametros)
    {   
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);          
            $strSql  = " UPDATE DB_FINANCIERO.INFO_DEBITO_RESPUESTA "
                     . " SET ESTADO_CIERRE = NULL "
                     . " WHERE DEBITO_GENERAL_ID = :intDebitoGeneralId ";           
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objQuery->setParameter('intDebitoGeneralId', $arrayParametros['idDebGen']);  
            $objQuery->setSQL($strSql);
            
            $arrayResult = $objQuery->getResult();

        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
    
    /**
    * Documentación para findCountDebitosPendientesAbonados.
    *  
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 24-07-2019 Método para obtener los pendientes abonados.
    * 
    * Costo query : 11
    *   
    * @param integer $intId (Id de la cabecera del débito).
    * @param string $strEstado (Estado de la cabecera de débitos que retorne).
    * @return array (Retorna arreglo con el valor total y los registros encontrados).
    */
    public function findCountDebitosPendientesAbonados($intId, $strEstado)
    {
        $strQuery = $this->_em->createQuery(
        " SELECT count(det) as total, sum(det.valorDebitado) as recaudado
          FROM
            schemaBundle:InfoDebitoDet det,
            schemaBundle:InfoDebitoCab cab
          WHERE
            det.debitoCabId=cab.id 
          AND cab.id=:debitoCabId 
          AND cab.estado not in (:estadosCabecera) 
          AND det.valorDebitado > 0 
          AND det.estado = :estadoDetalle");
        $strEstadosCabecera = array('Inactivo');
        $strQuery->setParameter('estadosCabecera',$strEstadosCabecera);
        $strQuery->setParameter('debitoCabId',$intId);
        $strQuery->setParameter('estadoDetalle',$strEstado);
        $arrayDatos = $strQuery->getResult();
        return $arrayDatos;
    }
    
    /**
    * Documentación para findCountDebitosPendientesNoAbonados. 
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 24-07-2019 Método para obtener los pendientes no abonados.
    * 
    * Costo query : 11
    * 
    * @param integer $intId (Id de la cabecera del débito).
    * @param string $strEstado (Estado de la cabecera de débitos que retorne).
    * @return array (Retorna arreglo con el valor total y los registros encontrados).
    */
    public function findCountDebitosPendientesNoAbonados($intId, $strEstado)
    {       
        $objRsm   = new ResultSetMappingBuilder($this->_em);
        $strSql  = " SELECT COUNT(*) AS TOTAL, SUM((DET.VALOR_TOTAL - NVL(DET.VALOR_DEBITADO, 0))) AS RECAUDADO "
                    ." FROM "
                    ." DB_FINANCIERO.INFO_DEBITO_DET DET, "
                    ." DB_FINANCIERO.INFO_DEBITO_CAB CAB "
                    ." WHERE "
                    ." DET.DEBITO_CAB_ID = CAB.ID_DEBITO_CAB  "
                    ." AND CAB.ID_DEBITO_CAB =:debitoCabId "
                    ." AND CAB.ESTADO NOT IN (:estadosCabecera) "
                    ." AND NVL(det.valor_debitado, 0) >= 0 "
                    ." AND DET.ESTADO = :estadoDetalle " ;
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        $strEstadosCabecera = array('Inactivo');
        $objQuery->setParameter('estadosCabecera',$strEstadosCabecera);
        $objQuery->setParameter('debitoCabId',$intId);
        $objQuery->setParameter('estadoDetalle',$strEstado);
        $objRsm->addScalarResult('TOTAL', 'total', 'float');
        $objRsm->addScalarResult('RECAUDADO', 'recaudado', 'float');
        $objQuery->setSQL($strSql);
        $arrayResult = $objQuery->getResult();

        return $arrayResult;
    }
    
    /*
     * Documentación para función findTipoFiltroEscenarioDebito
     * 
     * Obtiene el Tipo de Escenario y Filtro según corresponda el débito.
     *
     * @author: Hector Lozano<hlozano@telconet.ec>
     * @version: 1.0 11-06-2020 
     * @param type $arrayParametros['intIdDebitoGeneral' : Id del Débito
     *                              'intIdEmpresa'       : Id de Empresa 
     *                             ]
     * @return array
     */
    public function findTipoFiltroEscenarioDebito($arrayParametros)
    {
        try
        {
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $strSql  = " SELECT DEBITO_GENERAL_ID, TIPO_ESCENARIO, FILTRO_ESCENARIO "
                        ." FROM "
                        ." DB_FINANCIERO.INFO_DEBITO_CAB "
                        ." WHERE "
                        ." DEBITO_GENERAL_ID  =:intIdDebitoGeneral "
                        ." AND EMPRESA_ID     =:intIdEmpresa "
                        ." GROUP BY DEBITO_GENERAL_ID, TIPO_ESCENARIO, FILTRO_ESCENARIO ";
            
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            
            $objQuery->setParameter('intIdDebitoGeneral',$arrayParametros['intIdDebitoGeneral']);
            $objQuery->setParameter('intIdEmpresa',$arrayParametros['intIdEmpresa']);
            
            $objRsm->addScalarResult('DEBITO_GENERAL_ID', 'debitoGeneralId', 'integer');
            $objRsm->addScalarResult('TIPO_ESCENARIO', 'tipoEscenario', 'string');
            $objRsm->addScalarResult('FILTRO_ESCENARIO', 'filtroEscenario', 'string');
            
            $objQuery->setSQL($strSql);
            $arrayResult = $objQuery->getResult();

            return $arrayResult;
            
        } 
        catch (Exception $ex) 
        {
            
            return null;

        }
        
    }
    
     /**
     * Documentación para getParametroCierre.
     *
     * Obtiene la descripción del parámetro que se registra en el proceso de cierre.
     * 
     * @param  array $arrayParametros (Descripción del parámetro)
     * @return array (Retorna el valor del parámetro)
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 26-07-2019 - Versión inicial.
     * Costo query : 3
     */
    public function getParametroCierre($arrayParametros)
    {
        try
        {
            $objRsm  = new ResultSetMappingBuilder($this->_em);
            $strSql  = " SELECT APD.VALOR1 AS DESCRIPCION
                         FROM 
                           DB_GENERAL.ADMI_PARAMETRO_CAB APC,
                           DB_GENERAL.ADMI_PARAMETRO_DET APD
                         WHERE 
                           APD.PARAMETRO_ID = APC.ID_PARAMETRO
                           AND APD.DESCRIPCION =(:strDescripcion)
                           AND APD.ESTADO='Activo'";

           $objQuery = $this->_em->createNativeQuery(null, $objRsm);

           $objQuery->setParameter('strDescripcion', $arrayParametros['strDescripcion']);
           $objRsm->addScalarResult('DESCRIPCION', 'descripcion', 'string');
           $objQuery->setSQL($strSql);
           $arrayResult = $objQuery->getResult();
           
        } 
        catch (\Exception $ex)
        {
            throw($ex);
        }
         return $arrayResult;
    }
}
