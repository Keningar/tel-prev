<?php

namespace telconet\seguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\SistPerfil;
use telconet\schemaBundle\Entity\SistModulo;
use telconet\schemaBundle\Entity\SeguAsignacion;

use telconet\schemaBundle\Entity\SeguPerfilPersona;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoOficinaGrupo;
use telconet\schemaBundle\Entity\InfoEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersona;

use telconet\schemaBundle\Form\SeguPerfilPersonaType;
use telconet\schemaBundle\Entity\AdmiTelefonoSeguPerfilPersona;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * SeguPerfilPersona controller.
 *
 */
class SeguPerfilPersonaController extends Controller implements TokenAuthenticatedController
{
    
     /**
     * Perfil Consultar Perfil Persona
     * @Secure(roles="ROLE_20-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Metodo que realiza la redireccion a pantalla principal de la administracion
     *
     * @author Modificado: Karen Rodríguez Véliz <kyrodriguez@telconet.ec>
     * @version 1.6 15-02-2020 - Se agrega el rol 7137 para reseteo de clave.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 03-12-2014 - Se agrega gestion de roles de usuarios
     
     * @version 1.0 Version Inicial
     *
     * @return render
     */
    public function indexAction()
    {
        $rolesPermitidos = array();      

        if(true === $this->get('security.context')->isGranted('ROLE_20-6'))
        {
            $rolesPermitidos[] = 'ROLE_20-6'; //Perfil Consultar Perfil Persona
        }
        if(true === $this->get('security.context')->isGranted('ROLE_20-4'))
        {
            $rolesPermitidos[] = 'ROLE_20-4'; //Perfil Editar Perfil Persona
        }
        if(true === $this->get('security.context')->isGranted('ROLE_20-8'))
        {
            $rolesPermitidos[] = 'ROLE_20-8'; //Perfil Eliminar Perfil Persona
        }
        if(true === $this->get('security.context')->isGranted('ROLE_20-9'))
        {
            $rolesPermitidos[] = 'ROLE_20-9'; //Perfil Eliminar Perfil Persona
        }
        if(true === $this->get('security.context')->isGranted('ROLE_20-1937'))
        {
            $rolesPermitidos[] = 'ROLE_20-1937'; //Perfil Editar Roles Perfil Persona
        }               

        if(true === $this->get('security.context')->isGranted('ROLE_20-7137'))
        {
            //Perfil Reseteo de Clave
            $rolesPermitidos[] = 'ROLE_20-7137'; 
        } 
        
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "1");

        return $this->render('seguridadBundle:SeguPerfilPersona:index.html.twig', array(
                'item' => $entityItemMenu,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * Finds and displays a SeguPerfilPersona entity.
     *
     */
    /**
    * Perfil Consultar Perfil Persona
    * @Secure(roles="ROLE_20-6")
    */ 
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "1");
        
        $arrayPersonaRol =  $this->retornaArrayPersonas();
        $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneById($id);
        $entity = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findOneByPersonaId($id);
        $nombrePersona = $entityPersona->getNombres() . " " . $entityPersona->getApellidos();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SeguPerfilPersona entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('seguridadBundle:SeguPerfilPersona:show.html.twig', array(
            'item' => $entityItemMenu,
            'id_persona' => $id,
            'nombre_persona' => $nombrePersona,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new SeguPerfilPersona entity.
     *
     */
    /**
    * Perfil Crear Perfil Persona
    * @Secure(roles="ROLE_20-2")
    */ 
    public function newAction()
    {
        $arrayPersonaRol =  $this->retornaArrayPersonas();
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "1");
        $entity = new SeguPerfilPersona();
        $form   = $this->createForm(new SeguPerfilPersonaType(array('idsPersonas'=>$arrayPersonaRol, 'si_edita'=>true)), $entity);
        
        return $this->render('seguridadBundle:SeguPerfilPersona:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Perfil Crear Perfil Persona
     * @Secure(roles="ROLE_20-2")
     * Documentación para el método 'asignacionMasiva'.
     *
     * Metodo que realiza la redireccion a pantalla de reasignación masiva de perfil/persona
     * @return render
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-11-2014
     */
    public function asignacionMasivaAction()
    {
        $em_seguridad    = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "2");
        
        return $this->render('seguridadBundle:SeguPerfilPersona:asignacionMasiva.html.twig', array(
            'item' => $entityItemMenu
        ));
    }

    /**
     * Creates a new SeguPerfilPersona entity.
     *
     */
    /**
    * Perfil Crear Perfil Persona
    * @Secure(roles="ROLE_20-3")
    */ 
    public function createAction()
    {
        $peticion = $this->get('request');
        $arrayPersonaRol =  $this->retornaArrayPersonas();
       
        $PerfilesSeleccionados = array();    
        $json_asignaciones = json_decode($peticion->get('perfiles_asignados'));
        $array_asignaciones = $json_asignaciones->asignaciones;
        foreach($array_asignaciones as $asignacion)
        {
            $PerfilesSeleccionados[] = $asignacion->id_perfil;
        }
        
        $entity  = new SeguPerfilPersona();
        $request = $this->getRequest();
        $form    = $this->createForm(new SeguPerfilPersonaType(array('idsPersonas'=>$arrayPersonaRol, 'si_edita'=>true)), $entity);
        $form->bind($request);
        
        $em = $this->getDoctrine()->getManager(); 
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "1"); 
            
        $personaId = $peticion->get('cmbPersonaId') ? $peticion->get('cmbPersonaId') : "";
        $empresaId = "";
        $oficinaId = "";
        if($personaId)
        {
            $InfoPersonaEmpresa = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByPersonaId($personaId);
            
            if($InfoPersonaEmpresa && count($InfoPersonaEmpresa)>0)
            {
                $empresaId = ($InfoPersonaEmpresa[0]->getEmpresaRolId()->getEmpresaCod() ? $InfoPersonaEmpresa[0]->getEmpresaRolId()->getEmpresaCod()->getId() : "");
                $oficinaId = ($InfoPersonaEmpresa[0]->getOficinaId() ? $InfoPersonaEmpresa[0]->getOficinaId()->getId() : "");
            }
        }  
            
        if ($form->isValid() && $PerfilesSeleccionados && $personaId && $empresaId && $oficinaId) {
            $entityPerfiles = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findByPersonaId($personaId);
            $arrayPerfiles = null;
            if($entityPerfiles && count($entityPerfiles)>0)
            {
                foreach($entityPerfiles as $key => $entityPERFIL)
                {
                    $arrayPerfiles[] = $entityPERFIL->getPerfilId()->getId();
                }
            }
            
            foreach($PerfilesSeleccionados as $key => $value)
            {
                if(!$arrayPerfiles || ($arrayPerfiles && !in_array($value, $arrayPerfiles)))
                {
                    $ObjSistPerfil = $em_seguridad->getRepository('schemaBundle:SistPerfil')->findOneById($value);

                    $entityGuardo  = new SeguPerfilPersona();
                    $entityGuardo->setPersonaId($personaId);
                    $entityGuardo->setEmpresaId($empresaId);
                    $entityGuardo->setOficinaId($oficinaId);
                    $entityGuardo->setPerfilId($ObjSistPerfil);
					$entityGuardo->setUsrCreacion($peticion->getSession()->get('user'));
                    $entityGuardo->setFeCreacion(new \DateTime('now'));
                    $entityGuardo->setIpCreacion($peticion->getClientIp());
                    //$entityGuardo->setIpCreacion("192.168.240.11");

                    $em_seguridad->persist($entityGuardo);
                    $em_seguridad->flush();
                }
            }
            
            return $this->redirect($this->generateUrl('seguperfilpersona_edit', array('id' => $personaId)));
            //return $this->redirect($this->generateUrl('seguperfilpersona_show', array('id' => $entity->getId())));
        }

        return $this->render('seguridadBundle:SeguPerfilPersona:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }	
    
    
    /**
     * @Secure(roles="ROLE_20-3")
     * Documentación para el método 'createAsignacionMasiva'.
     *
     * Metodo que realiza la creacion masiva de perfil/persona
     * @return render
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 13-11-2014
     */
    public function createAsignacionMasivaAction()
    {
        $peticion               = $this->get('request');
        $json_asignaciones      = json_decode($peticion->get('perfiles_asignados'));
        $array_asignaciones     = $json_asignaciones->asignaciones;
        $json_empleados         = json_decode($peticion->get('empleados_seleccionados'));
        $array_empleados        = $json_empleados->empleados;

        $em             = $this->getDoctrine()->getManager();
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "3");

        foreach($array_empleados as $empleado)
        {
            $personaId = $empleado->id_empleado;
            $empresaId = "";
            $oficinaId = "";
            if($personaId)
            {
                $InfoPersonaEmpresa = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByPersonaId($personaId);

                if($InfoPersonaEmpresa && count($InfoPersonaEmpresa) > 0)
                {
                    $empresaId = ($InfoPersonaEmpresa[0]->getEmpresaRolId()->getEmpresaCod() ? $InfoPersonaEmpresa[0]->getEmpresaRolId()->getEmpresaCod()->getId() : "");
                    $oficinaId = ($InfoPersonaEmpresa[0]->getOficinaId() ? $InfoPersonaEmpresa[0]->getOficinaId()->getId() : "");
                }
            }

            if($array_asignaciones && $personaId && $empresaId && $oficinaId)
            {
                //se eliminan todos los perfiles anteriores en caso de tenerlos
                $entityPerfiles = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findByPersonaId($personaId);
                if($entityPerfiles && count($entityPerfiles)>0)
                {
                    foreach($entityPerfiles as $key => $entityPERFIL)
                    {                    
                        if($entityPERFIL)
                        {
                            $em_seguridad->remove($entityPERFIL);
                            $em_seguridad->flush();
                        }
                    }
                }
                //se guardan todos los nuevos perfiles/persona a cada usuario
                foreach($array_asignaciones as $key => $asignacion)
                {
                    $ObjSistPerfil = $em_seguridad->getRepository('schemaBundle:SistPerfil')->findOneById($asignacion->id_perfil);
                    $entityGuardo = new SeguPerfilPersona();
                    $entityGuardo->setPersonaId($personaId);
                    $entityGuardo->setEmpresaId($empresaId);
                    $entityGuardo->setOficinaId($oficinaId);
                    $entityGuardo->setPerfilId($ObjSistPerfil);
                    $entityGuardo->setUsrCreacion($peticion->getSession()->get('user'));
                    $entityGuardo->setFeCreacion(new \DateTime('now'));
                    $entityGuardo->setIpCreacion($peticion->getClientIp());
                    $em_seguridad->persist($entityGuardo);
                    $em_seguridad->flush();
                }
            }
            
        }

        return $this->render('seguridadBundle:SeguPerfilPersona:index.html.twig', array(
                'item' => $entityItemMenu
        ));
    }

    /**
     * @Secure(roles="ROLE_20-3")
     * 
     * Documentación para el método 'agregarPerfilesPersonaAjax'.
     *
     * agrega registros de perfiles seleccionados en la edición del perfil/persona
     * @param ajax personaId y perfilesId
     * @return response
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-09-2015 - Se cambia el método para que obtenga el 'empresaId' del usuario seleccionado por GET
     *                           y obtener el 'oficinaId' por medio del método 'findPersonaEmpresaRolByParams'. Este 
     *                           cambio ayuda para que la asignación de perfiles sea multiempresa.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-11-2014
     */
    public function agregarPerfilesPersonaAjaxAction()
    {
        $peticion       = $this->get('request');
        $em             = $this->getDoctrine()->getManager();
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $personaId      = $peticion->get('personaId') ? $peticion->get('personaId') : '';
        $perfilesId     = $peticion->get('perfilesId') ? $peticion->get('perfilesId') : '';
        $empresaId      = $peticion->get('intIdEmpresa') ? $peticion->get('intIdEmpresa') : '';
        $oficinaId      = "";
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("No se pudieron asignar los perfiles de manera correcta, favor notificar a sistemas.");
        $em_seguridad->getConnection()->beginTransaction();
        
        try 
        {
            if($personaId)
            {
                $strDescripcionRol = 'Empleado';
                $arrayEstados      = array('Activo');
                
                $InfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findPersonaEmpresaRolByParams($personaId, $empresaId, $arrayEstados, $strDescripcionRol);

                if(!is_object($InfoPersonaEmpresaRol))
                {
                    $strDescripcionRol     = 'Personal Externo';
                    $InfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->findPersonaEmpresaRolByParams($personaId, $empresaId, $arrayEstados, $strDescripcionRol);
                }

                if($InfoPersonaEmpresaRol)
                {
                    $objOficina = $InfoPersonaEmpresaRol->getOficinaId();
                    
                    if( $objOficina )
                    {
                        $oficinaId = $objOficina->getId();
                    }
                }
            }
            if($personaId && $empresaId && $oficinaId)
            {
                $entityPerfiles = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->getPerfilesAsginados("",$personaId,"","");
                $arrayPerfiles = array();
                if($entityPerfiles && count($entityPerfiles)>0)
                {
                    foreach($entityPerfiles as $key => $entityPerfil)
                    {
                        $arrayPerfiles[] = $entityPerfil->getPerfilId()->getId();
                    }
                }

                $array_valor = explode("|", $perfilesId);
                foreach($array_valor as $idPerfil):
                    if(!$arrayPerfiles || ($arrayPerfiles && !in_array($idPerfil, $arrayPerfiles)))
                    {
                        $ObjSistPerfil = $em_seguridad->getRepository('schemaBundle:SistPerfil')->findOneById($idPerfil);
                        $entityGuardo = new SeguPerfilPersona();
                        $entityGuardo->setPersonaId($personaId);
                        $entityGuardo->setEmpresaId($empresaId);
                        $entityGuardo->setOficinaId($oficinaId);
                        $entityGuardo->setPerfilId($ObjSistPerfil);
                        $entityGuardo->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityGuardo->setFeCreacion(new \DateTime('now'));
                        $entityGuardo->setIpCreacion($peticion->getClientIp());
                        $em_seguridad->persist($entityGuardo);
                        $em_seguridad->flush();
                    }
                endforeach;
                
                //se agrega eliminacion de registro en tabla SeguMenuPersona para cargar menus y submenus actualizados
                $menuPersona = $em_seguridad->getRepository('schemaBundle:SeguMenuPersona')->findOneByPersonaId($personaId);

                if($menuPersona && count($menuPersona) > 0)
                {
                    $em_seguridad->remove($menuPersona);
                    $em_seguridad->flush();
                }
                
                $em_seguridad->getConnection()->commit();
                $respuesta->setContent("Perfiles asignados Exitosamente");
            }
            else
            {
                $em_seguridad->getConnection()->rollback();
                $respuesta->setContent("No se pudieron asignar los perfiles de manera correcta, favor notificar a sistemas.");
            }
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt
            $em_seguridad->getConnection()->rollback();
            $em_seguridad->getConnection()->close();
            $respuesta->setContent("No se pudieron asignar los perfiles de manera correcta, favor notificar a sistemas.");
        }
        return $respuesta;
            
    }

    
    /**
     * @Secure(roles="ROLE_20-4")
     *
     * Documentación para el método 'editAction'.
     *
     * Muestra la pantalla para editar los perfiles asignados a una persona.
     * 
     * @param  integer $id           Id de la persona seleccionada tomado de la tabla InfoPersona
     * @param  integer $intIdEmpresa Id de la empresa de la persona seleccionada tomado de la tabla InfoEmpresaGrupo
     * @return response
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 23-09-2015 - Se cambia que el método permita presentar la información al personal nuevo que no 
     *                           le han asignado perfiles anteriormente.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-09-2015 - Se cambia el método para que reciba por GET el id de la empresa a la que pertenece 
     *                           el usuario al cual se le van a cambiar los perfiles asignados.
     * 
     * @version 1.0 Versión Inicial
     */ 
    public function editAction($id, $intIdEmpresa)
    {        
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "1");
        
        $arrayPersonaRol =  $this->retornaArrayPersonas();
        $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneById($id);
        $nombrePersona = $entityPersona->getNombres() . " " . $entityPersona->getApellidos();
        $entityPerfiles = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findByPersonaId($id);
        
        $arrayPerfil = null;
        $arrayJs = "";
        if($entityPerfiles)
        {
            foreach($entityPerfiles as $key => $value)
            {
                $arrayPerfil[$value->getPerfilId()->getId()] = $value->getPerfilId()->getNombrePerfil();
                $arrayJs .= 'array_js['. $value->getPerfilId()->getId() .'] = "'. $value->getPerfilId()->getNombrePerfil() .'"; ';
            }
        }

        return $this->render('seguridadBundle:SeguPerfilPersona:edit.html.twig', array(
                                                                                            'item'            => $entityItemMenu,
                                                                                            'nombre_persona'  => $nombrePersona,
                                                                                            'arrayPerfiles'   => $arrayPerfil,
                                                                                            'arrayPerfilesJs' => $arrayJs,
                                                                                            'intIdEmpresa'    => $intIdEmpresa,
                                                                                            'intIdPersona'    => $id
                                                                                       )
                            );
    }

    /**
     * Edits an existing SeguPerfilPersona entity.
     *
     */
    /**
    * @Secure(roles="ROLE_20-5")
    */ 
    public function updateAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("20", "1");
        
        $arrayPersonaRol =  $this->retornaArrayPersonas();
        $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneById($id);
        $nombrePersona = $entityPersona->getNombres() . " " . $entityPersona->getApellidos();
        $entityPerfiles = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findByPersonaId($id);
        $entity = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findOneByPersonaId($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SeguPerfilPersona entity.');
        }
        $editForm   = $this->createForm(new SeguPerfilPersonaType(array('idsPersonas'=>$arrayPersonaRol, 'si_edita'=>false)), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
            $InfoPersonaEmpresa = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByPersonaId($id);        
            if(count($InfoPersonaEmpresa)>0)
            {
                $empresaId = $InfoPersonaEmpresa[0]->getEmpresaRolId()->getEmpresaCod()->getId();
                $oficinaId = $InfoPersonaEmpresa[0]->getOficinaId()->getId(); 
            }
            else
                $empresaId = 'TN'; $oficinaId=1;
                
            $PerfilesSeleccionados = array();    
            $json_asignaciones = json_decode($peticion->get('perfiles_asignados'));
            $array_asignaciones = $json_asignaciones->asignaciones;
            foreach($array_asignaciones as $asignacion)
            {
                $PerfilesSeleccionados[] = $asignacion->id_perfil;
            }
                
            $arrayPerfil = null;
            $arrayPerfiles = null;
            if($entityPerfiles && count($entityPerfiles)>0)
            {
                foreach($entityPerfiles as $key => $entityPERFIL)
                {
                    $arrayPerfil[$entityPERFIL->getPerfilId()->getId()] = $entityPERFIL->getPerfilId()->getNombrePerfil();
                    $arrayPerfiles[] = $entityPERFIL->getPerfilId()->getId();
                    
                    if(!in_array($entityPERFIL->getPerfilId()->getId(), $PerfilesSeleccionados))
                    {
                       // echo $entityPERFIL->getPerfilId()->getNombrePerfil() . "eliminar ";
                        $em_seguridad->remove($entityPERFIL);
                        $em_seguridad->flush();
                    }
                }
            }
            
            if($PerfilesSeleccionados && count($PerfilesSeleccionados)>0)
            {
                foreach($PerfilesSeleccionados as $key => $value)
                {
                    if(!in_array($value, $arrayPerfiles))
                    {
                        //echo $value . " -- ";
                        $ObjSistPerfil = $em_seguridad->getRepository('schemaBundle:SistPerfil')->findOneById($value);
                        
                        $entityGuardo  = new SeguPerfilPersona();
                        $entityGuardo->setPersonaId($entity->getPersonaId());
                        $entityGuardo->setEmpresaId($empresaId);
                        $entityGuardo->setOficinaId($oficinaId);
                        $entityGuardo->setPerfilId($ObjSistPerfil);
						$entityGuardo->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityGuardo->setFeCreacion(new \DateTime('now'));
                        $entityGuardo->setIpCreacion($peticion->getClientIp());
                        //$entityGuardo->setIpCreacion("192.168.240.11");

                        $em_seguridad->persist($entityGuardo);
                        $em_seguridad->flush();
                    }
                }
                
                $menuPersona = $em_seguridad->getRepository('schemaBundle:SeguMenuPersona')->findOneByPersonaId($entity->getPersonaId());

		if($menuPersona && count($menuPersona)>0)
		{
		   $em_seguridad->remove($menuPersona);
		   $em_seguridad->flush();
		}

            }

            return $this->redirect($this->generateUrl('seguperfilpersona_edit', array('id' => $id)));
        }
        return $this->redirect($this->generateUrl('seguperfilpersona_edit', array('id' => $id)));
    }

    /**
     * Deletes a SeguPerfilPersona entity.
     *
     */
    /**
    * @Secure(roles="ROLE_20-8")
    */ 
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:SeguPerfilPersona')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SeguPerfilPersona entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('SeguPerfilPersona'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * @Secure(roles="ROLE_20-7")
     * 
     * Documentación para el método 'grid'.
     *
     * Metodo que obtiene registros de personas con perfiles asignados
     * @return response
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-11-2014
     */
    public function gridAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $codEmpresa         = "";
        $peticion           = $this->get('request');
        $em                 = $this->getDoctrine()->getManager();
        $queryNombre        = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $queryApellido      = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        //se agrega codigo para agregar nuevos filtros
        $queryEmpresa       = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $queryCiudad        = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $queryDepartamento  = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre             = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $apellido           = ($queryApellido != '' ? $queryApellido : $peticion->query->get('apellido'));
        //se agrega codigo para agregar nuevos filtros
        $empresa            = ($queryEmpresa != '' ? $queryEmpresa : $peticion->query->get('empresa'));
        $ciudad             = ($queryCiudad != '' ? $queryCiudad : $peticion->query->get('ciudad'));
        $departamento       = ($queryDepartamento != '' ? $queryDepartamento : $peticion->query->get('departamento'));
        if($empresa != "")
        {
            $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo' => $empresa));
            if($empresa)
            {
                $codEmpresa = $empresa->getId();
            }
        }

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');


        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:SeguPerfilPersona')
            ->generarJson($em, $nombre, $apellido, $codEmpresa, $ciudad, $departamento, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
    * #@Secure(roles="ROLE_20-9")
    */ 
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if($id != "undefined" && $id)
            {
                $entityPerfiles = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')->findByPersonaId($id);
                if($entityPerfiles && count($entityPerfiles)>0)
                {
                    foreach($entityPerfiles as $key => $entityPERFIL)
                    {                    
                        if($entityPERFIL)
                        {
                            $em_seguridad->remove($entityPERFIL);
                            $em_seguridad->flush();
                        }
                    }

                    $respuesta->setContent("Se eliminaron todos los perfiles para esta persona");
                }
                else
                {
                    $respuesta->setContent("No existen perfiles para esta persona"); 
                }
            }
            else
            {
                $respuesta->setContent("El Id Persona esta erroneo"); 
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    
    /**
     * @Secure(roles="ROLE_20-9")
     * 
     * Documentación para el método 'deletePerfilPersonaAjax'.
     *
     * elimina registros de perfiles seleccionados en la edición del perfil/persona
     * @param ajax personaId y perfilId
     * @return response
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-11-2014
     */
    public function deletePerfilPersonaAjaxAction()
    {
        $peticion       = $this->get('request');
        $personaId      = $peticion->get('personaId');
        $perfilesId     = $peticion->get('perfilesId');
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $array_valor    = explode("|",$perfilesId);
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $em_seguridad->getConnection()->beginTransaction();
        try {
            foreach($array_valor as $idPerfil):
                $entityPerfilPersona = $em_seguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                             ->findOneBy(array( "personaId" => $personaId, "perfilId"=>$idPerfil));
                if($entityPerfilPersona)
                {
                    $em_seguridad->remove($entityPerfilPersona);
                    $em_seguridad->flush();
                }
            endforeach;
            
            //se agrega eliminacion de registro en tabla SeguMenuPersona para cargar menus y submenus actualizados
            $menuPersona = $em_seguridad->getRepository('schemaBundle:SeguMenuPersona')->findOneByPersonaId($personaId);

            if($menuPersona && count($menuPersona) > 0)
            {
                $em_seguridad->remove($menuPersona);
                $em_seguridad->flush();
            }
            
            $em_seguridad->getConnection()->commit();
            $respuesta->setContent("Se eliminaron los perfil seleccionados para esta persona");
            
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt
            $em_seguridad->getConnection()->rollback();
            $em_seguridad->getConnection()->close();
            $respuesta->setContent("No se pudieron eliminar los perfiles de manera correcta, favor notificar a sistemas.");
        }
        return $respuesta;
    }

    //FUNCION QUE RETORNA LOS PERFILES INGRESADOS... RETORNA JSON
    /**
    * ##@Secure(roles="ROLE_20-27")
    */ 
    public function ajaxListPerfilesAction()
    {
        $request = $this->getRequest();
        $session  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet_seguridad');

        $PerfilesA = $em->getRepository('schemaBundle:SistPerfil')->findByEstado('Activo');
        $PerfilesM = $em->getRepository('schemaBundle:SistPerfil')->findByEstado('Modificado');		

        $Perfiles[] = $PerfilesA;
        $Perfiles[] = $PerfilesM;
        
        $i=1;
        
        if($Perfiles && count($Perfiles)>0)
        {
            foreach ($Perfiles as $perfilGlobal){
                if($perfilGlobal && count($perfilGlobal)>0)
                {
                    foreach ($perfilGlobal as $perfil){
                        if($i % 2==0)
                            $class='k-alt';
                        else
                            $class='';

                        $arreglo[]= array(
                                'id'=> $perfil->getId(),
                                'checks'=> $perfil->getNombrePerfil(),
                                'perfil'=> $perfil->getNombrePerfil(),
                                'clase'=> $class,
                                'mostrar'=> ""
                        );  
                        $i++;
                    }
                }
            }
        }

        if (empty($arreglo)){
            $arreglo[]= array(
                    'id'=> "",
                    'checks'=> "",
                    'perfil'=> "",
                    'clase'=> "",
                    'mostrar'=> "display:none"
                    );
        }
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }	
    
    /**
    * #@Secure(roles="ROLE_20-25")
    */ 
    public function gridPerfilesAction(){        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
                
        $nombre = $peticion->query->get('nombre');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistPerfil')
            ->getJsonPerfiles($nombre,"Todos",$start,$limit);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    /**
    * #@Secure(roles="ROLE_20-26")
    */ 
    public function gridAsignacionesAction($id){        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->getRequest();
        $nombre = $request->get("nombre");
        $limit = $request->get("limit");
        $start = $request->get("start");
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SeguPerfilPersona')
            ->getJsonAsignacionesPerfil($nombre, $id, $start, $limit);
             
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    //se agregaMetodo por cambio en opcion ver perfiles
    /**
    * #@Secure(roles="ROLE_20-26")
    */ 
    public function gridAsignacionesPopUpAction(){        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $id = $peticion->query->get('idPersona');
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SeguPerfilPersona')
            ->getJsonAsignacionesPerfil('', $id, '', '');
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_20-108")
     *
     * Documentación para el método 'getEmpleados'.
     *
     * Metodo utilizado para llenar combo de empleados via Ajax
     * @return response
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 17-11-2014
     */
    public function getEmpleadosAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em                 = $this->getDoctrine()->getManager();
        $peticion           = $this->get('request');
        $nombre             = $peticion->query->get('query');
        $strNombre          = $peticion->get("nombre");
        $strTipoAsignacion  = $peticion->get("tipoAsignacion");
        //se agrega codigo para agregar nuevos filtros
        $strEmpresa         = $peticion->get("empresa");
        $strCiudad          = $peticion->get("ciudad");
        $strDepartamento    = $peticion->get("departamento");
        if(!$nombre)
        {
            $nombre = $strNombre;
        }
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $intCodEmpresa = "";
        if($strEmpresa != "")
        {
            $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo' => $strEmpresa));
            if($empresa)
            {
                $intCodEmpresa = $empresa->getId();
            }
        }

        $arrayParamPerfiles = array();
        $arrayParamPerfiles["tipoAsignacion"] = $strTipoAsignacion;
        $arrayParamPerfiles["empresa"]        = $intCodEmpresa;
        $arrayParamPerfiles["ciudad"]         = $strCiudad;
        $arrayParamPerfiles["departamento"]   = $strDepartamento;
        
        $objData = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoPersona')
            ->findPersonasXTipoRol("Empleado-Perfiles", $nombre, $codEmpresa, "", $arrayParamPerfiles);

        $arreglo = array();
        $num = count($objData);
        if($objData && count($objData) > 0)
        {
            foreach($objData as $key => $entityPersona)
            {
                $nombreSinFormato = sprintf("%s", trim($entityPersona));
                $nombreConFormato = ucwords(strtolower($nombreSinFormato));

                $arreglo[] = array('id_empleado' => $entityPersona->getId(), 'nombre_empleado' => trim($nombreConFormato));
            }

            $dataF = json_encode($arreglo);
            $objJson = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
        }
        else
        {
            $objJson = '{"total":"0","encontrados":[]}';
        }

        $respuesta->setContent($objJson);
        return $respuesta;
    }

    public function retornaArrayPersonas()
    {
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        $em = $this->getDoctrine()->getManager();
        $Roles = $em_general->getRepository('schemaBundle:AdmiRol')->getRolesByTipoRol();
        $arrayPersonaRol = false;
        $arrayIdRol = null;
        if($Roles && count($Roles)>0)
        {
            foreach($Roles as $key => $valueRol)
            {
                $arrayIdRol[] = $valueRol->getId();
            }
            
            
            $personasRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaByRoles2($arrayIdRol);
            if($personasRol && count($personasRol)>0)
            {
                foreach($personasRol as $key => $valuePersonaRol)
                {            
                    $arrayPersona = false;
                    $arrayPersona["id"] = $valuePersonaRol->getId();
                    $arrayPersona["nombres"] = $valuePersonaRol->getNombres();
                    $arrayPersona["apellidos"] = $valuePersonaRol->getApellidos();
                    
                    $arrayPersonaRol[] = $arrayPersona;
                }
            }
        }
        return $arrayPersonaRol;
    }
    
    /**     
    * VerPerfilAction
    *
    * Método que obtiene los valores del perfil del id_persona enviado vía POST para ser mostrados en ventana
    *
    * @param ninguno         
    *
    * @return JSON con valores a mostrar
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 29-05-2014
    *    
    * Perfil Consultar Perfil Persona
    * @Secure(roles="ROLE_20-6")
    */
    public function verPerfilAction()
    {
    
	  $respuesta = new Response();
          $respuesta->headers->set('Content-Type', 'text/json');
          
          $em = $this->getDoctrine()->getManager("telconet");
          
          $peticion  = $this->get('request');        
	  $idPersona = $peticion->get('id');
	  $prefijo   = $peticion->get('empresa');	  	  
	  
	  $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijo);	  	  
	  
	  $obj = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->generarJsonPersonaRol($idPersona,$empresa->getId());
        
	  $respuesta->setContent($obj);
          return $respuesta;
    
    }
    /**
    * getDepartamentosByEmpresaYNombreAction
    *
    * Método que obtiene todos los departamentos por empresa
    *
    * @param ninguno         
    *
    * @return JSON con valores a mostrar
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 29-05-2014
    */
     public function getDepartamentosByEmpresaYNombreAction()
     {
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 	    	    	  	    	    
	    
	    $em = $this->getDoctrine()->getManager("telconet");
	    
	    $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";	    
	    $nombreDep    = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	    
	    if($paramEmpresa!=""){
		  
		  $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
		  if($empresa)$codEmpresa = $empresa->getId();
	    }
		    
	    $objJson = $em->getRepository('schemaBundle:AdmiDepartamento')
	    ->generarJsonDepartamentoByEmpresaYNombre($codEmpresa,$nombreDep);
	    
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;                    
    
    }
    /**
    * getRolesEmpleadosAction
    *
    * Método que obtiene los roles de empleado por Empresa
    *
    * @param ninguno         
    *
    * @return JSON con valores a mostrar
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 30-05-2014
    */
    public function getRolesEmpleadosAction()
    {
	  
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 	    	    	  	    	    
	    
	    $em = $this->getDoctrine()->getManager("telconet");
	    
	    $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";	    
	    $nombreDep    = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	    
	    if($paramEmpresa!=""){
		  
		  $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
		  if($empresa)$codEmpresa = $empresa->getId();
	    }
		    
	    $objJson = $em->getRepository('schemaBundle:AdmiRol')
	    ->generarJsonRolesEmpleadosXEmpresa($codEmpresa,$nombreDep);	    	    
	    
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;        
    }
    
    
/**
    * actualizarPersonaRolAction
    *
    * Método que permite actualizar o crear un registro en la tabla INFO_PERSONA_EMPRESA_ROL
    *
    * @param ninguno         
    *
    * @return JSON con confirmación de la acción realizada
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 30-05-2014
    *
    * Perfil Editar Roles Perfil Persona
    * @Secure(roles="ROLE_20-1937")
    */
    public function actualizarPersonaRolAction()
    {
	    
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	    
	  $peticion = $this->get('request');
	  
	  $em = $this->getDoctrine()->getManager("telconet");
	  
	  $idPersonaRol = $peticion->get('id')? $peticion->get('id') : "";	
	  $idPersona    = $peticion->get('idPersona')? $peticion->get('idPersona') : "";	
	  
	  $oficina      = $peticion->get('oficina') ? $peticion->get('oficina') : "";	
	  $departamento = $peticion->get('departamento') ? $peticion->get('departamento') : "";	
	  $empresaRol   = $peticion->get('rol') ? $peticion->get('rol') : "";	
	  
	  $em->getConnection()->beginTransaction();	  	  	  
	  
	  try{
	  
		// Se guardara un registro nuevo
		if($idPersonaRol == "N/A")
		{
		
		      $infoPersonaRol = new InfoPersonaEmpresaRol();
		      
		      $infoPersona = $em->getRepository('schemaBundle:InfoPersona')->find($idPersona);		      		      	      
		      
		      $infoPersonaRol->setPersonaId($infoPersona);
		      
		      $infoEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->find($empresaRol);
		      
		      $infoPersonaRol->setEmpresaRolId($infoEmpresaRol);
		      
		      $infoOficina = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficina);
		      
		      $infoPersonaRol->setOficinaId($infoOficina);
		      
		      $infoPersonaRol->setDepartamentoId($departamento);
		      
		      $infoPersonaRol->setEstado('Activo');
		      $infoPersonaRol->setUsrCreacion($peticion->getSession()->get('user'));
		      $infoPersonaRol->setFeCreacion(new \DateTime('now'));
		      $infoPersonaRol->setIpCreacion($peticion->getClientIp());
				      				      
		}
		else // Se actualizara registro existente
		{
		
		      $infoPersonaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPersonaRol);
		      
		      if($empresaRol){
			    $infoEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->find($empresaRol);		      
			    $infoPersonaRol->setEmpresaRolId($infoEmpresaRol);
		      }
		      
		      if($departamento){
			    $infoPersonaRol->setDepartamentoId($departamento);
		      }
		      
		      if($oficina){
			    $infoOficina = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficina);		      
			    $infoPersonaRol->setOficinaId($infoOficina);
		      }
		      
		      
		      
		}
		
		$em->persist($infoPersonaRol);
		$em->flush();
		$em->getConnection()->commit();
		
		$resultado = json_encode(array('success'=>true));				
	  
	  }catch(Exception $e){
		$resultado = json_encode(array('success'=>false));
	  }
	  	  	  
	  $respuesta->setContent($resultado);
	    
	  return $respuesta;
            
    }
    
    /**
    * eliminarRolAction
    *
    * Método que cambio el estado a Eliminado del registro enviado
    *
    * @param ninguno         
    *
    * @return Mensaje de confirmacion de la accion realizada
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 30-05-2014
    * 
    * Perfil Editar Roles Perfil Persona
    * @Secure(roles="ROLE_20-1937")
    */
    public function eliminarRolAction(){
    
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	    
	  $peticion = $this->get('request');
	  
	  $em = $this->getDoctrine()->getManager("telconet");
	  
	  $idPersonaRol = $peticion->get('id')? $peticion->get('id') : "";
	  
	  $em->getConnection()->beginTransaction();
	  
	  try{
	  		
		if($idPersonaRol!='')
		{		
		      $infoPersonaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPersonaRol);
		      $infoPersonaRol->setEstado('Eliminado');
		      $em->persist($infoPersonaRol);
		      $em->flush();		      				      				      
		      $em->getConnection()->commit();
		}				      		      		
				
		$resultado = json_encode(array('success'=>true));
	  
	  }catch(Exception $e){
		$resultado = json_encode(array('success'=>false));  
	  }
	  	  	  
	  $respuesta->setContent($resultado);
	    
	  return $respuesta;
                
    }
    
     
    /**
    * gridPerfilesPersonaAction
    *
    * Método que muestra todos los perfiles de una persona
    *
    * @param ninguno         
    *
    * @return JSON con informacion a mostrar en la tabla
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 30-05-2014
    *    
    * Perfil Consultar Perfil Persona
    * @Secure(roles="ROLE_20-6")
    */
    public function gridPerfilesPersonaAction(){
    
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	    
	  $peticion = $this->get('request');
	  
	  $em = $this->getDoctrine()->getManager("telconet");
	  
	  $idPersonaRol = $peticion->get('id')? $peticion->get('id') : "";		  
	  
	  $objJson = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
	    ->generarJsonPerfilesPersona($idPersonaRol);
	    
	  $respuesta->setContent($objJson);
	    
	  return $respuesta; 
                
    }
}