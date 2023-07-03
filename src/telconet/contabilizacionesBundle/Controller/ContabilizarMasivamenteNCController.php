<?php
namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;
use telconet\contabilizacionesBundle\Controller\NotaCreditoController;

class ContabilizarMasivamenteNCController extends Controller
{
    public function indexAction()
    {
		//$request = $this->getRequest();
		//$session=$request->getSession();
		echo "\n";
		echo "\n";
		echo "=============================================\n";
		echo "Contabilizacion Masiva de NC\n";
		echo "=============================================\n";
		echo "Inicio:".date("d-mm-Y H:m:i")."\n";
		echo "\n";
		//$empresa_id=$session->get('idEmpresa');
		$empresa_id='09';
		echo "Empresa a procesar: ".$empresa_id;
		//echo "<br />";
		echo "\n";
		$emfn = $this->getDoctrine()->getManager('telconet_financiero');
		
		$resultado=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->obtenerNCParaMigra();
		
		
		$listadoNC=$resultado['registros'];
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
        foreach($listadoNC as $nota)
        {
				echo "registro:".$i;
				echo "\n";
				echo "Id nc a procesar: ".$nota->getId();
				//echo "<br />";
				echo "\n";
				echo "Codigo: ".$nota->getTipoDocumentoId()->getCodigoTipoDocumento();
				//echo "<br />";
				echo "\n";
				$nc=new NotaCreditoController();
				$nc->setContainer($this->container);
				$nc->contabilizarNotaCreditoAction($nota->getId());
					
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

