<?php

namespace telconet\comunicacionesBundle\Service;
/**
 * Documentación para la clase 'NotifMasivaService'.
 *
 * Clase utilizada para manejar métodos que permiten realizar las transacciones relacionadas a las notificaciones masivas
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 30-09-2017
 */
class NotifMasivaService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComunicacion;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                        = $container;
        $this->emComunicacion                   = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
    }
    
    
    /**
     * Método que crea un envío masivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "strInfoFiltros"                => información de los filtros en formato HTML,
     *                                  "strGrupo"                      => grupo del producto
     *                                  "strSubgrupo"                   => subgrupo del producto
     *                                  "intIdElementoNodo"             => id del elemento nodo
     *                                  "intIdElementoSwitch"           => id del elemento del switch
     *                                  "strEstadoServicio"             => estado del servicio
     *                                  "strEstadoPunto"                => estado del punto
     *                                  "strEstadoCliente"              => estado del cliente
     *                                  "strClientesVIP"                => 'S' si sólo se consulta los clientes VIP
     *                                  "intNumerosFactAbiertas"        => número mínimo de facturas abiertas a consultar
     *                                  "strPuntosFacturacion"          => 'S' si sólo se consulta los puntos de facturación
     *                                  "strIdsTiposNegocio"            => id de los tipos de negocio
     *                                  "strIdsOficinas"                => id de las oficinas
     *                                  "intIdFormaPago"                => id de la forma de pago
     *                                  "strNombreFormaPago"            => nombre de la forma de pago
     *                                  "strIdsBancosTarjetas"          => id de los bancos o tarjetas
     *                                  "strFechaDesdeFactura"          => fecha desde la que se empezará a comparar la fecha de autorización
     *                                                                     de la factura
     *                                  "strFechaHastaFactura"          => fecha hasta la que se empezará a comparar la fecha de autorización
     *                                                                     de la factura
     *                                  "strSaldoPendientePago"         => 'S' sis e desea consultar a los clientes que tengan saldo pendiente
     *                                  "floatValorSaldoPendientePago"  => valor mínimo con el que se comparará el saldo pendiente
     *                                  "intIdPlantilla"                => id de la plantilla que se desea enviar
     *                                  "strIdsTipoContacto"            => ids empresa rol de los tipos de contacto seleccionado para el envío
     *                                  "strAsuntoEnvio"                => asunto del envío de la notificación
     *                                  "strTipoEnvio"                  => tipo de envío de la notificación
     *                                  "strUsrCreacion"                => usuario de creación
     *                                  "strIpCreacion"                 => ip de creación
     *                              ]
     * 
     * @return string $strStatus
     */
    public function crearEnvioMasivo($arrayParametros)
    {
        $strStatus                  = "ERROR";
        try
        {
            
            if( ( isset($arrayParametros['intIdPlantilla']) && !empty($arrayParametros['intIdPlantilla']) )
                && ( isset($arrayParametros['strIdsTipoContacto']) && !empty($arrayParametros['strIdsTipoContacto']) )
                && ( isset($arrayParametros['strAsuntoEnvio']) && !empty($arrayParametros['strAsuntoEnvio']) )
                && ( isset($arrayParametros['strTipoEnvio']) && !empty($arrayParametros['strTipoEnvio']) )
                && ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                && ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) ))
            {
                $strInfoFiltros                 = ( isset($arrayParametros['strInfoFiltros']) && !empty($arrayParametros['strInfoFiltros']) )
                                                    ? $arrayParametros['strInfoFiltros'] : null;
                $strGrupo                       = ( isset($arrayParametros['strGrupo']) && !empty($arrayParametros['strGrupo']) )
                                                    ? $arrayParametros['strGrupo'] : null;
                $strSubgrupo                    = ( isset($arrayParametros['strSubgrupo']) && !empty($arrayParametros['strSubgrupo']) )
                                                    ? $arrayParametros['strSubgrupo'] : null;
                $intIdElementoNodo              = ( isset($arrayParametros['intIdElementoNodo']) && !empty($arrayParametros['intIdElementoNodo']) )
                                                    ? $arrayParametros['intIdElementoNodo'] : 0;
                $intIdElementoSwitch            = ( isset($arrayParametros['intIdElementoSwitch']) 
                                                    && !empty($arrayParametros['intIdElementoSwitch']) )
                                                    ? $arrayParametros['intIdElementoSwitch'] : 0;
                $strEstadoServicio              = ( isset($arrayParametros['strEstadoServicio']) && !empty($arrayParametros['strEstadoServicio']) )
                                                    ? $arrayParametros['strEstadoServicio'] : null;
                $strEstadoPunto                 = ( isset($arrayParametros['strEstadoPunto']) && !empty($arrayParametros['strEstadoPunto']) )
                                                    ? $arrayParametros['strEstadoPunto'] : null;
                $strEstadoCliente               = ( isset($arrayParametros['strEstadoCliente']) && !empty($arrayParametros['strEstadoCliente']) )
                                                    ? $arrayParametros['strEstadoCliente'] : null;
                $strClientesVIP                 = ( isset($arrayParametros['strClientesVIP']) && !empty($arrayParametros['strClientesVIP']) )
                                                    ? $arrayParametros['strClientesVIP'] : null;
                $strUsrCreacionFactura          = ( isset($arrayParametros['strUsrCreacionFactura']) 
                                                    && !empty($arrayParametros['strUsrCreacionFactura']) )
                                                    ? $arrayParametros['strUsrCreacionFactura'] : null;
                $intNumerosFactAbiertas         = ( isset($arrayParametros['intNumerosFactAbiertas'])
                                                    && !empty($arrayParametros['intNumerosFactAbiertas']) )
                                                    ? $arrayParametros['intNumerosFactAbiertas'] : 0;
                $strPuntosFacturacion           = ( isset($arrayParametros['strPuntosFacturacion']) 
                                                    && !empty($arrayParametros['strPuntosFacturacion']) )
                                                    ? $arrayParametros['strPuntosFacturacion'] : null;
                $strIdsTiposNegocio             = ( isset($arrayParametros['strIdsTiposNegocio']) && !empty($arrayParametros['strIdsTiposNegocio']) )
                                                    ? $arrayParametros['strIdsTiposNegocio'] : null;
                $strIdsOficinas                 = ( isset($arrayParametros['strIdsOficinas']) && !empty($arrayParametros['strIdsOficinas']) )
                                                    ? $arrayParametros['strIdsOficinas'] : null;
                $intIdFormaPago                 = ( isset($arrayParametros['intIdFormaPago']) && !empty($arrayParametros['intIdFormaPago']) )
                                                    ? $arrayParametros['intIdFormaPago'] : 0;
                $strNombreFormaPago             = ( isset($arrayParametros['strNombreFormaPago']) && !empty($arrayParametros['strNombreFormaPago']) )
                                                    ? $arrayParametros['strNombreFormaPago'] : null;
                $strIdsBancosTarjetas           = ( isset($arrayParametros['strIdsBancosTarjetas']) 
                                                    && !empty($arrayParametros['strIdsBancosTarjetas']) )
                                                    ? $arrayParametros['strIdsBancosTarjetas'] : null;
                $strFechaDesdeFactura           = ( isset($arrayParametros['strFechaDesdeFactura']) 
                                                    && !empty($arrayParametros['strFechaDesdeFactura']) )
                                                    ? $arrayParametros['strFechaDesdeFactura'] : null;
                $strFechaHastaFactura           = ( isset($arrayParametros['strFechaHastaFactura']) 
                                                    && !empty($arrayParametros['strFechaHastaFactura']) )
                                                    ? $arrayParametros['strFechaHastaFactura'] : null;
                $strSaldoPendientePago          = ( isset($arrayParametros['strSaldoPendientePago']) 
                                                    && !empty($arrayParametros['strSaldoPendientePago']) )
                                                    ? $arrayParametros['strSaldoPendientePago'] : null;
                $floatValorSaldoPendientePago   = ( isset($arrayParametros['floatValorSaldoPendientePago']) 
                                                    && !empty($arrayParametros['floatValorSaldoPendientePago']) )
                                                    ? $arrayParametros['floatValorSaldoPendientePago'] : 0;
                $strFechaHoraProgramada         = ( isset($arrayParametros['strFechaHoraProgramada'])
                                                    && !empty($arrayParametros['strFechaHoraProgramada']) )
                                                    ? $arrayParametros['strFechaHoraProgramada'] : null;
                $strFechaEjecucionDesde         = ( isset($arrayParametros['strFechaEjecucionDesde'])
                                                    && !empty($arrayParametros['strFechaEjecucionDesde']) )
                                                    ? $arrayParametros['strFechaEjecucionDesde'] : null;
                $strHoraEjecucion               = ( isset($arrayParametros['strHoraEjecucion']) && !empty($arrayParametros['strHoraEjecucion']) )
                                                    ? $arrayParametros['strHoraEjecucion'] : null;
                $strPeriodicidad                = ( isset($arrayParametros['strPeriodicidad']) && !empty($arrayParametros['strPeriodicidad']) )
                                                    ? $arrayParametros['strPeriodicidad'] : null;
                $intNumeroDia                   = ( isset($arrayParametros['intNumeroDia']) && !empty($arrayParametros['intNumeroDia']) )
                                                    ? $arrayParametros['intNumeroDia'] : 0;  
                
                $intIdPlantilla                 = $arrayParametros['intIdPlantilla'];
                $strIdsTipoContacto             = $arrayParametros['strIdsTipoContacto'];
                $strAsuntoEnvio                 = $arrayParametros['strAsuntoEnvio'];
                $strTipoEnvio                   = $arrayParametros['strTipoEnvio'];
                $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];
                $strIpCreacion                  = $arrayParametros['strIpCreacion'];
                
                $strMsjError                    = '';
                $strSqlConfigCrearJob           = "BEGIN 
                                                     DB_COMUNICACION.CUKG_TRANSACTIONS.P_CREA_JOB_NOTIF_MASIVA(
                                                                                                               :infoFiltros,
                                                                                                               :grupo,
                                                                                                               :subgrupo, 
                                                                                                               :idElementoNodo, 
                                                                                                               :idElementoSwitch, 
                                                                                                               :estadoServicio,
                                                                                                               :estadoPunto,
                                                                                                               :estadoCliente,
                                                                                                               :clientesVIP, 
                                                                                                               :usrCreacionFactura,
                                                                                                               :numFacturasAbiertas, 
                                                                                                               :puntosFacturacion, 
                                                                                                               :idsTiposNegocio, 
                                                                                                               :idsOficinas, 
                                                                                                               :idFormaPago, 
                                                                                                               :nombreFormaPago,
                                                                                                               :idsBancosTarjetas,
                                                                                                               :fechaDesdeFactura,
                                                                                                               :fechaHastaFactura,
                                                                                                               :saldoPendientePago,
                                                                                                               :valorSaldoPendientePago,
                                                                                                               :idPlantilla,
                                                                                                               :idsTipoContacto,
                                                                                                               :asunto,
                                                                                                               :tipoEnvio,
                                                                                                               :fechaHoraProgramada,
                                                                                                               :fechaEjecucionDesde,
                                                                                                               :horaEjecucion,
                                                                                                               :periodicidad,
                                                                                                               :numeroDia,
                                                                                                               :usrCreacion,
                                                                                                               :ipCreacion,
                                                                                                               :mensajeError
                                                                                                              ); 
                                                   END;";
                
                $objStmt = $this->emComunicacion->getConnection()->prepare($strSqlConfigCrearJob);
                $objStmt->bindParam( ':infoFiltros',                $strInfoFiltros);
                $objStmt->bindParam( ':grupo',                      $strGrupo);
                $objStmt->bindParam( ':subgrupo',                   $strSubgrupo);
                $objStmt->bindParam( ':idElementoNodo',             $intIdElementoNodo);
                $objStmt->bindParam( ':idElementoSwitch',           $intIdElementoSwitch);
                $objStmt->bindParam( ':estadoServicio',             $strEstadoServicio);
                $objStmt->bindParam( ':estadoPunto',                $strEstadoPunto);
                $objStmt->bindParam( ':estadoCliente',              $strEstadoCliente);
                $objStmt->bindParam( ':clientesVIP',                $strClientesVIP);
                $objStmt->bindParam( ':usrCreacionFactura',         $strUsrCreacionFactura);
                $objStmt->bindParam( ':numFacturasAbiertas',        $intNumerosFactAbiertas);
                $objStmt->bindParam( ':puntosFacturacion',          $strPuntosFacturacion);
                $objStmt->bindParam( ':idsTiposNegocio',            $strIdsTiposNegocio);
                $objStmt->bindParam( ':idsOficinas',                $strIdsOficinas);
                $objStmt->bindParam( ':idFormaPago',                $intIdFormaPago);
                $objStmt->bindParam( ':nombreFormaPago',            $strNombreFormaPago);
                $objStmt->bindParam( ':idsBancosTarjetas',          $strIdsBancosTarjetas);
                $objStmt->bindParam( ':fechaDesdeFactura',          $strFechaDesdeFactura);
                $objStmt->bindParam( ':fechaHastaFactura',          $strFechaHastaFactura);
                $objStmt->bindParam( ':saldoPendientePago',         $strSaldoPendientePago);
                $objStmt->bindParam( ':valorSaldoPendientePago',    $floatValorSaldoPendientePago);
                $objStmt->bindParam( ':idPlantilla',                $intIdPlantilla);
                $objStmt->bindParam( ':idsTipoContacto',            $strIdsTipoContacto);
                $objStmt->bindParam( ':asunto',                     $strAsuntoEnvio);
                $objStmt->bindParam( ':tipoEnvio',                  $strTipoEnvio);
                $objStmt->bindParam( ':fechaHoraProgramada',        $strFechaHoraProgramada);
                $objStmt->bindParam( ':fechaEjecucionDesde',        $strFechaEjecucionDesde);
                $objStmt->bindParam( ':horaEjecucion',              $strHoraEjecucion);
                $objStmt->bindParam( ':periodicidad',               $strPeriodicidad);
                $objStmt->bindParam( ':numeroDia',                  $intNumeroDia);
                $objStmt->bindParam( ':usrCreacion',                $strUsrCreacion);
                $objStmt->bindParam( ':ipCreacion',                 $strIpCreacion);
                $objStmt->bindParam( ':mensajeError',               $strMsjError);

                $objStmt->execute();

                if(strlen(trim($strMsjError))>0)
                {
                    $strStatus  = "ERROR";
                    throw new \Exception($strMsjError);
                }
                else
                {
                    $strStatus  = "OK";
                }
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros obligatorios para realizar la creación de la notificación masiva');
            }
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $strStatus;
    }
    
    
   /**
     * Método que elimina la notificación masiva
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017
     * 
     * @param array $arrayParametros[
     *                                  "intIdNotifMasiva"          => id de la notificación masiva
     *                                  "strUsrCreacion"            => usuario de sesión que envía a eliminar el envío masivo
     *                              ]
     * 
     * @return string $strStatus
     */
    public function eliminarNotificacionMasiva($arrayParametros)
    {
        $strStatus                          = "ERROR";
        try
        {
            if(( isset($arrayParametros['intIdNotifMasiva']) && !empty($arrayParametros['intIdNotifMasiva']) )
                && ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) ))
            {
                $intIdNotifMasiva           = $arrayParametros['intIdNotifMasiva'];
                $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
            
                $strObservacion             = 'Eliminación Manual del envío masivo ';
                $strMensajeError            = '';
                $strSQL                     = "BEGIN 
                                                 DB_COMUNICACION.CUKG_TRANSACTIONS.P_ELIMINA_JOB_NOTIF_MASIVA(
                                                                                                                :intIdNotifMasiva,
                                                                                                                :strObservacion,
                                                                                                                :strUsrCreacion,
                                                                                                                :strMensajeError ); 
                                               END;";
                $objStmt = $this->emComunicacion->getConnection()->prepare($strSQL);
                $objStmt->bindParam( ":intIdNotifMasiva", $intIdNotifMasiva);
                $objStmt->bindParam( ":strObservacion",   $strObservacion);
                $objStmt->bindParam( ":strUsrCreacion",   $strUsrCreacion);
                $objStmt->bindParam( ":strMensajeError",  $strMensajeError);
                $objStmt->execute();
                
                if(strlen(trim($strMensajeError))>0)
                {
                    throw new \Exception('Ha ocurrido un problema al eliminar el envío masivo');
                }
                $strStatus = "OK";
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros obligatorios para realizar la eliminación de la notificación masiva');
            }
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $strStatus;
    }
}
