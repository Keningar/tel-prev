/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function() {   
    
    if ((strPrefijoEmpresaSession === 'TN') && boolPuedeVerGridActividades) {
        storeActividadesPuntoAfectado = new Ext.data.Store({
            pageSize: 4,
            autoLoad: true,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: url_getActividadesPuntoAfectado,
                timeout: 60000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'actividades'
                }
            },
            fields:
                [
                    {name: 'idActividad', mapping: 'idActividad'},
                    {name: 'titulo', mapping: 'titulo'},
                    {name: 'motivo', mapping: 'motivo'},
                    {name: 'responsable', mapping: 'responsable'},
                    {name: 'areaResponsable', mapping: 'areaResponsable'},
                    {name: 'contacto', mapping: 'contacto'},
                    {name: 'notificado', mapping: 'notificado'},
                    {name: 'fechaNotificacion', mapping: 'fechaNotificacion'},
                    {name: 'asuntoNotificacion', mapping: 'asuntoNotificacion'},
                    {name: 'tipoAfectacion', mapping: 'tipoAfectacion'},
                    {name: 'serviciosAfectados', mapping: 'serviciosAfectados'},
                    {name: 'origen', mapping: 'origen'},
                    {name: 'fechaInicio', mapping: 'fechaInicio'},
                    {name: 'fechaFin', mapping: 'fechaFin'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'idCasoTarea', mapping: 'idCasoTarea'},
                    {name: 'tipoCasoTarea', mapping: 'tipoCasoTarea'}
                ]
        });
    
        gridActividadesPuntoAfectado = Ext.create('Ext.grid.Panel', {
            store: storeActividadesPuntoAfectado,
            collapsible: true,
            collapsed: false,
            width: 1000,
            height: 175,
            viewConfig: { enableTextSelection: true },
            title: 'Trabajos realizados recientemente', 
            columnLines: true,  
            columns: [
                {
                    header: 'Origen',
                    dataIndex: 'origen',
                    width: 90
                },
                {
				    header: 'idCasoTarea',
				    dataIndex: 'idCasoTarea',
				    hidden: true,
				    hideable: false
                },
                {
				    header: 'tipoCasoTarea',
				    dataIndex: 'tipoCasoTarea',
				    hidden: true,
				    hideable: false
                },
                {
                    header: 'Id Actividad',
                    dataIndex: 'idActividad',
                    width: 100,
                    renderer : function(value, p,record){
                        var idCasoTarea = record.data.idCasoTarea;
                        var idActividad = record.data.idActividad;
                        var tipoCasoTarea = record.data.tipoCasoTarea;
                        return '<a href="#" onclick="verInformacionActividad(\''+idCasoTarea+'\',\''+tipoCasoTarea+'\');">'+idActividad+'</a>';
                    } 
                },
                {
                    header: 'Titulo',
                    dataIndex: 'titulo',
                    width: 150
                },
                {
                    header: 'Motivo',
                    dataIndex: 'motivo',
                    width: 205
                },
                {
                    header: 'Fecha Inicio',
                    dataIndex: 'fechaInicio',
                    width: 120
                },
                {
                    header: 'Fecha Fin',
                    dataIndex: 'fechaFin',
                    width: 120
                },
                {
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 70
                },
                {
                    header: 'Responsable',
                    dataIndex: 'responsable',
                    width: 200
                },
                {
                    header: 'Area Respon.',
                    dataIndex: 'areaResponsable',
                    width: 90
                },
                {
                    header: 'Tipo Afectacion',
                    dataIndex: 'tipoAfectacion',
                    width: 100
                },
                {
                    header: 'Servicios',
                    dataIndex: 'serviciosAfectados',
                    width: 90
                },
                {
                    header: 'Contacto',
                    dataIndex: 'contacto',
                    width: 230
                },
                {
                    header: 'Notificado',
                    dataIndex: 'notificado',
                    width: 60
                },
                {
                    header: 'Fecha Notificacion',
                    dataIndex: 'fechaNotificacion',
                    width: 120
                },
                {
                    header: 'Asunto Notificacion',
                    dataIndex: 'asuntoNotificacion',
                    width: 300
                }],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeActividadesPuntoAfectado,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'actividadesPuntoAfectado'
        });
    }
    
	store = new Ext.data.Store({ 
		pageSize: 10,
		total: 'total',
		proxy: {
			type: 'ajax',
			url : 'grid',
            timeout: 500000,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				nombre: '',
				estado: 'Todos'
			}
		},
		fields:
		[
			{name:'id_comunicacion', mapping:'id_comunicacion'},
			{name:'cerrarTarea', mapping:'cerrarTarea'},
			{name:'cliente', mapping:'cliente'},
			{name:'claseDocumento', mapping:'claseDocumento'},
			{name:'origenGenera', mapping:'origenGenera'},
			{name:'idCaso', mapping:'idCaso'},
			{name:'numCaso', mapping:'numCaso'},
			{name:'idDetalle', mapping:'idDetalle'},			
                       {name:'idTarea', mapping:'idTarea'},
                       {name:'nombreTarea', mapping:'nombreTarea'},
                       {name:'fechaActual', mapping:'fechaActual'},
                       {name:'horaActual', mapping:'horaActual'},
                       {name:'fechaEjecucion', mapping:'fechaEjecucion'},
                       {name:'horaEjecucion', mapping:'horaEjecucion'},
			{name:'descripcion', mapping:'descripcion'},
			{name:'fecha', mapping:'fecha'},
			{name:'hora', mapping:'hora'},
			{name:'clase', mapping:'clase'},
			{name:'departamentoAsignado', mapping:'departamentoAsignado'},
			{name:'nombreAsignada', mapping:'nombreAsignada'},
			{name:'esTarea', mapping:'esTarea'},
			{name:'estado', mapping:'estado'},
			{name:'estadoTarea', mapping:'estadoTarea'},
			{name:'estadoCaso', mapping:'estadoCaso'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'},
			{name:'action4', mapping:'action4'},
			{name:'action5', mapping:'action5'},
                       {name:'action6', mapping:'action6'}
		],
        
	});
											 
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', 
    {
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view)
        {
            if( record.get('idCaso') != '' )
            {
                if (record.get('estadoCaso') != 'Cerrado')
                {
                    return '<div class="'+Ext.baseCSSPrefix+'grid-row-checker">&#160;</div>';
                }
            }
            else
            {
                if ( record.get('estadoTarea') != 'Finalizada' && record.get('estadoTarea') != 'Cancelada'
                     && record.get('estadoTarea') != 'Rechazada' )
                {
                    return '<div class="'+Ext.baseCSSPrefix+'grid-row-checker">&#160;</div>';
                }
            }
        },
        beforeselect: function(grid, record, index, eOpts) 
        {
            if( record.get('idCaso') != '' )
            {
                if (record.get('estadoCaso') == 'Cerrado')
                {
                    return false;
                }
            }
            else
            {
                if ( record.get('estadoTarea') == 'Finalizada' || record.get('estadoTarea') == 'Cancelada' 
                     || record.get('estadoTarea') == 'Rechazada' )
                {
                    return false;
                }
            }
        },
        listeners: 
        {
            selectionchange: function(sm, selections)
            {
                grid.down('#delete').setDisabled(selections.length == 0);
            }
        }
    });

    
    grid = Ext.create('Ext.grid.Panel',
    {
        width: 1000,
        height: 800,
        store: store,
        loadMask: true,
        frame: false,
        plugins: 
        [
            {ptype : 'pagingselectpersist'}
        ],
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
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
                        iconCls: 'icon_exportar',
                        text: 'Exportar',
                        scope: this,
                        handler: function(){ exportarExcel();}
                    }
                ]
            }
        ],                  
        columns:
		[
			{
			  id: 'id_comunicacion',
			  header: 'No',
			  dataIndex: 'id_comunicacion',
			  hideable: false,
			  width: 60
			},
			{
			  id: 'llamada',
			  header: 'Detalle',
			  xtype: 'templatecolumn', 
			  width: 370,
			  tpl: '<span class="bold">Cliente:</span></br>\n\
					<span class="box-detalle">{cliente}</span>\n\
					<span class="bold">Clase:</span> \n\
					<span>{claseDocumento}</span></br>\n\
					<span class="bold">Genera:</span>\n\
					<span>{origenGenera}</span></br>\n\
					<span class="bold">Descripcion:</span></br>\n\
					<span>{descripcion}</span></br></br>'
			},
            {
                header: 'Fecha',
                xtype: 'templatecolumn', 
                align: 'center',
                tpl: '<span class="center">{fecha}</br>{hora}</span>',
                width: 150
            },
			{
			  id: 'numero',
			  header: 'Detalle',
			  xtype: 'templatecolumn', 
			  width: 250,
			  tpl: '<tpl if="origenGenera==\'Caso\'">\n\
						  <span class="bold">Numero Caso:</span>\n\
						  <span><a href="#" onClick="window.open(\'../../../soporte/info_caso/{idCaso}/show\');" />{numCaso}</a></span></br>\n\
						  <span class="bold">Estado Caso:</span>\n\
						  <span>{estadoCaso}</span></br></br>\n\
					</tpl>\n\
					<tpl if="origenGenera==\'Tarea\'">\n\
						  <span class="bold">Nombre Tarea:</span></br>\n\
						  <span >{nombreTarea}</span></br>\n\
						  <span class="bold">Departamento Asignado:</span></br>\n\
						  <span >{departamentoAsignado}</span></br>\n\
						  <span class="bold">Empleado Asignado:</span></br>\n\
						  <span >{nombreAsignada}</span></br>\n\
						  <span class="bold">Estado Tarea:</span></br>\n\
						  <span>{estadoTarea}</span></br></br>\n\
					</tpl>\n\
					<tpl if="origenGenera==\'Ninguno\'">\n\
						  <span>-</span></br>\n\
					</tpl>'				
			},
// 			{
// 			  header: 'Estado',
// 			  dataIndex: 'estado',
// 			  width: 70,
// 			  sortable: true
// 			},
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 120,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) {return rec.get('action1')},
                        tooltip: 'Ver Llamada',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec         = store.getAt(rowIndex);
                            window.location = rec.get('id_comunicacion')+"/show";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if (rec.get('action4') == "icon-invisible") 
                                    this.items[1].tooltip = '';
                            else 
                                    this.items[1].tooltip = 'Agregar Seguimiento';

                            return rec.get('action4');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action4')!="icon-invisible")
                                    agregarSeguimiento(rec.data.id_caso,'',rec.data);
                        }
                    },	
					{
						getClass: function(v, meta, rec) {
							if (rec.get('action5') == "icon-invisible") 
								this.items[2].tooltip = '';
							else 
								this.items[2].tooltip = 'Ver Seguimiento';
							
							return rec.get('action5');
						},
						handler: function(grid, rowIndex, colIndex) {
							var rec = store.getAt(rowIndex);
							if(rec.get('action5')!="icon-invisible")
								verSeguimientoTarea(rec.data);
							}
					},
					{
						getClass: function(v, meta, rec) {
							if (rec.get('action6') == "icon-invisible")
								this.items[3].tooltip = '';
							else
								this.items[3].tooltip = 'Finalizar Tarea';

							return rec.get('action6');
						},
						handler: function(grid, rowIndex, colIndex) {
							var rec = store.getAt(rowIndex);
							if(rec.get('action6')!="icon-invisible")
								finalizarTarea(rec.data);
						}
					}
				]
			}
		],
		bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });        
	
    storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEmpleados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
              [
                {name:'id_empleado', mapping:'id_empleado'},
                {name:'nombre_empleado', mapping:'nombre_empleado'}
              ]
    });
    
    comboEmpleados = Ext.create('Ext.form.ComboBox', {
        id:'comboEmpleados',
        name:'comboEmpleados',
        fieldLabel: 'Usuario Asignado:',
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        height:30,
	width: 400,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
    
    
	
	comboClaseDocumentoStore_index = new Ext.data.Store({ 
		total: 'total',
		proxy: {
			type: 'ajax',
			url: '../../administracion/comunicacion/admi_clase_documento/grid',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				nombre: '',
				estado: 'Activo',
				visible:'SI'
			}
		},
		fields:
		[
			{name:'id_clase_documento', mapping:'id_clase_documento'},
			{name:'nombre_clase_documento', mapping:'nombre_clase_documento'}
		]
	});
		
	 // Create the combo box, attached to the states data store
	comboClaseDocumento_index = Ext.create('Ext.form.ComboBox', {
		id:'comboClaseDocumento_index',
		name:'comboClaseDocumento_index',
		store: comboClaseDocumentoStore_index,
		displayField: 'nombre_clase_documento',
		valueField: 'id_clase_documento',
		width: 400,
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Clase:',	
		queryMode: "remote",
		emptyText: ''
	});
	
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
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
        width: 1000,
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
				id: 'txtLogin',
				name: 'txtLogin',
				fieldLabel: 'Cliente Login',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
			comboClaseDocumento_index,
			{html:"&nbsp;",border:false,width:50},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'combobox',
				fieldLabel: 'Genera',
				id: 'sltGenera',
				value:'N',
				store: [
					['N','Ninguno'],
					['C','Caso'],
					['T','Tarea']
				],
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
            comboEmpleados,
			{html:"&nbsp;",border:false,width:50},
			
		
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'fieldcontainer',
				fieldLabel: 'Fecha Comunicacion',				
				items: [
					{
						xtype: 'datefield',
						width: 290,
						id: 'feDesde',
						name: 'feDesde',
						fieldLabel: 'Desde:',
						format: 'Y-m-d',
						editable: false
					},
					{
						xtype: 'datefield',
						width: 290,
						id: 'feHasta',
						name: 'feHasta',
						fieldLabel: 'Hasta:',
						format: 'Y-m-d',
						editable: false
					}
				]
			},
			{html:"&nbsp;",border:false,width:80},			
            {
				xtype: 'numberfield',
				id: 'txtActividad',
				hideTrigger: true,
				name: 'txtActividad',
				fieldLabel: 'Numero Tarea',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			{html:"&nbsp;",border:false,width:80},
		],	
        renderTo: 'filtro'
    }); 
    
});

function buscar()
{   
    login   = Ext.getCmp('txtLogin').value;
    numero  = Ext.getCmp('txtActividad').value;
    feDesde = Ext.getCmp('feDesde').value;
    feHasta = Ext.getCmp('feHasta').value;
        
	if (isNaN(comboClaseDocumento_index.getValue()))
        comboClaseDocumento_index.setValue('');        
    
    //Si el cliente se encuentra en sesion la busqueda de las actividades permanece sin cambio
    //caso contrario se valida que se escoja fechas de apertura siempre y cuando no pongan 
    //los filtros determinados como importantes login, fecha o numero de tarea/actividad
    if(!clienteSesion)
    {
        if(login === "" && (numero === null || numero === "") && ( feDesde === "" || feHasta === "" || isNaN(feDesde) || isNaN(feHasta)))
        {        
            if( (feDesde !== "" && !isNaN(feDesde)) && (feHasta === "" || isNaN(feHasta)))
            {
                 Ext.Msg.alert('Alerta','Debe escoger la fecha final de busqueda');
                 return;
            }
            if( (feHasta !== "" && !isNaN(feHasta)) && (feDesde === "" || isNaN(feDesde)))
            {
                 Ext.Msg.alert('Alerta','Debe escoger la fecha Inicial de busqueda');
                 return;
            }
            else            
            {
               Ext.Msg.alert('Alerta','Debe escoger al menos un login, numero de tarea o Fecha de Comunicacion');
               return;
            }
        }
        else
        {
            if((feDesde !== "" && !isNaN(feDesde)) && (feHasta !== "" && !isNaN(feHasta)))
            {
                if(getDiferenciaTiempo(feDesde , feHasta ) > 31)
                {
                    Ext.Msg.alert('Alerta ', "Consulta permitida con un maximo de 30 dias");
                    return;
                }
            }
        }
    }

    store.proxy.extraParams = {
        login           : login,
        tipo_genera     : Ext.getCmp('sltGenera').value,
        idClaseDocumento: (!isNaN(comboClaseDocumento_index.getValue()) ? comboClaseDocumento_index.getValue() : ''),        
        feDesde         : feDesde,
        feHasta         : feHasta,
        numeroActividad : numero,
        asignado        : Ext.getCmp('comboEmpleados').value ? Ext.getCmp('comboEmpleados').value : ''
    };
    
    store.load();
}

function exportarExcel(){
    
    login       = Ext.getCmp('txtLogin').value;    
    tipoGenera  = Ext.getCmp('sltGenera').value;
    feDesde     = Ext.getCmp('feDesde').value;
    feHasta     = Ext.getCmp('feHasta').value;
	
	$('#hid_login').val(login ? login : '');
	$('#hid_tipoGenera').val(tipoGenera ? tipoGenera : '');
	$('#hid_claseDocumento').val( (!isNaN(comboClaseDocumento_index.getValue()) ? comboClaseDocumento_index.getValue() : '') );	
	$('#hid_feDesde').val(feDesde ? feDesde : '');
	$('#hid_feHasta').val(feHasta ? feHasta : '');
	$('#hid_asignado').val((!isNaN(Ext.getCmp('comboEmpleados').value) ? Ext.getCmp('comboEmpleados').value : ''));	
	$('#hid_actividad').val((!isNaN(Ext.getCmp('txtActividad').value) ? Ext.getCmp('txtActividad').value : ''));	
         
	document.forms[0].submit();
}

function limpiar(){
  
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");
    Ext.getCmp('sltGenera').value="Ninguno";
    Ext.getCmp('sltGenera').setRawValue("Ninguno"); 
    Ext.getCmp('comboClaseDocumento_index').value="";
    Ext.getCmp('comboClaseDocumento_index').setRawValue("");	
    Ext.getCmp('feHasta').value="";
    Ext.getCmp('feHasta').setRawValue("");
    Ext.getCmp('feDesde').value="";
    Ext.getCmp('feDesde').setRawValue("");
    Ext.getCmp('comboEmpleados').value="";
    Ext.getCmp('comboEmpleados').setRawValue("");    
    Ext.getCmp('txtActividad').value="";
    Ext.getCmp('txtActividad').setRawValue("");
			
    store.proxy.extraParams = {
        login: '',
        tipo_genera: 'N',
        idClaseDocumento: '',        
        estado: 'Todos',
        feDesde: '',
        feHasta: '',
        asignado: ''
    };
    
    grid.getStore().removeAll();  
}

function eliminarAlgunos()
{
    var param = '';

    if(sm.getSelection().length > 0)
    {
        var estado = 0;
        
        for(var i=0 ;  i < sm.getSelection().length ; ++i)
        {
            var boolError = false;
            
            if( sm.getSelection()[i].data.idCaso != '' )
            {
                if( sm.getSelection()[i].data.estadoCaso == 'Cerrado' )
                {
                    boolError = true;
                }
            }
            else
            {
                if ( sm.getSelection()[i].data.estadoTarea == 'Finalizada' || sm.getSelection()[i].data.estadoTarea == 'Cancelada'
                     || sm.getSelection()[i].data.estadoTarea == 'Rechazada' )
                {
                    boolError = true;
                }
            }

            if( boolError )
            {
                estado = estado + 1;
            }
            else
            {
                param = param + sm.getSelection()[i].data.id_comunicacion;
                
                if(i < (sm.getSelection().length -1))
                {
                    param = param + '|';
                }
            }
        } 
        
        if(estado == 0)
        {
            Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn)
            {
                if(btn=='yes')
                {
                    Ext.MessageBox.wait("Guardando datos...");
                    
                    Ext.Ajax.request
                    ({
                        url: strUrlDeleteAjax,
                        method: 'post',
                        params: { param : param},
                        success: function(response)
                        {
                            Ext.MessageBox.hide();
                            
                            var text = response.responseText;
                            
                            Ext.Msg.alert('Información', text);
                            
                            store.load();
                        },
                        failure: function(result)
                        {
                            Ext.MessageBox.hide();
                            
                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                        }
                    });
                }
            });
        }
        else
        {
            Ext.Msg.alert('Alerta', 'Por lo menos uno de las registro se encuentra en estado Finalizado, Cerrado, Rechazado o Cancelado');
        }
    }
    else
    {
        Ext.Msg.alert('Alerta', 'Seleccione por lo menos un registro de la lista.');
    }
}



function agregarSeguimiento(id_caso,numero,data){
    
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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
        
    btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
              //  json_tareas = obtenerTareas();
                var llamadasSeguimiento = Ext.getCmp('seguimiento').value
                winSeguimiento.destroy();
                conn.request({
                    method: 'POST',
                    params :{
                        id_caso: id_caso,
                        id_detalle: data.idDetalle,
                        seguimiento: llamadasSeguimiento
                    },
                    url: '../info_caso/ingresarSeguimiento',
                    success: function(response){
                        Ext.Msg.alert('Mensaje','Se ingreso el seguimiento.', function(btn){
                        });
                    },
                    failure: function(rec, op) {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert('Alerta ',json.mensaje);
                    }
            });
            }
    });
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winSeguimiento.destroy();
            }
    });
    
                        
            
    formPanel2 = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 200,
            width: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 140,
                msgTarget: 'side'
            },

            items: [{
                xtype: 'fieldset',
                title: 'Información',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Tarea:',
                        id: 'tareaCaso',
                        name: 'tareaCaso',
                        value: data.nombreTarea
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Seguimiento:',
                        id: 'seguimiento',
                        name: 'seguimiento',
                        rows: 7,
                        cols: 70
                    }
                ]
            }]
         });
    winSeguimiento = Ext.create('Ext.window.Window', {
            title: 'Ingresar Seguimiento',
            modal: true,
            width: 660,
            height: 280,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show(); 
    
}


function verSeguimientoTarea(data){
  
  
      var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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
   
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winSeguimientoTarea.destroy();													
            }
    });
    
	storeSeguimientoTarea = new Ext.data.Store({ 
		//pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : '../info_caso/verSeguimientoTarea',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id_detalle: data.idDetalle				
			}
		},
		fields:
		[
		      {name:'id_detalle', mapping:'id_detalle'},
		      {name:'observacion', mapping:'observacion'},
		      {name:'departamento', mapping:'departamento'},
		      {name:'empleado', mapping:'empleado'},
		      {name:'fecha', mapping:'fecha'}					
		]
	});
	gridSeguimiento = Ext.create('Ext.grid.Panel', {
		id:'gridSeguimiento',
		store: storeSeguimientoTarea,		
		columnLines: true,
		columns: [
			{
			      id: 'observacion',
			      header: 'Observación',
			      dataIndex: 'observacion',
			      width:400,
			      sortable: true						 
			},
			  {
			      id: 'empleado',
			      header: 'Ejecutante',
			      dataIndex: 'empleado',
			      width:80,
			      sortable: true						 
			},
			  {
			      id: 'departamento',
			      header: 'Departamento',
			      dataIndex: 'departamento',
			      width:100,
			      sortable: true						 
			},
			  {
			      id: 'fecha',
			      header: 'Fecha Observación',
			      dataIndex: 'fecha',
			      width:120,
			      sortable: true						 
			}
		],		
		width: 700,
		height: 300,
		listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            },
                            viewready: function (grid) {
                                var view = grid.view;

                                // record the current cellIndex
                                grid.mon(view, {
                                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                        grid.cellIndex = cellIndex;
                                        grid.recordIndex = recordIndex;
                                    }
                                });

                                grid.tip = Ext.create('Ext.tip.ToolTip', {
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
	formPanelSeguimiento = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 300,
			width:700,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				//labelWidth: 140,
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					gridSeguimiento
				]
			}]
		 });
	winSeguimientoTarea = Ext.create('Ext.window.Window', {
			title: 'Seguimiento Tareas',
			modal: true,
			width: 750,
			height: 400,
			resizable: true,
			layout: 'fit',
			items: [formPanelSeguimiento],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show();       
}


function finalizarTarea(data)
{
    if (data.cerrarTarea == "S")
    {
        var conn = new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function(con, opt) {
                        Ext.get(document.body).mask('Finalizando Tarea...');
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function(con, res, opt) {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function(con, res, opt) {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                }
            }
        });
        conn.request({
            method: 'POST',
            params: {
                id_detalle      : data.idDetalle,
                observacion     : "se realiza el cierre de la tarea",
                esSolucion      : true,
                tiempo_total    : "",
                tiempo_cierre   : data.fechaActual,
                hora_cierre     : data.horaActual,
                tiempo_ejecucion: data.fechaEjecucion,
                hora_ejecucion  : data.horaEjecucion,
                tarea           : data.idTarea,
                tarea_final     : "N"
            },
            url: url_finalizarTarea,
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);
                if (json.success)
                {
                    Ext.Msg.alert('Mensaje', 'Se finalizo la tarea.');
                    store.load();
                }
                else
                {
                    Ext.Msg.alert('Alerta ', json.mensaje);
                }
            },
            failure: function(rec, op)
            {
                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ', json.mensaje);
            }
        });
    }
    else
    {
       Ext.Msg.alert('Alerta ', "Esta tarea no se puede finalizar debido que posee una o más subtareas asociadas, por favor cerrar las \n\
                                tareas asociadas a la tarea principal.");
    }
}





function getDiferenciaTiempo(fechaIni, fechaFin) {

    var fechaIniS = getDate(fechaIni).split("-");
    var fechaFinS = getDate(fechaFin).split("-");

    fechaF = (String)(fechaFinS[2] + "/" + fechaFinS[1] + "/" + fechaFinS[0]);

    fecha = (String)(fechaIniS[2] + "/" + fechaIniS[1] + "/" + fechaIniS[0]);

    var fechaInicio = new Date(fecha);
    var fechaFin = new Date(fechaF);

    var difFecha = fechaFin - fechaInicio;

    return Math.ceil((((difFecha / 1000) / 60) / 60) / 24);
}
function addZero(num)
{
    (String(num).length < 2) ? num = String("0" + num) : num = String(num);
    return num;
}
function getDate(date)
{
    if (typeof date === 'undefined')
    {
        var currentTime = new Date();
    }
    else
    {
        var currentTime = date;
    }

    var month = addZero(currentTime.getMonth() + 1);
    var day = addZero(currentTime.getDate());
    var year = currentTime.getFullYear();
    return(day + "-" + month + "-" + year);
}
function getHour(hour)
{
    if (typeof hour === 'undefined')
    {
        var currentTime = new Date();
    }
    else
    {
        var currentTime = hour;
    }

    var hour = addZero(currentTime.getHours());
    var minute = addZero(currentTime.getMinutes());
    return(hour + ":" + minute);
}

/**
 * Funcion que permite visualizar la informacion de un caso o tarea en una ventana nueva
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 12/08/2021
 * @param  strIdCasoTarea Numero del Caso o Tarea,
 * @param  strIdentificadorActividad Identifica si es un Caso o Tarea
 */
 function verInformacionActividad(strIdCasoTarea,strIdentificadorActividad)
 {     
    if (strIdentificadorActividad === 'C')
    {
        window.open('/soporte/info_caso/'+strIdCasoTarea+'/show', '_blank');
    } 
    else if  (strIdentificadorActividad === 'T') 
    {
        window.open('/soporte/call_activity/'+strIdCasoTarea+'/show', '_blank');
    }
 }