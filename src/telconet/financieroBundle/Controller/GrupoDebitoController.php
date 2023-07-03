<?php

namespace telconet\financieroBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\schemaBundle\Entity\AdmiGrupoArchivoDebitoDet;
use telconet\schemaBundle\Entity\AdmiGrupoArchivoDebitoCab;

use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Entity\AdmiFormatoDebito;
use telconet\schemaBundle\Form\AdmiFormatoDebitoType;


/**
 * GrupoDebito controller.
 * 
 * 
 * @package    financieroBundle
 * @subpackage Controller
 * @author     Ivan Romero <icromero@telconet.ec>
 * @version 1.0  16-07-2021
 */
class GrupoDebitoController extends Controller implements TokenAuthenticatedController
{

    /**
     * 
     * Documentación para funcion 'gridGenerarDebitosAction'.
     * funcion que muestra los grupos que pueden generar archivos txt para debitos
     * @author <icromero@telconet.ec>
     * @version 1.0 
     * @return render
     */
    public function indexGrupoDebitosAction()
    {
        $objSession         = $this->getRequest()->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $objAdmiFormatoDebito             = new AdmiFormatoDebito();
        $objForm            = $this->createForm(new AdmiFormatoDebitoType(), $objAdmiFormatoDebito);
        return $this->render('financieroBundle:GrupoDebito:grupoDebitos.html.twig', 
            array(
                'prefijoEmpresa'             => $strPrefijoEmpresa,
                'entity'                     => $objAdmiFormatoDebito,
                'form'                       => $objForm->createView()
            )
        );
    }
    
     /**
    * Documentación para funcion 'gridGenerarDebitosAction'.
    * funcion que muestra los grupos que pueden generar archivos txt para debitos
    * @author <icromero@telconet.ec>
    * @return objeto - json
    * @version 1.0 
    */     
    public function comboGrupoDebitoAction() 
    {
        $strRequest   = $this->getRequest();
        $intIdEmpresa = $strRequest->getSession()->get('idEmpresa');
        $emFinanciero        = $this->get('doctrine')->getManager('telconet_financiero');
        $objDatos     = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')
                        ->findBy( array("empresaCod" => $intIdEmpresa, "estado" => "Activo", 'tipoGrupo' => 'NORMAL') );
        
        foreach ($objDatos as $objDatos)
        {    
            
            $arrayDatos[] = array(
                'id' => $objDatos->getId(),
                'banco' => $objDatos->getNombreGrupo()
            );
        }
        if (!empty($arrayDatos))
        {
            $objResponse = new Response(json_encode(array('detalles' => $arrayDatos)));
        }
           
        else
        {
            $arrayDatos[] = array();
            $objResponse = new Response(json_encode(array('detalles' => $arrayDatos)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    
    /** 
    * Documentación para funcion 'comboBancosAction'.
    * funcion que muestra los bancos que pueden agregar a un grupo
    * @author <icromero@telconet.ec>
    * @return objeto - json
    * @version 1.0 
    */
    public function getComboBancosAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');    
        $strPeticion = $this->get('request');        
        $strEsTarjeta = $strPeticion->query->get('es_tarjeta');
        $emTelconet = $this->getDoctrine()->getManager("telconet");
        $arrayBancos = $emTelconet->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosParaDebitos();

        if($arrayBancos && count($arrayBancos)>0)
        {
            $intNumeroBanco = count($arrayBancos);           
            foreach($arrayBancos as $key => $objBanco)
            {                
                $arrayEncontrados[]=array('id_banco' =>$objBanco["id"],
                                         'descripcion_banco' =>trim($objBanco["descripcionBanco"]));
            }

            if($intNumeroBanco == 0)
            {
                $strResultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco' => 0 , 'descripcion_banco' =>
                                 'Ninguno','banco_id' => 0 , 'banco_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $strResultado);
            }
            else
            {
                $objData=json_encode($arrayEncontrados);
                $objJson= '{"total":"'.$intNumeroBanco.'","encontrados":'.$objData.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $objResponse->setContent($objJson);
        return $objResponse;
    }	

    /** 
    * Documentación para funcion 'getListadoTiposCuentaAction'.
    * funcion que muestra los tipo cuenta que pueden agregar a un grupo
    * @author <icromero@telconet.ec>
    * @return objeto - json
    * @version 1.0 
    */
    public function getListadoTiposCuentaAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $strPeticion = $this->get('request');
        $intIdBanco = $strPeticion->query->get('id_banco');
        $emTelconet = $this->getDoctrine()->getManager("telconet");
        if($intIdBanco!=0)
        {
            $arrayItems = $emTelconet->getRepository('schemaBundle:AdmiBancoTipoCuenta')
			->findTiposCuentaPorBancoPorVisibleFormatoParaDebitos($intIdBanco);
            if($arrayItems && count($arrayItems)>0)
            {
                $intNumeroItems = count($arrayItems);
                foreach($arrayItems as $key => $objItems)
                {
					if(strtoupper($objItems["descripcionCuenta"])=='CORRIENTE' || strtoupper($objItems["descripcionCuenta"])=='AHORRO')
                    {
                        $strDescripcion='CUENTA';
                    }
						
					else
                    {
                        $strDescripcion=$objItems["descripcionCuenta"];
                    }
							
                    $arrayEncontrados[]=array('id_cuenta' =>$objItems["id"],
                                            'descripcion_cuenta' =>trim($strDescripcion));
                }
                if($intNumeroItems == 0)
                {
                    $strResultado= array('total' => 1 ,
                                    'encontrados' => array('id_cuenta' => 0 , 'descripcion_cuenta' => 'Ninguno','cuenta_id' => 0 , 
                                    'cuenta_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                    $objJson = json_encode( $strResultado);
                }
                else
                {
                    $objData=json_encode($arrayEncontrados);
                    $objJson= '{"total":"'.$intNumeroItems.'","encontrados":'.$objData.'}';
                }
            }
            else
            {
                $objJson= '{"total":"0","encontrados":[]}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        $objResponse->setContent($objJson);
        return $objResponse;
    }

     /** 
    * Documentación para funcion 'creaGrupoAction'.
    * agrega un banco y su tipo cuenta a un grupo existente oun grupo nuevo
    * @author <icromero@telconet.ec>
    * @return objeto - json
    * @version 1.0 
    */
    public function creaGrupoAction()
    {
        $emFinanciero          = $this->get('doctrine')->getManager('telconet_financiero');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $objRequest            = $this->getRequest();
        $strIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa     = $objRequest->getSession()->get('prefijoEmpresa');
        $strIp                 = $objRequest->getClientIp();
        $strUser               = $objRequest->getSession()->get('user');
        $strMensaje            = "";
        $strCheckNuevoGrupo    = $objRequest->get('strCheckNuevoGrupo');
        $strComboGrupo         = $objRequest->get('strComboGrupo');
        $strComboBanco         = $objRequest->get('strComboBanco');
        $strComboTipoCuenta    = $objRequest->get('strComboTipoCuenta');
        $strTextGrupo          = $objRequest->get('strTextGrupo');
        $strEstado            = "Activo";
        $arrayParametros                  = array();
        $arrayParametros['strTipoCuenta'] = $strComboTipoCuenta;
        $arrayParametros['strBancoId']    = $strComboBanco;
        $arrayParametros['arrayEstados']  = array('Activo','Activo-debitos');
        $arrayAdmiBanco = $this->getDoctrine()->getManager("telconet")
                                              ->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                              ->findBancosTipoCuentaPorCriterio($arrayParametros);
        $serviceUtil          = $this->get('schema.Util');
        $arrayParametrosProcedure                  = array();
        $objAdmiTipoCuenta = $arrayAdmiBanco[0];
        
        try
        {
            if( !empty($strComboGrupo) )
            {
                $strMensaje = "Se agrego el Banco seleccionado al Grupo Base elegido, puede validarlo en la lista a continuacion";
                $this->get('session')->getFlashBag()->add('subida', $strMensaje);

                if($strCheckNuevoGrupo== 'false')
                {
                    $arrayParametrosProcedure['strGrupoDebitoId'] = $strComboGrupo;
                    $arrayParametrosProcedure['strBancoTipoCuentaId'] = $objAdmiTipoCuenta->getId();
                    $arrayParametrosProcedure['strUser'] =  $strUser;
                    $arrayParametrosProcedure['strEstado'] =  $strEstado;
                    $objAdmiGrupoDebito   = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($strComboGrupo);
                    //Inserto en AdmiGrupoArchivoDebitoDet
                    $arrayParametrosResultProcedure = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')
                    ->insertGrupoDet($arrayParametrosProcedure);
                    
                }else
                {
                    $arrayParametrosProcedure['strNombreGrupo'] = $strTextGrupo;
                    $arrayParametrosProcedure['strBancoTipoCuentaId'] = $objAdmiTipoCuenta->getId();
                    $arrayParametrosProcedure['strUser'] =  $strUser;
                    $arrayParametrosProcedure['strEstado'] =  $strEstado;
                    $arrayParametrosProcedure['strEmpresa'] =  $strIdEmpresa;
                    $objAdmiGrupoDebito   = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($strComboGrupo);
                    $arrayParametrosResultProcedure = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')
                    ->insertGrupoCabDet($arrayParametrosProcedure);
                    
                    
                }
                

            }
            else
            {
                $strMensaje = "No se pudo agregar el Banco al Grupo porque no se enviaron "
                ."los parámetros correspondientes. Por favor volver a intentarlo.";
                $this->get('session')->getFlashBag()->add('notice', $strMensaje);
            }
        }
        catch (\Exception $ex)
        {
            $emFinanciero->getConnection()->rollback();

            $serviceUtil->insertError('Telcos+', 'leeArchivoAction', $ex->getMessage(), $strUser, $strIp);
            $strTipoMensaje = 'notice';
            $strMensaje     = 'Ha ocurrido un error inesperado guardar Banco en Grupo Base' . $strMensaje;
        }
        $this->get('session')->getFlashBag()->add($strTipoMensaje, $strMensaje);
        return $this->redirect($this->generateUrl('financiero_grupoDebito',array()));       
    }
}
