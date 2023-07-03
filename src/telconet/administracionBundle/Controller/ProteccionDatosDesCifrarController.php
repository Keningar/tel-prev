<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoDocumento;

use telconet\schemaBundle\Form\PersonaEmpleadoType;
use telconet\schemaBundle\Form\InfoDocumentoType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProteccionDatosDesCifrarController extends Controller implements TokenAuthenticatedController
{ 
     /**
     * @Secure(roles="ROLE_501-1")
     *
     * Documentación para el método 'indexAction'.
     * Muestra la pagina principal del modulo de ProteccionDatosCifrar
     *
     * @return Response.
     *
     * @author Eduardo Montenegro <emontenegro@telconet.ec>
     * @version 1.0 Version Inicial
     */
    public function indexAction()
    {
        $arrayRolesPermitidos   = [];
        $objEmSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $entityItemMenu         =
                                    $objEmSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                            ->searchItemMenuByModulo("501", "1");
        $objSession             = $this->get('request')->getSession();
        $objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        return $this->render('administracionBundle:DerechoTitular:descifrar.html.twig', []);
    }

     /**
     * @Secure(roles="ROLE_501-1")
     *
     * Documentación para el método 'validarIdentificacionAction'.
     * Muestra la pagina principal del modulo de ProteccionDatosCifrar
     *
     * @return Response.
     *
     * @author Eduardo Montenegro <emontenegro@telconet.ec>
     * @version 1.0 Version Inicial
     */
    public function validarIdentificacionAction()
    {
        $objSession            = $this->get('request')->getSession();
        $objResponse           = new JsonResponse();
        $arrayRespuesta        = [];
        $objDerechoTitular     = $this->get('administracion.DerechoTitular');
        $strIdentificacion     = $this->getRequest()->get('identificacion');
        $strTipoIdentificacion = $this->getRequest()->get('tipoIdentificacion');
        $arrayRespuesta        = $objDerechoTitular->validarIdentificacion([
                                                'identificacion'=>$strIdentificacion,
                                                'tipoIdentificacion'=>$strTipoIdentificacion
        ]);
        $objResponse->setData( $arrayRespuesta );
        return $objResponse;
    }

     /**
     * Documentación para el método 'cifrarClienteAction'.
     * Muestra la pagina principal del modulo de ProteccionDatosCifrar
     *
     * @return Response.
     *
     * @author Eduardo Montenegro <emontenegro@telconet.ec>
     * @version 1.0 Version Inicial
     */
    public function descifrarClienteAction()
    {
        $objResponse                                  = new JsonResponse();
        $objSession                                   = $this->get('session');
        $arrayParametros['identificacion']            = $this->getRequest()->get('identificacion');
        $arrayParametros['tipoIdentificacion']        = $this->getRequest()->get('tipoIdentificacion');
        $arrayParametros['infoLog']['identificacion'] = $objSession->get('id_empleado');
        $arrayParametros['infoLog']['login']          = $objSession->get('user');
        $arrayParametros['infoLog']['origen']         = 'TelcoS+';
        $objDerechoTitular                            = $this->get('administracion.DerechoTitular');
        $arrayRespuesta                               = $objDerechoTitular->descifrarCliente($arrayParametros);
        $objResponse->setData( $arrayRespuesta );
        return $objResponse;
    }



}