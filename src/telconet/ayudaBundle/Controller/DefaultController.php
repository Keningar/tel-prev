<?php

namespace telconet\ayudaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ayudaBundle:Default:index.html.twig', array('name' => $name));
    }
	public function ayudaAction()
	{
		return $this->render('ayudaBundle:Default:ayuda.html.twig');
	}
	public function modulo_comercialAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:modulo_comercial2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:modulo_comercial.html.twig');
		}
		
		
		//return $this->render('ayudaBundle:Default:modulo_comercial.html.twig');
	}
	
	public function modulo_comercial2Action()
	{
		return $this->render('ayudaBundle:Default:modulo_comercial2.html.twig');
	}
	public function introduccionAction()
	{
		return $this->render('ayudaBundle:Default:introduccion.html.twig');
	}
	
	public function modulo_inicioAction()
	{
		return $this->render('ayudaBundle:Default:modulo_inicio.html.twig');
	}
	
	public function modulo_financieroAction()
	{
		return $this->render('ayudaBundle:Default:modulo_financiero.html.twig');
	}
	
	public function modulo_tecnicoAction()
	{
		return $this->render('ayudaBundle:Default:modulo_tecnico.html.twig');
	}
	public function tecnico_clientesAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_clientes.html.twig');
	}
	public function tecnico_elementosAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_elementos.html.twig');
	}
	public function tecnico_enlaceAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_enlace.html.twig');
	}
	public function tecnico_procesos_masivosAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_procesos_masivos.html.twig');
	}
	
	public function cambio_plan_masivoAction()
	{
		return $this->render('ayudaBundle:Default:cambio_plan_masivo.html.twig');
	}
	public function cortes_masivosAction()
	{
		return $this->render('ayudaBundle:Default:cortes_masivos.html.twig');
	}
	public function reactivacion_masivaAction()
	{
		return $this->render('ayudaBundle:Default:reactivacion_masiva.html.twig');
	}
	public function tecnico_dslamAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_dslam.html.twig');
	}
	public function tecnico_oltAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_olt.html.twig');
	}
	
	public function tecnico_popAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_pop.html.twig');
	}
	
	public function tecnico_radioAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_radio.html.twig');
	}
	public function tecnico_nodoAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_nodo.html.twig');
	}
	
	public function tecnico_servidorAction()
	{
		return $this->render('ayudaBundle:Default:tecnico_servidor.html.twig');
	}
	
	public function modulo_planificacionAction()
	{
		return $this->render('ayudaBundle:Default:modulo_planificacion.html.twig');
	}
	public function planificacion_factibilidadAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:planificacion_factibilidad2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:planificacion_factibilidad.html.twig');
		}
		//return $this->render('ayudaBundle:Default:planificacion_factibilidad.html.twig');
	}
	
	public function planificacion_factibilidad2Action()
	{
		return $this->render('ayudaBundle:Default:planificacion_factibilidad2.html.twig');
	}
	
	public function planificacion_planAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:planificacion_plan2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:planificacion_plan.html.twig');
		}
		
		//return $this->render('ayudaBundle:Default:planificacion_plan.html.twig');
	}
	public function planificacion_plan2Action()
	{
		return $this->render('ayudaBundle:Default:planificacion_plan2.html.twig');
	}
	public function planificacion_instalacionAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_instalacion.html.twig');
	}
	public function planificacion_reportesAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_reportes.html.twig');
	}
	public function planificacion_ingreso_materialAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_ingreso_material.html.twig');
	}
	public function planificacion_datos_instalacionAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_datos_instalacion.html.twig');
	}
	public function planificacion_reporte_generalAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_reporte_general.html.twig');
	}
	public function planificacion_reporte_asignadoAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_reporte_asignado.html.twig');
	}
	public function planificacion_reporte_supertelAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_reporte_supertel.html.twig');
	}
	public function planificacion_coordinarAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:planificacion_coordinar2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:planificacion_coordinar.html.twig');
		}
		//return $this->render('ayudaBundle:Default:planificacion_coordinar.html.twig');
	}
	public function planificacion_coordinar2Action()
	{
		return $this->render('ayudaBundle:Default:planificacion_coordinar2.html.twig');
	}
	public function planificacion_verAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:planificacion_ver2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:planificacion_ver.html.twig');
		}
		//return $this->render('ayudaBundle:Default:planificacion_ver.html.twig');
	}
	public function planificacion_ver2Action()
	{
		return $this->render('ayudaBundle:Default:planificacion_ver2.html.twig');
	}
	public function planificacion_asignarAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		return $this->render('ayudaBundle:Default:planificacion_asignar2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:planificacion_asignar.html.twig');
		}
		
		//return $this->render('ayudaBundle:Default:planificacion_asignar.html.twig');
	}
	public function planificacion_asignar2Action()
	{
		return $this->render('ayudaBundle:Default:planificacion_asignar2.html.twig');
	}
	public function planificacion_recursosAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		return $this->render('ayudaBundle:Default:planificacion_recursos2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:planificacion_recursos.html.twig');
		}
		//return $this->render('ayudaBundle:Default:planificacion_recursos.html.twig');
	}
	public function planificacion_recursos2Action()
	{
		return $this->render('ayudaBundle:Default:planificacion_recursos2.html.twig');
	}
	public function planificacion_retiroAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_retiro.html.twig');
	}
    public function planificacion_ver_asignacionAction()
	{
		return $this->render('ayudaBundle:Default:planificacion_ver_asignacion.html.twig');
	}
	public function modulo_soporteAction()
	{
		return $this->render('ayudaBundle:Default:modulo_soporte.html.twig');
	}
	public function soporte_casosAction()
	{
		return $this->render('ayudaBundle:Default:soporte_casos.html.twig');
	}
	public function soporte_actividadesAction()
	{
		return $this->render('ayudaBundle:Default:soporte_actividades.html.twig');
	}
	public function soporte_agendaAction()
	{
		return $this->render('ayudaBundle:Default:soporte_agenda.html.twig');
	}
	public function soporte_notificacionesAction()
	{
		return $this->render('ayudaBundle:Default:soporte_notificaciones.html.twig');
	}
	public function soporte_tareasAction()
	{
		return $this->render('ayudaBundle:Default:soporte_tareas.html.twig');
	}
	
	public function modulo_administracionAction()
	{
		return $this->render('ayudaBundle:Default:modulo_administracion.html.twig');
	}
	
	public function comercial_prospectoAction()
	{
				
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:comercial_prospecto2.html.twig');
		
		}else{
		return $this->render('ayudaBundle:Default:comercial_prospecto.html.twig');
		}
		
		
		//return $this->render('ayudaBundle:Default:comercial_prospecto.html.twig');
	}
	public function comercial_prospecto2Action()
	{
		return $this->render('ayudaBundle:Default:comercial_prospecto2.html.twig');
	}
	
	public function comercial_padre_facturaAction()
	{
		return $this->render('ayudaBundle:Default:comercial_padre_factura.html.twig');
	}
	public function comercial_convertir_ordenAction()
	{
		return $this->render('ayudaBundle:Default:comercial_convertir_orden.html.twig');
	}
		
	public function comercial_clienteAction()
	{
		return $this->render('ayudaBundle:Default:comercial_cliente.html.twig');
	}
	
	public function comercial_documentoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_documento.html.twig');
	}
	
	public function comercial_solicitudAction()
	{
		return $this->render('ayudaBundle:Default:comercial_solicitud.html.twig');
	}
	public function comercial_crear_contratoAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:comercial_crear_contrato2.html.twig');
		}else{
		
		return $this->render('ayudaBundle:Default:comercial_crear_contrato.html.twig');
		}
		
		//return $this->render('ayudaBundle:Default:comercial_crear_contrato.html.twig');
	}
	public function comercial_crear_contrato2Action()
	{
		return $this->render('ayudaBundle:Default:comercial_crear_contrato2.html.twig');
	}
	
	public function comercial_autorizacionAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:comercial_autorizacion2.html.twig');
		}else{
		
		return $this->render('ayudaBundle:Default:comercial_autorizacion.html.twig');
		
		}
		
		//return $this->render('ayudaBundle:Default:comercial_autorizacion.html.twig');
	}
	
	public function comercial_autorizacion2Action()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizacion2.html.twig');
	}
	
	public function comercial_reportesAction()
	{
		return $this->render('ayudaBundle:Default:comercial_reportes.html.twig');
	}
	
	public function comercial_contratoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_contrato.html.twig');
	}
	
	public function comercial_orden_trabajoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_orden_trabajo.html.twig');
	}
	
	public function comercial_referidoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_referido.html.twig');
	}
	
	public function comercial_cl_primeras_facturasAction()
	{
		return $this->render('ayudaBundle:Default:comercial_cl_primeras_facturas.html.twig');
	}
	public function comercial_nuevo_puntoAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:comercial_nuevo_punto2.html.twig');
		}else{
		
		return $this->render('ayudaBundle:Default:comercial_nuevo_punto.html.twig');
		
		}
		
		//return $this->render('ayudaBundle:Default:comercial_nuevo_punto.html.twig');
	}
	public function comercial_nuevo_punto2Action()
	{
		return $this->render('ayudaBundle:Default:comercial_nuevo_punto2.html.twig');
	}
	public function comercial_autorizar_contratoAction()
	{
		$request  = $this->get('request');
		$session  = $request->getSession();
		
		$prefijoEmpresa=$request->getSession()->get('prefijoEmpresa');
				
		if($prefijoEmpresa=='MD'){
		
		return $this->render('ayudaBundle:Default:comercial_autorizar_contrato2.html.twig');
		
		}else{
		
		return $this->render('ayudaBundle:Default:comercial_autorizar_contrato.html.twig');
		}
		
		//return $this->render('ayudaBundle:Default:comercial_autorizar_contrato.html.twig');
	}
	public function comercial_autorizar_contrato2Action()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizar_contrato2.html.twig');
	}
	public function comercial_consulta_solicitudAction()
	{
		return $this->render('ayudaBundle:Default:comercial_consulta_solicitud.html.twig');
	}
	public function comercial_solicitud_descuentoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_solicitud_descuento.html.twig');
	}
	public function comercial_cambio_documentoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_cambio_documento.html.twig');
	}
	
	public function comercial_suspension_temporalAction()
	{
		return $this->render('ayudaBundle:Default:comercial_suspension_temporal.html.twig');
	}
	public function comercial_cancelacionAction()
	{
		return $this->render('ayudaBundle:Default:comercial_cancelacion.html.twig');
	}
	public function comercial_autorizar_descuentoAction()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizar_descuento.html.twig');
	}
	public function comercial_autorizar_excedenteAction()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizar_excedente.html.twig');
	}
	public function comercial_autorizar_cancelacionAction()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizar_cancelacion.html.twig');
	}
	public function comercial_autorizar_suspensionAction()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizar_suspension.html.twig');
	}
	public function comercial_autorizar_cambioAction()
	{
		return $this->render('ayudaBundle:Default:comercial_autorizar_cambio.html.twig');
	}
		
	public function financiero_documentoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_documento.html.twig');
	}
	
	public function financiero_pagosAction()
	{
		return $this->render('ayudaBundle:Default:financiero_pagos.html.twig');
	}
	
	public function financiero_debitosAction()
	{
		return $this->render('ayudaBundle:Default:financiero_debitos.html.twig');
	}
	
	public function financiero_comisionesAction()
	{
		return $this->render('ayudaBundle:Default:financiero_comisiones.html.twig');
	}
	
	public function financiero_reportesAction()
	{
		return $this->render('ayudaBundle:Default:financiero_reportes.html.twig');
	}
	
	public function financiero_procesosAction()
	{
		return $this->render('ayudaBundle:Default:financiero_procesos.html.twig');
	}
	
	public function financiero_autorizacionesAction()
	{
		return $this->render('ayudaBundle:Default:financiero_autorizaciones.html.twig');
	}
	public function financiero_facturasAction()
	{
		return $this->render('ayudaBundle:Default:financiero_facturas.html.twig');
	}
	public function financiero_facturas_proporcionalAction()
	{
		return $this->render('ayudaBundle:Default:financiero_facturas_proporcional.html.twig');
	}
	public function financiero_notas_creditoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_notas_credito.html.twig');
	}
	public function financiero_notas_debitoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_notas_debito.html.twig');
	}
	public function financiero_pagoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_pago.html.twig');
	}
	public function financiero_anticipoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_anticipo.html.twig');
	}
	public function financiero_anticipo_clienteAction()
	{
		return $this->render('ayudaBundle:Default:financiero_anticipo_cliente.html.twig');
	}
	public function financiero_genera_depositoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_genera_deposito.html.twig');
	}
	public function financiero_procesar_depositoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_procesar_deposito.html.twig');
	}
	public function financiero_recaudacionAction()
	{
		return $this->render('ayudaBundle:Default:financiero_recaudacion.html.twig');
	}
	public function financiero_tarjetaAction()
	{
		return $this->render('ayudaBundle:Default:financiero_tarjeta.html.twig');
	}
	public function financiero_formato_debitoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_formato_debito.html.twig');
	}
	public function financiero_subir_respuestaAction()
	{
		return $this->render('ayudaBundle:Default:financiero_subir_respuesta.html.twig');
	}
	public function financiero_generar_debitoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_generar_debito.html.twig');
	}
	public function financiero_autorizacion_debitoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_autorizacion_debito.html.twig');
	}
	public function financiero_autorizacion_creditoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_autorizacion_credito.html.twig');
	}
	public function financiero_estadoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_estado.html.twig');
	}
	public function financiero_estado_puntoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_estado_punto.html.twig');
	}
	public function financiero_anticipo_cruzadoAction()
	{
		return $this->render('ayudaBundle:Default:financiero_anticipo_cruzado.html.twig');
	}
	public function financiero_carteraAction()
	{
		return $this->render('ayudaBundle:Default:financiero_cartera.html.twig');
	}
	public function financiero_cierreAction()
	{
		return $this->render('ayudaBundle:Default:financiero_cierre.html.twig');
	}
	public function financiero_documentosAction()
	{
		return $this->render('ayudaBundle:Default:financiero_documentos.html.twig');
	}

}
