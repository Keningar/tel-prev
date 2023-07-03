<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoDocumento;

use telconet\schemaBundle\Form\PersonaEmpleadoType;
use telconet\schemaBundle\Form\InfoDocumentoType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class PersonaEmpleadoController extends Controller implements TokenAuthenticatedController
{ 
     /**
     * @Secure(roles="ROLE_171-1")
     *
     * Documentación para el método 'indexAction'.
     * Muestra la pagina principal del modulo de Empleado en Administracion
     *
     * @return Response.
     *
     * @version 1.0 Version Inicial
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 19-02-2021 - Se agrega perfil para habilitar y deshabilitar los usuarios en el servidor del Tacacs
     */
    public function indexAction()
    {
        $rolesPermitidos   = array();
        $em_seguridad      = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $entityItemMenu    = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("171", "1");

        $objSession            = $this->get('session');
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $strNombreDepartamento = "";
        $intIdDepartamento     = "";

        if($strCodEmpresa == "10")
        {
            $intIdDepartamento   = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
            $objAdmiDepartamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamento);

            if(is_object($objAdmiDepartamento))
            {
                $strNombreDepartamento = $objAdmiDepartamento->getNombreDepartamento();
            }
        }

        //MODULO 171 - EMPLEADO/CREAR
        if(true === $this->get('security.context')->isGranted('ROLE_171-3'))
        {
            $rolesPermitidos[] = 'ROLE_171-3';
        }
        //MODULO 171 - EMPLEADO/EDITAR
        if (true === $this->get('security.context')->isGranted('ROLE_171-4'))
        {
            $rolesPermitidos[] = 'ROLE_171-4';
        }
        //MODULO 171 - EMPLEADO/CONSULTAR
        if (true === $this->get('security.context')->isGranted('ROLE_171-6'))
        {
            $rolesPermitidos[] = 'ROLE_171-6';
        }
        //MODULO 171 - EMPLEADO/ELIMINAR
        if (true === $this->get('security.context')->isGranted('ROLE_171-8'))
        {
            $rolesPermitidos[] = 'ROLE_171-8';
        }
        
        //MODULO 171 - EMPLEADO/SUBIR_ARCHIVOS
        if (true === $this->get('security.context')->isGranted('ROLE_171-3998'))
        {
            $rolesPermitidos[] = 'ROLE_171-3998';
        }
        
        //MODULO 171 - EMPLEADO/VER_ARCHIVOS
        if (true === $this->get('security.context')->isGranted('ROLE_171-3997'))
        {
            $rolesPermitidos[] = 'ROLE_171-3997';
        }

        //MODULO 171 - EMPLEADO/HABILITAR/DESHABILITAR USER TACACS
        if (true === $this->get('security.context')->isGranted('ROLE_171-7937'))
        {
            $rolesPermitidos[] = 'ROLE_171-7937';
        }
        
        //MODULO 171 - MOSTRAR TODOS LOS DEPARTAMENTOS 
        if (true === $this->get('security.context')->isGranted('ROLE_171-7957'))
        {
            $rolesPermitidos[] = 'ROLE_171-7957';
        }        

        //MODULO 171 - EMPLEADO/ELIMINAR_ARCHIVOS
        if (true === $this->get('security.context')->isGranted('ROLE_171-3999'))
        {
            $rolesPermitidos[] = 'ROLE_171-3999';
        }
        
        return $this->render('administracionBundle:PersonaEmpleado:index.html.twig', array(
            'item'                 => $entityItemMenu,
            'rolesPermitidos'      => $rolesPermitidos,
            'codigoEmpresa'        => $strCodEmpresa,
            'departamentoSesion'   => $strNombreDepartamento,
            'departamentoIdSesion' => $intIdDepartamento
        ));
    }
    
    /**
     * @Secure(roles="ROLE_171-6")
     * 
     * Documentación para el método 'showAction'.
     * 
     * Muestra la información de un empleado.
     * 
     * @param integer $id
     * 
     * @return Response.
     * 
     * @version 1.0 Version Inicial
     * 
     */    

    public function showAction($id)
    {
        $peticion       = $this->get('request');
        
        $em             = $this->getDoctrine()->getManager();
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("171", "1");

        if (null == $empleado = $em->find('schemaBundle:InfoPersonaEmpresaRol', $id)) {
            throw new NotFoundHttpException('No existe el InfoPersona que se quiere mostrar');
        }

        return $this->render('administracionBundle:PersonaEmpleado:show.html.twig', array(
            'item'       => $entityItemMenu,
            'empleado'   => $empleado,
            'flag'      => $peticion->get('flag')
        ));
    }

    /**
     * 
     * Documentación para el método 'getArchivosCaducadosAction'.
     * 
     * Muestra los documentos caducados de un empleado.
     * 
     * @param integer $id // id de InfoPersonaEmpresaRol  
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-12-2015
     * 
     */ 
    public function getArchivosCaducadosAction($id)
    {
        $fechaActual                            = date("Y-m-d");
        list($anioActual,$mesActual,$diaActual) = explode('-', $fechaActual);
        $timestampActual                        = mktime('0','0','0',$mesActual,$diaActual,$anioActual);
        
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');        
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');		
        $emComunicacion   = $this->getDoctrine()->getManager('telconet_comunicacion');
        
        $objInfoDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
        $objEntities              = $objInfoDocumentoRelacion->findBy(array('personaEmpresaRolId' => $id,'estado' => 'Activo'), 
                                                                      array('id' => 'DESC'));
             
        $arrayResponse                   = array();
        $arrayResponse['docsCaducados']  = array();
        $arrayResponse['docsPorCaducar'] = array();
        
        $contDocsCaducados  = 0;
        $contDocsPorCaducar = 0;
        
        foreach ($objEntities as $entity)
        {
            $arrayDocCaducado   = array();
            $arrayDocPorCaducar = array();
            
            $infoDocumento=$emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($entity->getDocumentoId());
            
            if($infoDocumento->getFechaPublicacionHasta())
            {
                $fechaPublicacionHasta=$infoDocumento->getFechaPublicacionHasta();
                
                $anioPublicacionHasta = strval(date_format($fechaPublicacionHasta, "Y"));
                $mesPublicacionHasta  = strval(date_format($fechaPublicacionHasta, "m"));
                $diaPublicacionHasta  = strval(date_format($fechaPublicacionHasta, "d"));

                
                $timestampPublicacionHasta = mktime('0','0','0',$mesPublicacionHasta,$diaPublicacionHasta,$anioPublicacionHasta);

                $diferenciaSegundos        = $timestampPublicacionHasta-$timestampActual;
                $diferenciaDias            = $diferenciaSegundos/(60*60*24);
                $absDiferenciaDias         = abs($diferenciaDias);
                $floorAbsDiferenciaDias    = floor($absDiferenciaDias);

                //Documentos ya Caducados
                if($diferenciaDias<0)
                {
                    $arrayDocCaducado['id']                       = $entity->getDocumentoId();
                    $arrayDocCaducado['ubicacionLogicaDocumento'] = $infoDocumento->getUbicacionLogicaDocumento();

                        $objTipoDocumentoGeneral                  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                              ->find($infoDocumento->getTipoDocumentoGeneralId());                                                                                                                                    

                    if( $objTipoDocumentoGeneral != null )
                    {       
                        $urlVerDocumento                          = $this->generateUrl('personaempleado_descargarDocumento', 
                                                                                        array('id' => $entity->getDocumentoId()));                             
                                                     
                        $arrayDocCaducado['tipoDocumentoGeneral'] = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();

                    } 

                    $arrayDocCaducado['feCreacion']            = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
                    $arrayDocCaducado['feCaducidad']           = date_format($infoDocumento->getFechaPublicacionHasta(), 'd-m-Y');
                    $arrayDocCaducado['usrCreacion']           = $entity->getUsrCreacion();     
                    $arrayDocCaducado['linkVerDocumento']      = $urlVerDocumento;
                    $arrayDocCaducado['estadoCaducidad']       = "Ya caducado"; 
                    $arrayResponse['docsCaducados'][]          = $arrayDocCaducado;
                    $contDocsCaducados++;
                }
                //Documentos que caducan en 2 semanas o menos 
                else if($floorAbsDiferenciaDias<=14)
                {
                    $arrayDocPorCaducar['ubicacionLogicaDocumento'] = $infoDocumento->getUbicacionLogicaDocumento();

                    $objTipoDocumentoGeneral                        = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                                ->find($infoDocumento->getTipoDocumentoGeneralId());                                                                                                                                    

                    if( $objTipoDocumentoGeneral != null )
                    {       
                        $urlVerDocumento                            = $this->generateUrl('personaempleado_descargarDocumento', 
                                                                                          array('id' => $entity->getDocumentoId()));                             
                                                     
                        $arrayDocPorCaducar['tipoDocumentoGeneral'] = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();

                    } 

                    $arrayDocPorCaducar['feCreacion']            = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
                    $arrayDocPorCaducar['feCaducidad']           = date_format($infoDocumento->getFechaPublicacionHasta(), 'd-m-Y');
                    $arrayDocPorCaducar['usrCreacion']           = $entity->getUsrCreacion();     
                    $arrayDocPorCaducar['linkVerDocumento']      = $urlVerDocumento;
                    $strCantidadDias="";
                    if($diferenciaDias==0) 
                    {
                        $strCantidadDias="Hoy";
                    }
                    else if($diferenciaDias==1) 
                    {
                        $strCantidadDias="Mañana";
                    }
                    else 
                    {
                        $strCantidadDias="En ".$diferenciaDias." días";
                    }
                    $arrayDocPorCaducar['estadoCaducidad']       = $strCantidadDias; 
                    $arrayResponse['docsPorCaducar'][]           = $arrayDocPorCaducar;
                    
                    
                    $contDocsPorCaducar++;
                }  
  
            }   
        }
        $arrayResponse['totalDocsCaducados']  = $contDocsCaducados;
        $arrayResponse['totalDocsPorCaducar'] = $contDocsPorCaducar;
        $arrayResponse['total']               = $arrayResponse['totalDocsCaducados'] + $arrayResponse['totalDocsPorCaducar'];
        $arrayResponse['docs']                = array_merge($arrayResponse['docsCaducados'],$arrayResponse['docsPorCaducar']);
       
        $response->setContent(json_encode($arrayResponse));
        return $response;
        
    }
    
    /**
     * Función consultarUserTacacsAction: funcion que llama al api del Tacacs para consultar el estado de un usuario
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 18-02-2021
     *
     * @return JsonResponse
     */
    public function consultarUserTacacsAction()
    {
        ini_set('max_execution_time', 650000);

        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');

        $objPeticion                   = $this->get('request');
        $emNaf                         = $this->getDoctrine()->getManager('telconet_naf');

        $objSession                    = $objPeticion->getSession();
        $strIpUsuarioCreacion          = $objPeticion->getClientIp();
        $strUsrCreacion                = $objSession->get('user');
        $strCodEmpresa                 = $objSession->get('idEmpresa');
        $strUsuario                    = $objPeticion->get('usuario')?$objPeticion->get('usuario'):"";
        $strConsultarUserTacacsRestURL = $this->container->getParameter('consultarUserTacacs_webService_url');
        $serviceUtil                   = $this->get('schema.Util');

        $arrayParametros["strUsrl"]   = $strConsultarUserTacacsRestURL;
        $arrayParametros["strLogin"]  = $strUsuario;
        $arrayParametros["strOpcion"] = "consultar";

        try
        {
            //Primero consultar el estado del login en el NAF
            $arrayParametros["strLogin"]       = $strUsuario;
            $arrayParametros["intCodEmpresa"]  = $strCodEmpresa;
            $arrayParametros["objUtilService"] = $serviceUtil;

            $strEstadoNaf = $emNaf->getRepository('schemaBundle:InfoPersona')->getEstadoNafPorServicio($arrayParametros);

            if($strEstadoNaf == "A")
            {
                $arrayRespuesta = $this->llamadaWsTacacs($arrayParametros);

                if($arrayRespuesta["status"] == "ok")
                {
                    $boolSuccess = true;
                    $strMensaje  = $arrayRespuesta["activoEnTacacs"];
                }
                else
                {
                    $boolSuccess = false;
                    $strMensaje  = $arrayRespuesta["mensaje"];
                }
            }
            else
            {
                $boolSuccess = false;
                $strMensaje  = "El empleado se encuentra Inactivo en el NAF";
            }
        }
        catch (\Exception $e)
        {
            $boolSuccess = false;
            $strMensaje  = "Se presentó un error en el llamado al WS del TACACS";

            $serviceUtil->insertError('Telcos+',
                                      'PersonaEmpleadoController.actualizarUserTacacsAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpUsuarioCreacion
                                     );
        }

        $arrayRespuesta = '{"success":"'.$boolSuccess.'","respuesta":"'.$strMensaje.'"}';

        $objResponse->setContent($arrayRespuesta);

        return $objResponse;
    }


    /**
     * Función actualizarUserTacacsAction: funcion que llama al api del Tacacs para activar/desactivar y eliminar
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 17-02-2021
     *
     * @return JsonResponse
     */
    public function actualizarUserTacacsAction()
    {
        ini_set('max_execution_time', 650000);

        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');

        $objPeticion                 = $this->get('request');
        $objSession                   = $objPeticion->getSession();
        $strIpUsuarioCreacion         = $objPeticion->getClientIp();
        $strUsrCreacion               = $objSession->get('user');
        $strUsuario                   = $objPeticion->get('usuario')?$objPeticion->get('usuario'):"";
        $strUserActivoTacacs          = $objPeticion->get('usuarioActivoEnTacacs')?$objPeticion->get('usuarioActivoEnTacacs'):"";
        $strPersonaEmpresaRolId       = $objPeticion->get('personaEmpresaRolId')?$objPeticion->get('personaEmpresaRolId'):"";
        $strAgregarUserTacacsRestURL  = $this->container->getParameter('agregarUserTacas_webService_url');
        $strEliminarUserTacacsRestURL = $this->container->getParameter('eliminarUserTacacs_webService_url');
        $serviceUtil                  = $this->get('schema.Util');
        $emComercial                  = $this->getDoctrine()->getManager('telconet');
        $emGeneral                    = $this->getDoctrine()->getManager('telconet_general');
        $strEstadoPersonaEmpresaRol   = "";
        $strMensajeObservacion        = "";
        $intIdMotivo                  = "";

        if($strUserActivoTacacs == "Activo")
        {
            $arrayParametros["strUsrl"]  = $strEliminarUserTacacsRestURL;
            $strMensajeObservacion       = "Se quitaron los accesos en el servidor TACACS para el usuario: ".$strUsuario;
        }

        if($strUserActivoTacacs == "Inactivo")
        {
            $arrayParametros["strUsrl"]  = $strAgregarUserTacacsRestURL;
            $strMensajeObservacion       = "Se otorgaron los accesos en el servidor TACACS para el usuario: ".$strUsuario;
        }

        $arrayParametros["strLogin"]  = $strUsuario;
        $arrayParametros["strOpcion"] = "actualizar";

        try
        {

            $arrayRespuesta = $this->llamadaWsTacacs($arrayParametros);

            if($arrayRespuesta["status"] == "ok")
            {
                $boolSuccess = true;

                $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($strPersonaEmpresaRolId);

                if(is_object($objPersonaEmpresaRol))
                {
                    $strEstadoPersonaEmpresaRol = $objPersonaEmpresaRol->getEstado();
                }

                $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo("Actualizar accesos de usuario");

                if(is_object($objMotivo))
                {
                    $intIdMotivo = $objMotivo->getId();
                }

                //Registrar la accion ejecuta en un historial
                $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                $objInfoPersonaEmpresaRolHistorial->setEstado($strEstadoPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUsuarioCreacion);
                $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUsrCreacion);
                $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                $emComercial->flush();
            }
            else
            {
                $boolSuccess = false;
            }

            $strMensaje  = $arrayRespuesta["mensaje"];
        }
        catch (\Exception $e)
        {
            $boolSuccess = false;
            $strMensaje  = "Se presentó un error en el llamado al WS del TACACS";

            $serviceUtil->insertError('Telcos+',
                                      'PersonaEmpleadoController.actualizarUserTacacsAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpUsuarioCreacion
                                     );
        }

        $arrayRespuesta = '{"success":"'.$boolSuccess.'","respuesta":"'.$strMensaje.'"}';

        $objResponse->setContent($arrayRespuesta);

        return $objResponse;
    }


    /**
     * llamadaWsTacacs
     *
     * Método que se encarga de llamar al ws del Tacacs
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0
     * @since 18-02-2021
     *
     * @param  Array $arrayParametros [ información requerida por el WS segun el tipo del método a ejecutar ]
     *
     * @return Array $arrayResultado [ información que retorna el método llamado ]
     */
    private function llamadaWsTacacs($arrayParametros)
    {
        $arrayResultado = array();
        $objRestClient  = $this->container->get('schema.RestClient');
        //Se genera el json a enviar al ws por tipo de proceso a ejecutar
        $strDataString = $this->generateJson($arrayParametros);

        //Se obtiene el resultado de la ejecucion via rest hacia el ws
        $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);

        $arrayResponseJson = $objRestClient->postJSON($arrayParametros["strUsrl"], $strDataString , $arrayOptions);
        $arrayResponse     = json_decode($arrayResponseJson['result'],true);

        if($arrayResponseJson['status'] == 200)
        {
            if($arrayResponse['status'] == "ok")
            {
                if($arrayParametros["strOpcion"] == "consultar")
                {
                    $arrayResultado['status']         = $arrayResponse['status'];
                    $arrayRespuesta                   = $arrayResponse['response'];
                    $arrayResultado['activoEnTacacs'] = $arrayRespuesta['active'];
                }

                if($arrayParametros["strOpcion"] == "actualizar")
                {
                    $arrayResultado['mensaje'] = $arrayResponse['response'];
                    $arrayResultado['status']  = $arrayResponse['status'];
                }
            }
            else
            {
                $arrayResultado['mensaje'] = $arrayResponse['error'];
                $arrayResultado['status']  = $arrayResponse['status'];
            }
        }
        else
        {
            $arrayResultado['mensaje'] = "Se presentó un error en el llamado al WS del TACACS";
            $arrayResultado['status']  = $arrayResponseJson['status'];
        }

        return $arrayResultado;
    }

    /**
     * generateJson
     *
     * Función encargada de generar el array a enviar para el consumo del Web Service Rest del Tacacs
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0
     * @since 18-02-2021
     *
     * @param  json {
     *                  uid  : login
     *              }
     *
     * @return json [ respuesta del ws del tacacs ]
     */
    private function generateJson($arrayParametros)
    {
        $objJsonArray = array
                        (
                            "uid"  => $arrayParametros['strLogin']
                        );

        return json_encode($objJsonArray);
    }


    /**
     * @Secure(roles="ROLE_171-4")
     * 
     * Documentación para el método 'editAction'.
     * 
     * Edita la información de un empleado.
     * 
     * @param integer $id
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-12-2015
     * 
     */
    public function editAction($id)
   {   
        $em              = $this->getDoctrine()->getManager();
        $em_seguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu  = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("171", "1");
        $rolesPermitidos = array();
        
        $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);

        if(null == $objPersonaEmpleado = $em->find('schemaBundle:InfoPersona', $entityPersonaEmpresaRol->getPersonaId()->getId()))
        {
            throw new NotFoundHttpException('No existe el Empleado que se quiere modificar');
        }

        $formulario =$this->createForm(new PersonaEmpleadoType(), $objPersonaEmpleado);
        
        //MODULO 171 - EMPLEADO/ELIMINAR
        if(true === $this->get('security.context')->isGranted('ROLE_171-8'))
        {
            $rolesPermitidos[] = 'ROLE_171-8';
        }
        return $this->render('administracionBundle:PersonaEmpleado:edit.html.twig', array('item'              => $entityItemMenu,
                                                                                          'rolesPermitidos'   => $rolesPermitidos,
                                                                                          'edit_form'         => $formulario->createView(),
                                                                                          'personaEmpresaRol' => $entityPersonaEmpresaRol,
                                                                                          'personaempleado'   => $objPersonaEmpleado));
    }
    
    /**
     * @Secure(roles="ROLE_171-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información del empleado seleccionado validando la edición del empleado y sus formas de contacto para proceder con la 
     * respectiva actualización.
     *
     * @param integer $id
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-12-2015
     *  
     */
    public function updateAction($id)
    {        
        $objRequest              = $this->getRequest();
        $em                      = $this->getDoctrine()->getManager();
        $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);
        $entityPersona           = $entityPersonaEmpresaRol->getPersonaId();
        $objResponse             = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');

        if(!$entityPersona)
        {
            throw new \Exception('Unable to find InfoPersona entity.');
        }
        
        $strTipoIdentificacion = $objRequest->get("strTipoIdentificacion");
        $strIdentificacion     = $objRequest->get("strIdentificacion");
        $strNacionalidad       = $objRequest->get("strNacionalidad");
        $strNombres            = $objRequest->get("strNombres");
        $strDireccion          = $objRequest->get("strDireccion");
        $strApellidos          = $objRequest->get("strApellidos");
        $strGenero             = $objRequest->get("strGenero");
        $datFechaInstitucionY  = $objRequest->get("datFechaInstitucionY");
        $datFechaInstitucionM  = $objRequest->get("datFechaInstitucionM");
        $datFechaInstitucionD  = $objRequest->get("datFechaInstitucionD");
        $strEstadoCivil        = $objRequest->get("strEstadoCivil");
        $strTitulo             = $objRequest->get("strTitulo");
        
        /*
         * $listaFormasContacto contiene la informacion de todas las formas de contacto.
         * Cada forma de contacto tiene su id(si es que ya existe o 0 si es nuevo), la forma de contacto y el valor de dicho contacto
         * Toda la información de $listaFormasContacto se encuentra separada por comas
         * 
         */
        $listaFormasContacto   = $objRequest->get("lstFormasContacto");
       
        $arrayParametros=array(
                          'idPersona'          => $entityPersona->getId(),
                          'tipoIdentificacion' => $strTipoIdentificacion,
                          'identificacion'     => $strIdentificacion,
                          'nombres'            => $strNombres,
                          'apellidos'          => $strApellidos,
                          'direccion'          => $strDireccion,
                          'genero'             => $strGenero,
                          'titulo'             => $strTitulo,
                          'estadoCivil'        => $strEstadoCivil
                         );
        
        /* @var $servicePersonaEmpleado \telconet\administracionBundle\Service\PersonaEmpleadoService */
        $servicePersonaEmpleado = $this->get('administracion.PersonaEmpleado');
        $arrayValidacion        = $servicePersonaEmpleado->validacionesCreacionActualizacion($arrayParametros);
        
        if(!$arrayValidacion['boolOk'])    
        {
            return $objResponse->setContent(json_encode(array('estatus' => false, 'msg' => $arrayValidacion['strMsg'], 'id' => 0)));
        }
        
        

        $strUsrUltMod   = $objRequest->getSession()->get('user');
        $em->getConnection()->beginTransaction();
        
        try 
        {
            $entityPersona->setTipoIdentificacion($strTipoIdentificacion);
            $entityPersona->setIdentificacionCliente($strIdentificacion);
            $entityPersona->setNombres($strNombres);
            $entityPersona->setApellidos($strApellidos);
            $entityPersona->setNacionalidad($strNacionalidad);
            $entityPersona->setGenero($strGenero);
            $entityPersona->setDireccion($strDireccion);
            $entityPersona->setEstado('Activo');
            $entityPersona->setOrigenProspecto('N');
            
            if($strEstadoCivil!='')
            {
                $entityPersona->setEstadoCivil($strEstadoCivil);
            }
            if($datFechaInstitucionY != '' && $datFechaInstitucionM != '' && $datFechaInstitucionD != '')
            {
                $entityPersona->setFechaNacimiento(date_create($datFechaInstitucionY . '-' . $datFechaInstitucionM . '-' . $datFechaInstitucionD));
            }
            
            $entityTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($strTitulo);
            if($entityTitulo)
            {
                $entityPersona->setTituloId($entityTitulo);
            }
            $em->persist($entityPersona);
            $em->flush();
            
            
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
            $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
            $entityPersonaHistorial->setEstado($entityPersona->getEstado());
            $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
            $entityPersonaHistorial->setIpCreacion($objRequest->getClientIp());
            $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entityPersonaHistorial->setUsrCreacion($strUsrUltMod);
            $em->persist($entityPersonaHistorial);
            $em->flush();
            
            

            //Pone estado inactivo a todas las formas de contacto con estado 'Activo' del empleado
            /* @var $objServicioInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
            $objServicioInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            $objServicioInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entityPersona->getId(), $strUsrUltMod);
            
            /*
            * $arrayFormasContactoTmp tiene distribuida la información de la siguiente manera para las formas de contacto
            * [0=>'id',1=>'forma de contacto',2=>'valor de forma de contacto',...] 
            * 
            */
            $arrayFormasContactoTmp = explode(",", $listaFormasContacto);
            $intIndiceColumnaFormaContacto = 0; 
            $intIndiceArrayFormaContacto = 0;
            $arrayFormasContacto = array();
            for($i = 0; $i < count($arrayFormasContactoTmp); $i++)
            {
                /*
                 * Cuando $intIndiceColumnaFormaContacto=3 se setea a 0 dicha variable para que el $arrayFormasContactoTmp con índice $i
                 * sea el id de la forma de contacto con el cual no se está trabajando
                 */
                if($intIndiceColumnaFormaContacto == 3)
                {
                    $intIndiceColumnaFormaContacto = 0;
                    $intIndiceArrayFormaContacto++;
                }
                //$intIndiceColumna=1 Columna que tiene la forma de contacto que el usuario ha elegido del combobox
                if($intIndiceColumnaFormaContacto == 1)
                {
                    $arrayFormasContacto[$intIndiceArrayFormaContacto]['formaContacto'] = $arrayFormasContactoTmp[$i];
                }
                //$intIndiceColumna=2 Columna que tiene el valor de la forma de contacto que el usuario ha ingresado
                if($intIndiceColumnaFormaContacto == 2)
                {
                    $arrayFormasContacto[$intIndiceArrayFormaContacto]['valor'] = $arrayFormasContactoTmp[$i];
                }
                $intIndiceColumnaFormaContacto++;
            }
            
            //Registra las formas de contacto del empleado
            for($i = 0; $i < count($arrayFormasContacto); $i++)
            {
                $strFormaContacto = $arrayFormasContacto[$i]["formaContacto"];
                if($strFormaContacto != '')
                {
                    $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')
                                                          ->findPorDescripcionFormaContacto($strFormaContacto);
                    if($entityAdmiFormaContacto == null)
                    {
                        throw new \Exception("No existe la forma de contacto: $strFormaContacto");
                    }
                    $entityPersonaFormaContacto = new InfoPersonaFormaContacto();
                    $entityPersonaFormaContacto->setValor($arrayFormasContacto[$i]["valor"]);
                    $entityPersonaFormaContacto->setEstado("Activo");
                    $entityPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                    $entityPersonaFormaContacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entityPersonaFormaContacto->setIpCreacion($objRequest->getClientIp());
                    $entityPersonaFormaContacto->setPersonaId($entityPersona);
                    $entityPersonaFormaContacto->setUsrCreacion($strUsrUltMod);
                    $em->persist($entityPersonaFormaContacto);
                    $em->flush();
                }
            }     
            $em->getConnection()->commit();
            return $objResponse->setContent(json_encode(array('estatus' => true, 
                                                               'msg'    => 'Guardado satisfactoriamente', 
                                                               'id'     => $entityPersonaEmpresaRol->getId())));
        }
        catch (\Exception $e) 
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            return $objResponse->setContent(json_encode(array('estatus' => false, 'msg' => $e->getMessage())));
        }
        
    }    

    
    /**
     * @Secure(roles="ROLE_171-8")
     * 
     * Documentación para el método 'deleteAction'.
     *
     * Eliminar lógicamente un empleado .
     *
     * @param integer $id
     * 
     * @return Response. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-12-2015
     * 
     */
    public function deleteAction($id)
    {
        $objRequest              = $this->getRequest();
        $em                      = $this->getDoctrine()->getManager();
        $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id);

        if(!$entityPersonaEmpresaRol)
        {
            $objResponse = new Response(json_encode(array('estatus' => false, 'msg' => 'Unable to find PersonaEmpleado entity')));
        }
        else
        {
            $strEstado = 'Eliminado';
            $entityPersonaEmpresaRol->setEstado($strEstado);
            $em->persist($entityPersonaEmpresaRol);
            $em->flush();
            
            $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
            $entityPersonaHistorial->setEstado($strEstado);
            $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
            $entityPersonaHistorial->setIpCreacion($objRequest->getClientIp());
            $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entityPersonaHistorial->setUsrCreacion($objRequest->getSession()->get('user'));
            $em->persist($entityPersonaHistorial);
            $em->flush();
            $objResponse = new Response(json_encode(array('estatus' => true, 'msg' => 'Eliminado satisfactoriamente')));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_171-8")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     * 
     * Eliminar lógicamente la selección de empleados.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-12-2015
     * 
     */
    public function deleteAjaxAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $em                   = $this->getDoctrine()->getManager();
        $objRequest           = $this->get('request');
        $arrayElementos       = $objRequest->get('param');
        $arrayPersonaEmpleado = explode("|",$arrayElementos);
        $strMensajeError      = "";
        
        foreach($arrayPersonaEmpleado as $intIdPersonaEmpleado)
        {
            if(null == $entityPersonaEmpresaRol = $em->find('schemaBundle:InfoPersonaEmpresaRol', $intIdPersonaEmpleado))
            {
                $strMensajeError.="No existe el empleado con id ".$intIdPersonaEmpleado." que se quiere eliminar<br>";
            }
            else
            {
                if(strtolower($entityPersonaEmpresaRol->getEstado()) != "eliminado")
                {
                    $strEstado = 'Eliminado';
                    $entityPersonaEmpresaRol->setEstado($strEstado);
                    $em->persist($entityPersonaEmpresaRol);
                    $em->flush();

                    $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                    $entityPersonaHistorial->setEstado($strEstado);
                    $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                    $entityPersonaHistorial->setIpCreacion($objRequest->getClientIp());
                    $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $entityPersonaHistorial->setUsrCreacion($objRequest->getSession()->get('user'));
                    $em->persist($entityPersonaHistorial);
                    $em->flush();
                }
                
            }
        }
        if($strMensajeError!="")
        {
            $objResponse->setContent($strMensajeError);
        }
        else
        {
            $objResponse->setContent("Eliminado satisfactoriamente");
        }
        
        return $objResponse;
    }
    
    

    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_171-7")
    */
    public function gridAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        
        $objRequest = $this->get('request');
        
        $strNombres        = $objRequest->query->get('nombres') ? trim($objRequest->query->get('nombres')) : "";
        $strApellidos      = $objRequest->query->get('apellidos') ? trim($objRequest->query->get('apellidos')) : "";
        $strIdentificacion = $objRequest->query->get('identificacion') ? trim($objRequest->query->get('identificacion')) : "";
        $strEstado         = $objRequest->query->get('estado') ? trim($objRequest->query->get('estado')) : "";
        $intIdDepartamento = $objRequest->query->get('departamento') ? trim($objRequest->query->get('departamento')) : 0;
        $intIdCanton       = $objRequest->query->get('canton') ? trim($objRequest->query->get('canton')) : 0;
        $intStart          = $objRequest->query->get('start');
        $intLimit          = $objRequest->query->get('limit');
        
        $session      = $this->get( 'session' ); 
        $intIdEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
        
        $arrayParametros = array(
                                    'codEmpresa' => $intIdEmpresa,
                                    'intStart'   => $intStart,
                                    'intLimit'   => $intLimit,
                                    'criterios'  => array(  'nombres'        => $strNombres, 
                                                            'apellidos'      => $strApellidos,
                                                            'identificacion' => $strIdentificacion,
                                                            'estado'         => $strEstado,
                                                            'departamento'   => $intIdDepartamento,
                                                            'canton'         => $intIdCanton
                                                          )
                                );
        
        	
        
        $objJson = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoPersona')->getJSONEmpleados($arrayParametros);
        $objResponse->setContent($objJson);
        
        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'departamentosAction'.
     *
     * Departamentos correspondientes a los empleados
     *
     * @return JsonResponse 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-12-2015 
     */
    public function departamentosAction()
    {
        $response     = new JsonResponse();
        $session      = $this->get( 'session' ); 
        $intIdEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
        
        $objRequest            = $this->get('request');
        $strNombreDepartamento = $objRequest->query->get('query');
        
        $emSoporte = $this->getDoctrine()->getManager("telconet_general");  
        
        $intTotal           = 0;
        $arrayDepartamentos = array();
        
        $arrayResultados = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')
                                     ->getDepartamentosByEmpresaYNombre($intIdEmpresa,$strNombreDepartamento);
        
        if($arrayResultados)
        {
            foreach($arrayResultados as $arrayDepartamento)
            {
                $item              = array();
                $item['strValue']  = $arrayDepartamento->getId();
                $item['strNombre'] = $arrayDepartamento->getNombreDepartamento();
                
                $arrayDepartamentos[] = $item;
                
                $intTotal++;
            }
        }
        
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayDepartamentos) );
        
        return $response;
    }
    
    /**
     * Documentación para el método 'getCantonesAction'.
     *
     * Cantones correspondientes a las oficinas a las que pertenecen los empleados
     *
     * @return JsonResponse 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-12-2015
     *
     *
     * Se modifica funcion para enviar parametro intPaisId a la funcion getCantonesPorNombre
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 04-07-2017
     *
     * Se edita el parametro de strNombres a strNombre.
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 09-02-2021
     * 
     */
    public function getCantonesAction()
    {
        $response   = new JsonResponse(); 
        $objRequest = $this->get('request');
        $objSession = $this->get( 'session' ); 
        $emSoporte  = $this->getDoctrine()->getManager("telconet_general");  
        
        $arrayParametros               = array();
        $arrayParametros['strNombre']  = $objRequest->query->get('query');
        $arrayParametros['intIdPais']  = $objSession->get('intIdPais');
        
        $intTotal      = 0;
        $arrayCantones = array();
        
        /*Se realiza la búsqueda de los cantones
         * Si el usuario ha ingresado algún texto en el campo de búsqueda del combo del cantón, la búsqueda se realiza por 
         * el nombre del cantón que coincida con $strNombreCanton=texto ingresado
         * Si por el contrario el combo se carga inicialmente $strNombreCanton='' y se obtienen todos los cantones
        */
        $arrayResultados = $emSoporte->getRepository('schemaBundle:AdmiCanton')->getCantonesPorNombre($arrayParametros);
        
        if($arrayResultados)
        {
            foreach($arrayResultados as $arrayCanton)
            {
                $item              = array();
                $item['strValue']  = $arrayCanton->getId();
                $item['strNombre'] = $arrayCanton->getNombreCanton();
                
                $arrayCantones[] = $item;
                
                $intTotal++;
            }
        }
        
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayCantones) );
        
        return $response;
    }
    
    
    
    /**
     * Documentación para el método 'formasContactoAjaxAction'.
     * 
     * Consulta las formas de contacto activas para asignar al empleado.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-12-2015
     * 
     */
    public function formasContactoAjaxAction() 
    {
        $em                  = $this->get('doctrine')->getManager('telconet');
        $listaFormasContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
        foreach($listaFormasContacto as $entityFormaContacto)
        {
            $arrayFormasContacto[] = array('id'          => $entityFormaContacto->getId(),
                                           'descripcion' => $entityFormaContacto->getDescripcionFormaContacto());
        }
        $objResponse = new Response(json_encode(array('formasContacto' => $arrayFormasContacto)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'formasContactoGridAction'.
     * 
     * Consulta las formas de contacto del Empleado
     * 
     * @return Response JSON con la lista de formas de contacto activas del empleado.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-12-2015
     * 
     */
    public function formasContactoGridAction()
    {
        $objRequest   = $this->getRequest();
        $intLimit     = $objRequest->get("limit");
        $intPage      = $objRequest->get("page");
        $intStart     = $objRequest->get("start");
        $intPersonaId = $objRequest->get("personaid");
        
        $em            = $this->get('doctrine')->getManager();
        $arrayResponse = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                            ->findPorEstadoPorPersona($intPersonaId, 'Activo', $intLimit, $intPage, $intStart);
        
                           
        $listaFormasContacto = $arrayResponse['registros'];
        $intTotalRegistros   = $arrayResponse['total'];
        $arrayFormasContacto = null;
        foreach($listaFormasContacto as $entityFormaContacto)
        {
            $arrayFormasContacto[] = array('idPersonaFormaContacto' => $entityFormaContacto->getId(),
                                           'idPersona'              => $entityFormaContacto->getPersonaId()->getId(),
                                           'formaContacto'          => $entityFormaContacto->getFormaContactoId()->getDescripcionFormaContacto(),
                                           'valor'                  => $entityFormaContacto->getValor());
        }
        $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'personaFormasContacto' => $arrayFormasContacto)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'buscarPersonaPorIdentificacionAjaxAction'.
     * 
     * Consulta a los Empleados por identificación validando por identificación.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-12-2015
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 21-09-2017 - Se agrega el parámetro del país para validar la identificación.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     */
    public function buscarPersonaPorIdentificacionAjaxAction()
    {
        $objRequest            = $this->get('request');
        $strTipoIdentificacion = $objRequest->get('tipoIdentificacion');
        $strIdentificacion     = $objRequest->get('identificacion');
        $intIdPais             = $objRequest->getSession()->get('intIdPais');
        $intIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $em                    = $this->get('doctrine')->getManager();
        $objRepositorio        = $em->getRepository('schemaBundle:InfoPersona');
        $arrayParamValidaIdentifica = array(
                                                'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                'strIdentificacionCliente'  => $strIdentificacion,
                                                'intIdPais'                 => $intIdPais,
                                                'strCodEmpresa'             => $intIdEmpresa
                                            );
        $strMensaje            = $objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica);
        $arrayPersona          = false;
        
        if($strMensaje == '')
        {
            $objListaPersonal = $objRepositorio->findPersonaPorIdentificacion($strTipoIdentificacion, $strIdentificacion);
            
            if($objListaPersonal)
            {
                foreach($objListaPersonal as $entityPersonal)
                {
                    $objListaPersonaEmpresa  = $objRepositorio->getEmpresasExternas($intIdEmpresa, 'Todos', $entityPersonal->getId());
                    $objListaPersonaEmpleado = $objRepositorio->findPersonasXTipoRol("Empleado","", $intIdEmpresa, "", "");
    
                    if(!empty($objListaPersonaEmpresa))
                    {
                        $strMensaje   = 'La identificación corresponde a una Empresa Externa';
                        $arrayPersona = false;
                        break;
                    }
                    if(!empty($objListaPersonaEmpleado))
                    {
                        $strMensaje   = 'La identificación corresponde a un Empleado Activo actualmente';
                        $arrayPersona = false;
                        break;
                    }
                    
                    $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->getPersonaEmpresaRolPorPersonaPorTipoRol($entityPersonal->getId(),
                                                                                                     'Empleado', $intIdEmpresa);
                    $arrayPersona[] = array('id'                   => $entityPersonal->getId(),
                                            'personaEmpresaRolId'  => $entityPersonaEmpresaRol ? $entityPersonaEmpresaRol->getId() : 0,
                                            'nombres'              => $entityPersonal->getNombres(),
                                            'apellidos'            => $entityPersonal->getApellidos(),
                                            'tituloId'             => $entityPersonal->getTituloId() ? ($entityPersonal->getTituloId()->getId() ?
                                                                      $entityPersonal->getTituloId()->getId() : '') : '',
                                            'genero'               => $entityPersonal->getGenero(),
                                            'estadoCivil'          => $entityPersonal->getEstadoCivil() ? $entityPersonal->getEstadoCivil() : '',
                                            'fechaNacimiento_anio' => $entityPersonal->getFechaNacimiento() ?
                                                                      strval(date_format($entityPersonal->getFechaNacimiento(), "Y")) : '',
                                            'fechaNacimiento_mes'  => $entityPersonal->getFechaNacimiento() ?
                                                                      strval(date_format($entityPersonal->getFechaNacimiento(), "m")) : '',
                                            'fechaNacimiento_dia'  => $entityPersonal->getFechaNacimiento() ?
                                                                      strval(date_format($entityPersonal->getFechaNacimiento(), "d")) : '',
                                            'nacionalidad'         => $entityPersonal->getNacionalidad(),
                                            'direccion'            => $entityPersonal->getDireccion());
                }
            }
        }
        $objResponse = new Response(json_encode(array('persona' => $arrayPersona, 'msg'=>$strMensaje)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    
    
  
    /**
     * @Secure(roles="ROLE_171-3998")
     * 
     * Documentación para el método 'newArchivoDigitalAction'.
     * 
     * Función para el ingreso de Nuevos Archivos Digitales asociados al Empleado.
     * 
     * @param interger $id // id de InfoPersonaEmpresaRol   
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */
    public function newArchivoDigitalAction($id)
    {
              
        $em                          = $this->getDoctrine()->getManager();		
        $emGeneral                   = $this->getDoctrine()->getManager('telconet_general');		
        
        $objPersonaEmpresaRol        = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id);	    
        
        	
        $arrayTipoDocumentos = array(); 
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findBy(array(
                                                                                                           'estado'           => "Activo",
                                                                                                           'visibleEnPersona' => 'S'
                                                                                                          ));                   
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }

        $form_documentos                           = $this->createForm(new InfoDocumentoType(array(
                                                                                              'validaFile'                 =>true,
                                                                                              'validaFechaPublicacionHasta'=>true,
                                                                                              'arrayTipoDocumentos'        =>$arrayTipoDocumentos
                                                                                             )
                                                                                            ), new InfoDocumento());
        $arrayParametros                                = array('form_documentos' => $form_documentos->createView());   
        
        
        $arrayParametros['arrayTipoDocumentos']         = $arrayTipoDocumentos;
        $arrayParametros['objPersonaEmpresaRol']        = $objPersonaEmpresaRol;
        $arrayParametros['estadoPersonaEmpresaRol']     = $objPersonaEmpresaRol->getEstado();

         return $this->render('administracionBundle:PersonaEmpleado:newArchivoDigital.html.twig',$arrayParametros);       
    }
    
    
    
    /**
     * @Secure(roles="ROLE_171-3999")
     * 
     * Documentación para el método 'eliminarDocumentoAjaxAction'.
     * 
     * Método encargado de eliminar individualmente o masivamente documentos a partir del o los ids de la referencia enviada.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */
    public function eliminarDocumentoAjaxAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest  = $this->get('request');
        $parametro   = $objRequest->get('id');
        
        /*
         * tipo=1 Eliminar un documento por medio de su id
         * tipo=2 Eliminación Masiva de documentos
         * 
         */
        $tipo = $objRequest->get('tipo');
        
        if($parametro)
        {
            $arrayValor = explode("|",$parametro);
        }
        else
        {
            $parametro  = $objRequest->get('param');
           
            $arrayValor = explode("|",$parametro);
        } 

        $emComunicacion  = $this->getDoctrine()->getManager("telconet_comunicacion");
        $documentoRelacion    = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($arrayValor[0]);
        $idPersonaEmpresaRolId=$documentoRelacion->getPersonaEmpresaRolId();
        $strMensajeError = "";
        try
        {
            foreach($arrayValor as $id)
            {            
                $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);                                              
                if( $objInfoDocumento )
                {                                  
                     /* @var $servicePersonaEmpleado \telconet\administracionBundle\Service\PersonaEmpleadoService */
                    $servicePersonaEmpleado = $this->get('administracion.PersonaEmpleado');
                    $entity                 = $servicePersonaEmpleado->eliminarDocumento($id);
                 }
                 else
                 {
                     $strMensajeError.="No existe el documento con id ".$id." <br>";
                 }

            }

            if($tipo==1)
            {
                return $this->redirect($this->generateUrl('personaempleado_show', array('id'=>$idPersonaEmpresaRolId))); 
            }
            else if($tipo==2)
            {
                return $objResponse->setContent('La eliminacion fue exitosa');
            }
   
        } 
        catch (Exception $e) 
        {
            if($tipo==1)
            {
                $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                return $this->redirect($this->generateUrl('personaempleado_show', array('id'=>$idPersonaEmpresaRolId)));
            }
            else if($tipo==2)
            {
                return $objResponse->setContent($strMensajeError);
            }
        }                 
    } 
   
    /**
     * 
     * @Secure(roles="ROLE_171-3997")
     * 
     * Documentación para el método 'showDocumentosEmpleadoAction'.
     * 
     * Función en Ajax que lista los archivos digitales asociados al empleado.
     * 
     * @param integer $id // id de InfoPersonaEmpresaRol
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */      
    public function showDocumentosEmpleadoAction($id) 
    {
        $objRequest  = $this->getRequest();
        $start       = $objRequest->get('start', 0);
        $limit       = $objRequest->get('limit', 10);
        $response    = new Response();
        $response->headers->set('Content-type', 'text/json'); 
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');		
        $emComunicacion   = $this->getDoctrine()->getManager('telconet_comunicacion');

        $objInfoDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
        $objEntities              = $objInfoDocumentoRelacion->findBy(array('personaEmpresaRolId' => $id,'estado' => 'Activo'), 
                                                                      array('id' => 'DESC'), $limit, $start);
        $intTotal                 = $objInfoDocumentoRelacion->findBy(array('personaEmpresaRolId' => $id,'estado' => 'Activo'));
             
        $arrayResponse          = array();
        $arrayResponse['total'] = count($intTotal);
        $arrayResponse['logs']  = array();
        
        foreach ($objEntities as $entity) 
		{
            $arrayEntity                             = array();
            $arrayEntity['id']                       = $entity->getDocumentoId();
            
            $infoDocumento=$emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($entity->getDocumentoId());
            $arrayEntity['ubicacionLogicaDocumento'] = $infoDocumento->getUbicacionLogicaDocumento();
            
            $objTipoDocumentoGeneral                 = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                 ->find($infoDocumento->getTipoDocumentoGeneralId());                                                                                                                                    
            
            if( $objTipoDocumentoGeneral != null )
            {       
                $urlVerDocumento                     = $this->generateUrl('personaempleado_descargarDocumento', 
                                                                           array('id' => $entity->getDocumentoId()));                             
                                             
                $arrayEntity['tipoDocumentoGeneral'] = $objTipoDocumentoGeneral->getDescripcionTipoDocumento();
               
            } 

            $arrayEntity['feCreacion']            = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
            if($infoDocumento->getFechaPublicacionHasta())
            {
                $arrayEntity['feCaducidad'] = $infoDocumento->getFechaPublicacionHasta()->format('d-m-Y');
            }
            else
            {
                $arrayEntity['feCaducidad']       = "";
            }
            
            $arrayEntity['usrCreacion']           = $entity->getUsrCreacion();     
            $arrayEntity['linkVerDocumento']      = $urlVerDocumento;            
            $arrayResponse['logs'][]              = $arrayEntity;
        }
        
        $response->setContent(json_encode($arrayResponse));
        return $response;
    }


        /**
     * 
     * Documentación para el método 'descargarDocumentoAction'.
     * 
     * Método encargado de descargar los documentos a partir del id de la referencia enviada.
     * 
     * @param integer $id // id de InfoDocumento
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */ 
    public function descargarDocumentoAction($id)
    {
        $em                = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento  = $em->getRepository('schemaBundle:InfoDocumento')->find($id);               
        $path              = $objInfoDocumento->getUbicacionFisicaDocumento();        
        $path_telcos       = $this->container->getParameter('path_telcos');
        $content           = file_get_contents($path_telcos.$path);        
        $response          = new Response();
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$objInfoDocumento->getUbicacionLogicaDocumento());
        $response->setContent($content);
        return $response;       
    }
    
    /**
     * 
     * Documentación para el método 'eliminarDocumentoAction'.
     * 
     * Método encargado de eliminar documentos a partir del id de la referencia enviada
     * 
     * @param integer $id // id de InfoDocumento
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */ 
    public function eliminarDocumentoAction($id)
    {   
        $emComunicacion   = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objInfoDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);           
        if ( !$objInfoDocumento ) 
        {
            throw $this->createNotFoundException('Unable to find InfoDocumento entity.');
        }                        
        if( $objInfoDocumento )
        {
            $documentoRelacion    = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($id);
            $idPersonaEmpresaRolId=$documentoRelacion->getPersonaEmpresaRolId();
            try
            {
                /* @var $servicePersonaEmpleado \telconet\administracionBundle\Service\PersonaEmpleadoService */
                $servicePersonaEmpleado = $this->get('administracion.PersonaEmpleado');
                $entity                 = $servicePersonaEmpleado->eliminarDocumento($id);                
                return $this->redirect($this->generateUrl('personaempleado_show', array('id'=>$idPersonaEmpresaRolId)));        
            }
            catch (\Exception $e)
            {   
                $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                return $this->redirect($this->generateUrl('personaempleado_show', array('id'=>$idPersonaEmpresaRolId)));        
            }
        }      
    }
  
    
    
    /**
     * 
     * Documentación para el método 'guardarArchivoDigitalAction'.
     * 
     * Método que Guarda Archivos Digitales agregados al empleado
     * 
     * @param request $request
     * @param integer $id // id de InfoPersonaEmpresaRol 
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */     
    public function guardarArchivoDigitalAction(Request $objRequest,$id)
    {       
        $intClientIp                = $objRequest->getClientIp();
        $session                    = $objRequest->getSession();
        $strUsrCreacion             = $session->get('user');                     
        $datos_form_files           = $objRequest->files->get('infodocumentotype');               
        $datos_form_Doc             = $objRequest->get('infodocumentotype');
        $arrayTipoDocumentos        = array ();
        $arrayFechasHastaDocumentos = array ();

        foreach ($datos_form_Doc as $key => $arrayAttr)
        {   
            if($key=='tipos')
            {
                foreach ( $arrayAttr as $key_tipo => $value)
                {                     
                    $arrayTipoDocumentos[$key_tipo]=$value;                
                }
            }
            else if($key=="fechasPublicacionHasta")
            {
                foreach ( $arrayAttr as $key_fecha => $value)
                {                     
                    if($arrayAttr[$key_fecha]['year'] && $arrayAttr[$key_fecha]['month'] && $arrayAttr[$key_fecha]['day'])
                    {
                        $fechaHasta=date_create($arrayAttr[$key_fecha]['year'].'-'.$arrayAttr[$key_fecha]['month'].'-'.$arrayAttr[$key_fecha]['day']);
                        $arrayFechasHastaDocumentos[$key_fecha]=$fechaHasta;
                    }
                }
            }
        } 

        $datos_form = array_merge(               
                                    $objRequest->get('personaempleadoextratype'),
                                    array('datos_form_files'           => $datos_form_files),
                                    array('arrayTipoDocumentos'        => $arrayTipoDocumentos),
                                    array('arrayFechasHastaDocumentos' => $arrayFechasHastaDocumentos)
                                 );         
        try
        {
            /* @var $servicePersonaEmpleado \telconet\administracionBundle\Service\PersonaEmpleadoService */
            $servicePersonaEmpleado = $this->get('administracion.PersonaEmpleado');
            //retorna un objInfoDocumentoRelacion
            $entity                 = $servicePersonaEmpleado->guardarArchivoDigital($id, $strUsrCreacion, $intClientIp, $datos_form);
            return $this->redirect($this->generateUrl('personaempleado_newArchivoDigital', array('id' => $id)));
            
        }
        catch (\Exception $e)
        {   
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('personaempleado_newArchivoDigital', array('id'=>$id)));
        }
    }
    
    
    /**
     * 
     * Documentación para el método 'validarDocumentosObligatoriosAjaxAction'.
     * 
     * Método que valida que los archivos obligatorios para un empleado de acuerdo a su cargo se hayan subido.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-12-2015
     */    
    public function validarDocumentosObligatoriosAjaxAction()
    {
        $objRequest                = $this->get('request');
        $idPersonaEmpresaRol       = $objRequest->get('idPersonaEmpresaRol');
        $strDatosFormTipos         = $objRequest->get('strIdsTiposDocsASubir');
        $strDatosFormFechasHasta   = $objRequest->get('strFechasHastaDocsPorSubir');
        

        $em               = $this->getDoctrine()->getManager();	
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');
        $emComunicacion   = $this->getDoctrine()->getManager("telconet_comunicacion");
        
        $arrayIdsTiposDocsActuales              = array();
        $arrayCodigoTipoDocumentosObligatorios  = array();
        $arrayTiposDocumentoBase                = array();
        $arrayFechasObligatorias                = array();
        
        $strMensaje     = '';
        $strCargoTelcos = '';
        
        $objIdsTiposDocsActuales = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                  ->getIdsTiposDocumentosArchivosSubidosByPersonaEmpresaRol($idPersonaEmpresaRol);
        
        if($objIdsTiposDocsActuales)
        {
            foreach ( $objIdsTiposDocsActuales as $objIdTipoDocActual)
            {   
                foreach ( $objIdTipoDocActual as $keyObjIdTipoDocActual=> $valObjIdTipoDocActual)
                {   
                    $arrayIdsTiposDocsActuales[] = $valObjIdTipoDocActual; 
                }
            }
        }
       
        $arrayIdsTiposDocumentosASubir    = explode("/",$strDatosFormTipos);
        $arrayFechasHastaDocumentosASubir = explode("/",$strDatosFormFechasHasta);
        
        
        $strCargoNaf              = strtolower($em->getRepository('schemaBundle:AdmiRol')
                                                  ->getRolEmpleadoEmpresa( array('usuario' => $idPersonaEmpresaRol) ));

        
        $objCaracteristica        = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                       ->findOneBy( array( 'descripcionCaracteristica' => 'CARGO',
                                                           'estado'                    => 'Activo' ) );
         
        $objInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($idPersonaEmpresaRol);

        $arrayTmpParametrosCaracteristica = array( 
                                                    'estado'              => 'Activo',
                                                    'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                    'caracteristicaId'    => $objCaracteristica
                                                 );
        $objPersonaEmpresaRolCarac = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy( $arrayTmpParametrosCaracteristica );
        if($objPersonaEmpresaRolCarac)
        {
            $strCargoTelcos=strtolower($objPersonaEmpresaRolCarac->getValor());
        }

        switch(true)
        {
            case ( $strCargoNaf=="chofer" || $strCargoTelcos=="chofer") :
                $arrayCodigoTipoDocumentosObligatorios=array('CED','LIC','CDC');
            break;
            default:
                $arrayCodigoTipoDocumentosObligatorios=array('CED');
            break;
        }
  
        if(!empty($arrayCodigoTipoDocumentosObligatorios))
        {
            $objTiposDocumentosObligatorios  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                         ->findByCodigosTipoDocumento($arrayCodigoTipoDocumentosObligatorios);
            if($objTiposDocumentosObligatorios)
            {
                foreach ( $objTiposDocumentosObligatorios as $objTipoDocumento )
                {   
                   $arrayIdsTipoDocumentosBase[]                       = $objTipoDocumento->getId(); 
                   $arrayTiposDocumentoBase[$objTipoDocumento->getId()]=
                                                                array(
                                                                'codigoTipoDocumento'     =>$objTipoDocumento->getCodigoTipoDocumento(),
                                                                'descripcionTipoDocumento'=>$objTipoDocumento->getDescripcionTipoDocumento()
                                                                   );
                   
                   
                   if(in_array($objTipoDocumento->getId(),$arrayIdsTiposDocumentosASubir))
                   {
                       $arrayPosicionesTiposObligatorios = array_keys($arrayIdsTiposDocumentosASubir, $objTipoDocumento->getId());
                       
                       for($i=0;$i<count($arrayPosicionesTiposObligatorios);$i++)
                       {
                           $arrayFechasObligatorias[]=array(
                                    'posicion'                 => $arrayPosicionesTiposObligatorios[$i],
                                    'descripcionTipoDocumento' => $objTipoDocumento->getDescripcionTipoDocumento(),
                                    'valorFechaDocumento'      => $arrayFechasHastaDocumentosASubir[$arrayPosicionesTiposObligatorios[$i]]
                                               );
                       }
                       
                       
                   }

                }
            }
            
            
            $arrayIdsTiposDocumentosFinales=array_merge($arrayIdsTiposDocsActuales,$arrayIdsTiposDocumentosASubir);
            
            /* @var $objServicioInfoDocumento \telconet\comunicacionesBundle\Service\InfoDocumentoService */
            $objServicioInfoDocumento = $this->get('comunicaciones.InfoDocumento');
            $arrayValidacion          = $objServicioInfoDocumento->validacionesDocumentosObligatorios($arrayTiposDocumentoBase,
                                                                                                      $arrayIdsTiposDocumentosFinales,
                                                                                                      $arrayIdsTipoDocumentosBase); 
            
            if(!$arrayValidacion['boolOk'])    
            {
                $strMensaje .= $arrayValidacion['strMsg'];
            }
            
            
            $arrayValidacionFechasObligatorias = $objServicioInfoDocumento->validacionesFechasDocumentosObligatorios($arrayFechasObligatorias); 
            
            if(!$arrayValidacionFechasObligatorias['boolOk']) 
            {
                if($strMensaje!="")
                {
                    $strMensaje .="<br>";
                }
                $strMensaje .= $arrayValidacionFechasObligatorias['strMsg'];
            }
            
        }
        
        $objResponse = new Response(json_encode(array('msg'=>$strMensaje)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse; 
    }
    
    
    
}



