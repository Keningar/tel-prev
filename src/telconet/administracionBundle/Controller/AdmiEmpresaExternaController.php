<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Form\AdmiEmpresaExternaType;

use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'AdmiEmpresaExternaController'.
 *
 * Clase utilizada para manejar métodos que permiten realizar la administración de Empresas Externas en el módulo Corporativo.
 *
 * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
 * @version 1.0 08-09-2015
 */
class AdmiEmpresaExternaController extends Controller
{ 
    /**
     *  @Secure(roles="ROLE_298-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * Método inicial que consulta la lista de empresas externas
     * 
     * @return Response retorna la renderización de lista de empresas externas
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_298-1'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-1'; //INDEX EMPRESA EXTERNA
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2977'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2977'; //MOSTRAR EMPRESA EXTERNA
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2979'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2979'; //CONSULTAR EMPRESA EXTERNA
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2897'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2897'; //CREAR EMPRESA EXTERNA
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2917'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2917'; //EDITAR EMPRESA EXTERNA
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2918'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2918'; //ELIMINAR EMPRESA EXTERNA
        }
        return $this->render('administracionBundle:AdmiEmpresaExterna:index.html.twig', array('rolesPermitidos' => $arregloRolesPermitidos));
    }
    
    /**
     * @Secure(roles="ROLE_298-2977")
     * 
     * Documentación para el método 'showAction'.
     * 
     * Renderiza a la vista de visualización de la información de una empresa externa
     * 
     * @param mixed $id The entity id
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function showAction($id)
    {
        $objPeticion = $this->get('request');
        $objManager = $this->getDoctrine()->getManager();
        $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);
        $empresaExterna = $objManager->find('schemaBundle:InfoPersona', $entityPersonaEmpresaRol->getPersonaId());
        
        if(null == $empresaExterna)
        {
            throw new \Exception('No existe la Empresa Externa que se quiere mostrar');
        }
        $listaHistorial = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findHistorialPorPersonaEmpresaRol($id);

        //Recorre el historial y separa en arreglos cada estado
        $intIndiceI    = 0;
        $strCreacion   = null;
        $strEliminado  = null;
        $strUltMod     = null;
        
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
                    $strUltMod = array('estado'     => $entityHistorial->getEstado(),    'usrCreacion' => $entityHistorial->getUsrCreacion(),
                                       'feCreacion' => $entityHistorial->getFeCreacion(),'ipCreacion'  => $entityHistorial->getIpCreacion());
                }
            }
            $intIndiceI++;
        }
        if($entityPersonaEmpresaRol->getEstado() != 'Eliminado')
        {
            $strEliminado = null;
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2917'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2917'; //EDITAR EMPRESA EXTERNA
        }
        if(true === $this->get('security.context')->isGranted('ROLE_298-2918'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2918'; //ELIMINAR EMPRESA EXTERNA
        }
        return $this->render('administracionBundle:AdmiEmpresaExterna:show.html.twig', array('personaEmpresaRol'     => $entityPersonaEmpresaRol,
                                                                                             'rolesPermitidos'       => $arregloRolesPermitidos,
                                                                                             'creacion'              => $strCreacion,
                                                                                             'ultMod'                => $strUltMod,
                                                                                             'eliminado'             => $strEliminado,
                                                                                             'personaempresaexterna' => $empresaExterna,
                                                                                             'flag'                  => $objPeticion->get('flag')));
    }
    
    /**
     * @Secure(roles="ROLE_298-2897")
     * 
     * Documentación para el método 'newAction'.
     * 
     * Renderiza a la vista de creación de una nueva empresa externa
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 14-02-2018 
     * Se agrega envió del valor de la longitud máxima del campo identificación obteniéndolo del parámetro correspondiente.
     */
    public function newAction()
    {
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $emComercial = $this->get('doctrine')->getManager('telconet');
        
        $arrayParametros["strTipoIdentificacion"]  = 'RUC';
        $arrayParametros["strNombrePais"]          = $objSession->get('strNombrePais');
               
        $strJsonLongitudIdentificacion = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->getJsonMaxLongitudIdentificacion($arrayParametros);
        
        $objIdentificacion = json_decode($strJsonLongitudIdentificacion);

        $intMaxLongitudIdentificacion = $objIdentificacion->intMaxLongitudIdentificacion;
        
        $arrayOptions = array("intMaxLongitudIdentificacion"=>$intMaxLongitudIdentificacion);
        
        $objForm = $this->createForm(new AdmiEmpresaExternaType(), new InfoPersona($arrayOptions));
        
        return $this->render('administracionBundle:AdmiEmpresaExterna:new.html.twig', array('new_form' => $objForm->createView()));
    }
   
    /**
     * @Secure(roles="ROLE_298-2917")
     * 
     * Documentación para el método 'editAction'.
     * 
     * Renderiza a la vista para la edición de la información de una empresa externa
     * 
     * @param mixed $id The entity id
     * 
     * @return render
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function editAction($id)
    {
        $objManager = $this->getDoctrine()->getManager();
        $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);
        if(null == $entityEmpresaExterna = $objManager->find('schemaBundle:InfoPersona', $entityPersonaEmpresaRol->getPersonaId()->getId()))
        {
            throw new \Exception('No existe la Empresa Externa que se quiere modificar');
        }
        $objFormulario = $this->createForm(new AdmiEmpresaExternaType(), $entityEmpresaExterna);
        if(true === $this->get('security.context')->isGranted('ROLE_298-2918'))
        {
            $arregloRolesPermitidos[] = 'ROLE_298-2918'; //ELIMINAR EMPRESA EXTERNA
        }
        return $this->render('administracionBundle:AdmiEmpresaExterna:edit.html.twig', 
                             array('edit_form'             => $objFormulario->createView(),
                                   'rolesPermitidos'       => $arregloRolesPermitidos,
                                   'personaempresaexterna' => $entityEmpresaExterna->getId(),
                                   'estado'                => $entityPersonaEmpresaRol->getEstado(),
                                   'empresaexterna'        => $id));
    }
   
    /**
     * @Secure(roles="ROLE_298-2897")
     * 
     * Documentación para el método 'saveOrUpdateAction'.
     * 
     * Método que ejecuta la acción de crear una nueva empresa externa o editar una existente
     * 
     * @return Response retorna el resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.1 09-01-2018 Se agrega la categoria de la empresa externa, insertandola como caracteristica
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.2 01-03-2018 Se agrega la oficina de la empresa externa.
     */
    public function saveOrUpdateAction()
    {
        $objManager   = $this->getDoctrine()->getManager('telconet');
        $objPeticion  = $this->getRequest();
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        $intIdEmpresa = $objPeticion->getSession()->get('idEmpresa');
        $usrCreacion  = $objPeticion->getSession()->get('user');
        $usrUltMod    = $objPeticion->getSession()->get('user');

        $intPersonaEmpresaId  = $objPeticion->get("intPersonaEmpresa");
        $intEmpresaExternaId  = $objPeticion->get("intEmpresaExterna");
        $strNombreEmpresa     = $objPeticion->get("strNombreEmpresa");
        $strRazonSocial       = $objPeticion->get("strRazonSocial");
        $strRuc               = $objPeticion->get("strRuc");
        $datFechaInstitucionY = $objPeticion->get("datFechaInstitucionY");
        $datFechaInstitucionM = $objPeticion->get("datFechaInstitucionM");
        $datFechaInstitucionD = $objPeticion->get("datFechaInstitucionD");
        $strNacionalidad      = $objPeticion->get("strNacionalidad");
        $strDireccion         = $objPeticion->get("strDireccion");
        $listaFormasContacto  = $objPeticion->get("lstFormasContacto");
        $strCategoria         = $objPeticion->get("strCategoria");
        $strOficinaId         = $objPeticion->get("strOficina");
        
        /*************************************/
        /* INICIO BLOQUE DE VALIDACIONES     */
        /*************************************/
        $arregloValidacion = $this->validacionesCreacionActualizacion($strRuc, $strNombreEmpresa, $strRazonSocial, $strDireccion);

        if(!$arregloValidacion['boolOk'])
        {
            return $objRespuesta->setContent(json_encode(array('estatus' => false, 'msg' => $arregloValidacion['strMsg'], 'id' => 0)));
        }
        $boolNuevaPersona = $intPersonaEmpresaId == '';
        $boolNuevaPerEmpR = $intEmpresaExternaId == '';
        $boolEditando     = !$boolNuevaPersona && !$boolNuevaPerEmpR;
        
        if(!$boolEditando)
        {
            $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->getPersonaEmpresaRolPorPersonaPorTipoRol($intPersonaEmpresaId, 'Empresa Externa', $intIdEmpresa);
            if(null != $entityPersonaEmpresaRol)
            {
                $intEmpresaExternaId = $entityPersonaEmpresaRol->getId();
                $boolNuevaPerEmpR    = $intEmpresaExternaId == '';
            }
        }
        else
        {
            $entityPersonaEmpresaRol = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intEmpresaExternaId);
            if(null == $entityPersonaEmpresaRol)
            {
               throw new \Exception('No existe la Empresa que se quiere modificar');
            }
        }
        $listaEmpresasExternas = $objManager->getRepository('schemaBundle:InfoPersona')->getEmpresasExternas($intIdEmpresa, 'Todos');
        
        foreach($listaEmpresasExternas as $entityEmpresaExterna)
        {
            //Se compara con el resto de Empresas Externas diferentes a la actual en edición de ser el caso.
            $boolDiferente = $intEmpresaExternaId != $entityEmpresaExterna['objPerEmpRol']->getId();
            if($boolDiferente)
            {
                $entityPerEmpR = $entityEmpresaExterna['objPerEmpRol']->getPersonaId();
                $strEstadoPER  = $entityEmpresaExterna['objPerEmpRol']->getEstado();
                $strEstado     = $strEstadoPER == 'Eliminado' ? '[eliminada]':'';
                if($strRuc == $entityPerEmpR->getIdentificacionCliente())
                {
                    return $objRespuesta->setContent(json_encode(array('estatus' => false, 
                                                                       'msg'     => 'El RUC ya está registrado a otra empresa'.$strEstado, 
                                                                       'id'      => '0')));
                }
                else if(strtoupper($strNombreEmpresa) == strtoupper($entityPerEmpR->getNombres()))
                {
                    return $objRespuesta->setContent(json_encode(array('estatus' => false, 
                                                                       'msg'     => 'El Nombre Empresa ya está registrado a otra empresa'.$strEstado, 
                                                                       'id'      => '0')));
                }
                else if(strtoupper($strRazonSocial) == strtoupper($entityPerEmpR->getRazonSocial()))
                {
                    return $objRespuesta->setContent(json_encode(array('estatus' => false, 
                                                                       'msg'     => 'La Razón Social ya está registrada a otra empresa'.$strEstado, 
                                                                       'id'      => '0')));
                }
            }
        }
        /*************************************/
        /* FIN BLOQUE DE VALIDACIONES        */
        /*************************************/
        $arregloFormasContactoTemp = explode(",", $listaFormasContacto);
        $intIndiceA = 0;
        $intIndiceX = 0;
        $arregloFormasContacto = array();
        
        for($i = 0; $i < count($arregloFormasContactoTemp); $i++)
        {
            if($intIndiceA == 3) { $intIndiceA = 0; $intIndiceX++; }
            if($intIndiceA == 1) {$arregloFormasContacto[$intIndiceX]['formaContacto'] = $arregloFormasContactoTemp[$i];}
            if($intIndiceA == 2) {$arregloFormasContacto[$intIndiceX]['valor']         = $arregloFormasContactoTemp[$i];}
            $intIndiceA++;
        }
        $objManager->getConnection()->beginTransaction();
        
        try 
        {
            if(!$boolNuevaPersona)
            {
                $entityPersona = $objManager->find('schemaBundle:InfoPersona', $intPersonaEmpresaId);

                if(null == $entityPersona)
                {
                    throw new \Exception('No existe la Persona que se quiere modificar');
                }
                //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
                $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
                $serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($intPersonaEmpresaId, $usrUltMod);
            }
            else
            {
                $entityPersona = new InfoPersona();
                $entityPersona->setTipoIdentificacion('RUC');
                $entityPersona->setTipoTributario("JUR");
                $entityPersona->setOrigenProspecto('N');
                $entityPersona->setFeCreacion(new \DateTime('now'));
                $entityPersona->setUsrCreacion($usrCreacion);
                $entityPersona->setIpCreacion($objPeticion->getClientIp());
            }
            $entityPersona->setEstado('Activo');
            $entityPersona->setIdentificacionCliente($strRuc);
            $entityPersona->setRazonSocial(strtoupper(trim(preg_replace('/\s+/', ' ', $strRazonSocial))));
            $entityPersona->setNombres(strtoupper(trim(preg_replace('/\s+/', ' ', $strNombreEmpresa))));
            $entityPersona->setDireccion(strtoupper(trim(preg_replace('/\s+/', ' ', $strDireccion))));
            $entityPersona->setNacionalidad($strNacionalidad);

            if($datFechaInstitucionY != '' && $datFechaInstitucionM != '' && $datFechaInstitucionD != '')
            {
                $entityPersona->setFechaNacimiento(date_create($datFechaInstitucionY . '-' . $datFechaInstitucionM . '-' . $datFechaInstitucionD));
            }
            $objManager->persist($entityPersona);
            $objManager->flush();

            if($entityPersonaEmpresaRol == null)
            {
                //ASIGNA ROL DE "EMPRESA EXTERNA" A LA EMPRESA
                $strRol = 'Empresa Externa';
                $entityEmpresaRol = $objManager->getRepository('schemaBundle:InfoEmpresaRol')
                                               ->findPorNombreRolPorNombreTipoRolPorEmpresa($strRol, $strRol, $intIdEmpresa);
                if(!$entityEmpresaRol)
                {
                    throw new \Exception('No se ha definido el ROL de Empresa Externa');
                }
                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
                $entityPersonaEmpresaRol->setPersonaId($entityPersona);                
                $entityOficinaId = $objManager->getRepository('schemaBundle:InfoOficinaGrupo')
                                               ->find($strOficinaId);
                if (!$entityOficinaId)
                {
                    throw new \Exception('No se ha definido la Oficina');
                }
                $entityPersonaEmpresaRol->setOficinaId($entityOficinaId);
                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRol->setUsrCreacion($usrCreacion);
            }
            $entityPersonaEmpresaRol->setEstado('Activo');
            $objManager->persist($entityPersonaEmpresaRol);
            $objManager->flush();
            
            //REGISTRO LA CARACTERISITCA -> CATEGORIA DE LA EMPRESA EXTERNA
            $strCaracteristica    = 'CATEGORIA EMPRESA EXTERNA';
            $strEstado            = 'Activo';
            $entityCaracteristica = $objManager->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
            if (!$entityCaracteristica)
            {
                throw new \Exception("No se ha definido la característica $strCaracteristica.");
            }
            //VERIFICO QUE YA TENGA UNA CARACTERISTICA "EMPRESA EXTERNA" ASIGNADA, SINO SE CREA UNA NUEVA.
            $arrayCriterios = array ('caracteristicaId'    => $entityCaracteristica->getId(),
                                       'personaEmpresaRolId' => $entityPersonaEmpresaRol->getId());
            $entityPersonaEmpresaRolCarac = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy($arrayCriterios);
            
            if (!$entityPersonaEmpresaRolCarac)
            {
                $entityPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                $entityPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entityPersonaEmpresaRolCarac->setCaracteristicaId($entityCaracteristica);
                $entityPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRolCarac->setUsrCreacion($usrCreacion);
                $entityPersonaEmpresaRolCarac->setIpCreacion($objPeticion->getClientIp());
            }
            $entityPersonaEmpresaRolCarac->setValor(strtoupper($strCategoria));
            $entityPersonaEmpresaRolCarac->setEstado($strEstado);            
            $objManager->persist($entityPersonaEmpresaRolCarac);
            $objManager->flush();           
            
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
            $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
            $entityPersonaHistorial->setEstado($entityPersonaEmpresaRol->getEstado());
            $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
            $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
            $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entityPersonaHistorial->setUsrCreacion($usrCreacion);
            $objManager->persist($entityPersonaHistorial);
            $objManager->flush();
            
            //REGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for ($i = 0; $i < count($arregloFormasContacto); $i++)
            {
                $strFormaContacto = $arregloFormasContacto[$i]['formaContacto'];
                if($strFormaContacto != '')
                {
                    $entityAdmiFormaContacto = $objManager->getRepository('schemaBundle:AdmiFormaContacto')
                                                          ->findPorDescripcionFormaContacto($strFormaContacto);
                    if(null == $entityAdmiFormaContacto)
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
                    $entityPersonaFormaContacto->setUsrCreacion($usrCreacion);
                    $objManager->persist($entityPersonaFormaContacto);
                    $objManager->flush();
                }
            }
            $objManager->getConnection()->commit();
            return $objRespuesta->setContent(json_encode(array('estatus' => true, 
                                                               'msg'     => 'Guardado satisfactoriamente', 
                                                               'new'     => !$boolEditando,
                                                               'id'      => $entityPersonaEmpresaRol->getId())));
        }
        catch (\Exception $e) 
        {
            $objManager->getConnection()->rollback();
            $objManager->getConnection()->close();
            return $objRespuesta->setContent(json_encode(array('estatus' => false, 'msg'=> $e->getMessage())));
        }        
    }

    /**
     * @Secure(roles="ROLE_298-2918")
     * 
     * Documentación para el método 'deleteAction'.
     * 
     * Cambia a estado Eliminado a la empresa externa y en casada elimina el personal externo asociado.
     * 
     * @return Response JSON con la lista de empresas paginadas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function deleteAction($id)
    {
        $objManager = $this->getDoctrine()->getManager();
        $boolEstado = true;
        $strEstadoEliminado = 'Eliminado';
        $strEstadoActivo    = 'Activo';
        try
        {
            $objPeticion = $this->getRequest();
            $entityEmpresaExterna = $objManager->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id);
            
            if(!$entityEmpresaExterna)
            {
                $boolEstado = false;
                $strMsg = 'No existe la Empresa Externa que se quiere eliminar.';
            }
            else
            {
                $objManager->getConnection()->beginTransaction();
                $entityEmpresaExterna->setEstado($strEstadoEliminado);
                $objManager->persist($entityEmpresaExterna);
                $objManager->flush();

                $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                $entityPersonaHistorial->setEstado($strEstadoEliminado);
                $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
                $entityPersonaHistorial->setPersonaEmpresaRolId($entityEmpresaExterna);
                $entityPersonaHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                $objManager->persist($entityPersonaHistorial);
                $objManager->flush();
                
                $intIdEmpresa = $objPeticion->getSession()->get('idEmpresa');
                $listaPersonalExterno = $this->getDoctrine()->getManager()
                                                            ->getRepository('schemaBundle:InfoPersona')
                                                            ->getRegistrosPersonalExterno('', '', '', $id, $strEstadoActivo, '', '', $intIdEmpresa);
                if($listaPersonalExterno)
                {
                    foreach($listaPersonalExterno as $entityPersonaEmpresaRol)
                    {
                        $entityPersonaEmpresaRol->setEstado($strEstadoEliminado);
                        $objManager->persist($entityPersonaEmpresaRol);
                        $objManager->flush();

                        $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaHistorial->setEstado($strEstadoEliminado);
                        $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                        $entityPersonaHistorial->setIpCreacion($objPeticion->getClientIp());
                        $entityPersonaHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $entityPersonaHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                        $objManager->persist($entityPersonaHistorial);
                        $objManager->flush();
                    }
                }
                $objManager->getConnection()->commit();
                $strMsg ='Eliminación exitosa.';
            }
        }
        catch(Exception $ex)
        {
            $boolEstado = false;
            $strMsg = $ex->getMessage();
            $objManager->getConnection()->rollback();
            $objManager->getConnection()->close();
        }
        $objRespuesta = new Response(json_encode(array('estatus' => $boolEstado, 'msg' => $strMsg)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_298-2979")
     * 
     * Documentación para el método 'getListaEmpresasExternasAction'.
     * 
     * Consulta la lista de empresas externas.
     * 
     * @return Response JSON con la lista de empresas paginadas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function getListaEmpresasExternasAction()
    {
        $objRespuesta = new Response();
        $intIdEmpresa = $this->getRequest()->getSession()->get('idEmpresa');
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion = $this->get('request');
        
        $strIdentificación = $objPeticion->get('identificacion');
        $strNombre         = $objPeticion->get('nombre');
        $strRazonSocial    = $objPeticion->get('razonSocial');
        $strEstado         = $objPeticion->get('estado');
        $intStart          = $objPeticion->get('start');
        $intLimit          = $objPeticion->get('limit');
        
        $objJson = $this->getDoctrine()->getManager()->getRepository('schemaBundle:InfoPersona')
                                                     ->getListaEmpresasExternas($strIdentificación, $strNombre, $strRazonSocial, $intIdEmpresa,
                                                                                $strEstado,         $intStart,  $intLimit);
        $objRespuesta->setContent($objJson);
        return $objRespuesta;
    }

    /**
     * Documentación para el método 'formasContactoGridAction'.
     * 
     * Consulta las formas de contacto de la Empresa Externa paginadas.
     * 
     * @return Response JSON con la lista de formas de contacto paginadas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function formasContactoGridAction()
    {
        $objPeticion         = $this->getRequest();
        $intLimit            = $objPeticion->get("limit");
        $intPage             = $objPeticion->get("page");
        $intStart            = $objPeticion->get("start");
        $intPersonaEmpresaId = $objPeticion->get("personaempresaexternaID");
        
        $objManager          = $this->get('doctrine')->getManager('telconet');
        $arregloResultado    = $objManager->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                          ->findPorEstadoPorPersona($intPersonaEmpresaId, 'Activo', $intLimit, $intPage, $intStart);
        $listaFormasContacto = $arregloResultado['registros'];
        $intTotalRegistros   = $arregloResultado['total'];

        foreach ($listaFormasContacto as $entityFormasContacto)
        {
            $arregloFormasContacto[] = array('idPersonaFormaContacto' => $entityFormasContacto->getId(),
                                             'idPersona'              => $entityFormasContacto->getPersonaId()->getId(),
                                             'formaContacto'          => $entityFormasContacto->getFormaContactoId()->getDescripcionFormaContacto(),
                                             'valor'                  => $entityFormasContacto->getValor());
        }
        if (!empty($arregloFormasContacto))
        {
            $objRespuesta = new Response(json_encode(array('total' => $intTotalRegistros, 'personaFormasContacto' => $arregloFormasContacto)));
        }
        else
        {
            $arregloFormasContacto[] = array();
            $objRespuesta = new Response(json_encode(array('total' => $intTotalRegistros, 'personaFormasContacto' => $arregloFormasContacto)));
        }
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'formasContactoAjaxAction'.
     * 
     * Consulta las formas de contacto en la edición y agregación de una nueva empresa externa.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 08-09-2015
     */
    public function formasContactoAjaxAction()
    {
        $objManager = $this->get('doctrine')->getManager('telconet');
        $listaFormasContacto = $objManager->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
        foreach($listaFormasContacto as $entityFormasContacto)
        {
            $arregloFormasContacto[] = array('id'          => $entityFormasContacto->getId(), 
                                             'descripcion' => $entityFormasContacto->getDescripcionFormaContacto());
        }
        $objRespuesta = new Response(json_encode(array('formasContacto' => $arregloFormasContacto)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'buscarEmpresaPorIdentificacionAjaxAction'.
     * 
     * Consulta la Empresa Externa por identificación.
     * 
     * @return Response JSON con la lista de formas de contacto activas.
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
    public function buscarEmpresaPorIdentificacionAjaxAction()
    {
        $objPeticion           = $this->get('request');
        $strTipoIdentificacion = 'RUC';
        $strIdentificacion     = $objPeticion->get('identificacion');
        $intIdPais             = $objPeticion->getSession()->get('intIdPais');
        $intIdEmpresa          = $objPeticion->getSession()->get('idEmpresa');
        $objManager            = $this->get('doctrine')->getManager('telconet');
        $objRepositorio        = $objManager->getRepository('schemaBundle:InfoPersona');
        $arrayEmpresa          = false;
        $arrayParamValidaIdentifica = array(
                                                'strTipoIdentificacion'     => 'RUC',
                                                'strIdentificacionCliente'  => $strIdentificacion,
                                                'intIdPais'                 => $intIdPais,
                                                'strCodEmpresa'             => $intIdEmpresa
                                            );
        $strMensaje            = $objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica);
        
        if($strMensaje == '')
        {
            $listaPersonaEmpresa = $objRepositorio->findPersonaPorIdentificacion($strTipoIdentificacion, $strIdentificacion);

            if($listaPersonaEmpresa)
            {
                foreach($listaPersonaEmpresa as $entityEmpresa)
                {
                    $arrayEmpresa[] = array('id'                   => $entityEmpresa->getId(),
                                            'nombres'              => $entityEmpresa->getNombres(),
                                            'razonSocial'          => $entityEmpresa->getRazonSocial(),
                                            'tituloId'             => $entityEmpresa->getTituloId() ? ($entityEmpresa->getTituloId()->getId() ?
                                                                      $entityEmpresa->getTituloId()->getId() : ''): '',
                                            'genero'               => $entityEmpresa->getGenero(),
                                            'estadoCivil'          => $entityEmpresa->getEstadoCivil() ? $entityEmpresa->getEstadoCivil() : '',
                                            'fechaNacimiento_anio' => $entityEmpresa->getFechaNacimiento() ?
                                                                      strval(date_format($entityEmpresa->getFechaNacimiento(), "Y")) : '',
                                            'fechaNacimiento_mes'  => $entityEmpresa->getFechaNacimiento() ?
                                                                      strval(date_format($entityEmpresa->getFechaNacimiento(), "m")) : '',
                                            'fechaNacimiento_dia'  => $entityEmpresa->getFechaNacimiento() ?
                                                                      strval(date_format($entityEmpresa->getFechaNacimiento(), "d")) : '',
                                            'nacionalidad'         => $entityEmpresa->getNacionalidad(),
                                            'direccion'            => $entityEmpresa->getDireccion());
                }
            }
        }
        $objRespuesta = new Response(json_encode(array('empresasExternas' => $arrayEmpresa, 'msg'=>$strMensaje)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_298-2897")
     * 
     * Documentación para el método 'validacionesCreacionActualizacion'.
     * 
     * Valida la información ingresada para la creación o actualización de Empresas Externas.
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
    private function validacionesCreacionActualizacion($strRuc, $strNombreEmpresa, $strRazonSocial, $strDireccion)
    {
        $boolOk    = true;
        $strMsg    = '';
        $intIdPais = $this->get('request')->getSession()->get('intIdPais');
        $intIdEmpresa = $this->get('request')->getSession()->get('idEmpresa');
        
        if(!isset($strRuc) || $strRuc == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese el RUC de la Empresa Externa';
        }
        else if(!isset($strNombreEmpresa) || $strNombreEmpresa == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese el Nombre de la Empresa Externa';
        }
        else if(!isset($strRazonSocial) || $strRazonSocial == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese la Razón Social de la Empresa Externa';
        }
        else if(!isset($strDireccion) || $strDireccion == '')
        {
            $boolOk = false;
            $strMsg = 'Ingrese la Dirección de la Empresa Externa';
        }
        else
        {
            $arrayParamValidaIdentifica = array(
                                                    'strTipoIdentificacion'     => 'RUC',
                                                    'strIdentificacionCliente'  => $strRuc,
                                                    'intIdPais'                 => $intIdPais,
                                                    'strCodEmpresa'             => $intIdEmpresa
                                                );
            $strMensaje = $this->get('doctrine')->getManager('telconet')
                                                ->getRepository('schemaBundle:InfoPersona')
                                                ->validarIdentificacionTipo($arrayParamValidaIdentifica);
            $boolOk = $strMensaje == '';
            $strMsg = $boolOk ? '' : $strMensaje;
        }
        return array('boolOk' => $boolOk, 'strMsg' => $strMsg);
    }

}
