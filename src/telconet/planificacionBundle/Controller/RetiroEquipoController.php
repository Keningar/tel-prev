<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleMaterial;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;

use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class RetiroEquipoController extends Controller implements TokenAuthenticatedController
{ 
   
    public function indexAction()
    { 
    
    $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_217-1'))
		{
	$rolesPermitidos[] = 'ROLE_217-1';
	}
		
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("224", "1");

        return $this->render('planificacionBundle:RetiroEquipo:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
        
    /**
     * Metodo que obtiene el grid de las solicitudes de retiro de equipos
     * @return JsonResponse
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 25-09-2016 Se agrega la consulta para obtener la fecha que servirá para comparar si la serie de un elemento se busca o no el Naf
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 30-12-2016 Se agrega que la consulta de la fecha que servirá para comparar si la serie de un elemento se busca o no el Naf sea
     *                         por empresa
     * 
     * @since 1.0
     */
    public function gridAction()
    {
        $objResponse                            = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest                             = $this->get('request');   

        $strCodEmpresa                          = ($objRequest->getSession()->get('idEmpresa') ? $objRequest->getSession()->get('idEmpresa') : "");
        $strPrefijoEmpresa                      = $objRequest->getSession()->get('prefijoEmpresa');
        $arrayFechaDesdeAsig                    = explode('T',$objRequest->query->get('fechaDesdeAsig'));
        $arrayFechaHastaAsig                    = explode('T',$objRequest->query->get('fechaHastaAsig'));

        $strLogin2                              = ($objRequest->query->get('login2') ? $objRequest->query->get('login2') : "");
        $strDescripcionPunto                    = ($objRequest->query->get('descripcionPunto') ? $objRequest->query->get('descripcionPunto') : "");
        $strVendedor                            = ($objRequest->query->get('vendedor') ? $objRequest->query->get('vendedor') : "");
        $strCiudad                              = ($objRequest->query->get('ciudad') ? $objRequest->query->get('ciudad') : "");
        $strNumOrdenServicio                    = ($objRequest->query->get('numOrdenServicio') ? $objRequest->query->get('numOrdenServicio') : "");
        //se agregan nuevos filtros
        $strNombre                              = ($objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : "");
        $strApellido                            = ($objRequest->query->get('apellido') ? $objRequest->query->get('apellido') : "");
        $strRazonSocial                         = ($objRequest->query->get('razonSocial') ? $objRequest->query->get('razonSocial') : "");
        $strIdentificacion                      = ($objRequest->query->get('identificacion') ? $objRequest->query->get('identificacion') : "");

        $intStart                               = $objRequest->query->get('start');
        $intLimit                               = $objRequest->query->get('limit');

        $strFechaComparacionBusquedaNaf         = "";
        $em                                     = $this->getDoctrine()->getManager("telconet");
        $emSoporte                              = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        
        $arrayAdmiParametroDet                  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(   'FECHA_COMPARACION_NAF_RETIRO', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        $strCodEmpresa );

        if( $arrayAdmiParametroDet )
        {
            $strFechaComparacionBusquedaNaf = $arrayAdmiParametroDet["valor1"];
        }
        
        $arrayParametros                                = array();
        $arrayParametros['em']                          = $em;
        $arrayParametros['start']                       = $intStart;
        $arrayParametros['limit']                       = $intLimit;
        $arrayParametros['startDate']                   = $arrayFechaDesdeAsig[0];
        $arrayParametros['endDate']                     = $arrayFechaHastaAsig[0];
        $arrayParametros['login2']                      = $strLogin2;
        $arrayParametros['sectorId']                    = '';
        $arrayParametros['descripcionPunto']            = $strDescripcionPunto;
        $arrayParametros['usrvendedor']                 = $strVendedor;
        $arrayParametros['numOrdenServicio']            = $strNumOrdenServicio;
        $arrayParametros['ciudad']                      = $strCiudad;
        $arrayParametros['emSoporte']                   = $emSoporte;
        $arrayParametros['codEmpresa']                  = $strCodEmpresa;
        $arrayParametros['prefijoEmpresa']              = $strPrefijoEmpresa;
        $arrayParametros['nombre']                      = $strNombre;
        $arrayParametros['apellido']                    = $strApellido;
        $arrayParametros['razonSocial']                 = $strRazonSocial;
        $arrayParametros['identificacion']              = $strIdentificacion;
        $arrayParametros['fechaComparacionBusquedaNaf'] = $strFechaComparacionBusquedaNaf;
        
        $strJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonSolicitudesRetirarEquipo($arrayParametros);
        $objResponse->setContent($strJson);
        
        return $objResponse;
    }
    
	public function ajaxGetElementosSolicitudAction()
    {
		$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');   
        
        $idSolicitud = $peticion->get('idSolicitud');
       
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
        $elementos = array();
        
        $em = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $caractsSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolCaract')->findBy(array("detalleSolicitudId"=>$idSolicitud,"estado"=>"AsignadoTarea"));
        
        $count = count($caractsSolicitud);
        
        foreach($caractsSolicitud as $caractSolicitud){
			$elementoSolicitud = array();
			$elementoCliente = $em->getRepository('schemaBundle:InfoElemento')->find($caractSolicitud->getValor());
			$modeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($elementoCliente->getModeloElementoId());
			$tipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->find($modeloElemento->getTipoElementoId());
			
			$elementoSolicitud['idSolCaract'] = $caractSolicitud->getId();
			$elementoSolicitud['tipoElemento'] = $tipoElemento->getNombreTipoElemento();
			$elementoSolicitud['nombreElemento'] = $elementoCliente->getNombreElemento();
                        $elementoSolicitud['idElemento'] = $elementoCliente->getId();
			
			$elementos[] = $elementoSolicitud;
        }
        
        if($count == 0)
		{
			$resultado= array('total' => 0 ,'encontrados' => array('idSolCaract' => 0 , 'tipoElemento' => 'Sin Informacion', 'nombreElemento' => 'Sin Informacion'));
			$resultado = json_encode( $resultado);
			
		}
		else
		{
			$elementos =json_encode($elementos);
			$resultado= '{"total":"'.$count.'","encontrados":'.$elementos.'}';
			
		}
            
        $respuesta->setContent($resultado);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para buscar CPE en el NAF y en TELCOS
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 03-05-2016   Se agrega filtro de empresa al obtener producto INTERNET DEDICADO
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 11-07-2016   se agrega validacion para retiro de equipos wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 15-09-2016 se estableció que se debe comparar por nombre tecnico del producto wifi
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 28-12-2016   Se crea metodo service para ser reutilizado desde app movil
     * 
     * @since 1.0
     */
    public function ajaxBuscarCpeNafAction()
    {
        $objResponse                      = new JsonResponse();
        $objPeticion                      = $this->get('request');
        $arrayParametros = array( 'strPrefijoEmpresa' => $objPeticion->getSession()->get('prefijoEmpresa'), 
                                  'intIdEmpresa'      => $objPeticion->getSession()->get('idEmpresa'),
                                  'intIdServicio'     => $objPeticion->get('idServicio'),
                                  'strModeloCpe'      => ($objPeticion->get('codigoArticulo') ? $objPeticion->get('codigoArticulo') : ""),
                                  'strEstadoCpe'      => $objPeticion->get('estadoCpe'),
                                  'strBandera'        => $objPeticion->get('bandera'),
                                  'strSerieCpe'       => ($objPeticion->get('serieCpe') ? $objPeticion->get('serieCpe') : ""),
                                  'intIdElementoCpe'  => $objPeticion->get('idElementoCpe')
                                );
        //ejecución de service para recuperar información de cliente
        $serviceRetiroEquipo        = $this->get('planificacion.RetiroEquipo');
        $arrayResultadoObtenerInfo  = $serviceRetiroEquipo->buscarCpeNaf($arrayParametros);
        $objResponse->setData( $arrayResultadoObtenerInfo );
        return $objResponse;
    }
    
    /**
     * Metodo para realizar la finalizacion de las solicitudes de retiro de equipo 
     * Retorna response
     * @return response
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 10-02-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 10-05-2016      Se realiza la finalización de tareas asociadas el retiro de equipo
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 25-09-2016 Se obtiene el parámetro $boolBuscarCpeNaf para verificar si se procede o no a buscar en el Naf siempre y cuando el 
     *                         estado sea diferente de 'NO ENTREGADO'.
     *                         En caso de que no vaya a buscar la serie en el NAf, se guardará la serie y el modelo ingresado referenciando
     *                         a la característica de la solicitud que guarda el elemento.
     *                         Además, independiente del estado, se procederá a guardar dicho estado y al custodio asignado como caracteristica de
     *                         solicitud 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 29-09-2016 Se modifica para que vaya a consultar los equipos en Naf, aún teniendo la fecha de activación menor al 01-07-2016,
     *                         fecha mínima especificada por el usuario para consultar los elementos en el NAF,
     *                         por si existiera algún caso en el que el elemento si existiera en Naf, pero se debe permitir la finalización del 
     *                         elemento en telcos
     * 
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 06-12-2016 Se agrega notificación vía correo electronico a solicitudes de retiro de equipos generadas por cancelación de 
     *                         servicios por procesos masivos.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.5 12-12-2016 Se valida que cuando el elemento es tipo roseta no valide en el naf y deje continuar
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 29-12-2016 Se modifica validación de búsqueda en el NAF. 
     *                         Si el elemento es una roseta nunca debe validar en el NAF, pero en caso de no serlo, debería seguir el flujo
     *                         normalmente tomando en cuenta la validación realizada previamente al consultar el login en la solicitud de retiro
     *                         de equipo que obtiene el valor de $strBuscarCpeNaf dependiendo de la fecha de activación del servicio parametrizada
     *                         por empresa.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 18-01-2017 Se envia programación de opción a metodo service para su reutilización desde App movil
     * @since 1.6
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 16-08-2017 -  Se obtiene el departamento en session para insertar en la tabla INFO_TAREA_SEGUIMIENTO
     *
     * @since 1.0
     */
    public function ajaxFinalizarRetiroEquipoAction()
    {
        $objResponse                             = new JsonResponse();
        $objPeticion                             = $this->get('request');
        $arrayParametros                         = array();    
        $arrayParametros['strIpCreacion']        = $objPeticion->getClientIp();
        $arrayParametros['strCodEmpresa']        = $objPeticion->getSession()->get('idEmpresa');
        $arrayParametros['intIdSolicitud']       = $objPeticion->get('idSolicitud');
        $arrayParametros['strBuscarCpeNaf']      = $objPeticion->get('buscarCpeNaf');
        $arrayParametros['intIdResponsable']     = $objPeticion->get('idResponsable');
        $arrayParametros['arrayDatosElementos']  = json_decode($objPeticion->get('datosElementos'),true);
        $arrayParametros['strPrefijoEmpresa']    = $objPeticion->getSession()->get('prefijoEmpresa');
        $arrayParametros['intIdDepartamento']    = $objPeticion->getSession()->get('idDepartamento');
        $arrayParametros['strUsuarioCreacion']   = $objPeticion->getSession()->get('user');
        $serviceRetiroEquipo                     = $this->get('planificacion.RetiroEquipo');
        $arrayResultadoObtenerInfo               = $serviceRetiroEquipo->finalizarRetiroEquipo($arrayParametros);
        $objResponse->setData( array('success' => $arrayResultadoObtenerInfo['strStatus'] == 'OK'?true:false,
                                     'msg'     => $arrayResultadoObtenerInfo['strMensaje']
                                    )
                             );
        
        return $objResponse;
    }


}