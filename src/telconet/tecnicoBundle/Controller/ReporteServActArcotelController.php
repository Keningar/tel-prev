<?php
/**
* Controlador utilizado para las transacciones en la pantalla de consulta de reportes de servicios activos para la arcotel
* 
* @author Richard Cabrera         <rcabrera@telconet.ec>
* @version 1.0 06-03-2017

*/
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class ReporteServActArcotelController extends Controller
{
    /**
    * indexAction - Función que llama la pantalla de consulta de los reportes de servicios activos generados para la Arcotel
    *
    * @return render
    *
    * @author Richard Cabrera   <rcabrera@telconet.ec>
    * @version 1.0 06-03-2017
    * 
    */
    public function indexAction() 
    {
        $arrayRolesPermitidos = array();
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu       = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("377", "1");
        
        //MODULO 377 - descargar archivo arcotel
        if (true === $this->get('security.context')->isGranted('ROLE_377-5197'))
        {
            $arrayRolesPermitidos[] = 'ROLE_377-5197';
        }
        
        //MODULO 377 - listar los archivos generados
        if (true === $this->get('security.context')->isGranted('ROLE_377-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_377-7';
        }
        return $this->render('tecnicoBundle:reporteServiciosActivosArcotel:index.html.twig',
                             array('item'            => $entityItemMenu,
                                   'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    
    
    /**
    * gridAction - Función que sirve para listar los archivos generados de servicios activos de Internet y Datos para la Arcotel
    *
    * @return Object $objResponse
    *
    * @author Richard Cabrera   <rcabrera@telconet.ec>
    * @version 1.0 06-03-2017
    * 
    *
	* @Secure(roles="ROLE_377-7")
	*/
    public function gridAction()
    {
        $objResponse     = new JsonResponse();
        $arrayRespuesta  = array();
        $strServerRoot   = $_SERVER['DOCUMENT_ROOT'];
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');

        $arrayParametros["strUrlServidor"] = $strServerRoot;
        $arrayRespuesta                    = $emGeneral->getRepository("schemaBundle:InfoServicio")->getJsonArchivosArcotel($arrayParametros);

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }
}