<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Form\PreClienteType;
use telconet\schemaBundle\Form\ConvertirType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
//
use telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago;
use telconet\schemaBundle\Form\InfoPersonaEmpFormaPagoType;
use telconet\schemaBundle\Form\InfoPersonaEmpFormaPagoEditType;
use telconet\comercialBundle\Service\ComercialCrmService;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * PreCliente controller.
 *
 */
class PreClienteController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_6-1")
     * 
     * @author Desarrolo Inicial
     * @version 1.0
     * 
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1  2016-05-28 Remosion de referencias no utilizadas y lineas de codigo comentadas
     *  
    */ 
    public function indexAction()
    {
		$request  = $this->get('request');
		$session  = $request->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("6", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        
        return $this->render('comercialBundle:precliente:index.html.twig', array(
            'item' => $entityItemMenu
		));
        
    }    

    /**
     * Documentación para el método 'getEmpleadosAction'.
     *
     * Retorna los vendedores de acuerdo a los criterios ingresados
     *
     * @return objResponse 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 25-01-2019
     *
     */ 
    public function getEmpleadosAction()
    {
        $objPeticion            = $this->getRequest();
        $objSesion              = $objPeticion->getSession();
        $strPrefijoEmpresa      = $objSesion->get('prefijoEmpresa');
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $intIdPersonaEmpresaRol = $objSesion->get('idPersonaEmpresaRol');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $strUsuarioSession      = $objSesion->get('user');
        $strIpCreacion          = $objPeticion->getClientIp();
        $arrayVendedores        = array();
        $strTipoPersonal        = 'Otros';

        $arrayParametros['EMPRESA'] = $strCodEmpresa;
        $arrayParametros['LIMIT']   = $objPeticion->get("limit");
        $arrayParametros['START']   = $objPeticion->get("start");

        try
        {
            if( $strPrefijoEmpresa == 'TN' )
            {
                /**
                 * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
                 */
                $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsuarioSession);
                if( !empty($arrayResultadoCaracteristicas) )
                {
                    $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                    $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
                }
                $arrayParametros                        = array();
                $arrayParametros['usuario']             = $intIdPersonaEmpresaRol;
                $arrayParametros['empresa']             = $strCodEmpresa;
                $arrayParametros['estadoActivo']        = 'Activo';
                $arrayParametros['caracteristicaCargo'] = 'CARGO_GRUPO_ROLES_PERSONAL';
                $arrayParametros['nombreArea']          = 'Comercial';
                $arrayParametros['strTipoRol']          = array('Empleado', 'Personal Externo');
    
                /**
                 * BLOQUE QUE BUSCA LOS ROLES NO PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
                 */
                $arrayRolesNoIncluidos = array();
                $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                'strValorRetornar'  => 'descripcion',
                                                'strNombreProceso'  => 'JEFES',
                                                'strNombreModulo'   => 'COMERCIAL',
                                                'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                'strUsrCreacion'    => $strUsuarioSession,
                                                'strIpCreacion'     => $strIpCreacion );
    
                $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);
    
                if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                    {
                        $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                    }//foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
    
                    $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
                }//( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
    
                /**
                 * BLOQUE QUE BUSCA LOS ROLES PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
                 */
                $arrayRolesIncluidos                       = array();
                $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';
    
                $arrayResultadosRolesIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);
    
                if( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
                    {
                        $arrayRolesIncluidos[] = $strRolIncluido;
                    }//foreach( $arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido )
    
                    $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
                }//( isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']) )
    
                /**
                 * SE VALIDA QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
                 */
                $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                      'strValorRetornar'  => 'valor1',
                                                      'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                      'strNombreModulo'   => 'COMERCIAL',
                                                      'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                      'strValor2Detalle'  => 'COMERCIAL',
                                                      'strUsrCreacion'    => $strUsuarioSession,
                                                      'strIpCreacion'     => $strIpCreacion);
    
                $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);
    
                if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                {
                    $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
    
                /**
                 * SE OBTIENE EL CARGO DE VENDEDOR DEL PARAMETRO 'GRUPO_ROLES_PERSONAL'
                 */
                $arrayParametrosCargoVendedor = array('strCodEmpresa'     => $strCodEmpresa,
                                                      'strValorRetornar'  => 'id',
                                                      'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                      'strNombreModulo'   => 'COMERCIAL',
                                                      'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                      'strValor3Detalle'  => 'VENDEDOR',
                                                      'strUsrCreacion'    => $strUsuarioSession,
                                                      'strIpCreacion'     => $strIpCreacion);
    
                $arrayResultadosCargoVendedor = $serviceUtilidades->getDetallesParametrizables($arrayParametrosCargoVendedor);
    
                if( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )
                {
                    foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdCargoVendedor )
                    {
                        $arrayParametros['criterios']['cargo'] = $intIdCargoVendedor;
                    }//foreach( $arrayResultadosCargoVendedor['resultado'] as $intIdDepartamento )
                }//( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) )
                $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonaEmpresaRol;
                $arrayPersonalVendedor = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findPersonalByCriterios($arrayParametros);
                
                if( isset($arrayPersonalVendedor['registros']) && !empty($arrayPersonalVendedor['registros']) 
                    && isset($arrayPersonalVendedor['total']) && $arrayPersonalVendedor['total'] > 0 )
                {
                    foreach($arrayPersonalVendedor['registros'] as $arrayVendedor)
                    {
                        $strNombreVendedor = ( isset($arrayVendedor['nombres']) && !empty($arrayVendedor['nombres']) )
                                             ? ucwords(strtolower($arrayVendedor['nombres'])).' ' : '';
                        $strNombreVendedor .= ( isset($arrayVendedor['apellidos']) && !empty($arrayVendedor['apellidos']) )
                                              ? ucwords(strtolower($arrayVendedor['apellidos'])) : '';
                        $strLoginVendedor = ( isset($arrayVendedor['login']) && !empty($arrayVendedor['login']) )
                                            ? $arrayVendedor['login'] : '';
                        
                        $arrayItemVendedor           = array();
                        $arrayItemVendedor['nombre'] = $strNombreVendedor;
                        $arrayItemVendedor['login']  = $strLoginVendedor;
                        $arrayVendedores[]           = $arrayItemVendedor;
                    }//foreach($arrayPersonalVendedor['registros'] as $arrayVendedor)
                    $objResponse = new Response(json_encode(array('empleados' => $arrayVendedores)));
                    $objResponse->headers->set('Content-type', 'text/json');
                }//( isset($arrayPersonalVendedor['registros']) && !empty($arrayPersonalVendedor['registros'])
            }
            else
            {
                $objResponse = new Response(json_encode(array('empleados' => $arrayVendedores)));
                $objResponse->headers->set('Content-type', 'text/json');
            }
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'administracionBundle.PreClienteController.getEmpleadosAction',
                                      $e->getMessage(),
                                      $strUsuarioSession,
                                      $strIpCreacion);
        }
        return $objResponse;
    }
    /**
     *
     * Documentación para funcion 'getEditUserCreacionAction'.
     * Esta funcion edita el usuario creacion
     * @author Kevin Baque Puya<kbaque@telconet.ec>
     * @version 1.0 28-01-2019
     *
     */
    public function getEditUserCreacionAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $intIdPersona           = $objRequest->get('intIdPersona');
        $intIdPersonaEmpresaRol = $objRequest->get('intIdPersonaEmpresaRol');
        $strNuevoUserCreacion   = $objRequest->get('strIdLogin');
        $strAntesUserCreacion   = $objRequest->get('strUsCreacionAntes');
        $emComercial            = $this->get('doctrine')->getManager('telconet');

        try
        {
            $strMsg = "Se edito usuario creacion correctamente";
            $strSuccess = true;
            
            $objInfoPersona   = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
            $objPersonaEmpRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
            $objMotivo        = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                    ->findOneBy(array('nombreMotivo' => 'Cambio de usuario creación'));

            if(!$objMotivo)
            {
                throw new \Exception("No encontro motivo de Edición de Usuario creacion");
            }
            if($objInfoPersona)
            {
                $emComercial->getConnection()->beginTransaction();
                // Guardo Historial de informacion editada
                $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                $objInfoPersonaEmpresaRolHisto->setEstado($objPersonaEmpRol->getEstado());
                $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
                $objInfoPersonaEmpresaRolHisto->setIpCreacion($strIpCreacion);
                $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);
                $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                $objInfoPersonaEmpresaRolHisto->setObservacion("El usuario ".$strUsrCreacion. " realizó el cambio de usuario creación de ".$strAntesUserCreacion." a: " .$strNuevoUserCreacion);
                $objInfoPersona->setUsrCreacion($strNuevoUserCreacion);
                $objPersonaEmpRol->setUsrCreacion($strNuevoUserCreacion);

                $emComercial->persist($objInfoPersonaEmpresaRolHisto);
                $emComercial->persist($objInfoPersona);
                $emComercial->persist($objPersonaEmpRol);
                $emComercial->flush();

                if ($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->getConnection()->commit();
                }
            }
            $objResponse = new Response(json_encode(array('success' => $strSuccess, 'msg' => $strMsg)));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;

        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strSuccess = false;
            $strMsg = $e->getMessage();
            $objResponse = new Response(json_encode(array('success' => $strSuccess, 'msg' => $strMsg)));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
    }
    /**
     * @Secure(roles="ROLE_6-7")
     * 
     * Documentacion para el método 'gridAction'
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 16-05-2016 - Se modifica la función para que busque al personal paginado de forma correcta.
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-05-28 Se remueve el envio del cliente como parámetro
     *                         Se omite el incluir un registro en blanco en arreglo cuanto no retorna nada
     * 
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 15-07-2016 Se agrega campo tipo_tributario, representante_legal al arreglo.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.4 08-08-2016 
     * Se agrega parametro de idPersonaEmpresaRol para el envio en la edicion y poder guardar el Historial
     * se aumenta edicion de Oficina de Facturacion
     * 
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.5 24-12-2018 Se realiza cambio para quela consulta de clientes se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente solo tendrá acceso a los pre-clientes de los vendedores asignados al asistente
     *                         en caso de ser vendedor solo tendrá acceso a sus pre-clientes
     *                         en caso de ser subgerente solo tendrá acceso a pre-clientes de los vendedores que reportan al subgerente
     *                         en caso de ser gerente u otro cargo no aplican los cambios
     */     
    public function gridAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strUsrCreacion    = $objSession->get('user');
        $fechaDesde        = explode('T', $objRequest->get("fechaDesde"));
        $fechaHasta        = explode('T', $objRequest->get("fechaHasta"));
        $strEstado         = $objRequest->get("estado");
        $strNombre         = $objRequest->get("nombre");
        $strApellido       = $objRequest->get("apellido");
        $strRazonSocial    = $objRequest->get("razonSocial");
        $limit             = $objRequest->get("limit");
        $start             = $objRequest->get("start");
        $page              = $objRequest->get("page");
        $strIdentificacion = $objRequest->get("identificacion");
        $intIdEmpresa      = $objRequest->getSession()->get('idEmpresa');
        $em                = $this->get('doctrine')->getManager('telconet');
        $strTipoPersonal   = 'Otros';
        $strModulo         = 'Pre-Cliente';
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $em->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        $arrayParametros   = array();
        $arrayParametros['estado']         = $strEstado;
        $arrayParametros['idEmpresa']      = $intIdEmpresa;
        $arrayParametros['fechaDesde']     = $fechaDesde[0];
        $arrayParametros['fechaHasta']     = $fechaHasta[0];
        $arrayParametros['nombre']         = $strNombre;
        $arrayParametros['apellido']       = $strApellido;
        $arrayParametros['razon_social']   = $strRazonSocial;
        $arrayParametros['identificacion'] = $strIdentificacion;
        $arrayParametros['limit']          = $limit;
        $arrayParametros['page']           = $page;
        $arrayParametros['start']          = $start;
        $arrayParametros['tipo_persona']   = 'Pre-cliente';
        $arrayParametros['usuario']        = '';
        $arrayParametros['strModulo']             = $strModulo;
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        $arrayResultado  = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonasPorCriterios($arrayParametros);
        $arrayRegistros      = $arrayResultado['registros'];
        $intTotal      = $arrayResultado['total'];

        $arrayPreClientes = array();
		$i=1;
		foreach ($arrayRegistros as $arrayDatos):
            $clase='';
            if($i % 2==0)
            {
                $clase='k-alt';
            }
            
            $intIdPersona   = $arrayDatos['persona_id'];
            $strUrlVer      = $this->generateUrl('precliente_show', array('id' => $intIdPersona,'idper' =>$arrayDatos['id'] ));
            $strUrlEditar   = $this->generateUrl('precliente_edit', array('id' => $intIdPersona));
            $strUrlEliminar = $this->generateUrl('precliente_delete_ajax', array('id' => $intIdPersona));
            $strLinkVer     = $strUrlVer;
            $arrayFechaEmision  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getUltimaFacturaPorPersonaEmpresaRol($arrayDatos['id']);
            $strFechaEmision = '';
            if( !empty($arrayFechaEmision) )
            {
                $arrayFechaEmision = $arrayFechaEmision[0];
                $strFechaEmision   = $arrayFechaEmision['fechaEmision'];
            }
            $strVendAsignado = 'N';
            $arrayParametrosVend                          = array();
            $arrayParametrosVend['strPrefijoEmpresa']     = $strPrefijoEmpresa;
            $arrayParametrosVend['strTipoPersonal']       = $strTipoPersonal;
            $arrayParametrosVend['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
            $arrayParametrosVend['strLoginVendedor']      = $arrayDatos['usr_creacion'];
            
            $arrayResultadoVendAsignado = $em->getRepository('schemaBundle:InfoPersona')->getVendAsignado($arrayParametrosVend);
            
            if( (!empty($arrayDatos['vendedorasignado']) && $strTipoPersonal !='Otros' && $strTipo !='GERENTE_VENTAS') || ($arrayDatos['usr_creacion']==$strUsrCreacion) )
            {
                $strVendAsignado = 'S';
            }
            if( !empty($arrayResultadoVendAsignado['resultados']) )
            {
                $strVendAsignado = 'S';
            }
            $strEstado = $arrayDatos['estado'];	
            if($strEstado!="Convertido")
            {
                $strLinkEditar = $strUrlEditar;
            }
            else
            {
                $strLinkEditar ="#";
            }
            
            $strLinkEliminar = $strUrlEliminar;

            if ($arrayDatos['razon_social'])
            {
                $strNombreProspecto = $arrayDatos['razon_social'];
            }
            else
            {
                $strNombreProspecto = $arrayDatos['nombres'].' '.$arrayDatos['apellidos'];
            }
            $intIdOficina     = '';
            $strNombreOficina = '';
            if( !empty($arrayDatos['oficina_id']) )
            {
                $objInfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayDatos['oficina_id']);	
                if( $objInfoOficinaGrupo )
                {
                    $intIdOficina     = $objInfoOficinaGrupo->getId();
                    $strNombreOficina = $objInfoOficinaGrupo->getNombreOficina();
                }
            }
            $strNombre      = '';
            $strApellido    = '';
            $objInfoPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneByLogin($arrayDatos['usr_creacion']);
            if( !empty($objInfoPersona) )
            {
                $strNombre           = $objInfoPersona->getNombres() ? $objInfoPersona->getNombres():'';
                $strApellido         = $objInfoPersona->getApellidos() ? $objInfoPersona->getApellidos():'';
                $strNombresCompletos = ucwords( strtolower($strNombre)).' '. ucwords( strtolower($strApellido));
            }
            $arrayPreClientes[]= array(
                              'idPersona'           => $intIdPersona,
                              'idPersonaEmpresaRol' => $arrayDatos['id'],
                              'idOficina'           => $intIdOficina,
                              'nombreOficina'       => $strNombreOficina,
                              'Nombre'              => $strNombreProspecto,
                              'Direccion'           => $arrayDatos['direccion_tributaria'],
                              'fechaCreacion'       => strval(date_format($arrayDatos['fe_creacion'],"d/m/Y G:i")),
                              'usrVendedor'         => $arrayDatos['vendedor'],
                              'feEmision'           => $strFechaEmision,
                              'strVendAsignado'     => $strVendAsignado,
                              'strTipoPersonal'     => $strTipoPersonal ? $strTipoPersonal :'Otros',
                              'usuarioCreacion'     => $strNombresCompletos,
                              'loginUserCreacion'   => $arrayDatos['usr_creacion'],
                              'estado'              => $strEstado,
                              'tipoEmpresa'         => $arrayDatos['tipo_empresa'],    
                              'linkVer'             => $strLinkVer,
                              'linkEditar'          => $strLinkEditar,
                              'linkEliminar'        => $strLinkEliminar,
                              'clase'               => $clase,
                              'tipoTributario'      => $arrayDatos['tipo_tributario'],
                              'representanteLegal'  => $arrayDatos['representante_legal'],
                              'boton'               => ""                              
                             );          
                 
            $i++;     
		endforeach;
        $objResponse = new Response(json_encode(array('total'=>$intTotal,'preclientes'=>$arrayPreClientes)));
		$objResponse->headers->set('Content-type', 'text/json');
        
		return $objResponse;
    }

    /*combo estado llenado ajax*/
    public function estadosAction()
    {
                $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
                $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
                $arreglo[]= array('idEstado'=>'Convertido','codigo'=> 'ACT','descripcion'=> 'Convertido');
				$arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');				
				$arreglo[]= array('idEstado'=>'Pend-convertir','codigo'=> 'ACT','descripcion'=> 'Pend-convertir');				
                $response = new Response(json_encode(array('estados'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    }

    /**
    * @Secure(roles="ROLE_6-6")
    */
    /**
     * Documentación para el método 'showAction'.
     *
     * Mostrar pantalla de precliente
     *
     * @param integer   $id
     * @param integer   $idper
     * @return twig     retornar twig de infomacion de precliente
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 11-11-2014
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.1 02-10-2017
     * Se agrega que sea visible en el Show del PreCliente la informacion de su ciclo de Facturacion si la empresa en sesion es MD
     *
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 1.2 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 18-04-2018 Se agrega seteo de variable de sesión 'cicloFacturacionCliente' 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto'.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.5 10-06-2020 - Consulta de información del representante legal para persona jurídica de megadatos
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 10-12-2020 - Se cambia la validación del perfil que permite consultar, registrar y actualizar la
     *                           información del representante legal por la acción.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.7 09-05-2021 - Se agrega validación para visualizar si el cliente en sesión es de tipo distribuidor.
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022 - Se elimina consumo de representante legal por nueva implemnetacion con ms
     *
     */
    public function showAction($id, $idper)
    {
        $verHistorial   = false;
        $strNombreCiclo = '';
        if(true === $this->get('security.context')->isGranted('ROLE_265-1797'))
        {
            $verHistorial = true;
        }

        $request           = $this->getRequest();
        $strEmpresaCod     = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');       
        $strEsDistribuidor = "NO";
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);
        $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->find($entity->getPersonaId()->getId());

        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strEmpresaCod']     = $strEmpresaCod;

        $serviceComercial   = $this->get('comercial.Comercial');
        $strAplicaCiclosFac = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        if($strPrefijoEmpresa == 'TN')
        {
            // Obtengo oficina de facturacion
            $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                      ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Pre-cliente', $strEmpresaCod);
            if($entityPersonaEmpresaRol)
            {
                $oficinaFacturacionId = $entityPersonaEmpresaRol->getOficinaId(); 
                $oficinaFacturacion   = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficinaFacturacionId);
            }
            
            $strCaracteristica     = 'HOLDING EMPRESARIAL';
            $strEstado             = 'Activo';
            $objCaracteristicaHol  = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
            
            if(is_object($objCaracteristicaHol))
            {
                $objEmpresaRolCarac = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy(
                                                                                       array('personaEmpresaRolId'=>$entityPersonaEmpresaRol->getId(),
                                                                                             'caracteristicaId'=>$objCaracteristicaHol->getId()));
                
                if(is_object($objEmpresaRolCarac) && !empty($objEmpresaRolCarac))
                {
                   $objParametroDet = $em->getRepository('schemaBundle:AdmiParametroDet')->find($objEmpresaRolCarac->getValor()); 
                   if(is_object($objParametroDet) && !empty($objParametroDet))
                   {
                       $strHolding = $objParametroDet->getValor1();
                   }
                }
            }
            $objCaractEsDisttribuidor = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                           ->findOneBy(array("descripcionCaracteristica" => 'ES_DISTRIBUIDOR',
                                                             "estado"                    => 'Activo'));
            if(is_object($objCaractEsDisttribuidor) && !empty($objCaractEsDisttribuidor))
            {
                $objEmpresaRolCaracDist = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                             ->findOneBy(array('personaEmpresaRolId' => $entityPersonaEmpresaRol->getId(),
                                                               'caracteristicaId'    => $objCaractEsDisttribuidor->getId()));
                if(is_object($objEmpresaRolCaracDist) && !empty($objEmpresaRolCaracDist))
                {
                    $strEsDistribuidor = $objEmpresaRolCaracDist->getValor();
                }
            }
        }

        if(($strPrefijoEmpresa == 'MD' ||$strPrefijoEmpresa == 'EN'  ) && $entityPersona->getTipoTributario() == 'JUR')
        {
            //Acción IngresarRepresentanteLegal.
            $strEsCoordinadorMD     = true === $this->get('security.context')->isGranted('ROLE_13-7737') ? '1' : '0';       

        }
	
        $strProspecto = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession($strEmpresaCod, $entity->getPersonaId()->getId(), 
                                                                                           'Pre-cliente');
        
        $strProspecto['esRecontratacion'] = "";
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $session->set('cliente', $strProspecto);
        $session->set('ptoCliente', '');
        $session->set('clienteContacto', '');
        $session->set('puntoContactos', '');
        $session->set('contactosCliente', '');
        $session->set('contactosPunto', '');
        $session->set('numServicios', '');
        $session->set('serviciosPunto', '');
        $session->set('datosFinancierosPunto', '');
        
        $session->set('menu_modulo_activo', '');
        $session->set('nombre_menu_modulo_activo', '');
        $session->set('id_menu_modulo_activo', '');
        $session->set('imagen_menu_modulo_activo', '');
        $session->set('cicloFacturacionCliente', '');

        if ($strAplicaCiclosFac == 'S' )
        {
            $arrayParam                    = array();
            $arrayParam['intIdPersonaRol'] = $entity->getId();
            //Obtengo Ciclo de Facturacion asignado en el Pre_cliente
            $arrayPersEmpRolCaracCicloPreCliente = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                      ->getCaractCicloFacturacion($arrayParam);
            if( isset($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract'])
                        && !empty($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract']) )
            {
                $strNombreCiclo = $arrayPersEmpRolCaracCicloPreCliente['strNombreCiclo'];
                $session->set('cicloFacturacionCliente', $strNombreCiclo);
            }
        }
        
        $deleteForm = $this->createDeleteForm($entity->getPersonaId()->getId());

        $entityPersonaRef = $em->getRepository('schemaBundle:InfoPersonaReferido')->findOneBy(
            array('personaEmpresaRolId' => $idper, 'estado' => 'Activo'));
        $referido = null;
        $idperref = null;
        if($entityPersonaRef)
        {
            if($entityPersonaRef->getRefPersonaEmpresaRolId())
            {
                $referido = $entityPersonaRef->getRefPersonaEmpresaRolId()->getPersonaId();
                //OBTIENE PERSONA EMPRESA ROL DEL REFERIDO
                $personaEmpresaRolReferido = $entityPersonaRef->getRefPersonaEmpresaRolId();
                if($personaEmpresaRolReferido)
                    $idperref = $personaEmpresaRolReferido->getId();
                else
                    $idperref = null;
            }
        }
        //Obtiene el historial del prospecto(pre-cliente)
        $historial = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findHistorialPorPersonaEmpresaRol($entity->getId());
        $ultimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($entity->getId());
        $idUltimoEstado = $ultimoEstado[0]['ultimo'];
        $entityUltimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
        $estado = $entityUltimoEstado->getEstado();
        //Recorre el historial y separa en arreglos cada estado
        $i = 0;
        $creacion = null;
        $convertido = null;
        $eliminado = null;
        $ultMod = null;

        //Obtiene formas de contacto
        $formasContacto = null;
        $arrformasContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($entity->getPersonaId()->getId(), 
                                                                                                                  'Activo', 9999999, 1, 0);
        if($arrformasContacto['registros'])
            $formasContacto = $arrformasContacto['registros'];

        foreach($historial as $dato):
            
            if($i == 0)
            {
                $creacion = array('estado' => $dato->getEstado(), 'usrCreacion' => $dato->getUsrCreacion(), 'feCreacion' => $dato->getFeCreacion(), 
                                  'ipCreacion' => $dato->getIpCreacion());
            }
            if($i > 0)
            {
                if($dato->getEstado() == 'Convertido')
                {
                    $convertido = array('estado' => $dato->getEstado(), 'usrCreacion' => $dato->getUsrCreacion(), 
                                        'feCreacion' => $dato->getFeCreacion(), 'ipCreacion' => $dato->getIpCreacion());
                }
                elseif($dato->getEstado() == 'Eliminado')
                {
                    $eliminado = array('estado' => $dato->getEstado(), 'usrCreacion' => $dato->getUsrCreacion(), 
                                       'feCreacion' => $dato->getFeCreacion(), 'ipCreacion' => $dato->getIpCreacion());
                }
                else
                {
                    $ultMod = array('estado' => $dato->getEstado(), 'usrCreacion' => $dato->getUsrCreacion(), 
                                    'feCreacion' => $dato->getFeCreacion(), 'ipCreacion' => $dato->getIpCreacion());
                }
            }
            $i++;
        endforeach;

        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("6", "1");
        $session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
        $servicePreCliente = $this->get('comercial.PreCliente');
        $entityPersonaEmpFormaPago = $servicePreCliente->getDatosPersonaEmpFormaPago($id, $strEmpresaCod);
        $formaPago = null;
        $tipoCuenta = null;
        $banco = null;
        if($entityPersonaEmpFormaPago != null)
        {
            $formaPago = $entityPersonaEmpFormaPago->getFormaPagoId();
            if($entityPersonaEmpFormaPago->getTipoCuentaId() != null)
                $tipoCuenta = $entityPersonaEmpFormaPago->getTipoCuentaId();
            if($entityPersonaEmpFormaPago->getBancoTipoCuentaId() != null)
                $banco = $entityPersonaEmpFormaPago->getBancoTipoCuentaId()->getBancoId();
        }

        //se agrega control de roles permitidos
        $rolesPermitidos = array();
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-1779'))
        {
            $rolesPermitidos[] = 'ROLE_13-1779'; //ANULAR PUNTO
        }

        return $this->render('comercialBundle:precliente:show.html.twig', 
                             array('item'                      => $entityItemMenu,
                                   'entity'                    => $entityPersona,
                                   'delete_form'               => $deleteForm->createView(),
                                   'referido'                  => $referido,
                                   'creacion'                  => $creacion,
                                   'ultMod'                    => $ultMod,
                                   'eliminado'                 => $eliminado,
                                   'convertido'                => $convertido,
                                   'estado'                    => $estado,
                                   'formasContacto'            => $formasContacto,
                                   'idperref'                  => $idperref,
                                   'idper'                     => $idper,
                                   'entityPersonaEmpFormaPago' => $entityPersonaEmpFormaPago,
                                   'formaPago'                 => $formaPago,
                                   'tipoCuenta'                => $tipoCuenta,
                                   'banco'                     => $banco,
                                   'verHistorial'              => $verHistorial,
                                   // Se agrega parametro de roles permitidos
                                   'rolesPermitidos'           => $rolesPermitidos, 
                                   'oficinaFacturacion'        => $oficinaFacturacion,
                                   'entityPersonaEmpresaRol'   => $entityPersonaEmpresaRol,
                                   'strNombreCiclo'            => $strNombreCiclo,
                                   'esCoordinadorMD'           => $strEsCoordinadorMD,
                                   'holding'                   => $strHolding,
                                   'strEsDistribuidor'         => ucwords(strtolower($strEsDistribuidor))
         ));       
    }

    /**
     * @Secure(roles="ROLE_6-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Método que renderiza la pantalla para la inserción de un nuevo prospecto.
     * 
     * @return Render Pantalla.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 29-05-2016
     * Se agrega el prefijoEmpresa a los parámetros de inicialización del Form-Type.
     * 
     * @author : Andrés Montero <amontero@telconet.ec>
     * @version 1.2 10-07-2017
     * Se envia id de pais de la empresa en sesión por parametros al crear formulario de formas de pago con InfoPersonaEmpFormaPagoType 
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.3 09-11-2020
     * Se agrega validación para mostrar datos de Holding segun login de consulta. 
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 03-05-2021 - Se agrega campo "es_distribuidor" con valor "NO".
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.5 25-11-2021 - Se agrega validaciones por empresa MD para obtener las formas de pago parametrizadas.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.6 22-12-2022 - Se modifica valor del nombre de parámetro a 'PARAM_CLIENTE_VALIDACIONES' en la consulta para las formas de pago.
     *                           Adicional se agrega el tipo de proceso 'PUNTO_ADICIONAL' para consultar los detalles de parámetros.
     */
    public function newAction()
    {
        $request            = $this->getRequest();
        $session            = $request->getSession();
        $entity             = new InfoPersona();
        $empresaId          = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa  = $request->getSession()->get('prefijoEmpresa');
        $intIdPais          = $request->getSession()->get('intIdPais');
        $strUsrCreacion         = $request->getSession()->get('user');
        $intIdPersonaEmpresaRol = $request->getSession()->get('idPersonaEmpresaRol');
        $oficinaFacturacion = null;
        $tieneCarnetConadis = 'N';
        $esPrepago          = 'S';
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');

        if($entity->getNumeroConadis()!=null && $entity->getNumeroConadis()!='')
        {
            $tieneCarnetConadis = 'S';
        }                
	
        if($strPrefijoEmpresa == 'TN')
        {
            $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
            if( !empty($arrayResultadoCaracteristicas) )
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = 
                    $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            $arrayParametrosVend                          = array();
            $arrayParametrosVend['strPrefijoEmpresa']     = $strPrefijoEmpresa;
            $arrayParametrosVend['strTipoPersonal']       = $strTipoPersonal;
            $arrayParametrosVend['intIdPersonEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametrosVend['boolHolding']           = true;
            
            $arrayResultadoVendAsignado = $emComercial->getRepository('schemaBundle:InfoPersona')->getVendAsignado($arrayParametrosVend);
        }
        
        $form = $this->createForm(new PreClienteType( array( 'empresaId'          => $empresaId,
                                                             'oficinaFacturacion' => $oficinaFacturacion,
                                                             'tieneCarnetConadis' => $tieneCarnetConadis,
                                                             'esPrepago'          => $esPrepago,
                                                             'prefijoEmpresa'     => $strPrefijoEmpresa,
                                                             'pagaIva'            => 'S',
                                                             'es_distribuidor'    => 'NO',
                                                             'vendedores'         => $arrayResultadoVendAsignado['resultados']
                                                            )
                                                    ), $entity);        
        
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("6", "1");    	
        
        $session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
	
        //Guardo en parametro los forms
        $parametros=array('item'   => $entityItemMenu,
                          'entity' => $entity,
                          'banco'  => null,
                          'form'   => $form->createView());
        
        //Se valida por empresa MD para obtener consulta de las formas de pagos parametrizadas.
        $arrayFormasPago = array();
        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN') 
        {
            //Obtengo parámetro de validación
            $arrayValidaFormaPago = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PARAM_CLIENTE_VALIDACIONES','FINANCIERO','',
                                                 'VALIDACION_FORMAS_PAGO','', 'FORMA_PAGO', '', '', '', $empresaId,'','','PUNTO_ADICIONAL');
            //Se valida con el parámetro si aplica proceso.
            if($arrayValidaFormaPago["valor1"] == "S")
            {
                $arrayParamDetFormaPagos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get("PARAM_CLIENTE_VALIDACIONES", "FINANCIERO", "", 
                                                              "FORMAS_PAGO_WEB_MOVIL", "", "", "", "", "", $empresaId,"","","PUNTO_ADICIONAL");
                foreach($arrayParamDetFormaPagos as $arrayValorDetFormaPagos)
                {
                    $arrayFormasPago[] = $arrayValorDetFormaPagos["valor1"];
                } 
            }
        }    
        
        $entityPersonaEmpFormaPago = new InfoPersonaEmpFormaPago();
        $arrayFormaPago             = $this->createForm(new InfoPersonaEmpFormaPagoType(array("intIdPais"=>$intIdPais,
                                                                                            "arrayFormasPago"=>$arrayFormasPago)), 
                                                       $entityPersonaEmpFormaPago);
        
        $parametros['formFormaPago'] = $arrayFormaPago->createView();
        $parametros['clase']         = "campo-oculto";

        $parametros['formasDeContacto']       = "";       
        $parametros['prefijoEmpresa']         = $strPrefijoEmpresa;
        $parametros['habilitaIdentificacion'] = "N";  

        return $this->render('comercialBundle:precliente:new.html.twig', $parametros);
    }

    /**
     * @Secure(roles="ROLE_6-3")
     * Documentación para el método 'createAction'.
     * 
     * Metodo para guardar informacion de un Cliente
     * Consideracion: Se aumenta campo origen WEB ya que se requiere que se identifiquen los Clientes que han sido ingresados 
     * por la versión Web y los que se ingresaron mediante el Mobil.
     * 
     * @param  request $request       
     * @throws Exception    
     * @return a RedirectResponse to the given URL.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1 26-03-2015     
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.2 29-05-2016
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>  
     * @version 1.3 12-08-2016   Se agrega envió de parametro para habilitar ingreso de identificación en caso de que la ingresada ya exista.
     * 
     * Se agrega validación para no permitir ingreso de campo cédula vacio.
     * En caso de error, se utilizan los datos de creación para enviarlos al Type y corregir la información sin tener que volver a ingresar 
     * nuevamente todos los datos.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4 04-07-2017  Se agregan las variables strNombrePais e intIdPais para validar las formas de contacto en crearPreCliente.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 08-07-2019  Se agrega envío de array de parámetros a función de service invocada para crear un prospecto. 
     * 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022 - Se  actualiza consumo de  de crear prospecto con representante legal
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.7 22-12-2022 - Se agrega validaciones en la lógica del catch por empresa MD para obtener las formas de pago parametrizadas 
     *                           mediante el tipo de proceso 'PUNTO_ADICIONAL'.
     */
    public function createAction(Request $request)
    {
        $codEmpresa     = $request->getSession()->get('idEmpresa');
        $idOficina      = $request->getSession()->get('idOficina');
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $usrCreacion    = $request->getSession()->get('user');
        $clientIp       = $request->getClientIp();
        $datos_form     = $request->request->get('preclientetype');
        $formFormaPago  = $request->get('infopersonaempformapagotype');
        $strNombrePais  = $request->getSession()->get('strNombrePais');
        $intIdPais      = $request->getSession()->get('intIdPais');
        $datosFormaPago = array();
        
        $objInfoPersona     = new InfoPersona();
        $objPersonaEmpresaRol = new InfoPersonaEmpresaRol();
        $tieneCarnetConadis = 'N';
        $esPrepago          = 'S';  
        $request            = $this->getRequest();

        $entityOficinaFacturacion = null;
        $entityTitulo             = null;
        $objEmComercial             = $this->getDoctrine()->getManager('telconet');
       
        try
        {
            if(!empty($formFormaPago))
            {
                $datos_form = array_merge($datos_form, $formFormaPago);
            }
    
            if ($prefijoEmpresa=='MD' || $prefijoEmpresa=='EN')
             {


                $strDireccionTributaria = $request->request->get("preclientetype_direccionTributaria");

                if ( $strDireccionTributaria)
                {
                    $datos_form["direccionTributaria"] =  $strDireccionTributaria; 
                }
   

                $arrayFormasContacto = array(); 
                // si no se ha especificado formas de contacto, obtenerlas de $datos_form
                if(isset($datos_form['formas_contacto']))
                {
                    if(is_array($datos_form['formas_contacto']))
                    {
                        $arrayFormasContacto = $datos_form['formas_contacto'];
                    }
                    else
                    {
                        $arrayFormContacto = explode(',', $datos_form['formas_contacto']);
                        for($intContador = 0; $intContador < count($arrayFormContacto); $intContador+=3)
                        {
                            $strFormaContacto = $arrayFormContacto[$intContador + 1]; 
                            $strValor= $arrayFormContacto[$intContador + 2]; 
                            $objRepositoriFormaPago = $objEmComercial ->getRepository('schemaBundle:AdmiFormaContacto'); 
                            $entityAdmiFormaContacto =$objRepositoriFormaPago->findPorDescripcionFormaContacto($strFormaContacto);
                            $intFormaContactoId= $entityAdmiFormaContacto->getId();
                            $arrayFormasContacto[] = array(
                                'formaContactoId' => $intFormaContactoId,
                                'formaContacto'   => $strFormaContacto,
                                'valor'           => $strValor
                            );
                        }
                    }
                } 
                
                $objFechaNacimiento = $datos_form['fechaNacimiento']; 
                if (!is_null($objFechaNacimiento)) 
                {
                    $strMonth = $objFechaNacimiento ['month']; 
                    $strDay   = $objFechaNacimiento ['day']; 
                   if (!is_null($strMonth)&& !$strMonth=="" )
                   {
                    $datos_form['fechaNacimiento'] ['month'] =  str_pad( $strMonth , 2, "0", STR_PAD_LEFT); 
                   }
                   if (!is_null($strDay)&& !$strDay=="")
                   {
                    $datos_form['fechaNacimiento'] ['day']  =  str_pad( $strDay, 2, "0", STR_PAD_LEFT); 
                   }
                
                }
                
                $objDatosForm =$datos_form; 
                $strRecomendacion =   $objDatosForm["dataRecomendacion"];  
                unset($objDatosForm["dataRecomendacion"]);
                
                $arrayRepresentanteLegal = [];           
                if ($objDatosForm["representante_legal"]!="")
                {
                    $arrayRepresentanteLegal = json_decode(  $objDatosForm["representante_legal"] , true ); 
                }
           
                unset($objDatosForm["representante_legal"]);

                $serviceTokenCas = $this->get('seguridad.TokenCas');
                $arrayTokenCas = $serviceTokenCas->generarTokenCas();   

                $arrayParametrosPreCliente  =  array(                    
                                                    'token'                => $arrayTokenCas['strToken'],
                                                    'codEmpresa'           => $codEmpresa,
                                                    'oficinaId'            => $idOficina,
                                                    'usrCreacion'          => $usrCreacion,
                                                    'clientIp'             => $clientIp,
                                                    'datosForm'            => $objDatosForm,
                                                    'prefijoEmpresa'       => $prefijoEmpresa, 
                                                    'origenWeb'            => 'S',  
                                                    'idPais'               => $intIdPais,
                                                    'formaContacto'        => $arrayFormasContacto,
                                                    'strRecomendacionTarjeta' => $strRecomendacion ,
                                                    'representanteLegal'      => $arrayRepresentanteLegal 
                                                );  
                                                
                $servicePreClienteMs = $this->get('comercial.PreClienteMs');
                    
                $objResponse     =  $servicePreClienteMs->wsCrearProspecto($arrayParametrosPreCliente);
                if ($objResponse['strStatus']!='OK' ) 
                {
                    throw new \Exception( $objResponse['strMensaje']);
                } 
                $objData            =  $objResponse['objData']; 
                $intIdPersona          =  $objData['idPersona']; 
                $intIdPersonaEmpresaRol=  $objData['idPersonaEmpresaRol']; 
                
                return $this->redirect($this->generateUrl('precliente_show', array('id' => $intIdPersona, 'idper' => $intIdPersonaEmpresaRol)));
                
           }
           else 
           {
                
            //Agrego origen_web al arreglo que se envia al service y le envio con "S"
            $arrayOrigenWeb              = array('origen_web'=>'S'); 
            $datos_form                  = array_merge($datos_form, $arrayOrigenWeb);
            $datos_form['strNombrePais']    = $strNombrePais;
            $datos_form['intIdPais']        = $intIdPais;


            /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
            $servicePreCliente          = $this->get('comercial.PreCliente');
            
            $arrayParametrosPreCliente  =  array('strCodEmpresa'        => $codEmpresa,
                                                 'intOficinaId'         => $idOficina,
                                                 'strUsrCreacion'       => $usrCreacion,
                                                 'strClientIp'          => $clientIp,
                                                 'arrayDatosForm'       => $datos_form,
                                                 'strPrefijoEmpresa'    => $prefijoEmpresa,
                                                 'arrayFormasContacto'  => null);

            $objPersonaEmpresaRol     =  $servicePreCliente->crearPreCliente($objInfoPersona,$arrayParametrosPreCliente);

            return $this->redirect($this->generateUrl('precliente_show', array('id'    => $objPersonaEmpresaRol->getPersonaId()->getId(), 
                                                                               'idper' => $objPersonaEmpresaRol->getId())));
           }
        }  
        catch(\Exception $e)
        {
            $bancoTipoCuentaId = null;
            $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");       
            $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("6", "1");
            $em             = $this->getDoctrine()->getManager('telconet');
            $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
            // Obtengo la Oficina de Facturación
            if(isset($datos_form['idOficinaFacturacion']) && $datos_form['idOficinaFacturacion'] > 0)
            {
                $entityOficinaFacturacion = $this->getDoctrine()->getManager('telconet')
                                                                ->getRepository('schemaBundle:InfoOficinaGrupo')
                                                                ->findOneBy(array('id' => $datos_form['idOficinaFacturacion']));
            }
            // Obtengo el Título de la persona
            if(isset($datos_form['tituloId']) && $datos_form['tituloId'] > 0)
            {
                $entityTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->findOneBy(array('id' => $datos_form['tituloId']));
            }
            
            if(isset($formFormaPago['formaPagoId']) && intval($formFormaPago['formaPagoId']) > 0)
            {
                $datosFormaPago['entityFormaPago'] = $em->getRepository('schemaBundle:AdmiFormaPago')
                                                        ->findOneBy(array('id' => $formFormaPago['formaPagoId']));
            }
            if(isset($formFormaPago['tipoCuentaId']) && intval($formFormaPago['tipoCuentaId']) > 0)
            {
                $datosFormaPago['entityTipoCuenta'] = $em->getRepository('schemaBundle:AdmiTipoCuenta')
                                                         ->findOneBy(array('id' => $formFormaPago['tipoCuentaId']));
            }
            if(isset($formFormaPago['bancoTipoCuentaId']) && intval($formFormaPago['bancoTipoCuentaId']) > 0)
            {
                $bancoTipoCuentaId = $formFormaPago['bancoTipoCuentaId'];
            }
            
            $form = $this->createForm(new PreClienteType( array('empresaId'          => $codEmpresa,
                                                                'oficinaFacturacion' => $entityOficinaFacturacion,
                                                                'titulo'             => $entityTitulo,
                                                                'tieneCarnetConadis' => $tieneCarnetConadis,
                                                                'datos'              => $datos_form,
                                                                'prefijoEmpresa'     => $prefijoEmpresa,
                                                                'esPrepago'          => $esPrepago)), $objPersonaEmpresaRol->getPersonaId());
            //aqui algun mensaje con la excepcion concatenada
            //Guardo en parametro los forms
            $parametros = array('item'   => $entityItemMenu,
                                'error'  => $e->getMessage(),
                                'entity' => $objPersonaEmpresaRol->getPersonaId(),
                                'banco'  => $bancoTipoCuentaId,
                                'form'   => $form->createView());
            
            $parametros['prefijoEmpresa']   = $prefijoEmpresa;
            $parametros['tipoTributario']   = $datos_form['tipoTributario'];
            $parametros['formasDeContacto'] = $datos_form['formas_contacto'];
            
            //Se valida por empresa MD para obtener consulta de las formas de pagos parametrizadas.
            $arrayFormasPago = array();
            if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN') 
            {
                //Obtengo parámetro de validación
                $arrayValidaFormaPago = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAM_CLIENTE_VALIDACIONES','FINANCIERO','','VALIDACION_FORMAS_PAGO','', 
                                                     'FORMA_PAGO','','','',$codEmpresa,'','','PUNTO_ADICIONAL');
                //Se valida con el parámetro si aplica proceso.
                if($arrayValidaFormaPago["valor1"] == "S")
                {
                    $arrayParamDetFormaPagos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("PARAM_CLIENTE_VALIDACIONES","FINANCIERO","","FORMAS_PAGO_WEB_MOVIL",
                                                                  "","","","","", $codEmpresa,"","","PUNTO_ADICIONAL");
                    foreach($arrayParamDetFormaPagos as $arrayValorDetFormaPagos)
                    {
                        $arrayFormasPago[] = $arrayValorDetFormaPagos["valor1"];
                    } 
                }
            }
            
            $formFormaPago = $this->createForm(new InfoPersonaEmpFormaPagoType(array('datos' => $datosFormaPago,
                                                                                     'intIdPais' => $intIdPais,
                                                                                     'arrayFormasPago' => $arrayFormasPago)), 
                                               new InfoPersonaEmpFormaPago());
            
            $parametros['formFormaPago']          = $formFormaPago->createView();
            $parametros['clase']                  = "campo-oculto";
            $parametros['habilitaIdentificacion'] = "S";
            return $this->render('comercialBundle:precliente:new.html.twig', $parametros);
        }
    }

    /**
    * @Secure(roles="ROLE_6-4")
    * Documentación para el método 'editAction'.
    * 
    * Obtiene la informacion del PreCliente y sus formas de Contacto para edicion.
    *     
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
    * @version 1.1 07-11-2016
    * Se agrega parametro prefijoEmpresa
    * 
    * @author : Andrés Montero <amontero@telconet.ec>
    * @version 1.2 10-07-2017
    * Se envia id de pais de la empresa en sesión por parametros al crear formulario de formas de pago con InfoPersonaEmpFormaPagoEditType 
    *
    * @author Edgar Holguín <eholguin@telconet.ec>       
    * @version 1.3 02-04-2019
    * Se agrega envío de parametro prefijoEmpresa en la creación de typeform
    *
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.4 22-12-2022 - Se agrega validaciones por empresa MD para obtener las formas de pago parametrizadas mediante el
    *                           tipo de proceso 'PUNTO_ADICIONAL'.
    */
    public function editAction($id)
    {
        $request              = $this->getRequest();
        $session              = $request->getSession();		
        $em                   = $this->getDoctrine()->getManager('telconet');
        $idEmpresa            = $request->getSession()->get('idEmpresa');
        $prefijoEmpresa       = $request->getSession()->get('prefijoEmpresa');
        $intIdPais            = $request->getSession()->get('intIdPais');
        $referido             = null;
        $referidoper          = null;
        $oficinaFacturacionId = null;
        $oficinaFacturacion   = null;
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                      ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($id,'Pre-cliente', $idEmpresa);        
        if($prefijoEmpresa == 'TN')
        {
            if($entityPersonaEmpresaRol)
            {
                $oficinaFacturacionId = $entityPersonaEmpresaRol->getOficinaId(); 
                $oficinaFacturacion   = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficinaFacturacionId);
            }
        }
        $tieneCarnetConadis = 'N';
        $esPrepago = 'S';
        
        if($entity->getNumeroConadis()!=null && $entity->getNumeroConadis()!='')
        {
            $tieneCarnetConadis = 'S';
        }   
        if($entityPersonaEmpresaRol->getEsPrepago()!=null && $entityPersonaEmpresaRol->getEsPrepago()!='')
        {
            $esPrepago = $entityPersonaEmpresaRol->getEsPrepago();
        }        
	    if($entity->getPagaIva()!=null && $entity->getPagaIva()!='')
        {
            $pagaIva = $entity->getPagaIva();
        }  
        $editForm = $this->createForm(new PreClienteType(array('empresaId'         => $idEmpresa,
                                                               'oficinaFacturacion'=> $oficinaFacturacion,
                                                               'tieneCarnetConadis'=> $tieneCarnetConadis,
                                                               'esPrepago'         => $esPrepago,
                                                               'pagaIva'           => $pagaIva,
                                                               'prefijoEmpresa'    => $prefijoEmpresa
                                                              )), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $entityPersonaRef  = $em->getRepository('schemaBundle:InfoPersonaReferido')->findPorPersona($id);
        if ($entityPersonaRef){
            $referido = $entityPersonaRef->getReferidoId();
            $referidoper = $entityPersonaRef->getRefPersonaEmpresaRolId();
        }
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("6", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        
        //Se valida por empresa MD para obtener consulta de las formas de pagos parametrizadas.
        $arrayFormasPago = array();
        if($prefijoEmpresa == 'MD'|| $prefijoEmpresa == 'EN') 
        {
            //Obtengo parámetro de validación
            $arrayValidaFormaPago = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('PARAM_CLIENTE_VALIDACIONES','FINANCIERO','','VALIDACION_FORMAS_PAGO','', 
                                                 'FORMA_PAGO','','','',$idEmpresa,'','','PUNTO_ADICIONAL');
            //Se valida con el parámetro si aplica proceso.
            if($arrayValidaFormaPago["valor1"] == "S")
            {
                $arrayParamDetFormaPagos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get("PARAM_CLIENTE_VALIDACIONES","FINANCIERO","","FORMAS_PAGO_WEB_MOVIL",
                                                              "","","","","", $idEmpresa,"","","PUNTO_ADICIONAL");
                foreach($arrayParamDetFormaPagos as $arrayValorDetFormaPagos)
                {
                    $arrayFormasPago[] = $arrayValorDetFormaPagos["valor1"];
                } 
            }
        }
				
        /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
        $servicePreCliente = $this->get('comercial.PreCliente');
        $entityPersonaEmpFormaPago=$servicePreCliente->getDatosPersonaEmpFormaPago($id,$idEmpresa); 
        if($entityPersonaEmpFormaPago){
            $objFormFormaPago = $this->createForm(new InfoPersonaEmpFormaPagoEditType(array("intIdPais"=>$intIdPais,
                                                                                            "arrayFormasPago" => $arrayFormasPago)), 
                                                 $entityPersonaEmpFormaPago);
            $objFormFormaPago = $objFormFormaPago->createView();
            $bancoTipoCuentaId=null;
            $tipoCuentaId=null;
            $id_persona_empresa_rol=$entityPersonaEmpFormaPago->getPersonaEmpresaRolId()->getId();
           // echo "id_persona_emp_rol:".$id_persona_empresa_rol; die();
           if($entityPersonaEmpFormaPago->getBancoTipoCuentaId()!=null){
               $bancoTipoCuentaId=$entityPersonaEmpFormaPago->getBancoTipoCuentaId()->getId();
           }
           if($entityPersonaEmpFormaPago->getTipoCuentaId()!=null){
             $tipoCuentaId=$entityPersonaEmpFormaPago->getTipoCuentaId()->getId();
          }
        }else{
            $id_persona_empresa_rol="";
            $objFormFormaPago = new InfoPersonaEmpFormaPago();
            $objFormFormaPago = $this->createForm(new InfoPersonaEmpFormaPagoEditType(array("intIdPais"=>$intIdPais,
                                                                                            "arrayFormasPago" => $arrayFormasPago)), 
                                                  $objFormFormaPago);
            
            $objFormFormaPago = $objFormFormaPago->createView();
            $bancoTipoCuentaId=null;
            $tipoCuentaId=null;
        }
      
        return $this->render('comercialBundle:precliente:edit.html.twig', array(
                             'item'                => $entityItemMenu,
                             'entity'              => $entity,
                             'edit_form'           => $editForm->createView(),
                             'delete_form'         => $deleteForm->createView(),
                             'referidoActual'      => $referido,
                             'referidoActualPer'   => $referidoper,
                             'formFormaPago'       => $objFormFormaPago,
                             'clase'               => 'campo-oculto',
                             'bancoTipoCuentaId'   => $bancoTipoCuentaId,
                             'tipoCuentaId'        => $tipoCuentaId,
                             'personaEmpresaRolId' => $id_persona_empresa_rol,
                             'strPrefijoEmpresa'   => $prefijoEmpresa
            
        ));       
    }

    /**
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.1 04-07-2017  Se agregan las variables strNombrePais e intIdPais para validar las formas de contacto en actualizarPreCliente
    * @since 1.0
    * @Secure(roles="ROLE_6-5")
    */
    public function updateAction(Request $request, $id)
    {
        $objSession            = $request->getSession();
        $intIdPais             = $objSession->get('intIdPais');
        $strNombrePais         = $objSession->get('strNombrePais');
        $em                    = $this->getDoctrine()->getManager('telconet');
        $entity                = $em->getRepository('schemaBundle:InfoPersona')->find($id);      
        $datos_form            = $request->request->get('preclientetype');
        //print_r($datos_form);die;
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $formas_contacto = array();
        for ($i = 0; $i < count($array_formas_contacto); $i+=3) {
            $formas_contacto[] = array(
                            'formaContacto' => $array_formas_contacto[$i+1],
                            'valor' => $array_formas_contacto[$i+2]
            );
        }
        
        
        $codEmpresa=$request->getSession()->get('idEmpresa');
        $idOficina=$request->getSession()->get('idOficina');
        $usrUltMod=$request->getSession()->get('user');
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $clientIp=$request->getClientIp();        
        $formFormaPago=$request->get('infopersonaempformapagotype');
        if($formFormaPago['bancoTipoCuentaId']=='Seleccione'){
            $formFormaPago['bancoTipoCuentaId']="";          
        }
        if($formFormaPago['tipoCuentaId']=='Seleccione'){
            $formFormaPago['tipoCuentaId']="";
        }
        if(!empty($formFormaPago)){
           $datos_form=array_merge($datos_form,$formFormaPago);
        }
                        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
         try
        {
            $datos_form['strNombrePais'] = $strNombrePais;
            $datos_form['intIdPais']     = $intIdPais;
            /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
            $servicePreCliente           = $this->get('comercial.PreCliente');
            $entityPersonaEmpresaRol     = $servicePreCliente->ActualizarPreCliente($entity, $codEmpresa, $idOficina, $usrUltMod, $clientIp, $datos_form, $prefijoEmpresa);
           
            return $this->redirect($this->generateUrl('precliente_show', array('id' => $entity->getId(),'idper'=>$entityPersonaEmpresaRol->getId()/*,'prefijoEmpresa' => $prefijoEmpresa*/)));                 
    	}
        catch (\Exception $e) {
           
	    $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('precliente_edit', array('id' => $entity->getId())));            
	}           
    }

    public function planCondicionIncumplidaAjaxAction()
    {
        $request = $this->getRequest();
        $idFormaPago=$request->request->get("id_forma_pago");
        $tipoCuenta=$request->request->get("tipoCuenta");
	$bancoTipoCuentaId=$request->request->get("bcoTipoCtaId");
        $persona_empresa_rol_id=$request->request->get("persona_empresa_rol_id");
        $em = $this->getDoctrine()->getManager('telconet');        
        $listado_servicios_incumplidos=$em->getRepository('schemaBundle:InfoServicio')->findByPlanCondicionIncumplida($persona_empresa_rol_id, $idFormaPago, $tipoCuenta, $bancoTipoCuentaId);        
        
        if(!empty($listado_servicios_incumplidos))
        {
               $arreglo=array('msg'=>'Error');
        }
        else
        {
            $arreglo=array('msg'=>'Ok');
        }
        
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;			
    }
    
    /**
    * @Secure(roles="ROLE_6-8")
    */
    public function deleteAction(Request $request, $id)
    {
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoPersona entity.');
            }
         $em->getConnection()->beginTransaction();
 	try{  
            //INACTIVA LA PERSONA
            $entity->setEstado('Inactivo');    
            $em->persist($entity);
            $em->flush();
            //INACTIVA REFERIDO ANTERIOR
            $entityPersonaRef  = $em->getRepository('schemaBundle:InfoPersonaReferido')->findPorPersona($entity->getId()); 
            if ($entityPersonaRef){
                    $entityPersonaRef->setEstado('Inactivo');
                    $em->persist($entityPersonaRef);
                    $em->flush();                    
            }
            //INACTIVA PERSONA_EMPRESA_ROL
            $entityPersonaEmpRol  = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($entity->getId(),'Pre-cliente',$idEmpresa); 
            $entityPersonaEmpRol->setEstado('Inactivo');
                    $em->persist($entityPersonaEmpRol);
                    $em->flush(); 
            
            //REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
            $personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Pre-cliente',$idEmpresa);
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($personaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($request->getSession()->get('user'));
            $em->persist($entity_persona_historial);
            $em->flush();        
            $em->getConnection()->commit();             
        }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            return $this->redirect($this->generateUrl('precliente_show', array('id' => $entity->getId())));            
	}
       }
        return $this->redirect($this->generateUrl('precliente'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function ajaxGetClientesPorNombreAction()
    {
		$request = $this->getRequest();		    
		$filter = $request->request->get("filter");    
		$filtro=$filter['filters'][0]['value'];
		$idEmpresa=$request->getSession()->get('idEmpresa');
                $em = $this->get('doctrine')->getManager('telconet');                

		$datos = $em->getRepository('schemaBundle:InfoPersona')->findClientesPorEmpresaPorEstadoPorNombre('Activo',$idEmpresa,$filtro);
	
		$i=1;
                //
		$arreglo[]= array('Id'=>'','Nombre'=> '');
                
		foreach ($datos as $datos):
				$arreglo[]= array(
				'Id'=>$datos->getId(),
				'Nombre'=> $datos->getNombres()." ".$datos->getApellidos()
                                );             

                                $i++;     
		endforeach;
		if (!empty($arreglo))
			$response = new Response(json_encode(array('tickets'=>$arreglo)));
		else
		{
			$arreglo[]= array(
				'Id'=> "",
				'Nombre'=> ""
			);
			$response = new Response(json_encode(array('tickets'=>$arreglo)));
		}		
		$response->headers->set('Content-type', 'text/json');
		return $response;
    }


    public function delete_ajaxAction()
    {
        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        //echo $id;die;
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id):             
                $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro el prospecto buscado');
                }            
                //INACTIVA LA PERSONA
                $entity->setEstado('Inactivo');    
                $em->persist($entity);
                $em->flush();
                //INACTIVA REFERIDO ANTERIOR
                $entityPersonaRef  = $em->getRepository('schemaBundle:InfoPersonaReferido')->findPorPersona($entity->getId()); 
                if ($entityPersonaRef){
                        $entityPersonaRef->setEstado('Inactivo');
                        $em->persist($entityPersonaRef);
                        $em->flush();                    
                }
                //INACTIVA PERSONA_EMPRESA_ROL
                $entityPersonaEmpRol  = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($entity->getId(),'Pre-cliente',$idEmpresa); 
                if ($entityPersonaEmpRol){    
                    $entityPersonaEmpRol->setEstado('Inactivo');
                    $em->persist($entityPersonaEmpRol);
                    $em->flush();
                }
           endforeach;
           
            //REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
            $personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Pre-cliente',$idEmpresa);
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($personaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($request->getSession()->get('user'));
            $em->persist($entity_persona_historial);
            $em->flush(); 
            
           $em->getConnection()->commit();   
           $respuesta->setContent("Se elimino el registro con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent("error al tratar de eliminar registro. Consulte con el Administrador.");            
	}
       

       return $respuesta;
    }
    
    
    /*form para convertir de prospecto a cliente*/
    public function convertirAction($id_prospecto)
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $entities = $em->getRepository('schemaBundle:InfoPersona')->find($id_prospecto);

		/*Para la carga de la imagen desde el default controller*/
		/*$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		$adminController = new DefaultController();
		$img_opcion = $adminController->getImgOpcion($em_seguridad,'COM-PROS');*/
		$entity = new InfoPersona();
		$options['identificacion']=$entities->getIdentificacionCliente();
                $options['razonSocial']=$entities->getRazonSocial();
                $options['nombres']=$entities->getNombres();
                $options['apellidos']=$entities->getApellidos();
				$options['direccion']=$entities->getDireccion();
                $options['tipoEmpresa']=$entities->getTipoEmpresa();
                $options['tipoIdentificacion']=$entities->getTipoIdentificacion();
                $options['tipoTributario']=$entities->getTipoTributario();
                $options['nacionalidad']=$entities->getNacionalidad(); 
                $options['direccionTributaria']=$entities->getDireccionTributaria();
                $options['calificacionCrediticia']=$entities->getCalificacionCrediticia();
                $options['genero']=$entities->getGenero();
                $options['estadoCivil']=$entities->getEstadoCivil();
                $options['fechaNacimiento']=$entities->getFechaNacimiento();
                $options['representanteLegal']=$entities->getRepresentanteLegal();
                //echo($entities->getTituloId()->getId());die;
                if($entities->getTituloId())
                    $options['titulo']=$entities->getTituloId();
                else
                    $options['titulo']="";
                //echo $options['titulo'];die;
				//print_r($options);die;
		$form   = $this->createForm(new ConvertirType($options), $entity);
		//$form->setDefault('direccionTributaria', $entities->getDireccionTributaria());
		/*Usuario en session*/
        //$user = $this->get('security.context')->getToken()->getUser();
        
        /*Para la carga de la imagen desde el default controller*/
		//$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		//$adminController = new DefaultController();
		//$img_opcion = $adminController->getImgOpcion($em_seguridad,'COM-PROSC');
		
		//Pongo el modulo nuevo en session
		//$request  = $this->get('request');
		//$session  = $request->getSession();
		//$session->set('menu_modulo_activo',"clientes");
		
        return $this->render('comercialBundle:precliente:convertir.html.twig', array(
				'entity' => $entities,
				//'usuario'=> $user->getUsername(),
				'form'   => $form->createView(),
				'direccionTributaria' =>$entities->getDireccionTributaria()
				//'img_opcion_menu'=>$img_opcion
			));
        
        //'img_opcion_menu'=>$img_opcion
    }
    
    public function procesar_prospectoAction($id_prospecto)
    {
	/*Proesar prospecto:
	* - verificar por medio de la identificaion si el mismo ya existe
	* - cambiar el estado a procesado
	* - si no existe guardar la informacion del mismo*/
	$request = $this->getRequest();        
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $idOficina = $request->getSession()->get('idOficina');
        $usrCreacion = $request->getSession()->get('user');
	//$prospecto_convertir=$request->request->get("convertirtype");
	$estadoI='Inactivo';
	$em = $this->getDoctrine()->getManager('telconet');
	$prospecto = $em->getRepository('schemaBundle:InfoPersona')->find($id_prospecto);
        if($prospecto->getIdentificacionCliente())
        {
            $cliente = $em->getRepository('schemaBundle:InfoPersona')->findClientesPorIdentificacion($prospecto->getIdentificacionCliente(), $idEmpresa);
        }
        else
        {
            $cliente = "";
        }
            $options['identificacion']=$prospecto->getIdentificacionCliente();
            $options['razonSocial']=$prospecto->getRazonSocial();
            $options['nombres']=$prospecto->getNombres();
            $options['apellidos']=$prospecto->getApellidos();
            $options['direccion']=$prospecto->getDireccion();
            $options['tipoEmpresa']=$prospecto->getTipoEmpresa();
            $options['tipoIdentificacion']=$prospecto->getTipoIdentificacion();
            $options['tipoTributario']=$prospecto->getTipoTributario();
            $options['nacionalidad']=$prospecto->getNacionalidad(); 
            $options['direccionTributaria']=$prospecto->getDireccionTributaria();
            $options['calificacionCrediticia']=$prospecto->getCalificacionCrediticia();
            $options['genero']=$prospecto->getGenero();
            $options['estadoCivil']=$prospecto->getEstadoCivil();
            $options['fechaNacimiento']=$prospecto->getFechaNacimiento();
            $options['representanteLegal']=$prospecto->getRepresentanteLegal();
            if($prospecto->getTituloId())
                    $options['titulo']=$prospecto->getTituloId()->getId();
            else
                    $options['titulo']="";        
        
        //print_r($cliente);die;
        $datos_form=$request->request->get("convertirtype");
		//print_r($datos_form);die;
		$datos_form_extra=$request->request->get("convertirextratype");
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $a = 0;$x = 0;
        for ($i = 0; $i < count($array_formas_contacto); $i++) {
            if ($a == 3) {$a = 0;$x++;}
            if ($a == 1)$formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if ($a == 2)$formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }

        
        if(empty($cliente))
        {
            $em = $this->getDoctrine()->getManager('telconet');
            $em->getConnection()->beginTransaction();
            //$entity = new InfoPersona();
            $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id_prospecto);
        try {
            //INGRESA EL CLIENTE
            $entity->setTipoIdentificacion($datos_form['tipoIdentificacion']);
			//SI LA IDENTIFICACION ES DIFERENTE A LA QUE YA ESTA INGRESADA LE PERMITE INGRESAR 
			if($prospecto->getIdentificacionCliente()!=$datos_form['identificacionCliente'])
				$entity->setIdentificacionCliente($datos_form['identificacionCliente']);
            $entity->setTipoEmpresa($datos_form['tipoEmpresa']);
            $entity->setTipoTributario($datos_form['tipoTributario']);
            if($datos_form['tipoEmpresa']){
                $entity->setRazonSocial($datos_form['razonSocial']);                
            }
            else
            {
                $entity->setNombres($datos_form['nombres']);
                $entity->setApellidos($datos_form['apellidos']);
                $entityAdmiTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);                
                if ($entityAdmiTitulo)
                    $entity->setTituloId($entityAdmiTitulo);
                $entity->setGenero($datos_form['genero']);
                $entity->setEstadoCivil($datos_form['estadoCivil']);
                if ($datos_form['fechaNacimiento']['year'] && $datos_form['fechaNacimiento']['month'] && $datos_form['fechaNacimiento']['day'])
                    $entity->setFechaNacimiento(date_create($datos_form['fechaNacimiento']['year'].'-'.$datos_form['fechaNacimiento']['month'].'-'.$datos_form['fechaNacimiento']['day']));         
            }
            $entity->setRepresentanteLegal($datos_form['representanteLegal']);
            $entity->setNacionalidad($datos_form['nacionalidad']);
            $entity->setDireccionTributaria($datos_form_extra['direccionTributaria']);
            $entity->setOrigenProspecto('S');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($usrCreacion);
            $entity->setIpCreacion($request->getClientIp());
            $entity->setEstado('Pendiente');            
            $em->persist($entity);
            $em->flush();
            //ASIGNA ROL DE CLIENTE A LA PERSONA
            $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
            $entityEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPorNombreTipoRolPorEmpresa('Cliente', $idEmpresa);
            $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
            $entityPersonaEmpresaRol->setPersonaId($entity);
            $entityOficina = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($idOficina);
            $entityPersonaEmpresaRol->setOficinaId($entityOficina);
            $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
            $entityPersonaEmpresaRol->setUsrCreacion($usrCreacion);
            $entityPersonaEmpresaRol->setEstado('Activo');
            $em->persist($entityPersonaEmpresaRol);
            $em->flush();
			//echo "proceso 0";die;            			
            //CAMBIA ESTADO DEL ROL DE PROSPECTO A INACTIVO
            $entityPersonaEmpresaRolPros=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorPersonaPorTipoRol($id_prospecto, 'Pre-cliente',$idEmpresa);
			//echo "proceso 0.1";die;            			
            $entityPersonaEmpresaRolPros->setEstado('Inactivo');
            $em->persist($entityPersonaEmpresaRolPros);
            $em->flush();
            //ACTUALIZA EL REFERIDO
            $referido = $em->getRepository('schemaBundle:InfoPersonaReferido')->findOneBy(
                    array( "personaEmpresaRolId" => $entityPersonaEmpresaRolPros->getId(), "estado" => "Activo"));
            if($referido){
                //echo "entro a referido";die;
                $referido->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                $em->persist($referido);
                $em->flush();                
            }
            //echo "paso a referido";die;
            
			//echo "proceso 1";die;

			//Actualiza el presonaEmpresaRolId en cada uno de los puntos
			$puntosProspecto=$em->getRepository('schemaBundle:InfoPunto')
			->findByPersonaEmpresaRolId($entityPersonaEmpresaRolPros->getId());
			if ($puntosProspecto){
			foreach($puntosProspecto as $punto):
				$punto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
				$punto->setUsrUltMod($request->getSession()->get('user'));
				$punto->setFeUltMod(new \DateTime('now'));
				$em->persist($punto);
				$em->flush();			
			endforeach;
			}
			//echo "proceso 2";die;
			//echo $entityPersonaEmpresaRolPros->getId();die;			
			//Actualiza el personaEMpresaRol en el contrato
			$entityContrato=$em->getRepository('schemaBundle:InfoContrato')
			->findByPersonaEmpresaRolId($entityPersonaEmpresaRolPros->getId());
                        foreach($entityContrato as $contrato){
                            $contrato->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                            $em->persist($contrato);
                            $em->flush();			
                        }			
			//echo "proceso 3";die;

            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            /* @var $serviceInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
            $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            $serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entity->getId(), $request->getSession()->get('user'));

            //echo "proceso 4";die;
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
                $entity_persona_forma_contacto->setUsrCreacion($request->getSession()->get('user'));
                $em->persist($entity_persona_forma_contacto);
                $em->flush();
            }
			//echo "proceso 5";die;
            //REGISTRA EL ESTADO CONVERTIDO DEL PROSPECTO
            //$personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
            //        ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($entity->getId(),'Pre-cliente',$idEmpresa);
				//echo "sub-proceso 6";die;	
            $entity_persona_historial_prosp = new InfoPersonaEmpresaRolHisto();
			//echo "sub-sub-proceso 6";die;	
            $entity_persona_historial_prosp->setEstado('Convertido');
            $entity_persona_historial_prosp->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial_prosp->setIpCreacion($request->getClientIp());
			//echo "sub-sub-sub-proceso 6";die;
			//echo 'objeto:';
			//print_r($personaEmpresaRol);die;
            //$entity_persona_historial_prosp->setPersonaEmpresaRolId($personaEmpresaRol);
			$entity_persona_historial_prosp->setPersonaEmpresaRolId($entityPersonaEmpresaRolPros);
			
			//echo "sub-sub-sub-proceso 6";die;
            $entity_persona_historial_prosp->setUsrCreacion($usrCreacion);
            $em->persist($entity_persona_historial_prosp);
            $em->flush(); 
			//echo "proceso 6";die;
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL DEL CLIENTE
            $personaEmpresaRolCli=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($entity->getId(),'Cliente',$idEmpresa);   
			//echo "sub-proceso 7";die;	
            $entity_persona_historial_cli = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial_cli->setEstado($personaEmpresaRolCli->getEstado());
            $entity_persona_historial_cli->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial_cli->setIpCreacion($request->getClientIp());
            $entity_persona_historial_cli->setPersonaEmpresaRolId($personaEmpresaRolCli);
            $entity_persona_historial_cli->setUsrCreacion($usrCreacion);
            $em->persist($entity_persona_historial_cli);
            $em->flush();
			//echo "proceso 7";die;			
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('cliente_show', array('id' => $entity->getId(),'idper'=>$personaEmpresaRolCli->getId())));
        } catch (\Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //aqu? alg?n mensaje con la excepci?n concatenada
    
            //$form   = $this->createForm(new ConvertirType($options), $entity);
			$this->get('session')->getFlashBag()->add('notice', $e->getMessage());
			//return $this->redirect($this->generateUrl('cliente_show', array('id' => $entity->getId())));
			return $this->redirect($this->generateUrl('precliente_convertir', array('id_prospecto' => $entity->getId())));
        }                                  
        }
        else
        {
            $form   = $this->createForm(new ConvertirType($options), $prospecto);
            return $this->render('comercialBundle:precliente:convertir.html.twig', array(
				'entity' => $prospecto,
                                'error' => 'Ya existe un cliente con la misma identificacion, por favor corregir y volver a intentar',
				//'usuario'=> $user->getUsername(),
				'form'   => $form->createView(),
				'direccionTributaria' => '',
				//'img_opcion_menu'=>$img_opcion
			));            
            
        }
    }
    
        //funcion para dashboard
	public function ajaxProspectosPorTipoEmpresaMesAction()
	{
		$request = $this->getRequest();
		$session  = $request->getSession();
                $idEmpresa=$session->get('idEmpresa');
                $em = $this->get('doctrine')->getManager('telconet');				
		$codigoEstado=$request->query->get('est');
		
		$fechaActual=date('l Y');
		$fechaActual="1 ".$fechaActual;
		$fechaComparacion = strtotime($fechaActual);
                $calculo= strtotime("31 days", $fechaComparacion); //Le aumentamos 31 dias
                $fechaFin= date("Y-m-d", $calculo);
                $fechaIni= date('Y-m')."-01";
		$prospecto= $em->getRepository('schemaBundle:InfoPersona')->findProspectosAgrupadosPorTipoEmpresa($idEmpresa,$fechaIni,$fechaFin);
				
		foreach($prospecto as $dato){	
			$arreglo[]= array(
				'name'=> sprintf("%s",$dato['tipoEmpresa']),
				'data1'=> sprintf("%s",$dato['total'])
				);  
		}	
		if (empty($arreglo)){
			$arreglo[]= array(
				'name'=> "",
				'data1'=> ""
				);  
		}
		$response = new Response(json_encode(array('prospectos'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;	
	}    
        
	public function obtieneIdPersonaEmpresaRolPorIdPersona($idPersona,$idEmpresa,$em){
			$tieneCancelado=false;
			$tieneOtroEstado=false;
			$datosPersonaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
			->getPersonaEmpresaRolPorPersonaPorTipoRolTodos($idPersona,'Pre-cliente',$idEmpresa);
			foreach($datosPersonaEmpresaRol as $per){
				if(($per->getEstado()=='Cancelado')||($per->getEstado()=='Cancel')){
					$tieneCancelado=true;
					$personaEmpresaRolCancelado=$per;
				}else
				{
					$tieneOtroEstado=true;
					$personaEmpresaRolOtroEstado=$per;
				}		
			}
			if($tieneCancelado && $tieneOtroEstado)
			{
				$personaEmpresaRol=$personaEmpresaRolOtroEstado;        				
			}elseif($tieneCancelado && !$tieneOtroEstado)
			{
				$personaEmpresaRol=$personaEmpresaRolCancelado;
			}else
			{
				$personaEmpresaRol=$personaEmpresaRolOtroEstado;
			}	
			return $personaEmpresaRol;
	}    
    
    /**
     * @Secure(roles="ROLE_6-1697")
     * Documentación para funcion 'editarNombrePersona'.
     * Esta funcion edita el nombre de la persona
     * @author <amontero@telconet.ec>
     * @version 1.0
     * @since 25/08/2014
     * 
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 15-07-2016 
     * Se agrega a la opcion de edicion de nombre o Razon Social la edicion de Representante Legal
     * Tipo Tributario, y Tipo Empresa, se envia en arreglo los parametros para la actualizacion de la informacion.
     * 
     * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 08-08-2016 
     * Se agrega generacion del Historico en la opcion de edicion, se aumenta edicion de Oficina de Facturacion
     * Se envia en arreglo de parametros el id_persona_rol, usuario_creacion, ip_creacion
     * @param array $arrayParametros    - intIdPersona
     *                                  - intIdPersonaRol
    *                                   - intIdOficina
     *                                  - strTipoEmpresa
     *                                  - strNombres            
     *                                  - strApellidos
     *                                  - strRazonSocial
     *                                  - strRepresentanteLegal
     *                                  - strTipoEmpresaNuevo
     *                                  - strTipoTributarioNuevo        
     *                                  - strUsrCreacion
     *                                  - strIpCreacion 
     * @return objeto response     
     */
    public function editarNombrePersonaAction()
    {
        $request                = $this->getRequest();
        $intIdPersona           = $request->request->get('idPersona');
        $intIdPersonaRol        = $request->request->get('idPersonaRol');
        $intIdOficina           = $request->request->get('idOficina');
        $strTipoEmpresa         = $request->request->get('tipoEmpresa');
        $strNombres             = $request->get('nombre');
        $strApellidos           = $request->request->get('apellido');
        $strRazonSocial         = $request->request->get('razonsocial');
        $strRepresentanteLegal  = $request->request->get('representanteLegal');
        $strTipoEmpresaNuevo    = $request->request->get('tipoEmpresaNuevo');
        $strTipoTributarioNuevo = $request->request->get('tipoTributarioNuevo');        
        $objSesion              = $request->getSession();
        
        $arrayParametros        = array('intIdPersona'           => $intIdPersona,
                                        'intIdPersonaRol'        => $intIdPersonaRol,
                                        'intIdOficina'           => $intIdOficina,
                                        'strTipoEmpresa'         => $strTipoEmpresa,
                                        'strNombres'             => $strNombres,
                                        'strApellidos'           => $strApellidos,
                                        'strRazonSocial'         => $strRazonSocial,
                                        'strRepresentanteLegal'  => $strRepresentanteLegal,
                                        'strTipoEmpresaNuevo'    => $strTipoEmpresaNuevo,
                                        'strTipoTributarioNuevo' => $strTipoTributarioNuevo,
                                        'strUsrCreacion'         => $objSesion->get('user'),
                                        'strIpCreacion'          => $request->getClientIp()
                                       );
        
        $servicePersona = $this->get('comercial.InfoPersona');
        $response       = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent(json_encode($servicePersona->editaNombrePersona($arrayParametros)));                   
        return $response;
    }

    /**
     * Documentación para funcion 'getListaOficinasEmpresa'.
     * Esta funcion lista las oficinas según la empresa en sesion
     * @author <eholguin@telconet.ec>
     * @since 10/12/2015
     * @return objeto response
    */    
    public function getListaOficinasEmpresaAction()
    {
        $objRequest          = $this->getRequest();
        $intIdEmpresa        = $objRequest->getSession()->get('idEmpresa');
        $objServiceComercial = $this->get('comercial.Comercial');
        $arrayOficinas       = $objServiceComercial->getOficinasPorEmpresa($intIdEmpresa);
        $objResponse         = new Response(json_encode(array('oficinas'=>$arrayOficinas)));
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }

   /**
     *
     * Documentación para la función 'getInformacionClienteAction'.
     *
     * Función que renderiza la página de Ver detalle de clientes.
     *
     * @return render - Página de Ver detalle de clientes.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 24-05-2021
     *
     */
    public function getInformacionClienteAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')   ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        try
        {
            if( $this->get('security.context')->isGranted('ROLE_6-1') )
            {
                $arrayRolesPermitidos[] = 'ROLE_6-1';
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 
                                      'PreClienteController.getInformacionClienteAction', 
                                      $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:precliente:indexInformacionClt.html.twig', array('rolesPermitidos'  => $arrayRolesPermitidos));
    }

   /**
     * @Secure(roles="ROLE_6-1")
     *
     * Documentación para la función 'gridInformacionClienteAction'.
     *
     * Función que retorna el listado de clientes.
     *
     * @return $objResponse - Listado de clientes.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 24-05-2021
     *
     */
    public function gridInformacionClienteAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strIdentificacion      = $objRequest->get("strIdentificacion")   ? $objRequest->get("strIdentificacion"):"";
        $strRazonSocial         = $objRequest->get("strRazonSocial")      ? $objRequest->get("strRazonSocial"):"";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa')      ? $objSession->get('prefijoEmpresa'):"";
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $intTotal               = 0;
        $arrayRespuesta         = array();
        $serviceComercialCrm    = $this->get('comercial.ComercialCRM');
        try
        {
            if(!empty($strIdentificacion) || !empty($strRazonSocial))
            {
                $strRol                               = "Cliente";
                $strCantPropuestasCrm                 = 0;
                $arrayParametros                      = array();
                $arrayParametros['estado']            = "Activo";
                $arrayParametros['idEmpresa']         = $intIdEmpresa;
                $arrayParametros['razon_social']      = $strRazonSocial;
                $arrayParametros['identificacion']    = $strIdentificacion;
                $arrayParametros['tipo_persona']      = array('cliente');
                $arrayParametros['strModulo']         = 'Cliente';
                $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                $arrayResultado                       = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->findPersonasPorCriterios($arrayParametros);
                $arrayRegistros                       = $arrayResultado['registros'];
                $intTotal                             = $arrayResultado['total'];
                if(empty($arrayRegistros) || $intTotal == 0)
                {
                    $strRol                               = "Pre-Cliente";
                    $arrayParametros['tipo_persona']      = array('pre-cliente');
                    $arrayParametros['strModulo']         = 'Pre-Cliente';
                    $arrayResultado                       = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->findPersonasPorCriterios($arrayParametros);
                    $arrayRegistros                       = $arrayResultado['registros'];
                    $intTotal                             = $arrayResultado['total'];
                }
                if(!empty($arrayRegistros) && is_array($arrayRegistros))
                {
                    foreach($arrayRegistros as $arrayItem)
                    {
                        $arrayParametrosCRM   = array("strIdentificacion"  => $arrayItem['identificacion']);
                        $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCRM,
                                                      "strOp"              => 'getDatosCliente',
                                                      "strFuncion"         => 'procesar');
                        $arrayRespuestaWSCrm  = $serviceComercialCrm->getRequestCRM($arrayParametrosWSCrm);
                        if(!empty($arrayRespuestaWSCrm) && (is_array($arrayRespuestaWSCrm["resultado"]) 
                        && !empty($arrayRespuestaWSCrm["resultado"])))
                        {
                            $strRol              .= ' /TelcoCRM';
                            $arrayItemCrm         = $arrayRespuestaWSCrm["resultado"][0];
                            $strCantPropuestasCrm = $arrayItemCrm->strPropuestaCRM;
                        }
                        $arrayFechaEmision  = $emComercial->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                          ->getUltimaFacturaPorPersonaEmpresaRol($arrayItem['id']);
                        $strFechaEmision = '';
                        if( !empty($arrayFechaEmision) )
                        {
                            $arrayFechaEmision = $arrayFechaEmision[0];
                            $strFechaEmision   = $arrayFechaEmision['fechaEmision'];
                        }
                        $floatSaldoPendiente = 0;
                        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
                        {
                            $arraySaldoPendiente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $arrayItem['id'],
                                                                                          "strPrefijoEmpresa"     => $strPrefijoEmpresa));
                            if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
                              !empty($arraySaldoPendiente["floatSaldoPendiente"]))
                            {
                                $floatSaldoPendiente = $arraySaldoPendiente["floatSaldoPendiente"];
                            }
                        }
                        $arrayRespuesta[] = array('intIdPersona'           => $arrayItem["persona_id"],
                                                  'strIdentificacion'      => $arrayItem['identificacion'],
                                                  'strRazonSocial'         => $arrayItem['razon_social'],
                                                  'strUsrVendedor'         => $arrayItem['vendedor'],
                                                  'strDireccion'           => $arrayItem['direccion_tributaria'],
                                                  'strFechaCreacion'       => strval(date_format($arrayItem['fe_creacion'],"d/m/Y G:i")),
                                                  'strFechaUltEmision'     => $strFechaEmision,
                                                  'strSaldoPendiente'      => "$ ".$floatSaldoPendiente,
                                                  'strEstado'              => $arrayItem['estado'],
                                                  'strRol'                 => $strRol,
                                                  'strPropuestaCRM'        => $strCantPropuestasCrm);
                    }
                }
                else
                {
                    $arrayParametrosCRM   = array("strIdentificacion"  => $strIdentificacion,
                                                  "strRazonSocial"     => $strRazonSocial);
                    $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCRM,
                                                  "strOp"              => 'getDatosCliente',
                                                  "strFuncion"         => 'procesar');
                    $arrayRespuestaWSCrm  = $serviceComercialCrm->getRequestCRM($arrayParametrosWSCrm);
                    if(!empty($arrayRespuestaWSCrm) && (is_array($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"])))
                    {
                        $arrayItem        = $arrayRespuestaWSCrm["resultado"][0];
                        $arrayRespuesta[] = array('intIdPersona'           => "",
                                                  'strIdentificacion'      => $arrayItem->strIdentificacion,
                                                  'strRazonSocial'         => $arrayItem->strRazonSocial,
                                                  'strUsrVendedor'         => $arrayItem->strUsrVendedor,
                                                  'strDireccion'           => $arrayItem->strDireccion,
                                                  'strFechaCreacion'       => date("d/m/Y G:i", strtotime($arrayItem->strFechaCreacion)),
                                                  'strFechaUltEmision'     => "",
                                                  'strSaldoPendiente'      => "",
                                                  'strEstado'              => "Activo",
                                                  'strRol'                 => 'TelcoCRM',
                                                  'strPropuestaCRM'        => $arrayItem->strPropuestaCRM);
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+',
                                      'PreClienteController.gridInformacionClienteAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayRespuesta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para funcion 'callWsPrefactibilidadAjaxAction'.
     * Esta funcion realiza un llamado al Web Service de Prefactbilidad
     * para realizar una consulta
     *
     * @return Objeto Response     
     * @throws Exception    
     * 
     * @author Andrea Cárdenas  <ascardenas@telconet.ec>
     * @version 1.0 11-06-2021
    */

    public function callWsPrefactibilidadAjaxAction()
    {

        $objRequest             = $this->get('request');
        $strCodEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa      = $objRequest->getSession()->get('prefijoEmpresa');
        $strUsrCreacion         = $objRequest->getSession()->get('user');
        $strClientIp            = $objRequest->getClientIp();
        $arrayDatosForm         = json_decode($objRequest->get('preclientetype'),true);
        $serviceUtil            = $this->get('schema.Util');
        $arrayResultadoConsulta = array();
        $serviceTokenCas        = $this->get('seguridad.TokenCas');
        $arrayTokenCas          = $serviceTokenCas->generarTokenCas();

        if (empty($arrayTokenCas['strToken'])) 
        {
            throw new \Exception($arrayTokenCas['strMensaje']);
        }


        try
        {
            
            
            $arrayParametrosPrefactibilidad  =  array('strCodEmpresa'      => $strCodEmpresa,
                                                      'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                                      'strUsrCreacion'       => $strUsrCreacion,
                                                      'strClientIp'          => $strClientIp,
                                                      'strOrigenWeb'         => 'S',
                                                      'token'                => $arrayTokenCas['strToken'],
                                                      'strCanal'             =>'Telcos Web',
                                                      'arrayDatosForm'       => $arrayDatosForm);

            $servicePreCliente          = $this->get('comercial.PreCliente');
            $arrayResultadoConsulta = $servicePreCliente->callWebServicePrefactibilidad($arrayParametrosPrefactibilidad);

            
        }
        catch(\Exception $e)
        {
         
            $serviceUtil->insertError('TelcoS+',
                                      'PreClienteController.callWsPrefactibilidadAjaxAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strClientIp);
                  
        }

        $objResponse = new Response(json_encode($arrayResultadoConsulta));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     *
     * Documentación para la función 'prospectoFormularioAction'.
     *
     * Función que renderiza la página de Formulario de aceptación de política de mejora de la experiencia.
     *
     * @return render - Página de Formulario de prospecto.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-10-2022
     *
     */
    public function prospectoFormularioAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')   ? $objSession->get('user'):"";
        $strEmpresaCod          = $objRequest->getSession()->get("idEmpresa"); 
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        try
        {
            if( $this->get('security.context')->isGranted('ROLE_6-1') )
            {
                $arrayRolesPermitidos[] = 'ROLE_6-1';
            }
            $arrayAdmiParametro = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne("PARAMETROS_FORMULARIOS_COMERCIAL_CREDENCIAL", "", "",
                                             "ENVIO_WHATSAPP","", "", "", "","flujo de prospectos",
                                             $strEmpresaCod);
            error_log("parametro " . json_encode($arrayAdmiParametro) );                                
            $strEnviaWhatsapp = 'NO';
            if($arrayAdmiParametro['valor1'])
            {
                $strEnviaWhatsapp = $arrayAdmiParametro['valor1'];
            }

        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 
                                      'PreClienteController.prospectoFormularioAction', 
                                      $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:precliente:indexProspectoFormulario.html.twig', array('rolesPermitidos'  => $arrayRolesPermitidos,
                                                                                                    'enviaWhatsapp' => $strEnviaWhatsapp));
    }

}
