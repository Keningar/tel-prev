var storePlanesMantenimientos=null;
var storeMantenimientosPlanesMantenimientos=null;
var numTareasMantenimientoStore=0;

Ext.onReady(function()
{
    storePlanesMantenimientos = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetPlanesMantenimientos,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        
        fields:
        [
            {name: 'intIdPlanMantenimiento',     mapping: 'id_proceso'},
            {name: 'strNombrePlanMantenimiento', mapping: 'nombre_proceso'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeCasosXTransporte  = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetCasosXTransporte,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        extraParams: {
                idElemento: idElemento
        },
        fields:
        [
            {name: 'intIdCaso',     mapping: 'id_caso'},
            {name: 'strNumeroCaso', mapping: 'numero_caso'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeMantenimientosPlanMantenimiento = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetMantenimientosPlanMantenimiento,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        
        fields:
        [
            {name: 'intIdMantenimiento',     mapping: 'id_proceso'},
            {name: 'strNombreMantenimiento', mapping: 'nombre_proceso'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    
    storeContratistas  = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetContratistas,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        
        fields:
        [
            {name: 'intIdPerContratista',           mapping: 'idPersonaEmpresaRol'},
            {name: 'strNombreContratista',          mapping: 'Nombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });

    storePerAutorizadoPor  = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetPerAutorizadoPor,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        
        fields:
        [
            {name: 'intIdPerAutorizadoPor', mapping: 'idPersonaEmpresaRol'},
            {name: 'strPerAutorizadoPor',   mapping: 'nombreCompleto'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    
    var DTFechaDesde = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'fechaInicio',
            name:'fechaInicio',
            fieldLabel: '<b>Fecha Inicio</b>',
            editable: false,
            format: 'd/m/Y',
            value:new Date(),
            emptyText: "Seleccione",
            labelWidth: 200,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasOrdenTrabajo(cmp);
                }
            }
     });

    var DTFechaHasta = new Ext.form.DateField({
        xtype: 'datefield',
        id: 'fechaFin',
        name:'fechaFin',
        editable: false,
        fieldLabel: '<b>Fecha Fin</b>',
        format: 'd/m/Y',
        value:new Date(),
        anchor:'100%',
        emptyText: "Seleccione",
        labelWidth: 150,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarFechasOrdenTrabajo(cmp);
            }
        }
    });
    
    var formKmActual = new Ext.form.NumberField({
     id: 'kmActual',
     name: 'kmActual',
     anchor:'35%',
     fieldLabel: '<b>KM Actual</b>',
     maxLength: 6,
     labelWidth: 200,
     width: 400,
     autoCreate: {
         tag: 'input', type: 'text', size: '20', autocomplete:'off', maxlength: '10'
     } });

    var formNuevaOrdenTrabajo = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: '100%',
        renderTo: 'bloque_orden_trabajo_transporte',
        border: false,
        margin: 0,
        fieldDefaults: {
           labelAlign: 'left',
           msgTarget: 'side'
        },
        defaults: {
           margins: '0 0 10 0'
        },
        items: [
           {
               xtype: 'fieldset',
               title: '',
               defaultType: 'textfield',
               width: '100%',
               height: '100%',
               margin: 0,
               padding: '10 10 0 10',
               border: false,
               items:
               [
                   {
                       xtype: 'fieldset',
                       title: 'Información de la Orden de Trabajo',                       
                       width: '100%',
                       height: '100%',
                       margin: 0,
                       
                       items: 
                       [
                            {
                                xtype: 'checkboxfield',
                                fieldLabel: '<b>Generar con numeración</b>',
                                name: 'chkOTNumeracionActual',
                                id: 'chkOTNumeracionActual',
                                checked: true,
                                labelWidth: 200,
                                inputValue: '0'
                            },
                            {
                                layout: 'table',
                                border: false,
                                padding: '5 0',
                                items: 
                                [
                                    {
                                        width: 340,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:200,
                                        items: 
                                        [
                                            DTFechaDesde
                                        ]
                                    },
                                    {
                                        width: 200,
                                        layout: 'form',
                                        border: false,
                                        items: 
                                        [
                                            {
                                                xtype: 'displayfield'
                                            },
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                        width: 290,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:200,
                                        items: 
                                        [
                                            DTFechaHasta
                                        ]
                                    }
                                ]
                            },
                            {   
                                xtype: 'radiogroup',
                                labelWidth: 200,
                                fieldLabel: '<b>Tipo de Mantenimiento</b>',
                                items: [
                                    {
                                        xtype: 'radiofield',
                                        id: 'mantenimientoPreventivo',
                                        name: 'tipoMantenimiento',
                                        boxLabel: 'PREVENTIVO',
                                        checked: true,
                                        inputValue: 'PREVENTIVO'
                                    },
                                    {
                                        xtype: 'radiofield',
                                        id: 'mantenimientoCorrectivo',
                                        name: 'tipoMantenimiento',
                                        boxLabel: 'CORRECTIVO',
                                        inputValue: 'CORRECTIVO'
                                    }
                                ],
                                listeners: {
                                    change: function(field, newValue, oldValue) {
                                        var value = newValue.tipoMantenimiento;
                                        if (Ext.isArray(value)) {
                                            return;
                                        }
                                        if (value == 'PREVENTIVO') {
                                            Ext.getCmp('cmbCasoTransporte').hide();
                                            Ext.getCmp('cmbPlanMantenimiento').show();
                                            Ext.getCmp('cmbMantenimiento').show();
                                            document.getElementById("escogido_tipo_mantenimiento").value='PREVENTIVO';
                                            
                                        }
                                        else if (value == 'CORRECTIVO') {
                                            Ext.getCmp('cmbPlanMantenimiento').hide();
                                            Ext.getCmp('cmbMantenimiento').hide();
                                            Ext.getCmp('cmbCasoTransporte').show();
                                            document.getElementById("escogido_tipo_mantenimiento").value='CORRECTIVO';
                                        }
                                        storeTareasMantenimientoOCaso.loadData([],false);
                                    }
                                }
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Plan de Mantenimiento</b>',
                                id: 'cmbPlanMantenimiento',
                                name: 'cmbPlanMantenimiento',
                                store: storePlanesMantenimientos,
                                displayField: 'strNombrePlanMantenimiento',
                                valueField: 'intIdPlanMantenimiento',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200,
                                width: 450,
                                listeners: {

                                    change: function(cmp, newValue, oldValue) {
                                        var objExtraParams = storeMantenimientosPlanMantenimiento.proxy.extraParams;
                                        if(isNaN(cmp.getValue()) || (cmp.getValue()=='') || !cmp.getValue())
                                        {
                                            Ext.getCmp('cmbMantenimiento').disable();
                                            objExtraParams.idPlanMantenimiento = '';
                                            storeMantenimientosPlanMantenimiento.loadData([],false);

                                        }
                                        else
                                        {
                                            objExtraParams.idPlanMantenimiento = cmp.getValue();
                                            Ext.getCmp('cmbMantenimiento').enable();
                                            storeMantenimientosPlanMantenimiento.load();
                                        }
                                        
                                        storeTareasMantenimientoOCaso.loadData([],false);
                                    }
                                }
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Mantenimiento</b>',
                                id: 'cmbMantenimiento',
                                name: 'cmbMantenimiento',
                                store: storeMantenimientosPlanMantenimiento,
                                disabled: true,
                                displayField: 'strNombreMantenimiento',
                                valueField: 'intIdMantenimiento',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200,
                                width: 450,
                                listeners: {

                                    change: function(cmp, newValue, oldValue) {
                                        var objExtraParams = storeTareasMantenimientoOCaso.proxy.extraParams;
                                        if(isNaN(cmp.getValue()) || (cmp.getValue()=='') || !cmp.getValue())
                                        {
                                            objExtraParams.idMantenimiento = '';
                                            objExtraParams.idCaso = '';
                                            objExtraParams.tipoMantenimiento='';
                                            storeTareasMantenimientoOCaso.loadData([],false);

                                        }
                                        else
                                        {
                                            objExtraParams.idMantenimiento = cmp.getValue();
                                            objExtraParams.idCaso = '';
                                            objExtraParams.tipoMantenimiento='PREVENTIVO';
                                            storeTareasMantenimientoOCaso.load();
                                        }
                                    }
                                }
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Número de Caso</b>',
                                id: 'cmbCasoTransporte',
                                name: 'cmbCasoTransporte',
                                store: storeCasosXTransporte,
                                disabled: false,
                                displayField: 'strNumeroCaso',
                                valueField: 'intIdCaso',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                hidden:true,
                                labelWidth: 200,
                                width: 450,
                                listeners: {

                                    change: function(cmp, newValue, oldValue) {
                                        var objExtraParams = storeTareasMantenimientoOCaso.proxy.extraParams;
                                        if(isNaN(cmp.getValue()) || (cmp.getValue()=='') || !cmp.getValue())
                                        {
                                            objExtraParams.idMantenimiento = '';
                                            objExtraParams.idCaso = '';
                                            objExtraParams.tipoMantenimiento='';
                                            storeTareasMantenimientoOCaso.loadData([],false);

                                        }
                                        else
                                        {
                                            objExtraParams.idMantenimiento = '';
                                            objExtraParams.idCaso = cmp.getValue();
                                            objExtraParams.tipoMantenimiento='CORRECTIVO';
                                            storeTareasMantenimientoOCaso.load();
                                        }
                                    }
                                }
                            },
                            formKmActual,
                            {
                               xtype: 'displayfield',
                               id: 'strTipoAsignacion',
                               name: 'strTipoAsignacion',
                               value: 'CONTRATISTA',
                               hidden: true
                            },
                            {
                                xtype: 'radiogroup',
                                fieldLabel: '<b>Asignado a</b>',
                                labelWidth: 200,
                                items: [
                                    {boxLabel: 'Contratista', name: 'asignadoA', inputValue: 'CONTRATISTA', checked: true}
                                ]
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Taller</b>',
                                id: 'cmbContratista',
                                name: 'cmbContratista',
                                store: storeContratistas,
                                width: 450,
                                displayField: 'strNombreContratista',
                                valueField: 'intIdPerContratista',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Autorizado Por</b>',
                                id: 'cmbAutorizadoPor',
                                name: 'cmbAutorizadoPor',
                                store: storePerAutorizadoPor,
                                width: 450,
                                displayField: 'strPerAutorizadoPor',
                                valueField: 'intIdPerAutorizadoPor',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200
                            },
                            {
                               xtype: 'textarea',
                               fieldLabel: '<b>Observación</b>',
                               id: 'strObservacion',
                               name: 'strObservacion',
                               labelWidth: 200,
                               width: 600
                            }
                            
                       ]
                   }

               ]
           }

        ]
    });
    
    
     //crear modelo para el grid
    Ext.define('TareaOrdenTrabajoTransporteModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idTarea',                mapping:'idTarea'},
            {name:'nombreTarea',            mapping:'nombreTarea'},
            {name:'id_categoria_tarea',     mapping:'id_categoria_tarea'},
            {name:'nombre_categoria_tarea', mapping:'nombre_categoria_tarea'}
            
        ]
    });
    
    storeTareasMantenimientoOCaso = new Ext.data.JsonStore(
    {
        pageSize: 200,
        autoLoad: false,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlGetTareasMantenimientosOCasoTransporte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
        
            {name:'idTarea',                mapping:'id_tarea'},
            {name:'nombreTarea',            mapping:'nombre_tarea'},
            {name:'id_categoria_tarea',     mapping:'id_categoria'},
            {name:'nombre_categoria_tarea', mapping:'nombre_categoria'}
        ]
    });
    
                        
    cellEditingTareasOrdenTrabajoTransporte = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            beforeedit: function(editor, event, opts) {
                if (!event.record.phantom) {
                    //En caso de editar la categoría de la tarea
                    if(event.colIdx==3)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            },
            edit: function() {
                gridTareasOrdenTrabajoTransporte.getView().refresh();
            }
        }
    });
    
    comboTareaNuevaStore = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetTareasCasosMovilizacionTransporte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo',
                visible: 'NO'
            }
        },
        fields:
            [
                {name: 'idTarea', mapping: 'id_tarea'},
                {name: 'nombreTarea', mapping: 'nombre_tarea'}
            ]
    });
    
    combo_tarea_nueva = new Ext.form.ComboBox({
        id: 'searchTareaNueva_cmp',
        name: 'searchTareaNueva_cmp',
        displayField: 'nombreTarea',
        valueField: 'idTarea',
        store: comboTareaNuevaStore,
        loadingText: 'Buscando ...',
        disabled: true,
        fieldLabel: false,
        queryMode: "remote",                                        
        listClass: 'x-combo-list-small'
    });
    
    
    comboCategoriaTareaStore = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetCategoriasTareasOTyMantenimientosTransporte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'id_categoria_tarea', mapping: 'idParametroDet'},
                {name: 'nombre_categoria_tarea', mapping: 'valor1'}
            ]
    });
    
    combo_categoria_tarea = new Ext.form.ComboBox({
        id: 'searchCategoriaTarea_cmp',
        name: 'searchCategoriaTarea_cmp',
        displayField: 'nombre_categoria_tarea',
        valueField: 'id_categoria_tarea',
        store: comboCategoriaTareaStore,
        loadingText: 'Buscando ...',
        disabled: true,
        fieldLabel: false,
        queryMode: "remote",                                        
        listClass: 'x-combo-list-small'
    });
    
    gridTareasOrdenTrabajoTransporte = Ext.create('Ext.grid.Panel', {
        title:'Detalle de Tareas', 
        width: '100%',
        height: 300,
        sortableColumns:false,
        store: storeTareasMantenimientoOCaso,
        viewConfig: {enableTextSelection: true, stripeRows: true},
        id:'gridTareasOrdenTrabajoTransporte',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
        // inline buttons
        dockedItems: 
        [{
            xtype: 'toolbar',
            items: 
            [
                {
                    text:'Agregar',
                    tooltip:'Agrega una tarea a la lista',
                    iconCls:'add',
                    handler : function(){
                        // Create a model instance
                        var r = Ext.create('TareaOrdenTrabajoTransporteModel', { 
                                idTarea:     '',
                                nombreTarea: '',
                                id_categoria_tarea: '',
                                nombre_categoria_tarea: ''
                        });
                        if(!existeRecordTareaOrdenTrabajoTransporte(r, gridTareasOrdenTrabajoTransporte))
                        {
                            storeTareasMantenimientoOCaso.insert(0, r);
                            cellEditingTareasOrdenTrabajoTransporte.startEditByPosition({row: 0, column: 0});
                        }
                        else
                        {
                          alert('Ya existe un registro vacio.');
                        }
                    }
                }]
        }],
         plugins: [cellEditingTareasOrdenTrabajoTransporte],
        listeners:{
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

        },
        columns: [
            {
              id: 'idTarea',
              header: 'idTarea',
              dataIndex: 'idTarea',
              hidden: true,
              hideable: false
            },
             {
              id: 'nombreTarea',
              header: 'Nombre',
              dataIndex: 'nombreTarea',
              width:180,
              renderer: function(value, metadata, record, rowIndex, colIndex, store) {

                    combo_tarea_nueva.setDisabled(false);
                    var idTareaNuevaTmp=record.data.nombreTarea;
                    for (var i = 0; i < comboTareaNuevaStore.data.items.length; i++)
                    {
                        if (comboTareaNuevaStore.data.items[i].data.idTarea == idTareaNuevaTmp)
                        {
                            
                            record.data.idTarea = comboTareaNuevaStore.data.items[i].data.idTarea;
                            record.data.nombreTarea = comboTareaNuevaStore.data.items[i].data.nombreTarea;
                            break;
                        }
                    }

                    return record.data.nombreTarea;
                },
                editor: combo_tarea_nueva
            },
            {
                id: 'id_categoria_tarea',
                header: 'CategoriaTareaId',
                dataIndex: 'id_categoria_tarea',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombre_categoria_tarea',
                header: 'Categoría de la Tarea',
                dataIndex: 'nombre_categoria_tarea',
                width: 100,
                sortable: true,
                renderer: function(value, metadata, record, rowIndex, colIndex, store) {

                    combo_categoria_tarea.setDisabled(false);
                    var idCategoriaTareaTmp=record.data.nombre_categoria_tarea;
                    for (var i = 0; i < comboCategoriaTareaStore.data.items.length; i++)
                    {
                        if (comboCategoriaTareaStore.data.items[i].data.id_categoria_tarea == idCategoriaTareaTmp)
                        {
                            record.data.id_categoria_tarea = comboCategoriaTareaStore.data.items[i].data.id_categoria_tarea;
                            record.data.nombre_categoria_tarea = comboCategoriaTareaStore.data.items[i].data.nombre_categoria_tarea;
                            break;
                        }
                    }

                    return record.data.nombre_categoria_tarea;
                },
                editor: combo_categoria_tarea
            }

        ],    
        renderTo: 'detalle_tareas_orden_trabajo_transporte'
    });
    
});


function validarFormulario()
{
    if(validarFechasOrdenTrabajo())
    {
        var tipoMantenimiento=document.getElementById("escogido_tipo_mantenimiento").value;
        if(tipoMantenimiento=="PREVENTIVO")
        {
            var planMantenimientoId = Ext.getCmp('cmbPlanMantenimiento').getValue();    
            if(planMantenimientoId=="" || !planMantenimientoId){  planMantenimientoId = 0; }
            Ext.get('escogido_plan_mantenimiento_id').dom.value = planMantenimientoId;      
            if(planMantenimientoId==0)
            {
                Ext.Msg.alert('Error ', "No se ha escogido el Plan de Mantenimiento");
                return false;
            }

            var mantenimientoId = Ext.getCmp('cmbMantenimiento').getValue();    
            if(mantenimientoId=="" || !mantenimientoId){  mantenimientoId = 0; }
            Ext.get('escogido_mantenimiento_id').dom.value = mantenimientoId;      
            if(mantenimientoId==0)
            {
                Ext.Msg.alert('Error ', "No se ha escogido el Mantenimiento");
                return false;
            }
        }
        else
        {
            var casoMantenimientoId = Ext.getCmp('cmbCasoTransporte').getValue();    
            if(casoMantenimientoId=="" || !casoMantenimientoId || !isPositiveInteger(casoMantenimientoId)){  casoMantenimientoId = 0; }
            Ext.get('escogido_caso_mantenimiento_id').dom.value = casoMantenimientoId;      
            if(casoMantenimientoId==0)
            {
                Ext.Msg.alert('Error ', "No se ha escogido el caso");
                return false;
            }
        }

        var contratistaId = Ext.getCmp('cmbContratista').getValue();
        if(contratistaId=="" || !contratistaId){  contratistaId = 0; }
        Ext.get('escogido_contratista_id').dom.value = contratistaId;      
        if(contratistaId==0)
        {
            Ext.Msg.alert('Error ', "No se ha escogido el Contratista");
            return false;
        }

        var idPerAutorizadoPor = Ext.getCmp('cmbAutorizadoPor').getValue();
        if(idPerAutorizadoPor=="" || !idPerAutorizadoPor){  idPerAutorizadoPor = 0; }
        Ext.get('idPerAutorizadoPor').dom.value = idPerAutorizadoPor;      
        if(idPerAutorizadoPor==0)
        {
            Ext.Msg.alert('Error ', "No se ha escogido la persona que autoriza la Orden de Trabajo");
            return false;
        }

        var kmActual = Ext.getCmp('kmActual').getValue();
        if(kmActual=="" || !kmActual)
        {
            Ext.Msg.alert('Error ', "Por favor ingrese el KM Actual");
            return false;
        }
        else if(kmActual<0)
        {
            Ext.Msg.alert('Error ', "El KM actual no puede ser menor a 0");
            return false;
        }

        //Validando que se han ingresado las categorias en las tareas del detalle}
        if(gridTareasOrdenTrabajoTransporte.getStore().getCount()>0)
        {
            for (var i = 0; i < gridTareasOrdenTrabajoTransporte.getStore().getCount(); i++)
            {
               if(gridTareasOrdenTrabajoTransporte.getStore().getAt(i).data.id_categoria_tarea)
                {
                    if (gridTareasOrdenTrabajoTransporte.getStore().getAt(i).data.id_categoria_tarea === "") {
                        Ext.Msg.alert("Alerta", "Debe ingresar las categorías a todas las tareas");
                        return false;
                    }
                }
                else
                {
                    Ext.Msg.alert("Alerta", "Debe ingresar las categorías a todas las tareas");
                    return false;
                }
            }
        }
        else
        {
            Ext.Msg.alert("Alerta", "Debe ingresar al menos una tarea a la Orden de Trabajo");
            return false;
        }

        jsonTareasyCategoriasOrdenTrabajoTransporte = obtenerTareasOrdenTrabajoTransporte();
        Ext.get('json_tareas_y_categorias_orden_trabajo').dom.value = jsonTareasyCategoriasOrdenTrabajoTransporte; 

        var valueChkOTNumeracionActual                          = Ext.getCmp('chkOTNumeracionActual').getValue();
        if(valueChkOTNumeracionActual)
        {
            document.getElementById("OT_numeracion_actual").value   = "SI";
        }
        else
        {
            document.getElementById("OT_numeracion_actual").value   = "NO";
        }
        return true;        
    }
    return false;
}

function validarFechasOrdenTrabajo(cmp)
{
    var fieldFechaInicioOrdenTrabajo    = Ext.getCmp('fechaInicio');
    var valFechaInicioOrdenTrabajo      = fieldFechaInicioOrdenTrabajo.getSubmitValue();

    var fieldFechaFinOrdenTrabajo       = Ext.getCmp('fechaFin');
    var valFechaFinOrdenTrabajo         = fieldFechaFinOrdenTrabajo.getSubmitValue();

    var boolOKFechas        = true;
    var boolCamposLLenos    = false;
    var strMensaje          = '';
    var boolSinErrorFechas  = false;

    if(valFechaInicioOrdenTrabajo && valFechaFinOrdenTrabajo)
    {
        var valCompFechaInicioOrdenTrabajo  = Ext.Date.parse(valFechaInicioOrdenTrabajo, "d/m/Y");
        var valCompFechaFinOrdenTrabajo     = Ext.Date.parse(valFechaFinOrdenTrabajo, "d/m/Y");

        if(valCompFechaInicioOrdenTrabajo>valCompFechaFinOrdenTrabajo)
        {
            boolOKFechas    = false;
            strMensaje      = 'La Fecha Inicio '+ valFechaInicioOrdenTrabajo +' no puede ser mayor a la Fecha Fin '+valFechaFinOrdenTrabajo;
            Ext.Msg.alert('Atenci\xf3n', strMensaje); 
        }
    }

    if(valFechaInicioOrdenTrabajo && valFechaFinOrdenTrabajo )
    {
        boolCamposLLenos=true;
    }
    else
    {
        strMensaje      = 'La Fecha Inicio y Fecha Fin no pueden estar vacías';
        Ext.Msg.alert('Atenci\xf3n', strMensaje); 
    }

    if(boolOKFechas && boolCamposLLenos)
    {
        boolSinErrorFechas     = true;
    }
    else if(!boolOKFechas )
    {
        if(cmp!=null)
        {
            cmp.value = "";
            cmp.setRawValue("");
        }
        
        boolSinErrorFechas = false;
    }
    return boolSinErrorFechas;
}

function showChoferesOrdenTrabajo(input1,input2,input3)
{
    TFNombresChofer = new Ext.form.TextField({
        name: 'txtNombresChoferOrdenTrabajo',
        fieldLabel: 'Nombres',
        xtype: 'textfield',
        labelWidth: 50
    });

    TFApellidosChofer = new Ext.form.TextField({
        name: 'txtApellidosChoferOrdenTrabajo',
        fieldLabel: 'Apellidos',
        xtype: 'textfield',
        labelWidth: 50
    });

    TFIdentificacionChofer = new Ext.form.TextField({
        name: 'txtIdentificacionChoferOrdenTrabajo',
        fieldLabel: 'Identificación',
        xtype: 'textfield',
        labelWidth: 70
    }); 

    storeChoferes = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 5,
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: strUrlGetChoferes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                identificacionChoferOrdenTrabajo:'',
                nombresChoferOrdenTrabajo:'',
                apellidosChoferOrdenTrabajo:''
            }
        },
        fields: [
            {name:'idPersonaEmpresaRolChofer',  mapping: 'idPersonaEmpresaRol'},
            {name:'idPersonaChofer',            mapping: 'idPersona'},
            {name:'identificacionChofer',       mapping: 'identificacion'},
            {name:'nombreCompletoChofer',       mapping: 'nombreCompleto'},
            {name:'nombresChofer',              mapping: 'nombres'},
            {name:'apellidosChofer',            mapping: 'apellidos'}
        ]
    });
    
    storeChoferes.load();
    var listViewChoferes = Ext.create('Ext.grid.Panel', {
        width:520,
        height:200,
        collapsible:false,
        title: '',
        store: storeChoferes,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeChoferes,
                    displayInfo: true,
                    displayMsg: 'Mostrando Choferes {0} - {1} of {2}',
                    emptyMsg: "No hay datos para mostrar"
        }),

        columns: 
        [
            new Ext.grid.RowNumberer(),  
            {
                text: 'idPersonaEmpresaRolChofer',
                dataIndex: 'idPersonaEmpresaRolChofer',
                hidden: true,
                hideable: false
            },
            {
                text: 'idPersonaChofer',
                dataIndex: 'idPersonaChofer',
                hidden: true,
                hideable: false
            },
            {
                text: 'Apellidos',
                dataIndex: 'apellidosChofer',
                width: 200			
            },
            {
                text: 'Nombres',
                dataIndex: 'nombresChofer',
                width: 200			
            },
            {
                text: 'Identificación',
                width: 100,
                dataIndex: 'identificacionChofer'
            }
            
        ],
        listeners: 
        {
            itemdblclick:
            {
                fn: function( view, rec, node, index, e, options )
                {
                    $(input3).val(rec.data.idPersonaEmpresaRolChofer);
                    $(input2).val(rec.data.idPersonaChofer);
                    $(input1).val(rec.data.nombreCompletoChofer);  
                    winChoferes.close();
                    formChoferes.destroy();
                }
            }
        }
    });

    filterPanelChoferes = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout:{
                type:'table',
                columns: 5,
                align: 'left'
        },
        bodyStyle: {
                background: '#fff'
        },                     
        defaults: {
                bodyStyle: 'padding:10px'
        },
        collapsible : true,
        collapsed: true,
        width: 520,
        title: 'Criterios de búsqueda',

        buttons: [                   
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        { 
                            buscarChoferesOrdenTrabajo();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        { 
                            limpiarChoferesOrdenTrabajo();
                        }
                    }
                ],                
        items: [
                    TFApellidosChofer,
                    { width: '5%',border:false},
                    TFNombresChofer,
                    { width: '5%',border:false},
                    TFIdentificacionChofer
                ]	
             });
             
    formChoferes = Ext.widget('form', {
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        border: false,
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'top',
            labelWidth: 100,
            labelStyle: 'font-weight:bold'
        },
        defaults: {
            margins: '0 0 10 0'
        },
        items: [			
            filterPanelChoferes,listViewChoferes			
        ]
    });
    
    
    winChoferes = Ext.create('Ext.window.Window',
    {
      title: 'Choferes',
      modal: true,
      width: 600,
      closable: true,
      layout: 'fit',
      floating: true,
      shadow: true,
      shadowOffset:20,
      resizable:true,
      items: [formChoferes]
    }).show();
}





function buscarChoferesOrdenTrabajo()
{
    if(Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferOrdenTrabajo]')[0].value!="" 
            || Ext.ComponentQuery.query('textfield[name=txtNombresChoferOrdenTrabajo]')[0].value!=""
            || Ext.ComponentQuery.query('textfield[name=txtApellidosChoferOrdenTrabajo]')[0].value!="")
    {
        storeChoferes.loadData([],false);
        storeChoferes.currentPage = 1;

        storeChoferes.getProxy().extraParams.identificacionChoferOrdenTrabajo   = 
        Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferOrdenTrabajo]')[0].value;
        storeChoferes.getProxy().extraParams.nombresChoferOrdenTrabajo         =
        Ext.ComponentQuery.query('textfield[name=txtNombresChoferOrdenTrabajo]')[0].value;
        storeChoferes.getProxy().extraParams.apellidosChoferOrdenTrabajo        =
        Ext.ComponentQuery.query('textfield[name=txtApellidosChoferOrdenTrabajo]')[0].value;
        storeChoferes.load({params: {start: 0, limit: 5}});
    }
    else
    {
        Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor Ingrese el nombre, apellido o una identificación para buscar',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
    }


}

function limpiarChoferesOrdenTrabajo()
{
    Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferOrdenTrabajo]')[0].value  = "";
    Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferOrdenTrabajo]')[0].setRawValue("");

    Ext.ComponentQuery.query('textfield[name=txtNombresChoferOrdenTrabajo]')[0].value = "";
    Ext.ComponentQuery.query('textfield[name=txtNombresChoferOrdenTrabajo]')[0].setRawValue("");

    Ext.ComponentQuery.query('textfield[name=txtApellidosChoferOrdenTrabajo]')[0].value = "";
    Ext.ComponentQuery.query('textfield[name=txtApellidosChoferOrdenTrabajo]')[0].setRawValue("");

    storeChoferes.currentPage   = 1;

    storeChoferes.getProxy().extraParams.identificacionChoferOrdenTrabajo   = 
        Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferOrdenTrabajo]')[0].value;  
    storeChoferes.getProxy().extraParams.nombresChoferOrdenTrabajo          = 
        Ext.ComponentQuery.query('textfield[name=txtNombresChoferOrdenTrabajo]')[0].value;
    storeChoferes.getProxy().extraParams.apellidosChoferOrdenTrabajo        = 
        Ext.ComponentQuery.query('textfield[name=txtApellidosChoferOrdenTrabajo]')[0].value;
    storeChoferes.load();
}

function obtenerTareasOrdenTrabajoTransporte()
{
  var array = new Object();
  array['total'] =  gridTareasOrdenTrabajoTransporte.getStore().getCount();
  array['tareasyCategoriasOrdenTrabajoTransporte'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridTareasOrdenTrabajoTransporte.getStore().getCount(); i++)
  {
    array_data.push(gridTareasOrdenTrabajoTransporte.getStore().getAt(i).data);
  }
  array['tareasyCategoriasOrdenTrabajoTransporte'] = array_data;
  return Ext.JSON.encode(array);
}



function existeRecordTareaOrdenTrabajoTransporte(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();

    for (var i = 0; i < num; i++)
    {
        var tarea = grid.getStore().getAt(i).get('idTarea');
        if (tarea === myRecord.get('idTarea') )
        {
            existe = true;
            break;
        }
    }
    return existe;
}

function isPositiveInteger(s) {
  return /^\+?[1-9][\d]*$/.test(s);
}
