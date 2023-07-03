<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class AnticiposController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('contabilizacionesBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function contabilizarAnticiposAction($id_pago)
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
		
		
		$em = $this->getDoctrine()->getManager("telconet_financiero");
		//$request = $this->getRequest();
		//$session=$request->getSession();
		
		/*$empresa_id=$session->get('idEmpresa');
		$oficina_id=$session->get('idOficina');
		$ptocliente=$session->get('ptoCliente');
		$nombre_oficina=$session->get('nombreOficina');*/
		
		
		$info_total = $em->getRepository('schemaBundle:InfoPagoCab')->getDetallePago($id_pago,"ANT");
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		//$em_naf->getConnection()->beginTransaction();
		
		//$cta_contable_cliente = $em->getRepository('schemaBundle:InfoPagoCab')->getCtaCliente($empresa_id, $oficina_id,"ANT");
		//die();
		//echo "paso la cta contable!";
		
		//print_r($cta_contable_cliente);
		//print_r($info_total);
		
		//die();
		if($info_total)
		{
			
			//$em->getConnection()->beginTransaction();
			try
			{
				//$oci_con = oci_connect("naf47_tnet", "naf47_tnet", "192.168.213.227/XE");
//				$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", "172.24.5.78/dboracle");
				$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.24.5.78)(PORT = 1521)))(CONNECT_DATA=(SID=dboracle)))" ;
				$oci_con = oci_connect("TNET_SISTEMAS", "TNETSISTEMAS", $db);
				
				foreach($info_total as $info)
				{
					$busqueda=$this->buscarExistentente($id_pago,$info['id'],$info['empresaId']);
					$variable_existe['migra_arckmm']=0;
					$variable_existe['migra_arcgae']=0;
					if($busqueda['migra_arckmm']==1 || $busqueda['migra_arcgae']==1)
						echo "Ya existe: pago : ".$id_pago."det: ".$info['id'];
					else
					{
						echo "Va a procesar";
						
					$empresa_id=$info['empresaId'];
					$oficina_id=$info['oficinaId'];
					$nombre_oficina=$info['nombreOficina'];					
					$cta_contable_cliente = $em->getRepository('schemaBundle:InfoPagoCab')->getCtaCliente($empresa_id, $oficina_id,"ANT");
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
                    
                    if ($info['codigoFormaPago']!='EFEC' && $info['codigoFormaPago']!='CHEQ' && $info['codigoFormaPago']!='RF2' && $info['codigoFormaPago']!='RF8')
                    {
						if($info['bancoTipoCuentaId']!="")
						{
							$cta_contable_array=$this->verificarTipoCuenta($info['bancoTipoCuentaId']);
							$cta_contable=$cta_contable_array['ctaContable'];
							$nombre_banco=$cta_contable_array['descripcionBanco'];
							$no_cta=$cta_contable_array['noCta'];
							$es_tarjeta=$cta_contable_array['esTarjeta'];
							if($es_tarjeta=='S')
								$cta_contable=$cta_contable_array['ctaTarjeta'];
						}
						elseif($info['bancoCtaContableId']!="")
						{
							$cta_contable_array=$this->verificarTipoCuentaContable($info['bancoCtaContableId']);
							$cta_contable=$cta_contable_array['ctaContable'];
							$nombre_banco=$cta_contable_array['descripcionBanco'];
							//echo "<br>CtaContable:".$cta_contable;
							//die;
							$no_cta=$cta_contable_array['noCta'];
							$es_tarjeta=$cta_contable_array['esTarjeta'];
							if($es_tarjeta=='S')
								$cta_contable=$cta_contable_array['ctaTarjeta'];
						}
						else
						{
							if($info['ctaContable']!="")
							{
								$cta_contable=$info['ctaContable'];
								//$no_cta=$info['noCta'];
								$no_cta="";
							}
							else
							{
								$cta_contable_array=$this->verificarClasificacion($info['codigoFormaPago'],$empresa_id, $oficina_id);
								$cta_contable=$cta_contable_array['ctaContablePagos'];
								$no_cta=$cta_contable_array['noCta'];
								if($info['bancoTipoCuentaId']!="")
								{
									$resultado_bnc=$this->obtenerBanco($info['bancoTipoCuentaId']);
									$nombre_banco=$resultado_bnc['descripcionBanco'];
								}
								elseif($info['bancoCtaContableId']!="")
								{
									$resultado_bnc=$this->obtenerBancoPorContable($info['bancoCtaContableId']);
									$nombre_banco=$resultado_bnc['descripcionBanco'];
								}
								else
									$nombre_banco="";
							}
							
						}	
					}
					else
					{
						if($info['ctaContable']!="")
						{
							$cta_contable=$info['ctaContable'];
							//$no_cta=$info['noCta'];
							$no_cta="";
						}
						else
						{
							$cta_contable_array=$this->verificarClasificacion($info['codigoFormaPago'],$empresa_id, $oficina_id);
							$cta_contable=$cta_contable_array['ctaContablePagos'];
							$no_cta=$cta_contable_array['noCta'];
							if($info['bancoTipoCuentaId']!="")
							{
								$resultado_bnc=$this->obtenerBanco($info['bancoTipoCuentaId']);
								$nombre_banco=$resultado_bnc['descripcionBanco'];
							}
							elseif($info['bancoCtaContableId']!="")
							{
								$resultado_bnc=$this->obtenerBancoPorContable($info['bancoCtaContableId']);
								$nombre_banco=$resultado_bnc['descripcionBanco'];
							}
							else
								$nombre_banco="";
						}
					}
					
					/*echo $no_cta;
					echo "-";
					echo $es_tarjeta;
					
					die();*/
					
					if($no_cta!="" && $es_tarjeta=="N")
					{
						
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
							
						$glosa=$info['login'].' TELCOS '.$fecha_mod_depo.' '.substr($info['nombreOficina'],13,3).' '.$info['numeroPago'].' '.substr($info['codigoFormaPago'],0,3).' '.$numero.' '.$nombre_banco_str;
						$glosa=substr('Ant. user: '.$glosa,0,250);
						
						
						$valores['Pv_IdCompania']='09';	
						$valores['Pv_NoCuenta']=$no_cta;
						$valores['Pv_TipoDocumento']='NC';
						$valores['Pv_IdDcoumento']=$info['id'];
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
						
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='NC';
						$valores_dos['Pv_IdDocumento']=$info['id'];
						$valores_dos['Pv_CtaContable']=$cta_contable;
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
						
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='NC';
						$valores_dos['Pv_IdDocumento']=$info['id'];
						$valores_dos['Pv_CtaContable']=$cta_contable_cliente['ctaContableAnticipos'];
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
					{
						$no_linea=0;
						//Asiento en otras tablas xq es de otro tipo la cta
						$fecha_str=$info['feCreacion'];
						$fecha_exp=explode('-',$fecha_str);
						$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
						//Asiento
						
						$nombre_banco_str="";
						$glosa="";
						
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
						}
						else
							$fecha_mod_depo=$fecha_mod;
							
						$glosa=$info['login'].' TELCOS '.$fecha_mod_depo.' '.substr($info['nombreOficina'],13,3).' '.$info['numeroPago'].' '.substr($info['codigoFormaPago'],0,3).' '.$numero;
						$glosa=substr('Ant. user: '.$glosa,0,250);
						
						
						$valores['pv_idcompania']='09';
						$valores['pn_anio']=$fecha_exp[0];
						$valores['pn_mes']=$fecha_exp[1];
						$valores['pv_idasiento']=$info['id'].'77';
						$valores['pv_concepto']=$glosa;
						$valores['pn_totaldebe']=$info['valorPago'];
						$valores['pn_totalhaber']=$info['valorPago'];
						if($info['codigoFormaPago']=='RF2' || $info['codigoFormaPago']=='RF8' || $info['codigoFormaPago']=='RTF')
							$valores['pv_tipodiario']="M_RET";
						else
						{
							if($info['codigoFormaPago']=='DEB')
								$valores['pv_tipodiario']="M_TAR";
							else
								$valores['pv_tipodiario']="M_NC1";
						}
						$valores['pv_uuariocrea']=$info['usrCreacion'];
						
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
					
						//Cta deudora
						
						$no_linea=1;
				
						//Llamada al procedimiento
						$valores_dos['pv_idcompania']='09';
						$valores_dos['pn_anio']=$fecha_exp[0];
						$valores_dos['pn_mes']=$fecha_exp[1];
						$valores_dos['pv_idasiento']=$info['id'].'77';
						$valores_dos['pn_linea']=$no_linea;
						$valores_dos['pv_ctacontable']=$cta_contable;
						$valores_dos['pv_concepto']=$glosa;
						$valores_dos['pn_monto']=$info['valorPago'];
						if($info['codigoFormaPago']=='RF2' || $info['codigoFormaPago']=='RF8' || $info['codigoFormaPago']=='RTF')
							$valores_dos['pv_tipodiario']="M_RET";
						else
						{
							if($info['codigoFormaPago']=='DEB')
								$valores_dos['pv_tipodiario']="M_TAR";
							else
								$valores_dos['pv_tipodiario']="M_NC1";
						}
						
						$valores_dos['pv_tipo']="D";
						
						echo "<pre>";
						print_r($valores_dos);
						echo "</pre>";
						
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
						}
						else		
							echo "No connect";
						
						$no_linea = $no_linea + 1;
						
						//Cuenta acreedora
						
						
						$valores_dos['pv_idcompania']='09';
						$valores_dos['pn_anio']=$fecha_exp[0];
						$valores_dos['pn_mes']=$fecha_exp[1];
						$valores_dos['pv_idasiento']=$info['id'].'77';
						$valores_dos['pn_linea']=$no_linea;
						$valores_dos['pv_ctacontable']=$cta_contable_cliente['ctaContableAnticipos'];
						$valores_dos['pv_concepto']=$glosa;
						$valores_dos['pn_monto']=$info['valorPago']*(-1);
						if($info['codigoFormaPago']=='RF2' || $info['codigoFormaPago']=='RF8' || $info['codigoFormaPago']=='RTF')
							$valores_dos['pv_tipodiario']="M_RET";
						else
						{
							if($info['codigoFormaPago']=='DEB')
								$valores_dos['pv_tipodiario']="M_TAR";
							else
								$valores_dos['pv_tipodiario']="M_NC1";
						}
						
						$valores_dos['pv_tipo']="C";
						
						echo "<pre>";
						print_r($valores_dos);
						echo "</pre>";
						
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
					//$em_naf->getConnection()->rollback();
					//$em_naf->getConnection()->close();
					die();
					die();
			}
	}
		return true;	
	/*return $this->redirect(
		$this->generateUrl("infopagocab_show", array("id" => $id_pago))
	);*/
	}
	
	public function verificarTipoCuenta($banco_tipo_cta_id)
	{
		$em = $this->getDoctrine()->getManager("telconet_general");
		
		$cta_contable=$em->getRepository('schemaBundle:InfoPagoCab')->getBanco($banco_tipo_cta_id);
		
		return $cta_contable;
	}
	
	public function verificarTipoCuentaContable($banco_cta_contable_id)
	{
		$em = $this->getDoctrine()->getManager("telconet_general");
		
		$cta_contable=$em->getRepository('schemaBundle:InfoPagoCab')->getBancoContable($banco_cta_contable_id);
		
		return $cta_contable;
	}
	
	public function verificarClasificacion($codigoFormaPago,$empresa_id, $oficina_id)
	{
		if($codigoFormaPago=='EFEC' || $codigoFormaPago=='CHEQ')
		{
			$em = $this->getDoctrine()->getManager("telconet");
			//Debo cargar las ctas contables de caja chica segun la empresa
			$cta_contable=$em->getRepository('schemaBundle:InfoPagoCab')->getCajaChica($empresa_id, $oficina_id);
			
			return $cta_contable;
		}
		
	}
	
	public function obtenerBanco($banco_tipo_cta_id)
	{
		$em = $this->getDoctrine()->getManager("telconet_general");
		$nombre_banco=$em->getRepository('schemaBundle:InfoPagoCab')->getNombreBanco($banco_tipo_cta_id);
		return $nombre_banco;
	}
	
	public function obtenerBancoPorContable($banco_cta_contable_id)
	{
		$em = $this->getDoctrine()->getManager("telconet_general");
		$nombre_banco=$em->getRepository('schemaBundle:InfoPagoCab')->getNombreBancoContable($banco_cta_contable_id);
		return $nombre_banco;
	}
	
	public function buscarExistentente($id_pago,$id_pago_det,$empresa_id)
	{
		$variable_existe['migra_arckmm']=0;
		$variable_existe['migra_arcgae']=0;
		
		$em_naf = $this->getDoctrine()->getManager("telconet_naf");
		$resultado = $em_naf->getRepository('schemaBundle:MigraArckmm')->getExisteContabilidad($id_pago_det,$empresa_id);
		$listadoPagos=$resultado['registros'];
		$total=$resultado['total'];
		
		if($total>=1)
			$variable_existe['migra_arckmm']=1;
			
		$resultado_dos = $em_naf->getRepository('schemaBundle:MigraArcgae')->getExisteContabilidad($id_pago_det,$empresa_id);
		$listadoPagos_dos=$resultado_dos['registros'];
		$total_dos=$resultado_dos['total'];
			
		if($total_dos>=1)
			$variable_existe['migra_arcgae']=1;
			
		return $variable_existe;
	}				
}
