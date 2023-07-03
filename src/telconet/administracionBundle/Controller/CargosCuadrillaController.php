<?php
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiParametroDet;


/**
 * CargosCuadrilla Controller.
 *
 * Controlador que se encargará de gestionar los cargos que deben ser tomados en cuenta para la asignación de empleados a jefes, que
 * posteriormente formarán parte de una cuadrilla
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 24-01-2017
 */
class CargosCuadrillaController extends Controller
{
    /**
     * @Secure(roles="ROLE_372-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redirección a la pantalla principal que muestra los cargos asociados en las cuadrillas y los cargos que aún no se encuentran asociados.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-01-2017
     * 
     */
    public function indexAction()
    {
        $arrayRolesPermitidos   = array();
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu         = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("372", "1");

        //MODULO 312 - CargosCuadrilla/update
        if(true === $this->get('security.context')->isGranted('ROLE_372-5'))
        {
            $arrayRolesPermitidos[] = 'ROLE_372-5';
        }

        return $this->render('administracionBundle:CargosCuadrilla:index.html.twig', array(
                'item'              => $entityItemMenu,
                'rolesPermitidos'   => $arrayRolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_372-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Función que permite actualizar los cargos asociados en las cuadrillas y los cargos que aún no se encuentran asociados.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-01-2017
     * 
     */
    public function updateAction()
    {
        $objRequest         = $this->getRequest();
        $objResponse        = new JsonResponse();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $strCargosCuadrilla = $objRequest->get('strCargosCuadrilla');
        
        $strIpClient        = $objRequest->getClientIp();
        $objSession         = $objRequest->getSession();
	    $strUserSession     = ($objSession->get('user') ? $objSession->get('user') : "");
        $serviceUtil        = $this->get('schema.Util');
        
        $boolSuccess        = false;
        $strMensaje         = "";
        
        $emGeneral->beginTransaction();
        try
        {
            $intTotal               = 0;
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'CARGOS AREA TECNICA',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );
            if($strCargosCuadrilla != '' && is_object($objAdmiParametroCab))
            {
                $objCargosCuadrilla = json_decode($strCargosCuadrilla);           
                $intTotal           = $objCargosCuadrilla->intTotal;
                
                
                if($intTotal>0)
                { 
                    $arrayCargosCuadrillasGestionados = $objCargosCuadrilla->arrayRegistros;
                    foreach( $arrayCargosCuadrillasGestionados as $objCargoCuadrillaGestionado )
                    {
                        $strAccionAEjecutar     = $objCargoCuadrillaGestionado->strAccion;
                        $strFuncionaComoJefe    = $objCargoCuadrillaGestionado->strFuncionaComoJefe;
                        $strFuncionRol          = $strFuncionaComoJefe == "SI" ? "Jefes" : "Personal Tecnico";

                        if($strAccionAEjecutar=="Insertar")
                        {
                            $objAdmiParametroDetCargo = new AdmiParametroDet();
                            $objAdmiParametroDetCargo->setParametroId($objAdmiParametroCab);
                            $objAdmiParametroDetCargo->setDescripcion($objCargoCuadrillaGestionado->strDescripcionRol);
                            $objAdmiParametroDetCargo->setValor1($strFuncionRol);
                            $objAdmiParametroDetCargo->setValor2($strFuncionaComoJefe);
                            $objAdmiParametroDetCargo->setEstado('Activo');
                            $objAdmiParametroDetCargo->setFeCreacion(new \DateTime('now'));
                            $objAdmiParametroDetCargo->setUsrCreacion($strUserSession);
                            $objAdmiParametroDetCargo->setIpCreacion($strIpClient);
                            $emGeneral->persist($objAdmiParametroDetCargo);
                            $emGeneral->flush();
                        }
                        else if($strAccionAEjecutar=="Editar")
                        {
                            $objAdmiParametroDetCargo   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->find($objCargoCuadrillaGestionado->intIdParametroDet);
                            
                            if(is_object($objAdmiParametroDetCargo))
                            {
                                $objAdmiParametroDetCargo->setValor1($strFuncionRol);
                                $objAdmiParametroDetCargo->setValor2($strFuncionaComoJefe);
                                $objAdmiParametroDetCargo->setFeUltMod(new \DateTime('now'));
                                $objAdmiParametroDetCargo->setUsrUltMod($strUserSession);
                                $objAdmiParametroDetCargo->setIpUltMod($strIpClient);
                                $emGeneral->persist($objAdmiParametroDetCargo);
                                $emGeneral->flush();
                            }
                        }
                        else if($strAccionAEjecutar=="Eliminar")
                        {
                            $objAdmiParametroDetCargo   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->find($objCargoCuadrillaGestionado->intIdParametroDet);
                            if(is_object($objAdmiParametroDetCargo))
                            {
                                $objAdmiParametroDetCargo->setEstado('Eliminado');
                                $objAdmiParametroDetCargo->setFeUltMod(new \DateTime('now'));
                                $objAdmiParametroDetCargo->setUsrUltMod($strUserSession);
                                $objAdmiParametroDetCargo->setIpUltMod($strIpClient);
                                $emGeneral->persist($objAdmiParametroDetCargo);
                                $emGeneral->flush();
                            }
                        }
                    }
                    $emGeneral->commit();
                    $boolSuccess    = true;
                    $strMensaje     .= "Se actualizaron los cargos de manera correcta";
                }
                else
                {
                    $strMensaje     .= "No se ha podido obtener el número de cargos gestionados. Por favor notifique a Sistemas!";
                }
                
            }
            else
            {
                $strMensaje     .= "Se presentaron errores al intentar actualizar los cargos de las cuadrillas"
                                . " debido a que no se envían los cargos de cuadrilla o no se econtró el parámetro CARGOS AREA TECNICA."
                                . " Por favor notifique a Sistemas!";
            }
        }
        catch (\Exception $ex)
        {
            error_log($ex->getMessage());
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->rollback();
                $serviceUtil->insertError('Telcos+', 'CargosCuadrillaController->updateAction', $ex->getMessage(), $strUserSession, $strIpClient);
            }
            $emGeneral->close();
            $strMensaje .= "Se presentaron errores al intentar actualizar los cargos de las cuadrillas. Por favor notifique a Sistemas!";
        }
        
        $objResponse->setData(['success' => $boolSuccess, 'mensaje' => $strMensaje]);

        return $objResponse;
    }
    
    /**
     * getCargosCuadrillaAction
     *
     * Función que obtiene los cargos que se toman en cuenta para los empleados que son asignados a cuadrillas 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-01-2017
     * 
     */
    public function getCargosCuadrillaAction()
    {
        $objResponse            = new JsonResponse();
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strDescripcionRol      = $objRequest->get("strDescripcionRol") ? $objRequest->get("strDescripcionRol") : '';

        $emComercial        = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametros    = array(
                                    'strCodEmpresa'                     => $strCodEmpresa,
                                    'strDescripcionTipoRol'             => 'Empleado',
                                    'strDescripcionRol'                 => $strDescripcionRol,
                                    'arrayCriteriosInfoRolesCuadrillas' => array(
                                                                                    "strGetInfoRolesEnCuadrillas"  => "SI",
                                                                                    "strNombreParametroCargos"     => "CARGOS AREA TECNICA"
                                                                           )
        );
        $arrayRolesParametroDet = $emComercial->getRepository('schemaBundle:AdmiRol')->getResultadoRolesParametroDet($arrayParametros);
        $objResponse->setData($arrayRolesParametroDet);
        return $objResponse;
    }
    
    
    /**
     * getCargosAction
     *
     * Función que obtiene todos los cargos de empleados de acuerdo a la empresa 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-01-2017
     * 
     */
    public function getCargosAction()
    {
        $objResponse            = new JsonResponse();
        
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        
        $strDescripcionRol      = $objRequest->get("strDescripcionRol") ? $objRequest->get("strDescripcionRol") : '';
        $arrayCargosCuadrilla   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                    "CARGOS AREA TECNICA", 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    "Personal Tecnico", 
                                                                                                    '',
                                                                                                    '', 
                                                                                                    ''
                                                                                                    );
        $arrayDescCargosYaEnCuadrillas = array();
        if( $arrayCargosCuadrilla )
        {
            foreach($arrayCargosCuadrilla as $arrayCargoCuadrilla)
            {
                $arrayDescCargosYaEnCuadrillas[]   = $arrayCargoCuadrilla['descripcion'];
            }
        }

        $arrayParametros = array(
                                    'strCodEmpresa'                     => $strCodEmpresa,
                                    'strDescripcionTipoRol'             => 'Empleado',
                                    'strDescripcionRol'                 => $strDescripcionRol,
                                    'arrayDescCargosYaEnCuadrillas'     => $arrayDescCargosYaEnCuadrillas
        );

        $arrayRolesParametroDet    = $emComercial->getRepository('schemaBundle:AdmiRol')->getResultadoRolesParametroDet($arrayParametros);
        $objResponse->setData($arrayRolesParametroDet);
        return $objResponse;
    }
}
