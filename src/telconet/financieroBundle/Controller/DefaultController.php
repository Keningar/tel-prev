<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller implements TokenAuthenticatedController
{
    public function indexAction($name)
    {
        return $this->render('financieroBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function menuAction($opcion_menu)
    {
        if (true === $this->get('security.context')->isGranted('ROLE_64-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'financiero','opcion_menu' =>$opcion_menu));
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
    
    public function ajaxFacturasAbiertasMesAction(){
         //obtiene fechas del ultimo mes
        $fechaActual=date('l Y');
        $fechaActual="1 ".$fechaActual;
        $fechaComparacion = strtotime($fechaActual);
        $calculo= strtotime("31 days", $fechaComparacion); //Le aumentamos 31 dias
        $fechaFin= date("Y-m-d", $calculo);
	$fechaIni= date('Y-m')."-01";
        $request=$this->getRequest();
        $idOficina=$request->getSession()->get('idOficina');
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $facturasAbiertas=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findFacturasAbiertasPorFecha($fechaIni, $fechaFin, $idOficina);
//print_r($facturasAbiertas);die;
        foreach($facturasAbiertas as $dato){
            if ($dato['total'])
                $total=$dato['total'];
            else
                $total=0;
            $arreglo[]= array(
                        'name'=> sprintf("%s",'Facturas abiertas (en $)'),
                        'data1'=> sprintf("%s",$total)
                        );  
        }	
        if (empty($arreglo)){
                $arreglo[]= array(
                        'name'=> "Facturas abiertas (en $)",
                        'data1'=> "0"
                        );  
        }
        $response = new Response(json_encode(array('items'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;        
    } 

    public function ajaxFacturasVencidasAction(){
        $request=$this->getRequest();
        $idOficina=$request->getSession()->get('idOficina');
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $facturas=$em->getRepository('schemaBundle:FacturasVencidas')->findByOficinaId($idOficina);
        
                    $arrFact['0-15']= array('name'=> "0-15",'data1'=> 0);
                    $arrFact['16-30']= array('name'=> "16-30",'data1'=> 0);
                    $arrFact['31-45']= array('name'=> "31-45",'data1'=> 0);
                    $arrFact['+45']= array('name'=> "+45",'data1'=> 0);
        if($facturas){
            foreach($facturas as $dato){
                if($dato->getRango()=='0-15'){
                    $arrFact['0-15']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='16-30'){
                    $arrFact['16-30']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='31-45'){
                    $arrFact['31-45']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='+45'){
                    $arrFact['+45']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }
            }	
        }
        foreach ($arrFact as $dato1){
            $arreglo[]=array('name'=>$dato1['name'],'data1'=>$dato1['data1']);
        }
        //die;
        $response = new Response(json_encode(array('items'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;        
    }  


    public function ajaxFacturasPeriodoAction(){
        $request=$this->getRequest();
        $idOficina=$request->getSession()->get('idOficina');
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $facturas=$em->getRepository('schemaBundle:FacturasPeriodo')->findByOficinaId($idOficina);
        //print_r($facturas);die;
                    $arrFact['hoy']= array('name'=> "hoy",'data1'=> 0);
                    $arrFact['semana']= array('name'=> "semana",'data1'=> 0);
                    $arrFact['mes']= array('name'=> "mes",'data1'=> 0);
                    $arrFact['trimestre']= array('name'=> "trimestre",'data1'=> 0);        
                    $arrFact['anio']= array('name'=> "anio",'data1'=> 0);        
        if($facturas){
            foreach($facturas as $dato){
                //print_r($dato);
                if($dato->getRango()=='hoy'){
                    $arrFact['hoy']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='semana'){
                    $arrFact['semana']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='mes'){
                    $arrFact['mes']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='trimestre'){
                    $arrFact['trimestre']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='anio'){
                    $arrFact['anio']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }                
            }	

        }
        foreach ($arrFact as $dato1){
            $arreglo[]=array('name'=>$dato1['name'],'data1'=>$dato1['data1']);
        }        
            //echo 'hola';die;
        $response = new Response(json_encode(array('items'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;        
    }      

    public function ajaxPagosPeriodoAction(){
        $request=$this->getRequest();
        $idOficina=$request->getSession()->get('idOficina');
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $pagos=$em->getRepository('schemaBundle:PagosPeriodo')->findByOficinaId($idOficina);
        //print_r($facturas);die;
                    $arrPago['hoy']= array('name'=> "hoy",'data1'=> 0);
                    $arrPago['semana']= array('name'=> "semana",'data1'=> 0);
                    $arrPago['mes']= array('name'=> "mes",'data1'=> 0);
                    $arrPago['trimestre']= array('name'=> "trimestre",'data1'=> 0);        
                    $arrPago['anio']= array('name'=> "anio",'data1'=> 0);          
        if($pagos){
            foreach($pagos as $dato){
                if($dato->getRango()=='hoy'){
                    $arrPago['hoy']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='semana'){
                    $arrPago['semana']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='mes'){
                    $arrPago['mes']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='trimestre'){
                    $arrPago['trimestre']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='anio'){
                    $arrPago['anio']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }   	

        } 
        }
        foreach ($arrPago as $dato1){
            $arreglo[]=array('name'=>$dato1['name'],'data1'=>$dato1['data1']);
        }         
            //echo 'hola';die;
        $response = new Response(json_encode(array('items'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;        
    }   
    
    public function ajaxAdeudadoPeriodoAction(){
        $request=$this->getRequest();
        $idOficina=$request->getSession()->get('idOficina');
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $facturas=$em->getRepository('schemaBundle:FacturasPeriodo')->findByOficinaId($idOficina);
        $pagos=$em->getRepository('schemaBundle:PagosPeriodo')->findByOficinaId($idOficina);
        //print_r($facturas);die;
        $arrAdeudado['hoy']= array('name'=> "hoy",'data1'=> 0);
        $arrAdeudado['semana']= array('name'=> "semana",'data1'=> 0);
        $arrAdeudado['mes']= array('name'=> "mes",'data1'=> 0);
        $arrAdeudado['trimestre']= array('name'=> "trimestre",'data1'=> 0);        
        $arrAdeudado['anio']= array('name'=> "anio",'data1'=> 0); 
        $arrFact=$this->armaArregloFactura();
        $arrPago=$this->armaArregloPagos();
        if($pagos){
            foreach($pagos as $dato){
                if($dato->getRango()=='hoy'){
                    $arrPago['hoy']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='semana'){
                    $arrPago['semana']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='mes'){
                    $arrPago['mes']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='trimestre'){
                    $arrPago['trimestre']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='anio'){
                    $arrPago['anio']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }
            } 
        }
        if($facturas){
            foreach($facturas as $dato){
                //print_r($dato);
                if($dato->getRango()=='hoy'){
                    $arrFact['hoy']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='semana'){
                    $arrFact['semana']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='mes'){
                    $arrFact['mes']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='trimestre'){
                    $arrFact['trimestre']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }elseif($dato->getRango()=='anio'){
                    $arrFact['anio']= array('name'=> $dato->getRango(),'data1'=> $dato->getTotal());
                }                
            }	

        }        

        $arreglo[]=array('name'=>'hoy','data1'=>$arrFact['hoy']['data1']-$arrPago['hoy']['data1']);
        $arreglo[]=array('name'=>'semana','data1'=>$arrFact['semana']['data1']-$arrPago['semana']['data1']);
        $arreglo[]=array('name'=>'mes','data1'=>$arrFact['mes']['data1']-$arrPago['mes']['data1']);
        $arreglo[]=array('name'=>'trimestre','data1'=>$arrFact['trimestre']['data1']-$arrPago['trimestre']['data1']);
        $arreglo[]=array('name'=>'anio','data1'=>$arrFact['anio']['data1']-$arrPago['anio']['data1']);
        
                 
            //echo 'hola';die;
        $response = new Response(json_encode(array('items'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;        
    }
    
    function armaArregloPagos(){
                    $arrPago['hoy']= array('name'=> "hoy",'data1'=> 0);
                    $arrPago['semana']= array('name'=> "semana",'data1'=> 0);
                    $arrPago['mes']= array('name'=> "mes",'data1'=> 0);
                    $arrPago['trimestre']= array('name'=> "trimestre",'data1'=> 0);        
                    $arrPago['anio']= array('name'=> "anio",'data1'=> 0); 
                    return $arrPago;
    }
    function armaArregloFactura(){
                     $arrFact['hoy']= array('name'=> "hoy",'data1'=> 0);
                    $arrFact['semana']= array('name'=> "semana",'data1'=> 0);
                    $arrFact['mes']= array('name'=> "mes",'data1'=> 0);
                    $arrFact['trimestre']= array('name'=> "trimestre",'data1'=> 0);        
                    $arrFact['anio']= array('name'=> "anio",'data1'=> 0);        
                    return $arrFact;
    }
}
