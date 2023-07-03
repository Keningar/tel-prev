<?php

namespace telconet\inicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function homeAction($modulo,$opcion_menu)
    {	
	$opcion_menu_anterior = $opcion_menu;
	if(strtolower($modulo) == "comercial" && strtolower($opcion_menu) == "default") $opcion_menu = "prospectos";
	if(strtolower($modulo) == "planificacion" && strtolower($opcion_menu) == "default") $opcion_menu = "factibilidad";
	//if(strtolower($modulo) == "soporte" && strtolower($opcion_menu) == "default") $opcion_menu = "casos";
		
        //variables globales
        $request  = $this->get('request');
        $session  =  $request->getSession();
	$id_modulo_activo = "";
	//$user = $this->get('security.context')->getToken()->getUser();   
        
        $em = $this->get('doctrine')->getManager('telconet_seguridad');
        $em_comercial = $this->get('doctrine')->getManager('telconet');
      
        

	$arrayVariables = array(
			"id_persona" => $session->get('id_empleado')
			);   
        
        
		
        /*
		* verifica si el modulo activo esta en session y si es el mismo al que el usuario desea ir 
	        * sino vuelve a cargar el menu del modulo ,la imagen del modulo y pone en session el nuevo modulo activo
		*/
        if(!$session->get('modulo_activo') or strtolower($session->get('modulo_activo')) != strtolower($modulo) or strtolower($opcion_menu) != strtolower($session->get('modulo_activo'))){	
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
				
            //MENU 3ER NIVEL---------  
            // si la opcion del menu no es el dashboard recupero el submenu          
            $submenu_opcion_modulo = array();
            if(strtolower($opcion_menu) != 'dashboard' && strtolower($opcion_menu) != 'menu'){  
              		
                $obj_menu = $em->getRepository('schemaBundle:SistItemMenu')->findDescripcionItem($id_modulo_activo, ucwords($opcion_menu));
                if($obj_menu){                    
                    //AQUI RETORNA ITENMENU -- BANDERA
                    //echo $obj_menu['id'];			
					
			        //BUSCAR LOS PERFILES ASIGNADOS A ESA PERSONA    
			        $usuario_perfiles = $em->getRepository('schemaBundle:SeguPerfilPersona')->loadPerfilEmpleado($arrayVariables);
			        $arrayPerfiles = false;
			        if($usuario_perfiles)
			        {
			            foreach($usuario_perfiles as $perfiles){	
			                $arrayPerfiles[] = $perfiles->getPerfilId()->getId();
			            }
			        }  
		
                    $arrayItemsTotals = $this->retornaItemsMenu($arrayPerfiles, $opcion_menu_id);
                
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
                            $submenu['href'] = $opcion_menu."/".$arraySubmenu['href'];
                            $submenu_opcion_modulo[] = $submenu;
                        }
                    }
                }
                $session->set('submenu_modulo',$submenu_opcion_modulo);
            }

		    /*pongo la empresa en session*/
		    /*pondremos por default a la empresa telconet asumiendo que es la que nos devuelve el API*/
		    $em = $this->get('doctrine')->getManager('telconet');
		    
		    /*pongo la empresa en session*/
		    //$session->set('menu_modulo',$menu_modulo);
		    $session->set('modulo_activo',$modulo_activo);
		    $session->set('modulo_activo_html',$modulo_activo_html);
		    $session->set('id_modulo_activo',$id_modulo_activo);
		    $session->set('imagen_modulo_activo',$imagen_modulo_activo);
			
		    $session->set('menu_modulo_activo',$opcion_menu);
		    $session->set('nombre_menu_modulo_activo',$opcion_menu_nombre_html);
		    $session->set('id_menu_modulo_activo',$opcion_menu_id);
		    $session->set('imagen_menu_modulo_activo',$opcion_menu_imagen);	
		}
        	
		//echo strtolower($session->get('modulo_activo')) . " -- " . $opcion_menu . "  -- "  . ucfirst($opcion_menu_nombre_html) . ' -- ' . $opcion_menu_imagen;
		// ****************** QUEMADO PARA QUE SE VAYA AUTOMATICAMANTE A OTRO QUE NO SEA DASHBOARD
		if(strtolower($modulo) == "comercial" && strtolower($opcion_menu_anterior) == "default") 
		{
			return $this->render(strtolower($session->get('modulo_activo')).'Bundle:PreCliente:index.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html),  'menu_imagen'=>$opcion_menu_imagen ));
			break;
		}
		if(strtolower($modulo) == "planificacion" && strtolower($opcion_menu_anterior) == "default") 
		{
			return $this->render(strtolower($session->get('modulo_activo')).'Bundle:Default:factibilidad.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html),  'menu_imagen'=>$opcion_menu_imagen ));
			break;
		}
		if(strtolower($modulo) == "soporte" && strtolower($opcion_menu_anterior) == "default") 
		{
			//return $this->render(strtolower($session->get('modulo_activo')).'Bundle:InfoCaso:index.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html),  'menu_imagen'=>$opcion_menu_imagen ));
			//break;
		}
			
		// como el modulo de administracion es adminBundle y no administracionBundle.. por eso se valida el nombre del template
		switch (strtolower($session->get('modulo_activo')))
		{
		    case ('administracion'):
		    {
				if(strtolower($opcion_menu) == 'menu'){
					return ($this->render('administracionBundle:Default:'.$opcion_menu.'.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html), 'menu_imagen'=>$opcion_menu_imagen,'html_modulos'=>$html_modulos, 'html_submodulos'=>$html_submodulos)));
				}else{
					return ($this->render('administracionBundle:Default:layout_opcion_admin.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html), 'menu_imagen'=>$opcion_menu_imagen, 'html_modulos'=>$html_modulos, 'html_submodulos'=>$html_submodulos)));
				}			
				break;
		    }
		    case ('inicio'):
		    {            
				return ($this->render('adminBundle:Inicio:'.$opcion_menu.'.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html))));
				break;
		    }
		    default:
		    {
				return ($this->render(strtolower($session->get('modulo_activo')).'Bundle:Default:'.$opcion_menu.'.html.twig',array('menu_title'=>ucfirst($opcion_menu_nombre_html),  'menu_imagen'=>$opcion_menu_imagen )));
				break;
		    }
		      
		}		
    }
    
     public function retornaItemsMenu($arrayPerfiles, $itemPadre=null)
    { 
        $em = $this->get('doctrine')->getManager('telconet_seguridad');
        
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
