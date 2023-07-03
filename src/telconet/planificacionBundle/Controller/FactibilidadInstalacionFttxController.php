<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\ReturnResponse;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FactibilidadInstalacionFttxController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * @Secure(roles="ROLE_403-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Metodo de direccionamiento principal a la pantalla de factibilidad para telconet light. 
     * Se realiza ajuste para realizar factibilidad manual para produtos especiales con ultima milla FTTX.
     * Se guarda en sesion la informacion referente a la Ultima Milla FTTX [intIdEmpresa, strPrefijoEmpresa, strUltimaMillaFttx]
     * Con la finalidad de usar la logica de Megadatos.
     * 
     * @return render direccionamiento a la pantalla de factibilidad de telconet light
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 22-11-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 11-02-2019 Se elimina seteo de variable de sesión FTTx que ya no está siendo utilizada.
     * 
     * @since 1.0
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        //MODULO 403 - PLANIFIFCACION/FACTIBILIDAD TN LIGHT
        if(true === $this->get('security.context')->isGranted('ROLE_403-95'))
        {
            $rolesPermitidos[] = 'ROLE_403-95'; // PREPLANIFICAR VIA AJAX
        }
        if(true === $this->get('security.context')->isGranted('ROLE_403-94'))
        {
            $rolesPermitidos[] = 'ROLE_403-94'; // RECHAZAR VIA AJAX
        }
        if(true === $this->get('security.context')->isGranted('ROLE_403-2717'))
        {
            $rolesPermitidos[] = 'ROLE_403-2717'; // EDITAR FACTIBILIDAD
        }
        
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("403", "1");
        
        return $this->render('planificacionBundle:Factibilidad:indexFactibilidadIsb.html.twig', array(
                            'item'            => $entityItemMenu,
                            'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_403-1")
     * 
     * Documentación para el método 'indexConsultarAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 22-11-2017
     * 
     * @return render direccionamiento a la pantalla solicitada
     *
     */
    public function indexConsultarAction()
    {
        $rolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_403-95'))
        {
            $rolesPermitidos[] = 'ROLE_403-95';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_403-94'))
        {
            $rolesPermitidos[] = 'ROLE_403-94';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_403-1'))
        {
            $rolesPermitidos[] = 'ROLE_403-1';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_403-2717'))
        {
            $rolesPermitidos[] = 'ROLE_403-2717'; // EDITAR FACTIBILIDAD
        }

        $em             = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("235", "1");


        return $this->render('planificacionBundle:Factibilidad:indexFactibilidadIsb.html.twig', array(
                             'item'            => $entityItemMenu,
                             'rolesPermitidos' => $rolesPermitidos
        ));
    }


    /**
    * ajaxGridAction
    * 
    * Llena el grid de consulta de factibilidad de Telconet Light.
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 22-11-2017
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 30-09-2019 - Se agrega el parametro: 'serviceTecnico' a la funcion generarJsonPreFactibilidad, para consultar el tipo de red
    *                           por servicio
    *
    * @Secure(roles="ROLE_403-7")
    */
    public function ajaxGridAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $strCodEmpresa          = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strPrefijoEmpresa      = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");

        $strFechaDesdePlanif    = explode('T', $objRequest->get('fechaDesdePlanif'));
        $strFechaHastaPlanif    = explode('T', $objRequest->get('fechaHastaPlanif'));

        $strLogin2              = $objRequest->get('login2');
        $strDescripcionPunto    = $objRequest->get('descripcionPunto');
        $strVendedor            = $objRequest->get('vendedor');
        $strCiudad              = $objRequest->get('ciudad');
        $intNumOrdenServicio    = $objRequest->get('numOrdenServicio');
        //migracion clientes transtelco - se agrega parametro ultima milla
        $strUltimaMilla         = $objRequest->get('ultimaMilla');
        $intStart               = $objRequest->get('start');
        $intLimit               = $objRequest->get('limit');
        $strLimite                 = $objRequest->get('limite');
        $strIdJurisdiccion         = $objRequest->get('id_jurisdiccion');

        $emComercial                     = $this->getDoctrine()->getManager("telconet"); 
        $arrayJurisdiccionesId = array();
        $arrayJurisdiccionesEmpresaId = array();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $objSession->get('idPersonaEmpresaRol');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
        $arrayJurisdiccionesEmpresa = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
        ->getResultadoJurisdiccionesPorEmpresaSinEstado($strEmpresaCod);
        foreach ($arrayJurisdiccionesEmpresa as $empresa ) 
        {
            $arrayJurisdiccionesEmpresaId[] = $empresa->getId();
        }
        foreach ($arrayInfoEmpresaRolCarac as $registro) 
        {
            if (in_array((int) $registro->getValor(), $arrayJurisdiccionesEmpresaId)) 
            {
                $arrayJurisdiccionesId[] = $registro->getValor();
            }
        }

        $strFiltrarJurisdiccion = $objRequest->get('strFiltrarJurisdiccion') ? $objRequest->get('strFiltrarJurisdiccion') : "";

        $emComercial            = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceTecnico         = $this->get('tecnico.InfoServicioTecnico');
        $intIdOficina           = 0;
        
        if($strPrefijoEmpresa=="TN")
        {
            $intIdPerSession        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
            if($intIdPerSession)
            {
                $objPerSession              = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($intIdPerSession);
                $arrayEstadosJurisdiccion   = array("Activo","Modificado");
                if(is_object($objPerSession))
                {
                    $objOficinaPerSession   = $objPerSession->getOficinaId();
                    if(is_object($objOficinaPerSession))
                    {
                        
                        $arrayParametros = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->get('FACTIBILIDAD_SECTOR_POR_OFICINA', 
                                                                                                            '', 
                                                                                                            '', 
                                                                                                            '', 
                                                                                                            $objOficinaPerSession->getNombreOficina(),
                                                                                                            '', 
                                                                                                            '', 
                                                                                                            '');
                        if(is_array($arrayParametros))
                        {             
                            foreach ($arrayParametros as $arrayParametro)
                            {
                                 $arraySectoresId[] = $arrayParametro['valor3'];
                            }
                        }
                        
                        if(count($arraySectoresId) == 0 )                        
                        {
                            $intIdOficinaPerSession         = $objOficinaPerSession->getId();
                            $arrayParamsOficinaJurisdiccion = array(
                                                                    "oficinaId" => $intIdOficinaPerSession,
                                                                    "estado"    => $arrayEstadosJurisdiccion
                                                                    );
                            $objJurisdiccionXIdOficinaPerSession    = $emComercial->getRepository("schemaBundle:AdmiJurisdiccion")
                                                                                  ->findOneBy($arrayParamsOficinaJurisdiccion);

                            if(is_object($objJurisdiccionXIdOficinaPerSession))
                            {
                                $intIdOficina       = $intIdOficinaPerSession;
                            }
                            else
                            {
                                /*
                                 * No existe jurisdicción con el id oficina de la persona en sesión, por lo que se consulta el id de la oficina referenciada
                                 * 
                                 */
                                $intIdRefOficina        = $objOficinaPerSession->getRefOficinaId();
                                if($intIdRefOficina)
                                {
                                    $arrayParamsOficinaRefJurisdiccion = array(
                                                                                "oficinaId" => $intIdRefOficina,
                                                                                "estado"    => $arrayEstadosJurisdiccion
                                                                              );

                                    $objJurisdiccionXIdRefOficinaPerSession    = $emComercial->getRepository("schemaBundle:AdmiJurisdiccion")
                                                                                             ->findOneBy($arrayParamsOficinaRefJurisdiccion);
                                    if(is_object($objJurisdiccionXIdRefOficinaPerSession))
                                    {
                                        $intIdOficina   = $intIdRefOficina;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $strFiltrarJurisdiccion = "NO";
        }
        
        $strFiltrarJurisdiccion  ="NO";
        
        //se agrage modificacion de parametros para realizar consulta de registros de prefactibilidad
        $arrayParametros                             = array();
        $arrayParametros["em"]                       = $emComercial;
        $arrayParametros["start"]                    = $intStart;
        $arrayParametros["limit"]                    = $intLimit;
        $arrayParametros["search_fechaDesdePlanif"]  = $strFechaDesdePlanif[0];
        $arrayParametros["search_fechaHastaPlanif"]  = $strFechaHastaPlanif[0];
        $arrayParametros["search_login2"]            = $strLogin2;
        $arrayParametros["search_descripcionPunto"]  = $strDescripcionPunto;
        $arrayParametros["search_vendedor"]          = $strVendedor;
        $arrayParametros["search_ciudad"]            = $strCiudad;
        $arrayParametros["search_numOrdenServicio"]  = $intNumOrdenServicio;
        $arrayParametros["codEmpresa"]               = $strCodEmpresa;
        $arrayParametros["ultimaMilla"]              = $strUltimaMilla;
        $arrayParametros["validaRechazado"]          = "";
        $arrayParametros["intIdOficina"]             = $intIdOficina;
        $arrayParametros["strFiltrarJurisdiccion"]   = $strFiltrarJurisdiccion;
        $arrayParametros["serviceTecnico"]           = $serviceTecnico;
        $arrayParametros["arrayJurisdiccionesId"]    = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]        = $strIdJurisdiccion;
        $arrayParametros["limite"] = $strLimite;

        if(count($arraySectoresId) > 0 )
        {
            $arrayParametros["arraySectoresId"]      = $arraySectoresId;
        }
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonPreFactibilidad($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    public function ajaxComboJurisdiccionesAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $strCodEmpresa          = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strPrefijoEmpresa      = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");

        $strFechaDesdePlanif    = explode('T', $objRequest->get('fechaDesdePlanif'));
        $strFechaHastaPlanif    = explode('T', $objRequest->get('fechaHastaPlanif'));

        $strLogin2              = $objRequest->get('login2');
        $strDescripcionPunto    = $objRequest->get('descripcionPunto');
        $strVendedor            = $objRequest->get('vendedor');
        $strCiudad              = $objRequest->get('ciudad');
        $intNumOrdenServicio    = $objRequest->get('numOrdenServicio');
        //migracion clientes transtelco - se agrega parametro ultima milla
        $strUltimaMilla         = $objRequest->get('ultimaMilla');
        $intStart               = $objRequest->get('start');
        $intLimit               = $objRequest->get('limit');
        $strLimite                 = $objRequest->get('limite');
        $strIdJurisdiccion         = $objRequest->get('id_jurisdiccion');
        $strJurisdiccion = $objRequest->get('query');

        $emComercial                     = $this->getDoctrine()->getManager("telconet"); 
        $arrayJurisdiccionesId = array();
        $arrayJurisdiccionesEmpresaId = array();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $objSession->get('idPersonaEmpresaRol');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
        $arrayJurisdiccionesEmpresa = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
        ->getResultadoJurisdiccionesPorEmpresaSinEstado($strEmpresaCod, $strJurisdiccion);
        foreach ($arrayJurisdiccionesEmpresa as $empresa ) 
        {
            $arrayJurisdiccionesEmpresaId[] = $empresa->getId();
        }
        foreach ($arrayInfoEmpresaRolCarac as $registro) 
        {
            if (in_array((int) $registro->getValor(), $arrayJurisdiccionesEmpresaId)) 
            {
                $arrayJurisdiccionesId[] = $registro->getValor();
            }
        }
        if (is_array($arrayJurisdiccionesId) && count($arrayJurisdiccionesId) > 0)
        {
            $arrayResponse[] = array("id_jurisdiccion"     => -1, 
                                    "jurisdiccion" => "Todas");
            foreach ($arrayJurisdiccionesEmpresa as $empresa ) 
            {
                if (in_array((string) $empresa->getId(), $arrayJurisdiccionesId)) 
                {
                    $objJurisdiccion = array("id_jurisdiccion"=>$empresa->getId(), "jurisdiccion"=>$empresa->getNombreJurisdiccion());
                    $arrayResponse[] = $objJurisdiccion;
                }
            }
            usort($arrayResponse, "cmp");
            $objData = '{"total":"' . count($arrayResponse) . '","encontrados":' . json_encode($arrayResponse) . '}';

            $objRespuesta->setContent($objData);
        
            return $objRespuesta;
        }


        $strFiltrarJurisdiccion = $objRequest->get('strFiltrarJurisdiccion') ? $objRequest->get('strFiltrarJurisdiccion') : "";

        $emComercial            = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceTecnico         = $this->get('tecnico.InfoServicioTecnico');
        $intIdOficina           = 0;
        
        if($strPrefijoEmpresa=="TN")
        {
            $intIdPerSession        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
            if($intIdPerSession)
            {
                $objPerSession              = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($intIdPerSession);
                $arrayEstadosJurisdiccion   = array("Activo","Modificado");
                if(is_object($objPerSession))
                {
                    $objOficinaPerSession   = $objPerSession->getOficinaId();
                    if(is_object($objOficinaPerSession))
                    {
                        
                        $arrayParametros = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->get('FACTIBILIDAD_SECTOR_POR_OFICINA', 
                                                                                                            '', 
                                                                                                            '', 
                                                                                                            '', 
                                                                                                            $objOficinaPerSession->getNombreOficina(),
                                                                                                            '', 
                                                                                                            '', 
                                                                                                            '');
                        if(is_array($arrayParametros))
                        {             
                            foreach ($arrayParametros as $arrayParametro)
                            {
                                 $arraySectoresId[] = $arrayParametro['valor3'];
                            }
                        }
                        
                        if(count($arraySectoresId) == 0 )                        
                        {
                            $intIdOficinaPerSession         = $objOficinaPerSession->getId();
                            $arrayParamsOficinaJurisdiccion = array(
                                                                    "oficinaId" => $intIdOficinaPerSession,
                                                                    "estado"    => $arrayEstadosJurisdiccion
                                                                    );
                            $objJurisdiccionXIdOficinaPerSession    = $emComercial->getRepository("schemaBundle:AdmiJurisdiccion")
                                                                                  ->findOneBy($arrayParamsOficinaJurisdiccion);

                            if(is_object($objJurisdiccionXIdOficinaPerSession))
                            {
                                $intIdOficina       = $intIdOficinaPerSession;
                            }
                            else
                            {
                                /*
                                 * No existe jurisdicción con el id oficina de la persona en 
                                 * sesión, por lo que se consulta el id de la oficina referenciada
                                 * 
                                 */
                                $intIdRefOficina        = $objOficinaPerSession->getRefOficinaId();
                                if($intIdRefOficina)
                                {
                                    $arrayParamsOficinaRefJurisdiccion = array(
                                                                                "oficinaId" => $intIdRefOficina,
                                                                                "estado"    => $arrayEstadosJurisdiccion
                                                                              );

                                    $objJurisdiccionXIdRefOficinaPerSession    = $emComercial->getRepository("schemaBundle:AdmiJurisdiccion")
                                                                                             ->findOneBy($arrayParamsOficinaRefJurisdiccion);
                                    if(is_object($objJurisdiccionXIdRefOficinaPerSession))
                                    {
                                        $intIdOficina   = $intIdRefOficina;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $strFiltrarJurisdiccion = "NO";
        }
        
        $strFiltrarJurisdiccion  ="NO";
        
        //se agrage modificacion de parametros para realizar consulta de registros de prefactibilidad
        $arrayParametros                             = array();
        $arrayParametros["em"]                       = $emComercial;
        $arrayParametros["start"]                    = $intStart;
        $arrayParametros["limit"]                    = $intLimit;
        $arrayParametros["search_fechaDesdePlanif"]  = $strFechaDesdePlanif[0];
        $arrayParametros["search_fechaHastaPlanif"]  = $strFechaHastaPlanif[0];
        $arrayParametros["search_login2"]            = $strLogin2;
        $arrayParametros["search_descripcionPunto"]  = $strDescripcionPunto;
        $arrayParametros["search_vendedor"]          = $strVendedor;
        $arrayParametros["search_ciudad"]            = $strCiudad;
        $arrayParametros["search_numOrdenServicio"]  = $intNumOrdenServicio;
        $arrayParametros["codEmpresa"]               = $strCodEmpresa;
        $arrayParametros["ultimaMilla"]              = $strUltimaMilla;
        $arrayParametros["validaRechazado"]          = "";
        $arrayParametros["intIdOficina"]             = $intIdOficina;
        $arrayParametros["strFiltrarJurisdiccion"]   = $strFiltrarJurisdiccion;
        $arrayParametros["serviceTecnico"]           = $serviceTecnico;
        $arrayParametros["arrayJurisdiccionesId"]    = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]        = $strIdJurisdiccion;
        $arrayParametros["jurisdiccion"] = $strJurisdiccion;
        $arrayParametros["limite"] = $strLimite;

        if(count($arraySectoresId) > 0 )
        {
            $arrayParametros["arraySectoresId"]      = $arraySectoresId;
        }
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonPreFactibilidad($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

    
    /**
     * @Secure(roles="ROLE_403-94")
     * 
     * Documentación para el método 'rechazarAjaxAction'.
     *
     * Rechaza solicitudes de factibilidad via Ajax.
     * @return response con mensaje de respuesta.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 10-05-2021 Se modifican los parámetros enviados a la función liberarInterfaceSplitter
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 26-05-2021 Se agrega si el servicio es tipo de red GPON en los parámetros de la función liberarInterfaceSplitter
     *
     */
    public function rechazarAjaxAction()
    {
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion        = $this->get('request');
        $intId              = $objPeticion->get('id');
        $intIdMotivo        = $objPeticion->get('id_motivo');
        $strObservacion     = $objPeticion->get('observacion');
        $objSession         = $objPeticion->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $em                 = $this->getDoctrine()->getManager();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");   
        $em_infraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');

        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intId);
        $entityMotivo           = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);        
		
		$em->getConnection()->beginTransaction();
		$em_infraestructura->getConnection()->beginTransaction();
		
        try{
			if($entityDetalleSolicitud)
            {
				$entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
				$entityServicio->setEstado("Rechazada");
				$em->persist($entityServicio);
				$em->flush();   	
		
                //se verifica si el servicio es tipo de red GPON
                $booleanTipoRedGpon = false;
                if(is_object($entityServicio->getProductoId()))
                {
                    $objCaractTipoRed = $serviceServicioTecnico->getServicioProductoCaracteristica($entityServicio, 
                                                                                                   "TIPO_RED", 
                                                                                                   $entityServicio->getProductoId());
                    if(is_object($objCaractTipoRed))
                    {
                        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                                'COMERCIAL',
                                                                                                                '',
                                                                                                                'VERIFICAR TIPO RED',
                                                                                                                'VERIFICAR_GPON',
                                                                                                                $objCaractTipoRed->getValor(),
                                                                                                                '',
                                                                                                                '',
                                                                                                                '');
                        if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                        {
                            $booleanTipoRedGpon = true;
                        }
                    }
                }
				//liberacion de ptos para Telconet Light
                /* @var $serviceInterfaceElemento InfoInterfaceElementoService */
                $serviceInterfaceElemento       = $this->get('tecnico.InfoInterfaceElemento');
                $arrayRespuestaLiberaSplitter   = $serviceInterfaceElemento->liberarInterfaceSplitter(
                                                            array(  "objServicio"           => $entityServicio,
                                                                    "strUsrCreacion"        => $objPeticion->getSession()->get('user'),
                                                                    "strIpCreacion"         => $objPeticion->getClientIp(),
                                                                    "strProcesoLibera"      => " por rechazo del servicio",
                                                                    "strVerificaLiberacion" => "SI",
                                                                    "booleanTipoRedGpon"    => $booleanTipoRedGpon,
                                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresa));
                $strStatusLiberaSplitter        = $arrayRespuestaLiberaSplitter["status"];
                $strMensajeLiberaSplitter       = $arrayRespuestaLiberaSplitter["mensaje"];
                if($strStatusLiberaSplitter === "ERROR")
                {
                    $em->getConnection()->rollback();
                    $objRespuesta->setContent($strMensajeLiberaSplitter);
                    return $objRespuesta;
                }
                
                //GUARDAR INFO SERVICIO HISTORIAL
				$entityServicioHistorial = new InfoServicioHistorial();  
				$entityServicioHistorial->setServicioId($entityServicio);	
				$entityServicioHistorial->setMotivoId($intIdMotivo);
				$entityServicioHistorial->setObservacion($strObservacion);	
				$entityServicioHistorial->setIpCreacion($objPeticion->getClientIp());
				$entityServicioHistorial->setFeCreacion(new \DateTime('now'));
				$entityServicioHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));	
				$entityServicioHistorial->setEstado('Rechazada'); 
				$em->persist($entityServicioHistorial);
				$em->flush();  
				
				$entityDetalleSolicitud->setMotivoId($intIdMotivo);
				$entityDetalleSolicitud->setObservacion($strObservacion);	
				$entityDetalleSolicitud->setEstado("Rechazada");
				$entityDetalleSolicitud->setUsrRechazo($objPeticion->getSession()->get('user'));		
				$entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
				$em->persist($entityDetalleSolicitud);
				$em->flush();               
				
				//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
				$entityDetalleSolHist = new InfoDetalleSolHist();
				$entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
				$entityDetalleSolHist->setObservacion($strObservacion);
				$entityDetalleSolHist->setMotivoId($intIdMotivo);            
				$entityDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
				$entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
				$entityDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
				$entityDetalleSolHist->setEstado('Rechazada');
				$em->persist($entityDetalleSolHist);
				$em->flush();
                
                $objTipoSolicitudPlanficacion = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                   ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                     "estado"               => "Activo"));
                if (is_object($objTipoSolicitudPlanficacion))
                {
                    $objSolicitudPlanficacion = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                   ->findOneBy(array("servicioId"      => $entityServicio->getId(),
                                                                     "tipoSolicitudId" => $objTipoSolicitudPlanficacion->getId(),
                                                                     "estado"          => "Asignar-factibilidad"));
                    if (is_object($objSolicitudPlanficacion))
                    {
                        $objSolicitudPlanficacion->setEstado('Rechazada');  
                        $em->persist($objSolicitudPlanficacion);
                        $em->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $objDetalleSolHist = new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objSolicitudPlanficacion);            
                        $objDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
                        $objDetalleSolHist->setEstado('Rechazada');  
                        $em->persist($objDetalleSolHist);
                        $em->flush();
                        
                        $intCantidadServicios = 0;
                        $objServicioTecnicoRechazar = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneBy(array( "servicioId" =>$entityServicio));
                        if (is_object($objServicioTecnicoRechazar))
                        {
                            $objInterfaceElementoSwAnt = $em_infraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                            ->find($objServicioTecnicoRechazar->getInterfaceElementoId());
                            if (is_object($objInterfaceElementoSwAnt) && $objInterfaceElementoSwAnt->getEstado() == 'reserved')
                            {
                                $arrayServiciosTecnicos = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                             ->findBy(array( "interfaceElementoId" => $objInterfaceElementoSwAnt->getId()));
                                foreach($arrayServiciosTecnicos as $objServicioTecnico)
                                {
                                    $objServicio       = $objServicioTecnico->getServicioId();
                                    $strEstadoServicio = $objServicio->getEstado();
                                    if($strEstadoServicio == "Factibilidad-anticipada" || $strEstadoServicio == "Asignar-factibilidad")
                                    {
                                        $intCantidadServicios++;
                                    }
                                }

                                if ($intCantidadServicios <=1)
                                {
                                    // se reserva puerto del switch
                                    $objInterfaceElementoSwAnt->setEstado('not connect');
                                    $objInterfaceElementoSwAnt->setUsrUltMod($objPeticion->getSession()->get('user'));
                                    $objInterfaceElementoSwAnt->setFeUltMod(new \DateTime('now'));
                                    $em_infraestructura->persist($objInterfaceElementoSwAnt);
                                    $em_infraestructura->flush(); 
                                    
                                    $objServicioTecnicoRechazar->setElementoId(null);
                                    $objServicioTecnicoRechazar->setInterfaceElementoId(null);
                                    $em->persist($objServicioTecnicoRechazar);
                                    $em->flush(); 
                                }
                            }
                        }
                    }
                }
				
				//------- COMUNICACIONES --- NOTIFICACIONES 
                $asunto  ="Rechazo de Solicitud de Factibilidad de Instalacion #".$entityDetalleSolicitud->getId();
                $to = array();
                
                if($entityServicio->getUsrVendedor())
                {
                    //DESTINATARIOS.... 
                    $formasContacto = $em->getRepository('schemaBundle:InfoPersona')
                                         ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getUsrVendedor(),
                                                                                      'Correo Electronico');
                    
                    if($formasContacto)
                    {
                        foreach($formasContacto as $formaContacto)
                        {
                            $to[] = $formaContacto['valor'];
                        }
                    }
                }
                
                /*Envío de correo por medio de plantillas**/
                /* @var $envioPlantilla EnvioPlantilla */
                $arrayParametros    = array('detalleSolicitud' => $entityDetalleSolicitud,'motivo'=> $entityMotivo);
                $envioPlantilla     = $this->get('soporte.EnvioPlantilla');
                $envioPlantilla->generarEnvioPlantilla( $asunto, 
                                                        $to, 
                                                        'RECHAZA_FACTIB', 
                                                        $arrayParametros,
                                                        $intIdEmpresa,
                                                        '',
                                                        '',
                                                        null, 
                                                        true,
                                                        'notificaciones_telcos@telconet.ec');
                
				$objRespuesta->setContent("Se rechazo la Solicitud de Factibilidad");                      
			}
			else
            {
				$objRespuesta->setContent("No existe el detalle de solicitud");
            }
			
            $em->getConnection()->commit();
            $em_infraestructura->getConnection()->commit();
			
		}catch(\Exception $e){
			if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            if ($em_infraestructura->getConnection()->isTransactionActive())
            {
                $em_infraestructura->getConnection()->rollback();
            }
			$mensajeError = "Error: ".$e->getMessage();
			error_log($mensajeError);
			$objRespuesta->setContent($mensajeError);
		}
        
        $em->getConnection()->close();
        $em_infraestructura->getConnection()->close();
		return $objRespuesta;
    }	    
    
    /**
     * @Secure(roles="ROLE_135-2717")
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 28-12-2017 - Metodo utilizado para guardar nuevas factibilidades asignadas al servicio INTERNET SMALL BUSINESS 
     *                           Se utiliza flujo de Megadatos para realizar la factibilidad.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 29-07-2018 - Se cambia validación de empresa en revisión de proyecto ZTE
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 01-10-2018 - Se agrega el envío de la notificación al asesor comercial y al asistente cuando la factibilidad en el traslado
     *                           de un servicio Small Business haya provisto un olt diferente al olt del punto origen
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 11-02-2019 - Se mapea el nombre técnico respectivo para la notificación que se envía al guardar la factibilidad 
     *                            de servicios Small Business y TelcoHome cuando es un traslado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 18-03-2019 - Se agrega validación para que la notificación por traslado sólo se envíe cuando sea un traslado de Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 05-04-2020 - Se modifica la consulta del producto Small Business por id del producto y no por nombre técnico 
     *                            al realizar un traslado 
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 05-04-2021 - Se valida que el OLT para servicios TN con red GPON sea permitido que el elemento sea Multiplataforma.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 26-04-2021 - Se agrega validación para actualizar los estados de los servicios adicionales del producto Datos SafeCity.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 16-03-2022 - Se valida cantidad de vrf del elemento olt para los servicios Cámara Safecity para la red GPON_MPLS.
     *
     * @return objResponse
     */
    public function ajaxGuardaNuevaFactibilidadAction()
    {
        $objRespuesta                       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion                        = $this->get('request');
        $objSession                         = $objPeticion->getSession();
        $intIdEmpresa                       = 18;
        $strUsrCreacion                     = $objSession->get('user');
        $intIdSolFactibilidad               = $objPeticion->get('intIdSolFactibilidad');
        $intIdElemento                      = $objPeticion->get('intIdElemento');
        $intIdInterfaceElemento             = $objPeticion->get('intIdInterfaceElemento');
        $intIdElementoCaja                  = $objPeticion->get('intIdElementoCaja');
        $intElementoPNivel                  = $objPeticion->get('intElementoPNivel');
        $strErrorMetraje                    = $objPeticion->get('strErrorMetraje');
        $strNombreTipoElemento              = $objPeticion->get('strNombreTipoElemento');
        $strTipoRed                         = $objPeticion->get('strTipoRed')?$objPeticion->get('strTipoRed'):"MPLS";
        $intIdEmpresaOrigin                 = $objSession->get('idEmpresa');
        $strPrefijoEmpresaOrigin            = $objSession->get('prefijoEmpresa');
        $boolFactibilidadOk                 = false;
        $arrayEstados                       = array('FactibilidadEnProceso','PreFactibilidad','Factible','PrePlanificada','Detenido','Planificada','RePlanificada','AsignadoTarea');
        $em                                 = $this->getDoctrine()->getManager();
        $emInfraestructura                  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral                          = $this->getDoctrine()->getManager("telconet_general");
        $strObservacion                     = 'Factibilidad por nuevo Tramo<br>';
        $strMetraje                         = '';
        $strEstado                          = '';
        $entityDetalleSolicitud             = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolFactibilidad);
        $em->getConnection()->beginTransaction();

        try
        {
            //se verifica si el servicio es tipo de red GPON
            $booleanTipoRedGpon = false;
            $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'VERIFICAR TIPO RED',
                                                                                                    'VERIFICAR_GPON',
                                                                                                    $strTipoRed,
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
            {
                $booleanTipoRedGpon = true;
            }
            if($booleanTipoRedGpon)
            {
                //seteo la variable para validar que el elemento sea permitido Olt Multiplataforma
                $booleanPermiteGpon = false;
                //Se consulta el modelo del OLT
                $objInfoElemento    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
                if(is_object($objInfoElemento))
                {
                    $strDetalleMulti         = "MULTIPLATAFORMA";
                    $arrayParametrosDetMulti = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
                    if(isset($arrayParametrosDetMulti) && !empty($arrayParametrosDetMulti)
                       && isset($arrayParametrosDetMulti['valor1']) && !empty($arrayParametrosDetMulti['valor1']))
                    {
                        $strDetalleMulti = $arrayParametrosDetMulti['valor1'];
                    }
                    $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array("elementoId"    => $objInfoElemento->getId(),
                                                                          "detalleNombre" => $strDetalleMulti,
                                                                          "detalleValor"  => "SI",
                                                                          "estado"        => "Activo"));
                    if(is_object($objDetalleElemento))
                    {
                        $booleanPermiteGpon = true;
                    }
                }
                //Validar que el OLT sea Multiplataforma
                if(!$booleanPermiteGpon)
                {
                    $objRespuesta->setContent("El OLT seleccionado para la Red GPON no soporta Multiplataforma.");
                    return $objRespuesta;
                }
            }

            if(is_object($entityDetalleSolicitud))
            {
                //obtengo al servicio y al servicio tecnico
                $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());

                //se agrega codigo para la validacion del estado del servicio y no realizar asignaciones de factibilidad erroneas
                if(is_object($entityServicio))
                {
                    if(in_array($entityServicio->getEstado(), $arrayEstados))
                    {   
                         if($intIdElemento != 0 && $intIdElemento)
                         {
                             $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneByServicioId($entityServicio->getId());
                             $strEstado = "Factible";
                             //obtengo datos del ingreso de la factibilidad
                             $entityInfoElemento                  = $em->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
                             $strMarcaModeloElemento              = $entityInfoElemento->getModeloElementoId()->getMarcaElementoId()
                                                                                       ->getNombreMarcaElemento();
                             $strNombreTipoElemento               = $entityInfoElemento->getModeloElementoId()
                                                                                       ->getTipoElementoId()->getNombreTipoElemento();
                             $strNombreElemento                   = $entityInfoElemento->getNombreElemento();
                             $strNombreInterfaceElemento          = sprintf("%s", $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                     ->find($intIdInterfaceElemento));
                             $strNombreElementoContenedor         = sprintf("%s", $em->getRepository('schemaBundle:InfoElemento')
                                                                                     ->find($intIdElementoCaja));
                             $entityInfoElementoDistribucion      = $em->getRepository('schemaBundle:InfoElemento')->find($intElementoPNivel);
                             $strMarcaElementoDistribuidor        = $entityInfoElementoDistribucion->getModeloElementoId()->getMarcaElementoId()
                                                                                                   ->getNombreMarcaElemento();
                             $strNombreTipoElementoDistribuidor   = $entityInfoElementoDistribucion->getModeloElementoId()
                                                                                                   ->getTipoElementoId()->getNombreTipoElemento();
                             $strNombreElementoDistribuidor       = $entityInfoElementoDistribucion->getNombreElemento();

                             //Si la empresa es MD obtiene la primera interface en estado no connect que encuentre.
                             $entityInterfaceElementoDistribucion = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->findOneBy(array("elementoId" => $intElementoPNivel,
                                                                                         "estado"     => "not connect"));
                              if(!is_object($entityInterfaceElementoDistribucion))
                              {
                                 //No encontro interfaces y termina el metodo.
                                 $objRespuesta->setContent("No se puede obtener la interface del elemento de distribucion " 
                                                        . $strNombreTipoElementoDistribuidor);
                                 return $objRespuesta;
                              }

                             if($booleanTipoRedGpon)
                             {
                                 $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
                                 $arrayValidarVrfCamaras = $serviceServicioTecnico->validarVrfCamaraGponMpls(
                                                            array("objPunto"               => $entityServicio->getPuntoId(),
                                                                  "strTipoOrden"           => 'FACTIBILIDAD',
                                                                  "intIdInterfaceConector" => $entityInterfaceElementoDistribucion->getId(),
                                                                  "strCodEmpresa"          => $intIdEmpresaOrigin,
                                                                  "strPrefijoEmpresa"      => $strPrefijoEmpresaOrigin,
                                                                  "strUsrCreacion"         => $strUsrCreacion,
                                                                  "strIpCreacion"          => $objPeticion->getClientIp()));
                                 if($arrayValidarVrfCamaras['status'] == "ERROR")
                                 {
                                     //No encontro interfaces y termina el metodo.
                                     $objRespuesta->setContent($arrayValidarVrfCamaras['mensaje']);
                                     return $objRespuesta;
                                 }
                             }

                             $strDatosNuevos = "<b>Datos Nuevos:</b><br> "
                                          . "<b>" . $strNombreTipoElemento . ":</b></b> " . $strNombreElemento . "<br> "
                                          . "<b>Marca:</b> " . $strMarcaModeloElemento . "<br> "
                                          . "<b>Puerto:</b> " . $strNombreInterfaceElemento . "<br> "
                                          . "<b>Caja:</b> " . $strNombreElementoContenedor . "<br> "
                                          . '<b>' . $strNombreTipoElementoDistribuidor . ":</b> " . $strNombreElementoDistribuidor . "<br> "
                                          . "<b>Marca:</b> " . $strMarcaElementoDistribuidor . "<br> "
                                          . "<b>Int " . $strNombreTipoElementoDistribuidor . ":</b> "
                                          . $entityInterfaceElementoDistribucion->getNombreInterfaceElemento() . "<br> "
                                          . $strMetraje
                                          . $strErrorMetraje;

                             //si es el mismo estado es porque estan editando la factibilidad
                             
                            $arrayEstadosServicio = array("Factible", 
                                                          "PrePlanificada", 
                                                          "Planificada", 
                                                          "Detenido", 
                                                          "RePlanificada", 
                                                          "AsignadoTarea"); 
                            
                            $strEstadoServicio    = $entityServicio->getEstado();
                             
                             if (in_array($strEstadoServicio, $arrayEstadosServicio)) 
                             {
                                 $strEstado = $entityServicio->getEstado();
                                 
                                 if($entityServicioTecnico->getElementoId() > 0            &&
                                    $entityServicioTecnico->getInterfaceElementoId() > 0  &&
                                    $entityServicioTecnico->getElementoContenedorId() > 0 &&
                                    $entityServicioTecnico->getElementoConectorId() > 0   &&
                                    $entityServicioTecnico->getInterfaceElementoConectorId() > 0)
                                 {
                                     $entityInfoElementoAnt                  = $em->getRepository('schemaBundle:InfoElemento')
                                                                                  ->find($entityServicioTecnico->getElementoId());
                                     
                                     $strMarcaModeloElementoAnt              = $entityInfoElementoAnt->getModeloElementoId()->getMarcaElementoId()
                                                                                                     ->getNombreMarcaElemento();
                                     
                                     $strNombreTipoElementoAnt               = $entityInfoElementoAnt->getModeloElementoId()
                                                                                                     ->getTipoElementoId()->getNombreTipoElemento();
                                     
                                     $strNombreElementoAnt                   = $entityInfoElementoAnt->getNombreElemento();
                                     
                                     $strNombreInterfaceElementoAnt          = sprintf("%s", $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                             ->find($entityServicioTecnico->getInterfaceElementoId()));
                                     
                                     $strNombreElementoContenedorAnt         = sprintf("%s", $em->getRepository('schemaBundle:InfoElemento')
                                                                                             ->find($entityServicioTecnico->getElementoContenedorId()));
                                     
                                     $entityInfoElementoDistribucionAnt      = $em->getRepository('schemaBundle:InfoElemento')
                                                                                  ->find($entityServicioTecnico->getElementoConectorId());
                                     
                                     $strMarcaElementoDistribuidorAnt        = $entityInfoElementoDistribucionAnt->getModeloElementoId()
                                                                                                                 ->getMarcaElementoId()
                                                                                                                 ->getNombreMarcaElemento();
                                     
                                     $strNombreTipoElementoDistribuidorAnt   = $entityInfoElementoDistribucionAnt->getModeloElementoId()
                                                                                                                 ->getTipoElementoId()
                                                                                                                 ->getNombreTipoElemento();
                                     
                                     $strNombreElementoDistribuidorAnt       = $entityInfoElementoDistribucionAnt->getNombreElemento();

                                     $entityInterfaceElementoDistribucionAnt = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                  ->find($entityServicioTecnico->getInterfaceElementoConectorId());
                                     
                                     //libero interface anterior
                                     $entityInterfaceElementoDistribucionAnt->setEstado("not connect");
                                     $em->persist($entityInterfaceElementoDistribucionAnt);
                                     $em->flush();

                                     $strDatosAnteriores = "<b>Datos Anteriores:</b><br> "
                                         . "<b>" . $strNombreTipoElementoAnt . ":</b> " . $strNombreElementoAnt . "<br> "
                                         . "<b>Marca:</b> " . $strMarcaModeloElementoAnt . "<br> "
                                         . "<b>Puerto:</b> " . $strNombreInterfaceElementoAnt . "<br> "
                                         . "<b>Caja:</b> " . $strNombreElementoContenedorAnt . "<br> "
                                         . "<b>" . $strNombreTipoElementoDistribuidorAnt . ":</b> " . $strNombreElementoDistribuidorAnt . "<br> "
                                         . "<b>Marca:</b> " . $strMarcaElementoDistribuidorAnt . "<br> "
                                         . "<b>Int " . $strNombreTipoElementoDistribuidorAnt . ":</b> "
                                         . $entityInterfaceElementoDistribucionAnt->getNombreInterfaceElemento() . "<br> "
                                         . $strMetraje
                                         . $strErrorMetraje;
                                 }
                                 else
                                 {
                                     $strDatosAnteriores = "No se encontraron datos anteriores para la actualizacion.";
                                 }

                                 $strObservacion = "Se edito la Factibilidad.<br>" . $strDatosNuevos . "<br>" . $strDatosAnteriores;
                             }
                             else
                             {
                                 //Se guarda por primera vez la factibilidad
                                 $strObservacion .=$strDatosNuevos;
                             }
                             
                             //reservo una interface del elemento de distribucion
                             $entityInterfaceElementoDistribucion->setEstado('Factible');
                             $em->persist($entityInterfaceElementoDistribucion);
                             $em->flush();
                             
                             //actualizo servicio tecnico
                             $entityServicioTecnico->setElementoId($intIdElemento);
                             $entityServicioTecnico->setInterfaceElementoId($intIdInterfaceElemento);
                             $entityServicioTecnico->setElementoContenedorId($intIdElementoCaja);
                             $entityServicioTecnico->setElementoConectorId($intElementoPNivel);
                             $entityServicioTecnico->setInterfaceElementoConectorId($entityInterfaceElementoDistribucion->getId());
                             $em->persist($entityServicioTecnico);
                             $em->flush();
                             
                             //actualizo estado del servicio
                             $entityServicio->setEstado($strEstado);
                             $em->persist($entityServicio);
                             $em->flush();
                             
                             //GUARDAR INFO SERVICIO HISTORIAL
                             $entityServicioHistorial = new InfoServicioHistorial();
                             $entityServicioHistorial->setServicioId($entityServicio);
                             $entityServicioHistorial->setIpCreacion($objPeticion->getClientIp());
                             $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                             $entityServicioHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                             $entityServicioHistorial->setEstado($strEstado);
                             $entityServicioHistorial->setObservacion($strObservacion);
                             $em->persist($entityServicioHistorial);
                             $em->flush();
                             
                             //GUARDA INFO DETALLE SOLICITUD
                             $entityDetalleSolicitud->setEstado("Factible");
                             $entityDetalleSolicitud->setObservacion($strObservacion);
                             $em->persist($entityDetalleSolicitud);
                             $em->flush();
                             
                             //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                             $entityDetalleSolHist = new InfoDetalleSolHist();
                             $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                             $entityDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                             $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                             $entityDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
                             $entityDetalleSolHist->setEstado('Factible');
                             $entityDetalleSolHist->setObservacion($strObservacion);
                             $em->persist($entityDetalleSolHist);
                             $em->flush();
                             
                             $boolFactibilidadOk = true;

                            /***ACTUALIZAR ESTADO DEL SERVICIO ADICIONAL***/
                            if(is_object($entityServicio) && is_object($entityServicio->getProductoId()))
                            {
                                $arrayParServAdd = array(
                                    "intIdProducto"      => $entityServicio->getProductoId()->getId(),
                                    "intIdServicio"      => $entityServicio->getId(),
                                    "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                    "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
                                );
                                $arrayProdCaracConfProAdd  = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
                                if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
                                {
                                    foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                                    {
                                        $arrayEstadosSerProAdd = array('Pre-servicio','Pendiente','PreFactibilidad','FactibilidadEnProceso',
                                                                       'Factible','PrePlanificada','AsignadoTarea','Detenido',
                                                                       'Planificada','Replanificada');
                                        $objServicioConfProAdd = $em->getRepository('schemaBundle:InfoServicio')
                                                                        ->createQueryBuilder('p')
                                                                        ->where("p.id = :idServicio")
                                                                        ->andWhere("p.estado IN (:estados)")
                                                                        ->setParameter('idServicio', $arrayServicioConfProAdd['idServicio'])
                                                                        ->setParameter('estados',    array_values($arrayEstadosSerProAdd))
                                                                        ->setMaxResults(1)
                                                                        ->getQuery()
                                                                        ->getOneOrNullResult();
                                        if(is_object($objServicioConfProAdd))
                                        {
                                            //actualizar estado del servicio adicional
                                            $objServicioConfProAdd->setEstado($entityServicio->getEstado());
                                            $em->persist($objServicioConfProAdd);
                                            $em->flush();
                                            //guardar historial del servicio adicional
                                            $objSerHisConfProAdd = new InfoServicioHistorial();
                                            $objSerHisConfProAdd->setServicioId($objServicioConfProAdd);
                                            $objSerHisConfProAdd->setIpCreacion($objPeticion->getClientIp());
                                            $objSerHisConfProAdd->setFeCreacion(new \DateTime('now'));
                                            $objSerHisConfProAdd->setUsrCreacion($objPeticion->getSession()->get('user'));
                                            $objSerHisConfProAdd->setObservacion($strObservacion);
                                            $objSerHisConfProAdd->setEstado($objServicioConfProAdd->getEstado());
                                            $em->persist($objSerHisConfProAdd);
                                            $em->flush();
                                            //se actualiza la solicitud para los servicios adicionales
                                            $arrayEstadosSolicitud    = array("FactibilidadEnProceso","Factible");
                                            $objSolFactServicioProAdd = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->createQueryBuilder('p')
                                                            ->where("p.servicioId         = :servicioId")
                                                            ->andWhere("p.tipoSolicitudId = :tipoSolicitudId")
                                                            ->andWhere("p.estado          IN (:estados)")
                                                            ->setParameter('servicioId',      $objServicioConfProAdd->getId())
                                                            ->setParameter('tipoSolicitudId', $entityDetalleSolicitud->getTipoSolicitudId()->getId())
                                                            ->setParameter('estados',         array_values($arrayEstadosSolicitud))
                                                            ->setMaxResults(1)
                                                            ->getQuery()
                                                            ->getOneOrNullResult();
                                            //se verifica si no existe la solicitud para ser generada
                                            if(!is_object($objSolFactServicioProAdd))
                                            {
                                                //se genera la solicitud para los servicios adicionales
                                                $objSolFactServicioProAdd = new InfoDetalleSolicitud();
                                                $objSolFactServicioProAdd->setServicioId($objServicioConfProAdd);
                                                $objSolFactServicioProAdd->setTipoSolicitudId($entityDetalleSolicitud->getTipoSolicitudId());
                                                $objSolFactServicioProAdd->setEstado($entityDetalleSolicitud->getEstado());
                                                $objSolFactServicioProAdd->setUsrCreacion($objPeticion->getSession()->get('user'));
                                                $objSolFactServicioProAdd->setObservacion($entityDetalleSolicitud->getObservacion());
                                                $objSolFactServicioProAdd->setFeCreacion(new \DateTime('now'));
                                                $em->persist($objSolFactServicioProAdd);
                                                $em->flush();
                                            }
                                            else
                                            {
                                                //actualiza el estado de la solicitud
                                                $objSolFactServicioProAdd->setEstado($entityDetalleSolicitud->getEstado());
                                                $objSolFactServicioProAdd->setObservacion($entityDetalleSolicitud->getObservacion());
                                                $em->persist($objSolFactServicioProAdd);
                                                $em->flush();
                                            }
                                            $objServTecConfProAdd = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                                        ->findOneByServicioId($objServicioConfProAdd->getId());
                                            if(is_object($objServTecConfProAdd))
                                            {
                                                //actualizo servicio tecnico
                                                $objServTecConfProAdd->setElementoId($intIdElemento);
                                                $objServTecConfProAdd->setInterfaceElementoId($intIdInterfaceElemento);
                                                $objServTecConfProAdd->setElementoContenedorId($intIdElementoCaja);
                                                $objServTecConfProAdd->setElementoConectorId($intElementoPNivel);
                                                $objServTecConfProAdd->setInterfaceElementoConectorId($entityInterfaceElementoDistribucion->getId());
                                                $em->persist($objServTecConfProAdd);
                                                $em->flush();
                                            }
                                            //guardar historial de la solicitud
                                            $entityDetalleSolHistProAdd = new InfoDetalleSolHist();
                                            $entityDetalleSolHistProAdd->setDetalleSolicitudId($objSolFactServicioProAdd); 
                                            $entityDetalleSolHistProAdd->setIpCreacion($objPeticion->getClientIp());
                                            $entityDetalleSolHistProAdd->setFeCreacion(new \DateTime('now'));
                                            $entityDetalleSolHistProAdd->setUsrCreacion($objPeticion->getSession()->get('user'));
                                            $entityDetalleSolHistProAdd->setEstado($objSolFactServicioProAdd->getEstado());
                                            $entityDetalleSolHistProAdd->setObservacion($objSolFactServicioProAdd->getObservacion());
                                            $em->persist($entityDetalleSolHistProAdd);
                                            $em->flush();
                                        }
                                    }
                                }
                            }
                            /***FIN ACTUALIZAR ESTADO DEL SERVICIO ADICIONAL***/

                             /* Generación del envío de correo al aprobar la solicitud de Factibilidad
                              * Se obtiene el vendedor del servicio para agregarlo como destinatario del correo que se enviará al aprobar
                              * la factibilidad
                              */ 
                             $strAsunto         = "Aprobacion de Solicitud de Factibilidad de Instalacion #" . $entityDetalleSolicitud->getId();
                             $arrayTo           = array();

                             if($entityServicio->getUsrVendedor())
                             {
                                 $entityFormasContacto = $em->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getUsrVendedor(), 
                                                                                                         'Correo Electronico');
                                 if(is_object($entityFormasContacto))
                                 {
                                     foreach($entityFormasContacto as $objFormaContacto)
                                     {
                                         $arrayTo[] = $objFormaContacto['valor'];
                                     }
                                 }
                             }

                             /* Envío de correo por medio de plantillas
                              * Se obtiene la plantilla y se invoca a la función generarEnvioPlantilla del service que internamente obtiene
                              * los alias asociados a la plantilla 'APROBAR_FACTIB' y envía el respectivo correo
                              */
                             /* @var $serviceEnvioPlantilla EnvioPlantilla */
                             $arrayParametros           = array('detalleSolicitud' => $entityDetalleSolicitud,'usrAprueba'=>$strUsrCreacion);
                             $serviceEnvioPlantilla     = $this->get('soporte.EnvioPlantilla');
                             $serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto, 
                                                                            $arrayTo, 
                                                                            'APROBAR_FACTIB', 
                                                                            $arrayParametros,
                                                                            $intIdEmpresa,
                                                                            '',
                                                                            '',
                                                                            null, 
                                                                            true,
                                                                            'notificaciones_telcos@telconet.ec');

                             $objRespuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad");
                         }
                         else
                         {
                             $objRespuesta->setContent("Caja sin " . $strNombreTipoElemento . " imposible seguir.");
                         }
                        
                    }
                    else
                    {
                        $objRespuesta->setContent("El servicio se encuentra en estado: " .
                                               $entityServicio->getEstado() .
                                               ". No se puede realizar edicion de factibilidad.");
                        return $objRespuesta;
                    }
                }
            }
            else
            {
                $objRespuesta->setContent("No existe el detalle de Solicitud");
            }
            
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();

            $strMsmError = "Error FactibilidadInstalacionFttxController-ajaxGuardaNuevaFactibilidadAction: " . $e->getMessage();
            error_log($strMsmError);
            $objRespuesta->setContent($strMsmError);
        }

        if($boolFactibilidadOk && is_object($entityServicio) && is_object($entityServicioTecnico) 
            && $entityServicio->getTipoOrden() === "T")
        {
            $serviceSoporteService  = $this->get('soporte.SoporteService');
            $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
            $intIdOltDestino        = $entityServicioTecnico->getElementoId();
            $strCodEmpresaSesion    = $objSession->get('idEmpresa');
            $strIpClient            = $objPeticion->getClientIp();
            if(is_object($entityServicio->getProductoId()))
            {
                $objProductoIsb         = $entityServicio->getProductoId();
                $strNombreTecnicoProd   = $objProductoIsb->getNombreTecnico();
                if(is_object($objProductoIsb) && $strNombreTecnicoProd === "INTERNET SMALL BUSINESS")
                {
                    $objSpcTraslado = $serviceServicioTecnico->getServicioProductoCaracteristica($entityServicio, 
                                                                                                "TRASLADO", 
                                                                                                $objProductoIsb);
                    if(is_object($objSpcTraslado))
                    {
                        $intIdServicioOrigen        = $objSpcTraslado->getValor();
                        $objServicioTecnicoOrigen   = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneByServicioId($intIdServicioOrigen);
                        if(is_object($objServicioTecnicoOrigen))
                        {
                            $intIdOltOrigen         = $objServicioTecnicoOrigen->getElementoId();
                            $arrayRespuestaNotifSb  = $serviceSoporteService->crearNotifFactibTrasladoSb(array(
                                                                                                            "objServicio"       => $entityServicio,
                                                                                                            "intIdOltOrigen"    => $intIdOltOrigen,
                                                                                                            "intIdOltDestino"   => $intIdOltDestino,
                                                                                                            "strCodEmpresa"     => 
                                                                                                            $strCodEmpresaSesion,
                                                                                                            "strIpClient"       => $strIpClient,
                                                                                                            "strUsrSession"     => $strUsrCreacion
                                                                                                        ));
                            if($arrayRespuestaNotifSb["strStatus"] === "OK")
                            {
                                $strContentFactib       = $objRespuesta->getContent();
                                $strNotifFactibTraslado = "<br><strong style='color:red; font-size:14px;'>"
                                                            .$arrayRespuestaNotifSb["strMensaje"]."</strong>";
                                $objRespuesta->setContent($strContentFactib.$strNotifFactibTraslado);
                            }
                        }
                    }
                }
            }
        }
        return $objRespuesta;
    }
    
    
}
