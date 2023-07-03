<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;

/**
 * DocumentosFinancieros controller.
 *
 * Controlador que se encargará de administrar las funcionalidades de la facturación por consumo.
 *
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 13/03/2018
 */
class ArchivoConsumoController extends Controller
{

    /**
     * @Secure(roles="ROLE_407-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administración de documentos financieros para un cliente
     * @return render.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 20-03-2018
     */
    public function indexAction()
    {
        return $this->render('administracionBundle:ArchivoConsumo:index.html.twig');
    }

    /**
     * @Secure(roles="ROLE_407-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redireccion a la pantalla principal de la administración de documentos financieros para un cliente
     * @return render.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 20-03-2018
     */
    public function subirArchivoAction()
    {
        $strServerRoot       = $this->container->getParameter('path_telcos');
        $strRuta             = $this->container->getParameter('cloudforms_ruta_archivos');
        $emInfraestructura   = $this->get('doctrine')->getManager('telconet_infraestructura');
        $strUrlDestino       = $strServerRoot . $strRuta;
        $objRequest          = $this->getRequest();
        $strIp               = $objRequest->getClientIp();
        $strUser             = $objRequest->getSession()->get('user');
        $arrayDatosFormFiles = $objRequest->files->get('file_consumo');
        $strFechaConsumo     = $objRequest->request->get('feConsumo');
        $strIdEmpresa        = $objRequest->getSession()->get('idEmpresa');
        $serviceUtil         = $this->get('schema.Util');
        $strTipoMensaje      = 'subida';
        ini_set('upload_max_filesize', '3072M');
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {
            foreach($arrayDatosFormFiles as $objArchivo)
            {
                if($objArchivo)
                {
                    $strOriginalName = $objArchivo->getClientOriginalName();
                    $intLastIndex    = strripos($strOriginalName, '.');
                    $strExtension    = substr($strOriginalName, $intLastIndex);
                    if($strExtension != '.csv')
                    {
                        throw new \Exception("Uno o más archivos tienen la extensión csv");
                    }
                    //Escribo en INFO_PROCESO_MASIVO_CAB
                    $entityProcesoMasivoCab = new InfoProcesoMasivoCab();
                    $entityProcesoMasivoCab->setTipoProceso("consumoCloudforms");
                    $entityProcesoMasivoCab->setEmpresaCod($strIdEmpresa);
                    $entityProcesoMasivoCab->setEstado("Pendiente");
                    $entityProcesoMasivoCab->setFechaEmisionFactura(new \DateTime($strFechaConsumo)); //FECHA_CONSUMO
                    $entityProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
                    $entityProcesoMasivoCab->setUsrCreacion($strUser);
                    $entityProcesoMasivoCab->setIpCreacion($strIp);
                    $strNombreArchivo       = "report_" . date("Ymd_His") . $strExtension;
                    $objArchivo->move($strUrlDestino, $strNombreArchivo);
                    $entityProcesoMasivoCab->setIdsOficinas($strUrlDestino . $strNombreArchivo);
                    $emInfraestructura->persist($entityProcesoMasivoCab);
                    $emInfraestructura->flush();                    
                }
            }
            $emInfraestructura->getConnection()->commit();
            $strMensaje = "Los archivos de consumo se estan procesando. Llegara un correo notificando cuando termine el proceso.";
        }
        catch(\Exception $ex)
        {
            $emInfraestructura->getConnection()->rollback();
            $serviceUtil->insertError('Telcos+', 'subirArchivoAction', $ex->getMessage(), $strUser, $strIp);
            $strTipoMensaje = 'notice';
            $strMensaje = 'Ha ocurrido un error inesperado al subir los archivos de consumo.';
        }
        $this->get('session')->getFlashBag()->add($strTipoMensaje, $strMensaje);
        return $this->redirect($this->generateUrl('archivo_consumo_index', array()));
    }

    /**
     * @Secure(roles="ROLE_407-1")
     * 
     * Documentación para el método 'gridConsumoCloudAction'.
     *
     * Obtiene los valores a cargar en el grid
     * @return render.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 20-03-2018
     */
    public function gridConsumoCloudAction()
    {
        $objRequest = $this->getRequest();
        $arrayParametros = array("estado" => $objRequest->get("estado"),
            "puntoId" => $objRequest->get("puntoId"),
            "limit" => $objRequest->get("limit"),
            "page" => $objRequest->get("page"),
            "start" => $objRequest->get("start"),
            "mesConsumo" => $objRequest->get("mesConsumo"));
        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');
        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoConsumoCloudCab')->obtieneGridConsumo($arrayParametros);
        return new JsonResponse($arrayResultado);
    }

    /**
     * Documentación para getEstadosAction
     * Obtiene los estados para el filtro del grid
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 13/03/2018
     */
    public function getEstadosAction()
    {
        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');
        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoConsumoCloudCab')->obtieneEstados();
        return new JsonResponse($arrayResultado);
    }

    /**
     * Documentación para getLoginsAction
     * Obtiene los login para el filtro del grid
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 13/03/2018
     */
    public function getLoginsAction()
    {
        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');
        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoConsumoCloudCab')->obtieneLogins();
        return new JsonResponse($arrayResultado);
    }

    /**
     * Documentación para getMesesAction
     * Obtiene los meses para el filtro del grid
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 13/03/2018
     */
    public function getMesesAction()
    {
        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');
        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoConsumoCloudCab')->obtieneMeses();
        return new JsonResponse($arrayResultado);
    }

    /**
     * @Secure(roles="ROLE_407-1")
     * 
     * Documentación para el método 'anulaConsumosAction'.
     *
     * Anula los consumos en base a un filtro
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 20-03-2018
     */
    public function anulaConsumosAction()
    {
        $objRequest = $this->getRequest();
        $arrayParametros = array("estado" => $objRequest->get("estado"),
            "puntoId" => $objRequest->get("puntoId"),
            "mesConsumo" => $objRequest->get("mesConsumo"),
            "usuario" => $objRequest->getSession()->get('user'),
            "ip" => $objRequest->getClientIp());

        if(is_null($arrayParametros["estado"]) || $arrayParametros["estado"] == 'Todos')
        {
            return new Response("Debe escoger un estado específico para anular los consumos");
        }
        if($arrayParametros["estado"] == 'Procesado' || $arrayParametros["estado"] == 'Eliminado' || $arrayParametros["estado"] == 'Anulado')
        {
            return new Response("No es posible anular registros ya procesados");
        }
        if(is_null($arrayParametros["mesConsumo"]) || $arrayParametros["mesConsumo"] == 'Todos')
        {
            return new Response("Debe escoger un mes de consumo específico para anular los consumos");
        }

        $emFinanciero = $this->getDoctrine()->getManager('telconet_financiero');
        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoConsumoCloudCab')->anulaConsumos($arrayParametros);

        return new Response($arrayResultado["mensaje"]);
    }

}
