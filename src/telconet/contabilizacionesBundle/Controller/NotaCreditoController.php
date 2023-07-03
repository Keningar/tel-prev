<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class NotaCreditoController extends Controller
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
    public function contabilizarNotaCreditoAction($id_factura)
    {
		$em = $this->getDoctrine()->getManager("telconet_financiero");
		//$request = $this->getRequest();
		//$session=$request->getSession();
		
		$empresa_id="09";
		
		$info_total = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getValorCtaCliente($id_factura,$empresa_id);
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		//$em_naf->getConnection()->beginTransaction();
		
		//print_r($info_total);
		
		if($info_total)
		{
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "dbnaf.telconet.net/dboracle2");
			//$oci_con = oci_connect("naf47_tnet", "naf47_tnet", "192.168.213.227/XE");
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "172.24.5.78/dboracle");
			$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.24.5.78)(PORT = 1521)))(CONNECT_DATA=(SID=dboracle)))" ;
			$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", $db);
			
			//$em->getConnection()->beginTransaction();
			try
			{
				$fecha_str=$info_total['feCreacion'];
				echo "fecha str:".$fecha_str;
				
				$fecha_exp=explode('-',$fecha_str);
				
				
				$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
				$newformat=$fecha_exp[2]."/".$fecha_exp[1]."/".$fecha_exp[0];
				
				echo "fecha newformat:".$newformat;
				echo "fecha mod:".$fecha_mod;
				//Asiento
				/*
				$migra_arcgae = new MigraArcgae();
				$migra_arcgae->setNoCia($empresa_id);
				$migra_arcgae->setAno(date('Y'));
				$migra_arcgae->setMes(date('n'));
				$migra_arcgae->setNoAsiento($id_factura.'88');
				$migra_arcgae->setImpreso('N');
				$migra_arcgae->setFecha(new \DateTime($fecha_mod));
				$migra_arcgae->setDescri1(substr('N/C TELCOS. '.$fecha_mod.' '.substr($info_total['nombreOficina'],13,3).' user: '.$ptocliente['login'].' '.$info_total['numeroFacturaSri'],0,250));
				//$migra_arcgae->setDescri1(substr('Fact. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri'],0,250));
				$migra_arcgae->setEstado('P');
				$migra_arcgae->setAutorizado('N');
				$migra_arcgae->setOrigen('TN');
				$migra_arcgae->setTDebitos($info_total['valorTotal']);
				$migra_arcgae->setTCreditos($info_total['valorTotal']);
				$migra_arcgae->setCodDiario('M_NC1');
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
				*/
				
				$glosa=substr('N/C TELCOS '.$fecha_mod.' '.substr($info_total['nombreOficina'],13,3).' user: '.$info_total['login'].' '.$info_total['numeroFacturaSri'],0,250);
				
				//Llamada al procedimiento
				$valores['pv_idcompania']='09';
				$valores['pn_anio']=$fecha_exp[0];
				$valores['pn_mes']=$fecha_exp[1];
				$valores['pv_idasiento']=$info_total['id'].'88';
				$valores['pd_fecha']="to_date('2013-04-01','dd/mm/yyyy')";
				$valores['pv_concepto']=$glosa;
				$valores['pn_totaldebe']=$info_total['valorTotal'];
				$valores['pn_totalhaber']=$info_total['valorTotal'];
				$valores['pv_tipodiario']="M_NC1";
				$valores['pv_uuariocrea']=$info_total['usrCreacion'];
				$valores['pd_fechacrea']="to_date('2013-04-01','dd/mm/yyyy')";
				
				echo "<pre>";
				print_r($valores);
				echo "</pre>";
				
				if ($oci_con) 
				{
					$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgae('".$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes'].",".$valores['pv_idasiento'].",'".$newformat."','".$glosa."',".$valores['pn_totaldebe'].",".$valores['pn_totalhaber'].",'".$valores['pv_tipodiario']."','".$valores['pv_uuariocrea']."','".$newformat."',:pv_mensajeerror ); end;");
					
					/*$query = "BEGIN gek_migracion.cgp_inserta_arcgae (:pv_idcompania,:pn_anio,:pn_mes,:pv_idasiento,:pd_fecha,:pv_concepto,:pn_totaldebe,:pn_totalhaber,:pv_tipodiario,:pv_uuariocrea,:pd_fechacrea,:pv_mensajeerror ); END;";
					$s = ociparse($oci_con, $query);
					oci_bind_by_name($s, ':pv_idcompania', $valores['pv_idcompania']);
					oci_bind_by_name($s, ':pn_anio',intval($valores['pn_anio']));
					oci_bind_by_name($s, ':pn_mes', intval($valores['pn_mes']));
					oci_bind_by_name($s, ':pv_idasiento', intval($valores['pv_idasiento']));
					oci_bind_by_name($s, ':pd_fecha', $valores['pd_fecha']);
					oci_bind_by_name($s, ':pv_concepto', $valores['pv_concepto']);
					oci_bind_by_name($s, ':pn_totaldebe', floatval($valores['pn_totaldebe']));
					oci_bind_by_name($s, ':pn_totalhaber', floatval($valores['pn_totalhaber']));
					oci_bind_by_name($s, ':pv_tipodiario', $valores['pv_tipodiario']);
					oci_bind_by_name($s, ':pv_uuariocrea', $valores['pv_uuariocrea']);
					oci_bind_by_name($s, ':pd_fechacrea', $valores['pd_fechacrea']);*/
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
					
				//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
				
				//Cta Cliente
				/*$migra_arcgal = new MigraArcgal();                
				$migra_arcgal->setNoCia($empresa_id);
				$migra_arcgal->setAno(date('Y'));
				$migra_arcgal->setMes(date('n'));
				$migra_arcgal->setNoAsiento($id_factura.'88');
				$migra_arcgal->setNoLinea('1');
				$migra_arcgal->setCuenta($info_total['ctaContableClientes']);
				$migra_arcgal->setDescri('N/C TELCOS. '.$fecha_mod.' '.$info_total['nombreOficina'].' user: '.$ptocliente['login'].' '.$info_total['numeroFacturaSri']);
				//$migra_arcgal->setDescri('Fact. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$info_total['nombreOficina'].' '.$info_total['numeroFacturaSri']);
				$migra_arcgal->setCodDiario('M_NC1');
				$migra_arcgal->setMoneda('P');
				$migra_arcgal->setTipoCambio(1);
				$migra_arcgal->setMonto($info_total['valorTotal']*(-1));
				$migra_arcgal->setCentroCosto('000000000');
				$migra_arcgal->setTipo('C');
				$migra_arcgal->setMontoDol($info_total['valorTotal']*(-1));
				$migra_arcgal->setCc1('000');
				$migra_arcgal->setCc2('000');
				$migra_arcgal->setCc3('000');
				$migra_arcgal->setLineaAjustePrecision('N');

				//print_r($migra_arcgal);
				
				$em_naf->persist($migra_arcgal);
				//$em_naf->flush();*/
				
				$no_linea=1;
				
				//Llamada al procedimiento
				$valores_dos['pv_idcompania']='09';
				$valores_dos['pn_anio']=$fecha_exp[0];
				$valores_dos['pn_mes']=$fecha_exp[1];
				$valores_dos['pv_idasiento']=$info_total['id'].'88';
				$valores_dos['pn_linea']=$no_linea;
				$valores_dos['pv_ctacontable']=$info_total['ctaContableClientes'];
				$valores_dos['pv_concepto']=$glosa;
				$valores_dos['pn_monto']=$info_total['valorTotal']*(-1);
				$valores_dos['pv_tipodiario']="M_NC1";
				$valores_dos['pv_tipo']="C";
				
				echo "<pre>";
				print_r($valores_dos);
				echo "</pre>";
				
				//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
				if ($oci_con) 
				{
					//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
					
					$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$valores_dos['pv_idcompania']."',".$valores_dos['pn_anio'].",".$valores_dos['pn_mes'].",'".$valores_dos['pv_idasiento']."',".$valores_dos['pn_linea'].",'".$valores_dos['pv_ctacontable']."','".$valores_dos['pv_concepto']."',".$valores_dos['pn_monto'].",'".$valores_dos['pv_tipodiario']."','".$valores_dos['pv_tipo']."',:pv_mensajeerror ); END;");
					
					/*$s = ociparse($oci_con, $query);
					oci_bind_by_name($s, ':pv_idcompania', $valores_dos['pv_idcompania']);
					oci_bind_by_name($s, ':pn_anio', intval($valores_dos['pn_anio']));
					oci_bind_by_name($s, ':pn_mes', intval($valores_dos['pn_mes']));
					oci_bind_by_name($s, ':pv_idasiento', intval($valores_dos['pv_idasiento']));
					oci_bind_by_name($s, ':pn_linea', intval($valores_dos['pn_linea']));
					oci_bind_by_name($s, ':pv_ctacontable', $valores_dos['pv_ctacontable']);
					oci_bind_by_name($s, ':pv_concepto', $valores_dos['pv_concepto']);
					oci_bind_by_name($s, ':pn_monto', floatval($valores_dos['pn_monto']));
					oci_bind_by_name($s, ':pv_tipodiario', $valores_dos['pv_tipodiario']);
					oci_bind_by_name($s, ':pv_tipo', $valores_dos['pv_uuariocrea']);*/
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
					
					/*
					$migra_arcgal = new MigraArcgal();                
					$migra_arcgal->setNoCia($empresa_id);
					$migra_arcgal->setAno(date('Y'));
					$migra_arcgal->setMes(date('n'));
					$migra_arcgal->setNoAsiento($id_factura.'88');
					$migra_arcgal->setNoLinea($no_linea);
					$migra_arcgal->setCuenta($detalle['ctaContableProdNc']);
					//$migra_arcgal->setDescri('N/C TELCOS. '.$fecha_mod.' '.$nombre_oficina.' user: '.$ptocliente['login']);
					$migra_arcgal->setDescri('N/C TELCOS. '.$fecha_mod.' '.$info_total['nombreOficina'].' user: '.$ptocliente['login'].' '.$info_total['numeroFacturaSri']);
					$migra_arcgal->setCodDiario('M_NC1');
					$migra_arcgal->setMoneda('P');
					$migra_arcgal->setTipoCambio(1);
					$migra_arcgal->setMonto(($detalle['precioVentaFacproDetalle']));
					$migra_arcgal->setCentroCosto('000000000');
					$migra_arcgal->setTipo('D');
					$migra_arcgal->setMontoDol(($detalle['precioVentaFacproDetalle']));
					$migra_arcgal->setCc1('000');
					$migra_arcgal->setCc2('000');
					$migra_arcgal->setCc3('000');
					$migra_arcgal->setLineaAjustePrecision('N');
					
					//print_r($migra_arcgal);
					
					$em_naf->persist($migra_arcgal);
					//$em_naf->flush();
					*/
					
					//Llamada al procedimiento
					$valores_dos['pv_idcompania']='09';
					$valores_dos['pn_anio']=$fecha_exp[0];
					$valores_dos['pn_mes']=$fecha_exp[1];
					$valores_dos['pv_idasiento']=$info_total['id'].'88';
					$valores_dos['pn_linea']=$no_linea;
					$valores_dos['pv_ctacontable']=" ";
					$valores_dos['pv_concepto']=$glosa;
					$valores_dos['pn_monto']=$detalle['precioVentaFacproDetalle'];
					$valores_dos['pv_tipodiario']="M_NC1";
					$valores_dos['pv_tipo']="D";
					
					//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
					if ($oci_con) 
					{
						//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
						$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$valores_dos['pv_idcompania']."',".$valores_dos['pn_anio'].",".$valores_dos['pn_mes'].",'".$valores_dos['pv_idasiento']."',".$valores_dos['pn_linea'].",'".$valores_dos['pv_ctacontable']."','".$valores_dos['pv_concepto']."',".$valores_dos['pn_monto'].",'".$valores_dos['pv_tipodiario']."','".$valores_dos['pv_tipo']."',:pv_mensajeerror ); END;");
						
						
						/*$query = "BEGIN gek_migracion.cgp_inserta_arcgal (:pv_idcompania,:pn_anio,:pn_mes,:pv_idasiento,:pn_linea,:pv_ctacontable,:pv_concepto,:pn_monto,:pv_tipodiario,:pv_tipo,:pv_mensajeerror ); END;";
						$s = ociparse($oci_con, $query);
						oci_bind_by_name($s, ':pv_idcompania', $valores_dos['pv_idcompania']);
						oci_bind_by_name($s, ':pn_anio', intval($valores_dos['pn_anio']));
						oci_bind_by_name($s, ':pn_mes', intval($valores_dos['pn_mes']));
						oci_bind_by_name($s, ':pv_idasiento', intval($valores_dos['pv_idasiento']));
						oci_bind_by_name($s, ':pn_linea', intval($valores_dos['pn_linea']));
						oci_bind_by_name($s, ':pv_ctacontable', $valores_dos['pv_ctacontable']);
						oci_bind_by_name($s, ':pv_concepto', $valores_dos['pv_concepto']);
						oci_bind_by_name($s, ':pn_monto', floatval($valores_dos['pn_monto']));
						oci_bind_by_name($s, ':pv_tipodiario', $valores_dos['pv_tipodiario']);
						oci_bind_by_name($s, ':pv_tipo', $valores_dos['pv_uuariocrea']);*/
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
				/*
				$migra_arcgal = new MigraArcgal();                
				$migra_arcgal->setNoCia($empresa_id);
				$migra_arcgal->setAno(date('Y'));
				$migra_arcgal->setMes(date('n'));
				$migra_arcgal->setNoAsiento($id_factura.'88');
				$migra_arcgal->setNoLinea($no_linea);
				$migra_arcgal->setCuenta($info_imp['ctaContable']);
				//$migra_arcgal->setDescri('N/C TELCOS. '.$fecha_mod.' '.$nombre_oficina.' user: '.$ptocliente['login']);
				$migra_arcgal->setDescri('N/C TELCOS. '.$fecha_mod.' '.$info_total['nombreOficina'].' user: '.$ptocliente['login'].' '.$info_total['numeroFacturaSri']);                
				$migra_arcgal->setCodDiario('M_NC1');
				$migra_arcgal->setMoneda('P');
				$migra_arcgal->setTipoCambio(1);
				$migra_arcgal->setMonto($info_imp['totalImpuesto']*(-1));
				$migra_arcgal->setCentroCosto('000000000');
				$migra_arcgal->setTipo('D');
				$migra_arcgal->setMontoDol($info_imp['totalImpuesto']*(-1));
				$migra_arcgal->setCc1('000');
				$migra_arcgal->setCc2('000');
				$migra_arcgal->setCc3('000');
				$migra_arcgal->setLineaAjustePrecision('N');              
				
				//print_r($migra_arcgal);
				
				$em_naf->persist($migra_arcgal);
				
				$em_naf->flush();*/
				
				//Llamada al procedimiento
				$valores_dos['pv_idcompania']='09';
				$valores_dos['pn_anio']=$fecha_exp[0];
				$valores_dos['pn_mes']=$fecha_exp[1];
				$valores_dos['pv_idasiento']=$info_total['id'].'88';
				$valores_dos['pn_linea']=$no_linea;
				$valores_dos['pv_ctacontable']=$info_imp['ctaContable'];
				$valores_dos['pv_concepto']=$glosa;
				$valores_dos['pn_monto']=$info_imp['totalImpuesto'];
				$valores_dos['pv_tipodiario']="M_NC1";
				$valores_dos['pv_tipo']="D";
				
				//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
				if ($oci_con) 
				{
					//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
					$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$valores_dos['pv_idcompania']."',".$valores_dos['pn_anio'].",".$valores_dos['pn_mes'].",'".$valores_dos['pv_idasiento']."',".$valores_dos['pn_linea'].",'".$valores_dos['pv_ctacontable']."','".$valores_dos['pv_concepto']."',".$valores_dos['pn_monto'].",'".$valores_dos['pv_tipodiario']."','".$valores_dos['pv_tipo']."',:pv_mensajeerror ); END;");
					/*
					$query = "BEGIN gek_migracion.cgp_inserta_arcgal (:pv_idcompania,:pn_anio,:pn_mes,:pv_idasiento,:pn_linea,:pv_ctacontable,:pv_concepto,:pn_monto,:pv_tipodiario,:pv_tipo,:pv_mensajeerror ); END;";
					$s = ociparse($oci_con, $query);
					oci_bind_by_name($s, ':pv_idcompania', $valores_dos['pv_idcompania']);
					oci_bind_by_name($s, ':pn_anio', intval($valores_dos['pn_anio']));
					oci_bind_by_name($s, ':pn_mes', intval($valores_dos['pn_mes']));
					oci_bind_by_name($s, ':pv_idasiento', intval($valores_dos['pv_idasiento']));
					oci_bind_by_name($s, ':pn_linea', intval($valores_dos['pn_linea']));
					oci_bind_by_name($s, ':pv_ctacontable', $valores_dos['pv_ctacontable']);
					oci_bind_by_name($s, ':pv_concepto', $valores_dos['pv_concepto']);
					oci_bind_by_name($s, ':pn_monto', floatval($valores_dos['pn_monto']));
					oci_bind_by_name($s, ':pv_tipodiario', $valores_dos['pv_tipodiario']);
					oci_bind_by_name($s, ':pv_tipo', $valores_dos['pv_uuariocrea']);*/
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
				
				//echo "llega al fw";
				/*return $this->forward('financieroBundle:InfoDocumentoFinancieroCab:show', array(
					'id'  => $id_factura,
				));
				
				return true;*/
			}
			catch (\Exception $e) {
					echo $e->getMessage();
					//$em_naf->getConnection()->rollback();
					//$em_naf->getConnection()->close();
					//die();
					//die();
					return false;
			}
	}
	/*		
	return $this->redirect(
		$this->generateUrl("infodocumentonotacredito_show", array("id" => $id_factura))
	);
	* */
	return false;
	}
}
