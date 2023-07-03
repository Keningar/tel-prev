<?php
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiNumeracion;
use telconet\schemaBundle\Entity\AdmiNumeracionHisto;
use telconet\schemaBundle\Form\AdmiNumeracionType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse; 

class AdmiNumeracionController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_33-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Muestra la información de las numeraciones guardadas para la empresa en sessión
     * 
     * @return Response 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-06-2017 - Se modifica para que pueda mostrar los secuenciales o numero de autorización dependiendo de la empresa en sessión
     */
    public function indexAction()
    {
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
        $emSeguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $objItemMenu   = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("33", "1");
        
        /**
         * Bloque que retorna los parámetros adecuados para poder mostrar los campos respectivos ingresados de la numeración
         */
        $arrayParametros = array('strMostrarSecuenciales'       => 'N',
                                 'strMostrarNumeroAutorizacion' => 'N');

        $arrayDetallesNumeracion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get('ADMI_NUMERACION', 
                                                   'ADMINISTRACION',
                                                   'NUMERACION', 
                                                   '', 
                                                   '',
                                                   '',
                                                   '', 
                                                   '', 
                                                   '', 
                                                   $strEmpresaCod);

        if ( !empty($arrayDetallesNumeracion) )
        {
            foreach ( $arrayDetallesNumeracion as $arrayDetalle )
            {
                if ( isset($arrayDetalle['descripcion']) && !empty($arrayDetalle['descripcion']) && isset($arrayDetalle['valor1']) 
                    && !empty($arrayDetalle['valor1']) )
                {
                    $arrayParametros[$arrayDetalle['descripcion']] = $arrayDetalle['valor1'];
                }// ( isset($arrayDetalle['descripcion']) && !empty($arrayDetalle['descripcion']) && isset($arrayDetalle['valor1']) 
            }//foreach ( $arrayDetallesNumeracion as $arrayDetalle )
        }// ( !empty($arrayDetallesNumeracion) )
        
        $arrayParametros['item'] = $objItemMenu;

        return $this->render('administracionBundle:AdmiNumeracion:index.html.twig', $arrayParametros);
    }
    
    /**
     * @Secure(roles="ROLE_33-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Muestra la información guardada de una numeración
     * 
     * @return Response 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para que muestre el tipo de comprobante al que hace referencia la numeración
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 27-06-2017 - Se modifica para que pueda mostrar los secuenciales o numero de autorización dependiendo de la empresa en sessión
     */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em             = $this->getDoctrine()->getManager("telconet");
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("33", "1");

        if (null == $numeracion = $em->find('schemaBundle:AdmiNumeracion', $id)) {
            throw new NotFoundHttpException('No existe el AdmiNumeracion que se quiere mostrar');
        }
        
        $nombre_empresa = "";
        if($numeracion->getEmpresaId())
        {   
            $infoEmpresaGrupo = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($numeracion->getEmpresaId());
            $nombre_empresa = $infoEmpresaGrupo ? $infoEmpresaGrupo->getNombreEmpresa() : "";
        }
        $nombre_oficina = "";
        if($numeracion->getOficinaId())
        {   
            $infoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->findOneById($numeracion->getOficinaId());
            $nombre_oficina = $infoOficinaGrupo ? $infoOficinaGrupo->getNombreOficina() : "";
        }
        
        $strTipoComprobante  = '';
        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy( array( 'descripcion' => 'TIPO_COMPROBANTES',
                                                             'estado'      => 'Activo',
                                                             'valor2'      => $numeracion->getCodigo() ) );
            
        if( $objAdmiParametroDet )
        {
            $strTipoComprobante = ucwords(strtolower($objAdmiParametroDet->getValor1()));
        }


        /**
         * Bloque que retorna los parámetros adecuados para poder mostrar los campos respectivos ingresados de la numeración
         */
        $objSession      = $peticion->getSession();
        $strEmpresaCod   = $objSession->get('idEmpresa');
        $arrayParametros = array('intMaxLengthSecuencial1'        => 0,
                                 'intMaxLengthSecuencial2'        => 0,
                                 'intMaxLengthNumeroAutorizacion' => 0,
                                 'strMostrarSecuenciales'         => 'N',
                                 'strMostrarNumeroAutorizacion'   => 'N');

        $arrayDetallesNumeracion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get('ADMI_NUMERACION', 
                                                   'ADMINISTRACION',
                                                   'NUMERACION', 
                                                   '', 
                                                   '',
                                                   '',
                                                   '', 
                                                   '', 
                                                   '', 
                                                   $strEmpresaCod);

        if ( !empty($arrayDetallesNumeracion) )
        {
            foreach ( $arrayDetallesNumeracion as $arrayDetalle )
            {
                if ( isset($arrayDetalle['descripcion']) && !empty($arrayDetalle['descripcion']) && isset($arrayDetalle['valor1']) 
                    && !empty($arrayDetalle['valor1']) )
                {
                    $arrayParametros[$arrayDetalle['descripcion']] = $arrayDetalle['valor1'];
                }// ( isset($arrayDetalle['descripcion']) && !empty($arrayDetalle['descripcion']) && isset($arrayDetalle['valor1']) 
            }//foreach ( $arrayDetallesNumeracion as $arrayDetalle )
        }// ( !empty($arrayDetallesNumeracion) )
        
        $arrayParametros['item']            = $entityItemMenu;
        $arrayParametros['numeracion']      = $numeracion;
        $arrayParametros['nombreEmpresa']   = $nombre_empresa;
        $arrayParametros['nombreOficina']   = $nombre_oficina;
        $arrayParametros['tipoComprobante'] = $strTipoComprobante;
        $arrayParametros['flag']            = $peticion->get('flag');
        
        return $this->render('administracionBundle:AdmiNumeracion:show.html.twig', $arrayParametros);
    }


    /**
     * @Secure(roles="ROLE_33-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Obtiene la configuración inicial para presentar el formulario para el ingreso de la numeración respectiva
     * 
     * @return Response 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-06-2017 - Se modifica para que pueda mostrar los secuenciales o numero de autorización dependiendo de la empresa en sessión
     */
    public function newAction()
    {
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
        $emSeguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $objItemMenu   = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("33", "1");
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();
        $strEmpresaCod = $objSession->get('idEmpresa');
        /**
         * Bloque que retorna los parámetros adecuados para poder mostrar los campos respectivos en el formulario para el ingreso de la numeración
         */
        $arrayParametros = array('intMaxLengthSecuencial1'        => 0,
                                 'intMaxLengthSecuencial2'        => 0,
                                 'intMaxLengthNumeroAutorizacion' => 0,
                                 'strMostrarSecuenciales'         => 'N',
                                 'strMostrarNumeroAutorizacion'   => 'N');

        $arrayDetallesNumeracion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get('ADMI_NUMERACION', 
                                                   'ADMINISTRACION',
                                                   'NUMERACION', 
                                                   '', 
                                                   '',
                                                   '',
                                                   '', 
                                                   '', 
                                                   '', 
                                                   $strEmpresaCod);

        if ( !empty($arrayDetallesNumeracion) )
        {
            foreach ( $arrayDetallesNumeracion as $arrayDetalle )
            {
                if ( isset($arrayDetalle['descripcion']) && !empty($arrayDetalle['descripcion']) && isset($arrayDetalle['valor1']) 
                    && !empty($arrayDetalle['valor1']) )
                {
                    $arrayParametros[$arrayDetalle['descripcion']] = $arrayDetalle['valor1'];
                }// ( isset($arrayDetalle['descripcion']) && !empty($arrayDetalle['descripcion']) && isset($arrayDetalle['valor1']) 
            }//foreach ( $arrayDetallesNumeracion as $arrayDetalle )
        }// ( !empty($arrayDetallesNumeracion) )


        $objAdmiNumeracion = new AdmiNumeracion();
        $objForm           = $this->createForm(new AdmiNumeracionType($arrayParametros), $objAdmiNumeracion);

        $arrayParametros['item']       = $objItemMenu;
        $arrayParametros['numeracion'] = $objAdmiNumeracion;
        $arrayParametros['form']       = $objForm->createView();

        return $this->render( 'administracionBundle:AdmiNumeracion:new.html.twig', $arrayParametros );
    }


    /**
     * @Secure(roles="ROLE_33-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Inserta una numeración nueva en la tabla 'ADMI_NUMERACION' con su historial respectivo en 'ADMI_NUMERACION_HISTORIAL'
     * 
     * @return Response 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para que se guarde el Historial respectivo de la Numeración ingresada
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 27-06-2017 - Se agrega el campo 'numeroAutorización' para que sea guardado en la base de datos
     */
    public function createAction()
    {        
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("33", "1");
        $strUsrSession  = $objSession->get('user');
        $datetimeActual = new \DateTime('now');
        
        $objAdmiNumeracion = new AdmiNumeracion();
        
        $form = $this->createForm(new AdmiNumeracionType(), $objAdmiNumeracion); 
        
        $emComercial->getConnection()->beginTransaction();

        try
        {
            $intIdEmpresa       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
            $intIdOficina       = $objRequest->request->get('intIdOficinaSeleccionado') ? $objRequest->request->get('intIdOficinaSeleccionado') : 0;
            $strTipoComprobante = $objRequest->request->get('strCodigoComprobanteSeleccionado') 
                                  ? $objRequest->request->get('strCodigoComprobanteSeleccionado') : 0;
            $strDescripcion     = $objRequest->request->get('descripcion') ? $objRequest->request->get('descripcion') : 0;
            $strNumeracionUno   = $objRequest->request->get('numeracionUno') ? $objRequest->request->get('numeracionUno') : 0;
            $strNumeracionDos   = $objRequest->request->get('numeracionDos') ? $objRequest->request->get('numeracionDos') : 0;
            
            $strNumeroAutorizacion = $objRequest->request->get('numeroAutorizacion') ? $objRequest->request->get('numeroAutorizacion') : null;

            $objAdmiNumeracion->setDescripcion($strDescripcion);
            $objAdmiNumeracion->setNumeracionUno($strNumeracionUno);
            $objAdmiNumeracion->setNumeracionDos($strNumeracionDos);
            $objAdmiNumeracion->setNumeroAutorizacion($strNumeroAutorizacion);
            $objAdmiNumeracion->setCodigo($strTipoComprobante);
            $objAdmiNumeracion->setSecuencia(1);
            $objAdmiNumeracion->setTabla('info_documento_cab');
            $objAdmiNumeracion->setEmpresaId($intIdEmpresa);
            $objAdmiNumeracion->setOficinaId($intIdOficina);
            $objAdmiNumeracion->setEstado('Activo');
            $objAdmiNumeracion->setProcesosAutomaticos('N');
            $objAdmiNumeracion->setFeCreacion($datetimeActual);
            $objAdmiNumeracion->setUsrCreacion($strUsrSession);
            $objAdmiNumeracion->setFeUltMod($datetimeActual);
            $objAdmiNumeracion->setUsrUltMod($strUsrSession);
            $emComercial->persist($objAdmiNumeracion);


            $objAdmiNumeracionHisto = new AdmiNumeracionHisto();
            $objAdmiNumeracionHisto->setNumeracionId($objAdmiNumeracion);
            $objAdmiNumeracionHisto->setNumeracionUno($objAdmiNumeracion->getNumeracionUno());
            $objAdmiNumeracionHisto->setNumeracionDos($objAdmiNumeracion->getNumeracionDos());
            $objAdmiNumeracionHisto->setSecuenciaInicio(1);
            $objAdmiNumeracionHisto->setNumeroAutorizacion($strNumeroAutorizacion);
            $objAdmiNumeracionHisto->setCodEstablecimiento($objAdmiNumeracion->getNumeracionUno());
            $objAdmiNumeracionHisto->setFeCreacion($datetimeActual);
            $objAdmiNumeracionHisto->setUsrCreacion($strUsrSession);
            $objAdmiNumeracionHisto->setEstado('Activo');
            $emComercial->persist($objAdmiNumeracionHisto);

            $emComercial->flush();

            $emComercial->getConnection()->commit();

            return $this->redirect($this->generateUrl('adminumeracion_show', array('id' => $objAdmiNumeracion->getId())));
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            $emComercial->getConnection()->rollback();
        }

        $emComercial->getConnection()->close();
        
        return $this->render('administracionBundle:AdmiNumeracion:new.html.twig', array(
            'item'       => $entityItemMenu,
            'numeracion' => $objAdmiNumeracion,
            'form'       => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_33-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("33", "1");

        if (null == $numeracion = $em->find('schemaBundle:AdmiNumeracion', $id)) {
            throw new NotFoundHttpException('No existe el AdmiNumeracion que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiNumeracionType(), $numeracion);
        return $this->render('administracionBundle:AdmiNumeracion:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'numeracion'   => $numeracion));
    }
    
        /**
    * @Secure(roles="ROLE_33-5")
    */
    public function updateAction($id)
    {        
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("33", "1");
        $entity = $em->getRepository('schemaBundle:AdmiNumeracion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiNumeracion entity.');
        }

        $editForm   = $this->createForm(new AdmiNumeracionType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        $peticion = $this->get('request');
        if ($editForm->isValid()) {
            
            $escogido_empresa_id = $peticion->get('escogido_empresa_id');
            $escogido_oficina_id = $peticion->get('escogido_oficina_id');
            
            $entity->setEmpresaId($escogido_empresa_id);
            $entity->setOficinaId($escogido_oficina_id);
            
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('adminumeracion_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiNumeracion:edit.html.twig',array(
            'item' => $entityItemMenu,
            'numeracion'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_33-8")
     * 
     * Documentación para el método 'deleteAction'.
     *
     * Elimina una numeración de la tabla 'ADMI_NUMERACION' y guarda su historial respectivo en 'ADMI_NUMERACION_HISTORIAL'
     * 
     * @return Response $objRespuesta
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para que se guarde el Historial respectivo de la Numeración al eliminarla
     */
    public function deleteAction($id)
    {
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        $strUsrSession  = $objSession->get('user');
        $datetimeActual = new \DateTime('now');
        
        $emComercial->getConnection()->beginTransaction();

        try
        {
            if (null == $objAdmiNumeracion = $emComercial->find('schemaBundle:AdmiNumeracion', $id)) 
            {
                throw $this->createNotFoundException('No existe la numeracion indicada.');
            }
            else
            {
                if(strtolower($objAdmiNumeracion->getEstado()) != "eliminado")
                {
                    $objAdmiNumeracion->setEstado("Eliminado");
                    $objAdmiNumeracion->setFeUltMod($datetimeActual);
                    $objAdmiNumeracion->setUsrUltMod($strUsrSession);
                    $emComercial->persist($objAdmiNumeracion);

                    $objAdmiNumeracionHisto = new AdmiNumeracionHisto();
                    $objAdmiNumeracionHisto->setNumeracionId($objAdmiNumeracion);
                    $objAdmiNumeracionHisto->setNumeracionUno($objAdmiNumeracion->getNumeracionUno());
                    $objAdmiNumeracionHisto->setNumeracionDos($objAdmiNumeracion->getNumeracionDos());
                    $objAdmiNumeracionHisto->setSecuenciaInicio(1);
                    $objAdmiNumeracionHisto->setSecuenciaFin($objAdmiNumeracion->getSecuencia());
                    $objAdmiNumeracionHisto->setCodEstablecimiento($objAdmiNumeracion->getNumeracionUno());
                    $objAdmiNumeracionHisto->setFeCreacion($datetimeActual);
                    $objAdmiNumeracionHisto->setUsrCreacion($strUsrSession);
                    $objAdmiNumeracionHisto->setEstado('Eliminado');
                    $emComercial->persist($objAdmiNumeracionHisto);
                }
            }
            
            $emComercial->flush();
            $emComercial->getConnection()->commit();
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            $emComercial->getConnection()->rollback();
        }

        $emComercial->getConnection()->close();

        return $this->redirect($this->generateUrl('adminumeracion'));
    }

    
    /**
     * @Secure(roles="ROLE_33-9")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Elimina una numeración de la tabla 'ADMI_NUMERACION' y guarda su historial respectivo en 'ADMI_NUMERACION_HISTORIAL'
     * 
     * @return Response $objRespuesta
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para que se guarde el Historial respectivo de la Numeración al eliminarla
     */
    public function deleteAjaxAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        $strUsrSession  = $objSession->get('user');
        $datetimeActual = new \DateTime('now');
        $strParametro   = $objRequest->get('param');
        $strMensaje     = 'Al eliminar las numeraciones se obtuvo los siguientes resultados:<br/>';
        
        $arrayIdNumeraciones = explode("|",$strParametro);
        
        
        $emComercial->getConnection()->beginTransaction();

        try
        {
            foreach($arrayIdNumeraciones as $intIdNumeracion)
            {
                if (null == $objAdmiNumeracion = $emComercial->find('schemaBundle:AdmiNumeracion', $intIdNumeracion)) 
                {
                    $strMensaje .= 'No existe la numeracion seleccionada de id: '.$intIdNumeracion.'.<br>';
                }
                else
                {
                    if(strtolower($objAdmiNumeracion->getEstado()) != "eliminado")
                    {
                        $objAdmiNumeracion->setEstado("Eliminado");
                        $objAdmiNumeracion->setFeUltMod($datetimeActual);
                        $objAdmiNumeracion->setUsrUltMod($strUsrSession);
                        $emComercial->persist($objAdmiNumeracion);
                        
                        $objAdmiNumeracionHisto = new AdmiNumeracionHisto();
                        $objAdmiNumeracionHisto->setNumeracionId($objAdmiNumeracion);
                        $objAdmiNumeracionHisto->setNumeracionUno($objAdmiNumeracion->getNumeracionUno());
                        $objAdmiNumeracionHisto->setNumeracionDos($objAdmiNumeracion->getNumeracionDos());
                        $objAdmiNumeracionHisto->setSecuenciaInicio(1);
                        $objAdmiNumeracionHisto->setSecuenciaFin($objAdmiNumeracion->getSecuencia());
                        $objAdmiNumeracionHisto->setCodEstablecimiento($objAdmiNumeracion->getNumeracionUno());
                        $objAdmiNumeracionHisto->setFeCreacion($datetimeActual);
                        $objAdmiNumeracionHisto->setUsrCreacion($strUsrSession);
                        $objAdmiNumeracionHisto->setEstado('Eliminado');
                        $emComercial->persist($objAdmiNumeracionHisto);
                        
                        $strMensaje .= 'Se elimina la numeración : '.$objAdmiNumeracion->getNumeracionUno().'-'
                                                                    .$objAdmiNumeracion->getNumeracionDos().'<br>';
                    }
                    else
                    {
                        $strMensaje .= 'No se elimina la numeración '.$objAdmiNumeracion->getNumeracionUno().'-'
                                       .$objAdmiNumeracion->getNumeracionDos().' porque ya se encuentra en estado Eliminado.<br>';
                    }
                }
            }
            
            $emComercial->flush();
            $emComercial->getConnection()->commit();
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            $strMensaje .= 'Hubo un problema de base de datos.';
            $emComercial->getConnection()->rollback();
        }

        $emComercial->getConnection()->close();
        
        $objRespuesta->setContent($strMensaje);
        
        return $objRespuesta;
    }
    
    
    /**
     * @Secure(roles="ROLE_33-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Llena el grid de consulta
     * 
     * @return Response 
     * 
     * @version 1.0 Version Inicial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29-12-2015 - Se modifica para que se muestre las numeraciones dependiendo de la empresa del usuario en sessión
     */
    public function gridAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objRequest   = $this->get('request');
        $objSession   = $objRequest->getSession();
        $emComercial  = $this->getDoctrine()->getManager("telconet");
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");
        $intIdEmpresa = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strTmpNombre = $objRequest->query->get('query') ? $objRequest->query->get('query') : "";
        $strNombre    = ($strTmpNombre != '' ? $strTmpNombre : $objRequest->query->get('nombre'));
        $intIdOficina = $objRequest->query->get('oficina') ? $objRequest->query->get('oficina') : 0;
        $strEstado    = $objRequest->query->get('estado');
        $intInicio    = $objRequest->query->get('start');
        $intLimite    = $objRequest->query->get('limit');
        
        $arrayParametros = array( 'nombre'    => $strNombre, 
                                  'estado'    => $strEstado, 
                                  'inicio'    => $intInicio, 
                                  'limite'    => $intLimite,
                                  'empresa'   => $intIdEmpresa,
                                  'oficina'   => $intIdOficina,
                                  'emGeneral' => $emGeneral );
        
        $objJson = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->generarJson($arrayParametros);
        
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    
    /**
    * @Secure(roles="ROLE_33-16")
    */
    public function getListadoEmpresasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager();
        $empresas = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findByEstado("Activo");

        if($empresas && count($empresas)>0)
        {
            $num = count($empresas);
            
            $arr_encontrados[]=array('id_empresa' =>0, 'nombre_empresa' =>"Seleccion una empresa");
            foreach($empresas as $key => $empresa)
            {                
                $arr_encontrados[]=array('id_empresa' =>$empresa->getId(),
                                         'nombre_empresa' =>trim($empresa->getNombreEmpresa()));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_empresa' => 0 , 'nombre_empresa' => 'Ninguno','modulo_id' => 0 , 'modulo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $resultado);
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    
    /**
     * @Secure(roles="ROLE_33-17")
     * 
     * Documentación para el método 'getListadoOficinasAction'
     * 
     * Función que retorna las oficinas que son de facturación de la empresa en sessión
     * 
     * @return json $jsonRespuesta
     * 
     * @version 1.0 Versión Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 29/12/2015 - Se modifica para que retorne sólo las oficinas que están habilitadas para facturar. 
     */
    public function getListadoOficinasAction()
    {
        $jsonRespuesta   = new JsonResponse();
        $arrayResultados = array();
        $objRequest      = $this->get('request');
        $objSession      = $objRequest->getSession();
        $intIdEmpresa    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $emComercial     = $this->getDoctrine()->getManager();
        $intContador     = 0;
        
        if( $intIdEmpresa != 0 )
        {
            $objOficinas = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                       ->findBy( array( 'esOficinaFacturacion' => 'S',
                                                        'empresaId'            => $intIdEmpresa ) );
            
            if( $objOficinas )
            {
                foreach($objOficinas as $objOficina)
                {               
                    $item                   = array();
                    $item['id_oficina']     = $objOficina->getId();
                    $item['nombre_oficina'] = $objOficina->getNombreOficina();
                    
                    $intContador++;
                    
                    $arrayResultados[] = $item;
                }//foreach($objOficinas as $objOficina)
            }//( $objOficinas )
        }//if( $intIdEmpresa != 0 )
        
        $jsonRespuesta->setData( array( 'total' => $intContador, 'encontrados' => $arrayResultados) );
        
        return $jsonRespuesta;
    }
    
    
    /**
     * Documentación para el método 'getNumeroEstablecimientoPorOficinaAction'
     * 
     * Función que retorna el numero de establecimiento agregado a la oficina seleccionada
     * 
     * @return json $jsonRespuesta
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29/12/2015
     */
    public function getNumeroEstablecimientoPorOficinaAction()
    {
        $jsonRespuesta   = new JsonResponse();
        $arrayResultado  = array();
        $objRequest      = $this->get('request');
        $objSession      = $objRequest->getSession();
        $intIdEmpresa    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdOficina    = $objRequest->request->get('idOficina') ? $objRequest->request->get('idOficina') : 0;
        $emComercial     = $this->getDoctrine()->getManager();
        $boolError       = true;
        $strMensaje      = 'La oficina seleccionada no tiene un número de establecimiento agregado';
        
        if( $intIdEmpresa != 0 )
        {
            $objOficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                      ->findOneBy( array( 'esOficinaFacturacion' => 'S',
                                                          'empresaId'            => $intIdEmpresa,
                                                          'id'                   => $intIdOficina ) );
            
            if( $objOficina )
            {
                if( $objOficina->getNumEstabSri() )
                {
                    $boolError  = false;
                    $strMensaje = $objOficina->getNumEstabSri();
                }//( $objOficina->getNumEstabSri() )
            }//( $objOficinas )
        }//if( $intIdEmpresa != 0 )
        
        $jsonRespuesta->setData( array( 'error' => $boolError, 'mensaje' => $strMensaje) );
        
        return $jsonRespuesta;
    }
    
    
    /**
     * Documentación para el método 'verificarNumeracionPorOficinaAction'.
     *
     * Verifica que las secuencias uno (Número de Establecimiento SRI) y dos (Punto de Emisión) no hayan sido ingresadas anteriormente.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29-12-2015
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-06-2017 - Se añade validación por número de autorización
     */
    public function verificarNumeracionPorOficinaAction()
    {
        $response                    = new Response();
        $objRequest                  = $this->get('request');
        $objSession                  = $objRequest->getSession();
        $strMensaje                  = 'OK';
        $emComercial                 = $this->getDoctrine()->getManager('telconet');
        $strPuntoEmision             = $objRequest->request->get('strPuntoEmision') ? $objRequest->request->get('strPuntoEmision') : '';
        $intIdOficina                = $objRequest->request->get('intIdOficina') ? $objRequest->request->get('intIdOficina') : 0;
        $strNumeroEstablecimientoSri = $objRequest->request->get('strNumeroEstablecimientoSri') 
                                       ? $objRequest->request->get('strNumeroEstablecimientoSri') : '';
        $intIdEmpresa                = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdAdmiNumeracion         = $objRequest->request->get('intIdAdmiNumeracion') ? $objRequest->request->get('intIdAdmiNumeracion') : 0;

        $strNumeroAutorizacion        = $objRequest->request->get('strNumeroAutorizacion') ? $objRequest->request->get('strNumeroAutorizacion') : '';
        $strMostrarNumeroAutorizacion = $objRequest->request->get('strMostrarNumeroAutorizacion') 
                                        ? $objRequest->request->get('strMostrarNumeroAutorizacion') : 'N';
        $strMostrarSecuenciales       = $objRequest->request->get('strMostrarSecuenciales') 
                                        ? $objRequest->request->get('strMostrarSecuenciales') : 'N';

        $arrayParametrosVerificar = array( 'oficinaId' => $intIdOficina,
                                           'empresaId' => $intIdEmpresa,
                                           'estado'    => 'Activo' );
        
        if ( $strMostrarSecuenciales == "S" )
        {
            $arrayParametrosVerificar["numeracionUno"] = $strNumeroEstablecimientoSri;
            $arrayParametrosVerificar["numeracionDos"] = $strPuntoEmision;
        }// ( $strMostrarSecuenciales == "S" )
        
        if ( $strMostrarNumeroAutorizacion == "S" )
        {
            $arrayParametrosVerificar["numeroAutorizacion"] = $strNumeroAutorizacion;
        }// ( $strMostrarNumeroAutorizacion == "S" )
        
        if( $intIdEmpresa != 0 )
        {
            $objOficina = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findOneBy( $arrayParametrosVerificar );
            
            if( $objOficina )
            {
                if( $intIdAdmiNumeracion )
                {
                    if( $intIdAdmiNumeracion != $objOficina->getId() )
                    {
                        $strMensaje = 'La numeración ingresada para esta oficina ya existe';
                    }
                }
                else
                {
                    $strMensaje = 'La numeración ingresada para esta oficina ya existe';
                }//( $intIdAdmiNumeracion )
            }//( $objOficinas )
        }//if( $intIdEmpresa != 0 )
        else
        {
            $strMensaje = 'No existe empresa en sesión';
        }
        
        $response->setContent( $strMensaje );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'getTipoComprobantesAction'
     * 
     * Función que retorna los tipos de comprobantes financieros existentes en el sistema.
     * 
     * @return response $objResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29/12/2015
     */
    public function getTipoComprobantesAction()
    {
        $objResponse   = new Response();
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $intIdEmpresa  = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
        
        if( $intIdEmpresa != 0 )
        {
            $jsonRespuesta = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getJSONParametrosByCriterios( array( 'descripcion' => 'TIPO_COMPROBANTES',
                                                                              'estado'      => 'Activo' ) );
            
            $objResponse->setContent($jsonRespuesta);
        }//if( $intIdEmpresa != 0 )
        
        return $objResponse;
    }
}