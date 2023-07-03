/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function() {   
    Ext.tip.QuickTipManager.init();
    
	store = new Ext.data.Store({     
        extend: "Ext.data.Store",
		pageSize: 15,
		total: 'total',
		proxy: {
			type: 'ajax',
			url : 'grid',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'                
			},
			extraParams: {				
				estado: 'Todos'				
			}           
		},
		fields:
		[
			{name:'id', mapping:'id'},
            {name:'idComun', mapping:'idComun'},
			{name:'nombre', mapping:'nombre'},
			{name:'clase', mapping:'clase'},
			{name:'estado', mapping:'estado'},
			{name:'observacion', mapping:'observacion'},
			{name:'enviados', mapping:'enviados'},
			{name:'noEnviados', mapping:'noEnviados'},
            {name:'tipo', mapping:'tipo'},
			{name:'feCreacion', mapping:'feCreacion'},
            {name:'usrCreacion', mapping:'usrCreacion'},
            {name:'feFinaliza', mapping:'feFinaliza'},
            {name:'isOcupado', mapping:'isOcupado'}
		],
		autoLoad: true
	});    
    
    store.load({
        callback : function(records, operation, success) 
        {            
            if(success === false)
                Ext.Msg.alert('Error','Ha ocurrido un problema, por favor notificar a Sistemas');                      
        }
    });
                        										
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1240,
        height: 400,
        store: store,
        loadMask: true,
	    renderTo:grid,
        frame: false,
		viewConfig: { enableTextSelection: true },
        selModel: sm,
        dockedItems: 
		[ 
			{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: 
				[                    
                    { xtype: 'tbfill' },                   
                    {
                    }
                ]
			}
        ],                  
        columns:[
			{
			      id: 'id',
			      header: 'id',
			      dataIndex: 'id',
			      hidden: true,
			      hideable: false
			},
            {
			      id: 'idComun',
			      header: 'idComun',
			      dataIndex: 'idComun',
			      hidden: true,
			      hideable: false
			},
			{
			      id: 'nombre',
			      header: 'Nombre Documento',
			      dataIndex: 'nombre',
			      width: 200,
			      sortable: true			
			},
			{
			      id: 'tipo',
			      header: 'Tipo Envio',
			      dataIndex: 'tipo',
			      width: 110,
			      sortable: true			  
			},
			{
			      id: 'observacion',
			      header: 'Observacion',
			      dataIndex: 'observacion',
			      width: 310,
			      sortable: true			  
			},
			{
			      id: 'enviados',
			      header: 'Enviados',
			      dataIndex: 'enviados',
			      width: 70,
                  align: 'center'			      
			},
		        {
			      id: 'noEnviados',
			      header: 'No Enviados',
			      dataIndex: 'noEnviados',
			      width: 80,
                  align: 'center'			      
			},
            {
			      id: 'procesando',
			      header: '',
			      dataIndex: 'procesando',
			      width: 20,
                  renderer: renderAccionEjecutando			      
			},
		    {
			      id: 'estado',
			      header: 'Estado',
			      dataIndex: 'estado',
			      width: 90,
			      sortable: true
			},	
            {
			      id: 'usrCreacion',
			      header: 'Usuario Creacion',
			      dataIndex: 'usrCreacion',
			      width: 100,
			      sortable: true
			},
            {
			      id: 'feCreacion',
			      header: 'Fecha Ejecucion',
			      dataIndex: 'feCreacion',
			      width: 120,
			      sortable: true
			},
            {
			      id: 'feFinaliza',
			      header: 'Fecha Finalizacion',
			      dataIndex: 'feFinaliza',
			      width: 120,
			      sortable: true
			},	
			{
			      xtype: 'actioncolumn',
			      header: 'Acciones',
			      width: 90,
			      items: 
			      [
                      {
					    getClass: function(v, meta, rec) 
					    {		
                            
                            if( rec.get('clase') === 'SMS')
                            {
                                if(rec.get('isOcupado') === 'N')
                                {
                                    if (rec.get('estado') !== 'Enviado') 
                                    {                                
                                        return 'button-grid-invisible';
                                    }
                                    else 
                                    {                                
                                        return 'button-grid-cambioVelocidad';
                                    }
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }   
                            }
                            else if(rec.get('clase') === 'Correo' && rec.get('estado')==='Enviado')
                            {
                                return 'button-grid-cambioVelocidad';
                            }else return 'button-grid-invisible';
					    },
					    tooltip: 'Reenviar Notificacion',
					    handler: function(grid, rowIndex, colIndex) 
					    {						    																  
						    if(grid.getStore().getAt(rowIndex).data.estado==='Enviado')
                            {							    
                                //Reenviar envio, lo pone en estado Pendiente y es ejecutado por script de pendientes/programados
                                reenviarNotificacion(grid.getStore().getAt(rowIndex).data.id);
                            }						    
										    
					    }                                                                                                
				      },                      
                      
                       {
					    getClass: function(v, meta, rec) 
					    {			
                            if( rec.get('clase') === 'SMS')
                            {
                                if(rec.get('isOcupado') === 'N')
                                {
                                    if (rec.get('estado') !== 'Pendiente' && rec.get('estado') !== 'Programado') 
                                    {                                
                                        return 'button-grid-invisible';
                                    }
                                    else 
                                    {                                
                                        return 'button-grid-cambioCpe';
                                    }
                                }else
                                {
                                    return 'button-grid-invisible';
                                }
                            }else if(rec.get('clase') === 'Correo' && rec.get('estado')==='Pendiente')
                            {
                                return 'button-grid-cambioCpe';
                            }else return 'button-grid-invisible';
					    },
					    tooltip: 'Ejecutar Inmediato',
					    handler: function(grid, rowIndex, colIndex) 
					    {						    																  
						    if(grid.getStore().getAt(rowIndex).data.estado==='Pendiente' || 
                                grid.getStore().getAt(rowIndex).data.estado==='Programado')                                
                            {							    
                                //Ejecucion de envio rapido cuando el equipo se encuentre desocuapdo
                                reenviarNotificacion(grid.getStore().getAt(rowIndex).data.id);
                            }
                            else{
                                Ext.Msg.alert('Mensaje','No Puede ejecutar esta opcion Equipo Ocupado');                              
                    
                            }
										    
					    }                                                                                                
				      },
                      
                       {
					    getClass: function(v, meta, rec) 
					    {									    						    
                            if (rec.get('estado') === 'Enviado' || rec.get('estado') === 'Cancelado' || 
                                rec.get('estado') === 'Enviando' || rec.get('estado') === 'Reenvio'
                                || rec.get('estado') === 'Conectando' || rec.get('estado') === 'Sin Envio') 
                            {                                
                                return 'button-grid-invisible';
                            }
						    else 
                            {                                
                                return 'button-grid-cancelarCliente';
                            }
					    },
					    tooltip: 'Cancelar envios Programados/Pendientes',
					    handler: function(grid, rowIndex, colIndex) 
					    {						    
																	    
						    if(grid.getStore().getAt(rowIndex).data.estado!=='Enviado' || grid.getStore().getAt(rowIndex).data.estado!=='Enviando')
                            {							    
                                //Cambiar de estado Cancelado a envios Pendientes/Programados
                                cancelarEnviosPendientes(grid.getStore().getAt(rowIndex).data.id);
                            }						    
										    
					    }                                                                                                
				      }	 ,
				      {
					    getClass: function(v, meta, rec) 
					    {									    
						    if (rec.get('noEnviados') === 0) 
                            {                                
                                return 'button-grid-invisible';
                            }
						    else 
                            {                                
                                return 'button-grid-excel';
                            }
					    },
					    tooltip: 'Exportar No Enviados',
					    handler: function(grid, rowIndex, colIndex) 
					    {						    
																	    
						    if(grid.getStore().getAt(rowIndex).data.noEnviados!==0)
                            {							    
                                exportarNoEnviados(grid.getStore().getAt(rowIndex).data.idComun);
                            }						    
										    
					    }                                                                                                
				      },
                      
                      /*Ver logs de ejecucion*/
                        {
					    getClass: function(v, meta, rec) 
					    {			                            
                            if(verLogEnvio!=='')
                            {
                                if (rec.get('estado') !== 'Enviando' && rec.get('estado') !== 'Conectando') 
                                {                                
                                    return 'button-grid-invisible';
                                }
                                else 
                                {                                
                                    return 'button-grid-logs';
                                }
                            }
                            else{
                                return 'button-grid-invisible';
                            }
					    },
					    tooltip: 'Ver Log de ejecucion',
					    handler: function(grid, rowIndex, colIndex) 
					    {						    
																	    
						    if(grid.getStore().getAt(rowIndex).data.estado === 'Enviando' ||
                               grid.getStore().getAt(rowIndex).data.estado === 'Conectando')
                            {							    
                                consultarLogEnvio();
                            }						    
										    
					    }                                                                                                
				      }	,
                      //Cambiar Estados Enviando/Conectando
                        {
                            getClass: function(v, meta, rec)
                            {
                                if (verLogEnvio !== '')
                                {
                                    if (rec.get('estado') !== 'Enviando' && rec.get('estado') !== 'Conectando')
                                    {
                                        return 'button-grid-invisible';
                                    }
                                    else
                                    {
                                        return 'button-grid-cambioEstadoEnvio';
                                    }
                                }
                                else {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Cambiar Estado',
                            handler: function(grid, rowIndex, colIndex)
                            {

                                if (grid.getStore().getAt(rowIndex).data.estado === 'Enviando' ||
                                    grid.getStore().getAt(rowIndex).data.estado === 'Conectando')
                                {
                                    cambiarEstado(grid.getStore().getAt(rowIndex).data.id);
                                }

                            }
                        },
                        //Desocupar Equipo
                         {
                            getClass: function(v, meta, rec)
                            {
                                if (verLogEnvio !== '' && rec.get('isOcupado') === 'S' && rec.get('clase') === 'SMS' &&
                                   ( rec.get('estado') === 'Pendiente' || rec.get('estado') === 'Cancelado' ))
                                {                                    
                                    return 'button-grid-confirmarActivacion';
                                }
                                else 
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Desocupar Equipo',
                            handler: function(grid, rowIndex, colIndex)
                            {
                                  desocuparEquipo();
                            }
                        }	
				]
			}
		],
		bbar: Ext.create('Ext.PagingToolbar', {
		    store: store,
		    displayInfo: true,
		    displayMsg: 'Mostrando {0} - {1} de {2}',
		    emptyMsg: "No hay datos que mostrar."
		}),
		listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj)
            {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function(grid)
            {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners: {
                            beforeshow: function updateTipBody(tip) {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });

            }
        }
    });        
          
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders        
        border:false,        
        buttonAlign: 'center',
        layout: {
            type:'table',
            columns: 5
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: false,
        width: 1200,
        title: 'Criterios de busqueda',
        buttons: 
		[
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function(){ buscar();}
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function(){ limpiar();}
            }

		],                
		items: 
		[
			{html:"&nbsp;",border:false,width:50},		
			{
				xtype: 'textfield',
				id: 'txt_nombre',
				name: 'txt_nombre',
				fieldLabel: 'Nombre',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'combobox',
				fieldLabel: 'Clase Comunicacion',
				id: 'cmb_clase',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['Correo','Correo'],
					['SMS','SMS']					
				],
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
												
			//----------------------------------//
								
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado',
				id: 'cmb_estado',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['Enviado','Enviado'],					
					['Pendiente','Pendiente'],
					['Programado','Programado'],
                    ['Enviando','Enviando'],
                    ['Reenvio','Reenvio']
				],
				width: 400
			},
            {html:"&nbsp;",border:false,width:80},
			{
				xtype: 'combobox',
				fieldLabel: 'Tipo Envio',
				id: 'cmb_tipo',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['MASIVO','MASIVO'],
					['PERSONALIZADO','PERSONALIZADO']					
				],
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			//----------------------------------//					
		],	
        renderTo: 'filtro'
    }); 
        
    setInterval('ejecutarCadaTiempo()',300000);
    
});

function buscar()
{                                 				
    store.proxy.extraParams = 
    {
                nombre: Ext.getCmp('txt_nombre').value ? Ext.getCmp('txt_nombre').value : '',
                clase : Ext.getCmp('cmb_clase').value ? Ext.getCmp('cmb_clase').value : '',
                estado: Ext.getCmp('cmb_estado').value ? Ext.getCmp('cmb_estado').value : '',                           
                tipo  : Ext.getCmp('cmb_tipo').value ? Ext.getCmp('cmb_tipo').value : ''
    };	
    store.load({
        callback : function(records, operation, success) 
        {            
            if(success === false)
                Ext.Msg.alert('Error','Ha ocurrido un problema, por favor notificar a Sistemas');                      
        }
    });
}

function limpiar()
{
  
    Ext.getCmp('txt_nombre').value = "";
    Ext.getCmp('txt_nombre').setRawValue("");  
    Ext.getCmp('cmb_clase').value= "";
    Ext.getCmp('cmb_clase').setRawValue("Todos");
    Ext.getCmp('cmb_estado').value= "Todos";
    Ext.getCmp('cmb_estado').setRawValue("Todos");
    Ext.getCmp('cmb_tipo').value= "Todos";
    Ext.getCmp('cmb_tipo').setRawValue("Todos");			
            
    store.proxy.extraParams = { estado: 'Todos'};
    store.load();
}

function ejecutarCadaTiempo()
{
    store.load({params: {start: 0, limit: 10}});
}
      
function renderAccionEjecutando(value, p, record) 
{
    var iconos='';
    if(record.data.estado==='Enviando' || record.data.estado==='Conectando' )
    {
        iconos=iconos+iconoEjecutando;                    
    }
    return Ext.String.format(iconos,value);
}
       
function exportarNoEnviados(idEnvio)
{             		
		      
      $('#hid').val(idEnvio);      
      document.forms[0].submit();		
}

function cancelarEnviosPendientes(id)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Ejecutando Accion...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    conn.request({
            method: 'POST',
            params :{
                idComun: id                
            },
            url: 'cancelar',
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                
                if(json.success === true)
                {                    
                   
                    Ext.Msg.alert('Mensaje',json.mensaje, function(btn){
                        if(btn==='ok')
                        {                            
                                store.proxy.extraParams = { estado: 'Todos'};
                                store.load();
                        }
                    });
                }
                else Ext.Msg.alert('Error',json.mensaje);
            }
    });
}

function reenviarNotificacion(id)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Ejecutando Accion...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    conn.request({
            method: 'POST',
            params :{
                idComun: id                
            },
            url: 'reenviar',
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                
                if(json.success === true)
                {                    
                   
                    Ext.Msg.alert('Mensaje',json.mensaje, function(btn){
                        if(btn==='ok')
                        {                            
                                store.proxy.extraParams = { estado: 'Todos'};
                                store.load();
                        }
                    });
                }
                else Ext.Msg.alert('Error',json.mensaje);
            }
    });
}

function cargarLogPorTipo(tipo)
{
    Ext.MessageBox.wait("Consultando Log de ejecucion...");
    
	Ext.Ajax.request({
		    url: 'consultar_log',
		    method: 'post',
            params :{
                tipo: tipo               
            },
		    success: function(response)
            {                
                Ext.MessageBox.hide();
                var text = response.responseText;                            

                Ext.MessageBox.show({
                  title: 'Log de Ejecucion',
                  msg: text,                  
                  buttons: Ext.MessageBox.OK,
                  icon: Ext.MessageBox.INFO                    
                });                            	           
		    },
		    failure: function(result)
		    {
                Ext.MessageBox.hide();
                Ext.MessageBox.show({
                  title: 'Error',
                  msg: result.statusText,
                  buttons: Ext.MessageBox.OK,
                  icon: Ext.MessageBox.ERROR
                });
		    }
	    });
}

function consultarLogEnvio()
{
    winTipoLogs="";
    formPanelTipoLogs= "";
    
    if (!winTipoLogs)
    {              
         
        espacioVacio =  Ext.create('Ext.Component', {
            html: '',
            width: 500,
            padding: 5,
            layout: 'anchor',
            style: { color: '#000000' }
        });  
                          
        var iniHtml=   'Log de Ejecucion: '+
			'&nbsp;&nbsp;&nbsp;&nbsp;'+
			'<input type="radio" checked="" value="inmediato" name="log" >&nbsp;Inmediata' + 
            '&nbsp;&nbsp;'+
            '<input type="radio" value="pendiente" name="log" >&nbsp;Pendiente/Programado' +
            '&nbsp;&nbsp;'+
            '<input type="radio" value="sms" name="log" >&nbsp;SMS';
        
        RadiosTipoLog =  Ext.create('Ext.Component', {
            html: iniHtml,
            width: 600,
            padding: 5,
            style: { color: '#000000' }
        });		       	
                        
        formPanelTipoLogs = Ext.create('Ext.form.Panel', {
            width:700,
            height:200,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [espacioVacio, espacioVacio, RadiosTipoLog, espacioVacio],
            buttons:[
                {
                    text: 'Ver Log',
                    handler: function(){                                                
                        cargarLogPorTipo($("input[name='log']:checked").val());                        
                        winTipoLogs.close();
                        winTipoLogs.destroy();
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function(){  
                        winTipoLogs.close();
                        winTipoLogs.destroy();
                    }
                }
            ]
        });                     
               
	winTipoLogs = Ext.widget('window', {
            title: 'Seleccion de log a consultar',
            width: 500,
            height:130,            
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelTipoLogs]
        });
    }
    
    winTipoLogs.show();    
}

function cambiarEstadoAjax(id,estado)
{
    Ext.MessageBox.wait("Cambiando Estado de Envio...");
    
	Ext.Ajax.request({
		    url: 'cambiar_estado',
		    method: 'post',
            params :{
                estado: estado, 
                id  : id
            },
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                
                if(json.success === true)
                {                    
                   
                    Ext.Msg.alert('Mensaje',json.mensaje, function(btn){
                        if(btn==='ok')
                        {                            
                                store.proxy.extraParams = { estado: 'Todos'};
                                store.load();
                        }
                    });
                }
                else Ext.Msg.alert('Error',json.mensaje);
            }		    
	    });
}

function cambiarEstado(id)
{
    winCambioEstado="";
    formPanelCambioEstado= "";
    
    if (!winCambioEstado)
    {              
         
        espacioVacio =  Ext.create('Ext.Component', {
            html: '',
            width: 500,
            padding: 5,
            layout: 'anchor',
            style: { color: '#000000' }
        });  
                          
        var iniHtml=   'Estados a Cambiar: '+
			'&nbsp;&nbsp;&nbsp;&nbsp;'+
			'<input type="radio" checked="" value="Pendiente" name="cancelar" >&nbsp;Pendiente' + 
            '&nbsp;&nbsp;'+
            '<input type="radio" value="Cancelado" name="cancelar" >&nbsp;Cancelado';
        
        RadiosTipoLog =  Ext.create('Ext.Component', {
            html: iniHtml,
            width: 600,
            padding: 5,
            style: { color: '#000000' }
        });		       	
                        
        formPanelCambioEstado = Ext.create('Ext.form.Panel', {
            width:700,
            height:200,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [espacioVacio, espacioVacio, RadiosTipoLog, espacioVacio],
            buttons:[
                {
                    text: 'Cancelar Envio',
                    handler: function(){                                                
                        cambiarEstadoAjax(id,$("input[name='cancelar']:checked").val());                        
                        winCambioEstado.close();
                        winCambioEstado.destroy();
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function(){  
                        winCambioEstado.close();
                        winCambioEstado.destroy();
                    }
                }
            ]
        });                     
               
	winCambioEstado = Ext.widget('window', {
            title: 'Seleccion de Estados',
            width: 400,
            height:130,            
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelCambioEstado]
        });
    }
    
    winCambioEstado.show();    
}

function desocuparEquipo()
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Desconectando Equipo...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    conn.request({
            method: 'POST',           
            url: 'desconectar_equipo',
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                
                if(json.success === true)
                {                    
                   
                    Ext.Msg.alert('Mensaje',json.mensaje, function(btn){
                        if(btn==='ok')
                        {                            
                                store.proxy.extraParams = { estado: 'Todos'};
                                store.load();
                        }
                    });
                }
                else Ext.Msg.alert('Error',json.mensaje);
            }
    });
}