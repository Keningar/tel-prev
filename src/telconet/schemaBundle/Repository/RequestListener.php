<?php
 
namespace telconet\seguridadBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\HttpKernel;
use TelconetSSO\TelconetSSOBundle\Security\Authentication\Token\UserSSOToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use telconet\schemaBundle\Entity\SeguMenuPersona;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
// use Symfony\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.0.x
/**
* Custom Request listener.
*/
class RequestListener extends controller
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;
    /** @var \Doctrine\ORM\EntityManager */
    private $doctrine;
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;
   /**
    * Constructor
    *
    * @param SecurityContext $securityContext
    * @param Doctrine $doctrine
    * @param Doctrine $session
    */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Session $session)
    {
	$this->securityContext = $securityContext;
	$this->doctrine = $doctrine;
	$this->session = $session;
    }
   /**
    * Do the magic.
    *
    * @param GetResponseEvent $event
    */
    public function onKernelRequest(GetResponseEvent $event)
    {   
      
//       if (HttpKernel::MASTER_REQUEST == $event->getRequestType()) {
    
//         if($this->session->get('request_login')){
// 	  echo "es login";
// 	  $this->session->remove('request_login');
//         }else{
// 	  echo "no es login";
//         }
//         
//         }
//         return;
    //if you are passing through any data

      $request = $event->getRequest();
      
      if(!$request->isXmlHttpRequest()){
	$modulos = array();
	$modulos[] = "inicio";
	$modulos[] = "comercial";
	$modulos[] = "planificacion";
	$modulos[] = "tecnico";
	$modulos[] = "financiero";
	$modulos[] = "soporte";
	$modulos[] = "administracion";
        
        $user ='';
	// do some other magic here
	if($this->securityContext->getToken()){
	  $user = $this->securityContext->getToken()->getUser();
	}
	// ...
	if(!$user){
	  $user = $request->getSession()->get('user_sso');
	}
	
	if($user){
	$empleado = $this->doctrine->getManager()->getRepository('schemaBundle:InfoPersona')->getPersonaPorLogin($user->getUsername());
            
	if($empleado)
	{  
	    $idEmpresa = $this->session->get('idEmpresa');
	    if(isset($idEmpresa)){
	    }else{
	      //guardo empresas del usuario
	      $arrayEmpresas = $this->doctrine->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpresasByPersona($user->getUsername(), "Empleado");
	      $arrayEmpresasExternos = $this->doctrine->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpresasByPersona($user->getUsername(), "Personal Externo");
	      $arrayEmpresas = array_merge($arrayEmpresas,$arrayEmpresasExternos);
	      
	      $this->session->set('arrayEmpresas',$arrayEmpresas);
	      if($arrayEmpresas && count($arrayEmpresas)>0)
	      {
		$this->session->set('idPersonaEmpresaRol', $arrayEmpresas[0]["IdPersonaEmpresaRol"]);
		$this->session->set('idEmpresa', $arrayEmpresas[0]["CodEmpresa"]);
		$this->session->set('empresa', $arrayEmpresas[0]["razonSocial"]);
		$this->session->set('idOficina', $arrayEmpresas[0]["IdOficina"]);
		$this->session->set('oficina', $arrayEmpresas[0]["nombreOficina"]);
		$this->session->set('idDepartamento', $arrayEmpresas[0]["IdDepartamento"]);
		$this->session->set('departamento', $arrayEmpresas[0]["nombreDepartamento"]);
	      }
	    }
	    $idEmpleado = $this->session->get('id_empleado');
	    if(isset($idEmpleado)){
	    }else{
	      $this->session->set('empleado', $empleado->getNombres().' '.$empleado->getApellidos() );
	      $this->session->set('id_empleado', $empleado->getId() );
	      $this->session->set('user', $empleado->getLogin() );
	    }
	    //guardo roles del usuario
	    $roles = $this->doctrine->getManager()->getRepository('schemaBundle:SeguAsignacion')->getRolesXEmpleado($empleado->getId());
	
	    foreach($roles as $rol):				
			    $nombreRol = 'ROLE_'.$rol["modulo_id"].'-'.$rol["accion_id"];
// 			    $role = new \Symfony\Component\Security\Core\Role\Role($nombreRol);
			    $user->addRol($nombreRol);
	    endforeach;
			    
	    
	    $arrayVariables = array(
			"id_persona" => $this->session->get('id_empleado')
			);
	    //actualizo el usuario con sus roles
// $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, null, 'my_fos_user_provider', $user->getRoles());
           $authenticatedToken = new UserSSOToken($user, $user->getRoles());
//            $authenticatedToken->setUser($user);
	   
            $this->securityContext->setToken($authenticatedToken);
//             $this->securityContext->getToken()->eraseCredentials();
	    $this->securityContext->getToken()->setUser($user);
 	    $this->securityContext->getToken()->setAuthenticated(true);
	    
 	    $rolesPermitidos = $this->securityContext->getToken()->getUser()->getRoles();
 	    sort($rolesPermitidos);
 	    $rolesPermitidos = array_unique($rolesPermitidos);
 	    $this->session->set('rolesPermitidos',$rolesPermitidos);	
	      
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $path = $request->getPathInfo();
//         echo $path."<br>";
        $partsPath = explode("/",$path);
//          echo "tiene".count($partsPath);
//          die;
// echo "part0".$partsPath[0]."<br>";
// echo "part1".$partsPath[1];
        if($partsPath[1]!=""){
// 	  echo "soy menu";

	  $modulo = $partsPath[1];
	  if(isset($partsPath[2])){
	    $opcion_menu = $partsPath[2];
	  }else{
	    $opcion_menu= "dashboard";
	  }
        }else{
// 	  echo "no soy menu";
	  $modulo = "inicio";
	  $opcion_menu= "dashboard";
        }
        
        //if(strtolower($modulo) == "comercial" && strtolower($opcion_menu) == "default") $opcion_menu = "prospectos";
	//if(strtolower($modulo) == "planificacion" && strtolower($opcion_menu) == "default") $opcion_menu = "factibilidad";
        if(strtolower($modulo) == "comercial" && $opcion_menu=="dashboard") $opcion_menu = "cliente";
        if(strtolower($modulo) == "financiero" && $opcion_menu=="dashboard") $opcion_menu = "documentos";
	if(strtolower($modulo) == "administracion" && $opcion_menu=="dashboard") $opcion_menu = "menu";
	if(strtolower($modulo) == "tecnico" && $opcion_menu=="dashboard") $opcion_menu = "clientes";
	//if(strtolower($modulo) == "soporte" && $opcion_menu=="dashboard") $opcion_menu = "info_caso";
//         die;
        //if you need to update the session data
        $session = $request->getSession();
	$em = $this->doctrine->getManager();
	
	
	if(in_array(strtolower($modulo),$modulos)){  
	  //guardo el menu del usuario
	    $menuPersona = $this->doctrine->getManager('telconet_seguridad')->getRepository('schemaBundle:SeguMenuPersona')->findByPersonaId($arrayVariables["id_persona"]);

	    if($menuPersona && count($menuPersona)>0)
	    {
		$arrayMenuPersona = explode("@@@@", $menuPersona[0]->getMenuHtml());
		$html_modulos = stripslashes($arrayMenuPersona[0]);
		$html_submodulos = stripslashes($arrayMenuPersona[1]);
	    }
	    else
	    {
			//BUSCAR EL MENU -- YA NO CREAR4 EL MODULO...
			$arregloModulos = $this->retornaModulos($request,$arrayVariables);
			$html_modulos = $arregloModulos["modulos"];
			$html_submodulos = $arregloModulos["submodulos"];
			
			//GRABAR EL MENU PERSONA
			$seguMenuPersona = new SeguMenuPersona();
			$seguMenuPersona->setPersonaId($arrayVariables["id_persona"]);
			$seguMenuPersona->setMenuHtml(addslashes($arregloModulos["modulos"]."@@@@".$arregloModulos["submodulos"]));
			$seguMenuPersona->setUsrCreacion('telcos');
			$seguMenuPersona->setFeCreacion(new \DateTime('now'));
			$seguMenuPersona->setIpCreacion($request->getClientIp());
			//$seguMenuPersona->setIpCreacion("192.168.240.11");
			$this->doctrine->getManager('telconet_seguridad')->persist($seguMenuPersona);
			$this->doctrine->getManager('telconet_seguridad')->flush();
	    }
	    $this->session->set('html_modulos',$html_modulos);
	    $this->session->set('html_submodulos',$html_submodulos);
	    
	  if(!$session->get('modulo_activo') or strtolower($session->get('modulo_activo')) != strtolower($modulo) or strtolower($opcion_menu) != strtolower($session->get('menu_modulo_activo'))){	
            $arrayVariables = array(
	       "id_persona" => $session->get('id_empleado')
	    ); 
            $menu_elegido = $em->getRepository('schemaBundle:SistItemMenu')->findXModulo(null, ucfirst($modulo));
            if($menu_elegido && count($menu_elegido)>0)
            {
                $imagen_modulo_activo = $menu_elegido['urlImagen'];
                $modulo_activo = $menu_elegido['nombreItemMenu'];
                $modulo_activo_html = $menu_elegido['titleHtml'];
                $id_modulo_activo = $menu_elegido['id'];
            }
		
	    if(empty($id_modulo_activo)){
			$id_modulo_activo = 2;
			$opcion_menu = "dashboard";
			$modulo_activo = "Comercial";
			$modulo_activo_html = "Comercial";
			$imagen_modulo_activo = "shopping_cart.png";
	    }
            
            $arrayItems = null; $arrayItemsTotals=null;            
            
	    $opcion_menu_imagen = "";
	    $opcion_menu_id = "";
	    $opcion_menu_nombre = "";
	    $opcion_menu_nombre_html = "";
			    
	    $obj_menu = $em->getRepository('schemaBundle:SistItemMenu')->findDescripcionItem($id_modulo_activo, ucwords($opcion_menu));
	    if($obj_menu){
		    //AQUI RETORNA ITENMENU -- BANDERA
		    //echo $obj_menu['id'];							
		    $opcion_menu_imagen = $obj_menu["urlImagen"];
		    $opcion_menu_id = $obj_menu["id"];
		    $opcion_menu_nombre = $obj_menu["nombreItemMenu"];
		    $opcion_menu_nombre_html = $obj_menu["titleHtml"];
	    }
	    
	    $session->set('modulo_activo',$modulo_activo);
	    $session->set('modulo_activo_html',$modulo_activo_html);
	    $session->set('id_modulo_activo',$id_modulo_activo);
	    $session->set('imagen_modulo_activo',$imagen_modulo_activo);
		
	    $session->set('menu_modulo_activo',$opcion_menu);
	    $session->set('nombre_menu_modulo_activo',$opcion_menu_nombre_html);
	    $session->set('id_menu_modulo_activo',$opcion_menu_id);
	    $session->set('imagen_menu_modulo_activo',$opcion_menu_imagen);
	    
	    //MENU 3ER NIVEL---------  
            // si la opcion del menu no es el dashboard recupero el submenu          
            $submenu_opcion_modulo = array();
            if(strtolower($opcion_menu) != 'dashboard' && strtolower($opcion_menu) != 'menu'){ 
//                 $obj_menu = $em->getRepository('schemaBundle:SistItemMenu')->findDescripcionItem($session->get('id_modulo_activo'), ucwords($opcion_menu));
//                 if($obj_menu){
		    //BUSCAR LOS PERFILES ASIGNADOS A ESA PERSONA    
		    $usuario_perfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadIdPerfilEmpleado($arrayVariables);
		    $arrayPerfiles = false;
		    if($usuario_perfiles)
		    {
			foreach($usuario_perfiles as $perfiles){	
// 			    $arrayPerfiles[] = $perfiles->getPerfilId()->getId();
			    $arrayPerfiles[] = $perfiles['id'];
			}
		    }  
		
                    $arrayItemsTotals = $this->retornaItemsMenu($arrayPerfiles, $session->get('id_menu_modulo_activo'));
                
                    $session->set('limite_items_menu',round(count($arrayItemsTotals)/2));
                    if($arrayItemsTotals && count($arrayItemsTotals)>0)
                    {
                        foreach($arrayItemsTotals as $arraySubmenu){
                            $submenu = array();
                            $submenu['descripcionOpcion'] = $arraySubmenu['descripcionOpcion'];
                            $submenu['descripcionSubOpcion'] = $arraySubmenu['descripcionHTML'];							
							$submenu['tituloHTML'] = $arraySubmenu['tituloHTML'];
							$submenu['descripcionHTML'] = $arraySubmenu['descripcionHTML'];
                            $submenu['img'] = $arraySubmenu['img'];
                            $submenu['href'] = "/".$modulo."/".$opcion_menu."/".$arraySubmenu['href'];
                            $submenu_opcion_modulo[] = $submenu;
                        }
                    }
//                 }
                $session->set('submenu_modulo',$submenu_opcion_modulo);
            }
	    
	    //recupero todas las opciones de administracion para ponerlos en el dashboard
	    if(strtolower($opcion_menu) == 'menu' and strtolower($modulo_activo) == 'administracion'){
                $script_name = $_SERVER['SCRIPT_NAME'];
                $nameFinal = ($script_name ? $script_name : $_SERVER['REQUEST_URI']);
                					
		//BUSCAR LOS PERFILES ASIGNADOS A ESA PERSONA    
		$usuario_perfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadPerfilEmpleado($arrayVariables);
		$arrayPerfiles = false;
		if($usuario_perfiles)
		{
			foreach($usuario_perfiles as $perfiles){	
				$arrayPerfiles[] = $perfiles->getPerfilId()->getId();
			}
		}  
					
                $menu_dashboard = array();                    
                $menu_modulo = $em->getRepository('schemaBundle:SeguAsignacion')->loadAsignacion_RelacionSistema($arrayPerfiles, $id_modulo_activo);
                
                $session->set('limite_menu_admin',round(count($menu_modulo)/2));
                if($menu_modulo && count($menu_modulo)>0)
                {
                    foreach($menu_modulo as $item_menu)
                    { 
                        $tituloOpcion = $item_menu->getRelacionSistemaId()->getItemMenuId()->getTitleHtml(); 
                        $descripcionOpcion = $item_menu->getRelacionSistemaId()->getItemMenuId()->getNombreItemMenu(); 
                        $nombre_modulo = $item_menu->getRelacionSistemaId()->getModuloId()->getNombreModulo();
                                                        
                        $idOpcion = $item_menu->getRelacionSistemaId()->getItemMenuId()->getId();
                        if(strtolower($descripcionOpcion) !='menu'){
                            $menu_dashboard[$idOpcion] = array();
                            
                            //AQUI RETORNA ITENMENU -- BANDERA
                            $submenu_modulo = $this->retornaItemsMenu($arrayPerfiles, $idOpcion);
                            $items_submenu = array();
                            if($submenu_modulo && count($submenu_modulo)>0)
                            {
                                foreach($submenu_modulo as $obj_submenu){
                                    $item_submenu = array();
                                    $item_submenu['descripcionOpcion'] = ucfirst($obj_submenu['descripcionOpcion']);
                                    $item_submenu['tituloHTML'] = ucfirst($obj_submenu['tituloHTML']);
                                    $item_submenu['descripcionHTML'] = ucfirst($obj_submenu['descripcionHTML']);
                                    $item_submenu['href'] = $nameFinal . "/" . strtolower($modulo_activo) . "/" . $nombre_modulo . "/" . $obj_submenu['href'];
                                    $items_submenu[] = $item_submenu;
                                }
                            }
                            $menu_dashboard[$idOpcion]['titulo'] =  ucfirst($descripcionOpcion);
                            $menu_dashboard[$idOpcion]['tituloHTML'] =  ucfirst($tituloOpcion);
                            $menu_dashboard[$idOpcion]['items'] = $items_submenu;
                        }
                    }
                }
                $session->set('menu_dashboard',$menu_dashboard);
	    }
// 	    $user = $this->securityContext->getToken()->getUser();
// 	    print_r( $user->getRoles());
	  }
	  //calcula saldo del cliente para session
	  if($this->session->get('ptoCliente')){
	      $ptoCliente = $this->session->get('ptoCliente');
	      $em = $this->doctrine->getManager();
	      $codEmpresa = $this->session->get('idEmpresa');
	      $emFinanciero = $this->doctrine->getManager('telconet_financiero');
	      
	      $arraySaldoYFacturasAbiertasPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getPuntosFacturacionAndFacturasAbiertasByIdPunto($ptoCliente['id'],$em,$codEmpresa);
	      
	      $this->session->set('datosFinancierosPunto',$arraySaldoYFacturasAbiertasPunto);
	      
	  }
	}
      }
     }
    }
  }
  
   public function retornaModulos($request,$arrayVariables)
    {        
        $script_name = $_SERVER['SCRIPT_NAME'];
        $nameFinal = ($script_name ? $script_name : $_SERVER['REQUEST_URI']);

        $em = $this->doctrine->getManager('telconet_seguridad');
        
		//ARMA ARREGLOS --> PERFILES PERMITIDOS
		$usuario_perfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadPerfilEmpleado($arrayVariables);
		$arrayPerfiles = false;
		if($usuario_perfiles)
		{
		    foreach($usuario_perfiles as $perfiles){	
			$arrayPerfiles[] = $perfiles->getPerfilId()->getId();
		    }
		}
        
        if($arrayPerfiles && count($arrayPerfiles)>0)
        {
            //ARMA ARREGLOS --> ITEMS MENU PERMITIDOS
            $seguasigna = $em->getRepository('schemaBundle:SeguAsignacion')->loadAsignacion($arrayPerfiles);
            $arrayItems = false;
            $arrayItemsTotals = false;
            if($seguasigna)
            {
                foreach($seguasigna as $seguasig){
		    $seguRelacionSistema = $seguasig->getRelacionSistemaId();
                    $sel_itemid = $seguRelacionSistema->getItemMenuId();
					
                    if($sel_itemid){
			      $seguAccion = $seguRelacionSistema->getAccionId();
			      $sel_itemid = $sel_itemid->getId();
			      $arrayItems[] = $sel_itemid;
			      $arrayItemsTotals[$sel_itemid]["accion_id"] = $seguAccion->getId();
			      $arrayItemsTotals[$sel_itemid]["accion_nombre"] = $seguAccion->getNombreAccion();
			      $arrayItemsTotals[$sel_itemid]["modulo_id"] = $seguAccion->getId();
			      $arrayItemsTotals[$sel_itemid]["modulo_nombre"] = $seguRelacionSistema->getModuloId()->getNombreModulo();
		      }
                }
            }

            if($arrayItems && count($arrayItems)>0)
            {
                $menu_modulos_query = $em->getRepository('schemaBundle:SistItemMenu')->findListarItemsMenu("", $arrayItems);
                $menu_modulos = false;
                $menu_modulos = $this->retornaArregloMenu($menu_modulos_query, $arrayItemsTotals, "S");

                $htmlModulos = "";
                $htmlSubModulos = "";
                if($menu_modulos && count($menu_modulos)>0)
                {
                    $htmlModulos = "<div id='menu_modulos'><ul>";
                    $htmlSubModulos = "";
                    foreach($menu_modulos as $key => $value)
                    {
                        $htmlModulos .= "<li id='item_modulo_" . strtolower($value["descripcionOpcion"]) . "' " . 
                                    "><a href='". $nameFinal . "/" .strtolower($value["href"])."'><span class='item_menu_modulo'>".$value["descripcionOpcion"]."</span></a></li>";

                        //SUBMODULOS ->getId()                
                        $htmlSubModulos .= "<div id='menu_modulo_".strtolower($value["descripcionOpcion"])."' style='display:none;'>";
                        $htmlSubModulos .= "    <div id='logo_modulo'><img src='".$request->getBasePath()."/public/images/".$value["img"]."' alt='".$value["img"]."' width='50' height='51' title='Modulo ".$value["descripcionOpcion"]."'/></div>";
                        $htmlSubModulos .= "    <div id='logo_sit'><img src='".$request->getBasePath()."/public/images/logo.png' alt='logo.png' width='103' height='40' /><p id='nombre_modulo'>".$value["descripcionOpcion"]."</p></div>";
                        //$htmlSubModulos .= "    <div id='search_login'><img src='".$request->getBasePath()."/public/images/search.png' alt='search.png' /><input type='text' placeholder='Buscar login' label='Buscar login' name='login' maxlength='100' autocomplete='off' id='login' /></div>";
                        $htmlSubModulos .= "    <div id='menu_modulo'>";

                        $menu_modulo_query = $em->getRepository('schemaBundle:SistItemMenu')->findListarItemsMenu($value["id"], $arrayItems);
                        $menu_modulo = $this->retornaArregloMenu($menu_modulo_query, $arrayItemsTotals, "N");

                        $htmlSubModulos .= "        <ul>";
                        if($menu_modulo && count($menu_modulo)>0)
                        {
                            foreach($menu_modulo as $key2 => $value2)
                            {
                                $htmlSubModulos .= "<li id='item_submodulo_".strtolower($value2["descripcionOpcion"])."' class='rounded-corners'>".
                                                    "<a href='" . $nameFinal . "/" .strtolower($value["descripcionOpcion"])."/".strtolower($value2["href"])."'>".
                                                    "<img class='img_menu' src='".$request->getBasePath()."/public/images/".$value2["img"]."' alt='".$value2["img"]."' width='35' height='36.5'/>".
                                                    "<p class='alignright'>".$value2["descripcionOpcion"]."</p></a></li>";

                            }
                        }
                        $htmlSubModulos .= "        </ul>";
                        $htmlSubModulos .= "    </div>";
                        $htmlSubModulos .= "</div>";
                    }
                    $htmlModulos .= "</ul></div>";
                }

                return array('modulos' => $htmlModulos, 'submodulos'=>$htmlSubModulos);
            }
        }
        return false;
    }
	
  public function retornaArregloMenu($modulo_query, $arrayItemsTotals, $opcion, $opcion_menu="")
    {                
		$menu_modulos = false;
		if($modulo_query && count($modulo_query)>0)
		{ 
		    foreach($modulo_query as $imodulo){
				$menu_modulo = array();

				if($opcion == "S")
				{
				    $id = $imodulo->getId();
				    $menu_modulo['id'] = $id;
				    $menu_modulo['descripcionOpcion'] = $imodulo->getNombreItemMenu();
				    $menu_modulo['img'] = $imodulo->getUrlImagen();
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
}

?>
