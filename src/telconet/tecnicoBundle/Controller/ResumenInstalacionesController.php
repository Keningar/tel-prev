<?php
namespace telconet\tecnicoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ResumenInstalaciones controller.
 *
 * Controlador que se encargará de administrar las funcionalidades
 * respecto a la opción de Resumen Instalaciones
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 28-09-2015
 */
class ResumenInstalacionesController extends Controller
{
    /**
     * @Secure(roles="ROLE_304-7")
     *
     * Documentación para el método 'gridAction'.
     *
     * Realizará la búsqueda de las instalaciones que correspondan a los criterios ingresados por los usuarios.
     * 
     * @param string $objRequest Criterios ingresados por el usuario.
     *
     * @return JsonResponse $response
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29-09-2015
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 18-11-2015 - Se modifica que al consultar las 'ACTA ENTREGA RECEPCION' y 'ENCUESTA' se envíe el parámetro 'nombreDocumento'
     *                           vacío puesto que en dichas consultas no es necesario.
     */
    public function gridAction(Request $objRequest)
    {
        $response        = new JsonResponse();
        $arrayParametros = array();
        $arrayResultados = array();
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        $emComunicacion  = $this->getDoctrine()->getManager('telconet_comunicacion');
        $strActivo       = 'Activo';
        
        $objSession = $objRequest->getSession();
        $idEmpresa  = $objSession->get('idEmpresa');
        
        $strFechaDesde   = $objRequest->query->get('fechaDesde');
        $arrayFechaDesde = explode('T', $strFechaDesde);
        $strFechaHasta   = $objRequest->query->get('fechaHasta');
        $arrayFechaHasta = explode('T', $strFechaHasta);
        
        $arrayParametros['empresa']              = $idEmpresa;
        $arrayParametros['instalacionesActivas'] = true;
        
        if( isset($arrayFechaDesde[0]) )
        {
            if($arrayFechaDesde[0])
            {
                $arrayParametros['fechaDesde'] = $arrayFechaDesde[0];
            }
            else
            {
                $arrayParametros['fechaDesde'] = date('Y-m-d');
            }
        }
        
        if( isset($arrayFechaHasta[0]) )
        {
            if($arrayFechaHasta[0])
            {
                $arrayParametros['fechaHasta'] = $arrayFechaHasta[0];
            }
            else
            {
                $arrayParametros['fechaHasta'] = date('Y-m-d');
            }
        }
        
        $intContadorResultados = 0; 
        
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                     ->findOneBy( array('estado' => $strActivo, 'nombreParametro' => 'CIUDADES_INSTALACIONES') );
        
        if( $objParametroCab )
        {
            $objParametroCantones = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->findBy( array('estado' => $strActivo, 'parametroId' => $objParametroCab) );
            
            if( $objParametroCantones )
            {
                foreach($objParametroCantones as $objCanton)
                {
                    $arrayItemResultado = array();
                    $intEncuestas       = 0;
                    $intActasEntrega    = 0;
                    $intImagenes        = 0;
                    $strCanton          = $objCanton->getDescripcion();

                    $arrayParametros['canton'] = $strCanton;

                    $arrayInstalaciones = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getInstalacionesByCriterios( $arrayParametros );
                    $intInstalaciones   = $arrayInstalaciones['total'];

                    if($intInstalaciones)
                    {
                        $arrayServicios = array();

                        foreach($arrayInstalaciones['registros'] as $arrayItem)
                        {
                            $arrayServicios[] = $arrayItem['idServicio'];
                        }

                        //Para saber el número de Encuestas ingresadas por los técnicos
                        $arrayParametros['descripcionTipoDocumento']   = 'ENCUESTA';
                        $arrayParametros['estadoDocumento']            = array('Activo');
                        $arrayParametros['estadoDocumentoRelacion']    = array('Activo');
                        $arrayParametros['estadoTipoDocumentoGeneral'] = array('Activo');
                        $arrayParametros['nombreDocumento']            = '';
                        $arrayParametros['servicios']                  = $arrayServicios;

                        $arrayEncuestas = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getDocumentosByCriterios( $arrayParametros );
                        $intEncuestas   = $arrayEncuestas['total'];
                        //Fin Para saber el número de Encuestas ingresadas por los técnicos


                        //Para saber el número de Actas de Entrega ingresadas por los técnicos
                        $arrayParametros['descripcionTipoDocumento'] = 'ACTA ENTREGA RECEPCION';

                        $arrayActasEntrega = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getDocumentosByCriterios( $arrayParametros );
                        $intActasEntrega   = $arrayActasEntrega['total'];
                        //Fin Para saber el número de Actas de Entrega ingresadas por los técnicos


                        //Para saber el número de Imágenes ingresadas por los técnicos
                        $arrayParametros['descripcionTipoDocumento'] = 'IMAGENES';
                        $arrayParametros['nombreDocumento']          = 'Activar Servicio';

                        $arrayImagenes = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getDocumentosByCriterios( $arrayParametros );
                        $intImagenes   = $arrayImagenes['total'];
                        //Fin Para saber el número de Imágenes ingresadas por los técnicos

                    }//($intInstalaciones)


                    $arrayItemResultado['nombreDepartamento']  = $strCanton;
                    $arrayItemResultado['numeroInstalaciones'] = $intInstalaciones;
                    $arrayItemResultado['numeroEncuestas']     = $intEncuestas;
                    $arrayItemResultado['numeroActasEntrega']  = $intActasEntrega;
                    $arrayItemResultado['numeroImagenes']      = $intImagenes;

                    $arrayResultados[] = $arrayItemResultado;

                    $intContadorResultados++;
                    
                }//foreach($objParametroCantones as $objCanton)
            }//( $objParametroCantones )
        }//( $objParametroCab )
        
        $response->setData(
                            array(
                                    'total'       => $intContadorResultados,
                                    'encontrados' => $arrayResultados
                                 )
                          );
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_304-2997")
     *
     * Documentación para el método 'getInstalacionesGroupByEstadosAction'.
     *
     * Realizará la búsqueda de las instalaciones que correspondan a los criterios ingresados por los usuarios,
     * y agrupadas por estados.
     * 
     * @param string $objRequest Criterios ingresados por el usuario.
     *
     * @return JsonResponse $response
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 21-10-2015 - Se cambia que si viene el estado del servicio 'Activo' quiere decir que la instalación fue realizada
     *                           y se la considera como finalizada
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 29-09-2015
     */
    public function getInstalacionesGroupByEstadosAction(Request $objRequest)
    {
        $response        = new JsonResponse();
        $arrayParametros = array();
        $arrayResultados = array();
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        
        $objSession = $objRequest->getSession();
        $idEmpresa  = $objSession->get('idEmpresa');
        
        $strCanton       = $objRequest->query->get('canton');
        $strFechaDesde   = $objRequest->query->get('fechaDesde');
        $arrayFechaDesde = explode('T', $strFechaDesde);
        $strFechaHasta   = $objRequest->query->get('fechaHasta');
        $arrayFechaHasta = explode('T', $strFechaHasta);
        
        $arrayParametros['empresa'] = $idEmpresa;
        
        if( isset($arrayFechaDesde[0]) )
        {
            if($arrayFechaDesde[0])
            {
                $arrayParametros['fechaDesde'] = $arrayFechaDesde[0];
            }
            else
            {
                $arrayFechaInicio = explode("-", date('Y-m-d'));
                $timeFechaInicio  = strtotime("01-".$arrayFechaInicio[1]."-".$arrayFechaInicio[0]);
                $dateFechaInicio  = date("Y-m-d", $timeFechaInicio);
                
                $arrayParametros['fechaDesde'] = $dateFechaInicio;
            }
        }
        
        if( isset($arrayFechaHasta[0]) )
        {
            if($arrayFechaHasta[0])
            {
                $arrayParametros['fechaHasta'] = $arrayFechaHasta[0];
            }
            else
            {
                $arrayFechaInicio = explode("-", date('Y-m-d'));
                $timeFechaInicio  = strtotime("01-".$arrayFechaInicio[1]."-".$arrayFechaInicio[0]);
                $dateFechaFinal  = strtotime(date("d-m-Y", $timeFechaInicio)." +1 month");
                $dateFechaFinal  = date("Y-m-d", $dateFechaFinal);
                
                $arrayParametros['fechaHasta'] = $dateFechaFinal;
            }
        }
        
        $intContadorResultados = 0; 

        $arrayParametros['canton']  = $strCanton;
        $arrayParametros['groupBy'] = 'estados';

        $arrayInstalaciones = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getInstalacionesByCriterios( $arrayParametros );
        $intInstalaciones   = $arrayInstalaciones['total'];

        if($intInstalaciones)
        {
            foreach($arrayInstalaciones['registros'] as $arrayItem)
            {
                $arrayItemResultado          = array();
                $arrayItemResultado['name']  = ( $arrayItem['estado'] == 'Activo' ) ? 'Finalizada' : $arrayItem['estado'];
                $arrayItemResultado['value'] = $arrayItem['totalInstalaciones'];
                
                $arrayResultados[] = $arrayItemResultado;
                
                $intContadorResultados++;
            }
        }
        
        $response->setData(
                            array(
                                    'total'       => $intContadorResultados,
                                    'encontrados' => $arrayResultados
                                 )
                          );
        return $response;
    }
}
