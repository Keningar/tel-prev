<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoDetalleElemento;

use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'VlanController'.
 *
 * Clase que contiene toda la funcionalidad de la Administración de Vlans para los Clientes
 *
 * @author Kenneth Jimenez <kjimenez@telconet.ec>
 * @version 1.0 08-12-2015
*/
class VlanController extends Controller
{ 
    /**
     * @Secure(roles="ROLE_320-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Método utilizado para cargar la vista principal de la Administración de Vlans
     *
     * @return twig index
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author Modifica: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Envio de itemMenu para utilizar el icono de la opcion
    */
    public function indexAction()
    {
        
        $rolesPermitidos = array();
        
        //ROLES VLAN
        if (true === $this->get('security.context')->isGranted('ROLE_320-1'))
        {
                $rolesPermitidos[] = 'ROLE_320-1'; //index
        }
        if (true === $this->get('security.context')->isGranted('ROLE_320-7'))
        {
                $rolesPermitidos[] = 'ROLE_320-7'; //grid
        }
        if (true === $this->get('security.context')->isGranted('ROLE_320-2'))
        {
                $rolesPermitidos[] = 'ROLE_320-2'; //new
        }
        if (true === $this->get('security.context')->isGranted('ROLE_320-3'))
        {
                $rolesPermitidos[] = 'ROLE_320-3'; //create
        }
        if (true === $this->get('security.context')->isGranted('ROLE_320-8'))
        {
                $rolesPermitidos[] = 'ROLE_320-8'; //delete
        }
        
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("320", "1");
        return $this->render('tecnicoBundle:Vlan:index.html.twig', array(
                                'item'            => $entityItemMenu,
                                'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * @Secure(roles="ROLE_320-7")
     *
     * Documentación para el método 'ajaxGridAction'.
     *
     * Método utilizado para generar el Json de registros de las Vlans de los Clientes
     *
     * @param string nombre Nombre del pe a buscar vlans.
     * @param int vlan Numero de la vlan a buscar.
     * @param int start min de registros de vlans a buscar.
     * @param int limit max de registros de vlans a buscar.
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id':'',
     *                                   'vlan':'',
     *                                   'id_elemento':'',
     *                                   'elemento':'',
     *                                   'fe_creacion':'',
     *                                   'usr_creacion':'',
     *                                   }]
     *                      }]
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
    */
    public function ajaxGridAction()
    {
        $response    = new JsonResponse();
        
        $request     = $this->get('request');
        $session     = $this->get('session');
        
        $cliente     = $session->get('cliente');
        $nombre      = $request->get('nombre');
        $vlan        = $request->get('vlan');
        $start       = $request->get('start');
        $limit       = $request->get('limit');
        
        $objResult   = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                            ->getJsonVlansCliente($cliente['id_persona_empresa_rol'],$nombre,$vlan,$start,$limit);
        
        $response->setContent($objResult);

        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_320-2")
     *
     * Documentación para el método 'newAction'.
     *
     * Método utilizado para cargar la vista de reservar una vlan para un cliente
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
     * @version 1.2 2021-04-26 - Se envían por parámetro los tipos de red MPLS y GPON a la vista.
    */
    public function newAction()
    {
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("320", "1");
        $emGeneral      = $this->get('doctrine')->getManager('telconet_general');
        $arrayListaTipoRed     = array();
        $intIdEmpresa          = $this->get('request')->getSession()->get('idEmpresa');
        $arrayTipoRedDetalles  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('PROD_TIPO_RED',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                $intIdEmpresa);
        foreach($arrayTipoRedDetalles as $arrayItemDet)
        {
            $arrayListaTipoRed[] = array(
                'strValue'    => $arrayItemDet['valor1'],
                'strTipo'     => $arrayItemDet['valor2'],
                'strSelected' => $arrayItemDet['valor3']
            );
        }
        return $this->render('tecnicoBundle:Vlan:new.html.twig',array('item' => $entityItemMenu,'arrayListaTipoRed' => $arrayListaTipoRed));
    }
    
    /**
     * @Secure(roles="ROLE_320-3")
     *
     * Documentación para el método 'createAction'.
     *
     * Método utilizado para reservar una Vlan para un cliente
     *
     * @param int id_elemento Id del Router donde se reservará la vlan
     * @param string nombre_elemento nombre del Router donde se reservará la vlan
     * @param int numero_anillo Anillo de donde se tomará la vlan a reservar
     * @param int vlan_sugerida Numero de la vlan sugerida a reservar
     *
     * @return mixed redirect
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-05-15 Mensaje de Vlan reservada por el sistema
     *                         Mensaje de ERROR por Excepcion no controlada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 26-08-2019 Se agrega lógica para reservar vlans otorgadas por el proyecto mapeo de VRF-VLAN
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 20-11-2019 Se agrega lógica para reservar vlans en caso de que el tipo de red sea GPON.
     */
    public function createAction()
    {
        $request           = $this->get('request');
        $session           = $this->get('session');
        $emComercial       = $this->get('doctrine')->getManager();
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');

        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $idVlanAreservar     = 0;
            $cliente             = $session->get('cliente');
            $idPe                = $request->get('id_elemento');
            $nombrePe            = $request->get('nombre_elemento');
            $numAnillo           = $request->get('numero_anillo');
            $vlanSugerida        = $request->get('vlan_sugerida');
            $intComboVlan        = $request->get('comboVlan');
            $strTipoRed          = $request->get('tipoRed') ? $request->get('tipoRed') : "MPLS";
            $strBandMapeoCliente = $request->get('bandMapeoCliente')?$request->get('bandMapeoCliente'):"N";
            $strCodEmpresa       = $session->get('idEmpresa') ? $session->get('idEmpresa') : '10';
            $strMensajeError     = null;
            //verificar si el tipo de red es GPON
            $booleanTipoRedGpon = false;
            $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'VERIFICAR TIPO RED',
                                                                                            'VERIFICAR_GPON',
                                                                                            $strTipoRed,
                                                                                            '',
                                                                                            '',
                                                                                            '');
            if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
            {
                $booleanTipoRedGpon = true;
            }
            if($idPe>0)
            {
                if($vlanSugerida>0)
                {
                    if($booleanTipoRedGpon)
                    {
                        $objVlanReservada = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array("elementoId"    => $idPe,
                                                                          "detalleNombre" => "VLAN GPON",
                                                                          "detalleValor"  => $vlanSugerida));
                        if(!is_object($objVlanReservada))
                        {
                            //se guarda el detalle de la vlan reservada al pe
                            $objVlanReservada = new InfoDetalleElemento();
                            $objVlanReservada->setElementoId($idPe);
                            $objVlanReservada->setDetalleNombre("VLAN GPON");
                            $objVlanReservada->setDetalleValor($vlanSugerida);
                            $objVlanReservada->setDetalleDescripcion("VLAN PE");
                            $objVlanReservada->setFeCreacion(new \DateTime('now'));
                            $objVlanReservada->setUsrCreacion($session->get('user'));
                            $objVlanReservada->setIpCreacion($request->getClientIp());
                            $objVlanReservada->setEstado("Activo");
                            $emInfraestructura->persist($objVlanReservada);
                            $emInfraestructura->flush();
                        }
                    }
                    else
                    {
                        $objVlanReservada = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array("elementoId"    => $idPe,
                                                                          "detalleNombre" => "VLAN", 
                                                                          "detalleValor"  => $vlanSugerida
                                                                          )
                                                                   );
                    }
                    if($objVlanReservada)
                    {
                        if($objVlanReservada->getEstado()=="Reservada")
                        {
                            $this->get('session')->getFlashBag()->add('error', 
                                                                      'Vlan sugerida '.$vlanSugerida." ya se encuentra reservada, favor corregir."
                                                                     );
                            return $this->redirect($this->generateUrl('vlan_new'));
                        }
                        if($objVlanReservada->getEstado()=="Activo")
                        {
                            $idVlanAreservar = $objVlanReservada->getId();
                            
                            $objVlanReservada->setEstado('Reservada');
                            
                            $emInfraestructura->persist($objVlanReservada);
                            $emInfraestructura->flush();
                            $this->get('session')->getFlashBag()->add('info', 'Se reservo la Vlan sugerida: '.$vlanSugerida);
                        }
                    }
                    else
                    {
                        $strMensajeError = "No existe la vlan $vlanSugerida en la detalle elemento del $nombrePe.";
                    }
                }
                else
                {
                    //Se reserva la vlan configurada para el proyecto Mapeo de VRF - VLAN
                    if($strBandMapeoCliente === "S")
                    {
                        //Se verifica si la vlan ya esta mapeada
                        $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAMETROS PROYECTO SEGMENTACION VLAN',
                                                                     'INFRAESTRUCTURA',
                                                                     'ASIGNAR RECURSOS DE RED',
                                                                     'MAPEO VRF - VLAN Nedetel',
                                                                     '',
                                                                     '',
                                                                     $intComboVlan,
                                                                     '',
                                                                     '',
                                                                     '');

                        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                        {
                            //Se consulta el valor referencial de la vlan nueva
                            $objInfoDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                        ->findOneBy(array("detalleDescripcion" => "VLAN PE",
                                                                                          "detalleNombre"      => "VLAN",
                                                                                          "detalleValor"       => $intComboVlan,
                                                                                          "elementoId"         => $idPe));

                            $objCaracteristicaVlan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array("descripcionCaracteristica" =>"VLAN", "estado" => "Activo"));

                            $objPersonEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($cliente['id_persona_empresa_rol']);

                            if(is_object($objInfoDetalleElemento))
                            {
                                //Si entra por rango especial [42 - 50], se valida si la vlan ya fue reservada
                                $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                    ->findOneBy(array("personaEmpresaRolId" => $objPersonEmpresaRol,
                                                                                      "caracteristicaId"    => $objCaracteristicaVlan,
                                                                                      "valor"               => $objInfoDetalleElemento->getId(),
                                                                                      "estado"              => "Activo"));

                                if($objPersonaEmpresaRol)
                                {
                                    $this->get('session')->getFlashBag()
                                                         ->add('error','La vlan: '.$intComboVlan.'  ya se encuentra reservada en este PE');

                                    return $this->redirect($this->generateUrl('vlan_new'));
                                }

                                $idVlanAreservar = $objInfoDetalleElemento->getId();

                                //Se cambia a estado reservada la vlan
                                $objInfoDetalleElemento->setEstado("Reservada");
                                $emInfraestructura->persist($objInfoDetalleElemento);
                                $emInfraestructura->flush();

                                $this->get('session')->getFlashBag()->add('info', 'La Vlan reservada fue: '
                                                                           .$objInfoDetalleElemento->getDetalleValor());
                            }
                            else
                            {
                                $this->get('session')->getFlashBag()->add('error', 'La vlan: '.$intComboVlan.' no esta registrada,'
                                                                                 . ' favor comunicarse con Sistema.');
                                return $this->redirect($this->generateUrl('vlan_new'));
                            }
                        }
                        else
                        {
                            //Se valida que la vlan a reservar este mapeada previamente
                            $this->get('session')->getFlashBag()
                                                 ->add('error','Para reservar la Vlan: '.$intComboVlan.' primero debe mapearla a una VRF');

                            return $this->redirect($this->generateUrl('vlan_new'));
                        }
                    }
                    else
                    {
                        if($booleanTipoRedGpon)
                        {
                            $arrayParametros = array('intIdElemento' => $idPe,
                                                     'strCodEmpresa' => $strCodEmpresa);
                            $arrayResultado  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                 ->getVlanLibreGpon($arrayParametros);
                            if(is_array($arrayResultado) && isset($arrayResultado['resultado']) && !empty($arrayResultado['resultado']))
                            {
                                $objVlanLibre = $arrayResultado['resultado'];
                            }
                            else if(is_array($arrayResultado) && isset($arrayResultado['error']) && !empty($arrayResultado['error']))
                            {
                                throw new \Exception($arrayResultado['error']);
                            }
                            else
                            {
                                //verificar vlan reservada
                                $arrayVlansReservadas   = array();
                                $arrayVlansRangos       = array();
                                $arrayDetVlanReservada  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findBy(array("elementoId"    => $idPe,
                                                                                   "detalleNombre" => "VLAN GPON",
                                                                                   "estado"        => "Reservada"));
                                foreach($arrayDetVlanReservada as $objVlanReservada)
                                {
                                    $arrayVlansReservadas[] = intval($objVlanReservada->getDetalleValor());
                                }
                                //verificar rango vlans
                                $arrayParametrosRangos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('NUEVA_RED_GPON_TN',
                                                                        'COMERCIAL',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        'CATALOGO_VLANS_DATOS',
                                                                        $strCodEmpresa);
                                if(isset($arrayParametrosRangos) && !empty($arrayParametrosRangos) && is_array($arrayParametrosRangos))
                                {
                                    $intValorVlan   = intval($arrayParametrosRangos['valor1']);
                                    $intVlanMaximo  = intval($arrayParametrosRangos['valor2']);
                                    for( ; $intValorVlan <= $intVlanMaximo; $intValorVlan++)
                                    {
                                        $arrayVlansRangos[] = $intValorVlan;
                                    }
                                }
                                //obtener vlan disponibles
                                $arrayVlansDisponibles = array_diff($arrayVlansRangos, $arrayVlansReservadas);
                                if(is_array($arrayVlansDisponibles) && count($arrayVlansDisponibles) > 0)
                                {
                                    //se guarda el detalle de la vlan reservada al pe
                                    $objVlanLibre = new InfoDetalleElemento();
                                    $objVlanLibre->setElementoId($idPe);
                                    $objVlanLibre->setDetalleNombre("VLAN GPON");
                                    $objVlanLibre->setDetalleValor(reset($arrayVlansDisponibles));
                                    $objVlanLibre->setDetalleDescripcion("VLAN PE");
                                    $objVlanLibre->setFeCreacion(new \DateTime('now'));
                                    $objVlanLibre->setUsrCreacion($session->get('user'));
                                    $objVlanLibre->setIpCreacion($request->getClientIp());
                                    $objVlanLibre->setEstado("Activo");
                                    $emInfraestructura->persist($objVlanLibre);
                                    $emInfraestructura->flush();
                                }
                                else
                                {
                                    throw new \Exception("No existen Vlans libres para la red GPON en el PE");
                                }
                            }
                        }
                        else
                        {      
                            $objVlanLibre = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->findVlanLibre($idPe,$numAnillo);
                        }

                        if($objVlanLibre)
                        {
                            $idVlanAreservar = $objVlanLibre->getId();

                            $objVlanLibre->setEstado('Reservada');

                            $emInfraestructura->persist($objVlanLibre);
                            $emInfraestructura->flush();
                            $this->get('session')->getFlashBag()->add('info', 'La Vlan reservada fue: '.$objVlanLibre->getDetalleValor());
                        }
                        else
                        {
                            $this->get('session')->getFlashBag()->add('error', 'No existen Vlans libres para el Pe '.$nombrePe);
                            return $this->redirect($this->generateUrl('vlan_new'));
                        }
                    }
                }

                if($idVlanAreservar>0)
                {
                    $objCaracteristicaVlan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array("descripcionCaracteristica" =>"VLAN", "estado" => "Activo"));
                    $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->find($cliente['id_persona_empresa_rol']);

                    $objInfoPersonaEmpresaRolCaracVlan = new InfoPersonaEmpresaRolCarac();
                    $objInfoPersonaEmpresaRolCaracVlan->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objInfoPersonaEmpresaRolCaracVlan->setCaracteristicaId($objCaracteristicaVlan);
                    $objInfoPersonaEmpresaRolCaracVlan->setValor($idVlanAreservar);
                    $objInfoPersonaEmpresaRolCaracVlan->setEstado("Activo");
                    $objInfoPersonaEmpresaRolCaracVlan->setFeCreacion(new \DateTime('now'));
                    $objInfoPersonaEmpresaRolCaracVlan->setUsrCreacion($session->get('user'));
                    $objInfoPersonaEmpresaRolCaracVlan->setIpCreacion($request->getClientIp());
                    $emComercial->persist($objInfoPersonaEmpresaRolCaracVlan);
                    $emComercial->flush();

                    $emComercial->getConnection()->commit();
                    $emInfraestructura->getConnection()->commit();

                    return $this->redirect($this->generateUrl('vlan'));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('error', 'Pe no existente para poder reservar Vlan.');
                return $this->redirect($this->generateUrl('vlan_new'));
            }
        }
        catch (\Exception $e) 
        {
            $mensajeError = "Error Vlan: ".$e->getMessage();
            error_log($mensajeError);
            $this->get('session')->getFlashBag()->add('error', $mensajeError);
            
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }

        if(!empty($strMensajeError))
        {
            $this->get('session')->getFlashBag()->add('error', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas. '.
                                                               $strMensajeError);
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.');
        }
        return $this->redirect($this->generateUrl('vlan_new'));

    }

    /**
     * @Secure(roles="ROLE_320-8")
     *
     * Documentación para el método 'ajaxDeleteAction'.
     *
     * Método utilizado para eliminar una Vpn del cliente
     *
     * @param int id Id de la Vlan a eliminar
     *
     * @return Response msg
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Modificado: Duval Medina C <dmedina@telconet.ec>
     * @version 1.1 2016-05-22 Comprobar lo servicios activos vincuados con la VLAN
    */
    public function ajaxDeleteAction()
    {
        $response    = new Response();
        $request     = $this->get('request');
        $emComercial = $this->get('doctrine')->getManager();
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $msg         = "La vlan ha sido eliminada exitosamente.";
        
        $idPersonaEmpresaRolCarac = $request->get('id');
        
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $objServicioProdCaracVlan   = $this->getDoctrine()
                                            ->getManager()
                                            ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                            ->getServiciosByIdVlan($idPersonaEmpresaRolCarac);
        
              if($objServicioProdCaracVlan['status']=='OK')
              {
                  $totalServicios = count($objServicioProdCaracVlan['data']);
                  
                  if($totalServicios>0)
                  {
                      $msg = 'Vlan no puede ser eliminada debido a que tiene '.$totalServicios.' servicios Activos';
                  }
                  else
                  {
                        $objPersonaEmpresaRolCaracVlan = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                ->find($idPersonaEmpresaRolCarac);
                        $objDetalleElementoVlan        = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->find($objPersonaEmpresaRolCaracVlan->getValor());
                        
                        
                        $objPersonaEmpresaRolCaracVlan->setEstado("Eliminado");
                        $emComercial->persist($objPersonaEmpresaRolCaracVlan);
                        
                        $objDetalleElementoVlan->setEstado("Activo");
                        $emInfraestructura->persist($objDetalleElementoVlan);
                        
                        $emComercial->flush();
                        $emComercial->getConnection()->commit();
                        
                        $emInfraestructura->flush();
                        $emInfraestructura->getConnection()->commit();
                  }
              } 
              else
              {
                    $msg = $objServicioProdCaracVlan['data'];
              }
           
        }
        catch (\Exception $e) 
        {
            $msg = "Error Eliminar Vlan: ".$e->getMessage();
            error_log($msg);
            
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }
        
        $response->setContent($msg);
        return $response;  
        
    }
    
    /**
     * 
     * Metodo encargado de obtener la informacion de rango de vlans para procesos de DATACENTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 27-09-2017
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetInformacionReservarVlansDCAction()
    {
        $objRequest     = $this->get('request');
        $strTipoVlan    = $objRequest->get('tipoVlan');
        $emGeneral      = $this->get('doctrine')->getManager('telconet_general');
        $arrayRespuesta = array();
        
        $arrayRangosPermitidos =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('RANGO_VLANS_DC', 
                                                    'TECNICO', 
                                                    '',
                                                    '',
                                                    $strTipoVlan,
                                                    '',
                                                    '',
                                                    '', 
                                                    '', 
                                                    $objRequest->getSession()->get('idEmpresa'));
        
        $arrayRespuesta['minRango'] = 'No definido';
        $arrayRespuesta['maxRango'] = 'No definido';
        
        if(!empty($arrayRangosPermitidos))
        {
            $arrayRespuesta['minRango'] = $arrayRangosPermitidos['valor2'];
            $arrayRespuesta['maxRango'] = $arrayRangosPermitidos['valor3'];
        }
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de obtener la informacion de rango de vlans para productos 
     * CLEAR CHANNEL PUNTO A PUNTO
     * 
     * @author Josue Valencia <arsuarez@telconet.ec>
     * @version 1.0 - 27-09-2017
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetInformacionReservarVlansCHAction()
    {
        $objRequest     = $this->get('request');
        $strTipoVlan    = $objRequest->get('tipoVlan');
        $emGeneral      = $this->get('doctrine')->getManager('telconet_general');
        $arrayRespuesta = array();
        $strEmpresaCod  = $objRequest->getSession()->get('idEmpresa');
        $arrayRangosPermitidos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('RANGO_VLANS_CH',
                                                                      'TECNICO',
                                                                      null,
                                                                      'RANGO_VLANS_CLEAR_CHANNEL',
                                                                      null,
                                                                      null,
                                                                      null,
                                                                      null,
                                                                      null,
                                                                      $strEmpresaCod);
        $arrayRespuesta['minRango'] = 'No definido';
        $arrayRespuesta['maxRango'] = 'No definido';
        
        if(!empty($arrayRangosPermitidos))
        {
            $arrayRespuesta['minRango'] = $arrayRangosPermitidos['valor2'];
            $arrayRespuesta['maxRango'] = $arrayRangosPermitidos['valor3'];
        }
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;
    }

    /**
     * 
     * Metodo encargado de reservar las VLANS ( Lan o Wan ) en un cliente para flujos de DATACENTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 27-09-2017
     * 
     * @author Luis Farro <lfarro@telconet.ec>
     * @version 1.0 - 30-12-2022 Se añade idEmpresa y reserva de Vlan en el proceso de sugerir VLAN desocupadas.
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.1 - 26-04-2023 Se realiza logica para la reserva de VLAN a nivel nacional .
     * 
     * 
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxCrearVlanClienteDCAction()
    {
        $objResponse       = new JsonResponse();
        $strStatus         = 'OK';
        $strMensaje        = 'OK';
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intIdEmpresa      = $objSession->get('idEmpresa');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $strTipoVlan       = $objRequest->get('tipoVlan');
        $intVlanSugerida   = $objRequest->get('vlanSugerida');
        $intPersonaRol     = $objRequest->get('idPersonaEmpresaRol');
        $intIdPe           = $objRequest->get('idPe');
        
        $serviceUtil       = $this->get('schema.Util');
        
        $intIdVlanSugerida = 0;
        
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $arrayPeEncontrados  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PE_DATACENTER',
                                                     'PLANIFICACION',
                                                     null,
                                                     'PE_VLANS',
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     $intIdEmpresa);


            if($intVlanSugerida>0 && !empty($intVlanSugerida))
            {
                
                //Reservar a Nivel Nacional
                foreach($arrayPeEncontrados as $objPePermitido)
                {
                    $objVlanReservaNacional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                            ->findOneBy(array("elementoId"    => $objPePermitido['valor2'],
                                            "detalleNombre" => "VLAN", 
                                            "detalleValor"  => $intVlanSugerida));

                    if(is_object($objVlanReservaNacional))
                    {
                        //Si se encuentra la VLAN reservada se informa al usuario
                        if($objVlanReservaNacional->getEstado()=="Reservada")
                        {
                            $strStatus  = 'ERROR';
                            $strMensaje = 'Vlan sugerida '.$intVlanSugerida.' ya se encuentra reservada, por favor escoja otra';
                        }
                        else if($objVlanReservaNacional->getEstado()=="Ocupado")
                        {
                            $strStatus  = 'ERROR';
                            $strMensaje = 'Vlan sugerida '.$intVlanSugerida.' ya se encuentra ocupado, por favor escoja otra';
                        }
                        else
                        {
                            $arrayVlanDetalleElemento[] = $objVlanReservaNacional->getId();

                            $objVlanReservaNacional->setEstado('Reservada');
                            $emInfraestructura->persist($objVlanReservaNacional);
                            $emInfraestructura->flush();
                            
                        }
                    }
                    else
                    {
                        $strStatus  = 'ERROR';
                        $strMensaje = 'No existen VLAN configurada en el PE, notificar a Sistemas';
                    }
                    
                }
            }
            else
            {
                //Sugerir automaticamente la vlan
                $strReservaVlan='OP_VLAN';
                $objVlanLibre = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findVlanLibreDC($intIdPe,
                                                                        '',
                                                                        $strTipoVlan,
                                                                        $intIdEmpresa,
                                                                        $strReservaVlan
                                                                    );
                if($objVlanLibre)
                {
                    
                    foreach($arrayPeEncontrados as $objPePermitido)
                    {
                        $objVlanReservaNacional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                ->findOneBy(array("elementoId"    => $objPePermitido['valor2'],
                                                "detalleNombre" => "VLAN", 
                                                "detalleValor"  => $objVlanLibre['strDetalleValor']));

                        if(is_object($objVlanReservaNacional))
                        {
                            $arrayVlanDetalleElemento[] = $objVlanReservaNacional->getId();

                            $objVlanReservaNacional->setEstado('Reservada');
                            $emInfraestructura->persist($objVlanReservaNacional);
                            $emInfraestructura->flush();

                        }
                        else
                        {
                            $strStatus  = 'ERROR';
                            $strMensaje = 'No existen VLAN configurada en el PE, notificar a Sistemas';
                            
                        }
                         
                    }
                }
                else
                {    
                    $strStatus  = 'ERROR';
                    $strMensaje = 'No se encuentra Vlans disponibles para el Pe';
                }
            }
            

            //Guardar la VLAN generada o enviada por el usuario
            if(count($arrayVlanDetalleElemento)>0 && $strStatus == 'OK')
            {
                $strDescripcionCaracteristica = 'VLAN_'.$strTipoVlan;

                $objCaracteristicaVlan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" => $strDescripcionCaracteristica, 
                                                                       "estado"                    => "Activo")
                                                                );

                $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->find($intPersonaRol);

                if(is_object($objCaracteristicaVlan) && is_object($objPersonaEmpresaRol))
                {
                    foreach($arrayVlanDetalleElemento as $intIdVlanDetalleElemento)
                    {
                        $objInfoPersonaEmpresaRolCaracVlan = new InfoPersonaEmpresaRolCarac();
                        $objInfoPersonaEmpresaRolCaracVlan->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolCaracVlan->setCaracteristicaId($objCaracteristicaVlan);
                        $objInfoPersonaEmpresaRolCaracVlan->setValor($intIdVlanDetalleElemento);
                        $objInfoPersonaEmpresaRolCaracVlan->setEstado("Activo");
                        $objInfoPersonaEmpresaRolCaracVlan->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolCaracVlan->setUsrCreacion($objSession->get('user'));
                        $objInfoPersonaEmpresaRolCaracVlan->setIpCreacion($objRequest->getClientIp());
                        $emComercial->persist($objInfoPersonaEmpresaRolCaracVlan);
                        $emComercial->flush();
                    }

                    $emComercial->getConnection()->commit();
                    $emInfraestructura->getConnection()->commit();

                    $strMensaje = 'Vlan fue Reservada Correctamente';
                }
            }
        } 
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxCrearVlanClienteDCAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar la Vlan Sugerida para el Cliente, notificar a Sistemas';
        }
        
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->close();
        }
        
        if ($emInfraestructura->getConnection()->isTransactionActive())
        {
            $emInfraestructura->close();
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }

    /**
     * 
     * Metodo encargado de reservar las VLANS ( Lan o Wan ) en un cliente para 
     * flujos de CLEAR CHANNEL PUNTO A PUNTO
     * 
     * @author Josue Valencia <arsuarez@telconet.ec>
     * @version 1.0 - 27-09-2022
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxCrearVlanClienteCHAction()
    {
        $objResponse       = new JsonResponse();
        $strStatus         = 'OK';
        $strMensaje        = 'OK';
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intIdEmpresa      = $objSession->get('idEmpresa');
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $strTipoVlan       = $objRequest->get('tipoVlan');
        $intVlanSugerida   = $objRequest->get('vlanSugerida');
        $intPersonaRol     = $objRequest->get('idPersonaEmpresaRol');
        $intIdPe           = $objRequest->get('idPe');
        
        $serviceUtil       = $this->get('schema.Util');
        
        $intIdVlanSugerida = 0;
        $strRegion = "";
        
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            if($intIdPe!= "" )
            {
                $objInfoElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->find($intIdPe);
                                                
                $objTipoRegionGye = strpos($objInfoElemento->getNombreElemento(), 'gye');
                $objTipoRegionUio = strpos($objInfoElemento->getNombreElemento(), 'uio');

                if($objTipoRegionGye)
                {
                    $strRegion = 'gye';
                }

                if($objTipoRegionUio)
                {
                    $strRegion = 'uio';
                }

                $arrayResponse  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PE_TELCONET',
                                                        'TECNICO',
                                                        null,
                                                        'PE_TELCONET_ASIGNAR',
                                                        null,
                                                        null,
                                                        null,
                                                        null,
                                                        null,
                                                        $intIdEmpresa);
                $arrayPeEncontrados = array();
            
                if(count($arrayResponse)>0)
                {
                    foreach($arrayResponse as $reg)
                    {
                        $arrayValidaPe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                        ->getValidarPeTelco($reg['valor1'],
                                                            $reg['valor3'],
                                                            $reg['valor4']);
                        if($arrayValidaPe['status'] === 'OK')
                        {
                            
                            $objTipoRegionPE = strpos($reg['valor1'], $strRegion);
                            if($objTipoRegionPE)
                            {
                                $arrayPeEncontrados[] = array('nombreElemento'    => $reg['valor1'],
                                                        'valor' => $arrayValidaPe['result']);
                            }
                            
                        }
                    } 
                }

                $objVlanReservada = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy(array("elementoId"    => $intIdPe,
                                                                                "detalleNombre" => "VLAN", 
                                                                                "detalleValor"  => $intVlanSugerida
                                                                                )
                                                                        );

                if($intVlanSugerida>0 && !empty($intVlanSugerida))
                {
                    if(is_object($objVlanReservada))
                    {
                        //Si se encuentra la VLAN reservada se informa al usuario
                        if($objVlanReservada->getEstado()=="Reservada")
                        {
                            $strStatus  = 'ERROR';
                            $strMensaje = 'Vlan sugerida '.$intVlanSugerida.' ya se encuentra reservada, por favor escoja otra';
                        }
                        else if($objVlanReservada->getEstado()=="Ocupado")
                        {
                            $strStatus  = 'ERROR';
                            $strMensaje = 'Vlan sugerida '.$intVlanSugerida.' ya se encuentra ocupado, por favor escoja otra';
                        }
                        else//Activa
                        {
                            $intIdVlanSugerida = $objVlanReservada->getId();

                            
                            $objVlanReservada->setEstado('Reservada');
                            $emInfraestructura->persist($objVlanReservada);
                            $emInfraestructura->flush();
                            
                             //Reservar a Nivel Nacional
                             foreach($arrayPeEncontrados as $objPePermitido)
                             {
                                
                                 $objVlanReservaNacional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findOneBy(array("elementoId"    => $objPePermitido['valor'],
                                                         "detalleNombre" => "VLAN", 
                                                         "detalleValor"  => $intVlanSugerida));
                                 
                                 if(is_object($objVlanReservaNacional) && $objPePermitido['nombreElemento'] != $objInfoElemento->getNombreElemento())
                                 {
                                     $objVlanReservaNacional->setEstado('Reservada');
                                     $emInfraestructura->persist($objVlanReservaNacional);
                                     $emInfraestructura->flush();
                                 }
                             }
                            
                        }
                    }
                    else
                    {
                        $strStatus  = 'ERROR';
                        $strMensaje = 'No existen VLAN configurada en el PE, notificar a Sistemas';
                    }
                }
                else
                {
                    //Sugerir automaticamente la vlan CLEAR CHANNEL PUNTO A PUNTO
                    $strReservaVlan='VLAN_CCPP';
                    $objVlanLibre = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->findVlanLibre($intIdPe,
                                                                                                                        '',
                                                                                                                        $strTipoVlan,
                                                                                                                        $intIdEmpresa,
                                                                                                                        $strReservaVlan
                                                                                                                        );
                    if(is_object($objVlanLibre))
                    {
                        $intVlanSugerida =  $objVlanLibre->getDetalleValor();
                        $intIdVlanSugerida = $objVlanLibre->getId();

                        $objVlanLibre->setEstado('Reservada');
                        $emInfraestructura->persist($objVlanLibre);
                        $emInfraestructura->flush();

                        //Reservar a Nivel Nacional
                        foreach($arrayPeEncontrados as $objPePermitido)
                        {
                           
                            $objVlanReservaNacional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                    ->findOneBy(array("elementoId"    => $objPePermitido['valor'],
                                                    "detalleNombre" => "VLAN", 
                                                    "detalleValor"  => $objVlanLibre->getDetalleValor()));
                            
                            if(is_object($objVlanReservaNacional) && $objPePermitido['nombreElemento'] != $objInfoElemento->getNombreElemento())
                            {
                                $objVlanReservaNacional->setEstado('Reservada');
                                $emInfraestructura->persist($objVlanReservaNacional);
                                $emInfraestructura->flush();
                            }
                        }
                    }
                    else
                    {    
                        $strStatus  = 'ERROR';
                        $strMensaje = 'No se encuentra Vlans disponibles para el Pe';
                    }
                }
                
                //GUardar la VLAN generada o enviada por el usuario
                if($intIdVlanSugerida != 0 && $strStatus == 'OK' && $intVlanSugerida !='')
                {
                    $strDescripcionCaracteristica = 'VLAN_'.$strTipoVlan;

                    $objCaracteristicaVlan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => $strDescripcionCaracteristica, 
                                                                        "estado"                    => "Activo")
                                                                    );

                    $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->find($intPersonaRol);

                    if(is_object($objCaracteristicaVlan) && is_object($objPersonaEmpresaRol))
                    {
                        $objInfoPersonaEmpresaRolCaracVlan = new InfoPersonaEmpresaRolCarac();
                        $objInfoPersonaEmpresaRolCaracVlan->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolCaracVlan->setCaracteristicaId($objCaracteristicaVlan);
                        $objInfoPersonaEmpresaRolCaracVlan->setValor($intIdVlanSugerida);
                        $objInfoPersonaEmpresaRolCaracVlan->setEstado("Activo");
                        $objInfoPersonaEmpresaRolCaracVlan->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolCaracVlan->setUsrCreacion($objSession->get('user'));
                        $objInfoPersonaEmpresaRolCaracVlan->setIpCreacion($objRequest->getClientIp());
                        $emComercial->persist($objInfoPersonaEmpresaRolCaracVlan);
                        $emComercial->flush();

                        foreach($arrayPeEncontrados as $objPePermitido)
                        {
                           
                            $objVlanAsociar = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                    ->findOneBy(array("elementoId"    => $objPePermitido['valor'],
                                                    "detalleNombre" => "VLAN", 
                                                    "detalleValor"  => $intVlanSugerida));
                            
                            if(is_object($objVlanAsociar) 
                               && $objPePermitido['nombreElemento'] != $objInfoElemento->getNombreElemento())
                            {
                                
                                $objInfoPersonaEmpresaRolCaracVlanAsociar = new InfoPersonaEmpresaRolCarac();
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setCaracteristicaId($objCaracteristicaVlan);
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setValor($objVlanAsociar->getId());
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setEstado("Activo");
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setFeCreacion(new \DateTime('now'));
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setUsrCreacion($objSession->get('user'));
                                $objInfoPersonaEmpresaRolCaracVlanAsociar->setIpCreacion($objRequest->getClientIp());
                                $emComercial->persist($objInfoPersonaEmpresaRolCaracVlanAsociar);
                                $emComercial->flush();
                            }
                        }


                        $emComercial->getConnection()->commit();
                        $emInfraestructura->getConnection()->commit();

                        $strMensaje = 'Vlan fue Reservada Correctamente';
                    }
                }
            }
            else
            {
                $strStatus  = 'ERROR';
                $strMensaje = 'Debe seleccionar previamente un PE de TN';

            }
        } 
        catch (\Exception $ex) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxCrearVlanClienteCHAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Guardar la Vlan Sugerida para el Cliente, notificar a Sistemas';
        }
        
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->close();
        }
        
        if ($emInfraestructura->getConnection()->isTransactionActive())
        {
            $emInfraestructura->close();
        }
        
        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));
        
        return $objResponse;
    }
    
}