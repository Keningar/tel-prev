<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoAsignacion;
use telconet\administracionBundle\Service\InfoCoordinadorTurnoService;

use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 

use telconet\schemaBundle\Entity\SeguPerfilPersona;
use telconet\schemaBundle\Entity\AdmiCuadrillaHistorial;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Jefes controller.
 *
 * Controlador que se encargará de administrar la información acerca de los jefes
 * de la empresa
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 25-08-2015
 */
class JefesController extends Controller
{
    const CARACTERISTICA_CARGO_GERENTE_PRODUCTO = 'CARGO_GERENTE_PRODUCTO';
    const CARACTERISTICA_CARGO_GRUPO_ROLES      = 'CARGO_GRUPO_ROLES_PERSONAL';
    const CARACTERISTICA_CARGO                  = 'CARGO';
    const CARACTERISTICA_META_BRUTA             = 'META BRUTA';
    const CARACTERISTICA_META_ACTIVA            = 'META ACTIVA';
    const PARAMETRO_CARGO                       = 'CARGOS';
    const CARACTERISTICA_PRESTAMO_EMPLEADO      = 'PRESTAMO EMPLEADO';
    const CARACTERISTICA_PRESTAMO_CUADRILLA     = 'PRESTAMO CUADRILLA';
    const DETALLE_ASOCIADO_ELEMENTO_TABLET      = 'LIDER';
    const PARAMETRO_CARGOS_TECNICOS             = 'CARGOS AREA TECNICA';
    
    
    /**
     * Documentación para el método 'indexAction'.
     *
     * Muestra el listado de todos los jefes (líderes, coordinadores, entre otros) del usuario logueado.
     *
     * @return Response 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-10-2015 - Se adapta la opción para las administración de jefes para el área técnica usando el parámetro
     *                           '$strNombreArea'
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 25-08-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-03-2017 - Se envía el prefijo de la empresa en sessión al twig
     */
    public function indexAction($strNombreArea)
    {
        if( false === $this->get('security.context')->isGranted('ROLE_307-1') && 
            false === $this->get('security.context')->isGranted('ROLE_296-1') )
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCaracteristicaCargo = ( $strPrefijoEmpresa == 'TN' ) ? self::CARACTERISTICA_CARGO_GRUPO_ROLES : self::CARACTERISTICA_CARGO;
        
        return $this->render( 'administracionBundle:Jefes:index.html.twig',
                              array(
                                        'strCaracteristicaCargo'      => $strCaracteristicaCargo,
                                        'strCaracteristicaMetaBruta'  => self::CARACTERISTICA_META_BRUTA,
                                        'strCaracteristicaMetaActiva' => self::CARACTERISTICA_META_ACTIVA,
                                        'strNombreArea'               => $strNombreArea,
                                        'strPrefijoEmpresa'           => $strPrefijoEmpresa
                                   )
                            );
    }
    
    
    /**
     * Documentación para el método 'gridListadoEmpleadosAction'.
     *
     * Consulta todas los empleados que pertenecen al departamento del usuario logoneado
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 25-08-2015
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-10-2015 - Se cambia el método 'findPersonal' por 'findPersonalByCriterios'
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 13-10-2015 - Se adapta la opción para las administración de jefes para el área técnica usando el parámetro
     *                           '$strNombreArea'
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 14-12-2015 - Se modifica para que retorne el personal de acuerdo a la ciudad del usuario en sessión y los cargos del personal 
     *                           para el área Técnica, y para ello no se envía el parámetro 'departamento' cuando se requiere realizar la búsqueda 
     *                           del personal.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 28-06-2016 - Se agrega al 'Personal Externo' en la visualización del personal para ser asignado a un Jefe en la parte Comercial.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.5 29-11-2016 Se agrega en los filtros aquellos cargos que funcionarán como cargos de Jefe para que puedan ser considerados 
     *                         al prestar empleados y cambiar responsable
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 13-03-2017 - Se envía el parámetro 'strPrefijoEmpresa' a la función 'getListadoEmpleados' para que realice las validaciones
     *                           correspondientes dependiendo de la empresa en sessión. Adicional se valida que para TN se busque al personal 
     *                           asignado a los departamentos agrupados en el parámetro 'GRUPO_DEPARTAMENTOS' del área COMERCIAL.
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.7 16-01-2023 - Se agrega una validación para que los jefes que solicitan información de sus empleados correspondientes 
     *                           al departamento de ‘Operaciones Urbanas’, lleven adjunto en cada registro información sobre 
     *                           la existencia o no de un Turno Asignado.
     */   
    public function gridListadoEmpleadosAction()
    {
        if( false === $this->get('security.context')->isGranted('ROLE_307-1') && 
            false === $this->get('security.context')->isGranted('ROLE_296-1') )
        {            
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        $strSoloAsis   = $this->get('security.context')->isGranted('ROLE_296-6337');
        $response      = new JsonResponse();
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $strNombres    = $objRequest->query->get("nombre") ? $objRequest->query->get("nombre") : '';
        $strApellidos  = $objRequest->query->get("apellido") ? $objRequest->query->get("apellido") : '';
        $strCargo      = $objRequest->query->get("cargo") ? $objRequest->query->get("cargo") : '';
        $intLimite     = $objRequest->query->get("limit") ? $objRequest->query->get("limit") : 0;
        $intInicio     = $objRequest->query->get("start") ? $objRequest->query->get("start") : 0;
        $strNombreArea = $objRequest->query->get("strNombreArea") ? $objRequest->query->get("strNombreArea") : '';
        
        $serviceJefesComercial = $this->get('administracion.JefesComercial');
        $serviceJefesTecnico   = $this->get('administracion.JefesTecnico');
        $serviceUtilidades     = $this->get('administracion.Utilidades');
        $emComercial           = $this->getDoctrine()->getManager('telconet');

        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strCodEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $intIdDepartamento      = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strUsuarioCreacion     = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $strCaracteristicaCargo = ( $strPrefijoEmpresa == 'TN' ) ? self::CARACTERISTICA_CARGO_GRUPO_ROLES : self::CARACTERISTICA_CARGO;

        $strEstadoActivo       = 'Activo';

        $arrayParametros = array(
                                    'usuario'             => $intIdPersonEmpresaRol,
                                    'inicio'              => $intInicio,
                                    'limite'              => $intLimite,
                                    'empresa'             => $strCodEmpresa,
                                    'strPrefijoEmpresa'   => $strPrefijoEmpresa,
                                    'nombreArea'          => $strNombreArea,
                                    'estadoActivo'        => $strEstadoActivo,
                                    'caracteristicaCargo' => $strCaracteristicaCargo,
                                    'metaBruta'           => self::CARACTERISTICA_META_BRUTA,
                                    'metaActiva'          => self::CARACTERISTICA_META_ACTIVA,
                                    'rolesNoIncluidos'    => array('Cliente', 'Pre-cliente', 'Mensajero', 'Programador Jr.'),
                                    'criterios'           => array(
                                                                      'nombres'   => $strNombres,
                                                                      'apellidos' => $strApellidos,
                                                                      'cargo'     => $strCargo
                                                                  )
                                );
        
        
        if( $strNombreArea == 'Comercial' )
        {
            $arrayParametros['departamento'] = $intIdDepartamento;
            
            //SE VALIDA PARA LA EMPRESA TN QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
            if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
            {
                $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                      'strValorRetornar'  => 'valor1',
                                                      'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                      'strNombreModulo'   => 'COMERCIAL',
                                                      'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                      'strValor2Detalle'  => 'COMERCIAL',
                                                      'strUsrCreacion'    => $strUsuarioCreacion,
                                                      'strIpCreacion'     => $strIpCreacion);
                
                $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                {
                    $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
            }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
            
            $arrayParametros['strTipoRol'] = array('Empleado', 'Personal Externo');
            $arrayParametros['strSoloAsis'] = $strSoloAsis ? 'S' : 'N';
            $arrayResultados = $serviceJefesComercial->getListadoEmpleados( $arrayParametros );
        }
        elseif( $strNombreArea == 'Tecnico' )
        {
            $arrayParametros['esJefe']          = 'S';
            $arrayParametros['soloJefesNaf']    = 'S';
            $arrayCargosFuncionanComoJefe       = array();
            
            
            $arrayCargos = array();
            $objCargos   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                self::PARAMETRO_CARGOS_TECNICOS, 
                                                                                                '', 
                                                                                                '', 
                                                                                                '', 
                                                                                                'Jefes', 
                                                                                                '',
                                                                                                '', 
                                                                                                ''
                                                                                             );

            if( $objCargos )
            {
                foreach($objCargos as $objCargoTecnico)
                {
                    $arrayCargos[]              = $objCargoTecnico['descripcion'];
                    $strCargoFuncionaComoJefe   = $objCargoTecnico['valor2'];
                    
                    if($strCargoFuncionaComoJefe=="SI")
                    {
                        $arrayCargosFuncionanComoJefe[] = $objCargoTecnico['descripcion'];
                    }

                }//foreach($objCargosSuperiores as $objCargoSuperior)
            }//( $objCargosSuperiores )

            $arrayParametros['criterios']['cargoSimilar']   = $arrayCargos;
            $arrayParametros['cargosFuncionanComoJefe']     = $arrayCargosFuncionanComoJefe;    
            $arrayResultados = $serviceJefesTecnico->getListadoEmpleados( $arrayParametros );
        }
        
        $arrayEmpleado          = $emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->getPersonaDepartamentoPorUserEmpresa($strUsuarioCreacion, $strCodEmpresa);
        $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];

        if ($strNombreDepartamento == 'Operaciones Urbanas' && $strPrefijoEmpresa == 'TN')
        {
            $arrayNuevosResultados  = array();
            $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
            
            foreach($arrayResultados['usuarios'] as $objEmpleado)
            {
                $intIdPersonaEmpresaRol = $objEmpleado['intIdPersonaEmpresaRol'];
                $arrayRespuesta         = $serviceAdministracion->getTurnoPorPersona($intIdPersonaEmpresaRol);
                
                $objEmpleado['strTieneTurno']  = $arrayRespuesta['strTieneTurno'];
                $objEmpleado['intIdTurno']     = $arrayRespuesta['intIdTurno'];
    
                $arrayNuevosResultados[] = $objEmpleado;
            }
            
            $arrayResultados['usuarios'] = $arrayNuevosResultados;
        }

        $response->setData( $arrayResultados );
        
        return $response;
    }
    

    /**
     * Documentación para el método 'getTurnoAction'.
     * 
     * Se busca y retorna un Turno en base a la Id.
     * 
     * @return JsonResponse
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     *  
     */   
    public function getTurnoAction()
    {
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $intIdTurno    = $objRequest->request->get('intIdTurno') ? $objRequest->request->get('intIdTurno') : 0;
        $objResponse   = new JsonResponse();

        $arrayNuevosResultados  = array();
        $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');

        $arrayResultado = $serviceAdministracion->getTurnoPorId($intIdTurno);
        
        $objResponse->setData($arrayResultado);
        
        return $objResponse;
    }


    /**
     * Documentación para el método 'deleteTurnoAjaxAction'.
     * 
     * Cambia a estado 'Eliminado' un Turno en base a la Id.
     * 
     * @return JsonResponse
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     *  
     */ 
    public function deleteTurnoAjaxAction()
    {
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $strResponse   = 'ERROR';
        $intIdTurno    = $objRequest->request->get('intIdTurno') ? $objRequest->request->get('intIdTurno') : 0;
        $strUsrUltMod  = $objSession->get('user') ? $objSession->get('user'):'';
        $strIpUltMod   = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
        
        try
        {
            $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');

            $arrayParametros                                = array();
            $arrayParametros['intIdInfoCoordinadorTurno']   = $intIdTurno;
            $arrayParametros['strUsrUltMod']                = $strUsrUltMod;
            $arrayParametros['strIpUltMod']                 = $strIpUltMod;
                
            $arrayResultados = $serviceAdministracion->eliminarInfoCoordinadorTurno($arrayParametros);
            $strResponse     = $arrayResultados['status'];
        }
        catch (\Exception $e)
        {   
            error_log(  'ERROR: '.$e->getMessage()  );
            $strResponse = 'Ocurrió un error al eliminar el turno, por favor consulte con Sistemas.';
        }

        return new Response($strResponse);
    }
    

    /**
     * Documentación para el método 'getEmpleadosAction'.
     *
     * Consulta los empleados o los jefes que pertenecen al departamento del usuario logoneado
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-08-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-10-2015 - Se cambia el método 'findPersonal' por 'findPersonalByCriterios'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 13-10-2015 - Se adapta la opción para las administración de jefes para el área técnica usando el parámetro
     *                           '$strNombreArea'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 26-10-2015 - Se modifica para enviar el parámetro '$arrayParametros['prestamoEmpleado']' el cual ayudará a verificar
     *                           si el empleado asignado al Jefe o Coordinador está en estado 'Prestado', adicional se añaden los filtros
     *                           por Nombre y Apellidos de los empleados.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 17-11-2015 - Se modifica para que envíe como parámetro $arrayParametros['usuario'] el id del Ayudante Coordinador para el área
     *                           Tecnica, para que pueda visualizar los empleados prestados por otros coordinadores.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.5 17-11-2015 - Se modifica para que envíe como parámetro el ayudante coordinador o el usuario en sessión puesto que se necesita 
     *                           que retorne el personal de un departamento específico.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.6 18-11-2015 - Se modifica para enviar el parámetro '$arrayParametros['cargoSimilar']' el cual ayudará a verificar
     *                           los empleados que comiencen con la palabra 'Coord', para retornar el personal que tienen cargo de Coordinador.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.7 18-11-2015 - Se envía la constante 'DETALLE_ASOCIADO_ELEMENTO_TABLET' para saber que personal que es 'Lider' o 'Jefe Cuadrilla'
     *                           tiene una tablet asociada.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.8 14-12-2015 - Se modifica para que retorne el personal de acuerdo a la ciudad del usuario en sessión y los cargos del personal 
     *                           para el área Técnica, y para ello no se envía el parámetro 'departamento' cuando se requiere realizar la búsqueda 
     *                           del personal.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 28-06-2016 - Se agrega al 'Personal Externo' en la visualización del personal para ser asignado a un Jefe en la parte Comercial.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 09-08-2016 - Se modifica para que cuando se requiera listar a solo los jefes se escoja los cargos respectivos a los Jefes
     *                           guardados en la tabla de 'ADMI_PARAMETRO_DET' de acuerdo al parámetro de cabecera 'CARGOS AREA TECNICA' y que el
     *                           valor sea 'Jefes'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 29-11-2016 Se agrega en los filtros aquellos cargos que funcionarán como cargos de Jefe para que puedan ser considerados 
     *                         al prestar empleados y cambiar responsable
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 12-01-2017 Se agrega el parámetro para filtrar los empleados por la región del usuario en sesión en lugar del cantón en sesión
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 14-03-2017 - Se envía el parámetro 'strPrefijoEmpresa' a la función 'getListadoEmpleados' para que realice las validaciones
     *                           correspondientes dependiendo de la empresa en sessión.
     *                           Se valida que para TN se busque al personal asignado a los departamentos agrupados en el parámetro
     *                           'GRUPO_DEPARTAMENTOS' del área COMERCIAL, también si se consultan los Jefes del área COMERCIAL se agregan los cargos
     *                           que tienen como valor4 'ES_JEFE' en los parámetros 'GRUPO_ROLES_PERSONAL'.
     *                           Se agregan a la variable '$arrayParametros' las variables '$strNoAsignadosProducto' y '$strAsignadosProducto' las
     *                           cuales permiten realizar la búsqueda de los empleados asignados o no al grupo de un producto.
     *                           Se agregan a la variable '$arrayParametros' las variables '$strExceptoPersonalExterno' y '$strSoloPersonalExterno'
     *                           las cuales permiten consultar al personal que ha sido marcado como 'Freelance' o 'Comisionista'.
     */   
    public function getEmpleadosAction()
    {
        if( false === $this->get('security.context')->isGranted('ROLE_307-2837') &&
            false === $this->get('security.context')->isGranted('ROLE_296-2837') )
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        
        $response    = new JsonResponse();
        $objRequest  = $this->get('request');
        $objSession  = $objRequest->getSession();
        $arrayCargos = array();
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        
        $serviceJefesComercial = $this->get('administracion.JefesComercial');
        $serviceJefesTecnico   = $this->get('administracion.JefesTecnico');
        $serviceUtilidades     = $this->get('administracion.Utilidades');

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strCodEmpresa         = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $intIdDepartamento     = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strUsuarioCreacion    = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion         = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        
        $strEmpleado         = $objRequest->query->get("query") ? $objRequest->query->get("query") : '';
        $strNombreEmpleado   = $objRequest->query->get("nombre") ? $objRequest->query->get("nombre") : '';
        $strApellidoEmpleado = $objRequest->query->get("apellido") ? $objRequest->query->get("apellido") : '';
        $strFiltroCargo      = $objRequest->query->get("strFiltroCargo") ? $objRequest->query->get("strFiltroCargo") : '';
        $strSoloJefes        = $objRequest->query->get("strSoloJefes") ? $objRequest->query->get("strSoloJefes") : 'N';
        $strCargo            = $objRequest->query->get("strCargo") ? strtoupper($objRequest->query->get("strCargo")) : '';
        $strExceptoUsr       = $objRequest->query->get("strExceptoUsr") ? $objRequest->query->get("strExceptoUsr") : 0;
        $arrayExceptoUsr     = $strExceptoUsr ? explode('|', $strExceptoUsr) : array();
        $strNoAsignados      = $objRequest->query->get("strNoAsignados") ? $objRequest->query->get("strNoAsignados") : '';
        $intAsignadosA       = $objRequest->query->get("strsignadosA") ? $objRequest->query->get("strsignadosA") : 0;
        $intIdCuadrilla      = $objRequest->query->get("intIdCuadrilla") ? $objRequest->query->get("intIdCuadrilla") : 0;
        $strSinCuadrilla     = $objRequest->query->get("strSinCuadrilla") ? $objRequest->query->get("strSinCuadrilla") : '';
        $intLimite           = $objRequest->query->get("limit") ? $objRequest->query->get("limit") : 0;
        $intInicio           = $objRequest->query->get("start") ? $objRequest->query->get("start") : 0;
        $strEstadoActivo     = 'Activo';
        $strSoloJefesNaf     = '';
        $strNombreArea       = $objRequest->query->get("strNombreArea") ? $objRequest->query->get("strNombreArea") : '';
        $strAccion           = $objRequest->query->get("strAccion") ? $objRequest->query->get("strAccion") : '';
        $strExceptoChoferes  = $objRequest->query->get("strExceptoChoferes") ? $objRequest->query->get("strExceptoChoferes") : '';
        $intIdCargoTelcos    = $objRequest->query->get("intIdCargoTelcos") ? $objRequest->query->get("intIdCargoTelcos") : 0;
        $strEsGestion        = $objRequest->query->get("strEsGestion") ? $objRequest->query->get("strEsGestion") : '';
        $intIdCuadrillaGestion = $objRequest->query->get("intIdCuadrillaGestion") ? $objRequest->query->get("intIdCuadrillaGestion") : null;
        
        $strNoAsignadosProducto          = $objRequest->query->get("strNoAsignadosProducto") ? $objRequest->query->get("strNoAsignadosProducto") : "";
        $strAsignadosProducto            = $objRequest->query->get("strAsignadosProducto") ? $objRequest->query->get("strAsignadosProducto") : "";
        $strSoloFreelanceComisionista    = $objRequest->query->get("strSoloFreelanceComisionista") 
                                           ? $objRequest->query->get("strSoloFreelanceComisionista") : "N";
        $strExceptoFreelanceComisionista = $objRequest->query->get("strExceptoFreelanceComisionista")
                                           ? $objRequest->query->get("strExceptoFreelanceComisionista") : "N";
        $strSoloPersonalExterno          = $objRequest->query->get("strSoloPersonalExterno") ? $objRequest->query->get("strSoloPersonalExterno")
                                           : "N";

        $arrayParametros = array();
        
        if( $strCargo != 'JEFE' && $strNombreArea == 'Comercial' ) 
        {
            if( $strPrefijoEmpresa == 'TN' && $strSoloJefes == 'S' )
            {
                //SE VALIDA PARA LA EMPRESA TN QUE SE CONSIDEREN LOS CARGOS DE JEFES DEL PARAMETRO 'GRUPO_ROLES_PERSONAL'
                if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
                {
                    $arrayParametrosCargosJefes = array('strCodEmpresa'     => $strCodEmpresa,
                                                        'strValorRetornar'  => 'id',
                                                        'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                        'strNombreModulo'   => 'COMERCIAL',
                                                        'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                        'strValor4Detalle'  => 'ES_JEFE',
                                                        'strUsrCreacion'    => $strUsuarioCreacion,
                                                        'strIpCreacion'     => $strIpCreacion);

                    $arrayResultadosCargosJefes = $serviceUtilidades->getDetallesParametrizables($arrayParametrosCargosJefes);

                    if( isset($arrayResultadosCargosJefes['resultado']) && !empty($arrayResultadosCargosJefes['resultado']) )
                    {
                        foreach($arrayResultadosCargosJefes['resultado'] as $strCargoParametro)
                        {
                            $arrayCargos[] = ucwords(strtolower($strCargoParametro));
                        }//foreach($arrayResultadosCargosJefes['resultado'] as $strCargo)
                    }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
            }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' && !empty($strSoloJefes) && $strSoloJefes == 'S' )
            else
            {
                $strParametroCabCargo = self::PARAMETRO_CARGO;
                
                if( $strPrefijoEmpresa == 'TN' )
                {
                    $strParametroCabCargo = "GRUPO_ROLES_PERSONAL";
                }
                
                $entityTmpParametro = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneByNombreParametro( 
                                                                            array(
                                                                                    'nombreParametro' => $strParametroCabCargo,
                                                                                    'estado'          => $strEstadoActivo
                                                                                 )
                                                                           );

                $intIdParametroCargo = 0;

                if( $entityTmpParametro )
                {
                    $intIdParametroCargo = $entityTmpParametro->getId();
                }

                $arrayParametros  = array( 
                                            'estado'      => $strEstadoActivo, 
                                            'parametroId' => $intIdParametroCargo,
                                            'descripcion' => $strCargo
                                         );
                
                if( $strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN"  )
                {
                    $arrayParametros['valor3'] = strtoupper($strNombreArea);
                }

                $objParametroCargo = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametros);

                if( $objParametroCargo )
                {
                    $intTmpValor2Cargo = $objParametroCargo->getValor2() ? $objParametroCargo->getValor2() : 0;

                    if( $intTmpValor2Cargo )
                    {
                        if( $strCargo != 'AYUDANTE DEL COORDINADOR' )
                        {
                            $strTmpValor2 = 'CargosMayores:'.$intTmpValor2Cargo;
                        }
                        else
                        {
                            $strTmpValor2 = $intTmpValor2Cargo;
                        }
                        
                        $strTmpNombreArea = '';
                        
                        if( $strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN")
                        {
                            $strTmpNombreArea = strtoupper($strNombreArea);
                        }
                        
                        $objCargosSuperiores = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                                    $strParametroCabCargo, 
                                                                                                                    '', 
                                                                                                                    '', 
                                                                                                                    '', 
                                                                                                                    '', 
                                                                                                                    $strTmpValor2,
                                                                                                                    $strTmpNombreArea, 
                                                                                                                    ''
                                                                                                                 );

                        if( $objCargosSuperiores )
                        {
                            foreach($objCargosSuperiores as $arrayCargoSuperior)
                            {
                                if( $arrayCargoSuperior['descripcion'] != $strCargo )
                                {
                                    if( $strPrefijoEmpresa == 'TN' )
                                    {
                                        $arrayCargos[] = $arrayCargoSuperior['id'];
                                    }
                                    else
                                    {
                                        $arrayCargos[] = ucwords(strtolower($arrayCargoSuperior['descripcion']));
                                    }
                                }//( $arrayCargoSuperior['descripcion'] != $strCargo )
                            }//foreach($objCargosSuperiores as $arrayCargoSuperior)
                        }//( $objCargosSuperiores )
                    }//( $intTmpValor2Cargo )
                }//( $objParametroCargo )
            }//else( $strPrefijoEmpresa == 'TN' && $strSoloJefes == 'S' )
        }
        else
        {
            $strSoloJefesNaf = 'S';
            
            $arrayParametros['soloJefesNaf'] = $strSoloJefesNaf;
        }//( $strCargo != 'JEFE' ) 
        
        
        if( $strNoAsignados )
        {
            /*
             * Se agrega el cargo de la persona que se está consultando para que no se tomen en cuenta las personas que tienen el mismo cargo
             * asignado. Para TN se envía el id del cargo que no se debe consultar.
             */
            if( $strPrefijoEmpresa == 'TN' )
            {
                $arrayCargos[] = $intIdCargoTelcos;
            }
            else
            {
                $arrayCargos[] = ucwords(strtolower($strCargo));
            }
        }
        
        $arrayParametros['usuario']             = $intIdPersonEmpresaRol;
        $arrayParametros['strPrefijoEmpresa']   = $strPrefijoEmpresa;
        $arrayParametros['esJefe']              = $strSoloJefes;
        $arrayParametros['empresa']             = $strCodEmpresa;
        $arrayParametros['exceptoUsr']          = $arrayExceptoUsr;
        $arrayParametros['noAsignados']         = $strNoAsignados;
        $arrayParametros['asignadosA']          = $intAsignadosA;
        $arrayParametros['jefeConCargo']        = $arrayCargos;
        $arrayParametros['inicio']              = $intInicio;
        $arrayParametros['limite']              = $intLimite;
        $arrayParametros['sinCuadrilla']        = $strSinCuadrilla;
        $arrayParametros['intIdCuadrilla']      = $intIdCuadrilla;
        $arrayParametros['criterios']           = array( 
                                                            'nombreEmpleado' => $strEmpleado, 
                                                            'cargo'          => $strFiltroCargo,
                                                            'nombres'        => $strNombreEmpleado, 
                                                            'apellidos'      => $strApellidoEmpleado 
                                                       );
        $arrayParametros['nombreArea']            = $strNombreArea;
        $arrayParametros['rolesNoIncluidos']      = array('Cliente', 'Pre-cliente', 'Mensajero', 'Programador Jr.');
        $arrayParametros['estadoActivo']          = $strEstadoActivo;
        $arrayParametros['caracteristicaCargo']   = self::CARACTERISTICA_CARGO;
        $arrayParametros['metaBruta']             = self::CARACTERISTICA_META_BRUTA;
        $arrayParametros['metaActiva']            = self::CARACTERISTICA_META_ACTIVA;
        $arrayParametros['prestamoEmpleado']      = self::CARACTERISTICA_PRESTAMO_EMPLEADO;
        $arrayParametros['prestamoCuadrilla']     = self::CARACTERISTICA_PRESTAMO_CUADRILLA;
        $arrayParametros['detalleElementoTablet'] = self::DETALLE_ASOCIADO_ELEMENTO_TABLET;
        
        $arrayParametros['strNoAsignadosProducto']          = $strNoAsignadosProducto;
        $arrayParametros['strAsignadosProducto']            = $strAsignadosProducto;
        $arrayParametros['strSoloFreelanceComisionista']    = $strSoloFreelanceComisionista;
        $arrayParametros['strExceptoFreelanceComisionista'] = $strExceptoFreelanceComisionista;
        
        if($strExceptoChoferes=="S")
        {
            $arrayParametros['rolesNoIncluidos'][]      = 'Chofer';
        } 
        
        if( $strNombreArea == 'Comercial' )
        {
            $arrayParametros['departamento'] = $intIdDepartamento;
            $arrayParametros['strTipoRol']   = array('Empleado', 'Personal Externo');
            
            if( $strSoloPersonalExterno == "S" )
            {
                $arrayParametros['strTipoRol'] = array('Personal Externo');
            }
            
            //SE VALIDA PARA LA EMPRESA TN QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
            if( $strPrefijoEmpresa == 'TN' )
            {
                $arrayParametros['caracteristicaCargo'] = self::CARACTERISTICA_CARGO_GRUPO_ROLES;

                $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                      'strValorRetornar'  => 'valor1',
                                                      'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                      'strNombreModulo'   => 'COMERCIAL',
                                                      'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                      'strValor2Detalle'  => 'COMERCIAL',
                                                      'strUsrCreacion'    => $strUsuarioCreacion,
                                                      'strIpCreacion'     => $strIpCreacion);
                
                $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                {
                    $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                
                if( $strSoloJefes == "S" )
                {
                    $arrayParametros['strSoloJefesTelcos'] = 'S';
                }//( $strSoloJefes == "S" )
            }//( $strPrefijoEmpresa == 'TN' )

            $arrayResultados = $serviceJefesComercial->getListadoEmpleados( $arrayParametros );
        }
        elseif( $strNombreArea == 'Tecnico' )
        {
            $arrayParametros['usuario'] = $intAsignadosA ? $intAsignadosA : $intIdPersonEmpresaRol;
            
            $arrayResultCargosFuncionanComoJefe = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->get( self::PARAMETRO_CARGOS_TECNICOS, 
                                                                     '', 
                                                                     '', 
                                                                     '', 
                                                                     'Jefes', 
                                                                     'SI',
                                                                     '', 
                                                                     '' );
            $arrayCargosFuncionanComoJefe   = array();
            if( $arrayResultCargosFuncionanComoJefe )
            {
                foreach($arrayResultCargosFuncionanComoJefe as $objCargoTecnicoFuncionaComoJefe)
                {
                    $arrayCargosFuncionanComoJefe[] = $objCargoTecnicoFuncionaComoJefe['descripcion'];

                }//foreach($objCargosSuperiores as $objCargoSuperior)
            }//( $objCargosSuperiores )
            $arrayParametros['cargosFuncionanComoJefe']     = $arrayCargosFuncionanComoJefe;
            
            if( $strAccion == 'prestamo_cuadrilla')
            {
                $arrayParametros['criterios']['cargo']        = '';
                $arrayParametros['criterios']['cargoSimilar'] = 'Coord';
            }
            else
            {
                /**
                 * SE VERIFICA SI SE REQUIERE SOLO EL PERSONAL ASIGNADO COMO JEFE
                 */
                $strValorParametroDet           = "Personal Tecnico";
                if( $strSoloJefes == "S" )
                {
                    $strValorParametroDet = "Jefes";
                }//( $strSoloJefes == "S" )
                
                
                $arrayCargos = array();
                $objCargos   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                    self::PARAMETRO_CARGOS_TECNICOS, 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    $strValorParametroDet, 
                                                                                                    '',
                                                                                                    '', 
                                                                                                    ''
                                                                                                 );
                
                if( $objCargos )
                {
                    foreach($objCargos as $objCargoTecnico)
                    {
                        $arrayCargos[]  = $objCargoTecnico['descripcion'];
                    }//foreach($objCargosSuperiores as $objCargoSuperior)
                }//( $objCargosSuperiores )
                $arrayParametros['criterios']['cargoSimilar']   = $arrayCargos;
            }//( $strAccion == 'prestamo_cuadrilla')

            if ($strEsGestion == 'SI')
            {
                $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($intIdCuadrillaGestion); 
                $intCoordinadorPrincipal = $objCuadrilla->getCoordinadorPrincipalId();
                $arrayParametros['usuario'] = $intCoordinadorPrincipal;
                $arrayParametros['strExceptoUsr'] = array($intCoordinadorPrincipal);
            }

            if ($intIdCuadrilla)
            {
                $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($intIdCuadrilla); 
                $intCoordinadorPrincipal = $objCuadrilla->getCoordinadorPrincipalId();
                $arrayParametros['usuario'] = $intCoordinadorPrincipal;
                $arrayParametros['strExceptoUsr'] = array($intCoordinadorPrincipal);
            }

            $arrayParametros['strFiltrarPorRegion'] = 'SI';
            $arrayResultados                        = $serviceJefesTecnico->getListadoEmpleados( $arrayParametros );
        }
        
        $response->setData( $arrayResultados );
        
        return $response;
    }
    /**
     * @Secure(roles="ROLE_296-6217")
     * Documentación para el método 'getVendedoresAction'.
     *
     * Retorna los vendedores de acuerdo a los criterios ingresados
     *
     * @return objResponse 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 07-12-2018
     *
     */ 
    public function getVendedoresAction()
    {
        $objResponse = new JsonResponse();
        $objRequest  = $this->get('request');
        $objSession  = $objRequest->getSession();

        $serviceJefesComercial  = $this->get('administracion.JefesComercial');
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $serviceUtil            = $this->get('schema.Util');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strCodEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $intIdDepartamento      = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $strUsuarioCreacion     = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $strEmpleado            = $objRequest->query->get("query") ? $objRequest->query->get("query") : '';
        $strNombreEmpleado      = $objRequest->query->get("nombre") ? $objRequest->query->get("nombre") : '';
        $strApellidoEmpleado    = $objRequest->query->get("apellido") ? $objRequest->query->get("apellido") : '';
        $strFiltroCargo         = $objRequest->query->get("strFiltroCargo") ? $objRequest->query->get("strFiltroCargo") : '';
        $strSoloJefes           = $objRequest->query->get("strSoloJefes") ? $objRequest->query->get("strSoloJefes") : 'N';
        $strExceptoUsr          = $objRequest->query->get("strExceptoUsr") ? $objRequest->query->get("strExceptoUsr") : 0;
        $arrayExceptoUsr        = $strExceptoUsr ? explode('|', $strExceptoUsr) : array();
        $strNombreArea          = $objRequest->query->get("strNombreArea") ? $objRequest->query->get("strNombreArea") : '';
        $strExceptoChoferes     = $objRequest->query->get("strExceptoChoferes") ? $objRequest->query->get("strExceptoChoferes") : '';
        $strEsAsistente         = $objRequest->query->get("strEsAsistente") ? $objRequest->query->get("strEsAsistente") : '';
        $intAsignadosA          = $objRequest->query->get("strsignadosA") ? $objRequest->query->get("strsignadosA") : 0;
        $intLimite              = $objRequest->query->get("limit") ? $objRequest->query->get("limit") : 0;
        $intInicio              = $objRequest->query->get("start") ? $objRequest->query->get("start") : 0;
        $strNoAsignados         = $objRequest->query->get("strNoAsignados") ? $objRequest->query->get("strNoAsignados") : '';
        $strEstadoActivo        = 'Activo';
        $arrayParametros        = array();
        $strSoloPersonalExterno = $objRequest->query->get("strSoloPersonalExterno") ? $objRequest->query->get("strSoloPersonalExterno") : "N";

        try
        {
            if( $strNombreArea == 'Comercial' && $strPrefijoEmpresa == 'TN' && $strEsAsistente == 'S')
            {
                $arrayParametrosCargoVend  = array('strCodEmpresa'     => $strCodEmpresa,
                                                    'strValorRetornar'  => 'id',
                                                    'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                    'strNombreModulo'   => 'COMERCIAL',
                                                    'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                    'strValor4Detalle'  => 'VENDEDOR',
                                                    'strUsrCreacion'    => $strUsuarioCreacion,
                                                    'strIpCreacion'     => $strIpCreacion);

                $arrayResultadosCargoVendedor = $serviceUtilidades->getDetallesParametrizables($arrayParametrosCargoVend);

                if( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) 
                    && isset($arrayResultadosCargoVendedor['intTotal']) && $arrayResultadosCargoVendedor['intTotal'] == 1 )
                {
                    $intIdCargoVendedor = $arrayResultadosCargoVendedor['resultado'][0];
                }

                $arrayParametros['usuario']           = $intIdPersonEmpresaRol;
                $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                $arrayParametros['esJefe']            = $strSoloJefes;
                $arrayParametros['empresa']           = $strCodEmpresa;
                $arrayParametros['exceptoUsr']        = $arrayExceptoUsr;
                $arrayParametros['asistentesDe']      = $intAsignadosA;
                $arrayParametros['CargoVendedor']     = $intIdCargoVendedor;
                $arrayParametros['inicio']            = $intInicio;
                $arrayParametros['limite']            = $intLimite;
                $arrayParametros['strEsAsistente']    = $strEsAsistente;
                $arrayParametros['criterios']         = array(
                                                                    'nombreEmpleado' => $strEmpleado,
                                                                    'cargo'          => $strFiltroCargo,
                                                                    'nombres'        => $strNombreEmpleado,
                                                                    'apellidos'      => $strApellidoEmpleado
                                                                );
                $arrayParametros['nombreArea']        = $strNombreArea;
                $arrayParametros['rolesNoIncluidos']  = array('Cliente', 'Pre-cliente', 'Mensajero', 'Programador Jr.');
                $arrayParametros['estadoActivo']      = $strEstadoActivo;

                if( $strExceptoChoferes == "S" )
                {
                    $arrayParametros['rolesNoIncluidos'][]      = 'Chofer';
                }

                $arrayParametros['departamento'] = $intIdDepartamento;
                $arrayParametros['strTipoRol']   = array('Empleado', 'Personal Externo');

                if( $strSoloPersonalExterno == "S" )
                {
                    $arrayParametros['strTipoRol'] = array('Personal Externo');
                }

                $arrayParametros['caracteristicaCargo'] = self::CARACTERISTICA_CARGO_GRUPO_ROLES;

                $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                        'strValorRetornar'  => 'valor1',
                                                        'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                        'strNombreModulo'   => 'COMERCIAL',
                                                        'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                        'strValor2Detalle'  => 'COMERCIAL',
                                                        'strUsrCreacion'    => $strUsuarioCreacion,
                                                        'strIpCreacion'     => $strIpCreacion);
                $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                {
                    $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                }
                if( $strNoAsignados == 'S' && !empty($intIdPersonEmpresaRol) )
                {
                    /**
                     * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
                     */
                    $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsuarioCreacion);
                    if( !empty($arrayResultadoCaracteristicas) )
                    {
                        $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                        $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
                    }
                    if( $strTipoPersonal == 'SUBGERENTE' )
                    {
                        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
                    }
                }
                $arrayResultados = $serviceJefesComercial->getListadoEmpleados( $arrayParametros );
                $objResponse->setData( $arrayResultados );
            }
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError('TELCOS+',
                                      'administracionBundle.JefesController.getVendedoresAction',
                                      $e->getMessage(),
                                      $strUsuarioCreacion,
                                      $strIpCreacion);
        }
        return $objResponse;
    }
    /**
     * @Secure(roles="ROLE_296-6218")
     * Documentación para el método 'getAsignacionVendedorAction'.
     *
     * Asigna uno o varios vendedores al asistente
     *
     * @return Response 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 07-12-2018
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 - 24-02-2021 - Se agrega lógica para sincronizar los cambio en TelcoCRM.
     *
     */ 
    public function getAsignacionVendedorAction()
    {
        $objRequest                  = $this->get('request');
        $serviceUtil                 = $this->get('schema.Util');
        $serviceTelcoCrm             = $this->get('comercial.ComercialCrm');
        $objSession                  = $objRequest->getSession();
        $strUserSession              = $objSession->get('user');
        $strIpUserSession            = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $objDatetimeActual           = new \DateTime('now');
        $strResponse                 = 'ERROR';
        $strEstadoActivo             = 'Activo';
        $strEstadoEliminado          = 'Eliminado';
        $emComercial                 = $this->getDoctrine()->getManager('telconet');
        $strPrefijoEmpresa           = $objSession->get('prefijoEmpresa');
        $strCodEmpresa               = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strNombreArea               = $objRequest->request->get("strNombreArea") ? $objRequest->request->get("strNombreArea") : '';
        $intIdPersonaEmpresaRolAsist = $objRequest->request->get('intIdJefe') ? $objRequest->request->get('intIdJefe') : null;
        $strVendTemp                 = $objRequest->request->get('strVendTemp') ? $objRequest->request->get('strVendTemp') : null;
        $arrayVendTemp               = $strVendTemp ? explode('|', $strVendTemp) : array();
        $strAccion                   = $objRequest->request->get("strAccion") ? $objRequest->request->get("strAccion") : '';
        $strIdPersonaEmpresaRolVend  = $objRequest->request->get('strIdPersonaEmpresaRol') ? $objRequest->request->get('strIdPersonaEmpresaRol') : 0;
        $arrayPersonaEmpresaRol      = $strIdPersonaEmpresaRolVend ? explode('|', $strIdPersonaEmpresaRolVend) : array();
        $arrayParametrosCarac        = array( 'descripcionCaracteristica' => 'ASISTENTE_POR_CARGO',
                                              'estado'                    => $strEstadoActivo );
        $emComercial->getConnection()->beginTransaction();

        try
        {
            if( $strNombreArea == "Comercial" && $strPrefijoEmpresa == "TN" && $strAccion == "asignar_vendedor" )
            {
                $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy($arrayParametrosCarac);
                foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)
                {
                    if( !empty($intIdPersonaEmpresaRol) && $intIdPersonaEmpresaRol != $intIdPersonaEmpresaRolAsist )
                    {
                        $entityEmpleado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRol);
                        $entityAsist    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRolAsist);

                        $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                        $objInfoPersonaEmpresaRolCarac->setEstado($strEstadoActivo);
                        $objInfoPersonaEmpresaRolCarac->setFeCreacion($objDatetimeActual);
                        $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpUserSession);
                        $objInfoPersonaEmpresaRolCarac->setUsrCreacion($strUserSession);
                        $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityAsist);
                        $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristica);
                        $objInfoPersonaEmpresaRolCarac->setValor($entityEmpleado->getPersonaIdValor());

                        $emComercial->persist($objInfoPersonaEmpresaRolCarac);
                        $emComercial->flush();
                        if ($emComercial->getConnection()->isTransactionActive())
                        {
                            $emComercial->getConnection()->commit();
                            $strResponse = 'OK';
                        }
                        /**
                         * Bloque que agrega la asistente en TelcoCRM.
                         */
                        if((!empty($entityEmpleado) && is_object($entityEmpleado)) &&
                           (!empty($entityAsist)    && is_object($entityAsist)))
                        {
                            $arrayParametrosCrm   = array("strLoginEmpleado"   => $entityEmpleado->getPersonaId()->getLogin(),
                                                          "strLoginAsistente"  => $entityAsist->getPersonaId()->getLogin(),
                                                          "strAccion"          => "AGREGAR",
                                                          "strPrefijoEmpresa"  => $strPrefijoEmpresa,
                                                          "strCodEmpresa"      => $strCodEmpresa);
                            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCrm,
                                                          "strOp"              => 'editAsistente',
                                                          "strFuncion"         => 'procesar');
                            $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                        }
                    }
                }
            }
            else if( ($strNombreArea == "Comercial" && $strPrefijoEmpresa == "TN") && ($strAccion == "asignar_vendedor_tiempo" && !empty($arrayVendTemp)) )
            {
                foreach( $arrayVendTemp as $arrayItemVendTemp)
                {
                    if( !empty($arrayItemVendTemp) )
                    {
                        $objDatetimeLimite      = new \DateTime("now");
                        $intTiempo              = substr($arrayItemVendTemp,0,strpos($arrayItemVendTemp,':'));
                        $strLimite              = "P".$intTiempo."D";
                        $objDatetimeIntervalLimite = new \DateInterval($strLimite);
                        $objDatetimeLimite     ->add($objDatetimeIntervalLimite);
                        $intIdVend              = substr($arrayItemVendTemp,strpos($arrayItemVendTemp,':')+1);
                        
                        $entityAsist         = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRolAsist);
                        $entityEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdVend);
                        $entityLoginVend     = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($entityEmpleado->getPersonaIdValor());
                        $arrayInfoAsignacion = $emComercial->getRepository('schemaBundle:InfoAsignacion')
                                                           ->findBy(array( 'estado'                   => $strEstadoActivo,
                                                                           'personaEmpresaRolIdVend'  => $entityEmpleado,
                                                                           'personaEmpresaRolIdAsist' => $entityAsist));
                        if( !empty($arrayInfoAsignacion) )
                        {
                            foreach($arrayInfoAsignacion as $objInfoAsignacion)
                            {
                                $objInfoAsignacion->setFeUltMod($objDatetimeActual);
                                $objInfoAsignacion->setUsrUltMod($strUserSession);
                                $objInfoAsignacion->setEstado($strEstadoEliminado);
                                $objInfoAsignacion->setIpUltMod($strIpUserSession);

                                $emComercial->persist($objInfoAsignacion);
                                $emComercial->flush();
                            }
                        }
                        $objInfoAsignacion = new InfoAsignacion();
                        $objInfoAsignacion->setPersonaEmpresaRolIdAsist($entityAsist);
                        $objInfoAsignacion->setPersonaEmpresaRolIdVend($entityEmpleado);
                        $objInfoAsignacion->setUsrVendedor($entityLoginVend->getLogin());
                        $objInfoAsignacion->setTiempoDias($objDatetimeLimite);
                        $objInfoAsignacion->setEstado($strEstadoActivo);
                        $objInfoAsignacion->setFeCreacion($objDatetimeActual);
                        $objInfoAsignacion->setIpCreacion($strIpUserSession);
                        $objInfoAsignacion->setUsrCreacion($strUserSession);

                        $emComercial->persist($objInfoAsignacion);
                        $emComercial->flush();
                        if( $emComercial->getConnection()->isTransactionActive())
                        {
                            $emComercial->getConnection()->commit();
                            $strResponse = 'OK';
                        }
                    }
                }
            }
        }
        catch(Exception $e)
        {
            error_log($e->getMessage());
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $serviceUtil->insertError('TELCOS+',
                                    'administracionBundle.JefesController.getAsignacionVendedorAction',
                                    $e->getMessage(),
                                    $strUserSession,
                                    $strIpUserSession);
        }
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        return new Response($strResponse);
    }
    /**
     * @Secure(roles="ROLE_296-6219")
     * Documentación para el método 'getCambioVendedorAction'.
     *
     * Cambia el estado del vendedor al asistente que este asignado, y tambien en caso de que este asignado temporalmente
     *
     * @return Response 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 07-12-2018
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 - 24-02-2021 - Se agrega lógica para sincronizar los cambio en TelcoCRM.
     *
     */    
    public function getCambioVendedorAction()
    {
        $objRequest                  = $this->get('request');
        $serviceUtil                 = $this->get('schema.Util');
        $serviceTelcoCrm             = $this->get('comercial.ComercialCrm');
        $objSession                  = $objRequest->getSession();
        $strUserSession              = $objSession->get('user');
        $strIpUserSession            = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $objDatetimeActual           = new \DateTime('now');
        $strResponse                 = 'ERROR';
        $emComercial                 = $this->getDoctrine()->getManager('telconet');
        $strPrefijoEmpresa           = $objSession->get('prefijoEmpresa');
        $strCodEmpresa               = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdPersonaEmpresaRolAsist = $objRequest->request->get('intIdAsist') ? $objRequest->request->get('intIdAsist') : null;
        $strIdPersonaEmpresaRolVend  = $objRequest->request->get('strIdPersonaEmpresaRol') ? $objRequest->request->get('strIdPersonaEmpresaRol') : 0;
        $arrayPersonaEmpresaRolVend  = $strIdPersonaEmpresaRolVend ? explode('|', $strIdPersonaEmpresaRolVend) : array();
        $strNombreArea               = $objRequest->request->get("strNombreArea") ? $objRequest->request->get("strNombreArea") : '';
        $strEstadoEliminado          = 'Eliminado';
        $strEstadoActivo             = 'Activo';
        $arrayParametrosCarac        = array( 'descripcionCaracteristica' => 'ASISTENTE_POR_CARGO',
                                              'estado'                    => $strEstadoActivo );
        $emComercial->getConnection()->beginTransaction();

        if( $strNombreArea == "Comercial" && $strPrefijoEmpresa == "TN")
        {
            try
            {
                $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy($arrayParametrosCarac);
                foreach( $arrayPersonaEmpresaRolVend as $intIdPersonaEmpresaRolVend)
                {
                    if( !empty($intIdPersonaEmpresaRolVend) && $intIdPersonaEmpresaRolVend != $intIdPersonaEmpresaRolAsist )
                    {
                        $entityEmpleado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRolVend);
                        $entityAsist    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRolAsist);

                        $arrayParametrosVend = array( 'estado'              => $strEstadoActivo,
                                                      'valor'               => $entityEmpleado->getPersonaIdValor(),
                                                      'caracteristicaId'    => $objAdmiCaracteristica,
                                                      'personaEmpresaRolId' => $entityAsist->getId());

                        $arrayInfoPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                       ->findBy($arrayParametrosVend);

                        $arrayInfoAsignacion = $emComercial->getRepository('schemaBundle:InfoAsignacion')
                                                           ->findBy(array( 'estado'                   => $strEstadoActivo,
                                                                           'personaEmpresaRolIdVend'  => $intIdPersonaEmpresaRolVend,
                                                                           'personaEmpresaRolIdAsist' => $intIdPersonaEmpresaRolAsist));
                        if( !empty($arrayInfoAsignacion) )
                        {
                            foreach($arrayInfoAsignacion as $objInfoAsignacion)
                            {
                                $objInfoAsignacion->setFeUltMod($objDatetimeActual);
                                $objInfoAsignacion->setUsrUltMod($strUserSession);
                                $objInfoAsignacion->setEstado($strEstadoEliminado);
                                $objInfoAsignacion->setIpUltMod($strIpUserSession);

                                $emComercial->persist($objInfoAsignacion);
                                $emComercial->flush();
                            }
                        }
                        if( !empty($arrayInfoPersonaEmpresaRolCarac) )
                        {
                            foreach($arrayInfoPersonaEmpresaRolCarac as $objInfoPersonaEmpresaRolCarac)
                            {
                                $objInfoPersonaEmpresaRolCarac->setFeUltMod($objDatetimeActual);
                                $objInfoPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                                $objInfoPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);

                                $emComercial->persist($objInfoPersonaEmpresaRolCarac);
                                $emComercial->flush();
                            }
                        }
                        if ($emComercial->getConnection()->isTransactionActive())
                        {
                            $emComercial->getConnection()->commit();
                            $strResponse = 'OK';
                        }
                        /**
                         * Bloque que elimina la asistente en TelcoCRM.
                         */
                        if((!empty($entityEmpleado) && is_object($entityEmpleado)) &&
                           (!empty($entityAsist)    && is_object($entityAsist)))
                        {
                            $arrayParametrosCrm   = array("strLoginEmpleado"   => $entityEmpleado->getPersonaId()->getLogin(),
                                                          "strLoginAsistente"  => $entityAsist->getPersonaId()->getLogin(),
                                                          "strAccion"          => "ELIMINAR",
                                                          "strPrefijoEmpresa"  => $strPrefijoEmpresa,
                                                          "strCodEmpresa"      => $strCodEmpresa);
                            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametrosCrm,
                                                          "strOp"              => 'editAsistente',
                                                          "strFuncion"         => 'procesar');
                            $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                        }
                    }
                }
            }
            catch(Exception $e)
            {
                error_log($e->getMessage());
                if ($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->getConnection()->rollback();
                }
                $serviceUtil->insertError('TELCOS+',
                                        'administracionBundle.JefesController.getCambioVendedorAction',
                                        $e->getMessage(),
                                        $strUserSession,
                                        $strIpUserSession);
            }
        }
        if ($emComercial->getConnection()->isTransactionActive())
        {
            $emComercial->getConnection()->close();
        }
        return new Response($strResponse);
    }
    /**
     * Documentación para el método 'cambioJefeAction'.
     *
     * Cambia el id del jefe al cual reporta un empleado
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-08-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-10-2015 - Se cambia el método 'findPersonal' por 'findPersonalByCriterios'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 13-10-2015 - Se adapta la opción para las administración de jefes para el área técnica identificando si tiene
     *                           los perfiles correspondientes para acceder a la opción
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 18-11-2015 - Se verifica que se guarde el historial de las cuadrillas al eliminar un empleado, y que se desvincule la tablet
     *                           asociada a un empleado que es Lider o Jefe Cuadrilla. Adicional se verifica si tiene alguna característica de CARGO
     *                           creada para ser eliminada.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.4 14-12-2015 - Se modifica para que retorne el personal de acuerdo a la ciudad del usuario en sessión y los cargos del personal 
     *                           para el área Técnica a los cuales se les desea cambiar de jefe, y para ello no se envía el parámetro 'departamento' 
     *                           cuando se requiere realizar la búsqueda del personal.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 18-04-2017 - Se modifica la función para que en TN retorne el personal asignado del jefe anterior que se encuentre en los 
     *                           departamentos asignados en el parámetro 'GRUPO_DEPARTAMENTOS'
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.6 - 24-02-2021 - Se agrega lógica para sincronizar los cambio en TelcoCRM.
     *
     * @author Modificado: José Castillo <jmcastillo@telconet.ec>
     * @version 1.7 - 05-06-2023 - Se modifica la validación permitiendo que no solo se transfieran las cuadrillas en el cambio de 
     *                             responsable solo para Coordinadores sino tambien para Ayudante Coordinador.
     */   
    public function cambioJefeAction()
    {
        if( false === $this->get('security.context')->isGranted('ROLE_307-2838') &&
            false === $this->get('security.context')->isGranted('ROLE_296-2838') )
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strResponse        = 'ERROR';
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtilidades  = $this->get('administracion.Utilidades');
        $serviceTelcoCrm    = $this->get('comercial.ComercialCrm');

        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strIdPersonaEmpresaRol = $objRequest->request->get('strIdPersonaEmpresaRol') ? $objRequest->request->get('strIdPersonaEmpresaRol') : 0;
        $intIdNuevoJefe         = $objRequest->request->get('intIdJefe') ? $objRequest->request->get('intIdJefe') : null;
        $intIdCargoSeleccionado = $objRequest->request->get('intIdCargoSeleccionado') ? $objRequest->request->get('intIdCargoSeleccionado') : null;
        $strAccion              = $objRequest->request->get('strAccion') ? $objRequest->request->get('strAccion') : '';
        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento      = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strNombreArea          = $objRequest->request->get("strNombreArea") ? $objRequest->request->get("strNombreArea") : '';
        $strEstadoActivo        = 'Activo';
        $strEstadoEliminado     = 'Eliminado';

        $arrayPersonaEmpresaRol = array();
        
        if( $strNombreArea == 'Tecnico' )
        {
            $strMovitoCuadrilla = 'Se eliminan miembros de la cuadrilla';
            $objMotivoCuadrilla = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMovitoCuadrilla);
                        
            $intIdMotivoCuadrilla = null;
            
            if( $objMotivoCuadrilla )
            {
                $intIdMotivoCuadrilla = $objMotivoCuadrilla->getId();
            }
        }
        
        if( $strAccion == 'cambioJefeEmpleadosAsignados' )
        {
            $arrayTmpParametros = array(
                                            'usuario'      => $intIdPersonEmpresaRol,
                                            'empresa'      => $intIdEmpresa,
                                            'asignadosA'   => $strIdPersonaEmpresaRol
                                       );
            
            if( $strNombreArea == 'Comercial' )
            {
                $arrayTmpParametros['departamento'] = $intIdDepartamento;

                //SE VALIDA PARA LA EMPRESA TN QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
                if( $strPrefijoEmpresa == 'TN' )
                {
                    $arrayParametros['caracteristicaCargo'] = self::CARACTERISTICA_CARGO_GRUPO_ROLES;

                    $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                          'strValorRetornar'  => 'valor1',
                                                          'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                          'strNombreModulo'   => 'COMERCIAL',
                                                          'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                          'strValor2Detalle'  => 'COMERCIAL',
                                                          'strUsrCreacion'    => $strUserSession,
                                                          'strIpCreacion'     => $strIpUserSession);

                    $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

                    if( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                    {
                        $arrayTmpParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
                    }//( isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']) )
                }//( $strPrefijoEmpresa == 'TN' )
            }
            elseif( $strNombreArea == 'Tecnico' )
            {
                $arrayCargos = array();
                $objCargos   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                    self::PARAMETRO_CARGOS_TECNICOS, 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    'Personal Tecnico', 
                                                                                                    '',
                                                                                                    '', 
                                                                                                    ''
                                                                                                 );

                if( $objCargos )
                {
                    foreach($objCargos as $objCargoTecnico)
                    {
                        $arrayCargos[] = $objCargoTecnico['descripcion'];

                    }//foreach($objCargosSuperiores as $objCargoSuperior)
                }//( $objCargosSuperiores )

                $arrayTmpParametros['criterios']['cargoSimilar'] = $arrayCargos;
            }//( $strNombreArea == 'Tecnico' )
            
            $arrayTmpResultados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonalByCriterios($arrayTmpParametros);

            if( $arrayTmpResultados )
            {
                $arrayTmpPersonalAsignado = array();
                $arrayTmpPersonalAsignado = $arrayTmpResultados['registros'];
                
                if( $arrayTmpPersonalAsignado )
                {
                    foreach($arrayTmpPersonalAsignado as $arrayPersonalAsignado)
                    {
                        $arrayPersonaEmpresaRol[] = $arrayPersonalAsignado['idPersonaEmpresaRol'];
                    }
                }
            }
        }
        else
        {
            $arrayPersonaEmpresaRol = explode('|', $strIdPersonaEmpresaRol);
        }  

        $emComercial->getConnection()->beginTransaction();

        try
        {
            foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)
            {
                if( $intIdPersonaEmpresaRol )
                {
                    $entityEmpleado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRol);

                    if( $entityEmpleado )
                    {
                        $intIdCuadrillaActual = $entityEmpleado->getCuadrillaId();
                        
                        if( $strAccion == 'cambioJefeEmpleadosAsignados' )
                        {
                            $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                   ->findOneByNombreMotivo('Cambio de Jefe a Empleados Asignados');
                        }
                        else
                        {
                            $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo('Cambio de Jefe');
                        }
                        
                        $intIdJefeAnterior = $entityEmpleado->getReportaPersonaEmpresaRolId() ? $entityEmpleado->getReportaPersonaEmpresaRolId() : 0;

                        if( $intIdNuevoJefe != $entityEmpleado->getId() )
                        {
                            $entityEmpleado->setReportaPersonaEmpresaRolId($intIdNuevoJefe);
                        }
                        else
                        {
                            $entityEmpleado->setReportaPersonaEmpresaRolId(null);
                        }
                        
                        if( $strNombreArea == 'Tecnico' && $strAccion != 'cambioJefeEmpleadosAsignados' )
                        {
                            $objTmpCuadrillaAnterior    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                      ->findOneById($intIdCuadrillaActual);
                            $strCodigoCuadrillaAnterior = $objTmpCuadrillaAnterior ? $objTmpCuadrillaAnterior->getCodigo() : '';
                            
                            $strObservacion = 'Cuadrilla anterior: '.$strCodigoCuadrillaAnterior;
                            
                            $entityEmpleado->setCuadrillaId(null);
                            
                            $entityPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                            $entityPersonaEmpresaRolHisto->setEstado($entityEmpleado->getEstado());
                            $entityPersonaEmpresaRolHisto->setFeCreacion($datetimeActual);
                            $entityPersonaEmpresaRolHisto->setIpCreacion($strIpUserSession);
                            $entityPersonaEmpresaRolHisto->setUsrCreacion($strUserSession);
                            $entityPersonaEmpresaRolHisto->setPersonaEmpresaRolId($entityEmpleado);
                            $entityPersonaEmpresaRolHisto->setObservacion($strObservacion);
                            $entityPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
                            
                            $emComercial->persist($entityPersonaEmpresaRolHisto);
                            
                            
                            /*
                             * Bloque que guarda el historial de la cuadrilla
                             */
                            $strObservacionHistoCuadrilla = 'Se eliminan los siguientes miembros de la cuadrilla:<br/>';
                            $strNombreUsuario             = $entityEmpleado->getPersonaId() ? 
                                                            $entityEmpleado->getPersonaId()->getInformacionPersona() : '';
                            $strObservacionHistoCuadrilla .= $strNombreUsuario.'<br/>';
                            
                            $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($intIdCuadrillaActual);
                            
                            if( $objCuadrilla )
                            {
                                $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                                $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
                                $objCuadrillaHistorial->setEstado($strEstadoActivo);
                                $objCuadrillaHistorial->setFeCreacion($datetimeActual);
                                $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                                $objCuadrillaHistorial->setObservacion($strObservacionHistoCuadrilla);
                                $objCuadrillaHistorial->setMotivoId($intIdMotivoCuadrilla);
                                $emComercial->persist($objCuadrillaHistorial);
                            }//( $objCuadrilla )
                            /*
                             * Fin del Bloque que guarda el historial de la cuadrilla
                             */
                            
                            
                            /*
                             * Bloque que elimina las caracteristicas de cargo asignados y si está prestado el empleado
                             */
                            $arrayTmpCaracteristicas = array(self::CARACTERISTICA_CARGO, self::CARACTERISTICA_PRESTAMO_EMPLEADO);
                            
                            foreach( $arrayTmpCaracteristicas as $strDescripcionCaracteristica )
                            {
                                $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy( array( 'descripcionCaracteristica' => $strDescripcionCaracteristica,
                                                                                     'estado'                    => $strEstadoActivo ) );

                                $arrayTmpParametrosCargo = array( 
                                                                    'estado'              => $strEstadoActivo,
                                                                    'personaEmpresaRolId' => $entityEmpleado,
                                                                    'caracteristicaId'    => $objCaracteristica
                                                                );

                                $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                         ->findBy($arrayTmpParametrosCargo);

                                if( $objPersonaEmpresaRolCarac )
                                {
                                    foreach( $objPersonaEmpresaRolCarac as $objCaracteristicaCargo)
                                    {
                                        $objCaracteristicaCargo->setFeUltMod($datetimeActual);
                                        $objCaracteristicaCargo->setUsrUltMod($strUserSession);
                                        $objCaracteristicaCargo->setEstado($strEstadoEliminado);

                                        $emComercial->persist($objCaracteristicaCargo);
                                        $emComercial->flush();
                                    }//foreach( $objPersonaEmpresaRolCarac as $objCaracteristicaCargo)
                                }//( $objPersonaEmpresaRolCarac )
                            }//foreach( $arrayTmpCaracteristicas as $strDescripcionCaracteristica )
                            /*
                             * Fin del Bloque que elimina las caracteristicas de cargo asignados y si está prestado el empleado
                             * /
                            
                            /* 
                             * Bloque que desasocia una tablet con el personal asignado a una cuadrilla
                             */
                            $arrayTmpParametrosTablet = array( 'estado'        => $strEstadoActivo, 
                                                               'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_TABLET, 
                                                               'detalleValor'  => $intIdPersonaEmpresaRol );

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
                                $objInfoPersonaEmpresaRolHistorial->setEstado($entityEmpleado->getEstado());
                                $objInfoPersonaEmpresaRolHistorial->setFeCreacion($datetimeActual);
                                $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpUserSession);
                                $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($entityEmpleado);
                                $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUserSession);
                                $objInfoPersonaEmpresaRolHistorial->setObservacion($strMensajeObservacion);
                                $objInfoPersonaEmpresaRolHistorial->setMotivoId($intIdMotivoTablet);
                                $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                                $emComercial->flush();
                            }//( $objDetalleTablet )
                            /*
                             * Fin del Bloque que desasocia una tablet con el personal asignado a una cuadrilla
                             */
                        }//( $strNombreArea == 'Tecnico' && $strAccion == 'cambioJefeEmpleadosAsignados' )

                        
                        $strObservacion = 'Jefe Anterior: ';

                        if( $intIdJefeAnterior )
                        {
                            $objInfoJefeActual   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->findOneById($intIdJefeAnterior);
                            $strNombreJefeActual = $objInfoJefeActual->getPersonaId() 
                                                   ? $objInfoJefeActual->getPersonaId()->getInformacionPersona() : '';
                            
                            $strObservacion .= $strNombreJefeActual;
                        }//( $intIdJefeAnterior )

                        $entityPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaEmpresaRolHisto->setEstado($entityEmpleado->getEstado());
                        $entityPersonaEmpresaRolHisto->setFeCreacion($datetimeActual);
                        $entityPersonaEmpresaRolHisto->setIpCreacion($strIpUserSession);
                        $entityPersonaEmpresaRolHisto->setUsrCreacion($strUserSession);
                        $entityPersonaEmpresaRolHisto->setPersonaEmpresaRolId($entityEmpleado);
                        $entityPersonaEmpresaRolHisto->setObservacion($strObservacion);
                        $entityPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());

                        $emComercial->persist($entityEmpleado);
                        $emComercial->persist($entityPersonaEmpresaRolHisto);
                        $emComercial->flush();
                        /**
                         * Bloque que actualiza el jefe en TelcoCRM.
                         */
                        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" &&
                           !empty($intIdJefeAnterior) && !empty($intIdNuevoJefe))
                        {
                            $entityJefeAnterior = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                              ->findOneById($intIdJefeAnterior);
                            $entityJefeNuevo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                              ->findOneById($intIdNuevoJefe);
                            if((!empty($entityJefeAnterior) && is_object($entityJefeAnterior))&&
                               (!empty($entityJefeNuevo)    && is_object($entityJefeNuevo))   &&
                               (!empty($entityEmpleado)     && is_object($entityEmpleado)))
                            {
                                $arrayParametrosCrm   = array("strLoginEmpleado"     => $entityEmpleado->getPersonaId()->getLogin(),
                                                              "strLoginJefeAnterior" => $entityJefeAnterior->getPersonaId()->getLogin(),
                                                              "strLoginJefeNuevo"    => $entityJefeNuevo->getPersonaId()->getLogin(),
                                                              "strPrefijoEmpresa"    => $strPrefijoEmpresa, 
                                                              "strCodEmpresa"        => $intIdEmpresa);
                                $arrayParametrosWSCrm = array("arrayParametrosCRM"   => $arrayParametrosCrm,
                                                              "strOp"                => 'editJefe',
                                                              "strFuncion"           => 'procesar');
                                $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                            }
                        }
                        /**
                         * Bloque que elimina las caracteristicas asociadas al empleado con respecto al cargo asignado en TELCOS+ para agregarle el
                         * nuevo cargo seleccionado por el usuario en session.
                         */
                        if( $strNombreArea == "Comercial" && $strPrefijoEmpresa == "TN" && ($intIdCargoSeleccionado > 0 || $strAccion=='Eliminar') )
                        {
                            $arrayParametrosCaracteristicas = array( 'descripcionCaracteristica' => self::CARACTERISTICA_CARGO_GRUPO_ROLES,
                                                                     'estado'                    => $strEstadoActivo );
                            
                            $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy($arrayParametrosCaracteristicas);
                            
                            $arrayParametrosCargos = array( 'estado'              => $strEstadoActivo,
                                                            'personaEmpresaRolId' => $entityEmpleado,
                                                            'caracteristicaId'    => $objCaracteristica );
                            
                            $arrayInfoPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                           ->findBy($arrayParametrosCargos);
                            
                            if( !empty($arrayInfoPersonaEmpresaRolCarac) )
                            {
                                foreach($arrayInfoPersonaEmpresaRolCarac as $objInfoPersonaEmpresaRolCarac)
                                {
                                    $objInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                                    $objInfoPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                                    $objInfoPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);

                                    $emComercial->persist($objInfoPersonaEmpresaRolCarac);
                                    $emComercial->flush();
                                }//foreach($arrayInfoPersonaEmpresaRolCarac as $objInfoPersonaEmpresaRolCarac)
                            }//( !empty($arrayInfoPersonaEmpresaRolCarac) )
                            
                            if( $intIdCargoSeleccionado > 0 )
                            {
                                $objPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                                $objPersonaEmpresaRolCaracNew->setEstado($strEstadoActivo);
                                $objPersonaEmpresaRolCaracNew->setFeCreacion(new \DateTime('now'));
                                $objPersonaEmpresaRolCaracNew->setIpCreacion($strIpUserSession);
                                $objPersonaEmpresaRolCaracNew->setUsrCreacion($strUserSession);
                                $objPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($entityEmpleado);
                                $objPersonaEmpresaRolCaracNew->setCaracteristicaId($objCaracteristica);
                                $objPersonaEmpresaRolCaracNew->setValor($intIdCargoSeleccionado);

                                $emComercial->persist($objPersonaEmpresaRolCaracNew);
                                $emComercial->flush();
                            }//( $intIdCargoSeleccionado > 0 )
                        }//( $strNombreArea == "Comercial" && $strPrefijoEmpresa == "TN" && ($intIdCargoSeleccionado > 0 || $strAccion=='Eliminar') )
                    }//( $entityEmpleado )
                }//( $intIdPersonaEmpresaRol )
            }//foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)
            

            /*
             * Se hace una validación adicional que si el cambio de jefe viene por el área técnica las cuadrillas que tiene asociado
             * el empleado seleccionado también cambien al nuevo Jefe.
             */
            if( $strNombreArea == 'Tecnico' && $strAccion == 'cambioJefeEmpleadosAsignados' )
            {
                $strCargo = $emComercial->getRepository('schemaBundle:AdmiRol')->getRolEmpleadoEmpresa(array('usuario' => $strIdPersonaEmpresaRol));
        
                $intPos = strpos(strtolower($strCargo), 'coord');
                $intPosAyudante = strpos(strtolower($strCargo), 'ayudante coordinador');
                
                if($intPos === 0 || $intPosAyudante === 0 )
                {
                    $strLogin = '';
                    
                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findOneById($strIdPersonaEmpresaRol);
                    
                    if($objInfoPersonaEmpresaRol)
                    {
                        $strLogin = $objInfoPersonaEmpresaRol->getPersonaId()->getLogin();
                    }

                    $arrayParametrosCuadrillas = array(
                                                          'strUsrCreacion'          => trim($strLogin), 
                                                          'intCoordinadorPrincipal' => $strIdPersonaEmpresaRol,
                                                          'criterios'               => array( 'estado' => $strEstadoActivo )
                                                      );

                    $arrayCuadrillasActivas = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                          ->getCuadrillasByCriterios($arrayParametrosCuadrillas);

                    $arrayRegistros = $arrayCuadrillasActivas['registros'];
        
                    if($arrayRegistros)
                    {
                        foreach($arrayRegistros as $objDato)
                        {
                            $objCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($objDato->getId());
        
                            if( $objCuadrilla )
                            {
                                /*
                                 * Informacion de la Cuadrilla Anterior
                                 */
                                $objCoordinadorAnterior  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                     ->findOneById($objCuadrilla->getCoordinadorPrincipalId());

                                $strNombreCoordinadorPrincipalAnterior = "";

                                if($objCoordinadorAnterior)
                                {
                                    $strNombreCoordinadorPrincipalAnterior = $objCoordinadorAnterior->getPersonaId() 
                                                                             ? $objCoordinadorAnterior->getPersonaId()->getInformacionPersona() : '';
                                }
                                /*
                                 * Fin Informacion de la Cuadrilla Anterior
                                 */

                                $objCuadrilla->setCoordinadorPrincipalId($intIdNuevoJefe);
                                $objCuadrilla->setFeUltMod($datetimeActual);
                                $objCuadrilla->setUsrModificacion($strUserSession);
                                $emComercial->persist($objCuadrilla);
                                $emComercial->flush();

                                /*
                                 * Informacion de la Cuadrilla Nueva
                                 */
                                $objCoordinadorNuevo  = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->findOneById($objCuadrilla->getCoordinadorPrincipalId());

                                $strNombreCoordinadorPrincipalNuevo = "";

                                if($objCoordinadorNuevo)
                                {
                                    $strNombreCoordinadorPrincipalNuevo = $objCoordinadorNuevo->getPersonaId() 
                                                                          ? $objCoordinadorNuevo->getPersonaId()->getInformacionPersona() : '';
                                }
                                /*
                                 * Fin Informacion de la Cuadrilla Nueva
                                 */


                                $strObservacionHistoCuadrilla = "Edici&oacute;n de cuadrilla:<br/><br/>
                                                                 <b>Datos Anteriores</b>
                                                                 Coordinador Principal: ".$strNombreCoordinadorPrincipalAnterior."<br/><br/>
                                                                 <b>Datos Nuevos</b>
                                                                 Coordinador Principal: ".$strNombreCoordinadorPrincipalNuevo."<br/>";
                                

                                
                                $objMotivoCuadrilla = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                ->findOneByNombreMotivo('Cambio de Jefe a Empleados Asignados');
                                
                                $objCuadrillaHistorial = new AdmiCuadrillaHistorial();
                                $objCuadrillaHistorial->setCuadrillaId($objCuadrilla);
                                $objCuadrillaHistorial->setEstado($objCuadrilla->getEstado());
                                $objCuadrillaHistorial->setFeCreacion($datetimeActual);
                                $objCuadrillaHistorial->setUsrCreacion($strUserSession);
                                $objCuadrillaHistorial->setObservacion($strObservacionHistoCuadrilla);
                                $objCuadrillaHistorial->setMotivoId($objMotivoCuadrilla->getId());
                                $emComercial->persist($objCuadrillaHistorial);
                                $emComercial->flush();
                                
                            }//( $objCuadrilla )
                        }//($arrayRegistros as $arrayDato)
                    }//($arrayRegistros)
                }//($intPos === 0)
            }//( $strNombreArea == 'Tecnico' && $strAccion == 'cambioJefeEmpleadosAsignados')

            
            $strResponse = 'OK';
            
            $emComercial->getConnection()->commit();
            $emComercial->getConnection()->close();		
        }
        catch(Exception $e)
        {
            error_log($e->getMessage());

            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();			
        }
            
        return new Response($strResponse);
    }
    
    
    /**
     * Documentación para el método 'asignarEmpleadosAction'.
     *
     * Pantalla para asignar empleados a un jefe específico.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-08-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 07-10-2015 - Se adapta la opción para las administración de jefes para el área técnica usando el parámetro
     *                           '$strNombreArea'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 18-11-2015 - Se adapta la opción para mostrar el cargo del Telcos y del NAF en la pantalla de asignar empleados
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 27-03-2017 - Se envía el parámetro '$strPrefijoEmpresa' para validar que para TN no se deben mostrar las características de
     *                           'Meta Activa' y 'Meta Bruta' del Jefe seleccionado al cual se le asignará empleados. Adicional se obtiene el id del
     *                           cargo asignado en TELCOS+ mediante la variable '$intIdCargoTelcos' para ser enviada al twig. Además se consulta el
     *                           id del cargo vendedor que será asignado a los empleados seleccionados por el usuario.
     */
    public function asignarEmpleadosAction( )
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $strNombreJefe          = '';
        $strNombreReportaA      = 'Sin Asignación';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $intIdPersonaEmpresaRol = $objRequest->request->get('itemIntIdPersonaEmpresaRol') 
                                  ? $objRequest->request->get('itemIntIdPersonaEmpresaRol') : '';
        $intIdCargoTelcos       = $objRequest->request->get('itemIntIdCargoTelcos') ? $objRequest->request->get('itemIntIdCargoTelcos') : 0;
        $strCargo               = $objRequest->request->get('itemStrCargo') ? $objRequest->request->get('itemStrCargo') : '';
        $strCargoNaf            = $objRequest->request->get('itemStrCargoNaf') ? $objRequest->request->get('itemStrCargoNaf') : '';
        $strMetaBruta           = $objRequest->request->get('itemStrMetaBruta') ? $objRequest->request->get('itemStrMetaBruta') : '0';     
        $strMetaActiva          = $objRequest->request->get('itemStrMetaActiva') ? $objRequest->request->get('itemStrMetaActiva') : '';     
        $intMetaActivaValor     = $objRequest->request->get('itemIntMetaActivaValor') ? $objRequest->request->get('itemIntMetaActivaValor') : 0;     
        $strNombreArea          = $objRequest->request->get('itemStrNombreArea') ? $objRequest->request->get('itemStrNombreArea') : '';
        $strUsuarioCreacion     = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp() : '127.0.0.1';
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $strEsAsistente         = $objRequest->request->get('itemStrEsAsistente') ? $objRequest->request->get('itemStrEsAsistente') : '';
        $intIdCargoVendedor     = 0;

        if( false === $this->get('security.context')->isGranted('ROLE_307-2839') &&
            false === $this->get('security.context')->isGranted('ROLE_296-2839') )
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        
        $entityJefe = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRol);
        
        if( $entityJefe )
        {
            $strNombresJefe   = ucwords(strtolower(trim($entityJefe->getPersonaId()->getNombres())));
            $strApellidosJefe = ucwords(strtolower(trim($entityJefe->getPersonaId()->getApellidos())));
            $strNombreJefe    = $strNombresJefe.' '.$strApellidosJefe;
                        
            $intIdReportaA = $entityJefe->getReportaPersonaEmpresaRolId() ? $entityJefe->getReportaPersonaEmpresaRolId() : 0;
            
            if( $intIdReportaA )
            {
                $objReportaA = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdReportaA);

                if( $objReportaA )
                {
                    $strNombresDeReportaA   = ucwords(strtolower(trim($objReportaA->getPersonaId()->getNombres())));
                    $strApellidosDeReportaA = ucwords(strtolower(trim($objReportaA->getPersonaId()->getApellidos())));

                    $strNombreReportaA = $strNombresDeReportaA.' '.$strApellidosDeReportaA;
                }
            }
        }
        
        //SE VALIDA PARA LA EMPRESA TN QUE SE ENVIE AL TWIG EL CARGO DE VENDEDOR PARA SER ASIGNADO AL PERSONAL SELECCIONADO POR EL USUARIO
        if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
        {
            $arrayParametrosCargosJefes = array('strCodEmpresa'     => $strCodEmpresa,
                                                'strValorRetornar'  => 'id',
                                                'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                'strNombreModulo'   => 'COMERCIAL',
                                                'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                'strValor3Detalle'  => 'VENDEDOR',
                                                'strValor4Detalle'  => 'NO_JEFE',
                                                'strUsrCreacion'    => $strUsuarioCreacion,
                                                'strIpCreacion'     => $strIpCreacion);

            $arrayResultadosCargoVendedor = $serviceUtilidades->getDetallesParametrizables($arrayParametrosCargosJefes);

            if( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']) 
                && isset($arrayResultadosCargoVendedor['intTotal']) && $arrayResultadosCargoVendedor['intTotal'] == 1 )
            {
                $intIdCargoVendedor = $arrayResultadosCargoVendedor['resultado'][0];
            }//( isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado'])...
        }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN' )
        
        return $this->render( 'administracionBundle:Jefes:asignarEmpleados.html.twig',
                              array(
                                        'intIdJefeSeleccionado'       => $intIdPersonaEmpresaRol,
                                        'nombreJefe'                  => $strNombreJefe,
                                        'nombreCargo'                 => $strCargo,
                                        'nombreReportaA'              => $strNombreReportaA,
                                        'metaBruta'                   => $strMetaBruta,
                                        'metaActiva'                  => $strMetaActiva,
                                        'metaActivaValor'             => $intMetaActivaValor,
                                        'strNombreArea'               => $strNombreArea,
                                        'strCargoNaf'                 => $strCargoNaf,
                                        'strPrefijoEmpresa'           => $strPrefijoEmpresa,
                                        'strCaracteristicaMetaBruta'  => self::CARACTERISTICA_META_BRUTA,
                                        'strCaracteristicaMetaActiva' => self::CARACTERISTICA_META_ACTIVA,
                                        'intIdCargoTelcos'            => $intIdCargoTelcos,
                                        'intIdCargoVendedor'          => $intIdCargoVendedor,
                                        'strEsAsistente'              => $strEsAsistente
                                   ) 
                            );
    }
    
    
    /**
     * Documentación para el método 'getCargosEmpleadosAction'.
     *
     * Consulta los cargos habilitados para los usuarios
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 28-08-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 07-10-2015 - Se adapta la opción para las administración de jefes para el área técnica usando el parámetro
     *                           '$strNombreArea'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 25-11-2015 - Se adapta la opción para que verifique si el cargo de 'LIDER' o 'CHOFER' ya ha sido asignado a algún miembro de
     *                           la cuadrilla anteriormente.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 13-04-2016 - Se corrige para que se muestren los cargos visibles para asignar a un personal del área comercial.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 13-03-2017 - Para TN se debe obtener los cargos respectivos del parámetro 'GRUPO_ROLES_PERSONAL'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 18-05-2017 - Para TN se debe obtener los cargos respectivos del parámetro 'GRUPO_ROLES_PERSONAL' sólo cuando se envía como nombre
     *                           de área diferente de 'Tecnico'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 12-06-2017 - Se corrige para que el valor de '$strEsVisible' se use solo para la empresa MD cuando el nombre de area es
     *                           'COMERCIAL'
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.7 05-11-2020 - Se agrega lógica para mostrar diferentes cargos a cambiar.
     * 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.8 07-06-2021 - Se ordena el arreglo que retorna los cargos.
     *
     * 
     */   
    public function getCargosEmpleadosAction()
    {
        $response       = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $arrayRegistros = array();
        $intTotal       = 0;
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');

        $strCargo                = $objRequest->query->get("strCargo") ? $objRequest->query->get("strCargo") : '';
        $strEsVisible            = $objRequest->query->get("strEsVisible") ? $objRequest->query->get("strEsVisible") : '';
        $strEstadoParametroCargo = 'Activo';
        $strNombreArea           = $objRequest->query->get("strNombreArea") ? $objRequest->query->get("strNombreArea") : '';
        $intIdCuadrilla          = $objRequest->query->get("intIdCuadrilla") ? $objRequest->query->get("intIdCuadrilla") : 0;
        $strCargoChoferNoVisible = $objRequest->query->get("strCargoChoferNoVisible") ? $objRequest->query->get("strCargoChoferNoVisible") : '';
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strNombreParametroCab   = self::PARAMETRO_CARGO;
        $strCargosJefes          = $objRequest->query->get("strCargosJefes") ? $objRequest->query->get("strCargosJefes") : '';

        
        if( false === $this->get('security.context')->isGranted('ROLE_307-2840') &&
            false === $this->get('security.context')->isGranted('ROLE_296-2840') )
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        
        if( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" && $strNombreArea != 'Tecnico' )
        {
            $strNombreParametroCab = "GRUPO_ROLES_PERSONAL";
        }//( !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" )
        
        $entityTmpParametro = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneByNombreParametro( 
                                                                    array(
                                                                            'nombreParametro' => $strNombreParametroCab,
                                                                            'estado'          => $strEstadoParametroCargo
                                                                         )
                                                                   );
        $intIdParametroCargo = 0;

        if( $entityTmpParametro )
        {
            $intIdParametroCargo = $entityTmpParametro->getId();
        }

        $arrayParametros  = array(
                                    'estado'      => $strEstadoParametroCargo,
                                    'parametroId' => $intIdParametroCargo,
                                    'valor4'      => $strCargosJefes
                                 );
        
        if( $strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN" )
        {
            $arrayParametros['valor3'] = strtoupper($strNombreArea);
        }
        
        if( $strNombreArea == 'Comercial' )
        {
            if( $strEsVisible && ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") )
            {
                $arrayParametros['valor1'] = $strEsVisible;
            }

            $arrayResultados  = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->getParametrosByCriterios($arrayParametros);
            sort($arrayResultados['registros']);
            if( $arrayResultados['registros'] )
            {
                foreach($arrayResultados['registros'] as $arrayDato)
                {
                    $strDescripcion = ucwords(strtolower($arrayDato['descripcion']));

                    if( $strDescripcion != $strCargo )
                    {
                        $arrayRegistros[] = array( 'intIdCargo' => $arrayDato['id'], 'strNombreCargo' => str_replace("_"," ",$strDescripcion) );
                        
                        $intTotal++;
                    }//( $strDescripcion != $strCargo )
                }//foreach($arrayResultados['registros'] as $arrayDato)
            }//( $arrayResultados['registros'] )
        }//( $strNombreArea == 'Comercial' )
        elseif( $strNombreArea == 'Tecnico' )
        {
            if( $strEsVisible == 'SI')
            {
                if($strCargoChoferNoVisible=='S')
                {
                    $arrayParametros['cargosNoVisibles']            = array('CHOFER');
                }
                
                if( $strEsVisible )
                {
                    $arrayParametros['valor4'] = $strEsVisible;
                }
                
                if( $intIdCuadrilla )
                {
                    $arrayParametros['noCargosAsignadosACuadrilla'] = 'S';
                    $arrayParametros['cuadrilla']                   = $intIdCuadrilla;

                    if($strCargo == 'Lider')
                    {
                        $arrayParametros['cargosCuadrilla']             = array('Chofer', 'Lider');  
                    }
                    elseif($strCargo == 'Operativo')
                    {
                        $arrayParametros['cargosCuadrilla']             = array('Chofer', 'Operativo');  
                    }
                }

                $arrayResultados  = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->getParametrosByCriterios($arrayParametros);

                if( $arrayResultados['registros'] )
                {
                    foreach($arrayResultados['registros'] as $arrayDato)
                    {
                        $strDescripcion   = ucwords(strtolower($arrayDato['descripcion']));
                        $arrayRegistros[] = array( 'intIdCargo' => $arrayDato['id'], 'strNombreCargo' => $strDescripcion );

                        $intTotal++;
                    }//foreach($arrayResultados as $objDato)
                }//( $arrayResultados )
            }
            else
            {
                $arrayTmpParametros = array('soloJefes' => 'S');
                $arrayResultados    = $emComercial->getRepository('schemaBundle:AdmiRol')->getRolesPersonalCuadrillas($arrayTmpParametros);

                if( $arrayResultados['resultados'] )
                {
                    foreach($arrayResultados['resultados'] as $arrayDato)
                    {
                        $strDescripcion = ucwords(strtolower($arrayDato['descripcionRol']));

                        $arrayRegistros[] = array(
                                                    'intIdCargo'     => $arrayDato['id'],
                                                    'strNombreCargo' => $strDescripcion
                                                 );
                        $intTotal++;
                    }
                }
            
                $intTotal = $arrayResultados['total'];
            }//( $strEsVisible == 'SI')
        }//( $strNombreArea == 'Tecnico' )
            
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayRegistros) );
        
        return $response;
    }
    
    
    /**
     * Documentación para el método 'asignarCaracteristicaAction'.
     *
     * Guarda el valor asignado al empleado dependiendo de la característica, que puede ser Meta o Cargo
     *
     * @return JsonResponse 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 28-08-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 07-10-2015 - Se adapta la opción para las administración de jefes para el área técnica usando el parámetro
     *                           '$strNombreArea'
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-11-2015 - Se modifica para que al buscar en la tabla 'InfoPersonaEmpresaRolCarac' se envíe como objetos los parámetros de
     *                           'caracteristicaId' y 'personaEmpresaRolId'.
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.3 11-12-2015 - Se modifica para que cuando le cambien de cargo a un integrante de la cuadrilla se elimine la Tablet que tiene
     *                           asociado dicho empleado.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 12-04-2017 - Se modifica la función para asignar o desasociar al GERENTE DE PRODUCTO de un producto cuando se envía como acción 
     *                           'eliminarGerenteProducto' o 'asignarGerenteProducto'.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.5 05-11-2020 - Se modifica la función para realizar cambios de lideres en cuadrillas. 
     */   
    public function asignarCaracteristicaAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strResponse            = 'ERROR';
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");  
        $objInfoPersonaService  = $this->get('comercial.InfoPersona');
        $serviceSoporte         = $this->get('soporte.SoporteService');


        $strIdPersonaEmpresaRol     = $objRequest->request->get('intIdPersonaEmpresaRol') ? $objRequest->request->get('intIdPersonaEmpresaRol') : 0;
        $strCaracteristica          = $objRequest->request->get('strCaracteristica') ? $objRequest->request->get('strCaracteristica') : 0;
        $strUserSession             = $objSession->get('user');
        $strIpUserSession           = $objRequest->getClientIp();
        $datetimeActual             = new \DateTime('now');
        $strValor                   = $objRequest->request->get('strValor') ? $objRequest->request->get('strValor') : '';
        $strAccion                  = $objRequest->request->get('strAccion') ? $objRequest->request->get('strAccion') : 'Guardar';
        $strNombreArea              = $objRequest->request->get('strNombreArea') ? $objRequest->request->get('strNombreArea') : '';
        $arrayEmpleadosAsignados    = $objRequest->request->get('arrayEmpleadosAsignados') ? 
                                        json_decode($objRequest->request->get('arrayEmpleadosAsignados')) : array();
        $strPerfilTecnicoCuadrillas = 'tecnicoCuadrillas';
        $strEstadoActivo            = 'Activo';
        $strEstadoEliminado         = 'Eliminado';
        
        $serviceUtil                    = $this->get('schema.Util');
        $strCaracteristicaCargoProducto = $objRequest->request->get('strCaracteristicaCargoProducto') 
                                          ? $objRequest->request->get('strCaracteristicaCargoProducto') : '';
        $strGrupoProducto               = $objRequest->request->get('strGrupoProducto') ? $objRequest->request->get('strGrupoProducto') : '';
        
        if( false === $this->get('security.context')->isGranted('ROLE_307-2841') &&
            false === $this->get('security.context')->isGranted('ROLE_296-2841') &&
            false === $this->get('security.context')->isGranted('ROLE_310-3057') )
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                                                                            'mensaje' => 'No tiene Usuario y Credenciales para '.
                                                                                                         'utilizar el Telcos +. Favor Solicitarlos '.
                                                                                                         'a sistemas@telconet.ec'
                                                                                       )
                                );
        }
        
        $arrayPersonaEmpresaRol = explode('|', $strIdPersonaEmpresaRol);
        $arrayCaracteristicas   = explode('|', $strCaracteristica);
        $arrayValores           = explode('|', $strValor);
        
        $emComercial->getConnection()->beginTransaction();

        try
        {
            $arrayInfoDetalleAsignacion = array();
            $arrayInfoEmpresaRolCarac   = array();

            if($strCaracteristica == 'CARGO' && $strValor == 'Lider')
            {
                $arrayRequestGetLider = array('personaEmpresaRolId' => $strIdPersonaEmpresaRol);

                $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                ->getLiderCuadrillaByPersonaEmpresaRol($arrayRequestGetLider);

                if (!empty($arrayInfoEmpresaRolCarac) && $arrayInfoEmpresaRolCarac['status'] === 'OK')
                {
                    $arrayRequestdetalleAsignacion = array(
                        'personaEmpresaRolId'   => $arrayInfoEmpresaRolCarac['result'][0]['personaEmpresaRolId']
                    );

                    $arrayInfoDetalleAsignacion = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                        ->getDetalleAsignacionByPersonaEmpresaRol($arrayRequestdetalleAsignacion);
                }

                for ($intIteracion = 0; $intIteracion < count($arrayEmpleadosAsignados); $intIteracion++) 
                {    
                    $strCargoEmpleado = $arrayEmpleadosAsignados[$intIteracion]->strCargoTelcos;

                    if($strCargoEmpleado == 'Lider')
                    {
                        $arrayParametrosCambioCargo = array(
                            'intIdPersonaEmpresaRol' => $arrayEmpleadosAsignados[$intIteracion]->intIdPersonaEmpresaRol,
                            'strCaracteristica' => 'CARGO',
                            'strValor'          => 'Operativo',
                            'strAccion'         => 'Guardar',
                            'strNombreArea'     => 'Tecnico',
                            'user'              => $strUserSession,
                            'clientIp'          => $strIpUserSession
                        );

                        $objInfoPersonaService->cambiarCargoTelcos($arrayParametrosCambioCargo);
                        break;
                    }
                }
            }

            foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)
            {
                if( $intIdPersonaEmpresaRol )
                {
                    $objPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->findOneBy( array('id' => $intIdPersonaEmpresaRol, 'estado' => $strEstadoActivo) );
                    
                    $i = 0;
                    
                    $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->findOneById($intIdPersonaEmpresaRol);

                    foreach( $arrayCaracteristicas as $strCaracteristica )
                    {
                        $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristica,
                                                                             'estado'                    => $strEstadoActivo ) );

                        $arrayParametros = array( 'estado'              => $strEstadoActivo,
                                                  'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                  'caracteristicaId'    => $objCaracteristica );

                        $arrayPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                   ->findBy($arrayParametros);
                    
                        if( !empty($arrayPersonaEmpresaRolCarac) )
                        {
                            foreach($arrayPersonaEmpresaRolCarac as $entityPersonaEmpresaRolCarac)
                            {
                                $entityPersonaEmpresaRolCarac->setFeUltMod($datetimeActual);
                                $entityPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                                $entityPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);

                                $emComercial->persist($entityPersonaEmpresaRolCarac);

                                /*
                                 * Bloque que identifica si la característica que se está eliminando es de tipo 'CARGO', que su valor es 
                                 * 'Coordinador' y que el área que lo está realizando es 'Tecnico', para remover el perfil de 'tecnicoCuadrillas' de
                                 * la persona a la cual se le está realizando el cambio de cargo
                                 */
                                if( $strNombreArea == 'Tecnico' && ( $entityPersonaEmpresaRolCarac->getValor() == 'Coordinador' || 
                                                                     $entityPersonaEmpresaRolCarac->getValor() == 'Ayudante Del Coordinador' ) 
                                  )
                                {
                                    $objPerfilCuadrilla = $emSeguridad->getRepository('schemaBundle:SistPerfil')
                                                                      ->findOneBy( 
                                                                                    array(
                                                                                            'nombrePerfil' => $strPerfilTecnicoCuadrillas, 
                                                                                            'estado'       => $strEstadoActivo
                                                                                         ) 
                                                                                 );

                                    if( $objPerfilCuadrilla )
                                    {
                                        if( $objPersonaEmpresaRol )
                                        {
                                            $intPersonaId  = $objPersonaEmpresaRol->getPersonaId()->getId();

                                            $objSeguPerfilPersona = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                                                                ->findOneBy( 
                                                                                                array( 
                                                                                                        "personaId" => $intPersonaId, 
                                                                                                        "perfilId"  => $objPerfilCuadrilla 
                                                                                                     ) 
                                                                                           );

                                            if( $objSeguPerfilPersona )
                                            {
                                                $emSeguridad->remove($objSeguPerfilPersona);

                                                //Se agrega eliminacion de registro en tabla SeguMenuPersona para cargar menus y submenus actualizados
                                                $objSeguMenuPersona = $emSeguridad->getRepository('schemaBundle:SeguMenuPersona')
                                                                                  ->findOneByPersonaId($intPersonaId);

                                                if( $objSeguMenuPersona )
                                                {
                                                    $emSeguridad->remove($objSeguMenuPersona);
                                                }
                                            }//( $objSeguPerfilPersona )
                                        }
                                        else
                                        {
                                            throw new \Exception('No existe la persona empresa rol Activa');
                                        }//( $objPersonaEmpresaRol )
                                    }
                                    else
                                    {
                                        throw new \Exception('No existe el perfil de Administracion de Cuadrillas');
                                    }//( $objPerfilCuadrilla )
                                }//( $strNombreArea == 'Tecnico' && $arrayValores[$i] == 'Coordinador' )
                            }//foreach($arrayPersonaEmpresaRolCarac as $entityPersonaEmpresaRolCarac)
                        }//( !empty($arrayPersonaEmpresaRolCarac) )
                        
                        
                        /**
                         * Bloque que asigna y elimina la característica del 'CARGO_GERENTE_PRODUCTO' asociado a un producto seleccionado por el 
                         * usuario
                         */
                        if( $strAccion == 'asignarGerenteProducto' || $strAccion == 'eliminarGerenteProducto' )
                        {
                            if( !empty($strCaracteristicaCargoProducto) && !empty($strGrupoProducto) )
                            {
                                $objCaracteristicaCargoProducto = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                  ->findOneBy( array( 'descripcionCaracteristica' => $strCaracteristicaCargoProducto,
                                                                                      'estado'                    => $strEstadoActivo ) );

                                if( is_object($objCaracteristicaCargoProducto) )
                                {
                                    $arrayParametrosCargoProducto = array( 'estado'              => $strEstadoActivo,
                                                                           'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                           'caracteristicaId'    => $objCaracteristicaCargoProducto );

                                    $arrayPersonaEmpresaRolCaracProductos = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                                        ->findBy($arrayParametrosCargoProducto);

                                    if( !empty($arrayPersonaEmpresaRolCaracProductos) )
                                    {
                                        foreach($arrayPersonaEmpresaRolCaracProductos as $objPersonaEmpresaRolCarac)
                                        {
                                            if( is_object($objPersonaEmpresaRolCarac) )
                                            {
                                                $objPersonaEmpresaRolCarac->setFeUltMod($datetimeActual);
                                                $objPersonaEmpresaRolCarac->setUsrUltMod($strUserSession);
                                                $objPersonaEmpresaRolCarac->setEstado($strEstadoEliminado);

                                                $emComercial->persist($objPersonaEmpresaRolCarac);
                                            }//( is_object($objPersonaEmpresaRolCarac) ))
                                        }//foreach($arrayPersonaEmpresaRolCaracProductos as $objPersonaEmpresaRolCarac)
                                    }//( !empty($arrayPersonaEmpresaRolCaracProductos) )

                                    if( $strAccion == 'asignarGerenteProducto' )
                                    {
                                        $objPersonaEmpresaRolCaracProducto = new InfoPersonaEmpresaRolCarac();
                                        $objPersonaEmpresaRolCaracProducto->setEstado($strEstadoActivo);
                                        $objPersonaEmpresaRolCaracProducto->setFeCreacion($datetimeActual);
                                        $objPersonaEmpresaRolCaracProducto->setIpCreacion($strIpUserSession);
                                        $objPersonaEmpresaRolCaracProducto->setUsrCreacion($strUserSession);
                                        $objPersonaEmpresaRolCaracProducto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                        $objPersonaEmpresaRolCaracProducto->setCaracteristicaId($objCaracteristicaCargoProducto);
                                        $objPersonaEmpresaRolCaracProducto->setValor($strGrupoProducto);

                                        $emComercial->persist($objPersonaEmpresaRolCaracProducto);
                                    }//( $strAccion == 'asignarGerenteProducto' )
                                }//( is_object($objCaracteristicaCargoProducto) )
                                else
                                {
                                    throw new \Exception('No se encontró característica para guardar la relacion del producto con el Gerente de '.
                                                         'Producto asignado');
                                }
                            }
                            else
                            {
                                throw new \Exception('No se han enviado todos los parámetros correspondientes para asignar los Gerentes de '.
                                                     'Producto respectivamente. Accion('.$strAccion.'), Caracteristica('.
                                                     $strCaracteristicaCargoProducto.'), GrupoProducto('.$strGrupoProducto.')');
                            }//( !empty($strCaracteristicaCargoProducto) && !empty($strGrupoProducto) )
                        }//( ($strAccion == 'asignarGerenteProducto' || 'eliminarGerenteProducto') )
                        
                        
                        if( $strAccion == 'Guardar' || $strAccion == 'asignarGerenteProducto' )
                        {
                            $entityPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                            $entityPersonaEmpresaRolCaracNew->setEstado($strEstadoActivo);
                            $entityPersonaEmpresaRolCaracNew->setFeCreacion($datetimeActual);
                            $entityPersonaEmpresaRolCaracNew->setIpCreacion($strIpUserSession);
                            $entityPersonaEmpresaRolCaracNew->setUsrCreacion($strUserSession);
                            $entityPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                            $entityPersonaEmpresaRolCaracNew->setCaracteristicaId($objCaracteristica);
                            $entityPersonaEmpresaRolCaracNew->setValor($arrayValores[$i]);

                            $emComercial->persist($entityPersonaEmpresaRolCaracNew);
                            
                            /*
                             * Bloque que identifica si la característica que se está guardando es de tipo 'CARGO', que su valor es 'Coordinador'
                             * y que el área que lo está realizando es 'Tecnico', para agregar el perfil de 'tecnicoCuadrillas' de la persona a la 
                             * cual se le está realizando el cambio de cargo
                             */
                            if( $strNombreArea == 'Tecnico' && ( $arrayValores[$i] == 'Coordinador' || 
                                                                 $arrayValores[$i] == 'Ayudante Del Coordinador' ) 
                              )
                            {
                                $objPerfilCuadrilla = $emSeguridad->getRepository('schemaBundle:SistPerfil')
                                                                  ->findOneBy( 
                                                                                array(
                                                                                        'nombrePerfil' => $strPerfilTecnicoCuadrillas, 
                                                                                        'estado'       => $strEstadoActivo
                                                                                     ) 
                                                                             );
                                
                                if( $objPerfilCuadrilla )
                                {
                                    if( $objPersonaEmpresaRol )
                                    {
                                        $intEmpresaCod = $objPersonaEmpresaRol->getEmpresaRolId()->getEmpresaCod()->getId();
                                        $intOficinaId  = $objPersonaEmpresaRol->getOficinaId()->getId();
                                        $intPersonaId  = $objPersonaEmpresaRol->getPersonaId()->getId();
                                        
                                        $objSeguPerfilPersona = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                                                            ->findOneBy( 
                                                                                            array( 
                                                                                                    "personaId" => $intPersonaId, 
                                                                                                    "perfilId"  => $objPerfilCuadrilla 
                                                                                                 ) 
                                                                                       );
                                        if( !$objSeguPerfilPersona )
                                        {
                                            $objSeguPerfilPersona = new SeguPerfilPersona();
                                        }
                                            
                                        $objSeguPerfilPersona->setPersonaId($intPersonaId);
                                        $objSeguPerfilPersona->setEmpresaId($intEmpresaCod);
                                        $objSeguPerfilPersona->setOficinaId($intOficinaId);
                                        $objSeguPerfilPersona->setPerfilId($objPerfilCuadrilla);
                                        $objSeguPerfilPersona->setUsrCreacion($strUserSession);
                                        $objSeguPerfilPersona->setFeCreacion($datetimeActual);
                                        $objSeguPerfilPersona->setIpCreacion($strIpUserSession);
                                        
                                        $emSeguridad->persist($objSeguPerfilPersona);
                                        
                                        //Se agrega eliminacion de registro en tabla SeguMenuPersona para cargar menus y submenus actualizados
                                        $objSeguMenuPersona = $emSeguridad->getRepository('schemaBundle:SeguMenuPersona')
                                                                          ->findOneByPersonaId($intPersonaId);

                                        if( $objSeguMenuPersona )
                                        {
                                            $emSeguridad->remove($objSeguMenuPersona);
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception('No existe la persona empresa rol Activa');
                                    }//( $objPersonaEmpresaRol )
                                }
                                else
                                {
                                    throw new \Exception('No existe el perfil de Administracion de Cuadrillas');
                                }//( $objPerfilCuadrilla )
                            }//( $strNombreArea == 'Tecnico' && $arrayValores[$i] == 'Coordinador' )
                        }//( $strAccion == 'Guardar' || $strAccion == 'asignarGerenteProducto' )
                        
                        
                        /*
                         * Bloque que elimina la relación que existe entre la persona que se le cambia el cargo y un elemento de tipo Tablet
                         */
                        if( $strNombreArea == 'Tecnico' )
                        {
                            $objDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 'estado'        => $strEstadoActivo, 
                                                                                        'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_TABLET, 
                                                                                        'detalleValor'  => $intIdPersonaEmpresaRol ) 
                                                                               );
        
                            if( $objDetalleElemento )
                            {
                                $strElementoActual   = 'Sin asignaci&oacute;n';
                                $intIdElementoActual = $objDetalleElemento->getElementoId();
                                $objElementoActual   = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                         ->findOneBy( array( 'id'     => $intIdElementoActual,
                                                                                             'estado' => $strEstadoActivo ) 
                                                                                    );
                                if( $objElementoActual )
                                {
                                    $strElementoActual = $objElementoActual->getNombreElemento();
                                }

                                $objDetalleElemento->setEstado($strEstadoEliminado);
                                $emInfraestructura->persist($objDetalleElemento);
                                $emInfraestructura->flush();
                                
                                
                                $strMotivoElemento  = 'Se elimina tablet asociada';
                                $objMotivo          = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                ->findOneByNombreMotivo($strMotivoElemento);
                                $intIdMotivo        = $objMotivo ? $objMotivo->getId() : 0;
                    
                                $strMensajeObservacion = $strMotivoElemento.": ".$strElementoActual;

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
                            }//( $objDetalleElemento )
                        }//( $strNombreArea == 'Tecnico' )
                        /*
                         * Fin del Bloque que elimina la relación que existe entre la persona que se le cambia el cargo y un elemento de tipo Tablet
                         */
                        
                        
                        $emSeguridad->flush();
                        $emComercial->flush();
                        
                        $i++;
                    }//foreach( $arrayCaracteristicas as $intIdCaracteristica )
                }//( $intIdPersonaEmpresaRol )
            }//foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)
            
            if ($strCaracteristica == 'CARGO' && 
                $strValor == 'Lider' && 
                !empty($arrayInfoDetalleAsignacion) && 
                $arrayInfoDetalleAsignacion['status'] === 'OK')
            {
                foreach($arrayInfoDetalleAsignacion['result'] as $arrayDetalleAsignacion)
                {
                    $arrayRequestGetUltimoDetHist = array('detalleId'   => $arrayDetalleAsignacion['detalleId']);

                    $arrayUltimoDetHist = $emSoporte->getRepository('schemaBundle:InfoDetalleHistorial')
                                            ->obtenerTareaActiva($arrayRequestGetUltimoDetHist);
                    
                    if (!empty($arrayUltimoDetHist) && $arrayUltimoDetHist['status'] === 'OK')
                    {
                        $strPersonaEmpresaRolId = $arrayUltimoDetHist['result'][0]['personaEmpresaRolId'];
                        $strNombrePersona       = ucwords(strtolower($arrayUltimoDetHist['result'][0]['nombreCompleto']));
                        $strLoginPersona        = $arrayUltimoDetHist['result'][0]['login'];

                        if (!empty($arrayInfoEmpresaRolCarac) && 
                            $arrayInfoEmpresaRolCarac['status'] === 'OK' && 
                            $strPersonaEmpresaRolId == $arrayInfoEmpresaRolCarac['result'][0]['personaEmpresaRolId'])
                        {
                            $objPersonaEMpresaRolACambiar = $emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->find($strIdPersonaEmpresaRol);

                            $arrayParametrosReasignar = array(
                                'idEmpresa'             => 10,
                                'prefijoEmpresa'        => 'TN',
                                'id_detalle'            => $arrayDetalleAsignacion['detalleId'],
                                'motivo'                => 'Cambio de lider en la cuadrilla: '. 
                                $objPersonaEMpresaRolACambiar->getPersonaId()->getNombres() .' '
                                .$objPersonaEMpresaRolACambiar->getPersonaId()->getApellidos(),
                                'tipo_asignado'         => 'cuadrilla',
                                'cuadrilla_asignada'    => $objPersonaEMpresaRolACambiar->getCuadrillaId()->getId(),
                                'id_departamento'       => $objPersonaEMpresaRolACambiar->getDepartamentoId(),
                                'user'                  => $strUserSession,
                                'clientIp'              => $strIpUserSession 
                            );

                            $arrayResultadoReasignar = $serviceSoporte->reasignarTareaCambioLider($arrayParametrosReasignar);
                            
                            if(!$arrayResultadoReasignar['success'])
                            {
                                throw new \Exception($arrayResultadoReasignar['mensaje']);                
                            }
                        }
                    }
                }
            }

            $strResponse = 'OK';
            
            $emComercial->getConnection()->commit();
            $emComercial->getConnection()->close();	
        }
        catch(\Exception $e)
        {            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.JefesController.asignarCaracteristicaAction', 
                                       $e->getMessage(), 
                                       $strUserSession, 
                                       $strIpUserSession );

            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
        }//try
            
        return new Response($strResponse);
    }

    
    /**
     * Documentación para el método 'asignarCoordinadorGeneralAction'.
     * 
     * Se guarda el resgistro correspondiente al turno asignado.
     * 
     * @return JsonResponse
     * 
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.0 16-01-2023
     *  
     */ 
    public function asignarCoordinadorGeneralAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
        $strResponse            = 'ERROR';
        
        $intIdPersonaEmpresaRol = $objRequest->request->get('intIdPersonaEmpresaRol') ? $objRequest->request->get('intIdPersonaEmpresaRol') : 0;
        $strFechaInicio         = $objRequest->request->get('strFechaInicio') ? $objRequest->request->get('strFechaInicio') : '';
        $strHoraInicio          = $objRequest->request->get('strHoraInicio') ? $objRequest->request->get('strHoraInicio') : '';
        $strFechaFin            = $objRequest->request->get('strFechaFin') ? $objRequest->request->get('strFechaFin') : '';
        $strHoraFin             = $objRequest->request->get('strHoraFin') ? $objRequest->request->get('strHoraFin') : '';
        $strAsignarAhora        = $objRequest->request->get('strAsignarAhora') ? $objRequest->request->get('strAsignarAhora') : 'N';
        $strUsrCreacion         = $objSession->get('user') ? $objSession->get('user'): '';
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp(): '127.0.0.1';
        
        $arrayParametros                            = array();
        $arrayParametros['intIdPersonaEmpresaRol']  = $intIdPersonaEmpresaRol;
        $arrayParametros['strFechaInicio']          = $strFechaInicio;  
        $arrayParametros['strHoraInicio']           = $strHoraInicio;
        $arrayParametros['strFechaFin']             = $strFechaFin;
        $arrayParametros['strHoraFin']              = $strHoraFin;
        $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
        $arrayParametros['strIpCreacion']           = $strIpCreacion;
        $arrayParametros['strAsignarAhora']         = $strAsignarAhora;

        try
        {
            $arrayResultados = $serviceAdministracion->guardarInfoCoordinadorTurno($arrayParametros);
            $strResponse     = $arrayResultados['status'];
        }
        catch (\Exception $e)
        {   
            error_log( 'ERROR: '.$e->getMessage() );
            $strResponse = 'Ocurrió un error al asignar el turno, por favor consulte con Sistemas.';
        }

        return new Response($strResponse);
    }
    
    
    /**
     * Documentación para el método 'verificarEmpleadosAEliminarAction'.
     *
     * Verifica que los empleados que pertenecen a un coordinador y se desea desvincular su relación con el coordinador no pertenezcan a una
     * cuadrilla.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 25-11-2015
     */   
    public function verificarEmpleadosAEliminarAction()
    {        
        $objRequest  = $this->get('request');
        $strResponse = 'No se pueden eliminar el personal seleccionado<br>';
        $boolError   = false;
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $strIdPersonaEmpresaRol     = $objRequest->request->get('strIdPersonaEmpresaRol') ? $objRequest->request->get('strIdPersonaEmpresaRol') : 0;
        $arrayPersonaEmpresaRol     = explode('|', $strIdPersonaEmpresaRol);
        $arrayIntegrantesCuadrillas = array();

        try
        {
            foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)
            {
                if( $intIdPersonaEmpresaRol )
                {
                    $objEmpleado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonaEmpresaRol);

                    if( $objEmpleado )
                    {
                        $intIdCuadrillaActual     = $objEmpleado->getCuadrillaId();
                        
                        if( $intIdCuadrillaActual )
                        {
                            $objIntegrantesCuadrillas = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->findByCuadrillaId($intIdCuadrillaActual);

                            $intNumeroIntegrantes = 0;
                            foreach( $objIntegrantesCuadrillas as $objIntegranteCuadrilla)
                            {
                                $intNumeroIntegrantes++;
                            }

                            $strIdCuadrillaActual = ''.$intIdCuadrillaActual;

                            if( isset($arrayIntegrantesCuadrillas[$strIdCuadrillaActual]) )
                            {
                                $intTmpNumeroIntegrantes = $arrayIntegrantesCuadrillas[$strIdCuadrillaActual];
                                $intTmpNumeroIntegrantes--;

                                $arrayIntegrantesCuadrillas[$strIdCuadrillaActual] = $intTmpNumeroIntegrantes;
                            }
                            else
                            {
                                $intNumeroIntegrantes--;
                                $arrayIntegrantesCuadrillas[$strIdCuadrillaActual] = $intNumeroIntegrantes;
                            }//( isset($arrayIntegrantesCuadrillas[$intIdCuadrillaActual]) )


                            if( $arrayIntegrantesCuadrillas[$strIdCuadrillaActual] == 0 )
                            {
                                $strNombreEmpleado = $objEmpleado->getPersonaId() ? $objEmpleado->getPersonaId()->getInformacionPersona() : '';

                                $boolError    = true;
                                $strResponse .= $strNombreEmpleado.'<br>';
                            }//( $arrayIntegrantesCuadrillas[$intIdCuadrillaActual] == 0 )
                        }//( $intIdCuadrillaActual )
                    }//( $objEmpleado )
                }//( $intIdPersonaEmpresaRol )
            }//foreach( $arrayPersonaEmpresaRol as $intIdPersonaEmpresaRol)

            if( !$boolError )
            {
                $strResponse = 'OK';	
            }
            else
            {
                $strResponse .= '<br><b>Motivo:</b> La(s) cuadrilla(s) a la(s) que pertenece(n) no pueden quedarse sin integrantes.<br>'
                                .'Por favor, primero eliminar la(s) cuadrilla(s) a la que pertenece(s) el personal seleccionado';
            }
        }
        catch(Exception $e)
        {
            error_log($e->getMessage());	
        }
            
        return new Response($strResponse);
    }


    /**
     * @Secure(roles="ROLE_382-1")
     * 
     * Documentación para el método 'asignarGerenteProductoAction'.
     *
     * Función que muestra el twig para la asignación respectiva del gerente de producto asociado a un producto específico
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 05-04-2017
     */     
    public function asignarGerenteProductoAction()
    {
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $objRequest                = $this->get('request');
        $objSession                = $objRequest->getSession();
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $strCaracteristicaCargo    = ( $strPrefijoEmpresa == 'TN' ) ? self::CARACTERISTICA_CARGO_GRUPO_ROLES : self::CARACTERISTICA_CARGO;
        $intIdCargoGerenteProducto = 0;
        
        $arrayCargoGerenteProducto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->getOne('GRUPO_ROLES_PERSONAL', 
                                                        'COMERCIAL', 
                                                        'ADMINISTRACION_JEFES', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        '', 
                                                        'INDEPENDIENTE',
                                                        '', 
                                                        $strCodEmpresa);

        if( isset($arrayCargoGerenteProducto['id']) && !empty($arrayCargoGerenteProducto['id']) )
        {
            $intIdCargoGerenteProducto = $arrayCargoGerenteProducto['id'];
        }//( is_object($objCargoGerenteProducto) )

        return $this->render( 'administracionBundle:Jefes:asignarGerenteProducto.html.twig', 
                              array( //Caracteristica correspondiente al cargo del empleado en telcos
                                     'strCaracteristicaCargo'         => $strCaracteristicaCargo,
                                  
                                     //Caracteristica correspondiente a la relación del gerente de producto con el grupo del producto asociado
                                     'strCaracteristicaCargoProducto' => self::CARACTERISTICA_CARGO_GERENTE_PRODUCTO,
                                  
                                     'strPrefijoEmpresa'              => $strPrefijoEmpresa,
                                     'intIdCargoGerenteProducto'      => $intIdCargoGerenteProducto,
                                     'strNombreArea'                  => 'Comercial' ) );
    }


    /**
     * @Secure(roles="ROLE_383-1")
     * 
     * Documentación para el método 'asignarPersonalExternoAction'.
     *
     * Función que muestra el twig para la asignación respectiva de los cargos al personal ingresado como externo
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-04-2017
     */     
    public function asignarPersonalExternoAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCaracteristicaCargo = ( $strPrefijoEmpresa == 'TN' ) ? self::CARACTERISTICA_CARGO_GRUPO_ROLES : self::CARACTERISTICA_CARGO;

        return $this->render( 'administracionBundle:Jefes:asignarPersonalExterno.html.twig', 
                              array( 'strPrefijoEmpresa'      => $strPrefijoEmpresa,
                                     'strNombreArea'          => 'Comercial',
                                     'strCaracteristicaCargo' => $strCaracteristicaCargo ) );
    }
}