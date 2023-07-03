<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;

/**
 * CargaInfoGisController, controlador que contiene los metodos para subir la informacion de GIS
 * 
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 21-07-2019
 * @since 1.0
 */
class CargaInfoGisController extends Controller
{

    /**
     * indexAction, index de la opcion
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 21-07-2019
     * @since 1.0
     * 
     * @return render Redirecciona al index de la opcion
     * 
     * @Secure(roles="ROLE_355-1")
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_355-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_355-1'; //Carga informacion GIS
        }
        return $this->render('tecnicoBundle:CargaInfoGis:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    } //indexAction

    /**
     * upLoadFileAction, sube el archivo que contiene la informacion de GIS
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 21-07-2019
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_355-1")
     */
    public function upLoadFileAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strIpClient            = $objRequest->getClientIp();
        $strUser                = $objSession->get("user");
        $serviceProcesoMasivo   = $this->get("tecnico.ProcesoMasivo");
        $serviceUtil            = $this->get('schema.Util');
        $objResponse            = new Response();
        $objReturnResponse      = new ReturnResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        try
        {

            $strHost                = $this->container->getParameter('host');
            $strPathParameters      = $this->container->getParameter('path_parameters');
            $strFileRoot            = $this->container->getParameter('ruta_uploadDataGis');
            $strPathTelcos          = $this->container->getParameter('path_telcos');
            $strRutaExcelDataGis    = $strPathTelcos . $this->container->getParameter('ruta_creaExcelDataGis');
            $objFile                = $objRequest->files;

            //Valida que el objeto $objFile no sea nulo y contenga al menos un elemento
            if($objFile && count($objFile) > 0)
            {
                $objArchivo = $objFile->get('archivo');
                //Valida que la variable $objArchivo este seteada
                if(isset($objArchivo))
                {
                    //Valida que el objeto $objArchivo no sea nulo y contenga al menos un elemento
                    if($objArchivo && count($objArchivo) > 0)
                    {
                        $archivo = $objArchivo->getClientOriginalName();
                        //Valida que $archivo sea diferente de vacio
                        if($archivo != "")
                        {
                            $arrayArchivo = explode('.', $archivo);
                            $countArray = count($arrayArchivo);
                            $extArchivo = $arrayArchivo[$countArray - 1];
                            if("xlsx" !== $extArchivo)
                            {
                                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " Archivo con extension incorrecta");
                                $objResponse->setContent(json_encode((array) $objReturnResponse));
                                return $objResponse;
                            }
                            $strPrefijo = substr(md5(uniqid(rand())), 0, 6);
                            $strNuevoNombre = $strUser . "_" . $strPrefijo . "." . $extArchivo;
                            $arrayDestino = array();
                            $arrayDestino['strPath'] = $strPathTelcos . $strFileRoot;
                            $strDestinoArchivo = $arrayDestino['strPath'] . $strNuevoNombre;
                            if($objReturnResponse::PROCESS_SUCCESS === $serviceUtil->creaDirectorio($arrayDestino)->getStrStatus())
                            {
                                //Mueve el archivo al ruta $arrayDestino['strPath'] y a la vez valida con la respuesta del metodo "move"
                                if($objArchivo->move($arrayDestino['strPath'], $strNuevoNombre))
                                {
                                    $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
                                    //Crea un array con los valores para ejecutar el jar ec.telcos.telconet.insertadatagis.jar
                                    $arrayParametros = ["nohup java -jar -Djava.security.egd=file:/dev/./urandom ",
                                                       $strPathTelcos . 
                                                       "telcos/src/telconet/tecnicoBundle/batch/ec.telcos.telconet.insertadatagis.jar ",
                                                       $strHost . " ",
                                                       $strPathParameters . " ",
                                                       $strPathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ ",
                                                       "leeDataGis ",
                                                       $strDestinoArchivo . " ",
                                                       $strRutaExcelDataGis . " ",
                                                       $strUser,
                                                       " ec.telcos.telconet.insertadatagis.jar ",
                                                       " & "];
                                    //Ejecuta el jar ec.telcos.telconet.insertadatagis.jar
                                    $serviceProcesoMasivo->execjar($arrayParametros);
                                }
                            }
                        }
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR);
            $serviceUtil->insertError('Telcos+', 'upLoadFileAction', $ex->getMessage(), $strUser, $strIpClient);
        }
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    } //upLoadFileAction
}
