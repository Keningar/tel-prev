<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;

/**
 * Documentación para la clase Repository 'InfoProcesoMasivoCab'.
 *
 * Clase utilizada para manejar metodos que permiten realizar la liberacion de recursos de red
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 04-06-2015
 */
class InfoProcesoMasivoCabRepository extends EntityRepository {
        
    /**
     * Documentación para el método 'findSumatoriaPorFechas'.
     *
     * Me devuelve la sumatoria de los documentos tales como FAC, FACP, ND, NDI
     *
     * @param mixed $idOficina Oficina en session.
     * @param mixed $fechaDesde Fecha desde para la consulta.
     * @param mixed $puntos Pto o listado de ptos clientes.
     *
     * @return resultado Listado de documentos y total de documentos.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 25-09-2015
     */
    public function findExisteProcesoMasivoCab($intIdEmpresa)
    {
        $arrayResultado['registro']= "";
        try
        {
            $query = $this->_em->createQuery();

            $dql="SELECT ipmc.id";
            $cuerpo="
                    FROM schemaBundle:InfoProcesoMasivoCab ipmc
                    WHERE 
                    ipmc.tipoProceso= :tipoProceso
                    and ipmc.empresaCod= :empresaCod
                    and ipmc.estado= :estado
                    ";

            $dql.=$cuerpo;
            $query->setParameter('tipoProceso',"NumerarFacturas");
            $query->setParameter('empresaCod',$intIdEmpresa);
            $query->setParameter('estado','Activo');

            $query->setDQL($dql);
            $datos= $query->getResult();
            $arrayResultado['registro']=$datos;
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $arrayResultado;
    }
    
    /**
     * Documentación para el método 'generarNumeracionFacturas'.
     * Invoca a procedure que genera numeracion de facturas
     *
     * @param  String $arrayParametros   Recibe como parametro el prefijo de empresa en sesion y fecha de emision a procesar
     * @return String $arrayResultado    Retorna un mensaje de error en caso de existir uno
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 31-03-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 08-08-2016 - Se agrega que reciba los parámetros de 'usrCreacion', 'estadoImpresionFact' y 'esElectronica' a la función de 
     *                           numerar por Lote
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 03-09-2016 - Se agrega a la función que reciba el parámetro de 'arrayIdDocumentos' que contiene los id de los documentos 
     *                           seleccionados por el usuario que desea procesar.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.3 20-02-2017  Se agrega registro de historial.
     */
    public function generarNumeracionFacturas($arrayParametros)
    {
        $strMensaje = 'OK';
        
        try
        {  
            $strFechaEmision        = $arrayParametros['fecha_emision'];
            $strPrefijoEmpresa      = $arrayParametros['prefijo_empresa'];
            $strTipoDoc             = $arrayParametros['strTipoDoc'];
            $strUsrCreacion         = $arrayParametros['usrCreacion'];
            $strEstadoImpresionFact = $arrayParametros['estadoImpresionFact'];
            $strEsElectronica       = $arrayParametros['esElectronica'];
            $arrayIdDocumentos      = $arrayParametros['arrayIdDocumentos'];
            $strUsrSesion           = $arrayParametros['strUsrSesion'];

            if( !empty($arrayIdDocumentos) )
            {
                foreach( $arrayIdDocumentos as $intIdDocumento)
                {
                    $strMensajeError        = "";//Se la instancia en cero para evitar los NOTICE en el log por variable no definida previamente
                    $strMensajeError        = str_pad($strMensajeError, 1000, " ");

                    $strSql = "BEGIN DB_FINANCIERO.FNCK_FACTURACION_MENSUAL_TN.P_NUMERAR_LOTE_POR_OFICINA( :strPrefijoEmpresa, ".
                                                                                                          ":strFechaEmision, ".
                                                                                                          ":strTipoDoc, ".
                                                                                                          ":strUsrCreacion, ".
                                                                                                          ":strEstadoImpresionFact, ".
                                                                                                          ":strEsElectronica, ".
                                                                                                          ":intIdDocumento, ".
                                                                                                          ":strMensajeError ); END;";
                    $stmt = $this->_em->getConnection()->prepare($strSql);

                    $stmt->bindParam('strPrefijoEmpresa',       trim($strPrefijoEmpresa));
                    $stmt->bindParam('strFechaEmision',         $strFechaEmision);
                    $stmt->bindParam('strTipoDoc',              $strTipoDoc);
                    $stmt->bindParam('strUsrCreacion',          $strUsrCreacion);
                    $stmt->bindParam('strEstadoImpresionFact',  $strEstadoImpresionFact);
                    $stmt->bindParam('strEsElectronica',        $strEsElectronica);
                    $stmt->bindParam('intIdDocumento',          $intIdDocumento);
                    $stmt->bindParam('strMensajeError',         $strMensajeError);
                    $stmt->execute();
                    
                    $strMensajeError = trim($strMensajeError);
                    
                    $objInfoDocumentoFinancieroCab = $this->_em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdDocumento);
                    
                    // Se registra historial de numeración del documento.
                    if(is_object($objInfoDocumentoFinancieroCab) && empty($strMensajeError) )
                    {
                        $objInfoDocumentoHistorial = new InfoDocumentoHistorial();
                        $objInfoDocumentoHistorial->setDocumentoId($objInfoDocumentoFinancieroCab);
                        $objInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumentoHistorial->setUsrCreacion($strUsrSesion);
                        $objInfoDocumentoHistorial->setEstado($strEstadoImpresionFact);
                        $objInfoDocumentoHistorial->setObservacion("Se numera la Factura mediante proceso de aprobación de facturas pendientes");
                        $this->_em->persist($objInfoDocumentoHistorial);
                        $this->_em->flush();
                    }                    
                    
                    if( !empty($strMensajeError) )
                    {
                        throw new \Exception($strMensajeError);
                    }
                }//foreach( $arrayIdDocumentos as $intIdDocumento)
            }//( !empty($arrayIdDocumentos) )
            else
            {
               throw new \Exception("No se encontraron documentos seleccionados"); 
            }//( empty($arrayIdDocumentos) )
        }
        catch(\Exception $ex)
        {
            throw($ex);
        }

        return $strMensaje;
    }   
    
     /**
    * guardarProcesoMasivo.
    *
    * Método que genera un Proceso Masivo "ArchivoTarjetasAbu" para el procesamiento del archivo de tarjetas ABU 
    * 
    * @param array arrayParametros[]                  
    *              'strUrlFile'              => Ruta donde se almacena el archivo de tarjetas Abu
    *              'intIdMotivo'             => Motivo del Proceso del PMA
    *              'strObservacion'          => Observación del Proceso del PMA                 
    *              'strUsrCreacion'          => Usuario en sesión
    *              'strCodEmpresa'           => Codigo de empresa en sesión
    *              'strIpCreacion'           => Ip de creación
    *              'strTipoPma'              => Tipo de Proceso Masivo: ArchivoTarjetasAbu
    *              'strDestinatario'         => Correo del usuario en sesion
    *
    * @return strResultado  Resultado de la ejecución.
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 19-09-2022
    */
    public function guardarProcesoMasivo($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                             DB_FINANCIERO.FNCK_ACTUALIZA_TARJETAS_ABU.P_CREA_PMA_ACTUALIZA_ABU
                             ( :Pv_UrlFile, 
                               :Pn_IdMotivo, 
                               :Pv_Observacion,
                               :Pv_UsrCreacion,
                               :Pv_CodEmpresa, 
                               :Pv_IpCreacion,
                               :Pv_TipoPma,
                               :Pv_Destinatario,
                               :Pv_MsjResultado);
                           END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pv_UrlFile', $arrayParametros['strUrlFile']);
                $objStmt->bindParam('Pn_IdMotivo', $arrayParametros['intIdMotivo']);
                $objStmt->bindParam('Pv_Observacion', $arrayParametros['strObservacion']);
                $objStmt->bindParam('Pv_UsrCreacion', $arrayParametros['strUsrCreacion']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);
                $objStmt->bindParam('Pv_IpCreacion', $arrayParametros['strIpCreacion']);                
                $objStmt->bindParam('Pv_TipoPma', $arrayParametros['strTipoPma']);
                $objStmt->bindParam('Pv_Destinatario', $arrayParametros['strDestinatario']);                 
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

    /*
     * Método encargado de ejecutar el proceso para grabar afectados 
     * de casos backbone en fallas masivas
     *
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.0 - 20-04-2022
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta
     */
    public function grabarAfectadosNotificaPush($arrayParametros)
    {
        $strMensajeError  = str_pad("", 2500, " ");
        $intCodError      = 0;
        $strSql = "BEGIN DB_INFRAESTRUCTURA.INKG_NOTIFICACIONES_PUSH.P_GRABAR_INFO_NOTIFICACION(:P_CASO_ID,".
                                                                                   ":P_COD_EMPRESA,".
                                                                                   ":P_TIPO_PROCESO,".
                                                                                   ":P_USR_CREACION,".
                                                                                   ":p_IP_CREACION,".
                                                                                   ":P_ERROR,".
                                                                                   ":P_COD_ERROR); END;";

        try
        {
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('P_CASO_ID'      , $arrayParametros['intCasoId']);
            $objStmt->bindParam('P_COD_EMPRESA'  , $arrayParametros['strCodEmpresa']);
            $objStmt->bindParam('P_TIPO_PROCESO' , $arrayParametros['strTipoProceso']);
            $objStmt->bindParam('P_USR_CREACION' , $arrayParametros['strUserSession']);
            $objStmt->bindParam('p_IP_CREACION'  , $arrayParametros['strIpCreacion']);
            $objStmt->bindParam('P_ERROR'        , $strMensajeError);
            $objStmt->bindParam('P_COD_ERROR'    , $intCodError);
            $objStmt->execute();

            $arrayRespuesta = array('status'  => ($intCodError == 0 ? "Ok" : "Error"),
                                    'message' => $strMensajeError);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array('status'  => "Error",
                                    'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

}
