<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiCuadrilla;
use telconet\schemaBundle\Entity\AdmiCuadrillaHistorial;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Form\AdmiCuadrillaType;
use telconet\schemaBundle\Entity\AdmiTipoHorario;
use telconet\schemaBundle\Entity\InfoHistoHorarioCuadrilla;
use telconet\schemaBundle\Entity\InfoHistoEmpleCuadrilla;
use telconet\schemaBundle\Entity\InfoDiaSemanaCuadrilla;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\administracionBundle\Service\InfoCoordinadorTurnoService;

/**
 * AdmiCuadrilla controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Cuadrillas
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 13-10-2015
 */
class AdmiCuadrillaController extends Controller implements TokenAuthenticatedController
{ 
        
        const CARACTERISTICA_PRESTAMO_CUADRILLA = 'PRESTAMO CUADRILLA';
        const CARACTERISTICA_PRESTAMO_EMPLEADO  = 'PRESTAMO EMPLEADO';

        const DETALLE_ASOCIADO_ELEMENTO_VEHICULO        = 'CUADRILLA';
        const DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED  = 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA'; 
        const DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO = 'ASIGNACION_VEHICULAR_FECHA_INICIO_CUADRILLA';
        const DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN    = 'ASIGNACION_VEHICULAR_FECHA_FIN_CUADRILLA';
        const DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO	= 'ASIGNACION_VEHICULAR_HORA_INICIO_CUADRILLA';
        const DETALLE_ASIGNACION_VEHICULAR_HORA_FIN		= 'ASIGNACION_VEHICULAR_HORA_FIN_CUADRILLA'; 

        const DETALLE_ASIGNACION_PROVISIONAL_CHOFER                 = 'ASIGNACION_PROVISIONAL_CHOFER';
        const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO    = 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO';
        const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN       = 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN';
        const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO     = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO';
        const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN        = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN';

        const CATEGORIA_ELEMENTO_TABLET         = 'tablet';
        const CATEGORIA_ELEMENTO_TRANSPORTE     = 'transporte';
        const DETALLE_ASOCIADO_ELEMENTO_TABLET  = 'LIDER';

        const DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA            ='SOLICITUD ASIGNACION VEHICULAR PREDEFINIDA';
        const NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA            = 'ZONA_PREDEFINIDA_ASIGNACION_VEHICULAR';
        const NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA           = 'TAREA_PREDEFINIDA_ASIGNACION_VEHICULAR';
        const NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO    = 'DEPARTAMENTO_PREDEFINIDO_ASIGNACION_VEHICULAR';

    /**
     * @Secure(roles="ROLE_170-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administracion de cuadrillas
     * @return render.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 18-02-2012
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 15-10-2015 - Se agrega que se envíe el 'Cargo' y el 'idPersonaEmpresaRol' del Coordinador Principal
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 20-11-2015 - Se envía como parámetro la variable 'strCategoriaTransporte' que corresponde a la categoría de los elementos
     *                           de tipo 'VEHICULO' que se requiere presentar al asignar un activo.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 20-11-2015 - Se crea la constante 'CATEGORIA_ELEMENTO_TRANSPORTE' y se la envía como parámetro a la vista inicial.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 04-01-2017 - Se agregan las credenciales para liberar y reactivar una cuadrilla.
     * 
     * @author Modificado: Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.7 17-01-2023 - Se reutiliza esta función para renderizar diferentes vistas según los parámetros recibidos,
     *                           además se valida el cargo del empleado para que solo ciertos departamentos pueden renderizar 
     *                           la vista de gestión general cuadrillas.
     */
    public function indexAction()
    {
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $emSeguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial   = $this->getDoctrine()->getManager();
        $strNombreArea = "Tecnico";

        $strEsGestion   = $objRequest->query->get('strEsGestion') ? $objRequest->query->get('strEsGestion') : 'NO';
        $strRenderVista = 'administracionBundle:AdmiCuadrilla:index.html.twig';
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strEmpresaCod         = $objSession->get('idEmpresa');

        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $strEmpresaCod);
        
        $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        
        $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol) );

        $entityItemMenu  = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("170", "1");

        $rolesPermitidos = array();
        //MODULO 170 - CUADRILLA/DELETE
        if(true === $this->get('security.context')->isGranted('ROLE_170-8'))
        {
            $rolesPermitidos[] = 'ROLE_170-8';
        }
        //MODULO 170 - CUADRILLA/DELETE AJAX
        if(true === $this->get('security.context')->isGranted('ROLE_170-9'))
        {
            $rolesPermitidos[] = 'ROLE_170-9';
        }
        //MODULO 170 - CUADRILLA/SHOW
        if(true === $this->get('security.context')->isGranted('ROLE_170-6'))
        {
            $rolesPermitidos[] = 'ROLE_170-6';
        }
        //MODULO 170 - CUADRILLA/EDITAR
        if(true === $this->get('security.context')->isGranted('ROLE_170-4'))
        {
            $rolesPermitidos[] = 'ROLE_170-4';
        }
        
        //MODULO 170 - asignacionVehicular/asignarVehiculosCuadrilla
        if(true === $this->get('security.context')->isGranted('ROLE_170-3137'))
        {
            $rolesPermitidos[] = 'ROLE_170-3137';
        }
        //MODULO 170 - asignacionVehicular/eliminarAsignacionVehiculoCuadrilla
        if(true === $this->get('security.context')->isGranted('ROLE_170-3597'))
        {
            $rolesPermitidos[] = 'ROLE_170-3597';
        }
        
        //MODULO 170 - cuadrilla/inactivar (dejar a la cuadrilla libre)
        if(true === $this->get('security.context')->isGranted('ROLE_170-4957'))
        {
            $rolesPermitidos[] = 'ROLE_170-4957';
        }
        //MODULO 170 - cuadrilla/reactivar (volver a la cuadrilla operativa)
        if(true === $this->get('security.context')->isGranted('ROLE_170-4977'))
        {
            $rolesPermitidos[] = 'ROLE_170-4977';
        }

        if ($strEsGestion == 'SI')
        {
            $arrayDefaultDepartamentos  = array('Operaciones Urbanas', 'Tecnica Sucursal');
            $boolCheckDepartamento      = in_array($strNombreDepartamento, $arrayDefaultDepartamentos);

            $strRenderVista  = 'administracionBundle:AdmiCuadrilla:gestion.html.twig';
            $boolTurnoActivo = true;

            if ($strNombreDepartamento == 'Operaciones Urbanas')
            {
                $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
                $boolTurnoActivo        = $serviceAdministracion->isCoordinador($intIdPersonEmpresaRol);
            }

            if (!$boolTurnoActivo || !$boolCheckDepartamento)
            {
                return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                      'mensaje' => 'No tiene permisos para usar esta función.'));
            }
            
        }

        return $this->render( $strRenderVista, 
                              array(
                                        'item'                   => $entityItemMenu,
                                        'rolesPermitidos'        => $rolesPermitidos,
                                        'strCargo'               => $strCargo,
                                        'intIdPersonaEmpresaRol' => $intIdPersonEmpresaRol,
                                        'strNombreArea'          => $strNombreArea,
                                        'strCategoriaTransporte' => self::CATEGORIA_ELEMENTO_TRANSPORTE,
                                        'strNombreDepartamento'  => $strNombreDepartamento
                                    )
                            );
    }


    /**
     * @Secure(roles="ROLE_170-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todas las cuadrillas creadas o que corresponden al usuario logueado.
     *
     * @return Response 
     *
     * @version 1.0 Version Inicial
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se cambia la opción para que solo muestre las cuadrillas del usuario logueado
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 27-10-2015 - Se cambia para que retorne la información sobre los vehículos asignados a las cuadrillas
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 06-11-2015 - Se cambia para que retorne la información del activo fijo (Vehículo o Moto) asignado a la cuadrilla el cual es
     *                           referenciado de la tabla 'INFO_ELEMENTO'
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 28-12-2015 - Se muestran las horas de los turnos de cada cuadrilla
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 04-01-2017 - Se agrega el campo strEstaLibre en el arreglo con la información de las cuadrillas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 02-04-2018 - Se agrega la informacion si las cuadrillas pertenecen a HAL
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.8 29-11-2019 - Se agrega código de programación para obtener preferencia de cada cuadrilla.
     * 
     * 
     * @author Modificado: Andrés Montero H <amontero@telconet.ec>
     * @version 1.9 27-10-2020 - Se elimina validaciones y registro de preferencia para las cuadrillas.
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.8 17-01-2023 - Se modifica las busqueda inical a los estados 'Activo' y 'Prestado',
     *                           con la finalidad de no mostrar las cuadrillas con estado 'Eliminado'.
     *                           Se valida si la solicitud corresponde a gestión de cuadrillas, para limitar
     *                           la búsquedas de cuadrillas en base a la oficina, departamento u oficina y departamento.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.9 06-06-2023 - Se agrega parametro del departamento para su posterior uso.
     */
    public function gridAction()
    {
        $jsonResponse    = new JsonResponse();
        $arrayCuadrillas = array();

        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strEstadoActivo   = 'Activo';

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strEmpresaCod         = $objSession->get('idEmpresa');

        $strNombre = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : "";
        $strEstado = $objRequest->query->get('estado') ? $objRequest->query->get('estado') : 'multiple';
        $intIdZona = $objRequest->query->get('idZona') ? $objRequest->query->get('idZona') : null;

        $intStart = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
        $strEsGestion = $objRequest->query->get('esGestion') ? $objRequest->query->get('esGestion') : 'NO';

        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $strEmpresaCod);
        
        $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
        $intDepartamentoId      = $arrayEmpleado['ID_DEPARTAMENTO'];
        $intOficinaId           = $arrayEmpleado['ID_OFICINA'];

        $strNombreDepartamento1  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento1,'');
        $boolDepConfigHE  = false;
        if(count($arrayDepConfigHE['registros']) > 0)
        {
        $boolDepConfigHE  = true;
        }
        
        $arrayParametros = array(
                                    'intCoordinadorPrincipal' => $intIdPersonEmpresaRol,
                                    'intStart'                => $intStart,
                                    'intLimit'                => $intLimit,
                                    'criterios'               => array( 'nombre' => $strNombre, 'estado' => $strEstado )
                                );
        
        if ($strEsGestion == 'SI')
        {
            $arrayParametros['intCoordinadorPrincipal']                 = null;
            $arrayParametros['criterios']['excluirCoordinadorPrestado'] = 'SI';
            $arrayParametros['intCoordinadorPrestado']                  = $intIdPersonEmpresaRol;
            $arrayParametros['criterios']['strBuscarPor']               = 'departamento';
            $arrayParametros['criterios']['intDepartamentoId']          = $intDepartamentoId;
            $arrayParametros['criterios']['estado']                     = 'multiple';
            $arrayParametros['criterios']['intIdZona']                  = $intIdZona;
            $arrayParametros['criterios']['strNombreDepartamento']      = $strNombreDepartamento;

            $arrayDefaultDepartamentos  = array('Operaciones Urbanas', 'Fiscalizacion', 'GIS');
            $boolCheckDepartamento      = in_array($strNombreDepartamento, $arrayDefaultDepartamentos);
            $boolTurnoActivo            = false;

            if ($strNombreDepartamento == 'Operaciones Urbanas')
            {
                $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
                $boolTurnoActivo        = $serviceAdministracion->isCoordinador($intIdPersonEmpresaRol);
            }

            if (($boolCheckDepartamento && !$boolTurnoActivo) ||
                 !$boolCheckDepartamento)
            {
                $arrayParametros['criterios']['strBuscarPor']               = 'soloOficina';
                $arrayParametros['criterios']['intOficinaId']               = $intOficinaId;
                $arrayParametros['criterios']['estado']                     = 'soloPrestado';
            }
        }



        $arrayResultados = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->getCuadrillasByCriterios($arrayParametros);

        $arrayRegistros = $arrayResultados['registros'];

        if($arrayRegistros)
        {
            foreach($arrayRegistros as $objDato)
            {
                $arrayItem      = array();
                $strNombreZona  = "";
                $strNombreTarea = "";
                $intIdZona      = "";
                $intIdTarea     = "";

                if( $objDato->getZonaId() )
                {
                    $intIdZona=$objDato->getZonaId();
                    $strNombreZona = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiZona')->find($intIdZona));
                }
                elseif( $objDato->getTareaId() )
                {
                    $intIdTarea=$objDato->getTareaId();
                    $strNombreTarea = sprintf("%s", $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTarea));
                }

                $strNombreDepartamento = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                                 ->find($objDato->getDepartamentoId()));
                $arrayItem['boolDepConfigHE']        = $boolDepConfigHE;
                $arrayItem['intIdZona']              = $intIdZona;
                $arrayItem['strZona']                = $strNombreZona;
                $arrayItem['strTarea']               = $strNombreTarea;
                $arrayItem['intIdTarea']             = $intIdTarea;
                $arrayItem['strEsHal']               = is_null($objDato->getEsHal()) ? "N" : $objDato->getEsHal();
                $arrayItem['strCodigo']              = $objDato->getCodigo();
                $arrayItem['strEstado']              = $objDato->getEstado();
                $arrayItem['strTurnoInicio']         = $objDato->getTurnoHoraInicio() ? $objDato->getTurnoHoraInicio() : "";
                $arrayItem['strTurnoFin']            = $objDato->getTurnoHoraFin() ? $objDato->getTurnoHoraFin() : "";
                $arrayItem['strFechaInicio']         = $objDato->getTurnoInicio() ? date("d-m-Y", strtotime($objDato->getTurnoInicio())) : "";
                $arrayItem['strFechaFin']            = $objDato->getTurnoFin() ? date("d-m-Y", strtotime($objDato->getTurnoFin())) : "";
                $arrayItem['intIdCuadrilla']         = $objDato->getId();
                $arrayItem['intIdDepartamento']      = $objDato->getDepartamentoId()? $objDato->getDepartamentoId(): "";
                $arrayItem['strDepartamento']        = $strNombreDepartamento;
                $arrayItem['strNombreCuadrilla']     = $objDato->getNombreCuadrilla();
                $arrayItem['coordinadorPrincipalId'] = $intIdPersonEmpresaRol;
                $arrayItem['coordinadorPrestadoId']  = $objDato->getCoordinadorPrestadoId();
                $arrayItem['strActivoAsignado']      = 'Sin Asignación';
                $arrayItem['intIdActivoAsignado']    = 0;
                $arrayItem['strTipoActivoAsignado']  = '';
                $arrayItem['intIdDetAsignacionVehicular']   = '';
                $arrayItem['strDISCOVehiculo']              = '';
               $arrayItem['strEsSatelite']               = is_null($objDato->getEsSatelite()) ? "N" : $objDato->getEsSatelite();

                
                
                $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy( 
                                                                        array( 
                                                                                'estado'        => $strEstadoActivo, 
                                                                                'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                                'detalleValor'  => $arrayItem['intIdCuadrilla'],
                                                                             ) 
                                                                );

                if( $objDetalleElemento )
                {
                    $arrayItem['intIdDetAsignacionVehicular']   = $objDetalleElemento->getId();
                    $intIdActivoActual = $objDetalleElemento->getElementoId();

                    $objActivoActual = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->findOneBy( array('id' => $intIdActivoActual, 'estado' => $strEstadoActivo) );

                    if( $objActivoActual )
                    {
                        $arrayItem['intIdActivoAsignado']   = $intIdActivoActual;
                        
                        $strNombreTipoElemento              = ucwords( strtolower( $objActivoActual->getModeloElementoId()
                                                                                      ->getTipoElementoId()->getNombreTipoElemento() ) );

                        $arrayItem['strTipoActivoAsignado'] = $strNombreTipoElemento;
                        $arrayItem['strActivoAsignado']     = $objActivoActual->getNombreElemento();
                        
                        $objDetalleDiscoVehiculo = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy( 
                                                                        array( 
                                                                                'elementoId'    => $intIdActivoActual,
                                                                                'estado'        => $strEstadoActivo, 
                                                                                'detalleNombre' => 'DISCO',
                                                                             ) 
                                                                );
                        if($objDetalleDiscoVehiculo)
                        {
                            $arrayItem['strDISCOVehiculo']              = $objDetalleDiscoVehiculo->getDetalleValor();
                        }
                        
                    }//( $objActivoActual )
                }//( $objDetalleElemento )
                
                
                //Url de las Acciones permitidas
                $arrayItem['strUrlVer']    = '';
                $arrayItem['strUrlEditar'] = '';

                if( $arrayItem['strEstado'] == 'Activo' )
                {
                    $arrayItem['strUrlVer']    = $this->generateUrl('admicuadrilla_show', array('id' => $arrayItem['intIdCuadrilla']));
                    $arrayItem['strUrlEditar'] = $this->generateUrl('admicuadrilla_edit', array('id' => $arrayItem['intIdCuadrilla']));
                }
                elseif( $arrayItem['strEstado'] == 'Prestado' )
                {
                    $arrayItem['strUrlVer']    = $this->generateUrl('admicuadrilla_show', array('id' => $arrayItem['intIdCuadrilla']));

                    if( $intIdPersonEmpresaRol == $arrayItem['coordinadorPrestadoId'] )
                    {
                        $arrayItem['strUrlEditar'] = $this->generateUrl('admicuadrilla_edit', array('id' => $arrayItem['intIdCuadrilla']));
                        $arrayItem['strEstado']    = 'Es Prestamo';
                    }  

                    if ( $strEsGestion == 'SI' )
                    {
                        $arrayItem['strUrlEditar'] = $this->generateUrl('admicuadrilla_edit', array('id' => $arrayItem['intIdCuadrilla'])); 
                    }
                }
                $arrayItem['strEstaLibre']  = $objDato->getEstaLibre();
                $arrayCuadrillas[] = $arrayItem;
            }
        }

        $jsonResponse->setData( array( 'total' => $arrayResultados['total'], 'cuadrillas' => $arrayCuadrillas) );

        return $jsonResponse;
    }


    /**
     * asignarAHalAction
     * Funcion que define si una cuadrilla es asignada a HAL
     *
     * @return json $arrayRespuesta
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 03-04-2018
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 10-04-2018 - Se agrega una nueva funcionalidad de notificaciones para HAL.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 13-07-2018 - Se agrega una nueva validación, para no desmarcar una cuadrilla hal si tiene tareas asignadas.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 15-08-2018 - Se agrega una nueva validación, para no marcar una cuadrilla a hal si no tiene un Líder asignado.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 03-09-2018 - Se agrega una nueva validación, para no desmarcar una cuadrilla hal si tiene al menos una planificación Activa.
     *
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.5 28-11-2019 - Se agrega cambio de preferencia para las cuadrillas y se valida que solo se permite cambiar 
     *                           preferencia para las cuadrillas que no sean HAL.
     * 
     * @author Modificado: Andrés Montero H <amontero@telconet.ec>
     * @version 1.6 27-10-2020 - Se elimina validaciones y registro de preferencia para las cuadrillas.
     */
    public function asignarAHalAction()
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
        $strTramaCuadrillas = $objPeticion->get('tramaCruadrillas');
        $strValorHal        = $objPeticion->get('valorHal');
        $arrayCuadrillas    = explode("|", $strTramaCuadrillas);
        $objResponse        = new JsonResponse();
        $arrayRespuesta     = array();
        $serviceUtil        = $this->get('schema.Util');
        $serviceSoporte     = $this->get('soporte.SoporteService');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $arrayCuadriSinAct  = array();
        $arrayCuadriAct     = array();
        $boolExisteLider    = false;
        $arraySinIntegrant  = array();
        $arraySinLider      = array();
        $arrayPlanifActiva  = array();
        $boolBandera        = false;

        $emComercial->getConnection()->beginTransaction();

        try
        {
            for ($i = 0; $i <= count($arrayCuadrillas); $i++)
            {
                if (!empty($arrayCuadrillas[$i]))
                {
                    $objAdmiCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($arrayCuadrillas[$i]);

                    if (is_object($objAdmiCuadrilla))
                    {
                        if (strtoupper($strValorHal) === 'N' && strtoupper($objAdmiCuadrilla->getEsHal()) === 'S')
                        {
                            $arrayTareasHal = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                                ->getPlanificacionTareasHal(array ('intAsignadoId'     => $objAdmiCuadrilla->getId(),
                                                                   'strTipoAsignado'   => 'CUADRILLA',
                                                                   'arrayEstadosTarea' =>  array('Finalizada','Cancelada','Rechazada','Anulada'),
                                                                   'strEstadoCab'      => 'Activo',
                                                                   'strEstadoDet'      => 'Activo'));

                            if ($arrayTareasHal['status'] === 'ok' && !empty($arrayTareasHal['planificacion']))
                            {
                                $arrayCuadriSinAct[] = $objAdmiCuadrilla->getNombreCuadrilla();
                                $boolBandera = true;
                                continue;
                            }

                            //Verificamos si la cuadrilla tiene al menos una planificación Activa
                            $objFechaNow = new \DateTime('now');
                            $arrayPlanificacionTrabajo = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                                ->getSolicitarTrabajoCuadrilla(array ('intIdCuadrilla'     => $objAdmiCuadrilla->getId(),
                                                                      'strFechaIni'        => date_format($objFechaNow, 'Y-m-d'),
                                                                      'strEstadoPlanifCab' => 'Activo'));

                            if ($arrayPlanificacionTrabajo['mensaje'] === 'ok' && !empty($arrayPlanificacionTrabajo['planificacion'])
                                && count($arrayPlanificacionTrabajo['planificacion'] > 0))
                            {
                                $arrayPlanifActiva[] = $objAdmiCuadrilla->getNombreCuadrilla();
                                $boolBandera = true;
                                continue;
                            }
                        }

                        if (strtoupper($strValorHal) === 'S' && (strtoupper($objAdmiCuadrilla->getEsHal()) === 'N'
                            || is_null($objAdmiCuadrilla->getEsHal())))
                        {
                            $arrayIntegrantesCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                                ->getIntegrantesCuadrilla($objAdmiCuadrilla->getId());

                            //Validamos si no existen integrantes en la cuadrilla
                            if (empty($arrayIntegrantesCuadrilla) || count($arrayIntegrantesCuadrilla) < 1)
                            {
                                $arraySinIntegrant[] = $objAdmiCuadrilla->getNombreCuadrilla();
                                $boolBandera = true;
                                continue;
                            }

                            //Verificamos el líder de la cuadrilla
                            foreach ($arrayIntegrantesCuadrilla as $arrayDato)
                            {
                                $arrayLiderCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrilla')
                                    ->getLiderCuadrilla($arrayDato['idPersona']);

                                if (!empty($arrayLiderCuadrilla) && count($arrayLiderCuadrilla) > 0)
                                {
                                    $boolExisteLider = true;
                                    break;
                                }
                            }

                            if (!$boolExisteLider)
                            {
                                $arraySinLider[] = $objAdmiCuadrilla->getNombreCuadrilla();
                                $boolBandera = true;
                                continue;
                            }
                        }

                        $arrayCuadriAct[] = $objAdmiCuadrilla->getId();
                        $objAdmiCuadrilla->setEsHal($strValorHal);
                        $objAdmiCuadrilla->setUsrModificacion($strUserSession);
                        $objAdmiCuadrilla->setFeUltMod(new \DateTime('now'));
                        $emComercial->persist($objAdmiCuadrilla);
                        $emComercial->flush();
                    }
                }
            }

            $emComercial->getConnection()->commit();

            /*========================= INICIO NOTIFICACION HAL ==========================*/
            if (!empty($arrayCuadriAct))
            {
                foreach ($arrayCuadriAct as $intIdCuadrilla)
                {
                    $serviceSoporte->notificacionesHal(
                            array ('strModulo' => 'cuadrilla',
                                   'strUser'   =>  $strUserSession,
                                   'strIp'     =>  $strIpCreacion,
                                   'arrayJson' =>  array ('metodo' => 'actualizo',
                                                          'id'     => $intIdCuadrilla)));
                }
            }
            /*========================== FIN NOTIFICACION HAL ============================*/

            $arrayRespuesta["estado"]  = "Ok";
            $arrayRespuesta["mensaje"] = "Transacción Exitosa";

            if ($boolBandera)
            {
                if (!empty($arrayCuadriSinAct) && count($arrayCuadriSinAct) > 0)
                {
                    $strMensaje .= 'Las siguientes cuadrillas:<br />';

                    foreach ($arrayCuadriSinAct as $strValor)
                    {
                        if (is_null($strSinPlanificacion))
                        {
                            $strSinPlanificacion = '[<b style="color:green;">'.$strValor.'</b>';
                        }
                        else
                        {
                            $strSinPlanificacion .= ', <b style="color:green;">'.$strValor.'</b>';
                        }
                    }

                    $strMensaje .= $strSinPlanificacion.']<br />No se pueden modificar, por motivos que tienen tareas asignadas.<br /><br />';
                }

                if (!empty($arraySinLider) && count($arraySinLider) > 0)
                {
                    $strMensaje .= 'Las siguientes cuadrillas:<br />';

                    foreach ($arraySinLider as $strValor)
                    {
                        if (is_null($strSinLider))
                        {
                            $strSinLider = '[<b style="color:green;">'.$strValor.'</b>';
                        }
                        else
                        {
                            $strSinLider .= ', <b style="color:green;">'.$strValor.'</b>';
                        }
                    }

                    $strMensaje .= $strSinLider.']<br />No pueden ser HAL, por motivos que no tienen un Líder asignado.<br /><br />';
                }

                if (!empty($arrayPlanifActiva) && count($arrayPlanifActiva) > 0)
                {
                    $strMensaje .= 'Las siguientes cuadrillas:<br />';

                    foreach ($arrayPlanifActiva as $strValor)
                    {
                        if (is_null($strPlanifActiva))
                        {
                            $strPlanifActiva = '[<b style="color:green;">'.$strValor.'</b>';
                        }
                        else
                        {
                            $strPlanifActiva .= ', <b style="color:green;">'.$strValor.'</b>';
                        }
                    }

                    $strMensaje .= $strPlanifActiva.']<br />No se pueden modificar, por motivos que tiene al menos una planificación Activa.<br />'
                        . 'por favor eliminar las planificaciones desde hoy en adelante<br /><br />';
                }

                $arrayRespuesta["mensaje"] = $strMensaje;
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
                                      'AdmiCuadrillaController->asignarAHalAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $arrayRespuesta["estado"]  = "Error";
            $arrayRespuesta["mensaje"] = "Se produjo un error en la ejecución.";
        }

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
     * Documentación para el método 'verificarPlanificacionAction'.
     *
     * Verifica que la cuadrilla no contengan tereas o planificacion HAL
     * al momento que se va a recuperar una cuadrilla
     * 
     * @return Response 
     *
     * @author Daniel Guzmán<ddguzman@telconet.ec>
     * @version 1.0 19-01-2023
     */
    public function verificarPlanificacionAction()
    {
        $objResponse      = new Response();
        $objRequest       = $this->get('request');
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $emSoporte        = $this->getDoctrine()->getManager('telconet_soporte');
        $intIdCuadrilla   = $objRequest->get('cuadrilla') ? $objRequest->request->get('cuadrilla') : 0;
        $strValorHal      = $objRequest->get('valorHal') ? $objRequest->get('valorHal') : '';
        $strMensaje       = '<b>No se puede recuperar la cuadrilla</b><br/><br/>';
        $intContadorError = 0;

        try
        {
            $objAdmiCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrilla);

            if( is_object($objAdmiCuadrilla) )
            {
                $strValorHal        = strtoupper($objAdmiCuadrilla->getEsHal());
                $intIdCuadrilla     = $objAdmiCuadrilla->getId();
                $arrayEstadosTarea  = array('Finalizada','Cancelada','Rechazada','Anulada');

                $arrayParametros                        = array();
                $arrayParametros['intAsignadoId']       = $intIdCuadrilla;
                $arrayParametros['strTipoAsignado']     = 'CUADRILLA';
                $arrayParametros['arrayEstadosTarea']   = $arrayEstadosTarea;
                $arrayParametros['strEstadoCab']        = 'Activo';
                $arrayParametros['strEstadoDet']        = 'Activo';
                

                if ( $strValorHal == 'S')
                {
                    $arrayTareasHal = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                                                ->getPlanificacionTareasHal($arrayParametros);
                
                    if ($arrayTareasHal['status'] === 'ok' && !empty($arrayTareasHal['planificacion']))
                    {
                        $strMensaje .= 'La cuadrilla tienen tareas asignadas.<br />';
                        $intContadorError++;
                    }

                    $objFechaNow = date_format(new \DateTime('now'), 'Y-m-d');

                    $arrayParametrosPlanificacion                       = array();
                    $arrayParametrosPlanificacion['intIdCuadrilla']     = $intIdCuadrilla;
                    $arrayParametrosPlanificacion['strFechaIni']        = $objFechaNow;
                    $arrayParametrosPlanificacion['strEstadoPlanifCab'] = 'Activo';

                    $arrayPlanificacionTrabajo = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                                                            ->getSolicitarTrabajoCuadrilla($arrayParametrosPlanificacion);

                    if ($arrayPlanificacionTrabajo['mensaje'] === 'ok' && !empty($arrayPlanificacionTrabajo['planificacion'])
                                && count($arrayPlanificacionTrabajo['planificacion'] > 0))
                    {
                        $strMensaje .= 'La cuadrilla tiene al menos una planificación Activa, <br />'
                                        . 'por favor eliminar las planificaciones desde hoy en adelante. <br />';
                        $intContadorError++;
                    }
                }
                
            }

            if( $intContadorError == 0 )
            {
                $strMensaje = 'OK';
            }
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }

        $objResponse->setContent( $strMensaje );

        return $objResponse;
    }


   /**
    * asignarASateliteAction
    * Funcion que define si una cuadrilla es asignada a Satelite
    *
    * @return json $arrayRespuesta
    *
    * @author Jeampier Carriel <jacarriel@telconet.ec>
    * @version 1.0 29-11-2021
    *
    */

   public function asignarASateliteAction()
   {
       $emComercial         = $this->getDoctrine()->getManager('telconet');
       $objPeticion         = $this->getRequest();
       $objSession          = $objPeticion->getSession();
       $strUserSession      = $objSession->get('user');
       $strIpCreacion       = $objPeticion->getClientIp();
       $strTramaCuadrillas  = $objPeticion->get('tramaCruadrillas');
       $strValorSatelite    = $objPeticion->get('valorSatelite');
       $arrayCuadrillas     = explode("|", $strTramaCuadrillas);
       $objResponse         = new JsonResponse();
       $serviceUtil         = $this->get('schema.Util');
       $emSoporte           = $this->getDoctrine()->getManager('telconet_soporte');
       $arrayCuadriSinAct   = array();
       $arrayCuadriAct      = array();
       $boolExisteLider     = false;
       $arraySinIntegrant   = array();
       $arraySinLider       = array();
       $arrayPlanifActiva   = array();
       $boolBandera         = false;
       $strMensaje          = "";
       $arrayRespuesta      = array();
       $arrayDatos          = array();
       $arrayParametros['user']        = $this->container->getParameter('user_soporte');
       $arrayParametros['pass']        = $this->container->getParameter('passwd_soporte');
       $arrayParametros['db']          = $this->container->getParameter('database_dsn');  

       $emComercial->getConnection()->beginTransaction();

       try
       {
           for ($intCount = 0; $intCount <= count($arrayCuadrillas); $intCount++)
           {
               if (!empty($arrayCuadrillas[$intCount]))
               {
                   $objAdmiCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($arrayCuadrillas[$intCount]);

                   if (is_object($objAdmiCuadrilla))
                   {
                       if (strtoupper($strValorSatelite) === 'N' || strtoupper($strValorSatelite) === 'S')
                       {
                        
                        $arrayDatos = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($arrayCuadrillas[$intCount]); 
                        
                        if($arrayDatos['idPersona'] != '')
                        {
                            $arrayParametros['intPersona']=$arrayDatos['idPersona'];

                            $arrayTareasSatelite = $emSoporte->getRepository('schemaBundle:InfoPersona')
                                                                ->getTareasPorPersona($arrayParametros);

                                if (!empty($arrayTareasSatelite))
                                {
                                    $arrayCuadriSinAct[] = $objAdmiCuadrilla->getNombreCuadrilla();
                                    $boolBandera = true;
                                    continue;
                                }
                        }

                        //Verificamos si la cuadrilla tiene al menos una planificación Activa
                        $objFechaNow = new \DateTime('now');
                        $arrayPlanificacionTrabajo = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                               ->getSolicitarTrabajoCuadrilla(array ('intIdCuadrilla'     => $objAdmiCuadrilla->getId(),
                                   'strFechaIni'        => date_format($objFechaNow, 'Y-m-d'),
                                   'strEstadoPlanifCab' => 'Activo'));

                        if ($arrayPlanificacionTrabajo['mensaje'] === 'ok' && !empty($arrayPlanificacionTrabajo['planificacion'])
                               && count($arrayPlanificacionTrabajo['planificacion'] > 0))
                           {
                               $arrayPlanifActiva[] = $objAdmiCuadrilla->getNombreCuadrilla();
                               $boolBandera = true;
                               continue;
                           }
                       }

                       if (strtoupper($strValorSatelite) === 'S' && (strtoupper($objAdmiCuadrilla->getEsSatelite()) === 'N'
                               || is_null($objAdmiCuadrilla->getEsSatelite())))
                       {
                           $arrayIntegrantesCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                               ->getIntegrantesCuadrilla($objAdmiCuadrilla->getId());

                           //Validamos si no existen integrantes en la cuadrilla
                           if (empty($arrayIntegrantesCuadrilla) || count($arrayIntegrantesCuadrilla) < 1)
                           {
                               $arraySinIntegrant[] = $objAdmiCuadrilla->getNombreCuadrilla();
                               $boolBandera = true;
                               continue;
                           }

                           //Verificamos el líder de la cuadrilla
                           foreach ($arrayIntegrantesCuadrilla as $arrayDato)
                           {
                               $arrayLiderCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrilla')
                                   ->getLiderCuadrilla($arrayDato['idPersona']);

                               if (!empty($arrayLiderCuadrilla) && count($arrayLiderCuadrilla) > 0)
                               {
                                   $boolExisteLider = true;
                                   break;
                               }
                           }

                           if (!$boolExisteLider)
                           {
                               $arraySinLider[] = $objAdmiCuadrilla->getNombreCuadrilla();
                               $boolBandera = true;
                               continue;
                           }
                       }

                       $arrayCuadriAct[] = $objAdmiCuadrilla->getId();
                       $objAdmiCuadrilla->setEsSatelite($strValorSatelite);
                       $objAdmiCuadrilla->setUsrModificacion($strUserSession);
                       $objAdmiCuadrilla->setFeUltMod(new \DateTime('now'));
                       $emComercial->persist($objAdmiCuadrilla);
                       $emComercial->flush();
                   }
               }
           }

           $emComercial->getConnection()->commit();

           $arrayRespuesta["estado"]  = "Ok";
           $arrayRespuesta["mensaje"] = "Transacción Exitosa";

           if ($boolBandera)
           {
               if (!empty($arrayCuadriSinAct) && count($arrayCuadriSinAct) > 0)
               {
                   $strMensaje .= 'Las siguientes cuadrillas:<br />';

                   foreach ($arrayCuadriSinAct as $strValor)
                   {
                       if (is_null($strSinPlanificacion))
                       {
                           $strSinPlanificacion = '[<b style="color:green;">'.$strValor.'</b>';
                       }
                       else
                       {
                           $strSinPlanificacion .= ', <b style="color:green;">'.$strValor.'</b>';
                       }
                   }

                   $strMensaje .= $strSinPlanificacion.']<br />No se pueden modificar, por motivos que tienen tareas asignadas.<br /><br />';
               }

               if (!empty($arraySinLider) && count($arraySinLider) > 0)
               {
                   $strMensaje .= 'Las siguientes cuadrillas:<br />';

                   foreach ($arraySinLider as $strValor)
                   {
                       if (is_null($strSinLider))
                       {
                           $strSinLider = '[<b style="color:green;">'.$strValor.'</b>';
                       }
                       else
                       {
                           $strSinLider .= ', <b style="color:green;">'.$strValor.'</b>';
                       }
                   }

                   $strMensaje .= $strSinLider.']<br />No pueden ser Satélite, por motivos que no tienen un Líder asignado.<br /><br />';
               }

               if (!empty($arrayPlanifActiva) && count($arrayPlanifActiva) > 0)
               {
                   $strMensaje .= 'Las siguientes cuadrillas:<br />';

                   foreach ($arrayPlanifActiva as $strValor)
                   {
                       if (is_null($strPlanifActiva))
                       {
                           $strPlanifActiva = '[<b style="color:green;">'.$strValor.'</b>';
                       }
                       else
                       {
                           $strPlanifActiva .= ', <b style="color:green;">'.$strValor.'</b>';
                       }
                   }

                   $strMensaje .= $strPlanifActiva.']<br />No se pueden modificar, por motivos que tiene al menos una planificación Activa.<br />'
                       . 'por favor eliminar las planificaciones desde hoy en adelante<br /><br />';
               }

               $arrayRespuesta["mensaje"] = $strMensaje;
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
               'AdmiCuadrillaController->asignarASateliteAction',
               $objEx->getMessage(),
               $strUserSession,
               $strIpCreacion);

           $arrayRespuesta["estado"]  = "Error";
           $arrayRespuesta["mensaje"] = "Se produjo un error en la ejecución.";
       }

       $objResponse->setData($arrayRespuesta);

       return $objResponse;
   }


    /**
     * @Secure(roles="ROLE_170-2")
     * 
     * Documentación para el método 'newAction'.
     *
     * Muestra usado para mostrar el formulario vacío para crear una cuadrilla.
     *
     * @return Response
     *
     * @version 1.0 Version Inicial 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se le añade al formulario los campos de 'Zona' y 'Departamento'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 17-11-2015 - Se modifica para que retorne al personal del Coordinador Principal y no del Ayudante Coordinador.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     */
    public function newAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombreArea         = "Tecnico";
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);

        $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol) );

        $arrayParametros = array('intEmpresaCod' => $intIdEmpresa);

        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("170", "1");
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;
        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        } 
        $entity         = new AdmiCuadrilla();
        $form           = $this->createForm(new AdmiCuadrillaType($arrayParametros), $entity);

        return $this->render('administracionBundle:AdmiCuadrilla:new.html.twig', array(
                                                                                          'item'                  => $entityItemMenu,
                                                                                          'cuadrilla'             => $entity,
                                                                                          'form'                  => $form->createView(),
                                                                                          'strNombreArea'         => $strNombreArea,
                                                                                          'strCargo'              => $strCargo,
                                                                                          'intIdJefeSeleccionado' => $intIdPersonEmpresaRol,
                                                                                          'boolDepConfigHE'       => $boolDepConfigHE,
                                                                                       )
                            );
    }


    /** 	
    * Documentación para la funcion getCuadrillasActivasAction(). 	
    *  	
    * Esta funcion retorna todas las cuadrillas diferentes de estado Eliminado 	
    *  	
    * @author Richard Cabrera <rcabrera@telconet.ec> 	
    * @version 1.0 27-10-2015 	
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 27-10-2015 Se ralizan ajustes para habilitar la busqueda del combo de cuadrillas del modulo de Tareas
    *
    */
    public function getCuadrillasActivasAction()  	
    { 	
        $strRespuesta        = new Response();

        $strRespuesta        ->headers->set('Content-Type', 'text/json');               	
        $emComercial         = $this->getDoctrine()->getManager("telconet"); 
        $strPeticion         = $this->get('request');         	
        $strEstado           = $strPeticion->query->get('estado');
        $strNombreCuadrilla  = $strPeticion->query->get('query');
        $objJson             = $emComercial->getRepository('schemaBundle:AdmiCuadrilla') 	
                                           ->generarJsonCuadrillasActivas($strEstado,$strNombreCuadrilla);
        $strRespuesta  ->setContent($objJson);

        return $strRespuesta; 	
    } 	    


    /**
     * @Secure(roles="ROLE_170-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda una cuadrilla.
     *
     * @return Response 
     *
     * @version 1.0 Version Inicial
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se guarda la cuadrilla incluyendo el historial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 05-01-2016 - Se guarda la fecha y hora de inicio y la fecha y hora de fin de la cuadrilla
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 05-01-2017 - Se guarda el campo estaLibre por defecto como 'NO' al crear la cuadrilla
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 10-04-2018 - Se modifica el método agregando la funcionalidad de notificacion a Hal.
     *
     */
    public function createAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceSoporte    = $this->get('soporte.SoporteService');

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("170", "1");

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento     = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strNombreArea         = "Tecnico";
        $strEstadoActivo       = 'Activo';
        $strCargo              = $emComercial->getRepository('schemaBundle:AdmiRol')
                                             ->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol) );

        $objCoordinadorPrincipal = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonEmpresaRol);

        $arrayParametros    = array('intEmpresaCod' => $intIdEmpresa);
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;
        $arrayDatosTrama = [];
        $intSecuencialId = 0;

        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        } 

        $objCuadrilla = new AdmiCuadrilla();
        $form         = $this->createForm(new AdmiCuadrillaType($arrayParametros), $objCuadrilla);

        $emComercial->getConnection()->beginTransaction();	

        try
        {
            $intIdDepartamentoSeleccionado = $objRequest->get('departamentoId');
            $intIdZonaSeleccionado         = $objRequest->get('zonaId');
            $intIdTareaSeleccionada        = $objRequest->get('tareaId');
            $strTipoCuadrilla              = $objRequest->get('tipoCuadrilla');

            $strSecuencial      = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->getSecuencialParaCodigoCuadrilla();
            $objMotivoCuadrilla = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Creacion de cuadrilla');
            $objMotivoPersona   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Se le asigna nueva cuadrilla');

            $intIdCoordinadorPrincipal = null;
            if( $objCoordinadorPrincipal )
            {
                $intIdCoordinadorPrincipal     = $objCoordinadorPrincipal->getId();
                $strNombreCoordinadorPrincipal = $objCoordinadorPrincipal->getPersonaId() 
                                                 ? $objCoordinadorPrincipal->getPersonaId()->getInformacionPersona() : '';
            }

            $intIdMotivoCuadrilla = null;
            if( $objMotivoCuadrilla )
            {
                $intIdMotivoCuadrilla = $objMotivoCuadrilla->getId();
            }

            $intIdMotivoPersona = null;
            if( $objMotivoPersona )
            {
                $intIdMotivoPersona = $objMotivoPersona->getId();
            }

            $strNombreCuadrilla = $objRequest->get('nombreCuadrilla');
            $strCodigoCuadrilla = "CUA-".$intIdDepartamento."-".$strSecuencial;
            $strUserSession     = $objSession->get('user');
            $strIpUserSession   = $objRequest->getClientIp();
            $datetimeActual     = new \DateTime('now');

            $strTurnoHoraInicio     = $objRequest->get('horaInicioTurnoCuadrilla') ? trim($objRequest->get('horaInicioTurnoCuadrilla')) : '';
            $strTurnoHoraFin        = $objRequest->get('horaFinTurnoCuadrilla') ? trim($objRequest->get('horaFinTurnoCuadrilla')) : '';
            if ($boolDepConfigHE)
            {
                $strTurnoInicio   = '';
                $strTurnoFin      = '';
                if($objRequest->get('fechaInicioTurnoCuadrilla'))
                {
                    $strTurnoInicio   = date("Y-m-d", strtotime(trim($objRequest->get('fechaInicioTurnoCuadrilla'))));
                } 
                if($objRequest->get('fechaFinTurnoCuadrilla'))
                {
                    $strTurnoFin   = date("Y-m-d", strtotime(trim($objRequest->get('fechaFinTurnoCuadrilla'))));
                }  

                $arrayDiasSemana = $objRequest->get('diasSemana') ? $objRequest->get('diasSemana'): '';
                if ($arrayDiasSemana != '' )
                {
                    $arrayDiasSemana1   = $arrayDiasSemana[0];
                    $arrayDiasSemanaSeleccion = json_decode($arrayDiasSemana1, true);
                    $arrayDiasSemanaId = $arrayDiasSemanaSeleccion['valueDiasSemana'];
                }
                $objCuadrilla->setTurnoInicio($strTurnoInicio);
                $objCuadrilla->setTurnoFin($strTurnoFin);
            }
            else
            {
                $objCuadrilla->setTurnoInicio(null);
                $objCuadrilla->setTurnoFin(null);
            }

            $objCuadrilla->setCodigo($strCodigoCuadrilla);
            $objCuadrilla->setNombreCuadrilla($strNombreCuadrilla);
            $objCuadrilla->setDepartamentoId($intIdDepartamentoSeleccionado);
            $objCuadrilla->setCoordinadorPrincipalId($intIdCoordinadorPrincipal);
            $objCuadrilla->setCoordinadorPrestadoId(null);
            $objCuadrilla->setEstado($strEstadoActivo);
            $objCuadrilla->setFeCreacion($datetimeActual);
            $objCuadrilla->setUsrCreacion($strUserSession);
            $objCuadrilla->setIpCreacion($strIpUserSession);
            $objCuadrilla->setTurnoHoraInicio($strTurnoHoraInicio);
            $objCuadrilla->setTurnoHoraFin($strTurnoHoraFin);
            $objCuadrilla->setEstaLibre('NO');

            $strObservacionHistoCuadrilla = "Creaci&oacute;n de cuadrilla:<br/><br/>".
                                            "C&oacute;digo: ".$strCodigoCuadrilla."<br/>".
                                            "Nombre: ".$strNombreCuadrilla."<br/>".
                                            "Esta Libre: NO<br/>";

            if( $strTipoCuadrilla == 'zona' )
            {
                $objCuadrilla->setZonaId($intIdZonaSeleccionado);

                $strNombreZona                = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiZona')->find($intIdZonaSeleccionado));
                $strObservacionHistoCuadrilla .= "Zona: ".$strNombreZona."<br/>";
            }
            elseif( $strTipoCuadrilla == 'tarea' )
            {
                $objCuadrilla->setTareaId($intIdTareaSeleccionada);

                $strNombreTarea               = sprintf("%s", $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTareaSeleccionada));
                $strObservacionHistoCuadrilla .= "Tarea: ".$strNombreTarea."<br/>";

            }

            $strNombreDepartamento = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamentoSeleccionado));

            $strObservacionHistoCuadrilla .= "Departamento: ".$strNombreDepartamento."<br/>".
                                             "Coordinador Principal: ".$strNombreCoordinadorPrincipal."<br/>".
                                             "Estado: ".$strEstadoActivo."<br/>".
                                             "Hora Inicio: ".$strTurnoHoraInicio."<br/>".
                                             "Hora Fin: ".$strTurnoHoraFin."<br/><br/>";

            $emComercial->persist($objCuadrilla);
            $emComercial->flush();

            $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
            $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
            $objCuadrillaHistorial->setEstado($strEstadoActivo);
            $objCuadrillaHistorial->setFeCreacion($datetimeActual);
            $objCuadrillaHistorial->setUsrCreacion($strUserSession);
            $objCuadrillaHistorial->setObservacion($strObservacionHistoCuadrilla);
            $objCuadrillaHistorial->setMotivoId($intIdMotivoCuadrilla);
            $emComercial->persist($objCuadrillaHistorial);
            $emComercial->flush();

            if ($boolDepConfigHE)
            {
                $objHistoHorarioCuadrilla = new InfoHistoHorarioCuadrilla();
                $objHistoHorarioCuadrilla->setCuadrillaId($objCuadrilla);
                $objHistoHorarioCuadrilla->setFechaInicio(date("d-m-Y", strtotime($strTurnoInicio)));
                $objHistoHorarioCuadrilla->setHoraInicio($strTurnoHoraInicio);
                $objHistoHorarioCuadrilla->setFechaFin(date("d-m-Y", strtotime($strTurnoFin)));
                $objHistoHorarioCuadrilla->setHoraFin($strTurnoHoraFin);
                $objHistoHorarioCuadrilla->setEstado($strEstadoActivo);
                $objHistoHorarioCuadrilla->setUsrCreacion($strUserSession);
                $objHistoHorarioCuadrilla->setIpCreacion($strIpUserSession);
                $objHistoHorarioCuadrilla->setFechaCreacion($datetimeActual);
                $emComercial->persist($objHistoHorarioCuadrilla);
                $emComercial->flush();

                for ($intIndice=0; $intIndice < count($arrayDiasSemanaId); $intIndice++)
                {
                    $objInfoDiaSemanaCuadrillaNuevo = new InfoDiaSemanaCuadrilla();
                    $objInfoDiaSemanaCuadrillaNuevo->setCuadrillaId($objCuadrilla);
                    $objInfoDiaSemanaCuadrillaNuevo->setNumeroDiaId(intval($arrayDiasSemanaId[$intIndice]));
                    $objInfoDiaSemanaCuadrillaNuevo->setEstado('Activo');
                    $objInfoDiaSemanaCuadrillaNuevo->setFechaCreacion(new \DateTime('now'));
                    $objInfoDiaSemanaCuadrillaNuevo->setUsrCreacion($objSession->get('user'));
                    $objInfoDiaSemanaCuadrillaNuevo->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($objInfoDiaSemanaCuadrillaNuevo);
                    $emComercial->flush();
                }
            }
            if( $objCuadrilla )
            {
                $jsonIntegrantes  = json_decode($objRequest->get('empleados_integrantes'));
                $arrayIntegrantes = $jsonIntegrantes->encontrados;
                $strObservacionCuadrilla1 = 'Se agregaron los siguientes miembros a la cuadrilla:<br/>';
                $objTmpMotivo     = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                              ->findOneByNombreMotivo('Se agregan miembros a la cuadrilla');

                $intIdMotivo      = $objTmpMotivo->getId();

                foreach($arrayIntegrantes as $objIntegrante)
                {
                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findOneById($objIntegrante->intIdPersonaEmpresaRol);

                    if( $objInfoPersonaEmpresaRol )
                    {
                        $strCodigoCuadrillaAnterior = $objInfoPersonaEmpresaRol->getCuadrillaId() 
                                                      ? $objInfoPersonaEmpresaRol->getCuadrillaId()->getCodigo()
                                                      : null;
                        $strNombreUsuario = $objInfoPersonaEmpresaRol->getPersonaId()
                                            ? $objInfoPersonaEmpresaRol->getPersonaId()->getInformacionPersona() 
                                            : '';
                        $strUsuarios .= $strNombreUsuario.'<br/>';
                        $strObservacionCuadrilla1 .= $strUsuarios;

                        $objInfoPersonaEmpresaRol->setCuadrillaId($objCuadrilla);
                        $emComercial->persist($objInfoPersonaEmpresaRol);
                        $emComercial->flush();	

                        $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                        $objInfoPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHistorial->setIpCreacion($objSession->get('user'));
                        $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($objSession->get('user'));
                        $objInfoPersonaEmpresaRolHistorial->setObservacion('Cuadrilla anterior: '.$strCodigoCuadrillaAnterior);
                        $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivoPersona);
                        $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                        $emComercial->flush();	

                        $objCuadrillaHistorial1 = new AdmiCuadrillaHistorial();
                        $objCuadrillaHistorial1->setCuadrillaId($objCuadrilla);
                        $objCuadrillaHistorial1->setEstado($strEstadoActivo);
                        $objCuadrillaHistorial1->setFeCreacion($datetimeActual);
                        $objCuadrillaHistorial1->setUsrCreacion($strUserSession);
                        $objCuadrillaHistorial1->setObservacion($strObservacionCuadrilla1);
                        $objCuadrillaHistorial1->setMotivoId($intIdMotivo);
                        $emComercial->persist($objCuadrillaHistorial1);
                        $emComercial->flush();

                        if ($boolDepConfigHE)
                        {
                            $objInfoHistoEmpleCuadrilla = new InfoHistoEmpleCuadrilla();
                            $objInfoHistoEmpleCuadrilla->setCuadrillaId($objCuadrilla);
                            $objInfoHistoEmpleCuadrilla->setPersonaId($objInfoPersonaEmpresaRol->getPersonaId());
                            $objInfoHistoEmpleCuadrilla->setTipoHorarioId(1);
                            $objInfoHistoEmpleCuadrilla->setFechaInicio(date("d-m-Y", strtotime($strTurnoInicio)));
                            $objInfoHistoEmpleCuadrilla->setHoraInicio($strTurnoHoraInicio);
                            $objInfoHistoEmpleCuadrilla->setFechaFin(date("d-m-Y", strtotime($strTurnoFin)));
                            $objInfoHistoEmpleCuadrilla->setHoraFin($strTurnoHoraFin);
                            $objInfoHistoEmpleCuadrilla->setEstado($strEstadoActivo);
                            $objInfoHistoEmpleCuadrilla->setUsrCreacion($strUserSession);
                            $objInfoHistoEmpleCuadrilla->setIpCreacion($objRequest->getClientIp());
                            $objInfoHistoEmpleCuadrilla->setFechaCreacion(new \DateTime('now'));
                            $emComercial->persist($objInfoHistoEmpleCuadrilla);
                            $emComercial->flush();
                            $arrayParamPersona =  array(
                                "idPersona"            => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                "codEmpresa"           => '10'
                            );
                            $objInfoPersona      =  $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->getDatosPersonaById($arrayParamPersona);
                            
                            $intSecuencialId     =  $intSecuencialId+1;
                            $strNoEmpleado       =  $objInfoPersona[0]['NO_EMPLE'];
                            $arrayNoEmpleado[]   =  intval($strNoEmpleado);
                            $arrayFechaInicio[]  =  date("d-m-Y", strtotime($strTurnoInicio));
                            $arrayFechaFin[]     =  date("d-m-Y", strtotime($strTurnoFin));
                            $arrayHoraInicio[]   =  $strTurnoHoraInicio;
                            $arrayHoraFin[]      =  $strTurnoHoraFin;
                            $arrayTipoHorario[]  =  intval(1);
                            $arrayPlaniAnual[]   =  'N';
                            $arrayIdSecuencia[]  =  $intSecuencialId;
                            $arrayCuadrillaId[]  =  intval($objCuadrilla->getId());

                            if ($arrayDiasSemanaId != '')
                            {   
                                $arrayDias = [];
                                for ($intIndice=0; $intIndice < count($arrayDiasSemanaId); $intIndice++) 
                                {
                                    $arrayDias[] = array('noEmple' => intval($strNoEmpleado), 
                                                        "dia"=> intval($arrayDiasSemanaId[$intIndice]),
                                                        "idDia" => $intSecuencialId);
                                }
                                $arrayDiasEmple[] = $arrayDias;

                            }

                        }
                        
                        

                    }//( $objInfoPersonaEmpresaRol )
                }//foreach($arrayIntegrantes as $objIntegrante)
                if ($boolDepConfigHE)
                {
                    $arrayDatosTrama['usrCreacion']        = $objSession->get('user');
                    $arrayDatosTrama['empresaCod']         = $intIdEmpresa;
                    $arrayDatosTrama['noEmpleado']         = $arrayNoEmpleado;
                    $arrayDatosTrama['fechaInicio']        = $arrayFechaInicio;
                    $arrayDatosTrama['fechaFin']           = $arrayFechaFin;
                    $arrayDatosTrama['horaInicio']         = $arrayHoraInicio;
                    $arrayDatosTrama['tipoHorario']        = $arrayTipoHorario;
                    $arrayDatosTrama['horaFin']            = $arrayHoraFin;
                    $arrayDatosTrama['planificacionAnual'] = $arrayPlaniAnual;
                    $arrayDatosTrama['idSecuencia']        = $arrayIdSecuencia;
                    $arrayDatosTrama['diasEscogidos']      = $arrayDiasEmple;
                    $arrayDatosTrama['cuadrillaId']        = $arrayCuadrillaId;

                    $objInfoHistoEmpleCuadrilla    = $emSoporte ->getRepository('schemaBundle:InfoHorarioEmpleados')
                                                                ->ejecutarCrearPlaniCuadrillaHE($arrayDatosTrama);

                    if ($objInfoHistoEmpleCuadrilla)
                    {
                        
                        if ($objInfoHistoEmpleCuadrilla['status'] == 'ERROR')
                        {
                            $strMensaje = $objInfoHistoEmpleCuadrilla['mensaje'];   
                            $strStatus = $objInfoHistoEmpleCuadrilla['status'];
                            error_log($objInfoHistoEmpleCuadrilla['mensaje']);
                        }
                        else
                        {
                            $strMensaje = $objInfoHistoEmpleCuadrilla['mensaje'];   
                            $strStatus = $objInfoHistoEmpleCuadrilla['status'];
                        }
                    }
                }
            }//( $objCuadrilla )

            $emComercial->getConnection()->commit();
            $emComercial->getConnection()->close();


            /*========================= INICIO NOTIFICACION HAL ==========================*/
            $serviceSoporte->notificacionesHal(
                            array ('strModulo' => 'cuadrilla',
                                   'strUser'   =>  $objSession->get('user'),
                                   'strIp'     =>  $objRequest->getClientIp(),
                                   'arrayJson' =>  array ('metodo' => 'nueva',
                                                          'id'     => $objCuadrilla->getId())));
            /*========================== FIN NOTIFICACION HAL ============================*/

            return $this->redirect($this->generateUrl('admicuadrilla_show', array('id' => $objCuadrilla->getId())));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }//try

        return $this->render('administracionBundle:AdmiCuadrilla:new.html.twig', array(
                                                                                          'item'                  => $entityItemMenu,
                                                                                          'cuadrilla'             => $objCuadrilla,
                                                                                          'form'                  => $form->createView(),
                                                                                          'strNombreArea'         => $strNombreArea,
                                                                                          'strCargo'              => $strCargo,
                                                                                          'intIdJefeSeleccionado' => $intIdPersonEmpresaRol,
                                                                                       )
                            );
    }


    /**
     * @Secure(roles="ROLE_170-6")
     * 
     * Documentación para el método 'showAction'.
     *
     * Guarda una cuadrilla.
     *
     * @param integer $id
     * @return Response 
     *
     * @version 1.0 Version Inicial
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se muestra la cuadrilla seleccionada y se añade la 'Zona' y el 'Departamento'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 28-10-2015 - Se modifica para que se muestre el vehículo asignado a la cuadrilla
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 06-11-2015 - Se cambia para que retorne la información del activo fijo asignado a la cuadrilla el cual es
     *                           referenciado de la tabla 'INFO_ELEMENTO'
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 27-11-2015 - Se modifica para que se muestren en el grid de los integrantes de la cuadrilla el cargo de Telcos, cargo del NAF
     *                           y el estado del empleado
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 24-12-2015 - Se modifica para que se muestren las Fecha y Hora de Inicio y Fecha y Hora de Fin de la cuadrilla
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 13-04-2016 - Se modifica para que se muestre el chofer asignado actualmente, ya sea el predefinido o el provisional
     *                           cuando corresponda
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 24-08-2016 - Se modifica para obtener el chofer predefinido asignado de acuerdo al horario de asignación predefinida
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     */
    public function showAction($id)
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emSeguridad       = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strNombreArea     = "Tecnico";
        $strEstadoActivo   = 'Activo';

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("170", "1");

        $objCuadrilla    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($id);
        $objDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->findOneById($objCuadrilla->getDepartamentoId());
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;
        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        } 

        $strHoraInicio = '';
        $strHoraFin    = '';

        if($objCuadrilla->getTurnoHoraInicio())
        {
            $strHoraInicio = $objCuadrilla->getTurnoHoraInicio();
        }
        if($objCuadrilla->getTurnoHoraFin())
        {
            $strHoraFin    = $objCuadrilla->getTurnoHoraFin();
        }

        $strActivoAsignado      = 'Sin Asignación';
        $strTipoActivoAsignado  = 'Sin Asignación';
        $strChoferAsignado      = 'Sin Asignación';
        $strChoferPredefinido   = 'Sin Asignación';
        $strChoferProvisional   = 'Sin Asignación';
        $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy( 
                                                                array( 
                                                                        'estado'        => $strEstadoActivo,
                                                                        'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                        'detalleValor'  => $objCuadrilla->getId(),
                                                                     ) 
                                                        );
        if( $objDetalleElemento )
        {
            $intIdActivoActual = $objDetalleElemento->getElementoId();
            $objActivoActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                   ->findOneBy( array('id' => $intIdActivoActual, 'estado' => $strEstadoActivo) );

            if( $objActivoActual )
            {
                $strNombreTipoElemento = ucwords( strtolower( $objActivoActual->getModeloElementoId()->getNombreModeloElemento() ) );
                $strTipoActivoAsignado = $strNombreTipoElemento;
                $strActivoAsignado     = $objActivoActual->getNombreElemento();
                
                
                /*Obtener Información de Chofer Asignado*/
                $codEmpresaSession          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        
                $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

                $objCaracteristicaZonaPredefinida = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

                $objCaracteristicaTareaPredefinida = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);

                $objCaracteristicaDepartamentoPredefinido = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);


                //Busca el chofer Predefinido
                $arrayParametrosAsignacion  = array(
                                                    'codEmpresa'                                    => $codEmpresaSession,
                                                    'tipoElemento'                                  => 'VEHICULO',
                                                    'strEstadoActivo'                               => 'Activo',
                                                    'strEstadoPrestado'                             => 'Prestado',
                                                    'strDetalleCuadrilla'                           => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                    'strDetalleChoferProvisional'                   => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER,
                                                    'intIdTipoSolicitud'                            => $objTipoSolicitud->getId(),
                                                    'arrayDetallesFechasYHorasAsignacionVehicular'  => 
                                                        array(
                                                                'strDetalleFechaInicioAsignacionVehicular'          =>
                                                                    self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                                                'strDetalleSolicitudAsignacionVehicular'            =>
                                                                    self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                                                'strDetalleFechaFinAsignacionVehicular'             =>
                                                                    self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN,
                                                                'strDetalleHoraInicioAsignacionVehicular'           =>
                                                                    self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                                                'strDetalleHoraFinAsignacionVehicular'              =>
                                                                    self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN
                                                        ),
                                                    'intIdCaracteristicaDepartamentoPredefinido'    => 
                                                        $objCaracteristicaDepartamentoPredefinido->getId(),
                                                    'intIdCaracteristicaZonaPredefinida'            => $objCaracteristicaZonaPredefinida->getId(),
                                                    'intIdCaracteristicaTareaPredefinida'           => $objCaracteristicaTareaPredefinida->getId(),
                                                    'criterios'                                     => array(
                                                                                                            'idElemento'    => $intIdActivoActual,
                                                                                                            'idCuadrilla'   => $id
                                                                                                        )
                                              );
        
                $arrayResultadoChoferPredefinido =   $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->getResultadoAsignacionOperativaByCriterios($arrayParametrosAsignacion,$emComercial);

                $resultadoChoferPredefinido = $arrayResultadoChoferPredefinido['resultado'];
                
                if($resultadoChoferPredefinido)
                {
                    foreach($resultadoChoferPredefinido as $data)
                    {
                        
                        $strChoferPredefinido=$data['nombresChoferPredefinido']." ".$data['apellidosChoferPredefinido'];

                        //Buscar si hay actualmente un chofer provisional
                        $objParent = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->find($data['idDetalleCuadrilla']);
                        $objDetalleChoferProvisional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                        ->findOneBy(array(
                                                                            "elementoId"    => $intIdActivoActual,
                                                                            "detalleNombre" => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER,
                                                                            "parent"        => $objParent,
                                                                            "estado"        => 'Activo'
                                                                        ));
                        
                        if($objDetalleChoferProvisional)
                        {
                            $idPerChoferProvisional=$objDetalleChoferProvisional->getDetalleValor();
                    
                            $objPerChoferProvisional=$emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerChoferProvisional);
                            $objPersonaChoferProvisional=$objPerChoferProvisional->getPersonaId();
                            
                            if($objPersonaChoferProvisional)
                            {
                                $strChoferProvisional   =$objPersonaChoferProvisional->getNombres()." ".$objPersonaChoferProvisional->getApellidos();
                                $strChoferAsignado      = $strChoferProvisional;
                            }
                        }
                        else
                        {
                            $strChoferAsignado      = $strChoferPredefinido;
                        }
                    }
                }
                
            }//( $objActivoActual )
        }//( $objDetalleElemento )

        $strLabelAPresentar = "Zona:";

        if( $objCuadrilla->getZonaId() )
        {
            $objAPresentar = $emGeneral->getRepository('schemaBundle:AdmiZona')->findOneById($objCuadrilla->getZonaId());
        }
        else
        {
            $strLabelAPresentar = "Tarea:";
            $objAPresentar      = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneById($objCuadrilla->getTareaId());
        }



        if(!$objCuadrilla)
        {
            throw new NotFoundHttpException('No existe el AdmiCuadrilla que se quiere mostrar');
        }

        $strNombreCoordinadorPrestado  = "";
        $strNombreCoordinadorPrincipal = "";
        $strFechaPrestamo              = "";
        $strEstado                     = $objCuadrilla->getEstado();


        $intTmpIdPersonEmpresaRol   = $intIdPersonEmpresaRol;
        $objCoordinadorPrincipal    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonEmpresaRol);
        if( $strEstado == "Prestado" )
        {
            if( $intTmpIdPersonEmpresaRol == $objCuadrilla->getCoordinadorPrestadoId() )
            {
                $strEstado = "Es Préstamo";
            }

            //Para conocer el coordinador principal
            $objCoordinadorPrincipal = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->findOneById( $objCuadrilla->getCoordinadorPrincipalId() );

            if($objCoordinadorPrincipal)
            {
                $strNombreCoordinadorPrincipal = $objCoordinadorPrincipal->getPersonaId() 
                                                 ? $objCoordinadorPrincipal->getPersonaId()->getInformacionPersona() : '';

                $strNombreCoordinadorPrincipal = ucwords(strtolower($strNombreCoordinadorPrincipal));
            }
            //Fin Para conocer el coordinador principal


            //Para conocer el coordinador prestado
            $objCoordinadorPrestado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->findOneById( $objCuadrilla->getCoordinadorPrestadoId() );

            if($objCoordinadorPrestado)
            {
                $strNombreCoordinadorPrestado = $objCoordinadorPrestado->getPersonaId() 
                                                ? $objCoordinadorPrestado->getPersonaId()->getInformacionPersona() : '';

                $strNombreCoordinadorPrestado = ucwords(strtolower($strNombreCoordinadorPrestado));
            }
            //Fin Para conocer el coordinador prestado


            //Para conocer fecha del préstamo
            $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Se presta la cuadrilla');

            $intIdMotivo = 0;

            if( $objMotivo )
            {
                $intIdMotivo = $objMotivo->getId();
            }

            $objHistorial = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                        ->findHistorialCuadrillasByCriterios( 
                                                                                array(
                                                                                        'cuadrillaId' => $objCuadrilla->getId(),
                                                                                        'motivoId'    => $intIdMotivo
                                                                                     )
                                                                            );

            if( $objHistorial )
            {
                $strFechaPrestamo = $objHistorial->getFeCreacion()->format('d M Y');
            }
            //Fin Para conocer fecha del préstamo

        }//( $objCuadrilla->getEstado() == "Prestado" )
        
        
        
        

        return $this->render('administracionBundle:AdmiCuadrilla:show.html.twig', array(
                                                                                            'item'                  => $entityItemMenu,
                                                                                            'objetoAPresentar'      => $objAPresentar,
                                                                                            'labelAPresentar'       => $strLabelAPresentar,
                                                                                            'flag'                  => $objRequest->get('flag'),
                                                                                            'cuadrilla'             => $objCuadrilla,
                                                                                            'departamento'          => $objDepartamento,
                                                                                            'activoAsignado'        => $strActivoAsignado,
                                                                                            'choferAsignado'        => $strChoferAsignado,
                                                                                            'choferPredefinido'     => $strChoferPredefinido,
                                                                                            'choferProvisional'     => $strChoferProvisional,
                                                                                            'tipoActivoAsignado'    => $strTipoActivoAsignado,
                                                                                            'coordinadorPrincipal'  => $strNombreCoordinadorPrincipal,
                                                                                            'coordinadorPrestado'   => $strNombreCoordinadorPrestado,
                                                                                            'fechaPrestamo'         => $strFechaPrestamo,
                                                                                            'strEstado'             => $strEstado,
                                                                                            'strNombreArea'         => $strNombreArea,
                                                                                            'intIdJefeSeleccionado' => $intTmpIdPersonEmpresaRol,
                                                                                            'strHoraInicio'         => $strHoraInicio,
                                                                                            'strHoraFin'            => $strHoraFin,
                                                                                            'boolDepConfigHE'       => $boolDepConfigHE,
                                                                                        )
                            );
    }


    /**
     * @Secure(roles="ROLE_170-245")
     * 
     * Documentación para el método 'gridIntegrantesAction'.
     *
     * Guarda una cuadrilla.
     *
     * @return Response 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Retorna los integrantes asociados a una cuadrilla
     *
     * @version 1.0 Version Inicial
     */
    public function gridIntegrantesAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');

        $intIdCuadrilla   = $objRequest->query->get('intIdCuadrilla');
        $intTotal         = 0;
        $arrayIntegrantes = array();

        if($intIdCuadrilla)
        {
            $emComercial = $this->getDoctrine()->getManager();

            $objIntegrantes = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByCuadrillaId($intIdCuadrilla);

            if( $objIntegrantes )
            {
                $arrayPersonasExistentes = array();

                foreach($objIntegrantes as $objIntegrante)
                {
                    $idIntActual = $objIntegrante->getPersonaId()->getId();

                    if(!in_array($idIntActual, $arrayPersonasExistentes))
                    {
                        $strNombre = ucwords(strtolower($objIntegrante->getPersonaId()->getNombres()." ".
                                                        $objIntegrante->getPersonaId()->getApellidos()));

                        $intIdEmpleado = $objIntegrante->getId();

                        $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')
                                                ->getRolEmpleadoEmpresa( array('usuario' => $intIdEmpleado) );

                        $item                  = array();
                        $item['intIdEmpleado'] = $intIdEmpleado;
                        $item['strNombre']     = $strNombre;
                        $item['strCargo']      = $strCargo;

                        $arrayIntegrantes[] = $item;

                        $intTotal++;
                    }//(!in_array($idIntActual, $arrayPersonasExistentes))
                }//foreach($objIntegrantes as $objIntegrante)

                $jsonEncontrados = json_encode($arrayIntegrantes);

                $objJson = '{"total":"'.$intTotal.'","encontrados":'.$jsonEncontrados.'}';
            } 
            else
            {
                $objJson = '{"total":"0","encontrados":[]}';
            }//( $objIntegrantes )   	        
        } 
        else
        {
            $objJson = '{"total":"0","encontrados":[]}';
        }//($intIdCuadrilla)             

        $objResponse->setContent($objJson);

        return $objResponse;
    }


    /**
     * @Secure(roles="ROLE_170-4")
     * 
     * Documentación para el método 'editAction'.
     *
     * Edita la información de una cuadrilla.
     *
     * @param integer $id
     * 
     * @return Response 
     *
     * @version 1.0 Version Inicial
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se modifica para que puedan agregar la zona y departamento a las cuadrillas
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 04-11-2015 - Se modifica para que retorne al personal del Coordinador Principal y no del Ayudante Coordinador.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 20-11-2015 - Se envía como parámetro la variable 'strCategoriaTablet' que corresponde a la categoría de los elementos
     *                           de tipo 'TABLET' que se requiere presentar al asignar una tablet a un Líder o Jefe Cuadrilla.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     * @author Modificado: Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.5 03-01-2023 - Se agrega validación para cuando la cuadrilla tenga el estado "Prestado" y
     *                           el CoordinadorPrestado tenga una Id diferente al usuario actual, modifica la Id por la cuadrilla.
     */
    public function editAction($id)
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombreArea         = "Tecnico";

        $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')
                                ->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol) );


        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("170", "1");

        $objCuadrilla    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($id);
        $arrayParametros    = array('intEmpresaCod' => $intIdEmpresa);
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;
        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        } 
                                                                   
        if(!$objCuadrilla)
        {
            throw new NotFoundHttpException('No existe el AdmiCuadrilla que se quiere mostrar');
        }

        $objDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->findOneById($objCuadrilla->getDepartamentoId());

        $arrayParametros = array( 'intEmpresaCod' => $intIdEmpresa, 'departamentoId' => $objDepartamento );


        $strCheckear = ''; //Variable que identificará si se debe checkear en el radioGroup la opción de Zona o Tarea

        if( $objCuadrilla->getZonaId() )
        {
            $objZona     = $emGeneral->getRepository('schemaBundle:AdmiZona')->findOneById($objCuadrilla->getZonaId());
            $strCheckear = 'zona';

            $arrayParametros['zonaId'] = $objZona;
        }
        elseif( $objCuadrilla->getTareaId() )
        {
            $objTarea    = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneById($objCuadrilla->getTareaId());
            $strCheckear = 'tarea';

            $arrayParametros['tareaId'] = $objTarea;
        }     


        $turnoHoraInicio    = '';
        $turnoHoraFin       = '';
        if($objCuadrilla->getTurnoHoraInicio())
        {
            $turnoHoraInicio=$objCuadrilla->getTurnoHoraInicio();
        }

        if($objCuadrilla->getTurnoHoraInicio())
        {
            $turnoHoraFin=$objCuadrilla->getTurnoHoraFin();
        }

        $strFechaInicioTurno  = $objCuadrilla->getTurnoInicio() ? date("d-m-Y", strtotime($objCuadrilla->getTurnoInicio())) : '';
        $strFechaFinTurno     = $objCuadrilla->getTurnoFin() ? date("d-m-Y", strtotime($objCuadrilla->getTurnoFin())) : '';  

        $strEstado                  = $objCuadrilla->getEstado();
        $intCoordinadorPrestadoId   = $objCuadrilla->getCoordinadorPrestadoId();
        $intCoordinadorPrincipalId  = $objCuadrilla->getCoordinadorPrincipalId();

        if($strEstado == 'Prestado' && $intCoordinadorPrestadoId != $intIdPersonEmpresaRol)
        {
            $intIdPersonEmpresaRol = $intCoordinadorPrestadoId;
        }

        if ($strEstado == 'Activo' && $intCoordinadorPrincipalId != $intIdPersonEmpresaRol)
        {
            $intIdPersonEmpresaRol = $intCoordinadorPrincipalId;
        }

        $form = $this->createForm(new AdmiCuadrillaType($arrayParametros), $objCuadrilla);

        return $this->render('administracionBundle:AdmiCuadrilla:edit.html.twig', array(
                                                                                           'item'                  => $entityItemMenu,
                                                                                           'cuadrilla'             => $objCuadrilla,
                                                                                           'form'                  => $form->createView(),
                                                                                           'strNombreArea'         => $strNombreArea,
                                                                                           'strCargo'              => $strCargo,
                                                                                           'intIdJefeSeleccionado' => $intIdPersonEmpresaRol,
                                                                                           'strCheckear'           => $strCheckear,
                                                                                           'strCategoriaTablet'    => 'tablet',
                                                                                           'strTurnoHoraInicio'    => $turnoHoraInicio,
                                                                                           'strTurnoHoraFin'       => $turnoHoraFin,
                                                                                           'strFechaInicioTurno'   => $strFechaInicioTurno,
                                                                                           'strFechaFinTurno'      => $strFechaFinTurno,
                                                                                           'boolDepConfigHE'       => $boolDepConfigHE,
                                                                                       )
                            );
    }


    /**
     * @Secure(roles="ROLE_170-9")
     * 
     * Documentación para el método 'cambioEstadoEmpleadosAction'.
     *
     * Elimina o Agrega empleados a una cuadrilla existente.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-10-2015 - Se modifica para verificar que si el empleado estpá prestado por otro coordinador no se cambi el
     *                           coordinador al que reporta actualmente.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-11-2015 - Se modifica para que al buscar en la tabla 'InfoPersonaEmpresaRolCarac' se envíe como objetos los parámetros de
     *                           'caracteristicaId' y 'personaEmpresaRolId'.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 11-12-2015 - Se modifica para que al eliminar un empleado se disvincule la 'TABLET' asignado a un Lider de cuadrilla o Jefe
     *                           Cuadrilla.
     */
    public function cambioEstadoEmpleadosAction()
    {
        $response           = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $strMensaje         = 'Error';
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';

        $intIdCuadrilla = $objRequest->get('intIdCuadrilla');
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;

        $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($intIdCuadrilla);
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUserSession, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;
        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        }
        //VARIABLES AGREGADAS PARA EL HISTORIAL EMPLEADO CUADRILLA

        if(!$objCuadrilla)
        {
            $strMensaje = 'No existe el AdmiCuadrilla que se desea mostrar';
        }

        if( $objCuadrilla )
        {
            $strAccion   = $objRequest->get('strAccion');

            if( $strAccion == "Agregar" )
            {
                $strMovitoCuadrilla      = 'Se agregan miembros a la cuadrilla';
                $strObservacionCuadrilla = 'Se agregaron los siguientes miembros a la cuadrilla:<br/>';

                if( $objCuadrilla->getEstado() == "Prestado" )
                {
                    $objTmpMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                              ->findOneByNombreMotivo('Se agrega empleado a cuadrilla prestada');

                    if( $objTmpMotivo )
                    {
                        $intIdMotivo = $objTmpMotivo->getId();
                    }
                }
                else
                {
                    $objTmpMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                              ->findOneByNombreMotivo('Edicion de cuadrilla');

                    if( $objTmpMotivo )
                    {
                        $intIdMotivo  = $objTmpMotivo->getId();
                    }
                }//( $objCuadrilla->getEstado() == "Prestado" )

            }
            elseif( $strAccion == "Eliminar" )
            {
                $strMovitoCuadrilla      = 'Se eliminan miembros de la cuadrilla';
                $strObservacionCuadrilla = 'Se eliminan los siguientes miembros de la cuadrilla:<br/>';
                $intIdMotivo             = $objRequest->get('intIdMotivo');
            }//( $strAccion == "Agregar" )


            $jsonIntegrantes  = json_decode($objRequest->get('strEmpleados'));
            $arrayIntegrantes = $jsonIntegrantes->encontrados;

            $emComercial->getConnection()->beginTransaction();	

            try
            {
                if( $arrayIntegrantes )
                {
                    $strUsuarios = "";

                    foreach($arrayIntegrantes as $objIntegrante)
                    {
                        $intIdIntegrante          = $objIntegrante->intIdPersonaEmpresaRol;
                        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->findOneById($intIdIntegrante);
                        
                        if( $objInfoPersonaEmpresaRol )
                        {
                            $strNombreUsuario = $objInfoPersonaEmpresaRol->getPersonaId() 
                                                ? $objInfoPersonaEmpresaRol->getPersonaId()->getInformacionPersona() : '';

                            $strUsuarios .= $strNombreUsuario.'<br/>'; 
                            
                            $arrayParamDiaSemanaCuadrilla = array(      'estado'        => 'Activo', 
                                                                      'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId());

                            $objInfoDiaSemanaCuadrilla = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                                     ->findBy($arrayParamDiaSemanaCuadrilla);

                            if( $strAccion == "Agregar" )
                            {
                                $intCuadrillaAnterior = $strCodigoCuadrillaAnterior = null;
                                $objInfoPersonaEmpresaRol->setCuadrillaId($objCuadrilla);
                                if($boolDepConfigHE)
                                {
                                    $strTipoHorarioId = $objRequest->get('strTipoHorarioId');
                                    $strFechaInicio = $objRequest->get('strFechaInicio');
                                    $strFechaFin= $objRequest->get('strFechaFin');
                                    $strHoraInicio = $objRequest->get('strhoraInicio');
                                    $strHoraFin = $objRequest->get('strhoraFin');
                                    $arrayDiasSemana = json_decode($objRequest->get('arrayDiaSemana'), true);
                                    $arrayDiasSemana1   = $arrayDiasSemana['dias'];
                                    //historial al agregar un empleado con detalle del horario de la cuadrilla a la que ingreso
                                    $objInfoHistoEmpleCuadrilla = new InfoHistoEmpleCuadrilla();
                                    $objInfoHistoEmpleCuadrilla->setCuadrillaId($objCuadrilla);
                                    $objInfoHistoEmpleCuadrilla->setPersonaId($objInfoPersonaEmpresaRol->getPersonaId());
                                    $objInfoHistoEmpleCuadrilla->setTipoHorarioId($strTipoHorarioId);
                                    $objInfoHistoEmpleCuadrilla->setFechaInicio(date("d-m-Y", strtotime($strFechaInicio)));
                                    $objInfoHistoEmpleCuadrilla->setHoraInicio($strHoraInicio);
                                    $objInfoHistoEmpleCuadrilla->setFechaFin(date("d-m-Y", strtotime($strFechaFin)));
                                    $objInfoHistoEmpleCuadrilla->setHoraFin($strHoraFin);
                                    $objInfoHistoEmpleCuadrilla->setEstado($strEstadoActivo);
                                    $objInfoHistoEmpleCuadrilla->setUsrCreacion($strUserSession);
                                    $objInfoHistoEmpleCuadrilla->setIpCreacion($strIpUserSession);
                                    $objInfoHistoEmpleCuadrilla->setFechaCreacion($datetimeActual);
                                    $emComercial->persist($objInfoHistoEmpleCuadrilla);
                                    $emComercial->flush();
                                    
                                    if ( !$objInfoDiaSemanaCuadrilla && $strTipoHorarioId != 1 )
                                    {
                                        for ($intIndice=0; $intIndice < count($arrayDiasSemana1); $intIndice++) 
                                        { 
                                            $objInfoDiaSemanaCuadrillaNuevo = new InfoDiaSemanaCuadrilla();
                                            $objInfoDiaSemanaCuadrillaNuevo->setPersonaId($objInfoPersonaEmpresaRol->getPersonaId());
                                            $objInfoDiaSemanaCuadrillaNuevo->setNumeroDiaId(intval($arrayDiasSemana1[$intIndice]));
                                            $objInfoDiaSemanaCuadrillaNuevo->setEstado('Activo');
                                            $objInfoDiaSemanaCuadrillaNuevo->setFechaCreacion(new \DateTime('now'));
                                            $objInfoDiaSemanaCuadrillaNuevo->setUsrCreacion($objSession->get('user'));
                                            $objInfoDiaSemanaCuadrillaNuevo->setIpCreacion($objRequest->getClientIp());
                                            $emComercial->persist($objInfoDiaSemanaCuadrillaNuevo);
                                            $emComercial->flush();
                                        }
                                    }
                                    elseif($objInfoDiaSemanaCuadrilla && $strTipoHorarioId != 1)
                                    {
                                        for ($intIndice=0; $intIndice < count($objInfoDiaSemanaCuadrilla); $intIndice++) 
                                        {
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setEstado('Inactivo');
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setUsrUltMod($objSession->get('user'));
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setIpUltMod($objRequest->getClientIp());
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setFechaUltMod(new \DateTime('now'));
                                            $emComercial->persist($objInfoDiaSemanaCuadrilla[$intIndice]);
                                            $emComercial->flush();
                                        }
                                    }
                                }
                                
                            }
                            elseif( $strAccion == "Eliminar" )
                            {
                                $intCuadrillaAnterior       = $objCuadrilla->getId();
                                $strCodigoCuadrillaAnterior = $objCuadrilla->getCodigo();
                                $objInfoPersonaEmpresaRol->setCuadrillaId(null);


                                /*
                                 * Bloque que desasocia una tablet con el personal asignado a una cuadrilla
                                 */
                                $arrayTmpParametrosTablet = array( 'estado'        => $strEstadoActivo, 
                                                                   'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_TABLET, 
                                                                   'detalleValor'  => $intIdIntegrante );

                                $objDetalleTablet = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->findOneBy($arrayTmpParametrosTablet);

                                if( $objDetalleTablet )
                                {
                                    $strTabletActual   = 'Sin asignaci&oacute;n';
                                    $intIdTabletActual = $objDetalleTablet->getElementoId();
                                    $objTabletActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                           ->findOneBy( array( 'id'     => $intIdTabletActual,
                                                                                               'estado' => $strEstadoActivo ) 
                                                                                      );
                                    if( $objTabletActual )
                                    {
                                        $strTabletActual = $objTabletActual->getNombreElemento();
                                    }

                                    $objDetalleTablet->setEstado($strEstadoEliminado);
                                    $emInfraestructura->persist($objDetalleTablet);
                                    $emInfraestructura->flush();


                                    $strMotivoElementoTablet = 'Se elimina tablet asociada';
                                    $objMotivoTablet         = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                         ->findOneByNombreMotivo($strMotivoElementoTablet);
                                    $intIdMotivoTablet       = $objMotivoTablet ? $objMotivoTablet->getId() : 0;
                                    $strMensajeObservacion   = $strMotivoElementoTablet.": ".$strTabletActual;

                                    $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                                    $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                                    $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                                    $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                                    $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                    $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                                    $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                                    $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivoTablet);
                                    $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                                    $emComercial->flush();
                                }//( $objDetalleTablet )
                                /*
                                 * Fin del Bloque que desasocia una tablet con el personal asignado a una cuadrilla
                                 */

                                //historial al eliminar empleado con detalle del horario de la cuadrilla a la que esta saliendo
                                if($boolDepConfigHE)
                                {
                                    $arrayTmpParametrosHistoEmple = array(  'estado'        => $strEstadoActivo, 
                                                                            'cuadrillaId'   => $objCuadrilla->getId(), 
                                                                            'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId()->getId());
    
                                    $objDetalleHistoEmple = $emComercial->getRepository('schemaBundle:InfoHistoEmpleCuadrilla')
                                                                        ->findOneBy($arrayTmpParametrosHistoEmple);
                                    if($objDetalleHistoEmple)
                                    {
                                        $objDetalleHistoEmple->setEstado($strEstadoEliminado);
                                        $objDetalleHistoEmple->setUsrUltMod($strUserSession);
                                        $objDetalleHistoEmple->setIpUltMod($strIpUserSession);
                                        $objDetalleHistoEmple->setFechaUltMod($datetimeActual);
                                        $emComercial->persist($objDetalleHistoEmple);
                                        $emComercial->flush();
                                    }
    
                                    if ( $objInfoDiaSemanaCuadrilla && $strTipoHorarioId != 1 )
                                    {   
                                        for ($intIndice=0; $intIndice < count($objInfoDiaSemanaCuadrilla); $intIndice++) 
                                        {
                                        $objInfoDiaSemanaCuadrilla[$intIndice]->setEstado('Inactivo');
                                        $objInfoDiaSemanaCuadrilla[$intIndice]->setUsrUltMod($objSession->get('user'));
                                        $objInfoDiaSemanaCuadrilla[$intIndice]->setIpUltMod($objRequest->getClientIp());
                                        $objInfoDiaSemanaCuadrilla[$intIndice]->setFechaUltMod(new \DateTime('now'));
                                        $emComercial->persist($objInfoDiaSemanaCuadrilla[$intIndice]);
                                        $emComercial->flush();
                                        }
                                    }
                                }
                            }

                            $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                            $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                            $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                            $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                            $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                            $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                            $objInfoPersonaEmpresaRolHistorial->setObservacion('Cuadrilla anterior: '.$strCodigoCuadrillaAnterior);
                            $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                            $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                            $emComercial->flush();	


                            // Se verifica si el empleado es préstamo de un coordinador
                            $boolEsPrestamoElEmpleado      = false;
                            $arrayParametrosCaracteristica = array( 'descripcionCaracteristica' => self::CARACTERISTICA_PRESTAMO_EMPLEADO,
                                                                    'estado'                    => $strEstadoActivo );

                            $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy( $arrayParametrosCaracteristica );

                            $arrayTmpParametrosCaracteristica = array( 
                                                                         'estado'              => $strEstadoActivo,
                                                                         'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                         'caracteristicaId'    => $objCaracteristica
                                                                     );

                            $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                     ->findOneBy( $arrayTmpParametrosCaracteristica );

                            if( $objPersonaEmpresaRolCarac )
                            {
                                $boolEsPrestamoElEmpleado = true;
                            }
                            // Se verifica si el empleado es préstamo de un coordinador


                            if( $objCuadrilla->getEstado() == "Prestado" && $strAccion == "Eliminar" && !$boolEsPrestamoElEmpleado )
                            {
                                //Se obtiene el coordinador actual para guardarlo en el historial como Jefe Anterior
                                $intTmpCoordinadorActual = $objInfoPersonaEmpresaRol->getReportaPersonaEmpresaRolId();

                                $objInfoCoordinadorActual = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->findOneById($intTmpCoordinadorActual);

                                $strNombreCoordinadorActual = $objInfoCoordinadorActual->getPersonaId() 
                                                              ? $objInfoCoordinadorActual->getPersonaId()->getInformacionPersona() : '';

                                $arrayParametrosCaracteristica = array( 'descripcionCaracteristica' => self::CARACTERISTICA_PRESTAMO_CUADRILLA,
                                                                        'estado'                    => $strEstadoActivo );

                                $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy( $arrayParametrosCaracteristica );

                                $arrayTmpParametrosCaracteristica = array( 
                                                                             'estado'              => $strEstadoActivo,
                                                                             'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                             'caracteristicaId'    => $objCaracteristica
                                                                         );

                                $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                         ->findOneBy( $arrayTmpParametrosCaracteristica );

                                if( $objPersonaEmpresaRolCarac )
                                {
                                    $intTmpCoordinadorAnterior = $objCuadrilla->getCoordinadorPrincipalId();

                                    $objPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);
                                    $objPersonaEmpresaRolCarac->setFeUltMod($datetimeActual);
                                    $objPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                                    $emComercial->persist($objPersonaEmpresaRolCarac);
                                    $emComercial->flush();
                                }
                                else
                                {
                                    $intTmpCoordinadorAnterior = $objCuadrilla->getCoordinadorPrestadoId();
                                }

                                $objInfoPersonaEmpresaRol->setReportaPersonaEmpresaRolId($intTmpCoordinadorAnterior);

                                $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                                $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                                $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                                $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                                $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                                $objInfoPersonaEmpresaRolHistorial->setObservacion('Jefe Anterior: '.$strNombreCoordinadorActual);
                                $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                                $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                                $emComercial->flush();

                            }//( $objCuadrilla->getEstado() == "Prestado" && $strAccion == "Eliminar" )

                            $emComercial->persist($objInfoPersonaEmpresaRol);
                            $emComercial->flush();	
                        }//( $objInfoPersonaEmpresaRol )
                    }//foreach($arrayIntegrantes as $objIntegrante)

                    if( $strUsuarios )
                    {
                        $objCuadrilla->setFeUltMod(new \DateTime('now'));
                        $objCuadrilla->setUsrModificacion($objSession->get('user'));
                        $emComercial->persist($objCuadrilla);
                        $emComercial->flush();


                        $objMotivoCuadrilla = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMovitoCuadrilla);

                        $intIdMotivoCuadrilla = null;
                        if( $objMotivoCuadrilla )
                        {
                            $intIdMotivoCuadrilla = $objMotivoCuadrilla->getId();
                        }

                        $strObservacionCuadrilla .= $strUsuarios;

                        $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                        $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
                        $objCuadrillaHistorial->setEstado($objCuadrilla->getEstado());
                        $objCuadrillaHistorial->setFeCreacion($datetimeActual);
                        $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                        $objCuadrillaHistorial->setObservacion($strObservacionCuadrilla);
                        $objCuadrillaHistorial->setMotivoId($intIdMotivoCuadrilla);
                        $emComercial->persist($objCuadrillaHistorial);
                        $emComercial->flush();
                    }//( $strUsuarios )

                    $emComercial->getConnection()->commit();
                    $emComercial->getConnection()->close();

                    $strMensaje = 'OK';
                }//( $arrayIntegrantes )
            }
            catch (\Exception $e)
            {
                error_log($e->getMessage());

                $strMensaje = 'Hubo un problema de base de datos al eliminar los empleados seleccionados';

                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }//try

        }//( $objCuadrilla )

        $response->setContent( $strMensaje );

        return $response;
    }


    /**
     * Documentación para el método 'motivosAction'.
     *
     * Motivos correspondientes a las cuadrillas
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015 
     */
    public function motivosAction()
    {
        $response    = new JsonResponse();
        $objRequest  = $this->get('request');
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $strModulo    = $objRequest->get('strModulo');
        $strItemMenu  = $objRequest->get('strItemMenu');
        $strAccion    = $objRequest->get('strAccion');
        $intTotal     = 0;
        $arrayMotivos = array();

        $objMotivos = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                  ->findMotivosPorModuloPorItemMenuPorAccion($strModulo, $strItemMenu, $strAccion);

        if( $objMotivos )
        {
            foreach($objMotivos as $objMotivo)
            {
                $item                = array();
                $item['intIdMotivo'] = $objMotivo->getId();
                $item['strMotivo']   = $objMotivo->getNombreMotivo();

                $arrayMotivos[] = $item;

                $intTotal++;
            }//foreach($objMotivos as $objMotivo)
        }//( $objMotivos )

        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayMotivos) );

        return $response;
    }


    /**
     * @Secure(roles="ROLE_170-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de los empleados de una cuadrilla seleccionada.
     *
     * @param integer $id
     * 
     * @return JsonResponse 
     *
     * @version 1.0 Version Inicial
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 14-10-2015 - Se modifica para agregar historial a la tablas 'InfoPersonaEmpresaRolHisto' y 'AdmiCuadrillaHistorial'
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 24-12-2015 - Se modifica para actualizar la información de la fecha y hora de inicio y fin de la cuadrilla y se guarde esta
     *                           información como parte de la observación del historial.
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 10-04-2018 - Se agrega una nueva funcionalidad de notificaciones para HAL.
     *
     */
    public function updateAction($id)
    {
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceSoporte = $this->get('soporte.SoporteService');
        $intZonaAnt     = null;
        $objCuadrillaAnterior = null;

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("170", "1");

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombreArea         = "Tecnico";

        $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')
                                ->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol) );

        $objCuadrilla = $objCuadrillaAnterior = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($id);
        
        $arrayTmpParametrosHistoHorario = array(  'estado'        => 'Activo', 
                                                  'cuadrillaId'   => $objCuadrilla->getId());

        $objDetalleHistoHorario = $emComercial->getRepository('schemaBundle:InfoHistoHorarioCuadrilla')
                                              ->findOneBy($arrayTmpParametrosHistoHorario);
        
        $arrayParamDiaSemanaCuadrilla = array(      'estado'        => 'Activo', 
                                                  'cuadrillaId'   => $objCuadrilla->getId());

        $objInfoDiaSemanaCuadrilla = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                 ->findBy($arrayParamDiaSemanaCuadrilla);
        $arrayParametros    = array('intEmpresaCod' => $intIdEmpresa);
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;

        if(!$objCuadrilla)
        {
            throw new NotFoundHttpException('No existe el AdmiCuadrilla que se desea mostrar');
        }
        // Se agrega para guardar informacion de los dias de la semana que trabaja una cuadrilla
        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        } 
        if($boolDepConfigHE)
        {
            $arrayDiasSemana = $objRequest->get('diasSemana');
            $arrayDiasSemana1   = $arrayDiasSemana[0];
            $arrayDiasSemanaSeleccion = json_decode($arrayDiasSemana1, true);
            $arrayDiasSemanaId = $arrayDiasSemanaSeleccion['valueDiasSemana'];
            $boolEsDiasSemanaAnterior = false;
            $intNumCoincidencia = 0;
            if ( $objInfoDiaSemanaCuadrilla )
            {
                for ($intIndice1=0; $intIndice1 < count($objInfoDiaSemanaCuadrilla); $intIndice1++)
                {
                    if(in_array($objInfoDiaSemanaCuadrilla[$intIndice1]->getNumeroDiaId(), $arrayDiasSemanaId))
                    {
                        $intNumCoincidencia = $intNumCoincidencia + 1;
                    }
                }
    
                $boolEsDiasSemanaAnterior =  count($objInfoDiaSemanaCuadrilla) == $intNumCoincidencia && 
                                             count($arrayDiasSemanaId) == $intNumCoincidencia ? true : false;
            }
            if ( !$boolEsDiasSemanaAnterior )
            {
                for ($intIndice=0; $intIndice < count($arrayDiasSemanaId); $intIndice++)
                {
                    $objInfoDiaSemanaCuadrillaNuevo = new InfoDiaSemanaCuadrilla();
                    $objInfoDiaSemanaCuadrillaNuevo->setCuadrillaId($objCuadrilla);
                    $objInfoDiaSemanaCuadrillaNuevo->setNumeroDiaId(intval($arrayDiasSemanaId[$intIndice]));
                    $objInfoDiaSemanaCuadrillaNuevo->setEstado('Activo');
                    $objInfoDiaSemanaCuadrillaNuevo->setFechaCreacion(new \DateTime('now'));
                    $objInfoDiaSemanaCuadrillaNuevo->setUsrCreacion($objSession->get('user'));
                    $objInfoDiaSemanaCuadrillaNuevo->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($objInfoDiaSemanaCuadrillaNuevo);
                    $emComercial->flush();
                }
                for ($intIndice1=0; $intIndice1 < count($objInfoDiaSemanaCuadrilla); $intIndice1++)
                {
                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setEstado('Eliminado');
                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setUsrUltMod($objSession->get('user'));
                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setIpUltMod($objRequest->getClientIp());
                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setFechaUltMod(new \DateTime('now'));
                    $emComercial->persist($objInfoDiaSemanaCuadrilla[$intIndice1]);
                    $emComercial->flush();
                }
    
            }
        }

        $objDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->findOneById($objCuadrilla->getDepartamentoId());

        $arrayParametros = array( 'intEmpresaCod' => $intIdEmpresa, 'departamentoId' => $objDepartamento );


        $strCheckear = ''; //Variable que identificará si se debe checkear en el radioGroup la opción de Zona o Tarea

        if( $objCuadrilla->getZonaId() )
        {
            $objZona     = $emGeneral->getRepository('schemaBundle:AdmiZona')->findOneById($objCuadrilla->getZonaId());
            $strCheckear = 'zona';
            $intZonaAnt  = $objCuadrilla->getZonaId();
            $arrayParametros['zonaId'] = $objZona;
        }
        elseif( $objCuadrilla->getTareaId() )
        {
            $objTarea    = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneById($objCuadrilla->getTareaId());
            $strCheckear = 'tarea';

            $arrayParametros['tareaId'] = $objTarea;
        }     

        $form = $this->createForm(new AdmiCuadrillaType($arrayParametros), $objCuadrilla);

        $emComercial->getConnection()->beginTransaction();	

        try
        {
            $intIdDepartamentoSeleccionado  = $objRequest->get('departamentoId') ? $objRequest->get('departamentoId') : 0;
            $intIdZonaSeleccionado          = $objRequest->get('zonaId') ? $objRequest->get('zonaId') : 0;
            $intIdTareaSeleccionada         = $objRequest->get('tareaId') ? $objRequest->get('tareaId') : 0;
            $strTipoCuadrilla               = $objRequest->get('tipoCuadrilla') ? $objRequest->get('tipoCuadrilla') : '';
            $strHoraDesdeTurno              = $objRequest->get('horaInicioTurnoCuadrilla') ? trim($objRequest->get('horaInicioTurnoCuadrilla')) : '';
            $strHoraHastaTurno              = $objRequest->get('horaFinTurnoCuadrilla') ? trim($objRequest->get('horaFinTurnoCuadrilla')) : '';
            $strFechaDesdeTurno             = '';
            $strFechaHastaTurno             = '';
            if($objRequest->get('fechaInicioTurnoCuadrilla'))
            {
                $strFechaDesdeTurno =date("Y-m-d", strtotime(trim($objRequest->get('fechaInicioTurnoCuadrilla'))));
            }
            if($objRequest->get('fechaFinTurnoCuadrilla'))
            {
                $strFechaHastaTurno  =  date("Y-m-d", strtotime(trim($objRequest->get('fechaFinTurnoCuadrilla'))));
            }
            $objMotivoCuadrilla = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Edicion de cuadrilla');
            $objMotivoPersona   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Se le asigna nueva cuadrilla');

            $intIdMotivoCuadrilla = null;
            if( $objMotivoCuadrilla )
            {
                $intIdMotivoCuadrilla = $objMotivoCuadrilla->getId();
            }

            $intIdMotivoPersona = null;
            if( $objMotivoPersona )
            {
                $intIdMotivoPersona = $objMotivoPersona->getId();
            }


            $strHoraInicioTurnoAnterior   = $objCuadrillaAnterior->getTurnoHoraInicio() ? $objCuadrillaAnterior->getTurnoHoraInicio() : '';
            $strHoraFinTurnoAnterior      = $objCuadrillaAnterior->getTurnoHoraFin() ? $objCuadrillaAnterior->getTurnoHoraFin() : '';      

            $strCodigoAnterior       = $objCuadrillaAnterior->getCodigo();
            $strNombreAnterior       = $objCuadrillaAnterior->getNombreCuadrilla();
            $strZonaAnterior         = $objCuadrillaAnterior->getZonaId() ? 
                                       sprintf( "%s", $emGeneral->getRepository('schemaBundle:AdmiZona')
                                                                ->find($objCuadrillaAnterior->getZonaId()) ) : '';
            $strTareaAnterior        = $objCuadrillaAnterior->getTareaId() ? 
                                       sprintf( "%s", $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                ->find($objCuadrillaAnterior->getTareaId()) ) : '';
            $strDepartamentoAnterior = sprintf( "%s", $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                                ->find($objCuadrillaAnterior->getDepartamentoId()) );
            $strEstadoAnterior       = $objCuadrillaAnterior->getEstado();

            $objCoordinadorAnterior  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->findOneById($objCuadrillaAnterior->getCoordinadorPrincipalId());

            $strNombreCoordinadorPrincipalAnterior = "";

            if($objCoordinadorAnterior)
            {
                $strNombreCoordinadorPrincipalAnterior = $objCoordinadorAnterior->getPersonaId() 
                                                         ? $objCoordinadorAnterior->getPersonaId()->getInformacionPersona() : '';
            }


            $strNombreCoordinadorPrestadoAnterior = "";

            if( $strEstadoAnterior == 'Prestado' )
            {
                $objCoordinadorPrestadoAnterior  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                             ->findOneById($objCuadrillaAnterior->getCoordinadorPrestadoId());

                if($objCoordinadorPrestadoAnterior)
                {
                    $strNombreCoordinadorPrestadoAnterior = $objCoordinadorPrestadoAnterior->getPersonaId() 
                                                            ? $objCoordinadorPrestadoAnterior->getPersonaId()->getInformacionPersona() : '';
                }
            }
            /*
             * Fin Informacion de la Cuadrilla Anterior
             */


            if( $strTipoCuadrilla == 'zona' )
            {
                $objCuadrilla->setZonaId($intIdZonaSeleccionado);
                $objCuadrilla->setTareaId(null);
            }
            elseif( $strTipoCuadrilla == 'tarea' )
            {
                $objCuadrilla->setZonaId(null);
                $objCuadrilla->setTareaId($intIdTareaSeleccionada);
            }

            $objCuadrilla->setTurnoHoraInicio($strHoraDesdeTurno);
            $objCuadrilla->setTurnoHoraFin($strHoraHastaTurno);
            if($boolDepConfigHE)
            {
            $objCuadrilla->setTurnoInicio($strFechaDesdeTurno);
            $objCuadrilla->setTurnoFin($strFechaHastaTurno);
            }
            $objCuadrilla->setNombreCuadrilla($objRequest->get('nombreCuadrilla'));
            $objCuadrilla->setDepartamentoId($intIdDepartamentoSeleccionado);
            $objCuadrilla->setFeUltMod(new \DateTime('now'));
            $objCuadrilla->setUsrModificacion($objSession->get('user'));
            $emComercial->persist($objCuadrilla);
            $emComercial->flush();

            if($objDetalleHistoHorario && $boolDepConfigHE)
            {
                $objDetalleHistoHorario->setEstado('Eliminado');
                $objDetalleHistoHorario->setUsrUltMod($objSession->get('user'));
                $objDetalleHistoHorario->setIpUltMod($objRequest->getClientIp());
                $objDetalleHistoHorario->setFechaUltMod(new \DateTime('now')); 
                $emComercial->persist($objDetalleHistoHorario);
                $emComercial->flush();
            }
            /*
             * Informacion de la Cuadrilla Nueva
             */
            $strZonaNueva         = $intIdZonaSeleccionado ? 
                                    sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiZona')->find($intIdZonaSeleccionado)) : '';
            $strTareaNueva        = $intIdTareaSeleccionada ? 
                                    sprintf("%s", $emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTareaSeleccionada)) : '';
            $strDepartamentoNuevo = sprintf("%s", $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamentoSeleccionado));
            $objCoordinadorNuevo  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->findOneById($objCuadrilla->getCoordinadorPrincipalId());

            $strNombreCoordinadorPrincipalNuevo = "";

            if($objCoordinadorNuevo)
            {
                $strNombreCoordinadorPrincipalNuevo = $objCoordinadorNuevo->getPersonaId() 
                                                      ? $objCoordinadorNuevo->getPersonaId()->getInformacionPersona() : '';
            }


            $strNombreCoordinadorPrestadoNuevo = "";

            $objCoordinadorPrestadoNuevo  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                      ->findOneById($objCuadrilla->getCoordinadorPrestadoId());

            if($objCoordinadorPrestadoAnterior)
            {
                $strNombreCoordinadorPrestadoNuevo = $objCoordinadorPrestadoNuevo->getPersonaId() 
                                                     ? $objCoordinadorPrestadoNuevo->getPersonaId()->getInformacionPersona() : '';
            }
            /*
             * Fin Informacion de la Cuadrilla Nueva
             */


            $strObservacionHistoCuadrilla = "Edici&oacute;n de cuadrilla:<br/><br/>".
                                            "<b>Datos Anteriores</b>".
                                            "C&oacute;digo: ".$strCodigoAnterior."<br/>".
                                            "Nombre: ".$strNombreAnterior."<br/>";

            if( $strZonaAnterior )
            {
                $strObservacionHistoCuadrilla .= "Zona: ".$strZonaAnterior."<br/>";
            }
            elseif( $strTareaAnterior )
            {
                $strObservacionHistoCuadrilla .= "Tarea: ".$strTareaAnterior."<br/>";
            }

            $strObservacionHistoCuadrilla .= "Departamento: ".$strDepartamentoAnterior."<br/>".
                                             "Coordinador Principal: ".$strNombreCoordinadorPrincipalAnterior."<br/>".
                                             "Coordinador Prestado: ".$strNombreCoordinadorPrestadoAnterior."<br/>".
                                             "Estado: ".$strEstadoAnterior."<br/><br/>".
                                             "Hora Inicio: ".$strHoraInicioTurnoAnterior."<br/>".
                                             "Hora Fin: ".$strHoraFinTurnoAnterior."<br/><br/>".
                                             "<b>Datos Nuevos</b>".
                                             "C&oacute;digo: ".$objCuadrilla->getCodigo()."<br/>".
                                             "Nombre: ".$objCuadrilla->getNombreCuadrilla()."<br/>";

            if( $strZonaNueva )
            {
                $strObservacionHistoCuadrilla .= "Zona: ".$strZonaNueva."<br/>";
            }
            elseif( $strTareaNueva )
            {
                $strObservacionHistoCuadrilla .= "Tarea: ".$strTareaNueva."<br/>";
            }

            $strObservacionHistoCuadrilla .= "Departamento: ".$strDepartamentoNuevo."<br/>".
                                             "Coordinador Principal: ".$strNombreCoordinadorPrincipalNuevo."<br/>".
                                             "Coordinador Prestado: ".$strNombreCoordinadorPrestadoNuevo."<br/>".
                                             "Estado: ".$objCuadrilla->getEstado()."<br/><br/>".
                                             "Hora Inicio: ".$objCuadrilla->getTurnoHoraInicio()."<br/>".
                                             "Hora Fin: ".$objCuadrilla->getTurnoHoraFin()."<br/><br/>";

            $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
            $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
            $objCuadrillaHistorial->setEstado($objCuadrilla->getEstado());
            $objCuadrillaHistorial->setFeCreacion(new \DateTime('now'));
            $objCuadrillaHistorial->setUsrCreacion($objSession->get('user'));
            $objCuadrillaHistorial->setObservacion($strObservacionHistoCuadrilla);
            $objCuadrillaHistorial->setMotivoId($intIdMotivoCuadrilla);
            $emComercial->persist($objCuadrillaHistorial);
            $emComercial->flush();

            if($boolDepConfigHE)
            {
                $objHistoHorarioCuadrilla = new InfoHistoHorarioCuadrilla();
                $objHistoHorarioCuadrilla->setCuadrillaId($objCuadrilla);
                $objHistoHorarioCuadrilla->setFechaInicio(date("d-m-Y", strtotime($strFechaDesdeTurno)));
                $objHistoHorarioCuadrilla->setHoraInicio($strHoraDesdeTurno);
                $objHistoHorarioCuadrilla->setFechaFin(date("d-m-Y", strtotime($strFechaHastaTurno)));
                $objHistoHorarioCuadrilla->setHoraFin($strHoraHastaTurno);
                $objHistoHorarioCuadrilla->setEstado('Activo');
                $objHistoHorarioCuadrilla->setUsrCreacion($objSession->get('user'));
                $objHistoHorarioCuadrilla->setIpCreacion($objRequest->getClientIp());
                $objHistoHorarioCuadrilla->setFechaCreacion(new \DateTime('now'));
                $emComercial->persist($objHistoHorarioCuadrilla);
                $emComercial->flush();
            }

            // se guardaran los dias en los que trabaja la cuadrilla 
            //InfoDiaSemanaCuadrilla

            if( $objCuadrilla )
            {
                $jsonIntegrantes  = json_decode($objRequest->get('empleados_integrantes'));
                $arrayIntegrantes = $jsonIntegrantes->encontrados;

                foreach($arrayIntegrantes as $objIntegrante)
                {
                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findOneById($objIntegrante->intIdPersonaEmpresaRol);
                    //historial al eliminar empleado con detalle del horario de la cuadrilla a la que esta saliendo
                    $arrayTmpParametrosHistoEmple = array(  'estado'        => 'Activo', 
                                                            'cuadrillaId'   => $objCuadrilla->getId(), 
                                                            'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId()->getId());

                    $objDetalleHistoEmple = $emComercial->getRepository('schemaBundle:InfoHistoEmpleCuadrilla')
                                                        ->findOneBy($arrayTmpParametrosHistoEmple);
                    
                    if(!$objDetalleHistoEmple && $boolDepConfigHE)
                    {
                        //historial al agregar un empleado con detalle del horario de la cuadrilla a la que ingreso
                        $objInfoHistoEmpleCuadrilla = new InfoHistoEmpleCuadrilla();
                        $objInfoHistoEmpleCuadrilla->setCuadrillaId($objCuadrilla);
                        $objInfoHistoEmpleCuadrilla->setPersonaId($objInfoPersonaEmpresaRol->getPersonaId());
                        $objInfoHistoEmpleCuadrilla->setTipoHorarioId(1);
                        $objInfoHistoEmpleCuadrilla->setFechaInicio(date("d-m-Y", strtotime($strFechaDesdeTurno)));
                        $objInfoHistoEmpleCuadrilla->setHoraInicio($strHoraDesdeTurno);
                        $objInfoHistoEmpleCuadrilla->setFechaFin(date("d-m-Y", strtotime($strFechaHastaTurno)));
                        $objInfoHistoEmpleCuadrilla->setHoraFin($strHoraHastaTurno);
                        $objInfoHistoEmpleCuadrilla->setEstado('Activo');
                        $objInfoHistoEmpleCuadrilla->setUsrCreacion($objSession->get('user'));
                        $objInfoHistoEmpleCuadrilla->setIpCreacion($objRequest->getClientIp());
                        $objInfoHistoEmpleCuadrilla->setFechaCreacion(new \DateTime('now'));
                        $emComercial->persist($objInfoHistoEmpleCuadrilla);
                        $emComercial->flush();
                    }

                    if( $objInfoPersonaEmpresaRol )
                    {
                        $boolGuardar = false;

                        $objCodigoCuadrillaAnterior = $objInfoPersonaEmpresaRol->getCuadrillaId();

                        if( $objCodigoCuadrillaAnterior )
                        {
                            $strCodigoCuadrillaAnterior = $objCodigoCuadrillaAnterior->getCodigo();

                            if( $strCodigoCuadrillaAnterior != $objCuadrilla->getCodigo())
                            {
                                $boolGuardar = true;
                            }   
                        }
                        else
                        {
                            $strCodigoCuadrillaAnterior = null;
                            $boolGuardar                = true;
                        }

                        if( $boolGuardar )
                        {
                            $objInfoPersonaEmpresaRol->setCuadrillaId($objCuadrilla);
                            $emComercial->persist($objInfoPersonaEmpresaRol);
                            $emComercial->flush();	

                            $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                            $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                            $objInfoPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                            $objInfoPersonaEmpresaRolHistorial->setIpCreacion($objSession->get('user'));
                            $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                            $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($objSession->get('user'));
                            $objInfoPersonaEmpresaRolHistorial->setObservacion('Cuadrilla anterior: '.$strCodigoCuadrillaAnterior);
                            $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivoPersona);
                            $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                            $emComercial->flush();	
                        }//( $boolGuardar )
                    }//( $objInfoPersonaEmpresaRol )
                }//foreach($arrayIntegrantes as $objIntegrante)
            }//( $objCuadrilla )

            $emComercial->getConnection()->commit();
            $emComercial->getConnection()->close();

            /*========================= INICIO NOTIFICACION HAL ==========================*/
            $serviceSoporte->notificacionesHal(
                    array ('strModulo' => 'cuadrilla',
                           'strUser'   =>  $objSession->get('user'),
                           'strIp'     =>  $objRequest->getClientIp(),
                           'arrayJson' =>  array ('metodo' => 'actualizo',
                                                  'id'     => $objCuadrilla->getId())));

            // Notificacion a Hal Por Cambio de Zona
	    if (!is_null($intZonaAnt) && $objCuadrilla->getZonaId() != $intZonaAnt)
            {
                error_log('Zonas: '.$objCuadrilla->getZonaId().' - '.$intZonaAnt);
                $serviceSoporte->notificacionesHal(
                    array ('strModulo' => 'cambioZonaCuadrilla',
                           'strUser'   =>  $objSession->get('user'),
                           'strIp'     =>  $objRequest->getClientIp(),
                           'arrayJson' =>  array ('id'         => $objCuadrilla->getId(),
                                                  'idAnterior' => intval($intZonaAnt),
                                                  'idNueva'    => intval($objCuadrilla->getZonaId()))));
            }
            /*========================== FIN NOTIFICACION HAL ============================*/


            return $this->redirect($this->generateUrl('admicuadrilla_show', array('id' => $objCuadrilla->getId())));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());

            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }//try

        return $this->render('administracionBundle:AdmiCuadrilla:edit.html.twig', array(
                                                                                           'item'                  => $entityItemMenu,
                                                                                           'cuadrilla'             => $objCuadrilla,
                                                                                           'form'                  => $form->createView(),
                                                                                           'strNombreArea'         => $strNombreArea,
                                                                                           'strCargo'              => $strCargo,
                                                                                           'intIdJefeSeleccionado' => $intIdPersonEmpresaRol,
                                                                                           'strCheckear'           => $strCheckear
                                                                                       )
                            );
    }


    /**
     * @Secure(roles="ROLE_170-8")
     * 
     * Documentación para el método 'cambioEstadoCuadrillasAction'.
     *
     * Cambio de estado de una cuadrilla seleccionada, los cuales pueden ser 'Prestado', 'Eliminado' o 'Activo'. 
     * 'Activo' se dará cuando el usuario que prestó la cuadrilla la devuelve.
     * 
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-10-2015
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-11-2015 - Se modifica para que retorne al personal del Coordinador Principal y no del Ayudante Coordinador.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-11-2015 - Se modifica para que al buscar en la tabla 'InfoPersonaEmpresaRolCarac' se envíe como objetos los parámetros de
     *                           'caracteristicaId' y 'personaEmpresaRolId'.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 25-11-2015 - Se modifica para que al eliminar una cuadrilla se disvincule el 'VEHICULO' asignado a la cuadrilla, y adicional
     *                           se controla que si no se encuentran empleados de la cuadrilla que se desea 'Eliminar' retorne un error.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 11-12-2015 - Se modifica para que al eliminar una cuadrilla se disvincule la 'TABLET' asignado a un Lider de cuadrilla o Jefe
     *                           Cuadrilla.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 10-04-2018 - Se agrega una nueva funcionalidad de notificaciones para HAL.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 10-09-2018 - Se agrega en el método la validación para detectar si la cuadrilla a eliminar tiene una planificación Hal Activa
     *                           y en caso de tenerla no podrá ser eliminada.
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.8 17-01-2023 - Se agrega la funcionalidad para recuperar la cuadrilla con estado 'Prestado'. 
     *                           Se corrige la funcionalidad para devolver cuadrillas que provienen de oficinas
     *                           diferentes e incluso departamentos diferentes.
     */
    public function cambioEstadoCuadrillasAction()
    {
        $response          = new Response();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strUserSession    = $objSession->get('user');
        $strIpUserSession  = $objRequest->getClientIp();
        $datetimeActual    = new \DateTime('now');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $boolError         = false;
        $strMensaje        = 'No se encontró cuadrilla en estado activo';
        $boolEliminarAsignaciones=false;
        
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento     = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strNombreArea         = "Tecnico";
        $strEstadoActivo       = 'Activo';
        $strEstadoPrestado     = 'Prestado';
        $strEstadoEliminado    = 'Eliminado';
        $objElementoActual     = null;
        $serviceSoporte        = $this->get('soporte.SoporteService');

        $strCuadrillasSeleccionadas = $objRequest->get('cuadrillas') ? trim($objRequest->get('cuadrillas')) : '';
        $arrayCuadrillas            = explode("|", $strCuadrillasSeleccionadas);
        $strAccion                  = $objRequest->get('accion') ? trim($objRequest->get('accion')) : '';
        $intCoordinadorPrestadoId   = $objRequest->get('coordinadorPrestado') ? trim($objRequest->get('coordinadorPrestado')) : 0;

        $arrayParametrosCaracteristica = array( 'descripcionCaracteristica' => self::CARACTERISTICA_PRESTAMO_CUADRILLA,
                                                'estado'                    => $strEstadoActivo );

        $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy( $arrayParametrosCaracteristica );
        $arrayParametros    = array('intEmpresaCod' => $intIdEmpresa);
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getPersonaDepartamentoPorUserEmpresa($strUserSession, $intIdEmpresa);
        
        $strNombreDepartamento  = strtoupper($arrayEmpleado['NOMBRE_DEPARTAMENTO']);
        $arrayDepConfigHE   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA',$strNombreDepartamento,'');
        $boolDepConfigHE  = false;
        if(count($arrayDepConfigHE['registros']) > 0)
        {
            $boolDepConfigHE  = true;
        } 
        if ($boolDepConfigHE)
        {
            $arrayDiasSeleccionados = $objRequest->get('arrayDiaSemana')? trim($objRequest->get('arrayDiaSemana')) : '';
            $strFechaInicio = $objRequest->get('strFechaInicio')? trim($objRequest->get('strFechaInicio')) : '';
            $strFechaFin = $objRequest->get('strFechaFin')? trim($objRequest->get('strFechaFin')) : '';
            $strTipoHorarioId = $objRequest->get('cmbTipoHorario1')? trim($objRequest->get('cmbTipoHorario1')) : '';
            if ($arrayDiasSeleccionados != '' )
            {
                $arrayDiasSemana = json_decode($objRequest->get('arrayDiaSemana'), true);
                $arrayDiasSemana1   = $arrayDiasSemana['dias'];
            }
        }
        
        $strHoraInicio = $objRequest->get('strHoraInicio')? trim($objRequest->get('strHoraInicio')) : '';
        $strHoraFin = $objRequest->get('strHoraFin')? trim($objRequest->get('strHoraFin')) : '';

        if( $arrayCuadrillas )
        {
            $emComercial->getConnection()->beginTransaction();	

            try
            {
                foreach($arrayCuadrillas as $intIdCuadrilla)
                {
                    /*
                     * Variable que contendrá los 'idPersonaEmpresaRol' de los integrantes de la cuadrilla que se van ha alterar,
                     * en formato string y separados por los símbolos '||'
                     */
                    $strUsuariosCambiados = ""; 

                    $strEstadoBusqueda      = '';
                    $strMensajeMotivo       = '';
                    $strEstadoACambiar      = '';
                    $intCoordinadorACambiar = 0;//Coordinador que se va a guardar en el campo 'CoordinadorPrestadoId'
                    $arrayParamDiaSemanaCuadrilla = array(    'estado'        => 'Activo', 
                                                            'cuadrillaId'   => $intIdCuadrilla);

                    $objInfoDiaSemanaCuadrilla = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                                            ->findBy($arrayParamDiaSemanaCuadrilla);

                    if( $strAccion == 'prestar')
                    {
                        $strEstadoBusqueda      = $strEstadoActivo;
                        $strEstadoACambiar      = $strEstadoPrestado;
                        $strMensajeMotivo       = 'Se presta la cuadrilla';
                        $intCoordinadorACambiar = $intCoordinadorPrestadoId;
                        $intIdCuadrillaACambiar = $intIdCuadrilla;
                    }
                    elseif( $strAccion == 'devolver')
                    {
                        $strEstadoBusqueda      = $strEstadoPrestado;
                        $strEstadoACambiar      = $strEstadoActivo;
                        $strMensajeMotivo       = 'Se devuelve la cuadrilla';
                        $intCoordinadorACambiar = null;
                        $intIdCuadrillaACambiar = $intIdCuadrilla;
                    }
                    elseif($strAccion == 'recuperar')
                    {
                        $strEstadoBusqueda      = $strEstadoPrestado;
                        $strEstadoACambiar      = $strEstadoActivo;
                        $strMensajeMotivo       = 'Se recupera la cuadrilla';
                        $intCoordinadorACambiar = null;
                        $intIdCuadrillaACambiar = $intIdCuadrilla;
                    }
                    elseif( $strAccion == 'eliminar')
                    {
                        //Verificamos si la cuadrilla tiene al menos una planificación HAL Activa
                        $objAdmiCuadrillaHal = $emComercial->getRepository("schemaBundle:AdmiCuadrilla")->find($intIdCuadrilla);

                        if (!is_object($objAdmiCuadrillaHal))
                        {
                            continue;
                        }

                        $objFechaNow = new \DateTime('now');

                        $arrayPlanificacionTrabajo = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                            ->getSolicitarTrabajoCuadrilla(array ('intIdCuadrilla'     => $objAdmiCuadrillaHal->getId(),
                                                                  'strFechaIni'        => date_format($objFechaNow, 'Y-m-d'),
                                                                  'strEstadoPlanifCab' => 'Activo'));

                        if ($arrayPlanificacionTrabajo['mensaje'] === 'ok' && !empty($arrayPlanificacionTrabajo['planificacion'])
                            && count($arrayPlanificacionTrabajo['planificacion'] > 0))
                        {
                            if ($emComercial->getConnection()->isTransactionActive())
                            {
                                $emComercial->getConnection()->rollback();
                                $emComercial->getConnection()->close();
                            }

                            $strMensaje = "La(s) cuadrilla(s) no puede(n) ser eliminada(s) por tener una<br /> "
                                         ."planificación de hal en estado <b>Activo</b> <br /><br /> "
                                         ."<b>Cuadrilla:</b> <b style='color:green;'>".$objAdmiCuadrillaHal->getNombreCuadrilla().'</b>';

                            $response->setContent($strMensaje);
                            return $response;
                        }
                        else
                        {
                            $strEstadoBusqueda      = $strEstadoActivo;
                            $strEstadoACambiar      = $strEstadoEliminado;
                            $strMensajeMotivo       = 'Se elimina la cuadrilla';
                            $intCoordinadorACambiar = null;
                            $intIdCuadrillaACambiar = null;

                            $strVehiculoAsignado = 'Veh&iacute;culo Asignado:';
                            $objDetalleElemento  = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                     ->findOneBy( 
                                                                                    array( 
                                                                                            'estado'        => $strEstadoActivo,
                                                                                            'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                                            'detalleValor'  => $intIdCuadrilla,
                                                                                         ) 
                                                                                );


                            if( $objDetalleElemento )
                            {
                                $intIdElementoActual = $objDetalleElemento->getElementoId();
                                $objElementoActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                         ->findOneBy( array('id' => $intIdElementoActual, 'estado' => $strEstadoActivo) );

                                if( $objElementoActual )
                                {
                                    $strVehiculoAsignado .= $objElementoActual->getNombreElemento().'<br/><br/>';
                                    $boolEliminarAsignaciones=true;
                                }
                                else
                                {
                                    $strVehiculoAsignado .= 'Sin Asignaci&oacute;n<br/><br/>';
                                }
                            }//( $objDetalleElemento )
                            
                            $arrayTmpParametrosHistoHorario = array(  'estado'        => 'Activo', 
                                                                      'cuadrillaId'   => $objAdmiCuadrillaHal->getId());

                            $objDetalleHistoHorario = $emComercial->getRepository('schemaBundle:InfoHistoHorarioCuadrilla')
                                                                  ->findOneBy($arrayTmpParametrosHistoHorario);
                            
                            $arrayParamDiaSemanaCuadrilla = array(    'estado'        => 'Activo', 
                                                                    'cuadrillaId'   => $objAdmiCuadrillaHal->getId());

                            $objInfoDiaSemanaCuadrilla = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                                     ->findBy($arrayParamDiaSemanaCuadrilla);

                            if ( $objInfoDiaSemanaCuadrilla && $objDetalleHistoHorario && $boolDepConfigHE)
                            {
                                for ($intIndice1=0; $intIndice1 < count($objInfoDiaSemanaCuadrilla); $intIndice1++)
                                {
                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setEstado('Eliminado');
                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setUsrUltMod($objSession->get('user'));
                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setIpUltMod($objRequest->getClientIp());
                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setFechaUltMod(new \DateTime('now'));
                                    $emComercial->persist($objInfoDiaSemanaCuadrilla[$intIndice1]);
                                    $emComercial->flush();
                                }
                                $objDetalleHistoHorario->setEstado('Eliminado');
                                $objDetalleHistoHorario->setUsrUltMod($objSession->get('user'));
                                $objDetalleHistoHorario->setIpUltMod($objRequest->getClientIp());
                                $objDetalleHistoHorario->setFechaUltMod(new \DateTime('now')); 
                                $emComercial->persist($objDetalleHistoHorario);
                                $emComercial->flush();
                            }
                             
                        }
                    }//( $strAccion == 'eliminar')

                    $objMotivo    = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMensajeMotivo);
                    $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                ->findOneBy( array('id' => $intIdCuadrilla, 'estado' => $strEstadoBusqueda) );

                    $intIdMotivo = null;
                    if( $objMotivo )
                    {
                        $intIdMotivo = $objMotivo->getId();
                    }

                    if(!$objCuadrilla)
                    {
                        $boolError  = true;
                    }

                    if( !$boolError )
                    {
                        $arrayParametros                   = array();
                        $arrayParametros['usuario']        = $intIdPersonEmpresaRol;
                        $arrayParametros['departamento']   = $intIdDepartamento;
                        $arrayParametros['empresa']        = $intIdEmpresa;
                        $arrayParametros['exceptoUsr']     = array($intIdPersonEmpresaRol);
                        $arrayParametros['asignadosA']     = $intIdPersonEmpresaRol;
                        $arrayParametros['intIdCuadrilla'] = $objCuadrilla->getId();

                        $intDepartamentoCuadrilla = $objCuadrilla->getDepartamentoId();

                        if ($strAccion == 'prestar')
                        {
                            $arrayParametros['usuario'] = $objCuadrilla->getCoordinadorPrincipalId();
                            $arrayParametros['asignadosA'] = $objCuadrilla->getCoordinadorPrincipalId();
                            $arrayParametros['exceptoUsr']     = array($objCuadrilla->getCoordinadorPrincipalId());
                        }

                        if ($strAccion == 'devolver' &&
                            $intDepartamentoCuadrilla == $intIdDepartamento)
                        {
                            $arrayParametros['usuario'] = $objCuadrilla->getCoordinadorPrincipalId();
                        }

                        if ($strAccion == 'devolver' &&
                            $intDepartamentoCuadrilla != $intIdDepartamento)
                        {
                            $arrayParametros['usuario']         = $objCuadrilla->getCoordinadorPrincipalId();
                            $arrayParametros['departamento']    = $intDepartamentoCuadrilla;
                        }

                        if ($strAccion == 'recuperar' &&
                            $intDepartamentoCuadrilla == $intIdDepartamento)
                        {
                            $arrayParametros['usuario'] = $objCuadrilla->getCoordinadorPrincipalId();
                            $arrayParametros['asignadosA'] = $objCuadrilla->getCoordinadorPrestadoId();
                        }

                        if ($strAccion == 'recuperar' &&
                            $intDepartamentoCuadrilla != $intIdDepartamento)
                        {
                            $arrayParametros['usuario'] = $objCuadrilla->getCoordinadorPrincipalId();
                            $arrayParametros['asignadosA'] = $objCuadrilla->getCoordinadorPrestadoId();
                            $arrayParametros['departamento']    = $intDepartamentoCuadrilla;
                        }

                        if( $strNombreArea == 'Tecnico')
                        {
                            $arrayParametros['nombreArea']       = $strNombreArea;
                            $arrayParametros['rolesNoIncluidos'] = array('Cliente', 'Pre-cliente', 'Mensajero', 'Programador Jr.');
                        }
                        
                        $arrayResultados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->findPersonalByCriterios($arrayParametros);

                        $arrayRegistros = $arrayResultados['registros'];
                        
                        if( $arrayRegistros )
                        {
                            foreach($arrayRegistros as $arrayDatos)
                            {
                                $intTmpIdPersonaEmpresaRol = $arrayDatos['idPersonaEmpresaRol'];
                                $objInfoPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                         ->findOneById($intTmpIdPersonaEmpresaRol);

                                if( $objInfoPersonaEmpresaRol )
                                {
                                    $strNombreUsuario = $objInfoPersonaEmpresaRol->getPersonaId() 
                                                        ? $objInfoPersonaEmpresaRol->getPersonaId()->getInformacionPersona() : '';

                                    $strUsuariosCambiados .= $strNombreUsuario.'<br/>'; 

                                    $arrayTmpParametrosHistoEmple = array(  'estado'        => $strEstadoActivo, 
                                                                            'cuadrillaId'   => $objCuadrilla->getId(), 
                                                                            'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId()->getId());
                                    

                                    $objDetalleHistoEmple = $emComercial->getRepository('schemaBundle:InfoHistoEmpleCuadrilla')
                                                                            ->findOneBy($arrayTmpParametrosHistoEmple);

                                    $arrayParamDiaSemanaCuadrilla = array(    'estado'        => 'Activo', 
                                                                            'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId()
                                                                            );

                                    $objInfoDiaSemanaCuadrilla = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                                             ->findBy($arrayParamDiaSemanaCuadrilla);

                                    if( $strAccion == 'prestar')
                                    {
                                        $intTmpCoordinadorActual    = $objInfoPersonaEmpresaRol->getReportaPersonaEmpresaRolId();
                                        $objInfoCoordinadorActual   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                  ->findOneById($intTmpCoordinadorActual);
                                        $strNombreCoordinadorActual = $objInfoCoordinadorActual->getPersonaId() 
                                                                      ? $objInfoCoordinadorActual->getPersonaId()->getInformacionPersona() : '';

                                        $strMensajeObservacion = 'Jefe Anterior: '.$strNombreCoordinadorActual;
                                        $objInfoPersonaEmpresaRol->setReportaPersonaEmpresaRolId($intCoordinadorACambiar);

                                        $objPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                                        $objPersonaEmpresaRolCaracNew->setEstado($strEstadoActivo);
                                        $objPersonaEmpresaRolCaracNew->setFeCreacion($datetimeActual);
                                        $objPersonaEmpresaRolCaracNew->setIpCreacion($strIpUserSession);
                                        $objPersonaEmpresaRolCaracNew->setUsrCreacion($strUserSession);
                                        $objPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                        $objPersonaEmpresaRolCaracNew->setCaracteristicaId($objCaracteristica);
                                        $objPersonaEmpresaRolCaracNew->setValor('SI');
                                        $emComercial->persist($objPersonaEmpresaRolCaracNew);
                                        $emComercial->flush();
                                        if($boolDepConfigHE)
                                        {
                                            if ( !$objInfoDiaSemanaCuadrilla && $strTipoHorarioId != 1 )
                                            {
                                                for ($intIndice=0; $intIndice < count($arrayDiasSemana1); $intIndice++) 
                                                { 
                                                    $objInfoDiaSemanaCuadrillaNuevo = new InfoDiaSemanaCuadrilla();
                                                    $objInfoDiaSemanaCuadrillaNuevo->setPersonaId($objInfoPersonaEmpresaRol->getPersonaId());
                                                    $objInfoDiaSemanaCuadrillaNuevo->setNumeroDiaId(intval($arrayDiasSemana1[$intIndice]));
                                                    $objInfoDiaSemanaCuadrillaNuevo->setEstado($strEstadoActivo);
                                                    $objInfoDiaSemanaCuadrillaNuevo->setFechaCreacion(new \DateTime('now'));
                                                    $objInfoDiaSemanaCuadrillaNuevo->setUsrCreacion($objSession->get('user'));
                                                    $objInfoDiaSemanaCuadrillaNuevo->setIpCreacion($objRequest->getClientIp());
                                                    $emComercial->persist($objInfoDiaSemanaCuadrillaNuevo);
                                                    $emComercial->flush();
                                                }
                                            }
                                            //historial al agregar un empleado con detalle del horario de la cuadrilla a la que ingreso
                                            if (!$objDetalleHistoEmple)
                                            {
                                                $objInfoHistoEmpleCuadrilla = new InfoHistoEmpleCuadrilla();
                                                $objInfoHistoEmpleCuadrilla->setCuadrillaId($objCuadrilla);
                                                $objInfoHistoEmpleCuadrilla->setPersonaId($objInfoPersonaEmpresaRol->getPersonaId());
                                                $objInfoHistoEmpleCuadrilla->setTipoHorarioId(intval($strTipoHorarioId));
                                                $objInfoHistoEmpleCuadrilla->setFechaInicio(date("d-m-Y", strtotime($strFechaInicio)));
                                                $objInfoHistoEmpleCuadrilla->setHoraInicio($strHoraInicio);
                                                $objInfoHistoEmpleCuadrilla->setFechaFin(date("d-m-Y", strtotime($strFechaFin)));
                                                $objInfoHistoEmpleCuadrilla->setHoraFin($strHoraFin);
                                                $objInfoHistoEmpleCuadrilla->setEstado($strEstadoActivo);
                                                $objInfoHistoEmpleCuadrilla->setUsrCreacion($objSession->get('user'));
                                                $objInfoHistoEmpleCuadrilla->setIpCreacion($objRequest->getClientIp());
                                                $objInfoHistoEmpleCuadrilla->setFechaCreacion(new \DateTime('now'));
                                                $emComercial->persist($objInfoHistoEmpleCuadrilla);
                                                $emComercial->flush();
                                            }
                                        }

                                    }
                                    elseif( $strAccion == 'devolver' || $strAccion == 'recuperar')
                                    {
                                        $intTmpCoordinadorActual    = $objInfoPersonaEmpresaRol->getReportaPersonaEmpresaRolId();
                                        $objInfoCoordinadorActual   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                  ->findOneById($intTmpCoordinadorActual);
                                        $strNombreCoordinadorActual = $objInfoCoordinadorActual->getPersonaId() 
                                                                      ? $objInfoCoordinadorActual->getPersonaId()->getInformacionPersona() : '';

                                        $strMensajeObservacion  = 'Jefe Anterior: '.$strNombreCoordinadorActual.'<br>';

                                        $arrayTmpParametrosCaracteristica = array( 
                                                                                    'estado'              => $strEstadoActivo,
                                                                                    'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                                    'caracteristicaId'    => $objCaracteristica
                                                                                 );

                                        $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                                 ->findOneBy( $arrayTmpParametrosCaracteristica );

                                        if( $objPersonaEmpresaRolCarac )
                                        {
                                            $objPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);
                                            $objPersonaEmpresaRolCarac->setFeUltMod($datetimeActual);
                                            $objPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                                            $emComercial->persist($objPersonaEmpresaRolCarac);
                                            $emComercial->flush();

                                            $intTmpCoordinadorACambiar = $objCuadrilla->getCoordinadorPrincipalId();
                                        }
                                        else
                                        {
                                            $intTmpCoordinadorACambiar  = $objCuadrilla->getCoordinadorPrestadoId();
                                            $objTmpCuadrillaAnterior    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                                      ->findOneById($objInfoPersonaEmpresaRol->getCuadrillaId());
                                            $strCodigoCuadrillaAnterior = $objTmpCuadrillaAnterior ? $objTmpCuadrillaAnterior->getCodigo() : '';
                                            $strMensajeObservacion      .= 'Cuadrilla anterior: '.$strCodigoCuadrillaAnterior;

                                            $objInfoPersonaEmpresaRol->setCuadrillaId(null);
                                        }

                                        $objInfoPersonaEmpresaRol->setReportaPersonaEmpresaRolId($intTmpCoordinadorACambiar);

                                        if ($objInfoDiaSemanaCuadrilla && $objDetalleHistoEmple 
                                            && $objDetalleHistoEmple->getTipoHorarioId() !=1 && $boolDepConfigHE)
                                        {   
                                            for ($intIndice=0; $intIndice < count($objInfoDiaSemanaCuadrilla); $intIndice++) 
                                            {
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setEstado('Inactivo');
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setUsrUltMod($objSession->get('user'));
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setIpUltMod($objRequest->getClientIp());
                                            $objInfoDiaSemanaCuadrilla[$intIndice]->setFechaUltMod(new \DateTime('now'));
                                            $emComercial->persist($objInfoDiaSemanaCuadrilla[$intIndice]);
                                            $emComercial->flush();
                                            }
    
                                            $objDetalleHistoEmple->setEstado('Inactivo');
                                            $objDetalleHistoEmple->setUsrUltMod($objSession->get('user'));
                                            $objDetalleHistoEmple->setIpUltMod($objRequest->getClientIp());
                                            $objDetalleHistoEmple->setFechaUltMod(new \DateTime('now')); 
                                            $emComercial->persist($objDetalleHistoEmple);
                                            $emComercial->flush();
                                        }

                                    }
                                    elseif( $strAccion == 'eliminar')
                                    {
                                        $objTmpCuadrillaAnterior    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                                  ->findOneById($objInfoPersonaEmpresaRol->getCuadrillaId());
                                        $strCodigoCuadrillaAnterior = $objTmpCuadrillaAnterior ? $objTmpCuadrillaAnterior->getCodigo() : '';

                                        $strMensajeObservacion = 'Cuadrilla anterior: '.$strCodigoCuadrillaAnterior;
                                        $objInfoPersonaEmpresaRol->setCuadrillaId($intIdCuadrillaACambiar);


                                        /*
                                         * Bloque que desasocia una tablet con el personal asignado a una cuadrilla
                                         */
                                        $arrayTmpParametrosTablet = array( 'estado'        => $strEstadoActivo, 
                                                                           'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_TABLET, 
                                                                           'detalleValor'  => $intTmpIdPersonaEmpresaRol );

                                        $objDetalleTablet = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                              ->findOneBy($arrayTmpParametrosTablet);

                                        if( $objDetalleTablet )
                                        {
                                            $strTabletActual   = 'Sin asignaci&oacute;n';
                                            $intIdTabletActual = $objDetalleTablet->getElementoId();
                                            $objTabletActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                   ->findOneBy( array( 'id'     => $intIdTabletActual,
                                                                                                       'estado' => $strEstadoActivo ) 
                                                                                              );
                                            if( $objTabletActual )
                                            {
                                                $strTabletActual = $objTabletActual->getNombreElemento();
                                            }

                                            $objDetalleTablet->setEstado($strEstadoEliminado);
                                            $emInfraestructura->persist($objDetalleTablet);
                                            $emInfraestructura->flush();


                                            $strMotivoElementoTablet = 'Se elimina tablet asociada';
                                            $objMotivoTablet         = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                                 ->findOneByNombreMotivo($strMotivoElementoTablet);
                                            $intIdMotivoTablet       = $objMotivoTablet ? $objMotivoTablet->getId() : 0;

                                            $strMensajeObservacion = $strMotivoElementoTablet.": ".$strTabletActual;

                                            $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                                            $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                                            $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                                            $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                                            $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                            $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                                            $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                                            $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivoTablet);
                                            $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                                            $emComercial->flush();
                                        }//( $objDetalleTablet )
                                        /*
                                         * Fin del Bloque que desasocia una tablet con el personal asignado a una cuadrilla
                                         */
                                        //historial al eliminar empleado con detalle del horario de la cuadrilla a la que esta saliendo
                                        if($boolDepConfigHE)
                                        {
                                            if($objDetalleHistoEmple) 
                                            {
                                                $objDetalleHistoEmple->setEstado('Eliminado');
                                                $objDetalleHistoEmple->setUsrUltMod($strUserSession);
                                                $objDetalleHistoEmple->setIpUltMod($strIpUserSession);
                                                $objDetalleHistoEmple->setFechaUltMod($datetimeActual);
                                                $emComercial->persist($objDetalleHistoEmple);
                                                $emComercial->flush();
                                            }
    
                                            if ( $objInfoDiaSemanaCuadrilla && $objDetalleHistoHorario)
                                            {
                                                for ($intIndice1=0; $intIndice1 < count($objInfoDiaSemanaCuadrilla); $intIndice1++)
                                                {
                                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setEstado('Eliminado');
                                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setUsrUltMod($objSession->get('user'));
                                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setIpUltMod($objRequest->getClientIp());
                                                    $objInfoDiaSemanaCuadrilla[$intIndice1]->setFechaUltMod(new \DateTime('now'));
                                                    $emComercial->persist($objInfoDiaSemanaCuadrilla[$intIndice1]);
                                                    $emComercial->flush();
                                                }
                                                $objDetalleHistoHorario->setEstado('Eliminado');
                                                $objDetalleHistoHorario->setUsrUltMod($objSession->get('user'));
                                                $objDetalleHistoHorario->setIpUltMod($objRequest->getClientIp());
                                                $objDetalleHistoHorario->setFechaUltMod(new \DateTime('now')); 
                                                $emComercial->persist($objDetalleHistoHorario);
                                                $emComercial->flush();
                                            }
                                        }
                                    }//( $strAccion == 'prestar')

                                    $emComercial->persist($objInfoPersonaEmpresaRol);
                                    $emComercial->flush();	

                                    $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                                    $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                                    $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                                    $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                                    $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                    $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                                    $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                                    $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                                    $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                                    $emComercial->flush();	
                                    
                                }//( $objInfoPersonaEmpresaRol )
                            }//foreach($arrayRegistros as $arrayDatos)
                        }
                        else
                        {
                            //throw new \Exception('No se pudo eliminar la cuadrilla porque no se encontraron los empleados vinculados a la cuadrilla');
                        }//( $arrayRegistros )

                            
                        /*
                         * Informacion de la Cuadrilla Anterior
                         */
                        $strEstadoAnterior      = $objCuadrilla->getEstado();
                        $objCoordinadorAnterior = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findOneById($objCuadrilla->getCoordinadorPrincipalId());

                        $strNombreCoordinadorPrincipalAnterior = "";

                        if($objCoordinadorAnterior)
                        {
                            $strNombreCoordinadorPrincipalAnterior = $objCoordinadorAnterior->getPersonaId() 
                                                                     ? $objCoordinadorAnterior->getPersonaId()->getInformacionPersona() : '';
                        }


                        $strNombreCoordinadorPrestadoAnterior = "";

                        if( $strEstadoAnterior == 'Prestado' )
                        {
                            $objCoordinadorPrestadoAnterior  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->findOneById($objCuadrilla->getCoordinadorPrestadoId());

                            if($objCoordinadorPrestadoAnterior)
                            {
                                $strNombreCoordinadorPrestadoAnterior = $objCoordinadorPrestadoAnterior->getPersonaId() 
                                                                        ? $objCoordinadorPrestadoAnterior->getPersonaId()->getInformacionPersona():'';
                            }
                        }
                        /*
                         * Fin Informacion de la Cuadrilla Anterior
                         */


                        $objCuadrilla->setFeUltMod($datetimeActual);
                        $objCuadrilla->setUsrModificacion($strUserSession);
                        $objCuadrilla->setEstado($strEstadoACambiar);
                        $objCuadrilla->setCoordinadorPrestadoId($intCoordinadorACambiar);
                        $emComercial->persist($objCuadrilla);
                        $emComercial->flush();


                        /*
                         * Informacion de la Cuadrilla Nueva
                         */
                        $objCoordinadorNuevo = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->findOneById($objCuadrilla->getCoordinadorPrincipalId());

                        $strNombreCoordinadorPrincipalNuevo = "";

                        if($objCoordinadorNuevo)
                        {
                           $strNombreCoordinadorPrincipalNuevo = $objCoordinadorNuevo->getPersonaId() 
                                                                 ? $objCoordinadorNuevo->getPersonaId()->getInformacionPersona() : '';
                        }


                        $strNombreCoordinadorPrestadoNuevo = "";

                        $objCoordinadorPrestadoNuevo = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->findOneById($objCuadrilla->getCoordinadorPrestadoId());

                        if($objCoordinadorPrestadoNuevo)
                        {
                           $strNombreCoordinadorPrestadoNuevo = $objCoordinadorPrestadoNuevo->getPersonaId() 
                                                                ? $objCoordinadorPrestadoNuevo->getPersonaId()->getInformacionPersona() : '';
                        }
                        /*
                         * Fin Informacion de la Cuadrilla Nueva
                         */


                        if( $strAccion == 'prestar')
                        {
                            $strMensajeObservacion = "Pr&eacute;stamo de Cuadrilla<br><br>".
                                                     "<b>Datos Anteriores</b>".
                                                     "Coordinador Principal: ".$strNombreCoordinadorPrincipalAnterior."<br/>".
                                                     "Coordinador Prestado: ".$strNombreCoordinadorPrestadoAnterior."<br/>".
                                                     "<b>Datos Nuevos</b>".
                                                     "Coordinador Principal: ".$strNombreCoordinadorPrincipalNuevo."<br/>".
                                                     "Coordinador Prestado: ".$strNombreCoordinadorPrestadoNuevo."<br/><br/>".
                                                     "Se prestan los siguientes miembros de la cuadrilla:<br/>"
                                                      .$strUsuariosCambiados;
                        }
                        elseif( $strAccion == 'devolver')
                        {
                            $strMensajeObservacion = "Devoluci&oacute;n de Cuadrilla<br><br>".
                                                     "<b>Datos Anteriores</b>".
                                                     "Coordinador Principal: ".$strNombreCoordinadorPrincipalAnterior."<br/>".
                                                     "Coordinador Prestado: ".$strNombreCoordinadorPrestadoAnterior."<br/>".
                                                     "<b>Datos Nuevos</b>".
                                                     "Coordinador Principal: ".$strNombreCoordinadorPrincipalNuevo."<br/>".
                                                     "Coordinador Prestado: ".$strNombreCoordinadorPrestadoNuevo."<br/><br/>".
                                                     "Se devuelven los siguientes miembros de la cuadrilla:<br/>"
                                                     .$strUsuariosCambiados;
                        }
                        elseif($strAccion == 'recuperar')
                        {
                            $strEmpleado            = $objSession->get('empleado') ? $objSession->get('empleado') : '';
                            $strMensajeObservacion  = "Recuperaci&oacute;n de Cuadrilla<br><br>".
                                                        "Recuperada por: ".$strEmpleado."<br/>".
                                                        "<b>Datos Anteriores</b>".
                                                        "Coordinador Principal: ".$strNombreCoordinadorPrincipalAnterior."<br/>".
                                                        "Coordinador Prestado: ".$strNombreCoordinadorPrestadoAnterior."<br/>".
                                                        "<b>Datos Nuevos</b>".
                                                        "Coordinador Principal: ".$strNombreCoordinadorPrincipalNuevo."<br/>".
                                                        "Coordinador Prestado: ".$strNombreCoordinadorPrestadoNuevo."<br/><br/>".
                                                        "Se devuelven los siguientes miembros de la cuadrilla:<br/>"
                                                        .$strUsuariosCambiados;
                        }
                        elseif( $strAccion == 'eliminar')
                        {
                            $strMensajeObservacion = "Eliminaci&oacute;n de Cuadrilla<br><br>".
                                                     "Se desasocia ".$strVehiculoAsignado.
                                                     "Se eliminan los siguientes miembros de la cuadrilla:<br/>"
                                                     .$strUsuariosCambiados;

                            $strMotivoElemento = 'Se desasocia veh&iacute;culo de la cuadrilla '.$objCuadrilla->getCodigo();

                            if( $objElementoActual )
                            {
                                $objInfoHistorialElemento = new InfoHistorialElemento();
                                $objInfoHistorialElemento->setElementoId($objElementoActual);
                                $objInfoHistorialElemento->setObservacion($strMotivoElemento);
                                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                                $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                                $emInfraestructura->persist($objInfoHistorialElemento);
                                $emInfraestructura->flush();
                            }//( $objElementoActual )
                        }//( $strAccion == 'eliminar')

                        $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                        $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
                        $objCuadrillaHistorial->setEstado($strEstadoACambiar);
                        $objCuadrillaHistorial->setFeCreacion($datetimeActual);
                        $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                        $objCuadrillaHistorial->setObservacion($strMensajeObservacion);
                        $objCuadrillaHistorial->setMotivoId($intIdMotivo);
                        $emComercial->persist($objCuadrillaHistorial);
                        $emComercial->flush();

                    }//( !$boolError )
                }//foreach($arrayCuadrillas as $intIdCuadrilla)

                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();


                /*==================================================================
                ====================== INICIO NOTIFICACION HAL =====================
                ==================================================================*/

                if ($strAccion == 'eliminar')
                {
                    foreach ($arrayCuadrillas as $intIdCuadrilla)
                    {
                        $serviceSoporte->notificacionesHal(
                            array ('strModulo' => 'cuadrilla',
                                   'strUser'   =>  $strUserSession,
                                   'strIp'     =>  $strIpUserSession,
                                   'arrayJson' =>  array ('metodo' => 'elimino',
                                                          'id'     => $intIdCuadrilla)));
                    }
                }

                /*===============================================================
                ====================== FIN NOTIFICACION HAL =====================
                ================================================================*/


                $strMensaje = 'OK';
            }
            catch(\Exception $e)
            {
                error_log($e->getMessage());

                $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }//try
        }
        else
        {
            $strMensaje = 'No se ha seleccionado ninguna cuadrilla';
        }//( $arrayCuadrillas )
        
        if($boolEliminarAsignaciones && $strMensaje=='OK')
        {
            $arrayParametrosEliminacion = array(
            "intIdElemento"     => $intIdElementoActual,
            "intIdCuadrilla"    => $intIdCuadrilla
            );

            /* @var $serviceInfoElemento \telconet\tecnicoBundle\Service\InfoElementoService */
            $serviceInfoElemento            = $this->get('tecnico.InfoElemento');
            $strMensajeEliminacionAsignacion= $serviceInfoElemento->eliminarAsignacionVehicularYProvisionalXCuadrilla($arrayParametrosEliminacion);
        }
        
        $response->setContent( $strMensaje );

        return $response;
    }


    /**
     * Documentación para el método 'estadosAction'.
     *
     * Estados correspondientes a las cuadrillas
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 14-10-2015 
     */
    public function estadosAction()
    {
        $response    = new JsonResponse();
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $intTotal     = 0;
        $arrayEstados = array();

        $arrayResultados = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->getEstadosCuadrillas();

        if($arrayResultados)
        {
            foreach($arrayResultados as $arrayEstado)
            {
                $item              = array();
                $item['strValue']  = $arrayEstado['estado'];
                $item['strNombre'] = $arrayEstado['estado'];

                $arrayEstados[] = $item;

                $intTotal++;
            }
        }

        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayEstados) );

        return $response;
    }


    /**
     * @Secure(roles="ROLE_170-3117")
     * 
     * Documentación para el método 'prestarEmpleadosAction'.
     *
     * Redireccion a la pantalla de préstamo de empleados asignados a un coordinador.
     * @return render.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-10-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 29-11-2016 - Se elimina la validación donde se le permite al ayudante del coordinador visualizar las cuadrillas creadas 
     *                           por el coordinador al que reporta y se envía directamente el id_persona_empresa_rol de sesión
     * 
     * @author Modificado: Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.2 18-01-2023  - Se agrega el parametro 'strNombreDepartamento' al arrayParametrosView
     * 
     */
    public function prestarEmpleadosAction()
    {
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $strNombreArea = "Tecnico";
        $emComercial   = $this->getDoctrine()->getManager('telconet');

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        
        $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')->getRolEmpleadoEmpresa( array('usuario' => $intIdPersonEmpresaRol) );

        $strNombreDepartamento = '';
        $strCodEmpresa         = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $strUsurioActual       = $objSession->get('user') ? $objSession->get('user') : '';

        $arrayEmpleado          = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getPersonaDepartamentoPorUserEmpresa($strUsurioActual, $strCodEmpresa);
        if ( $arrayEmpleado )
        {
            $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
        }
        
        $arrayParametrosView = array('strCargo' => $strCargo, 'intIdPersonaEmpresaRol' => $intIdPersonEmpresaRol, 'strNombreArea'=> $strNombreArea);
        
        $arrayParametrosView['strNombreDepartamento'] = $strNombreDepartamento;

        return $this->render('administracionBundle:AdmiCuadrilla:prestarEmpleados.html.twig', $arrayParametrosView);
    }


    /**
     * @Secure(roles="ROLE_170-3117")
     * 
     * Documentación para el método 'cambioCoordinadorEmpleadoAction'.
     *
     * Método que realiza el préstamo o la devolución de un empleado a un coordinador.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-10-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-11-2015 - Se modifica para que al buscar en la tabla 'InfoPersonaEmpresaRolCarac' se envíe como objetos los parámetros de
     *                           'caracteristicaId' y 'personaEmpresaRolId'.
     * 
     * @author Modificado: Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.2 18-11-2023 - Se agrega la opción re recuperar un empleado prestado
     * 
     */
    public function cambioCoordinadorEmpleadoAction()
    {
        $response           = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $strMensaje         = 'No se encontró persona en estado activo';
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';

        $strEmpleadosSeleccionados = $objRequest->get('empleados') ? trim($objRequest->get('empleados')) : '';
        $arrayEmpleados            = explode("|", $strEmpleadosSeleccionados);
        $strAccion                 = $objRequest->get('accion') ? trim($objRequest->get('accion')) : '';
        $intCoordinadorPrestadoId  = $objRequest->get('coordinadorPrestado') ? trim($objRequest->get('coordinadorPrestado')) : 0;

        if( $strAccion == "prestar" )
        {
            $strNombreMotivo = "Se presta empleado a un coordinador";
        }
        elseif( $strAccion == "devolver" )
        {
            $strNombreMotivo = "Se devuelve empleado a un coordinador";
        }
        elseif( $strAccion = "recuperar")
        {
            $strNombreMotivo = "Se recupera empleado prestado";
        }

        $objMotivo   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strNombreMotivo);
        $intIdMotivo = $objMotivo ? $objMotivo->getId() : 0;

        $arrayDescripcionesCaracteristicas = array( 'prestamoCuadrilla' => self::CARACTERISTICA_PRESTAMO_CUADRILLA, 
                                                    'prestamoEmpleado'  => self::CARACTERISTICA_PRESTAMO_EMPLEADO );

        $arrayTmpIdCaracteristicas = array( 'prestamoCuadrilla' => 0, 'prestamoEmpleado'  => 0 );

        foreach($arrayDescripcionesCaracteristicas as $key => $value)
        {
            $arrayParametrosCaracteristica = array( 'descripcionCaracteristica' => $value,
                                                    'estado'                    => $strEstadoActivo );

            $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy( $arrayParametrosCaracteristica );

            $arrayTmpIdCaracteristicas[$key] = $objCaracteristica;
        }



        if( $arrayEmpleados )
        {
            $emComercial->getConnection()->beginTransaction();	

            try
            {
                foreach( $arrayEmpleados as $intIdEmpleado )
                {
                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdEmpleado);

                    if( $objInfoPersonaEmpresaRol )
                    {
                        $strNombreEmpleadoARealizarCambios = $objInfoPersonaEmpresaRol->getPersonaId() 
                                                             ? $objInfoPersonaEmpresaRol->getPersonaId()->getInformacionPersona() : '';

                        $intIdCuadrillaActual     = $objInfoPersonaEmpresaRol->getCuadrillaId() 
                                                    ? $objInfoPersonaEmpresaRol->getCuadrillaId()->getId() : 0;
                        $objCuadrillaActual       = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($intIdCuadrillaActual);
                        $intTmpCoordinadorActual  = $objInfoPersonaEmpresaRol->getReportaPersonaEmpresaRolId();

                        if( $objCuadrillaActual )
                        {
                            $objInfoPersonaEmpresaRol->setCuadrillaId(null);

                            $strCodigoCuadrillaAnterior = $objCuadrillaActual->getCodigo();
                            $strMensajeObservacion      = 'Cuadrilla anterior: '.$strCodigoCuadrillaAnterior;

                            $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                            $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                            $objInfoPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                            $objInfoPersonaEmpresaRolHistorial->setIpCreacion($objSession->get('user'));
                            $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                            $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($objSession->get('user'));
                            $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                            $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                            $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                            $emComercial->flush();	


                            if( $strAccion == "prestar" )
                            {
                                $strMensajeObservacion = "Pr&eacute;stamo de Empleado<br>".
                                                         "Se presta el siguiente empleado:<br/>".$strNombreEmpleadoARealizarCambios;
                            }
                            elseif( $strAccion == "devolver" )
                            {
                                $strMensajeObservacion = "Devoluci&oacute;n de Empleado<br>".
                                                         "Se devuelve el siguiente empleado:<br/>".$strNombreEmpleadoARealizarCambios;
                            }
                            elseif( $strAccion == "recuperar" )
                            {
                                $strMensajeObservacion = "Recpueraci&oacute;n de Empleado<br>".
                                                         "Se recupera el siguiente empleado:<br/>".$strNombreEmpleadoARealizarCambios;
                            }

                            $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                            $objCuadrillaHistorial->setCuadrillaId($objCuadrillaActual);
                            $objCuadrillaHistorial->setEstado($objCuadrillaActual->getEstado());
                            $objCuadrillaHistorial->setFeCreacion(new \DateTime('now'));
                            $objCuadrillaHistorial->setUsrCreacion($objSession->get('user'));
                            $objCuadrillaHistorial->setObservacion($strMensajeObservacion);
                            $objCuadrillaHistorial->setMotivoId($intIdMotivo);
                            $emComercial->persist($objCuadrillaHistorial);
                            $emComercial->flush();


                            if( $objCuadrillaActual->getEstado() == "Prestado" )
                            {
                                $intTmpCoordinadorActual = $objCuadrillaActual->getCoordinadorPrestadoId();
                            }

                            $objCuadrillaActual->setFeUltMod(new \DateTime('now'));
                            $objCuadrillaActual->setUsrModificacion($objSession->get('user'));
                            $emComercial->persist($objCuadrillaActual);
                            $emComercial->flush();
                        }//( $objCuadrillaActual )


                        foreach($arrayTmpIdCaracteristicas as $key => $objCaracteristica)
                        {
                            $arrayTmpParametrosCaracteristica = array( 
                                                                        'estado'              => $strEstadoActivo,
                                                                        'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                        'caracteristicaId'    => $objCaracteristica
                                                                     );

                            $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                     ->findBy( $arrayTmpParametrosCaracteristica );

                            if( $objPersonaEmpresaRolCarac )
                            {
                                foreach( $objPersonaEmpresaRolCarac as $objDatoCaracteristica )
                                {
                                    if( ($strAccion == "devolver" || $strAccion == "recuperar") && $key == "prestamoEmpleado" )
                                    {
                                        $intTmpCoordinadorActual = $objDatoCaracteristica->getValor();
                                    }

                                    $objDatoCaracteristica->setEstado($strEstadoEliminado);
                                    $objDatoCaracteristica->setFeUltMod(new \DateTime('now'));
                                    $objDatoCaracteristica->setUsrUltMod($objSession->get('user'));
                                    $emComercial->persist($objDatoCaracteristica);
                                    $emComercial->flush();
                                }//foreach( $objPersonaEmpresaRolCarac as $objDatoCaracteristica )
                            }//( $objPersonaEmpresaRolCarac )
                        }//foreach($arrayTmpIdCaracteristicas as $intIdCaracteristica)

                        if( $strAccion == "prestar" )
                        {
                            $objPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                            $objPersonaEmpresaRolCaracNew->setEstado($strEstadoActivo);
                            $objPersonaEmpresaRolCaracNew->setFeCreacion(new \DateTime('now'));
                            $objPersonaEmpresaRolCaracNew->setIpCreacion($objRequest->getClientIp());
                            $objPersonaEmpresaRolCaracNew->setUsrCreacion($objSession->get('user'));
                            $objPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                            $objPersonaEmpresaRolCaracNew->setCaracteristicaId($arrayTmpIdCaracteristicas['prestamoEmpleado']);
                            $objPersonaEmpresaRolCaracNew->setValor($intCoordinadorPrestadoId);
                            $emComercial->persist($objPersonaEmpresaRolCaracNew);
                            $emComercial->flush();
                        }


                        $objInfoCoordinadorActual   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->findOneById($intTmpCoordinadorActual);
                        $strNombreCoordinadorActual = $objInfoCoordinadorActual->getPersonaId() 
                                                      ? $objInfoCoordinadorActual->getPersonaId()->getInformacionPersona() : '';

                        $strMensajeObservacion  = 'Jefe Anterior: '.$strNombreCoordinadorActual;

                        $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                        $objInfoPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHistorial->setIpCreacion($objSession->get('user'));
                        $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($objSession->get('user'));
                        $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                        $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                        $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                        $emComercial->flush();


                        $emComercial->persist($objInfoPersonaEmpresaRol);
                        $emComercial->flush();	
                    }//( $objInfoPersonaEmpresaRol )
                }//foreach( $arrayEmpleados as $intIdEmpleado )

                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();

                $strMensaje = 'OK';
            }
            catch(\Exception $e)
            {
                error_log($e->getMessage());

                $strMensaje = 'Hubo un problema de base de datos por favor contactar al departamento de sistemas';

                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }//try
        }
        else
        {
            $strMensaje = 'No se ha seleccionado ningún empleado';
        }//( $arrayEmpleados )

        $response->setContent( $strMensaje );

        return $response;
    }


    /**
     * Documentación para el método 'getElementosAction'.
     *
     * Retorna los elementos creados actualmente dependiendo de los criterios enviados por el usuario
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 10-11-2015 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-11-2015 - Se agrega que se envíen los elementos creados dependiendo de la empresa del usuario en sessión 
     */
    public function getElementosAction()
    {
        $response            = new JsonResponse();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $intIdEmpresaSession = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intTotal            = 0;
        $arrayElementos      = array();
        $serviceInfoElemento = $this->get('tecnico.InfoElemento');

        $intIdElementoAsignado        = $objRequest->get('intIdElementoAsignado') ? trim($objRequest->get('intIdElementoAsignado')) : 0;
        $strModeloElemento            = $objRequest->get('strModeloElemento') ? trim($objRequest->get('strModeloElemento')) : '';
        $strCategoriaElemento         = $objRequest->get('strCategoria') ? trim($objRequest->get('strCategoria')) : '';
        $strMostrarElementosAsignados = $objRequest->get('strMostrarElementosAsignados') 
                                        ? trim($objRequest->get('strMostrarElementosAsignados')) : 'S';
        $arrayDetallesNoMostrados     = array(  'tablet'     => array(self::DETALLE_ASOCIADO_ELEMENTO_TABLET), 
                                                'transporte' => array(self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO) );
        $strNombreElemento            = $objRequest->query->get('query') ? trim($objRequest->query->get('query')) : '';

        $arrayParametros = array(
                                    'strEstadoActivo'          => 'Activo',
                                    'intEmpresa'               => $intIdEmpresaSession,
                                    'strCategoriaElemento'     => $strCategoriaElemento,
                                    'arrayDetallesNoMostrados' => $arrayDetallesNoMostrados,
                                    'criterios'                => array( 'noMostrarElemento'         => array( $intIdElementoAsignado ), 
                                                                         'modeloElemento'            => array( $strModeloElemento ),
                                                                         'nombre'                    => $strNombreElemento,
                                                                         'mostrarElementosAsignados' => $strMostrarElementosAsignados )
                                );

        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);

        if($arrayResultados['encontrados'])
        {
            foreach($arrayResultados['encontrados'] as $arrayElemento)
            {
                if( $intIdElementoAsignado != $arrayElemento['intIdElemento'] )
                {
                    $item                      = array();
                    $item['intIdElemento']     = $arrayElemento['intIdElemento'];
                    $item['strNombreElemento'] = $arrayElemento['strNombreElemento'];

                    $arrayElementos[] = $item;

                    $intTotal++;
                }//if( $intIdVehiculoAsignado != $objVehiculo->getId() )
            }//foreach($arrayResultados as $objVehiculo)
        }//($arrayResultados)

        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayElementos) );

        return $response;
    }


    /**
     * @Secure(roles="ROLE_170-3137")
     * 
     * Documentación para el método 'asignarElementoAction'.
     *
     * Método que asigna la relación de una elemento con un empleado o una cuadrilla.
     * 
     * @return JsonResponse 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 10-11-2015
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 20-11-2015 - Se adapta la opción para la asignación de 'Tablet' a un Líder o un Jefe Cuadrilla
     * 
     */
    public function asignarElementoAction()
    {
        $response           = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $boolError          = false;
        $strMensaje         = 'No se encontró cuadrilla activa';
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';

        $intIdAsociado             = $objRequest->get('idAsociado') ? trim($objRequest->get('idAsociado')) : '';
        $intIdElementoSeleccionado = $objRequest->get('elemento') ? trim($objRequest->get('elemento')) : '';
        $strCategoriaElemento      = $objRequest->get('strCategoria') ? trim($objRequest->get('strCategoria')) : '';



        $objAsociado            = null;
        $strDetalleAsociado     = '';

        $strMotivoElemento      = '';


        if( $strCategoriaElemento == 'tablet' )
        {
            $strMotivoElemento  = 'Se asigna tablet';
            $objAsociado        = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneBy( array('id' => $intIdAsociado) );
            $strDetalleAsociado = self::DETALLE_ASOCIADO_ELEMENTO_TABLET;
        }

        $objMotivo   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoElemento);
        $intIdMotivo = $objMotivo ? $objMotivo->getId() : 0;

        //$objElemento es el nuevo elemento que se quiere asignar a la cuadrilla
        $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                         ->findOneBy( array('id' => $intIdElementoSeleccionado, 'estado' => $strEstadoActivo) );

        if(!$objAsociado)
        {
            $boolError = true;
        }
        else
        {
            if( $objAsociado->getEstado() == 'Eliminado' )
            {
                $boolError = true;
            }
        }

        if(!$boolError)
        {
            $emComercial->getConnection()->beginTransaction();	

            try
            {
                $strEstadoActualAsociado = $objAsociado->getEstado();

                $strElementoActual  = 'Sin Asignaci&oacute;n';

                //Busca si ya hay algún vehículo asignado
                $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                            'detalleNombre' => $strDetalleAsociado, 
                                                                            'detalleValor'  => $intIdAsociado ) 
                                                                   );


                if( $strCategoriaElemento == 'tablet' )
                {
                    $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->findOneBy( array('id' => $intIdAsociado, 'estado' => $strEstadoActivo) );

                    $strMensajeObservacion = $strMotivoElemento.": ".($objElemento ? $objElemento->getNombreElemento() : "Sin Asignaci&oacute;n");

                    $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                    $objInfoPersonaEmpresaRolHistorial->setEstado($objPersonaEmpresaRol->getEstado());
                    $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                    $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                    $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                    $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                    $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivo);
                    $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                    $emComercial->flush();

                    $strTmpNombreEmpleado = $objPersonaEmpresaRol->getPersonaId() 
                                            ? $objPersonaEmpresaRol->getPersonaId()->getInformacionPersona() : '';

                    $strMotivoElemento .= ' a '.$strTmpNombreEmpleado;

                }//( $strCategoriaElemento == 'tablet' )

                //if( $intIdElementoSeleccionado && !$boolErrorAsignacion )
                if( $intIdElementoSeleccionado)
                {

                    if( $objDetalleElemento )
                    {
                        $intIdElementoActual = $objDetalleElemento->getElementoId();
                        $objElementoActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                 ->findOneBy( array('id' => $intIdElementoActual, 'estado' => $strEstadoActivo) );

                        if( $objElementoActual )
                        {
                            $strElementoActual = $objElementoActual->getNombreElemento();
                        }

                        $objDetalleElemento->setEstado($strEstadoEliminado);
                        $emInfraestructura->persist($objDetalleElemento);
                        $emInfraestructura->flush();
                    }

                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($intIdElementoSeleccionado);
                    $objInfoDetalleElemento->setDetalleNombre($strDetalleAsociado);
                    $objInfoDetalleElemento->setDetalleValor($intIdAsociado);
                    $objInfoDetalleElemento->setDetalleDescripcion($strDetalleAsociado);
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado($strEstadoActivo);
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();

                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objElemento);
                    $objInfoHistorialElemento->setObservacion($strMotivoElemento);
                    $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                    $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                    $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                    $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();
                }//( $intIdElementoSeleccionado )

                    $emComercial->getConnection()->commit();
                    $strMensaje = 'OK';
                    $emComercial->getConnection()->close(); 
            }
            catch(\Exception $e)
            {
                error_log($e->getMessage());

                $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }//try
        }//(!$boolError)

        $response->setContent( $strMensaje );

        return $response;
    }


    /**
     * Documentación para el método 'verificarIntegrantesCuadrillaAction'.
     *
     * Verifica que la cuadrilla no contengan personal prestado de otro coordinador al momento que se va a realizar el préstamo de una cuadrilla.
     * 
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-11-2015
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.1 17-01-2023 - Se valida la acción que invoca este método para que el mensaje cambien
     *                              en base a dicha acción. 
     */
    public function verificarIntegrantesCuadrillaAction()
    {
        $response         = new Response();
        $objRequest       = $this->get('request');
        $intIdCuadrilla   = $objRequest->request->get('cuadrilla') ? $objRequest->request->get('cuadrilla') : 0;
        $strAccion        = $objRequest->request->get('accion') ? $objRequest->request->get('accion') : '';
        $strMensaje       = 'No se puede prestar cuadrilla<br/><br/>';
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $intContadorError = 0;
        $strEstadoActivo  = 'Activo';

        try
        {
            if ( $strAccion == 'devolver' )
            {
                $strMensaje = 'No se puede devolver la cuadrilla <br/><br/>';
            }
            elseif ($strAccion == 'recuperar')
            {
                $strMensaje = 'No se puede recuperar la cuadrilla <br/><br/>';
            }

            $objIntegrantesCuadrilla = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByCuadrillaId($intIdCuadrilla);

            if( $objIntegrantesCuadrilla )
            {
                $arrayParametrosCaracteristica = array( 'descripcionCaracteristica' => self::CARACTERISTICA_PRESTAMO_EMPLEADO,
                                                        'estado'                    => $strEstadoActivo );

                $objCaracteristica   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy( $arrayParametrosCaracteristica );
                $intIdCaracteristica = 0;

                $objCaracteristicaPrestamoCuadrilla   = null;
                $intIdCaracteristicaPerstamoCuadrilla = null;

                if ( $strAccion == 'devolver' || $strAccion== 'recuperar' )
                {
                    $arrayParametrosCaracteristica['descripcionCaracteristica'] = self::CARACTERISTICA_PRESTAMO_CUADRILLA;
                    $objCaracteristicaPrestamoCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                      ->findOneBy( $arrayParametrosCaracteristica );
                }

                if ( $objCaracteristicaPrestamoCuadrilla )
                {
                    $intIdCaracteristicaPerstamoCuadrilla = $objCaracteristicaPrestamoCuadrilla->getId();
                }

                if( $objCaracteristica )
                {
                    $intIdCaracteristica = $objCaracteristica->getId();
                }

                if( $intIdCaracteristica )
                {
                    foreach( $objIntegrantesCuadrilla as $objIntegrante )
                    {
                        $objInfoCaracteristicaIntegrante = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                       ->findOneBy(
                                                                                      array(
                                                                                                'estado'              => $strEstadoActivo,
                                                                                                'personaEmpresaRolId' => $objIntegrante->getId(),
                                                                                                'caracteristicaId'    => $intIdCaracteristica
                                                                                           )  
                                                                                  );

                        if( $objInfoCaracteristicaIntegrante )
                        {
                            $intContadorError++;
                            $strNombreIntegrante = $objIntegrante->getPersonaId() ? $objIntegrante->getPersonaId()->getInformacionPersona() : '';
                            $strMensaje          .= '<b>'.$strNombreIntegrante.'</b>: Es préstamo de un coordinador<br/>';
                        }//( $objInfoCaracteristicaIntegrante )

                        $objInfoCaracteristicaIntegrantePrestamo = null;
                        if ( $strAccion == 'devolver' || $strAccion== 'recuperar')
                        {
                            $arrayParemetrosEmpresaRol = array(
                                                                'estado'              => $strEstadoActivo,
                                                                'personaEmpresaRolId' => $objIntegrante->getId(),
                                                                'caracteristicaId'    => $intIdCaracteristicaPerstamoCuadrilla
                                                            );

                            $objInfoCaracteristicaIntegrantePrestamo = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                                    ->findOneBy($arrayParemetrosEmpresaRol);
                        }

                        if (($strAccion == 'devolver' || $strAccion== 'recuperar') &&
                            !$objInfoCaracteristicaIntegrantePrestamo &&
                            !$objInfoCaracteristicaIntegrante &&
                            $objIntegrante->getEstado() == 'Activo')
                        {
                            $intContadorError++;
                            $strNombreIntegrante = $objIntegrante->getPersonaId() ? $objIntegrante->getPersonaId()->getInformacionPersona() : '';
                            $strMensaje          .= '<b>'.$strNombreIntegrante.'</b>: No es empleado del coordinador a devolver.<br/>';
                        }

                    }//foreach( $arrayMediosTransporte as $intIdMedioTransporte )

                    if( $intContadorError == 0 )
                    {
                        $strMensaje = 'OK';
                    }//( $intContadorError == 0 )
                }//( $intIdCaracteristica ) 
            }//( $objIntegrantesCuadrilla )
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }//try

        $response->setContent( $strMensaje );

        return $response;
    }


    /**
     * Documentación para el método 'verificarVehiculoConNuevoHorarioAction'.
     *
     * Verifica que al modificar el horario de la cuadrilla, 
     * si la cuadrilla tiene asignado un vehículo, se pueda mover también el vehículo asignado, 
     * es decir que no exista otra cuadrilla que utilice el vehículo en el nuevo horario que se quiere actualizar.
     * Se considera el rango de fechas ya asignado con las nuevas horas.
     * 
     * @param id id de Cuadrilla
     * 
     * @return Response 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-12-2015
     */
    public function verificarVehiculoConNuevoHorarioAction($id)
    {
        $objResponse        = new Response();
        $strMsg             = '';
        $objRequest         = $this->get('request');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $boolCrearDetalleHorarioAsignacion  = false;
        $boolCrearDetalleAsignacion         = false;

        $strEstadoActivo    = 'Activo';
        
        
        $objCuadrilla               = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($id);
        $strNombreCuadrilla         = $objCuadrilla ? $objCuadrilla->getNombreCuadrilla() : '';
        $strTurnoHoraInicioCuadrilla= $objCuadrilla ? $objCuadrilla->getTurnoHoraInicio() : '';
        $strTurnoHoraFinCuadrilla   = $objCuadrilla ? $objCuadrilla->getTurnoHoraFin() : '';
        
        $strHoraDesdeNuevoTurno      = $objRequest->get('strHoraDesdeTurno');
        $strHoraHastaNuevoTurno      = $objRequest->get('strHoraHastaTurno');
        
        
        
        if($strTurnoHoraInicioCuadrilla!=$strHoraDesdeNuevoTurno || $strTurnoHoraFinCuadrilla!=$strHoraHastaNuevoTurno)
        {
            
            $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                    'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO, 
                                                                    'detalleValor'  => $id ) 
                                                                );
            
            //Buscar Activo asignado
            if( $objDetalleElemento )
            {
                
                $intIdElementoActual = $objDetalleElemento->getElementoId();

                $objElemento=$emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoActual);
                
                
                $objDetalleElementoFechaInicio = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                    'detalleNombre' => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO, 
                                                                                    'parent'        => $objDetalleElemento  ) );

                //Existe un horario de asignacion
                if($objDetalleElementoFechaInicio)
                {
                    $strFechaInicioCuadrilla=$objDetalleElementoFechaInicio->getDetalleValor();
                    $arrayParametros=array( 
                        'strHoraDesdeNuevoTurno'            => $strHoraDesdeNuevoTurno,
                        'strHoraHastaNuevoTurno'            => $strHoraHastaNuevoTurno,
                        'estadoActivo'                      => $strEstadoActivo,
                        'detalleCuadrilla'                  => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                        'detalleFechaInicio'                => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                        'detalleHoraInicio'                 => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                        'detalleHoraFin'                    => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN,
                        'elementoId'                        => $intIdElementoActual,
                        'idCuadrilla'                       => $id
                    );
                    
                    $objetosDetalleElementosTurnosSolapados = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->getCuadrillasTurnosSolapados($arrayParametros);


                    if($objetosDetalleElementosTurnosSolapados)
                    {


                        $strMsg.="No se puede modificar el horario de la cuadrilla.<br>"
                                ."Actualmente esta cuadrilla tiene asignado el vehículo con placa ".$objElemento->getNombreElemento()."<br>"
                                .'Fechas: Desde '.$strFechaInicioCuadrilla.
                                ' hasta la actualidad <br>'
                                .'Horario: De '. $strTurnoHoraInicioCuadrilla.' a '. $strTurnoHoraFinCuadrilla.'<br>'

                                .'Horario a modificar: De '. $strHoraDesdeNuevoTurno.' a '.$strHoraHastaNuevoTurno.'<br><br>'

                                ."El vehículo ya se encuentra asignado a otra cuadrilla en el horario que desea modificar<br>"
                                .'Nombre de Cuadrilla: '.$objetosDetalleElementosTurnosSolapados[0]['nombreCuadrilla'].'<br>'
                                .'Fechas: Desde '. $objetosDetalleElementosTurnosSolapados[0]['fechaInicioAsignacionVehicular'] 
                                .' hasta la actualidad <br>'
                                .'Horario: De '. $objetosDetalleElementosTurnosSolapados[0]['horaInicioAsignacionVehicular'].' a '
                                . $objetosDetalleElementosTurnosSolapados[0]['horaFinAsignacionVehicular'].'<br>'
                                ."Para editar el horario de la cuadrilla, elimine el vehículo que tiene asignado actualmente";

                    }
                    else
                    {
                        
                        //Asignar Vehículo en nuevo horario y eliminar asignacion provisional si es que hubiese
                        

                        //Eliminar Asignación Actual
                        $arrayParametrosEliminacion = array(
                            "intIdElemento"                     => $intIdElementoActual,
                            "intIdCuadrilla"                    => $id
                        );

                        /*Se eliminan la asignaciones vehicular y chofer profisional si es que hubiese para luego crear una nueva
                         *asignacion vehicular con el nuevo horario
                         *@var $serviceInfoElemento \telconet\tecnicoBundle\Service\InfoElementoService*/
                        $serviceInfoElemento            = $this->get('tecnico.InfoElemento');
                        $strMensajeEliminacionDetalles  = $serviceInfoElemento->eliminarAsignacionVehicularYProvisionalXCuadrilla($arrayParametrosEliminacion);
                        
                        if($strMensajeEliminacionDetalles=='OK')
                        {
                            $boolCrearDetalleAsignacion         = true;
                            $boolCrearDetalleHorarioAsignacion  = true;
                            
                        }
                        else
                        {
                            $strMsg=$strMensajeEliminacionDetalles;
                        }
                    }

                }
                else
                {
                    //Crear horario de asignacion vehicular
                    $boolCrearDetalleHorarioAsignacion=true; 
                }
                
                
                $objSession                 = $objRequest->getSession();
                $strUserSession             = $objSession->get('user');
                $strIpUserSession           = $objRequest->getClientIp();
                $datetimeActual             = new \DateTime('now');
                $strFechaDesdeAsignacion    = $datetimeActual->format('d/m/Y');
                
                if($boolCrearDetalleAsignacion)
                {
                    //Crear los detalles de la nueva asignacion
                    $objInfoDetalleElemento = new InfoDetalleElemento();
                    $objInfoDetalleElemento->setElementoId($intIdElementoActual);
                    $objInfoDetalleElemento->setDetalleNombre(self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO);
                    $objInfoDetalleElemento->setDetalleValor($id);
                    $objInfoDetalleElemento->setDetalleDescripcion(self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO);
                    $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                    $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                    $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                    $objInfoDetalleElemento->setEstado($strEstadoActivo);
                    $emInfraestructura->persist($objInfoDetalleElemento);
                    $emInfraestructura->flush();
                }
                
                $emComercial->getConnection()->beginTransaction();	
                $emInfraestructura->getConnection()->beginTransaction();
                try
                {
                    if($boolCrearDetalleHorarioAsignacion)
                    {
                        if($boolCrearDetalleAsignacion)
                        {
                            $objParent = $objInfoDetalleElemento;
                        }
                        else
                        {
                            $objParent = $objDetalleElemento;
                        }

                        //Fecha Inicio
                        $objInfoDetalleFechaInicioAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleFechaInicioAsignacion->setElementoId($intIdElementoActual);
                        $objInfoDetalleFechaInicioAsignacion->setDetalleNombre(self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO);
                        $objInfoDetalleFechaInicioAsignacion->setDetalleValor($strFechaDesdeAsignacion);
                        $objInfoDetalleFechaInicioAsignacion->setDetalleDescripcion(self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO);
                        $objInfoDetalleFechaInicioAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleFechaInicioAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleFechaInicioAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleFechaInicioAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleFechaInicioAsignacion->setParent($objParent);
                        $emInfraestructura->persist($objInfoDetalleFechaInicioAsignacion);
                        $emInfraestructura->flush();

                        //La fecha fin solo se guarda al eliminar la asignación

                        //Hora Inicio
                        $objInfoDetalleHoraInicioAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleHoraInicioAsignacion->setElementoId($intIdElementoActual);
                        $objInfoDetalleHoraInicioAsignacion->setDetalleNombre(self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO);
                        $objInfoDetalleHoraInicioAsignacion->setDetalleValor($strHoraDesdeNuevoTurno);
                        $objInfoDetalleHoraInicioAsignacion->setDetalleDescripcion(self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO);
                        $objInfoDetalleHoraInicioAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleHoraInicioAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleHoraInicioAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleHoraInicioAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleHoraInicioAsignacion->setParent($objParent);
                        $emInfraestructura->persist($objInfoDetalleHoraInicioAsignacion);
                        $emInfraestructura->flush(); 

                        //Hora Fin
                        $objInfoDetalleHoraFinAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleHoraFinAsignacion->setElementoId($intIdElementoActual);
                        $objInfoDetalleHoraFinAsignacion->setDetalleNombre(self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN);
                        $objInfoDetalleHoraFinAsignacion->setDetalleValor($strHoraHastaNuevoTurno);
                        $objInfoDetalleHoraFinAsignacion->setDetalleDescripcion(self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN);
                        $objInfoDetalleHoraFinAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleHoraFinAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleHoraFinAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleHoraFinAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleHoraFinAsignacion->setParent($objParent);
                        $emInfraestructura->persist($objInfoDetalleHoraFinAsignacion);
                        $emInfraestructura->flush(); 


                        $strMotivoElementoAsignacion  = 'Se realiza asignacion vehicular a cuadrilla';
                        $objMotivoAsignacion   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                            ->findOneByNombreMotivo($strMotivoElementoAsignacion);
                        $intIdMotivoAsignacion = $objMotivoAsignacion ? $objMotivoAsignacion->getId() : 0;

                        $strMensajeObservacionAsignacionCuadrilla="Se realiza asignaci&oacute;n vehicular a cuadrilla<br>";



                        //Se mueve el vehículo al nuevo horario

                        //Historial Cuadrilla 
                        $strMensajeObservacionNuevo =   "<b>Datos Nuevos</b><br>".
                                                        "Veh&iacute;culo Asignado: ".$objElemento->getNombreElemento()." ".
                                                        "Fecha Inicio: ".$strFechaDesdeAsignacion." ".
                                                        "Hora Inicio: ".$strHoraDesdeNuevoTurno." ".
                                                        "Hora Fin: ".$strHoraHastaNuevoTurno;


                        $strMensajeObservacion=$strMensajeObservacionAsignacionCuadrilla.$strMensajeObservacionNuevo;

                        $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                        $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
                        $objCuadrillaHistorial->setEstado($objCuadrilla->getEstado());
                        $objCuadrillaHistorial->setFeCreacion($datetimeActual);
                        $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                        $objCuadrillaHistorial->setObservacion($strMensajeObservacion);
                        $objCuadrillaHistorial->setMotivoId($intIdMotivoAsignacion);
                        $emComercial->persist($objCuadrillaHistorial);
                        $emComercial->flush();


                        //Historial Elemento
                        $strMensajeObservacionAsignacionElemento  = 'Se asigna veh&iacute;culo';
                        $strMensajeObservacionAsignacionElemento .= ' a la cuadrilla '.$strNombreCuadrilla."<br/>";
                        $strMensajeObservacionAsignacionElemento .= "Fecha Inicio: ".$strFechaDesdeAsignacion." ".
                                                                    "Hora Inicio: ".$strHoraDesdeNuevoTurno." ".
                                                                    "Hora Fin: ".$strHoraHastaNuevoTurno;

                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objElemento);
                        $objInfoHistorialElemento->setObservacion($strMensajeObservacionAsignacionElemento);
                        $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                        $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                        $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                        $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                        $emInfraestructura->persist($objInfoHistorialElemento);
                        $emInfraestructura->flush();
                        
                        $emComercial->getConnection()->commit();
                        $emComercial->getConnection()->close();

                        $emInfraestructura->getConnection()->commit();
                        $emInfraestructura->getConnection()->close();

                        $strMsg ='OK';
                    }
                }
                catch (\Exception $e)
                {
                    error_log($e->getMessage());
                    $strMsg = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';
                    $emComercial->getConnection()->rollback();
                    $emComercial->getConnection()->close();

                    $emInfraestructura->getConnection()->rollback();
                    $emInfraestructura->getConnection()->close();

                } 

            }
            //No hay vehículo asociado
            else
            {
                $strMsg='OK';
            }
        }
        //No hay cambio de horario
        else
        {
            $strMsg='OK';
        }
        
        
        $objResponse->setContent( $strMsg );
        return $objResponse;
        
    }
    
    /**
     * @Secure(roles="ROLE_170-3137")
     * 
     * Documentación para el método 'asignarVehiculoAction'.
     *
     * Método que asigna el vehículo a la cuadrilla,Se verifica que el vehiculo que se quiere asignar no se encuentre ocupado 
     * en el turno de otra cuadrilla
     * 
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se realizan cambios para considerar los horarios de las asignaciones predefinidas de chofer
     * 
     */
    public function asignarVehiculoAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $boolError          = false;

        $strMensaje         = 'No se encontró cuadrilla activa';
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';

        $intIdCuadrilla             = $objRequest->get('idCuadrilla') ? trim($objRequest->get('idCuadrilla')) : '';
        
        $intIdDetalleSolicitud      = $objRequest->get('idDetalleSolicitud') ? trim($objRequest->get('idDetalleSolicitud')) : '';
        
        $strFechaDesdeAsignacion    = $datetimeActual->format('d/m/Y');

        $strHoraDesdeAsignacion     = $objRequest->get('strHoraDesdeAsignacion') ? trim($objRequest->get('strHoraDesdeAsignacion')) : '';
        $strHoraHastaAsignacion     = $objRequest->get('strHoraHastaAsignacion') ? trim($objRequest->get('strHoraHastaAsignacion')) : '';

        $strMensajeObservacionPrincipal="Se realiza asignaci&oacute;n vehicular a cuadrilla<br>";
        $strMensajeObservacionNuevo = "";
        $strMensajeObservacionActual= "";
        $strMotivoElemento  = 'Se realiza asignacion vehicular a cuadrilla';
        $strMensajeObservacionElemento  = 'Se asigna veh&iacute;culo';
        $objAsociado        = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneBy( array('id' => $intIdCuadrilla) );
        $strDetalleAsociado = self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO;

        $objMotivo   = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoElemento);
        $intIdMotivo = $objMotivo ? $objMotivo->getId() : 0;

        
        if(!$objAsociado)
        {
            $boolError = true;
        }
        else
        {
            if( $objAsociado->getEstado() == 'Eliminado' )
            {
                $boolError = true;
            }
        }

        if(!$boolError)
        {
            $emComercial->getConnection()->beginTransaction();
            $emInfraestructura->getConnection()->beginTransaction();

            try
            {
                
                $objInfoDetalleSolicitud        = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
                if($objInfoDetalleSolicitud)
                {
                    $intIdElementoSeleccionado  = $objInfoDetalleSolicitud->getElementoId();
                
                    //$objElemento es el nuevo vehículo que se quiere asignar a la cuadrilla
                    $objElemento                = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->findOneBy( array('id' => $intIdElementoSeleccionado, 'estado' => $strEstadoActivo) );

                    $strEstadoActualAsociado    = $objAsociado->getEstado();

                    $strElementoActual          = 'Sin Asignaci&oacute;n';

                    //Busca si ya hay algún vehículo asignado
                    $objDetalleElementoActual   = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                        'detalleNombre' => $strDetalleAsociado, 
                                                                                        'detalleValor'  => $intIdCuadrilla ) 
                                                                                );

                    //Si existe el nuevo elemento que se quiere agregar a la cuadrilla
                    if($objElemento)
                    {
                        $strElementoNuevo           = $objElemento ? $objElemento->getNombreElemento() : "Sin Asignaci&oacute;n";
                        $strMensajeObservacionNuevo = "<b>Datos Nuevos</b><br>".
                                                        "Veh&iacute;culo Asignado: ".$strElementoNuevo." ".
                                                        "Fecha Inicio: ".$strFechaDesdeAsignacion." ".
                                                        "Hora Inicio: ".$strHoraDesdeAsignacion." ".
                                                        "Hora Fin: ".$strHoraHastaAsignacion;


                        $strMensajeObservacionElemento .= ' a la cuadrilla '.$objAsociado->getNombreCuadrilla()."<br/>";

                        $strDetalleFechaInicioAV    = self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO;
                        $strDetalleHoraInicioAv     = self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO;
                        $strDetalleHoraFinAv        = self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN;
                        $strDetalleIdSolicitud      = self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED;


                        if( $objDetalleElementoActual )
                        {
                            $intIdElementoActual = $objDetalleElementoActual->getElementoId();
                            $objElementoActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                     ->findOneBy( array('id' => $intIdElementoActual, 'estado' => $strEstadoActivo) );

                            if( $objElementoActual )
                            {
                                $strElementoActual = $objElementoActual->getNombreElemento();
                            }


                            $objDetalleElementoActual->setEstado($strEstadoEliminado);
                            $emInfraestructura->persist($objDetalleElementoActual);
                            $emInfraestructura->flush();

                            /*Crear Detalle Fecha Fin de Asignacion Vehicular
                             * La Fecha Desde de la nueva Asignación sería la Fecha Fin de la anterior Asignación
                             */
                            $objInfoDetalleFechaFinAV = new InfoDetalleElemento();
                            $objInfoDetalleFechaFinAV->setElementoId($intIdElementoActual);
                            $objInfoDetalleFechaFinAV->setDetalleNombre(self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN);
                            $objInfoDetalleFechaFinAV->setDetalleValor($strFechaDesdeAsignacion);
                            $objInfoDetalleFechaFinAV->setDetalleDescripcion(self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN);
                            $objInfoDetalleFechaFinAV->setFeCreacion($datetimeActual);
                            $objInfoDetalleFechaFinAV->setUsrCreacion($strUserSession);
                            $objInfoDetalleFechaFinAV->setIpCreacion($strIpUserSession);
                            $objInfoDetalleFechaFinAV->setEstado($strEstadoEliminado);
                            $objInfoDetalleFechaFinAV->setParent($objDetalleElementoActual);
                            $emInfraestructura->persist($objInfoDetalleFechaFinAV);
                            $emInfraestructura->flush();


                            $strMensajeObservacionActual  = "<b>Datos Anteriores</b>".
                                                            "Veh&iacute;culo Asignado: ".$strElementoActual." ";

                            $arrayDetallesAEliminarAsignacionVehicular=
                                array
                                    (
                                        'Fecha Inicio'   => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                        'Hora Inicio'    => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                        'Hora Fin'       => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN,
                                        'ID Solicitud'   => self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED
                                    );


                            foreach($arrayDetallesAEliminarAsignacionVehicular as $detalleNombreAlias=>$detalleNombre)
                            {
                                $objDetalleAEliminar = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                                'detalleNombre' => $detalleNombre, 
                                                                                                'parent'        => $objDetalleElementoActual ) 
                                                                                       );

                                if($objDetalleAEliminar)
                                {
                                    $strMensajeObservacionActual.=$detalleNombreAlias.": ".$objDetalleAEliminar->getDetalleValor()." ";
                                    $objDetalleAEliminar->setEstado($strEstadoEliminado);
                                    $emInfraestructura->persist($objDetalleAEliminar);
                                    $emInfraestructura->flush();
                                }

                            }

                            $strMensajeObservacionActual.="Fecha Fin: ".$strFechaDesdeAsignacion." ";


                            //Busca si hay un chofer provisional asociado a esta cuadrilla
                            $objDetalleChoferProvisional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                'detalleNombre' => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER, 
                                                                                'parent'        => $objDetalleElementoActual ) 
                                                                       );

                            //Si se elimina una asignación vehicular, se elimina la asignación provisional si es que tuviera alguna
                            if($objDetalleChoferProvisional)
                            {
                                $objDetalleChoferProvisional->setEstado($strEstadoEliminado);
                                $emInfraestructura->persist($objDetalleChoferProvisional);
                                $emInfraestructura->flush();

                                $arrayDetallesAEliminarAsignacionProvisional=array
                                                                                    (
                                                                                        'Fecha Inicio Provisional'   =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO,
                                                                                        'Fecha Fin Provisional'    =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN,
                                                                                        'Hora Inicio Provisional'   =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO,
                                                                                        'Hora Fin Provisional'    =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN,   
                                                                                        'Motivo Provisional'    =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO
                                                                                    );


                                foreach($arrayDetallesAEliminarAsignacionProvisional as $detalleNombreAliasProvisional=>$detalleNombreProvisional)
                                {
                                    $objDetalleAEliminar = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                                    'detalleNombre' => $detalleNombreProvisional, 
                                                                                                    'parent'        => $objDetalleChoferProvisional ) 
                                                                                           );
                                    if($objDetalleAEliminar)
                                    {
                                        $objDetalleAEliminar->setEstado($strEstadoEliminado);
                                        $emInfraestructura->persist($objDetalleAEliminar);
                                        $emInfraestructura->flush();
                                    }
                                }
                            }

                        }

                        $objInfoDetalleElemento = new InfoDetalleElemento();
                        $objInfoDetalleElemento->setElementoId($intIdElementoSeleccionado);
                        $objInfoDetalleElemento->setDetalleNombre($strDetalleAsociado);
                        $objInfoDetalleElemento->setDetalleValor($intIdCuadrilla);
                        $objInfoDetalleElemento->setDetalleDescripcion($strDetalleAsociado);
                        $objInfoDetalleElemento->setFeCreacion($datetimeActual);
                        $objInfoDetalleElemento->setUsrCreacion($strUserSession);
                        $objInfoDetalleElemento->setIpCreacion($strIpUserSession);
                        $objInfoDetalleElemento->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objInfoDetalleElemento);
                        $emInfraestructura->flush();

                        //Fecha Inicio
                        $objInfoDetalleFechaInicioAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleFechaInicioAsignacion->setElementoId($intIdElementoSeleccionado);
                        $objInfoDetalleFechaInicioAsignacion->setDetalleNombre($strDetalleFechaInicioAV);
                        $objInfoDetalleFechaInicioAsignacion->setDetalleValor($strFechaDesdeAsignacion);
                        $objInfoDetalleFechaInicioAsignacion->setDetalleDescripcion($strDetalleFechaInicioAV);
                        $objInfoDetalleFechaInicioAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleFechaInicioAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleFechaInicioAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleFechaInicioAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleFechaInicioAsignacion->setParent($objInfoDetalleElemento);
                        $emInfraestructura->persist($objInfoDetalleFechaInicioAsignacion);
                        $emInfraestructura->flush();

                        //La fecha fin solo se guarda al eliminar la asignación

                        //Hora Inicio
                        $objInfoDetalleHoraInicioAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleHoraInicioAsignacion->setElementoId($intIdElementoSeleccionado);
                        $objInfoDetalleHoraInicioAsignacion->setDetalleNombre($strDetalleHoraInicioAv);
                        $objInfoDetalleHoraInicioAsignacion->setDetalleValor($strHoraDesdeAsignacion);
                        $objInfoDetalleHoraInicioAsignacion->setDetalleDescripcion($strDetalleHoraInicioAv);
                        $objInfoDetalleHoraInicioAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleHoraInicioAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleHoraInicioAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleHoraInicioAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleHoraInicioAsignacion->setParent($objInfoDetalleElemento);
                        $emInfraestructura->persist($objInfoDetalleHoraInicioAsignacion);
                        $emInfraestructura->flush(); 

                        //Hora Fin
                        $objInfoDetalleHoraFinAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleHoraFinAsignacion->setElementoId($intIdElementoSeleccionado);
                        $objInfoDetalleHoraFinAsignacion->setDetalleNombre($strDetalleHoraFinAv);
                        $objInfoDetalleHoraFinAsignacion->setDetalleValor($strHoraHastaAsignacion);
                        $objInfoDetalleHoraFinAsignacion->setDetalleDescripcion($strDetalleHoraFinAv);
                        $objInfoDetalleHoraFinAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleHoraFinAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleHoraFinAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleHoraFinAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleHoraFinAsignacion->setParent($objInfoDetalleElemento);
                        $emInfraestructura->persist($objInfoDetalleHoraFinAsignacion);
                        $emInfraestructura->flush(); 

                        /*Crear Detalle con el id del detalle solicitud de la asignacion*/
                        $objInfoDetalleSolicitudAsignacion = new InfoDetalleElemento();
                        $objInfoDetalleSolicitudAsignacion->setElementoId($intIdElementoSeleccionado);
                        $objInfoDetalleSolicitudAsignacion->setDetalleNombre($strDetalleIdSolicitud);
                        $objInfoDetalleSolicitudAsignacion->setDetalleValor($intIdDetalleSolicitud);
                        $objInfoDetalleSolicitudAsignacion->setDetalleDescripcion($strDetalleIdSolicitud);
                        $objInfoDetalleSolicitudAsignacion->setFeCreacion($datetimeActual);
                        $objInfoDetalleSolicitudAsignacion->setUsrCreacion($strUserSession);
                        $objInfoDetalleSolicitudAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoDetalleSolicitudAsignacion->setEstado($strEstadoActivo);
                        $objInfoDetalleSolicitudAsignacion->setParent($objInfoDetalleElemento);
                        $emInfraestructura->persist($objInfoDetalleSolicitudAsignacion);
                        $emInfraestructura->flush();

                        //Historial Cuadrilla
                        $strMensajeObservacion=$strMensajeObservacionPrincipal.$strMensajeObservacionActual.$strMensajeObservacionNuevo;
                        $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                        $objCuadrillaHistorial->setCuadrillaId($objAsociado);
                        $objCuadrillaHistorial->setEstado($strEstadoActualAsociado);
                        $objCuadrillaHistorial->setFeCreacion($datetimeActual);
                        $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                        $objCuadrillaHistorial->setObservacion($strMensajeObservacion);
                        $objCuadrillaHistorial->setMotivoId($intIdMotivo);
                        $emComercial->persist($objCuadrillaHistorial);
                        $emComercial->flush();

                        //Historial Elemento
                        $strMensajeObservacionElemento .=   "Fecha Inicio: ".$strFechaDesdeAsignacion." ".
                                                            "Hora Inicio: ".$strHoraDesdeAsignacion." ".
                                                            "Hora Fin: ".$strHoraHastaAsignacion;

                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objElemento);
                        $objInfoHistorialElemento->setObservacion($strMensajeObservacionElemento);
                        $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                        $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                        $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                        $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                        $emInfraestructura->persist($objInfoHistorialElemento);
                        $emInfraestructura->flush();

                        $emComercial->getConnection()->commit();
                        $emInfraestructura->getConnection()->commit();
                    }
                }
                

                $emComercial->getConnection()->close();
                $emInfraestructura->getConnection()->close();

                $strMensaje = 'OK';
            }
            catch(\Exception $e)
            {
                error_log($e->getMessage());

                $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();

                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }//try
        }//(!$boolError)

        $objResponse->setContent( $strMensaje );

        return $objResponse;
    }




    /**
     * @Secure(roles="ROLE_170-3597")
     * 
     * Documentación para el método 'eliminarAsignacionVehicularAction'.
     *
     * Método que elimina la asignación actual de un vehículo hacia una cuadrilla
     * 
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     */
    public function eliminarAsignacionVehicularAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->get('request');

        $intIdElemento      = $objRequest->get('idElemento') ? $objRequest->get('idElemento') : '';
        $intIdCuadrilla     = $objRequest->get('idCuadrilla') ? $objRequest->get('idCuadrilla') : '';
        
        
        $arrayParametros    = array(
            "intIdElemento"     => $intIdElemento,
            "intIdCuadrilla"    => $intIdCuadrilla
        );
        
        
        /* @var $serviceInfoElemento \telconet\tecnicoBundle\Service\InfoElementoService */
        $serviceInfoElemento    = $this->get('tecnico.InfoElemento');
        $strMensaje             = $serviceInfoElemento->eliminarAsignacionVehicularYProvisionalXCuadrilla($arrayParametros);
        
        $objResponse->setContent( $strMensaje );
        return $objResponse;
    }

    
    
    
    /**
     * Documentación para el método 'getVehiculosDisponiblesAction'.
     *
     * Vehículos que no están ocupados por otra cuadrilla actualmente o por un chofer provisional en algún horario establecido.
     *
     * @return Response 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getVehiculosDisponiblesAction()
    {
        $em                 = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objResponse        = new Response();
        $objResponse->headers->set('Content-type', 'text/json'); 
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;

        $intModeloElemento      = $objRequest->get('strModeloElemento') ? $objRequest->get('strModeloElemento') : '';
        $strTipoElemento        = $objRequest->get('strTipoElemento') ? trim($objRequest->get('strTipoElemento')) : '';
        
        $intIdZonaCuadrilla         = $objRequest->get('idZonaCuadrilla') ? trim($objRequest->get('idZonaCuadrilla')) : '';
        $intIdTareaCuadrilla        = $objRequest->get('idTareaCuadrilla') ? trim($objRequest->get('idTareaCuadrilla')) : '';
        $intIdDepartamentoCuadrilla = $objRequest->get('idDepartamentoCuadrilla') ? trim($objRequest->get('idDepartamentoCuadrilla')) : '';
        

        $datetimeActual             = new \DateTime('now');
        $strFechaDesdeAsignacion    = $datetimeActual->format('d/m/Y');
        $strFechaHastaAsignacion    = $datetimeActual->format('t/m/Y');

        
        $strHoraDesdeAsignacion     = $objRequest->get('strHoraDesdeAsignacion') ? trim($objRequest->get('strHoraDesdeAsignacion')) : '';
        $strHoraHastaAsignacion     = $objRequest->get('strHoraHastaAsignacion') ? trim($objRequest->get('strHoraHastaAsignacion')) : '';

        $strNombreElemento          = $objRequest->query->get('query') ? trim($objRequest->query->get('query')) : '';
        
        
        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        $objCaracteristicaZonaPredefinida = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);
        
        $objCaracteristicaTareaPredefinida = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);
        
        
        $objCaracteristicaDepartamentoPredefinido = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
        $strRegion      = '';
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $em->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }
        $arrayParametros = array(
                                    'strEstadoActivo'           => 'Activo',
                                    'intEmpresa'                => $intIdEmpresaSession,
                                    'intIdZonaCuadrilla'        => $intIdZonaCuadrilla,
                                    'intIdTareaCuadrilla'       => $intIdTareaCuadrilla,
                                    'intIdDepartamentoCuadrilla'=> $intIdDepartamentoCuadrilla,
                                    'intIdTipoSolicitud'        => $objTipoSolicitud->getId(),
                                    'intIdCaracteristicaTareaPredefinida'          => $objCaracteristicaTareaPredefinida->getId(),
                                    'intIdCaracteristicaZonaPredefinida'           => $objCaracteristicaZonaPredefinida->getId(),
                                    'intIdCaracteristicaDepartamentoPredefinido'   => $objCaracteristicaDepartamentoPredefinido->getId(),
                                    'strDetalleCuadrilla'       => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                    'strDetalleFechaInicioAV'   => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                    'arrayDetallesHorasAV'  => array(
                                        'strDetalleHoraInicioAV'       => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                        'strDetalleHoraFinAV'          => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN,
                                        
                                    ),
                                    'arrayDetallesFechasAP'     => array(
                                        'strDetalleFechaInicioAPChofer'    => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO,
                                        'strDetalleFechaFinAPChofer'       => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN,
                                    ),
                                    'arrayDetallesHorasAP'     => array(
                                        'strDetalleHoraInicioAPChofer'      => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO,
                                        'strDetalleHoraFinAPChofer'         => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN
                                    ),
                                    'region'                    => $strRegion,
                                    'strTipoElemento'           => $strTipoElemento,
                                    'intModeloElemento'         => $intModeloElemento,
                                    'strFechaDesdeAsignacion'   => $strFechaDesdeAsignacion,
                                    'strFechaHastaAsignacion'   => $strFechaHastaAsignacion,
                                    'strHoraDesdeAsignacion'    => $strHoraDesdeAsignacion,
                                    'strHoraHastaAsignacion'    => $strHoraHastaAsignacion,
                                    'nombreElemento'            => $strNombreElemento,
                                    
                                );
        
        $objJson    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getJSONVehiculosDisponibles( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse;

    }
    
    
    /**
     * Documentación para el método 'getChoferAsignacionPredefinidaAction'.
     *
     * Obtiene el chofer de la asignación vehicular predefinida.
     *
     * @return Response 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  10-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-08-2016 Se obtiene el chofer predefinido de acuerdo al horario de la asignación predefinida
     */
    public function getChoferAsignacionVehicularPredefinidaAction()
    {
        $em                 = $this->getDoctrine()->getManager();
        $objResponse        = new Response();
        $objResponse->headers->set('Content-type', 'text/json'); 
        $objRequest             = $this->get('request');

        $intIdDetalleSolicitud      = $objRequest->get('idDetalleSolicitud') ? $objRequest->get('idDetalleSolicitud') : '';
        $intIdZonaCuadrilla         = $objRequest->get('idZonaCuadrilla') ? trim($objRequest->get('idZonaCuadrilla')) : '';
        $intIdTareaCuadrilla        = $objRequest->get('idTareaCuadrilla') ? trim($objRequest->get('idTareaCuadrilla')) : '';
        $intIdDepartamentoCuadrilla = $objRequest->get('idDepartamentoCuadrilla') ? trim($objRequest->get('idDepartamentoCuadrilla')) : '';

        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

        $objCaracteristicaZonaPredefinida = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

        $objCaracteristicaTareaPredefinida = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);


        $objCaracteristicaDepartamentoPredefinido = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);


        $arrayParametros = array(
                                    'intIdCaracteristicaTareaPredefinida'          => $objCaracteristicaTareaPredefinida->getId(),
                                    'intIdCaracteristicaZonaPredefinida'           => $objCaracteristicaZonaPredefinida->getId(),
                                    'intIdCaracteristicaDepartamentoPredefinido'   => $objCaracteristicaDepartamentoPredefinido->getId(),

                                    'intIdZonaCuadrilla'        => $intIdZonaCuadrilla,
                                    'intIdTareaCuadrilla'       => $intIdTareaCuadrilla,
                                    'intIdDepartamentoCuadrilla'=> $intIdDepartamentoCuadrilla,

                                    'intIdTipoSolicitud'        => $objTipoSolicitud->getId(),
                                    'intIdDetalleSolicitud'     => $intIdDetalleSolicitud,

                                    'strEstadoActivo'           => 'Activo'
                                );

        $strMensaje         = 'ERROR';
        $arrayResultado     = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                 ->getResultadoChoferAsignacionVehicularPredefinida( $arrayParametros );


        $resultado          = $arrayResultado['resultado'];

        if($resultado)
        {
            foreach($resultado as $data)
            {
                $strMensaje ='OK-';
                $strMensaje.= $data['chofer'];
            }

        }
        $objResponse->setContent( $strMensaje );
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_170-4957")
     * 
     * Documentación para el método 'liberarCuadrillaAction'.
     *
     * Función que inactiva una cuadrilla ya que se encuentra libre por algún motivo.
     *
     * @return JsonResponse $objResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-01-2017
     */ 
    public function liberarCuadrillaAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emComercial        = $this->getDoctrine()->getManager();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objDatetimeActual  = new \DateTime('now');
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMensaje         = "";
        $serviceUtil        = $this->get('schema.Util');
        
        $intIdCuadrillaALiberar         = $objRequest->get('intIdCuadrilla') ? $objRequest->get('intIdCuadrilla') : 0;
        $strMotivoCuadrillaLibre        = 'La cuadrilla pasa a estar libre';
        $objMotivoCuadrillaLibre        = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoCuadrillaLibre);
        $intIdMotivoCuadrillaLibre      = $objMotivoCuadrillaLibre ? $objMotivoCuadrillaLibre->getId() : 0;
        
        
        if($intIdCuadrillaALiberar && $intIdMotivoCuadrillaLibre)
        {
            $objCuadrillaALiberar       = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrillaALiberar);
            
            if(is_object($objCuadrillaALiberar))
            {
                $emComercial->beginTransaction();
                try
                {
                    
                    $objCuadrillaALiberar->setEstaLibre("SI");
                    $objCuadrillaALiberar->setUsrModificacion($strUserSession);
                    $objCuadrillaALiberar->setFeUltMod($objDatetimeActual);
                    $emComercial->persist($objCuadrillaALiberar);
                    
                    $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                    $objCuadrillaHistorial->setCuadrillaId($objCuadrillaALiberar);
                    $objCuadrillaHistorial->setEstado($objCuadrillaALiberar->getEstado());
                    $objCuadrillaHistorial->setFeCreacion($objDatetimeActual);
                    $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                    $objCuadrillaHistorial->setObservacion($strMotivoCuadrillaLibre);
                    $objCuadrillaHistorial->setMotivoId($intIdMotivoCuadrillaLibre);
                    $emComercial->persist($objCuadrillaHistorial);
                    
                    $emComercial->flush();
                    $emComercial->commit();
                    $strMensaje .= 'OK';
                }
                catch (\Exception $e)
                {
                    $strMensaje .= 'Ha ocurrido un problema al liberar a la cuadrilla. Por favor notificar a Sistemas!';
                    if ($emComercial->getConnection()->isTransactionActive())
                    {
                        $emComercial->getConnection()->rollback();
                    }
                    $emComercial->getConnection()->close();

                    $serviceUtil->insertError(
                                                'Telcos+', 
                                                'AdmiCuadrillaController->liberarCuadrillaAction', 
                                                $e->getMessage(), 
                                                $strUserSession, 
                                                $strIpUserSession);
                }

            }
            else
            {
                $strMensaje .= 'No se ha obtenido la información de la cuadrilla. '
                             . 'Por favor notificar a Sistemas!';
            }
        }
        else
        {
            $strMensaje .= 'No se ha obtenido la información de la cuadrilla o el motivo para liberar la cuadrilla de manera correcta. '
                         . 'Por favor notificar a Sistemas!';
        }
        
        $objResponse->setContent( $strMensaje );
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_170-4977")
     * 
     * Documentación para el método 'reactivarCuadrillaLibreAction'.
     *
     * Función que reactiva una cuadrilla que se encontraba libre, es decir vuelve a estar operativa
     *
     * @return JsonResponse $objResponse
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-01-2017
     */ 
    public function reactivarCuadrillaLibreAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emComercial        = $this->getDoctrine()->getManager();
        $objDatetimeActual  = new \DateTime('now');
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMensaje         = "";
        $serviceUtil        = $this->get('schema.Util');
        
        
        $intIdCuadrillaAReactivar   = $objRequest->get('intIdCuadrilla') ? $objRequest->get('intIdCuadrilla') : 0;
        
        $strMotivoCuadrillaAReactivar     = 'La cuadrilla vuelve a estar operativa';
        $objMotivoCuadrillaAReactivar     = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoCuadrillaAReactivar);
        $intIdMotivoCuadrillaAReactivar   = $objMotivoCuadrillaAReactivar ? $objMotivoCuadrillaAReactivar->getId() : 0;

        
        if($intIdCuadrillaAReactivar && $intIdMotivoCuadrillaAReactivar)
        {
            $objCuadrillaAReactivar = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrillaAReactivar);
            
            if(is_object($objCuadrillaAReactivar))
            {
                $emComercial->beginTransaction();
                try
                {
                    $objCuadrillaAReactivar->setEstaLibre("NO");
                    $objCuadrillaAReactivar->setUsrModificacion($strUserSession);
                    $objCuadrillaAReactivar->setFeUltMod($objDatetimeActual);
                    $emComercial->persist($objCuadrillaAReactivar);
                    
                    $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                    $objCuadrillaHistorial->setCuadrillaId($objCuadrillaAReactivar);
                    $objCuadrillaHistorial->setEstado($objCuadrillaAReactivar->getEstado());
                    $objCuadrillaHistorial->setFeCreacion($objDatetimeActual);
                    $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                    $objCuadrillaHistorial->setObservacion($strMotivoCuadrillaAReactivar);
                    $objCuadrillaHistorial->setMotivoId($intIdMotivoCuadrillaAReactivar);
                    $emComercial->persist($objCuadrillaHistorial);
                    
                    $emComercial->flush();
                    $emComercial->commit();
                    $strMensaje .= 'OK';
                }
                catch (\Exception $e)
                {
                    $strMensaje .= 'Ha ocurrido un problema al reactivar la cuadrilla. Por favor notificar a Sistemas!';
                    if ($emComercial->getConnection()->isTransactionActive())
                    {
                        $emComercial->getConnection()->rollback();
                    }   
                    $emComercial->getConnection()->close();
                    $serviceUtil->insertError(
                                                'Telcos+', 
                                                'AdmiCuadrillaController->reactivarCuadrillaLibreAction', 
                                                $e->getMessage(), 
                                                $strUserSession, 
                                                $strIpUserSession);
                }

            }
            else
            {
                $strMensaje .= 'No se ha obtenido la información de la cuadrilla. Por favor notificar a Sistemas!';
            }
        }
        else
        {
            $strMensaje .= 'No se ha obtenido la información de la cuadrilla o el motivo para reactivar la cuadrilla de manera correcta. '
                         . 'Por favor notificar a Sistemas!';
        }
        
        $objResponse->setContent( $strMensaje );
        return $objResponse;
    }

    /**
     * Metodo encargado de retornar los tipos de horarios
     * 
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * @return JsonResponse
     */
    public function ajaxGetTipoHorarioAction()  	
    { 	
        $emSoporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $objResponse         = new JsonResponse();
        $arrayTipoHorarios   = array();
        $arrayTipoHorario    = $emSoporte->getRepository("schemaBundle:AdmiTipoHorarios")->getTiposHorarios();        
        for ($intIndice=0; $intIndice < count($arrayTipoHorario); $intIndice++)
        {
            array_push($arrayTipoHorarios, $arrayTipoHorario[$intIndice]);

        }
        $objResponse->setData($arrayTipoHorarios);
        error_log( $objResponse);
        return $objResponse;
    }

    /**
     * Metodo encargado de retornar los Dias de la semana
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * @return JsonResponse
     */
    public function ajaxGetDiasSemanaAction()  	
    { 	
        $objResponse       = new JsonResponse();
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');

        $arrayDiasSemana   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getResultadoDetallesParametro('DIAS_SEMANA_HE','','');
        $intTotal          = count($arrayDiasSemana['registros']);       
        
        if( $arrayDiasSemana )
        {
            foreach($arrayDiasSemana['registros'] as $arrayDiaSemana)
            {
                $arrayDiasSemanaDet[]   = array ('idDia' => $arrayDiaSemana['valor1'], 
                                                 'nombreDia' => $arrayDiaSemana['valor2']);
            }
        }

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayDiasSemanaDet);
        $objResponse->setData($arrayRespuesta);
    
        return $objResponse;
    }

    /**
     * Metodo encargado de retornar los Dias de la semana que labora la cuadrilla y el empleado que se agrega
     * a la cuadrilla de tipo horario (canje, temporal, linea base)
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * @return JsonResponse
     */
    public function ajaxGetDiasSemanaCuadrillaAction()  	
    { 	
        $objRequest          = $this->getRequest();
        $objResponse         = new JsonResponse();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');

        $intIdCuadrilla      = $objRequest->get('intIdCuadrilla') ? $objRequest->get('intIdCuadrilla') : 0;
        $objCuadrilla        = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                    ->findOneById($intIdCuadrilla);
        $arrayDiasSemana     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getResultadoDetallesParametro('DIAS_SEMANA_HE','',''); 
        $arrayTmpParamInfoDiaSemanaCuadri = array(  'estado'        => 'Activo', 
                                                  'cuadrillaId'   => $objCuadrilla->getId());
        $objInfoDiaSemanaCuadrilla    = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                    ->findBy($arrayTmpParamInfoDiaSemanaCuadri);
        $arrayDiasSemana1       = array();
        $intTotal            = count($objInfoDiaSemanaCuadrilla);
        
        if( $arrayDiasSemana && $objInfoDiaSemanaCuadrilla)
        {
            for ($intIndice=0; $intIndice < count($objInfoDiaSemanaCuadrilla); $intIndice++)
            {   
                $intNumeroDiaId      = $objInfoDiaSemanaCuadrilla[$intIndice]->getNumeroDiaId();
                $arrayDiasSemana1[]  = $objInfoDiaSemanaCuadrilla[$intIndice]->getNumeroDiaId();
                foreach($arrayDiasSemana['registros'] as $arrayDiaSemana)
                {
                    if ($intNumeroDiaId == intval($arrayDiaSemana['valor1']))
                    {
                        $arrayDiasSemanaDet[]   = array ('idDia' => $arrayDiaSemana['valor1'], 
                                                         'nombreDia' => $arrayDiaSemana['valor2']);
                    }
                }
            }
        }
        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayDiasSemanaDet);

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
     * Metodo encargado de retornar los Dias de la semana en los que labora la cuadrilla
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * @return JsonResponse
     */
    public function ajaxPlanificacionCuadrillaAction()  	
    { 	
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objResponse         = new JsonResponse();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSoporte           = $this->getDoctrine()->getManager("telconet_soporte");

        $strAccion             = $objRequest->get('accion') ? trim($objRequest->get('accion')) : '';
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strEstadoActivo       = 'Activo';
        $strEstadoPrestado     = 'Prestado';

        $strFechaInicio     = $objRequest->get('strFechaInicio') ? $objRequest->get('strFechaInicio') : '';
        $strFechaFin        = $objRequest->get('strFechaFin') ? $objRequest->get('strFechaFin') : '';
        $strHoraInicio      = $objRequest->get('strHoraInicio') ? $objRequest->get('strHoraInicio') : '';
        $strHoraFin         = $objRequest->get('strHoraFin') ? $objRequest->get('strHoraFin') : '';
        $arrayTipoHorario1    = $objRequest->get('cmbTipoHorario1') ? $objRequest->get('cmbTipoHorario1') : '';
        $arrayDiasSeleccionados = $objRequest->get('comboDiaSemana1')? trim($objRequest->get('comboDiaSemana1')) : '';

        $strCuadrillasSeleccionadas = $objRequest->get('cuadrillas') ? trim($objRequest->get('cuadrillas')) : '';
        $arrayCuadrillas            = explode("|", $strCuadrillasSeleccionadas);
        $intIdDepartamento          = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $intIdPersonEmpresaRol      = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strEstadoBusqueda          = '';
        $arrayDiasSemana1              = '';
        $arrayDatosTrama = [];
        $intSecuencialId = 0;
        $intIdCuadrilla             = $objRequest->get('intIdCuadrilla') ? $objRequest->get('intIdCuadrilla'): '';


        if ($arrayDiasSeleccionados != '' )
        {
            $arrayDiasSemana = json_decode($objRequest->get('comboDiaSemana1'), true);
            $arrayDiasSemana1   = $arrayDiasSemana['dias'];
        }

        if( $arrayCuadrillas && ($arrayCuadrillas > 0 && $arrayCuadrillas [0] != ""))
        {
            $emComercial->getConnection()->beginTransaction();	
            
            try
            {
                foreach($arrayCuadrillas as $intIdCuadrilla)
                {
                    if( $strAccion == 'prestar')
                    {
                        $strEstadoBusqueda      = $strEstadoActivo;

                    }
                    elseif( $strAccion == 'devolver' || $strAccion == 'recuperar')
                    {
                        $strEstadoBusqueda      = $strEstadoPrestado;
                    }
                    elseif($strAccion == 'eliminar')
                    {
                        $strEstadoBusqueda      = $strEstadoPrestado;
                    }
                    
                    $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                ->findOneBy( array('id' => $intIdCuadrilla, 'estado' => $strEstadoBusqueda) );
                    
                    if($objCuadrilla)
                    {

                        $intDepartamentoCuadrilla = $objCuadrilla->getDepartamentoId();
    
                        $arrayParametros                     = array();
                        $arrayParametros['usuario']          = $intIdPersonEmpresaRol;
                        $arrayParametros['departamento']     = $intIdDepartamento;
                        $arrayParametros['empresa']          = $intIdEmpresa;
                        $arrayParametros['exceptoUsr']       = array($intIdPersonEmpresaRol);
                        $arrayParametros['asignadosA']       = $intIdPersonEmpresaRol;
                        $arrayParametros['intIdCuadrilla']   = $objCuadrilla->getId();
                        $arrayParametros['nombreArea']       = 'Tecnico'; 
                        $arrayParametros['rolesNoIncluidos'] = array('Cliente', 'Pre-cliente', 'Mensajero', 'Programador Jr.');
    
                        switch ($strAccion) 
                        {
                            case 'prestar':
                                $arrayParametros['usuario']         = $objCuadrilla->getCoordinadorPrincipalId();
                                $arrayParametros['asignadosA']      = $objCuadrilla->getCoordinadorPrincipalId();
                                $arrayParametros['exceptoUsr']      = array($objCuadrilla->getCoordinadorPrincipalId());
                                break;
                            case 'devolver':
                                $arrayParametros['usuario']         = $objCuadrilla->getCoordinadorPrincipalId();
    
                                if ($intDepartamentoCuadrilla != $intIdDepartamento) 
                                {
                                    $arrayParametros['departamento']= $intDepartamentoCuadrilla;
                                }
                                break;
                            case 'recuperar':
                                $arrayParametros['usuario']         = $objCuadrilla->getCoordinadorPrincipalId();
                                $arrayParametros['asignadosA']      = $objCuadrilla->getCoordinadorPrestadoId();
    
                                if ($intDepartamentoCuadrilla != $intIdDepartamento) 
                                {
                                    $arrayParametros['departamento']= $intDepartamentoCuadrilla;
                                }
                                break;
                            default:
                                error_log($e->getMessage());

                                $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';
                                
                                $emComercial->getConnection()->rollback();
                                $emComercial->getConnection()->close();
                                break;
                        }
                        $arrayResultados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                           ->findPersonalByCriterios($arrayParametros);
    
                        $arrayRegistros = $arrayResultados['registros'];
                            
                        if( $arrayRegistros )
                        {
                            
                            foreach($arrayRegistros as $arrayDatos)
                            {
                                $intTmpIdPersonaEmpresaRol = $arrayDatos['idPersonaEmpresaRol'];
                                $objInfoPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                         ->findOneById($intTmpIdPersonaEmpresaRol);
    
                                $arrayParamPersona =  array(
                                                            "idPersona"            => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                            "codEmpresa"           => '10'
                                                        );
                                $intSecuencialId     =  $intSecuencialId+1;
                                $objInfoPersona      =  $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                    ->getDatosPersonaById($arrayParamPersona);
                                $strNoEmpleado       =  $objInfoPersona[0]['NO_EMPLE'];
                                $arrayNoEmpleado[]   =  intval($strNoEmpleado);
                                $arrayFechaInicio[]  =  $strFechaInicio;
                                $arrayFechaFin[]     =  $strFechaFin;
                                $arrayHoraInicio[]   =  $strHoraInicio;
                                $arrayHoraFin[]      =  $strHoraFin;
                                $arrayTipoHorario[]  =  intval($arrayTipoHorario1);
                                $arrayPlaniAnual[]   =  'N';
                                $arrayIdSecuencia[]  =  $intSecuencialId;
                                $arrayCuadrillaId[]    =  intval($intIdCuadrilla);

                                if ($arrayDiasSemana1 != '')
                                {   
                                    $arrayDias = [];
                                    for ($intIndice=0; $intIndice < count($arrayDiasSemana1); $intIndice++) 
                                    {
                                        $arrayDias[] = array('noEmple' => intval($strNoEmpleado), 
                                                             "dia"=> intval($arrayDiasSemana1[$intIndice]),
                                                             "idDia" => $intSecuencialId);
                                    }
                                    $arrayDiasEmple[] = $arrayDias;
                    
                                }
                            }
                            $arrayDatosTrama['usrCreacion']        = $objSession->get('user');
                            $arrayDatosTrama['empresaCod']         = $intIdEmpresa;
                            $arrayDatosTrama['noEmpleado']         = $arrayNoEmpleado;
                            $arrayDatosTrama['fechaInicio']        = $arrayFechaInicio;
                            $arrayDatosTrama['fechaFin']           = $arrayFechaFin;
                            $arrayDatosTrama['horaInicio']         = $arrayHoraInicio;
                            $arrayDatosTrama['tipoHorario']        = $arrayTipoHorario;
                            $arrayDatosTrama['horaFin']            = $arrayHoraFin;
                            $arrayDatosTrama['planificacionAnual'] = $arrayPlaniAnual;
                            $arrayDatosTrama['idSecuencia']        = $arrayIdSecuencia;
                            $arrayDatosTrama['diasEscogidos']      = $arrayDiasEmple;
                            $arrayDatosTrama['cuadrillaId']        = $arrayCuadrillaId;
                        }
                    }

                    $objInfoHistoEmpleCuadrilla    = $emSoporte ->getRepository('schemaBundle:InfoHorarioEmpleados')
                                                                ->ejecutarCrearPlaniCuadrillaHE($arrayDatosTrama);

                    if ($objInfoHistoEmpleCuadrilla)
                    {
                        $strMensaje = 'OK';
                        if ($objInfoHistoEmpleCuadrilla['status'] == 'ERROR')
                        {
                            $strMensaje = $objInfoHistoEmpleCuadrilla['mensaje'];   
                            $strStatus = $objInfoHistoEmpleCuadrilla['status'];
                        }
                        else 
                        {
                            $strMensaje = $objInfoHistoEmpleCuadrilla['mensaje'];   
                            $strStatus = $objInfoHistoEmpleCuadrilla['status'];
                        }
                    }
                    
                }
                
            }
            catch(\Exception $e)
            {
                error_log($e->getMessage());

                $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';
                
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }

        }
        elseif($intIdCuadrilla != '' )
        {
            $objIntegrantes            = $objRequest->get('strEmpleados')? json_decode($objRequest->get('strEmpleados')) : '';
            $arrayIntegrantes           = $objIntegrantes->encontrados;
            
            if( $arrayIntegrantes )
                        {
                            
                            foreach($arrayIntegrantes as $arrayDatos)
                            {
                                $intTmpIdPersonaEmpresaRol = $arrayDatos->intIdPersonaEmpresaRol;
                                $objInfoPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                         ->findOneById($intTmpIdPersonaEmpresaRol);
    
                                $arrayParamPersona =  array(
                                                            "idPersona"            => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                            "codEmpresa"           => '10'
                                                        );
                                $objInfoPersona      =  $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                    ->getDatosPersonaById($arrayParamPersona);

                                //historial al eliminar empleado con detalle del horario de la cuadrilla a la que esta saliendo
                                
                                $intSecuencialId     =  $intSecuencialId+1;
                                $strNoEmpleado       =  $objInfoPersona[0]['NO_EMPLE'];
                                $arrayNoEmpleado[]   =  intval($strNoEmpleado);
                                $arrayFechaInicio[]  =  $strFechaInicio;
                                $arrayFechaFin[]     =  $strFechaFin;
                                $arrayHoraInicio[]   =  $strHoraInicio;
                                $arrayHoraFin[]      =  $strHoraFin;
                                $arrayTipoHorario[]  =  intval($arrayTipoHorario1);
                                $arrayPlaniAnual[]   =  'N';
                                $arrayIdSecuencia[]  =  $intSecuencialId;
                                $arrayCuadrillaId[]    =  intval($intIdCuadrilla);

                                if ($arrayDiasSemana1 != '')
                                {   
                                    $arrayDias = [];
                                    for ($intIndice=0; $intIndice < count($arrayDiasSemana1); $intIndice++) 
                                    {
                                        $arrayDias[] = array('noEmple' => intval($strNoEmpleado), 
                                                             "dia"=> intval($arrayDiasSemana1[$intIndice]),
                                                             "idDia" => $intSecuencialId);
                                    }
                                    $arrayDiasEmple[] = $arrayDias;
                    
                                }
                            }
                            $arrayDatosTrama['usrCreacion']        = $objSession->get('user');
                            $arrayDatosTrama['empresaCod']         = $intIdEmpresa;
                            $arrayDatosTrama['noEmpleado']         = $arrayNoEmpleado;
                            $arrayDatosTrama['fechaInicio']        = $arrayFechaInicio;
                            $arrayDatosTrama['fechaFin']           = $arrayFechaFin;
                            $arrayDatosTrama['horaInicio']         = $arrayHoraInicio;
                            $arrayDatosTrama['tipoHorario']        = $arrayTipoHorario;
                            $arrayDatosTrama['horaFin']            = $arrayHoraFin;
                            $arrayDatosTrama['planificacionAnual'] = $arrayPlaniAnual;
                            $arrayDatosTrama['idSecuencia']        = $arrayIdSecuencia;
                            $arrayDatosTrama['diasEscogidos']      = $arrayDiasEmple;
                            $arrayDatosTrama['cuadrillaId']        = $arrayCuadrillaId;

                            $objInfoHistoEmpleCuadrilla    = $emSoporte ->getRepository('schemaBundle:InfoHorarioEmpleados')
                                                                        ->ejecutarCrearPlaniCuadrillaHE($arrayDatosTrama);
                            
                            if ($objInfoHistoEmpleCuadrilla)
                            {
                                $strMensaje = '';
                                $strStatus  = '';
                                if ($objInfoHistoEmpleCuadrilla['status'] == 'ERROR')
                                {
                                    $strMensaje = $objInfoHistoEmpleCuadrilla['mensaje'];   
                                    $strStatus = $objInfoHistoEmpleCuadrilla['status'];
                                }
                                else
                                {
                                    $strMensaje = $objInfoHistoEmpleCuadrilla['mensaje'];   
                                    $strStatus = $objInfoHistoEmpleCuadrilla['status'];
                                }
                            }
                        }
                else 
                {
                    $strStatus  = 'ERROR';
                    $strMensaje = 'No se ha seleccionado ningun empleado';
                }
                    
        }
        else
        {
            $strStatus  = 'ERROR';
            $strMensaje = 'No se ha seleccionado ninguna cuadrilla';
        }

        $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        $objResponse->setData($arrayRespuesta);

        return $objResponse;

    }

    /**
     * Metodo encargado de verificar las planificaciones de los empleados que se agregaron a la cuadrilla (canje, temporal, linea base)
     * si la fecha registrada es menor a la fecha hoy se elimina la planificiacion del empleado diferente a linea base
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * @return JsonResponse
     */
    public function ajaxVerificarPlanificacionAction()  	
    { 	
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $objResponse         = new JsonResponse();
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSoporte           = $this->getDoctrine()->getManager("telconet_soporte");

        $intIdEmpresa       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;

        $objDateHoy      = new \DateTime('now');
        $intDiaSemana   = 0;
        $intIdCuadrilla             = $objRequest->get('intIdCuadrilla') ? $objRequest->get('intIdCuadrilla'): '';

        $objIntegrantes            = $objRequest->get('strEmpleados')? json_decode($objRequest->get('strEmpleados')) : '';
        $arrayIntegrantes           = $objIntegrantes->encontrados;
        
        if( $arrayIntegrantes )
                        {
                            
                            foreach($arrayIntegrantes as $arrayDatos)
                            {
                                $intTmpIdPersonaEmpresaRol = $arrayDatos->intIdPersonaEmpresaRol;
                                $objInfoPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                         ->findOneById($intTmpIdPersonaEmpresaRol);
    
                                $arrayParamPersona =  array(
                                                            "idPersona"            => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                            "codEmpresa"           => '10'
                                                        );
                                $objInfoPersona  =  $emComercial->getRepository('schemaBundle:InfoPersona')->getDatosPersonaById($arrayParamPersona);

                                //historial al eliminar empleado con detalle del horario de la cuadrilla a la que esta saliendo
                                $arrayTmpParametrosHistoEmple = array(  'estado'        => 'Activo', 
                                                                        'cuadrillaId'   => $intIdCuadrilla, 
                                                                        'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId()->getId());

                                $objDetalleHistoEmple = $emComercial->getRepository('schemaBundle:InfoHistoEmpleCuadrilla')
                                    ->findOneBy($arrayTmpParametrosHistoEmple);
                              
                                if ($objDetalleHistoEmple)
                                {
                                    if($objDetalleHistoEmple->getTipoHorarioId() != 1)
                                    {
                                        $arrayTmpParamInfoDiaSemanaCuadri = array(  'estado'        => 'Activo', 
                                                                                  'personaId'     => $objInfoPersonaEmpresaRol->getPersonaId() 
                                                                                                                              ->getId());
                                    }
                                    else
                                    {
                                        $arrayTmpParamInfoDiaSemanaCuadri = array(  'estado'        => 'Activo', 
                                                                                  'cuadrillaId'     => $intIdCuadrilla);
                                    }

                                    $objInfoDiaSemanaCuadrilla    = $emComercial->getRepository('schemaBundle:InfoDiaSemanaCuadrilla')
                                                                                ->findBy($arrayTmpParamInfoDiaSemanaCuadri);
                                    
                                    if( $objInfoDiaSemanaCuadrilla)
                                    {
                                        for ($intIndice=0; $intIndice < count($objInfoDiaSemanaCuadrilla); $intIndice++)
                                        {   
                                            $intNumeroDiaId[] = $objInfoDiaSemanaCuadrilla[$intIndice]->getNumeroDiaId();
                                        }
                                    }  
                                    $objDateInicio    = new \DateTime($objDetalleHistoEmple->getFechaInicio().' '.
                                                                      $objDetalleHistoEmple->getHoraInicio()); 
                                    $objDateFin       = new \DateTime($objDetalleHistoEmple->getFechaFin().' '.$objDetalleHistoEmple->getHoraFin());
                                    $objDateInicio1   = new \DateTime($objDetalleHistoEmple->getFechaInicio());
                                    $objDateFin1      = new \DateTime($objDetalleHistoEmple->getFechaFin());
                                    $intDiferenciaDia = date_diff($objDateFin1, $objDateInicio1);
                                    $strDiferenciaDay1   = $intDiferenciaDia->format('%a');
                                    $objFechaInicio   = $objDateInicio;
                                    for ($intContador = 0; $intContador <= intval($strDiferenciaDay1); $intContador++) 
                                    { 
                                        if($intContador > 0)
                                        {
                                            $objFechaInicio    = $objDateInicio->add(new \DateInterval("P1D"));
                                        }
                                        
                                        $strDiaSemanaFechaInicio = date('w', strftime($objFechaInicio->getTimestamp()));
                                        $intDiaSemana = intval($strDiaSemanaFechaInicio);
                                        $boolEsDiaSemana = in_array($intDiaSemana+1, $intNumeroDiaId);
                                        
                                                            
                                                            $boolEsMenorAFechaIniIngresada = $objDateHoy < $objFechaInicio ? true : false;
                                                            $intNumeroPersona = $objInfoPersona[0]['NO_EMPLE'];
                                                            if ($boolEsMenorAFechaIniIngresada && $boolEsDiaSemana)
                                                            {
                                                                $arrayTmpParametrosHorarioEmple = array('idCuadrilla'   => intval($intIdCuadrilla),
                                                                                                        'idTipoHorario' => $objDetalleHistoEmple
                                                                                                                            ->getTipoHorarioId(),
                                                                                                        'feInicio' => date("d-m-Y", 
                                                                                                                      strftime($objFechaInicio
                                                                                                                      ->getTimestamp())),
                                                                                                        'noEmple'  => intval($intNumeroPersona));
                    
                                                                $objHorarioEmple = $emComercial->getRepository('schemaBundle:InfoHorarioEmpleados')
                                                                                               ->getHorarioEmpleado($arrayTmpParametrosHorarioEmple);
                                                                if(count($objHorarioEmple) > 0)
                                                                    {
                                                                        $arrayIdHorarioEmpleado[] = intval($objHorarioEmple[0]['idHorarioEmpleado']);
                                                                        $strObservacion = 'Se elimina planificacion de empleado desde el telcos+';
                                                                        $arrayDatosTrama = [];
                                                                        $arrayDatosTrama['idHorarioEmpleado']  = $arrayIdHorarioEmpleado;
                                                                        $arrayDatosTrama['usrCreacion']        = $objSession->get('user');
                                                                        $arrayDatosTrama['empresaCod']         = $intIdEmpresa;
                                                                        $arrayDatosTrama['observacion']        = $strObservacion;
                                                                        $objInfoHistoEmpleCuad = $emSoporte
                                                                                                ->getRepository('schemaBundle:InfoHorarioEmpleados')
                                                                                                ->ejecutarEliminarPlaniCuadrillaHE($arrayDatosTrama);
                        

                                                                            if ($objInfoHistoEmpleCuad)
                                                                            {
                                                                                $strMensaje = '';
                                                                                $strStatus  = '';
                                                                                if ($objInfoHistoEmpleCuad['status'] == 'ERROR')
                                                                                {
                                                                                    $strMensaje = $objInfoHistoEmpleCuad['mensaje'];   
                                                                                    $strStatus = $objInfoHistoEmpleCuad['status'];
                                                                                }
                                                                                else
                                                                                {
                                                                                    $strMensaje = $objInfoHistoEmpleCuad['mensaje'];   
                                                                                    $strStatus = $objInfoHistoEmpleCuad['status'];
                                                                                }
                                                                            }
                                                                        
                                                                    }
                                                                    
                                                            }
                                                                $strMensaje = 'Verificacion correcta';   
                                                                $strStatus = 'OK';
                                                            
                                        
                                    }


                                }
                                else
                                {
                                    $strMensaje = 'No existe registro en el historial empleado cuadrilla';   
                                    $strStatus  = 'OK';
                                }

                            }

                        }
                        else
                        {
                            $strMensaje = 'No existen integrantes seleccionados';   
                            $strStatus = 'ERROR';
                        }
        $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }

    /**
     * Metodo encargado de retornar los departamentos para el proceso automático de horas extras
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 14-04-2023
     *
     * @return JsonResponse
     */
    public function ajaxGetDepatamentosConfHEAction()  	
    { 	
        $objResponse       = new JsonResponse();
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');

        $arrayDiasSemana   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getResultadoDetallesParametro('DEPARTAMENTOS_TECNICA','','');
        $intTotal          = count($arrayDiasSemana['registros']);       
        
        if( $arrayDiasSemana )
        {
            foreach($arrayDiasSemana['registros'] as $arrayDiaSemana)
            {
                $arrayDiasSemanaDet[]   = array ('nombreDepartamento' => $arrayDiaSemana['valor1']);
            }
        }

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayDiasSemanaDet);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }
}
