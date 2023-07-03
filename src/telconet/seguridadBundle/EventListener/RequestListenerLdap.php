<?php
 
namespace telconet\seguridadBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
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
error_log('request listener');
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
        error_log($modulo);
error_log($opcion_menu);
        //if(strtolower($modulo) == "comercial" && strtolower($opcion_menu) == "default") $opcion_menu = "prospectos";
	//if(strtolower($modulo) == "planificacion" && strtolower($opcion_menu) == "default") $opcion_menu = "factibilidad";
        if(strtolower($modulo) == "comercial" && $opcion_menu=="dashboard") $opcion_menu = "cliente";
        if(strtolower($modulo) == "financiero" && $opcion_menu=="dashboard") $opcion_menu = "documentos";
	if(strtolower($modulo) == "administracion" && $opcion_menu=="dashboard") $opcion_menu = "menu";
//         die;
        //if you need to update the session data
        $session = $request->getSession();              
        
	$em = $this->doctrine->getManager();
	if(in_array(strtolower($modulo),$modulos)){  
	
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
      }
    }
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
