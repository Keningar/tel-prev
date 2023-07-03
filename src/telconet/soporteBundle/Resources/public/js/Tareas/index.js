/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var valorAsignacion   = "empleado";
var cuadrillaAsignada = "S";
var seleccionaHal     = false;
var nIntentos = 0;
var tipoHal;
var finesTarea  = null;
var finesTareaReasig  = null;
var nombreOlt = '';

Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();

    //Actualizar el indicador de tareas
    Ext.Ajax.request({
        url: url_indicadorTareas,
        method: 'post',
        timeout: 9600000,
        params: {
            personaEmpresaRol : intPersonaEmpresaRolId
        },
        success: function(response) {
            var text = Ext.decode(response.responseText);
                $("#spanTareasDepartamento").text(text.tareasPorDepartamento);
                $("#spanTareasPersonales").text(text.tareasPersonales);
                $("#spanCasosMoviles").text(text.cantCasosMoviles);

        },
        failure: function(result)
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });

	// **************** CLIENES ******************
    storeClientes = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getClientes',
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
			{name:'id_cliente', mapping:'id_cliente'},
			{name:'cliente', mapping:'cliente'}
		],
		autoLoad: false
    });
    comboCliente = new Ext.form.ComboBox({
        id: 'cmb_cliente',
        name: 'cmb_cliente',
        fieldLabel: "Login",
        emptyText: 'Seleccione Cliente',
        store: storeClientes,
        displayField: 'cliente',
        valueField: 'id_cliente',
        height:30,
		width: 425,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
		
    comboTareaStore = new Ext.data.Store({ 
		total: 'total',
		autoLoad:false,
		proxy: {
			type: 'ajax',			
			url:url_gridTarea,
			reader: {
			    type: 'json',
			    totalProperty: 'total',
			    root: 'encontrados'
			},
			extraParams: {
			    nombre: '',
			    estado: 'Activo',
			    visible: 'SI'
			}
		},
		fields:
		[
			{name:'id_tarea', mapping:'id_tarea'},
	                {name:'nombre_tarea', mapping:'nombre_tarea'}
		],
	});
	
	comboTarea = Ext.create('Ext.form.ComboBox', {
		id:'cmb_tarea',
		store: comboTareaStore,
		displayField: 'nombre_tarea',
		valueField: 'id_tarea',
		fieldLabel: 'Nombre Tarea',	
		height:30,
		width: 425,
		queryMode: "remote",
		emptyText: ''
	});
	
	storeDepartamentos = new Ext.data.Store({ 
		total: 'total',
		pageSize: 200,
		proxy: {
		    type: 'ajax',
		    method: 'post',
		    url: '/soporte/info_caso/getDepartamentosPorEmpresaYCiudad',
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
			{name:'id_departamento', mapping:'id_departamento'},
			{name:'nombre_departamento', mapping:'nombre_departamento'}
		      ]
	}); 


	storeDepartamentosO = new Ext.data.Store({
		total: 'total',
		pageSize: 200,
		proxy: {
		    type: 'ajax',
		    method: 'post',
		    url: url_departamentoPorEmpresaCiudad,
		    reader: {
			type: 'json',
			totalProperty: 'total',
			root: 'encontrados'
		    },
		    extraParams: {
			origen: 'O',
			estado: 'Activo'
		    }
		},
		fields:
		      [
			{name:'id_departamentoO', mapping:'id_departamentoO'},
			{name:'nombre_departamentoO', mapping:'nombre_departamentoO'}
		      ]
	});

    storeListaProcesos = new Ext.data.Store({
        total    : 'total',
        autoLoad : false,
        proxy: {
            type: 'ajax',
            url: url_procesos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'nombreProceso', mapping: 'nombreProceso'}
            ]
    });

    comboListaProcesos = Ext.create('Ext.form.ComboBox', {
		id:'cmb_listaProcesos',
		store: storeListaProcesos,
		displayField: 'nombreProceso',
		valueField: 'id',
		fieldLabel: 'Proceso',
		height:30,
		width: 425,
		queryMode: "remote",
		minChars: 3,
		emptyText: ''
    });

	comboDepartamentoEmpresa = Ext.create('Ext.form.ComboBox', {
		id:'cmb_departamento',
		store: storeDepartamentos,
		displayField: 'nombre_departamento',
		valueField: 'id_departamento',
		fieldLabel: 'Departamento',	
		height:30,
		width: 425,
		queryMode: "remote",
		minChars: 3,
		emptyText: '',
		disabled:true
	});

	comboDepartamentoOrigen = Ext.create('Ext.form.ComboBox', {
		id:'cmb_departamentoOrigen',
		store: storeDepartamentosO,
		displayField: 'nombre_departamentoO',
		valueField: 'id_departamentoO',
		fieldLabel: 'Departamento Origen',
		height:30,
		width: 425,
		queryMode: "remote",
		minChars: 3,
		emptyText: '',
		disabled:false
	});


    storeEstadosTareas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_estadosTareas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombre_estado_tarea',
             direction: 'ASC'
        }],
        fields: [
            {name: 'nombre_estado_tarea', mapping: 'nombre_estado_tarea'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount', {
        alias: 'plugin.selectedCount',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var len = records.length,
                        store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                len = store.getCount();
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboEstadosTareas = Ext.create('Ext.form.ComboBox', {
        id           : 'comboEstadosTareas',
        store        :  storeEstadosTareas,
        displayField : 'nombre_estado_tarea',
        valueField   : 'nombre_estado_tarea',
        fieldLabel   : 'Estado',
        width        :  425,
        queryMode    : "remote",
        plugins      : ['selectedCount'],
        disabled     : false,
        editable     : false,
        multiSelect  : true,
        displayTpl   : '<tpl for="."> {nombre_estado_tarea} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig   : {
            itemTpl: '{nombre_estado_tarea} <div class="uncheckedChkbox"></div>'
        }
    });

    storeCiudadesO = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ciudadPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                origen: 'O',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_cantonO', mapping: 'id_cantonO'},
                {name: 'nombre_cantonO', mapping: 'nombre_cantonO'}
            ]
    });

    storeCiudadesD = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ciudadPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                origen: 'D',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_cantonD', mapping: 'id_cantonD'},
                {name: 'nombre_cantonD', mapping: 'nombre_cantonD'}
            ]
    });

    comboCiudadOrigen = Ext.create('Ext.form.ComboBox', {
        id: 'comboCiudadOrigen',
        store: storeCiudadesO,
        displayField: 'nombre_cantonO',
        valueField: 'id_cantonO',
        fieldLabel: 'Ciudad Origen',
        height: 30,
        width: 425,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        disabled: false
    });


    comboCiudadDestino = Ext.create('Ext.form.ComboBox', {
        id: 'comboCiudadDestino',
        store: storeCiudadesD,
        displayField: 'nombre_cantonD',
        valueField: 'id_cantonD',
        fieldLabel: 'Ciudad',
        height: 30,
        width: 425,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        disabled: false
    });
		
    //Se consultan cuadrillas para buscar tareas relacionadas a estas
    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_cuadrillas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'id_cuadrilla', mapping: 'id_cuadrilla'},
                {name: 'nombre_cuadrilla', mapping: 'nombre_cuadrilla'}
            ]      
    }); 

    
    comboCuadrillas = new Ext.form.TextField(
    {
        xtype     : 'textfield',
        id        : 'cmb_cuadrillas',
        fieldLabel: 'Cuadrillas',
        width     : 425,
        readOnly  : true
    });

    storeEmpresas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresaPorSistema,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                app: 'TELCOS'                    
            }
            
        },
        fields:
            [
                {name: 'opcion', mapping: 'nombre_empresa'},
                {name: 'valor', mapping: 'prefijo'}
            ]
    }); 
    //Fin consulta Cuadrillas
    
    storeMotivosPausarTarea = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_motivosPausarTarea,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                opcion: 'PAUSAR TAREA'
            }
        },
        fields:
            [
                {name: 'id_motivo', mapping: 'id_motivo'},
                {name: 'nombre_motivo', mapping: 'nombre_motivo'}
            ]
    });

    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        total: 'total',
        proxy: 
        {
            timeout: 9600000,
            type: 'ajax',
            url : 'grid',
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                cliente: '',
                tarea: '',
                asignado: '',
                strOrigen: strOrigen,
                estado: 'Todos',
                feSolicitadaDesde: '',
                feSolicitadaHasta: '',
                feFinalizadaDesde: '',
                feFinalizadaHasta: '',
                numeroActividad  :intNumeroActividad
            }
        },
        fields:
		[
            {name: 'strEmpresaTarea',        mapping: 'strEmpresaTarea'},
            {name: 'id_detalle',             mapping: 'id_detalle'},
            {name: 'id_tarea',               mapping: 'id_tarea'},
            {name: 'id_tarea',               mapping: 'id_tarea'},
            {name: 'iniciadaDesdeMobil',     mapping: 'iniciadaDesdeMobil'},
            {name: 'strBanderaFinalizarInformeEjecutivo',     mapping: 'strBanderaFinalizarInformeEjecutivo'},
            {name: 'asignado_id',            mapping: 'asignado_id'},
            {name: 'ref_asignado_id',        mapping: 'ref_asignado_id'},
            {name: 'id_persona_empresa_rol', mapping: 'id_persona_empresa_rol'},
            {name: 'numero_tarea_Padre',     mapping: 'numero_tarea_Padre'},
            {name: 'numero_tarea',           mapping: 'numero_tarea'},
            {name: 'nombre_tarea',           mapping: 'nombre_tarea'},
            {name: 'cerrarTarea',            mapping: 'cerrarTarea'},
            {name: 'seguimientoInterno',     mapping: 'seguimientoInterno'},
            {name: 'asignado_nombre',        mapping: 'asignado_nombre'},
            {name: 'ref_asignado_nombre',    mapping: 'ref_asignado_nombre'},
            {name: 'clientes',               mapping: 'clientes'},
            {name: 'observacion',            mapping: 'observacion'},
            {name: 'strTareaIncAudMant',     mapping: 'strTareaIncAudMant'},
            {name: 'feTareaCreada',          mapping: 'feTareaCreada'},
            {name: 'feSolicitada',           mapping: 'feSolicitada'},
            {name: 'feTareaAsignada',        mapping: 'feTareaAsignada'},
            {name: 'feTareaHistorial',       mapping: 'feTareaHistorial'},
            {name: 'actualizadoPor',         mapping: 'actualizadoPor'},
            {name: 'perteneceCaso',          mapping: 'perteneceCaso'},
            {name: 'sessionTN',              mapping: 'sessionTN'},
            {name: 'casoPerteneceTN',        mapping: 'casoPerteneceTN'},
            {name: 'tareasManga',            mapping: 'tareasManga'},
            {name: 'mostrarCoordenadas',     mapping: 'mostrarCoordenadas'},
            {name: 'fechaEjecucion',         mapping: 'fechaEjecucion'},
            {name: 'tipoAsignado',           mapping: 'tipoAsignado'},
            {name: 'horaEjecucion',          mapping: 'horaEjecucion'},
            {name: 'estado',                 mapping: 'estado'},                
            {name: 'numero_caso',            mapping: 'numero_caso'},
            {name: 'numero_actividad',       mapping: 'numero_actividad'},    
            {name: 'estado_caso',            mapping: 'estado_caso'},            
            {name: 'nombre_proceso',         mapping: 'nombre_proceso'},
            {name: 'id_caso',                mapping: 'id_caso'},
            {name: 'action1',                mapping: 'action1'},
            {name: 'action2',                mapping: 'action2'},
            {name: 'action3',                mapping: 'action3'},
            {name: 'action4',                mapping: 'action4'},
            {name: 'action6',                mapping: 'action6'},
            {name: 'action7',                mapping: 'action7'},
            {name: 'action8',                mapping: 'action8'},
            {name: 'action9',                mapping: 'action9'},
            {name: 'action10',               mapping: 'action10'},
            {name: 'action11',               mapping: 'action11'},
            {name: 'action12',               mapping: 'action12'},
            {name: 'action13',               mapping: 'action13'},
            {name: 'action14',               mapping: 'action14'},
            {name: 'action15',               mapping: 'action15'},
            {name: 'seFactura',              mapping: 'seFactura'},
            {name: 'duracionTarea',          mapping: 'duracionTarea'},
            {name: 'tiempoPausada',          mapping: 'tiempoPausada'},
            {name: 'personaEmpresaRolId',    mapping: 'personaEmpresaRolId'},
            {name: 'duracionMinutos',        mapping: 'duracionMinutos'},
            {name: 'tareaEsHal',             mapping: 'tareaEsHal'},
            {name: 'esHal',                  mapping: 'esHal'},
            {name: 'tareaParametro',         mapping: 'tareaParametro'},
            {name: 'atenderAntes',           mapping: 'atenderAntes'},
            {name: 'boolRenviarSysCloud',    mapping: 'boolRenviarSysCloud'},
            {name: 'tieneProgresoRuta',      mapping: 'tieneProgresoRuta'},
            {name: 'tieneProgresoMateriales',mapping: 'tieneProgresoMateriales'},
            {name: 'requiereControlActivo',  mapping: 'requiereControlActivo'},
            {name: 'personaId',              mapping: 'personaId'},
            {name: 'servicioId',             mapping: 'servicioId'},
            {name: 'tipoMedioId',            mapping: 'tipoMedioId'},
            {name: 'permiteRegistroActivos', mapping: 'permiteRegistroActivos'},
            {name: 'departamentoId',         mapping: 'departamentoId'},
            {name: 'loginSesion',            mapping: 'loginSesion'},
            {name: 'intIdDetalleHist',       mapping: 'intIdDetalleHist'},
            {name: 'numBobinaVisualizar',    mapping: 'numBobinaVisualizar'},
            {name: 'estadoNumBobinaVisual',  mapping: 'estadoNumBobinaVisual'},
            {name: 'esInterdepartamental',   mapping: 'esInterdepartamental'},
            {name: 'permiteConfirIpSopTn',   mapping: 'permiteConfirIpSopTn'},
            {name: 'strTieneConfirIpServ',   mapping: 'strTieneConfirIpServ'},
            {name: 'idServicioVrf',          mapping: 'idServicioVrf'},
            {name: 'ultimaMillaSoporte',     mapping: 'ultimaMillaSoporte'},
            {name: 'tipoCasoEnlace',        mapping: 'tipoCasoEnlace'},
            {name: 'permiteValidarEnlaceSopTn', mapping: 'permiteValidarEnlaceSopTn' },
            {name: 'permiteCrearKml',       mapping: 'permiteCrearKml' },
            {name: 'idTareaAnterior',       mapping: 'idTareaAnterior' },
            {name: 'nombreTareaAnterior',   mapping: 'nombreTareaAnterior' }

            
            
	],
        autoLoad: true
    });   
    
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Obteniendo Fecha y Hora...');
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
    
    sm = Ext.create('Ext.selection.CheckboxModel',
    {
        checkOnly: true
    });    
    
        
    grid = Ext.create('Ext.grid.Panel', 
    {
        width: 1250,
        height: 400,
        store: store,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        loadMask: true,
        frame: false,
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_exportar',
                        text: 'Exportar',
                        scope: this,
                        handler: function() 
                        {
                            exportarExcel();
                        }
                    }
                ]
            }
        ],
        columns:
        [
            {
                id       : 'strEmpresaTarea',
                header   : 'Emp.',
                dataIndex: 'strEmpresaTarea',
                width    : 35,
                hidden   : boolOcultarColumnaEmpresa,
                hideable : true
            },
            {
                id: 'id_detalle',
                header: 'IdDetalle',
                dataIndex: 'id_detalle',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_tarea',
                header: 'IdTarea',
                dataIndex: 'id_tarea',
                hidden: true,
                hideable: false
            },
            {
                id: 'asignado_id',
                header: 'IdAsignado',
                dataIndex: 'asignado_id',
                hidden: true,
                hideable: false
            },
            {
                id: 'ref_asignado_id',
                header: 'IdRefAsignado',
                dataIndex: 'ref_asignado_id',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_persona_empresa_rol',
                header: 'IdPersonaEmpresaRol',
                dataIndex: 'id_persona_empresa_rol',
                hidden: true,
                hideable: false
            },
            {
                id: 'clientes',
                header: 'Pto. Cliente',
                dataIndex: 'clientes',                      
                width: 120,
                sortable: true,
                renderer : function(value, p,record){
                    var login = record.data.clientes;
                    return '<a href="#" onclick="setPuntoSesionByLogin(\''+login+'\');">'+login+'</a>';
                }                
            },
            {
                id: 'numero_caso',
                header: 'Numero Caso',
                dataIndex: 'numero_caso',
                width: 100,
                sortable: true,
            },
            {
                id: 'estado_caso',
                header: 'Estado del Caso',
                dataIndex: 'estado_caso',
                width: 90,
                sortable: true,
            },
            {
                id: 'nombre_proceso',
                header: 'Nombre Proceso',
                dataIndex: 'nombre_proceso',
                width: 200,
                sortable: true,
            },
            {
                id: 'numero_tarea_Padre',
                header: 'No. Tarea Padre',
                dataIndex: 'numero_tarea_Padre',
                width: 90,
                sortable: true,
            },
            {
                id: 'numero_tarea',
                header: 'No. Tarea',
                dataIndex: 'numero_tarea',
                width: 70,
                sortable: true,
            },
            {
                id: 'nombre_tarea',
                header: 'Tarea',
                dataIndex: 'nombre_tarea',
                width: 200,
                sortable: true
            },
            {
                id: 'Observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 200
            },
            {
                id: 'ref_asignado_nombre',
                header: 'Responsable Asignado',
                dataIndex: 'ref_asignado_nombre',
                width: 170,
                sortable: true
            },
            {
                id: 'feSolicitada',
                header: 'Fecha Ejecucion',
                dataIndex: 'feSolicitada',
                width: 100,
                sortable: true
            },
            {
                id: 'actualizadoPor',
                header: 'Actualizado Por',
                dataIndex: 'actualizadoPor',
                width: 170,
                sortable: true
            },
            {
                id: 'feTareaHistorial',
                header: 'Fecha Estado',
                dataIndex: 'feTareaHistorial',
                width: 100,
                sortable: true
            },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            },
            {
                id: 'seFactura',
                header: 'Se Factura',
                dataIndex: 'seFactura',
                width: 80,
                sortable: true,
                align: 'center',
            },
            {
                id: 'seFactura',
                header: 'Se Factura',
                dataIndex: 'seFactura',
                width: 80,
                sortable: true,
                align: 'center',
            },
            {
                id        : 'esHal',
                dataIndex : 'esHal',
                header    : 'Es Hal',
                width     :  80,
                sortable  :  true,
                align     : 'center'
            },
            {
                id        : 'atenderAntes',
                dataIndex : 'atenderAntes',
                header    : 'Atender Antes',
                width     :  90,
                sortable  :  true,
                align     : 'center'
            },
            {
                id        : 'tareaEsHal',
                dataIndex : 'tareaEsHal',
                hidden    : true,
                hideable  : false
            },
            {
                id        : 'duracionTarea',
                header    : 'Tiempo<br/>Transcurrido',
                dataIndex : 'duracionTarea',
                width     :  90,
                sortable  :  true,
                align     : 'center'
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 320,
                sortable: false,
                items:
                [
                    // Ejecutar Tarea
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso     = '{{ is_granted("ROLE_197-1237") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action6 = "icon-invisible";
                            }
                            
                            if (rec.get('action6') == "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Ejecutar Tarea';
                            }

                            return rec.get('action6');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso     = '{{ is_granted("ROLE_197-1237") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) 
                            {
                                rec.data.action6 = "icon-invisible";
                            }

                            if (rec.get('action6') != "icon-invisible") {
                                validarTareasAbiertas('iniciar', rec.data.id_detalle, rec.data.nombre_tarea, rec.data.duracionTarea, rec.data);
                            } else {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Pausar Tarea
                    {   //Boton que permite pausar los tiempos de una tarea
                        getClass: function(v, meta, rec)
                        {
                            if (!boolPermisoRenaudarPausar)
                            {
                                rec.data.action13 = "icon-invisible";
                            }

                            if (rec.get('action13') == "icon-invisible")
                            {
                                this.items[1].tooltip = '';
                            }
                            else
                            {
                                this.items[1].tooltip = 'Pausar Tarea';
                            }

                            return rec.get('action13');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            if (!boolPermisoRenaudarPausar) {
                                rec.data.action13 = "icon-invisible";
                            }

                            if (rec.get('action13') != "icon-invisible") {
                                aceptarRechazarTarea('pausar',rec.data.id_detalle, rec.data.nombre_tarea, rec.data.duracionTarea, rec.data);
                            } else {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Reanudar Tarea
                    {   //Boton que permite reanudar los tiempos de una tarea
                        getClass: function(v, meta, rec) 
                        {
                            if (!boolPermisoRenaudarPausar)
                            {
                                rec.data.action14 = "icon-invisible";
                            }

                            if (rec.get('action14') == "icon-invisible")
                            {
                                this.items[2].tooltip = '';
                            }
                            else
                            {
                                this.items[2].tooltip = 'Reanudar Tarea';
                            }

                            return rec.get('action14');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            if (!boolPermisoRenaudarPausar) {
                                rec.data.action14 = "icon-invisible";
                            }

                            if (rec.get('action14') != "icon-invisible") {
                                validarTareasAbiertas('reanudar', rec.data.id_detalle, rec.data.nombre_tarea, rec.data.duracionTarea, rec.data);
                            } else {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },                    
                    // Reprogramar Tarea
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = '{{ is_granted("ROLE_197-584") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') === "icon-invisible")
                            {
                                this.items[3].tooltip = '';
                            }
                            else
                            {
                                this.items[3].tooltip = 'Reprogramar Tarea';
                            }

                            return rec.get('action1');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-584") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') !== "icon-invisible")
                            {

                                conn.request({
                                    method: 'POST',                                                                                                                                                                          
                                    url:url_obtenerFechaServer,
                                    success: function(response) 
                                    {
                                        var json = Ext.JSON.decode(response.responseText);

                                        if (json.success)
                                        {
                                            if (rec.data.tareaEsHal && rec.data.tipoAsignado.toUpperCase() === 'CUADRILLA') {
                                                halAsigna('reprogramar',
                                                          json.fechaActual,
                                                          rec.data.id_detalle,
                                                          rec.data.id_tarea,
                                                          rec.data.numero_tarea,
                                                          rec.data);
                                            } else {
                                                reprogramarTarea(rec.data.id_detalle, rec.data, json.fechaActual, json.horaActual);
                                            }
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Alerta ', json.error);
                                        }
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Cancelar Tarea
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso     = '{{ is_granted("ROLE_197-585") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') == "icon-invisible")
                            {
                                this.items[4].tooltip = '';
                            }
                            else
                            {
                                this.items[4].tooltip = 'Cancelar Tarea';
                            }

                            return rec.get('action2');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso     = '{{ is_granted("ROLE_197-585") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') != "icon-invisible")
                            {
                                cancelarTarea(rec.data.id_detalle, rec.data, 'cancelada');
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Finalizar Tarea
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') == "icon-invisible")
                            {
                                this.items[5].tooltip = '';
                            }
                            else
                            {
                                this.items[5].tooltip = 'Finalizar Tarea';
                            }

                            return rec.get('action3');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec         = store.getAt(rowIndex);
                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') !== "icon-invisible")
                            {                                        
                                //Obtener la fecha y hora del servidor por cada instante en que requieran finalizar la tarea                                        
                                conn.request({
                                    method: 'POST',                                                                                                                                                                          
                                    url:url_obtenerFechaServer,
                                    success: function(response) 
                                    {
                                        var json = Ext.JSON.decode(response.responseText);                                                                                                

                                        if (json.success)
                                        {
                                            var fechaFinArray = json.fechaActual.split("-");
                                            var fechaActual   = fechaFinArray[2]+"-"+fechaFinArray[1]+"-"+fechaFinArray[0];

                                            rs = validarFechaTareaReprogramada(rec.data.fechaEjecucion, rec.data.horaEjecucion, 
                                                                                fechaActual, 
                                                                                json.horaActual);
                                                                                
                                            
                                            if (rs !== -1)
                                            {

                                                if(rec.get('permiteRegistroActivos') === true && ((rec.get('id_caso') !== 0) || (rec.get('esInterdepartamental') === true)))
                                                {
                                                        
                                                        var idDepartamento                  = rec.get('departamentoId');
                                                        var tipo                            = 'WEB';
                                                        var user                            = '';
                                                        var op                              = 'getCategoriasFinTarea';                                       
                                                        var msgErrorGetFin                  = 'No se pudo cargar las categorías de fin de tarea, vuelva a intentar';
                                                        var titleAlerta                     = 'ALERTA';

                                                        var dataRequest = 
                                                        {
                                                            idDepartamento: idDepartamento,
                                                            tipo:           tipo
                                                        };

                                                        var dataJsonRequest = 
                                                        {
                                                            data: dataRequest,
                                                            user: user,    
                                                            op: op
                                                        };
                                                        
                                                        Ext.getBody().mask('Obteniendo información...'); 
                                                        Ext.Ajax.request({
                                                            url: '../../rs/tecnico/ws/rest/procesar',
                                                            method: 'post',
                                                            timeout: 400000,
                                                            headers: { 'Content-Type': 'application/json' },
                                                            params : Ext.JSON.encode(dataJsonRequest),
                                                            success: function(conn, response, options, eOpts) {
                                                                    Ext.getBody().unmask();
                                                                    var resultFines = Ext.JSON.decode(conn.responseText);
                                                                    
                                                                    var status = resultFines.status;
                                                                    var categoriaTareas = resultFines.categoriaTareas;
                                                                    if(status === null || status === 'ERROR')
                                                                    {
                                                                        Ext.Msg.alert(titleAlerta,msgErrorGetFin);     
                                                                    }
                                                                    else
                                                                    {   
                                                                        if(status === 204){
                                                                            Ext.Msg.alert(titleAlerta,resultFines.mensaje);  
                                                                        }
                                                                        else
                                                                        {
                                                                            finesTarea = categoriaTareas;
                                                                            finalizarTarea(rec.data.id_detalle, rec.data, fechaActual,
                                                                                                        json.horaActual, rec.data.tipoAsignado, rec.data.asignado_id);  
                                                                                                        
                                                                            
                                                                            // Funcion que crea la pantalla de indisponibilidad, de manera que permanezca oculta
                                                                            //para poder obtener los valores a guardar y realizar un unico commit
                                                                            verIndisponibilidadTarea(rec.data);
                                                                            obtenerTiempoAfectacionIndisponibilidadTarea(rec.data);
                                                                        }
                                                                    }
                                                                },
                                                            failure: function(conn, response, options, eOpts) {
                                                                    Ext.Msg.alert(titleAlerta,msgErrorGetFin);
                                                            }
                                                        });
         
                                                }
                                                else
                                                {

                                                    finalizarTarea(rec.data.id_detalle, rec.data, fechaActual,
                                                    json.horaActual, rec.data.tipoAsignado, rec.data.asignado_id);

                                                    
                                                    // Funcion que crea la pantalla de indisponibilidad, de manera que permanezca oculta
                                                    //para poder obtener los valores a guardar y realizar un unico commit
                                                    verIndisponibilidadTarea(rec.data);
                                                    obtenerTiempoAfectacionIndisponibilidadTarea(rec.data);
                                                }
                                            }
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Alerta ', json.error);
                                        }
                                    }
                                });                                       
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // ReasignarTarea
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action4') === "icon-invisible")
                            {
                                this.items[6].tooltip = '';
                            }
                            else
                            {
                                this.items[6].tooltip = 'ReasignarTarea';
                            }

                            return rec.get('action4');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso) 
                            {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') !== "icon-invisible")
                            {

                                var arrayDataGrid               = grid.getStore().getAt(rowIndex).data;
                                var strTieneProgresoMateriales  = grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales;
                                var strEmpresaTarea             = grid.getStore().getAt(rowIndex).data.strEmpresaTarea;
                                var strControlActivos           = grid.getStore().getAt(rowIndex).data.requiereControlActivo;
                                
                                if((strTieneProgresoMateriales === "NO")   
                                   && strEmpresaTarea === "TN" 
                                   && strControlActivos === 'SI' 
                                   ){
                                    
                                    registroFibraMaterial(arrayDataGrid);
                                }
                                else
                                {
                                    //if(rec.get('id_caso') !== 0)
                                    if(rec.get('permiteRegistroActivos') === true && rec.get('departamentoId') != 126)
                                    {
                                        var idDepartamento                  = rec.get('departamentoId');
                                        var tipo                            = 'WEB';
                                        var user                            = '';
                                        var op                              = 'getCategoriasFinTarea';
                                        var msgErrorGetFin                  = 'No se pudo cargar las categorías de fin de tarea, vuelva a intentar';
                                        var titleAlerta                     = 'ALERTA';

                                        var dataRequest =
                                        {
                                            idDepartamento: idDepartamento,
                                            tipo:           tipo
                                        };

                                        var dataJsonRequest =
                                        {
                                            data: dataRequest,
                                            user: user,
                                            op: op
                                        };

                                        Ext.getBody().mask('Obteniendo información...');
                                        Ext.Ajax.request({
                                            url: '../../rs/tecnico/ws/rest/procesar',
                                            method: 'post',
                                            timeout: 400000,
                                            headers: { 'Content-Type': 'application/json' },
                                            params : Ext.JSON.encode(dataJsonRequest),
                                            success: function(connInfo, response, options, eOpts) {
                                                    Ext.getBody().unmask();
                                                    var resultFines = Ext.JSON.decode(connInfo.responseText);
                                                    var status = resultFines.status;
                                                    var categoriaTareas = resultFines.categoriaTareas;

                                                    if(status === null || status === 'ERROR')
                                                    {
                                                        Ext.Msg.alert(titleAlerta,msgErrorGetFin);
                                                    }
                                                    else if(status === 204)
                                                    {
                                                        Ext.Msg.alert(titleAlerta,resultFines.mensaje);
                                                    }
                                                    else
                                                    {
                                                        finesTareaReasig = categoriaTareas;
                                                        //Obtener la fecha y hora del servidor por cada instante en que requieran finalizar la tarea
                                                        conn.request
                                                        ({
                                                            method: 'POST',
                                                            url:url_obtenerFechaServer,
                                                            success: function(response)
                                                            {
                                                                var json = Ext.JSON.decode(response.responseText);

                                                                if (json.success)
                                                                {
                                                                    reasignarTarea(rec.data.id_detalle,
                                                                                   rec.data.id_tarea,
                                                                                   rec.data,
                                                                                   json.fechaActual,
                                                                                   json.horaActual,
                                                                                   rec.data.tareaEsHal,
                                                                                   rec.data.tipoAsignado);
                                                                }
                                                                else
                                                                {
                                                                    Ext.Msg.alert('Alerta ', json.error);
                                                                }
                                                            }
                                                        });
                                                    }
                                                                                                
                                                },
                                            failure: function(connInfo, response, options, eOpts) {
                                                    Ext.Msg.alert(titleAlerta,msgErrorGetFin);
                                            }
                                        });
                                    }
                                    else
                                    {
                                        //Obtener la fecha y hora del servidor por cada instante en que requieran finalizar la tarea
                                        conn.request
                                        ({
                                            method: 'POST',
                                            url:url_obtenerFechaServer,
                                            success: function(response)
                                            {
                                                var json = Ext.JSON.decode(response.responseText);

                                                if (json.success)
                                                {
                                                    reasignarTarea(rec.data.id_detalle,
                                                                   rec.data.id_tarea,
                                                                   rec.data,
                                                                   json.fechaActual,
                                                                   json.horaActual,
                                                                   rec.data.tareaEsHal,
                                                                   rec.data.tipoAsignado);
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Alerta ', json.error);
                                                }
                                            }
                                        });
                                        
                                    }    
                                    
                                }
                                    
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Ingresar Seguimiento
                    {
                        getClass: function(v, meta, rec) {
                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action7 = "icon-invisible";
                            }

                            if (rec.get('action7') == "icon-invisible")
                            {
                                this.items[7].tooltip = '';
                            }
                            else
                            {
                                this.items[7].tooltip = 'Ingresar Seguimiento';
                            }

                            return rec.get('action7');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso) 
                            {
                                rec.data.action7 = "icon-invisible";
                            }

                            if (rec.get('action7') != "icon-invisible")
                            {
                                agregarSeguimiento(rec.data.id_caso, rec.data.nombre_tarea, rec.data.id_detalle,rec.data.seguimientoInterno);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Rechazar Tarea
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action9 = "icon-invisible";
                            }

                            if (rec.get('action9') == "icon-invisible")
                            {
                                this.items[8].tooltip = '';
                            }
                            else
                            {
                                this.items[8].tooltip = 'Rechazar Tarea';
                            }

                            return rec.get('action9');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso || rec.get('strBanderaFinalizarInformeEjecutivo') == "N")
                            {
                                rec.data.action9 = "icon-invisible";
                            }

                            if (rec.get('action9') != "icon-invisible")
                            {
                                cancelarTarea(rec.data.id_detalle, rec.data, 'rechazada');
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Ver Seguimiento
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso)
                            {
                                rec.data.action8 = "icon-invisible";
                            }

                            if (rec.get('action8') == "icon-invisible")
                            {
                                this.items[9].tooltip = '';
                            }
                            else
                            {
                                this.items[9].tooltip = 'Ver Seguimiento';
                            }

                            return rec.get('action8');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action8 = "icon-invisible";
                            }

                            if (rec.get('action8') != "icon-invisible")
                            {
                                verSeguimientoTarea(rec.data.id_detalle);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Cargar Archivo
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) {
                                rec.data.action10 = "icon-invisible";
                            }

                            if (rec.get('action10') == "icon-invisible")
                                this.items[10].tooltip = '';
                            else
                                this.items[10].tooltip = 'Cargar Archivo';

                            return rec.get('action10')
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) {
                                rec.data.action10 = "icon-invisible";
                            }

                            if (rec.get('action10') != "icon-invisible")
                                subirMultipleAdjuntosTarea(rec);
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    // Ver Archivos
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) {
                                rec.data.action11 = "icon-invisible";
                            }

                            if (rec.get('action11') == "icon-invisible")
                                this.items[11].tooltip = '';
                            else
                                this.items[11].tooltip = 'Ver Archivos';

                            return rec.get('action11')
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) {
                                rec.data.action11 = "icon-invisible";
                            }

                            if (rec.get('action11') != "icon-invisible")
                                presentarDocumentosTareas(rec);
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    // Crear Tarea
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            if (!boolPermiso) {
                                rec.data.action12 = "icon-invisible";
                            }

                            if (rec.get('action12') === "icon-invisible")
                            {
                                this.items[12].tooltip = '';
                            }
                            else
                            {
                                this.items[12].tooltip = 'Crear Tarea';
                            }

                            return rec.get('action12');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action12 = "icon-invisible";
                            }

                            if (rec.get('action12') !== "icon-invisible")
                            {
                                //Obtener la fecha y hora del servidor por cada instante en que requieran finalizar la tarea
                                conn.request
                                ({
                                    method: 'POST',
                                    url:url_obtenerFechaServer,
                                    success: function(response)
                                    {
                                        var json = Ext.JSON.decode(response.responseText);

                                        if (json.success)
                                        {
                                            var fechaFinArray = json.fechaActual.split("-");
                                            var fechaActual = fechaFinArray[0] + "-" + fechaFinArray[1] + "-" + fechaFinArray[2];

                                            agregarSubTarea(rec.data,fechaActual,json.horaActual);
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Alerta ', json.error);
                                        }
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // Anular Tarea
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action15 = "icon-invisible";
                            }

                            if (rec.get('action15') == "icon-invisible")
                            {
                                this.items[13].tooltip = '';
                            }
                            else
                            {
                                this.items[13].tooltip = 'Anular Tarea';
                            }

                            return rec.get('action15');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);
                            
                            var permiso = $("#ROLE_197-38");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso)
                            {
                                rec.data.action15 = "icon-invisible";
                            }

                            if (rec.get('action15') != "icon-invisible")
                            {
                                cancelarTarea(rec.data.id_detalle, rec.data, 'anulada');
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    // ReintentoTareaSysCloud
                    {
                        getClass: function(v, meta, rec) {
                            var permiso     = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = false;
                            if(typeof permiso !== 'undefined')
                            {
                                boolPermiso = permiso;
                            }

                            var icon = 'button-grid-reintentoMonitoreTg';
                            this.items[14].tooltip = 'Reintento Tarea Sys Cloud-Center';

                            if (!boolPermiso || !rec.get('boolRenviarSysCloud')) {
                                icon = "icon-invisible";
                                this.items[14].tooltip = '';
                            }

                            return icon;
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = '{{ is_granted("ROLE_197-38") }}';
                            var boolPermiso = false;
                            if(typeof permiso !== 'undefined')
                            {
                                boolPermiso = permiso;
                            }

                            if (!boolPermiso || !rec.data.boolRenviarSysCloud) {
                                Ext.Msg.alert('Error', 'No tiene permisos para realizar esta acción');
                                return;
                            }

                            reintentoTareaSysCloud(rec.data);
                        }
                    },
                    // Confirmar Ip Servicio
                    {
                        getClass: function(v, meta, rec) 
                        {
                            
                            var permiso             = rec.get('permiteConfirIpSopTn');
                            var casoPerteneceTN     = rec.get('casoPerteneceTN');
                            var tieneProgConfIp     = rec.get('strTieneConfirIpServ');
                            var ultimaMillaSoporte  = rec.get('ultimaMillaSoporte');
                            var icon                = "icon-invisible";
                            this.items[15].tooltip  = '';
                            
                            if(permiso && casoPerteneceTN && 
                               tieneProgConfIp === 'NO' &&  (ultimaMillaSoporte === 'FO' 
                               || ultimaMillaSoporte === 'RAD' 
                               || ultimaMillaSoporte === 'UTP'))
                            {   
                                icon = 'button-grid-confirmarIpServicio';
                                this.items[15].tooltip = 'Confirmar enlace';    
                            }
                            
                            return icon;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            
                            var permiso             = rec.get('permiteConfirIpSopTn');
                            var casoPerteneceTN     = rec.get('casoPerteneceTN');
                            var tieneProgConfIp     = rec.get('strTieneConfirIpServ');
                            var ultimaMillaSoporte  = rec.get('ultimaMillaSoporte');
                            
                            if(permiso && casoPerteneceTN && 
                               tieneProgConfIp === 'NO' &&  (ultimaMillaSoporte === 'FO' 
                               || ultimaMillaSoporte === 'RAD' 
                               || ultimaMillaSoporte === 'UTP'))
                            {
                                var strEmpresaTarea     = rec.get('strEmpresaTarea');
                                var idEmpresa;
                                if(strEmpresaTarea == 'TN')
                                {
                                    idEmpresa = '10';
                                }
                                else if(strEmpresaTarea == 'MD')
                                {
                                    idEmpresa = '18';
                                } else 
                                {
                                    idEmpresa = '33';
                                }
                               
                                var arayDataConfirmar = 
                                                        {
                                                            idEmpresa:          idEmpresa,
                                                            idComunicacion:     rec.get('numero_tarea'),
                                                            idDetalle:          rec.get('id_detalle'),
                                                            strCodigoProgreso:  'CONFIRMA_IP_SERVICIO',
                                                            idServicio:         rec.get('servicioId'),
                                                            strOrigenProgreso:  'WEB'
                                                        };
                                         
                                confirmarIpServicioSoporte(arayDataConfirmar);
                            }
                            else
                            {
                                Ext.Msg.alert('Error', 'No puede realizar esta acción.');
                                return;
                            }
                            
                        }
                    },
                    // Validar enlace
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = rec.get('permiteValidarEnlaceSopTn');
                            var casoPerteneceTN = rec.get('casoPerteneceTN');
                            var tieneProgConfIp = rec.get('strTieneConfirIpServ');
                            var ultimaMillaSoporte = rec.get('ultimaMillaSoporte');
                            var tipoCasoEnlace = rec.get('tipoCasoEnlace');
                            var icon = "icon-invisible";
                            this.items[16].tooltip = '';
                            
                            if (permiso 
                                && casoPerteneceTN 
                                && tieneProgConfIp === 'NO' 
                                && (ultimaMillaSoporte === 'FO' 
                                    || ultimaMillaSoporte === 'RAD' 
                                    || ultimaMillaSoporte === 'UTP') 
                                && tipoCasoEnlace != 'Backbone') 
                                {
                                    icon = 'button-grid-validarEnlace';
                                    this.items[16].tooltip = 'Validar Enlace';
                                }

                            return icon;
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso = rec.get('permiteValidarEnlaceSopTn');
                            var casoPerteneceTN = rec.get('casoPerteneceTN');
                            var tieneProgConfIp = rec.get('strTieneConfirIpServ');
                            var ultimaMillaSoporte = rec.get('ultimaMillaSoporte');
                            

                            if (permiso && casoPerteneceTN &&
                                tieneProgConfIp === 'NO' 
                                &&  (ultimaMillaSoporte === 'FO' 
                                || ultimaMillaSoporte === 'RAD' 
                                || ultimaMillaSoporte === 'UTP'))  {
                                var strEmpresaTarea = rec.get('strEmpresaTarea');
                                var idEmpresa;
                                if (strEmpresaTarea == 'TN') {
                                    idEmpresa = '10';
                                } 
                                else if(strEmpresaTarea == 'MD')
                                {
                                    idEmpresa = '18';
                                } else 
                                {
                                    idEmpresa = '33';
                                }

                                var arayDataValidar = {
                                    idEmpresa: idEmpresa,
                                    idComunicacion: rec.get('numero_tarea'),
                                    idDetalle: rec.get('id_detalle'),
                                    casoId: rec.get('id_caso'),
                                    servicioId: rec.get('idServicioVrf'),
                                    user: rec.get('loginSesion'),
                                    ultimaMilla: ultimaMillaSoporte,
                                    empresaCod: strEmpresaTarea,
                                    departamentoId: rec.get('departamentoId'),
                                    strOrigenProgreso: 'WEB'
                                };

                                validarServicioSoporte(arayDataValidar);
                            } else {
                                Ext.Msg.alert('Error', 'No puede realizar esta acción.');
                                return;
                            }

                        }
                    },
                    //Permite crear kml ?
                    {
                        getClass: function(v, meta, rec) {
                            
                            var tipoCasoEnlace = rec.get('tipoCasoEnlace');
                            var permiteCrearKml = rec.get('permiteCrearKml');
                            var etadoTarea  = rec.get('estado');
                            var tipoMedioId = rec.get('tipoMedioId');
                            var icon = "icon-invisible";
                            this.items[17].tooltip = '';
                           
                            if ( (etadoTarea === 'Aceptada' || etadoTarea === 'Pausada')
                                && (   tipoMedioId === 1 
                                    || tipoMedioId === 2
                                    || tipoMedioId === 104
                                    || tipoMedioId === 107 ) 
                                && boolPermisoCrearKml === 'S'
                                && (tipoCasoEnlace != 'Backbone' || isEmpty($tipoCasoEnlace)) 
                                && (permiteCrearKml == null || permiteCrearKml != 'S')) 
                                {
                                    icon = 'button-grid-permitir-crear-KML';
                                    this.items[17].tooltip = 'Permite Crear KML';
                                }
                    
                            return icon;
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                           
                            
                            if (boolPermisoCrearKml === 'S') 
                            {
                                var strEmpresaTarea = rec.get('strEmpresaTarea');
                                var idEmpresa;
                                if (strEmpresaTarea == 'TN') {
                                    idEmpresa = '10';
                                }  
                                else if(strEmpresaTarea == 'MD')
                                {
                                    idEmpresa = '18';
                                } else 
                                {
                                    idEmpresa = '33';
                                }
                    
                                var arayDataValidar = {
                                    idEmpresa: idEmpresa,
                                    idComunicacion: rec.get('numero_tarea'),
                                    idDetalle: rec.get('id_detalle'),
                                    casoId: rec.get('id_caso'),
                                    servicioId: rec.get('idServicioVrf'),
                                    user: rec.get('loginSesion'),
                                    empresaCod: strEmpresaTarea,
                                    departamentoId: rec.get('departamentoId'),
                                    strOrigenProgreso: 'WEB'
                                };
                    
                                permitirCrearKml(arayDataValidar);
                            } else {
                                Ext.Msg.alert('Error', 'No puede realizar esta acción.');
                                return;
                            }
                            
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
        renderTo: 'grid',
        listeners: 
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
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
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;
                    
                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        }
    });

    //Store Seleccion Cuadrillas
    storeSeleccionCuadrillas = Ext.create('Ext.data.Store', {
        fields   : ['id_cuadrilla','nombre_cuadrilla'],
        pageSize : 5,
        proxy    : {type: 'memory'}
    });

    //Button para la seleccion de cuadrillas
    verSeleccionCuadrillas = Ext.create('Ext.Button', {
        id          : 'btnVerSeleccionCuadrillas',
        text        : '<i class="fa fa-search" aria-hidden="true"></i>',
        tooltip     : 'Cuadrillas Seleccionadas',
        tooltipType : 'title',
        handler: function() {
            seleccionarCuadrillas({'storeCuadrillas'          : storeCuadrillas,
                                   'storeSeleccionCuadrillas' : storeSeleccionCuadrillas});
        }
    });

    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders        
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 1250,
        title: 'Criterios de busqueda',
        buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function() {
                        buscar();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function() {
                        limpiar();
                    }
                }
            ],
        items:
            [
                {html: "&nbsp;", border: false, width: 150},
                comboCliente,
                {html: "&nbsp;", border: false, width: 150},
                comboTarea,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtAsignado',
                    fieldLabel: 'Asignado',
                    value: '',
                    width: 425
                },
                {html: "&nbsp;", border: false, width: 150},
                comboEstadosTareas,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Solicitada',
                    items: [
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'feSolicitadaDesde',
                            name: 'feSolicitadaDesde',
                            fieldLabel: 'Desde:',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'feSolicitadaHasta',
                            name: 'feSolicitadaHasta',
                            fieldLabel: 'Hasta:',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Estado',
                    items: [
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'feFinalizadaDesde',
                            name: 'feFinalizadaDesde',
                            fieldLabel: 'Desde:',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'feFinalizadaHasta',
                            name: 'feFinalizadaHasta',
                            fieldLabel: 'Hasta:',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'txtActividad',
                    fieldLabel: 'Numero Tarea',
                    value: '',
                    width: 425
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'txtCaso',
                    fieldLabel: 'Numero Caso',
                    value: '',
                    width: 425
                },
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Empresa',
                    id: 'cmbEmpresaBusqueda',                    
                    store: storeEmpresas,
                    displayField: 'opcion',
					valueField: 'valor',
                    width: 425,
                    listeners: {
                        select: function(combo)
                        {
                            Ext.getCmp('cmb_departamento').reset();
                            Ext.getCmp('cmb_departamento').setDisabled(false);

                            Ext.getCmp('cmb_cuadrillas').value = "";
                            Ext.getCmp('cmb_cuadrillas').setRawValue("");
                            Ext.getCmp('cmb_cuadrillas').setDisabled(true);
                            storeSeleccionCuadrillas.removeAll();
                            Ext.getCmp('btnVerSeleccionCuadrillas').setDisabled(true);

                            storeDepartamentos.proxy.extraParams = {id_canton: '', empresa: combo.getValue()};
                            storeDepartamentos.load();
                        }
                    }
                },
                {html: "&nbsp;", border: false, width: 150},
                comboCiudadDestino,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                comboDepartamentoEmpresa,
                {html: "&nbsp;", border: false, width: 150},
                comboCuadrillas,verSeleccionCuadrillas,
                {html: "&nbsp;", border: false, width: 150},
                comboCiudadOrigen,
                {html: "&nbsp;", border: false, width: 150},
                comboDepartamentoOrigen,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'txtTareaPadre',
                    fieldLabel: 'Numero Tarea Padre',
                    value: '',
                    width: 425
                },
                {html: "&nbsp;", border: false, width: 150},
                comboListaProcesos,
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'checkbox',
                    id: 'checkboxECUCERT',
                    name: 'checkboxECUCERT',
                    fieldLabel: 'Tareas Ecucert',
                    hidden: boolNoVisualizacionECUCERT,
                    itemCls: 'x-check-group-alt'
                }
            ],
        renderTo: 'filtro'
    });
    
    if( strPuntoPersonaSession != '' )
    {
        store.getProxy().extraParams.cliente             = strPuntoPersonaSession;
        store.getProxy().extraParams.departamentoSession = strDepartamentoSession;
        store.load();
    }
});

/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function exportarExcel(){

    var opcionBusqueda     = $('#hid_opcion_busqueda').val()   ? $('#hid_opcion_busqueda').val() : 'N';
    var numeroTarea        = Ext.getCmp('txtActividad').value  ? Ext.getCmp('txtActividad').value : '';
    var tareaPadre         = Ext.getCmp('txtTareaPadre').value ? Ext.getCmp('txtTareaPadre').value : '';
    var numeroCaso         = Ext.getCmp('txtCaso').value       ? Ext.getCmp('txtCaso').value : '';

    var feSolicitadaDesde  = Ext.getCmp('feSolicitadaDesde').value ? Ext.getCmp('feSolicitadaDesde').value : '';
    var feSolicitadaHasta  = Ext.getCmp('feSolicitadaHasta').value ? Ext.getCmp('feSolicitadaHasta').value : '';
    var feFinalizadaDesde  = Ext.getCmp('feFinalizadaDesde').value ? Ext.getCmp('feFinalizadaDesde').value : '';
    var feFinalizadaHasta  = Ext.getCmp('feFinalizadaHasta').value ? Ext.getCmp('feFinalizadaHasta').value : '';
    var filtroUsuario      = $('#filtroUsuario').val() ? $('#filtroUsuario').val() : '';

    var login              = Ext.getCmp('cmb_cliente').value ? Ext.getCmp('cmb_cliente').value : '';
    var tarea              = Ext.getCmp('cmb_tarea').value ? Ext.getCmp('cmb_tarea').value : '';
    var asignado           = Ext.getCmp('txtAsignado').value ? Ext.getCmp('txtAsignado').value : '';
    var estado             = Ext.getCmp('comboEstadosTareas').value ? Ext.getCmp('comboEstadosTareas').value : '';
    var empresaBusca       = Ext.getCmp('cmbEmpresaBusqueda').value ? Ext.getCmp('cmbEmpresaBusqueda').value : '';
    var departamento       = Ext.getCmp('cmb_departamento').value ? Ext.getCmp('cmb_departamento').value : '';
    var cuadrilla          =  '';
    var nombreCuadrilla    =  '';

    if (storeSeleccionCuadrillas !== null &&
        storeSeleccionCuadrillas.data !== null &&
        storeSeleccionCuadrillas.data.items !== null &&
        storeSeleccionCuadrillas.data.items.length > 0 ) {
        cuadrilla       = [];
        nombreCuadrilla = [];
        for(var i = 0 ; i < storeSeleccionCuadrillas.data.items.length ; ++i) {
            cuadrilla.push(storeSeleccionCuadrillas.data.items[i].data.id_cuadrilla);
            nombreCuadrilla.push(storeSeleccionCuadrillas.data.items[i].data.nombre_cuadrilla);
        }
        cuadrilla       = cuadrilla.toString();
        nombreCuadrilla = nombreCuadrilla.toString();
    }

    if (estado.toString().indexOf('Todos') >= 0 || !estado) {
        estado = '';
    } else {
        estado = estado.toString();
    }

    if (
        (
          (opcionBusqueda === 'S' || store === null ||  store.data === null || store.data.items === null || store.data.items.length < 1)
           && Ext.isEmpty(numeroTarea) && Ext.isEmpty(tareaPadre) && Ext.isEmpty(numeroCaso)
        ) ||
        (
           !Ext.isEmpty(login) || !Ext.isEmpty(tarea) || !Ext.isEmpty(asignado) || !Ext.isEmpty(estado) ||
           !Ext.isEmpty(departamento) || !Ext.isEmpty(empresaBusca) || !Ext.isEmpty(cuadrilla)
        )
       )
    {
        if (Ext.isEmpty(Ext.getCmp('feSolicitadaDesde').value) &&
            Ext.isEmpty(Ext.getCmp('feSolicitadaHasta').value) &&
            Ext.isEmpty(Ext.getCmp('feFinalizadaDesde').value) &&
            Ext.isEmpty(Ext.getCmp('feFinalizadaHasta').value)) {
            Ext.Msg.alert('Alerta ', "Por favor elegir un rango de fechas sea por <b>Solicitada</b> o <b>Estado</b><br/>"+
                                     "y <b>no mayor a 30 días</b>.");
            return;
        }

        if ((Ext.isEmpty(Ext.getCmp('feSolicitadaDesde').value) && !Ext.isEmpty(Ext.getCmp('feSolicitadaHasta').value)) ||
            (!Ext.isEmpty(Ext.getCmp('feSolicitadaDesde').value) && Ext.isEmpty(Ext.getCmp('feSolicitadaHasta').value))) {
            Ext.Msg.alert('Alerta ', "Por favor completar el rango de <b>Fecha Solicitada</b>.");
            return;
        }

        if ((Ext.isEmpty(Ext.getCmp('feFinalizadaDesde').value) && !Ext.isEmpty(Ext.getCmp('feFinalizadaHasta').value)) ||
            (!Ext.isEmpty(Ext.getCmp('feFinalizadaDesde').value) && Ext.isEmpty(Ext.getCmp('feFinalizadaHasta').value))) {
            Ext.Msg.alert('Alerta ', "Por favor completar el rango de <b>Fecha Estado</b>.");
            return;
        }

        if (!Ext.isEmpty(Ext.getCmp('feSolicitadaDesde').value) && !Ext.isEmpty(Ext.getCmp('feSolicitadaHasta').value)) {

            if (new Date(Ext.getCmp('feSolicitadaHasta').value) < new Date(Ext.getCmp('feSolicitadaDesde').value) ) {
                Ext.Msg.alert('Alerta ', "La Fecha Solicitada <b>Hasta</b> no puede ser menor a la Fecha Solicitada <b>Desde</b>.");
                return;
            }

            if (getDiferenciaTiempo(Ext.getCmp('feSolicitadaDesde').value , Ext.getCmp('feSolicitadaHasta').value ) > 31) {
                Ext.Msg.alert('Alerta ', "La <b>Fecha Solicitada</b> supera un rango mayor a 30 días.");
                return;
            }
        }

        if (!Ext.isEmpty(Ext.getCmp('feFinalizadaDesde').value) && !Ext.isEmpty(Ext.getCmp('feFinalizadaHasta').value)) {

            if (new Date(Ext.getCmp('feFinalizadaHasta').value) < new Date(Ext.getCmp('feFinalizadaDesde').value) ) {
                Ext.Msg.alert('Alerta ', "La Fecha Estado <b>Hasta</b> no puede ser menor a la Fecha Estado <b>Desde</b>.");
                return;
            }

            if (getDiferenciaTiempo(Ext.getCmp('feFinalizadaDesde').value , Ext.getCmp('feFinalizadaHasta').value ) > 31) {
                Ext.Msg.alert('Alerta ', "La <b>Fecha Estado</b> supera un rango mayor a 30 días.");
                return;
            }
        }
    }

    Ext.MessageBox.show({
        title      : "Mensaje",
        msg        : '¿Está seguro de generar el <b>Reporte de Tareas</b>?',
        closable   : false,
        multiline  : false,
        icon       : Ext.Msg.QUESTION,
        buttons    : Ext.Msg.YESNO,
        buttonText : {yes: 'Si', no: 'No'},
        fn: function (buttonValue)
        {
            if (buttonValue === "yes") {
                Ext.MessageBox.wait('Procesando...');
                Ext.Ajax.request({
                    url    :  urlTareasExportar,
                    method : 'post',
                    timeout:  400000,
                    params : {
                        'hid_cliente'         : login,
                        'hid_tarea'           : tarea,
                        'hid_asignado'        : asignado,
                        'hid_estado'          : estado,
                        'hid_numeroTarea'     : numeroTarea,
                        'hid_TareaPadre'      : tareaPadre,
                        'hid_numeroCaso'      : numeroCaso,
                        'hid_departamento'    : departamento,
                        'hid_empresa'         : empresaBusca,
                        'hid_cuadrilla'       : cuadrilla,
                        'hid_nombreCuadrilla' : nombreCuadrilla,
                        'hid_opcion_busqueda' : opcionBusqueda,
                        'filtroUsuario'       : filtroUsuario,
                        'feSolicitadaDesde'   : feSolicitadaDesde,
                        'feSolicitadaHasta'   : feSolicitadaHasta,
                        'feFinalizadaDesde'   : feFinalizadaDesde,
                        'feFinalizadaHasta'   : feFinalizadaHasta
                    },
                    success: function (response) {

                        var objData = Ext.JSON.decode(response.responseText); //Obtenemos la respuesta del controlador
                        var status  = objData.status;
                        var message = objData.status === 'ok'
                            ? objData.message+'. En breves minutos llegará el reporte a su correo.'
                            : objData.message;

                        Ext.MessageBox.show({
                            title      : status === 'ok' ? 'Mensaje' : 'Alerta',
                            msg        : message,
                            buttons    : Ext.MessageBox.OK,
                            icon       : status === 'ok' ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                            closable   : false,
                            multiline  : false,
                            buttonText : {ok: 'Cerrar'}
                        });
                    },
                    failure: function (result) {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}

function validarTareasAbiertas(origen, data, nombre, duracionTarea, grid)
{
    var connTareasAbiertas = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Validando Tareas Abiertas...');
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


    connTareasAbiertas.request({
        url: url_getTareasAbiertas,
        method: 'post',
        params:
            {
                personaEmpresaRolId: grid.personaEmpresaRolId
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);
            if (text.intCantidadTareasEjecutando > 0)
            {
                Ext.Msg.confirm("Confirmación", "Actualmente posee la tarea #" + text.strTareas + " en ejecución, \n\
                                                 ¿Desea pausarla?",
                    function(btnText) {
                        if (btnText === "yes")
                        {
                            gestionarTareas(origen, data, nombre, duracionTarea, grid);
                        }
                    }, this);
            }
            else
            {
                aceptarRechazarTarea(origen, data, nombre, duracionTarea, grid);
            }
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}



function gestionarTareas(origen, data, nombre, duracionTarea, grid)
{
    var connPausandoTareas = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Pausando Tareas...');
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


    connPausandoTareas.request({
        url: url_ejecutarPausarTareas,
        method: 'post',
        params:
            {
                personaEmpresaRolId: grid.personaEmpresaRolId,
                numeroTarea: grid.numero_tarea,
                nombre_tarea: grid.nombre_tarea,
                nombre_proceso: grid.nombre_proceso,
                asignado_nombre: grid.ref_asignado_nombre,
                departamento_nombre: grid.asignado_nombre,
                id_detalle: grid.id_detalle
            },
        success: function(response) {
            var json = Ext.decode(response.responseText);

            Ext.MessageBox.show({
                title: "Información",
                msg: json.mensaje,
                icon: Ext.MessageBox.INFO,
                buttons: Ext.Msg.OK,
                fn: function(buttonId)
                {
                    aceptarRechazarTarea(origen, data, nombre, duracionTarea, grid);
                }
            });
        },
        failure: function(response) {
            var json = Ext.JSON.decode(response.responseText);

            Ext.Msg.show(
                {
                    title: 'Error',
                    width: 300,
                    cls: 'msg_floating',
                    icon: Ext.MessageBox.ERROR,
                    msg: json.mensaje
                });
        }
    });
}

/**
* 
* aceptarRechazarTarea
*
* @version 1.0 - No se encontro historial de versiones
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 09-10-2019 - Se agrega la variable grid para obtener los datos de la tarea para ser procesada en Syscloud.
* @since 1.0
*
*/
function aceptarRechazarTarea(origen, data, nombre, duracionTarea, grid){

   var tituloVentana        = "";
   var tituloFielset        = "";
   var boolMotivoPausa      = true;
   var boolCampoObservacion = false;
   var jsonDatosPausa       = "";

   if(origen == "iniciar")
   {
       tituloVentana        = "Ejecutar Tarea Asignada";
       tituloFielset        = "Ejecutar Tarea";
       boolCampoObservacion = false;
   }

   if(origen == "pausar")
   {
       tituloVentana        = "Pausar Tarea Asignada";
       tituloFielset        = "Pausar Tarea";
       boolMotivoPausa      = false;
       boolCampoObservacion = true;
   }

   if(origen == "reanudar")
   {
       tituloVentana        = "Reanudar Tarea Asignada";
       tituloFielset        = "Reanudar Tarea";
       boolCampoObservacion = false;
   }

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

    btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
	cls : 'x-btn-rigth',
	handler: function() {

            if(origen == "pausar") {
                var acepObservacion = Ext.getCmp('comboMotivoPausa').value;
            } else {
                var acepObservacion = Ext.getCmp('observacion').value;
            }

            win.destroy();

            conn.request({
                method : 'POST',
                url    : 'administrarTareaAsignada',
                params : {
                    id               : data,
                    observacion      : acepObservacion,
                    bandera          : 'Aceptada',
                    origen           : origen,
                    duracionTarea    : duracionTarea,
                    jsonDatosPausa   : jsonDatosPausa,
                    intIdDetalleHist : grid.intIdDetalleHist,
                    numeroTarea    : grid.numero_tarea,
                    nombre_tarea   : grid.nombre_tarea,
                    nombre_proceso : grid.nombre_proceso,
                    asignado_nombre: grid.ref_asignado_nombre,
                    departamento_nombre: grid.asignado_nombre
                },
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);

                    if (!json.success && !json.seguirAccion) {
                        Ext.MessageBox.show({
                            closable   :  false  , multiline : false,
                            title      : 'Alerta', msg : json.mensaje,
                            buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                            buttonText : {ok: 'Cerrar'},
                            fn : function (button) {
                                if(button === 'ok') {
                                    store.load();
                                }
                            }
                        });
                        return;
                    }

                    if (json.mensaje != "cerrada") {
                        Ext.Msg.alert('Mensaje', 'Se actualizó los datos.', function(btn) {
                            store.load();
                        });
                    } else {
                        Ext.Msg.alert('Alerta ', "La tarea se encuentra Cerrada, por favor consultela nuevamente");
                    }
                },
                failure: function(rec, op) {
                    var json = Ext.JSON.decode(op.response.responseText);
                    Ext.Msg.alert('Alerta ',json.mensaje);
                }
            });
        }
    });

    btncancelar = Ext.create('Ext.Button', {
        text : 'Cerrar',
        cls  : 'x-btn-rigth',
        handler: function() {
            win.destroy();
        }
    });

	formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		layout: 'column',
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 150,
			msgTarget: 'side'
		},
		items: 
		[			
			{
				xtype: 'fieldset',
				title: tituloFielset,
				autoHeight: true,
				width: 475,
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'calendario_tarea',
						name: 'calendario_tarea',
						value: nombre
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'observacion',
						name: 'observacion',
						rows: 3,
						cols: 40,
                                                hidden:boolCampoObservacion
					},
                                        {
                                            width: 475,
                                            height:10,
                                            layout: 'form',
                                            border: false,
                                            hidden:boolMotivoPausa,
                                            items:
                                            [
                                                {
                                                    xtype: 'displayfield'
                                                }
                                            ]
                                        },
					{
                                            xtype: 'combobox',
                                            fieldLabel: 'Motivo',
                                            id: 'comboMotivoPausa',
                                            width: 450,
                                            name: 'comboMotivoPausa',
                                            store: storeMotivosPausarTarea,
                                            displayField: 'nombre_motivo',
                                            valueField: 'nombre_motivo',
                                            queryMode: "remote",
                                            emptyText: '',
                                            hidden:boolMotivoPausa,
                                            listeners : {
                                                select : function(combo, rec, idx, data) {
                                                    if (typeof rec[0].raw.tiempo !== 'undefined' &&
                                                        typeof rec[0].raw.tipo   !== 'undefined') {
                                                        jsonDatosPausa = Ext.JSON.encode({'tiempo':rec[0].raw.tiempo,
                                                                                          'tipo'  :rec[0].raw.tipo});
                                                    }
                                                }
                                            }
					}
				]
			}
		]
	});

	win = Ext.create('Ext.window.Window', {
		title: tituloVentana,
		closable: false,
		modal: true,
		width: 500,
		height: 200,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btncancelar]
	}).show();
    
    
    
}

function subirAdjuntoTarea(rec)
{
      var id_tarea = rec.get('id_detalle');

      var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Procesando...');
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

     var formPanel = Ext.create('Ext.form.Panel',
     {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',

        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },

        items: [{
            xtype: 'filefield',
            id: 'form-file',
            name: 'archivo',
            emptyText: 'Seleccione una Archivo',
            buttonText: 'Browse',
            buttonConfig: {
                iconCls: 'upload-icon'
            }
        }],

        buttons: [{
            text: 'Subir',
            handler: function(){
                 var form = this.up('form').getForm();
                 if(form.isValid())
		 {
		      form.submit({
			    url: url_fileUpload,
			    params :{
				  IdTarea    : id_tarea,
                  origenTarea: 'S'
			    },
			    waitMsg: 'Procesando Archivo...',
			    success: function(fp, o)
			    {
				  Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
					if(btn=='ok')
					{
					      win.destroy();
					}
				  });
			    },
			    failure: function(fp, o) {
				  Ext.Msg.alert("Alerta",o.result.respuesta);
			    }
			});
                }
            }
        },{
            text: 'Cancelar',
            handler: function() {
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Archivo Tarea',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}


function presentarDocumentosTareas(rec)
{
    var id_tarea           = rec.get('id_detalle');
    var strTareaIncAudMant = rec.get('strTareaIncAudMant');
    var cantidadDocumentos = 1;
    var connDocumentosTarea = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.MessageBox.show({
                       msg: 'Consultando documentos, Por favor espere!!',
                       progressText: 'Saving...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connDocumentosTarea.request({
        url: url_verifica_casos,
        method: 'post',
        params:
            {
                idTarea             : id_tarea,
                strTareaIncAudMant  : strTareaIncAudMant
            },
        success: function(response){
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if(cantidadDocumentos > 0)
            {
                var storeDocumentosCaso = new Ext.data.Store({
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url : url_documentosCaso,
                        reader: {
                            type         : 'json',
                            totalProperty: 'total',
                            root         : 'encontrados'
                        },
                        extraParams: {
                            idTarea             : id_tarea,
                            strTareaIncAudMant  : strTareaIncAudMant
                        }
                    },
                    fields:
                        [
                            {name:'idDocumento',            mapping:'idDocumento'},
                            {name:'ubicacionLogica',        mapping:'ubicacionLogica'},
                            {name:'feCreacion',             mapping:'feCreacion'},
                            {name:'usrCreacion',            mapping:'usrCreacion'},
                            {name:'linkVerDocumento',       mapping:'linkVerDocumento'},
                            {name:'boolEliminarDocumento',  mapping:'boolEliminarDocumento'}
                        ]
                });

                Ext.define('DocumentosCaso', {
                    extend: 'Ext.data.Model',
                    fields: [
                          {name:'ubicacionLogica',  mapping:'ubicacionLogica'},
                          {name:'feCreacion',       mapping:'feCreacion'},
                          {name:'linkVerDocumento', mapping:'linkVerDocumento'}
                    ]
                });

                //grid de documentos por Caso
                gridDocumentosCaso = Ext.create('Ext.grid.Panel', {
                    id:'gridMaterialesPunto',
                    store: storeDocumentosCaso,
                    columnLines: true,
                    columns: [{
                        header   : 'Nombre Archivo',
                        dataIndex: 'ubicacionLogica',
                        width    : 260
                    },
                    {
                        header   : 'Usr. Creación',
                        dataIndex: 'usrCreacion',
                        width    : 80
                    },
                    {
                        header   : 'Fecha de Carga',
                        dataIndex: 'feCreacion',
                        width    : 120
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 90,
                        items:
                        [
                            {
                                iconCls: 'button-grid-show',
                                tooltip: 'Ver Archivo Digital',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec         = storeDocumentosCaso.getAt(rowIndex);
                                    verArchivoDigital(rec);
                                }
                            },
                            {
                                getClass: function(v, meta, rec) 
                                {
                                    var strClassButton  = 'button-grid-delete';
                                    if(!rec.get('boolEliminarDocumento'))
                                    {
                                        strClassButton = ""; 
                                    }

                                    if (strClassButton == "")
                                    {
                                        this.items[0].tooltip = ''; 
                                    }   
                                    else
                                    {
                                        this.items[0].tooltip = 'Eliminar Archivo Digital';
                                    }
                                    return strClassButton;

                                },
                                tooltip: 'Eliminar Archivo Digital',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec                 = storeDocumentosCaso.getAt(rowIndex);
                                    var idDocumento         = rec.get('idDocumento');
                                    var strClassButton      = 'button-grid-delete';
                                    if(!rec.get('boolEliminarDocumento'))
                                    {
                                        strClassButton = ""; 
                                    }

                                    if (strClassButton != "" )
                                    {
                                        eliminarAdjunto(storeDocumentosCaso,idDocumento);
                                            
                                    } 
                                }
                            }
                        ]
                    }
                ],
                    viewConfig:{
                        stripeRows:true,
                        enableTextSelection: true
                    },
                    frame : true,
                    height: 200
                });

                function verArchivoDigital(rec)
                {
                    var rutaFisica = rec.get('linkVerDocumento');
                    var posicion = rutaFisica.indexOf('/public')
                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                }

                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding  : 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget : 'side'
                    },
                    items: [

                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',

                        defaults: {
                            width: 550
                        },
                        items: [

                            gridDocumentosCaso

                        ]
                    }
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title   : 'Documentos Cargados',
                    modal   : true,
                    width   : 580,
                    closable: true,
                    layout  : 'fit',
                    items   : [formPanel]
                }).show();
            }else{
                Ext.Msg.show({
                title  :'Mensaje',
                msg    : 'La tarea seleccionada no posee archivos adjuntos.',
                buttons: Ext.Msg.OK,
                animEl : 'elId',
                });
            }

        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}


/**
* 
* buscar
*
* @version 1.0 - No se encontro historial de versiones
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 09-08-2019 - Se agrega el filtro para visualizar las tareas de ECUCERT.
* @since 1.0
*
*/
function buscar() 
{        
    login       = Ext.getCmp('cmb_cliente').value;
    numeroTarea = Ext.getCmp('txtActividad').value;
    tareaPadre  = Ext.getCmp('txtTareaPadre').value;
    numeroCaso  = Ext.getCmp('txtCaso').value;
    feDesde     = Ext.getCmp('feSolicitadaDesde').value;
    feHasta     = Ext.getCmp('feSolicitadaHasta').value;    
    feDesdeEst  = Ext.getCmp('feFinalizadaDesde').value;    
    feHastaEst  = Ext.getCmp('feFinalizadaHasta').value;    

    if( (login === "" || !login) && (numeroTarea === "" || !numeroTarea) && numeroCaso === "" && (tareaPadre === "" || !tareaPadre))
    {
        if(( feDesde === "" || feHasta === "" || isNaN(feDesde) || isNaN(feHasta)) &&
           ( feDesdeEst === "" || feHastaEst === "" || isNaN(feDesdeEst) || isNaN(feHastaEst))
          )       
        {
            Ext.Msg.alert('Alerta',"Debe escoger el rango de <b>fecha Solicitada o Estado</b> para su búsqueda");
            return;
        }
        else
        {
            if(!Ext.isEmpty(feDesde) && !Ext.isEmpty(feHasta))
            {
                if(getDiferenciaTiempo(feDesde , feHasta ) > 31)
                {
                    Ext.Msg.alert('Alerta ', "Consulta permitida para Fecha Solicitida con un máximo de 30 dias");
                    return;
                }
            }
            if(!Ext.isEmpty(feDesdeEst) && !Ext.isEmpty(feHastaEst))
            {
                if(getDiferenciaTiempo(feDesdeEst , feHastaEst ) > 31)
                {
                    Ext.Msg.alert('Alerta ', "Consulta permitida para Fecha de Estado con un máximo de 30 dias");
                    return;
                }
            }
        }
    }
        
    if (isNaN(comboCliente.getValue()))
    {
        comboCliente.setValue('');
    }
    $('#hid_opcion_busqueda').val("S");

    var JsonCuadrillas = [];
    if (storeSeleccionCuadrillas !== null && storeSeleccionCuadrillas.data !== null
            && storeSeleccionCuadrillas.data.items !== null
            && storeSeleccionCuadrillas.data.items.length > 0
            && !Ext.isEmpty(Ext.getCmp('cmb_cuadrillas').value)) {
        for(var i = 0 ; i < storeSeleccionCuadrillas.data.items.length ; ++i) {
            JsonCuadrillas.push(storeSeleccionCuadrillas.data.items[i].data.id_cuadrilla);
        }
        JsonCuadrillas = Ext.JSON.encode(JsonCuadrillas);
    }

    var arrayEstados = Ext.getCmp('comboEstadosTareas').getValue();
    if (arrayEstados.toString().indexOf('Todos') >= 0 || !arrayEstados) {
        arrayEstados = '';
    } else {
        arrayEstados = Ext.JSON.encode(arrayEstados);
    }

    var checkEcucert    = Ext.getCmp('checkboxECUCERT').value;
    var verTareasEcucert = 'N';
    if(checkEcucert != null && checkEcucert == true)
    {
        verTareasEcucert = 'S';
    }
    store.removeAll();
    store.getProxy().extraParams.cliente           = login;
    store.getProxy().extraParams.departamento      = Ext.getCmp('cmb_departamento').value;
    store.getProxy().extraParams.departamentoOrig  = Ext.getCmp('cmb_departamentoOrigen').value;
    store.getProxy().extraParams.ciudadOrigen      = Ext.getCmp('comboCiudadOrigen').value;
    store.getProxy().extraParams.proceso           = Ext.getCmp('cmb_listaProcesos').value;
    store.getProxy().extraParams.ciudadDestino     = Ext.getCmp('comboCiudadDestino').value;
    store.getProxy().extraParams.tarea             = Ext.getCmp('cmb_tarea').value;
    store.getProxy().extraParams.asignado          = Ext.getCmp('txtAsignado').value;
    store.getProxy().extraParams.estado            = arrayEstados;
    store.getProxy().extraParams.numeroActividad   = numeroTarea;
    store.getProxy().extraParams.numeroTareaPadre  = Ext.getCmp('txtTareaPadre').value;
    store.getProxy().extraParams.numeroCaso        = numeroCaso;
    store.getProxy().extraParams.feSolicitadaDesde = feDesde;
    store.getProxy().extraParams.feSolicitadaHasta = feHasta;
    store.getProxy().extraParams.feFinalizadaDesde = Ext.getCmp('feFinalizadaDesde').value;
    store.getProxy().extraParams.feFinalizadaHasta = Ext.getCmp('feFinalizadaHasta').value;
    store.getProxy().extraParams.cuadrilla         = JsonCuadrillas;
    store.getProxy().extraParams.opcionBusqueda    = "S";
    store.getProxy().extraParams.verTareasEcucert  = verTareasEcucert;

    store.load();
      
}

/**
* 
* buscar
*
* @version 1.0 - No se encontro historial de versiones
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 10-08-2019 - Se limpia el filtro de visualización para las tareas de ECUCERT.
* @since 1.0
*
*/
function limpiar() 
{
    Ext.getCmp('cmb_cliente').value = "";
    Ext.getCmp('cmb_cliente').setRawValue("");
    Ext.getCmp('cmb_tarea').value = "";
    Ext.getCmp('cmb_tarea').setRawValue("");

    Ext.getCmp('txtAsignado').value = "";
    Ext.getCmp('txtAsignado').setRawValue("");

    Ext.getCmp('txtActividad').value = "";
    Ext.getCmp('txtActividad').setRawValue("");

    Ext.getCmp('txtCaso').value = "";
    Ext.getCmp('txtCaso').setRawValue("");

    Ext.getCmp('comboEstadosTareas').value = "Todos";
    Ext.getCmp('comboEstadosTareas').setRawValue("Todos");

    Ext.getCmp('feSolicitadaDesde').value = "";
    Ext.getCmp('feSolicitadaDesde').setRawValue("");
    Ext.getCmp('feSolicitadaHasta').value = "";
    Ext.getCmp('feSolicitadaHasta').setRawValue("");

    Ext.getCmp('feFinalizadaDesde').value = "";
    Ext.getCmp('feFinalizadaDesde').setRawValue("");
    Ext.getCmp('feFinalizadaHasta').value = "";
    Ext.getCmp('feFinalizadaHasta').setRawValue("");

    Ext.getCmp('cmbEmpresaBusqueda').value = "";
    Ext.getCmp('cmbEmpresaBusqueda').setRawValue("");

    Ext.getCmp('cmb_departamento').value = "";
    Ext.getCmp('cmb_departamento').setRawValue("");

    Ext.getCmp('cmb_cuadrillas').value = "";
    Ext.getCmp('cmb_cuadrillas').setRawValue("");

    Ext.getCmp('comboCiudadDestino').value = "";
    Ext.getCmp('comboCiudadDestino').setRawValue("");

    Ext.getCmp('comboCiudadOrigen').value = "";
    Ext.getCmp('comboCiudadOrigen').setRawValue("");

    Ext.getCmp('cmb_departamentoOrigen').value = "";
    Ext.getCmp('cmb_departamentoOrigen').setRawValue("");

    Ext.getCmp('cmb_listaProcesos').value = "";
    Ext.getCmp('cmb_listaProcesos').setRawValue("");

    Ext.getCmp('txtTareaPadre').value = "";
    Ext.getCmp('txtTareaPadre').setRawValue("");

    Ext.getCmp('cmb_cuadrillas').setDisabled(false);
    Ext.getCmp('cmb_departamento').setDisabled(true);
    Ext.getCmp('cmbEmpresaBusqueda').setDisabled(false);
    storeSeleccionCuadrillas.removeAll();
    Ext.getCmp('btnVerSeleccionCuadrillas').setDisabled(false);

    Ext.getCmp('checkboxECUCERT').value = "";
    Ext.getCmp('checkboxECUCERT').setRawValue("");

    store.removeAll();
    store.getProxy().extraParams.cliente = "";
    store.getProxy().extraParams.departamento = "";
    store.getProxy().extraParams.tarea = "";
    store.getProxy().extraParams.asignado = "";
    store.getProxy().extraParams.numeroActividad = "";
    store.getProxy().extraParams.numeroCaso = "";
    store.getProxy().extraParams.estado = "Todos";
    store.getProxy().extraParams.feSolicitadaDesde = "";
    store.getProxy().extraParams.feSolicitadaHasta = "";
    store.getProxy().extraParams.feFinalizadaDesde = "";
    store.getProxy().extraParams.feFinalizadaHasta = "";
    store.getProxy().extraParams.cuadrilla = "";
    store.getProxy().extraParams.departamentoOrig = "";
    store.getProxy().extraParams.ciudadOrigen = "";
    store.getProxy().extraParams.ciudadDestino = "";
    store.getProxy().extraParams.numeroTareaPadre = "";
    store.getProxy().extraParams.verTareasEcucert  = "";

    grid.getStore().removeAll();  
}



/************************************************************************ */
/*********************** REPROGRAMAR TAREA ******************************** */
/************************************************************************ */
var winReprogramarTarea;

/**
* 
* reprogramarTarea
*
* @version 1.0 - No se encontro historial de versiones
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 28-09-2019 - Se agrega la variable nombre tarea, nombre proceso, departamento
*                           para ser procesada y enviada a Syscloud.
* @since 1.0
*
*/
function reprogramarTarea(id_detalle, data , fechaActual , horaActual){
    
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
     
    if(data.id_caso==0)visibleMotivo = true;else visibleMotivo=false;
    
     var storeMotivos = Ext.create('Ext.data.Store', {
      
		fields: ['opcion', 'valor'],
		data: 
		[{
		    "opcion": "Cliente Solicita Reprogramar",
		    "valor": "C"
		    }, {
		    "opcion": "Tecnico Solicita Reprogramar",
		    "valor": "T"
		    }		   
		]
 	    });
    btnguardar2 = Ext.create('Ext.Button', {
		text: 'Guardar',
		cls: 'x-btn-rigth',
		handler: function() {		
			var valorBool = true;//validarTareasMateriales();
			if(valorBool)
			{
				if( Ext.getCmp('comboMotivo').value==null && !visibleMotivo)
				  Ext.Msg.alert('Alerta ',"Debe elegir un motivo");
				else{
                    var fechaEjecucion   = Ext.getCmp('fe_ejecucion_value').value;
                    var horaEjecucion    = Ext.getCmp('ho_ejecucion_value').value;
                    var observacion      = Ext.getCmp('observacion').value;
                    var comboMotivo      = Ext.getCmp('comboMotivo').value;
                    winReprogramarTarea.destroy();
					conn.request({
						method: 'POST',
						params :{
							id_detalle       : id_detalle,
							fe_ejecucion     : fechaEjecucion,
							ho_ejecucion     : horaEjecucion,
							observacion      : observacion,
							motivo           : visibleMotivo ? "" : comboMotivo,
                            intIdDetalleHist : data.intIdDetalleHist,
                            numeroTarea: data.numero_tarea,
                            nombre_tarea: data.nombre_tarea,
                            nombre_proceso: data.nombre_proceso,
                            asignado_nombre: data.ref_asignado_nombre,
                            departamento_nombre: data.asignado_nombre
						},
						url: 'reprogramarTarea',
						success: function(response){
							var json = Ext.JSON.decode(response.responseText);

                            if (!json.success && !json.seguirAccion) {
                                Ext.MessageBox.show({
                                    closable   :  false  , multiline : false,
                                    title      : 'Alerta', msg : json.mensaje,
                                    buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                    buttonText : {ok: 'Cerrar'},
                                    fn : function (button) {
                                        if(button === 'ok') {
                                            store.load();
                                        }
                                    }
                                });
                                return;
                            }

                            if(json.success) {
                                if(json.mensaje != "cerrada"){
                                    Ext.Msg.alert('Mensaje','Se reprogramó la tarea.', function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                        }
                                    });
                                } else {
                                    Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                                }
							} else {
                                Ext.Msg.alert('Alerta ',json.mensaje);
							}
						},
						failure: function(rec, op) {
							var json = Ext.JSON.decode(op.response.responseText);
							Ext.Msg.alert('Alerta ',json.mensaje);
						}
					});
				}
			}	
		}
    });
    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winReprogramarTarea.destroy();
		}
    });
    
    formPanel2 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 280,
		width: 500,
		layout: 'fit',
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				title: 'Información',
				defaultType: 'textfield',
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'tareaCaso',
						name: 'tareaCaso',
						value: data.nombre_tarea
					},
					{                                                        
						xtype: 'datefield',
						fieldLabel: 'Fecha de Ejecucion:',
						id: 'fe_ejecucion_value',
						name:'fe_ejecucion_value',
						editable: false,
						format: 'Y-m-d',
						value:fechaActual,
						minValue: fechaActual
					},
					{
						xtype: 'timefield',
						fieldLabel: 'Hora de Ejecucion:',
						format: 'H:i',
						id: 'ho_ejecucion_value',
						name: 'ho_ejecucion_value',
						minValue: '00:01',
						maxValue: '23:59',
						increment: 1,
						editable: true,
						value:horaActual
					},	
					{
						xtype: 'combobox',
						fieldLabel: 'Motivo:',
						id: 'comboMotivo',
						name: 'comboMotivo',
						store: storeMotivos,
						displayField: 'opcion',
						valueField: 'valor',
						queryMode: "remote",
						emptyText: '',
						hidden:visibleMotivo
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'observacion',
						name: 'observacion',
						rows: 5,
						cols: 160
					}
				]
			}
		]
	 });  
	
    winReprogramarTarea = Ext.create('Ext.window.Window', {
		title: 'Reprogramar Tarea',
		modal: true,
		width: 500,
		height: 350,
		resizable: false,
		layout: 'fit',
		items: [formPanel2],
		buttonAlign: 'center',
		buttons:[btnguardar2,btncancelar2]
    }).show(); 
}


/************************************************************************ */
/*********************** CANCELAR TAREA ******************************** */
/************************************************************************ */
var winCancelarTarea;

/**
* 
* cancelarTarea
*
* @version 1.0 - No se encontro historial de versiones
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 28-09-2019 - Se agrega la variable nombre tarea, nombre proceso, departamento
*                           para ser procesada y enviada a Syscloud.
* @since 1.0
*
*/
function cancelarTarea(id_detalle, data , tipo)
{
    if(data.cerrarTarea == "S")
    {
        var conn = new Ext.data.Connection
        ({
            listeners: 
            {
                'beforerequest':
                {
                    fn: function(con, opt) 
                    {
                        Ext.get(document.body).mask('Loading...');
                    },
                    scope: this
                },
                'requestcomplete': 
                {
                    fn: function(con, res, opt)
                    {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                },
                'requestexception': 
                {
                    fn: function(con, res, opt) 
                    {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                }
            }
        });

        btnguardar2 = Ext.create('Ext.Button', 
        {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function()
            {
                    var CanObservacion = Ext.getCmp('observacion').value;

                    winCancelarTarea.destroy();

                    Ext.MessageBox.wait("Guardando datos...");

                    conn.request
                    ({
                        method: 'POST',
                        params:
                        {
                            'id_detalle'       : id_detalle,
                            'observacion'      : CanObservacion,
                            'tipo'             : tipo,
                            'id_caso'          : data.id_caso,
                            'nombreTarea'      : data.nombre_tarea,
                            'intIdDetalleHist' : data.intIdDetalleHist,
                            numeroTarea        : data.numero_tarea,
                            nombre_proceso     : data.nombre_proceso,
                            asignado_nombre    : data.ref_asignado_nombre,
                            departamento_nombre: data.asignado_nombre
                        },
                        url: strUrlCancelarTarea,
                        success: function(response)
                        {
                            Ext.MessageBox.hide();
                            var json = Ext.JSON.decode(response.responseText);

                            if (!json.success && !json.seguirAccion) {
                                Ext.MessageBox.show({
                                    closable   :  false  , multiline : false,
                                    title      : 'Alerta', msg : json.mensaje,
                                    buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                    buttonText : {ok: 'Cerrar'},
                                    fn : function (button) {
                                        if(button === 'ok') {
                                            store.load();
                                        }
                                    }
                                });
                                return;
                            }

                            if (json.success)
                            {
                                if(json.mensaje != "cerrada")
                                {
                                    winCancelarTarea.destroy();

                                    if (tipo == 'rechazada')
                                    {
                                        mensaje = 'Se rechazo la tarea.';
                                    }
                                    else if(tipo == 'anulada')
                                    {
                                        mensaje = 'Se anuló la tarea.';
                                    }
                                    else
                                    {
                                        mensaje = 'Se cancelo la tarea.';
                                    }

                                    Ext.Msg.alert('Mensaje', mensaje, function(btn)
                                    {
                                        if (btn == 'ok')
                                        {
                                            store.load();

                                            //Cuando se cancela la tarea y esta pertenece a un caso
                                            if (tipo !== 'rechazada' && data.id_caso !== 0 && json.tareasAbiertas === 0 && !data.casoPerteneceTN)
                                            {
                                                esCancelada = true;
                                                obtenerDatosCasosCierre(data.id_caso, conn, esCancelada);
                                            }
                                        }
                                    });
                                }
                                else
                                {
                                        Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Alerta ', json.mensaje);
                            }
                        },
                        failure: function(rec, op)
                        {
                            Ext.MessageBox.hide();

                            var json = Ext.JSON.decode(op.response.responseText);
                            Ext.Msg.alert('Alerta ', json.mensaje);
                        }
                    });
            }
        });

        btncancelar2 = Ext.create('Ext.Button',
        {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function()
            {
                winCancelarTarea.destroy();
            }
        });

        formPanel2 = Ext.create('Ext.form.Panel', 
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 250,
            width: 500,
            layout: 'fit',
            fieldDefaults:
            {
                labelAlign: 'left',
                msgTarget: 'side'
            },
            items: 
            [
                {
                    xtype: 'fieldset',
                    title: 'Información',
                    defaultType: 'textfield',
                    items: 
                    [
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Tarea:',
                            id: 'tareaCaso',
                            name: 'tareaCaso',
                            value: data.nombre_tarea
                        },
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Obsevacion:',
                            id: 'observacion',
                            name: 'observacion',
                            maxLength: 450,
                            enforceMaxLength:true,
                            rows: 5,
                            cols: 160
                        }
                    ]
                }
            ]
        });  

        winCancelarTarea = Ext.create('Ext.window.Window',
        {
            title: tipo=='cancelada'? "Cancelar": (tipo=='anulada') ? "Anular" : "Rechazar",
            modal: true,
            width: 500,
            height: 280,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
        }).show();
        
    }
    else
    {
       Ext.Msg.alert('Alerta ', "Esta tarea no se puede cancelar / rechazar debido que posee una o más subtareas asociadas, por favor \n\
                                cerrar las tareas asociadas a la tarea principal.");
    }
}

/************************************************************************ */
/*********************** INDISPONIBILIDAD ******************************** */
/************************************************************************ */

var winIndisponibilidadTarea;

function verIndisponibilidadTarea(data)
{

    actualizarTiempoAfectacion = Ext.create('Ext.Button', {
        id          : 'actualizarTiempoAfectacion',
        text        : '<i class="fa fa-refresh" aria-hidden="true"></i>',
        tooltip     : 'Actualizar tiempo afectacion',
        tooltipType : 'title',
        style       : 'position: absolute; margin: -8% 0% 0% 50%;',
        hidden       : true,
        handler: function() {
            obtenerTiempoAfectacionIndisponibilidadTarea(data);
        }
    });


    /*** combo olt multiple ***/
    storeOltTarea = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getElementosPorTipo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombreElemento',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idElemento', mapping: 'idElemento'},
            {name: 'nombreElemento', mapping: 'nombreElemento'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount1Tarea', {
        alias: 'plugin.selectedCount1Tarea',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    var acumuladorValue = '';
                    var acumuladorDescripcion = '';

                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;

                        

                        // acumula lo seleccionado en label aparte, para poder dejar el combo libre para 
                        // realizar busquedas
                        //anteriorValue = Ext.getCmp('oltValue').getValue();
                        acumuladorDescripcion = Ext.getCmp('oltSeleccionadosTarea').getValue();

                        if (combo.valueModels != null){
                            
                            if (combo.valueModels.length > 0){

                                if (acumuladorDescripcion != ''){
                                    //anteriorValue = anteriorValue + ',';
                                    acumuladorDescripcion = acumuladorDescripcion + ', ';
                                }

                                // si no existe lo agrego
                                if(acumuladorDescripcion.indexOf(combo.rawValue) == -1){

                                    //Ext.getCmp('oltValue').setValue(anteriorValue + combo.valueModels[0].data.idElemento);
                                    Ext.getCmp('oltSeleccionadosTarea').setValue(acumuladorDescripcion + combo.rawValue);

                                }
                                
                            }

                        }

                        Ext.getCmp('comboOltTarea').value = "";
                        Ext.getCmp('comboOltTarea').setRawValue("");
                    }
                }
            })
        }
    });

    comboOltTarea = Ext.create('Ext.form.ComboBox', {
        id           : 'comboOltTarea',
        store        :  storeOltTarea,
        displayField : 'nombreElemento',
        valueField   : 'idElemento',
        fieldLabel   : 'Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount1Tarea'],
        disabled     : false,
        editable     : true,
        multiSelect  : false,
        hidden       : true/*,
        displayTpl   : '<tpl for="."> {nombreElemento} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig   : {
            itemTpl: '{nombreElemento} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                

            }
        }*/
    });
    /*** combo olt multiple ***/

    /*** combo puerto multiple ***/
    storePuertoTarea = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getInterfacesPorElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        sorters: [{
             property : 'idInterface',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idInterface', mapping: 'idInterface'},
            {name: 'nombreInterface', mapping: 'nombreInterface'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount2Tarea', {
        alias: 'plugin.selectedCount2Tarea',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboPuertoTarea = Ext.create('Ext.form.ComboBox', {
        id           : 'comboPuertoTarea',
        store        :  storePuertoTarea,
        displayField : 'idInterface',
        valueField   : 'nombreInterface',
        fieldLabel   : 'Puerto Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount2Tarea'],
        disabled     : false,
        editable     : true,
        multiSelect  : true,
        hidden       : true,
        displayTpl   : '<tpl for=".">{nombreInterface}<tpl if="xindex < xcount">,</tpl> </tpl>',
        listConfig   : {
            itemTpl: '{nombreInterface} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                var acumulador = '';

                if(combo.valueModels != null){

                    nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();

                    if(nombreOlt.indexOf(",") == -1){

                        for (var i = 0; i<combo.valueModels.length; i++){

                            for (var j = 0; j<storePuertoTarea.data.items.length; j++){

                                if (combo.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                    
                                    if (acumulador == ''){
                                        acumulador = combo.valueModels[i].data.idInterface;
                                    }else{
                                        acumulador = acumulador + ', ' + combo.valueModels[i].data.idInterface;
                                    }
                                    break;
        
                                }

                            }

                        }

                        nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
                        storeCajaTarea.proxy.extraParams = {nombreOlt: nombreOlt, idPuerto: acumulador};
                        storeCajaTarea.load({params: {}});

                        obtenerClientesAfectadosIndisponibilidadTarea(acumulador, '', '');
                    }

                } 
                
            }
        }
    });
    /*** combo puerto multiple ***/

    /*** combo caja multiple ***/
    storeCajaTarea = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getElementosContenedoresPorPuerto,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombreCaja',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idCaja', mapping: 'idCaja'},
            {name: 'nombreCaja', mapping: 'nombreCaja'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount3Tarea', {
        alias: 'plugin.selectedCount3Tarea',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboCajaTarea = Ext.create('Ext.form.ComboBox', {
        id           : 'comboCajaTarea',
        store        :  storeCajaTarea,
        displayField : 'idCaja',
        valueField   : 'nombreCaja',
        fieldLabel   : 'Caja Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount3Tarea'],
        disabled     : false,
        editable     : true,
        multiSelect  : true,
        hidden       : true,
        displayTpl   : '<tpl for=".">{nombreCaja}<tpl if="xindex < xcount">,</tpl></tpl>',
        listConfig   : {
            itemTpl: '{nombreCaja} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                var acumuladorPuerto = '';  
                var acumuladorCaja = '';  
                var i;
                var j;

                if(comboPuertoTarea.valueModels != null){

                    if(comboPuertoTarea.getRawValue() != 'NO APLICA'){
                    
                        for (i = 0; i<comboPuertoTarea.valueModels.length; i++){

                            for (j = 0; j<storePuertoTarea.data.items.length; j++){

                                if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                    
                                    if (acumuladorPuerto == ''){
                                        acumuladorPuerto = comboPuertoTarea.valueModels[i].data.idInterface;
                                    }else{
                                        acumuladorPuerto = acumuladorPuerto + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                                    }
                                    break;
        
                                }

                            }

                        }



                        if(comboCajaTarea.valueModels != null){
                        
                            for (i = 0; i<comboCajaTarea.valueModels.length; i++){
        
                                for (j = 0; j<storeCajaTarea.data.items.length; j++){
        
                                    if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                                        
                                        if (acumuladorCaja == ''){
                                            acumuladorCaja = comboCajaTarea.valueModels[i].data.idCaja;
                                        }else{
                                            acumuladorCaja = acumuladorCaja + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                                        }
                                        break;
            
                                    }
        
                                }
        
                            }
                        }

                        nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
                        storeSplitterTarea.proxy.extraParams = {nombreOlt: nombreOlt, idPuerto: acumuladorPuerto, idCaja: acumuladorCaja};
                        storeSplitterTarea.load({params: {}});

                        obtenerClientesAfectadosIndisponibilidadTarea(acumuladorPuerto, acumuladorCaja, '');

                    }
                } 
                
            }
        }
    });
    /*** combo caja multiple ***/
    
    /*** combo splitter multiple ***/
    storeSplitterTarea = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getElementosConectorPorElementoContenedor,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombreSplitter',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idSplitter', mapping: 'idSplitter'},
            {name: 'nombreSplitter', mapping: 'nombreSplitter'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount4Tarea', {
        alias: 'plugin.selectedCount4Tarea',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboSplitterTarea = Ext.create('Ext.form.ComboBox', {
        id           : 'comboSplitterTarea',
        store        :  storeSplitterTarea,
        displayField : 'idSplitter',
        valueField   : 'nombreSplitter',
        fieldLabel   : 'Splitter Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount4Tarea'],
        disabled     : false,
        editable     : true,
        multiSelect  : true,
        hidden       : true,
        displayTpl   : '<tpl for="."> {nombreSplitter} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig   : {
            itemTpl: '{nombreSplitter} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                var acumuladorSplitter = '';
                var acumuladorPuerto = '';  
                var acumuladorCaja = ''; 
                var i;
                var j;

                if(combo.valueModels != null){
                    
                    for (i = 0; i<combo.valueModels.length; i++){

                        for (j = 0; j<storeSplitterTarea.data.items.length; j++){

                            if (combo.valueModels[i].data.idSplitter == storeSplitterTarea.data.items[j].data.idSplitter){
                                
                                if (acumuladorSplitter == ''){
                                    acumuladorSplitter = combo.valueModels[i].data.idSplitter;
                                }else{
                                    acumuladorSplitter = acumuladorSplitter + ', ' + combo.valueModels[i].data.idSplitter;
                                }
                                break;
    
                            }

                        }

                    }


                    for (i = 0; i<comboPuertoTarea.valueModels.length; i++){

                        for (j = 0; j<storePuertoTarea.data.items.length; j++){

                            if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                
                                if (acumuladorPuerto == ''){
                                    acumuladorPuerto = comboPuertoTarea.valueModels[i].data.idInterface;
                                }else{
                                    acumuladorPuerto = acumuladorPuerto + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                                }
                                break;
    
                            }

                        }

                    }



                    if(comboCajaTarea.valueModels != null){
                    
                        for (i = 0; i<comboCajaTarea.valueModels.length; i++){
    
                            for (j = 0; j<storeCajaTarea.data.items.length; j++){
    
                                if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                                    
                                    if (acumuladorCaja == ''){
                                        acumuladorCaja = comboCajaTarea.valueModels[i].data.idCaja;
                                    }else{
                                        acumuladorCaja = acumuladorCaja + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                                    }
                                    break;
        
                                }
    
                            }
    
                        }
                    }

                    obtenerClientesAfectadosIndisponibilidadTarea(acumuladorPuerto, acumuladorCaja, acumuladorSplitter);

                } 
                
            }
        }
    });
    /*** combo splitter multiple ***/

    /*** combo responsable ***/
    comboResponsableStoreTarea = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        limit:1000,
        proxy: {
        type: 'ajax',
            url : url_empresaIndisponibilidadTarea,
            reader:
        {
        type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        }
        },
        fields:
        [
        {name:'codigo', mapping:'valor1'},
        {name:'descripcion', mapping:'valor2'}
        ]
    });
        
    comboResponsableTarea = Ext.create('Ext.form.ComboBox', {
        id:'comboResponsableTarea',
        store: comboResponsableStoreTarea,
        displayField: 'descripcion',
        valueField: 'codigo',
        height:30,
        width:200,
        border:0,
        margin:0,
        fieldLabel: 'Responsable del problema',
        queryMode: "remote",
        emptyText: '',
        hidden: true,
        editable: false
    });
    /*** combo responsable ***/


    /*** combo tarea ***/
    comboTareaStore2 = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        limit:1000,
        proxy: {
            type: 'ajax',
            url : url_gridTarea,
            reader:
        {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        },
            extraParams:
        {
            nombre: '',
            estado: 'Activo',
            visible: 'SI',
            caso:data.id_caso,
            detalle:data.idDetalle //Se añade el id detalle para validacion de empresa
        }
        },
        fields:
        [
        {name:'id_tarea', mapping:'id_tarea'},
        {name:'nombre_tarea', mapping:'nombre_tarea'}
        ]
    });
        
    comboTarea2 = Ext.create('Ext.form.ComboBox', {
        id:'comboTarea2',
        store: comboTareaStore2,
        displayField: 'nombre_tarea',
        valueField: 'id_tarea',
        height:30,
        width:390,
        border:0,
        margin:0,
        fieldLabel: 'Tarea Inicial',
        queryMode: "remote",
        emptyText: '',
        editable: true
    });
    Ext.getCmp('comboTarea2').setRawValue(data.nombre_tarea);
    /*** combo tarea ***/

    /*** combo indisponibilidad ***/   
    comboIndisponibilidadStoreTarea = Ext.create('Ext.data.Store', {
        fields: ['codigo', 'descripcion'],
        data : [
            {"codigo":"NO", "descripcion":"NO"},
            {"codigo":"SI", "descripcion":"SI"}
        ]
    });
    
    comboIndisponibilidadTarea = Ext.create('Ext.form.ComboBox', {
        id:'comboIndisponibilidadTarea',
        store: comboIndisponibilidadStoreTarea,
        displayField: 'descripcion',
        valueField: 'codigo',
        height:30,
        width:200,
        border:0,
        margin:0,
        fieldLabel: 'Indisponibilidad',
        queryMode: "remote",
        emptyText: '',
        editable: false,
        listeners: {
            select: function(combo, records, eOpts) {
                
                var cmbIndisponibilidad = records[0].get('descripcion');
                
                if (cmbIndisponibilidad == 'SI'){
                    setVisibleIndisponibilidadTarea(true);
                }else{
                    setVisibleIndisponibilidadTarea(false);
                }
            }
        }
    });
    Ext.getCmp('comboIndisponibilidadTarea').value = "NO";
    Ext.getCmp('comboIndisponibilidadTarea').setRawValue('NO');
    /*** combo indisponibilidad ***/   

    /*** combo masivo ***/   
    comboMasivoStoreTarea = Ext.create('Ext.data.Store', {
        fields: ['codigo', 'descripcion'],
        data : [
            {"codigo":"SI", "descripcion":"SI"},
            {"codigo":"NO", "descripcion":"NO"}
        ]
    });
    
    comboMasivoTarea = Ext.create('Ext.form.ComboBox', {
        id:'comboMasivoTarea',
        store: comboMasivoStoreTarea,
        displayField: 'descripcion',
        valueField: 'codigo',
        height:30,
        width:200,
        border:0,
        margin:0,
        fieldLabel: 'Masivo',
        //labelStyle: 'width:600px',
        queryMode: "remote",
        emptyText: '',
        hidden: true,
        editable: false,
        listeners: {
            select: function(combo, records, eOpts) {
                
                var cmbVisible = records[0].get('descripcion');
                
                if (cmbVisible == 'SI'){
                    setVisibleMasivoTarea(true);
                }else{
                    setVisibleMasivoTarea(false);
                }
            }
        }
    });
    Ext.getCmp('comboMasivoTarea').value = "NO";
    Ext.getCmp('comboMasivoTarea').setRawValue('NO');
    /*** combo masivo ***/


    btnCerrarTarea = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            Ext.getCmp('winIndisponibilidadTarea').setVisible(false);
        }
    });

    btnLimpiarTarea = Ext.create('Ext.Button', {
        text: 'Limpiar',
        cls: 'x-btn-rigth',
        handler: function() {

            Ext.getCmp('oltSeleccionadosTarea').setValue('');
            
            storePuertoTarea.removeAll();
            storePuertoTarea.proxy.extraParams = {};
            storePuertoTarea.load();
            comboPuertoTarea.setValue('');

            storeCajaTarea.removeAll();
            storeCajaTarea.proxy.extraParams = {};
            storeCajaTarea.load();
            comboCajaTarea.setValue('');

            storeSplitterTarea.removeAll();
            storeSplitterTarea.proxy.extraParams = {};
            storeSplitterTarea.load();
            comboSplitterTarea.setValue('');

        }
    });

    formPanelIndisponibilidadTarea = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: 530,
        width: 440,
        layout: 'fit',
        fieldDefaults: {
        labelAlign: 'left',
            msgTarget: 'side'
        },
        items:
        [
        {
            xtype: 'fieldset',
            title: 'Información',
            defaultType: 'textfield',
            items:
            [
                comboIndisponibilidadTarea,
                {
                    xtype: 'numberfield',
                    fieldLabel: 'Tiempo de afectación:',
                    id: 'tiempoAfectacionTarea',
                    name: 'tiempoAfectacionTarea',
                    hidden: true,
                    width: 200,
                    minValue: 0,
                    allowNegative: false,
                    allowPureDecimal: true
                    //value: data.fechaEjecucion
                },
                actualizarTiempoAfectacion,
                comboMasivoTarea,
                comboOltTarea,
                {
                    xtype: 'textfield',
                    fieldLabel: '',
                    id: 'oltSeleccionadosTarea',
                    name: 'oltSeleccionadosTarea',
                    hidden: true,
                    disabled: true,
                    width: 390,
                    listeners : {
                        change : function (txt, newValue,oldValue){
                            
                            nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
                            storePuertoTarea.removeAll();
                            storeCajaTarea.removeAll();
                            storeSplitterTarea.removeAll();

                            // activa/desactiva combos puerto, caja, splitter
                            validarSeleccionOltTarea(nombreOlt, storePuertoTarea);
                        }
                    }
                },
                /*{
                    xtype: 'textfield',
                    fieldLabel: 'Olt Value',
                    id: 'oltValue',
                    name: 'oltValue',
                    hidden: true,
                    width: 390
                    
                },*/
                comboPuertoTarea,
                comboCajaTarea,
                comboSplitterTarea,
                {
                    xtype: 'numberfield',
                    fieldLabel: 'Clientes afectados',
                    id: 'clientesAfectadosTarea',
                    name: 'clientesAfectadosTarea',
                    hidden: true,
                    width: 200,
                    minValue: 0,
                    allowNegative: false,
                    allowPureDecimal: true
                    //value:data.duracionMinutos
                },
                comboResponsableTarea,
                {
                    xtype: 'textarea',
                    fieldLabel: 'Observaciones:',
                    id: 'observacionesTarea',
                    name: 'observacionesTarea',
                    maxLength: 500,
                    enforceMaxLength: true,
                    enableKeyEvents: true,
                    rows: 5,
                    cols: 160,
                    hidden: true
                }
            ]
        }
        ]
    });
    
    winIndisponibilidadTarea = Ext.create('Ext.window.Window', {
        id: 'winIndisponibilidadTarea',
        title: 'Indisponibilidad',
        modal: true,
        width: 440,
        height: 530,
        resizable: true,
        layout: 'fit',
        items: [formPanelIndisponibilidadTarea],
        buttonAlign: 'center',
        buttons:[btnLimpiarTarea, btnCerrarTarea],
        closable: false
    }).show();

    Ext.getCmp('winIndisponibilidadTarea').setVisible(false);

}

function setVisibleIndisponibilidadTarea(boolean){

    Ext.getCmp('tiempoAfectacionTarea').setVisible(boolean);
    Ext.getCmp('comboMasivoTarea').setVisible(boolean);
    Ext.getCmp('actualizarTiempoAfectacion').setVisible(boolean);

    if (!boolean){
        setVisibleMasivoTarea(boolean);
        Ext.getCmp('comboMotivoFinalizaTarea').value = "NO";
        Ext.getCmp('comboMasivoTarea').setRawValue("NO");
    }

    /*Ext.getCmp('comboMotivoFinaliza').value = "";
    Ext.getCmp('comboMotivoFinaliza').setRawValue("");
    Ext.getCmp('comboMotivoFinaliza').reset();*/
}

function setVisibleMasivoTarea(boolean){

    Ext.getCmp('clientesAfectadosTarea').setVisible(boolean);
    Ext.getCmp('comboResponsableTarea').setVisible(boolean);
    Ext.getCmp('observacionesTarea').setVisible(boolean);
    Ext.getCmp('comboOltTarea').setVisible(boolean);
    //Ext.getCmp('oltValue').setVisible(boolean);
    Ext.getCmp('oltSeleccionadosTarea').setVisible(boolean);
    Ext.getCmp('comboPuertoTarea').setVisible(boolean);
    Ext.getCmp('comboCajaTarea').setVisible(boolean);
    Ext.getCmp('comboSplitterTarea').setVisible(boolean);
}



/************************************************************************ */
/*********************** FINALIZAR TAREA ******************************** */
/************************************************************************ */
var winFinalizarTarea;


/**
* 
* finalizarTarea
*
* @version 1.0 - No se encontro historial de versiones
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 25-09-2019 - Se agrega la variable nombre tarea, nombre proceso, departamento
*                           para ser procesada y enviada a Syscloud.
* @since 1.0
*
*/

function finalizarTarea(id_detalle, data, fechaActual, horaActual,tipoAsignado, idCuadrilla)
{
    if(data.cerrarTarea == "S")
    {
        if(data.iniciadaDesdeMobil == "S")
        {
            isCuadrilla = false;
                comboTareaStore = new Ext.data.Store({
                pageSize: 200,
                    total: 'total',
                    limit:1000,
                    proxy: {
                    type: 'ajax',
                        url : url_gridTarea,
                        reader:
                    {
                    type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                        extraParams:
                    {
                    nombre: '',
                        estado: 'Activo',
                        visible: 'SI',
                        caso:data.id_caso,
                        detalle:id_detalle //Se añade el id detalle para validacion de empresa
                    }
                    },
                    fields:
                    [
                    {name:'id_tarea', mapping:'id_tarea'},
                    {name:'nombre_tarea', mapping:'nombre_tarea'}
                    ]
                });
                
                comboTarea = Ext.create('Ext.form.ComboBox', {
                id:'comboTarea',
                    store: comboTareaStore,
                    displayField: 'nombre_tarea',
                    valueField: 'id_tarea',
                    height:30,
                    width:450,
                    border:0,
                    margin:0,
                    fieldLabel: 'Tarea Final ',
                    queryMode: "remote",
                    emptyText: ''
                });
                comboMotivoFinalizaStore = new Ext.data.Store({
                    pageSize: 200,
                        total: 'total',
                        limit:1000,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                                url : url_gridMotivosCategoriaTareas,
                                reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'data'
                            },
                            extraParams: {
                                nombre: '',
                                estado: 'Activo',
                                visible: 'SI',
                                caso:data.id_caso,
                                detalle:id_detalle //Se añade el id detalle para validacion de empresa
                            }
                        },
                        fields:
                        [
                            {name:'idMotivo', mapping:'idMotivo'},
                            {name:'nombreMotivo', mapping:'nombreMotivo'}
                        ],
                        listeners: {
                            load: function(store) { 
                                if (store.data.length > 0)
                                {
                                    Ext.getCmp('comboMotivoFinaliza').value = "";
                                    Ext.getCmp('comboMotivoFinaliza').setRawValue("");
                                    Ext.getCmp('comboMotivoFinaliza').reset();
                                    Ext.getCmp('comboMotivoFinaliza').setVisible(true);
                                }
                                else
                                {
                                    Ext.getCmp('comboMotivoFinaliza').value = "";
                                    Ext.getCmp('comboMotivoFinaliza').setRawValue("");
                                    Ext.getCmp('comboMotivoFinaliza').reset();
                                    Ext.getCmp('comboMotivoFinaliza').setVisible(false);
                                }
                            }
                        }
                });
                comboMotivoFinaliza = Ext.create('Ext.form.ComboBox', {
                    id:'comboMotivoFinaliza',
                        store: comboMotivoFinalizaStore,
                        disabled: true,
                        displayField: 'nombreMotivo',
                        valueField: 'idMotivo',
                        height:30,
                        width:450,
                        border:0,
                        margin:0,
                        fieldLabel: 'Motivo ',
                        queryMode: "remote",
                        emptyText: ''
                    });
                gridCuadrilla = null;
                Ext.getCmp('comboTarea').setRawValue(data.nombre_tarea);
                //data['intFinTareaId'] = data.id_tarea;
                
                var strRequiereMaterial     = '';
                var strRequiereFibra        = '';
                var strRequiereRutaFibra    = '';
                data['strFinSelectNombre']  = '';
                
                if(data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true)))
                {
                    var arrayMenu1              = [];
                    
                    for(var i = 0; i < finesTarea.length; i++)
                    {
                        var categoriaPadre          = finesTarea[i];
                        var nombrePadre             = categoriaPadre.nombreCategoria;
                        var hijosPadre              = categoriaPadre.hijosCategoria;        
                        var arrayMenu2              = [];
                            
                            for(var j = 0; j < hijosPadre.length; j++)
                            {
                                var categoriaHijo           = hijosPadre[j];
                                var nombreHijo              = categoriaHijo.nombreHijo;
                                var listaTareas             = categoriaHijo.listaTareas;    
                                var arrayMenuTres           = [];
                                    
                                    for(var k = 0; k < listaTareas.length; k++)
                                    {
                                        var tarea = listaTareas[k];
                                        var numeroTarea         = tarea.numeroTarea;
                                        var nombreTarea         = tarea.nombreTarea;
                                        var nombreNivel2        = nombreHijo;
                                        var nombreNivel1        = nombrePadre;
                                        strRequiereMaterial     = tarea.requiereMaterial;
                                        strRequiereFibra        = tarea.requiereFibra;
                                        strRequiereRutaFibra    = tarea.requiereRutaFibra;
                                        var objFinLTres = 
                                        {
                                            text: nombreTarea,
                                            requiereMaterial:   strRequiereMaterial,
                                            requiereFibra:      strRequiereFibra,
                                            requiereRutaFibra:  strRequiereRutaFibra,
                                            numeroTarea:        numeroTarea,
                                            nombreNivel2:       nombreNivel2,
                                            nombreNivel1:       nombreNivel1,
                                        };
                                        arrayMenuTres.push(objFinLTres);  
                                    }
                                    
                                var menuLevel3          = Ext.create('Ext.menu.Menu', {
                                    items: arrayMenuTres
                                });
                                
                                var objFinLDos;
                                    if(arrayMenuTres.length > 0)
                                    {
                                        objFinLDos = 
                                        {
                                            text: nombreHijo,
                                            menu:menuLevel3
                                        };    
                                    }
                                    else
                                    {
                                        objFinLDos = {
                                            text: nombreHijo
                                        };    
                                    }

                                menuLevel3.on('click', function(menu, item)
                                {
                                    data['strFinSelectNombre']      = item.text;
                                    data['strRequiereMaterial']     = item.requiereMaterial;
                                    data['strRequiereFibra']        = item.requiereFibra;
                                    data['strRequiereRutaFibra']    = item.requiereRutaFibra;
                                    data['intFinTareaId']           = item.numeroTarea;
                                    Ext.getCmp('splitbutton_fin').setText(data['strFinSelectNombre']);
                                    Ext.getCmp('comboMotivoFinaliza').value = "";
                                    Ext.getCmp('comboMotivoFinaliza').setRawValue("");
                                    Ext.getCmp('comboMotivoFinaliza').reset();
                                    Ext.getCmp('comboMotivoFinaliza').setDisabled(false);

                                    comboMotivoFinalizaStore.proxy.extraParams = {
                                                                                     valor1: item.nombreNivel1, 
                                                                                     valor2: item.nombreNivel2, 
                                                                                     valor3: item.numeroTarea
                                                                                 };
                                    comboMotivoFinalizaStore.load();
                                });

                                arrayMenu2.push(objFinLDos);   
                            }
                            
                            var menuLevel2          = Ext.create('Ext.menu.Menu', {
                                items: arrayMenu2
                            });
                            
                            var objFinLUno;
                            if(arrayMenu2.length > 0)
                            {
                                objFinLUno = {
                                    text: nombrePadre,
                                    menu:menuLevel2
                                };  
                            }
                            else
                            {
                                objFinLUno = 
                                {
                                    text: nombrePadre
                                };  
                            }

                            arrayMenu1.push(objFinLUno);  
                    }

                    var mymenu      = new Ext.menu.Menu({
                                        items: arrayMenu1
                                    });

                     var cmbSeleccion = Ext.create('Ext.SplitButton', {
                        id:'splitbutton_fin',
                        xtype:  'splitbutton',
                        text:   'Seleccionar fin de tarea',
                        name:   'btnSelecFinTarea',
                        width:  270,
                        menu:   mymenu,
                        style: 'margin:0px 0px 10px 0px',
                    });
                    
                }
                
                var cmbMostrarFinTarea;
                var itemTareaInical;
                
                if( data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true))) 
                {
                    cmbMostrarFinTarea  = cmbSeleccion;
                    Ext.getCmp('comboMotivoFinaliza').setVisible(true);
                    itemTareaInical =  new Ext.form.FieldSet({
                        xtype: 'fieldset',
                        title: '',
                        style: 'border: none;padding:0px',
                        bodyStyle: 'padding:0px',
                        layout: {
                            type: 'table',
                            columns: 3,
                            pack: 'center'
                        },
                        items: [
                                {
                                 xtype: 'displayfield',
                                 fieldLabel: 'Tarea Inicial:',
                                 id: 'tareaCaso',
                                 name: 'tareaCaso',
                                 value: data.nombre_tarea
                                }, 
                                {
                                    width: 40,
                                    layout: 'form',
                                    border: false,
                                    items: 
                                    [
                                            {
                                            xtype: 'displayfield'
                                            }
                                    ]
                                },
                                {
                                        xtype: 'checkboxfield',
                                        fieldLabel: 'Hereda Tarea Anterior',
                                        name: 'cbox_tarea_ini',
                                        id: 'cbox_tarea_ini',
                                        checked: false,
                                        labelWidth: 120,
                                        listeners: {
                                            afterrender: function(checkbox) {
                                                   checkbox.getEl().on('click', function() {
                                                    if(Ext.getCmp('cbox_tarea_ini').checked == true)
                                                    {
                                                        Ext.getCmp('splitbutton_fin').setDisabled(true);
                                                        Ext.getCmp('tareaAnterior').show();
                                                    }    
                                                    else
                                                    {
                                                        Ext.getCmp('splitbutton_fin').setDisabled(false);
                                                        Ext.getCmp('tareaAnterior').hide();
                                                    }
                                                 });
                                            }
                                         }
                                }
                            ]
                    });
                }
                else
                {   
                    cmbMostrarFinTarea  = comboTarea;    
                    Ext.getCmp('comboMotivoFinaliza').setVisible(false);
                    itemTareaInical =  new Ext.form.DisplayField({
                        id:'tareaCaso',
                        xtype:  'displayfield',
                        text:   'Tarea Inicial:',
                        fieldLabel: 'Tarea Inicial:',
                        name:   'tareaCaso',
                        value:  data.nombre_tarea
                    });
                }
                    
                if (tipoAsignado === 'CUADRILLA')
            {
            isCuadrilla = true;
                //Grid Asignados de Cuadrilla
                storeCuadrilla = new Ext.data.Store({
                pageSize: 10,
                    total: 'total',
                    proxy: {
                    type: 'ajax',
                        url : url_getMiembrosCuadrilla,
                        reader: {
                        type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams: {
                        idCuadrilla: idCuadrilla,
                        }
                    },
                    fields:
                    [
                    {name: 'id_persona_rol', mapping: 'id_persona_rol'},
                    {name: 'id_persona', mapping: 'id_persona'},
                    {name: 'nombre', mapping: 'nombre'},
                    ],
                    autoLoad: true
                });
                gridCuadrilla = Ext.create('Ext.grid.Panel', {
                width: 450,
                    height: 170,
                    title:'Miembros de Cuadrilla',
                    store: storeCuadrilla,
                    viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
                        loadMask: true,
                        frame: false,
                        columns:
                        [
                        {
                        id: 'id_persona',
                            header: 'id_persona',
                            dataIndex: 'id_persona',
                            hidden: true,
                            hideable: false
                        },
                        {
                        id: 'id_persona_rol',
                            header: 'id_persona_rol',
                            dataIndex: 'id_persona_rol',
                            hidden: true,
                            hideable: false
                        },
                        {
                        id: 'nombre',
                            header: 'Nombre Tecnico',
                            dataIndex: 'nombre',
                            width: 440,
                            sortable: true
                        }
                        ],
                        bbar: Ext.create('Ext.PagingToolbar', {
                        store: storeCuadrilla,
                            displayInfo: true,
                            displayMsg: 'Mostrando {0} - {1} de {2}',
                            emptyMsg: "No hay datos que mostrar."
                        })
                    });
                }

                btnguardar2 = Ext.create('Ext.Button', {
                text: 'Guardar',
                    cls: 'x-btn-rigth',
                    handler: function() {
                            //Obtener si es cuadrilla los empleados relacionados a la misma
                            if (tipoAsignado === 'CUADRILLA')
                            {
                            Ext.Msg.confirm('Confirmación', 'Esta seguro que desea cerrar la tarea con los integrantes de esta cuadrilla ?, caso contrario \n\
                                             notificar para que se actualice los integrantes', function(id) {
                            if (id === 'yes')
                            {
                                guardarTareaFinalizada(data, id_detalle);
                            }
                            else
                            {
                                winIndisponibilidadTarea.destroy();
                                winFinalizarTarea.destroy();

                            }
                            }, this);
                            }
                            else
                            {
                                guardarTareaFinalizada(data, id_detalle);
                            }
                    }
                });
                    btncancelar2 = Ext.create('Ext.Button', {
                    text: 'Cerrar',
                        cls: 'x-btn-rigth',
                        handler: function() {
                            winIndisponibilidadTarea.destroy();
                            winFinalizarTarea.destroy();

                        }
                    });

                    fieldsetCoordenadasTotal     = null;
                    fieldsetCoordenadasManga1    = null;
                    fieldsetCoordenadasManga2    = null;
                    fieldsetCoordenadasIncidente = null;

                     btnCoordenadasIncidente = Ext.create('Ext.button.Button', {
                         iconCls: 'button-grid-Gmaps',
                         itemId: 'ing_coordenadasIncidente',
                         text: ' ',
                         scope: this,
                         handler: function(){ muestraMapa(3);}
                     });
                     btnCoordenadasManga1 = Ext.create('Ext.button.Button', {
                         iconCls: 'button-grid-Gmaps',
                         itemId: 'ing_coordenadasManga1',
                         text: ' ',
                         scope: this,
                         handler: function(){ muestraMapa(1);}
                     });
                     btnCoordenadasManga2 = Ext.create('Ext.button.Button', {
                         iconCls: 'button-grid-Gmaps',
                         itemId: 'ing_coordenadasManga2',
                         text: ' ',
                         scope: this,
                         handler: function(){ muestraMapa(2);}
                     });

                    if (data.perteneceCaso && data.casoPerteneceTN && data.mostrarCoordenadas == "S")
                    {
                        widthCoordenadas = 300;
                        if(data.tareasManga == "S")
                        {
                            widthCoordenadas = "100%";
                            fieldsetCoordenadasManga1 = new Ext.form.FieldSet(
                            {
                                xtype: 'fieldset',
                                title: 'Manga 1',
                                width: 230,
                                items:
                                [
                                    {
                                        layout: 'table',
                                        border: false,
                                        items:
                                        [
                                            {
                                                width: 180,
                                                layout: 'form',
                                                border: false,
                                                items:
                                                [
                                                    {
                                                        xtype: 'displayfield'
                                                    }
                                                ]
                                            },
                                            btnCoordenadasManga1
                                        ]
                                    },
                                    {
                                        width: 230,
                                        layout: 'form',
                                        border: false,
                                        items:
                                        [
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                    xtype: 'textfield',
                                        fieldLabel: '* Longitud:',
                                        maskRe: /[0-9-.]/,
                                        id: 'text_longitudManga1',
                                        name: 'text_longitudManga1',
                                        width: 200,
                                        value: '',
                                        readOnly: false
                                    },
                                    {
                                    xtype: 'textfield',
                                        fieldLabel: '* Latitud:',
                                        maskRe: /[0-9-.]/,
                                        id: 'text_latitudManga1',
                                        name: 'text_latitudManga1',
                                        width: 200,
                                        value: '',
                                        readOnly: false
                                    },

                                ]
                            });

                            fieldsetCoordenadasManga2 = new Ext.form.FieldSet(
                            {
                                xtype: 'fieldset',
                                title: 'Manga 2',
                                width: 230,
                                items:
                                [
                                    {
                                        layout: 'table',
                                        border: false,
                                        items:
                                        [
                                            {
                                                width: 180,
                                                layout: 'form',
                                                border: false,
                                                items:
                                                [
                                                    {
                                                        xtype: 'displayfield'
                                                    }
                                                ]
                                            },
                                            btnCoordenadasManga2
                                        ]
                                    },
                                    {
                                        width: 230,
                                        layout: 'form',
                                        border: false,
                                        items:
                                        [
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                    xtype: 'textfield',
                                        fieldLabel: '* Longitud:',
                                        maskRe: /[0-9-.]/,
                                        id: 'text_longitudManga2',
                                        name: 'text_longitudManga2',
                                        width: 200,
                                        value: '',
                                        readOnly: false
                                    },
                                    {
                                    xtype: 'textfield',
                                        fieldLabel: '* Latitud:',
                                        maskRe: /[0-9-.]/,
                                        id: 'text_latitudManga2',
                                        name: 'text_latitudManga2',
                                        width: 200,
                                        value: '',
                                        readOnly: false
                                    },

                                ]
                            });
                        }

                        fieldsetCoordenadasIncidente = new Ext.form.FieldSet(
                        {
                            xtype: 'fieldset',
                            title: 'Incidente',
                            width: 230,
                            items:
                            [
                                {
                                    layout: 'table',
                                    border: false,
                                    items:
                                    [
                                        {
                                            width: 180,
                                            layout: 'form',
                                            border: false,
                                            items:
                                            [
                                                {
                                                    xtype: 'displayfield'
                                                }
                                            ]
                                        },
                                        btnCoordenadasIncidente
                                    ]
                                },
                                {
                                    width: 230,
                                    layout: 'form',
                                    border: false,
                                    items:
                                    [
                                        {
                                            xtype: 'displayfield'
                                        }
                                    ]
                                },
                                {
                                xtype: 'textfield',
                                    fieldLabel: '* Longitud:',
                                    maskRe: /[0-9-.]/,
                                    id: 'text_longitudI',
                                    name: 'text_longitudI',
                                    width: 200,
                                    value: '',
                                    readOnly: false
                                },
                                {
                                xtype: 'textfield',
                                    fieldLabel: '* Latitud:',
                                    maskRe: /[0-9-.]/,
                                    id: 'text_latitudI',
                                    name: 'text_latitudI',
                                    width: 200,
                                    value: '',
                                    readOnly: false
                                },

                            ]
                        });

                        fieldsetCoordenadasTotal = new Ext.form.FieldSet(
                        {
                            xtype: 'fieldset',
                            title: 'Seleccionar Coordenadas',
                            width: widthCoordenadas,
                            items:
                            [
                                {
                                    layout: 'table',
                                    border: false,
                                    items:
                                    [
                                        fieldsetCoordenadasManga1,
                                        fieldsetCoordenadasManga2,
                                    ]
                                },
                                fieldsetCoordenadasIncidente
                            ]
                        });
                    }
                    if(data.tareasManga == "S")
                    {
                        width = 540;
                    }
                    else
                    {
                        width = 500;
                    }

                    if (data.perteneceCaso && !data.casoPerteneceTN )
                    {
                        height = 540;
                    }
                    else
                    {
                        if(data.mostrarCoordenadas == "S" && data.casoPerteneceTN && data.tareasManga == "S")
                        {
                            height = 890;
                        }
                        else if(data.mostrarCoordenadas == "S" && data.casoPerteneceTN && data.tareasManga == "N")
                        {
                            height = 690;
                        }
                        else
                        {
                            height = 490;
                        }
                    }

                    if (isCuadrilla && data.mostrarCoordenadas == "S" && data.casoPerteneceTN)
                    {
                        if (data.tareasManga == "S")
                        {
                            height = 970;
                        }
                        else
                        {
                            height = 865;
                        }
                    }
                    else if(isCuadrilla)
                    {
                        height = 680;
                    }
                    formPanel2 = Ext.create('Ext.form.Panel', {
                    bodyPadding: 5,
                        waitMsgTarget: true,
                        height: height,
                        width: width,
                        layout: 'fit',
                        fieldDefaults: {
                        labelAlign: 'left',
                            msgTarget: 'side'
                        },
                        items:
                        [
                        {
                        xtype: 'fieldset',
                            title: 'Información',
                            defaultType: 'textfield',
                            items:
                            [
                                itemTareaInical,
                            {
                                id:'tareaAnterior',
                                xtype:  'displayfield',
                                fieldLabel: 'Tarea Final:',
                                text:   'Tarea Final:',
                                name:   'tareaFinalCaso',
                                hidden: true,
                                value:  data.nombreTareaAnterior
                            },
                                cmbMostrarFinTarea,
                                comboMotivoFinaliza,
                            {
                            xtype: 'textarea',
                                fieldLabel: 'Obsevacion:',
                                id: 'observacion',
                                name: 'observacion',
                                maxLength: 500,
                                enforceMaxLength: true,
                                enableKeyEvents: true,
                                rows: 5,
                                cols: 160 
                            },
                            {
                            xtype: 'radiogroup',
                                fieldLabel: 'Es Solucion',
                                columns: 1,
                                vertical: true,
                                hidden: (data.perteneceCaso && !data.casoPerteneceTN) ? false : true,
                                items: [
                                {
                                boxLabel: 'Si',
                                    name: 'esSolucion',
                                    inputValue: 'S',
                                    id: 'radio1',
                                    checked: true
                                }, {
                                boxLabel: 'No',
                                    name: 'esSolucion',
                                    inputValue: 'N',
                                    id: 'radio2'
                                }
                                ]
                            },
                            {
                            xtype: 'textfield',
                                fieldLabel: 'Fecha de Inicio:',
                                id: 'fechaInicial',
                                name: 'fechaInicial',
                                value: data.fechaEjecucion,
                                readOnly: true
                            },
                            {
                            xtype: 'textfield',
                                fieldLabel: 'Hora Inicial Tarea:',
                                id: 'horaInicial',
                                name: 'horaInicial',
                                value: data.horaEjecucion,
                                readOnly: true
                            },
                            {
                            fieldLabel: 'Fecha de Cierre:',
                                xtype: 'textfield',
                                id: 'fe_cierre_value',
                                name: 'fe_cierre_value',
                                format: 'Y-m-d',
                                editable: false,
                                readOnly: true,
                                value: fechaActual,
                                listeners:
                            {
                            select:
                            {
                            fn: function(e)
                            {
                            date = e.getValue();
                                total =
                                getTiempoTotal(fecha, hora, date, Ext.getCmp('ho_cierre_value').value,
                                    'fecha');
                                if (total !== - 1)
                            {
                            Ext.getCmp('tiempoTotal').setValue(total);
                            }
                            else
                            {
                            Ext.getCmp('tiempoTotal').setValue(getTiempoTotal(fecha, hora, date,
                                Ext.getCmp('ho_cierre_value').value,
                                ''));
                                Ext.getCmp('fe_cierre_value').setValue(date);
                            }
                            }
                            }
                            }
                            },
                            {
                            fieldLabel: 'Hora de Cierre:',
                                xtype: 'textfield',
                                format: 'H:i',
                                id: 'ho_cierre_value',
                                name: 'ho_cierre_value',
                                value: horaActual,
                                editable: false,
                                increment: 1,
                                readOnly: true,
                                listeners:
                            {
                            select:
                            {
                            fn: function(e)
                            {
                            date = e.getValue();
                                total =
                                getTiempoTotal(fecha, hora, Ext.getCmp('fe_cierre_value').value, date,
                                    'hora');
                                if (total !== - 1)
                            {
                            Ext.getCmp('tiempoTotal').setValue(total);
                            }
                            else
                            {
                            Ext.getCmp('tiempoTotal').setValue(getTiempoTotal(fecha, hora,
                                Ext.getCmp('fe_cierre_value').value,
                                date, ''));
                                Ext.getCmp('ho_cierre_value').setValue(date);
                            }
                            }
                            }
                            }
                            },
                            {
                            xtype: 'textfield',
                                fieldLabel: 'Tiempo Total Tarea (minutos) index',
                                id: 'tiempoTotal',
                                name: 'tiempoTotal',
                                value:data.duracionMinutos,
                                readOnly: true
                            },
                            
                            {
                                xtype: 'button',
                                id: 'btnIndisponibilidadTarea',
                                text : 'Indisponibilidad',
                                formBind: true,
                                hidden: true,
                                style: {
                                    marginLeft: '79%'
                                },
                                handler: function() {
                                    Ext.getCmp('winIndisponibilidadTarea').setVisible(true);
                                },
                            },
                                fieldsetCoordenadasTotal,
                                gridCuadrilla
                            ]
                        }
                        ]
                    });

                    var conn = new Ext.data.Connection({
                        listeners: {
                            'beforerequest': {
                                fn: function (con, opt) {
                                    Ext.get(document.body).mask('Cargando...');
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

                    // verificar perfil TAP
                    conn.request({
                        url: url_verificarRolTap,
                        method: 'post',
                        success: function(response){	
                            if((response.responseText) === 'S'){
                                Ext.getCmp('btnIndisponibilidadTarea').setVisible(true);
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.show({
                                title: 'Error',
                                msg: result.statusText,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }); 


                    winFinalizarTarea = Ext.create('Ext.window.Window', {
                    title: 'Finalizar Tarea',
                        modal: true,
                        width: width,
                        height: height,
                        resizable: false,
                        layout: 'fit',
                        items: [formPanel2],
                        buttonAlign: 'center',
                        buttons:[btnguardar2, btncancelar2],
                        closable: false//jgiler
                    }).show();
        }
        else
        {
           Ext.Msg.alert('Alerta ', "Para poder finalizar esta tarea, debe ser iniciada desde la aplicación Móvil");
        }
    }
    else
    {
       Ext.Msg.alert('Alerta ', "Esta tarea no se puede finalizar debido que posee una o más subtareas asociadas, por \n\
                                favor cerrar las tareas asociadas a la tarea principal.");
    }
}

/*
* Sin previa documentación
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 - Se agrega los parámetros necesarios para cerrar la tarea SYSCLOUD
* @since 1.0
*/

function guardarTareaFinalizada(data, id_detalle)
{
    
    var boolValidaTareaFinal = true;
    if (!Ext.getCmp('cbox_tarea_ini') || Ext.getCmp('cbox_tarea_ini').getValue() == false) 
    { 
        boolValidaTareaFinal = false ;
    }    

    if(data.casoPerteneceTN && data.mostrarCoordenadas == "S" && data.tareasManga == "S" && ( Ext.getCmp('text_longitudManga1').getValue() == ""
        || Ext.getCmp('text_latitudManga1').getValue() == "" || Ext.getCmp('text_latitudManga2').getValue() == ""
        || Ext.getCmp('text_latitudManga2').getValue() == "" || Ext.getCmp('text_latitudI').getValue() == ""
        || Ext.getCmp('text_latitudI').getValue() == ""))
    {
        alert("Por favor llenar los campos obligatorios");
    }
    else if(data.casoPerteneceTN && data.mostrarCoordenadas == "S" && data.tareasManga == "N" && (Ext.getCmp('text_latitudI').getValue() == ""
        || Ext.getCmp('text_latitudI').getValue() == ""))
    {
        alert("Por favor llenar los campos obligatorios");
    }
    else if(data.permiteRegistroActivos === true && data.strFinSelectNombre === '' && ((data.id_caso !== 0) || (data.esInterdepartamental === true))
            && boolValidaTareaFinal == false)
    {
        Ext.Msg.alert('Alerta', 'Por favor seleccione fin de tarea');
    }
    else if((Ext.getCmp('comboMotivoFinaliza').getValue() == '' || Ext.getCmp('comboMotivoFinaliza').getValue() == null) 
             && Ext.getCmp('comboMotivoFinaliza').isVisible() && boolValidaTareaFinal == false)
    {
        Ext.Msg.alert('Alerta', 'Por favor seleccione motivo');
    }
    else
    {
        var conn = new Ext.data.Connection
        ({
            listeners:
            {
                'beforerequest':
                {
                    fn: function (con, opt)
                    {
                        Ext.get(document.body).mask('Finalizando Tarea...');
                    },
                    scope: this
                },
                'requestcomplete':
                {
                    fn: function (con, res, opt)
                    {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                },
                'requestexception':
                {
                    fn: function (con, res, opt)
                    {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                }
            }
        });
        var finalizaObservacion = Ext.getCmp('observacion').value;
        var finalizaRadio       = Ext.getCmp('radio1').getValue();
        var finalizaTiempo      = Ext.getCmp('tiempoTotal').getValue();
        var finalizaFeCierre    = Ext.getCmp('fe_cierre_value').getValue();
        var finalizaHorCierre   = Ext.getCmp('ho_cierre_value').getValue();
        var motivoFinalizaTarea;
        var finalizaComboTarea;
        
        if (data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true))
           && boolValidaTareaFinal == false)
        {
            motivoFinalizaTarea = Ext.getCmp('comboMotivoFinaliza').getValue();
            finalizaComboTarea  = data.strFinSelectNombre;
        }
        else if(boolValidaTareaFinal)
        {
            finalizaComboTarea =  data.nombreTareaAnterior;
        }  
        else
        {
            finalizaComboTarea  = Ext.getCmp('comboTarea').getRawValue();
        }

        var longitudIncidente   = "";
        var latitudIncidente    = "";
        var longitudManga1      = "";
        var latitudManga1       = "";
        var longitudManga2      = "";
        var latitudManga2       = "";
        if(data.casoPerteneceTN && data.mostrarCoordenadas == "S")
        {
            longitudIncidente   = Ext.getCmp('text_longitudI').getValue();
            latitudIncidente    = Ext.getCmp('text_latitudI').getValue();

            if(data.tareasManga == "S")
            {
                longitudManga1   = Ext.getCmp('text_longitudManga1').getValue();
                latitudManga1    = Ext.getCmp('text_latitudManga1').getValue();
                longitudManga2   = Ext.getCmp('text_longitudManga2').getValue();
                latitudManga2    = Ext.getCmp('text_latitudManga2').getValue();
            }
        }

        if(data.perteneceCaso && data.casoPerteneceTN )
        {
            finalizaRadio       = false;
        }

        let intFinTareaId;
        let nombreTarea = Ext.getCmp('comboTarea').getValue();
        if (data.intFinTareaId === undefined) 
        {
            if (Ext.getCmp('comboTarea').getValue() == data.nombre_tarea && boolValidaTareaFinal == false) 
            {
                intFinTareaId = data.id_tarea;
            }
            else if(boolValidaTareaFinal)
            {
                intFinTareaId = data.idTareaAnterior;
            }
            else
            {            
                intFinTareaId = nombreTarea;
            }
        }
        else
        {
            intFinTareaId = data.intFinTareaId;
        }


        
        var strGuardar = 'NO';
        var strIndisponibilidadI = '';
        var strTipoI = 'T';
        var intTiempoAfectacionI = '0';
        var strMasivoI = '';
        var intComboResponsableI = '';
        var intClientesAfectadosI = '0';
        var strObservacionesI = '';
        var strOltI = '';
        var strPuertoI = '';
        var strCajaI = '';
        var strSplitterI = '';
        var i;
        var j;

        if(Ext.getCmp('btnIndisponibilidadTarea').isVisible()){

            strGuardar = 'SI';

            strIndisponibilidadI = Ext.getCmp('comboIndisponibilidadTarea').getValue();

            if(strIndisponibilidadI == 'SI'){
                
                intTiempoAfectacionI = Ext.getCmp('tiempoAfectacionTarea').getValue();
                strMasivoI = Ext.getCmp('comboMasivoTarea').getValue();

                if(strMasivoI == 'SI'){
                
                    intClientesAfectadosI = Ext.getCmp('clientesAfectadosTarea').getValue();
                    intComboResponsableI = Ext.getCmp('comboResponsableTarea').getValue();
                    strObservacionesI = Ext.getCmp('observacionesTarea').getValue();
                    strOltI = Ext.getCmp('oltSeleccionadosTarea').getValue();

                    if (strOltI == ''){
                        Ext.Msg.alert("Alerta","Debe escoger un elemento Olt");
                        return false;
                    }else if (intClientesAfectadosI == null){
                        Ext.Msg.alert("Alerta","Debe llenar clientes afectados");
                        return false;
                    }else if (intComboResponsableI == null){
                        Ext.Msg.alert("Alerta","Debe escoger un responsable del problema");
                        return false;
                    }


                    // combo puerto
                    if(comboPuertoTarea.valueModels != null){
                        
                        for (i = 0; i<comboPuertoTarea.valueModels.length; i++){

                            for (j = 0; j<storePuertoTarea.data.items.length; j++){

                                if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                    
                                    if (strPuertoI == ''){
                                        strPuertoI = comboPuertoTarea.valueModels[i].data.idInterface;
                                    }else{
                                        strPuertoI = strPuertoI + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                                    }
                                    break;
        
                                }

                            }

                        }
                    }

                    // combo caja
                    if(comboCajaTarea.valueModels != null){
                        
                        for (i = 0; i<comboCajaTarea.valueModels.length; i++){

                            for (j = 0; j<storeCajaTarea.data.items.length; j++){

                                if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                                    
                                    if (strCajaI == ''){
                                        strCajaI = comboCajaTarea.valueModels[i].data.idCaja;
                                    }else{
                                        strCajaI = strCajaI + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                                    }
                                    break;
        
                                }

                            }

                        }
                    }

                    // combo splitter
                    if(comboSplitterTarea.valueModels != null){
                        
                        for (i = 0; i<comboSplitterTarea.valueModels.length; i++){

                            for (j = 0; j<storeSplitterTarea.data.items.length; j++){

                                if (comboSplitterTarea.valueModels[i].data.idSplitter == storeSplitterTarea.data.items[j].data.idSplitter){
                                    
                                    if (strSplitterI == ''){
                                        strSplitterI = comboSplitterTarea.valueModels[i].data.idSplitter;
                                    }else{
                                        strSplitterI = strSplitterI + ', ' + comboSplitterTarea.valueModels[i].data.idSplitter;
                                    }
                                    break;
        
                                }

                            }

                        }
                    }
        
                }

            }

        }

        var arayDataFinalizar = 
            {
                id_detalle:         id_detalle,
                observacion:        finalizaObservacion,
                esSolucion:         finalizaRadio,
                tiempo_total:       finalizaTiempo,
                tiempo_cierre:      finalizaFeCierre,
                hora_cierre:        finalizaHorCierre,
                tiempo_ejecucion:   data.fechaEjecucion,
                hora_ejecucion:     data.horaEjecucion,
                clientes:           data.clientes,
                tarea:              finalizaComboTarea,
                tarea_final:        "S",
                longitud:           longitudIncidente,
                latitud:            latitudIncidente,
                longitudManga1:     longitudManga1,
                latitudManga1:      latitudManga1,
                longitudManga2:     longitudManga2,
                latitudManga2:      latitudManga2,
                duracionTarea:      data.duracionTarea,
                conn:               conn,
                id_caso:            data.id_caso,
                casoPerteneceTN:    data.casoPerteneceTN,
                intIdDetalleHist:   data.intIdDetalleHist,
                numeroTarea:        data.numero_tarea,
                nombre_tarea:       data.nombre_tarea,
                nombre_proceso:     data.nombre_proceso,
                asignado_nombre:    data.ref_asignado_nombre,
                departamento_nombre:data.asignado_nombre,
                esInterdepartamental:data.esInterdepartamental,
                tipoMedioId:        data.tipoMedioId,
                idMotivoFinaliza:   motivoFinalizaTarea,
                idFinTarea:         intFinTareaId,
        boolFinalTareaAnterior: boolValidaTareaFinal,
                strGuardar: strGuardar,
                strIndisponibilidadI:  strIndisponibilidadI,
                strTipoI:              strTipoI,
                intTiempoAfectacionI:  intTiempoAfectacionI,
                strMasivoI:            strMasivoI,
                intComboResponsableI:  intComboResponsableI,
                intClientesAfectadosI: intClientesAfectadosI,
                strObservacionesI:     strObservacionesI,
                strOltI:               strOltI,
                strPuertoI:            strPuertoI,
                strCajaI:              strCajaI,
                strSplitterI:          strSplitterI
	};
        
        if((data.permiteRegistroActivos  === true && 
           ((data.strRequiereFibra       === 'S'  && data.tieneProgresoRuta           === 'NO')  ||
            (data.strRequiereMaterial    === 'S'  && data.tieneProgresoMateriales     === 'NO'))
           ) && ((data.id_caso !== 0) || (data.esInterdepartamental === true)))
        {   
                    if(data.strRequiereFibra === 'N')
                    {
                        data['tieneProgresoRuta'] = 'SI';
                    }
                    if(data.strRequiereMaterial === 'N')
                    {
                        data['tieneProgresoMateriales'] = 'SI';
                    }
                    
                    registroFibraMaterial(data, function (statusRegistro) { 

                        if(statusRegistro === 'OK')
                        {
                            finalizarTareaRequest(arayDataFinalizar);
                        }
                        else
                        {
                            Ext.Msg.alert('Alerta', 'No se pudo finalizar la tarea.');
                        }
                    });      
        }
        else
        {
            
            finalizarTareaRequest(arayDataFinalizar);
        
        }    
    
    }
}

/************************************************************************ */
/*********************** REASIGNAR TAREA ******************************** */
/************************************************************************ */

var winReasignarTarea;

function presentarCiudades(empresa) 
{
    storeCiudades.proxy.extraParams = {empresa: empresa};
    storeCiudades.load();
}


function presentarDepartamentosPorCiudad(id_canton, empresa) 
{
    storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
    storeDepartamentosCiudad.load();
}


function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento) 
{
    storeAsignaEmpleado.proxy.extraParams = {id_canton: id_canton, empresa: empresa, id_departamento: id_departamento, departamento_caso: valorIdDepartamento};
    storeAsignaEmpleado.load();
}


function presentarCuadrillasXDepartamento(id_departamento){
    
    storeCuadrillas.proxy.extraParams = { departamento:id_departamento,estado: 'Eliminado',origenD: 'Departamento'};
    storeCuadrillas.load();
  
}

function presentarContratistas(){

    storeContratista.proxy.extraParams = { rol : 'Empresa Externa'};
    storeContratista.load();

}

function reasignarTarea(id_detalle, id_tarea,data,fechaActual, horaActual, tareaEsHal,tipoAsignado, grid){

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ciudadPorEmpresa,
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
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });

    storeDepartamentosCiudad = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_departamentoPorEmpresaCiudad,
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
                {name: 'id_departamento', mapping: 'id_departamento'},
                {name: 'nombre_departamento', mapping: 'nombre_departamento'}
            ]
    });

    storeAsignaEmpleado = new Ext.data.Store({
        total: 'total',        
        proxy: {
            type: 'ajax',
            url: url_empleadoPorDepartamentoCiudad,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_empleado', mapping: 'id_empleado'},
                {name: 'nombre_empleado', mapping: 'nombre_empleado'}
            ]           
    });

    storeCuadrillas = new Ext.data.Store({ 
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_integrantesCuadrilla,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'  
            },
            extraParams: {
                estado: 'Eliminado'		
            }
        },
        fields:
              [
                {name:'idCuadrilla', mapping:'idCuadrilla'},
                {name:'nombre', mapping:'nombre'}
              ],
        listeners: {
	 
            load: function(store) { 
                if(store.proxy.extraParams.origenD == "Departamento")
                {                
                    document.getElementById('radio_e').disabled = false;
                    document.getElementById('radio_c').disabled = false;
                    document.getElementById('radio_co').disabled = false;
                    document.getElementById('radio_e').checked  = false;
                    document.getElementById('radio_c').checked  = false;
                    document.getElementById('radio_co').checked  = false;
                    Ext.getCmp('comboCuadrilla').setDisabled(true);
                    Ext.getCmp('combo_empleados').setDisabled(true);
                    Ext.getCmp('comboContratista').setDisabled(true);
                    
                    storeCuadrillas.proxy.extraParams.origenD = '';
                }
            }
	 
        }              
	}); 

    storeContratista = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                rol : 'Empresa Externa'
            }
        },
        fields:
              [
                {name:'id_empresa_externa', mapping:'id_empresa_externa'},
               {name:'nombre_empresa_externa', mapping:'nombre_empresa_externa'}
              ]
	});

    var iniHtml = "";
    if (tareaEsHal && tipoAsignado.toUpperCase() === 'CUADRILLA') {
        iniHtml =   '<input type="radio" onchange="setearCombo(1);" value="empleado" name="radioCuadrilla" id="radio_e" disabled>\n\
                    &nbsp;Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(2);" value="cuadrilla" \n\
                    disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" \n\
                    onchange="setearCombo(3);" value="contratista" name="radioCuadrilla" id="radio_co" >&nbsp;Contratista';
    } else {
        iniHtml =   '<input type="radio" onchange="setearCombo(1);" value="empleado" name="radioCuadrilla" id="radio_e" disabled>\n\
                    &nbsp;Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(2);" value="cuadrilla" \n\
                    name="radioCuadrilla" id="radio_c" disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" \n\
                    onchange="setearCombo(3);" value="contratista" name="radioCuadrilla" id="radio_co" disabled>&nbsp;Contratista';
    }

    RadiosTiposResponsable =  Ext.create('Ext.Component', {
	html: iniHtml,    
	width: 600,
	padding: 10,
	style: { color: '#000000' }});  

    combo_empleados = new Ext.form.ComboBox({
        id: 'combo_empleados',
        name: 'combo_empleados',
        width: 400,
        fieldLabel: "Empleado",
        store: storeAsignaEmpleado,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        hidden: false,
        disabled: true,
        listeners: {
            select: function(){	                
                Ext.getCmp('comboCuadrilla').value = "";
                Ext.getCmp('comboCuadrilla').setRawValue("");                            
            }
        }        
    });

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

    var containerFinTarea = Ext.create('Ext.container.Container',   {
                                                                        xtype: 'container',
                                                                        layout: 'hbox',
                                                                        align: 'stretch',
                                                                        items: [                                                 

                                                                        ]
                                                                    }); 
    
    var containerMotivoFinTarea = Ext.create('Ext.container.Container', {
                                                                            xtype: 'container',
                                                                            layout: 'hbox',
                                                                            align: 'stretch',
                                                                            items: [                                                 

                                                                            ]
                                                                       }); 
                              
    
    if(data.permiteRegistroActivos === true &&  typeof  finesTareaReasig !== 'undefined' 
       &&  finesTareaReasig !== null && finesTareaReasig.length > 0 )
    {
        comboMotivoReasignaStore = new Ext.data.Store({
                pageSize: 200,
                total: 'total',
                limit:1000,
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                        url : url_gridMotivosCategoriaTareas,
                        reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'data'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'Activo',
                        visible: 'SI',
                        caso:data.id_caso,
                        detalle:id_detalle //Se añade el id detalle para validacion de empresa
                    }
                },
                fields:
                [
                    {name:'idMotivo', mapping:'idMotivo'},
                    {name:'nombreMotivo', mapping:'nombreMotivo'}
                ],
                listeners: {
                    load: function(store) { 
                        if (store.data.length > 0)
                        {
                            Ext.getCmp('comboMotivoReasigna').value = "";
                            Ext.getCmp('comboMotivoReasigna').setRawValue("");
                            Ext.getCmp('comboMotivoReasigna').reset();
                            Ext.getCmp('comboMotivoReasigna').setVisible(true);
                        }
                        else
                        {
                            Ext.getCmp('comboMotivoReasigna').value = "";
                            Ext.getCmp('comboMotivoReasigna').setRawValue("");
                            Ext.getCmp('comboMotivoReasigna').reset();
                            Ext.getCmp('comboMotivoReasigna').setVisible(false);
                        }
                    }
                }
        });

        var strRequiereMaterialReasig       = '';   
        var strRequiereFibraReasig          = '';
        var strRequiereRutaFibraReasig      = '';
        data['strFinSelectNombreReasig']    = '';
        var arrayMenu1                      = []; 

        for(var i = 0; i < finesTareaReasig.length; i++)
        {
            var categoriaPadre          = finesTareaReasig[i];
            var nombrePadre             = categoriaPadre.nombreCategoria;
            var hijosPadre              = categoriaPadre.hijosCategoria;        
            var arrayMenu2              = [];

            for(var j = 0; j < hijosPadre.length; j++)
            {
                var categoriaHijo           = hijosPadre[j];
                var nombreHijo              = categoriaHijo.nombreHijo;
                var listaTareas             = categoriaHijo.listaTareas;    
                var arrayMenuTres           = [];

                    for(var k = 0; k < listaTareas.length; k++)
                    {
                        var tarea = listaTareas[k];
                        var numeroTarea             = tarea.numeroTarea;
                        var nombreTarea             = tarea.nombreTarea;
                        var nombreNivel2            = nombreHijo;
                        var nombreNivel1            = nombrePadre;
                        strRequiereMaterialReasig   = tarea.requiereMaterial;
                        strRequiereFibraReasig      = tarea.requiereFibra;
                        strRequiereRutaFibraReasig  = tarea.requiereRutaFibra;
                        var objFinLTres = 
                        {
                            text:               nombreTarea,
                            requiereMaterial:   strRequiereMaterialReasig,
                            requiereFibra:      strRequiereFibraReasig,
                            requiereRutaFibra:  strRequiereRutaFibraReasig,
                            numeroTarea:        numeroTarea,
                            nombreNivel2:       nombreNivel2,
                            nombreNivel1:       nombreNivel1,
                        };
                        arrayMenuTres.push(objFinLTres);  
                    }

                var menuLevel3          = Ext.create('Ext.menu.Menu', {
                    items: arrayMenuTres
                });

                var objFinLDos;
                    if(arrayMenuTres.length > 0)
                    {
                        objFinLDos = 
                        {
                            text: nombreHijo,
                            menu:menuLevel3
                        };    
                    }
                    else
                    {
                        objFinLDos = {
                            text: nombreHijo
                        };    
                    }

                menuLevel3.on('click', function(menu, item)
                {
                    data['strFinSelectNombreReasig']        = item.text;
                    data['intFinTareaId']                   = item.numeroTarea;
                    data['strRequiereMaterialReasig']       = item.requiereMaterial;
                    data['strRequiereFibraReasig']          = item.requiereFibra;
                    data['strRequiereRutaFibraReasig']      = item.requiereRutaFibra;
                    
                    Ext.getCmp('splitbutton_reasig').setText(data['strFinSelectNombreReasig']);
                    Ext.getCmp('comboMotivoReasigna').value = "";
                    Ext.getCmp('comboMotivoReasigna').setRawValue("");
                    Ext.getCmp('comboMotivoReasigna').reset();
                    Ext.getCmp('comboMotivoReasigna').setDisabled(false);

                    comboMotivoReasignaStore.proxy.extraParams = {
                                                                     valor1: item.nombreNivel1, 
                                                                     valor2: item.nombreNivel2, 
                                                                     valor3: item.numeroTarea
                                                                 };
                    comboMotivoReasignaStore.load();
                });

                arrayMenu2.push(objFinLDos);   
            }

            var menuLevel2          = Ext.create('Ext.menu.Menu', {
                items: arrayMenu2
            });

            var objFinLUno;
            if(arrayMenu2.length > 0)
            {
                objFinLUno = {
                    text: nombrePadre,
                    menu:menuLevel2
                };  
            }
            else
            {
                objFinLUno = 
                {
                    text: nombrePadre
                };  
            }

            arrayMenu1.push(objFinLUno);  
        }               

        var mymenu      = new Ext.menu.Menu({
                             items: arrayMenu1
                         });
                         
                         
        containerFinTarea.add(
                    {
                        xtype: 'label',
                        html: 'Tarea Final:',
                        style: 'padding:10px 0px 10px 0px',
                    }, 
                    {
                        xtype:  'splitbutton',
                        text:   'Seleccionar fin de tarea',
                        id:     'splitbutton_reasig',
                        name:   'btnSelecFinTarea',
                        width:  294,
                        menu:   mymenu,
                        style: 'margin:10px 0px 10px 40px'
                    }
        );
    
        containerMotivoFinTarea.add(new Ext.form.ComboBox(
        {
                        xtype: 'combobox',
                        id:'comboMotivoReasigna',
                        store: comboMotivoReasignaStore,
                        disabled: true,
                        displayField: 'nombreMotivo',
                        valueField: 'idMotivo',
                        height:30,
                        width:400,
                        border:0,
                        fieldLabel: 'Motivo:',
                        queryMode: "remote",
                        emptyText: '',
                        style: 'margin: 0px 0px 10px 0px'
        })
        );
        

    }  

    btnguardar2  = Ext.create('Ext.Button', {
		text: 'Guardar',
		cls: 'x-btn-rigth',
		handler: function() {		
		var valorBool = true;//validarTareasMateriales();
            
                if(data.permiteRegistroActivos === true &&  typeof  finesTareaReasig !== 'undefined'
                   && finesTareaReasig !== null && finesTareaReasig.length > 0
                   && data.strFinSelectNombreReasig === '')
                {
                    Ext.Msg.alert('Alerta', 'Por favor seleccione fin de tarea'); 
                }
                else if(data.permiteRegistroActivos === true &&  typeof  finesTareaReasig !== 'undefined'
                        && finesTareaReasig !== null && finesTareaReasig.length > 0 &&
                        (Ext.getCmp('comboMotivoReasigna').getValue() === '' || 
                         Ext.getCmp('comboMotivoReasigna').getValue() === null) 
                        && Ext.getCmp('comboMotivoReasigna').isVisible())
                {
                    Ext.Msg.alert('Alerta', 'Por favor seleccione motivo');
                }
                else
                {
                    if(Ext.getCmp('comboEmpresa').getValue() != null && Ext.getCmp('comboDepartamento').getValue() != null && 
                       Ext.getCmp('comboCiudad').getValue()  != null )
                    {
                        if(Ext.getCmp('combo_empleados').getValue() || Ext.getCmp('comboCuadrilla').getValue() || Ext.getCmp('comboContratista').getValue())
                        {        
                            if((data.permiteRegistroActivos === true &&  typeof  finesTareaReasig !== 'undefined'
                                && finesTareaReasig !== null && finesTareaReasig.length > 0)
                               &&((data.strRequiereFibraReasig       === 'S'  && data.tieneProgresoRuta           === 'NO')  
                               ||(data.strRequiereMaterialReasig    === 'S'  && data.tieneProgresoMateriales     === 'NO'))  
                               )
                            {   
                                     if(data.strRequiereFibraReasig === 'N')
                                     {
                                         data['tieneProgresoRuta'] = 'SI';
                                     }
                                     if(data.strRequiereMaterialReasig === 'N')
                                     {
                                         data['tieneProgresoMateriales'] = 'SI';
                                     }

                                     registroFibraMaterial(data, function (statusRegistro) { 

                                         if(statusRegistro === 'OK')
                                         {
                                             if(valorBool)
                                             {
                                                    //json_materiales = obtenerMateriales();
                                                    var idMotivoFinTarea;
                                                    var motivoFinTarea;  
                                                    var ReaMotivo       = Ext.getCmp('motivo').value;
                                                    var ReaDepartamento = Ext.getCmp('comboDepartamento').value;
                                                    var ReaEmpleados    = Ext.getCmp('combo_empleados').value;
                                                    var ReaCuadrilla    = Ext.getCmp('comboCuadrilla').value;
                                                    var ReaContratista  = Ext.getCmp('comboContratista').value;
                                                    var ReaFechaEjecu   = Ext.getCmp('fecha_ejecucion').value;
                                                    var ReaHoraEjecu    = Ext.getCmp('hora_ejecucion').value;
                                                    var nombreFinTarea  = (data.strFinSelectNombreReasig ? data.strFinSelectNombreReasig : "");
                                                    var idFinTarea      = (data.intFinTareaId ? data.intFinTareaId : ""); //jobedon
                                                    if(typeof  Ext.getCmp('comboMotivoReasigna') !== 'undefined')
                                                    {
                                                        idMotivoFinTarea    = (Ext.getCmp('comboMotivoReasigna').getValue() ? Ext.getCmp('comboMotivoReasigna').getValue() : null);;
                                                        motivoFinTarea      = (Ext.getCmp('comboMotivoReasigna').getRawValue() ? Ext.getCmp('comboMotivoReasigna').getRawValue() : "Sin Motivo");  
                                                    }        
                                                    winReasignarTarea.destroy();
                                                    conn.request({
                                                        method: 'POST',
                                                        params :{
                                                            id_detalle: id_detalle,
                                                            id_tarea:id_tarea,
                                                            //observacion: Ext.getCmp('observacion').value,
                                                            motivo: ReaMotivo,
                                                            departamento_asignado: ReaDepartamento,
                                                            empleado_asignado   :  ReaEmpleados,
                                                            cuadrilla_asignada  :  ReaCuadrilla,
                                                            contratista_asignada:  ReaContratista,
                                                            tipo_asignado       :  valorAsignacion,
                                                            fecha_ejecucion     :  ReaFechaEjecu,
                                                            hora_ejecucion      :  ReaHoraEjecu,
                                                            intIdDetalleHist    :  data.intIdDetalleHist,
                                                            nombre_tarea        :  data.nombre_tarea,
                                                            numero_tarea        :  data.numero_tarea,
                                                            nombreFinTarea      :  nombreFinTarea,
                                                            idFinTarea          :  idFinTarea,
                                                            motivoFinTarea      :  motivoFinTarea,
                                                            idMotivoFinTarea    :  idMotivoFinTarea
                                                        },
                                                        url: 'reasignarTarea',
                                                        success: function(response){                         
                                                            var json = Ext.JSON.decode(response.responseText);

                                                            if (!json.success && !json.seguirAccion) {
                                                                Ext.MessageBox.show({
                                                                    closable   :  false  , multiline : false,
                                                                    title      : 'Alerta', msg : json.mensaje,
                                                                    buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                                                    buttonText : {ok: 'Cerrar'},
                                                                    fn : function (button) {
                                                                        if(button === 'ok') {
                                                                            store.load();
                                                                        }
                                                                    }
                                                                });
                                                                return;
                                                            }

                                                            if(json.mensaje != "cerrada")
                                                            {
                                                                if(json.success)
                                                                {
                                                                    Ext.Msg.alert('Mensaje','Se asigno la tarea.', function(btn){
                                                                        if(btn=='ok'){
                                                                            store.load();
                                                                        }
                                                                    });
                                                                }
                                                                else
                                                                {
                                                                    Ext.Msg.alert('Alerta ',json.mensaje);
                                                                }
                                                            }
                                                            else
                                                            {
                                                                    Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                                                            }
                                                        },
                                                        failure: function(response) {
                                                            var json = Ext.JSON.decode(response.responseText);
                                                            Ext.Msg.alert('Alerta ',json.mensaje);
                                                        }
                                                    });
                                                }
                                         }
                                         else
                                         {
                                             Ext.Msg.alert('Alerta', 'No se pudo reasigar la tarea.');
                                         }
                                     });      
                            }
                            else
                            {
                                if(valorBool)
                                {
                                    //json_materiales = obtenerMateriales();
                                    var idMotivoFinTarea;
                                    var motivoFinTarea;  
                                    var ReaMotivo       = Ext.getCmp('motivo').value;
                                    var ReaDepartamento = Ext.getCmp('comboDepartamento').value;
                                    var ReaEmpleados    = Ext.getCmp('combo_empleados').value;
                                    var ReaCuadrilla    = Ext.getCmp('comboCuadrilla').value;
                                    var ReaContratista  = Ext.getCmp('comboContratista').value;
                                    var ReaFechaEjecu   = Ext.getCmp('fecha_ejecucion').value;
                                    var ReaHoraEjecu    = Ext.getCmp('hora_ejecucion').value;
                                    var nombreFinTarea  = (data.strFinSelectNombreReasig ? data.strFinSelectNombreReasig : "");
                                    var idFinTarea      = (data.intFinTareaId ? data.intFinTareaId : ""); //jobedon
                                    if(typeof  Ext.getCmp('comboMotivoReasigna') !== 'undefined')
                                    {
                                        idMotivoFinTarea    = (Ext.getCmp('comboMotivoReasigna').getValue() ? Ext.getCmp('comboMotivoReasigna').getValue() : null);;
                                        motivoFinTarea      = (Ext.getCmp('comboMotivoReasigna').getRawValue() ? Ext.getCmp('comboMotivoReasigna').getRawValue() : "Sin Motivo");  
                                    }
                                    winReasignarTarea.destroy();
                                    conn.request({
                                        method: 'POST',
                                        params :{
                                            id_detalle: id_detalle,
                                            id_tarea:id_tarea,
                                            //observacion: Ext.getCmp('observacion').value,
                                            motivo: ReaMotivo,
                                            departamento_asignado: ReaDepartamento,
                                            empleado_asignado   :  ReaEmpleados,
                                            cuadrilla_asignada  :  ReaCuadrilla,
                                            contratista_asignada:  ReaContratista,
                                            tipo_asignado       :  valorAsignacion,
                                            fecha_ejecucion     :  ReaFechaEjecu,
                                            hora_ejecucion      :  ReaHoraEjecu,
                                            intIdDetalleHist    :  data.intIdDetalleHist,
                                            nombre_tarea        :  data.nombre_tarea,
                                            numero_tarea        :  data.numero_tarea,
                                            nombreFinTarea      :  nombreFinTarea,
                                            idFinTarea          :  idFinTarea,
                                            motivoFinTarea      :  motivoFinTarea,
                                            idMotivoFinTarea    :  idMotivoFinTarea
                                        },
                                        url: 'reasignarTarea',
                                        success: function(response){                         
                                            var json = Ext.JSON.decode(response.responseText);

                                            if (!json.success && !json.seguirAccion) {
                                                Ext.MessageBox.show({
                                                    closable   :  false  , multiline : false,
                                                    title      : 'Alerta', msg : json.mensaje,
                                                    buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                                    buttonText : {ok: 'Cerrar'},
                                                    fn : function (button) {
                                                        if(button === 'ok') {
                                                            store.load();
                                                        }
                                                    }
                                                });
                                                return;
                                            }

                                            if(json.mensaje != "cerrada")
                                            {
                                                if(json.success)
                                                {
                                                    Ext.Msg.alert('Mensaje','Se asigno la tarea.', function(btn){
                                                        if(btn=='ok'){
                                                            store.load();
                                                        }
                                                    });
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Alerta ',json.mensaje);
                                                }
                                            }
                                            else
                                            {
                                                    Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                                            }
                                        },
                                        failure: function(response) {
                                            var json = Ext.JSON.decode(response.responseText);
                                            Ext.Msg.alert('Alerta ',json.mensaje);
                                        }
                                    });
                                }
                            }

                        }
                        else
                        {
                                    Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado, cuadrilla o contratista');
                        }
                    } 
                    else
                    {
                        Ext.Msg.alert('Alerta ','Campos incompletos, debe seleccionar Empresa,Ciudad y Departamento');
                    }            

                }

            
	}
    });

    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winReasignarTarea.destroy();
		}
    });

    var title = 'Información';

    if (tareaEsHal)
    {
        title = title + ' - <b style="color:red;">Tarea HAL</b>';
    }

    formPanel2   = Ext.create('Ext.form.Panel', {
                title: (data.tareaParametro ? "Manual" : ""),
		bodyPadding: 5,
		waitMsgTarget: true,
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				title: title,
				defaultType: 'textfield',
				items: 
				[
                    {
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'tareaCaso',
						name: 'tareaCaso',
						value: data.nombre_tarea
					}, 
                                        containerFinTarea,
                                        containerMotivoFinTarea,
                    {
                        xtype: 'fieldset',
                        title: '',
                        style: 'border: none;padding:0px',
                        bodyStyle: 'padding:0px',
                        layout: {
                            type: 'table',
                            columns: 3,
                            pack: 'center'
                        },
                        items: [
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Empresa:',
                                width: 400,
                                id: 'comboEmpresa',
                                name: 'comboEmpresa',
                                store: storeEmpresas,
                                displayField: 'opcion',
                                valueField: 'valor',
                                queryMode: "remote",
                                emptyText: '',
                                listeners: {
                                    select: function(combo){
                                        Ext.getCmp('comboCiudad').reset();
                                        Ext.getCmp('comboDepartamento').reset();
                                        Ext.getCmp('combo_empleados').reset();

                                        Ext.getCmp('comboCiudad').setDisabled(false);
                                        Ext.getCmp('comboDepartamento').setDisabled(true);
                                        Ext.getCmp('combo_empleados').setDisabled(true);

                                        presentarCiudades(combo.getValue());
                                        Ext.getCmp('cbox_responder').setValue(false);
                                    }
                                },
                                forceSelection: true
                            }, 
                            {
                                width: 30,
                                layout: 'form',
                                border: false,
                                items: 
                                [
                                    {
                                        xtype: 'displayfield'
                                    }
                                ]
                            },
                            {
                                 xtype: 'checkboxfield',
                                 fieldLabel: 'Respuesta Inmediata',
                                 name: 'cbox_responder',
                                 id: 'cbox_responder',
                                 checked: false,
                                 labelWidth: 150,
                                 listeners: {
                                    afterrender: function(checkbox) {
                                           checkbox.getEl().on('click', function() {
                                               setearUsuarioResponder(id_detalle);
                                         });
                                    }
                                 }
                             }
                        ]
                    },
					{
						xtype: 'combobox',
						fieldLabel: 'Ciudad',
						id: 'comboCiudad',
						width: 400,
						name: 'comboCiudad',
						store: storeCiudades,
						displayField: 'nombre_canton',
						valueField: 'id_canton',
						queryMode: "remote",
						emptyText: '',
						disabled: true,
						listeners: {
							select: function(combo){															
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('combo_empleados').reset();
																								
								Ext.getCmp('comboDepartamento').setDisabled(false);
								Ext.getCmp('combo_empleados').setDisabled(true);
								
								empresa = Ext.getCmp('comboEmpresa').getValue();
								
								presentarDepartamentosPorCiudad(combo.getValue(),empresa);
								Ext.getCmp('cbox_responder').setValue(false);
							}
						},
						forceSelection: true
					}, 
					{
						xtype: 'combobox',
						fieldLabel: 'Departamento',
						id: 'comboDepartamento',
						width: 400,
						name: 'comboDepartamento',
						store: storeDepartamentosCiudad,
						displayField: 'nombre_departamento',
						valueField: 'id_departamento',
						queryMode: "remote",
						emptyText: '',
						disabled: true,
						listeners: {
                            afterRender: function(combo) {
                                if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                {
                                    storeEmpresas.load(function() {
                                        Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                        storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };
                                        storeCiudades.load(function() {
                                            Ext.getCmp('comboCiudad').setDisabled(false);
                                            if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                            {
                                                Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                storeDepartamentosCiudad.proxy.extraParams = { id_canton:   strIdCantonUsrSession,
                                                                                               empresa  :   strPrefijoEmpresaSession};
                                                storeDepartamentosCiudad.load(function() {
                                                    Ext.getCmp('comboDepartamento').setDisabled(false);
                                                    combo.setValue(Number(strIdDepartamentoUsrSession));
                                                    presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                          strIdCantonUsrSession, 
                                                                                          strPrefijoEmpresaSession);
                                                    presentarCuadrillasXDepartamento(strIdDepartamentoUsrSession);
                                                    presentarContratistas();
                                                    elWinReasignarTarea.unmask();
                                                });
                                            }
                                            else
                                            {
                                                elWinReasignarTarea.unmask();
                                            }
                                        });
                                    });
                                }
                                else
                                {
                                    elWinReasignarTarea.unmask();
                                }
                            },
							select: function(combo){							
								

                                Ext.getCmp('combo_empleados').reset();
                                Ext.getCmp('combo_empleados').value = "";
                                Ext.getCmp('combo_empleados').setDisabled(true);                               
                                Ext.getCmp('combo_empleados').setRawValue("");  
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                                Ext.getCmp('comboCuadrilla').setDisabled(true);
                                Ext.getCmp('comboContratista').value = "";
                                Ext.getCmp('comboContratista').setRawValue("");
                                Ext.getCmp('comboContratista').setDisabled(true);
                                empresa = Ext.getCmp('comboEmpresa').getValue();
                                canton  = Ext.getCmp('comboCiudad').getValue();
                                presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa);
                                presentarCuadrillasXDepartamento(Ext.getCmp('comboDepartamento').getValue());
                                presentarContratistas();
                                Ext.getCmp('cbox_responder').setValue(false);
							}
						},
						forceSelection: true
					}, 
                    RadiosTiposResponsable,
                    combo_empleados,
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Cuadrilla',
                        id: 'comboCuadrilla',
                        width: 400,
                        name: 'comboCuadrilla',
                        store: storeCuadrillas,
                        displayField: 'nombre',
                        valueField: 'idCuadrilla',
                        queryMode: "remote",
                        minChars: 3,
                        emptyText: '',
                        disabled: true,
                        listeners: {
                            select: function(combo){
                                Ext.getCmp('combo_empleados').value = "";
                                Ext.getCmp('combo_empleados').setRawValue("");
                                validarTabletPorCuadrilla(combo.getValue());
                            }
                        }

                    },
                    {
                        xtype       : 'combobox',
                        fieldLabel  : 'Contratista',
                        id          : 'comboContratista',
                        width       : 400,
                        name        : 'comboContratista',
                        store       : storeContratista,
                        displayField: 'nombre_empresa_externa',
                        valueField  : 'id_empresa_externa',
                        queryMode   : "remote",
                        emptyText   : '',
                        disabled    : true,
                        listeners: {
                            select: function(){
                                Ext.getCmp('combo_empleados').value = "";
                                Ext.getCmp('combo_empleados').setRawValue("");
                                Ext.getCmp('comboCuadrilla').value = "";
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                            }
                        }

                    },
					{
						xtype: 'datefield',
						fieldLabel: 'Fecha de Ejecucion:',
						id: 'fecha_ejecucion',
						name:'fecha_ejecucion',
						editable: false,
						format: 'Y-m-d',
						value:fechaActual,
						minValue: fechaActual
					},
					{
						xtype: 'timefield',
						fieldLabel: 'Hora de Ejecucion:',
						format: 'H:i',
						id: 'hora_ejecucion',
						name: 'hora_ejecucion',
						minValue: '00:01',
						maxValue: '23:59',
						increment: 1,						
						editable: true,
						value:horaActual
					},
                                        {
						xtype: 'textarea',
						fieldLabel: 'Motivo:',
						id: 'motivo',
						name: 'motivo',
						rows: 3,
						cols: 100
					},
                                                
                                                
				]
			}
		],
                buttonAlign: 'center',
                buttons:[btnguardar2,btncancelar2]
	 });  

    var panelHal = halAsigna('reasignar',
                             fechaActual,
                             id_detalle,
                             id_tarea,
                             data.numero_tarea,
                             data);

    var tabs = new Ext.TabPanel({
        xtype     :'tabpanel',
        activeTab : 0,
        autoScroll: false,
        layoutOnTabChange: true,
        items: [formPanel2,panelHal]
    });

    winReasignarTarea = Ext.create('Ext.window.Window', {
            title    : 'Reasignar Tarea',
            modal    : true,
            closable : false,
            width    : 700,
            layout   : 'fit',
            items    : (data.tareaParametro ? [tabs] : [formPanel2])
    }).show();

    elWinReasignarTarea = winReasignarTarea.getEl();

    elWinReasignarTarea.mask('Cargando...');
}


function validarTabletPorCuadrilla(idCuadrilla)
{        
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
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
        url: url_validarTabletPorCuadrilla,
        method: 'post',
        params: 
            { 
                cuadrillaId : idCuadrilla
            },
        success: function(response){			
            var text = Ext.decode(response.responseText);

            if(text.existeTablet == "S")
            {
                cuadrillaAsignada = "S";
            }
            else
            {
                Ext.Msg.alert("Alerta","La cuadrilla "+text.nombreCuadrilla+" no posee tablet asignada. Realice la asignación de tablet correspondiente o \n\
                                        seleccione otra cuadrilla.");
                cuadrillaAsignada = "N";
                Ext.getCmp('comboCuadrilla').setValue("");
            }
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });          
}


/************************************************************************ */
/*********************** CREAR SUB TAREAS ******************************** */
/************************************************************************ */

function agregarSubTarea(data,fecha,hora)
{
    var valorIdDepartamento = '';

    storeProcesos = new Ext.data.Store({
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_procesos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'nombreProceso', mapping: 'nombreProceso'}
            ]

    });

    storeTareas = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_tareaProceso,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreProceso: 'TAREAS SOPORTE',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idTarea', mapping: 'idTarea'},
                {name: 'nombreTarea', mapping: 'nombreTarea'}
            ]
    });

    //Informacion de asignacion

    storeAsignaEmpleado = new Ext.data.Store({
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: url_empleadoPorDepartamentoCiudad,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_empleado', mapping: 'id_empleado'},
                {name: 'nombre_empleado', mapping: 'nombre_empleado'}
            ]
    });


    storeEmpresas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresaPorSistema,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                app: 'TELCOS'
            }
        },
        fields:
            [
                {name: 'opcion', mapping: 'nombre_empresa'},
                {name: 'valor', mapping: 'prefijo'}
            ]
    });

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ciudadPorEmpresa,
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
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });


    storeDepartamentosCiudad = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_departamentoPorEmpresaCiudad,
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
                {name: 'id_departamento', mapping: 'id_departamento'},
                {name: 'nombre_departamento', mapping: 'nombre_departamento'}
            ]
    });


    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_integrantesCuadrilla,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'idCuadrilla', mapping: 'idCuadrilla'},
                {name: 'nombre', mapping: 'nombre'}
            ],
        listeners: {
            load: function(store) {
                if (store.proxy.extraParams.origenD === "Departamento")
                {
                    document.getElementById('radio_e').disabled = false;
                    document.getElementById('radio_c').disabled = false;
                    document.getElementById('radio_co').disabled = false;
                    document.getElementById('radio_e').checked = false;
                    document.getElementById('radio_c').checked = false;
                    document.getElementById('radio_co').checked = false;
                    Ext.getCmp('comboCuadrilla').setDisabled(true);
                    Ext.getCmp('combo_empleados').setDisabled(true);
                    Ext.getCmp('comboContratista').setDisabled(true);
                    
                    storeCuadrillas.proxy.extraParams.origenD = '';
                }
            }

        }
    });

    storeContratista = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
            }
        },
        fields:
            [
                {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
            ]
    });

    var iniHtml = '<div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                      &nbsp;<input type="radio" onchange="setearCombo(1);" value="empleado" name="radioCuadrilla" id="radio_e" disabled>&nbsp;\n\
                      Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(2);" value="cuadrilla" name="radioCuadrilla" \n\
                      id="radio_c" disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(3);"\n\
                      value="contratista" name="radioCuadrilla" id="radio_co" disabled>&nbsp;Contratista</div>';

    RadiosTiposResponsable = Ext.create('Ext.Component', {
        html: iniHtml,
        width: 600,
        padding: 10,
        style: {color: '#000000'}});

    combo_empleados = new Ext.form.ComboBox({
        id: 'combo_empleados',
        name: 'combo_empleados',
        fieldLabel: "Empleado",
        store: storeAsignaEmpleado,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        disabled: true,
        width: 380,
        listeners: {
            select: function(){
                Ext.getCmp('comboCuadrilla').value = "";
                Ext.getCmp('comboCuadrilla').setRawValue("");
                Ext.getCmp('comboContratista').value = "";
                Ext.getCmp('comboContratista').setRawValue("");
            }
        }
    });
    var formPanelTareas = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 450,
                        items:
                            [
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Procesos:',
                                    id:'cmbProcesos',
                                    store: storeProcesos,
                                    displayField: 'nombreProceso',
                                    valueField: 'id',
                                    queryMode: "remote",
                                    emptyText: '',
                                    width: 380,
                                    listeners: {
                                        select: function(combo, records, eOpts)
                                        {
                                            storeTareas.proxy.extraParams = {id: combo.getValue()};
                                            storeTareas.load();
                                            Ext.getCmp('cmbTarea').setVisible(true);
                                            Ext.getCmp('cmbTarea').setDisabled(false);

                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cmbTarea',
                                    store: storeTareas,
                                    displayField: 'nombreTarea',
                                    valueField: 'idTarea',
                                    fieldLabel: 'Tarea:',
                                    queryMode: "remote",
                                    emptyText: '',
                                    width: 380,
                                    disabled: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Empresa:',
                                    id: 'comboEmpresa',
                                    name: 'comboEmpresa',
                                    store: storeEmpresas,
                                    displayField: 'opcion',
                                    valueField: 'valor',
                                    queryMode: "remote",
                                    emptyText: '',
                                    width: 380,
                                    listeners: {
                                        select: function(combo) {

                                            Ext.getCmp('comboCiudad').reset();
                                            Ext.getCmp('comboDepartamento').reset();
                                            Ext.getCmp('combo_empleados').reset();

                                            Ext.getCmp('comboCiudad').setDisabled(false);
                                            Ext.getCmp('comboDepartamento').setDisabled(true);
                                            Ext.getCmp('combo_empleados').setDisabled(true);

                                            presentarCiudades(combo.getValue());
                                        }
                                    },
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Ciudad',
                                    id: 'comboCiudad',
                                    name: 'comboCiudad',
                                    store: storeCiudades,
                                    displayField: 'nombre_canton',
                                    valueField: 'id_canton',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function(combo) {
                                            Ext.getCmp('comboDepartamento').reset();
                                            Ext.getCmp('combo_empleados').reset();

                                            Ext.getCmp('comboDepartamento').setDisabled(false);
                                            Ext.getCmp('combo_empleados').setDisabled(true);

                                            empresa = Ext.getCmp('comboEmpresa').getValue();

                                            presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                                        }
                                    },
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Departamento',
                                    id: 'comboDepartamento',
                                    name: 'comboDepartamento',
                                    store: storeDepartamentosCiudad,
                                    displayField: 'nombre_departamento',
                                    valueField: 'id_departamento',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        afterRender: function(combo) {
                                            if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                            {
                                                storeEmpresas.load(function() {
                                                    Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                                    storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };
                                                    storeCiudades.load(function() {
                                                        Ext.getCmp('comboCiudad').setDisabled(false);
                                                        if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                                        {
                                                            Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                            storeDepartamentosCiudad.proxy.extraParams = { id_canton:   strIdCantonUsrSession,
                                                                                                           empresa  :   strPrefijoEmpresaSession};
                                                            storeDepartamentosCiudad.load(function() {
                                                                Ext.getCmp('comboDepartamento').setDisabled(false);
                                                                combo.setValue(Number(strIdDepartamentoUsrSession));
                                                                presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                                      strIdCantonUsrSession, 
                                                                                                      strPrefijoEmpresaSession);
                                                                presentarCuadrillasXDepartamento(strIdDepartamentoUsrSession);
                                                                presentarContratistas();
                                                                elWinCrearTarea.unmask();
                                                            });
                                                        }
                                                        else
                                                        {
                                                            elWinCrearTarea.unmask();
                                                        }
                                                    });
                                                });
                                            }
                                            else
                                            {
                                                elWinCrearTarea.unmask();
                                            }
                                        },
                                        select: function(combo) {


                                            Ext.getCmp('combo_empleados').reset();
                                            Ext.getCmp('combo_empleados').setDisabled(true);
                                            Ext.getCmp('comboCuadrilla').value = "";
                                            Ext.getCmp('comboCuadrilla').setRawValue("");
                                            Ext.getCmp('comboCuadrilla').setDisabled(true);
                                            Ext.getCmp('comboContratista').value = "";
                                            Ext.getCmp('comboContratista').setRawValue("");
                                            Ext.getCmp('comboContratista').setDisabled(true);
                                            empresa = Ext.getCmp('comboEmpresa').getValue();
                                            canton = Ext.getCmp('comboCiudad').getValue();
                                            presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, valorIdDepartamento, 'no');
                                            presentarCuadrillasXDepartamento(Ext.getCmp('comboDepartamento').getValue());
                                            presentarContratistas();
                                        }
                                    },
                                    forceSelection: true
                                },
                                RadiosTiposResponsable,
                                {
                                    xtype: 'combobox',
                                    id: 'combo_empleados',
                                    name: 'combo_empleados',
                                    fieldLabel: "Empleado",
                                    store: storeAsignaEmpleado,
                                    displayField: 'nombre_empleado',
                                    valueField: 'id_empleado',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function() {
                                            Ext.getCmp('comboCuadrilla').value = "";
                                            Ext.getCmp('comboCuadrilla').setRawValue("");
                                            Ext.getCmp('comboContratista').value = "";
                                            Ext.getCmp('comboContratista').setRawValue("");
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Cuadrilla',
                                    id: 'comboCuadrilla',
                                    name: 'comboCuadrilla',
                                    store: storeCuadrillas,
                                    displayField: 'nombre',
                                    valueField: 'idCuadrilla',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function(combo) {
                                            Ext.getCmp('combo_empleados').value = "";
                                            Ext.getCmp('combo_empleados').setRawValue("");
                                            Ext.getCmp('comboContratista').value = "";
                                            Ext.getCmp('comboContratista').setRawValue("");
                                            validarTabletPorCuadrilla(combo.getValue());
                                        }
                                    }

                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Contratista',
                                    id: 'comboContratista',
                                    name: 'comboContratista',
                                    store: storeContratista,
                                    displayField: 'nombre_empresa_externa',
                                    valueField: 'id_empresa_externa',
                                    queryMode: "remote",
                                    emptyText: '',
                                    disabled: true,
                                    width: 380,
                                    listeners: {
                                        select: function() {
                                            Ext.getCmp('combo_empleados').value = "";
                                            Ext.getCmp('combo_empleados').setRawValue("");
                                            Ext.getCmp('comboCuadrilla').value = "";
                                            Ext.getCmp('comboCuadrilla').setRawValue("");
                                        }
                                    }

                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha de Ejecución:',
                                    id: 'fecha_ejecucion',
                                    name: 'fecha_ejecucion',
                                    editable: false,
                                    format: 'Y-m-d',
                                    width: 380,
                                    value: fecha,
                                    minValue: fecha
                                },
                                {
                                    xtype: 'timefield',
                                    fieldLabel: 'Hora de Ejecución:',
                                    format: 'H:i',
                                    id: 'hora_ejecucion',
                                    name: 'hora_ejecucion',
                                    minValue: '00:01',
                                    maxValue: '23:59',
                                    increment: 1,
                                    editable: true,
                                    value: hora,
                                    width: 380
                                },
                                {
                                    xtype: 'textarea',
                                    id: 'observacionAsignacion',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion',
                                    rows: 3,
                                    allowBlank: false,
                                    width: 380
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Agregar Tarea',
                        handler: function()
                        {
                            if (Ext.getCmp('cmbTarea').value !== null && Ext.getCmp('cmbTarea').value !== "")
                            {
                                if (Ext.getCmp('comboEmpresa').getValue() !== null && Ext.getCmp('comboDepartamento').getValue() !== null &&
                                    Ext.getCmp('comboCiudad').getValue() !== null)
                                {

                                    if ((Ext.getCmp('combo_empleados') && Ext.getCmp('combo_empleados').value) ||
                                        (Ext.getCmp('comboCuadrilla') && Ext.getCmp('comboCuadrilla').value && valorAsignacion === "cuadrilla") ||
                                        (Ext.getCmp('comboContratista') && Ext.getCmp('comboContratista').value &&
                                         valorAsignacion === "contratista"))
                                    {
                                        personaEmpresaRol = null;
                                        refAsignadoNombre = null;

                                        if (valorAsignacion === "empleado")
                                        {
                                            var comboEmpleado        = Ext.getCmp('combo_empleados').value;
                                            var valoresComboEmpleado = comboEmpleado.split("@@");
                                            refAsignadoId       = valoresComboEmpleado[0];
                                            personaEmpresaRol   = valoresComboEmpleado[1];
                                            refAsignadoNombre   = Ext.getCmp('combo_empleados').rawValue;
                                            asignadoId          = Ext.getCmp('comboDepartamento').value;
                                            asignadoNombre      = Ext.getCmp('comboDepartamento').rawValue;
                                            tipoAsignado        = "EMPLEADO";

                                        }
                                        else if (valorAsignacion === "cuadrilla")
                                        {
                                            refAsignadoId       = "0";
                                            asignadoId          = Ext.getCmp('comboCuadrilla').value;
                                            asignadoNombre      = Ext.getCmp('comboCuadrilla').rawValue;
                                            tipoAsignado        = "CUADRILLA";
                                        }
                                        else
                                        {
                                            refAsignadoId       = "0";
                                            asignadoId          = Ext.getCmp('comboContratista').value;
                                            asignadoNombre      = Ext.getCmp('comboContratista').rawValue;
                                            tipoAsignado        = "EMPRESAEXTERNA";
                                        }

                                        observacion     = Ext.getCmp('observacionAsignacion').value;
                                        fechaEjecucion  = Ext.getCmp('fecha_ejecucion').value;
                                        horaEjecucion   = Ext.getCmp('hora_ejecucion').value;

                                        var conn = new Ext.data.Connection({
                                            listeners: {
                                                'beforerequest': {
                                                    fn: function(con, opt) {
                                                        Ext.getBody().mask('Creando Subtarea');
                                                    },
                                                    scope: this
                                                },
                                                'requestcomplete': {
                                                    fn: function(con, res, opt) {
                                                        Ext.getBody().unmask();
                                                    },
                                                    scope: this
                                                },
                                                'requestexception': {
                                                    fn: function(con, res, opt) {
                                                        Ext.getBody().unmask();
                                                    },
                                                    scope: this
                                                }
                                            }
                                        });

                                        conn.request({
                                            method: 'POST',
                                            params: {
                                                detalleIdRelac    : data.id_detalle,
                                                idTarea           : Ext.getCmp('cmbTarea').value,
                                                personaEmpresaRol : personaEmpresaRol,
                                                asignadoId        : asignadoId,
                                                nombreAsignado    : asignadoNombre,
                                                refAsignadoId     : refAsignadoId,
                                                refAsignadoNombre : refAsignadoNombre,
                                                observacion       : observacion,
                                                fechaEjecucion    : fechaEjecucion,
                                                horaEjecucion     : horaEjecucion,
                                                tipoAsignacion    : tipoAsignado,
                                                numeroTarea       : data.numero_tarea,
                                                empresaAsignacion : Ext.getCmp('comboEmpresa').value,
                                                intIdDetalleHist  : data.intIdDetalleHist,
                                                strValidarAccion  : 'SI'
                                            },
                                            url: url_crearSubTarea,
                                            success: function(response)
                                            {                                           
                                                var json = Ext.JSON.decode(response.responseText);

                                                if (!json.success && !json.seguirAccion) {
                                                    Ext.MessageBox.show({
                                                        closable   :  false  , multiline : false,
                                                        title      : 'Alerta', msg : json.mensaje,
                                                        buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                                        buttonText : {ok: 'Cerrar'},
                                                        fn : function (button) {
                                                            if(button === 'ok') {
                                                                winAsignarTarea.destroy();
                                                                store.load();
                                                            }
                                                        }
                                                    });
                                                    return;
                                                }

                                                Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                                    if (btn == 'ok') {
                                                        winAsignarTarea.destroy();
                                                        store.load();
                                                    }
                                                });                                              
                                            },
                                            failure: function(rec, op) {
                                                var json = Ext.JSON.decode(op.response.responseText);
                                                Ext.Msg.alert('Alerta ', json.mensaje);
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado, cuadrilla o contratista');
                                    }
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ', 'Campos incompletos, debe seleccionar Empresa,Ciudad y Departamento');
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Alerta ', 'Debe escoger una Tarea a asignar');
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winAsignarTarea.destroy();
                        }
                    }
                ]
        });
    var winAsignarTarea = Ext.create('Ext.window.Window',
        {
            title: 'Asignar Tarea',
            modal: true,
            width: 480,
            closable: true,
            layout: 'fit',
            items: [formPanelTareas]
        }).show();
        
    elWinCrearTarea = winAsignarTarea.getEl();
    elWinCrearTarea.mask('Cargando...');
}





/************************************************************************ */
/*********************** VER LOGS TAREA ******************************** */
/************************************************************************ */
var winLogsTarea;

function verLogsTarea(id_detalle, data){
    
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
			var valorBool = true;//validarTareasMateriales();
			if(valorBool)
			{
				//json_materiales = obtenerMateriales();
				
				conn.request({
					method: 'POST',
					params :{
						id_detalle: id_detalle
						
					},
					url: 'getHistorialTareas',
					success: function(response){
						var json = Ext.JSON.decode(response.responseText);
						if(json.success)
						{
							Ext.Msg.alert('Mensaje','Ver el logs de tareas.', function(btn){
								if(btn=='ok'){
									winLogsTarea.destroy();
									store.load();
								}
							});
						}
						else
						{
							Ext.Msg.alert('Alerta ',json.mensaje);
						}
					},
					failure: function(rec, op) {
						var json = Ext.JSON.decode(op.response.responseText);
						Ext.Msg.alert('Alerta ',json.mensaje);
					}
				});
			}	
		}
    });
    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winLogsTarea.destroy();
		}
    });
    
    formPanel3 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 250,
		width: 500,
		layout: 'fit',
                 store: store,
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		 columns:
		[					
			{
			  id: 'id_detalle',
			  header: 'IdDetalle',
			  dataIndex: 'id_detalle',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'id_tarea',
			  header: 'IdTarea',
			  dataIndex: 'id_tarea',
			  hidden: true,
			  hideable: false
			}
			
		 ]
			
		
	 });  
	
    winLogsTarea = Ext.create('Ext.window.Window', {
		title: 'Logs de Tarea',
		modal: true,
		width: 500,
		height: 280,
		resizable: false,
		layout: 'fit',
		items: [formPanel3],
		buttonAlign: 'center',
		buttons:[btnguardar2,btncancelar2]
    }).show(); 
}


function valorArray(array){
	     
      sumador = "";
      if(array.length == 0)return 0;
      else{
	  for(i=0;i<array.length;i++){
	   
		sumador += array[i];
	    
	  }
	  
      }
      
      return parseInt(sumador);
  
}

function validarFechaTareaReprogramada(fechaInicio, horaInicio, fechaFin, horaFin) 
{         
    if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 0) 
    {
        Ext.Msg.alert('Alerta ', 'No puede finalizar la tarea, aun no se cumple la fecha de planificacion');
        return -1;
    } 
    else 
    {
        //son fechas iguales por tanto se valida la diferencia por horas
        if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 2) 
        {     
            if (validate_fechaMayorQue(horaInicio, horaFin, 'hora') === 0) 
            {
                Ext.Msg.alert('Alerta ', 'No puede finalizar la tarea, aun no se cumple la hora de planificacion');
                return -1;
            }
        } 
        else
        {
            return 1;
        }
    }
}

function getTiempoTotal(fechaInicio,horaInicio,fechaFin, horaFin,tipo)
{ 
    
   if(tipo === 'fecha') 
   {
        if (validate_fechaMayorQue(horaInicio, horaFin, 'hora') === 1) 
        {
            if (validate_fechaMayorQue(fechaInicio, fechaFin, tipo) === 0) 
            {
                Ext.Msg.alert('Alerta ', 'Fecha de Cierre no puede ser menor a la Fecha de Apertura');
                return -1;
            }
        } 
        else 
        {
            Ext.Msg.alert('Alerta ', 'La Hora de cierre es menro que la fecha de Apertura, corrija');
            return -1;
        }
    }
    if (tipo === 'hora') 
    {
        if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 2) 
        {
            if (validate_fechaMayorQue(horaInicio, horaFin, tipo) === 0) 
            {
                Ext.Msg.alert('Alerta ', 'Hora de Cierre no puede ser menor a la Hora de Apertura');
                return -1;
            }
        } 
        else if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 0) 
        {
            Ext.Msg.alert('Alerta ', 'La Fecha de cierre es menor que la fecha de Apertura, corrija');
            return -1;
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////////7
    
	var fechas = fechaInicio.split("-");

    //FECHAS - DETERMINAR DIAS
    fecha = (String)(fechas[2] + "/" + fechas[1] + "/" + fechas[0]);

    var fechaFinS = fechaFin.split("-");

    fechaF = (String)(fechaFinS[2] + "/" + fechaFinS[1] + "/" + fechaFinS[0]);

    var fechaInicio = new Date(fecha);
    var fechaFin = new Date(fechaF);

    var horaFin = horaFin;

    var horasTotalesInicio = horaInicio.split(":");
    var horasTotalesFin    = horaFin.split(":");

    var difFecha = fechaFin - fechaInicio;

    //     (((fechaResta/1000)))          --> Segundos
    //     (((fechaResta/1000)/60))       --> Minutos
    //     (((fechaResta/1000)/60)/60)    --> Horas
    //     (((fechaResta/1000)/60)/60)/24 --> Días	

    var diasTotales = Math.ceil((((difFecha / 1000) / 60) / 60) / 24); //dias totales        

    var minutosAdjudicar = "";

    if (diasTotales > 0) 
    {
        diasTotales = diasTotales - 1;

        minutosInicio = (24 * 60) - (parseInt(horasTotalesInicio[0]) * 60 + parseInt(horasTotalesInicio[1]));
        minutosFin = (parseInt(horasTotalesFin[0]) * 60 + parseInt(horasTotalesFin[1]));

        minutosTotales = minutosInicio + minutosFin;

        minutosAdjudicar = (diasTotales * 1440) + minutosTotales; //minutos						
    }
    else
    {
        minutosInicio = parseInt(horasTotalesInicio[0]) * 60 + parseInt(horasTotalesInicio[1]);
        minutosFin = parseInt(horasTotalesFin[0]) * 60 + parseInt(horasTotalesFin[1]);

        minutosAdjudicar = minutosFin - minutosInicio;
    }

    return  minutosAdjudicar < 0 ? minutosAdjudicar * -1 : minutosAdjudicar;	        
}

function validate_fechaMayorQue(fechaInicial,fechaFinal,tipo)
{        
      if (tipo === 'fecha') 
      {
        valuesStart = fechaInicial.split("-");
        valuesEnd   = fechaFinal.split("-");
       
       //Si los años son diferentes
        if (parseInt(valuesStart[2]) !== parseInt(valuesEnd[2]))
        {
            if (parseInt(valuesStart[2]) > parseInt(valuesEnd[2])) // Si el año de programacion es mayor lanza el mensaje
            {
                return 0;
            }
            else //Si el año de promagramacion ya paso por ende se puede finalizar la tarea
            {
                return 1;
            }
        }
        //Si el año es igual se valida meses y luego por dias
        else if (parseInt(valuesStart[1]) > parseInt(valuesEnd[1]))
        {
            return 0;
        }
        else if (parseInt(valuesStart[1]) < parseInt(valuesEnd[1]))//Significa que el dia de ejecucion ya paso y puede ser finalizado
        {  
            return 1;
        }
        else if (parseInt(valuesStart[0]) > parseInt(valuesEnd[0]))
        {
            return 0;
        }
        else //Se valida la diferencia entre dias
        {
            if (parseInt(valuesStart[0]) === parseInt(valuesEnd[0]))
            {
                return 2;
            }
            else if (parseInt(valuesStart[0]) < parseInt(valuesEnd[0]))
            {
                return 1;
            }
        }
    } 
    else 
    {
        valuesStart = fechaInicial.split(":");
        valuesEnd   = fechaFinal.split(":");

        if (parseInt(valuesStart[0]) > parseInt(valuesEnd[0]))
            return 0;

        else if (parseInt(valuesStart[0]) === parseInt(valuesEnd[0])) //es la misma hora
        {
            if (parseInt(valuesStart[1]) > parseInt(valuesEnd[1]))
            {
                return 0;
            }
            else
            {
                return 1;
            }
        }
        else
        {
            return 1;
        }
    }
}

function addZero(num)
{
    (String(num).length < 2) ? num = String("0" + num) :  num = String(num);
    return num;        
}
function getDate(date) {

    if (date === null)
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
function getHour(hour) {

    if (hour === null)
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

function agregarSeguimiento(id_caso,nombre_tarea,id_detalle,registroInterno){

    mostrarIngresoRegistro = true;
    if(registroInterno == "S")
    {
        mostrarIngresoRegistro = false;
    }

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
               // json_tareas = obtenerTareas();
                var valorSeguimiento = Ext.getCmp('seguimiento').value;
                var registroInterno  = Ext.getCmp('seguimientoInterno').value;
                winSeguimiento.destroy();
                conn.request({
                    method: 'POST',
                    params :{
                        id_caso: id_caso,
                        id_detalle: id_detalle,
                        seguimiento: valorSeguimiento,
                        registroInterno: registroInterno
                    },
                    url: '../info_caso/ingresarSeguimiento',
                    success: function(response){
                        var json = Ext.JSON.decode(response.responseText);
                        if(json.mensaje != "cerrada")
                        {
                            Ext.Msg.alert('Mensaje','Se ingreso el seguimiento.', function(btn){
                                if(btn=='ok'){
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                        }
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
                        value: nombre_tarea
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Seguimiento:',
                        id: 'seguimiento',
                        name: 'seguimiento',
                        rows: 7,
                        cols: 70
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Registro Interno',
                        id: 'seguimientoInterno',
                        hidden: mostrarIngresoRegistro,
                        value: 'N',
                        store: [
                            ['N', 'No'],
                            ['S', 'Si']
                        ],
                        width: 200
                    }
                ]
            }]
         });
    winSeguimiento = Ext.create('Ext.window.Window', {
            title: 'Ingresar Seguimiento',
            modal: true,
            width: 660,
            height: 300,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show(); 
    
}



function verSeguimientoTarea(id_detalle){
  
  
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
				id_detalle: id_detalle			
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

function setearCombo(tipo)
{

    if(tipo == "1")
    {        
        Ext.getCmp('combo_empleados').value = "";
        Ext.getCmp('combo_empleados').setRawValue("");
        cuadrillaAsignada = "S";
        var myData_message = storeAsignaEmpleado.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeAsignaEmpleado.getProxy().getReader().jsonData.myMetaData.boolSuccess;
                
        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Mensaje ', myData_message);
            Ext.getCmp('combo_empleados').setDisabled(true); 
            Ext.getCmp('comboCuadrilla').setDisabled(true); 
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
        }
        else
        {
            if (storeAsignaEmpleado.getCount() <= 1 && myData_boolSuccess != "1") {
                Ext.Msg.alert('Mensaje ', "No existen empleados asignados para este departamento.");
                Ext.getCmp('combo_empleados').setDisabled(true);  
                Ext.getCmp('comboCuadrilla').setDisabled(true); 
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
            }
            else
            {
                Ext.getCmp('combo_empleados').setDisabled(false);
                Ext.getCmp('comboCuadrilla').setDisabled(true); 
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
                valorAsignacion = "empleado";  
            }
            
        }
    }
    else if (tipo == "2")
    {     
        var myData_message = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.boolSuccess;                 

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('combo_empleados').setDisabled(true); 
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }                
        else
        {                
            Ext.getCmp('comboCuadrilla').setDisabled(false);
            Ext.getCmp('combo_empleados').setDisabled(true);
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
            valorAsignacion = "cuadrilla";
        }
    } 
    else
    {
        cuadrillaAsignada = "S";
        var myData_message = storeContratista.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeContratista.getProxy().getReader().jsonData.myMetaData.boolSuccess;

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('combo_empleados').setDisabled(true);
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }
        else
        {
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('combo_empleados').setDisabled(true);
            Ext.getCmp('combo_empleados').setValue("");
            Ext.getCmp('comboContratista').setDisabled(false);
            valorAsignacion = "contratista";
        }
    }

    Ext.getCmp('cbox_responder').setValue(false);
}


function setearUsuarioResponder(id_detalle)
{
    var intCiudad               = "";
    var intIdDepartamento       = "";
    var strPrefijoEmpresa       = "";
    var strUsuarioRespuesta     = "";
    var strPrefijoSession       = "";
    var intCiudadSession        = "";
    var intDepartamentoSession  = "";

    var connDatosRespuestaAutomatica = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.MessageBox.show({
                       msg: 'Consultando datos para responder la tarea...',
                       progressText: 'Cargando...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connDatosRespuestaAutomatica.request({
        url: url_datosRespuestaTarea,
        method: 'post',
        params:
            {
                intDetalleId : id_detalle
            },
        success: function(response){
            var text = Ext.decode(response.responseText);

                strPrefijoEmpresa       = text.strPrefijoEmpresa;
                intCiudad               = text.intIdCiudad;
                intIdDepartamento       = text.intDepartamento;
                strUsuarioRespuesta     = text.strUsuarioRespuesta;
                strPrefijoSession       = text.strPrefijoSession;
                intCiudadSession        = text.intCiudadSession;
                intDepartamentoSession  = text.intDepartamentoSession;  

                if(Ext.getCmp('cbox_responder').checked == true)
                {
                    //Cargo datos del ultimo responsable
                    Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresa);

                    storeCiudades.proxy.extraParams = {empresa: strPrefijoEmpresa};
                    storeCiudades.load();
                    Ext.getCmp('comboCiudad').setValue(intCiudad);

                    storeDepartamentosCiudad.proxy.extraParams = {id_canton: intCiudad, empresa: strPrefijoEmpresa};
                    storeDepartamentosCiudad.load();
                    Ext.getCmp('comboDepartamento').setValue(intIdDepartamento);

                    storeAsignaEmpleado.proxy.extraParams = {id_canton: intCiudad, empresa: strPrefijoEmpresa, id_departamento: intIdDepartamento};
                    storeAsignaEmpleado.load();
                    Ext.getCmp('combo_empleados').setValue(strUsuarioRespuesta);

                    Ext.getCmp('combo_empleados').setDisabled(false);

                    document.getElementById('radio_e').disabled = false;
                    document.getElementById('radio_c').disabled = false;
                    document.getElementById('radio_co').disabled = false;
                    document.getElementById('radio_e').checked = true;
                    document.getElementById('radio_c').checked = false;
                    document.getElementById('radio_co').checked = false;
                }
                else
                {
                    //Cargo datos del departamento en session
                    Ext.getCmp('comboEmpresa').setValue(strPrefijoSession);

                    storeCiudades.proxy.extraParams = {empresa: strPrefijoSession};
                    storeCiudades.load(function() {
                        Ext.getCmp('comboCiudad').setValue(Number(intCiudad));
                    });

                    storeDepartamentosCiudad.proxy.extraParams = {id_canton: intCiudadSession,empresa: strPrefijoSession};
                    storeDepartamentosCiudad.load(function() {
                        Ext.getCmp('comboDepartamento').setValue(Number(intDepartamentoSession));
                    });

                    storeAsignaEmpleado.proxy.extraParams = {id_canton: intCiudadSession, empresa: strPrefijoSession,
                                                             id_departamento: intDepartamentoSession};
                    storeAsignaEmpleado.load();

                    storeCuadrillas.proxy.extraParams = { departamento:intDepartamentoSession,estado: 'Eliminado',origenD: 'Departamento'};
                    storeCuadrillas.load();

                    Ext.getCmp('combo_empleados').value = "";
                    Ext.getCmp('combo_empleados').setRawValue("");

                    Ext.getCmp('combo_empleados').setDisabled(true);
                }

                Ext.getCmp('comboCuadrilla').value = "";
                Ext.getCmp('comboCuadrilla').setRawValue("");

                Ext.getCmp('comboContratista').value = "";
                Ext.getCmp('comboContratista').setRawValue("");

                Ext.getCmp('comboEmpresa').setDisabled(false);
                Ext.getCmp('comboCiudad').setDisabled(false);
                Ext.getCmp('comboDepartamento').setDisabled(false);
                Ext.getCmp('comboCuadrilla').setDisabled(true);
                Ext.getCmp('comboContratista').setDisabled(true);
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}



function subirMultipleAdjuntosTarea(rec)
{
    var id_tarea = rec.get('id_detalle');
    var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Procesando...');
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
    
    var panelMultiupload = Ext.create('widget.multiupload',{ fileslist: [] });
    var formPanel = Ext.create('Ext.form.Panel',
     {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },
        items: [panelMultiupload],
        buttons: [{
            text: 'Subir',
            handler: function()
            {
                var form = this.up('form').getForm();
                if(form.isValid())
                {
                    if(numArchivosSubidos>0)
                    {
                        form.submit({
                            url: url_multipleFileUpload,
                            params :{
                              IdTarea    : id_tarea,
                              origenTarea: 'S',
                              subirEnMsNfs: 'S'
                            },
                            waitMsg: 'Procesando Archivo...',
                            success: function(fp, o)
                            {
                                Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                                    if(btn=='ok')
                                    {
                                        numArchivosSubidos=0;
                                        win.destroy();
                                          
                                    }
                                });
                            },
                            failure: function(fp, o) {
                              Ext.Msg.alert("Alerta",o.result.respuesta);
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert("Mensaje", "No existen archivos para subir", function(btn){
                            if(btn=='ok')
                            {
                                numArchivosSubidos=0;
                                win.destroy();
                            }
                        });
                    }
                    
                }
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                numArchivosSubidos=0;
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Archivos Tarea',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
}


function eliminarAdjunto(storeDocumentosCaso,idDocumento)
{
    Ext.Msg.confirm('Alerta','Se eliminará el documento. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
              Ext.MessageBox.wait("Eliminando Archivo...", 'Por favor espere'); 
              Ext.Ajax.request({
                url: url_eliminar_adjunto,
                method: 'post',
                params: { id:idDocumento },
                success: function(response)
                {
                    Ext.MessageBox.hide();
                    var json = Ext.JSON.decode(response.responseText);                                                                                                

                    if (json.status=="OK")
                    {
                        Ext.MessageBox.show({
                            title: "Información",
                            cls: 'msg_floating',
                            msg: json.message,
                            icon: Ext.MessageBox.INFO,
                            buttons: Ext.Msg.OK,
                            fn: function(buttonId) 
                            {
                                if (buttonId === "ok")
                                {
                                    storeDocumentosCaso.load();
                                }
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.show(
                        {
                           title: 'Error',
                           width: 300,
                           cls: 'msg_floating',
                           icon: Ext.MessageBox.ERROR,
                           msg: json.message
                        });
                    }
                  },
                  failure: function(response)
                  {
                    Ext.MessageBox.hide();
                    var json = Ext.JSON.decode(response.responseText);
                    Ext.Msg.show(
                    {
                       title: 'Error',
                       width: 300,
                       cls: 'msg_floating',
                       icon: Ext.MessageBox.ERROR,
                       msg: json.message
                    });
                  }
              });
        }
    });
}

/**
 * Documentacion para funcion setPuntoSesionByLogin
 * Función que permite setear el punto en sesión con el punto cuyo login es el enviado como parámetro.
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 12/04/2018
 * @param   strLogin Login del punto que estará en sesión
 */
function setPuntoSesionByLogin(strLogin)
{
    if(confirm("Est\u00E1 seguro(a) de cambiar el punto en sesi\u00F3n ?"))
    {
        
            Ext.Ajax.request({
                url: urlSetPtoSessionByLogin,
                method: 'post',
                params: { 
                    strLogin: strLogin
                },
                success: function(response){
                    var strRespuesta = response.responseText;
                    if(strRespuesta !== "Error")
                    {
                        window.open('/comercial/punto/'+strRespuesta+'/Cliente/show', '_blank');
                    }else{
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error al setear punto en sesi\u00F3n.');
                    }
                },
                failure: function(result)
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error',result.responseText);
                }
            });       
        
        	
    }
}

function halAsigna(accion,fechaActual,idDetalle,idTarea,numeroTarea,data)
{
    nIntentos = 0;

    var checked = (data.atenderAntes !== 'NO' ? 'checked' : '');

    // Variables locales
    var idSugerencia, fechaEjecucion, horaEjecucion, fechaVigencia,
        segTiempoVigencia, fechaTiempoVigencia, horaTiempoVigencia,
        tipoAsignado, idAsignado, empleadoAsignado, cuadrillaAsignada,
        departamentoAsignado;

    var radbuttonHal = '<div align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                        &nbsp;<input type="radio" onchange="opcionesHal(1,'+idDetalle+');\n\
                        " value="halDice" name="radioCuadrilla" id="radio_a">&nbsp;\n\
                        Mejor Opci&oacuten&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" \n\
                        onchange="opcionesHal(2,'+idDetalle+');" value="halSugiere" \n\
                        name="radioCuadrilla" id="radio_b">&nbsp;Sugerencias&nbsp;&nbsp;&nbsp;&nbsp;</div>';

    var radioButtonAA = '<div align="left" id="divAtenderAntes" style="display:none;">\n\
                            <label><b>¿De existir disponibilidad, el cliente desea ser atendido antes\n\
                                       de la fecha acordada?</b></label>&nbsp;&nbsp;&nbsp;\n\
                            <input type="checkbox" id="cboxAtenderAntes" name="cboxAtenderAntes" '+checked+' />\n\
                        </div>';

    /* Componente para los radio button */
    radioAtenderAntes = Ext.create('Ext.Component',
    {
       html    : radioButtonAA,
       width   : 600,
       padding : 10,
       style   : { color: '#000000' }
    });

    /* Componente para los radio button */
    radiosTiposHal = Ext.create('Ext.Component',
    {
       html    : radbuttonHal,
       width   : 600,
       padding : 10,
       style   : { color: '#000000' }
    });

    FieldNotificacionHal = new Ext.form.field.Display(
    {
         xtype : 'displayfield',
         id    : 'notificacionHal',
         name  : 'notificacionHal'
    });

    /* Store que obtiene las sugerencias de hal */
    storeIntervalosHal = new Ext.data.Store(
    {
        pageSize : 1000,
        total    : 'total',
        async    : false,
        proxy:
        {
            type : 'ajax',
            url  : url_getIntervalosHal,
            reader:
            {
                type: 'json',
                root: 'intervalos',
                totalProperty: 'total'
            }
        },
        fields:
        [
            {name: 'idSugerencia'      , mapping: 'idSugerencia'},
            {name: 'fecha'             , mapping: 'fecha'},
            {name: 'horaIni'           , mapping: 'horaIni'},
            {name: 'fechaTexto'        , mapping: 'fechaTexto'},
            {name: 'segTiempoVigencia' , mapping: 'segTiempoVigencia'},
            {name: 'fechaVigencia'     , mapping: 'fechaVigencia'},
            {name: 'horaVigencia'      , mapping: 'horaVigencia'}
        ],
        listeners:
        {
            load: function(sender, node, records)
            {
                if (tipoHal === 2) {
                    Ext.getCmp('nueva_sugerencia').setDisabled(false);
                }

                var boolExiste = (typeof sender.getProxy().getReader().rawData === 'undefined') ? false :
                                 (typeof sender.getProxy().getReader().rawData.mensaje === 'undefined') ? false : true;

                if (boolExiste) {
                    var mensaje = sender.getProxy().getReader().rawData.mensaje;
                    if (mensaje !== null || mensaje !== '') {
                        Ext.getCmp('notificacionHal').setValue(mensaje);
                    } else {
                        Ext.getCmp('notificacionHal').setValue(null);
                    }
                } else {
                    var mensaje = '<b style="color:red";>Error interno, Comunique a Sistemas..!!</b>';
                    Ext.getCmp('notificacionHal').setValue(mensaje);
                }

                formPanelHal.refresh;
            }
        }
    });

    /* Model para la seleccion de las sugerencias  */
    selModelHalSugiere = Ext.create('Ext.selection.CheckboxModel',
    {
        mode: 'SINGLE'
    });

    /* Componente para fecha Sugerida por el cliente */
    FieldFechaSugerida = new Ext.form.field.Display(
    {
        xtype      : 'displayfield',
        fieldLabel : 'Fecha Solicitada',
        width      :  90,
        padding    : '6px'
    });

    DTFechaSugerida = new Ext.form.DateField(
    {
        id       : 'fecha_sugerida',
        name     : 'fecha_sugerida',
        xtype    : 'datefield',
        format   : 'Y-m-d',
        editable : false,
        minValue : fechaActual,
        width    : 120
    });

    /* Componente para la hora Sugerida por el cliente */
    FieldHoraSugerida = new Ext.form.field.Display(
    {
        xtype      : 'displayfield',
        fieldLabel : 'Hora',
        width      : 32,
        padding    : '3px'
    });

    TMHoraSugerida = new Ext.form.TimeField(
    {
        xtype     : 'timefield',
        format    : 'H:i',
        id        : 'hora_sugerida',
        name      : 'hora_sugerida',
        minValue  : '00:00',
        maxValue  : '23:59',
        increment : 15,
        editable  : false,
        width     : 75
    });

    /* Grid Hal Sugiere */
    gridHalSugiere = Ext.create('Ext.grid.Panel',
    {
        width       : 650,
        height      : 240,
        collapsible : false,
        title       : 'Sugerencias',
        id          : 'gridHalSugiere',
        selModel    : selModelHalSugiere,
        store       : storeIntervalosHal,
        loadMask    : true,
        frame       : true,
        forceFit    : true,
        autoRender  : true,
        columnLines : true,
        enableColumnResize : false,
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
        },
        dockedItems: [
        {
            xtype : 'toolbar',
            dock  : 'top',
            align : '->',
            items : [
                 FieldFechaSugerida,
                 DTFechaSugerida,
                 '-',
                 FieldHoraSugerida,
                 TMHoraSugerida,
                { xtype: 'tbfill' },
                {
                    text     : 'Nueva Sugerencia',
                    iconCls  : 'icon_aprobar',
                    disabled : true,
                    itemId   : 'automatica',
                    scope    : this,
                    id       : 'nueva_sugerencia',
                    name     : 'nueva_sugerencia',
                    handler  : function()
                    {
                        Ext.getCmp('nueva_sugerencia').setDisabled(true);
                        nIntentos = nIntentos + 1;
                        storeIntervalosHal.getProxy().extraParams.idDetalle     = idDetalle;
                        storeIntervalosHal.getProxy().extraParams.nIntentos     = nIntentos;
                        storeIntervalosHal.getProxy().extraParams.fechaSugerida = Ext.getCmp('fecha_sugerida').value;
                        storeIntervalosHal.getProxy().extraParams.horaSugerida  = Ext.getCmp('hora_sugerida').value;
                        storeIntervalosHal.getProxy().extraParams.tipoHal       = tipoHal;
                        storeIntervalosHal.getProxy().extraParams.solicitante   = Ext.getCmp('cmbSolicitante').value;
                        storeIntervalosHal.load();
                    }
                }
            ]
        }],
        viewConfig:
        {
            enableTextSelection: true,
            stripeRows: true,
            emptyText: 'Sin datos para mostrar, Por favor leer la Notificación HAL'
        },
        columns:
            [
                {
                    id: 'id_Sugerencia',
                    header: "id_Sugerencia",
                    dataIndex: 'idSugerencia',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'fecha_disponible',
                    header: 'Fecha Disponible',
                    dataIndex: 'fecha',
                    width: 90
                },
                {
                    id: 'horaIni_disponible',
                    header: 'Hora Inicio',
                    dataIndex: 'horaIni',
                    width: 60
                },
                {
                    id: 'fechaTexto',
                    header: 'Mensaje',
                    dataIndex: 'fechaTexto',
                    width: 310
                },
                {
                    id: 'tiempo_reserva',
                    header: 'Reserva (Seg)',
                    dataIndex: 'segTiempoVigencia',
                    width: 80
                },
                {
                    id: 'hora_fin_reserva',
                    header: 'Hora Fin Reserva',
                    dataIndex: 'horaVigencia',
                    width: 130,
                    hidden: true
                },
                {
                    id: 'fecha_reserva',
                    dataIndex: 'fechaVigencia',
                    hidden: true,
                    hideable: false
                }
            ]
    });

    /* Inavilitamos el gridHalSugiere */
    gridHalSugiere.setVisible(false);

    /* Grid hal dice */
    gridHalDice = Ext.create('Ext.grid.Panel',
    {
        title    : 'Sugerencia de Hal',
        id       : 'gridHalDice',
        width    : 650,
        height   : 100,
        store    : storeIntervalosHal,
        loadMask : true,
        frame    : true,
        forceFit : true,
        autoRender : true,
        enableColumnResize : false,
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
        },
        viewConfig:
        {
            enableTextSelection: true,
            stripeRows: true,
            emptyText: 'Sin datos para mostrar, Por favor leer la Notificación HAL'
        },
        columns:
            [
                {
                    id: 'id_Sugerencia_hal_dice',
                    header: "id_Sugerencia",
                    dataIndex: 'idSugerencia',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'fecha_disponible_hal_dice',
                    header: 'Fecha Disponible',
                    dataIndex: 'fecha',
                    width: 90
                },
                {
                    id: 'horaIni_disponible_hal_dice',
                    header: 'Hora Inicio',
                    dataIndex: 'horaIni',
                    width: 60
                },
                {
                    id: 'fechaTexto_hal_dice',
                    header: 'Mensaje',
                    dataIndex: 'fechaTexto',
                    width: 310
                },
                {
                    id: 'tiempo_reserva_hal_dice',
                    header: 'Reserva (Seg)',
                    dataIndex: 'segTiempoVigencia',
                    width: 80
                },
                {
                    id: 'hora_fin_reserva_hal_dice',
                    header: 'Hora Fin Reserva',
                    dataIndex: 'horaVigencia',
                    width: 130,
                    hidden: true
                },
                {
                    id: 'fecha_reserva_hal_dice',
                    dataIndex: 'fechaVigencia',
                    hidden: true,
                    hideable: false
                }
            ]
    });

    /* Inavilitamos el gridHalDice */
    gridHalDice.setVisible(false);

    /* Panel principal para la comunicacion con hal */
    formPanelHal = Ext.create('Ext.form.Panel',
    {
        title         : (accion === 'reasignar' ? "HAL" : ""),
        bodyPadding   : 5,
        waitMsgTarget : true,
        fieldDefaults :
        {
                labelAlign : 'left',
                labelWidth :  200,
                msgTarget  : 'side'
        },
        items:
        [
            {
                xtype : 'fieldset',
                title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue";>Notificación HAL</b>',
                items :
                [
                    FieldNotificacionHal
                ]
            },
            {
                xtype : 'fieldset',
                title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Asignación de Tareas HAL</b>',
                items :
                [
                    {
                        xtype      : 'combobox',
                        id         : 'cmbSolicitante',
                        fieldLabel : accion === 'reprogramar' ? '<b>Solicita Reprogramar</b>' : '<b>Solicita Reasignar</b>',
                        value      : 'S',
                        store      : [
                            ['S' , 'Seleccione..'],
                            ['C' , 'Cliente'],
                            ['E' , 'Empresa']
                        ],
                        listeners : {
                            change : function() {
                                nIntentos = 0;
                                gridHalSugiere.setVisible(false);
                                gridHalDice.setVisible(false);
                                document.getElementById('radio_a').checked = false;
                                document.getElementById('radio_b').checked = false;
                                document.getElementById('divAtenderAntes').style.display = 'none';
                                formPanelHal.doLayout();
                            }
                        }
                    },
                   radiosTiposHal,
                   gridHalDice,
                   gridHalSugiere,
                   radioAtenderAntes
                ]
            }
        ],
        buttonAlign: 'center',
        buttons:
        [
            {
                text     : 'Guardar',
                formBind : true,
                handler  : function()
                {
                    var atenderAntes = "N";
                    var solicitante  = Ext.getCmp('cmbSolicitante').value;

                    if(document.getElementById('cboxAtenderAntes').checked)
                    {
                        atenderAntes = "S";
                    }

                    if (!seleccionaHal)
                    {
                        Ext.Msg.alert("Alerta","Debe escoger una opción de Hal...!!");
                        return;
                    }

                    if (tipoHal == 1)
                    {
                        if (gridHalDice.getStore().data.items.length < 1)
                        {
                            Ext.Msg.alert("Alerta","No se obtuvieron sugerencias de hal...!!");
                            return;
                        }

                        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
                        {
                            idSugerencia   = gridHalDice.getStore().data.items[i].data.idSugerencia;
                            fechaEjecucion = gridHalDice.getStore().data.items[i].data.fecha;
                            horaEjecucion  = gridHalDice.getStore().data.items[i].data.horaIni;
                            fechaVigencia  = gridHalDice.getStore().data.items[i].data.fechaVigencia;
                        }
                    }
                    else
                    {
                        if (gridHalSugiere.getStore().data.items.length < 1)
                        {
                            Ext.Msg.alert("Alerta","No se obtuvieron sugerencias de hal...!!");
                            return;
                        }

                        if (selModelHalSugiere.getSelection().length < 1)
                        {
                            Ext.Msg.alert("Alerta","Debe escoger una fecha...!!");
                            return;
                        }

                        for (var i = 0; i < selModelHalSugiere.getSelection().length; ++i)
                        {
                            idSugerencia   = selModelHalSugiere.getSelection()[i].data.idSugerencia;
                            fechaEjecucion = selModelHalSugiere.getSelection()[i].data.fecha;
                            horaEjecucion  = selModelHalSugiere.getSelection()[i].data.horaIni;
                            fechaVigencia  = selModelHalSugiere.getSelection()[i].data.fechaVigencia;
                        }
                    }

                    Ext.MessageBox.wait("Verificando datos...");
                    Ext.Ajax.request(
                    {
                        url    :  url_confirmarReservaHal,
                        method : 'post',
                        params :
                        {
                            idDetalle     : idDetalle,
                            idSugerencia  : idSugerencia,
                            fechaVigencia : fechaVigencia
                        },
                        success: function(response)
                        {
                            var responseJson = Ext.JSON.decode(response.responseText);

                            if (responseJson.success)
                            {
                                segTiempoVigencia   = responseJson.segTiempoVigencia;
                                fechaTiempoVigencia = responseJson.fechaTiempoVigencia;
                                horaTiempoVigencia  = responseJson.horaTiempoVigencia;

                                Ext.Ajax.request(
                                {
                                    url    :  url_confirmarSugerenciaHal,
                                    method : 'post',
                                    params :
                                    {
                                        idDetalle      : idDetalle,
                                        idComunicacion : numeroTarea,
                                        idSugerencia   : idSugerencia,
                                        atenderAntes   : atenderAntes,
                                        'solicitante'  : solicitante
                                    },
                                    success: function(response)
                                    {
                                        var responseJson = Ext.JSON.decode(response.responseText);

                                        if (responseJson.success)
                                        {
                                           tipoAsignado         = responseJson.tipoAsignado;
                                           idAsignado           = responseJson.idAsignado;
                                           fechaEjecucion       = responseJson.fecha;
                                           horaEjecucion        = responseJson.horaIni;
                                           empleadoAsignado     = responseJson.empleadoAsignado;
                                           cuadrillaAsignada    = responseJson.cuadrillaAsignada;
                                           departamentoAsignado = responseJson.departamentoAsignado;

                                            Ext.Msg.alert('Mensaje','Se asignó la tarea.', function(btn)
                                            {
                                                if(btn === 'ok')
                                                {
                                                    winReasignarTarea.destroy();
                                                    store.load();
                                                }
                                            });

                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Alert', responseJson.mensaje);
                                        }
                                    },
                                    failure: function(rec, op)
                                    {
                                        var responseJson = Ext.JSON.decode(op.response.responseText);
                                        Ext.Msg.alert('Alerta ', responseJson.mensaje);
                                        return;
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Alert', responseJson.mensaje);

                                if (responseJson.noDisponible)
                                {
                                    eliminarSeleccionHal(selModelHalSugiere,gridHalDice,tipoHal);
                                }
                            }
                        },
                        failure: function(rec, op)
                        {
                            var responseJson = Ext.JSON.decode(op.response.responseText);
                            Ext.Msg.alert('Alerta ', responseJson.mensaje);
                            return;
                        }
                    });
                }
            },
            {
                text: 'Cerrar',
                handler: function()
                {
                    nIntentos     = 0;
                    seleccionaHal = false;
                    Ext.getCmp('fecha_sugerida').setValue(null);
                    Ext.getCmp('hora_sugerida').setValue(null);
                    winReasignarTarea.destroy();

                    //Notificar a HAL al presionar botón Cerrar                    
                    if (gridHalDice.getStore().data.items.length > 0)
                    {
                        var idSugerencias = '';
                        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
                        {
                            idSugerencias = idSugerencias + gridHalDice.getStore().data.items[i].data.idSugerencia+'|';
                        }

                        Ext.Ajax.request
                        ({
                            url    :  urlNotificarCancelarHal,
                            method : 'post',
                            params :
                            {
                                idSugerencia  : idSugerencias
                            }
                        });
                    }
                }
            }
        ]
    });

    if (accion === 'reprogramar') {
        winReasignarTarea = Ext.create('Ext.window.Window', {
                title    : 'Reprogramar Tarea',
                modal    : true,
                closable : false,
                width    : 700,
                layout   : 'fit',
                items    : [formPanelHal]
        }).show();
        elWinReasignarTarea = winReasignarTarea.getEl();
    } else {
        return formPanelHal;
    }
}

function opcionesHal(tipo,idDetalle)
{
    var solicitante = Ext.getCmp('cmbSolicitante').value;

    if (Ext.isEmpty(solicitante) || solicitante === 'S') {
        Ext.Msg.alert('Alerta ', 'Debe escoger un <b>Solicitante</b>.');
        document.getElementById('radio_a').checked = false;
        document.getElementById('radio_b').checked = false;
        return;
    }

    if (tipo === 1)
    {
        storeIntervalosHal.getProxy().extraParams.nIntentos = 1;
        gridHalSugiere.setVisible(false);
        gridHalDice.setVisible(true);
        formPanelHal.doLayout();
    }
    else
    {
        nIntentos = nIntentos + 1;
        storeIntervalosHal.getProxy().extraParams.nIntentos = nIntentos;
        gridHalSugiere.setVisible(true);
        gridHalDice.setVisible(false);
        formPanelHal.doLayout();
    }

    document.getElementById('divAtenderAntes').style.display = 'block';
    Ext.getCmp('nueva_sugerencia').setDisabled(true);
    tipoHal       = tipo;
    seleccionaHal = true;
    storeIntervalosHal.removeAll();
    storeIntervalosHal.getProxy().extraParams.idDetalle     = idDetalle;
    storeIntervalosHal.getProxy().extraParams.fechaSugerida = null;
    storeIntervalosHal.getProxy().extraParams.horaSugerida  = null;
    storeIntervalosHal.getProxy().extraParams.solicitante   = solicitante;
    storeIntervalosHal.getProxy().extraParams.tipoHal       = tipoHal;
    storeIntervalosHal.load();
    Ext.getCmp('fecha_sugerida').setValue(null);
    Ext.getCmp('hora_sugerida').setValue(null);
}

function eliminarSeleccionHal(selModelHalSugiere,gridHalDice,tipoHal)
{
    if (tipoHal === 2)
    {
        for (var i = 0; i < selModelHalSugiere.getSelection().length; ++i)
        {
            selModelHalSugiere.getStore().remove(selModelHalSugiere.getSelection()[i]);
        }
    }
    else
    {
        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
        {
            gridHalDice.getStore().remove(gridHalDice.getStore().data.items[i]);
        }
    }
}


function finalizarTareaRequest(arrayFin){

    var id_detalle          = arrayFin.id_detalle;
    var finalizaObservacion = arrayFin.observacion;
    var finalizaRadio       = arrayFin.esSolucion;
    var finalizaTiempo      = arrayFin.tiempo_total;
    var finalizaFeCierre    = arrayFin.tiempo_cierre;
    var finalizaHorCierre   = arrayFin.hora_cierre;
    var fechaEjecucion      = arrayFin.tiempo_ejecucion;
    var horaEjecucion       = arrayFin.hora_ejecucion;
    var clientes            = arrayFin.clientes;
    var finalizaComboTarea  = arrayFin.tarea;
    var tarea_final         = arrayFin.tarea_final;
    var longitudIncidente   = arrayFin.longitud;
    var latitudIncidente    = arrayFin.latitud;
    var longitudManga1		= arrayFin.longitudManga1;
    var latitudManga1		= arrayFin.latitudManga1;
    var longitudManga2		= arrayFin.longitudManga2;
    var latitudManga2		= arrayFin.latitudManga2;
    var duracionTarea  		= arrayFin.duracionTarea;
	var conn                = arrayFin.conn;
    var id_caso             = arrayFin.id_caso;
    var casoPerteneceTN     = arrayFin.casoPerteneceTN;
    var numeroTarea         =    arrayFin.numeroTarea;
    var nombre_tarea        =    arrayFin.nombre_tarea;
    var nombre_proceso      =    arrayFin.nombre_proceso;
    var asignado_nombre     =    arrayFin.asignado_nombre;
    var departamento_nombre =    arrayFin.departamento_nombre;
    var esInterdepartamental=    arrayFin.esInterdepartamental; 
    var idMotivoFinaliza    =    arrayFin.idMotivoFinaliza; 
    var idFinTarea          = arrayFin.idFinTarea;
    var boolFinalTareaAnterior = arrayFin.boolFinalTareaAnterior;
    var strGuardar             =  arrayFin.strGuardar;
    var strIndisponibilidadI   =  arrayFin.strIndisponibilidadI;
    var strTipoI               =  arrayFin.strTipoI;
    var intTiempoAfectacionI   =  arrayFin.intTiempoAfectacionI;
    var strMasivoI             =  arrayFin.strMasivoI;
    var intComboResponsableI   =  arrayFin.intComboResponsableI;
    var intClientesAfectadosI  =  arrayFin.intClientesAfectadosI;
    var strObservacionesI      =  arrayFin.strObservacionesI;
    var strOltI                =  arrayFin.strOltI;
    var strPuertoI             =  arrayFin.strPuertoI;
    var strCajaI               =  arrayFin.strCajaI;
    var strSplitterI           =  arrayFin.strSplitterI;


    winIndisponibilidadTarea.destroy();
    winFinalizarTarea.destroy();      

        Ext.MessageBox.wait("Guardando datos...");

        conn.request
        ({
            method: 'POST',
            params:
            {
                id_detalle: id_detalle,
                observacion: finalizaObservacion,
                esSolucion: finalizaRadio,
                tiempo_total: finalizaTiempo,
                tiempo_cierre: finalizaFeCierre,
                hora_cierre: finalizaHorCierre,
                tiempo_ejecucion: fechaEjecucion,
                hora_ejecucion: horaEjecucion,
                clientes: clientes,
                tarea: finalizaComboTarea,
                tarea_final: tarea_final,
                longitud: longitudIncidente,
                latitud: latitudIncidente,
                longitudManga1: longitudManga1,
                latitudManga1: latitudManga1,
                longitudManga2: longitudManga2,
                latitudManga2: latitudManga2,
                duracionTarea: duracionTarea,
                intIdDetalleHist : arrayFin.intIdDetalleHist,
                numeroTarea:         numeroTarea,
                nombre_tarea:        nombre_tarea,
                nombre_proceso:      nombre_proceso,
                asignado_nombre:     asignado_nombre,
                departamento_nombre: departamento_nombre,
                esInterdepartamental:esInterdepartamental,
                idMotivoFinaliza    : idMotivoFinaliza,
                idFinTarea          : idFinTarea, //jobedon
                boolFinalTareaAnterior : boolFinalTareaAnterior,
                strGuardar:            strGuardar,
		        strIndisponibilidadI:  strIndisponibilidadI,
            	strTipoI:              strTipoI,
            	intTiempoAfectacionI:  intTiempoAfectacionI,
            	strMasivoI:            strMasivoI,
            	intComboResponsableI:  intComboResponsableI,
            	intClientesAfectadosI: intClientesAfectadosI,
            	strObservacionesI:     strObservacionesI,
            	strOltI:               strOltI,
            	strPuertoI:            strPuertoI,
            	strCajaI:              strCajaI,
            	strSplitterI:          strSplitterI
            },
            url: url_finalizarTarea,
            timeout: 300000,
            success: function(response)
            {
                Ext.MessageBox.hide();

                var json = Ext.JSON.decode(response.responseText);

                if (!json.success && !json.seguirAccion) {
                    Ext.MessageBox.show({
                        closable   :  false  , multiline : false,
                        title      : 'Alerta', msg : json.mensaje,
                        buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                        buttonText : {ok: 'Cerrar'},
                        fn : function (button) {
                            if(button === 'ok') {
                                store.load();
                            }
                        }
                    });
                    return;
                }

                if (json.success)
                {
                    winIndisponibilidadTarea.destroy();
                    winFinalizarTarea.destroy(); 

                        Ext.Msg.alert('Mensaje', 'Se finalizó la tarea.',
                        function(btn)
                        {
                            if (btn == 'ok')
                            {
                                winIndisponibilidadTarea.destroy();
                                winFinalizarTarea.destroy(); 

                                store.load();
                                //Se determina analisis de cierre de caso siempre y cuando la tarea que finaliza
                                //pertenezca a un caso, caso contrario no verifica nada
                                if (id_caso !== 0)
                                {
                                    winIndisponibilidadTarea.destroy();
                                    winFinalizarTarea.destroy();

                                    store.load();
                                    //Se determina analisis de cierre de caso siempre y cuando la tarea que finaliza
                                    //pertenezca a un caso, caso contrario no verifica nada
                                    if (id_caso !== 0)
                                    {
                                        //Si ya no existen tareas abiertas y al menos una tarea que dio solucion al caso
                                        if (json.tareasAbiertas === 0 && json.tareasSolucionadas > 0 && json.presentar == "1")
                                        {
                                            store.load();

                                            //Se determina analisis de cierre de caso siempre y cuando la tarea que finaliza
                                            //pertenezca a un caso, caso contrario no verifica nada
                                            if (id_caso !== 0)
                                            {
                                                //Si ya no existen tareas abiertas y al menos una tarea que dio solucion al caso
                                                if(json.tareasAbiertas === 0 && json.tareasSolucionadas > 0 && json.presentar == "1"
                                                    && !casoPerteneceTN)
                                                {
                                                    obtenerDatosCasosCierre(id_caso, conn, false);
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ', json.mensaje);
                                    winIndisponibilidadTarea.destroy();
                                    winFinalizarTarea.destroy();

                                    store.load();
                                }
                            }
                        });
                }
                else
                {
                        Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                }  
            },
            failure: function(rec, op)
            {
                Ext.MessageBox.hide();

                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ', json.mensaje);
            }
    });
}
   
    
    

function obtenerTiempoAfectacionIndisponibilidadTarea(data)
{        

   // Ext.getCmp('tiempoAfectacion').setValue(data.id_tarea);
    
    var strIdDetalle = data.id_detalle;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
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
        url: url_getTiempoAfectacionIndisponibilidadTarea,
        method: 'post',
        params: 
            { 
                strIdDetalle : strIdDetalle
            },
        success: function(response){			
            Ext.getCmp('tiempoAfectacionTarea').setValue(Ext.decode(response.responseText));
          
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });      
      
}



function obtenerClientesAfectadosIndisponibilidadTarea(pIdPuerto, pIdCaja, pIdSplitter)
{        
    
    var nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
    var reemplazar = /, /gi;
    nombreOlt = nombreOlt.replace(reemplazar, "','");

    var idPuerto = pIdPuerto;
    var idCaja = pIdCaja;
    var idSplitter = pIdSplitter;
    var i;
    var j;

    // parametro puerto vacio, obtengo seleccionados
    if(idPuerto == ''){
        
        for (i = 0; i<comboPuertoTarea.valueModels.length; i++){

            for (j = 0; j<storePuertoTarea.data.items.length; j++){

                if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                    
                    if (idPuerto == ''){
                        idPuerto = comboPuertoTarea.valueModels[i].data.idInterface;
                    }else{
                        idPuerto = idPuerto + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                    }
                    break;

                }

            }

        }

    }


    // parametro caja vacio, obtengo seleccionados
    if(idCaja == ''){

        if(comboCajaTarea.valueModels != null){

            for (i = 0; i<comboCajaTarea.valueModels.length; i++){

                for (j = 0; j<storeCajaTarea.data.items.length; j++){

                    if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                        
                        if (idCaja == ''){
                            idCaja = comboCajaTarea.valueModels[i].data.idCaja;
                        }else{
                            idCaja = idCaja + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                        }
                        break;

                    }

                }

            }
            
        }

    }


    // parametro splitter vacio, obtengo seleccionados
    if(idSplitter == ''){

        if(comboSplitterTarea.valueModels != null){

            for (i = 0; i<comboSplitterTarea.valueModels.length; i++){

                for (j = 0; j<storeSplitterTarea.data.items.length; j++){

                    if (comboSplitterTarea.valueModels[i].data.idSplitter == storeSplitterTarea.data.items[j].data.idSplitter){
                        
                        if (idSplitter == ''){
                            idSplitter = comboSplitterTarea.valueModels[i].data.idSplitter;
                        }else{
                            idSplitter = idSplitter + ', ' + comboSplitterTarea.valueModels[i].data.idSplitter;
                        }
                        break;

                    }

                }

            }
            
        }
    }




    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
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
        url: url_getClientesAfectados,
        method: 'post',
        params: 
            { 
                nombreOlt : nombreOlt,
                idPuerto : idPuerto,
                idCaja : idCaja,
                idSplitter : idSplitter,
            },
        success: function(response){			
            Ext.getCmp('clientesAfectadosTarea').setValue(Ext.decode(response.responseText));
          
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });      
      
}

function validarSeleccionOltTarea(obj, storePuertoTarea)
{ 

    // si selecciono mas de 1, se bloquean los demas
    if(obj.indexOf(",") != -1){

        comboPuertoTarea.setValue("NO APLICA");
        comboPuertoTarea.setRawValue("NO APLICA");
        comboPuertoTarea.setDisabled(true);

        comboCajaTarea.setValue("NO APLICA");
        comboCajaTarea.setRawValue("NO APLICA");
        comboCajaTarea.setDisabled(true);

        comboSplitterTarea.setValue("NO APLICA");
        comboSplitterTarea.setRawValue("NO APLICA");
        comboSplitterTarea.setDisabled(true);

    // solo un olt
    }else{

        //comboPuerto.setValue("");
        //comboPuerto.setRawValue("Seleccione puerto");
        comboPuertoTarea.setDisabled(false);

        comboCajaTarea.setRawValue("");
        comboCajaTarea.setValue(""); 
        comboCajaTarea.setDisabled(false);

        comboSplitterTarea.setRawValue("");
        comboSplitterTarea.setValue("");
        comboSplitterTarea.setDisabled(false);

        if(obj.length > 0){
            storePuertoTarea.proxy.extraParams = {nombreOlt: nombreOlt};
            storePuertoTarea.load({params: {}});
        } 

    }

    obtenerClientesAfectadosIndisponibilidadTarea('', '', '');

}
