<?php
namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;
use telconet\contabilizacionesBundle\Controller\NotaDebitoController;

class ContabilizarMasivamenteNDController extends Controller
{
    public function indexAction()
    {
		//$request = $this->getRequest();
		//$session=$request->getSession();
		echo "\n";
		echo "\n";
		echo "=============================================\n";
		echo "Contabilizacion Masiva de ND\n";
		echo "=============================================\n";
		echo "Inicio:".date("d-mm-Y H:m:i")."\n";
		echo "\n";
		//$empresa_id=$session->get('idEmpresa');
		$empresa_id='09';
		echo "Empresa a procesar: ".$empresa_id;
		//echo "<br />";
		echo "\n";
		$emfn = $this->getDoctrine()->getManager('telconet_financiero');
		
		$resultado=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->obtenerNDParaMigra();
		
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
					
					 
					$ndController=new NotaDebitoController();
					$ndController->setContainer($this->container);
					$ndController->contabilizarNotaDebitoAction($pago->getId());							
					
						
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

