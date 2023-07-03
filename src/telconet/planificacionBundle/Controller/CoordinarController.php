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
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\admiProyectos;
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
use Symfony\Component\Validator\Constraints\True;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\planificacionBundle\Service\PlanificarService;


/**
 * Documentación para la clase 'CoordinarController'.
 *
 * Clase que contiene toda la funcionalidad de la Coordinacion de las Planificaciones
 *
 * @author Desarrollo Inicial
 * @version 1.0 
 * 
 * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
 * @version 1.1 2016-05-19 Remover 'use' no utilizados
*/
class CoordinarController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_137-1")
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
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.4 21-06-2022 Se agrega perfiles para consultas de solicitudes de inspeccioón
     *
    */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        $arrayRolesPermitInsp = array();
        $objSession           = $this->get( 'session' );
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strPrefijoEmpresa    = $objSession->get('prefijoEmpresa');
        $objEmGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $objEmSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        if(true === $this->get('security.context')->isGranted('ROLE_137-103'))
        {
            $arrayRolesPermitidos[] = 'ROLE_137-103';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_137-105'))
        {
            $arrayRolesPermitidos[] = 'ROLE_137-105';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_137-104'))
        {
            $arrayRolesPermitidos[] = 'ROLE_137-104';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_137-106'))
        {
            $arrayRolesPermitidos[] = 'ROLE_137-106';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_137-225'))
        {
            $arrayRolesPermitidos[] = 'ROLE_137-225';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_145-37'))
        {
            $arrayRolesPermitidos[] = 'ROLE_145-37';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_137-5897'))
        {
            $arrayRolesPermitidos[] = 'ROLE_137-9712';
            $arrayRolesPermitidos[] = 'ROLE_137-9711'; 
        }        

        //ROLES PERMITIDOS PARA ACCIONES DE SOLICITUD DE INSPECCION
        $arrayParametrosRp['accion']     = '';
        $arrayParametrosRp['buscaTodos'] = 'S';
        $arrayParametrosRp['codEmpresa'] = $strCodEmpresa;
        $arrayRolesPermitInsp            = $this->getRolesPermitidosInspeccion($arrayParametrosRp);
        $arrayPermiteVerSolInspeccion    = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('TIPOS_SOLICITUD_GRID_PLANIFICACION_COORDINAR',
                                                                'COMERCIAL',
                                                                '',
                                                                'PermiteVerInspecciones'.strtoupper($strPrefijoEmpresa),
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '');

        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("137", "1");

        return $this->render('planificacionBundle:Coordinar:index.html.twig', array(
                'item'            => $entityItemMenu,
                'rolesPermitidos' => $arrayRolesPermitidos,
                'rolesPermitInsp' => $arrayRolesPermitInsp,
                'permitVerSolInsp'=> (isset($arrayPermiteVerSolInspeccion['valor2'])?$arrayPermiteVerSolInspeccion['valor2']:'N'),
                'codEmpresa'      => $strCodEmpresa
        ));
    }

    /*
	 * Llena el grid de consulta.
	 */
    /**
	 * @Secure(roles="ROLE_137-7")
	 *
     * @author Desarrollo Inicial
     * @version 1.0 
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-20 Agregar Esquema Comercial para realizar la consulta de los Datos de Factibilidad
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 2016-08-08 Se implementa la funcion insertError del UtilService, que permite guardar el error en la
     *                         DB_GENERAL.INFO_ERROR
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 2016-10-06 Se agrega en el grid la columna con el estado del punto
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 2017-12-15 Se envia tipo de producto a mostrar y ciudad de sesion del usuario segun su departamento
     *                         Dado que para flujos DC esta pantalla no solo la podra ver PYL sino tambien IPCCL2
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 2018-03-20 Se envia service tecnico para realizar verificacion de si un servicio es Solucion o no para gestion
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.5 2019-04-09 Se agrega PlanificarService a $datosBusqueda para poder obtener el tipo de esquema de los servicios.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 2019-07-06 Se agrega el envío de emInfraestructura, necesaria para obtener los datos del splitter clonado en el proceso 
     *                          de migración y equipos Dual Band necesarios
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.7 2019-09-30 - Se agrega serviceInfoServicio a $datosBusqueda para poder obtener el tipo de red para GPON.
     *
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.7 2020-09-21 Se modifica el proceso para que muestre varias solicitudes de un mismo servicio, cuando tenga una solicitud de 
     *                         Planificación.
     * 
     * @author Karen Rodríguez Véliz <kyrodriguez@telconet.ec>
     * @version 1.8 22-04-2021 - Se agrega validación del tipo de orden si es empresa MD
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 08-04-2022 Se verifica si el usuario en sesión tiene asignado uno de los perfiles parametrizados para la coordinación 
     *                         y activación de servicios adicionales en MD
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 2.0 21-06-2022 Se agrega el parametro ociCon para la consulta de solicitudes
     * 
     */
    public function gridAction()
    {
        $serviceUtil = $this->get('schema.Util');
        $respuesta   = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emGeneral   = $this->getDoctrine()->getManager("telconet_general");

        $peticion         = $this->get('request');
        $session          = $this->get( 'session' );

        $codEmpresa       = $session->get('idEmpresa');
        $prefijoEmpresa   = $session->get('prefijoEmpresa');
        $usrCreacion      = $session->get('user');
        $strIpClient      = $peticion->getClientIp();
        $intIdDepartamento= $session->get('idDepartamento');
        
        $fechaDesdePlanif = explode('T',$peticion->query->get('fechaDesdePlanif'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanif'));
        $fechaDesdeIngOrd = explode('T',$peticion->query->get('fechaDesdeIngOrd'));
        $fechaHastaIngOrd = explode('T',$peticion->query->get('fechaHastaIngOrd'));
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura= $this->get('doctrine')->getManager('telconet_infraestructura');
        $objEmSoporte     = $this->get('doctrine')->getManager('telconet_soporte');
        $arrayCiudad      = array();
        $strCiudad        = $peticion->query->get('ciudad');
        $datosBusqueda    = array();
        $datosBusqueda['fechaDesdePlanif'] = $fechaDesdePlanif[0];
        $datosBusqueda['fechaHastaPlanif'] = $fechaHastaPlanif[0];
        $datosBusqueda['fechaDesdeIngOrd'] = $fechaDesdeIngOrd[0];
        $datosBusqueda['fechaHastaIngOrd'] = $fechaHastaIngOrd[0];
        $datosBusqueda['tipoSolicitud']    = $peticion->query->get('tipoSolicitud');
        $datosBusqueda['estado']           = $peticion->query->get('estado');
        $datosBusqueda['ciudad']           = !empty($strCiudad)?explode(",", $peticion->query->get('ciudad')):"";
        $datosBusqueda['idSector']         = $peticion->query->get('sector');
        $datosBusqueda['identificacion']   = $peticion->query->get('identificacion');
        $datosBusqueda['vendedor']         = $peticion->query->get('vendedor');
        $datosBusqueda['nombres']          = $peticion->query->get('nombres');
        $datosBusqueda['apellidos']        = $peticion->query->get('apellidos');
        $datosBusqueda['login']            = $peticion->query->get('login');
        $datosBusqueda['descripcionPunto'] = $peticion->query->get('descripcionPunto');
        $datosBusqueda['estadoPunto']      = $peticion->query->get('estadoPunto');
        $datosBusqueda['codEmpresa']       = $codEmpresa;
        $datosBusqueda['prefijoEmpresa']   = $prefijoEmpresa;
        $datosBusqueda['ultimaMilla']      = $peticion->query->get('ultimaMilla');
        $datosBusqueda['usrCreacion']      = $usrCreacion;
        $datosBusqueda['start']            = $peticion->query->get('start');
        $datosBusqueda['limit']            = $peticion->query->get('limit');
        $datosBusqueda['emComercial']      = $emComercial;
        $datosBusqueda['emInfraestructura']= $emInfraestructura;
        $datosBusqueda['tipoConsulta']     = "GRID";
        $datosBusqueda['grupo']            = '';
        
        $datosBusqueda["ociCon"] = array('userComercial' => $this->container->getParameter('user_comercial'),
                                         'passComercial' => $this->container->getParameter('passwd_comercial'),
                                         'databaseDsn' => $this->container->getParameter('database_dsn'));

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
                                                          $session->get('idEmpresa'));
            if(!empty($arrayInfoVisualizacion))
            {
                //Si no es enviado como parametro setea por default la oficina en sesion
                if(empty($datosBusqueda['ciudad']) && $session->get('idEmpresa') != 18)
                {
                    $intIdOficina          = $session->get('idOficina');

                    $objOficina = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                    if(is_object($objOficina))
                    {
                        $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                        if(is_object($objCanton))
                        {
                            $strRegion              = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                            $datosBusqueda['grupo'] = 'DATACENTER';
                        }
                    }
                }
        
                foreach($arrayInfoVisualizacion as $array)
                {
                    $arrayProductos[]     = array($array['valor2']);
                    $arrayTipoOrden[]     = array($array['valor2']);
                    $arrayTipoSolicitud[] = array($array['valor3']);
                }
                
                if(!empty($datosBusqueda['prefijoEmpresa']) && ($datosBusqueda['prefijoEmpresa']=='MD' || $datosBusqueda['prefijoEmpresa']=='EN'))
                {
                    $datosBusqueda['tipoSolicitud']                = $arrayTipoSolicitud[0][0];
                    $datosBusqueda['arrayTipoOrden']               = $arrayTipoOrden;
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
                                                              $session->get('idEmpresa'));
                if(!empty($arrayInfoNoVisualizacion))
                {
                    foreach($arrayInfoNoVisualizacion as $array)
                    {
                        $arrayProductosExcepcion[] = array($array['valor1']);
                    }
                }
            }
        }

        $datosBusqueda['region']                            = $strRegion;
        $datosBusqueda['arrayDescripcionProducto']          = $arrayProductos;
        $datosBusqueda['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;
        $datosBusqueda['serviceTecnico']                    = $this->get('tecnico.InfoServicioTecnico');
        $datosBusqueda['planificarService']                 = $this->get('planificacion.planificar');
        $datosBusqueda['serviceInfoServicio']               = $this->get('comercial.infoservicio');
        $datosBusqueda['objEmSoporte']                      = $objEmSoporte;
        $datosBusqueda['tipoSolicitudPeticion']             = $peticion->query->get('tipoSolicitud');
        $arrayParamsVerifPerfilesCoordinar                  = array("strPrefijoSesion"      => $prefijoEmpresa,
                                                                    "strUserSesion"         => $usrCreacion,
                                                                    "strCodEmpresaSesion"   => $codEmpresa,
                                                                    "strProcesoEjecutante"  => 'GESTION_COORDINAR');
        $datosBusqueda['arrayParamsVerifPerfilesCoordinar'] = $arrayParamsVerifPerfilesCoordinar;
        if(!empty($datosBusqueda['prefijoEmpresa']) && $datosBusqueda['prefijoEmpresa']=='MD' && !empty($datosBusqueda['login'])
            && !empty($datosBusqueda['codEmpresa']))
        {
            $objPunto =   $emComercial->getRepository('schemaBundle:InfoPunto')->findOneBy(array('login' => $datosBusqueda['login'])); 
            
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
                                                                             $datosBusqueda['codEmpresa']);
                if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor) && is_object($objPunto))
                {
                    foreach($arrayParametrosValor as $arrayParametro)
                    {
                        //buscamos en el plan instalado tenga el producto
                        $arrayParametros    =    array("Punto"      => $objPunto->getId(),
                                                       "Producto"   => $arrayParametro['valor2'],
                                                       "Estado"     => 'Todos');
                        $arrayResultado     = $emComercial->getRepository('schemaBundle:InfoServicio')->getProductoByPlanes($arrayParametros);


                        if($arrayResultado['total'] > 0)
                        {
                            $datosBusqueda['prodAdicional'] = 'SI';
                        }
                    }
                }
        }
        
        try
        {
            $objJson = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->generarJsonCoordinar($datosBusqueda);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'CoordinarController->gridAction', $ex->getMessage(), $usrCreacion, $strIpClient);
        }
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     *
     * Documentación para el método 'exportarGridAction'.
     *
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
     * @version 1.6 2019-11-2019 - Se agrega servicio 'comercial.infoservicio' al arreglo '$datosBusqueda' para dar soporte a servicios GPON-TN.
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.7 2020-01-10 Se agrega el envío de objEmSoporte necesario para la función generarJsonCoordinar.
     * 
     * @author Jefferson Carrillo  <jacarrillo@telconet.ec>
     * @version 1.8 20-06-2022 - Se implementa  variable esPlanificacionMega para la solicitud de planificacion comercial.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.9 2022-06-21 - Se agrega el parámetro ociCon para la consulta de solicitudes
     * 
     *  
     * @author Liseth Candlario <lcandelario@telconet.ec>
     * @version 1.9 2022-04-07 Se consulta solicitud de excedente y se envía para la función generarJsonCoordinar.
     * 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 2.0 2023-02-16 Se quita filtro por problemas al mostrar información para el reporte de planificaciones a pyl
     */
    public function exportarGridAction()
    {
        $peticion         = $this->get('request');
        $session          = $this->get( 'session' );
        
        $codEmpresa       = $session->get('idEmpresa');
        $prefijoEmpresa   = $session->get('prefijoEmpresa');
        $usrCreacion      = $session->get('user');
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $peticion->getClientIp();
        
        $fechaDesdePlanif = explode('T',$peticion->query->get('fechaDesdePlanif'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanif'));
        $fechaDesdeIngOrd = explode('T',$peticion->query->get('fechaDesdeIngOrd'));
        $fechaHastaIngOrd = explode('T',$peticion->query->get('fechaHastaIngOrd'));
        $empresaCod       = $peticion->query->get('empresaCod');
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        $emNaf            = $this->get('doctrine')->getManager("telconet_naf");
        $emInfraestructura= $this->get('doctrine')->getManager('telconet_infraestructura');
        $datosBusqueda    = array();
        $objEmSoporte     = $this->get('doctrine')->getManager('telconet_soporte');
        $intIdSolExcedente ='';
        //Se verifica si la empresa es enviada desde el JS
        if($empresaCod)
        {
            $codEmpresa          = $empresaCod;
            $objInfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($codEmpresa);
            if($objInfoEmpresaGrupo)
            {
                $prefijoEmpresa = $objInfoEmpresaGrupo->getPrefijo();
            }
        }
        if($prefijoEmpresa=='TN')
        {
            // Se consulta solicitud de excedente
            $objTipoSolExcMaterial = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                 ->findByDescripcionSolicitud('SOLICITUD MATERIALES EXCEDENTES');      
            $intIdSolExcedente     = $objTipoSolExcMaterial[0]->getId();                      
        }
        
        $strStart = $peticion->query->get('start');
        $strLimit = $peticion->query->get('limit');

        $datosBusqueda['fechaDesdePlanif']      = $fechaDesdePlanif[0];
        $datosBusqueda['fechaHastaPlanif']      = $fechaHastaPlanif[0];
        $datosBusqueda['fechaDesdeIngOrd']      = $fechaDesdeIngOrd[0];
        $datosBusqueda['fechaHastaIngOrd']      = $fechaHastaIngOrd[0];
        $datosBusqueda['tipoSolicitud']         = $peticion->query->get('tipoSolicitud');
        $datosBusqueda['estado']                = $peticion->query->get('estado');
        $datosBusqueda['ciudad']                = explode(",", $peticion->query->get('ciudad'));
        $datosBusqueda['idSector']              = $peticion->query->get('sector');
        $datosBusqueda['identificacion']        = $peticion->query->get('identificacion');
        $datosBusqueda['vendedor']              = $peticion->query->get('vendedor');
        $datosBusqueda['nombres']               = $peticion->query->get('nombres');
        $datosBusqueda['apellidos']             = $peticion->query->get('apellidos');
        $datosBusqueda['login']                 = $peticion->query->get('login');
        $datosBusqueda['descripcionPunto']      = $peticion->query->get('descripcionPunto');
        $datosBusqueda['estadoPunto']           = $peticion->query->get('estadoPunto');
        $datosBusqueda['codEmpresa']            = $codEmpresa;
        $datosBusqueda['prefijoEmpresa']        = $prefijoEmpresa;
        $datosBusqueda['usrCreacion']           = $usrCreacion;
        $datosBusqueda['start']                 = (empty($strStart))?0:$strStart;
        $datosBusqueda['limit']                 = $strLimit;
        $datosBusqueda['emComercial']           = $emComercial;
        $datosBusqueda['tipoConsulta']          = "XLS";
        $datosBusqueda['emInfraestructura']     = $emInfraestructura;
        $datosBusqueda['serviceTecnico']        = $this->get('tecnico.InfoServicioTecnico');
        $datosBusqueda['planificarService']     = $this->get('planificacion.planificar');
        $datosBusqueda['coordinarService']      = $this->get('planificacion.coordinar');
        $datosBusqueda['serviceInfoServicio']   = $this->get('comercial.infoservicio');
        $datosBusqueda['emNaf']                 = $emNaf;
        $datosBusqueda['objEmSoporte']          = $objEmSoporte; 
        $datosBusqueda['intIdSolExcedente']     = $intIdSolExcedente;

        $datosBusqueda["ociCon"]                = array('userComercial' => $this->container->getParameter('user_comercial'),
                                                        'passComercial' => $this->container->getParameter('passwd_comercial'),
                                                        'databaseDsn'   => $this->container->getParameter('database_dsn'));
        try
        {
            $objJson = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                   ->generarJsonCoordinar($datosBusqueda);

            $objJson = json_decode($objJson,true);
            $datos   = $objJson['encontrados'];

            $this->generateExcelConsulta($datosBusqueda,$datos);
error_log("< LOG DESPUES DE generateExcelConsulta >");
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 'CoordinarController->exportarGridAction', $e->getMessage(), $usrCreacion, $strIpClient);
            return $this->indexAction();
        }
    }
    
    /**
	* @Secure(roles="ROLE_137-100")
	*/
    public function getMotivosRechazoAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        //rsaenz -- CAMBIARLO AUTOMATICO.... ESTOS SON DE LA factibilidad PLANIFICACION... HABRIA QUE CAMBIARLO         
		//cambiar ... no es accionId = 1 sino el de getMotivos
        $entitySeguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>137, "accionId"=>100));
		$relacionSistemaId = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;      		
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiMotivo')
            ->generarJson("","Activo",$start,$limit, $relacionSistemaId);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_137-101")
     * 
     * Documentación para el método 'getMotivosReplanificacionAction'.
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-10-2016 Se modifica la forma de obtener el json con los motivos de replanificación
     * 
     */
    public function getMotivosReplanificacionAction()
    {
        $objResponse    = new Response();
        $objRequest     = $this->get('request');
        $emGeneral      = $this->getDoctrine()->getManager('telconet');
        
        $arrayParametros    = array(
            "nombreModulo"  => 'coordinar',
            "nombreAccion"  => 'getMotivosReplanificacion',
            "nombreMotivo"  => $objRequest->get('query'),
            "estados"       => array(
                "estadoActivo"    => "Activo",
                "estadoModificado"=> "Modificado"
            )
        );
        $objJson        = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->getJSONMotivosPorModuloYPorAccion( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse; 
    }

    /**
     * Documentación para el método getAsignadosTareaAction, que permite obtener un HTML con los servicios que se encuentran
     * en estado AsignadoTarea en el mismo punto.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 - Versión Inicial.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 03-02-2020 | Se modifica funcionamiento para que obtenga datos del servicio en instalacion simultanea.
     * 
     * @return JsonResponse
     * 
     */
    public function getAsignadosTareaAction()
    {
        $objRequest          = $this->get('request');
        $intIdServicio       = $objRequest->get('servicioId');
        $strRespuesta        = null;

        $arrayResponse       = $this->get('comercial.infoservicio')->validaInstalacionSimultanea($intIdServicio);

        if ($arrayResponse['status'])
        {
            $objServicioTrad = $arrayResponse['objServicio'];

            if ($objServicioTrad->getEstado() == 'PrePlanificada' || $objServicioTrad->getEstado() == 'AsignadoTarea' )
            {
                $strProducto = $objServicioTrad->getProductoId()->getDescripcionProducto();
                $strEstado = $objServicioTrad->getEstado();

                $strRespuesta['status'] = 'OK';
                $strRespuesta['data'] = "<b>Instalación Simultánea WAE:</b><br/><br/>";
                $strRespuesta['data'] .= "<b>&#10140; Servicio: </b>$strProducto<br/>";
                $strRespuesta['data'] .= "<b>&#10140; Estado: </b>$strEstado<br/>";
                $strRespuesta['data'] .= "</b><p style='font-size: .7rem;color: crimson; margin-top: 5px;'>
                Verificar la disponibilidad para instalación simultanea.</p>";
            }

        }

        return new JsonResponse(($strRespuesta));

    }
    
    /**
	* @Secure(roles="ROLE_137-102")
	*/
    public function getMotivosDetenidoAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        //rsaenz -- CAMBIARLO AUTOMATICO.... ESTOS SON DE LA factibilidad PLANIFICACION... HABRIA QUE CAMBIARLO         
		//cambiar ... no es accionId = 1 sino el de getMotivos
        $entitySeguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>137, "accionId"=>102));
		$relacionSistemaId = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;         
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiMotivo')
            ->generarJson("","Activo",$start,$limit, $relacionSistemaId);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
    /**
	* @Secure(roles="ROLE_137-224,ROLE_13-225")
	*/
    public function getMotivosAnulacionAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        //rsaenz -- CAMBIARLO AUTOMATICO.... ESTOS SON DE LA factibilidad PLANIFICACION... HABRIA QUE CAMBIARLO         
		//cambiar ... no es accionId = 1 sino el de getMotivos
        $entitySeguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>137, "accionId"=>224));
		$relacionSistemaId = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;         
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiMotivo')
            ->generarJson("","Activo",$start,$limit, $relacionSistemaId);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_137-103")
     * Metodo utilizado para realizar la programacion de las solicitudes
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 30-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-05-2016 - Se guarda toda la información de la planificación en la observación del historial del servicio
     * 
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 23-02-2017 - Se agrega nuevo tipo solicitud SOLICITUD AGREGAR EQUIPO para  gestionar solicitudes generadas
     *                           en el proceso de cambio de planes donde el nuevo plan incluya como detalle un producto SMART WIFI
     * @since 1.2
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 22-01-2018 - Se agrega programación para coordinar solicitudes de reubicacion de tn
     * @since 1.3
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 26-03-2018 Se agrega validacion para asignar cupo para planificacion mobile, se unifica coordinacion y planificacion
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 26-05-2018 Se agrega validacion para controlar si hay cupo disponible en la fecha que se desea programar una solicitud
     * @return response
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 10-06-2018 bub.-Se corrije que no se asigne 2 veces cupo para una misma instalacion, solo se asigna cupo cuando se coordina
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.7 23-07-2018 bub.-Se agrega validacion a producto netvoice para que no entre a asignar recursos de red.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 15-11-2018 Para proceder con la asignación de responsable, se verifica que el proceso de planificación 
     *                         se haya realizado correctamente
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.9 04-12-2018  Se quita validación a producto netvoice para que no entre a asignar recursos de red.
     * 
     * @return response
     *
     * @author Jose Alava  <jialava@telconet.ec>
     * @version 1.10 01-04-2019  Se añade Hosting /Pool de Recursos al flujo,  se envía correo y 
     *                           se añade una tarea para la creación de maquinas virtuales
     * 
     * @author Jose Alava  <jialava@telconet.ec>
     * @version 1.11 10-04-2019  Se agrega validación para nombre tecnico, se verifica que el campo producto_id exista.
     *
     * @author Marlon Pluas  <mpluas@telconet.ec>
     * @version 1.12 06-05-2019  Se corrije variables redundantes y se valida que sea crea 1 tarea al momento de
     * coordinar.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.13 21-05-2019 - Se agrega funcionalidad para manejar el parámetro de 'idIntWifiSim' que corresponde al id
     * del servicio "INTERNET WIFI" a instalar en conjunto con el servicio tradicional.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.14 27-08-2019 - Se agrega el "InfoServicioService" al arreglo de parametros.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.15 29-11-2019 Se agrega validaciones y recepción de nuevos campos para implementar HAL en la acción programar.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.16 30-05-2020 - Se agrega a arrayParametros el indice 'arraySimultaneos' que contiene un arreglo de servicios simultaneos.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.17 30-06-2020 - Se recorre las fechas, las horas y las observaciones por cada departamento que Requiera Trabajo.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.18 23-09-2020 - Se valida los productos que no realizaran asignación de recursos de red.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 26-04-2021 - Se agrega validación para programar las ordenes de trabajo de los servicios adicionales del producto Datos SafeCity.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 18-10-2021 - Se valida la programación de las ordenes de trabajo de los servicios adicionales SafeCity.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 11-04-2022  Se agregan validaciones para evitar verificar el cupo disponible cuando se encuentre 
     *                          personalizadas las opciones de PYL
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.8 19-10-2022 - Se agrega parametrizacion de  los productos NetlifeCam y sus Tareas.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.9 20-03-2023 - Se agrega validacion por prefijo empresa Ecuanet (EN) para validar el cupo de planificacion disponible. 
     * 
     * @author Ruben Vera <rhvera@telconet.ec>
     * @version 2.2 12-05-2023 - Se corrige la fecha de inicio y fin para la programacion con Hal. 
     * 
     */
    public function programarAjaxAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");        
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $objPeticion    = $this->get('request');
        $objSession     = $objPeticion->getSession();
        $serviceGeneral = $this->get('tecnico.InfoServicioTecnico');
        $serviceSoporte  = $this->container->get('soporte.SoporteService');
        $serviceCambiarPlanService = $this->get('tecnico.InfoCambiarPlan');
        $serviceInfoServicio = $this->get('comercial.infoservicio');
        $intIdCanton    = 0;
        $strNombreTecnico = "";
        $arrayParametros['intIdPerEmpRolSesion']   = $objSession->get('idPersonaEmpresaRol');
        $arrayParametros['strOrigen']              = $objPeticion->get('origen');
        $arrayParametros['intIdFactibilidad']      = $objPeticion->get('id');
        $arrayParametros['intIdCuadrilla']         = $objPeticion->get('idCuadrilla');
        $arrayParametros['strParametro']           = $objPeticion->get('param');
        $arrayParametros['strParamResponsables']   = $objPeticion->get('paramResponsables');
        $arrayParametros['intIdPersona']           = $objPeticion->get('idPersona');
        $arrayParametros['intIdPersonaEmpresaRol'] = $objPeticion->get('idPersonaRol');
        $arrayParametros['intIdPerTecnico']        = $objPeticion->get('idPerTecnico');
        $arrayParametros['strCodEmpresa']          = $objSession->get('idEmpresa');
        $arrayParametros['strPrefijoEmpresa']      = $objSession->get('prefijoEmpresa');
        $arrayParametros['intIdDepartamento']      = $objSession->get('idDepartamento');
        $arrayParametros['serviceInfoServicio']    = $serviceInfoServicio;
        /*Array con los Id's de los servicios internet Wifi para instalacion simultanea*/
        $arrayParametros['idIntWifiSim']           = $objPeticion->get('idIntWifiSim');
        $arrayParametros['tipoEsquema']            = $objPeticion->get('tipoEsquema');
        /*Array con los Id's de los servicios COU LINEAS TELEFONIA FIJA para instalacion simultanea*/
        $arrayParametros['idIntCouSim']            = $objPeticion->get('idIntCouSim');
        /* Array con los ID's de los servicios para instalación simultanea en estado pendiente.*/
        $arrayParametros['arraySimultaneos']       = json_decode($objPeticion->get('arraySimultaneos'));
        $arrayParametros['tienePersonalizacionOpcionesGridCoordinar'] = $objPeticion->get('tienePersonalizacionOpcionesGridCoordinar');

        $intOpcion                                 = $objPeticion->get('opcion');
        $arrayParametrosSolucion = array();
        $boolNoExisteFechaProgramacion = false;
        $arrayFechaProgramacion = explode("T", $objPeticion->get('fechaProgramacion'));
        if($arrayFechaProgramacion[0] == '' || empty($arrayFechaProgramacion))
        {
            $arrayFechaProgramacionNueva = new \DateTime('now');
            $arrayFechaProgramacion = explode(" ", $arrayFechaProgramacionNueva->format('Y-m-d H:i:s'));
            $arrayFecha             = $arrayFechaProgramacion;
            $boolNoExisteFechaProgramacion = true;
        } else
        {
            $arrayFecha             = explode("T", $objPeticion->get('ho_inicio'));
        }        
        $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF             = explode(":", $arrayFecha[1]);
        $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));

        $arrayFechaI = date_create(date('Y/m/d', strtotime($arrayFecha[0])));    //Fecha de Reprogramacion

        $arrayFechaI = $arrayFechaI->format("Y-m-d");

        if($boolNoExisteFechaProgramacion)
        {
            $arrayFecha2 = $arrayFecha;
        } else
        {
            $arrayFecha2            = explode("T", $objPeticion->get('ho_fin'));
        }
        $arrayF2                = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF2            = explode(":", $arrayFecha2[1]);
        $arrayFechaInicio2      = date("Y/m/d H:i", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));
        $strHoraInicioServicio  = $arrayHoraF;
        $strHoraFinServicio     = $arrayHoraF2;
        $strHoraInicio          = $arrayHoraF;
        $strHoraFin             = $arrayHoraF2;

        $arrayParametros['dateF']                   = $arrayF;
        $arrayParametros['dateFecha']               = $arrayFecha;
        $arrayParametros['strFechaInicio']          = $arrayFechaInicio;
        $arrayParametros['strFechaFin']             = $arrayFechaInicio2;
        $arrayParametros['strHoraInicioServicio']   = $strHoraInicioServicio;
        $arrayParametros['strHoraFinServicio']      = $strHoraFinServicio;
        $arrayParametros['dateFechaProgramacion']   = $arrayFechaI;
        $arrayParametros['strHoraInicio']           = $strHoraInicio;
        $arrayParametros['strHoraFin']              = $strHoraFin;
        $arrayParametros['strObservacionServicio']  = $objPeticion->get('observacion');
        $arrayParametros['strIpCreacion']           = $objPeticion->getClientIp();
        $arrayParametros['strUsrCreacion']          = $objPeticion->getSession()->get('user');
        $arrayParametros['strObservacionSolicitud'] = $objPeticion->get('observacion');
        $arrayParametros['arrayEmpresas']           = $objPeticion->getSession()->get('arrayEmpresas');
        $arrayParametros['strAtenderAntes']         = $objPeticion->get('atenderAntes');
        $arrayParametros['strEsHal']                = $objPeticion->get('esHal');
        $arrayParametros['intIdSugerenciaHal']      = $objPeticion->get('idSugerencia');
        $boolControlaCupo = true;
        $arrayParamProducNetCam   = $serviceGeneral->paramProductosNetlifeCam();
        $strFechaReserva        = $objPeticion->get('fechaVigencia');
        $objDateFechaReserva    = new \DateTime(date('Y-m-d H:i:s',strtotime($strFechaReserva)));
        $objDateNow             = new \DateTime('now');

        if ($arrayParametros['strEsHal'] === 'S' && $objDateNow > $objDateFechaReserva)
        {
            $objRespuesta->setContent('El tiempo de reserva para la sugerencia escogida ha culminado..!!');
            return $objRespuesta;    
        }
        $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->find($arrayParametros['intIdFactibilidad']);
        
        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($entityDetalleSolicitud->getServicioId()->getId());

        $intJurisdicionId = $entityDetalleSolicitud->getServicioId()
                        ->getPuntoId()
                        ->getPuntoCoberturaId()->getId();
        
        $intControlaCupo = $entityDetalleSolicitud->getServicioId()
                        ->getPuntoId()
                        ->getPuntoCoberturaId()->getCupo();       
        
        if ( is_null($intControlaCupo) ||  $intControlaCupo <= 0 || $arrayParametros['strEsHal'] === 'S' )
        {
            $boolControlaCupo = false;
        }
        
        if(isset($arrayParametros['tienePersonalizacionOpcionesGridCoordinar']) 
            && !empty($arrayParametros['tienePersonalizacionOpcionesGridCoordinar'])
            && $arrayParametros['tienePersonalizacionOpcionesGridCoordinar'] === "SI")
        {
            $boolControlaCupo = false;
        }

        if ($arrayParametros['strPrefijoEmpresa'] == "MD" &&
            is_object($objServicio->getProductoId()) &&
            $arrayParametros['tienePersonalizacionOpcionesGridCoordinar'] === "SI")
        {
            $arrayParamsServInternetValido      = array("intIdPunto"                => $objServicio->getPuntoId()->getId(),
                                                        "strCodEmpresa"             => $objSession->get('idEmpresa'),
                                                        "arrayEstadosInternetIn"    => array("Activo"));
            $arrayRespuestaServInternetValido   = $serviceGeneral->obtieneServicioInternetValido($arrayParamsServInternetValido);
            $objServicioInternetValido          = $arrayRespuestaServInternetValido["objServicioInternet"];
            if(!is_object($objServicioInternetValido))
            {
                $objRespuesta->setContent("No es posible coordinar, debe existir un servicio de internet activo en el punto.");
                return $objRespuesta;
            }
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
        
        if (($arrayParametros['strPrefijoEmpresa'] == "MD" ||
            $arrayParametros['strPrefijoEmpresa'] == "EN") && 
            $intOpcion == 0 && $boolControlaCupo )
        {
            $strFechaPar = substr($arrayParametros['strFechaInicio'], 0, -1);
            $strFechaPar .= "1";
            $strFechaPar = str_replace("-", "/", $strFechaPar);
            $strFechaAgenda = str_replace("-", "/", $arrayParametros['strFechaInicio']);


            $intHoraCierre = $this->container->getParameter('planificacion.mobile.hora_cierre');

            $arrayPar    = array(
                "strFecha" => $strFechaPar,
                "strFechaAgenda" => $strFechaAgenda,
                "intJurisdiccion" =>  $intJurisdicionId,
                "intHoraCierre" => $intHoraCierre);

            $arrayCount  = $emComercial
                ->getRepository('schemaBundle:InfoCupoPlanificacion')
                ->getCountDisponiblesWeb($arrayPar);

            if ($arrayCount == 1)
            {
                $objRespuesta->setContent("No hay cupo disponible para este horario, seleccione otro horario por favor!");
                return $objRespuesta;            
            }
        }
        //Planifico la solicitud
        $objServicePlanificacion = $this->get('planificacion.Planificar');
        $strNombreTecnico = is_object($objServicio->getProductoId()) ? $objServicio->getProductoId()->getNombreTecnico():'';
        // En el caso de que sea hosting se envía correo 
        if($strNombreTecnico == 'HOSTING')
        {
            $arrayParametrosSolucion['objServicio']   = $objServicio;
            $arrayParametrosSolucion['strCodEmpresa'] = $objPeticion->getSession()->get('idEmpresa');
            $strSolucion = $serviceGeneral->getNombreGrupoSolucionServicios($arrayParametrosSolucion);
            $strNombreCanton = $serviceGeneral->getCiudadRelacionadaPorRegion($objServicio,$objSession->get('idEmpresa'));
            $objPunto   =  $entityDetalleSolicitud->getServicioId()->getPuntoId();
            if(!empty($strNombreCanton))
            {
                $objCanton = $emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);

                if(is_object($objCanton))
                {
                    $intIdCanton     = $objCanton->getId();
                }
            }
            $strNombreParametro = 'HOSTING TAREAS POR DEPARTAMENTO';
            $strProceso         = 'INSTALACION MV';
            $strObservacion     = 'Tarea automática: Creación de Máquinas Virtuales según recursos asignados en el '
                                . 'Producto POOL DE RECURSOS ( HOSTING ) . '
                                . '<br><b>Login : </b> '.$objPunto->getLogin()
                                . '<br>'.$strSolucion;
            
            $arrayParametrosEnvioPlantilla                      = array();
            $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $objPeticion->getSession()->get('user');
            $arrayParametrosEnvioPlantilla['strIpCreacion']     = $objPeticion->getClientIp();
            $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $entityDetalleSolicitud->getId();
            $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
            $arrayParametrosEnvioPlantilla['objPunto']          = $objPunto;
            $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
            $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $objSession->get('idEmpresa');
            $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa');
            $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
            $arrayInfoEnvio   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get($strNombreParametro, 
                                                'SOPORTE', 
                                                '',
                                                $strProceso,
                                                $strNombreCanton,//GUAYAQUIL/UIO 
                                                '',
                                                '',
                                                '', 
                                                '', 
                                                $objSession->get('idEmpresa'));
            foreach($arrayInfoEnvio as $array)                    
            {
                $objTarea  = $emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);
                $arrayParametrosEnvioPlantilla['intTarea']          = is_object($objTarea)?$objTarea->getId():'';

                if(isset($array['valor2']) && !empty($array['valor2']))
                {
                    $arrayParametrosEnvioPlantilla['arrayCorreos'] = array($array['valor2']);
                }

                $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                             ->findOneByNombreDepartamento($array['valor4']);
                $arrayParametrosEnvioPlantilla['objDepartamento']    = $objDepartamento;
                $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";

                $serviceCambiarPlanService->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
            }
        }
        
        if ($intOpcion == 0)
        {
            $arrayResultado         = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);
            if(isset($arrayResultado['codigoRespuesta']) && !empty($arrayResultado['codigoRespuesta']) && $arrayResultado['codigoRespuesta'] > 0)
            {
                $strStatusCoordinar = "OK";
            }
            else
            {
                $strStatusCoordinar = "ERROR";
            }
        }
        else
        {
            $strStatusCoordinar = "OK";
        }
        
        $objSolicitud  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($objPeticion->get('id'));
        
        $boolEsHousing = false;
        
        if(is_object($objSolicitud) && $objPeticion->getSession()->get('prefijoEmpresa') == 'TN')
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($objSolicitud->getServicioId()->getId());
            
            if(is_object($objServicio) && $objServicio->getProductoId()->getNombreTecnico() == 'HOUSING')
            {
                $boolEsHousing = true;
            }               
            
        }
        if(is_object($objSolicitud) && $objPeticion->getSession()->get('prefijoEmpresa') == 'MD')
        {
            $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(
                                                                                    array('descripcionCaracteristica' => 'PRODUCTO_ADICIONAL',
                                                                                          'estado' => 'Activo'));
            if(is_object($objCaracteristica) && !empty($objCaracteristica))
            {
                
                $objDetalleSol    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')->findOneBy(
                                                                                        array('detalleSolicitudId' => $objSolicitud->getId(),
                                                                                              'caracteristicaId'   => $objCaracteristica->getId()));
            }
        }
        //seteo el status de asignar
        $booleanStatusAsignar = false;
        //Asigno responsable
        if (
           (($arrayParametros['strEsHal'] <> "S" && $arrayParametros['strParamResponsables'] <> "" && !$boolEsHousing && $strStatusCoordinar === "OK")
            || ($arrayParametros['strEsHal'] === "S" && !$boolEsHousing && $strStatusCoordinar === "OK")) && !is_object($objDetalleSol)
           )
        {
            $booleanStatusAsignar = true;
            $arrayParametros['objDetalleSolHist']    = $arrayResultado['entityDetalleSolHist'];
            $arrayParametros['serviceRecursosRed']   = $this->get('planificacion.RecursosDeRed');
            $arrayParametros['objServicioHistorial'] = $arrayResultado['entityServicioHistorial'];
            $arrayParametros['strTipoProceso'] = 'Programar';
            $arrayResultado = $objServicePlanificacion->asignarPlanificacion($arrayParametros);
            //Regularizacion de tareas NetlifeCam Outdoor
            $arrayServAdiNet = $emComercial->getRepository('schemaBundle:InfoServicio')
                                           ->findBy(array('puntoId' => 
                                           $entityDetalleSolicitud->getServicioId()->getPuntoId(),
                                                             'estado'  => array('Asignada')));
            if(!empty($arrayServAdiNet))
            {
                foreach($arrayServAdiNet as $arrayServiciosNetCam)
                {
                    $objSolicitud  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                        ->findOneBy(array("servicioId" => $arrayServiciosNetCam->getId()));
                    $objInfoDetNet = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                        ->findOneBy(array("detalleSolicitudId" => $objSolicitud->getId()));
                    $objInfoTarNet = $emSoporte->getRepository('schemaBundle:InfoTarea')
                                        ->findOneBy(array("detalleId" => $objInfoDetNet->getId()));
                    $strNomTecNetlife    = is_object($arrayServiciosNetCam->getProductoId()) ? 
                                            $arrayServiciosNetCam->getProductoId()->getNombreTecnico() : null;
                    if(in_array($strNomTecNetlife,$arrayParamProducNetCam) && !is_object($objInfoTarNet))
                    {
                        $arrayParametrosInfoTarea['intDetalleId']   = $objInfoDetNet->getId();
                        $arrayParametrosInfoTarea['strUsrCreacion'] = $objInfoDetNet->getUsrCreacion();                      
                        $serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                    }
                }
            }
        }
        /***OBTENER LOS SERVICIOS ADICIONALES PARA PLANIFICACION***/
        if( is_object($objServicio) && is_object($objServicio->getProductoId()) && $strStatusCoordinar === "OK" )
        {
            //seteo id de orden trabajo
            $objOrdenTrabajoServ = $objServicio->getOrdenTrabajoId();
            //seteo el status de asignar
            $booleanStatusAsignar = false;
            $objSolicitud  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($objPeticion->get('id'));
            if( is_object($objSolicitud) && $objSolicitud->getEstado() == "AsignadoTarea" )
            {
                $booleanStatusAsignar = true;
            }
            //seteo variable
            $booleanServicioSwPoe = false;
            $objServicioPrincipal = $objServicio;
            $arrayParametrosDet   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                       'INFRAESTRUCTURA',
                                                       'PARAMETROS',
                                                       'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                       $objServicio->getProductoId()->getId(),
                                                       '',
                                                       '',
                                                       '',
                                                       '',
                                                       $objSession->get('idEmpresa'));
            if(!empty($arrayParametrosDet) && isset($arrayParametrosDet["valor5"]) && $arrayParametrosDet["valor5"] == "SWITCHPOE")
            {
                $booleanServicioSwPoe       = true;
                $objCaractServicioPrincipal = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                        'RELACION_SERVICIOS_GPON_SAFECITY',$objServicio->getProductoId());
                if(is_object($objCaractServicioPrincipal))
                {
                    $objServicioPrincipal = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objCaractServicioPrincipal->getValor());
                    if(!is_object($objServicioPrincipal))
                    {
                        $objServicioPrincipal = $objServicio;
                    }
                }
            }
            //obtener servicios adicionales
            $arrayParServAdd = array(
                "intIdProducto"      => $objServicioPrincipal->getProductoId()->getId(),
                "intIdServicio"      => $objServicioPrincipal->getId(),
                "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
            );
            $arrayProdCaracConfProAdd  = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
            if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
            {
                $arrayContadorAsigSerAdd = array();
                foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                {
                    $arrayEstadosSerAdd    = array("PrePlanificada","Planificada","Detenido");
                    $objServicioConfProAdd = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->findOneBy(array("id"             => $arrayServicioConfProAdd['idServicio'],
                                                                  "ordenTrabajoId" => $objOrdenTrabajoServ,
                                                                  "estado"         => $arrayEstadosSerAdd));
                    if(is_object($objServicioConfProAdd) && $objServicioConfProAdd->getId() != $objServicio->getId())
                    {
                        $objDetSolicitudProAdd = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->findOneBy(array("servicioId"      => $objServicioConfProAdd->getId(),
                                                              "tipoSolicitudId" => $entityDetalleSolicitud->getTipoSolicitudId()->getId(),
                                                              "estado"          => $arrayEstadosSerAdd));
                        if(is_object($objDetSolicitudProAdd))
                        {
                            //seteo el descripcion producto
                            $strDescripcionProductoAdd = $objServicioConfProAdd->getProductoId()->getDescripcionProducto();
                            //se obtiene los detalles del servicio adicional
                            $arrayDetallesConfProAdd = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                                     'COMERCIAL',
                                                                     '',
                                                                     '',
                                                                     $objServicioConfProAdd->getProductoId()->getId(),
                                                                     'COORDINAR_OBSERVACION',
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     $arrayParametros['strCodEmpresa']);
                            if(isset($arrayDetallesConfProAdd) && !empty($arrayDetallesConfProAdd) &&
                               !empty($arrayDetallesConfProAdd['valor3']) && !empty($arrayDetallesConfProAdd['valor4']))
                            {
                                $arrayParametros['strObservacionServicio']  = $arrayDetallesConfProAdd['valor3'];
                                $arrayParametros['strObservacionSolicitud'] = $arrayDetallesConfProAdd['valor4'];
                            }
                            $arrayParametros['intIdFactibilidad'] = $objDetSolicitudProAdd->getId();
                            $arrayParametros['strParametro']      = $objDetSolicitudProAdd->getId();
                            //se coordina el servicio adicional
                            $arrayResultadoProAdd = array();
                            if($objDetSolicitudProAdd->getEstado() == "PrePlanificada" || $objDetSolicitudProAdd->getEstado() == "Detenido")
                            {
                                $arrayResultadoProAdd = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);
                                if(isset($arrayResultadoProAdd['codigoRespuesta']) && !empty($arrayResultadoProAdd['codigoRespuesta'])
                                   && $arrayResultadoProAdd['codigoRespuesta'] > 0)
                                {
                                    $strStatusCoordinarServAdd = "OK";
                                }
                                else
                                {
                                    $strStatusCoordinarServAdd = "ERROR";
                                }
                            }
                            else
                            {
                                $strStatusCoordinarServAdd = "OK";
                            }
                            //se realiza la asignacion del servicio adicional
                            if( $strStatusCoordinarServAdd == "OK" && $booleanStatusAsignar)
                            {
                                //verificar si existe un id de tarea para validar
                                if(!empty($arrayParametros['strParamResponsables']))
                                {
                                    $arrayParamResponGpon   = explode("|", $arrayParametros['strParamResponsables']);
                                    $arrayParDetCamSafeCity = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->getOne('NUEVA_RED_GPON_TN',
                                                                        'COMERCIAL',
                                                                        '',
                                                                        'TAREA DE INSTALACION DEL SERVICIO',
                                                                        $objServicioConfProAdd->getProductoId()->getId(),
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $arrayParametros['strCodEmpresa']);
                                    if( isset($arrayParDetCamSafeCity) && !empty($arrayParDetCamSafeCity)
                                        && isset($arrayParDetCamSafeCity['valor3'])&& !empty($arrayParDetCamSafeCity['valor3']) )
                                    {
                                        //obtengo la tarea
                                        $objAdmiTareaGpon = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                    ->findOneBy(array("nombreTarea" => $arrayParDetCamSafeCity['valor3'],
                                                                                      "estado"      => "Activo"));
                                        if(is_object($objAdmiTareaGpon))
                                        {
                                            $intIdTareaGpon = $objAdmiTareaGpon->getId();
                                            //verifico los responsables
                                            foreach($arrayParamResponGpon as $keyDet => $arrayDetResponGpon)
                                            {
                                                $arrayVariablesRespon = explode("@@", $arrayDetResponGpon);
                                                if(is_array($arrayVariablesRespon) && count($arrayVariablesRespon) > 0
                                                   && $arrayVariablesRespon[0] != $intIdTareaGpon)
                                                {
                                                    $arrayVariablesRespon[0] = $intIdTareaGpon;
                                                    $arrayDetResponGpon      = implode('@@',$arrayVariablesRespon);
                                                    $arrayParamResponGpon[$keyDet] = $arrayDetResponGpon;
                                                    $arrayParametros['strParamResponsables'] = implode("|", $arrayParamResponGpon);
                                                }
                                            }
                                        }
                                    }
                                }
                                //se realiza la asignacion
                                $arrayParametros['objDetalleSolHist']    = $arrayResultadoProAdd['entityDetalleSolHist'];
                                $arrayParametros['serviceRecursosRed']   = $this->get('planificacion.RecursosDeRed');
                                $arrayParametros['objServicioHistorial'] = $arrayResultadoProAdd['entityServicioHistorial'];
                                $arrayResAsigServAdd = $objServicePlanificacion->asignarPlanificacion($arrayParametros);
                                if(isset($arrayResAsigServAdd['idDetalle']) && !empty($arrayResAsigServAdd['idDetalle']))
                                {
                                    //seteo contador
                                    if(!isset($arrayContadorAsigSerAdd[$strDescripcionProductoAdd]))
                                    {
                                        $arrayContadorAsigSerAdd[$strDescripcionProductoAdd] = 1;
                                    }
                                    else
                                    {
                                        $arrayContadorAsigSerAdd[$strDescripcionProductoAdd] += 1;
                                    }
                                    //obtengo el info detalle
                                    $objInfoDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($arrayResAsigServAdd['idDetalle']);
                                    if(is_object($objInfoDetalle))
                                    {
                                        //eliminar característica anterior
                                        $objCaracSerDetalle = $serviceGeneral->getServicioProductoCaracteristica($objServicioConfProAdd,
                                                                                'ID_DETALLE_TAREA_INSTALACION',
                                                                                $objServicioConfProAdd->getProductoId());
                                        if(is_object($objCaracSerDetalle))
                                        {
                                            $objCaracSerDetalle->setEstado("Eliminado");
                                            $objCaracSerDetalle->setUsrUltMod($objPeticion->getSession()->get('user'));
                                            $objCaracSerDetalle->setFeUltMod(new \DateTime('now'));
                                            $emComercial->persist($objCaracSerDetalle);
                                            $emComercial->flush();
                                        }
                                        //se guarda el id del detalle de la tarea
                                        $objCaracDetalle = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneByDescripcionCaracteristica('ID_DETALLE_TAREA_INSTALACION');
                                        if(is_object($objCaracDetalle))
                                        {
                                            $objAdmiProDetalle = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array("productoId"       => $objServicioConfProAdd->getProductoId()->getId(),
                                                                      "caracteristicaId" => $objCaracDetalle->getId(),
                                                                      "estado"           => "Activo"));
                                            if(is_object($objAdmiProDetalle))
                                            {
                                                $objCaracSerDetalle = new InfoServicioProdCaract();
                                                $objCaracSerDetalle->setServicioId($objServicioConfProAdd->getId());
                                                $objCaracSerDetalle->setProductoCaracterisiticaId($objAdmiProDetalle->getId());
                                                $objCaracSerDetalle->setValor($objInfoDetalle->getId());
                                                $objCaracSerDetalle->setEstado("Activo");
                                                $objCaracSerDetalle->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                                $objCaracSerDetalle->setFeCreacion(new \DateTime('now'));
                                                $emComercial->persist($objCaracSerDetalle);
                                                $emComercial->flush();
                                            }
                                        }
                                        //verifico si se actualiza el estado de la tarea del servicio por detalles del parametro de la red GPON
                                        $arrayParDetEstadoTareaGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                   ->getOne('NUEVA_RED_GPON_TN',
                                                                            'COMERCIAL',
                                                                            '',
                                                                            'CAMBIAR ESTADO TAREA SERVICIO',
                                                                            $objServicioConfProAdd->getProductoId()->getId(),
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $arrayParametros['strCodEmpresa']);
                                        if( isset($arrayParDetEstadoTareaGpon) && !empty($arrayParDetEstadoTareaGpon)
                                            && isset($arrayParDetEstadoTareaGpon['valor2'])&& !empty($arrayParDetEstadoTareaGpon['valor2'])
                                            && isset($arrayParDetEstadoTareaGpon['valor3'])&& !empty($arrayParDetEstadoTareaGpon['valor3']) )
                                        {
                                            $arrayParametrosEstado                 = array();
                                            $arrayParametrosEstado['cargarTiempo'] = "cliente";
                                            $arrayParametrosEstado['esSolucion']   = "N";
                                            $arrayParametrosEstado['estado']       = $arrayParDetEstadoTareaGpon['valor2'];
                                            $arrayParametrosEstado['observacion']  = $arrayParDetEstadoTareaGpon['valor3'];
                                            
                                            $serviceSoporte->cambiarEstadoTarea($objInfoDetalle, null, $objPeticion, $arrayParametrosEstado);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //agregar historial tarea servicio principal
                if(count($arrayContadorAsigSerAdd) > 0 && isset($arrayResultado['idDetalle']) && !empty($arrayResultado['idDetalle']))
                {
                    //obtengo el info detalle
                    $objInfoDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($arrayResultado['idDetalle']);
                    if(is_object($objInfoDetalle))
                    {
                        $strObservacionTarea = "";
                        foreach($arrayContadorAsigSerAdd as $strDescripcionProductoAdd => $strContadorProductoAdd)
                        {
                            $strObservacionTarea .= "<br>".$strContadorProductoAdd." ".$strDescripcionProductoAdd;
                        }
                        $arrayParametrosHist                   = array();
                        $arrayParametrosHist["intDetalleId"]   = $objInfoDetalle->getId();
                        $arrayParametrosHist["strCodEmpresa"]  = $objSession->get('idEmpresa');
                        $arrayParametrosHist["strUsrCreacion"] = $objSession->get('user');
                        $arrayParametrosHist["strIpCreacion"]  = $objPeticion->getClientIp();
                        $arrayParametrosHist["strOpcion"]      = "Seguimiento";
                        $arrayParametrosHist["strEstadoActual"] = "Asignada";
                        $arrayParametrosHist["strEnviaDepartamento"] = "N";
                        $arrayParametrosHist["strObservacion"] = "Se generaron las siguientes tareas de instalación ".
                                                                 "para los servicios adicionales: ".$strObservacionTarea;
                        
                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                    }
                }
            }
        }

        //Si el servicio a coordinar es un servicio adicional DATOS GPON
        if(is_object($objServicio) && is_object($objServicio->getProductoId()))
        {
            //Validar si el producto esta configurado para servicios adicionales DATOS GPON
            $arrayParametrosDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                       'INFRAESTRUCTURA',
                                                       'PARAMETROS',
                                                       'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                       $objServicio->getProductoId()->getId(),
                                                       '',
                                                       '',
                                                       '',
                                                       '',
                                                       $objSession->get('idEmpresa'));
            if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"]))
            {
                //eliminar característica anterior
                $objCaracSerDetalle = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                        'ID_DETALLE_TAREA_INSTALACION',$objServicio->getProductoId());
                if(is_object($objCaracSerDetalle))
                {
                    $objCaracSerDetalle->setEstado("Eliminado");
                    $objCaracSerDetalle->setUsrUltMod($objPeticion->getSession()->get('user'));
                    $objCaracSerDetalle->setFeUltMod(new \DateTime('now'));
                    $emComercial->persist($objCaracSerDetalle);
                    $emComercial->flush();
                }
                //se guarda el id del detalle de la tarea
                $objCaracDetalle = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('ID_DETALLE_TAREA_INSTALACION');
                if(is_object($objCaracDetalle) && isset($arrayResultado['idDetalle']))
                {
                    $objAdmiProDetalle = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                            ->findOneBy(array("productoId"       => $objServicio->getProductoId()->getId(),
                                              "caracteristicaId" => $objCaracDetalle->getId(),
                                              "estado"           => "Activo"));
                    if(is_object($objAdmiProDetalle))
                    {
                        $objCaracSerDetalle = new InfoServicioProdCaract();
                        $objCaracSerDetalle->setServicioId($objServicio->getId());
                        $objCaracSerDetalle->setProductoCaracterisiticaId($objAdmiProDetalle->getId());
                        $objCaracSerDetalle->setValor($arrayResultado['idDetalle']);
                        $objCaracSerDetalle->setEstado("Activo");
                        $objCaracSerDetalle->setUsrCreacion($objPeticion->getSession()->get('user'));
                        $objCaracSerDetalle->setFeCreacion(new \DateTime('now'));
                        $emComercial->persist($objCaracSerDetalle);
                        $emComercial->flush();
                    }
                }
                //generar recursos red
                $arrayParametros["objServicio"]                      = $objServicio;
                $arrayParametros["strProtocoloEnrutamiento"]         = $arrayParametrosDet["valor4"];
                $arrayParametros["strUsrCreacion"]                   = $objPeticion->getSession()->get('user');
                $arrayParametros["strIpCreacion"]                    = $objPeticion->getClientIp();
                $arrayParametros["strCodEmpresa"]                    = $objSession->get('idEmpresa');
                $arrayParametros["strIdDepartamento"]                = $objSession->get('idDepartamento');
                $arrayParametros["intIdDetalleSolicitudInstalacion"] = $arrayParametros['intIdFactibilidad'];
                $arrayParametros["strTipoServicio"]                  = $arrayParametrosDet["valor5"];
                $arrayResultadoCamara = $serviceInfoServicio->migrarServicioAEstadoAsignada($arrayParametros);
                if($arrayResultadoCamara['status'] != "OK")
                {
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($arrayResultadoCamara['mensaje']);
                    $objServicioHistorial->setIpCreacion($objPeticion->getClientIp());
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                }
            }
        }

        $objRespuesta->setContent($arrayResultado['mensaje']);
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_137-104")
     *  Metodo utilizado para realizar anulaciones de solicitudes
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 21-08-2014
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 28-10-2014 Se modifica liberación de Ips de servicios
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 30-12-2015 Se agrega validacion de estado del servicio al momento de ejecutar la accion
     * 
     * @author Modificado: Duval Medina C <dmedina@telconet.ec>
     * @version 1.3 2016-05-20 Para TN no eliminar información técnica, únicamente el cambio de estado.
     *                         No enviar correo to 'notificaciones_telcos@telconet.ec'
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 22-05-2016 - Se guarda toda la información de la replanificación en la observación del historial del servicio
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 30-03-2017 - Se agrega validación para que las solicitudes de agregar equipo continúen con el flujo normal de replanificación
     *                           de solicitud
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.6 20-06-2017 Se valida para que libere el numero telefonico de netvoice cuando se replanifique
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.7 04-07-2017 Validacion de estado de Servicio "Activo" no aplica para solicitudes de retiro de equipos existentes
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 17-07-2017 Se agrega validación para que se permita la replanificación de un servicio en caso de no tener última milla
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 05-09-2017 Se valida tipo de solicitud para eliminar caracteristicas e ips de servicios 
     *                         cuya solicitud sea diferente de solicitud agregar equipo
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.0 09-03-2018 Se agrega validacion para gestionar solicitudes de reubicacion
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.1 26-03-2018 Se agrega validacion para liberar cupo y asignar un nuevo cupo para planificacion mobile
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.2 26-05-2018 Se agrega validacion para controlar el numero de cupos disponibles en el horario que se desea replanificar
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.3 02/07/2018 bug.- Se invocaba un entity manager inexistente, se corrige llamando al entity manager respectivo
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.4 21-08-2018 Se agrega validación para controlar el número de cupos disponibles en el horario que se desea replanificar
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.5 25-08-2018 bug - Se corrige para que no se grabe la fecha solicitada en null 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 01-01-2019 Se agrega la opción de replanificar un producto Extender Dual Band
     *
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 2.7 26-12-2018 se valida que cuando sea un producto netvoice no se elimine la caracteristica
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 19-06-2019 Se agrega programación para poder gestionar solicitudes de cambio de equipo soporte
     * @since 2.7
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.9 Se agrega bloque de código para cambiar de estado a la tarea a "Replanificada"
     * @since 2.8
     * 
     * @author Pablo Pin <ppin@telconet.ec> 
     * @version 3.0 25-09-2019 Se agrega el servicio InfoServicioService al arreglo de parametros que se envia para  
     *                         programar la orden.  
     * @since 2.9
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.1 26-09-2019 Se agrega variable "strNuevaObsDetalleSolicitud" agrupando las observaciones
     * y actualizando en la INFO_DETALLE_SOLICITUD. 
     * @since 3.0
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 3.2 29-11-2019 Se agrega validaciones y recepción de nuevos campos para implementar HAL en la replanificación.
     * @since 3.1
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.3 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * @since 3.2
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.4 06-11-2020 - Se realiza un control para no activar las tareas que han sido cerradas.
     * @since 3.3
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 3.5 20-11-2020 - Se valida si es cableado estructurado y si es de TN para evitar hacer la asignación de la planificación.
     * @since 3.4
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.6 08-04-2021 - Se agrega validacion para poder replanificar solicitudes de cableado ethernet
     * @since 3.5
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.7 09-05-2021 Se agrega logica para llamar al service que replanifica las n cantidad de servicios adicionales que tiene el un
     *                          preferencial, ejemplo: CAMARAS SAFE-CITY
     * @since 3.6
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.8 21-05-2021 - Se anexa invocación para realizar planificacion simultanea cuando servicio posea solicitudes de CE
     * @since 3.6
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.8 18-06-2021 - Se agrega logica que valide que las tareas: FACTURACION: FACTURAS no sean replanificadas cuando se Replanifica la
     *                           orden de trabajo
     * @since 3.7
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.9 13-04-2021 Se pasa toda la lógica de programación al service para que pueda ser reutilizado desde la gestión de solicitudes
     *                         simultáneas
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.0 18-10-2021 Se valida el estado del servicio para replanificar de las ordenes de trabajo de los
     *                         servicios adicionales SafeCity.
     *
     * @return response
     */
    public function replanificarAjaxAction()
    {
        $objResponse                        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest                         = $this->getRequest();
        $intIdSolicitud                     = $objRequest->get('id');
        $intIdMotivo                        = $objRequest->get('id_motivo');
        $strBoolPerfilOpu                   = $objRequest->get('boolPerfilOpu');
        $objSession                         = $objRequest->getSession();
        $strPrefijoEmpresa                  = $objSession->get('prefijoEmpresa');
        $strFechaReplanificacion            = $objRequest->get('fechaReplanificacion');
        $strFechaHoraInicioReplanificacion  = $objRequest->get('ho_inicio');
        $strFechaHoraFinReplanificacion     = $objRequest->get('ho_fin');
        $strObservacion                     = $objRequest->get('observacion');
        $strFechaReserva                    = $objRequest->get('fechaVigencia');
        $strParamResponsables               = $objRequest->get('paramResponsables');
        $strIpCreacion                      = $objRequest->getClientIp();
        $strUsrCreacion                     = $objSession->get('user');
        $strEsHal                           = $objRequest->get('esHal');
        $strOrigen                          = $objRequest->get('origen');
        $strParamIdSolicitud                = $objRequest->get('param');
        $intIdPerTecnico                    = $objRequest->get('idPerTecnico');
        $strCodEmpresa                      = $objSession->get('idEmpresa');
        $strAtenderAntes                    = $objRequest->get('atenderAntes');    
        $intIdSugerenciaHal                 = $objRequest->get('idSugerencia');
        $intIdPerEmpRolSesion               = $objSession->get('idPersonaEmpresaRol');
        $intIdDetalleExistente              = $objRequest->get('idDetalle');
        $serviceGestionPyl                  = $this->get('planificacion.GestionPyl');
        $servicePlanificacion               = $this->get('planificacion.Planificar');
        $boolEsReplanificacionHal           = false;
        $boolMostrarMsjErrorUsr             = false;
        $emComercial                        = $this->getDoctrine()->getManager("telconet");
        try   
        {
            $objServicio         = null;
            $strEstadoServicio   = "";
            $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            if(is_object($objDetalleSolicitud))
            {
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
                if(is_object($objServicio))
                {
                    $strEstadoServicio = $objServicio->getEstado();
                }
            }

            $arrayParametrosReprogramacion      = array("intIdSolicitud"                    => $intIdSolicitud,
                                                        "intIdMotivo"                       => $intIdMotivo,
                                                        "strBoolPerfilOpu"                  => $strBoolPerfilOpu,
                                                        "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                        "strCodEmpresa"                     => $strCodEmpresa,
                                                        "strFechaReplanificacion"           => $strFechaReplanificacion,
                                                        "strFechaHoraInicioReplanificacion" => $strFechaHoraInicioReplanificacion,
                                                        "strFechaHoraFinReplanificacion"    => $strFechaHoraFinReplanificacion,
                                                        "strObservacion"                    => $strObservacion,
                                                        "strFechaReserva"                   => $strFechaReserva,
                                                        "strParamResponsables"              => $strParamResponsables,
                                                        "strIpCreacion"                     => $strIpCreacion,
                                                        "strUsrCreacion"                    => $strUsrCreacion,
                                                        "strEsHal"                          => $strEsHal,
                                                        "objRequest"                        => $objRequest
                                                        );
            $arrayResultadoReprogramar          = $serviceGestionPyl->reprogramarPlanificacion($arrayParametrosReprogramacion);
            $strStatusReprogramar               = $arrayResultadoReprogramar["status"];
            $strMensajeReprogramar              = $arrayResultadoReprogramar["mensaje"];
            $objServicioReprogramar             = $arrayResultadoReprogramar["objServicio"];
            $objServicioHistorialReprogramar    = $arrayResultadoReprogramar["objServicioHistorial"];
            if($strStatusReprogramar === "ERROR")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception($strMensajeReprogramar);
            }
            $strMensaje = $strMensajeReprogramar;
            
            if(
                ($strPrefijoEmpresa == 'TN'
                    && (($strEsHal <> "S" && $strParamResponsables <> "" && is_object($objServicioReprogramar) 
                        && is_object($objServicioReprogramar->getProductoId()) 
                        && $objServicioReprogramar->getProductoId()->getDescripcionProducto() !== "Cableado Estructurado")
                        || $strEsHal === "S"))
                || ($strPrefijoEmpresa != 'TN'
                    && (($strEsHal <> "S" && $strParamResponsables <> "") || $strEsHal === "S"))  
                )
            {
                if(isset($strEsHal) && !empty($strEsHal) && $strEsHal === "S")
                {
                    $boolEsReplanificacionHal = true;
                }
                $arrayParametrosAsignacion = array(
                                                    "strOrigen"                 => $strOrigen,
                                                    "intIdFactibilidad"         => $intIdSolicitud,
                                                    "strParametro"              => $strParamIdSolicitud,
                                                    "strParamResponsables"      => $strParamResponsables,
                                                    "intIdPerTecnico"           => $intIdPerTecnico,
                                                    "strIpCreacion"             => $strIpCreacion,
                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                    "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                    "strEsHal"                  => $strEsHal,
                                                    "boolEsReplanifHal"         => $boolEsReplanificacionHal,
                                                    "strAtenderAntes"           => $strAtenderAntes,
                                                    "intIdSugerenciaHal"        => $intIdSugerenciaHal,
                                                    "intIdPerEmpRolSesion"      => $intIdPerEmpRolSesion,
                                                    "intIdDetalleExistente"     => $intIdDetalleExistente,
                                                    "objServicioHistorial"      => $objServicioHistorialReprogramar
                                                );
                $arrayRespuestaAsignacion   = $servicePlanificacion->asignarPlanificacion($arrayParametrosAsignacion);
                $strMensaje                 = $arrayRespuestaAsignacion["mensaje"];

                /*Se identifica si es preferencial y tiene adicionales, de ser asi se envia a actualizar el estado de los servicios y
                solicitudes.*/ 
                if(is_object($objDetalleSolicitud))
                {
                    $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
                }
                if( is_object($objServicio) && $strPrefijoEmpresa == "TN" && (!isset($arrayRespuestaAsignacion['status'])
                    || $arrayRespuestaAsignacion['status'] != "ERROR") )
                {
                    $arrayParametrosAsi['strOrigen']              = $objRequest->get('origen');
                    $arrayParametrosAsi['intIdFactibilidad']      = $objRequest->get('id');
                    $arrayParametrosAsi['intIdCuadrilla']         = $objRequest->get('idCuadrilla');
                    $arrayParametrosAsi['strParametro']           = $objRequest->get('param');
                    $arrayParametrosAsi['strParamResponsables']   = $objRequest->get('paramResponsables');
                    $arrayParametrosAsi['intIdPersona']           = $objRequest->get('idPersona');
                    $arrayParametrosAsi['intIdPersonaEmpresaRol'] = $objRequest->get('idPersonaRol');
                    $arrayParametrosAsi['intIdPerTecnico']        = $objRequest->get('idPerTecnico');
                    $arrayParametrosAsi['strIpCreacion']          = $objRequest->getClientIp();
                    $arrayParametrosAsi['strUsrCreacion']         = $objRequest->getSession()->get('user');
                    $arrayParametrosAsi['strCodEmpresa']          = $objRequest->getSession()->get('idEmpresa');
                    $arrayParametrosAsi['strPrefijoEmpresa']      = $objRequest->getSession()->get('prefijoEmpresa');
                    $arrayParametrosAsi['strEsHal']               = $objRequest->get('esHal');
                    $arrayParametrosAsi['boolEsReplanifHal']      = false;
                    $arrayParametrosAsi['strAtenderAntes']        = $objRequest->get('atenderAntes');
                    $arrayParametrosAsi['intIdSugerenciaHal']     = $objRequest->get('idSugerencia');
                    $arrayParametrosAsi['intIdPerEmpRolSesion']   = $objRequest->getSession()->get('idPersonaEmpresaRol');
                    $arrayParametrosAsi['intIdDetalleExistente']  = $objRequest->get('idDetalle');
                    if($arrayParametrosAsi['strEsHal'] == 'S')
                    {
                        $arrayParametrosAsi['boolEsReplanifHal'] = true;
                    }

                    $arrayParametros['strEstadoServicio']                       = $strEstadoServicio;
                    $arrayParametros["strOpcion"]                               = "Replanificada";
                    $arrayParametros["strObservacion"]                          = $strObservacion;
                    $arrayParametros["intIdMotivo"]                             = $intIdMotivo;
                    $arrayParametros["objRequest"]                              = $objRequest;
                    $arrayParametros["objSession"]                              = $objRequest->getSession();
                    $arrayParametros["strUsrCreacion"]                          = $objRequest->getSession()->get('user');
                    $arrayParametros["strIpCreacion"]                           = $objRequest->getClientIp();
                    $arrayParametros["strOjServicioPreferencial"]               = $objServicio;
                    $arrayParametros["strFechaPlanificacion"]                   = $strFechaReplanificacion;
                    $arrayParametros["strHoraInicio"]                           = $strFechaHoraInicioReplanificacion;
                    $arrayParametros["strHoraFin"]                              = $strFechaHoraFinReplanificacion;
                    $arrayParametros["arrayParametrosAsignaciorPlanificacion"]  = $arrayParametrosAsi;
                    $arrayParametros["strPrefijoEmpresa"]                       = $strPrefijoEmpresa;
                    $arrayParametros["strCodEmpresa"]                           = $objRequest->getSession()->get('idEmpresa');
                    $serviceInfoServicioComercial = $this->get('comercial.InfoServicio');
                    $serviceInfoServicioComercial->actualizarServiciosYSolicitudesAdicionales($arrayParametros);
                }
            }
        }
        catch (\Exception $e)
        {
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un error inesperado al ejecutar la replanificación. Comuníquese con el Dep. de Sistemas";
            }
        }
        $objResponse->setContent($strMensaje);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_137-105")
     * Metodo utilizado para realizar el rechazo de las solicitudes
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 30-12-2015 Se agrega validacion de estado del servicio al momento de ejecutar la accion
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 10-01-2017 Se agrega validación para liberar puertos de INTERNET WIFI
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 30-03-2017 Se agrega validación para que las solicitudes de agregar equipo continúen con el flujo normal de rechazo de solicitud
     * 
     * @author John Vera R <javera@telconet.ec>
     * @version 1.4 21-06-2017 Se agrega validación de reverso de factibilidad
     * 
     * @author John Vera R <javera@telconet.ec>
     * @version 1.5 15-08-2017 se procede a validar que cuando sea un concentrador verifique si tiene extremos
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 04-10-2017 Se agrega código para eliminar caracteristica SERVICIO_MISMA_ULTIMA_MILLA de todos los servicios que dependan
     *                         del servicio rechazado
     * @since 1.5
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 09-03-2018 Se agrega validacion para gestionar solicitudes de reubicacion
     * @since 1.6
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.8 26-03-2018 Se agrega proceso para rechazar la solicitud en cupos de planificacion online
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 22-06-2018 Se libera factibilidad al rechazar una solicitud de factibilidad de un servicio Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 27-11-2018 Se agregan validaciones para gestiona productos de la empresa TNP
     * @since 1.9
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 06-02-2019 Se agregan validaciones para eliminar caracteristica CORREO ELECTRONICO de servicios de planes 
     *                          que incluyan McAfee como parte del mismo
     * @since 1.10
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 07-02-2019 Se libera factibilidad al rechazar una solicitud de factibilidad de un servicio TelcoHome y se rechazan los 
     *                           servicios adicionales al servicio preferencial
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.13 28-02-2019 Se agregan validaciones para servicios con tipo de orden traslados MD, se rechazaran todos 
     *                          los servicios adicionales en el destino del traslado y se activan nuevamente los servicios
     *                          adicionales en el origen del traslado
     * @since 1.11
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.14 19-06-2019 Se agrega programación para poder gestionar solicitudes de cambio de equipo soporte
     * @since 1.13
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.15 Se agrega bloque de código para cambiar de estado a la tarea a "Rechazada"
     * @since 1.14
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.16 16-03-2020 | Se agrega funcionalidad para que cuando se reciba el parámetro $arraySimultaneos
     *                            se ejecute el rechazo de los servicios simultaneos que dependenden del tradicional.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.16 Se agrega consulta del producto si tiene la marca de activación simultánea para reversar la factibilidad
     *               del producto marcado
     * @since 1.15
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.17 23-04-2020 Se agrega el envío del parámetro objProductoPref en lugar del parámetro strNombreTecnicoPref a la función 
     *                          gestionarServiciosAdicionales, debido a los cambios realizados para la reestructuración de servicios Small Business
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.18 04-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * @since 1.16
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.19 19-09-2020 Se agrega validación para flujo de rechazo de servicios W+AP con solicitudes de planificación o de agregar equipo
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 1.20 19-10-2020 
     *          - Se agrega reactivacion automatica en el punto origen de la info-serv-prod-carac 
     *            de los productos Paramount y Noggin al rechazar orden de traslado.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.21 13-11-2020 Se agrega la recreación de solicitud del servicio origen W+AP cuando se rechaza un servicio W+AP 
     *                          o el servicio de Internet que a su vez rechaza los servicios adicionales
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 1.22 28-12-2020 
     *          - Solvencia de Error al rechazar Orden de trabajo para traslado en los productos adicionales Paramount y Noggin.
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.23 01-04-2021 - Se anexa filtro para poder rechazar solicitudes de instalacion de cableado ethernet
     * 
     * @author Karen Rodríguez V.  <kyrodriguez@telconet.ec>
     * @version 1.23 26-02-2021 
     *          - Se consulta si existe solicitud de excedente de material asociada al servicio para anular
     *          - Se consulta si existe tarea por validación de excedente de material asociada a la solicitud
     *            de planificación, si existe tarea se finaliza.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.24 10-05-2021 Se modifican los parámetros enviados a la función liberarInterfaceSplitter
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.25 12-04-2021 Se pasa toda la lógica de programación al service para que pueda ser reutilizado desde la gestión de solicitudes
     *                         simultáneas
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.26 26-05-2021 Se valida si el servicio es tipo de red GPON para ejecutar el método de liberarInterfaceSplitter
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.27 07-05-2021 Se agrega logica para llamar al service que rechaza las n cantidad de servicios adicionales que tiene el un
     *                          preferencial, ejemplo: CAMARAS SAFE-CITY
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.28 10-06-2021 Se anexa variables para enviar el idDepartamento y el idPersonaEmpresaRol
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 18-10-2021 Se valida el estado del servicio para rechazar las ordenes de trabajo de los
     *                         servicios adicionales SafeCity.
     *
     * @return response
     */
    public function rechazarAjaxAction()
    {
        $objResponse                = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest                 = $this->getRequest();
        $intIdSolicitud             = $objRequest->get('id');
        $intIdMotivo                = $objRequest->get('id_motivo');
        $strObservacion             = $objRequest->get('observacion');
        $arraySimultaneos           = $objRequest->get('serviciosSimultaneos') ? json_decode($objRequest->get('serviciosSimultaneos')) : null;
        $objSession                 = $objRequest->getSession();
        $strCodEmpresa              = $objSession->get('idEmpresa');
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        $strIpCreacion              = $objRequest->getClientIp();
        $strUsrCreacion             = $objSession->get('user');
        $strEmpleadoSession         = $objSession->get('empleado');
        $intIdDepartamento          = $objSession->get('idDepartamento');
        $intIdPersonaEmpresaRol     = $objSession->get('idPersonaEmpresaRol');
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $serviceGestionPyl          = $this->get('planificacion.GestionPyl');
        $boolMostrarMsjErrorUsr     = false;
        try
        {
            $objServicio         = null;
            $strEstadoServicio   = "";
            $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            if(is_object($objDetalleSolicitud))
            {
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
                if(is_object($objServicio))
                {
                    $strEstadoServicio = $objServicio->getEstado();
                }
            }

            $arrayParametrosRechazar    = array("intIdSolicitud"            => $intIdSolicitud,
                                                "intIdMotivo"               => $intIdMotivo,
                                                "strObservacion"            => $strObservacion,
                                                "arraySimultaneos"          => $arraySimultaneos,
                                                "strCodEmpresa"             => $strCodEmpresa,
                                                "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                "objRequest"                => $objRequest,
                                                "strIpCreacion"             => $strIpCreacion,
                                                "strUsrCreacion"            => $strUsrCreacion,
                                                "strEmpleadoSession"        => $strEmpleadoSession,
                                                "intIdDepartamento"         => $intIdDepartamento,
                                                "intIdPersonaEmpresaRol"    => $intIdPersonaEmpresaRol);
            $arrayResultadoRechazar = $serviceGestionPyl->rechazarPlanificacion($arrayParametrosRechazar);
            $strStatusRechazar      = $arrayResultadoRechazar["status"];
            $strMensajeRechazar     = $arrayResultadoRechazar["mensaje"];
            if($strStatusRechazar === "ERROR")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception($strMensajeRechazar);
            }
            $strMensaje = $strMensajeRechazar;
            /*Se identifica si es preferencial y tiene adicionales, de ser asi se envia a actualizar el estado de los servicios y
            solicitudes.*/
            if(is_object($objDetalleSolicitud))
            {
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
            }
            if( $strPrefijoEmpresa == "TN" && is_object($objServicio) )
            {
                $arrayParametros['strEstadoServicio']           = $strEstadoServicio;
                $arrayParametros["strOpcion"]                   = "Rechazada";
                $arrayParametros["strObservacion"]              = $strObservacion;
                $arrayParametros["intIdMotivo"]                 = $intIdMotivo;
                $arrayParametros["objRequest"]                  = $objRequest;
                $arrayParametros["objSession"]                  = $objRequest->getSession();
                $arrayParametros["strUsrCreacion"]              = $objRequest->getSession()->get('user');
                $arrayParametros["strIpCreacion"]               = $objRequest->getClientIp();
                $arrayParametros["strOjServicioPreferencial"]   = $objServicio;
                $arrayParametros["strCodEmpresa"]               = $strCodEmpresa;
                $arrayParametros["strPrefijoEmpresa"]           = $strPrefijoEmpresa;
                $serviceInfoServicioComercial = $this->get('comercial.InfoServicio');
                $serviceInfoServicioComercial->actualizarServiciosYSolicitudesAdicionales($arrayParametros);
            }
        }
        catch (\Exception $e)
        {
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un error inesperado al detener la solicitud. Comuníquese con el Dep. de Sistemas";
            }
        }
        $objResponse->setContent($strMensaje);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_137-106")
     * Metodo utilizado para detener las solicitudes
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 30-12-2015 Se agrega validacion de estado del servicio al momento de ejecutar la accion
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 30-03-2017 Se agrega validación para que las solicitudes de agregar equipo continúen con el flujo normal al detener la solicitud
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 09-03-2018 Se agrega validacion para gestionar solicitudes de reubicacion
     * @since 1.2
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 26-03-2018 Se agrega validacion para liberar cupo para planificacion mobile
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 19-06-2019 Se agrega programación para poder gestionar solicitudes de cambio de equipo soporte
     * @since 1.4
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.6 Se agrega bloque de código para cambiar de estado a la tarea a "Detenido"
     * @since 1.5
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.7 26-09-2019 Se agrega variable "strNuevaObsDetalleSolicitud" agrupando las observaciones
     * y actualizando en la INFO_DETALLE_SOLICITUD. 
     * @since 1.6
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * @since 1.7
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.8 20-04-2021 - Se anexa filtro para poder detener solicitudes de instalacion de cableado ethernet
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 12-04-2021 Se pasa toda la lógica de programación al service para que pueda ser reutilizado desde la gestión de solicitudes
     *                         simultáneas
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 07-05-2021 Se agrega logica para llamar al service que rechaza las n cantidad de servicios adicionales que tiene el un
     *                          preferencial, ejemplo: CAMARAS SAFE-CITY
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.1 09-06-2021 - Se anexa variable para enviar el idPersonaEmpresaRol
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 18-10-2021 - Se valida el estado del servicio para detener las ordenes de trabajo de los
     *                           servicios adicionales SafeCity.
     *
     * @return response
     */
    public function detenerAjaxAction()
    {
        $objResponse                = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest                 = $this->getRequest();
        $intIdSolicitud             = $objRequest->get('id');
        $intIdMotivo                = $objRequest->get('id_motivo');
        $strObservacion             = $objRequest->get('observacion');
        $objSession                 = $objRequest->getSession();
        $strCodEmpresa              = $objSession->get('idEmpresa');
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        $intIdDepartamentoSession   = $objSession->get('idDepartamento');
        $intIdEmpleadoSession       = $objSession->get('id_empleado');
        $intIdPersonaEmpresaRol     = $objSession->get('idPersonaEmpresaRol');
        $strIpCreacion              = $objRequest->getClientIp();
        $strUsrCreacion             = $objSession->get('user');
        $serviceGestionPyl          = $this->get('planificacion.GestionPyl');
        $boolMostrarMsjErrorUsr     = false;
        $emComercial                = $this->getDoctrine()->getManager("telconet");

        try
        {
            $objServicio         = null;
            $strEstadoServicio   = "";
            $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            if(is_object($objDetalleSolicitud))
            {
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
                if(is_object($objServicio))
                {
                    $strEstadoServicio = $objServicio->getEstado();
                }
            }
            $arrayParametrosDetener = array("intIdSolicitud"            => $intIdSolicitud,
                                            "intIdMotivo"               => $intIdMotivo,
                                            "strObservacion"            => $strObservacion,
                                            "strCodEmpresa"             => $strCodEmpresa,
                                            "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                            "intIdDepartamentoSession"  => $intIdDepartamentoSession,
                                            "intIdEmpleadoSession"      => $intIdEmpleadoSession,
                                            "objRequest"                => $objRequest,
                                            "strIpCreacion"             => $strIpCreacion,
                                            "strUsrCreacion"            => $strUsrCreacion,
                                            "intIdPersonaEmpresaRol"    => $intIdPersonaEmpresaRol);
            $arrayResultadoDetener  = $serviceGestionPyl->detenerPlanificacion($arrayParametrosDetener);
            $strStatusDetener       = $arrayResultadoDetener["status"];
            $strMensajeDetener      = $arrayResultadoDetener["mensaje"];
            if($strStatusDetener === "ERROR")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception($strMensajeDetener);
            }
            $strMensaje = $strMensajeDetener;

            /*Se identifica si es preferencial y tiene adicionales, de ser asi se envia a actualizar el estado de los servicios y solicitudes.*/
            if(is_object($objDetalleSolicitud))
            {
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objDetalleSolicitud->getServicioId());
            }
            if( $strPrefijoEmpresa == "TN" && is_object($objServicio))
            {
                $arrayParametros['strEstadoServicio']                       = $strEstadoServicio;
                $arrayParametros["strOpcion"]                               = "Detenido";
                $arrayParametros["strObservacion"]                          = $strObservacion;
                $arrayParametros["intIdMotivo"]                             = $intIdMotivo;
                $arrayParametros["objRequest"]                              = $objRequest;
                $arrayParametros["objSession"]                              = $objRequest->getSession();
                $arrayParametros["strUsrCreacion"]                          = $objRequest->getSession()->get('user');
                $arrayParametros["strIpCreacion"]                           = $objRequest->getClientIp();
                $arrayParametros["strOjServicioPreferencial"]               = $objServicio;
                $arrayParametros["strPrefijoEmpresa"]                       = $strPrefijoEmpresa;
                $arrayParametros["strCodEmpresa"]                           = $objRequest->getSession()->get('idEmpresa');
                $serviceComercial = $this->container->get('comercial.InfoServicio');
                $serviceComercial->actualizarServiciosYSolicitudesAdicionales($arrayParametros);
            }
        }
        catch (\Exception $e)
        {
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Ha ocurrido un error inesperado al detener la solicitud. Comuníquese con el Dep. de Sistemas";
            }
        }
        $objResponse->setContent($strMensaje);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_137-225,ROLE_13-225")
     */
    /**
     *  Metodo utilizado para realizar anulaciones de solicitudes
     *
     *  @param integer $idDetalleSolicitud (ajax)
     *  @param integer $idMotivo (ajax)
     *  @param String  $observacion (ajax)
     *  @param String  $prefijoEmpresa (ajax)
     *  @return response
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 17-03-2020 | Se agrega parámetro $arraySimultaneos para controlar cuando sea necesario anular servicios
     *                           planificados en simultaneo.
     *                        preferencial, ejemplo: CAMARAS SAFE-CITY
     */
    public function anularAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $strRespuesta       = "";
        $id                 = $peticion->get('id');
        $id_motivo          = $peticion->get('id_motivo');
        $observacion        = $peticion->get('observacion');
        $arraySimultaneos   = $peticion->get('serviciosSimultaneos') ? json_decode($peticion->get('serviciosSimultaneos')) : null;
        $prefijoEmpresa     = $session->get('prefijoEmpresa');

        /* @var $serviceCoordinarService \telconet\planificacionBundle\Service\CoordinarService */
        $serviceCoordinarService = $this->get('planificacion.Coordinar');


        $strRespuesta = $serviceCoordinarService->anularOrdenDeTrabajo($id, $id_motivo, $observacion, $prefijoEmpresa,$peticion);

        if (!empty($arraySimultaneos) && !is_null($arraySimultaneos) && is_array($arraySimultaneos) && $strRespuesta == 'Se anulo la solicitud')
        {
            /*Construimos el arreglo de parámetros necesarios.*/
            $arrayParams = array(
                'arraySimultaneos' => $arraySimultaneos,
                'strEstadoSoli' => 'Anulada',
                'strEstadoServ' => 'Anulado',
                'strObsSolicitud' => 'Se anula solicitud debido a que el servicio tradicional en simultaneo fue anulado.',
                'strObsHistorial' => 'Servicio anulado en simultáneo',
                'idMotivo' => $id_motivo
            );

            /*Se asigna una variable con el objeto del servicio.*/
            $serviceInfoServicio = $this->get('tecnico.InfoServicioTecnico');

            /*Se llama al método encargado en el servicio asignado anteriormente.*/
            $arrayResponseSim    = $serviceInfoServicio->ejecutarRechazoSimultaneo($arrayParams);

            /*Validamos si la respuesta tuvo algún error.*/
            if ($arrayResponseSim['status'] == 'ERROR')
            {
                $strRespuesta .= ' ' . $arrayResponseSim['msg'];
            }
        }

        $respuesta->setContent($strRespuesta);
        return $respuesta;
    }
    
    public function ajaxGetSectoresAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiSector')
            ->generarJsonSectoresPorEmpresa($codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
	 * Permite generar el excel de la consulta del grid de coordinar
     * 
     * @version 1.0 
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 2016-10-07 Se agrega la columna del estado del punto en el excel que se desea exportar
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 2017-05-022 Se agrega la columna del estado del Servicio en el excel que se desea exportar
     * 
     * @author Modificado: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 2018-15-05 Se aumenta la columna fecha planificacion y campo para saber si fue planificada desde mobile
     *
     * @author Modificado: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 2018-07-11 Se muestra la caja tanto para MD y TN, solo se mostraba para MD
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 2018-09-26 Se cambia columna a utilizar para exportar resultados de planificación
     * @since 1.6
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 2019-07-11 Se agrega estilo a columna A del excel para mostrar la información de manera más ordenada
     * @since 1.7
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.9 2020-05-30 - Se agrega uso de servicio que permite obtener un resumen de los servicios en simultaneo
     *                           para colocarlos en una unica celda.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 2021-03-09 - Se agrega columna de Tipo de Red para poder mostrar si es GPON O MPLS.
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 2.1 2022-04-07 - Se agrega columna de solicitud de excedentes, un SI cuando está en aprobada .
     * 
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 2.2 2023-03-30 - Se agrega columna nombre de proyecto.
     *
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 2.2 2023-04-10 - Se agrega validacion por prefijo empresa para Ecuanet.
     * 
     */
    public static function generateExcelConsulta($datosBusqueda,$datos)
    {
        error_reporting(E_ALL);
        
        $usrCreacion       = $datosBusqueda['usrCreacion'];
        $prefijoEmpresa    = $datosBusqueda['prefijoEmpresa'];
        $serviceCoordinar  = $datosBusqueda['coordinarService'];
        $strDescripcionSol = "";
        $objPHPExcel       = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateReporteCoordinar".$prefijoEmpresa.".xls");
       
        $objPHPExcel->getProperties()->setCreator("TELCOS+");
        $objPHPExcel->getProperties()->setLastModifiedBy($usrCreacion);
        $objPHPExcel->getProperties()->setTitle("Reporte Coordinacion de Planificacion");
        $objPHPExcel->getProperties()->setSubject("Reporte Coordinacion de Planificacion");
        $objPHPExcel->getProperties()->setDescription("Reporte Coordinacion de Planificacion");
        $objPHPExcel->getProperties()->setKeywords("planificacion");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('B4',$usrCreacion);

        $objPHPExcel->getActiveSheet()->setCellValue('B5', strval(date_format(new \DateTime('now'), "d/m/Y")) );
        $objPHPExcel->getActiveSheet()->getStyle('B5')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
		
        $objPHPExcel->getActiveSheet()->setCellValue('B8',$datosBusqueda['login']);
        $objPHPExcel->getActiveSheet()->setCellValue('E8',$datosBusqueda['descripcionPunto']);
        $objPHPExcel->getActiveSheet()->setCellValue('B9',$datosBusqueda['vendedor']);
        $objPHPExcel->getActiveSheet()->setCellValue('E9',implode(",",$datosBusqueda['ciudad']));
        $objPHPExcel->getActiveSheet()->setCellValue('B10',$datosBusqueda['tipoSolicitud']);
        $objPHPExcel->getActiveSheet()->setCellValue('E10',$datosBusqueda['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('B11',$datosBusqueda['fechaDesdePlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('C11',$datosBusqueda['fechaHastaPlanif']);
        $objPHPExcel->getActiveSheet()->setCellValue('E11',$datosBusqueda['fechaDesdeIngOrd']);
        $objPHPExcel->getActiveSheet()->setCellValue('E11',$datosBusqueda['fechaHastaIngOrd']);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $i=16;		
        foreach ($datos as $data): 
            $strDescripcionSol = implode("\n",array_map('trim', explode("-", $data['descripcionSolicitud'])));
            $strDescripcionSol = wordwrap($strDescripcionSol, 40, "\n");
            $intHeightRow = 15 * (substr_count( $strDescripcionSol, "\n" )+1);
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight($intHeightRow);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $strDescripcionSol);
	        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $data['cliente']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $data['vendedor']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $data['productoServicio']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $data['tipo_orden']);
	        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $data['feCreacion']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $data['fePlanificada'] . ' ' .$data['HoraIniPlanificada']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $data['login2']);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $data['estado_punto']);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $data['latitud'].",".$data['longitud']);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $data['ciudad']);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $data['direccion']);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $data['nombreSector']);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $data['observacion']);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $data['telefonos']);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $data['estado']);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $data['estadoServicio']);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $data['motivo']);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $data['observacionMotivo']);
	        $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $data['caja']);
            if($prefijoEmpresa=="MD" || $prefijoEmpresa=="EN")
            {
                $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $data['origenPlanificacion']);                
            }
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $data['splitterMigra']);
            if($prefijoEmpresa=="TN")
            {
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $data['switch']);
				$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $data['puerto']);
                $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $data['hilo']);

                /*Unimos ambos arreglos en uno solo, para poderlo enviar a agrupar.*/
                $arraySimultaneos = array_merge(
                    !empty($data['idIntWifiSim']) ? $data['idIntWifiSim']: array(),
                    !empty($data['arraySimultaneos']) ? $data['arraySimultaneos'] : array());

                /*Si la variable es un arreglo y no esta vacío, se procede a obtener el listado resumido.*/
                if (is_array($arraySimultaneos) && !empty($arraySimultaneos))
                {
                    $strCeldaInstalacionSimultanea = $serviceCoordinar->getCeldaInstalacionSimultanea($arraySimultaneos);
                }else
                {
                    $strCeldaInstalacionSimultanea = null;
                }

                $objPHPExcel->getActiveSheet()->setCellValue('Y' . $i, !empty($strCeldaInstalacionSimultanea) ? $strCeldaInstalacionSimultanea : '-');
                $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, $data['idIntCouSim'] ? 'SI' : '-');
                $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, isset($data['strTipoRed']) && !empty($data['strTipoRed'])
                                                                     ? $data['strTipoRed'] : 'MPLS');

                //Se muestra si tiene solicitud de materiales excedentes
                $intIdServicio               = $data['id_servicio'];
                $emComercial                 = $datosBusqueda['emComercial'];
                $intIdSolExcedente           = $datosBusqueda['intIdSolExcedente'];
                $objDetalleSolicitudExcTodas = "";
                if (!empty($intIdServicio) && !empty($intIdSolExcedente))
                {
                    $objDetalleSolicitudExcTodas = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findUlitmoDetalleSolicitudByIds( $intIdServicio,$intIdSolExcedente);
                }
                if(is_object($objDetalleSolicitudExcTodas))
                {
                    $strEstadoSolicitudExcTodas  = $objDetalleSolicitudExcTodas->getEstado();
                    if($strEstadoSolicitudExcTodas=='Aprobado')
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, 'SI' );
                    }
                    else
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, '-' );
                    }
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, '-' );
                }
                $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, isset($data['nombreProyecto']) ? $data['nombreProyecto'] : '');
                $objPHPExcel->getActiveSheet()->setCellValue('AD'.$i, isset($data['fechaIngArticulo']) ? $data['fechaIngArticulo'] : '');
            }
            $i=$i+1;
        endforeach;
	
        $arrayStyle = array('alignment' => array('vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
        $objPHPExcel->getActiveSheet()->getStyle("A16:X".($i-1))->applyFromArray($arrayStyle);
	$objPHPExcel->getSecurity()->setWorkbookPassword("PHPExcel");
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Coordinar_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     *
     * Método utilizado para ingresar seguimientos a la Orden de Trabajo.
     *
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.0 24-09-2019
     * 
     * Se realiza cambio de lógica para guardar el estado del servicio y solicitud
     * respectivamente.
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.1 28-02-2020
     * 
     * @return response
     */
    public function ingresarSeguimientoAjaxAction()
    {
        $serviceUtil            = $this->get('schema.Util');
        $objRespuesta           = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion            = $this->get('request');
        $intIdDetalleSolicitud  = $objPeticion->get('id');
        $strSeguimiento         = $objPeticion->get('seguimiento');
        $objSession             = $objPeticion->getSession();
        $emGeneral              = $this->getDoctrine()->getManager();
        $entityDetalleSolicitud = $emGeneral->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdDetalleSolicitud);

        $emGeneral->getConnection()->beginTransaction();

        try
        {
            if ($entityDetalleSolicitud)
            {     
                //ACTUALIZAR OBSERVACIÓN
                $strNuevaObsDetalleSolicitud = $entityDetalleSolicitud->getObservacion().
                                                "\n".
                                               'Seguimiento: ' .$strSeguimiento;
                $entityDetalleSolicitud->setObservacion($strNuevaObsDetalleSolicitud);
                $emGeneral->persist($entityDetalleSolicitud);
                $emGeneral->flush();

                $entityServicio = $emGeneral->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());

                //GUARDAR INFO SERVICIO HISTORIAL
                $entityServicioHistorial = new InfoServicioHistorial();
                $entityServicioHistorial->setServicioId($entityServicio);
                $entityServicioHistorial->setObservacion('<b>Seguimiento:</b> '.$strSeguimiento);
                $entityServicioHistorial->setIpCreacion($objPeticion->getClientIp());
                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                $entityServicioHistorial->setUsrCreacion($objSession->get('user'));
                $entityServicioHistorial->setEstado($entityServicio->getEstado());
                $entityServicioHistorial->setAccion('Seguimiento');

                $emGeneral->persist($entityServicioHistorial);
                $emGeneral->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setObservacion('<b>Seguimiento:</b> '.$strSeguimiento);
                $entityDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($objSession->get('user'));
                $entityDetalleSolHist->setEstado($entityDetalleSolicitud->getEstado());

                $emGeneral->persist($entityDetalleSolHist);
                $emGeneral->flush();

                $objRespuesta->setContent("Se ingreso el seguimiento.");
            }
            else
            {
                $objRespuesta->setContent("No existe el detalle de solicitud");
            }
            $emGeneral->getConnection()->commit();
        }
        catch (\Exception $e)
        {
            $emGeneral->getConnection()->rollback();

            $serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->ingresarSeguimientoAjaxAction', 
                                        $e->getMessage(), 
                                        $objSession->get('user'), 
                                        $objPeticion->getClientIp()
                                    );

            $strMensajeError = "Error: " . $e->getMessage();
            $objRespuesta->setContent($strMensajeError);
        }
        return $objRespuesta;
    }

     /**
     *
     * Método empleado para realizar la planificación del producto Datos FWA.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 14-11-2019
     *
     * @return response
     */
    public function planificarFwaAjaxAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura  = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intId              = $objRequest->get('idServicio');
        $strClienteIp       = $objRequest->getClientIp();
        $strUsrCreacion     = $objSession->get('user');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');


        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $serviceTecnico      = $this->get('tecnico.InfoServicioTecnico');

        try
        {
            if("TN" === $strPrefijoEmpresa)
            {
                $objEntityServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intId);
                $strNombreProducto = '';
                if(is_object($objEntityServicio))
                {
                    $objProducto = $objEntityServicio->getProductoId();
                    if(is_object($objProducto))
                    {
                        $strNombreProducto = $objProducto->getNombreTecnico();
                    }
                }
                    if($strNombreProducto === 'DATOS FWA')
                    {
                        $boolConcentradorPorActivar = true;
                        //Consultar el concentrador virtual si esta activo.
                        if(is_object($objEntityServicio->getPuntoId()))
                        {
                            $objPersonaEmpresaRol = $objEntityServicio->getPuntoId()->getPersonaEmpresaRolId();
                            $intIdOfiServ         = is_object($objEntityServicio->getPuntoId()->getPuntoCoberturaId()) ?
                                                                $objEntityServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId() : 0;
                            $objOficina           = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                ->find($intIdOfiServ);
                            if(is_object($objOficina))
                            {
                                $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                                  ->find($objOficina->getCantonId());
                                if(is_object($objCanton))
                                {
                                    $strRegionServicio = $objCanton->getRegion();
                                }
                            }
                            if(is_object($objPersonaEmpresaRol))
                            {
                                //Consultar si tiene un concentrador virtual de INTERCONEXION
                                $objCaracConcentradorFWA = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                       ->findOneBy(array(
                                                                                          "descripcionCaracteristica" => 'CONCENTRADOR_FWA',
                                                                                          "estado"                    => 'Activo'
                                                                                        ));
                                $arrayParamConcentraInter= $emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                                     ->getOne('CONCENTRADOR INTERCONEXION FWA',
                                                                              'COMERCIAL',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '');
                                if( isset($arrayParamConcentraInter['valor1']) && !empty($arrayParamConcentraInter['valor1']) )
                                {
                                    $strNombreTecnico = $arrayParamConcentraInter['valor1'];
                                }
                                $objProductoConcinter    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                       ->findOneBy(array("nombreTecnico"   =>  $strNombreTecnico,
                                                                                         "estado"          =>  "Activo",
                                                                                         "esConcentrador"  =>  "SI",
                                                                                         "empresaCod"      =>  $strCodEmpresa));
                                $objAdmiProdCaract       = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                       ->findOneBy(array('caracteristicaId' => $objCaracConcentradorFWA,
                                                                                         'productoId'       => $objProductoConcinter,
                                                                                         'estado'           => "Activo"
                                                                                        )
                                                                                   );

                                $arrayConcentradorVirtual= $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                       ->findBy(array('productoCaracterisiticaId' => $objAdmiProdCaract->getId(),
                                                                                      'servicioId'                => $objEntityServicio->getId(),
                                                                                      'estado'                    => "Activo"));
                                if(isset($arrayConcentradorVirtual) && !empty($arrayConcentradorVirtual))
                                {
                                    foreach($arrayConcentradorVirtual as $objPerEmprRolCarac)
                                    {
                                        $objServicioConcentradorVirtual = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                      ->findOneById($objPerEmprRolCarac->getValor());

                                        $intIdOfiServConcentra          = is_object($objServicioConcentradorVirtual->getPuntoId()
                                                                                                                   ->getPuntoCoberturaId()) ?
                                                                            $objServicioConcentradorVirtual->getPuntoId()->getPuntoCoberturaId()
                                                                                                                         ->getOficinaId() : 0;
                                        $objOfiConcentrador             = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                                      ->find($intIdOfiServConcentra);
                                        if(is_object($objOfiConcentrador))
                                        {
                                            $objServicioTecnico         = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                      ->findOneByServicioId($objPerEmprRolCarac->getValor());
                                            if(!is_object($objServicioTecnico))
                                            {
                                                throw new \Exception("No se puede obtener el elemento del concentrador");
                                            }
                                            $objElementoConcentradorFWA = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                            ->findOneById($objServicioTecnico->getElementoId());
                                            if(!is_object($objElementoConcentradorFWA))
                                            {
                                                throw new \Exception("No existe el elemento del concentrador");
                                            }
                                            $arrayParametrosWs["intIdElemento"] = $objElementoConcentradorFWA->getId();
                                            $arrayParametrosWs["intIdServicio"] = $objPerEmprRolCarac->getValor();

                                            $objElementoPe  = $serviceTecnico->getPeBySwitch($arrayParametrosWs);
                                            if(!is_object($objElementoPe))
                                            {
                                                throw new \Exception("No se puede obtener el pe del concentrador");
                                            }
                                            $arrayUbicacionPeFWA   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                       ->getUbicacionElementoNodo($objElementoPe->getId());
                                            if(empty($arrayUbicacionPeFWA))
                                            {
                                                throw new \Exception("No se puede obtener la ubicación del pe del concentrador");
                                            }
                                            $intCantonPeFWA = $arrayUbicacionPeFWA[0]['idCanton'];
                                            $objCantonPeFWA = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                                          ->findOneById($intCantonPeFWA);
                                            if(is_object($objCantonPeFWA))
                                            {
                                                $strRegionConcentradorVirtual = $objCantonPeFWA->getRegion();
                                            }

                                        }

                                        if($strRegionServicio == $strRegionConcentradorVirtual)
                                        {
                                            if($objServicioConcentradorVirtual->getEstado() == 'Activo')
                                            {
                                                $boolConcentradorPorActivar = false;
                                            }
                                            else
                                            {
                                                $boolConcentradorPorActivar = true;
                                            }
                                            break;
                                        }
                                    }
                                }
                                else
                                {
                                    throw new \Exception("No existe la característica CONCENTRADOR_FWA, este servicio debe estar previamente enlazado"
                                                        . " a un concentrador.");
                                }
                            }
                        }
                        if($boolConcentradorPorActivar)
                        {
                            throw new \Exception("El concentrador virtual FWA no esta activo aún ");
                        }

                    }
                    $strContent = $serviceInfoServicio->solicitarFactibilidadServicio($strCodEmpresa,
                                                                                      $strPrefijoEmpresa,
                                                                                      $intId,
                                                                                      $strUsrCreacion,
                                                                                      $strClienteIp);
            }
        }
        catch(\Exception $e)
        {
            $strContent = $e->getMessage();
        }

        $objResponse->setContent($strContent);
        return $objResponse;
    }
    
    /**
     * verTareasClientesAjaxAction obtiene el listado de tareas por login.
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 18-03-2020
     *
     * @return response
     */
    public function verTareasClientesAjaxAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager("telconet");       
        $objRequest         = $this->getRequest();
        $strLogin           = $objRequest->get('login');        
        
        $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")
                                      ->getTareasClientes(array('strLogin'    => $strLogin,
                                                                'strEstado'   => '',
                                                                'strFechaIni' => '',
                                                                'strFechaFin' => '',
                                                                'strMostrarAsignado'  => 'S',
                                                                'strVerTareasValidas' => 'S'));
        
        return new JsonResponse($arrayRespuesta);      
    }
    
    /**
     *
     * Método empleado listar los pedidos de un servicio siempre y cuando este tenga cotización registrada.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 07-05-2020
     *
     * @return response
     */
    public function pedidosByServicioAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intId              = $objRequest->get('idServicio');
        $strClienteIp       = $objRequest->getClientIp();
        $strUsrCreacion     = $objSession->get('user');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $arrayPedidos       = array();
        $serviceUtil        = $this->get('schema.Util');
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');
        $servicePlanificar  = $this->get('planificacion.planificar');
        $intContador=0;
        
        try
        {
        $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intId);
        
        if(is_object($objServicio) && !empty($objServicio))
        {
            $objServCaractCotizacion = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                  'COTIZACION_PRODUCTOS',
                                                                                                  $objServicio->getProductoId()
                                                                                                  );
            if(is_object($objServCaractCotizacion) && !empty($objServCaractCotizacion))
            {
                $arrayDatos = array("nombreAplicativo" => 'TELCOS',
                                         "numeroReferencia" => $objServCaractCotizacion->getValor(),
                                         "ipCreacion"       => $strClienteIp,
                                         "origen"           => 'N',
                                         "usrCreacion"      => $strUsrCreacion,
                                         "codEmpresa"       => $strCodEmpresa,
                                         "prefijoEmpresa"   => $strPrefijoEmpresa
                );
                $arrayFuente = array("name"          => 'Consulta_Pedidos',
                                     "originID"      => $strClienteIp,
                                     "tipoOriginID"  => 'IP'
                );
                
                $arrayParametros = array(
                                        "datos" => $arrayDatos,
                                        "op"    => "SELECT",
                                        "fuente"=> $arrayFuente
                );
                
                $arrayRespuesta = $servicePlanificar->getPedidosByDepartamento($arrayParametros);
                
                if($arrayRespuesta['status']==200)
                {
                    foreach($arrayRespuesta['datos'] as $arrayDepartamento)
                    {
                        foreach($arrayDepartamento['articulos'] as $arrayArticulo)
                        {   
                            $intContador=$intContador+1;
                            array_push($arrayPedidos, array('pedidoId'=>$arrayDepartamento['numeroPedido'],
                                'departamento'=> $arrayDepartamento['nombreDepartamento']."   /   NUM_PEDIDO:".$arrayDepartamento['numeroPedido'],
                                'taskId'=> $intContador,
                                'articulo'=> $arrayArticulo['descripcion'], 
                                'cantidad'=> $arrayArticulo['cantidad'],
                                'estado'=> $arrayArticulo['estado'],
                                'codArticulo'=>$arrayArticulo['codArticulo'],
                                'usrAsignado'=>$arrayArticulo['usrAsignado']));
                        }
                    }
                }
                
            }
        }
       
        }
        catch (\Exception $e)
        {
            $serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->pedidosByServicioAction', 
                                        $e->getMessage(), 
                                        $objSession->get('user'), 
                                        $strClienteIp
                                    );
            $intContador=0;
            $arrayPedidos       = array();
        }

        $objResponse = new Response(json_encode(array('total' => $intContador, 'pedidos' => $arrayPedidos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }
    
    /**
     *
     * Método que ejecuta algoritmo de autorización automática o no en exceso de material.
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 04-01-2021
     *
     * @author Mario Ayerve E. <mayerve@telconet.ec>
     * @version 1.1 20-04-2021
     * 
     * @author Liseth Candelario. <lcandelario@telconet.ec>
     * @version 1.2 07-10-2021
     * Se modifica o agrega los valores a enviar para la trazabilidad de la OT
     * 
     * @return response
     */
    public function validadorExcedenteMaterialAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse              = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial              = $this->getDoctrine()->getManager();
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();
        $intId                    = $objRequest->get('intIdServicio');
        $strClienteIp             = $objRequest->getClientIp();
        $strUsrCreacion           = $objSession->get('user');
        $strCodEmpresa            = $objSession->get('idEmpresa');
        $serviceUtil              = $this->get('schema.Util');
        $serviceEnvioPlantilla    = $this->get('soporte.EnvioPlantilla');
        $serviceSolicitud         = $this->get('comercial.Solicitudes');
        $serviceAutorizaciones    = $this->get('comercial.Autorizaciones');
        $emGeneral                = $this->getDoctrine()->getManager("telconet_general");
        $intIdDetalleSolicitud    = $objRequest->get('detalleSolId');
        $strEvidencia             = $objRequest->get('strEvidencia');
        $floatPrecioFibra         = $objRequest->get('precioFibra');
        $intMetrosFibra           = $objRequest->get('metrosFibra');
        $floatPrecioObraCivil     = $objRequest->get('precioObraCivil');
        $floatPrecioOtrosMate     = $objRequest->get('precioOtrosMate');
        
        // Total de Precio de obra civil y otros materiales
        $floatSubTotalOtrosClientes = $objRequest->get('subTotalOtrosClientes'); 
        // % que el cliente cancela
        $floatCanceladoPorCliente   = $objRequest->get('canceladoPorCliente');   
        // $ valor dependiente del % que el cliente asume
        $floatAsumeCliente          = $objRequest->get('asumeCliente');          
        $floatAsumeEmpresa          = $objRequest->get('asumeEmpresa');
        // Total a pagar, suma de los otros totales
        $floatTotalPagar            = $objRequest->get('totalPagar');            
        $strObservacion             = $objRequest->get('observacion');
        $strModulo                  = $objRequest->get('modulo');
        $strAccion                  = 'validaExcedenteMaterial';
        
        $floatSumaExcedente    = floatval($floatTotalPagar);
        $floatValidacionMRC    = 0; 
        $floatMCREnlaceActual  = 0;
        $boolAutorizado        = false;
        $boolExisteSolicitud   = false;
        $boolModuloQueValida   = false;
        $strRespuesta          = '';
        $boolAplicativoValida  = false;
        $boolPreplanificaAutomaticamente = false;

        // ----activa o no la validación de excedentes de materiales
        if($strModulo =='PLANIFICACION')
        {
            $boolModuloQueValida   = false;
        }
        else if(($strModulo =='COMERCIAL'))
        {
            $boolModuloQueValida   = true;
        }

        try
        {
        $emComercial->getConnection()->beginTransaction();
        $objServicio     = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intId);
        if(is_object($objServicio))
        {
            $strEstadoServicio     = $objServicio->getEstado();
        }

        
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                               ->findOneBy(array("descripcion"=>'Validaciones para excedente de material', 
                                                 "modulo"=>'PLANIFICACIÓN',
                                                 "estado"=>'Activo'));
        if(is_object($objParametroCab))
        {
 
        $objParamValidacion1 = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                            "descripcion" => 'VALIDACION1',
                                            "parametroId" => $objParametroCab->getId(),
                                            "estado"      => 'Activo'));

        $objParamValidacion2 = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                            "descripcion" => 'VALIDACION2',
                                            "parametroId" => $objParametroCab->getId(),
                                            "estado"      => 'Activo' ));
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>Validaciones para excedente de material</b>');
        }

        $objParametroCabCondicional = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'ESTADO PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES', 
                                                                    "modulo"=>'COMERCIAL',
                                                                    "estado"=>'Activo'));
        if(is_object($objParametroCabCondicional))
        {                                                        
            $objParamDetCondicional = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                "descripcion" => 'CONDICIONAR PREPLANIFICACION EXCEDENTES',
                                                                "parametroId" => $objParametroCabCondicional->getId(),
                                                                "estado"      => 'Activo'));
            //Variable de decisión para preplanificación.
            if(!is_object($objParamDetCondicional))
            {
                throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>CONDICIONAR PREPLANIFICACION EXCEDENTES</b>');
            }
            else
            {
                $strEstadoCondicional  = $objParamDetCondicional->getValor1();
            }
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>ESTADO PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES</b>');
        }


        $objParametroCabCodigo = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN', 
                                                                    "modulo"=>'COMERCIAL',
                                                                    "estado"=>'Activo'));
        if(is_object($objParametroCabCodigo))
        {
            $objParamDetCodigo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array("descripcion" => 'CODIGO DE MATERIAL DE FIBRA OPTICA',
                                                                  "parametroId" => $objParametroCabCodigo->getId(),
                                                                  "estado"      => 'Activo'));
            //Variable del código del material para insertar a la infodetalleSolMaterial.
            if(!is_object($objParamDetCodigo))
            {
                throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>CODIGO DE MATERIAL DE FIBRA OPTICA</b>');
            }
            else
            {    
                $strCodigoMaterial  = $objParamDetCodigo->getValor1();
            }
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ UN PARÁMETRO <br> <b>INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN</b>');
        }

        $objCaracteristicaFibra  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD PRECIO');

        $objCaracteristicaFibraMetros  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD');

        $objCaracteristicaOCivil = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
        if(!is_object($objCaracteristicaOCivil))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>OBRA CIVIL PRECIO</b>');
        }

        $objCaracteristicaOtrosMat = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
        if(!is_object($objCaracteristicaOtrosMat))
        { 

            throw new \Exception(': NO SE ENCONTRÓ <br> <b>OTROS MATERIALES PRECIO</b>');
        }

        $objCaracteristicaCanceladoCliente = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneByDescripcionCaracteristica('COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE');
        if(!is_object($objCaracteristicaCanceladoCliente))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE</b>');
        }

        $objCaracteristicaAsumeCliente = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS ASUME EL CLIENTE PRECIO');   
        if(!is_object($objCaracteristicaAsumeCliente))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME EL CLIENTE PRECIO</b>');
        }

        $objCaracteristicaAsumeEmpresa = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS ASUME LA EMPRESA PRECIO');
        if(!is_object($objCaracteristicaAsumeEmpresa))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME LA EMPRESA PRECIO</b>');
        }

        // CARACTERÍSTICA DE TAREA EXCESO DE MATERIAL
        $objParametroTarea             = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro'=>'TAREA EXCESO DE MATERIAL'));
        
        //DATOS DE LA SOLICITUD MATERIALES EXCEDENTES
        $entityTipoSolicitud           = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");

        //DATOS DE LA SOLICITUD PLANIFICACION                                        
        $entityTipoSolicitudPla        = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

         //DATOS DE LA SOLICITUD FACTIBILIDAD
        $entityTipoSolicitudFactibilidad = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");
        
        // Obtenemos el Correo de GTN .
        $objParametroCargo      = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                    ->findOneBy(array("descripcion"=>'Cargo que autoriza excedente de material', 
                                                    "modulo"=>'PLANIFICACIÓN',  "estado"=>'Activo'));

        $objCargoAutoriza       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->findOneBy(array("descripcion"   => 'Cargo que recibirá solicitud de excedente de material', 
                                                    "parametroId" => $objParametroCargo->getId(), "estado"      => 'Activo'));

        $objDepartamento        = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                        ->findOneBy(array("nombreDepartamento" =>$objCargoAutoriza->getValor2(),
                                                        "estado"             =>'Activo'));

        $objRol                  = $emGeneral->getRepository('schemaBundle:AdmiRol')
                            ->findOneBy(array("descripcionRol" => $objCargoAutoriza->getValor1()));
        
        $objEmpresaRol           = $emGeneral->getRepository('schemaBundle:InfoEmpresaRol')
                                    ->findOneBy(array("rolId"      => $objRol->getId(),
                                                    "empresaCod" => $strCodEmpresa, "estado"     => 'Activo'));
        
        $objPersonaEmpresaRol    = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findOneBy(array("empresaRolId"   => $objEmpresaRol->getId(),
                                                            "departamentoId" => $objDepartamento->getId(),
                                                            "estado"         => 'Activo'));

        $arrayFormasContactoAGtn = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getContactosByLoginPersonaAndFormaContacto($objPersonaEmpresaRol
                                            ->getPersonaId()->getLogin(),'Correo Electronico');


        //Obtenemos la forma de contacto de alias PYL y comercial - - CORREOS A ENVIAR LOS VALORES DE EXCEDENTES
        $objParametroCabCorreosExce = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'CORREOS A ENVIAR LOS VALORES DE EXCEDENTES', 
                                                                    "modulo"=>'COMERCIAL',
                                                                    "estado"=>'Activo'));

        if(is_object($objParametroCabCorreosExce))
        {
            $strDescParametroDetCorreoCom   = 'CORREOS QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES A COMERCIAL';
            $strDescParametroDetPyl         = 'CORREOS QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES AL ALIAS PYL';
            $arrayCorreosParaExcedente      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('CORREO_EXCEDENTES', 
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $objSession->get('idEmpresa'));

            if(isset($arrayCorreosParaExcedente) && !empty($arrayCorreosParaExcedente))
            {
                foreach($arrayCorreosParaExcedente as $arrayValores)
                {
                    if($arrayValores['descripcion'] == $strDescParametroDetCorreoCom)
                    {
                        // Formas de Contacto alias Comercial
                        $arrayDestinatario[] = $arrayValores["valor1"];
                    }

                    if($arrayValores['descripcion'] == $strDescParametroDetPyl)      
                    {
                        // Formas de Contacto alias Pyl
                        $arrayDestinatario[] = $arrayValores["valor1"];
                    }                    
                }
            }
            else
            {
                throw new \Exception(': NO SE ENCONTRÓ UN CORREO PARA ENVIAR LA NOTIFICACIÓN');
            }         
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ UN PARÁMETRO <br> <b> CORREOS A ENVIAR LOS VALORES DE EXCEDENTES </b>');
        }

        //Obtenemos la forma de contacto del creador del servicio
        $arrayFormasContactoAsistente = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
                                                ->getUsrCreacion(),'Correo Electronico');
        
        //Obtenemos la forma de contacto del asesor LOGIN_AUX
        $arrayFormasContactoAsesor = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
                                                ->getUsrVendedor(),'Correo Electronico');

        /* INI Validamos si existe solicitud de excedente de materiales en estado pendiente*/
        $objDetalleSolicitudExc     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                   ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                       "estado"         => 'Pendiente',
                                                      "tipoSolicitudId" => $entityTipoSolicitud->getId()));
        
        $objTipoSolExcMaterial = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                            ->findByDescripcionSolicitud('SOLICITUD MATERIALES EXCEDENTES');
        $objDetalleSolicitudExcTodas = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findUlitmoDetalleSolicitudByIds( $objServicio->getId(),$objTipoSolExcMaterial[0]->getId());
        if(is_object($objDetalleSolicitudExcTodas))
        {
            $strEstadoSolicitudExcTodas  = $objDetalleSolicitudExcTodas->getEstado();
            $intIdSolicitudExcedenteTodas = $objDetalleSolicitudExcTodas->getId();

        }
        
        if(is_object($objServicio) && !empty($objServicio) &&
          is_object($objParametroCab) && !empty($objParametroCab) &&
          is_object($objParametroTarea) && !empty($objParametroTarea)&&
          is_object($entityTipoSolicitud) && !empty($entityTipoSolicitud))
        {
            //Obtenemos el detalle de solicitud de planificación
            $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                              ->findOneById($intIdDetalleSolicitud);

            // Solicitud de planificacion
            $objDetalleSolicitudPla = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                                   "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));
            if(is_object($objDetalleSolicitudPla))
            {
                $strEstadoInfoDetalleSolicitud     = $objDetalleSolicitudPla->getEstado();
                $intIdInfoDetalleSolicitudPlan     = $objDetalleSolicitudPla->getId();

                // Consulta si existe información en la info_solicitud_material de ese servicio
                $entityDetalleSolMaterial = $emComercial->getRepository('schemaBundle:InfoDetalleSolMaterial')
                                ->findOneBy(array( "detalleSolicitudId" => $objDetalleSolicitudPla->getId()));
            }

            $objDetalleSolicitudFactibilidad      = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneBy(array( "servicioId" => $objServicio->getId()  
                                                                         ,"tipoSolicitudId" => $entityTipoSolicitudFactibilidad->getId()
                                                        ));

            if(is_object($objDetalleSolicitudFactibilidad))
            {
                $strEstadoSolicitudFactibilidad     = $objDetalleSolicitudFactibilidad->getEstado();
            }

            if(is_object($objDetalleSolicitudPla))
            {
            //Consulta si existe detalleSolCaract de fibra, obra civil, otros materiales, 
            //copagos(% que cancela el cliente, y precio que asume empresa o cliente)
            $objInfoDetalleSolCaractFibra = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractFibraMetros = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibraMetros,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractOCivil      = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractOtrosMat    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMat,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractCanceladoCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                             'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                                ));
            $objInfoDetalleSolCaractAsumeCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                                ));
            $objInfoDetalleSolCaractAsumeEmpresa  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                        'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                                ));
                                                                
               if(!is_object($objInfoDetalleSolCaractFibra)  && empty($objInfoDetalleSolCaractFibra))
                    {
                        //Guardamos los valores de fibra en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibra  = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractFibra->setCaracteristicaId($objCaracteristicaFibra);
                        $objInfoDetalleSolCaractFibra->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractFibra->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractFibra->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibra->setUsrCreacion($objSession->get('user'));

                        $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                    
                        $emComercial->persist($objInfoDetalleSolCaractFibra);
                        $emComercial->flush();                
                    }
                    else
                    {
                        // Actualiza los valores de fibra en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibra->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibra->getUsrUltMod($objSession->get('user'));

                        $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                    
                        $emComercial->persist($objInfoDetalleSolCaractFibra);
                        $emComercial->flush(); 
                    }

                    if(!is_object($objInfoDetalleSolCaractFibraMetros)   && empty($objInfoDetalleSolCaractFibraMetros))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractFibraMetros  = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractFibraMetros->setCaracteristicaId($objCaracteristicaFibraMetros);
                        $objInfoDetalleSolCaractFibraMetros->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractFibraMetros->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractFibraMetros->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibraMetros->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    }
                    
                    if(!is_object($objInfoDetalleSolCaractOCivil)   && empty($objInfoDetalleSolCaractOCivil))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractOCivil = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOCivil->setCaracteristicaId($objCaracteristicaOCivil);
                        $objInfoDetalleSolCaractOCivil->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractOCivil->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractOCivil->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOCivil->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }


                    if(!is_object($objInfoDetalleSolCaractOtrosMat) && empty($objInfoDetalleSolCaractOtrosMat))
                    {
                        //Guardamos los valores de otros materiales en InfoDetalleSolCaract              
                        $objInfoDetalleSolCaractOtrosMat = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOtrosMat->setCaracteristicaId($objCaracteristicaOtrosMat);
                        $objInfoDetalleSolCaractOtrosMat->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractOtrosMat->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractOtrosMat->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->setUsrCreacion($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();
                        }
                        else
                        {
                            //Actualiza los valores de otros materiales en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOtrosMat->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->getUsrUltMod($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();

                    }

                        // ---- COPAGOS ----
                    if(!is_object($objInfoDetalleSolCaractCanceladoCliente) && empty($objInfoDetalleSolCaractCanceladoCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractCanceladoCliente->setCaracteristicaId($objCaracteristicaCanceladoCliente);
                        $objInfoDetalleSolCaractCanceladoCliente->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractCanceladoCliente->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractCanceladoCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->setUsrCreacion($objSession->get('user'));

                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->getUsrUltMod($objSession->get('user'));

                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }

                    if(!is_object($objInfoDetalleSolCaractAsumeCliente)     && empty($objInfoDetalleSolCaractAsumeCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeCliente->setCaracteristicaId($objCaracteristicaAsumeCliente);
                        $objInfoDetalleSolCaractAsumeCliente->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractAsumeCliente->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractAsumeCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->setUsrCreacion($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos y en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->getUsrUltMod($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }

                    if(!is_object($objInfoDetalleSolCaractAsumeEmpresa)     && empty($objInfoDetalleSolCaractAsumeEmpresa))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeEmpresa = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeEmpresa->setCaracteristicaId($objCaracteristicaAsumeEmpresa);
                        $objInfoDetalleSolCaractAsumeEmpresa->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractAsumeEmpresa->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->setUsrCreacion($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->getUsrUltMod($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
            }
            else
            {
                if(is_object($objDetalleSolicitudFactibilidad))
                {
                    
                $objInfoDetalleSolCaractFibra = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractFibraMetros = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibraMetros,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractOCivil      = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractOtrosMat    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMat,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractCanceladoCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                                'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                    ));
                $objInfoDetalleSolCaractAsumeCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                        'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                    ));
                $objInfoDetalleSolCaractAsumeEmpresa  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                            'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                    ));
            
                if(!is_object($objInfoDetalleSolCaractFibra)  && empty($objInfoDetalleSolCaractFibra))
                {
                    //Guardamos los valores de fibra en InfoDetalleSolCaract
                    $objInfoDetalleSolCaractFibra  = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractFibra->setCaracteristicaId($objCaracteristicaFibra);
                    $objInfoDetalleSolCaractFibra->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                    $objInfoDetalleSolCaractFibra->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                    $objInfoDetalleSolCaractFibra->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolCaractFibra->setUsrCreacion($objSession->get('user'));

                    $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                
                    $emComercial->persist($objInfoDetalleSolCaractFibra);
                    $emComercial->flush();                
                }
                else
                {
                    // Actualiza los valores de fibra en InfoDetalleSolCaract
                    $objInfoDetalleSolCaractFibra->setFeUltMod(new \DateTime('now'));
                    $objInfoDetalleSolCaractFibra->getUsrUltMod($objSession->get('user'));

                    $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                
                    $emComercial->persist($objInfoDetalleSolCaractFibra);
                    $emComercial->flush(); 
                }
                    if(!is_object($objInfoDetalleSolCaractFibraMetros) && empty($objInfoDetalleSolCaractFibraMetros))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractFibraMetros  = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractFibraMetros->setCaracteristicaId($objCaracteristicaFibraMetros);
                        $objInfoDetalleSolCaractFibraMetros->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractFibraMetros->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractFibraMetros->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibraMetros->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    } 
                    if(!is_object($objInfoDetalleSolCaractOCivil)   && empty($objInfoDetalleSolCaractOCivil))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractOCivil = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOCivil->setCaracteristicaId($objCaracteristicaOCivil);
                        $objInfoDetalleSolCaractOCivil->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractOCivil->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractOCivil->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOCivil->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }    
        
                    if(!is_object($objInfoDetalleSolCaractOtrosMat) && empty($objInfoDetalleSolCaractOtrosMat))
                    {
                        //Guardamos los valores de otros materiales en InfoDetalleSolCaract              
                        $objInfoDetalleSolCaractOtrosMat = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOtrosMat->setCaracteristicaId($objCaracteristicaOtrosMat);
                        $objInfoDetalleSolCaractOtrosMat->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractOtrosMat->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractOtrosMat->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->setUsrCreacion($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();
                        }
                        else
                        {
                            //Actualiza los valores de otros materiales en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOtrosMat->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->getUsrUltMod($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();
        
                    }
        
                        // ---- COPAGOS ----
                    if(!is_object($objInfoDetalleSolCaractCanceladoCliente) && empty($objInfoDetalleSolCaractCanceladoCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractCanceladoCliente->setCaracteristicaId($objCaracteristicaCanceladoCliente);
                        $objInfoDetalleSolCaractCanceladoCliente->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractCanceladoCliente->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractCanceladoCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->setUsrCreacion($objSession->get('user'));
        
                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->getUsrUltMod($objSession->get('user'));
        
                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }
        
                    if(!is_object($objInfoDetalleSolCaractAsumeCliente)     && empty($objInfoDetalleSolCaractAsumeCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeCliente->setCaracteristicaId($objCaracteristicaAsumeCliente);
                        $objInfoDetalleSolCaractAsumeCliente->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractAsumeCliente->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractAsumeCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->setUsrCreacion($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos y en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->getUsrUltMod($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }
                    if(!is_object($objInfoDetalleSolCaractAsumeEmpresa)     && empty($objInfoDetalleSolCaractAsumeEmpresa))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeEmpresa = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeEmpresa->setCaracteristicaId($objCaracteristicaAsumeEmpresa);
                        $objInfoDetalleSolCaractAsumeEmpresa->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractAsumeEmpresa->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->setUsrCreacion($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->getUsrUltMod($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
                    
                }
                
            }
            if($floatTotalPagar    ==  0)
            {
                throw new \Exception(': NO SE ENCONTRÓ <b>NIGÚN VALOR PARA ENVIAR O VALIDAR</b>');
            }

            if($boolModuloQueValida)
            {
                if (($strEvidencia) && ($floatCanceladoPorCliente == 0))
                {
                    throw new \Exception(': EL CLIENTE TIENE EVIDENCIAS, <br> <b>POR FAVOR INGRESE VALORES EN COPAGOS</b>');
                }

                if ((!$strEvidencia) && ($floatCanceladoPorCliente != 0))
                {
                    throw new \Exception(': EL CLIENTE NO TIENE EVIDENCIAS, <br> 
                                                        <b>POR FAVOR CARGUE ARCHIVO(S) SI NECESITA VALORES EN COPAGOS</b>');
                }
            }
            
            if($floatCanceladoPorCliente != 0)
            {
                $floatPorcentajeEmpresa            = 100 - $floatCanceladoPorCliente;
            }
            else
            {
                $floatPorcentajeEmpresa            = 0;
            }
            $strTablaDeValores = '<br/><br/>
                                <table width="100%" cellspacing="4" cellpadding="4">
                                    <tr> <td colspan="4"><b>*Valores de excedente de materiales*</b></td></tr>
                                    <tr> <td colspan="4"><b>Valores de Otros Clientes:</b></td></tr>
                                    <tr><td>Precio de Fibra            </td><td>$'.+$floatPrecioFibra.            '</td> </tr>
                                    <tr><td>Precio de Obra Civil       </td> <td>$'.+$floatPrecioObraCivil.       '</td> </tr>
                                    <tr><td>Precio de Otros Materiales </td> <td>$'.+$floatPrecioOtrosMate .      '</td> </tr>
                                    <tr><td>Subtotal de Otros Clientes </td> <td><b>$'.+$floatSubTotalOtrosClientes.'<b></td> </tr> 
                                    <tr> <td colspan="3"><b>Valores de COPAGOS:</b></td></tr>
                                   <tr><td>% Cliente                     </td> <td>'.+$floatCanceladoPorCliente .'%</td> </tr>
                                   <tr><td>% Empresa                     </td> <td>'.+$floatPorcentajeEmpresa          .'% </td> </tr>
                                    <tr><td>Cliente cancela         </td> <td>$'.+$floatAsumeCliente       .'</td>  </tr>
                                    <tr><td>Empresa cancela         </td> <td>$'.+$floatAsumeEmpresa       .'</td>  </tr>
                                    <tr> <td colspan="1"><b>TOTAL:</b></td><td><b>$'.+$floatTotalPagar     .'<b></td></tr>
                               </table> <br/>';
 
            //se arma la tabla con los datos a enviar
            
            $strTablaDeValoresEnviar = '<br/><br/>
                                <table width="100%" cellspacing="4" cellpadding="4">
                                    <tr> <td colspan="4"><b>*Valores de excedente de materiales*</b></td></tr>
                                    <tr> <td colspan="4"><b>Valores de Otros Clientes:</b></td></tr>
                                    <tr><td>Precio de Fibra            </td><td>$'.+$floatPrecioFibra.            '</td> </tr>
                                    <tr><td>Precio de Obra Civil       </td> <td>$'.+$floatPrecioObraCivil.       '</td> </tr>
                                    <tr><td>Precio de Otros Materiales </td> <td>$'.+$floatPrecioOtrosMate .      '</td> </tr>
                                    <tr><td>Subtotal de Otros Clientes </td> <td><b>$'.+$floatSubTotalOtrosClientes.'<b></td> </tr> 
                                    <tr> <td colspan="3"><b>Valores de COPAGOS:</b></td></tr>
                                   <tr><td>% Cliente                     </td> <td>'.+$floatCanceladoPorCliente .'%</td> </tr>
                                   <tr><td>% Empresa                     </td> <td>'.+$floatPorcentajeEmpresa          .'% </td> </tr>
                                    <tr><td>Cliente cancela         </td> <td>$'.+$floatAsumeCliente       .'</td>  </tr>
                                    <tr><td>Empresa cancela         </td> <td>$'.+$floatAsumeEmpresa       .'</td>  </tr>
                                    <tr> <td colspan="1"><b>TOTAL:</b></td><td><b>$'.+$floatTotalPagar     .'<b></td></tr>
                               </table> <br/>';                                               
            
                // --------- Pregunta si está habilitada la variable de validación y continua al proceso de validar - MODULO COMERCIAL --
                if ($boolModuloQueValida)
                {
                  // 1.- COMERCIAL: Excedente de metraje la OT en estado FACTIBLE, Detenida, Detenido, PrePlanificada, Replanificada
                  if (($strEstadoServicio  == 'Factible')      || ($strEstadoServicio  == 'Detenida') || ($strEstadoServicio  == 'Detenido')
                  || ($strEstadoServicio  == 'PrePlanificada') || ($strEstadoServicio  == 'Replanificada') )
                  {
                        /* Es decir que tiene cargado respaldos o evidencias del cliente (Correo ok cliente, PDF - FORMATO Facturación) */
                    if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                    {
                            //  COPAGOS.
                             $strAsunto         = "Notificación: se informa los valores del excedente de material | login: "
                                                .$objServicio->getPuntoId()->getLogin();
                       
                            $boolAplicativoValida = true;
                            $strObservacionEnviar = '<br/> <b>Servicio:</b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                                    .'- ' . $objServicio->getDescripcionPresentaFactura()
                                                    .'<br/> <b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. <br/>'
                                                    .$strTablaDeValoresEnviar;
            
                            $strSeguimiento = '<br/> <b>Servicio:</b>
                                            '. $objServicio->getProductoId()->getDescripcionProducto()
                                            . '<br/> <b>Observación:</b>  << '. $strObservacion.'>>.<br/>'.
                                            $strTablaDeValores;
                    }
                    //   EL CLIENTE NO AUTORIZA EL COBRO, no hay evidencias
                    else
                    {
                            $strAsunto            = "Notificación: se informa los valores del excedente de material | login: "
                                                    .$objServicio->getPuntoId()->getLogin();

                            $strObservacionEnviar = '<br/> <b>Servicio:</b> ' . $objServicio->getProductoId()->getDescripcionProducto()
                                                    . ' - ' . $objServicio->getDescripcionPresentaFactura()
                                                    . ' <br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>.'
                                                    .$strTablaDeValoresEnviar;
                        
                            $strSeguimiento       = '<br/> <b>Servicio: </b>
                                                    ' . $objServicio->getProductoId()->getDescripcionProducto()
                                                    . ' <br/><b>Observación:</b>  << '. $strObservacion.'>>.'
                                                    .$strTablaDeValores;

                            $boolAplicativoValida = true;                        
                    }
                        if($boolAplicativoValida)
                        {
                            $floatMCREnlaceActual = $objServicio->getPrecioVenta();
                            if (is_object($objParamValidacion1) && !empty($objParamValidacion1) &&
                                is_object($objParamValidacion2) && !empty($objParamValidacion2) )
                            {
                                //Variables de decisión para autorización automática.
                                $floatValidacionMRC  = floatval($objParamValidacion1->getValor2());
                                $intMinMRCxRUC       = intval($objParamValidacion2->getValor2());
                                $intSupera           = intval($objParamValidacion2->getValor4());
                            
                                /*Cada vez que se requiera un excedente de fibra, como también materiales adicionales y trabajos de 
                                *obras civiles, el sistema debe unificar estos valores y comparar si sobrepasa su MRC (del enlace 
                                * a instalar) en 2,5 veces. */
                                if ($floatSumaExcedente < ($floatMCREnlaceActual * $floatValidacionMRC)) 
                                {
                                    $boolAutorizado =  true;
                                    $strSeguimiento = '<b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 1: </b><br/>'
                                                    . '"El sistema unifica los valores de fibra y obra civil<br/>'
                                                        . 'luego compara si sobrepasa su MRC (del <br/>'
                                                        . 'enlace a instalar) en ' . $floatValidacionMRC . ' veces".
                                                        <br/>'.$strSeguimiento;

                                    $strMail        = '<b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 1: </b>:<br/>'
                                                    . '"El sistema unifica los valores de fibra y obra civil '
                                                    . 'luego compara si sobrepasa su MRC (del '
                                                    . 'enlace a instalar) en ' . $floatValidacionMRC . ' veces". ' ;                              
                                }
                                else
                                {
                                    $arrayParametrosFacturacion = array();
                                    $arrayParametrosFacturacion['intIdPersonaRol'] = $objServicio->getPuntoId()->getPersonaEmpresaRolId();
                                    $arrayParametrosFacturacion['arrayEstados']    = ['Activo'];
                                    $arrayParametrosFacturacion['strEsVenta']      = $objServicio->getEsVenta();
                                    $floatTotalFacturacion  = $emComercial
                                                            ->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->getTotalFacturacionTelcograph($arrayParametrosFacturacion);
                                    /*Si al cliente como RUC le facturamos más de $5K en MRC y si supera las 5 veces (costo de 
                                        * excedentes) el MRC del enlace entonces se autoriza la instalación y debe seguir 
                                        * por flujo normal.
                                        */
                                    if (($floatTotalFacturacion > $intMinMRCxRUC)
                                        && $floatSumaExcedente < ($floatMCREnlaceActual * $intSupera))
                                    {
                                        $boolAutorizado =  true;
                                        $strSeguimiento = '<br/><b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 2: </b><br/>'
                                                        . '"Si al cliente como RUC le facturamos más de <br/>
                                                          $' . $intMinMRCxRUC . ' en MRC y  si supera <br/>
                                                          las ' . $intSupera . ' veces (costo de excedentes)<br/>
                                                           el MRC del enlace entonces se autoriza la instalación".'
                                                        .'<br/>'.$strSeguimiento;

                                        $strMail       = '<br/><b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 2: </b><br/>'
                                                        . '"Si al cliente como RUC le facturamos más de 
                                                        $' . $intMinMRCxRUC . ' en MRC y si supera 
                                                        las ' . $intSupera . ' veces (costo de excedentes) 
                                                        el MRC del enlace entonces se autoriza la instalación".<br/>';
                                    }
                                    else
                                    {
                                        $boolAutorizado =  false;
                                        $strSeguimiento = '<br/><b>NO se autoriza por Validación 2 </b><br/>'
                                                        . '"Si al cliente como RUC le facturamos más de<br/>
                                                         $' . $intMinMRCxRUC . ' en MRC y si supera 
                                                         <br/> las ' . $intSupera . ' veces (costo de excedentes) 
                                                         <br/>el MRC del enlace entonces se autoriza la instalación".'
                                                        .'<br/> <br/>'.$strSeguimiento;

                                        $strMail       = '<br/><b>NO se autoriza por Validación 2:</b> <br/>'
                                                        . '"Si al cliente como RUC le facturamos más 
                                                         de $' . $intMinMRCxRUC . ' en MRC y si supera 
                                                         las ' . $intSupera . ' veces (costo de excedentes)
                                                         el MRC del enlace entonces se autoriza la instalación".'
                                                         ;                                   
                                    }
                                }                          
                            }
                        
                            if( ($floatPrecioFibra==0) )
                            {
                                $floatTotalPagar     = $floatSubTotalOtrosClientes;
                            }

                            if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                            {
                                $floatTotalPagar     = $floatAsumeCliente;
                            }
                           
                            //formatear a solo dos decimales
                            $floatTotalPagar = number_format($floatTotalPagar, 2,'.','');

                            $intCantidadEstimada    = 1;
                            $strCostoMaterial       = 0;
                            $intCantidadCliente     = 1;
                            $intCantidadUsada       = 0;
                            $intCantidadFacturada   = 1;
                            $strPrecioVentaMaterial = $floatTotalPagar;
                            $strValorCobrado        = $floatTotalPagar;
                            if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                            {
                                $floatAsumeEmpresa;
                            }
                            else
                            {
                                $floatAsumeEmpresa = $floatTotalPagar;
                            }
                            //Consecuencias si se autoriza automáticamente
                            if($boolAutorizado)
                            {
                                // Inicio de cuando Si existe una solicitud de excedente pendiente
                                if($objDetalleSolicitudExc)
                                {
                                    $boolExisteSolicitud = true;

                                    $strObservacionEnviar .= '<b>Revalidaci&oacute;n de solcitud!<b> <br/>. 
                                                            <br/>Solicitud de excedente de material '.' #'.$objDetalleSolicitudExc->getId()
                                                            .'<br/>';

                                    $strSeguimiento .= '<b>Revalidación de solcitud!. <b> <br/>'.
                                                        'Solicitud de excedente de material </b> '.' #'.$objDetalleSolicitudExc->getId();
                                    $strSeguimientoSol = '<b>Revalidación de solcitud!. <b> <br/>'.
                                                    'Solicitud de excedente de material</b> <br/>'.' #'.$objDetalleSolicitudExc->getId();

                                    if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                    {
                                        $strSeguimiento     .= '. <br/> Con valores copagos:
                                                            La empresa asume $'.$floatAsumeEmpresa.'<br/>';
                                    }

                                    /* GUARDAR historial a INFO DETALLE_SOLICITUD_HISTORIAL*/
                                    $strEstadoEnviado = $objDetalleSolicitudExc->getEstado();
                                    $arrayParametrosTraSol = array(
                                                                "emComercial"                => $emComercial,
                                                                "strClienteIp"               => $strClienteIp,
                                                                "objDetalleSolicitudExc"     => $objDetalleSolicitudExc,
                                                                "strObservacion"             => $strSeguimientoSol,
                                                                "strUsrCreacion"             => $strUsrCreacion,
                                                                "strEstadoEnviado"           => $strEstadoEnviado );
                                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                                    
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDeLaSolicitud
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }

                                }
                                /* FIN Validamos si existe solicitud de excedente de material pendiente*/
                                else
                                {
                                    // Solo si no tiene ninguna solicitud excedente, se crea una aprobada
                                    if(!is_object($objDetalleSolicitudExcTodas))
                                    {
                                        //CREO LA INFO DETALLE SOLICICITUD DE MATERIALES EXCEDENTES
                                        $strSeguimiento   .= 'Solicitud de excedente de material autorizada por aplicativo';
                                        $strSeguimientoSol = 'Solicitud de excedente de material autorizada por aplicativo';

                                        $strEstadoEnviado = "Aprobado";
                                        $strObservacionEnviar .= '<br/><b>Solicitud de excedente de material autorizada por aplicativo</b>,
                                                            en estado:'. $strEstadoEnviado.'<br/>';
                                        $arrayParametrosSolExc = array(
                                                                    "emComercial"                => $emComercial,
                                                                    "strClienteIp"               => $strClienteIp,
                                                                    "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                    "objServicio"                => $objServicio,
                                                                    "strSeguimiento"             => $strSeguimientoSol,
                                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                                    "strEstadoEnviado"           => $strEstadoEnviado);
                                        $arrayVerificar    = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                        $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                        if($arrayVerificar['status'] == 'ERROR' )
                                        {
                                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                        }
                                    }
                                    else
                                    {
                                        if($strEstadoSolicitudExcTodas =='Aprobado')
                                        {
                                            if($strValorCobrado==0)
                                            {
                                                throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                            }
                                            
                                            //ANULO LA SOLICITUD APROBADA DE MATERIALES EXCEDENTES
                                            $strEstadoEnviado          = "Anulada";
                                            $strSeguimiento            .= '<br/>Se anula solicitud #'
                                                                        .$intIdSolicitudExcedenteTodas.' porque se revalidan los datos';
                                            $strSeguimientoSol         = 'Se anula solicitud #'.$intIdSolicitudExcedenteTodas.' 
                                                                         porque se revalidan los datos';
                                            $arrayParametrosAnulSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "intIdSolicitudExcedente"    => $intIdSolicitudExcedenteTodas,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar = $serviceAutorizaciones
                                                                ->anulacionSolicitudDeExcedenteMateriales($arrayParametrosAnulSolExc);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: anulacionSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }
                                            $strObservacionEnviar    .= '<br/>Se anula solicitud #.'.$intIdSolicitudExcedenteTodas.'
                                                                         porque se revalidan los datos';

                                            //CREO LA INFO DETALLE SOLICICITUD DE MATERIALES EXCEDENTES Aprobado
                                            $strSeguimiento       .= '<br/>Solicitud nueva de excedente de material autorizada 
                                                                     por aplicativo';
                                            $strSeguimientoSol    = 'Solicitud nueva de excedente de material autorizada   por aplicativo';
                                            $strEstadoEnviado     = "Aprobado";
                                            $strObservacionEnviar .= '<br/><b>Solicitud de excedente de material autorizada por aplicativo</b>,
                                                                    en estado:'. $strEstadoEnviado.'<br/>';
                                            $arrayParametrosSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                        "objServicio"                => $objServicio,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar    = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                            $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }

                                            if(is_object($entityDetalleSolMaterial))
                                            {
                                                //ACTUALIZO LOS DATOS EN INFO DETALLE SOLICITUD MATERIAL
                                                $entityDetalleSolMaterial->setCostoMaterial($strCostoMaterial);
                                                $entityDetalleSolMaterial->setPrecioVentaMaterial($strPrecioVentaMaterial);
                                                $entityDetalleSolMaterial->setCantidadEstimada($intCantidadEstimada);
                                                $entityDetalleSolMaterial->setCantidadCliente($intCantidadCliente);
                                                $entityDetalleSolMaterial->setValorCobrado($strValorCobrado);
                                                $entityDetalleSolMaterial->setUsrCreacion($strUsrCreacion);
                                                $entityDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                                                $entityDetalleSolMaterial->setIpCreacion($strClienteIp);
                                                $entityDetalleSolMaterial->setCantidadUsada($intCantidadUsada);
                                                $entityDetalleSolMaterial->setCantidadFacturada($intCantidadFacturada);
                                                $emComercial->persist($entityDetalleSolMaterial);
                                                $emComercial->flush();
                                            }
                                        }
                                    }

                                    if((is_object($objDetalleSolicitudPla)) &&                        
                                    ($strEvidencia) && ($floatCanceladoPorCliente != 0) && ($objServicio->getEstado()!='Factible'))
                                    {
                                        if($strValorCobrado==0)
                                        {
                                            throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                        }

                                        
                                            //SI ES QUE NO HAY ENVÍO NUEVOS VALORES EN INFO DETALLE SOLICITUD MATERIAL
                                            $arrayParametrosSolMat  = array(
                                                                    "emComercial"                => $emComercial,
                                                                    "strClienteIp"               => $strClienteIp,
                                                                    "intIdDetalleSolicitud"      => $intIdInfoDetalleSolicitudPlan,
                                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                                    "strCodigoMaterial"          => $strCodigoMaterial,
                                                                    "strCostoMaterial"           => $strCostoMaterial,
                                                                    "strPrecioVentaMaterial"     => $strPrecioVentaMaterial,
                                                                    "intCantidadEstimada"        => $intCantidadEstimada,
                                                                    "intCantidadCliente"         => $intCantidadCliente,
                                                                    "intCantidadUsada"           => $intCantidadUsada,
                                                                    "intCantidadFacturada"       => $intCantidadFacturada,
                                                                    "strValorCobrado"            => $strValorCobrado);                    
                                            $arrayVerificar = $serviceAutorizaciones->registroSolicitudMaterial($arrayParametrosSolMat);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                                throw new \Exception(': NO SE REALIZÓ EL PROCESO: registroSolicitudMaterial  
                                                                        <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                            }
                                    }
                                }

                                if(($strEstadoServicio !== 'Anulado') )
                                {
                                    // Si el servico está en detenido lo preplanifica
                                    $boolPreplanificaAutomaticamente = true;
                                }
                                
                            }
                            /*  Consecuencias si NO se autoriza automáticamente */
                            else 
                            {
                                // Si existe solicitud en estado pendiente
                                if($objDetalleSolicitudExc)
                                {
                                    $boolExisteSolicitud = true;

                                    $strObservacionEnviar .= '<br/><b>Revalidaci&oacute;n de solcitud!<b> <br/>. 
                                                            <br/>Solicitud de excedente de material '.' #'.$objDetalleSolicitudExc->getId();

                                    $strSeguimiento  .= '<b>Revalidación de solcitud!. <b> <br/>'.
                                                        'Se validó la solicitud de excedente de material</b> <br/>'
                                                        .' #'.$objDetalleSolicitudExc->getId();

                                    if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                    {
                                        $strSeguimiento    .= '. <br/> Con valores copagos: 
                                                                La empresa asume $'.$floatAsumeEmpresa.'<br/>';
                                    }

                                    /* GUARDAR INFO DETALLE SOLICICITUD  HISTORIAL*/
                                    $strEstadoEnviado  = $objDetalleSolicitudExc->getEstado();
                                    $strSeguimientoSol = '<b>Revalidación de solcitud!. <b> <br/>'.
                                                    'Se validó la solicitud de excedente de material</b> <br/>'
                                                    .' #'.$objDetalleSolicitudExc->getId();
                                    $arrayParametrosTraSol = array(
                                                                "emComercial"                => $emComercial,
                                                                "strClienteIp"               => $strClienteIp,
                                                                "objDetalleSolicitudExc"     => $objDetalleSolicitudExc,
                                                                "strObservacion"             => $strSeguimientoSol,
                                                                "strUsrCreacion"             => $strUsrCreacion,
                                                                "strEstadoEnviado"           => $strEstadoEnviado );
                                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                                    
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDeLaSolicitud
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }

                                    /* FIN Validamos si existe solicitud de excedente de material pendiente*/                                
                                }
                                else
                                {
                                    // Preguntamos que no tenga ninguna solicitud excedente para poder crearla pendiente
                                    if(!is_object($objDetalleSolicitudExcTodas))
                                    {
                                        $strSeguimiento       .= ' <br/><b>Se crea solicitud de excedente de material a GTN</b>';
                                        
                                        //CREO LA INFO DETALLE SOLICITUD Y EL HISTORIAL DE SOL. MATERIALES EXCEDENTES
                                        $strEstadoEnviado = "Pendiente";
                                        $strSeguimientoSol = 'Aplicativo no autoriza el valor de $'.$floatAsumeEmpresa.',
                                                             <br/><b>Se crea solicitud de excedente de material a GTN</b>';
                                        $arrayParametrosSolExc = array(
                                                                    "emComercial"                => $emComercial,
                                                                    "strClienteIp"               => $strClienteIp,
                                                                    "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                    "objServicio"                => $objServicio,
                                                                    "strSeguimiento"             => $strSeguimientoSol,
                                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                                    "strEstadoEnviado"           => $strEstadoEnviado);
                                        $arrayVerificar    = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                        $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                        if($arrayVerificar['status'] == 'ERROR' )
                                        {
                                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                        }
                                        
                                        $strObservacionEnviar    .= '<br/>Se crea solicitud de excedente de material a GTN'.''.
                                                                    ', con # Solicitud: '.$intIdSolicitudGtn;

                                        if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                        {
                                            $strObservacionEnviar .= '. <br> Con valores copagos: La empresa asume $'.$floatAsumeEmpresa;
                                        }
                                    }
                                    else
                                    {
                                        if($strEstadoSolicitudExcTodas =='Aprobado')
                                        {
                                            //ANULO LA SOLICITUD APROBADA DE MATERIALES EXCEDENTES
                                            $strEstadoEnviado        = "Anulada";
                                            $strSeguimiento         .= 'Se anula solicitud #
                                                                    '.$intIdSolicitudExcedenteTodas.' porque se revalidan los datos';
                                            $strSeguimientoSol         = 'Se anula solicitud #.'.$intIdSolicitudExcedenteTodas.'
                                                                     porque se revalidan los datos';
                                            $arrayParametrosAnulSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "intIdSolicitudExcedente"    => $intIdSolicitudExcedenteTodas,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar = $serviceAutorizaciones
                                                                ->anulacionSolicitudDeExcedenteMateriales($arrayParametrosAnulSolExc);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: anulacionSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }
                                            $strObservacionEnviar    .= '<br/>Se anula solicitud #.'.$intIdSolicitudExcedenteTodas.'
                                                                        porque se revalidan los datos';
                                            
                                            
                                            $strSeguimiento     .= ' <br/><b>Se crea nueva solicitud 
                                                                de excedente de material a GTN</b>';
                                            
                                            //CREO LA INFO DETALLE SOLICITUD Y EL HISTORIAL DE SOL. MATERIALES EXCEDENTES
                                            $strEstadoEnviado  = "Pendiente";
                                            $strSeguimientoSol = 'Aplicativo no autoriza el valor de $'.$floatAsumeEmpresa.',
                                                                 <br/><b>Se crea nueva solicitud de excedente de material a GTN</b>';
                                            $arrayParametrosSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                        "objServicio"                => $objServicio,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar    = $serviceSolicitud
                                                                ->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                            $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }
                                            
                                            $strObservacionEnviar    .= '<br/><br/>Se crea solicitud de excedente de material a GTN'.''.
                                                                        ', con # Solicitud: '.$intIdSolicitudGtn;

                                            if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                            {
                                                $strObservacionEnviar .= '.<br/> Con valores copagos: La empresa asume $'.$floatAsumeEmpresa;
                                            }
                                        }  
                                        else
                                        {
                                            $strSeguimiento       .= ' <br/><b>Solicitud en estado '.$strEstadoSolicitudExcTodas.'</b>';
                                        }
                                    } // fin del if y else a cualquier solicitud de materiales excedentes
                                } // fin de pregunta si hay una solicitud de materiales excedentes en pendiente
                                
                                    
                                    /* Inicio de envío de Correo a GTN para notificar de la solicitud de Excedende de Material. */
                                    $arrayParametrosMail = array(
                                                                "login"        => $objServicio->getPuntoId()->getLogin(),
                                                                "producto"     => $objServicio->getProductoId()->getDescripcionProducto(),
                                                                "mensaje"      => $strObservacionEnviar
                                                                );
                                    $strAsunto  = "Se creó una Solicitud de Materiales Excedentes "
                                                . "| login: ".$objServicio->getPuntoId()->getLogin() ;    
                                                
                                    $arrayParametrosNotif = array(
                                                                "strAsunto"                      => $strAsunto,
                                                                "arrayParametrosMail"            => $arrayParametrosMail,
                                                                "arrayDestinatario"              => 'GTN',
                                                                "strCodEmpresa"                  => $strCodEmpresa,
                                                                "serviceEnvioPlantilla"          => $serviceEnvioPlantilla,
                                                                "arrayFormasContactoAsistente"   => '' ,
                                                                "arrayFormasContactoAsesor"      => '',
                                                                "arrayFormasContactoAGtn"       => $arrayFormasContactoAGtn);
                                    $arrayVerificar = $serviceSolicitud->envioDeNotificaciones($arrayParametrosNotif);
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }/* Fin de envío de Correo  */
                                    
                            } // fin si no autoriza
                        } //fin de si validar
                        
                        $strMail  .= $strObservacionEnviar;
                        $arrayParametrosMail = array(
                                                "login"      => $objServicio->getPuntoId()->getLogin(),
                                                "producto"   => $objServicio->getProductoId()->getDescripcionProducto(),
                                                "mensaje"    => $strMail );
                    
                        // GUARDAR INFO SERVICIO HISTORIAL - InfoServicioHistorial
                        $strEstadoEnviado = $objServicio->getEstado();
                        $arrayParametrosTraServ = array(
                                                    "emComercial"                => $emComercial,
                                                    "strClienteIp"               => $strClienteIp,
                                                    "objServicio"                => $objServicio,
                                                    "strSeguimiento"             => $strSeguimiento,
                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                    "strAccion"                  => $strAccion,
                                                    "strEstadoEnviado"           => $strEstadoEnviado );
                        $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                        if($arrayVerificar['status'] == 'ERROR' )
                        {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDelServicio
                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                        }

                        // Notificación por correo al área comercial respectiva.
                        $arrayParametrosNotif = array(
                                                    "strAsunto"                      => $strAsunto,
                                                    "arrayParametrosMail"            => $arrayParametrosMail,
                                                    "arrayDestinatario"              => 'Alias',
                                                    "strCodEmpresa"                  => $strCodEmpresa,
                                                    "serviceEnvioPlantilla"          => $serviceEnvioPlantilla,
                                                    "arrayFormasContactoAsistente"    => $arrayFormasContactoAsistente,
                                                    "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                                    "arrayFormasContactoAGtn"        => $arrayFormasContactoAGtn  );
                        $arrayVerificar = $serviceSolicitud->envioDeNotificaciones($arrayParametrosNotif);
                        if($arrayVerificar['status'] == 'ERROR' )
                        {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                        }

                        if($boolPreplanificaAutomaticamente)
                        {
                            $strEstadoEnviado = 'PrePlanificada';
                            $arrayParametrosPrePla = array(
                                                "emComercial"                => $emComercial,
                                                "strEstadoEnviado"           => $strEstadoEnviado,
                                                "objServicio"                => $objServicio,
                                                "strClienteIp"               => $strClienteIp,
                                                "strUsrCreacion"             => $strUsrCreacion);
                            $arrayVerificar = $serviceAutorizaciones->registroEstadoPrePlanificadaInfoDetalleSolicitud($arrayParametrosPrePla);
                            if($arrayVerificar['status'] == 'ERROR' )
                            {
                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroEstadoPrePlanificadaInfoDetalleSolicitud
                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                            }
                        }
                        
                        if(is_object($objDetalleSolicitudPla))
                        {
                            // GUARDAR SOLICITUD HISTORIAL - InfoDetalleSolHist
                            $arrayParametrosTraSol = array(
                                                        "emComercial"                => $emComercial,
                                                        "strClienteIp"               => $strClienteIp,
                                                        "objDetalleSolicitudExc"     => $objDetalleSolicitudPla,
                                                        "strObservacion"             => $strSeguimientoSol,
                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                        "strEstadoEnviado"           => $strEstadoEnviado );
                            $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                            
                            if($arrayVerificar['status'] == 'ERROR' )
                            {
                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDeLaSolicitud
                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                            }
                        }
                        $strRespuesta   = $strSeguimiento;
                       $emComercial->getConnection()->commit();
                  }
                  else
                  {
                    $strSeguimiento = '<b> Actualmente está en estado'. $strEstadoServicio.', </b> ,no se puede proceder';
                    $strRespuesta = $strSeguimiento; 
                  }
            }




            /* Entonces, es del módulo PYL y no valida, solo envia a comercial, envía notificaciones,registra historial, etc */
            else   
            {
               if( ($strEstadoInfoDetalleSolicitud == 'PrePlanificada') || ($strEstadoInfoDetalleSolicitud == 'Replanificada'))
               {
                //se valida que se requieren materiales adicionales u obras civiles
                if(($floatPrecioObraCivil!=0) || ($floatPrecioOtrosMate!=0)) 
                {
                    /*1.-	"Que se validen materiales adicionales u obras civiles de OTS y que el cliente AUTORICE EL COBRO */
                    if (($strEvidencia) &&  ($floatCanceladoPorCliente!=0))
                    {
                         $strSeguimiento = 'Envío a comercial los siguientes valores<br/> 
                                            que se requieren en materiales adicionales 
                                            <br/>u obras civiles. <br/>'.
                                            'El cliente autoriza el cobro<br/>'
                                            .'<br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                            .'<br/><b>Observación:</b>  << '. $strObservacion.'>>. <br/>'
                                            .$strTablaDeValores
                                            .'<br/> <br/><b>Nota: DETENER LA OT: </b>';

                        $strObservacionEnviar = 'Env&iacute;o a comercial los siguientes valores que se requieren en materiales adicionales
                                                u obras civiles. <br/>'.
                                              'El cliente autoriza el cobro<br/>'
                                            . ' <br/> <b>Servicio: </b> ' . $objServicio->getProductoId()->getDescripcionProducto()
                                            . ' <br/>- ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                            . '<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. '
                                            .$strTablaDeValoresEnviar;
                    }
                    else
                    {
                        $strSeguimiento = 'Envío a comercial los siguientes valores
                                            <br/> que se requieren en materiales adicionales u obras civiles<br/>'
                                            . ' <br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                            . '<br/><b>Observación:</b>  << '. $strObservacion.'>>.'
                                            .$strTablaDeValores
                                            .' <br/> <b>Nota: DETENER LA OT: </b>';

                        $strObservacionEnviar = 'Env&iacute;o a comercial los siguientes valores que se requieren en materiales adicionales
                                                u obras civiles.'.
                                                '<br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                                . ' - ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                                . '<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. '
                                                .$strTablaDeValoresEnviar;
                    }
                 }
                 else
                 {
                   // No hay materiales adicionales u obras civiles de OTS y que el cliente AUTORICE EL COBRO"
                   if (($strEvidencia) &&  ($floatCanceladoPorCliente!=0))
                   {
                        $strSeguimiento     = 'Envío a comercial los siguientes valores 
                                             <br/> El cliente autoriza el cobro  
                                             <br/> <b>Servicio: </b>:' 
                                            . $objServicio->getProductoId()->getDescripcionProducto()
                                            .$strTablaDeValores
                                            .'<br/><b>Observación:</b>  << '. $strObservacion.'>>. <br/>'
                                            .'<br/><b>Nota: DETENER LA OT: </b>';

                        $strObservacionEnviar = 'Env&iacute;o a comercial los valores que el cliente autoriza'
                                                . ' <br/> <b>Servicio: </b> ' . $objServicio->getProductoId()->getDescripcionProducto()
                                                . ' - ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                                .$strTablaDeValoresEnviar
                                                . '<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. ';
                   }
                   /* 2.-	Que se validen materiales adicionales u obras civiles de OTS y que el cliente 
                            NO AUTORICE EL COBRO, pero APLICATIVO APRUEBA */
                   else
                   {
                       $strSeguimiento      = 'Envío a comercial los siguientes valores 
                                            <br/> del servicio: ' . $objServicio->getProductoId()->getDescripcionProducto()
                                            .$strTablaDeValores
                                            .'<br/><b>Observación:</b>  << '. $strObservacion.'>>.'  
                                            .' <br/><b>Nota: DETENER LA OT: </b>';

                       $strObservacionEnviar = 'Env&iacute;o a comercial los siguientes valores 
                                                <br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                                .' - ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                                .'<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>.' 
                                                .$strTablaDeValoresEnviar;
                   }
                 }
                    $strAsunto = "Se envió a comercial los valores de otros clientes | login: "
                                                    . $objServicio->getPuntoId()->getLogin();

                    /* INI enviar notificacion*/ 
                    $arrayParametrosMail = array(
                                    "login"                 => $objServicio->getPuntoId()->getLogin(),
                                    "producto"              => $objServicio->getProductoId()->getDescripcionProducto(),
                                    "mensaje"               => $strObservacionEnviar
                                    );
                        
                    // Notificación por correo al alias pyl y al área comercial respectiva.

                    $arrayParametrosNotif = array(
                                    "strAsunto"                         => $strAsunto,
                                    "arrayParametrosMail"               => $arrayParametrosMail,
                                    "arrayDestinatario"                 => 'Alias',
                                    "strCodEmpresa"                     => $strCodEmpresa,
                                    "serviceEnvioPlantilla"             => $serviceEnvioPlantilla,
                                    "arrayFormasContactoAsistente"       => $arrayFormasContactoAsistente,
                                    "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                    "arrayFormasContactoAGtn"           => $arrayFormasContactoAGtn
                                 );
                    $arrayVerificar = $serviceSolicitud->envioDeNotificaciones($arrayParametrosNotif);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }
                        
                    //GUARDAR INFO SERVICIO HISTORIAL - InfoServicioHistorial,
                    $strEstadoEnviado = $objServicio->getEstado();
                    $arrayParametrosTraServ = array(
                                "emComercial"                => $emComercial,
                                "strClienteIp"               => $strClienteIp,
                                "objServicio"                => $objServicio,
                                "strSeguimiento"             => $strSeguimiento,
                                "strUsrCreacion"             => $strUsrCreacion,
                                "strAccion"                  => $strAccion,
                                "strEstadoEnviado"           => $strEstadoEnviado );
                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO:registroTrazabilidadDelServicio
                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }

                else
                {       
                    $strRespuesta = $strSeguimiento;         
                    $emComercial->getConnection()->commit();
                }
                }
                else
                {
                    $strSeguimiento = '<b/> Actualmente está en estado'. $strEstadoServicio.', <br/> no se puede proceder';
                    $strRespuesta = $strSeguimiento; 
                }
            }
         }
        }
        catch (\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $e->getMessage(), 
                                        $objSession->get('user'), 
                                        $strClienteIp
                                    );
       
            $arrayParametrosLog['enterpriseCode']   = "18";
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "validadorExcedenteMaterialAction";
            $arrayParametrosLog['appClass']         = "validadorExcedenteMaterialAction";
            $arrayParametrosLog['appMethod']        = "validadorExcedenteMaterial";
            $arrayParametrosLog['appAction']        = "noDefinido";
            $arrayParametrosLog['messageUser']      = "Ocurrió un error en el validador de excedentes";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = $strRespuesta;
            $arrayParametrosLog['creationUser']     = "TELCOS";
            $serviceUtil->insertLog($arrayParametrosLog);

            $objResponse = new Response(json_encode(array('mensaje' => 'ERROR'.$e->getMessage())));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;

        }
       
        $arrayParametrosLog['enterpriseCode']   = "18";
        $arrayParametrosLog['logType']          = "1";
        $arrayParametrosLog['logOrigin']        = "TELCOS";
        $arrayParametrosLog['application']      = "validadorExcedenteMaterialAction";
        $arrayParametrosLog['appClass']         = "validadorExcedenteMaterialAction";
        $arrayParametrosLog['appMethod']        = "validadorExcedenteMaterial";
        $arrayParametrosLog['appAction']        = "noDefinido";
        $arrayParametrosLog['messageUser']      = "Proceso completo en el validador de excedentes";
        $arrayParametrosLog['status']           = "Ok";
        $arrayParametrosLog['descriptionError'] = 'validador de excedentes';
        $arrayParametrosLog['inParameters']     = $strRespuesta;
        $arrayParametrosLog['creationUser']     = "TELCOS";
        $serviceUtil->insertLog($arrayParametrosLog);

        $objResponse = new Response(json_encode(array('mensaje' => $strRespuesta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }
    
    /**
     * Función que consulta las solicitudes simultáneas asociadas a una solicitud
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @return JsonResponse
     */
    public function getInfoSolsGestionSimultaneaAction()
    {
        $objJsonResponse                        = new JsonResponse();
        $objRequest                             = $this->getRequest();
        $serviceTecnico                         = $this->get('tecnico.InfoServicioTecnico');
        $intIdSolicitud                         = $objRequest->get('intIdSolicitud');
        $strOpcionGestionSimultanea             = $objRequest->get('strOpcionGestionSimultanea');
        $arrayRegistrosInfoGestionSimultanea    = array();
        try
        {
            $arrayRespuestaGetInfoSimultanea        = $serviceTecnico->getInfoGestionSimultanea(array(
                                                                                    "intIdSolicitud"                => $intIdSolicitud,
                                                                                    "strOpcionGestionSimultanea"    => $strOpcionGestionSimultanea,
                                                                                    "strTipoOpcionGestionSimultanea"=> "CONSULTA"));
            $arrayRegistrosInfoGestionSimultanea    = $arrayRespuestaGetInfoSimultanea["arrayRegistrosInfoGestionSimultanea"];
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }
        $strJsonRespuesta    = json_encode(array('intTotal'                             => count($arrayRegistrosInfoGestionSimultanea), 
                                                'arrayRegistrosInfoGestionSimultanea'   => $arrayRegistrosInfoGestionSimultanea));
        $objJsonResponse->setContent($strJsonRespuesta);
        return $objJsonResponse;
    }
    
    /**
     * Función para ejecutar las solicitudes simultáneas asociadas a una solicitud
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 03-01-2022 - Se anexan las variables de HAL y el idSugerencia para realizar
     *                           la planificacion simultanea de los productos EDB
     * 
     * @return JsonResponse
     */
    public function ejecutaSolsGestionSimultaneaAction()
    {
        $objJsonResponse                    = new JsonResponse();
        $objRequest                         = $this->getRequest();
        $objSession                         = $objRequest->getSession();
        $intIdPerSession                    = $objSession->get('idPersonaEmpresaRol');
        $strCodEmpresa                      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa                  = $objSession->get('prefijoEmpresa');
        $intIdDepartamentoSession           = $objSession->get('idDepartamento');
        $intIdEmpleadoSession               = $objSession->get('id_empleado');
        $strIpCreacion                      = $objRequest->getClientIp();
        $strUsrCreacion                     = $objSession->get('user');
        $intIdSolGestionada                 = $objRequest->get('intIdSolGestionada');
        $strOpcionGestionSimultanea         = $objRequest->get('strOpcionGestionSimultanea');
        $strMensajeEjecucionSolGestionada   = $objRequest->get('strMensajeEjecucionSolGestionada');
        $serviceGestionPyl                  = $this->get('planificacion.GestionPyl');
        $strMensaje                         = "";
        $boolMostrarMensajeErrorUsr         = false;
        try
        {
            if($strPrefijoEmpresa !== "MD")
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se ha ejecutado la gestión simultánea ya que no existe flujo para la empresa con prefijo "
                                     .$strPrefijoEmpresa);
            }
            
            if($strOpcionGestionSimultanea === "PLANIFICAR")
            {
                $strOrigen                          = $objRequest->get('strOrigen');
                $strParamResponsables               = $objRequest->get('strParamResponsables');
                $strFechaProgramacion               = $objRequest->get('strFechaProgramacion');
                $strFechaHoraInicioProgramacion     = $objRequest->get('strFechaHoraInicioProgramacion');
                $strFechaHoraFinProgramacion        = $objRequest->get('strFechaHoraFinProgramacion');
                $strAtenderAntes                    = $objRequest->get('atenderAntes');
                $strEsHal                           = $objRequest->get('esHal');
                $arrayParametros                    = array(
                                                            "intIdSolGestionada"                => $intIdSolGestionada,
                                                            "strOpcionGestionSimultanea"        => $strOpcionGestionSimultanea,
                                                            "strMensajeEjecucionSolGestionada"  => $strMensajeEjecucionSolGestionada,
                                                            "strOrigen"                         => $strOrigen,
                                                            "strParamResponsables"              => $strParamResponsables,
                                                            "strFechaProgramacion"              => $strFechaProgramacion,
                                                            "strFechaHoraInicioProgramacion"    => $strFechaHoraInicioProgramacion,
                                                            "strFechaHoraFinProgramacion"       => $strFechaHoraFinProgramacion,
                                                            "intIdPerSession"                   => $intIdPerSession,
                                                            "intIdDepartamentoSession"          => $intIdDepartamentoSession,
                                                            "strCodEmpresa"                     => $strCodEmpresa,
                                                            "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                            "strIpCreacion"                     => $strIpCreacion,
                                                            "strUsrCreacion"                    => $strUsrCreacion,
                                                            "strAtenderAntes"                   => $strAtenderAntes,
                                                            "strEsHal"                          => $strEsHal
                                                            );
                $arrayRespuestaPlanificacionSimultanea  = $serviceGestionPyl->programarPlanificacionSimultanea($arrayParametros);
                $strStatus                              = $arrayRespuestaPlanificacionSimultanea["status"];
                $strMensaje                             = $arrayRespuestaPlanificacionSimultanea["mensaje"];
            }
            else if($strOpcionGestionSimultanea === "REPLANIFICAR")
            {
                $strOrigen                                  = $objRequest->get('strOrigen');
                $intIdMotivo                                = $objRequest->get('intIdMotivo');
                $strBoolPerfilOpu                           = $objRequest->get('strBoolPerfilOpu');
                $strParamResponsables                       = $objRequest->get('strParamResponsables');
                $strFechaReplanificacion                    = $objRequest->get('strFechaReplanificacion');
                $strFechaHoraInicioReplanificacion          = $objRequest->get('strFechaHoraInicioReplanificacion');
                $strFechaHoraFinReplanificacion             = $objRequest->get('strFechaHoraFinReplanificacion');
                $intIdPerTecnico                            = $objRequest->get('intIdPerTecnico');
                $strAtenderAntes                            = $objRequest->get('atenderAntes');
                $strEsHal                                   = $objRequest->get('esHal');
                $arrayParametros                            = array(
                                                                    "intIdSolGestionada"                => $intIdSolGestionada,
                                                                    "strOpcionGestionSimultanea"        => $strOpcionGestionSimultanea,
                                                                    "strMensajeEjecucionSolGestionada"  => $strMensajeEjecucionSolGestionada,
                                                                    "strOrigen"                         => $strOrigen,
                                                                    "intIdMotivo"                       => $intIdMotivo,
                                                                    "strBoolPerfilOpu"                  => $strBoolPerfilOpu,
                                                                    "strParamResponsables"              => $strParamResponsables,
                                                                    "strFechaReplanificacion"           => $strFechaReplanificacion,
                                                                    "strFechaHoraInicioReplanificacion" => $strFechaHoraInicioReplanificacion,
                                                                    "strFechaHoraFinReplanificacion"    => $strFechaHoraFinReplanificacion,
                                                                    "intIdPerTecnico"                   => $intIdPerTecnico,
                                                                    "intIdPerSession"                   => $intIdPerSession,
                                                                    "objRequest"                        => $objRequest,
                                                                    "strCodEmpresa"                     => $strCodEmpresa,
                                                                    "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                                    "strIpCreacion"                     => $strIpCreacion,
                                                                    "strUsrCreacion"                    => $strUsrCreacion,
                                                                    "strAtenderAntes"                   => $strAtenderAntes,
                                                                    "strEsHal"                          => $strEsHal
                                                                );
                $arrayRespuestaReplanificacionSimultanea    = $serviceGestionPyl->reprogramarPlanificacionSimultanea($arrayParametros);
                $strStatus                                  = $arrayRespuestaReplanificacionSimultanea["status"];
                $strMensaje                                 = $arrayRespuestaReplanificacionSimultanea["mensaje"];
            }
            else if($strOpcionGestionSimultanea === "DETENER")
            {
                $intIdMotivo                        = $objRequest->get('intIdMotivo');
                $arrayParametros                    = array(
                                                                "intIdSolGestionada"                => $intIdSolGestionada,
                                                                "strOpcionGestionSimultanea"        => $strOpcionGestionSimultanea,
                                                                "intIdMotivo"                       => $intIdMotivo,
                                                                "strMensajeEjecucionSolGestionada"  => $strMensajeEjecucionSolGestionada,
                                                                "strCodEmpresa"                     => $strCodEmpresa,
                                                                "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                                "intIdDepartamentoSession"          => $intIdDepartamentoSession,
                                                                "intIdEmpleadoSession"              => $intIdEmpleadoSession,
                                                                "objRequest"                        => $objRequest,
                                                                "strIpCreacion"                     => $strIpCreacion,
                                                                "strUsrCreacion"                    => $strUsrCreacion
                                                            );
                $arrayRespuestaDetencionSimultanea  = $serviceGestionPyl->detenerPlanificacionSimultanea($arrayParametros);
                $strStatus                          = $arrayRespuestaDetencionSimultanea["status"];
                $strMensaje                         = $arrayRespuestaDetencionSimultanea["mensaje"];
            }
            else if($strOpcionGestionSimultanea === "RECHAZAR")
            {
                $intIdMotivo                        = $objRequest->get('intIdMotivo');
                $arrayParametros                    = array("intIdSolGestionada"                => $intIdSolGestionada,
                                                            "strOpcionGestionSimultanea"        => $strOpcionGestionSimultanea,
                                                            "intIdMotivo"                       => $intIdMotivo,
                                                            "strMensajeEjecucionSolGestionada"  => $strMensajeEjecucionSolGestionada,
                                                            "strCodEmpresa"                     => $strCodEmpresa,
                                                            "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                            "objRequest"                        => $objRequest,
                                                            "strIpCreacion"                     => $strIpCreacion,
                                                            "strUsrCreacion"                    => $strUsrCreacion);
                $arrayRespuestaRechazoSimultanea    = $serviceGestionPyl->rechazarPlanificacionSimultanea($arrayParametros);
                $strStatus                          = $arrayRespuestaRechazoSimultanea["status"];
                $strMensaje                         = $arrayRespuestaRechazoSimultanea["mensaje"];
            }
            else if($strOpcionGestionSimultanea === "ANULAR")
            {
                $intIdMotivo                        = $objRequest->get('intIdMotivo');
                $arrayParametros                    = array(
                                                            "intIdSolGestionada"                => $intIdSolGestionada,
                                                            "strOpcionGestionSimultanea"        => $strOpcionGestionSimultanea,
                                                            "intIdMotivo"                       => $intIdMotivo,
                                                            "strMensajeEjecucionSolGestionada"  => $strMensajeEjecucionSolGestionada,
                                                            "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                            "objRequest"                        => $objRequest
                                                      );
                $arrayRespuestaAnulacionSimultanea  = $serviceGestionPyl->anularPlanificacionSimultanea($arrayParametros);
                $strStatus                          = $arrayRespuestaAnulacionSimultanea["status"];
                $strMensaje                         = $arrayRespuestaAnulacionSimultanea["mensaje"];
            }
            else
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se ha ejecutado la gestión simultánea ya que no existe flujo para la opción ".$strOpcionGestionSimultanea);
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if($boolMostrarMensajeErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar la gestión simultánea por un error desconocido. Comuníquese con el Dep. de Sistemas!";
                error_log("Error en ejecutaSolsGestionSimultaneaAction ".$e->getMessage());
            }
            $strMensaje = $strMensajeEjecucionSolGestionada . "<br><b>Gestión simultánea</b><br>".$strMensaje;
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }

    /**
    * 
    * Construye la informacion para mostrar el listado de pendientes del departamento en sesión
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 01-11-2021
    * @since 1.0
    * @return JsonResponse
    */
   public function getListadoAsignadosSolInspeccionAction()
   {
       $objJsonRespuesta     = new JsonResponse();
       $objServiceUtil       = $this->get('schema.Util');
       $objSoporteService    = $this->get('soporte.SoporteService');
       $objSession           = $this->get('request')->getSession();
       $strIpCreacion        = $this->get('request')->getClientIp();
       $strCodEmpresa        = $objSession->get('idEmpresa');
       $intIdOficina         = $objSession->get('idOficina');
       $strUsr               = $objSession->get('user');
       $objEmComercial       = $this->getDoctrine()->getManager("telconet");
       $strUserDbComercial     = $this->container->getParameter('user_comercial');
       $strPasswordDbComercial = $this->container->getParameter('passwd_comercial');
       $strDatabaseDsn       = $this->container->getParameter('database_dsn');
       $objDateFechaHoy      = date("Y/m/d");
       $objRequest           = $this->get('request');
       $intIdSolicitud       = $objRequest->get('idSolicitud');
       try
       {

           $arrayParametrosOciCon['userComercial'] = $strUserDbComercial;
           $arrayParametrosOciCon['passComercial'] = $strPasswordDbComercial;
           $arrayParametrosOciCon['databaseDsn']   = $strDatabaseDsn;
           $arrayParametros['ociCon']              = $arrayParametrosOciCon;
           $arrayParametros['idSolicitud']         = $intIdSolicitud;
           $arrayParametros['start']               = 0;
           $arrayParametros['limit']               = 3000;

           $objJson = $this->getDoctrine()->getManager("telconet")
                           ->getRepository('schemaBundle:InfoDetalleSolPlanif')->generarJsonAsignadosSolInspeccion($arrayParametros);

            $objResultadoConsulta = json_decode($objJson);

            $intTotal = $objResultadoConsulta->total;
            $arrayResultadoConsulta = $objResultadoConsulta->encontrados;

            foreach($arrayResultadoConsulta as $arrayDato)
            {
                $arrayResultado[] = array(
                'idAsignado'=> $arrayDato->idAsignado,  "nombreAsignado"=>$arrayDato->nombreAsignado, "tipoAsignado"=>$arrayDato->tipoAsignado,
                "numeroTarea"=>$arrayDato->numeroTarea,"estadoTarea"=>$arrayDato->estadoTarea,"estado"=>$arrayDato->estado,
                "fechaInicio"=>$arrayDato->fechaInicio, "fechaFin"=>$arrayDato->fechaFin, "origen"=>$arrayDato->origen,
                'idSolicitud'=> $arrayDato->idSol,'idSolPlanif'=> $arrayDato->idSolPlanif,"observacion"=> $arrayDato->observacion);
            }
           $objJsonRespuesta->setData(['total' => $intTotal,'inspecciones' => $arrayResultado]);

       }
       catch(\Exception $objE)
       {
           $objServiceUtil->insertError($strUsr,'PlanificacionBundle.CoordinarController.getListadoCuadrillasSolInspeccionAction',
                                       'Error al consultar las asignaciones para solicitud de inspección. '.
                                       $objE->getMessage(),$strUsr,$strIpCreacion);
       }
       return $objJsonRespuesta;
   }


    /**
    * 
    * Construye la informacion para mostrar el historial de una solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 01-11-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function getHistorialAsignadosSolInspeccionAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $intIdOficina         = $objSession->get('idOficina');
        $strUsr               = $objSession->get('user');
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $strUserDbComercial     = $this->container->getParameter('user_comercial');
        $strPasswordDbComercial = $this->container->getParameter('passwd_comercial');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $objDateFechaHoy      = date("Y/m/d");
        $objRequest           = $this->get('request');
        $intIdSolPlanif       = $objRequest->get('idSolPlanif');
        try
        {
 
            $arrayParametrosOciCon['userComercial'] = $strUserDbComercial;
            $arrayParametrosOciCon['passComercial'] = $strPasswordDbComercial;
            $arrayParametrosOciCon['databaseDsn']   = $strDatabaseDsn;
            $arrayParametros['ociCon']              = $arrayParametrosOciCon;
            $arrayParametros['idSolPlanif']         = $intIdSolPlanif;
            $arrayParametros['start']               = 0;
            $arrayParametros['limit']               = 3000;
 
            $objJson = $this->getDoctrine()->getManager("telconet")
                            ->getRepository('schemaBundle:InfoDetalleSolPlanifHist')
                            ->generarJsonHistorialAsignadosSolInsp($arrayParametros);
 
             $objResultadoConsulta = json_decode($objJson);
 
             $intTotal = $objResultadoConsulta->total;
             $arrayResultadoConsulta = $objResultadoConsulta->encontrados;
 
             foreach($arrayResultadoConsulta as $arrayDato)
             {

                 $arrayResultado[] = array(
                 "idSolPlanifHist"=> $arrayDato->idSolPlanifHist,  
                 "nombreAsignado"=>$arrayDato->nombreAsignado, 
                 "observacion"=>$arrayDato->observacion,
                 "feCreacion"=>$arrayDato->feCreacion, 
                 "usrCreacion"=>$arrayDato->usrCreacion, 
                 "estado"=>$arrayDato->estado);
             }
            $objJsonRespuesta->setData(['total' => $intTotal,'historialInsp' => $arrayResultado]);
 
        }
        catch(\Exception $objE)
        {
            $objServiceUtil->insertError($strUsr,'PlanificacionBundle.CoordinarController.getHistorialAsignadosSolInspeccionAction',
                                        'Error al consultar el historial de la solicitud de inspección. '.
                                        $objE->getMessage(),$strUsr,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }

    /**
    * 
    * Obtiene la información de la solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 19-11-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function getInfoSolicitudInspeccionAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $intIdOficina         = $objSession->get('idOficina');
        $strUsr               = $objSession->get('user');
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $objDateFechaHoy      = date("Y/m/d");
        $objRequest           = $this->get('request');
        $intIdSolicitud       = $objRequest->get('idSolicitud');
        $strDescSolicitud     = $objRequest->get('descSolicitud');
        $strLogin             = $objRequest->get('login');
        try
        {

            $arrayParametros['intIdDetalleSolicitud']     = $intIdSolicitud;
            $arrayParametros['strDescripcionSolicitud']   = $strDescSolicitud;

            $arrayResultado = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolCaractInsp')
                                             ->getJSONSolicitudesPorDetSolCaracts($arrayParametros);

            $objJsonRespuesta->setData($arrayResultado);
 
        }
        catch(\Exception $objE)
        {
            $objServiceUtil->insertError($strUsr,'PlanificacionBundle.CoordinarController.getInfoSolicitudInspeccionAction',
                                        'Error al consultar información de solicitud de inspección. '.
                                        $objE->getMessage(),$strUsr,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }


    /**
    * 
    * Construye la informacion para mostrar el historial de una solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 01-11-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function getDocumentosSolInspeccionAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $objRequest           = $this->get('request');
        $intIdSolicitud       = $objRequest->get('idSolicitud');
        try
        {
            $arrayParametros['intIdDetalleSolicitud']        = $intIdSolicitud;
            $arrayParametros['strDescripcionCaracteristica'] = 'PRODUCTO_INSPECCION';
            $arrayParametros['strDescripcionSolicitud'] = 'SOLICITUD INSPECCION';
            $objJson = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolCaractInsp')
                                             ->getJSONSolicitudesPorDetSolCaracts($arrayParametros);
  


            $objResultadoConsulta = json_decode($objJson);
 
            $intTotal = $objResultadoConsulta->intTotal;
            $arrayResultadoConsulta = $objResultadoConsulta->arrayResultado;


            foreach($arrayResultadoConsulta as $arrayDato)
            {

                $arrayProductos = json_decode($arrayDato->valorDetSolCaract);

                foreach($arrayProductos as $objProducto)
                {
                    $arrayResultado[] = array(
                    "idSolCaracteristica"=> $arrayDato->idDetSolCaract,  
                    "descripcionCaracteristica"=> $arrayDato->descripcionCaract,  
                    "nombreDocumento"=>'Checklist - '.$objProducto->nombre,
                    "linkVerDocumento"=>$objProducto->checklist);
                }
            }
            $objJsonRespuesta->setData(['total' => $intTotal,'documentosInsp' => $arrayResultado]);
 
        }
        catch(\Exception $objE)
        {
            $objServiceUtil->insertError($strUsr,'PlanificacionBundle.CoordinarController.getDocumentosSolInspeccionAction',
                                        'Error al consultar caracteristicas de la solicitud de inspección. '.
                                        $objE->getMessage(),$strUsr,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }
 
    /**
    * Permite grabar los asignados de una solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 23-11-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function programarInspeccionesAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $intIdOficina         = $objSession->get('idOficina');
        $intIdDepartamento    = $objSession->get('idDepartamento');
        $strUsrCreacion       = $objSession->get('user');
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objDateFechaHoy      = date("Y/m/d");
        $objRequest           = $this->get('request');
        $strAsignados         = $objRequest->get('asignados');
        /* @var $serviceCoordinarService \telconet\planificacionBundle\Service\CoordinarService */
        $objCoordinarService = $this->get('planificacion.CoordinarInspeccion');

        try
        {
            //VALIDA SI TIENES PERMISOS PARA REALIZAR LA ACCION
            $arrayParametrosRp['accion']     = 'programarInspeccion';
            $arrayParametrosRp['buscaTodos'] = 'N';
            $arrayParametrosRp['codEmpresa'] = $strCodEmpresa;
            $arrayRolesPermitInsp            = $this->getRolesPermitidosInspeccion($arrayParametrosRp);
            if (count($arrayRolesPermitInsp) <= 0)
            {
                throw new \Exception("No tiene permisos para realizar esta acción");
            }

            $arrayParametrosProgramar['ipCreacion']           = $strIpCreacion;
            $arrayParametrosProgramar['codEmpresa']           = $strCodEmpresa;
            $arrayParametrosProgramar['idOficina']            = $intIdOficina;
            $arrayParametrosProgramar['idDepartamento']       = $intIdDepartamento;
            $arrayParametrosProgramar['usrCreacion']          = $strUsrCreacion;
            $arrayParametrosProgramar['asignados']            = $strAsignados;
            $arrayParametrosProgramar['intIdPerEmpRolSesion'] = $objSession->get('idPersonaEmpresaRol');
            $arrayParametrosProgramar['strPrefijoEmpresa']    = $objSession->get('prefijoEmpresa');
            $arrayRespuestaProgramarInsp = $objCoordinarService->programarInspeccion($arrayParametrosProgramar);

            if (strtoupper($arrayRespuestaProgramarInsp['status']) !== 'OK')
            {
                throw new \Exception($arrayRespuestaProgramarInsp['mensaje']);
            }

            $objJsonRespuesta->setData(array("status"  => "ok",
                                             "mensaje" => "Se realizo programación con éxito!"));
        }
        catch(\Exception $objE)
        {
            $strError = $objE->getMessage();
            if (strpos($strError,"permisos") <= 0)
            {
                $strError = "Ocurrio un error, no se pudo procesar la acción en la inspección!";
            }
            error_log($strError);
            $objServiceUtil->insertError($strUsrCreacion,'PlanificacionBundle.CoordinarController.programarInspeccionesAction()',
                                        'Error al grabar asignado de inspección: '.$objE->getMessage(),$strUsrCreacion,$strIpCreacion);
            $objJsonRespuesta->setData(array("status"  => "Error",
                                             "mensaje" => $strError));
        }
        return $objJsonRespuesta;
    }

    /**
    * Permite crear la replanificación de una solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 10-12-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function replanificarInspeccionAction()
    {
        $objJsonRespuesta      = new JsonResponse();
        $objServiceUtil        = $this->get('schema.Util');
        $objSession            = $this->get('request')->getSession();
        $strIpCreacion         = $this->get('request')->getClientIp();
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdOficina          = $objSession->get('idOficina');
        $intIdDepartamento     = $objSession->get('idDepartamento');
        $strUsrCreacion        = $objSession->get('user');
        $intIdPersonaEmpresaRol= $objSession->get('idPersonaEmpresaRol');
        $objEmComercial        = $this->getDoctrine()->getManager("telconet");
        $objEmSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objDateFechaHoy       = date("Y/m/d");
        $objRequest            = $this->get('request');
        //parametros recibidos
        $intIdDetalleSolicitud = $objRequest->get('idSol');
        $intIdDetalleSolPlanif = $objRequest->get('idSolPlanif');
        $intIdAsignado         = $objRequest->get('idAsignado');
        $strTipoAsignado       = $objRequest->get('tipoAsignado');
        $strFeIniPlan          = $objRequest->get('fechaInicio');
        $strFeFinPlan          = $objRequest->get('fechaFin');
        $strLogin              = $objRequest->get('login');
        $strObservacion        = $objRequest->get('obs');
        $intIdMotivo           = $objRequest->get('idMotivo');

        /* @var $serviceCoordinarService \telconet\planificacionBundle\Service\CoordinarService */
        $objCoordinarService     = $this->get('planificacion.CoordinarInspeccion');
        /* @var $serviceCoordinarService \telconet\planificacionBundle\Service\PlanificarService */
        $objPlanificacionService = $this->get('planificacion.Planificar');
        /* @var $serviceCoordinarService \telconet\soporteBundle\Service\SoporteService */
        $objSoporteService       = $this->get('soporte.SoporteService');

        $objFeIniPlan            = new \DateTime(date("Y/m/d H:i", strtotime($strFeIniPlan)));
        $objFeFinPlan            = new \DateTime(date("Y/m/d H:i", strtotime($strFeFinPlan)));

        $arrayTareaProgramacion = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->get(
                                                        'TAREA_PROGRAMAR_INSPECCION',
                                                        'COMERCIAL','','','','','','','', $strCodEmpresa,''
                                                    );

        foreach($arrayTareaProgramacion as $arrayTareaProg)
        {
            $intIdTarea = $arrayTareaProg['valor1'];
        }

        if (empty($intIdTarea))
        {
            throw new \Exception("Error al obtener tarea");
        }

        $arrayResponsable        = array();

        if (strtolower($strTipoAsignado) == 'empleado' )
        {

            $arrayParametrosRol = array();
            $arrayParametrosRol['intPersonaId'] = $intIdAsignado;
            $arrayParametrosRol['strCodigoEmpresa'] = $strCodEmpresa;

            $intInfoPersonaEmpresaRolid = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorIdPersonaYEmpresa($arrayParametrosRol);

            $objInfoPersonaEmpresaRol = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->find($intInfoPersonaEmpresaRolid);
                    
            $arrayResponsable = array(0 => $intIdTarea."@@".strtolower($strTipoAsignado)."@@".
                              $objInfoPersonaEmpresaRol->getPersonaId()->getId()."@@".$objInfoPersonaEmpresaRol->getId());
        }
        elseif (strtolower($strTipoAsignado) == 'cuadrilla' )
        {
            $arrayResponsable = array(0 => $intIdTarea."@@".strtolower($strTipoAsignado)."@@".$intIdAsignado."@@@@");
        }

        try
        {
            //VALIDA SI TIENES PERMISOS PARA REALIZAR LA ACCION
            $arrayParametrosRp['accion']     = 'replanificarInspeccion';
            $arrayParametrosRp['buscaTodos'] = 'N';
            $arrayParametrosRp['codEmpresa'] = $strCodEmpresa;
            $arrayRolesPermitInsp            = $this->getRolesPermitidosInspeccion($arrayParametrosRp);
            if (count($arrayRolesPermitInsp) <= 0)
            {
                throw new \Exception("No tiene permisos para realizar esta acción");
            }

            $arrayParametrosReplanificar['idDetalleSolicitud']  = $intIdDetalleSolicitud;
            $arrayParametrosReplanificar['idDetalleSolPlanif']  = $intIdDetalleSolPlanif;
            $arrayParametrosReplanificar['idAsignado']          = $intIdAsignado;
            $arrayParametrosReplanificar['tipoAsignado']        = $strTipoAsignado;
            $arrayParametrosReplanificar['objFechaIniPlan']     = $objFeIniPlan;
            $arrayParametrosReplanificar['objFechaFinPlan']     = $objFeFinPlan;
            $arrayParametrosReplanificar['strFechaIniPlan']     = $strFechaInicio;
            $arrayParametrosReplanificar['strFechaFinPlan']     = $strFechaFin;
            $arrayParametrosReplanificar['login']               = $strLogin;
            $arrayParametrosReplanificar['observacion']         = $strObservacion;
            $arrayParametrosReplanificar['usrCreacion']         = $strUsrCreacion;
            $arrayParametrosReplanificar['ipCreacion']          = $strIpCreacion;
            $arrayParametrosReplanificar['arrayResponsable']    = $arrayResponsable;
            $arrayParametrosReplanificar['codEmpresa']          = $strCodEmpresa;
            $arrayParametrosReplanificar['prefijoEmpresa']      = $strPrefijoEmpresa;
            $arrayParametrosReplanificar['idDepartamento']      = $intIdDepartamento;
            $arrayParametrosReplanificar["intIdMotivo"]         = $intIdMotivo;
            $arrayParametrosReplanificar['idTarea']             = $intIdTarea;
            $arrayParametrosReplanificar['idPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametrosReplanificar['objRequest']          = $objRequest;

            $arrayRespuestaReplanificarInsp = $objCoordinarService->replanificarInspeccion($arrayParametrosReplanificar);
            if (strtoupper($arrayRespuestaReplanificarInsp['status']) !== 'OK')
            {
                throw new \Exception($arrayRespuestaReplanificarInsp['mensaje']);
            }

            $objJsonRespuesta->setData(array("status"  => "ok",
                                             "mensaje" => "Se realizo replanificación con éxito!"));
        }
        catch(\Exception $objE)
        {
            $strError = $objE->getMessage();
            if (strpos($strError,"permisos") <= 0)
            {
                $strError = "Ocurrio un error, no se pudo procesar la acción en la inspección!";
            }
            $objServiceUtil->insertError($strUsrCreacion,'PlanificacionBundle.CoordinarController.replanificarInspeccionAction()',
                                        'Error al grabar replanificación de inspección: '.$objE->getMessage(),$strUsrCreacion,$strIpCreacion);
            $objJsonRespuesta->setData(array("status"  => "Error",
                                             "mensaje" => $strError));

        }
        return $objJsonRespuesta;
    }


    /**
    * Permite Detener,Anular o Rechazar una inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 05-01-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function gestionarInspeccionAction()
    {
        $objJsonRespuesta      = new JsonResponse();
        $objServiceUtil        = $this->get('schema.Util');
        $objSession            = $this->get('request')->getSession();
        $strIpCreacion         = $this->get('request')->getClientIp();
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdDepartamento     = $objSession->get('idDepartamento');
        $strUsrCreacion        = $objSession->get('user');
        $intIdEmpleado         = $objSession->get('id_empleado');
        $objRequest            = $this->get('request');
        //parametros recibidos
        $intIdDetalleSolicitud = $objRequest->get('idSol');
        $intIdDetalleSolPlanif = $objRequest->get('idSolPlanif');
        $strLogin              = $objRequest->get('login');
        $strObservacion        = $objRequest->get('obs');
        $intIdMotivo           = $objRequest->get('idMotivo');
        $strEstado             = $objRequest->get('estado');

        /* @var $serviceCoordinarService \telconet\planificacionBundle\Service\CoordinarService */
        $objCoordinarService     = $this->get('planificacion.CoordinarInspeccion');

        $arrayResponsable        = array();

        try
        {
            //VALIDA SI TIENES PERMISOS PARA REALIZAR LA ACCION
            $arrayParametrosRp['accion'] = '';
            $arrayParametrosRp['buscaTodos'] = 'N';
            $arrayParametrosRp['codEmpresa'] = $strCodEmpresa;
            if ($strEstado == "Detenido")
            {
                $arrayParametrosRp['accion'] = 'detenerInspeccion';
            }
            elseif ($strEstado == "Rechazada")
            {
                $arrayParametrosRp['accion'] = 'rechazarInspeccion';
            }
            elseif ($strEstado == "Anulada")
            {
                $arrayParametrosRp['accion'] = 'anularInspeccion';
            }
            $arrayRolesPermitInsp = $this->getRolesPermitidosInspeccion($arrayParametrosRp);
            if (count($arrayRolesPermitInsp) <= 0)
            {
                throw new \Exception("No tiene permisos para realizar esta acción");
            }
            $arrayParametrosDetener["estado"]                   = $strEstado;
            $arrayParametrosDetener["intIdMotivo"]              = $intIdMotivo;
            $arrayParametrosDetener["strObservacion"]           = $strObservacion;
            $arrayParametrosDetener["strCodEmpresa"]            = $strCodEmpresa;
            $arrayParametrosDetener["strPrefijoEmpresa"]        = $strPrefijoEmpresa;
            $arrayParametrosDetener["intIdDepartamentoSession"] = $intIdDepartamento;
            $arrayParametrosDetener["intIdEmpleadoSession"]     = $intIdEmpleado;
            $arrayParametrosDetener["objRequest"]               = $objRequest;
            $arrayParametrosDetener["strIpCreacion"]            = $strIpCreacion;
            $arrayParametrosDetener["strUsrCreacion"]           = $strUsrCreacion;
            $arrayParametrosDetener['idDetalleSolPlanif']       = $intIdDetalleSolPlanif;
            $arrayParametrosDetener["login"]                    = $strLogin;

            $arrayRespuestaReplanificarInsp = $objCoordinarService->gestionarInspeccion($arrayParametrosDetener);

            if (strtoupper($arrayRespuestaReplanificarInsp['status']) !== 'OK')
            {
                throw new \Exception($arrayRespuestaReplanificarInsp['mensaje']);
            }

            $objJsonRespuesta->setData(array("status"  => "ok",
                                             "mensaje" => "Se realizo la acción en la inspección con éxito!"));
        }
        catch(\Exception $objE)
        {
            $strError = $objE->getMessage();
            if (strpos($strError,"permisos") <= 0)
            {
                $strError = "Ocurrio un error, no se pudo procesar la acción en la inspección!";
            }

            $objServiceUtil->insertError($strUsrCreacion,'PlanificacionBundle.CoordinarController.gestionarInspeccionAction()',
                                        'Error al realizar esta acción en la inspección: '.$objE->getMessage(),$strUsrCreacion,$strIpCreacion);
            $objJsonRespuesta->setData(array("status"  => "Error","mensaje" => $strError));
        }
        return $objJsonRespuesta;
    }

    /**
    *
    * 
    * Documentación para el método 'getMotivosDetenerInspeccionAction'.
    * Permite obtener los motivos para Detener una solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 06-01-2021
    * @since 1.0
    * @return JsonResponse
    *
    */
    public function getMotivosGestionarInspeccionAction()
    {
        $objResponse    = new Response();
        $objRequest     = $this->get('request');
        $emGeneral      = $this->getDoctrine()->getManager('telconet');
        
        $arrayParametros    = array(
            "nombreModulo"  => $objRequest->get('mod'),
            "nombreAccion"  => $objRequest->get('acc'),
            "nombreMotivo"  => $objRequest->get('query'),
            "estados"       => array(
                "estadoActivo"    => "Activo",
                "estadoModificado"=> "Modificado"
            )
        );
        $objJson        = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->getJSONMotivosPorModuloYPorAccion( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse; 
    }

    /**
     * 
     * Función que consulta los roles de gestión de inspección que estan permitidos para el usuario en sesión
     * @param array arrayParametros [
     *                                  accion           => acción del rol que se desea consultar
     *                                  buscaTodos             => consulta si se busca todos o solo por la acción
     *                                  codEmpresa            => el código de la empresa
     *                                ]
     * 
     * @return array arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 08-02-2022
     * 
     */
    public function getRolesPermitidosInspeccion($arrayParametros)
    {
        $strAccion            = ( !empty($arrayParametros['accion']) )? $arrayParametros['accion'] : '';
        $strBuscarTodos       = $arrayParametros['buscaTodos'];
        $strCodEmpresa        = $arrayParametros['codEmpresa'];
        $arrayRolesPermitInsp = array();
        $objEmGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $objEmSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');

        //ROLES PERMITIDOS PARA ACCIONES DE SOLICITUD DE INSPECCION
        if ( (!empty($strAccion) && $strBuscarTodos == 'N') || 
             (empty($strAccion) && $strBuscarTodos == 'S' ))
        {
            $arrayRolesPermitidos = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->get(
                                                           'ROLES_PERMITIDOS_GESTIONAR_INSPECCION','PLANIFICACION',
                                                           '','','',$strAccion,'','','', $strCodEmpresa,''
                                                      );

            foreach($arrayRolesPermitidos as $arrayItem)
            {
                $objModulo = $objEmSeguridad->getRepository('schemaBundle:SistModulo')
                                            ->findOneByNombreModulo($arrayItem['valor1']);

                $objAccion = $objEmSeguridad->getRepository('schemaBundle:SistAccion')
                                            ->findOneByNombreAccion($arrayItem['valor2']);
                $strRole = '';
                $strNombreAccion = '';
                if (is_object($objModulo) && is_object($objAccion))
                {
                    $strRole = 'ROLE_'.$objModulo->getId().'-'.$objAccion->getId();
                    $strNombreAccion = $objAccion->getNombreAccion();
                }

                //Se consulta si el tiene permiso para realizar la acción
                if ($this->get('security.context')->isGranted($strRole))
                {
                    $arrayRolesPermitInsp[] = $strNombreAccion;
                }
            }
        }
        return $arrayRolesPermitInsp;
    }

 
    /**
    * Permite grabar los asignados de una solicitud de inspección
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 23-11-2021
    * @since 1.0
    * @return JsonResponse
    */
    public function rechazarSolicitudInspeccionAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strUsrCreacion       = $objSession->get('user');
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $objRequest           = $this->get('request');
        $intIdMotivo          = $objRequest->get('idMotivo');
        $strObservacion       = $objRequest->get('obs');
        $intIdSolicitud       = $objRequest->get('idSol');

        /* @var $serviceCoordinarService \telconet\planificacionBundle\Service\CoordinarService */
        $objGestionarService = $this->get('planificacion.GestionarInspeccion');

        try
        {
            //VALIDA SI TIENES PERMISOS PARA REALIZAR LA ACCION
            $arrayParametrosRp['accion']     = 'rechazarInspeccion';
            $arrayParametrosRp['buscaTodos'] = 'N';
            $arrayParametrosRp['codEmpresa'] = $strCodEmpresa;
            $arrayRolesPermitInsp            = $this->getRolesPermitidosInspeccion($arrayParametrosRp);
            if (count($arrayRolesPermitInsp) <= 0)
            {
                throw new \Exception("No tiene permisos para realizar esta acción");
            }

            $arrayParametros["strObservacion"]  = $strObservacion;
            $arrayParametros["strIpCreacion"]   = $strIpCreacion;
            $arrayParametros["strUsrCreacion"]  = $strUsrCreacion;
            $arrayParametros["intIdSolicitud"]  = $intIdSolicitud;
            $arrayParametros["intIdMotivo"]     = $intIdMotivo;
            $arrayParametros["strEmpresaCod"]  = $strCodEmpresa;
            $arrayRespuesta = $objGestionarService->rechazarSolicitudInspeccion($arrayParametros);

            if ($arrayRespuesta['status'] !== 200)
            {
                throw new \Exception($arrayRespuesta['mensaje']);
            }

            $objJsonRespuesta->setData(array("status"  => "ok",
                                             "mensaje" => "Se realizo rechazo de la solicitud de inspección con éxito!"));
        }
        catch(\Exception $objE)
        {
            $strError = $objE->getMessage();
            if (strpos($strError,"permisos") <= 0)
            {
                $strError = "Ocurrio un error, no se pudo procesar la acción en la solicitud de inspección!";
            }
            $objServiceUtil->insertError($strUsrCreacion,'PlanificacionBundle.CoordinarController.rechazarSolicitudInspeccionAction()',
                                        'Error al rechazar solicitud de inspección: '.$objE->getMessage(),$strUsrCreacion,$strIpCreacion);
            $objJsonRespuesta->setData(array("status"  => "Error",
                                             "mensaje" => $strError));
        }
        return $objJsonRespuesta;
    }

 }

