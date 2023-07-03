<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Form\AdmiCaracteristicaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiCaracteristicaController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_32-1")
    */
    public function indexAction()
    {
    
    $rolesPermitidos = array();
if (true === $this->get('security.context')->isGranted('ROLE_32-6'))
        {
$rolesPermitidos[] = 'ROLE_32-6';
}
if (true === $this->get('security.context')->isGranted('ROLE_32-4'))
        {
$rolesPermitidos[] = 'ROLE_32-4';
}
if (true === $this->get('security.context')->isGranted('ROLE_32-8'))
        {
$rolesPermitidos[] = 'ROLE_32-8';
}
if (true === $this->get('security.context')->isGranted('ROLE_32-9'))
        {
$rolesPermitidos[] = 'ROLE_32-9';
}

        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("32", "1");

        $entities = $em->getRepository('schemaBundle:AdmiCaracteristica')->findAll();

        return $this->render('administracionBundle:AdmiCaracteristica:index.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entities,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * @Secure(roles="ROLE_32-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información de una característica guardada
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se modifica para que cuando el tipo de ingreso es 'Seleccionable' presente las opciones relacionadas a esa 
     *                           característica.
     */
    public function showAction($id)
    {
        $objRequest        = $this->get('request');
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $entityItemMenu    = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("32", "1");
        $strTipoIngreso    = '';
        $intIdParametroCab = 0;

        if (null == $objAdmiCaracteristica = $emComercial->find('schemaBundle:AdmiCaracteristica', $id))
        {
            throw new NotFoundHttpException('No existe el AdmiCaracteristica que se quiere mostrar');
        }
        
        if( $objAdmiCaracteristica->getTipoIngreso() == 'N' )
        {
            $strTipoIngreso = 'Numero';
        }
        elseif( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
        {
            $strTipoIngreso = 'Seleccionable';
        
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array( 'descripcion' => 'PROD_'.$objAdmiCaracteristica->getDescripcionCaracteristica(),
                                                             'estado'      => $objAdmiCaracteristica->getEstado() ) );
        
            if( $objParametroCab )
            {
                $intIdParametroCab = $objParametroCab->getId();
            }
        }
        elseif( $objAdmiCaracteristica->getTipoIngreso() == 'O' )
        {
            $strTipoIngreso = 'Opcion (Si/No)';
        }
        elseif( $objAdmiCaracteristica->getTipoIngreso() == 'T' )
        {
            $strTipoIngreso = 'Texto';
        }

        return $this->render('administracionBundle:AdmiCaracteristica:show.html.twig', array(
                                                                                                'item'              => $entityItemMenu,
                                                                                                'caracteristica'    => $objAdmiCaracteristica,
                                                                                                'flag'              => $objRequest->get('flag'),
                                                                                                'strTipoIngreso'    => $strTipoIngreso,
                                                                                                'intIdParametroCab' => $intIdParametroCab
                                                                                            ));
    }
    
    /**
    * @Secure(roles="ROLE_32-2")
    */
    public function newAction()
    {
        $entity = new AdmiCaracteristica();
        $form   = $this->createForm(new AdmiCaracteristicaType(), $entity);
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("32", "1");

        return $this->render('administracionBundle:AdmiCaracteristica:new.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_32-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda la característica ingresada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se modifica que si el tipo de ingreso es 'Seleccionable' deben guardarse opciones en la 'ADMI_PARAMETRO_CAB' y 
     *                           'ADMI_PARAMETRO_DET' para esa característica.
     */
    public function createAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strUserSession         = $objSession->get('user');
        $datetimeActual         = new \DateTime('now');
        $strEstadoActivo        = 'Activo';
        $strIpCreacion          = $objRequest->getClientIp();
        $emGeneral              = $this->get('doctrine')->getManager('telconet_general');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');		
        $entityItemMenu         = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("32", "1");		
        $objAdmiCaracteristica  = new AdmiCaracteristica();
        $boolError              = false;
        
        $form = $this->createForm(new AdmiCaracteristicaType(), $objAdmiCaracteristica);        
        
        $emComercial->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();

        try
        {
            $arrayParametrosCaracteristicas = $objRequest->get('admicaracteristica');
            $strDescripcionCaracteristica   = $arrayParametrosCaracteristicas['descripcionCaracteristica'];
            $strTipoIngreso                 = $arrayParametrosCaracteristicas['tipoIngreso'];
            $strTipo                        = $arrayParametrosCaracteristicas['tipo'];
            
            $objAdmiCaracteristica->setDescripcionCaracteristica($strDescripcionCaracteristica);
            $objAdmiCaracteristica->setTipoIngreso($strTipoIngreso);
            $objAdmiCaracteristica->setTipo($strTipo);
            $objAdmiCaracteristica->setEstado($strEstadoActivo);
            $objAdmiCaracteristica->setFeCreacion($datetimeActual);
            $objAdmiCaracteristica->setUsrCreacion($strUserSession);
            $objAdmiCaracteristica->setFeUltMod($datetimeActual);
            $objAdmiCaracteristica->setUsrUltMod($strUserSession);

            $emComercial->persist($objAdmiCaracteristica);
            $emComercial->flush();

            if( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
            {
                $strNombreParametro = 'PROD_'.$strDescripcionCaracteristica;

                $objAdmiParametroCab = new AdmiParametroCab();
                $objAdmiParametroCab->setDescripcion($strNombreParametro);
                $objAdmiParametroCab->setEstado($strEstadoActivo);
                $objAdmiParametroCab->setFeCreacion($datetimeActual);
                $objAdmiParametroCab->setFeUltMod($datetimeActual);
                $objAdmiParametroCab->setIpCreacion($strIpCreacion);
                $objAdmiParametroCab->setIpUltMod($strIpCreacion);
                $objAdmiParametroCab->setModulo('COMERCIAL');
                $objAdmiParametroCab->setNombreParametro($strNombreParametro);
                $objAdmiParametroCab->setUsrCreacion($strUserSession);
                $objAdmiParametroCab->setUsrUltMod($strUserSession);

                $emGeneral->persist($objAdmiParametroCab);
                $emGeneral->flush();

                $jsonOpciones = json_decode($objRequest->get('opciones'));
                $objOpciones  = $jsonOpciones->opciones;

                foreach($objOpciones as $objOpcion)
                {
                    $objAdmiParametroDet = new AdmiParametroDet();
                    $objAdmiParametroDet->setDescripcion($strNombreParametro);
                    $objAdmiParametroDet->setEstado($strEstadoActivo);
                    $objAdmiParametroDet->setFeCreacion($datetimeActual);
                    $objAdmiParametroDet->setFeUltMod($datetimeActual);
                    $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                    $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                    $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
                    $objAdmiParametroDet->setUsrCreacion($strUserSession);
                    $objAdmiParametroDet->setUsrUltMod($strUserSession);
                    $objAdmiParametroDet->setValor1($objOpcion->valorParametro);

                    $emGeneral->persist($objAdmiParametroDet);
                    $emGeneral->flush();
                }//foreach($objOpciones as $objOpcion)
            }

            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
            }
        }
        catch (Exception $ex)
        {
            $boolError = true;
            
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }

            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }

        if($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        
        
        if( $boolError )
        {
            return $this->render('administracionBundle:AdmiCaracteristica:new.html.twig', array(
                                                                                                    'item'           => $entityItemMenu,
                                                                                                    'caracteristica' => $objAdmiCaracteristica,
                                                                                                    'form'           => $form->createView()
                                                                                                ));
        }
        else
        {
            return $this->redirect($this->generateUrl('com_admicaracteristica_show', array('id' => $objAdmiCaracteristica->getId())));
        }
    }
    
    /**
     * @Secure(roles="ROLE_32-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Edita la información de la característica seleccionada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se modifica para editar las opciones relacionadas a la característica de tipo de ingreso 'Seleccionable'.
     */
    public function editAction($id)
    {
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu    = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("32", "1");
        $intIdParametroCab = 0;

        if (null == $objAdmiCaracteristica = $emComercial->find('schemaBundle:AdmiCaracteristica', $id)) 
        {
            throw new NotFoundHttpException('No existe el AdmiCaracteristica que se quiere modificar');
        }
        
        if( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
        {
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array( 'descripcion' => 'PROD_'.$objAdmiCaracteristica->getDescripcionCaracteristica(),
                                                             'estado'      => 'Activo' ) );
        
            if( $objParametroCab )
            {
                $intIdParametroCab = $objParametroCab->getId();
            }
        }

        $formulario =$this->createForm(new AdmiCaracteristicaType(), $objAdmiCaracteristica);
        
        return $this->render('administracionBundle:AdmiCaracteristica:edit.html.twig', array(
                                                                                                'item'              => $entityItemMenu,
                                                                                                'edit_form'         => $formulario->createView(),
                                                                                                'caracteristica'    => $objAdmiCaracteristica,
                                                                                                'intIdParametroCab' => $intIdParametroCab
                                                                                            ));
    }
    
    /**
     * @Secure(roles="ROLE_32-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de la característica seleccionada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se modifica para editar las opciones relacionadas a la característica de tipo de ingreso 'Seleccionable'.
     */
    public function updateAction($id)
    {
        $emComercial           = $this->getDoctrine()->getManager("telconet");
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu        = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("32", "1");
        $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find($id);
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $strUserSession        = $objSession->get('user');
        $datetimeActual        = new \DateTime('now');
        $strEstadoActivo       = 'Activo';
        $strEstadoEliminado    = 'Eliminado';
        $strIpCreacion         = $objRequest->getClientIp();
        $boolError             = false;    
        $intIdParametroCabOld  = 0;
        $arrayOpcionesEditadas = array();
        

        if (!$objAdmiCaracteristica)
        {
            throw $this->createNotFoundException('No se ha encontrado caracteristica en nuestra base de datos.');
        }

        $editForm = $this->createForm(new AdmiCaracteristicaType(), $objAdmiCaracteristica);
        
        if( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
        {
            $objParametroCabOld = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array( 'descripcion' => 'PROD_'.$objAdmiCaracteristica->getDescripcionCaracteristica(),
                                                             'estado'      => 'Activo' ) );
        
            if( $objParametroCabOld )
            {
                $intIdParametroCabOld = $objParametroCabOld->getId();
            }
        }
        
        $emComercial->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $arrayParametrosCaracteristicas = $objRequest->get('admicaracteristica');
            $strDescripcionCaracteristica   = $arrayParametrosCaracteristicas['descripcionCaracteristica'];
            $strTipoIngreso                 = $arrayParametrosCaracteristicas['tipoIngreso'];
            $strTipo                        = $arrayParametrosCaracteristicas['tipo'];
            
            $objAdmiCaracteristica->setDescripcionCaracteristica($strDescripcionCaracteristica);
            $objAdmiCaracteristica->setTipoIngreso($strTipoIngreso);
            $objAdmiCaracteristica->setTipo($strTipo);
            $objAdmiCaracteristica->setFeUltMod($datetimeActual);
            $objAdmiCaracteristica->setUsrUltMod($strUserSession);

            $emComercial->persist($objAdmiCaracteristica);
            $emComercial->flush();
            
            if( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
            {
                $strNombreParametro         = 'PROD_'.$strDescripcionCaracteristica;
                $intIdParametroCabNew       = 0;
                $objAdmiParametroCabCurrent = null; 
                
                $objParametroCabNew = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy( array( 'descripcion' => $strNombreParametro,
                                                                    'estado'      => 'Activo' ) );
        
                if( $objParametroCabNew )
                {
                    $intIdParametroCabNew = $objParametroCabNew->getId();
                }
                
                if( $intIdParametroCabNew != $intIdParametroCabOld )
                {
                    $objAdmiParametroCab = new AdmiParametroCab();
                    $objAdmiParametroCab->setDescripcion($strNombreParametro);
                    $objAdmiParametroCab->setEstado($strEstadoActivo);
                    $objAdmiParametroCab->setFeCreacion($datetimeActual);
                    $objAdmiParametroCab->setFeUltMod($datetimeActual);
                    $objAdmiParametroCab->setIpCreacion($strIpCreacion);
                    $objAdmiParametroCab->setIpUltMod($strIpCreacion);
                    $objAdmiParametroCab->setModulo('COMERCIAL');
                    $objAdmiParametroCab->setNombreParametro($strNombreParametro);
                    $objAdmiParametroCab->setUsrCreacion($strUserSession);
                    $objAdmiParametroCab->setUsrUltMod($strUserSession);

                    $emGeneral->persist($objAdmiParametroCab);
                    $emGeneral->flush();
                    
                    $objAdmiParametroCabCurrent = $objAdmiParametroCab;
                }
                else
                {
                    $objAdmiParametroCabCurrent = $objParametroCabNew;
                }

                $jsonOpciones = json_decode($objRequest->get('opciones'));
                $objOpciones  = $jsonOpciones->opciones;

                foreach($objOpciones as $objOpcion)
                {
                    if($objOpcion->idParametroDet != "" && $objOpcion->idParametroDet > 0)
                    {
                        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->findOneById($objOpcion->idParametroDet);

                        if( $objAdmiParametroDet )
                        {
                            $arrayOpcionesEditadas[] = $objOpcion->idParametroDet;

                            $objAdmiParametroDet->setFeUltMod($datetimeActual);
                            $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                            $objAdmiParametroDet->setParametroId($objAdmiParametroCabCurrent);
                            $objAdmiParametroDet->setUsrUltMod($strUserSession);
                            $objAdmiParametroDet->setValor1($objOpcion->valorParametro);

                            $emGeneral->persist($objAdmiParametroDet);
                            $emGeneral->flush();
                        }//( $objAdmiParametroDet )
                    }
                    else
                    {
                        $objAdmiParametroDet = new AdmiParametroDet();
                        $objAdmiParametroDet->setDescripcion($strNombreParametro);
                        $objAdmiParametroDet->setEstado($strEstadoActivo);
                        $objAdmiParametroDet->setFeCreacion($datetimeActual);
                        $objAdmiParametroDet->setFeUltMod($datetimeActual);
                        $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                        $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                        $objAdmiParametroDet->setParametroId($objAdmiParametroCabCurrent);
                        $objAdmiParametroDet->setUsrCreacion($strUserSession);
                        $objAdmiParametroDet->setUsrUltMod($strUserSession);
                        $objAdmiParametroDet->setValor1($objOpcion->valorParametro);

                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();
                        
                        $arrayOpcionesEditadas[] = $objAdmiParametroDet->getId();
                    }//($objOpcion->idParametroDet != "" && $objOpcion->idParametroDet > 0)
                }//foreach($objOpciones as $objOpcion)
                

                /*
                 * Bloque cambia de estado a eliminado las opciones que el usuario eliminó
                 */
                $objParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->findBy( array( 'parametroId' => $objAdmiParametroCabCurrent,
                                                               'estado'      => 'Activo' ) );
                
                if( $objParametrosDet )
                {
                    foreach( $objParametrosDet as $objParametro )
                    {
                        if( !in_array($objParametro->getId(), $arrayOpcionesEditadas) )
                        {
                            $objParametro->setEstado($strEstadoEliminado);
                            $objParametro->setFeUltMod($datetimeActual);
                            $objParametro->setIpUltMod($strIpCreacion);
                            $objParametro->setUsrUltMod($strUserSession);

                            $emGeneral->persist($objParametro);
                            $emGeneral->flush();
                        }//( !in_array($objParametro->getId(), $arrayOpcionesEditadas) )
                    }//( $objParametrosDet as $objParametro )
                }//( $objParametrosDet )
                /*
                 * Fin Bloque cambia de estado a eliminado las opciones que el usuario eliminó
                 */
            }//( $objAdmiCaracteristica->getTipoIngreso() == 'S' )

            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
            }
        }
        catch (Exception $ex)
        {
            $boolError = true;
            
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }

            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }

        if($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        
        
        if( $boolError )
        {
            return $this->render('administracionBundle:AdmiCaracteristica:edit.html.twig', array(
                                                                                                    'item'              => $entityItemMenu,
                                                                                                    'caracteristica'    => $objAdmiCaracteristica,
                                                                                                    'edit_form'         => $editForm->createView(),
                                                                                                    'intIdParametroCab' => $intIdParametroCabOld
                                                                                                ));
        }
        else
        {
            return $this->redirect($this->generateUrl('com_admicaracteristica_show', array('id' => $id)));
        }
    }
    
    /**
     * @Secure(roles="ROLE_32-8")
     * 
     * Documentación para el método 'deleteAction'.
     *
     * Cambia de estado a 'Eliminado' de una caracteristica seleccionada
     *
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se modifica para eliminar las opciones relacionadas a la característica de tipo de ingreso 'Seleccionable'.
     */
    public function deleteAction($id)
    {
        $objRequest         = $this->getRequest();
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $datetimeActual     = new \DateTime('now');
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';
        $strIpCreacion      = $objRequest->getClientIp();
        $boolError          = false;

        $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find($id);

        if (!$objAdmiCaracteristica)
        {
            throw $this->createNotFoundException('No se encontró la característica seleccionada por el usuario.');
        }
        
        $emComercial->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $objAdmiCaracteristica->setEstado($strEstadoEliminado);
            $objAdmiCaracteristica->setFeUltMod($datetimeActual);
            $objAdmiCaracteristica->setUsrUltMod($strUserSession);
			
			$emComercial->persist($objAdmiCaracteristica);	
            $emComercial->flush();
            
            if( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
            {
                $strNombreParametro = 'PROD_'.$objAdmiCaracteristica->getDescripcionCaracteristica();
                $objParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy( array( 'descripcion' => $strNombreParametro,
                                                                    'estado'      => $strEstadoActivo ) );
        
                if( $objParametroCab )
                {
                    $objParametroCab->setEstado($strEstadoEliminado);
                    $objParametroCab->setFeUltMod($datetimeActual);
                    $objParametroCab->setIpUltMod($strIpCreacion);
                    $objParametroCab->setUsrUltMod($strUserSession);

                    $emGeneral->persist($objParametroCab);
                    $emGeneral->flush();
                    
                    
                    $objParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findBy( array( 'parametroId' => $objParametroCab,
                                                                   'estado'      => $strEstadoActivo ) );

                    if( $objParametrosDet )
                    {
                        foreach( $objParametrosDet as $objParametro )
                        {
                            $objParametro->setEstado($strEstadoEliminado);
                            $objParametro->setFeUltMod($datetimeActual);
                            $objParametro->setIpUltMod($strIpCreacion);
                            $objParametro->setUsrUltMod($strUserSession);

                            $emGeneral->persist($objParametro);
                            $emGeneral->flush();
                        }//( $objParametrosDet as $objParametro )
                    }//( $objParametrosDet )
                }//( $objParametroCab )
            }//( $objAdmiCaracteristica->getTipoIngreso() == 'S' )

            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
            }
        }
        catch (Exception $ex)
        {
            $boolError = true;
                    
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }

            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }

        if($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        
        
        if( $boolError )
        {
            return $this->redirect($this->generateUrl('com_admicaracteristica_show', array('id' => $id)));
        }
        else
        {
            return $this->redirect($this->generateUrl('com_admicaracteristica'));
        }
    }

    /**
     * @Secure(roles="ROLE_32-9")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Cambia de estado a 'Eliminado' de una caracteristica seleccionada
     *
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-01-2016 - Se modifica para eliminar las opciones relacionadas a la característica de tipo de ingreso 'Seleccionable'.
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $objRequest         = $this->getRequest();
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $datetimeActual     = new \DateTime('now');
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';
        $strIpCreacion      = $objRequest->getClientIp();
        $boolError          = false;
        $strParametro       = $objRequest->get('param');
        
        $arrayCaracteristicas = explode("|",$strParametro);
        
        $emComercial->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            foreach($arrayCaracteristicas as $id)
            {
                if (null == $objAdmiCaracteristica = $emComercial->find('schemaBundle:AdmiCaracteristica', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($objAdmiCaracteristica->getEstado()) != "eliminado")
                    {
                        $objAdmiCaracteristica->setEstado($strEstadoEliminado);
                        $objAdmiCaracteristica->setFeUltMod($datetimeActual);
                        $objAdmiCaracteristica->setUsrUltMod($strUserSession);

                        $emComercial->persist($objAdmiCaracteristica);
                        $emComercial->flush();
                        
                        
                        if( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
                        {
                            $strNombreParametro = 'PROD_'.$objAdmiCaracteristica->getDescripcionCaracteristica();
                            $objParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy( array( 'descripcion' => $strNombreParametro,
                                                                                'estado'      => $strEstadoActivo ) );

                            if( $objParametroCab )
                            {
                                $objParametroCab->setEstado($strEstadoEliminado);
                                $objParametroCab->setFeUltMod($datetimeActual);
                                $objParametroCab->setIpUltMod($strIpCreacion);
                                $objParametroCab->setUsrUltMod($strUserSession);

                                $emGeneral->persist($objParametroCab);
                                $emGeneral->flush();


                                $objParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->findBy( array( 'parametroId' => $objParametroCab,
                                                                               'estado'      => $strEstadoActivo ) );

                                if( $objParametrosDet )
                                {
                                    foreach( $objParametrosDet as $objParametro )
                                    {
                                        $objParametro->setEstado($strEstadoEliminado);
                                        $objParametro->setFeUltMod($datetimeActual);
                                        $objParametro->setIpUltMod($strIpCreacion);
                                        $objParametro->setUsrUltMod($strUserSession);

                                        $emGeneral->persist($objParametro);
                                        $emGeneral->flush();
                                    }//( $objParametrosDet as $objParametro )
                                }//( $objParametrosDet )
                            }//( $objParametroCab )
                        }//( $objAdmiCaracteristica->getTipoIngreso() == 'S' )
                    }//(strtolower($objAdmiCaracteristica->getEstado()) != "eliminado")

                    $respuesta->setContent("Se elimino la entidad");
                }//(null == $objAdmiCaracteristica = $emComercial->find('schemaBundle:AdmiCaracteristica', $id))
            }//foreach($arrayCaracteristicas as $id)
            
            
            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
            }
        }
        catch (Exception $ex)
        {
            $boolError = true;
                    
            error_log($ex->getMessage());

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }

            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
        }//try

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }

        if($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_32-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiCaracteristica')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    /**
     * Documentación para el método 'getOpcionesSeleccionableAction'.
     *
     * Retorna las opciones guardadas en la tabla 'ADMI_PARAMETRO_DET' para la característica de tipo 'S' seleccionada
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 09-01-2016
     */
    public function getOpcionesSeleccionableAction()
    {
        $response            = new JsonResponse();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intTotal            = 0;
        $arrayParametros     = array();
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");
        $intIdParametroCab   = $objRequest->get('idParametroCab') ? trim($objRequest->get('idParametroCab')) : 0;
        
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneById( $intIdParametroCab );
        
        if( $objParametroCab )
        {
            $objResultados = $emGeneral->getRepository( 'schemaBundle:AdmiParametroDet' )
                                       ->findBy( array( 'estado'      => $objParametroCab->getEstado(), 
                                                        'parametroId' => $objParametroCab ) );

            if($objResultados)
            {
                foreach( $objResultados as $objParametro )
                {
                    $item                   = array();
                    $item['idParametroCab'] = $intIdParametroCab;
                    $item['idParametroDet'] = $objParametro->getId();
                    $item['valorParametro'] = $objParametro->getValor1();

                    $arrayParametros[] = $item;

                    $intTotal++;
                }//foreach($objResultados as $objParametro)
            }//($objResultados)
        } 
        
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayParametros) );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'verificarCaracteristicaAction'.
     *
     * Verifica que las caracteristicas no hayan sido ingresadas anteriormente.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 09-01-2016
     */
    public function verificarCaracteristicaAction()
    {
        $response            = new Response();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strMensaje          = 'OK';
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $strCaracteristica   = $objRequest->request->get('strCaracteristica') ? $objRequest->request->get('strCaracteristica') : '';
        $strTipoIngreso      = $objRequest->request->get('strTipoIngreso') ? $objRequest->request->get('strTipoIngreso') : '';
        $strTipo             = $objRequest->request->get('strTipo') ? $objRequest->request->get('strTipo') : '';
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdCaracteristica = $objRequest->request->get('intIdCaracteristica') ? $objRequest->request->get('intIdCaracteristica') : 0;
        
        if( $intIdEmpresa != 0 )
        {
            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristica,
                                                                     'tipoIngreso'               => $strTipoIngreso,
                                                                     'tipo'                      => $strTipo,
                                                                     'estado'                    => 'Activo' ) );
            
            if( $objAdmiCaracteristica )
            {
                if( $intIdCaracteristica != 0)
                {
                    if( $intIdCaracteristica != $objAdmiCaracteristica->getId() )
                    {
                        $strMensaje = 'Caracteristica ya fue ingresada anteriormente';
                    }
                }
                else
                {
                    $strMensaje = 'Caracteristica ya fue ingresada anteriormente';
                }
            }//( $objAdmiCaracteristica )
        }//if( $intIdEmpresa != 0 )
        else
        {
            $strMensaje = 'No tiene existe empresa en sessión';
        }
        
        $response->setContent( $strMensaje );
        
        return $response;
    }

    /**
     * getValoresCaracteristicasAction
     * 
     * Función que obtiene todos los valores de las características asociadas a un parámetro
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-11-2017 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 05-05-2020 Se modifica función para permitir solo las velocidades establecidas en algunos productos Small Business
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 08-03-2022 Se modifica función para permitir solo las velocidades establecidas para producto
     *                         que se encuentra en el parametro
     * 
     */
    public function getValoresCaracteristicasAction()
    {
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strCodEmpresa              = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 10;
        $intIdProducto              = $objRequest->get('intIdProducto') ? $objRequest->get('intIdProducto') : 0;
        $strNombreTecnicoProducto   = $objRequest->get('strNombreTecnicoProducto') ? $objRequest->get('strNombreTecnicoProducto') : "";
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $strNombreTecnicoProdBusq   = "";
        $arrayRegistros             = array();
        
        $strNombreParametro         = 'DESCRIPCION_CARACT_VELOCIDAD_X_NOMBRE_TECNICO';
        $strCodEmpresaParam         = '';
        
        //Consultamos en la tabla de parametro si el producto tiene otras velocidades
        $objProductoParam = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
        if(is_object($objProductoParam))
        {
            $strNombreProducto = $objProductoParam->getDescripcionProducto();
        }
        
        $arrayAdmiParametroProducto = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAM_CARACT_VELOCIDAD_X_PRODUCTO',
                                                           '',
                                                           '',
                                                           '',
                                                           $strNombreProducto,
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           $strCodEmpresa
                                                          );
        if (isset($arrayAdmiParametroProducto['valor2']) && !empty($arrayAdmiParametroProducto['valor2']))
        {
            $strNombreParametro = $arrayAdmiParametroProducto['valor2'];
            $strCodEmpresaParam = $strCodEmpresa;
        }
        
        
        if(isset($intIdProducto) && !empty($intIdProducto) && $intIdProducto > 0)
        {
            if(!isset($strNombreTecnicoProducto) || empty($strNombreTecnicoProducto))
            {
                $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                if(is_object($objProducto))
                {
                    $strNombreTecnicoProdBusq = $objProducto->getNombreTecnico();
                }
            }
            else
            {
                $strNombreTecnicoProdBusq = $strNombreTecnicoProducto;
            }
            
            if(isset($strNombreTecnicoProdBusq) && !empty($strNombreTecnicoProdBusq))
            {
                $arrayVerificaCaractXNombreTecnico  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMS_PRODS_TN_GPON',
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         $strNombreParametro,
                                                                         $strNombreTecnicoProdBusq,
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         $strCodEmpresa);
        
                if(isset($arrayVerificaCaractXNombreTecnico) && !empty($arrayVerificaCaractXNombreTecnico))
                {
                    $strNombreParametroVelocidad    = "PROD_".$arrayVerificaCaractXNombreTecnico["valor3"];
                    $arrayVelocidadesXNombreTecnico = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get($strNombreParametroVelocidad, '', '', '', '', '', '', '', '', 
                                                                      $strCodEmpresaParam, 'valor7');
                    $arrayVerificaProdsVelocidades  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('PARAMS_PRODS_TN_GPON',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 'PRODUCTOS_VERIFICA_VELOCIDAD',
                                                                                 $intIdProducto,
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $strCodEmpresa);
                    if(isset($arrayVelocidadesXNombreTecnico) && !empty($arrayVelocidadesXNombreTecnico))
                    {
                        foreach($arrayVelocidadesXNombreTecnico as $arrayVelocidadXNombreTecnico)
                        {
                            if(isset($arrayVerificaProdsVelocidades) && !empty($arrayVerificaProdsVelocidades))
                            {
                                $arrayVerificaVelocidadesPermitidas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->get(  'PARAMS_PRODS_TN_GPON',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
                                                                                        $intIdProducto,
                                                                                        $arrayVelocidadXNombreTecnico["valor1"],
                                                                                        '',
                                                                                        '',
                                                                                        $strCodEmpresa);
                                if(isset($arrayVerificaVelocidadesPermitidas) && !empty($arrayVerificaVelocidadesPermitidas))
                                {
                                    $arrayRegistros[] = array('valor1' => $arrayVelocidadXNombreTecnico["valor1"]);
                                }
                            }
                            else
                            {
                                $arrayRegistros[] = array('valor1' => $arrayVelocidadXNombreTecnico["valor1"]);
                            }
                        }
                    }
                }
            }
        }
        
        $objResponse = new JsonResponse();
        $objResponse->setData(array("arrayRegistros" => $arrayRegistros));
        return $objResponse;
    }
}