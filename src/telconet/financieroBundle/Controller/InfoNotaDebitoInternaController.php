<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Form\InfoDocumentoFinancieroCabType;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;

use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\financieroBundle\Service\InfoDevolucionService;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;
/**
 * InfoNotaDebitoInterna controller.
 *
 */
class InfoNotaDebitoInternaController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Documentación para el método 'indexAction'.
     *
     * Por medio de la funcion se podra cargar la pantalla de notas de debito interna
     * realizadas a un punto cliente (login)
     *
     * @return twig Html para la presentacion de las notas de debito internas.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 10-07-2014
     */
    /**
     * @Secure(roles="ROLE_262-1")
    */
    public function indexAction()
    {
        return $this->render('financieroBundle:InfoNotaDebitoInterna:index.html.twig');
    }

    /**
     * Documentación para el método 'ajaxListarNotasDebitoInternaAction'.
     *
     * Por medio de la funcion obtenemos el listado de las notas de debito internas
     * realizadas a un punto cliente (login)
     *
     * @return json Listado de Notas de debito Internas.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 10-07-2014
     * 
     * Se agrega a la presentacion del listado el valor total de la nota de debito
     * Se realiza la reestriccion de estado Eliminado unicamente
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 03-08-2016
     */
    public function ajaxListarNotasDebitoInternaAction()
    {
        $em             = $this->get('doctrine')->getManager('telconet_financiero');
        $em_comercial   = $this->get('doctrine')->getManager('telconet');
        
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $intPtocliente     = $objSession->get('ptoCliente');               
        $intIdEmpresa      = $objSession->get('idEmpresa');               
        $intIdOficina      = $objSession->get('idOficina');               
        $strEstado         = 'Activo';
        $strFechaDesde     = explode('T',$objRequest->get("fechaDesde"));
        $strFechaHasta     = explode('T',$objRequest->get("fechaHasta"));
        $strEstado         = $objRequest->get("estado");
        $intLimit          = $objRequest->get("limit");
        $intPage           = $objRequest->get("page");
        $intStart          = $objRequest->get("start");

        $strTipoPersonal       = 'Otros';
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strUsrCreacion        = $objSession->get('user');
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $em_comercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        $strEstado = 'Eliminado';
                
        if($intPtocliente)
        {
            $punto = $intPtocliente['id'];
        }
        else
        {
            $punto = "";
        }
            
        $arrayParametros["estado"]               = $strEstado;
        $arrayParametros["idEmpresa"]            = $intIdEmpresa;
        $arrayParametros["limit"]                = $intLimit;
        $arrayParametros["page"]                 = $intPage;
        $arrayParametros["start"]                = $intStart;
        $arrayParametros["punto"]                = $punto;
        $arrayParametros["idOficina"]            = $intIdOficina;
        $arrayParametros["codigoTipoDocumento"]  = "NDI";
        $arrayParametros['strPrefijoEmpresa']    = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']      = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol']= $intIdPersonEmpresaRol;
        if ((!$strFechaDesde[0])&&(!$strFechaHasta[0]))
        {
            $arrayParametros["feDesde"] = "";
            $arrayParametros["feHasta"] = "";
            
            //Modificar repositorio
            $arrayResultado  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findDevolucionPorCriterios($arrayParametros);
            $objDatos      = $arrayResultado['registros'];
            $intTotal      = $arrayResultado['total'];
        }
        else
        {
            $arrayParametros["feDesde"] = $strFechaDesde[0];
            $arrayParametros["feHasta"] = $strFechaHasta[0];
            
            //Modificar repositorio
            $arrayResultado  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findDevolucionPorCriterios($arrayParametros);
            $objDatos      = $arrayResultado['registros'];
            $intTotal      = $arrayResultado['total'];
        }

        $i=1;
        foreach ($objDatos as $datos):
            if($i % 2==0)
                    $clase='k-alt';
            else
                    $clase='';

            $urlVer  = $this->generateUrl('infodocumentonotadebitointerna_show', array('id' => $datos->getId()));
            $linkVer = $urlVer;
            
            
            $pto_cliente = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($datos->getPuntoId());
            $persona     = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                        ->find($pto_cliente->getPersonaEmpresaRolId()->getId());
            
            if($persona->getPersonaId()->getNombres()!="" && $persona->getPersonaId()->getApellidos()!="")
            {
                $informacion_cliente    = $persona->getPersonaId()->getNombres()." ".$persona->getPersonaId()->getApellidos();
            }
            
            if($persona->getPersonaId()->getRazonSocial()!="")
            {
                $informacion_cliente    = $persona->getPersonaId()->getRazonSocial();
            }
            
            if($datos->getEsAutomatica()=="S")
            {
                $automatica = "Si";
            }
            else
            {
                $automatica = "No";
            }
                
            $arrayDatos[]= array(
                                'Numerofacturasri'  => $datos->getNumeroFacturaSri(),
                                'Punto'             => $pto_cliente->getLogin(),
                                'Cliente'           => $informacion_cliente,
                                'Esautomatica'      => $automatica,
                                'Estado'            => $datos->getEstadoImpresionFact(),
                                'Fecreacion'        => strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
                                'linkVer'           => $linkVer,
                                'linkEliminar'      => "",
                                'clase'             => $clase,
                                'boton'             => "",
                                'totalNotaDebito'   => $datos->getValorTotal(),
                                'id'                => $datos->getId(),
                             );              
            $i++;     
        endforeach;

        $objResponse = new Response(json_encode(array('total' => $intTotal, 'documentos' => $arrayDatos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para el método 'showAction'.
     *
     * Por medio de la funcion podemos obtener la informacion de las notas de debito
     *
     * @param integer $id
     * @return twig Html para visualizar la informacion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * Se verifica que el documento posea la caracteristica de CHEQUE_PROTESTADO asociado
     * @version 1.1 23-06-2016
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 10-07-2014
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.2 28-04-2020 - Se agregan detalles de las características del documento (NDI).
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.3 02-06-2020 - Se realizan cambios para que se visualicen un mejor detalle de las características y los
     * numero_factura_Sri en la columna de valores de características.
     */
    public function showAction($id)
    {
        $em     = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral   = $this->getDoctrine()->getManager("telconet_general");
        
        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) 
        {
            throw $this->createNotFoundException('Unable to find Nota debito interna entity.');
        }

        $deleteForm     = $this->createDeleteForm($id);
        $oficina        = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        $em_comercial   = $this->getDoctrine()->getManager("telconet");
        $pto_cliente    = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $persona        = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($pto_cliente->getPersonaEmpresaRolId()->getId());
        
        $informacion_persona['puntoId']=$pto_cliente->getLogin();
        
        if($persona->getPersonaId()->getNombres()!="" && $persona->getPersonaId()->getApellidos()!="")
            $informacion_persona['cliente'] = $persona->getPersonaId()->getNombres()." ".$persona->getPersonaId()->getApellidos();
        
        if($persona->getPersonaId()->getRepresentanteLegal()!="")
            $informacion_persona['cliente'] = $persona->getPersonaId()->getRepresentanteLegal();
            
        if($persona->getPersonaId()->getRazonSocial()!="")
            $informacion_persona['cliente'] = $persona->getPersonaId()->getRazonSocial();
    
        $rolesPermitidos = array();
        
        //Se verifica si el documento posee caracteristicas asociada 
        $arrayCaracteristicas = array();
        $arrayObjInfoDocumentoCaracteristica = $em->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                  ->findBy(array('documentoId' => $entity, 
                                                                 'estado'      => 'Activo'));        
        
        if(!empty($arrayObjInfoDocumentoCaracteristica))
        {
            foreach ($arrayObjInfoDocumentoCaracteristica as $objInfoDocumentoCaracteristica):
                if(is_object($objInfoDocumentoCaracteristica))
                {            
                    $objAdmiCaracteristica  = $em_comercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->find($objInfoDocumentoCaracteristica->getCaracteristicaId());
                    if(is_object($objAdmiCaracteristica))
                    {
                        $arrayCaracteristica = array();
                        $strDetalleCarac    = $objAdmiCaracteristica->getDetalleCaracteristica();
                        $strDescricionCarac = $objAdmiCaracteristica->getDescripcionCaracteristica();
                        if(!empty($strDetalleCarac))
                        {
                            $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDetalleCaracteristica();
                        }
                        else
                        {
                            $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDescripcionCaracteristica();
                        }
                        if($strDescricionCarac === 'ID_REFERENCIA_NCI')
                        {
                            if(is_numeric($objInfoDocumentoCaracteristica->getValor())) 
                            {
                                $objInfoDocumentoFinancieroCabNCI = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                       ->findOneById($objInfoDocumentoCaracteristica->getValor());

                                if( is_object($objInfoDocumentoFinancieroCabNCI) )
                                {
                                    $arrayCaracteristica['strValor'] = $objInfoDocumentoFinancieroCabNCI->getNumeroFacturaSri();
                                }
                                else
                                {
                                    $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                                }
                            }
                            else
                            {
                                $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                            }

                        }
                        else
                        {
                            $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                        }

                        $arrayCaracteristicas[] = $arrayCaracteristica;
                    }
                }
            endforeach;            
        }
        
        $entityHistorial = $em->getRepository('schemaBundle:InfoDocumentoHistorial')
                              ->findBy(array('documentoId' => $entity->getId()),
                                       array('feCreacion' => 'asc'));
        $arrayHistorial = array();
        if($entityHistorial)
        {
            $intIndice = 0;
            foreach($entityHistorial as $objHistorial)
            {

                if($objHistorial->getMotivoId() != null)
                {
                    $entityMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($objHistorial->getMotivoId());

                    if($entityMotivo)
                    {
                        $strNombreMotivo = $entityMotivo->getNombreMotivo();
                    }
                    else
                    {
                        $strNombreMotivo = "";
                    }
                }
                else
                {
                    $strNombreMotivo = "";
                }
                $arrayHistorial[$intIndice]['motivo']       = $strNombreMotivo;
                $arrayHistorial[$intIndice]['estado']       = $objHistorial->getEstado();
                $arrayHistorial[$intIndice]['fe_creacion']  = strval(date_format($objHistorial->getFeCreacion(), "d/m/Y G:i"));
                $arrayHistorial[$intIndice]['usr_creacion'] = $objHistorial->getUsrCreacion();
                $arrayHistorial[$intIndice]['observacion']  = $objHistorial->getObservacion();

                $intIndice++;
            }
        }

        
        return $this->render('financieroBundle:InfoNotaDebitoInterna:show.html.twig', array(
            'entity'                    => $entity,
            'delete_form'               => $deleteForm->createView(),
            'info_cliente'              => $informacion_persona,
            'oficina'                   => $oficina->getNombreOficina(),
            'rolesPermitidos'           => $rolesPermitidos,
            'historial'                 => $arrayHistorial,
            'arrayCaracteristicas'      => $arrayCaracteristicas,
            'intCountCaracteristicas'   => count($arrayCaracteristicas)
            ));
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Documentación para el método 'newNotaDebitoInternaAction'.
     *
     * Por medio de la funcion cargamos la pantalla para el ingreso de informacion
     * relacionada a la nota de debito interna asociado al punto cliente
     *
     * @return twig Html para visualizar la informacion ingresada
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 10-07-2014
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 07-09-2016 - Se agrega el estado de 'Cerrado' al momento que valida que pagos se deben mostrar en el formulario de las NDI
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 19-09-2016 - Se agrega los estados 'Asignado','Activo' al momento que valida los pagos o anticipos que deben mostrarse
     * en el formulario de las NDI    
     * Obtengo el Parametro que me permite verificar si la empresa en Sesion tiene permitido realizar NDI por un valor menor al valor del Anticipo     
     */
    /**
     * @Secure(roles="ROLE_262-2")
    */
    public function newAction()
    {
        $entity = new InfoDocumentoFinancieroCab();
        $form   = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        
        //informacion del pto cliente
        $request    =$this->get('request');
        $session    =$request->getSession();
        $cliente    =$session->get('cliente');
        $ptocliente =$session->get('ptoCliente');
        
        //debemos obtener la empresa ya que la multa del cheque protestado no aplica a MD
        $strPrefijoEmpresa  = $session->get('prefijoEmpresa');
        
        $parametros=array(
        'entity' => $entity,
        'form'   => $form->createView(),
        );
        
        if($ptocliente)
        {
            $parametros['punto_id']=$ptocliente;
            $parametros['cliente']=$cliente;
            
            $em = $this->getDoctrine()->getManager("telconet_financiero");  
            $estados=array('Anulado','Anulada','Asignado','Activo'); 
            $listadoPagos=$em->getRepository('schemaBundle:InfoPagoDet')->listarDetallesDePagoPorPuntoNotIn($ptocliente['id'],$estados);
            foreach($listadoPagos as $pago){
                $objNotaDebitoDet=$em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                    ->findNotasDeDebitoPorPagoDetIdPorEstados(
                        $pago['id'],
                        array('Activo','Activa','Pendiente', 'Cerrado')
                );
                if(!$objNotaDebitoDet){
                    $pagos[]=$pago;
                }
            }
        }

        if(isset($pagos))
            $parametros['listadosPagos']=$pagos;
        else
            $parametros['listadosPagos']="";
        //busqueda del documento
        $em_general = $this->getDoctrine()->getManager();
        
        //Listado de motivos para la nota de debito interna cambiar 
        $listadoMotivos = $em_general->getRepository('schemaBundle:AdmiMotivo')->findMotivosPorModuloPorItemMenuPorAccion(
            'nota_de_debito',
            '',
            'devolucion');
        
        $parametros['listadoMotivos']   =$listadoMotivos;
        $parametros['strPrefijoEmpresa']=$strPrefijoEmpresa;
        
        /* Obtengo el Parametro que me permite verificar si la empresa en Sesion tiene permitido realizar NDI
           por un valor menor al valor del Anticipo */    
        $arrayParametroDet = array();                
        $arrayParametroDet = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne("CREACION NDI POR MENOR VALOR AL ANTICIPO", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
        if(is_array($arrayParametroDet))
        {
            $parametros['strPermiteEdicionNdi'] = $arrayParametroDet["valor2"];
        }
        else
        {
            $parametros['strPermiteEdicionNdi'] = "";
        }
        
        return $this->render('financieroBundle:InfoNotaDebitoInterna:new.html.twig', $parametros);
    }

    /**
     * Documentación para el método 'createNotaDebitoInternoAction'.
     *
     * Por medio de la funcion podemos guardar la informacion correspondiente a la nota de debito interno
     *
     * @param Request $request
     * @return twig Html para el ingreso de informacion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 10-07-2014
     *
     * Se agrega el parametros nuevo requerido para la llamada del proceso de contabilizacion
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 03-08-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 07-09-2016 - Se añade try/catch para controlar la exception envía por el método 'generarDevolucion'
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 26-09-2016 - Se agrega PREFIJO_EMPRESA al arreglo de parametros.
     * Obtengo el Parametro strPermiteEdicionNdi que me permite verificar si la empresa en Sesion tiene permitido realizar NDI 
     * por un valor menor al valor del Anticipo. Envio valor al arreglo de parametros.
     * 
     * Se inicializa $objParametros y se envía como parámetro a la función contabilizarDocumentosNDI()
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.4 07-11-2019
     * @since 1.3
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.5 19-10-2020 Se agrega validación para verificar existencia de detalles de estado de cuenta asociados al pago relacionado 
     *                         y su cambio a estado Pendiente.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.6 02-08-2021 Se agrega envío de parámetro para seteo de nuevo estado.
     */
    /**
     * @Secure(roles="ROLE_262-3")
    */
    public function createAction(Request $request)
    {
        $emComercial                = $this->getDoctrine()->getManager();
        $emFinanciero               = $this->getDoctrine()->getManager("telconet_financiero");
        $strMsnErrorContabilidad    = "";
        $serviceUtil                = $this->get('schema.Util');
        $strIpSession               = $request->getClientIp();
        
        $entity  = new InfoDocumentoFinancieroCab();
        $form    = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $form->bind($request);

        $informacionGrid = $request->get('listado_informacion');
        $informacionGrid = json_decode($informacionGrid);
        
        //informacion del pto cliente
        $session    = $request->getSession();
        $cliente    = $session->get('cliente');
        $ptocliente = $session->get('ptoCliente');
        $empresa_id = $session->get('idEmpresa');
        $oficina_id = $session->get('idOficina');
        $user       = $session->get('user');
        
        $punto_id   = $ptocliente['id'];
        $estado     = "Activo";
        
        //Se obtiene el prefijo de la empresa para el proceso
        $strPrefijoEmpresa  = $session->get('prefijoEmpresa');
        
        /* Obtengo el Parametro que me permite verificar si la empresa en Sesion tiene permitido realizar NDI
           por un valor menor al valor del Anticipo */    
        $arrayParametroNdi = array();                
        $arrayParametroNdi = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getOne("CREACION NDI POR MENOR VALOR AL ANTICIPO", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
        if(is_array($arrayParametroNdi))
        {
            $strPermiteEdicionNdi = $arrayParametroNdi["valor2"];
        }
        else
        {
            $strPermiteEdicionNdi = "";
        }
        if($punto_id)
        {
            try
            {
                $arrayParametrosDevolucion                         = array();
                $arrayParametrosDevolucion['empresa_id']           = $empresa_id;
                $arrayParametrosDevolucion['oficina_id']           = $oficina_id;
                $arrayParametrosDevolucion['codigoTipoDocumento']  = "NDI";
                $arrayParametrosDevolucion['informacionGrid']      = $informacionGrid;
                $arrayParametrosDevolucion['user']                 = $user;
                $arrayParametrosDevolucion['punto_id']             = $punto_id;
                $arrayParametrosDevolucion['estado']               = $estado;
                $arrayParametrosDevolucion['prefijoEmpresa']       = $strPrefijoEmpresa;
                $arrayParametrosDevolucion['strPermiteEdicionNdi'] = $strPermiteEdicionNdi;

                $devolucion = $this->get('financiero.InfoDevolucion'); 
                
                //Retorna el id del documento creado, la misma funcion sirve para DEV o para NDI
                $entityNotaDebitoInterna = $devolucion->generarDevolucion($arrayParametrosDevolucion);               
                
                //En caso de que la entidad no se cree, se redirecciona al new, caso contrario al show
                if($entityNotaDebitoInterna)
                {                
                    //Contabilizacion de documento NDI
                    $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");

                    //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
                    if($arrayParametroDet["valor2"]=="S")
                    {
                        $objParametros['serviceUtil']             = $this->get('schema.Util');
                        $arrayContabilidad["empresaCod"]          = $empresa_id;
                        $arrayContabilidad["prefijo"]             = $strPrefijoEmpresa;
                        $arrayContabilidad["codigoTipoDocumento"] = "NDI";
                        $arrayContabilidad["tipoProceso"]         = "INDIVIDUAL";
                        $arrayContabilidad["idDocumento"]         = $entityNotaDebitoInterna->getId();
                        $arrayContabilidad["fechaProceso"]        = null;
                        
                        $strMsnErrorContabilidad=$emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                              ->contabilizarDocumentosNDI($arrayContabilidad, $objParametros);                
                    }

                    if($strPrefijoEmpresa == "TN")
                    {
                        $floatTotalNdi  = 0;
                        $objInfoNdiDet  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                       ->findOneBy(array(  "documentoId"  => $entityNotaDebitoInterna->getId()));
                        $strNuevoEstado = 'Pendiente';
                        if(is_object($objInfoNdiDet))
                        {
                            $intIdPagoDet =  $objInfoNdiDet->getPagoDetId();
                            
                            $objInfoPagoDet = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                           ->find($intIdPagoDet); 
                            if(is_object($objInfoPagoDet))
                            {
                                $intIdPagoCab = $objInfoPagoDet->getPagoId()->getId();
                                
                                $objInfoPagoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                               ->find($intIdPagoCab);
                                if(is_object($objInfoPagoCab) )
                                {
                                    $intIdPagoAutomaticoDet = $objInfoPagoCab->getDetallePagoAutomaticoId();
                                    
                                    if(!isset($intIdPagoAutomaticoDet))
                                    {
                                        $intIdPagoAutomaticoDet = $objInfoPagoDet->getReferenciaDetPagoAutId();
                                        $strNuevoEstado = 'Eliminado';
                                    }
                                    
                                    if(isset($intIdPagoAutomaticoDet))
                                    {
                                        $objInfoPagoAutomaticoDet = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                                 ->find($intIdPagoAutomaticoDet);

                                        if(is_object($objInfoPagoAutomaticoDet))
                                        {
                                            $floatTotalNdi =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                           ->getTotalNDI($intIdPagoAutomaticoDet);
                                            $floatTotalPag =  $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                                           ->getTotalPagosAnt($intIdPagoAutomaticoDet);
                                            if( isset($floatTotalNdi) && $floatTotalNdi === floatval($objInfoPagoAutomaticoDet->getMonto()) || 
                                                isset($floatTotalPag) && $floatTotalPag === floatval($objInfoPagoAutomaticoDet->getMonto()))
                                            {
                                                $arrayParametros   = [];
                                                $arrayParametros['intIdPagoAutomaticoDet']  = $intIdPagoAutomaticoDet;
                                                $arrayParametros['strUsrCreacion']          = $user;
                                                $arrayParametros['strNuevoEstado']          = $strNuevoEstado;
                                                $devolucion->actualizaDetEstadoCta($arrayParametros) ;
                                            }
                                        }
                                        $arrayProcesados = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                            ->findBy(array('pagoAutomaticoId' => $objInfoPagoAutomaticoDet->getPagoAutomaticoId(), 
                                                                           'estado'           => array("Procesado")));

                                        if(count($arrayProcesados)===0)
                                        {
                                            $intIdPagoAutomaticoCab = $objInfoPagoAutomaticoDet->getPagoAutomaticoId();
                                            $arrayParametros   = [];
                                            $arrayParametros['intIdPagoAutomaticoCab']  = $intIdPagoAutomaticoCab;
                                            $arrayParametros['strUsrCreacion']          = $user;
                                            $arrayParametros['strNuevoEstado']          = $strNuevoEstado;
                                            $devolucion->actualizaCabEstadoCta($arrayParametros) ;                                            
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    return $this->redirect($this->generateUrl('infodocumentonotadebitointerna_show', 
                                           array('id' => $entityNotaDebitoInterna->getId())));
                }//($entityNotaDebitoInterna)
                else
                {
                    return $this->redirect($this->generateUrl('infodocumentonotadebitointerna_new'));
                }
            }
            catch(\Exception $ex)
            {
                $serviceUtil->insertError('Telcos+', 'createAction', $ex->getMessage(), $user, $strIpSession);
            }//try/catch
        }//($punto_id)
        
        return $this->redirect($this->generateUrl('infodocumentonotadebitointerna_new'));
    }
    
    
    public function estadosAction()
    {
        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');

        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	 
    /**
     * Documentación para el método 'detalleNotaDebitoInternaAction'.
     *
     * Por medio de la funcion podemos obtener el detalle de la nota de debito interna
     *
     * @return json Listado detalle devolucion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function detalleNotaDebitoInternaAction()
    {
        $request = $this->getRequest();
        $facturaid=$request->get('facturaid');
        
        $em = $this->get('doctrine')->getManager('telconet_financiero');    
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
            $detalle_orden_l[] = array("motivo"=>"","observacion"=>"","valor"=>"");
        }else{
            $em_comercial = $this->get('doctrine')->getManager('telconet');    
            $detalle_orden_l = array();
        
            foreach($resultado as $factdet)
            {
                $informacion=$em_comercial->getRepository('schemaBundle:AdmiMotivo')->find($factdet->getMotivoId());
                $pago_det=$em->getRepository('schemaBundle:InfoPagoDet')->find($factdet->getPagoDetId());
                $tecn['id'] = $factdet->getId();            
                $tecn['motivo'] = $informacion->getNombreMotivo();
                $tecn['observacion'] = $factdet->getObservacionesFacturaDetalle();
                $tecn['valor'] = $factdet->getCantidad();
                $tecn['valor_total'] = $factdet->getPrecioVentaFacproDetalle();
                $tecn['numero_pago'] = $pago_det->getPagoId()->getNumeroPago();
                $detalle_orden_l[] = $tecn;
            }
        }
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
}
?>
