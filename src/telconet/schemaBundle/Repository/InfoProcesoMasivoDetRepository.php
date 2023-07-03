<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoProcesoMasivoDetRepository extends EntityRepository {
    
    /**
     * Descripcion Obtiene el json de la consulta de proceso masivo
     * 
     * @param mixed $login              login del cliente
     * @param mixed $estado             estado del detalle de proceso masivo
     * @param mixed $fechaDesde         fecha incial del detalle de proceso masivo
     * @param mixed $fechaHasta         fecha final del detalle de proceso masivo
     * @param mixed $ultimaMillaBusq    ultima milla del detalle de proceso masivo
     * @param mixed $tipo               tipo de proceso masivo (corte, reconexion)
     * @param mixed $idProcesoMasivo    codigo del proceso masivo
     * @param mixed $strCodEmpresa      Id de la empresa
     * @param mixed $em                 conexion a la BD comercial
     * @param mixed $emInfraestructura  conexion a la BD infraestructura
     * @param mixed $start              limite inicial
     * @param mixed $limit              limite final
     *
     * @return mixed $resultado         json 
     *       
     * @author Creado:     John Vera         <javera@telconet.ec>
     * @version 1.0 04-12-2014
     * 
     * @author Modificado:     John Vera  <javera@telconet.ec> Incluir olt en consulta
     * @version 1.1 27-11-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-01-2018 Se agrega validación para que al filtrar por CambioPlanMasivo, 
     *                         consulte sólo los procesos masivos de CambioPlanMasivo de este año. 
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 10-09-2021 Se valida el campo elemento para el tipo de proceso actualizar características del Olt
     */
    public function generarJsonConsultaProcesoMasivo ($arrayParametros, $emInfraestructura )
    {
        $login              = $arrayParametros['login'];
        $estado             = $arrayParametros['estado'];
        $fechaDesde         = $arrayParametros['fechaDesde'];
        $fechaHasta         = $arrayParametros['fechaHasta'];
        $ultimaMillaBusq    = $arrayParametros['ultimaMilla'];
        $tipo               = $arrayParametros['tipo'];
        $idProcesoMasivo    = $arrayParametros['idProcesoMasivo'];
        $start              = $arrayParametros['start'];
        $limit              = $arrayParametros['limit'];
        $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
        
        //si no trae ningun filtro envio la fecha de hoy por default
        if (trim($login)=='' && trim($estado) =='' && trim($fechaDesde) =='' && trim($fechaHasta)=='' && trim($ultimaMillaBusq)=='' 
            && trim($tipo)=='' && trim($idProcesoMasivo)=='')
        {
            $arrayParametros['fechaDesde'] =  date("Y-m-d");
            $arrayParametros['fechaHasta'] =  date("Y-m-d");            
        }

        if ($tipo == 'CambioPlanMasivo' && trim($fechaDesde) =='' && trim($fechaHasta)=='')
        {
            $strFormatoFecha                = 'Y-m-d';
            $objFecha                       = \DateTime::createFromFormat($strFormatoFecha, '2019-01-01');
            $arrayParametros['fechaDesde']  =  $objFecha->format('Y-m-d');   
        }

        //datos del tipo de proceso masivo
        $strTipoProcesoActualizarOlt = "";
        $arrayDatosTipoProceso = $this->_em->getRepository('schemaBundle:AdmiParametroDet')->getOne('DATOS_TIPO_PROCESO_ACTUALIZAR_CARACT_OLT',
                                                                                        'TECNICO',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        $strCodEmpresa);
        if(isset($arrayDatosTipoProceso) && !empty($arrayDatosTipoProceso) && isset($arrayDatosTipoProceso['valor1']))
        {
            $strTipoProcesoActualizarOlt = $arrayDatosTipoProceso['valor1'];
        }

        $num=0;
        $arr_encontrados = array();        
        $todosEncontrados = $this->getConsultaProcesoMasivo($arrayParametros, $emInfraestructura);
        $num = count($todosEncontrados);
        
        if ($num > 0)
        {
            //paginacion
            $limite = $start + $limit;
            $encontrados = array_slice($todosEncontrados, $start, $limite);
            
            if ($encontrados) 
            {

                foreach ($encontrados as $registro)
                {
                    
                    $ultimaMilla = '';
                    $strNombreElemento = '';
                    if ($registro['puntoId'])
                    {
                        $ultimaMilla = $this->_em->getRepository('schemaBundle:InfoPunto')->getUltimaMillaPorPunto($registro['puntoId']);
                        
                        $servicioTecnico = $this->_em->getRepository('schemaBundle:InfoServicioTecnico')
                                                     ->getServiciotecnicoByPuntoProd($registro['puntoId'], 'INTERNET');
                        $objElemento =  $this->_em->getRepository('schemaBundle:InfoElemento')->findOneById($servicioTecnico[0]['elemento']);
                        if($registro['tipoProceso'] == $strTipoProcesoActualizarOlt)
                        {
                            $objElemento =  $this->_em->getRepository('schemaBundle:InfoElemento')->findOneById($registro['elementoId']);
                        }
                        if($objElemento)
                        {
                            $strNombreElemento = $objElemento->getNombreElemento();
                        }

                        if($ultimaMilla)
                        {
                            $objTipoMedio = $this->_em->getRepository('schemaBundle:AdmiTipoMedio')->findOneBy(array( 'codigoTipoMedio'=> $ultimaMilla, 
                                                                                                               'estado' => 'Activo' ));
                            $nombreUltimaMilla = $objTipoMedio->getNombreTipoMedio();
                        }
                    }
                    //filtro segun la ultima milla
                    if(trim($ultimaMillaBusq) == '' || strtoupper($ultimaMilla) == strtoupper($ultimaMillaBusq)) 
                    {
                        //se crea el array con los datos consultados
                        $arr_encontrados[] = array('procesoMasivo'  => $registro['id'],
                                                    'procesoMasivoDet'  => $registro['idDetalle'],
                                                    'login'         => $registro['login'],
                                                    'nombreElemento'=> $strNombreElemento,
                                                    'tipoProceso'   => $registro['tipoProceso'],
                                                    'fechaProceso'  => date_format($registro['feCreacion'], 'd-m-Y H:i:s'),
                                                    'usuarioCrea'   => $registro['usrCreacion'],
                                                    'estado'        => $registro['estado'],
                                                    'observacion'   => $registro['observacion'],
                                                    'ultimaMilla'   => $nombreUltimaMilla
                                                    );                    
                    }
                }
            }
        }
        $data=json_encode($arr_encontrados);
        $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';
        return $resultado;
    }
    
    /**
     * Funcion que sirve para setear los estados de los proceso masivos det de una cabecera en especifico
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.0 6-10-2017
     * @param int $intIdProcesoMasivoCab 
     * @param int $strEstado
     */
    public function setEstadoProcesoMasivoDetByProcesoMasivoCabId($intIdProcesoMasivoCab, $strEstado)
    {
        try
        {
            $strSql    = "UPDATE INFO_PROCESO_MASIVO_DET SET ESTADO=:estadoDet WHERE PROCESO_MASIVO_CAB_ID = :idProcesoMasivoCab";
            
            $objStmt   = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindValue('idProcesoMasivoCab',  $intIdProcesoMasivoCab);
            $objStmt->bindValue('estadoDet',           $strEstado);
            $objStmt->execute();
            
            $strStatus = "OK";
            $stMensaje = "Se procedio a realizar rollback del proceso masivo";
        }
        catch(\Exception $e)
        {
            $strStatus = "ERROR";
            $stMensaje = "Error: ".$e->getMessage();
        }
        
        return array('status' => $strStatus, 'mensaje' => $stMensaje);
    }
    
    /**
    * Descripcion Obtiene la consulta filtrada por los parametros de proceso masivos
    *
    * @param mixed $strCodEmpresa      codigo de la empresa en session
    * @param mixed $login              login del cliente
    * @param mixed $estado             estado del detalle de proceso masivo
    * @param mixed $fechaDesde         fecha incial del detalle de proceso masivo
    * @param mixed $fechaHasta         fecha final del detalle de proceso masivo
    * @param mixed $ultimaMillaBusq    ultima milla del detalle de proceso masivo
    * @param mixed $tipo               tipo de proceso masivo (corte, reconexion)
    * @param mixed $idProcesoMasivo    codigo del proceso masivo
    * @param mixed $emInfraestructura  conexion a la BD infraestructura
    *
    * @return mixed $resultado         json 
    *       
    * @author Creado:     John Vera         <javera@telconet.ec>
    * @version 1.0 04-12-2014
    * @version 1.1 15-02-2016 John Vera Aumento de filtro de elemento
    *
    * @author Richard Cabrera  <rcabrera@telconet.ec>
    * @version 1.2 16-02-2018 Los procesos se consultaran por la empresa en session
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.3 10-09-2021 Se agrega el campo elemento
    */
    public function getConsultaProcesoMasivo($arrayParametros, $emInfraestructura)
    {
        $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
        $login              = $arrayParametros['login'];
        $estado             = $arrayParametros['estado'];
        $fechaDesde         = $arrayParametros['fechaDesde'];
        $fechaHasta         = $arrayParametros['fechaHasta'];
        $tipo               = $arrayParametros['tipo'];
        $idProcesoMasivo    = $arrayParametros['idProcesoMasivo'];
        $idElemento         = $arrayParametros['idElemento'];

        
        $query = $emInfraestructura->createQuery();
        
        $dql = "SELECT pmc.id , 
                       pmd.id idDetalle, 
                       p.login, 
                       pmc.tipoProceso, 
                       pmc.elementoId, 
                       pmd.feCreacion, 
                       pmd.usrCreacion, 
                       pmd.estado, 
                       pmd.observacion, 
                       pmd.puntoId
                  FROM
                       schemaBundle:InfoProcesoMasivoDet pmd,
                       schemaBundle:InfoProcesoMasivoCab pmc,
                       schemaBundle:InfoPunto p
                 WHERE
                       pmc.id = pmd.procesoMasivoCabId
                   AND pmd.puntoId = p.id ";
        
        if ($login != "")
        {
            $dql .= " AND upper(p.login) like upper(:login)";
            $query->setParameter("login", "%".$login."%");
        }
        if ($strCodEmpresa != "")
        {
            $dql .= " AND pmc.empresaCod = :empresaCod ";
            $query->setParameter("empresaCod", $strCodEmpresa );
        }
        if ( $idProcesoMasivo!= "")
        {
            $dql .= " AND pmc.id = :idProcesoMasivo";
            $query->setParameter("idProcesoMasivo", $idProcesoMasivo );
        }
        if ( $estado!= "")
        {
            $dql .= " AND pmd.estado = :estado ";
            $query->setParameter("estado", $estado);
        }

        if ( $tipo!= "")
        {
            $dql .= " AND pmc.tipoProceso = :tipo ";
            $query->setParameter("tipo", $tipo);
        }
        if ( $fechaDesde!= "")
        {   
            $dql .= " AND SUBSTRING(pmd.feCreacion,1,10) >= :fechaDesde ";
            $query->setParameter("fechaDesde", substr($fechaDesde, 0,10));
        }
        if ( $fechaHasta!= "")
        {
            $dql .= " AND SUBSTRING(pmd.feCreacion,1,10) <= :fechaHasta";
            $query->setParameter("fechaHasta", substr($fechaHasta, 0,10));
        }
        if ( $idElemento!= "")
        {
            $dql .= " AND pmc.elementoId = :idElemento";
            $query->setParameter("idElemento", $idElemento);
        }
                
        $dql .= " ORDER BY pmc.id DESC";
        
        $query->setDQL($dql);
        
        $datos = $query->getResult();
        
        return $datos;
        
    }
    
    /**
     * Obtiene el ultimo detalle de procesos masivo para reprocesar clientes 
     * cancelados con data incorrecta
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 05-06-2015
     * 
     * @param string  $tipoCabecera
     * @param string  $empresaId
     * @param Integer $puntoId
     * @param Integer $servicioId
     * 
     * @return array $arrayRegistroCab
     **/
    public function getObtenerUltimoProcesoMasivoDet($tipoCabecera, $empresaId, $puntoId, $servicioId)
    {
        $strQueryObtenerPadre = "SELECT INFO_PROCESO_MASIVO_DET.ID_PROCESO_MASIVO_DET as ID_PROCESO_MASIVO_DET
                                FROM INFO_PROCESO_MASIVO_DET
                                WHERE INFO_PROCESO_MASIVO_DET.PROCESO_MASIVO_CAB_ID =
                                  (SELECT MAX(INFO_PROCESO_MASIVO_CAB.ID_PROCESO_MASIVO_CAB)
                                  FROM INFO_PROCESO_MASIVO_CAB ,
                                    INFO_PROCESO_MASIVO_DET
                                  WHERE INFO_PROCESO_MASIVO_DET.PROCESO_MASIVO_CAB_ID = INFO_PROCESO_MASIVO_CAB.ID_PROCESO_MASIVO_CAB
                                  AND INFO_PROCESO_MASIVO_DET.PUNTO_ID                = :puntoId
                                  AND INFO_PROCESO_MASIVO_DET.SERVICIO_ID             = :servicioId
                                  AND INFO_PROCESO_MASIVO_CAB.TIPO_PROCESO            = :tipoCabecera
                                  and INFO_PROCESO_MASIVO_CAB.EMPRESA_ID= :empresaId
                                  )
                                AND INFO_PROCESO_MASIVO_DET.PUNTO_ID    = :puntoId
                                AND INFO_PROCESO_MASIVO_DET.SERVICIO_ID = :servicioId";
        
        $stmt = $this->_em->getConnection()->prepare($strQueryObtenerPadre);
        $stmt->bindValue('empresaId',   $empresaId);
        $stmt->bindValue('tipoCabecera', $tipoCabecera);
        $stmt->bindValue('puntoId', $puntoId);
        $stmt->bindValue('servicioId', $servicioId);
        $stmt->execute();
        $arraResult = $stmt->fetchAll();
        return $arraResult;
    }

    /**
     * getProcesosPendientes, obtiene la cantidad de proceso del estado recibido como parámetro.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 06-04-2020
     *      
     * @param array $arrayParametros [
     *                                "strProceso" => Nombre del proceso.
     *                                "strEstado"  => Estado del proceso.
     *                                "intIdPunto" => Id del punto.
     *                               ]
     *
     * Costo de query : 2
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 25-06-2020 - Se agrega Id de Punto en la consulta.
     * 
     * @author Andrea Cárdenas <ascardenas@telconet.ec>
     * @version 1.2 27-07-2021 - Se agrega Id de Cabecera en la consulta

     * 
     * @return Response lista de Tipos de Negocio
     */
    public function getProcesosPendientes($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery        = " SELECT COUNT (IPMC.ID_PROCESO_MASIVO_CAB) AS CANTIDAD
                             FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB IPMC,
                               DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET IPMD
                             WHERE IPMC.TIPO_PROCESO        = :strProceso
                             AND IPMD.PROCESO_MASIVO_CAB_ID = IPMC.ID_PROCESO_MASIVO_CAB
                             AND IPMC.ESTADO                = :strEstado
                             AND IPMD.ESTADO                = :strEstado ";

        $objRsm->addScalarResult('CANTIDAD', 'cantidad', 'integer');
                  
        $objNtvQuery->setParameter('strProceso' ,$arrayParametros['strProceso']);
        $objNtvQuery->setParameter('strEstado' ,$arrayParametros['strEstado']);
        
        if (isset($arrayParametros['intIdPunto']) &&  !empty($arrayParametros['intIdPunto']))
        {
            $strQuery = $strQuery . ' AND IPMD.PUNTO_ID = :intIdPunto ';
            $objNtvQuery->setParameter('intIdPunto' ,$arrayParametros['intIdPunto']);
        }

        if (isset($arrayParametros['intIdProcesoCab']) &&  !empty($arrayParametros['intIdProcesoCab']))
        {
            $strQuery = $strQuery . ' AND IPMC.ID_PROCESO_MASIVO_CAB = :intIdProcesoCab ';
            $objNtvQuery->setParameter('intIdProcesoCab' ,$arrayParametros['intIdProcesoCab']);
        }

        $objNtvQuery->setSQL($strQuery);
        $arrayRespuesta  = $objNtvQuery->getScalarResult();
        return $arrayRespuesta[0]["cantidad"] ;
    }

    /**
    * guardarProcesoMasivo.
    *
    * Método que genera un Proceso Masivo que puede ser por EjecutarEmerSanit y/o ReporteEmerSanit, en base a parámetros enviados.
    * El método incluirá en el PMA todas los parámetros que servirán para evaluar a los Clientes que apliquen diferidos por emergencia
    * sanitaria.
    * 
    * @param array $arrayParametros []
    *              'strObservacion'          => Trama del Proceso del PMA
    *              'strUsrCreacion'          => Usuario en sesión
    *              'strCodEmpresa'           => Codigo de empresa en sesión
    *              'strIpCreacion'           => Ip de creación
    *              'strTipoPma'              => Tipo de Proceso Masivo :EjecutarEmerSanit y/o ReporteEmerSanit.
    *              'intIdPunto'              => Id del Punto para el caso de ejecutarse Proceso Individual de Diferido de Facturas.
    *
    * @return strResultado  Resultado de la ejecución.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 07-04-2020       
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.1 25-06-2020 - Se agrega parámetro intIdPunto para crear proceso individual de Diferido de Facturas por Punto en sesión.
    */
    public function guardarProcesoMasivo($arrayParametros)
    {
        $strResultado = "";
        $intIdPunto  = ( isset($arrayParametros['intIdPunto']) && !empty($arrayParametros['intIdPunto']) ) ? $arrayParametros['intIdPunto']
                       : null;        
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = " BEGIN 
                             DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.P_CREA_PM_EMER_SANIT 
                             ( :Pv_Observacion,
                               :Pv_UsrCreacion,
                               :Pv_CodEmpresa,
                               :Pv_IpCreacion,
                               :Pv_TipoPma,
                               :Pn_IdPunto,
                               :Pv_MsjResultado ); 
                           END; ";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pv_Observacion', $arrayParametros['strObservacion']);
                $objStmt->bindParam('Pv_UsrCreacion', $arrayParametros['strUsrCreacion']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);
                $objStmt->bindParam('Pv_IpCreacion', $arrayParametros['strIpCreacion']);
                $objStmt->bindParam('Pv_TipoPma', $arrayParametros['strTipoPma']);
                $objStmt->bindParam('Pn_IdPunto', $intIdPunto);
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);
                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar el Proceso Masivo '.$arrayParametros['strTipoPma'];
            }

        }
        catch (\Exception $e)
        {
            $strResultado= 'Ocurrió un error al guardar el Proceso Masivo '.$arrayParametros['strTipoPma'];
            throw($e);
        }
        
        return $strResultado; 
    }
   /**
    * crearSolicitudesNci
    * 
    * Método que genera las solicitudes "SOLICITUD DIFERIDO DE FACTURA POR EMERGENCIA SANITARIA", para la creación de NCI
    * de las facturas diferidas.
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 28-06-2020
    * @param array $arrayParametros []     
    *              'strTipoPma'              => Tipo de Proceso Masivo:                                               
    *                                           EjecutarEmerSanitPto(Individual por Punto)
    *              'strCodEmpresa'           => Empresa en sesión     
    *              'strEstado'               => Estado del Proceso Masivo a procesar "Pendiente"     
    *              'intIdPunto'              => Id del Punto para el caso de ejecutarse Proceso Individual de Diferido de Facturas
    * 
    * @return arrayResultado  Resultado de la ejecución.    
    */
    public function crearSolicitudesNci($arrayParametros)
    {
        $strResultado          = "";          
        $intIdProcesoMasivoCab = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = " BEGIN 
                             DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.P_CREA_SOLICITUDES_NCI 
                             ( :Pv_TipoPma,                               
                               :Pv_CodEmpresa,
                               :Pv_Estado,                               
                               :Pn_IdPunto,
                               :Pv_MsjResultado,
                               :Pn_IdProcesoMasivoCab); 
                           END; ";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");                
                $intIdProcesoMasivoCab = str_pad($intIdProcesoMasivoCab, 5000, " ");     
                $objStmt->bindParam('Pv_TipoPma', $arrayParametros['strTipoPma']);                
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);
                $objStmt->bindParam('Pv_Estado', $arrayParametros['strEstado']);                
                $objStmt->bindParam('Pn_IdPunto', $arrayParametros['intIdPunto']);
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);
                $objStmt->bindParam('Pn_IdProcesoMasivoCab', $intIdProcesoMasivoCab);
                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para la creación de Solicitudes de Diferido por Emergencia Sanitaria: '
                    .$arrayParametros['strTipoPma']
                    .' IdPunto: ' .$arrayParametros['intIdPunto'];
            }

        }
        catch (\Exception $e)
        {           
            throw($e);
        }
        $arrayResultado = array ('strResultado'           => $strResultado,
                                 'intIdProcesoMasivoCab'  => $intIdProcesoMasivoCab);
        return $arrayResultado; 
    }
   /**
    * ejecutaSolDiferido
    * 
    * Método que genera las NCI de las solicitudes "SOLICITUD DIFERIDO DE FACTURA POR EMERGENCIA SANITARIA".
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 28-06-2020
    * @param array $arrayParametros []     
    *              'strCodEmpresa'             => Empresa en sesión     
    *              'strUsrCreacion'            => Usuario de Creación 'telcos_diferido'
    *              'strIpCreacion'             => Ip de Creación   
    *              'strDescripcionSolicitud'   => Tipo de Solicitud
    *              'intIdPunto'                => Id del Punto que ejecuta el Proceso de Diferido.                        
    * 
    * @return strResultado  Resultado de la ejecución.    
    */
    public function ejecutaSolDiferido($arrayParametros)
    {
        $strResultado = "";  
        $strFormaPago = null;
        $strIdCiclo   = null;
        $strIdsFormasPagoEmisores = null;
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = " BEGIN 
                             DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.P_EJECUTA_SOL_DIFERIDO 
                             ( :Pv_CodEmpresa,
                               :Pv_UsrCreacion,
                               :Pv_DescripcionSolicitud,     
                               :Pv_FormaPago,
                               :Pn_IdCiclo,
                               :Pv_IdsFormasPagoEmisores,
                               :Pn_IdPunto,
                               :Pv_MsjResultado ); 
                           END; ";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");                
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);                
                $objStmt->bindParam('Pv_UsrCreacion', $arrayParametros['strUsrCreacion']);
                $objStmt->bindParam('Pv_DescripcionSolicitud', $arrayParametros['strDescripcionSolicitud']);
                $objStmt->bindParam('Pv_FormaPago', $strFormaPago);
                $objStmt->bindParam('Pn_IdCiclo', $strIdCiclo);
                $objStmt->bindParam('Pv_IdsFormasPagoEmisores', $strIdsFormasPagoEmisores);
                $objStmt->bindParam('Pn_IdPunto', $arrayParametros['intIdPunto']);
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);
                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para la creación de las NCI de las Solicitudes de Diferido por Emergencia Sanitaria: '
                    .$arrayParametros['strDescripcionSolicitud']
                    .' IdPunto: ' .$arrayParametros['intIdPunto'];
            }

        }
        catch (\Exception $e)
        {            
            throw($e);
        }
        
        return $strResultado; 
    }
   /**
    * ejecutaNdiDiferido
    * 
    * Método que se encarga de generar las NDI (Notas de débito interna) por las cuotas diferidas que se encuentran definidas en las NCI.  
    *         
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 28-06-2020
    * @param array $arrayParametros []     
    *              'strCodEmpresa'             => Empresa en sesión     
    *              'strUsrCreacion'            => Usuario de Creación 'telcos_diferido'
    *              'strIpCreacion'             => Ip de Creación        
    *              'intIdPunto'                => Id del Punto que ejecuta el Proceso de Diferido.    
    *              'intIdProcesoMasivoCab'     => Id del Proceso masivo generado para el diferido individual                             
    *     
    * @return $strRespuesta
    */
    public function ejecutaNdiDiferido($arrayParametros)
    {
        $strResultado             = "";  
        $strFormaPago             = null;
        $strIdCiclo               = null;
        $strIdsFormasPagoEmisores = null;        
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = " BEGIN 
                             DB_FINANCIERO.FNCK_PAGOS_DIFERIDOS.P_EJECUTA_NDI_DIFERIDO 
                             ( :Pv_CodEmpresa,
                               :Pv_UsrCreacion,                                    
                               :Pv_FormaPago,
                               :Pn_IdCiclo,
                               :Pv_IdsFormasPagoEmisores,
                               :Pn_IdPunto,
                               :Pn_IdProcesoMasivoCab,
                               :Pv_MsjResultado); 
                           END; ";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");                
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);                
                $objStmt->bindParam('Pv_UsrCreacion', $arrayParametros['strUsrCreacion']);                
                $objStmt->bindParam('Pv_FormaPago', $strFormaPago);
                $objStmt->bindParam('Pn_IdCiclo', $strIdCiclo);
                $objStmt->bindParam('Pv_IdsFormasPagoEmisores', $strIdsFormasPagoEmisores);
                $objStmt->bindParam('Pn_IdPunto', $arrayParametros['intIdPunto']);
                $objStmt->bindParam('Pn_IdProcesoMasivoCab', $arrayParametros['intIdProcesoMasivoCab']);
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);
                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para la creación de las NDI o cuotas por Diferido por Emergencia Sanitaria: '
                   .' IdPunto: ' .$arrayParametros['intIdPunto'];
            }

        }
        catch (\Exception $e)
        {
            $strResultado= 'Ocurrió un error al crear las NDI o cuotas por Diferido por Emergencia Sanitaria: '
                .' IdPunto: ' .$arrayParametros['intIdPunto'];
            throw($e);
        }
        
        return $strResultado; 
    }
    
    
    /**
     * Función que sirve para crear los registros de corte masivo por lotes
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-09-2021
     * 
     * @param array $arrayParametros [
     *                                  "strDatabaseDsn"            => Conexión a la Base de Datos
     *                                  "strUserInfraestructura"    => Usuario del esquema Comercial
     *                                  "strPasswordInfraestructura"=> Password del esquema Comercial
     *                                  "arrayParamsBusqueda"       => Arreglo con los parámetros de búsqueda de la consulta
     *                                                                  "strCodEmpresa"             => id de la empresa
     *                                                                  "strFechaCreacionDoc"       => fecha de creación del documento
     *                                                                  "strTiposDocumentos"        => códigos de los tipos de documentos 
     *                                                                                                 concatenados por ,
     *                                                                  "strNumDocsAbiertos"        => número de documentos abiertos
     *                                                                  "strValorMontoCartera"      => valor de monto de cartera
     *                                                                  "strIdTipoNegocio"          => id del tipo de negocio
     *                                                                  "strValorClienteCanal"      => 'Todos', 'SI', 'NO'
     *                                                                  "strNombreUltimaMilla"      => nombre de la última milla
     *                                                                  "strIdCicloFacturacion"     => id del ciclo de facturación
     *                                                                  "strIdsOficinas"            => ids de oficinas concatenados por ,
     *                                                                  "strIdsFormasPago"          => ids de la formas de pago concatenados por ,
     *                                                                  "strValorCuentaTarjeta"     => 'Cuenta', 'Tarjeta'
     *                                                                  "strIdsTiposCuentaTarjeta"  => ids de tipos de cuenta concatenados por ,
     *                                                                  "strIdsBancos"              => ids de bancos concatenados por ,
     *                                ]
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => Mensaje de error,
     *                                  "idSolCortePorLotes"    => id de la solicitud de corte masivo por lotes
     *                                ]
     */
    public function creaCorteMasivoPorLotes($arrayParametros)
    {
        $strDatabaseDsn             = ( isset($arrayParametros['strDatabaseDsn']) && !empty($arrayParametros['strDatabaseDsn']) )
                                        ? $arrayParametros['strDatabaseDsn'] : null;
        $strUserInfraestructura     = ( isset($arrayParametros['strUserInfraestructura']) && !empty($arrayParametros['strUserInfraestructura']) )
                                        ? $arrayParametros['strUserInfraestructura'] : null;
        $strPasswordInfraestructura = ( isset($arrayParametros['strPasswordInfraestructura']) 
                                        && !empty($arrayParametros['strPasswordInfraestructura']) ) 
                                        ? $arrayParametros['strPasswordInfraestructura'] : null;
        $strPrefijoEmpresa          = $arrayParametros['strPrefijoEmpresa'];
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
        $strIpCreacion              = $arrayParametros['strIpCreacion'];
        $strStatus                  = "";
        $strMsjError                = "";
        $intIdSolCortePorLotes      = 0;
        try
        {
            if(!empty($strDatabaseDsn) && !empty($strUserInfraestructura) && !empty($strPasswordInfraestructura))
            {
                $arrayParamsBusqueda    = $arrayParametros["arrayParamsBusqueda"];
                if(isset($arrayParamsBusqueda) && !empty($arrayParamsBusqueda))
                {
                    $strJsonFiltrosBusqueda     = json_encode($arrayParamsBusqueda);
                    $objOciConexion             = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
                    $objCursorPuntosCorte       = oci_new_cursor($objOciConexion);
                    $strSql                     = "BEGIN
                                                    DB_INFRAESTRUCTURA.INKG_TRANSACCIONES_MASIVAS.P_CREA_CORTE_MASIVO_X_LOTES
                                                    (
                                                        :strJsonFiltrosBusqueda,
                                                        :strPrefijoEmpresa,
                                                        :strUsrCreacion,
                                                        :strIpCreacion,
                                                        :strStatus,
                                                        :strMsjError,
                                                        :intIdSolCortePorLotes
                                                    );
                                                   END;";
                    $objStmt                    = oci_parse($objOciConexion,$strSql);
                    $strClobJsonFiltrosBusqueda = oci_new_descriptor($objOciConexion);
                    $strClobJsonFiltrosBusqueda->writetemporary($strJsonFiltrosBusqueda);
                    oci_bind_by_name($objStmt, ":strJsonFiltrosBusqueda", $strClobJsonFiltrosBusqueda, -1, OCI_B_CLOB);
                    oci_bind_by_name($objStmt, ":strPrefijoEmpresa", $strPrefijoEmpresa);
                    oci_bind_by_name($objStmt, ":strUsrCreacion", $strUsrCreacion);
                    oci_bind_by_name($objStmt, ":strIpCreacion", $strIpCreacion);
                    oci_bind_by_name($objStmt, ":strStatus", $strStatus, 5);
                    oci_bind_by_name($objStmt, ":strMsjError", $strMsjError, 4000);
                    oci_bind_by_name($objStmt, ":intIdSolCortePorLotes", $intIdSolCortePorLotes, 32, SQLT_INT);
                    oci_execute($objStmt);
                    oci_execute($objCursorPuntosCorte, OCI_DEFAULT);
                }
                else
                {
                    throw new \Exception('No se han enviado los parámetros de búsqueda para realizar la consulta para los logines a cortar');
                }
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para crear el corte masivo por lotes de los puntos. Database('.
                                     $strDatabaseDsn.'), UsrInfraestructura('.$strUserInfraestructura
                                     .'), PassInfraestructura('.$strPasswordInfraestructura.').');
            }
        }
        catch (\Exception $e) 
        {
            $strStatus      = "ERROR";
            $strMsjError    = "Ha ocurrido una excepción al intentar crear el corte masivo por lotes";
            error_log("Error en creaCorteMasivoPorLotes ".$e->getMessage());      
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMsjError,
                                "idSolCortePorLotes"    => $intIdSolCortePorLotes);
        return $arrayRespuesta;
    }
}

