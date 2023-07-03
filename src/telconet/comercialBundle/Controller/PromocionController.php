<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

/**
 * Promoción controller.
 *
 */
class PromocionController extends Controller implements TokenAuthenticatedController
{

    /**
    * @Secure(roles="ROLE_431-1")
    * crearPromoInstalacionAction Función para el ingreso de nuevas Promociones en instalación.
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 25-03-2019
    *                     
    * @return \telconet\comercialBundle\Controller\Renders
    */
    public function crearPromoInstalacionAction()
    {
        
        return $this->render('comercialBundle:promocion:crearPromoInstalacion.html.twig', array());
    }
    /**
    * @Secure(roles="ROLE_431-1") 
    * ajaxGuardarPromoInstalacionAction Función guarda nueva Promoción en instalación.
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 1-07-2022 - Se agrega función json_decode() para decodificar el JSON 
    *                          de Emisores y Sectorización enviados desde la peticion ajax de la interfaz,
    *                          la cual se utilizó para recibir una gran cantidad de información de la misma. 
    *              
    * @return $strResponse
    *
    */    
    public function ajaxGuardarPromoInstalacionAction()
    {                      
        $objRequest           = $this->getRequest();
        $objSesion            = $objRequest->getSession();
        $strNombrePromocion   = $objRequest->get('strNombrePromocion');
        $strInicioVigencia    = $objRequest->get('strInicioVigencia');
        $strFinVigencia       = $objRequest->get('strFinVigencia');
        $arrayTiposNegocio    = $objRequest->get('arrayTiposNegocio');        
        $arrayFormasPago      = $objRequest->get('arrayFormasPago');
        $arrayUltimaMilla     = $objRequest->get('arrayUltimasMilla');
        $arrayEstadoServicio  = $objRequest->get('arrayEstadoServicio');                
        $arrayEmisores        = json_decode($objRequest->get('arrayEmisores'),true); 
        $arrayPeriodo         = $objRequest->get('arrayPeriodo'); 
        $strUsrCreacion       = $objSesion->get('user');
        $strCodEmpresa        = $objSesion->get('idEmpresa');        
        $strCodigoPromocion   = $objRequest->get('strCodigoPromocion');
        $strIpCreacion        = $objRequest->getClientIp();        
        $arrayParametros      = array();        
        $strIdsTiposNegocio   = implode(",", $arrayTiposNegocio);
        $strIdsFormasPago     = implode(",", $arrayFormasPago);
        $strIdsUltimaMilla    = implode(",", $arrayUltimaMilla);
        $strIdsEstadoServicio = implode(",", $arrayEstadoServicio);        
        $strPeriodos          = implode(",", $arrayPeriodo);         
        $arraySectorizacion   = json_decode($objRequest->get('arraySectorizacion'),true);
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $strTipoEdicion       = $objRequest->get('strTipoEdicion');
        $intIdPromocionOrigen = $objRequest->get('intIdPromocionOrigen');
        
        try
        {            
            $arrayParametros = array('strNombrePromocion'     => $strNombrePromocion,
                                     'strInicioVigencia'      => $strInicioVigencia,
                                     'strFinVigencia'         => $strFinVigencia,
                                     'strIdsTiposNegocio'     => $strIdsTiposNegocio,                                     
                                     'strIdsFormasPago'       => $strIdsFormasPago,
                                     'strIdsUltimaMilla'      => $strIdsUltimaMilla,
                                     'strIdsEstadoServicio'   => $strIdsEstadoServicio,
                                     'arrayEmisores'          => $arrayEmisores,
                                     'strPeriodos'            => $strPeriodos,                                    
                                     'arraySectorizacion'     => $arraySectorizacion,
                                     'strUsrCreacion'         => $strUsrCreacion,
                                     'strCodEmpresa'          => $strCodEmpresa,
                                     'strIpCreacion'          => $strIpCreacion,
                                     'strTipoEdicion'         => $strTipoEdicion,
                                     'intIdPromocionOrigen'   => $intIdPromocionOrigen,
                                     'strCodigoPromocion'     => $strCodigoPromocion
                                    );
            $servicePromocionInstalacion = $this->get('comercial.PromocionInstalacion');
            $strResponse                 = $servicePromocionInstalacion->guardarPromoInstalacion($arrayParametros);            
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al ingresar una nueva promoción de instalación, por favor consulte con el Administrador";           
        }

        return new Response($strResponse);

    }
    /**
    * @Secure(roles="ROLE_431-1") 
    * ajaxEditarPromoInstalacionAction Función actualiza una Promoción en instalación.
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 12-04-2019
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 1-07-2022 - Se agrega función json_decode() para decodificar el JSON 
    *                          de Emisores y Sectorización enviados desde la peticion ajax de la interfaz,
    *                          la cual se utilizó para recibir una gran cantidad de información de la misma.   
    *             
    * @return $strResponse
    *
    */    
     public function ajaxEditarPromoInstalacionAction()
    {                      
        $objRequest           = $this->getRequest();
        $objSesion            = $objRequest->getSession();
        $intIdPromocion       = $objRequest->get('intIdPromocion');
        $strNombrePromocion   = $objRequest->get('strNombrePromocion');
        $strInicioVigencia    = $objRequest->get('strInicioVigencia');
        $strFinVigencia       = $objRequest->get('strFinVigencia');
        $arrayTiposNegocio    = $objRequest->get('arrayTiposNegocio');        
        $arrayFormasPago      = $objRequest->get('arrayFormasPago');
        $arrayUltimaMilla     = $objRequest->get('arrayUltimasMilla');
        $arrayEstadoServicio  = $objRequest->get('arrayEstadoServicio');                
        $arrayEmisores        = json_decode($objRequest->get('arrayEmisores'),true); 
        $arrayPeriodo         = $objRequest->get('arrayPeriodo'); 
        $strCodigoPromocion   = $objRequest->get('strCodigoPromocion');
        $strCodigoPromocionIng= $objRequest->get('strCodigoPromocionIng');
        $strUsrUltMod         = $objSesion->get('user');
        $strCodEmpresa        = $objSesion->get('idEmpresa');        
        $strIpUltMod          = $objRequest->getClientIp();        
        $arrayParametros      = array();        
        $strIdsTiposNegocio   = implode(",", $arrayTiposNegocio);
        $strIdsFormasPago     = implode(",", $arrayFormasPago);
        $strIdsUltimaMilla    = implode(",", $arrayUltimaMilla);
        $strIdsEstadoServicio = implode(",", $arrayEstadoServicio);        
        $strPeriodos          = implode(",", $arrayPeriodo); 
        $arraySectorizacion   = json_decode($objRequest->get('arraySectorizacion'),true);
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        try
        {            
            $arrayParametros = array('intIdPromocion'         => $intIdPromocion,
                                     'strNombrePromocion'     => $strNombrePromocion,
                                     'strInicioVigencia'      => $strInicioVigencia,
                                     'strFinVigencia'         => $strFinVigencia,
                                     'strIdsTiposNegocio'     => $strIdsTiposNegocio,                                     
                                     'strIdsFormasPago'       => $strIdsFormasPago,
                                     'strIdsUltimaMilla'      => $strIdsUltimaMilla,
                                     'strIdsEstadoServicio'   => $strIdsEstadoServicio,
                                     'arrayEmisores'          => $arrayEmisores,
                                     'strPeriodos'            => $strPeriodos,
                                     'arraySectorizacion'     => $arraySectorizacion,
                                     'strUsrUltMod'           => $strUsrUltMod,
                                     'strCodEmpresa'          => $strCodEmpresa,
                                     'strIpUltMod'            => $strIpUltMod,
                                     'strCodigoPromocion'     => $strCodigoPromocion,
                                     'strCodigoPromocionIng'  => $strCodigoPromocionIng 
                                    );            
            $servicePromocionInstalacion  = $this->get('comercial.PromocionInstalacion');
            $strResponse                  = $servicePromocionInstalacion->editarPromoInstalacion($arrayParametros);              
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al actualizar la promoción de instalación, por favor consulte con el Administrador";           
        }
        return new Response($strResponse);
    }
    /**
    * @Secure(roles="ROLE_431-1")  
    * ajaxInactivarPromocionesAction.
    * Función que crea un Proceso Masivo para la Inactivación  y/o DarBajaPromo de las Promociones.    
    * Tipos de PMA para promociones :InactivarPromo y/o DarBajaPromo, ClonarPromo.
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 03-04-2019
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.1 24-10-2019 - Se agrega validación para verificar si la fecha de activación es menor a la 
    * fecha actual, se cambia palabra Inactivar por Anular.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.2 14-08-2020 - Se agrega validación para Anular promociones en su fecha de vigencia si poseen 
    * el rol respectivo.
    *
    * @author Daniel Reyes <djreyes@telconet.ec>
    * @version 1.3 20-04-2022 - Se crea validacion para que promociones por franja horaria no pueda hacer
    *                           inactivaciones masivas.
    *             
    * @param $intIdMotivo,             Motivo de Inactivación y/o DarBajaPromo de la Promoción.
    * @param $strObservacion,          Observación de la Inactivación y/o DarBajaPromo de la Promoción.
    * @param arrayIdsGrupoPromocion,   Array con los ids de los grupos de Promociones ADMI_GRUPO_PROMOCION.       
    * 
    * @return $strResponse
    *
    */    
    public function ajaxInactivarPromocionesAction()
    {                      
        $objRequest               = $this->getRequest();
        $objSesion                = $objRequest->getSession();
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $intIdMotivo              = $objRequest->get('intIdMotivo');       
        $strObservacion           = $objRequest->get('strObservacion');       
        $arrayIdsGrupoPromocion   = $objRequest->get('arrayIdsGrupoPromocion'); 
        $strTipoPma               = $objRequest->get('strTipoPma'); 
        $strAccion                = $objRequest->get('strAccion'); 
        $strIdsGrupoPromocion     = implode(",", $arrayIdsGrupoPromocion);      
        $strUsrCreacion           = $objSesion->get('user');
        $strCodEmpresa            = $objSesion->get('idEmpresa');        
        $strIpCreacion            = $objRequest->getClientIp();                        
        $arrayParametros          = array();                        
        $arrayParametros          = array(
                                          'strIdsGrupoPromocion'   => $strIdsGrupoPromocion,
                                          'intIdMotivo'            => $intIdMotivo,
                                          'strObservacion'         => $strObservacion,
                                          'strUsrCreacion'         => $strUsrCreacion,
                                          'strCodEmpresa'          => $strCodEmpresa,
                                          'strIpCreacion'          => $strIpCreacion,
                                          'strTipoPma'             => $strTipoPma
                                    );    
        
        // Validamos si es un tipo de promocion permitido
        $servicePromocion   = $this->get('comercial.Promocion');
        $intPromosNoMasivas = $servicePromocion->validaPromocionesMasivas(array('arrayIdsGrupoPromocion' => $arrayIdsGrupoPromocion,
                                                                                'strCodEmpresa'          => $strCodEmpresa));
        if($intPromosNoMasivas > 0)
        {
            $strResponse = "Existen promociones que no pueden ejecutar {$strAccion} en masivo sino individual";
            return new Response($strResponse);
        }
        
        $intCantidad              = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                   ->validaGruposPromocionActivas(array('arrayIdsGrupoPromocion' => $arrayIdsGrupoPromocion,
                                                                        'strEstado'              => 'Activo'));  
        
        $intCantidadFechaVigencia = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                   ->validaGruposPromocionFechaInicio(array('arrayIdsGrupoPromocion' => $arrayIdsGrupoPromocion));  

        if($intCantidad>0)
        {
            $strResponse = "Solo puede {$strAccion}, Promociones en estado Activo, por favor revise las promociones seleccionadas";
            return new Response($strResponse);
        }
        
        if (((false === $this->get('security.context')->isGranted('ROLE_431-7517')) && $intCantidadFechaVigencia>0) 
             || false === $this->get('security.context')->isGranted('ROLE_431-7517'))
	    {
            $strResponse = "Solo puede {$strAccion} Promociones antes de su fecha de Inicio Vigencia, por favor revise las promociones seleccionadas"
                    . " o solicite el rol correspondiente para {$strAccion}  promociones en cualquier fecha de vigencia.";
            return new Response($strResponse);
        }

        try
        {             
           
            $servicePromocion = $this->get('comercial.Promocion');
            $strResponse      = $servicePromocion->crearProcesoMasivo($arrayParametros);            
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al {$strAccion}, la(s) Promocion(es), por favor consulte con el Administrador";           
        }

        return new Response($strResponse);

    }
     /**
     * getMotivos, obtiene la información de las motivos para Inactivar o clonar promociones.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-04-2019
     *  
     *                    
     * @return Response lista de Motivos
     */
    public function getMotivosAction()
    {
        $arrayParametros                         = array();        
        $arrayParametros['arrayEstadoMotivos']   = array('Activo');  
        $arrayParametros['strNombreModulo']      = 'promocion';          
        $servicePromocion = $this->get('comercial.Promocion');
        $arrayMotivos     = $servicePromocion->getMotivos($arrayParametros);
        $objResponse      = new Response(json_encode(array('motivos'=> $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;	
    }      
    
     /**
     * getFormasPagoAction, obtiene la información de las Formas de Pago que se encuentran parametrizados en ADMI_PARAMETRO_CAB.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-04-2019          
     *                    
     * @return Response lista de Formas de Pago.
     */
    public function getFormasPagoAction()
    {                      
        $strEmpresaCod  = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral      = $this->get('doctrine')->getManager('telconet');
        
        $arrayListFormasPago  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PROD_PROM_FORMA_PAGO', 'COMERCIAL', '', '', '', '', '', '', '', $strEmpresaCod,'valor3');
        $arrayFormasPago      = array();
        
        foreach($arrayListFormasPago as $objFormaPago)
        {
            $arrayFormasPago[] = array('id' => $objFormaPago['valor3'], 'nombre' => $objFormaPago['valor1']);
        }              
        $objResponse = new Response(json_encode(array('formas_de_pago' => $arrayFormasPago)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }      
     
    /**
     * getPeriodosAction, obtiene los períodos que se encuentran parametrizados en ADMI_PARAMETRO_CAB.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 18-03-2019        
     *                    
     * @return Response lista de Períodos parametrizados.
     */
    public function getPeriodosAction()
    {
        $strEmpresaCod  = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral      = $this->get('doctrine')->getManager('telconet');
        
        $arrayListPeriodos  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PROD_PROM_PERIODO', 'COMERCIAL', '', '', '', '', '', '', '', $strEmpresaCod,'valor3');
        $arrayPeriodos = array();
        
        foreach($arrayListPeriodos as $objPeriodo)
        {
            $arrayPeriodos[] = array('id' => $objPeriodo['valor1'], 'nombre' => $objPeriodo['valor1']);
        }      
        sort($arrayPeriodos);
        $objResponse = new Response(json_encode(array('periodos' => $arrayPeriodos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
   /**
     * getTiposNegocio, obtiene los tipos de Negocio por empresa en estado activo.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-03-2019
     *      
     * @param array $arrayParametros[]                  
     *              'strEstado'          => Estado del tipo de Negocio.    
     *              'strEmpresaCod'      => Empresa en sesión.
     *                    
     * @return Response lista de Tipos de Negocio.
     */
    public function getTiposNegocioAction()
    {       
        $objRequest                       = $this->getRequest();  
        $strEmpresaCod                    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros                  = array();        
        $arrayParametros['strEstado']     = array('Activo');     
        $arrayParametros['strEmpresaCod'] = $strEmpresaCod;     
                
        $servicePromocion   = $this->get('comercial.Promocion');
        $arrayTiposNegocio  = $servicePromocion->getTiposNegocio($arrayParametros);
        $objResponse        = new Response(json_encode(array('tipos_de_negocio'=> $arrayTiposNegocio)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;	
    }       
     /**
     * getUltimaMillaAction, obtiene las últimas milla en estado Activo.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-03-2019
     *
     * @param strCodTipoPromocion Código del Tipo de Promoción PROM_INS: Promoción por Instalación, PROM_BW: Promoción por Ancho de Banda.
     *
     * @return Response lista de últimas milla.
     */
    public function getUltimaMillaAction()
    {   
        $objRequest             = $this->getRequest();        
        $strCodTipoPromocion    = $objRequest->get("strCodTipoPromocion") ? $objRequest->get("strCodTipoPromocion") : "X";
        $strEmpresaCod          = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral              = $this->get('doctrine')->getManager('telconet');
        
        $arrayListUltimasMillas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PROD_PROM_ULTIMA_MILLA','COMERCIAL', '', '', '', '','', $strCodTipoPromocion, 
                                                  '', $strEmpresaCod,'valor3');
        $arrayUltimasMillas     = array();
        
        foreach($arrayListUltimasMillas as $objUltimasMillas)
        {
            $arrayUltimasMillas[] = array('id' => $objUltimasMillas['valor3'], 'nombre' => $objUltimasMillas['valor1']);
        }              
        $objResponse = new Response(json_encode(array('ultimas_millas' => $arrayUltimasMillas)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }      
     /**
     * getEstadosAction, obtiene los estados de los servicios que se encuentran parametrizados en ADMI_PARAMETRO_CAB.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-03-2019        
     *                    
     * @return Response lista de Estados parametrizados.
     */
    public function getEstadosServAction()
    {
        $strEmpresaCod  = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral      = $this->get('doctrine')->getManager('telconet');
        
        $arrayListEstados  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->get('PROD_PROM_ESTADO_SERVICIO', 'COMERCIAL', '', '', '', '', '', '', '', $strEmpresaCod,'valor3');
        $arrayEstados= array();
        
        foreach($arrayListEstados as $objEstados)
        {
            $arrayEstados[] = array('id' => $objEstados['valor1'], 'nombre' => $objEstados['valor1']);
        }              
        $objResponse = new Response(json_encode(array('estados_servicios' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_431-1")  
     * indexAction()
     * Función que renderiza la página principal de las Promociones.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 07-02-2019
     * @since 1.0
     * 
     * @return render - Página de Consulta de Promociones.
     */
    public function indexAction()
    {
        return $this->render('comercialBundle:promocion:index.html.twig', array());
    }
    
    /**
     * @Secure(roles="ROLE_431-1")  
     * crearPromoMensualidadAction()
     * Función que renderiza la página de Crear Promoción de Mensualidad.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * @since 1.0
     * 
     * @return render - Página de Crear Promoción de Mensualidad.
     */
    public function crearPromoMensualidadAction()
    {
        return $this->render('comercialBundle:promocion:crearPromoMensualidad.html.twig', array());
    }
    
    /**
     * @Secure(roles="ROLE_431-1")  
     * ajaxGuardarPromoMensualidadAction()
     * Función guarda nueva Promoción de Mensualidad.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 03-04-2019
     * @since 1.0
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 1-07-2022 - Se agrega función json_decode() para decodificar el JSON 
     *                          de Emisores y Sectorización enviados desde la peticion ajax de la interfaz,
     *                          la cual se utilizó para recibir una gran cantidad de información de la misma. 
     *    
     * @return $strResponse - Mensaje de estado de la transacción.
     */
    public function ajaxGuardarPromoMensualidadAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $arrayConfGenerales     = $objRequest->get('arrayConfGenerales');
        $arraySectorizacion     = json_decode($arrayConfGenerales['arraySectorizacion'],true);
        $strNombrePromocion     = $arrayConfGenerales['strNombrePromocion'];
        $strIdsEstadoServicio   = implode(",", $arrayConfGenerales['arrayEstadoServicio']);
        $strInicioVigencia      = $arrayConfGenerales['strInicioVigencia'];
        $strFinVigencia         = $arrayConfGenerales['strFinVigencia'];
        $strIdsFormasPago       = implode(",", $arrayConfGenerales['arrayFormaPago']);
        $arrayEmisores          = json_decode($arrayConfGenerales['arrayEmisores'],true);
        $strTipoCliente         = implode(",", $arrayConfGenerales['arrayTipoCliente']);
        $strPermMinimaCancelVol = $arrayConfGenerales['strPermMinimaCancelVol'];
        $arrayPromoMix          = $objRequest->get('arrayPromoMix');
        $arrayPromoPlanes       = $objRequest->get('arrayPromoPlanes');
        $arrayPromoProductos    = $objRequest->get('arrayPromoProductos');
        $arrayPromoDescTotal    = $objRequest->get('arrayPromoDescTotal');
        $strUsrCreacion         = $objSesion->get('user');
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $strIpCreacion          = $objRequest->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $strTipoEdicion         = $objRequest->get('strTipoEdicion');
        $intIdPromocionOrigen   = $objRequest->get('intIdPromocionOrigen');
        $strCodigoPromocion     = $objRequest->get('strCodigoPromocion');

        try
        {
            $arrayParametros = array('arraySectorizacion'     => $arraySectorizacion,
                                     'strNombrePromocion'     => $strNombrePromocion,
                                     'strIdsEstadoServicio'   => $strIdsEstadoServicio,
                                     'strInicioVigencia'      => $strInicioVigencia,
                                     'strFinVigencia'         => $strFinVigencia,
                                     'strIdsFormasPago'       => $strIdsFormasPago,
                                     'arrayEmisores'          => $arrayEmisores,
                                     'strTipoCliente'         => $strTipoCliente,
                                     'strUsrCreacion'         => $strUsrCreacion,
                                     'strCodEmpresa'          => $strCodEmpresa,
                                     'strIpCreacion'          => $strIpCreacion,
                                     'arrayPromoMix'          => $arrayPromoMix,
                                     'arrayPromoPlanes'       => $arrayPromoPlanes,
                                     'arrayPromoProductos'    => $arrayPromoProductos,
                                     'arrayPromoDescTotal'    => $arrayPromoDescTotal,
                                     'strTipoEdicion'         => $strTipoEdicion,
                                     'intIdPromocionOrigen'   => $intIdPromocionOrigen,
                                     'strCodigoPromocion'     => $strCodigoPromocion,
                                     'strPermMinimaCancelVol' => $strPermMinimaCancelVol
                                    );
            /* @var $servicePromocionMensualidad \telconet\comercialBundle\Service\PromocionMensualidadService */
            $servicePromocionMensualidad = $this->get('comercial.PromocionMensualidad');
            $strResponse                 = $servicePromocionMensualidad->guardarPromoMensualidad($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al ingresar una nueva Promoción de Mensualidad, por favor consulte con el Administrador.";
        }
        return new Response($strResponse);
    }

   /**
     * @Secure(roles="ROLE_431-1")  
     * gridPromocionesAction()
     * Función que obtiene un listado de promociones, por medio de los siguientes filtros: NombrePromoción, Estado, Fecha de Vigencia, Empresa.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 02-02-2019
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 04-10-2019 - Se obtiene el tipo de promoción para mostrarla en la consulta de promociones.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.2 01-12-2021 - Se aumenta validacion para detener promociones en estado pendiente y activos
     *                      que no esten en tiempo de ejecucion y se mejora la presentacion de la fecha para
     *                      incluir las horas y minutos si los tiene
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.3 07-03-2022 - Se corrige validacion en opcion de detener promocion que provocaba que el boton aparezca
     *                           una hora despues de finalizada la franja horaria
     *
     * @return $objResponse - Listado de Promociones
     */
    public function gridPromocionesAction()
    {
        $objRequest     = $this->getRequest();
        $strFechaPromo  = $objRequest->get("strInicioVigencia");
        $strEstadoPromo = $objRequest->get("strEstadoPromo");
        $strNombrePromo = $objRequest->get("strNombrePromo");
        $intIdEmpresa   = $objRequest->getSession()->get('idEmpresa');
        $emComercial    = $this->get('doctrine')->getManager('telconet');

        $arrayParametros                   = array();
        $arrayParametros['strEstadoPromo'] = $strEstadoPromo;
        $arrayParametros['intIdEmpresa']   = $intIdEmpresa;
        $arrayParametros['strFechaPromo']  = $strFechaPromo;
        $arrayParametros['strNombrePromo'] = $strNombrePromo;

        $arrayResultado   = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')->getPromocionesPorCriterios($arrayParametros);
        $arrayRegistros   = $arrayResultado['registros'];
        $intTotal         = $arrayResultado['total'];
        $arrayPromociones = array();

        foreach($arrayRegistros as $arrayDatos):    
            
            $strEstado           = $arrayDatos['strEstado'];
            $strNombrePromocion  = $arrayDatos['strNombreGrupo'];
            $intIdPromocion      = $arrayDatos['intIdGrupoPromocion'];
            $strFeInicioVigencia = strval(date_format($arrayDatos['dateFeInicioVigencia'], "Y-m-d"));
            $strFeFinVigencia    = strval(date_format($arrayDatos['dateFeFinVigencia'], "Y-m-d"));
            $strFeCreacion       = strval(date_format($arrayDatos['dateFeCreacion'], "Y-m-d"));
            $strUsrCreacion      = $arrayDatos['strUsrCreacion'];
            $strUrlEditar        = '';
            $strEditarPromo      = '';
            $strIdOrigen         = 'N';
            $strUrlEditarVigente = $strNombrePromocion;
            $strFechaActual      = date("Y-m-d");
            $intHoraActual       = intval(date("H"));
            
            /*Obtener caracteristica para saber si se deriba de una promoción anterior*/
            $objCaracteristica    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array('descripcionCaracteristica' => 'ORIGEN_PROMOCION_EDITADA',
                                                           'tipo'                      => 'COMERCIAL',
                                                           'estado'                    => 'Activo'
                                                           )
                                                     );
            if(!is_object($objCaracteristica))
            {
                throw new \Exception("No se pudo actualizar la Promoción, No se encontró la Regla definida para el Tipo de Promoción");

            }
            
            
            $objAdmiTipoPromocion = $emComercial->getRepository('schemaBundle:AdmiTipoPromocion')
                                   ->findBy(array("grupoPromocionId" => $intIdPromocion));
            if(empty($objAdmiTipoPromocion))
            {
                throw new \Exception("No se pudo ver el detalle de Promoción, No existe Tipo de Promoción.");
            }

            $strTipoPromocion = $objAdmiTipoPromocion[0]->getCodigoTipoPromocion();
            if($strTipoPromocion === "PROM_MIX"||$strTipoPromocion === "PROM_MPLA"||
               $strTipoPromocion === "PROM_MPRO"||$strTipoPromocion === "PROM_TOT")
            {
                  $objAdmiGrupoPromocionRegla    = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocionRegla')
                                         ->findOneBy(array('grupoPromocionId' => $intIdPromocion,
                                                           'caracteristicaId' => $objCaracteristica->getId(),
                                                           'estado'                    => 'Activo'
                                                           )
                                                     );        
                        
                      
                if($objAdmiGrupoPromocionRegla)
                { 
                   $strIdOrigen=$objAdmiGrupoPromocionRegla->getValor();
                }
              
            }
            else if ($strTipoPromocion === "PROM_BW" || $strTipoPromocion === "PROM_INS")
            {
                
                $arrayParametroTipoRegla = array('intTipoPromocionId'  => $objAdmiTipoPromocion[0]->getId(),
                                         'intCaracteristicaId' => $objCaracteristica->getId(),
                                         'arrayEstados'        => array('Eliminado')
                               );
                $objAdmiTipoPromocionRegla = $emComercial->getRepository("schemaBundle:AdmiGrupoPromocion")
                                                 ->getTipoPromocionReglaEstado($arrayParametroTipoRegla); 
        
                 
                if(is_object($objAdmiTipoPromocionRegla))
                {  
                   $strIdOrigen=$objAdmiTipoPromocionRegla->getValor();
                }
               
                
            }   
            
            if(($strEstado === 'Pendiente' || $strEstado === 'Programado' ) || 
                ($strEstado === 'Activo' && $strFechaActual<$strFeInicioVigencia))
            {
                $strUrlEditar = $this->generateUrl('promocion_detalleEditar', 
                        array('intIdPromocion' => $intIdPromocion,
                               'strTipoEdicion' => 'E,'.$strIdOrigen
                        ));
            }

            // Obtenemos los tipos de promociones que no generean link de clonacion
            $arrayPromocionesNoLink = null;
            $arrayDatosPromociones = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                            'Promociones que no generan link de clonacion en el nombre',
                                            '','','','','',
                                            $intIdEmpresa);
            foreach($arrayDatosPromociones as $objDatoPromocion)
            {
                $arrayPromocionesNoLink[] = $objDatoPromocion['valor1'];
            }
            
            if(($strEstado === 'Pendiente' || $strEstado === 'Activo') && $this->get('security.context')->isGranted('ROLE_431-7762'))
            {
                $strEditarPromo = $this->generateUrl('promocion_detalleEditar', 
                                                    array('intIdPromocion' => $intIdPromocion,
                                                           'strTipoEdicion' => 'ED,'.$strIdOrigen
                                                    ));
                // Se quita opcion de link de clonacion para las promociones de Ancho de Banda
                if(!empty($arrayPromocionesNoLink) && in_array($strTipoPromocion, $arrayPromocionesNoLink))
                {
                    $strUrlEditarVigente = $strNombrePromocion;
                }
                else
                {
                    $strUrlEditarVigente ="<a href='{$strEditarPromo}'>{$strNombrePromocion}</a>";
                }
            }

            // Se valida que para las promociones de Ancho de Banda tenga la opcion de detener si estan en pendiente
            $arrayEstadosDetener   = null;
            $arrayDatosDetener = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                            'Estados permitidos para detener la promocion',
                                            $strTipoPromocion,'','','','',
                                            $intIdEmpresa);
            foreach($arrayDatosDetener as $objDatoDetener)
            {
                $arrayEstadosDetener[] = $objDatoDetener['valor2'];
            }
            $strDatosDetener = "";
            if(!empty($arrayEstadosDetener) && in_array($strEstado, $arrayEstadosDetener))
            {
                // Obtenemos la hora de ejecucion de promocion
                $intHoraInicia = intval(date_format($arrayDatos['dateFeInicioVigencia'], "H"));
                $intHoraFinal  = intval(date_format($arrayDatos['dateFeFinVigencia'], "H"));
                if (($strFechaActual >= $strFeInicioVigencia && $strFechaActual <= $strFeFinVigencia) &&
                    ($intHoraActual >= $intHoraInicia && $intHoraActual < $intHoraFinal)
                    && $strEstado == 'Activo')
                {
                    $strDatosDetener = "";
                }
                else
                {
                    $strDatosDetener = array('intIdPromocion' => $intIdPromocion,
                                       'strTipoPromocion' => $strTipoPromocion);
                }
            }

            // Se valida que para las promociones de Ancho de Banda apareca la opcion de Anular
            $arrayEstadosAnula   = null;
            $arrayDatosParametros = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                            'Estados permitidos para anular la promocion',
                                            $strTipoPromocion,'','','','',
                                            $intIdEmpresa);
            foreach($arrayDatosParametros as $objDatoParametro)
            {
                $arrayEstadosAnula[] = $objDatoParametro['valor2'];
            }
            $strDatosAnular = "";
            if(!empty($arrayEstadosAnula) && in_array($strEstado, $arrayEstadosAnula))
            {
                $strDatosAnular = array('intIdPromocion' => $intIdPromocion,
                                        'strTipoPromocion' => $strTipoPromocion,
                                        'strEstadoPromocion' => $strEstado);
            }

            // Cambiamos para que muestre las fechas completas
            $strInicioVigencia = strval(date_format($arrayDatos['dateFeInicioVigencia'], "Y-m-d H:i:s"));
            $strFinVigencia    = strval(date_format($arrayDatos['dateFeFinVigencia'], "Y-m-d H:i:s"));
            
            $arrayPromociones[] = array('intIdPromocion'         => $intIdPromocion,
                                        'strNombrePromocion'     => $strUrlEditarVigente,
                                        'strFechaInicioVigencia' => $strInicioVigencia,
                                        'strFechaFinVigencia'    => $strFinVigencia,
                                        'strEstado'              => $strEstado,
                                        'strFeCreacion'          => $strFeCreacion,
                                        'strUsrCreacion'         => $strUsrCreacion,
                                        'strTipoPromocion'       => $arrayDatos['strTipoPromocion'],
                                        'strAcciones'            => array('linkVer'    => $this->generateUrl('promocion_detalle', 
                                                                                          array('intIdPromocion' => $intIdPromocion,
                                                                                                 'strIdOrigen'=>$strIdOrigen)),
                                                                          'linkEditar'  => $strUrlEditar,
                                                                          'linkDetener' => $strDatosDetener,
                                                                          'linkAnular'  => $strDatosAnular)
                                       );
        endforeach;

        if(empty($arrayPromociones))
        {
            $arrayPromociones[] = array('intIdPromocion'         => "",
                                        'strNombrePromocion'     => "",
                                        'strFechaInicioVigencia' => "",
                                        'strFechaFinVigencia'    => "",
                                        'strEstado'              => "",
                                        'strFeCreacion'          => "",
                                        'strUsrCreacion'         => "",
                                        'strTipoPromocion'       => "",
                                        'strAcciones'            => array('linkVer' => '', 'linkEditar' => '')
                                       );
        }

        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayPromociones)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

  /**
     * getOltsAction()
     * Función que obtiene listado de los Olt's mediante el idParroquia, idEmpresa y tipo de elemento.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-03-2019
     * @since 1.0
     *    
     * @return $objResponse - Listado de Olt's.
     */
    public function getOltsAction()
    {
        $objRequest      = $this->getRequest();
        $intIdParroquia  = $objRequest->get('intIdparroquia');
        $intIdEmpresa    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros = array('intIdParroquia'  => $intIdParroquia,
                                 'intIdEmpresa'    => $intIdEmpresa,
                                 'strTipoElemento' => 'OLT'
                                );

        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion = $this->get('comercial.Promocion');
        $arrayOlts        = $servicePromocion->getOlts($arrayParametros);

        $objResponse = new Response(json_encode(array('olts' => $arrayOlts)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * getEdificiosAction()
     * Función que obtiene listado de los Edificios mediante el idParroquia, idEmpresa y tipo de elemento.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 08-05-2019
     * @since 1.0
     *    
     * @return $objResponse - Listado de Edificios.
     */
    public function getEdificiosAction()
    {
        $objRequest      = $this->getRequest();
        $intIdParroquia  = $objRequest->get('intIdparroquia');
        $intIdEmpresa    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros = array('intIdParroquia'   => $intIdParroquia,
                                 'intIdEmpresa'     => $intIdEmpresa,
                                 'strTipoElemento'  => 'EDIFICACION'
                                );

        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion = $this->get('comercial.Promocion');
        $arrayEdificios   = $servicePromocion->getEdificios($arrayParametros);

        $objResponse = new Response(json_encode(array('edificios' => $arrayEdificios)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }    
    
    /**
     * getEstadosAction()
     * Función que retorna los estados para consulta de las Promociones.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-04-2019
     * @since 1.0
     *    
     * @return $objResponse - Listado de Estados.
     */
    public function getEstadosAction() 
    {
        $arrayEstados[] = array('id' => 'Activo', 'nombre' => 'Activo');
        $arrayEstados[] = array('id' => 'Inactivo', 'nombre' => 'Inactivo');
        $arrayEstados[] = array('id' => 'Pendiente', 'nombre' => 'Pendiente');
        $arrayEstados[] = array('id' => 'Anulado', 'nombre' => 'Anulado');
        $arrayEstados[] = array('id' => 'Cancel', 'nombre' => 'Cancel');		

        $objResponse = new Response(json_encode(array('estados' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
    * @Secure(roles="ROLE_431-1")    
    * ajaxClonarPromocionesAction()
    * Función que crea un Proceso Masivo para la Clonación de las Promociones.    
    * Tipos de PMA para promociones : ClonarPromo 
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 09-04-2019
    *             
    * @param $intIdMotivo,             Motivo de Clonación de la Promoción
    * @param $strObservacion,          Observación de la Clonación de la Promoción
    * @param arrayIdsGrupoPromocion,   Array con los ids de los grupos de Promociones ADMI_GRUPO_PROMOCION       
    * 
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 18-07-2022 - Se obtiene lista de los Grupos promocionales en PMA para validar que un grupo promocional no se encuentre
    *                           en un proceso masivo pendiente para poder procesar la Clonacion.
    *                           Se parametriza mensaje de validacion a presentar  en 'MENSAJE_VALIDACION_CLONA_PROM'
    *  
    * @return $strResponse
    */    
    public function ajaxClonarPromocionesAction()
    {                      
        $objRequest               = $this->getRequest();
        $objSesion                = $objRequest->getSession();
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $intIdMotivo              = $objRequest->get('intIdMotivo');       
        $strObservacion           = $objRequest->get('strObservacion');       
        $arrayIdsGrupoPromocion   = $objRequest->get('arrayIdsGrupoPromocion'); 
        $strIdsGrupoPromocion     = implode(",", $arrayIdsGrupoPromocion);      
        $strUsrCreacion           = $objSesion->get('user');
        $strCodEmpresa            = $objSesion->get('idEmpresa');        
        $strIpCreacion            = $objRequest->getClientIp();      
        $strGruposPromocionesPma  = "";
        $intContador              = 1;
        $boolGruposPromocionesPma = false;
        $arrayParametros          = array();                        
        $arrayParametros          = array(
                                          'strIdsGrupoPromocion'   => $strIdsGrupoPromocion,
                                          'intIdMotivo'            => $intIdMotivo,
                                          'strObservacion'         => $strObservacion,
                                          'strUsrCreacion'         => $strUsrCreacion,
                                          'strCodEmpresa'          => $strCodEmpresa,
                                          'strIpCreacion'          => $strIpCreacion,
                                          'strTipoPma'             => 'ClonarPromo'
                                         );    
        
        $arrayGruposPromoPma     = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                   ->getGruposPromocionesPma(array('strTipoProceso'         => 'ClonarPromo',
                                                                   'arrayIdsGrupoPromocion' => $arrayIdsGrupoPromocion,
                                                                   'strEstado'              => 'Pendiente'));  
                
        foreach($arrayGruposPromoPma as $objGruposPromoPma)
        {            
            $strGruposPromocionesPma = $intContador == 1 ? $objGruposPromoPma['nombre_grupo'] : $strGruposPromocionesPma . 
                    '' .$objGruposPromoPma['nombre_grupo']; 
            $strGruposPromocionesPma .= "<br>";
            $intContador ++;
            $boolGruposPromocionesPma = true;
        }        
        if($boolGruposPromocionesPma)
        {
            
            //Se obtiene mensaje  "Grupos promocionales se encuentran en un proceso masivo pendiente"
            $arrayMsjGruposPromocionesPma = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PROM_PARAMETROS',
                                                     'COMERCIAL','','','MENSAJE_VALIDACION_CLONA_PROMO','',
                                                     '','','',$strCodEmpresa);
                                   
            $strMsjGruposPromocionesPma  = (isset($arrayMsjGruposPromocionesPma["valor1"])
                                       && !empty($arrayMsjGruposPromocionesPma["valor1"])) ? $arrayMsjGruposPromocionesPma["valor1"]
                                       : 'Existe(n) grupo(s) promocional(es) en proceso de Clonación: <br> strGruposPromocionesPma';
                        
            $strMsjGruposPromocionesPma = str_replace("strGruposPromocionesPma", $strGruposPromocionesPma, $strMsjGruposPromocionesPma);
           
            return new Response($strMsjGruposPromocionesPma);
        }
        try
        {                        
            $servicePromocion = $this->get('comercial.Promocion');
            $strResponse      = $servicePromocion->crearProcesoMasivo($arrayParametros);                
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al Inactivar la(s) Promocion(es), por favor consulte con el Administrador.";           
        }

        return new Response($strResponse);
    }
    
    /**
     * getProductosAction()
     * Función que obtiene un listado de productos mediante el filtro de Nombre de Producto y el idEmpresa.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-04-2019
     * @since 1.0
     *    
     * @return $objResponse - Listado de Productos.
     */
    public function getProductosAction()
    {
        $objRequest      = $this->getRequest();
        $intIdEmpresa    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros = array('intIdEmpresa' => $intIdEmpresa);

        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion = $this->get('comercial.Promocion');
        $arrayProductos   = $servicePromocion->getProductos($arrayParametros);

        $objResponse = new Response(json_encode(array('productos' => $arrayProductos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

     /**
     * getPlanesAction()
     * Función que obtiene un listado de planes mediante el filtro de Nombre de Planes y idEmpresa.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-04-2019
     * @since 1.0
     *    
     * @return $objResponse - Listado de Planes.
     */
    public function getPlanesAction()
    {
        $objRequest       = $this->getRequest();
        $strIdTipoNegocio = ($objRequest->get('idTipoNegocio')) ? $objRequest->get('idTipoNegocio') : '' ;
        $intIdEmpresa     = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros  = array('intIdEmpresa'     => $intIdEmpresa,
                                  'strIdTipoNegocio' => $strIdTipoNegocio);

        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion = $this->get('comercial.Promocion');
        $arrayPlanes      = $servicePromocion->getPlanes($arrayParametros);

        $objResponse = new Response(json_encode(array('planes' => $arrayPlanes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getPlanesNoSeleccionadosAction()
     * Función que obtiene un listado de planes no seleccionados en otras promociones en base al tipo de empresa
     * y el tipo de promocion.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 26-11-2021
     * @since 1.0
     *    
     * @return $objResponse - Listado de Planes.
    */
    public function getPlanesNoSeleccionadosAction()
    {
        $objRequest         = $this->getRequest();
        $strIdTipoPromocion = ($objRequest->get('strIdTipoPromocion')) ? $objRequest->get('strIdTipoPromocion') : '' ;
        $intIdEmpresa       = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros    = array('intIdEmpresa'       => $intIdEmpresa,
                                    'strIdTipoPromocion' => $strIdTipoPromocion);
        $servicePromocion   = $this->get('comercial.Promocion');
        $arrayPlanes        = $servicePromocion->getPlanesNoSeleccionados($arrayParametros);
        $objResponse = new Response(json_encode(array('planes' => $arrayPlanes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getPlanesDestinoAction()
     * Función que obtiene un listado de planes dependiendo del tipo de negocio del plan seleccionado.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 26-11-2021
     * @since 1.0
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 15-09-2022 
     * Se incluyo en el arreglo el parametro 'strFiltraEstProm', utilizada para validar 
     * el filtro de los estados en el query que obtiene los planes destinos para crear promociones 
     * de Franja Horaria.
     *    
     * @return $objResponse - Listado de Planes.
    */
    public function getPlanesDestinoAction()
    {
        $objRequest       = $this->getRequest();
        $intIdPlan        = $objRequest->get('idPlan');
        $intIdEmpresa     = $objRequest->getSession()->get('idEmpresa');
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        // Obtenemos el tipo de negocio del plan
        $objPlan = $emComercial->getRepository('schemaBundle:InfoPlanCab')->findOneById($intIdPlan);
        $strIdTipoNegocio = $objPlan->getTipo();
        $arrayParametros  = array('intIdEmpresa'     => $intIdEmpresa,
                                  'strIdTipoNegocio' => $strIdTipoNegocio,
                                  'strFiltraEstProm' => 'SI');

        $servicePromocion = $this->get('comercial.Promocion');
        $arrayPlanes      = $servicePromocion->getPlanes($arrayParametros);

        $objResponse = new Response(json_encode(array('planes' => $arrayPlanes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getPermanenciasMinimasAction()
     * Función que obtiene un listado de permanencias mínimas mediante el filtro de idEmpresa.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-03-2019
     * @since 1.0
     *    
     * @return $objResponse - Listado de Permanencias Mínimas.
     */
    public function getPermanenciasMinimasAction()
    {
        $objRequest      = $this->getRequest();
        $intIdEmpresa    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros = array('intIdEmpresa' => $intIdEmpresa);

        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion       = $this->get('comercial.Promocion');
        $arrayPermanenciaMinima = $servicePromocion->obtenerPermanenciaMinima($arrayParametros);

        $objResponse = new Response(json_encode(array('permanenciasMinimas' => $arrayPermanenciaMinima)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    
    /**
     * getPermanenciaPromoCancelVolAction()
     * Función que obtiene un listado de permanencias mínimas utilizadas para la cancelacion voluntaria.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 25-07-2012
     * @since 1.0
     *    
     * @return $objResponse - Listado de Permanencias Mínimas.
     */
    public function getPermanenciaPromoCancelVolAction()
    {
        $objRequest      = $this->getRequest();
        $intIdEmpresa    = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros = array('intIdEmpresa' => $intIdEmpresa);

        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion               = $this->get('comercial.Promocion');
        $arrayPermanenciaPromoCancelVol = $servicePromocion->obtenerPermanenciaMinPromoCancelVol($arrayParametros);

        $objResponse = new Response(json_encode(array('permanenciaMinimaPromoCancelVol' => $arrayPermanenciaPromoCancelVol)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
    * @Secure(roles="ROLE_431-1")
    * ajaxEditarPromoMensualidadAction Función actualiza una Promoción de Mensualidad.
    *
    * @author Hector Lozano<hlozano@telconet.ec>
    * @version 1.0 12-04-2019
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 1-07-2022 - Se agrega función json_decode() para decodificar el JSON 
    *                          de Emisores y Sectorización enviados desde la peticion ajax de la interfaz,
    *                          la cual se utilizó para recibir una gran cantidad de información de la misma.  
    *             
    * @return $strResponse
    *
    */   
     public function ajaxEditarPromoMensualidadAction()
    {                     
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $intIdPromocion         = $objRequest->get('intIdPromocion');
        $arrayConfGenerales     = $objRequest->get('arrayConfGenerales');
        $arraySectorizacion     = json_decode($arrayConfGenerales['arraySectorizacion'],true);
        $strNombrePromocion     = $arrayConfGenerales['strNombrePromocion'];
        $strIdsEstadoServicio   = implode(",", $arrayConfGenerales['arrayEstadoServicio']);
        $strInicioVigencia      = $arrayConfGenerales['strInicioVigencia'];
        $strFinVigencia         = $arrayConfGenerales['strFinVigencia'];
        $strIdsFormasPago       = implode(",", $arrayConfGenerales['arrayFormaPago']);
        $strTipoCliente         = implode(",", $arrayConfGenerales['arrayTipoCliente']);
        $strPermMinimaCancelVol = $arrayConfGenerales['strPermMinimaCancelVol'];
        $arrayEmisores          = json_decode($arrayConfGenerales['arrayEmisores'],true);
        $arrayPromoMix          = $objRequest->get('arrayPromoMix');
        $arrayPromoPlanes       = $objRequest->get('arrayPromoPlanes');
        $arrayPromoProductos    = $objRequest->get('arrayPromoProductos');
        $arrayPromoDescTotal    = $objRequest->get('arrayPromoDescTotal');
        $strCodigoPromocion     = $objRequest->get('strCodigoPromocion');
        $strCodigoPromocionIng  = $objRequest->get('strCodigoPromocionIng');
        $strUsrUltMod           = $objSesion->get('user');
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $strIpUltMod            = $objRequest->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        
        try
        {           
            $arrayParametros = array('intIdPromocion'         => $intIdPromocion,
                                     'arraySectorizacion'     => $arraySectorizacion,
                                     'strNombrePromocion'     => $strNombrePromocion,
                                     'strIdsEstadoServicio'   => $strIdsEstadoServicio,
                                     'strInicioVigencia'      => $strInicioVigencia,
                                     'strFinVigencia'         => $strFinVigencia,
                                     'strIdsFormasPago'       => $strIdsFormasPago,
                                     'strTipoCliente'         => $strTipoCliente,
                                     'arrayEmisores'          => $arrayEmisores,
                                     'strUsrUltMod'           => $strUsrUltMod,
                                     'strCodEmpresa'          => $strCodEmpresa,
                                     'strIpUltMod'            => $strIpUltMod,
                                     'arrayPromoMix'          => $arrayPromoMix,
                                     'arrayPromoPlanes'       => $arrayPromoPlanes,
                                     'arrayPromoProductos'    => $arrayPromoProductos,
                                     'arrayPromoDescTotal'    => $arrayPromoDescTotal,
                                     'strCodigoPromocion'     => $strCodigoPromocion,
                                     'strCodigoPromocionIng'  => $strCodigoPromocionIng,
                                     'strPermMinimaCancelVol' => $strPermMinimaCancelVol
                                    );
            $servicePromocionMensualidad  = $this->get('comercial.PromocionMensualidad');
            $strResponse                  = $servicePromocionMensualidad->editarPromoMensualidad($arrayParametros);           
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al actualizar la Promoción de Mensualidad, por favor consulte con el Administrador.";          
        }
        return new Response($strResponse);

    }
    
   /**
     * getEmisoresPromoMensualidadAction()
     * Función que obtiene los emisores de la Promoción de Mensualidad
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 17-04-2019    
     * @param array $arrayParametros[               
     *                                'intIdPromocion   => Id de la promoción
     *                                'strEstado'       => Estado de la Promoción
     *                              ]  
     */
    public function getEmisoresPromoMensualidadAction()
    {
        $servicePromocionMensualidad        = $this->get('comercial.PromocionMensualidad');
        $objRequest                         = $this->getRequest();
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['arrayEstado']     = array('Eliminado');
        $arrayEmisores                      = $servicePromocionMensualidad->getEmisoresPromoMensualidad($arrayParametros);
        $objResponse                        = new Response(json_encode(array('emisores' => $arrayEmisores)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_431-1")    
     * detalleAction()
     * Función que renderiza la página de Ver detalle de una Promoción.
     * 
     * @author : Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 28-03-2019 
     * @since 1.0
     * 
     * @param int $intIdPromocion => id de la Promoción
     * 
     * @return render - Página de Ver Promoción.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.3 24-11-2020 - Se modifica función para las promociones origen.
     */
    public function detalleAction($intIdPromocion,$strIdOrigen)
    {
        $intIdEmpresa         = $this->get('request')->getSession()->get('idEmpresa');
        $emComercial          = $this->get('doctrine')->getManager('telconet');
        $objAdmiTipoPromocion = $emComercial->getRepository('schemaBundle:AdmiTipoPromocion')->findBy(array("grupoPromocionId" => $intIdPromocion));

        if(empty($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo ver el detalle de Promoción, No existe Tipo de Promoción.");
        }

        $strTipoPromocion = $objAdmiTipoPromocion[0]->getCodigoTipoPromocion();
        $arrayParametros  = array('intIdPromocion' => $intIdPromocion,
                                  'intIdEmpresa'   => $intIdEmpresa
                                 );        
        
        $arrayParametrosOrigen  = array('intIdPromocion' => $strIdOrigen,
                                         'intIdEmpresa'   => $intIdEmpresa
                                        );  
        $servicePromocionMensualidad = $this->get('comercial.PromocionMensualidad');
        $servicePromocionInstalacion = $this->get('comercial.PromocionInstalacion');
        $servicePromocionAnchoBanda  = $this->get('comercial.PromocionAnchoBanda');
        
        if($strTipoPromocion === "PROM_MIX"||$strTipoPromocion === "PROM_MPLA"||
           $strTipoPromocion === "PROM_MPRO"||$strTipoPromocion === "PROM_TOT")
        {
            $objPromocionMensual       = $servicePromocionMensualidad->getPromocionMensual($arrayParametros);
            if($strIdOrigen!='N')
            {
                $objPromocionMensualOrigen = $servicePromocionMensualidad->getPromocionMensual($arrayParametrosOrigen);
                return $this->render('comercialBundle:promocion:verPromoMensualidadOrigen.html.twig',
                                     array('objPromocion'=>$objPromocionMensual,
                                            'objPromocionOrigen'=>$objPromocionMensualOrigen));
            }
            else
            {
                return $this->render('comercialBundle:promocion:verPromoMensualidad.html.twig',
                                      array('objPromocion'=>$objPromocionMensual));
            }
   
        }
        elseif($strTipoPromocion === "PROM_INS")
        {
            $objPromocionInstalacion       = $servicePromocionInstalacion->getPromocionInstalacion($arrayParametros);
         
            if($strIdOrigen!='N')
            {
               $objPromocionInstalacionOrigen = $servicePromocionInstalacion->getPromocionInstalacion($arrayParametrosOrigen);   
            
               return $this->render('comercialBundle:promocion:verPromoInstalacionOrigen.html.twig',
                       array('objPromocion' => $objPromocionInstalacion,
                             'objPromocionOrigen'=>$objPromocionInstalacionOrigen));
            }
            else
            {
               return $this->render('comercialBundle:promocion:verPromoInstalacion.html.twig',
                                     array('objPromocion' => $objPromocionInstalacion));     
            }
            
        }
        elseif($strTipoPromocion === "PROM_BW")
        {
            $objPromocionAnchoBanda       = $servicePromocionAnchoBanda->getPromocionAnchoBanda($arrayParametros);
            if($strIdOrigen!='N')
            {
               $objPromocionAnchoBandaOrigen = $servicePromocionAnchoBanda->getPromocionAnchoBanda($arrayParametrosOrigen);
               return $this->render('comercialBundle:promocion:verPromoAnchoBandaOrigen.html.twig',
                       array('objPromocion' => $objPromocionAnchoBanda,
                           'objPromocionOrigen'=>$objPromocionAnchoBandaOrigen));
            }
            else
            {
                return $this->render('comercialBundle:promocion:verPromoAnchoBanda.html.twig',
                        array('objPromocion' => $objPromocionAnchoBanda));
            }

        }
    }

    /**
     * detenerAction()
     * Función que detiene una promocion no activa.
     * 
     * @author: Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 30-11-2021 
     * @since 1.0
     * 
     * @param array [ intIdPromocion => El codigo de la promocion a detener,
     *                strTipoPromocion => El tipo de promocion que de detendra]
     * 
     * @return render - Página principal de promociones.
     *
     */
    public function detenerAction()
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $intIdEmpresa         = $objSession->get('idEmpresa');
        $strPreEmpresa        = $objSession->get('prefijoEmpresa');
        $strUsrDetiene        = $objSession->get('user');
        $strIpDetiene         = $objRequest->getClientIp();
        $intIdPromocion       = $objRequest->get('intIdPromocion');
        $strTipoPromocion     = $objRequest->get('strTipoPromocion');
        $emComercial          = $this->get('doctrine')->getManager('telconet');
        $objAdmiTipoPromocion = $emComercial->getRepository('schemaBundle:AdmiTipoPromocion')
                                    ->findBy(array("grupoPromocionId" => $intIdPromocion));
        if(empty($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo ver el detalle de Promoción, No existe Tipo de Promoción.");
        }

        $servicePromoAnchoBanda = $this->get('comercial.PromocionAnchoBanda');
        $arrayParametros  = array(
                    'intIdPromocion'   => $intIdPromocion,
                    'strTipoPromocion' => $strTipoPromocion,
                    'intIdEmpresa'     => $intIdEmpresa,
                    'strPreEmpresa'    => $strPreEmpresa,
                    'strUsrDetiene'    => $strUsrDetiene,
                    'strIpDetiene'     => $strIpDetiene);
        
        $strRespueta = $servicePromoAnchoBanda->detenerPromoAnchoBanda($arrayParametros);

        if ($strRespueta === 'OK')
        {
            $arrayRespuesta = array(
                'result' => 'OK',
                'message' => 'Exito'
            );
        }
        else
        {
            $arrayRespuesta = array(
                'result' => 'ERROR',
                'message' => $strRespueta
            );
        }
        $objResponse = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }

    /**
     * anularAction() - Función que anula una promocion indicada.
     * 
     * @author: Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 11-04-2022
     * @since 1.0
     * 
     * @param array [intIdPromocion => El codigo de la promocion a detener,
     *               strTipoPromocion => El tipo de promocion que de detendra]
     * 
     * @return render - Página principal de promociones.
     *
     */
    public function anularAction()
    {
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $intIdEmpresa         = $objSession->get('idEmpresa');
        $strPreEmpresa        = $objSession->get('prefijoEmpresa');
        $strUsrDetiene        = $objSession->get('user');
        $strIpDetiene         = $objRequest->getClientIp();
        $intIdPromocion       = $objRequest->get('intIdPromocion');
        $strTipoPromocion     = $objRequest->get('strTipoPromocion');
        $strEstadoPromocion   = $objRequest->get('strEstadoPromocion');
        $emComercial          = $this->get('doctrine')->getManager('telconet');
        $objAdmiTipoPromocion = $emComercial->getRepository('schemaBundle:AdmiTipoPromocion')
                                    ->findBy(array("grupoPromocionId" => $intIdPromocion));
        if(empty($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo ver el detalle de Promoción, No existe Tipo de Promoción.");
        }

        $servicePromoAnchoBanda = $this->get('comercial.PromocionAnchoBanda');
        $arrayParametros  = array(
                    'intIdPromocion'     => $intIdPromocion,
                    'strTipoPromocion'   => $strTipoPromocion,
                    'strEstadoPromocion' => $strEstadoPromocion,
                    'intIdEmpresa'       => $intIdEmpresa,
                    'strPreEmpresa'      => $strPreEmpresa,
                    'strUsrDetiene'      => $strUsrDetiene,
                    'strIpDetiene'       => $strIpDetiene);
        
        $strRespueta = $servicePromoAnchoBanda->anularPromoAnchoBanda($arrayParametros);

        if ($strRespueta === 'OK')
        {
            $arrayRespuesta = array(
                'result' => 'OK',
                'message' => 'Exito'
            );
        }
        else
        {
            $arrayRespuesta = array(
                'result' => 'ERROR',
                'message' => $strRespueta
            );
        }
        $objResponse = new Response(json_encode($arrayRespuesta));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_431-1")    
     * @author : José Candelario <jcandelario@telconet.ec>
     * @version 1.0 07-08-2018 Método para renderizar el detalle de una promoción.
     * @since 1.0
     *     
     */
    public function detalleEditarAction($intIdPromocion,$strTipoEdicion)
    {
        $emComercial = $this->get('doctrine')->getManager('telconet');
        $objAdmiTipoPromocion = $emComercial->getRepository('schemaBundle:AdmiTipoPromocion')->findBy(array("grupoPromocionId" => $intIdPromocion));
        $strTipoEdicionArray= explode(",", $strTipoEdicion);
  
        $strTipoEdicion=$strTipoEdicionArray[0];
        $strIdOrigen=$strTipoEdicionArray[1];

        if(empty($objAdmiTipoPromocion))
        {
            throw new \Exception("No se pudo ver el detalle de Promoción, No existe Tipo de Promoción.");
        }

        $strTipoPromocion = $objAdmiTipoPromocion[0]->getCodigoTipoPromocion();     
        $intIdEmpresa     = $this->get('request')->getSession()->get('idEmpresa');
        $arrayParametros  = array();
        $arrayParametros  = array('intIdPromocion' => $intIdPromocion,
                                  'intIdEmpresa'   => $intIdEmpresa
                                 );
        
        $arrayParametrosOrigen  = array('intIdPromocion' => $strIdOrigen,
                                         'intIdEmpresa'   => $intIdEmpresa
                                        );  

        $arrayParametros['strTipoEdicion']       = $strTipoEdicion;

        if($strTipoPromocion === "PROM_BW")
        {
        
            $servicePromocion                   = $this->get('comercial.Promocion');
            $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');        
            $strFechaEditar                     =  date('Y-m-d', strtotime('+1 days'));
            if ($strIdOrigen!='N') 
            {
             
              $objPromocionAnchoBandaOrigen = $servicePromocionAnchoBanda->getPromocionAnchoBanda($arrayParametrosOrigen);   
            }
            
            $arrayParametros['strEstado']       = 'Activo';
            $arrayParametros['arrayEstado']     = array('Eliminado');
            $arrayDatosGeneralesPromo           = $servicePromocionAnchoBanda->getDatosGenePromoAnchoBanda($arrayParametros);
            $arrayPlanes                        = $servicePromocionAnchoBanda->getPlanesPromoAnchoBanda($arrayParametros);
            $arrayEmisores                      = $servicePromocionAnchoBanda->getEmisoresPromoAnchoBanda($arrayParametros);
            $arraySectorizacion                 = $servicePromocion->getSectorizacion($arrayParametros);
            $arrayRespuesta                     = array('intIdPromocion'            => $intIdPromocion,
                                                        'arraySectorizacion'        => $arraySectorizacion,
                                                        'arrayPlanes'               => $arrayPlanes,
                                                        'arrayEmisores'             => $arrayEmisores,
                                                        'strTipoEdicion'            => $strTipoEdicion,
                                                        'strFechaEditar'            => $strFechaEditar,
                                                        'arrayDatosGeneralesPromo'  => $arrayDatosGeneralesPromo,
                                                        'intIdOrigen'               => $strIdOrigen);
            return $this->render('comercialBundle:promocion:editarPromoAnchoBanda.html.twig', 
                                   array('objPromocion'              => $arrayRespuesta,
                                          'objPromocionOrigen'        => $objPromocionAnchoBandaOrigen));
        }
        elseif($strTipoPromocion === "PROM_MIX"||$strTipoPromocion === "PROM_MPLA"||$strTipoPromocion === "PROM_MPRO"
            ||$strTipoPromocion === "PROM_TOT")
        {
            $servicePromocionMensualidad = $this->get('comercial.PromocionMensualidad');
            if ($strIdOrigen!='N') 
            {
              $objPromocionMensualOrigen = $servicePromocionMensualidad->getPromocionMensual($arrayParametrosOrigen);  
            }
            $arrayPromocionMensual       = $servicePromocionMensualidad->getPromocionMensualEditar($arrayParametros);
            if($strTipoEdicion=='E') 
            {
                return $this->render('comercialBundle:promocion:editarPromoMensualidad.html.twig',
                        array('objPromocion' => $arrayPromocionMensual,
                               'objPromocionOrigen' => $objPromocionMensualOrigen,
                               'intIdOrigen'=>$strIdOrigen));
            }
            else if($strTipoEdicion=='ED') 
            {
                return $this->render('comercialBundle:promocion:editarPromoVigenteMensualidad.html.twig',
                        array('objPromocion' => $arrayPromocionMensual,
                              'objPromocionOrigen' => $objPromocionMensualOrigen,
                               'intIdOrigen'=>$strIdOrigen,
                               'strTipoEdicion'                     => $strTipoEdicion,
                                'dateFechaActual'                    => $arrayPromocionMensual['dateFechaActual'],
                              ));
            }
                
        }
        elseif($strTipoPromocion === "PROM_INS")
        {            
            $arrayParametros['strEstado']    = 'Activo';                                                     
            $servicePromocionInstalacion     = $this->get('comercial.PromocionInstalacion');
            $arrayPromocionInstalacion       = $servicePromocionInstalacion->getEditarPromocionInstalacion($arrayParametros);            
            
            if ($strIdOrigen!='N') 
            {
              $objPromocionInstalacionOrigen = $servicePromocionInstalacion->getPromocionInstalacion($arrayParametrosOrigen);   
            }
            
            if (empty($arrayPromocionInstalacion))
            {
                throw new \Exception("No se pudo obtener el detalle de la Promoción. Consulte con el Administrador del Sistema");
            }            
            return $this->render('comercialBundle:promocion:editarPromoInstalacion.html.twig', 
                                 array('objAdmiGrupoPromocion'              => $arrayPromocionInstalacion['objAdmiGrupoPromocion'],
                                       'arrayCaracteristicas'               => $arrayPromocionInstalacion['arrayCaracteristicas'],  
                                       'objAdmiGrupoPromocionOrigenClonado' => $arrayPromocionInstalacion['objAdmiGrupoPromocionOrigenClonado'],
                                       'strTipoEdicion'                     => $strTipoEdicion,
                                       'dateFechaActual'                    => $arrayPromocionInstalacion['dateFechaActual'],
                                       'objPromocionOrigen'                 => $objPromocionInstalacionOrigen,
                                       'intIdOrigen'                        => $strIdOrigen
                                       ));
        }
    }    
    /**
    * @Secure(roles="ROLE_431-1")
    * crearPromoAnchoBandaAction, permite crear una promoción por ancho de banda.
    * 
    * @author : José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019.
    * @since 1.0
    */
    public function crearPromoAnchoBandaAction()
    {
        return $this->render('comercialBundle:promocion:crearPromoAnchoBanda.html.twig', array());
    }
    /**
    *
    * getAntiguedadAction, obtiene el valor límite para la regla antigüedad en promociones de ancho de banda parametrizado
    * en ADMI_PARAMETRO_CAB.
    *
    * @author José CAndelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019
    *
    * @return Response lista de Antigüedad.
    */
    public function getAntiguedadAction()
    {
        $strEmpresaCod          = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral              = $this->get('doctrine')->getManager('telconet');
        $arrayAntiguedad        = array();
        $arrayListAntiguedad    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PROD_PROM_ANTIGUEDAD',
                                                  'COMERCIAL',
                                                  '',
                                                  '',
                                                  '',
                                                  '',
                                                  '',
                                                  '',
                                                  '',
                                                  $strEmpresaCod,
                                                  '');

        foreach($arrayListAntiguedad as $objAntiguedad)
        {
            $arrayAntiguedad[] = array('id'     => $objAntiguedad['valor2'],
                                       'nombre' => $objAntiguedad['valor2']);
        }

        sort($arrayAntiguedad);
        $objResponse            = new Response(json_encode(array('antiguedad' => $arrayAntiguedad)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }   
    /**
    *
    * getDatosGenePromoAnchoBandaAction, obtiene información básica por tipo promoción ancho de banda.
    *
    * @author José CAndelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019
    *
    * @return Response lista de información básica por tipo promoción ancho de banda.
    */
    public function getDatosGenePromoAnchoBandaAction()
    {
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $objRequest                         = $this->getRequest();
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['strEstado']       = 'Activo';
        $arrayDatosPromo                    = $servicePromocionAnchoBanda->getDatosGenePromoAnchoBanda($arrayParametros);
        $objResponse                        = new Response(json_encode(array('datosGenePromo' => $arrayDatosPromo)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getPlanesAction, obtiene los planes por tipo promoción ancho de banda.
    *
    * @author José CAndelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019
    *
    * @return Response lista de planes por tipo promoción ancho de banda.
    */
    public function getPlanesPromoAnchoBandaAction()
    {
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $objRequest                         = $this->getRequest();
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['strEstado']       = 'Activo';
        $arrayPlanes                        = $servicePromocionAnchoBanda->getPlanesPromoAnchoBanda($arrayParametros);
        $objResponse                        = new Response(json_encode(array('data' => $arrayPlanes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getEmisoresPromoAnchoBandaAction, obtiene los emisores por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019
    *
    * @return Response lista de planes por tipo promoción ancho de banda.
    */
    public function getEmisoresPromoAnchoBandaAction()
    {
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $objRequest                         = $this->getRequest();
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['strEstado']       = 'Activo';
        $arrayParametros['arrayEstado']     = array('Eliminado');
        $arrayEmisores                      = $servicePromocionAnchoBanda->getEmisoresPromoAnchoBanda($arrayParametros);
        $objResponse                        = new Response(json_encode(array('emisores' => $arrayEmisores)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getSelectTiposNegocio, obtiene los tipos de Negocio por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @return Response lista de Tipos de Negocio por promoción.
    */
    public function getSelectTiposNegocioAction()
    {
        /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionService */
        $servicePromocion                   = $this->get('comercial.Promocion');
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $objRequest                         = $this->getRequest();
        $strEmpresaCod                      = $objRequest->getSession()->get('idEmpresa');
        $arrayTiposNegocioTodos             = [];
        $arrayParametros                    = array();
        $arrayParametros['strEstado']       = array('Activo');
        $arrayParametros['strEmpresaCod']   = $strEmpresaCod;
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayTiposNegocio                  = $servicePromocion->getTiposNegocio($arrayParametros);
        $arrayTiposNegocioSelect            = $servicePromocionAnchoBanda->getSelectTiposNegocio($arrayParametros);

        foreach ($arrayTiposNegocio as $arrayParametro) 
        {
            $arrayTiposNegocioTodos[] = ['id'   => $arrayParametro['id'],
                                         'text' => $arrayParametro['nombre']
                                        ];
        }

        $objResponse    = new Response(json_encode(array('tipos_de_negocio'         => $arrayTiposNegocioTodos,
                                                         'tipos_de_negocio_select'  => $arrayTiposNegocioSelect
                                                        )
                                                  )
                                      );
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getSelectUltimMillaAction, obtiene las últimas millas por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @return Response lista de últimas millas por promoción.
    */
    public function getSelectUltimMillaAction()
    {
        
        $objRequest                         = $this->getRequest();
        $strCodTipoPromocion                = $objRequest->get("strCodTipoPromocion") ? $objRequest->get("strCodTipoPromocion") : "X";
        $strEmpresaCod                      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral                          = $this->get('doctrine')->getManager('telconet');
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['strEstado']       = 'Activo';
        $arrayUltimaMillasSelect            = $servicePromocionAnchoBanda->getSelectUltimaMillas($arrayParametros);
        $arrayListUltimasMillas             = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PROD_PROM_ULTIMA_MILLA',
                                                              'COMERCIAL',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              $strCodTipoPromocion, 
                                                              '',
                                                              $strEmpresaCod,
                                                              'valor3');
        $arrayUltimasMilla     = array();
        
        foreach($arrayListUltimasMillas as $objUltimasMillas)
        {
            $arrayUltimasMilla[] = array('id'   => $objUltimasMillas['valor3'], 
                                         'text' => $objUltimasMillas['valor1']);
        }
        $objResponse    = new Response(json_encode(array('ultimas_millas'        => $arrayUltimasMilla,
                                                         'ultimas_millas_select' => $arrayUltimaMillasSelect)));

        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getSelectFormaPagosAction, obtiene las formas de pagos por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @return Response lista de formas de pagos por promoción.
    */
    public function getSelectFormaPagosAction()
    {
        $servicePromocionAnchoBanda                 = $this->get('comercial.PromocionAnchoBanda');
        $arrayFormaPagosTodos                       = [];
        $objRequest                                 = $this->getRequest(); 
        $arrayParametros                            = array();
        $arrayParametros['arrayEstadoFormaPago']    = array('Activo');
        $arrayParametros['strEstado']               = 'Activo';
        $arrayParametros['intIdPromocion']          = $objRequest->get('intIdPromocion');
        $strEmpresaCod                              = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral                                  = $this->get('doctrine')->getManager('telconet');
        $arrayListFormasPago                        = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PROD_PROM_FORMA_PAGO', 
                                                                      'COMERCIAL',
                                                                      '', 
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      $strEmpresaCod,
                                                                      'valor3'
                                                                     );
        $arrayFormaPagosTodos                       = array();
        $arrayFormaPagosSelect                      = $servicePromocionAnchoBanda->getSelectFormaPagos($arrayParametros);
        foreach($arrayListFormasPago as $objFormaPago)
        {
            $arrayFormaPagosTodos[] = array('id'    => $objFormaPago['valor3'], 
                                            'text'  => $objFormaPago['valor1']
                                           );
        }
        $objResponse    = new Response(json_encode(array('formas_de_pago'           => $arrayFormaPagosTodos,
                                                         'formas_de_pago_select'    => $arrayFormaPagosSelect
                                                        )
                                                  )
                                      );
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getSelectEstadosAction, obtiene los estados que aplica la promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @return Response lista de estados que aplica la promoción.
    */
    public function getSelectEstadosAction()
    {
        $strEmpresaCod                      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral                          = $this->get('doctrine')->getManager('telconet');
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $arrayEstados                       = array();
        $objRequest                         = $this->getRequest();
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['strEstado']       = 'Activo';
        $arrayListEstados                   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PROD_PROM_ESTADO_SERVICIO',
                                                              'COMERCIAL',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              $strEmpresaCod,
                                                              'valor3');
        $arrayEstadosSelect = $servicePromocionAnchoBanda->getSelectEstados($arrayParametros);

        foreach($arrayListEstados as $objEstados)
        {
            $arrayEstados[] = array('id'    => $objEstados['valor1'],
                                    'text'  => $objEstados['valor1']
                                   );
        }

        $objResponse    = new Response(json_encode(array('estados_servicios'        => $arrayEstados,
                                                         'estados_servicios_select' => $arrayEstadosSelect
                                                        )
                                                  )
                                      );
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    *
    * getSelectPeriodosAction, obtiene los períodos que aplica la promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019    
    *                   
    * @return Response lista de períodos que aplica la promoción.
    */
    public function getSelectPeriodosAction()
    {
        $strEmpresaCod                      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral                          = $this->get('doctrine')->getManager('telconet');
        $objRequest                         = $this->getRequest();
        $servicePromocionAnchoBanda         = $this->get('comercial.PromocionAnchoBanda');
        $arrayParametros                    = array();
        $arrayParametros['intIdPromocion']  = $objRequest->get('intIdPromocion');
        $arrayParametros['strEstado']       = 'Activo';
        $arrayPeriodos                      = array();
        $arrayListPeriodos                  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PROD_PROM_PERIODO',
                                                              'COMERCIAL',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              $strEmpresaCod,
                                                              'valor3');
        $arrayPeriodosSelect                = $servicePromocionAnchoBanda->getSelectPeriodos($arrayParametros);

        foreach($arrayListPeriodos as $objPeriodo)
        {
            $arrayPeriodos[] = array('id'       => $objPeriodo['valor1'],
                                     'nombre'   => $objPeriodo['valor1']
                                    );
        }
        sort($arrayPeriodos);

        $objResponse = new Response(json_encode(array('periodos'        => $arrayPeriodos,
                                                      'periodos_select' => $arrayPeriodosSelect
                                                     )
                                                )
                                    );
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
    * @Secure(roles="ROLE_431-1")
    * guardaPromoAnchoBandaAction, Función guarda nueva Promoción de ancho de banda.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 1-07-2022 - Se agrega función json_decode() para decodificar el JSON 
    *                          de Emisores y Sectorización enviados desde la peticion ajax de la interfaz,
    *                          la cual se utilizó para recibir una gran cantidad de información de la misma.  
    * 
    * @return JsonResponse
    */
    public function guardaPromoAnchoBandaAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $strNombrePromocion     = $objRequest->get('strNombrePromocion');
        $strAntiguedad          = $objRequest->get('strAntiguedad') ? $objRequest->get('strAntiguedad') : '';
        $strFeIniVigencia       = $objRequest->get('strFeIniVigencia');
        $strFeFinVigencia       = $objRequest->get('strFeFinVigencia');
        $strTipoNegocio         = $objRequest->get('strTiposNegocio') ? $objRequest->get('strTiposNegocio') : '';
        $arrayFormasPago        = $objRequest->get('arrayFormasPago') ? $objRequest->get('arrayFormasPago') : '';
        $strFormaPago           = implode(",", $arrayFormasPago);
        $arrayUltimaMilla       = $objRequest->get('arrayUltimasMilla') ? $objRequest->get('arrayUltimasMilla') : '';
        $strUltimaMilla         = implode(",", $arrayUltimaMilla);
        $arrayEstadoServicio    = $objRequest->get('arrayEstadoServicio') ? $objRequest->get('arrayEstadoServicio') : '';
        $strEstadoServicio      = implode(",", $arrayEstadoServicio);
        $arrayPeriodo           = $objRequest->get('arrayPeriodos') ? $objRequest->get('arrayPeriodos') : '';
        $strPeriodo             = implode(",", $arrayPeriodo);
        $arrayTipoCliente       = $objRequest->get('arrayTipoCliente') ? $objRequest->get('arrayTipoCliente') : '';
        $strTipoCliente         = implode(",", $arrayTipoCliente);
        $arrayEmisores          = json_decode($objRequest->get('arrayEmisores'),true) ? json_decode($objRequest->get('arrayEmisores'),true) : '';
        $arrayPlanes            = $objRequest->get('arrayPlanes');
        $arraySectorizacion     = json_decode($objRequest->get('arraySectorizacion'),true);
        $strUsrCreacion         = $objSesion->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $strCodigoPromocion     = $objRequest->get('strCodigoPromocion') ? $objRequest->get('strCodigoPromocion') : '';
        $strTipoEdicion         = $objRequest->get('strTipoEdicion') ? $objRequest->get('strTipoEdicion') : '';
        $intIdPromocionOrigen   = $objRequest->get('intIdPromocionOrigen');

        try
        {
            /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionAnchoBandaService */
            $servicePromocionAnchoBanda                 = $this->get('comercial.PromocionAnchoBanda');
            $arrayParametros                            = array();
            $arrayParametros['strNombrePromocion']      = $strNombrePromocion;
            $arrayParametros['strFeIniVigencia']        = $strFeIniVigencia;
            $arrayParametros['strFeFinVigencia']        = $strFeFinVigencia;
            $arrayParametros['strTipoNegocio']          = $strTipoNegocio;
            $arrayParametros['strFormaPago']            = $strFormaPago;
            $arrayParametros['strUltimaMilla']          = $strUltimaMilla;
            $arrayParametros['strEstadoServicio']       = $strEstadoServicio;
            $arrayParametros['strAntiguedad']           = $strAntiguedad;
            $arrayParametros['strPeriodo']              = $strPeriodo;
            $arrayParametros['strTipoCliente']          = $strTipoCliente;
            $arrayParametros['arrayEmisores']           = $arrayEmisores;//EMISORES
            $arrayParametros['arrayPlanes']             = $arrayPlanes;
            $arrayParametros['arraySectorizacion']      = $arraySectorizacion;
            $arrayParametros['strUsrCrea']              = $strUsrCreacion;
            $arrayParametros['strFeCreacion']           = new \DateTime('now');
            $arrayParametros['strIpCreacion']           = $strIpCreacion;
            $arrayParametros['strCodEmpresa']           = $strCodEmpresa;
            $arrayParametros['strCodigoPromocion']      = $strCodigoPromocion;
            $arrayParametros['strTipoEdicion']          = $strTipoEdicion;
            $arrayParametros['intIdPromocionOrigen']    = $intIdPromocionOrigen;
            $strResponse                                = $servicePromocionAnchoBanda->crearPromoAnchoBanda($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al ingresar una nueva promoción de ancho de banda, por favor consulte con el Administrador";
        }
        return new Response($strResponse);
    }
    /**
    * @Secure(roles="ROLE_431-1")
    * editarPromoAnchoBandaAction Función edita una Promoción de ancho de banda.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 18-03-2019
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 1-07-2022 - Se agrega función json_decode() para decodificar el JSON 
    *                          de Emisores y Sectorización enviados desde la peticion ajax de la interfaz,
    *                          la cual se utilizó para recibir una gran cantidad de información de la misma.  
    * 
    * @return JsonResponse
    */
    public function editarPromoAnchoBandaAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $intIdPromocion         = $objRequest->get('intIdPromocion');
        $strNombrePromocion     = $objRequest->get('strNombrePromocion');
        $strAntiguedad          = $objRequest->get('strAntiguedad');
        $strFeIniVigencia       = $objRequest->get('strFeIniVigencia');
        $strFeFinVigencia       = $objRequest->get('strFeFinVigencia');
        $strTipoNegocio         = $objRequest->get('strTiposNegocio');
        $arrayFormasPago        = $objRequest->get('arrayFormasPago');
        $strFormaPago           = implode(",", $arrayFormasPago);
        $arrayUltimaMilla       = $objRequest->get('arrayUltimasMilla');
        $strUltimaMilla         = implode(",", $arrayUltimaMilla);
        $arrayEstadoServicio    = $objRequest->get('arrayEstadoServicio');
        $strEstadoServicio      = implode(",", $arrayEstadoServicio);
        $arrayPeriodo           = $objRequest->get('arrayPeriodos');
        $strPeriodo             = implode(",", $arrayPeriodo);
        $arrayTipoCliente       = $objRequest->get('arrayTipoCliente');
        $strTipoCliente         = implode(",", $arrayTipoCliente);
        $arrayEmisores          = json_decode($objRequest->get('arrayEmisores'),true);
        $arrayPlanes            = $objRequest->get('arrayPlanes');
        $arraySectorizacion     = json_decode($objRequest->get('arraySectorizacion'),true);
        $strCodigoPromocionIng  = $objRequest->get('strCodigoPromocionIng');
        $strUsrMod              = $objSesion->get('user');
        $strIpMod               = $objRequest->getClientIp();
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $strCodigoPromocion     = $objRequest->get('strCodigoPromocion');
        $emComercial            = $this->getDoctrine()->getManager("telconet");

        try
        {
            /* @var $servicePromocion \telconet\comercialBundle\Service\PromocionAnchoBandaService */
            $servicePromocionAnchoBanda                 = $this->get('comercial.PromocionAnchoBanda');
            $arrayParametros                            = array();
            $arrayParametros['intIdPromocion']          = $intIdPromocion;
            $arrayParametros['strNombrePromocion']      = $strNombrePromocion;
            $arrayParametros['strFeIniVigencia']        = $strFeIniVigencia;
            $arrayParametros['strFeFinVigencia']        = $strFeFinVigencia;
            $arrayParametros['strTipoNegocio']          = $strTipoNegocio;
            $arrayParametros['strFormaPago']            = $strFormaPago;
            $arrayParametros['strUltimaMilla']          = $strUltimaMilla;
            $arrayParametros['strEstadoServicio']       = $strEstadoServicio;
            $arrayParametros['strAntiguedad']           = $strAntiguedad;
            $arrayParametros['strPeriodo']              = $strPeriodo;
            $arrayParametros['strTipoCliente']          = $strTipoCliente;
            $arrayParametros['arrayEmisores']           = $arrayEmisores;//EMISORES
            $arrayParametros['arrayPlanes']             = $arrayPlanes;
            $arrayParametros['arraySectorizacion']      = $arraySectorizacion;
            $arrayParametros['strUsrMod']               = $strUsrMod;
            $arrayParametros['strFeMod']                = new \DateTime('now');
            $arrayParametros['strIpMod']                = $strIpMod;
            $arrayParametros['strCodEmpresa']           = $strCodEmpresa;
            $arrayParametros['strCodigoPromocion']      = $strCodigoPromocion;
            $arrayParametros['strCodigoPromocionIng']   = $strCodigoPromocionIng;
            $strResponse                                = $servicePromocionAnchoBanda->editarPromoAnchoBanda($arrayParametros);
        }
        catch(\Exception $e)
        {
            error_log(  'ERROR: '.$e->getMessage()  );
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al editar la promoción de ancho de banda, por favor consulte con el Administrador";
        }
        return new Response($strResponse);
    }
    
    
     /**
     * getTipoClientesAction, obtiene la información de los tipos de clientes que se encuentran parametrizados en ADMI_PARAMETRO_CAB.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 14-08-2020
     *                    
     * @return Response lista de Tipos de Clientes.
     */
    public function getTipoClientesAction()
    {               
        
        $strEmpresaCod  = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral      = $this->get('doctrine')->getManager('telconet');
        
        $arrayListTipoClientes  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('TIPO_CLIENTE_PROM', 'COMERCIAL', '', 'TIPO_CLIENTE', 'MENS', '', '', '', '', $strEmpresaCod,'');
        
        $arrayTipoClientes      = array();
        
        foreach($arrayListTipoClientes as $objTipoClientes)
        {
            $arrayTipoClientes[] = array('id' => $objTipoClientes['valor3'], 'nombre' => $objTipoClientes['valor2']);
        }              
        $objResponse = new Response(json_encode(array('tipo_cliente' => $arrayTipoClientes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }      
     /**
    * @Secure(roles="ROLE_431-1")
    * ajaxEditarPromoMensualidadVigenteAction Función actualiza una Promoción de Mensualidad Vigentes.
    *
    * @author Katherine Yager<kyager@telconet.ec>
    * @version 1.0 12-10-2020
    *             
    * @return $strResponse
    *
    */   
    public function ajaxEditarPromoMensualidadVigenteAction()
    {             
        $objRequest                       = $this->getRequest();
        $objSesion                        = $objRequest->getSession();
        $intIdPromocion                   = $objRequest->get('intIdPromocion');
        $strUsrUltMod                     = $objSesion->get('user');
        $strCodEmpresa                    = $objSesion->get('idEmpresa');
        $strIpUltMod                      = $objRequest->getClientIp();
        $emComercial                      = $this->getDoctrine()->getManager('telconet');
        $strMotivoInactivarVigente        = $objRequest->get('strMotivoInactivarVigente');
        $strObservacionInactivarVigente   = $objRequest->get('strObservacionInactivarVigente');
        $strTipoPma                       = 'InactPromoUnico';
        $arrayParametrosMasivo            = array(); 
        $arrayParametrosEjecutarMasivo    = array(); 

        try
        {                
                                      
            $arrayParametrosMasivo = array(
                                          'strIdsGrupoPromocion'   => $intIdPromocion,
                                          'intIdMotivo'            => $strMotivoInactivarVigente,
                                          'strObservacion'         => $strObservacionInactivarVigente,
                                          'strUsrCreacion'         => $strUsrUltMod,
                                          'strCodEmpresa'          => $strCodEmpresa,
                                          'strIpCreacion'          => $strIpUltMod,
                                          'strTipoPma'             => $strTipoPma
                                    );  
            
            $servicePromocion             = $this->get('comercial.Promocion');
            $strResponse                  = $servicePromocion->crearProcesoMasivo($arrayParametrosMasivo);   
                       
            if(strpos($strResponse, 'Se proce') === false )
            {
                $strResponse='Error';
            }
            else
            {
                // se llama a proceso que ejecuta la inactivación de la promocioń
                $arrayParametrosEjecutarMasivo = array(
                            'strTipoPma'      => $strTipoPma,
                            'strOrigenPma'    => 'InactivacionJob',
                            'strCodEmpresa'   => $strCodEmpresa,
                            'strEstado'       => 'Pendiente',
                            'strUsrCreacion'  => $strUsrUltMod,
                            'strIpCreacion'   => $strIpUltMod
                        );  
                
                $strResponseEjecMasivo = $servicePromocion->ejecutarProcesoMasivo($arrayParametrosEjecutarMasivo);
            }
            
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al actualizar la Promoción de Mensualidad, por favor consulte con el Administrador.";          
            
            error_log( 'ERROR: '.$e->getMessage() );
        }
        return new Response($strResponseEjecMasivo);

    }
    
    /**
    * @Secure(roles="ROLE_431-1")
    * validacionPromocionAction Función que valida los código de promociones ingresados por el Usuario.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 15-10-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $strGrupoPromocion      = $objRequest->get('strGrupoPromocion');
        $strTipoPromocion       = $objRequest->get('strTipoPromocion');
        $strTipoProceso         = $objRequest->get('strTipoProceso');
        $strCodigo              = $objRequest->get('strCodigo');
        $intIdServicio          = $objRequest->get('intIdServicio');
        $intIdPunto             = $objRequest->get('intIdPunto');
        $intIdPlan              = $objRequest->get('intIdPlan');
        $intIdProducto          = $objRequest->get('intIdProducto');
        $intIdUltimaMilla       = $objRequest->get('intIdUltimaMilla');
        $strPrecio              = $objRequest->get('strPrecioTotal');
        $strFormaPago           = $objRequest->get('strFormaPago');
        $strEsContrato          = $objRequest->get('strEsContrato');
        $strUsrMod              = $objSesion->get('user');
        $strIpMod               = $objRequest->getClientIp();
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $emComercial            = $this->getDoctrine()->getManager("telconet");

        try
        {
            
            if($strTipoProceso === "GRADE")
            {
                $objInfoServicio        = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                $intIdPunto             = $objInfoServicio->getPuntoId()->getId();
                $floatPrecioVentaActual = 0;
                if ( is_object($objInfoServicio) )
                {
                    $floatPrecioVentaActual   = $objInfoServicio->getPrecioVenta();
                }
                if( floatval($strPrecio) < floatval($floatPrecioVentaActual) )
                {
                    $strTipoProceso = 'DOWNGRADE';
                }
                else
                {
                    $strTipoProceso = 'UPGRADE';
                }
            }
            
            $servicePromocion                           = $this->get('comercial.Promocion');
            $arrayParametros                            = array();
            $arrayParametros['strGrupoPromocion']       = $strGrupoPromocion;
            $arrayParametros['strTipoPromocion']        = $strTipoPromocion;
            $arrayParametros['strTipoProceso']          = $strTipoProceso;
            $arrayParametros['strCodigo']               = $strCodigo;
            $arrayParametros['intIdServicio']           = $intIdServicio;
            $arrayParametros['intIdPunto']              = $intIdPunto;
            $arrayParametros['intIdPlan']               = $intIdPlan;
            $arrayParametros['intIdProducto']           = $intIdProducto;
            $arrayParametros['intIdUltimaMilla']        = $intIdUltimaMilla;
            $arrayParametros['strFormaPago']            = $strFormaPago;
            $arrayParametros['strEsContrato']           = $strEsContrato;
            $arrayParametros['strUsrMod']               = $strUsrMod;
            $arrayParametros['strFeMod']                = new \DateTime('now');
            $arrayParametros['strIpMod']                = $strIpMod;
            $arrayParametros['strCodEmpresa']           = $strCodEmpresa;
            $arrayResponse                              = $servicePromocion->validaCodigoPromocion($arrayParametros);
        }
        catch(\Exception $e)
        {
            $strAplica     = 'N';
            $strMensaje    = 'Ocurrió un error al validar el código ingresado.';
            $arrayResponse = array('strAplica'  => $strAplica,
                                   'strMensaje' => $strMensaje);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
    * @Secure(roles="ROLE_431-1")
    * ajaxValidaCodigoUnicoAction Función que valida si el código promocional ingresado es único.
    * 
    * @author Katherine Yager <kyager@telconet.ec
    * @version 1.0 05-11-2020
    * 
    * @return JsonResponse
    */
    public function ajaxValidaCodigoUnicoAction()
    {
        $objRequest             = $this->getRequest();
        $strCodigoPromocion     = $objRequest->get('strCodigoPromocion');
        $strGrupoPromocion      = $objRequest->get('strGrupoPromocion');
        $strIdGrupoPromocion    = $objRequest->get('strIdGrupoPromocion');
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        try
        {
            $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'PROM_CODIGO',
                                                                                       'tipo'                      => 'COMERCIAL'));
            if(!is_object($objCaracteristica))
            {
                throw new \Exception("No se ha definido la característica : PROM_CODIGO");
            }
            
              $intCaracteristicaId=$objCaracteristica->getId();
              
              $arrayParametros                         = array();
              $arrayParametros['intCaracteristicaId']  = $intCaracteristicaId;
              $arrayParametros['strCodigoPromocion']   = $strCodigoPromocion;
              $arrayParametros['strGrupoPromocion']    = $strGrupoPromocion;
              $arrayParametros['strIdGrupoPromocion']  = $strIdGrupoPromocion;
              $servicePromocion                        = $this->get('comercial.Promocion');
              $intResponse                             = $servicePromocion->validaCodigoPromocionUnico($arrayParametros);
              
              $arrayResponse = array('intCantidad'  => $intResponse,
                                     'strMensaje' => '');
             
        }
        catch(\Exception $e)
        {
            $strAplica     = 'N';
            $strMensaje    = 'Ocurrió un error al validar el código ingresado.';
            $arrayResponse = array('intCantidad'  => $strAplica,
                                   'strMensaje' => $strMensaje);
        }
        error_log("arrayResponse---->". json_encode($arrayResponse));
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
    * @Secure(roles="ROLE_431-1")
    * guardarCodigoPromocionAction Función que guarda el código promocional ingresado.
    * 
    * @author Katherine Yager <kyager@telconet.ec
    * @version 1.0 06-11-2020
    * 
    * @return JsonResponse
    */
    public function guardarCodigoPromocionAction()
    { 
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $serviceUtil             = $this->get('schema.Util');
        $strCodigoMens          = $objRequest->get('strCodigoMens');
        $strPomocionMens        = $objRequest->get('strPomocionMens');
        $strCodigoBW            = $objRequest->get('strCodigoBW');
        $strPromocionBw         = $objRequest->get('strPromocionBw');
        $intIdServicio          = $objRequest->get('intIdServicio');
        $strServiciosMix        = $objRequest->get('strServiciosMix');
        $strTipoProceso         = $objRequest->get('strTipoProceso');
        $strIdTipoPromocionMens = $objRequest->get('strIdTipoMens');
        $strIdTipoPromocionBW   = $objRequest->get('strIdTipoBw');
        $strUsrCreacion         = $objSesion->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $servicePromocion       = $this->get('comercial.Promocion');
        $emComercial->getConnection()->beginTransaction();
      
        try
        {
            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById( $intIdServicio );
            
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha encontrado el servicio.");
            }
                 
            if ($strTipoProceso=='EXISTENTE')
            {
              $strDescripcionCaracteristica='PROM_COD_EXISTENTE';
            }
            else if ($strTipoProceso=='NUEVO')
            {
               $strDescripcionCaracteristica='PROM_COD_NUEVO';
            }
             
            if($strCodigoMens!='')
            {
                
                if($strServiciosMix !== '')
                {
                    $arrayServiciosMix = explode(",", $strServiciosMix);
                    for ($intServicio=0; $intServicio<count($arrayServiciosMix); $intServicio++)
                    {
                        $intIdServicioMix  = ($arrayServiciosMix[$intServicio]);
                        $objServicioMix    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                         ->findOneById( $intIdServicioMix );

                        if(!is_object($objServicioMix))
                        {
                            throw new \Exception("No se ha encontrado el servicio.");
                        }
                        
                        $objCaracteristicaMens = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCaracteristica,
                                                                               'tipo'                      => 'COMERCIAL'));

                        if(!is_object($objCaracteristicaMens))
                        {
                            throw new \Exception("No se ha definido la característica");
                        }
                        $arrayParametros                        = array();
                        $arrayParametros['intIdServicio']       = $intIdServicioMix;
                        $arrayParametros['strIpCreacion']       = $strIpCreacion;
                        $arrayParametros['strUsrCreacion']      = $strUsrCreacion;
                        $arrayParametros['strCodigo']           = $strCodigoMens;
                        $arrayParametros['strPromocion']        = $strPomocionMens;
                        $arrayParametros['strEstado']           = 'Activo';
                        $arrayParametros['strObservacion']      = "Se crea el código {$strCodigoMens} promocional para Mensualidad Existentes.";
                        $arrayParametros['objCaracteristica']   = $objCaracteristicaMens;
                        $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocionMens;
                        $arrayResponseMens                      = $servicePromocion->guardarCodigoServicioCarac($arrayParametros);
                    }
                }
                else 
                { 
                    $objCaracteristicaMens = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCaracteristica,
                                                                           'tipo'                      => 'COMERCIAL'));

                    if(!is_object($objCaracteristicaMens))
                    {
                        throw new \Exception("No se ha definido la característica");
                    }

                    $objCaracteristicaMensExist =  $emComercial->getRepository('schemaBundle:InfoServicioCaracteristica')
                                                               ->findOneBy(array("servicioId"       => $objServicio,
                                                                                 "caracteristicaId" => $objCaracteristicaMens,
                                                                                 "estado"           => 'Activo'));

                    if(!is_object($objCaracteristicaMensExist))
                    {
                        $arrayParametros                        = array();
                        $arrayParametros['intIdServicio']       = $intIdServicio;
                        $arrayParametros['strIpCreacion']       = $strIpCreacion;
                        $arrayParametros['strUsrCreacion']      = $strUsrCreacion;
                        $arrayParametros['strCodigo']           = $strCodigoMens;
                        $arrayParametros['strPromocion']        = $strPomocionMens;
                        $arrayParametros['strEstado']           = 'Activo';
                        $arrayParametros['strObservacion']      = "Se crea el código {$strCodigoMens} promocional para Mensualidad Existentes.";
                        $arrayParametros['objCaracteristica']   = $objCaracteristicaMens;
                        $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocionMens;
                        $arrayResponseMens                      = $servicePromocion->guardarCodigoServicioCarac($arrayParametros);

                    }
                }
            }
            
            if($strCodigoBW!='')
            {
                $objCaracteristicaBW = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array('descripcionCaracteristica' => 'PROM_COD_BW',
                                                                     'tipo'                      => 'COMERCIAL'));
                
                if(!is_object($objCaracteristicaBW))
                {
                    throw new \Exception("No se ha definido la característica");
                }
                
                $objCaracteristicaBwExist =  $emComercial->getRepository('schemaBundle:InfoServicioCaracteristica')
                                                         ->findOneBy(array("servicioId"       => $objServicio,
                                                                           "caracteristicaId" => $objCaracteristicaBW,
                                                                           "estado"           => 'Activo'));

                if(!is_object($objCaracteristicaBwExist))
                {
                    $arrayParametros                        = array();
                    $arrayParametros['intIdServicio']       = $intIdServicio;
                    $arrayParametros['strIpCreacion']       = $strIpCreacion;
                    $arrayParametros['strUsrCreacion']      = $strUsrCreacion;
                    $arrayParametros['strCodigo']           = $strCodigoBW;
                    $arrayParametros['strPromocion']        = $strPromocionBw;
                    $arrayParametros['strEstado']           = 'Activo';
                    $arrayParametros['strObservacion']      = "Se crea el código {$strCodigoBW} promocional para Ancho de Banda Existentes.";
                    $arrayParametros['objCaracteristica']   = $objCaracteristicaBW;
                    $arrayParametros['strIdTipoPromocion']  = $strIdTipoPromocionBW;
                    $arrayResponseBW                        = $servicePromocion->guardarCodigoServicioCarac($arrayParametros);
                }
            }
            $emComercial->getConnection()->commit();
            $arrayResponse = array('strPromMens' => $arrayResponseMens,
                                   'strPromBW'   => $arrayResponseBW,
                                   'strMensaje'  => '');
            
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $serviceUtil->insertErrorr('Telcos+',
                                            'PromocionService.guardarCodigoServicioCarac',
                                             'Error PromocionService.guardarCodigoServicioCarac:'.$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            
            error_log( 'ERROR: '.$e->getMessage() );
            $strMensaje    = 'Ocurrió un error al guardar el código ingresado promocional.';
            $arrayResponse = array('strPromMens' => '',
                                   'strPromBW'   => '',
                                   'strMensaje'  => $strMensaje);
        }
        
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
    * @Secure(roles="ROLE_431-1")
    * validaCaracExistPromoAction Función que valida si el código promocional ya existe para el servicio.
    * 
    * @author Katherine Yager <kyager@telconet.ec
    * @version 1.0 09-11-2020
    * 
    * @return JsonResponse
    */
    public function validaCaracExistPromoAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $intIdServicio          = $objRequest->get('intIdServicio');
        $strEstado              = 'Activo';
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        try
        {
            //Mensualidad
            $arrayParametros                         = array();
            $arrayParametros['strDescripcionCaracteristica']  = 'PROM_COD_EXISTENTE';
            $arrayParametros['intIdServicio']        = $intIdServicio;
            $arrayParametros['strEstado']            = $strEstado;
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $servicePromocion                        = $this->get('comercial.Promocion');
            $strResponseMens = $servicePromocion->validaCodigoPromocionExist($arrayParametros);


            //Ancho de Banda
            $arrayParametros                         = array();
            $arrayParametros['strDescripcionCaracteristica']  = 'PROM_COD_BW';
            $arrayParametros['intIdServicio']        = $intIdServicio;
            $arrayParametros['strEstado']            = $strEstado;
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $servicePromocion                        = $this->get('comercial.Promocion');
            $strResponseBW = $servicePromocion->validaCodigoPromocionExist($arrayParametros);

            //Validación de presentar caja de texto o boton grid
            //Mensualidad
            $servicePromocion                                            = $this->get('comercial.Promocion');

            $arrayParametrosEstadoMapeo                                  = array();
            $arrayParametrosEstadoMapeo['strDescripcionCaracteristica']  = 'PROM_COD_EXISTENTE';
            $arrayParametrosEstadoMapeo['intIdServicio']                 = $intIdServicio;
            $arrayParametrosEstadoMapeo['strEstado']                     = $strEstado;
            $arrayParametrosEstadoMapeo['strCodigoGrupoPromo']           = 'PROM_MENS';
            $arrayParametrosEstadoMapeo['strCodEmpresa']                 = $strCodEmpresa;
            $strResponseBotonGridMens = $servicePromocion->validaCodigoPromocionEstadoMapeo($arrayParametrosEstadoMapeo);

            //Ancho de Banda
            $arrayParametrosEstadoMapeo                                  = array();
            $arrayParametrosEstadoMapeo['strDescripcionCaracteristica']  = 'PROM_COD_BW';
            $arrayParametrosEstadoMapeo['intIdServicio']                 = $intIdServicio;
            $arrayParametrosEstadoMapeo['strEstado']                     = $strEstado;
            $arrayParametrosEstadoMapeo['strCodigoGrupoPromo']           = 'PROM_BW';
            $arrayParametrosEstadoMapeo['strCodEmpresa']                 = $strCodEmpresa;
            $strResponseBotonGridBw = $servicePromocion->validaCodigoPromocionEstadoMapeo($arrayParametrosEstadoMapeo);

            $arrayResponse = array('srtExisteBw'           => $strResponseBotonGridBw,
                                   'strResponseMens'       => $strResponseBotonGridMens,
                                   'srtExisteValorBw'      => $strResponseBW,
                                   'strResponseValorMens'  => $strResponseMens,
                                   'strMensaje'            => '');
             
        }
        catch(\Exception $e)
        {
            error_log( 'ERROR: '.$e->getMessage() );
            $strAplica     = 'N';
            $strMensaje    = 'Ocurrió un error al validar el código ingresado.';
            $arrayResponse = array('srtExiste'  => $strAplica,
                                   'strMensaje' => $strMensaje);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getBeneficiariosOltAction() - Función para obtener la cantidad de beneficiarios por olt en cantones
     * 
     * @author: Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 18-04-2022
     * @since 1.0
     * 
     * @param array [idJurisdiccion => El codigod de la jurisdiccion
     *               idCanton       => El codigo del canton a buscar,
     *               idParroquia    => El codigo de la parroquia a buscar,
     *               idSector       => El codigo del sector/olt/edificio a buscar,
     *               codLineProfile => Arreglo de los line profiles configurados]
     * 
     * @return render - Página principal de promociones.
     */
    public function getBeneficiariosOltAction()
    {
        $objRequest      = $this->get('request');
        $objSession      = $objRequest->getSession();
        $intIdEmpresa    = $objSession->get('idEmpresa');
        $strPreEmpresa   = $objSession->get('prefijoEmpresa');
        $strUsr          = $objSession->get('user');
        $strIp           = $objRequest->getClientIp();
        $arrayParametros = array();
        $arrayParametros['intJurisdiccion']  = $objRequest->get('idJurisdiccion') ? $objRequest->get('idJurisdiccion') : 0;
        $arrayParametros['intIdCanton']      = $objRequest->get('idCanton') ? $objRequest->get('idCanton') : 0;
        $arrayParametros['intIdParroquia']   = $objRequest->get('idParroquia') ? $objRequest->get('idParroquia') : 0;
        $arrayParametros['arrayIdSectores']  = $objRequest->get('idSectores') ? $objRequest->get('idSectores') : 0;
        $arrayParametros['arrayLineProfile'] = $objRequest->get('codLineProfile') ? $objRequest->get('codLineProfile') : "";
        $arrayParametros['strTipoPromocion'] = $objRequest->get('tipoPromocion');
        $arrayParametros['intIdEmpresa']     = $intIdEmpresa;
        $arrayParametros['strUsr']           = $strUsr;
        $arrayParametros['strIp']            = $strIp;
        $servicePromocionAnchoBanda          = $this->get('comercial.PromocionAnchoBanda');
        $objBeneficiariosOlt                 = $servicePromocionAnchoBanda->getBeneficiariosOlt($arrayParametros);
        $objResponse                         = new Response(json_encode($objBeneficiariosOlt));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
     /**
     * getEmpresaEnSesion, obtiene el prefijo de la empresa en sesión.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 05-04-2019
     *                    
     * @return Response lista de Motivos
     */
    public function getEmpresaEnSesionAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $objResponse      = new Response(json_encode(array('prefijoEmpresa'=> $strPrefijoEmpresa)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;	
    } 

}
