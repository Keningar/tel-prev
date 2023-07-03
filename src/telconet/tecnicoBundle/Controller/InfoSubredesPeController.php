<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Form\InfoSubredType;
use telconet\schemaBundle\Form\InfoSubredesType;
use telconet\schemaBundle\Form\InfoElementoPopType;
use telconet\tecnicoBundle\Resources\util\Util;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Entity\InfoSubred;
use telconet\schemaBundle\Entity\AdmiParametroDet; 
use telconet\schemaBundle\Entity\InfoSubredTag;
use telconet\schemaBundle\Entity\AdmiPolicy;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use \telconet\schemaBundle\Entity\ReturnResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class InfoSubredesPeController extends Controller
 *
 * logica de las subredes asignadas a los Pe
 *
 * @author Jonathan Montecé <jmontece@telconet.ec>
 * @version 1.0 10-08-2021
 */

class InfoSubredesPeController extends Controller
{ 
    
    /**
    * indexAction
    * funcion que administra las nuevas subredes para los Pe a nivel nacional
    * Se declaran los permisos necesarios para el procesamiento
    *
    * @author Jonathan Montecé <jmontece@telconet.ec>
    * @version 1.0 10-08-2021
    */ 
    
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_468-8397'))
        {
            $arrayRolesPermitidos[] = 'ROLE_468-8397'; 
        }
        if (true === $this->get('security.context')->isGranted('ROLE_468-8398'))
        {
            $arrayRolesPermitidos[] = 'ROLE_468-8398'; 
        }
        return $this->render('tecnicoBundle:InfoSubRed:index.html.twig', array(
            'rolesPermitidos'         => $arrayRolesPermitidos
            
        ));
    }

    public function createSubredAction()
    {             
            
        $arrayRolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_468-8399'))
        {
            $arrayRolesPermitidos[] = 'ROLE_468-8399'; 
        }

        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $objParametroDet        = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->findOneBy(array('descripcion'=> 'TIPOS DE RED SUBREDES POR PE',
                                                       'estado'=> 'Activo'));
       
        $arrayParametroMascara  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('MASCARA_SUBREDES',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'MASCARAS DE RED PARA SUBREDES POR PE', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
        $arrayParametroTipo     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('TIPO_RED',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'TIPOS DE RED SUBREDES POR PE', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
        $arrayParametroUso      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('TIPO_USO',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'TIPOS DE USO EN SUBREDES POR PE', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
        $arrayTipos            = array();
        $arrayUsos             = array();
        $arrayMascaras         = array();
        $arrayPe               = array();


        
        foreach($arrayParametroUso as $arrayUsos)
        {
            $arrayResultadoUso[]  = $arrayUsos['valor1'];
        }

        foreach($arrayParametroTipo as $arrayTipos)
        {
            $arrayResultadoTipo[]  = $arrayTipos['valor1'];
        }

        foreach($arrayParametroMascara as $arrayMascaras)
        {
            $arrayResultadoMascaras[]  = $arrayMascaras['valor1'];
        }

        
        return $this->render('tecnicoBundle:InfoSubRed:new.html.twig', array(
            'rolesPermitidos'         => $arrayRolesPermitidos,
            'valorParametroDet'       => $objParametroDet->getValor1(),
            'arrayTipos'              => $arrayResultadoTipo,
            'arrayUsos'               => $arrayResultadoUso,
            'arrayMascaras'           => $arrayResultadoMascaras
            
        ));



        
        
    }

    public function getUsosSubredGrid()
    {
        
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametroUso      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('TIPO_USO',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'TIPOS DE USO EN SUBREDES POR PE', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
         $arrayUsos             = array();
         foreach($arrayParametroUso as $arrayUsos)
         {
             $arrayResultadoUso[]  = $arrayUsos['valor1'];
         }
         return $arrayResultadoUso;

    }

    public function getLikeGrid()
    {
        
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametroUso      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('TIPOS_ELEMENTO_SUBREDES',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'QUERY LIKE PE EN SUBREDES', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
         $arrayUsos             = array();
         foreach($arrayParametroUso as $arrayUsos)
         {
             $arrayResultadoUso[]  = $arrayUsos['valor1'];
         }
         return $arrayResultadoUso;

    }

    public function getLikeRoGrid()
    {
        
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametroUso      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('TIPOS_ELEMENTO_SUBREDES',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'QUERY LIKE RO EN SUBREDES', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
         $arrayUsos             = array();
         foreach($arrayParametroUso as $arrayUsos)
         {
             $arrayResultadoUso[]  = $arrayUsos['valor1'];
         }
         return $arrayResultadoUso;

    }

    public function getTipoElementoGrid()
    {
        
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametroUso      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('TIPOS_ELEMENTO_SUBREDES',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'QUERY TIPO ELEMENTO PE EN SUBREDES', //descripcion det
                                                          '','','','','',
                                                          '10'); //empresa
         $arrayUsos             = array();
         foreach($arrayParametroUso as $arrayUsos)
         {
             $arrayResultadoUso[]  = $arrayUsos['valor1'];
         }
         return $arrayResultadoUso;

    }

    public function getEncontradosPeAction()
    {
        $arraySession        = $this->get('session');
        $intIdEmpresa        = $arraySession->get('idEmpresa'); 
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRequest = $this->get('request');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strLike             = $this->getLikeGrid();
        $strLikeRo           = $this->getLikeRoGrid();
        $strTipoElemento     = $this->getTipoElementoGrid();
        
        
        $arrayParams["strLike"]          = $strLike[0];
        $arrayParams["strLikeRo"]        = $strLikeRo[0];
        $arrayParams["strTipoElemento"] = $strTipoElemento[0];
        if($intIdEmpresa == 10)
        {
            $strJsonParamPe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->getElemVisInfSubRed($arrayParams);
        
            $objRespuesta->setContent($strJsonParamPe);

            return $objRespuesta;
        }

    }
   
    public function guardarPeAction()
    {
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->getRequest();
        $strIpSubRed            = $objRequest->get('ip_subred');
        $strMascara             = $objRequest->get('mascara_subred');
        $strIdPe                = $objRequest->get('id_pe');
        $strTipoSubRed          = $objRequest->get('tipo_red');
        $strUso                 = $objRequest->get('uso');
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get("user");
        $strEmpresaCod          = $objSession->get("idEmpresa");
        $strIpClient            = $objRequest->getClientIp();
        $strAnillo              = $objRequest->get("anillo");



        $objInfoElemento= $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                     ->findOneBy(array('id'=> $strIdPe,
                                                       'estado'=> 'Activo'));
        $arrayMascara      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('MASCARA_SUBREDES',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'SUBREDES_PE',//proceso cab
                                                          'MASCARAS DE RED PARA SUBREDES POR PE', //descripcion det
                                                          $strMascara,'','','','',
                                                          $strEmpresaCod); //empresa

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $emInfraestructura->getConnection()->beginTransaction(); 
        try
        {
            //Calculo ip
            if(!empty($strIpSubRed))
            {   
                if ($strMascara === "/31") 
                {
                 //Calculo de gateway
                 $strIpSubRedGateway  = $strIpSubRed;
                 //Calculo de ip inicial
                 $strIpInicial = $strIpSubRed;
                 //Calculo de ip final
                 $arrayIpSubRedfinal  = explode(".", $strIpSubRed);
                 $arrayIpSubRedfinal[3] = $arrayIpSubRedfinal[3] + $arrayMascara['valor3'];                
                 $strValoripFinal = implode(".", $arrayIpSubRedfinal);
                 }
                else
                {
                //Calculo de gateway
                $arrayIpSubRedGateway  = explode(".", $strIpSubRed);
                $intValorUltimoIp = intval($arrayIpSubRedGateway[3]);
                $intValorUltimoIp =  $intValorUltimoIp+1;
                $arrayIpSubRedGateway[3]  = strval($intValorUltimoIp);
                $strIpSubRedGateway  = implode(".", $arrayIpSubRedGateway);
                //Calculo de ip inicial
                $arrayIpGateway = explode(".", $strIpSubRedGateway);
                $intValorUltimoGateway = intval($arrayIpGateway[3]);
                $intValorUltimoGateway = $intValorUltimoGateway+1;
                $arrayIpGateway[3]= strval($intValorUltimoGateway);
                $strIpInicial = implode(".", $arrayIpGateway);
                //Calculo de ip final
                $arrayIpSubRedfinal  = explode(".", $strIpSubRed);
                $arrayIpSubRedfinal[3] = $arrayIpSubRedfinal[3] + $arrayMascara['valor3'];                
                $strValoripFinal = implode(".", $arrayIpSubRedfinal);
                }
                //Concatenado de ip ingresada para guardado en base
                $strIpSubredConcatenado =  $strIpSubRed.=$arrayMascara['valor1'];
               

            }
            $strMascaraNumero = ltrim($strMascara,'/');
            $intAnilloSet = 0;
            //Se comprueba que no exista Subred repetida
            $objSubRedRepetida = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                    ->findOneBy(array("subred"   => $strIpSubredConcatenado));
            
            if(is_object($objSubRedRepetida))
            {
                $objResultado = json_encode(array('success' => false, 'mensaje' => 'La Subred ya se encuentra registrada'));
                $objRespuesta->setContent($objResultado);
            }
            else
                {
                //Se setea la data en los campos correspondientes
            $objInfoSubred = new InfoSubred();
            $objInfoSubred->setRedId($strMascaraNumero);
            $objInfoSubred->setSubRed($strIpSubredConcatenado);
            $objInfoSubred->setMascara($arrayMascara['valor2']);
            $objInfoSubred->setGateway($strIpSubRedGateway);
            $objInfoSubred->setUsrCreacion($strUsrCreacion);
            $objInfoSubred->setFeCreacion(new \DateTime('now'));
            $objInfoSubred->setIpCreacion($strIpClient);
            $objInfoSubred->setEstado('Activo');
            $objInfoSubred->setElementoId($objInfoElemento);
            $objInfoSubred->setIpInicial($strIpInicial);
            $objInfoSubred->setIpFinal($strValoripFinal);
            $objInfoSubred->setTipo($strTipoSubRed);
            $objInfoSubred->setUso($strUso);
            $objInfoSubred->setEmpresaCod($strEmpresaCod);
            $objInfoSubred->setVersionIp('IPv4');
            $objInfoSubred->setAnillo($intAnilloSet);


            $emInfraestructura->persist($objInfoSubred);
            $emInfraestructura->flush();

            $emInfraestructura->getConnection()->commit();

            $objResultado = json_encode(array('success' => true, 'mensaje' => 'OK'));
            
             }
           
        
        }
        catch (\Exception $ex) 
        {
            $serviceUtil->insertError('Telcos+', 'Subred', $ex->getMessage(), $strUsrCreacion, $strIpClient);
            $emInfraestructura->getConnection()->rollback();
            $objResultado = json_encode(array('success' => false, 'mensaje' => 'Se presentaron errores al guardar la Informacion'));
            $objRespuesta->setContent($objResultado);
        }
        $emInfraestructura->getConnection()->close();
        $objRespuesta->setContent($objResultado);

        return $objRespuesta;
 
    }

    /**
     * getUsosSubredAction
     * funcion que obtiene los tipo de Uso para las subredes por Pe
     *
     * @return json
     * 
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */
    public function getUsosSubredAction()
    {
        
        $arrayRespuesta              = new Response();
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayPeticion               = $this->get('request');
        

        $arrayTmpParametros     = array( 'estadoActivo' => 'Activo' );
        
        $objResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getJsonUsoSubred( $arrayTmpParametros );

        $arrayRespuesta->setContent($objResult);

        return $arrayRespuesta;
    }

    /**
     * getTipoSubredAction
     * funcion que obtiene el tipo de Subred para los Pe
     *
     * @return json
     * 
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 13-09-2021
     */
    public function getTipoSubredAction()
    {
        $arrayRespuesta              = new Response();
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayPeticion               = $this->get('request');
        

        $arrayTmpParametros     = array( 'estadoActivo' => 'Activo');
        
        $objResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getJsonTipoSubred( $arrayTmpParametros );

        $arrayRespuesta->setContent($objResult);

        return $arrayRespuesta;
    }

    /**
     * getEstadosAction
     * funcion que obtiene los estados de las Subredes
     *
     * @return json
     * 
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 1.0 22-09-2021
     */
    public function getEstadosAction()
    {
        $arrayRespuesta              = new Response();
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $arrayPeticion               = $this->get('request');
        

        $arrayTmpParametros     = array( 'estadoActivo' => 'Activo');
        
        $objResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getJsonEstadoSubred( $arrayTmpParametros );

        $arrayRespuesta->setContent($objResult);

        return $arrayRespuesta;
    }


     /**
    * getEncontrados
    * obtiene todos los registros de las subredes registradas para mostrarlos en el grid
    *
    * @return json con la data 
    *
    * @author Jonathan Montecé <jmontece@telconet.ec>
    * @version 1.0 13-09-2021
    */    
    public function getEncontradosGridAction()
    {
        ini_set('max_execution_time', 3000000);
        $arrayRespuesta      = new JsonResponse();
        $arraySession        = $this->get('session');
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $objPeticion         = $this->get('request');        
        $strNombreElemento   = $objPeticion->query->get('nombreElemento');
        $strEstado           = $objPeticion->query->get('estado');
        $intIdEmpresa        = $arraySession->get('idEmpresa');       
        $strStart            = $objPeticion->query->get('start');
        $strLimit            = $objPeticion->query->get('limit');
        $strConsultaUsos     = $this->getUsosSubredGrid();
        $strLike             = $this->getLikeGrid();
        $strLikeRo           = $this->getLikeRoGrid();
        $strTipoElemento     = $this->getTipoElementoGrid();
        $strNombrePe         = $objPeticion->query->get('nombrePe') ? $objPeticion->query->get('nombrePe') : "";
        $strUso              = $objPeticion->query->get('uso') ? $objPeticion->query->get('uso') : "";
        $strTipo             = $objPeticion->query->get('tipo') ? $objPeticion->query->get('tipo') : "";
        $strFechaDesde       = $objPeticion->query->get('fecha_desde') ? $objPeticion->query->get('fecha_desde') : "";
        $strFechaHasta       = $objPeticion->query->get('fecha_hasta') ? $objPeticion->query->get('fecha_hasta') : "";
        $strEstadoSubred     = $objPeticion->query->get('estado_subred') ? $objPeticion->query->get('estado_subred') : "";
        $strSubred           = $objPeticion->query->get('subred') ? $objPeticion->query->get('subred') : "";





        
        $arrayParametros = array();
        $arrayParametros['start']           = $strStart;
        $arrayParametros['limit']           = $strLimit;
        $arrayParametros["codEmpresa"]      = $intIdEmpresa;
        $arrayParametros["nombreSubred"]    = $strNombreElemento;        
        $arrayParametros["estadoElemento"]  = $strEstado;
        $arrayParametros["strNombrePe"]     = $strNombrePe;
        $arrayParametros["strUso"]          = $strUso;
        $arrayParametros["strTipo"]         = $strTipo;
        $arrayParametros["strFechaDesde"]   = $strFechaDesde;
        $arrayParametros["strFechaHasta"]   = $strFechaHasta;
        $arrayParametros["strEstadoSubred"] = $strEstadoSubred;
        $arrayParametros["strSubred"]       = $strSubred;
        $arrayParametros["strLike"]         = $strLike;
        $arrayParametros["strLikeRo"]       = $strLikeRo;
        $arrayParametros["strTipoElemento"] = $strTipoElemento;







        if($intIdEmpresa == 10)
        {
            $objRespSol = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoElemento')
            ->getJsonRegistrosSubredes($arrayParametros);

            $arrayRespuesta->setContent($objRespSol);

            return $arrayRespuesta;
        }
       
    }

    /**
    * editAction
    * función que renderiza el formulario para la edición de los datos de la Subred
    *
    * @param integer $id
    * 
    * @author Jonathan Montecé <jmontece@telconet.ec>
    * @version 1.0 13-09-2021
    * 
    */     
    public function editAction($intId)
    {
        $objRequest      = $this->get('request');
        $arraySession    = $objRequest->getSession();
        $intEmpresaId    = $arraySession->get('idEmpresa');
        $emInfraestructura   = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strCapacidad  = '';

        if (null == $objElem = $emInfraestructura->find('schemaBundle:InfoSubred', $intId)) 
        {
            throw $this->createNotFoundException('No existe el elemento que se quiere modificar');
        }
        else
        {            
            //Busco dentro del entity InfoSubred el id de la subred
            $objRelacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoSubred')->findOneBy(array('id'=>$objElem->getId()));
            $objElementoContenedor = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objRelacionElemento->getElementoId());
            if($objElementoContenedor)
            {
                $strNombreElemCont = $objElementoContenedor->getNombreElemento() ;
                $intIdElemento = $objElementoContenedor->getId() ;

            }
           
            
        }

        $objFormulario =$this->createForm(new InfoSubredesType(array("empresaId"=>$intEmpresaId)), $objElem);
        
        return $this->render(   'tecnicoBundle:InfoSubRed:edit.html.twig', array(
                                'edit_form'                 => $objFormulario->createView(),
                                'objElemento'               => $objElem,
                                'intIdElemento'             => $intIdElemento,
                                'idElementoPe'              => $objElem->getElementoId())

                                //'ip'                        => $ip)
                            );
    }

    /**
    * updateAction
    * funcion que actualiza datos de la Subred en la BD
    * @param integer $intId
    * 
    * @author Jonathan Montecé <jmontece@telconet.ec>
    * @version 1.0 13-09-2021
    * 
    */     
    public function updateAction()
    {
        $objRequest      = $this->get('request');
        $arraySession    = $objRequest->getSession();
        $intId    = $objRequest->get('strInt');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $strIpInicial = '';
        $strIpFinal = '';
        $entitySubred = $emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intId);
        
        if(is_object($entitySubred))
        {
            $strIpInicial = $entitySubred->getIpInicial();
            $strIpFinal = $entitySubred->getIpFinal();
        }
        $objElementoContenedor = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($entitySubred->getElementoId());
        if(is_object($objElementoContenedor))
        {
            $strNombreElemCont = $objElementoContenedor->getNombreElemento();
            $intIdElemento = $objElementoContenedor->getId();

        }
        
        if (!$entitySubred) 
        {
            throw $this->createNotFoundException('No se puede encontrar la entidad.');
        }

        $objRequest            = $this->get('request');
        $arraySession          = $objRequest->getSession();
        $strUserSession        = $arraySession->get('user');
        $strDatetimeAct        = new \DateTime('now');
        $strIpUserSession      = $objRequest->getClientIp();     
        $strSubred             = $objRequest->request->get('subred') ? $objRequest->request->get('subred') : 0;
        $strUso                = $objRequest->request->get('comboUsos') ? $objRequest->request->get('comboUsos') : 0;
        $strTipo               = $objRequest->request->get('comboTipos') ? $objRequest->request->get('comboTipos') : 0;
        $strEstado             = $objRequest->request->get('comboEstados') ? $objRequest->request->get('comboEstados') : 0;
        $strNombrePe           = $objRequest->request->get('comboPe') ? $objRequest->request->get('comboPe') : 0;

        $strCadenaHistorial = 'Datos modificados: Subred:'.$strSubred.',Nombre Pe:'
                            .$strNombrePe.',Uso:'.$strUso.',Tipo:'.$strTipo.',Estado:'.$strEstado;
        $emInfraestructura->getConnection()->beginTransaction();
        $objRespuesta = new JsonResponse();
        
        try
        {
            
           //lógica para validar servicios activos dentro de la subred para no editar servicios activos
            if(!empty($strIpInicial) && !empty($strIpFinal))
            {   //separo octetos de la ip inicial -final
                $arrayIpInicial  = explode(".", $strIpInicial);
                $arrayIpFinal  = explode(".", $strIpFinal);
                
                //ultimo octeto de la ip inicial -final
                $intUltimoInicial = intval($arrayIpInicial[3]);
                $intUltimoFinal = intval($arrayIpFinal[3]);

                $arrayIps = array();
                
                for ($intFor = $intUltimoInicial; $intFor <= $intUltimoFinal; $intFor++) 
                {
                    unset($arrayIpInicial[3]);
                    $arrayIpInicial[3]= strval($intUltimoInicial);
                    $arrayIps[] = implode(".", $arrayIpInicial);
                    $intUltimoInicial = $intUltimoInicial +1;
                }
                if(!empty($arrayIps))
                {
                    foreach($arrayIps as $strIp)
                    {
                        $objInfoIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array("ip"   => $strIp,
                                                             "estado"   => 'Activo'));
                        if(is_object($objInfoIp))
                        {
                            $arrayInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array("id"   => $objInfoIp->getServicioId(),
                                                            "estado"=> array('Activo','In-Corte')));
                            if($arrayInfoServicio)
                            {
                                
                                throw new \Exception("No se puede editar esta subred por tener servicios activos");

                                
                                
                            }
                        }
                       
                    }
                }
            }  
            
         
            
            
            
            //verificar que el nombre del elemento no se repita
                    $objElemRepe = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                           ->findOneBy(array("nombreElemento"   => $strNombrePe));
                    
                    if(is_object($objElemRepe))
                    {
                        
                     $objElemRepe->getId();
                        
                    }
                
                //Se guarda los elementos editados para la Subred  
                 $entitySubred->setElementoId($objElemRepe); 
                 $entitySubred->setSubred($strSubred);       
                 $entitySubred->setUso($strUso);
                 $entitySubred->setTipo($strTipo);
                 $entitySubred->setEstado($strEstado);
                 $emInfraestructura->persist($entitySubred);
                 $emInfraestructura->flush();
                

                //Se guarda historial de modificaciones de la subred
                 $entityInfoHisElem = new InfoHistorialElemento();
                 $entityInfoHisElem->setElementoId($objElemRepe);
                 $entityInfoHisElem->setEstadoElemento($strEstado);
                 $entityInfoHisElem->setObservacion($strCadenaHistorial);
                 $entityInfoHisElem->setUsrCreacion($strUserSession);
                 $entityInfoHisElem->setFeCreacion($strDatetimeAct);
                 $entityInfoHisElem->setIpCreacion($strIpUserSession);
                 $emInfraestructura->persist($entityInfoHisElem);
                 $emInfraestructura->flush();

                 $emInfraestructura->getConnection()->commit();

                 
                $objRespuesta->setContent(json_encode(array('status' => 'OK', 'mensaje' => 'OK')));

                 
        }        
        catch(\Exception $e)
        {
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            $strMsjError = "Error: " . $e->getMessage();
           
            $objRespuesta->setContent(json_encode(array('status' => 'ERROR', 'mensaje' => $strMsjError)));
            
            
        }
        return $objRespuesta;
        
    }

    
}

