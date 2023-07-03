<?php
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoTelconetTmpUserAAAA;
use telconet\schemaBundle\Form\PersonalExternoType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class PersonalExternoController extends Controller implements TokenAuthenticatedController
{
    const strCaracteristicaMetaBruta  = 'META BRUTA';
    const strCaracteristicaMetaActiva = 'META ACTIVA';
       
    /**
     * @Secure(roles="ROLE_182-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * Método inicial que consulta la lista de personales externos
     * 
     * @return Response retorna el resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     */
    public function indexAction()
    {
        $objManagerSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $objManagerSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("182", "1");
        
        if(true === $this->get('security.context')->isGranted('ROLE_182-1'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-1'; //INDEX PERSONAL EXTERNO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-3'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-3'; //CREAR PERSONAL EXTERNO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-4'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-4'; //EDITAR PERSONAL EXTERNO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-6'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-6'; //CONSULTAR PERSONAL EXTERNO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-8'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-8'; //ELIMINAR PERSONAL EXTERNO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-2980'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-2980'; //ASIGNAR META PERSONAL EXTERNO
        }
        return $this->render('administracionBundle:PersonalExterno:index.html.twig', 
                              array('item'                        => $entityItemMenu, 
                                    'rolesPermitidos'             => $arregloRolesPermitidos,
                                    'strCaracteristicaCargo'      => $this->strCaracteristicaCargo,
                                    'strCaracteristicaMetaBruta'  => self::strCaracteristicaMetaBruta,
                                    'strCaracteristicaMetaActiva' => self::strCaracteristicaMetaActiva));
    }
    
    /**
     * @Secure(roles="ROLE_182-6")
     * 
     * Documentación para el método 'showAction'.
     * 
     * Renderiza a la vista de visualización de la información de un personal externo
     * 
     * @param mixed $id The entity id
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 07-04-2017 - Se muestra en el twig el departamento asociado al personal externo. Adicional se envía al twig el prefijo de la 
     *                           empresa en sessión
     */
    public function showAction($id)
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $objManager              = $this->getDoctrine()->getManager();
        $objManagerSeguridad     = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu          = $objManagerSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("182", "1");
        $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);
        
        $strDepartamentoEmpresaSession = "";
        $emGeneral                     = $this->getDoctrine()->getManager('telconet_general');
        
        if( is_object($entityPersonaEmpresaRol) )
        {
            $intIdDepartamentoEmpresaSesion = $entityPersonaEmpresaRol->getDepartamentoId() ? $entityPersonaEmpresaRol->getDepartamentoId()
                                              : 0;
            
            if( !empty($intIdDepartamentoEmpresaSesion) )
            {
                $objAdmiDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->findOneById($intIdDepartamentoEmpresaSesion);
                
                if( is_object($objAdmiDepartamento) )
                {
                    $strDepartamentoEmpresaSession = $objAdmiDepartamento->getNombreDepartamento() ? $objAdmiDepartamento->getNombreDepartamento()
                                                     : '';
                }//( is_object($objAdmiDepartamento) )
            }//( !empty($intIdDepartamentoEmpresaSesion) )
        }
        else
        {
            throw new \Exception("No existe relación de Personal Externo");
        }
        
        $entityPersonalExterno = $objManager->getRepository('schemaBundle:InfoPersona')
                                            ->findOneById($entityPersonaEmpresaRol->getPersonaId()->getId());
        if(!$entityPersonalExterno)
        {
            throw new \Exception("Entity EntityPersonalExterno doesn't exist.");
        }
        $arregloActivo = array('Activo');
        $entityPersonaEmpresaRolCarac = $objManager->getRepository('schemaBundle:InfoPersona')
                                                   ->getCaracteristicaPersonaEmpresaRol($id, 'EMPRESA EXTERNA', $arregloActivo);
        if(!$entityPersonaEmpresaRolCarac)
        {
            throw new \Exception("El personal externo no tiene una característica de empresa externa definida.");
        }
        $entityEmpresaExternaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->findOneById($entityPersonaEmpresaRolCarac->getValor());
        if(!$entityEmpresaExternaRol)
        {
            throw new \Exception("El personal externo no tiene una empresa externa asociada.");
        }
        $entityEmpresaExterna  = $objManager->getRepository('schemaBundle:InfoPersona')
                                            ->findOneById($entityEmpresaExternaRol->getPersonaId()->getId());
        if(!$entityEmpresaExterna)
        {
            throw new \Exception("La empresa externa no tiene una persona asociada.");
        }
        
        //Obtiene el historial del personal externo
        $listaHistorial = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findHistorialPorPersonaEmpresaRol($id);
        //Recorre el historial y separa en arreglos cada estado
        $intIndiceI   = 0;
        $strCreacion  = null;
        $strEliminado = null;
        $strUltMod    = null;
        
        foreach($listaHistorial as $entityHistorial)
        {
            if($intIndiceI == 0)
            {
                $strCreacion = array('estado'     => $entityHistorial->getEstado(),     'usrCreacion' => $entityHistorial->getUsrCreacion(),
                                     'feCreacion' => $entityHistorial->getFeCreacion(), 'ipCreacion'  => $entityHistorial->getIpCreacion());
            }
            if($intIndiceI > 0)
            {
                if($entityHistorial->getEstado() == 'Eliminado')
                {
                    $strEliminado = array('estado'     => $entityHistorial->getEstado(),     'usrCreacion' => $entityHistorial->getUsrCreacion(),
                                          'feCreacion' => $entityHistorial->getFeCreacion(), 'ipCreacion'  => $entityHistorial->getIpCreacion());
                }
                else
                {
                    $strUltMod = array('estado'     => $entityHistorial->getEstado(),     'usrCreacion' => $entityHistorial->getUsrCreacion(),
                                       'feCreacion' => $entityHistorial->getFeCreacion(), 'ipCreacion'  => $entityHistorial->getIpCreacion());
                }
            }
            $intIndiceI++;
        }
        if($entityPersonaEmpresaRol->getEstado() != 'Eliminado')
        {
            $strEliminado = null;
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-4'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-4'; //EDITAR PERSONAL EXTERNO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_182-8'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-8'; //ELIMINAR PERSONAL EXTERNO
        }
        return $this->render('administracionBundle:PersonalExterno:show.html.twig', 
                             array('item'                          => $entityItemMenu,
                                   'rolesPermitidos'               => $arregloRolesPermitidos,
                                   'personaEmpresaRol'             => $entityPersonaEmpresaRol,
                                   'creacion'                      => $strCreacion,
                                   'ultMod'                        => $strUltMod,
                                   'eliminado'                     => $strEliminado,
                                   'personalexterno'               => $entityPersonalExterno,
                                   'empresaExterna'                => $entityEmpresaExterna->getRazonSocial(),
                                   'flag'                          => $objRequest->get('flag'),
                                   'strDepartamentoEmpresaSession' => $strDepartamentoEmpresaSession,
                                   'strPrefijoEmpresa'             => $strPrefijoEmpresa));
    }
    
    /**
     * @Secure(roles="ROLE_182-2")
     * 
     * Documentación para el método 'newAction'.
     * 
     * Método que renderiza la vista de creación de un nuevo personal externo
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 11-04-2017 - Se envía la empresa en sessión al twig
     */
    public function newAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        
        $objManagerSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $objManagerSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("182", "1");
        $entityPersona  = new InfoPersona();
        $objForm        = $this->createForm(new PersonalExternoType(), $entityPersona);

        return $this->render('administracionBundle:PersonalExterno:new.html.twig', array('item'              => $entityItemMenu,
                                                                                         'personalexterno'   => $entityPersona,
                                                                                         'form'              => $objForm->createView(),
                                                                                         'strPrefijoEmpresa' => $strPrefijoEmpresa));		
    }
    
    /**
     * @Secure(roles="ROLE_182-3")
     * 
     * Documentación para el método 'createAction'.
     * 
     * Método que ejecuta la acción de crear un nuevo personal externo.
     * 
     * @return Render En caso de éxito renderiza showAction.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 07-04-2016 - Se guarda el departamento de la empresa en sessión seleccionado por el usuario a la cual va a pertenecer el personal
     *                           externo.
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.3 02-01-2017 - Se reciben de manera separada los nombre y apellidos para posterior generacion de login.
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.2 01-03-2018 Se agrega la oficina de la empresa externa, creacion de cuenta de correo y envio de notificacion.
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.3 18-10-2018 Se modifica metodo para verificar la existencia del rol de personal externo.
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.4 03-12-2018 Se utiliza repositorio que considere estados.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.5 13-10-2019 Se solicita enviar el contacto CORREO ELECTRÓNICO en el arbol LDAP-PERSONAL EXTERNO
     */
    public function createAction()
    {
        $objPeticion         = $this->getRequest();
        $objSession          = $objPeticion->getSession();
        $objRespuesta        = new Response();
        $arrayParametrosLdap = array();
        $arrayParametrosArea = array();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        
        $intPersonalExternoId  = $objPeticion->get("intPersonalExternoId");
        $strTipoIdentificacion = $objPeticion->get("strTipoIdentificacion");
        $strIdentificacion     = $objPeticion->get("strIdentificacion");
        $strPrimerNombre       = $objPeticion->get("strPrimerNombre");
        $strSegundoNombre      = $objPeticion->get("strSegundoNombre");
        $strNombres            = $strPrimerNombre.' '.$strSegundoNombre;
        $strGenero             = $objPeticion->get("strGenero");
        $strTitulo             = $objPeticion->get("strTitulo");
        $strEstadoCivil        = $objPeticion->get("strEstadoCivil");
        $strPrimerApellido     = $objPeticion->get("strPrimerApellido");
        $strSegundoApellido    = $objPeticion->get("strSegundoApellido");
        $strApellidos          = $strPrimerApellido.' '.$strSegundoApellido;
        $datFechaInstitucionY  = $objPeticion->get("datFechaInstitucionY");
        $datFechaInstitucionM  = $objPeticion->get("datFechaInstitucionM");
        $datFechaInstitucionD  = $objPeticion->get("datFechaInstitucionD");
        $strNacionalidad       = $objPeticion->get("strNacionalidad");
        $strDireccion          = $objPeticion->get("strDireccion");
        $intEmpresaExterna     = $objPeticion->get("intEmpresaExterna");
        $listaFormasContacto   = $objPeticion->get("lstFormasContacto");
        $intIdArea             = $objPeticion->get("intIdArea");
        $strOficinaId          = $objPeticion->get("strOficina");
        $strEmpresa             = $objSession->get('prefijoEmpresa');
        $servicePersonalExterno = $this->get('administracion.PersonalExterno');
        $serviceEjecutaJar      = $this->get('administracion.EjecucionJar');
        $strUrlMiddleware       = $this->container->getParameter('ws_ldap_personal_externo_url');
        $strUrlMiddlewareToken  = $this->container->getParameter('seguridad.token_authentication_url');
        $strTokenUsername       = $this->container->getParameter('seguridad.token_username_telcos');
        $strTokenPassword       = $this->container->getParameter('seguridad.token_password_telcos');
        $strTokenSource         = $this->container->getParameter('seguridad.token_source_telcos');
        $strPathTelcos          = $this->container->getParameter('path_telcos');
        $emAAAA                 = $this->getDoctrine()->getManager('telconet_aaaa');
        $strCtaCorreo           = '';
       
        $intIdDepartamentoEmpresaSession = $objPeticion->get("intIdDepartamentoEmpresaSession") 
                                           ? $objPeticion->get("intIdDepartamentoEmpresaSession") : null;
        
        $arregloValidacion     = $this->validacionesCreacionActualizacion($strTipoIdentificacion , $strIdentificacion, $strNombres, 
                                                                          $strDireccion          , $strApellidos     , $strGenero, 
                                                                          $strTitulo             , $intEmpresaExterna);
        if(!$arregloValidacion['boolOk'])    
        {
            return $objRespuesta->setContent(json_encode(array('estatus' => false, 'msg' => $arregloValidacion['strMsg'])));
        }
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $arregloFormasContactoTmp = explode(",", $listaFormasContacto);
        $intIndiceA = 0;
        $intIndiceX = 0;
        $arregloFormasContacto = array();
        for($i = 0; $i < count($arregloFormasContactoTmp); $i++)
        {
            if($intIndiceA == 3)
            {
                $intIndiceA = 0;
                $intIndiceX++;
            }
            if($intIndiceA == 1)
            {
                $arregloFormasContacto[$intIndiceX]['formaContacto'] = $arregloFormasContactoTmp[$i];
            }
            if($intIndiceA == 2)
            {
                $arregloFormasContacto[$intIndiceX]['valor']         = $arregloFormasContactoTmp[$i];
            }
            $intIndiceA++;
        }
        $intIdEmpresa   = $objPeticion->getSession()->get('idEmpresa');
        $strUsrCreacion = $objPeticion->getSession()->get('user');
        $strUsrUltMod   = $objPeticion->getSession()->get('user');
        $emComercial->getConnection()->beginTransaction();
        try 
        {
            $intIdPersona = (isset($intPersonalExternoId) ? ($intPersonalExternoId!='' ? $intPersonalExternoId : '') : '');
            if($intIdPersona == '')
            {
                $entityPersona = new InfoPersona();
                $entityPersona->setFeCreacion(new \DateTime('now'));
                $entityPersona->setUsrCreacion($strUsrCreacion);
                $entityPersona->setIpCreacion($objPeticion->getClientIp());
            }
            else
            {
                $entityPersona = $emComercial->find('schemaBundle:InfoPersona', $intIdPersona);
                if(null == $entityPersona) 
                {
                    throw new \Exception('No existe el Personal Externo que se quiere modificar');
                }
                
            }
            $entityPersona->setTipoIdentificacion($strTipoIdentificacion);
            $entityPersona->setIdentificacionCliente($strIdentificacion);
            $entityPersona->setTipoTributario("NAT");
            $entityPersona->setNombres(strtoupper(trim(preg_replace('/\s+/', ' ', $strNombres))));
            $entityPersona->setApellidos(strtoupper(trim(preg_replace('/\s+/', ' ', $strApellidos))));
            $entityPersona->setDireccion(strtoupper(trim(preg_replace('/\s+/', ' ', $strDireccion))));
            $entityPersona->setNacionalidad($strNacionalidad);
            $entityPersona->setGenero($strGenero);
            $entityPersona->setEstado('Activo');
            
            $entityPersona->setOrigenProspecto('N');
            if($strEstadoCivil != '')
            {
                $entityPersona->setEstadoCivil($strEstadoCivil);
            }
            
            if($datFechaInstitucionY != '' && $datFechaInstitucionM != '' && $datFechaInstitucionD != '')
            {
                $entityPersona->setFechaNacimiento(date_create($datFechaInstitucionY . '-' . $datFechaInstitucionM . '-' . $datFechaInstitucionD));
            }
            $entityTitulo = $emComercial->getRepository('schemaBundle:AdmiTitulo')->find($strTitulo);
            
            if($entityTitulo)
            {
                $entityPersona->setTituloId($entityTitulo);
            }
            $emComercial->persist($entityPersona);
            //ASIGNA ROL DE "PERSONAL EXTERNO" A LA PERSONA EXTERNA
            $strRol = 'Personal Externo';
            $entityRolEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                           ->findPorNombreRolPorNombreTipoRolPorEmpresa($strRol, $strRol, $intIdEmpresa);
            if(!$entityRolEmpresa)
            {
                return $objRespuesta->setContent(json_encode(array('estatus' => false, 
                                                                   'msg'     => 'No se ha definido el rol de Personal Externo','id' => '0')));
            }
            $entityPersonaEmpresaRol = null;
            if($intIdPersona != '')
            {
                $entityPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->getPersonaEmpresaRolPorPersonaPorTipoRolParaNew($intIdPersona, 'Personal Externo', $intIdEmpresa);
            }
       
            if(!$entityPersonaEmpresaRol)
            {
                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                $entityPersonaEmpresaRol->setEmpresaRolId($entityRolEmpresa);
                $entityPersonaEmpresaRol->setPersonaId($entityPersona);
                $entityOficinaId = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                               ->find($strOficinaId);
                if (!$entityOficinaId)
                {
                    throw new \Exception('No se ha definido la Oficina');
                }
                $entityPersonaEmpresaRol->setOficinaId($entityOficinaId);
                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRol->setUsrCreacion($strUsrCreacion);
            }
            $entityPersonaEmpresaRol->setEstado('Activo');
            $entityPersonaEmpresaRol->setDepartamentoId($intIdDepartamentoEmpresaSession);
            $emComercial->persist($entityPersonaEmpresaRol);
            $emComercial->flush();
            $strCaracteristica    = 'EMPRESA EXTERNA';
            $strEstado            = 'Activo';
            $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
            if(!$entityCaracteristica)
            {
                throw new \Exception("No se ha definido la característica $strCaracteristica.");
            }
            //VERIFICO QUE YA TENGA UNA CARACTERISTICA "EMPRESA EXTERNA" ASIGNADA, SINO SE CREA UNA NUEVA.
            $arrayCriterios = array ('caracteristicaId'    => $entityCaracteristica->getId(),
                                       'personaEmpresaRolId' => $entityPersonaEmpresaRol->getId());
            $entityPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy($arrayCriterios);
            if(!$entityPersonaEmpresaRolCarac)
            {
                $entityPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                $entityPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entityPersonaEmpresaRolCarac->setCaracteristicaId($entityCaracteristica);
                $entityPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRolCarac->setUsrCreacion($strUsrCreacion);
                $entityPersonaEmpresaRolCarac->setIpCreacion($objPeticion->getClientIp());
            }
            $entityPersonaEmpresaRolCarac->setValor($intEmpresaExterna);
            $entityPersonaEmpresaRolCarac->setEstado($strEstado);

            $strCaracteristicaCat    = 'CATEGORIA EMPRESA EXTERNA';
            $entityCaracteristicaCat = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->getCaracteristicaPorDescripcionPorEstado($strCaracteristicaCat, $strEstado);
            if(!$strCaracteristicaCat)
            {
                throw new \Exception("No se ha definido la característica $strCaracteristica.");
            }
            $arrayCriteriosCat = array ('caracteristicaId'    => $entityCaracteristicaCat->getId(),
                                          'personaEmpresaRolId' => $intEmpresaExterna);
            $entityPersonaEmpresaRolCaracCat = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                           ->findOneBy($arrayCriteriosCat);
            if(!is_object($entityPersonaEmpresaRolCaracCat))
            {
                   throw new \Exception("No se ha definido característica empresa externa.");
            }
            $entityPersonaEmpresaRolCarac->setPersonaEmpresaRolCaracId($entityPersonaEmpresaRolCaracCat->getId());
            //
            $emComercial->persist($entityPersonaEmpresaRolCarac);
            $emComercial->flush();

            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL           
            $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
            $entityPersonaHistorial->setEstado($entityPersona->getEstado());
            $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
            $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
            $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entityPersonaHistorial->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($entityPersonaHistorial);
            $emComercial->flush();

            if($intIdPersona != '')
            {
                //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
                $objServicioInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
                $objServicioInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($intIdPersona, $strUsrUltMod);
            }

            //REGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for($i = 0; $i < count($arregloFormasContacto); $i++)
            {
                $strFormaContacto = $arregloFormasContacto[$i]['formaContacto'];
                if($strFormaContacto != '')
                {
                    $entityAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                          ->findPorDescripcionFormaContacto($strFormaContacto);
                    if($entityAdmiFormaContacto == null)
                    {
                        throw new \Exception("No existe la forma de contacto: $strFormaContacto");
                    }
                    $entityPersonaFormaContacto = new InfoPersonaFormaContacto();
                    $entityPersonaFormaContacto->setValor($arregloFormasContacto[$i]['valor']);
                    $entityPersonaFormaContacto->setEstado("Activo");
                    $entityPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                    $entityPersonaFormaContacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entityPersonaFormaContacto->setIpCreacion($objPeticion->getClientIp());
                    $entityPersonaFormaContacto->setPersonaId($entityPersona);
                    $entityPersonaFormaContacto->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($entityPersonaFormaContacto);
                    $emComercial->flush();
                    if($strFormaContacto === 'Correo Electronico')
                    {
                        $strCtaCorreo = $arregloFormasContacto[$i]['valor'];
                    }
                }
            }

            $objEmNaf = $this->getDoctrine()->getManager("telconet_naf");
            $entityEmpresa = $objEmNaf->getRepository('schemaBundle:VEmpresasGrupo')->findOneById($intIdEmpresa);
            if(!is_object($entityEmpresa))
            {
                throw new \Exception("No se encuentra el ID empresa " . $intIdEmpresa);
            }

            $arrayEmpresa                           = ['ou'          => $entityEmpresa->getRazonSocial(), 
                                                       'objectclass' => ['top', 'organizationalUnit']];
            $arrayParametrosLdap['data']['nivel'][] = $arrayEmpresa;

            $arrayEmpresa                           = ['ou'          => $entityPersonaEmpresaRolCaracCat->getValor(), 
                                                       'objectclass' => ['top', 'organizationalUnit']];
            $arrayParametrosLdap['data']['nivel'][] = $arrayEmpresa;

            $entityPersonaEmpresaExternaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                          ->find($intEmpresaExterna);
            if (is_object($entityPersonaEmpresaExternaRol))
            {
                $entityEmpresaExterna  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                     ->find($entityPersonaEmpresaExternaRol->getPersonaId());
            }

            if (is_object($entityEmpresaExterna))
            {
                $arrayEmpresa = ['ou'          => $entityEmpresaExterna->getRazonSocial(), 
                                 'objectclass' => ['top', 'organizationalUnit']];
            }
            $arrayParametrosLdap['data']['nivel'][] = $arrayEmpresa;
            $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        
            $arrayParametrosArea['strEstado']    = $strEstado;
            $arrayParametrosArea['strIdEmpresa'] = $intIdEmpresa;
            $arrayParametrosArea['intIdArea']    = $intIdArea;

            $arrayAreas  = $emGeneral->getRepository('schemaBundle:AdmiArea')->getRegistrosByEmpresa($arrayParametrosArea);
            $arrayEmpresa                           = ['ou'          => $arrayAreas[0]['strNombreArea'], 
                                                       'objectclass' => ['top', 'organizationalUnit']];
            $arrayParametrosLdap['data']['nivel'][] = $arrayEmpresa;

            $arrayParametrosDepto['strEstado']         = $strEstado;
            $arrayParametrosDepto['strIdEmpresa']      = $intIdEmpresa;
            $arrayParametrosDepto['intIdArea']         = $intIdArea;
            $arrayParametrosDepto['intIdDepartamento'] = $intIdDepartamentoEmpresaSession;
            
            $arrayDepartamento  = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                            ->getRegistrosByAreaYEmpresa($arrayParametrosDepto);
            $arrayEmpresa                           = ['ou'          => $arrayDepartamento[0]['strNombreDepartamento'], 
                                                       'objectclass' => ['top', 'organizationalUnit']];
            $arrayParametrosLdap['data']['nivel'][] = $arrayEmpresa;
            $arrayParametrosToken['username']       = $strTokenUsername;
            $arrayParametrosToken['password']       = $strTokenPassword;
            $arrayParametrosToken['source']['name'] = $strTokenSource;
            $arrayParametrosToken['url']            = $strUrlMiddlewareToken;

            $arrayParametrosLoginExterno['strPrimerNombre']    = $strPrimerNombre;
            $arrayParametrosLoginExterno['strSegundoNombre']   = $strSegundoNombre;
            $arrayParametrosLoginExterno['strPrimerApellido']  = $strPrimerApellido;
            $arrayParametrosLoginExterno['strSegundoApellido'] = $strSegundoApellido;
            $arrayParametrosLoginExterno['strIdentificacion']  = $strIdentificacion;
            $arrayParametrosLoginExterno['strUsrCreacion']     = $strUsrCreacion;
            
            $arrayLoginExterno = $servicePersonalExterno->generaLoginPersonaExterno($arrayParametrosLoginExterno);
            error_log('Login: '.$arrayLoginExterno['login']);
            if(isset($arrayLoginExterno['status']) && $arrayLoginExterno['status'] != '200')
            {
                throw new \Exception("Existio un error al generar el login. " . $arrayLoginExterno['msj']);
            }
           
            $entityPersona->setLogin($arrayLoginExterno['login']);
            $emComercial->flush();
            
            $arrayParametrosPassword['op']  = 'password';
            $arrayParametrosPassword['url'] = $strUrlMiddleware;
            $arrayToken = $servicePersonalExterno->middlewareLdapEmpleadoExterno($arrayParametrosToken);
            error_log('$arrayToken: ');
            
            if(isset($arrayToken['status']) && $arrayToken['status'] != '200')
            {
                throw new \Exception("Existio un error al consultar token para generar la clave.");
            }
            $arrayParametrosPassword['source']['name']          = 'TELCOS';
            $arrayParametrosPassword['source']['originID']      = 'nuevoEmpleadoExterno';
            $arrayParametrosPassword['source']['tipoOriginID']  = 'nuevoEmpleadoExterno';
            $arrayParametrosPassword['token'] = $arrayToken['token'];
            $arrayParametrosPassword['user']  = $strUsrCreacion;

            $arrayPassword = $servicePersonalExterno->middlewareLdapEmpleadoExterno($arrayParametrosPassword);
            error_log('$arrayPassword');
            if (isset($arrayPassword['status']) && $arrayPassword['status'] != '200')
            {
                throw new \Exception("Error al generar la clave");
            }
            error_log('generado: '. $arrayPassword['password']);
            $arrayAttrUser = ['uid'           => $arrayLoginExterno['login'],
                              'uidNumber'     => '1000',
                              'cn'            => $strNombres,
                              'sn'            => $strApellidos,
                              'homeDirectory' => '/home/'.$arrayLoginExterno['login'],
                              'gidNumber'     => '1000',
                              'cedula'        => $strIdentificacion,
                              'displayName'   => $strNombres.' '.$strApellidos,
                              'loginShell'    => '/bin/bash',
                              'userPassword'  => $arrayPassword['password'],
                              'mail'          => $strCtaCorreo,
                              'mailBox'       => $arrayLoginExterno['login'],
                              'objectclass'   => ['top', 'person', 'posixAccount', 'organizationalPerson', 'inetOrgPerson', 'telcoPerson']
                            ];
            
            $arrayParametrosLdap['data']['user'][] = $arrayAttrUser;
            
            
            $arrayParametrosLdap['op']= 'nuevoEmpleadoExterno';
            
            $arrayParametrosLdap['source']['name']= 'TELCOS';
            $arrayParametrosLdap['source']['originID']= 'nuevoEmpleadoExterno';
            $arrayParametrosLdap['source']['tipoOriginID']= 'nuevoEmpleadoExterno';

            $arrayParametrosLdap['user']= $strUsrCreacion;
            $arrayParametrosLdap['url'] = $strUrlMiddleware;
            $arrayParametrosLdap['app'] = 'PERSONA_EXTERNO';

            $arrayToken = $servicePersonalExterno->middlewareLdapEmpleadoExterno($arrayParametrosToken);            
            if(isset($arrayToken['status']) && $arrayToken['status'] != '200')
            {
                throw new \Exception("Existio un error al consultar token para crear el registro en LDAP.");
            }

            $arrayParametrosLdap['token'] = $arrayToken['token'];
            $arrayDatos                   = $servicePersonalExterno->middlewareLdapEmpleadoExterno($arrayParametrosLdap);

            if ($arrayDatos['status']== '200')
            {
                $emComercial->getConnection()->commit();
                $emAAAA->beginTransaction();
                try
                {
                    $objTmpUser = $emAAAA->getRepository('schemaBundle:InfoTelconetTmpUserAAAA')
                                         ->findOneByLogin($arrayLoginExterno['login']);
                    if (!$objTmpUser)
                    {
                        error_log('createAction->PersonalExternoController : exiiiiste ');
                        $strLoginAAAA = $arrayLoginExterno['login'];
                        $objTmpUser = new InfoTelconetTmpUserAAAA();
                        $objTmpUser->setLogin($strLoginAAAA);
                    }
                    $objTmpUser->setFeUltMod(new \DateTime('now'));
                    $objTmpUser->setPrefijoEmpresa($strEmpresa);
                    $emAAAA->persist($objTmpUser);
                    $emAAAA->flush();
                    $emAAAA->commit();                    

                }
                catch (\Exception $ex) 
                {
                    $strMsnAAAA = "No se pudo crear registro en DB AAAA. ";
                    error_log('createAction->PersonalExternoController : ' . $ex->getMessage());
                }
                $strMsjError  = str_repeat(' ', 1000);
                $strSql       = "BEGIN DB_COMUNICACION.CUKG_TRANSACTIONS.P_ENVIA_CTA_CORREO_GENERADO(:strLogin,:strPassword,:strCorreo,:intPersonaId,:strPrefijoEmpresa,:strMensajeError); END;";
                $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
                $objStmt        = $emComunicacion->getConnection()->prepare($strSql);
                $objStmt->bindParam('strLogin', $arrayLoginExterno['login']);
                $objStmt->bindParam('strPassword', $arrayPassword['password']);
                $objStmt->bindParam('strCorreo', $strCtaCorreo);
                $objStmt->bindParam('intPersonaId', $entityPersona->getId());
                $objStmt->bindParam('strPrefijoEmpresa', $strEmpresa);
                $objStmt->bindParam('strMensajeError', $strMsjError);
                $objStmt->execute();
                if ($strMsjError  === 'Error' || $strMsjError != '')
                {
                   $strMsnAAAA .= "Existi&oacute; un error al enviar el correo. ($strMsjError)";
                }

                return $objRespuesta->setContent(json_encode(array('estatus' => true, 
                                                   'msg'     => 'Guardado satisfactoriamente. ' . $strMsnAAAA, 
                                                   'id'      => $entityPersonaEmpresaRol->getId())));
            }
            else
            {
                $emComercial->getConnection()->rollback();
                return $objRespuesta->setContent(json_encode(array('estatus' => false, 
                                                                   'msg'     => 'Error Personal Externo: '.$arrayDatos['msjEx'])));
            }
        } 
        catch (\Exception $e)
        {
            error_log('createAction->PersonalExternoController : Error ' . $e->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            return $objRespuesta->setContent(json_encode(array('estatus' => false, 'msg'     => $e->getMessage())));
        }
    }
    
    /**
     * @Secure(roles="ROLE_182-4")
     * 
     * Documentación para el método 'editAction'.
     * 
     * Método que renderiza la vista para edición del persona externo
     * 
     * @return Render
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 07-04-2017 - Se envía la empresa en sessión y el departamento de la empresa en sessión al cual se asoció al personal externo
     */
    public function editAction($id)
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $objManagerSeguridad     = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu          = $objManagerSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("182", "1");
        $objManager              = $this->getDoctrine()->getManager();
        $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);
        
        $intIdDepartamentoEmpresaSession = 0;
        
        if( is_object($entityPersonaEmpresaRol) )
        {
            $intIdDepartamentoEmpresaSession = $entityPersonaEmpresaRol->getDepartamentoId() ? $entityPersonaEmpresaRol->getDepartamentoId() : 0;
        }

        if(null == $personalExterno = $objManager->find('schemaBundle:InfoPersona', $entityPersonaEmpresaRol->getPersonaId()->getId()))
        {
            throw new \Exception('No existe el Personal Externo que se quiere modificar');
        }
        $formulario =$this->createForm(new PersonalExternoType(), $personalExterno);
        
        if(true === $this->get('security.context')->isGranted('ROLE_182-8'))
        {
            $arregloRolesPermitidos[] = 'ROLE_182-8'; //ELIMINAR PERSONAL EXTERNO
        }
        return $this->render( 'administracionBundle:PersonalExterno:edit.html.twig',
                              array('item'                            => $entityItemMenu,
                                    'rolesPermitidos'                 => $arregloRolesPermitidos,
                                    'edit_form'                       => $formulario->createView(),
                                    'personaEmpresaRol'               => $entityPersonaEmpresaRol,
                                    'personalexterno'                 => $personalExterno,
                                    'intIdDepartamentoEmpresaSession' => $intIdDepartamentoEmpresaSession,
                                    'strPrefijoEmpresa'               => $strPrefijoEmpresa) );
    }
    
    /**
     * @Secure(roles="ROLE_182-5")
     * 
     * Documentación para el método 'updateAction'.
     * 
     * Método que ejecuta la acción de actualizar la información de un personal externo.
     * 
     * @return Render En caso de éxito renderiza showAction.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 07-04-2016 - Se guarda el departamento de la empresa en sessión seleccionado por el usuario a la cual va a pertenecer el personal
     *                           externo.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 19-07-2021 - Se valida que el campo intIdDepartamentoEmpresaSession esté lleno, 
     *                           caso contrario no setear el departamento al personal.
     *
     */
    public function updateAction($id)
    {		
        $objPeticion   = $this->getRequest();
        $objManager    = $this->getDoctrine()->getManager('telconet');
        $entityPersona = $objManager->getRepository('schemaBundle:InfoPersona')->find($id);
        $objRespuesta  = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        if(!$entityPersona)
        {
            throw new \Exception('Unable to find InfoPersona entity.');
        }
        $strTipoIdentificacion = $objPeticion->get("strTipoIdentificacion");
        $strIdentificacion     = $objPeticion->get("strIdentificacion");
        $strNacionalidad       = $objPeticion->get("strNacionalidad");
        $strNombres            = $objPeticion->get("strNombres");
        $strDireccion          = $objPeticion->get("strDireccion");
        $strApellidos          = $objPeticion->get("strApellidos");
        $strGenero             = $objPeticion->get("strGenero");
        $datFechaInstitucionY  = $objPeticion->get("datFechaInstitucionY");
        $datFechaInstitucionM  = $objPeticion->get("datFechaInstitucionM");
        $datFechaInstitucionD  = $objPeticion->get("datFechaInstitucionD");
        $strEstadoCivil        = $objPeticion->get("strEstadoCivil");
        $strTitulo             = $objPeticion->get("strTitulo");
        $intEmpresaExterna     = $objPeticion->get("intEmpresaExterna");
        $listaFormasContacto   = $objPeticion->get("lstFormasContacto");
        
        $intIdDepartamentoEmpresaSession = $objPeticion->get("intIdDepartamentoEmpresaSession") 
                                           ? $objPeticion->get("intIdDepartamentoEmpresaSession") : null;
       
        $arregloValidacion = $this->validacionesCreacionActualizacion($strTipoIdentificacion , $strIdentificacion, $strNombres,
                                                                      $strDireccion          , $strApellidos     , $strGenero , 
                                                                      $strTitulo             , $intEmpresaExterna);
        if(!$arregloValidacion['boolOk'])    
        {
            return $objRespuesta->setContent(json_encode(array('estatus' => false, 'msg' => $arregloValidacion['strMsg'], 'id' => 0)));
        }
        $arregloFormasContactoTmp = explode(",", $listaFormasContacto);
        $intIndiceA = 0; 
        $intIndiceX = 0;
        $arregloFormasContacto = array();
        for($i = 0; $i < count($arregloFormasContactoTmp); $i++)
        {
            if($intIndiceA == 3)
            {
                $intIndiceA = 0;
                $intIndiceX++;
            }
            if($intIndiceA == 1)
            {
                $arregloFormasContacto[$intIndiceX]['formaContacto'] = $arregloFormasContactoTmp[$i];
            }
            if($intIndiceA == 2)
            {
                $arregloFormasContacto[$intIndiceX]['valor'] = $arregloFormasContactoTmp[$i];
            }
            $intIndiceA++;
        }
        $intIdEmpresa   = $objPeticion->getSession()->get('idEmpresa');
        $strUsrCreacion = $objPeticion->getSession()->get('user');
        $strUsrUltMod   = $objPeticion->getSession()->get('user');
        $objManager->getConnection()->beginTransaction();
        
        try 
        {
            $entityPersona->setTipoIdentificacion($strTipoIdentificacion);
            $entityPersona->setIdentificacionCliente($strIdentificacion);
            $entityPersona->setTipoTributario("NAT");
            $entityPersona->setNombres($strNombres);
            $entityPersona->setApellidos($strApellidos);
            $entityPersona->setNacionalidad($strNacionalidad);
            $entityPersona->setGenero($strGenero);
            $entityPersona->setDireccion($strDireccion);
            $entityPersona->setOrigenProspecto('N');
            $entityPersona->setEstado('Activo');
            $entityPersona->setTituloId($objManager->getRepository('schemaBundle:AdmiTitulo')->find($strTitulo));
            
            if($strEstadoCivil!='')
            {
                $entityPersona->setEstadoCivil($strEstadoCivil);
            }
            if($datFechaInstitucionY != '' && $datFechaInstitucionM != '' && $datFechaInstitucionD != '')
            {
                $entityPersona->setFechaNacimiento(date_create($datFechaInstitucionY . '-' . $datFechaInstitucionM . '-' . $datFechaInstitucionD));
            }
            $objManager->persist($entityPersona);
            $objManager->flush();
            
            //VERIFICO QUE YA TENGA UNA EMPRESA EXTERNA ASIGNADA, SINO SE CREA UNA NUEVA.
            $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Personal Externo',$intIdEmpresa);
            if(!$entityPersonaEmpresaRol)
            {
                throw new \Exception('No existe el Personal Externo que se quiere modificar');
            }
            if(!empty($intIdDepartamentoEmpresaSession))
            {
                $entityPersonaEmpresaRol->setDepartamentoId($intIdDepartamentoEmpresaSession);
            }
            $entityPersonaEmpresaRol->setEstado('Activo');
            $objManager->persist($entityPersonaEmpresaRol);
            $objManager->flush();
            
            $strCaracteristica = 'EMPRESA EXTERNA';
            $arregloEstados    = array('Activo', 'Eliminado');
            $entityPersonaEmpresaRolCarac = $objManager->getRepository('schemaBundle:InfoPersona')
                                                       ->getCaracteristicaPersonaEmpresaRol($entityPersonaEmpresaRol->getId(), 
                                                                                            $strCaracteristica, $arregloEstados);
            if($entityPersonaEmpresaRolCarac==null)
            {
                $entityCaracteristica = $objManager->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $arregloEstados);
                if(!$entityCaracteristica)
                {
                    throw new \Exception("No se ha definido la característica $strCaracteristica.");
                }
                $entityPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                $entityPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entityPersonaEmpresaRolCarac->setCaracteristicaId($entityCaracteristica);
                $entityPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRolCarac->setUsrCreacion($strUsrCreacion);
                $entityPersonaEmpresaRolCarac->setIpCreacion($objPeticion->getClientIp());
            }
            $entityPersonaEmpresaRolCarac->setValor($intEmpresaExterna);
            $entityPersonaEmpresaRolCarac->setEstado('Activo');
            $objManager->persist($entityPersonaEmpresaRolCarac);
            $objManager->flush();
            
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
            $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
            $entityPersonaHistorial->setEstado($entityPersona->getEstado());
            $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
            $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
            $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entityPersonaHistorial->setUsrCreacion($strUsrUltMod);
            $objManager->persist($entityPersonaHistorial);
            $objManager->flush();

            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            /* @var $objServicioInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
            $objServicioInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            $objServicioInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entityPersona->getId(), $strUsrUltMod);
            
            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for($i = 0; $i < count($arregloFormasContacto); $i++)
            {
                $strFormaContacto = $arregloFormasContacto[$i]["formaContacto"];
                if($strFormaContacto != '')
                {
                    $entityAdmiFormaContacto = $objManager->getRepository('schemaBundle:AdmiFormaContacto')
                                                          ->findPorDescripcionFormaContacto($strFormaContacto);
                    if($entityAdmiFormaContacto == null)
                    {
                        throw new \Exception("No existe la forma de contacto: $strFormaContacto");
                    }
                    $entityPersonaFormaContacto = new InfoPersonaFormaContacto();
                    $entityPersonaFormaContacto->setValor($arregloFormasContacto[$i]["valor"]);
                    $entityPersonaFormaContacto->setEstado("Activo");
                    $entityPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                    $entityPersonaFormaContacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entityPersonaFormaContacto->setIpCreacion($objPeticion->getClientIp());
                    $entityPersonaFormaContacto->setPersonaId($entityPersona);
                    $entityPersonaFormaContacto->setUsrCreacion($strUsrUltMod);
                    $objManager->persist($entityPersonaFormaContacto);
                    $objManager->flush();
                }
            }     
            if($objManager->getConnection()->isTransactionActive())
            {
                $objManager->getConnection()->commit();
            }
            return $objRespuesta->setContent(json_encode(array('estatus' => true, 
                                                               'msg'     => 'Guardado satisfactoriamente', 
                                                               'id'      => $entityPersonaEmpresaRol->getId())));
        }
        catch (\Exception $e) 
        {
            if($objManager->getConnection()->isTransactionActive())
            {
                $objManager->getConnection()->rollback();
                $objManager->getConnection()->close();
            }
            return $objRespuesta->setContent(json_encode(array('estatus' => false, 'msg' => $e->getMessage())));
        }
    }
    
    /** 
     * @Secure(roles="ROLE_182-8")
     * 
     * Documentación para el método 'deleteAction'.
     * 
     * Método que ejecuta la acción de eliminar lógicamente un personal externo.
     * 
     * @return Render En caso de éxito renderiza showAction.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 25-11-2021 - Se agrega conexión con ldap, para eliminar el personal externo.
     *
     */
    public function deleteAction($id)
    {
        $objPeticion               = $this->getRequest();
        $objManager                = $this->getDoctrine()->getManager();
        $entityPersonaEmpresaRol   = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id);
        $objSession                = $objPeticion->getSession();
        $strUsrCreacion            = $objSession->get('user');
        $strIpCreacion             = $objPeticion->getClientIp();
        $serviceUtil               = $this->get('schema.Util');
        $objServicePersonalExterno = $this->get('administracion.PersonalExterno');
        $arrayParametros           = array();
        $arrayRespuesta            = array();
        $strMensaje                = "Personal externo eliminado de manera correcta.";
        $boolEstatus               = true;
        try
        {
            if(!$entityPersonaEmpresaRol)
            {
                throw new \Exception("No existe el personal externo que se quiere eliminar");
            }
            else
            {
                $arrayParametros["strUsrCreacion"] = $strUsrCreacion;
                $arrayParametros["strIpCreacion"]  = $strIpCreacion;
                $arrayParametros["strLogin"]       = $entityPersonaEmpresaRol->getPersonaId()->getLogin();
                $arrayRespuestaLdap                = $objServicePersonalExterno->eliminarPersonalExterno($arrayParametros);
                if(empty($arrayRespuestaLdap) || !is_array($arrayRespuestaLdap) ||
                   !isset($arrayRespuestaLdap['status']) || ($arrayRespuestaLdap['status'] != '200' && $arrayRespuestaLdap['status'] != '204'))
                {
                    throw new \Exception("Error al eliminar el personal externo en Ldap.");
                }

                $strEstado = 'Eliminado';
                $entityPersonaEmpresaRol->setEstado($strEstado);
                $objManager->persist($entityPersonaEmpresaRol);
                $objManager->flush();

                $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                $entityPersonaHistorial->setEstado($strEstado);
                $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
                $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entityPersonaHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objManager->persist($entityPersonaHistorial);
                $objManager->flush();
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje  = $ex->getMessage();
            $boolEstatus = false;
            $serviceUtil->insertError("TelcoS+",
                                      "PersonalExternoController.deleteAction",
                                      $strMensaje,
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $arrayRespuesta["estatus"] = $boolEstatus;
        $arrayRespuesta["msg"]     = $strMensaje;
        $objRespuesta = new Response(json_encode($arrayRespuesta));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_182-8")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     * 
     * Método que ejecuta la acción de eliminar lógicamente la selección de personales externos.
     * 
     * @return Render En caso de éxito renderiza showAction.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 25-11-2021 - Se agrega conexión con ldap, para eliminar el personal externo.
     *
     */
    public function deleteAjaxAction()
    {
        $objRespuesta              = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objManager                = $this->getDoctrine()->getManager();
        $objPeticion               = $this->get('request');
        $arrayElementos            = $objPeticion->get('param');
        $arrayPersonalExterno      = explode("|",$arrayElementos);
        $objSession                = $objPeticion->getSession();
        $strUsrCreacion            = $objSession->get('user');
        $strIpCreacion             = $objPeticion->getClientIp();
        $serviceUtil               = $this->get('schema.Util');
        $objServicePersonalExterno = $this->get('administracion.PersonalExterno');
        $arrayParametros           = array();
        $strMensaje                = "Personal externo eliminado de manera correcta.";
        try
        {
            foreach($arrayPersonalExterno as $intIdPersonalExterno)
            {
                if(null == $entityPersonaEmpresaRol = $objManager->find('schemaBundle:InfoPersonaEmpresaRol', $intIdPersonalExterno))
                {
                    throw new \Exception("No existe el personal externo que se quiere eliminar");
                }
                else
                {
                    if(strtolower($entityPersonaEmpresaRol->getEstado()) != "eliminado")
                    {
                        $arrayParametros["strUsrCreacion"] = $strUsrCreacion;
                        $arrayParametros["strIpCreacion"]  = $strIpCreacion;
                        $arrayParametros["strLogin"]       = $entityPersonaEmpresaRol->getPersonaId()->getLogin();
                        $arrayRespuestaLdap                = $objServicePersonalExterno->eliminarPersonalExterno($arrayParametros);
                        if(empty($arrayRespuestaLdap) || !is_array($arrayRespuestaLdap) ||
                         !isset($arrayRespuestaLdap['status']) || ($arrayRespuestaLdap['status'] != '200' && $arrayRespuestaLdap['status'] != '204'))
                        {
                            throw new \Exception("Error al eliminar el personal externo en Ldap.");
                        }
                        $strEstado = 'Eliminado';
                        $entityPersonaEmpresaRol->setEstado($strEstado);
                        $objManager->persist($entityPersonaEmpresaRol);
                        $objManager->flush();
                        $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaHistorial->setEstado($strEstado);
                        $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                        $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
                        $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $entityPersonaHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                        $objManager->persist($entityPersonaHistorial);
                        $objManager->flush();
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
            $serviceUtil->insertError("TelcoS+",
                                      "PersonalExternoController.deleteAjaxAction",
                                      $strMensaje,
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objRespuesta->setContent($strMensaje);
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_182-7")
     * 
     * Documentación para el método 'gridAction'.
     * 
     * Obtiene la lista del personal externo.
     * 
     * @return Response JSON con la lista de formas del personal externo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     */
    public function gridAction()
    {
        $objPeticion  = $this->get('request');
        $intIdEmpresa = $this->getRequest()->getSession()->get('idEmpresa');
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $strNombres        = $objPeticion->get('nombres');
        $strApellidos      = $objPeticion->get('apellidos');
        $strIdentificacion = $objPeticion->get('identificacion');
        $intEmpresaExterna = $objPeticion->get('empresaExterna');
        $strEstado         = $objPeticion->get('estado');
        $intStart          = $objPeticion->get('start');
        $intLimit          = $objPeticion->get("limit");
        
        $objJson = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoPersona')
                                                     ->generarJsonPersonalExterno($strNombres, $strApellidos, $strIdentificacion, $intEmpresaExterna,
                                                                                  $strEstado,  $intStart,     $intLimit,          $intIdEmpresa);
        $objRespuesta->setContent($objJson);
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'formasContactoGridAction'.
     * 
     * Consulta las formas de contacto del Personal Externo
     * 
     * @return Response JSON con la lista de formas de contacto activas del personal externo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     */
    public function formasContactoGridAction()
    {
        $objPeticion  = $this->getRequest();
        $intLimit     = $objPeticion->get("limit");
        $intPage      = $objPeticion->get("page");
        $intStart     = $objPeticion->get("start");
        $intPersonaId = $objPeticion->get("personaid");
        
        $objManager       = $this->get('doctrine')->getManager('telconet');
        $arregloResultado = $objManager->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                       ->findPorEstadoPorPersona($intPersonaId, 'Activo', $intLimit, $intPage, $intStart);
        
                           
        $listaFormasContacto = $arregloResultado['registros'];
        $intTotalRegistros   = $arregloResultado['total'];
        $arregloFormasContacto = null;
        foreach($listaFormasContacto as $entityFormaContacto)
        {
            $arregloFormasContacto[] = array('idPersonaFormaContacto' => $entityFormaContacto->getId(),
                                             'idPersona'              => $entityFormaContacto->getPersonaId()->getId(),
                                             'formaContacto'          => $entityFormaContacto->getFormaContactoId()->getDescripcionFormaContacto(),
                                             'valor'                  => $entityFormaContacto->getValor());
        }
        $objRespuesta = new Response(json_encode(array('total' => $intTotalRegistros, 'personaFormasContacto' => $arregloFormasContacto)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }

    /**
     * Documentación para el método 'formasContactoAjaxAction'.
     * 
     * Consulta las formas de contacto activas para asignar al persona externo.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     */
    public function formasContactoAjaxAction() 
    {
        $objManager          = $this->get('doctrine')->getManager('telconet');
        $listaFormasContacto = $objManager->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
        foreach($listaFormasContacto as $entityFormaContacto)
        {
            $arregloFormasContacto[] = array('id'          => $entityFormaContacto->getId(),
                                             'descripcion' => $entityFormaContacto->getDescripcionFormaContacto());
        }
        $objRespuesta = new Response(json_encode(array('formasContacto' => $arregloFormasContacto)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'buscarPersonaPorIdentificacionAjaxAction'.
     * 
     * Consulta el Personal Externo por identificación validando por identificación.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 10-09-2015
     * @since 1.0
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2 21-09-2017 - Se agrega el parámetro del país para validar la identificación.
     *
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.3 19-03-2018 - Se divide el campo nombre en Primer y Segundo nombre, apellido en Primer y Segundo apellido,
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     * se valida el rol de la persona.
     */
    public function buscarPersonaPorIdentificacionAjaxAction()
    {
        $objPeticion           = $this->get('request');
        $strTipoIdentificacion = $objPeticion->get('tipoIdentificacion');
        $strIdentificacion     = $objPeticion->get('identificacion');
        $intIdPais             = $objPeticion->getSession()->get('intIdPais');
        
        $objManager         = $this->get('doctrine')->getManager('telconet');
        $objManagerSegu     = $this->get('doctrine')->getManager('telconet_seguridad');
        $objRepositorio     = $objManager->getRepository('schemaBundle:InfoPersona');
        $objRepositorioSegu = $objManagerSegu->getRepository('schemaBundle:SeguPerfilPersona');
        $intIdEmpresa       = $objPeticion->getSession()->get('idEmpresa');
        $arrayParamValidaIdentifica = array(
                                                'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                'strIdentificacionCliente'  => $strIdentificacion,
                                                'intIdPais'                 => $intIdPais,
                                                'strCodEmpresa'             => $intIdEmpresa
                                            );
        $strMensaje         = $objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica);
        $arregloPersona     = false;
        
        if($strMensaje == '')
        {
            
            $listaPersonalExterno = $objRepositorio->findPersonaPorIdentificacion($strTipoIdentificacion, $strIdentificacion);
            
            if($listaPersonalExterno)
            {
                foreach($listaPersonalExterno as $entityPersonal)
                {
                    $listaPersonaEmpresa = $objRepositorio->getEmpresasExternas($intIdEmpresa, 'Todos', $entityPersonal->getId());
                    
                    if (!empty($listaPersonaEmpresa))
                    {
                        $strMensaje = 'La identificación corresponde a una Empresa Externa';
                        $arregloPersona = false;
                        break;
                    }
                    $arrayNombres       = explode(" ",$entityPersonal->getNombres());
                    $strPrimerNombre    = $arrayNombres[0];
                    $strSegundoNombre   = $arrayNombres[1];
                    $arrayApellidos     = explode(" ",$entityPersonal->getApellidos());
                    $strPrimerApellido  = $arrayApellidos[0];
                    $strSegundoApellido = $arrayApellidos[1];

                    $arrayParametros = array();
                    $arrayParametros['arrayEstadoIPER']         = ['arrayEstado' => ['Cancelado', 'Inactivo', 'Modificado', 'Eliminado'], 'strComparador' => 'NOT IN'];
                    $arrayParametros['arrayDescripcionTipoRol'] = ['arrayDescripcionTipoRol' => ['Empleado','Personal Externo'], 'strComparador' => 'IN'];
                    $arrayParametros['arrayEmpresaCod']         = ['arrayEmpresaCod' => [$intIdEmpresa]];
                    $arrayParametros['arrayPersona']            = ['arrayPersona'    => [$entityPersonal->getId()]];
   
                    $objJsonInfoPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->getResultadoPersonaEmpresaRol($arrayParametros);
                    foreach($objJsonInfoPersonaEmpresaRol->registros as $arrayRegistros)
                    {
                    
                        if ($arrayRegistros['strDescripcionTipoRol'] == 'Empleado')
                        {
                             $arregloPersona[] = array('id'               => $entityPersonal->getId(),
                                                  'rol'                   => $arrayRegistros['strDescripcionTipoRol'],
                                                  'personaEmpresaRolId'  => 0,
                                                  'intDepartamentoId'    => 0,
                                                  'intOficinaId'         => 0,
                                                  'strPimerNombre'       => $strPrimerNombre,
                                                  'strSegundoNombre'     => $strSegundoNombre,
                                                  'strPrimerApellido'    => $strPrimerApellido,
                                                  'strSegundoApellido'   => $strSegundoApellido,
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
                        elseif ($arrayRegistros['strDescripcionTipoRol'] == 'Personal Externo') 
                        {
                              $arregloPersona[] = array('id'             => $entityPersonal->getId(),
                                                  'rol'                  => $arrayRegistros['strDescripcionTipoRol'],
                                                  'personaEmpresaRolId'  => $arrayRegistros['intIdPersonaEmpresaRol'],
                                                  'intDepartamentoId'    => 0,
                                                  'intOficinaId'         => 0,
                                                  'strPimerNombre'       => $strPrimerNombre,
                                                  'strSegundoNombre'     => $strSegundoNombre,
                                                  'strPrimerApellido'    => $strPrimerApellido,
                                                  'strSegundoApellido'   => $strSegundoApellido,
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
                    if ($arrayRegistros['strDescripcionTipoRol'] !== 'Empleado' &&
                        $arrayRegistros['strDescripcionTipoRol'] !== 'Personal Externo')
                      {
                           $intTotalRegistros = $objRepositorioSegu->validarPerfilPersonalExterno($entityPersonal->getId());

                            if($intTotalRegistros==0)
                            {                                $arregloPersona[] = array('id'                   => $entityPersonal->getId(),
                                                'rol'                   => 'Otros',
                                                'personaEmpresaRolId'  => 0,
                                                'intDepartamentoId'    => 0,
                                                'intOficinaId'         => 0,
                                                'strPimerNombre'       => $strPrimerNombre,
                                                'strSegundoNombre'     => $strSegundoNombre,
                                                'strPrimerApellido'    => $strPrimerApellido,
                                                'strSegundoApellido'   => $strSegundoApellido,
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
                            else
                            {
                                $strMensaje = ' Error en perfiles.';
                                $arregloPersona = false;
                                break;
                            }
                      }
                }
            }
            else
            {
                 $arregloPersona[] = array('id'                    => 0,
                                            'rol'                  => 'Nuevo',
                                            'personaEmpresaRolId'  => 0,
                                            'intDepartamentoId'    => 0,
                                            'intOficinaId'         => 0,
                                            'strPimerNombre'       => '',
                                            'strSegundoNombre'     => '',
                                            'strPrimerApellido'    => '',
                                            'strSegundoApellido'   => '',
                                            'tituloId'             => '',
                                            'genero'               => '',
                                            'estadoCivil'          => '',
                                            'fechaNacimiento_anio' => '',
                                            'fechaNacimiento_mes'  => '',
                                            'fechaNacimiento_dia'  => '',
                                            'nacionalidad'         => '',
                                            'direccion'            => '');
            }
        }
        $objRespuesta = new Response(json_encode(array('persona' => $arregloPersona, 'msg'=>$strMensaje)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
     /**
      * 
      * Documentación para el método 'getEmpresasExternasAction'.
      * 
      * Consulta las empresas externas del grid en el Index.
      * 
      * @return Response objeto JSON con la lista de empresas externas.
      * 
      * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
      * @version 1.0 10-09-2015
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.1 07-04-2017 - Se cambia el método de búsqueda de la 'InfoPersonaEmpresaRolId' por 'findOneById' cuando existe la relación del
      *                           personal externo con una empresa externa
      */
    public function getEmpresasExternasAction()
    {
        $objPeticion  = $this->getRequest();
        $intIdEmpresa = $objPeticion->getSession()->get('idEmpresa');
        $strNuevo    = $objPeticion->get('nuevo');
        $strEstado    = 'Activo';
        $objManager   = $this->getDoctrine()->getManager("telconet");
        $objRepositorioPersona = $objManager->getRepository('schemaBundle:InfoPersona');
        $listaEmpresasExternas = $objRepositorioPersona->getEmpresasExternas($intIdEmpresa, $strEstado);
        $boolEstado = false;
        if($listaEmpresasExternas)
        {
            // Se consulta la caracteristica 'EMPRESA EXTERNA relacionada al Personal Externo
            $intPersonaEmpresaRolId = $this->get('request')->get('personaEmpresaRolId');
            $arregloActivo = array('Activo');
            $strCaract = 'EMPRESA EXTERNA';
            $entityCaracteristica = $objRepositorioPersona->getCaracteristicaPersonaEmpresaRol($intPersonaEmpresaRolId, $strCaract, $arregloActivo);
            
            if( is_object($entityCaracteristica) )
            {
                $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                      ->findOneById($entityCaracteristica->getPersonaEmpresaRolId());
                $boolEstado = $entityPersonaEmpresaRol ? ($entityPersonaEmpresaRol->getEstado() == 'Eliminado' ? false : true) : false;
            }
            
            $boolEsEmpresa = false;
            $boolSeleccion = false;
            $boolNuevo = $strNuevo == 1;
            foreach($listaEmpresasExternas as $entityEmpresaExterna)
            {
                $boolEsEmpresa = $entityCaracteristica ? $entityCaracteristica->getValor() == $entityEmpresaExterna['objPerEmpRol']->getId() : false;
                if($boolEsEmpresa)
                {
                    $boolSeleccion = $boolEsEmpresa && $boolEstado;
                    $boolEsEmpresa = !($boolNuevo && !$boolEstado) ;
                }
                $presentacion_div.="<option value='" . $entityEmpresaExterna['objPerEmpRol']->getId() . "' " .
                                    ($boolEsEmpresa ? 'selected="selected"' : '' ).
                                    " >" .
                                    $entityEmpresaExterna['razonSocial'] .
                                    "</option>";
            }
            if(!$boolSeleccion)
            {
                $presentacion_div = "<option value=''>Seleccione...</option>".$presentacion_div;
            }
            $objResponse = new Response(json_encode(array('msg' => 'ok', 'div' => $presentacion_div, 'bloqueado' => !($boolNuevo && !$boolEstado))));
        }
        else
        {
            $objResponse = new Response(json_encode(array('msg' => '')));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }   
    
    /**
     * @Secure(roles="ROLE_182-4")")
     *
     * Documentación para el método 'asignarCaracteristicaAction'.
     *
     * Guarda el valor de la meta asignada al personal externo
     *
     * @return JsonResponse 
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 28-08-2015
     */   
    public function asignarCaracteristicaAction()
    {
        $objRequest  = $this->get('request');
        $objSession  = $objRequest->getSession();
        $strResponse = 'ERROR';
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $strIdPersonaEmpresaRol = $objRequest->request->get('intIdPersonaEmpresaRol') ? $objRequest->request->get('intIdPersonaEmpresaRol') : 0;
        $strCaracteristica      = $objRequest->request->get('strCaracteristica') ? $objRequest->request->get('strCaracteristica') : 0;
        $strUsrCreacion         = $objSession->get('user') ? $objSession->get('user') : '';
        $strValor               = $objRequest->request->get('strValor') ? $objRequest->request->get('strValor') : '';
        $strAccion              = $objRequest->request->get('strAccion') ? $objRequest->request->get('strAccion') : 'Guardar';
        
        $arregloPersonaEmpresaRol = explode('|', $strIdPersonaEmpresaRol);
        $arregloCaracteristicas   = explode('|', $strCaracteristica);
        $arregloValores           = explode('|', $strValor);
        foreach($arregloPersonaEmpresaRol as $intIdPersonaEmpresaRol)
        {
            if($intIdPersonaEmpresaRol)
            {
                $intIndiceI = 0;
                
                foreach($arregloCaracteristicas as $strCaracteristica)
                {
                    $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica($strCaracteristica);
                    $intIdCaracteristica = 0;
                    
                    if($entityCaracteristica)
                    {
                        $intIdCaracteristica = $entityCaracteristica->getId();
                    }
                    $arregloParametros = array('estado'              => 'Activo',
                                               'personaEmpresaRolId' => $intIdPersonaEmpresaRol,
                                               'caracteristicaId'    => $intIdCaracteristica);

                    $entityPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                ->findOneBy($arregloParametros);
                    
                    if($strAccion == 'Guardar')
                    {
                        $entityPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
                        $entityPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                        $entityPersonaEmpresaRolCaracNew->setEstado('Activo');
                        $entityPersonaEmpresaRolCaracNew->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRolCaracNew->setIpCreacion($objRequest->getClientIp());
                        $entityPersonaEmpresaRolCaracNew->setUsrCreacion($strUsrCreacion);
                        $entityPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $entityPersonaEmpresaRolCaracNew->setCaracteristicaId($entityCaracteristica);
                        $entityPersonaEmpresaRolCaracNew->setValor($arregloValores[$intIndiceI]);
                    }
                    $emComercial->getConnection()->beginTransaction();
                    try
                    {
                        if($entityPersonaEmpresaRolCarac)
                        {
                            $entityPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                            $entityPersonaEmpresaRolCarac->setUsrUltMod($strUsrCreacion);
                            $entityPersonaEmpresaRolCarac->setEstado('Eliminado');
                            $emComercial->persist($entityPersonaEmpresaRolCarac);
                        }
                        if($strAccion == 'Guardar')
                        {
                            $emComercial->persist($entityPersonaEmpresaRolCaracNew);
                        }
                        $emComercial->flush();
                        $emComercial->getConnection()->commit();
                        $strResponse = 'OK';
                    }
                    catch(Exception $e)
                    {
                        $emComercial->getConnection()->rollback();
                        $emComercial->getConnection()->close();		
                        $strResponse = $e->getMessage();
                    }
                    $intIndiceI++;
                }
            }
        }
        return new Response($strResponse);
    }
    
    /**
     * @Secure(roles="ROLE_182-3")
     * 
     * Documentación para el método 'validacionesCreacionActualizacion'.
     * 
     * Valida la información ingresada para la creación o actualización de Personal Externo.
     * 
     * @return Boolean si aprueba o no las validaciones.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 21-09-2017 - Se agrega el parámetro del país para validar la identificación.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     */
    private function validacionesCreacionActualizacion($strTipoIdentificacion , $strIdentificacion , $strNombres, 
                                                       $strDireccion          , $strApellidos      , $strGenero, 
                                                       $strTitulo             , $intEmpresaExterna)
    {
        $boolOk      = true;
        $strMsg      = '';
        $objPeticion = $this->get('request');
        $intIdPais   = $objPeticion->getSession()->get('intIdPais');
        $intIdEmpresa= $this->getRequest()->getSession()->get('idEmpresa');

        if(!isset($strTipoIdentificacion) || !isset($strTipoIdentificacion) || $strTipoIdentificacion == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione un Tipo de Identificación para el Personal Externo';
        }
        else if(!isset($strIdentificacion) || $strIdentificacion == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese la Identificación del Personal Externo';
        }
        else if(!isset($strNombres) || $strNombres == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese los Nombres del Personal Externo';
        }
        else if(!isset($strDireccion) || $strDireccion == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese la Dirección del Personal Externo';
        }
        else if(!isset($strApellidos) || $strApellidos == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese los Apellidos del Personal Externo';
        }
        else if(!isset($strGenero) || $strGenero == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione el Género del Personal Externo';
        }
        else if(!isset($strTitulo) || $strTitulo == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione el Título del Personal Externo';
        }
        else if(!isset($intEmpresaExterna) || $intEmpresaExterna == '' || $intEmpresaExterna == 'Seleccione...')
        {
            $boolOk = false;
            $strMsg = 'Seleccione la Empresa Externa a la que pertence el Personal Externo';
        }
        else
        {
            $objRepositorio = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPersona');
            $arrayParamValidaIdentifica = array(
                                                    'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                    'strIdentificacionCliente'  => $strIdentificacion,
                                                    'intIdPais'                 => $intIdPais,
                                                    'strCodEmpresa'             => $intIdEmpresa
                                                );
            $strMensaje     = $objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica);
            
            if($strMensaje=='')
            {
                $listaPersonalExterno = $objRepositorio->findPersonaPorIdentificacion($strTipoIdentificacion, $strIdentificacion);
                if(!empty($listaPersonalExterno))
                {
                    $listaPersonaEmpresa = $objRepositorio->getEmpresasExternas($intIdEmpresa, 'Todos', $listaPersonalExterno[0]->getId());
                    if(!empty($listaPersonaEmpresa))
                    {
                        $strMensaje = 'La identificación corresponde a una Empresa Externa';
                    } 
                }
            }
            $boolOk = $strMensaje == '';
            $strMsg = $boolOk ? '' : $strMensaje;
        }
        return array('boolOk' => $boolOk, 'strMsg' => $strMsg);
    }
    
}
