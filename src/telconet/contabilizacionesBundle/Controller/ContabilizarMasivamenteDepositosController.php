<?php
namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;
use telconet\contabilizacionesBundle\Controller\DepositosController;

class ContabilizarMasivamenteDepositosController extends Controller
{
    public function indexAction()
    {
		//$request = $this->getRequest();
		//$session=$request->getSession();
		echo "\n";
		echo "\n";
		echo "=============================================\n";
		echo "Contabilizacion Masiva de Depositos\n";
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
		$resultado=$emfn->getRepository('schemaBundle:InfoDeposito')->obtenerDepositosParaMigrarAA();
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
        foreach($listadoPagos as $pago)
        {
				echo "registro:".$i;
				echo "\n";
				echo "Id pago procesado: ".$pago->getId();
				//echo "<br />";
				echo "\n";
				
			   //Contabilizacion del deposito		   
				$depositoController=new DepositosController();
				$depositoController->setContainer($this->container);
				$depositoController->contabilizarDepositosAction($pago->getId());
									
				//echo "Debio";
				//echo "<br />";
				echo "\n";
				echo "\n";
				$i++;
			
		}
		echo "Total procesados:".$i;
		echo "\n";
		echo "=============================================\n";
		echo "Fin:".date("d-mm-Y H:m:i")."\n";
    }
}

?>

