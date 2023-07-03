<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\AdmiCanton;
use telconet\schemaBundle\Entity\InfoPersona;
/**
 * InfoDetalleSolicitud controller.
 *
 */
class SolicitudDescuentoController extends Controller implements TokenAuthenticatedController
{
     private $tipoSolicitud='SOLICITUD DESCUENTO';
     const RANGO_APROBACION_SOLICITUDES      = 'RANGO_APROBACION_SOLICITUDES';
     const ADMINISTRACION_CARGOS_SOLICITUDES = 'ADMINISTRACION_CARGOS_SOLICITUDES';
     const COMERCIAL                         = 'COMERCIAL';
     const CARGO_GRUPO_ROLES_PERSONAL        = 'CARGO_GRUPO_ROLES_PERSONAL';
     const GRUPO_ROLES_PERSONAL              = 'GRUPO_ROLES_PERSONAL';
     const GERENTE_VENTAS                    = 'GERENTE_VENTAS';
     const ROLES_NO_PERMITIDOS               = 'ROLES_NO_PERMITIDOS'; 
     const TIPOS_AUTORIZACIONES              = 'TIPOS_AUTORIZACIONES';
     const AUTORIZACION_DESCUENTOS           = 'AUTORIZACION_DESCUENTOS';
     const VALOR_INICIAL_BUSQUEDA = 0;
     const VALOR_LIMITE_BUSQUEDA  = 10;
    /**
    * @Secure(roles="ROLE_62-1")
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.1 28-08-2020 - Se modifica opción de Solicitudes por motivo "Cliente con Discapacidad", se agregan controles y validación a nivel 
    *                           del ingreso de la solicitud se parametriza porcentaje de descuento y motivo.
    *                           Se valida que el beneficio por discapacidad solo se da por un login no es posible tener más de un servicio de 
    *                           internet dedicado con este beneficio. Aplicable para empresa MD.
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.2 18-01-2021 - Se modifica opción de Solicitudes por motivo "Beneficio 3era Edad / Adulto Mayor", se agregan controles y 
    *                           validaciones a nivel del ingreso de la solicitud se parametriza los calculos a aplicarse segun formulas y motivo de  
    *                           la solicitud, la pantalla realizará el cálculo de la solicitud y este no será editable:     
    *                          1)- Si el valor del plan >= FORMULA_PLAN_BASICO   (SALARIO_BASICO_UNIFICADO*PORCENTAJE_VALOR_RESIDENCIAL_BASICO/100)
    *                           se colocará que se realizará un descuento por el valor calculado de la formula :
    *                           FORMULA_DESC_ADULTO_MAYOR:  
    *                                           ((SALARIO_BASICO_UNIFICADO*PORCENTAJE_VALOR_RESIDENCIAL_BASICO/100)*PORCENTAJE_DESC_ADULTO_MAYOR/100)
    *                           se deberá precargar y no será editable el valor del descuento.
    *                          2)- Si el plan < FORMULA_PLAN_BASICO parametrizada se colocará el valor del plan por el PORCENTAJE_DESC_ADULTO_MAYOR
    *                           se deberá precargar y no será editable.
    *
    *                           Se implementa validación en base a Parámetro de validación de doble beneficio en solicitud de descuento fijo S/N, el
    *                           cual permitirá indicar si el cliente puede aplicar a 2 beneficios (Discapacitado y Beneficio 3eraEdad / Adulto Mayor)
    *                           Cada uno en un login diferente.
    *                           Se valida que el beneficio por 3er edad / Adulto mayor solo se da por un login no es posible tener más de un 
    *                           servicio de internet dedicado con este beneficio. Aplicable para empresa MD
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.3 12-08-2021 - Se elimina parámetro que obtenía el motivo de Beneficio 3era Edad / Adulto Mayor ya que se manejará mediante  
    *                           flujos de proceso para los motivos de tercera edad. 
    * 
    */ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session  = $request->getSession();
		$puntoIdSesion=null;
        $ptoCliente_sesion=$session->get('ptoCliente');
		if($ptoCliente_sesion){  
			$puntoIdSesion=$ptoCliente_sesion['id'];
		}
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("62", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());  
		
        $strCodEmpresa          = $session->get('idEmpresa');
        $strPrefijoEmpresa      = $session->get('prefijoEmpresa');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        //Motivo de Solicitud Discapacidad parametrizado
        $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                     'COMERCIAL',
                                                     '',
                                                     'MOTIVO_DESC_DISCAPACIDAD',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     $strCodEmpresa);
                                       
        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
        {
            $strMotivoDescDiscapacidad = $arrayValoresParametros["valor1"];
        }
        //Porcentaje de descuento Discapacidad parametrizado
        $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                     'COMERCIAL',
                                                     '',
                                                     'PORCENTAJE_DESCUENTO',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     $strCodEmpresa);
                                       
        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
        {
            $strPorcentajeDiscapacidad = $arrayValoresParametros["valor1"];
        }      
        
        return $this->render('comercialBundle:solicituddescuento:index.html.twig', array('strMotivoDescDiscapacidad' => $strMotivoDescDiscapacidad,
                                                                                         'strPorcentajeDiscapacidad' => $strPorcentajeDiscapacidad,
                                                                                         'strCodEmpresa'             => $strCodEmpresa,
                                                                                         'strPrefijoEmpresa'         => $strPrefijoEmpresa,
             'item' => $entityItemMenu,
            'entities' => '',
            'puntoId' => $puntoIdSesion
        ));
    }

    /**
     * grabaSolicitudDesc_ajaxAction, Se agrega relacion entre la solictud de descuento y la caracteristica
     *                                relacionada al tipo de descuento, UNITARIO o TOTALIZADO
     *                                                       
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 04-05-2017
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2 20-07-2017 - Se modifican los number_format por round. Para que no genere errores al ingresar valores mayores a 999 en la base.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.3 28-08-2020 - Se modifica opción de Solicitudes por motivo "Cliente con Discapacidad", se agregan controles y validación a nivel 
     *                           del ingreso de la solicitud se parametriza porcentaje de descuento y motivo.
     *                           Se valida que el beneficio por discapacidad solo se da por un login no es posible tener más de un servicio de 
     *                           internet dedicado con este beneficio. Aplicable para empresa MD
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.4 18-01-2021- Se implementa validación en base a Parámetro de validación de doble beneficio en solicitud de descuento fijo S/N, el
     *                          cual permitirá indicar si el cliente puede aplicar a 2 beneficios (Discapacitado y Beneficio 3eraEdad / Adulto Mayor)
     *                          Cada uno en un login diferente.
     *                          Se valida que el beneficio por 3er edad / Adulto mayor solo se da por un login no es posible tener más de un 
     *                          servicio de internet dedicado con este beneficio. Aplicable para empresa MD
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.5 11-03-2021- Se agrega Validación de Adulto mayor (> 65) y que cliente sea persona Natural
     *                          Valida que el "Beneficio 3era Edad / Adulto Mayor" solo se aplique a Planes Home segun parametrización.
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.6 13/08/2021 - Se modifica código que obtenía valor del motivo de beneficio adulto mayor parametrizado, para que mediante el
     *                           mismocon el nombre del motivo en proceso se retorne el flujo de proceso de adulto mayor tercera edad 
     *                           y se realiza las validaciones correspondientes para el flujo tercera edad. En el caso de ser otro motivo se  
     *                           devuelve vacío el valor del parámetro y se valida para la no afectación de los procesos del mismo.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.7 17-08-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     *
     */
    public function grabaSolicitudDesc_ajaxAction() {
        $request             = $this->getRequest();
        $session             = $request->getSession();         
        $idEmpresa           = $session->get('idEmpresa');
        $usrCreacion         = $session->get('user');
        $respuesta           = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $em                  = $this->getDoctrine()->getManager('telconet'); 

        //Obtiene parametros enviados desde el ajax
        $peticion            = $this->get('request');
        $parametro           = $peticion->get('param');
        $array_valor         = explode("|", $parametro);
        $relacionSistemaId   = $peticion->get('rs');
        $motivoId            = $peticion->get('motivoId');
        $tipoSolicitudId     = $peticion->get('ts');
        $tipoValor           = $peticion->get('tValor');
        $valor               = $peticion->get('v');
        $obs                 = $peticion->get('obs');
        $strTipoDescuento    = $peticion->get('tipoDescuento');
        $strDescripcionCarac = '';
        $serviceComercial    = $this->get('comercial.Comercial');
        $strMsnError         = '';
        $strStatus           = 'OK';
        $intCantidad         = 0;
        $floatPrecio         = 0;
        $floatPrecioDcto     = 0;
        
        $arrayPuntoSession   = $session->get('ptoCliente');
        $intIdPunto          = $arrayPuntoSession['id'];              
        $strCodEmpresa       = $session->get('idEmpresa');
        $strPrefijoEmpresa   = $session->get('prefijoEmpresa');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');        
        $objJsonResponse     = new JsonResponse();
        $arrayDestinatarios  = array();
        $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
        $em->getConnection()->beginTransaction();
        try {
            if ($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
            {
                $arrayParametroValidacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                               'COMERCIAL', 
                                                               '',
                                                               'VALIDACION_SOLICITUD_DISCAPACIDAD_POR_CLIENTE',
                                                               '',
                                                               '',
                                                               '',
                                                               '',
                                                               '',
                                                               $strCodEmpresa);                
                $strParametroValidacion   = (isset($arrayParametroValidacion["valor1"])
                                             && !empty($arrayParametroValidacion["valor1"])) ? $arrayParametroValidacion["valor1"] : 'S';
                                              
                //Obtengo los parámetros a validar Discapacidad
                $arrayMotivoDiscapacidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                              'COMERCIAL','','MOTIVO_DESC_DISCAPACIDAD','',
                                                              '','','','', $strCodEmpresa);
            
                $arrayNotificacionAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                                   'COMERCIAL','','','MENSAJE_INGRESO_SOLICITUD','',
                                                                   '','','',$strCodEmpresa);
            
                $arrayValorDobleBeneficio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                               'COMERCIAL','','DOBLE_BENEFICIO','',
                                                               '','','','',$strCodEmpresa);
            
                $arrayValorMaxCliente = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                           'COMERCIAL','','CANT_MAX_POR_CLIENTE','',
                                                           '','','','',$strCodEmpresa);
        
                $arrayValorMaxPunto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                         'COMERCIAL','','CANT_MAX_POR_PUNTO','',
                                                         '','','','',$strCodEmpresa);
            
                $arrayCantExistBeneficio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                              'COMERCIAL','','CANT_EXISTE_BENEFICIO','',
                                                              '','','','',$strCodEmpresa); 
                
                //Se obtiene el parámetro para ser enviado en el array si el valor de doble beneficio es "N".
                $arrayCantMaxCliente = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                          'COMERCIAL','','CANT_MAX_CLIENTE','',
                                                          '','','','',$strCodEmpresa);
                                               
                $strMensajeValidacion   = (isset($arrayNotificacionAdultoMayor["valor2"])
                                             && !empty($arrayNotificacionAdultoMayor["valor2"])) ? $arrayNotificacionAdultoMayor["valor2"]
                                             : 'Cliente ya posee descuento por {strMotivoBeneficio} Login: {strLogin}- Dirección: {strDireccion}.'
                                             . ' No puede ingresar una nueva solicitud de descuento.';
                
                //Se obtiene edad parametrizada para validar que cliente sea Adulto mayor > 65 
                $arrayEdadAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                           'COMERCIAL','','EDAD_ADULTO_MAYOR','',
                                                           '','','','', $strCodEmpresa);
                
                $intEdadParam  = (isset($arrayEdadAdultoMayor["valor1"])
                                  && !empty($arrayEdadAdultoMayor["valor1"])) ? intval($arrayEdadAdultoMayor["valor1"]) : 65;
                
                //Se obtiene mensaje de validación si cliente no cumple ser Adulto Mayor
                $arrayValidacionAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                                 'COMERCIAL','','','MENSAJE_VALIDACION_ADULTO_MAYOR','',
                                                                 '','','',$strCodEmpresa);
                
                $strMensajeValidacionAdultoMayor = (isset($arrayValidacionAdultoMayor["valor2"])
                                                   && !empty($arrayValidacionAdultoMayor["valor2"])) ? $arrayValidacionAdultoMayor["valor2"]
                                                   : 'No es Adulto Mayor.';
                
                //Obtiene los Tipos de planes permitidos para otrogar beneficio Adulto mayor
                $arrayTipoPlan = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                           'COMERCIAL','','TIPO_PLAN','',
                                                           '','','','', $strCodEmpresa);
                
                //Se obtiene mensaje de validación si cliente no cumple con el Tipo de Plan permitido.
                $arrayValidaPlanPermitido  =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                                 'COMERCIAL','','','MENSAJE_VALIDACION_PLANES_PERMITIDOS','',
                                                                 '','','',$strCodEmpresa);
                                               
                $strMsjValidaPlanPermitido = (isset($arrayValidaPlanPermitido["valor2"])
                                                   && !empty($arrayValidaPlanPermitido["valor2"])) ? $arrayValidaPlanPermitido["valor2"]
                                                   : 'Plan no permitido para el Beneficio.';
                                                
                $objPunto              = $em->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                $objPersonaEmpresaRol  = $objPunto->getPersonaEmpresaRolId();
                                
                $objMotivo             = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($motivoId);
                $strNombreMotivo       = $objMotivo->getNombreMotivo();
                
                //Con el nombre de motivo obtengo el flujo parametrizado a procesar
                $arrayFlujoAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                            $strNombreMotivo, '', '', '', '', $strCodEmpresa);

                $strFlujoAdultoMayor = !empty($arrayFlujoAdultoMayor["valor6"]) ? $arrayFlujoAdultoMayor["valor6"] : ""; 
                
                if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' || $strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_RESOLUCION_072021'
                    || $strNombreMotivo == $arrayMotivoDiscapacidad["valor1"])
                {
                    $arrayValidaSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                               ->validaSolDobleBeneficio(array('intIdPunto'            => $objPunto->getId(),
                                                                               'intIdPersonaRol'       => $objPersonaEmpresaRol->getId(), 
                                                                               'intCantMaxPorCliente'  => $arrayValorMaxCliente["valor1"],     
                                                                               'intCantMaxPorPunto'    => $arrayValorMaxPunto["valor1"],       
                                                                               'intCantExistBeneficio' => $arrayCantExistBeneficio["valor1"],  
                                                                               'intCantMaxCliente'     => $arrayCantMaxCliente["valor1"],      
                                                                               'strDobleBeneficio'     => $arrayValorDobleBeneficio["valor1"],
                                                                               'intMotivoId'           => $objMotivo->getId()
                                                                              ));
                   
                    $arrayRegistros   = $arrayValidaSolicitud['objRegistros'];
                    $strBanderaAplica = $arrayValidaSolicitud['strBanderaAplica'];
                                                          
                    if($strBanderaAplica == 'N')
                    {                           
                        foreach ($arrayRegistros as $arrayData)
                        {                               
                            $strMensajeValidacionPto = $strMensajeValidacion;
                            $strMensajeValidacionPto = str_replace('strMotivoBeneficio', $arrayData['nombreMotivo'], $strMensajeValidacionPto);
                            $strMensajeValidacionPto = str_replace('strLogin', $arrayData['login'], $strMensajeValidacionPto);
                            $strMensajeValidacionPto = str_replace('strDireccion', $arrayData['direccion'], $strMensajeValidacionPto);
                            $strMsnError .= $strMensajeValidacionPto;
                            $strMsnError .= "<br><br>"; 
                        }    
                        $strStatus = 'Error';
                        throw new \Exception($strMsnError);
                    }
                }
                else
                {                                                    
                    //Valida si existe en el punto una solicitud de '3era Edad / Adulto Mayor' ó 'Cliente con Discapacidad'.
                    $arrayValidaSolOtrosMotivos = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->getSolicitudesPorPunto(array('intIdPunto' => $objPunto->getId()));
                    $arraySolOtrosMotivos       = $arrayValidaSolOtrosMotivos['objRegistros'];
                    $intCantidad                = $arrayValidaSolOtrosMotivos['intCantidad'];
                    
                    if($intCantidad > 0)
                    {                                        
                        foreach ($arraySolOtrosMotivos as $arrayData)
                        {                               
                            $strMensajeValidacionPto = $strMensajeValidacion;
                            $strMensajeValidacionPto = str_replace('strMotivoBeneficio', $arrayData['nombreMotivo'], $strMensajeValidacionPto);
                            $strMensajeValidacionPto = str_replace('strLogin', $arrayData['login'], $strMensajeValidacionPto);
                            $strMensajeValidacionPto = str_replace('strDireccion', $arrayData['direccion'], $strMensajeValidacionPto);
                            $strMsnError .= $strMensajeValidacionPto;
                            $strMsnError .= "<br><br>"; 
                        }                                                
                        $strStatus = 'Error';
                        throw new \Exception($strMsnError);
                    }
                }
                if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' || $strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
                {   
                    //Validación Tipo Tributario
                    $objInfoPersonaService = $this->get('comercial.InfoPersona');
                    $strMsjValidaTipoTributario = $objInfoPersonaService->getValidaTipoTributario(
                                                                          array('intIdPersona'  => $objPersonaEmpresaRol->getPersonaId()->getId(),
                                                                                'strCodEmpresa' => $strCodEmpresa));                    
                    if(!empty($strMsjValidaTipoTributario))
                    {
                        $strStatus = 'Error';
                        throw new \Exception($strMsjValidaTipoTributario);
                    }
                    
                    //Validacion Adulto Mayor
                    $intEdadCliente = $em->getRepository('schemaBundle:InfoPersona')
                                         ->getEdadPersona(array('intIdPersona' => $objPersonaEmpresaRol->getPersonaId()->getId()));
                    
                    if($intEdadCliente < $intEdadParam)
                    {                        
                        $strStatus = 'Error';
                        throw new \Exception($strMensajeValidacionAdultoMayor);
                    }
                   
                }      
            }
            if($strTipoDescuento =='DESCUENTO_UNITARIO')
            {
                $strDescripcionCarac = 'DESCUENTO UNITARIO FACT';
            }
            else if ($strTipoDescuento == 'DESCUENTO_TOTALIZADO')
            {
                $strDescripcionCarac = 'DESCUENTO TOTALIZADO FACT';
            }
            
            foreach ($array_valor as $id):
                
                if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' || $strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
                {
                    //Valida que el beneficio de tercera edad solo se aplique a Planes Home segun parametro.
                    $intExisteServicio = $em->getRepository("schemaBundle:InfoServicio")
                                            ->getServicioTipoPlan(array('intIdServicio'  => $id,
                                                                        'arrayTipoPlan'  => $arrayTipoPlan));
                    
                    if($strPrefijoEmpresa === 'MD' && $intExisteServicio === 0)
                    {
                        $strStatus = 'Error';
                        throw new \Exception($strMsjValidaPlanPermitido);
                    }
                }
                $entity                 = new InfoDetalleSolicitud();
                $entity->setMotivoId($motivoId);
                $entityServicio         =    $em->getRepository('schemaBundle:InfoServicio')->find($id);
                $entity->setServicioId($entityServicio);
                $entityTipoSolicitud    = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
                $entity->setTipoSolicitudId($entityTipoSolicitud);
                               
                if($tipoValor=='porcentaje')
                {
                     $intCantidad = $entityServicio->getCantidad();
                     $floatPrecio = $entityServicio->getPrecioVenta();
                    
                     $floatPrecioDcto = ( $intCantidad * $floatPrecio ) * ($valor / 100); 
                         
                     $entity->setPorcentajeDescuento($valor);
                     $entity->setPrecioDescuento(round( $floatPrecioDcto  , 2));
                }
                elseif($tipoValor=='valor')
                    $entity->setPrecioDescuento($valor);
                
                $entity->setObservacion($obs);
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($usrCreacion);
                $entity->setEstado('Pendiente');
                $em->persist($entity);
                $em->flush();
                
                //Busca la caracteristica asociada al descuento.
                $entityAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCarac,
                                                                 'estado'                    => 'Activo'));
                
                if( !is_object($entityAdmiCaracteristica) )
                {
                    $strMsnError = 'No se pudo generar solicitud de descuento, no existe caracteristica asociada a la empresa.';
                    throw new \Exception( $strMsnError );
                }
                
                //Crea array para generar el objeto detalle solicitud caracteristica
                $arrayRequestDetalleSolCaract = array();
                $arrayRequestDetalleSolCaract['entityAdmiCaracteristica']   = $entityAdmiCaracteristica;
                $arrayRequestDetalleSolCaract['floatValor']                 = round( $valor  , 2);
                $arrayRequestDetalleSolCaract['entityDetalleSolicitud']     = $entity;
                $arrayRequestDetalleSolCaract['strEstado']                  = 'Pendiente';
                $arrayRequestDetalleSolCaract['strUsrCreacion']             = $usrCreacion;
                
                //Crea el objeto InfoDetalleSolCaract
                $entityDetalleSolCaract = $serviceComercial->creaObjetoInfoDetalleSolCaract($arrayRequestDetalleSolCaract);
                
                $em->persist($entityDetalleSolCaract);
                $em->flush();
                
                //Grabamos en la tabla de historial de la solicitud
				$entityHistorial = new InfoDetalleSolHist();
				$entityHistorial->setEstado('Pendiente');
				$entityHistorial->setDetalleSolicitudId($entity);
				$entityHistorial->setUsrCreacion($usrCreacion);
				$entityHistorial->setFeCreacion(new \DateTime('now'));
				$entityHistorial->setIpCreacion($request->getClientIp());
				$entityHistorial->setMotivoId($motivoId);
				$entityHistorial->setObservacion($obs);
                $em->persist($entityHistorial);
                $em->flush();
                //Bloque que obtiene los correos de la persona que crea la solicitud, vendedor asociado y subgerente.
                if($strPrefijoEmpresa == 'TN')
                {
                    $arrayDestinatarios       = array();
                    $objPunto                 = (is_object($entityServicio)) ? $entityServicio->getPuntoId():"";
                    $strVendedor              = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                    $objPersona               = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                    $strCliente               = "";
                    $strIdentificacion        = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                    $strCliente               = (is_object($objPersona) && $objPersona->getRazonSocial()) ?
                                                $objPersona->getRazonSocial() : $objPersona->getNombres() . " " .$objPersona->getApellidos();
                    $floatPrecioVenta         = $entityServicio->getPrecioVenta() ? $entityServicio->getPrecioVenta():0;
                    $intCantidad              = $entityServicio->getCantidad() ? $entityServicio->getCantidad():0;
                    $floatPrecioDescuento     = $entity->getPrecioDescuento() ? $entity->getPrecioDescuento() : 0;
                    $floatPorcentajeDescuento = $entity->getPorcentajeDescuento() ? $entity->getPorcentajeDescuento() : 0;
                    $floatValorTotal          = 0;
                    if((!empty($floatPrecioVenta) && $floatPrecioVenta > 0) && (!empty($intCantidad) && $intCantidad > 0))
                    {
                        $floatValorTotal = $floatPrecioVenta * $intCantidad;
                    }
                    $floatValorFinal = $floatValorTotal - $floatPrecioDescuento;
                    if((empty($floatPorcentajeDescuento)&&$floatPorcentajeDescuento==0)&&(!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                    {
                        $floatPorcentajeDescuento = ($floatPrecioDescuento * 100)/$floatValorTotal;
                    }
                    $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                              ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                'modulo'          => self::COMERCIAL,
                                                                'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                'estado'          => 'Activo'));
                    if(!is_object($objCargosCab) || empty($objCargosCab))
                    {
                        throw new \Exception('No se encontraron datos con los parámetros enviados.');
                    }
                    $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                               'valor4'      => 'ES_JEFE',
                                                               'valor7'      => 'SI',
                                                               'estado'      => 'Activo'));
                    $strCargoAsignado = "";
                    if(is_array($arrayCargosDet) && $strPrefijoEmpresa == "TN")
                    {
                        foreach($arrayCargosDet as $objCargosItem)
                        {
                            if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                               floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                            {
                                $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                            }
                        }
                    }
                    //Correo del vendedor.
                    $arrayCorreos = $em->getRepository('schemaBundle:InfoPersona')
                                       ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                    "Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    //Correo del subgerente
                    $arrayResultadoCorreo    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                    if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                    {
                        $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                        $arrayCorreos         = $em->getRepository('schemaBundle:InfoPersona')
                                                   ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                    }
                    //Correo de la persona quien crea la solicitud.
                    $arrayCorreos = $em->getRepository('schemaBundle:InfoPersona')
                                       ->getContactosByLoginPersonaAndFormaContacto($usrCreacion,"Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $strCuerpoCorreo      = "El presente correo es para indicarle que se creó una solicitud en TelcoS+ con los siguientes datos:";
                    $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                  "strIdentificacionCliente" => $strIdentificacion,
                                                  "strObservacion"           => $obs,
                                                  "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                  "strCargoAsignado"         => $strCargoAsignado);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("CREACIÓN DE SOLICITUD DE DESCUENTO",
                                                                  array_unique($arrayDestinatarios),
                                                                  "NOTIFICACION",
                                                                  $arrayParametrosMail,
                                                                  $strPrefijoEmpresa,
                                                                  "",
                                                                  "",
                                                                  null,
                                                                  true,
                                                                  "notificaciones_telcos@telconet.ec");
                }
            endforeach;
            $em->getConnection()->commit();            
            
            $strMsnError = 'Se registro solicitud con exito.';            
        
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            error_log('SolicitudDescuentoController->grabaSolicitudDesc_ajaxAction ' . $e->getMessage());
            
            if(empty( $strMsnError )) {
                $strMsnError = 'Ocurrio un error al generar la solicitud de descuento. ';
            }                        
            $strMsnError = $e->getMessage();
        }     
        $arrayRespuesta = array("strStatus"   => $strStatus,
                                "strMensaje"  => $strMsnError);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    } 
    
    public function getMotivos_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')->findMotivosPorDescripcionTipoSolicitud($this->tipoSolicitud);
        $entityAdmiTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findByDescripcionSolicitud($this->tipoSolicitud);
	$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId(),
                'idTipoSolicitud'=> $entityAdmiTipoSolicitud[0]->getId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }      

    /**
    * Documentación para funcion 'getServiciosParaSolicitudDesc_ajaxAction'.
    * Funcion que envia los datos de los servicios para el listado de solicitudes 
    * de descuento unico.
    * Se agrega nombre del producto al listado de solicitudes de descuento unico.
    * 
    * @author <rcoello@telconet.ec>
    * @version 1.1 05-05-2017
    * 
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.2 24-01-2018 - Se restringe la solicitud de descuento para el producto 'INTERNET SMALL BUSINESS'.
    * 
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.3 02-09-2020 - Se valida marca de descuento para TelcoHome.
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.4 19-01-2021 - Se habilita bandera para marcar si el servicio corresponde a un plan con producto de internet dedicado parametrizado
    *                           por codigo_producto : INTD 
    * 
    * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
    * @version 1.5 18-05-2021 - Se elimina restricción de solicitud de descuento para 'INTERNET SMALL BUSINESS'
    *                           por requerimiento de Erika Intriago.
    * 
    * @return objeto - response
    * 
    * @Secure(roles="ROLE_62-161")
    */     
    public function getServiciosParaSolicitudDesc_ajaxAction($id) {
        $request    = $this->getRequest();
        $filter     = $request->request->get("filter");
        $estado     = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado     = $request->get("estado");
        $nombre     = $request->get("nombre");
        $limit      = $request->get("limit");
        $page       = $request->get("page");
        $start      = $request->get("start");
        $idEmpresa  = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa =$request->getSession()->get('prefijoEmpresa');

        $em         = $this->get('doctrine')->getManager('telconet');
        $emGeneral  = $this->get('doctrine')->getManager('telconet_general');
        $resultado  = $em->getRepository('schemaBundle:InfoServicio')->getServiciosSolicitudDcto($idEmpresa,$id,$limit,$start);
        $datos      = $resultado['registros'];
        $total      = $resultado['total'];
        
        $arrayParametroCodProducto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                       'COMERCIAL', 
                                                       '',
                                                       'CODIGO_PRODUCTO',
                                                       '',
                                                       '',
                                                       '',
                                                       '',
                                                       '',
                                                       $idEmpresa);                
        $strParametroCodProducto   = (isset($arrayParametroCodProducto["valor1"])
                                    && !empty($arrayParametroCodProducto["valor1"])) ? $arrayParametroCodProducto["valor1"] : 'INTD';
                
        foreach ($datos as $dato):
            //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
            $detalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                   ->findSolicDescuentoPorServicio($dato->getId(),
                                                                   $this->tipoSolicitud,
                                                                   'Pendiente');
            if ($detalleSolicitud)
                $yaFueSolicitada='S';
            else
                $yaFueSolicitada='N';
            
            $strAplicaDesc      = 'SI';
            $strNombreTecnico   = '';
            
            $strAplicaDescDiscapacidadAdultoMayor  = 'SI';
            
            if($strPrefijoEmpresa === 'TN')
            {
                $objProducto   = $em->getRepository('schemaBundle:AdmiProducto')->find($dato->getProductoId());
            
                $strNombreTecnico = (is_object($objProducto)) ? $objProducto->getNombreTecnico() : '';
               
            }
            
            $idProducto             = '';
            $descripcionProducto    = '';
            $strNombreProducto      = '';
            
            if ($dato->getProductoId()){
                $idProducto          = $dato->getProductoId()->getId();
                $descripcionProducto = $dato->getProductoId()->getDescripcionProducto();
                $strNombreProducto   = $dato->getProductoId()->getDescripcionProducto();
                $tipo                = 'producto';
                if($strPrefijoEmpresa === 'MD' && $dato->getProductoId()->getCodigoProducto() != $strParametroCodProducto)
                {
                    $strAplicaDescDiscapacidadAdultoMayor  = 'NO';     
                }
            }elseif($dato->getPlanId())
            {
                $tipo                = 'plan';
                $idProducto          = $dato->getPlanId()->getId();
                $descripcionProducto = $dato->getPlanId()->getDescripcionPlan();
                $strNombreProducto   = $dato->getPlanId()->getNombrePlan();
                $intExisteServicio = 0;
                $intExisteServicio = $em->getRepository("schemaBundle:InfoServicio")
                                        ->getServicioPlanCodProducto(array('intIdServicio'      => $dato->getId(),
                                                                           'strCodigoProducto'  => $strParametroCodProducto ));
            
                if($strPrefijoEmpresa === 'MD' && $intExisteServicio===0)
                {
                    $strAplicaDescDiscapacidadAdultoMayor  = 'NO';    
                }
            }            
            $arreglo[] = array(
                'idServicio'            => $dato->getId(),
                'tipo'                  => $tipo,
                'idPunto'               => $dato->getPuntoId()->getId(),
                'descripcionPunto'      => $dato->getPuntoId()->getDescripcionPunto(),
                'idProducto'            => $idProducto,
                'descripcionProducto'   => $descripcionProducto,
                'cantidad'              => $dato->getCantidad(),
                'fechaCreacion'         => strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")),
                'precioVenta'           => $dato->getPrecioVenta(),
                'estado'                => $dato->getEstado(),
                'yaFueSolicitada'       => $yaFueSolicitada,
                'strNombreProducto'     => $strNombreProducto,
                'strAplicaDesc'         => $strAplicaDesc,
                'strAplicaDescDiscapacidadAdultoMayor' => $strAplicaDescDiscapacidadAdultoMayor
             );
            
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total'      => $total, 
                                                       'servicios'  => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total'      => $total, 
                                                       'servicios'  => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    

    /**
     * @Secure(roles="ROLE_443-1")
     *
     * Documentación para la función 'gestionDescuentoAction'.
     *
     * Función que direciona a la pagina principal de Gestión de Descuento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 28-12-2019
     * 
     * @return Response se retorna los valores necesarios para visualizar el valor pendiente por aprobar y cortesías.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 15-06-2021 - Se retorna los roles permitidos para gestionar las acciones de las solicitudes.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 15-10-2022 - Actualización de Twig.
     *
     */
    public function gestionDescuentoAction()
    {
        $objRequest        = $this->getRequest();
        $intIdEmpresa      = $objRequest->getSession()->get('idEmpresa');
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $strUsrCreacion    = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpClient       = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtil       = $this->get('schema.Util');

        $arrayRolesPermitidos = array();
        $strEstadoCargo       = 'Todos';
        $strCargosAdicionales = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
        $arrayTipoPersonal    = ["VENDEDOR","ASISTENTE"];
        $strAutorizacion      = "NO";
        try
        {
            $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                      ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                        'modulo'          => self::COMERCIAL,
                                                        'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                        'estado'          => 'Activo'));
            if(!is_object($objCargosCab) || empty($objCargosCab))
            {
                throw new \Exception('No se encontraron datos con los parámetros enviados.');
            }
            $arrayCargoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
            $strTipoPersonal   = (!empty($arrayCargoPersona)&&is_array($arrayCargoPersona)) ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';
            $strAutorizacion   = in_array($strTipoPersonal,$arrayTipoPersonal) ? "NO":"SI";
            $objCargosDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findOneBy(array('parametroId' => $objCargosCab->getId(),
                                                             'valor3'        => $strTipoPersonal,
                                                             'valor4'        => 'ES_JEFE',
                                                             'estado'        => 'Activo'));
            if(!empty($objCargosDet) && is_object($objCargosDet))
            {
                $strEstadoCargo = ucwords(strtolower(str_replace("-"," ",$objCargosDet->getValor6())));
            }
            else
            {
                $objCargosCabAux = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                             ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                               'modulo'          => self::COMERCIAL,
                                                               'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                               'estado'          => 'Activo'));
                if(!empty($objCargosCabAux) && is_object($objCargosCabAux))
                {
                    $objCargosDetAux = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objCargosCabAux->getId(),
                                                                   'valor4'        => 'ES_JEFE',
                                                                   'estado'        => 'Activo',
                                                                   'observacion'   => $strUsrCreacion));
                    if(!empty($objCargosDetAux) && is_object($objCargosDetAux))
                    {
                        $strEstadoCargo = ucwords(strtolower(str_replace("-"," ",$objCargosDetAux->getValor6())));
                    }
                }
            }
            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->totalServiciosPorSolicitud($this->tipoSolicitud,'Pendiente', $intIdEmpresa);

            $strAnioActual  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                          ->totalServicioCortesiaAnual(date("Y"),'Activo',$intIdEmpresa);

            $strAnioPasado  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                          ->totalServicioCortesiaAnual(date("Y")-1,'Activo',$intIdEmpresa);
            //Perfil:Solo visualizar las solicitudes(Asistente, vendedores)
            if ($this->get('security.context')->isGranted('ROLE_443-1'))
            {
                $arrayRolesPermitidos[] = 'ROLE_443-1';
            }
            //Perfil:AProbar, Rechazar Solicitud de descuento, instalación(Subgerente,Gerente de ventas, Vicepresidente)
            if ($this->get('security.context')->isGranted('ROLE_443-7017'))
            {
                $arrayRolesPermitidos[] = 'ROLE_443-7017';
            }
            //Perfil:AProbar, Rechazar Solicitud Cambio de documento(Gerente General, Gerente General Regional)
            if ($this->get('security.context')->isGranted('ROLE_443-7037'))
            {
                $arrayRolesPermitidos[] = 'ROLE_443-7037';
            }
            //Perfil:Cambiar Precio de Traslado
            if ($this->get('security.context')->isGranted('ROLE_404-5639'))
            {
                $arrayRolesPermitidos[] = 'ROLE_404-5639';
            }
            //Perfil:AProbar, Rechazar Solicitud de Traslado
            if ($this->get('security.context')->isGranted('ROLE_404-5917'))
            {
                $arrayRolesPermitidos[] = 'ROLE_404-5917';
            }
            //Perfil:AProbar, Rechazar Solicitud de Reubicación
            if ($this->get('security.context')->isGranted('ROLE_404-5638'))
            {
                $arrayRolesPermitidos[] = 'ROLE_404-5638';
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudDescuentoController->gestionDescuentoAction', 
                                      $ex->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient);
        }
        return $this->render('comercialBundle:GestionAutorizaciones:index.html.twig', array(
                             'totalAprobar'        => '$'.$arrayResultado[0]['total'],
                             'totalAnioActual'     => '$'.(round($strAnioActual[0]['total'], 2)),
                             'totalAnioPasado'     => '$'.(round($strAnioPasado[0]['total'], 2)),
                             'strEstadoCargo'      => $strEstadoCargo,
                             'rolesPermitidos'     => $arrayRolesPermitidos,
                             'strPrefijoEmpresa'   => $strPrefijoEmpresa,
                             'strAutorizacion'     => $strAutorizacion));
    }
    /**          
     * Documentación para la función 'aprobarDescuentoAction'.
     *
     * Función que carga pantalla de Aprobación de Solicitudes de descuento Fijo.
     * @since  1.1
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>     
     * @version 1.1 10-09-2020 - Se agrega parametro de prefijo empresa en sesion.          
     *
     */
    public function aprobarDescuentoAction()
    {
        $objRequest        = $this->getRequest();
        $strPrefijoEmpresa = $objRequest->getSession()->get('prefijoEmpresa');
        return $this->render('comercialBundle:solicituddescuento:aprobarDescuento.html.twig', array('strPrefijoEmpresa' => $strPrefijoEmpresa));
    }
    /**
    * Documentación para funcion 'gridAprobarDescuentoAction'.
    * funcion que envia los datos para el listado de solicitudes de descuento
    * @author <amontero@telconet.ec>
    * @since 12/12/2014
    * @return objeto - response
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.1 13-12-2017 Se adicciona los parametros nombre, apellido, razon social, usuario creacion,
    *                         y login, el método findSolicDescuentoPorCriterios recibirá por parametro un array 
    * 
    * Se extrae el array $datos por medio del id del servicio, el precio de venta, la cantidad del servicio y
    * el asesor del servicio(vendedor) y en caso que el $arreglo este vacio se retorna ese valor.
    * @author Douglas Natha <dnatha@telconet.ec>
    * @version 1.2 20-11-2019
    * @since 1.1 
    *
    * @author David León <mdleon@telconet.ec>
    * @version 1.3 27-01-2020 Se valida existencia del código del vendedor.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.4 03-02-2020 - Se envía un arreglo vacío en caso de que no existan solicitudes pendientes por aprobar,
    *                           para visualizar mensaje al usuario en el grid.
    *
    * @author : Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.5 15-06-2021 - Se realiza cambio para que la consulta de solicitudes se realice a través de la persona en sesión
    *                           en caso de ser asistente solo tendrá acceso a las solicitudes de los vendedores asignados al asistente
    *                           en caso de ser vendedor solo tendrá acceso a sus solicitudes
    *                           en caso de ser subgerente solo tendrá acceso a solicitudes de los vendedores que reportan al subgerente
    *                           en caso de ser gerente u otro cargo no aplican los cambios.
    *                           Se agrega la capacidad1, capacidad2 y una variable booleana la cual mostrará dichas capacidades en caso
    *                           que el producto sea Internet Dedicado o L3MPLS, se valida que el vicepresidente comercial apruebe sus
    *                           sus solicitudes hasta su rango máximo permitido y se valida si el vendedor del servicio es un vendedor
    *                           kam.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.6 03-12-2021 - Se establece nueva lógica para visualizar las solicitudes de acuerdo al rango de aprobación y cargo,
    *                           los cambios solo aplican para Telconet.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.7 17-08-2022 - Se agregan nuevos parámetros de búsqueda.
    */ 
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarDescuentoAction()
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $intIdEmpresa            = $objSession->get('idEmpresa');
        $intIdCanton             = $objSession->get('intIdCanton') ? $objSession->get('intIdCanton') : "";
        $strFechaDesde           = explode('T', $objRequest->get("fechaDesde"));
        $strFechaHasta           = explode('T', $objRequest->get("fechaHasta"));
        $strNombre               = $objRequest->get("nombre");
        $strApellido             = $objRequest->get("apellido");
        $strRazonSocial          = $objRequest->get("razonSocial");
        $strUsuarioCreacion      = $objRequest->get("usuarioCreacion");
        $strEstadoCargo          = $objRequest->get("strEstadoFiltro");
        $strIsp                  = $objRequest->get("strIsp") ? $objRequest->get("strIsp"):"No";
        $strIpCreacion           = $objRequest->getClientIp();
        $strLogin                = $objRequest->get("login");
        $strIdentificacion       = $objRequest->get("identificacion");
        $boolVerTodo             = $objRequest->get("boolVerTodo") ? $objRequest->get("boolVerTodo"): "NO";
        $intLimit                = $objRequest->get("limit") ? $objRequest->get("limit"): self::VALOR_LIMITE_BUSQUEDA;
        $intStart                = $objRequest->get("start") ? $objRequest->get("start"): self::VALOR_INICIAL_BUSQUEDA;
        $strDraw                 = $objRequest->get("draw")  ? $objRequest->get("draw"):"1";
        $em                      = $this->get('doctrine')->getManager('telconet');
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtilidades       = $this->get('administracion.Utilidades');
        $arrayParametros         = array();
        $intIdEmpRol             = $objSession->get('idPersonaEmpresaRol');
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $strCodEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion          = $objSession->get('user') ? $objSession->get('user') : '';
        $serviceUtil             = $this->get('schema.Util');
        $arrayCargoPersona       = "Otros";
        $strCargosAdicionales    = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
        $strMensajeCargo         = '';
        $arrayLoginVendedoresKam = array();
        $arrayRolesNoIncluidos   = array();
        $strRegionSesion         = "";
        $floatDescPorAprobarIni  = 0;
        $floatDescPorAprobarFin  = 0;
        $intTotal                = 0;
        $strTipoPersonal         = "Otros";
        $arrayTipoPersonal       = ["Otros","VENDEDOR","ASISTENTE"];
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO
             */
            if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
            {
                $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                          ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                            'modulo'          => self::COMERCIAL,
                                                            'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                            'estado'          => 'Activo'));
                if(!is_object($objCargosCab) || empty($objCargosCab))
                {
                    throw new \Exception('No se encontraron datos con los parámetros enviados.');
                }
                $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                           'valor4'      => 'ES_JEFE',
                                                           'valor7'      => 'SI',
                                                           'estado'      => 'Activo'));
                foreach($arrayCargosDet as $objItem)
                {
                    $arrayCargos = $arrayCargos.''.ucwords(strtolower(str_replace("_"," ",$objItem->getValor3()))).'|';
                }
                $arrayCargoPersona = $em->getRepository('schemaBundle:InfoPersona')
                                        ->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
                $strTipoPersonal   = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona)) ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';

                if($strTipoPersonal == '' || is_null($strTipoPersonal))
                {
                    $strMensajeCargo = 'El usuario no tiene un cargo definido, por favor consultar con sistemas.';
                }

                if(!empty($strTipoPersonal) && $strTipoPersonal!='Otros')
                {
                    $objCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->findOneBy(array('parametroId' => $objCargosCab->getId(),
                                                                'valor3'      => $strTipoPersonal,
                                                                'valor4'      => 'ES_JEFE',
                                                                'estado'      => 'Activo'));
                    $strEstadoCargoAprobar   = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor6():'';
                    $floatDescPorAprobarIni  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor1():'';
                    $floatDescPorAprobarFin  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor2():'';
                    $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                    $arrayParametros['intIdPersonEmpresaRol'] = $intIdEmpRol;
                    $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                }
                else
                {
                    $objCargosCabAux = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                   'modulo'          => self::COMERCIAL,
                                                                   'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                   'estado'          => 'Activo'));
                    if(!empty($objCargosCabAux) && is_object($objCargosCabAux))
                    {
                        $objCargosDetAux = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objCargosCabAux->getId(),
                                                                       'valor4'        => 'ES_JEFE',
                                                                       'estado'        => 'Activo',
                                                                       'observacion'   => $strUsrCreacion));
                        if(!empty($objCargosDetAux) && is_object($objCargosDetAux))
                        {
                            $strTipoPersonalAux    = $objCargosDetAux->getValor3();
                            $strEstadoCargoAprobar = $objCargosDetAux->getValor6();
                            if($strTipoPersonalAux == "GERENTE_VENTAS")
                            {
                                $arrayParametros['strTipoPersonal']       = $strTipoPersonalAux;
                                $arrayParametros['intIdPersonEmpresaRol'] = $intIdEmpRol;
                                $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                            }
                        }
                    }
                }
                /**
                 * BLOQUE QUE OBTIENE EL LISTADO DE VENDEDORES KAMS
                 */
                $arrayParametrosKam                          = array();
                $arrayResultadoVendedoresKam                 = array();
                $arrayParametrosKam['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                $arrayParametrosKam['strCodEmpresa']         = $strCodEmpresa;
                $arrayParametrosKam['strEstadoActivo']       = 'Activo';
                $arrayParametrosKam['strDescCaracteristica'] = self::CARGO_GRUPO_ROLES_PERSONAL;
                $arrayParametrosKam['strNombreParametro']    = self::GRUPO_ROLES_PERSONAL;
                $arrayParametrosKam['strDescCargo']          = self::GERENTE_VENTAS;
                $arrayParametrosKam['strDescRolNoPermitido'] = self::ROLES_NO_PERMITIDOS;
                $arrayResultadoVendedoresKam                 = $em->getRepository('schemaBundle:InfoPersona')
                                                                  ->getVendedoresKams($arrayParametrosKam);
                if(isset($arrayResultadoVendedoresKam['error']) && !empty($arrayResultadoVendedoresKam['error']))
                {
                    throw new \Exception($arrayResultadoVendedoresKam['error']);
                }
                if(!empty($arrayResultadoVendedoresKam['vendedoresKam']) && is_array($arrayResultadoVendedoresKam['vendedoresKam']))
                {
                    foreach($arrayResultadoVendedoresKam['vendedoresKam'] as $arrayItem)
                    {
                        $arrayLoginVendedoresKam[] = $arrayItem['LOGIN'];
                    }
                }
                /**
                 * BLOQUE QUE OBTIENE LA REGIÓN EN SESIÓN Y LOS PARÁMETROS NECESARIOS PARA FILTRAR POR REGIÓN
                 */
                if(empty($intIdCanton))
                {
                    throw new \Exception('Error al obtener el cantón del usuario en sesión.');
                }
                $objCanton = $em->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
                if(empty($objCanton) || !is_object($objCanton))
                {
                    throw new \Exception('Error al obtener el cantón del usuario en sesión.');
                }
                $strRegionSesion       = $objCanton->getRegion();
                $arrayParametrosRoles  = array( 'strCodEmpresa'     => $intIdEmpresa,
                                                'strValorRetornar'  => 'descripcion',
                                                'strNombreProceso'  => 'JEFES',
                                                'strNombreModulo'   => 'COMERCIAL',
                                                'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                'strUsrCreacion'    => $strUsrCreacion,
                                                'strIpCreacion'     => $strIpCreacion );
                $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);
                if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                    {
                        $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                    }
                }
            }
            $arrayParametros['strEstado']               = (!empty($strEstadoCargo)) ? str_replace(" ","-",$strEstadoCargo):'PENDIENTE';
            $arrayParametros['strTipoSolicitud']        = $this->tipoSolicitud;
            $arrayParametros['intIdEmpresa']            = $intIdEmpresa;
            $arrayParametros['strFechaDesde']           = $strFechaDesde;
            $arrayParametros['strFechaHasta']           = $strFechaHasta;
            $arrayParametros['strNombre']               = $strNombre;
            $arrayParametros['strApellido']             = $strApellido;
            $arrayParametros['strRazonSocial']          = $strRazonSocial;
            $arrayParametros['strIdentificacion']       = $strIdentificacion;
            $arrayParametros['strUsuarioCreacion']      = $strUsuarioCreacion;
            $arrayParametros['strLogin']                = $strLogin;
            $arrayParametros['intStart']                = $intStart;
            $arrayParametros['intLimit']                = $intLimit;
            $arrayParametros['arrayLoginVendedoresKam'] = $arrayLoginVendedoresKam;
            $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
            $arrayParametros['strRegion']               = $strRegionSesion;
            $arrayParametros['strIsp']                  = $strIsp;
            $arrayParametros['boolVerTodo']             = $boolVerTodo;
            $arrayParametros['arrayRolNoPermitido']     = (!empty($arrayRolesNoIncluidos) && is_array($arrayRolesNoIncluidos))?
                                                          $arrayRolesNoIncluidos:"";
            $arrayResultado                             = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                             ->findSolicDescuentoPorCriterios($arrayParametros);
            if(!empty($arrayResultado) && is_array($arrayResultado))
            {
                $intTotal = $arrayResultado["total"] ? $arrayResultado["total"]:0;
                foreach($arrayResultado['registros'] as $objItem)
                {
                    $strNombreMotivo         = '';
                    $linkVer                 = '#';
                    $strNombresCompletos     = '';
                    $objIdServicio           = $objItem->getServicioId();
                    $objPunto                = $objIdServicio->getPuntoId();
                    if(!empty($objIdServicio) && is_object($objIdServicio))
                    {
                        $strLoginVendedor    = $objIdServicio->getUsrVendedor();
                        $objPersonaVendedor  = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$strLoginVendedor));
                        $strNombresCompletos = (!empty($objPersonaVendedor) && is_object($objPersonaVendedor)) 
                                               ? $objPersonaVendedor->getNombres().' '.$objPersonaVendedor->getApellidos(): $strLoginVendedor;
                    }
                    else
                    {
                        throw new \Exception('No existe servicio asociado con la solicitud.');
                    }
                    $strTipoNegocioPto        = $objPunto->getTipoNegocioId()->getCodigoTipoNegocio();
                    $floatPrecioVenta         = $objIdServicio->getPrecioVenta() ? $objIdServicio->getPrecioVenta():0;
                    $intCantidad              = $objIdServicio->getCantidad() ? $objIdServicio->getCantidad():0;
                    $floatPrecioDescuento     = $objItem->getPrecioDescuento() ? $objItem->getPrecioDescuento() : 0;
                    $floatPorcentajeDescuento = $objItem->getPorcentajeDescuento() ? $objItem->getPorcentajeDescuento() : 0;

                    if((!empty($floatPrecioVenta) && $floatPrecioVenta > 0) && (!empty($intCantidad) && $intCantidad > 0))
                    {
                        $floatValorTotal = $floatPrecioVenta * $intCantidad;
                    }
                    else
                    {
                        $floatValorTotal = 0;
                    }
                    $floatValorFinal = $floatValorTotal - $floatPrecioDescuento;
                    if((empty($floatPorcentajeDescuento)&&$floatPorcentajeDescuento==0)&&(!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                    {
                        $floatPorcentajeDescuento = ($floatPrecioDescuento * 100)/$floatValorTotal;
                    }
                    $strCargoAsignado = "";
                    if(is_array($arrayCargosDet) && $strPrefijoEmpresa == "TN")
                    {
                        //Se obtiene los datos del vendedor para saber si es de la región R1 o R2 y con ello se mostrará el cargo asignado
                        $arrayDatosVendedor = $em->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                 ->getInfoDatosPersona(array('strRol'                     => 'Empleado',
                                                                             'strPrefijo'                 => $strPrefijoEmpresa,
                                                                             'strEstadoPersona'           => array('Activo',
                                                                                                                   'Pendiente',
                                                                                                                   'Modificado'),
                                                                             'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                             'strLogin'                   => $strLoginVendedor));
                        if(empty($arrayDatosVendedor) || !is_array($arrayDatosVendedor) ||
                            (isset($arrayDatosVendedor['status']) && $arrayDatosVendedor['status'] === 'fail') ||
                            ($arrayDatosVendedor['status'] === 'ok' && empty($arrayDatosVendedor['result'])))
                        {
                            throw new \Exception('Error al obtener los datos del vendedor asignado, por favor comunicar a Sistemas.');
                        }
                        foreach($arrayCargosDet as $objCargosItem)
                        {
                            if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                               floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                            {
                                $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                if((!empty($strCargoAsignado) && $strCargoAsignado == "Gerente Ventas") || 
                                   (!empty($strCargoAsignado) && $strCargoAsignado == "Subgerente" && 
                                    in_array($strLoginVendedor,$arrayLoginVendedoresKam)))
                                {
                                    $strCargoAsignado = (!empty($arrayDatosVendedor['result'][0]['region'])) ? 
                                                        "Gerente Comercial ".$arrayDatosVendedor['result'][0]['region']:
                                                        "Gerente Comercial";
                                }
                                $strCargoAsignado = (!empty($strCargoAsignado) && $strCargoAsignado == "Subgerente" && $strTipoNegocioPto == "ISP") ?
                                                    "Aprobador ISP" : $strCargoAsignado;
                            }
                        }
                    }
                    if($objItem->getMotivoId()!= null && $objItem->getMotivoId() > 0)
                    {
                        $entityMotivo    = $em->getRepository('schemaBundle:AdmiMotivo')->find($objItem->getMotivoId());
                        $strNombreMotivo = (is_object($entityMotivo) && !empty($entityMotivo)) ? $entityMotivo->getNombreMotivo() : '';
                    }
                    $strProductoPlan ='';
                    $strCapacidadUno  = '';
                    $strCapacidadDos  = '';
                    if($objIdServicio->getProductoId())
                    {
                        $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1',
                                                                      "estado"                    => "Activo"));

                        if( is_object($objAdmiCaracteristica) && !empty($objAdmiCaracteristica) )
                        {
                            $objProdCarac1 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objIdServicio->getProductoId(),
                                                                  "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                  "estado"           => "Activo"));
                            if( is_object($objProdCarac1) && !empty($objProdCarac1) )
                            {
                                $objServProdCarac1 = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findOneBy(array("servicioId"                => $objIdServicio->getId(),
                                                                          "productoCaracterisiticaId" => $objProdCarac1->getId(),
                                                                          "estado"                    => "Activo"));
                                if( is_object($objServProdCarac1) && !empty($objServProdCarac1) )
                                {
                                    $strCapacidadUno = $objServProdCarac1->getValor();
                                }
                                
                            }
                        }
                        $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2',
                                                                      "estado"                    => "Activo"));
                        if( is_object($objAdmiCaracteristica) && !empty($objAdmiCaracteristica) )
                        {
                            $objProdCarac2 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objIdServicio->getProductoId(),
                                                                  "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                  "estado"           => "Activo"));

                            if( is_object($objProdCarac2) && !empty($objProdCarac2) )
                            {
                                $objServProdCarac2 = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findOneBy(array("servicioId"                => $objIdServicio->getId(),
                                                                          "productoCaracterisiticaId" => $objProdCarac2->getId(),
                                                                          "estado"                    => "Activo"));
                                if( is_object($objServProdCarac2) && !empty($objServProdCarac2) )
                                {
                                    $strCapacidadDos = $objServProdCarac2->getValor();
                                }
                            }
                        }

                        $entityProducto  = $em->getRepository('schemaBundle:AdmiProducto')
                                              ->find($objIdServicio->getProductoId()->getId());
                        $strProductoPlan = (is_object($entityProducto) && !empty($entityProducto)) ? $entityProducto->getDescripcionProducto():'';
                    }
                    elseif($objIdServicio->getPlanId())
                    {
                        $entityProducto = $em->getRepository('schemaBundle:InfoPlanCab')->find($objIdServicio->getPlanId()->getId());
                        $strProductoPlan = (is_object($entityProducto) && !empty($entityProducto)) ? $entityProducto->getNombrePlan():'';
                    }

                    $boolVelocidad = true;
                    if($strProductoPlan == 'Internet Dedicado' || $strProductoPlan == 'L3MPLS')
                    {
                        $boolVelocidad=false;
                    }

                    if($objPunto->getPersonaEmpresaRolId() && $objPunto->getPersonaEmpresaRolId()->getPersonaId())
                    {
                        if($objPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                        {
                            $strCliente = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                            $strEstado  = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getEstado();
                        }
                        else
                        {
                            $strCliente = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres() . " " .
                                          $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                            $strEstado  = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getEstado();
                        }
                    }
                    if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" && !in_array($strTipoPersonal,$arrayTipoPersonal)
                       && $strTipoPersonalAux != "Otros" && $boolVerTodo == "NO")
                    {
                        //Se valida el porcentaje de descuento de la solicitud y cargo, para poder ser presentada la solicitud en el grid.
                        if((floatval($floatPorcentajeDescuento) >= floatval($floatDescPorAprobarIni) && 
                            floatval($floatPorcentajeDescuento) <=  floatval($floatDescPorAprobarFin)) ||
                           (($strTipoPersonal == "GERENTE_VENTAS" || $strTipoPersonalAux == "GERENTE_VENTAS") &&
                            (in_array($strLoginVendedor,$arrayLoginVendedoresKam) || $strUsrCreacion == $strLoginVendedor) &&
                            ($floatPorcentajeDescuento <=  floatval($floatDescPorAprobarFin))))
                        {
                            $arraySolicitudes[] = array('id'               => $objItem->getId(),
                                                        'servicio'         => $strProductoPlan,
                                                        'cliente'          => $strCliente,
                                                        'estadoClt'        => $strEstado,
                                                        'asesor'           => ucwords(strtolower($strNombresCompletos)),
                                                        'login'            => $objPunto->getLogin(),
                                                        'motivo'           => $strNombreMotivo,
                                                        'vOriginal'        => '$'.$floatValorTotal,
                                                        'descuento'        => '$'.$floatPrecioDescuento,
                                                        'vFinal'           => '$'.$floatValorFinal,
                                                        'observacion'      => $objItem->getObservacion(),
                                                        'feCreacion'       => strval(date_format($objItem->getFeCreacion(), "d/m/Y G:i")),
                                                        'usrCreacion'      => $objItem->getUsrCreacion(),
                                                        'arrayCargos'      => $arrayCargosAux ? $arrayCargosAux : '',
                                                        'strCargoActual'   => $strCargoActual,
                                                        'intCantCargos'    => $intCantCargos+1,
                                                        'estadoSolicitud'  => $objItem->getEstado(),
                                                        'boolAprobar'      => $boolAprobar,
                                                        'linkVer'          => $linkVer,
                                                        'boolVelocidad'    => $boolVelocidad,
                                                        'strVelocidadUp'   => $strCapacidadUno,
                                                        'strVelocidadDown' => $strCapacidadDos,
                                                        'floatPorcentaje'  => round(floatval($floatPorcentajeDescuento),2).'%',
                                                        'strCargoAsignado' => $strCargoAsignado);
                        }
                    }
                    else
                    {
                        $arraySolicitudes[] = array('id'               => $objItem->getId(),
                                                    'servicio'         => $strProductoPlan,
                                                    'cliente'          => $strCliente,
                                                    'estadoClt'        => $strEstado,
                                                    'asesor'           => ucwords(strtolower($strNombresCompletos)),
                                                    'login'            => $objPunto->getLogin(),
                                                    'motivo'           => $strNombreMotivo,
                                                    'vOriginal'        => '$'.$floatValorTotal,
                                                    'descuento'        => '$'.$floatPrecioDescuento,
                                                    'vFinal'           => '$'.$floatValorFinal,
                                                    'observacion'      => $objItem->getObservacion(),
                                                    'feCreacion'       => strval(date_format($objItem->getFeCreacion(), "d/m/Y G:i")),
                                                    'usrCreacion'      => $objItem->getUsrCreacion(),
                                                    'arrayCargos'      => $arrayCargosAux ? $arrayCargosAux : '',
                                                    'strCargoActual'   => $strCargoActual,
                                                    'intCantCargos'    => $intCantCargos+1,
                                                    'estadoSolicitud'  => $objItem->getEstado(),
                                                    'boolAprobar'      => $boolAprobar,
                                                    'linkVer'          => $linkVer,
                                                    'boolVelocidad'    => $boolVelocidad,
                                                    'strVelocidadUp'   => $strCapacidadUno,
                                                    'strVelocidadDown' => $strCapacidadDos,
                                                    'floatPorcentaje'  => round(floatval($floatPorcentajeDescuento),2).'%',
                                                    'strCargoAsignado' => $strCargoAsignado);
                    }
                }
            }
            else
            {
                throw new \Exception('No existen solicitudes con las descripciones enviadas por parámetros.');
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                            'SolicitudDescuentoController->gridAprobarDescuentoAction', 
                                            $ex->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
            
        }
        if(!empty($arraySolicitudes) && is_array($arraySolicitudes))
        {
            if( empty($strMensajeCargo) )
            {
                $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                              "solicitudes"     => $arraySolicitudes,
                                                              "draw"            => $strDraw,
                                                              "recordsTotal"    => $intTotal,
                                                              "recordsFiltered" => $intTotal)));
            }else
            {
                $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                              "solicitudes"     => $arraySolicitudes,
                                                              "mensajeCargo"    => $strMensajeCargo,
                                                              "draw"            => $strDraw,
                                                              "recordsTotal"    => $intTotal,
                                                              "recordsFiltered" => $intTotal)));
            }
        }
        else
        {
            if( empty($strMensajeCargo) )
            {
                $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                              "solicitudes"     => [],
                                                              "draw"            => $strDraw,
                                                              "recordsTotal"    => $intTotal,
                                                              "recordsFiltered" => $intTotal)));
            }else
            {
                $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                              "solicitudes"     => [],
                                                              "mensajeCargo"    => $strMensajeCargo,
                                                              "draw"            => $strDraw,
                                                              "recordsTotal"    => $intTotal,
                                                              "recordsFiltered" => $intTotal)));
            }
        }
            
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * calculaDescAdultoMayorAction
     * 
     * Método encargado de Calcular el descuento por Beneficio 3era Edad / Adulto Mayor  
     * Se realizará el cálculo de la solicitud :     
     *                     1)- Si el valor del plan >= FORMULA_PLAN_BASICO   (SALARIO_BASICO_UNIFICADO*PORCENTAJE_VALOR_RESIDENCIAL_BASICO/100)
     *                        se colocará que se realizará un descuento por el valor calculado de la formula :
     *                        FORMULA_DESC_ADULTO_MAYOR:  
     *                                  ((SALARIO_BASICO_UNIFICADO*PORCENTAJE_VALOR_RESIDENCIAL_BASICO/100)*PORCENTAJE_DESC_ADULTO_MAYOR/100)
     *                        se deberá precargar y no será editable el valor del descuento.
     *                     2)- Si el plan < FORMULA_PLAN_BASICO parametrizada se colocará el valor del plan por el PORCENTAJE_DESC_ADULTO_MAYOR
     *                         se deberá precargar y no será editable el porcentaje de descuento parametrizado.                                 
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20/01/2021
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 12-08-2021 - Se modifica código en la función para dividir en dos flujos de proceso para motivos de tercera edad.
     *                         - Los flujos para los motivos parametrizados son:
     *                           1) Beneficio 3era Edad / Adulto Mayor -> PROCESO_3ERA_EDAD_ADULTO_MAYOR.
     *                              - Se mantiene programación de las validaciones sin modificación.
     *                           2) 3era Edad Resolución 07-2021       -> PROCESO_3ERA_EDAD_RESOLUCION_072021.
     *                              - Los cálculos de descuento se hará con el último plan básico activo existente.
     *                              - Para servicio con plan básico se calcula por porcentaje y para servicio con plan comercial
     *                                se calcula por valor.
     *
     * @return JsonResponse
     */
    public function calculaDescAdultoMayorAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();        
        $intIdServicio           = $objRequest->get('intIdServicio');       
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $strUsrCreacion          = $objSession->get('user');
        $strIpCreacion           = $objRequest->getClientIp();        
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');    
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $serviceUtil             = $this->get('schema.Util');           
        $strStatus               = 'OK';   
        $strValorDescuento       = 0;
        $strFlujoAdultoMayor     = $objRequest->get('strFlujoAdultoMayor');
        $strTipoCategoriaPlan    = "";
        $strMsjErrorMotResolucion= "";
        try
        {           
            //Parámetros para motivo de flujo PROCESO_3ERA_EDAD_ADULTO_MAYOR
            //Parametro para definir si el calculo del descuento sera por detalles de Productos => S
            $arrayParametroCalculoPorProducto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                                   'COMERCIAL', 
                                                                   '',
                                                                   'CALCULO_POR_PRODUCTO',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   $strCodEmpresa);
            
            $strParametroCalculoPorProducto = (isset($arrayParametroCalculoPorProducto["valor1"])
                                             && !empty($arrayParametroCalculoPorProducto["valor1"])) 
                                                ? $arrayParametroCalculoPorProducto["valor1"] : 'S';
            
            //Codigo del producto de Internet
            $arrayParametroCodProducto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL', 
                                                            '',
                                                            'CODIGO_PRODUCTO',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $strCodEmpresa); 
            
            $strParametroCodProducto   = (isset($arrayParametroCodProducto["valor1"])
                                          && !empty($arrayParametroCodProducto["valor1"])) ? $arrayParametroCodProducto["valor1"] : 'INTD';
        
            //Porcentaje de descuento por Adulto Mayor
            $arrayParamPorcDescAdultMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                               'COMERCIAL', 
                                                               '',
                                                               'PORCENTAJE_DESC_ADULTO_MAYOR',
                                                               '',
                                                               '',
                                                               '',
                                                               '',
                                                               '',
                                                               $strCodEmpresa); 
            
            $fltParamPorcDescAdultMayor  = (isset($arrayParamPorcDescAdultMayor["valor1"])
                                          && !empty($arrayParamPorcDescAdultMayor["valor1"])) ? $arrayParamPorcDescAdultMayor["valor1"] : 0;
            
            //Porcentaje por Valor Residencial
            $arrayParamPorcValorResidencial = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL', 
                                                            '',
                                                            'PORCENTAJE_VALOR_RESIDENCIAL_BASICO',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $strCodEmpresa); 
            
            $fltParamPorcValorResidencial  = (isset($arrayParamPorcValorResidencial["valor1"])
                                          && !empty($arrayParamPorcValorResidencial["valor1"])) ? $arrayParamPorcValorResidencial["valor1"] : 0;
            
            //Salario Basico Unificado
            $arrayParamSalarioBasico = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                          'COMERCIAL', 
                                                          '',
                                                          'SALARIO_BASICO_UNIFICADO',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          $strCodEmpresa); 
            
            //Parámetros para motivo de flujo PROCESO_3ERA_EDAD_RESOLUCION_072021
            $arrayMsjTipoCategPlan = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                          'COMERCIAL','','','MENSAJE_VALIDACION_TIPO_CATEGORIA_PLAN','',
                                                          '','','',$strCodEmpresa);
                
            $arrayMsjNoExistePlan = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                      'COMERCIAL','','','MENSAJE_VALIDACION_NOEXISTE_TIPO_CATEGORIA_PLAN','',
                                                      '','','',$strCodEmpresa);

            $arrayPorcMotivoResolucion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL','','PORCENTAJE_DESC_RESOLUCION_072021_ADULTO_MAYOR',
                                                            '','','','','',$strCodEmpresa);

            $arrayAplicaDescPlanComerc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL','','APLICA_DESC_TIPO_PLAN_COMERCIAL',
                                                            '','','','','',$strCodEmpresa);
            
            $arrayParamCategPlanBasico = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL','','CATEGORIA_PLAN_ADULTO_MAYOR',
                                                            '','','PLAN_BASICO','','',$strCodEmpresa);
                
            //Obtengo Servicio
            $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            
            if(!is_object($objInfoServicio))
            {
                throw $this->createNotFoundException('No se encontro el servicio para el calculo del descuento' . $intIdServicio);
            }     
            
            if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_ADULTO_MAYOR') 
            { 
                $fltParamSalarioBasico  = (isset($arrayParamSalarioBasico["valor1"])
                                          && !empty($arrayParamSalarioBasico["valor1"])) ? $arrayParamSalarioBasico["valor1"] : 0;

                if($fltParamPorcDescAdultMayor == 0 || $fltParamPorcValorResidencial == 0 || $fltParamSalarioBasico == 0)
                {                  
                    throw new \Exception('No existe parametrizado valores para la Formula del Calulo del Descuento'
                                       . ' por motivo Beneficio 3era Edad / Adulto Mayor');
                }
  
                //Se valida si el producto corresponde a un plan de internet dedicado INTD
                $intExisteServicio = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                 ->getServicioPlanCodProducto(array('intIdServicio'      => $intIdServicio,
                                                                                    'strCodigoProducto'  => $strParametroCodProducto ));            
                if($intExisteServicio===0)
                {
                    throw new \Exception('No corresponde a un servicio '.$strParametroCodProducto.', no es posible calcular el descuento');
                }
                //Se obtiene la sumatoria de los precios de los detalles de Productos de un Plan correspondiente a un servicio en base al
                //parametro 'APLICA_PRODUCTO_DESCUENTO_ADULTO_MAYOR' =>'S'
                if($strParametroCalculoPorProducto == 'S')
                {
                    $fltSumPrecioItem =  $emComercial->getRepository('schemaBundle:InfoServicio')
                                                     ->getSumPrecioItemServPlan(array('intIdServicio' => $intIdServicio));
                }
                else
                {
                    $fltSumPrecioItem = $objInfoServicio->getPrecioVenta();
                }
                //Calculo del Valor de descuento a otorgarse por Beneficio 3era Edad / Adulto Mayor
                $arrayParamCalculoDescuentoAdultoMayor= array('fltParamPorcValorResidencial' => $fltParamPorcValorResidencial,
                                                              'fltParamSalarioBasico'        => $fltParamSalarioBasico ,
                                                              'fltParamPorcDescAdultMayor'   => $fltParamPorcDescAdultMayor,
                                                              'fltSumPrecioItem'             => $fltSumPrecioItem);

                $fltValorDescuentoAdultoMayor = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                            ->getValorDescuentoAdultoMayor($arrayParamCalculoDescuentoAdultoMayor);

                $fltValorDescuentoAdultoMayor = (!empty($fltValorDescuentoAdultoMayor) ? round($fltValorDescuentoAdultoMayor, 2) : 0 );  

                if($fltValorDescuentoAdultoMayor == 0)
                {
                    throw new \Exception('No es posible calcular el descuento');
                }
            }
            else if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
            {
                $strMsjTipoCategPlan     = (isset($arrayMsjTipoCategPlan["valor2"])
                                            && !empty($arrayMsjTipoCategPlan["valor2"])) ? $arrayMsjTipoCategPlan["valor2"]
                                            : 'Beneficio Adulto Mayor No aplica a Planes Comerciales '; 
                
                $strMsjNoExistePlan      = (isset($arrayMsjNoExistePlan["valor2"])
                                            && !empty($arrayMsjMotivoResolucion["valor2"])) ? $arrayMsjNoExistePlan["valor2"]
                                            : 'No existe Plan Básico. Imposible otorgar beneficio ';
                
                $fltPorcMotivoResolucion = (isset($arrayPorcMotivoResolucion["valor1"])
                                            && !empty($arrayPorcMotivoResolucion["valor1"])) ? $arrayPorcMotivoResolucion["valor1"] : 0;
                
                if($fltPorcMotivoResolucion == 0 )
                { 
                    $strMsjErrorMotResolucion = "No es posible calcular el descuento";
                    throw new \Exception('No existe parametrizado valor porcentaje del cálculo del descuento para flujo '.$strFlujoAdultoMayor);
                }
               
                //Se verifica y obtiene el precio del tipo de categoría del plan basico
                $intValorPlanBasico = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->getPrecioPlanBasico(array('strDescripcionCaract' => "TIPO_CATEGORIA_PLAN_ADULTO_MAYOR",
                                                                               'strValorCaract'       => 'BASICO',
                                                                               'strCodEmpresa'        => $strCodEmpresa));
               
                if($intValorPlanBasico == 0)
                {
                    $strMsjErrorMotResolucion = $strMsjNoExistePlan; 
                    throw new \Exception('No existe un plan básico activo o valor del plan básico es cero.' );
                }

                //Se obtiene el tipo de categoría del plan ligado al servicio
                $arrayTipoCategoriaPlan = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getTipoCategoriaPlan(array('strDescripcionCaract' => "TIPO_CATEGORIA_PLAN_ADULTO_MAYOR",
                                                                                     'intIdServicio'        => $intIdServicio));
                
                if (!$arrayTipoCategoriaPlan && !isset($arrayTipoCategoriaPlan['strValor']))
                {
                    $strMsjErrorMotResolucion = "No existe tipo categoría en el plan. No es posible calcular el descuento ";
                    throw new \Exception('No existe la característica de tipo de categoría adulto mayor en el plan.' );
                }
                
                $strTipoCategoriaPlan = $arrayTipoCategoriaPlan['strValor'];
                
                //Se valida si el producto corresponde a un plan de internet dedicado INTD
                $intExisteServicio = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                 ->getServicioPlanCodProducto(array('intIdServicio'      => $intIdServicio,
                                                                                    'strCodigoProducto'  => $strParametroCodProducto ));            
                if($intExisteServicio===0)
                {
                    $strMsjErrorMotResolucion = "No es posible calcular el descuento";
                    throw new \Exception('No corresponde a un servicio '.$strParametroCodProducto.', no es posible calcular el descuento');
                }
                
                //Se valida con el tipo de categoría del plan para realizar los cálculos por porcentaje o por valor.
                if($strTipoCategoriaPlan == $arrayParamCategPlanBasico['valor1'])
                {
                    $fltValorDescuentoAdultoMayor = $fltPorcMotivoResolucion; 
                }
                else 
                {
                    if($arrayAplicaDescPlanComerc["valor1"] == 'S') 
                    {
                        $fltValorDescuentoAdultoMayor = $intValorPlanBasico * ($fltPorcMotivoResolucion/100); 
                    }
                    else
                    {
                        $strMsjErrorMotResolucion = $strMsjTipoCategPlan;
                        throw new \Exception('No aplica a planes comerciales el beneficio. Parámetro APLICA_DESC_TIPO_PLAN_COMERCIAL: '
                            . $arrayAplicaDescPlanComerc["valor1"] ); 
                    }
                } 
            }
            else
            {
                throw new \Exception('No existe flujo para el motivo seleccionado.');
            } 
            
            $strStatus         = 'OK';   
            $strMensaje        = '';            
        }
        catch(\Exception $e)
        {
            if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
            {
                $strStatus                    = "ERROR";
                $strMensaje                   = $strMsjErrorMotResolucion;
                $fltValorDescuentoAdultoMayor = 0;
            }
            else
            { 
              $strStatus                    = "ERROR";
              $strMensaje                   = "No es posible calcular el descuento";
              $fltValorDescuentoAdultoMayor = 0;  
            }
            
            $serviceUtil->insertError('Telcos+',
                                      'SolicitudDescuentoController->calculaDescAdultoMayorAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $arrayRespuesta = array("strStatus"                    => $strStatus,
                                "strMensaje"                   => $strMensaje,
                                "fltValorDescuentoAdultoMayor" => $fltValorDescuentoAdultoMayor,
                                "strTipoCategoriaPlan"         => $strTipoCategoriaPlan,
                                "strParamCategPlanBasico"       => $arrayParamCategPlanBasico['valor1']);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }

    /**
     * validaSolDescAction
     * 
     * Método encargado de validar si es posible aprobar solicitud de descuento, se valida la existencia de Solicitud de descuento Fijo por motivo
     * Discapacidad en estado Pendiente, Aprobado, Finalizado para servicio de Internet en estado Activo o In-Corte.     
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05/09/2020
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 22/01/2021 - Se modifica nombre de método de 'validaSolDescDiscapacidadAction' a 'validaSolDescAction'.
     *                         - Se reestructura código para incluir validaciones por los motivos de Beneficio 3era Edad / Adulto Mayor 
     *                           ó Cliente con Discapacidad. Se agrega la llamada de los parámetros a validar por 'PARAM_FLUJO_ADULTO_MAYOR'.
     *                           Se realiza validaciones de doble benefecio cuando se encuentre en los motivos mencionados.
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.2 13/08/2021 - Se modifica código que obtenía valor del motivo de beneficio adulto mayor parametrizado, para que mediante el
     *                           mismocon el nombre del motivo en proceso se retorne el flujo de proceso de adulto mayor tercera edad 
     *                           y se realiza las validaciones correspondientes para el flujo tercera edad. En el caso de ser otro motivo se  
     *                           devuelve vacío el valor del parámetro y se valida para la no afectación de los procesos del mismo.
     * 
     * @return JsonResponse
     */
    public function validaSolDescAction()
    {
        $objJsonResponse         = new JsonResponse();
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();        
        $strParametro            = $objRequest->get('param');
        $arrayIdsSolicitudes     = explode("|", $strParametro);
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $strUsrCreacion          = $objSession->get('user');
        $strIpCreacion           = $objRequest->getClientIp();        
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');    
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $serviceUtil             = $this->get('schema.Util');    
        $strIdsDetSolicitud      = '';
        $strParamIdsDetSolicitud = '';
        $strStatus               = 'Solicitudes Cumplen';
        $strMensajeValidacion    = '';
        $intIndice               = 0;

        try
        {
            $arrayParametroValidacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                           'COMERCIAL', 
                                                           '',
                                                           'VALIDACION_SOLICITUD_DISCAPACIDAD_POR_CLIENTE',
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           $strCodEmpresa);                
            $strParametroValidacion   = (isset($arrayParametroValidacion["valor1"])
                                        && !empty($arrayParametroValidacion["valor1"])) ? $arrayParametroValidacion["valor1"] : 'S';
                
            $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                         'COMERCIAL', 
                                                         '',
                                                         '',
                                                         'NOTIFICACION_APROBACION',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         $strCodEmpresa);
        
            $arrayMotivoDiscapacidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_SOLICITUD_DESC_DISCAPACIDAD',
                                                         'COMERCIAL','','MOTIVO_DESC_DISCAPACIDAD','',
                                                         '','','','', $strCodEmpresa);
            
            $arrayNotificacionAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','','NOTIFICACION_APROBACION','',
                                                     '','','',$strCodEmpresa);
            
            $arrayValorDobleBeneficio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','DOBLE_BENEFICIO','',
                                                     '','','','',$strCodEmpresa);
            
            $arrayValorMaxCliente = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','CANT_MAX_POR_CLIENTE','',
                                                     '','','','',$strCodEmpresa);
        
            $arrayValorMaxPunto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','CANT_MAX_POR_PUNTO','',
                                                     '','','','',$strCodEmpresa);
            
            $arrayCantExistBeneficio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','CANT_EXISTE_BENEFICIO','',
                                                     '','','','',$strCodEmpresa); 
            
            //Se obtiene el parámetro para ser enviado en el array si el valor de doble beneficio es "N".
            $arrayCantMaxCliente = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                     'COMERCIAL','','CANT_MAX_CLIENTE','',
                                                     '','','','',$strCodEmpresa);
            
            $strMensajeValidacionAprob  = (isset($arrayNotificacionAdultoMayor["valor2"])
                                        && !empty($arrayNotificacionAdultoMayor["valor2"])) ? $arrayNotificacionAdultoMayor["valor2"]
                                        : 'Solicitud Imposible de Aprobar srtCliente ya posee beneficio';               
            
            
            foreach($arrayIdsSolicitudes as $intId)
            {
                $strMensajeValidacion ='';
                $intIndice ++;
                $objDetalleSol = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intId);
                if(!is_object($objDetalleSol))
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada' . $intId);
                }              
                $objServicio           = $objDetalleSol->getServicioId();
                $objPunto              = $objServicio->getPuntoId();
                $objPersonaEmpresaRol  = $objPunto->getPersonaEmpresaRolId();
                $objPersona            = $objPersonaEmpresaRol->getPersonaId();
                
                $strCliente = sprintf('%s', $objPersona);
                $strLogin = $objPunto->getLogin();
                
                $objMotivoSolicitud = $objDetalleSol->getMotivoId(); 
                $objMotivo          = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($objMotivoSolicitud);
                $strNombreMotivo    = $objMotivo->getNombreMotivo();
                
                //Con el nombre de motivo obtengo el flujo parametrizado a procesar
                $arrayFlujoAdultoMayor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                            'COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                            $strNombreMotivo, '', '', '', '', $strCodEmpresa);
                
                $strFlujoAdultoMayor = !empty($arrayFlujoAdultoMayor["valor6"]) ? $arrayFlujoAdultoMayor["valor6"] : ""; 
                
                if($strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' || $strFlujoAdultoMayor == 'PROCESO_3ERA_EDAD_RESOLUCION_072021'
                    || $strNombreMotivo == $arrayMotivoDiscapacidad["valor1"])
                {
                    $arrayValidaSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->validaSolDobleBeneficio(array('intIdPunto'            => $objPunto->getId(),
                                                                                'intIdPersonaRol'       => $objPersonaEmpresaRol->getId(), 
                                                                                'intCantMaxPorCliente'  => $arrayValorMaxCliente["valor2"],     
                                                                                'intCantMaxPorPunto'    => $arrayValorMaxPunto["valor2"],       
                                                                                'intCantExistBeneficio' => $arrayCantExistBeneficio["valor2"],  
                                                                                'intCantMaxCliente'     => $arrayCantMaxCliente["valor2"],      
                                                                                'strDobleBeneficio'     => $arrayValorDobleBeneficio["valor1"],
                                                                                'intMotivoId'           => $objDetalleSol->getMotivoId()
                                                                               ));
                     
                    $arrayRegistros   = $arrayValidaSolicitud['objRegistros'];
                    $strBanderaAplica = $arrayValidaSolicitud['strBanderaAplica'];
                    
                    if($strBanderaAplica == 'S')
                    {
                        $strIdsDetSolicitud = $strIdsDetSolicitud . $intId;
                        if ($intIndice < count($arrayIdsSolicitudes))
                        {
                            $strIdsDetSolicitud = $strIdsDetSolicitud . '|';           
                        }
                    }
                    else
                    {
                        $strMensajeValidacion = str_replace('srtCliente', $strCliente, $strMensajeValidacionAprob);
                        $strRegistrosConcat  .= $strMensajeValidacion.$intId.": "."<br>";
                        $strStatus            = 'Solicitudes No Cumplen';

                        foreach ($arrayRegistros as $arrayData)
                        {   
                            $strRegistrosConcat .= "Login: ".$arrayData['login']."&nbsp;&nbsp;".
                                                   "Motivo: ".$arrayData['nombreMotivo']."&nbsp;&nbsp;".
                                                   "Estado: ".$arrayData['estado']."&nbsp;&nbsp;"; 
                            $strRegistrosConcat .= "<br>"; 
                        } 
                        $strRegistrosConcat .= "<br>"; 
                        
                    }
                }
                else
                {                                                    
                    //Valida si existe en el punto una solicitud de '3era Edad / Adulto Mayor' ó 'Cliente con Discapacidad'.
                    $arrayValidaSolOtrosMotivos = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                              ->getSolicitudesPorPunto(array('intIdPunto' => $objPunto->getId()));
                    $arraySolOtrosMotivos       = $arrayValidaSolOtrosMotivos['objRegistros'];
                    $intCantidad                = $arrayValidaSolOtrosMotivos['intCantidad'];
                    
                    if($intCantidad > 0)
                    { 
                        $strMensajeValidacion = str_replace('srtCliente', $strCliente, $strMensajeValidacionAprob);
                        $strRegistrosConcat  .= $strMensajeValidacion.$intId.": "."<br>";
                        $strStatus            = 'Solicitudes No Cumplen';
                       
                        foreach ($arraySolOtrosMotivos as $arrayData)
                        {   
                            $strRegistrosConcat .= "Login: ".$arrayData['login']."&nbsp;&nbsp;".
                                                   "Motivo: ".$arrayData['nombreMotivo']."&nbsp;&nbsp;".
                                                   "Estado: ".$arrayData['estado']."&nbsp;&nbsp;"; 
                            $strRegistrosConcat .= "<br>"; 
                        } 
                        $strRegistrosConcat .= "<br>"; 
                       
                    }
                    else
                    {
                        $strIdsDetSolicitud = $strIdsDetSolicitud . $intId;
                        if ($intIndice < count($arrayIdsSolicitudes))
                        {
                            $strIdsDetSolicitud = $strIdsDetSolicitud . '|';           
                        }
                    }
                }    
            }
            $strMensaje              = $strRegistrosConcat;
            $strParamIdsDetSolicitud = $strIdsDetSolicitud;
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Ha ocurrido un error. Por favor Notificar a Sistemas!";
            $serviceUtil->insertError('Telcos+',
                                      'SolicitudDescuentoController->validaSolDescAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $arrayRespuesta = array("strStatus"               => $strStatus,
                                "strMensaje"              => $strMensaje,
                                "strParamIdsDetSolicitud" => $strParamIdsDetSolicitud);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }

    /**    
     * Documentación para el método 'aprobarDescuentoAjaxAction'.
     *
     * Descripcion: Permite aprobar una o más solicitudes de descuento.
     * 
     * version 1.0 Versión Inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 16-09-2016 - Se envía correo cuando se aprueba un descuento utilizando la respectiva plantilla 
     *                           y adjuntando un archivo con la información de la solicitudes 
     * 
     * @author Modificado: Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 04-05-2017 - Se modifica seteo de campo valor descuento según la caracteristica asociada a la solicitud, se realiza seteo
     *                           del campo descuento unitario en la tabla InfoServicio.
     *
     * @author Modificado: Edgar Holguin <eholguin@telconet.ec>
     * @version 1.3 29-05-2017 - Se realiza corrección en la aprobación de solicitudes, se incluye consulta de característica dentro
     *                           de validación de característica asociada a la solicitud de descuento.
     *   
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4 27-07-2017 - Se modifican los number_format por round. Para que no genere errores al ingresar valores mayores a 999 en la base.
     *                           Se agrega la lógica para almacenar el historial del servicio cuando se aprueba una solicitud.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 21-02-2018 - Se agrega validación para permitir aprobar la solicitud de descuento solo cuando el valor de descuento es menor al
     *                           precio de venta del servicio.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.6 25-01-2021 - Se agrega try catch dentro del ciclo for para controlar error en caso de excepción y proceda con
     *                           el siguiente registro.
     *
     * @author : Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.7 15-06-2021 - Se agrega validación que verifica el cargo de la persona auxiliar que aprobará la solicitud.
     *
     * @author : Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.8 10-12-2021 - Se agrega validación que verifica si la solicitud requiere flujo de aprobación.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.9 17-08-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 2.0 08-05-2023 Verifica si el precio de descuento en una solicitud de descuento está actualizado
     *                         y coincide con el precio de descuento calculado en base al porcentaje de descuento
     *                         y valor del servicio. Si no coincide, recalcula el precio descuento, ademas se añade
     *                         el motivo del recalculo en el historial de la solicitud.
     *                         Se realiza esto con el fin de evitar inconsistencias en los precios de descuento después
     *                         de un cambio de plan o una aprobación tardía de la solicitud de descuento.
     *
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 2.0 28/04/2023 - se envia un correo con un adendum de autorizacion de descuento cuando se aprueba el descuento.
     * 
     * @return Response $objResponse
     * 
     */
    public function aprobarDescuentoAjaxAction()
    {
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();
        $strCodEmpresa            = $objSession->get('idEmpresa');
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $strUsrCreacion           = $objSession->get('user') ? $objSession->get('user') : '';
        $strEmpleado              = $objSession->get('empleado');
        $strIpClient              = $objRequest->getClientIp();
        $objResponse              = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent("error del Form");
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        /* @var $serviceServicioHistorial \telconet\comercialBundle\Service\InfoServicioHistorialService */
        $serviceServicioHistorial = $this->get('comercial.InfoServicioHistorial');
        //Obtiene parametros enviados desde el ajax
        $strParametro             = $objRequest->get('param');
        $arrayIdsSolicitudes      = explode("|", $strParametro);
        $serviceUtil              = $this->get('schema.Util');
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $serviceEnvioPlantilla    = $this->get('soporte.EnvioPlantilla');
        $strCuerpoCorreo          = "El presente correo es para indicarle que se aprobó una solicitud en TelcoS+ con los siguientes datos:";
        $arrayData = array();
        $emComercial->getConnection()->beginTransaction();
        try
        {
            foreach($arrayIdsSolicitudes as $intId)
            {   
                try
                {
                    $strObservacion = 'Solicitud de descuento autorizada: Descuento ';
                    $strDescuento   = '';
                    $strMotivo      = '';
                    $strProductoPlan= '';
                    $strCliente     = '';
                    $strLogin       = '';
                    $objDetalleSol  = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intId);
                    if(!$objDetalleSol)
                    {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada' . $intId);
                    }  
                               
                    if($objDetalleSol->getPorcentajeDescuento() && $strPrefijoEmpresa == 'MD')
                    {
                        $objServicio = $objDetalleSol->getServicioId();
                        $intPorcentajeDsc = $objDetalleSol->getPorcentajeDescuento();
                        $floatValorDsctoActual = $objDetalleSol->getPrecioDescuento();

                        $intCantidadServicio = $objServicio->getCantidad();
                        $floatPrecioVenta = $objServicio->getPrecioVenta();

                        $floatValorDctoRecalculo = ($intCantidadServicio * $floatPrecioVenta) * ($intPorcentajeDsc / 100);
                        $floatValorDctoRecalculo = round($floatValorDctoRecalculo, 2);

                        if($floatValorDsctoActual != $floatValorDctoRecalculo)
                        {
                            $strObsRecalculo = 'Se ha identificado una inconsistencia en el precio con descuento, ' .
                                                'la cual se debe a una discrepancia entre el precio del servicio ' .
                                                'y el porcentaje de descuento actualmente aplicado en la solicitud de descuento. '.   
                                                'Por lo tanto, se procederá a recalcular el precio con el descuento correspondiente. <br>' .
                                                '<b>Porcentaje:</b> '. $intPorcentajeDsc . '%<br>' .
                                                '<b>Precio actual del servicio:</b> '. $floatPrecioVenta . '<br>' .
                                                '<b>Cantidad:</b> ' . $intCantidadServicio . '<br>' . 
                                                '<b>Precio descuento actual:</b> '. $floatValorDsctoActual . '<br>' .
                                                '<b>Nuevo precio descuento:</b> '. $floatValorDctoRecalculo .
                            
                            $objDetalleSol->setPrecioDescuento($floatValorDctoRecalculo);
                            $emComercial->persist($objDetalleSol);
                    
                            $objDetalleSolHist = new InfoDetalleSolHist();
                            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSol);
                            $objDetalleSolHist->setObservacion($strObsRecalculo);
                            $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                            $objDetalleSolHist->setEstado('Pendiente');
                            $objDetalleSolHist->setMotivoId($objDetalleSol->getMotivoId());
                            
                            $emComercial->persist($objDetalleSolHist);
                            $emComercial->flush();
                        }
                    }


                    //CAMBIA PRECIO AL SERVICIO
                    /*Se obtiene la información necesaria para el archivo que se enviará como adjunto del correo*/
                    $objServicio = $objDetalleSol->getServicioId();
                    if(is_object($objServicio))
                    {
                        $floatPrecioVenta = $objServicio->getPrecioVenta();
                        $floatValorDcto   = $objDetalleSol->getPrecioDescuento();
                        if( $floatPrecioVenta > $floatValorDcto)
                        {

                            $objDetalleSol->setEstado('Aprobado');
                            $emComercial->persist($objDetalleSol);
                            $emComercial->flush();

                            //Grabamos en la tabla de historial de la solicitud
                            $objDetalleSolHistorial = new InfoDetalleSolHist();
                            $objDetalleSolHistorial->setEstado('Aprobado');
                            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
                            $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolHistorial->setIpCreacion($strIpClient);
                            $emComercial->persist($objDetalleSolHistorial);
                            $emComercial->flush();

                            $objInfoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                   ->findOneBy(array("detalleSolicitudId" => $intId));

                            $objProducto= $objServicio->getProductoId();
                            $objPlan    = $objServicio->getPlanId();
                            $objPunto   = $objServicio->getPuntoId();
                            if($objProducto)
                            {
                                $strProductoPlan= $objProducto->getDescripcionProducto();
                            }
                            elseif($objPlan)
                            {
                                $strProductoPlan= $objPlan->getNombrePlan();
                            }

                            if($objDetalleSol->getPrecioDescuento() && is_null($objDetalleSol->getPorcentajeDescuento()))
                            {
                                $strDescuento   = (float)($objDetalleSol->getPrecioDescuento());

                                // Si la solicitud posee características asociadas
                                if(is_object($objInfoDetalleSolCaract))
                                {
                                    $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                         ->find($objInfoDetalleSolCaract->getCaracteristicaId());  

                                    $objInfoDetalleSolCaract->setEstado('Aprobado');
                                    $emComercial->persist($objInfoDetalleSolCaract);
                                    $emComercial->flush();

                                    if(is_object($objAdmiCaracteristica))
                                    {
                                        $floatValorDcto = (float)$strDescuento; 

                                        if('DESCUENTO UNITARIO FACT' === $objAdmiCaracteristica->getDescripcionCaracteristica())
                                        {
                                            $floatValorDescuento = ($floatValorDcto * $objServicio->getCantidad());
                                            $objServicio->setValorDescuento($floatValorDescuento);
                                            $objServicio->setDescuentoUnitario($floatValorDcto);
                                            $strObservacion     .= 'Unitario ';
                                        }
                                        else if('DESCUENTO TOTALIZADO FACT' === $objAdmiCaracteristica->getDescripcionCaracteristica())
                                        {
                                            $objServicio->setValorDescuento($floatValorDcto);
                                            $strObservacion .= 'Totalizado ';

                                            if("MD" === $strPrefijoEmpresa)
                                            {
                                                $objServicio->setDescuentoUnitario(round( $floatValorDcto  , 2));
                                            }
                                            else
                                            {
                                                $floatResultadoDctoUnitario = $floatValorDcto / $objServicio->getCantidad();

                                                $objServicio->setDescuentoUnitario(round( $floatResultadoDctoUnitario  , 2));
                                            }
                                        }

                                    }

                                }
                                else
                                {
                                    if("MD" === $strPrefijoEmpresa)
                                    {
                                        $objServicio->setValorDescuento($strDescuento);
                                    }
                                    else
                                    {
                                        $floatValorDcto = ($strDescuento * $objServicio->getCantidad());
                                        $objServicio->setValorDescuento(round( $floatValorDcto  ,  2));
                                    }

                                    $objServicio->setDescuentoUnitario(round( $strDescuento  , 2));
                                }

                            }

                            elseif($objDetalleSol->getPorcentajeDescuento())
                            {
                                if($objDetalleSol->getPrecioDescuento())
                                {
                                    $floatValorDcto     = $objDetalleSol->getPrecioDescuento();
                                    $floatValorDctoUni  = $floatValorDcto;

                                    if("TN" === $strPrefijoEmpresa)
                                    {
                                        $floatValorDctoUni  = $floatValorDcto / $objServicio->getCantidad();
                                    }

                                    $objServicio->setValorDescuento(round( $floatValorDcto  , 2));
                                    $objServicio->setDescuentoUnitario(round( $floatValorDctoUni  , 2));
                                }
                                else
                                {
                                    $floatResultadoDctoUnitario = 0;
                                    $floatResultadoValorDcto    = 0;
                                    $floatPrecioVenta = $objServicio->getPrecioVenta();
                                    $floatPorcentajeDcto = $objDetalleSol->getPorcentajeDescuento();

                                    if($floatPorcentajeDcto > 0 && $floatPrecioVenta > 0)
                                    {

                                        $floatResultadoValorDcto     = ($floatPrecioVenta * $objServicio->getCantidad())*($floatPorcentajeDcto/100);

                                        $floatResultadoDctoUnitario  = $floatResultadoValorDcto;

                                        if("TN" === $strPrefijoEmpresa)
                                        {
                                            $floatResultadoDctoUnitario  =  $floatResultadoDctoUnitario / $objServicio->getCantidad();
                                        }
                                        $objServicio->setValorDescuento(round( $floatResultadoValorDcto, 2));

                                        $objServicio->setDescuentoUnitario(round( $floatResultadoDctoUnitario  , 2)); 

                                        $strDescuento = $floatResultadoValorDcto;
                                    }
                                }
                            }

                            if($objPunto)
                            {
                                $objPersonaEmpresaRol   = $objPunto->getPersonaEmpresaRolId();
                                if($objPersonaEmpresaRol)
                                {
                                    $objPersona     = $objPersonaEmpresaRol->getPersonaId();
                                    if($objPersona)
                                    {
                                        $strCliente = sprintf('%s', $objPersona);
                                    }
                                }
                                $strLogin   = $objPunto->getLogin();
                            }

                            $emComercial->persist($objServicio);
                            $emComercial->flush();

                            //obtener parametros empresa para regularizacion cambio de plan.
                            $boolRegAutDescuento = false;
                            $objParametroAutDescCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                array('nombreParametro' => 'REGULARIZACION_CAMBIO_DE_PLAN',
                                    'estado'          => 'Activo'));
                            if(is_object($objParametroAutDescCab))
                            {
                                $objParametroAutDescDet     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                    array("parametroId" => $objParametroAutDescCab->getId(),
                                        "valor1"      => "EMPRESA_AUTO_DESCUENTO",
                                        "valor2"      => $strPrefijoEmpresa,
                                        "estado"      => "Activo"));
                                if(is_object($objParametroAutDescDet) && $objParametroAutDescDet->getValor3() == "SI" )
                                {
                                    $boolRegAutDescuento = true;
                                }
                            }

                            if($boolRegAutDescuento)
                            {
                                error_log("Ejecutando flujo de regularizacion autorizacion de descuento.");
                                $strMotivoX = '';
                                $objMotivo      = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                                if($objMotivo)
                                {
                                    $strMotivoX  = $objMotivo->getNombreMotivo();
                                }

                                $strObservacionTarea = 'Se le realiza la autorización de descuento al ' 
                                                . ($objProducto? ' producto:' .$objProducto->getDescripcionProducto() : ' plan:' 
                                                . $objPlan->getNombrePlan())                                                
                                                . ',  por el '
                                                . ($objDetalleSol->getPorcentajeDescuento() ? 
                                                'porcentaje  ' . $objDetalleSol->getPorcentajeDescuento() . '%.' :
                                                'valor  $' . $objDetalleSol->getPrecioDescuento(). 'USD. ')
                                                .($strMotivoX? '<br>Motivo: '. $strMotivoX:'');

                                $arrayParametrosTarea                              = array();
                                $arrayParametrosTarea['intIdEmpresa']              = $strCodEmpresa;
                                $arrayParametrosTarea['strPrefijoEmpresa']         = $strPrefijoEmpresa;
                                $arrayParametrosTarea['strNombreTarea']            = "AUTORIZACION DE DESCUENTO";
                                $arrayParametrosTarea['strNombreProceso']          = "PROCESOS TAREAS ATC";
                                $arrayParametrosTarea['strUserCreacion']           = $strUsrCreacion;
                                $arrayParametrosTarea['strIpCreacion']             = $strIpClient;
                                $arrayParametrosTarea['intFormaContacto']          = 5;
                                $arrayParametrosTarea['strMotivoTarea']            = $strObservacionTarea;
                                $arrayParametrosTarea['intPuntoId']                = $objServicio->getPuntoId()->getId(); 

                                $arrayParametrosTarea['intIdPersonaEmpresaRol']    = $objSession->get('idPersonaEmpresaRol')?
                                                                                    $objSession->get('idPersonaEmpresaRol'):0;
                                $arrayParametrosTarea['strObsSeguimiento']         = $strObservacionTarea;
                                $arrayParametrosTarea['strObservacionTarea']       = $strObservacionTarea;
                                $arrayParametrosTarea['boolAsignarTarea']          = true;
                                $arrayParametrosTarea['strTipoTarea']              = 'T';
                                $arrayParametrosTarea['strTareaRapida']            = 'S';
                                $arrayParametrosTarea['strUsuarioAsigna']          = $strUsrCreacion;
                                $arrayParametrosTarea['strTipoAsignacion']         = 'empleado';

                                $serviceSoporteService = $this->get("soporte.SoporteService");
                                $arrayRespuestaTareaSoporte = $serviceSoporteService->crearTareaCasoSoporte($arrayParametrosTarea);
                                
                                if($arrayRespuestaTareaSoporte["mensaje"] != "ok" )
                                {
                                    $serviceUtil->insertError(  'Telcos+', 
                                                'serviceSoporteService->crearTareaCasoSoporte', 
                                                'La tarea de soporte no ha sido creada', 
                                                $strUsrCreacion, 
                                                $strIpClient
                                            );
                                }
                                ////////
                                $serviceTokenCas = $this->get('seguridad.TokenCas');
                                $arrayTokenCas = $serviceTokenCas->generarTokenCas();
                                if(empty($arrayTokenCas['strToken']))
                                {
                                    throw new \Exception($arrayTokenCas['strMensaje']);
                                }
                                
                                $arrayParametrosCorreo   = array();
                                $arrayParametrosCorreo['token'] = $arrayTokenCas['strToken'];
                                $arrayParametrosCorreo['idServicio'] = $objServicio->getId();
                                $arrayParametrosCorreo['descuentoTotal'] = ($objDetalleSol->getPorcentajeDescuento() ? 
                                ($objServicio->getPrecioVenta() * $objDetalleSol->getPorcentajeDescuento()/100) :
                                $objDetalleSol->getPrecioDescuento());

                                $serviceGeneral       = $this->get('tecnico.InfoServicioTecnico');
                                
                                try 
                                {
                                    error_log("Ejecutando autorizacion de descuento....");
                                    $objSalida = $serviceGeneral->envioCorreoAutDesc($arrayParametrosCorreo);
                                } catch (\Exception $e) 
                                {
                                    $strMensajeError = "Ha ocurrido un error con el microservicio resumen de compra. ".
                                    "Error en envioCorreoAutDesc. ".$e->getMessage();
                                    error_log($strMensajeError);
                                    $serviceUtil->insertError('Telcos+', 
                                              'SolicitudDescuentoController.aprobarDescuentoAjaxAction', 
                                              $strMensajeError,
                                              $strUsrCreacion, 
                                              $strIpClient
                                             );
                                }
                                
                                
                                //SE ALMACENA LA INFORMACION EN EL HISTORIAL DEL SERVICIO.
                                $strObservacion.= ($objDetalleSol->getPorcentajeDescuento() ? 
                                        'del ' . $objDetalleSol->getPorcentajeDescuento() 
                                        . '%. Descuento totalizadoA de $' 
                                        . ($objServicio->getPrecioVenta() * $objDetalleSol->getPorcentajeDescuento()/100):
                                        'de $' . $objDetalleSol->getPrecioDescuento()
                                        . '. Descuento totalizadoB de $' .$objDetalleSol->getPrecioDescuento());
                            }
                            else 
                            {
                                error_log("Ejecutado flujo normal de autorizacion de descuento.");
                                //SE ALMACENA LA INFORMACION EN EL HISTORIAL DEL SERVICIO.
                                $strObservacion                   .= ($objDetalleSol->getPorcentajeDescuento() ? 
                                'del ' . $objDetalleSol->getPorcentajeDescuento() . '%' :
                                'de $' . $objDetalleSol->getPrecioDescuento());
                            }
                            
                            $arrayParametros['objServicio']    = $objServicio;
                            $arrayParametros['strIpClient']    = $strIpClient;
                            $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
                            $arrayParametros['strObservacion'] = $strObservacion;
                            $arrayParametros['strAccion']      = 'autorizarDescuento';
                            $objServicioHistorial              = $serviceServicioHistorial->crearHistorialServicio($arrayParametros);
                            $emComercial->persist($objServicioHistorial);
                            $emComercial->flush();
                            if($strPrefijoEmpresa == 'TN')
                            {
                                $arrayDestinatarios       = array();
                                $strVendedor              = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                                $objPersona               = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                                $strCliente               = "";
                                $strIdentificacion        = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                                $strCliente               = (is_object($objPersona) && $objPersona->getRazonSocial())?$objPersona->getRazonSocial():
                                                            $objPersona->getNombres() . " " .$objPersona->getApellidos();
                                $floatPrecioVenta         = $objServicio->getPrecioVenta() ? $objServicio->getPrecioVenta():0;
                                $intCantidad              = $objServicio->getCantidad() ? $objServicio->getCantidad():0;
                                $floatPrecioDescuento     = $objDetalleSol->getPrecioDescuento() ? $objDetalleSol->getPrecioDescuento() : 0;
                                $floatPorcentajeDescuento = $objDetalleSol->getPorcentajeDescuento() ? $objDetalleSol->getPorcentajeDescuento() : 0;
                                $floatValorTotal          = 0;
                                if((!empty($floatPrecioVenta) && $floatPrecioVenta > 0) && (!empty($intCantidad) && $intCantidad > 0))
                                {
                                    $floatValorTotal = $floatPrecioVenta * $intCantidad;
                                }
                                $floatValorFinal = $floatValorTotal - $floatPrecioDescuento;
                                if((empty($floatPorcentajeDescuento)&&$floatPorcentajeDescuento==0)&&
                                   (!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                                {
                                    $floatPorcentajeDescuento = ($floatPrecioDescuento * 100)/$floatValorTotal;
                                }
                                $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                          ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                            'modulo'          => self::COMERCIAL,
                                                                            'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                            'estado'          => 'Activo'));
                                if(!is_object($objCargosCab) || empty($objCargosCab))
                                {
                                    throw new \Exception('No se encontraron datos con los parámetros enviados.');
                                }
                                $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                                           'valor4'      => 'ES_JEFE',
                                                                           'valor7'      => 'SI',
                                                                           'estado'      => 'Activo'));
                                $strCargoAsignado = "";
                                if(is_array($arrayCargosDet))
                                {
                                    foreach($arrayCargosDet as $objCargosItem)
                                    {
                                        if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                                           floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                                        {
                                            $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                        }
                                    }
                                }
                                //Correo del vendedor.
                                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                                "Correo Electronico");
                                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                {
                                    foreach($arrayCorreos as $arrayItem)
                                    {
                                        if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                        {
                                            $arrayDestinatarios[] = $arrayItem['valor'];
                                        }
                                    }
                                }
                                //Correo del subgerente
                                $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                       ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                                if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                                {
                                    $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                                    $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                         "Correo Electronico");
                                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                    {
                                        foreach($arrayCorreos as $arrayItem)
                                        {
                                            if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                            {
                                                $arrayDestinatarios[] = $arrayItem['valor'];
                                            }
                                        }
                                    }
                                }
                                //Correo de la persona quien crea la solicitud.
                                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($objDetalleSol->getUsrCreacion(),
                                                                                                         "Correo Electronico");
                                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                {
                                    foreach($arrayCorreos as $arrayItem)
                                    {
                                        if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                        {
                                            $arrayDestinatarios[] = $arrayItem['valor'];
                                        }
                                    }
                                }
                                $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                              "strIdentificacionCliente" => $strIdentificacion,
                                                              "strObservacion"           => $objDetalleSol->getObservacion(),
                                                              "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                              "strCargoAsignado"         => $strCargoAsignado);
                                $serviceEnvioPlantilla->generarEnvioPlantilla("APROBACIÓN DE SOLICITUD DE DESCUENTO",
                                                                              array_unique($arrayDestinatarios),
                                                                              "NOTIFICACION",
                                                                              $arrayParametrosMail,
                                                                              $strPrefijoEmpresa,
                                                                              "",
                                                                              "",
                                                                              null,
                                                                              true,
                                                                              "notificaciones_telcos@telconet.ec");
                            }

                        }
                        else // Se rechaza solicitud debido a que el descuento no debe ser mayor al precio de venta del servicio.
                        {
                            $strObservacion = 'Valor de descuento excede a precio de venta del servicio.';

                            $objDetalleSol->setEstado('Rechazado');
                            $objDetalleSol->setUsrRechazo('telcos');
                            $objDetalleSol->setFeRechazo(new \DateTime('now'));
                            $emComercial->persist($objDetalleSol);
                            $emComercial->flush();

                            $objAdmiMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                                         ->findOneBy(array('nombreMotivo' => 'Valor Incorrecto'));
                            if(is_object($objAdmiMotivo))
                            {
                                //Grabamos en la tabla de historial de la solicitud
                                $objDetalleSolHistorial = new InfoDetalleSolHist();
                                $objDetalleSolHistorial->setEstado('Rechazado');
                                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
                                $objDetalleSolHistorial->setUsrCreacion('telcos');
                                $objDetalleSolHistorial->setObservacion($strObservacion);
                                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                                $objDetalleSolHistorial->setIpCreacion($strIpClient);
                                $objDetalleSolHistorial->setMotivoId($objAdmiMotivo->getId());
                                $emComercial->persist($objDetalleSolHistorial);
                                $emComercial->flush();
                            }

                            $arrayParametros['objServicio']    = $objServicio;
                            $arrayParametros['strIpClient']    = $strIpClient;
                            $arrayParametros['strObservacion'] = $strObservacion;
                            $arrayParametros['strUsrCreacion'] = 'telcos';
                            $arrayParametros['strAccion']      = 'rechazarDescuento';
                            $objServicioHistorial              = $serviceServicioHistorial->crearHistorialServicio($arrayParametros);
                            $emComercial->persist($objServicioHistorial);
                            $emComercial->flush();                        

                        }
                    }
                    else
                    {
                        throw $this->createNotFoundException('No se encontro el servicio asociado a la solicitud buscada');
                    }

                    if($objDetalleSol->getMotivoId())
                    {
                        $objMotivo      = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                        if($objMotivo)
                        {
                            $strMotivo  = $objMotivo->getNombreMotivo();
                        }
                    }

                    $arrayData[] = array(
                                            "cliente"               => $strCliente,
                                            "login"                 => $strLogin,
                                            "servicio"              => $strProductoPlan,
                                            "motivo"                => $strMotivo,
                                            "descuento"             => $strDescuento,
                                            "observacionSolicitud"  => $objDetalleSol->getObservacion(),
                                            "fechaCreacion"         => strval(date_format($objDetalleSol->getFeCreacion(), "d/m/Y G:i")),
                                            "usuarioCreacion"       => $objDetalleSol->getUsrCreacion()
                                    );
                
                }
                catch(\Exception $e)
                {
                    $serviceUtil->insertError(  'Telcos+', 
                                                'SolicitudDescuentoController->aprobarDescuentoAjax', 
                                                $e->getMessage(), 
                                                $strUsrCreacion, 
                                                $strIpClient
                    );

                }
                
            }
            
            /* Envío Correo Solicitudes Descuento Aprobadas
             * Para los diferentes tipos de solicitudes que serán aprobadas por Gerencia, se utilizará la misma plantilla de rechazo,
             * con la diferencia de que dependiendo del tipo de solicitud que se desea aprobar, se adjuntará un PDF con la información de las 
             * distintas solicitudes que fueron aprobadas. Es por esta razón que se enviarán como parámetros los nombres de las cabeceras 
             * de las columnas de la tabla con el contenido de las solicitudes.
             * Además se envían los parámetros necesarios para el contenido del correo que se enviará utilizando plantillas.
             */
            $arrayNombresCabeceraAdjunto = array(   "Cliente",
                                                    "Login",
                                                    "Servicio",
                                                    "Motivo",
                                                    "Descuento",
                                                    "Observación Solicitud",
                                                    "Fecha Creación",
                                                    "Usuario Creación");
            $arrayParametrosMail = array(
                                            "idEmpresaSession"              => $strCodEmpresa,
                                            "prefijoEmpresaSession"         => $strPrefijoEmpresa,
                                            "codigoPlantilla"               => "APROB_AUTORIZAC",
                                            "usrCreacion"                   => $strUsrCreacion,
                                            "ipClient"                      => $strIpClient,
                                            "empleadoSession"               => $strEmpleado,
                                            "tituloAdjunto"                 => "APROBACIÓN DE SOLICITUDES DE DESCUENTO",
                                            "tipoAutorizacion"              => "AUTORIZACIÓN DE DESCUENTO",
                                            "tipoGestion"                   => "APROBACIÓN",
                                            "nombreTipoAutorizacionAdjunto" => "Aprobacion_Autorizacion_Descuento",
                                            "arrayNombresCabeceraAdjunto"   => $arrayNombresCabeceraAdjunto,
                                            "arrayDataAdjunto"              => $arrayData,
                                            "asunto"                        => "Gestion en Solicitudes de Descuento"
            );
            /* @var $serviceAutorizaciones \telconet\comercialBundle\Service\Autorizaciones */
            $serviceAutorizaciones = $this->get('comercial.Autorizaciones');
            $serviceAutorizaciones->envioMailAutorizaciones($arrayParametrosMail);

            $emComercial->getConnection()->commit();
            $objResponse->setContent("Se aprobaron las solicitudes con exito.");
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();
            $serviceUtil->insertError(  'Telcos+', 
                                        'SolicitudDescuentoController->aprobarDescuentoAjax', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpClient
            );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }
        return $objResponse;
    }

    /**    
     * Documentación para el método 'rechazarSolicitudDescuentoAjaxAction'.
     *
     * Descripcion: Permite rechazar una o más solicitudes de descuento.
     * 
     * version 1.0 Versión Inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-09-2016 - Se envía correo cuando se rechaza un descuento utilizando la respectiva plantilla 
     *                           y adjuntando un archivo con la información de la solicitudes
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 06-01-2020 - Se agrega lógica para guardar historial del servicio.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 17-08-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     *
     * @return Response $objResponse
     * 
     */
    public function rechazarSolicitudDescuentoAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $strEmpleado            = $objSession->get('empleado');
        $strIpClient            = $objRequest->getClientIp();
        $objResponse            = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent("error del Form");
        $emComercial            = $this->getDoctrine()->getManager('telconet');

        $strParametro           = $objRequest->get('param');
        $arrayIdsSolicitudes    = explode("|", $strParametro);
        $intIdMotivo            = $objRequest->get('motivoId');
        $serviceUtil = $this->get('schema.Util');
        $arrayData              = array();
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $emComercial->getConnection()->beginTransaction();
        try
        {
            foreach($arrayIdsSolicitudes as $intId)
            {
                $strCliente             = "";
                $strProductoPlan        = "";
                $strDescuento           = "";
                $strLogin               = "";
                $objDetalleSol          = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intId);
                if(!$objDetalleSol)
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $strNombreSolicitud = $objDetalleSol->getTipoSolicitudId()->getDescripcionSolicitud();
                $objDetalleSol->setEstado('Rechazado');
                $objDetalleSol->setUsrRechazo($strUsrCreacion);
                $objDetalleSol->setFeRechazo(new \DateTime('now'));
                $emComercial->persist($objDetalleSol);
                $emComercial->flush();

                //Grabamos en la tabla de historial de la solicitud
                $objDetalleSolHistorial = new InfoDetalleSolHist();
                $objDetalleSolHistorial->setEstado('Rechazado');
                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
                $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorial->setIpCreacion($strIpClient);
                $objDetalleSolHistorial->setMotivoId($intIdMotivo);
                $emComercial->persist($objDetalleSolHistorial);
                $emComercial->flush();

                /*Se obtiene la información necesaria para el archivo que se enviará como adjunto del correo*/
                $objServicio        = $objDetalleSol->getServicioId();
                if($objServicio)
                {
                    $objProducto    = $objServicio->getProductoId();
                    $objPlan        = $objServicio->getPlanId();
                    $objPunto       = $objServicio->getPuntoId();
                    if($objProducto)
                    {
                        $strProductoPlan    = $objProducto->getDescripcionProducto();
                    }
                    elseif($objPlan)
                    {
                        $strProductoPlan    = $objPlan->getNombrePlan();
                    }

                    if($objDetalleSol->getPrecioDescuento())
                    {
                        $strDescuento       = $objDetalleSol->getPrecioDescuento();
                    }
                    elseif($objDetalleSol->getPorcentajeDescuento())
                    {
                        $strDescuento       = $objDetalleSol->getPorcentajeDescuento() . '%';
                    }

                    if($objPunto)
                    {
                        $objPersonaEmpresaRol= $objPunto->getPersonaEmpresaRolId();
                        if($objPersonaEmpresaRol)
                        {
                            $objPersona     = $objPersonaEmpresaRol->getPersonaId();
                            if($objPersona)
                            {
                                $strCliente = sprintf('%s', $objPersona);
                            }
                        }
                        $strLogin           = $objPunto->getLogin();
                    }
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                    $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setIpCreacion($strIpClient);
                    $objInfoServicioHistorial->setServicioId($objServicio);
                    if(!empty($intIdMotivo) && $intIdMotivo > 0)
                    {
                        $objMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                        if(is_object($objMotivo) && !empty($objMotivo))
                        {
                            $objInfoServicioHistorial->setMotivoId($objMotivo->getId());
                        }
                    }
                    $objInfoServicioHistorial->setObservacion('Se rechazó : '.$strNombreSolicitud);
                    $emComercial->persist($objInfoServicioHistorial);
                    $emComercial->flush();
                    if($strPrefijoEmpresa == 'TN')
                    {
                        $arrayDestinatarios       = array();
                        $strVendedor              = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                        $objPersona               = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                        $strCliente               = "";
                        $strIdentificacion        = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                        $strCliente               = (is_object($objPersona) && $objPersona->getRazonSocial()) ?
                                                    $objPersona->getRazonSocial() : $objPersona->getNombres() . " " .$objPersona->getApellidos();
                        $floatPrecioVenta         = $objServicio->getPrecioVenta() ? $objServicio->getPrecioVenta():0;
                        $intCantidad              = $objServicio->getCantidad() ? $objServicio->getCantidad():0;
                        $floatPrecioDescuento     = $objDetalleSol->getPrecioDescuento() ? $objDetalleSol->getPrecioDescuento() : 0;
                        $floatPorcentajeDescuento = $objDetalleSol->getPorcentajeDescuento() ? $objDetalleSol->getPorcentajeDescuento() : 0;
                        $floatValorTotal          = 0;
                        if((!empty($floatPrecioVenta) && $floatPrecioVenta > 0) && (!empty($intCantidad) && $intCantidad > 0))
                        {
                            $floatValorTotal = $floatPrecioVenta * $intCantidad;
                        }
                        $floatValorFinal = $floatValorTotal - $floatPrecioDescuento;
                        if((empty($floatPorcentajeDescuento)&&$floatPorcentajeDescuento==0)&&(!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                        {
                            $floatPorcentajeDescuento = ($floatPrecioDescuento * 100)/$floatValorTotal;
                        }
                        $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                    'modulo'          => self::COMERCIAL,
                                                                    'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                    'estado'          => 'Activo'));
                        if(!is_object($objCargosCab) || empty($objCargosCab))
                        {
                            throw new \Exception('No se encontraron datos con los parámetros enviados.');
                        }
                        $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                                   'valor4'      => 'ES_JEFE',
                                                                   'valor7'      => 'SI',
                                                                   'estado'      => 'Activo'));
                        $strCargoAsignado = "";
                        if(is_array($arrayCargosDet))
                        {
                            foreach($arrayCargosDet as $objCargosItem)
                            {
                                if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                                   floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                                {
                                    $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                }
                            }
                        }
                        //Correo del vendedor.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                        "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        //Correo del subgerente
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                        if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                            $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                 "Correo Electronico");
                            if(!empty($arrayCorreos) && is_array($arrayCorreos))
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }
                        //Correo de la persona quien crea la solicitud.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objDetalleSol->getUsrCreacion(),
                                                                                                 "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        $strCuerpoCorreo = "El presente correo es para indicarle que se rechazó una solicitud en TelcoS+ con los siguientes datos:";
                        $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                      "strIdentificacionCliente" => $strIdentificacion,
                                                      "strObservacion"           => $objDetalleSol->getObservacion(),
                                                      "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                      "strCargoAsignado"         => $strCargoAsignado);
                        $serviceEnvioPlantilla->generarEnvioPlantilla("RECHAZO DE SOLICITUD DE DESCUENTO",
                                                                      array_unique($arrayDestinatarios),
                                                                      "NOTIFICACION",
                                                                      $arrayParametrosMail,
                                                                      $strPrefijoEmpresa,
                                                                      "",
                                                                      "",
                                                                      null,
                                                                      true,
                                                                      "notificaciones_telcos@telconet.ec");
                    }

                }
                else
                {
                    throw $this->createNotFoundException('No se encontro el servicio asociado a la solicitud buscada');
                }

                if($objDetalleSol->getMotivoId())
                {
                    $objMotivo      = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                    if($objMotivo)
                    {
                        $strMotivo  = $objMotivo->getNombreMotivo();
                    }
                }

                $arrayData[] = array(
                                        "cliente"               => $strCliente,
                                        "login"                 => $strLogin,
                                        "servicio"              => $strProductoPlan,
                                        "motivo"                => $strMotivo,
                                        "descuento"             => $strDescuento,
                                        "observacionSolicitud"  => $objDetalleSol->getObservacion(),
                                        "fechaCreacion"         => strval(date_format($objDetalleSol->getFeCreacion(), "d/m/Y G:i")),
                                        "usuarioCreacion"       => $objDetalleSol->getUsrCreacion()
                );
            }
            $strMotivoRechazo           = '';
            if($intIdMotivo)
            {
                $objMotivoRechazo       = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                if($objMotivoRechazo)
                {
                    $strMotivoRechazo   = $objMotivoRechazo->getNombreMotivo();
                }
            }
            
            
            /* Envío Correo Solicitudes Descuento Rechazadas
             * Para los diferentes tipos de solicitudes que serán rechazadas por Gerencia, se utilizará la misma plantilla de rechazo,
             * con la diferencia de que dependiendo del tipo de solicitud que se desea rechazar, se adjuntará un PDF con la información de las 
             * distintas solicitudes que fueron rechazadas. Es por esta razón que se enviarán como parámetros los nombres de las cabeceras 
             * de las columnas de la tabla con el contenido de las solicitudes.
             * Además se envían los parámetros necesarios para el contenido del correo que se enviará utilizando plantillas.
             */
            $arrayNombresCabeceraAdjunto = array(   "Cliente",
                                                    "Login",
                                                    "Servicio",
                                                    "Motivo",
                                                    "Descuento",
                                                    "Observación Solicitud",
                                                    "Fecha Creación",
                                                    "Usuario Creación");

            $arrayParametrosMail = array(
                                            "idEmpresaSession"              => $strCodEmpresa,
                                            "prefijoEmpresaSession"         => $strPrefijoEmpresa,
                                            "codigoPlantilla"               => "RECHZ_AUTORIZAC",
                                            "usrCreacion"                   => $strUsrCreacion,
                                            "ipClient"                      => $strIpClient,
                                            "empleadoSession"               => $strEmpleado,
                                            "tituloAdjunto"                 => "RECHAZO DE SOLICITUDES DE DESCUENTO",
                                            "tipoAutorizacion"              => "AUTORIZACIÓN DE DESCUENTO",
                                            "tipoGestion"                   => "RECHAZO",
                                            "nombreTipoAutorizacionAdjunto" => "Rechazo_Autorizacion_Descuento",
                                            "arrayNombresCabeceraAdjunto"   => $arrayNombresCabeceraAdjunto,
                                            "arrayDataAdjunto"              => $arrayData,
                                            "asunto"                        => "Gestion en Solicitudes de Descuento",
                                            "motivoGestion"                 => $strMotivoRechazo
            );
            /* @var $serviceAutorizaciones \telconet\comercialBundle\Service\Autorizaciones */
            $serviceAutorizaciones = $this->get('comercial.Autorizaciones');
            $serviceAutorizaciones->envioMailAutorizaciones($arrayParametrosMail);

            $emComercial->getConnection()->commit();
            $objResponse->setContent("Se rechazaron las solicitudes de descuento con exito.");
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();
            $serviceUtil->insertError(  'Telcos+', 
                                        'SolicitudCambioDocumentoController->rechazarSolicitudDescuentoAjaxAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpClient
            );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }
        return $objResponse;
    }

    public function getMotivosRechazoDescuento_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobaciondescuento','AutorizacionDescuento','rechazarSolicitudDescuentoAjax');
		$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
     * Documentación para la función 'getTipoAutorizacionAjaxAction'.
     *
     * Función que devuelve los tipos de autorizaciones que se mostrarán en el Combo de Gestión.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-12-2018
     *
     * @return Response objeto JSON con la lista de tipos de autorizaciones.
     *
     */
    public function getTipoAutorizacionAjaxAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $intCodEmpresa     = $objSession->get('idEmpresa');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $strUsrCreacion    = $objSession->get('user');
        $strIpClient       = $objRequest->getClientIp();
        $serviceUtil       = $this->get('schema.Util');
        try
        {
            $arrayTipoAutorizacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->get(self::TIPOS_AUTORIZACIONES,
                                                     self::COMERCIAL,
                                                     self::AUTORIZACION_DESCUENTOS,
                                                     '',
                                                     '',
                                                     '',
                                                     $strPrefijoEmpresa,
                                                     'TIPO',
                                                     '',
                                                     $intCodEmpresa);
            if(empty($arrayTipoAutorizacion) || !is_array($arrayTipoAutorizacion))
            {
                $arrayResultado[] = array('descripcion' => 'No existen datos',
                                          'empresa'     => 'No existen datos');
                throw new \Exception('No se encontraron datos con los parámetros enviados.');
            }
            sort($arrayTipoAutorizacion);
            foreach($arrayTipoAutorizacion as $arrayItem)
            {
                $arrayResultado[] = array('descripcion'    => $arrayItem["valor1"],
                                          'descripTecnica' => $arrayItem["valor2"],
                                          'empresa'        => $arrayItem["valor3"]);
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudDescuentoController->getTipoAutorizacionAjaxAction', 
                                      $ex->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient);
        }
        $objResponse = new Response(json_encode(array('tipoAutorizacion' => $arrayResultado)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'getEstadoAjaxAction'.
     *
     * Función que devuelve los estados de autorizaciones.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 28-10-2022
     *
     * @return Response objeto JSON.
     *
     */
    public function getEstadoAjaxAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intCodEmpresa     = $objSession->get('idEmpresa');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $strUsrCreacion    = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpClient       = $objRequest->getClientIp();
        $serviceUtil       = $this->get('schema.Util');
        $strEstadoActivo   = "Activo";
        try
        {
            if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
            {
                $arrayEstados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->get(self::TIPOS_AUTORIZACIONES,
                                                self::COMERCIAL,
                                                self::AUTORIZACION_DESCUENTOS,
                                                '',
                                                '',
                                                '',
                                                '',
                                                'ESTADO',
                                                '',
                                                $intCodEmpresa);
                if(empty($arrayEstados) || !is_array($arrayEstados))
                {
                    $arrayResultado[] = array('descripcion' => 'No existen datos');
                    throw new \Exception('No se encontraron datos con los parámetros enviados.');
                }
                sort($arrayEstados);
                foreach($arrayEstados as $arrayItem)
                {
                    $arrayResultado[] = array('descripcion' => $arrayItem["valor1"]);
                }
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudDescuentoController->getEstadoAjaxAction', 
                                      $ex->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient);
        }
        $objResponse = new Response(json_encode(array('estados' => $arrayResultado)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * Documentación para la función 'flujoMotivoAdultoMayor'.
     *
     * Función que devuelve el flujo de proceso de adulto mayor parametrizado mediante el nombre motivo enviado
     * por parámetro sólo para tercera edad.
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 12-08-2021
     *
     * @return objResponse.
     *
     */    
    public function flujoMotivoAdultoMayorAction()
    {
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $intCodEmpresa        = $objSession->get('idEmpresa');
        $strNombreMotivo      = $objRequest->get('strNombreMotivo');
        $emGeneral            = $this->get('doctrine')->getManager('telconet_general');
        $boolFlujoAdultoMayor = false;
        $strFlujoAdultoMayor  = "";
        $strMensaje           = "No se encontró flujo para el motivo";
        try
        {
            if(!empty($strNombreMotivo))
            {
                $arrayValorFlujoMotivo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                                'COMERCIAL','','MOTIVO_DESC_ADULTO_MAYOR',
                                                                $strNombreMotivo, '', '', '', '', $intCodEmpresa);

                if( isset($arrayValorFlujoMotivo["valor1"]) && !empty($arrayValorFlujoMotivo["valor1"]) 
                    && !empty($arrayValorFlujoMotivo["valor6"]) )
                {
                    $boolFlujoAdultoMayor = true;
                    $strFlujoAdultoMayor  = $arrayValorFlujoMotivo["valor6"];
                    $strMensaje           = "Flujo Ok";
                }
            }
            
            $arrayResponse = array('boolFlujoAdultoMayor' => $boolFlujoAdultoMayor,
                                   'strMensaje'           => $strMensaje,
                                   'strFlujoAdultoMayor'  => $strFlujoAdultoMayor);
        }
        catch(\Exception $e)
        {
            $boolFlujoAdultoMayor = false;
            $strMensaje           = 'Ocurrió un error al obtener el flujo adulto mayor en el parámetro. ';
            $arrayResponse        = array('boolFlujoAdultoMayor' => $boolFlujoAdultoMayor,
                                          'strMensaje'           => $strMensaje,
                                          'strFlujoAdultoMayor'  => $strFlujoAdultoMayor);
        }
        error_log("arrayResponse---->". json_encode($arrayResponse));
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

}
