<?php

namespace telconet\contabilizacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\MigraArckmm;
use telconet\schemaBundle\Entity\MigraArckml;
use telconet\schemaBundle\Entity\MigraArcgae;
use telconet\schemaBundle\Entity\MigraArcgal;

class AnticiposAnulacionController extends Controller
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
						/*$migra_arckmm = new MigraArckmm();
						$migra_arckmm->setNoCia($empresa_id);
						$migra_arckmm->setNoCta($no_cta);
						$migra_arckmm->setProcedencia('C');
						$migra_arckmm->setTipoDoc('NC');
						$migra_arckmm->setNoDocu($info['id']);
						$migra_arckmm->setFecha(new \DateTime($fecha_mod));
						*/
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
						$glosa=substr('Anul. Ant. user: '.$glosa,0,250);
						/*
						$migra_arckmm->setComentario(substr('Ant. user: '.$glosa,0,250));
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
						$valores['Pv_TipoDocumento']='ND';
						$valores['Pv_IdDcoumento']=$info['id'].'50';
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
						$migra_arckml->setTipoMov('C');
						$migra_arckml->setMonto($info['valorPago']);
						$migra_arckml->setMontoDol($info['valorPago']);
						$migra_arckml->setTipoCambio(1);
						$migra_arckml->setMoneda('P');
						$migra_arckml->setModificable('N');
						$migra_arckml->setAno($fecha_exp[0]);
						$migra_arckml->setMes($fecha_exp[1]);
						$migra_arckml->setMontoDc($info['valorPago']);
						//$migra_arckml->setGlosa(substr('NC:0- ant user: '.$glosa,0,100)); 
						$migra_arckml->setGlosa(substr('Ant. user: '.$glosa,0,100)); 
						
						//print_r($migra_arckml);
                    
						$em_naf->persist($migra_arckml);
						$em_naf->flush();*/
						
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='ND';
						$valores_dos['Pv_IdDocumento']=$info['id'].'50';
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
						$migra_arckml->setCodCont($cta_contable_cliente['ctaContableAnticipos']);
						$migra_arckml->setCentroCosto('000000000');
						$migra_arckml->setTipoMov('D');
						$migra_arckml->setMonto($info['valorPago']);
						$migra_arckml->setMontoDol($info['valorPago']);
						$migra_arckml->setTipoCambio(1);
						$migra_arckml->setMoneda('P');
						$migra_arckml->setModificable('S');
						$migra_arckml->setAno($fecha_exp[0]);
						$migra_arckml->setMes($fecha_exp[1]);
						$migra_arckml->setMontoDc($info['valorPago']);
						//$migra_arckml->setGlosa(substr('NC:0- ant user: '.$glosa,0,100));         
						$migra_arckml->setGlosa(substr('Ant. user: '.$glosa,0,100));         
						
						//print_r($migra_arckml);
						
						$em_naf->persist($migra_arckml);
						$em_naf->flush();
					
						$em_naf->getConnection()->commit();*/
						
						$valores_dos['Pv_IdCompania']='09';
						$valores_dos['Pv_TipoDocumento']='ND';
						$valores_dos['Pv_IdDocumento']=$info['id'].'50';
						$valores_dos['Pv_CtaContable']=$cta_contable_cliente['ctaContableAnticipos'];
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
					else
					{
						$no_linea=0;
						//Asiento en otras tablas xq es de otro tipo la cta
						$fecha_str=$info['feCreacion'];
						$fecha_exp=explode('-',$fecha_str);
						$fecha_mod=$fecha_exp[2]."-".$fecha_exp[1]."-".$fecha_exp[0];
						//Asiento
						/*$migra_arcgae = new MigraArcgae();
						$migra_arcgae->setNoCia($empresa_id);
						$migra_arcgae->setAno($fecha_exp[0]);
						$migra_arcgae->setMes($fecha_exp[1]);
						$migra_arcgae->setNoAsiento($info['id'].'77');
						$migra_arcgae->setImpreso('N');
						$migra_arcgae->setFecha(new \DateTime($fecha_mod));*/
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
						$glosa=substr('Anul. Ant. user: '.$glosa,0,250);
						
						//$migra_arcgae->setDescri1(substr('Ant. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$nombre_oficina,0,250));
						/*$migra_arcgae->setDescri1(substr('Ant. user: '.$glosa,0,250));
						$migra_arcgae->setEstado('P');
						$migra_arcgae->setAutorizado('N');
						$migra_arcgae->setOrigen('TN');
						$migra_arcgae->setTDebitos($info['valorPago']);
						$migra_arcgae->setTCreditos($info['valorPago']);
						//Codigo de diario nuevo que debe crear luis
						if($info['codigoFormaPago']=='RF2' || $info['codigoFormaPago']=='RF8' || $info['codigoFormaPago']=='RTF')
							$migra_arcgae->setCodDiario('M_RET');
						else
						{
							if($info['codigoFormaPago']=='DEB')
								$migra_arcgae->setCodDiario('M_TAR');
							else
								$migra_arcgae->setCodDiario('M_NC1');
						}
							
						//$migra_arcgae->setCodDiario('M_NC1');
						$migra_arcgae->setTCambCV('C');
						$migra_arcgae->setTipoCambio('1');
						$migra_arcgae->setTipoComprobante('T');
						$migra_arcgae->setAnulado('N');
						$migra_arcgae->setUsuarioCreacion($info['usrCreacion']);
						$migra_arcgae->setTransferido('N');
						$migra_arcgae->setFechaCreacion(new \DateTime($fecha_mod));
						
						//print_r($migra_arcgae);
						
						$em_naf->persist($migra_arcgae);
						//$em_naf->flush();*/
						
						$valores['pv_idcompania']='09';
						$valores['pn_anio']=$fecha_exp[0];
						$valores['pn_mes']=$fecha_exp[1];
						$valores['pv_idasiento']=$info['id'].'50';
						$valores['pv_concepto']=$glosa;
						$valores['pn_totaldebe']=$info['valorPago'];
						$valores['pn_totalhaber']=$info['valorPago'];
						$valores['pv_tipodiario']="MRVP";
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
						/*$migra_arcgal = new MigraArcgal();                
						$migra_arcgal->setNoCia($empresa_id);
						$migra_arcgal->setAno($fecha_exp[0]);
						$migra_arcgal->setMes($fecha_exp[1]);
						$migra_arcgal->setNoAsiento($info['id'].'77');
						$migra_arcgal->setNoLinea('1');
						$migra_arcgal->setCuenta($cta_contable);
						//$migra_arcgal->setDescri('Ant. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$nombre_oficina);
						$migra_arcgal->setDescri('Ant. user: '.$glosa);
						//Codigo de diario nuevo que debe crear luis
						if($info['codigoFormaPago']=='RF2' || $info['codigoFormaPago']=='RF8' || $info['codigoFormaPago']=='RTF')
							$migra_arcgal->setCodDiario('M_RET');
						else
						{
							if($info['codigoFormaPago']=='DEB')
								$migra_arcgal->setCodDiario('M_TAR');
							else
								$migra_arcgal->setCodDiario('M_NC1');
						}
						//$migra_arcgal->setCodDiario('M_NC1');
						$migra_arcgal->setMoneda('P');
						$migra_arcgal->setTipoCambio(1);
						$migra_arcgal->setMonto($info['valorPago']);
						$migra_arcgal->setCentroCosto('000000000');
						$migra_arcgal->setTipo('C');
						$migra_arcgal->setMontoDol($info['valorPago']);
						$migra_arcgal->setCc1('000');
						$migra_arcgal->setCc2('000');
						$migra_arcgal->setCc3('000');
						$migra_arcgal->setLineaAjustePrecision('N');

						//print_r($migra_arcgal);
						
						$em_naf->persist($migra_arcgal);*/
						
						$no_linea=1;
				
						//Llamada al procedimiento
						$valores_dos['pv_idcompania']='09';
						$valores_dos['pn_anio']=$fecha_exp[0];
						$valores_dos['pn_mes']=$fecha_exp[1];
						$valores_dos['pv_idasiento']=$info['id'].'50';
						$valores_dos['pn_linea']=$no_linea;
						$valores_dos['pv_ctacontable']=$cta_contable;
						$valores_dos['pv_concepto']=$glosa;
						$valores_dos['pn_monto']=$info['valorPago']*(-1);
						$valores_dos['pv_tipodiario']="MRVP";
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
						
						
						$no_linea = $no_linea + 1;
						/*
						//Cuenta acreedora
						$migra_arcgal = new MigraArcgal();                
						$migra_arcgal->setNoCia($empresa_id);
						$migra_arcgal->setAno($fecha_exp[0]);
						$migra_arcgal->setMes($fecha_exp[1]);
						$migra_arcgal->setNoAsiento($info['id'].'77');
						$migra_arcgal->setNoLinea($no_linea);
						$migra_arcgal->setCuenta($cta_contable_cliente['ctaContableAnticipos']);
						$migra_arcgal->setDescri('Ant. user: '.$glosa);
						//$migra_arcgal->setDescri('Ant. user: '.$ptocliente['login'].' TELCOS '.$fecha_mod.' '.$nombre_oficina);                
						//Codigo de diario nuevo que debe crear luis
						if($info['codigoFormaPago']=='RF2' || $info['codigoFormaPago']=='RF8' || $info['codigoFormaPago']=='RTF')
							$migra_arcgal->setCodDiario('M_RET');
						else
						{
							if($info['codigoFormaPago']=='DEB')
								$migra_arcgal->setCodDiario('M_TAR');
							else
								$migra_arcgal->setCodDiario('M_NC1');
						}
						
						//$migra_arcgal->setCodDiario('M_NC1');
						$migra_arcgal->setMoneda('P');
						$migra_arcgal->setTipoCambio(1);
						$migra_arcgal->setMonto($info['valorPago']*(-1));
						$migra_arcgal->setCentroCosto('000000000');
						$migra_arcgal->setTipo('D');
						$migra_arcgal->setMontoDol($info['valorPago']*(-1));
						$migra_arcgal->setCc1('000');
						$migra_arcgal->setCc2('000');
						$migra_arcgal->setCc3('000');
						$migra_arcgal->setLineaAjustePrecision('N');              
				
						$em_naf->persist($migra_arcgal);
						$em_naf->flush();
						
						$em_naf->getConnection()->commit();*/
						
						$valores_dos['pv_idcompania']='09';
						$valores_dos['pn_anio']=$fecha_exp[0];
						$valores_dos['pn_mes']=$fecha_exp[1];
						$valores_dos['pv_idasiento']=$info['id'].'50';
						$valores_dos['pn_linea']=$no_linea;
						$valores_dos['pv_ctacontable']=$cta_contable_cliente['ctaContableClientes'];
						$valores_dos['pv_concepto']=$glosa;
						$valores_dos['pn_monto']=$info['valorPago'];
						$valores_dos['pv_tipodiario']="MRVP";
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
		$resultado = $em_naf->getRepository('schemaBundle:MigraArckmm')->getExisteAnuladosContabilidad($id_pago_det,$empresa_id);
		$listadoPagos=$resultado['registros'];
		$total=$resultado['total'];
		
		if($total>=1)
			$variable_existe['migra_arckmm']=1;
			
		$resultado_dos = $em_naf->getRepository('schemaBundle:MigraArcgae')->getExisteAnuladosContabilidad($id_pago,$empresa_id);
		$listadoPagos_dos=$resultado_dos['registros'];
		$total_dos=$resultado_dos['total'];
			
		if($total_dos>=1)
			$variable_existe['migra_arcgae']=1;
			
		return $variable_existe;
	}				
}
