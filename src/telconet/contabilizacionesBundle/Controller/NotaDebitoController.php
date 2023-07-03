<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class NotaDebitoController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('contabilizacionesBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function contabilizarNotaDebitoAction($id_factura)
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
		
		//echo "llega aki";
		//die();
		$em = $this->getDoctrine()->getManager("telconet_financiero");
		$em_general = $this->getDoctrine()->getManager("telconet_general");
		//$request = $this->getRequest();
		//$session=$request->getSession();
		
		$empresa_id="09";
		
		$info_total = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getValorCtaND($id_factura,$empresa_id);
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		//$em_naf->getConnection()->beginTransaction();
		
		//print_r($info_total);
		
		if(isset($info_total))
		{
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "dbnaf.telconet.net/dboracle2");
			//$oci_con = oci_connect("naf47_tnet", "naf47_tnet", "192.168.213.227/XE");
			//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "172.24.5.78/dboracle");
			$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.24.5.78)(PORT = 1521)))(CONNECT_DATA=(SID=dboracle)))" ;
			$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", $db);
			
			//$em->getConnection()->beginTransaction();
			
			try
			{
				$cta_contable_cargo = $em_general->getRepository('schemaBundle:InfoOficinaGrupo')->findOneByEmpresaId($empresa_id);
				
				foreach($info_total as $info)
				{
					/*echo "<pre>";
					print_r($cta_contable_cargo);
					echo "</pre>";*/
					
					if($cta_contable_cargo)
					{
						$no_linea=0;
						$no_cta="CARGO_CTA_UIO";
						//Asiento en otras tablas xq es de otro tipo la cta
						$fecha_str=$info['feCreacion'];
						$fecha_exp=explode('-',$fecha_str);
						$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
						
						$newformat=$fecha_exp[2]."/".$fecha_exp[1]."/".$fecha_exp[0];
						$newformat_depo=$newformat;
			
						echo "fecha newformat:".$newformat;
						echo "fecha mod:".$fecha_mod;
						//Asiento
						
						$nombre_banco_str="";
						$glosa="";
						
						$numero="";
						
						$fecha_mod_depo="";
						if($info['feCreacion']!="")
						{
							$fecha_deposito=$info['feCreacion'];
							$fecha_exp_depo=explode('-',$fecha_deposito);
							$fecha_mod_depo=$fecha_exp_depo[2]."-".$fecha_exp_depo[1]."-".$fecha_exp_depo[0];
						}
						else
							$fecha_mod_depo=$fecha_mod;
						
						
						$glosa=$info['login'].' TELCOS '.$fecha_mod_depo.' '.substr($info['nombreOficina'],13,3).' '.$info['numeroFacturaSri'];
						$glosa=substr('N/D. user: '.$glosa,0,250);
						
						
						$valores['Pv_IdCompania']='09';	
						$valores['Pv_NoCuenta']=$no_cta;
						$valores['Pv_TipoDocumento']='ND';
						$valores['Pv_IdDcoumento']=$info['id'].'50';
						$valores['Pd_Fecha']=$newformat;
						$valores['Pd_FechaDoc']=$newformat_depo;
						$valores['Pv_Comentario']=$glosa;
						$valores['Pn_Monto']=$info['valorTotal'];
						$valores['Pn_Anio']=$fecha_exp[0];
						$valores['Pn_Mes']=$fecha_exp[1];
						$valores['Pv_UsuarioCrea']=$info['usrCreacion'];
						$valores['Pd_FechaCrea']=$newformat;
						$valores['Pv_NoFisico']="";
						
						echo "<pre>";
						print_r($valores);
						echo "</pre>";
						
						if ($oci_con) 
						{
							$s = oci_parse($oci_con, "begin gek_migracion.ckp_inserta_arckmm('".$valores['Pv_IdCompania']."','".$valores['Pv_NoCuenta']."','".$valores['Pv_TipoDocumento']."','".$valores['Pv_IdDcoumento']."','".$newformat."','".$newformat_depo."','".$glosa."',".$valores['Pn_Monto'].",".$valores['Pn_Anio'].",".$valores['Pn_Mes'].",'".$valores['Pv_UsuarioCrea']."','".$newformat."','".$valores['Pv_NoFisico']."',:pv_mensajeerror ); end;");
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
									
						//Cta deudora
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='ND';
						$valores_dos['Pv_IdDocumento']=$info['id'].'50';
						$valores_dos['Pv_CtaContable']=$info['ctaContable'];
						$valores_dos['Pv_CentroCosto']='000000000';
						$valores_dos['Pv_TipoMov']='D';
						$valores_dos['Pn_Monto']=$info['valorTotal'];
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
			
						
						//Cuenta acreedora
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='ND';
						$valores_dos['Pv_IdDocumento']=$info['id'].'50';
						$valores_dos['Pv_CtaContable']=$cta_contable_cargo->getCtaContableCargo();
						$valores_dos['Pv_CentroCosto']='000000000';
						$valores_dos['Pv_TipoMov']='C';
						$valores_dos['Pn_Monto']=$info['valorTotal'];
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
					}
					else
						echo "No existe cta, no se puede procesar";
				}
			}
			catch (\Exception $e) {
					echo $e->getMessage();
					//$em_naf->getConnection()->rollback();
					//$em_naf->getConnection()->close();
					//die();
					//die();
					//return false;
			}
	}
	else
		echo "No se puede procesar";
	/*		
	return $this->redirect(
		$this->generateUrl("infodocumentonotacredito_show", array("id" => $id_factura))
	);
	* */
	//return false;

	}
}
