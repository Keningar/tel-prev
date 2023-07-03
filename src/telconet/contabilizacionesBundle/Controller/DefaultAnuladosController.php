<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class DefaultAnuladosController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('contabilizacionesBundle:Default:index.html.twig', array('name' => $name));
    }
    
    /**
     * Proceso:
     * - Busco la factura en las tablas info_documento_financiero_det
     * - Si es plan :
     * 	+ Ingreso al listado de los planes hago join con la de productos 
     * 	+ y con la de impuestos para saber si tiene cada producto y cta contable
     * - Si es producto:
     * 	+ Ya tengo los productos y hago join solo con los impuestos
     * 
     * @author Desarrollo Inicial
     * @version 1.0 
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.1 2016-04-25 - Colocar en blanco (" ") los campos llenados por: ctaContableProd y ctaContableProdNc
     * 
     * @param integer $id_factura
     * 
     * @return Response
     **/
    public function contabilizarFacturaAction($id_factura)
    {
		$em = $this->getDoctrine()->getManager("telconet_financiero");
		$request = $this->getRequest();
		$session=$request->getSession();
		
		$empresa_id=$session->get('idEmpresa');
		$oficina_id=$session->get('idOficina');
		$ptocliente=$session->get('ptoCliente');
		$nombre_oficina=$session->get('nombreOficina');
		
		
		$info_total = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getValorCtaCliente($id_factura,$empresa_id,$oficina_id);
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		$em_naf->getConnection()->beginTransaction();
		
		if($info_total)
		{
			
			$em->getConnection()->beginTransaction();
			try
			{
				$fecha_str=$info_total['feCreacion'];
				$fecha_exp=explode('-',$fecha_str);
				$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
				//Asiento
				$migra_arcgae = new MigraArcgae();
				$migra_arcgae->setNoCia($empresa_id);
				$migra_arcgae->setAno(date('Y'));
				$migra_arcgae->setMes(date('n'));
				$migra_arcgae->setNoAsiento($id_factura);
				$migra_arcgae->setImpreso('N');
				$migra_arcgae->setFecha(new \DateTime($fecha_mod));
				$migra_arcgae->setDescri1(substr('Fact. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri'],0,250));
				$migra_arcgae->setEstado('P');
				$migra_arcgae->setAutorizado('N');
				$migra_arcgae->setOrigen('TN');
				$migra_arcgae->setTDebitos($info_total['valorTotal']);
				$migra_arcgae->setTCreditos($info_total['valorTotal']);
				$migra_arcgae->setCodDiario('M_F_1');
				$migra_arcgae->setTCambCV('C');
				$migra_arcgae->setTipoCambio('1');
				$migra_arcgae->setTipoComprobante('T');
				$migra_arcgae->setAnulado('N');
				$migra_arcgae->setUsuarioCreacion($info_total['usrCreacion']);
				$migra_arcgae->setTransferido('N');
				$migra_arcgae->setFechaCreacion(new \DateTime(date('d-m-Y')));
				
				//print_r($migra_arcgae);
				
				$em_naf->persist($migra_arcgae);
				//$em_naf->flush();
				
				//Cta Cliente
				$migra_arcgal = new MigraArcgal();                
				$migra_arcgal->setNoCia($empresa_id);
				$migra_arcgal->setAno(date('Y'));
				$migra_arcgal->setMes(date('n'));
				$migra_arcgal->setNoAsiento($id_factura);
				$migra_arcgal->setNoLinea('1');
				$migra_arcgal->setCuenta($info_total['ctaContableClientes']);
				$migra_arcgal->setDescri('Fact. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);
				$migra_arcgal->setCodDiario('M_F_1');
				$migra_arcgal->setMoneda('P');
				$migra_arcgal->setTipoCambio(1);
				$migra_arcgal->setMonto($info_total['valorTotal']);
				$migra_arcgal->setCentroCosto('000000000');
				$migra_arcgal->setTipo('D');
				$migra_arcgal->setMontoDol($info_total['valorTotal']);
				$migra_arcgal->setCc1('000');
				$migra_arcgal->setCc2('000');
				$migra_arcgal->setCc3('000');
				$migra_arcgal->setLineaAjustePrecision('N');

				//print_r($migra_arcgal);
				
				$em_naf->persist($migra_arcgal);
				//$em_naf->flush();
				
				//Detalle de factura
				$listado_det = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getDetalleContabilizar($id_factura);
				
				foreach($listado_det as $detalle)
				{
					$no_linea = $migra_arcgal->getNoLinea();
					
					$no_linea = $no_linea + 1;
					$migra_arcgal = new MigraArcgal();                
					$migra_arcgal->setNoCia($empresa_id);
					$migra_arcgal->setAno(date('Y'));
					$migra_arcgal->setMes(date('n'));
					$migra_arcgal->setNoAsiento($id_factura);
					$migra_arcgal->setNoLinea($no_linea);
					$migra_arcgal->setCuenta(" ");
					$migra_arcgal->setDescri('Fact. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);
					$migra_arcgal->setCodDiario('M_F_1');
					$migra_arcgal->setMoneda('P');
					$migra_arcgal->setTipoCambio(1);
					if($detalle['precioItem']!=$detalle['precioVentaFacproDetalle'])
						$migra_arcgal->setMonto(($detalle['precioVentaFacproDetalle'])*(-1));
					else
						$migra_arcgal->setMonto(($detalle['precioItem'])*(-1));
					$migra_arcgal->setCentroCosto('000000000');
					$migra_arcgal->setTipo('C');
					if($detalle['precioItem']!=$detalle['precioVentaFacproDetalle'])
						$migra_arcgal->setMontoDol(($detalle['precioVentaFacproDetalle'])*(-1));
					else
						$migra_arcgal->setMontoDol(($detalle['precioItem'])*(-1));
					$migra_arcgal->setCc1('000');
					$migra_arcgal->setCc2('000');
					$migra_arcgal->setCc3('000');
					$migra_arcgal->setLineaAjustePrecision('N');
					
					//print_r($migra_arcgal);
					
					$em_naf->persist($migra_arcgal);
					//$em_naf->flush();
				}
				
				//Impuestos
				$info_imp = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getValorIva($id_factura);
				
				$no_linea = $no_linea + 1;
				$migra_arcgal = new MigraArcgal();                
				$migra_arcgal->setNoCia($empresa_id);
				$migra_arcgal->setAno(date('Y'));
				$migra_arcgal->setMes(date('n'));
				$migra_arcgal->setNoAsiento($id_factura);
				$migra_arcgal->setNoLinea($no_linea);
				$migra_arcgal->setCuenta($info_imp['ctaContable']);
				$migra_arcgal->setDescri('Fact. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);                
				$migra_arcgal->setCodDiario('M_F_1');
				$migra_arcgal->setMoneda('P');
				$migra_arcgal->setTipoCambio(1);
				$migra_arcgal->setMonto($info_imp['totalImpuesto']*(-1));
				$migra_arcgal->setCentroCosto('000000000');
				$migra_arcgal->setTipo('C');
				$migra_arcgal->setMontoDol($info_imp['totalImpuesto']*(-1));
				$migra_arcgal->setCc1('000');
				$migra_arcgal->setCc2('000');
				$migra_arcgal->setCc3('000');
				$migra_arcgal->setLineaAjustePrecision('N');              
				
				//print_r($migra_arcgal);
				
				$em_naf->persist($migra_arcgal);
				$em_naf->flush();
				$em_naf->getConnection()->commit();
				
				//die();
				//echo "llega al fw";
				/*return $this->forward('financieroBundle:InfoDocumentoFinancieroCab:show', array(
					'id'  => $id_factura,
				));*/
				
				return $this->redirect(
					$this->generateUrl("infodocumentofinancierocab_show", array("id" => $id_factura))
				);
			}
			catch (\Exception $e) {
					echo $e->getMessage();
					$em_naf->getConnection()->rollback();
					$em_naf->getConnection()->close();
					die();
					//die();
			}
	}
			
	return $this->redirect(
		$this->generateUrl("infodocumentofinancierocab_show", array("id" => $id_factura))
	);
	}
	
    /**
     * Proceso:
     * - Busco la factura en las tablas info_documento_financiero_det
     * - Si es plan :
     * 	+ Ingreso al listado de los planes hago join con la de productos 
     * 	+ y con la de impuestos para saber si tiene cada producto y cta contable
     * - Si es producto:
     * 	+ Ya tengo los productos y hago join solo con los impuestos
     * 
     * @author Desarrollo Inicial
     * @version 1.0 
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.1 2016-04-25 - Colocar en blanco (" ") los campos llenados por: ctaContableProd y ctaContableProdNc
     * 
     * @param integer $id_factura
     * 
     * @return Response
     **/
	public function contabilizarFacturaMasivoAction($id_factura)
    {
		$em = $this->getDoctrine()->getManager("telconet_financiero");
		
		$empresa_id="09";
		$oficina_id="32";
		$ptocliente="";
		$nombre_oficina="";
		
		$info_total = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getValorCtaCliente($id_factura,$empresa_id,$oficina_id);
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		//$em_naf->getConnection()->beginTransaction();
		
		//print_r($info_total);
		
		if($info_total)
		{
			
			//$em->getConnection()->beginTransaction();
			//$oci_con = oci_connect("naf47_tnet", "naf47_tnet", "192.168.213.227/XE");
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "172.24.5.78/dboracle");
			$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.24.5.78)(PORT = 1521)))(CONNECT_DATA=(SID=dboracle)))" ;
			$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", $db);
			try
			{
				$busqueda=$this->buscarExistentente($id_factura,$empresa_id);
				//$variable_existe['migra_arckmm']=0;
				//$variable_existe['migra_arcgae']=0;
				$busqueda['migra_arcgae']=0;
				
				if($busqueda['migra_arcgae']==1)
					echo "Ya existe";
				else
				{
					echo "Va a procesar";
					
					$fecha_str=$info_total['feEmision'];
					$fecha_exp=explode('-',$fecha_str);
					$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
					
					$newformat=$fecha_exp[2]."/".$fecha_exp[1]."/".$fecha_exp[0];
				
					echo "fecha newformat:".$newformat;
					echo "fecha mod:".$fecha_mod;
					//Asiento
					/*$migra_arcgae = new MigraArcgae();
					$migra_arcgae->setNoCia($empresa_id);
					$migra_arcgae->setAno($fecha_exp[0]);
					$migra_arcgae->setMes($fecha_exp[1]);
					$migra_arcgae->setNoAsiento($id_factura);
					$migra_arcgae->setImpreso('N');
					$migra_arcgae->setFecha(new \DateTime($fecha_mod));
					$migra_arcgae->setDescri1(substr('Fact. user: '.$info_total['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri'],0,250));
					$migra_arcgae->setEstado('P');
					$migra_arcgae->setAutorizado('N');
					$migra_arcgae->setOrigen('TN');
					$migra_arcgae->setTDebitos($info_total['valorTotal']);
					$migra_arcgae->setTCreditos($info_total['valorTotal']);
					$migra_arcgae->setCodDiario('M_F_1');
					$migra_arcgae->setTCambCV('C');
					$migra_arcgae->setTipoCambio('1');
					$migra_arcgae->setTipoComprobante('T');
					$migra_arcgae->setAnulado('N');
					$migra_arcgae->setUsuarioCreacion($info_total['usrCreacion']);
					$migra_arcgae->setTransferido('N');
					$migra_arcgae->setFechaCreacion(new \DateTime($fecha_mod));
					
					//print_r($migra_arcgae);
					
					$em_naf->persist($migra_arcgae);
					$em_naf->flush();*/
					
					$glosa=substr('Anul. Fact. user: '.$info_total['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri'],0,250);
					
					$valores['pv_idcompania']='09';
					$valores['pn_anio']=$fecha_exp[0];
					$valores['pn_mes']=$fecha_exp[1];
					$valores['pv_idasiento']=$id_factura.'60';
					$valores['pv_concepto']=$glosa;
					$valores['pn_totaldebe']=$info_total['valorTotal'];
					$valores['pn_totalhaber']=$info_total['valorTotal'];
					$valores['pv_tipodiario']="M_F_2";
					$valores['pv_uuariocrea']=$info_total['usrCreacion'];
					
					echo "<pre>";
					print_r($valores);
					echo "</pre>";
					
					if ($oci_con) 
					{
						$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgae('".$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes'].",".$valores['pv_idasiento'].",'".$newformat."','".$glosa."',".$valores['pn_totaldebe'].",".$valores['pn_totalhaber'].",'".$valores['pv_tipodiario']."','".$valores['pv_uuariocrea']."','".$newformat."',:pv_mensajeerror ); end;");
						oci_bind_by_name($s, ":pv_mensajeerror", $out_var, 2000); // 32 is the return length*/
						
						oci_execute($s);
						oci_commit($oci_con);
						if($out_var)
							echo $out_var;
						else
							echo "Proceso";
					}
					else		
						echo "No connect";
					
					//Cta Cliente
					/*$migra_arcgal = new MigraArcgal();                
					$migra_arcgal->setNoCia($empresa_id);
					$migra_arcgal->setAno($fecha_exp[0]);
					$migra_arcgal->setMes($fecha_exp[1]);
					$migra_arcgal->setNoAsiento($id_factura);
					$migra_arcgal->setNoLinea('1');
					$migra_arcgal->setCuenta($info_total['ctaContableClientes']);
					$migra_arcgal->setDescri('Fact. user: '.$info_total['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);
					$migra_arcgal->setCodDiario('M_F_1');
					$migra_arcgal->setMoneda('P');
					$migra_arcgal->setTipoCambio(1);
					$migra_arcgal->setMonto($info_total['valorTotal']);
					$migra_arcgal->setCentroCosto('000000000');
					$migra_arcgal->setTipo('D');
					$migra_arcgal->setMontoDol($info_total['valorTotal']);
					$migra_arcgal->setCc1('000');
					$migra_arcgal->setCc2('000');
					$migra_arcgal->setCc3('000');
					$migra_arcgal->setLineaAjustePrecision('N');

					//print_r($migra_arcgal);
					
					$em_naf->persist($migra_arcgal);
					$em_naf->flush();*/
					
					$no_linea=1;
				
					//Llamada al procedimiento
					$valores_dos['pv_idcompania']='09';
					$valores_dos['pn_anio']=$fecha_exp[0];
					$valores_dos['pn_mes']=$fecha_exp[1];
					$valores_dos['pv_idasiento']=$id_factura.'60';
					$valores_dos['pn_linea']=$no_linea;
					$valores_dos['pv_ctacontable']=$info_total['ctaContableClientes'];
					$valores_dos['pv_concepto']=$glosa;
					$valores_dos['pn_monto']=$info_total['valorTotal']*(-1);
					$valores_dos['pv_tipodiario']="M_F_2";
					$valores_dos['pv_tipo']="C";
					
					echo "<pre>";
					print_r($valores_dos);
					echo "</pre>";
					
					if ($oci_con) 
					{
						//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
						
						$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$valores_dos['pv_idcompania']."',".$valores_dos['pn_anio'].",".$valores_dos['pn_mes'].",'".$valores_dos['pv_idasiento']."',".$valores_dos['pn_linea'].",'".$valores_dos['pv_ctacontable']."','".$valores_dos['pv_concepto']."',".$valores_dos['pn_monto'].",'".$valores_dos['pv_tipodiario']."','".$valores_dos['pv_tipo']."',:pv_mensajeerror ); END;");
						oci_bind_by_name($s, ":pv_mensajeerror", $out_var, 2000); // 32 is the return length
						oci_execute($s);
						oci_commit($oci_con);
						if($out_var)
							echo $out_var;
						else
							echo "Proceso";
					}
					else		
						echo "No connect";
					
					
					//Detalle de factura
					$listado_det = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getDetalleContabilizar($id_factura);
					
					foreach($listado_det as $detalle)
					{
						$no_linea = $no_linea + 1;
						
						/*$no_linea = $migra_arcgal->getNoLinea();
						
						$no_linea = $no_linea + 1;
						$migra_arcgal = new MigraArcgal();                
						$migra_arcgal->setNoCia($empresa_id);
						$migra_arcgal->setAno($fecha_exp[0]);
						$migra_arcgal->setMes($fecha_exp[1]);
						$migra_arcgal->setNoAsiento($id_factura);
						$migra_arcgal->setNoLinea($no_linea);
						$migra_arcgal->setCuenta($detalle['ctaContableProd']);
						$migra_arcgal->setDescri('Fact. user: '.$info_total['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);
						$migra_arcgal->setCodDiario('M_F_1');
						$migra_arcgal->setMoneda('P');
						$migra_arcgal->setTipoCambio(1);
						if($detalle['precioItem']!=$detalle['precioVentaFacproDetalle'])
							$migra_arcgal->setMonto(($detalle['precioVentaFacproDetalle'])*(-1));
						else
							$migra_arcgal->setMonto(($detalle['precioItem'])*(-1));
						$migra_arcgal->setCentroCosto('000000000');
						$migra_arcgal->setTipo('C');
						if($detalle['precioItem']!=$detalle['precioVentaFacproDetalle'])
							$migra_arcgal->setMontoDol(($detalle['precioVentaFacproDetalle'])*(-1));
						else
							$migra_arcgal->setMontoDol(($detalle['precioItem'])*(-1));
						$migra_arcgal->setCc1('000');
						$migra_arcgal->setCc2('000');
						$migra_arcgal->setCc3('000');
						$migra_arcgal->setLineaAjustePrecision('N');
						
						//print_r($migra_arcgal);
						
						$em_naf->persist($migra_arcgal);
						$em_naf->flush();*/
						
						$valores_dos['pv_idcompania']='09';
						$valores_dos['pn_anio']=$fecha_exp[0];
						$valores_dos['pn_mes']=$fecha_exp[1];
						$valores_dos['pv_idasiento']=$id_factura.'60';
						$valores_dos['pn_linea']=$no_linea;
						$valores_dos['pv_ctacontable']=" ";
						$valores_dos['pv_concepto']=$glosa;
						$valores_dos['pn_monto']=$detalle['precioVentaFacproDetalle'];
						$valores_dos['pv_tipodiario']="M_F_2";
						$valores_dos['pv_tipo']="D";
						
						//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
						if ($oci_con) 
						{
							//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
							$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$valores_dos['pv_idcompania']."',".$valores_dos['pn_anio'].",".$valores_dos['pn_mes'].",'".$valores_dos['pv_idasiento']."',".$valores_dos['pn_linea'].",'".$valores_dos['pv_ctacontable']."','".$valores_dos['pv_concepto']."',".$valores_dos['pn_monto'].",'".$valores_dos['pv_tipodiario']."','".$valores_dos['pv_tipo']."',:pv_mensajeerror ); END;");
							oci_bind_by_name($s, ":pv_mensajeerror", $out_var, 2000); // 32 is the return length
							oci_execute($s);
							oci_commit($oci_con);
							if($out_var)
								echo $out_var;
							else
								echo "Proceso";
						
						//Detalle de factura
						//$listado_det = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getDetalleContabilizar($id_factura);
						}
						else		
							echo "No connect";
					}
					
					//Impuestos
					$info_imp = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getValorIva($id_factura);
					
					$no_linea = $no_linea + 1;
					/*$migra_arcgal = new MigraArcgal();                
					$migra_arcgal->setNoCia($empresa_id);
					$migra_arcgal->setAno($fecha_exp[0]);
					$migra_arcgal->setMes($fecha_exp[1]);
					$migra_arcgal->setNoAsiento($id_factura);
					$migra_arcgal->setNoLinea($no_linea);
					$migra_arcgal->setCuenta($info_imp['ctaContable']);
					$migra_arcgal->setDescri('Fact. user: '.$info_total['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);                
					$migra_arcgal->setCodDiario('M_F_1');
					$migra_arcgal->setMoneda('P');
					$migra_arcgal->setTipoCambio(1);
					$migra_arcgal->setMonto($info_imp['totalImpuesto']*(-1));
					$migra_arcgal->setCentroCosto('000000000');
					$migra_arcgal->setTipo('C');
					$migra_arcgal->setMontoDol($info_imp['totalImpuesto']*(-1));
					$migra_arcgal->setCc1('000');
					$migra_arcgal->setCc2('000');
					$migra_arcgal->setCc3('000');
					$migra_arcgal->setLineaAjustePrecision('N');              
					
					//print_r($migra_arcgal);
					
					$em_naf->persist($migra_arcgal);
					$em_naf->flush();*/
					
					$valores_dos['pv_idcompania']='09';
					$valores_dos['pn_anio']=$fecha_exp[0];
					$valores_dos['pn_mes']=$fecha_exp[1];
					$valores_dos['pv_idasiento']=$id_factura.'60';
					$valores_dos['pn_linea']=$no_linea;
					$valores_dos['pv_ctacontable']=$info_imp['ctaContable'];
					$valores_dos['pv_concepto']=$glosa;
					$valores_dos['pn_monto']=$info_imp['totalImpuesto'];
					$valores_dos['pv_tipodiario']="M_F_2";
					$valores_dos['pv_tipo']="D";
				
					//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
					if ($oci_con) 
					{
						//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
						$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$valores_dos['pv_idcompania']."',".$valores_dos['pn_anio'].",".$valores_dos['pn_mes'].",'".$valores_dos['pv_idasiento']."',".$valores_dos['pn_linea'].",'".$valores_dos['pv_ctacontable']."','".$valores_dos['pv_concepto']."',".$valores_dos['pn_monto'].",'".$valores_dos['pv_tipodiario']."','".$valores_dos['pv_tipo']."',:pv_mensajeerror ); END;");
						oci_bind_by_name($s, ":pv_mensajeerror", $out_var, 2000); // 32 is the return length
						oci_execute($s);
						oci_commit($oci_con);
						//oci_bind_by_name($s, ":pv_mensajeerror", $out_var, 2000); // 32 is the return length
						//oci_execute($s);

						if($out_var)
							echo $out_var;
						else
							echo "Proceso";
					}
					else		
						echo "No connect";
						//$em_naf->getConnection()->commit();
					
					return true;
				}
					
			}
			catch (\Exception $e) {
					echo $e->getMessage();
					//$em_naf->getConnection()->rollback();
					//$em_naf->getConnection()->close();
					die();
					//die();
			}
	}
	else
		return false;
			
	}

	public function buscarExistentente($id_factura,$empresa_id)
	{
		$id_fact_anul=$id_factura.'60';
		$variable_existe['migra_arcgae']=0;
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		$resultado_dos = $em_naf->getRepository('schemaBundle:MigraArcgae')->getExisteFacturaContabilidad($id_fact_anul,$empresa_id);
		$listadoPagos_dos=$resultado_dos['registros'];
		$total_dos=$resultado_dos['total'];
			
		if($total_dos>=1)
			$variable_existe['migra_arcgae']=1;
			
		return $variable_existe;
	}
}
