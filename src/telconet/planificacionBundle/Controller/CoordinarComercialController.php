<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\planificacionBundle\Service\PlanificarService;


/**
 * Documentación para la clase 'CoordinarComercialController'.
 *
 * Clase que contiene toda la funcionalidad de la Coordinacion de las Planificaciones para asesores comerciales MD
 *
 * @author Edgar Pin Villavicencio 20-10-2021
 * @version 1.0 
 * 
*/
class CoordinarComercialController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_473-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Método utilizado para cargar la vista principal de la Administración de Coordinacion
     *
     * @return twig index
     *
     * @author Desarrollo Inicial
     * @version 1.0 
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-19 Eliminacion de variable $em no utilizada
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 30-07-2016 Se captura la empresa en sesion
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 25-06-2018 Se pone perfil de Replanificar OPU 
     *
    */
    public function indexAction()
    {
        $arrayRolesPermitidos   = array();
        $objSession             = $this->get( 'session' );
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $intPersonaEmpresaRolId = $objSession->get('idPersonaEmpresaRol');
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager("telconet_general");


        $strNombreVendedor      = "";
        $boolReadOnly           = false;

        $entityPersonaRol   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->findOneById($intPersonaEmpresaRolId);
                 
        $intIdPersona = $entityPersonaRol->getPersonaId()->getId();

        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                          ->getPerfilPlanificacion($intIdPersona,'visualizarPlanTodos');


        if($strTienePerfil === 'S')
        {
            $arrayRolesPermitidos[] = 'ROLE_473-1';
            $arrayRolesPermitidos[] = 'ROLE_467-1';
        }
        else
        {
            $entityPersona     = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRolId);
            $strNombreVendedor = $entityPersona->getPersonaId()->getNombres() . " " . $entityPersona->getPersonaId()->getApellidos();
            $boolReadOnly = true;
    
        }

        $strTienePerfil     = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                          ->getPerfilPlanificacion($intIdPersona, 'visualizarPlanUsuario');
        if($strTienePerfil === 'S')
        {
            $arrayRolesPermitidos[] = 'ROLE_473-1';
            $arrayRolesPermitidos[] = 'ROLE_467-1';
        }


        
        
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("473", "1");

        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('CANT_DIA_MAX_PLANIFICACION','COMERCIAL','','CANTIDAD DE DÍA MAXIMO PARA PLANIFICAR','','','','','','');   
    

        $objFecha       = new \DateTime('now');
        $strFecha       = $objFecha->format('Y-m-d');  
        $objFechaFin    = new \DateTime('now'); 


        $strFecha       = date("Y-m-d", strtotime($strFecha . "+ 1 days"));
        $objFecha       = new \DateTime($strFecha);

        $strFecha       = $objFecha->format('Y-m-d');


        $intDias = 0;
        if ($arrayAdmiParametroDet)
        {
            $strFecha       = date("Y-m-d", strtotime($strFecha . "+ " . $arrayAdmiParametroDet['valor1']. " days"));
            $objFechaFin    = new \DateTime($strFecha);
        }         


        
        return $this->render('planificacionBundle:CoordinarComercial:index.html.twig', array(
                'item'            => $entityItemMenu,
                'rolesPermitidos' => $arrayRolesPermitidos,
                'codEmpresa'      => $strCodEmpresa,
                'nombreVendedor'  => $strNombreVendedor,
                'boolReadOnly'    => $boolReadOnly,
                'strFechaMinima'  => $objFecha->format('Y-m-d'),
                'strFechaMaxima'  => $objFechaFin->format('Y-m-d')

        ));
    }

    /*
	 * Llena el grid de consulta.
	 * @Secure(roles="ROLE_473-1")
	 *
     * @author Edgar Pin Villavicencio
     * @version 1.0 20-10-2021 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 01-07-2022 - Se agrega Token Cas
     * 
     */
    public function gridAction()
    {
        $serviceUtil    = $this->get('schema.Util');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emGeneral   = $this->getDoctrine()->getManager("telconet_general");
        $strUrlMsConsulta = $this->container->getParameter('planificacion.comercial.url.buscar');

        $objPeticion         = $this->get('request');
        $objSession          = $this->get( 'session' );

        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $intIdDepartamento= $objSession->get('idDepartamento');

        $arrayFechaDesdePlanif = explode('T',$objPeticion->query->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif = explode('T',$objPeticion->query->get('fechaHastaPlanif'));
        $arrayFechaDesdeIngOrd = explode('T',$objPeticion->query->get('fechaDesdeIngOrd'));
        $arrayFechaHastaIngOrd = explode('T',$objPeticion->query->get('fechaHastaIngOrd'));
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura= $this->get('doctrine')->getManager('telconet_infraestructura');
        $strFechaDesdePlanif = "";
        $strFechaHastaPlanif = "";
        $strFechaDesdePlanifOrd = "";
        $strFechaHastaPlanifOrd = "";        


        if ($arrayFechaDesdePlanif && $arrayFechaDesdePlanif[0] !== "") 
        {
            $arrayFechaDesdePlanif = explode('-', $arrayFechaDesdePlanif[0]);
            $strFechaDesdePlanif = $arrayFechaDesdePlanif[2] . "/" . $arrayFechaDesdePlanif[1] . "/" . $arrayFechaDesdePlanif[0];    
        }       

        if ($arrayFechaHastaPlanif && $arrayFechaHastaPlanif[0] !== "") 
        {
            $arrayFechaHastaPlanif = explode('-', $arrayFechaHastaPlanif[0]);
            $strFechaHastaPlanif = $arrayFechaHastaPlanif[2] . "/" . $arrayFechaHastaPlanif[1] . "/" . $arrayFechaHastaPlanif[0];    
        }

        if ($arrayFechaDesdeIngOrd &&  $arrayFechaDesdeIngOrd[0] !== "") 
        {
            $arrayFechaDesdeIngOrd = explode('-', $arrayFechaDesdeIngOrd[0]);
            $strFechaDesdePlanifOrd = $arrayFechaDesdeIngOrd[2] . "/" . $arrayFechaDesdeIngOrd[1] . "/" . $arrayFechaDesdeIngOrd[0];    
        }       

        if ($arrayFechaHastaIngOrd && $arrayFechaHastaIngOrd[0] !== "")  
        {
            $arrayFechaHastaIngOrd = explode('-', $arrayFechaHastaIngOrd[0]);
            $strFechaHastaPlanifOrd = $arrayFechaHastaIngOrd[2] . "/" . $arrayFechaHastaIngOrd[1] . "/" . $arrayFechaHastaIngOrd[0];    
        }        
        
        $arrayDatosBusqueda    = array();
        $arrayDatosBusqueda['fechaDesdePlanif'] = $strFechaDesdePlanif;
        $arrayDatosBusqueda['fechaHastaPlanif'] = $strFechaHastaPlanif;
        $arrayDatosBusqueda['fechaDesdeIngOrd'] = $strFechaDesdePlanifOrd;
        $arrayDatosBusqueda['fechaHastaIngOrd'] = $strFechaHastaPlanifOrd;
        $arrayDatosBusqueda['tipoSolicitud']    = "SOLICITUD PLANIFICACION";
        $arrayDatosBusqueda['estado']           = "PrePlanificada";
        $arrayDatosBusqueda['ciudad']           = explode(",", $objPeticion->query->get('ciudad'));
        $arrayDatosBusqueda['idSector']         = $objPeticion->query->get('sector');
        $arrayDatosBusqueda['identificacion']   = $objPeticion->query->get('identificacion');
        $arrayDatosBusqueda['vendedor']         = $objPeticion->query->get('vendedor');
        $arrayDatosBusqueda['nombres']          = $objPeticion->query->get('nombres');
        $arrayDatosBusqueda['apellidos']        = $objPeticion->query->get('apellidos');
        $arrayDatosBusqueda['login']            = $objPeticion->query->get('login');
        $arrayDatosBusqueda['descripcionPunto'] = $objPeticion->query->get('descripcionPunto');
        $arrayDatosBusqueda['estadoPunto']      = $objPeticion->query->get('estadoPunto');
        $arrayDatosBusqueda['codEmpresa']       = $strCodEmpresa;
        $arrayDatosBusqueda['prefijoEmpresa']   = $strPrefijoEmpresa;
        $arrayDatosBusqueda['ultimaMilla']      = $objPeticion->query->get('ultimaMilla');
        $arrayDatosBusqueda['usrCreacion']      = $strUsrCreacion;
        $arrayDatosBusqueda['start']            = $objPeticion->query->get('start');
        $arrayDatosBusqueda['limit']            = $objPeticion->query->get('limit');
        $arrayDatosBusqueda['emComercial']      = $emComercial;
        $arrayDatosBusqueda['emInfraestructura']= $emInfraestructura;
        $arrayDatosBusqueda['tipoConsulta']     = "GRID";
        $arrayDatosBusqueda['grupo']            = '';
        $objDepartamento = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);
        $arrayProductos          = array();
        $arrayProductosExcepcion = array();
        $strRegion               = '';

        if(is_object($objDepartamento))
        {
            //Si el usuario pertence a IPCCL2 se muestra la informacion ligada a DATACENTER
            $arrayInfoVisualizacion   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('VISUALIZACION DATOS POR DEPARTAMENTO', 
                                                          'COMERCIAL', 
                                                          '',
                                                          'COORDINAR',
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
                    $arrayProductos[]     = array($array['valor2']);
                    $arrayTipoOrden[]     = array($array['valor2']);
                    $arrayTipoSolicitud[] = array($array['valor3']);
                }
                if(!empty($arrayDatosBusqueda['prefijoEmpresa']) && $arrayDatosBusqueda['prefijoEmpresa']=='MD')
                {
                    $arrayDatosBusqueda['tipoSolicitud']                = $arrayTipoSolicitud[0][0];
                    $arrayDatosBusqueda['arrayTipoOrden']               = $arrayTipoOrden;
                }
            }
            else//Filtra los productos que no seben ser mostrados en flujos normales con donde solo interviene PyL
            {

                $arrayInfoNoVisualizacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES', 
                                                              'COMERCIAL', 
                                                              '',
                                                              'COORDINAR',
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
        $arrayDatosBusqueda['region']                            = $strRegion;
        $arrayDatosBusqueda['arrayDescripcionProducto']          = $arrayProductos;
        
        $arrayDatosBusqueda['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;
        
        if(!empty($arrayDatosBusqueda['prefijoEmpresa']) && $arrayDatosBusqueda['prefijoEmpresa']=='MD' && !empty($arrayDatosBusqueda['login'])
            && !empty($arrayDatosBusqueda['codEmpresa']))
        {
            $objPunto =   $emComercial->getRepository('schemaBundle:InfoPunto')->findOneBy(array('login' => $arrayDatosBusqueda['login'])); 
            
            $arrayParametrosValor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('VALIDA_PROD_ADICIONAL', 
                                                                             'COMERCIAL', 
                                                                             '',
                                                                             '',
                                                                             'PROD_ADIC_PLANIFICA',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             $arrayDatosBusqueda['codEmpresa']);
            if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
            {
                foreach($arrayParametrosValor as $arrayParametro)
                {
                    //buscamos en el plan instalado tenga el producto
                    if ($objPunto) 
                    {
                        $arrayParametros    =    array("Punto"      => $objPunto->getId(),
                        "Producto"   => $arrayParametro['valor2'],
                        "Estado"     => 'Todos');
                        $arrayResultado     = $emComercial->getRepository('schemaBundle:InfoServicio')->getProductoByPlanes($arrayParametros);


                        if($arrayResultado['total'] > 0)
                        {
                            $arrayDatosBusqueda['prodAdicional'] = 'SI';
                        }

                    }
                }
            }
        }
        $arrayDatosBusqueda['origen'] = 'web';
        try
        {
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }            

            $arrayRestUpdate[CURLOPT_HTTPHEADER]     = array('Content-Type: application/json',
                                                             'tokencas: '.$arrayTokenCas['strToken']);
            $arrayRestUpdate[CURLOPT_TIMEOUT]        = 900000;
            $serviceRest = $this->get('schema.RestClient');
            $arrayRespuesta = $serviceRest->postJSON($strUrlMsConsulta,json_encode($arrayDatosBusqueda), $arrayRestUpdate);
            $arrayData = json_decode($arrayRespuesta['result'], true);
            $arrayResp["total"] = 0;
            $arrayResp["encontrados"] = $arrayData['data'];
            $objJson = json_encode($arrayResp, true);
            
            if ($arrayData['status'] != "OK")
            {
                throw new \Exception('ERROR ' . "No se pudo obtener información de la consulta", 1);
            }
        }
        catch(\Exception $ex)
        {
            $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayDatosBusqueda, 128);
            $arrayParametrosLog['creationUser']     = $strUsrCreacion;
            $serviceUtil->insertLog($arrayParametrosLog); 
        }
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }
    
    /**
     *
     * Documentación para el método 'exportarGridAction'.
     *postJSON
     * Método utilizado para exportar resultados de búsqueda a Excel
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-19 Manejar Excepcion cuando haya problemas con el archivo
     * @version 1.2 2016-05-20 Incluir el Esquema Comercial para consultar los Datos de Factibilidad
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 30-07-2016 Se envia la empresa desde el JS
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 2016-08-08 Se implementa la funcion insertError del UtilService, que permite guardar el error en la
     *                         DB_GENERAL.INFO_ERROR
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 2019-07-06 Se agrega el envío de emInfraestructura y serviceTecnico, necesaria para obtener los datos del splitter 
     *                          clonado en el proceso de migración y equipos Dual Band necesarios
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.6 2019-11-2019 - Se agrega servicio 'comercial.infoservicio' al arreglo '$arrayDatosBusqueda' para dar soporte a servicios GPON-TN.
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.7 2020-01-10 Se agrega el envío de objEmSoporte necesario para la función generarJsonCoordinar.
     * 
     * @author Jefferson Carrillo  <jacarrillo@telconet.ec>
     * @version 1.8 20-06-2022 - Se implementa  variable esPlanificacionMega para la solicitud de planificacion comercial.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.9 21-06-2022 - Se agrega el parámetro ociCon para la consulta de solicitudes
     *             
     */
    public function exportarGridAction()
    {
        $objPeticion         = $this->get('request');
        $objSession          = $this->get( 'session' );
        
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objPeticion->getClientIp();
        
        $arrayFechaDesdePlanif = explode('T',$objPeticion->query->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif = explode('T',$objPeticion->query->get('fechaHastaPlanif'));
        $arrayFechaDesdeIngOrd = explode('T',$objPeticion->query->get('fechaDesdeIngOrd'));
        $arrayFechaHastaIngOrd = explode('T',$objPeticion->query->get('fechaHastaIngOrd'));
        $strEmpresaCod       = $objPeticion->query->get('empresaCod');
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura= $this->get('doctrine')->getManager('telconet_infraestructura');
        $arrayDatosBusqueda    = array();
        $objEmSoporte     = $this->get('doctrine')->getManager('telconet_soporte');
        //Se verifica si la empresa es enviada desde el JS
        if($strEmpresaCod)
        {
            $strCodEmpresa          = $strEmpresaCod;
            $objInfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
            if($objInfoEmpresaGrupo)
            {
                $strPrefijoEmpresa = $objInfoEmpresaGrupo->getPrefijo();
            }
        }
        $strStart = $objPeticion->query->get('start');
        $strLimit = $objPeticion->query->get('limit');

        $arrayDatosBusqueda['fechaDesdePlanif']      = $arrayFechaDesdePlanif[0];
        $arrayDatosBusqueda['fechaHastaPlanif']      = $arrayFechaHastaPlanif[0];
        $arrayDatosBusqueda['fechaDesdeIngOrd']      = $arrayFechaDesdeIngOrd[0];
        $arrayDatosBusqueda['fechaHastaIngOrd']      = $arrayFechaHastaIngOrd[0];
        $arrayDatosBusqueda['tipoSolicitud']         = $objPeticion->query->get('tipoSolicitud');
        $arrayDatosBusqueda['estado']                = $objPeticion->query->get('estado');
        $arrayDatosBusqueda['ciudad']                = explode(",", $objPeticion->query->get('ciudad'));
        $arrayDatosBusqueda['idSector']              = $objPeticion->query->get('sector');
        $arrayDatosBusqueda['identificacion']        = $objPeticion->query->get('identificacion');
        $arrayDatosBusqueda['vendedor']              = $objPeticion->query->get('vendedor');
        $arrayDatosBusqueda['nombres']               = $objPeticion->query->get('nombres');
        $arrayDatosBusqueda['apellidos']             = $objPeticion->query->get('apellidos');
        $arrayDatosBusqueda['login']                 = $objPeticion->query->get('login');
        $arrayDatosBusqueda['descripcionPunto']      = $objPeticion->query->get('descripcionPunto');
        $arrayDatosBusqueda['estadoPunto']           = $objPeticion->query->get('estadoPunto');
        $arrayDatosBusqueda['codEmpresa']            = $strCodEmpresa;
        $arrayDatosBusqueda['prefijoEmpresa']        = $strPrefijoEmpresa;
        $arrayDatosBusqueda['usrCreacion']           = $strUsrCreacion;
        $arrayDatosBusqueda['start']                 = (empty($strStart))?0:$strStart;
        $arrayDatosBusqueda['limit']                 = $strLimit;
        $arrayDatosBusqueda['emComercial']           = $emComercial;
        $arrayDatosBusqueda['tipoConsulta']          = "XLS";
        $arrayDatosBusqueda['emInfraestructura']     = $emInfraestructura;
        $arrayDatosBusqueda['serviceTecnico']        = $this->get('tecnico.InfoServicioTecnico');
        $arrayDatosBusqueda['planificarService']     = $this->get('planificacion.planificar');
        $arrayDatosBusqueda['coordinarService']      = $this->get('planificacion.coordinar');
        $arrayDatosBusqueda['serviceInfoServicio']   = $this->get('comercial.infoservicio');
        $arrayDatosBusqueda['objEmSoporte']          = $objEmSoporte;
        $arrayDatosBusqueda['esPlanificacionMega']   = true;

        $arrayDatosBusqueda["ociCon"]                = array('userComercial' => $this->container->getParameter('user_comercial'),
                                                             'passComercial' => $this->container->getParameter('passwd_comercial'),
                                                             'databaseDsn'   => $this->container->getParameter('database_dsn'));
        try
        {
            $objJson = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                   ->generarJsonCoordinar($arrayDatosBusqueda);

            $objJson = json_decode($objJson,true);
            $arrayDatos   = $objJson['encontrados'];

            $this->generateExcelConsulta($arrayDatosBusqueda,$arrayDatos);
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 'CoordinarController->exportarGridAction', $e->getMessage(), $strUsrCreacion, $strIpClient);
            return $this->indexAction();
        }
    }
    
    /**
     * 
     * Documentación para el método 'getMotivosNoplanificacionAction'.
     * @version 1.0
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 12-10-2021 Se trae los motivos por los cuales no se puede planificar comercial
     * 
     */
    public function getMotivosNoplanificacionAction()
    {
        $objResponse    = new Response();
        $emGeneral      = $this->getDoctrine()->getManager('telconet');
        
        $arrayParametros    = array(
            "strNombreParametro" => "PROGRAMAR_MOTIVO_HAL",
            "strEstado" => "Activo"

        );
        $arrayRespuesta        = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->getMotivosNoPlanificacion( $arrayParametros );
        
        $arrayResp["total"]       = count($arrayRespuesta);
        $arrayResp["encontrados"] = $arrayRespuesta['result'];
        $objJson = json_encode($arrayResp, 128);
        error_log("json " . $objJson) ; 

        $objResponse->setContent($objJson);
        return $objResponse; 
    }
    








    /**
     * Metodo utilizado para traer el horario comercial
     *
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 1.0 19-08-2022
     */
    public function getCronogramaComercialAction()
    {

        $strUrlMsCronograma = $this->container->getParameter('planificacion.comercial.url.getCronogramaComercial');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion       = $this->get('request');
        $objPeticion       = $this->get('request');
        $serviceUtil       = $this->get('schema.Util');        
        $intIdDetSolic     = $objPeticion->get('idDetalleSolicitud');
        $intIdPersona      = $objPeticion->getSession()->get('idPersonaEmpresaRol');
        $strUserSession    = $objPeticion->getSession()->get('user');
        $strIpCreacion     = $objPeticion->getClientIp();

        
    try
    {

        $arrayParametros   = array();
        $arrayParametros['idDetalleSolicitud']=$intIdDetSolic;
        $arrayParametros['idPersona']=$intIdPersona;
        $arrayParametros['usrCreacion']=$strUserSession;

             
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }            

        $arrayRestUpdate[CURLOPT_HTTPHEADER]     = array('Content-Type: application/json',
                                                         'tokencas: '.$arrayTokenCas['strToken']);        
        $arrayRestUpdate[CURLOPT_TIMEOUT]        = 900000;
        $serviceRest = $this->get('schema.RestClient');

    
        $arrayRespuesta = $serviceRest->postJSON($strUrlMsCronograma,json_encode($arrayParametros), $arrayRestUpdate);
        
        $arrayData = json_decode($arrayRespuesta['result'], true);

        if (empty($arrayData))
        {
            throw new \Exception('ERROR ' . "No se pudo comunicar con el ms-core-sop-cuadrilla", 1);
        }

        $objResponse = new Response(json_encode($arrayData));
        $objResponse->headers->set('Content-type', 'text/json');


    }catch(\Exception $ex)
     {
           
            $serviceUtil->insertError('Telcos+',
            'coordinarComercialController.getCronogramaComercialAction',
            $ex->getMessage(),
             $strUserSession,
             $strIpCreacion);


            $arrayData['status']='ERROR';
            $arrayData['message']=$ex->getMessage();
            $objResponse = new Response(json_encode($arrayData));
            $objResponse->headers->set('Content-type', 'text/json');
     }

    
        return $objResponse;

    }




    
    /**
     * @Secure(roles="ROLE_473-1")
     * Metodo utilizado para realizar la programacion de las solicitudes
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 20-10-2021
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 14-03-2023 - Se obtiene el teléfono del punto para enviar notificación SMS al planificar el servicio por HAL
     * @since 1.0
     * 
     */
    public function programarAjaxAction()
    {
       

      
        $strUrlMsPlanificar = $this->container->getParameter('planificacion.comercial.url.planificacion.hal');
        $objSession          = $this->get('session');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $objPeticion    = $this->get('request');
        $serviceUtil       = $this->get('schema.Util');        

        $arrayParametros['idPersonaEmpRolSession']   = (int)$objSession->get('idPersonaEmpresaRol');
        $arrayParametros['origen']              = $objPeticion->get('origen');
        $arrayParametros['idFactibilidad']      = (int)$objPeticion->get('id');
        $arrayParametros['idCuadrilla']         = $objPeticion->get('idCuadrilla');
        $arrayParametros['parametro']           = $objPeticion->get('param');
        $arrayParametros['parametroResponsable']   = $objPeticion->get('paramResponsables');
        $arrayParametros['idpersona']           = $objPeticion->get('idPersona');
        $arrayParametros['idPersonaEmpRol'] = (int)$objPeticion->get('idPersonaRol');
        $arrayParametros['idPersonaTecnico']        = (int)$objPeticion->get('idPerTecnico');
        $arrayParametros['codEmpresa']          = $objSession->get('idEmpresa');
        $arrayParametros['prefijoEmpresa']      = $objSession->get('prefijoEmpresa');
        $arrayParametros['idDepartamento']      = (int)$objSession->get('idDepartamento');
        $arrayParametros['ipCreacion']          = $objPeticion->getClientIp();
        /*Array con los Id's de los servicios internet Wifi para instalacion simultanea*/
        $arrayParametros['idWifiSim']           = 0;
        $arrayParametros['tipoEsquema']         = $objPeticion->get('tipoEsquema');
        /*Array con los Id's de los servicios COU LINEAS TELEFONIA FIJA para instalacion simultanea*/
        $arrayParametros['idIntCountSim']            = $objPeticion->get('idIntCouSim');
        /* Array con los ID's de los servicios para instalación simultanea en estado pendiente.*/
        $arrayParametros['arraySimultaneos']       = json_decode($objPeticion->get('arraySimultaneos'));

        $intOpcion                                 = $objPeticion->get('opcion');
                
        $arrayFechaProgramacion = explode("T", $objPeticion->get('fechaProgramacion'));
        $arrayFecha             = explode("T", $objPeticion->get('ho_inicio'));
        $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF             = explode(":", $arrayFecha[1]);
        $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));

        $arrayFechaI = date_create(date('Y/m/d', strtotime($arrayFecha[0])));

        $arrayFechaI = $arrayFechaI->format("d/m/Y"); 

        $arrayFecha2            = explode("T", $objPeticion->get('ho_fin'));
        $arrayF2                = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF2            = explode(":", $arrayFecha2[1]);
        $arrayFechaInicio2      = date("Y/m/d H:i", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));
        $strHoraInicioServicio  = $arrayHoraF;
        $strHoraFinServicio     = $arrayHoraF2;
        $strHoraInicio          = $arrayHoraF;
        $strHoraFin             = $arrayHoraF2;
        $arrayParametros['idMotivo']             = (int)$objPeticion->get('idMotivo');
        $arrayParametros['fechaProgramacion']   = $arrayFechaI . ' ' . $arrayFechaProgramacion[1];
        $arrayParametros['horaInicio']          = $objPeticion->get('ho_inicio');
        $arrayParametros['horaFin']             = $objPeticion->get('ho_fin');
        $arrayParametros['observacionServicio']  = $objPeticion->get('observacion');
        $arrayParametros['ipCreacion']           = $objPeticion->getClientIp();
        $arrayParametros['usrCreacion']          = $objPeticion->getSession()->get('user');

        $strUserSession                          = $objPeticion->getClientIp();
        $strIpCreacion                           = $objPeticion->getSession()->get('user');
        $arrayParametros['observacionPlanif']    = $objPeticion->get('observacion');
        
        $arrayParametros['atenderAntes']         = $objPeticion->get('atenderAntes');
        $arrayParametros['esHal']                = $objPeticion->get('esHal');
        $arrayParametros['idSugerencia']         = (int)$objPeticion->get('idSugerencia');
        
        $arrayParametros['opcion']     = 0;
        $boolControlaCupo = true;

    try
    {    
        $strFechaReserva        = $objPeticion->get('fechaVigencia');
        $objDateFechaReserva    = new \DateTime(date('Y-m-d H:i:s',strtotime($strFechaReserva)));
        $objDateNow             = new \DateTime('now');

        if ($arrayParametros['strEsHal'] === 'S' && $objDateNow > $objDateFechaReserva)
        {
            $objRespuesta->setContent('El tiempo de reserva para la sugerencia escogida ha culminado..!!');
            return $objRespuesta;    
        }
        $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->find($arrayParametros['idFactibilidad']);
        
     
        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($entityDetalleSolicitud->getServicioId()->getId());

        $intJurisdicionId = $entityDetalleSolicitud->getServicioId()
                        ->getPuntoId()
                        ->getPuntoCoberturaId()->getId();
        
        $intControlaCupo = $entityDetalleSolicitud->getServicioId()
                        ->getPuntoId()
                        ->getPuntoCoberturaId()->getCupo();
                        
        $intIdPunto      = $entityDetalleSolicitud->getServicioId()
                           ->getPuntoId();
        $objPunto        = $emComercial->getRepository('schemaBundle:InfoPunto')
                           ->findOneById($intIdPunto);

        if($objPunto)
        {
            $arrayFormaContacto  = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                    ->findBy(array(
                                                'estado'                   => 'Activo',
                                                'descripcionFormaContacto' => 
                                                        array(
                                                                "Telefono Movil Claro",
                                                                "Telefono Movil CNT",
                                                                "Telefono Movil Digicel",
                                                                "Telefono Movil Movistar",
                                                                "Telefono Movil Referencia IPCC",
                                                                "Telefono Movil Tuenti"
                                                            )
                                                )
                                            );

            foreach($arrayFormaContacto as $objFormaContacto)
            {
                $arrayInfoFormContactPunto = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                    ->findBy(array('puntoId'          => $objPunto->getId(),
                                                    'formaContactoId' => $objFormaContacto->getId(),
                                                    'estado'          => 'Activo'
                                                    )
                                                );
                foreach($arrayInfoFormContactPunto as $objInfoFormaContactoPto)
                {
                    $valorFormaContacto            = $objInfoFormaContactoPto->getValor();
                    $arrayParametros['telefono']   = $valorFormaContacto;
                }
            }

        }

        if ( is_null($intControlaCupo) ||  $intControlaCupo <= 0 || $arrayParametros['esHal'] === 'S' )
        {
            $boolControlaCupo = false;
        }

        if (is_object($objServicio) && is_object($objServicio->getProductoId()) && $boolControlaCupo)
        {
            $intProductoId = $objServicio->getProductoId()->getId();
            $intCaracteristicaId = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneByDescripcionCaracteristica('PRODUCTO CONTROLA CUPO');

            $entityControlaCupo =  $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                         ->findOneBy(array("productoId" => $intProductoId,
                                                           "caracteristicaId" => $intCaracteristicaId));
            if (is_object($entityControlaCupo))
            {
                $boolControlaCupo = false;
            }           
        }
        if ($arrayParametros['prefijoEmpresa'] == "MD" && $intOpcion == 0 && $boolControlaCupo )
        {
            $strFechaPar = substr($arrayFechaInicio, 0, -1);
            $strFechaPar .= "1";
            $strFechaPar = str_replace("-", "/", $strFechaPar);
            $strFechaAgenda = str_replace("-", "/", $arrayFechaInicio);


            $intHoraCierre = $this->container->getParameter('planificacion.mobile.hora_cierre');

            $arrayPar    = array(
                "strFecha" => $strFechaPar,
                "strFechaAgenda" => $strFechaAgenda,
                "intJurisdiccion" =>  $intJurisdicionId,
                "intHoraCierre" => $intHoraCierre);

            $arrayCount  = $emComercial
                ->getRepository('schemaBundle:InfoCupoPlanificacion')
                ->getCountDisponiblesWeb($arrayPar);

            if ($arrayCount == 0)
            {
                $objRespuesta->setContent("No hay cupo disponible para este horario, seleccione otro horario por favor!");
                return $objRespuesta;            
            }
        }
        
        $strNombreTecnico = is_object($objServicio->getProductoId()) ? $objServicio->getProductoId()->getNombreTecnico():'';
        // En el caso de que sea hosting se envía correo 
            
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();
   
        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }            

        $arrayRestUpdate[CURLOPT_HTTPHEADER]     = array('Content-Type: application/json',
                                                         'tokenCas: '.$arrayTokenCas['strToken']);        
        $arrayRestUpdate[CURLOPT_TIMEOUT]        = 900000;
        $serviceRest = $this->get('schema.RestClient');

        $arrayRespuesta = $serviceRest->postJSON($strUrlMsPlanificar,json_encode($arrayParametros), $arrayRestUpdate);

        

        $arrayData = json_decode($arrayRespuesta['result'], true);
        if (empty($arrayData))
        {
          throw new \Exception('ERROR ' . "No se pudo comunicar con el ms-core-sop-cuadrilla", 1);
        }

        $arrayResp["total"] = 0;
        $arrayResp["encontrados"] = $arrayData['data'];
        if ($arrayData['status'] == "OK")
        {
            if ($arrayParametros['idMotivo'])
            {
                $arrayData['message'] = 'No se asignará responsable.';
            }
            else
            {
                $arrayData['message'] = 'Se asignaron la(s) Tarea(s) Correctamente.';
            }
        }
        else 
        {
            throw new \Exception($arrayData['message']);
        }

        }catch(\Exception $ex)
        {
            
            $serviceUtil->insertError('Telcos+',
            'coordinarComercialController.programarAjaxAction',
            $ex->getMessage(),
                $strUserSession,
                $strIpCreacion);


            $arrayData['status']='ERROR';
            $arrayData['message']=$ex->getMessage();
            $objResponse = new Response(json_encode($arrayData));
            $objResponse->headers->set('Content-type', 'text/json');
        }
        
        $objRespuesta->setContent($arrayData['message']);
        return $objRespuesta;
    }

    

    /**
     * Función que obtiene los intervalos consumiendo microservicios
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @since 1.0
     * @version 1.0 24-10-2021
     *
     * 
     */
    public function getIntervalosHalAction()
    {
        set_time_limit(240);

        $objPeticion       = $this->get('request');
        $strIpCreacion     = $objPeticion->getClientIp();
        $strUserSession    = $objPeticion->getSession()->get('user');
        $intIdDetalle      = $objPeticion->query->get('idDetalle');
        $intIdCaso         = $objPeticion->query->get('idCaso');
        $intIdHipotesis    = $objPeticion->query->get('idHipotesis');
        $strNombreTarea    = $objPeticion->query->get('idAdmiTarea');
        $intNIntentos      = $objPeticion->query->get('nIntentos');
        $strFechaSugerida  = $objPeticion->query->get('fechaSugerida');
        $strHoraSugerida   = $objPeticion->query->get('horaSugerida');
        $intTipoHal        = $objPeticion->query->get('tipoHal');
        $strSolicitante    = $objPeticion->query->get('solicitante');
        $intIdDetSolic     = $objPeticion->query->get('idDetSolicitud');
        $strEsInstalacion  = $objPeticion->query->get('esInstalacion');
        $intIdPersona      = $objPeticion->getSession()->get('idPersonaEmpresaRol');
        $intIdComunicacion = $objPeticion->query->get('idComunicacion');
        $serviceSoporte    = $this->get('soporte.SoporteService');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil       = $this->get('schema.Util');
        $objRespuesta      = new Response();
        $intNOpciones      = 1;
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $arrayIntervalos  = array();
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();


        try
        {
            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }    
            if ($intTipoHal == 2)
            {
                if ($strFechaSugerida != "")
                {
                    $arrayFechaSugerida = explode("T", $strFechaSugerida);
                    $strFechaSugerida   = $arrayFechaSugerida[0];
                }

                if ($strHoraSugerida != "")
                {
                    $arrayHoraSugerida = explode("T", $strHoraSugerida);
                    $strHoraSugerida   = $arrayHoraSugerida[1];
                }

                $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('PLANIFICACION_SOPORTE_HAL','SOPORTE','','CANTIDAD_OPCIONES_INTERVALOS','','','','','','');

                if (empty($arrayAdmiParametroDet) || count($arrayAdmiParametroDet) < 1)
                {
                    $intNOpciones = 3;
                }
                else
                {
                    $intNOpciones = $arrayAdmiParametroDet['valor1'];
                }
            }

            if (is_null($intIdDetalle))
            {
                $arrayAdmiTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->getTareasXNombre($strNombreTarea);
                $intIdCaso      = intval($intIdCaso);
                $intIdHipotesis = intval($intIdHipotesis);
            }
            else
            {
                $intIdDetalle = intval($intIdDetalle);
            }

            // Array para el envio a hal
            $arrayParametrosHal = array ('intIdDetalleSolicitud'  => intval($intIdDetSolic),
                                            'intIdDetalle'           => $intIdDetalle,
                                            'token'               => $arrayTokenCas['strToken'],
                                            'intIdComunicacion'      => $intIdComunicacion,
                                            'strEsInstalacion'       => $strEsInstalacion,
                                            'intIdPersonaEmpresaRol' => intval($intIdPersona),
                                            'intNOpciones'           => intval($intNOpciones),
                                            'intNIntentos'           => intval($intNIntentos),
                                            'strFechaSugerida'       => $strFechaSugerida,
                                            'strHoraSugerida'        => $strHoraSugerida,
                                            'boolConfirmar'          => false,
                                            'strSolicitante'         => $strSolicitante,
                                            'strUrl'                 => $this->container
                                            ->getParameter('planificacion.comercial.url.intervalos.hal'));
            // Establecemos la comunicacion con hal
            $arrayRespuestaHal  = $serviceSoporte->getSolicitarConfirmarSugerenciasHal($arrayParametrosHal);

            if (strtoupper($arrayRespuestaHal['result']['mensaje']) == 'FAIL')
            {
                $serviceUtil->insertError('Telcos+',
                                           'InfoCasoController.getIntervalosHalAction',
                                           'getSolicitarConfirmarSugerenciasHal: '.$arrayRespuestaHal['descripcion'],
                                           $strUserSession,
                                           $strIpCreacion);
            }
            else
            {
                if ($arrayRespuestaHal['result']['data']['respuesta'] === 'conSugerencias')
                {
                    $strMensajeHal = '<b>'.$arrayRespuestaHal['result']['mensaje'].'</b>';
                    foreach ($arrayRespuestaHal['result']['data']['sugerencias'] as $arrayDatos)
                    {
                        $objDateTiempoVigencia = new \DateTime('now');
                        $objDateTiempoVigencia->modify('+'.$arrayDatos['segTiempoVigencia'].' second');
                        $arrayDatos['fechaVigencia'] = date_format($objDateTiempoVigencia, 'Y-m-d H:i:s');
                        $arrayDatos['horaVigencia']  = date_format($objDateTiempoVigencia, 'H:i:s');
                        $arrayIntervalos[]           = $arrayDatos;
                    }
                }
                elseif ($arrayRespuestaHal['mensaje'] === 'fail')
                {
                    $strMensajeHal = '<b style="color:red";>'.$arrayRespuestaHal['mensaje'].'</b>';
                }
                elseif($arrayRespuestaHal['result']['status'] === 'ERROR') 
                {
                    $strMensajeHal = '<b style="color:green";>'.$arrayRespuestaHal['result']['message'].'</b>';
                }

                $arrayRespuesta['intervalos'] = $arrayIntervalos;
                $arrayRespuesta['mensaje']    = $strMensajeHal;
            }

            $objResultado = json_encode($arrayRespuesta);
        }
        catch(\Exception $objException)
        {
            error_log("Error - coordinarComercialController.getIntervalosHalAction -> Detalle: ".$objException->getMessage());
            $serviceUtil->insertError('Telcos+',
                                      'coordinarComercialController.getIntervalosHalAction',
                                       $objException->getMessage(),
                                       $strUserSession,
                                       $strIpCreacion);
        }

        $objRespuesta->setContent($objResultado);

        return $objRespuesta;
    }    


}
