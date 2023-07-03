/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){
				
			
                
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
			
			DTFechaCancelarHasta = new Ext.form.DateField({
                    id: 'fechaCancelarHasta',
                    fieldLabel: 'Cancelar hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    value: fecha_cancelar,
                    editable: false,
                    width:200,
            });
            
			Mensaje = new Ext.form.TextArea({
					id: 'message',
                    xtype     : 'textareafield',
					grow      : true,
					name      : 'message',
					fieldLabel: 'Mensaje',
					value: mensaje,
					width:800,
            });

            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 1,
                    align: 'left',
                    border:1,
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: false,
                width: 1100,
                title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Procesar',
                            //xtype: 'button',
                            iconCls: "icon_search",
                            handler: Buscar,
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function(){ limpiar();}
                        }
                        
                        ],                

                        items: [
                                DTFechaDesde,
                                DTFechaHasta,
                                {html:"&nbsp;",border:false,width:10},
                                {html:"Información del archivo del Courier:",border:false,width:200},
                                {html:"&nbsp;",border:false,width:10},
                                DTFechaCancelarHasta,
                                Mensaje
                                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
           
    });

    function Buscar()
    {
		var fechaDesde=Ext.getCmp('fechaDesde').getValue();
		var fechaHasta=Ext.getCmp('fechaHasta').getValue();
		var fechaCancelar=Ext.getCmp('fechaCancelarHasta').getValue();
		var message=Ext.getCmp('message').getValue();
		
		
		Ext.Msg.confirm('Alerta','Se generara el archivo para el Courier. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: direccion,
						method: 'post',
						params: { 
							fechaDesde : fechaDesde, 
							fechaHasta:fechaHasta,
							fechaCancelar:fechaCancelar,
							message:message
						},
						success: function(response){
							//alert(response);
							url = descarga+"?nombre="+response.responseText;
							//method: 'post',
							//params: { 
								//nombre_archivo : response.archivo_nombre, 
							//},
							window.location = url;
						},
						failure: function(result)
						{
							Ext.Msg.alert('Error ','Error: ' + result.statusText);
						}
					});
				}
			});
    }
    

    function eliminar(direccion)
    {
        //alert(direccion);
        Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: direccion,
                    method: 'post',
                    success: function(response){
                        var text = response.responseText;
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }

    function limpiar(){
        Ext.getCmp('fechaDesde').setRawValue("");
        Ext.getCmp('fechaHasta').setRawValue("");

    }


