<?php

namespace telconet\tecnicoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FoxPremiumController extends Controller
{
    //Se definen las constantes de la clase.
    const EM_COMERCIAL             = 'telconet';
    const DOCTRINE                 = 'doctrine';
    const INFO_SERVICIO_REPOSITORY = 'schemaBundle:InfoServicio';
    const ESTADO_ACTIVO            = 'Activo';
    const FOX_PREMIUM_SERVICE      = 'tecnico.FoxPremium';
    const OK                       = 'OK';

    /**
     * @Secure(roles="ROLE_151-5877, ROLE_151-7577, ROLE_151-7579, ROLE_151-8277, ROLE_151-8417")
     * Función que reinicia la clave de un cliente específico.
     * @author Luis Cabrera
     * @version 1.0
     * @since 18-06-2018
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1
     * @since 15-09-2020
     * se modifican roles para usar método con productos Fox, Paramount y Noggin
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 
     * @since 07-12-2020  Se Modifica valores de entrada del metodo (strNombreProducto)-> se ontiene el nombre tecnico del producto.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3
     * @since 09-08-2021  Se Agrega Rol para el producto ECDF
     */
    public function restablecerContraseniaAction()
    {
        try
        {
            $strMensaje     = self::OK;
            $objRequest     = $this->getRequest();
            $objSession     = $objRequest->getSession();
            $intIdServicio  = $objRequest->get("intIdServicio");
            $strClientIp    = $objRequest->getClientIp();
            $strUsrCreacion = $objSession->get("user");
            $strEmpresaCod  = $objSession->get("idEmpresa");
            $strNombreProducto  = $objRequest->get("strNombreProducto");
            /* @var $serviceFoxPremium \telconet\tecnicoBundle\Service\FoxPremiumService */
            $serviceFoxPremium = $this->get(self::FOX_PREMIUM_SERVICE);
            $strRespuesta      = $serviceFoxPremium->restablecerContrasenia(array("intIdServicio"  => $intIdServicio,
                                                                                  "strNombreProducto" => $strNombreProducto,
                                                                                  "strUsrCreacion" => $strUsrCreacion,
                                                                                  "strEmpresaCod"  => $strEmpresaCod,
                                                                                  "strClientIp"    => $strClientIp));
            if (self::OK != $strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }
        return new Response($strMensaje);
    }

    /**
     * @Secure(roles="ROLE_151-6257, ROLE_151-7557, ROLE_151-7578")
     * Función que limpia la caché en el servidor de Toolbox (FOX-PARAMOUNT-NOGGIN)
     * @author Luis Cabrera
     * @version 1.0
     * @since 17-12-2018
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1
     * @since 15-09-2020
     * se modifican roles para usar método con productos Fox, Paramount y Noggin
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 
     * @since 07-12-2020  Se Modifica valores de entrada del metodo (strNombreProducto)-> se ontiene el nombre tecnico del producto.
     *
     */
    public function clearCacheToolboxAction()
    {
        try
        {
            $strMensaje     = self::OK;
            $objRequest     = $this->getRequest();
            $objSession     = $objRequest->getSession();
            $intIdServicio  = $objRequest->get("intIdServicio");
            $strClientIp    = $objRequest->getClientIp();
            $strUsrCreacion = $objSession->get("user");
            $strNombreProducto  = $objRequest->get("strNombreProducto");

            /* @var $serviceFoxPremium \telconet\tecnicoBundle\Service\FoxPremiumService */
            $serviceFoxPremium = $this->get(self::FOX_PREMIUM_SERVICE);
            $strRespuesta      = $serviceFoxPremium->clearCacheToolbox(array("intIdServicio"     => $intIdServicio,
                                                                             "strNombreProducto" => $strNombreProducto,
                                                                             "strCreaProcMasivo" => "S",
                                                                             "strUsrCreacion"    => $strUsrCreacion,
                                                                             "strIpCreacion"     => $strClientIp));
            if (self::OK != $strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = "Ha ocurrido un error inesperado. " . $ex->getMessage();
        }
        return new Response($strMensaje);
    }
    
    /**
     * @Secure(roles="ROLE_151-6517,ROLE_151-7558,ROLE_151-7559,ROLE_151-8418")
     * reenviarContraseniaAction, Función que reenvía la clave de un cliente específico.
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0
     * @since 08-05-2019
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1
     * @since 15-09-2020
     * se modifican roles para usar método con productos Fox, Paramount y Noggin.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 
     * @since 07-12-2020  Se Modifica valores de entrada del metodo (strNombreProducto)-> se ontiene el nombre tecnico del producto.
     *
     */
    public function reenviarContraseniaAction()
    {
        try
        {
            $strMensaje     = self::OK;
            $objRequest     = $this->getRequest();
            $objSession     = $objRequest->getSession();
            $intIdServicio  = $objRequest->get("intIdServicio");
            $strClientIp    = $objRequest->getClientIp();
            $strUsrCreacion = $objSession->get("user");
            $strEmpresaCod  = $objSession->get("idEmpresa");
            $strNombreProducto  = $objRequest->get("strNombreProducto");

            /* @var $serviceFoxPremium \telconet\tecnicoBundle\Service\FoxPremiumService */
            $serviceFoxPremium = $this->get(self::FOX_PREMIUM_SERVICE);
            $strRespuesta      = $serviceFoxPremium->reenviarContrasenia(array("intIdServicio"  => $intIdServicio,
                                                                               "strNombreProducto" => $strNombreProducto,
                                                                               "strUsrCreacion" => $strUsrCreacion,
                                                                               "strEmpresaCod"  => $strEmpresaCod,
                                                                               "strClientIp"    => $strClientIp));
            if (self::OK != $strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }
        return new Response($strMensaje);
    }

    /**
     * @Secure(roles="ROLE_151-7717,ROLE_151-7718,ROLE_151-8477")
     * ingresarContactoAction, Función que Ingresa Contacto para productos Paramount y Noggin.
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0
     * @since 01-12-2020
     */
    public function ingresarContactoAction()
    {
        try
        {
            $strMensaje         = self::OK;
            $objRequest         = $this->getRequest();
            $objSession         = $objRequest->getSession();
            $strClientIp        = $objRequest->getClientIp();
            $strUsrCreacion     = $objSession->get("user");
            $strEmpresaCod      = $objSession->get("idEmpresa");
            
            $strNombreProducto  = $objRequest->get("nombreProducto");
            $arrayFormaContacto = json_decode($objRequest->get("array_data"),true);
            $intPuntoId          = $objRequest->get("intPuntoId");
            $intIdServicio       = $objRequest->get("intIdServicio");


            /* @var $serviceFoxPremium \telconet\tecnicoBundle\Service\FoxPremiumService */
            $serviceFoxPremium = $this->get(self::FOX_PREMIUM_SERVICE);
            $strRespuesta      = $serviceFoxPremium->guardarFormaContactoPunto(array(   "arrayFormaContacto" => $arrayFormaContacto,
                                                                                        "strNombreProducto"  => $strNombreProducto,
                                                                                        "intIdServicio"      => $intIdServicio,
                                                                                        "strUsrCreacion"     => $strUsrCreacion,
                                                                                        "strEmpresaCod"      => $strEmpresaCod,
                                                                                        "strClientIp"        => $strClientIp,
                                                                                        "intPuntoId"         => $intPuntoId));
            if (self::OK != $strRespuesta)
            {
                throw new \Exception($strRespuesta);
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = "Ocurrió un error al realizar el guardado de los Correos, por favor consulte con el Administrador";
        }

        return new Response($strMensaje);
    }
    /**
     * caractCorreoAction, Función que obtiene los correos para productos Paramount y Noggin.
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.0
     * @since 01-12-2020
     */
    public function caractCorreoAction()
    {
        $objRequest         = $this->getRequest();
        $intIdServicio      = $objRequest->get("intIdServicio");
        $strNombreProducto  = $objRequest->get("nombreProducto");
        /* @var $serviceFoxPremium \telconet\tecnicoBundle\Service\FoxPremiumService */
        $serviceFoxPremium = $this->get(self::FOX_PREMIUM_SERVICE);
        $strRespuesta      = $serviceFoxPremium->obtenerCaractCorreo(array( "intIdServicio" => $intIdServicio,
                                                                            "strNombreProducto" =>  $strNombreProducto));
        $arrayArreglo = $strRespuesta['registros'];
        if (empty($arrayArreglo))
        {
            $arrayArreglo = array(array());
        }
        $strResponse = new Response(json_encode(array('personaFormasContacto' => $arrayArreglo)));
        $strResponse->headers->set('Content-type', 'text/json');
        return $strResponse;
    }

    /**
     * Funcion que activa el producto ECDF cuando se ingrese un correo válido 
     * servicios ECDF que se encuentren en estado Pendiente.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 01-12-2021
     
     * @return redirect
     **/
    public function activaServicioECDFAction()
    {
        $strMensaje                 = self::OK;
        try
        {       
          $objRequest               = $this->getRequest();
          $objSession               = $objRequest->getSession();
          $intIdServicio            = $objRequest->get("intIdServicio");
          $intIdPersonaEmpresaRol   = $objRequest->get("idPersonaEmpresaRol");
          $boolEliminarCorreo       = $objRequest->get("boolEliminarCorreo") == "true" ? true : false;
          $boolActualizar           = $objRequest->get("boolActualizar") == "true" ? true : false;
          $strClientIp              = $objRequest->getClientIp();
          $strUsrCreacion           = $objSession->get("user");
          $strEmpresaCod            = $objSession->get("idEmpresa");

          $serviceFoxPremium        = $this->get(self::FOX_PREMIUM_SERVICE);
          $serviceUtil              = $this->get('schema.Util');

          $strRespuesta      = $serviceFoxPremium->activarServicioECDF(array( "intIdServicio"       => $intIdServicio,
                                                                              "strUsrCreacion"      => $strUsrCreacion,
                                                                              "strEmpresaCod"       => $strEmpresaCod,
                                                                              "strClientIp"         => $strClientIp,
                                                                              "boolEliminarCorreo"  => $boolEliminarCorreo,
                                                                              "boolActualizar"      => $boolActualizar,
                                                                              "boolCrearTarea"      => false,
                                                                              "boolEliminarCorreo"  => $boolEliminarCorreo,
                                                                              "intIdPersonaEmpresaRol" => $intIdPersonaEmpresaRol));
          if (self::OK != $strRespuesta)
          {
              throw new \Exception($strRespuesta);
          }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'activaServicioECDFAction', $ex->getMessage(), $strUsrCreacion, $strClientIp);
            $strMensaje = $ex->getMessage();
        }
        return new Response($strMensaje);
    }
    /**
     * Funcion que activa los productos que no generan credenciales 
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 25-08-2022
     
     * @return redirect
     **/
    public function activaServicioSinCredencialesAction()
    {
        $strMensaje                 = self::OK;
        try
        {       
          $objRequest               = $this->getRequest();
          $objSession               = $objRequest->getSession();
          $intIdServicio            = $objRequest->get("intIdServicio");
          $intIdPersonaEmpresaRol   = $objRequest->get("idPersonaEmpresaRol");
          $strClientIp              = $objRequest->getClientIp();
          $strUsrCreacion           = $objSession->get("user");
          $strEmpresaCod            = $objSession->get("idEmpresa");

          $serviceFoxPremium        = $this->get(self::FOX_PREMIUM_SERVICE);
          $serviceUtil              = $this->get('schema.Util');

          $strRespuesta      = $serviceFoxPremium->activarServicioSinCredenciales(array( "intIdServicio"       => $intIdServicio,
                                                                              "strUsrCreacion"      => $strUsrCreacion,
                                                                              "strEmpresaCod"       => $strEmpresaCod,
                                                                              "strClientIp"         => $strClientIp));
          if (self::OK != $strRespuesta)
          {
              throw new \Exception($strRespuesta);
          }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'activaServicioSinCredencialesAction', $ex->getMessage(), $strUsrCreacion, $strClientIp);
            $strMensaje = $ex->getMessage();
        }
        return new Response($strMensaje);
    }
    /**
     * Funcion que reenvía el enlace para crear contraseña de productos de streaming
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.0 10-09-2022
     
     * @return redirect
     **/
    public function reenviarCorreoPasswordAction()
    {
        $strMensaje                 = self::OK;
        try
        {       
          $objRequest               = $this->getRequest();
          $objSession               = $objRequest->getSession();
          $intIdServicio            = $objRequest->get("intIdServicio");
          $strClientIp              = $objRequest->getClientIp();
          $strUsrCreacion           = $objSession->get("user");
          $strEmpresaCod            = $objSession->get("idEmpresa");

          $serviceFoxPremium        = $this->get(self::FOX_PREMIUM_SERVICE);
          $serviceUtil              = $this->get('schema.Util');

          $strRespuesta      = $serviceFoxPremium->reenviarCorreoPassword(array( "intIdServicio"       => $intIdServicio,
                                                                              "strUsrCreacion"      => $strUsrCreacion,
                                                                              "strEmpresaCod"       => $strEmpresaCod,
                                                                              "strClientIp"         => $strClientIp));
          if (self::OK != $strRespuesta)
          {
              throw new \Exception($strRespuesta);
          }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'reenviarCorreoPasswordAction', $ex->getMessage(), $strUsrCreacion, $strClientIp);
            $strMensaje = $ex->getMessage();
        }
        return new Response($strMensaje);
    }
}
