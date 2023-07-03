<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDeposito;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class DepositosController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('contabilizacionesBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function contabilizarDepositosAction($id_deposito)
    {
		/*
		 * Proceso:
		 * - Busco la factura en las tablas info_documento_financiero_det
		 * - Si es plan :
		 * 	+ Ingreso al listado de los planes hago join con la de productos 
		 * 	+ y con la de impuestos para saber si tiene cada producto y cta contable
		 * - Si es producto:
		 * 	+ Ya tengo los productos y hago join solo con los impuestos
		 * */
		
		$emfinan = $this->getDoctrine()->getManager("telconet_financiero");
		$emgeneral = $this->getDoctrine()->getManager("telconet_general");
		$info_total = $emfinan->getRepository('schemaBundle:InfoDeposito')->getDeposito($id_deposito);
		
		$info=$emgeneral->getRepository('schemaBundle:AdmiFormaPago')->findByDescripcionFormaPago("DEPOSITO");
		echo "<pre>";
		print_r($info);
		echo "</pre>";
		//die();
		
		if($info_total)
		{
			
			//$em->getConnection()->beginTransaction();
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "dbnaf.telconet.net/dboracle2");
			//$oci_con = oci_connect("naf47_tnet", "naf47_tnet", "192.168.213.227/XE");
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "172.24.5.78/dboracle");
			$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.24.5.78)(PORT = 1521)))(CONNECT_DATA=(SID=dboracle)))" ;
			$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", $db);
			echo "entro aki";
			try
			{
				//hasta que se haga la modificacion en los debitos que tengan empresa
				$empresa_id="09";
				$fecha_str=substr(strval(date_format($info_total[0]['feDeposito'],"Y-m-d G:i")),0,10);
				echo $fecha_str;
				$oficina_id=$info_total[0]['oficinaId'];
				//$cta_contable_cliente = $emfinan->getRepository('schemaBundle:InfoPagoCab')->getCtaCliente($empresa_id, $oficina_id,"PAG");

				$cta_contable_caja=$emfinan->getRepository('schemaBundle:InfoPagoCab')->getCajaChica($empresa_id, $oficina_id);
				//echo "<br>entro a grabar el asiento. cta:".$no_cta;
				/*$migra_arckmm = new MigraArckmm();
				$migra_arckmm->setNoCia($empresa_id);
				$migra_arckmm->setNoCta($info_total['noCuentaBancoNaf']);
				$migra_arckmm->setProcedencia('C');
				$migra_arckmm->setTipoDoc('DP');
				$migra_arckmm->setNoDocu($info_total['id']);*/
				
				
				$fecha_exp=explode('-',$fecha_str);
				$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
				$newformat=$fecha_exp[2]."/".$fecha_exp[1]."/".$fecha_exp[0];
				
				//$migra_arckmm->setFecha(new \DateTime($fecha_mod));
				$nombre_banco_str="";
				$glosa="";
				
				$nombre_banco=$info_total[0]['descripcionBanco'];
				
				if($nombre_banco=="BANCO DEL PACIFICO" || $nombre_banco=="BANCO DEL AUSTRO")
					$nombre_banco_str=substr($nombre_banco,10,3);
				else
					$nombre_banco_str=substr($nombre_banco,6,3);
				
				$numero=$info_total[0]['noComprobanteDeposito'];
				
				if($fecha_str!="")
				{
					$fecha_deposito=$fecha_str;
					$fecha_exp_depo=explode('-',$fecha_deposito);
					$fecha_mod_depo=$fecha_exp_depo[2]."-".$fecha_exp_depo[1]."-".$fecha_exp_depo[0];
				}
				else
					$fecha_mod_depo=$fecha_str;
				
				$glosa='Depo. TELCOS '.$fecha_mod_depo.' '.substr($info_total[0]['nombreOficina'],13,3).' '.substr($info[0]->getCodigoFormaPago(),0,3).' '.$numero.' '.$nombre_banco_str;
				
				/*$migra_arckmm->setComentario(substr('Depo. user: '.$glosa,0,250));
				$migra_arckmm->setMonto($info_total['valor']);
				$migra_arckmm->setEstado('P');
				$migra_arckmm->setConciliado('N');
				//$migra_arckmm->setMes(date('n'));
				$migra_arckmm->setMes($fecha_exp[1]);
				//$migra_arckmm->setAno(date('Y'));
				$migra_arckmm->setAno($fecha_exp[0]);
				$migra_arckmm->setIndOtromov('S');
				$migra_arckmm->setMonedaCta('P');
				$migra_arckmm->setTipoCambio('1');
				$migra_arckmm->setTCambCV('C');
				$migra_arckmm->setIndOtrosMeses('N');
				$migra_arckmm->setNoFisico(substr($numero,0,11));
				$migra_arckmm->setOrigen('TN');
				$migra_arckmm->setUsuarioCreacion($info_total['usrProcesa']);
				$migra_arckmm->setFechaDoc(new \DateTime($fecha_mod));
				$migra_arckmm->setIndDivision('N');  
				$migra_arckmm->setFechaCreacion(new \DateTime($fecha_mod));					
				
				//print_r($migra_arckmm);                    
				$em_naf->persist($migra_arckmm);
				$em_naf->flush();*/
				
				
				$valores['Pv_IdCompania']='09';	
				$valores['Pv_NoCuenta']=$info_total[0]['noCuentaBancoNaf'];
				$valores['Pv_TipoDocumento']='DP';
				$valores['Pv_IdDcoumento']=$info_total[0]['id'];
				$valores['Pd_Fecha']=$newformat;
				$valores['Pd_FechaDoc']=$newformat;
				$valores['Pv_Comentario']=$glosa;
				$valores['Pn_Monto']=$info_total[0]['valor'];
				$valores['Pn_Anio']=$fecha_exp[0];
				$valores['Pn_Mes']=$fecha_exp[1];
				$valores['Pv_UsuarioCrea']=$info_total[0]['usrProcesa'];
				$valores['Pd_FechaCrea']=$newformat;
				$valores['Pv_NoFisico']=$info_total[0]['noComprobanteDeposito'];
				
				echo "<pre>";
				print_r($valores);
				echo "</pre>";
				
				if ($oci_con) 
				{
					$s = oci_parse($oci_con, "begin gek_migracion.ckp_inserta_arckmm('".$valores['Pv_IdCompania']."','".$valores['Pv_NoCuenta']."','".$valores['Pv_TipoDocumento']."','".$valores['Pv_IdDcoumento']."','".$newformat."','".$newformat."','".$glosa."',".$valores['Pn_Monto'].",".$valores['Pn_Anio'].",".$valores['Pn_Mes'].",'".$valores['Pv_UsuarioCrea']."','".$newformat."','".$valores['Pv_NoFisico']."',:pv_mensajeerror ); end;");
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
				//Cta de caja , bancos
				
				/*$migra_arckml = new MigraArckml();                
				$migra_arckml->setNoCia($empresa_id);
				$migra_arckml->setProcedencia('C');
				$migra_arckml->setTipoDoc('DP');
				$migra_arckml->setNoDocu($info_total['id']);*/
				//cta contable
				$cta_contable=$info_total[0]['noCuentaContableNaf'];
				
				/*$migra_arckml->setCodCont($cta_contable);
				$migra_arckml->setCentroCosto('000000000');
				$migra_arckml->setTipoMov('D');
				$migra_arckml->setMonto($info_total['valor']);
				$migra_arckml->setMontoDol($info_total['valor']);
				$migra_arckml->setTipoCambio(1);
				$migra_arckml->setMoneda('P');
				$migra_arckml->setModificable('N');
				$migra_arckml->setAno($fecha_exp[0]);
				$migra_arckml->setMes($fecha_exp[1]);
				$migra_arckml->setMontoDc($info_total['valor']);
				$migra_arckml->setGlosa(substr('Depo. user: '.$glosa,0,100)); 
			
				$em_naf->persist($migra_arckml);
				$em_naf->flush();*/
				
				$valores_dos['Pv_IdCompania']='09';
				$valores_dos['Pv_TipoDocumento']='DP';
				$valores_dos['Pv_IdDocumento']=$info_total[0]['id'];
				$valores_dos['Pv_CtaContable']=$cta_contable;
				$valores_dos['Pv_CentroCosto']='000000000';
				$valores_dos['Pv_TipoMov']='D';
				$valores_dos['Pn_Monto']=$info_total[0]['valor'];
				$valores_dos['Pv_Glosa']=$glosa;
				$valores_dos['Pn_Anio']=$fecha_exp[0];
				$valores_dos['Pn_Mes']=$fecha_exp[1];
				$valores_dos['Pv_Modificable']="N";
				
				echo "<pre>";
				print_r($valores_dos);
				echo "</pre>";
				
				//$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->insertarMigraArcgae($valores);
				if ($oci_con) 
				{
					//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
					
					$s = oci_parse($oci_con, "begin gek_migracion.ckp_inserta_arckml('".$valores_dos['Pv_IdCompania']."','".$valores_dos['Pv_TipoDocumento']."','".$valores_dos['Pv_IdDocumento']."','".$valores_dos['Pv_CtaContable']."','".$valores_dos['Pv_CentroCosto']."','".$valores_dos['Pv_TipoMov']."',".$valores_dos['Pn_Monto'].",'".$valores_dos['Pv_Glosa']."',".$valores_dos['Pn_Anio'].",".$valores_dos['Pn_Mes'].",'".$valores_dos['Pv_Modificable']."',:pv_mensajeerror ); END;");
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
				//Cta cliente
				 //en este caso no hay saldos a favor
				/*$migra_arckml = new MigraArckml();                
				$migra_arckml->setNoCia($empresa_id);
				$migra_arckml->setProcedencia('C');
				$migra_arckml->setTipoDoc('NC');
				$migra_arckml->setNoDocu($info['id']);
				$migra_arckml->setCodCont($cta_contable_cliente['ctaContableClientes']);
				$migra_arckml->setCentroCosto('000000000');
				$migra_arckml->setTipoMov('C');
				$migra_arckml->setMonto($info_total['valor']);
				$migra_arckml->setMontoDol($info_total['valor']);
				$migra_arckml->setTipoCambio(1);
				$migra_arckml->setMoneda('P');
				$migra_arckml->setModificable('S');
				$migra_arckml->setAno($fecha_exp[0]);
				$migra_arckml->setMes($fecha_exp[1]);
				$migra_arckml->setMontoDc($info_total['valor']);
				$migra_arckml->setGlosa(substr('Depo. user: '.$glosa,0,100)); 
				
				//print_r($migra_arckml);
				
				$em_naf->persist($migra_arckml);
				$em_naf->flush();*/
				$valores_dos['Pv_IdCompania']='09';
				$valores_dos['Pv_TipoDocumento']='DP';
				$valores_dos['Pv_IdDocumento']=$info_total[0]['id'];
				$valores_dos['Pv_CtaContable']=$cta_contable_caja['ctaContablePagos'];
				$valores_dos['Pv_CentroCosto']='000000000';
				$valores_dos['Pv_TipoMov']='C';
				$valores_dos['Pn_Monto']=$info_total[0]['valor'];
				$valores_dos['Pv_Glosa']=$glosa;
				$valores_dos['Pn_Anio']=$fecha_exp[0];
				$valores_dos['Pn_Mes']=$fecha_exp[1];
				$valores_dos['Pv_Modificable']="N";
				
				echo "<pre>";
				print_r($valores_dos);
				echo "</pre>";
				
				if ($oci_con) 
				{
					//$s = oci_parse($oci_con, "begin gek_migracion.cgp_inserta_arcgal('".$$valores['pv_idcompania']."',".$valores['pn_anio'].",".$valores['pn_mes']",'".$valores['pv_idasiento']."',".$valores['pn_linea'].",'".$valores['pv_ctacontable']."','".$valores['pv_concepto']."',".$valores['pn_monto'].",'".$valores['pv_tipodiario']."','".$valores['pv_tipo'].",:pv_mensajeerror ); end;");
					
					$s = oci_parse($oci_con, "begin gek_migracion.ckp_inserta_arckml('".$valores_dos['Pv_IdCompania']."','".$valores_dos['Pv_TipoDocumento']."','".$valores_dos['Pv_IdDocumento']."','".$valores_dos['Pv_CtaContable']."','".$valores_dos['Pv_CentroCosto']."','".$valores_dos['Pv_TipoMov']."',".$valores_dos['Pn_Monto'].",'".$valores_dos['Pv_Glosa']."',".$valores_dos['Pn_Anio'].",".$valores_dos['Pn_Mes'].",'".$valores_dos['Pv_Modificable']."',:pv_mensajeerror ); END;");
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
			
				return true;	
			}
			catch (\Exception $e) {
					echo $e->getMessage();
					//$em_naf->getConnection()->rollback();
					//$em_naf->getConnection()->close();
					die();
					die();
			}
		}
		else
			return false;	
		/*return $this->redirect(
			$this->generateUrl("infopagocab_show", array("id" => $id_pago))
		);*/
	}
	
	public function buscarExistentente($id_deposito,$empresa_id)
	{
		$variable_existe['migra_arckmm']=0;
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		$resultado = $em_naf->getRepository('schemaBundle:MigraArckmm')->getExisteDepositoContabilidad($id_deposito,$empresa_id);
		$listadoPagos=$resultado['registros'];
		$total=$resultado['total'];
		
		if($total>=1)
			$variable_existe['migra_arckmm']=1;
			
		return $variable_existe;
	}		
}
