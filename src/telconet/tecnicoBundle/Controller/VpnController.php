<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use telconet\schemaBundle\Form\VpnType;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\schemaBundle\Entity\AdmiParametroDet;

/**
 * Documentación para la clase 'VpnController'.
 *
 * Clase que contiene toda la funcionalidad de la Administración de Vpns para los Clientes
 *
 * @author Kenneth Jimenez <kjimenez@telconet.ec>
 * @version 1.0 08-12-2015
*/
class VpnController extends Controller
{  
    /**
     * @Secure(roles="ROLE_319-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Método utilizado para cargar la vista principal de la Administración de Vpns
     *
     * @return twig index
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Envio de itemMenu para utilizar el icono de la opcion
     *
     * @author Modifica: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 2019-08-29 Se crea la acción: mapearVrfVlan
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 2021-05-10 - Se agrega el filtro tipo red para el grid Vpn.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 2022-05-11 - Se elimina el filtro tipo red para el grid Vpn.
     */
    public function indexAction()
    {
        
        $rolesPermitidos = array();
        
        //ROLES VPN
        if (true === $this->get('security.context')->isGranted('ROLE_319-1'))
        {
                $rolesPermitidos[] = 'ROLE_319-1'; //index
        }
        if (true === $this->get('security.context')->isGranted('ROLE_319-7'))
        {
                $rolesPermitidos[] = 'ROLE_319-7'; //grid
        }
        if (true === $this->get('security.context')->isGranted('ROLE_319-2'))
        {
                $rolesPermitidos[] = 'ROLE_319-2'; //new
        }
        if (true === $this->get('security.context')->isGranted('ROLE_319-3'))
        {
                $rolesPermitidos[] = 'ROLE_319-3'; //create
        }
        if (true === $this->get('security.context')->isGranted('ROLE_319-3297'))
        {
                $rolesPermitidos[] = 'ROLE_319-3297'; //import
        }
        if (true === $this->get('security.context')->isGranted('ROLE_319-6677'))
        {
                $rolesPermitidos[] = 'ROLE_319-6677'; //mapearVrfVlan
        }

        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("319", "1");
        return $this->render('tecnicoBundle:Vpn:index.html.twig', array(
            'item'            => $entityItemMenu,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * @Secure(roles="ROLE_319-7")
     *
     * Documentación para el método 'ajaxGridAction'.
     *
     * Método utilizado para generar el Json de registros de las Vpns de los Clientes
     *
     * @param string nombre Nombre de la Vpn a buscar.
     * @param string flag Bandera utilizada para cargar vpns del cliente o no, esto debido a la opcion de importar.
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id':'',
     *                                   'vpn':'',
     *                                   'fe_creacion':'',
     *                                   'usr_creacion':'',
     *                                   'vrf':'',
     *                                   'rd_id':''
     *                                   }]
     *                      }]
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-08-2019 Se agrega el parámetro 'strMigracionVlan' a la llamada de la función getJsonVpnsCliente, por ende se transorman
     *                         a un array
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 2021-05-10 - Se agrega el filtro tipo red para el grid Vpn.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 2022-05-11 - Se elimina el filtro tipo red para el grid Vpn.
     */
    public function ajaxGridAction()
    {
        $response    = new JsonResponse();
        
        $request     = $this->get('request');
        $session     = $this->get('session');
        
        $cliente     = $session->get('cliente');
        $nombre      = $request->get('nombre');
        $flag        = $request->get('flag');
        $start       = $request->get('start');
        $limit       = $request->get('limit');

        $arrayParametros["intPersonaEmpresaRol"] = $cliente['id_persona_empresa_rol'];
        $arrayParametros["strNombre"]            = $nombre;
        $arrayParametros["intStart"]             = $start;
        $arrayParametros["intLimit"]             = $limit;
        $arrayParametros["strFlag"]              = $flag;
        $arrayParametros["strMigracionVlan"]     = "N";

        $objResult   = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                            ->getJsonVpnsCliente($arrayParametros);

        $response->setContent($objResult);

        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_319-7")
     *
     * Documentación para el método 'ajaxGridImportAction'.
     *
     * Método utilizado para generar el Json de registros de las Vpns Importadas de los Clientes
     *
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id':'',
     *                                   'vpn':'',
     *                                   'fe_creacion':'',
     *                                   'usr_creacion':'',
     *                                   'vrf':'',
     *                                   'rd_id':''
     *                                   }]
     *                     }]
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27-08-2019 Se agrega el parámetro 'strMigracionVlan' a la llamada de la función getJsonVpnsImportCliente
     */
    public function ajaxGridImportAction()
    {
        $response    = new JsonResponse();
        
        $request     = $this->get('request');
        $session     = $this->get('session');
        
        $cliente          = $session->get('cliente');
        $start            = $request->get('start');
        $limit            = $request->get('limit');
        $strMigracionVlan = "N";

        $objResult   = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                            ->getJsonVpnsImportCliente($cliente['id_persona_empresa_rol'],$strMigracionVlan,$start,$limit);
        
        $response->setContent($objResult);

        return $response;
    }
    /**
     * @Secure(roles="ROLE_319-7")
     *
     * Documentación para el método 'ajaxGridBuscarImportAction'.
     *
     * Método utilizado para generar el Json de registros de las Vpns de los Clientes
     *
     * @param string nombre Nombre de la Vpn a buscar.
     * @param string flag Bandera utilizada para cargar vpns del cliente o no, esto debido a la opcion de importar.
     * @param string start min de registros de vpns a buscar.
     * @param string limit max de registros de vpns a buscar.
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id':'',
     *                                   'vpn':'',
     *                                   'fe_creacion':'',
     *                                   'usr_creacion':'',
     *                                   'vrf':'',
     *                                   'rd_id':''
     *                                   }]
     *                      }]
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-23 Incluir la Razon Social como criterio de búsqueda
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27-08-2019 Se agrega el parámetro 'strMigracionVlan' a la llamada de la función getJsonVpnsCliente, por ende se transorman
     *                         a un array
     */
    public function ajaxGridBuscarImportAction()
    {
        $response        = new JsonResponse();
        $arrayParametros = array();

        $request     = $this->get('request');

        $nombre      = $request->get('nombre');
        $cliente = $request->get('cliente');
        if($nombre=="" && $cliente=="")
        {
            $response->setContent(null);
        }
        else
        {
            $flag        = $request->get('flag');
            $start       = $request->get('start');
            $limit       = $request->get('limit');

            $idPersonaEmpresaRol=0;

            $arrayParametros["intPersonaEmpresaRol"] = $idPersonaEmpresaRol;
            $arrayParametros["strNombre"]            = $nombre;
            $arrayParametros["intStart"]             = $start;
            $arrayParametros["intLimit"]             = $limit;
            $arrayParametros["strFlag"]              = $flag;
            $arrayParametros["strCliente"]           = $cliente;
            $arrayParametros["strMigracionVlan"]     = "N";

            $objResult   = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                ->getJsonVpnsCliente($arrayParametros);
            $response->setContent($objResult);
        }

        return $response;
    }


    /**
    * mapearVrfyVlanAction
    * Función que asocia una vlan a una vrf
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 14-08-2019
	*/
    public function mapearVrfyVlanAction()
    {
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');

        $objPeticion         = $this->getRequest();
        $objSession          = $objPeticion->getSession();
        $strUserSession      = $objSession->get('user');
        $strIpCreacion       = $objPeticion->getClientIp();
        $arrayCliente        = $objSession->get('cliente');
        $strNuevaVlan        = $objPeticion->get('nuevaVlan');
        $strVrf              = $objPeticion->get('vrf');
        $intIdVrf            = $objPeticion->get('idVrf');
        $strVpn              = $objPeticion->get('vpn');
        $intIdVpn            = $objPeticion->get('idVpn');

        $strDescipcionCab    = "PARAMETROS PROYECTO SEGMENTACION VLAN";
        $strDescipcionDet    = "MAPEO VRF - VLAN Nedetel";
        $strDescipcionDetVpn = "VPN";
        $strEstado           = "Activo";

        $objResponse          = new JsonResponse();
        $arrayRespuesta       = array();
        $serviceUtil          = $this->get('schema.Util');
        $serviceInfoElemento  = $this->get('tecnico.InfoElemento');

        $emGeneral->getConnection()->beginTransaction();

        try
        {
            //Se verifica si el cliente esta configurado para obtener vlans por vrf
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                             ->findOneBy(array("nombreParametro" => $strDescipcionCab,
                                                               "estado"          => $strEstado));

            if(is_object($objAdmiParametroCab))
            {
                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array("descripcion"  => $strDescipcionDet,
                                                                   "valor1"       => $intIdVrf,
                                                                   "estado"       => $strEstado));

                //Se mapea la vrf con la vlan
                if(!is_object($objAdmiParametroDet))
                {
                    //Se registra la vpn en la tabla de parametros
                    $objAdmiParametroDet = new AdmiParametroDet();
                    $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
                    $objAdmiParametroDet->setDescripcion($strDescipcionDetVpn);
                    $objAdmiParametroDet->setValor1($intIdVpn);
                    $objAdmiParametroDet->setValor2($strVpn);
                    $objAdmiParametroDet->setEstado('Activo');
                    $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                    $objAdmiParametroDet->setUsrCreacion($strUserSession);
                    $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                    $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                    $objAdmiParametroDet->setUsrUltMod($strUserSession);
                    $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                    $emGeneral->persist($objAdmiParametroDet);
                    $emGeneral->flush();

                    //Se relaciona la vrf con la vlan
                    $objAdmiParametroDet = new AdmiParametroDet();
                    $objAdmiParametroDet->setParametroId($objAdmiParametroCab);
                    $objAdmiParametroDet->setDescripcion($strDescipcionDet);
                    $objAdmiParametroDet->setValor1($intIdVrf);
                    $objAdmiParametroDet->setValor2($strVrf);
                    $objAdmiParametroDet->setValor3($strNuevaVlan);
                    $objAdmiParametroDet->setEstado('Activo');
                    $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                    $objAdmiParametroDet->setUsrCreacion($strUserSession);
                    $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                    $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                    $objAdmiParametroDet->setUsrUltMod($strUserSession);
                    $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                    $emGeneral->persist($objAdmiParametroDet);
                    $emGeneral->flush();
                }
                else
                {
                    //Se actualiza el mapeo de una vrf y vlan
                    $objAdmiParametroDet->setValor3($strNuevaVlan);
                    $objAdmiParametroDet->setEstado('Activo');
                    $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                    $objAdmiParametroDet->setUsrUltMod($strUserSession);
                    $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                    $emGeneral->persist($objAdmiParametroDet);
                    $emGeneral->flush();
                }

                $arrayParametros["intPersonaEmpresaRol"] = $arrayCliente['id_persona_empresa_rol'];
                $arrayParametros["strVlan"]              = $strNuevaVlan;
                $arrayParametros["strUser"]              = $strUserSession;
                $arrayParametros["strIpUser"]            = $strIpCreacion;

                //De no existir asociada la VLAN, se procede a realizar la relacion con los PE
                $serviceInfoElemento->reservarVlanPorPE($arrayParametros);

                $emGeneral->getConnection()->commit();
            }

            $arrayRespuesta["status"]  = "OK";
            $arrayRespuesta["mensaje"] = 'Mapeo realizado exitosamente!';
        }
        catch(\Exception $ex)
        {
            if($emGeneral->isTransactionActive())
            {
                $emGeneral->rollback();
            }

            $serviceUtil->insertError('Telcos+',
                                      'VpnController->mapearVrfyVlanAction',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $arrayRespuesta["status"]  = "ERROR";
            $arrayRespuesta["mensaje"] = "Error en la transacción, Favor comunicarse con Sistemas";

            $emGeneral->close();
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_319-2")
     *
     * Documentación para el método 'newAction'.
     *
     * Método utilizado para cargar la vista de nueva Vpn para un cliente
     *
     * @return twig new
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Envio de itemMenu para utilizar el icono de la opcion
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 2021-05-10 - Se agrega los tipo red GPON y MPLS para la creación Vpn.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 2022-05-11 - Se quita e tipo de red y se asocia la vlan para vrf de cámaras.
    */
    public function newAction()
    {
        $objSession            = $this->get('session');
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $strEmpresaCod         = $this->getRequest()->getSession()->get('idEmpresa');
        $objPtoCliente         = $objSession->get('ptoCliente');
        $booleanCamara         = false;
        $arrayEstadosNotCamara = array();
        //obtener id del producto camara gpon
        $arrayProdCamaraGpon   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                                    'COMERCIAL',
                                                                    '',
                                                                    '',
                                                                    'PRODUCTO_ADICIONAL_CAMARA',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strEmpresaCod);
        //obtener estados no permitidos del producto camara gpon
        $arrayParEstNotCamara  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('NUEVA_RED_GPON_TN',
                                                                    'COMERCIAL',
                                                                    '',
                                                                    '',
                                                                    'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '');
        foreach($arrayParEstNotCamara as $arrayParametroItem)
        {
            $arrayEstadosNotCamara[] = $arrayParametroItem['valor2'];
        }
        //obtener id del producto camara gpon
        $arrayProdCamaraMpls   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                                    'COMERCIAL',
                                                                    '',
                                                                    '',
                                                                    'PRODUCTO_CAMARA_MPLS',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strEmpresaCod);
        //verificar camara activa gpon
        if(isset($arrayProdCamaraGpon) && !empty($arrayProdCamaraGpon) && !empty($arrayEstadosNotCamara))
        {
            $objServCamActiva = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                ->createQueryBuilder('t')
                                                ->where("t.puntoId       = :puntoId")
                                                ->andWhere("t.productoId = :productoId")
                                                ->andWhere("t.estado     NOT IN (:estados)")
                                                ->setParameter('puntoId', $objPtoCliente['id'])
                                                ->setParameter('productoId', $arrayProdCamaraGpon['valor2'])
                                                ->setParameter('estados', array_values($arrayEstadosNotCamara))
                                                ->orderBy('t.id', 'ASC')
                                                ->setMaxResults(1)
                                                ->getQuery()
                                                ->getOneOrNullResult();
            if(is_object($objServCamActiva))
            {
                $booleanCamara = true;
            }
        }
        //verificar camara activa mpls
        if(isset($arrayProdCamaraMpls) && !empty($arrayProdCamaraMpls))
        {
            $arrayEstadosCamara = array('Pendiente','Activo');
            $objServCamActiva = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                ->createQueryBuilder('t')
                                                ->where("t.puntoId       = :puntoId")
                                                ->andWhere("t.productoId = :productoId")
                                                ->andWhere("t.estado     IN (:estados)")
                                                ->setParameter('puntoId', $objPtoCliente['id'])
                                                ->setParameter('productoId', $arrayProdCamaraMpls['valor2'])
                                                ->setParameter('estados', array_values($arrayEstadosCamara))
                                                ->orderBy('t.id', 'ASC')
                                                ->setMaxResults(1)
                                                ->getQuery()
                                                ->getOneOrNullResult();
            if(is_object($objServCamActiva))
            {
                $booleanCamara = true;
            }
        }
        //seteo los tipo de red
        $arrayListaFormatos    = array();
        $arrayFormatoDetalles  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'FORMATO_VRF_SERVICIOS',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                $strEmpresaCod);
        foreach($arrayFormatoDetalles as $arrayItemDet)
        {
            $arrayListaFormatos['choices'][$arrayItemDet['valor2']] = $arrayItemDet['valor2'];
            if($arrayItemDet['valor3'] === "SI")
            {
                $arrayListaFormatos['select'] = $arrayItemDet['valor2'];
            }
        }
        $objForm = $this->createForm(new VpnType(array('arrayListaFormatos'=>$arrayListaFormatos,'booleanCamara'=>$booleanCamara)), null);

        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("319", "1");

        return $this->render('tecnicoBundle:Vpn:new.html.twig', array(
            'item' => $entityItemMenu,
            'tieneCamara' => $booleanCamara ? 'SI' : 'NO',
            'form' => $objForm->createView()));
    }

    /**
     * @Secure(roles="ROLE_319-3")
     *
     * Documentación para el método 'createAction'.
     *
     * Método utilizado para crear la Vpn para un cliente
     *
     * @param string nombre nombre de la Vpn a crear
     *
     * @return mixed redirect
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 2021-05-10 - Se valida el tipo red GPON o MPLS para la creación Vpn.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 2022-05-11 - Se quita e tipo de red y se asocia la vlan para vrf de cámaras.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 2022-06-23 - Se valida si el punto posee cámara para obtener los parámetros de cámaras del request
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 2022-06-23 - Se agrega característica para identificar las vrf de cámaras
     *
    */
    public function createAction()
    {
        $request     = $this->get('request');
        $session     = $this->get('session');
        $emComercial = $this->get('doctrine')->getManager();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $arrayVpnFormat = $request->get('vpn_form');
        $booleanCamara  = isset($arrayVpnFormat['tiene_camara']) ? $arrayVpnFormat['tiene_camara'] == 'SI' : false;
        //seteo los tipo de red
        $arrayListaFormatos    = array();
        $strEmpresaCod         = $session->get('idEmpresa');
        $arrayFormatoDetalles  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'FORMATO_VRF_SERVICIOS',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                $strEmpresaCod);
        foreach($arrayFormatoDetalles as $arrayItemDet)
        {
            $arrayListaFormatos['choices'][$arrayItemDet['valor2']] = $arrayItemDet['valor2'];
            if($arrayItemDet['valor3'] === "SI")
            {
                $arrayListaFormatos['select'] = $arrayItemDet['valor2'];
            }
        }
        $objForm = $this->createForm(new VpnType(array('arrayListaFormatos'=>$arrayListaFormatos,'booleanCamara'=>$booleanCamara)), null);
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
           
            $objForm->bind($request);
            if ($objForm->isValid())
            {
                $cliente = $session->get('cliente');
                $strNombre        = trim($objForm->get('nombre')->getData());
                $strEsCamara      = null;
                $strFormatoCamara = null;
                if($booleanCamara)
                {
                    $strEsCamara      = trim($objForm->get('es_camara')->getData());
                    $strFormatoCamara = trim($objForm->get('formato_camara')->getData());
                }
                //verificar si es camara
                if(!empty($strEsCamara) && $strEsCamara == 1)
                {
                    $strNombre = $strFormatoCamara."_".$strNombre;
                }
                
                $objVpnRepetido = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                              ->findOneBy(array("valor" => $strNombre, "estado" => "Activo"));
                if($objVpnRepetido)
                {
                    $this->get('session')->getFlashBag()->add('error', 'Nombre ya existente, favor ingrese otro!');
                    return $this->redirect($this->generateUrl('vpn_new'));
                }

                //obtener caracteristica
                $objCaractVrfCamara    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" =>"VRF_VIDEO_SAFECITY", "estado" => "Activo"));
                $objCaracteristicaVpn  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" =>"VPN", "estado" => "Activo"));
                $objCaracteristicaVrf  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" =>"VRF", "estado" => "Activo"));
                $objCaracteristicaRdId = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" =>"RD_ID", "estado" => "Activo"));
                $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($cliente['id_persona_empresa_rol']);

                //VPN
                $objInfoPersonaEmpresaRolCaracVpn = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCaracVpn->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCaracVpn->setCaracteristicaId($objCaracteristicaVpn);
                $objInfoPersonaEmpresaRolCaracVpn->setValor($strNombre);
                $objInfoPersonaEmpresaRolCaracVpn->setEstado("Activo");
                $objInfoPersonaEmpresaRolCaracVpn->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolCaracVpn->setUsrCreacion($session->get('user'));
                $objInfoPersonaEmpresaRolCaracVpn->setIpCreacion($request->getClientIp());
                $emComercial->persist($objInfoPersonaEmpresaRolCaracVpn);
                $emComercial->flush();
                
                //VRF
                $objInfoPersonaEmpresaRolCaracVrf = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCaracVrf->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCaracVrf->setCaracteristicaId($objCaracteristicaVrf);
                $objInfoPersonaEmpresaRolCaracVrf->setValor($strNombre."_".$objInfoPersonaEmpresaRolCaracVpn->getId());
                $objInfoPersonaEmpresaRolCaracVrf->setPersonaEmpresaRolCaracId($objInfoPersonaEmpresaRolCaracVpn->getId());
                $objInfoPersonaEmpresaRolCaracVrf->setEstado("Activo");
                $objInfoPersonaEmpresaRolCaracVrf->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolCaracVrf->setUsrCreacion($session->get('user'));
                $objInfoPersonaEmpresaRolCaracVrf->setIpCreacion($request->getClientIp());
                $emComercial->persist($objInfoPersonaEmpresaRolCaracVrf);
                $emComercial->flush();

                //RD ID
                $objInfoPersonaEmpresaRolCaracRdId = new InfoPersonaEmpresaRolCarac();
                $objInfoPersonaEmpresaRolCaracRdId->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objInfoPersonaEmpresaRolCaracRdId->setCaracteristicaId($objCaracteristicaRdId);
                $objInfoPersonaEmpresaRolCaracRdId->setValor("27947:".$objInfoPersonaEmpresaRolCaracVpn->getId());
                $objInfoPersonaEmpresaRolCaracRdId->setPersonaEmpresaRolCaracId($objInfoPersonaEmpresaRolCaracVpn->getId());
                $objInfoPersonaEmpresaRolCaracRdId->setEstado("Activo");
                $objInfoPersonaEmpresaRolCaracRdId->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolCaracRdId->setUsrCreacion($session->get('user'));
                $objInfoPersonaEmpresaRolCaracRdId->setIpCreacion($request->getClientIp());
                $emComercial->persist($objInfoPersonaEmpresaRolCaracRdId);

                if(is_object($objCaractVrfCamara) && !empty($strEsCamara) && $strEsCamara == 1)
                {
                    //VRF_VIDEO_SAFECITY
                    $objInfoPerEmpRolCaracVrfCam = new InfoPersonaEmpresaRolCarac();
                    $objInfoPerEmpRolCaracVrfCam->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objInfoPerEmpRolCaracVrfCam->setCaracteristicaId($objCaractVrfCamara);
                    $objInfoPerEmpRolCaracVrfCam->setValor($objInfoPersonaEmpresaRolCaracVrf->getId());
                    $objInfoPerEmpRolCaracVrfCam->setPersonaEmpresaRolCaracId($objInfoPersonaEmpresaRolCaracVpn->getId());
                    $objInfoPerEmpRolCaracVrfCam->setEstado("Activo");
                    $objInfoPerEmpRolCaracVrfCam->setFeCreacion(new \DateTime('now'));
                    $objInfoPerEmpRolCaracVrfCam->setUsrCreacion($session->get('user'));
                    $objInfoPerEmpRolCaracVrfCam->setIpCreacion($request->getClientIp());
                    $emComercial->persist($objInfoPerEmpRolCaracVrfCam);
                }

                $emComercial->flush();
                $emComercial->getConnection()->commit();
                return $this->redirect($this->generateUrl('vpn'));
            }
        }
        catch (\Exception $e) 
        {
            $mensajeError = "Error VPN: ".$e->getMessage();
            error_log($mensajeError);
            
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        
        $this->get('session')->getFlashBag()->add('error', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.');
        return $this->redirect($this->generateUrl('vpn_new'));  
        
    }
    
    /**
     * @Secure(roles="ROLE_319-2")
     *
     * Documentación para el método 'newImportAction'.
     *
     * Método utilizado para cargar la vista de Importar una Vpn para el cliente
     *
     * @return twig new_import
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Envio de itemMenu para utilizar el icono de la opcion
    */
    public function newImportAction()
    {
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("319", "1");
        return $this->render('tecnicoBundle:Vpn:new_import.html.twig', array('item' => $entityItemMenu));
    }
    
    /**
     * @Secure(roles="ROLE_319-3")
     *
     * Documentación para el método 'createImportAction'.
     *
     * Método utilizado para importar una Vpn para un cliente
     *
     * @param int idVpn id de la vpn a importar
     *
     * @return mixed redirect
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Agregar mensaje por error en VPN seleccionada e incluir el mensaje de que fue importada
    */
    public function createImportAction()
    {
        $request     = $this->get('request');
        $session     = $this->get('session');
        $emComercial = $this->get('doctrine')->getManager();
        
        $idVpn       = $request->get('idVpn');
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
           
            $cliente = $session->get('cliente');
            $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($cliente['id_persona_empresa_rol']);
            $objCaracteristicaVpnImportada  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array("descripcionCaracteristica" =>"VPN_IMPORTADA", "estado" => "Activo"));
            
            $objVpnImportadaRepetida = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneBy(
                                                                array(
                                                                        "personaEmpresaRolId" => $cliente['id_persona_empresa_rol'],
                                                                        "caracteristicaId"    => $objCaracteristicaVpnImportada->getId(),
                                                                        "valor"               => $idVpn, 
                                                                        "estado"              => "Activo"
                                                                        )
                                                                );
            if($objVpnImportadaRepetida)
            {
                $this->get('session')->getFlashBag()->add('error', 'Vrf ya se encuentra importada, favor seleccione otra!');
                return $this->redirect($this->generateUrl('vpn_import_new'));
            }
            
            //RELACION CLIENTE - VPN IMPORTADA
            $objInfoPersonaEmpresaRolCaracVrfImportada = new InfoPersonaEmpresaRolCarac();
            $objInfoPersonaEmpresaRolCaracVrfImportada->setPersonaEmpresaRolId($objPersonaEmpresaRol);
            $objInfoPersonaEmpresaRolCaracVrfImportada->setCaracteristicaId($objCaracteristicaVpnImportada);
            $objInfoPersonaEmpresaRolCaracVrfImportada->setValor($idVpn);
            $objInfoPersonaEmpresaRolCaracVrfImportada->setEstado("Activo");
            $objInfoPersonaEmpresaRolCaracVrfImportada->setFeCreacion(new \DateTime('now'));
            $objInfoPersonaEmpresaRolCaracVrfImportada->setUsrCreacion($session->get('user'));
            $objInfoPersonaEmpresaRolCaracVrfImportada->setIpCreacion($request->getClientIp());
            $emComercial->persist($objInfoPersonaEmpresaRolCaracVrfImportada);
            $emComercial->flush();
            
            $emComercial->getConnection()->commit();
            
            $this->get('session')->getFlashBag()->add('info', 'Se importo exitosamente la VPN ('.$idVpn.')');
            return $this->redirect($this->generateUrl('vpn'));
        }
        catch (\Exception $e) 
        {
            $mensajeError = "Error VPN: ".$e->getMessage();
            error_log($mensajeError);
            $this->get('session')->getFlashBag()->add('error', $mensajeError);
            
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        
        $this->get('session')->getFlashBag()->add('error', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.');
        return $this->redirect($this->generateUrl('vpn_import_new'));  
        
    }
    
}