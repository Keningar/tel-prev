<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoServicioComision;
use telconet\schemaBundle\Entity\AdmiTipoMedio;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Form\InfoOrdenTrabajoType;
use telconet\schemaBundle\Form\InfoServicioType;

use telconet\schemaBundle\Entity\InfoServicioRecursoCab;
use telconet\schemaBundle\Entity\InfoServicioRecursoDet;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * InfoOrdenTrabajo controller.
 *
 */
class InfoServicioController extends Controller
{
    const STR_DOCTRINE      = 'doctrine';
    const STR_EM_COMERCIAL  = 'telconet';
    const STR_EM_GENERAL    = 'telconet_general';
    const STR_DESCRIPCION   = 'descripcion';
    const STR_ESTADO        = 'estado';
    const STR_ACTIVO        = 'Activo';
    const STR_PUNTO_ID      = 'puntoId';
    const STR_PRODUCTO_ID   = 'productoId';
    const STR_ID_PUNTO      = 'idPunto';
    const VALOR_INICIAL_BUSQUEDA = 0;
    const VALOR_LIMITE_BUSQUEDA  = 10;

    /**
     * Lists all InfoOrdenTrabajo entities.
     *
     */
    public function indexAction()
    {
		$request=$this->get('request');
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		
        $em = $this->getDoctrine()->getManager();

        if($ptocliente)
			$presentar="S";
		else
			$presentar="N";
		
		$parametros=array(
            //'entities' => $entities,
            'orden_servicio'=>$presentar
        );
        
        if($ptocliente)
        {
			$parametros['punto_id']=$ptocliente;
			$parametros['cliente']=$cliente;
		}
		
        return $this->render('comercialBundle:infoservicio:index.html.twig',$parametros);
    }

    /**
     * Documentación para el método 'showAction'.
     *
     * Finds and displays a InfoOrdenTrabajo entity.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 18-12-2015
     * @since   1.0
     * Se envía el parámetro "rol" para el routing de infoservicio_new
     * 
     * @param int $id Id del Servicio
     * @return Render Pantalla Show del Servicio
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$request=$this->get('request');
		//Debo listar todos los servicios del punto
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		$empresa=$session->get('idEmpresa');
		$oficina=$session->get('idOficina');
		$user=$session->get('user');
				$entitypunto=$em->getRepository('schemaBundle:InfoPunto')
				->find($ptocliente['id']);
				$entityrol=$em->getRepository('schemaBundle:AdmiRol')
				->find($entitypunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
				if(strtoupper($entityrol->getDescripcionRol())=='PRE-CLIENTE')
					$estado="Pre-servicio";
				else					
					$estado="Pre-servicio";
		$listado_servicios = $em->getRepository('schemaBundle:InfoServicio')->findTodosServiciosNoOrden($empresa,$ptocliente['id'],$estado);
        $servicios=$listado_servicios['registros'];
        //$servicios = $em->getRepository('schemaBundle:InfoServicio')->findById($id);

        //Presentacion del listado de servicios si los mismo estan enlazados
        //se debe presentar los diferentes a Anulado
        //funcion q devuelve dif al estado enviado
        $estado="Inactivo";
        //$servicios=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoIdYEstado($id,$estado);
        //$servicios=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($id);
        
        if (!$servicios) {
            throw $this->createNotFoundException('Unable to find InfoServicio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $parametros=array(
            //'entity'      => $entity,
            'delete_form' => $deleteForm->createView());
		
        if($servicios)
        {
            $i=0;
            
            foreach($servicios as $servicio)
            {
				$variable_plan="";
				//echo $servicio.planId;
				//print_r($servicio);
                if($servicio->getProductoId()!="")
                {
                    $info_plan_prod=$servicio->getProductoId()->getDescripcionProducto();
                    $arreglo[$i]['tienedetalle']="N";
				}
                else
                {
                    $info_plan_prod=$servicio->getPlanId()->getNombrePlan();
                    $variable_plan="S";
                }
                
                $arreglo[$i]['producto']=$info_plan_prod;
                $arreglo[$i]['cantidad']=$servicio->getCantidad();
                $arreglo[$i]['precio']=$servicio->getPrecioVenta();
                
                if($variable_plan=="S")
                {
					$detalle=$this->listarDetallePlan($servicio->getPlanId());
					if($detalle)
					{
						$arreglo[$i]['detalle']=$detalle;
						$arreglo[$i]['tienedetalle']="S";
					}
					else
						$arreglo[$i]['tienedetalle']="N";
				}
                $i++;
            }
            if(isset($arreglo))
				$parametros['servicios']=$arreglo;
			else
				$parametros['servicios']="";
        }
        $parametros['id']  = $id;
        $parametros['rol'] = $entityrol->getDescripcionRol();

        return $this->render('comercialBundle:infoservicio:show.html.twig', $parametros);
    }

	public function listarDetallePlan($idPlan)
	{
		$em = $this->getDoctrine()->getManager();
		$listado_productos=$em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($idPlan);
	
		$arreglo=array();
		$i=0;
		foreach($listado_productos as $prod)
		{
			$infoProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($prod->getProductoId());
			$arreglo[$i]['producto']=$infoProducto->getDescripcionProducto();
			$i++;
		}
		
		return $arreglo;
	}

    /**
     * Funcion utilizada para redireccionar a pantalla de traslado 
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-01-2016 Se crea funcion que obtiene los parametros utilizados 
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 12-09-2016 
     * Se reciben los parámetros $id y $rol para definir el botón "Regresar" al Login en sesión.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 23-05-2017
     * Se agrega al arreglo de parametros el id_persona_rol INFO_PERSONA_EMPRESA_ROL
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 27-10-2017
     * @since 1.2
     * Se agrega parametro prefijo empresa para proceso de traslado TN
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 17-08-2022 - Se agregan motivos para el ingreso de la solicitud.
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.5 16-02-2023 - Se añade una validación para que si la solicitud proviene del nuevo flujo ‘Proceso Traslado’ se actualizará 
     *                           la sesión con los datos del nuevo punto, además se añade otros parámetros para la renderización de la página.
     * @return Redireccionamiento de pantalla
     */
    public function trasladarServiciosAction($id, $rol)
    {
        $request         = $this->get('request');
        $objSession      = $request->getSession();
        $arrayCliente    = $objSession->get('cliente');
        $arrayPtocliente = $objSession->get('ptoCliente');
        $emComercial     = $this->getDoctrine()->getManager();
        $strEmpresaCod   = $objSession->get('prefijoEmpresa');
        $intCodEmpresa   = $objSession->get('idEmpresa');
        
        if ($strEmpresaCod == "TN")
        {
            $arraySolicitudTraslado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->getSolicitudPorPunto( $arrayPtocliente['id'],
                    'SOLICITUD TRASLADO',
                    array('Pendiente',
                        'Aprobado'));
            if (count($arraySolicitudTraslado)>0)
            {
                $objSession->getFlashBag()->add('error', "El punto ya tiene solicitudes de traslado pendientes de gestionar");
                return $this->redirect($this->generateUrl('infopunto_show', array('id' => $arrayPtocliente['id'], 'rol'=> $rol), true));
            }
        }

        $strTipoProceso = $request->query->get('strTipo');
        if($strTipoProceso == 'continuo')
        {
            $emComercial->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($id,$objSession);
          $objSession->set('numServicios', '');
          $objSession->set('serviciosPunto', '');
            $arrayCliente    = $objSession->get('cliente');
            $arrayPtocliente = $objSession->get('ptoCliente');
         

        }

        $arrayParametros = $this->trasladarServiciosInfo($arrayPtocliente, $arrayCliente, "");
        $arrayParametros["punto_id"]["direccion"] = str_replace(array("\n", "\r"), '', trim($arrayParametros["punto_id"]["direccion"]));
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");
        $objAdmiMotivos  = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findBy(array("estado"            => "Activo",
            "relacionSistemaId" => "11054"));
        foreach ($objAdmiMotivos as $objItemMotivo)
        {
            $arrayMotivos[] = array("intIdMotivos" => $objItemMotivo->getId(),
                "strMotivos"   => $objItemMotivo->getNombreMotivo());
        }

        $strLoginAnterior     = '';
        $strDireccionAnterior = '';
        $intIdPuntoAnterior   = $request->query->get('intIdPuntoAnterior');

        if ($strTipoProceso == 'continuo')
        {
$objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPuntoAnterior);

            if($objInfoPunto)
            {
                $strLoginAnterior = $objInfoPunto->getLogin();
                $strDireccionAnterior = str_replace(array("\n", "\r"), '',trim($objInfoPunto->getDireccion()));
            }

        }

        $arrayParametros['id']                = $id;
        $arrayParametros['rol']               = $rol;
        $arrayParametros['intIdPersonaRol']   = $arrayCliente['id_persona_empresa_rol'];
        $arrayParametros['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa');
        $arrayParametros['arrayMotivos']      = (is_array($arrayMotivos) && !empty($arrayMotivos)) ? $arrayMotivos:array();
        $arrayParametros['strTipoProceso']    = $strTipoProceso;
        $arrayParametros['strLoginAnterior']  = $strLoginAnterior;
        $arrayParametros['strDireccionAnterior'] = $strDireccionAnterior;
        $arrayParametros['intIdPuntoAnterior']   = $intIdPuntoAnterior;

        return $this->render('comercialBundle:infoservicio:traslado.html.twig', $arrayParametros);
    }
    
    /**
     * reubicacionServicios
     * 
     * Funcion utilizada para redireccionar a pantalla de reubicacion 
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-01-2018
     * @since 1.0
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 17-08-2022 - Se agregan motivos para el ingreso de la solicitud.
     *
     * @return Redireccionamiento de pantalla
     */
    public function reubicarServiciosAction($id, $rol)
    {
        $request         = $this->get('request');
        $session         = $request->getSession();
        $cliente         = $session->get('cliente');
        $ptocliente      = $session->get('ptoCliente');
        $strPrefijoEmp   = $session->get('prefijoEmpresa');
        $emComercial     = $this->getDoctrine()->getManager();
        if ($strPrefijoEmp == "TN")
        {
            //validar que no exista una solicitud en proceso
            $arraySolicitudReubicacion = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->getSolicitudPorPunto( $ptocliente['id'], 
                                                                             'SOLICITUD REUBICACION', 
                                                                             array('Planificada',
                                                                                   'Pendiente',
                                                                                   'Aprobado',
                                                                                   'PrePlanificada',
                                                                                   'Replanificada',
                                                                                   'AsignadoTarea',
                                                                                   'Asignada',
                                                                                   'Detenido'));
            
            if (count($arraySolicitudReubicacion)>0)
           {
               $session->getFlashBag()->add('error', "El punto ya tiene solicitudes de reubicacion pendientes de gestionar");
               return $this->redirect($this->generateUrl('infopunto_show', array('id' => $ptocliente['id'], 'rol'=> $rol), true));
           }
        }
        
        $arrayParametros = $this->trasladarServiciosInfo($ptocliente, $cliente, "");
        $arrayMotivos    = array();
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");
        $objAdmiMotivos  = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findBy(array("estado"            => "Activo",
                                                                                              "relacionSistemaId" => "11054"));
        foreach ($objAdmiMotivos as $objItemMotivo)
        {
            $arrayMotivos[] = array("intIdMotivos" => $objItemMotivo->getId(),
                                    "strMotivos"   => $objItemMotivo->getNombreMotivo());
        }
        $arrayParametros['id']                = $id;
        $arrayParametros['rol']               = $rol;
        $arrayParametros['intIdPersonaRol']   = $cliente['id_persona_empresa_rol'];
        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmp;
        $arrayParametros['arrayMotivos']      = $arrayMotivos;

        return $this->render('comercialBundle:infoservicio:reubicacion.html.twig', $arrayParametros);
    }

    /**
     * Funcion utilizada para recuperar parametros necesarios en redireccionamiento a pantalla de traslado
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-01-2016 Se crea funcion que obtiene los parametros utilizados 
     * 
     * 
     * @return Redireccionamiento de pantalla
     */
    public function trasladarServiciosInfo($arrayPuntoCliente, $arrayCliente, $strMensajeError)
    {
        $arrayParametros             = array();
        $arrayParametros['punto_id'] = $arrayPuntoCliente;
        $arrayParametros['cliente']  = $arrayCliente;
        $em                          = $this->getDoctrine()->getManager();
        try
        {
            if($arrayPuntoCliente)
            {
                $objTipoNegocio              = $em->getRepository('schemaBundle:AdmiTipoNegocio')->find($arrayPuntoCliente['id_tipo_negocio']);
                if (strtoupper(trim($objTipoNegocio->getNombreTipoNegocio())) == 'HOME')
                {
                    $strNombreTipoNegocio = $objTipoNegocio->getNombreTipoNegocio();
                    $strNombreTipoNegocioNo = 'PYME';
                }
                elseif (strtoupper(trim($objTipoNegocio->getNombreTipoNegocio())) == 'PYME')
                {
                    $strNombreTipoNegocio = $objTipoNegocio->getNombreTipoNegocio();
                    $strNombreTipoNegocioNo = 'HOME';
                }
                else
                {
                    $strNombreTipoNegocio = $objTipoNegocio->getNombreTipoNegocio();
                    $strNombreTipoNegocioNo = '';
                }
                $arrayParametros['nombre_tipo_negocio']    = $strNombreTipoNegocio;
                $arrayParametros['nombre_tipo_negocio_no'] = $strNombreTipoNegocioNo;
                $arrayParametros['mensajeError']           = $strMensajeError;
            }
        }
        catch (Exception $exc)
        {
            $mensajeError = "Error: " . $exc->getMessage();
            error_log($mensajeError);
        }
        
        
        return $arrayParametros;
    }

     /**
     * Funcion generarSolicitudTraslado se encarga de crear una solicitud de traslado que necesita una autorizacion de cobranzas, debido a que
     *         son creadas a partir de puntos que presentan deuda
     *
     * @author Creado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 Version Inicial
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 17-08-2022 - Se guardan los motivos para el ingreso de la solicitud y se envía notificaciones.
     *
     * return JSON $objResponse
     */
    public function generarSolicitudTrasladoAction()
    {
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $emComercial = $this->getDoctrine()->getManager();
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');

        $objResponse            = new JsonResponse();
        $serviceCambioPlan      = $this->get('tecnico.InfoCambiarPlan');
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $strPrecioTraslado      = $objRequest->get('precioTraslado');
        $strDescripcionTraslado = $objRequest->get('descripcionTraslado');
        $strServiciosATrasladar = $objRequest->get('idsServiciosTrasladar');
        $strTipoNegocio         = $objRequest->get('tipoNegocio');
        $intIdMotivo            = $objRequest->get('intIdMotivo') ? $objRequest->get('intIdMotivo'):"";
        $serviceUtil            = $this->get('schema.Util');
        $intIdPunto             = $objRequest->get("idPunto");
        $arrayPuntoCliente      = $objSession->get('ptoCliente');
        $arrayServicios         = explode(",", $strServiciosATrasladar);
        $intIdPuntoSession      = $arrayPuntoCliente['id'];
        $strUsrCreacion         = $objSession->get('user');
        $strCodigoEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strIdDepartamento      = $objSession->get('idDepartamento');
        $strIpCreacion          = $objRequest->getClientIp();
        $arrayEmpleadoJefe      = array();
        $strLogin               = "";
        $intIdServicio          = "";
        $strEstadoRespuesta     = "";
        $strMensajeRespuesta    = "";
        $strCanton              = "";
        $strParametroCiudad     = "GYE";

        try
        {
            $emComercial->getConnection()->beginTransaction();

            $objTipoSolicitud  = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                             ->findOneByDescripcionSolicitud("SOLICITUD TRASLADO");

            $objSolicitud = new InfoDetalleSolicitud();

            if(!empty($intIdPuntoSession))
            {
                $objInfoPunto  = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPuntoSession);

                if(is_object($objInfoPunto))
                {
                    $strLogin         = $objInfoPunto->getLogin();
                    $objInfoServicio  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->findOneBy(array("puntoId" => $objInfoPunto->getId()));

                    if(is_object($objInfoServicio))
                    {
                        $objSolicitud->setServicioId($objInfoServicio);
                        $intIdServicio = $objInfoServicio->getId();
                    }
                }
            }

            $objSolicitud->setTipoSolicitudId($objTipoSolicitud);
            $objSolicitud->setEstado("PendienteAutorizar");
            $objSolicitud->setUsrCreacion($objSession->get('user'));
            $objSolicitud->setFeCreacion(new \DateTime('now'));
            $objSolicitud->setPrecioDescuento($strPrecioTraslado);
            $objSolicitud->setObservacion($strDescripcionTraslado);
            if(!empty($intIdMotivo))
            {
                $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
            }
            if(!empty($objMotivo) && is_object($objMotivo))
            {
                $objSolicitud->setMotivoId($objMotivo->getId());
            }
            $emComercial->persist($objSolicitud);
            $emComercial->flush();


            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
            $objDetalleSolHist->setObservacion($strDescripcionTraslado);
            $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
            $objDetalleSolHist->setEstado("PendienteAutorizar");
            if(!empty($objMotivo) && is_object($objMotivo))
            {
                $objDetalleSolHist->setMotivoId($objMotivo->getId());
            }
            $emComercial->persist($objDetalleSolHist);
            $emComercial->flush();

            //SE OBTIENE LA CARACTERISTICA ID_PUNTO
            $arrayCaracteristicaPunto = array('estado'                    => "Activo",
                                              'descripcionCaracteristica' => "ID_PUNTO");

            //SE OBTIENE LA CARACTERISTICA ID_PUNTO_TRASLADO
            $arrayCaracteristicaPuntoTraslado = array('estado'                    => "Activo",
                                                      'descripcionCaracteristica' => "ID_PUNTO_TRASLADO");

            //SE OBTIENE LA CARACTERISTICA SERVICIOS_TRASLADAR
            $arrayCaracteristicaServTraslado = array('estado'                    => "Activo",
                                                     'descripcionCaracteristica' => "SERVICIOS_TRASLADAR");

            //SE OBTIENE LA CARACTERISTICA TIPO_NEGOCIO
            $arrayCaracteristicaTipoNegocio = array('estado'                    => "Activo",
                                                    'descripcionCaracteristica' => "TIPO_NEGOCIO");

            //SE OBTIENE LA CARACTERISTICA REGION
            $arrayCaracteristicaRegion = array('estado'                    => "Activo",
                                               'descripcionCaracteristica' => "REGION");

            $objCaractPunto = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")->findOneBy( $arrayCaracteristicaPunto );

            $objCaractPuntoTraslado = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")->findOneBy( $arrayCaracteristicaPuntoTraslado );

            $objCaractServiciosTrasladar = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                       ->findOneBy( $arrayCaracteristicaServTraslado );

            $objCaractTipoNegocio = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")->findOneBy( $arrayCaracteristicaTipoNegocio );

            $objCaractRegion = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")->findOneBy( $arrayCaracteristicaRegion );

            //Se graba el punto a donde los servicios trasladados se dirigen
            if( is_object($objCaractPunto) )
            {
                $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                $objDetalleSolCaracteristica->setCaracteristicaId($objCaractPunto);
                $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolCaracteristica->setEstado('PendienteAutorizar');
                $objDetalleSolCaracteristica->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCaracteristica->setUsrCreacion($objSession->get('user'));
                $objDetalleSolCaracteristica->setValor($intIdPuntoSession);

                $emComercial->persist($objDetalleSolCaracteristica);
                $emComercial->flush();
            }

            if( is_object($objCaractPuntoTraslado) )
            {
                //Se graba el punto trasladado
                $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                $objDetalleSolCaracteristica->setCaracteristicaId($objCaractPuntoTraslado);
                $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolCaracteristica->setEstado('PendienteAutorizar');
                $objDetalleSolCaracteristica->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCaracteristica->setUsrCreacion($objSession->get('user'));
                $objDetalleSolCaracteristica->setValor($intIdPunto);
                $emComercial->persist($objDetalleSolCaracteristica);
                $emComercial->flush();
            }

            if( is_object($objCaractServiciosTrasladar) )
            {
                //Se graba una cadena string con la concatenacion de los servicios a trasladar
                $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                $objDetalleSolCaracteristica->setCaracteristicaId($objCaractServiciosTrasladar);
                $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolCaracteristica->setEstado('PendienteAutorizar');
                $objDetalleSolCaracteristica->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCaracteristica->setUsrCreacion($objSession->get('user'));
                $objDetalleSolCaracteristica->setValor($strServiciosATrasladar);
                $emComercial->persist($objDetalleSolCaracteristica);
                $emComercial->flush();
            }

            if( is_object($objCaractTipoNegocio) )
            {
                //Se graba una cadena string con la concatenacion de los servicios a trasladar
                $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                $objDetalleSolCaracteristica->setCaracteristicaId($objCaractTipoNegocio);
                $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolCaracteristica->setEstado('PendienteAutorizar');
                $objDetalleSolCaracteristica->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCaracteristica->setUsrCreacion($objSession->get('user'));
                $objDetalleSolCaracteristica->setValor($strTipoNegocio);
                $emComercial->persist($objDetalleSolCaracteristica);
                $emComercial->flush();
            }

            //Se obtiene la region a la que pertenece el servicio
            if(!empty($intIdPuntoSession))
            {
                $arrayParametrosRegion["intPuntoId"] = $intIdPuntoSession;
                $strRegionServicio = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getRegionPorPunto($arrayParametrosRegion);
            }
            //R1 y R2

            if( is_object($objCaractRegion) )
            {
                //Se graba una cadena string con la concatenacion de los servicios a trasladar
                $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                $objDetalleSolCaracteristica->setCaracteristicaId($objCaractRegion);
                $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolCaracteristica->setEstado('PendienteAutorizar');
                $objDetalleSolCaracteristica->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCaracteristica->setUsrCreacion($objSession->get('user'));
                $objDetalleSolCaracteristica->setValor($strRegionServicio);
                $emComercial->persist($objDetalleSolCaracteristica);
                $emComercial->flush();
            }

            //**************************************************Se envia notificación************************************************//

            //Se obtiene el departamento destino - COBRANZAS
            $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS PROYECTO TRASLADO',
                                                         'COMERCIAL',
                                                         'TRASLADO',
                                                         'DEPARTAMENTO DE COBRANZAS',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         '');

            if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
            {
                $strIdDepartamento = $arrayValoresParametros["valor1"];
            }

            //Se obtiene el canton_id correspondiente al punto que se traslada, esto se hace para el envio de las notificaciones
            if($strRegionServicio == "R1")
            {
                $strParametroCiudad = 'GYE';
                $strCanton          = "CANTON_DEMO_GYE";
            }
            elseif($strRegionServicio == "R2")
            {
                $strParametroCiudad = 'UIO';
                $strCanton          = "CANTON_DEMO_UIO";
            }

            //Se obtiene el canton a notificar
            $arrayValorCanton = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAMETROS PROYECTO DEMOS',
                                                           'COMERCIAL',
                                                           'DEMOS',
                                                           $strCanton,
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           '');

            if(isset($arrayValorCanton["valor1"]) && !empty($arrayValorCanton["valor1"]))
            {
                $strCiudadDestino = $arrayValorCanton["valor1"];
            }

            //Se obtiene la forma de contacto Correo Electronico
            $objFormaContacto = $emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                            ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                              'estado'                   => 'Activo'));

            if(is_object($objFormaContacto))
            {
                $intFormaContacto = $objFormaContacto->getId();
            }

            $arrayEmpleadoJefe = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                             ->getResultadoJefeDepartamentoEmpresa($strIdDepartamento,
                                                                                   $strCodigoEmpresa,
                                                                                   $strRegionServicio);

            //Se obtiene el correo electronico del usuario creador de la solicitud de traslado
            $objInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                       ->findOneBy(array('personaId'       => $arrayEmpleadoJefe["idPersona"],
                                                                         'formaContactoId' => $intFormaContacto,
                                                                         'estado'          => "Activo"));

            if($objInfoPersonaFormaContacto)
            {
                $arrayTo[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
            }

            //Se obtiene los correos del departamento de Ventas que se necesita notificar
            $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS PROYECTO TRASLADO',
                                                         'COMERCIAL',
                                                         'TRASLADO',
                                                         'CORREOS_VENTAS_'.$strParametroCiudad,
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         '');

            if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
            {
                $arrayTo[] = $arrayValoresParametros["valor1"];
                $arrayTo[] = $arrayValoresParametros["valor2"];
            }

            $strAsunto = "SE SOLICITA LA AUTORIZACION/RECHAZO DE UN TRASLADO - Login: ".$strLogin;

            $arrayParametrosCorreo = array('numeroSolicitud' => $objSolicitud->getId(),
                                           'puntoDestino'    => $strLogin,
                                           'empresa'         => $strPrefijoEmpresa);

            $serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                          $arrayTo,
                                                          'SOL_AUT_TRASL',
                                                          $arrayParametrosCorreo,
                                                          $strCodigoEmpresa,
                                                          $strCiudadDestino,
                                                          $strIdDepartamento);
            if($strPrefijoEmpresa == 'TN')
            {
                $arrayDestinatarios = array();
                $strVendedor        = (is_object($objInfoPunto)) ? $objInfoPunto->getUsrVendedor():"";
                $objPersona         = (is_object($objInfoPunto)) ? $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                $strCliente         = "";
                $strIdentificacion  = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                $strCliente         = (is_object($objPersona) && $objPersona->getRazonSocial()) ? 
                                      $objPersona->getRazonSocial():$objPersona->getNombres() . " " .$objPersona->getApellidos();
                //Correo del vendedor.
                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                         "Correo Electronico");
                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                {
                    foreach($arrayCorreos as $arrayItem)
                    {
                        if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                        {
                            $arrayDestinatarios[] = $arrayItem['valor'];
                        }
                    }
                }
                //Correo del subgerente
                $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                {
                    $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                    $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                     "Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                }
                //Correo de la persona quien crea la solicitud.
                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getContactosByLoginPersonaAndFormaContacto($objSession->get('user'),"Correo Electronico");
                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                {
                    foreach($arrayCorreos as $arrayItem)
                    {
                        if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                        {
                            $arrayDestinatarios[] = $arrayItem['valor'];
                        }
                    }
                }
                $strCuerpoCorreo      = "El presente correo es para indicarle que se creó una solicitud en TelcoS+ con los siguientes datos:";
                $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                              "strIdentificacionCliente" => $strIdentificacion,
                                              "strObservacion"           => $strDescripcionTraslado,
                                              "strCuerpoCorreo"          => $strCuerpoCorreo,
                                              "strCargoAsignado"         => "Subgerente");
                $serviceEnvioPlantilla->generarEnvioPlantilla("CREACIÓN DE SOLICITUD DE TRASLADO",
                                                              array_unique($arrayDestinatarios),
                                                              "NOTIFICACION",
                                                              $arrayParametrosMail,
                                                              $strPrefijoEmpresa,
                                                              "",
                                                              "",
                                                              null,
                                                              true,
                                                              "notificaciones_telcos@telconet.ec");
            }
            //**************************************************Se envia notificación************************************************//

            $strEstadoRespuesta  = "OK";
            $strMensajeRespuesta = "Se creó una solicitud de Traslado en estado: PendienteAutorizar que necesita autorización por parte del "
                                 . "departamento de Cobranzas - ".$arrayEmpleadoJefe["nombreCompleto"].". Asociada al login: ".$strLogin;

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoServicioController.generarSolicitudTrasladoAction',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();

            $strEstadoRespuesta  = "ERROR";
            $strMensajeRespuesta = "Se presento un error en la generación de la solicitud de Traslado";
        }

        $objResponse->setData( array('strStatus' => $strEstadoRespuesta,'strMensaje' => $strMensajeRespuesta));

        return $objResponse;
    }


     /**
     * Funcion rechazarSolTrasladoAction: se encarga de rechazar la solicitud de traslado porque no es viable por deuda
     *
     * @author Creado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 28-06-2018
     *
     * return JSON $objResponse
     */
    public function rechazarSolTrasladoAction()
    {
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $emComercial = $this->getDoctrine()->getManager();
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');

        $serviceUtil            = $this->get('schema.Util');
        $strUsrCreacion         = $objSession->get('user');
        $strDepartamentoSession = $objSession->get('idDepartamento');
        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strIpCreacion          = $objRequest->getClientIp();
        $strMotivoId            = $objRequest->get('motivo');
        $intIdCreadorTraslado   = 0;
        $intFormaContacto       = 5;
        $strLoginDestino        = "";
        $strParametroCiudad     = "GYE";

        $objResponse    = new JsonResponse();
        $strIdSolicitud = $objRequest->get('idDetalleSolicitud');
        $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');

        $objSolicitudTraslado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($strIdSolicitud);

        $objAdmiMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($strMotivoId);

        try
        {
            if(is_object($objSolicitudTraslado))
            {
                $intDetalleSolicitudId = $objSolicitudTraslado->getId();

                $objSolicitudTraslado->setEstado("Rechazada");
                $emComercial->persist($objSolicitudTraslado);
                $emComercial->flush();

                $strDescripcionTraslado = "Solicitud fue rechazada";

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objSolicitudTraslado);
                $objDetalleSolHist->setObservacion($strDescripcionTraslado);
                $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                $objDetalleSolHist->setEstado("Rechazada");

                if(is_object($objAdmiMotivo))
                {
                    $objDetalleSolHist->setMotivoId($objAdmiMotivo->getId());
                }

                $emComercial->persist($objDetalleSolHist);
                $emComercial->flush();

                //**************************************************Se envia notificación************************************************//

                //Se obtiene el departamento que rechazó - COBRANZAS
                $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAMETROS PROYECTO TRASLADO',
                                                             'COMERCIAL',
                                                             'TRASLADO',
                                                             'DEPARTAMENTO DE COBRANZAS',
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             '');

                if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                {
                    $strIdDepartamento = $arrayValoresParametros["valor1"];
                }

                //Consulta la caracteristica para ID_PUNTO
                $objCaractIdPunto = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => 'ID_PUNTO',
                                                                  "estado"                    => "Activo"));

                if(is_object($objCaractIdPunto))
                {
                    $objInfoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                 ->findOneBy(array("detalleSolicitudId" => $objSolicitudTraslado->getId(),
                                                                                   "caracteristicaId"   => $objCaractIdPunto->getId()));
                    if(is_object($objInfoDetalleSolCaract))
                    {
                        $strIdPuntoDestino = $objInfoDetalleSolCaract->getValor();

                        $objInfoPuntoDestino = $emComercial->getRepository('schemaBundle:InfoPunto')->find($strIdPuntoDestino);

                        if(is_object($objInfoPuntoDestino))
                        {
                            $strLoginDestino = $objInfoPuntoDestino->getLogin();
                        }
                    }
                }

                //Consulta la caracteristica para REGION
                $objCaractRegion = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array("descripcionCaracteristica" => 'REGION',
                                                                 "estado"                    => "Activo"));
                if(is_object($objCaractRegion))
                {
                    $objInfoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                 ->findOneBy(array("detalleSolicitudId" => $intDetalleSolicitudId,
                                                                                   "caracteristicaId"   => $objCaractRegion->getId()));
                    if(is_object($objInfoDetalleSolCaract))
                    {
                        $strRegionDestino = $objInfoDetalleSolCaract->getValor();

                        if($strRegionDestino == "R1")
                        {
                            $strParametroCiudad = "GYE";
                            $strCanton          = "CANTON_DEMO_GYE";
                        }
                        elseif($strRegionDestino == "R2")
                        {
                            $strParametroCiudad = "UIO";
                            $strCanton          = "CANTON_DEMO_UIO";
                        }

                        //Se obtiene el canton a notificar
                        $arrayValorCanton = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->getOne('PARAMETROS PROYECTO DEMOS',
                                                                       'COMERCIAL',
                                                                       'DEMOS',
                                                                       $strCanton,
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '',
                                                                       '');

                        if(isset($arrayValorCanton["valor1"]) && !empty($arrayValorCanton["valor1"]))
                        {
                            $strCiudadDestino = $arrayValorCanton["valor1"];
                        }
                    }
                }
                
                //Se obtiene los correos del departamento de Ventas que se necesita notificar
                $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAMETROS PROYECTO TRASLADO',
                                                             'COMERCIAL',
                                                             'TRASLADO',
                                                             'CORREOS_VENTAS_'.$strParametroCiudad,
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             '');

                if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                {
                    $arrayTo[] = $arrayValoresParametros["valor1"];
                    $arrayTo[] = $arrayValoresParametros["valor2"];
                }

                //Se obtiene el id_persona del usuario creador de la solicitud
                $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                              ->findOneBy(array("login" => $objSolicitudTraslado->getUsrCreacion()));

                if(is_object($objInfoPersona))
                {
                    $intIdCreadorTraslado = $objInfoPersona->getId();
                }

                //Se obtiene la forma de contacto Correo Electronico
                $objFormaContacto = $emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                                  'estado'                   => 'Activo'));

                if(is_object($objFormaContacto))
                {
                    $intFormaContacto = $objFormaContacto->getId();
                }

                //Se obtiene el correo electronico del usuario creador de la solicitud de traslado
                $objInfoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                           ->findOneBy(array('personaId'       => $intIdCreadorTraslado,
                                                                             'formaContactoId' => $intFormaContacto,
                                                                             'estado'          => "Activo"));

                if($objInfoPersonaFormaContacto)
                {
                    $arrayTo[] = $objInfoPersonaFormaContacto->getValor(); //Correo Persona Asignada
                }

                $strAsunto = "SE RECHAZÓ SOLICITUD DE TRASLADO - Login: ".$strLoginDestino;

                $arrayParametrosCorreo = array('numeroSolicitud' => $intDetalleSolicitudId,
                                               'puntoDestino'    => $strLoginDestino,
                                               'empresa'         => $strPrefijoEmpresa);

                $serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                              $arrayTo,
                                                              'RECH_TRASLADO',
                                                              $arrayParametrosCorreo,
                                                              $strEmpresaCod,
                                                              $strCiudadDestino,
                                                              $strIdDepartamento);

                //**************************************************Se envia notificación************************************************//

                $strEstadoRespuesta  = "OK";
                $strMensajeRespuesta = "Se rechazo con exito la solicitud de Traslado.";
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoServicioController.rechazarSolTrasladoAction',
                                       $ex->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion );

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();

            $strEstadoRespuesta  = "ERROR";
            $strMensajeRespuesta = "Se presento un error al rechazar la solicitud de Traslado";
        }

        $objResponse->setData( array('strStatus' => $strEstadoRespuesta,'strMensaje' => $strMensajeRespuesta));

        return $objResponse;
    }


    /**
     * Funcion que se encarga de realizar el traslado de los servicios a un nuevo punto
     *       
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 Version Inicial
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 16-07-2015 - Se agrega bloque de código que coloque estado PreAsignadoTecnico a los productos de Ips Adicionales
     *                           ya que siempre pasaba con estado Activo
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 06-01-2016   Se agrega validacion para traslados de clientes con tecnologia TELLION que aprovisionan con CNR
     *
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 13-04-2016   Se retira validacion para traslados de clientes con tecnologia TELLION que aprovisionan con CNR, 
     *                           ya sera soportado este escenario
     *
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.4 13-08-2016
     * - Se valida que todos los servicios dispongan del campo Meses_Restantes y Frecuencia Producto válidos
     * - Se valida que los servicios dispongan de padre de facturación diferente de null y válido.
     * - Se setea el nuevo padre de facturación de los servicios trasladados, si punto_id != punto_facturacion_id entonces se hereda el mismo
     *   padre de facturación, caso contrario se establecerá el nuevo login siempre que sea padre facturación, sino se le asignará al servicio
     *   otro padre de facturación del cliente.
     * - Se valida que solo MD pueda usar esta opción.
     * - Correción de ortografía en mensajes de error.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 16-11-2016   Se setea login de orígen en historial de servicio trasladado.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.6 28-11-2016   Se restringe validación de punto de facturación válido para que solo se aplique a servicios que 
     *                           no son cortesia o no son venta (ES_VENTA != N).
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.7 24-05-2017
     * Se agrega Validacion para realizar un traslado el Sistema deberá validar que el login del cual se desea realizar el traslado
     * no registre deuda en caso de registrar deuda deberá bloquear y  no permitir el traslado de ese login,
     * esta validación únicamente debe aplicar a clientes con forma de pago Efectivo
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 01-11-2017   Se agrega programación para gestionar traslados TN
     * @since 1.7
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 21-06-2018  Se agregan consideraciones adicionales para determinar si la factura se ecuentra vencida y asi determinar si
     *                           tiene deuda o no
     * @since 1.8
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 2.0 16-07-2018- Se considera que se clone las caracteristicas correspondientes al servicio Fox_Primium
     *                          y que al clonar dichas caracteristicas se marque la caracteristica 'MIGRADO_FOX' en S.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 27-07-2018  Se modifica el estado del historial de los servicios del punto origen 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 14-08-2018 Se agregan nombres técnicos del servicio Internet Small Business y de sus ips adicionales
     *                         junto con las características que se clonarán al realizar la solicitud de traslado para el servicio del 
     *                         punto destino
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 04-02-2019 Se agregan nombres técnicos y características para permitir el traslado de servicios TELCOHOME y sus ips adicionales
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.4 26-02-2019 Se agregan validaciones para traslado de servicios por proyecto nuevos planes MD
     * @since 2.2
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 18-03-2019 Se elimina nombre técnico IPTELCOHOME, ya que el producto no saldrá a la venta
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.6 19-06-2019 Se agregan validaciones para poder trasladar los servicios adicionales dual band con estado Pendiente, de
     *                         esta forma podemos realizar el traslado de información de estos servicios de manera correcta
     * 
     * @since 2.4
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 2.7 21-09-2020 Se parametriza productos que no seran considerados para traslados.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 14-10-2020 Se modifican los parámetros enviados a la función getProductosTradicionales, ya que se ha cambiado dicha consulta
     *                         evitando comparar con el grupo de los productos
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.9 19-10-2020  - Se considera que se clone las caracteristicas correspondientes al servicio Paramount y Noggin
     *                              y que al clonar dichas caracteristicas se marque la caracteristica de MIGRADO en S.
     * 
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 2.9  22-10-2020  - Se agrega el proceso para clonar caracteristicas
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.10 10-11-2020 Se agrega validación para trasladar en estado Pendiente el servicio W+AP
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.11 22-03-2021 Se abre la programacion para servicios Internet SDWAN.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 2.12  24-03-2021  - Se inserta historial con fecha de activación de servicio trasladado.
     *
     * @author Ivan Mata <imata@telconet.ec>
     * @version 2.13
     * @since 07-04-2021 Se pasa Logica de crear traslado de servicio a capa Service.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.14 21-04-2021 - Se aumenta informacion del punto origen del request para trasladar
     * 
     * @author Andre Lazo <alazo@telconet.ec>
     * @version 2.15 16-02-2023 - Se aumenta informacion a enviar en el arrayParametroTraslado
     * - Se aumenta en el request del tipo de proceso para el proceso traslado
     * 
     */
    public function trasladarServiciosCreateAction()
    {
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $objResponse       = new JsonResponse();
        $emComercial       = $this->getDoctrine()->getManager();
        $strStatus         = "";
        $strMensaje        = "";

        $objPtoCliente          = $session->get('ptoCliente');
        $intIdOficina           = $session->get('idOficina');
        $serviceUtil            = $this->get('schema.Util');
        $strPrefijoEmpresa      = $session->get('prefijoEmpresa');
        $strDepartamentoSession = $session->get('idDepartamento');
        $strEmpresaCod          = $session->get('idEmpresa');
        $strPersonaEmpresaRol   = $session->get('idPersonaEmpresaRol');
        $intIdPuntoCliente      = $objPtoCliente['id'];
        $intIdPuntoSession      = $request->get('idPuntoSession');
        $intIdPtoTraslado       = $request->get('idPuntoTraslado');
        $strCanal               = "telcos";
        $strUsuarioCreacion     = $session->get('user');
        $strBanderaAutorizarSol = $request->get('banderaAutorizarSol');
        $serviceInfoServicio    = $this->get('comercial.InfoServicio');
        $strTipoProceso         = $request->request->get('strTipo') ? $request->request->get('strTipo') : '';

        try
        {
            if($strBanderaAutorizarSol == "S")
            {
               $intIdPuntoCliente = $intIdPuntoSession;
            }
            
            $arrayParametrosTraslado  = array ('intIdOficina'             => $intIdOficina,
                                               'strPrefijoEmpresa'        => $strPrefijoEmpresa,
                                               'strDepartamento'          => $strDepartamentoSession,
                                               'arrayValor'               => $request,
                                               'strEmpresaCod'            => $strEmpresaCod,
                                               'strPersonaEmpresaRol'     => $strPersonaEmpresaRol,
                                               'strCanal'                 => $strCanal,
                                               'strUsuarioCreacion'       => $strUsuarioCreacion,
                                               'objPuntoCliente'          => $objPtoCliente,
                                               'intIdPuntoCliente'        => $intIdPuntoCliente,
                                               'intIdPtoTraslado'         => $intIdPtoTraslado,
                                               'strTipoProceso'           => $strTipoProceso,
                                               'session'                  => $session,
                                               'strIp'                    =>$request->getClientIp());
        
            
            $arrayRespuesta  = $serviceInfoServicio->trasladarServiciosPunto($arrayParametrosTraslado);
            
            $strStatus  = $arrayRespuesta['strStatus'];
            $strMensaje = $arrayRespuesta['strMensaje'];
              
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Se presento un error al procesar la solicitud de traslado";

            $serviceUtil->insertError( 'Telcos+',
                                       'ComercialBundle.InfoServicioController.trasladarServiciosCreateAction',
                                       $e->getMessage(),
                                       $strUsuarioCreacion,
                                       $request->getClientIp() );

            $emComercial->getConnection()->rollback();

        }

        if($strBanderaAutorizarSol == "S")
        {
            $objResponse->setData( array('strStatus' => $strStatus,'strMensaje' => $strMensaje));
            return $objResponse;
        }
        else
        {
            if($strStatus== "ERROR")
            {
                if ($strPrefijoEmpresa == 'MD' && $strTipoProceso == 'continuo')
                {
                    $session->getFlashBag()->add('error', $strMensaje);
                    return $this->redirect($this->generateUrl('infoservicio_trasladar_servicios',
                                            array('id' => $intIdPuntoCliente, 'rol'=>'Cliente', 
                                            'strTipo' => $strTipoProceso, 'intIdPuntoAnterior' => $intIdPtoTraslado), true));
                }
                $session->getFlashBag()->add('error', $strMensaje);
                return $this->redirect($this->generateUrl('infoservicio_trasladar_servicios', 
                                       array('id' => $intIdPuntoCliente, 'rol'=>'Cliente'), true));
            }
            else
            {
               return $this->redirect($this->generateUrl('infopunto_show', array('id' => $intIdPuntoCliente, 'rol'=>'Cliente'), true)); 
            }
            
        }
        
    }
    

    /**
     * comprobarDeudasClienteAction
     *
     * Función que se encarga de comprobar las facturas vencidas de un cliente y su total por cada punto que posea.
     *
     * @author Daniel Guzman <ddguzman@telconet.ec>
     * @version 1.0 07-03-2023
     */
    public function comprobarDeudasClienteAction()
    {
        ini_set('max_execution_time', 3600);
        
        $objRequest        = $this->getRequest();
        $objResponse       = new JsonResponse();
        $objSession        = $objRequest->getSession();
        $intCodEmpresa     = $objSession->get('idEmpresa');
        $emComercial       = $this->getDoctrine()->getManager();
        $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $objCliente        = $objSession->get('cliente');
        $strStatus         = 'OK';
        $strMensaje        = '';
        $floatDeudaTotal   = 0;
        $intTotalFCV       = 0;
        $strMensajeDeudas  = '';
        $intTotalPuntosConDeuda = 0;
        $intTotalMensajeMostrar = 5;

        $intIdPersonaEmpresaRol = $objCliente['id_persona_empresa_rol'];
        $objCicloCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->getCaracteristicaPorDescripcionPorEstado('CICLO_FACTURACION', 'Activo');
        $arrayCriterios = array(
                                'caracteristicaId'    => $objCicloCaracteristica->getId(),
                                'personaEmpresaRolId' => $intIdPersonaEmpresaRol
                        );

        $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                    ->findOneBy($arrayCriterios);
        $objAdmiCiclo              = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                    ->find($objPersonaEmpresaRolCarac->getValor());

        $intDiaActual  = intval(date('j'));
        $intMesActual  = intval(date('m'));
        $intAnioActual = intval(date( 'Y'));
        $intDiaInicioCiclo = intval($objAdmiCiclo->getFeInicio()->format('j'));

        if($intDiaActual <= $intDiaInicioCiclo)
        {
            $intMesActual -= 1;

            if($intMesActual === 0)
            {
                $intAnioActual -= 1;
                $intMesActual   = 12;
            }
        }

        $objFechaLimite = new \DateTime("{$intAnioActual}-{$intMesActual}-{$intDiaInicioCiclo}");

        $objPuntosCliente  = $emComercial->getRepository('schemaBundle:InfoPunto')
                                            ->findBy(array('personaEmpresaRolId' => $intIdPersonaEmpresaRol, 'estado' => 'Activo'));

        foreach( $objPuntosCliente as $objPuntoCliente )
        {
            $intIdPunto         = $objPuntoCliente->getId();
            $floatDeudaPunto    = 0;

            if(!empty($intIdPunto))
            {
                $arrayParametros = array();
                $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
                $arrayParametros['intIdPunto'] = $intIdPunto;
                $arrayParametros['strFechaLimite'] = $objFechaLimite;

                $intTotalFacturas = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getTotalFacturasVencidasPorPersonaEmpresaRolPorPunto($arrayParametros);
                $intTotalFCV += $intTotalFacturas;
                $floatDeudaPunto = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                    ->obtieneSaldoPorPunto($intIdPunto);
                $floatDeudaPunto = $floatDeudaPunto[0]['saldo'];
                if ($floatDeudaPunto > 0)
                {
                    if($intTotalPuntosConDeuda <= $intTotalMensajeMostrar)
                    {
                        $objServicioInternet = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->obtieneServicioInternetxPunto($intIdPunto);
                        $strEstadoOS = is_object($objServicioInternet) ? $objServicioInternet->getEstado() : '';
                        $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                        $strMensajeDeudas .= "<b>" . $floatDeudaPunto . " USD</b>  en el <b>Login:</b> " . $objInfoPunto->getLogin() .
                                " <b>Dirección:</b> " . $objInfoPunto->getDireccion() . " <b>Estado:</b> ".$strEstadoOS."<br> <br>";
                    }
                    $intTotalPuntosConDeuda++;
                    $floatDeudaTotal += $floatDeudaPunto;
                }
            }  
        }

        $strMensaje = "<b> Cliente posee una deuda pendiente de ". $floatDeudaTotal . " USD </b>  <br> <br>" .
                    "<br>" . $strMensajeDeudas;

        if($intTotalPuntosConDeuda > $intTotalMensajeMostrar)
        {
            $strMensaje = $strMensaje."<br> Además, tiene ".  ($intTotalPuntosConDeuda - $intTotalMensajeMostrar). " login más con deuda.";
        }            

        $arrayValoresParam = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('VALIDACIONES_ADEUDAMIENTO','COMERCIAL','',
                'PARAMETROS_DEUDA','','','',
                '','',$intCodEmpresa);


        $floatDeudaMinima = $arrayValoresParam[0]['valor1'];
        $intMinimoFCV     = $arrayValoresParam[0]['valor2'];

        if (($floatDeudaTotal >= $floatDeudaMinima || $floatDeudaTotal < $floatDeudaMinima)
            && $intTotalFCV >= $intMinimoFCV)
        {
            $strStatus = "ERROR";
        }
        if(isset($arrayValoresParam[0]['valor1'])
        && !empty($arrayValoresParam[0]['valor1'])
        && isset($arrayValoresParam[0]['valor2'])
        && !empty($arrayValoresParam[0]['valor2']))
            {
                $floatDeudaMinima = $arrayValoresParam[0]['valor1'];
                $intMinimoFCV     = $arrayValoresParam[0]['valor2'];

                if (($floatDeudaTotal >= $floatDeudaMinima || $floatDeudaTotal < $floatDeudaMinima)
                    && $intTotalFCV >= $intMinimoFCV)
                {
                    $strStatus = "ERROR";
                }
            }
        else
            {
                $strStatus  = "ERROR";
                $strMensaje = "No se encontraron los parametros para VALIDACIONES_ADEUDAMIENTO, por favor informar a sistemas";
            }
        $arrayRespuesta = array();
        $arrayRespuesta["status"] = $strStatus;
        $arrayRespuesta["mensaje"] = $strMensaje;
        $objResponse->setData( $arrayRespuesta );

        return $objResponse;
    }


    /**
     * reubicarServiciosCreateAction
     * 
     * Funcion que se encarga de realizar el traslado de los servicios a un nuevo punto
     *       
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 21-01-2018   Se agrega programación para gestionar traslados TN
     * @since 1.0
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 17-08-2022 - Se guardan los motivos para el ingreso de la solicitud y se envía notificaciones.
     *
     */
    public function reubicarServiciosCreateAction()
    {
        $objRequest                = $this->getRequest();
        $objSession                = $objRequest->getSession();
        $emComercial               = $this->getDoctrine()->getManager();
        $arrayPtoCliente           = $objSession->get('ptoCliente');
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $intIdPtoCliente           = $arrayPtoCliente['id'];
        $strEsFacturableTn         = $objRequest->get('esFacturableTn');
        $strPrecioReubicacion      = $objRequest->get('precioReubicacionTn');
        $strDescripcionReubicacion = $objRequest->get('descripcionReubicacionTn');
        $intIdMotivo               = $objRequest->get('objListadoMotivos') ? $objRequest->get('objListadoMotivos'):"";
        $objServicioReubicacion    = null;
        $strEstadoSolicitud        = "";
        $serviceUtil               = $this->get('schema.Util');
        $serviceEnvioPlantilla     = $this->get('soporte.EnvioPlantilla');
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPtoCliente);
            if (is_object($objPunto))
            {
                $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findBy(array('puntoId' => $objPunto->getId()));
                foreach($arrayServicios as $objServicio)
                {
                    if ($objServicio->getEstado() == "Activo")
                    {
                        $objServicioReubicacion = $objServicio;
                        break;
                    }
                }
                if (is_object($objServicioReubicacion))
                {
                    if ($strEsFacturableTn == "SI")
                    {
                        $strEstadoSolicitud = "PrePlanificada";
                    }
                    else
                    {
                        $strEstadoSolicitud = "Pendiente";
                    }
                    $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud("SOLICITUD REUBICACION");

                    $objSolicitud = new InfoDetalleSolicitud();
                    $objSolicitud->setServicioId($objServicioReubicacion);
                    $objSolicitud->setTipoSolicitudId($objTipoSolicitud);
                    $objSolicitud->setEstado($strEstadoSolicitud);
                    $objSolicitud->setUsrCreacion($objSession->get('user'));
                    $objSolicitud->setFeCreacion(new \DateTime('now'));
                    $objSolicitud->setPrecioDescuento($strPrecioReubicacion);
                    $objSolicitud->setObservacion($strDescripcionReubicacion);
                    if(!empty($intIdMotivo))
                    {
                        $objMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                    }
                    if(!empty($objMotivo) && is_object($objMotivo))
                    {
                        $objSolicitud->setMotivoId($objMotivo->getId());
                    }
                    $emComercial->persist($objSolicitud);
                    $emComercial->flush();
                    
                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                    $objDetalleSolHist->setObservacion($strDescripcionReubicacion);
                    $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                    $objDetalleSolHist->setEstado($strEstadoSolicitud);
                    if(!empty($objMotivo) && is_object($objMotivo))
                    {
                        $objDetalleSolHist->setMotivoId($objMotivo->getId());
                    }
                    $emComercial->persist($objDetalleSolHist);
                    $emComercial->flush();
                    
                    if ($strEstadoSolicitud == "Pendiente")
                    {
                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $objDetalleSolHist = new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                        $objDetalleSolHist->setObservacion("La solicitud fue seleccionada como NO facturable.");
                        $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                        $objDetalleSolHist->setEstado($strEstadoSolicitud);

                        $emComercial->persist($objDetalleSolHist);
                        $emComercial->flush();
                    }
                    
                    //SE OBTIENE LA CARACTERISTICA ID_PUNTO
                    $arrayCaracteristicasParametros = array('estado'                    => "Activo", 
                                                            'descripcionCaracteristica' => "ID_PUNTO");
                    $objCaracteristicaIdPunto = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                              ->findOneBy( $arrayCaracteristicasParametros );

                    if( is_object($objCaracteristicaIdPunto) )
                    {
                        $objDetalleSolCaracteristica = new InfoDetalleSolCaract();
                        $objDetalleSolCaracteristica->setCaracteristicaId($objCaracteristicaIdPunto);
                        $objDetalleSolCaracteristica->setDetalleSolicitudId($objSolicitud);
                        $objDetalleSolCaracteristica->setEstado($strEstadoSolicitud);
                        $objDetalleSolCaracteristica->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolCaracteristica->setUsrCreacion($objSession->get('user'));
                        $objDetalleSolCaracteristica->setValor($objPunto->getId());
                        $emComercial->persist($objDetalleSolCaracteristica);
                        $emComercial->flush();
                    }
                    
                    $objServicioReubicacion->setTipoOrden("R");
                    $emComercial->persist($objServicioReubicacion);
                    $emComercial->flush();
                    //historial del servicio
                    $objServicioHistorialNuevo = new InfoServicioHistorial();
                    $objServicioHistorialNuevo->setServicioId($objServicioReubicacion);
                    $objServicioHistorialNuevo->setObservacion("Se cambia tipo de orden a R para ".
                                                               "proceder a facturar reubicación");
                    $objServicioHistorialNuevo->setEstado($objServicioReubicacion->getEstado());
                    $objServicioHistorialNuevo->setUsrCreacion($objSession->get('user'));
                    $objServicioHistorialNuevo->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorialNuevo->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($objServicioHistorialNuevo);
                    $emComercial->flush();
                    if($strPrefijoEmpresa == 'TN')
                    {
                        $arrayDestinatarios = array();
                        $strVendedor        = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                        $objPersona         = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                        $strCliente         = "";
                        $strIdentificacion  = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                        $strCliente         = (is_object($objPersona) && $objPersona->getRazonSocial()) ? 
                                              $objPersona->getRazonSocial():$objPersona->getNombres() . " " .$objPersona->getApellidos();
                        //Correo del vendedor.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                                 "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        //Correo del subgerente
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                        if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                     "Correo Electronico");
                            if(!empty($arrayCorreos) && is_array($arrayCorreos))
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }
                        //Correo de la persona quien crea la solicitud.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objSession->get('user'),"Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        $strCuerpoCorreo     = "El presente correo es para indicarle que se creó una solicitud en TelcoS+ con los siguientes datos:";
                        $arrayParametrosMail = array("strNombreCliente"         => $strCliente,
                                                     "strIdentificacionCliente" => $strIdentificacion,
                                                     "strObservacion"           => $strDescripcionReubicacion,
                                                     "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                     "strCargoAsignado"         => "Subgerente");
                        $serviceEnvioPlantilla->generarEnvioPlantilla("CREACIÓN DE SOLICITUD DE REUBICACIÓN",
                                                                      array_unique($arrayDestinatarios),
                                                                      "NOTIFICACION",
                                                                      $arrayParametrosMail,
                                                                      $strPrefijoEmpresa,
                                                                      "",
                                                                      "",
                                                                      null,
                                                                      true,
                                                                      "notificaciones_telcos@telconet.ec");
                    }
                    $emComercial->getConnection()->commit();
                    $objSession->getFlashBag()->add('success', 'Se genero solicitud de reubicación correctamente.');
                }
                else
                {
                    $objSession->getFlashBag()->add('error', "El punto no tienen ningun servicio Activo para reubicar.");
                    return $this->redirect($this->generateUrl('infoservicio_reubicar_servicios', 
                                                              array('id' => $intIdPtoCliente, 'rol'=>'Cliente'),
                                                              true));
                }
            }
            else
            {
                $objSession->getFlashBag()->add('error', "El punto no se encuentra en sesión.");
                return $this->redirect($this->generateUrl('infoservicio_reubicar_servicios', 
                                                          array('id' => $intIdPtoCliente, 'rol'=>'Cliente'),
                                                          true));
            }
        }
        catch(\Exception $e)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.reubicarServiciosCreateAction', 
                                      $e->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                     );
            $objSession->getFlashBag()
                       ->add('error', 
                            '"Existieron problemas generales al generar solicitud, por favor notificar a sistemas.');
            return $this->redirect($this->generateUrl('infoservicio_reubicar_servicios', 
                                                      array('id' => $intIdPtoCliente, 'rol'=>'Cliente'), true));
        }
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        return $this->redirect($this->generateUrl('infopunto_show', array('id' => $intIdPtoCliente, 'rol'=>'Cliente'), true));
    }

    /**
     * Documentación para el método 'obtenerNombreServicio'.

     * Función que procesa la obtención del nombre del servicio
     *       
     * @param InfoServicio $objInfoServicio Objeto INFO_SERVICIO.
     * 
     * @return String Nombre del Servicio.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 06-09-2016
     */
    public function obtenerNombreServicio($objInfoServicio)
    {
        if($objInfoServicio)
        {
            $entityInfoPlan = $objInfoServicio->getPlanId();

            if($entityInfoPlan)
            {
                return $entityInfoPlan->getNombrePlan();
            }
            else
            {
                $entityAdmiProducto = $objInfoServicio->getProductoId();
                if($entityAdmiProducto)
                {
                    return $entityAdmiProducto->getDescripcionProducto();
                }
            }
        }
        return "";
    }
    
    /**
     * Funcion utilizada para validar el traslado de servicios
     * En caso de ser un servicio con tecnologia Tellion Cnr no es posible general el traslado
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 05-05-2020 Se envía un arreglo de parámetros a la función getServicioTecnicoByPuntoId debido a los cambios realizados 
     *                          por la reestucturación de servicios Small Business
     * 
     * @param integer $intIdPunto
     * @param string  $strRespuesta
     * 
     * @return redirect
     **/
    public function validaFactibilidadTrasladoTellionCnr ($intIdPunto)
    {
        $strRespuesta         = "";
        $emInfraestructura    = $this->getDoctrine()->getManager('telconet_infraestructura');
        /* @var $recursosDeRedService \telconet\planificacionBundle\Service\RecursosDeRedService */
        $recursosDeRedService = $this->get('planificacion.RecursosDeRed');
        try
        {
            $objServicioTecnicoPlan = $recursosDeRedService->getServicioTecnicoByPuntoId(array("intIdPunto" => $intIdPunto));
            if(is_object($objServicioTecnicoPlan))
            {
                $objInfoElemento    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnicoPlan->getElementoId());
                //caracteristica para saber que tipo de aprovisionamiento tiene el OLT origen del traslado
                $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array( "elementoId"    => $objServicioTecnicoPlan->getElementoId(),
                                                                           "detalleNombre" => "APROVISIONAMIENTO_IP")
                                                                         );
                if ($objDetalleElemento)
                {
                    if ( $objDetalleElemento->getDetalleValor() == "CNR" &&  
                         $objInfoElemento->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento() == "TELLION"
                       )
                    {
                        $strRespuesta = "No es posible generar traslado por motivos de migración de tecnologias.";
                    }

                }
            }
        }
        catch (Exception $exc)
        {
            $mensajeError = "Error: " . $exc->getMessage();
            error_log($mensajeError);
            $strRespuesta = "Se presentaron problremas al generar el traslado, favor notificar a Sistemas.";
        }
        return $strRespuesta;
    }
    /**         
    * 
    * Documentación para el método 'gridPuntosTrasladosAction'.
    *
    * Metodo para obtener los puntos clientes Login que pueden ser trasladados.
    * Consideracion:  
    * 1)Solo se permite trasladar Puntos Clientes en estado Activo.
    * 2)Solo se permite trasladar si el Login posee al menos 1 servicio en estado Activo
    * 3)Se Cargara solo con los Logines que cumplen las condiciones. 
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-05-2017
    * 
    * @param  integer $intIdPersonaRol  // Id de la PersonaEmpresaRol Cliente          
    * @param  integer $id               // Id del punto que realiza el traslado
    * 
    * @return JsonResponse        
    */
    public function gridPuntosTrasladosAction() 
    {
        $emComercial      = $this->getDoctrine()->getManager('telconet');        
        $arrayParametros  = array();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();  
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user');         
        $intIdPersonaRol  = $objRequest->get("intIdPersonaRol");           
        $intIdPto         = $objRequest->get("id");  
        
        $arrayParametros  = array('intIdPersonaRol'  => $intIdPersonaRol,
                                  'intIdPto'         => $intIdPto,
                                  'strEstado'        => 'Activo'
                                 );
        $arrayJsonLoginesTraslado   = array();
        $objJsonResponse            = new JsonResponse($arrayJsonLoginesTraslado);
             
        try
        {        
            $arrayJsonLoginesTraslado  = $emComercial ->getRepository('schemaBundle:InfoPunto')
                                                      ->getPuntosTraslados($arrayParametros);
            $objJsonResponse->setData($arrayJsonLoginesTraslado);   
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.gridPuntosTrasladosAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }                                  
        return $objJsonResponse;                
    }

    /**
    * getServiciosATrasladarAction
    * Obtiene informacion de los servicios que van a ser trasladados, esta info se muestra en la panntalla de autorizar Traslado.
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 28-07-2018
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.1 17-08-2022 - Se agregan nuevos parámetros para mejorar la presentación en el grid.
    *
	*/
    public function getServiciosATrasladarAction()
    {
        $objPeticion  = $this->get('request');
        $emComercial  = $this->getDoctrine()->getManager('telconet');

        $strServicios = $objPeticion->query->get('idsServiciosTraslado') ? $objPeticion->query->get('idsServiciosTraslado') : "";
        $intStart     = $objPeticion->query->get('start') ? $objPeticion->query->get('start'): self::VALOR_INICIAL_BUSQUEDA;
        $intLimit     = $objPeticion->query->get('limit') ? $objPeticion->query->get('limit'): self::VALOR_LIMITE_BUSQUEDA;
        $strDraw      = $objPeticion->query->get('draw') ? $objPeticion->query->get('draw'):"1";
        $arrayEncontrados      = array();
        $arrayRespuesta        = array();
        $intNumeroRegistros    = 0;
        $objResponse           = new JsonResponse();
        $idsServiciosTrasladar = explode(",", $strServicios);

        $arrayParametros["arrayServicios"] = $idsServiciosTrasladar;

        //Se consulta los servicios que seran trasladados
        $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')->getServiciosTraslado($arrayParametros);

        $intNumeroRegistros = count($arrayServicios["registros"]);

        if($intNumeroRegistros > 0)
        {
            foreach($arrayServicios["registros"] as $objElemento)
            {
                $arrayEncontrados[] = array('servicio' => $objElemento["servicio"],
                                            'estado'   => $objElemento["estado"]);
            }
        }

        $arrayRespuesta["total"]           = $intNumeroRegistros;
        $arrayRespuesta["encontrados"]     = $arrayEncontrados;
        $arrayRespuesta["draw"]            = $strDraw;
        $arrayRespuesta["recordsTotal"]    = $intNumeroRegistros;
        $arrayRespuesta["recordsFiltered"] = $intNumeroRegistros;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
     * getProductosEnServicios
     *
     * Método encargado de obtener un JSON con el listado de productos existentes por empresa
     *
     * @param array $arrayParametros [ strDescripcionProducto => nombre del producto
     *                                 strCodEmpresa          => cod de la empresa
     *                                 strEstado              => estado servicio ]
     *
     * @return json $resultado
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 28-02-2019
     */
    public function getProductosServiciosAction()
    {
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        $arrayParametros = array();
        $arrayProductos  = array();
        $objPeticion     = $this->get('request');
        $intStart        = $objPeticion->query->get('start');
        $intLimit        = $objPeticion->query->get('limit');
        $strProducto     = $objPeticion->query->get('query');
        $strIdEmpresa    = $objPeticion->getSession()->get('idEmpresa');
        $strEstado       = 'Activo';

        $arrayParametros['strEstado']               = $strEstado;
        $arrayParametros['strDescripcionProducto']  = $strProducto;
        $arrayParametros['strCodEmpresa']           = $strIdEmpresa;
        $arrayParametros['intStart']                = $intStart;
        $arrayParametros['intLimit']                = $intLimit;

        $arrayProductos = $emComercial->getRepository('schemaBundle:InfoServicio')->getProductosEnServiciosJson($arrayParametros);

        return $arrayProductos;
    }



    /**
     * Documentación para el método 'getServiciosByLoginAction'.
     *
     * Método que obtiene los servicios de un punto
     *     
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 29-06-2016
     * @since   1.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 27-06-2019  Se modifican validaciones para poder controlar escenacios de traslados MD donde existen problemas
     *                          por generar traslados duplicados de servicios en diversos puntos del cliente
     * @since   1.1
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.3 02-10-2019  Se inserta validación para no migrar servicios en estado eliminados.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 11-11-2020 Se parametrizan los estados permitidos para realizar un traslado tanto en TN como en MD
     * 
     * @since   1.2
     */
    public function getServiciosByLoginAction()
    {
        /* Para listar:
         * Debo buscar con el pto
         * Y los servicios con estado Factible
         * */
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $objRequest             = $this->getRequest();
        $intIdPunto             = $objRequest->get("idPunto");//Identificador del punto seleccionado en el combobox de la pantalla web de traslados
        $strEstado              = $objRequest->get("estado");
        $intPuntoId             = $objRequest->get("puntoId");//Identificador del punto en sesión para realizar el traslado
        $strEsReubicacion       = $objRequest->get("strEsReubicacion");
        $objSession             = $objRequest->getSession();
        $intEmpresaCod          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strLoginAux            = "";
        $strDescripcionFactura  = "";

        $arrayServicios   = null;
        $objServicioRep   = $emComercial->getRepository('schemaBundle:InfoServicio');
        $arrayParamValida = array();
        $arrayParamValida['intEmpresaCod']    = $intEmpresaCod;
        $arrayParamValida['intPuntoIdSesion'] = $intPuntoId;
        $arrayParamValida['intPuntoIdSeleccionado'] = $intIdPunto;
        $arrayTrasladados = $objServicioRep->getServiciosTrasladados($arrayParamValida);
        $arrayResultado   = $objServicioRep->findServiciosByPuntoAndEstado($intIdPunto, $strEstado, $arrayTrasladados);
        $listaServicios   = $arrayResultado['registros'];
        $intTotal         = $arrayResultado['total'];
        
        $emGeneral                          = $this->get('doctrine')->getManager('telconet_general');
        $strOpcion                          = "";
        $arrayEstadosServiciosPermitidos    = array();
        if($strEsReubicacion != "SI")
        {
            $strOpcion                          = "TRASLADO";
            $arrayRegEstadosServiciosPermitidos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoEmpresa,
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $strOpcion,
                                                                  'ESTADOS_SERVICIOS_PERMITIDOS',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $intEmpresaCod);
            if(!empty($arrayRegEstadosServiciosPermitidos))
            {
                foreach($arrayRegEstadosServiciosPermitidos as $arrayRegEstadoServicio)
                {
                    $arrayEstadosServiciosPermitidos[]  = $arrayRegEstadoServicio['valor3'];
                }
            }
        }
        
        if($listaServicios)
        {
            foreach($listaServicios as $objServicio)
            {
                if(strtolower($objServicio->getEstado()) != "eliminado")
                {
                    $strPermiteSeleccion   = "NO";
                    $strNombreServicio     = "";
                    $strLoginAux           = "";
                    $strDescripcionFactura = "";
                    if($objServicio->getProductoId() != "")
                    {
                        $strNombreServicio     = $objServicio->getProductoId()->getDescripcionProducto();
                        $strLoginAux           = $objServicio->getLoginAux();
                        $strDescripcionFactura = $objServicio->getDescripcionPresentaFactura();
                    }

                    if($objServicio->getPlanId() != "")
                    {
                        $strNombreServicio = $objServicio->getPlanId()->getNombrePlan();
                    }
                    if ($strEsReubicacion == "SI")
                    {
                        if( $objServicio->getEstado() == "Activo" ||
                            $objServicio->getEstado() == "EnPruebas" ||
                            $objServicio->getEstado() == "In-Corte" ||
                            $objServicio->getEstado() == "EnVerificacion" )
                        {
                            $arrayServicios[] = array('idServicio'            => $objServicio->getId(),
                                                      'servicio'              => $strNombreServicio,
                                                      'strLoginAux'           => $strLoginAux,
                                                      'strDescripcionFactura' => $strDescripcionFactura,
                                                      'estado'                => $objServicio->getEstado());
                        }
                    }
                    else
                    {
                        if(isset($arrayEstadosServiciosPermitidos) && !empty($arrayEstadosServiciosPermitidos)
                            && in_array($objServicio->getEstado(), $arrayEstadosServiciosPermitidos))
                        {
                            $strPermiteSeleccion = "SI";
                        }
                        else if(is_object($objServicio->getProductoId()))
                        {
                            $arrayRegEstadoServicioXProdPermitido   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoEmpresa,
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            $strOpcion,
                                                                                            'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
                                                                                            $objServicio->getProductoId()->getNombreTecnico(),
                                                                                            $objServicio->getEstado(),
                                                                                            '',
                                                                                            $intEmpresaCod);
                            if(isset($arrayRegEstadoServicioXProdPermitido) && !empty($arrayRegEstadoServicioXProdPermitido))
                            {
                                $strPermiteSeleccion = "SI";
                            }
                        }
                        $arrayServicios[] = array('idServicio'            => $objServicio->getId(),
                                                  'servicio'              => $strNombreServicio,
                                                  'strLoginAux'           => $strLoginAux,
                                                  'strDescripcionFactura' => $strDescripcionFactura,
                                                  'estado'                => $objServicio->getEstado(),
                                                  'permiteSeleccion'      => $strPermiteSeleccion);
                    }
                }
                else
                {
                    $intTotal--;
                }
            }
        }

        $response = new Response(json_encode(array('total' => $intTotal, 'listado' => $arrayServicios)));
        
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
     * Documentación para el método 'ajaxValidaTrasladoTnAction'.
     *
     * Método que valida deuda de cliente y estados de servicios en traslados TN
     *     
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 27-10-2017
     * @since   1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 19-06-2018 - Se agregan consideraciones adicionales para determinar si la factura se ecuentra vencida y asi determinar si
     *                           tiene deuda o no
     * @since   1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 05-07-2018 Se valida que el punto destino no posea un servicio Internet Small Business, sólo cuando uno de los servicios 
     *                         a ser trasladado sea Internet Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 06-02-2019 Se agrega validación de que exista un sólo servicio Telcohome por punto al realizar un traslado y 
     *                          se envía como parámetro el nombre técnico de servicio de Internet a la función validarIpsMaxPermitidasProducto
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 27-06-2019  Se modifica función que recupera servicios trasladados del punto en sesión
     * @since   1.3
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.5 17-06-2022 Se agrega una validacón y su respectivo mensaje para cuando el punto tenga un servicio INTERNET SAFE 
*                              no se permita realizar el traslado.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.6 08-07-2022 Se quita validación para que se permita realizar traslado de servicio INTERNET SAFE
     *                      
     */
    public function ajaxValidaTrasladoTnAction()
    {
        $objResponse        = new JsonResponse();
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emFinanciero       = $this->getDoctrine()->getManager('telconet_financiero');
        $objRequest         = $this->getRequest();
        $intIdPunto         = $objRequest->get("idPunto");
        $strEstado          = $objRequest->get("estado");
        $intPuntoId         = $objRequest->get("puntoId");
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $intEmpresaCod      = $objSession->get('idEmpresa');
        $serviceUtil        = $this->get('schema.Util');
        $strEstadoServicios = "";
        $strTieneDeuda      = "NO";
        $strPermiteTraslado = "SI";
        $intDeuda           = 0;
        $strDiferenciaDias  = 0;
        $intFacturasPendientes = 0;
        $strTiempoEsperaMeses  = 1;
        $strTiempoDias      = 30;
        $strStatus          = "ERROR";
        $strMsjAdicional    = "";
        $strMsjIntWifi      = "";
        $strMsjDataCenter   = "";
        $strMsjPuntoWifi    = "";
        $strMsjValidaIsb    = "";
        $strMsjIntSafe      = "";
        $strMensaje         = "Validacion ejecutada correctamente";
        $arrayResultadoAjax = array('estadoServicios' => $strEstadoServicios,
                                    'strTieneDeuda'      => $strTieneDeuda,
                                    'strPermiteTraslado' => $strPermiteTraslado,
                                    'strDeuda'           => $intDeuda,
                                    'strStatus'          => $strStatus,
                                    'strMensaje'         => $strMensaje
                                   );

        $arrayParametrosDeudaCliente = array();
        $arraySaldoPunto             = array();

        try
        {
            $objServicioRep      = $emComercial->getRepository('schemaBundle:InfoServicio');
            $arrayParamValida    = array();
            $arrayParamValida['intEmpresaCod']    = $intEmpresaCod;
            $arrayParamValida['intPuntoIdSesion'] = $intPuntoId;
            $arrayParamValida['intPuntoIdSeleccionado'] = $intIdPunto;
            $arrayTrasladados    = $objServicioRep->getServiciosTrasladados($arrayParamValida);
            $arrayResultado      = $objServicioRep->findServiciosByPuntoAndEstado($intIdPunto, $strEstado, $arrayTrasladados);
            $arrayListaServicios = $arrayResultado['registros'];

            //Se calcula la deuda total de un cliente
            $arrayParametrosDeudaCliente["intIdPunto"] = $intIdPunto;
            $arraySaldoPunto = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->obtieneDeudaPorCliente($arrayParametrosDeudaCliente);

            $intDeuda = $arraySaldoPunto["saldoTotal"];

            if ( $intDeuda > 0)
            {
                $arrayParametrosFactura["intIdPunto"]           = $intIdPunto;
                $arrayParametrosFactura["arrayTipoDocumentoId"] = array(1,5);
                $arrayParametrosFactura["strEstadoFactura"]     = "Activo";

                //Se obtiene la primera factura pendiente que tiene un cliente
                $strFechaEmision = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->obtienePrimeraFeEmision($arrayParametrosFactura);

                $dateFechaHoy = new \DateTime();
                $strFechaHoy = strval(date_format($dateFechaHoy, "d-m-Y"));

                $arrayParametrosDias["strFechaEmision"] = $strFechaEmision;
                $arrayParametrosDias["strFechaActual"]  = $strFechaHoy;

                if(!empty($strFechaEmision) && !empty($strFechaHoy))
                {
                    // Se calculan los dias transcurridos entre la fecha de emision de la factura contra la fecha del dia.
                    $strDiferenciaDias = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                      ->obtieneDiferenciaFechas($arrayParametrosDias);
                }

                //Obtiene el tiempo de espera de meses corte
                $arrayParametrosEspera["intIdPunto"] = $intIdPunto;

                $strTiempoEsperaMeses = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                     ->obtieneTiempoEsperaMeses($arrayParametrosEspera);

                if(!empty($strTiempoEsperaMeses["feEsperaMeses"]))
                {
                    if($strTiempoEsperaMeses["feEsperaMeses"] == 0 || $strTiempoEsperaMeses["feEsperaMeses"] == 1)
                    {
                        $strTiempoDias = 30;
                    }
                    if($strTiempoEsperaMeses["feEsperaMeses"] == 2)
                    {
                        $strTiempoDias = 60;
                    }
                    if($strTiempoEsperaMeses["feEsperaMeses"] == 3)
                    {
                        $strTiempoDias = 90;
                    }
                    if($strTiempoEsperaMeses["feEsperaMeses"] == 4)
                    {
                        $strTiempoDias = 120;
                    }
                }
                else
                {
                    $strTiempoDias = 30;
                }

                if($strDiferenciaDias > $strTiempoDias)
                {
                    $strTieneDeuda = "SI";
                }
                else
                {
                    $strTieneDeuda = "NO";
                }
            }

            foreach($arrayListaServicios as $objServicio)
            {
                if (strtolower($objServicio->getEstado()) != "activo"    &&
                    strtolower($objServicio->getEstado()) != "in-corte"  &&
                    strtolower($objServicio->getEstado()) != "anulado"   &&
                    strtolower($objServicio->getEstado()) != "rechazada" &&
                    strtolower($objServicio->getEstado()) != "rechazado" &&
                    strtolower($objServicio->getEstado()) != "trasladado" &&
                    strtolower($objServicio->getEstado()) != "reubicado" &&
                    strtolower($objServicio->getEstado()) != "cancel"    &&
                    strtolower($objServicio->getEstado()) != "eliminado")
                {
                    $strPermiteTraslado = "NO";
                    $strMensaje         = "El punto tiene servicios con estados no permitidos, dichos estados son: ";
                    $boolFalse = false;
                    if (strpos($strEstadoServicios, $objServicio->getEstado()) == $boolFalse)
                    {
                        $strEstadoServicios = $objServicio->getEstado() . " ";
                    }
                }
                
                if (strtolower($objServicio->getEstado()) == "activo" || 
                    strtolower($objServicio->getEstado()) == "in-corte" )
                {
                    $objProductoServicio = $objServicio->getProductoId();
                    if (is_object($objProductoServicio))
                    {
                        if ($objProductoServicio->getNombreTecnico() == "INTERNET WIFI")
                        {
                            $strPermiteTraslado = "NO";
                            $strMsjIntWifi      = "INTERNET WIFI ";
                        }
                        else if($objProductoServicio->getNombreTecnico() == "INTERNET SMALL BUSINESS" 
                                || $objProductoServicio->getNombreTecnico() == "TELCOHOME")
                        {
                            $arrayValidacionIsbParams       = array("intIdPunto"                    => $intPuntoId,
                                                                    "strCodEmpresa"                 => $objSession->get('idEmpresa'),
                                                                    "intIdProducto"                 => $objProductoServicio->getId(),
                                                                    "intIdProductoPrincipal"        => $objProductoServicio->getId(),
                                                                    "strDescripcionProdPrincipal"   => $objProductoServicio->getDescripcionProducto(),
                                                                    "intIdProductoIp"               => 0,
                                                                    "strDescripcionProdIp"          => "",
                                                                    "strNombreTecnicoProdPrincipal" => $objProductoServicio->getNombreTecnico());
                            $arrayRespuestaValidacionIsb    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->validarIpsMaxPermitidasProducto($arrayValidacionIsbParams);
                            if($arrayRespuestaValidacionIsb["strStatus"] !== "OK")
                            {
                                $strPermiteTraslado = "NO";
                                $strMsjValidaIsb    = "El punto al que desea realizar el traslado ya posee un servicio "
                                                       .$objProductoServicio->getDescripcionProducto();
                            }
                        }
                        else
                        {
                            if ($objProductoServicio->getGrupo() == "DATACENTER")
                            {
                                $strPermiteTraslado = "NO";
                                $strMsjDataCenter = "DATACENTER ";
                            }
                            else
                            {
                                if ($objServicio->getDescripcionPresentaFactura() == "Concentrador L3MPLS Administracion" ||
                                    $objServicio->getDescripcionPresentaFactura() == "Concentrador L3MPLS Navegacion" )
                                {
                                    $strPermiteTraslado = "NO";
                                    $strMsjPuntoWifi    = "Concentrador WIFI";
                                }
                            }
                        }
                        if ($objProductoServicio->getDescripcionProducto() == "INTERNET SAFE")
                        {
                            $strPermiteTraslado = "SI";
                            $strMsjIntSafe      = "INTERNET SAFE";
                        }
                    }
                }
            }
            /* En caso de pasar primera validación, ahora verificamos que los servicios no sean de tipo 
               INTERNET WIFI, DATA CENTER, INTERNET SAFE*/
            if ($strPermiteTraslado == "NO" &&
                (!empty($strMsjIntWifi) || !empty($strMsjDataCenter) || !empty($strMsjPuntoWifi) || !empty($strMsjValidaIsb) 
                || !empty($strMsjIntSafe))
               )
            {
                if ($strMensaje == "Validacion ejecutada correctamente")
                {
                    $strMensaje = "";
                }
                $strMsjAdicional = " El punto seleccionado tiene servicios con productos no permitidos, ";
                if (!empty($strMsjIntWifi))
                {
                    $strMsjAdicional .= $strMsjIntWifi;
                }
                if (!empty($strMsjDataCenter))
                {
                    $strMsjAdicional .= $strMsjDataCenter;
                }
                if (!empty($strMsjPuntoWifi))
                {
                     $strMsjAdicional .= $strMsjPuntoWifi;
                }
                if (!empty($strMsjValidaIsb))
                {
                     $strMsjAdicional .= $strMsjValidaIsb;
                }
                if (!empty($strMsjIntSafe))
                {
                     $strMsjAdicional .= $strMsjIntSafe;
                }
            }
            $strStatus  = "OK";
            $arrayResultadoAjax = array('estadoServicios' => $strEstadoServicios,
                                        'strTieneDeuda'      => $strTieneDeuda,
                                        'strPermiteTraslado' => $strPermiteTraslado,
                                        'strStatus'          => $strStatus,
                                        'strDeuda'           => strval($intDeuda),
                                        'strMensaje'         => $strMensaje.$strMsjAdicional
                                       );
        }
        catch (\Exception $objEx) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxValidaTrasladoTnAction',
                                      $objEx->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
            
            $strEstadoServicios = "";
            $strTieneDeuda      = "NO";
            $strPermiteTraslado = "NO";
            $strStatus          = "ERROR";
            $strMensaje         = "Existieron problemas al validar deuda del clientes y estados servicios del punto, notificar a Sistemas";
            
            $arrayResultadoAjax = array('estadoServicios' => $strEstadoServicios,
                                        'strTieneDeuda'      => $strTieneDeuda,
                                        'strPermiteTraslado' => $strPermiteTraslado,
                                        'strStatus'          => $strStatus,
                                        'strDeuda'           => strval($intDeuda),
                                        'strMensaje'         => $strMensaje.$strMsjAdicional
                                       );
        }       
        $objResponse->setData($arrayResultadoAjax);
        return $objResponse;
    }
    
    /**
     * ajaxValidaTrasladoMdAction
     *
     * Método que realiza validaciones de los servicios en traslados MD
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 27-06-2019
     * @since   1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-04-2020 - Se valida si el servicio tiene una solicitud: SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO activa.
     * @since   1.0
     */
    public function ajaxValidaTrasladoMdAction()
    {
        $objResponse        = new JsonResponse();
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $objRequest         = $this->getRequest();
        $intIdPunto         = $objRequest->get("idPunto");
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $strPermiteTraslado = "SI";
        $strStatus          = "ERROR";
        $strMensaje         = "La validaciones no pudieron ser ejecutadas, notificar a sistemas";
        $arrayResultadoAjax = array('strPermiteTraslado' => $strPermiteTraslado,
                                    'strStatus'          => $strStatus,
                                    'strMensaje'         => $strMensaje
                                   );
        try
        {
            $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                          ->findBy(array("puntoId" => $intIdPunto, "estado" => array('Activo','In-Corte')));
            
            $objTipoSolicitudPorSoporte = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                      ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
                                                                        'estado'               => 'Activo'));

            //Se consulta la solicitud SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO
            $objTipoSolicitudPorSoporteMasivo = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                                            ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
                                                                              'estado'               => 'Activo'));

            if(count($arrayServicios)>0 && is_object($objTipoSolicitudPorSoporte) && is_object($objTipoSolicitudPorSoporteMasivo))
            {
                foreach($arrayServicios as $objServicioPunto)
                {
                    if (is_object($objServicioPunto->getPlanId()))
                    {
                        //Se busca solicitudes pendientes de cambio de equipo por soporte de un cliente
                        $arrayDetSolPorSoporte = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                             ->findBy(array('servicioId'      => $objServicioPunto->getId(),
                                                                            'tipoSolicitudId' => $objTipoSolicitudPorSoporte->getId(),
                                                                            'estado'          => 'Pendiente'));
                        if(count($arrayDetSolPorSoporte)>0)
                        {
                            $strPermiteTraslado = 'NO';
                        }

                        $arrayDetSolPorSoporteMasivo = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                                   ->findBy(array('servicioId'      => $objServicioPunto->getId(),
                                                                                  'tipoSolicitudId' => $objTipoSolicitudPorSoporteMasivo->getId(),
                                                                                  'estado'          => 'Pendiente'));
                        if(count($arrayDetSolPorSoporteMasivo)>0)
                        {
                            $strPermiteTraslado = 'NO';
                        }
                    }
                }
            }

            $strStatus  = "OK";
            $arrayResultadoAjax = array('strPermiteTraslado' => $strPermiteTraslado,
                                        'strStatus'          => $strStatus,
                                        'strMensaje'         => 'Proceso validado exitosamente'
                                       );
        }
        catch (\Exception $objEx) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxValidaTrasladoMdAction',
                                      $objEx->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
            
            $strPermiteTraslado = "NO";
            $strStatus          = "ERROR";
            $strMensaje         = "Existieron problemas al validar los servicios del punto, notificar a Sistemas";
            
            $arrayResultadoAjax = array('strPermiteTraslado' => $strPermiteTraslado,
                                        'strStatus'          => $strStatus,
                                        'strMensaje'         => $strMensaje
                                       );
        }       
        $objResponse->setData($arrayResultadoAjax);
        return $objResponse;
    }
    
    /**
     * ajaxActualizaPrecioTrasladoAction
     * 
     * Documentación para el método 'ajaxActualizaPrecioTrasladoAction'.
     *
     * Método que actualiza el precio de solicitudes de traslados TN
     *     
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 06-11-2017
     * @since   1.0
     */
    public function ajaxActualizaPrecioTrasladoAction()
    {
        $objResponse        = new JsonResponse();
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $objRequest         = $this->getRequest();
        $intIdSolicitud     = $objRequest->get("idSolicitud");
        $strPrecioNuevo     = $objRequest->get("precioNuevo");
        $strObservacion     = $objRequest->get("observacion");
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $strStatus          = "ERROR";
        $strMensaje         = "Problemas al ejecutar la actualización, notifique a sistemas.";
        $arrayResultadoAjax = array('strStatus'  => $strStatus,
                                    'strMensaje' => $strMensaje
                                   );
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $objSolicitudTraslado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            
            if(is_object($objSolicitudTraslado))
            {
                $strPrecioAnterior = $objSolicitudTraslado->getPrecioDescuento();
                $objSolicitudTraslado->setPrecioDescuento($strPrecioNuevo);
                $emComercial->persist($objSolicitudTraslado);
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitudTraslado);
                $objSolicitudHistorial->setUsrCreacion($strUsrSesion);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setIpCreacion($strIpClient);
                $objSolicitudHistorial->setEstado($objSolicitudTraslado->getEstado());
                $objSolicitudHistorial->setObservacion("Se actualizó precio del traslado: <br>".
                                                       "Precio anterior : ".$strPrecioAnterior."<br>".
                                                       "Precio nuevo    : ".$strPrecioNuevo."<br><br>".
                                                       $strObservacion
                                                      );        
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();
                $emComercial->getConnection()->commit();
                
                $strStatus  = "OK";
                $strMensaje = "Precio actualizado exitosamente!";
                $arrayResultadoAjax = array('strStatus'          => $strStatus,
                                            'strMensaje'         => $strMensaje
                                           );
            }
        }
        catch (\Exception $objEx) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxActualizaPrecioTrasladoAction',
                                      $objEx->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
            
            $strStatus  = "ERROR";
            $strMensaje = "Existieron problemas al actualizar el precio del traslado, notificar a Sistemas";
            
            $arrayResultadoAjax = array('strStatus'  => $strStatus,
                                        'strMensaje' => $strMensaje
                                       );
        } 
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        $objResponse->setData($arrayResultadoAjax);
        return $objResponse;
    }         
    
    /**
     * ajaxAprobarSolicitudReubicacionAction
     * 
     * Documentación para el método 'ajaxAprobarSolicitudReubicacionAction'.
     *
     * Método que aprueba la solicitud de reubicacion TN
     *     
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 06-11-2017
     * @since   1.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 02-06-2018    Se agrega actualización de fecha de creación de solicitud por requerimiento de PYL
     * @since   1.0
     */
    public function ajaxAprobarRechazarSolicitudReubicacionAction()
    {
        $objResponse        = new JsonResponse();
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $objRequest         = $this->getRequest();
        $intIdSolicitud     = $objRequest->get("idSolicitud");
        $strObservacion     = $objRequest->get("observacion");
        $strProceso         = $objRequest->get("proceso");
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $strStatus          = "ERROR";
        $strMensaje         = "Problemas al ejecutar la transacción, notifique a sistemas.";
        $arrayResultadoAjax = array('strStatus'  => $strStatus,
                                    'strMensaje' => $strMensaje
                                   );
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $objSolicitudReubicacion = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            
            if(is_object($objSolicitudReubicacion))
            {
                if ($strProceso == "PrePlanificada")
                {
                    $objSolicitudReubicacion->setFeCreacion(new \DateTime('now'));
                }
                $objSolicitudReubicacion->setEstado($strProceso);
                $emComercial->persist($objSolicitudReubicacion);
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitudReubicacion);
                $objSolicitudHistorial->setUsrCreacion($strUsrSesion);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setIpCreacion($strIpClient);
                $objSolicitudHistorial->setEstado($objSolicitudReubicacion->getEstado());
                $objSolicitudHistorial->setObservacion($strObservacion);        
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();
                
                //actualizar las solicitudes caract
                $arraySolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                              ->findBy(array("detalleSolicitudId"  => $objSolicitudReubicacion->getId(), 
                                                             "estado"              => "Pendiente"));
                foreach($arraySolCaract as $objDetalleSolCarac)
                {
                    $objDetalleSolCarac->setEstado($objSolicitudReubicacion->getEstado());
                    $emComercial->persist($objDetalleSolCarac);
                    $emComercial->flush();
                }
                
                $emComercial->getConnection()->commit();
                $strStatus  = "OK";
                $strMensaje = "Se procesó la solicitud exitosamente!";
                $arrayResultadoAjax = array('strStatus'          => $strStatus,
                                            'strMensaje'         => $strMensaje
                                           );
            }
        }
        catch (\Exception $objEx) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxAprobarRechazarSolicitudReubicacionAction',
                                      $objEx->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
            
            $strStatus  = "ERROR";
            $strMensaje = "Existieron problemas al ejecutar transacción, notificar a Sistemas";
            
            $arrayResultadoAjax = array('strStatus'  => $strStatus,
                                        'strMensaje' => $strMensaje
                                       );
        }    
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        $objResponse->setData($arrayResultadoAjax);
        return $objResponse;
    }
    
    /**
     * ajaxAprobarRechazarSolicitudSoporteAction
     * 
     * Documentación para el método 'ajaxAprobarRechazarSolicitudSoporteAction'.
     *
     * Método que aprueba la solicitud de cambio de equipo por soporte MD
     *     
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 12-05-2019
     * @since   1.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 11-07-2019    Se agrega envío de notificación al área de pyl para informar que la solicitud de cambio
     *                            de equipo por soporte requiere un modelo de CPE ONT en específico
     * @since   1.0
     */
    public function ajaxAprobarRechazarSolicitudSoporteAction()
    {
        $objResponse        = new JsonResponse();
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $objRequest         = $this->getRequest();
        $intIdSolicitud     = $objRequest->get("idSolicitud");
        $strObservacion     = $objRequest->get("observacion");
        $strProceso         = $objRequest->get("proceso");
        $strModeloOnt       = $objRequest->get("strModeloOnt");
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $strStatus          = "ERROR";
        $strProcesoObs      = !empty($strProceso)?($strProceso=="PrePlanificada"?"aprobó":"rechazó"):"SIN PROCESO";
        $strMensaje         = "Problemas al ejecutar la transacción, notifique a sistemas.";
        $arrayResultadoAjax = array('strStatus'  => $strStatus,
                                    'strMensaje' => $strMensaje
                                   );
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
            $objSolCamEquiPorSoporte = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            
            if(is_object($objSolCamEquiPorSoporte))
            {
                if ($strProceso == "PrePlanificada")
                {
                    $objSolCamEquiPorSoporte->setFeCreacion(new \DateTime('now'));
                    
                    $objCaractModeloElemento = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array('descripcionCaracteristica'  => 'MODELO ROUTER',
                                                                             'estado'                     => 'Activo'));
                    if (!is_object($objCaractModeloElemento))
                    {
                        throw new \Exception("No se encontró información acerca de característica MODELO ELEMENTO");
                    }
                    $objDetalleSolCaractModeloElemento = new InfoDetalleSolCaract();
                    $objDetalleSolCaractModeloElemento->setCaracteristicaId($objCaractModeloElemento);
                    $objDetalleSolCaractModeloElemento->setDetalleSolicitudId($objSolCamEquiPorSoporte);
                    $objDetalleSolCaractModeloElemento->setValor($strModeloOnt);
                    $objDetalleSolCaractModeloElemento->setEstado($strProceso);
                    $objDetalleSolCaractModeloElemento->setUsrCreacion($strUsrSesion);
                    $objDetalleSolCaractModeloElemento->setFeCreacion(new \DateTime('now'));
                    $emComercial->persist($objDetalleSolCaractModeloElemento);
                    $emComercial->flush();
                }
                $objSolCamEquiPorSoporte->setEstado($strProceso);
                $emComercial->persist($objSolCamEquiPorSoporte);
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolCamEquiPorSoporte);
                $objSolicitudHistorial->setUsrCreacion($strUsrSesion);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setIpCreacion($strIpClient);
                $objSolicitudHistorial->setEstado($objSolCamEquiPorSoporte->getEstado());
                $objSolicitudHistorial->setObservacion($strObservacion);        
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();
                
                //Se registra historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objSolCamEquiPorSoporte->getServicioId());
                $objServicioHistorial->setObservacion("Se ".$strProcesoObs." la solicitud, ".$strObservacion.".");
                $objServicioHistorial->setIpCreacion($strIpClient);
                $objServicioHistorial->setUsrCreacion($strUsrSesion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setEstado($objSolCamEquiPorSoporte->getServicioId()->getEstado());
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush();
                
                //actualizar las solicitudes caract
                $arraySolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                              ->findBy(array("detalleSolicitudId"  => $objSolCamEquiPorSoporte->getId(), 
                                                             "estado"              => "Pendiente"));
                foreach($arraySolCaract as $objDetalleSolCarac)
                {
                    $objDetalleSolCarac->setEstado($objSolCamEquiPorSoporte->getEstado());
                    $emComercial->persist($objDetalleSolCarac);
                    $emComercial->flush();
                }
                
                $emComercial->getConnection()->commit();
                $strStatus  = "OK";
                $strMensaje = "Se procesó la solicitud exitosamente!";
                $arrayResultadoAjax = array('strStatus'          => $strStatus,
                                            'strMensaje'         => $strMensaje
                                           );
                
                /* enviar Notificación a usuario de PYL para indicar que equipo es el asignado
                   al cliente mediante la solicitud de cambio de equipo por soporte */
                $objServicio = $objSolCamEquiPorSoporte->getServicioId();
                if(!is_object($objServicio) )
                {
                    throw new \Exception('No se encontró servicio asociado a la solicitud.');
                }
                $objPunto    = $objServicio->getPuntoId();
                if(!is_object($objPunto) )
                {
                    throw new \Exception('No se encontró punto asociado a la servicio.');
                }
                $objTipoSolicitud     = $objSolCamEquiPorSoporte->getTipoSolicitudId();
                if(!is_object($objTipoSolicitud) )
                {
                    throw new \Exception('No se encontró tipo de solicitud asociado a la solicitud.');
                }
                $strObservacionCorreo = $objTipoSolicitud->getDescripcionSolicitud()." aprobada, continuar con el flujo de ".
                                        "planificación considerando el Modelo de CPE a instalar en el cliente.";
                $serviceTecnico->envioNotifCambioEquipoPorSoporte( array( 
                                                                    "objPunto"                  => $objPunto,
                                                                    "objServicio"               => $objServicio,
                                                                    "strObservacionCorreo"      => $strObservacionCorreo,
                                                                    "strDescripcionSolicitud"   => $objSolCamEquiPorSoporte->getTipoSolicitudId()
                                                                                                                           ->getDescripcionSolicitud(),
                                                                    "strUsrCreacion"            => $strUsrSesion,
                                                                    "strIpCreacion"             => $strIpClient,
                                                                    "strCodigoPlantilla"        => "CEPS-APRUEBASOL",
                                                                    "strModeloCpe"              => $strModeloOnt));
            }
        }
        catch (\Exception $objEx) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxAprobarRechazarSolicitudSoporteAction',
                                      $objEx->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
            
            $strStatus  = "ERROR";
            $strMensaje = "Existieron problemas al ejecutar transacción, notificar a Sistemas";
            
            $arrayResultadoAjax = array('strStatus'  => $strStatus,
                                        'strMensaje' => $strMensaje
                                       );
        }    
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        $objResponse->setData($arrayResultadoAjax);
        return $objResponse;
    }
    
    /**
     * Funcion para agregar Servicios a un Punto o Login
     * @author : telcos
     * @author : apenaherrera
     * @version 1.0 06-11-2014
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 29-04-2016
     * Se envía el prefijo de la empresa al twig para validar que solo TN disponga de "Última Milla" -> "Ninguna".
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 01-08-2016
     * Se agrega ingreso de Vendedor por Servicio, opcion precargara el vendedor asignado al punto y permite escoger o cambiar
     * vendedor asignado al servicio, sera permitido para todas las empresa.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 12-07-2016
     * Se recibe el rol del cliente en sesión y se envía a la pantalla de New Servicio para renderizar el botón que regresa al show del punto.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 14-09-2016    
     * Se modificó contenido del combo Tipo de Orden: 
     * Para TN:
     *             N: Nueva
     *             T: Traslado
     *             R: Reubicacion
     * Para MD:
     *             N: Nueva
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 26-10-2018 Se obtiene la información de la solicitud de aprobación de servicio asociada al cliente y se genera el respectivo
     *                         mensaje de validación al agregar un producto que necesite de este tipo de solicitud    
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.5 03-09-2020 - Se agrega lógica para listar las propuestas de TelcoCRM en base al cliente en sesión.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 04-10-2020 - Se agrega validación para verificar si el punto ya tiene un servicio con producto IP FIJA WAN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 09-03-2021 Se agrega lógica para poder elegir el tipo de red al momento de crear el servicio, 
     *                         con el criterio de que la cobertura del punto pertenezca al listado parametrizado,
     *                         estos cambios solo aplican para la empresa Telconet.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 20-09-2021 - Se agrega validación de múltiples input con una clase específica,
     *                           que permita el ingreso por lo mínimo de uno o todos.
     * 
     * @author Edgar Pin villavicencio <epin@telconet.ec>
     * @version 1.9 23-08-2022 - Se valida para que la empresa MD el vendedor no sea el del punto sino el usuario que este logueado
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 01-08-2022 - Se agrega validación de los input con relación de clases para el producto INTERNET VPNoGPON.
     *
     * @see \telconet\schemaBundle\Entity\InfoServicio
     * @return Renders a view.
     */
    public function newAction($rol)
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $cliente    = $session->get('cliente');
        $ptocliente = $session->get('ptoCliente');
        
        $strEmpresaCod     = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $strUsrCreacion    = $session->get('user');                
        $em                = $this->getDoctrine()->getManager();
        $entity            = new InfoServicio();
        $form              = $this->createForm(new InfoServicioType(array('strPrefijoEmpresa'=> $strPrefijoEmpresa)), $entity);
        $strLoginEmpleado  = '';
        $strNombreEmpleado = '';
        $serviceTelcoCrm   = $this->get('comercial.ComercialCrm');
        $arrayPropuestas   = array();
        $strExisteIpWan    = 'NO';
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        
        $objInfoPunto      = $em->getRepository('schemaBundle:InfoPunto')->find($ptocliente['id']);
        if( !$objInfoPunto )
        {
            throw $this->createNotFoundException('No se encontro Punto Cliente');
        }        
        if($objInfoPunto->getUsrVendedor())
        {            
            $arrayParametros['EMPRESA'] = $strEmpresaCod;
            $arrayParametros['LOGIN']   = ($strPrefijoEmpresa == "MD"  || $strPrefijoEmpresa == "EN" ) ? 
                                          $strUsrCreacion : $objInfoPunto->getUsrVendedor();
        
            $arrayResultado     = $em->getRepository('schemaBundle:InfoPersona')->getResultadoVendedoresPorEmpresa($arrayParametros);

            if($arrayResultado['TOTAL'] > 0)
            {
                $strLoginEmpleado  = $arrayResultado['REGISTROS']['login'];
                $strNombreEmpleado = $arrayResultado['REGISTROS']['nombre'];
            }           
        }
                       
        $parametros = array(
            'entity'                 => $entity,
            'punto_id'               => '',
            'cliente'                => '',
            'tipo_medio'             => '',
            'frecuencia_item'        => '',
            'nombre_tipo_negocio'    => '',
            'nombre_tipo_negocio_no' => '',
            'form'                   => $form->createView(),
            'loginEmpleado'          => '',
            'nombreEmpleado'         => '',
            'existeIpWan'            => $strExisteIpWan,
            'cantidadTotalIngresada' => 0,
        );
        $parametros['loginEmpleado']  = $strLoginEmpleado;
        $parametros['nombreEmpleado'] = $strNombreEmpleado;

        $parametros['prefijoEmpresa'] = $strPrefijoEmpresa;
        
        if( $ptocliente )
        {
            $parametros['punto_id'] = $ptocliente;
            $parametros['cliente']  = $cliente;            
            $tipo_negocio           = $em->getRepository('schemaBundle:AdmiTipoNegocio')->find($ptocliente['id_tipo_negocio']);
            if (strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'HOME' )
            {
                $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                $nombre_tipo_negocio_no = 'PYME';
            }
            elseif( strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'PYME' )
            {
                $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                $nombre_tipo_negocio_no = 'HOME';
            }
            elseif( strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'ISP' )
            {
                $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                $nombre_tipo_negocio_no = 'UM';
            }
            else
            {
                $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                $nombre_tipo_negocio_no = '';
            }
            $parametros['nombre_tipo_negocio']    = $nombre_tipo_negocio;
            $parametros['nombre_tipo_negocio_no'] = $nombre_tipo_negocio_no;


            $em_infraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
            $tipo_medio         = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->findByEstado('Activo');
            
            $parametros['tipo_medio'] = $tipo_medio;

            $objFrecuenciaItem  = $em->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get("FRECUENCIA_FACTURACION", "", "", "", "", "", "", "", "", $strEmpresaCod);
            $parametros['frecuencia_item'] = $objFrecuenciaItem;
            // Recorro las frecuencias para mostrar el mensaje informativo
            $strFrecuencias = "";
            foreach($objFrecuenciaItem as $objFrecuencia)
            {
                $strFrecuencias .= $objFrecuencia['valor1'] . ' ' . $objFrecuencia['valor2'] . ', ';
            }
            if($strFrecuencias)
            {
                $strFrecuencias = substr($strFrecuencias, 0, -2) . '...';
            }
            $parametros['frecuencias'] = $strFrecuencias;

            if($strPrefijoEmpresa == 'MD' && strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'PYME')
            {
                $arrayParametrosExisteIpWan = array('intIdPunto' => $ptocliente['id']);
                $arrayRespuestaExisteIpWan  = $em->getRepository('schemaBundle:InfoServicio')->getExisteIpWan($arrayParametrosExisteIpWan);
                if ($arrayRespuestaExisteIpWan['strStatus'] === "OK")
                {
                    $parametros['existeIpWan'] = $arrayRespuestaExisteIpWan['strExisteIpWan'];
                }
            }
        }
            $parametros['rol'] = $rol;
            
        $strMsjSolAprobServicio = "";    
        if($strPrefijoEmpresa == 'TN' && is_object($objInfoPunto->getPersonaEmpresaRolId()))
        {
            $arrayParamsSolicitud       = array("arrayEstadosSolicitudes"       => array("Pendiente", "Aprobada"),
                                                "intValorDetSolCaract"          => $objInfoPunto->getPersonaEmpresaRolId()->getId(),
                                                "strDescripcionSolicitud"       => "SOLICITUD APROBACION SERVICIO",
                                                "strDescripcionCaracteristica"  => "ID_PERSONA_ROL",
                                                "strConServicio"                => "SI");
            
            $arrayRespuestaSolicitud    = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                             ->getSolicitudesPorDetSolCaracts($arrayParamsSolicitud);
            $intTotalSolicitud          = $arrayRespuestaSolicitud["intTotal"];
            $arrayResultadoSolicitud    = $arrayRespuestaSolicitud["arrayResultado"];
            if($intTotalSolicitud > 0 && !empty($arrayResultadoSolicitud))
            {
                $arrayDetalleSolicitud      = $arrayResultadoSolicitud[0];
                if($arrayDetalleSolicitud["estadoSolicitud"] === "Pendiente")
                {
                    $strMsjSolAprobServicio .= "El servicio agregado requiere la aprobación de la ".$arrayDetalleSolicitud["descripcionSolicitud"]
                                            ." No. ".$arrayDetalleSolicitud["idSolicitud"]
                                            ." asociada al login ".$arrayDetalleSolicitud["loginPuntoSolicitud"]
                                            ." para continuar con el proceso de activación";
                }
                else
                {
                    $strMsjSolAprobServicio .= "El cliente ya posee la ".$arrayDetalleSolicitud["descripcionSolicitud"]." "
                                            ." No. ".$arrayDetalleSolicitud["idSolicitud"]
                                            ." asociada al login ".$arrayDetalleSolicitud["loginPuntoSolicitud"]
                                            ." en estado ".$arrayDetalleSolicitud["estadoSolicitud"];
                    
                }
            }
            else
            {
                $strMsjSolAprobServicio .= "El servicio agregado requiere aprobación. Se generará una solicitud de aprobación del servicio "
                                        ."asociada al login ".$objInfoPunto->getLogin();
            }
        }
        $parametros['msjSolAprobServicio'] = $strMsjSolAprobServicio;
        $parametros['strCiudadPermitida']  = 'S';
        $parametros['arrayListaTipoRed']   = array();
        $parametros['arrayListaClassRelacion'] = array();
        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN')
        {
            $objPersona           = $em->getRepository('schemaBundle:InfoPersona')
                                       ->find($objInfoPunto->getPersonaEmpresaRolId()
                                                           ->getPersonaId()
                                                           ->getId());
            if(!empty($objPersona) && is_object($objPersona))
            {
                $arrayParametros      = array("strRuc"               => $objPersona->getIdentificacionCliente(),
                                              "strPrefijoEmpresa"    => $strPrefijoEmpresa, 
                                              "strCodEmpresa"        => $strEmpresaCod);
                $arrayParametrosWSCrm = array("arrayParametrosCRM"   => $arrayParametros,
                                              "strOp"                => 'getPropuesta',
                                              "strFuncion"           => 'procesar');
                $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                if(isset($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"]))
                {
                    foreach($arrayRespuestaWSCrm["resultado"] as $arrayItem)
                    {
                        array_push($arrayPropuestas,array("intIdPropuesta" => $arrayItem->intIdPropuesta,
                                                          "strPropuesta"   => $arrayItem->strPropuesta));
                    }
                }
            }
            $arrayCiudadDisponible = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    '',
                                                                                                    $ptocliente['id_cobertura'],
                                                                                                    $ptocliente['cobertura'],
                                                                                                    '',
                                                                                                    'S',
                                                                                                    'CIUDADES_DISPONIBLES',
                                                                                                    $strEmpresaCod);
            $strCiudadPermitida    = (!empty($arrayCiudadDisponible) && is_array($arrayCiudadDisponible) && $strPrefijoEmpresa == 'TN') ? 'S':'N';
            $parametros['strCiudadPermitida'] = $strCiudadPermitida;
            $arrayListaTipoRed     = array();
            $arrayTipoRedDetalles  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('PROD_TIPO_RED',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                   '',
                                                                                                    $strEmpresaCod);
            foreach($arrayTipoRedDetalles as $arrayItemDet)
            {
                $arrayListaTipoRed[] = array(
                    'strValue'    => $arrayItemDet['valor1'],
                    'strTipo'     => $arrayItemDet['valor2'],
                    'strSelected' => $arrayItemDet['valor3']
                );
            }
            $parametros['arrayListaTipoRed'] = $arrayListaTipoRed;
            //se obtiene las relaciones de clases para permitir el ingreso de cualquiera de los input por la clase
            $arrayListaClassRelacion    = array();
            $arrayClassRelacionDetalles = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('PRODUCTO_CARACTERISTICA_CLASS_RELACION',
                                                                              'COMERCIAL',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '');
            foreach($arrayClassRelacionDetalles as $arrayItemDet)
            {
                if(!in_array($arrayItemDet['valor2'], $arrayListaClassRelacion) &&
                    strrpos($arrayItemDet['valor2'],'-null-') === false)
                {
                    $arrayListaClassRelacion[] = $arrayItemDet['valor2'];
                }
            }
            $parametros['arrayListaClassRelacion'] = $arrayListaClassRelacion;
        }
        $parametros['arrayListadoPropuesta'] = (is_array($arrayPropuestas) && !empty($arrayPropuestas)) ? $arrayPropuestas:array();
        return $this->render('comercialBundle:infoservicio:new.html.twig', $parametros);
    }

    /**
     * Funcion utilizada para la creación de nuevos servicios.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 27-10-2015
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.2 14-07-2016
     * Se omite el envío del Id de la Última Milla como parámetro, se toma la última milla por cada nuevo servicio.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 03-04-2017 Se realizan cambios en el envío de parámetros a la función crearServicio del service InfoServicioService
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.4 03-10-2022 Se agrega funcionalidad para remover palabra NINGUNO cuando es un producto NG FIREWALL
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.5 01-12-2022 Se agrega validación para determinar si se requiere cambio de equipo para migración SDWAN
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.6 08-02-2023 Se agrega validación al generar tareas internas en migraciones SDWAN
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.7 21-04-2023 Se corrije BUG al valdar array de parámetros en SDWAN
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.5 21-11-2022 Se añade valores que vienen del request para horas de soporte
     *                         Se condiciono en el caso de ser solo los productos de paquete de soporte y se consulta el uuid
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.8 09-12-2022 Se agrega a arrayParamsServicio los archivos cargados para el servicio SAFE ENTRY
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.8 09-12-2022 Se agrega a arrayParamsServicio los archivos cargados para el servicio SAFE ENTRY
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.8 09-12-2022 Se agrega a arrayParamsServicio los archivos cargados para el servicio SAFE ENTRY
     * 
     * @param Request $request
     * @param integer $servicioId
     * 
     * @return redirect
     **/
    public function createAction(Request $request)
    {
        $session     = $request->getSession();
        $objEm       = $this->getDoctrine()->getManager();
        $emGeneral       = $this->get('doctrine')->getManager('telconet_general');
        $codEmpresa  = $session->get('idEmpresa');
        $idOficina   = $session->get('idOficina');
        $usrCreacion = $session->get('user');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');       
        $clientIp    = $request->getClientIp();
        $serviceGeneral = $this->get('doctrine')->getManager('telconet_general');
        $serviceSoporte = $this->get('soporte.soporteservice');
        $serviceEmcom   = $this->getDoctrine()->getManager('telconet');
        $emSoporte      = $this->get('doctrine')->getManager('telconet_soporte');
        $entityServiceTecnico = $this->get('tecnico.InfoServicioTecnico');

        $arrayArchivos = array();


        // Verificar: Si el pto esta en session, si no tomarlo del formulario
        $ptocliente  = $session->get('ptoCliente');
        if ($ptocliente)
        {
            $idPto = $ptocliente['id'];
        }
        else
        {
            $idPto = $request->request->get('puntoid');
        }

        $entityPunto = $serviceEmcom->getRepository('schemaBundle:InfoPunto')->find($idPto);
        $entityRol   = $serviceEmcom->getRepository('schemaBundle:AdmiRol')
        ->find($entityPunto->getPersonaEmpresaRolId()
        ->getEmpresaRolId()
        ->getRolId());

        $datos_form       = $request->request->get('infoserviciotype');
        $tipoOrden        = $datos_form['tipoOrden'];
        $datos            = $request->get('valores');
        $servicios        = json_decode($datos, true);

        //Valores que vienen llenos si es un paquete de soporte
        $strUuidPaquete       = $request->request->get('strUuidPaquete');
        $strTipoPaquete       = $request->request->get('strTipoPaquete');
        

        if(is_array($servicios) && count($servicios) > 0)
        {
            foreach($servicios as &$arrayServicio)
            {
                $objProducto = $objEm->getRepository('schemaBundle:AdmiProducto')
                ->find($arrayServicio['codigo']);
                // VALIDACIÓN SOLO PARA PRODUCTO NG FIREWALL
                // SE DETERMINA SI EL SERVICIO ES NG FIREWALL
                if(is_object($objProducto) && $objProducto->getDescripcionProducto() === 'SECURITY NG FIREWALL')
                {
                    // SE REMUEVE LA CADENA <<NINGUNO>> DE LA DESCRIPCIÓN
                    $arrayServicio['producto'] = str_replace('NINGUNO','', $arrayServicio['producto']);
                }
                //Si el producto a ingresar es SAFE ENTRY se obtienen los documentos parametrizados desde el request
                if(is_object($objProducto) && $objProducto->getDescripcionProducto() === 'SAFE ENTRY')
                {
                    $arrayArchivosRequeridos =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('CONFIG SAFE ENTRY','COMERCIAL','','ARCHIVOS_REQUERIDOS','','','','','','10');
                    foreach($arrayArchivosRequeridos as $arrayArchivo)
                    {               
                        array_push($arrayArchivos,$this->getRequest()->files->get($arrayArchivo['valor3'].'_file'));
                    }
                }
            }
        }

        //INICIO  validación si es migracion SDWAN **********************************
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $arrayCheckCambioEquipo = $serviceInfoServicio->validarCambioEquipoSDWAN($idPto, $servicios);
        //FIN  validación si es migracion SDWAN **********************************



        //INICIO  validación tarea interna si es migracion SDWAN

        if(is_array($arrayCheckCambioEquipo) 
        && $arrayCheckCambioEquipo['esSdwan'] === true 
        && $arrayCheckCambioEquipo['esMigracion'] === true)
        {
            if($arrayCheckCambioEquipo['error'] !== false)
            {
                $session->getFlashBag()->add('error', $arrayCheckCambioEquipo['msg']);
                return $this->redirect($this->generateUrl('infopunto_show', array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())));
            }
                // SE VERIFICA SI EXISTEN SERVICIOS CUYO EQUIPO DEBE SER CAMBIADO
            if(($arrayCheckCambioEquipo['requiereCambioEquipo'] === true))
            {

                $arrayParametroTareaCambioEquipo = $serviceGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getArrayDetalleParametros(array(
                        'strNombreParametroCab' => 'TAREA_CAMBIO_EQUIPO_MIGRACION_SDWAN',
                        'estado'=>'Activo'
                    ));


                // SE VALIDA QUE EXISTA EL PARÁMETRO CON LA DATA DE LA TAREA DE CAMBIO DE EQUIPO
                if(is_array($arrayParametroTareaCambioEquipo) 
                && count($arrayParametroTareaCambioEquipo)>0
                && count($arrayParametroTareaCambioEquipo['encontrados'])>0
                )
                {
                    if(
                        empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor1'])
                        || empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor2'])
                        || empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor3'])
                        || empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor4'])
                        || empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor5'])
                        || empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor6'])
                        || empty($arrayParametroTareaCambioEquipo['encontrados'][0]['valor7'])
                    )
                    {
                        $session->getFlashBag()->add('error', 'Detalle de tarea de cambio de equipo incompleto.');
                        return $this->redirect(
                            $this->generateUrl('infopunto_show', 
                                array('id' => $idPto, 
                                'rol'=> $entityRol->getDescripcionRol()
                                )
                            )
                        );    
                    }


                    $entityPunto = $objEm->getRepository('schemaBundle:InfoPunto')->find($idPto);



                    $arrayPersonaPYL = $objEm->getRepository('schemaBundle:InfoPersona')
                                                        ->getOnePersonaBy(
                                                            array(
                                                                'idPersonaRol' => $arrayParametroTareaCambioEquipo['encontrados'][0]['valor4'],
                                                            ));

                    if(!is_array($arrayPersonaPYL) || count($arrayPersonaPYL) === 0)
                    {
                        $session->getFlashBag()
                        ->add(
                            'error', 
                                "Error al intentar generar tarea por cambio de equipo. Responsable no definido. 
                                Póngase en contacto con Dpto. de Sistemas"
                        );   
                        
                        return $this->redirect(
                            $this->generateUrl('infopunto_show', array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol()))
                        );

                    }
                                           

                    $arrayPersonaPYL['nombreCompleto'] = $arrayPersonaPYL['nombres'].' '.$arrayPersonaPYL['apellidos'];
                    $strResponsable = $arrayPersonaPYL['nombres'].' '.$arrayPersonaPYL['apellidos'];


                    $objPersonaEmpresaRol = $entityPunto->getPersonaEmpresaRolId();
                    if(is_object($objPersonaEmpresaRol))
                    {
                        $objPersona = $objPersonaEmpresaRol->getPersonaId();
                        $strCliente = sprintf("%s",$objPersona);
                    }
                    else
                    {
                        $strCliente = "";
                    }


                    // SE ITERA SOBRE ARREGLO PARA GENERAR TAREAS INTERNAS
                    foreach($arrayCheckCambioEquipo['arrayIdsEquipos'] as $objServicio)
                    {
                        // SE VALIDA QUE DATA DE SERVICIO ESTÉ DISPONIBLE
                        if(!is_object($objServicio) || !is_object($objServicio->getProductoId()))
                        {
                            $session->getFlashBag()
                            ->add(
                                'error', 
                                "Información de servicio incompleta. Comuníquese con departamento de sistemas."  
                            );

                            return $this->redirect(
                                $this->generateUrl('infopunto_show', 
                                array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())
                                )
                            );  
                        }

                        $arrayDetalleTareaInternaSDWAN = array();
                        $objServProdCaractDetalleTareaSDWAN = $entityServiceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                    'DETALLE_TAREA_INTERNA_SDWAN',
                                                                                    $objServicio->getProductoId());



                        $strObservacionSDWAN = $arrayParametroTareaCambioEquipo['encontrados'][0]['valor3'];
                        $strObservacionDetalladaSDWAN = $arrayParametroTareaCambioEquipo['encontrados'][0]['valor7'];

                        // SE AGREGA INFO DE SERVICIO
                        if(is_object($objServicio) && $objServicio->getLoginAux())
                        {
                            $strObservacionSDWAN = str_replace(
                                array('%loginAux%'),
                                array($objServicio->getLoginAux()),
                                $strObservacionSDWAN
                            );
                        }
                        


                        // INICIO SE OBTIENEN CARACTERÍSTICAS DEL SERVICIO PRINCIPAL
                        if(isset($arrayCheckCambioEquipo['arrayCaracteristicasAdic']) 
                           && is_array($arrayCheckCambioEquipo['arrayCaracteristicasAdic'])
                           && count($arrayCheckCambioEquipo['arrayCaracteristicasAdic'])>0
                           )
                        {
                            foreach($arrayCheckCambioEquipo['arrayCaracteristicasAdic'] as $objcaractAdicional)
                            {
                                if(strstr($objcaractAdicional->caracteristica, 'NombreModeloElemento') && ($objcaractAdicional->valor))
                                {
                                    $strObservacionDetalladaSDWAN = str_replace(
                                        '%modeloEquipo%',
                                        $objcaractAdicional->valor,
                                        $strObservacionDetalladaSDWAN
                                    );
                                }

                                if(strstr($objcaractAdicional->caracteristica, 'CAPACIDAD1') && ($objcaractAdicional->valor))
                                {
                                    $strObservacionDetalladaSDWAN = str_replace(
                                        '%capacidad%',
                                        $objcaractAdicional->valor,
                                        $strObservacionDetalladaSDWAN
                                    );
                                }

                                if(strstr($objcaractAdicional->caracteristica, '[CANTIDAD USUARIOS SDWAN]') && ($objcaractAdicional->valor))
                                {
                                    $strObservacionDetalladaSDWAN = str_replace(
                                        '%usuariosMigracionSDWAN%',
                                        $objcaractAdicional->valor,
                                        $strObservacionDetalladaSDWAN
                                    );                                
                                }
                            }
                        }

                        //SE CONCATENA DETALLE DE TAREA
                        $strObservacionDetalladaSDWAN = $strObservacionSDWAN . $strObservacionDetalladaSDWAN;

                        // SE VALIDA QUE TODOS LOS PARÁMETROS HAYAN SIDO REEMPLAZADOS
                        $arrayParametrosEncontrados = array();
                        preg_match_all("/(?:%[^\s]+%)/", $strObservacionDetalladaSDWAN, $arrayParametrosEncontrados);


                        if(isset($arrayParametrosEncontrados[0]) && count($arrayParametrosEncontrados[0]) > 0)
                        {
                            $session->getFlashBag()
                            ->add(
                                'error', 
                                "Los siguientes parámetros no fueron definidos: " . implode(',', $arrayParametrosEncontrados[0])
                            );

                            return $this->redirect(
                                $this->generateUrl('infopunto_show', 
                                array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())
                                )
                            );  
                        }

                        // FIN SE OBTIENEN CARACTERÍSTICAS DEL SERVICIO PRINCIPAL  
                        
                        
                        // INICIO: SE VERIFICA SI YA EXISTE UNA TAREA INTERNA
                        if(isset($objServProdCaractDetalleTareaSDWAN) 
                           && is_object($objServProdCaractDetalleTareaSDWAN))
                        {
                            
                            $arrayDetalleTareaInternaSDWAN = JSON_DECODE($objServProdCaractDetalleTareaSDWAN->getValor());
                            

                            if(is_object($arrayDetalleTareaInternaSDWAN) && isset($arrayDetalleTareaInternaSDWAN))
                            {
                                $strEstadoTareaInterna = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                                    ->getUltimoEstado($arrayDetalleTareaInternaSDWAN->id_detalle);

                                if($strEstadoTareaInterna === 'Asignada' || $strEstadoTareaInterna === 'Aceptada' 
                                    ||$strEstadoTareaInterna === 'Detenido' || $strEstadoTareaInterna === 'Replanificada'
                                    || $strEstadoTareaInterna === 'Pausada' || $strEstadoTareaInterna === 'Reprogramada')
                                {

                                    $arrayParametrosSeguimiento = array(
                                        'idEmpresa'             => $codEmpresa,
                                        'prefijoEmpresa'        => $strPrefijoEmpresa == 'TNP' ? 'TN' : $strPrefijoEmpresa,
                                        'idDetalle'             => $arrayDetalleTareaInternaSDWAN->id_detalle,
                                        'seguimiento'           => str_replace('<br>', ' ', $strObservacionDetalladaSDWAN),
                                        'usrCreacion'           => $usrCreacion,
                                        'ipCreacion'            => "127.0.0.1",
                                        'strEnviaDepartamento'  => "N",
                                    );   
                
                                    $serviceSoporte->ingresarSeguimientoTarea($arrayParametrosSeguimiento);   

                                    $session->getFlashBag()
                                    ->add(
                                        'error', 
                                        "Se requiere cambio de equipo para continuar con proceso de migración SDWAN 
                                        Tarea {$arrayDetalleTareaInternaSDWAN->no_tarea} existente"
                                    );
    
                                    return $this->redirect(
                                        $this->generateUrl('infopunto_show', 
                                        array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())
                                        )
                                    );  
                                }
                            }
                        }
                        // FIN: SE VERIFICA SI YA EXISTE UNA TAREA INTERNA

 


                    
                        // INICIO: SE GENERA TAREA INTERNA SI PASA LA VALIDACIÓN Y NO HA SIDO GENERADA
                        $arrayParametrosTareaNotif  = array(
                            'strIdEmpresa'               => $codEmpresa,
                            'strPrefijoEmpresa'          => $strPrefijoEmpresa == 'TNP' ? 'TN' : $strPrefijoEmpresa,
                            'strNombreTarea'             => $arrayParametroTareaCambioEquipo['encontrados'][0]['valor2'],
                            'arrayJefeResponsable'       => $arrayPersonaPYL,
                            'strObservacion'             => $strObservacionDetalladaSDWAN,
                            'strNombreDepartamento'      => $arrayParametroTareaCambioEquipo['encontrados'][0]['valor1'],
                            'strEmpleado'                => $strResponsable,
                            'strUsrCreacion'             => $usrCreacion,
                            'strIp'                      => $clientIp,
                            'strOrigen'                  => 'WEB-TN',
                            'strLogin'                   => $entityPunto->getLogin(),
                            'intPuntoId'                 => $entityPunto->getId(),
                            'strNombreCliente'           => $strCliente,
                            'strValidacionTags'          => 'NO',
                            'boolParametroNombreTecnico' => true
                        );
                        
                        $arrayTarea = $serviceSoporte->ingresarTareaInterna($arrayParametrosTareaNotif);
                        // FIN: SE GENERA TAREA INTERNA SI PASA LA VALIDACIÓN Y NO HA SIDO GENERADA


                        if($arrayTarea['status'] === 'OK')
                        {

                            $arrayParametrosSeguimiento = array(
                                'idEmpresa'             => $codEmpresa,
                                'prefijoEmpresa'        => $strPrefijoEmpresa == 'TNP' ? 'TN' : $strPrefijoEmpresa,
                                'idDetalle'             => $arrayTarea['idDetalle'],
                                'seguimiento'           => str_replace('<br>', ' ', $strObservacionDetalladaSDWAN),
                                'usrCreacion'           => $usrCreacion,
                                'ipCreacion'            => "127.0.0.1",
                                'strEnviaDepartamento'  => "N",
                            );   
        
                            $serviceSoporte->ingresarSeguimientoTarea($arrayParametrosSeguimiento);    

                            $arrayDetalleTareaInternaSDWAN = array(
                                'no_tarea'   =>  $arrayTarea['id'],
                                'id_detalle' =>  $arrayTarea['idDetalle'],
                            );
                            

                             $arrayResultado = $entityServiceTecnico->actualizarServicioProductoCaracteristica(array(
                                                                        'objServicio'       => $objServicio,
                                                                        'objProducto'       => $objServicio->getProductoId(),
                                                                        'strCaracteristica' => 'DETALLE_TAREA_INTERNA_SDWAN',
                                                                        'strValor'          => JSON_ENCODE($arrayDetalleTareaInternaSDWAN),
                                                                        'strUsrCreacion'    => $usrCreacion
                                                                    ));
                            if(!$arrayResultado)
                            {
                                $session->getFlashBag()
                                ->add(
                                    'error', 
                                    "No se pudo registrar detalle de tarea interna. Comuníquese con Dpto. de Sistemas"
                                );
    
                                return $this->redirect(
                                    $this->generateUrl('infopunto_show', 
                                    array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())
                                    )
                                );
                            }

                            $serviceInfoServicio = $this->get('comercial.InfoServicioHistorial');
  

                            
                     
                            $objServicioHistorial = $serviceInfoServicio->crearHistorialServicio(array(
                                'objServicio'    => $objServicio,
                                'strIpClient'    => $clientIp,
                                'strUsrCreacion' => $usrCreacion,
                                'strObservacion' => $strObservacionDetalladaSDWAN .
                                 "<br><br>Tarea <strong>{$arrayDetalleTareaInternaSDWAN['no_tarea']}</strong> generada",
                                'strAccion'      => 'CambioEquipo'           
                            ));

                            $serviceEmcom->persist($objServicioHistorial);
                            $serviceEmcom->flush();    


                            $session->getFlashBag()
                            ->add(
                                'error', 
                                "Se requiere cambio de equipo para continuar con proceso de migración SDWAN  
                                Tarea {$arrayDetalleTareaInternaSDWAN['no_tarea']} generada"
                            );
                        }
                        else
                        {
                            $session->getFlashBag()
                            ->add(
                                'error', 
                                    "Error al intentar generar tarea por cambio de equipo. Póngase en contacto con Dpto. de Sistemas"
                            );
                            return $this->redirect(
                                $this->generateUrl('infopunto_show', 
                                array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())
                                )
                            );

                        }

                    }     
                    return $this->redirect($this->generateUrl('infopunto_show', array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())));

                }
                else
                {
                    $session->getFlashBag()
                    ->add('error', 
                        "No se encontró parámetro para tarea de cambio de equipo. 
                        Póngase en contacto con Dpto. de Sistemas"
                        );
                        
                    return $this->redirect($this->generateUrl('infopunto_show', array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())));
                }
            }
        }
        

        //FIN  validación si es migracion SDWAN 


        
        try
        {
            /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
            $serviceInfoServicio = $this->get('comercial.InfoServicio');
            $arrayParamsServicio = array(   "codEmpresa"            => $codEmpresa,
                                            "idOficina"             => $idOficina,
                                            "entityPunto"           => $entityPunto,
                                            "entityRol"             => $entityRol,
                                            "usrCreacion"           => $usrCreacion,
                                            "clientIp"              => $clientIp,
                                            "tipoOrden"             => $tipoOrden,
                                            "ultimaMillaId"         => null,
                                            "servicios"             => $servicios,
                                            "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                            "session"               => $session,
                                            "intIdSolFlujoPP"       => $request->get('idSolFlujoPrePlanificacion') 
                                                                       ? $request->get('idSolFlujoPrePlanificacion') : 0,
                                            "arrayArchivos"         => $arrayArchivos
                                    );
            
            $emSoporte                = $this->get('doctrine')->getManager('telconet_soporte');
            $emGeneral                = $this->get('doctrine')->getManager('telconet_general');

            $objParametroDetValProd =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne("VALIDA_PRODUCTO_PAQUETE_HORAS_SOPORTE", //nombre parametro cab
                            "SOPORTE", "", 
                            "VALORES QUE AYUDAN A IDENTIFICAR QUE PRODUCTO ES PARA LA MUESTRA DE OPCIONES EN LA VISTA", //descripcion det
                            "", "", "", "", "", $codEmpresa
                        );

                $strValorProductoPaqHoras    = $objParametroDetValProd['valor1'];
                $strValorProductoPaqHorasRec = $objParametroDetValProd['valor2'];

            if ( ($objProducto->getDescripcionProducto() == $strValorProductoPaqHorasRec) )            
            {
                if(($strTipoPaquete)&&($strUuidPaquete))
                {
                    $arrayParamsServicioPaquete     = array( "strTipoPaquete"        => $strTipoPaquete,
                                                            "strUuidPaquete"         => $strUuidPaquete
                                                        );
                }
                else
                {
                    $objProductoPaquetePrincipal    = $emcom->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array("descripcionProducto" => $strValorProductoPaqHoras));
                    if ($objProductoPaquetePrincipal)
                    {
                        $intIdProductoPaquetePrincipal  = $objProductoPaquetePrincipal->getId();
                        $objPrimerServicio              = $emcom->getRepository('schemaBundle:InfoServicio')
                                                                ->findOneBy(array("puntoId"    => $entityPunto->getId(),
                                                                                "productoId"   => $intIdProductoPaquetePrincipal
                                                                        ), array("feCreacion"  => 'ASC'));
                        $intPrimerServicioId            = $objPrimerServicio->getId();        
                        $arrayObtenerUuid               = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
                                                                ->obtenerUuidPaquete($intPrimerServicioId);
                        if ($arrayObtenerUuid) 
                        {
                            $strUuidPaquete             = $arrayObtenerUuid[0]['strUuidPaquete'];
                            $strTipoPaquete             = 'recarga';

                            $arrayParamsServicioPaquete = array( "strTipoPaquete"        => $strTipoPaquete,
                                                                "strUuidPaquete"         => $strUuidPaquete
                                                            );
                        }
                    }
                }

                $arrayParamsServicio = array_merge($arrayParamsServicio, $arrayParamsServicioPaquete);
            }
            $serviceInfoServicio->crearServicio($arrayParamsServicio);


            $session->getFlashBag()->add('success', 'Servicios agregados correctamente');
        }
        catch (\Exception $e)
        {
            $session->getFlashBag()->add('error', $e->getMessage());
        }
        return $this->redirect($this->generateUrl('infopunto_show', array('id' => $idPto, 'rol'=> $entityRol->getDescripcionRol())));
    }


    /**
     * Funcion utilizada para validar si se requiere cambio de equipo en servicios que migran a SDWAN
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.1 10-01-2023
     *
     * 
     * @param integer $intIdPunto
     * @param array $arrayServicios
     * 
     * @return array
     **/
    public function validarCambioEquipoSDWAN($intIdPunto, $arrayServicios)
    {
        $objEm       = $this->getDoctrine()->getManager();
        $emGeneral   = $this->get('doctrine')->getManager('telconet_general');

        $arrayIdsEquipos             = false;
        $booleanEsMigracion          = false;
        $booleanEsSdwan              = false;
        $arrayCaracteristicasAdic    = false;


        try
        {
            if (!(is_array($arrayServicios) && count($arrayServicios) > 0)) 
            {
                return array(
                    'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                    'esMigracion' => $booleanEsMigracion,
                    'requiereCambioEquipo' => false,
                    'esSdwan' => $booleanEsSdwan,
                    'arrayIdsEquipos' => $arrayIdsEquipos,
                    'error' => false,
                    'idAdmiProductoServicioMigrado' => null,
                    'msg' => null            
                );
            }
                
            foreach ($arrayServicios as $arrayServicio) 
            {
                $objProducto = $objEm->getRepository('schemaBundle:AdmiProducto')
                    ->find($arrayServicio['codigo']);
    
    
    
                //SE VALIDA SI ES SDWAN
                if (is_object($objProducto) &&  ($objProducto->getNombreTecnico() === 'INTERNET SDWAN'
                    || $objProducto->getNombreTecnico() === 'L3MPLS SDWAN')) 
                    {
                        $booleanEsSdwan =  true;
    
    
                        $arrayCaracteristicasProducto = json_decode($arrayServicio['caracteristicasProducto'], true);
    
                        //SE VALIDAN CARACTERISTICAS DEL SERVICIO
                        if (!(is_array($arrayCaracteristicasProducto) && count($arrayCaracteristicasProducto) > 0))
                        {
                            return array(
                                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                'esMigracion' => $booleanEsMigracion,
                                'esSdwan' => $booleanEsSdwan,
                                'arrayIdsEquipos' => $arrayIdsEquipos,
                                'requiereCambioEquipo' => false,
                                'error' => true,
                                'idAdmiProductoServicioMigrado' => null,
                                'msg' => 'Error al intentar obtener características del producto',
                            );
                        }
                        $booleanEsMigracion = count(array_filter($arrayCaracteristicasProducto,  function($objCaracteristica)
                        {
                            return $objCaracteristica['caracteristica'] === '[Migración de Tecnología SDWAN]'
                            && $objCaracteristica['valor'] === 'S';
                        }))>0;
    
    
    
                        //SE VALIDA SI ES UNA MIGRACION
                        if(!$booleanEsMigracion)
                        {
                            return array(
                                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                'esMigracion' => $booleanEsMigracion,
                                'esSdwan' => $booleanEsSdwan,
                                'requiereCambioEquipo' => false,
                                'idAdmiProductoServicioMigrado' => null,
                                'arrayIdsEquipos' => $arrayIdsEquipos,
                                'error' => false,
                                'msg' => '',
                            );
                        }
    
    
                        $arrayCaracteristicasMigracionServicio = $objEm->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array(
                            'descripcionCaracteristica' => 'SERVICIO_MIGRADO_SDWAN',
                            'estado'                    => 'Activo'
                        ));
    
                        //SE VALIDA QUE EXISTA LA CARACTERISTICA
                        if(!is_object($arrayCaracteristicasMigracionServicio))
                        {
                            return array(
                                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                'esMigracion' => $booleanEsMigracion,
                                'requiereCambioEquipo' => false,
                                'esSdwan' => $booleanEsSdwan,
                                'arrayIdsEquipos' => $arrayIdsEquipos,
                                'error' => true,
                                'idAdmiProductoServicioMigrado' => null,
                                'msg' => 'No se encontró característica [SERVICIO_MIGRADO_SDWAN]',
                            );
                        }
    
                    
    
                        $arrayAdmiProdCaract= $objEm->getRepository('schemaBundle:AdmiProductoCaracteristica')
                        ->findOneBy(array('caracteristicaId'=> $arrayCaracteristicasMigracionServicio->getId(),
                                          'productoId'      => $objProducto->getId(),
                                          'estado'          => 'Activo'
                                    ));
    
                        //SE VALIDA QUE EXISTA EL PRODUCTO SDWAN POSEA LA CARACTERISTICA
                        if(!is_object($arrayAdmiProdCaract))
                        {
                            return array(
                                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                'esMigracion' => $booleanEsMigracion,
                                'esSdwan' => $booleanEsSdwan,
                                'requiereCambioEquipo' => false,
                                'arrayIdsEquipos' => $arrayIdsEquipos,
                                'error' => true,
                                'idAdmiProductoServicioMigrado' => null,
                                'msg' => 'La característica no se encuentra asociada al producto SDWAN',
                            );
                        }


                        //SE OBTIENE PARÁNETRO PARA OBTENER ESTADO(S) DE SERVICIO A BUSCAR
                        $arrayParametroTareaCambioEquipo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getArrayDetalleParametros(array(
                            'strNombreParametroCab' => 'TAREA_CAMBIO_EQUIPO_MIGRACION_SDWAN',
                            'estado'=>'Activo'
                        ));


                        // SE VALIDA QUE EXISTA EL PARÁMETRO CON LA DATA DE LA TAREA DE CAMBIO DE EQUIPO
                        if(
                            !(is_array($arrayParametroTareaCambioEquipo) 
                            && count($arrayParametroTareaCambioEquipo)>0
                            && isset($arrayParametroTareaCambioEquipo['encontrados'])
                            && isset($arrayParametroTareaCambioEquipo['encontrados'][0]['valor2']))
                        )
                        {
                            return array(
                                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                'esMigracion' => $booleanEsMigracion,
                                'esSdwan' => $booleanEsSdwan,
                                'requiereCambioEquipo' => false,
                                'arrayIdsEquipos' => $arrayIdsEquipos,
                                'error' => true,
                                'idAdmiProductoServicioMigrado' => null,
                                'msg' => 'Parámetro TAREA_CAMBIO_EQUIPO_MIGRACION_SDWAN incorrecto. 
                                Póngase en contacto con Dpto. de Sistemas',
                            );
                        }

    
    
                        //SE OBTIENE LISTADO DE SERVICIOS DISPONIBLES DEL PUNTO
                        $arrayServiciosPunto = $objEm->getRepository('schemaBundle:InfoServicio')
                        ->findBy(array(
                            'puntoId' => $intIdPunto,
                            'estado' => explode(',', $arrayParametroTareaCambioEquipo['encontrados'][0]['valor6'])
                        ));
    
    
    
                        if($objProducto->getNombreTecnico() === 'INTERNET SDWAN')
                        {
                            //SE OBTIENE UN SOLO SERVICIO DE LOS DISPONIBLES CUYO ID NO ESTÉ ASOCIADO A OTRA MIGRACIÓN
                            $arrayServiciosPunto = (array_filter($arrayServiciosPunto, function($arrayServicioPunto) 
                                use($objEm, $arrayAdmiProdCaract)
                                {            
                                    $arrayInfoServProdCaract = $objEm->getRepository('schemaBundle:InfoServicioProdCaract')
                                    ->findOneBy(array('valor'                    =>$arrayServicioPunto->getId(),
                                                      'estado'                   => 'Activo',
                                                      'productoCaracterisiticaId'=>$arrayAdmiProdCaract->getId()));
        
                                    return ((strtoupper($arrayServicioPunto->getProductoId()->getDescripcionProducto()) === 'INTERNET DEDICADO'
                                                        && $arrayServicioPunto->getProductoId()->getNombreTecnico() === 'INTERNET')
                                                        || $arrayServicioPunto->getProductoId()->getNombreTecnico() === 'INTMPLS')
                                                        && !$arrayInfoServProdCaract; 
                              
                                }
                            ));
                        }
                        else if($objProducto->getNombreTecnico() === 'L3MPLS SDWAN') 
                        {
                            //SE OBTIENE UN SOLO SERVICIO DE LOS DISPONIBLES CUYO ID NO ESTÉ ASOCIADO A OTRA MIGRACIÓN
                            $arrayServiciosPunto = (array_filter($arrayServiciosPunto, function($arrayServicioPunto) 
                                use($objEm, $arrayAdmiProdCaract)
                                {            
                                    $arrayInfoServProdCaract = $objEm->getRepository('schemaBundle:InfoServicioProdCaract')
                                    ->findOneBy(array('valor'                    =>$arrayServicioPunto->getId(),
                                                        'estado'                   => 'Activo',
                                                        'productoCaracterisiticaId'=>$arrayAdmiProdCaract->getId()));
        
                                    return strtoupper($arrayServicioPunto->getProductoId()->getNombreTecnico()) === 'L3MPLS'
                                                        && strtoupper($arrayServicioPunto->getProductoId()->getDescripcionProducto())  === 'L3MPLS'
                                                        && !$arrayInfoServProdCaract; 
                                
                                }
                            ));
                        }
          
    
                        //SE VALIDA QUE EXISTA UN SERVICIO PRINCIPAL DISPONIBLE PARA VERIFICAR EQUIPO
                        if (!(is_array($arrayServiciosPunto) && count($arrayServiciosPunto) > 0))
                        {
                            return array(
                                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                'esMigracion' => $booleanEsMigracion,
                                'esSdwan' => $booleanEsSdwan,
                                'requiereCambioEquipo' => false,
                                'arrayIdsEquipos' => $arrayIdsEquipos,
                                'idAdmiProductoServicioMigrado' => null,
                                'error' => true,
                                'msg' => 'No existe servicio principal disponible para iniciar proceso de migración',
                            );
                        }
    
                        // SE OBTIENEN PARÁMETROS PARA VALIDACIÓN DE EQUIPO SDWAN VALIDAR******
                        $arrayEquiposSDWAN = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getArrayDetalleParametros(array(
                            'strNombreParametroCab' => 'FACTIBILIDAD_EQUIPOS_MIGRACION_SDWAN',
                            'estado'=>'Activo'
                         ));
    
    
                        foreach($arrayServiciosPunto as $arrayServicioPunto)
                        {
                            $arrayParametros = array();
                            $arrayParametros['idServicio']        = $arrayServicioPunto->getId();
                            $arrayParametros['idPunto']           = $intIdPunto;
                            $arrayParametros['tipo']              = 'CPE';
                            $arrayParametros['estadoServicio']    = 'Activo';
                            $arrayParametros['fueSolicitada']     = 'N';
                            $arrayParametros['emInfraestructura'] = $this->get('doctrine')->getManager('telconet_infraestructura');
                            $arrayParametros['serviceTecnico']    = $this->get('tecnico.InfoServicioTecnico'); 
                            $arrayParametros['prefijoEmpresa']    = 'TN';
                            $arrayParametros['strTieneSmartWifi'] = 'NO';
            
                            $arrayResponse = $this->getDoctrine()
                                                  ->getManager("telconet")
                                                  ->getRepository('schemaBundle:InfoServicio')
                                                  ->generarElementosPorServicio($arrayParametros);
    
                            
    
                            $booleanCumpleCantidadUsuarios =  false;
                            $booleanCumpleCapacidadKbps    =  false;
                            $strNombreModeloElemento       = '';
    
                            foreach($arrayResponse as $arrayEquipoCPE)
                            {
                                if($arrayEquipoCPE['nombreTipoElemento'] === 'CPE')
                                {
                                    $strNombreModeloElemento = $arrayEquipoCPE['nombreModeloElemento'];

                                    foreach($arrayEquiposSDWAN['encontrados'] as $arrayParametrosDet)
                                    {
                                        //SE VALIDA CANTIDAD DE USUARIOS
                                        $booleanCumpleCantidadUsuarios = (count(array_filter($arrayCaracteristicasProducto, 
                                        function($arrayCaracteristica)
                                        use($arrayParametrosDet, $arrayEquipoCPE)
                                        {
                                            return $arrayEquipoCPE['nombreModeloElemento'] === $arrayParametrosDet['valor1'] 
                                            && $arrayCaracteristica['caracteristica'] === '[CANTIDAD USUARIOS SDWAN]'
                                            &&  intval($arrayCaracteristica['valor']) >= $arrayParametrosDet['valor3'] 
                                            && intval($arrayCaracteristica['valor']) <= $arrayParametrosDet['valor4'];
                                        }))>0);

                                        //SE VALIDAN MEGAS EN KKBPS
                                        $booleanCumpleCapacidadKbps = (count(array_filter($arrayCaracteristicasProducto, 
                                        function($arrayCaracteristica)
                                        use($arrayParametrosDet, $arrayEquipoCPE)
                                        {
                                            return $arrayEquipoCPE['nombreModeloElemento'] === $arrayParametrosDet['valor1']  
                                            && $arrayCaracteristica['caracteristica'] === '[CAPACIDAD1]'
                                            && intval($arrayCaracteristica['valor']) <= $arrayParametrosDet['valor2'];
                                        }))>0);


                                        if($booleanCumpleCantidadUsuarios && $booleanCumpleCapacidadKbps)
                                        {
                                            break 2;
                                        }
                                        else
                                        {
                                            $booleanCumpleCantidadUsuarios =  false;
                                            $booleanCumpleCapacidadKbps =  false;
                                        }
                                    }
                                }
                            }
    
                            if($booleanCumpleCantidadUsuarios && $booleanCumpleCapacidadKbps)
                            {
                                return array(
                                    'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                    'esMigracion' => $booleanEsMigracion,
                                    'requiereCambioEquipo' => false,
                                    'esSdwan' => $booleanEsSdwan,
                                    'arrayIdsEquipos' => array($arrayServicioPunto),
                                    'error' => false,
                                    'idAdmiProductoServicioMigrado' => $arrayAdmiProdCaract->getId(),
                                    'msg' => null
                                );
                            }
                            else
                            {

                                // SE GUARDAN CARACTERISTICAS ADICIONALES DEL SERVICIO SDWAN PARA HISTORIAL
                                $arrayCaracteristicasAdic = JSON_DECODE($arrayServicio['caracteristicasProducto']);
                                array_push($arrayCaracteristicasAdic, (object)array(
                                    'caracteristica' => 'NombreModeloElemento',
                                    'valor'          => $strNombreModeloElemento
                                ));

                                return array(
                                    'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                                    'esMigracion' => $booleanEsMigracion,
                                    'requiereCambioEquipo' => true,
                                    'esSdwan' => $booleanEsSdwan,
                                    'arrayIdsEquipos' => array($arrayServicioPunto),
                                    'error' => false,
                                    'idAdmiProductoServicioMigrado' => null,
                                    'msg' => null
                                );                           
                            }
                        }
    
                    }
            }
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            
            return array(
                'arrayCaracteristicasAdic' =>  $arrayCaracteristicasAdic,
                'esMigracion' => false,
                'esSdwan' => false,
                'arrayIdsEquipos' => $arrayIdsEquipos,
                'requiereCambioEquipo' => false,
                'idAdmiProductoServicioMigrado' => null,
                'error' => true,
                'msg' => $e->getMessage()
            );
        }
    }

    
    /**
     * Displays a form to edit an existing InfoOrdenTrabajo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
        }

		//estado para los detalles es Anulado
        $estado="Inactivo";
        $items=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoIdYEstado($id,$estado);
        
        if($items)
        {
            $i=0;
            foreach($items as $item)
            {
                $descripcion="";
                $id_info_plan_prod="";
                $info="";
                
                if($item->getProductoId()!="")
                {
                    $info_plan_prod=$em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId()->getId());
                    $descripcion=$info_plan_prod->getDescripcionProducto();
                    $id_info_plan_prod=$info_plan_prod->getId();
                    $info="C";
                }
                
                if($item->getPlanId()!="")
                {
                    $info_plan_prod=$em->getRepository('schemaBundle:InfoPlanCab')->find($item->getPlanId()->getId());
                    $descripcion=$info_plan_prod->getNombrePlan();
                    $id_info_plan_prod=$info_plan_prod->getId();
                    $info="P";
                }
                
                $info_servicio[$i]['producto']=$descripcion;
                $info_servicio[$i]['cantidad']=$item->getCantidad();
                $info_servicio[$i]['precio_total']=$item->getPrecioVenta();
                $info_servicio[$i]['producto_id']=$id_info_plan_prod;
                $i++;
                
                
                $info_detalle[] = array('producto' =>$id_info_plan_prod,
                                        'cantidad' => $item->getCantidad(),
                                        'precio_total' => $item->getPrecioVenta(),
                                        'id_det'=>$item->getId(),
                                        'info'=>$info);
                
            }
            
            //$obj_item = (object)$plandet;
        }
        
        if(isset($info_detalle))
            $arreglo_encode= json_encode($info_detalle);
        
        $editForm = $this->createForm(new InfoOrdenTrabajoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
            
        $parametros=array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
        
        if(isset($arreglo_encode))
            $parametros['arreglo']=$arreglo_encode;
        
        if(isset($info_servicio))
            $parametros['items_detalle']=$info_servicio;
        
        return $this->render('comercialBundle:InfoOrdenTrabajo:edit.html.twig', $parametros);
    }

    /**
     * Edits an existing InfoOrdenTrabajo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
		//informacion del pto cliente
		$session=$request->getSession();
		$user=$session->get('user');
		
        $datos=$request->get('valores');
        $valores=json_decode($datos);
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoOrdenTrabajoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('infoplancab_edit', array('id' => $id)));
            $items=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($id);

            //Verificacion de items existentes
            if($items)
            {
                $band=0;
                foreach($items as $item)
                {
                    foreach($valores as $valor)
                    {    
                        if($item->getId()==$valor->id_det && $valor->id_det!="")
                        {
                            $band=1;
                            break;
                        }
                        else
                            $band=2;
                    }
                    if($band==2)
                    {
                        $estado="Inactivo";
                        $item->setEstado($estado);
                        $em->persist($item);
                        $em->flush();
                    }
                }
            }


            foreach($valores as $valor):
                $id_producto=$valor->producto;
                $cantidad=$valor->cantidad;
                $precio=$valor->precio_total;
                $id_det=$valor->id_det;
                $info=$valor->info;
                if($id_det=="" && $info=="C")
                {
                    $prod_caract=$valor->prod_caract;
                    $valor_caract=$valor->valor_caract;
                }

                //echo $id_det;

                $entityservicio  = new InfoServicio();	
                
                if($id_det=="")
                {
                    if($info=="C")
                    {
                        $producto=$em->getRepository('schemaBundle:AdmiProducto')->findOneById($id_producto);
                        $entityservicio->setProductoId($producto);
                    }
                    else
                    {
                        $plan=$em->getRepository('schemaBundle:InfoPlanCab')->findOneById($id_producto);
                        $entityservicio->setPlanId($plan);
                    }               
                    $entityservicio->setPuntoId($entity->getPuntoId());
                    $entityservicio->setOrdenTrabajoId($entity);
                    $entityservicio->setEsVenta("S");
                    $entityservicio->setPrecioVenta($precio);
                    $entityservicio->setCantidad($cantidad);
                    $entityservicio->setEstado("Pre-servicio");	
                    $entityservicio->setUsrCreacion($user);	
                    $entityservicio->setIpCreacion($request->getClientIp());			
                    $entityservicio->setFeCreacion(new \DateTime('now'));	
                    $em->persist($entityservicio);
                    $em->flush();

                //print_r($prod_caract);

                    if($id_det=="" && $info=="C")
                    {
                        if(isset($prod_caract))
                        {
                            if(sizeof($prod_caract)>0)
                            {
                                for($i=0;$i<sizeof($prod_caract);$i++)
                                {
                                    //print_r($prod_caract[$i]);
                                    //Guardar informacion de la caracteristica del producto
                                    $entityservproductocaract  = new InfoServicioProdCaract();	
                                    $entityservproductocaract->setServicioId($entityservicio->getId());
                                    $entityservproductocaract->setProductoCaracterisiticaId($prod_caract[$i]);
                                    $entityservproductocaract->setValor($valor_caract[$i]);
                                    $entityservproductocaract->setEstado("Activo");	
                                    $entityservproductocaract->setUsrCreacion($user);	
                                    $entityservproductocaract->setFeCreacion(new \DateTime('now'));	
                                    $em->persist($entityservproductocaract);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            endforeach;
            
            return $this->redirect($this->generateUrl('infoordentrabajo_edit', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('infoordentrabajo_edit', array('id' => $id)));
        /*
        return $this->render('comercialBundle:InfoOrdenTrabajo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));*/
    }

    /**
     * Deletes a InfoOrdenTrabajo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        $estado="Inactivo";
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
            }
            $entity->setEstado($estado);
            $em->persist($entity);
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infoordentrabajo'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * getServicioProdCaractAction
     *
     * Método encargado de obtener una característica asociada a un servicio
     *
     * @return json $objRespuesta
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 01-04-2019
     */
     public function getServicioProdCaractAction()
     {
        $objRespuesta              = new JsonResponse();
        $arrayResultado            = array();
        $arrayParametrosProdCaract = array();
        $objRequest                = $this->get('request');
        $serviceUtil               = $this->get('schema.Util');
        $objSession                = $objRequest->getSession();
        $strUsrCreacion            = $objSession->get('user');
        $strClienteIp              = $objRequest->getClientIp();
        $intIdServicio             = $objRequest->get("intIdServicio");
        $intIdProducto             = $objRequest->get("intIdProducto");
        $serviceCliente            = $this->get('comercial.Cliente');
        $serviceServicio           = $this->get('comercial.InfoServicio');
        $emComercial               = $this->getDoctrine()->getManager();
        $emInfraestructura         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strBandServCaract         = "N";
        $strStatus                 = "";
        $intElementoCliBackup      = "";
        $intElementoCliePrincip    = "";

        try
        {
            if(!empty($intIdProducto) && !empty($intIdServicio))
            {
                //Se obtiene el objeto Servicio
                $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                //Se obtiene el objeto Producto
                $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);

                //***************Validar que el servicio principal asociado al backup se encuentre en otro equipo*****************//
                //Se obtiene el equipo cliente del backup
                $objInfoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intIdServicio);

                if(is_object($objInfoServicioTecnico))
                {
                    $intInterfaceElementoClienteId = $objInfoServicioTecnico->getInterfaceElementoClienteId();

                    $arrayParametrosCpe = array('interfaceElementoConectorId' => $intInterfaceElementoClienteId,
                                                'tipoElemento'                => "CPE");

                    $arrayRespuestaCpe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                           ->getElementoClienteByTipoElemento($arrayParametrosCpe);

                    if($arrayRespuestaCpe['msg'] == "FOUND")
                    {
                        $intElementoCliBackup = $arrayRespuestaCpe['idElemento'];
                    }
                    else //Si no encuentre conectado el CPE busca el ROUTER ( nodo WIFI )
                    {
                        $arrayParametrosCpe = array('interfaceElementoConectorId'   => $objInfoServicioTecnico->getInterfaceElementoClienteId(),
                                                    'tipoElemento'                  => "ROUTER");

                        $arrayRespuestaCpe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                               ->getElementoClienteByTipoElemento($arrayParametrosCpe);

                        if($arrayRespuestaCpe['msg'] == "FOUND")
                        {
                            $intElementoCliBackup  = $arrayRespuestaCpe['idElemento'];
                        }
                        else //Si no encuentra CPE va a buscar el ROUTER o CPE directo ( MIGRADOS )
                        {
                            if($objInfoServicioTecnico->getElementoClienteId())
                            {
                                $intElementoCliBackup  = $objInfoServicioTecnico->getElementoClienteId();
                            }
                        }
                    }
                }

                //Se obtiene el servicio principal asociado al backup
                $arrayParametros["objServicio"]             = $objServicio;
                $arrayParametros["strNombreCaracteristica"] = "ES_BACKUP";

                $objServicioProdCarct = $serviceServicio->getValorCaracteristicaServicio($arrayParametros);

                if(is_object($objServicioProdCarct))
                {
                    $strServicioPrincipal = $objServicioProdCarct->getValor();
                }
                else
                {
                    $arrayResultado['strStatus']         = "OK";
                    $arrayResultado['strCaracteristica'] = "C";

                    $objRespuesta->setData($arrayResultado);

                    return $objRespuesta;
                }

                //Se obtiene el equipo cliente del principal
                if(!empty($strServicioPrincipal))
                {
                    $objInfoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                          ->findOneByServicioId($strServicioPrincipal);

                    if(is_object($objInfoServicioTecnico))
                    {
                        $intInterfaceElementoClienteId = $objInfoServicioTecnico->getInterfaceElementoClienteId();

                        $arrayParametrosCpe = array('interfaceElementoConectorId' => $intInterfaceElementoClienteId,
                                                    'tipoElemento'                => "CPE");

                        $arrayRespuestaCpe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                               ->getElementoClienteByTipoElemento($arrayParametrosCpe);

                        if($arrayRespuestaCpe['msg'] == "FOUND")
                        {
                            $intElementoCliePrincip = $arrayRespuestaCpe['idElemento'];
                        }
                        else //Si no encuentre conectado el CPE busca el ROUTER ( nodo WIFI )
                        {
                            $arrayParametrosCpe = array('interfaceElementoConectorId'   => $objInfoServicioTecnico->getInterfaceElementoClienteId(),
                                                        'tipoElemento'                  => "ROUTER");

                            $arrayRespuestaCpe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                   ->getElementoClienteByTipoElemento($arrayParametrosCpe);

                            if($arrayRespuestaCpe['msg'] == "FOUND")
                            {
                                $intElementoCliePrincip  = $arrayRespuestaCpe['idElemento'];
                            }
                            else //Si no encuentra CPE va a buscar el ROUTER o CPE directo ( MIGRADOS )
                            {
                                if($objInfoServicioTecnico->getElementoClienteId())
                                {
                                    $intElementoCliePrincip  = $objInfoServicioTecnico->getElementoClienteId();
                                }
                            }
                        }
                    }
                }

                if(!empty($intElementoCliBackup) && !empty($intElementoCliePrincip))
                {
                    if(($intElementoCliBackup === $intElementoCliePrincip))
                    {
                        //Se valida que el principal se encuentre en otro equipo
                        $strBandServCaract = "E";
                    }
                }
                else
                {
                    //Se valida inconsistencias con los servicios principal o backup
                    $strBandServCaract = "T";
                }
                //***************Validar que el servicio principal asociado al backup se encuentre en otro equipo*****************//
            }

            if($strBandServCaract !== "E" && $strBandServCaract !== "T" && is_object($objProducto) && is_object($objServicio))
            {
                //*************Buscar si el servicio ya cuenta con la característica enviado*************//
                $arrayParametrosProdCaract["strCaracteristica"] = "SDWAN-CAMBIO_EQUIPO";
                $arrayParametrosProdCaract["objProducto"]       = $objProducto;
                $arrayParametrosProdCaract["objServicio"]       = $objServicio;

                $strBandServCaract = $serviceCliente->consultaServicioProdCaract($arrayParametrosProdCaract);
                //*************Buscar si el servicio ya cuenta con la característica enviado*************//
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus         = "ERROR";
            $strBandServCaract = "N";

            $serviceUtil->insertError('Telcos+',
                                      'InfoServicioController.getServicioProdCaractAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strClienteIp
                                     );
        }

        $arrayResultado['strStatus']         = $strStatus;
        $arrayResultado['strCaracteristica'] = $strBandServCaract;

        $objRespuesta->setData($arrayResultado);

        return $objRespuesta;
    }


    /**
     * Documentación para el método 'definirEsquemaSdwanCambioEquipo'.
     *
     * Método encargado de definir si una orden de servicio va utilizar el esquema SDWAN en cambio de CPE
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 04-04-2019
     */
    public function definirEsquemaSdwanCambioEquipoAction()
    {
        $objResponse    = new JsonResponse();
        $arrayRespuesta = array();
        $objRequest     = $this->get('request');
        $serviceUtil    = $this->get('schema.Util');
        $intIdServicio  = $objRequest->get('idServicio');
        $intIdProducto  = $objRequest->get('idProducto');
        $emComercial    = $this->getDoctrine()->getManager();
        $objSession     = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user');
        $strClienteIp   = $objRequest->getClientIp();

        try
        {
            $emComercial->getConnection()->beginTransaction();

            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array("descripcionCaracteristica" => 'SDWAN-CAMBIO_EQUIPO',
                                                                   "estado"                    => "Activo"));

            if(is_object($objAdmiCaracteristica))
            {
                $objAdmiProductoCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                     ->findOneBy(array("caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                       "productoId"       => $intIdProducto));
            }

            if(is_object($objAdmiProductoCaract))
            {
                //Se obtiene el objeto info servicio
                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                //se inserta el servicio producto caracteristica
                $objServicioProdCaract = new InfoServicioProdCaract();
                $objServicioProdCaract->setServicioId($intIdServicio);
                $objServicioProdCaract->setProductoCaracterisiticaId($objAdmiProductoCaract->getId());
                $objServicioProdCaract->setValor("S");
                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                $objServicioProdCaract->setUsrCreacion($strUsrCreacion);
                $objServicioProdCaract->setEstado("Activo");
                $emComercial->persist($objServicioProdCaract);
                $emComercial->flush();

                if(is_object($objInfoServicio))
                {
                    //Se registra historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objInfoServicio);
                    $objServicioHistorial->setObservacion("Se habilita el cambio de CPE con un equipo ya instalado");
                    $objServicioHistorial->setIpCreacion($strClienteIp);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                }

                $arrayRespuesta["status"]  = "OK";
                $arrayRespuesta["mensaje"] = "Registro Exitoso!";

                $emComercial->getConnection()->commit();
            }
            else
            {
                $arrayRespuesta["status"]  = "ERROR";
                $arrayRespuesta["mensaje"] = "Característica SDWAN-CAMBIO_EQUIPO no esta asociada al producto actual";
            }
        }
        catch(Exception $ex)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();

            $serviceUtil->insertError('Telcos+',
                                      'InfoServicioController.definirEsquemaSdwanCambioEquipo',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strClienteIp
                                     );

            $arrayRespuesta["status"]  = "ERROR";
            $arrayRespuesta["mensaje"] = "Ocurrió un error en la ejecución por favor notificar a Sistemas";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
     * 
     * Metodo encargado para crear Servicio Backup dado un Servicio Principal existente
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 08-12-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-06-2017 Se modifican los parámetros enviados en la función del service para crear servicio.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 21-01-2018 Se crea servicio con tipo orden cambio tipo medio.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 09-05-2019 - Se consulta la cantidad de servicios factibles que sean del mismo tipo al enviado como parámetro, este cambio se
     *                           realiza por unos ajustes implementados en la herramienta de cambio tipo medio, la cual va permitir reutilizar la
     *                           misma UM de un servicio que ya se encuentre factible
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 13-09-2019 - Se agrega la variable: 'tipoRed', la cual va permitir identificar con que tipo de red se va crear el servicio:
     *                           MPLS o GPON
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.6 10-08-2021 - Se inicializa la variable: '$strTipoOrden', la cual va permitir que se genere un servicio
     *                           Backup por cambio de tipo de medio
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.7 22-08-2021 - Se inicializa la variable: '$strTipoMedio', la cual va permitir que se genere un servicio
     *                           por cambio de tipo de medio
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxCrearServicioBackupAction()
    {
        $objRequest = $this->getRequest();
        
        $objSession = $objRequest->getSession();
        
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $strCodEmpresa     = $objSession->get('idEmpresa');
        $intIdOficina      = $objSession->get('idOficina');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $usrCreacion       = $objSession->get('user');
        $strClientIp       = $objRequest->getClientIp();
        $strTipoOrden      = $objRequest->get('tipoOrden');
        $strTipoMedio      = $objRequest->get('tipoMedio');
        $strMensaje        = ($strTipoOrden == 'C' && $strTipoMedio == 'S') ? "Se procede a realizar el cambio de tipo medio" 
                             : "Servicio Backup agregado correctamente";
        $strTipoEnlace     = $objRequest->get('tipoEnlace');
        $intIdPunto        = $objRequest->get('punto');
        $intIdProducto     = $objRequest->get('codigo');
        $floatValor        = $objRequest->get('facturable');
        $strTipoRed        = $objRequest->get('tipoRed')?$objRequest->get('tipoRed'):'MPLS';

        $arrayRespServicio = array();
        
        if ($strTipoOrden == 'C' && $strTipoMedio !== 'S')
        {
            $strTipoOrdenBackup = 'N';
        }
        else
        {
            $strTipoOrdenBackup = $strTipoOrden;
        }

        $emComercial  = $this->getDoctrine()->getManager('telconet');
        $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
        
        //Determinar si el Producto es IntMpls y es Backup se envie como codigo el referente a InternetDedicado
        if($objRequest->get('servicio') && $objRequest->get('servicio')!='')
        {
            $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($objRequest->get('codigo'));

            if(is_object($objProducto) &&
                ($objProducto->getNombreTecnico() == 'INTMPLS' || $objProducto->getNombreTecnico() == 'INTERNET SDWAN'))
            {
                $objProductoInternet = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                   ->findOneBy(array('nombreTecnico'       => 'INTERNET',
                                                                     'empresaCod'          => $strCodEmpresa,
                                                                     'descripcionProducto' => 'Internet Dedicado',
                                                                     'estado'              => 'Activo'
                                                                    ));
                if(is_object($objProductoInternet))
                {
                    $intIdProducto = $objProductoInternet->getId();
                }
            }
        }

        //Se valida si existe el objeto referente al Punto al cual se va a ligar el Servicio Backup para su creacion, si este no existe
        //no podrá ser creado el servicio nuevo
        if(is_object($objInfoPunto))
        {
            $objRol       = $emComercial->getRepository('schemaBundle:AdmiRol')
                                        ->find($objInfoPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
            $strBackup = 'S';
            try
            {
                $arrayParametros = array(
                        array(
                            'codigo'                     =>     $intIdProducto,
                            'producto'                   =>     $objRequest->get('descripcion'),
                            'cantidad'                   =>     $objRequest->get('cantidad'),
                            'frecuencia'                 =>     $objRequest->get('frecuencia'),
                            'precio'                     =>     $objRequest->get('precioUnitario'),
                            'floatPrecioFacturable'      =>     $floatValor,
                            'precio_total'               =>     $objRequest->get('precioVenta'),
                            'info'                       =>     $objRequest->get('info'),
                            'hijo'                       =>     false,
                            'servicio'                   =>     $objRequest->get('servicio'),
                            'precio_venta'               =>     $objRequest->get('precioVenta'),
                            'precio_instalacion'         =>     $objRequest->get('precioInstalacionFormula'),
                            'precio_instalacion_pactado' =>     $objRequest->get('precioInstalacionPactado'),
                            'ultimaMilla'                =>     $objRequest->get('ultimaMilla'),
                            'login_vendedor'             =>     $objRequest->get('loginVendedor'),
                            'caracteristicasProducto'    =>     $objRequest->get('caracteristicas'),
                            'idPadreFacturacion'         =>     $objRequest->get('idPadreFacturacion'),
                            'loginAux'                   =>     $objRequest->get('loginAux'),
                            'strTipoEnlace'              =>     $strTipoEnlace,
                            'intIdServicioAnt'           =>     $intIdServicioAnt,
                            'strTipoRed'                 =>     $strTipoRed,
                            'conmutadorOptico'           =>     $objRequest->get('conmutadorOptico'),
                            'modeloBackUp'               =>     $objRequest->get('modeloBackUp'),
                            'esBackup'                   =>     $strBackup
                        )
                    );
                
                /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
                $serviceInfoServicio = $this->get('comercial.InfoServicio');
                
                $arrayParamsServicio = array(   "codEmpresa"            => $strCodEmpresa,
                                                "idOficina"             => $intIdOficina,
                                                "entityPunto"           => $objInfoPunto,
                                                "entityRol"             => $objRol,
                                                "usrCreacion"           => $usrCreacion,
                                                "clientIp"              => $strClientIp,
                                                "tipoOrden"             => $strTipoOrdenBackup,
                                                "ultimaMillaId"         => null,
                                                "servicios"             => $arrayParametros,
                                                "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                "session"               => $objSession
                                       );
                
                $arrayRespServicio = $serviceInfoServicio->crearServicio($arrayParamsServicio);
                if ($arrayRespServicio['intIdServicio'] && $strTipoOrden == 'C')
                {
                    $arrayFacturarServicio = array( "intIdServicio"     => $arrayRespServicio['intIdServicio'],
                                                    "strUser"           => $usrCreacion,
                                                    "floatValor"        => $floatValor,
                                                    "strNombreMotivo"   => 'Solicitud al crear tarea por requerimientos de clientes',
                                                    "strLogin"          => $objRequest->get('loginAux'),
                                                    "strDescripcion"    => 'SOLICITUD CAMBIO TIPO MEDIO',
                                                    "strCaracteristica" => 'SOLICITUD_CAMBIO_TIPO_MEDIO'
                                                  );
                    $serviceInfoServicio->facturarServicio($arrayFacturarServicio);
                }
                $arrayRespuesta = array('status'            => 'OK',
                                        'mensaje'           => $strMensaje,
                                        'intIdServicio'     => $arrayRespServicio['intIdServicio'],
                                        'intTotalServicios' => $arrayRespServicio['intTotalServiciosPunto']);
            }
            catch (\Exception $e)
            {
                $serviceUtil = $this->get('schema.Util');
                
                $serviceUtil->insertError("Telcos+",
                                          "ajaxCrearServicioBackupAction",
                                          $e->getMessage(),
                                          $usrCreacion,
                                          $strClientIp
                                         );
               
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'Problema al crear Servicio Backup, notificar a Sistemas');
            }
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'No se encuentra referencia de Punto a crear Servicio Backup');
        }
        
        $objResponse = new JsonResponse($arrayRespuesta);
        return $objResponse;
    }
    /**
     * @deprecated Nadie usa el route 'infoservicio_tipo' correspondiente a este action,
     * definido en infoservicio.yml del comercialBundle. Deberia eliminarse este action y el routing.
     * El routing 'infoordentrabajo_tipo', definido en infoordentrabajo.yml del comercialBundle,
     * corresponde a un action similar a este en InfoOrdenTrabajoController, el cual si se esta usando.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tipoOrdenAction()
    {
        $request = $this->getRequest();
        $tipo=$request->request->get("tipo");
        //echo $tipo;
        //informacion del pto cliente
		$session=$request->getSession();
		$empresa=$session->get('idEmpresa');
		$nombre=$session->get('nombre');
		$nombre_no=$session->get('nombre_no');
		//$ptocliente=$session->get('ptoCliente');
        
        //$empresa="10";
        $estado="Activo";
        
        if($tipo=="portafolio")
        {
            $em = $this->getDoctrine()->getManager('telconet');
            $listado_planes = $em->getRepository('schemaBundle:InfoPlanCab')->findListarPlanesPorNombreNegocio($estado,$nombre,$nombre_no);

            if(!$listado_planes){
                    $arreglo=array('msg'=>'No existen datos');
            }else{
                    $formulario_portafolio="";
                    $formulario_portafolio.="<option>Seleccione</option>";
                    foreach($listado_planes as $plan){
                        $formulario_portafolio.="<option value='".$plan->getId()."-".$plan->getNombrePlan()."'>".$plan->getNombrePlan()."</option>";
                    }
                    $arreglo=array('msg'=>'ok','div'=>$formulario_portafolio,'info'=>'portafolio');
            }

            $response = new Response(json_encode($arreglo));
            $response->headers->set('Content-type', 'text/json');		
            return $response;
        }
        
        if($tipo=="catalogo")
        {
            $em = $this->getDoctrine()->getManager('telconet');
            $listado_productos = $em->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYEstado($empresa,$estado);

            if(!$listado_productos){
                    $arreglo=array('msg'=>'No existen datos');
            }else{
                    $formulario_catalogo="";
                    $formulario_catalogo.="<option>Seleccione</option>";
                    foreach($listado_productos as $producto){
                        $formulario_catalogo.="<option value='".$producto->getId()."-".$producto->getDescripcionProducto()."'>".$producto->getDescripcionProducto()."</option>";
                    }
                    $arreglo=array('msg'=>'ok','div'=>$formulario_catalogo,'info'=>'catalogo');
            }

            $response = new Response(json_encode($arreglo));
            $response->headers->set('Content-type', 'text/json');		
            return $response;
        }
    }
    
    public function informacionPlanAction()
    {
        $request = $this->getRequest();
        $plan=$request->request->get("plan");
        
        //informacion del pto cliente
		$session=$request->getSession();
		$empresa=$session->get('idEmpresa');
		
        //$empresa="10";
        $estado="Activo";
        
        $em = $this->getDoctrine()->getManager('telconet');
        $detalle_planes = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($plan,$estado);
        
        //Obtener el descuento a nivel de cabecera que es el existente
        $cabecera_plan= $em->getRepository('schemaBundle:InfoPlanCab')->find($plan);
        
        if($detalle_planes)
        {
            $acum_total=0;
            $descuento=0;
            foreach($detalle_planes as $detalle){
                $descuento=(($detalle->getPrecioItem()*$cabecera_plan->getDescuentoPlan())/100);
                //$acum_total+=($detalle->getPrecioItem()-$descuento);
                $acum_total+=($detalle->getPrecioItem());
                $descuento=0;
            }
            $arreglo=array('msg'=>'ok','precio'=>$acum_total,'descuento'=>$cabecera_plan->getDescuentoPlan(),'tipoOrden'=>'MAN');
        }
        else
             $arreglo=array('msg'=>'No existen datos');
        
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }
    
    public function gridAction()
    {
        $request = $this->getRequest();		    
        $filter = $request->request->get("filter");    
        $estado_post=$filter['filters'][0]['value'];
        //$user = $this->get('security.context')->getToken()->getUser();
        //informacion del pto cliente
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
		$idOficina=$session->get('idOficina');
		
        $estado='Activo';
        //$idEmpresa='10';
        //$idOficina=2;
        $em = $this->get('doctrine')->getManager('telconet');
        $oficina_orden=$em->getRepository('schemaBundle:InfoOficinaGrupo')->findNombrePorOficinaYEmnpresa($idOficina,$idEmpresa,$estado);
        $fechaDesde=explode('T',$request->get("fechaDesde"));
        $fechaHasta=explode('T',$request->get("fechaHasta"));
        $estado=$request->get("estado");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");

		if($estado)
			$estado=$estado;
		else
			$estado="Pre-servicio";
        

        //if ($estado=="inicio")
        if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
        {
                //Cuando sea inicio puedo sacar los 30 registros
                //$estado='Activo';
                $resultado = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find30OrdenesPorEmpresaPorEstado($idOficina,$idEmpresa,$estado,$limit, $page, $start);
                $datos = $resultado['registros'];
                $total = $resultado['total'];
        }
        else
        {
                //$datos= $em->getRepository('schemaBundle:InfoPersona')->findPreClientesPorCriterios($estado,$idEmpresa,'amontero',$fechaDesde[0],$fechaHasta[0],$nombre);
                $resultado= $em->getRepository('schemaBundle:InfoOrdenTrabajo')->findOrdenesPorCriterios($idOficina,$idEmpresa,$fechaDesde[0],$fechaHasta[0],$estado,$limit, $page, $start);
                $datos = $resultado['registros'];
                $total = $resultado['total'];
        }

        $i=1;
        foreach ($datos as $datos):
            if($i % 2==0)
                    $clase='k-alt';
            else
                    $clase='';

            $urlPlanificacion = $this->generateUrl('infoordentrabajo_planificacion_ajax', array('id' => $datos->getId()));
            $urlVer = $this->generateUrl('infoordentrabajo_show', array('id' => $datos->getId()));
            $urlEditar = $this->generateUrl('infoordentrabajo_edit', array('id' => $datos->getId()));
            $urlEliminar = $this->generateUrl('infoordentrabajo_delete_ajax', array('id' => $datos->getId()));
            $linkPlanificacion = $urlPlanificacion;
            $linkVer = $urlVer;
            $linkEditar = $urlEditar;
            $linkEliminar=$urlEliminar;

            //Obtener el tipo de orden
            if($datos->getTipoOrden()=="N")
                $tipo_orden="Nueva";
            elseif($datos->getTipoOrden()=="T")
                $tipo_orden="Traslado";
            elseif($datos->getTipoOrden()=="R")
                $tipo_orden="Reubicacion";
	    else
		$tipo_orden="No Definido";	
				
            
            
                    
            $arreglo[]= array(
				'idOrden'=>$datos->getId(),
                'Numeroorden'=>$datos->getNumeroOrdenTrabajo(),
                'Tipoorden'=> $tipo_orden,
                'Punto'=> $datos->getPuntoId()->getDescripcionPunto(),
                'Oficina'=> $oficina_orden->getNombreOficina(),
                'estado'=> $datos->getEstado(),
                'Fecreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
                'linkPlanificacion'=> $linkPlanificacion,
                'linkVer'=> $linkVer,
                'linkEditar'=> $linkEditar,
                'linkEliminar'=> $linkEliminar,
                'clase'=>$clase,
                'boton'=>""
            );              

            $i++;     
        endforeach;
        
        if (!empty($arreglo))
                $response = new Response(json_encode($arreglo));
        else
        {
                $arreglo[]= array(
						'idOrden'=>"",
                        'Numeroorden'=> "",
                        'Tipoorden'=> "",
                        'Punto'=> "",
                        'Oficina'=> "",
                        'estado'=> "",
                        'Fecreacion'=> "",
                        'linkPlanificacion'=> "",
                        'linkVer'=> "",
                        'linkEditar'=> "",
                        'clase'=>"",
                        'boton'=>"display:none;"
                );
                $response = new Response(json_encode($arreglo));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    public function estadosAction()
    {
        /*Modificacion a utilizacion de estados por modulos*/
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        //$em = $this->get('doctrine')->getManager('telconet');
        //$datos = $em->getRepository('schemaBundle:AdmiEstadoDat')->findEstadosXModulos($modulo_activo,"COM-PROSL");

        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');
        $arreglo[]= array('idEstado'=>'Pre-servicio','codigo'=> 'PRE','descripcion'=> 'Pre-servicio');

        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
		
    }
    
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            //echo $id;
            $entity=$em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
            if($entity){
                $entity->setEstado("Inactivo");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        return $respuesta;
    }
    
    public function planificacionAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $id = $peticion->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $entity=$em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
        if($entity){            
            $entityServicios =$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($id);
            $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITAR NUEVO SERVICIO");
                        
            if($entityServicios && count($entityServicios)>0)
            {
                $boolGrabo = false;
                
                foreach($entityServicios as $key => $entityServicio)
                {       
                    $entityDetalleSolicitud =$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findCountDetalleSolicitudByIds($entityServicio->getId(), $entityTipoSolicitud->getId());                    
                    if(!$entityDetalleSolicitud || $entityDetalleSolicitud["cont"]<=0)
                    {
                        $entitySolicitud  = new InfoDetalleSolicitud();
                        $entitySolicitud->setServicioId($entityServicio);
                        $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
                        $entitySolicitud->setEstado("Pendiente");	
                        $entitySolicitud->setUsrCreacion($peticion->getSession()->get('user'));		
                        $entitySolicitud->setFeCreacion(new \DateTime('now'));

                        $em->persist($entitySolicitud);
                        $em->flush();   
                        
                        $boolGrabo = true;                    
                    }
                }
                
                if(!$boolGrabo)
                {
                    $respuesta->setContent("Estos datos ya fueron ingresados."); 
                }
                else
                {
                    $respuesta->setContent("Se ingreso los detalles de solicitud");  
                }
            }
            else
            {
                $respuesta->setContent("No se ingreso los detalles de solicitud");
            }            
        }
        else
            $respuesta->setContent("No existe el registro");
            
        return $respuesta;
    }
    
    public function ptoClientesAjaxAction()
	{
		$em = $this->get('doctrine')->getManager('telconet');
		$request = $this->getRequest();
		//informacion del pto cliente
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
		
		//$idEmpresa="10";
		$limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $nombre="";
		$resultado=$em->getRepository('schemaBundle:InfoPunto')->findPtosPorEmpresaParaOrden($idEmpresa,$nombre,$limit,$page,$start);
		$datos = $resultado['registros'];
		$total = $resultado['total'];
		foreach ($datos as $datos):
			$arreglo[]= array(
                'id'=>$datos['id'],
                'login'=>$datos['login'],
                'descripcionPunto'=>$datos['descripcionPunto'],
                'razonSocial'=>$datos['razonSocial'],
                'nombres'=>$datos['nombres'],
                'apellidos'=> $datos['apellidos'],
            );              
		endforeach;
		
		if (!empty($arreglo))
                //$response = new Response(json_encode($arreglo));
                $response = new Response(json_encode(array('total' => $total, 'listado_ptos' => $arreglo)));
        else
        {
                $arreglo[]= array(
                        'id'=> "",
                        'login'=> "",
                        'descripcionPunto'=> "",
                        'razonSocial'=> "",
                        'nombres'=> "",
                        'apellidos'=> "",
                );
                //$response = new Response(json_encode($arreglo));
                $response = new Response(json_encode(array('total' => $total, 'listado_ptos' => $arreglo)));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
        /**
         * @author Luis Cabrera <lcabrera@telconet.ec>
         * @version 1.1
         * @since 29-11-2018
         * Se agrega validación: No es permitido agregar un plan de internet si ya existe una orden de servicio por aprobarse.
         *
         * @author Luis Cabrera <lcabrera@telconet.ec>
         * @version 1.2
         * @since 07-01-2019
         * Se agrega validación: No es permitido agregar un plan de internet si tiene una nota de crédito aprobada por autorizarse.
         */
	public function detallePlanAction()
    {
        $request = $this->getRequest();
        $objSession = $this->getRequest()->getSession();
        $intPuntoId = $objSession->get("ptoCliente")["id"];

        //Se valida si tiene una nota de crédito aprobada por autorizarse en el SRI.
        $intTotalNCxAutorizar   = $this->getDoctrine('telconet_financiero')
                                       ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                       ->cuentaNCInstalacionXPunto(array("intPuntoId"             => $intPuntoId,
                                                                         "strEstado"              => "Aprobada",
                                                                         "strCodigoTipoDocumento" => "NC"));
        if ($intTotalNCxAutorizar > 0)
        {
            $objResponse = new Response(json_encode(array('msg' => '<b>No es  posible </b> agregar el presente plan debido a que existe una o más '
                        . ' <b>notas de crédito por autorizarse.</b> <br/>Favor esperar unos minutos.')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
        $idPlan = $request->request->get("planId");
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $arreglo = $serviceInfoServicio->obtenerPlanInformacionDetalles($idPlan, false, true);

        $strEmpresaCod          = $objSession->get('idEmpresa');
        $boolEsInternet         = false;
        $boolPermiteAgregarPlan = true;
        foreach($arreglo as $arrayProducto)
        {
            if ("INTERNET" == $arrayProducto["nombreTecnico"])
            {
                $boolEsInternet = true;
            }
        }
        //Si es internet se valida la existencia de una factura de instalación
        if ($boolEsInternet)
        {
            $boolPermiteAgregarPlan = $serviceInfoServicio->validaOrdenesServicioPorInternetAdicional(array("intPuntoId"     => $intPuntoId,
                                                                                                            "strEmpresaCod"  => $strEmpresaCod));
        }

        if ($boolPermiteAgregarPlan)
        {
            if ($arreglo)
            {
                $response = new Response(json_encode(array('msg' => 'ok', 'listado' => $arreglo)));
            }
            else
            {
                $response = new Response(json_encode(array('msg' => 'No existen datos')));
            }
        }
        else
        {
            $response = new Response(json_encode(array('msg' => '<b>No es  posible </b> agregar el presente plan debido a que ya existe una o más '
                . ' órdenes de servicio por aprobarse. <br/>Debe anular las órdenes de servicio existentes.')));
        }
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }
    /**
     * Funcion que valida el numero de IPS maximas permitidas 
     * Consideraciones: Se valida en base al campo nombre_tecnico que define si se trata de un Plan que posee un producto IP o si se trata de un producto IP adicional          
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2014
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-04-2018 Se agrega validación para productos principales con productos IPs adicionales
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 27-11-2018 Se agregan validaciones para servicios de la empresa TNP
     * @since 1.1
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 06-02-2019 Se agrega validación para productos principales Telcohome con productos IPs adicionales para la empresa TN
     * 
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.4 06-06-2019 Se valida si el producto es IP SMB Centro Comercial y se busca la cantidad de IPs adicionales.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.5 16-07-2019 - Cantidad máxima de ip para el producto IP Small Business Razón Social.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.6 19-03-2020 - No validar las ip máximas para la activación del producto IP TELEWORKER.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 22-04-2020 Se elimina código que ya no es necesario por la reestructuración de programación para servicios Small Business 
     *                          y se invoca a la función obtenerParametrosProductosTnGpon en lugar de obtenerInfoProdPrincipalConIp
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 04-10-2020 Se valida si el punto es de tipo PYME y si se están ingresando la cantidad permitida de ips por punto.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 08-07-2022 Se obtiene el id del servicio INTERNET VPNoGPON para validar el número de ip por servicio.
     *
     * @param integer $intIdPlan     
     * @see \telconet\schemaBundle\Entity\InfoServicio
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validaIpsMaxPermitidasAction()
    {      
        $request                   = $this->getRequest();          
        $intIdPlan                 = $request->get("planId");   
        $intCantidadDetalle        = $request->get("cantidad_detalle");   
        $intCantidadTotalIngresada = $request->get("cantidad_total_ingresada");
        $strExisteIpWan            = $request->get("existe_ip_wan"); 
        $intProductoId             = $request->get("productoId");           
        $strTipo                   = $request->get("tipo");           
        $peticion                  = $this->get('request');
        $session                   = $peticion->getSession();
        $arrayPtoCliente           = $session->get('ptoCliente'); 
        $strPrefijoEmpresa         = $session->get('prefijoEmpresa');
        $strCodEmpresa             = $session->get('idEmpresa');
             
        $intNumIpsMaxPermitidas    = 0;
        $intNumIpsUtilizadas       = 0;
        $intCantidadIpsEnPlan      = 0;
        $em                        = $this->get('doctrine')->getManager('telconet');
        $serviceUtilidades         = $this->get('administracion.Utilidades');
        if($strPrefijoEmpresa === "TN" || ($strPrefijoEmpresa === "TNP" && !empty($intProductoId)))
        {
            $arrayParams                    = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                    "strCodEmpresa"                 => $strCodEmpresa,
                                                    "intIdProductoIp"               => $intProductoId);
            $arrayInfoProdsPrincipalConIp   = $em->getRepository('schemaBundle:InfoServicio')->obtenerParametrosProductosTnGpon($arrayParams);
            if(isset($arrayInfoProdsPrincipalConIp) && !empty($arrayInfoProdsPrincipalConIp))
            {
                $arrayInfoProdPrincipalConIp    = $arrayInfoProdsPrincipalConIp[0];
                $intIdProductoPrincipal         = $arrayInfoProdPrincipalConIp["intIdProdInternet"];
                $strDescripcionProdPrincipal    = $arrayInfoProdPrincipalConIp["strDescripcionProdInternet"];
                $intIdProductoIp                = $arrayInfoProdPrincipalConIp["intIdProdIp"];
                $strDescripcionProdIp           = $arrayInfoProdPrincipalConIp["strDescripcionProdIp"];
                $strNombreTecnicoProdPrincipal  = $arrayInfoProdPrincipalConIp["strNombreTecnicoProdIp"];
                $intNumIpsMaxPermitidas         = $arrayInfoProdPrincipalConIp["intNumIpsMaxPermitidas"];
                $strCaractRelProdPrincipal      = $arrayInfoProdPrincipalConIp["strCaractRelProdIp"];
                $arrayParams["intIdPunto"]                      = $arrayPtoCliente['id'];
                $arrayParams["intIdProductoPrincipal"]          = $intIdProductoPrincipal;
                $arrayParams["strDescripcionProdPrincipal"]     = $strDescripcionProdPrincipal;
                $arrayParams["intIdProductoIp"]                 = $intIdProductoIp;
                $arrayParams["strDescripcionProdIp"]            = $strDescripcionProdIp;
                $arrayParams["strNombreTecnicoProdPrincipal"]   = $strNombreTecnicoProdPrincipal;
                $arrayParams["strCaractRelProdPrincipal"]       = $strCaractRelProdPrincipal;
                $arrayValidarIpMaxPermitidas  = $em->getRepository('schemaBundle:InfoServicio')->validarIpsMaxPermitidasProducto($arrayParams);
                if($arrayValidarIpMaxPermitidas["strStatus"] == "OK" && isset($strCaractRelProdPrincipal) && !empty($strCaractRelProdPrincipal))
                {
                    $arrayParams["intIdServicio"] = $arrayValidarIpMaxPermitidas["arrayServicioValidarIpsMax"]["intIdServicio"];
                }
                $intNumIpsUtilizadas          = $em->getRepository('schemaBundle:InfoServicio')->obtenerIpsUtilizadasProdPrincipal($arrayParams);
                if($arrayValidarIpMaxPermitidas["strStatus"] === "OK")
                {
                    $arrayServicioValidarIpsMax     = $arrayValidarIpMaxPermitidas["arrayServicioValidarIpsMax"];
                    if(isset($arrayServicioValidarIpsMax) && !empty($arrayServicioValidarIpsMax))
                    {
                        if($intNumIpsMaxPermitidas > 0)
                        {
                            $intCantidadTotalIngresada  = $intCantidadTotalIngresada + $intCantidadDetalle;
                            if(($intNumIpsUtilizadas + $intCantidadTotalIngresada) > $intNumIpsMaxPermitidas)
                            {
                                $arrayResponseIps   = array('msg'                       => 'ok',
                                                            'num_ips_max_permitidas'    => $intNumIpsMaxPermitidas, 
                                                            'num_ips_utilizadas'        => $intNumIpsUtilizadas,
                                                            'mensaje_validaciones'      => 'No se permite el ingreso de ['
                                                                                            .$intCantidadTotalIngresada
                                                                                            .'] IPS adicional(es):  IPS Utilizadas['
                                                                                            .$intNumIpsUtilizadas
                                                                                            .'] IPS Max Permitidas['.$intNumIpsMaxPermitidas
                                                                                            .'] para el punto cliente',
                                                            'prod_ip'                   => $intIdProductoIp);
                            }
                            else
                            {
                                $arrayResponseIps   = array('msg'                       => '',
                                                            'num_ips_max_permitidas'    => $intNumIpsMaxPermitidas, 
                                                            'num_ips_utilizadas'        => $intNumIpsUtilizadas,
                                                            'mensaje_validaciones'      => '',
                                                            'prod_ip'                   => $intIdProductoIp );
                            }
                        }
                        else
                        {
                            $arrayResponseIps   = array('msg'                       => 'ok',
                                                        'num_ips_max_permitidas'    => $intNumIpsMaxPermitidas, 
                                                        'num_ips_utilizadas'        => $intNumIpsUtilizadas,
                                                        'mensaje_validaciones'      => "No se ha definido el número máximos de IPs",
                                                        'prod_ip'                   => $intIdProductoIp);
                        }
                    }
                    else
                    {
                        $arrayResponseIps = array('msg' => 'NoIP');
                    }
                }
                else
                {
                    $arrayResponseIps   = array('msg'                       => 'ok',
                                                'num_ips_max_permitidas'    => $intNumIpsMaxPermitidas, 
                                                'num_ips_utilizadas'        => $intNumIpsUtilizadas,
                                                'mensaje_validaciones'      => $arrayValidarIpMaxPermitidas["strMensaje"],
                                                'prod_ip'                   => $intIdProductoIp);
                }
            }
            else
            {
                $arrayResponseIps = array('msg' => 'NoIP');
            }
            $response = new Response(json_encode($arrayResponseIps));
        }
        else
        {
            $objProductoIP             = $em->getRepository('schemaBundle:InfoServicio')->obtenerProductoIp($intIdPlan,$intProductoId,$strTipo);   
            $intNumIpsUtilizadas       = $em->getRepository('schemaBundle:InfoServicio')->obtenerIpsUtilizadas($arrayPtoCliente['id']);	                
            $intNumIpsMaxPermitidas    = $em->getRepository('schemaBundle:InfoServicio')->obtenerIpsMaxPermitidas($arrayPtoCliente['id'],$intIdPlan,$strTipo);	                
            
            if($objProductoIP!=null && $objProductoIP->getId())
            {
                if($arrayPtoCliente['tipo_negocio'] === "PYME" && $strTipo === "catalogo" && $strExisteIpWan === "NO")
                {
                    $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $intProductoId,
                                                             'strDescCaracteristica' => 'IP WAN',
                                                             'strEstado'             => 'Activo' );
                    $strExisteIpWan = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
                    $strExisteIpWan = $strExisteIpWan === 'S' ? 'SI' : 'NO';
                }
                
                if($strTipo=="portafolio")
                {
                    $intCantidadIpsEnPlan      = $em->getRepository('schemaBundle:InfoServicio')->obtenerCantidadIpsEnPlan($intIdPlan);	                        
                    $intCantidadTotalIngresada = $intCantidadTotalIngresada + ($intCantidadDetalle*$intCantidadIpsEnPlan);            
                }
                else
                {
                    $intCantidadTotalIngresada = $intCantidadTotalIngresada + $intCantidadDetalle;        
                }
                if($intNumIpsMaxPermitidas>0)
                {             
                    /* Se agrega validación de productos (punto de cliente con tipo de negocio PYME) con nombre técnico IP, en caso
                       de superar la cantidad de productos IP FIJA ADICIONAL PYME permitida por punto se lanza un mensaje de error */   
                    if ($arrayPtoCliente['tipo_negocio'] === "PYME" &&
                        $strExisteIpWan === "NO" &&
                        (($intNumIpsUtilizadas + $intCantidadTotalIngresada) >= $intNumIpsMaxPermitidas))
                    {
                        $response = new Response(json_encode(array('msg'           => 'ok',
                                                                   'existe_ip_wan' => $strExisteIpWan,
                                                                   'num_ips_max_permitidas' => $intNumIpsMaxPermitidas,
                                                                   'num_ips_utilizadas'     => $intNumIpsUtilizadas,
                                                                   'mensaje_validaciones'   => 'No se permite el ingreso de ['.
                                                                                               $intCantidadTotalIngresada.
                                                                                               '] IPS adicional(es):  IPS Utilizadas['.
                                                                                               $intNumIpsUtilizadas.
                                                                                               '] IPS Adicionales Pyme Max Permitidas['.
                                                                                               ($intNumIpsMaxPermitidas - 1).
                                                                                               '] para el punto cliente',
                                                                   'prod_ip'=> $objProductoIP->getId())));
                    }
                    else if (($intNumIpsUtilizadas + $intCantidadTotalIngresada) > $intNumIpsMaxPermitidas)
                    {  
                        $response = new Response(json_encode(array('msg'           => 'ok',
                                                                   'existe_ip_wan' => $strExisteIpWan,
                                                                   'num_ips_max_permitidas' => $intNumIpsMaxPermitidas,
                                                                   'num_ips_utilizadas'     => $intNumIpsUtilizadas,
                                                                   'mensaje_validaciones'   => 'No se permite el ingreso de ['.
                                                                                               $intCantidadTotalIngresada.
                                                                                               '] IPS adicional(es):  IPS Utilizadas['.
                                                                                               $intNumIpsUtilizadas.
                                                                                               '] IPS Max Permitidas['.
                                                                                               $intNumIpsMaxPermitidas.
                                                                                               '] para el punto cliente',
                                                                    'prod_ip' => $objProductoIP->getId())));
                    }
                    else
                    {
                        $response = new Response(json_encode(array('msg'           => '',
                                                                   'existe_ip_wan' => $strExisteIpWan,
                                                                   'num_ips_max_permitidas' => $intNumIpsMaxPermitidas,
                                                                   'num_ips_utilizadas'     => $intNumIpsUtilizadas,
                                                                   'mensaje_validaciones'   =>'',
                                                                   'prod_ip'                => $objProductoIP->getId())));
                    }
                }else
                {
                      $response = new Response(json_encode(array('msg'           => 'ok',
                                                                 'existe_ip_wan' => $strExisteIpWan,
                                                                 'num_ips_max_permitidas' => $intNumIpsMaxPermitidas,
                                                                 'num_ips_utilizadas'     => $intNumIpsUtilizadas,
                                                                 'mensaje_validaciones'   => 'No se permite el ingreso de ['.
                                                                                             $intCantidadTotalIngresada.
                                                                                             '] IPS adicional(es):  IPS Utilizadas['.
                                                                                             $intNumIpsUtilizadas.
                                                                                             '] IPS Max Permitidas['.
                                                                                             $intNumIpsMaxPermitidas.
                                                                                             '] para el punto cliente',
                                                                 'prod_ip' => $objProductoIP->getId())));
                }
            }else
            {
                $response = new Response(json_encode(array('msg' => 'NoIP')));
            }
        }
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }      
              
    /**
     * Funcion que valida que exista Frecuencia definida en el Plan
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2014
     * @param integer planId     
     * @see \telconet\schemaBundle\Entity\InfoServicio
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validaFrecuenciaAction()
    {      
        $request       = $this->getRequest();          
        $intPlanId     = $request->get("planId");                           
        $em            = $this->get('doctrine')->getManager('telconet');
        
        if($intPlanId<0 || $intPlanId =="" || preg_match('/[^\d]/',$intPlanId))
        { 
           $response = new Response(json_encode(array('msg' => 'ok', 'frecuencia_plan' => '', 'mensaje_validaciones' =>'Debe escoger un Plan')));                        
        }
        else
        {
            $intFrecuencia = $em->getRepository('schemaBundle:InfoServicio')->obtenerFrecuencia($intPlanId); 
        
            if(!$intFrecuencia || $intFrecuencia<0)
            {             
                $response = new Response(json_encode(array('msg' => 'ok', 'frecuencia_plan' => $intFrecuencia, 'mensaje_validaciones' =>'No se permite el ingreso del servicio por no poseer una Frecuencia ['.$intFrecuencia.'] valida para su Facturacion')));                       
            }
            else
            {
                $response = new Response(json_encode(array('msg' => '', 'frecuencia_plan' => $intFrecuencia, 'mensaje_validaciones' =>'')));
            }
        }
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }
    
    /**
     * Función que valida que el Login posea un servicio de internet válido para poder contratar un servicio 
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 02-07-2018 
     * 
     * @author David Leon
     * @version 1.1 16-04-2019 - Se valida que los productos de Seguridad consten con un contacto de escalabilidad registrado.
     * 
     * @author David Leon
     * @version 1.2 05-02-2020 - Se valida para servicios Sdwan la cantidad de Cpe activos y el plan del servicio Ng Firewall.
     *
     * @author Antonio Ayala
     * @version 1.3 13-02-2020 - Se valida que si el producto COU LINEAS TELEFONIA FIJA SMB tiene la marca de activación simultánea el estado
     *                           del servicio tradicional debe ser Factible .
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 05-05-2020 - Se parametrizan los ids de los productos Small Business permitidos para netvoice, para evitar realizar 
     *                            las validaciones por la descripción del producto. 
     *                            Además, se eliminan dos validaciones innesarias: la variable $boolInternet se setea a true dentro de una validación
     *                            una validación y luego se realiza otra actualización de lo mismo y que además es dependiente de una variable 
     *                            que solo se actualiza en la validación anterior y la otra validación siempre se va por el else puesto que la 
     *                            variable $strDescripcion nunca es seteada a Internet Small Business
     * 
     * @author David Leon
     * @version 1.5 23-07-2020 - Se valida para servicios de Seguridad si existe un servicio de sdwan en el punto.
     *
     * @author Richard Cabrera
     * @version 1.6 11-02-2021 - Se crea el parametro: VALIDACIONES SDWAN para controlar si la validacion de mas de un servicio aplica o no
     * 
     * @author Antonio Ayala
     * @version 1.7 14-07-2021 - Se valida para producto secure cpe si existe un servicio Internet/Datos MPLS y si estos servicios tienen
     *                           cpe FORTIGATE
     *
     * @author Antonio Ayala
     * @version 1.8 03-08-2021 - Se valida si el producto Secure Cpe se va a ingresar es por migración de equipo cpe del
     *                           Security NG Firewall
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 01-08-2022 - Se agrega validación para el límite de cámaras para el producto SAFE ANALYTICS CAM.
     *
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 2.0 09-12-2022 - Se agrega la funcion que permite validar el producto SAFE ENTRY
     */
    public function validaProductoInternetAction()
    {
        $objRequest          = $this->getRequest();
        $intProductoId       = $objRequest->get("productoId");
        $strCategoria        = $objRequest->get("categorias_telefonia");
        $intNoCanales        = $objRequest->get("numero_canales");
        $intCantLineas       = $objRequest->get("cantidad_lineas");
        $emComercial         = $this->get('doctrine')->getManager('telconet');
        $emGeneral           = $this->get('doctrine')->getManager('telconet_general');
        $objPeticion         = $this->get('request');
        $objSession          = $objPeticion->getSession();
        $arrayPtoCliente     = $objSession->get('ptoCliente');
        $arrayCliente        = $objSession->get('cliente');
        $strInstalacion      = $objRequest->get("instalacion_simultanea_cou_telefonia_fija");
        $boolInternet        = false;
        $intEmpresaId        = $objSession->get('idEmpresa');
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $serviceTecnico      = $this->get('tecnico.InfoServicioTecnico');
        $strUserCreacion     = $objSession->get('user');
        $strClientIp         = $objRequest->getClientIp();
        $strEsParaMigracion  = $objRequest->get("es_para_migracion");
                        
        if ($strInstalacion == null)
        {
            $strInstalacion = 'null';
        }
       
        $objProducto    = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intProductoId);
        $boolEscalabilidadProducto    = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->findProductosEscalabilidad($intProductoId);
        if(is_object($objProducto))
        {
            //se agrega funcionalidad de TN para verificar  de igual forma que exista un servicio de internet valido
            if($objProducto->getEmpresaCod()->getPrefijo() == 'TN')
            {
                $boolParametroNombreTecnico = false;
                $arrayParamsNombreTecnico  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NOMBRE_TECNICO_PRODUCTOS',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $objProducto->getDescripcionProducto(),
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $intEmpresaId);
                if(isset($arrayParamsNombreTecnico) && !empty($arrayParamsNombreTecnico))
                {
                    $boolParametroNombreTecnico = true;
                    $strParamNombreTecnico      = $arrayParamsNombreTecnico["valor2"];
                    $strCpe                     = $arrayParamsNombreTecnico["valor3"];
                }
                
                //inicializo
                if($objProducto->getNombreTecnico() == 'TELEFONIA_NETVOICE')
                {
                    if ($strInstalacion != 'null')
                    {
                      $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array('puntoId' => $arrayPtoCliente['id'],
                                                                                                    'estado'  => 'Factible'));   
                    }
                    else
                    {
                       $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array('puntoId' => $arrayPtoCliente['id'],
                                                                                                    'estado'  => 'Activo')); 
                    }
                    
                    //validamos los distintos casos de netvoice
                    if($strCategoria == 'FIJA ANALOGA' || $strCategoria == 'FIJA SIP TRUNK')
                    {
                        $strObservacion = ' Internet Dedicado o L3MPLS o Internet MPLS ';
                        foreach($arrayServicios as $objServicio)
                        {
                            $strDescripcion = $objServicio->getProductoId()->getDescripcionProducto();
                            if($strDescripcion == 'L3MPLS' || $strDescripcion == 'Internet Dedicado' || $strDescripcion == 'Concentrador L3MPLS' ||
                               $strDescripcion == 'Internet MPLS')
                            {
                                $boolInternet  = true;
                            }
                        }
                    }
                    else if($strCategoria == 'FIJA SMB')
                    {
                        $strObservacion = ' Internet SMALL BUSINESS ';
                        foreach($arrayServicios as $objServicio)
                        {
                            $arrayParamsVerificaCouFijaSmb  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('PARAMS_PRODS_TN_GPON',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 'PRODUCTOS_VERIFICA_COU_FIJA_SMB',
                                                                                 $objServicio->getProductoId()->getId(),
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $intEmpresaId);
                            if(isset($arrayParamsVerificaCouFijaSmb) && !empty($arrayParamsVerificaCouFijaSmb))
                            {
                                $boolInternet = true;
                            }
                        }
                    }
                    
                    if($boolInternet)
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                    }
                    else
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 'No se permite el ingreso '
                                       . ' por no poseer un servicio de '.$strObservacion.' contratado en estado Activo.')));
                    }
                    
                    if($strCategoria != 'FIJA SIP TRUNK' && $intNoCanales > 2 )
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 'Para la categoría '.$strCategoria
                                        .' solo se pueden ingresar 2 canales.')));  
                    }
                    
                    if($strCategoria == 'FIJA SMB' && $intCantLineas > 2 )
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 'Para la categoría '.$strCategoria
                                        .' solo se pueden ingresar hasta 2 líneas.')));  
                    }
                    
                    if($strCategoria == 'FIJA ANALOGA' && $intCantLineas > 8 )
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 'Para la categoría '.$strCategoria
                                        .' solo se pueden ingresar hasta 8 líneas.')));  
                    }                    
                }
                //Buscamos enlaces de internet mpls activos y revisamos la cantidad de servicios en ese cpe
                else if($objProducto->getNombreTecnico() == 'INTERNET SDWAN')
                {
                    $strBanderaVariosServicios    = "S";
                    $strBanderaSecurityNgFireWall = "S";

                    //Consultar banderas para controlar si las validaciones aplican o no.
                    $arrayParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('VALIDACIONES SDWAN',
                                                          'INFRAESTRUCTURA',
                                                          'INTERNET SDWAN',
                                                          'BANDERA-VALIDAR-VARIOS-SERVICIOS',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          $intEmpresaId);

                    if(isset($arrayParametros["valor1"]) && !empty($arrayParametros["valor1"]))
                    {
                        $strBanderaVariosServicios    = $arrayParametros["valor1"];
                        $strBanderaSecurityNgFireWall = $arrayParametros["valor2"];
                    }

                    $serviceCancelarServicio = $this->get('tecnico.InfoCancelarServicio');
                    $objProductoInter    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'Internet MPLS',
                                                                                            'empresaCod'          => $intEmpresaId));
                    if(is_object($objProductoInter) && !empty($objProductoInter))
                    {
                        $objServicioInternet = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                                    array('puntoId' => $arrayPtoCliente['id'],
                                                                                                          'productoId' => $objProductoInter->getId(),
                                                                                                          'estado' => 'Activo'));
                    }
                    if(is_object($objServicioInternet) && !empty($objServicioInternet))
                    {
                        $objServTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                          ->findOneBy(array('servicioId' => $objServicioInternet->getId()));
                        
                        if(is_object($objServTecnico) && !empty($objServTecnico))
                        {
                            $objElementoCpeCambiar = $serviceCancelarServicio->getElementoCpeServicioTn($objServTecnico);
                            
                            if(is_object($objElementoCpeCambiar) && !empty($objElementoCpeCambiar) && $strBanderaVariosServicios == "S")
                            {
                                $arrayParametrosCpe = array('objServicio'      => $objServicioInternet,
                                                            'objElementoCpe'   => $objElementoCpeCambiar,
                                                            'strTipoEnlace'    => 'BACKUP');
                                $booleanCpe         = $serviceCancelarServicio->validarCpePorServicio($arrayParametrosCpe);
                                if($booleanCpe)
                                {
                                    $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                                }
                                else
                                {
                                    $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                        'Existe mas de un Servicio Conectado al Cpe, '
                                        . 'Favor comunicarse con el Gerente de Producto.')));
                                }
                            }
                            else
                            {
                                $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                            }
                        }
                        $objProductoFirewall    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'SECURITY NG FIREWALL',
                                                                                            'empresaCod'          => $intEmpresaId));
                        if(is_object($objProductoFirewall) && !empty($objProductoFirewall))
                        {
                            $objServicioNgFire = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                    ->findOneBy(array('puntoId' => $arrayPtoCliente['id'],
                                                                                                      'productoId' => $objProductoFirewall->getId(),
                                                                                                      'estado' => 'Activo'));
                        }
                        
                        if(is_object($objServicioNgFire) && !empty($objServicioNgFire))
                        {
                            $serviceServicio                            = $this->get('comercial.InfoServicio');
                            $arrayParametros["objServicio"]             = $objServicioNgFire;
                            $arrayParametros["strNombreCaracteristica"] = "SEC PLAN NG FIREWALL";
                            $objServicioProdCaractPl                    = $serviceServicio->getValorCaracteristicaServicio($arrayParametros);

                            if(is_object($objServicioProdCaractPl) && !empty($objServicioProdCaractPl) && $strBanderaSecurityNgFireWall == "S")
                            {
                                $arrayParametrosDet =   $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("LICENCIAS_SDWAN", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $objServicioProdCaractPl->getValor(), 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                                if(is_array($arrayParametrosDet) && !empty($arrayParametrosDet))
                                {
                                    $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                                }
                                else
                                {
                                    $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                        'No se permite el Ingreso de Servicio Sdwan por el producto Seguridad en sitio, '
                                        . 'Favor comunicarse con el Gerente de Producto.')));
                                }
                            }
                            else
                            {
                                $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));

                            }
                        }
                        
                        
                    }
                    else
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                    }
                }
                else if($objProducto->getNombreTecnico() == 'SECSALES' && !$boolParametroNombreTecnico)
                {
                    $objProductoInterSdwan    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'Internet SDWAN',
                                                                                            'empresaCod'          => $intEmpresaId));
                    
                    $objProductoDatoSdwan    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'L3MPLS SDWAN',
                                                                                            'empresaCod'          => $intEmpresaId));
                    if((is_object($objProductoInterSdwan) && !empty($objProductoInterSdwan)) || 
                        (is_object($objProductoDatoSdwan) && !empty($objProductoDatoSdwan)))
                    {
                        if(is_object($objProductoInterSdwan) && !empty($objProductoInterSdwan))
                        {
                            $objServicioInternetSdwan = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                            array('puntoId' => $arrayPtoCliente['id'],
                                                                                                  'productoId' => $objProductoInterSdwan->getId()));
                        }
                        
                        if(is_object($objProductoDatoSdwan) && !empty($objProductoDatoSdwan))
                        {
                            $objServicioDatosSdwan = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                            array('puntoId' => $arrayPtoCliente['id'],
                                                                                                  'productoId' => $objProductoDatoSdwan->getId()));
                        }
                        if((is_object($objServicioInternetSdwan) && !empty($objServicioInternetSdwan)) ||
                            (is_object($objServicioDatosSdwan) && !empty($objServicioDatosSdwan)))
                        {
                            $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                        }
                        else
                        {
                            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                                                'No existe producto Sdwan en el Punto.')));
                        }
                    }
                }
                else if($objProducto->getNombreTecnico() == "SAFECITYDATOS"
                        || $objProducto->getDescripcionProducto() == "SAFE ANALYTICS CAM")
                {
                    $arrayParametrosTotalCamaras["intIdProducto"]    = $objProducto->getId();
                    $arrayParametrosTotalCamaras["intIdPunto"]       = $arrayPtoCliente['id'];
                    $arrayParametrosTotalCamaras["strCodEmpresa"]    = $intEmpresaId;

                    $arrayRespuesta = $serviceInfoServicio->validarLimiteCamarasSafecityPorPunto($arrayParametrosTotalCamaras);

                    $objResponse = new Response(json_encode(array('msg'                  => $arrayRespuesta["status"],
                                                                  'mensaje_validaciones' => $arrayRespuesta["respuesta"])));
                }
                else if($objProducto->getNombreTecnico() == "SERVICIOS-CAMARA-SAFECITY")
                {
                    $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intProductoId);

                    $arrayParametrosValidarServMascarilla["intIdPunto"]    = $arrayPtoCliente['id'];
                    $arrayParametrosValidarServMascarilla["intIdProducto"] = $intProductoId;
                    $arrayParametrosValidarServMascarilla["strCodEmpresa"] = $intEmpresaId;

                    $arrayRespuesta = $serviceInfoServicio->validarCamaraActiva($arrayParametrosValidarServMascarilla);

                    $objResponse = new Response(json_encode(array('msg'                  => $arrayRespuesta["status"],
                                                                  'mensaje_validaciones' => $arrayRespuesta["respuesta"])));
                }
                else if($objProducto->getNombreTecnico() == 'SAFE ENTRY')
                {
                    $arrayParametrosValidar ['intPuntoId']  = $arrayPtoCliente['id'];
                    $arrayParametrosValidar ['objProducto'] = $objProducto;
                    $arrayParametrosValidar["intIdEmpresa"] = $intEmpresaId;

                    $arrayRespuesta = $serviceInfoServicio->validarSafeEntry($arrayParametrosValidar);

                    $objResponse = new Response(json_encode($arrayRespuesta));
                }    
                else if($boolParametroNombreTecnico && $strParamNombreTecnico == $objProducto->getNombreTecnico())
                {

                    $objProductoInterMpls           = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'Internet MPLS',
                                                                                            'empresaCod'          => $intEmpresaId));
                    
                    $objProductoDatoMpls            = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'L3MPLS',
                                                                                            'empresaCod'          => $intEmpresaId));
                    
                    $objProductoInternetDedicado    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto' => 'Internet Dedicado',
                                                                                            'nombreTecnico'       => 'INTERNET',
                                                                                            'empresaCod'          => $intEmpresaId));
                    if((is_object($objProductoInterMpls) && !empty($objProductoInterMpls)) || 
                        (is_object($objProductoDatoMpls) && !empty($objProductoDatoMpls)) ||
                        (is_object($objProductoInternetDedicado) && !empty($objProductoInternetDedicado)))
                    {
                        if(is_object($objProductoInterMpls) && !empty($objProductoInterMpls))
                        {
                            $objServicioInternetMpls = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                            array('puntoId'    => $arrayPtoCliente['id'],
                                                                                                  'productoId' => $objProductoInterMpls->getId(),
                                                                                                  'estado'     => 'Activo'));
                        }
                        
                        if(is_object($objProductoDatoMpls) && !empty($objProductoDatoMpls))
                        {
                            $objServicioDatosMpls = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                            array('puntoId'    => $arrayPtoCliente['id'],
                                                                                                  'productoId' => $objProductoDatoMpls->getId(),
                                                                                                  'estado'     => 'Activo'));
                        }
                        
                        if(is_object($objProductoInternetDedicado) && !empty($objProductoInternetDedicado))
                        {
                            $objServicioInternetDedicado = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                        array('puntoId'    => $arrayPtoCliente['id'],
                                                                                              'productoId' => $objProductoInternetDedicado->getId(),
                                                                                              'estado'     => 'Activo'));
                        }
                        if((is_object($objServicioInternetMpls) && !empty($objServicioInternetMpls)) ||
                            (is_object($objServicioDatosMpls) && !empty($objServicioDatosMpls)) ||
                            (is_object($objServicioInternetDedicado) && !empty($objServicioInternetDedicado)))
                        {
                            $arrayParametrosCpe['strCpe']          = $strCpe;
                            $arrayParametrosCpe['strUserCreacion'] = $strUserCreacion;
                            $arrayParametrosCpe['strClientIp']     = $strClientIp;
                            if(is_object($objServicioInternetMpls) && !empty($objServicioInternetMpls))
                            {
                                $objServTecnicoMpls = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->findOneBy(array('servicioId' => $objServicioInternetMpls->getId()));
                                $arrayParametrosCpe['objServicioTecnico'] = $objServTecnicoMpls;
                                $arrayRespuestaMarca  = $serviceTecnico->obtenerMarcaCpe($arrayParametrosCpe);
                                $boolResult           = $arrayRespuestaMarca['result'];
                                $strMensaje           = $arrayRespuestaMarca['mensaje'];
                                                                
                                if ($boolResult)
                                {
                                    $strNombreModelo = $arrayRespuestaMarca['strNombreModelo'];
                                    //buscamos si el modelo existe en la tabla de parámetros
                                    $arrayParamsModeloCpe  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne('NO_SOPORTA_MODELOS_CPE_FORTIGATE',
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     $strNombreModelo,
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     $intEmpresaId);
                                    if(isset($arrayParamsModeloCpe) && !empty($arrayParamsModeloCpe))
                                    {
                                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'Equipo Fortigate no soporta Security Secure Cpe.')));
                                    }
                                    else
                                    {
                                        $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                                    }
                                }
                                else
                                {
                                    //Consultamos si se va a migrar debe tener un servicio Secure NG FIREWALL
                                    if ($strEsParaMigracion == 'S')
                                    {
                                        $objProductoSecureNg    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                              'descripcionProducto' => 'SECURITY NG FIREWALL',
                                                                                              'empresaCod'          => $intEmpresaId));
                                        if(is_object($objProductoSecureNg) && !empty($objProductoSecureNg))
                                        {
                                            $objServicioSecureNg = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                              array('puntoId'    => $arrayPtoCliente['id'],
                                                                                                    'productoId' => $objProductoSecureNg->getId(),
                                                                                                    'estado'     => 'Activo'));
                                            
                                            if(is_object($objServicioSecureNg) && !empty($objServicioSecureNg))
                                            {
                                                $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));   
                                            }
                                            else
                                            {
                                                $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'Para migración debe tener un servicio Secure NG Firewall en estado Activo.')));
                                            }
                                        }
                                        else
                                        {
                                            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'No existe Producto SECURITY NG FIREWALL.')));
                                        }
                                    }
                                    else
                                    {
                                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                                $strMensaje)));
                                    }
                                }
                            }
                            elseif (is_object($objServicioInternetDedicado) && !empty($objServicioInternetDedicado))
                            {
                                $objServTecnicoMpls = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->findOneBy(array('servicioId' => $objServicioInternetDedicado->getId()));
                                $arrayParametrosCpe['objServicioTecnico'] = $objServTecnicoMpls;
                                $arrayRespuestaMarca  = $serviceTecnico->obtenerMarcaCpe($arrayParametrosCpe);
                                $boolResult           = $arrayRespuestaMarca['result'];
                                $strMensaje           = $arrayRespuestaMarca['mensaje'];
                                                                
                                if ($boolResult)
                                {
                                    $strNombreModelo = $arrayRespuestaMarca['strNombreModelo'];
                                    //buscamos si el modelo existe en la tabla de parámetros
                                    $arrayParamsModeloCpe  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne('NO_SOPORTA_MODELOS_CPE_FORTIGATE',
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     $strNombreModelo,
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     $intEmpresaId);
                                    if(isset($arrayParamsModeloCpe) && !empty($arrayParamsModeloCpe))
                                    {
                                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'Equipo Fortigate no soporta Security Secure Cpe.')));
                                    }
                                    else
                                    {
                                        $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                                    }
                                }
                                else
                                {
                                    //Consultamos si se va a migrar debe tener un servicio Secure NG FIREWALL
                                    if ($strEsParaMigracion == 'S')
                                    {
                                        $objProductoSecureNg    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                              'descripcionProducto' => 'SECURITY NG FIREWALL',
                                                                                              'empresaCod'          => $intEmpresaId));
                                        if(is_object($objProductoSecureNg) && !empty($objProductoSecureNg))
                                        {
                                            $objServicioSecureNg = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                              array('puntoId'    => $arrayPtoCliente['id'],
                                                                                                    'productoId' => $objProductoSecureNg->getId(),
                                                                                                    'estado'     => 'Activo'));
                                            
                                            if(is_object($objServicioSecureNg) && !empty($objServicioSecureNg))
                                            {
                                                $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));   
                                            }
                                            else
                                            {
                                                $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'Para migración debe tener un servicio Secure NG Firewall en estado Activo.')));
                                            }
                                        }
                                        else
                                        {
                                            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'No existe Producto SECURITY NG FIREWALL.')));
                                        }
                                    }
                                    else
                                    {
                                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                                $strMensaje)));
                                    }
                                }
                            }
                            else
                            {
                                if(is_object($objServicioDatosMpls) && !empty($objServicioDatosMpls))
                                {
                                    $objServTecnicoMpls = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                      ->findOneBy(array('servicioId' => $objServicioDatosMpls->getId()));
                                    $arrayParametrosCpe['objServicioTecnico'] = $objServTecnicoMpls;

                                    $arrayRespuestaMarca  = $serviceTecnico->obtenerMarcaCpe($arrayParametrosCpe);
                                    $boolResult           = $arrayRespuestaMarca['result'];
                                    $strMensaje           = $arrayRespuestaMarca['mensaje'];
                                    
                                    if ($boolResult)
                                    {
                                        $strNombreModelo = $arrayRespuestaMarca['strNombreModelo'];
                                        //buscamos si el modelo existe en la tabla de parámetros
                                        $arrayParamsModeloCpe  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne('NO_SOPORTA_MODELOS_CPE_FORTIGATE',
                                                                                         '',
                                                                                         '',
                                                                                         '',
                                                                                         $strNombreModelo,
                                                                                         '',
                                                                                         '',
                                                                                         '',
                                                                                         '',
                                                                                         $intEmpresaId);
                                        if(isset($arrayParamsModeloCpe) && !empty($arrayParamsModeloCpe))
                                        {
                                            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                   'Equipo Fortigate no soporta Security Secure Cpe.')));
                                        }
                                        else
                                        {
                                            $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                                        }
                                    }
                                    else
                                    {
                                        //Consultamos si se va a migrar debe tener un servicio Secure NG FIREWALL
                                        if ($strEsParaMigracion == 'S')
                                        {
                                            $objProductoSecureNg    = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                              'descripcionProducto' => 'SECURITY NG FIREWALL',
                                                                                              'empresaCod'          => $intEmpresaId));
                                            if(is_object($objProductoSecureNg) && !empty($objProductoSecureNg))
                                            {
                                                $objServicioSecureNg = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(
                                                                                              array('puntoId'    => $arrayPtoCliente['id'],
                                                                                                    'productoId' => $objProductoSecureNg->getId(),
                                                                                                    'estado'     => 'Activo'));
                                            
                                                if(is_object($objServicioSecureNg) && !empty($objServicioSecureNg))
                                                {
                                                    $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));   
                                                }
                                                else
                                                {
                                                    $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'Para migración debe tener un servicio Secure NG Firewall en estado Activo.')));
                                                }
                                            }
                                            else
                                            {
                                                $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                               'No existe Producto SECURITY NG FIREWALL.')));
                                            }
                                        }
                                        else
                                        {
                                            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                                $strMensaje)));
                                        }
                                    }
                                }
                            }
                            
                        }
                        else
                        {
                            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                          'Servicios Internet/Datos MPLS deben estar en estado Activo.')));
                        }
                    }
                    else
                    {
                        $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 
                                                                                                'No existen productos.')));
                    }
                }
                else
                {
                    $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
                }
            }
            else
            {
                $objResponse = new Response(json_encode(array('msg' => 'OK', 'mensaje_validaciones' => 'OK')));
            }
        }
        else
        {
            $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 'No existe producto.')));
        }
        
        if($boolEscalabilidadProducto)
        {
                $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findPersonaEmpresaRolCaracByPersona($arrayCliente['id_persona_empresa_rol']);
            if (empty($arrayInfoEmpresaRolCarac))
            {
                $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findPersonaEmpresaRolCaracByPunto($arrayPtoCliente['id']);
            }
            if (count($arrayInfoEmpresaRolCarac)<2 || empty($arrayInfoEmpresaRolCarac))
            {
                $objResponse = new Response(json_encode(array('msg' => 'ERROR', 'mensaje_validaciones' => 'No existe contacto escalable para el producto, '
                    . 'Favor ingresar en la ventana de contactos dicha información.')));
            }
        }

        $objResponse->headers->set('Content-type', 'text/json');		
        return $objResponse;
    }    
    
    
    public function validarCamaraActivaAction()
    {
        $objRequest             = $this->getRequest();
        $intProductoId          = $objRequest->get("productoId");

        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $emGeneral              = $this->get('doctrine')->getManager('telconet_general');
        $objPeticion            = $this->get('request');
        $objSession             = $objPeticion->getSession();
        $objPuntoCliente        = $objSession->get('ptoCliente');
        $arrayCliente           = $objSession->get('cliente');
        $boolTieneCamaraActiva  = false;
        $intEmpresaId           = $objSession->get('idEmpresa');
        $serviceInfoServicio    = $this->get('comercial.InfoServicio');

        $objProducto    = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intProductoId);

        $arrayParametros["objPunto"]      = $objPuntoCliente;
        $arrayParametros["intIdProducto"] = $intProductoId;
        $arrayParametros["strCodEmpresa"] = $intEmpresaId;

        $arrayRespuesta = $serviceInfoServicio->validarCamaraActiva($arrayParametros);

        $objResponse = new Response(json_encode(array('status'    => $arrayRespuesta["status"],
                                                      'respuesta' => $arrayRespuesta["respuesta"])));

        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }


     /**
     * Funcion que valida que el Login posea un servicio de internet valido para poder contratar NetlifeCam 
     * Se excluyen los servicios en estado Rechazado, Rechazada, Cancelado, Anulado, Cancel, Eliminado, Reubicado, Trasladado, Incorte, InTemp   
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 16-06-2014
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.1 19-10-2022 - Se agrega parametrizacion de  los productos NetlifeCam.
     * 
     * @param integer productoId     
     * @see \telconet\schemaBundle\Entity\InfoServicio
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function validaProductoNetlifecamAction()
    {      
        $request                = $this->getRequest();          
        $intProductoId          = $request->get("productoId");                           
        $em                     = $this->get('doctrine')->getManager('telconet');	                
        $peticion               = $this->get('request');
        $session                = $peticion->getSession();
        $arrayPtoCliente        = $session->get('ptoCliente');    
        $boolProductoNetlifeCam = false;
        $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $arrayParamProducNetCam   = $serviceServicioTecnico->paramProductosNetlifeCam();

        $objProductoNetlifeCam = $em->getRepository('schemaBundle:AdmiProducto')->find($intProductoId);
        if($objProductoNetlifeCam && in_array($objProductoNetlifeCam->getNombreTecnico(),$arrayParamProducNetCam))
        {
            $boolProductoNetlifeCam = true;   
        }
        if( $boolProductoNetlifeCam )
        {
            $objInternet    = $em->getRepository('schemaBundle:InfoServicio')->obtieneProductoInternetxPunto($arrayPtoCliente['id']); 
            if($objInternet!=null && $objInternet->getId())
            {  
                $response = new Response(json_encode(array('msg' => '', 'mensaje_validaciones' =>'')));
            }
            else
            {
                $response = new Response(json_encode(array('msg' => 'ok', 'mensaje_validaciones' =>'No se permite el ingreso de netlifeCam por no poseer un servicio de Internet Contratado en estado Valido ')));                                       
            }
        }
        else
        {
             $response = new Response(json_encode(array('msg' => '', 'mensaje_validaciones' =>'')));
        }
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_322-3337")
     *
     * Documentación para el método 'ajaxGetInfoEnlaceDatos'.
     *
     * Método utilizado para obtener la información del enlace de datos de un servicio
     *
     * @param string login login a buscar en el listado de puntos disponibles para enlazar
     
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 11-04-2016
    */
    public function ajaxGetInfoEnlaceDatosAction()
    {
        $response        = new JsonResponse();
        $request         = $this->get('request');
        $session         = $this->get('session');
        $emComercial     = $this->getDoctrine()->getManager();
        
        $idServicio      = $request->get('idServicio');
        $servicioTecnicoService = $this->get('tecnico.InfoServicioTecnico');
    
        $objJsonOrigen   = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                       ->getInfoEnlaceDatos($idServicio,$servicioTecnicoService,'ORIGEN');
        
        $objJsonDestino  = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                       ->getInfoEnlaceDatos($idServicio,$servicioTecnicoService,'DESTINO');
        
        $objJson['origen']  = $objJsonOrigen;
        $objJson['destino'] = $objJsonDestino;
        
        $response->setContent(json_encode($objJson));
        
        return $response;
                                                                                                                  
    }
    
    /**
     * 
     *
     * Documentación para el método 'ajaxGetPuntosParaEnlazarDatosAction'.
     *
     * Método utilizado para obtener los logins de los puntos disponibles para enlazar
     *
     * @param string login login a buscar en el listado de puntos disponibles para enlazar
     * @param int idPunto id del punto a no considerar para el listado de puntos disponibles para enlazar
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id':'',
     *                                   'login':''
     *                                  }]
     *                      }]
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 15-12-2015
    */
    public function ajaxGetPuntosParaEnlazarDatosAction()
    {     
        $request         = $this->get('request');
        $session         = $this->get('session');
        $response        = new JsonResponse();
        $emComercial     = $this->getDoctrine()->getManager();
        
        $login           = $request->get('login');
        $razonSocial     = $request->get('razonSocial');
        $direccion       = $request->get('direccion');
        $idPuntoAenlazar = $request->get('idPunto');
        $codEmpresa      = $session->get('idEmpresa');

        if(!empty($login))
        {
            $razonSocial = "";
            $direccion = "";
        }
        
        $arrayPuntos = $emComercial->getRepository('schemaBundle:InfoPunto')->getPuntosParaEnlazarDatos($login,$razonSocial,$direccion,$idPuntoAenlazar,$codEmpresa);
        
        $arrayResultado = array(
                              'data'  => $arrayPuntos
                             );
        
        $objJson = json_encode($arrayResultado);
        
        $response->setContent($objJson);
        
        return $response;
    }
    
     /**         
    * 
    * Documentación para el método 'ajaxValidaFrecuenciaAction'.
    * Valida si un Producto tiene la caracteristica FACTURACION_UNICA su frecuencia debe ser UNICA frecuencia=0
    * Si producto posee caracteristica RENTA_MENSUAL y FACTURACION_UNICA se permite el ingreso de frecuencia=0 (UNICA) o Frecuencia=1 (MENSUAL)
    * Si producto no posee caracteristica de RENTA_MENSUAL y FACTURACION_UNICA , solo se permite ingresar frecuencia =1 (Mensual)
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-08-2018
    * 
    * @param  integer $intIdProducto         
    * @param  integer $intFrecuencia           
    * 
    * @return $objRespuesta        
    */
    public function ajaxValidaFrecuenciaAction() 
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');                
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();  
        $serviceUtil        = $this->get('schema.Util');
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');         
        $intIdProducto      = $objRequest->get("intIdProducto");           
        $intFrecuencia      = $objRequest->get("intFrecuencia");  
        $serviceUtilidades  = $this->get('administracion.Utilidades');
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $strRespuesta       = "";
        try
        {  
            $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                    'strDescCaracteristica' => 'FACTURACION_UNICA',
                                                    'strEstado'             => 'Activo');
            $strEsFacturacionUnica = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            
            $arrayParametrosCaracteristicas = array('intIdProducto'         => $intIdProducto,
                                                    'strDescCaracteristica' => 'RENTA_MENSUAL',
                                                    'strEstado'             => 'Activo');
            $strEsRentaMensual = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            
            $objAdmiProducto       = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($intIdProducto);

            if((!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "S" )
                && (empty($strEsRentaMensual) || (!empty($strEsRentaMensual) && $strEsRentaMensual == "N"))
              )
            {
                if($intFrecuencia != "0")
                {                                                                               
                    $strRespuesta = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto().' ya que es de '
                        . '[FACTURACION_UNICA] y la Frecuencia que debe escoger es [UNICA]';                                   
                }
            } 
            else
            {   if(!empty($strEsFacturacionUnica) && $strEsFacturacionUnica == "N"  && $intFrecuencia == "0")                                   
                {
                    $strRespuesta = 'No se puede agregar producto '. $objAdmiProducto->getDescripcionProducto(). ' ya que no es de '.
                                    '[FACTURACION_UNICA] no puede escoger Frecuencia [UNICA]';                    
                }
            }
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'InfoServicioController.ajaxValidaFrecuenciaAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     ); 
            $strRespuesta       = "Se presentaron errores en la validacion de FRECUENCIA y servicios de FACTURACION_UNICA , "
                                . "favor notificar a Sistemas.";
                            
            $objRespuesta->setContent($strRespuesta);   
        }
        
        $objRespuesta->setContent($strRespuesta);   
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_322-3337") 
     *
     * Documentación para el método 'ajaxGetServiciosDatosByPuntoAction'.
     *
     * Método utilizado para obtener los servicios tipos transmisión de datos de un login
     *
     * @param int idPunto id del punto al cual pertenecen los servicios
     * @param int idServicio id del servicio a enlazar, utilizado para listar solo los de ese nombre tecnico
     * @param int start min de registros a buscar.
     * @param int limit max de registros a buscar.
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{ 
     *                                    'id'       : ''
     *                                    'estado'   : ''
     *                                    'servicio' : ''
     *                                    'producto' : ''
     *                                }]
     *                      }]
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 15-12-2015
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 28-09-2016 
     * - Se Modifica para que se listen todos los servicios del Login Concentrador segun su clasificacion a la 
     *   que pertenezca su extremo, se listaran los servicios de Todos los Nombres Tecnicos de la misma Clasificacion.
     * - Se elimina parametro "esConcentrador" ya que nunca es usado en el Consulta del Repositorio getServiciosDatosByPunto
     * - Se elimina parametro nombreTecnico ya que el requerimiento cambio y ya no deben listarse los Servicios con mismo nombre Tecnico 
     *   del servicio del Login extremo.
    */
    public function ajaxGetServiciosDatosByPuntoAction()
    {     
        $request         = $this->get('request');
        $session         = $this->get('session');
        $response        = new JsonResponse();
        $emComercial     = $this->getDoctrine()->getManager();
        
        $idPunto         = $request->get('idPunto');
        $idServicio      = $request->get('idServicio');        
        $start           = $request->get('start');
        $limit           = $request->get('limit');
        
        $objServicio        = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array('servicioId'=>$idServicio));
        $strClasificacion   = $objServicio->getProductoId()->getClasificacion();
        $arrayServicios     = $emComercial->getRepository('schemaBundle:InfoServicio')->getServiciosDatosByPunto($idPunto,
                                                                                                              $strClasificacion,                                                                                                              
                                                                                                              $start,
                                                                                                              $limit);

        if($objServicio->getEstado() != 'Cancel')
        {
            //registro por defecto del grid
            $arrayNuevoServicio                        = array();
            $arrayNuevoServicio['id']                  = 0;
            $arrayNuevoServicio['estado']              = "";
            $arrayNuevoServicio['producto']            = "Crear Nuevo";
            $arrayNuevoServicio['descripcionProducto'] = "";
            $arrayNuevoServicio['ultimaMillaId']       = "";
            $arrayNuevoServicio['tipoNombreTecnico']   = "";
            $arrayNuevoServicio['loginAux']            = "";
            $arrayNuevoServicio['tipoEnlace']          = (is_null($objServicioTecnico->getTipoEnlace())) ? "": $objServicioTecnico->getTipoEnlace();
            $arrayNuevoServicio['enable']              = "true";
            
            $arrayServicios['total']        = $arrayServicios['total'] + 1;
            $arrayServicios['data'][]       = $arrayNuevoServicio;
        }    
        
        $objJson = json_encode($arrayServicios);
        
        $response->setContent($objJson);
        
        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_322-3337")
     *
     * Documentación para el método 'ajaxCrearEnlaceDatosAction'.
     *
     * Método utilizado para crear el enlace de datos entre 2 puntos
     *
     * @param int idPuntoOrigen id del punto origen a donde se enlazara el punto en sesion
     * @param int idServicioOrigen id del servicio a enlazar con el id del punto origen
     * @param int idServicioDestino id del servicio que se esta creando enlace de datos
     *
     * @return Response mensaje
     *
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 15-12-2015
     * @version 1.1 21-03-2015 Cambios para soportar la opcion de definicion de concentradores de datos
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 30-09-2016
     * La herramienta permitira enlazar Servicios Extremos con diferentes tipos de concentrador(diferentes nombres tecnicos)     
     * Cuando el punto Concentrador escogido no contengan ningún servicio se creara automáticamente el concentrador que el usuario  
     * requiera sin considerar el nombre técnico del Punto Extremo, el nuevo Servicio Concentrador se genererara deacuerdo 
     * al TIPO DE CONCENTRADOR escogido previamente.
     * Se agrega ID_EMPRESA al momento de buscar el producto Concentrador para el enlace.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 05-10-2016 - Se adapta funcion para que invoque a metodo encargado del recalculo de las capacidades totales dado un concentrador
     *                           para eventos de enlace y desenlace del extremo a configurar
     * 
     * @author Allan Suarez C. <arsuarez@telconet.ec>
     * @version 1.4 2016-11-14 Se incluye llamado a metodo de control de excepciones para manejo de mensajes de las mismas de manera correcta
     * 
     * @author John Vera R. <javera@telconet.ec> 
     * @version 1.5 2017-02-01 Se valido para que cuando el servicio es diferente a EnPruebas o Activo y se defina un nuevo  concentrador no se 
     *                         realice nincuna operación
     * 
     * @author Allan Suarez <arsuarez@telconet.ec> 
     * @version 1.6 2018-02-23 Para Concentradores creados para esquema de interconexiones se agrega la caracteristica que hace referencia para
     *                         trabaje correctamente bajo ese flujo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec> 
     * @version 1.7 2018-04-06 Se agrega a validacion de creacion de caracteristica de Interconexion que se tome en cuenta el nombre tecnico
     *                         CONCINTER para que pueda seguir flujo de interconexion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec> 
     * @version 1.8 2019-01-02 Se realiza cambio para que al asignar puerto automático para concentradores L2 tome la región del login determinado
     *                         como concentrador mas no del extremo.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.9 2019-11-18 Se realiza el llamado para enlazar y crear el concentrador virtual de interconexión para los productos Datos FWA.
    */
    public function ajaxCrearEnlaceDatosAction()
    {
        $mensaje           = "Error";
        $request           = $this->get('request');
        $session           = $this->get('session');
        $emComercial       = $this->get('doctrine')->getManager();
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $strIdEmpresa      = $session->get('idEmpresa');
        
        $clienteOrigenAnterior  = "Sin dato";
        $loginOrigenAnterior    = "Sin dato";
        $bwsOrigenAnterior      = "Sin dato";//cap1 de concentrador anterior ( a desenlazar )
        $bwbOrigenAnterior      = "Sin dato";//cap2 de concentrador anterior ( a desenlazar )
        $strObservacionFactAuto = '';
        $boolEsProductoFWA      = false;
        
        $utilService = $this->get('schema.Util');
        
        $emComercial->getConnection()->beginTransaction();        
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $idPuntoOrigen        = $request->get('idPuntoOrigen');
            $idServicioDestino    = $request->get('idServicioDestino');//id del Servicio Extremo
            $idServicioOrigen     = $request->get('idServicioOrigen'); //id del Servicio Concentrador
            $ultimaMillaId        = $request->get('ultimaMillaId');
            $descripcionProducto  = $request->get('descripcionProducto');
            $loginAux             = $request->get('loginAux');
            $tipoEnlace           = $request->get('tipoEnlace');  
            $strTipoNombreTecnico = $request->get('tipoNombreTecnico');

            $serviceServicio      = $this->get('comercial.InfoServicio');

            
            if ($idPuntoOrigen>0 && $idServicioDestino>0) 
            {
                $servicioTecnicoService  = $this->get('tecnico.InfoServicioTecnico');
                $objServProdCaractBwsOrigenNew = "";
                $objServProdCaractBwbOrigenNew = "";
                
                //datos del servicio en session ( Extremo - Servicio a ser enlazado )
                $objServicio             = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicioDestino);
                $objPunto                = $objServicio->getPuntoId();
                $objProducto             = $objServicio->getProductoId();
                
                $objServProdCaractBws    = $servicioTecnicoService->getServicioProductoCaracteristica($objServicio, 
                                                                                                      'CAPACIDAD1',
                                                                                                      $objProducto
                                                                                                     );
                $objServProdCaractBwb    = $servicioTecnicoService->getServicioProductoCaracteristica($objServicio, 
                                                                                                      'CAPACIDAD2',
                                                                                                      $objProducto
                                                                                                     );
                /* Obtengo el Producto Concentrador deacuerdo al nombreTecnico o Tipo Concentrador seleccionado
                 * por estado y codigo de empresa en sesion.*/
                $objProductoConcentrador = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                       ->findOneBy(array("nombreTecnico"   =>  $strTipoNombreTecnico,
                                                                         "estado"          =>  "Activo",
                                                                         "esConcentrador"  =>  "SI",
                                                                         "empresaCod"      =>  $strIdEmpresa));
                
                // Si no existe Producto Concentrador creado para el tipo de Concentrador muestro mensaje.
                if(!is_object($objProductoConcentrador))
                {
                    return new Response("No existe Producto Concentrador definido para "
                                        .$strTipoNombreTecnico.", No se puede crear Enlace de Datos"); 
                }
                
                $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PRODUCTO_NO_VALIDA_VELOCIDADES',
                                                             'COMERCIAL',
                                                             '',
                                                             '',
                                                             $objProducto->getId(),
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             '');
                if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                {
                    $boolEsProductoFWA = true;
                }
                //Capacidades del Servicio Extremo
                $bws = $objServProdCaractBws->getValor(); //Capacidad1 Extremo
                $bwb = $objServProdCaractBwb->getValor(); //Capacidad2 Extremo
                //fin datos del servicio en session
                
                //datos para el historial ( Informacion general del Servicio Extremo )
                $objPersonaDestino      = $objPunto->getPersonaEmpresaRolId()->getPersonaId();
                $clienteDestino         = sprintf("%s",$objPersonaDestino);
                $loginDestino           = $objPunto->getLogin();
                $loginAuxDestino        = $objServicio->getLoginAux();
                
                //datos punto origen 
                $objPuntoOrigen  = $emComercial->getRepository('schemaBundle:InfoPunto')->find($idPuntoOrigen);                
                
                //si no existe un servicio origen en el punto, se crea ( Servicio a ser definido como Concentrador y si existe y no esta
                //definido como Concentrador se redefine como tal el Servicio)
                if($idServicioOrigen==0)
                {
                    $objServicioOrigen = new InfoServicio();
                    $objServicioOrigen->setPuntoId($objPuntoOrigen);
                    $objServicioOrigen->setTipoOrden('N');
                    $objServicioOrigen->setEsVenta('N');
                    $objServicioOrigen->setPrecioVenta("0.00");
                    $objServicioOrigen->setEstado($objProductoConcentrador->getEstadoInicial());
                    $objServicioOrigen->setCantidad(1);  
                    $objServicioOrigen->setFrecuenciaProducto(1);
                    $objServicioOrigen->setProductoId($objProductoConcentrador);
                    $objServicioOrigen->setDescripcionPresentaFactura($descripcionProducto);
                    $objServicioOrigen->setLoginAux($loginAux);
                    $objServicioOrigen->setIpCreacion($request->getClientIp());
                    $objServicioOrigen->setUsrCreacion($session->get('user'));
                    $objServicioOrigen->setFeCreacion(new \DateTime('now'));
                        
                    $emComercial->persist($objServicioOrigen);
                    $emComercial->flush();
                    
                    $objServicioOrigenHist = new InfoServicioHistorial();
                    $objServicioOrigenHist->setServicioId($objServicioOrigen);
                    $objServicioOrigenHist->setObservacion('Se Creo el servicio concentrador por Enlace de Datos');
                    $objServicioOrigenHist->setIpCreacion($request->getClientIp());
                    $objServicioOrigenHist->setFeCreacion(new \DateTime('now'));
                    $objServicioOrigenHist->setUsrCreacion($session->get('user'));
                    $objServicioOrigenHist->setEstado($objProductoConcentrador->getEstadoInicial());
                    $emComercial->persist($objServicioOrigenHist);
                    $emComercial->flush();
                    
                    // InfoServicioTecnico
                    $objServicioTecnico = new InfoServicioTecnico();
                    $objServicioTecnico->setServicioId($objServicioOrigen);
                    $objServicioTecnico->setUltimaMillaId($ultimaMillaId);                 
                    $objServicioTecnico->setTipoEnlace($tipoEnlace);
                    $emComercial->persist($objServicioTecnico);
                    $emComercial->flush();
                    
                    
                    $objServProdCaractBwsOrigenNew = $servicioTecnicoService->ingresarServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                                     $objProductoConcentrador,
                                                                                                                     'CAPACIDAD1',
                                                                                                                     $bws,
                                                                                                                     $session->get('user')
                                                                                                                    );

                   $objServProdCaractBwbOrigenNew = $servicioTecnicoService->ingresarServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                                    $objProductoConcentrador,
                                                                                                                    'CAPACIDAD2',
                                                                                                                    $bwb,
                                                                                                                    $session->get('user')
                                                                                                                   );

                    $objServProdCaractMigracion = $servicioTecnicoService->getServicioProductoCaracteristica($objServicio, 
                                                                                                             'INTERCONEXION_CLIENTES',
                                                                                                             $objProducto
                                                                                                            );
                    
                    if((is_object($objServProdCaractMigracion) && $objServProdCaractMigracion->getValor() == 'S') ||
                       $objProductoConcentrador->getNombreTecnico() == 'CONCINTER')
                    {
                        $objServProdCaractMigraClientes = $servicioTecnicoService->ingresarServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                                          $objProductoConcentrador,
                                                                                                                          'INTERCONEXION_CLIENTES',
                                                                                                                          'S',
                                                                                                                          $session->get('user')
                                                                                                                        );
                    }
                    
                    if($objProductoConcentrador->getNombreTecnico() == 'L2MPLS')
                    {
                        $objRoConcentrador  = null;
                        
                        $intIdOficina = $objServicioOrigen->getPuntoId()->getPuntoCoberturaId()->getOficinaId();
                    
                        $objOficina   = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                        if(is_object($objOficina))
                        {
                            $objCanton = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                            if(is_object($objCanton))
                            {                                
                                $intIdCanton     = $objCanton->getId();
                            }
                        }
                        
                        $objCanton = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
                        
                        if(is_object($objCanton))
                        {
                            $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                        }
                                                
                        $arrayInfoPe = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('ROUTERS DC - CONCENTRADORES L2', 
                                                        'TECNICO', 
                                                        '',
                                                        $strRegion,
                                                        '',

                                                        '',
                                                        '',
                                                        '', 
                                                        '', 
                                                        $session->get('idEmpresa'));
                        if(!empty($arrayInfoPe))
                        {
                            $objRoConcentrador = $emInfraestructura->getRepository("schemaBundle:InfoElemento")      
                                                                   ->findOneByNombreElemento($arrayInfoPe['valor1']);
                        }
                        
                        if(is_object($objRoConcentrador))
                        {
                            //Obtener el siguiente puerto disponible asignado al Ro
                            $objInterfaceElemento =  $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                       ->findOneBy(array('elementoId' => $objRoConcentrador->getId(),
                                                                                         'estado'     => 'not connect'));
                            //Si existe puertos disponibles
                            if(is_object($objInterfaceElemento))
                            {
                                $objServicioOrigen->setEstado('Factible');
                                $emComercial->persist($objServicioOrigen);
                                $emComercial->flush();
                                
                                $objServicioTecnico->setElementoId($objRoConcentrador->getId());
                                $objServicioTecnico->setInterfaceElementoId($objInterfaceElemento->getId());
                                $emComercial->persist($objServicioTecnico);
                                $emComercial->flush();
                                
                                $objInterfaceElemento->setEstado('connected');
                                $objInterfaceElemento->setUsrUltMod($session->get('user'));
                                $objInterfaceElemento->setFeUltMod(new \DateTime('now'));
                                $emInfraestructura->persist($objInterfaceElemento);
                                $emInfraestructura->flush();
                                
                                $strObservacionFactAuto  = 'Se asignó factibilidad automática para el Concentrador L2: '
                                                          .'<br><i class="fa fa-long-arrow-right" aria-hidden="true"></i><b>Elemento Ro: </b> '.
                                                           $objRoConcentrador->getNombreElemento()
                                                          .'<br><i class="fa fa-long-arrow-right" aria-hidden="true"></i><b>Interface:</b> '.
                                                           $objInterfaceElemento->getNombreInterfaceElemento();
                                
                                $objServicioOrigenHist = new InfoServicioHistorial();
                                $objServicioOrigenHist->setServicioId($objServicioOrigen);
                                $objServicioOrigenHist->setObservacion($strObservacionFactAuto);
                                $objServicioOrigenHist->setIpCreacion($request->getClientIp());
                                $objServicioOrigenHist->setFeCreacion(new \DateTime('now'));
                                $objServicioOrigenHist->setUsrCreacion($session->get('user'));
                                $objServicioOrigenHist->setEstado('Factible');
                                $emComercial->persist($objServicioOrigenHist);
                                $emComercial->flush();
                                
                                //Se agregar referencia de puerto
                                $objServProdCaractMigraClientes = $servicioTecnicoService->ingresarServicioProductoCaracteristica(
                                                                                                                    $objServicio, 
                                                                                                                    $objServicio->getProductoId(),
                                                                                                                    'INTERFACE_L2',
                                                                                                                    $objInterfaceElemento->getId(),
                                                                                                                    $session->get('user')
                                                                                                                    );
                                
                                $objTipoSolicitud    = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                   ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");
                                
                                if(is_object($objTipoSolicitud))
                                {
                                    $objSolicitud = new InfoDetalleSolicitud();
                                    $objSolicitud->setServicioId($objServicioOrigen);
                                    $objSolicitud->setTipoSolicitudId($objTipoSolicitud);
                                    $objSolicitud->setEstado('Factible');
                                    $objSolicitud->setUsrCreacion($session->get('user'));
                                    $objSolicitud->setObservacion($strObservacionFactAuto);
                                    $objSolicitud->setFeCreacion(new \DateTime('now'));
                                    $emComercial->persist($objSolicitud);
                                    $emComercial->flush();

                                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                    $objDetalleSolHist = new InfoDetalleSolHist();
                                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                                    $objDetalleSolHist->setIpCreacion($request->getClientIp());
                                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                    $objDetalleSolHist->setUsrCreacion($session->get('user'));
                                    $objDetalleSolHist->setObservacion($strObservacionFactAuto);
                                    $objDetalleSolHist->setEstado('Factible');
                                    $emComercial->persist($objDetalleSolHist);
                                    $emComercial->flush();
                                }
                            }
                            else
                            {
                                return new Response('No Existen Puertos disponibles para el RO <b>'.$objRoConcentrador->getNombreElemento().
                                                    '</b>, por favor notificar al Departamento de NetWorking');
                            }
                        }
                        else
                        {
                            return new Response('No Existe Información para el RO Concentrador L2 para asignación de Puerto, notificar a Sistemas');
                        }
                    }
                }
                else if($idServicioOrigen>0) //Si el servicio definido como concentrador Existente
                {
                    //Concentrador Nuevo
                    $objServicioOrigen    = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicioOrigen);
                    $objProductoOrigen    = $objServicioOrigen->getProductoId();
                    
                    //si no tiene caracteristica de Enlace de Datos, Se crea como ORIGEN
                    if($objProductoOrigen->getEsConcentrador()=="NO")
                    {
                        //se eliminan caracteristicas de capacidad con producto anterior
                        $objServProdCaractBwsAntOrigen  = $servicioTecnicoService->getServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                                     'CAPACIDAD1',
                                                                                                                     $objProductoOrigen
                                                                                                                    );
                    
                        $objServProdCaractBwbAntOrigen  = $servicioTecnicoService->getServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                                     'CAPACIDAD2',
                                                                                                                     $objProductoOrigen
                                                                                                                    );
                        $objServProdCaractBwsAntOrigen->setEstado("Eliminado");
                        $emComercial->persist($objServProdCaractBwsAntOrigen);
                        $emComercial->flush();
                    
                        $objServProdCaractBwbAntOrigen->setEstado("Eliminado");
                        $emComercial->persist($objServProdCaractBwbAntOrigen);
                        $emComercial->flush();
                        
                        //se crean nuevas caracteristicas de capacidad con el nuevo producto concentrador
                        $objServProdCaractBwsOrigenNew = $servicioTecnicoService
                                                           ->ingresarServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                    $objProductoConcentrador,
                                                                                                    'CAPACIDAD1',
                                                                                                    $objServProdCaractBwsAntOrigen->getValor(),
                                                                                                    $session->get('user')
                                                                                                   );
                    
                        $objServProdCaractBwbOrigenNew = $servicioTecnicoService
                                                           ->ingresarServicioProductoCaracteristica($objServicioOrigen, 
                                                                                                    $objProductoConcentrador,
                                                                                                    'CAPACIDAD2',
                                                                                                    $objServProdCaractBwbAntOrigen->getValor(),
                                                                                                    $session->get('user')
                                                                                                    );
                        
                        //se actualiza el nuevo producto concentrador
                        $objServicioOrigen->setProductoId($objProductoConcentrador);
                        $emComercial->persist($objServicioOrigen);
                        $emComercial->flush();
                        
                        $objServicioOrigenHist = new InfoServicioHistorial();
                        $objServicioOrigenHist->setServicioId($objServicioOrigen);
                        $objServicioOrigenHist->setObservacion("Se define como CONCENTRADOR por creación de Enlace de Datos");
                        $objServicioOrigenHist->setIpCreacion($request->getClientIp());
                        $objServicioOrigenHist->setFeCreacion(new \DateTime('now'));
                        $objServicioOrigenHist->setUsrCreacion($session->get('user'));
                        $objServicioOrigenHist->setEstado($objServicioOrigen->getEstado());
                        $emComercial->persist($objServicioOrigenHist);
                        $emComercial->flush();
                    }
                }
                else
                {
                    return new Response("Servicio origen necesario para crear Enlace de Datos");
                }   
                //fin datos punto origen
                
                //===========================================================================================================================
                //"$objServicioOrigen" es el que termina definido como nuevo Concentrador por ( Creacion del mismo, redefinicion o existente )
                //===========================================================================================================================
                
                //consulta de enlace existente del extremo con otro concentrador
                $objServProdCaractOrigenAnt = $servicioTecnicoService->getServicioProductoCaracteristica($objServicio, 
                                                                                                         'ENLACE_DATOS',
                                                                                                         $objProducto
                                                                                                        );
                //Si Existe enlace se procede a desenlazar para enganchar al nuevo concentrador
                if($objServProdCaractOrigenAnt)
                {
                    //si es el mismo punto origen al ya existente, se retorna
                    if($objServProdCaractOrigenAnt->getValor() == $objServicioOrigen->getId())
                    {
                        return new Response("Debe escoger otro Punto y servicio que no sea el ya enlazado para poder realizar el cambio.");
                    }
                    //si no se obtienen datos del punto anterior para el historial y desenlace ( Concentrador Anterior )
                    $objServicioOrigenAnt    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                           ->find($objServProdCaractOrigenAnt->getValor());
                   
                    $objPuntoOrigenAnt       = $objServicioOrigenAnt->getPuntoId();
                    $objPersonaOrigenAnt     = $objPuntoOrigenAnt->getPersonaEmpresaRolId()->getPersonaId();
                    
                    //valido que solo cuando el servicio este en estado Activo o EnPruebas disminuya
                    if($objServicio->getEstado() == 'Activo' || $objServicio->getEstado() == 'EnPruebas')
                    {
                        $strSignoOperacion = "-";
                    }
                    else
                    {
                        $strSignoOperacion = "=";
                    }
                    
                    //bajar bw del concentrador ( Motivo desenlace de extremo )
                    $arrayParametrosBw = array( 
                                                "objServicio"       => $objServicio,
                                                "usrCreacion"       => $session->get('user'),
                                                "ipCreacion"        => $request->getClientIp(),
                                                "capacidadUnoNueva" => $bws,
                                                "capacidadDosNueva" => $bwb,
                                                "operacion"         => $strSignoOperacion,
                                                "accion"            => "<b>Se desenlazó:</b><br>
                                                                        Cliente: "                  . $clienteDestino  . "<br>
                                                                        Login: "                    . $loginDestino    . "<br>
                                                                        Login Aux: "                . $loginAuxDestino . "<br>"
                                               );
                                        
                    //Se actualiza las capacidades del Concentrador
                    $arrayCapacidadesConcentrador = $servicioTecnicoService->actualizarCapacidadesEnConcentrador($arrayParametrosBw);
                    
                    $utilService->validaObjeto($arrayCapacidadesConcentrador,"No Se pudo actualizar capacidades al Concentrador");
                                       
                    //se desenlaza el extremo del concentrador actual
                    $objServProdCaractOrigenAnt->setEstado('Eliminado');
                    $emComercial->persist($objServProdCaractOrigenAnt);
                    $emComercial->flush();            
                    
                    //datos para el historial
                    $clienteOrigenAnterior  = sprintf("%s",$objPersonaOrigenAnt);
                    $loginOrigenAnterior    = $objPuntoOrigenAnt->getLogin();
                    $loginAuxOrigenAnterior = $objServicioOrigenAnt->getLoginAux();                    
                    //Se determinan Capacidades actuales del concentrador anterior
                    $bwsOrigenAnterior      = $arrayCapacidadesConcentrador['capacidadUpNueva'];
                    $bwbOrigenAnterior      = $arrayCapacidadesConcentrador['capacidadDownNueva'];
                }
                else
                {
                    //datos para el historial
                    $clienteOrigenAnterior  = "Sin información";
                    $loginOrigenAnterior    = "Sin información";
                    $loginAuxOrigenAnterior = "Sin información";
                    $bwsOrigenAnterior      = "Sin información";
                    $bwbOrigenAnterior      = "Sin información";
                }
                
                //datos de origen para historial del nuevo Concentrador
                $objServicioOrigenNew    = $objServicioOrigen;     //Nuevo Concentrador           
                $objPuntoOrigenNew       = $objServicioOrigenNew->getPuntoId();
                $objPersonaOrigenNew     = $objPuntoOrigenNew->getPersonaEmpresaRolId()->getPersonaId();     
                
                //creacion de registros para el nuevo enlace de datos hacia el Nuevo Concentrador
                $objCaracteristicaEnlaceDatos = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array( 
                                                                            "descripcionCaracteristica" => 'ENLACE_DATOS',
                                                                            "estado"                    => "Activo"
                                                                            ));
                
                $utilService->validaObjeto($objCaracteristicaEnlaceDatos,"No existe Caracteristica de Enlace de Datos");
                
                $objProdCaractEnlaceDatos = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array( 
                                                                        "productoId"       => $objProducto->getId(),
                                                                        "caracteristicaId" => $objCaracteristicaEnlaceDatos->getId(),
                                                                        "estado"           => "Activo"
                                                                        ));
                
                $utilService->validaObjeto($objProdCaractEnlaceDatos,"No existe registro que relacione el Producto con caracteristica de Enlace"
                                                                     . " de Datos");
                
                //Se realiza el enlazado del Extremo al Nuevo Concentrador
                $objServProdCaractEnlaceDatos = new InfoServicioProdCaract();
                $objServProdCaractEnlaceDatos->setServicioId($objServicio->getId());
                $objServProdCaractEnlaceDatos->setProductoCaracterisiticaId($objProdCaractEnlaceDatos->getId());
                $objServProdCaractEnlaceDatos->setValor($objServicioOrigenNew->getId()); //Servicio Concentrador Nuevo
                $objServProdCaractEnlaceDatos->setEstado("Activo");
                $objServProdCaractEnlaceDatos->setUsrCreacion($session->get('user'));
                $objServProdCaractEnlaceDatos->setFeCreacion(new \DateTime('now'));
                $emComercial->persist($objServProdCaractEnlaceDatos);
                $emComercial->flush();
                
                //Siempre y cuando el estado del servicio sea Activo. In-Corte o EnPruebas se realiza actualizado de capacidades en concentrador a
                //nivel físico.
                //Si el servicio ha sido recien creado no se ejecuta BW en concentrador pero realiza la regularizacion a nivel logico de las 
                //capacidades
                $strOperacion      = "=";
                $strEstadoServicio = $objServicio->getEstado();
                
                //Se determina si el servicio se nuevo solo realizar el recalculo del concentrador al cual se enlaza, la suma de las capacidades
                //se realiza al activar el servicio
                //Si el servicio ya se encuentra activo se puede sumar la capacidad al nuevo concentrador ( ya no requiere ser activado ) y se esta
                //realizando un cambio de concentrador por lo tanto el nuevo concentrador debe sumar las capacidades del extremo e incluirlas
                if($strEstadoServicio == 'Activo' || $strEstadoServicio == 'InCorte' || $strEstadoServicio == 'EnPruebas')
                {
                    $strOperacion = "+";
                }
                
                //Suir bw del concentrador ( Motivo enlace de nuevo extremo )
                $arrayParametrosBw = array( 
                                            "objServicio"       => $objServicio,
                                            "usrCreacion"       => $session->get('user'),
                                            "ipCreacion"        => $request->getClientIp(),
                                            "capacidadUnoNueva" => $bws,
                                            "capacidadDosNueva" => $bwb,
                                            "operacion"         => $strOperacion,
                                            "accion"            => "<b>Se enlazó:</b><br>
                                                                    Cliente: "                  . $clienteDestino  . "<br>
                                                                    Login: "                    . $loginDestino    . "<br>
                                                                    Login Aux: "                . $loginAuxDestino . "<br>",
                                            "boolSeEnlaza"      => true
                                           );

                //Se actualiza las capacidades del Concentrador ( Se aumenta BW por enlace de nuevo extremo )
                $arrayCapacidadesConcentrador = $servicioTecnicoService->actualizarCapacidadesEnConcentrador($arrayParametrosBw);
                
                $utilService->validaObjeto($arrayCapacidadesConcentrador,"No Se pudo actualizar capacidades al Concentrador");                
                               
                $clienteOrigenNew  = sprintf("%s",$objPersonaOrigenNew);
                $loginOrigenNew    = $objPuntoOrigenNew->getLogin();
                $loginAuxOrigenNew = $objServicioOrigenNew->getLoginAux();
                $bwsOrigenNew      = $arrayCapacidadesConcentrador['capacidadUpNueva'];
                $bwbOrigenNew      = $arrayCapacidadesConcentrador['capacidadDownNueva'];
                              
                $datosNuevos = "<b>Datos Nuevos:</b><br>
                                    Cliente: "            . $clienteOrigenNew  . "<br>
                                    Login: "              . $loginOrigenNew    . "<br>
                                    LoginAux: "           . $loginAuxOrigenNew . "<br>
                                    Ancho Banda Subida: " . $bwsOrigenNew      . "<br>
                                    Ancho Banda Bajada: " . $bwbOrigenNew      . "<br>";
                $datosAnteriores = "<b>Datos Anteriores:</b><br>
                                    Cliente: "            . $clienteOrigenAnterior  . "<br>
                                    Login: "              . $loginOrigenAnterior    . "<br>
                                    LoginAux: "           . $loginAuxOrigenAnterior . "<br>
                                    Ancho Banda Subida: " . $bwsOrigenAnterior      . "<br>
                                    Ancho Banda Bajada: " . $bwbOrigenAnterior      . "<br>";
                
                $observacionDestino = "Se definió nuevo Concentrador.<br><br>" . $datosNuevos . "<br>" . $datosAnteriores;
               
                $objServicioHist = new InfoServicioHistorial();
                $objServicioHist->setServicioId($objServicio);
                $objServicioHist->setObservacion($observacionDestino);
                $objServicioHist->setIpCreacion($request->getClientIp());
                $objServicioHist->setFeCreacion(new \DateTime('now'));
                $objServicioHist->setUsrCreacion($session->get('user'));
                $objServicioHist->setEstado($objServicio->getEstado());
                $emComercial->persist($objServicioHist);
                $emComercial->flush();

                $mensaje = "Servicio Enlazado exitosamente";

                if($boolEsProductoFWA)
                {
                    $objUltimaMilla     = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                            ->findOneByCodigoTipoMedio('UTP');
                    $strUltimaMillaId   = (is_object($objUltimaMilla)) ? $objUltimaMilla->getId() : $ultimaMillaId;

                    $arrayParametrosFWA = array("descripcionCaracteristica" => 'CONCENTRADOR_FWA',
                                                "estado"                    => 'Activo',
                                                "objPunto"                  => $objPunto,
                                                "objServicio"               => $objServicio,
                                                "objProducto"               => $objProducto,
                                                "strUsuario"                => $session->get('user'),
                                                "strIpCreacion"             => $request->getClientIp(),
                                                "strEmpresaCod"             => $strIdEmpresa,
                                                "strNombreTecnico"          => 'CONCINTER',
                                                "strLoginAux"               => $loginAux,
                                                "objPuntoOrigen"            => $objPuntoOrigen,
                                                "strUltimaMillaId"          => $strUltimaMillaId,
                                                "strTipoEnlace"             => $tipoEnlace,
                                                "strCapacidad1"             => $bws,
                                                "strCapacidad2"             => $bwb,
                                                "strPrefijoEmpresa"         => $session->get('prefijoEmpresa')
                                                );
                    $arrayRespuesta = $serviceServicio->concentradorVirtual($arrayParametrosFWA);

                    if(!empty($arrayRespuesta) && $arrayRespuesta['strStatus'] === 'OK')
                    {
                        $mensaje.= $arrayRespuesta['strMensaje'];
                    }
                    else if(!empty($arrayRespuesta) && $arrayRespuesta['strStatus'] === 'ERROR')
                    {
                        throw new \Exception($arrayRespuesta['strMensaje']);
                    }
                }

                $emComercial->commit();               

                if(!empty($strObservacionFactAuto) && $objProductoConcentrador->getNombreTecnico() == 'L2MPLS')
                {
                    $mensaje = $mensaje.'<br/>'.$strObservacionFactAuto;
                    $emInfraestructura->commit();
                }
            }
        }
        catch (\Exception $e) 
        {            
            $utilService->insertError('Telcos+', 
                                      'InfoServicioController->ajaxCrearEnlaceDatosAction', 
                                      $e->getMessage(),
                                      $session->get('user'), 
                                      $request->getClientIp()
                                     );
                       
            $mensaje = $utilService->getMensajeException($e);
            
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();                
            }  
            
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();                
            }  
            
            $emComercial->getConnection()->close();
            $emInfraestructura->getConnection()->close();
        }
        
        return new Response($mensaje);  
        
    }
    
    /**
     * Documentación para el método 'ajaxServiciosRequiereBackupAction'.
     *
     * Método utilizado para obtener los servicios por producto que posee el Punto.
     *
     * @return Response Mensaje
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 29-04-2016
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 20-07-2016
     * Se recibe y envía el nuevo parámetro EXCLUIDOS para filtrar la asignación de BackUps a servicios principales que ya están seleccionados en el
     * ingreso de nuevos servicios.
     */
    public function ajaxServiciosRequiereBackupAction()
    {
        $objPeticion = $this->get('request');
        $objSession  = $objPeticion->getSession();
        
        try
        {
            $arrayExcluidos = null;
            $strExcluidos   = $objPeticion->get('excluidos');
            
            if($strExcluidos != '')
            {
                $arrayExcluidos = explode(',', $strExcluidos);
            }
            $i = 0;
            if($arrayExcluidos)
            {
                foreach($arrayExcluidos as $excluido)
                {
                    $arrayExcluidos[$i] = intval($excluido);
                    $i++;
                }
            }
            $arrayParametros = array('PUNTO'     => $objPeticion->get('puntoId'), 
                                     'PRODUCTO'  => $objPeticion->get('codigo'), 
                                     'EMPRESA'   => $objSession->get('idEmpresa'),
                                     'EXCLUIDOS' => $arrayExcluidos);

            return $this->get('comercial.InfoServicio')->serviciosRequiereBackup($arrayParametros, true);
        }
        catch(\Exception $e)
        {
            $strJsonServicios = '{"msg":"error", "error":"' . $e->getMessage() . '"}';
            $objResponse   = new Response();
            $objResponse->headers->set('Content-type', 'text/json');
            $objResponse->setContent($strJsonServicios);
            return $objResponse;
        }
    }
    
     /**     
     * Documentación para el método 'ajaxGetAdmiTipoMedio'.
     *
     * Método utilizado para obtener el listado de los tipo medio existentes
     *     
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.0 18-05-2016
    */
    public function ajaxGetAdmiTipoMedioAction()
    {
        $response        = new JsonResponse();
        $request         = $this->get('request');
        $session         = $this->get('session');        
        
        $em_infraestructura      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $json_tipo_medio         = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->getJsonTiposMedios();

        $response->setContent($json_tipo_medio);
        
        return $response;                                                                                                                  
    }
    
    /**     
     * @Secure(roles="ROLE_13-4577")
     * 
     * Documentación para el método 'ajaxActualizarPlanEquivalente'.
     *
     * Método utilizado para actualizar el id del plan de un servicio en estado AsignadoTarea ó Activo
     *     
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 10-08-2016
     */
    public function ajaxActualizarPlanEquivalenteAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUser      = $objSession->get("user");
        $serviceUtil  = $this->get('schema.Util');
        $em           = $this->getDoctrine()->getManager();
        $em->beginTransaction();
        try
        {
            $objInfoServicio = $em->getRepository('schemaBundle:InfoServicio')->find($objRequest->get('idServicio'));
            $objInfoPlanCab  = $objInfoServicio->getPlanId();
            if ($objInfoPlanCab->getEstado() == "Activo")
            {
                $objRespuesta->setContent("El plan del servicio se encuentra en estado Activo.");
            }
            else
            {
                $objInfoPlanCabActivo = $em->getRepository('schemaBundle:InfoPlanCab')
                                           ->findOneBy(array('nombrePlan' => $objInfoPlanCab->getNombrePlan(),
                                                             'estado'     => 'Activo'),
                                                       array('nombrePlan' => 'DESC'));
                if ($objInfoPlanCabActivo)
                {
                    $objInfoServicio->setPlanId($objInfoPlanCabActivo);
                    $em->persist($objInfoServicio);
                    $em->flush();
                    $em->getConnection()->commit();
                    $objRespuesta->setContent("OK");
                }
                else
                {
                    $objRespuesta->setContent("No se encontro un plan con el nombre "+$objInfoPlanCab->getNombrePlan()+" en estado Activo.");
                }
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'actualizarPlanEquivalenteAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            
        }
        $em->getConnection()->close();
        return $objRespuesta;                                                                                                                  
    }
    
    /**
     * Funcion que valida la existencia de un correo electronico como caracteristica de 
     * servicios de internet protegido McAfee que se encuentren en cualquier estado, no
     * se validaran estado de servicios por que los correos electronicos que ya han sido utilizados en 
     * suscripciones McAfee (sin importar si esta Cancelado, In-Corte ó Activo ) no pueden volver
     * a usarse para futuras suscripciones, tecnicamente McAfee trabaja de esa manera.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 09-09-2016
     * @since 1.0 09-09-2016
     * 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-12-2018 Se agregan estado no considerados en la consulta de características del servicio
     * 
     * @return redirect
     **/
    public function ajaxValidaCorreoMcAfeeAction()
    {
        $objRespuesta    = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();
        $strIpClient     = $objRequest->getClientIp();
        $strUser         = $objSession->get("user");
        $serviceUtil     = $this->get('schema.Util');
        $em              = $this->getDoctrine()->getManager();
        $arrayParametros = array();
        $objRespuestaValidacion    = null;
        $strRespuesta              = "ERROR";
        try
        {
            $arrayParametros["descripcionCaracteristica"] = "CORREO ELECTRONICO";
            $arrayParametros["descripcionProducto"]       = "I. ";
            $arrayParametros["nombreTecnico"]             = "OTROS";
            $arrayParametros["valorSpc"]                  = $objRequest->get('correoElectronico');
            $arrayParametros["estadosSpcNoConsiderados"]  = array("Eliminado");
            
            $objRespuestaValidacion = $em->getRepository('schemaBundle:InfoServicio')
                                         ->existeCaraceristicaServicio($arrayParametros);
            
            if (is_object($objRespuestaValidacion))
            {
                $strRespuesta = "EXISTENTE";
            }
            else
            {
                $strRespuesta = "NO EXISTENTE";
            }
            
            $objRespuesta->setContent($strRespuesta);   
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'ajaxValidaCorreoMcAfeeAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("ERROR");
        }
        return $objRespuesta;
    }
    /**
     * Funcion que valida la existencia de un correo electronico como caracteristica de 
     * servicios ECDF que se encuentren en estado Activo.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0 09-08-2021
     
     * @return redirect
     **/
    public function ajaxValidaCorreoECDFAction()
    {
        $objRespuesta    = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();
        $strIpClient     = $objRequest->getClientIp();
        $strUser         = $objSession->get("user");
        $serviceUtil     = $this->get('schema.Util');
        $emComercial     = $this->getDoctrine()->getManager();
        $emGeneral       = $this->get('doctrine')->getManager('telconet_general');
        $arrayParametros = array();
        $objRespuestaValidacion    = null;
        $strRespuesta              = "ERROR";
        try
        {
            //parametro que devuelve la data de los productos a validar el correo existente.
            $arrayParametroValidaCorreo =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('VALIDA_CORREO_EXISTENTE_ECDF',//nombre parametro cab
                                                            'COMERCIAL', //modulo cab
                                                            'OBTENER_DATA',//proceso cab
                                                            'PRODUCTO_TV', //descripcion det
                                                            '','','','','',
                                                            '18'); //empresa
            if(!empty($arrayParametroValidaCorreo))
            {
                $arrayParametros["nombreTecnico"]             = $arrayParametroValidaCorreo['valor1'];
                $arrayParametros["descripcionCaracteristica"] = $arrayParametroValidaCorreo['valor2'];
                $arrayParametros["descripcionProducto"]       = $arrayParametroValidaCorreo['valor3'];
                $arrayParametros["estadoSpc"]                 = $arrayParametroValidaCorreo['valor4'];
                $arrayParametros["inEstadoServ"]              = explode(",",$arrayParametroValidaCorreo['valor5']);
                $arrayParametros["valorSpc"]                  = $objRequest->get('correoElectronico');
                
                $objRespuestaValidacion = $emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->existeCaraceristicaServicio($arrayParametros);
            }
            else
            {
                throw new \Exception("No se encontraron Valores para Validar Correo Electrónico"); 
            }
            
            if (is_object($objRespuestaValidacion))
            {
                $strRespuesta = "EXISTENTE";
            }
            else
            {
                $strRespuesta = "NO EXISTENTE";
            }
            
            $objRespuesta->setContent($strRespuesta);   
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'ajaxValidaCorreoECDFAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("ERROR");
        }
        return $objRespuesta;
    }

    /**
     * Documentación para el método 'ajaxGetFrecuenciasFacturacionAction'.
     *
     * Método utilizado para obtener el listado JSON de las frecuencias de facturación por empresa.
     *     
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 29-06-2016
     */
    public function ajaxGetFrecuenciasFacturacionAction()
    {
        $response = new JsonResponse();
        $response->setContent($this->get('doctrine')->getManager('telconet')
                                                    ->getRepository('schemaBundle:InfoServicio')
                                                    ->getJsonFrecuenciasFacturacion($this->get('request')->getSession()->get('idEmpresa')));
        return $response;                                                                                                                  
    }
    
    /**
     * Documentación para el método 'cambiarFrecuenciaFacturacionAction'.
     *
     * Método actualiza la frecuencia de facturación de un servicio y los meses restantes para la próxima facturación.
     * Se deja en el historial la huella de esta operación.
     *     
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 29-06-2016
     */
    public function cambiarFrecuenciaFacturacionAction()
    {
        $objResponse  = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        
        $objRequest    = $this->get('request');
        $intIdServicio = $objRequest->get('servicio');
        $intFrecuencia = intVal($objRequest->get('frecuencia'));
        $clientIp      = $objRequest->getClientIp();
        $emComercial   = $this->getDoctrine()->getManager();
        $session       = $objRequest->getSession();
        $usrCreacion   = $session->get('user');
        try
        {
            $emComercial->getConnection()->beginTransaction();
            
            $entityServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!$entityServicio)
            {
                throw new \Exception("Servicio no existe, por favor comuníquese con sistemas.");
            }
            $intFrecuenciaAnt = $entityServicio->getFrecuenciaProducto();
            
            // Historial de cambio de frecuencia
            $entityServicioHist = new InfoServicioHistorial();
            $entityServicioHist->setServicioId($entityServicio);
            $entityServicioHist->setObservacion('Se cambió la Frecuencia y Meses Facturación de ' . $intFrecuenciaAnt . ' a ' . $intFrecuencia);
            $entityServicioHist->setIpCreacion($clientIp);
            $entityServicioHist->setFeCreacion(new \DateTime('now'));
            $entityServicioHist->setUsrCreacion($usrCreacion);
            $entityServicioHist->setEstado($entityServicio->getEstado());
            
            $entityServicio->setFrecuenciaProducto($intFrecuencia);
            $entityServicio->setMesesRestantes($intFrecuencia);
            
            $emComercial->persist($entityServicio);
            $emComercial->persist($entityServicioHist);
            
            $emComercial->flush();
            $emComercial->getConnection()->commit();
            $objResponse->setContent("OK");
        }
        catch(Exception $ex)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
            $objResponse->setContent($ex->getMessage());
        }
        
        return $objResponse;
    }

    /**
     * Documentación para el método 'definirEsquemaPeHsrpAction'.
     *
     * Método encargado de definir si una orden de servicio va utilizar el esquema de Pe-Hsrp
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-03-2019
     */
    public function definirEsquemaPeHsrpAction()
    {
        $objResponse    = new JsonResponse();
        $arrayRespuesta = array();
        $objRequest     = $this->get('request');
        $serviceUtil    = $this->get('schema.Util');
        $intIdServicio  = $objRequest->get('idServicio');
        $intIdProducto  = $objRequest->get('idProducto');
        $emComercial    = $this->getDoctrine()->getManager();
        $objSession     = $objRequest->getSession();
        $strUsrCreacion = $objSession->get('user');
        $strClienteIp   = $objRequest->getClientIp();

        try
        {
            $emComercial->getConnection()->beginTransaction();

            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array("descripcionCaracteristica" => 'PE-HSRP',
                                                                   "estado"                    => "Activo"));

            if(is_object($objAdmiCaracteristica))
            {
                $objAdmiProductoCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                     ->findOneBy(array("caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                       "productoId"       => $intIdProducto));
            }

            if(is_object($objAdmiProductoCaract))
            {
                //Se obtiene el objeto info servicio
                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                //se inserta el servicio producto caracteristica
                $objServicioProdCaract = new InfoServicioProdCaract();
                $objServicioProdCaract->setServicioId($intIdServicio);
                $objServicioProdCaract->setProductoCaracterisiticaId($objAdmiProductoCaract->getId());
                $objServicioProdCaract->setValor("S");
                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                $objServicioProdCaract->setUsrCreacion($strUsrCreacion);
                $objServicioProdCaract->setEstado("Activo");
                $emComercial->persist($objServicioProdCaract);
                $emComercial->flush();

                if(is_object($objInfoServicio))
                {
                    //Se registra historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objInfoServicio);
                    $objServicioHistorial->setObservacion("Se configura esquema PE-HSRP");
                    $objServicioHistorial->setIpCreacion($strClienteIp);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                }
            }

            $arrayRespuesta["status"]  = "OK";
            $arrayRespuesta["mensaje"] = "Registro Exitoso";

            $emComercial->getConnection()->commit();
        }
        catch(Exception $ex)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();

            $serviceUtil->insertError('Telcos+',
                                      'InfoServicioController.definirEsquemaPeHsrpAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strClienteIp
                                     );

            $arrayRespuesta["status"]  = "ERROR";
            $arrayRespuesta["mensaje"] = $ex->getMessage();
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
     * Documentación para el método 'reversarOrdenTrabajoAction'.
     *
     * Encargado de reservar el estado de un servicio de Asignada a AsignadoTarea, y adicional dejar regularizada toda la data.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 03-01-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-03-2020 - Se agrega el reverso de asignación de recursos existentes
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 28-07-2020 - Se Agrega logica para reservar el servicio a un estado que sea configurable segun el producto_id.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     */
    public function reversarOrdenTrabajoAction()
    {
        $objResponse              = new JsonResponse();
        $arrayRespuesta           = array();
        $objRequest               = $this->get('request');
        $serviceUtil              = $this->get('schema.Util');
        $strIpCreacion            = $objRequest->getClientIp();
        $intIdServicio            = $objRequest->get('idServicio');
        $strObservacion           = $objRequest->get('observacion');
        $strDescripcionProducto   = $objRequest->get('descripcionProducto');
        $strProductoId            = $objRequest->get('productoId');
        $strStatus                = "";
        $strMensaje               = "";
        $strEstadoActualizar      = "AsignadoTarea";
        $strEstadoSolictudABuscar = "Finalizada";
        $strProductoPermitido     = "N";
        $emComercial       = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $objSession        = $objRequest->getSession();
        $strUsrCreacion    = $objSession->get('user');
        $strClienteIp      = $objRequest->getClientIp();

        try
        {
            $emComercial->getConnection()->beginTransaction();

            if(!empty($intIdServicio))
            {
                //Se obtiene el objeto info servicio
                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                if(is_object($objInfoServicio))
                {
                    //**************Consultar el estado al que se debe actualizar el servicio segun el producto************//
                    $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PRODUCTOS PERMITIDOS HERRAMIENTA REVERSAR ORDEN TRABAJO',
                                                                 'INFRAESTRUCTURA',
                                                                 'REVERSAR ORDEN DE TRABAJO',
                                                                 'MAPEO DE PRODUCTOS Y ESTADOS',
                                                                 $objInfoServicio->getProductoId()->getId(),
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '10');

                    if( isset($arrayValoresParametros) && !empty($arrayValoresParametros) )
                    {
                        $strProductoPermitido     = "S";
                        $strEstadoActualizar      = $arrayValoresParametros["valor2"];
                        $strEstadoSolictudABuscar = $arrayValoresParametros["valor3"];
                    }
                    //**************************** SERVICIO *****************************//
                    //Se actualiza el estado del servicio
                    $objInfoServicio->setEstado($strEstadoActualizar);
                    $emComercial->persist($objInfoServicio);
                    $emComercial->flush();

                    //Se registra historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objInfoServicio);
                    $objServicioHistorial->setObservacion("<b>Reverso de Orden de Trabajo de Asignada a ".$strEstadoActualizar."</b><br>"
                                                        ."Se reversó: <br><br>&#8226; El estado del servicio<br>&#8226; El estado de la solicitud"
                                                        ."<br>&#8226; Se liberaron recursos de red <br><br> <b>Obs.: </b> "
                                                        .$strObservacion);
                    $objServicioHistorial->setIpCreacion($strClienteIp);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setEstado($strEstadoActualizar);
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                    //**************************** SERVICIO *****************************//

                    //**************************** SOLICITUD *****************************//
                    //Se obtiene la solicitud de instalación
                    $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

                    if(is_object($objTipoSolicitud))
                    {
                        $objInfoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                               ->findOneBy(array('servicioId'      => $intIdServicio,
                                                                                 'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                                 'estado'          => 'Asignada'));

                        //Si no se encuentra la solicitud y es un servicio configurado se busca la solicitud Finalizada
                        if(!is_object($objInfoDetalleSolicitud) && $strProductoPermitido === "S")
                        {
                            $objInfoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                   ->findOneBy(array('servicioId'      => $intIdServicio,
                                                                                     'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                                     'estado'          => 'Finalizada'));
                        }

                        if(is_object($objInfoDetalleSolicitud))
                        {
                            //Se actualiza el estado a la solicitud
                            $objInfoDetalleSolicitud->setEstado($strEstadoActualizar);
                            $emComercial->persist($objInfoDetalleSolicitud);
                            $emComercial->flush();

                            //Se registra historial de la solicitud
                            $objInfoDetalleSolHist= new InfoDetalleSolHist();
                            $objInfoDetalleSolHist->setEstado($strEstadoActualizar);
                            $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                            $objInfoDetalleSolHist->setObservacion("<b>Reverso de Orden de Trabajo de Asignada a ".$strEstadoActualizar."</b><br>"
                                                           ."Se reversó: <br><br>&#8226; El estado del servicio<br>&#8226; El estado de la solicitud"
                                                           ."<br>&#8226; Se liberaron recursos de red <br><br> <b>Obs.: </b> "
                                                           .$strObservacion);
                            $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                            $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                            $emComercial->persist($objInfoDetalleSolHist);
                            $emComercial->flush();
                        }
                    }
                    //**************************** SOLICITUD *****************************//
                    
                    //**************************** OBTENGO EL TIPO DE RECURSO *****************************//
                    $strRecursosNuevos = 'S';
                    $strNombreTecnico  = $objInfoServicio->getProductoId()->getNombreTecnico();
                    if( $strNombreTecnico === "L3MPLS" )
                    {
                        $arrayParametrosTP["intIdServicio"]   = $objInfoServicio->getId();
                        $arrayParametrosTP["strTipoRecursos"] = "Nuevos";
                        $arrayParametrosTP["strHistorial"]    = "Recursos:";
                        $arrayParametrosTP["strEstado"]       = "Asignada";
                        $strRecursosNuevos = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                                            ->validaTipoRecurso($arrayParametrosTP);
                    }
                    //**************************** OBTENGO EL TIPO DE RECURSO *****************************//

                    //*******************************************************************************************************************//
                    //************************************************* LIBERAR RECURSOS DE RED *****************************************//
                    //*******************************************************************************************************************//
                        //-------------------------------------------- VLAN --------------------------------------------//
                        //Se obtiene id de la caracteristica
                        $objCaractVlan = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                     ->findOneBy(array('descripcionCaracteristica' => 'VLAN',
                                                                       'estado'                    => 'Activo'));

                        if(is_object($objCaractVlan))
                        {
                            $objAdmiProdCarac = $emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                                            ->findOneBy(array('caracteristicaId' => $objCaractVlan->getId(),
                                                                              'productoId'       => $strProductoId,
                                                                              'estado'           => "Activo"));

                            if(is_object($objAdmiProdCarac))
                            {
                                $objServCaractVlan = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                                                 ->findOneBy(array('servicioId'                => $intIdServicio,
                                                                                   'productoCaracterisiticaId' => $objAdmiProdCarac->getId(),
                                                                                   'estado'                    => "Activo"));

                                if(is_object($objServCaractVlan))
                                {
                                    //1ero. poner en estado Eliminado la INFO_SERVICIO_PROD_CARACT
                                    $objServCaractVlan->setEstado("Eliminado");
                                    $objServCaractVlan->setUsrUltMod($strUsrCreacion);
                                    $objServCaractVlan->setFeUltMod(new \DateTime('now'));
                                    $emComercial->persist($objServCaractVlan);
                                    $emComercial->flush();

                                    if( $strRecursosNuevos === 'S' )
                                    {
                                        if($strDescripcionProducto === "L3MPLS")
                                        {
                                            $intCantidadVlan = 0;

                                            //2do. poner en estado Eliminado la INFO_PERSONA_EMPRESA_ROL_CARAC
                                            $objPersonaEmpresaRolCaract = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                                                      ->find($objServCaractVlan->getValor());

                                            if(is_object($objPersonaEmpresaRolCaract))
                                            {
                                                //***Consultar la vlan asociada y verificar si esta asociada a mas de un servicio***/
                                                $objInfoDetalleElemento = $emComercial->getRepository("schemaBundle:InfoDetalleElemento")
                                                                                      ->find($objPersonaEmpresaRolCaract->getValor());

                                                if(is_object($objInfoDetalleElemento))
                                                {
                                                    $objPersonaEmpRolCaract = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                                                        ->findBy(array('valor'  => $objInfoDetalleElemento->getId(),
                                                                                                       'estado' => "Activo"));

                                                    $intCantidadVlan = count($objPersonaEmpRolCaract);
                                                }
                                                //******Consultar la vlan asociada y verificar si esta asociada a mas de un servicio******//

                                                $objPersonaEmpresaRolCaract->setEstado("Eliminado");
                                                $objPersonaEmpresaRolCaract->setUsrUltMod($strUsrCreacion);
                                                $objPersonaEmpresaRolCaract->setFeUltMod(new \DateTime('now'));
                                                $emComercial->persist($objPersonaEmpresaRolCaract);
                                                $emComercial->flush();

                                                //3ero. poner en estado Activo la INFO_DETALLE_ELEMENTO
                                                //Solo si la vlan esta asociada a un servicio se pone en estado 'Activo'
                                                if($intCantidadVlan == 1)
                                                {
                                                    $objInfoDetalleElemento->setEstado("Activo");
                                                    $emComercial->persist($objInfoDetalleElemento);
                                                    $emComercial->flush();
                                                }
                                            }
                                        }
                                        else if(($strDescripcionProducto === "INTMPLS") || ($strDescripcionProducto === "INTERNET")
                                                || ($strDescripcionProducto === "INTERNET SDWAN"))
                                        {
                                            $intCantidadVlan = 0;

                                            //************Consultar la vlan asociada y verificar si esta asociada a mas de un servicio************//
                                            $objInfoDetalleElemento = $emComercial->getRepository("schemaBundle:InfoDetalleElemento")
                                                                                  ->find($objServCaractVlan->getValor());

                                            if(is_object($objInfoDetalleElemento))
                                            {
                                                $objInfoServProdCaract = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                                                                     ->findBy(array('valor'  => $objInfoDetalleElemento->getId(),
                                                                                                    'estado' => "Activo"));

                                                $intCantidadVlan = count($objInfoServProdCaract);
                                            }
                                            //************Consultar la vlan asociada y verificar si esta asociada a mas de un servicio************//

                                            //2do. poner en estado Activo la INFO_DETALLE_ELEMENTO
                                            if($intCantidadVlan == 1)
                                            {
                                                $objInfoDetalleElemento->setEstado("Activo");
                                                $emComercial->persist($objInfoDetalleElemento);
                                                $emComercial->flush();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //-------------------------------------------- VLAN --------------------------------------------//

                    //-------------------------------------------- VRF --------------------------------------------//
                    //Se obtiene id de la caracteristica
                    $objCaractVrf = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                ->findOneBy(array('descripcionCaracteristica' => 'VRF',
                                                                  'estado'                    => 'Activo'));

                    if(is_object($objCaractVrf))
                    {
                        $objAdmiProdCarac = $emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                                       ->findOneBy(array('caracteristicaId' => $objCaractVrf->getId(),
                                                                         'productoId'       => $strProductoId,
                                                                         'estado'           => "Activo"));

                        if(is_object($objAdmiProdCarac))
                        {
                            $objServCaractVrf = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                                            ->findOneBy(array('servicioId'                => $intIdServicio,
                                                                              'productoCaracterisiticaId' => $objAdmiProdCarac->getId(),
                                                                              'estado'                    => "Activo"));

                            if(is_object($objServCaractVrf))
                            {
                                //1ero. poner en estado Eliminado la INFO_SERVICIO_PROD_CARACT
                                $objServCaractVrf->setEstado("Eliminado");
                                $objServCaractVrf->setUsrUltMod($strUsrCreacion);
                                $objServCaractVrf->setFeUltMod(new \DateTime('now'));
                                $emComercial->persist($objServCaractVrf);
                                $emComercial->flush();
                            }
                        }
                    }
                    //-------------------------------------------- VRF --------------------------------------------//

                    //-------------------------------------------- DEFAULT_GATEWAY --------------------------------------------//
                    //Se obtiene id de la caracteristica
                    $objCaractGateWay = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                    ->findOneBy(array('descripcionCaracteristica' => 'DEFAULT_GATEWAY',
                                                                      'estado'                    => 'Activo'));

                    if(is_object($objCaractGateWay))
                    {
                        $objAdmiProdCarac = $emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                                       ->findOneBy(array('caracteristicaId' => $objCaractGateWay->getId(),
                                                                         'productoId'       => $strProductoId,
                                                                         'estado'           => "Activo"));

                        if(is_object($objAdmiProdCarac))
                        {
                            $objServCaractGateway = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                                                ->findOneBy(array('servicioId'                => $intIdServicio,
                                                                                  'productoCaracterisiticaId' => $objAdmiProdCarac->getId(),
                                                                                  'estado'                    => "Activo"));

                            if(is_object($objServCaractGateway))
                            {
                                //1ero. poner en estado Eliminado la INFO_SERVICIO_PROD_CARACT
                                $objServCaractGateway->setEstado("Eliminado");
                                $objServCaractGateway->setUsrUltMod($strUsrCreacion);
                                $objServCaractGateway->setFeUltMod(new \DateTime('now'));
                                $emComercial->persist($objServCaractGateway);
                                $emComercial->flush();
                            }
                        }
                    }
                    //-------------------------------------------- DEFAULT_GATEWAY --------------------------------------------//

                    //----------------------------------------- PROTOCOLO_ENRUTAMIENTO -----------------------------------------//
                    //Se obtiene id de la caracteristica
                    $objCaractProtocolo = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                      ->findOneBy(array('descripcionCaracteristica' => 'PROTOCOLO_ENRUTAMIENTO',
                                                                        'estado'                    => 'Activo'));

                    if(is_object($objCaractProtocolo))
                    {
                        $objAdmiProdCarac = $emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                                       ->findOneBy(array('caracteristicaId' => $objCaractProtocolo->getId(),
                                                                         'productoId'       => $strProductoId,
                                                                         'estado'           => "Activo"));

                        if(is_object($objAdmiProdCarac))
                        {
                            $objServCaractProtocolo = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                                                  ->findOneBy(array('servicioId'                => $intIdServicio,
                                                                                    'productoCaracterisiticaId' => $objAdmiProdCarac->getId(),
                                                                                    'estado'                    => "Activo"));

                            if(is_object($objServCaractProtocolo))
                            {
                                //1ero. poner en estado Eliminado la INFO_SERVICIO_PROD_CARACT
                                $objServCaractProtocolo->setEstado("Eliminado");
                                $objServCaractProtocolo->setUsrUltMod($strUsrCreacion);
                                $objServCaractProtocolo->setFeUltMod(new \DateTime('now'));
                                $emComercial->persist($objServCaractProtocolo);
                                $emComercial->flush();
                            }
                        }
                    }
                    //----------------------------------------- PROTOCOLO_ENRUTAMIENTO -----------------------------------------//

                    //---------------------------------------------- IP ASIGNADO ----------------------------------------------//
                    $objInfoIp = $emComercial->getRepository("schemaBundle:InfoIp")
                                                                ->findOneBy(array('servicioId' => $intIdServicio,
                                                                                  'estado'     => "Activo"));

                    if(is_object($objInfoIp))
                    {
                        //Poner en estado Eliminado la INFO_IP
                        $objInfoIp->setEstado("Eliminado");
                        $emComercial->persist($objInfoIp);
                        $emComercial->flush();

                        if($strRecursosNuevos === 'S' && $strDescripcionProducto === "L3MPLS")
                        {
                        //------------------------------------------------- SUBRED ------------------------------------------------//
                            //Liberar la subred y sus hijas
                            $objInfoSubred = $emComercial->getRepository("schemaBundle:InfoSubred")->find($objInfoIp->getSubredId());

                            if(is_object($objInfoSubred))
                            {
                                $arrayParametrosLiberarSubred               = array();
                                $arrayParametrosLiberarSubred['tipoAccion'] = 'liberar';
                                $arrayParametrosLiberarSubred['uso']        = $objInfoSubred->getUso();
                                $arrayParametrosLiberarSubred['subredId']   = $objInfoSubred->getId();

                                $arrayRespuestaLiberar = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                           ->provisioningSubred($arrayParametrosLiberarSubred);

                                if($arrayRespuestaLiberar['msg'] !== 'OK')
                                {
                                    $strStatus  = "ERROR";
                                    $strMensaje = "No se pudo reversar estado de la orden, favor notificar a Sistemas";
                                }
                            }
                        }
                        //------------------------------------------------- SUBRED ------------------------------------------------//
                    }
                    //---------------------------------------------- IP ASIGNADO ----------------------------------------------//
                    //*******************************************************************************************************************//
                    //************************************************* LIBERAR RECURSOS DE RED *****************************************//
                    //*******************************************************************************************************************//

                    $strStatus  = "OK";
                    $strMensaje = "Se reversó la orden de Asignada a ".$strEstadoActualizar;
                }
            }

            $emComercial->getConnection()->commit();
        }
        catch(Exception $ex)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();

            $serviceUtil->insertError('Telcos+',
                                      'InfoServicioController.reversarOrdenTrabajoAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strClienteIp);

            $strStatus  = "ERROR";
            $strMensaje = "No se pudo reversar estado de la orden, problemas con la herramienta";
        }

        $arrayRespuesta["strStatus"]  = $strStatus;
        $arrayRespuesta["strMensaje"] = $strMensaje;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


    /**
     * Documentación para el método 'ajaxGetTiposConcentradoresAction'.
     *
     * Método utilizado para obtener los Tipos de Concentradores Disponibles segun el campo Clasificacion 
     * del Producto del servicio del Punto extremo
     *     
     * @param int idServicio id del servicio extremo del Enlace
     *
     * @return JsonResponse [{                       
     *                      'encontrados'  : [{
     *                                         'idTipoConcentrador':'',
     *                                         'tipoConcentrador'  :''
     *                                  }]
     *                      }]
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 28-09-2016  
    */
    public function ajaxGetTiposConcentradoresAction()
    {     
        $request         = $this->get('request');
        $session         = $this->get('session');
        $response        = new JsonResponse();
        $emComercial     = $this->getDoctrine()->getManager();
                
        $intIdServicio   = $request->get('idServicio');
        $strCodEmpresa   = $session->get('idEmpresa');
        
        $objServicio                = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        $strClasificacion           = $objServicio->getProductoId()->getClasificacion();
        $strJsonTiposConcentradores = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                  ->getJsonTiposConcentradores($strClasificacion,$strCodEmpresa);

        $response->setContent($strJsonTiposConcentradores);
        
        return $response;     
    }
    
    
    /**     
     * Documentación para el método 'ajaxGetPlantillaComisionistaAction'.
     *
     * Método utilizado para obtener la plantilla de comisionistas relacionada al producto seleccionada por el usuario
     *     
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 10-04-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 20-03-2018 - Se agrega atributo "class" al div que devuelve la informacion de comisionistas para poder
     *                           manejar el layout a nivel de pantalla
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 - 19-05-2019 - Se realiza un ajuste a unos elementos para poder mostrarlos de manera mas simétrica, mejorando la
     *                             visualización de los elementos en el formulario.
     *
     */
    public function ajaxGetPlantillaComisionistaAction()
    {
        $objResponse    = new JsonResponse();
        $arrayRespuesta = array('strMensaje'               => 'SIN_PLANTILLA', 
                                'strPlantillaComisionista' => '<div class="info-success" style="padding:5px!important; margin: 15px 0px!important;">
                                                                   El producto no requiere plantilla de comisionista
                                                               </div>',
                                'strCombosValidar'         => '');
        
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $intIdProducto          = $objRequest->request->get("intIdProducto") ? $objRequest->request->get("intIdProducto") : 0;
        $serviceUtil            = $this->get('schema.Util');
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $serviceJefesComercial  = $this->get('administracion.JefesComercial');
        $strUsuario             = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $intIdDepartamento      = $objSession->get('idDepartamento');
        $strCaracteristicaCargo = ( $strPrefijoEmpresa == "TN" ) ? 'CARGO_GRUPO_ROLES_PERSONAL' : 'CARGO';
        $strHtml                = "";
        $strCombosValidar       = "";
        $strMensajeError        = "";
        $strTipoPersonal        = 'Otros';
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsuario);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        try
        {
            if( !empty($intIdProducto) && !empty($strCodEmpresa) )
            {
                $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($intIdProducto);
                
                if( is_object($objAdmiProducto) )
                {
                    $strRequierePlantilla = $objAdmiProducto->getRequiereComisionar() ? $objAdmiProducto->getRequiereComisionar() : 'NO';

                    if( $strRequierePlantilla == "SI" )
                    {
                        $arrayParametros                          = array();
                        $arrayParametros['usuario']               = $intIdPersonaEmpresaRol;
                        $arrayParametros['empresa']               = $strCodEmpresa;
                        $arrayParametros['estadoActivo']          = 'Activo';
                        $arrayParametros['caracteristicaCargo']   = $strCaracteristicaCargo;
                        $arrayParametros['departamento']          = $intIdDepartamento;
                        $arrayParametros['nombreArea']            = 'Comercial';
                        $arrayParametros['strTipoRol']            = array('Empleado', 'Personal Externo');
                        
                        
                        /**
                         * BLOQUE QUE BUSCA LOS ROLES NO PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
                         */
                        $arrayRolesNoIncluidos = array();
                        $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                                        'strValorRetornar'  => 'descripcion',
                                                        'strNombreProceso'  => 'JEFES',
                                                        'strNombreModulo'   => 'COMERCIAL',
                                                        'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                        'strUsrCreacion'    => $strUsuario,
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
                         * SE VALIDA PARA LA EMPRESA TN QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 
                         * 'GRUPO_DEPARTAMENTOS'
                         */
                        if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
                        {
                            $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                            $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                            $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonaEmpresaRol;
                            $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                                  'strValorRetornar'  => 'valor1',
                                                                  'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                                  'strNombreModulo'   => 'COMERCIAL',
                                                                  'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                                  'strValor2Detalle'  => 'COMERCIAL',
                                                                  'strUsrCreacion'    => $strUsuario,
                                                                  'strIpCreacion'     => $strIpCreacion);

                            $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                            if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                            {
                                $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                            }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                        }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
            
                        $arrayPlantillaProductos = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                               ->getResultadoComisionPlantilla( array('intIdProducto' => $intIdProducto,
                                                                                                      'strCodEmpresa' => $strCodEmpresa) );
                        
                        if( isset($arrayPlantillaProductos['objRegistros']) && !empty($arrayPlantillaProductos['objRegistros']) )
                        {
                            foreach($arrayPlantillaProductos['objRegistros'] as $arrayItem)
                            {
                                if( isset($arrayItem['valor3']) && !empty($arrayItem['valor3']) )
                                {
                                    $strMarcaCampoRequerido = "";
                                    $strFuncionOnChange     = "";
                                    $strCampoRequerido      = "";
                                    $intIdRelacionCombo     = 0;
                                    $intIdComisionDet       = ( isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']) )
                                                              ? $arrayItem['idComisionDet'] : 0;

                                    if( $intIdComisionDet > 0 )
                                    {
                                        /**
                                         * Bloque que verifica si existe una relación entre los combos a selecionar en la plantilla
                                         */
                                        $arrayParametroRelacionCombos = array('strCodEmpresa'     => $strCodEmpresa,
                                                                              'strValorRetornar'  => 'valor1',
                                                                              'strNombreProceso'  => 'SERVICIOS',
                                                                              'strNombreModulo'   => 'COMERCIAL',
                                                                              'strNombreCabecera' => 'RELACION_GRUPO_ROLES_PERSONAL',
                                                                              'strValor2Detalle'  => $arrayItem['valor3'],
                                                                              'strValor3Detalle'  => 'LABEL',
                                                                              'strUsrCreacion'    => $strUsuario,
                                                                              'strIpCreacion'     => $strIpCreacion);

                                        $arrayResultados = $serviceUtilidades->getDetallesParametrizables($arrayParametroRelacionCombos);

                                        if( isset($arrayResultados['resultado']) && !empty($arrayResultados['resultado']) )
                                        {
                                            $intIdRelacionCombo = $arrayResultados['resultado'];

                                            $objAdmiComisionDet = $emComercial->getRepository('schemaBundle:AdmiComisionDet')
                                                                              ->findOneById($intIdComisionDet);

                                            if( !is_object($objAdmiComisionDet) )
                                            {
                                                throw new \Exception('No se encontró detalle de la plantilla de comisionistas');
                                            }//( !is_object($objAdmiComisionDet) )
                                            else
                                            {
                                                $objAdmiComisionCab = $objAdmiComisionDet->getComisionId();

                                                if( is_object($objAdmiComisionCab) )
                                                {
                                                    $objAdmiComisionRelacion = $emComercial->getRepository('schemaBundle:AdmiComisionDet')
                                                                                           ->findOneBy( array('comisionId'     => $objAdmiComisionCab,
                                                                                                              'parametroDetId' => $intIdRelacionCombo,
                                                                                                              'estado'         => 'Activo') );
                                                    if( is_object($objAdmiComisionRelacion) )
                                                    {
                                                        $intIdComisionDetRelacion = $objAdmiComisionRelacion->getId();

                                                        if( $intIdComisionDetRelacion > 0 )
                                                        {
                                                            $strFuncionOnChange = ' onchange = "agregarLabel(\''.$intIdComisionDet.'\', \''.
                                                                                  $intIdComisionDetRelacion.'\');" ';
                                                        }//( $intIdComisionDetRelacion > 0 )
                                                    }//( is_object($objAdmiComisionRelacion) )
                                                }//( is_object($objAdmiComisionCab) )
                                            }//( !is_object($objAdmiComisionDet) )
                                        }//( isset($arrayResultados['resultado']) && !empty($arrayResultados['resultado']) )


                                        if( isset($arrayItem['comisionVenta']) && !empty($arrayItem['comisionVenta']) )
                                        {
                                            $floatComisionVenta = round(floatval($arrayItem['comisionVenta']), 2);

                                            if( $floatComisionVenta > 0 )
                                            {
                                                $strMarcaCampoRequerido = "*";
                                                $strCampoRequerido      = " required='required' ";
                                            }//( $floatComisionVenta > 0 )
                                        }//( isset($arrayItem['comisionVenta']) && !empty($arrayItem['comisionVenta']) )

                                        if( $arrayItem['valor3'] == "GERENTE_PRODUCTO" )
                                        {
                                            $strLabelGerenteProducto = ( isset($arrayItem['descripcion']) && !empty($arrayItem['descripcion']) )
                                                                       ? $arrayItem['descripcion'] : 'Gerente de Producto';
                                            $strGrupoProducto        = $objAdmiProducto->getGrupo() ? $objAdmiProducto->getGrupo() : '';

                                            $arrayParametrosGerenteProducto                         = $arrayParametros;
                                            $arrayParametrosGerenteProducto['strAsignadosProducto'] = $strGrupoProducto;

                                            $arrayGerenteProducto = $serviceJefesComercial->getListadoEmpleados( $arrayParametrosGerenteProducto );

                                            if( isset($arrayGerenteProducto['usuarios']) && !empty($arrayGerenteProducto['usuarios']) )
                                            {
                                                if( !empty($strCombosValidar) )
                                                {
                                                    $strCombosValidar .= '|';
                                                }

                                                $strCombosValidar .= $intIdComisionDet;

                                                $strHtml .= '<div style="clear:both;" class="content-comisionistas">
                                                                <div style="float:left; width: 217px;">
                                                                    <label>'.$strMarcaCampoRequerido.$strLabelGerenteProducto.'</label>
                                                                </div>
                                                                <div style="float:left; width: 300px;">
                                                                    <select id="cmb'.$intIdComisionDet.'" name="cmb'.$intIdComisionDet.'" '.
                                                                            $strCampoRequerido.$strFuncionOnChange.'>
                                                                        <option value="0">Seleccione</option>';

                                                foreach( $arrayGerenteProducto['usuarios'] as $arrayUsuario )
                                                {
                                                    if( isset($arrayUsuario['intIdPersonaEmpresaRol']) && !empty($arrayUsuario['intIdPersonaEmpresaRol'])
                                                        && isset($arrayUsuario['strEmpleado']) && !empty($arrayUsuario['strEmpleado']) )
                                                    {
                                                        $strHtml .= '<option value="'.$arrayUsuario['intIdPersonaEmpresaRol'].'">'.
                                                                    $arrayUsuario['strEmpleado'].'</option>';
                                                    }//( isset($arrayUsuario['intIdPersonaEmpresaRol'])...
                                                }//foreach( $arrayGerenteProducto['usuarios'] as $arrayUsuario )

                                                $strHtml .= '       </select>
                                                                </div>
                                                             </div>';
                                            }
                                            else
                                            {
                                                $strMensajeError = 'No se encontró Gerentes de Producto para el producto seleccionado.';

                                                throw new \Exception( $strMensajeError.' ('.$intIdProducto.')');
                                            }//( isset($arrayGerenteProducto['usuarios']) && !empty($arrayGerenteProducto['usuarios']) )
                                        }//( isset($arrayItem['VALOR3']) && $arrayItem['VALOR3'] == "GERENTE_PRODUCTO" )
                                        elseif( $arrayItem['valor3'] == "SUBGERENTE" )
                                        {
                                            if( !empty($strCombosValidar) )
                                            {
                                                $strCombosValidar .= '|';
                                            }

                                            $strCombosValidar .= $intIdComisionDet;

                                            $strLabelSubgerente = ( isset($arrayItem['descripcion']) && !empty($arrayItem['descripcion']) )
                                                                  ? $arrayItem['descripcion'] : 'Subgerente';

                                            $strHtml .= '<div style="clear:both;" class="content-comisionistas">
                                                            <div style="float:left; width: 217px;">
                                                                <label>'.$strMarcaCampoRequerido.$strLabelSubgerente.'</label>
                                                            </div>
                                                            <div style="float:left; width: 300px;">
                                                                <input type="hidden" id="cmb'.$intIdComisionDet.'" name="cmb'.$intIdComisionDet.'" />
                                                                <input type="text" id="str'.$intIdComisionDet.'" name="str'.$intIdComisionDet.'" '.
                                                                'readonly="true" />
                                                            </div>
                                                         </div>';
                                        }//( isset($arrayItem['VALOR3']) && $arrayItem['VALOR3'] == "SUBGERENTE" )
                                        else
                                        {
                                            $arrayParametrosOtros = $arrayParametros;

                                            $intIdCargo = ( isset($arrayItem['idParametroDet']) && !empty($arrayItem['idParametroDet']) )
                                                          ? $arrayItem['idParametroDet'] : 0;

                                            if( !empty($intIdCargo) )
                                            {
                                                $strLabelPersonal = ( isset($arrayItem['descripcion']) && !empty($arrayItem['descripcion']) )
                                                                     ? $arrayItem['descripcion'] : ucwords(strtolower($arrayItem['valor3']));

                                                $arrayParametrosOtros['criterios']['cargo'] = $intIdCargo;

                                                $arrayPersonal = $serviceJefesComercial->getListadoEmpleados( $arrayParametrosOtros );

                                                if( isset($arrayPersonal['usuarios']) && !empty($arrayPersonal['usuarios']) )
                                                {
                                                    if( !empty($strCombosValidar) )
                                                    {
                                                        $strCombosValidar .= '|';
                                                    }

                                                    $strCombosValidar .= $intIdComisionDet;

                                                    $strHtml .= '<div style="clear:both;" class="content-comisionistas">
                                                                    <div style="float:left; width: 217px;">
                                                                        <label>'.$strMarcaCampoRequerido.$strLabelPersonal.'</label>
                                                                    </div>
                                                                    <div style="float:left; width: 300px;">';

                                                    if( $arrayItem['valor3'] == "VENDEDOR" )
                                                    {
                                                        $strHtml .= '<input type="hidden" id="inputVendedor'.$intIdComisionDet.'" name="inputVendedor'.
                                                                    $intIdComisionDet.'" value="S" />';
                                                    }//( $arrayItem['valor3'] == "VENDEDOR" )

                                                    $strHtml .= '<select id="cmb'.$intIdComisionDet.'" name="cmb'.$intIdComisionDet.'" '.
                                                                         $strCampoRequerido.$strFuncionOnChange.'>
                                                                    <option value="0">Seleccione</option>';

                                                    foreach( $arrayPersonal['usuarios'] as $arrayUsuario )
                                                    {
                                                        if( isset($arrayUsuario['intIdPersonaEmpresaRol']) 
                                                            && !empty($arrayUsuario['intIdPersonaEmpresaRol']) && isset($arrayUsuario['strEmpleado'])
                                                            && !empty($arrayUsuario['strEmpleado']) )
                                                        {
                                                            $strHtml .= '<option value="'.$arrayUsuario['intIdPersonaEmpresaRol'].'">'.
                                                                        $arrayUsuario['strEmpleado'].'</option>';
                                                        }//( isset($arrayUsuario['intIdPersonaEmpresaRol'])...
                                                    }//foreach( $arrayGerenteProducto['usuarios'] as $arrayUsuario )

                                                    $strHtml .= '       </select>
                                                                    </div>
                                                                 </div>';
                                                }
                                                else
                                                {
                                                    $strMensajeError = "No se encontró personal para el cargo de ".$strLabelPersonal;

                                                    throw new \Exception( $strMensajeError );
                                                }//( isset($arrayPersonal['usuarios']) && !empty($arrayPersonal['usuarios']) )
                                            }
                                            else
                                            {
                                                $strMensajeError = 'No se ha encontrado cargo para el personal a buscar. ('.$arrayItem['valor3'].')';

                                                throw new \Exception( $strMensajeError );
                                            }//( empty($intIdCargo) )
                                        }//else
                                    }//( $intIdComisionDet > 0 )
                                }//( isset($arrayItem['valor3']) && !empty($arrayItem['valor3']) )
                            }//foreach($arrayPlantillaProductos['objRegistros'] as $arrayItem)
                            
                            $arrayRespuesta['strMensaje']               = 'OK';
                            $arrayRespuesta['strCombosValidar']         = $strCombosValidar;
                            $arrayRespuesta['strPlantillaComisionista'] = $strHtml;
                        }//( isset($arrayPlantillaProductos['objRegistros']) && !empty($arrayPlantillaProductos['objRegistros']) )
                    }//( $strRequierePlantilla == "SI" )
                }
                else
                {
                    $strMensajeError = 'Hubo un problema al obtener la información del producto.';
                    
                    throw new \Exception( $strMensajeError );
                }//( is_object($admiProducto) )
            }
            else
            {
                $strMensajeError = 'No se han enviado los parámetros correspondientes para obtener la plantilla de comisionistas.';
                
                throw new \Exception( $strMensajeError.' Producto(' + $intIdProducto + '), CodEmpresa(' + $strCodEmpresa + ').');
            }//( !empty($intIdProducto) && !empty($strCodEmpresa) )
        }
        catch( \Exception $e )
        {
            $arrayRespuesta['strMensaje']               = 'ERROR';
            $arrayRespuesta['strPlantillaComisionista'] = '<div class="info-error" style="padding:5px!important; margin: 15px 0px!important;">
                                                               No se pudo obtener la plantilla para ingresar al personal que debe comisionar. '.
                                                               $strMensajeError.
                                                          '</div>';
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoServicioController.ajaxGetPlantillaComisionistaAction', 
                                       'Error al obtener la plantilla de comisionistas. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;                                                                                                                  
    }
    
    
    /**     
     * Documentación para el método 'ajaxGetRelacionPersonalAction'.
     *
     * Método utilizado para el nombre y el id de la persona a cargo del personal a buscar
     *     
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-04-2017
     */
    public function ajaxGetRelacionPersonalAction()
    {
        $objResponse    = new JsonResponse();
        $arrayRespuesta = array('strMensaje'     => 'Error al obtener la información del personal seleccionado.', 
                                'strNombreLabel' => '', 
                                'strComboLabel'  => '');
        
        $objRequest                = $this->getRequest();
        $objSession                = $objRequest->getSession();
        $emComercial               = $this->get('doctrine')->getManager('telconet');
        $intIdPersonalSeleccionado = $objRequest->request->get("intIdPersonalSeleccionado") ? $objRequest->request->get("intIdPersonalSeleccionado")
                                     : 0;
        $serviceUtil               = $this->get('schema.Util');
        $strUsuario                = $objSession->get('user');
        $strIpCreacion             = $objRequest->getClientIp();
        
        try
        {
            if( !empty($intIdPersonalSeleccionado) )
            {
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findOneById($intIdPersonalSeleccionado);
                
                if( is_object($objInfoPersonaEmpresaRol) )
                {
                    $intIdRelacionPersonal = $objInfoPersonaEmpresaRol->getReportaPersonaEmpresaRolId();
                    
                    if( $intIdRelacionPersonal > 0 )
                    {
                        $objInfoReportaPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                       ->findOneById($intIdRelacionPersonal);
                        
                        if( is_object($objInfoReportaPersonaEmpresaRol) )
                        {
                            $objInfoPersonaReporta = $objInfoReportaPersonaEmpresaRol->getPersonaId();
                            
                            if( is_object($objInfoPersonaReporta) )
                            {
                                $strNombres   = $objInfoPersonaReporta->getNombres();
                                $strApellidos = $objInfoPersonaReporta->getApellidos();

                                if( !empty($strNombres) || !empty($strApellidos) )
                                {
                                    $arrayRespuesta['strNombreLabel'] = trim( ucwords(strtolower(trim($strNombres))).' '.
                                                                              ucwords(strtolower(trim($strApellidos))) );
                                    $arrayRespuesta['strComboLabel']  = $intIdRelacionPersonal;
                                    $arrayRespuesta['strMensaje']     = "OK";
                                }//( !empty($strNombres) || !empty($strApellidos) )
                            }//( is_object($objInfoPersonaReporta) )
                        }
                        else
                        {
                            $arrayRespuesta['strMensaje'] = "No se encontró la información correspondiente del jefe relacionado al personal ".
                                                            "seleccionado.";
                    
                            throw new \Exception($arrayRespuesta['strMensaje']);
                        }//( is_object($objInfoReportaPersonaEmpresaRol) )
                    }
                    else
                    {
                        $arrayRespuesta['strMensaje'] = "No existe relación de jefe para el personal seleccionado.";

                        throw new \Exception($arrayRespuesta['strMensaje']);
                    }// $intIdRelacionPersonal > 0 )
                }
                else
                {
                    $arrayRespuesta['strMensaje'] = "No se encontró la información correspondiente del personal seleccionado.";
                    
                    throw new \Exception($arrayRespuesta['strMensaje']);
                }//( is_object($objInfoPersonaEmpresaRol) )
            }
            else
            {
                $arrayRespuesta['strMensaje'] = 'No se han enviado los parámetros correspondientes para obtener la información del personal '.
                                                'seleccionado.';
                
                throw new \Exception($arrayRespuesta['strMensaje'].' ID('+ $intIdPersonalSeleccionado + ')');
            }//( !empty($intIdPersonalSeleccionado) )
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoServicioController.ajaxGetRelacionPersonalAction', 
                                       'Error al obtener la información del personal seleccionado. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;                                                                                                                  
    }
    
    /**
     * Metodo encargado de obtener informacion de grupo, subgrupo de productos y los respectivos productos que se relacionan con 
     * la informacion anterior obtenida
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 21-08-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - 01-03-2018 - Se obtiene tipo de sub soluciones configuradas por producto
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 08-02-2021 - Se agrega lógica para retornar el listado de propuestas de acuerdo al cliente en sesión.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetGrupoSubgrupoProductosAction()
    {
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $emComercial = $this->get('doctrine')->getManager('telconet');
        $emGeneral   = $this->get('doctrine')->getManager('telconet_general');
        
        $arrayReferencias                 = array();
        $arrayParametros                  = array();
        $arraySubTipoSolucion             = array();
        $arrayParametros['strCodEmpresa'] = $objSession->get('idEmpresa');
        $arrayParametros['strTipo']       = $objRequest->get('tipo');
        $arrayParametros['strGrupo']      = $objRequest->get('grupo');
        $arrayParametros['strSubGrupo']   = $objRequest->get('subgrupo');
        $intIdPropuesta                   = $objRequest->get('intIdPropuesta') ? $objRequest->get('intIdPropuesta'):"";
        $arrayClienteSesion               = $objSession->get('cliente')        ? $objSession->get('cliente'):"";
        $strPrefijoEmpresa                = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $strCodEmpresa                    = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
        $arrayGrupoDC                     = array();
        $serviceTelcoCrm                  = $this->get('comercial.ComercialCrm');
        if((!empty($strPrefijoEmpresa)  && $strPrefijoEmpresa == "TN") && 
           (!empty($arrayClienteSesion) && is_array($arrayClienteSesion)) && 
           (!empty($intIdPropuesta)     && $intIdPropuesta != null))
        {
            $arrayParametrosCRM   = array("strRuc"             => $arrayClienteSesion["identificacion"],
                                          "strPrefijoEmpresa"  => $strPrefijoEmpresa,
                                          "strCodEmpresa"      => $strCodEmpresa,
                                          "intIdPropuesta"     => $intIdPropuesta,
                                          "strBandera"         => "DETALLE");
            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCRM,
                                          "strOp"              => 'getPropuesta',
                                          "strFuncion"         => 'procesar');
            $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
            if(isset($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"]))
            {
                foreach($arrayRespuestaWSCrm["resultado"] as $arrayItem)
                {
                    $arrayDescProductoDC[] = strtolower($arrayItem->Nombre_producto);
                    $arrayLineaNegocioDC[] = strtolower($arrayItem->Categoria_producto);
                }
            }
            $arrayParametros["arrayDescProductoDC"] = $arrayDescProductoDC;
            $arrayParametros["arrayLineaNegocioDC"] = $arrayLineaNegocioDC;
        }
        $arrayResultado = $emComercial->getRepository("schemaBundle:InfoServicio")
                                      ->getArrayGrupoSubgrupoProductos($arrayParametros);
        
        if($arrayParametros['strTipo'] == 'SUBGRUPO')
        {
            $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get("GRUPO PRODUCTOS CON SUB TIPO SOLUCION", 
                                                  "COMERCIAL", 
                                                  "", 
                                                  $objRequest->get('grupo'), 
                                                  "", 
                                                  "", 
                                                  "",
                                                  "",
                                                  "",
                                                  $objSession->get('idEmpresa')
                                                );
            
            if(!empty($arrayParametrosDet))
            {
                foreach($arrayParametrosDet as $arrayValue)
                {
                    $arraySubTipoSolucion[] = array('subSolucion' => $arrayValue['valor1'],
                                                    'tipo'        => $arrayValue['valor2']?$arrayValue['valor2']:'N');
                }
            }
        }        
        if($arrayParametros['strTipo'] == 'PRODUCTO')//Se obtiene de existir los productos referenciales para crear un grupo de servicios
        {
            $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get("GRUPO DE SERVICIOS CON PRODUCTO REFERENCIAL", 
                                                  "TECNICO", 
                                                  "", 
                                                  $objRequest->get('subgrupo'), 
                                                  "", 
                                                  "", 
                                                  "",
                                                  "",
                                                  "",
                                                  $objSession->get('idEmpresa')
                                                );
            if(!empty($arrayParametrosDet))
            {
                foreach($arrayParametrosDet as $arrayValue)
                {
                    $arrayReferencias[] = array('idProducto'          => $arrayValue['valor1'],
                                                'descripcionProducto' => $arrayValue['valor2'],
                                                'grupo'               => $arrayValue['valor3'],
                                                'subgrupo'            => $arrayValue['valor4'],
                                                'tipo'                => 'P'
                                               );
                }
            }
        }
        
        $arrayRespuesta = array('arrayRespuestaGenerica'   => $arrayResultado,
                                'arrayProductosReferencia' => $arrayReferencias,
                                'arraySubSolucion'         => $arraySubTipoSolucion
                               );
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de obtener el detalle de productos relacionados a una solucion ( tipo Solucion , descripcion, precios, estados )
     * Para efecto de edicion de la solución
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 12-03-2018
     * 
     * @author José Alava <jialava@telconet.ec>
     * @version 1.1 20-06-2019  
     * Se cambia lógica de creación de MVS a un Service
     * Extrae todas las maquinas virtuales que existen en esa solución, si es pool de recurso,
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 16-17-2020 - Se obtiene el detalle del punto desde las nuevas estructuras de solución.
     *
     * @return JsonResponse
     */
    public function ajaxGetDetalleProductosPorSolucionAction()
    {
        $objResponse                = new JsonResponse();
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $serviceInfoServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $serviceInfoSolucion        = $this->get('comercial.InfoSolucion');
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strIpUsuario               = $objRequest->getClientIp();
        $strUsuario                 = $objSession->get('user');
        $arrayPuntoCliente          = $objSession->get('ptoCliente');
        $intNumeroSolucion          = $objRequest->get('numeroSolucion');
        $arrayResultado             = array();
        $arrayMaquinasVirtuales     = array();

        $intIdPunto   = !empty($arrayPuntoCliente['id']) ? $arrayPuntoCliente['id'] : null;
        $arrayRequest = array('numeroSolucion' =>  $intNumeroSolucion,
                              'idPunto'        =>  $intIdPunto,
                              'estado'         => 'Activo');

        $arrayResponse = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $strUsuario,
                                                              'strIp'        =>  $strIpUsuario,
                                                              'strOpcion'    => 'soluciondc',
                                                              'strEndPoint'  => 'listarDetalleSolucion',
                                                              'arrayRequest' =>  $arrayRequest));

        if ($arrayResponse['status'] && !empty($arrayResponse['data']))
        {
            $arrayResultado = $arrayResponse['data'];
        }

        foreach ($arrayResultado as $arrayDatos)
        {
            $objProductoSolucion = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($arrayDatos['idProducto']);

            if (is_object($objProductoSolucion))
            {
                $boolEsPoolRecursos = $serviceInfoServicioTecnico->isContieneCaracteristica($objProductoSolucion,'ES_POOL_RECURSOS');

                if ($boolEsPoolRecursos)
                {
                   $intIdServicio    = $arrayDatos['idServicio'];
                   $arrayParametros  = array('intIdServicio' => $intIdServicio);
                   $arrayVmsServicio = $serviceInfoServicioTecnico->getInformacionGeneralHosting($arrayParametros);
                   $arrayVmsServicio = array('idServicio' => $intIdServicio,'maquinasVirtuales' => $arrayVmsServicio);
                   array_push($arrayMaquinasVirtuales, $arrayVmsServicio);
                }
            }
        }

        $arrayDatos = array('arrayMaquinasVirtualesPorPool' => $arrayMaquinasVirtuales,
                            'arrayResultado'                => $arrayResultado);

        $objResponse->setData($arrayDatos);
        return $objResponse;
    }
    
    /**
     * Metodo encargado de guardar la informacion de la solucion ( multiple producto ), este método es sólo utilizado
     * para creación de productos para la empresa Telconet cuando se requieras crear grupo de productos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 28-08-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - 15-03-2018 - Se edita metodo para enviar parametros para edicion de soluciones ya creadas
     * 
     * @author José Álava <jialava@telconet.ec>
     * @version 1.2 - 05-06-2019 - Se edita método para enviar información de las máquinas virtuales creadas
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 - 24-06-2020 - Se agrega en la respuesta el número de solución creada.
     *                           - Se agrega la llamada al web-service encargado de obtener las soluciones del punto.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGuardarSolucionAction()
    {
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $serviceInfoSolucion = $this->get('comercial.InfoSolucion');
        $strStatus           = 'OK';
        $strMensaje          = 'OK';
        $arrayInfo           = array();
        $boolErrorWs         = false;

        $strCodEmpresa     = $objSession->get('idEmpresa');
        $intIdOficina      = $objSession->get('idOficina');
        $strUsrCreacion    = $objSession->get('user');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $strClientIp       = $objRequest->getClientIp();

        //Obteniendo datos del request
        $objJsonData       = $objRequest->get('data');
        $strTipoSolucion   = $objRequest->get('tipoSolucion');
        $strNombreSolucion = $objRequest->get('nombreSolucion');
        $intIdPunto        = $objRequest->get('idPunto');
        $strTipoOrden      = $objRequest->get('tipoOrden');
        $intNumeroSolucion = $objRequest->get('numeroSolucion');
        $objJsonMaquinas   = $objRequest->get('maquinasVirtuales');
        $arrayServicios    = json_decode($objJsonData, true);
        $arrayMaquinasVirt = empty($objJsonMaquinas)?array():json_decode($objJsonMaquinas);

        $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
        $objRol   = null;

        if (is_object($objPunto))
        {
            $objRol = $emComercial->getRepository('schemaBundle:AdmiRol')
                    ->find($objPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
        }

        $boolEsTipoSolucion    = false;
        $boolEsEdicionSolucion = false;
        
        if($strTipoSolucion=='S')
        {
            $boolEsTipoSolucion = true;
        }
        
        if(!empty($intNumeroSolucion) && $intNumeroSolucion> 0)
        {
            $boolEsEdicionSolucion = true;
        }
        
        try
        {
            /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
            $serviceInfoServicio = $this->get('comercial.InfoServicio');
            $arrayParamsServicio = array(   "codEmpresa"            => $strCodEmpresa,
                                            "idOficina"             => $intIdOficina,
                                            "entityPunto"           => $objPunto,
                                            "entityRol"             => $objRol,
                                            "usrCreacion"           => $strUsrCreacion,
                                            "clientIp"              => $strClientIp,
                                            "tipoOrden"             => $strTipoOrden,
                                            "ultimaMillaId"         => null,
                                            "servicios"             => $arrayServicios,
                                            "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                            "session"               => $objSession,
                                            "intIdSolFlujoPP"       => $objRequest->get('idSolFlujoPrePlanificacion') 
                                                                       ? $objRequest->get('idSolFlujoPrePlanificacion') : 0,
                                            "boolEsTipoSolucion"    => $boolEsTipoSolucion,
                                            "strNombreSolucion"     => $strNombreSolucion,
                                            "numeroSolucion"        => $intNumeroSolucion,
                                            "boolEdicionSolucion"   => $boolEsEdicionSolucion,
                                            "arrayMaquinasVirtuales"=> $arrayMaquinasVirt,
                                            "serviceElemento"       => $this->get('tecnico.InfoElemento'),
                                            "objRequest"            => $objRequest
                                    );
            $arrayRespuestaServicio = $serviceInfoServicio->crearServicio($arrayParamsServicio);

            if (isset($arrayRespuestaServicio['numeroSolucion']))
            {
                $intNumeroSolucion = $arrayRespuestaServicio['numeroSolucion'];
            }

            if($arrayRespuestaServicio['intIdServicio'] > 0 && $boolEsEdicionSolucion)
            {
                //Consultamos mediante el WS, las soluciones del punto.
                $arrayRequest  = array ('puntoId' => $intIdPunto,'estado' => 'Activo');
                $arrayResponse = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $strUsrCreacion,
                                                                      'strIp'        =>  $strClientIp,
                                                                      'strOpcion'    => 'soluciondc',
                                                                      'strEndPoint'  => 'listarSolucionesPorPunto',
                                                                      'arrayRequest' =>  $arrayRequest));

                if ($arrayResponse['status'] && !empty($arrayResponse['data']))
                {
                    $arrayInfo = $arrayResponse['data'];
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatus   = 'ERROR';
            $strMensaje  = '_ajaxGuardarSolucionAction:_Error en la creación del Grupo de Solución de Productos, favor notificar a Sistemas';

            if(strpos($e->getMessage(),'HOUSING')!==false || strpos($e->getMessage(),'HOSTING')!==false)
            {
                $strMensaje  = 'Debe agregar producto de <b>'.$e->getMessage().'</b> para poder guardar su Solución';
            }
            
            if(strpos($e->getMessage(),'MAQUINAS VIRTUALES')!==false)
            {
                $strMensaje  = $e->getMessage();
            }

            if (strpos($e->getMessage(),'Solucion : ') !== false)
            {
                $boolErrorWs = true;
                $strMensaje  = explode('Solucion : ',$e->getMessage())[1];
            }

            error_log($e->getMessage());
        }

        $objResponse = new JsonResponse();
        $objResponse->setData(array('status'         => $strStatus,
                                    'mensaje'        => $strMensaje,
                                    'boolErrorWs'    => $boolErrorWs,
                                    'arrayInfo'      => $arrayInfo,
                                    'numeroSolucion' => $intNumeroSolucion));
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de realizar la edición del nombre de la solucion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 16-07-2020 - Editamos el nombre de la solución en base a las nuevas estructuras.
     *
     * @return JsonResponse
     */
    public function ajaxEditarNombreSolucionAction()
    {
        $objResponse             = new JsonResponse();
        $objRequest              = $this->getRequest();
        $emComercial             = $this->get('doctrine')->getManager('telconet');
        $objSession              = $objRequest->getSession();
        $strUsrCreacion          = $objSession->get('user');
        $strIpCreacion           = $objRequest->getClientIp();
        $serviceUtil             = $this->get('schema.Util');
        $serviceInfoSolucion     = $this->get('comercial.InfoSolucion');
        $intIdPunto              = $objRequest->get('idPunto');
        $intNumeroSolucion       = $objRequest->get('numeroSolucion');
        $strActualNombreSolucion = $objRequest->get('actualNombreSolucion');
        $strNuevoNombreSolucion  = $objRequest->get('nuevoNombreSolucion');
        $arrayInfo               = array();
        $strStatus               = 'OK';
        $strMensaje              = 'OK';

        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Obtenemos la cabecera de la solución.
            $objInfoSolucionCab = $emComercial->getRepository("schemaBundle:InfoSolucionCab")
                    ->findOneBy(array('puntoId' => $intIdPunto,'numeroSolucion' => $intNumeroSolucion));

            if (is_object($objInfoSolucionCab))
            {
                //Actualizamos el nuevo nombre de la solución.
                $objInfoSolucionCab->setNombreSolucion($strNuevoNombreSolucion);
                $objInfoSolucionCab->setUsrUltMod($strUsrCreacion);
                $objInfoSolucionCab->setIpUltMod($strIpCreacion);
                $objInfoSolucionCab->setFecUltMod(new \DateTime('now'));
                $emComercial->persist($objInfoSolucionCab);
                $emComercial->flush();

                //Obtenemos todo los servicios de la solución.
                $arrayServicios = $emComercial->getRepository("schemaBundle:InfoServicio")
                        ->getArrayServiciosPorGrupoSolucion(array('intSecuencial' => $intNumeroSolucion,'intPuntoId' => $intIdPunto));

                foreach($arrayServicios as $objInfoServicio)
                {
                    $strObservacion = "Se actualizó el nombre de la Solución (".$intNumeroSolucion.") de : ".
                                       $strActualNombreSolucion.' a '.$strNuevoNombreSolucion;

                    //Registramos el historial en cada uno de los servicios.
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objInfoServicio);
                    $objServicioHistorial->setObservacion($strObservacion);
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                }
            }

            $emComercial->commit();

            //Consultamos mediante el WS, las soluciones del punto.
            $arrayRequest  = array ('puntoId' => $intIdPunto,'estado' => 'Activo');
            $arrayResponse = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $strUsrCreacion,
                                                                  'strIp'        =>  $strIpCreacion,
                                                                  'strOpcion'    => 'soluciondc',
                                                                  'strEndPoint'  => 'listarSolucionesPorPunto',
                                                                  'arrayRequest' =>  $arrayRequest));

            if ($arrayResponse['status'] && !empty($arrayResponse['data']))
            {
                $arrayInfo = $arrayResponse['data'];
            }
        }
        catch (\Exception $e) 
        {
            $strStatus   = 'ERROR';
            $strMensaje  = 'Error al editar el nombre de la solución';
                        
            $serviceUtil->insertError( 'Telcos+', 
                                        'ComercialBundle.InfoServicioController.ajaxEditarNombreSolucionAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpCreacion );
            
            if($emComercial->getConnection()->isTransactionActive())
            {
               $emComercial->rollback();
            }
            
            $emComercial->close();
        }

        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje , 'arrayInfo' => $arrayInfo));
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de realizar la eliminacion de Servicios ligados a una Solucion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 16-03-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 30-07-2020 - Se obtiene las soluciones del punto mediante el web-service de solucionesdc.
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.2 - 01-11-2020 - Se agrega un parámetro de salida arrayServiciosLigados.
     *
     * @return JsonResponse
     */
    public function ajaxEliminarServiciosSolucionAction()
    {
        $objResponse         = new JsonResponse();
        $objRequest          = $this->getRequest();
        $intIdServicio       = $objRequest->get('idServicio');
        $intIdSolInfoTec     = $objRequest->get('idSolicitud')?$objRequest->get('idSolicitud'):'';
        $intIdPunto          = $objRequest->get('idPunto');
        $objSession          = $objRequest->getSession();
        $strUsrCreacion      = $objSession->get('user');
        $strIpCreacion       = $objRequest->getClientIp();
        $serviceServicio     = $this->get('comercial.InfoServicio');
        $serviceInfoSolucion = $this->get('comercial.InfoSolucion');
        $arrayInfo           = array();

        $arrayParametros                   = array();
        $arrayParametros['intIdServicio']  = $intIdServicio;
        $arrayParametros['intIdSolicitud'] = $intIdSolInfoTec;
        $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
        $arrayParametros['strIpCreacion']  = $strIpCreacion;
        $arrayParametros['objRequest']     = $objRequest;
        $arrayParametros['strObservacion'] = '';
        $arrayParametros['intIdMotivo']    = '';
        $arrayRespuesta = $serviceServicio->eliminarServicioPorSolucion($arrayParametros);

        if ($arrayRespuesta['status'] == 'OK')
        {
            //Consultamos mediante el WS, las soluciones del punto.
            $arrayRequest  = array ('puntoId' => $intIdPunto,'estado' => 'Activo');
            $arrayResponse = $serviceInfoSolucion->WsPostDc(array('strUser'      =>  $strUsrCreacion,
                                                                  'strIp'        =>  $strIpCreacion,
                                                                  'strOpcion'    => 'soluciondc',
                                                                  'strEndPoint'  => 'listarSolucionesPorPunto',
                                                                  'arrayRequest' =>  $arrayRequest));

            if ($arrayResponse['status'] && !empty($arrayResponse['data']))
            {
                $arrayInfo = $arrayResponse['data'];
            }
        }

        $objResponse->setData(array('status'                   => $arrayRespuesta['status'], 
                                    'mensaje'                  => $arrayRespuesta['mensaje'] , 
                                    'arrayServiciosEliminados' => $arrayRespuesta['arrayServiciosEliminados'],
                                    'arrayServiciosLigados'    => $arrayRespuesta['arrayServiciosLigados'],
                                    'arrayInfo'                => $arrayInfo));

        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de realizar la anulacion o rechazo de Servicios ligados a una Solucion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 20-03-2018
     * 
     * @return JsonResponse
     */
    public function ajaxAnularRechazarServiciosSolucionAction()
    {
        $objRequest        = $this->getRequest();
        $intIdServicio     = $objRequest->get('idServicio');
        $intIdSolicitud    = $objRequest->get('idSolicitud')?$objRequest->get('idSolicitud'):'';
        $intIdMotivo       = $objRequest->get('idMotivo');
        $strObservacion    = $objRequest->get('observacion');
        $strAccion         = $objRequest->get('accion');
        $strOrigen         = $objRequest->get('origen');
        $serviceServicio   = $this->get('comercial.InfoServicio');
        
        //Rechazo de servicios de solucion
        $arrayParametros                   = array();
        $arrayParametros['intIdServicio']  = $intIdServicio;
        $arrayParametros['intIdSolicitud'] = $intIdSolicitud;
        $arrayParametros['strUsrCreacion'] = $objRequest->getSession()->get('user');
        $arrayParametros['strIpCreacion']  = $objRequest->getClientIp();
        $arrayParametros['objRequest']     = $objRequest;
        $arrayParametros['intIdMotivo']    = $intIdMotivo;
        $arrayParametros['strObservacion'] = $strObservacion;
        $arrayParametros['strAccion']      = $strAccion;
        $arrayParametros['strOrigen']      = $strOrigen;
        $arrayRespuesta = $serviceServicio->anularRechazarServicioPorSolucion($arrayParametros);
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayRespuesta);
        
        return $objResponse;
    }

    /**
     * 
     * Metodo encargado de obtener la informacion basica y de caracteristicas ingresadas para un determinado servicio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 21-03-2018
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - 01-06-2020 - Obtenemos el id de las maquinas asociadas en el licenciamiento.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 21-07-2020 - Se modifica el método para obtener los datos de las nuevas estructuras de solución.
     *
     * @return JsonResponse
     */
    public function ajaxGetInformacionServicioSolucionAction()
    {
        $objResponse          = new JsonResponse();
        $objRequest           = $this->getRequest();
        $intIdServicio        = $objRequest->get('idServicio');
        $emComercial          = $this->get('doctrine')->getManager('telconet');
        $serviceGeneral       = $this->get('tecnico.InfoServicioTecnico');
        $serviceInfoSolucion  = $this->get('comercial.InfoSolucion');
        $arrayCaracteristicas = array();

        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);

        if (is_object($objServicio))
        {
            //Obtenemos la información basica de la solución.
            $arrayInfoBasica = $emComercial->getRepository("schemaBundle:InfoServicio")
                    ->getArrayInformacionServicioSolucion($intIdServicio);

            if (!empty($arrayInfoBasica))
            {
                $boolEsLicenciamiento = $serviceGeneral->isContieneCaracteristica(
                        $objServicio->getProductoId(),'ES_LICENCIAMIENTO_SO');

                $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                        ->findOneByServicioId($intIdServicio);
                $boolExisteRecurso = is_object($objInfoServicioRecursoCab);

                if ($boolEsLicenciamiento)
                {
                    $arrayCaracteristicas = $serviceInfoSolucion->getArrayCaracteristicasLicencias(
                            array('intIdServicio' => $intIdServicio));
                }
                else
                {
                    $arrayCaracteristicas = $emComercial->getRepository("schemaBundle:InfoServicio")
                            ->getArrayCaracteristicasPorServicioSolucion(array('existeRecurso' => $boolExisteRecurso,
                                                                               'intIdServicio' => $intIdServicio,
                                                                               'strEstado'     => 'Activo'));
                    $arrayCaracteristicas = $arrayCaracteristicas['data'];
                }
            }
        }
        $objResponse->setData(array('arrayInfoBasica' => $arrayInfoBasica, 'arrayCaracteristicas' => $arrayCaracteristicas));
        return $objResponse;
    }

    /**
     * 
     * Metodo encargado de realizar la edicion de Servicios creados que tengan un estado permitido para el proceso
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-03-2018
     * 
     * @author José Álava <jialava@telconet.ec>
     * @version 1.2 - 05-06-2019 - Se edita método para la edicion  de maquinas virtuales que contengan licencias 
     * editadas
     * 
     * @return JsonResponse
     * 
     * @author José Álava <jialava@telconet.ec>
     * @version 1.2 - 08-08-2019 - Se edita método para la edicion  de maquinas virtuales  y se añaden validaciones
     * editadas
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 08-08-2019 - Se edita el método para que los productos de Housing y Hosting sean editados y creados
     *                             en las nuevas estructuras de solución.
     *                           - Se añade el proceso de creación de maquinas virtuales.
     *
     * @return JsonResponse
     */
    public function ajaxEditarServicioSolucionAction()
    {
        $objRequest                 = $this->getRequest();
        $intIdServicio              = $objRequest->get('idServicio');
        $objJsonData                = $objRequest->get('data');
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $serviceTecnico             = $this->get('tecnico.InfoServicioTecnico');
        $serviceServicio            = $this->get('comercial.InfoServicio');
        $objSession                 = $objRequest->getSession();
        $strUsrCreacion             = $objSession->get('user');
        $strIpCreacion              = $objRequest->getClientIp();
        $serviceUtil                = $this->get('schema.Util');
        $serviceComercial           = $this->get('comercial.Comercial');
        $objResponse                = new JsonResponse();
        $arrayServicios             = json_decode($objJsonData, true)[0];
        $arrayLicenciasEliminadas   = json_decode($objRequest->get('arrayLicenciasEliminadas'));
        $arrayMaquinasVirtuales     = json_decode($objRequest->get('arrayMaquinasVirtuales'));
        $serviceInfoSolucion        = $this->get('comercial.InfoSolucion');
        $boolPoolRecursos           = false;

        //Variables de respuesta
        $strStatus  = 'OK';
        $strMensaje = 'Servicio Actualizado exitosamente.';

        $emComercial->getConnection()->beginTransaction();

        try
        {
            
            $strObservacionServicio  = 'Se actualizaron los siguientes valores del Servicio:';
            $strObservacionServicio .= '<br><table>';
            $objServicio             = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            
            if(is_object($objServicio))
            {
                $objProducto = $objServicio->getProductoId();

                //Informacion Anterior
                $arrayInfoBasica = $emComercial->getRepository("schemaBundle:InfoServicio")
                        ->getArrayInformacionServicioSolucion($intIdServicio)[0];

                //Consultamos si el servicio existe en la tabla de recurso.
                $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                        ->findOneByServicioId($intIdServicio);

                $boolExisteRecurso    = is_object($objInfoServicioRecursoCab);
                $boolEsLicenciamiento = $serviceTecnico->isContieneCaracteristica($objProducto,'ES_LICENCIAMIENTO_SO');
                $boolEsPoolRecursos   = $serviceTecnico->isContieneCaracteristica($objProducto,'ES_POOL_RECURSOS');

                //Caracteristicas Anterior del Servicio.
                if ($boolEsLicenciamiento)
                {
                    $arrayCaracteristicas = $serviceInfoSolucion
                            ->getArrayCaracteristicasLicencias(array('intIdServicio' => $intIdServicio));
                }
                else
                {
                    $arrayCaracteristicas = $emComercial->getRepository("schemaBundle:InfoServicio")
                            ->getArrayCaracteristicasPorServicioSolucion(array('existeRecurso' => $boolExisteRecurso,
                                                                               'intIdServicio' => $intIdServicio,
                                                                               'strEstado'     => 'Activo'));
                    $arrayCaracteristicas = $arrayCaracteristicas['data'];
                }

                //cantidad
                $intCantidadAnterior = $arrayInfoBasica['cantidad'];
                $intCantidadActual   = $arrayServicios['cantidad'];
            
                if($intCantidadAnterior != $intCantidadActual)
                {
                    $strObservacionServicio .= '  <tr>	  
                                                    <td><b>Cantidad Anterior:</b></td><td>&nbsp;&nbsp;</td><td>'.$intCantidadAnterior.'</td>
                                                  </tr>
                                                  <tr>
                                                    <td><b>Cantidad Nueva:</b></td><td>&nbsp;&nbsp;</td><td>'.$intCantidadActual.'</td>
                                                  </tr><tr><td>&nbsp;</td></tr>';
                    
                    $objServicio->setCantidad($intCantidadActual);
                    $emComercial->persist($objServicio);
                    $emComercial->flush();
                }

                //descripcion factura
                $strDescripcionAnterior = $arrayInfoBasica['descripcion'];
                $strDescripcionActual   = $arrayServicios['descripcion'];
                
                if($strDescripcionAnterior != $strDescripcionActual)
                {
                    $strObservacionServicio .= '  <tr>	  
                                                    <td><b>Descripción Anterior:</b></td><td>&nbsp;&nbsp;</td><td>'.$strDescripcionAnterior.'</td>
                                                  </tr>
                                                  <tr>
                                                    <td><b>Descripción Nueva:</b></td><td>&nbsp;&nbsp;</td><td>'.$strDescripcionActual.'</td>
                                                  </tr><tr><td>&nbsp;</td></tr>';
                    
                    $objServicio->setDescripcionPresentaFactura($strDescripcionActual);
                    $emComercial->persist($objServicio);
                    $emComercial->flush();
                }
                
                //frecuencia de facturacion
                $strFrecuenciaAnterior  = $arrayInfoBasica['frecuencia'];
                $strFrecuenciaActual    = $arrayServicios['frecuencia'];
                   
                if($strFrecuenciaAnterior != $strFrecuenciaActual)
                {
                    $arrayFrecuenciaAnterior =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne( 'FRECUENCIA_FACTURACION', 
                                                                    '', 
                                                                    '',
                                                                    '',
                                                                    $strFrecuenciaAnterior, 
                                                                    '',
                                                                    '',
                                                                    '', 
                                                                    '', 
                                                                    $objSession->get('idEmpresa')
                                                                  );
                    $strFrecuenciaAnterior = '';
                  
                    if(!empty($arrayFrecuenciaAnterior))
                    {
                        $strFrecuenciaAnterior = $arrayFrecuenciaAnterior['valor2'];
                    }
                    if(empty($strFrecuenciaActual))
                    {
                        $arrayFrecuenciaNueva    =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne( 'FRECUENCIA_FACTURACION', 
                                                                    '', 
                                                                    '',
                                                                    '',
                                                                    $strFrecuenciaActual, 
                                                                    '',
                                                                    '',
                                                                    '', 
                                                                    '', 
                                                                    $objSession->get('idEmpresa')
                                                                  );
                    }
   
              
                    
                    $strFrecuenciaNueva = '';
              
                    if(!empty($arrayFrecuenciaNueva))
                    {
                        $arrayFrecuenciaNueva = $arrayFrecuenciaNueva['valor2'];
                    }
                    
                    if(!empty($strFrecuenciaAnterior) && !empty($arrayFrecuenciaNueva))
                    {
                        $strObservacionServicio .= '  <tr>	  
                                                        <td><b>Frecuencia Anterior:</b></td><td>&nbsp;&nbsp;</td><td>'.$strFrecuenciaAnterior.'</td>
                                                      </tr>
                                                      <tr>
                                                        <td><b>Frecuencia Nueva:</b></td><td>&nbsp;&nbsp;</td><td>'.$arrayFrecuenciaNueva.'</td>
                                                      </tr><tr><td>&nbsp;</td></tr>';
                    }
                    
                    $objServicio->setFrecuenciaProducto($strFrecuenciaActual);
                    $emComercial->persist($objServicio);
                    $emComercial->flush();
                }
                  
                //UM
                if(!empty($arrayServicios['ultimaMilla']))
                {
                    $intUltimaMillaAnterior = $arrayInfoBasica['ultimaMilla'];
                    $intUltimaMillaNueva    = $arrayServicios['ultimaMilla'];
                    
                    if($intUltimaMillaAnterior != $intUltimaMillaNueva)
                    {
                        $objServicioTecnico = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($intIdServicio);
                        
                        if(is_object($objServicioTecnico))
                        {
                            $objAdmiTipoMedioAnterior = $emComercial->getRepository("schemaBundle:AdmiTipoMedio")->find($intUltimaMillaAnterior);
                            $objAdmiTipoMedioNueva    = $emComercial->getRepository("schemaBundle:AdmiTipoMedio")->find($intUltimaMillaNueva);
                            
                            if(is_object($objAdmiTipoMedioAnterior) && is_object($objAdmiTipoMedioNueva))
                            {
                                $strObservacionServicio .= '  <tr>	  
                                                                <td><b>Ultima Milla Anterior:</b></td><td>&nbsp;&nbsp;</td><td>'.
                                                                   $objAdmiTipoMedioAnterior->getNombreTipoMedio().'</td>
                                                                </tr>
                                                                <tr>
                                                                  <td><b>Ultima Milla Nueva:</b></td><td>&nbsp;&nbsp;</td><td>'.
                                                                   $objAdmiTipoMedioNueva->getNombreTipoMedio().'</td>
                                                                </tr><tr><td>&nbsp;</td></tr>';
                            }
                            $objServicioTecnico->setUltimaMillaId($intUltimaMillaNueva);
                            $emComercial->persist($objServicio);
                            $emComercial->flush();
                        }
                    }
                }                               
                 
                //Precios generales
                //Precio Instalacion
                $intPrecioInstalacionAnterior = $arrayInfoBasica['precioInstalacion'];
                $intPrecioInstalacionNuevo    = $arrayServicios['precio_instalacion'];
                
                if($intPrecioInstalacionAnterior != $intPrecioInstalacionNuevo)
                {
                    $strObservacionServicio .= '  <tr>	  
                                                    <td><b>Precio Instalación Anterior:</b></td><td>&nbsp;&nbsp;</td><td> $ '.
                                                       $intPrecioInstalacionAnterior.'</td>
                                                    </tr>
                                                    <tr>
                                                      <td><b>Precio Instalación Nuevo:</b></td><td>&nbsp;&nbsp;</td><td> $ '.
                                                       $intPrecioInstalacionNuevo.'</td>
                                                    </tr><tr><td>&nbsp;</td></tr>';
                    
                    $objServicio->setPrecioInstalacion($intPrecioInstalacionNuevo);
                    $emComercial->persist($objServicio);
                    $emComercial->flush();
                }
                
                
                
                //Precio
                $intPrecioAnterior = $arrayInfoBasica['precioFormula'];
                $intPrecioNuevo    = $arrayServicios['precio'];
                
                //PrecioVenta
                $intPrecioVentaAnterior = $arrayInfoBasica['precioVenta'];
                $intPrecioVentaNuevo    = $arrayServicios['precio_venta'];
                
                if($intPrecioAnterior != $intPrecioNuevo || $intPrecioVentaAnterior != $intPrecioVentaNuevo)
                {
                    $boolSolicitudDscto = false;
                    
                    $objServicio->setPrecioFormula($intPrecioNuevo);
                    
                    if($intPrecioVentaNuevo < $intPrecioNuevo)
                    {
                        $intPrecioSolicitado = $intPrecioNuevo - $intPrecioVentaNuevo; 
                        $objServicio->setPrecioVenta($intPrecioNuevo);
                        $boolSolicitudDscto  = true;
                    }
                    else
                    {
                        $objServicio->setPrecioVenta($intPrecioVentaNuevo);
                    }
                    
                    $emComercial->persist($objServicio);
                    $emComercial->flush();
                    
                    //Si no existe solicitud de descuento ( se edita precio pero existe idsolicitud esta sera anulada )
                    if(!empty($arrayInfoBasica['idSolicitud']) && !$boolSolicitudDscto)
                    {
                        $objDetalleSolicitudDscto = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                                ->find($arrayInfoBasica['idSolicitud']);
                        
                        if(is_object($objDetalleSolicitudDscto) && $objDetalleSolicitudDscto->getEstado() == 'Pendiente')
                        {
                            //anular solicitud anterior
                            $objDetalleSolicitudDscto->setEstado('Anulada');
                            $emComercial->persist($objDetalleSolicitudDscto);
                            $emComercial->flush();

                            $objInfoDetalleSolHist= new InfoDetalleSolHist();
                            $objInfoDetalleSolHist->setEstado('Anulada');
                            $objInfoDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitudDscto);
                            $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                            $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                            $objInfoDetalleSolHist->setMotivoId($objDetalleSolicitudDscto->getMotivoId());
                            $objInfoDetalleSolHist->setObservacion("Se anula solicitud de descuento por edición de Servicio");
                            $emComercial->persist($objInfoDetalleSolHist);
                            $emComercial->flush();

                            $intDescuentoAnterior = $arrayInfoBasica['descuento'];
                        }
                    }
                    
                    if($boolSolicitudDscto)
                    {
                        $intDescuentoAnterior  = 0;
                        $strDescripcionCarac   = 'DESCUENTO UNITARIO FACT';
                        $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCarac,
                                                                               'estado'                    => 'Activo'));
                        
                        $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                        ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD DESCUENTO', 'estado' => 'Activo'));
                        
                        //Se verifica si existe solicitud de descuento
                        if(!empty($arrayInfoBasica['idSolicitud']))
                        {
                            $objDetalleSolicitudDscto = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                                    ->find($arrayInfoBasica['idSolicitud']);
                            
                            if(is_object($objDetalleSolicitudDscto) && $objDetalleSolicitudDscto->getEstado() == 'Pendiente')
                            {
                                //anular solicitud anterior
                                $objDetalleSolicitudDscto->setEstado('Anulada');
                                $emComercial->persist($objDetalleSolicitudDscto);
                                $emComercial->flush();
                                
                                $objInfoDetalleSolHist= new InfoDetalleSolHist();
                                $objInfoDetalleSolHist->setEstado('Anulada');
                                $objInfoDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitudDscto);
                                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                                $objInfoDetalleSolHist->setMotivoId($objDetalleSolicitudDscto->getMotivoId());
                                $objInfoDetalleSolHist->setObservacion("Se anula solicitud de descuento por edición de Servicio");
                                $emComercial->persist($objInfoDetalleSolHist);
                                $emComercial->flush();
                                
                                $intDescuentoAnterior = $arrayInfoBasica['descuento'];
                            }
                        }
                      
                        $objAdmiMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                                     ->findOneBy(array('nombreMotivo' => 'Margen de Negociacion', 'estado' => 'Activo'));
                        
                        //Crear nueva solicitud de descuento
                        $objInfoDetalleSolicitud = new InfoDetalleSolicitud();
                        $objInfoDetalleSolicitud->setMotivoId($objAdmiMotivo->getId());
                        $objInfoDetalleSolicitud->setServicioId($objServicio);
                        $objInfoDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                        $objInfoDetalleSolicitud->setPrecioDescuento($intPrecioSolicitado);
                        $objInfoDetalleSolicitud->setObservacion("Solicitud de descuento creada por usr: ".$strUsrCreacion." TN");
                        $objInfoDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                        $objInfoDetalleSolicitud->setEstado('Pendiente');
                        $emComercial->persist($objInfoDetalleSolicitud);
                        $emComercial->flush();
                        
                        //Grabamos en la tabla de historial de la solicitud
                        $objInfoDetalleSolHist= new InfoDetalleSolHist();
                        $objInfoDetalleSolHist->setEstado('Pendiente');
                        $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                        $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                        $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                        $objInfoDetalleSolHist->setMotivoId($objAdmiMotivo->getId());
                        $objInfoDetalleSolHist->setObservacion("Solicitud de descuento creada por usr: ".$strUsrCreacion." TN");
                        $emComercial->persist($objInfoDetalleSolHist);
                        $emComercial->flush();
                        
                        if(is_object($objAdmiCaracteristica))
                        {                            
                            //Crea array para generar el objeto detalle solicitud caracteristica
                            $arrayRequestDetalleSolCaract = array();
                            $arrayRequestDetalleSolCaract['entityAdmiCaracteristica'] = $objAdmiCaracteristica;
                            $arrayRequestDetalleSolCaract['floatValor']               = round( $intPrecioSolicitado  , 2);
                            $arrayRequestDetalleSolCaract['entityDetalleSolicitud']   = $objInfoDetalleSolicitud;
                            $arrayRequestDetalleSolCaract['strEstado']                = 'Pendiente';
                            $arrayRequestDetalleSolCaract['strUsrCreacion']           = $strUsrCreacion;

                            //Crea el objeto InfoDetalleSolCaract
                            $objDetalleSolCaract = $serviceComercial->creaObjetoInfoDetalleSolCaract($arrayRequestDetalleSolCaract);
                            $emComercial->persist($objDetalleSolCaract);
                            $emComercial->flush(); 
                            
                            //historial
                            $strObservacionServicio .= '  <tr>	  
                                                            <td><b>Descuento Anterior:</b></td><td>&nbsp;&nbsp;</td><td> $ '.
                                                               $intDescuentoAnterior.'</td>
                                                            </tr>
                                                            <tr>
                                                              <td><b>Descuento Nuevo:</b></td><td>&nbsp;&nbsp;</td><td> $ '.
                                                               $intPrecioSolicitado.'</td>
                                                            </tr><tr><td>&nbsp;</td></tr>';
                        }
                    }
                }
                
                //Plantilla de comisionistas
                $arrayPlantillaComisionista = explode('|', $arrayServicios['strPlantillaComisionista']);
                
                if( !empty($arrayPlantillaComisionista) )
                {
                    foreach( $arrayPlantillaComisionista as $strPersonalSeleccionado )
                    {
                        if( !empty($strPersonalSeleccionado) )
                        {
                            $arrayPersonalSeleccionado = explode('---', $strPersonalSeleccionado);

                            if( isset($arrayPersonalSeleccionado[0]) && !empty($arrayPersonalSeleccionado[0]) 
                                && isset($arrayPersonalSeleccionado[1]) && !empty($arrayPersonalSeleccionado[1]) )
                            {
                                $intIdComisionDet              = $arrayPersonalSeleccionado[0];
                                $intIdPersonalSeleccionado     = $arrayPersonalSeleccionado[1];
                                $strNombrePersonalSeleccionado = "";
                                $strLoginPersonalSeleccionado  = "";
                                $floatComisionVenta            = 0;

                                if(is_numeric($intIdComisionDet) && is_numeric($intIdPersonalSeleccionado)) 
                                {
                                    $objAdmiComisionDet = $emComercial->getRepository('schemaBundle:AdmiComisionDet')
                                                                      ->findOneById($intIdComisionDet);
                                    
                                    if(is_object($objAdmiComisionDet))
                                    {
                                        $intIdParametroDet  = $objAdmiComisionDet->getParametroDetId();
                                        
                                        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                         ->findOneById($intIdParametroDet);
                                        
                                        if(is_object($objAdmiParametroDet))
                                        {
                                            $strValor3Parametro = $objAdmiParametroDet->getValor3();
                                            
                                            //se obtienen los vendedores/gerente anteriores para comparar con los enviados en la edicion
                                            //si llegasen a cambiar se elimina el comisionista anterior para colocar el nuevo
                                            if($strValor3Parametro == 'VENDEDOR')
                                            {
                                                $strVendedor = $arrayInfoBasica['vendedor'];
                                            }
                                            else//GERENTE
                                            {
                                                $strVendedor = $arrayInfoBasica['gerente'];
                                            }
                                            
                                            $arrayUsuarios = explode('-', $strVendedor);
                                            
                                            //Si el vendedor es diferente
                                            if($arrayUsuarios[0] != $intIdPersonalSeleccionado )
                                            {
                                                //Obtener el servicio comision anterior
                                                $objComision = $emComercial->getRepository("schemaBundle:InfoServicioComision")
                                                                           ->findOneBy(array('servicioId'          => $objServicio->getId(),
                                                                                             'personaEmpresaRolId' => $arrayUsuarios[0],
                                                                                             'comisionDetId'       => $arrayUsuarios[1],
                                                                                             'estado'              => 'Activo')
                                                                                      );
                                                if(is_object($objComision))
                                                {
                                                    $objComision->setEstado('Eliminado');
                                                    $emComercial->persist($objComision);
                                                    $emComercial->flush();
                                                    
                                                    //obtener la informacion del nuevo vendedor para ser agregado a comision del servicio
                                                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                            ->findOneById($intIdPersonalSeleccionado);

                                                    if( is_object($objInfoPersonaEmpresaRol) )
                                                    {
                                                        $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                                                        if( is_object($objInfoPersona) )
                                                        {
                                                            $strNombres                   = $objInfoPersona->getNombres();
                                                            $strApellidos                 = $objInfoPersona->getApellidos();
                                                            $strLoginPersonalSeleccionado = $objInfoPersona->getLogin();

                                                            if( !empty($strNombres) || !empty($strApellidos) )
                                                            {
                                                                $strNombrePersonalSeleccionado = trim( ucwords(strtolower(trim($strNombres))).' '.
                                                                                                       ucwords(strtolower(trim($strApellidos))) );
                                                            }//( !empty($strNombres) || !empty($strApellidos) )
                                                        }//( is_object($objInfoPersona) )
                                                        
                                                        $floatComisionVenta = $objAdmiComisionDet->getComisionVenta();
                                                    
                                                        //Se ingresa el nuevo comisionista actualizado
                                                        $objInfoServicioComision = new InfoServicioComision();
                                                        $objInfoServicioComision->setComisionDetId($objAdmiComisionDet);
                                                        $objInfoServicioComision->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                                        $objInfoServicioComision->setServicioId($objServicio);
                                                        $objInfoServicioComision->setComisionVenta($floatComisionVenta);
                                                        $objInfoServicioComision->setEstado('Activo');
                                                        $objInfoServicioComision->setFeCreacion(new \DateTime('now'));
                                                        $objInfoServicioComision->setIpCreacion($strIpCreacion);
                                                        $objInfoServicioComision->setUsrCreacion($strUsrCreacion);
                                                        $emComercial->persist($objInfoServicioComision);
                                                        $emComercial->flush();

                                                        //Se actualiza el vendedor seleccionado
                                                        $objServicio->setUsrVendedor($strLoginPersonalSeleccionado);
                                                        $emComercial->persist($objServicio);
                                                        $emComercial->flush();

                                                        $strNombrePersonalAnterior = '';
                                                        //Obtener vendedor anterior
                                                        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                                ->findOneById($arrayUsuarios[0]);

                                                        if( is_object($objInfoPersonaEmpresaRol) )
                                                        {
                                                            $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                                                            if( is_object($objInfoPersona) )
                                                            {
                                                                $strNombres                   = $objInfoPersona->getNombres();
                                                                $strApellidos                 = $objInfoPersona->getApellidos();
                                                                $strLoginPersonalSeleccionado = $objInfoPersona->getLogin();

                                                                if( !empty($strNombres) || !empty($strApellidos) )
                                                                {
                                                                    $strNombrePersonalAnterior = trim( ucwords(strtolower(trim($strNombres))).' '.
                                                                                                       ucwords(strtolower(trim($strApellidos))) );
                                                                }//( !empty($strNombres) || !empty($strApellidos) )
                                                            }
                                                        }

                                                        $strObservacionServicio .= '  <tr>	  
                                                                                            <td><b>'.$strValor3Parametro.' Anterior:</b></td>'
                                                                                         . '<td>&nbsp;&nbsp;</td><td>'.
                                                                                               $strNombrePersonalAnterior.'</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                            <td><b>'.$strValor3Parametro.' Nuevo:</b></td>'
                                                                                         . '<td>&nbsp;&nbsp;</td><td>'.
                                                                                             $strNombrePersonalSeleccionado.'</td>
                                                                                    </tr><tr><td>&nbsp;</td></tr>';
                                                        
                                                    }//( is_object($objInfoPersonaEmpresaRol) ) 
                                                }
                                            }//if comparacion de vendedores
                                        }//if validacion de parametro DET
                                    }
                                }
                            }//( isset($arrayPersonalSeleccionado[0]) && !empty($arrayPersonalSeleccionado[0])...
                        }//( !empty($strPersonalSeleccionado) )
                    }// foreach plantilla comisionistas enviado como parametro
                }

                //Edición de solución.
                $strObservacionServicio .= '<tr><td colspan="3"><b>Características</b></td></tr>';

                if($arrayInfoBasica['esMultipleCaracteristica'] == 'N')
                {
                    $arrayJson = json_decode($arrayServicios['caracteristicasProducto']);
                    
                    if(!empty($arrayJson))
                    {
                        foreach($arrayJson as $objJson)
                        {
                            $strValorNuevo     = $objJson->valor;
                            $strCaracteristica = str_replace("]","", str_replace("[","",$objJson->descripcion));
                            
                            $objServCaractGenerico = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                        $strCaracteristica,
                                                                                                        $objServicio->getProductoId());

                            //Consultamos si el servicio se encuentra en las tablas de recurso.
                            $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                                        ->findOneBy(array('servicioId' => $objServicio->getId(),
                                                          'estado'     => 'Activo'));

                            if (is_object($objServCaractGenerico) && !is_object($objInfoServicioRecursoCab))
                            {
                                $strValorAnterior = '';
                                //Verificar que el valor enviado sea diferente al nuevo para poder actualizar la caracteristica
                                foreach($arrayCaracteristicas as $array)
                                {
                                    if($array['descripcion'] == $strCaracteristica)
                                    {
                                        $strValorAnterior = $array['valor'];
                                        break;
                                    }
                                }
                                
                                if($strValorNuevo != $strValorAnterior)
                                {
                                    $objServCaractGenerico->setEstado('Eliminado');
                                    $objServCaractGenerico->setUsrUltMod($strUsrCreacion);
                                    $objServCaractGenerico->setFeUltMod(new \DateTime('now'));
                                    $emComercial->persist($objServCaractGenerico);
                                    $emComercial->flush();
                                    
                                    $serviceTecnico->ingresarServicioProductoCaracteristica($objServicio,
                                                                                            $objServicio->getProductoId(),
                                                                                            $strCaracteristica,
                                                                                            $strValorNuevo,
                                                                                            $strUsrCreacion
                                                                                           );
                                    
                                    $strObservacionServicio .= '  <tr>	  
                                                                <td><b>'.$strCaracteristica.' Anterior:</b></td><td>&nbsp;&nbsp;</td><td> '.
                                                                   $strValorAnterior.'</td>
                                                                </tr>
                                                                <tr>
                                                                  <td><b>'.$strCaracteristica.' Nuevo:</b></td><td>&nbsp;&nbsp;</td><td> '.
                                                                   $strValorNuevo.'</td>
                                                                </tr><tr><td>&nbsp;</td></tr>';
                                }
                            }
                            elseif (is_object($objInfoServicioRecursoCab))
                            {
                                $intCantidadNueva = intval($arrayServicios['cantidad']);

                                //Si se detecta algún cambio, se procede con la eliminación y creación del recurso.
                                if ($objInfoServicioRecursoCab->getTipoRecurso() != $strCaracteristica    ||
                                    $objInfoServicioRecursoCab->getDescripcionRecurso() != $strValorNuevo ||
                                    $objInfoServicioRecursoCab->getCantidad() != $intCantidadNueva)
                                {
                                    //Eliminamos la cabecera y el detalle.
                                    $arrayInfoServicioRecursoDet = $emComercial->getRepository("schemaBundle:InfoServicioRecursoDet")
                                            ->findBy(array('servicioRecursoCabId' => $objInfoServicioRecursoCab,
                                                           'estado'               => 'Activo'));

                                    foreach ($arrayInfoServicioRecursoDet as $objInfoServicioRecursoDet)
                                    {
                                        $objInfoServicioRecursoDet->setEstado('Eliminado');
                                        $objInfoServicioRecursoDet->setUsrUltMod($strUsrCreacion);
                                        $objInfoServicioRecursoDet->setFecUltMod(new \DateTime('now'));
                                        $objInfoServicioRecursoDet->setIpUltMod($strIpCreacion);
                                        $emComercial->persist($objInfoServicioRecursoDet);
                                        $emComercial->flush();
                                    }

                                    $strValorAnterior = $objInfoServicioRecursoCab->getDescripcionRecurso();
                                    $objInfoServicioRecursoCab->setEstado('Eliminado');
                                    $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                    $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                    $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                    $emComercial->persist($objInfoServicioRecursoCab);
                                    $emComercial->flush();

                                    //Creamos el nuevo recurso.
                                    $objInfoServicioRecursoCab = new InfoServicioRecursoCab();
                                    $objInfoServicioRecursoCab->setTipoRecurso($strCaracteristica);
                                    $objInfoServicioRecursoCab->setDescripcionRecurso($strValorNuevo);
                                    $objInfoServicioRecursoCab->setServicioId($objServicio->getId());
                                    $objInfoServicioRecursoCab->setCantidad($intCantidadNueva);
                                    $objInfoServicioRecursoCab->setEstado('Activo');
                                    $objInfoServicioRecursoCab->setUsrCreacion($strUsrCreacion);
                                    $objInfoServicioRecursoCab->setFecCreacion(new \DateTime('now'));
                                    $objInfoServicioRecursoCab->setIpCreacion($strIpCreacion);
                                    $emComercial->persist($objInfoServicioRecursoCab);
                                    $emComercial->flush();

                                    //Creamos el nuevo recurso con las nuevas características.
                                    $strObservacionServicio .=
                                        '<tr>'.
                                            '<td><b>'.$strCaracteristica.' Anterior:</b></td><td>&nbsp;&nbsp;</td>'.
                                            '<td>'.$strValorAnterior.'</td>'.
                                        '</tr>'.
                                        '<tr>'.
                                            '<td><b>'.$strCaracteristica.' Nuevo:</b></td><td>&nbsp;&nbsp;</td>'.
                                            '<td>'.$strValorNuevo.'</td>'.
                                        '</tr><tr><td>&nbsp;</td></tr>';
                                }
                            }
                            else
                            {
                                $strStatus  = 'ERROR';
                                $strMensaje = 'No existe Valor de caracteristica guardado para el Servicio, notificar a Sistemas';
                            }
                        }
                    }
                }
                else
                {
                    $boolPoolRecursos                   = true;
                    $serviceElemento                    = $this->get('tecnico.InfoElemento');
                    $arrayJson                          = json_decode($arrayServicios['caracteristicasPoolRecursos']);
                    $arrayPoolRecursosNuevos            = array();
                    $intContadorRn                      = 0;
                    $intContadorRe                      = 0;
                    $strObservacionRecursosNuevos       =
                            '<tr><td>&nbsp;</td></tr>'.
                            '<tr><td colspan="3"><b style="color:green;">Recursos Nuevos</b></td></tr>';
                    $strObservacionRecursosEliminados   =
                            '<tr><td>&nbsp;</td></tr>'.
                            '<tr><td colspan="3"><b style="color:green;">Recursos Eliminados</b></td></tr>';
                    $strObservacionRecursosActualizados =
                            '<tr><td>&nbsp;</td></tr>'.
                            '<tr><td colspan="3"><b style="color:green;">Recursos Actualizados</b></td></tr>';

                    if (!empty($arrayJson))
                    {
                        //Edición y eliminación de Pool de recursos
                        if (!$boolEsLicenciamiento)
                        {
                            //For para registrar todo los recursos nuevos, por motivos que los existentes no
                            //pueden ser editados desde la interface(Telcos).
                            foreach ($arrayJson as $objJson)
                            {
                                $boolNuevoRecurso = false;
                                $intCantidad      = intval($objJson->cantidad);
                                $intCantidad      = empty($intCantidad) || $intCantidad < 1 ?
                                                    $arrayServicios['cantidad'] : $intCantidad;

                                //Consultamos con precisión de información, si el recurso existe.
                                $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                                        ->findOneBy(array('id'                 => $objJson->idRaw,
                                                          'servicioId'         => $objServicio->getId(),
                                                          'tipoRecurso'        => $objJson->tipoRecurso,
                                                          'descripcionRecurso' => $objJson->caracteristica,
                                                          'cantidad'           => $intCantidad));

                                if (!is_object($objInfoServicioRecursoCab))
                                {
                                    $intContadorRn++;
                                    $boolNuevoRecurso          = true;
                                    $objInfoServicioRecursoCab = new InfoServicioRecursoCab();
                                    $objInfoServicioRecursoCab->setTipoRecurso($objJson->tipoRecurso);
                                    $objInfoServicioRecursoCab->setDescripcionRecurso($objJson->caracteristica);
                                    $objInfoServicioRecursoCab->setServicioId($objServicio->getId());
                                    $objInfoServicioRecursoCab->setCantidad($intCantidad);
                                    $objInfoServicioRecursoCab->setEstado('Activo');
                                    $objInfoServicioRecursoCab->setUsrCreacion($strUsrCreacion);
                                    $objInfoServicioRecursoCab->setFecCreacion(new \DateTime('now'));
                                    $objInfoServicioRecursoCab->setIpCreacion($strIpCreacion);
                                    $emComercial->persist($objInfoServicioRecursoCab);
                                    $emComercial->flush();

                                    $strPrefijo = !$boolEsPoolRecursos ? '' : (
                                            $objInfoServicioRecursoCab->getTipoRecurso() == 'PROCESADOR' ? ' (Cores)' : ' (Gb)');

                                    $strObservacionRecursosNuevos .=
                                        '<tr>'.
                                            '<td><b>Tipo:</b></td>'.
                                            '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                            '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                        '</tr>'.
                                        '<tr>'.
                                            '<td><b>Descripción:</b></td>'.
                                            '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                            '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                        '</tr>'.
                                        '<tr>'.
                                            '<td><b>Cantidad:</b></td>'.
                                            '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                            '<td>'.$objInfoServicioRecursoCab->getCantidad().$strPrefijo.'</td>'.
                                        '</tr>';

                                    //Almacenamos en un array los recursos nuevos con sus respectivas maquinas.
                                    if (count($objJson->idMaquinas) > 0)
                                    {
                                        $arrayPoolRecursosNuevos[] = array('idMaquinas'  => array_map('intval',$objJson->idMaquinas),
                                                                           'idRecurso'   => $objInfoServicioRecursoCab->getId(),
                                                                           'idAsociado'  => intval($objJson->idRaw),
                                                                           'tipoRecurso' => $objJson->tipoRecurso,
                                                                           'descripcion' => $objJson->caracteristica,
                                                                           'cantidad'    => $intCantidad);
                                    }
                                }

                                //Eliminamos de la lista actual, los recursos existentes para asi dejar los recursos a eliminar.
                                if (!$boolNuevoRecurso)
                                {
                                    $arrayIdRecursos = array_map(function($arrayResultDc)
                                    {
                                        return intval($arrayResultDc['id']);
                                    },$arrayCaracteristicas);

                                    //Buscamos la posición del recurso.
                                    $intPosicion = array_search(intval($objJson->idRaw),$arrayIdRecursos);

                                    if ($intPosicion === 0 || $intPosicion >= 1)
                                    {
                                        unset($arrayCaracteristicas[$intPosicion]);
                                    }
                                }
                            }

                            //Proceso para eliminar los recursos.
                            if (!empty($arrayCaracteristicas))
                            {
                                foreach ($arrayCaracteristicas as $arrayRecursosEliminar)
                                {
                                    $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                                            ->find($arrayRecursosEliminar['id']);

                                    if (is_object($objInfoServicioRecursoCab))
                                    {
                                        $arrayInfoServicioRecursoDet = $emComercial->getRepository("schemaBundle:InfoServicioRecursoDet")
                                                ->findBy(array('servicioRecursoCabId' => $objInfoServicioRecursoCab,
                                                               'estado'               => 'Activo'));

                                        foreach ($arrayInfoServicioRecursoDet as $objInfoServicioRecursoDet)
                                        {
                                            $objInfoServicioRecursoDet->setEstado('Eliminado');
                                            $objInfoServicioRecursoDet->setUsrUltMod($strUsrCreacion);
                                            $objInfoServicioRecursoDet->setFecUltMod(new \DateTime('now'));
                                            $objInfoServicioRecursoDet->setIpUltMod($strIpCreacion);
                                            $emComercial->persist($objInfoServicioRecursoDet);
                                            $emComercial->flush();
                                        }

                                        $intContadorRe++;
                                        $objInfoServicioRecursoCab->setEstado('Eliminado');
                                        $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                        $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                        $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                        $emComercial->persist($objInfoServicioRecursoCab);
                                        $emComercial->flush();

                                        $strPrefijo = !$boolEsPoolRecursos ? '' :
                                                ($objInfoServicioRecursoCab->getTipoRecurso() == 'PROCESADOR' ? ' (Cores)' : ' (Gb)');

                                        $strObservacionRecursosEliminados .=
                                            '<tr>'.
                                                '<td><b>Tipo:</b></td>'.
                                                '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td><b>Descripción:</b></td>'.
                                                '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td><b>Cantidad:</b></td>'.
                                                '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                '<td>'.$objInfoServicioRecursoCab->getCantidad().$strPrefijo.'</td>'.
                                            '</tr>';
                                    }
                                }
                            }

                            if ($intContadorRn > 0)
                            {
                                $strObservacionServicio .= $strObservacionRecursosNuevos.'<br/>';
                            }

                            if ($intContadorRe > 0)
                            {
                                $strObservacionServicio .= $strObservacionRecursosEliminados.'<br/>';
                            }

                            //Logica necesaria para poder adicionar el Id de recurso nuevo a las maquinas virtuales
                            //que tienen un id auto-generado.
                            if ($intContadorRn > 0 && !empty($arrayPoolRecursosNuevos) && !empty($arrayMaquinasVirtuales))
                            {
                                //For de maquinas virtuales.
                                foreach ($arrayMaquinasVirtuales as $i=>$objMaquinaVirtual)
                                {
                                    $arrayRecursosMv = json_decode($objMaquinaVirtual->arrayRecursos);

                                    foreach ($arrayPoolRecursosNuevos as $arrayPoolRecuso)
                                    {
                                        if (in_array(intval($objMaquinaVirtual->idMaquina), $arrayPoolRecuso['idMaquinas']))
                                        {
                                            $arrayIdRecursosMv = array_map(function($arrayResultDc)
                                            {
                                                return intval($arrayResultDc->idRecurso);
                                            },$arrayRecursosMv);

                                            $intPosicion    = array_search($arrayPoolRecuso['idAsociado'],$arrayIdRecursosMv);
                                            $arrayRecursoVm = ($intPosicion === 0 || $intPosicion >= 1) ?
                                                    $arrayRecursosMv[$intPosicion] : null;

                                            if (is_object($arrayRecursoVm) && $arrayRecursoVm->tipo == $arrayPoolRecuso['tipoRecurso']
                                                    && $arrayRecursoVm->caracteristica == $arrayPoolRecuso['descripcion'])
                                            {
                                                $arrayRecursosMv[$intPosicion]->idRecurso = $arrayPoolRecuso['idRecurso'];
                                            }
                                        }
                                    }

                                    $arrayMaquinasVirtuales[$i]->arrayRecursos = json_encode($arrayRecursosMv);
                                }
                            }
                        }
                        else
                        {
                            //Obtenemos las licencias nuevas.
                            $arrayLicenciasNuevas  = array_filter($arrayJson, function($objJson)
                            {
                                return !isset($objJson->esAntiguo) || !$objJson->esAntiguo;
                            });

                            //Obtenemos las licencias antiguas.
                            $arrayLicenciasAntiguas = array_filter($arrayJson, function($objJson)
                            {
                                return isset($objJson->esAntiguo) && $objJson->esAntiguo;
                            });

                            $arrayLicencias = array();

                            //For para sumarizar los recursos nuevos.
                            foreach ($arrayLicenciasNuevas as $objLicencia)
                            {
                                $objLicencia->cantidad = intval($objLicencia->cantidad);
                                $objJson->idMaquinas   = array_map('intval',$objJson->idMaquinas);
                                $intIdElementoMv       = $objLicencia->idMaquinas[0];
                                $boolAgregar           = true;

                                //Logica para sumarizar las licencias
                                {
                                    //Verificamos si el pool ya se encuentra registrado para posterior sumarizarlo.
                                    $arrayLicencia = array_filter($arrayLicencias, function($arrayDatos) use ($objLicencia)
                                    {
                                        return $arrayDatos['tipoRecurso']        === $objLicencia->tipoRecurso &&
                                               $arrayDatos['descripcionRecurso'] === $objLicencia->caracteristica;
                                    });

                                    if (!empty($arrayLicencia))
                                    {
                                        $boolAgregar = false;
                                        $intPosCab   = key($arrayLicencia);
                                        $arrayLicencias[$intPosCab]['cantidad'] += $objLicencia->cantidad;

                                        //Sumarizar cantidad de licencias asignadas a una maquina virtual.
                                        if (!empty($intIdElementoMv))
                                        {
                                            //Buscamos si la maquina virtual ya se encuentra registrada en el pool de licencias.
                                            $arrayDetalleMv = array_filter($arrayLicencias[$intPosCab]['detalle'],
                                                    function($arrayDatos) use ($intIdElementoMv)
                                            {
                                                return $arrayDatos['elementoId'] == $intIdElementoMv;
                                            });

                                            //Si la maquina virtual ya se encuentra registrada, se sumariza la cantidad
                                            //caso contrario la registramos.
                                            if (!empty($arrayDetalleMv))
                                            {
                                                $intPosDet = key($arrayDetalleMv);
                                                $arrayLicencias[$intPosCab]['detalle'][$intPosDet]['cantidad'] += $objLicencia->cantidad;
                                            }
                                            else
                                            {
                                                $arrayLicencias[$intPosCab]['detalle'][] = array('elementoId' => $intIdElementoMv,
                                                                                                 'cantidad'   => $objLicencia->cantidad);
                                            }
                                        }
                                    }
                                }

                                //Agregamos las licencias que no pasaron por el proceso de sumarización.
                                if ($boolAgregar)
                                {
                                    $arrayDatosLicencia = array();
                                    $arrayDatosLicencia['idServicio']         = $objServicio->getId();
                                    $arrayDatosLicencia['tipoRecurso']        = $objLicencia->tipoRecurso;
                                    $arrayDatosLicencia['descripcionRecurso'] = $objLicencia->caracteristica;
                                    $arrayDatosLicencia['cantidad']           = $objLicencia->cantidad;

                                    if (!empty($intIdElementoMv))
                                    {
                                        $arrayDatosLicencia['detalle'][] = array('elementoId' => $intIdElementoMv,
                                                                                 'cantidad'   => $objLicencia->cantidad);
                                    }

                                    $arrayLicencias[] = $arrayDatosLicencia;
                                }
                            }

                            //Proceso para eliminar los recursos.
                            if (!empty($arrayCaracteristicas)&& count($arrayCaracteristicas) > 0)
                            {
                                //Antes de crear el pool nuevo, eliminamos las licencias que el usuario desde
                                //la interface (libero,retiro,elimino, etc).
                                foreach ($arrayLicenciasAntiguas as $objLicencia)
                                {
                                    $objLicencia->idRaw      =  intval($objLicencia->idRaw);
                                    $objLicencia->cantidad   =  intval($objLicencia->cantidad);
                                    $objLicencia->idMaquinas =  array_map('intval',$objLicencia->idMaquinas);
                                    $objLicencia->idMaquinas = !empty($objLicencia->idMaquinas[0]) ? $objLicencia->idMaquinas[0] : null;

                                    //Verificamos si existe una licencia registrada con las siguientes características.
                                    $arrayLicencia = array_filter($arrayCaracteristicas, function($arrayDatos) use ($objLicencia)
                                    {
                                        return $arrayDatos['id']          === $objLicencia->idRaw          &&
                                               $arrayDatos['descripcion'] === $objLicencia->tipoRecurso    &&
                                               $arrayDatos['valor']       === $objLicencia->caracteristica &&
                                               $arrayDatos['valorCaract'] === $objLicencia->cantidad       &&
                                               $arrayDatos['idMaquina']   === $objLicencia->idMaquinas;
                                    });

                                    //Retiramos del array las licencias existentes para solo dejar las que
                                    //toca eliminar o actualizar.
                                    if (!empty($arrayLicencia))
                                    {
                                        $intPosicion = key($arrayLicencia);
                                        unset($arrayCaracteristicas[$intPosicion]);
                                    }
                                }

                                //Eliminación/Actualización del pool de licencias.
                                if (!empty($arrayCaracteristicas)&& count($arrayCaracteristicas) > 0)
                                {
                                    foreach ($arrayCaracteristicas as $arrayLicencia)
                                    {
                                        //Consultamos si el recurso existe.
                                        $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                                                ->find($arrayLicencia['id']);

                                        if (is_object($objInfoServicioRecursoCab))
                                        {
                                            $intCantidadTotal    = $objInfoServicioRecursoCab->getCantidad() !== null &&
                                                                   $objInfoServicioRecursoCab->getCantidad() >= 0 ?
                                                                   $objInfoServicioRecursoCab->getCantidad() : 0;
                                            $intCantidadAnterior = $intCantidadTotal;

                                            $arrayInfoServicioRecursoDet = $emComercial->getRepository("schemaBundle:InfoServicioRecursoDet")
                                                    ->findBy(array('servicioRecursoCabId' => $objInfoServicioRecursoCab,
                                                                   'estado'               => 'Activo'));

                                            //Si no existe detalle y la cantidad actual es menor o igual a la cantidad a
                                            //(eliminar/actualizar), se elimina la cabecera completamente
                                            //por motivos que no se tendria ninguna licencia disponible.
                                            if (empty($arrayInfoServicioRecursoDet) &&
                                                $intCantidadTotal <= $arrayLicencia['valorCaract'])
                                            {
                                                $objInfoServicioRecursoCab->setEstado('Eliminado');
                                                $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                                $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                                $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoCab);
                                                $emComercial->flush();

                                                $strObservacionRecursosEliminados .=
                                                    '<tr>'.
                                                        '<td><b>Tipo:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Descripción:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Cantidad:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$intCantidadTotal.'</td>'.
                                                    '</tr>'.
                                                    '<tr><td>&nbsp;</td></tr>';

                                                continue;
                                            }

                                            //Si no existe detalle y la cantidad actual es mayor a la cantidad a
                                            //(eliminar/actualizar), se actualiza la cantidad de la cabecera del
                                            //recurso, por motivos que aun tendriamos disponibilidad de licencias.
                                            if (empty($arrayInfoServicioRecursoDet) &&
                                                    $intCantidadTotal > $arrayLicencia['valorCaract'])
                                            {
                                                $intCantidadTotal -= $arrayLicencia['cantidad'];
                                                $objInfoServicioRecursoCab->setCantidad($intCantidadTotal);
                                                $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                                $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                                $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoCab);
                                                $emComercial->flush();

                                                $strObservacionRecursosActualizados .=
                                                    '<tr>'.
                                                        '<td><b>Tipo:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Descripción:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Cantidad Anterior:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$intCantidadAnterior.'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Cantidad Actual:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$intCantidadTotal.'</td>'.
                                                    '</tr>'.
                                                    '<tr><td>&nbsp;</td></tr>';

                                                continue;
                                            }

                                            //Si existe detalle y la cantidad actual es menor o igual a la cantidad a
                                            //(eliminar/actualizar), se elimina la cabecera y el detalle
                                            //por motivos que no se tendria ninguna licencia disponible.
                                            if (!empty($arrayInfoServicioRecursoDet) &&
                                                $intCantidadTotal <= $arrayLicencia['valorCaract'])
                                            {
                                                foreach ($arrayInfoServicioRecursoDet as $objInfoServicioRecursoDet)
                                                {
                                                    $objInfoServicioRecursoDet->setEstado('Eliminado');
                                                    $objInfoServicioRecursoDet->setUsrUltMod($strUsrCreacion);
                                                    $objInfoServicioRecursoDet->setFecUltMod(new \DateTime('now'));
                                                    $objInfoServicioRecursoDet->setIpUltMod($strIpCreacion);
                                                    $emComercial->persist($objInfoServicioRecursoDet);
                                                    $emComercial->flush();
                                                }

                                                $objInfoServicioRecursoCab->setEstado('Eliminado');
                                                $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                                $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                                $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoCab);
                                                $emComercial->flush();

                                                $strObservacionRecursosEliminados .=
                                                    '<tr>'.
                                                        '<td><b>Tipo:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Descripción:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Cantidad:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$intCantidadTotal.'</td>'.
                                                    '</tr>'.
                                                    '<tr><td>&nbsp;</td></tr>';

                                                continue;
                                            }


                                            /****************************************************************
                                             * A este punto existe detalle y la cantidad actual es mayor a
                                             * la cantidad a (eliminar/actualizar)
                                             ****************************************************************/

                                            //Si el recurso a (eliminar/actualizar) no tiene una maquina asociada
                                            //se actualiza la cantidad de la cabecera
                                            if (empty($arrayLicencia['idMaquina']))
                                            {
                                                $intCantidadTotal -= $arrayLicencia['valorCaract'];
                                                $objInfoServicioRecursoCab->setCantidad($intCantidadTotal);
                                                $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                                $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                                $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoCab);
                                                $emComercial->flush();

                                                $strObservacionRecursosActualizados .=
                                                    '<tr>'.
                                                        '<td><b>Tipo:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Descripción:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Cantidad Anterior:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$intCantidadAnterior.'</td>'.
                                                    '</tr>'.
                                                    '<tr>'.
                                                        '<td><b>Cantidad Actual:</b></td>'.
                                                        '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                        '<td>'.$intCantidadTotal.'</td>'.
                                                    '</tr>'.
                                                    '<tr><td>&nbsp;</td></tr>';

                                                continue;
                                            }

                                            //En caso que se tenga licencia, se procede con la actualización.
                                            $objInfoServicioRecursoDet = $emComercial->getRepository("schemaBundle:InfoServicioRecursoDet")
                                                    ->findOneBy(array('servicioRecursoCabId' => $objInfoServicioRecursoCab,
                                                                      'elementoId'           => $arrayLicencia['idMaquina'],
                                                                      'cantidad'             => $arrayLicencia['valorCaract'],
                                                                      'estado'               => 'Activo'));

                                            if (is_object($objInfoServicioRecursoDet))
                                            {
                                                //Eliminamos el detalle.
                                                $objInfoServicioRecursoDet->setEstado('Eliminado');
                                                $objInfoServicioRecursoDet->setUsrUltMod($strUsrCreacion);
                                                $objInfoServicioRecursoDet->setFecUltMod(new \DateTime('now'));
                                                $objInfoServicioRecursoDet->setIpUltMod($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoDet);
                                                $emComercial->flush();
                                            }

                                            $intCantidadTotal -= $arrayLicencia['valorCaract'];
                                            $objInfoServicioRecursoCab->setCantidad($intCantidadTotal);
                                            $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                            $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                            $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                            $emComercial->persist($objInfoServicioRecursoCab);
                                            $emComercial->flush();

                                            $strObservacionRecursosActualizados .=
                                                '<tr>'.
                                                    '<td><b>Tipo:</b></td>'.
                                                    '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                    '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                                '</tr>'.
                                                '<tr>'.
                                                    '<td><b>Descripción:</b></td>'.
                                                    '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                    '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                                '</tr>'.
                                                '<tr>'.
                                                    '<td><b>Cantidad Anterior:</b></td>'.
                                                    '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                    '<td>'.$intCantidadAnterior.'</td>'.
                                                '</tr>'.
                                                '<tr>'.
                                                    '<td><b>Cantidad Actual:</b></td>'.
                                                    '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                                    '<td>'.$intCantidadTotal.'</td>'.
                                                '</tr>'.
                                                '<tr><td>&nbsp;</td></tr>';
                                        }
                                    }

                                    $strObservacionServicio .= $strObservacionRecursosEliminados.$strObservacionRecursosActualizados;
                                }
                            }

                            //creacion del nuevo pool de licencias.
                            if (!empty($arrayLicencias) && count($arrayLicencias) > 0)
                            {
                                foreach ($arrayLicencias as $arrayLicencia)
                                {
                                    $objInfoServicioRecursoCab = $emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                                            ->findOneBy(array('servicioId'         => $arrayLicencia['idServicio'],
                                                              'tipoRecurso'        => $arrayLicencia['tipoRecurso'],
                                                              'descripcionRecurso' => $arrayLicencia['descripcionRecurso'],
                                                              'estado'             => 'Activo'));

                                    //Ingresamos la cabecera y sumarizamos en caso de que el recurso exista.
                                    if (is_object($objInfoServicioRecursoCab))
                                    {
                                        $intCantidadTotal  = $objInfoServicioRecursoCab->getCantidad() !== null &&
                                                             $objInfoServicioRecursoCab->getCantidad() >= 0 ?
                                                             $objInfoServicioRecursoCab->getCantidad() : 0;
                                        $intCantidadTotal += $arrayLicencia['cantidad'];

                                        //Actualizamos la cantidad total del recurso existente.
                                        $objInfoServicioRecursoCab->setCantidad($intCantidadTotal);
                                        $objInfoServicioRecursoCab->setUsrUltMod($strUsrCreacion);
                                        $objInfoServicioRecursoCab->setFecUltMod(new \DateTime('now'));
                                        $objInfoServicioRecursoCab->setIpUltMod($strIpCreacion);
                                        $emComercial->persist($objInfoServicioRecursoCab);
                                        $emComercial->flush();
                                    }
                                    else
                                    {
                                        //Creamos el nuevo recurso.
                                        $objInfoServicioRecursoCab = new InfoServicioRecursoCab();
                                        $objInfoServicioRecursoCab->setTipoRecurso($arrayLicencia['tipoRecurso']);
                                        $objInfoServicioRecursoCab->setDescripcionRecurso($arrayLicencia['descripcionRecurso']);
                                        $objInfoServicioRecursoCab->setServicioId($arrayLicencia['idServicio']);
                                        $objInfoServicioRecursoCab->setCantidad($arrayLicencia['cantidad']);
                                        $objInfoServicioRecursoCab->setEstado('Activo');
                                        $objInfoServicioRecursoCab->setUsrCreacion($strUsrCreacion);
                                        $objInfoServicioRecursoCab->setFecCreacion(new \DateTime('now'));
                                        $objInfoServicioRecursoCab->setIpCreacion($strIpCreacion);
                                        $emComercial->persist($objInfoServicioRecursoCab);
                                        $emComercial->flush();
                                    }

                                    $strObservacionRecursosNuevos .=
                                        '<tr>'.
                                            '<td><b>Tipo:</b></td>'.
                                            '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                            '<td>'.$objInfoServicioRecursoCab->getTipoRecurso().'</td>'.
                                        '</tr>'.
                                        '<tr>'.
                                            '<td><b>Descripción:</b></td>'.
                                            '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                            '<td>'.$objInfoServicioRecursoCab->getDescripcionRecurso().'</td>'.
                                        '</tr>'.
                                        '<tr>'.
                                            '<td><b>Cantidad:</b></td>'.
                                            '<td><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;</td>'.
                                            '<td>'.$arrayLicencia['cantidad'].'</td>'.
                                        '</tr>'.
                                        '<tr><td>&nbsp;</td></tr>';

                                    //Ingresamos el detalle y sumarizamos en caso que el detalle exista.
                                    if (!empty($arrayLicencia['detalle']) && count($arrayLicencia['detalle']) > 0)
                                    {
                                        foreach ($arrayLicencia['detalle'] as $arrayDetalle)
                                        {
                                            $objInfoServicioRecursoDet = $emComercial->getRepository("schemaBundle:InfoServicioRecursoDet")
                                                    ->findOneBy(array('servicioRecursoCabId' => $objInfoServicioRecursoCab,
                                                                      'elementoId'           => $arrayDetalle['elementoId'],
                                                                      'estado'               => 'Activo'));

                                            if (is_object($objInfoServicioRecursoDet))
                                            {
                                                $intCantidadTotal  = $objInfoServicioRecursoDet->getCantidad() !== null &&
                                                                     $objInfoServicioRecursoDet->getCantidad() >= 0 ?
                                                                     $objInfoServicioRecursoDet->getCantidad() : 0;
                                                $intCantidadTotal += $arrayDetalle['cantidad'];

                                                //Actualizamos la cantidad total del recurso existente.
                                                $objInfoServicioRecursoDet->setCantidad($intCantidadTotal);
                                                $objInfoServicioRecursoDet->setUsrUltMod($strUsrCreacion);
                                                $objInfoServicioRecursoDet->setFecUltMod(new \DateTime('now'));
                                                $objInfoServicioRecursoDet->setIpUltMod($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoDet);
                                                $emComercial->flush();
                                            }
                                            else
                                            {
                                                $objInfoServicioRecursoDet = new InfoServicioRecursoDet();
                                                $objInfoServicioRecursoDet->setServicioRecursoCabId($objInfoServicioRecursoCab);
                                                $objInfoServicioRecursoDet->setElementoId($arrayDetalle['elementoId']);
                                                $objInfoServicioRecursoDet->setCantidad($arrayDetalle['cantidad']);
                                                $objInfoServicioRecursoDet->setEstado('Activo');
                                                $objInfoServicioRecursoDet->setUsrCreacion($strUsrCreacion);
                                                $objInfoServicioRecursoDet->setFecCreacion(new \DateTime('now'));
                                                $objInfoServicioRecursoDet->setIpCreacion($strIpCreacion);
                                                $emComercial->persist($objInfoServicioRecursoDet);
                                                $emComercial->flush();
                                            }
                                        }
                                    }
                                }
                                $strObservacionServicio.= $strObservacionRecursosNuevos;
                            }
                        }

                        //Proceso para registrar el descuento.
                        foreach ($arrayJson as $objJson)
                        {
                            $strDescuento = $objJson->descuento;
                            $strDescuento = strpos($strDescuento,'$ ') !== false ? explode('$ ',$strDescuento)[1] : $strDescuento;
                            $strDescuento = strpos($strDescuento,'% ') !== false ? explode('% ',$strDescuento)[1] : $strDescuento;
                            $strDescuento = round(floatval($strDescuento),2);

                            if (empty($strDescuento) || $strDescuento < 0 || $strDescuento === 0)
                            {
                                continue;
                            }

                            $strDescuento       = '$ '.$strDescuento;
                            $strCaracteristica  = !$boolEsLicenciamiento ? $objJson->tipoRecurso : 'TIPO LICENCIAMIENTO SERVICE';
                            $strDescripcion     =  $objJson->caracteristica;
                            $strTipoRecursoAdic =  $boolEsLicenciamiento ? $objJson->tipoRecurso : '';
                            $strValor           = !empty($strTipoRecursoAdic) ? $strTipoRecursoAdic.'@'.$strDescripcion:$strDescripcion;

                            //Obtenemos las características.
                            $objCaracteristica     = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                    ->findOneByDescripcionCaracteristica($strCaracteristica);
                            $objCaracteristicaDesc = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                    ->findOneByDescripcionCaracteristica('DESCUENTO_POR_CARACTERISTICA');

                            if (!is_object($objCaracteristica) || !is_object($objCaracteristicaDesc))
                            {
                                continue;
                            }

                            //Obtenemos las características del producto.
                            $objProdCaract     = $emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                    ->findOneBy(array('productoId'       => $objProducto->getId(),
                                                      'caracteristicaId' => $objCaracteristica->getId()));
                            $objProdCaractDesc = $emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                    ->findOneBy(array('productoId'       => $objProducto->getId(),
                                                      'caracteristicaId' => $objCaracteristicaDesc->getId()));

                            if (!is_object($objProdCaract) || !is_object($objProdCaractDesc))
                            {
                                continue;
                            }

                            //Registramos el pool.
                            $objServicioProdCaract = new InfoServicioProdCaract();
                            $objServicioProdCaract->setServicioId($objServicio->getId());
                            $objServicioProdCaract->setProductoCaracterisiticaId($objProdCaract->getId());
                            $objServicioProdCaract->setValor($strValor);
                            $objServicioProdCaract->setEstado('Activo');
                            $objServicioProdCaract->setUsrCreacion($strUsrCreacion);
                            $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                            $emComercial->persist($objServicioProdCaract);
                            $emComercial->flush();

                            //Registramos el descuento.
                            $objServicioProdCaractDescto = new InfoServicioProdCaract();
                            $objServicioProdCaractDescto->setServicioId($objServicio->getId());
                            $objServicioProdCaractDescto->setProductoCaracterisiticaId($objProdCaractDesc->getId());
                            $objServicioProdCaractDescto->setValor($strDescuento);
                            $objServicioProdCaractDescto->setEstado('Activo');
                            $objServicioProdCaractDescto->setRefServicioProdCaractId($objServicioProdCaract->getId());
                            $objServicioProdCaractDescto->setUsrCreacion($strUsrCreacion);
                            $objServicioProdCaractDescto->setFeCreacion(new \DateTime('now'));
                            $emComercial->persist($objServicioProdCaractDescto);
                            $emComercial->flush();
                        }
                    }
                }

                //guardar historial del servicio y creación de maquinas virtuales.
                if ($strStatus == 'OK')
                {
                    $strObservacionServicio .= '</table>';
                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($objServicio);
                    $objServicioHist->setObservacion($strObservacionServicio);
                    $objServicioHist->setIpCreacion($strIpCreacion);
                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setEstado($objServicio->getEstado());
                    $emComercial->persist($objServicioHist);
                    $emComercial->flush();
                    $emComercial->commit();

                    //Creacion de maquinas virtuales nuevas.
                    if (!empty($arrayMaquinasVirtuales) && $boolPoolRecursos && !$boolEsLicenciamiento)
                    {
                        //LLamada al service que crea maquinas virtuales.
                        $arrayParametrosMv                   = array();
                        $arrayParametrosMv['strUsrCreacion'] = $strUsrCreacion;
                        $arrayParametrosMv['intIdEmpresa']   = $objSession->get('idEmpresa');
                        $arrayParametrosMv['strIpCreacion']  = $strIpCreacion;
                        $arrayParametrosMv['strJson']        = json_encode($arrayMaquinasVirtuales);
                        $arrayParametrosMv['intIdServicio']  = $objServicio->getId();
                        $arrayRespuestaMv = $serviceElemento->guardarMaquinasVirtuales($arrayParametrosMv);

                        if ($arrayRespuestaMv['strStatus'] === 'ERROR')
                        {
                            $strStatus  = 'ERROR';
                            $strMensaje = $arrayRespuestaMv['$strStatus'];
                        }
                    }
                }
            }
            else
            {
                $strStatus  = 'ERROR';
                $strMensaje = 'No existe Información de Servicio a actualizar, notificar a Sistemas';
            }
        } 
        catch (\Exception $e) 
        {
            $strStatus   = 'ERROR';
            $strMensaje  = 'Error al editar el Servicio, notificar a Sistemas';
                        
            $serviceUtil->insertError( 'Telcos+', 
                                        'ComercialBundle.InfoServicioController.ajaxEditarServicioSolucionAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpCreacion 
                                     );    
            
            if($emComercial->getConnection()->isTransactionActive())
            {
               $emComercial->rollback();
            }
            
            $emComercial->close();
        }
        
        $objResponse->setData(array('status' => $strStatus , 'mensaje' => $strMensaje));
        
        return $objResponse;
    }    
    
    

    
    /**
     * Metodo encargado de traer la informacion de los clientes parametrizados para ser migrados. los datos que trea son :
     * 
     *  - Clientes
     *  - Puntos
     *  - Servicios
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 22-01-2018
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetInfoClientesParaMigracionAction()
    {
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();
        $strCodEmpresa = $objSession->get('idEmpresa');
        $strAccion     = $objRequest->get('accion');
        $emGeneral     = $this->getDoctrine()->getManager('telconet_general');
        $emComercial   = $this->getDoctrine()->getManager('telconet');
        $arrayRespuesta= array();
        
        if($strAccion == 'clientes')
        {
            $arrayClientes =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('CLIENTES A MIGRAR POR INTERCONEXION', 
                                              'COMERCIAL', 
                                              '',
                                              '',
                                              '', 
                                              '',
                                              '',
                                              '', 
                                              '', 
                                              $strCodEmpresa);
            if(!empty($arrayClientes))
            {
                foreach($arrayClientes as $array)
                {
                    $arrayRespuesta[] = array('idPersonaRol'  =>  $array['valor2'],
                                              'razonSocial'   =>  $array['valor1']);
                }
            }
        }
        else if($strAccion == 'puntos')
        {
            $strRazonSocial  = $objRequest->get('razonSocial');
            $intIdServicioOri= $objRequest->get('idServicio');
            
            $arrayClientes   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->get('PRODUCTOS A MIGRAR POR INTERCONEXION', 
                                              'COMERCIAL', 
                                              '',
                                              $strRazonSocial,
                                              '', 
                                              '',
                                              '',
                                              '', 
                                              '', 
                                              $strCodEmpresa);
            
            $arrayProductosAdmitidos = array();
            
            if(!empty($arrayClientes))
            {
                foreach($arrayClientes as $array)
                {
                    $arrayProductosAdmitidos[] = $array['valor1'];
                }
            }
            
            //Obtener la UM del servicio a copiar los datos
            $objServicioTecnicoOri = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($intIdServicioOri);
            
            if(is_object($objServicioTecnicoOri))
            {
                $intUltimaMilla = $objServicioTecnicoOri->getUltimaMillaId();
            }
            
            $arrayParametros['intPersonaRolId']         = $objRequest->get('personaRol');
            $arrayParametros['intIdOficina']            = $objRequest->get('oficina');
            $arrayParametros['strLogin']                = $objRequest->get('login');
            $arrayParametros['intUltimaMilla']          = $intUltimaMilla;
            $arrayParametros['arrayProductosAdmitidos'] = $arrayProductosAdmitidos;
            
            $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")->getArrayPuntosMigracionFactibilidad($arrayParametros);
        }
        else//accion = servicios
        {
            $strRazonSocial  = $objRequest->get('razonSocial');
            $intIdServicioOri= $objRequest->get('idServicio');
            $intUltimaMilla  = '';
            
            $arrayClientes   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->get('PRODUCTOS A MIGRAR POR INTERCONEXION', 
                                              'COMERCIAL', 
                                              '',
                                              $strRazonSocial,
                                              '', 
                                              '',
                                              '',
                                              '', 
                                              '', 
                                              $strCodEmpresa);
            
            //Se obtienen los tipo de productos que pueden ser mostrados en la migracion
            $arrayProductosAdmitidos = array();
            
            if(!empty($arrayClientes))
            {
                foreach($arrayClientes as $array)
                {
                    $arrayProductosAdmitidos[] = $array['valor1'];
                }
            }
            
            //Obtener la UM del servicio a copiar los datos
            $objServicioTecnicoOri = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($intIdServicioOri);
            
            if(is_object($objServicioTecnicoOri))
            {
                $intUltimaMilla = $objServicioTecnicoOri->getUltimaMillaId();
            }
            
            $arrayParametros['intIdPunto']              = $objRequest->get('idPunto');
            $arrayParametros['intUltimaMilla']          = $intUltimaMilla;
            $arrayParametros['arrayProductosAdmitidos'] = $arrayProductosAdmitidos;
            $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")->getArrayServiciosMigracionFactibilidad($arrayParametros);
        }
                
        $objResponse    = new JsonResponse();
        
        $objResponse->setData(array('encontrados' => $arrayRespuesta));
        
        return $objResponse;
    }
    
    /**
     * Metodo encargado de guardar y realizar la clonacion del servicio seleccionado sobre el nuevo servicio creado ( migracion de datos tecnicos )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 23-01-2018
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 07-03-2018 - Se valida que se genere tarea a IPCCL2 por region del servicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 24-04-2018 - Se quita seteo de servicio origen de migración y se genera historial
     *                           de servicio origen de migración con ultimo estado del Servicio
     * @since 1.1
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 27-06-2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxClonarServiciosMigracionAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $intIdServicio     = $objRequest->get('idServicio');
        $intIdServicioAnte = $objRequest->get('idServicioAnterior');
        $serviceUtil       = $this->get('schema.Util');
        $serviceTecnico    = $this->get('tecnico.InfoServicioTecnico');
        $strUsrCreacion    = $objSession->get('user');
        $strIpCreacion     = $objRequest->getClientIp();
        $strCodEmpresa     = $objSession->get('idEmpresa');
        $strPrefijo        = $objSession->get('prefijoEmpresa');
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $emGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte         = $this->get('doctrine')->getManager('telconet_soporte');
        $strMensaje        = '';
        $strStatus         = 'OK';
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            $objServicioActual   = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            $objServicioAnterior = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicioAnte);
            
            if(is_object($objServicioActual) && is_object($objServicioAnterior))
            {
                $objServicioTecnicoAnterior = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                          ->findOneByServicioId($objServicioAnterior->getId());
                
                if(is_object($objServicioTecnicoAnterior))
                {
                    //Transacciones sobre servicio nueva que esta siendo creado=========================================================
                    $objServicioTecnicoActual = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                            ->findOneByServicioId($objServicioActual->getId());
                    
                    $intIdUltimaMillaOriginal = $objServicioTecnicoAnterior->getUltimaMillaId();
                    
                    if(is_object($objServicioTecnicoActual))
                    {
                        $intIdUltimaMillaOriginal = $objServicioTecnicoActual->getUltimaMillaId();
                        $emComercial->remove($objServicioTecnicoActual);
                        $emComercial->flush();
                    }
                    
                    $objServicioTecnicoActual = clone $objServicioTecnicoAnterior;
                    $objServicioTecnicoActual->setServicioId($objServicioActual);
                    $objServicioTecnicoActual->setUltimaMillaId($intIdUltimaMillaOriginal);
                    $emComercial->persist($objServicioTecnicoActual);
                    $emComercial->flush();
                    
                    $objServicioActual->setEstado('AsignadoTarea');
                    $emComercial->persist($objServicioActual);
                    $emComercial->flush();
                    
                    $strObservacion = 'Se clona información técnica desde el servicio: <b>'.$objServicioAnterior->getLoginAux().'</b>';
                    
                    //Historial del Servicio Actual
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioActual);
                    $objServicioHistorial->setObservacion($strObservacion);
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setEstado('AsignadoTarea');
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                    
                    //Se crea solicitud de planificacion
                    $objTipoSolicitud    = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                       ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
                    
                    $objDetalleSolicitud = new InfoDetalleSolicitud();
                    $objDetalleSolicitud->setServicioId($objServicioActual);
                    $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                    $objDetalleSolicitud->setEstado('AsignadoTarea');
                    $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolicitud->setObservacion($strObservacion);
                    $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                    $emComercial->persist($objDetalleSolicitud);
                    $emComercial->flush();

                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHist->setIpCreacion($strIpCreacion);
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolHist->setObservacion($strObservacion);
                    $objDetalleSolHist->setEstado('AsignadoTarea');
                    $emComercial->persist($objDetalleSolHist);
                    $emComercial->flush();
                    
                    //Obtener caracteristica de tipo de factibilidad
                    $objServCaractTipoFact = $serviceTecnico->getServicioProductoCaracteristica($objServicioAnterior,
                                                                                                'TIPO_FACTIBILIDAD',
                                                                                                $objServicioAnterior->getProductoId()
                                                                                               );
                    if(is_object($objServCaractTipoFact))
                    {
                        $serviceTecnico->ingresarServicioProductoCaracteristica($objServicioActual,
                                                                                $objServicioActual->getProductoId(),
                                                                                'TIPO_FACTIBILIDAD',
                                                                                $objServCaractTipoFact->getValor(),
                                                                                $strUsrCreacion);
                    }
                    
                    //Crear la caracteristica para agregar referencia de servicio heredado
                    $serviceTecnico->ingresarServicioProductoCaracteristica($objServicioActual,
                                                                            $objServicioActual->getProductoId(),
                                                                            'SERVICIO_HEREDADO',
                                                                            $intIdServicioAnte,
                                                                            $strUsrCreacion);
                    
                    $serviceTecnico->ingresarServicioProductoCaracteristica($objServicioActual,
                                                                            $objServicioActual->getProductoId(),
                                                                            'INTERCONEXION_CLIENTES',
                                                                            'S',
                                                                            $strUsrCreacion);
                    
                    //Se realiza notificacion de tarea automatica a IPCCL2 para asignar recursos de red para este servicio
                    
                    $strObservacion  = '<b>Tarea Automática:</b> Realizar la Asignación de Recursos de Red del Servicio cuyos datos estan siendo'
                                     . ' clonados desde <b>'.$objServicioAnterior->getLoginAux().'</b> ';
                    
                    $objDepartamento = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->findOneByNombreDepartamento('IPCCL2');
                    
                    $strCiudad = $serviceTecnico->getCiudadRelacionadaPorRegion($objServicioActual,$strCodEmpresa);
                    
                    $objCanton   = $emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strCiudad);
                    $intIdCanton = 0;
                    
                    if(is_object($objCanton))
                    {
                        $intIdCanton = $objCanton->getId();
                    }
                                        
                    $arrayParametrosEnvioPlantilla                      = array();
                    $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
                    $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $strUsrCreacion;
                    $arrayParametrosEnvioPlantilla['strIpCreacion']     = $strIpCreacion;
                    $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objDetalleSolicitud->getId();
                    $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
                    $arrayParametrosEnvioPlantilla['objPunto']          = $objServicioActual->getPuntoId();
                    $arrayParametrosEnvioPlantilla['objDepartamento']   = $objDepartamento;
                    $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
                    $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $strCodEmpresa;
                    $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $strPrefijo;
                    
                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                    
                    $objAdmiProceso   = $emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($strNombreProceso);
                    
                    if(is_object($objAdmiProceso))
                    {
                        $arrayTareas = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($objAdmiProceso->getId());
                        
                        foreach($arrayTareas as $objkey => $objTarea)
                        {
                            if(is_object($objTarea))
                            {
                                $arrayParametrosEnvioPlantilla['intTarea'] = $objTarea->getId();
                            }
                        }
                        
                        $serviceInfoCambiarPlan = $this->get('tecnico.InfoCambiarPlan');
                        $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                        $strNumeroTarea = $serviceInfoCambiarPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
                    }
                    
                    //=========================================================================
                    
                    //Se convierte a estado Migrado el servicio desde donde se obtiene información técnica
                    
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioAnterior);
                    $objServicioHistorial->setObservacion('Información Técnica del Servicio fue '
                                                        . 'clonada a Servicio del login : <b>'.$objServicioActual->getPuntoId()->getLogin().'</b>');
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setEstado($objServicioAnterior->getEstado());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                    
                    $emComercial->commit();
                    
                    $strStatus  = 'OK';
                    $strMensaje = 'Información Técnica del Servicio : <b>'.$objServicioAnterior->getLoginAux().'</b> fue clonada exitosamente<br>'
                                . 'Se debe asignar Recursos de Red al nuevo servicio.';
                }
            }
            else
            {
                $strStatus  = 'ERROR';
                $strMensaje = 'No se encontró información del Servicio a ser clonado, notificar a Sistemas';
            }
        } 
        catch (\Exception $e) 
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.InfoServicioController.ajaxClonarServiciosMigracionAction', 
                                       $e->getMessage(), 
                                       $strUsrCreacion, 
                                       $strIpCreacion );
            
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al Clonar la Información del Servicio seleccionado, notificar a Sistemas';
            
            if($emComercial->getConnection()->isTransactionActive())
            {
               $emComercial->rollback();
            }
            
            $emComercial->close();
        }
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData(array('mensaje' => $strMensaje, 'status' => $strStatus));
        
        return $objResponse;
    }
    
    /**
     * Método encargado de generar el HTML para establecer el tipo de esquema en base a si el cliente posee
     * un servicio tradicional activo.
     *
     * @version 1.0 13-03-2019
     * @author Pablo Pin <ppin@telconet.ec>
     *
     * $boolAllOptions: Booleano que determina si en la vista se van a presentar ambos tipos de
     * esquema para poder seleccionar.
     *
     * @return JsonResponse
     */
    public function ajaxGetTipoEsquemaAction()
    {
        $objRequest         = $this->getRequest();
        $intIdPunto         = $objRequest->get(self::STR_ID_PUNTO);
        $emComercial        = $this->get(self::STR_DOCTRINE)->getManager(self::STR_EM_COMERCIAL);
        $emGeneral          = $this->get(self::STR_DOCTRINE)->getManager(self::STR_EM_GENERAL);

        $objResponse        = new JsonResponse();

        $objParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                   ->findOneBy(array(self::STR_DESCRIPCION => 'SERVICIOS_TRADICIONALES'));
        // Obtengo el valor desde el objeto, lo arreglo dentro un array y transformo sus valores en enteros
        $arrayServiciosTradicionales = array_map('intval', explode(',', $objParametros->getValor1()));

        $objServicio        = $emComercial->getRepository("schemaBundle:InfoServicio")
                                          ->findOneBy(array(self::STR_PUNTO_ID    =>  $intIdPunto,
                                                            self::STR_PRODUCTO_ID =>  $arrayServiciosTradicionales,
                                                            self::STR_ESTADO      =>  self::STR_ACTIVO));
        $boolAllOptions     = (!is_null($objServicio));

        $objResponse->setData(array(
            'strStatus'      => 'OK',
            'boolAllOptions' => $boolAllOptions
        ));

        return $objResponse;
    }
    
    /**
     * validaAgregarIPMPAction
     * 
     * Método encargado de validar la cantidad de dispositivos y si el punto no posee servicios McAfee al agregar un producto I. PROTEGIDO MULTI PAID
     * con la nueva tecnología Kaspersky
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-08-2019
     *
     * @return JsonResponse
     */
    public function validaAgregarIPMPAction()
    {
        $objJsonResponse                = new JsonResponse();
        $objRequest                     = $this->getRequest();
        $objSession                     = $objRequest->getSession();
        $intIdPunto                     = $objRequest->get('intIdPunto');
        $strCantidadDispositivosIPMP    = $objRequest->get('strCantidadDispositivosIPMP');
        $strCodEmpresa                  = $objSession->get('idEmpresa');
        $strUsrCreacion                 = $objSession->get('user');
        $strIpCreacion                  = $objRequest->getClientIp();

        $serviceUtil                = $this->get('schema.Util');
        $serviceLicenciasKaspersky  = $this->get('tecnico.LicenciasKaspersky');
        try
        {
            $arrayParametros            = array("intIdPunto"                    => $intIdPunto,
                                                "strCantidadDispositivosIPMP"   => $strCantidadDispositivosIPMP,
                                                "strCodEmpresa"                 => $strCodEmpresa);
            $arrayRespuestaCambioCorreo = $serviceLicenciasKaspersky->validaAgregarIPMP($arrayParametros);
            $strStatus                  = $arrayRespuestaCambioCorreo['status'];
            $strMensaje                 = $arrayRespuestaCambioCorreo['mensaje'];
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Ha ocurrido un error. Por favor Notificar a Sistemas!";
            $serviceUtil->insertError('Telcos+',
                                      'InfoServicioController->validaAgregarIPMPAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $arrayRespuesta = array("status"  => $strStatus,
                                "mensaje" => $strMensaje);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }
    
    /**
     * verificaEliminacionServicioAction
     * 
     * Método encargado de verificar si un servicio puede o no ser eliminado del grid de servicios a nivel comercial
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 09-05-2020
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 04-10-2020 Se agrega validación de producto en punto con tipo de negocio PYME,
     *                         se verifica si el producto tiene asociada la característica IP WAN
     *
     * @return JsonResponse
     */
    public function validaEliminacionServicioAction()
    {
        $objJsonResponse        = new JsonResponse();
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $intIdProductoAEliminar = $objRequest->get('intIdProductoAEliminar');
        $strIdsProductosGrid    = $objRequest->get('strIdsProductosGrid');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $strIpCreacion          = $objRequest->getClientIp();
        $serviceUtil            = $this->get('schema.Util');
        $serviceServicio        = $this->get('comercial.InfoServicio');
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $strExisteIpWan         = "NO";
        try
        {
            $arrayParametros            = array("intIdProductoAEliminar"    => $intIdProductoAEliminar,
                                                "strCodEmpresa"             => $strCodEmpresa,
                                                "strIdsProductosGrid"       => $strIdsProductosGrid);
            $arrayRespuestaVerificacion = $serviceServicio->validaEliminacionServicio($arrayParametros);
            $strStatus                  = $arrayRespuestaVerificacion['status'];
            $strMensaje                 = $arrayRespuestaVerificacion['mensaje'];
            /* Se agrega validación de producto en punto con tipo de negocio PYME, se verifica
               si el producto tiene asociada la característica IP WAN */
            if($arrayPtoCliente['tipo_negocio'] === "PYME")
            {
                $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $intIdProductoAEliminar,
                                                         'strDescCaracteristica' => 'IP WAN',
                                                         'strEstado'             => 'Activo' );
                $strExisteIpWanTmp = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
                $strExisteIpWan = $strExisteIpWanTmp === 'S' ? 'SI' : $strExisteIpWan;
            }
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Ha ocurrido un error. Por favor Notificar a Sistemas!";
            $serviceUtil->insertError('Telcos+',
                                      'InfoServicioController->validaEliminacionServicioAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $arrayRespuesta = array("status"      => $strStatus,
                                "mensaje"     => $strMensaje,
                                "existeIpWan" => $strExisteIpWan,
                                "tipoNegocio" => $arrayPtoCliente['tipo_negocio']);
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }    
    
    
    
    /**
     *  Método encargado de traer los motivos para cargar el combo en la eliminación de la orden de servicio.
     * 
     * @author Josselhin Moreira Quezada <kjmoreira@telconet.ec>
     * @version 1.0 14-03-2019
     * @Secure(roles="ROLE_137-224,ROLE_13-225")
     * 
     */ 
    public function getMotivosEliminacionAction()
    {
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        
        $intStart = $objPeticion->query->get('start');
        $intLimit = $objPeticion->query->get('limit');
        
        //rsaenz -- CAMBIARLO AUTOMATICO.... ESTOS SON DE LA factibilidad PLANIFICACION... HABRIA QUE CAMBIARLO         
		//cambiar ... no es accionId = 1 sino el de getMotivos
        $entitySeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>60, "accionId"=>8));
	$intRelacionSistemaId      = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;         
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_general")
                        ->getRepository('schemaBundle:AdmiMotivo')
                        ->generarJson("","Activo",$intStart,$intLimit, $intRelacionSistemaId);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }
    
    
    /**
     * Método que valida si existe un proceso masivo de creación de facturas en estado Pendiente
     *
     * @version 1.0 16-12-2019
     * @author Katherine Yager <kyager@telconet.ec>
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-03-2020 Se corrige error de programación detectado al eliminar un servicio Telcohome
     * 
     * 
     * $strContinuaProceso: Retorna OK, para continuar proceso ó No 
     *
     * @return JsonResponse
     */
    public function ajaxGetProcesoMasivoNcAction()
    { 
        $objRespuesta           = new JsonResponse();
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strContinuaProceso     = 'OK';
        $emInfraestructura      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $strPuntoAdmin          ='50418';
        $strServicioAdmin       ='39889';
        $strCodEmpresa          = $objSession->get('idEmpresa');
        
        $arrayProcesoMasivoDet  = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                    ->getObtenerUltimoProcesoMasivoDet('AprobarNC', 
                                                                                       $strCodEmpresa,
                                                                                       $strPuntoAdmin, 
                                                                                       $strServicioAdmin);
          
        if ($arrayProcesoMasivoDet)
        {  
            $entityInfoProcesoMasivoDet = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                            ->find($arrayProcesoMasivoDet[0]['ID_PROCESO_MASIVO_DET']);
        }
        else
        { 
            $entityInfoProcesoMasivoDet = null;
        }

        if(is_object($entityInfoProcesoMasivoDet))
        {
            $strProcesoMasivoCabEstado=$entityInfoProcesoMasivoDet->getEstado();
        }

        if($strProcesoMasivoCabEstado=='Pendiente')
        {
          $strContinuaProceso='NO';
        }
        else
        {
          $strContinuaProceso='OK';
        }
    
        $objRespuesta->setContent($strContinuaProceso);
        return $objRespuesta;
    }
    
    /**
     * 
     * Documentación para el método 'getPuntosMdAsociadosAction'.
     *
     * Método utilizado para obtener los puntos que estarán asociados a servicios TN
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-07-2020
     * 
     */
    public function getPuntosMdAsociadosAction()
    {
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');
        $strUserComercial       = $this->container->getParameter('user_comercial');
        $strPasswordComercial   = $this->container->getParameter('passwd_comercial');
        $objRequest             = $this->getRequest();
        $intStart               = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit               = $objRequest->get('limit') ? $objRequest->get('limit') : 0;
        $strCedulaCliente       = $objRequest->get('cedulaCliente') ? $objRequest->get('cedulaCliente') : 0;
        $strLoginPunto          = $objRequest->get('loginPunto') ? $objRequest->get('loginPunto') : '';
        $objJsonResponse        = new JsonResponse();
        $arrayParametros        = array("strDatabaseDsn"            => $strDatabaseDsn,
                                        "strUserComercial"          => $strUserComercial,
                                        "strPasswordComercial"      => $strPasswordComercial,
                                        "strCedulaCliente"          => $strCedulaCliente,
                                        "strLoginPunto"             => $strLoginPunto,
                                        "intStart"                  => $intStart,
                                        "intLimit"                  => $intLimit);
        $strJsonResponse        = $emComercial->getRepository('schemaBundle:InfoServicio')->getJsonPuntosMdAsociados($arrayParametros);
        $objJsonResponse->setContent($strJsonResponse);
        return $objJsonResponse;
    }
    
    /**
     * 
     * Documentación para el método 'validaProductoAdicionalAction'.
     *
     * Método utilizado para validar datos sobre un producto adicional.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 17-09-2020
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.0 17-02-2021 Agrega validacion de estados para cableado ethernet al momento de agregar productos al servicio
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.22 17-05-2021 - Se anexa validacion para que al agregar un proucto adicional parametrizados,
     *                            solo lo haga con un servicio de internet en un estado permitido.
     * 
     */
    public function validaProductoAdicionalAction()
    {      
        $objRequest                = $this->getRequest();          
        $intIdPlan                 = $objRequest->get("planId");   
        $intCantidadIngresada      = $objRequest->get("cantidad_detalle");   
        $intCantidadTotalIngresada = $objRequest->get("cantidad_total_ingresada");   
        $intProductoId             = $objRequest->get("productoId");           
        $strTipo                   = $objRequest->get("tipo");           
        $objPeticion               = $this->get('request');
        $objSession                = $objPeticion->getSession();
        $arrayPtoCliente           = $objSession->get('ptoCliente'); 
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $serviceUtil               = $this->get('schema.Util');
        $serviceTecnico            = $this->get('tecnico.InfoServicioTecnico');
        $strMensaje                = 'Ok';
        $emComercial               = $this->get('doctrine')->getManager('telconet');
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        try
        {
            if($strPrefijoEmpresa === "MD" && !empty($intProductoId) && !empty($arrayPtoCliente))
            {
                $arrayParametrosValor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('VALIDA_PROD_ADICIONAL', 
                                                                         'COMERCIAL', 
                                                                         '',
                                                                         '',
                                                                         $intProductoId,
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         $strCodEmpresa);
                if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
                {
                    foreach($arrayParametrosValor as $arrayParametro)
                    {
                        if($arrayParametro['valor2']=='CANTIDAD_PROD')
                        {   
                            $arrayParametros    = array("Punto"      => $arrayPtoCliente['id'],
                                                        "Producto"   => $intProductoId,
                                                        "Estado"     => 'Todos');
                            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoServicio')->getProductoByPlanes($arrayParametros);
                            $arrayParametrosProduc    = array("Punto"      => $arrayPtoCliente['id'],
                                                              "Producto"   => $intProductoId);
                            $intServicios = $emComercial->getRepository('schemaBundle:InfoServicio')->
                                                                                                   getCantidadServiciosByProd($arrayParametrosProduc);
                            $intCantidadSevicio = $arrayResultado['total']+$intCantidadIngresada+$intServicios;
                            if($intCantidadSevicio>$arrayParametro['valor3'])
                            {
                                throw new \Exception('Error, Numero de Producto exedido. Maximo 3 Puntos cableados por Punto del Cliente.'); 
                            }
                            
                        }
                        elseif($arrayParametro['valor2']=='INTERNET')
                        {
                            // Valida los estados permitidos para agregar cableado ethernet
                            $strProdAdicional = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                        ->findOneBy(
                                                                            array('id'     => $intProductoId,
                                                                                  'estado' => 'Activo'));
                            $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findServiciosByPuntoAndEstado($arrayPtoCliente['id'], null, null);
                            $objSerActual = null;
                            foreach($arrayServicios['registros'] as $servicio)
                            {
                                if ($servicio->getPlanId()!=null)
                                {
                                    $objSerActual = $servicio;
                                }
                            }
                            // Validamos que solo sea para producto cableado ethernet
                            $arrayParametroTipos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('VALIDA_PROD_ADICIONAL','COMERCIAL','',
                                                        'Solicitud cableado ethernet',
                                                        '','','','','',$strCodEmpresa);
                            if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                            {
                                $objCableParametro = $arrayParametroTipos[0];
                            }
                            if ($strProdAdicional->getId() == $objCableParametro['valor1'])
                            {
                                // Obtenemos los valores de los estados parametrizados
                                $arrayEstadosPermitidos = array();
                                $arrayEstadosValor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->get('VALIDA_PROD_ADICIONAL', 
                                                                                'COMERCIAL','',
                                                                                'Estados permitidos para producto cableado ethernet',
                                                                                '','','','','',$strCodEmpresa);
                                if(is_array($arrayEstadosValor) && !empty($arrayEstadosValor))
                                {
                                    $arrayEstadosPermitidos = $serviceUtil->obtenerValoresParametro($arrayEstadosValor);
                                }
                                if (empty($objSerActual))
                                {
                                    throw new \Exception('Error, debe tener un servicio de internet vigente par agregar este producto');
                                }
                                else if (!in_array($objSerActual->getEstado(), $arrayEstadosPermitidos))
                                {
                                    throw new \Exception('Error, el estado actual de su servicio no permite agregar este producto');
                                }
                            }
                        }
                    }
                }
                else
                {
                    //Obtenemos los estados permitidos para agregar el producto adicional
                    $arrayEstados = array();
                    $arrayEstadosParam = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                  'Estados permitidos del servicio internet',
                                                  '','','','','',$strCodEmpresa);
                    if(is_array($arrayEstadosParam) && !empty($arrayEstadosParam))
                    {
                        $arrayEstados = $serviceUtil->obtenerValoresParametro($arrayEstadosParam);
                    }
                    // Validar que exista servicio internet para activacion de productos adicionales especificos
                    $arrayListadoServicios = array();
                    $arrayListadoServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PRODUCTOS ADICIONALES AUTOMATICOS', 
                                                        'COMERCIAL','',
                                                        'Lista de productos adicionales automaticos',
                                                        '','','','','',$strCodEmpresa);
                    $arrayObtenerServicio = $serviceTecnico->obtieneServicioInternetValido(array(
                                                                                "intIdPunto"    => $arrayPtoCliente['id'],
                                                                                    "strCodEmpresa" => $strCodEmpresa));                   
                    

                    foreach ($arrayListadoServicios as $objListado)
                    {
                        if ($intProductoId == $objListado['valor1'])
                        {  
                            if(is_array($arrayObtenerServicio) && !empty($arrayObtenerServicio) 
                            && $arrayObtenerServicio['status'] == 'OK')
                            {
                                $objServicioInternet = $arrayObtenerServicio['objServicioInternet'];
                                if ( !is_object($objServicioInternet)  ||
                                        !in_array($objServicioInternet->getEstado(), $arrayEstados) )
                                {
                                    throw new \Exception('Error, el estado actual de su servicio no permite agregar este producto'); 
                                }

                            }
                            else
                            {
                                throw new \Exception('Error, debe tener un servicio de internet vigente para agregar este producto'); 
                            }
                        }
                    }
                    
                }
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }
        $objResponse = new Response(json_encode(array('msg' => $strMensaje)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'ajaxGetPropuestasTelcoCRMAction'.
     *
     * Función que retorna el listado de propuestas de acuerdo al cliente en sesión.
     *
     * @return $objResponse - Listado de Propuestas.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 28-01-2021
     *
     */
    public function ajaxGetPropuestasTelcoCrmAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $arrayClienteSesion     = $objSession->get('cliente')        ? $objSession->get('cliente'):"";
            $arrayPuntoSesion       = $objSession->get('ptoCliente')     ? $objSession->get('ptoCliente'):"";
            $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
            $strCodEmpresa          = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
            $strUsrCreacion         = $objSession->get('user')           ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()         ? $objRequest->getClientIp():'127.0.0.1';
            $arrayPropuesta         = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $serviceTelcoCrm        = $this->get('comercial.ComercialCrm');

            if(empty($strPrefijoEmpresa) || $strPrefijoEmpresa !== "TN")
            {
                throw new \Exception("La consulta de propuestas solo aplica para la empresa Telconet.");
            }
            if(empty($arrayClienteSesion) || !is_array($arrayClienteSesion) || empty($arrayClienteSesion['identificacion']))
            {
                throw new \Exception("Se necesita tener un cliente en sesión");
            }
            if(empty($arrayPuntoSesion) || !is_array($arrayPuntoSesion))
            {
                throw new \Exception("Se necesita tener un punto en sesión");
            }

            $arrayParametros      = array("strRuc"             => $arrayClienteSesion["identificacion"],
                                          "strPrefijoEmpresa"  => $strPrefijoEmpresa,
                                          "strCodEmpresa"      => $strCodEmpresa);
            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametros,
                                          "strOp"              => 'getPropuesta',
                                          "strFuncion"         => 'procesar');
            $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);

            if(isset($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"]))
            {
                foreach($arrayRespuestaWSCrm["resultado"] as $arrayItem)
                {
                    array_push($arrayPropuesta,array("intIdPropuesta" => $arrayItem->intIdPropuesta,
                                                     "strPropuesta"   => $arrayItem->strPropuesta));
                }
            }
        }
        catch(\Exception $ex)
        {
            $arrayPropuesta = array();
            $serviceUtil->insertError('TelcoS+',
                                      'InfoServicioController.ajaxGetPropuestasTelcoCRMAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('arrayPropuesta' => $arrayPropuesta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

     /**
     * Documentación para la función 'ajaxValidaEstadoPuntoAction'.
     *
     * Función que invoca a service para validacion del estado del punto
     * previo agregación de servicios
     *
     * @return $objResponse - Mensaje de error.
     *
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.0 19-07-2022
     *
     */
    public function ajaxValidaEstadoPuntoAction()
    {
        $serviceInfoServicio    = $this->get('comercial.InfoServicio');
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa')  ? $objSession->get('idEmpresa'):'';
        $arrayPuntoSesion       = $objSession->get('ptoCliente') ? $objSession->get('ptoCliente'):'';
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $strNombreProducto      = $objRequest->get("nombreProducto");           

        if(empty($arrayPuntoSesion) || !is_array($arrayPuntoSesion))
        {
                throw new \Exception('Se necesita tener un punto en sesión');
        }

        $intIdPunto   = !empty($arrayPuntoSesion['id']) ? $arrayPuntoSesion['id'] : '';

        $arrayParametros = array('intIdPunto'  => $intIdPunto,
                                'strCodEmpresa' => $strCodEmpresa);

        $strMensajeResultado = $serviceInfoServicio->validaEstadoPunto($arrayParametros);

        if($strMensajeResultado != '')
        {
            $objResponse = new Response(json_encode(array('msg' => 'Error', 'mensaje_validaciones' => $strMensajeResultado)));
        }
        else
        {
            $objResponse = new Response(json_encode(array('msg' => 'ok', 'mensaje_validaciones' => '')));


            $objServicioInternetCod = $emComercial->getRepository('schemaBundle:InfoServicio')
            ->obtieneServicioInternetxPunto($intIdPunto);
            if (is_object($objServicioInternetCod))
            {

                      
          
                $strDescripcion='Estado del servicio de internet no permitido para agregacion de servicio.';
                //Se obtienen los estados no permitidos desde el parámetro
                $arrayParametrosEstados = array('strNombreParametroCab' => 'ESTADOS_RESTRICCION_PUNTO_ADDSERVICIO',
                                                'strEstado'             => 'Activo',
                                                'strDescripcion'        => $strDescripcion,
                                                'strEmpresaCod'         => $strCodEmpresa);



                //Obtiene los estados no permitidos según el parámetro.
                $arrayListParams   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->findParametrosDet($arrayParametrosEstados);



                    //Valida el estado actual del punto
            foreach($arrayListParams["arrayResultado"] as $arrayEstado)
            {

             if($arrayEstado['strValor1'] === $objServicioInternetCod->getEstado())
             {

             $strPrimero="No se permite el ingreso de ".$strNombreProducto;
             $strMensajeResultado=$strPrimero." por no poseer un servicio de Internet Contratado con estado Valido";

             $objResponse = new Response(json_encode(array('msg' => 'Error', 'mensaje_validaciones' => $strMensajeResultado)));                    
                        break;
             }
            }

           }    
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para la función 'ajaxGetCotizacionTelcoCrmAction'.
     *
     * Función que retorna el listado de cotizaciones de acuerdo a la propuesta seleccionada.
     *
     * @return $objResponse - Listado de Cotización.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 08-02-2021
     *
     */
    public function ajaxGetCotizacionTelcoCrmAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $arrayClienteSesion     = $objSession->get('cliente')        ? $objSession->get('cliente'):"";
            $arrayPuntoSesion       = $objSession->get('ptoCliente')     ? $objSession->get('ptoCliente'):"";
            $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
            $strCodEmpresa          = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
            $strUsrCreacion         = $objSession->get('user')           ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()         ? $objRequest->getClientIp():'127.0.0.1';
            $intIdPropuesta         = $objRequest->get('intIdPropuesta') ? $objRequest->get('intIdPropuesta'):"";
            $arrayCotizacion        = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $serviceTelcoCrm        = $this->get('comercial.ComercialCrm');

            if(empty($strPrefijoEmpresa) || $strPrefijoEmpresa !== "TN")
            {
                throw new \Exception("La consulta de propuestas solo aplica para la empresa Telconet.");
            }
            if(empty($arrayClienteSesion) || !is_array($arrayClienteSesion) || empty($arrayClienteSesion['identificacion']))
            {
                throw new \Exception("Se necesita tener un cliente en sesión");
            }
            if(empty($arrayPuntoSesion) || !is_array($arrayPuntoSesion))
            {
                throw new \Exception("Se necesita tener un punto en sesión");
            }

            $arrayParametros      = array("strRuc"               => $arrayClienteSesion["identificacion"],
                                          "strPrefijoEmpresa"    => $strPrefijoEmpresa,
                                          "strCodEmpresa"        => $strCodEmpresa,
                                          "strIdPropuesta"       => $intIdPropuesta,
                                          "strBanderaCotizacion" => "PROPUESTA-COTIZACION-TELCOS");
            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametros,
                                          "strOp"              => 'getCotizacion',
                                          "strFuncion"         => 'procesar');
            $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);

            if(isset($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"]))
            {
                foreach($arrayRespuestaWSCrm["resultado"] as $arrayItem)
                {
                    array_push($arrayCotizacion,array("intIdCotizacion" => $arrayItem->id_cotizacion,
                                                      "strCotizacion"   => $arrayItem->name_cotizacion));
                }
            }
        }
        catch(\Exception $ex)
        {
            $arrayCotizacion = array();
            $serviceUtil->insertError('TelcoS+',
                                      'InfoServicioController.ajaxGetCotizacionTelcoCrmAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('arrayCotizacion' => $arrayCotizacion)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * Funcion que valida la existencia de un correo electronico como caracteristica de 
     * servicios HBO-MAX y E-ELEARN que se encuentren en estado Activo.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 08-08-2022
     
     * @return redirect
     **/
    public function ajaxValidaCorreoHboElearnAction()
    {
        $objRespuesta    = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();
        $strIpClient     = $objRequest->getClientIp();
        $strUser         = $objSession->get("user");
        $serviceUtil     = $this->get('schema.Util');
        $emComercial     = $this->getDoctrine()->getManager();
        $emGeneral       = $this->get('doctrine')->getManager('telconet_general');
        $arrayParametros = array();
        $objRespuestaValidacion    = null;
        $strRespuesta              = "ERROR";
        $strTipoProducto           = $objRequest->get('tipoProducto');
        try
        {
            //parametro que devuelve la data de los productos a validar el correo existente.
            $arrayParametroValidaCorreo =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('VALIDA_CORREO_EXISTENTE_HBO_ELEARN',//nombre parametro cab
                                                            'COMERCIAL', //modulo cab
                                                            'OBTENER_DATA',//proceso cab
                                                            'PRODUCTO_TV', //descripcion det
                                                            $strTipoProducto,
                                                            '','','','',
                                                            '18'); //empresa
            if(!empty($arrayParametroValidaCorreo))
            {
                $arrayParametros["nombreTecnico"]             = $arrayParametroValidaCorreo['valor1'];
                $arrayParametros["descripcionCaracteristica"] = $arrayParametroValidaCorreo['valor2'];
                $arrayParametros["descripcionProducto"]       = $arrayParametroValidaCorreo['valor3'];
                $arrayParametros["estadoSpc"]                 = $arrayParametroValidaCorreo['valor4'];
                $arrayParametros["inEstadoServ"]              = explode(",",$arrayParametroValidaCorreo['valor5']);
                $arrayParametros["valorSpc"]                  = $objRequest->get('correoElectronico');
                
                $objRespuestaValidacion = $emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->existeCaraceristicaServicio($arrayParametros);
            }
            else
            {
                throw new \Exception("No se encontraron Valores para Validar Correo Electrónico"); 
            }
            
            if (is_object($objRespuestaValidacion))
            {
                $strRespuesta = "EXISTENTE";
            }
            else
            {
                $strRespuesta = "NO EXISTENTE";
            }
            
            $objRespuesta->setContent($strRespuesta);   
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'ajaxValidaCorreoHboElearnAction', $ex->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("ERROR");
        }
        return $objRespuesta;
    }
    
     /**
     * Documentación para función ajaxValidaProdAdicionalMsAction.
     * 
     * Función que consume microservicio para validaciones por producto adicional.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 08-12-2022  
     */
    public function ajaxValidaProdAdicionalMsAction()
    {
        $objSession      = $this->getRequest()->getSession();
        $strUsrCreacion  = $objSession->get('user') ? $objSession->get('user'):"";
        $strCodEmpresa   = $objSession->get('idEmpresa');
        $objRequest      = $this->getRequest();
        $strIpCreacion   = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
        $intIdPersona    = $objRequest->get('intIdPersona');
        $intIdPunto      = $objRequest->get('intIdPunto');
        $serviceServicio = $this->get('comercial.InfoServicio');

        try
        {
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas   = $serviceTokenCas->generarTokenCas();
            
            $arrayParametros = array();
            $arrayParametros['strTokenCas']    = $arrayTokenCas['strToken'];
            $arrayParametros['usrCreacion']    = $strUsrCreacion;
            $arrayParametros['clienteIp']      = $strIpCreacion;       
            $arrayParametros['idEmpresa']      = $strCodEmpresa;
            $arrayParametros['idPersona']      = $intIdPersona;
            $arrayParametros['idPunto']        = $intIdPunto;

            $arrayResponse = $serviceServicio->validacionProdAdicionalMs($arrayParametros);
        }
        catch(\Exception $e)
        {
            $strMensaje   = 'Error en el proceso ajaxValidaProductoAdicionalMsAction. Consulte con el Administrador del Sistema.';
            $arrayResponse = array('strStatus'  => 'ERROR',
                                   'strMensaje' => $strMensaje);
        }


        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }

    
    
    /**
     * Funcion que permite obtener los archivos que son requeridos en el modulo Comercia al ingresar el servicio SAFE ENTRY
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version  1.0 09-12-2022 - Version inicial
     * 
     * @return Response
     */
    public function ajaxGetArchivosReqSafeEntryAction()
    {
        $objRespuesta    = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent("Se presentaron errores al ejecutar la accion.");
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();
        $strIpClient     = $objRequest->getClientIp();
        $strUser         = $objSession->get("user");
        $serviceUtil     = $this->get('schema.Util');
        $emGeneral       = $this->get('doctrine')->getManager('telconet_general');
        $arrayRespuesta = array();
        try
        {
            $arrayArchivosRequeridos =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('CONFIG SAFE ENTRY','COMERCIAL','','ARCHIVOS_REQUERIDOS','','','','','','10');
            foreach($arrayArchivosRequeridos as $arrayArchivo)
            {               
               array_push($arrayRespuesta,array(
                    $arrayArchivo['valor1'],
                    $arrayArchivo['valor2'],
                    $arrayArchivo['valor3']));
            }
            $objRespuesta->setContent(json_encode($arrayRespuesta));
        }
        catch (\Exception $e) 
        {
            $serviceUtil->insertError('Telcos+', 'ajaxValidaCorreoHboElearnAction', $e->getMessage(), $strUser, $strIpClient);
            $objRespuesta->setContent("ERROR");
        }

        return $objRespuesta;
    
    }
}

