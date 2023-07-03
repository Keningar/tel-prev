<?php
 
namespace telconet\seguridadBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\HttpKernel;
use TelconetSSO\TelconetSSOBundle\Security\Authentication\Token\UserSSOToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use telconet\schemaBundle\Entity\SeguMenuPersona;
use telconet\adminBundle\Service\ActualizarPasswordService;
use telconet\schemaBundle\Entity\SeguBitacoraPersona;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
// use Symfony\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.0.x
/**
* Custom Request listener.
*/
class RequestListener extends Controller
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;
    /** @var \Doctrine\ORM\EntityManager */
    private $doctrine;
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;
    /** @var \telconet\adminBundle\Service\ActualizarPasswordService.php */
    private $serviceActualizarPassword;
   /**
    * Constructor
    *
    * @param SecurityContext $securityContext
    * @param Doctrine $doctrine
    * @param Doctrine $session
    */
    public function __construct(SecurityContext $securityContext,
                                Doctrine $doctrine, 
                                Session $session,
                                ActualizarPasswordService $serviceActualizarPassword)
    {
        $this->securityContext           = $securityContext;
        $this->doctrine                  = $doctrine;
        $this->session                   = $session;
        $this->serviceActualizarPassword = $serviceActualizarPassword;
    }
    
    /**
     * Documentación para el método 'onKernelRequest'.
     *
     * Crea el menú con los submenús a los cuales tiene acceso
     * el usuario logoneado, y adicional consulta la información
     * del usuario y la muestra en la barra inferior del portal.
     * 
     * Se agrega al menú el módulo de 'comunicaciones' con su
     * opción por defecto que sea 'documentos_imagenes'
     * 
     * @param GetResponseEvent $event
     *
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 03-08-2015
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 14-06-2016 - Se homologan las oficinas para TN dependiendo de la referencia id que tengan en la tabla INFO_OFICINA_GRUPO
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.3 29-09-2016 - Se valida el cambio de clave con claves expiradas
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.4 05-10-2016 - Cuando se carge la información del Usuario en sesión se creará un registro en la bitacora
     * 
     * @author Richard Cabrera Pereira <rcabrera@telconet.ec>
     * @version 1.5 11-10-2016 - Se crea una variable session 'numeroTareasAbiertas', en la cual se va almacenar la
     *                           cantidad de tareas abiertas que existen asignadas al usuario conectado
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 17-04-2017 Se realizan ajustes al momento de llamar a la función getNumeroTareasAbiertas, debido a que ahora se envian los 
     *                         parametros dentro de un array
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 30-05-2017 - Para TN se parametriza la validación de renderizar al listado de clientes cuando se ingresa al dashboard del módulo
     *                           comercial.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 27-06-2017 - Se añade a la sessión del usuario la información geográfica correspondiente
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 28-11-2017 Se calculan las tareas pendientes por departamento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 31-05-2018 - Se agrega credencial: indicadorTareasNacional, para que la informacion del indicador de tareas departamental,
     *                           sea a nivel nacional
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.1 04-12-2018 -  Se agrega la fecha por defecto para obtener un mejor tiempo de respuesta en los indicadores de tareas.
     *
     * $this->session->set("strLimiteLatitudNorte" , $strLimiteLatitudNorte);
                            $this->session->set("strLimiteLatitudSur"   , $strLimiteLatitudSur);
                            $this->session->set("strLimiteLongitudEste" , $strLimiteLongitudEste);
                            $this->session->set("strLimiteLongitudOeste", $strLimiteLongitudOeste);
                            $this->session->set("strRangoPais"          , $strRangoPais);
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 21-09-2018 - Se crean las variables de sesión strLimiteLatitudNorte, strLimiteLatitudSur, strLimiteLongitudEste,
     *                           strLimiteLongitudOeste para almacenar las coordenadas límites de los elementos dependiendo del país en sesión
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $arrayRespuesta = array();
        $intCantidadCasoMovil = 0;
        //Si es el Master Request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }
        
        $objRequest         = $event->getRequest();
        $strPrefijoEmpresa  = "";
        
        /* Web Services:
         * si se esta accediendo a la ruta base de los
         * web services /ws y /rs se debe saltar las validaciones
         * de seguridad, como se hace para AJAX
         */
        $strPath        = $objRequest->getPathInfo();
        if($strPath == "/logout")
        {
            return;
        }
        $arrayPartsPath = explode("/",$strPath);
        $strUri         = $arrayPartsPath[1];  

        if( !$objRequest->isXmlHttpRequest() && $strUri != "ws" && $strUri != "rs" )
        {
            
            $arrayModulos = array();
            $arrayModulos[] = "inicio";
            $arrayModulos[] = "comercial";
            $arrayModulos[] = "planificacion";
            $arrayModulos[] = "tecnico";
            $arrayModulos[] = "financiero";
            $arrayModulos[] = "soporte";
            $arrayModulos[] = "administracion";
            $arrayModulos[] = "comunicaciones";
        
            $objUser = null;
            
            if( $this->securityContext->getToken() )
            {
                $objUser = $this->securityContext->getToken()->getUser();
            }
	
            if( !$objUser )
            {
                $objUser = $objRequest->getSession()->get('user_sso');
            }

            if( $objUser )
            {
		$username = $objUser->getUsername();
		
                $boolRequiereCambioPass =  $this->session->get('requiereCambioPass');
                $objEmpleado = $this->doctrine->getManager()->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($objUser->getUsername());
                
                if( $objEmpleado )
                {
                    $intIdEmpresa = $this->session->get('idEmpresa');
                    
                    if( !isset($intIdEmpresa) )
                    {
			//se imprime en log ip del usuario
    			$xffIp    = $_SERVER['HTTP_X_FORWARDED_FOR'];
			error_log("USUARIO:".$username." IP:".$xffIp);

                        //guardo empresas del usuario
                        $arrayEmpresas = $this->doctrine->getManager()
                                              ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->getEmpresasByPersona($objUser->getUsername(), "Empleado");
                        
                        $arrayEmpresasExternos = $this->doctrine->getManager()
                                                      ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                      ->getEmpresasByPersona($objUser->getUsername(), "Personal Externo");

                        $arrayEmpresas = array_merge($arrayEmpresas, $arrayEmpresasExternos);

                        $this->session->set('arrayEmpresas', $arrayEmpresas);
	      
                        if( $arrayEmpresas && count($arrayEmpresas) > 0 )
                        {
                            //Se calcula el numero de tareas abiertas que tiene asignadas el usuario en session
                            $arrayParametros["intPersonaEmpresaRolId"] = $arrayEmpresas[0]["IdPersonaEmpresaRol"];
                            $arrayParametros["strTipoConsulta"]        = "CantidadTareasAbiertas";
                            $arrayParametros["arrayEstados"]           = array('Cancelada','Rechazada','Finalizada','Anulada');
                            $arrayParametros["intPersonaEmpresaRol"]   = $arrayEmpresas[0]["IdPersonaEmpresaRol"];
                            $arrayParametros["strTipoConsulta"]        = "persona";

                            $arrayFechaDefecto = $this->doctrine->getManager()->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('TAREAS_FECHA_DEFECTO','SOPORTE','','','','','','','','');

                            if (!empty($arrayFechaDefecto) && count($arrayFechaDefecto) > 0 &&
                                checkdate($arrayFechaDefecto['valor2'],$arrayFechaDefecto['valor3'],$arrayFechaDefecto['valor1']))
                            {
                                $strFechaDefecto = $arrayFechaDefecto['valor1'].'-'. //Año
                                                   $arrayFechaDefecto['valor2'].'-'. //Mes
                                                   $arrayFechaDefecto['valor3'];     //Día

                                $arrayParametros['strFechaDefecto'] = $strFechaDefecto;
                            }

                            $arrayRespuesta = $this->doctrine->getManager()->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                           ->getDetalleTareas($arrayParametros);

                            $intTareasAbiertasPersona = $arrayRespuesta["intCantidadTareas"];
                            //Se calcula el numero de tareas abiertas por departamento
                            $arrayParametros["intIdDepartamento"] = $arrayEmpresas[0]["IdDepartamento"];
                            $objInfoPersonaEmpresaRol = $this->doctrine->getManager()
                                                                       ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                       ->find($arrayEmpresas[0]["IdPersonaEmpresaRol"]);

                            if(is_object($objInfoPersonaEmpresaRol))
                            {
                                $arrayParametros["intOficinaId"] = $objInfoPersonaEmpresaRol->getOficinaId()->getId();
                            }
                            $arrayParametros["strEstado"]         = "Activo";
                            $arrayParametros["intDepartamentoId"] = $arrayEmpresas[0]["IdDepartamento"];
                            $arrayParametros["strTipoConsulta"]   = "departamento";

                            //Se consulta si la persona en session tiene la credencial: indicadorTareasNacional
                            $arrayParametrosPerfil["intIdPersonaRol"] = $arrayEmpresas[0]["IdPersonaEmpresaRol"];
                            $arrayParametrosPerfil["strNombrePerfil"] = "indicadorTareasNacional";

                            $strTienePerfil = $this->doctrine->getRepository('schemaBundle:SeguRelacionSistema')
                                                             ->getPerfilPorPersona($arrayParametrosPerfil);

                            $arrayParametros["strTieneCredencial"] = $strTienePerfil;

                            $arrayTareasPendientes = $this->doctrine->getManager()->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                                  ->getDetalleTareas($arrayParametros);

                            $intCantidadCasoMovil  = $this->doctrine->getManager()
                                                                    ->getRepository('schemaBundle:InfoCaso')
                                                                    ->getCantidadCasoMovil();

                            $this->session->set('numeroTareasAbiertas', $intTareasAbiertasPersona);
                            $this->session->set('numeroTareasAbiertasDepartamento' , $arrayTareasPendientes["intCantidadTareas"]);
                            $this->session->set('numeroTareasAbiertasMovil'        , $intCantidadCasoMovil);
                            $this->session->set("strBanderaTareasDepartamento","N");
                            $this->session->set('idPersonaEmpresaRol'  , $arrayEmpresas[0]["IdPersonaEmpresaRol"]);
                            $this->session->set('idEmpresa'            , $arrayEmpresas[0]["CodEmpresa"]);
                            $this->session->set('prefijoEmpresa'       , $arrayEmpresas[0]["prefijo"]);
                            $this->session->set('empresa'              , $arrayEmpresas[0]["razonSocial"]);
                            $this->session->set('idOficina'            , $arrayEmpresas[0]["IdOficina"]);
                            $this->session->set('oficina'              , $arrayEmpresas[0]["nombreOficina"]);
                            $this->session->set('idDepartamento'       , $arrayEmpresas[0]["IdDepartamento"]);
                            $this->session->set('departamento'         , $arrayEmpresas[0]["nombreDepartamento"]);
                            $this->session->set('intIdPais'            , $arrayEmpresas[0]["idPais"]);
                            $this->session->set('strNombrePais'        , $arrayEmpresas[0]["nombrePais"]);
                            $this->session->set('intIdRegion'          , $arrayEmpresas[0]["idRegion"]);
                            $this->session->set('strNombreRegion'      , $arrayEmpresas[0]["nombreRegion"]);
                            $this->session->set('intIdCanton'          , $arrayEmpresas[0]["idCanton"]);
                            $this->session->set('strNombreCanton'      , $arrayEmpresas[0]["nombreCanton"]);
                            $this->session->set('intIdProvincia'       , $arrayEmpresas[0]["idProvincia"]);
                            $this->session->set('strNombreProvincia'   , $arrayEmpresas[0]["nombreProvincia"]);
                            $this->session->set('strFacturaElectronico', $arrayEmpresas[0]["facturaElectronico"]);
                            $this->session->set('strNombreEmpresa'     , $arrayEmpresas[0]["nombreEmpresa"]);
                            $arrayLimitesCoordenadas    = $this->doctrine->getManager('telconet_general')
                                                                         ->getRepository('schemaBundle:AdmiParametroDet')
                                                                         ->getOne(  'LIMITES_COORDENADAS_ELEMENTO', 
                                                                                    '', 
                                                                                    '', 
                                                                                    $arrayEmpresas[0]["nombrePais"], 
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '');
                            if(empty($arrayLimitesCoordenadas))
                            {
                                $strLimiteLatitudNorte  = "";
                                $strLimiteLatitudSur    = "";
                                $strLimiteLongitudEste  = "";
                                $strLimiteLongitudOeste = "";
                                $strRangoPais           = "";
                            }
                            else
                            {
                                $strLimiteLatitudNorte  = $arrayLimitesCoordenadas["valor1"];
                                $strLimiteLatitudSur    = $arrayLimitesCoordenadas["valor2"];
                                $strLimiteLongitudEste  = $arrayLimitesCoordenadas["valor3"];
                                $strLimiteLongitudOeste = $arrayLimitesCoordenadas["valor4"];
                                $strRangoPais           = $arrayLimitesCoordenadas["valor5"];
                            }
                            $this->session->set("strLimiteLatitudNorte" , $strLimiteLatitudNorte);
                            $this->session->set("strLimiteLatitudSur"   , $strLimiteLatitudSur);
                            $this->session->set("strLimiteLongitudEste" , $strLimiteLongitudEste);
                            $this->session->set("strLimiteLongitudOeste", $strLimiteLongitudOeste);
                            $this->session->set("strRangoPais"          , $strRangoPais);
                        }
                    }
                    
                    $strPrefijoEmpresa = $this->session->get('prefijoEmpresa') ? $this->session->get('prefijoEmpresa') : "";

                    if( $strPrefijoEmpresa == "TN")
                    {
                        $intIdOficinaSession = $this->session->get('idOficina') ? $this->session->get('idOficina') : 0;
                        
                        if( $intIdOficinaSession )
                        {
                            $objInfoOficinaGrupo = $this->doctrine->getManager()->getRepository('schemaBundle:InfoOficinaGrupo')
                                                        ->findOneById($intIdOficinaSession);
                            
                            if( $objInfoOficinaGrupo )
                            {
                                $intIdRefOficinaPadre = $objInfoOficinaGrupo->getRefOficinaId() ? trim($objInfoOficinaGrupo->getRefOficinaId()) : 0;
                                
                                if( $intIdRefOficinaPadre )
                                {
                                    $objInfoOficinaGrupoPadre = $this->doctrine->getManager()->getRepository('schemaBundle:InfoOficinaGrupo')
                                                                     ->findOneById($intIdRefOficinaPadre);
                                    
                                    if( $objInfoOficinaGrupoPadre )
                                    {
                                        $this->session->set('idOficina' , $objInfoOficinaGrupoPadre->getId());
                                        $this->session->set('oficina'   , $objInfoOficinaGrupoPadre->getNombreOficina());
                                        
                                        $arrayEmpresas = $this->session->get('arrayEmpresas');
                                        
                                        if( $arrayEmpresas )
                                        {
                                            $arrayEmpresasNuevas = array();
                                            
                                            foreach( $arrayEmpresas as $arrayEmpresa )
                                            {
                                                if( $arrayEmpresa["prefijo"] == "TN" )
                                                {
                                                     $arrayEmpresa["IdOficina"]     = $objInfoOficinaGrupoPadre->getId();
                                                     $arrayEmpresa["nombreOficina"] = $objInfoOficinaGrupoPadre->getNombreOficina();
                                                }//( $arrayEmpresa["prefijo"] == "TN" )
                                                
                                                $arrayEmpresasNuevas[] = $arrayEmpresa;
                                                
                                            }//foreach( $arrayEmpresas as $arrayEmpresa )
                                            
                                            $this->session->set('arrayEmpresas', $arrayEmpresasNuevas);
                                        }//( $arrayEmpresas )
                                    }//( $objInfoOficinaGrupoPadre )
                                }//( $intIdRefOficinaPadre )
                            }//( $objInfoOficinaGrupo )
                        }//( $intIdOficinaSession )
                    }//( $strPrefijoEmpresa == "TN")
                    
	    
                    $intIdEmpleado = $this->session->get('id_empleado');
            
                    if( !isset($intIdEmpleado) )
                    {
                        $this->session->set('empleado'    , $objEmpleado->getNombres().' '.$objEmpleado->getApellidos() );
                        $this->session->set('id_empleado' , $objEmpleado->getId() );
                        $this->session->set('user'        , $objEmpleado->getLogin() );
                        
                        $objSeguRelacionSistema = $this->doctrine->getManager()->getRepository('schemaBundle:SeguRelacionSistema')
                                                                 ->getRelacionSistemaByModuloAndAccion('Telcos+','sesion');
                        if($objSeguRelacionSistema)
                        {
                            $this->guardarActividadLogin(array(
                                                                'intIdPersona'         => $objEmpleado->getId(),
                                                                'intIdRelacionSistema' => $objSeguRelacionSistema->getId(),
                                                                'strBitacoraDetalle'   => 'inició',
                                                                'strIpCreacion'        => $objRequest->getClientIp()
                            ));
                        }
                    }
                    
                    //verifica si requiere cambio de Password
                    if( !isset($boolRequiereCambioPass) )
                    {
                        $boolRequiereCambioPass = $this->serviceActualizarPassword
                                                       ->requiereCambioPassword(array('strLogin' => $objUser->getUsername()));
                        $this->session->set('requiereCambioPass', $boolRequiereCambioPass );
                    }
                    
                    //guardo roles del usuario
                    $arrayRoles = $this->doctrine->getManager()
                                       ->getRepository('schemaBundle:SeguAsignacion')
                                       ->getRolesXEmpleado($objEmpleado->getId());
	
                    foreach( $arrayRoles as $arrayRol )
                    {
                        $strNombreRol = 'ROLE_'.$arrayRol["modulo_id"].'-'.$arrayRol["accion_id"];
                        $objUser->addRol($strNombreRol);
                    }
			    
                    $arrayVariables = array( "id_persona" => $this->session->get('id_empleado') );
                    
                    //actualizo el usuario con sus roles
                    $authenticatedToken = new UserSSOToken($objUser, $objUser->getRoles());

                    $this->securityContext->setToken($authenticatedToken);
                    $this->securityContext->getToken()->setUser($objUser);
                    $this->securityContext->getToken()->setAuthenticated(true);
	    
                    $arrayRolesPermitidos = $this->securityContext->getToken()->getUser()->getRoles();
                    sort($arrayRolesPermitidos);
                    $arrayRolesPermitidos = array_unique($arrayRolesPermitidos);
                    $this->session->set('rolesPermitidos', $arrayRolesPermitidos);	
	      
                    if($arrayPartsPath[1]!="")
                    {
                        $strModulo = $arrayPartsPath[1];
                        
                        if(isset($arrayPartsPath[2]))
                        {
                            $strOpcionMenu = $arrayPartsPath[2];
                        }
                        else
                        {
                            $strOpcionMenu = "dashboard";
                        }
                    }
                    else
                    {
                        $strModulo     = "inicio";
                        $strOpcionMenu = "dashboard";
                    }

                    $boolRedireccionarDashboard = true;

                    //SE VERIFICA SI EL DASHBOARD SE DEBE HABILITAR O SE DEBE REDIRECCIONAR A OTRA OPCION
                    $arrayParametroHabilitarDashboard = $this->doctrine->getManager('telconet_general')
                                                             ->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('HABILITAR_DASHBOARD', 
                                                                      'GENERAL', 
                                                                      'RENDERIZACION_PANTALLA',
                                                                      '', 
                                                                      strtoupper($strModulo),
                                                                      strtoupper($strOpcionMenu),
                                                                      '',
                                                                      '', 
                                                                      '', 
                                                                      $intIdEmpresa);

                    if( isset($arrayParametroHabilitarDashboard['id']) && !empty($arrayParametroHabilitarDashboard['id']) )
                    {
                        if( strtoupper($strModulo) == "COMERCIAL" )
                        {
                            if( in_array("ROLE_387-1", $arrayRolesPermitidos) )
                            {
                                $boolRedireccionarDashboard = false;
                            }//( in_array("ROLE_387-1", $arrayRolesPermitidos) )
                        }//( strtoupper($strModulo) == "COMERCIAL" )
                        else
                        {
                            $boolRedireccionarDashboard = false;
                        }
                    }//( isset($arrayParametroHabilitarDashboard['id']) && !empty($arrayParametroHabilitarDashboard['id']) )

                    if( $boolRedireccionarDashboard )
                    {
                        switch(strtolower($strModulo).'_'.$strOpcionMenu)
                        {
                            case 'comercial_dashboard':
                                $strOpcionMenu = "cliente";
                                break;

                            case 'financiero_dashboard':
                                $strOpcionMenu = "documentos";
                                break;

                            case 'administracion_dashboard':
                                $strOpcionMenu = "menu";
                                break;

                            case 'tecnico_dashboard':
                                $strOpcionMenu = "clientes";
                                break;

                            case 'comunicaciones_dashboard':
                                $strOpcionMenu = "documentos_imagenes";
                                break;
                        }
                    }//( $boolRedireccionarDashboard )
                    
                    $session = $objRequest->getSession();
                    
                    $em = $this->doctrine->getManager();
                    
                    if( in_array(strtolower($strModulo),$arrayModulos) )
                    {  
                        $objMenuPersona = $this->doctrine->getManager('telconet_seguridad')
                                               ->getRepository('schemaBundle:SeguMenuPersona')
                                               ->findByPersonaId($arrayVariables["id_persona"]);
                        
                        if($objMenuPersona && count($objMenuPersona)>0)
                        {
                            $arrayMenuPersona   = explode("@@@@", $objMenuPersona[0]->getMenuHtml());
                            $strHtmlModulos     = stripslashes($arrayMenuPersona[0]);
                            $strHtmlSubmodulos  = stripslashes($arrayMenuPersona[1]);
                        }
                        else
                        {
                            //BUSCAR EL MENU -- YA NO CREAR EL MODULO...
                            $arregloModulos     = $this->retornaModulos($objRequest,$arrayVariables);
                            $strHtmlModulos     = $arregloModulos["modulos"];
                            $strHtmlSubmodulos  = $arregloModulos["submodulos"];
			
                            //GRABAR EL MENU PERSONA
                            if($arregloModulos["modulos"] && $arregloModulos["submodulos"])
                            {
                                $emSeguridad = $this->doctrine->getManager('telconet_seguridad');
                                $strMenuHtml = $arregloModulos["modulos"]."@@@@".$arregloModulos["submodulos"];
                                        
                                $entitySeguMenuPersona = new SeguMenuPersona();
                                $entitySeguMenuPersona->setPersonaId($arrayVariables["id_persona"]);
                                $entitySeguMenuPersona->setMenuHtml(addslashes($strMenuHtml));
                                $entitySeguMenuPersona->setUsrCreacion('telcos');
                                $entitySeguMenuPersona->setFeCreacion(new \DateTime('now'));
                                $entitySeguMenuPersona->setIpCreacion($objRequest->getClientIp());
                                //$seguMenuPersona->setIpCreacion("192.168.240.11");
                                
                                $emSeguridad->getConnection()->beginTransaction();
                                
                                try
                                {
                                    $emSeguridad->persist($entitySeguMenuPersona);
                                    $emSeguridad->flush();
                                    
                                    $emSeguridad->getConnection()->commit();
                                }
                                catch(\Exception $e)
                                {
                                    $emSeguridad->getConnection()->rollback();
                                    $emSeguridad->getConnection()->close();
                                }
                            }
                        }
                        
                        $this->session->set('html_modulos'      , $strHtmlModulos);
                        $this->session->set('html_submodulos'   , $strHtmlSubmodulos);
	  
                        if( !$session->get('modulo_activo') or strtolower($session->get('modulo_activo')) != strtolower($strModulo) 
                            or strtolower($strOpcionMenu) != strtolower($session->get('menu_modulo_activo')))
                        {
                            $arrayVariables = array("id_persona" => $session->get('id_empleado')); 
                            
                            $arrayMenuElegido = $em->getRepository('schemaBundle:SistItemMenu')->findXModulo(null, ucfirst($strModulo));
                            
                            if( $arrayMenuElegido && count($arrayMenuElegido) > 0 )
                            {
                                $strImagenModuloActivo = $arrayMenuElegido['urlImagen'];
                                $strModuloActivo       = $arrayMenuElegido['nombreItemMenu'];
                                $strModuloActivoHtml   = $arrayMenuElegido['titleHtml'];
                                $intIdModuloActivo     = $arrayMenuElegido['id'];
                            }
		
                            if( empty($intIdModuloActivo) )
                            {
                                $intIdModuloActivo     = 2;
                                $strOpcionMenu         = "dashboard";
                                $strModuloActivo       = "Comercial";
                                $strModuloActivoHtml   = "Comercial";
                                $strImagenModuloActivo = "shopping_cart.png";
                            }
            
                            $arrayItems              = null;
                            $arrayItemsTotals        = null;
                            $strOpcionMenuImagen     = "";
                            $intOpcionMenuId         = 0;
                            $strOpcionMenuNombre     = "";
                            $strOpcionMenuNombreHtml = "";
			    
                            $arrayMenu = $em->getRepository('schemaBundle:SistItemMenu')
                                           ->findDescripcionItem($intIdModuloActivo, ucwords($strOpcionMenu));
                            
                            if( $arrayMenu )
                            {
                                //AQUI RETORNA ITENMENU -- BANDERA				
                                $strOpcionMenuImagen     = $arrayMenu["urlImagen"];
                                $intOpcionMenuId         = $arrayMenu["id"];
                                $strOpcionMenuNombre     = $arrayMenu["nombreItemMenu"];
                                $strOpcionMenuNombreHtml = $arrayMenu["titleHtml"];
                            }
	    
                            $session->set('modulo_activo'             , $strModuloActivo);
                            $session->set('modulo_activo_html'        , $strModuloActivoHtml);
                            $session->set('id_modulo_activo'          , $intIdModuloActivo);
                            $session->set('imagen_modulo_activo'      , $strImagenModuloActivo);
                            $session->set('menu_modulo_activo'        , $strOpcionMenu);
                            $session->set('nombre_menu_modulo_activo' , $strOpcionMenuNombreHtml);
                            $session->set('id_menu_modulo_activo'     , $intOpcionMenuId);
                            $session->set('imagen_menu_modulo_activo' , $strOpcionMenuImagen);
	    
                            /*
                             * MENU 3ER NIVEL  
                             * Si la opcion del menu no es el dashboard recupero el submenu  
                             */       
                            $arraySubmenuOpcionModulo = array();
                            
                            if( strtolower($strOpcionMenu) != 'dashboard' && strtolower($strOpcionMenu) != 'menu' )
                            { 
                                //BUSCAR LOS PERFILES ASIGNADOS A ESA PERSONA    
                                $arrayUsuarioPerfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadIdPerfilEmpleado($arrayVariables);
                                
                                $arrayPerfiles = false;
                                
                                if($arrayUsuarioPerfiles)
                                {
                                    foreach($arrayUsuarioPerfiles as $arrayPerfil)
                                    {	
                                        $arrayPerfiles[] = $arrayPerfil['id'];
                                    }
                                }  
                    
                                $arrayItemsTotals = $this->retornaItemsMenu($arrayPerfiles, $session->get('id_menu_modulo_activo'));

                                $session->set('limite_items_menu', round(count($arrayItemsTotals)/2));
                    
                                if($arrayItemsTotals && count($arrayItemsTotals)>0)
                                {
                                    foreach($arrayItemsTotals as $arraySubmenu)
                                    {
                                        $submenu                            = array();
                                        $submenu['descripcionOpcion']       = $arraySubmenu['descripcionOpcion'];
                                        $submenu['descripcionSubOpcion']    = $arraySubmenu['descripcionHTML'];							
                                        $submenu['tituloHTML']              = $arraySubmenu['tituloHTML'];
                                        $submenu['descripcionHTML']         = $arraySubmenu['descripcionHTML'];
                                        $submenu['img']                     = $arraySubmenu['img'];
                                        $submenu['href']                    = "/".$strModulo."/".$strOpcionMenu."/".$arraySubmenu['href'];
                                        $arraySubmenuOpcionModulo[]         = $submenu;
                                    }
                                }
                                
                                $session->set('submenu_modulo', $arraySubmenuOpcionModulo);
                            }//( strtolower($strOpcionMenu) != 'dashboard' && strtolower($strOpcionMenu) != 'menu' )
	    
                            //recupero todas las opciones de administracion para ponerlos en el dashboard
                            if( strtolower($strOpcionMenu) == 'menu' and strtolower($strModuloActivo) == 'administracion' )
                            {
                                $strScriptName  = $_SERVER['SCRIPT_NAME'];
                                $strNameFinal   = ($strScriptName ? $strScriptName : $_SERVER['REQUEST_URI']);

                                //BUSCAR LOS PERFILES ASIGNADOS A ESA PERSONA    
                                $arrayUsuarioPerfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')
                                                           ->loadPerfilEmpleado($arrayVariables);
                                
                                $arrayPerfiles = false;
                                
                                if( $arrayUsuarioPerfiles )
                                {
                                    foreach($arrayUsuarioPerfiles as $objPerfiles)
                                    {	
                                        $arrayPerfiles[] = $objPerfiles->getPerfilId()->getId();
                                    }
                                }  
					
                                $arrayMenuDashboard = array();                    
                                $arrayMenuModulo = $em->getRepository('schemaBundle:SeguAsignacion')
                                                      ->loadAsignacion_RelacionSistema($arrayPerfiles, $intIdModuloActivo);
                
                                $session->set('limite_menu_admin',round(count($arrayMenuModulo)/2));
                                
                                if( $arrayMenuModulo && count($arrayMenuModulo) > 0 )
                                {
                                    foreach( $arrayMenuModulo as $objItemMenu )
                                    { 
                                        $strTituloOpcion      = $objItemMenu->getRelacionSistemaId()->getItemMenuId()->getTitleHtml(); 
                                        $strDescripcionOpcion = $objItemMenu->getRelacionSistemaId()->getItemMenuId()->getNombreItemMenu(); 
                                        $strNombreModulo      = $objItemMenu->getRelacionSistemaId()->getModuloId()->getNombreModulo();
                                                        
                                        $intIdOpcion = $objItemMenu->getRelacionSistemaId()->getItemMenuId()->getId();
                        
                                        if( strtolower($strDescripcionOpcion) !='menu' )
                                        {
                                            $arrayMenuDashboard[$intIdOpcion] = array();

                                            //AQUI RETORNA ITENMENU -- BANDERA
                                            $arraySubmenuModulo = $this->retornaItemsMenu($arrayPerfiles, $intIdOpcion);
                                            $arrayItemsSubmenu      = array();

                                            if($arraySubmenuModulo && count($arraySubmenuModulo)>0)
                                            {
                                                foreach($arraySubmenuModulo as $objSubmenu)
                                                {
                                                    $strHref = "/".strtolower($strModuloActivo)."/".$strNombreModulo
                                                               ."/".$objSubmenu['href'];
                                                    
                                                    $item_submenu                      = array();
                                                    $item_submenu['descripcionOpcion'] = ucfirst($objSubmenu['descripcionOpcion']);
                                                    $item_submenu['tituloHTML']        = ucfirst($objSubmenu['tituloHTML']);
                                                    $item_submenu['descripcionHTML']   = ucfirst($objSubmenu['descripcionHTML']);
                                                    $item_submenu['href']              = $strNameFinal.$strHref;
                                                    $arrayItemsSubmenu[]               = $item_submenu;
                                                }
                                            }
                            
                                            $arrayMenuDashboard[$intIdOpcion]['titulo']     = ucfirst($strDescripcionOpcion);
                                            $arrayMenuDashboard[$intIdOpcion]['tituloHTML'] = ucfirst($strTituloOpcion);
                                            $arrayMenuDashboard[$intIdOpcion]['items']      = $arrayItemsSubmenu;
                                        }
                                    }//foreach($arrayMenuModulo as $objItemMenu)
                                }//( $arrayMenuModulo && count($arrayMenuModulo) > 0 )
                                
                                $session->set('menu_dashboard', $arrayMenuDashboard);
                            }//( strtolower($strOpcionMenu) == 'menu' and strtolower($strModuloActivo) == 'administracion' )
                        }/*( !$session->get('modulo_activo') or strtolower($session->get('modulo_activo')) != strtolower($strModulo) 
                            or strtolower($strOpcionMenu) != strtolower($session->get('menu_modulo_activo')))*/
	  
                        //calcula saldo del cliente para session
                        if( $this->session->get('ptoCliente') )
                        {
                            $arrayPtoCliente    = $this->session->get('ptoCliente');
                            $em                 = $this->doctrine->getManager();
                            $intCodEmpresa      = $this->session->get('idEmpresa');
                            $emFinanciero       = $this->doctrine->getManager('telconet_financiero');
                            $objCliente = $this->session->get('cliente');
                            $arraySaldoYFacturasAbiertasPunto = array();

                            if ($intCodEmpresa == 10)
                            {
                                $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                ->getPuntosFacturacionAndFacturasAbiertasByIdPunto( $arrayPtoCliente['id'],
                                                                                    $em,
                                                                                    $intCodEmpresa);
    
                            }else if ($intCodEmpresa == 18 || $intCodEmpresa == 33)
                            {
                                try
                                {

                                    $arrayParametros = array();
                                    $arrayParametros["intIdPunto"] = $arrayPtoCliente['id'];
                                    $arrayParametros["em"] = $em;
                                    $arrayParametros["codEmpresa"] = $intCodEmpresa;
                                    $arrayParametros["identificacion"] = $objCliente['identificacion'];

                                    $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                    ->getPuntosAndFacturasAbiertasByIdentificacion($arrayParametros);

                                } catch (\Exception $th)
                                {
                                    $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                    ->getPuntosFacturacionAndFacturasAbiertasByIdPunto( $arrayPtoCliente['id'],
                                                                                    $em,
                                                                                    $intCodEmpresa);
                                }
                                
                            }

                            
                            $this->session->set('datosFinancierosPunto', $arraySaldoYFacturasAbiertasPunto);

                            //Servicios del login para toolbar
                            $arrayServiciosSession = array();
                            
                            $query = $em->createQuery(
                                                        "SELECT s
                                                         FROM schemaBundle:InfoServicio s
                                                         WHERE s.puntoId = :punto 
                                                           AND lower(s.estado) not in (:estados)
                                                         ORDER BY s.feCreacion ASC"
                                                     );
                            
        
                            $query->setParameter('punto', $arrayPtoCliente['id']);
                            $query->setParameter('estados', "'eliminado','anulado','rechazada','rechazado'");
                            
                            $arrayServicios = $query->getResult();
	      
                            if( $arrayServicios )
                            {
                                foreach($arrayServicios as $objServicio)
                                {
                                    $arrayItemServicioSession = array();
                                    $intInfoPlan              = $objServicio->getPlanId();
                                    $intAdmiProducto          = $objServicio->getProductoId();
                                    
                                    if($intInfoPlan)
                                    {
                                        $arrayItemServicioSession['nombre'] = $intInfoPlan->getNombrePlan();
                                    }
                                    
                                    if($intAdmiProducto)
                                    {
                                        $arrayItemServicioSession['nombre'] = $intAdmiProducto->getDescripcionProducto();
                                    }
                                    
                                    $arrayItemServicioSession['estado']  = $objServicio->getEstado();
                                    
                                    $arrayServiciosSession[] = $arrayItemServicioSession;
                                }
                            }
                            
                            $this->session->set('numServicios'   , count($arrayServiciosSession));
                            $this->session->set('serviciosPunto' , $arrayServiciosSession);
                        }//($this->session->get('ptoCliente'))
                    }//( in_array(strtolower($strModulo),$arrayModulos) )
                    if($boolRequiereCambioPass)
                    {
                        //Los path que no deben redireccionarse
                        if($strPath != "/inicio/" && $strPath != "/inicio" && $strPath != "/check" && $strPath != "/"
                            && $strPath != "/inicio/actualizarCambioClave" && $strPath != "/inicio/cambiarClave")
                        {
                            $event->setResponse(new RedirectResponse("/inicio"));
                        }
                    }
                    
                }//( $objEmpleado )
            }//( $objUser )
        }//( !$objRequest->isXmlHttpRequest() && $strUri != "ws" && $strUri != "rs" )
    }
  

    /**
      * Documentación para el método 'retornaModulos'.
      *
      * Retorna un arreglo que contiene el menú principal y el submenú de la
      * aplicación.
      * 
      * @param object $objRequest
      * @param array  $arrayVariables
      *
      * @author Modificado: Edson Franco <efranco@telconet.ec>
      * @version 1.1 07-08-2015 - Se modifica para que las opciones del submenú que 
      *                           contiene nombres largos concatenados con un espacio
      *                           ó un subguión se muestren en dos líneas.  
      * 
      * @version 1.0 Version Inicial
      */
    public function retornaModulos($objRequest, $arrayVariables)
    {        
        $arrayResultados = false;
        $strScriptName   = $_SERVER['SCRIPT_NAME'];
        $strNameFinal    = ($strScriptName ? $strScriptName : $_SERVER['REQUEST_URI']);
        
        $emSeguridad   = $this->doctrine->getManager('telconet_seguridad');

        //ARMA ARREGLOS --> PERFILES PERMITIDOS
        $arrayUsuarioPerfiles = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                            ->loadPerfilEmpleado($arrayVariables);
        
        $arrayPerfiles = false;
        
        if($arrayUsuarioPerfiles)
        {
            foreach($arrayUsuarioPerfiles as $objPerfil)
            {	
                $arrayPerfiles[] = $objPerfil->getPerfilId()->getId();
            }
        }

        if( $arrayPerfiles && count($arrayPerfiles)>0 )
        {
            //ARMA ARREGLOS --> ITEMS MENU PERMITIDOS
            $arraySeguAsignacion = $emSeguridad->getRepository('schemaBundle:SeguAsignacion')
                                               ->loadAsignacion($arrayPerfiles);
            $arrayItems       = false;
            $arrayItemsTotals = false;
            
            if($arraySeguAsignacion)
            {
                foreach($arraySeguAsignacion as $objSeguAsig)
                {
                    $objSeguRelacionSistema = $objSeguAsig->getRelacionSistemaId();
                    $objSelItemId           = $objSeguRelacionSistema->getItemMenuId();

                    if( $objSelItemId )
                    {
                        $objSeguAccion = $objSeguRelacionSistema->getAccionId();
                        $intSelItemId  = $objSelItemId->getId();
                        $arrayItems[]  = $intSelItemId;
                        
                        $arrayItemsTotals[$intSelItemId]["accion_id"]     = $objSeguAccion->getId();
                        $arrayItemsTotals[$intSelItemId]["accion_nombre"] = $objSeguAccion->getNombreAccion();
                        $arrayItemsTotals[$intSelItemId]["modulo_id"]     = $objSeguAccion->getId();
                        $arrayItemsTotals[$intSelItemId]["modulo_nombre"] = $objSeguRelacionSistema->getModuloId()
                                                                                                   ->getNombreModulo();
                    }
                }
            }

            if( $arrayItems && count($arrayItems) > 0 )
            {
                $arrayMenuModulosQuery = $emSeguridad->getRepository('schemaBundle:SistItemMenu')
                                                     ->findListarItemsMenu("", $arrayItems);
                $arrayMenuModulos = false;
                $arrayMenuModulos = $this->retornaArregloMenu($arrayMenuModulosQuery, $arrayItemsTotals, "S");

                $strHtmlModulos    = "";
                $strHtmlSubModulos = "";
             
                if( $arrayMenuModulos && count($arrayMenuModulos) > 0 )
                {
                    $strHtmlModulos     = "<div id='menu_modulos'><ul>";
                    $strHtmlSubModulos  = "";
                    
                    foreach( $arrayMenuModulos as $key => $arrayValue )
                    {
                        $strHtmlModulos .= "<li id='item_modulo_".strtolower($arrayValue["descripcionOpcion"])."'>"
                                            ."<a href='".$strNameFinal."/".strtolower($arrayValue["href"])."'>"
                                                ."<span class='item_menu_modulo'>".$arrayValue["descripcionOpcion"]."</span>"
                                            ."</a>"
                                          ."</li>";

                        //SUBMODULOS             
                        $strHtmlSubModulos .= "<div id='menu_modulo_".strtolower($arrayValue["descripcionOpcion"])."' style='display:none;'>"
                                                ."<div id='logo_modulo'>"
                                                    ."<img src='".$objRequest->getBasePath()."/public/images/".$arrayValue["img"]."' "
                                                           ."alt='".$arrayValue["img"]."' width='50' height='51' "
                                                           ."title='Modulo ".$arrayValue["descripcionOpcion"]."'/>"
                                
                                                ."</div>"
                                                ."<div id='logo_sit'>"
                                                    ."<img src='".$objRequest->getBasePath()."/public/images/logo.png' alt='logo.png' "
                                                           ."width='103' height='40' />"
                                                    ."<p id='nombre_modulo'>".$arrayValue["descripcionOpcion"]."</p>"
                                                ."</div>"
                                                ."<div id='menu_modulo'>"
                                                    ."<ul>";

                        $arrayMenuModuloQuery = $emSeguridad->getRepository('schemaBundle:SistItemMenu')
                                                            ->findListarItemsMenu($arrayValue["id"], $arrayItems);
                     
                        $arrayMenuModulo      = $this->retornaArregloMenu($arrayMenuModuloQuery, $arrayItemsTotals, "N");

                     
                        if( $arrayMenuModulo && count($arrayMenuModulo) > 0 )
                        {
                            foreach( $arrayMenuModulo as $key => $arrayItemMenu) 
                            {
                                $arrayDescripcionOpcion = array();
                                $intPosEspacio          = 0;
                                $intPosSubguion         = 0;
                                $strDescripcionOpcion   = $arrayItemMenu["descripcionOpcion"];
                                $strCssAdicional        = "";

                                $intPosEspacio = strpos($arrayItemMenu["descripcionOpcion"], ' ');

                                if( $intPosEspacio > 0 )
                                {
                                    $arrayDescripcionOpcion = explode(" ", $arrayItemMenu["descripcionOpcion"]);
                                }
                                else
                                {
                                    $intPosSubguion = strpos($arrayItemMenu["descripcionOpcion"], '_');

                                    if( $intPosSubguion > 0 )
                                    {
                                        $arrayDescripcionOpcion = explode("_", $arrayItemMenu["descripcionOpcion"]);
                                    }
                                }

                                if( count($arrayDescripcionOpcion) > 0 )
                                {
                                    $strCssAdicional      = " submenu-with-two-words";
                                    $strDescripcionOpcion = $arrayDescripcionOpcion[0]."<br/>".$arrayDescripcionOpcion[1];
                                }
                                
                                $strHtmlSubModulos .= "<li id='item_submodulo_".strtolower($arrayItemMenu["descripcionOpcion"])."' class='rounded-corners'>".
                                                        "<a href='".$strNameFinal."/".strtolower($arrayValue["descripcionOpcion"])
                                                                   ."/".strtolower($arrayItemMenu["href"])."'>".
                                                            "<img class='img_menu' src='".$objRequest->getBasePath()."/public/images/".$arrayItemMenu["img"]."'"
                                                                  ." alt='".$arrayItemMenu["img"]."' width='35' height='36.5' />".
                                                            "<p class='alignright".$strCssAdicional."'>".$strDescripcionOpcion."</p>"
                                                        ."</a>"
                                                    ."</li>";

                            }//foreach( $arrayMenuModulo as $key => $arrayItemMenu)
                        }// $arrayMenuModulo && count($arrayMenuModulo) > 0 )
                        
                        $strHtmlSubModulos .= "    </ul>";
                        $strHtmlSubModulos .= "  </div>";
                        $strHtmlSubModulos .= "</div>";
                        
                    }//foreach( $arrayMenuModulos as $key => $arrayValue )
                    
                    $strHtmlModulos .= "</ul></div>";
                    
                }//( $arrayMenuModulos && count($arrayMenuModulos) > 0 )

                $arrayResultados['modulos']    = $strHtmlModulos;
                $arrayResultados['submodulos'] = $strHtmlSubModulos;
                
            }//( $arrayItems && count($arrayItems)>0 )
        }//( $arrayPerfiles && count($arrayPerfiles)>0 )
        
        return $arrayResultados;
    }
	
  public function retornaArregloMenu($modulo_query, $arrayItemsTotals, $opcion, $opcion_menu="")
    {                
		$menu_modulos = false;
		if($modulo_query && count($modulo_query)>0)
		{ 
		    foreach($modulo_query as $imodulo)
            {
				$menu_modulo = array();

				if($opcion == "S")
				{
				    $id = $imodulo["id"];
				    $menu_modulo['id'] = $id;
				    $menu_modulo['descripcionOpcion'] = $imodulo['nombreItemMenu'];
				    $menu_modulo['img'] = $imodulo['urlImagen'];
				}
				else
				{
				    $id = $imodulo["id"];
				    $menu_modulo['id'] = $id;
				    $menu_modulo['descripcionOpcion'] = $imodulo['nombreItemMenu'];
				    $menu_modulo['img'] = $imodulo['urlImagen'];

				}

				$modulo_nombre = ($arrayItemsTotals[$id] ? $arrayItemsTotals[$id]["modulo_nombre"] : "");
				$accion_nombre = ($arrayItemsTotals[$id] ? $arrayItemsTotals[$id]["accion_nombre"] : "");
				if($accion_nombre=="index") $href = $modulo_nombre;	
				else $href = $modulo_nombre . "/" . $accion_nombre;
				
				$menu_modulo['href'] =  ($opcion_menu ? $opcion_menu . "/" . $href . "/" : $href);
				$menu_modulos[] = $menu_modulo;
			}
		}
		return $menu_modulos;
    }

    public function retornaItemsMenu($arrayPerfiles, $itemPadre=null)
    { 
        $em = $this->doctrine->getManager('telconet_seguridad');
        
        $seguasigna = $em->getRepository('schemaBundle:SeguAsignacion')->loadAsignacion_RelacionSistema($arrayPerfiles, $itemPadre);
        $arrayItems = false;
        $arrayItemsTotals = false;
        if($seguasigna)
        {
            foreach($seguasigna as $seguasig){	
//                print($seguasig->getRelacionSistemaId());
//                die();
                $relacionSistemaId = $seguasig->getRelacionSistemaId();
                $itemMenuId = $relacionSistemaId->getItemMenuId();
                
                $sel_itemid =  $itemMenuId->getId();
                $arrayItems[] = $sel_itemid;
                
                $accionId = $relacionSistemaId->getAccionId();
                
                $nombre_accion =  $accionId->getNombreAccion();
                $nombre_modulo =  $relacionSistemaId->getModuloId()->getNombreModulo();
				if($nombre_accion) $href = $nombre_modulo;	
				else $href = $nombre_modulo . "/" . $nombre_accion;
                
                if($nombre_accion && $nombre_accion != "index")
                {
                    $href = $nombre_modulo . "/" . $nombre_accion;
                }
                
                $arrayItemsTotals[$sel_itemid]["accion_id"] =  $accionId->getId();
                $arrayItemsTotals[$sel_itemid]["accion_nombre"] = $nombre_accion;
                $arrayItemsTotals[$sel_itemid]["modulo_id"] =  $accionId->getId();
                $arrayItemsTotals[$sel_itemid]["modulo_nombre"] = $nombre_modulo;
                $arrayItemsTotals[$sel_itemid]["descripcionOpcion"] =  $itemMenuId->getNombreItemMenu();
                $arrayItemsTotals[$sel_itemid]["tituloHTML"] =  $itemMenuId->getTitleHtml();
                $arrayItemsTotals[$sel_itemid]["descripcionHTML"] = $itemMenuId->getDescripcionHtml();
                $arrayItemsTotals[$sel_itemid]["img"] =  $itemMenuId->getUrlImagen();
                $arrayItemsTotals[$sel_itemid]["href"] = $href;
            }
        }
        return $arrayItemsTotals;
    }
    /**
     * Método guardarActividadLogin
     * 
     * Se registrará un evento del usuario en sesión en la bitacora de actividades
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-10-05
     * 
     * @param $arrayParametros [
     *                         'intIdPersona'         => id de la Persona en Sesión
     *                         'intIdRelacionSistema' => id de la Relación de Sistema efectuado
     *                         'strBitacoraDetalle'   => una descripción de la acción realizada
     *                         'strIpCreacion'        => ip de creación
     *                         ]
     */
    public function guardarActividadLogin($arrayParametros)
    {
        $emSeguridad = $this->doctrine->getManager('telconet_seguridad');
        
        $objSeguBitacoraPersona = new SeguBitacoraPersona();
        $objSeguBitacoraPersona->setPersonaId($arrayParametros["intIdPersona"]);
        $objSeguBitacoraPersona->setRelacionSistemaId($arrayParametros["intIdRelacionSistema"]);
        $objSeguBitacoraPersona->setBitacoraDetalle($arrayParametros["strBitacoraDetalle"]);
        $objSeguBitacoraPersona->setUsrCreacion('telcos');
        $objSeguBitacoraPersona->setFeCreacion(new \DateTime('now'));
        $objSeguBitacoraPersona->setIpCreacion($arrayParametros["strIpCreacion"]);

        $emSeguridad->getConnection()->beginTransaction();

        try
        {
            $emSeguridad->persist($objSeguBitacoraPersona);
            $emSeguridad->flush();

            $emSeguridad->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if($emSeguridad->getConnection()->isTransactionAcive())
            {
                $emSeguridad->getConnection()->rollback();
            }
            $emSeguridad->getConnection()->close();
        }
    }
}

?>
