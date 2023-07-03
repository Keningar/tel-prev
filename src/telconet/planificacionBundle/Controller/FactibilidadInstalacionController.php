<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\ReturnResponse;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FactibilidadInstalacionController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * @Secure(roles="ROLE_135-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 08-07-2015
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016 Se modifica el nombre del twig de IndexMD a indexFactibilidad
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 29-06-2017 Se agrega el cambio de tipo medio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 19-09-2017 Se agrega la asignación de factibilidad anticipada
     * @since 1.3
     * 
     * @since 1.1
     */
    public function indexAction($ultimaMilla)
    {
        $rolesPermitidos = array();
        //MODULO 135 - PLANIFIFCACION/FACTIBILIDAD INSTALACION
        if(true === $this->get('security.context')->isGranted('ROLE_135-89'))
        {
            $rolesPermitidos[] = 'ROLE_135-89'; // GRID FACTIBILIDAD MATERIALES
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-96'))
        {
            $rolesPermitidos[] = 'ROLE_135-96'; // GUARDAR FACTIBILIDAD DE MATERIALES VIA AJAX
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-95'))
        {
            $rolesPermitidos[] = 'ROLE_135-95'; // PREPLANIFICAR VIA AJAX
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-94'))
        {
            $rolesPermitidos[] = 'ROLE_135-94'; // RECHAZAR VIA AJAX
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-2717'))
        {
            $rolesPermitidos[] = 'ROLE_135-2717'; // EDITAR FACTIBILIDAD
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-5377'))
        {
            $rolesPermitidos[] = 'ROLE_135-5377'; // CAMBIO TIPO MEDIO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_267-5477'))
        {
            $rolesPermitidos[] = 'ROLE_267-5477'; // FACTIBILIDAD ANTICIPADA
        }
        
        $request = $this->get('request');

        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');

        $em             = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("135", "1");
        //migracion clientes transtelco - se agrega validacion ultima milla
        if($ultimaMilla == "RadioCobre")
        {
            return $this->render('planificacionBundle:Factibilidad:index.html.twig', array(
                                'item'            => $entityItemMenu,
                                'rolesPermitidos' => $rolesPermitidos
            ));
        }
        //migracion clientes transtelco - se agrega validacion ultima milla
        if($ultimaMilla == "FibraOptica")
        {
            return $this->render('planificacionBundle:Factibilidad:indexFactibilidad.html.twig', array(
                                'item'            => $entityItemMenu,
                                'rolesPermitidos' => $rolesPermitidos
            ));
        }
    }

    /**
     * @Secure(roles="ROLE_135-1")
     * 
     * Documentación para el método 'indexConsultarAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 08-07-2015
     */
    public function indexConsultarAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_135-89'))
        {
            $rolesPermitidos[] = 'ROLE_135-89';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-96'))
        {
            $rolesPermitidos[] = 'ROLE_135-96';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-95'))
        {
            $rolesPermitidos[] = 'ROLE_135-95';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-94'))
        {
            $rolesPermitidos[] = 'ROLE_135-94';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_235-1'))
        {
            $rolesPermitidos[] = 'ROLE_235-1';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-2717'))
        {
            $rolesPermitidos[] = 'ROLE_135-2717'; // EDITAR FACTIBILIDAD
        }

        $em             = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("235", "1");


        return $this->render('planificacionBundle:Factibilidad:indexConsultar.html.twig', array(
                             'item'            => $entityItemMenu,
                             'rolesPermitidos' => $rolesPermitidos
        ));
    }


    /**
     * ajaxGridAction
     * 
     * Llena el grid de consulta.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 05-07-2016 Se aumenta ultima milla utp
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 22-11-2016 Se agrega el filtro por jurisdicción sólo para el caso de Factibilidad de Fibra Óptica tomando en cuenta
     *                         la oficina de la persona en sesión.
     *                         Se valida si existe una jurisdicción con el id de la oficina de la persona empresa rol. 
     *                         En caso de que no exista la jurisdicción, el id de la oficina será el id de la oficina referenciada.
     *                         En cualquier otro caso no se filtrará por el id de la oficina.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 25-11-2016 Se modifica que el filtro del listado por jurisdicciones sea sólo para TN, mientras que en MD se mostrará 
     *                        todo el listado.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 19-09-2017 Se envia Service tecnico a metodo de consulta de Factibilidad
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 15-12-2017 Se envia tipo de producto a mostrar y ciudad de sesion del usuario segun su departamento
     *                         Dado que para flujos DC esta pantalla no solo la podra ver GIS sino tambien TI
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 20-03-2018 Inicialización de variables para evitar generacion de log innecesario
     *
     * @author Germán Valenzuela <arsuarez@telconet.ec>
     * @version 1.6 13-07-2020 - Se obtiene los parámetros OCI para enviarlos al método que retorna los servicios a dar factbilidad.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 12-03-2021 Se agrega el filtro para el listado de servicios por Radio/Cobre por oficina asociadas a las jurisdicciones
     *
     * @Secure(roles="ROLE_135-7")
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
        $strFiltrarJurisdiccion = $objRequest->get('strFiltrarJurisdiccion') ? $objRequest->get('strFiltrarJurisdiccion') : "";

        $emCom                  = $this->getDoctrine()->getManager("telconet"); 
        $emComercial            = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral              = $this->getDoctrine()->getManager("telconet_general");
        $intIdDepartamento      = $objSession->get('idDepartamento');
        $intIdOficina           = 0;       
        $arraySectoresId        = array();
        $arrayJurisdiccionesId  = array();
        $arrayJurisdiccionesEmpresaId = array();

        $strIdJurisdiccion         =$objRequest->get('id_jurisdiccion');

        $strLimite = $objRequest->get('limite');

        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emCom->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $objSession->get('idPersonaEmpresaRol');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emCom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
        $arrayJurisdiccionesEmpresa = $emCom->getRepository('schemaBundle:AdmiJurisdiccion')
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
                        
                        if(isset($intIdOficina) && !empty($intIdOficina) 
                            && isset($strUltimaMilla) && !empty($strUltimaMilla) && $strUltimaMilla === "Radio,Cobre")
                        {
                            $strFiltrarJurisdiccion = "SI";
                        }
                    }
                }
            }
        }
        else
        {
            $strFiltrarJurisdiccion = "NO";
        }
        
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
        $arrayParametros["serviceTecnico"]           = $this->get('tecnico.InfoServicioTecnico');
        $arrayParametros["arrayJurisdiccionesId"]    = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]          = $strIdJurisdiccion;
        $arrayParametros["limite"]                   = $strLimite;
        //TODO cambios
        if(count($arraySectoresId) > 0)
        {
            $arrayParametros["arraySectoresId"]      = $arraySectoresId;            
            if($strUltimaMilla != 'Radio,Cobre')
            {
                $arrayParametros["ultimaMilla"]          = 'Fibra Optica,UTP,Radio,Cobre';
            }
        }
        
        $objDepartamento = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);
        $arrayProductos          = array();
        $arrayProductosExcepcion = array();
        
        if(is_object($objDepartamento))
        {
            //Si el usuario pertence a IPCCL2 se muestra la informacion ligada a DATACENTER
            $arrayInfoVisualizacion   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('VISUALIZACION DATOS POR DEPARTAMENTO', 
                                                          'COMERCIAL', 
                                                          '',
                                                          'FACTIBILIDAD',
                                                          $objDepartamento->getNombreDepartamento(),
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $objSession->get('idEmpresa'));
            if(!empty($arrayInfoVisualizacion))
            {
                foreach($arrayInfoVisualizacion as $array)
                {
                    $arrayProductos[] = array($array['valor2']);
                }
            }
            else//Filtra los productos que no seben ser mostrados en flujos normales con donde solo interviene GIS
            {
                $arrayInfoNoVisualizacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES', 
                                                              'COMERCIAL', 
                                                              '',
                                                              'FACTIBILIDAD',
                                                              '',
                                                              '',
                                                              '',
                                                              '', 
                                                              '', 
                                                              $objSession->get('idEmpresa'));
                if(!empty($arrayInfoNoVisualizacion))
                {
                    foreach($arrayInfoNoVisualizacion as $array)
                    {
                        $arrayProductosExcepcion[] = array($array['valor1']);
                    }
                }
            }
        }

        $arrayParametros['arrayDescripcionProducto']          = $arrayProductos;
        $arrayParametros['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;
        $arrayParametros['strPrefijoEmpresa']                 = $strPrefijoEmpresa;

        $arrayParametros["ociCon"] = array('userCom'     => $this->container->getParameter('user_comercial'),
                                           'passCom'     => $this->container->getParameter('passwd_comercial'),
                                           'databaseDsn' => $this->container->getParameter('database_dsn'));

        //migracion clientes transtelco - se agrega parametro ultima milla
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
        $strFiltrarJurisdiccion = $objRequest->get('strFiltrarJurisdiccion') ? $objRequest->get('strFiltrarJurisdiccion') : "";

        $emCom                  = $this->getDoctrine()->getManager("telconet"); 
        $emComercial            = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral              = $this->getDoctrine()->getManager("telconet_general");
        $intIdDepartamento      = $objSession->get('idDepartamento');
        $intIdOficina           = 0;       
        $arraySectoresId        = array();
        $arrayJurisdiccionesId  = array();
        $arrayJurisdiccionesEmpresaId = array();

        $strIdJurisdiccion         =$objRequest->get('id_jurisdiccion');
        $strJurisdiccion = $objRequest->get('query');

        $strLimite = $objRequest->get('limite');

        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emCom->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $objSession->get('idPersonaEmpresaRol');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emCom->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
        $arrayJurisdiccionesEmpresa = $emCom->getRepository('schemaBundle:AdmiJurisdiccion')
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
                        
                        if(isset($intIdOficina) && !empty($intIdOficina) 
                            && isset($strUltimaMilla) && !empty($strUltimaMilla) && $strUltimaMilla === "Radio,Cobre")
                        {
                            $strFiltrarJurisdiccion = "SI";
                        }
                    }
                }
            }
        }
        else
        {
            $strFiltrarJurisdiccion = "NO";
        }
        
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
        $arrayParametros["serviceTecnico"]           = $this->get('tecnico.InfoServicioTecnico');
        $arrayParametros["arrayJurisdiccionesId"]    = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]          = $strIdJurisdiccion;
        $arrayParametros["limite"]                   = $strLimite;
        $arrayParametros["jurisdiccion"]                   = $strJurisdiccion;

        if(count($arraySectoresId) > 0 )
        {
            $arrayParametros["arraySectoresId"]      = $arraySectoresId;            
            if($strUltimaMilla != 'Radio,Cobre')
            {
                $arrayParametros["ultimaMilla"]          = 'Fibra Optica,UTP,Radio,Cobre';
            }
        }
        
        $objDepartamento = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);
        $arrayProductos          = array();
        $arrayProductosExcepcion = array();
        
        if(is_object($objDepartamento))
        {
            //Si el usuario pertence a IPCCL2 se muestra la informacion ligada a DATACENTER
            $arrayInfoVisualizacion   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('VISUALIZACION DATOS POR DEPARTAMENTO', 
                                                          'COMERCIAL', 
                                                          '',
                                                          'FACTIBILIDAD',
                                                          $objDepartamento->getNombreDepartamento(),
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $objSession->get('idEmpresa'));
            if(!empty($arrayInfoVisualizacion))
            {
                foreach($arrayInfoVisualizacion as $array)
                {
                    $arrayProductos[] = array($array['valor2']);
                }
            }
            else
            {
                //Filtra los productos que no seben ser mostrados en flujos normales con donde solo interviene GIS
                $arrayInfoNoVisualizacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES', 
                                                              'COMERCIAL', 
                                                              '',
                                                              'FACTIBILIDAD',
                                                              '',
                                                              '',
                                                              '',
                                                              '', 
                                                              '', 
                                                              $objSession->get('idEmpresa'));
                if(!empty($arrayInfoNoVisualizacion))
                {
                    foreach($arrayInfoNoVisualizacion as $array)
                    {
                        $arrayProductosExcepcion[] = array($array['valor1']);
                    }
                }
            }
        }

        $arrayParametros['arrayDescripcionProducto']          = $arrayProductos;
        $arrayParametros['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;
        $arrayParametros['strPrefijoEmpresa']                 = $strPrefijoEmpresa;

        $arrayParametros["ociCon"] = array('userCom'     => $this->container->getParameter('user_comercial'),
                                           'passCom'     => $this->container->getParameter('passwd_comercial'),
                                           'databaseDsn' => $this->container->getParameter('database_dsn'));

        //migracion clientes transtelco - se agrega parametro ultima milla
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonPreFactibilidad($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    /*
	 * Llena el grid de consulta.
	 */
    /**
	* @Secure(roles="ROLE_135-7")
	*/
    public function ajaxGridConsultarAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
		
        $fechaDesdePlanif = explode('T',$peticion->get('fechaDesdePlanif'));
        $fechaHastaPlanif = explode('T',$peticion->get('fechaHastaPlanif'));
        $fechaDesdeIngOrd = explode('T',$peticion->get('fechaDesdeIngOrd'));
        $fechaHastaIngOrd = explode('T',$peticion->get('fechaHastaIngOrd'));
        
        $login2 = $peticion->get('login2');
        $descripcionPunto = $peticion->get('descripcionPunto');
        $vendedor = $peticion->get('vendedor');
        $ciudad = $peticion->get('ciudad');
        $numOrdenServicio = $peticion->get('numOrdenServicio');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonFactibilidad($em, $start, $limit, $fechaDesdePlanif[0], $fechaHastaPlanif[0],
										$login2, $descripcionPunto, $vendedor, $ciudad, $numOrdenServicio,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * getTareasByProcesoAndTareaAction
     *
     * Esta funcion obtiene la tarea y materiales usados para los parámetros enviados
     *
     * @version 1.0 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 05-09-2016 Se realizan modificaciones para que en caso de que el servicio no tenga última milla, no se muestre el mensaje de error
     *                         por el id de la última milla y en su lugar escoja el proceso con la solicitud de fibra
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 08-09-2016 Se realizan modificaciones cuando las solicitudes sean SOLICITAR CAMBIO EQUIPO y SOLICITAR RETIRO EQUIPO
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 23-02-2017 - Se agrega nuevo tipo solicitud SOLICITUD AGREGAR EQUIPO para  gestionar solicitudes generadas
     *                           en el proceso de cambio de planes donde el nuevo plan incluya como detalle un producto SMART WIFI
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 14-07-2017 - Se agrega un nuevo proceso y una nueva tarea para la creación de tarea para la instalación del plan netlifecam
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 15-12-2017 - Se agrega un nuevo proceso y una nueva tarea para la creación de tarea para instalacion de cableado estructura
     *                           para servicios de Internet de DATACENTER
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 22-11-2017 - Se agrega validación para obtener el proceso y la tarea de la última milla FFTx
     * 
     * @since 1.2
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 22-01-2018 - Se agrega programación para recuperar tareas de solicitudes de reubicación 
     * @since 1.4
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 19-06-2019 Se agrega programación para gestionar solicitudes de cambio de equipo soporte
     * @since 1.5
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.6 23-01-2020 - Se agrega funcionalidad para que devuelva correctamente el proceso
     *                           INSTALACION WIFI ALQUILER DE EQUIPOS para Wifi Alquiler Equipos.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 04-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.8 25-06-2020 - Se agrega el nombre de proceso 'SOLICITUD CABLEADO ESTRUCTURADO' y se consulta si el servicio tiene un servicio 
     *                           relacionado por instalación simultánea para visualizar las tareas del servcio relacionado
     * 
     * @author Andrés Montero H <amontero@telconet.ec>
     * @version 1.9 27-10-2020 - Se pasa la lógica de programación al service PlanificarService.getTareasByProcesoAndTarea().
     */
    public function getTareasByProcesoAndTareaAction() 
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion       = $this->get('request');
        $intServicioId     = $objPeticion->get('servicioId');
        $intIdSolicitud    = $objPeticion->get('id_solicitud');
        $strNombreTarea    = $objPeticion->get('nombreTarea');
        $strEstado         = $objPeticion->get('estado');
        $intStart          = $objPeticion->get('start');
        $intLimit          = $objPeticion->get('limit');
        $strAccion         = $objPeticion->get('accion');
        $strCodEmpresa     = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
        $strPrefijoEmpresa = ($objPeticion->getSession()->get('prefijoEmpresa') ? $objPeticion->getSession()->get('prefijoEmpresa') : "");
        $arrayParametros    = array();     

        $arrayParametros['servicioId'] = $intServicioId;
        $arrayParametros['id_solicitud'] = $intIdSolicitud;
        $arrayParametros['nombreTarea'] = $strNombreTarea;
        $arrayParametros['estado'] = $strEstado;
        $arrayParametros['start'] = $intStart;
        $arrayParametros['limit'] = $intLimit;
        $arrayParametros['accion'] = $strAccion;
        $arrayParametros['idEmpresa'] = $strCodEmpresa;
        $arrayParametros['prefijoEmpresa'] = $strPrefijoEmpresa;

        $objServicePlanificar = $this->get('planificacion.planificar');
        $objJson = $objServicePlanificar->getTareasByProcesoAndTarea($arrayParametros);

        $objRespuesta->setContent($objJson);
        return $objRespuesta;
    }

    /**
      * 
      * getTareasByProcesoAndTareaSinModem
      * consulta las tareas por proceso para solicitudes que no requerieren modem
      * 
      * @author Jesus Bozada <jbozada@telconet.ec>
      * @version 1.1 20-05-2016   Se agrega validacion de planId del servicio
      * 
      * @since 1.0
    */
    public function getTareasByProcesoAndTareaSinModemAction() 
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion           = $this->get('request');
        $servicioId         = $peticion->query->get('servicioId');
        $nombreTarea        = $peticion->query->get('nombreTarea');
        $estado             = $peticion->query->get('estado');
        $start              = $peticion->query->get('start');
        $limit              = $peticion->query->get('limit');
        $codEmpresa         = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $prefijoEmpresa     = ($peticion->getSession()->get('prefijoEmpresa') ? $peticion->getSession()->get('prefijoEmpresa') : "");
        $em                 = $this->getDoctrine()->getManager("telconet");
        $em_soporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf             = $this->getDoctrine()->getManager("telconet_naf");
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $codEmpresaTmp      = "";
        $InfoServicio       = $em->getRepository('schemaBundle:InfoServicio')->find($servicioId);
        if($InfoServicio->getPlanId())
        {
            $nombrePlan = $InfoServicio->getPlanId()->getNombrePlan();
        }
        else
        {
            $nombrePlan = $InfoServicio->getProductoId()->getDescripcionProducto();
        }

        $infoServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($servicioId);
        $TipoMedio           = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($infoServicioTecnico->getUltimaMillaId());
        $codEmpresaTmp       = $em->getRepository('schemaBundle:AdmiParametroDet')->getEmpresaEquivalente($servicioId, $prefijoEmpresa);
        
        if ($codEmpresaTmp)
        {
            $codEmpresa = $codEmpresaTmp['id'];
        }
        if ($TipoMedio->getNombreTipoMedio() == "Cobre") 
        {
            if (strrpos($nombrePlan, "ADSL"))
            {
                $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
            }
            else
            {
                $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
            }
        }
        else if ($TipoMedio->getNombreTipoMedio() == "Fibra Optica")
        {
            $nombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
        }
        else 
        {
            $nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";
        }

        $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);

        $objJson = $this->getDoctrine()
                        ->getManager("telconet_soporte")
                        ->getRepository('schemaBundle:AdmiTarea')
                        ->generarJsonTareasConMaterialesByProcesoSinModem($em, $start, $limit, $estado, $entityProceso->getId(), $codEmpresa);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /*
	 * Llena el gridFactibilidadMateriales de consulta.
	 */
    /**
	* @Secure(roles="ROLE_135-89")
	*/
    public function gridFactibilidadMaterialesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id_solicitud = $peticion->query->get('id_detalle_solicitud');        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        
		$InfoDetalleSolicitud=$em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id_solicitud);
		$InfoServicio=$em->getRepository('schemaBundle:InfoServicio')->findOneById($InfoDetalleSolicitud->getServicioId());
		$TipoMedio = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($InfoServicio->getUltimaMillaId());
		
		if($TipoMedio->getNombreTipoMedio()=="Cobre")
			$nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE";
		else	
			$nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleSolMaterial')
            ->generarJsonFactibilidadLikeProceso($em, $em_naf, $start, $limit, $id_solicitud, $nombreProceso, $codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function gridMaterialesByTareaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $idSolicitud = $peticion->query->get('id_detalle_solicitud');
        $idTarea = $peticion->query->get('idTarea');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleSolMaterial')
            ->generarJsonMaterialesByTarea($em, $em_naf, $start, $limit,$idSolicitud, $idTarea, $codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
	* @Secure(roles="ROLE_135-90")
	*/
    public function getMotivosRechazoAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
		//cambiar ... no es accionId = 1 sino el de getMotivos
        $entitySeguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>135, "accionId"=>90));
		$relacionSistemaId = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;     
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiMotivo')
            ->generarJson("","Activo",$start,$limit, $relacionSistemaId);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
	* @Secure(roles="ROLE_135-91")
	*/
    public function ajaxComboTiposMedioAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->get('nombre');
        
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entityTipoMedio = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->generarJsonComboTiposMedios($nombre);
	
	$respuesta->setContent($entityTipoMedio);
        
        return $respuesta;
    }    
    
    /**
     * @Secure(roles="ROLE_135-91")
     * 
     * ajaxComboElementos, Obtiene elementos de acuerdo a los parametros enviados para la busqueda
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 20-05-2016   Se agrega parametro tipoElementoRed para poder obtener
     *                           elementos que tengan caracterisita TIPO ELEMENTO RED con 
     *                           valor BACKBONE
     * 
     * @since 1.0
     * 
     */
    public function ajaxComboElementosAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion           = $this->get('request');
        $nombre             = $peticion->get('nombre');
        $strTipoElementoRed = $peticion->get('tipoElementoRed');
        $elemento           = $peticion->get('elemento');
        $modelo             = $peticion->get('modelo');
        $start              = $peticion->get('start');
        $limit              = $peticion->get('limit');
        
        //rsaenz -- CAMBIARLO AUTOMATICO....22 -- POP
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entityTipoElemento = $em_infraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento($elemento);
	$tipoElemento       = $entityTipoElemento->getId();       
        
        $objJson = $this->getDoctrine()
			->getManager("telconet_infraestructura")
			->getRepository('schemaBundle:InfoElemento')
			->generarJsonComboElementosByNombreEstadoTipoModelo($nombre,"Activo", $tipoElemento,$modelo, $strTipoElementoRed);
            
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }    
    
    /**
     * ajaxComboCajas, Obtiene el nombre de las cajas para ser mostradas en el combo de ingresar factibilidad.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 05-05-2016 Se modifica el metodo para que obtenga las cajas dentro de un edificio cuando se lo requiera.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.2 24-11-2017 Se realiza ajuste cuando la ultima milla es FTTx se recupera de sesion la informacion con el objetivo de seguir el 
     * flujo de Megadatos.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 29-07-2018 Se agregan validaciones por revisión de proceso en proyecto ZTE
     * @since 1.2
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 05-12-2018 Se agregan validaciones por gestion de proyectos de la empresa TNP
     * @since 1.3
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.5 30-12-2021 Se agrega nombre de tipo de elemento Manga
     * @since 1.4
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec> 
     * @version 1.6 13-02-2023 - Se agrega bandera de Prefijo Empresa EN para Ecuanet y así realizar bypass para obtener los elementos de MD.
     * @since 1.6
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_135-91")
     */
    public function ajaxComboCajasAction()
    {
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objRequest         = $this->getRequest();
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strNombreCaja      = $objRequest->get('query');
        $intIdPunto         = $objRequest->get('intIdPunto');
        $strEmpresaCod      = ($objRequest->getSession()->get('idEmpresa') ? $objRequest->getSession()->get('idEmpresa') : "");
        $strEsFttx          = $objRequest->get('esFttx') ? $objRequest->get('esFttx') : "NO" ;
        //se obtiene el prefijo de la empresa para realizar bypass para obtener los elementos de MD.
        $strPrefijoEmpresa  = $objRequest->getSession()->get('prefijoEmpresa');
        $strEmpresaCod      = ($strPrefijoEmpresa === "EN" ? $strEmpresaCod = 18 : $strEmpresaCod);
        
        if($strEsFttx === "SI" && $strEmpresaCod != '26')
        {
            $strEmpresaCod = 18;
        }

        $arrayParametros                                                                = array();
        $arrayParametros['arrayEstadoElemento']['arrayEstado']                          = ['Activo'];
        $arrayParametros['arrayEstadoDetalleElemento']['arrayEstado']                   = ['Activo'];
        $arrayParametros['arrayEstadoEmpresaElemento']['arrayEstado']                   = ['Activo'];
        $arrayParametros['arrayNombreTipoElemento']['strComparador']                    = 'IN';
        $arrayParametros['arrayNombreTipoElemento']['arrayNombreTipoElemento']          = ['MANGA','CAJA DISPERSION'];
        $arrayParametros['arrayEmpresaCod']['arrayEmpresaCod']                          = [$strEmpresaCod];
        $arrayParametros['arrayDetalleNombreElemento']['arrayDetalleNombreElemento']    = ['NIVEL'];
        $arrayParametros['arrayDetalleValorElemento']['arrayDetalleValorElemento']      = ['2'];
        $arrayParametros['arrayNombreElemento']['strComparador']                        = 'LIKE';
        $arrayParametros['arrayNombreElemento']['arrayNombreElemento']                  = ['%' . strtoupper($strNombreCaja) . '%'];
        
        //Busca en InfoPuntoDatoAdicional para saber si pertenece a un edificio.
        $entityInfoPuntoDatoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($intIdPunto);
        
        //Entra si econtro el registro
        if($entityInfoPuntoDatoAdicional)
        {
            //Pregunsta si pertenece a edificio
            if("S" === $entityInfoPuntoDatoAdicional->getDependeDeEdificio())
            {
                //Setea los parametros si encuentra un elemento relacionado.
                if($entityInfoPuntoDatoAdicional->getElementoId())
                {
                    $arrayParametros['arrayElemento']['arrayElemento'] = [$entityInfoPuntoDatoAdicional->getElementoId()->getId()];
                }
            }
        }

        $objJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getJSONElementoByNombreEmpresaEdificio($arrayParametros);

        $objResponse->setContent($objJson);

        return $objResponse;
    } //ajaxComboCajasAction

    /**
     * ajaxGetInfoCajaAction, obtiene la informacion de la caja
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 17-04-2016 Se modifica el metodo que retorna la informacion de la caja
     * @since 1.0
     * 
     * @Secure(roles="ROLE_135-91")
     */
    public function ajaxGetInfoCajaAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objRequest = $this->get('request');
        $emGeneral  = $this->getDoctrine()->getManager("telconet_general");
        $strTipoRed = $objRequest->get('strTipoRed') ? $objRequest->get('strTipoRed') : "MPLS";

        //se valida si el tipo de red es GPON
        $booleanTipoRedGpon = false;
        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('NUEVA_RED_GPON_TN',
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
        $arrayParametros    = array('intIdElementoContenedor'   => $objRequest->get('intIdElementoContenedor'),
                                    'intIdElementoDistribucion' => $objRequest->get('intIdElementoDistribucion'),
                                    'strTipoBusqueda'           => $objRequest->get('strTipoBusqueda'),
                                    'booleanTipoRedGpon'        => $booleanTipoRedGpon,
                                    'strNombreElementoPadre'    => strtoupper($objRequest->get('strNombreElementoPadre')));

        $objJson            = $this->getDoctrine()
                                   ->getManager("telconet_infraestructura")
                                   ->getRepository('schemaBundle:InfoElemento')
                                   ->generarJsonInfoCajaByIdCajaOrIdElementoDistribuidor($arrayParametros);

        $objResponse->setContent($objJson);

        return $objResponse;
    } //ajaxGetInfoCajaAction

    /**
     * ajaxComboElementosByPadreAction, obtiene la informacion de los elementos de un elemento padre
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-01-2017 Se agrega validación adicional para servicios Internet Small Business
     * @since 1.0
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 14-06-2019 - Se agrega funcionalidad para soportar el estado "Restringido".
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * version 1.3 19-06-2019 - Se modifica variable 
     * 
     * @Secure(roles="ROLE_135-92")
     */
    public function ajaxComboElementosByPadreAction()
    {       
        $objResponse        = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdPop           = $objRequest->get('popId');
        $strTipoElemento    = $objRequest->get('elemento');
        $strNombre          = $objRequest->get('nombre');
        $intIdServicio      = $objRequest->get('idServicio') ? $objRequest->get('idServicio'): null;
        $strEsFttx          = $objRequest->get('esFttx') ? $objRequest->get('esFttx') : "NO" ;
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intIdTipoElemento  = 0;
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objInfoServicioService = $this->get('comercial.infoservicio');
        
        $objTipoElemento    = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento($strTipoElemento);
        if(is_object($objTipoElemento))
        {
            $intIdTipoElemento = $objTipoElemento->getId();
        }
        if($strEsFttx === "SI")
        {
            $strJson    = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                            ->getJsonSplittersEnCajaISB(array(  "intIdElementoCaja" => $intIdPop,
                                                                                "intIdTipoElemento" => $intIdTipoElemento,
                                                                                "strEstado"         => "'Activo', 'Restringido'"));
        }
        else
        {
            if(isset($intIdServicio))
            {
                $strJson = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                    ->generarJsonComboElmentosByPadre($strNombre, $intIdTipoElemento, $intIdPop, "'Activo', 'Restringido'");
                $strJson = $objInfoServicioService->filtrarRestringidosArray($strJson, $strPrefijoEmpresa, $intIdServicio);
            }else
            {
                $strJson = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                    ->generarJsonComboElmentosByPadre($strNombre, $intIdTipoElemento, $intIdPop, "'Activo'");
            }
        }

        $objResponse->setContent($strJson);
        return $objResponse;

    }

    /**
	* @Secure(roles="ROLE_135-93")
	*/
    public function ajaxDisponibilidadElementoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $idElemento = $peticion->get('idElemento');
        
        $resultCont = $this->getDoctrine()
			    ->getManager("telconet_infraestructura")
			    ->getRepository('schemaBundle:InfoInterfaceElemento')
			    ->getDisponibilidadElemento($idElemento);
        
        $cont = ($resultCont && count($resultCont)>0) ? $resultCont["cont"] : 0 ;        
        $respuesta->setContent($cont);
        return $respuesta;
    }
    
    /**
	* @Secure(roles="ROLE_135-94")
    * 
    * Documentación para el método 'rechazarAjaxAction'.
    *
    * Rechaza solicitudes de factibilidad via Ajax.
    * @return response con mensaje de respuesta.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 19-12-2014
    * 
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 13-09-2016 Se modifica la forma de enviar el correo a través de plantillas
    *
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 21-09-2016 Se modifica como destinatario al vendedor del punto por el vendedor del servicio
    * 
    * @author John Vera R <javera@telconet.ec>
    * @version 1.3 15-08-2017 se procede a validar que cuando sea un concentrador verifique si tiene extremos
    * 
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.4 11-09-2017 se agrega programación para procesar rechazo de servicios um RADIO TN que generaron factibilidad anticipada
    * @since 1.3
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 04-10-2017 Se agrega código para eliminar caracteristica SERVICIO_MISMA_ULTIMA_MILLA de todos los servicios que dependan
     *                         del servicio rechazado     
     * @since 1.4
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 27-11-2018 Se agregan validaciones para gestionar productos de la empresa TNP    
     * @since 1.5 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 28-02-2019 Se agregan validaciones para servicios con tipo de orden traslados MD, se rechazaran todos 
     *                         los servicios adicionales en el destino del traslado y se activan nuevamente los servicios
     *                         adicionales en el origen del traslado
     * @since 1.6
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 19-06-2019 Se modifica el nombre del método utilizado para recrear las solicitudes de Agregar Equipo y de
     *                         de Cambio de equipo por soporte
     * @since 1.7
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 13-11-2020 Se agrega la recreación de solicitud del servicio origen W+AP cuando se rechaza la solicitud de factibilidad
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 10-05-2021 Se modifican los parámetros enviados a la función liberarInterfaceSplitter
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.20 06-04-2021
     *          - Flujo para Rechazar solicitud de factibilidad para equipo Extender Dual Band
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.20 27-04-2021 Se elimina la recreación de solicitudes del extender al anular el servicio de Internet, ya que ahora se lo hará desde
     *                          la gestión simultánea y se agrega la recreación de la solicitud de cambio de ont
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.21 03-01-2022 Se elimina envío de parámetro strValorCaractTipoOntNuevo a la función recreaSolicitudCambioOntTraslado por cambio 
     *                          en dicha función para permitir Extender para ZTE
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 2.22 25-10-2022 Se rechazan servicios adicionales con estado Pendiente si vienen por proceso de reingreso
     */
    public function rechazarAjaxAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion       = $this->get('request');
        $id             = $peticion->get('id');
        $id_motivo      = $peticion->get('id_motivo');
        $observacion    = $peticion->get('observacion');
        $session        = $peticion->getSession();
        $idEmpresa      = $session->get('idEmpresa');
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
        
        $em                 = $this->getDoctrine()->getManager();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");   
        $em_infraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($id);
        $entityMotivo           = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($id_motivo);

        $serviceInfoServicioComercial   = $this->get('comercial.InfoServicio');
        $strRechazoFactibInternetMd     = "";
        $em->getConnection()->beginTransaction();
        $em_infraestructura->getConnection()->beginTransaction();
        try
        {
            if($entityDetalleSolicitud)
            {        
                $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());                                
                $entityServicio->setEstado("Rechazada");
                $em->persist($entityServicio);
                $em->flush();   
                
                $strTipoOrden = $entityServicio->getTipoOrden();

                //liberacion de ptos para MD
                if(($prefijoEmpresa=="MD" || $prefijoEmpresa == "TNP") && is_object($entityServicio->getPlanId()))
                {
                    $strRechazoFactibInternetMd = "SI";
                    /* @var $serviceInterfaceElemento InfoInterfaceElementoService */
                    $serviceInterfaceElemento       = $this->get('tecnico.InfoInterfaceElemento');
                    $arrayRespuestaLiberaSplitter   = $serviceInterfaceElemento->liberarInterfaceSplitter(
                                                            array(  "objServicio"           => $entityServicio,
                                                                    "strUsrCreacion"        => $peticion->getSession()->get('user'),
                                                                    "strIpCreacion"         => $peticion->getClientIp(),
                                                                    "strProcesoLibera"      => " por rechazo del servicio"));
                    $strStatusLiberaSplitter        = $arrayRespuestaLiberaSplitter["status"];
                    $strMensajeLiberaSplitter       = $arrayRespuestaLiberaSplitter["mensaje"];
                    if($strStatusLiberaSplitter === "ERROR")
                    {
                        $em->getConnection()->rollback();
                        $respuesta->setContent($strMensajeLiberaSplitter);
                        return $respuesta;
                    }
                }

                if($prefijoEmpresa  ==  "TN")
                {
                    $serviceTecnico->eliminarDependenciaMismaUM($entityServicio, 
                                                                $peticion->getSession()->get('user'),
                                                                $peticion->getClientIp());
                }

                if($prefijoEmpresa  ==  "TN" && $entityServicio->getProductoId()->getEsConcentrador() == 'SI')
                {
                    $arrayParametros['intIdServicio'] = $entityServicio->getId();
                    $arrayResult = $serviceTecnico->getServiciosPorConcentrador($arrayParametros);

                    if($arrayResult['strMensaje'])
                    {
                        if($arrayResult['strStatus'] == 'OK')
                        {
                            $respuesta->setContent('<b>No se puede Eliminar el servicio concentrador, debido a que tiene extremos '
                                                   . 'enlazados:</b> <br><br>'.$arrayResult['strMensaje']);
                        }
                        else
                        {
                            $respuesta->setContent($arrayResult['strMensaje']);
                        }
                        return $respuesta;
                    }
                }     

                //GUARDAR INFO SERVICIO HISTORIAL
                $entityServicioHistorial = new InfoServicioHistorial();  
                $entityServicioHistorial->setServicioId($entityServicio);	
                $entityServicioHistorial->setMotivoId($id_motivo);
                $entityServicioHistorial->setObservacion($observacion);	
                $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
                $entityServicioHistorial->setEstado('Rechazada'); 
                $em->persist($entityServicioHistorial);
                $em->flush();  

                $entityDetalleSolicitud->setMotivoId($id_motivo);
                $entityDetalleSolicitud->setObservacion($observacion);	
                $entityDetalleSolicitud->setEstado("Rechazada");
                $entityDetalleSolicitud->setUsrRechazo($peticion->getSession()->get('user'));		
                $entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                $em->persist($entityDetalleSolicitud);
                $em->flush();               

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setObservacion($observacion);
                $entityDetalleSolHist->setMotivoId($id_motivo);            
                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $entityDetalleSolHist->setEstado('Rechazada');
                $em->persist($entityDetalleSolHist);
                $em->flush();
                
                if ($prefijoEmpresa == "MD" && $strTipoOrden == "T")
                {
                    if(is_object($entityServicio->getPlanId()))
                    {
                        $strValorCaractMotivoCambioOnt      = "CAMBIO ONT POR AGREGAR EXTENDER";
                        $arrayRespuestaRecreaSolCambioOnt   = $serviceInfoServicioComercial->recreaSolicitudCambioOntTraslado(
                                                                                        array(  "objServicioPlanDestinoEnPunto" => $entityServicio,
                                                                                                "strCodEmpresa"                 => $idEmpresa,
                                                                                                "strUsrCreacion"                => 
                                                                                                $peticion->getSession()->get('user'),
                                                                                                "strIpCreacion"                 => 
                                                                                                $peticion->getClientIp(),
                                                                                                "strValorCaractMotivoCambioOnt" => 
                                                                                                $strValorCaractMotivoCambioOnt));
                        if($arrayRespuestaRecreaSolCambioOnt["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaRecreaSolCambioOnt["mensaje"]);
                        }
                    }
                    
                    /* Para servicios con tipo de orden traslados se debe validar si es que existe alguna solicitud
                     * de agregar equipo pendiente en el nuevo punto y de existir, se debe proceder a crearla nuevamente 
                     * en el punto origen del traslado en estado PrePlanificada
                     */
                    $arrayParametrosTrasladoSol = array();
                    $arrayParametrosTrasladoSol["objServicio"]    = $entityServicio;
                    $arrayParametrosTrasladoSol["strUsrCreacion"] = $peticion->getSession()->get('user');
                    $arrayParametrosTrasladoSol["strIpCreacion"]  = $peticion->getClientIp();
                    $arrayParametrosTrasladoSol["strEmpresaCod"]  = $idEmpresa;
                    $serviceInfoServicioComercial->recrearSolicitudesPorTraslado($arrayParametrosTrasladoSol);

                    $objProductoInternet = $em->getRepository('schemaBundle:AdmiProducto')
                                              ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                                "empresaCod"    => $idEmpresa, 
                                                                "estado"        => "Activo"));

                    /*
                     * se regulariza caracteristica TRASLADO en servicios adicionales a rechazar y
                     * en caso de que el servicio se encuentre Trasladado pase nuevamente a estado Activo
                     */
                    if (is_object($objProductoInternet))
                    {
                        $objSpcTraslado = $serviceTecnico->getServicioProductoCaracteristica($entityServicio, "TRASLADO", $objProductoInternet);
                        if (is_object($objSpcTraslado))
                        {
                            $strValorAntesCorreo  = $objSpcTraslado->getValor();
                            $strEstadoAntesCorreo = $objSpcTraslado->getEstado();
                            $objSpcTraslado->setValor('');
                            $objSpcTraslado->setEstado('Eliminado');
                            $objSpcTraslado->setFeUltMod(new \DateTime('now'));
                            $objSpcTraslado->setUsrUltMod($session->get('user'));
                            $em->persist($objSpcTraslado);
                            $em->flush();

                            //REGISTRA EN LA TABLA DE HISTORIAL
                            $entityServicioHistorial = new InfoServicioHistorial();
                            $entityServicioHistorial->setServicioId($entityServicio);
                            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServicioHistorial->setUsrCreacion($session->get('user'));
                            $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                            $entityServicioHistorial->setObservacion('Se actualizó característica TRASLADO con ID '.
                                                                     $objSpcTraslado->getId().' : <br>'.
                                                                     'Valores Anteriores: <br>'.  
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                     'Valores Actuales: <br>'.  
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                            $entityServicioHistorial->setAccion('actualizaCaracteristica');
                            $entityServicioHistorial->setEstado($entityServicio->getEstado());
                            $em->persist($entityServicioHistorial);
                            $em->flush();

                            $objServicioOrigenTraslado = $em->getRepository('schemaBundle:InfoServicio')->find($strValorAntesCorreo);
                            if (is_object($objServicioOrigenTraslado) && $objServicioOrigenTraslado->getEstado() == "Trasladado")
                            {
                                $objServicioOrigenTraslado->setEstado("Activo");
                                $em->persist($objServicioOrigenTraslado);
                                $em->flush();

                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objHistorialServicioAdicional = new InfoServicioHistorial();
                                $objHistorialServicioAdicional->setServicioId($objServicioOrigenTraslado);
                                $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                $objHistorialServicioAdicional->setObservacion("Se reactiva servicio por rechazo de Traslado del servicio en el punto :".
                                                                               $entityServicio->getPuntoId()->getLogin());
                                $objHistorialServicioAdicional->setMotivoId($id_motivo);
                                $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                $objHistorialServicioAdicional->setEstado('Activo');
                                $em->persist($objHistorialServicioAdicional);
                                $em->flush();
                            }
                        }
                    }
                    if($strRechazoFactibInternetMd === "SI")
                    {
                        //si el tipo de orden es traslado, los servicios adicionales tb son rechazados automaticamente
                        $arrayServiciosAdicionalesPunto = $em->getRepository('schemaBundle:InfoServicio')
                                                             ->findBy(array("puntoId" => $entityServicio->getPuntoId()));
                        $arrayEstadosServicios = array('Rechazado','Rechazada','Anulado','Anulada','Eliminado',
                                                       'Eliminada','Cancel','Cancelado','Cancelada');
                        foreach ($arrayServiciosAdicionalesPunto as $objServicioAdicional)
                        {
                            if (!in_array($objServicioAdicional->getEstado(),$arrayEstadosServicios))
                            {
                                if(is_object($objServicioAdicional->getProductoId()) 
                                   && ($objServicioAdicional->getProductoId()->getNombreTecnico() === "WDB_Y_EDB"))
                                {
                                    $arrayRespuestaRecreaSolWyAP = $serviceInfoServicioComercial->recreaSolicitudWyApTraslado(
                                                                            array(  "objServicioDestino"    => $objServicioAdicional,
                                                                                    "strOpcion"             => "TRASLADO",
                                                                                    "strCodEmpresa"         => $idEmpresa,
                                                                                    "strUsrCreacion"        => $peticion->getSession()->get('user'),
                                                                                    "strIpCreacion"         => $peticion->getClientIp()));
                                    if($arrayRespuestaRecreaSolWyAP["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaRecreaSolWyAP["mensaje"]);
                                    }
                                }
                                else if(is_object($objServicioAdicional->getProductoId()) 
                                  && ($objServicioAdicional->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND"))
                                {
                                    $arrayRespuestaRecreaSolEDB = $serviceInfoServicioComercial->recreaSolicitudEdbTraslado(
                                                                                                    array(  "objServicioDestino"    => 
                                                                                                            $objServicioAdicional,
                                                                                                            "strOpcion"             => "TRASLADO",
                                                                                                            "strCodEmpresa"         => $idEmpresa,
                                                                                                            "strUsrCreacion"        => 
                                                                                                            $peticion->getSession()->get('user'),
                                                                                                            "strIpCreacion"         => 
                                                                                                            $peticion->getClientIp()));
                                    if($arrayRespuestaRecreaSolEDB["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaRecreaSolEDB["mensaje"]);
                                    }
                                }
                                $objServicioAdicional->setEstado("Rechazada");
                                $em->persist($objServicioAdicional);
                                $em->flush();

                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objHistorialServicioAdicional = new InfoServicioHistorial();
                                $objHistorialServicioAdicional->setServicioId($objServicioAdicional);
                                $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                $objHistorialServicioAdicional->setObservacion($observacion);
                                $objHistorialServicioAdicional->setMotivoId($id_motivo);
                                $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                $objHistorialServicioAdicional->setEstado('Rechazada');
                                $em->persist($objHistorialServicioAdicional);
                                $em->flush();

                                //se regulariza caracteristica TRASLADO en servicios adicionales a rechazar
                                if (is_object($objProductoInternet))
                                {
                                    $objSpcTrasladoAdic = $serviceTecnico->getServicioProductoCaracteristica($objServicioAdicional, 
                                                                                                             "TRASLADO", 
                                                                                                             $objProductoInternet);
                                    if (is_object($objSpcTrasladoAdic))
                                    {
                                        $strValorAntesCorreo  = $objSpcTrasladoAdic->getValor();
                                        $strEstadoAntesCorreo = $objSpcTrasladoAdic->getEstado();
                                        $objSpcTrasladoAdic->setValor('');
                                        $objSpcTrasladoAdic->setEstado('Eliminado');
                                        $objSpcTrasladoAdic->setFeUltMod(new \DateTime('now'));
                                        $objSpcTrasladoAdic->setUsrUltMod($session->get('user'));
                                        $em->persist($objSpcTrasladoAdic);
                                        $em->flush();

                                        //REGISTRA EN LA TABLA DE HISTORIAL
                                        $entityServicioHistorial = new InfoServicioHistorial();
                                        $entityServicioHistorial->setServicioId($objServicioAdicional);
                                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                        $entityServicioHistorial->setUsrCreacion($session->get('user'));
                                        $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                                        $entityServicioHistorial->setObservacion('Se actualizó característica TRASLADO con ID '.
                                                                                 $objSpcTrasladoAdic->getId().' : <br>'.
                                                                                 'Valores Anteriores: <br>'.  
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                                 'Valores Actuales: <br>'.  
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                                        $entityServicioHistorial->setAccion('actualizaCaracteristica');
                                        $entityServicioHistorial->setEstado($objServicioAdicional->getEstado());
                                        $em->persist($entityServicioHistorial);
                                        $em->flush();

                                        //reactivar servicio en origen de traslado en caso de tener estado TRASLADADO
                                        $objServicioOrigenTraslado = $em->getRepository('schemaBundle:InfoServicio')->find($strValorAntesCorreo);
                                        if (is_object($objServicioOrigenTraslado) && $objServicioOrigenTraslado->getEstado() == "Trasladado")
                                        {
                                            $objServicioOrigenTraslado->setEstado("Activo");
                                            $em->persist($objServicioOrigenTraslado);
                                            $em->flush();

                                            //GUARDAR INFO SERVICIO HISTORIAL
                                            $objHistorialServicioAdicional = new InfoServicioHistorial();
                                            $objHistorialServicioAdicional->setServicioId($objServicioOrigenTraslado);
                                            $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                            $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                            $objHistorialServicioAdicional->setObservacion("Se reactiva servicio por rechazo de ".
                                                                                           "Traslado del servicio en el punto: ".
                                                                                           $entityServicio->getPuntoId()->getLogin());
                                            $objHistorialServicioAdicional->setMotivoId($id_motivo);
                                            $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                            $objHistorialServicioAdicional->setEstado('Activo');
                                            $em->persist($objHistorialServicioAdicional);
                                            $em->flush();
                                        }
                                    }
                                }
                                ///Se eliminan las características asociadas al servicio de Internet del punto origen
                                $arraySpcServicioAdi = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                          ->findBy(array( "servicioId"    => $objServicioAdicional->getId(),
                                                                          "estado"        => "Activo"));
                                foreach($arraySpcServicioAdi as $objSpcServicioAdi)
                                {
                                    $objSpcServicioAdi->setEstado('Eliminado');
                                    $objSpcServicioAdi->setUsrUltMod($session->get('user'));
                                    $objSpcServicioAdi->setFeUltMod(new \DateTime('now'));
                                    $em->persist($objSpcServicioAdi);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }

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
                        $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
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
                                    $objInterfaceElementoSwAnt->setUsrUltMod($peticion->getSession()->get('user'));
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

                //VERIFICA Y RECHAZA TODOS LOS SERVICIOS ADICIONALES REINGRESADOS, CON ESTADO PENDIENTE
                if ($prefijoEmpresa == "MD") 
                {
                    //RECUPERO CARACTERISTICA REINGRESO
                    $objCarReingreso = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(array('descripcionCaracteristica' => "ID_SERVICIO_REINGRESO",
                                                            'estado'                  => "Activo"));

                    //CONSULTO SERVICIOS POR PUNTO
                    $arrayServiciosAdicionalesPunto = $em->getRepository('schemaBundle:InfoServicio')
                                                    ->findBy(array("puntoId" => $entityServicio->getPuntoId()));

                    foreach ($arrayServiciosAdicionalesPunto as $objServicioAdicional) 
                    {
                        if ($objServicioAdicional->getEstado() == "Pendiente" && is_object($objServicioAdicional->getProductoId()))
                        {
                            //VALIDA QUE POSEE CARACTERISTICA DE REINGRESO
                            $objInfoServCaracReingreso   = $em->getRepository("schemaBundle:InfoServicioCaracteristica")
                                                            ->findOneBy(array("servicioId"       => $objServicioAdicional->getId(),
                                                                                "caracteristicaId" => $objCarReingreso));

                            if($objInfoServCaracReingreso)
                            {
                                //RECHAZA SERVICIO
                                $objServicioAdicional->setEstado("Rechazada");
                                $em->persist($objServicioAdicional);
                                $em->flush();

                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objHistorialServicioAdicional = new InfoServicioHistorial();
                                $objHistorialServicioAdicional->setServicioId($objServicioAdicional);
                                $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                $objHistorialServicioAdicional->setObservacion($observacion);
                                $objHistorialServicioAdicional->setMotivoId($id_motivo);
                                $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                $objHistorialServicioAdicional->setEstado('Rechazada');
                                $em->persist($objHistorialServicioAdicional);
                                $em->flush();

                                //CONSULTA Y ELIMINA CARACTERISTICA DEL PRODUCTO
                                $objInfoServicioProdCaract = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findBy(array("servicioId" => $objServicioAdicional->getId(),
                                                                                "estado"    => "Activo"));

                                foreach($objInfoServicioProdCaract as $servpc)
                                {
                                    $servpc->setEstado('Eliminado');
                                    $em->persist($servpc);
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
                                                        $idEmpresa,
                                                        '',
                                                        '',
                                                        null, 
                                                        true,
                                                        'notificaciones_telcos@telconet.ec');

                $respuesta->setContent("Se rechazo la Solicitud de Factibilidad");                                               				                    
            }
            else
            {
                $respuesta->setContent("No existe el detalle de solicitud");
            }

            $em->getConnection()->commit();
            $em_infraestructura->getConnection()->commit();
                
        }
        catch(\Exception $e)
        {
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
            $respuesta->setContent($mensajeError);
	}
        
        $em->getConnection()->close();
        $em_infraestructura->getConnection()->close();
	return $respuesta;
    }	    
    
    /**
    *@Secure(roles="ROLE_135-95")
    * 
    * Documentación para el método 'ajaxGuardaFactibilidadAction'.
    *
    * Guarda la factibilidad via Ajax.
    * @return Response $respuesta con mensaje de respuesta.
    *
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.1 21-09-2016 Se envía correo usando plantillas al guardar la factibilidad 
	*/
    public function ajaxGuardaFactibilidadAction()
    {
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion               = $this->get('request');
        $strIpClient            = $peticion->getClientIp();
        $esTercerizada          = $peticion->get('esTercerizada');
        $idTercerizadora        = $peticion->get('tercerizadora');
        $id                     = $peticion->get('id');
        $elemento_id            = $peticion->get('elemento_id') ? $peticion->get('elemento_id') : 0;
        $um_id                  = $peticion->get('um_id') ? $peticion->get('um_id') : 0;
        
        $session                = $peticion->getSession();
        $idEmpresa              = $session->get('idEmpresa');
        $prefijoEmpresa         = $session->get('prefijoEmpresa');
        $strUsrCreacion         = $session->get('user');

        $em                     = $this->getDoctrine()->getManager();
        $em_comunicacion        = $this->getDoctrine()->getManager("telconet_comunicacion");
        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
        $serviceUtil            = $this->get('schema.Util');

        $em->getConnection()->beginTransaction();
        $em_comunicacion->getConnection()->beginTransaction();
		
        try
        {
            if($entityDetalleSolicitud)
            {         
                //GUARDA INFO SERVICIO
                $entityServicio             =$em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());

                if($um_id && $um_id > 0) 
                {
                    $entityServicioTecnico=$em->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findOneByServicioId($entityDetalleSolicitud->getServicioId());
                    $entityServicioTecnico->setUltimaMillaId($um_id);
                    $em->persist($entityServicioTecnico);
                    $em->flush();
                }

                if($elemento_id != 0 && $elemento_id) 
                {
                    $entityServicio->setEstado("Factible");
                    $em->persist($entityServicio);
                    $em->flush();

                    $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($entityServicio->getId());
                    $entityServicioTecnico->setElementoId($elemento_id);
                    if($esTercerizada=='S')
                    {
                        $entityServicioTecnico->setTercerizadoraId($idTercerizadora);
                    }
                    $em->persist($entityServicioTecnico);
                    $em->flush();

                    //GUARDAR INFO SERVICIO HISTORIAL
                    $entityServicioHistorial = new InfoServicioHistorial();  
                    $entityServicioHistorial->setServicioId($entityServicio);	
                    $entityServicioHistorial->setIpCreacion($strIpClient);
                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $entityServicioHistorial->setUsrCreacion($strUsrCreacion);	
                    $entityServicioHistorial->setEstado('Factible'); 
                    $em->persist($entityServicioHistorial);
                    $em->flush(); 

                    //GUARDA INFO DETALLE SOLICITUD
                    $entityDetalleSolicitud->setEstado("Factible");
                    $em->persist($entityDetalleSolicitud);
                    $em->flush();  

                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);            
                    $entityDetalleSolHist->setIpCreacion($strIpClient);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $entityDetalleSolHist->setEstado('Factible');  
                    $em->persist($entityDetalleSolHist);
                    $em->flush();

                    // ------- COMUNICACIONES --- NOTIFICACIONES 
                    $asunto ="Aprobacion de Solicitud de Factibilidad de Instalacion #".$entityDetalleSolicitud->getId();
                    $to     = array();
                    // DESTINATARIOS....
                    if($entityServicio->getUsrVendedor())
                    {
                        $formasContacto = $em->getRepository('schemaBundle:InfoPersona')
                                             ->getContactosByLoginPersonaAndFormaContacto( $entityServicio->getUsrVendedor(),
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
                    $arrayParametros    = array('detalleSolicitud' => $entityDetalleSolicitud,'usrAprueba'=>$strUsrCreacion);
                    $envioPlantilla     = $this->get('soporte.EnvioPlantilla');
                    $envioPlantilla->generarEnvioPlantilla( $asunto, 
                                                            $to, 
                                                            'APROBAR_FACTIB', 
                                                            $arrayParametros,
                                                            $idEmpresa,
                                                            '',
                                                            '',
                                                            null, 
                                                            true,
                                                            'notificaciones_telcos@telconet.ec');

                    $respuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad");
                }
                else
                {
                    $respuesta->setContent("No escogio un Elemento de la Lista.");
                }
            }
            else
            {
                $respuesta->setContent("No existe el detalle de Solicitud");
            }

            $em->getConnection()->commit();
            $em_comunicacion->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em_comunicacion->getConnection()->rollback();

            $serviceUtil->insertError('Telcos+', 'ajaxGuardaFactibilidadAction', $e->getMessage(), $strUsrCreacion, $strIpClient
            );
            $respuesta->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }

        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_135-2717")
     * 
     * Metodo utilizado para guardar nuevas factibilidades asignadas a servicios
     * @return response
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 23-12-2014
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 17-04-2016 Se modifica el metodo para que guarde la factibilidad para TN
     * @since 1.0
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 23-06-2016 Agregar metraje
     * @since 1.0
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 01-07-2016 se agrego el tipo de factibilidad directa y ruta
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 06-07-2016 correccion de bug guarda elemento fact directa
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 13-07-2016 se agregó la edición de factibilidad directa 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 27-07-2016 se modifica para que cuando exista factibilidad RUTA se elimine las caracteristica en caso de existir para el 
     *                         escenario de edicion de factibilidad
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 27-09-2016 Se modifica la forma de enviar el correo utilizando la plantilla correspondiente para la aprobación de factibilidad
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.7 23-11-2016 Se agrega validación para que no se pueda editar factibilidad de los servicios que tengan la caracteristica 
     *                         SERVICIO_MISMA_ULTIMA_MILLA
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 27-11-2018 Se agregan validaciones para gestionar productos de la empresa TNP    
     * @since 1.7
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 27-02-2019 Se agrega traslado de solicitud de agregar equipo en destino de traslado para que pueda ser procesada
     *                          por proyecto de nuevos planes MD
     * @since 1.8
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 19-06-2019 Se modifica el nombre del método utilizado para clonar las solicitudes de Agregar Equipo y de
     *                          de Cambio de equipo por soporte en ordenes de servicio de tipo traslado
     *                          (antes: trasladarSolicitudAgregarEquipo y ahora: clonarSolicitudesPorTraslado)
     * @since 1.9
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.11 27-07-2020 Se agrega nueva programación que verifica equipos dual band y clona las solicitudes para traslados, se deja de usar
     *                         la función clonarSolicitudesPorTraslado y se usa en su lugar generaSolsPorTraslado
     *      
     * @since 1.10
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.12 15-06-2021 Se agrega validacion de tecnologias en traslados de puntos en empresa MD y tipo de origen T (traslado).
     *                          Se crea la orden de trabajo de los servicios por factibilidad manual para empresa MD y tipo de origen T (traslado)
     * 
     * @since 1.11
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.12 15-06-2021 Se agrega validacion de tecnologias en traslados de puntos en empresa MD y tipo de origen T (traslado).
     *                          Se crea la orden de trabajo de los servicios por factibilidad manual para empresa MD y tipo de origen T (traslado)
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.13 03-03-2023 Se quita validacion de tecnologias en traslados de puntos en empresa MD y tipo de origen T (traslado).
     *                          Se crea solicitud de retiro de equipo para el servicio anterior.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec> 
     * @version 1.14 13-02-2023 - Se agrega bandera de Prefijo Empresa EN para Ecuanet y así obtener la primera interface en estado no connect .
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec> 
     * @version 1.15 06-04-2023 - Se mejora el envio de parametros al obtener la tecnología del olt.
     * 
     * @since 1.2
     * 
    */
    
    public function ajaxGuardaNuevaFactibilidadAction()
    {
        $respuesta                      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion                       = $this->get('request');
        $session                        = $peticion->getSession();
        $idEmpresa                      = $session->get('idEmpresa');
        $strPrefijoEmpresa              = $session->get('prefijoEmpresa');
        $strUsrCreacion                 = $session->get('user');
        $intIdSolFactibilidad           = $peticion->get('intIdSolFactibilidad');
        $intIdElemento                  = $peticion->get('intIdElemento');
        $intIdInterfaceElemento         = $peticion->get('intIdInterfaceElemento');
        $intIdElementoCaja              = $peticion->get('intIdElementoCaja');
        $intElementoPNivel              = $peticion->get('intElementoPNivel');
        $intInterEleDistribucion        = $peticion->get('intInterfaceElementoDistribucion');
        $chbxObraCivil                  = $peticion->get('chbxObraCivil');
        $chbxObservacionRegeneracion    = $peticion->get('chbxObservacionRegeneracion');
        $strObservacionRegeneracion     = $peticion->get('strObservacionRegeneracion');
        $floatMetraje                   = $peticion->get('floatMetraje');
        $strErrorMetraje                = $peticion->get('strErrorMetraje');
        $strNombreTipoElemento          = $peticion->get('strNombreTipoElemento');
        $arrayEstados                   = array('FactibilidadEnProceso','PreFactibilidad','Factible','PrePlanificada','Detenido','Planificada','RePlanificada','AsignadoTarea');
        $em                             = $this->getDoctrine()->getManager();
        $em_infraestructura             = $this->getDoctrine()->getManager('telconet_infraestructura');
        $serviceSolicitudes             = $this->get('comercial.Solicitudes');
        $observacion                    = 'Factibilidad por nuevo Tramo<br>';
        $strMetraje                     = '';
        
        $intElementoDirecto             = $peticion->get('intElementoDirecto');
        $intInterfaceDirecto            = $peticion->get('intInterfaceDirecto');
        $strMetrajeDirecto              = $peticion->get('strMetrajeDirecto');
        $strObservacionDirecto          = $peticion->get('strObservacionDirecto');
        $strTipoBackone                 = $peticion->get('strTipoBackone');
        $strUltimaMilla                 = $peticion->get('strUltimaMilla');        
        $boolGeneraSolsPorTraslado      = false;
        $entityDetalleSolicitud         = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolFactibilidad);
        $em->getConnection()->beginTransaction();
        $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $objInfoServicioService = $this->get('comercial.infoservicio');
        
        try
        {
            if($entityDetalleSolicitud)
            {
                //obtengo al servicio y al servicio tecnico
                $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());                
                                               
                //se agrega codigo para la validacion del estado del servicio y no realizar asignaciones de factibilidad erroneas
                if($entityServicio)
                {
                    $strTipoOrdenServicio = $entityServicio->getTipoOrden();
                    //valido que este servicio no tenga SERVICIO_MISMA_ULTIMA_MILLA
                    $objCaracteristicaUM = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy( array("descripcionCaracteristica" => 'SERVICIO_MISMA_ULTIMA_MILLA',
                                                                                    "estado" => "Activo"));
                    if(is_object($objCaracteristicaUM))
                    {
                        $objProdCaractUM = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                              ->findOneBy( array("caracteristicaId" => $objCaracteristicaUM->getId(),
                                                                 "productoId"       => $entityServicio->getProductoId()));
                        //Si existe es porque se usó misma UM
                        if(is_object($objProdCaractUM))
                        {
                            $objSpcUM = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                  ->findOneBy( array("servicioId"               => $entityServicio->getId(),
                                                                     "productoCaracterisiticaId"=> $objProdCaractUM->getId(),
                                                                     "estado"                   => 'Activo'));
                            if(is_object($objSpcUM))
                            {                            
                                $respuesta->setContent('No es posible editar factibilidad, servicio posee Misma Última Milla.');
                                return $respuesta;
                            }
                        }           
                    }
                    
                    if(in_array($entityServicio->getEstado(), $arrayEstados))
                    {
                        /* @var $serviceInterfaceElemento InfoInterfaceElementoService */
                        $servicioGeneral = $this->get('tecnico.InfoServicioTecnico');
                        
                        if($strTipoBackone == 'DIRECTO' && $strPrefijoEmpresa == 'TN')
                        {
                            $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($entityServicio->getId());
                            $estado = "Factible";
                            $observacionEdicion = "";
                            $objProducto =  $em->getRepository('schemaBundle:AdmiProducto')->find($entityServicio->getProductoId());
                            
                            //Busca la caracteristica metraje factibilidad
                            $entityAdmiCaracteristicaMF = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy( array("descripcionCaracteristica" => 'METRAJE FACTIBILIDAD',
                                                                                                   "estado" => "Activo"));
                            //Si no existe la caracterisitica termina el metodo
                            if(!$entityAdmiCaracteristicaMF)
                            {
                                $respuesta->setContent('No creo la solicitud. <br> Revise que la caracteristica '
                                    . '(METRAJE FACTIBILIDAD) exista.');
                                return $respuesta;
                            }

                            //obtengo datos del ingreso de la factibilidad
                            $entityInfoElemento = $em->getRepository('schemaBundle:InfoElemento')->find($intElementoDirecto);
                            $strMarcaModeloElemento = $entityInfoElemento->getModeloElementoId()->getMarcaElementoId()
                                                                         ->getNombreMarcaElemento();
                            $strNombreTipoElemento = $entityInfoElemento->getModeloElementoId()
                                                                        ->getTipoElementoId()->getNombreTipoElemento();
                            $strNombreElemento = $entityInfoElemento->getNombreElemento();
                            $objInterfaceElemento =  $em->getRepository('schemaBundle:InfoInterfaceElemento')->find($intInterfaceDirecto);
                            $strNombreInterfaceElemento = $objInterfaceElemento->getNombreInterfaceElemento();
                            $objRelacionElemento = $em_infraestructura->getRepository('schemaBundle:InfoRelacionElemento')->findOneByElementoIdB($intElementoDirecto);

                            if($objRelacionElemento)
                            {
                                $objElementoContenedor =  $em->getRepository('schemaBundle:InfoElemento')
                                                             ->find($objRelacionElemento->getElementoIdA());
                                $strNombreElementoContenedor =$objElementoContenedor->getNombreElemento();
                                $intNombreElementoContenedor = $objElementoContenedor->getId();
                                
                            }
                            /*Si el estado del servicio es Factible, PrePlanificada, Planificada, Detenido, RePlanificada, AsignadoTarea significa que
                              el servicio ya entró por un proceso de factibilidad, por lo tanto quiere decir que dicha factibilidad entrará a un 
                              proceso de edición.*/
                            if( $entityServicio->getEstado() == "Factible" or
                                $entityServicio->getEstado() == "PrePlanificada" or
                                $entityServicio->getEstado() == "Planificada" or
                                $entityServicio->getEstado() == "Detenido" or
                                $entityServicio->getEstado() == "RePlanificada" or
                                $entityServicio->getEstado() == "AsignadoTarea" )
                            {
                                $observacionEdicion = "Editado: ";
                                //se procede a eliminar las carcateristicas del servicio                                
                                //METRAJE FACTIBILIDAD
                                $objDetalleSolCaractEdit =  $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                               ->findOneBy(array('detalleSolicitudId' => $intIdSolFactibilidad, 
                                                                                 'caracteristicaId'   => $entityAdmiCaracteristicaMF->getId()));
                                if($objDetalleSolCaractEdit)
                                {
                                    $objDetalleSolCaractEdit->setEstado('Eliminado');
                                    $em->persist($objDetalleSolCaractEdit);
                                    $em->flush();
                                }
                                
                                //TIPO_FACTIBILIDAD
                                $servicioProdCaractTipo = $servicioGeneral->getServicioProductoCaracteristica($entityServicio, 
                                                                                                              "TIPO_FACTIBILIDAD", 
                                                                                                              $objProducto);
                                if($servicioProdCaractTipo)
                                {
                                    $servicioProdCaractTipo->setEstado('Eliminado');
                                    $em->persist($servicioProdCaractTipo);
                                    $em->flush();
                                }
                                
                                if($entityServicioTecnico)     
                                {
                                    //libero la interfaz
                                    if($entityServicioTecnico->getInterfaceElementoId())
                                    {
                                        $objInterfaceElementoEdit =  $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($entityServicioTecnico->getInterfaceElementoId());
                                        //actualizo el puerto
                                        $objInterfaceElementoEdit->setEstado('not connect');
                                        $em->persist($objInterfaceElementoEdit);
                                        $em->flush();
                                    }                                
                                }
                                else
                                {
                                    $respuesta->setContent('El servicio no tiene información técnica.');
                                    return $respuesta;
                                }
                            }

                            $entityAdmiCaractTipoFact = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy( array("descripcionCaracteristica" => 'TIPO_FACTIBILIDAD',
                                                                                                   "estado" => "Activo"));
                            $entityAdmiProdCaractTipoFact = null;
                            if($entityAdmiCaractTipoFact)
                            {
                                $entityAdmiProdCaractTipoFact = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findOneBy( array("caracteristicaId" => $entityAdmiCaractTipoFact->getId(),
                                                                                      "productoId" => $entityServicio->getProductoId()));
                            }
                            //Si no existe la caracterisitica termina el metodo
                            if(!$entityAdmiProdCaractTipoFact)
                            {
                                $respuesta->setContent('No creo el detalle del servicio. <br> Revise que exista la relación producto y '
                                                        . 'caracteristica TIPO_FACTIBILIDAD');
                                return $respuesta;
                            }
                            else                            
                            {
                                $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($entityAdmiProdCaractTipoFact->getId());
                                $infoServProdCaractCapacidad1->setValor($strTipoBackone);
                                $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                $infoServProdCaractCapacidad1->setUsrCreacion($peticion->getSession()->get('user'));
                                $infoServProdCaractCapacidad1->setEstado("Activo");
                                $em->persist($infoServProdCaractCapacidad1);
                                $em->flush();
                            }

                            $strMetraje = '<b>Metraje:</b>' . $strMetrajeDirecto . '<br>';

                            $datosNuevos = "<b>Datos Nuevos:</b><br> "
                                . "<b>" . $strNombreTipoElemento . ":</b></b> " . $strNombreElemento . "<br> "
                                . "<b>Marca:</b> " . $strMarcaModeloElemento . "<br> "
                                . "<b>Puerto:</b> " . $strNombreInterfaceElemento . "<br> "
                                . "<b>Caja:</b> " . $strNombreElementoContenedor . "<br> "
                                . $strMetraje
                                . $strErrorMetraje;

                            //Se guarda por primera vez la factibilidad
                            $observacion .=$datosNuevos;

                            //Permite ingresar la caracteristica metraje factibilidad
                            if(!empty($strMetrajeDirecto))
                            {
                                $arrayInfoDetalleSolicitud = array();
                                $arrayInfoDetalleSolicitud['entityAdmiCaracteristica'] = $entityAdmiCaracteristicaMF;
                                $arrayInfoDetalleSolicitud['strValor'] = $strMetrajeDirecto;
                                $arrayInfoDetalleSolicitud['strEstado'] = 'Activo';
                                $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud'] = $entityDetalleSolicitud;
                                $arrayInfoDetalleSolicitud['strUsrCreacion'] = $peticion->getSession()->get('user');
                                $arrayInfoDetalleSolicitud['objFecha'] = new \DateTime('now');
                                $objInfoDetalleSolCaractMF = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                $em->persist($objInfoDetalleSolCaractMF);
                                $em->flush();
                            }

                            $objTipoMedio = $em->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio($strUltimaMilla);
                            
                            //actualizo servicio tecnico
                            $entityServicioTecnico->setElementoId($intElementoDirecto);
                            $entityServicioTecnico->setInterfaceElementoId($intInterfaceDirecto);
                            $entityServicioTecnico->setElementoContenedorId($intNombreElementoContenedor);
                            $entityServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
                            $em->persist($entityServicioTecnico);
                            $em->flush();
                            
                            //actualizo el puerto
                            $objInterfaceElemento->setEstado('connected');
                            $em->persist($objInterfaceElemento);
                            $em->flush();                            

                            //GUARDAR INFO SERVICIO HISTORIAL
                            $entityServicioHistorial = new InfoServicioHistorial();
                            $entityServicioHistorial->setServicioId($entityServicio);
                            $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                            $entityServicioHistorial->setEstado($estado);
                            $entityServicioHistorial->setObservacion($observacionEdicion.$observacion.' '.$strObservacionDirecto);
                            $em->persist($entityServicioHistorial);
                            $em->flush();
                            
                            if ($observacionEdicion == "")
                            {
                                //actualizo estado del servicio
                                $entityServicio->setEstado($estado);
                                $em->persist($entityServicio);
                                $em->flush();

                                //GUARDA INFO DETALLE SOLICITUD
                                $entityDetalleSolicitud->setEstado($estado);
                                $entityDetalleSolicitud->setObservacion($observacion.' '.$strObservacionDirecto);
                                $em->persist($entityDetalleSolicitud);
                                $em->flush();

                                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                $entityDetalleSolHist = new InfoDetalleSolHist();
                                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                                $entityDetalleSolHist->setEstado('Factible');
                                $entityDetalleSolHist->setObservacion($observacion);
                                $em->persist($entityDetalleSolHist);
                                $em->flush();
                            }
                            
                            $respuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad");

                        }
                        else
                        {
                            if($intIdElemento != 0 && $intIdElemento)
                            {
                                $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($entityServicio->getId());
                                $estado = "Factible";
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

                                // Validacion de tecnologias cuando se trata de un traslado de punto para MegaDatos
                                if($strTipoOrdenServicio === 'T' && ($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN'))
                                {
                                    $arrayPuntoAnterior = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->getTecnologiaPuntoAnterior(
                                                                array('servicioId'      => $entityServicio->getId(),
                                                                      'empresaId'       => $idEmpresa,
                                                                      'estado'          => 'Activo'
                                                                ));
                                    $strTecnologiaPuntoAnterior = $arrayPuntoAnterior[0]["tecnologia"];

                                    if($strMarcaModeloElemento != $strTecnologiaPuntoAnterior)
                                    {
                                        $objProductoInternet = $em->getRepository('schemaBundle:AdmiProducto')
                                              ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                                "empresaCod"    => $idEmpresa,
                                                                "estado"        => "Activo"));

                                        $objSpcTrasladoDifTecnologia  = $serviceServicioTecnico
                                                            ->getServicioProductoCaracteristica($entityServicio,
                                                            "DIFERENTE TECNOLOGIA FACTIBILIDAD",
                                                            $objProductoInternet);

                                        if(empty($objSpcTrasladoDifTecnologia))
                                        {
                                            $intIdPersonaEmpresaRol = $session->get('idPersonaEmpresaRol');
                                            $arrayParametrosRetiro = array();
                                            $arrayParametrosRetiro['objServicio']            = $entityServicio;
                                            $arrayParametrosRetiro['strIpCreacion']          = $peticion->getClientIp();
                                            $arrayParametrosRetiro['strUsrCreacion']         = $peticion->getSession()->get('user');
                                            $arrayParametrosRetiro['objProdInternet']        = $objProductoInternet;
                                            $arrayParametrosRetiro['strEmpresaCod']          = $idEmpresa;
                                            $arrayParametrosRetiro['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
                                            
                                            $objInfoServicioService->generarRetiroEquiposTraslado($arrayParametrosRetiro);
                                            $serviceServicioTecnico->ingresarServicioProductoCaracteristica(
                                                                    $entityServicio,
                                                                    $objProductoInternet,
                                                                    "DIFERENTE TECNOLOGIA FACTIBILIDAD",
                                                                    "DIFERENTE TECNOLOGIA FACTIBILIDAD",
                                                                    $peticion->getSession()->get('user'));
                                        }
                                    }

                                }

                                /*Si la empresa es TN obtiene las caracteristicas obra civil, requiere regeneracion, observacion regeneracion.
                                 * Tambien obtiene la interface que se haya seleccionado en la pantalla del elemento de distribucion.
                                 */
                                if("TN" === $strPrefijoEmpresa)
                                {
                                    //TIPO_FACTIBILIDAD - Eliminamos la caractersitica existente previamente
                                    $servicioProdCaractTipo = $servicioGeneral->getServicioProductoCaracteristica($entityServicio, 
                                                                                                                  "TIPO_FACTIBILIDAD", 
                                                                                                                  $entityServicio->getProductoId());
                                    if($servicioProdCaractTipo)
                                    {
                                        $servicioProdCaractTipo->setEstado('Eliminado');
                                        $em->persist($servicioProdCaractTipo);
                                        $em->flush();
                                    }
                                    
                                    $entityAdmiCaractTipoFact = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy( array("descripcionCaracteristica" => 'TIPO_FACTIBILIDAD',
                                                                                      "estado"                    => "Activo"));
                                    $entityAdmiProdCaractTipoFact = null;
                                    if($entityAdmiCaractTipoFact)
                                    {
                                        $entityAdmiProdCaractTipoFact = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                           ->findOneBy( array("caracteristicaId" => $entityAdmiCaractTipoFact->getId(),
                                                                                              "productoId" => $entityServicio->getProductoId()));
                                    }
                                    //Si no existe la caracterisitica termina el metodo
                                    if(!$entityAdmiProdCaractTipoFact)
                                    {
                                        $respuesta->setContent('No creo el detalle del servicio. <br> Revise que exista la relación producto y '
                                                                . 'caracteristica TIPO_FACTIBILIDAD');
                                        return $respuesta;
                                    }
                                    else                            
                                    {
                                        $infoServProdCaractCapacidad1 = new InfoServicioProdCaract();
                                        $infoServProdCaractCapacidad1->setServicioId($entityServicio->getId());
                                        $infoServProdCaractCapacidad1->setProductoCaracterisiticaId($entityAdmiProdCaractTipoFact->getId());
                                        $infoServProdCaractCapacidad1->setValor($strTipoBackone);
                                        $infoServProdCaractCapacidad1->setFeCreacion(new \DateTime('now'));
                                        $infoServProdCaractCapacidad1->setUsrCreacion($peticion->getSession()->get('user'));
                                        $infoServProdCaractCapacidad1->setEstado("Activo");
                                        $em->persist($infoServProdCaractCapacidad1);
                                        $em->flush();
                                    }                                        
                                    
                                    //Busca la interface del elemento distribuidor
                                    $entityInterfaceElementoDistribucion = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                              ->find($intInterEleDistribucion);
                                    //Busca la caracteristica obra civil
                                    $entityAdmiCaracteristicaOC          = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                              ->findOneBy(array("descripcionCaracteristica"  => 'OBRA CIVIL',
                                                                                              "estado"                       => "Activo"));
                                    //Si no existe la caracterisitica termina el metodo
                                    if(!$entityAdmiCaracteristicaOC)
                                    {
                                        $respuesta->setContent('No creo la solicitud. <br> Revise que la caracteristica (OBRA CIVIL) exista.');
                                        return $respuesta;
                                    }
                                    //Busca la caracteristica de permisos de regeneracion
                                    $entityAdmiCaracteristicaPR          = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                              ->findOneBy(array("descripcionCaracteristica" => 'PERMISOS REGENERACION',
                                                                                                "estado"                    => "Activo"));
                                    //Si no existe la caracterisitica termina el metodo
                                    if(!$entityAdmiCaracteristicaPR)
                                    {
                                        $respuesta->setContent('No creo la solicitud. <br> Revise que la caracteristica (PERMISOS REGENERACION) '
                                                               . 'exista.');
                                        return $respuesta;
                                    }
                                    //Busca la caracteristica de observacion de permisos de regeneracion
                                    $entityAdmiCaracteristicaOPR         = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                              ->findOneBy(
                                                                                  array("descripcionCaracteristica" => 'OBSERVACION PERMISO REGENERACION',
                                                                                        "estado"                    => "Activo"));
                                    //Si no existe la caracterisitica termina el metodo
                                    if(!$entityAdmiCaracteristicaOPR)
                                    {
                                        $respuesta->setContent('No creo la solicitud. <br> Revise que la caracteristica '
                                                               . '(OBSERVACION PERMISO REGENERACION) exista.');
                                        return $respuesta;
                                    }
                                    //Busca la caracteristica metraje factibilidad
                                    $entityAdmiCaracteristicaMF          = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                              ->findOneBy(
                                                                                  array("descripcionCaracteristica" => 'METRAJE FACTIBILIDAD',
                                                                                        "estado"                    => "Activo"));
                                    //Si no existe la caracterisitica termina el metodo
                                    if(!$entityAdmiCaracteristicaMF)
                                    {
                                        $respuesta->setContent('No creo la solicitud. <br> Revise que la caracteristica '
                                                               . '(METRAJE FACTIBILIDAD) exista.');
                                        return $respuesta;
                                    }

                                    $strMetraje = '<b>Metraje:</b>' . $floatMetraje . '<br>';

                                    //Si existe un error al calcular el metraje se da formato para mostrar en el historial
                                    if(!empty($strErrorMetraje))
                                    {
                                        $strErrorMetraje = '<b>' . $strErrorMetraje . '</b><br>';
                                    }

                                } //Si la empresa es MD obtiene la primera interface en estado no connect que encuentre.
                                elseif("MD" === $strPrefijoEmpresa || "EN" === $strPrefijoEmpresa || "TNP" === $strPrefijoEmpresa)
                                {
                                    $entityInterfaceElementoDistribucion = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                              ->findOneBy(array("elementoId" => $intElementoPNivel,
                                                                                                "estado"     => "not connect"));
                                } //No encontro interfaces y termina el metodo.
                                else
                                {
                                    $respuesta->setContent("No se puede obtener la interface del elemento de distribucion " 
                                                           . $strNombreTipoElementoDistribuidor);
                                    return $respuesta;
                                }
                                $datosNuevos = "<b>Datos Nuevos:</b><br> "
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
                                if(
                                   $entityServicio->getEstado() == "Factible" or 
                                   $entityServicio->getEstado() == "PrePlanificada" or
                                   $entityServicio->getEstado() == "Planificada" or
                                   $entityServicio->getEstado() == "Detenido" or
                                   $entityServicio->getEstado() == "RePlanificada" or
                                   $entityServicio->getEstado() == "AsignadoTarea"
                                  )
                                {
                                    $estado = $entityServicio->getEstado();
                                    if($entityServicioTecnico->getElementoId() > 0 &&
                                        $entityServicioTecnico->getInterfaceElementoId() > 0 &&
                                        $entityServicioTecnico->getElementoContenedorId() > 0 &&
                                        $entityServicioTecnico->getElementoConectorId() > 0 &&
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

                                        $datosAnteriores = "<b>Datos Anteriores:</b><br> "
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
                                        $datosAnteriores = "No se encontraron datos anteriores para la actualizacion.";
                                    }
                                    $observacion = "Se edito la Factibilidad.<br>" . $datosNuevos . "<br>" . $datosAnteriores;
                                    //Si la empresa es TN permite modificar las caracteristicas de la solicitud de haber existido un cambio.
                                    if("TN" === $strPrefijoEmpresa)
                                    {
                                        //Busca la caracteristica Obra civil.
                                        $entityInfoDetalleSolCaractOC = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                           ->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitud,
                                                                                             "caracteristicaId"   => $entityAdmiCaracteristicaOC));
                                        // Entra si existe caracteristica obra civil.
                                        if($entityInfoDetalleSolCaractOC)
                                        {
                                            // Modifica el valor si es distinto al anterior.
                                            if($entityInfoDetalleSolCaractOC->getValor() != $chbxObraCivil)
                                            {
                                                $entityInfoDetalleSolCaractOC->setValor($chbxObraCivil);
                                                $entityInfoDetalleSolCaractOC->setUsrUltMod($peticion->getSession()->get('user'));
                                                $entityInfoDetalleSolCaractOC->setFeUltMod(new \DateTime('now'));
                                                $em->persist($entityInfoDetalleSolCaractOC);
                                                $em->flush();
                                            }
                                        } //Si no econtro la caracteristica y se envio un valor lo inserta
                                        elseif("true" === $chbxObraCivil)
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaOC;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $chbxObraCivil;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractOC = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractOC);
                                            $em->flush();
                                        }

                                        //Busca la caracteristica requiere regeneracion.
                                        $entityInfoDetalleSolCaractPR = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                           ->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitud,
                                                                                             "caracteristicaId"   => $entityAdmiCaracteristicaPR));
                                        // Entra si existe caracteristica requiere regeneracion.
                                        if($entityInfoDetalleSolCaractPR)
                                        {
                                            // Modifica el valor si es distinto al anterior.
                                            if($entityInfoDetalleSolCaractPR->getValor() != $chbxObservacionRegeneracion)
                                            {
                                                $entityInfoDetalleSolCaractPR->setValor($chbxObservacionRegeneracion);
                                                $entityInfoDetalleSolCaractPR->setUsrUltMod($peticion->getSession()->get('user'));
                                                $entityInfoDetalleSolCaractPR->setFeUltMod(new \DateTime('now'));
                                                $em->persist($entityInfoDetalleSolCaractPR);
                                                $em->flush();
                                            }
                                        } //Si no econtro la caracteristica y se envio un valor lo inserta
                                        elseif("true" === $chbxObservacionRegeneracion)
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaPR;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $chbxObservacionRegeneracion;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractPR = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractPR);
                                            $em->flush();
                                        }

                                        //Busca la caracteristica metraje factibilidad
                                        $entityInfoDetalleSolCaractOPR = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                            ->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitud,
                                                                                              "caracteristicaId"   => $entityAdmiCaracteristicaOPR));
                                        // Entra si existe caracteristica observacion requiere regeneracion.
                                        if($entityInfoDetalleSolCaractOPR)
                                        {
                                            // Modifica el valor si es distinto al anterior.
                                            if($entityInfoDetalleSolCaractOPR->getValor() != $strObservacionRegeneracion)
                                            {
                                                $entityInfoDetalleSolCaractOPR->setValor($strObservacionRegeneracion);
                                                $entityInfoDetalleSolCaractOPR->setUsrUltMod($peticion->getSession()->get('user'));
                                                $entityInfoDetalleSolCaractOPR->setFeUltMod(new \DateTime('now'));
                                                $em->persist($entityInfoDetalleSolCaractOPR);
                                                $em->flush();
                                            }
                                        } //Si no econtro la caracteristica y se envio un valor lo inserta
                                        elseif(!empty($strObservacionRegeneracion))
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaOPR;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $strObservacionRegeneracion;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractOPR = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractOPR);
                                            $em->flush();
                                        }

                                        //Busca la caracteristica metraje factibilidad
                                        $entityInfoDetalleSolCaractMF  = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                            ->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitud,
                                                                                              "caracteristicaId"   => $entityAdmiCaracteristicaMF));
                                        // Entra si existe caracteristica metraje factibilidad
                                        if($entityInfoDetalleSolCaractMF)
                                        {
                                            // Modifica el valor si es distinto al anterior.
                                            if($entityInfoDetalleSolCaractMF->getValor() != $floatMetraje)
                                            {
                                                $entityInfoDetalleSolCaractMF->setValor($floatMetraje);
                                                $entityInfoDetalleSolCaractMF->setUsrUltMod($peticion->getSession()->get('user'));
                                                $entityInfoDetalleSolCaractMF->setFeUltMod(new \DateTime('now'));
                                                $em->persist($entityInfoDetalleSolCaractMF);
                                                $em->flush();
                                            }
                                        } //Si no econtro la caracteristica y se envio un valor lo inserta
                                        elseif(!empty($floatMetraje))
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaMF;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $floatMetraje;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractMF = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractMF);
                                            $em->flush();
                                        }
                                    }
                                }
                                else
                                {
                                    //Se guarda por primera vez la factibilidad
                                    $observacion .=$datosNuevos;
                                    //Si la empresa es TN permite ingresar las caracteristicas de la solicitud de haber existido un cambio.
                                    if("TN" === $strPrefijoEmpresa)
                                    {
                                        // Permite ingresar la caracteristica Obra civil.
                                        if("true" === $chbxObraCivil)
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaOC;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $chbxObraCivil;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractOC = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractOC);
                                            $em->flush();
                                        }
                                        // Permite ingresar la caracteristica requiere regeneracion.
                                        if("true" === $chbxObservacionRegeneracion)
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaPR;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $chbxObservacionRegeneracion;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractPR = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractPR);
                                            $em->flush();
                                        }
                                        // Permite ingresar la caracteristica observacion requiere regeneracion.
                                        if(!empty($strObservacionRegeneracion))
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaOPR;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $strObservacionRegeneracion;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractOPR = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractOPR);
                                            $em->flush();
                                        }
                                        //Permite ingresar la caracteristica metraje factibilidad
                                        if(!empty($floatMetraje))
                                        {
                                            $arrayInfoDetalleSolicitud                                  = array();
                                            $arrayInfoDetalleSolicitud['entityAdmiCaracteristica']      = $entityAdmiCaracteristicaMF;
                                            $arrayInfoDetalleSolicitud['strValor']                      = $floatMetraje;
                                            $arrayInfoDetalleSolicitud['strEstado']                     = 'Activo';
                                            $arrayInfoDetalleSolicitud['entityInfoDetalleSolicitud']    = $entityDetalleSolicitud;
                                            $arrayInfoDetalleSolicitud['strUsrCreacion']                = $peticion->getSession()->get('user');
                                            $arrayInfoDetalleSolicitud['objFecha']                      = new \DateTime('now');
                                            $objInfoDetalleSolCaractMF = $serviceSolicitudes->creaObjetoInfoDetalleSolCaract($arrayInfoDetalleSolicitud);
                                            $em->persist($objInfoDetalleSolCaractMF);
                                            $em->flush();
                                        }
                                    }
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
                                $entityServicio->setEstado($estado);
                                $em->persist($entityServicio);
                                $em->flush();

                                //GUARDAR INFO SERVICIO HISTORIAL
                                $entityServicioHistorial = new InfoServicioHistorial();
                                $entityServicioHistorial->setServicioId($entityServicio);
                                $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                $entityServicioHistorial->setEstado($estado);
                                $entityServicioHistorial->setObservacion($observacion);
                                $em->persist($entityServicioHistorial);
                                $em->flush();

                                //GUARDA INFO DETALLE SOLICITUD
                                $entityDetalleSolicitud->setEstado("Factible");
                                $entityDetalleSolicitud->setObservacion($observacion);
                                $em->persist($entityDetalleSolicitud);
                                $em->flush();

                                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                $entityDetalleSolHist = new InfoDetalleSolHist();
                                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                                $entityDetalleSolHist->setEstado('Factible');
                                $entityDetalleSolHist->setObservacion($observacion);
                                $em->persist($entityDetalleSolHist);
                                $em->flush();

                                /* Generación del envío de correo al aprobar la solicitud de Factibilidad
                                 * Se obtiene el vendedor del servicio para agregarlo como destinatario del correo que se enviará al aprobar
                                 * la factibilidad
                                 */ 
                                $asunto         = "Aprobacion de Solicitud de Factibilidad de Instalacion #" . $entityDetalleSolicitud->getId();
                                $to             = array();
                                
                                if($entityServicio->getUsrVendedor())
                                {
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
                                
                                /* Envío de correo por medio de plantillas
                                 * Se obtiene la plantilla y se invoca a la función generarEnvioPlantilla del service que internamente obtiene
                                 * los alias asociados a la plantilla 'APROBAR_FACTIB' y envía el respectivo correo
                                 */
                                /* @var $envioPlantilla EnvioPlantilla */
                                $arrayParametros    = array('detalleSolicitud' => $entityDetalleSolicitud,'usrAprueba'=>$strUsrCreacion);
                                $envioPlantilla     = $this->get('soporte.EnvioPlantilla');
                                $envioPlantilla->generarEnvioPlantilla( $asunto, 
                                                                        $to, 
                                                                        'APROBAR_FACTIB', 
                                                                        $arrayParametros,
                                                                        $idEmpresa,
                                                                        '',
                                                                        '',
                                                                        null, 
                                                                        true,
                                                                        'notificaciones_telcos@telconet.ec');

                                $respuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad");
                                $boolGeneraSolsPorTraslado = true;
                            }
                            else
                            {
                                $respuesta->setContent("Caja sin " . $strNombreTipoElemento . " imposible seguir.");
                            }
                        }
                    }
                    else
                    {
                        $respuesta->setContent("El servicio se encuentra en estado: " .
                                               $entityServicio->getEstado() .
                                               ". No se puede realizar edicion de factibilidad.");
                        return $respuesta;
                    }
                }
            }
            else
            {
                $respuesta->setContent("No existe el detalle de Solicitud");
            }
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $respuesta->setContent($mensajeError);
        }
        
        /* Si es la empresa MD, si el tipo de orden del servicio es Traslado,
         * se debe buscar alguna solicitud agregar equipo pendiente de gestión en el servicio origen del traslado
         * para poder asignarla al nuevo punto y que se gestionen luego del traslado del internet el cambio y 
         * agregación de los nuevos equipos DUAL BAND
         */
        if($boolGeneraSolsPorTraslado && $strPrefijoEmpresa  === "MD" && $strTipoOrdenServicio == "T")
        {
            $serviceInfoServicio                    = $this->get('comercial.infoservicio');
            $arrayRespuestaGeneraSolsPorTraslado    = $serviceInfoServicio->generaSolsPorTraslado(array("intIdServicio"     => 
                                                                                                        $entityServicio->getId(),
                                                                                                        "strCodEmpresa"     => $idEmpresa,
                                                                                                        "strUsrCreacion"    => 
                                                                                                        $peticion->getSession()->get('user'),
                                                                                                        "strIpCreacion"     => 
                                                                                                        $peticion->getClientIp()));
            if($arrayRespuestaGeneraSolsPorTraslado['status'] === "ERROR")
            {
                $respuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad".
                                       "<br><strong style='color:red; font-size:14px;'>".$arrayRespuestaGeneraSolsPorTraslado["mensaje"]."</strong>");
            }
        }
        
        // creacion automatica de orden de trabajo para MegaDatos y tipo de orden T (Traslado)

        $boolEsCliente = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->getEsCliente(
                                    array('puntoId' => $entityServicio->getPuntoId(),
                                          'empresaCod'=>$idEmpresa
                                    ));

        if ($strPrefijoEmpresa  === "MD" && $boolEsCliente  && $strTipoOrdenServicio == "T" && $entityServicio->getEstado() === "Factible")
        {
            $strOTClienteConDeuda = 'N';
            $strMensajeObservacion = '';
            $arrayParametro        = $entityServicio->getId();
            $arrayValor            = explode("|", $arrayParametro);
            try
            {            
                $arrayParametros = array('strOficina'            => $peticion->getSession()->get('idOficina'),
                                        'strUser'                => $entityServicio->getUsrCreacion(),
                                        'strIp'                  => $peticion->getClientIp(),
                                        'array_valor'            => $arrayValor,
                                        'strMensajeObservacion'  => $strMensajeObservacion,                                    
                                        'strOTClienteConDeuda'   => $strOTClienteConDeuda,
                                        'intIdPunto'             => $entityServicio->getPuntoId(),
                                        'strCodEmpresa'          => $idEmpresa,
                                        'strPrefijoEmpresa'      => $strPrefijoEmpresa);
        
                $serviceConvertirOT = $this->get('comercial.ConvertirOrdenTrabajo');
                $strResponse        = $serviceConvertirOT->convertirOrdenTrabajo($arrayParametros); 
                if($strResponse === "Se generaron las ordenes de trabajo de los servicios seleccionados.")
                {
                    $strResponse = "Se crea orden de trabajo automáticamente por factibilidad manual";
                }

            }
            catch(\Exception $e)
            {
                $strResponse = "Ocurrió un error al convertir a Orden de Trabajo, por favor consulte con el Administrador";           
            }
            $respuesta->setContent($respuesta->getContent().'. <br> '.$strResponse);

        }

        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_135-95")
     * Metodo utilizado para guardar la factibilidad tramo 
     *
     * @version 1.0 version inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-05-2016 - Se guarda toda la información de la factibilidad del tramo en la observación del historial del servicio
     *
     * 
     * @author Modificado: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 18-03-2021 - Se realiza la verificación estado del servico para poder realizar el registro de la información
     *                           de la factiblidad.
     *
     * @author Modificado: Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 26-04-2021 - Se agrega validación para actualizar los estados de los servicios adicionales del producto Datos SafeCity.
     *
     * @return response
     */
    public function ajaxGuardaFactibilidadTramoAction()
    {
        $respuesta = new Response();
        $session  = $this->get('request')->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        
        $id = $peticion->get('id');
        $observacion = $peticion->get('observacion');
        $fechaProgramacion = explode('T',$peticion->get('fechaProgramacion'));
        $dateF = explode("-",$fechaProgramacion[0]);
        $fechaCreacionTramo = new \DateTime(date("Y/m/d G:i:s", strtotime($dateF[2]."-".$dateF[1]."-".$dateF[0]))); 
        
        $em = $this->getDoctrine()->getManager();
	
        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
        
	$em->getConnection()->beginTransaction();
	$arrayEstadosServicios          = array('Rechazado','Rechazada','Anulado','Anulada','Eliminado',
                                                'Eliminada','Cancel','Cancelado','Cancelada');
        
	try
        {
		
            if($entityDetalleSolicitud)
            {         
                //GUARDA INFO SERVICIO
                $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());

                if(!in_array($entityServicio->getEstado(), $arrayEstadosServicios))
                {
                    if($fechaCreacionTramo) 
                    {
                        $fechaFactibilidadEnProceso=new \DateTime();
                        $fechaFactibilidadEnProceso->setDate($dateF[0], $dateF[1], $dateF[2]);

                        $entityServicio->setEstado("FactibilidadEnProceso");
                        $em->persist($entityServicio);
                        $em->flush();

                        //GUARDAR INFO SERVICIO HISTORIAL
                        $entityServicioHistorial = new InfoServicioHistorial();  
                        $entityServicioHistorial->setServicioId($entityServicio);	
                        $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
                        $entityServicioHistorial->setEstado('FactibilidadEnProceso');
                        $entityServicioHistorial->setObservacion($observacion."<br>Fecha Factibilidad: "
                                                . "".date_format($fechaFactibilidadEnProceso, 'd/m/Y')); 
                        $em->persist($entityServicioHistorial);
                        $em->flush(); 

                        //GUARDA INFO DETALLE SOLICITUD
                        $entityDetalleSolicitud->setEstado("FactibilidadEnProceso");
                        $em->persist($entityDetalleSolicitud);
                        $em->flush();  

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);            
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolHist->setEstado('FactibilidadEnProceso');
                        $entityDetalleSolHist->setObservacion($observacion);
                        $entityDetalleSolHist->setFeIniPlan(new \DateTime('now'));
                        $entityDetalleSolHist->setFeFinPlan($fechaFactibilidadEnProceso);
                        $em->persist($entityDetalleSolHist);
                        $em->flush();

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
                                    $arrayEstadosSerProAdd = array('Pre-servicio','Pendiente','PreFactibilidad');
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
                                        $objSerHisConfProAdd->setIpCreacion($peticion->getClientIp());
                                        $objSerHisConfProAdd->setFeCreacion(new \DateTime('now'));
                                        $objSerHisConfProAdd->setUsrCreacion($peticion->getSession()->get('user'));
                                        $objSerHisConfProAdd->setObservacion($observacion);
                                        $objSerHisConfProAdd->setEstado($objServicioConfProAdd->getEstado());
                                        $em->persist($objSerHisConfProAdd);
                                        $em->flush();
                                        //se actualiza la solicitud para los servicios adicionales
                                        $objSolFactServicioProAdd = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->findOneBy(array("servicioId"      => $objServicioConfProAdd->getId(),
                                                              "tipoSolicitudId" => $entityDetalleSolicitud->getTipoSolicitudId()->getId(),
                                                              "estado"          => "PreFactibilidad"));
                                        //se verifica si no existe la solicitud para ser generada
                                        if(!is_object($objSolFactServicioProAdd))
                                        {
                                            //se genera la solicitud para los servicios adicionales
                                            $objSolFactServicioProAdd = new InfoDetalleSolicitud();
                                            $objSolFactServicioProAdd->setServicioId($objServicioConfProAdd);
                                            $objSolFactServicioProAdd->setTipoSolicitudId($entityDetalleSolicitud->getTipoSolicitudId());
                                            $objSolFactServicioProAdd->setEstado($entityDetalleSolicitud->getEstado());
                                            $objSolFactServicioProAdd->setUsrCreacion($peticion->getSession()->get('user'));
                                            $objSolFactServicioProAdd->setObservacion($entityDetalleSolicitud->getObservacion());
                                            $objSolFactServicioProAdd->setFeCreacion(new \DateTime('now'));
                                            $em->persist($objSolFactServicioProAdd);
                                            $em->flush();
                                        }
                                        else
                                        {
                                            //actualiza el estado de la solicitud
                                            $objSolFactServicioProAdd->setEstado($entityDetalleSolicitud->getEstado());
                                            $em->persist($objSolFactServicioProAdd);
                                            $em->flush();
                                        }
                                        //guardar historial de la solicitud
                                        $entityDetalleSolHistProAdd = new InfoDetalleSolHist();
                                        $entityDetalleSolHistProAdd->setDetalleSolicitudId($objSolFactServicioProAdd);
                                        $entityDetalleSolHistProAdd->setIpCreacion($peticion->getClientIp());
                                        $entityDetalleSolHistProAdd->setFeCreacion(new \DateTime('now'));
                                        $entityDetalleSolHistProAdd->setUsrCreacion($peticion->getSession()->get('user'));
                                        $entityDetalleSolHistProAdd->setEstado($objSolFactServicioProAdd->getEstado());
                                        $entityDetalleSolHistProAdd->setObservacion($observacion);
                                        $entityDetalleSolHistProAdd->setFeIniPlan(new \DateTime('now'));
                                        $entityDetalleSolHistProAdd->setFeFinPlan($fechaFactibilidadEnProceso);
                                        $em->persist($entityDetalleSolHistProAdd);
                                        $em->flush();
                                    }
                                }
                            }
                        }
                        /***FIN ACTUALIZAR ESTADO DEL SERVICIO ADICIONAL***/

                        // ------- COMUNICACIONES --- NOTIFICACIONES 
                        // DESTINATARIOS.... 
                        $formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
                        $to = array();
                        $cc = array();

                        $to[] = 'notificaciones_telcos@telconet.ec';
                        $cc[] = 'notificaciones_telcos@telconet.ec';


                        if($formasContacto){
                            foreach($formasContacto as $formaContacto){
                                $to[] = $formaContacto['valor'];
                            }
                        }

                        //$this->get('mailer')->send($message);				
                        $em->getConnection()->commit();  
                        $respuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad");                      
                        
                    }
                    else
                    {
                        $respuesta->setContent("No escogio un Elemento de la Lista.");                      
                    }	
                    
                }
                else
                {
                       $respuesta->setContent("El servicio se encuentra en estado: " .
                                                                  $entityServicio->getEstado() .
                                                                  ". No se puede realizar la solicitud de factibilidad.");
                }    
            }
            else
            {    
                $respuesta->setContent("No existe el detalle de Solicitud");    
            }
            
            
	}
        catch(\Exception $e)
        {
		$em->getConnection()->rollback();
		$mensajeError = "Error: ".$e->getMessage();
		$respuesta->setContent($mensajeError);
	}
	
	return $respuesta;
    }
    
    /**
	* @Secure(roles="ROLE_135-96")
	*/
    public function guardaFactMaterialesAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
		$session  = $peticion->getSession();
        $id = $peticion->get('id');
		$prefijoEmpresa = $session->get('prefijoEmpresa');
             
        $em = $this->getDoctrine()->getManager();
		$em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($id);
		
        $em->getConnection()->beginTransaction();
		$em_comunicacion->getConnection()->beginTransaction();
		
		try {
	        if($entityDetalleSolicitud  && $entityDetalleSolicitud->getEstado() == "Factible")
			{
				$servicioId = $entityDetalleSolicitud->getServicioId();			
	            $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($servicioId);
				$entityPunto = $em->getRepository('schemaBundle:InfoPunto')->findOneById($entityServicio->getPuntoId());
				$entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($entityPunto->getPersonaEmpresaRolId());
	            $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");
	            
				// ACTUALIZO EL ESTADO DEL PROSPECTO
				if($entityPersonaEmpresaRol->getEstado()!='Activo'){
				    $entityPersonaEmpresaRol->setEstado('PendAprobSolctd');
				    $em->persist($entityPersonaEmpresaRol);
				    $em->flush();
				    
				    //REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
				    $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
				    $entity_persona_historial->setEstado($entityPersonaEmpresaRol->getEstado());
				    $entity_persona_historial->setFeCreacion(new \DateTime('now'));
				    $entity_persona_historial->setIpCreacion($peticion->getClientIp());
				    $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
				    $entity_persona_historial->setUsrCreacion($session->get('user'));
				    $em->persist($entity_persona_historial);
				    $em->flush();
				}	
				//ACTUALIZO EL ESTADO DEL SERVICIO
				$entityServicio->setEstado("PreAprobacionMateriales");
				$em->persist($entityServicio);
				$em->flush();   	
			
				//GUARDAR INFO SERVICIO HISTORIAL
				$entityServicioHistorial = new InfoServicioHistorial();  
				$entityServicioHistorial->setServicioId($entityServicio);	
				$entityServicioHistorial->setIpCreacion($peticion->getClientIp());
				$entityServicioHistorial->setFeCreacion(new \DateTime('now'));
				$entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
				$entityServicioHistorial->setEstado('PreAprobacionMateriales'); 
				$em->persist($entityServicioHistorial);
				$em->flush(); 
				
				//CREO LA INFO DETALLE SOLICITUD DE MATERIALES EXCEDENTES
				$entitySolicitud  = new InfoDetalleSolicitud();
				$entitySolicitud->setServicioId($entityServicio);
				$entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
				$entitySolicitud->setEstado("Pendiente");	
				$entitySolicitud->setUsrCreacion($peticion->getSession()->get('user'));		
				$entitySolicitud->setFeCreacion(new \DateTime('now'));

				$em->persist($entitySolicitud);
				$em->flush(); 	
				
				//CREO LA INFO DETALLE SOLICICITUD HISTORIAL DE AMTERIALES EXCEDENTES
				$entityDetalleSolHist = new InfoDetalleSolHist();
				$entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
				
				$entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
				$entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
				$entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
				$entityDetalleSolHist->setEstado('Pendiente');  

				$em->persist($entityDetalleSolHist);
				$em->flush();  

				if($entitySolicitud != null)
				{
		            $json_materiales = json_decode($peticion->get('materiales'));
		            $total_materiales = $json_materiales->total;
		            $array_materiales = $json_materiales->materiales;    

		            if($total_materiales>0 && $array_materiales && count($array_materiales)>0)
		            {
		                $boolGuardo = false;
		                
		                foreach($array_materiales as $material)
		                {
		                    $codMaterial = (isset($material->cod_material) ? $material->cod_material : "");
		                    
							if($material->costo_material>0){
								$prev_costoMaterial = (isset($material->costo_material) ? explode("$ ", $material->costo_material) : false);
								$costoMaterial = (($prev_costoMaterial && count($prev_costoMaterial)>0) ? number_format($prev_costoMaterial[1], 2, '.', '') : 0.00);
		                    }else{
								$costoMaterial = 0.00;
							}
							if($material->precio_venta_material>0){
								$prev_precioVentaMaterial = (isset($material->precio_venta_material) ? explode("$ ", $material->precio_venta_material) : false);
								$precioVentaMaterial = (($prev_precioVentaMaterial && count($prev_precioVentaMaterial)>0) ? number_format($prev_precioVentaMaterial[1], 2, '.', '') : 0.00);
		                    }else{
								$precioVentaMaterial = 0.00;
							}
		                    
		                 
		                    $cantidadEmpresa =  (isset($material->cantidad_empresa) ? ($material->cantidad_empresa ? $material->cantidad_empresa : 0) : 0); 
		                    $cantidadEstimada =  (isset($material->cantidad_estimada) ? ($material->cantidad_estimada ? $material->cantidad_estimada : 0) : 0); 
		                    $cantidadCliente =  ($cantidadEstimada > $cantidadEmpresa ? ($cantidadEstimada - $cantidadEmpresa) : 0); 
		                                 
		                    //GUARDAR INFO DETALLE SOLICICITUD MATERIAL
		                    $entityDetalleSolMaterial = new InfoDetalleSolMaterial();
		                    $entityDetalleSolMaterial->setDetalleSolicitudId($entitySolicitud);
		                    $entityDetalleSolMaterial->setMaterialCod($codMaterial);
		                    $entityDetalleSolMaterial->setCostoMaterial($costoMaterial);
		                    $entityDetalleSolMaterial->setPrecioVentaMaterial($precioVentaMaterial);                    
		                    $entityDetalleSolMaterial->setCantidadEstimada($cantidadEstimada);
		                    $entityDetalleSolMaterial->setCantidadCliente($cantidadCliente);                    
		                    $entityDetalleSolMaterial->setValorCobrado(0.00);

		                    $entityDetalleSolMaterial->setIpCreacion($peticion->getClientIp());
		                    $entityDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
		                    $entityDetalleSolMaterial->setUsrCreacion($peticion->getSession()->get('user'));

		                    $em->persist($entityDetalleSolMaterial);
		                    $em->flush();
		                    
		                    if($entityDetalleSolMaterial == null)
		                    {
		                        $boolGuardo = true;
		                    }                    
		                }  
		                
		                if(!$boolGuardo)
		                {
							//GUARDO LA INFO DETALLE SOLICITUD DE LA FACTIBILIDAD
							//GUARDA INFO DETALLE SOLICITUD
							$entityDetalleSolicitud->setEstado("PreAprobacionMateriales");
							$em->persist($entityDetalleSolicitud);
							$em->flush();  
							
							//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
							$entityDetalleSolHist = new InfoDetalleSolHist();
							$entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);            
							$entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
							$entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
							$entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
							$entityDetalleSolHist->setEstado('PreAprobacionMateriales');  
							$em->persist($entityDetalleSolHist);
							$em->flush(); 
							
							// ------- COMUNICACIONES --- NOTIFICACIONES 
							$mensaje = $this->renderView('planificacionBundle:Factibilidad:notificacionMateriales.html.twig', 
														array('detalleSolicitud' => $entitySolicitud,'motivo'=> null));
							
							$asunto  ="Solicitud de Materiales Excedentes de Instalacion #".$entitySolicitud->getId();
							
							// DESTINATARIOS.... 
							$formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
							$to = array();
							$cc = array();
							$cc[] = 'notificaciones_telcos@telconet.ec';
								
							if($prefijoEmpresa=="TTCO"){
								$to[] = 'rortega@trans-telco.com';
								$cc[] = 'sac@trans-telco.com';
							}
							else if($prefijoEmpresa=="MD"){
								$to[] = 'notificaciones_telcos@telconet.ec';
							}						
							
							if($formasContacto){
								foreach($formasContacto as $formaContacto){
									$to[] = $formaContacto['valor'];
								}
							}
							
							// ENVIO DE MAIL
							$message = \Swift_Message::newInstance()
								->setSubject($asunto)
								->setFrom('notificaciones_telcos@telconet.ec')
								->setTo($to)
								->setCc($cc)
								->setBody($mensaje,'text/html')
							;
							
							//$this->get('mailer')->send($message);				
							
							// ------- COMUNICACIONES --- NOTIFICACIONES 
							$mensaje = $this->renderView('planificacionBundle:Factibilidad:notificacion.html.twig', 
														array('detalleSolicitud' => $entityDetalleSolicitud,'motivo'=> null));
							
							$asunto  ="Aprobacion de Solicitud de Factibilidad de Instalacion #".$entityDetalleSolicitud->getId();
								
							// DESTINATARIOS.... 
							$formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
							$to = array();
							$cc = array();
							
							$cc[] = 'notificaciones_telcos@telconet.ec';
								
							if($prefijoEmpresa=="TTCO"){
								$to[] = 'rortega@trans-telco.com';
								$cc[] = 'sac@trans-telco.com';
							}
							else if($prefijoEmpresa=="MD"){
								$to[] = 'notificaciones_telcos@telconet.ec';
							}
							
							if($formasContacto){
								foreach($formasContacto as $formaContacto){
									$to[] = $formaContacto['valor'];
								}
							}
							
							// ENVIO DE MAIL
							$message = \Swift_Message::newInstance()
								->setSubject($asunto)
								->setFrom('notificaciones_telcos@telconet.ec')
								->setTo($to)
								->setCc($cc)
								->setBody($mensaje,'text/html')
							;
							
							//$this->get('mailer')->send($message);				
					
		                    $em->getConnection()->commit();
				    $em_comunicacion->getConnection()->commit();
		                    $respuesta->setContent("Se registro la Solicitud de los excedentes de los Materiales");                    
		                }
		                else
		                {
		                    // Rollback the failed transaction attempt
		                    $em->getConnection()->rollback();
							$em_comunicacion->getConnection()->rollback();
		                    
		                    $respuesta->setContent("No se registro la Solicitud de los Materiales");
		                }               
						            
		            }
		            else
		            {
						$em->getConnection()->rollback();
						$em_comunicacion->getConnection()->rollback();
		                $respuesta->setContent("No existe ningun material asociado");
		            }  						
				}
		        else
		        {
					$em->getConnection()->rollback();
					$em_comunicacion->getConnection()->rollback();
		            $respuesta->setContent("No se pudo guardar la Solicitud de Materiales");
		        }
			}
	        else
	        {
				$em->getConnection()->rollback();
				$em_comunicacion->getConnection()->rollback();
	            $respuesta->setContent("No existe el detalle de Solicitud");
	        }
			
		 } catch (\Exception $e) {
            $em->getConnection()->rollback();
			$em_comunicacion->getConnection()->rollback();
            
			$mensajeError = "Error: ".$e->getMessage();
			error_log($mensajeError);
			$respuesta->setContent($mensajeError);
		}
                
        return $respuesta;
    }
    
    /**
	* @Secure(roles="ROLE_146-1")
    * 
    * Documentación para el método 'ajaxInstalacionesMesAction'.
    *
    * Obtiene instalaciones del mes
    * @return response con mensaje y arreglo de respuesta.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 02-02-2015
	*/
    public function ajaxInstalacionesMesAction()
    {
        $em                     = $this->get('doctrine')->getManager('telconet');				
        $intNumDay              = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $fechaFin               = date('Y') . '-' . date('m') . '-' .$intNumDay  ;
        $fechaIni               = date('Y-m')."-01";
        $InstalacionesAprobadas = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findTotalInstalacionesAprobadas($fechaIni,$fechaFin);					
        foreach($InstalacionesAprobadas as $dato){	
            $arreglo[]= array(
                                'name'=> sprintf("%s",'Aprobadas'),
                                'data1'=> sprintf("%s",$dato)
                             );  
        }
        $InstalacionesRechazadas = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findTotalInstalacionesRechazadas($fechaIni,$fechaFin);	
        foreach($InstalacionesRechazadas as $dato){	
            $arreglo[]= array(
                                'name'=> sprintf("%s",'Rechazadas'),
                                'data1'=> sprintf("%s",$dato)
                            );  
        }		
        if (empty($arreglo)){
            $arreglo[]= array(
                                'name'=> "",
                                'data1'=> ""
                            );  
        }
        $response = new Response(json_encode(array('instalaciones'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }	

    /**
	* @Secure(roles="ROLE_146-1")
    * 
    * Documentación para el método 'ajaxInstalacionesRechazadasMesAction'.
    *
    * Obtiene instalaciones rechazadas del mes
    * @return response con mensaje y arreglo de respuesta.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 02-02-2015
	*/
    public function ajaxInstalacionesRechazadasMesAction()
    {
        $em                      = $this->get('doctrine')->getManager('telconet');				
        $intNumDay               = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $fechaFin                = date('Y') . '-' . date('m') . '-' .$intNumDay  ;
        $fechaIni                = date('Y-m')."-01";
        $InstalacionesRechazadas = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findTotalInstalacionesRechazadasGroupByMotivoRechazo($fechaIni,$fechaFin);					
        foreach($InstalacionesRechazadas as $dato){	
            $arreglo[]= array(
                                'name'=> sprintf("%s",substr($dato['descripcionMotivo'],0,25)),
                                'data1'=> sprintf("%s",$dato['total'])
                            );  
        }		
        if (empty($arreglo)){
            $arreglo[]= array(
                                'name'=> "",
                                'data1'=> ""
                            );  
        }
        $response = new Response(json_encode(array('instalaciones'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }
	
    /**
	* @Secure(roles="ROLE_146-1")
    * 
    * Documentación para el método 'ajaxInstalacionesAprobadasMesAction'.
    *
    * Obtiene instalaciones aprobadas del mes
    * @return response con mensaje y arreglo de respuesta.
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 02-02-2015
	*/
    public function ajaxInstalacionesAprobadasMesAction()
    {
        $em                     = $this->get('doctrine')->getManager('telconet');				
        $intNumDay              = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $fechaFin               = date('Y') . '-' . date('m') . '-' .$intNumDay  ;
        $fechaIni               = date('Y-m')."-01";
        $InstalacionesAprobadas = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findTotalInstalacionesAprobadasGroupByEstado($fechaIni,$fechaFin);					
        foreach($InstalacionesAprobadas as $dato){	
            $arreglo[]= array(
                                'name'=> sprintf("%s",substr($dato['descripcionEstado'],0,25)),
                                'data1'=> sprintf("%s",$dato['total'])
                            );  
        }		
        if (empty($arreglo)){
            $arreglo[]= array(
                                'name'=> "",
                                'data1'=> ""
                            );  
        }
        $response = new Response(json_encode(array('instalaciones'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }
    
    
    /**
    * 
    * Retorna el twig y carga los permisos de la pantalla
    * @return retorna el twig
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 14-11-2016 Se agregó la opción para asignar la factibilidad a un pseudo pe
    * 
	*/
    
    public function indexFactibilidadNodoClienteAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_323-3358'))
        {
            $rolesPermitidos[] = 'ROLE_323-3358'; // RECHAZAR FACTIBILIDAD
        }
        if(true === $this->get('security.context')->isGranted('ROLE_323-3357'))
        {
            $rolesPermitidos[] = 'ROLE_323-3357'; // FECHA FACTIBILIDAD 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_323-3359'))
        {
            $rolesPermitidos[] = 'ROLE_323-3359'; // APROBAR FACTIBILIDAD 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_323-4917'))
        {
            $rolesPermitidos[] = 'ROLE_323-4917'; // FACTIBILIDAD PSEUDO
        }        

        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("320", "1");
        
        return $this->render('planificacionBundle:Factibilidad:indexNodoCliente.html.twig', array(
                            'item'            => $entityItemMenu,
                            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * 
    * ajaxConsultaPlanificacionNodoAction
    * Consulta las solicitudes de nodo cliente
    * 
    * @return json con las solucitudes de nodo cliente
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
	*/
    
    public function ajaxConsultaPlanificacionNodoAction()
    {
        ini_set('max_execution_time', 3000000);
        $respuesta            = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion             = $this->get('request');
        $codEmpresa           = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
		
        $fechaDesdePlanif     = explode('T',$peticion->get('fechaDesdePlanif'));
        $fechaHastaPlanif     = explode('T',$peticion->get('fechaHastaPlanif'));
        $estado               = $peticion->get('txtEstado');
        $nombreNodo           = $peticion->get('txtNodo');
        $userCrea             = $peticion->get('txtUser');
        $start                = $peticion->get('start');
        $limit                = $peticion->get('limit');
        $strIdJurisdiccion       = $peticion->get('id_jurisdiccion');
        $strLimite = $peticion->get('limite');
        $emComercial                   = $this->getDoctrine()->getManager("telconet");
        $arrayJurisdiccionesId = array();

        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $peticion->getSession()->get('idPersonaEmpresaRol');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
                                        
        $arrayJurisdiccionesEmpresaId = array();
        $arrayJurisdiccionesEmpresa = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
        ->getResultadoJurisdiccionesPorEmpresaSinEstado($codEmpresa);
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

        $objSolicitud = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD EDIFICACION',
                                                                  'estado'              =>'Activo'));
        $arrayParametros                             = array();
        $arrayParametros["idSolicitud"]              = $objSolicitud->getId();
        $arrayParametros["codEmpresa"]               = $codEmpresa;
        $arrayParametros["estado"]                   = $estado;        
        $arrayParametros["nombreNodo"]               = $nombreNodo;
        $arrayParametros["userCrea"]                 = $userCrea;
        $arrayParametros["start"]                    = $start;
        $arrayParametros["limit"]                    = $limit;
        $arrayParametros["search_fechaDesde"]        = $fechaDesdePlanif[0];
        $arrayParametros["search_fechaHasta"]        = $fechaHastaPlanif[0];
        $arrayParametros["arrayJurisdicciones"]      = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]      = $strIdJurisdiccion;
        $arrayParametros["limite"]                   = $strLimite;

        $respuestaSolicitudes = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->getJsonFactibilidadNodoCliente($arrayParametros);        
        
        $respuesta->setContent($respuestaSolicitudes);
        
        return $respuesta;
    }

    public function cmp($strA, $strB) 
    {
        return strcmp($strA->jurisdiccion, $strB->jurisdiccion);
    }

    public function ajaxConsultaPlanificacionNodoComboAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRespuesta            = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion             = $this->get('request');
        $strCodEmpresa           = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
		
        $arrayFechaDesdePlanif     = explode('T',$objPeticion->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif     = explode('T',$objPeticion->get('fechaHastaPlanif'));
        $strTxtEstado               = $objPeticion->get('txtEstado');
        $strNombreNodo           = $objPeticion->get('txtNodo');
        $strUserCrea             = $objPeticion->get('txtUser');
        $intStart                = $objPeticion->get('start');
        $intLimit                = $objPeticion->get('limit');
        $strIdJurisdiccion       = $objPeticion->get('id_jurisdiccion');
        $strJurisdiccion = $objPeticion->get('query');  
        $strLimite = $objPeticion->get('limite');
        $objEmComercial                   = $this->getDoctrine()->getManager("telconet");
        $arrayJurisdiccionesId = array();

        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $objEmComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $objPeticion->getSession()->get('idPersonaEmpresaRol');
        $intIdCaracteristica = $entityCaracteristica->getId();
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
                                        
        $arrayJurisdiccionesEmpresaId = array();
        $arrayResponse = array();
        $arrayJurisdiccionesEmpresa = $objEmComercial->getRepository('schemaBundle:AdmiJurisdiccion')
        ->getResultadoJurisdiccionesPorEmpresaSinEstado($strCodEmpresa, $strJurisdiccion);
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

        $objSolicitud = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD EDIFICACION',
                                                                  'estado'              =>'Activo'));
        $arrayParametros                             = array();
        $arrayParametros["idSolicitud"]              = $objSolicitud->getId();
        $arrayParametros["codEmpresa"]               = $strCodEmpresa;
        $arrayParametros["estado"]                   = $strTxtEstado;        
        $arrayParametros["nombreNodo"]               = $strNombreNodo;
        $arrayParametros["userCrea"]                 = $strUserCrea;
        $arrayParametros["start"]                    = $intStart;
        $arrayParametros["limit"]                    = $intLimit;
        $arrayParametros["search_fechaDesde"]        = $arrayFechaDesdePlanif[0];
        $arrayParametros["search_fechaHasta"]        = $arrayFechaHastaPlanif[0];
        $arrayParametros["arrayJurisdicciones"]      = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]      = $strIdJurisdiccion;
        $arrayParametros["jurisdiccion"]             = $strJurisdiccion;
        $arrayParametros["limite"]                   = $strLimite;

        $objRespuestaSolicitudes = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->getJsonFactibilidadNodoCliente($arrayParametros);        
        
        $objRespuesta->setContent($objRespuestaSolicitudes);
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_323-3358")
     * 
     * rechazarSolicitudNodoClienteAction
     * rechaza la solicitud de nodo cliente
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function rechazarSolicitudNodoClienteAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $idSolicitud        = $peticion->get('idSolicitud');
        $idElemento         = $peticion->get('idElemento');
        $id_motivo          = $peticion->get('id_motivo');
        $observacion        = $peticion->get('observacion');
        $session            = $peticion->getSession();
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entityDetalleSolicitud = $em_infraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($idSolicitud);

        $em_infraestructura->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            if($entityDetalleSolicitud)
            {
                //actualizo la solicitud
                $entityDetalleSolicitud->setMotivoId($id_motivo);
                $entityDetalleSolicitud->setObservacion($observacion);
                $entityDetalleSolicitud->setUsrRechazo($session->get('user'));
                $entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                $entityDetalleSolicitud->setEstado("Rechazada");
                $em_infraestructura->persist($entityDetalleSolicitud);
                $em_infraestructura->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $entityDetalleSolHist->setEstado('FactibilidadEnProceso');
                $entityDetalleSolHist->setObservacion($observacion);
                $em_infraestructura->persist($entityDetalleSolHist);
                $em_infraestructura->flush();

                //actualizo el elemento
                $objElemento = $em_infraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($idElemento);
                $objElemento->setObservacion('Edificacion rechazada en la factibilidad. ' . $observacion);
                $objElemento->setEstado("Rechazada");
                $em_infraestructura->persist($entityDetalleSolicitud);
                $em_infraestructura->flush();

                //Historial elemento
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElemento);
                $objInfoHistorialElemento->setObservacion('Edificacion rechazada en la factibilidad');
                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objInfoHistorialElemento->setUsrCreacion($session->get('user'));
                $objInfoHistorialElemento->setIpCreacion($peticion->getClientIp());
                $objInfoHistorialElemento->setEstadoElemento('Rechazada');
                $em_infraestructura->persist($objInfoHistorialElemento);
                $em_infraestructura->flush();

                //rechazo todos los puntos que estan asociados a este nodo cliente
                $entityPuntos = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findByElementoId($idElemento);
                foreach($entityPuntos as $idPunto)
                {
                    $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneById($idPunto->getPuntoId());

                    if($objPunto->getEstado() == 'PendienteEdif')
                    {
                        $objPunto->setEstado('Anulado');
                        $objPunto->setFeUltMod(new \DateTime('now'));
                        $objPunto->setUsrUltMod($session->get('user'));
                        $objPunto->setObservacion('Se rechazó desde la factibilidad Edificacion.');
                        $emComercial->persist($objPunto);
                        $emComercial->flush();
                    }
                }
                $respuesta->setContent("OK");
            }
            else
            {
                $respuesta->setContent("No existe la solicitud");
            }
            $em_infraestructura->getConnection()->commit();
            $emComercial->getConnection()->commit();
        }
        catch(\Exception $e)
        {

            $em_infraestructura->getConnection()->rollback();
            $emComercial->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

    /**
     * calculaMetrajeEntrePuntoElementoAction, muestra el metraje entre el punto y un elemento.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 19-05-2016
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function calculaMetrajeEntrePuntoElementoAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        $objResponse            = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $intIdPunto             = $objRequest->get('intIdPunto');
        $intIdElemento          = $objRequest->get('intIdElemento');

        $emComercial            = $this->getDoctrine()->getManager();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $arrayParametrosLatLong = array();
        $objReturnResponse->setRegistros(0);

        //Termina el metodo si no tiene el id del punto
        if(empty($intIdPunto))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No se esta enviando el id del punto.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        //Termina el metodo si no tiene id de elemento
        if(empty($intIdElemento))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No se esta enviando el id del elemento.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        //Busca la informacion del punto
        $entityInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);

        //Termina el metodo si no existe el punto
        if(!$entityInfoPunto)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No encontro punto.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        
        //Termina el metodo si el punto no tiene latitud o longitud
        if(!$entityInfoPunto->getLatitud() || !$entityInfoPunto->getLongitud())
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Punto no tiene latitud o longitud, favor revisar.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        
        $arrayParametrosLatLong['floatLatitudFrom']  = $entityInfoPunto->getLatitud();
        $arrayParametrosLatLong['floatLongitudFrom'] = $entityInfoPunto->getLongitud();

        $arrayParametros                              = array();
        $arrayParametros['arrayEmpresaElementoUbica'] = ['arrayEmpresaCod' => [$objSession->get('idEmpresa')]];
        $arrayParametros['arrayElemento']             = ['arrayIdElemento' => [$intIdElemento]];

        //Busca la ubicacion de un elemento
        $objJsonInfoElementoUbicacion = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getResultadoUbicacionElemento($arrayParametros);

        //Itera los registros
        foreach($objJsonInfoElementoUbicacion->getRegistros() as $objInfoElementoUbicacion):
            $intLatitudElemento  = $objInfoElementoUbicacion['floatLatitudUbicacion'];
            $intLongitudElemento = $objInfoElementoUbicacion['floadLongitudUbicacion'];
            $strNombreElemento   = $objInfoElementoUbicacion['strNombreElemento'];
        endforeach;

        //Termina el metodo si el elemento no tiene latitud o longitud
        if(!$intLatitudElemento || !$intLongitudElemento)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Elemento ' . $strNombreElemento . 
                                                    ' no tiene latitud o longitud, favor revisar.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        
        $arrayParametrosLatLong['floatLatitudTo']   = $intLatitudElemento;
        $arrayParametrosLatLong['floatLongitudTo']  = $intLongitudElemento;
        $floatMetraje                               = round($serviceInfoElemento->haversineGreatCircleDistance($arrayParametrosLatLong), 2);
        
        
        $objReturnResponse->setRegistros($floatMetraje);
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));

        return $objResponse;
    } //calculaMetrajeEntrePuntoElementoAction

    /**
     * @Secure(roles="ROLE_323-3359")
     * 
     * factibilidadPuntoClienteAction
     * Aprueba la solicitud de nodo cliente
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function factibilidadPuntoClienteAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');

        $jsonCajas = json_decode($peticion->get('datosCajas'));
        $totalCajas = $jsonCajas->total;
        $arrayCajas = $jsonCajas->data;

        $session = $peticion->getSession();
        $idSolicitud = $peticion->get('idSolicitud');

        $idElemento = $peticion->get('idElemento');

        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em = $this->getDoctrine()->getManager('telconet');
        $entityDetalleSolicitud = $em_infraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($idSolicitud);

        $em_infraestructura->getConnection()->beginTransaction();
        $em->getConnection()->beginTransaction();
        try
        {
            if($entityDetalleSolicitud)
            {
                for($i = 0; $i < count($arrayCajas); $i++)
                {
                    $objCaja = $em_infraestructura->getRepository('schemaBundle:InfoElemento')->findOneByNombreElemento(trim($arrayCajas[$i]));
                    //relacion elemento
                    $objRelacionElemento = new InfoRelacionElemento();
                    $objRelacionElemento->setElementoIdA($idElemento);
                    $objRelacionElemento->setElementoIdB($objCaja->getId());
                    $objRelacionElemento->setTipoRelacion("CONTIENE");
                    $objRelacionElemento->setObservacion("Nodo Cliente contiene Caja");
                    $objRelacionElemento->setEstado("Activo");
                    $objRelacionElemento->setUsrCreacion($session->get('user'));
                    $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                    $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                    $em_infraestructura->persist($objRelacionElemento);
                }

                //actualizo la solicitud
                $entityDetalleSolicitud->setEstado("Finalizada");
                $em_infraestructura->persist($entityDetalleSolicitud);
                $em_infraestructura->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $entityDetalleSolHist->setEstado('Finalizada');
                $em_infraestructura->persist($entityDetalleSolHist);
                $em_infraestructura->flush();

                //actualizo el elemento
                $objElemento = $em_infraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($idElemento);
                $objElemento->setObservacion('Edificación aprobada en la factibilidad por ' . $session->get('user'));
                $objElemento->setEstado("Activo");
                $em_infraestructura->persist($objElemento);
                $em_infraestructura->flush();

                //Historial elemento
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElemento);
                $objInfoHistorialElemento->setObservacion('Edificación aprobada en la factibilidad');
                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objInfoHistorialElemento->setUsrCreacion($session->get('user'));
                $objInfoHistorialElemento->setIpCreacion($peticion->getClientIp());
                $objInfoHistorialElemento->setEstadoElemento('Activo');
                $em_infraestructura->persist($objInfoHistorialElemento);
                $em_infraestructura->flush();
                
                //Asigno estado Pendiente todos los puntos que estan asociados a este nodo cliente
                $entityPuntos = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findByElementoId($idElemento);
                foreach($entityPuntos as $value)
                {
                    $objPunto = $em->getRepository('schemaBundle:InfoPunto')->findOneById($value->getPuntoId());
                    
                    if ($objPunto->getEstado()=='PendienteEdif')
                    {
                        $objPunto->setEstado('Pendiente');
                        $objPunto->setFeUltMod(new \DateTime('now'));
                        $objPunto->setUsrUltMod($session->get('user'));
                        $objPunto->setObservacion('Se asigno Pendiente desde la factibilidad de Edificacion.');
                        $em->persist($objPunto);
                        $em->flush();
                    }                   
                }	
                

                $respuesta->setContent("OK");
            }
            else
            {
                $respuesta->setContent("No existe solicitud");
            }

            $em_infraestructura->getConnection()->commit();
            $em->getConnection()->commit();

        }
        catch(\Exception $e)
        {
            $em_infraestructura->getConnection()->rollback();
            $em->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_323-3357")
     * 
     * fechaFactibilidadAction
     * Actualiza la fecha cuando se realizará la instalación del nodo cliente
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-05-2016 - Se guarda toda la información de la factibilidad en la observación del historial del servicio y la fecha en el historial del 
     * detalle de la solicitud
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 21-06-2016 Se valido el servicio y la fecha de creacion
     * 
     */
    public function fechaFactibilidadAction()
    {        
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $session            = $this->get('request')->getSession();
        $peticion           = $this->get('request');
        $idSolicitud        = $peticion->get('idSolicitud');
        $observacion        = $peticion->get('observacion');
        $fechaProgramacion  = explode('T', $peticion->get('fechaProgramacion'));
        $dateF              = explode("-", $fechaProgramacion[0]);
        $fechaCreacionTramo = new \DateTime(date("Y/m/d G:i:s", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0])));

        $em = $this->getDoctrine()->getManager();

        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($idSolicitud);
        $objElemento = $em->getRepository('schemaBundle:InfoElemento')->findOneById($entityDetalleSolicitud->getElementoId());

        $em->getConnection()->beginTransaction();

        try
        {

            if($entityDetalleSolicitud)
            {
                //GUARDA INFO SERVICIO
                $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
			
                if($fechaCreacionTramo)
                {
                    //GUARDA INFO DETALLE SOLICITUD
                    $entityDetalleSolicitud->setObservacion($observacion);
                    $entityDetalleSolicitud->setFeEjecucion($fechaCreacionTramo);
                    $entityDetalleSolicitud->setEstado("FactibilidadEnProceso");
                    $em->persist($entityDetalleSolicitud);
                    $em->flush();
                    
                    if($entityServicio)
                    {
                        $entityServicioHistorial = new InfoServicioHistorial();  
                        $entityServicioHistorial->setServicioId($entityServicio);
                        $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
                        $entityServicioHistorial->setEstado('FactibilidadEnProceso');
                        $fechaTramo=$dateF[2]."/".$dateF[1]."/".$dateF[0];
                        $entityServicioHistorial->setObservacion($observacion."<br>Fecha Factibilidad Edificación: ".$fechaTramo); 
                        $em->persist($entityServicioHistorial);
                        $em->flush(); 
                    }
                    
                    
                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($session->get('user'));
                    $entityDetalleSolHist->setEstado('FactibilidadEnProceso');
                    $entityDetalleSolHist->setObservacion($observacion);
                    $entityDetalleSolHist->setFeIniPlan(new \DateTime('now'));
                    $entityDetalleSolHist->setFeFinPlan($fechaCreacionTramo);
                    $em->persist($entityDetalleSolHist);
                    $em->flush();

                    $respuesta->setContent("OK");

                    //se envia correo
                    $envioPlantilla1 = $this->get('soporte.EnvioPlantilla');
                    $asunto = "Fecha Factibilidad Edificación " . $objElemento->getNombreElemento();
                    $parametrosSolicitud = array('nombreElemento'   => $objElemento->getNombreElemento(),
                                                 'modeloElemento'   => $objElemento->getModeloElementoId()->getNombreModeloElemento(),
                                                 'observacion'      => $observacion,
                                                 'fecha'            => date_format($fechaCreacionTramo, 'Y-m-d'));
                    $envioPlantilla1->generarEnvioPlantilla($asunto, '', 'NFFE', $parametrosSolicitud, '', '', '');
                }
                else
                {
                    $respuesta->setContent("Ingrese la fecha de factibilidad.");
                }
            }
            else
                $respuesta->setContent("No existe Solicitud");

            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }
    
    /**
     * ajaxGetInfoPadreRadioAction, obtiene la informacion del elemento padre de la radio Enviada como parametro
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-05-2016 
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 24-08-2016  Se agrega codigo para recuperación de información utilizada en procesos de Cambios de Um Radio Tn
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @Secure(roles="ROLE_135-91")
     */
    public function ajaxGetInfoPadreRadioAction()
    {
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objRequest         = $this->get('request');
        $intIdElementoRadio = $objRequest->get('intIdElementoRadio');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strDetalleAnillo   = "";
        $arrayParametrosWs  = array();
        //se recupera interface de la radio de backbone
        $objInterfaceElementoRadio = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                       ->findOneBy(array("elementoId"              => $intIdElementoRadio,
                                                                         "nombreInterfaceElemento" => "wlan1",
                                                                         "estado"                  => "connected"));
        /* @var $serviceServicioTecnico InfoServicioTecnicoService */
        $serviceServicioTecnico    = $this->get('tecnico.InfoServicioTecnico');
        
        if ($objInterfaceElementoRadio)
        {
            /* se recupera enlace con la interface de la radio de backbone para poder obterne el SW y el Puerto en el cual esta 
               conectado este elemento */
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                           ->findOneBy( array("interfaceElementoFinId" => $objInterfaceElementoRadio->getId(),
                                                              "estado"                 => 'Activo' ));
            if($objEnlace)
            {
                //se recupera Objeto Interface del SW al cual esta conectado la radio de backbone
                $objInterfaceElementoSwitch = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objEnlace->getInterfaceElementoIniId());
                //se recupera SW al cual esta enlazado la radio de backbone
                $objInfoElementoSwitch      = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objInterfaceElementoSwitch->getElementoId());
                
                
                 //OBTENER PE 
                if($objInfoElementoSwitch)
                {
                    try
                    {
                        $arrayParametrosWs["intIdElemento"] = $objInfoElementoSwitch->getId();
                        $arrayParametrosWs["intIdServicio"] = "";

                        $objElementoPe = $serviceServicioTecnico->getPeBySwitch($arrayParametrosWs);

                        if(is_object($objElementoPe))
                        {
                            $idElementoPe     = $objElementoPe->getId();
                            $nombreElementoPe = $objElementoPe->getNombreElemento();
                        }
                        else
                        {
                            $idElementoPe     = "";
                            $nombreElementoPe = "";
                        }
                    }
                    catch(\Exception $e)
                    {
                        $idElementoPe     = "";
                        $nombreElementoPe = $e->getMessage();
                    }

                    //OBTENER ANILLO PE
                    $objAnillo = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                   ->findOneBy(array("elementoId"       => $objInfoElementoSwitch->getId(),
                                                                     "detalleNombre"    => "ANILLO",
                                                                     "estado"           => "Activo"));
                    if(is_object($objAnillo))
                    {
                        $strDetalleAnillo = $objAnillo->getDetalleValor();
                    }
                }
                
                if (is_object($objElementoPe) && is_object($objAnillo))
                {
                    $response = array( 'tipoElementoPadre' => 'SWITCH',
                                       'idElemento'        => $objInfoElementoSwitch->getId(),
                                       'nombreElemento'    => $objInfoElementoSwitch->getNombreElemento(),
                                       'idLinea'           => $objInterfaceElementoSwitch->getId(),
                                       'linea'             => $objInterfaceElementoSwitch->getNombreInterfaceElemento(),
                                       'idPe'              => $idElementoPe,
                                       'nombrePe'          => $nombreElementoPe,
                                       'anilloPe'          => $strDetalleAnillo,
                                       'error'             => 0,
                                       'msg'               => ""
                                     );
                }
                else
                {
                    $response = array( 'tipoElementoPadre' => '',
                                       'idElemento'        => '',
                                       'nombreElemento'    => '',
                                       'idLinea'           => '',
                                       'linea'             => '',
                                       'idPe'              => '',
                                       'nombrePe'          => '',
                                       'anilloPe'          => '',
                                       'error'             => 1,
                                       'msg'               => "No se logro recuperar información de PE"
                                     );
                }
            }
            else
            {
                $response = array(  'tipoElementoPadre'       => '',
                                    'idElemento'              => '',
                                    'nombreElemento'          => '',
                                    'idLinea'                 => '',
                                    'linea'                   => '',
                                    'idPe'                    => '',
                                    'nombrePe'                => '',
                                    'anilloPe'                => '',
                                    'error' => 1,
                                    'msg' => "No se encontro Enlace hacia el Switch"
                                 );
            }
        }
        else
        {
            $response = array('tipoElementoPadre' => '',
                              'idElemento'        => '',
                              'nombreElemento'    => '',
                              'idLinea'           => '',
                              'linea'             => '',
                              'idPe'              => '',
                              'nombrePe'          => '',
                              'anilloPe'          => '',
                              'error'             => 1,
                              'msg'               => "No se encontro Interface de Radio en estado connected"
                             );
        }
        
        $response = json_encode($response);

        $objResponse->setContent($response);

        return $objResponse;
    } //ajaxGetInfoPadreRadioAction
    
   /**
    * @Secure(roles="ROLE_135-95")
    * 
    * 
    * ajaxGuardaFactibilidadRadioTnAction
    * guarda factibilidad para clientes radio TN
    * 
    * @return Response con mensaje de respuesta de transaccion
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0
    * 
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 2.0  Se agregan validaciones para poder reutilizar metodo en proceso de Edición de factibilidad
    * 
    * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 2.1 Se agrega el envío de correo al guardar la factibilidad para clientes radio TN
    * 
    * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
    * @version 2.2 Se agrega flujo para asignación de factibilidad de servicio RADIO TN a los cuales se les asigno una factibilidad anticipada
    * @since 2.1
    * 
    * @since 1.0
    */
    public function ajaxGuardaFactibilidadRadioTnAction()
    {
        $respuesta                       = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $session                         = $this->get('request')->getSession();
        $peticion                        = $this->get('request');
        $esTercerizada                   = $peticion->get('esTercerizada');
        $idTercerizadora                 = $peticion->get('tercerizadora');
        $id                              = $peticion->get('id');
        $intElementoRadioId              = $peticion->get('elemento_radio_id') ? $peticion->get('elemento_radio_id') : 0;
        $intElementoSwitchId             = $peticion->get('elemento_switch_id') ? $peticion->get('elemento_switch_id') : 0;
        $intInterfaceElementoSwitchId    = $peticion->get('interface_elemento_switch_id') ? $peticion->get('interface_elemento_switch_id') : 0;
        $strProcesoFactibilidad          = $peticion->get('procesoFactibilidad') ? $peticion->get('procesoFactibilidad') : 'factibilidadManual';
        $em                              = $this->getDoctrine()->getManager();
        $em_infraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
        $entityDetalleSolicitud          = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
        $strObservacionFactibilidad      = "";
        $strDatosNuevos                  = "";
        $strDatosAntiguos                = "";
        $strInferfaceSwOcupada           = "";
        $idEmpresa                       = $session->get('idEmpresa');
        $strUsrCreacion                  = $session->get('user');

        $em->getConnection()->beginTransaction();
        $em_infraestructura->getConnection()->beginTransaction();
		
        try
        {
            if($entityDetalleSolicitud)
            {         
                //GUARDA INFO SERVICIO
                $entityServicio = $entityDetalleSolicitud->getServicioId();

                if($intElementoSwitchId != 0 && $intElementoSwitchId) 
                {
                    $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneByServicioId($entityServicio->getId());
                    
                    //se recupera info del servicio tecnico del cliente en caso de tener
                    $intInterfaceElementoIdAnt = $entityServicioTecnico->getInterfaceElementoId();
                    $intElementoConectorIdAnt  = $entityServicioTecnico->getElementoConectorId();
                    
                    $entityServicioTecnico->setElementoId($intElementoSwitchId);
                    $entityServicioTecnico->setInterfaceElementoId($intInterfaceElementoSwitchId);
                    $entityServicioTecnico->setElementoConectorId($intElementoRadioId);
                    if($esTercerizada=='S')
                    {
                        $entityServicioTecnico->setTercerizadoraId($idTercerizadora);
                    }
                    $em->persist($entityServicioTecnico);
                    $em->flush();
                    
                    $objInterfaceElementoSw = $em_infraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->find($intInterfaceElementoSwitchId);
                    $objElemento            = $em_infraestructura->getRepository('schemaBundle:InfoElemento')
                                                                 ->find($intElementoRadioId);
                    //se arma mensaje de factibilidad que sera registrado en el historial del servicio
                    $strDatosNuevos  = "Datos Nuevos:<br>";
                    $strDatosNuevos .= "Switch         : " . $objInterfaceElementoSw->getElementoId() . "<br>";
                    $strDatosNuevos .= "Puerto         : " . $objInterfaceElementoSw . "<br>";
                    $strDatosNuevos .= "Radio BackBone : " . $objElemento . "<br>";
                    
                    if($strProcesoFactibilidad == "factibilidadManualAnticipada")
                    {
                        $strEstadoRegistros = "PrePlanificada";
                    }
                    else
                    {
                        $strEstadoRegistros = "Factible";
                    }

                    //se debe agrear historial de solicitar planificacion cambiar esto de servicio a preplanificada, cambiar estado de solicitud 
                    //de factibilidad cambiar estado de solicitud de planificacion
                    if ($strProcesoFactibilidad == "factibilidadManual" || $strProcesoFactibilidad == "factibilidadManualAnticipada")
                    {
                        $strObservacionFactibilidad  = "Servicio Factible<br><br>";
                        $strObservacionFactibilidad .= $strDatosNuevos;
                        $entityServicio->setEstado($strEstadoRegistros);
                        $em->persist($entityServicio);
                        $em->flush();

                        //GUARDAR INFO SERVICIO HISTORIAL
                        $entityServicioHistorial = new InfoServicioHistorial();  
                        $entityServicioHistorial->setServicioId($entityServicio);	
                        $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setObservacion($strObservacionFactibilidad);
                        $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
                        $entityServicioHistorial->setEstado($strEstadoRegistros); 
                        $em->persist($entityServicioHistorial);
                        $em->flush(); 

                        //GUARDA INFO DETALLE SOLICITUD
                        $entityDetalleSolicitud->setEstado("Factible");
                        $em->persist($entityDetalleSolicitud);
                        $em->flush();  

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);            
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolHist->setEstado('Factible');  
                        $em->persist($entityDetalleSolHist);
                        $em->flush();
                        
                        //se busca solicitud de planificacion de servicio RADIO TN al cual le fue asignada una factibilidad anticipada, si existe
                        //esta solicitud se procede a cambiar a esta Preplanificada
                        if ($strProcesoFactibilidad == "factibilidadManualAnticipada")
                        {
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
                                    $objSolicitudPlanficacion->setEstado($strEstadoRegistros);  
                                    $em->persist($objSolicitudPlanficacion);
                                    $em->flush();
                                    
                                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                    $objDetalleSolHist = new InfoDetalleSolHist();
                                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitudPlanficacion);            
                                    $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                    $objDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                                    $objDetalleSolHist->setEstado($strEstadoRegistros);  
                                    $em->persist($objDetalleSolHist);
                                    $em->flush();
                                }
                            }
                            
                            //si los puertos del switch son diferentes se deben realizar las validaciones respectivas para proceder a liberar
                            //el puerto reservado del switch para esta factibilidad anticipada
                            if ($intInterfaceElementoIdAnt != $intInterfaceElementoSwitchId)
                            {
                                $intCantidadServicios      = 0;
                                $objInterfaceElementoSwAnt = $em_infraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                ->find($intInterfaceElementoIdAnt);
                                if (is_object($objInterfaceElementoSwAnt) && $objInterfaceElementoSwAnt->getEstado() == 'reserved')
                                {
                                    $arrayServiciosTecnicos = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                                 ->findBy(array( "interfaceElementoId" =>$intInterfaceElementoIdAnt));
                                    foreach($arrayServiciosTecnicos as $objServicioTecnico)
                                    {
                                        $objServicio       = $objServicioTecnico->getServicioId();
                                        $strEstadoServicio = $objServicio->getEstado();
                                        if($strEstadoServicio == "Factibilidad-anticipada" || $strEstadoServicio == "Asignar-factibilidad")
                                        {
                                            $intCantidadServicios++;
                                        }
                                    }
                                    
                                    if ($intCantidadServicios <= 1)
                                    {
                                        // se reserva puerto del switch
                                        $objInterfaceElementoSwAnt->setEstado('not connect');
                                        $objInterfaceElementoSwAnt->setUsrUltMod($peticion->getSession()->get('user'));
                                        $objInterfaceElementoSwAnt->setFeUltMod(new \DateTime('now'));
                                        $em_infraestructura->persist($objInterfaceElementoSwAnt);
                                        $em_infraestructura->flush(); 
                                    }
                                }
                            }
                        }
                        
                        /* Generación del envío de correo al aprobar la solicitud de Factibilidad
                         * Se obtiene el vendedor del servicio para agregarlo como destinatario del correo que se enviará al aprobar
                         * la factibilidad
                         */
                        if($strProcesoFactibilidad == "factibilidadManualAnticipada")
                        {
                            $asunto ="Asignacion de Factibilidad de Instalacion #".$entityDetalleSolicitud->getId().", servicio Preplanificado";
                        }
                        else
                        {
                            $asunto ="Aprobacion de Solicitud de Factibilidad de Instalacion #".$entityDetalleSolicitud->getId();
                        }
                        $to     = array();
                        
                        
                        if($entityServicio->getUsrVendedor())
                        {
                            // DESTINATARIOS.... 
                            $formasContacto = $em->getRepository('schemaBundle:InfoPersona')
                                                 ->getContactosByLoginPersonaAndFormaContacto(  $entityServicio->getUsrVendedor(),
                                                                                                'Correo Electronico');
                            if($formasContacto)
                            {
                                foreach($formasContacto as $formaContacto)
                                {
                                    $to[] = $formaContacto['valor'];
                                }
                            }
                        }
                        
                        /* Envío de correo por medio de plantillas
                         * Se obtiene la plantilla y se invoca a la función generarEnvioPlantilla del service que internamente obtiene
                         * los alias asociados a la plantilla 'APROBAR_FACTIB' y envía el respectivo correo
                         */
                        /* @var $envioPlantilla EnvioPlantilla */
                        $arrayParametros    = array('detalleSolicitud' => $entityDetalleSolicitud,'usrAprueba'=>$strUsrCreacion);
                        $envioPlantilla     = $this->get('soporte.EnvioPlantilla');
                        $envioPlantilla->generarEnvioPlantilla( $asunto, 
                                                                $to, 
                                                                'APROBAR_FACTIB', 
                                                                $arrayParametros,
                                                                $idEmpresa,
                                                                '',
                                                                '',
                                                                null, 
                                                                true,
                                                                'notificaciones_telcos@telconet.ec');
                    }
                    else if ($strProcesoFactibilidad == "edicionFactibilidad")
                    {
                        $objInterfaceElementoSwAnt = $em_infraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($intInterfaceElementoIdAnt);
                        $objElementoAnt            = $em_infraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($intElementoConectorIdAnt);
                    
                        $strDatosAntiguos  = "<br>Datos Anteriores:<br>";
                        $strDatosAntiguos .= "Switch         : " . $objInterfaceElementoSwAnt->getElementoId() . "<br>";
                        $strDatosAntiguos .= "Puerto         : " . $objInterfaceElementoSwAnt . "<br>";
                        $strDatosAntiguos .= "Radio BackBone : " . $objElementoAnt . "<br>";
                        
                        $strObservacionFactibilidad  = "Edición de Factibilidad<br><br>";
                        $strObservacionFactibilidad .= $strDatosNuevos;
                        $strObservacionFactibilidad .= $strDatosAntiguos;
                        
                        
                        //GUARDAR INFO SERVICIO HISTORIAL
                        $entityServicioHistorial = new InfoServicioHistorial();  
                        $entityServicioHistorial->setServicioId($entityServicio);	
                        $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setObservacion($strObservacionFactibilidad);
                        $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
                        $entityServicioHistorial->setEstado($entityServicio->getEstado()); 
                        $em->persist($entityServicioHistorial);
                        $em->flush(); 
                    }

                    $em->getConnection()->commit();
                    $em_infraestructura->getConnection()->commit();
                    
                    $respuesta->setContent("Se modifico Correctamente el detalle de la Solicitud de Factibilidad");    
                }
                else
                {
                    $respuesta->setContent("No escogio un Elemento de la Lista.");                      
                }	
            }
            else
            {
                $respuesta->setContent("No existe el detalle de Solicitud");
            }
        }
        catch(\Exception $e)
        {
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
                $respuesta->setContent($mensajeError);
        }

        $em->getConnection()->close();
        $em_infraestructura->getConnection()->close();
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_267-5477")
    * 
    * 
    * ajaxGuardaFactibilidadAnticipadaRadioTnAction
    * guarda factibilidad anticipada para clientes radio TN
    * 
    * @return Response con mensaje de respuesta de transaccion
    *
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0
    * @since 1.0
    */
    public function ajaxGuardaFactibilidadAnticipadaRadioTnAction()
    {
        $objRespuesta                    = new JsonResponse();
        $objPeticion                     = $this->get('request');
        $objSession                      = $this->get('request')->getSession();
        $intIdSolicitud                  = $objPeticion->get('id');
        $intElementoSwitchId             = $objPeticion->get('elemento_switch_id') ? $objPeticion->get('elemento_switch_id') : 0;
        $intInterfaceElementoSwitchId    = $objPeticion->get('interface_elemento_switch_id') ? $objPeticion->get('interface_elemento_switch_id') : 0;
        $emComercial                     = $this->getDoctrine()->getManager();
        $emInfraestructura               = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strObservacionFactibilidad      = "";
        $strDatosNuevos                  = "";
        $booleanStatus                   = false;
        $strMensaje                      = "";
        $strIpCreacion                   = $objPeticion->getClientIp();
        $strUsrCreacion                  = $objPeticion->getSession()->get('user');
        $intIdEmpresa                    = $objSession->get('idEmpresa');
        $serviceUtil                     = $this->get('schema.Util');
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
		
        try
        {
            $objDetalleSolicitud        = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            $objElementoSwitch          = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($intElementoSwitchId);
            $objInterfaceElementoSwitch = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->find($intInterfaceElementoSwitchId);
            
            if(is_object($objDetalleSolicitud) && is_object($objElementoSwitch) && is_object($objInterfaceElementoSwitch))
            {         
                //GUARDA INFO SERVICIO
                $objServicio = $objDetalleSolicitud->getServicioId();

                $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->findOneByServicioId($objServicio->getId());

                $objServicioTecnico->setElementoId($intElementoSwitchId);
                $objServicioTecnico->setInterfaceElementoId($intInterfaceElementoSwitchId);
                $emComercial->persist($objServicioTecnico);
                $emComercial->flush();

                $objInterfaceElementoSw = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->find($intInterfaceElementoSwitchId);
                //se arma mensaje de factibilidad que sera registrado en el historial del servicio
                $strDatosNuevos  = "Datos Factibilidad Anticipada:<br>";
                $strDatosNuevos .= "Switch         : " . $objInterfaceElementoSw->getElementoId() . "<br>";
                $strDatosNuevos .= "Puerto         : " . $objInterfaceElementoSw . "<br>";

                $strObservacionFactibilidad  = "Servicio con Factibilidad Anticipada<br><br>";
                $strObservacionFactibilidad .= $strDatosNuevos;
                $objServicio->setEstado("Factibilidad-anticipada");
                $emComercial->persist($objServicio);
                $emComercial->flush();

                //GUARDAR INFO SERVICIO HISTORIAL
                $objServicioHistorial = new InfoServicioHistorial();  
                $objServicioHistorial->setServicioId($objServicio);	
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setObservacion($strObservacionFactibilidad);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);	
                $objServicioHistorial->setEstado('Factibilidad-anticipada'); 
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush(); 

                //GUARDA INFO DETALLE SOLICITUD
                $objDetalleSolicitud->setEstado("Factibilidad-anticipada");
                $emComercial->persist($objDetalleSolicitud);
                $emComercial->flush();  

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);            
                $objDetalleSolHist->setIpCreacion($strIpCreacion);
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHist->setEstado('Factibilidad-anticipada');  
                $emComercial->persist($objDetalleSolHist);
                $emComercial->flush();
                
                // se reserva puerto del switch
                $objInterfaceElementoSw->setEstado('reserved');
                $objInterfaceElementoSw->setUsrUltMod($strUsrCreacion);
                $objInterfaceElementoSw->setFeUltMod(new \DateTime('now'));
                $emInfraestructura->persist($objInterfaceElementoSw);
                $emInfraestructura->flush();   


                /* Generación del envío de correo al aprobar la solicitud de Factibilidad
                 * Se obtiene el vendedor del servicio para agregarlo como destinatario del correo que se enviará al aprobar
                 * la factibilidad
                 */
                $strAsunto  = "Aprobacion de Solicitud de Factibilidad anticipada de Instalacion #".$objDetalleSolicitud->getId();
                $arrayTo    = array();


                if($objServicio->getUsrVendedor())
                {
                    // DESTINATARIOS.... 
                    $arrayFormasContacto = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                       ->getContactosByLoginPersonaAndFormaContacto( $objServicio->getUsrVendedor(),
                                                                                                     'Correo Electronico');
                    if($arrayFormasContacto)
                    {
                        foreach($arrayFormasContacto as $formaContacto)
                        {
                            $arrayTo[] = $formaContacto['valor'];
                        }
                    }
                }

                /* Envío de correo por medio de plantillas
                 * Se obtiene la plantilla y se invoca a la función generarEnvioPlantilla del service que internamente obtiene
                 * los alias asociados a la plantilla 'APROBAR_FACTIB' y envía el respectivo correo
                 */
                /* @var $envioPlantilla EnvioPlantilla */
                $arrayParametros       = array('detalleSolicitud' => $objDetalleSolicitud,'usrAprueba'=>$strUsrCreacion);
                $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                $serviceEnvioPlantilla->generarEnvioPlantilla(  $strAsunto, 
                                                                $arrayTo, 
                                                                'APROBAR_FACTIB', 
                                                                $arrayParametros,
                                                                $intIdEmpresa,
                                                                '',
                                                                '',
                                                                null, 
                                                                true,
                                                                'notificaciones_telcos@telconet.ec');
                
                $booleanStatus = true;
                $strMensaje    = "Ha sido asignada la factibilidad anticipada exitosamente.";
                $emComercial->getConnection()->commit();
                $emInfraestructura->getConnection()->commit();
            }
            else
            {
                $booleanStatus = false;
                $strMensaje    = "No existe el detalle de Solicitud";
            }

           
        }
        catch(\Exception $ex)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'FactibilidadInstalacionController->ajaxGuardaFactibilidadAnticipadaRadioTnAction', 
                                      $ex->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion);    

            $booleanStatus = false;
            $strMensaje    = "Se presentaron problemas al procesar la solicitud, favor notificar a sistemas!";
        }
        
        $emComercial->getConnection()->close();
        $emInfraestructura->getConnection()->close();
        
        $objRespuesta->setData( array('success' => $booleanStatus,
                                      'msg'     => $strMensaje
                                    )
                             );
        return $objRespuesta;
    }

    /**
    * 
    * ajaxComboCajasNodoAction
    * consulta las cajas que se enlazarán al nodo cliente
    * 
    * @return json con las cajas y los id
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
	*/
    public function ajaxComboCajasNodoAction()
    {
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion   = $this->get('request');
        $nombre     = $peticion->get('query');
        $nivel      = $peticion->get('nivel');
        $idCanton     = $peticion->get('idCanton');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->getJsonCajasNodoCliente ($nombre, $nivel, $codEmpresa, $idCanton);

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
    * 
    * getElementosPseudoPeAction
    * consulta las cajas que se enlazarán al nodo cliente
    * 
    * @return json con las cajas y los id
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
	*/
    public function getElementosPseudoPeAction()
    {
        $objRespuesta      = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion    = $this->get('request');
        $strNombre      = $objPeticion->get('query');
        $strAdministra  = $objPeticion->get('administra');
        $intCodEmpresa  = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
        
        if($strAdministra == 'EMPRESA')
        {
            $arrayParametros['strTipoElemento'] = 'SWITCH';
        }
        else
        {
            $arrayParametros['strTipoElemento'] = 'ROUTER';
        }
        
        $arrayParametros['strNombre']       = $strNombre;
        $arrayParametros['intCodEmpresa']   = $intCodEmpresa;
                
        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->getJsonElementosPseudoPe($arrayParametros);

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }
    

     /**
     * @Secure(roles="ROLE_323-3359")
     * 
     * flujoPseudoPeAction
     * Cambia de estado la solicitud de factibilidad para que el área encargada asigne los equipos al Pseudo Pe
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 15-05-2017 - Se guarda referencia de tipo de administracion al edificio ( PROPIA o TERCERIZADA )
     */
    public function flujoPseudoPeAction()
    {
        
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion    = $this->get('request');
        $objSession     = $objPeticion->getSession();
        $intIdSolicitud = $objPeticion->get('idSolicitud');
        $intIdElemento  = $objPeticion->get('idElemento');
        $strAdministra  = $objPeticion->get('administra');
        $strTipoAdmin   = $objPeticion->get('tipoAdministracion');

        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em = $this->getDoctrine()->getManager('telconet');
        
        $em_infraestructura->getConnection()->beginTransaction();
        $em->getConnection()->beginTransaction();
        
        try
        {
            $objDetalleSolicitud = $em_infraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            
            if($objDetalleSolicitud)
            {
                //actualizo la solicitud
                $objDetalleSolicitud->setEstado("FactibilidadEquipos");
                $em_infraestructura->persist($objDetalleSolicitud);
                $em_infraestructura->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objDetalleSolHist->setEstado('FactibilidadEquipos');
                $em_infraestructura->persist($objDetalleSolHist);
                $em_infraestructura->flush();

                //ingreso detalles al elemento que indican que es un pseudo pe
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setEstado('Activo');
                $objInfoDetalleElemento->setElementoId($intIdElemento);
                $objInfoDetalleElemento->setDetalleNombre('ADMINISTRA');
                $objInfoDetalleElemento->setDetalleValor($strAdministra);
                $objInfoDetalleElemento->setDetalleDescripcion('ADMINISTRA');
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($objSession->get('user'));
                $objInfoDetalleElemento->setIpCreacion($objPeticion->getClientIp());
                
                $em_infraestructura->persist($objInfoDetalleElemento);
                $em_infraestructura->flush();
                
                $objInfoDetalleElemento1 = new InfoDetalleElemento();
                $objInfoDetalleElemento1->setEstado('Activo');
                $objInfoDetalleElemento1->setElementoId($intIdElemento);
                $objInfoDetalleElemento1->setDetalleNombre('TIPO_ELEMENTO_RED');
                $objInfoDetalleElemento1->setDetalleValor('PSEUDO_PE');
                $objInfoDetalleElemento1->setDetalleDescripcion('TIPO_ELEMENTO_RED');
                $objInfoDetalleElemento1->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento1->setUsrCreacion($objSession->get('user'));
                $objInfoDetalleElemento1->setIpCreacion($objPeticion->getClientIp());                

                $em_infraestructura->persist($objInfoDetalleElemento1);
                $em_infraestructura->flush();     
                
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setEstado('Activo');
                $objInfoDetalleElemento->setElementoId($intIdElemento);
                $objInfoDetalleElemento->setDetalleNombre('TIPO_ADMINISTRACION');
                $objInfoDetalleElemento->setDetalleValor($strTipoAdmin);
                $objInfoDetalleElemento->setDetalleDescripcion('TIPO_ADMINISTRACION');
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleElemento->setUsrCreacion($objSession->get('user'));
                $objInfoDetalleElemento->setIpCreacion($objPeticion->getClientIp());                

                $em_infraestructura->persist($objInfoDetalleElemento);
                $em_infraestructura->flush(); 

                $objRespuesta->setContent("OK");
            }
            else
            {
                $objRespuesta->setContent("No existe solicitud");
            }

            $em_infraestructura->getConnection()->commit();
            $em->getConnection()->commit();

        }
        catch(\Exception $e)
        {
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError('Telcos+',
                                      'flujoPseudoPe',
                                      $e->getMessage(),
                                      $objSession->get('user'),
                                      $objPeticion->getClientIp());

            $em_infraestructura->getConnection()->rollback();
            $em->getConnection()->rollback();
            $objRespuesta->setContent('Ocurrió un error, favor notificar a sistemas.');
        }

        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_323-3359")
     * 
     * Metodo encargado de mostrar las Tercerizadoras Activas para ser relacionadas a un Edificio ( pseudope )
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 15-05-2017
     */
    public function ajaxGetTercerizadorasAction()
    {
        $objRespuesta      = new JsonResponse();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion       = $this->get('request');
        $objSession        = $objPeticion->getSession();
        $strPrefijoEmpresa = $objSession->get("prefijoEmpresa") ? $objSession->get("prefijoEmpresa") : '';
        
        //Cargar los tercerizados para cuando la Ultima Milla que se escoja se trate de TERCERIZADOS
        $arrayParametrosTercerizado                      = array();
        $arrayParametrosTercerizado['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametrosTercerizado['strDescripRol']     = 'TERCERIZADORA';
        $arrayParametrosTercerizado['strEstado']         = 'Activo';
        $arrayProveedores = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                        ->getPersonasProveedorVentaExterna($arrayParametrosTercerizado);
        
        $arrayTercerizadoras = array();
        
        if(!empty($arrayProveedores))
        {
            foreach($arrayProveedores as $objPersonaRol)
            {
                $objPersona       = $objPersonaRol->getPersonaId();
                $intIdPersona     = $objPersona->getId();
                $strNombre        = $objPersona->getInformacionPersona();
                $arrayTercerizadoras[] = array('idTercerizadora'     => $intIdPersona,
                                               'nombreTercerizadora' => $strNombre);
            }
        }
        
        $objRespuesta->setData($arrayTercerizadoras);

        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_323-3359")
     * 
     * Metodo encargado de guardar la tercerizadora y relacionarla al edificio pseudope
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 15-05-2017
     */
    public function ajaxGuardarTercerizadoraAction()
    {
        $objRespuesta         = new Response();
        $objPeticion          = $this->get('request');
        $objSession           = $objPeticion->getSession();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $intIdElemento        = $objPeticion->get('idElemento');
        $intIdTercerizadora   = $objPeticion->get('idTercerizadora');
                
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
                
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
            
            if(is_object($objElemento))
            {
                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($intIdElemento);
                $objRelacionElemento->setElementoIdB($intIdTercerizadora);
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("Relacion Tercerizadora");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($objPeticion->getClientIp());
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();
                
                //Historial elemento
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElemento);
                $objInfoHistorialElemento->setObservacion('Edificación Pseudo Pe se relaciona con Tercerizadora');
                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objInfoHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objInfoHistorialElemento->setIpCreacion($objPeticion->getClientIp());
                $objInfoHistorialElemento->setEstadoElemento('Activo');
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();
                
                $objRespuesta->setContent("OK");
                
                $emInfraestructura->commit();
            }
            else
            {
                $objRespuesta->setContent("No existe referencia del PseudPe, favor notificar a Sistemas");
            }
        } 
        catch (\Exception $e) 
        {
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError('Telcos+',
                                      'ajaxGuardarTercerizadoraAction',
                                      $e->getMessage(),
                                      $objSession->get('user'),
                                      $objPeticion->getClientIp());
                        
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }
            
            $objRespuesta->setContent('Ocurrió un error, favor notificar a sistemas.');
        }
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_323-4917")
     * 
     * factibilidadPseudoPe
     * Aprueba la solicitud de nodo cliente
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function factibilidadPseudoPeAction()
    {        
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion          = $this->get('request');
        $objSession           = $objPeticion->getSession();
        $intIdSolicitud       = $objPeticion->get('idSolicitud');
        $intIdElemento        = $objPeticion->get('idElemento');
        $intIdElementoPseudo  = $objPeticion->get('idElementoPseudo');
        $intIdInterfacePseudo = $objPeticion->get('idInterface');        
        
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em = $this->getDoctrine()->getManager('telconet');

        $em_infraestructura->getConnection()->beginTransaction();
        $em->getConnection()->beginTransaction();
        
        try
        {
            $objDetalleSolicitud = $em_infraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
                    
            if($objDetalleSolicitud)
            {
                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($intIdElemento);
                $objRelacionElemento->setElementoIdB($intIdElementoPseudo);
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("Pseudo Pe contiene elemento");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($objSession->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($objPeticion->getClientIp());
                $em_infraestructura->persist($objRelacionElemento);                

                //actualizo la solicitud
                $objDetalleSolicitud->setEstado("Finalizada");
                $em_infraestructura->persist($objDetalleSolicitud);
                $em_infraestructura->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objDetalleSolHist->setEstado('Finalizada');
                $em_infraestructura->persist($objDetalleSolHist);
                $em_infraestructura->flush();

                //actualizo el elemento
                $objElemento = $em_infraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
                $objElemento->setObservacion('Edificación aprobada en la factibilidad por ' . $objSession->get('user'));
                $objElemento->setEstado("Activo");
                $em_infraestructura->persist($objElemento);
                $em_infraestructura->flush();

                //Historial elemento
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElemento);
                $objInfoHistorialElemento->setObservacion('Edificación Pseudo Pe aprobada en la factibilidad');
                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objInfoHistorialElemento->setUsrCreacion($objSession->get('user'));
                $objInfoHistorialElemento->setIpCreacion($objPeticion->getClientIp());
                $objInfoHistorialElemento->setEstadoElemento('Activo');
                $em_infraestructura->persist($objInfoHistorialElemento);
                $em_infraestructura->flush();
                
                //Asigno estado Pendiente todos los puntos que estan asociados a este nodo cliente
                $entityPuntos = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findByElementoId($intIdElemento);
                foreach($entityPuntos as $value)
                {
                    $objPunto = $em->getRepository('schemaBundle:InfoPunto')->findOneById($value->getPuntoId());
                    if($objPunto)
                    {
                        if ($objPunto->getEstado()=='PendienteEdif')
                        {
                            $objPunto->setEstado('Pendiente');
                            $objPunto->setFeUltMod(new \DateTime('now'));
                            $objPunto->setUsrUltMod($objSession->get('user'));
                            $objPunto->setObservacion('Se asigno Pendiente desde la factibilidad de Edificacion.');
                            $em->persist($objPunto);
                            $em->flush();
                        } 
                    }
                }
                
                if($intIdInterfacePseudo)
                {
                    //ingreso detalles al elemento que indican que es un pseudo pe
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setEstado('Activo');
                    $objInfoDetalleElemento->setElementoId($intIdElemento);
                    $objInfoDetalleElemento->setDetalleNombre('INTERFACE_ELEMENTO_ID');
                    $objInfoDetalleElemento->setDetalleValor($intIdInterfacePseudo);
                    $objInfoDetalleElemento->setDetalleDescripcion('INTERFACE DEL ELEMENTO ASOCIADO AL PSEUDO PE');
                    $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleElemento->setUsrCreacion($objSession->get('user'));
                    $objInfoDetalleElemento->setIpCreacion($objPeticion->getClientIp());

                    $em_infraestructura->persist($objInfoDetalleElemento);
                    $em_infraestructura->flush();                
                }

                $objRespuesta->setContent("OK");
            }
            else
            {
                $objRespuesta->setContent("No existe solicitud");
            }

            $em_infraestructura->getConnection()->commit();
            $em->getConnection()->commit();

        }
        catch(\Exception $e)
        {
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError('Telcos+',
                                      'flujoPseudoPe',
                                      $e->getMessage(),
                                      $objSession->get('user'),
                                      $objPeticion->getClientIp());
            
            $em_infraestructura->getConnection()->rollback();
            $em->getConnection()->rollback();
            $objRespuesta->setContent('Ocurrió un error, favor notificar a sistemas.');
        }

        return $objRespuesta;
    }

    /**
     * 
     * obtenerTipoMedio
     * obtiene los tipos de medios al que se puede cambiar un tipo de medio
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-06-2017
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 06-07-2017 se quito el rol de la funcion ya que no es necesaria
     */
    
    public function obtenerTipoMedioAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objPeticion          = $this->get('request');
        $objSession           = $objPeticion->getSession();
        $strCodigoTipoMedio   = $objPeticion->get('strCodigoTipoMedio');
        
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');        
        
        try
        {
            $arrayParametros = $emInfraestructura->getRepository('schemaBundle:AdmiParametroDet')->get( 'CAMBIO_TIPO_MEDIO', 
                                                                                                        '', 
                                                                                                        '', 
                                                                                                        '', 
                                                                                                        $strCodigoTipoMedio, 
                                                                                                        '', 
                                                                                                        '', 
                                                                                                        '');
            if(is_array($arrayParametros))
            {             
                foreach ($arrayParametros as $arrayParametro)
                {
                    $objTipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                      ->findOneBy(array('codigoTipoMedio'=> $arrayParametro['valor2'], 
                                                                        'estado'         => 'Activo'));
                    if(is_object($objTipoMedio))
                    {
                        $arrayTipoMedio[] = array('id'      => $objTipoMedio->getId(),
                                                  'nombre'  => $objTipoMedio->getNombreTipoMedio());
                    }
                }

                
                $objRespuesta->setContent(json_encode(array('total'=>count($arrayTipoMedio), 'encontrados'=>$arrayTipoMedio)));                
            }
            
            return $objRespuesta;
            
        }
        catch(\Exception $e)
        {
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError('Telcos+', 'obtenerTipoMedio', $e->getMessage(), $objSession->get('user'), $objPeticion->getClientIp());

            $objRespuesta->setContent('Ocurrió un error, favor notificar a sistemas.');
        }
    }
    
    /**
     * @Secure(roles="ROLE_135-5377")
     * 
     * cambiarTipoMedio
     * realiza el cambio de tipo medio del servicio
     * 
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    
    public function cambiarTipoMedioAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objPeticion          = $this->get('request');
        $objSession           = $objPeticion->getSession();
        $intIdSolicitud       = $objPeticion->get('idSolicitud');
        $intIdTipoMedio       = $objPeticion->get('idTipomedio');
        $intIdMotivo          = $objPeticion->get('idMotivo');
        $strObervacion        = $objPeticion->get('observacion');
        
        $emComercial = $this->getDoctrine()->getManager('telconet');       
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //primero verifico que no tenga solicitud pasada
            $objSolicitud  = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            
            $objMotivo  = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
            
            if(!is_object($objMotivo))
            {
                throw new \Exception('No existe la solicitud seleccionada.');
            }
            else
            {
                $strMotivo = $objMotivo->getNombreMotivo();
            }
            
            if(is_object($objSolicitud))
            {                
                $objServicio = $objSolicitud->getServicioId();
                $objCaracteristica  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array('descripcionCaracteristica' => 'CAMBIO TIPO MEDIO', 
                                                                          'estado' => 'Activo'));
                if(!is_object($objCaracteristica))
                {
                    throw new \Exception('No existe la caracteristica CAMBIO TIPO MEDIO.');
                }
                
                $objSolicitudCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array( 'detalleSolicitudId' => $intIdSolicitud,
                                                                           'caracteristicaId'   => $objCaracteristica->getId(),                                                                            
                                                                           'estado'             => 'Activo'));
                
                if(is_object($objSolicitudCaract))
                {
                    throw new \Exception('Ha excedido el numero de cambios de UM. '
                                         . 'Por favor rechazar la solicitud para que sea ingresada por comercial nuevamente.');
                }
                else
                {   

                    $objSolicitudNueva  = new InfoDetalleSolicitud();
                    $objSolicitudNueva->setServicioId($objServicio);
                    $objSolicitudNueva->setTipoSolicitudId($objSolicitud->getTipoSolicitudId());	
                    $objSolicitudNueva->setEstado($objSolicitud->getEstado());
                    $objSolicitudNueva->setObservacion($strObervacion);
                    $objSolicitudNueva->setMotivoId($objSolicitud->getMotivoId());
                    $objSolicitudNueva->setUsrCreacion($objSession->get('user'));		
                    $objSolicitudNueva->setFeCreacion(new \DateTime('now'));

                    $emComercial->persist($objSolicitudNueva);
                    $emComercial->flush();

                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($objSolicitudNueva);
                    $entityDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($objSession->get('user'));
                    $entityDetalleSolHist->setObservacion('Se crea la solicitud porque se realizó el cambio de Tipo Medio.');
                    $entityDetalleSolHist->setEstado($objSolicitudNueva->getEstado());
                    $emComercial->persist($entityDetalleSolHist);

                    //creo la caracteristica de la solicitud
                    $objInfoDetalleSolCaractSerie  = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractSerie->setCaracteristicaId($objCaracteristica);
                    $objInfoDetalleSolCaractSerie->setValor('OK');
                    $objInfoDetalleSolCaractSerie->setDetalleSolicitudId($objSolicitudNueva);
                    $objInfoDetalleSolCaractSerie->setEstado("Activo");
                    $objInfoDetalleSolCaractSerie->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolCaractSerie->setUsrCreacion($objSession->get('user'));                            
                    $emComercial->persist($objInfoDetalleSolCaractSerie);
                    
                    //rechazo solicitud actual e ingreso historial
                    $objSolicitud->setEstado('Rechazada');
                    $objSolicitud->setMotivoId($intIdMotivo);
                    $objSolicitud->setUsrRechazo($objSession->get('user'));
                    $objSolicitud->setFeRechazo(new \DateTime('now'));                    
                    $emComercial->persist($objSolicitud);
                    
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                    $objDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setMotivoId($intIdMotivo);
                    $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                    $objDetalleSolHist->setObservacion('Se rechazó porque se realizó el cambio de Tipo Medio. Observacion Usuario: '.$strObervacion);
                    $objDetalleSolHist->setEstado('Rechazada');
                    $emComercial->persist($objDetalleSolHist);                    

                    
                    if(is_object($objServicio))
                    {
                        //actualizo el Tipo Medio en la info tecnica
                        $objServicioTecnico  = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                 ->findOneByServicioId($objServicio->getId());

                        if(is_object($objServicioTecnico))
                        {
                            $objTipoMedioNuevo = $emComercial->getRepository('schemaBundle:AdmiTipoMedio')
                                                             ->find($intIdTipoMedio);
                            
                            $objTipoMedio = $emComercial->getRepository('schemaBundle:AdmiTipoMedio')
                                                        ->find($objServicioTecnico->getUltimaMillaId());
                            
                            $objServicioTecnico->setUltimaMillaId($intIdTipoMedio);                        
                            $emComercial->persist($objServicioTecnico);

                            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                            $objServicioHist = new InfoServicioHistorial();
                            $objServicioHist->setServicioId($objServicio);
                            $objServicioHist->setIpCreacion($objPeticion->getClientIp());
                            $objServicioHist->setFeCreacion(new \DateTime('now'));
                            $objServicioHist->setUsrCreacion($objSession->get('user'));
                            $objServicioHist->setEstado($objServicio->getEstado());
                            $objServicioHist->setObservacion('Se Realizo cambio de Tipo medio.');
                            $emComercial->persist($objServicioHist);

                            //envio de notificacion
                        
                            /* Generación del envío de correo al aprobar la solicitud de Factibilidad
                             * Se obtiene el vendedor del servicio para agregarlo como destinatario del correo que se enviará al aprobar
                             * la factibilidad
                             */
                            
                            if($objServicio->getPuntoId())
                            {
                                $strLogin = $objServicio->getPuntoId()->getLogin();
                            }
                            
                            $strAsunto = "Cambio de tipo medio de la Solicitud de Factibilidad de Instalacion #".$objSolicitudNueva->getId().' - '.
                                         $strLogin;
                            $strTo     = array();


                            if($objServicio->getUsrVendedor())
                            {
                                // DESTINATARIOS.... 
                                $formasContacto = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                              ->getContactosByLoginPersonaAndFormaContacto(  $objServicio->getUsrVendedor(),
                                                                                                             'Correo Electronico');
                                if($formasContacto)
                                {
                                    foreach($formasContacto as $formaContacto)
                                    {
                                        $strTo[] = $formaContacto['valor'];
                                    }
                                }
                            }

                            if(is_object($objTipoMedioNuevo))
                            {
                                $strTipoMedioNuevo = strtoupper($objTipoMedioNuevo->getNombreTipoMedio()) ;
                            }
                            
                            if(is_object($objTipoMedio))
                            {
                                $strTipoMedio = strtoupper($objTipoMedio->getNombreTipoMedio()) ;
                            }
                            
                            /* Envío de correo por medio de plantillas
                             * Se obtiene la plantilla y se invoca a la función generarEnvioPlantilla del service que internamente obtiene
                             * los alias asociados a la plantilla 'APROBAR_FACTIB' y envía el respectivo correo
                             */
                            /* @var $envioPlantilla EnvioPlantilla */
                            $arrayParametros    = array('detalleSolicitud'  => $objSolicitudNueva,
                                                        'usrAprueba'        => $objSolicitud->getUsrCreacion(),
                                                        'tipoMedio'         => $strTipoMedio,
                                                        'tipoMedioNuevo'    => $strTipoMedioNuevo,
                                                        'motivo'            => $strMotivo);
                            $envioPlantilla     = $this->get('soporte.EnvioPlantilla');
                            $envioPlantilla->generarEnvioPlantilla( $strAsunto, 
                                                                    $strTo, 
                                                                    'TIPO_MEDIO', 
                                                                    $arrayParametros,
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    null, 
                                                                    true,
                                                                    'notificaciones_telcos@telconet.ec');                            
                            
                            
                            
                            
                            $emComercial->flush();
                            $emComercial->getConnection()->commit();
                            $emComercial->getConnection()->close();
                            
                        }
                        else
                        {
                            throw new \Exception('El Servicio no tiene info tecnica, no se puede cambiar el tipo medio.');
                        }
                    }
                    else
                    {
                        throw new \Exception('La solicitud no tiene servicio.');
                    }
                }
                
                $objRespuesta->setContent("OK");
            }
            return $objRespuesta;
        } 
        catch (\Exception $e)
        {   
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError('Telcos+', 'cambiarTipoMedio', $e->getMessage(), $objSession->get('user'), $objPeticion->getClientIp());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
            $objRespuesta->setContent($e->getMessage());   
            return $objRespuesta;

        }

        
    }
    
}
