<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class AnticiposSinClienteCruceController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('contabilizacionesBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function contabilizarAnticiposSinClientesAction($id_pago)
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
		
		//echo "entro a contabilizacion de anticipo sin cliente";die;
		
		$em = $this->getDoctrine()->getManager("telconet_financiero");
		//$request = $this->getRequest();
		//$session=$request->getSession();
		
		/*$empresa_id=$session->get('idEmpresa');
		$oficina_id=$session->get('idOficina');
		$ptocliente=$session->get('ptoCliente');
		$nombre_oficina=$session->get('nombreOficina');*/
		
		
		$info_total = $em->getRepository('schemaBundle:InfoPagoCab')->getDetallePago($id_pago,"ANTS");
		//echo "obtuvo detalle de pago";die;
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		//$em_naf->getConnection()->beginTransaction();
		
		
		if($info_total)
		{
			
			//$em->getConnection()->beginTransaction();
			try
			{
				//$oci_con = oci_connect("naf47_tnet", "naf47_tnet", "192.168.213.227/XE");
				//$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "172.24.5.78/dboracle");
				$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.24.5.78)(PORT = 1521)))(CONNECT_DATA=(SID=dboracle)))" ;
				$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", $db);
				foreach($info_total as $info)
				{
					$busqueda=$this->buscarExistentente($id_pago,$info['id'],$info['empresaId']);
					//$variable_existe['migra_arckmm']=0;
					//$variable_existe['migra_arcgae']=0;
					if($busqueda['migra_arckmm']==1 || $busqueda['migra_arcgae']==1)
						echo "Ya existe: pago : ".$id_pago."det: ".$info['id'];
					else
					{
						echo "Va a procesar";
						
					$empresa_id=$info['empresaId'];
					$oficina_id=$info['oficinaId'];
					$nombre_oficina=$info['nombreOficina'];					
					//echo "entro a for de detalles de pago";die;
					//print_r($info);
					$fecha_str=$info['feCreacion'];
					$fecha_exp=explode('-',$fecha_str);
					$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
					
					$newformat=$fecha_exp[2]."/".$fecha_exp[1]."/".$fecha_exp[0];
					$newformat_depo=$newformat;
					
                    //Depende de tipo 
                    $cta_contable="";
                    $no_cta="";
                    $es_tarjeta="N";
                    
                    if($info['bancoTipoCuentaId']!="")
                    {
						//echo "entro al bcotipocta";die;
						$cta_contable_array=$this->verificarTipoCuentaAnticipoSinCliente($info['bancoTipoCuentaId']);
						//echo "obtuvo cuenta contable";die;
						$cta_contable=$cta_contable_array['ctaContable'];
						$no_cta=$cta_contable_array['noCta'];
						$nombre_banco=$cta_contable_array['descripcionBanco'];
						$cta_contable_anticipo=$cta_contable_array['ctaContableAntSinClientes'];
						$es_tarjeta=$cta_contable_array['esTarjeta'];
						if($es_tarjeta=='S')
							$cta_contable=$cta_contable_array['ctaTarjeta'];
					}
					elseif($info['bancoCtaContableId']!="")
					{
						//echo "entro al bcotipocta";die;
						$cta_contable_array=$this->verificarTipoCuentaAnticipoSinClienteContable($info['bancoCtaContableId']);
						//echo "obtuvo cuenta contable";die;
						$cta_contable=$cta_contable_array['ctaContable'];
						$no_cta=$cta_contable_array['noCta'];
						$nombre_banco=$cta_contable_array['descripcionBanco'];
						$cta_contable_anticipo=$cta_contable_array['ctaContableAntSinClientes'];
						$es_tarjeta=$cta_contable_array['esTarjeta'];
						if($es_tarjeta=='S')
							$cta_contable=$cta_contable_array['ctaTarjeta'];
					}
					
					if($no_cta!="" && $es_tarjeta=="N")
					{
						//echo "entro a grabar en arckmm";die;
						/*$migra_arckmm = new MigraArckmm();
						$migra_arckmm->setNoCia($empresa_id);
						$migra_arckmm->setNoCta($no_cta);
						$migra_arckmm->setProcedencia('C');
						$migra_arckmm->setTipoDoc('NC');
						$migra_arckmm->setNoDocu($info['id']);
						$migra_arckmm->setFecha(new \DateTime($fecha_mod));*/
						
						$nombre_banco_str="";
						$glosa="";
						
						if($nombre_banco=="BANCO DEL PACIFICO" || $nombre_banco=="BANCO DEL AUSTRO")
							$nombre_banco_str=substr($nombre_banco,10,3);
						else
							$nombre_banco_str=substr($nombre_banco,6,3);
						
						$numero="";
						if($info['numeroCuentaBanco']!="")
							$numero=$info['numeroCuentaBanco'];
							
						if($info['numeroReferencia']!="")
							$numero=$info['numeroReferencia'];
						
						$fecha_mod_depo="";
						if($info['feDeposito']!="")
						{
							$fecha_deposito=$info['feDeposito'];
							$fecha_exp_depo=explode('-',$fecha_deposito);
							$fecha_mod_depo=$fecha_exp_depo[2]."-".$fecha_exp_depo[1]."-".$fecha_exp_depo[0];
							$newformat_depo=$fecha_exp_depo[2]."/".$fecha_exp_depo[1]."/".$fecha_exp_depo[0];
						}
						else
							$fecha_mod_depo=$fecha_mod;
							
						$glosa=$fecha_mod_depo.' '.substr($info['nombreOficina'],13,3).' '.$info['numeroPago'].' '.substr($info['codigoFormaPago'],0,3).' '.$numero.' '.$nombre_banco_str;
						$glosa=substr('Cruce. Ants. TELCOS '.$glosa,0,250);
						
						/*$migra_arckmm->setComentario(substr('Ants. TELCOS '.$glosa,0,250));
						$migra_arckmm->setMonto($info['valorPago']);
						$migra_arckmm->setEstado('P');
						$migra_arckmm->setConciliado('N');
						$migra_arckmm->setMes($fecha_exp[1]);
						$migra_arckmm->setAno($fecha_exp[0]);
						$migra_arckmm->setIndOtromov('S');
						$migra_arckmm->setMonedaCta('P');
						$migra_arckmm->setTipoCambio('1');
						$migra_arckmm->setTCambCV('C');
						$migra_arckmm->setIndOtrosMeses('N');
						$migra_arckmm->setNoFisico(substr($numero,0,11));
						$migra_arckmm->setOrigen('TN');
						$migra_arckmm->setUsuarioCreacion($info['usrCreacion']);
						$migra_arckmm->setFechaDoc(new \DateTime($fecha_mod));
						$migra_arckmm->setIndDivision('N');  
						$migra_arckmm->setFechaCreacion(new \DateTime($fecha_mod));					
						
						//print_r($migra_arckmm);                    
						$em_naf->persist($migra_arckmm);
						$em_naf->flush();*/
						
						$valores['Pv_IdCompania']='09';	
						$valores['Pv_NoCuenta']=$no_cta;
						$valores['Pv_TipoDocumento']='NC';
						$valores['Pv_IdDcoumento']=$info['id'].'30';
						$valores['Pd_Fecha']=$newformat;
						$valores['Pd_FechaDoc']=$newformat_depo;
						$valores['Pv_Comentario']=$glosa;
						$valores['Pn_Monto']=$info['valorPago'];
						$valores['Pn_Anio']=$fecha_exp[0];
						$valores['Pn_Mes']=$fecha_exp[1];
						$valores['Pv_UsuarioCrea']=$info['usrCreacion'];
						$valores['Pd_FechaCrea']=$newformat;
						if(!empty($numero) || $numero!="")
							$valores['Pv_NoFisico']=substr($numero,0,11);
						else
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
					
						//Cta de caja , bancos
						/*$migra_arckml = new MigraArckml();                
						$migra_arckml->setNoCia($empresa_id);
						$migra_arckml->setProcedencia('C');
						$migra_arckml->setTipoDoc('NC');
						$migra_arckml->setNoDocu($info['id']);
						//cta contable
						$migra_arckml->setCodCont($cta_contable);
						$migra_arckml->setCentroCosto('000000000');
						$migra_arckml->setTipoMov('D');
						$migra_arckml->setMonto($info['valorPago']);
						$migra_arckml->setMontoDol($info['valorPago']);
						$migra_arckml->setTipoCambio(1);
						$migra_arckml->setMoneda('P');
						$migra_arckml->setModificable('N');
						$migra_arckml->setAno($fecha_exp[0]);
						$migra_arckml->setMes($fecha_exp[1]);
						$migra_arckml->setMontoDc($info['valorPago']);
						//$migra_arckml->setGlosa(substr('NC:0- ants TELCOS '.date('d-m-Y').' '.$nombre_oficina,0,100)); 
						//$migra_arckml->setGlosa(substr('NC:0- ants TELCOS '.$glosa,0,100)); 
						$migra_arckml->setGlosa(substr('Ants. TELCOS '.$glosa,0,100)); 
						
						//print_r($migra_arckml);
                    
						$em_naf->persist($migra_arckml);
						$em_naf->flush();*/
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='NC';
						$valores_dos['Pv_IdDocumento']=$info['id'].'30';
						$valores_dos['Pv_CtaContable']=$cta_contable;
						$valores_dos['Pv_CentroCosto']='000000000';
						$valores_dos['Pv_TipoMov']='C';
						$valores_dos['Pn_Monto']=$info['valorPago'];
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
						$migra_arckml->setCodCont($cta_contable_anticipo);
						$migra_arckml->setCentroCosto('000000000');
						$migra_arckml->setTipoMov('C');
						$migra_arckml->setMonto($info['valorPago']);
						$migra_arckml->setMontoDol($info['valorPago']);
						$migra_arckml->setTipoCambio(1);
						$migra_arckml->setMoneda('P');
						$migra_arckml->setModificable('S');
						$migra_arckml->setAno($fecha_exp[0]);
						$migra_arckml->setMes($fecha_exp[1]);
						$migra_arckml->setMontoDc($info['valorPago']);
						//$migra_arckml->setGlosa(substr('NC:0- ants TELCOS '.date('d-m-Y').' '.$nombre_oficina,0,100));         
						//$migra_arckml->setGlosa(substr('NC:0- ants TELCOS '.$glosa,0,100)); 
						$migra_arckml->setGlosa(substr('Ants. TELCOS '.$glosa,0,100)); 
						
						//print_r($migra_arckml);
						
						$em_naf->persist($migra_arckml);
						$em_naf->flush();
					
						$em_naf->getConnection()->commit();*/
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='NC';
						$valores_dos['Pv_IdDocumento']=$info['id'].'30';
						$valores_dos['Pv_CtaContable']=$cta_contable_anticipo;
						$valores_dos['Pv_CentroCosto']='000000000';
						$valores_dos['Pv_TipoMov']='D';
						$valores_dos['Pn_Monto']=$info['valorPago'];
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
					}
				}
			}
				
				/*return $this->redirect(
					$this->generateUrl("infopagocab_show", array("id" => $id_pago))
				);*/
				return true;
				
			}
			catch (\Exception $e) {
					echo $e->getMessage();
					$em_naf->getConnection()->rollback();
					$em_naf->getConnection()->close();
					die();
					die();
			}
	}
		return true;	
	/*return $this->redirect(
		$this->generateUrl("infopagocab_show", array("id" => $id_pago))
	);*/
	}
	
	public function verificarTipoCuentaAnticipoSinCliente($banco_tipo_cta_id)
	{
		$em = $this->getDoctrine()->getManager("telconet_general");
		
		$cta_contable=$em->getRepository('schemaBundle:InfoPagoCab')->getAnticipoSinCliente($banco_tipo_cta_id);
		
		return $cta_contable;
	}
	
	public function verificarTipoCuentaAnticipoSinClienteContable($banco_cta_contable_id)
	{
		$em = $this->getDoctrine()->getManager("telconet_general");
		
		$cta_contable=$em->getRepository('schemaBundle:InfoPagoCab')->getAnticipoSinClienteContable($banco_cta_contable_id);
		
		return $cta_contable;
	}
	
	public function buscarExistentente($id_pago,$id_pago_det,$empresa_id)
	{
		$variable_existe['migra_arckmm']=0;
		$variable_existe['migra_arcgae']=0;
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		$resultado = $em_naf->getRepository('schemaBundle:MigraArckmm')->getExisteCruceContabilidad($id_pago_det,$empresa_id);
		$listadoPagos=$resultado['registros'];
		$total=$resultado['total'];
		
		if($total>=1)
		{
			$variable_existe['migra_arckmm']=1;
			$variable_existe['migra_arcgae']=1;
		}
		
		return $variable_existe;
	}		
}
