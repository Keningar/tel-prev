<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaContacto;
use telconet\schemaBundle\Entity\InfoPuntoContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPuntoFormaContacto;
use telconet\schemaBundle\Form\ContactoType;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use telconet\soporteBundle\Service\PlantillaService;
use telconet\schemaBundle\Entity\ReturnResponse;
/**
 * InfoPersona controller.
 *
 */
class ContactoController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_5-1")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
		$request  = $this->get('request');
		$session  = $request->getSession();
        //$session->set('menu_modulo_activo',"prospectos");

        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("5", "1");
        $session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

		/*Para la carga de la imagen desde el default controller*/
        //$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        //$adminController = new DefaultController();
        //$img_opcion = $adminController->getImgOpcion($em_seguridad,'COM-PROS');

		/*Presentar acciones relacionada*/
        //$acc_relacionadas=$adminController->getAccionesRelacionadas($em_seguridad,'COMPROS','index');

        return $this->render('comercialBundle:contacto:index.html.twig', array(
            'item' => $entityItemMenu,'pec'=>'pec'
                //'img_opcion_menu'=>$img_opcion,
                //'acc_relaciondas'=>$acc_relacionadas,
        ));

    }


    /**
     * getContactoClienteByTipoRolAjaxAction, obtiene los contactos de de un cliente segun un tipo de contacto.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getContactoClienteByTipoRolAjaxAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager("telconet");

        $arrayGetPersonaContacto = array();
        $arrayGetPersonaContacto['strJoinPunto']    = $objRequest->get('strJoinPunto');
        $arrayGetPersonaContacto['arrayTipoRol']    = ['arrayDescripcionTipoRol'    => ['Contacto']];
        $arrayGetPersonaContacto['arrayRol']        = ['arrayDescripcionRol'        => ['Contacto Tecnico']];
        $arrayGetPersonaContacto['arrayEmpresaRol'] = ['arrayEmpresaCod'            => [$strCodEmpresa]];
        $arrayGetPersonaContacto['arrayPersonaPuntoContacto'] = ['arrayPersonaEmpresaRol'   => [$objRequest->get('intIdPersonaEmpresaRol')],
                                                                 'arrayPunto'               => [$objRequest->get('intIdPunto')],
            'arrayEstPerPuntoContacto' => ['Activo']];
        $arrayGetPersonaContacto['strGroupBy']  = 'GROUP BY';
        //Busca contactos
        $jsonData = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
            ->getJSONContactoClienteByTipoRol($arrayGetPersonaContacto);
        $objJsonData = json_decode($jsonData);
        $objResponse = new Response($jsonData);
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getContactoClienteByTipoRolAjaxAction

    /*combo estado llenado ajax*/
    public function estadosAction()
    {
        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Convertido','codigo'=> 'ACT','descripcion'=> 'Convertido');            
        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * gridAction, muestra los contactos del cliente en sesion
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 15-06-2018 - Se obtiene el total de los contactos, validando su estado en Activo.
     * 
     * @Secure(roles="ROLE_5-7")
     */
    public function gridAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $intIdEmpresa           = $objSession->get('idEmpresa');
        $emComercial            = $this->get('doctrine')->getManager('telconet');

        $strEstado              = "Activo";
        $strNombres             = $objRequest->get("strNombres");
        $strApellidos           = $objRequest->get("strApellidos");
        $strUsrCreacion         = $objRequest->get("strUsrCreacion");
        $intIdRol               = $objRequest->get("intIdRol");
        $intIdTitulo            = $objRequest->get("intIdTitulo");
        $intIdPersonaEmpresaRol = $objRequest->get("intIdPersonaEmpresaRol");
        $intIdPunto             = $objRequest->get("intIdPunto");
        $strJoinPunto           = $objRequest->get("strJoinPunto");
        $strGroupBy             = $objRequest->get("strGroupBy");
        $strDescripcionTipoRol  = $objRequest->get("strDescripcionTipoRol");
        $arrayFechaDesde        = explode('T', $objRequest->get("dateFechaDesde"));
        $arrayFechaHasta        = explode('T', $objRequest->get("dateFechaHasta"));
        $intLimit               = $objRequest->get("limit");
        $intStart               = $objRequest->get("start");


        $arrayGetPersonaContacto = array();
        $arrayGetPersonaContacto['strJoinPunto']    = $strJoinPunto;
        $arrayGetPersonaContacto['arrayTipoRol']    = ['arrayDescripcionTipoRol'    => [$strDescripcionTipoRol]];
        $arrayGetPersonaContacto['arrayRol']        = ['arrayRol'                   => [$intIdRol]];
        $arrayGetPersonaContacto['arrayTitulo']     = ['arrayTitulo'                => [$intIdTitulo]];
        $arrayGetPersonaContacto['arrayEmpresaRol'] = ['arrayEmpresaCod'            => [$intIdEmpresa]];
        $arrayGetPersonaContacto['arrayPersonaPuntoContacto'] = ['arrayPersonaEmpresaRol'   => [$intIdPersonaEmpresaRol],
                                                                 'arrayPunto'               => [$intIdPunto],
            'arrayEstPerPuntoContacto' => [$strEstado],
                                                                 'arrayUsrCreacion'         => [$strUsrCreacion],
                                                                 'strFechaIncio'            => $arrayFechaDesde[0],
                                                                 'strFechaFin'              => $arrayFechaHasta[0]];

        if(!empty($strNombres))
        {
            $arrayGetPersonaContacto['arrayPersona'] = ['strComparadorNmbP' => 'LIKE',
                                                        'arrayNombres'      => ['%' . strtoupper(trim($strNombres)) . '%']];
        }
        if(!empty($strApellidos))
        {
            $arrayGetPersonaContacto['arrayPersona'] = ['strComparadorAplP' => 'LIKE',
                                                        'arrayApellidos'    => ['%' . strtoupper(trim($strApellidos)) . '%']];
        }
        $arrayGetPersonaContacto['intStart']    = $intStart;
        $arrayGetPersonaContacto['intLimit']    = $intLimit;
        $arrayGetPersonaContacto['strGroupBy']  = $strGroupBy;

        //Busca contactos
        $jsonData = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
            ->getJSONContactoClienteByTipoRol($arrayGetPersonaContacto);
        $objJsonData = json_decode($jsonData);
        $arrayDatos = array();
        foreach($objJsonData->encontrados as $objInfoPersona):
            $arrayDatos[] = array('intIdPersona'             => $objInfoPersona->intIdPersona,
                                  'strNombres'               => $objInfoPersona->strNombres,
                                  'strApellidos'             => $objInfoPersona->strApellidos,
                'strIdentificacionCliente' => $objInfoPersona->strIdentificacionCliente,
                                  'dateFeCreacion'           => $objInfoPersona->dateFeCreacion->date,
                                  'strUsuarioCreacion'       => $objInfoPersona->strUsrCreacion,
                                  'strTipoContacto'          => $objInfoPersona->strDescripcionRol,
                                  'strEstado'                => $objInfoPersona->strEstado,
                                  'intIdPersonaEmpresaRol'   => $objInfoPersona->intIdPersonaEmpresaRol,
                                  'intIdTitulo'              => $objInfoPersona->intIdTitulo,
                                  'strTitulo'                => $objInfoPersona->strDescripcionTitulo,
                                  'strUrlShow'               => $this->generateUrl('contacto_show', array('id' => $objInfoPersona->intIdPersona)),
                                  'strUrlEdit'               => $this->generateUrl('contacto_edit', array('id' => $objInfoPersona->intIdPersona)),
                                  'strUrlDelet'              => ("Convertido" !== $objInfoPersona->estado) ? 
                                                                $this->generateUrl('contacto_delete_ajax', 
                                                                                   array('id' => $objInfoPersona->intIdPersona)) : "");
        endforeach;
        $objResponse = new Response(json_encode(array('intTotalContactos'  => count($objJsonData->total),
                                                      'jsonContactos'      => $arrayDatos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //gridAction

    /**
     * gridAction, muestra la informacion del contacto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec> 
     * @version 1.1 18-12-2014 
     * @since 1.0
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016 Se modifica el metodo para una mejor presentacion de la informacion del contacto
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_5-6")
     */
    public function showAction($id)
    {

        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $intIdPersonaEmpresaRol = $objRequest->get('intIdPersonaEmpresaRol');
        $intIdPersonaEmpresaRol = (!empty($intPersonaEmpresaRol)) ? $intPersonaEmpresaRol : 0;
        $intIdPunto             = $objRequest->get('intIdPunto');
        $intIdPunto             = (!empty($intIdPunto)) ? $intIdPunto : 0;
        $em_seguridad           = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu         = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("5", "1");
        $objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        return $this->render('comercialBundle:contacto:show.html.twig', array('intIdPersona'           => $id, 
                                                                              'item'                   => $entityItemMenu,
                'intIdPersonaEmpresaRol' => $intIdPersonaEmpresaRol,
                                                                              'intIdPunto'             => $intIdPunto));
    }

    /**
     * getRolesPersonaPuntoAction, muestra la informacion de los roles de la persona o punto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec> 
     * @version 1.0 17-04-2016
     * @since 1.0
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 16-10-2019 Se agrega validación para que no retorne roles del contacto en estado 'ELiminado'
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     *
     * @Secure(roles="ROLE_5-6")
     */
    public function getRolesPersonaPuntoAction()
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objReturnResponse   = new ReturnResponse();
        $objResponse         = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $emComercial         = $this->getDoctrine()->getManager();

        $intIdPersona           = $objRequest->get('intIdPersona');
        $strTipoConsulta        = $objRequest->get('strTipoConsulta');
        $intIdPersonaEmpresaRol = $objRequest->get('intIdPersonaEmpresaRol');
        $intIdPunto             = $objRequest->get('intIdPunto');
        $intLimit               = $objRequest->get('limit');
        $intStart               = $objRequest->get('start');

        $arrayParametro['arrayEmpresaGrupo']    = ['arrayEmpresaGrupo'      => [$objSession->get('prefijoEmpresa')]];
        $arrayParametro['arrayPersonaContacto'] = ['arrayPersona'           => [$intIdPersona],
                                                       'arrayEstado'            => [$objRequest->get('strEstado')],
                                                       'arrayPersonaEmpresaRol' => [$intIdPersonaEmpresaRol],
                                                       'arrayPunto'             => [$intIdPunto]];
        $arrayParametro['intLimit']             = $intLimit;
        $arrayParametro['intStart']             = $intStart;
        $arrayParametro['strTipoConsulta']      = $strTipoConsulta;

        $objResultPersonaContacto   = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
            ->getRolesPersonaPunto($arrayParametro);

        foreach($objResultPersonaContacto->getRegistros() as $arrayInfoPersonaEmpresaRol):
            if($arrayInfoPersonaEmpresaRol['strEstadoIPER'] !== 'Eliminado')
            {
                $arrayPersonaContacto[] = ['intIdRol'               => $arrayInfoPersonaEmpresaRol['intIdRol'],
                                           'intIdPersonaEmpresaRol' => $arrayInfoPersonaEmpresaRol['intIdPersonaEmpresaRol'],
                                           'strDescripcionRol'      => $arrayInfoPersonaEmpresaRol['strDescripcionRol'],
                                           'strUsrCreacion'         => $arrayInfoPersonaEmpresaRol['strUsrCreacionIPER'],
                                           'strFeCreacion'          => ($arrayInfoPersonaEmpresaRol['dateFeCreacionIPER']) ?
                                               date_format($arrayInfoPersonaEmpresaRol['dateFeCreacionIPER'], "d-m-Y H:i:s")
                                               : '',
                                           'strEstado'              => $arrayInfoPersonaEmpresaRol['strEstadoIPER']];
            }
        endforeach;

        $objReturnResponse->setRegistros($arrayPersonaContacto);
        $objReturnResponse->setTotal($objResultPersonaContacto->getTotal());

        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //getRolesPersonaPuntoAction

    /**
     * getInfoPersonaAction, obtiene la informacion de la persona y persona empresa rol.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 29-03-2019 Se agregan nuevos tipos de identificacion para Telconet Guatemala.
     *  
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function getInfoPersonaAction()
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objReturnResponse   = new ReturnResponse();
        $objResponse         = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $emComercial         = $this->getDoctrine()->getManager();
        $arrayInfoPersona    = array();
        $intIdPersona        = $objRequest->get('intIdPersona');
        $strSoloTipoContacto = $objRequest->get('strSoloTipoContacto');
        $intLimit            = $objRequest->get('limit');
        $intStart            = $objRequest->get('start');
        if(!$intIdPersona)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No se esta enviando el id de la persona.");
        }
        $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
        if(!$entityInfoPersona)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No se econtro al contacto.");
        }
        $arrayInfoPersona['intIdPersona']               = $entityInfoPersona->getId();
        $arrayInfoPersona['strCalificacionCrediticia']  = $entityInfoPersona->getCalificacionCrediticia();
        $arrayInfoPersona['strOrigenProspecto']         = $entityInfoPersona->getOrigenProspecto();
        $arrayInfoPersona['strTipoIdentificacion']      = $entityInfoPersona->getTipoIdentificacion();
        $arrayInfoPersona['strIdentificacionCliente']   = $entityInfoPersona->getIdentificacionCliente();
        $arrayInfoPersona['strTipoEmpresa']             = $entityInfoPersona->getTipoEmpresa();
        $arrayInfoPersona['strEstadoCivil']             = $entityInfoPersona->getEstadoCivil();
        $arrayInfoPersona['strTipoTributario']          = $entityInfoPersona->getTipoTributario();
        $arrayInfoPersona['strNombres']                 = $entityInfoPersona->getNombres();
        $arrayInfoPersona['strApellidos']               = $entityInfoPersona->getApellidos();
        $arrayInfoPersona['strRazonSocial']             = $entityInfoPersona->getRazonSocial();
        $arrayInfoPersona['strRepresentanteLegal']      = $entityInfoPersona->getRepresentanteLegal();
        $arrayInfoPersona['strFfechaNacimiento']        = $entityInfoPersona->getFechaNacimiento();
        $arrayInfoPersona['strDireccion']               = $entityInfoPersona->getDireccion();
        $arrayInfoPersona['strLogin']                   = $entityInfoPersona->getLogin();
        $arrayInfoPersona['strCargo']                   = $entityInfoPersona->getCargo();
        $arrayInfoPersona['strDireccionTributaria']     = $entityInfoPersona->getDireccionTributaria();
        $arrayInfoPersona['strGenero']                  = $entityInfoPersona->getGenero();
        $arrayInfoPersona['strEstado']                  = $entityInfoPersona->getEstado();
        $arrayInfoPersona['strFeCreacion']              = $entityInfoPersona->getFeCreacion();
        $arrayInfoPersona['strDescripcionTitulo']       = 'Sin titulo';
        
        switch($arrayInfoPersona['strTipoIdentificacion']):
            case 'CED': $arrayInfoPersona['strDescTipoIdentificacion'] = 'Cedula';
                break;
            case 'RUC': $arrayInfoPersona['strDescTipoIdentificacion'] = 'Ruc';
                break;
            case 'NIT': $arrayInfoPersona['strDescTipoIdentificacion'] = 'Nit';
                break;
            case 'DPI': $arrayInfoPersona['strDescTipoIdentificacion'] = 'Dpi';
                break;
            case 'PAS': $arrayInfoPersona['strDescTipoIdentificacion'] = 'Pasaporte';
                break;            
            default: $arrayInfoPersona['strDescTipoIdentificacion'] = 'Cedula';
                break;
        endswitch;
        if($entityInfoPersona->getTituloId())
        {
            $arrayInfoPersona['strDescripcionTitulo'] = $entityInfoPersona->getTituloId()->getDescripcionTitulo();
        }

        $arrayParametro['arrayEmpresaGrupo']        = ['arrayEmpresaGrupo' => [$objSession->get('prefijoEmpresa')]];
        $arrayParametro['arrayPersonaEmpresaRol']   = ['arrayPersona'      => [$entityInfoPersona->getId()],
                                                       'arrayEstado'       => [$objRequest->get('strEstado')]];
        $arrayParametro['intLimit']                 = $intLimit;
        $arrayParametro['intStart']                 = $intStart;

        $arrayAdmiRol       = array();
        $objResultAdmiRol   = $emComercial->getRepository('schemaBundle:AdmiRol')->getResultadoRolesPersona($arrayParametro);
        foreach($objResultAdmiRol->registros as $arrayInfoPersonaEmpresaRol):
            $arrayAdmiRol[] = ['intIdRol'               => $arrayInfoPersonaEmpresaRol['intIdRol'],
                'intIdPersonaEmpresaRol' => $arrayInfoPersonaEmpresaRol['intIdPersonaEmpresaRol'],
                               'strDescripcionRol'      => $arrayInfoPersonaEmpresaRol['strDescripcionRol'],
                               'strUsrCreacion'         => $arrayInfoPersonaEmpresaRol['strUsrCreacionIPER'],
                               'strFeCreacion'          => ($arrayInfoPersonaEmpresaRol['dateFeCreacionIPER']) ? 
                    date_format($arrayInfoPersonaEmpresaRol['dateFeCreacionIPER'], "d-m-Y H:i:s") : '',
                               'strEstado'              => $arrayInfoPersonaEmpresaRol['strEstadoIPER']];
        endforeach;
        if($arrayAdmiRol)
        {
            $arrayInfoPersona['arrayRol'] = $arrayAdmiRol;
        }
        $objReturnResponse->setRegistros($arrayInfoPersona);
        if(!empty($strSoloTipoContacto))
        {
            $objReturnResponse->setRegistros($arrayAdmiRol);
            $objReturnResponse->setTotal($objResultAdmiRol->total);
        }
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //getInfoPersonaAction

    /**
     * getTipoContactoAction, obtiene el tipo de contacto de una persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function getTipoContactoAction()
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objReturnResponse   = new ReturnResponse();
        $objResponse         = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $emComercial         = $this->getDoctrine()->getManager();
        $arrayInfoPersona    = array();
        $intIdPersona        = $objRequest->get('intIdPersona');
        $strSoloTipoContacto = $objRequest->get('strSoloTipoContacto');
        if(!$intIdPersona)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No se esta enviando el id de la persona.");
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
        if(!$entityInfoPersona)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No se econtro al contacto.");
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        $arrayInfoPersona['intIdPersona']               = $entityInfoPersona->getId();
        $arrayInfoPersona['strCalificacionCrediticia']  = $entityInfoPersona->getCalificacionCrediticia();
        $arrayInfoPersona['strOrigenProspecto']         = $entityInfoPersona->getOrigenProspecto();
        $arrayInfoPersona['strTipoIdentificacion']      = $entityInfoPersona->getTipoIdentificacion();
        $arrayInfoPersona['strIdentificacionCliente']   = $entityInfoPersona->getIdentificacionCliente();
        $arrayInfoPersona['strTipoEmpresa']             = $entityInfoPersona->getTipoEmpresa();
        $arrayInfoPersona['strEstadoCivil']             = $entityInfoPersona->getEstadoCivil();
        $arrayInfoPersona['strTipoTributario']          = $entityInfoPersona->getTipoTributario();
        $arrayInfoPersona['strNombres']                 = $entityInfoPersona->getNombres();
        $arrayInfoPersona['strApellidos']               = $entityInfoPersona->getApellidos();
        $arrayInfoPersona['strRazonSocial']             = $entityInfoPersona->getRazonSocial();
        $arrayInfoPersona['strRepresentanteLegal']      = $entityInfoPersona->getRepresentanteLegal();
        $arrayInfoPersona['strFfechaNacimiento']        = $entityInfoPersona->getFechaNacimiento();
        $arrayInfoPersona['strDireccion']               = $entityInfoPersona->getDireccion();
        $arrayInfoPersona['strLogin']                   = $entityInfoPersona->getLogin();
        $arrayInfoPersona['strCargo']                   = $entityInfoPersona->getCargo();
        $arrayInfoPersona['strDireccionTributaria']     = $entityInfoPersona->getDireccionTributaria();
        $arrayInfoPersona['strGenero']                  = $entityInfoPersona->getGenero();
        $arrayInfoPersona['strEstado']                  = $entityInfoPersona->getEstado();
        $arrayInfoPersona['strFeCreacion']              = $entityInfoPersona->getFeCreacion();
        $arrayInfoPersona['strDescripcionTitulo']       = 'Sin titulo';
        if($entityInfoPersona->getTituloId())
        {
            $arrayInfoPersona['strDescripcionTitulo'] = $entityInfoPersona->getTituloId()->getDescripcionTitulo();
        }

        $arrayParametro['arrayEmpresaGrupo']        = ['arrayEmpresaGrupo' => [$objSession->get('prefijoEmpresa')]];
        $arrayParametro['arrayPersonaEmpresaRol']   = ['arrayPersona'      => [$entityInfoPersona->getId()]];

        $arrayAdmiRol       = array();
        $objResultAdmiRol   = $emComercial->getRepository('schemaBundle:AdmiRol')->getResultadoRolesPersona($arrayParametro);
        foreach($objResultAdmiRol->registros as $arrayInfoPersonaEmpresaRol):
            $arrayAdmiRol[] = ['intIdRol'           => $arrayInfoPersonaEmpresaRol['intIdRol'],
                               'strDescripcionRol'  => $arrayInfoPersonaEmpresaRol['strDescripcionRol'],
                               'strUsrCreacion'     => $arrayInfoPersonaEmpresaRol['strUsrCreacionIPER'],
                               'strFeCreacion'      => ($arrayInfoPersonaEmpresaRol['dateFeCreacionIPER']) ? 
                    date_format($arrayInfoPersonaEmpresaRol['dateFeCreacionIPER'], "d-m-Y H:i:s") : '',
                               'strEstado'          => $arrayInfoPersonaEmpresaRol['strEstadoIPER']];
        endforeach;
        if($arrayAdmiRol)
        {
            $arrayInfoPersona['arrayRol'] = $arrayAdmiRol;
        }
        $objReturnResponse->setRegistros($arrayInfoPersona);
        if(!empty($strSoloTipoContacto))
        {
            $objReturnResponse->setRegistros($arrayAdmiRol);
            $objReturnResponse->setTotal($objResultAdmiRol->total);
        }
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //getTipoContactoAction

    /**
     * getInfoPersonaRelacionadasAlContactoAction, obtiene la informacion de las personas relacionadas al contacto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInfoPersonaRelacionadasAlContactoAction()
    {
        $objRequest     = $this->getRequest();
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $emComercial    = $this->getDoctrine()->getManager();
        $intIdPersona   = $objRequest->get('intIdPersona');

        $arrayParametros['arrayPersonaContacto']    = ['arrayPersonaContactoId' => [$intIdPersona]];

        $objReturnResponse = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
            ->getResultadoInfoPersonaRelacionadasAlContacto($arrayParametros);
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //getInfoPersonaRelacionadasAlContactoAction

    /**
     * getInfoPersonaFormaContactoAction, obtiene la informacion de formas de contacto de la persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInfoPersonaFormaContactoAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $intIdPersona       = $objRequest->get('intIdPersona');
        $strEstado          = $objRequest->get('strEstado');
        $strJoinByPunto     = $objRequest->get('strJoinByPunto');
        $arrayPuntoSession  = $objSession->get('ptoCliente');
        $emComercial        = $this->getDoctrine()->getManager();

        $arrayParametros['strJoinByPunto']            = $strJoinByPunto;
        if(!empty($strJoinByPunto) && empty($arrayPuntoSession['id']))
        {
            $arrayPuntoSession['id'] = -1;
        }
        $arrayParametros['arrayPersonaPuntoFormCont'] = ['arrayPersona' => [$intIdPersona],
                                                         'arrayPunto'   => [$arrayPuntoSession['id']],
                                                         'arrayEstado'  => [$strEstado]];

        $objReturnResponse = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
            ->getResultadoPuntoPersonaFormaContacto($arrayParametros);

        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //getInfoPersonaFormaContactoAction

    /**
     * newAction, redirecciona al twig que muestra el formulario de ingreso de contactos.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-05-2016 Se permite que los clientes y preclientes puedan tener contactos.
     * @since 1.0
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.1 Se aumentan validaciones y obtencion de informacion.
     * 
     * @Secure(roles="ROLE_5-2")
     */
    public function newAction()
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objReturnResponse   = new ReturnResponse();
        $emComercial         = $this->getDoctrine()->getManager();
        $emSeguridad         = $this->getDoctrine()->getManager("telconet_seguridad");
        $arrayClienteSession = $objSession->get('cliente');
        $arrayPuntoSession   = $objSession->get('ptoCliente');
        $intIdPunto             = (!empty($arrayPuntoSession['id'])) ? $arrayPuntoSession['id'] : -1;
        $intIdPersonaEmpresaRol = (!empty($arrayClienteSession['id'])) ? $arrayClienteSession['id'] : -1;
        try
        {
            if(empty($arrayClienteSession['id']) && empty($arrayPuntoSession['id']))
            {
                throw new \Exception("Debe tener en sesion al punto o al cliente, para crear un nuevo contacto.");
            }

            $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("5", "1");
            $objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
            $objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
            $objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
            $objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

            $strNombreCliente = !empty($arrayClienteSession['razon_social']) ?
                $arrayClienteSession['razon_social'] : $arrayClienteSession['nombres'] . ' ' . $arrayClienteSession['apellidos'];

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);

            $arrayParametros = array();
            $arrayParametros['arrayEstadoATR']          = ['arrayEstado' => ['Activo']];
            $arrayParametros['arrayEstadoAR']           = ['arrayEstado' => ['Activo']];
            $arrayParametros['arrayEstadoIER']          = ['arrayEstado' => ['Activo']];
            $arrayParametros['arrayEstadoIPER']         = ['arrayEstado' => ['Activo', 'Pendiente', 'Modificado'], 'strComparador' => 'IN'];
            $arrayParametros['arrayEstadoIPR']          = ['arrayEstado' => ['Activo', 'Pendiente', 'Modificado'], 'strComparador' => 'IN'];
            $arrayParametros['arrayDescripcionTipoRol'] = ['arrayDescripcionTipoRol' => ['Cliente', 'Pre-cliente'], 'strComparador' => 'IN'];
            $arrayParametros['arrayEmpresaCod']         = ['arrayEmpresaCod' => [$objSession->get('idEmpresa')]];
            $arrayParametros['arrayPersona']            = ['arrayPersona'    => [$arrayClienteSession['id']]];

            $objJsonInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->getResultadoPersonaEmpresaRol($arrayParametros);
            if(0 === $objJsonInfoPersonaEmpresaRol->total)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' EL cliente no es tipo rol Cliente o Pre-cliente.');
            }
        }
        catch(\Exception $e)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' ' . $e->getMessage());
        }

        return $this->render('comercialBundle:contacto:new.html.twig', array('item'                     => $entityItemMenu,
                                                                             'strNombreCliente'         => $strNombreCliente,
                                                                             'objReturnResponse'        => $objReturnResponse,
                                                                             'intIdPunto'               => $intIdPunto,
                                                                             'intIdPersonaEmpresaRol'   => $intIdPersonaEmpresaRol));
    } //newAction


    /**
     * creaPersonaFormaContactoAction, metodo que crea contacto al cliente.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.0
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.3 16-12-2016 - Se agrego metodo envioNotificacionContactoNuevo, el cual notifica por medio de un correo eletronico 
     *                           cuando se crea un nuevo contacto por cualquier motivo.
     * @author Luis Cabrera
     * @version 1.4 28-09-2017 - Se valida la identificación del contacto.
     * @Secure(roles="ROLE_5-3")
     * 
     * @author David Leon
     * @version 1.5 16-04-2019 - Se registra la Escalabilidad para los tipos de contacto de Seguridad Escalable.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.6 08-10-2019 - Se agrega implementación para creación de contactos de manera masiva.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.7 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     * 
     * @author David Leon
     * @version 1.8 03-05-2022 - Se modifica para no registrar 2 veces el rol cuando se ingresa por cliente y punto.
     */
    public function creaPersonaFormaContactoAction()
    {
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();
        $objReturnResponse        = new ReturnResponse();
        $emComercial              = $this->getDoctrine()->getManager();
        $serviceUtil              = $this->get('schema.Util');
        $intIdTitulo              = $objRequest->get('intIdTitulo');
        $strNombreContacto        = $objRequest->get('strNombreContacto');
        $strApellidoContacto      = $objRequest->get('strApellidoContacto');
        $strEmpresaRol            = $objRequest->get('strEmpresaRol');
        $strEscalabilidad         = $objRequest->get('strEscalabilidad');
        $strHorarios              = $objRequest->get('strHorariosContact');
        $strRbValue               = $objRequest->get('rbValue');
        $strTipoIdentificacion    = $objRequest->get('strTipoIdentificacion');
        $strIdentificacionCliente = $objRequest->get('strIdentificacionCliente');
        $objPersonasFormaContacto = json_decode($objRequest->get('jsonCreaPersonaFormaContacto'));
        $arrayClienteSession      = $objSession->get('cliente');
        $intIdPais                = $objSession->get('intIdPais');
        $arrayPuntoSession        = $objSession->get('ptoCliente');
        $arrayContactoParametros  = array();
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $strEmpresaId             = $objSession->get('idEmpresa');
        $arrayTipoContacto        = array();
        $intTipoContacto          = 1;
        $boolEnvioNotifContacto   = false;
        $strAlcance               = $objRequest->get('strAlcance');
        $arrayIdPuntos            = json_decode($objRequest->get('strArrayIdPuntos'), true);
        $boolIncluirNivelCliente  = filter_var($objRequest->get('booleanIncluirNivelCliente'), FILTER_VALIDATE_BOOLEAN);

        //Termina el metodo si no se envia un nivel de creacion
        if(empty($strRbValue))
        {
            throw new \Exception('No selecciono nivel para la creacion del contacto.');
        }

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);

        $emComercial->getConnection()->beginTransaction();

        try
        {
            if(isset($strTipoIdentificacion) && $strTipoIdentificacion != '')
            {
                //VALIDA LA IDENTIFICACIÓN
                $objRepositorio = $emComercial->getRepository('schemaBundle:InfoPersona');
                $arrayParamValidaIdentifica = array(
                                                            'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                            'strIdentificacionCliente'  => $strIdentificacionCliente,
                                                            'intIdPais'                 => $intIdPais,
                                                            'strCodEmpresa'             => $strEmpresaId
                                                    );
                $strMensaje     = $objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica);
                if($strMensaje != null && $strMensaje != '')
                {
                    throw new \Exception($strMensaje);
                }
            }
            //Busca que el rol del cliente sea Cliente o Pre-cliente
            $arrayParametros = array();
            $arrayParametros['arrayEstadoATR']  = ['arrayEstado' => ['Activo']];
            $arrayParametros['arrayEstadoAR']   = ['arrayEstado' => ['Activo']];
            $arrayParametros['arrayEstadoIER']  = ['arrayEstado' => ['Activo']];
            $arrayParametros['arrayEstadoIPER']         = ['arrayEstado' => ['Activo', 'Pendiente', 'Modificado'],  'strComparador' => 'IN'];
            $arrayParametros['arrayEstadoIPR']          = ['arrayEstado' => ['Activo', 'Pendiente', 'Modificado'],  'strComparador' => 'IN'];
            $arrayParametros['arrayDescripcionTipoRol'] = ['arrayDescripcionTipoRol' => ['Cliente', 'Pre-cliente'], 'strComparador' => 'IN'];
            $arrayParametros['arrayEmpresaCod']         = ['arrayEmpresaCod'         => [$objSession->get('idEmpresa')]];
            $arrayParametros['arrayPersona']            = ['arrayPersona'            => [$arrayClienteSession['id']]];

            $objJsonInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->getResultadoPersonaEmpresaRol($arrayParametros);

            //Si el resultado es 0 no se encontraron regitros de cliente o precliente regitros
            if(0 === $objJsonInfoPersonaEmpresaRol->total)
            {
                throw new \Exception("No se puede crear el contacto, el cliente no es tipo rol Cliente o Pre-cliente.");
            }

            //Termina el metodo cuando no encuentra InfoPersonaFormaContacto
            if(!$arrayClienteSession['id_persona_empresa_rol'])
            {
                throw new \Exception('Cliente no tiene persona empresa rol.');
            }

            $intPersonaEmpresaRol        = $arrayClienteSession['id_persona_empresa_rol'];
            $entityInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRol);

            $arrayGetPersonaContacto = array();
            $arrayGetPersonaContacto['arrayTipoRol']    = ['arrayDescripcionTipoRol'    => ['Contacto']];
            $arrayGetPersonaContacto['arrayEmpresaRol'] = ['arrayEmpresaCod'            => [$objSession->get('idEmpresa')],
                                                           'arrayEstadoER'              => ['Eliminado', 'Anulado', 'Inactivo'],
                                                           'strComparadorEstR'          => 'NOT IN'];

            $arrayGetPersonaContacto['arrayPerEmpRol'] = ['arrayEstadoPerEmpRol'        => ['Eliminado', 'Anulado', 'Inactivo'],
                                                          'strComparadorEstPER'         => 'NOT IN'];
            $arrayGetPersonaContacto['arrayPersonaPuntoContacto'] = ['arrayPersonaEmpresaRol' => [$intPersonaEmpresaRol],
                'arrayEstPerPuntoContacto' => ['Eliminado', 'Anulado', 'Inactivo'],
                'strComparadorEstPPC' => 'NOT IN'];
            $arrayGetPersonaContacto['arrayPersona'] = ['arrayNombres'          => [strtoupper(trim($strNombreContacto))],
                                                        'arrayApellidos'        => [strtoupper(trim($strApellidoContacto))],
                                                        'arrayEstadoIPR'        => ['Eliminado', 'Anulado', 'Inactivo'],
                                                        'strComparadorEstIPR'   => 'NOT IN'];

            if($strAlcance === 'rbMasivo')
            {
                if ($boolIncluirNivelCliente)
                {
                    //Termina el metodo si existe un contacto con el mismo nombre a nivel de persona
                    $jsonPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                        ->getContactosDeClientePunto($arrayGetPersonaContacto);
                    if (0 !== $jsonPersonaContacto->total)
                    {
                        throw new \Exception("Ya existe un contacto con el mismo nombre a nivel de cliente, favor revisar.");
                    }
                }

                if($strRbValue === 'rbTodosPuntos')
                {
                    $arrayParametrosObtenerPuntos = array(
                        'idper'            => $intPersonaEmpresaRol,
                        'rol'              => $arrayClienteSession['nombre_tipo_rol'],
                        'strCodEmpresa'    => $strEmpresaId,
                        'intStart'         => 0,
                        'intLimit'         => PHP_INT_MAX,
                        'serviceInfoPunto' => $this->get('comercial.InfoPunto'),
                        'strNotInEstados'  => array('Eliminado', 'Cancelado', 'Anulado')
                    );

                    $arrayEntitiesInfoPuntos =  $emComercial->getRepository('schemaBundle:InfoPunto')
                        ->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametrosObtenerPuntos);
                    $arrayIdPuntos = array_map(function($entityInfoPuntos)
                    {
                        return $entityInfoPuntos['id'];
                    }, $arrayEntitiesInfoPuntos['registros']);

                }
                else if($strRbValue === 'rbSeleccionPuntos')
                {
                    if(is_array($arrayIdPuntos) && count($arrayIdPuntos) > 0)
                    {
                        $arrayGetPersonaContacto['strJoinPunto'] = 'BUSCA_POR_PUNTO';
                        $arrayGetPersonaContacto['arrayPersonaPuntoContacto'] = ['arrayPunto' => $arrayIdPuntos];
                    $jsonPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                        ->getContactosDeClientePunto($arrayGetPersonaContacto);

                        //Termina el metodo si existe un contacto con el mismo nombre a nivel de punto
                    if (0 !== $jsonPersonaContacto->total)
                    {
                            throw new \Exception("Ya existe un contacto con el mismo nombre a nivel de punto, favor revisar.");
                        }
                    }
                    else
                    {
                        throw new \Exception("Debe elegir al menos un punto para agregar nuevo contacto.");
                    }
                }
                else
                {
                    throw new \Exception("Debe elegir una opción de alcance masivo de puntos: \nTodos los puntos o Elegir punto.");
                }
            }
            else
            {
                //Termina el metodo si existe un contacto con el mismo nombre a nivel de persona
                if ('chkBoxCliente' === $strRbValue || 'chkBoxClientePunto' === $strRbValue)
                {
                    $jsonPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                        ->getContactosDeClientePunto($arrayGetPersonaContacto);
                    if (0 !== $jsonPersonaContacto->total)
                    {
                        throw new \Exception("Ya existe un contacto con el mismo nombre a nivel de cliente, favor revisar.");
                    }
                }

                        //Termina el metodo si existe un contacto con el mismo nombre a nivel de punto
                if ('chkBoxPunto' === $strRbValue || 'chkBoxClientePunto' === $strRbValue)
                {
                    //Termina el metodo cuando no esta el punto en sesion
                    if (empty($arrayPuntoSession['id']))
                    {
                        throw new \Exception("Esta intentando crear un contacto a nivel de punto pero no tiene el punto en sesion.");
                    }
                    //Busca el punto
                    $entityInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                        ->find($arrayPuntoSession['id']);
                    $arrayGetPersonaContacto['strJoinPunto'] = 'BUSCA_POR_PUNTO';
                    $arrayGetPersonaContacto['arrayPersonaPuntoContacto'] = ['arrayPunto' => [$arrayPuntoSession['id']]];
                    $jsonPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                        ->getContactosDeClientePunto($arrayGetPersonaContacto);

                    //Termina el metodo si existe un contacto con el mismo nombre a nivel de punto
                    if(0 !== $jsonPersonaContacto->total)
                    {
                        throw new \Exception("Ya existe un contacto con el mismo nombre a nivel de punto, favor revisar.");
                    }
                }
            }

            //Termina el metodo cuando no se envia titulo
            if(empty($intIdTitulo))
            {
                throw new \Exception('No se está enviando el titulo.');
            }

            //Termina el metodo cuando no se envia Nombres o apellidos
            if(empty($strNombreContacto) || empty($strApellidoContacto))
            {
                throw new \Exception('No se está enviando nombre o apellido.');
            }

            //Termina el metodo cuando no se envia empresa rol
            if(empty($strEmpresaRol))
            {
                throw new \Exception('No se está enviando el tipo contacto.');
            }

            //Termina el metodo cuando no se envia formas contactos
            if(!$objPersonasFormaContacto->arrayData)
            {
                throw new \Exception('No se está enviando las formas de contacto.');
            }

            $entityAdmiTitulo       = $emComercial->getRepository('schemaBundle:AdmiTitulo')->find($intIdTitulo);
            $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($objSession->get('idOficina'));

            //Termina el metodo si no existe oficina
            if(!$entityInfoOficinaGrupo)
            {
                throw new \Exception('No existe oficina.');
            }

            //Crea entidad InfoPersona
            $entityInfoPersona = new InfoPersona();
            $entityInfoPersona->setNombres(trim($strNombreContacto));
            $entityInfoPersona->setApellidos(trim($strApellidoContacto));
            if(!empty($strTipoIdentificacion))
            {
                $entityInfoPersona->setTipoIdentificacion($strTipoIdentificacion);
                if(empty($strIdentificacionCliente))
                {
                    throw new \Exception('No esta enviando la identificacion del cliente.');
                }
                $entityInfoPersona->setIdentificacionCliente($strIdentificacionCliente);
            }
            if($entityAdmiTitulo)
            {
                $entityInfoPersona->setTituloId($entityAdmiTitulo);
            }
            $entityInfoPersona->setOrigenProspecto('N');
            $entityInfoPersona->setFeCreacion(new \DateTime('now'));
            $entityInfoPersona->setUsrCreacion($objSession->get('user'));
            $entityInfoPersona->setIpCreacion($objRequest->getClientIp());
            $entityInfoPersona->setEstado('Activo');
            $emComercial->persist($entityInfoPersona);
            $emComercial->flush();

            //Crea un array con los roles enviados
            $arrayEmpresaRol = array_map('trim', explode(",", $strEmpresaRol));

            //Elimina el array con el valor 0
            if('0' === $arrayEmpresaRol[0])
            {
                $arrayEmpresaRol = $objReturnResponse->removeInArray(0, $arrayEmpresaRol);
            }

            if($strAlcance === 'rbMasivo')
            {
                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->beginTransaction();

                $strEmpresaRoles = implode(",", $arrayEmpresaRol);
                $strIdPuntos = implode(",", $arrayIdPuntos);

                $arrayRespuestaMasiva = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                    ->crearContactoMasivo(array('strEmpresaRoles'      => $strEmpresaRoles,
                                                'strIdPuntos'          => $strIdPuntos,
                                                'intIdCliente'         => $intPersonaEmpresaRol,
                                                'intIdPersona'         => $entityInfoPersona->getId(),
                                                'intIdOficina'         => $entityInfoOficinaGrupo->getId(),
                                                'intCrearNivelCliente' => $boolIncluirNivelCliente ? 1 : 0,
                                                'strCodEmpresa'        => $strEmpresaId,
                                                'strUsuario'           => $objSession->get('user'),
                                                'strIp'                => $objRequest->getClientIp(),
                                                'arrayExtraParams'     => array(
                                                    'strDescripcionRol1'   => 'Contacto Seguridad Escalable',
                                                    'strDescripcionCarac1' => 'NIVEL ESCALABILIDAD',
                                                    'strDescripcionCarac2' => 'HORARIO ESCALABILIDAD',
                                                    'strEscalabilidad'     => $strEscalabilidad,
                                                    'strHorario'           => $strHorarios
                                                )));

                if(!isset($arrayRespuestaMasiva['strMensaje']) || empty($arrayRespuestaMasiva['strMensaje']) ||
                    $arrayRespuestaMasiva['strMensaje'] !== 'OK')
                {
                    throw new \Exception('Al momento de crear el contacto masivo.<br/>' . $arrayRespuestaMasiva['strMensaje']);
                }
            }
            else
            {
                //Itera los id de empresa rol para insertar en la esntidad InfoPersonaEmpresaRol
                foreach($arrayEmpresaRol as $intEmpresaRol):

                    $entityInfoEmpresaRol = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')->find($intEmpresaRol);

                if($entityInfoEmpresaRol)
                {
                    $objResultAdmiRol                    = $emComercial->getRepository('schemaBundle:AdmiRol')
                            ->find($entityInfoEmpresaRol->getRolId());
                        //Entra cuando se elige creacion a nivel de cliente o cliente y punto
                    
                    if('chkBoxClientePunto' === $strRbValue)
                    {
                        $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                        $entityPersonaEmpresaRol->setEmpresaRolId($entityInfoEmpresaRol);
                        $entityPersonaEmpresaRol->setPersonaId($entityInfoPersona);
                        $entityPersonaEmpresaRol->setOficinaId($entityInfoOficinaGrupo);
                        $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRol->setUsrCreacion($objSession->get('user'));
                        $entityPersonaEmpresaRol->setEstado('Activo');
                        $emComercial->persist($entityPersonaEmpresaRol);
                        $emComercial->flush();

                        $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaEmpresaRolHist->setEstado($entityInfoPersona->getEstado());
                        $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRolHist->setIpCreacion($objRequest->getClientIp());
                        $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $entityPersonaEmpresaRolHist->setUsrCreacion($objRequest->getClientIp());
                        $emComercial->persist($entityPersonaEmpresaRolHist);
                        $emComercial->flush();
 
                    }
                    if('chkBoxCliente' === $strRbValue || 'chkBoxClientePunto' === $strRbValue)
                    {
                        if('chkBoxClientePunto' != $strRbValue)
                        {    
                            $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                            $entityPersonaEmpresaRol->setEmpresaRolId($entityInfoEmpresaRol);
                            $entityPersonaEmpresaRol->setPersonaId($entityInfoPersona);
                            $entityPersonaEmpresaRol->setOficinaId($entityInfoOficinaGrupo);
                            $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                            $entityPersonaEmpresaRol->setUsrCreacion($objSession->get('user'));
                            $entityPersonaEmpresaRol->setEstado('Activo');
                            $emComercial->persist($entityPersonaEmpresaRol);
                            $emComercial->flush();

                            $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                            $entityPersonaEmpresaRolHist->setEstado($entityInfoPersona->getEstado());
                            $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                            $entityPersonaEmpresaRolHist->setIpCreacion($objRequest->getClientIp());
                            $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                            $entityPersonaEmpresaRolHist->setUsrCreacion($objRequest->getClientIp());
                            $emComercial->persist($entityPersonaEmpresaRolHist);
                            $emComercial->flush();
                        }
                            $entityPersonaContacto = new InfoPersonaContacto();
                            $entityPersonaContacto->setPersonaEmpresaRolId($entityInfoPersonaEmpresaRol);
                            $entityPersonaContacto->setContactoId($entityInfoPersona);
                            $entityPersonaContacto->setFeCreacion(new \DateTime('now'));
                            $entityPersonaContacto->setUsrCreacion($objSession->get('user'));
                            $entityPersonaContacto->setIpCreacion($objRequest->getClientIp());
                            $entityPersonaContacto->setEstado('Activo');
                            $entityPersonaContacto->setPersonaRolId($entityPersonaEmpresaRol);
                            $emComercial->persist($entityPersonaContacto);
                        $emComercial->flush();

                        if(is_object($objResultAdmiRol) && $objResultAdmiRol->getDescripcionRol() === 'Contacto Seguridad Escalable')
                        {
                            $objAdmiCaracteristicaNivel         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('NIVEL ESCALABILIDAD');
                            $objAdmiCaracteristicaHorario       = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('HORARIO ESCALABILIDAD');
                            if($strEscalabilidad && $strHorarios && is_object($objAdmiCaracteristicaNivel) && is_object($objAdmiCaracteristicaHorario))
                            {
                                    $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                                    $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                    $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristicaNivel);
                                    $entityInfoPersonaEmpresaRolCarac->setValor($strEscalabilidad);
                                    $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setEstado('Activo');
                                    $entityInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                                    $emComercial->persist($entityInfoPersonaEmpresaRolCarac);
                                    $emComercial->flush();

                                    $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                                    $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                    $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristicaHorario);
                                    $entityInfoPersonaEmpresaRolCarac->setValor($strHorarios);
                                    $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setEstado('Activo');
                                    $entityInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                                    $emComercial->persist($entityInfoPersonaEmpresaRolCarac);
                                    $emComercial->flush();
                                }
                                else
                                {
                                        throw new \Exception('Favor Escoger Un Nivel de Escalabilidad y Horario.');
                                    }
                                }
                            }

                        //Entra cuando se elige creacion a nivel de punto o cliente y punto
                    if('chkBoxPunto' === $strRbValue || 'chkBoxClientePunto' === $strRbValue)
                    {
                        if($entityInfoPunto)
                        {
                            if('chkBoxClientePunto' != $strRbValue)
                            {
                                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                                $entityPersonaEmpresaRol->setEmpresaRolId($entityInfoEmpresaRol);
                                $entityPersonaEmpresaRol->setPersonaId($entityInfoPersona);
                                $entityPersonaEmpresaRol->setOficinaId($entityInfoOficinaGrupo);
                                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                                $entityPersonaEmpresaRol->setUsrCreacion($objSession->get('user'));
                                $entityPersonaEmpresaRol->setEstado('Activo');
                                $emComercial->persist($entityPersonaEmpresaRol);
                                $emComercial->flush();

                                $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                                $entityPersonaEmpresaRolHist->setEstado($entityInfoPersona->getEstado());
                                $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                                $entityPersonaEmpresaRolHist->setIpCreacion($objRequest->getClientIp());
                                $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                $entityPersonaEmpresaRolHist->setUsrCreacion($objRequest->getClientIp());
                                $emComercial->persist($entityPersonaEmpresaRolHist);
                                $emComercial->flush();
                            }
                            $entityPuntoContacto = new InfoPuntoContacto();
                            $entityPuntoContacto->setPuntoId($entityInfoPunto);
                            $entityPuntoContacto->setContactoId($entityInfoPersona);
                            $entityPuntoContacto->setFeCreacion(new \DateTime('now'));
                            $entityPuntoContacto->setUsrCreacion($objSession->get('user'));
                            $entityPuntoContacto->setIpCreacion($objRequest->getClientIp());
                            $entityPuntoContacto->setEstado('Activo');
                            $entityPuntoContacto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                            $emComercial->persist($entityPuntoContacto);
                            $emComercial->flush();

                            if(is_object($objResultAdmiRol) && $objResultAdmiRol->getDescripcionRol() === 'Contacto Seguridad Escalable')
                            {
                                $objAdmiCaracteristicaNivel         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('NIVEL ESCALABILIDAD');
                                $objAdmiCaracteristicaHorario       = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica('HORARIO ESCALABILIDAD');
                                if($strEscalabilidad && $strHorarios && is_object($objAdmiCaracteristicaNivel) && is_object($objAdmiCaracteristicaHorario))
                                {
                                    $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                                    $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                    $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristicaNivel);
                                    $entityInfoPersonaEmpresaRolCarac->setValor($strEscalabilidad);
                                    $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setEstado('Activo');
                                    $entityInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                                    $emComercial->persist($entityInfoPersonaEmpresaRolCarac);
                                    $emComercial->flush();

                                    $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                                    $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                    $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristicaHorario);
                                    $entityInfoPersonaEmpresaRolCarac->setValor($strHorarios);
                                    $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                                    $entityInfoPersonaEmpresaRolCarac->setEstado('Activo');
                                    $entityInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                                    $emComercial->persist($entityInfoPersonaEmpresaRolCarac);
                                    $emComercial->flush();
                                }
                                else
                                {
                                    throw new \Exception('Favor Escoger Un Nivel de Escalabilidad y Horario.');
                                }
                            }
                            }
                        }
                        $arrayTipoContacto[$intTipoContacto] = $objResultAdmiRol->getDescripcionRol();
                    $intTipoContacto                     = $intTipoContacto + 1;

                    }

                endforeach;
            }

            //Se intera las formas de contactos para ser insertadas en InfoPersonaFormaContacto
            foreach($objPersonasFormaContacto->arrayData as $objPersonaFormaContacto):

                $entityAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                    ->findOneBy(array('descripcionFormaContacto' => $objPersonaFormaContacto->strDescripcionFormaContacto));

                $entityInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                $entityInfoPersonaFormaContacto->setValor($objPersonaFormaContacto->strValorFormaContacto);
                $entityInfoPersonaFormaContacto->setEstado('Activo');
                $entityInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                $entityInfoPersonaFormaContacto->setFormaContactoId($entityAdmiFormaContacto);
                $entityInfoPersonaFormaContacto->setIpCreacion($objRequest->getClientIp());
                $entityInfoPersonaFormaContacto->setPersonaId($entityInfoPersona);
                $entityInfoPersonaFormaContacto->setUsrCreacion($objSession->get('user'));

                $emComercial->persist($entityInfoPersonaFormaContacto);
                $emComercial->flush();

            endforeach;

            $emComercial->getConnection()->commit();

            $arrayTipoContacto[0]   = $strEmpresaId;

            $boolEnvioNotifContacto = $this->permiteEnvioNotificacionContacto($arrayTipoContacto);

            if ($boolEnvioNotifContacto && $strAlcance !== 'rbMasivo')
            {

                $strFeCreacion      = strval(date_format($entityInfoPersona->getFeCreacion(), "d-m-Y H:i")) ?
                    strval(date_format($entityInfoPersona->getFeCreacion(), "d-m-Y H:i")) : "";

                $arrayContactoParametros['strNombres']                 = $entityInfoPersona->getNombres();
                $arrayContactoParametros['strApellidos']               = $entityInfoPersona->getApellidos();
                $arrayContactoParametros['strTitulo']                  = $entityAdmiTitulo->getDescripcionTitulo();
                $arrayContactoParametros['strIdentificacion']          = $entityInfoPersona->getIdentificacionCliente();
                $arrayContactoParametros['strFeCreacion']              = $strFeCreacion;
                $arrayContactoParametros['strUsrCreacion']             = $entityInfoPersona->getUsrCreacion();
                $arrayContactoParametros['strEmpresaId']               = $strEmpresaId;
                $arrayContactoParametros['strRazonSocialCliente']      = $arrayClienteSession['razon_social'];
                $arrayContactoParametros['strIdentificacionCliente']   = $arrayClienteSession['identificacion'];
                $arrayContactoParametros['strLogin']                   = $arrayPuntoSession['login'];

                $this->envioNotificacionContactoNuevo($arrayContactoParametros);
            }

            $objReturnResponse->setRegistros($this->generateUrl('contacto_show', array('id' => $entityInfoPersona->getId())));

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Se creo contacto!');

            }
        catch(\Exception $ex)
        {
            if($emComercial->getConnection()->isConnected())
            {
                $emComercial->getConnection()->rollback();
            }

            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $serviceUtil->insertError(
                'Telcos+',
                __METHOD__,
                isset($arrayRespuestaMasiva['strMensaje']) && !empty($arrayRespuestaMasiva['strMensaje'])
                    ? $arrayRespuestaMasiva['strMensaje']
                    : $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'), $objRequest->getClientIp());
        }
        $emComercial->getConnection()->close();
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    /**
     * asignaTipoContactoAjaxAction, asigna roles a una persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.0
     * 
     * @Secure(roles="ROLE_5-3")
     *
     * 
     * @author David Leon <mdleon@telconet.ec> Se Modifica para que permita borrar los registros de la Info_Persona_Empresa_Rol_Carac
     * cuando se elimina todo el contacto
     * @version 1.2 16-04-2019
     */
    public function eliminarContactoAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        $objResponse            = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $emComercial            = $this->getDoctrine()->getManager();

        $intIdPersona           = $objRequest->get('intIdPersona');
        $intIdPersonaEmpresaRol = $objRequest->get('intIdPersonaEmpresaRol');
        $intIdPunto             = $objRequest->get('intIdPunto');
        $strDelete              = $objRequest->get('strDelete');
        $strEmpresaId           = $objSession->get('idEmpresa');
        $intIdOficina           = $objSession->get('idOficina');
        $boolAlcanceMasivo      = filter_var($objRequest->get('strAlcanceMasivo'), FILTER_VALIDATE_BOOLEAN);

        //Termina el metodo si no se envia el id de la persona
        if(empty($intIdPersona) || $intIdPersona <= 0)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No se esta enviando el id del contacto.");
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);

        $emComercial->getConnection()->beginTransaction();

        try
        {
            if($boolAlcanceMasivo)
            {
                //Termina el metodo si no existe contacto
                if(!is_object($entityInfoPersona))
                {
                    throw new \Exception('No existe el contacto');
                }

                $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);

                //Termina el metodo si no existe oficina
                if(!is_object($entityInfoOficinaGrupo))
                {
                    throw new \Exception('No existe oficina.');
                }

                $arrayRespuestaMasiva = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                    ->eliminarContactoMasivo(array('intIdCliente'          => $intIdPersonaEmpresaRol,
                                                   'intIdPersona'          => $intIdPersona,
                                                   'intIdOficina'          => $entityInfoOficinaGrupo->getId(),
                                                   'strCodEmpresa'         => $strEmpresaId,
                                                   'strUsuario'            => $objSession->get('user'),
                                                   'strIp'                 => $objRequest->getClientIp()));

                if(!isset($arrayRespuestaMasiva['strMensaje']) || empty($arrayRespuestaMasiva['strMensaje']) ||
                    $arrayRespuestaMasiva['strMensaje'] !== 'OK')
                {
                    throw new \Exception('Al momento eliminar contacto masivo.');
                }
            }
            else
            {
                //Si el tipo a eliminar es diferente de punto busca en la entidad InfoPersonaContacto
                if ('PUNTO' !== $strDelete)
                {
                    $arrayInfoPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                        ->findBy(array('contactoId' => $intIdPersona,
                            'personaEmpresaRolId' => $intIdPersonaEmpresaRol,
                            'estado' => 'Activo'));
                    //Termina el mentodo si no exsiste el registro
                    if (!$arrayInfoPersonaContacto)
                    {
                        $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No exite persona empresa rol para el cliente.");
                        $objResponse->setContent(json_encode((array)$objReturnResponse));
                        return $objResponse;
                    }

                    //Itera el array con los registros encontrados en la entidad InfoPersonaContacto, procede a eliminar
                    foreach ($arrayInfoPersonaContacto as $objInfoPersonaContacto):

                        $objInfoPersonaContacto->setEstado('Eliminado');
                        $emComercial->persist($objInfoPersonaContacto);
                        $emComercial->flush();

                        $entityInfoPersonaEmpresaRol = $objInfoPersonaContacto->getPersonaRolId();
                        $entityInfoPersonaEmpresaRol->setEstado('Eliminado');
                        $emComercial->persist($entityInfoPersonaEmpresaRol);
                        $emComercial->flush();

                        $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaEmpresaRolHist->setEstado($entityInfoPersonaEmpresaRol->getEstado());
                        $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRolHist->setIpCreacion($objRequest->getClientIp());
                        $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityInfoPersonaEmpresaRol);
                        $entityPersonaEmpresaRolHist->setUsrCreacion($objRequest->getClientIp());
                        $emComercial->persist($entityPersonaEmpresaRolHist);
                        $emComercial->flush();

                    endforeach;
                }

                //Si el tipo a eliminar es igual de PUNTO busca en la entidad InfoPuntoContacto
                if ('PUNTO' === $strDelete)
                {
                    $arrayInfoPuntoContacto = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                        ->findBy(array('contactoId' => $intIdPersona,
                            'puntoId' => $intIdPunto,
                            'estado' => 'Activo'));
                    //Termina el mentodo si no exsiste el registro
                    if (!$arrayInfoPuntoContacto)
                    {
                        $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . "No exite punto.");
                        $objResponse->setContent(json_encode((array)$objReturnResponse));
                        return $objResponse;
                    }

                    //Itera el array con los registros encontrados en la entidad InfoPuntoContacto, procede a eliminar
                    foreach ($arrayInfoPuntoContacto as $objInfoPuntoContacto):

                        $objInfoPuntoContacto->setEstado('Eliminado');
                        $emComercial->persist($objInfoPuntoContacto);
                        $emComercial->flush();

                        $entityInfoPersonaEmpresaRol = $objInfoPuntoContacto->getPersonaEmpresaRolId();
                        $entityInfoPersonaEmpresaRol->setEstado('Eliminado');
                        $emComercial->persist($entityInfoPersonaEmpresaRol);
                        $emComercial->flush();

                        $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaEmpresaRolHist->setEstado($entityInfoPersonaEmpresaRol->getEstado());
                        $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRolHist->setIpCreacion($objRequest->getClientIp());
                        $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityInfoPersonaEmpresaRol);
                        $entityPersonaEmpresaRolHist->setUsrCreacion($objRequest->getClientIp());
                        $emComercial->persist($entityInfoPersonaEmpresaRol);
                        $emComercial->flush();

                    endforeach;
                }

                //Setea el estado Eliminado a la persona
                $entityInfoPersona->setEstado('Eliminado');
                $emComercial->persist($entityInfoPersona);
                $emComercial->flush();

                //Busca otro registros por el contacto ID en estado activo
                $arrayInfoPuntoContacto = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                    ->findBy(array('contactoId' => $intIdPersona,
                        'estado' => 'Activo'));

                //Busca otro registros por el contacto ID en estado activo
                $arrayInfoPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                    ->findBy(array('contactoId' => $intIdPersona,
                        'estado' => 'Activo'));

                //Si no se encontraron registros en estado activo se procede a eliminar en la entidad InfoPersonaFormaContacto
                if (!$arrayInfoPuntoContacto && !$arrayInfoPersonaContacto)
                {
                    $entityInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->findBy(['personaId' => $intIdPersona,
                            'estado' => 'Activo']);
                    //Itera las personas formas contactos para ser eliminadas
                    foreach ($entityInfoPersonaFormaContacto as $objInfoPersonaFormaContacto):

                        $objInfoPersonaFormaContacto->setUsrUltMod($objSession->get('user'));
                        $objInfoPersonaFormaContacto->setFeUltMod(new \DateTime('now'));
                        $objInfoPersonaFormaContacto->setEstado('Eliminado');
                        $emComercial->persist($objInfoPersonaFormaContacto);
                        $emComercial->flush();

                    endforeach;
                }

                $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                    ->findPersonaEmpresaRolCarac($intIdPersona);
                if (!empty($arrayInfoEmpresaRolCarac) && $arrayInfoEmpresaRolCarac['status'] === 'ok')
                {
                    foreach ($arrayInfoEmpresaRolCarac['datos'] as $objInfoEmpresaRolCarac):
                        $objInfoEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                        $objInfoEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                        $objInfoEmpresaRolCarac->setEstado('Eliminado');
                        $emComercial->persist($objInfoEmpresaRolCarac);
                        $emComercial->flush();
                    endforeach;
                }
                $emComercial->getConnection()->commit();
            }
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError(
                'Telcos+',
                __METHOD__,
                isset($arrayRespuestaMasiva['strMensaje']) && !empty($arrayRespuestaMasiva['strMensaje'])
                    ? $arrayRespuestaMasiva['strMensaje']
                    : $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'), $objRequest->getClientIp());
        }
        $emComercial->getConnection()->close();
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Se elimino contacto!');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //eliminarContactoAjaxAction


    
    /**
     * asignaTipoContactoAjaxAction, asigna roles a una persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.0
     * 
     * @Secure(roles="ROLE_5-3")
     * 
     * 
     * @author David Leon <mdleon@telconet.ec> Se Modifica para que permita registrar en  Info_Persona_Empresa_Rol_Carac
     * cuando sea tipo contacto seguridad escalable
     * @version 1.2 16-04-2019
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.3 10-10-2019 Se implementa compatibilidad para asignación masiva de tipos de contacto a nivel de
     *                         puntos y cliente.
     */
    public function asignaTipoContactoAjaxAction()
    {
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();
        $objReturnResponse        = new ReturnResponse();
        $emComercial              = $this->getDoctrine()->getManager();
        $serviceUtil              = $this->get('schema.Util');
        $strEmpresaId             = $objSession->get('idEmpresa');
        $intIdPersona             = $objRequest->get('intIdPersona');
        $strEmpresaRol            = $objRequest->get('strEmpresaRol');
        $strEscalabilidad         = $objRequest->get('strEscalabilidad');
        $strHorarios              = $objRequest->get('strHorariosContact');
        $strTipoInsert            = $objRequest->get('strTipoInsert');
        $intIdPersonaEmpresaRol   = $objRequest->get('intIdPersonaEmpresaRol');
        $intIdPunto               = $objRequest->get('intIdPunto');
        $boolAlcanceMasivo        = filter_var($objRequest->get('strAlcanceMasivo'), FILTER_VALIDATE_BOOLEAN);
        $intAsignaNivelCliente    = 1;

        //Verifica que se envie el id persona
        if(empty($intIdPersona))
        {
            throw new \Exception('No esta enviando el id de la persona.');
        }

        //Verifica que se envie los roles
        if(empty($strEmpresaRol))
        {
            throw new \Exception('No esta enviando los tipos de contacto.');
        }

        //Crea un array con los roles enviados
        $arrayEmpresaRol = array_map('trim', explode(",", $strEmpresaRol));

        //Remueve el array con valor 0
        if('0' === $arrayEmpresaRol[0])
        {
            $arrayEmpresaRol = $objReturnResponse->removeInArray(0, $arrayEmpresaRol);
        }

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);

        $emComercial->getConnection()->beginTransaction();

        try
        {
            //Busca en la entidad oficina
            $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($objSession->get('idOficina'));

            //Si no existe la oficina termina el metodo
            if(!$entityInfoOficinaGrupo)
            {
                throw new \Exception('No existe oficina.');
            }

            //Busca en la entidad persona
            $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);

            //Si no existe la persona termina el metodo
            if(!$entityInfoPersona)
            {
                throw new \Exception('No existe persona.');
            }

            if($boolAlcanceMasivo)
            {
                $arrayRespuestaMasiva = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                    ->asignarTipoContactoMasivo(array('strEmpresaRoles'       => $strEmpresaRol,
                                                      'intAsignaNivelCliente' => $intAsignaNivelCliente,
                                                      'intIdCliente'          => $intIdPersonaEmpresaRol,
                                                      'intIdPersona'          => $entityInfoPersona->getId(),
                                                      'intIdOficina'          => $entityInfoOficinaGrupo->getId(),
                                                      'strCodEmpresa'         => $strEmpresaId,
                                                      'strUsuario'            => $objSession->get('user'),
                                                      'strIp'                 => $objRequest->getClientIp(),
                                                      'arrayExtraParams'      => array(
                                                          'strDescripcionRol1'   => 'Contacto Seguridad Escalable',
                                                          'strDescripcionCarac1' => 'NIVEL ESCALABILIDAD',
                                                          'strDescripcionCarac2' => 'HORARIO ESCALABILIDAD',
                                                          'strEscalabilidad'     => $strEscalabilidad,
                                                          'strHorario'           => $strHorarios)));

                if(!isset($arrayRespuestaMasiva['strMensaje']) || empty($arrayRespuestaMasiva['strMensaje']) ||
                    $arrayRespuestaMasiva['strMensaje'] !== 'OK')
                {
                    throw new \Exception('Al momento de asignar tipo contacto masivo.');
                }
            }
            else
            {
                //Si el tipo es por persona, busca la persona rol del cliente
                if('PERSONA_ROL' === $strTipoInsert)
                {

                    $entityInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
                    if(!$entityInfoPersonaEmpresaRol)
                    {
                        throw new \Exception('No existe empresa rol del cliente.');
                    }

                }

                //Si el tipo es por punto, busca en la entidad InfoPunto
                if('PUNTO' === $strTipoInsert)
                {

                    $entityInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                    if(!$entityInfoPunto)
                    {
                        throw new \Exception('No existe punto.');
                    }

                }

                //Itera el array de roles
                foreach ($arrayEmpresaRol as $intEmpresaRol):

                    $entityInfoEmpresaRol = '';

                    //Verifica que el elemento no este vacio
                    if ($intEmpresaRol && 0 !== $intEmpresaRol)
                    {
                        //Busca la empresa rol
                        $entityInfoEmpresaRol = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')->find($intEmpresaRol);
                    }

                    //Verifica que haya empresa rol
                    if ($entityInfoEmpresaRol)
                    {
                        //Crea un nuevo objeto
                        $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                        $entityPersonaEmpresaRol->setEmpresaRolId($entityInfoEmpresaRol);
                        $entityPersonaEmpresaRol->setPersonaId($entityInfoPersona);
                        $entityPersonaEmpresaRol->setOficinaId($entityInfoOficinaGrupo);
                        $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRol->setUsrCreacion($objSession->get('user'));
                        $entityPersonaEmpresaRol->setEstado('Activo');
                        $emComercial->persist($entityPersonaEmpresaRol);
                        $emComercial->flush();

                        //Inserta en entidad InfoPersonaContacto
                        if($entityInfoPersonaEmpresaRol)
                        {
                            $entityPersonaContacto = new InfoPersonaContacto();
                            $entityPersonaContacto->setPersonaEmpresaRolId($entityInfoPersonaEmpresaRol);
                            $entityPersonaContacto->setContactoId($entityInfoPersona);
                            $entityPersonaContacto->setFeCreacion(new \DateTime('now'));
                            $entityPersonaContacto->setUsrCreacion($objSession->get('user'));
                            $entityPersonaContacto->setIpCreacion($objRequest->getClientIp());
                            $entityPersonaContacto->setEstado('Activo');
                            $entityPersonaContacto->setPersonaRolId($entityPersonaEmpresaRol);
                            $emComercial->persist($entityPersonaContacto);
                            $emComercial->flush();
                        }

                        //Inserta en la entidad InfoPuntoContacto
                        if($entityInfoPunto)
                        {
                            $entityPuntoContacto = new InfoPuntoContacto();
                            $entityPuntoContacto->setPuntoId($entityInfoPunto);
                            $entityPuntoContacto->setContactoId($entityInfoPersona);
                            $entityPuntoContacto->setFeCreacion(new \DateTime('now'));
                            $entityPuntoContacto->setUsrCreacion($objSession->get('user'));
                            $entityPuntoContacto->setIpCreacion($objRequest->getClientIp());
                            $entityPuntoContacto->setEstado('Activo');
                            $entityPuntoContacto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                            $emComercial->persist($entityPuntoContacto);
                            $emComercial->flush();
                        }

                        //Inserta en la entidad InfoPersonaEmpresaRolHisto
                        $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaEmpresaRolHist->setEstado($entityInfoPersona->getEstado());
                        $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                        $entityPersonaEmpresaRolHist->setIpCreacion($objRequest->getClientIp());
                        $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $entityPersonaEmpresaRolHist->setUsrCreacion($objRequest->getClientIp());
                        $emComercial->persist($entityPersonaEmpresaRolHist);
                        $emComercial->flush();
                    }
                    $objResultAdmiRol = $emComercial->getRepository('schemaBundle:AdmiRol')
                        ->find($entityInfoEmpresaRol->getRolId());

                    if(is_object($objResultAdmiRol) && $objResultAdmiRol->getDescripcionRol() === 'Contacto Seguridad Escalable')
                    {
                        $objAdmiCaracteristicaNivel = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneByDescripcionCaracteristica('NIVEL ESCALABILIDAD');
                        $objAdmiCaracteristicaHorario = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneByDescripcionCaracteristica('HORARIO ESCALABILIDAD');
                            if($strEscalabilidad && $strHorarios && is_object($objAdmiCaracteristicaNivel) && is_object($objAdmiCaracteristicaHorario))
                            {
                            $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                            $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                            $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristicaNivel);
                            $entityInfoPersonaEmpresaRolCarac->setValor($strEscalabilidad);
                            $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                            $entityInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                            $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($objSession->get('user'));
                            $entityInfoPersonaEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                            $entityInfoPersonaEmpresaRolCarac->setEstado('Activo');
                            $entityInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                            $emComercial->persist($entityInfoPersonaEmpresaRolCarac);
                            $emComercial->flush();

                            $entityInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                            $entityInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                            $entityInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristicaHorario);
                            $entityInfoPersonaEmpresaRolCarac->setValor($strHorarios);
                            $entityInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                            $entityInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                            $entityInfoPersonaEmpresaRolCarac->setUsrCreacion($objSession->get('user'));
                            $entityInfoPersonaEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                            $entityInfoPersonaEmpresaRolCarac->setEstado('Activo');
                            $entityInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                            $emComercial->persist($entityInfoPersonaEmpresaRolCarac);
                            $emComercial->flush();
                        }
                        else
                        {
                            throw new \Exception('Favor Escoger Un Nivel de Escalabilidad y Horario.');
                        }
                    }
                endforeach;
            }

            $emComercial->getConnection()->commit();

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Se asigno tipo de contacto!');

            }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError(
                'Telcos+',
                __METHOD__,
                isset($arrayRespuestaMasiva['strMensaje']) && !empty($arrayRespuestaMasiva['strMensaje'])
                    ? $arrayRespuestaMasiva['strMensaje']
                    : $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'), $objRequest->getClientIp());
        }
        $emComercial->getConnection()->close();
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //asignaTipoContactoAjaxAction

    /**
     * eliminaTipoContactoAjaxAction, asigna roles a una persona
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.0
     * 
     * @Secure(roles="ROLE_5-3")
     * 
     * @author David Leon <mdleon@telconet.ec> Se Modifica para que permita borrar de la Info_Persona_Empresa_Rol_Carac cuando el tipo sea de seguridad escalable
     * @version 1.2 16-04-2019
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.3 10-10-2019 Se implementa eliminación masiva de tipos de contacto a nivel de
     *                         puntos y cliente.
     */
    public function eliminaTipoContactoAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objReturnResponse      = new ReturnResponse();
        $objResponse            = new Response();
        $objSession             = $objRequest->getSession();
        $serviceUtil            = $this->get('schema.Util');
        $emComercial            = $this->getDoctrine()->getManager();
        $objIdPersonaEmpresaRol = json_decode($objRequest->get('jsonIdPersonaEmpresaRol'));
        $strTipo                = $objRequest->get('strTipo');
        $strEmpresaId           = $objSession->get('idEmpresa');
        $boolAlcanceMasivo      = filter_var($objRequest->get('booleanAlcanceMasivo'), FILTER_VALIDATE_BOOLEAN);
        $intEliminaNivelCliente = 1;

        $objResponse->headers->set('Content-Type', 'text/json');
        //Verifica que se envie el tipo por el cual se hara la eliminacion
        if(empty($strTipo) && !$boolAlcanceMasivo)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se envia nivel de eliminacion del tipo de contacto.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        $emComercial->getConnection()->beginTransaction();

        try
        {
            if($boolAlcanceMasivo)
            {
                if(!$objIdPersonaEmpresaRol || !is_array($objIdPersonaEmpresaRol->arrayData) ||
                    count($objIdPersonaEmpresaRol->arrayData) <= 0)
                {
                    throw new \Exception('Sin información de tipos de contacto a eliminar.');
                }

                $strIdRoles = implode(",", $objIdPersonaEmpresaRol->arrayData);
                $arrayRespuestaMasiva = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                    ->eliminarTipoContactoMasivo(array('strIdRoles'              => $strIdRoles,
                                                        'intIdCliente'           => $objSession->get('cliente')['id_persona_empresa_rol'],
                                                        'intIdPersona'           => $objSession->get('cliente')['id_persona'],
                                                        'intIdOficina'           => $objSession->get('idOficina'),
                                                        'intEliminaNivelCliente' => $intEliminaNivelCliente,
                                                        'strCodEmpresa'          => $strEmpresaId,
                                                        'strUsuario'             => $objSession->get('user'),
                                                        'strIp'                  => $objRequest->getClientIp(),
                                                        'arrayExtraParams'       => array(
                                                            'strDescripcionRol1'   => 'Contacto Seguridad Escalable',
                                                            'strDescripcionCarac1' => 'NIVEL ESCALABILIDAD',
                                                            'strDescripcionCarac2' => 'HORARIO ESCALABILIDAD',
                                                            'strEscalabilidad'     => $strEscalabilidad,
                                                            'strHorario'           => $strHorarios)));

                if(!isset($arrayRespuestaMasiva['strMensaje']) || empty($arrayRespuestaMasiva['strMensaje']) ||
                    $arrayRespuestaMasiva['strMensaje'] !== 'OK')
                {
                    throw new \Exception('Al momento de eliminar tipo contacto masivo.');
                }
            }
            else
            {
                //Itera los roles del contacto a eliminar
                foreach ($objIdPersonaEmpresaRol->arrayData as $intIdPersonaEmpresaRol):

                    //Busca en la entidad InfoPersonaEmpresaRol
                    $entityInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
                    $entityInfoPersonaEmpresaRol->setEstado('Eliminado');
                    $emComercial->persist($entityInfoPersonaEmpresaRol);
                    $emComercial->flush();

                    //Si el tipo es por persona busca en la entidad InfoPersonaContacto para eliminar el registro
                    if ('PERSONA_ROL' === $strTipo)
                    {

                        $entityInfoPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                            ->findOneBy(array('personaRolId' => $intIdPersonaEmpresaRol));
                        $entityInfoPersonaContacto->setEstado('Eliminado');
                        $emComercial->persist($entityInfoPersonaContacto);
                        $emComercial->flush();

                    } //Si el tipo es por punto busca en la entidad InfoPuntoContacto para eliminar el registro
                    else if ('PUNTO' === $strTipo)
                    {

                        $entityInfoPuntoContacto = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                            ->findOneBy(array('personaEmpresaRolId' => $intIdPersonaEmpresaRol));
                        $entityInfoPuntoContacto->setEstado('Eliminado');
                        $emComercial->persist($entityInfoPuntoContacto);
                        $emComercial->flush();

                    }

                    $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                        ->findPersonaEmpresaRolCaracByPerEmp($intIdPersonaEmpresaRol);
                    if (!empty($arrayInfoEmpresaRolCarac) && $arrayInfoEmpresaRolCarac['status'] === 'ok')
                    {
                        foreach ($arrayInfoEmpresaRolCarac['datos'] as $objInfoEmpresaRolCarac):
                            $objInfoEmpresaRolCarac->setUsrUltMod($objSession->get('user'));
                            $objInfoEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                            $objInfoEmpresaRolCarac->setEstado('Eliminado');
                            $emComercial->persist($objInfoEmpresaRolCarac);
                            $emComercial->flush();
                        endforeach;
                    }
                endforeach;
            }

            $emComercial->getConnection()->commit();

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Se elimino tipo de contacto!');
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError(
                'Telcos+',
                __METHOD__,
                isset($arrayRespuestaMasiva['strMensaje']) && !empty($arrayRespuestaMasiva['strMensaje'])
                    ? $arrayRespuestaMasiva['strMensaje']
                    : $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'), $objRequest->getClientIp());
        }
        $emComercial->getConnection()->close();
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //eliminaTipoContactoAjaxAction


    
    /**
     * editarContactoAjaxAction, edita la informacion de un contacto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.0
     * 
     * @Secure(roles="ROLE_5-3")
     * 
     * Se arega el llamada metodo enviarNotificacionEdicionContacto envio de notifición por edición de contacto
     * 
     * @author Hecto Ortega <haortega@telconet.ec>
     * @version 1.3 26-01-2017
     */
    
    public function editarContactoAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        $objResponse            = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $emComercial            = $this->getDoctrine()->getManager();

        $intIdPersona               = $objRequest->get('intIdPersona');
        $intIdTitulo                = $objRequest->get('intIdTitulo');
        $strNombres                 = $objRequest->get('strNombres');
        $strApellidos               = $objRequest->get('strApellidos');
        $objPersonaFormaContacto    = json_decode($objRequest->get('jsonPersonaFormaContacto'));
        $arrayClienteSession        = $objSession->get('cliente');
        $arrayPuntoSession          = $objSession->get('ptoCliente'); 
        $strCodEmpresa              = $objSession->get('idEmpresa');

        //Termina el metodo si no se envia el id persona        
        if(empty($intIdPersona))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el id de la persona.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        $emComercial->getConnection()->beginTransaction();

        try
        {
            //Busca en la entidad persona
            $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
            //Termina el metodo si no encuentra la persona
            if(!$entityInfoPersona)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No exista la persona!');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }
            //Busca en la entidad titulo cuando el id es enviado
            if(!empty($intIdTitulo))
            {
                $entityAdmiTitulo = $emComercial->getRepository('schemaBundle:AdmiTitulo')->find($intIdTitulo);
            }

            $arrayDatosNotificacion['strNombresActuales']      = $entityInfoPersona->getNombres();
            $arrayDatosNotificacion['strApellidosActuales']    = $entityInfoPersona->getApellidos();
            $arrayDatosNotificacion['strTituloActual']         = $entityInfoPersona->getTituloId();
            $arrayDatosNotificacion['strIdentificacionActual'] = $entityInfoPersona->getIdentificacionCliente();

            $entityInfoPersona->setNombres($strNombres);
            $entityInfoPersona->setApellidos($strApellidos);

            $arrayDatosNotificacion['strTituloContacto']="";
            if(is_object($entityAdmiTitulo))
            {
                $entityInfoPersona->setTituloId($entityAdmiTitulo);
                $arrayDatosNotificacion['strTituloContacto']=$entityAdmiTitulo->getDescripcionTitulo();
            }

            $emComercial->persist($entityInfoPersona);
            $emComercial->flush();

            $arrayInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                ->findBy(array('personaId' => $intIdPersona,
                                                                        'estado'    => 'Activo'));
            $strFormasContactoActuales="";
            foreach($arrayInfoPersonaFormaContacto as $objInfoPersonaFormaContacto):

                $strFormasContactoActuales .= "<tr> " .
                    "<td>" . $objInfoPersonaFormaContacto->getFormaContactoId() . "</td>" .
                    "<td>" . $objInfoPersonaFormaContacto->getValor() . "</td>" .
                                              "<td>" . $objInfoPersonaFormaContacto->getEstado() . "</td>"  . "</tr>";
            endforeach;
            $arrayDatosNotificacion['strFormasContactoActuales'] = $strFormasContactoActuales;

            //Itera la informacion de formas contactos a editar
            $strFormasContactoEditadas="";
            foreach($objPersonaFormaContacto->arrayEdit as $objPersonaFormaContactoEdit):

                $entityInfoPersonaFormaContactoEdit = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                    ->find($objPersonaFormaContactoEdit->intIdPersonaFormaContacto);
                $entityInfoPersonaFormaContactoEdit->setValor($objPersonaFormaContactoEdit->strValor);
                $entityInfoPersonaFormaContactoEdit->setFeUltMod(new \DateTime('now'));
                $entityInfoPersonaFormaContactoEdit->setUsrUltMod($objSession->get('user'));
                $emComercial->persist($entityInfoPersonaFormaContactoEdit);
                $emComercial->flush();

                $strFormasContactoEditadas .= "<tr>" .
                                              "<td>" .$entityInfoPersonaFormaContactoEdit->getFormaContactoId() . "</td>" .
                                              "<td>" .$entityInfoPersonaFormaContactoEdit->getValor() . "</td>" .
                                              "<td>" ."Editada" . "</td>" ."</tr>";

            endforeach;

            //Itera la informacion de formas contactos a eliminar
            $strFormasContactoEliminadas="";
            foreach($objPersonaFormaContacto->arrayDelete as $intIdPersonaFormaContacto):

                $entityInfoPersonaFormaContactoDelete = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                    ->find($intIdPersonaFormaContacto);
                $entityInfoPersonaFormaContactoDelete->setEstado('Eliminado');
                $entityInfoPersonaFormaContactoDelete->setFeUltMod(new \DateTime('now'));
                $entityInfoPersonaFormaContactoDelete->setUsrUltMod($objSession->get('user'));
                $emComercial->persist($entityInfoPersonaFormaContactoDelete);
                $emComercial->flush();

                $strFormasContactoEliminadas .= "<tr>" .
                                                "<td>" .$entityInfoPersonaFormaContactoDelete->getFormaContactoId() . "</td>" .
                                                "<td>" .$entityInfoPersonaFormaContactoDelete->getValor() . "</td>" .
                                                "<td>" .$entityInfoPersonaFormaContactoDelete->getEstado() . "</td>" ."</tr>";
            endforeach;

            //Itera la informacion de formas contacto a insertar
            $strFormasContactoNuevas="";
            foreach($objPersonaFormaContacto->arrayNew as $objPersonaFormaContactoNew):

                $entityAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                    ->findOneBy(array('descripcionFormaContacto' =>
                    $objPersonaFormaContactoNew->strDescripcionFormaContacto));
                $entityInfoPersonaFormaContactoNew = new InfoPersonaFormaContacto();
                $entityInfoPersonaFormaContactoNew->setValor($objPersonaFormaContactoNew->strValor);
                $entityInfoPersonaFormaContactoNew->setEstado('Activo');
                $entityInfoPersonaFormaContactoNew->setFeCreacion(new \DateTime('now'));
                $entityInfoPersonaFormaContactoNew->setFormaContactoId($entityAdmiFormaContacto);
                $entityInfoPersonaFormaContactoNew->setIpCreacion($objRequest->getClientIp());
                $entityInfoPersonaFormaContactoNew->setPersonaId($entityInfoPersona);
                $entityInfoPersonaFormaContactoNew->setUsrCreacion($objSession->get('user'));

                $emComercial->persist($entityInfoPersonaFormaContactoNew);
                $emComercial->flush();

                $strFormasContactoNuevas .=   "<tr>" .
                                              "<td>" .$entityInfoPersonaFormaContactoNew->getFormaContactoId() . "</td>" .
                                              "<td>" .$entityInfoPersonaFormaContactoNew->getValor() . "</td>" .
                                              "<td>" .$entityInfoPersonaFormaContactoNew->getEstado() . "</td>" ."</tr>";

            endforeach;

            $emComercial->getConnection()->commit();

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Se edito el contacto!');

            $arrayParametro['arrayEmpresaGrupo']        = ['arrayEmpresaGrupo'      => [$objSession->get('prefijoEmpresa')]];
            $arrayParametro['arrayPersonaContacto']     = ['arrayPersona'           => [$intIdPersona],
                                                           'arrayEstado'            => [$objRequest->get('strEstado')],
                'arrayPersonaEmpresaRol' => [$objRequest->get('intIdPersonaEmpresaRol')],
                                                           'arrayPunto'             => [$objRequest->get('intIdPunto')]];
            $arrayParametro['intLimit']                 = $objRequest->get('limit');
            $arrayParametro['intStart']                 = $objRequest->get('start');
            $arrayParametro['strTipoConsulta']          = $objRequest->get('strTipoConsulta');
            $objResultPersonaContacto                   = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                ->getRolesPersonaPunto($arrayParametro);
            $strTiposContacto                           = "";
            $intContadorTiposContacto                   = 0;

            foreach($objResultPersonaContacto->getRegistros() as $arrayInfoPersonaEmpresaRol):

                if ($intContadorTiposContacto==0)
                {
                    $strTiposContacto .= $arrayInfoPersonaEmpresaRol['strDescripcionRol'];
                }
                else
                {
                    $strTiposContacto .= ", " . $arrayInfoPersonaEmpresaRol['strDescripcionRol'];
                }
                $intContadorTiposContacto++;
            endforeach;

            $arrayDatosNotificacion['strTipoContactoActual']          = $strTiposContacto;
            $arrayParametrosNotificacion['strCodigoEmpresa']          = $strCodEmpresa;
            $objEnvioPlantilla                                        = $this->get('soporte.EnvioPlantilla');
            $arrayParametrosNotificacion['objEnvioPlantilla']         = $objEnvioPlantilla;
            $arrayDatosNotificacion['strRazonSocialCliente']          = $arrayClienteSession['razon_social'];
            $arrayDatosNotificacion['strIdentificacionCliente']       = $arrayClienteSession['identificacion'];
            $arrayDatosNotificacion['strLogin']                       = $arrayPuntoSession['login'];
            $arrayDatosNotificacion['strNombresContacto']             = $strNombres;
            $arrayDatosNotificacion['strApellidosContacto']           = $strApellidos;
            $arrayDatosNotificacion['strIdentificacionContacto']      = $entityInfoPersona->getIdentificacionCliente();
            $strFechaModificacionContacto                             = date_format(new \DateTime('now'), "d-m-Y H:i");
            $arrayDatosNotificacion['strFechaModificacionContacto']   = $strFechaModificacionContacto;
            $arrayDatosNotificacion['strUsuarioModificacionContacto'] = $objSession->get('user');
            $arrayDatosNotificacion['strFormasContactoEditadas']      = $strFormasContactoEditadas;
            $arrayDatosNotificacion['strFormasContactoEliminadas']    = $strFormasContactoEliminadas;
            $arrayDatosNotificacion['strFormasContactoNuevas']        = $strFormasContactoNuevas;
            $arrayParametrosNotificacion['arrayDatosEnvioPlantilla']  = $arrayDatosNotificacion;
            $arrayParametrosNotificacion['strIpCliente']              = $objRequest->getClientIp();;

            $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            $serviceInfoPersonaFormaContacto->enviarNotificacionEdicionContacto($arrayParametrosNotificacion);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
        }
        $emComercial->getConnection()->close();
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }//editarContactoAjax

    /**
     * createAction, metodo que crea contacto al cliente.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-05-2016 Se valida que cuando no encuentre la entidad infopersonaempresarol realice un throw exception
     * 
     * @Secure(roles="ROLE_5-3")
     */
    public function createAction(Request $objRequest)
    {
        $entityInfoPersona          = new InfoPersona();
        $arrayDatosForm             = $objRequest->request->get('contactotype');
        $objSession                 = $objRequest->getSession();
        $arrayDatosFormasContacto   = explode(",", $arrayDatosForm['formas_contacto']);
        $arrayClienteSession        = $objSession->get('cliente');
        $a                          = 0;
        $x                          = 0;
        for($i = 0; $i < count($arrayDatosFormasContacto); $i++)
        {
            if($a == 3)
            {
                $a = 0;
                $x++;
            }
            if($a == 1)
            {
                $arrayFormasContacto[$x]['formaContacto'] = $arrayDatosFormasContacto[$i];
            }
            if($a == 2)
            {
                $arrayFormasContacto[$x]['valor'] = $arrayDatosFormasContacto[$i];
            }
            $a++;
        }

        $intIdEmpresa       = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina       = $objRequest->getSession()->get('idOficina');
        $strUsrCreacion     = $objRequest->getSession()->get('user');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emComercial->getConnection()->beginTransaction();

        $objClienteSesion       = $objSession->get('cliente');
        $intClienteId           = $objClienteSesion['id'];
        $boolGuardaContacto     = true;
        $boolActualizaContacto  = false;
        //Obtengo los contactos existentes  del cliente
        $arrayContactos = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
            ->getResultadoContactosPorCliente($intIdEmpresa, $intClienteId);

        try
        {
            //Valida que el rol de la persona sea ['Cliente', 'Pre-cliente']
            if(!in_array($arrayClienteSession['nombre_tipo_rol'], ['Cliente', 'Pre-cliente']))
            {
                throw new \Exception('No se creara el contacto, la persona en sesion debe tener rol Cliente o Pre-cliente.');
            }
            if($arrayContactos)
            {
                foreach($arrayContactos as $objPersonaContacto)
                {
                    if($objPersonaContacto->getEstado() == 'Activo')
                    {
                        if(strcmp(trim($objPersonaContacto->getNombres()), trim($arrayDatosForm['nombres'])) == 0 &&
                            strcmp(trim($objPersonaContacto->getApellidos()), trim($arrayDatosForm['apellidos'])) == 0
                        )
                        {
                            $boolGuardaContacto = false;
                        }
                    }
                    else if($objPersonaContacto->getEstado() == 'Inactivo')
                    {
                        if(strcmp(trim($objPersonaContacto->getNombres()), trim($arrayDatosForm['nombres'])) == 0 &&
                            strcmp(trim($objPersonaContacto->getApellidos()), trim($arrayDatosForm['apellidos'])) == 0
                        )
                        {
                            $boolGuardaContacto = false;
                            $boolActualizaContacto = true;
                            $objPersonaContacto->setEstado('Activo');
                            $emComercial->persist($objPersonaContacto);
                            $emComercial->flush();
                        }
                        $arrayPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->getPersonaEmpresaRolPorPersonaPorEmpresa($objPersonaContacto->getId(), $intIdEmpresa);
                        foreach($arrayPersonaEmpresaRol as $objPersonaEmpresaRol)
                        {
                            $objPersonaEmpresaRol->setEstado('Activo');
                            $emComercial->persist($objPersonaEmpresaRol);
                            $emComercial->flush();
                        }
                    }
                }
            }
            if($boolGuardaContacto)
            {
                $entityOficina                  = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
                $entityAdmiTitulo               = $emComercial->getRepository('schemaBundle:AdmiTitulo')->find($arrayDatosForm['tituloId']);
                $entityEmpresaRol               = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                    ->findPoridRolPorEmpresa($arrayDatosForm['idrol'], $intIdEmpresa);
                $entityPersonaEmpresaRolCliente = 
                    $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($arrayDatosForm['idcliente'], 
                                                                                            $arrayClienteSession['nombre_tipo_rol'], 
                                                                                            $intIdEmpresa);
                if(!$entityPersonaEmpresaRolCliente)
                {
                    throw new \Exception('No se crea el contacto, la persona en sesion debe tener rol Cliente o Pre-cliente.');
                }
                $entityInfoPersona->setNombres($arrayDatosForm['nombres']);
                $entityInfoPersona->setApellidos($arrayDatosForm['apellidos']);
                if($entityAdmiTitulo)
                {
                    $entityInfoPersona->setTituloId($entityAdmiTitulo);
                }
                $entityInfoPersona->setOrigenProspecto('N');
                $entityInfoPersona->setFeCreacion(new \DateTime('now'));
                $entityInfoPersona->setUsrCreacion($strUsrCreacion);
                $entityInfoPersona->setIpCreacion($objRequest->getClientIp());
                $entityInfoPersona->setEstado('Activo');
                $emComercial->persist($entityInfoPersona);
                $emComercial->flush();

                //ASIGNA ROL DE CONTACTO A LA PERSONA
                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();

                $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
                $entityPersonaEmpresaRol->setPersonaId($entityInfoPersona);
                $entityPersonaEmpresaRol->setOficinaId($entityOficina);
                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRol->setUsrCreacion($strUsrCreacion);
                $entityPersonaEmpresaRol->setEstado('Activo');
                $emComercial->persist($entityPersonaEmpresaRol);
                $emComercial->flush();

                //GRABA RELACION ENTRE CONTACTO Y CLIENTE            
                if($arrayDatosForm['idcliente'])
                {
                    $entityPersonaContacto = new InfoPersonaContacto();
                    $entityPersonaContacto->setPersonaEmpresaRolId($entityPersonaEmpresaRolCliente);
                    $entityPersonaContacto->setContactoId($entityInfoPersona);
                    $entityPersonaContacto->setFeCreacion(new \DateTime('now'));
                    $entityPersonaContacto->setUsrCreacion($strUsrCreacion);
                    $entityPersonaContacto->setIpCreacion($objRequest->getClientIp());
                    $entityPersonaContacto->setEstado('Activo');
                    $emComercial->persist($entityPersonaContacto);
                    $emComercial->flush();
                }
                //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
                $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
                $entity_persona_historial->setEstado($entityInfoPersona->getEstado());
                $entity_persona_historial->setFeCreacion(new \DateTime('now'));
                $entity_persona_historial->setIpCreacion($objRequest->getClientIp());
                $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $entity_persona_historial->setUsrCreacion($strUsrCreacion);
                $emComercial->persist($entity_persona_historial);
                $emComercial->flush();

                //REGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
                for($i = 0; $i < count($arrayFormasContacto); $i++)
                {
                    $entityAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                        ->findPorDescripcionFormaContacto($arrayFormasContacto[$i]["formaContacto"]);
                    $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                    $entity_persona_forma_contacto->setValor($arrayFormasContacto[$i]["valor"]);
                    $entity_persona_forma_contacto->setEstado("Activo");
                    $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                    $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entity_persona_forma_contacto->setIpCreacion($objRequest->getClientIp());
                    $entity_persona_forma_contacto->setPersonaId($entityInfoPersona);
                    $entity_persona_forma_contacto->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($entity_persona_forma_contacto);
                    $emComercial->flush();
                }
                $emComercial->getConnection()->commit();
                return $this->redirect($this->generateUrl('contacto_show', array('id' => $entityInfoPersona->getId())));
            }
            else if($boolGuardaContacto == false && $boolActualizaContacto)
            {
                $emComercial->getConnection()->commit();
                return $this->redirect($this->generateUrl('contacto'));
            }
            else
            {
                $emComercial->getConnection()->commit();
                return $this->redirect($this->generateUrl('contacto_new'));
            }
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('contacto_new'));
        }
    } //createAction

    /**
     * @Secure(roles="ROLE_5-4")
     */
    public function editAction($id) {
		$request  = $this->get('request');
		$session  = $request->getSession();
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("5", "1");
        $session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $em = $this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $editForm = $this->createForm(new ContactoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $entityAdmiRol = $em->getRepository('schemaBundle:AdmiRol')->getRolesByDescripcionTipoRol('Contacto');
        $personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Contacto',$idEmpresa);         
        return $this->render('comercialBundle:contacto:edit.html.twig', array(
                'item' => $entityItemMenu,
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'admiRol' => $entityAdmiRol,
                'personaEmpresaRol' => $personaEmpresaRol
        ));
    }

    /**
     * @Secure(roles="ROLE_5-5")
     */
    public function updateAction(Request $request,$id)
    {
        $datos_form = $request->request->get('contactotype');
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $a = 0;$x = 0;
        for ($i = 0; $i < count($array_formas_contacto); $i++) {
            if ($a == 3) {$a = 0;$x++;}
            if ($a == 1)$formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if ($a == 2)$formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }
        $em = $this->getDoctrine()->getManager('telconet');
        $entity=$em->getRepository('schemaBundle:InfoPersona')->find($id);
        $idEmpresa = $request->getSession()->get('idEmpresa'); 
        $usrUltMod = $request->getSession()->get('user');
        $estadoI='Inactivo';
        $em->getConnection()->beginTransaction();
        try {
            $entity->setNombres($datos_form['nombres']);
            $entity->setApellidos($datos_form['apellidos']);
            $entityAdmiTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
            if ($entityAdmiTitulo)
                $entity->setTituloId($entityAdmiTitulo);
            $em->persist($entity);
            $em->flush();
            //ACTUALIZA ROL DE CONTACTO A LA PERSONA
            $entityPersonaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Contacto',$idEmpresa);
            $entityEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPoridRolPorEmpresa($datos_form['idrol'], $idEmpresa);
            $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
            $em->persist($entityPersonaEmpresaRol);
            $em->flush();

            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrUltMod);
            $em->persist($entity_persona_historial);
            $em->flush();

            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            /* @var $serviceInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
            $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            $serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entity->getId(), $usrUltMod);

            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for ($i=0;$i < count($formas_contacto);$i++){
                $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                $entity_persona_forma_contacto->setEstado("Activo");
                $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                $entity_persona_forma_contacto->setIpCreacion($request->getClientIp());
                $entity_persona_forma_contacto->setPersonaId($entity);
                $entity_persona_forma_contacto->setUsrCreacion($usrUltMod);
                $em->persist($entity_persona_forma_contacto);
                $em->flush();
            }

            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('contacto_show', array('id' => $entity->getId())));
        } catch (\Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            $em->getConnection()->close();

            $editForm = $this->createForm(new ContactoType(), $entity);
            $deleteForm = $this->createDeleteForm($id);
            $entityAdmiRol = $em->getRepository('schemaBundle:AdmiRol')->getRolesByDescripcionTipoRol('Contacto');
            $personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Contacto',$idEmpresa);         
            return $this->render('comercialBundle:contacto:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'admiRol' => $entityAdmiRol,
                    'personaEmpresaRol' => $personaEmpresaRol,
                    'error' => $e->getMessage()
            ));
        }
    }

    /**
     * @Secure(roles="ROLE_5-8")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoPersona entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infopersona'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                ->add('id', 'hidden')
                ->getForm()
        ;
    }

    /**
     * delete_ajaxAction, elimina contacto, persona empresa rol, persona forma contacto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 17-04-2016
     * @since 1.0
     * 
     * @Secure(roles="ROLE_5-9")
     */
    public function delete_ajaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        $emComercial            = $this->getDoctrine()->getManager();
        $intIdPersona           = $objRequest->get('intIdPersona');
        $intIdPersonaEmpresaRol = $objRequest->get('intIdPersonaEmpresaRol');

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);

        $emComercial->getConnection()->beginTransaction();
        try
        {
            if(empty($intIdPersona))
            {
                throw new \Exception('No se esta enviando id contacto.');
            }
            if(empty($intIdPersonaEmpresaRol))
            {
                throw new \Exception('No se esta enviando persona empresa rol.');
            }
            $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
            if(!$entityInfoPersona)
            {
                throw new \Exception('No se encontro a la persona.');
            }
            $entityInfoPersona->setEstado('Inactivo');
            $emComercial->persist($entityInfoPersona);
            $emComercial->flush();

            $entityInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
            if($entityInfoPersonaEmpresaRol)
            {
                $entityInfoPersonaEmpresaRol->setEstado('Inactivo');
                $emComercial->persist($entityInfoPersonaEmpresaRol);
                $emComercial->flush();
            }

            $entityInfoPersonaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                     ->findBy(array('contactoId'          => $intIdPersona, 
                'personaEmpresaRolId' => $intIdPersonaEmpresaRol));
            foreach($entityInfoPersonaContacto as $objInfoPersonaContacto):
                $objInfoPersonaContacto->setEstado('Inactivo');
                $emComercial->persist($objInfoPersonaContacto);
                $emComercial->flush();
            endforeach;

            $entityInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                ->findBy(array('personaId' => $intIdPersona));

            foreach($entityInfoPersonaFormaContacto as $objInfoPersonaFormaContacto):

                $objInfoPersonaFormaContacto->setEstado('Inactivo');
                $objInfoPersonaFormaContacto->setFeUltMod(new \DateTime('now'));
                $objInfoPersonaFormaContacto->setUsrUltMod($objSession->get('user'));
                $emComercial->persist($objInfoPersonaFormaContacto);
                $emComercial->flush();

            endforeach;

            $emComercial->getConnection()->commit();

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS . ' Contacto inactivo!');
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
        }
        $emComercial->getConnection()->close();
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    public function ajaxGetFormasContactoAction()
    {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet');
        $tipo = $em->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
		if(!$tipo){
			$tipos[] = array("id"=>"","descripcion"=>"");
		}else{
            $tipos = array();
			$tipos[] = array("id"=>"","descripcion"=>"");
			foreach($tipo as $emp){
                $tecn['id'] = $emp->getId();
                $tecn['descripcion'] = $emp->getDescripcionFormaContacto();
                $tipos[] = $tecn;
            }
        }
        $response = new Response(json_encode($tipos));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function ajaxGetTelefonosAction()
    {
        $request = $this->getRequest();
		$session  = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $idCliente = $request->request->get("idCliente");
        $idContactoCliente = $request->request->get("idContactoCliente");
        $telefono = $em->getRepository('schemaBundle:ClieTelefonoContacto')->findByIdContacto($idContactoCliente);
        //print_r($telefono);die();
        //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
		if(!$telefono){
			$telefonos[] = array("idTelefono"=>"","numero"=>"","tipoTelefono"=>"");
		}else{
            $telefonos = array();
			foreach($telefono as $emp){
                $tecn['idTelefono'] = $emp->getId();
                $tecn['numero'] = $emp->getNumeroTelefonoContacto();
                $tecn['tipoTelefono'] = $emp->getIdTipoTelefono()->getDescripcionTipoTelefono();
                $telefonos[] = $tecn;
            }
        }
        $response = new Response(json_encode($telefonos));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * @Secure(roles="ROLE_5-2")
     * 
     * Documentación para el método 'ingresarMasivosAction'.
     *
     * Metodo usado para mostrar el formulario de ingreso masivo de contactos.
     *
     * @return Response 
     *
     * @author Edgar Holguin <efranco@telconet.ec>
     *
     * @version 1.0 19-12-2015
     */
    public function ingresarMasivosAction()
    {
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();        
        $arrayClienteSesion   = $objSession->get('cliente');
        $objPtoCliente        = $objSession->get('ptoCliente');
        $strNombreCliente     = "";
        $intIdCliente         = 0;
        $strTipoRol           = "";
        $objAdmiRol           = null;


        if($arrayClienteSesion)
        {
            $intIdCliente=$arrayClienteSesion['id'];
            if($arrayClienteSesion['razon_social'])
            {
                $strNombreCliente = $arrayClienteSesion['razon_social'];
            }
            else
            {
                $strNombreCliente = $arrayClienteSesion['nombres'].' '.$arrayClienteSesion['apellidos'];
            }
            $strTipoRol=$arrayClienteSesion['nombre_tipo_rol'];	
        }



        $emComercial = $this->get('doctrine')->getManager('telconet');
        $objAdmiRol  = $emComercial->getRepository('schemaBundle:AdmiRol')->getRolesByDescripcionTipoRol('Contacto');

        return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig',
                              array('puntoCliente'        => $objPtoCliente,
                                    'admiRol'             => $objAdmiRol,
                                    'idClienteSesion'     => $intIdCliente,
                'nombreClienteSesion' => $strNombreCliente,
                                    'tipoRolClienteSesion'=> $strTipoRol
                )
        );
    }

    
    /**
     * fileUploadAction
     *
     * Metodo encargado de procesar el archivo de contactos y los
     * coloca en el directorio de destino para luego tomar su infirmacion basica y guardarlos en la base
     *          
     * @return Response 
     *
     * @author  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 19-12-2015
     * 
     *
     * Se corrige metodo para metodo para guardar masivamente
     * @author  Joel Broncano <jbroncano@telconet.ec>
     * @version 1.1 19-04-2023
     */
    public function fileUploadAction()
    {
        $objRequest            = $this->get('request');
        $strServerRoot         = $this->container->getParameter('path_telcos');                  
        $strNombreArchivo      = $_FILES['file_contactos']['name'];
        $strTipoArchivo        = strtolower( $_FILES['file_contactos']['type']);
        $strTamanioArchivo     = $_FILES['file_contactos']['size'];       
        $strUrlDestino         = $strServerRoot.'telcos/web/public/uploads/documentos/';  
        $strUrlFileContactos   = $strUrlDestino.$strNombreArchivo;  
        $strTipoIngreso        = $objRequest->get('tipo_ingreso');
        $intIdPunto            = $objRequest->get('id_punto');
        $objSession            = $objRequest->getSession();
        $objFile               = $objRequest->files;		
        $objClienteSesion      = $objSession->get('cliente');
        $strUsrSesion          = $objSession->get('user');
        $ptoCliente            = $objSession->get('ptoCliente');
        $intIdEmpresa          = $objSession->get('idEmpresa');
        $intIdOficina          = $objSession->get('idOficina');
        $objArchivoContactos   = $objFile   ->get('file_contactos'); 	  
        $emComercial           = $this->getDoctrine()->getManager('telconet');

        $expRegNumeros1        = "/^09.*/";      
        $expRegNumeros2        = "/^593.*/";
        $expRegNumeros3        = "/^\+593.*/";                       
        $strPatronLetras       = "/^[A-z ]+$/";
        $strPatronNumeros      = "/^[0-9]+$/";
        $strPatronEmail        = "#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#";

        $intNumContactValidos  = 0;

        if($strTamanioArchivo>2000000)
        {
            return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig',
                                  array('puntoCliente'        => $ptoCliente,
                                        'error'               => 'Error al cargar el Archivo, size maximo permitido 2MB',
                                        'idClienteSesion'     => $objClienteSesion
                    )
            );
        }

        try
        {
            if($objClienteSesion)
            {
                $intClienteId = $objClienteSesion['id'];

                if($objArchivoContactos)
                {
                    if($objArchivoContactos->move($strUrlDestino, $strNombreArchivo))
                    {

                        // Lectura del archivo de contactos

                        $objFileReadContactos     = fopen($strUrlFileContactos, "r");
                        $strFileContactosError    = "reporte_contactos_no_ingrasados_" . date('Ymd') . "_" . date('His') . ".txt";
                        $strUrlFileContactosError = $strUrlDestino . '/' . $strFileContactosError;
                        $objFileContactosError    = fopen($strUrlFileContactosError, "w");
                        $boolEnviaNotificacion    = false;
                        $intContador              = 0;
                        while(( $arrayDatos = fgetcsv($objFileReadContactos, 10000, ";", "\\")) !== FALSE)
                        {
                            $intNumColumnas             = count($arrayDatos);
                            $boolGuardaContacto         = true;
                            $boolGuardaTipoContacto     = true;
                            $boolTieneTelefonoFijo      = true;
                            $boolTieneTelefonoMovil1    = true;
                            $boolTieneTelefonoMovil2    = true;
                            $boolTieneCorreoElectronico = true;

                            if($arrayDatos)
                            {

                                if($intContador > 0 && $intNumColumnas >= 10)
                                {
                                    // INFORMACION DE NUEVO CONTACTO
                                    $strNombres         = trim($arrayDatos[0]);
                                    $strApellidos       = trim($arrayDatos[1]);
                                    $strTitulo          = trim($arrayDatos[2]);
                                    $strTipoContacto    = trim($arrayDatos[3]);
                                    $strFonoFijo        = trim($arrayDatos[4]);
                                    $strFonoMovil1      = trim($arrayDatos[5]);
                                    $strOperadoraMovil1 = trim($arrayDatos[6]);
                                    $strFonoMovil2      = trim($arrayDatos[7]);
                                    $strOperadoraMovil2 = trim($arrayDatos[8]);
                                    $strEmailContacto   = trim($arrayDatos[9]);


                                    //Obtengo los contactos existentes  del cliente
                                    $arrayContactos = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                        ->getResultadoContactosPorCliente($intIdEmpresa, $intClienteId);

                                    if($arrayContactos)
                                    {
                                        foreach($arrayContactos as $objPersonaContacto)
                                        {
                                            if($objPersonaContacto->getEstado()=='Activo')
                                            {


                                                if(strcmp(trim($objPersonaContacto->getNombres()),trim($strNombres))==0 &&
                                                   strcmp(trim($objPersonaContacto->getApellidos()),trim($strApellidos))==0  
                                                )
                                                {
                                                    $boolGuardaContacto    = false;
                                                    $boolEnviaNotificacion = true;
                                                    fwrite($objFileContactosError, 
                                                           "\nNo se ingresado el siguiente contacto, Contacto ya existente: \n");
                                                }

                                            }
                                            else if($objPersonaContacto->getEstado()=='Inactivo')
                                            {


                                                if(strcmp(trim($objPersonaContacto->getNombres()),trim($strNombres))==0 &&
                                                   strcmp(trim($objPersonaContacto->getApellidos()),trim($strApellidos))==0  
                                                )
                                                {
                                                    $boolGuardaContacto    = false;
                                                    $boolEnviaNotificacion = true;
                                                    fwrite($objFileContactosError, 
                                                           "\nNo se ingresado el siguiente contacto, Contacto ya existente, "
                                                        . "Se actualiza estado a Activo: \n");
                                                    $objPersonaContacto->setEstado('Activo');
                                                    $emComercial       ->persist($objPersonaContacto);
                                                    $emComercial       ->flush();                                                   
    
                                                }


                                                $arrayPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                          ->getPersonaEmpresaRolPorPersonaPorEmpresa($objPersonaContacto->getId(), 
                                                                                                                     $intIdEmpresa
                                                );
                                                foreach($arrayPersonaEmpresaRol as $objPersonaEmpresaRol)
                                                {
                                                    $objPersonaEmpresaRol->setEstado('Activo');
                                                    $emComercial->persist($objPersonaEmpresaRol);
                                                    $emComercial->flush();
                                                }
                                            }
                                        }
                                    }


                                    $objAdmiTitulo = $emComercial->getRepository('schemaBundle:AdmiTitulo')
                                        ->findOneBy(array('descripcionTitulo' => $strTitulo, 'estado' => 'Activo'));

                                    $objInfoPersona = new InfoPersona();

                                    if($objAdmiTitulo && $strNombres != "" && $strApellidos != "" && preg_match($strPatronLetras, $strNombres) &&
                                        preg_match($strPatronLetras, $strApellidos))
                                    {
                                        $objInfoPersona->setNombres($strNombres);
                                        $objInfoPersona->setApellidos($strApellidos);
                                        $objInfoPersona->setTituloId($objAdmiTitulo);
                                        $objInfoPersona->setOrigenProspecto('N');
                                        $objInfoPersona->setFeCreacion(new \DateTime('now'));
                                        $objInfoPersona->setUsrCreacion($strUsrSesion);
                                        $objInfoPersona->setIpCreacion($objRequest->getClientIp());
                                        $objInfoPersona->setEstado('Activo');
                                    }
                                    else
                                    {
                                        $boolGuardaContacto    = false;
                                        $boolEnviaNotificacion = true;
                                        fwrite($objFileContactosError, 
                                               "\nNo se ingresado el siguiente contacto, verificar los campos nombres , apellidos, titulo: \n");
                                    }


                                    $objPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                                    $objAdmiRol           = $emComercial->getRepository('schemaBundle:AdmiRol')
                                        ->findOneBy(array('descripcionRol' => $strTipoContacto,
                                                                                          'estado'         => 'Activo'
                                        )
                                    );
                                    if($objAdmiRol)
                                    {
                                        $intRolId      = $objAdmiRol ->getId();
                                        $objEmpresaRol = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                            ->findPoridRolPorEmpresa($intRolId, $intIdEmpresa);

                                        $objOficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
                                        $objPersonaEmpresaRol->setEmpresaRolId($objEmpresaRol);
                                        $objPersonaEmpresaRol->setPersonaId($objInfoPersona);
                                        $objPersonaEmpresaRol->setOficinaId($objOficina);
                                        $objPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                                        $objPersonaEmpresaRol->setUsrCreacion($strUsrSesion);
                                        $objPersonaEmpresaRol->setEstado('Activo');

                                        $objPersonaContacto = new InfoPersonaContacto();
                                        $entityPersonaEmpresaRolCliente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->
                                            getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($intClienteId,
                                                                                                      $objClienteSesion['nombre_tipo_rol'],
                                                                                                      $intIdEmpresa
                                        );
                                        $objPersonaContacto->setPersonaEmpresaRolId($entityPersonaEmpresaRolCliente);
                                        $objPersonaContacto->setContactoId($objInfoPersona);
                                        $objPersonaContacto->setFeCreacion(new \DateTime('now'));
                                        $objPersonaContacto->setUsrCreacion($strUsrSesion);
                                        $objPersonaContacto->setIpCreacion($objRequest->getClientIp());
                                        $objPersonaContacto->setEstado('Activo');

                                        $objPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                                        $objPersonaHistorial->setEstado($objInfoPersona->getEstado());
                                        $objPersonaHistorial->setFeCreacion(new \DateTime('now'));
                                        $objPersonaHistorial->setIpCreacion($objRequest->getClientIp());
                                        $objPersonaHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                                        $objPersonaHistorial->setUsrCreacion($strUsrSesion);
                                    }
                                    else
                                    {
                                        $boolGuardaTipoContacto = false;
                                        $boolEnviaNotificacion  = true;
                                        fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, "
                                            . "                         verificar el campo tipo contacto: \n");
                                    }

                                    //FORMAS DE CONTACTO DEL CONTACTO DEL CLIENTE
                                    if($strFonoFijo != "" && preg_match($strPatronNumeros, $strFonoFijo))
                                    {
                                        $objPersonaFormaContacto1 = new InfoPersonaFormaContacto();
                                        $objPersonaFormaContacto1->setValor($strFonoFijo);
                                        $objPersonaFormaContacto1->setEstado("Activo");
                                        $objPersonaFormaContacto1->setFeCreacion(new \DateTime('now'));
                                        $objAdmiFormaContacto1 = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findOneBy(array('descripcionFormaContacto' => 'Telefono Fijo',
                                            'estado' => 'Activo'
                                            )
                                        );
                                        $objPersonaFormaContacto1->setFormaContactoId($objAdmiFormaContacto1);
                                        $objPersonaFormaContacto1->setIpCreacion($objRequest->getClientIp());
                                        $objPersonaFormaContacto1->setPersonaId($objInfoPersona);
                                        $objPersonaFormaContacto1->setUsrCreacion($strUsrSesion);
                                    }
                                    else
                                    {
                                        $boolTieneTelefonoFijo = false;
                                        $boolEnviaNotificacion = true;
                                        fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, verificar campo telefono fijo: \n");
                                    }

                                    if($strFonoMovil1 && $strOperadoraMovil1 != "" && preg_match($strPatronNumeros, $strFonoMovil1))
                                    {
                                        $objAdmiFormaContacto2 = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findOneBy(array('descripcionFormaContacto' => $strOperadoraMovil1,
                                            'estado' => 'Activo'
                                            )
                                        );
                                        if($objAdmiFormaContacto2 && (preg_match($expRegNumeros1, $strFonoMovil1) ||
                                            preg_match($expRegNumeros2, $strFonoMovil1) ||
                                            preg_match($expRegNumeros3, $strFonoMovil1)
                                            )
                                        )
                                        {
                                            $objPersonaFormaContacto2 = new InfoPersonaFormaContacto();
                                            $objPersonaFormaContacto2->setValor($strFonoMovil1);
                                            $objPersonaFormaContacto2->setEstado("Activo");
                                            $objPersonaFormaContacto2->setFeCreacion(new \DateTime('now'));
                                            $objPersonaFormaContacto2->setFormaContactoId($objAdmiFormaContacto2);
                                            $objPersonaFormaContacto2->setIpCreacion($objRequest->getClientIp());
                                            $objPersonaFormaContacto2->setPersonaId($objInfoPersona);
                                            $objPersonaFormaContacto2->setUsrCreacion($strUsrSesion);
                                        }
                                        else
                                        {
                                            $boolTieneTelefonoMovil1 = false;
                                            $boolEnviaNotificacion   = true;
                                            fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, verificar telefono movil 1."
                                                . "Los numeros de celulares pueden empenzar con 593, +593, 09.. \n");
                                        }

                                    }
                                    else
                                    {
                                        $boolTieneTelefonoMovil1 = false;
                                        $boolEnviaNotificacion   = true;
                                        fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, verificar telefono movil 1."
                                            . " Los numeros de celulares pueden empenzar con 593, +593, 09.: \n");
                                    }

                                    if($strFonoMovil2 && $strOperadoraMovil2 != "")
                                    {
                                        $objAdmiFormaContacto3 = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findOneBy(array('descripcionFormaContacto' => $strOperadoraMovil2,
                                            'estado' => 'Activo'
                                            )
                                        );
                                        if($objAdmiFormaContacto3 && (preg_match($expRegNumeros1,  $strFonoMovil2) || 
                                                                      preg_match($expRegNumeros2,  $strFonoMovil2) ||
                                                                      preg_match($expRegNumeros3,  $strFonoMovil2))&& 
                                                                      preg_match($strPatronNumeros,$strFonoMovil2)
                                        )
                                        {
                                            $objPersonaFormaContacto3 = new InfoPersonaFormaContacto();
                                            $objPersonaFormaContacto3->setValor($strFonoMovil2);
                                            $objPersonaFormaContacto3->setEstado("Activo");
                                            $objPersonaFormaContacto3->setFeCreacion(new \DateTime('now'));
                                            $objPersonaFormaContacto3->setFormaContactoId($objAdmiFormaContacto3);
                                            $objPersonaFormaContacto3->setIpCreacion($objRequest->getClientIp());
                                            $objPersonaFormaContacto3->setPersonaId($objInfoPersona);
                                            $objPersonaFormaContacto3->setUsrCreacion($strUsrSesion);
                                        }
                                        else
                                        {
                                            $boolTieneTelefonoMovil2 = false;
                                            $boolEnviaNotificacion   = true;
                                            fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, verificar telefono movil 2."
                                                . "Los numeros de celulares pueden empenzar con 593, +593, 09.. \n");
                                        }
                                    }
                                    else
                                    {
                                        $boolTieneTelefonoMovil2 = false;
                                        $boolEnviaNotificacion   = true;
                                        fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, verificar telefono movil 2. "
                                            . "Los numeros de celulares pueden empenzar con 593, +593, 09.: \n");
                                    }

                                    if($strEmailContacto != "" && preg_match($strPatronEmail, $strEmailContacto))
                                    {

                                        $objAdmiFormaContacto4 = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                            ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                            'estado' => 'Activo'
                                            )
                                        );
                                        $objPersonaFormaContacto4 = new InfoPersonaFormaContacto();
                                        $objPersonaFormaContacto4->setValor($strEmailContacto);
                                        $objPersonaFormaContacto4->setEstado("Activo");
                                        $objPersonaFormaContacto4->setFeCreacion(new \DateTime('now'));
                                        $objPersonaFormaContacto4->setFormaContactoId($objAdmiFormaContacto4);
                                        $objPersonaFormaContacto4->setIpCreacion($objRequest->getClientIp());
                                        $objPersonaFormaContacto4->setPersonaId($objInfoPersona);
                                        $objPersonaFormaContacto4->setUsrCreacion($strUsrSesion);
                                    }
                                    else
                                    {
                                        $boolTieneCorreoElectronico = false;
                                        $boolEnviaNotificacion      = true;
                                        fwrite($objFileContactosError, "\nNo se ingresado el siguiente contacto, "
                                            . "verificar el formato del correo electronico: \n");
                                    }

                                    if($boolGuardaContacto && $boolGuardaTipoContacto && $boolTieneCorreoElectronico && $boolTieneTelefonoMovil1 
                                                           && $boolTieneTelefonoFijo
                                    )
                                    {

                                        $intNumContactValidos++;

                                        // Inactivacion de contactos existentes
                                        if($strTipoIngreso == 'r' && $intNumContactValidos==1) 
                                        {
                                            //Obtengo los contactos existentes  del cliente
                                            $arrayContactos = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                ->getResultadoContactosPorCliente($intIdEmpresa, $intClienteId);

                                            if($arrayContactos)
                                            {
                                                foreach($arrayContactos as $objPersContacto)
                                                {

                                                    // Actualizacion de estado de contactos activos  existentes  a Inactivo
                                                    if($objPersContacto->getEstado()=='Activo')
                                                    {
                                                        $objPersContacto->setEstado('Inactivo');
                                                        $emComercial       ->persist($objPersContacto);
                                                        $emComercial       ->flush();

                                                        $arrayPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                 ->getPersonaEmpresaRolPorPersonaPorEmpresa($objPersContacto->getId(), 
                                                                                                                             $intIdEmpresa
                                                        );
                                                        foreach($arrayPersonaEmpresaRol as $objPersEmpresaRol)
                                                        {
                                                            $objPersEmpresaRol->setEstado('Inactivo');
                                                            $emComercial->persist($objPersEmpresaRol);
                                                            $emComercial->flush();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        // REGISTRO DE CONTACTO
                                        $emComercial->persist($objInfoPersona);
                                        $emComercial->flush();
                                        //ASIGNA ROL DE CONTACTO A LA PERSONA
                                        $emComercial->persist($objPersonaEmpresaRol);
                                        $emComercial->flush();
                                        //GRABA RELACION ENTRE CONTACTO Y CLIENTE    
                                        $emComercial->persist($objPersonaContacto);
                                        $emComercial->flush();
                                        //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
                                        $emComercial->persist($objPersonaHistorial);
                                        $emComercial->flush();
                                        // REGISTRA FORMAS DE CONTACTO 
                                        if($boolTieneTelefonoFijo)
                                        {
                                            $emComercial->persist($objPersonaFormaContacto1);
                                            $emComercial->flush();
                                        }

                                        $emComercial->persist($objPersonaFormaContacto2);
                                        $emComercial->flush();

                                        if($boolTieneTelefonoMovil2)
                                        {
                                            $emComercial->persist($objPersonaFormaContacto3);
                                            $emComercial->flush();
                                        }

                                        $emComercial->persist($objPersonaFormaContacto4);
                                        $emComercial->flush();

                                    }
                                    else
                                    {
                                        $boolEnviaNotificacion = true;
                                        fwrite($objFileContactosError, " - Nombres: " . $strNombres . " Apellidos: " . $strApellidos .
                                            "; Titulo: " . $strTitulo . "; Tipo: " . $strTipoContacto .
                                            "; Telefono Fijo: " . $strFonoFijo . "; Telefono_movil1: " . $strFonoMovil1 .
                                            "; Operadora_movil1: " . $strOperadoraMovil1 .
                                            "; Telefono_movil2: " . $strFonoMovil2 .
                                            "; Operadora_movil2: " . $strOperadoraMovil2 .
                                            "; Email: " . $strEmailContacto . "\n");
                                    }
                                }
                            }
                            else
                            {
                                return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig', array('puntoCliente' => $ptoCliente,
                                        'error' => 'Error al cargar el Archivo, archivo vacio',
                                        'idClienteSesion' => $objClienteSesion
                                        )
                                );
                            }

                            $intContador++;
                        }
                    }
                    else
                    {
                        chmod($strUrlFileContactos, 777);
                        chmod($strUrlFileContactosError, 777);
                        unlink($strUrlFileContactos);
                        unlink($strUrlFileContactosError);
                        return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig', 
                                              array('puntoCliente'    => $ptoCliente,
                                                    'error'           => 'Error al cargar el Archivo',
                                'idClienteSesion' => $objClienteSesion
                                )
                        );
                    }
                }
                else
                {
                    return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig', 
                                          array('puntoCliente'    => $ptoCliente,
                                                'error'           => 'Error al cargar el Archivo',
                            'idClienteSesion' => $objClienteSesion
                            )
                    );
                }

                // ENVIO DE NOTIFICACION CON ARCHIVO ADJUNTO DE CONTACTOS NO INGRESADOS
                if($boolEnviaNotificacion)
                {

                    $strDescripcionCliente = "";
                    $objCliente            = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intClienteId);
                    $objEmpleadoSesion     = $emComercial->getRepository('schemaBundle:InfoPersona')
                        ->findOneBy(array('login' => $strUsrSesion));
                    if($objEmpleadoSesion)
                    {
                        $objFormaContacto      = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                            ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                            'estado' => 'Activo'
                            )
                        );

                        $objAdmiFormaContactoEmp = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                               ->findOneBy(array('personaId'       => $objEmpleadoSesion->getId(),
                            'formaContactoId' => $objFormaContacto->getId(),
                                                                                 'estado'          => 'Activo'
                            )
                        );

                        $strEmailEmpleadoSesion = strtolower($objAdmiFormaContactoEmp->getValor());
                        $arrayDestinatarios[]   = $strEmailEmpleadoSesion;
                    }
                    else
                    {
                        $arrayDestinatarios[]   = 'notificaciones_telcos@telconet.ec';
                    }

                    if($objCliente->getTipoTributario() == 'NAT')
                    {
                        $strDescripcionCliente = $objCliente->getNombres() . " " . $objCliente->getApellidos();
                    }
                    else if($objCliente->getTipoTributario() == 'JUR')
                    {
                        $strDescripcionCliente = $objCliente->getRazonSocial();
                    }

                    $strAsunto          = "Reporte de Contactos no Ingresados , Cliente: $strDescripcionCliente";
                    $strCodigoPlantilla = "CONTACT_NI";
                    $objEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
                    $objEnvioPlantilla->generarEnvioPlantilla($strAsunto, $arrayDestinatarios, $strCodigoPlantilla, null, $intIdEmpresa, '', '', 
                                                              $strUrlFileContactosError, false, 'notificaciones_telcos@telconet.ec');
                }
                fclose($objFileReadContactos);
                fclose($strFileContactosError);

                chmod($strUrlFileContactos, 777);
                chmod($strUrlFileContactosError, 777);
                unlink($strUrlFileContactos);
                unlink($strUrlFileContactosError);

                return $this->render('comercialBundle:contacto:index.html.twig');
            }
            else
            {
                return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig', array('puntoCliente' => $ptoCliente,
                        'error' => 'Error: Para ingreso masivo de contactos debe tener un cliente en sesion',
                        'idClienteSesion' => $objClienteSesion
                        )
                );
                
            }


        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            chmod($strUrlFileContactos, 777);
            chmod($strUrlFileContactosError, 777);
            unlink($strUrlFileContactos);
            unlink($strUrlFileContactosError);
            return $this->render('comercialBundle:contacto:ingresarMasivos.html.twig', array(
                                 'puntoCliente'    => $ptoCliente,
                                 'error'           => $e,
                                 'idClienteSesion' => $objClienteSesion)
            );
        }
    }

    /**
     * envioNotificacionContactoNuevo
     *
     * Metodo encargado de enviar correo destinatario, asunto del correo, codigo de la plantilla e informacion del contacto    
     * creado y posteriormente se notificará por correo.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * 
     * @version 1.0 15-12-2016
     */
    private function envioNotificacionContactoNuevo($arrayParametros)
    {
        $emGeneral                  = $this->getDoctrine()->getManager();
        $strNombreParametro         = 'ENVIO_CORREO_CONTACTO';
        $strValor1Contacto          = 'NEW_CONTACTO';
        $strValor1Usuario           = 'NEW_CONTACTO_FROM_SUBJECT';
        $strHabilitaNotificacion    = '';
        $strUsuarioCorreo           = '';
        $strAsunto                  = '';
        $strCodigoPlantilla         = "NUEVO_CONTACTO";
        $strEmpresaId               = $arrayParametros['strEmpresaId'];
        $objEnvioPlantilla          = $this->get('soporte.EnvioPlantilla');

        $objParametroCab            = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy( array('nombreParametro' => $strNombreParametro, 
                                                                   'estado'          => 'Activo') );
        if ( is_object( $objParametroCab ))
        {
            $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy( array( 'estado'      => 'Activo',
                'parametroId' => $objParametroCab,
                                                             'valor1'      => $strValor1Contacto ) );

            if ( is_object( $objParametroDet ))
            {
                // Valor2 => Habilita envio de notificacion cuando se crea un contacto.
                $strHabilitaNotificacion = $objParametroDet->getValor2();
                $objParametroDet         = NULL;

                if($strHabilitaNotificacion == 'SI')
                {
                    $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy( array( 'estado'      => 'Activo',
                        'parametroId' => $objParametroCab,
                                                                     'valor1'      => $strValor1Usuario,
                                                                     'empresaCod'  => $strEmpresaId) );
                    if ( is_object( $objParametroDet ))
                    {
                        // Valor2 => Define el usuario que envia el correo cuando se cre un nuevo contacto.
                        $strUsuarioCorreo = $objParametroDet->getValor2();
                        $strAsunto        = $objParametroDet->getValor3();

                        //Se envia el correo respectivo al destinatario ingresado
                        $objEnvioPlantilla->generarEnvioPlantilla($strAsunto, 
                                                                  null, 
                                                                  $strCodigoPlantilla, 
                                                                  $arrayParametros, 
                                                                  $strEmpresaId, 
                                                                  null, 
                                                                  null, 
                                                                  null, 
                                                                  false, 
                                                                  $strUsuarioCorreo);
                    }
                }
            }//( $objParametroDet != null )
        }//( $objParametroCab != null )
    }

    /**
     * permiteEnvioNotificacionContacto
     *
     * Metodo encargado de verificar si se envía la notificacion al usuario por correo, si existe restricción de envío solo
     * se enviará a los tipos de contactos configurados en la tabla de parametros, inicialmente para Contacto Comercial y
     * Contacto Facturación, en caso de no existir restricción la notificación se envía con normalidad.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * 
     * @version 1.0 27-12-2016
     */
    private function permiteEnvioNotificacionContacto($arrayTipoContacto){

        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $strNombreParametro         = 'RESTRICCION_ENVIO_CORREO_PREFACTURAS';
        $strModulo                  = 'FINANCIERO';
        $strProceso                 = 'RESTRICCION_PREFAC_ELIMINADAS';
        $strValor1Restriccion       = 'RESTRICCION_PREFACTURAS';
        $strTipoContactoEnvioCorreo = 'TIPO_CONTACTO_ENVIA_CORREO';
        $strRestriccionEnvioCorreo  = '';
        $strEmpresaId               = '';
        $boolEnvioNotificacion      = false;

        $arrayAdmiParametroDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(       $strNombreParametro, 
                                                                $strModulo, 
                                                                $strProceso, 
                                                                '', 
                                                                $strValor1Restriccion, 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                '' );

        if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
        {
            // Valor2 => Habilita restricción de envio de notificacion por correo cuando se crea un contacto.
            $strRestriccionEnvioCorreo    = $arrayAdmiParametroDet['valor2'];
            $arrayAdmiParametroDet        = NULL;

            if($strRestriccionEnvioCorreo == 'SI')
            {
                if( $arrayTipoContacto )
                {
                    $strEmpresaId         = $arrayTipoContacto[0];
                }

                $arrayAdmiParametroDet    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->get(     $strNombreParametro, 
                                                                    $strModulo, 
                                                                    $strProceso, 
                                                                    $strTipoContactoEnvioCorreo, 
                                                                    '', 
                                                                    'SI', 
                                                                    '', 
                                                                    '', 
                                                                    '', 
                                                                    $strEmpresaId );

                if( $arrayAdmiParametroDet )
                {
                    if($arrayTipoContacto && count($arrayTipoContacto) > 0)
                    {
                         foreach( $arrayAdmiParametroDet as $arrayParametro )
                        {
                             if( in_array($arrayParametro['valor1'], $arrayTipoContacto) )
                            {
                                 $boolEnvioNotificacion  = true;
                            }//( in_array($arrayParametro['valor1'], $arrayTipoContacto) )
                        }//( $arrayAdmiParametroDet as $arrayParametro )
                    }//($arrayTipoContacto && count($arrayTipoContacto) > 0)
                }//( $arrayAdmiParametroDet )
            }
            else
            {
                //No existe restriccion de envío de correo.
                $boolEnvioNotificacion  = true;
            }

        }//($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)

        return $boolEnvioNotificacion;
    }

    /**
     * duplicarContactoAjaxAction
     *
     * Duplica un contacto y sus tipos de contacto y los asigna a los diferentes puntos
     *                             que el usuario haya seleccionado.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 13-10-2019 Se agrega implementación para duplicación masiva de contactos
     * @since 1.0
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function duplicarContactoAjaxAction()
    {
        $emComercial               = $this->getDoctrine()->getManager("telconet");
        $objRequest                = $this->getRequest();
        $objSession                = $objRequest->getSession();
        $serviceUtil               = $this->get('schema.Util');
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $arrayInfoContacto         = json_decode($objRequest->get('jsonInfoContacto'), true);
        $arrayIdPuntos             = json_decode($objRequest->get('strArrayIdPuntos'), true);
        $boolIncluirNivelCliente   = filter_var($objRequest->get('booleanIncluirNivelCliente'), FILTER_VALIDATE_BOOLEAN);
        $boolIncluirTodosPuntos    = filter_var($objRequest->get('booleanTodosLosPuntos'), FILTER_VALIDATE_BOOLEAN);
        $intIdPersona              = $arrayInfoContacto['intIdPersona'];
        $intIdPersonaEmpresaRol    = $objSession->get('cliente')['id_persona_empresa_rol'];
        $strNombreTipoRol          = $objSession->get('cliente')['nombre_tipo_rol'];
        $intIdOficina              = $objSession->get('idOficina');
        $strEscalabilidad          = $objRequest->get('strEscalabilidad');
        $strHorarios               = $objRequest->get('strHorariosContact');
        $intTotalPuntos            = 0;
        $intCantidadLoginLimite    = 5;
        $strMensajeNivelCliente    = '';
        $strMensajeNivelPunto      = '';
        $arrayLoginRepetidos       = array();
        $objReturnResponse         = new ReturnResponse();

        $emComercial->getConnection()->beginTransaction();

        try
        {
            $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);

            //Si no existe id termina método
            if (!isset($intIdPersona) || $intIdPersona <= 0 || !isset($intIdPersonaEmpresaRol) || $intIdPersonaEmpresaRol <= 0)
            {
                throw new \Exception("Sin información de contacto");
            }

            if($boolIncluirTodosPuntos)
            {
                $arrayParametrosObtenerPuntos = array(
                    'idper'            => $intIdPersonaEmpresaRol,
                    'rol'              => $strNombreTipoRol,
                    'strCodEmpresa'    => $strCodEmpresa,
                    'intStart'         => 0,
                    'intLimit'         => PHP_INT_MAX,
                    'serviceInfoPunto' => $this->get('comercial.InfoPunto'),
                    'strNotInEstados'  => array('Eliminado', 'Cancelado', 'Anulado')
                );

                $arrayEntitiesInfoPunto =  $emComercial->getRepository('schemaBundle:InfoPunto')
                    ->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametrosObtenerPuntos);
                $arrayIdPuntos = array_map(function($entityInfoPuntos)
                {
                    return $entityInfoPuntos['id'];
                }, $arrayEntitiesInfoPunto['registros']);
            }

            if(!$boolIncluirNivelCliente && (!$arrayIdPuntos || count($arrayIdPuntos) <= 0))
            {
                throw new \Exception("No se ha seleccionado puntos");
            }
            else
            {
                $intTotalPuntos = count($arrayIdPuntos);
            }

            $objInfoPersona    = $this->getInfoPersonaAction();
            $arrayInfoPersona  = json_decode($objInfoPersona->getContent(), true);
            $arrayInfoPersona  = $arrayInfoPersona['registros'];
            $arrayRolesPersona = $arrayInfoPersona['arrayRol'];

            $arrayIdRoles = array_map(function($arrayRol)
            {
                if($arrayRol['strEstado'] == 'Activo' || $arrayRol['strEstado'] == 'Modificado')
                {
                    return $arrayRol['intIdRol'];
                }
            }, $arrayRolesPersona);

            $arrayIdRoles = array_unique(array_filter($arrayIdRoles));

            //Si contacto no tiene roles termina el método
            if (!$arrayIdRoles || count($arrayIdRoles) <= 0)
            {
                throw new \Exception("Contacto sin roles");
            }

            $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                ->find($intIdPersona);

            if(!is_object($entityInfoPersona))
            {
                throw new \Exception('No se encontró el contacto.');
            }

            $entityInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->find($intIdPersonaEmpresaRol);

            if(!is_object($entityInfoPersonaEmpresaRol))
            {
                throw new \Exception('No se encontró el cliente.');
            }

            $entityInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                ->find($intIdOficina);

            //Si no existe la oficina termina el método
            if (!is_object($entityInfoOficinaGrupo))
            {
                throw new \Exception('No existe oficina.');
            }

            $strIdRoles  = implode(',', $arrayIdRoles);
            $strIdPuntos = implode(',', $arrayIdPuntos);

            $arrayRespuestaMasiva = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                ->duplicarContactoMasivo(array('strIdRoles'             => $strIdRoles,
                                               'strIdPuntos'            => $strIdPuntos,
                                               'intIdCliente'           => $intIdPersonaEmpresaRol,
                                               'intIdPersona'           => $intIdPersona,
                                               'intIdOficina'           => $entityInfoOficinaGrupo->getId(),
                                               'intDuplicaNivelCliente' => $boolIncluirNivelCliente ? 1 : 0,
                                               'strCodEmpresa'          => $strCodEmpresa,
                                               'strUsuario'             => $objSession->get('user'),
                                               'strIp'                  => $objRequest->getClientIp(),
                                               'arrayExtraParams'       => array(
                                                   'intCantidadLoginLimite' => $intCantidadLoginLimite,
                                                   'strDescripcionRol1'     => 'Contacto Seguridad Escalable',
                                                   'strDescripcionCarac1'   => 'NIVEL ESCALABILIDAD',
                                                   'strDescripcionCarac2'   => 'HORARIO ESCALABILIDAD',
                                                   'strEscalabilidad'       => $strEscalabilidad,
                                                   'strHorario'             => $strHorarios)));

            if(!isset($arrayRespuestaMasiva['strMensaje']) || empty($arrayRespuestaMasiva['strMensaje']) ||
                $arrayRespuestaMasiva['strMensaje'] !== 'OK')
            {
                throw new \Exception('Al momento de duplicar el contacto.');
            }

            if($intTotalPuntos > 0)
            {
                $intLoginRepetidos = isset($arrayRespuestaMasiva['intLoginRepetidos']) ? $arrayRespuestaMasiva['intLoginRepetidos'] : 0;

                if(isset($arrayRespuestaMasiva['strLoginRepetidos']) && !empty($arrayRespuestaMasiva['strLoginRepetidos']))
                {
                    $arrayLoginRepetidos = explode(',', $arrayRespuestaMasiva['strLoginRepetidos']);
                    $arrayLoginRepetidos = array_unique(array_filter($arrayLoginRepetidos));

                    sort($arrayLoginRepetidos, SORT_LOCALE_STRING);

                    $strLoginRepetidos = implode('<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;',
                        array_slice($arrayLoginRepetidos, 0, $intCantidadLoginLimite));
                }

                $strMensajeNivelPunto = '<br/>Se duplicó el contacto en <b>' . ($intTotalPuntos - $intLoginRepetidos) .
                    '</b> de <b>' . $intTotalPuntos . '</b> punto' . (($intTotalPuntos > 1) ? 's.' : '.');
            }

            if(isset($arrayRespuestaMasiva['strMsjNivelCliente']) && !empty($arrayRespuestaMasiva['strMsjNivelCliente']))
            {
                $strMensajeNivelCliente .= '<br/><br/>' . $arrayRespuestaMasiva['strMsjNivelCliente'];
            }

            if($intLoginRepetidos > 0)
            {
                if($intLoginRepetidos > $intCantidadLoginLimite)
                {
                    $strMensajeNivelPuntoExtraInfo = '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;... y ' .
                        ($intLoginRepetidos - $intCantidadLoginLimite) . ' más.';
                }

                $strMensajeNivelPunto .= '<br/><br/>Contacto ya existente en punto' . (($intTotalPuntos > 1) ? 's' : '') .
                    ':<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>-&nbsp;' . $strLoginRepetidos . '</b>';
            }

            $strMensajeInicio = ($intLoginRepetidos == 0)
                ? 'Se realizó el proceso con éxito.<br/>'
                : 'Se realizó el proceso.<br/>';

            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($strMensajeInicio .
                $strMensajeNivelPunto . $strMensajeNivelPuntoExtraInfo . $strMensajeNivelCliente);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus('Existió un error.' . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError(
                'Telcos+',
                __METHOD__,
                isset($arrayRespuestaMasiva['strMensaje']) && !empty($arrayRespuestaMasiva['strMensaje'])
                    ? $arrayRespuestaMasiva['strMensaje']
                    : $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'), $objRequest->getClientIp());
        }

        $emComercial->getConnection()->close();
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));

        return $objResponse;
    }



  /** 
     * Actualizar listado de representantes legal vinculados
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     * 
     * @return Response mensaje de confirmacion de ejecucion correcta de la transaccion
     */
    public function ajaxValidarFormaContactoAction()
    {
       
        $objRequest          = $this->getRequest();
        $strEmpresaCod       = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $strUsuario          = $objRequest->getSession()->get('user');
        $strIdPais           = $objRequest->getSession()->get('intIdPais'); 
        $intIdOficina           = $objRequest->getSession()->get('idOficina'); 
        $strClientIp            = $objRequest->getClientIp();

       
        $serviceUtil            = $this->get('schema.Util');        
      
        $arrayResponse['status']   = "ERROR";
        $arrayResponse['message']  = "";         
     
        $strFormaContacto            = $objRequest->get('strFormaContacto');
        $strOrigen                   = $objRequest->get('strOrigen');
        
        
        try
        {

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();  

            $objFormaContacto =    json_decode(  $strFormaContacto  , true); 
  
            $arrayParams = array(
            'token'                                => $arrayTokenCas['strToken'],
            'codEmpresa'                           => $strEmpresaCod,
            'prefijoEmpresa'                       => $strPrefijoEmpresa,
            'oficinaId'                            => $intIdOficina,
            'origenWeb'                            => $strOrigen,
            'clientIp'                             => $strClientIp,
            'usrCreacion'                          => $strUsuario, 
            'idPais'                               => $strIdPais ,   
            'formaContacto'                       => $objFormaContacto 
           );

                                                   
            $servicePreClienteMs = $this->get('comercial.PreClienteMs');
            $objResponse     =    $servicePreClienteMs->wsValidarFormaContacto(  $arrayParams);
            if ($objResponse['strStatus']!='OK' ) 
            {
                throw new \Exception( $objResponse['strMensaje']);
            }         
          
            $arrayResponse['response'] = $objResponse ['objData'];
            $arrayResponse['status']   = $objResponse ['strStatus'];
            $arrayResponse['message']  = $objResponse ['strMensaje']; 
        }
        catch(\Exception $objException)
        {
            $arrayResponse['message'] = $objException->getMessage() ? $objException->getMessage()
              : 'Ha ocurrido un error inesperado al realizar la consulta';

            $arrayParametrosLog['enterpriseCode']   = $strEmpresaCod;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayResponse['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParams, 128);
            $arrayParametrosLog['creationUser']     = $strUsuario;
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }



}
