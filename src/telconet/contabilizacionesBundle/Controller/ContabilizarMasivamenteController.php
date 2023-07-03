<?php
namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;
use telconet\contabilizacionesBundle\Controller\PagosController;
use telconet\contabilizacionesBundle\Controller\AnticiposController;
use telconet\contabilizacionesBundle\Controller\AnticiposSinClienteController;

class ContabilizarMasivamenteController extends Controller
{
    public function indexAction()
    {
		//$request = $this->getRequest();
		//$session=$request->getSession();
		echo "\n";
		echo "\n";
		echo "=============================================\n";
		echo "Contabilizacion Masiva de Pagos\n";
		echo "=============================================\n";
		echo "Inicio:".date("d-mm-Y H:m:i")."\n";
		echo "\n";
		//$empresa_id=$session->get('idEmpresa');
		$empresa_id='09';
		echo "Empresa a procesar: ".$empresa_id;
		//echo "<br />";
		echo "\n";
		$emfn = $this->getDoctrine()->getManager('telconet_financiero');
		
		//$resultado=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtenerPagosParaMigra();
		$resultado=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtenerPagosParaMigrarAA();
		//$resultado=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtenerRecaudacionesParaMigra();
		
		$listadoPagos=$resultado['registros'];
		$total=$resultado['total'];
		//echo "\n";		
		echo "Cantidad a procesar: ".$total;
		//echo "<br />";
		echo "\n";
		echo "\n";		
		echo "Procesando...\n";
		echo "\n";
		//die();
		$i=1;
		
		//Naf
		//$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		try
		{
			//$em_naf->getConnection()->beginTransaction();
		
			foreach($listadoPagos as $pago)
			{
					echo "registro:".$i;
					echo "\n";
					echo "Id pago procesado: ".$pago->getId();
					//echo "<br />";
					echo "\n";
					echo "Codigo: ".$pago->getTipoDocumentoId()->getCodigoTipoDocumento();
					//echo "<br />";
					echo "\n";
					
					if($pago->getTipoDocumentoId()->getCodigoTipoDocumento()=='PAG')
					{
					   //Contabilizacion del pago		   
						$pagosController=new PagosController();
						$pagosController->setContainer($this->container);
						//$pagosController->cabeceraPagosAction($pago->getId());	
						$pagosController->contabilizarPagosAction($pago->getId());
					}
					if($pago->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANT')
					{
					   //Contabilizacion del anticipo		   
						$pagosAntController=new AnticiposController();
						$pagosAntController->setContainer($this->container);
						$pagosAntController->contabilizarAnticiposAction($pago->getId());
						//$pagosAntController->cabeceraAnticiposAction($pago->getId());
					}
					elseif($pago->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS')
					{
					   //Contabilizacion del anticipo		   
						$pagosAntsController=new AnticiposSinClienteController();
						$pagosAntsController->setContainer($this->container);
						$pagosAntsController->contabilizarAnticiposSinClientesAction($pago->getId());
					}
						
					//echo "Debio";
					//echo "<br />";
					echo "\n";
					echo "\n";
					$i++;
			
			}
			//$em_naf->getConnection()->commit();
			//$em_naf->close();
		}
		catch (\Exception $e) {
			echo "ERROR";
			//$em_naf->getConnection()->rollback();
			echo $msg = "Se hace rollback!";
			echo $code = "OK";
			//$em_naf->close();
			throw $e;
		}
				
		echo "Total procesados:".$i;
		echo "\n";
		echo "=============================================\n";
		echo "Fin:".date("d-mm-Y H:m:i")."\n";
    }
}

?>

