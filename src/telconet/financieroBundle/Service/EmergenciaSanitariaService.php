<?php
namespace telconet\financieroBundle\Service;
use Symfony\Component\HttpFoundation\Response;

class EmergenciaSanitariaService 
{ 
    private $emcom;
    private $emfinan;
    private $emInfraestructura;
    private $serviceUtil;
    private $emGeneral;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emcom              = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emfinan            = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emInfraestructura  = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral          = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil        = $objContainer->get('schema.Util');
    }
    
    /**
     * getCiclos, obtiene los ciclos de facturación por código de empresa.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 06-04-2020     
     * @param array $arrayParametros [
     *                                "strEmpresaCod" => Empresa en sesión
     *                               ]
     *
     * @return Response lista de los ciclos de facturación por código empresa.
     */
    public function getCiclos($arrayParametros)
    {
        return $this->emfinan->getRepository('schemaBundle:AdmiCiclo')->getCiclos($arrayParametros);
    }

    /**
     * crearProcesoMasivo
     * 
     * Método que genera un Proceso Masivo que puede ser por Reporte y/o Ejecutar NCI por emergencia sanitaria, 
     * en base a parámetros enviados.
     * El método incluirá en el PMA todos los parámetros con los que se evaluará a los Clientes.
     *         
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 07-04-2020
     * @param array $arrayParametros []
     *              'strSaldoDesde'           => Parametro para definir el Saldo de desde
     *              'strSaldoHasta'           => Parametro para definir el Saldo Hasta 
     *              'strCiclosFacturacion'    => Ciclos de Facturación
     *              'strEstadoServicio'       => Estados de servicio
     *              'strMesesDiferir'         => Meses a Diferir
     *              'strMotivo'               => Motivo o tipo de Proceso Masivo:
     *                                           EjecutarEmerSanit(Masivo)
     *                                           EjecutarEmerSanitPto(Individual por Punto)
     *              'strUsrCreacion'          => Usuario en sesión
     *              'strCodEmpresa'           => Código de empresa en sesión
     *              'strIpCreacion'           => Ip de creación     
     *              'intIdPunto'              => Id del Punto para el caso de ejecutarse Proceso Individual de Diferido de Facturas
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 25-06-2020 - Se agrega parámetro intIdPunto para crear proceso individual de Diferido de Facturas por Punto en sesión.
     * 
     * @return $strRespuesta
     */
    public function crearProcesoMasivo($arrayParametros)
    {      
        if (!isset($arrayParametros['intIdPunto']) || empty($arrayParametros['intIdPunto']))
        {
            $strSaldoDesde                  = $arrayParametros['strSaldoDesde'];
            $strSaldoHasta                  = $arrayParametros['strSaldoHasta'];        
            $strCiclosFacturacion           = $arrayParametros['strCiclosFacturacion'];
            $strEstadoServicio              = $arrayParametros['strEstadoServicio'];          
        }       
        $strMesesDiferir                = $arrayParametros['strMesesDiferir'];
        $strMotivo                      = $arrayParametros['strMotivo'];
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];

        $this->emcom->beginTransaction();
        try
        {
            if (!isset($arrayParametros['intIdPunto']) || empty($arrayParametros['intIdPunto']))
            {
                $strTramaParametros     = "SALDO_DESDE:".$strSaldoDesde."|".
                                          "SALDO_HASTA:".$strSaldoHasta."|".
                                          "MES_DIFERIDO:".$strMesesDiferir."|".
                                          "CICLO:".$strCiclosFacturacion."|".
                                          "ESTADO_SERVICIO:".$strEstadoServicio."|";
            }
            else
            {
                $strTramaParametros     = "MES_DIFERIDO:".$strMesesDiferir."|";                   
            }
 
            $arrayParametrosConsulta                 = array();
            $arrayParametrosConsulta['strProceso']   = $strMotivo;
            $arrayParametrosConsulta['strEstado']    = "Pendiente";
            
            if (isset($arrayParametros['intIdPunto']) &&  !empty($arrayParametros['intIdPunto']))
            {
                $arrayParametrosConsulta['intIdPunto'] = $arrayParametros['intIdPunto'];
            }
            $strCantidad = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                   ->getProcesosPendientes($arrayParametrosConsulta);

            if ($strCantidad !=  "0")
            {
                $strRespuesta = "EXISTE";
                return $strRespuesta;
            }

            $arrayParametrosProceso                    = array();
            $arrayParametrosProceso['strObservacion']  = $strTramaParametros;
            $arrayParametrosProceso['strUsrCreacion']  = $strUsrCreacion;
            $arrayParametrosProceso['strCodEmpresa']   = $strCodEmpresa;
            $arrayParametrosProceso['strIpCreacion']   = $strIpCreacion;
            $arrayParametrosProceso['strTipoPma']      = $strMotivo;
            
            if (isset($arrayParametros['intIdPunto']) &&  !empty($arrayParametros['intIdPunto']))
            {
                $arrayParametrosProceso['intIdPunto'] = $arrayParametros['intIdPunto'];
            }

            $strRespuesta                              = $this->emInfraestructura
                                                              ->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                              ->guardarProcesoMasivo($arrayParametrosProceso);
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo crear el Proceso Masivo: ".$arrayParametrosProceso['strTipoPma'].", <br> ". 
                             $e->getMessage(). ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'EmergenciaSanitariaService.crearProcesoMasivo',
                                            'Error EmergenciaSanitariaService.crearProcesoMasivo: No se pudo crear el Proceso Masivo: '
                                            .$arrayParametrosProceso['strTipoPma'].': '.$e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);

            return $strRespuesta;
        }
        return $strRespuesta;
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
     *              'strUsrCreacion'          => Usuario de Creación,
     *              'strIpCreacion'           => Ip de Creación    
     *     
     * @return $arrayResultado
     */
    public function crearSolicitudesNci($arrayParametros)
    {          
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];        
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];

        $this->emcom->beginTransaction();
        try
        {
            $arrayResultado = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                      ->crearSolicitudesNci($arrayParametros);
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strResultado = "No se pudo crear la Solicitud de Diferido por Emergencia Sanitaria: ".$arrayParametros['strTipoPma'].", <br> ". 
                             $e->getMessage(). ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'EmergenciaSanitariaService.crearSolicitudesNci',
                                            'Error EmergenciaSanitariaService.crearSolicitudesNci: No se pudo crear la Solicitud de Diferido por '
                                            . 'Emergencia Sanitaria: '
                                            .$arrayParametros['strTipoPma'].': '.$e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            
           $arrayResultado = array ('strResultado'           => $strResultado,
                                    'intIdProcesoMasivoCab'  => null);
            return $arrayResultado;
        }
        return $arrayResultado;
    }
    /**
     * ejecutaSolDiferido
     * 
     * Método que genera las Notas de Crédito Internas en base a las Solicitudes de Diferido 
     *  "SOLICITUD DIFERIDO DE FACTURA POR EMERGENCIA SANITARIA".     
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
     * @return $strRespuesta
     */
    public function ejecutaSolDiferido($arrayParametros)
    {          
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];        
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];

        $this->emcom->beginTransaction();
        try
        {            
            $strRespuesta   = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                      ->ejecutaSolDiferido($arrayParametros);
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo crear Nota de Credito Interna por Solicitud de Diferido por Emergencia Sanitaria, <br>".
                             $e->getMessage(). ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'EmergenciaSanitariaService.ejecutaSolDiferido',
                                            'Error EmergenciaSanitariaService.ejecutaSolDiferido: No se pudo crear NCI por Solicitud de Diferido '
                                            . 'por Emergencia Sanitaria: '
                                            .$arrayParametros['strDescripcionSolicitud'].': '.$e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);

            return $strRespuesta;
        }
        return $strRespuesta;
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
        $strUsrCreacion                 = $arrayParametros['strUsrCreacion'];        
        $strIpCreacion                  = $arrayParametros['strIpCreacion'];

        $this->emcom->beginTransaction();
        try
        {            
            $strRespuesta   = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                      ->ejecutaNdiDiferido($arrayParametros);
        }
        catch(\Exception $e)
        {
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo generar las NDI (Notas de débito interna) por las cuotas diferidas por Emergencia Sanitaria, <br>".
                             $e->getMessage(). ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'EmergenciaSanitariaService.ejecutaNdiDiferido',
                                            'Error EmergenciaSanitariaService.ejecutaNdiDiferido: No se pudo generar las NDI '
                                            . ' (Notas de débito interna) por las cuotas diferidas por Emergencia Sanitaria: '
                                            .$e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);

            return $strRespuesta;
        }
        return $strRespuesta;
    }
}
